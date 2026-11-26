<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_logged_in()) { header('Location: ../index.php'); exit; }
$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $stmt = db()->prepare('SELECT id, name, email, password_hash, login_count FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email']];
        $loginCount = (int) ($user['login_count'] ?? 0);
        $update = db()->prepare('UPDATE users SET login_count = login_count + 1 WHERE id = ?');
        $update->execute([(int) $user['id']]);
        if ($loginCount === 0) {
            set_flash('Welcome, ' . $user['name'] . '.', 'success', 'Welcome');
        } else {
            set_flash('Good to see you again, ' . $user['name'] . '.', 'success', 'Welcome back!!');
        }
        header('Location: ../index.php'); exit;
    }
    $error = 'The email or password you entered is incorrect.';
}
$pageTitle = 'Sign in';
include __DIR__ . '/../includes/header.php';
?>
<section class="auth-section"><div class="auth-aside"><p class="eyebrow">WELCOME BACK</p><h1>Your style.<br><em>Your pace.</em></h1><p>Sign in and pick up where your everyday edit left off.</p><span class="auth-aside-note">J / YOUR EVERYDAY EDIT</span></div><div class="auth-panel"><p class="eyebrow dark-eyebrow">GOOD TO SEE YOU</p><h2>Sign in</h2><p class="auth-intro">Enter your details to continue.</p><?php if ($error): ?><div class="form-errors" role="alert"><?= e($error) ?></div><?php endif; ?><form method="post" class="auth-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>" required><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required><div class="forgot-password-row"><a href="forgot_password.php?new=1">Forgot your password?</a></div><button class="button button-dark full-button" type="submit">Sign in <span>↗</span></button></form><p class="auth-switch">New to J Clothes? <a href="register.php">Create an account</a></p></div></section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
