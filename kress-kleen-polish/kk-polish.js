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

    /* ---------- 1a. text fixes (design v3 Phase 0) ----------
       Corrects the exact typos only; once fixed in Elementor these do nothing. */
    (function () {
        var fixes = [
            [/\bCatelouge\b/g, 'Catalogue'],             // X-18
            [/\bCatelog\b/g, 'Catalogue'],               // X-17, N-01
            [/\bLets Connect\b/g, 'Let’s Connect'], // X-19
            [/\bWe oer\b/g, 'We offer'],                 // W-26
            [/\bEortless\b/g, 'Effortless']              // F-10
        ];
        var scope = document.querySelectorAll('.elementor-30, .elementor-43, .elementor-132, .kk-side, .kk-hero');
        scope.forEach(function (root) {
            var w = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null), n;
            while ((n = w.nextNode())) {
                var v = n.nodeValue, o = v;
                fixes.forEach(function (f) { v = v.replace(f[0], f[1]); });
                if (v !== o) { n.nodeValue = v; }
            }
        });
        // W-21: label above the Where to Buy brand heading
        var lbl = document.querySelector('.elementor-132 .elementor-element-142ce73 .elementor-heading-title');
        if (lbl && /^\s*FEATURED PRODUCT\s*$/i.test(lbl.textContent)) { lbl.textContent = 'WHO WE ARE'; lbl.classList.add('kp-w21'); }
    })();

    /* ---------- 1b. footer links (X-21, X-25) ----------
       "Contact" and "Let's Connect" go to the contact form on the home page.
       Only fills a link that is missing (no link / "#"), so links set in Elementor win. */
    (function () {
        var footer = document.querySelector('.elementor-30');
        if (!footer) { return; }
        var homeLink = footer.querySelector('.elementor-element-501b3e5 a');
        var home = homeLink ? homeLink.href.split('#')[0] : '/';
        var target = home + '#contact';

        // give the home page contact section an anchor
        var form = document.querySelector('.elementor-43 .elementor-element-1c24237');
        if (form && !document.getElementById('contact')) { form.id = 'contact'; }

        footer.querySelectorAll('.elementor-element-501b3e5 .elementor-icon-list-item').forEach(function (li) {
            if (li.querySelector('a')) { return; }
            if (!/^\s*contact\s*$/i.test(li.textContent)) { return; }
            var a = document.createElement('a');
            a.href = target;
            while (li.firstChild) { a.appendChild(li.firstChild); }
            li.appendChild(a);
        });
        var lc = footer.querySelector('.elementor-element-cec317d a.elementor-button');
        if (lc) {
            var h = lc.getAttribute('href');
            if (!h || h === '#') { lc.setAttribute('href', target); }
        }
    })();

    // forced line breaks from the design (shown on desktop only, .kp-br)
    function kpAddBreak(el, phrase) {
        if (!el || el.__kpBr && el.__kpBr[phrase]) { return; }
        var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null), nodes = [], full = '', n;
        while ((n = walker.nextNode())) { nodes.push({ node: n, start: full.length }); full += n.nodeValue; }
        var re = new RegExp(phrase.trim().split(/\s+/).map(function (w) { return w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }).join('\\s+'));
        var m = re.exec(full);
        if (!m) { return; }
        var hit = null;
        for (var i = nodes.length - 1; i >= 0; i--) { if (nodes[i].start <= m.index) { hit = nodes[i]; break; } }
        if (!hit) { return; }
        var br = document.createElement('br');
        br.className = 'kp-br';
        var word = hit.node.parentElement && hit.node.parentElement.closest('.kk-w');
        if (word && el.contains(word)) {
            word.parentNode.insertBefore(br, word);
        } else {
            var after = hit.node.splitText(m.index - hit.start);
            after.parentNode.insertBefore(br, after);
        }
        (el.__kpBr = el.__kpBr || {})[phrase] = true;
    }

    /* ---------- 1c. home page (design v3 Phase 4) ---------- */
    (function () {
        var page = document.querySelector('.elementor-43');
        if (!page) { return; }

        var T = function (id, inner) { return page.querySelector('.elementor-element-' + id + ' ' + (inner || '.elementor-heading-title')); };
        [
            ['9a43ca3', null, ['TO KLEEN']],                                    // G-04
            ['1bde407', null, ['design, practical', 'make everyday']],          // G-05
            ['63b71db', '.elementor-icon-box-description', ['cleaning easier']],// G-10
            ['704d045', '.elementor-icon-box-description', ['Built to last']],  // G-10
            ['ed3bfa7', null, ['dust and dirt']],                               // S-10 (design)
            ['21ba642', null, ['fresh and spotless']],                          // S-10
            ['07011f8', null, ['clean and shine']],                             // S-10 (design)
            ['ccba669', null, ['with ease']],                                   // S-10
            ['ce02014', null, ['keep it dust']],                                // S-10
            ['884f790', null, ['MANY CLEANING', 'CHALLENGES']],                 // O-04
            ['36490c5', null, ['EASY-WRING', 'MOP BUCKET']],                    // F-08
            ['9d92933', null, ['get in touch']]                                 // C-04
        ].forEach(function (r) {
            var el = T(r[0], r[1]);
            r[2].forEach(function (ph) { kpAddBreak(el, ph); });
        });

        // N-07: Sweep and Mop cards go to their solution pages, like Wipe / Scrub / Dust
        [['48efe92', /\/product-category\/brooms\/?$/, 'solution/sweep/'], ['fe08656', /\/product-category\/mops\/?$/, 'solution/mop/']].forEach(function (r) {
            var a = page.querySelector('.elementor-element-' + r[0] + ' a.elementor-button');
            if (a && r[1].test(a.getAttribute('href') || '')) { a.setAttribute('href', a.href.replace(r[1], '/' + r[2])); }
        });

        // O-10: on desktop the area pop-up opens to the LEFT of its hotspot, joined by a thin line
        var hs = document.getElementById('sk-hs');
        if (hs) {
            var line = document.createElement('span');
            line.className = 'kp-hs-line';
            line.setAttribute('aria-hidden', 'true');
            line.style.cssText = 'position:absolute;z-index:2;height:1px;background:#ffffff;box-shadow:0 0 2px rgba(0,0,0,.35);pointer-events:none;display:none;';
            hs.appendChild(line);
            var desk = window.matchMedia('(min-width: 1025px)');
            var placeLeft = function (card) {
                line.style.display = 'none';
                if (!desk.matches || !card.classList.contains('is-open')) { return; }
                var dot = hs.querySelector('.sk-hs__dot[data-target="' + card.id + '"]');
                if (!dot) { return; }
                var px = dot.offsetLeft + dot.offsetWidth / 2, py = dot.offsetTop + dot.offsetHeight / 2;
                var r = dot.offsetWidth / 2, gap = 64, cw = card.offsetWidth, ch = card.offsetHeight;
                var x = px - r - gap - cw;
                if (x < 8) { return; }   // no room on the left: keep the original placement
                var y = Math.max(8, Math.min(py - 52, hs.clientHeight - ch - 8));
                card.style.left = x + 'px';
                card.style.top = y + 'px';
                line.style.left = (x + cw) + 'px';
                line.style.top = py + 'px';
                line.style.width = (gap) + 'px';
                line.style.display = 'block';
            };
            hs.querySelectorAll('.sk-hs__card').forEach(function (card) {
                new MutationObserver(function () { placeLeft(card); }).observe(card, { attributes: true, attributeFilter: ['class'] });
            });
            window.addEventListener('resize', function () { var open = hs.querySelector('.sk-hs__card.is-open'); if (open) { placeLeft(open); } else { line.style.display = 'none'; } });
        }
    })();

    /* ---------- 1d. Where to Buy (design v3 Phase 5) ---------- */
    (function () {
        var page = document.querySelector('.elementor-132');
        if (!page) { return; }
        var T = function (id) { return page.querySelector('.elementor-element-' + id + ' .elementor-heading-title'); };
        kpAddBreak(T('846c896'), 'specializing in household');   // W-23: line 1 ends "brand"
        kpAddBreak(T('e1a9a94'), 'We o');                         // W-27: "We offer…" starts line 2
        kpAddBreak(T('e1a9a94'), 'performing square');            // W-27: "performing…" starts line 3
    })();

    /* ---------- 1e. Catalogue cards (design v3 Phase 6, K-22): name on two lines, after the closing quote ---------- */
    document.querySelectorAll('.kk-cat ul.products .woocommerce-loop-product__title').forEach(function (t) {
        if (t.querySelector('.kp-br')) { return; }
        var m = /^(.*”)\s+(.+)$/.exec(t.textContent.trim());
        if (!m || t.children.length) { return; }
        t.textContent = '';
        t.appendChild(document.createTextNode(m[1] + ' '));
        var br = document.createElement('br'); br.className = 'kp-br'; t.appendChild(br);
        t.appendChild(document.createTextNode(m[2]));
    });

    /* ---------- 1f. product page scroll-down box (design v3 Phase 7, P-15) ---------- */
    (function () {
        var info = document.querySelector('.kk-sp .kk-pinfo');
        var target = document.querySelector('.kk-sp .kk-highlights, .kk-sp #kk-highlight');
        if (!info || !target || info.querySelector('.kp-sp-down')) { return; }
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'kp-sp-down';
        b.setAttribute('aria-label', 'Scroll to the product highlights');
        b.addEventListener('click', function () {
            var y = target.getBoundingClientRect().top + window.pageYOffset - 24;
            window.scrollTo({ top: y, behavior: 'smooth' });
        });
        info.appendChild(b);
    })();

    /* ---------- 1g. Who We Are (design v3 Phase 8): A-17 four lines, A-22 line 1 ends "affordable doesn't" ---------- */
    (function () {
        var page = document.querySelector('.elementor-153');
        if (!page) { return; }
        var T = function (id) { return page.querySelector('.elementor-element-' + id + ' .elementor-heading-title'); };
        kpAddBreak(T('f6db65f'), 'have to mean ordinary');
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
