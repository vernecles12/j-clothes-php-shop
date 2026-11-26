</main>
<?php $footerPrefix = str_contains($_SERVER['PHP_SELF'], '/auth/') ? '../' : ''; ?>
<footer class="site-footer" id="contact">
    <div class="container footer-grid">
        <div><a class="brand brand-footer" href="<?= e($footerPrefix) ?>index.php"><span class="brand-mark">J</span><span>J CLOTHES<small>wear well, live well</small></span></a><p class="footer-note">Pieces that feel like you. Made for everyday moments, chosen to stay in rotation.</p></div>
        <div><p class="footer-heading">Explore</p><a href="<?= e($footerPrefix) ?>shop.php">Shop all</a><a href="<?= e($footerPrefix) ?>index.php#about">Our story</a><a href="<?= e($footerPrefix) ?>cart.php">Shopping bag</a></div>
        <div><p class="footer-heading">Get in touch</p><a href="mailto:hello@jclothes.example">hello@jclothes.example</a><p>Monday – Friday<br>9:00 AM – 5:00 PM</p></div>
    </div>
    <div class="container footer-bottom"><span>© <?= date('Y') ?> J Clothes. All rights reserved.</span><span>Thoughtful style, naturally.</span></div>
</footer>
</body>
</html>
