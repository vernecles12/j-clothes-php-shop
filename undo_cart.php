<?php
require_once __DIR__ . '/includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}
verify_csrf();
$id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
if ($id && isset($_SESSION['cart'][$id])) {
    $quantity = (int) $_SESSION['cart'][$id];
    if ($quantity <= 1) {
        unset($_SESSION['cart'][$id]);
    } else {
        $_SESSION['cart'][$id] = $quantity - 1;
    }
    set_flash('The last item was removed from your cart.');
} else {
    set_flash('There is no item to undo.', 'error');
}
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php#shop'));
exit;
