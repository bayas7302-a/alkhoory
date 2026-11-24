(function () {
    /* Kress Kleen home hero – step scrolling.
       One scroll / swipe / key press = move to the next (or previous) card.
       Forward moves PLAY the video between the two cards; backward moves play a
       reversed copy of the video, so both directions are smooth (no seeking). */
    var FWD = 'https://soharon.co.uk/off-29/wp-content/uploads/2026/11/kress-kleen-hero-720-fwd.mp4';
    var REV = 'https://soharon.co.uk/off-29/wp-content/uploads/2026/11/kress-kleen-hero-720-rev.mp4';

    var root = document.getElementById('kk-sh');
    if (!root || root.kkShReady) { return; }
    root.kkShReady = true;
    var editor = document.body.classList.contains('elementor-editor-active') || /elementor-preview/.test(window.location.search);
    if (editor) { return; }
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var track = root.querySelector('.kk-sh__track');
    var stick = root.querySelector('.kk-sh__stick');
    var vf = root.querySelector('.kk-sh__video');
    var poster = root.querySelector('.kk-sh__poster');
    var bar = root.querySelector('.kk-sh__bar i');
    var cards = [].slice.call(root.querySelectorAll('.kk-sh__card'));
    var dots = [].slice.call(root.querySelectorAll('.kk-sh__dot'));

    // checked frame by frame in the video:
    // TIMES = where the video stops for each card (product fully in view, steady)
    // CUTS  = when each product first appears (the card text changes here)
    var TIMES = [0, 2.8, 5.0, 9.6, 11.6, 15.0, 18.0, 21.2, 23.8, 27.0, 29.8];
    var CUTS  = [0, 2.0, 4.5, 9.1, 10.9, 14.4, 16.9, 20.3, 22.8, 25.8, 29.0];
    if (TIMES.length !== cards.length) {
        try { TIMES = JSON.parse(root.getAttribute('data-times')); } catch (e) {}
        CUTS = TIMES.slice();
    }
    var DUR = 35.235;
    var INTRO = parseFloat(root.getAttribute('data-intro')) || 30;
    var PERVH = 24;   // scroll length per video second (vh) – only sets how long the section is
    var LAST = TIMES.length - 1;

    root.classList.add('is-ready', 'kk-step');
    root.style.setProperty('--kk-sh-len', (100 + INTRO + DUR * PERVH) + 'vh');

    /* ---------- videos ---------- */
    function prep(v, src) {
        v.muted = true; v.defaultMuted = true; v.playsInline = true;
        v.setAttribute('muted', ''); v.setAttribute('playsinline', ''); v.setAttribute('webkit-playsinline', '');
        v.preload = 'auto';
        v.removeAttribute('autoplay'); v.loop = false;
        if (src && v.getAttribute('src') !== src) { v.setAttribute('src', src); v.load(); }
    }
    prep(vf, FWD);
    var vr = vf.cloneNode(false);
    vr.classList.add('kk-sh__video--rev');
    prep(vr, REV);
    vf.parentNode.insertBefore(vr, vf.nextSibling);
    var active = vf;
    vf.classList.add('is-on');

    function unlock() {
        [vf, vr].forEach(function (v) {
            var p; try { p = v.play(); } catch (e) {}
            if (p && p.then) { p.then(function () { if (!busy) { v.pause(); } }).catch(function () {}); }
        });
    }
    ['touchstart', 'pointerdown', 'keydown', 'wheel'].forEach(function (ev) {
        window.addEventListener(ev, function once() { window.removeEventListener(ev, once); unlock(); }, { passive: true });
    });

    function seek(v, t) {
        return new Promise(function (res) {
            t = Math.max(0, Math.min(DUR - 0.04, t));
            if (Math.abs(v.currentTime - t) < 0.02 && v.readyState >= 2) { res(); return; }
            var done = false;
            function ok() { if (!done) { done = true; v.removeEventListener('seeked', ok); res(); } }
            v.addEventListener('seeked', ok);
            setTimeout(ok, 700);
            try { v.currentTime = t; } catch (e) { ok(); }
        });
    }
    function showVideo(v) {
        if (active === v) { return; }
        v.classList.add('is-on');
        active.classList.remove('is-on');
        active = v;
    }
    function nowTime() { return active === vr ? DUR - vr.currentTime : vf.currentTime; }

    /* ---------- geometry ---------- */
    var top0 = 0, total = 1, frac = 0;
    function measure() {
        var r = track.getBoundingClientRect();
        top0 = r.top + window.pageYOffset;
        total = Math.max(1, track.offsetHeight - stick.offsetHeight);
        frac = INTRO / (INTRO + DUR * PERVH);
    }
    function yAt(t) { return top0 + (t <= 0 ? 0 : frac + (t / DUR) * (1 - frac)) * total; }
    function tAt(y) { var s = (y - top0) / total; return s <= frac ? 0 : Math.min(DUR, (s - frac) / (1 - frac) * DUR); }
    function yEnd() { return top0 + total; }
    function chapterAt(t) { var k = 0; for (var i = 0; i < TIMES.length; i++) { if (t >= TIMES[i] - 0.05) { k = i; } } return k; }
    function cardAt(t) { var k = 0; for (var i = 0; i < CUTS.length; i++) { if (t >= CUTS[i]) { k = i; } } return k; }

    /* ---------- cards ---------- */
    var cur = 0;
    function setChapter(k) {
        var prev = cur; cur = k;
        cards.forEach(function (c, i) {
            if (i === k) { c.classList.remove('is-leaving'); c.classList.add('is-active'); }
            else if (i === prev) {
                c.classList.remove('is-active'); c.classList.add('is-leaving');
                setTimeout(function () { if (cur !== i) { c.classList.remove('is-leaving'); } }, 600);
            } else { c.classList.remove('is-active', 'is-leaving'); }
        });
        dots.forEach(function (d, i) { d.classList.toggle('is-active', i === k); d.setAttribute('aria-current', i === k ? 'true' : 'false'); });
    }
    function setPoster(t) { poster.classList.toggle('is-off', t > 0.05); }
    function progress() {
        var s = Math.min(1, Math.max(0, (window.pageYOffset - top0) / total));
        if (bar) { bar.style.transform = 'scaleX(' + s.toFixed(4) + ')'; }
        root.classList.toggle('is-scrolled', s > 0.004);
    }

    /* ---------- the move ---------- */
    var busy = false, raf = 0, ownScroll = false, lastSetY = -1;
    function ease(x) { return x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2; }

    function goTo(targetT, targetY, chapter) {
        measure();
        busy = true;
        var fromY = window.pageYOffset, fromT = nowTime();
        var seg = Math.abs(targetT - fromT);
        var D = Math.max(1400, Math.min(2100, seg * 420));
        var rate = seg > 0.05 ? Math.max(0.5, Math.min(8, seg / (D / 1000))) : 1;
        var forward = targetT > fromT + 0.03, backward = targetT < fromT - 0.03;
        var switched = false;

        // video
        var vPromise = Promise.resolve();
        var v = forward ? vf : vr;
        var endV = forward ? targetT : DUR - targetT;
        if ((forward || backward) && seg / (D / 1000) > 8) {
            // a long jump (e.g. a dot several cards away): cut straight to the target frame
            forward = backward = false;
            vPromise = seek(vf, targetT).then(function () { showVideo(vf); });
        } else if (forward || backward) {
            var startV = forward ? fromT : DUR - fromT;
            vPromise = (active === v || Math.abs(v.currentTime - startV) < 0.05 ? Promise.resolve() : seek(v, startV)).then(function () {
                showVideo(v);
                v.playbackRate = rate;
                var p; try { p = v.play(); } catch (e) {}
                if (p && p.catch) { p.catch(function () { seek(v, endV); }); }
            });
        }

        var t0 = 0;
        vPromise.then(function () {
            t0 = performance.now();
            cancelAnimationFrame(raf);
            (function step(now) {
                var p = Math.min(1, (now - t0) / D);
                ownScroll = true;
                lastSetY = Math.round(fromY + (targetY - fromY) * ease(p));
                window.scrollTo({ top: lastSetY, left: 0, behavior: 'instant' });
                progress();
                if (forward || backward) {
                    var vt = forward ? v.currentTime : DUR - v.currentTime;
                    var ck = cardAt(vt);
                    if (chapter != null) { ck = forward ? Math.min(ck, chapter) : Math.max(ck, chapter); }
                    if (ck !== cur) { setChapter(ck); }
                    if (vt > 0.15) { setPoster(1); }
                } else if (!switched && p >= 0.45) { switched = true; if (chapter != null) { setChapter(chapter); } }
                // stop the video exactly on the target frame
                if ((forward || backward) && !v.paused && v.currentTime >= endV - 0.03) { v.pause(); }
                if (p < 1) { raf = requestAnimationFrame(step); return; }
                // finish: if the video is a little behind, let it catch up, then pause
                var tEnd = performance.now();
                (function settle() {
                    if ((forward || backward) && !v.paused && v.currentTime < endV - 0.03 && performance.now() - tEnd < 1500) { raf = requestAnimationFrame(settle); return; }
                    if (forward || backward) { v.pause(); }
                    ownScroll = false;
                    busy = false;
                    lastDone = performance.now();
                    if (chapter != null && cur !== chapter) { setChapter(chapter); }
                    setPoster(targetT);
                    var other = active === vf ? vr : vf;
                    seek(other, active === vf ? DUR - targetT : targetT);
                })();
            })(t0);
        });
    }

    function stepBy(dir) {
        measure();
        var y = window.pageYOffset, t = tAt(y);
        var k = chapterAt(t);
        var onChapter = Math.abs(y - yAt(TIMES[k])) < 6;
        if (dir > 0) {
            if (y >= yEnd() - 2) { return false; }
            var n = onChapter || y < yAt(TIMES[k]) ? k + 1 : k + 1;
            if (n > LAST) { goTo(DUR - 0.05, yEnd(), null); return true; }   // leave the hero
            goTo(TIMES[n], yAt(TIMES[n]), n);
            return true;
        }
        if (y <= top0 + 2) { return false; }
        var m = y > yAt(TIMES[k]) + 6 ? k : k - 1;                          // between cards: back to the current one
        if (y >= yEnd() - 2) { m = LAST; }
        if (m < 0) { return false; }
        goTo(TIMES[m], yAt(TIMES[m]), m);
        return true;
    }
    function inZone(dir) {
        measure();
        var y = window.pageYOffset;
        return dir > 0 ? (y >= top0 - 2 && y < yEnd() - 2) : (y > top0 + 2 && y <= yEnd() + 2);
    }

    /* ---------- inputs ---------- */
    var lastWheel = 0, lastAbs = 0, lastDone = 0, lastDir = 0;
    if (!reduce) {
        window.addEventListener('wheel', function (e) {
            if (e.ctrlKey) { return; }
            var dy = e.deltaY;
            if (Math.abs(dy) < 1) { return; }
            var dir = dy > 0 ? 1 : -1;
            var now = performance.now(), gap = now - lastWheel, turned = dir !== lastDir;
            lastWheel = now; lastDir = dir;
            // one card per scroll gesture: anything during a move, or the rest of the same
            // gesture after it, is ignored. A new gesture = a short pause, or scrolling the other way.
            if (busy) { e.preventDefault(); return; }
            if (!inZone(dir)) { return; }
            e.preventDefault();
            var fresh = gap > 350 || (turned && now - lastDone > 150);
            if (!fresh) { return; }
            stepBy(dir);
        }, { passive: false });

        var ty = null, tzone = false;
        window.addEventListener('touchstart', function (e) { ty = e.touches[0].clientY; tzone = inZone(1) || inZone(-1); }, { passive: true });
        window.addEventListener('touchmove', function (e) {
            if (ty === null) { return; }
            var dir = ty - e.touches[0].clientY > 0 ? 1 : -1;
            if (busy || (tzone && inZone(dir))) { e.preventDefault(); }
        }, { passive: false });
        window.addEventListener('touchend', function (e) {
            if (ty === null) { return; }
            var dy = ty - (e.changedTouches[0] ? e.changedTouches[0].clientY : ty);
            ty = null;
            if (Math.abs(dy) < 30) { return; }
            var dir = dy > 0 ? 1 : -1;
            if (busy) { return; }
            if (inZone(dir)) { stepBy(dir); }
        }, { passive: true });

        window.addEventListener('keydown', function (e) {
            var tg = e.target && e.target.tagName;
            if (/INPUT|TEXTAREA|SELECT/.test(tg) || e.altKey || e.metaKey || e.ctrlKey) { return; }
            var dir = 0;
            if (e.key === 'ArrowDown' || e.key === 'PageDown' || (e.key === ' ' && !e.shiftKey)) { dir = 1; }
            if (e.key === 'ArrowUp' || e.key === 'PageUp' || (e.key === ' ' && e.shiftKey)) { dir = -1; }
            if (!dir) { return; }
            if (busy || e.repeat) { e.preventDefault(); return; }
            if (inZone(dir)) { e.preventDefault(); stepBy(dir); }
        });
    }

    dots.forEach(function (d, k) {
        d.addEventListener('click', function () {
            (function go() { if (busy) { setTimeout(go, 60); return; } goTo(TIMES[k], yAt(TIMES[k]), k); })();
        });
    });

    /* ---------- other scrolling (scrollbar, links, reduced motion): follow the position ---------- */
    var st = 0;
    window.addEventListener('scroll', function () {
        if (ownScroll || Math.abs(window.pageYOffset - lastSetY) < 2) { return; }
        progress();
        clearTimeout(st);
        st = setTimeout(function () {
            if (busy) { return; }
            measure();
            var t = tAt(window.pageYOffset);
            seek(vf, Math.min(t, DUR - 0.05)).then(function () { showVideo(vf); });
            var k = cardAt(t);
            if (k !== cur) { setChapter(k); }
            setPoster(t);
        }, 90);
    }, { passive: true });
    window.addEventListener('resize', function () { measure(); progress(); });

    measure();
    var t0 = tAt(window.pageYOffset);
    setChapter(chapterAt(t0));
    setPoster(t0);
    progress();
    if (t0 > 0) { seek(vf, t0); }
})();
