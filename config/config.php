<?php
declare(strict_types=1);

// Update these values to match your local MySQL / XAMPP setup.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'j_clothes');
define('DB_USER', 'root');
define('DB_PASS', '');
define('SITE_NAME', 'J Clothes');
define('CURRENCY', '₱');

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 465);
define('MAIL_USERNAME', getenv('JC_MAIL_USERNAME') ?: '');
define('MAIL_PASSWORD', getenv('JC_MAIL_PASSWORD') ?: '');
define('MAIL_FROM', getenv('JC_MAIL_USERNAME') ?: 'no-reply@jclothes.local');
define('MAIL_FROM_NAME', 'J Clothes');

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
