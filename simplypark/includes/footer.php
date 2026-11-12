<footer class="site-footer">
    <div class="footer-cols">
        <div class="footer-col footer-col-logo">
            <a class="footer-logo" href="./" title="<?= e($site['name']) ?>">
                <img src="<?= e($site['logo']) ?>" width="365" height="150" alt="<?= e($site['name']) ?>" loading="lazy">
            </a>
        </div>
        <div class="footer-col">
            <h2 class="footer-title">Contact Us</h2>
            <ul class="footer-list footer-list-contact">
                <li><span><?= e($contact['address']) ?></span></li>
                <li><a href="mailto:<?= e($contact['email']) ?>"><span><?= e($contact['email']) ?></span></a></li>
                <li><a href="tel:<?= e(preg_replace('/[^+0-9]/', '', $contact['phone'])) ?>"><span><?= e($contact['phone']) ?></span></a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h2 class="footer-title">Help</h2>
            <ul class="footer-list">
                <?php foreach ($help_links as [$label, $url]): ?>
                <li><?php if ($url): ?><a href="<?= e($url) ?>"><span><?= e($label) ?></span></a><?php else: ?><span><?= e($label) ?></span><?php endif; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="footer-col footer-col-empty"></div>
    </div>
    <div class="footer-bar">
        <p class="footer-version"><?= e($site['version']) ?></p>
        <p class="footer-copy">© <?= e($site['year']) ?> SIMPLYPARK | ALL RIGHTS RESERVED</p>
    </div>
</footer>

<button class="goto-top" type="button" aria-label="Back to top">
    <span class="goto-top-overlay"></span>
    <svg class="goto-top-arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
    <svg class="goto-top-arrow goto-top-arrow-hover" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
</button>

<script src="assets/js/main.js" defer></script>
</body>
</html>
