<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Shop the collection';
$pdo = db();
$allProducts = $pdo->query('SELECT * FROM products ORDER BY is_featured DESC, id ASC')->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="page-banner shop-page-banner"><div class="container"><p class="eyebrow dark-eyebrow">THE J CLOTHES EDIT</p><h1>Shop the collection<span class="period">.</span></h1><p>Easy pieces, thoughtful details, and everyday favourites.</p></div></section>
<section class="section shop-page-section"><div class="container">
    <div class="section-heading compact-heading"><div><p class="eyebrow dark-eyebrow">MORE TO LOVE</p><h2>The full edit<span class="period">.</span></h2></div><span class="item-count"><?= count($allProducts) ?> PIECES</span></div>
    <div class="mini-product-grid">
        <?php foreach ($allProducts as $product): ?>
            <article class="mini-product">
                <a class="mini-product-link" href="product.php?id=<?= (int) $product['id'] ?>"><img src="assets/images/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy"></a>
                <div>
                    <p class="product-category"><?= e($product['category']) ?></p>
                    <h3><a href="product.php?id=<?= (int) $product['id'] ?>"><?= e($product['name']) ?></a></h3>
                    <p class="mini-description"><?= e($product['description']) ?></p>
                    <div class="mini-meta"><strong><?= money($product['price']) ?></strong><span class="rating"><span>★</span> <?= number_format((float) $product['rating'], 1) ?></span></div>
                    <form method="post" action="add_to_cart.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>"><button class="mini-add" type="submit">Add to cart +</button></form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
