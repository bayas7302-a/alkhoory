(function () {
    var html = document.documentElement;
    var mqMobile = window.matchMedia('(max-width: 767px)');

    /* ---------- 1. mobile header: add a solid background once it sticks ---------- */
    var head = document.getElementById('masthead');
    if (head) {
        var ticking = false;
        var update = function () {
            ticking = false;
            head.classList.toggle('kp-stuck', mqMobile.matches && window.pageYOffset > 24);
        };
        window.addEventListener('scroll', function () {
            if (!ticking) { ticking = true; requestAnimationFrame(update); }
        }, { passive: true });
        if (mqMobile.addEventListener) { mqMobile.addEventListener('change', update); }
        update();
    }

    /* ---------- 1b. home: keep the header pinned through the scroll-video section ---------- */
    (function () {
        var hero = document.getElementById('kk-sh');
        var hdr = document.getElementById('masthead');
        var track = hero && hero.querySelector('.kk-sh__track');
        if (!hero || !hdr || !track) { return; }
        var mq = window.matchMedia('(min-width: 768px)');
        var raf = 0;
        function update() {
            raf = 0;
            if (!mq.matches) {
                hdr.classList.remove('kp-hero-pin');
                hdr.style.transform = '';
                return;
            }
            hdr.classList.add('kp-hero-pin');
            // the header leaves one screen-height before the end of the video section:
            // as soon as the pinned video frame starts to scroll away, the header moves up with it
            var lift = Math.min(0, track.getBoundingClientRect().bottom - window.innerHeight);
            hdr.style.transform = lift < 0 ? 'translate3d(0,' + Math.round(lift) + 'px,0)' : '';
        }
        function kick() { if (!raf) { raf = requestAnimationFrame(update); } }
        window.addEventListener('scroll', kick, { passive: true });
        window.addEventListener('resize', kick);
        if (mq.addEventListener) { mq.addEventListener('change', kick); }
        update();
    })();

    /* ---------- 1c. retailer logos: replace the stop-and-go carousel with a seamless continuous scroll ---------- */
    document.querySelectorAll('.elementor-widget-image-carousel').forEach(function (w) {
        if (w.querySelector('.kp-logos')) { return; }
        var seen = {}, srcs = [];
        w.querySelectorAll('.swiper-slide:not(.swiper-slide-duplicate) img').forEach(function (img) {
            var src = img.currentSrc || img.src || img.getAttribute('data-src');
            if (src && !seen[src]) { seen[src] = 1; srcs.push({ src: src, alt: img.alt || '' }); }
        });
        if (srcs.length < 2) { return; }
        var box = document.createElement('div');
        box.className = 'kp-logos';
        box.setAttribute('aria-label', 'Our retail partners');
        var track = document.createElement('div');
        track.className = 'kp-logos__track';
        // two identical halves: the animation moves exactly one half, so the loop has no gap or jump
        for (var r = 0; r < 2; r++) {
            for (var rep = 0; rep < 2; rep++) {
                srcs.forEach(function (s) {
                    var it = document.createElement('div');
                    it.className = 'kp-logos__item';
                    var im = document.createElement('img');
                    im.src = s.src; im.alt = r ? '' : s.alt; im.loading = 'eager'; im.decoding = 'async';
                    if (r) { it.setAttribute('aria-hidden', 'true'); }
                    it.appendChild(im);
                    track.appendChild(it);
                });
            }
        }
        box.appendChild(track);
        var host = w.querySelector('.elementor-widget-container') || w;
        host.appendChild(box);
        w.classList.add('kp-logos-on');
    });

    /* ---------- 1d. Arabic version: load Cairo once a page is translated (GTranslate) ---------- */
    (function () {
        var done = false;
        function check() {
            if (done) { return; }
            var h = document.documentElement;
            if (/^ar/i.test(h.getAttribute('lang') || '') || h.classList.contains('translated-rtl')) {
                done = true;
                if (!document.getElementById('kp-cairo')) {
                    var l = document.createElement('link');
                    l.id = 'kp-cairo'; l.rel = 'stylesheet';
                    l.href = 'https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap';
                    document.head.appendChild(l);
                }
            }
        }
        new MutationObserver(check).observe(document.documentElement, { attributes: true, attributeFilter: ['lang', 'class'] });
        check();
    })();

    /* ---------- 1e. contact form: wrap Submit so it can show the same shine-on-hover as the other buttons ---------- */
    document.querySelectorAll('.cf7-custom-form input[type="submit"], .cf7-custom-form .wpcf7-submit').forEach(function (btn) {
        if (btn.parentElement && btn.parentElement.classList.contains('kp-submit')) { return; }
        var wrap = document.createElement('span');
        wrap.className = 'kp-submit';
        btn.parentNode.insertBefore(wrap, btn);
        wrap.appendChild(btn);
    });

    /* ---------- 1f. Who We Are: new sparkle stars ---------- */
    (function () {
        var STAR = 'https://soharon.co.uk/off-29/wp-content/uploads/2026/11/kk-sparkle-star.png';
        ['1b9f740', '3686e69', 'f5a2f5e', 'e6c8adb'].forEach(function (id) {
            var img = document.querySelector('.elementor-153 .elementor-element-' + id + ' img');
            if (!img || img.classList.contains('kp-star')) { return; }
            img.removeAttribute('srcset');
            img.removeAttribute('sizes');
            img.removeAttribute('width');
            img.removeAttribute('height');
            img.loading = 'eager';
            img.src = STAR;
            img.alt = '';
            img.classList.add('kp-star');
        });
    })();

    /* ---------- 1g. Who We Are: "No" stays, the rest rotates smoothly ---------- */
    (function () {
        var h = document.querySelector('.elementor-153 .elementor-element-fb87731 .elementor-heading-title');
        if (!h || h.classList.contains('kp-rot-ready')) { return; }
        var phrases = ['hype.', 'gimmicks.', 'over-engineering.', 'premium price without premium value.'];
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        h.setAttribute('aria-label', 'No ' + phrases.join(' No '));
        h.textContent = '';
        var no = document.createElement('span');
        no.className = 'kp-no';
        no.textContent = 'No ';
        var rot = document.createElement('span');
        rot.className = 'kp-rot';
        rot.setAttribute('aria-hidden', 'true');
        no.setAttribute('aria-hidden', 'true');
        var items = phrases.map(function (t) {
            var s = document.createElement('span');
            s.className = 'kp-rot__item';
            var tx = document.createElement('span');
            tx.className = 'kp-rot__txt';
            tx.textContent = t;
            s.appendChild(tx);
            s.__t = tx;
            rot.appendChild(s);
            return s;
        });
        h.appendChild(no);
        h.appendChild(rot);
        h.classList.add('kp-rot-ready');

        var i = 2;   // start on "over-engineering"
        // each phrase gets the width it needs; a phrase too long for the line is shrunk to fit
        var narrow = window.matchMedia('(max-width: 767px)');
        var base = 0;
        // desktop/tablet: "No" and the phrase are always the same size; a phrase too long for the line
        // scales the whole line down (smoothly), the line height stays fixed so nothing below moves
        function apply(n) {
            var it = items[n];
            if (narrow.matches || !base) { h.style.removeProperty('font-size'); rot.style.width = ''; return; }
            h.style.setProperty('font-size', (base * it.__k).toFixed(2) + 'px', 'important');
            rot.style.width = (it.__em * 1.03).toFixed(4) + 'em';
        }
        // once the size change has finished, fit the box to the phrase's real width
        function settle() {
            if (narrow.matches || !base) { return; }
            var t = items[i].__t;
            rot.style.width = Math.ceil(t.getBoundingClientRect().width + 2) + 'px';
        }
        h.addEventListener('transitionend', function (e) { if (e.target === h && e.propertyName === 'font-size') { settle(); } });
        function fit() {
            h.style.removeProperty('font-size');
            h.style.removeProperty('height');
            h.style.removeProperty('line-height');
            items.forEach(function (it) { it.__t.style.fontSize = ''; });
            if (narrow.matches) {   // phones: stacked; a phrase that needs more than two lines is shrunk a little
                base = 0;
                rot.style.width = '';
                items.forEach(function (it) {
                    var k = 100;
                    while (it.__t.offsetHeight > it.clientHeight + 1 && k > 55) {
                        k -= 5;
                        it.__t.style.fontSize = k + '%';
                    }
                });
                return;
            }
            base = parseFloat(getComputedStyle(h).fontSize) || 0;
            var noW = no.offsetWidth, room = h.clientWidth - 4;
            items.forEach(function (it) {
                var w = it.__t.offsetWidth;
                it.__em = w / base;
                it.__k = Math.min(1, room / (noW + w));
            });
            h.style.setProperty('height', (base * 1.15).toFixed(1) + 'px', 'important');
            h.style.setProperty('line-height', (base * 1.15).toFixed(1) + 'px', 'important');
            apply(i);
        }
        function show(n, first) {
            items.forEach(function (it, k) {
                it.classList.toggle('is-active', k === n);
                it.classList.toggle('is-leaving', !first && k === i && k !== n);
                if (k !== n && k !== i) { it.classList.remove('is-leaving'); }
            });
            i = n;
            apply(n);
            setTimeout(settle, 760);
        }
        fit();
        show(i, true);
        if (document.fonts && document.fonts.ready) { document.fonts.ready.then(fit); }
        window.addEventListener('resize', function () { fit(); });
        if (reduce) { return; }

        var timer = null;
        function start() { if (!timer) { timer = setInterval(function () { show((i + 1) % items.length); }, 2600); } }
        function stop() { clearInterval(timer); timer = null; }
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (e) { if (e[0].isIntersecting) { start(); } else { stop(); } }).observe(h);
        } else { start(); }
    })();

    /* ---------- 2. product highlight images: every image gets the curtain reveal ----------
       The page animation starts the reveal when a picture scrolls into view, but these
       pictures are lazy-loaded, so only the first was ready in time. Here each one is
       downloaded early and revealed only once it is decoded. */
    var hl = [].slice.call(document.querySelectorAll('.kk-highlight'));
    if (hl.length && html.classList.contains('kk-anim') && 'IntersectionObserver' in window) {
        var ready = function (img) {
            return new Promise(function (resolve) {
                if (!img) { resolve(); return; }
                var done = false, ok = function () { if (!done) { done = true; resolve(); } };
                var dec = function () { if (img.decode) { img.decode().then(ok, ok); } else { ok(); } };
                if (img.complete && img.naturalWidth > 0) { dec(); } else {
                    img.addEventListener('load', dec, { once: true });
                    img.addEventListener('error', ok, { once: true });
                }
                setTimeout(ok, 5000);
            });
        };
        var hio = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) { return; }
                var el = e.target;
                hio.unobserve(el);
                ready(el.querySelector('img')).then(function () {
                    requestAnimationFrame(function () {
                        el.classList.add('kk-in');
                        setTimeout(function () { el.classList.add('kk-done'); el.classList.remove('kk-in'); }, 2100);
                    });
                });
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
        hl.forEach(function (el) {
            if (el.classList.contains('kk-done')) { return; }
            el.__kkRevealed = true;   // the general page animation leaves these to us
            el.__kkDone = true;
            var img = el.querySelector('img');
            if (img) { img.loading = 'eager'; img.decoding = 'async'; }
            hio.observe(el);
        });
    }

    /* ---------- 3. catalogue filters: phones only (<= 767px) ----------
       Accordion groups inside a slide-up drawer, opened from a "Filter" button.
       On tablet / desktop the original sidebar is left exactly as it was. */
    var side = document.querySelector('.kk-cat__side');
    if (!side) { return; }

    var home = document.createComment('kp-filters');
    side.parentNode.insertBefore(home, side);
    var main = document.querySelector('.kk-cat__main');
    var built = null, lastFocus = null;

    function setOpen(g, open) {
        g.classList.toggle('is-open', open);
        var b = g.querySelector('.kp-acc__btn');
        if (b) { b.setAttribute('aria-expanded', open ? 'true' : 'false'); }
    }

    function openDrawer() {
        if (!built) { return; }
        lastFocus = document.activeElement;
        html.classList.add('kp-filters-open');
        built.openBtn.setAttribute('aria-expanded', 'true');
        side.setAttribute('aria-hidden', 'false');
        setTimeout(function () { var c = side.querySelector('.kp-drawer__close'); if (c) { c.focus(); } }, 60);
    }
    function closeDrawer() {
        html.classList.remove('kp-filters-open');
        if (!built) { return; }
        built.openBtn.setAttribute('aria-expanded', 'false');
        side.setAttribute('aria-hidden', 'true');
        if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
    }
    function onKey(e) { if (e.key === 'Escape' && html.classList.contains('kp-filters-open')) { closeDrawer(); } }

    function build() {
        if (built) { return; }
        var b = { groups: [] };
        var uid = 0, activeGroup = null;

        [].slice.call(side.querySelectorAll('.kk-cat__group')).forEach(function (g) {
            var label = g.querySelector('.kk-cat__label');
            var list  = g.querySelector('.kk-cat__list');
            if (!label || !list) { return; }
            var id  = 'kp-acc-' + (++uid);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kp-acc__btn';
            btn.setAttribute('aria-controls', id);
            btn.innerHTML = '<span>' + label.textContent + '<span class="kp-acc__count">' + list.querySelectorAll('li').length + '</span></span><span class="kp-acc__icon" aria-hidden="true"></span>';
            var panel = document.createElement('div');
            panel.className = 'kp-acc__panel';
            panel.id = id;
            list.parentNode.insertBefore(panel, list);
            panel.appendChild(list);
            label.parentNode.insertBefore(btn, label);
            label.hidden = true;
            g.classList.add('kp-acc');
            btn.addEventListener('click', function () { setOpen(g, !g.classList.contains('is-open')); });
            if (!activeGroup && list.querySelector('.kk-cat__link.is-active')) { activeGroup = g; }
            b.groups.push({ g: g, label: label, list: list, btn: btn, panel: panel });
        });
        b.groups.forEach(function (x, k) { setOpen(x.g, activeGroup ? x.g === activeGroup : k === 0); });

        var body = document.createElement('div');
        body.className = 'kp-drawer__body';
        while (side.firstChild) { body.appendChild(side.firstChild); }

        var head = document.createElement('div');
        head.className = 'kp-drawer__head';
        head.innerHTML = '<p class="kp-drawer__title" id="kp-drawer-title">Filters</p><button type="button" class="kp-drawer__close" aria-label="Close filters">&times;</button>';

        var shopUrl = (document.querySelector('.kk-hero__crumbs a:nth-of-type(2)') || {}).href || '';
        var foot = document.createElement('div');
        foot.className = 'kp-drawer__foot';
        foot.innerHTML = (shopUrl ? '<a class="kp-drawer__reset" href="' + shopUrl + '">Show all</a>' : '') +
                         '<button type="button" class="kp-drawer__done">Done</button>';

        side.appendChild(head);
        side.appendChild(body);
        side.appendChild(foot);
        if (!side.id) { side.id = 'kp-filters'; b.setId = true; }
        side.setAttribute('aria-labelledby', 'kp-drawer-title');
        side.setAttribute('role', 'dialog');
        side.setAttribute('aria-modal', 'true');
        side.setAttribute('aria-hidden', 'true');
        side.classList.add('kp-drawer');
        document.body.appendChild(side);   // above the header and the overlay

        var overlay = document.createElement('div');
        overlay.className = 'kp-drawer-overlay';
        document.body.appendChild(overlay);

        var current = side.querySelector('.kk-cat__link.is-active');
        var bar = document.createElement('div');
        bar.className = 'kp-filterbar';
        bar.innerHTML =
            '<button type="button" class="kp-filterbar__btn" aria-controls="' + side.id + '" aria-expanded="false">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>Filter' +
            '</button>' +
            (current ? '<span class="kp-filterbar__chip">Showing: <b>' + current.textContent + '</b></span>' : '');
        if (main) { main.insertBefore(bar, main.firstChild); }

        b.body = body; b.head = head; b.foot = foot; b.overlay = overlay; b.bar = bar;
        b.openBtn = bar.querySelector('.kp-filterbar__btn');
        b.openBtn.addEventListener('click', openDrawer);
        overlay.addEventListener('click', closeDrawer);
        head.querySelector('.kp-drawer__close').addEventListener('click', closeDrawer);
        foot.querySelector('.kp-drawer__done').addEventListener('click', closeDrawer);
        document.addEventListener('keydown', onKey);
        built = b;
    }

    function destroy() {
        if (!built) { return; }
        var b = built;
        html.classList.remove('kp-filters-open');
        document.removeEventListener('keydown', onKey);
        b.groups.forEach(function (x) {
            x.label.hidden = false;
            x.panel.parentNode.insertBefore(x.list, x.panel);
            x.panel.parentNode.removeChild(x.panel);
            x.btn.parentNode.removeChild(x.btn);
            x.g.classList.remove('kp-acc', 'is-open');
        });
        while (b.body.firstChild) { side.insertBefore(b.body.firstChild, b.head); }
        [b.head, b.body, b.foot, b.overlay, b.bar].forEach(function (n) { if (n.parentNode) { n.parentNode.removeChild(n); } });
        ['role', 'aria-modal', 'aria-hidden', 'aria-labelledby'].forEach(function (a) { side.removeAttribute(a); });
        if (b.setId) { side.removeAttribute('id'); }
        side.classList.remove('kp-drawer');
        home.parentNode.insertBefore(side, home.nextSibling);
        built = null;
    }

    function sync() { if (mqMobile.matches) { build(); } else { destroy(); } }
    if (mqMobile.addEventListener) { mqMobile.addEventListener('change', sync); }
    else if (mqMobile.addListener) { mqMobile.addListener(sync); }
    sync();
})();
