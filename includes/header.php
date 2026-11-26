<?php
$pageTitle = $pageTitle ?? 'Thoughtful everyday style';
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#19372c">
    <meta name="description" content="J Clothes — thoughtfully selected everyday clothing in earthy, easy-to-wear tones.">
    <title><?= e($pageTitle) ?> · <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e((str_contains($_SERVER['PHP_SELF'], '/auth/')) ? '../assets/css/style.css' : 'assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= e((str_contains($_SERVER['PHP_SELF'], '/auth/')) ? '../assets/css/product.css' : 'assets/css/product.css') ?>">
    <link rel="stylesheet" href="<?= e((str_contains($_SERVER['PHP_SELF'], '/auth/')) ? '../assets/css/cards.css' : 'assets/css/cards.css') ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="<?= e((str_contains($_SERVER['PHP_SELF'], '/auth/')) ? '../assets/js/app.js' : 'assets/js/app.js') ?>"></script>
</head>
<body>
<div class="announcement">A little more thoughtful. A lot more you. <span>Free delivery on orders over ₱2,000</span></div>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= e((str_contains($_SERVER['PHP_SELF'], '/auth/')) ? '../index.php' : 'index.php') ?>" aria-label="J Clothes home"><span class="brand-mark">J</span><span>J CLOTHES<small>wear well, live well</small></span></a>
        <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false"><span></span><span></span></button>
        <nav class="main-nav" aria-label="Main navigation">
            <a href="<?= e((str_contains($_SERVER['PHP_SELF'], '/auth/')) ? '../shop.php' : 'shop.php') ?>">Shop</a>
            <a href="<?= e((str_contains($_SERVER['PHP_SELF'], '/auth/')) ? '../index.php#about' : 'index.php#about') ?>">Our story</a>
            <a href="<?= e((str_contains($_SERVER['PHP_SELF'], '/auth/')) ? '../index.php#contact' : 'index.php#contact') ?>">Contact</a>
        </nav>
        <div class="nav-actions">
            <a class="account-link" href="<?= e((str_contains($_SERVER['PHP_SELF'], '/auth/')) ? (is_logged_in() ? 'logout.php' : 'login.php') : (is_logged_in() ? 'auth/logout.php' : 'auth/login.php')) ?>"><?= is_logged_in() ? 'Sign out' : 'Sign in' ?></a>
            <a class="cart-link" href="<?= e((str_contains($_SERVER['PHP_SELF'], '/auth/')) ? '../cart.php' : 'cart.php') ?>" aria-label="Shopping bag">Bag <span><?= cart_count() ?></span></a>
        </div>
    </div>
</header>
<?php if ($flash): ?>
    <div class="container flash-wrap"><div class="flash <?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?><?php if (!empty($flash['undoProductId'])): ?><form method="post" action="<?= e(str_contains($_SERVER['PHP_SELF'], '/auth/') ? '../undo_cart.php' : 'undo_cart.php') ?>" class="undo-flash-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="product_id" value="<?= (int) $flash['undoProductId'] ?>"><button type="submit">Undo</button></form><?php endif; ?></div></div>
<?php endif; ?>
<main>
    <?php if ($flash): ?>
    <script>
        window.JCLOTHES_ALERT = <?= json_encode(['title' => $flash['alertTitle'] ?? null, 'message' => $flash['message'], 'type' => $flash['type'], 'undoProductId' => $flash['undoProductId'] ?? null], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    </script>
    <?php endif; ?>
