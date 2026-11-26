<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Thoughtful everyday style';
$pdo = db();
$featured = $pdo->query('SELECT * FROM products WHERE is_featured = 1 ORDER BY id ASC')->fetchAll();
$allProducts = $pdo->query('SELECT * FROM products ORDER BY is_featured DESC, id ASC')->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="hero" id="home">
    <div class="hero-photo" aria-hidden="true"></div>
    <div class="container hero-content">
        <p class="eyebrow"><span class="eyebrow-line"></span> CONSIDERED CLOTHING · EVERYDAY COMFORT</p>
        <h1>Feel good in<br><em>what you wear.</em></h1>
        <p class="hero-copy">Easy pieces, earthy tones, and thoughtful details. Find the kind of everyday style that feels naturally yours.</p>
        <div class="hero-actions"><a class="button button-light" href="shop.php">Explore the collection <span>↗</span></a><a class="text-link" href="#about">A little about us <span>↓</span></a></div>
        <div class="hero-note"><span>01 / 03</span><span>MADE FOR YOUR EVERYDAY</span></div>
    </div>
    <div class="hero-side-label">J CLOTHES — EST. 2026</div>
</section>
<section class="intro-strip"><div class="container intro-inner"><span>LESS, BUT BETTER</span><span class="intro-dot">✳</span><span>COMFORT IN EVERY DETAIL</span><span class="intro-dot">✳</span><span>MADE TO MIX & MATCH</span></div></section>
<section class="section collection-section" id="shop">
    <div class="container">
        <div class="section-heading"><div><p class="eyebrow dark-eyebrow">THE J CLOTHES EDIT</p><h2>Everyday favourites<span class="period">.</span></h2><p class="section-subtitle">A considered collection of easy-to-wear essentials.</p></div><a class="underlined-link" href="#all-products">View the collection <span>↗</span></a></div>
        <div class="product-grid">
            <?php foreach ($featured as $product): ?>
                <article class="product-card">
                    <a class="product-image" href="product.php?id=<?= (int) $product['id'] ?>" aria-label="See <?= e($product['name']) ?>"><img src="assets/images/<?= e($product['image']) ?>" alt="Illustration of <?= e($product['name']) ?>" loading="lazy"><span class="product-tag">J SELECT</span></a>
                    <div class="product-info"><div><p class="product-category"><?= e($product['category']) ?></p><h3><?= e($product['name']) ?></h3><p class="product-description"><?= e($product['description']) ?></p></div><p class="product-price"><?= money($product['price']) ?></p></div>
                    <div class="product-bottom"><span class="rating"><span>★</span> <?= number_format((float) $product['rating'], 1) ?> <small>(customer rating)</small></span><form method="post" action="add_to_cart.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>"><button class="add-button" type="submit" aria-label="Add <?= e($product['name']) ?> to bag">Add to cart <span>+</span></button></form></div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="editorial section" id="about"><div class="container editorial-grid"><div class="editorial-art"><div class="art-orbit"></div><div class="journal-photo-card"><img src="assets/images/journal-notes.jpg" alt="J Clothes style journal"><span>J / JOURNAL</span><strong>FIND YOUR<br>OWN PACE.</strong><small>STYLE NOTES — NO. 01</small></div><span class="art-caption">A softer kind of statement.</span></div><div class="editorial-copy"><p class="eyebrow dark-eyebrow">A NOTE FROM J CLOTHES</p><h2>Good style should feel <em>easy.</em></h2><p>J Clothes is built around a simple idea: getting dressed should be one of the easiest parts of your day. We bring together versatile silhouettes, wearable colours, and comfortable pieces that work with the life you already live.</p><p>Start with the essentials. Make them your own. Wear them on repeat.</p><a class="underlined-link" href="#all-products">Find your everyday pieces <span>↗</span></a></div></div></section>
<section class="section all-products" id="all-products"><div class="container"><div class="section-heading compact-heading"><div><p class="eyebrow dark-eyebrow">MORE TO LOVE</p><h2>The full edit<span class="period">.</span></h2></div><span class="item-count"><?= count($allProducts) ?> PIECES</span></div><div class="mini-product-grid"><?php foreach ($allProducts as $product): ?><article class="mini-product"><a class="mini-product-link" href="product.php?id=<?= (int) $product['id'] ?>"><img src="assets/images/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy"></a><div><p class="product-category"><?= e($product['category']) ?></p><h3><?= e($product['name']) ?></h3><p class="mini-description"><?= e($product['description']) ?></p><div class="mini-meta"><strong><?= money($product['price']) ?></strong><span class="rating"><span>★</span> <?= number_format((float) $product['rating'], 1) ?></span></div><form method="post" action="add_to_cart.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>"><button class="mini-add" type="submit">Add to cart +</button></form></div></article><?php endforeach; ?></div></div></section>
<section class="newsletter"><div class="container newsletter-inner"><div><p class="eyebrow">A NOTE IN YOUR INBOX</p><h2>Good things, occasionally.</h2><p>New arrivals, outfit notes, and little reminders to slow down.</p></div><a class="button button-outline-light" href="mailto:hello@jclothes.example?subject=Keep%20me%20in%20the%20loop">Say hello <span>↗</span></a></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
