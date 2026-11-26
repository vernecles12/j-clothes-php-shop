<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_logged_in()) { header('Location: ../index.php'); exit; }
$errors = [];
$name = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');
    if (mb_strlen($name) < 2 || mb_strlen($name) > 120) $errors[] = 'Please enter your name (2–120 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Your password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Your passwords do not match.';
    if (!$errors) {
        $check = db()->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) $errors[] = 'An account with that email already exists.';
    }
    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        unset($_SESSION['user']);
        session_regenerate_id(true);
        set_flash('Account created successfully. Please sign in with your new account.', 'success', 'Registration successful');
        header('Location: login.php'); exit;
    }
}
$pageTitle = 'Create your account';
include __DIR__ . '/../includes/header.php';
?>
<section class="auth-section"><div class="auth-aside"><p class="eyebrow">A GOOD PLACE TO START</p><h1>Make room for<br><em>your favourites.</em></h1><p>Create an account to keep your J Clothes experience close.</p><span class="auth-aside-note">J / YOUR EVERYDAY EDIT</span></div><div class="auth-panel"><p class="eyebrow dark-eyebrow">JOIN THE J COMMUNITY</p><h2>Create an account</h2><p class="auth-intro">A few details and you're all set.</p><?php if ($errors): ?><div class="form-errors"><strong>Please check the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?><form method="post" class="auth-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label for="name">Full name</label><input id="name" name="name" type="text" autocomplete="name" value="<?= e($name) ?>" required maxlength="120"><label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>" required><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required><label for="confirm_password">Confirm password</label><input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" minlength="8" required><button class="button button-dark full-button" type="submit">Create account <span>↗</span></button></form><p class="auth-switch">Already have an account? <a href="login.php">Sign in</a></p></div></section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
