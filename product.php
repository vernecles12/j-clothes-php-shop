<?php
require_once __DIR__ . '/includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = null;
if ($id) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}
if (!$product) {
    header('Location: index.php#shop');
    exit;
}

$pageTitle = $product['name'];
include __DIR__ . '/includes/header.php';

$firstNames = ['Ana', 'Mia', 'Josh', 'Liam', 'Sofia', 'Marco', 'Bea', 'Noah', 'Ella', 'Enzo'];
$lastNames = ['Reyes', 'Garcia', 'Lim', 'Cruz', 'Santos', 'Mendoza', 'Torres', 'Flores', 'Ramos', 'Tan'];
$seed = (int) $product['id'];
mt_srand($seed);
shuffle($firstNames);
shuffle($lastNames);
$comments = [
    'The fit is comfortable and the quality feels even better in person.',
    'Beautiful colour and an easy piece to style. I have worn it several times already.',
    'Delivery was quick and the item matched the photos perfectly.',
    'The material feels soft and the sizing was exactly as expected.',
    'A great everyday piece that looks much more expensive than it is.'
];
mt_srand($seed * 7);
shuffle($comments);
?>
<section class="product-page">
    <div class="container product-detail">
        <div class="product-detail-image">
            <img src="assets/images/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
        </div>
        <div class="product-detail-info">
            <a class="back-link" href="index.php#shop">← Back to the shop</a>
            <p class="eyebrow dark-eyebrow"><?= e($product['category']) ?></p>
            <h1><?= e($product['name']) ?></h1>
            <div class="detail-rating"><span class="stars">★★★★★</span> <strong><?= number_format((float) $product['rating'], 1) ?></strong> <span>(<?= 18 + (($seed * 7) % 83) ?> verified reviews)</span></div>
            <p class="detail-description"><?= e($product['description']) ?></p>
            <p class="detail-price"><?= money($product['price']) ?></p>
            <form method="post" action="add_to_cart.php" class="detail-cart-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                <button class="button button-dark" type="submit">Add to cart <span>+</span></button>
            </form>
        </div>
    </div>
</section>
<section class="reviews-section">
    <div class="container">
        <div class="reviews-heading">
            <div><p class="eyebrow dark-eyebrow">BUYER NOTES</p><h2>What customers say<span class="period">.</span></h2></div>
            <div class="review-score"><strong><?= number_format((float) $product['rating'], 1) ?></strong><span>out of 5</span></div>
        </div>
        <div class="review-grid">
            <?php for ($i = 0; $i < 3; $i++):
                $rating = 5 - (($i + $seed) % 2);
                $name = $firstNames[$i] . ' ' . $lastNames[$i];
            ?>
            <article class="review-card">
                <div class="review-meta"><div class="review-avatar"><?= e(strtoupper(substr($firstNames[$i], 0, 1))) ?></div><div><strong><?= e($name) ?></strong><span>Verified buyer</span></div></div>
                <div class="review-stars"><?= str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) ?></div>
                <p><?= e($comments[$i]) ?></p>
            </article>
            <?php endfor; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
