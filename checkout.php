<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: cart.php'); exit; }
verify_csrf();
$items = cart_items();
if (!$items) { set_flash('Your bag is empty.', 'error'); header('Location: cart.php'); exit; }
$total = cart_total($items);
$allowedPayments = ['Cash on Delivery', 'GCash', 'Debit/Credit Card'];
$paymentMethod = trim((string) ($_POST['payment_method'] ?? ''));
if (!in_array($paymentMethod, $allowedPayments, true)) {
    set_flash('Please choose a payment method.', 'error');
    header('Location: cart.php');
    exit;
}
$pdo = db();
try {
    $pdo->beginTransaction();
    $order = $pdo->prepare('INSERT INTO orders (user_id, total, status) VALUES (?, ?, ?)');
    $order->execute([(int) current_user()['id'], $total, 'Placed']);
    $orderId = (int) $pdo->lastInsertId();
    $line = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?)');
    foreach ($items as $item) $line->execute([$orderId, $item['id'], $item['name'], $item['quantity'], $item['price']]);
    $pdo->commit();
    unset($_SESSION['cart']);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    set_flash('We could not place your order. Please try again.', 'error');
    header('Location: cart.php'); exit;
}
$pageTitle = 'Order placed';
include __DIR__ . '/includes/header.php';
?>
<section class="section confirmation-section"><div class="container confirmation-card"><div class="confirmation-mark">✓</div><p class="eyebrow dark-eyebrow">THANK YOU, <?= e(strtoupper(current_user()['name'])) ?></p><h1>Payment successful<span class="period">.</span></h1><p>Your demo order <strong>#<?= $orderId ?></strong> was placed successfully using <strong><?= e($paymentMethod) ?></strong>. This sample shop does not process real payments or deliveries.</p><div class="confirmation-total"><span>Order total</span><strong><?= money($total) ?></strong></div><a class="button button-dark" href="index.php">Back to the shop <span>↗</span></a></div></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
