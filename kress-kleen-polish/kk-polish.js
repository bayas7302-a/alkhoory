(function () {
    var html = document.documentElement;
    var mqMobile = window.matchMedia('(max-width: 767px)');
    var mqDrawer = window.matchMedia('(max-width: 1024px)');

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

    /* ---------- 2. catalogue filters: accordion (desktop) + drawer (tablet / mobile) ---------- */
    var side = document.querySelector('.kk-cat__side');
    if (!side) { return; }

    var groups = [].slice.call(side.querySelectorAll('.kk-cat__group'));
    var uid = 0;

    groups.forEach(function (g) {
        var label = g.querySelector('.kk-cat__label');
        var list  = g.querySelector('.kk-cat__list');
        if (!label || !list) { return; }

        var id  = 'kp-acc-' + (++uid);
        var act = list.querySelector('.kk-cat__link.is-active');
        var n   = list.querySelectorAll('li').length;

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'kp-acc__btn';
        btn.setAttribute('aria-controls', id);
        btn.innerHTML = '<span>' + label.textContent + '<span class="kp-acc__count">' + n + '</span></span><span class="kp-acc__icon" aria-hidden="true"></span>';

        var panel = document.createElement('div');
        panel.className = 'kp-acc__panel';
        panel.id = id;
        list.parentNode.insertBefore(panel, list);
        panel.appendChild(list);
        label.parentNode.replaceChild(btn, label);

        g.classList.add('kp-acc');
        g.__kpActive = act;

        btn.addEventListener('click', function () { setOpen(g, !g.classList.contains('is-open')); });
    });

    function setOpen(g, open) {
        g.classList.toggle('is-open', open);
        var b = g.querySelector('.kp-acc__btn');
        if (b) { b.setAttribute('aria-expanded', open ? 'true' : 'false'); }
    }

    // open the group that holds the current filter, otherwise the first one
    var activeGroup = groups.filter(function (g) { return g.__kpActive; })[0];
    groups.forEach(function (g) { setOpen(g, g === (activeGroup || groups[0])); });

    /* drawer pieces */
    var body = document.createElement('div');
    body.className = 'kp-drawer__body';
    while (side.firstChild) { body.appendChild(side.firstChild); }

    var headEl = document.createElement('div');
    headEl.className = 'kp-drawer__head';
    headEl.innerHTML = '<p class="kp-drawer__title" id="kp-drawer-title">Filters</p><button type="button" class="kp-drawer__close" aria-label="Close filters">&times;</button>';

    var shopUrl = (document.querySelector('.kk-hero__crumbs a:nth-of-type(2)') || {}).href || '';
    var foot = document.createElement('div');
    foot.className = 'kp-drawer__foot';
    foot.innerHTML = (shopUrl ? '<a class="kp-drawer__reset" href="' + shopUrl + '">Show all</a>' : '') +
                     '<button type="button" class="kp-drawer__done">Done</button>';

    side.appendChild(headEl);
    side.appendChild(body);
    side.appendChild(foot);
    side.id = side.id || 'kp-filters';
    side.setAttribute('aria-labelledby', 'kp-drawer-title');

    var overlay = document.createElement('div');
    overlay.className = 'kp-drawer-overlay';
    document.body.appendChild(overlay);

    /* filter bar above the products */
    var main = document.querySelector('.kk-cat__main');
    var current = document.querySelector('.kk-cat__side .kk-cat__link.is-active');
    var bar = document.createElement('div');
    bar.className = 'kp-filterbar';
    bar.innerHTML =
        '<button type="button" class="kp-filterbar__btn" aria-controls="' + side.id + '" aria-expanded="false">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>Filter' +
        '</button>' +
        (current ? '<span class="kp-filterbar__chip">Showing: <b>' + current.textContent + '</b></span>' : '');
    if (main) { main.insertBefore(bar, main.firstChild); }
    var openBtn = bar.querySelector('.kp-filterbar__btn');

    // in drawer mode the panel lives at the end of <body>, so it sits above the header and the overlay
    var home = document.createComment('kp-filters');
    side.parentNode.insertBefore(home, side);
    function place() {
        if (mqDrawer.matches) {
            if (side.parentNode !== document.body) { document.body.appendChild(side); }
            side.classList.add('kp-drawer');
        } else {
            if (home.parentNode && side.previousSibling !== home) { home.parentNode.insertBefore(side, home.nextSibling); }
            side.classList.remove('kp-drawer');
        }
    }

    var lastFocus = null;
    function syncAria() {
        place();
        var isDrawer = mqDrawer.matches;
        if (isDrawer) {
            side.setAttribute('role', 'dialog');
            side.setAttribute('aria-modal', 'true');
            side.setAttribute('aria-hidden', html.classList.contains('kp-filters-open') ? 'false' : 'true');
        } else {
            side.removeAttribute('role');
            side.removeAttribute('aria-modal');
            side.removeAttribute('aria-hidden');
            html.classList.remove('kp-filters-open');
        }
    }
    function openDrawer() {
        lastFocus = document.activeElement;
        html.classList.add('kp-filters-open');
        openBtn.setAttribute('aria-expanded', 'true');
        syncAria();
        setTimeout(function () { var c = side.querySelector('.kp-drawer__close'); if (c) { c.focus(); } }, 60);
    }
    function closeDrawer() {
        html.classList.remove('kp-filters-open');
        openBtn.setAttribute('aria-expanded', 'false');
        syncAria();
        if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
    }

    openBtn.addEventListener('click', openDrawer);
    overlay.addEventListener('click', closeDrawer);
    side.querySelector('.kp-drawer__close').addEventListener('click', closeDrawer);
    side.querySelector('.kp-drawer__done').addEventListener('click', closeDrawer);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && html.classList.contains('kp-filters-open')) { closeDrawer(); }
    });
    if (mqDrawer.addEventListener) { mqDrawer.addEventListener('change', syncAria); }
    syncAria();
})();
