<?php
require_once __DIR__ . '/includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: shop.php'); exit; }
verify_csrf();
$id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$stmt = db()->prepare('SELECT id FROM products WHERE id = ?');
$stmt->execute([$id]);
if (!$id || !$stmt->fetch()) { set_flash('That item could not be found.', 'error'); header('Location: shop.php'); exit; }
$_SESSION['cart'] = $_SESSION['cart'] ?? [];
$_SESSION['cart'][$id] = min(20, (int) ($_SESSION['cart'][$id] ?? 0) + 1);
set_flash('The item has been added to your cart.', 'success', 'Add to cart success', (int) $id);
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'shop.php'));
exit;
