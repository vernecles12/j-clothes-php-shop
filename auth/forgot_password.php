<?php
require_once __DIR__ . '/../includes/functions.php';

function send_reset_code_email(string $recipient, string $code): bool
{
    if (trim(MAIL_USERNAME) === '' || trim(MAIL_PASSWORD) === '') return false;

    $host = 'ssl://' . MAIL_HOST . ':' . (MAIL_PORT === 587 ? 465 : MAIL_PORT);
    $socket = @stream_socket_client($host, $errorNumber, $errorMessage, 20);
    if (!$socket) return false;

    $read = function () use ($socket): string {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (strlen($line) >= 4 && $line[3] !== '-') break;
        }
        return $response;
    };
    $write = function (string $command) use ($socket): void {
        fwrite($socket, $command . "\r\n");
    };

    $read();
    $write('EHLO localhost'); $read();
    $write('AUTH LOGIN');
    $authResponse = $read();
    if (!str_starts_with($authResponse, '334')) { fclose($socket); return false; }

    $write(base64_encode(trim(MAIL_USERNAME))); $read();
    $write(base64_encode(trim(MAIL_PASSWORD)));
    if (!str_starts_with($read(), '235')) { fclose($socket); return false; }

    $write('MAIL FROM:<' . trim(MAIL_USERNAME) . '>'); $read();
    $write('RCPT TO:<' . $recipient . '>'); $read();
    $write('DATA'); $read();

    $body = "Your J Clothes password reset code is: {$code}\r\n\r\nThis code expires in 10 minutes.";
    $headers = "From: " . MAIL_FROM_NAME . " <" . trim(MAIL_USERNAME) . ">\r\n";
    $headers .= "To: {$recipient}\r\n";
    $headers .= "Subject: J Clothes password reset code\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $write($headers . "\r\n" . $body . "\r\n.");
    $result = $read();
    $write('QUIT'); $read();
    fclose($socket);

    return str_starts_with($result, '250');
}

if (is_logged_in()) { header('Location: ../index.php'); exit; }

$errors = [];
$email = '';
$step = 'email';

if (isset($_SESSION['password_reset']) && ($_SESSION['password_reset']['expires'] ?? 0) < time()) {
    unset($_SESSION['password_reset']);
}

if (!empty($_SESSION['password_reset']['verified'])) {
    $step = 'password';
    $email = (string) ($_SESSION['password_reset']['email'] ?? '');
    unset($_SESSION['password_reset']['demo_code']);
} elseif (!empty($_SESSION['password_reset']['email'])) {
    $step = 'code';
    $email = (string) $_SESSION['password_reset']['email'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $postedStep = (string) ($_POST['step'] ?? 'email');

    if ($postedStep === 'email') {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } else {
            $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if (!$user) {
                $errors[] = 'No account was found with that email address.';
            } else {
                $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $_SESSION['password_reset'] = [
                    'email' => $email,
                    'user_id' => (int) $user['id'],
                    'code' => password_hash($code, PASSWORD_DEFAULT),
                    'expires' => time() + 600,
                    'verified' => false,
                ];

                $subject = 'J Clothes password reset code';
                $message = "Your J Clothes password reset code is: {$code}\n\nThis code expires in 10 minutes.";

                // Gmail delivery requires SMTP credentials in config/config.php.
                $emailSent = false;
                if (MAIL_USERNAME !== '' && MAIL_PASSWORD !== '') {
                    $emailSent = send_reset_code_email($email, $code);
                }

                if (!$emailSent) {
                    $_SESSION['password_reset']['demo_code'] = $code;
                    set_flash('Gmail SMTP is not configured. Use the demo code shown below.', 'success', 'Verification code');
                    header('Location: forgot_password.php');
                    exit;
                }

                set_flash('A 6-digit verification code was sent to ' . $email . '.', 'success', 'Verification code sent');
                header('Location: forgot_password.php');
                exit;
            }
        }
    } elseif ($postedStep === 'code') {
        $reset = $_SESSION['password_reset'] ?? null;
        $email = (string) ($reset['email'] ?? '');
        $enteredCode = trim((string) ($_POST['code'] ?? ''));
        if (!$reset || !preg_match('/^\d{6}$/', $enteredCode)) {
            $errors[] = 'Enter the 6-digit verification code.';
        } elseif (!password_verify($enteredCode, (string) $reset['code'])) {
            $errors[] = 'The verification code is incorrect.';
        } elseif (($reset['expires'] ?? 0) < time()) {
            unset($_SESSION['password_reset']);
            $step = 'email';
            $errors[] = 'The verification code expired. Request a new code.';
        } else {
            $_SESSION['password_reset']['verified'] = true;
            unset($_SESSION['password_reset']['demo_code']);
            set_flash('Code verified. Create your new password.', 'success', 'Verified');
            header('Location: forgot_password.php');
            exit;
        }
        $step = 'code';
    } elseif ($postedStep === 'password') {
        $reset = $_SESSION['password_reset'] ?? null;
        $email = (string) ($reset['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');

        if (!$reset || empty($reset['verified']) || ($reset['expires'] ?? 0) < time()) {
            unset($_SESSION['password_reset']);
            $step = 'email';
            $errors[] = 'The password-reset session expired. Start again.';
        } elseif (strlen($password) < 8) {
            $step = 'password';
            $errors[] = 'Your new password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $step = 'password';
            $errors[] = 'The passwords do not match.';
        } else {
            $update = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $update->execute([password_hash($password, PASSWORD_DEFAULT), (int) $reset['user_id']]);
            unset($_SESSION['password_reset']);
            set_flash('Your password has been changed. You can now sign in.', 'success', 'Password updated');
            header('Location: login.php');
            exit;
        }
    }
}

// Explicit fresh-reset links clear the old code; normal POST redirects do not.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['new'])) {
    unset($_SESSION['password_reset']);
}

$pageTitle = 'Reset password';
include __DIR__ . '/../includes/header.php';
?>
<section class="auth-section">
    <div class="auth-aside"><p class="eyebrow">ACCOUNT RECOVERY</p><h1>Set a new<br><em>password.</em></h1><p>Verify your email first, then choose a secure password.</p><span class="auth-aside-note">J / YOUR EVERYDAY EDIT</span></div>
    <div class="auth-panel">
        <p class="eyebrow dark-eyebrow">FORGOT YOUR PASSWORD?</p>
        <h2><?= $step === 'email' ? 'Reset password' : ($step === 'code' ? 'Enter verification code' : 'Create new password') ?></h2>
        <?php if ($errors): ?><div class="form-errors"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

        <?php if ($step === 'email'): ?>
            <form method="post" class="auth-form">
                <input type="hidden" name="step" value="email">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>" required>
                <button class="button button-dark full-button" type="submit">Send verification code <span>↗</span></button>
            </form>
        <?php elseif ($step === 'code'): ?>
            <?php if (!empty($_SESSION['password_reset']['demo_code'])): ?>
                <div class="demo-reset-code"><strong>Demo verification code</strong><span><?= e((string) $_SESSION['password_reset']['demo_code']) ?></span></div>
                <p class="auth-intro">Enter the demo code below. It expires in 10 minutes.</p>
            <?php else: ?>
                <p class="auth-intro">We sent a 6-digit code to <strong><?= e($email) ?></strong>. The code expires in 10 minutes.</p>
            <?php endif; ?>
            <form method="post" class="auth-form">
                <input type="hidden" name="step" value="code">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label for="code">Verification code</label>
                <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required>
                <button class="button button-dark full-button" type="submit">Verify code <span>↗</span></button>
            </form>
            <form method="post" class="auth-form resend-reset-form">
                <input type="hidden" name="step" value="email">
                <input type="hidden" name="email" value="<?= e($email) ?>">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <button class="text-reset-button" type="submit">Send a new code</button>
            </form>
            <a class="another-email-link" href="forgot_password.php?new=1">Use another email address</a>
        <?php else: ?>
            <p class="auth-intro">Create a new password for <strong><?= e($email) ?></strong>.</p>
            <form method="post" class="auth-form">
                <input type="hidden" name="step" value="password">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label for="password">New password</label>
                <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
                <label for="confirm_password">Re-type new password</label>
                <input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
                <button class="button button-dark full-button" type="submit">Change password <span>↗</span></button>
            </form>
        <?php endif; ?>
        <p class="auth-switch"><a href="login.php">← Back to sign in</a></p>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
