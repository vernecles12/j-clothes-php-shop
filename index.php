<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Thoughtful everyday style';
$pdo = db();
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
<section class="editorial section" id="about"><div class="container editorial-grid"><div class="editorial-art"><div class="art-orbit"></div><div class="journal-photo-card"><img src="assets/images/journal-notes.jpg" alt="J Clothes style journal"><span>J / JOURNAL</span><strong>FIND YOUR<br>OWN PACE.</strong><small>STYLE NOTES — NO. 01</small></div><span class="art-caption">A softer kind of statement.</span></div><div class="editorial-copy"><p class="eyebrow dark-eyebrow">A NOTE FROM J CLOTHES</p><h2>Good style should feel <em>easy.</em></h2><p>J Clothes is built around a simple idea: getting dressed should be one of the easiest parts of your day. We bring together versatile silhouettes, wearable colours, and comfortable pieces that work with the life you already live.</p><p>Start with the essentials. Make them your own. Wear them on repeat.</p><a class="underlined-link" href="shop.php">Find your everyday pieces <span>↗</span></a></div></div></section>
<section class="newsletter"><div class="container newsletter-inner"><div><p class="eyebrow">A NOTE IN YOUR INBOX</p><h2>Good things, occasionally.</h2><p>New arrivals, outfit notes, and little reminders to slow down.</p></div><a class="button button-outline-light" href="mailto:hello@jclothes.example?subject=Keep%20me%20in%20the%20loop">Say hello <span>↗</span></a></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
