/* Simply Park – site scripts (vanilla JS, no jQuery / WordPress) */
(function () {
    'use strict';

    document.documentElement.classList.remove('no-js');

    /* ---------------------------------------------------------------
       1. Reveal-on-scroll animations
       --------------------------------------------------------------- */
    var reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        reveals.forEach(function (el) { io.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('is-visible'); });
    }

    /* ---------------------------------------------------------------
       2. 3D carousels
       --------------------------------------------------------------- */
    var VISIBLE_RANGE = 3;

    function initCarousel(wrap) {
        var cards = wrap.querySelectorAll('.carousel-card');
        var infos = wrap.querySelectorAll('.carousel-info-item');
        var track = wrap.querySelector('.carousel-track');
        var total = cards.length;
        var current = 0;
        var autoplay = wrap.dataset.autoplay === 'true';
        var speed = parseInt(wrap.dataset.autoplaySpeed, 10) || 3000;
        var timer = null;
        var dragging = false;
        var dragStartX = 0;
        var THRESHOLD = 50;

        if (!total) return;

        function render() {
            cards.forEach(function (card, i) {
                var pos = i - current;
                if (pos > total / 2) pos -= total;
                if (pos < -total / 2) pos += total;

                card.classList.toggle('is-active', pos === 0);
                if (Math.abs(pos) <= VISIBLE_RANGE) {
                    card.dataset.pos = String(pos);
                } else {
                    card.dataset.pos = pos > 0 ? 'edge' : '-edge';
                }
            });
            infos.forEach(function (info) {
                info.hidden = info.dataset.for !== String(current);
            });
        }

        function goTo(index) {
            current = ((index % total) + total) % total;
            render();
        }
        function next() { goTo(current + 1); restart(); }
        function prev() { goTo(current - 1); restart(); }

        function start() {
            if (!autoplay) return;
            clearInterval(timer);
            timer = setInterval(function () { goTo(current + 1); }, speed);
        }
        function stop() { clearInterval(timer); }
        function restart() { if (autoplay) { stop(); start(); } }

        wrap.querySelector('.carousel-next').addEventListener('click', next);
        wrap.querySelector('.carousel-prev').addEventListener('click', prev);

        // click a side card to bring it to the centre
        cards.forEach(function (card) {
            card.addEventListener('click', function () {
                var pos = parseInt(card.dataset.pos, 10);
                if (isNaN(pos) || pos === 0) return;
                goTo(current + pos);
                restart();
            });
        });

        // keyboard
        wrap.setAttribute('tabindex', '0');
        wrap.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowRight') next();
            if (e.key === 'ArrowLeft') prev();
        });

        // touch + mouse swipe
        function dragStart(x) { dragging = true; dragStartX = x; }
        function dragEnd(x) {
            if (!dragging) return;
            dragging = false;
            var delta = x - dragStartX;
            if (Math.abs(delta) > THRESHOLD) { delta < 0 ? next() : prev(); }
        }
        track.addEventListener('touchstart', function (e) { dragStart(e.touches[0].clientX); }, { passive: true });
        track.addEventListener('touchend', function (e) { dragEnd(e.changedTouches[0].clientX); });
        track.addEventListener('mousedown', function (e) { dragStart(e.clientX); });
        document.addEventListener('mouseup', function (e) { dragEnd(e.clientX); });

        // pause on hover
        wrap.addEventListener('mouseenter', stop);
        wrap.addEventListener('mouseleave', start);

        goTo(0);
        start();
    }

    document.querySelectorAll('.carousel').forEach(initCarousel);

    /* ---------------------------------------------------------------
       3. Background video – make sure autoplay starts on all browsers
       --------------------------------------------------------------- */
    var video = document.querySelector('.site-video');
    if (video) {
        video.muted = true;
        var p = video.play();
        if (p && p.catch) p.catch(function () { /* autoplay blocked: controls remain */ });
    }

    /* ---------------------------------------------------------------
       4. Back-to-top button
       --------------------------------------------------------------- */
    var toTop = document.querySelector('.goto-top');
    if (toTop) {
        var onScroll = function () {
            toTop.classList.toggle('is-shown', window.scrollY > 300);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
        toTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();
