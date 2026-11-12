<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/header.php';
?>
<main>
    <!-- ===== HERO ===== -->
    <section class="hero">
        <div class="hero-inner">
            <div class="hero-heading-wrap">
                <h1 class="hero-heading reveal" data-anim="fade-in-up">
                    <span class="hero-before"><?= e($hero['before']) ?></span><span class="hero-highlight"><?= e($hero['highlight']) ?></span>
                </h1>
            </div>

            <div class="hero-logo">
                <img src="<?= e($hero['image']) ?>" width="2077" height="1899" alt="" fetchpriority="high">
            </div>

            <div class="hero-timeline">
                <?php foreach ($hero['items'] as $i => $item): ?>
                <?php if ($i > 0): ?>
                <div class="hero-line reveal" data-anim="slide-from-top">
                    <img src="assets/img/line-1.png" width="25" height="464" alt="">
                </div>
                <?php endif; ?>
                <div class="hero-item reveal<?= $i === 0 ? ' hero-item-slow' : '' ?>" data-anim="fade-in-down">
                    <div class="hero-item-icon">
                        <img src="<?= e($item['icon']) ?>" width="150" height="149" alt="" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
                    </div>
                    <div class="hero-item-content">
                        <h4 class="hero-item-title<?= $item['dark'] ? ' is-dark' : '' ?>"><?= e($item['title']) ?></h4>
                        <p><?= e($item['text']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ===== CAROUSELS ===== -->
    <?php foreach ($carousels as $c): ?>
    <section class="carousel-section carousel-section-<?= e($c['id']) ?>">
        <div class="carousel-inner">
            <div class="carousel carousel-<?= e($c['layout']) ?>" id="<?= e($c['id']) ?>" data-autoplay="false" data-autoplay-speed="3000">
                <h2 class="carousel-heading"><?= e($c['heading']) ?></h2>
                <div class="carousel-stage">
                    <button class="carousel-arrow carousel-prev" type="button" aria-label="Previous">
                        <img src="assets/img/Arrow-Left.png" width="337" height="366" alt="">
                    </button>
                    <div class="carousel-track">
                        <?php foreach ($c['slides'] as $i => [$img, $label]): ?>
                        <div class="carousel-card" data-index="<?= $i ?>">
                            <img src="assets/img/<?= e($img) ?>" alt="<?= e($label) ?>" loading="lazy" draggable="false">
                            <div class="carousel-card-overlay"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-arrow carousel-next" type="button" aria-label="Next">
                        <img src="assets/img/Arrow-Right.png" width="337" height="366" alt="">
                    </button>
                </div>
                <div class="carousel-info" aria-live="polite">
                    <?php foreach ($c['slides'] as $i => [$img, $label]): ?>
                    <div class="carousel-info-item" data-for="<?= $i ?>"<?= $i ? ' hidden' : '' ?>>
                        <p class="carousel-label"><?= e($label) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endforeach; ?>

    <!-- ===== VIDEO ===== -->
    <section class="video-section">
        <video class="site-video" src="<?= e($video) ?>" autoplay loop muted playsinline controls controlslist="nodownload" preload="metadata"></video>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
