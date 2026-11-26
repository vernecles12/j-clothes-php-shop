<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Your shopping bag';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($action === 'remove' && $id) {
        unset($_SESSION['cart'][$id]);
        set_flash('Item removed from your bag.');
    } elseif ($action === 'update' && $id) {
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        if ($quantity && $quantity >= 1 && $quantity <= 20) $_SESSION['cart'][$id] = $quantity;
        else unset($_SESSION['cart'][$id]);
        set_flash('Your bag has been updated.');
    }
    header('Location: cart.php'); exit;
}
$items = cart_items();
$total = cart_total($items);
include __DIR__ . '/includes/header.php';
?>
<section class="page-banner"><div class="container"><p class="eyebrow dark-eyebrow">YOUR PICKS</p><h1>Your shopping bag<span class="period">.</span></h1><p>Good choices. Let's take a look.</p></div></section>
<section class="section cart-section"><div class="container cart-layout"><?php if (!$items): ?><div class="empty-cart"><div class="empty-icon">J</div><h2>Your bag is taking a little break.</h2><p>There are plenty of good things waiting for you.</p><a class="button button-dark" href="index.php#shop">Explore the collection <span>↗</span></a></div><?php else: ?><div class="cart-items"><?php foreach ($items as $item): ?><article class="cart-item"><img src="assets/images/<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>"><div class="cart-item-info"><p class="product-category"><?= e($item['category']) ?></p><h3><?= e($item['name']) ?></h3><p><?= money($item['price']) ?> each</p><div class="cart-controls"><form method="post" class="quantity-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="update"><input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>"><label for="qty-<?= (int) $item['id'] ?>">Qty</label><input id="qty-<?= (int) $item['id'] ?>" type="number" name="quantity" min="1" max="20" value="<?= (int) $item['quantity'] ?>"><button type="submit" class="plain-action">Update</button></form><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="remove"><input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>"><button type="submit" class="plain-action remove-action">Remove</button></form></div></div><strong class="cart-line-total"><?= money($item['line_total']) ?></strong></article><?php endforeach; ?></div><aside class="order-summary"><p class="eyebrow dark-eyebrow">THE TOTAL</p><h2>Order summary</h2><div class="summary-row"><span>Subtotal</span><strong><?= money($total) ?></strong></div><div class="summary-row"><span>Delivery</span><span><?= $total >= 2000 ? 'Free' : 'Calculated at checkout' ?></span></div><div class="summary-total"><span>Total</span><strong><?= money($total) ?></strong></div><form method="post" action="checkout.php" class="payment-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><fieldset><legend>Choose payment method</legend><label><input type="radio" name="payment_method" value="Cash on Delivery" required><img src="assets/images/cod-logo.svg" alt="Cash on Delivery" class="payment-logo"><span>Cash on Delivery</span></label><label><input type="radio" name="payment_method" value="GCash"><img src="assets/images/gcash-logo.svg" alt="GCash" class="payment-logo"><span>GCash</span></label><label><input type="radio" name="payment_method" value="Debit/Credit Card"><img src="assets/images/debit-card-logo.svg" alt="Debit or credit card" class="payment-logo"><span>Debit/Credit Card</span></label></fieldset><button class="button button-dark full-button" type="submit">Proceed to checkout <span>↗</span></button></form><p class="summary-note">Choose a payment method, then proceed to place your demo order.</p></aside><?php endif; ?></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
