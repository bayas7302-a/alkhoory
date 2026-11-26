

/**
 * Kress Kleen – site polish
 * Mobile sticky header, hover fixes, typography + spacing on every page,
 * mobile/tablet footer, Who We Are + Where to buy responsive layout,
 * catalogue filters on phones (accordion inside a slide-up drawer).
 * Paste at the END of functions.php. To undo, delete this block.
 * Source files: kress-kleen-polish/kk-polish.css + kk-polish.js
 */
/* Cairo for the Arabic language label in the header */
add_action( 'wp_enqueue_scripts', 'kk_polish_fonts' );
function kk_polish_fonts() {
    wp_enqueue_style( 'kk-cairo', 'https://fonts.googleapis.com/css2?family=Cairo:wght@600;700&display=swap', array(), null );
}

/* ---------- Home hero: step scrolling with forward + reversed 720p videos (replaces the old hero script) ---------- */
add_action( 'wp', 'kk_polish_hero_swap', 99 );
function kk_polish_hero_swap() {
    remove_action( 'wp_footer', 'kk_sh_footer_script', 98 );
}
add_filter( 'do_shortcode_tag', 'kk_polish_hero_markup', 10, 2 );
function kk_polish_hero_markup( $out, $tag ) {
    if ( 'kk_scroll_hero' !== $tag ) {
        return $out;
    }
    // the hero script picks the right video size (1080p / 720p), so no file is fetched before that
    $out = preg_replace( '#\s+src="[^"]*kress-kleen-hero-scrub\.mp4"#', '', $out );
    // opening image = the first frame of the hero video
    return str_replace( 'wp-content/uploads/2026/10/homepage-banner.jpg', 'wp-content/uploads/2026/11/kk-hero-v2-first-frame.jpg', $out );
}
add_action( 'wp_footer', 'kk_polish_hero_js', 98 );
function kk_polish_hero_js() {
    if ( is_admin() || empty( $GLOBALS['kk_sh_used'] ) ) {
        return;
    }
    ?>
<script id="kk-hero-js">
(function () {
    /* Kress Kleen home hero – step scrolling.
       One scroll / swipe / key press = move to the next (or previous) card.
       Forward moves PLAY the video between the two cards; backward moves play a
       reversed copy of the video, so both directions are smooth (no seeking). */
    // 1080p for large screens, 720p for phones / small tablets (made from the 1920x1080 master)
    var UP = 'https://soharon.co.uk/off-29/wp-content/uploads/2026/11/';
    var big = Math.max(window.screen.width || 0, window.innerWidth) * Math.min(window.devicePixelRatio || 1, 2) >= 1700 && window.innerWidth >= 1025;
    var FWD = UP + (big ? 'kk-hero-v2-1080-fwd.mp4' : 'kk-hero-v2-720-fwd.mp4');
    var REV = UP + (big ? 'kk-hero-v2-1080-rev.mp4' : 'kk-hero-v2-720-rev.mp4');
    var FIRST = UP + (big ? 'kk-hero-v2-first-frame.jpg' : 'kk-hero-v2-first-frame-720.jpg');

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
    // opening image = the video's own first frame; it simply hands over to the video once that has loaded
    if (poster && poster.getAttribute('src') !== FIRST) { poster.removeAttribute('srcset'); poster.setAttribute('src', FIRST); }
    vf.setAttribute('poster', FIRST);
    function posterOff() { if (vf.readyState >= 2) { poster.classList.add('is-off'); } }
    vf.addEventListener('loadeddata', posterOff);
    posterOff();

    function unlock() {
        [vf, vr].forEach(function (v) {
            var p; try { p = v.play(); } catch (e) {}
            if (p && p.then) { p.then(function () { if (!busy) { v.pause(); } }).catch(function () {}); }
        });
    }
    ['touchstart', 'pointerdown', 'keydown', 'wheel'].forEach(function (ev) {
        window.addEventListener(ev, function once() { window.removeEventListener(ev, once); unlock(); }, { passive: true });
    });

    // frame k of the forward video is frame (N-1-k) of the reversed one: one frame offset
    var FD = 1 / 29.97;
    function rv(t) { return Math.max(0, DUR - FD - t); }

    function seek(v, t) {
        return new Promise(function (res) {
            t = Math.max(0, Math.min(DUR - 0.04, t));
            if (Math.abs(v.currentTime - t) < 0.02 && v.readyState >= 2) { res(); return; }
            var done = false;
            function ok() { if (!done) { done = true; v.removeEventListener('seeked', painted); res(); } }
            function painted() {
                // wait until the decoded frame is ready to show, so a swap never shows an old frame
                if (v.requestVideoFrameCallback) { v.requestVideoFrameCallback(function () { ok(); }); setTimeout(ok, 150); }
                else { requestAnimationFrame(function () { requestAnimationFrame(ok); }); }
            }
            v.addEventListener('seeked', painted);
            setTimeout(ok, 900);
            try { v.currentTime = t; } catch (e) { ok(); }
        });
    }
    function showVideo(v) {
        if (active === v) { return; }
        v.classList.add('is-on');
        active.classList.remove('is-on');
        active = v;
    }
    function nowTime() { return active === vr ? rv(vr.currentTime) : vf.currentTime; }

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
    function setPoster() { posterOff(); }
    function progress() {
        var s = Math.min(1, Math.max(0, (window.pageYOffset - top0) / total));
        if (bar) { bar.style.transform = 'scaleX(' + s.toFixed(4) + ')'; }
        root.classList.toggle('is-scrolled', s > 0.004);
    }

    /* ---------- the move ---------- */
    var busy = false, raf = 0, ownScroll = false, lastSetY = -1;

    function goTo(targetT, targetY, chapter) {
        measure();
        busy = true;
        var fromY = window.pageYOffset, fromT = nowTime();
        var seg = Math.abs(targetT - fromT);
        var D = Math.max(2400, Math.min(3400, seg * 750));   // slow, calm moves: 2.4–3.4 s per card
        var rate = seg > 0.05 ? Math.max(0.5, Math.min(8, seg / (D / 1000))) : 1;
        var forward = targetT > fromT + 0.03, backward = targetT < fromT - 0.03;
        var switched = false;

        // video
        var vPromise = Promise.resolve();
        var v = forward ? vf : vr;
        var endV = forward ? targetT : rv(targetT);
        if ((forward || backward) && seg / (D / 1000) > 8) {
            // a long jump (e.g. a dot several cards away): cut straight to the target frame
            forward = backward = false;
            vPromise = seek(vf, targetT).then(function () { showVideo(vf); });
        } else if (forward || backward) {
            var startV = forward ? fromT : rv(fromT);
            vPromise = (active === v || Math.abs(v.currentTime - startV) < 0.05 ? Promise.resolve() : seek(v, startV)).then(function () {
                showVideo(v);
                v.playbackRate = rate;
                var p; try { p = v.play(); } catch (e) {}
                if (p && p.catch) { p.catch(function () { seek(v, endV); }); }
            });
        }

        var t0 = 0;
        vPromise.then(function () {
            var startV = forward ? fromT : rv(fromT);
            var playing = forward || backward;
            // start the clock only once the video is really moving, so page and video set off together
            return new Promise(function (go) {
                if (!playing) { go(); return; }
                var w0 = performance.now();
                (function wait() {
                    if (v.currentTime > startV + 0.004 || performance.now() - w0 > 350) { go(); return; }
                    requestAnimationFrame(wait);
                })();
            });
        }).then(function () {
            t0 = performance.now();
            cancelAnimationFrame(raf);
            var startV = forward ? fromT : rv(fromT);
            var lastRateSet = 0;
            function place(p) {
                ownScroll = true;
                lastSetY = Math.round(fromY + (targetY - fromY) * p);
                window.scrollTo({ top: lastSetY, left: 0, behavior: 'instant' });
                progress();
            }
            function finish() {
                if (forward || backward) {
                    v.pause(); v.playbackRate = rate;
                    if (Math.abs(v.currentTime - endV) > 0.06) { seek(v, endV); }   // land exactly on the stop frame
                }
                place(1);
                ownScroll = false;
                busy = false;
                lastDone = performance.now();
                if (chapter != null && cur !== chapter) { setChapter(chapter); }
                var other = active === vf ? vr : vf;
                seek(other, active === vf ? rv(targetT) : targetT);
            }
            (function step(now) {
                // the page moves at one constant speed (no easing)
                var p = Math.min(1, (now - t0) / D);
                place(p);
                if (forward || backward) {
                    var vt = v.currentTime;
                    // keep the video locked to the page: nudge its speed by up to ±30%
                    if (now - lastRateSet > 90 && p < 1) {
                        var want = startV + (endV - startV) * p;
                        var err = want - vt;
                        v.playbackRate = Math.max(0.25, rate * (1 + Math.max(-0.3, Math.min(0.3, err * 1.5))));
                        lastRateSet = now;
                    }
                    var t = forward ? vt : rv(vt);
                    var ck = cardAt(t);
                    if (chapter != null) { ck = forward ? Math.min(ck, chapter) : Math.max(ck, chapter); }
                    if (ck !== cur) { setChapter(ck); }
                    if (vt >= endV - 0.03) { v.pause(); }
                    // page arrived: wait (briefly) for the last video frames, then stop exactly on the target frame
                    if (p >= 1 && !v.paused && vt < endV - 0.03 && now - t0 < D + 900) { raf = requestAnimationFrame(step); return; }
                } else if (!switched && p >= 0.45) { switched = true; if (chapter != null) { setChapter(chapter); } }
                if (p < 1) { raf = requestAnimationFrame(step); return; }
                finish();
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
            // coming back up from the next section (even mid-swipe): take over right away
            if (dir < 0 && window.pageYOffset > yAt(TIMES[LAST]) + 6) { enterFromBelow(); return; }
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
    var st = 0, prevY = window.pageYOffset;
    function enterFromBelow() {
        // scrolling up from the next section: stop the momentum at the end of the hero
        // and play back to the last card straight away (no frozen frame, no jump)
        clearTimeout(st);
        ownScroll = true;
        lastSetY = Math.round(yEnd());
        window.scrollTo({ top: lastSetY, left: 0, behavior: 'instant' });
        prevY = lastSetY;
        lastDone = 0;
        goTo(TIMES[LAST], yAt(TIMES[LAST]), LAST);
    }
    window.addEventListener('scroll', function () {
        var y = window.pageYOffset, py = prevY;
        prevY = y;
        if (ownScroll || busy || Math.abs(y - lastSetY) < 2) { return; }
        measure();
        if (!reduce && py >= yEnd() - 2 && y < yEnd() - 2 && y > top0 + 2) { enterFromBelow(); return; }
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
</script>
    <?php
}

add_action( 'wp_head', 'kk_polish_css', 999 );
function kk_polish_css() {
    if ( is_admin() ) {
        return;
    }
    ?>
<style id="kk-polish-css">
/* =====================================================================
   Kress Kleen – site polish (responsive, typography, spacing, header,
   footer, hovers, Who We Are, catalogue filters)
   Breakpoints follow Elementor: tablet <= 1024px, mobile <= 767px.
   ===================================================================== */

:root {
  --kp-green: #00784c;
  --kp-green-dark: #00613d;
  --kp-orange: #f05a38;
  --kp-gold: #f0b232;
  --kp-ink: #111111;
  --kp-mint: #e4f3ec;
  --kp-ease: cubic-bezier(0.16, 1, 0.3, 1);
  --kp-gutter: 20px;            /* side padding on mobile */
  --kp-sec: 64px;               /* vertical section padding on mobile */
}

/* ---------- 0. no sideways scrolling anywhere ---------- */
body { overflow-x: clip; }
body .elementor-widget-image { overflow: hidden; }
body .elementor-widget-image img { max-width: 100% !important; }
body .wpcf7 form.wpcf7-form { position: relative; }
body .wpcf7-spinner { position: absolute !important; right: 14px; bottom: 14px; left: auto; margin: 0 !important; }
body .elementor-heading-title { overflow-wrap: break-word; }

/* =====================================================================
   1. HOME HERO (scroll video) – smaller banner text
   ===================================================================== */
body .kk-sh .kk-sh__card {
  width: min(540px, 100%);
  padding: 28px 30px 30px;
}
body .kk-sh .kk-sh__card--center { margin-left: calc(min(540px, 100%) / -2); }
body .kk-sh .kk-sh__eyebrow { font-size: 16px; letter-spacing: 0.08em; }
body .kk-sh h2.kk-sh__title { margin: 6px 0 10px; font-size: clamp(24px, 2.1vw, 32px); line-height: 1.1; }
body .kk-sh .kk-sh__text { margin-bottom: 20px; font-size: 16px; }
body .kk-sh a.kk-sh__btn { padding: 11px 24px; font-size: 13px; letter-spacing: 0.06em; }

@media (max-width: 767px) {
  body .kk-sh .kk-sh__card { padding: 18px 18px 20px; border-radius: 14px; bottom: 4vh !important; }
  body .kk-sh .kk-sh__eyebrow { font-size: 12px; }
  body .kk-sh h2.kk-sh__title { margin: 4px 0 6px; font-size: 19px; }
  body .kk-sh .kk-sh__text { margin-bottom: 14px; font-size: 14px; }
  body .kk-sh a.kk-sh__btn { padding: 9px 18px; font-size: 12px; }
}

/* =====================================================================
   2. PAGE BANNERS (Who We Are, Where to buy, Catalogue) – smaller titles
   ===================================================================== */
body .elementor-153 .elementor-element.elementor-element-901a2db .elementor-heading-title,
body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
body .kk-hero .kk-hero__title {
  font-size: clamp(40px, 4.2vw, 60px) !important;
  line-height: 1.05;
}
@media (max-width: 1024px) {
  body .elementor-153 .elementor-element.elementor-element-901a2db .elementor-heading-title,
  body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
  body .kk-hero .kk-hero__title { font-size: 40px !important; }
}
@media (max-width: 767px) {
  body .elementor-153 .elementor-element.elementor-element-901a2db .elementor-heading-title,
  body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
body .elementor-132 .elementor-element.elementor-element-4cd68f2 .elementor-heading-title,
  body .kk-hero .kk-hero__title { font-size: 32px !important; }
  body .elementor-153 .elementor-element.elementor-element-cc1a00a { --min-height: 0px; min-height: 0; --padding-top: 72px; padding-top: 72px !important; --padding-bottom: 40px; padding-bottom: 40px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; }
  body .elementor-153 .elementor-element.elementor-element-cc1a00a > .e-con-inner { padding: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-90a5b6c { margin-bottom: 8px; }
  body :is(.elementor-153 .elementor-element-90a5b6c, .elementor-132 .elementor-element-b0e94d1) :is(ul, ol, .hfe-breadcrumbs) { padding-left: 0 !important; margin-left: 0 !important; }
}

/* =====================================================================
   3. MOBILE HEADER – one clean row, sticky on phones only
   ===================================================================== */
@media (max-width: 767px) {
  body #masthead,
  body .ehf-header #masthead {
    position: -webkit-sticky !important;
    position: sticky !important;
    top: -28px;                    /* the "Kleening since 2003" bar scrolls away, the main bar stays */
    z-index: 1000 !important;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }
  /* slim marquee */
  body #masthead .elementor-element.elementor-element-56435bc { --min-height: 28px; min-height: 28px; --padding-top: 6px; padding-top: 6px !important; --padding-bottom: 6px; padding-bottom: 6px !important; }
  body #masthead .elementor-element.elementor-element-8750345 { font-size: 10px; }

  /* main bar: menu | logo | where to buy */
  body #masthead .elementor-element.elementor-element-4a693fd {
    --min-height: 64px; min-height: 64px;
    --padding-top: 8px; padding-top: 8px !important; --padding-bottom: 8px; padding-bottom: 8px !important;
    --padding-left: 16px; padding-left: 16px !important; --padding-right: 16px; padding-right: 16px !important;
    --flex-wrap: nowrap; flex-wrap: nowrap;
    background-color: transparent;
  }
  body #masthead .elementor-element.elementor-element-4a693fd > .e-con-inner {
    display: grid !important;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 8px;
    padding: 0 !important;
  }
  /* menu + language (left) */
  body #masthead .elementor-element.elementor-element-f760a15 {
    grid-column: 1; grid-row: 1;
    --width: auto; width: auto !important; max-width: none;
    --flex-wrap: nowrap; flex-wrap: nowrap;
    --gap: 10px; gap: 10px;
    --justify-content: flex-start; justify-content: flex-start;
    padding: 0 !important; margin: 0 !important;
    order: 0 !important;
  }
  body #masthead .elementor-element.elementor-element-98f606d .elementor-icon { font-size: 24px; }
  body #masthead .elementor-element.elementor-element-98f606d .elementor-icon svg { height: 22px; width: auto; }
  body #masthead .elementor-element.elementor-element-98f606d .elementor-icon-wrapper { line-height: 0; }
  body #masthead .elementor-element.elementor-element-e8b8918 { line-height: 1; }
  body #masthead .elementor-element.elementor-element-e8b8918 .gtranslate_wrapper { gap: 10px !important; }
  body #masthead .elementor-element.elementor-element-e8b8918 a.glink::after,
  body #masthead .elementor-element.elementor-element-e8b8918 a[data-gt-lang]::after { font-size: 12px !important; }
  /* logo (centre) */
  body #masthead .elementor-element.elementor-element-ae1ae2e {
    grid-column: 2; grid-row: 1;
    width: auto !important; max-width: none; --container-widget-width: auto;
    margin: 0 !important;
  }
  body #masthead .elementor-element.elementor-element-ae1ae2e img { width: auto !important; height: 46px; max-width: 120px; object-fit: contain; }
  /* where to buy (right) */
  body #masthead .elementor-element.elementor-element-0a2ca92 {
    grid-column: 3; grid-row: 1;
    justify-self: end;
    width: auto !important; max-width: none; --container-widget-width: auto;
    margin: 0 !important;
  }
  body #masthead .elementor-element.elementor-element-0a2ca92 .elementor-button {
    padding: 9px 12px;
    font-size: 11px;
    line-height: 1;
    letter-spacing: 0.04em;
    border-radius: 999px;
    white-space: nowrap;
  }

  /* stuck state: solid brand bar with a soft shadow */
  body #masthead.kp-stuck { background-color: rgba(0, 97, 61, 0.96); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.18); }
  @supports (backdrop-filter: blur(10px)) {
    body #masthead.kp-stuck { background-color: rgba(0, 97, 61, 0.88); -webkit-backdrop-filter: blur(12px) saturate(140%); backdrop-filter: blur(12px) saturate(140%); }
  }

  /* green strips that sit behind the (shorter) header */
  :root { --kk-hdr-h: 92px; }

  /* the side menu must open above the sticky header */
  body .kk-side-overlay { z-index: 1090; }
  body .kk-side { z-index: 1100; }
}

/* =====================================================================
   4. HOME – "Let's find your cleaning solution" cards
   ===================================================================== */
/* EXPLORE is a text link: no shine box, no lift, no shadow – arrow slides and underline draws */
body .elementor-43 .kkc-card .elementor-widget-button .elementor-button {
  overflow: visible;
  box-shadow: none !important;
  transform: none !important;
  background: transparent !important;
}
body .elementor-43 .kkc-card .elementor-widget-button .elementor-button::after {
  content: "";
  left: 0 !important;
  top: auto;
  bottom: -4px;
  width: 100%;
  height: 1.5px;
  background: currentColor;
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.45s var(--kp-ease) !important;
}
body .elementor-43 .kkc-card { transition: box-shadow 0.4s ease, transform 0.4s var(--kp-ease), border-color 0.3s ease; }
body .elementor-43 .kkc-card .elementor-widget-image img { transition: transform 0.9s var(--kp-ease); }
@media (hover: hover) {
  body .elementor-43 .kkc-card:hover { box-shadow: 0 18px 40px rgba(0, 0, 0, 0.10); }
  body .elementor-43 .kkc-card:hover .elementor-widget-image img { transform: scale(1.05); }
  body .elementor-43 .kkc-card .elementor-widget-button .elementor-button:hover::after,
  body .elementor-43 .kkc-card:hover .elementor-widget-button .elementor-button::after { transform: scaleX(1); }
  body .elementor-43 .kkc-card:hover .elementor-widget-button .elementor-button-icon { transform: translateX(5px); }
}

/* =====================================================================
   5. HOME – "One home. Many cleaning challenges" area buttons
   ===================================================================== */
body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) {
  border: 0 !important;
  border-radius: 999px !important;
  width: 100% !important;
  max-width: 300px !important;
  margin: 0 !important;
}
body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  min-height: 48px;
  padding: 0 20px 0 24px;
  border: 1px solid #1a1a1a !important;
  border-radius: 999px !important;
  background: transparent !important;
  color: var(--kp-orange) !important;
  font-size: 15px !important;
  line-height: 1;
  letter-spacing: 0.06em;
  box-shadow: none !important;
  transform: none !important;
  overflow: hidden;
  transition: background-color 0.35s ease, color 0.35s ease, border-color 0.35s ease, padding 0.4s var(--kp-ease) !important;
}
body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button::after {
  /* small arrow on the right, replaces the shine */
  content: "\2192";
  position: static;
  width: auto; height: auto;
  background: none;
  transform: translateX(-6px);
  opacity: 0;
  font-size: 16px;
  transition: transform 0.4s var(--kp-ease), opacity 0.3s ease !important;
}
body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button-content-wrapper { line-height: 1; }
@media (hover: hover) {
  body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button:hover {
    background: var(--kp-orange) !important;
    border-color: var(--kp-orange) !important;
    color: #ffffff !important;
  }
  body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button:hover::after { transform: none; opacity: 1; }
}
body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button:focus-visible {
  outline: 3px solid var(--kp-gold);
  outline-offset: 3px;
}
body .elementor-43 .elementor-element.elementor-element-743fae7 { --gap: 12px; gap: 12px; }
body .elementor-43 .elementor-element.elementor-element-08f48e7 { margin-bottom: 14px !important; }

/* =====================================================================
   6. HOME – typography + spacing on tablet / mobile
   ===================================================================== */
@media (max-width: 1024px) {
  body .elementor-43 .kkc-card .elementor-widget-heading:first-of-type .elementor-heading-title { font-size: 24px; }
}
@media (max-width: 767px) {
  /* section rhythm */
  body .elementor-43 :is(.elementor-element.elementor-element-b12f160, .elementor-element.elementor-element-975d254, .elementor-element.elementor-element-7149c5d, .elementor-element.elementor-element-1c24237) {
    --padding-top: var(--kp-sec); padding-top: var(--kp-sec) !important; --padding-bottom: var(--kp-sec); padding-bottom: var(--kp-sec) !important;
    --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important;
  }
  body .elementor-43 :is(.elementor-element.elementor-element-ad39a46, .elementor-element.elementor-element-755069b, .elementor-element.elementor-element-743fae7, .elementor-element.elementor-element-5448b30) {
    --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important;
  }

  /* section headings */
  body .elementor-43 :is(.elementor-element.elementor-element-9a43ca3, .elementor-element.elementor-element-cb4ed5d, .elementor-element.elementor-element-884f790, .elementor-element.elementor-element-2799513) .elementor-heading-title {
    font-size: 24px !important;
    line-height: 1.2 !important;
  }
  body .elementor-43 :is(.elementor-element.elementor-element-1bde407, .elementor-element.elementor-element-08f48e7, .elementor-element.elementor-element-9d92933) .elementor-heading-title {
    font-size: 15px !important;
    line-height: 1.55 !important;
  }
  body .elementor-43 .elementor-element.elementor-element-cb4ed5d { margin-bottom: 8px; }

  /* SMART / FUNCTIONAL / RELIABLE */
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-title { font-size: 20px !important; margin-bottom: 6px; }
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-description { font-size: 15px !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon { font-size: 40px; }
  body .elementor-43 .elementor-element.elementor-element-755069b { --gap: 28px; gap: 28px; margin-top: 12px; }

  /* solution cards: 2 per row */
  body .elementor-43 .elementor-element.elementor-element-79b14eb {
    --flex-direction: row; flex-direction: row;
    --flex-wrap: wrap; flex-wrap: wrap;
    --gap: 12px; gap: 12px;
    --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important;
  }
  body .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card {
    --width: calc(50% - 6px); width: calc(50% - 6px) !important;
    --padding-bottom: 16px; padding-bottom: 16px !important;
    border-radius: 10px; overflow: hidden;
  }
  body .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card:last-child:nth-child(odd) { --width: 100%; width: 100% !important; }
  body .elementor-43 .kkc-card .elementor-widget-image img { height: 130px !important; width: 100% !important; object-fit: cover; }
  body .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card:last-child:nth-child(odd) .elementor-widget-image img { height: 170px !important; }
  body .elementor-43 .kkc-card .elementor-widget-icon { margin: -22px 0 0 14px !important; }
  body .elementor-43 .kkc-card .elementor-widget-icon .elementor-icon { font-size: 34px; }
  body .elementor-43 .kkc-card .elementor-widget-icon svg { width: 34px; height: 34px; }
  body .elementor-43 .kkc-card .elementor-widget-heading { margin: 0 14px !important; }
  body .elementor-43 .kkc-card .elementor-widget-heading:nth-of-type(1) { margin-top: 10px !important; }
  body .elementor-43 .kkc-card :is(.elementor-element.elementor-element-b21629b, .elementor-element.elementor-element-665f377, .elementor-element.elementor-element-252f948, .elementor-element.elementor-element-f8975af, .elementor-element.elementor-element-bcefc1b) .elementor-heading-title { font-size: 20px !important; line-height: 1.1; }
  body .elementor-43 .kkc-card :is(.elementor-element.elementor-element-ed3bfa7, .elementor-element.elementor-element-21ba642, .elementor-element.elementor-element-07011f8, .elementor-element.elementor-element-ccba669, .elementor-element.elementor-element-ce02014) { margin-top: 6px !important; }
  body .elementor-43 .kkc-card :is(.elementor-element.elementor-element-ed3bfa7, .elementor-element.elementor-element-21ba642, .elementor-element.elementor-element-07011f8, .elementor-element.elementor-element-ccba669, .elementor-element.elementor-element-ce02014) .elementor-heading-title { font-size: 13px !important; line-height: 1.45; }
  body .elementor-43 .kkc-card .elementor-widget-button { padding: 0 14px !important; margin-top: 10px !important; }
  body .elementor-43 .kkc-card .elementor-widget-button .elementor-button { font-size: 12px !important; }

  /* one home */
  body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) { max-width: 100% !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-a682427, .elementor-element.elementor-element-3a87742, .elementor-element.elementor-element-e648c86, .elementor-element.elementor-element-0f74986) .elementor-button { min-height: 46px; font-size: 14px !important; }
  body .elementor-43 .elementor-element.elementor-element-38068a6 { margin-top: 24px; }

  /* featured product */
  body .elementor-43 .elementor-element.elementor-element-91b1f1f { --padding-top: 40px; padding-top: 40px !important; --padding-bottom: 32px; padding-bottom: 32px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; }
  body .elementor-43 .elementor-element.elementor-element-36490c5 .elementor-heading-title { font-size: 24px !important; line-height: 1.15 !important; }
  body .elementor-43 .elementor-element.elementor-element-0818b88 .elementor-heading-title { font-size: 16px !important; }
  body .elementor-43 .elementor-element.elementor-element-51cf488 .elementor-icon-list-text { font-size: 14px !important; }
  body .elementor-43 .elementor-element.elementor-element-d688fd8 .elementor-heading-title { font-size: 12px !important; letter-spacing: 0.1em; }
  body .elementor-43 .elementor-element.elementor-element-d7da6a9 img { height: 260px !important; object-fit: cover; }
  body .elementor-43 .elementor-element.elementor-element-9b21423 img { max-height: 340px; width: auto !important; margin: 0 auto; display: block; }

  /* buttons in content */
  body .elementor-43 :is(.elementor-element.elementor-element-c03da2d, .elementor-element.elementor-element-041e5ca) .elementor-button { font-size: 13px !important; padding: 12px 22px !important; }
  body .elementor-43 .elementor-element.elementor-element-7fb41c0 img { max-height: 320px; object-fit: contain; }
}

/* ---------- HOME – tablet (768–1024px) ---------- */
@media (min-width: 768px) and (max-width: 1024px) {
  body .elementor-43 :is(.elementor-element.elementor-element-b12f160, .elementor-element.elementor-element-975d254, .elementor-element.elementor-element-7149c5d, .elementor-element.elementor-element-1c24237) {
    --padding-top: 64px; padding-top: 64px !important; --padding-bottom: 64px; padding-bottom: 64px !important; --padding-left: 32px; padding-left: 32px !important; --padding-right: 32px; padding-right: 32px !important;
  }
  /* designing a better way: text on top, the three values in one row below */
  body .elementor-43 .elementor-element.elementor-element-b12f160 { --flex-direction: column; flex-direction: column !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-ad39a46, .elementor-element.elementor-element-755069b) { --width: 100%; width: 100% !important; --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important; }
  body .elementor-43 .elementor-element.elementor-element-755069b { --flex-direction: row; flex-direction: row !important; --flex-wrap: nowrap; --gap: 20px; margin-top: 24px; }
  body .elementor-43 .elementor-element.elementor-element-755069b > .elementor-widget { flex: 1 1 0; width: auto !important; max-width: none !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-title { font-size: 20px !important; }
  body .elementor-43 :is(.elementor-element.elementor-element-85d3640, .elementor-element.elementor-element-63b71db, .elementor-element.elementor-element-704d045) .elementor-icon-box-description { font-size: 15px !important; }
  body .elementor-43 .elementor-element.elementor-element-9a43ca3 .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-cb4ed5d .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-884f790 .elementor-heading-title,
  body .elementor-43 .elementor-element.elementor-element-2799513 .elementor-heading-title { font-size: 28px !important; line-height: 1.2 !important; }

  /* solution cards: 3 + 2 */
  body .elementor-43 .elementor-element.elementor-element-79b14eb { --flex-wrap: wrap; flex-wrap: wrap !important; --justify-content: center; justify-content: center !important; --gap: 14px; gap: 14px !important; --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important; }
  body .elementor-43 .elementor-element.elementor-element-79b14eb > .kkc-card { --width: calc(33.333% - 10px); width: calc(33.333% - 10px) !important; }
  body .elementor-43 .kkc-card .elementor-widget-image img { height: 170px !important; width: 100% !important; object-fit: cover; }

  /* one home: text on top, the map full width */
  body .elementor-43 .elementor-element.elementor-element-7149c5d { --flex-direction: column; flex-direction: column !important; }
  body .elementor-43 .elementor-element.elementor-element-743fae7 { --width: 100%; width: 100% !important; --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important; }
  body .elementor-43 .elementor-element.elementor-element-38068a6 { width: 100% !important; max-width: 100% !important; margin-top: 24px; }

  /* featured product: photo on top */
  body .elementor-43 .elementor-element.elementor-element-8e91e85 { --flex-direction: column; flex-direction: column !important; }
  body .elementor-43 .elementor-element.elementor-element-d7da6a9 { width: 100% !important; max-width: 100% !important; }
  body .elementor-43 .elementor-element.elementor-element-d7da6a9 img { width: 100% !important; height: 340px !important; object-fit: cover; }
  body .elementor-43 .elementor-element.elementor-element-1676003 { --width: 100%; width: 100% !important; }
  body .elementor-43 .elementor-element.elementor-element-36490c5 .elementor-heading-title { font-size: 30px !important; }
}

/* =====================================================================
   7. FOOTER – tidy on tablet / mobile
   ===================================================================== */
@media (max-width: 1024px) {
  body .elementor-30 .elementor-element.elementor-element-ab6f316 {
    --padding-top: 48px; padding-top: 48px !important; --padding-bottom: 28px; padding-bottom: 28px !important;
    --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important;
  }
  body .elementor-30 .elementor-element.elementor-element-ab6f316,
  body .elementor-30 .elementor-element.elementor-element-ab6f316 > .e-con-inner {
    --flex-direction: column; flex-direction: column !important;
    --align-items: center; align-items: center !important;
    --gap: 26px; gap: 26px !important;
    text-align: center;
  }
  body .elementor-30 .elementor-element.elementor-element-9f80d62 { width: auto !important; max-width: none !important; margin: 0 !important; }
  body .elementor-30 .elementor-element.elementor-element-9f80d62 img { width: 150px !important; height: auto; }
  body .elementor-30 .elementor-element.elementor-element-ce94520 {
    --width: 100%; width: 100% !important;
    --align-items: center; align-items: center !important;
    --gap: 12px; gap: 12px !important;
    padding: 0 !important;
  }
  body .elementor-30 .elementor-element.elementor-element-0302daf .elementor-heading-title { font-size: 12px !important; letter-spacing: 0.14em; }
  body .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-items { justify-content: center !important; row-gap: 8px; }
  body .elementor-30 .elementor-element.elementor-element-501b3e5 .elementor-icon-list-text { font-size: 15px !important; }
  body .elementor-30 .elementor-element.elementor-element-6e495df {
    --width: 100%; width: 100% !important; max-width: 420px;
    display: grid !important;
    grid-template-columns: 1fr;
    gap: 10px !important;
    padding: 0 !important;
  }
  body .elementor-30 .elementor-element.elementor-element-6e495df > .elementor-widget { width: 100% !important; max-width: none !important; margin: 0 !important; }
  body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button {
    display: flex; width: 100%;
    justify-content: center;
    min-height: 46px;
    padding: 0 14px !important;
    font-size: 14px !important;
    border-radius: 10px !important;
    align-items: center;
  }
  body .elementor-30 .elementor-element.elementor-element-4ba5bf5 {
    grid-column: 1 / -1;
    margin-top: 14px !important;
    padding-top: 18px;
    border-top: 1px solid rgba(255, 255, 255, 0.22);
  }
  body .elementor-30 .elementor-element.elementor-element-4ba5bf5 .elementor-heading-title { font-size: 12px !important; line-height: 1.6; opacity: 0.9; text-align: center; }
}

/* =====================================================================
   8. WHO WE ARE (page 153) – desktop scale + full mobile layout
   ===================================================================== */
body .elementor-153 .elementor-element.elementor-element-fb87731 .elementor-heading-title { font-size: clamp(56px, 6.4vw, 92px) !important; line-height: 1.05; }
body .elementor-153 .elementor-element.elementor-element-092cb7a .elementor-heading-title { font-size: clamp(72px, 9vw, 140px) !important; line-height: 1.1; }

@media (max-width: 1024px) {
  body .elementor-153 :is(.elementor-element.elementor-element-5d0b5e3, .elementor-element.elementor-element-b86e4b4) .elementor-heading-title { font-size: 40px !important; }
  body .elementor-153 :is(.elementor-element.elementor-element-89c5073, .elementor-element.elementor-element-162ec16) .elementor-heading-title { font-size: 28px !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 { --flex-wrap: wrap; flex-wrap: wrap; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner { display: flex !important; flex-wrap: wrap !important; gap: 24px 16px !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con,
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner > .e-con { --width: calc(50% - 8px); width: calc(50% - 8px) !important; }
}

@media (max-width: 767px) {
  /* intro */
  body .elementor-153 .elementor-element.elementor-element-79a8b7a { --padding-top: 48px; padding-top: 48px !important; --padding-bottom: 24px; padding-bottom: 24px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; --gap: 28px; }
  body .elementor-153 .elementor-element.elementor-element-1a21684 { --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important; --padding-top: 0px; padding-top: 0px !important; --gap: 10px; }
  body .elementor-153 :is(.elementor-element.elementor-element-5d0b5e3, .elementor-element.elementor-element-b86e4b4) .elementor-heading-title { font-size: 30px !important; line-height: 1.15 !important; }
  body .elementor-153 .elementor-element.elementor-element-71918f6 { margin: 10px 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-71918f6 img { height: 56px !important; width: auto !important; }
  body .elementor-153 :is(.elementor-element.elementor-element-89c5073, .elementor-element.elementor-element-162ec16) .elementor-heading-title { font-size: 22px !important; line-height: 1.25 !important; }
  body .elementor-153 .elementor-element.elementor-element-7ed0220 { margin-top: 8px !important; }
  body .elementor-153 .elementor-element.elementor-element-7ed0220 .elementor-heading-title { font-size: 15px !important; line-height: 1.6 !important; }
  body .elementor-153 .elementor-element.elementor-element-4c01e73 { width: 100% !important; max-width: 100% !important; --container-widget-width: 100%; text-align: center; margin: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-4c01e73 img { width: auto !important; max-width: 78% !important; height: auto !important; max-height: 380px; margin: 0 auto; display: block; object-fit: contain; }

  /* material / durability / ergonomics / workmanship: 2 x 2 grid */
  body .elementor-153 .elementor-element.elementor-element-bbc3710 {
    --flex-direction: row; flex-direction: row;
    --flex-wrap: wrap; flex-wrap: wrap;
    --gap: 16px 12px; gap: 22px 12px;
    --padding-top: 24px; padding-top: 24px !important; --padding-bottom: 56px; padding-bottom: 56px !important;
    --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important;
  }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    gap: 22px 12px !important;
    padding: 0 !important;
  }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con,
  body .elementor-153 .elementor-element.elementor-element-bbc3710 > .e-con-inner > .e-con {
    --width: calc(50% - 6px); width: calc(50% - 6px) !important;
    --padding-top: 0px; padding-top: 0px !important; --padding-left: 0px; padding-left: 0px !important; --padding-right: 0px; padding-right: 0px !important; --padding-bottom: 0px; padding-bottom: 0px !important;
    --gap: 10px;
  }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-image img { height: 140px !important; width: 100% !important; object-fit: cover; border-radius: 12px; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-heading .elementor-heading-title { font-size: 16px !important; }
  body .elementor-153 .elementor-element.elementor-element-bbc3710 .elementor-widget-divider { --divider-width: 36px; margin: 0 !important; }

  /* dark statement boxes */
  body .elementor-153 :is(.elementor-element.elementor-element-1753931, .elementor-element.elementor-element-915d10f) {
    --padding-top: 48px; padding-top: 48px !important; --padding-bottom: 48px; padding-bottom: 48px !important;
    --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important;
    --gap: 22px; --min-height: 0px;
  }
  body .elementor-153 :is(.elementor-element.elementor-element-1b9f740, .elementor-element.elementor-element-3686e69, .elementor-element.elementor-element-f5a2f5e, .elementor-element.elementor-element-e6c8adb) img { height: 18px !important; width: auto !important; }
  body .elementor-153 :is(.elementor-element.elementor-element-f17afcf, .elementor-element.elementor-element-e5f7c30) { margin: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-f17afcf .elementor-heading-title { font-size: 18px !important; line-height: 1.6 !important; }
  body .elementor-153 .elementor-element.elementor-element-e5f7c30 .elementor-heading-title { font-size: 22px !important; line-height: 1.45 !important; }

  /* no hype */
  body .elementor-153 .elementor-element.elementor-element-5419111 { --padding-top: 56px; padding-top: 56px !important; --padding-bottom: 56px; padding-bottom: 56px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; --gap: 16px; --min-height: 0px; }
  body .elementor-153 .elementor-element.elementor-element-83cce31 .elementor-heading-title { font-size: 14px !important; line-height: 1.6 !important; }
  body .elementor-153 .elementor-element.elementor-element-fb87731 .elementor-heading-title { font-size: 31px !important; line-height: 1.15 !important; }
  body .elementor-153 .elementor-element.elementor-element-f6db65f { margin: 0 !important; }
  body .elementor-153 .elementor-element.elementor-element-f6db65f .elementor-heading-title { font-size: 16px !important; line-height: 1.6 !important; }

  /* guaranteed to shine */
  body .elementor-153 .elementor-element.elementor-element-bcabc46 { --padding-top: 48px; padding-top: 48px !important; --padding-bottom: 48px; padding-bottom: 48px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; --min-height: 0px; }
  body .elementor-153 .elementor-element.elementor-element-092cb7a .elementor-heading-title { font-size: 54px !important; line-height: 1.2 !important; }
}

/* =====================================================================
   8b. WHERE TO BUY (page 132)
   ===================================================================== */
@media (max-width: 767px) {
  body .elementor-132 .elementor-element.elementor-element-39346a4 { --min-height: 0px; min-height: 0; --padding-top: 72px; padding-top: 72px !important; --padding-bottom: 40px; padding-bottom: 40px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; }
  body .elementor-132 .elementor-element.elementor-element-39346a4 > .e-con-inner { padding: 0 !important; }
  body .elementor-132 .elementor-element.elementor-element-609ea42 { --padding-top: 48px; padding-top: 48px !important; --padding-bottom: 48px; padding-bottom: 48px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; }
  body .elementor-132 .elementor-element.elementor-element-506a975 .elementor-heading-title { font-size: 24px !important; line-height: 1.2 !important; }
  body .elementor-132 .elementor-element.elementor-element-9039a56 { --padding-top: 56px; padding-top: 56px !important; --padding-bottom: 56px; padding-bottom: 56px !important; --padding-left: var(--kp-gutter); padding-left: var(--kp-gutter) !important; --padding-right: var(--kp-gutter); padding-right: var(--kp-gutter) !important; }
  body .elementor-132 .elementor-element.elementor-element-142ce73 .elementor-heading-title { font-size: 12px !important; letter-spacing: 0.1em; }
  body .elementor-132 .elementor-element.elementor-element-846c896 { margin-left: 0 !important; margin-right: 0 !important; }
  body .elementor-132 .elementor-element.elementor-element-846c896 .elementor-heading-title { font-size: 21px !important; line-height: 1.3 !important; }
  body .elementor-132 .elementor-element.elementor-element-e1a9a94 .elementor-heading-title { font-size: 15px !important; line-height: 1.65 !important; }
}

/* =====================================================================
   9. CATALOGUE – banner, filters (phones: accordion in a slide-up drawer), grid
   ===================================================================== */
/* accordion: phones only – tablet / desktop keep the original sidebar */
@media (max-width: 767px) {
body .kk-cat__group { margin-bottom: 0 !important; border-bottom: 1px solid rgba(0, 97, 61, 0.14); }
body .kk-cat__group:last-child { border-bottom: 0; }
body .kk-cat__side .kp-acc__btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  margin: 0;
  padding: 18px 0;
  border: 0;
  background: none;
  color: var(--kp-orange);
  font: inherit;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  text-align: left;
  cursor: pointer;
  box-shadow: none;
}
body .kk-cat__side .kp-acc__btn:hover,
body .kk-cat__side .kp-acc__btn:focus { background: none; color: var(--kp-orange); }
body .kk-cat__side .kp-acc__btn:focus-visible { outline: 2px solid var(--kp-gold); outline-offset: 4px; }
body .kk-cat__side .kp-acc__count {
  margin-left: 8px;
  padding: 2px 8px;
  border-radius: 99px;
  background: rgba(0, 97, 61, 0.1);
  color: var(--kp-green-dark);
  font-size: 11px;
  letter-spacing: 0;
}
body .kk-cat__side .kp-acc__icon {
  position: relative;
  flex: 0 0 auto;
  width: 12px; height: 12px;
  margin-left: auto;
}
body .kk-cat__side .kp-acc__icon::before,
body .kk-cat__side .kp-acc__icon::after {
  content: "";
  position: absolute;
  left: 0; top: 50%;
  width: 12px; height: 1.6px;
  margin-top: -0.8px;
  background: var(--kp-green-dark);
  transition: transform 0.35s var(--kp-ease);
}
body .kk-cat__side .kp-acc__icon::after { transform: rotate(90deg); }
body .kk-cat__side .kp-acc.is-open .kp-acc__icon::after { transform: rotate(0deg); }
body .kk-cat__side .kp-acc__panel {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 0.4s var(--kp-ease);
}
body .kk-cat__side .kp-acc.is-open .kp-acc__panel { grid-template-rows: 1fr; }
body .kk-cat__side .kp-acc__panel > .kk-cat__list { min-height: 0; overflow: hidden; margin: 0 !important; }
body .kk-cat__side .kp-acc.is-open .kp-acc__panel > .kk-cat__list { padding-bottom: 16px; }
body .kk-cat__side .kk-cat__link { transition: color 0.2s ease, padding 0.3s var(--kp-ease); }
body .kk-cat__side .kk-cat__link.is-active { color: var(--kp-orange) !important; }
@media (hover: hover) {
  body .kk-cat__side .kk-cat__link:hover { padding-left: 4px; }
}
}

/* filter bar + drawer (phones only) */
body .kp-filterbar { display: none; }
body .kp-drawer__head, body .kp-drawer__foot { display: none; }

@media (max-width: 1024px) {
  body .kk-hero { padding-top: 56px !important; padding-bottom: 40px !important; }
  body .kk-hero .kk-hero__crumbs { font-size: 11px; margin-bottom: 8px; }
}

@media (max-width: 767px) {

  body .kp-filterbar {
    position: sticky;
    top: 68px;                     /* below the sticky mobile header */
    z-index: 30;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 20px;
    padding: 12px 0;
    background: #ffffff;
  }
  body .kp-filterbar__btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 18px;
    border: 1px solid var(--kp-green-dark);
    border-radius: 999px;
    background: #ffffff;
    color: var(--kp-green-dark);
    font: inherit;
    font-size: 14px;
    font-weight: 600;
    line-height: 1;
    cursor: pointer;
  }
  body .kp-filterbar__btn svg { width: 16px; height: 16px; }
  body .kp-filterbar__btn:hover, body .kp-filterbar__btn:focus { background: var(--kp-green-dark); color: #ffffff; }
  body .kp-filterbar__chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
    padding: 8px 12px;
    border-radius: 999px;
    background: var(--kp-mint);
    color: var(--kp-green-dark);
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-decoration: none;
  }
  body .kp-filterbar__chip b { font-weight: 600; }

  /* the sidebar becomes a slide-up drawer (moved to <body> by the script) */
  body .kk-cat { display: block !important; }
  body .kk-cat__side.kp-drawer {
    position: fixed !important;
    left: 0; right: 0; bottom: 0; top: auto;
    z-index: 1200;
    width: 100% !important;
    max-width: none !important;
    max-height: 86vh;
    max-height: 86dvh;
    margin: 0 !important;
    padding: 0 !important;
    display: flex !important;
    flex-direction: column;
    border-radius: 20px 20px 0 0;
    background: #ffffff !important;
    box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.2);
    font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif;
    transform: translateY(105%);
    visibility: hidden;
    transition: transform 0.45s var(--kp-ease), visibility 0s linear 0.45s;
  }
  body .kk-cat__side.kp-drawer::before { display: none !important; }
  html.kp-filters-open body .kk-cat__side.kp-drawer { transform: none; visibility: visible; transition: transform 0.45s var(--kp-ease), visibility 0s; }
  body .kp-drawer .kk-cat__list { list-style: none; padding: 0 !important; }
  body .kp-drawer .kk-cat__link { color: var(--kp-green-dark); text-decoration: none; font-weight: 500; }
  body .kp-drawer__body { flex: 1 1 auto; overflow-y: auto; padding: 0 22px; overscroll-behavior: contain; }
  body .kp-drawer__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px 14px;
    border-bottom: 1px solid #eeeeee;
  }
  body .kp-drawer__head::before {
    content: "";
    position: absolute;
    left: 50%; top: 7px;
    width: 40px; height: 4px;
    margin-left: -20px;
    border-radius: 4px;
    background: #d9d9d9;
  }
  body .kp-drawer__title { margin: 0; font-size: 18px; font-weight: 700; color: var(--kp-ink); }
  body .kp-drawer__close {
    width: 36px; height: 36px;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 50% !important;
    background: #f2f2f2 !important;
    color: var(--kp-ink) !important;
    font-size: 22px;
    line-height: 36px;
    cursor: pointer;
  }
  body .kp-drawer__foot {
    display: flex;
    gap: 10px;
    padding: 14px 22px calc(14px + env(safe-area-inset-bottom));
    border-top: 1px solid #eeeeee;
  }
  body .kp-drawer__foot a, body .kp-drawer__foot button {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    border-radius: 999px;
    font: inherit;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
  }
  body .kp-drawer-overlay {
    position: fixed;
    inset: 0;
    z-index: 1150;
    background: rgba(0, 0, 0, 0.45);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.35s ease, visibility 0s linear 0.35s;
  }
  html.kp-filters-open .kp-drawer-overlay { opacity: 1; visibility: visible; transition: opacity 0.35s ease, visibility 0s; }
  html.kp-filters-open, html.kp-filters-open body { overflow: hidden; }

  body .kk-cat .kk-cat__main { width: 100% !important; padding: 8px var(--kp-gutter) 56px !important; }
  body .kp-drawer .kk-cat__link { font-size: 16px; padding: 9px 0; display: block; }
  body .kp-drawer .kk-cat__list li { margin: 0 !important; }
  body .kp-drawer__reset { border: 1px solid #cccccc !important; background: #ffffff !important; color: var(--kp-ink) !important; }
  body .kp-drawer__done { border: 0 !important; background: var(--kp-green-dark) !important; color: #ffffff !important; }
}
@media (max-width: 767px) {
  body .kk-cat ul.products { display: grid !important; grid-template-columns: 1fr 1fr; gap: 12px; }
  body .kk-cat ul.products::before, body .kk-cat ul.products::after { display: none !important; }
  body .kk-cat ul.products li.product { width: 100% !important; margin: 0 !important; float: none !important; padding: 12px !important; border-radius: 14px !important; }
  body .kk-cat ul.products li.product img { height: 130px !important; object-fit: contain; margin-bottom: 10px !important; }
  body .kk-cat ul.products li.product .woocommerce-loop-product__title { font-size: 13px !important; line-height: 1.35; padding: 0 !important; }
  body .kk-cat .kk-loop-code { font-size: 10px; }
  body .kk-cat .kk-loop-more { font-size: 11px; }
}

/* =====================================================================
   10. SINGLE PRODUCT – mobile text sizes
   ===================================================================== */
@media (max-width: 767px) {
  body .kk-sp .kk-sp__wood { padding-top: 44px !important; padding-bottom: 28px !important; }
  body .kk-sp .kk-sp__wood img.kk-sp__img { max-height: 300px; width: auto !important; margin: 0 auto; object-fit: contain; }
  body .kk-sp .kk-sp__title { font-size: 26px !important; line-height: 1.15; }
  body .kk-sp .kk-features li { font-size: 15px; }
  body .kk-sp .kk-pinfo__title { font-size: 18px; }
  body .kk-sp .kk-pinfo__row { font-size: 14px; }
}

/* =====================================================================
   11. Reduced motion
   ===================================================================== */
@media (prefers-reduced-motion: reduce) {
  body .kk-cat__side, body .kp-drawer-overlay, body .kk-cat__side .kp-acc__panel, body #masthead { transition: none !important; }
}


/* =====================================================================
   12. Design v3: header menu icon + scroll-video card style
   Only the look changes; card positions (left / right / bottom) stay as they are.
   ===================================================================== */

/* menu icon: three thin, equal lines (as in the design) */
body #masthead .elementor-element.elementor-element-98f606d .elementor-icon svg { display: none !important; }
body #masthead .elementor-element.elementor-element-98f606d .elementor-icon {
  display: block;
  width: 36px;
  height: 19px;
  font-size: 0;
  line-height: 0;
  background:
    linear-gradient(#ffffff, #ffffff) 0 0 / 100% 2px no-repeat,
    linear-gradient(#ffffff, #ffffff) 0 50% / 100% 2px no-repeat,
    linear-gradient(#ffffff, #ffffff) 0 100% / 100% 2px no-repeat;
  cursor: pointer;
}
@media (max-width: 1024px) {
  body #masthead .elementor-element.elementor-element-98f606d .elementor-icon { width: 28px; height: 15px; }
}
@media (max-width: 767px) {
  body #masthead .elementor-element.elementor-element-98f606d .elementor-icon { width: 26px; height: 14px; }
}

/* video cards: bright frosted glass, soft thick white rim, rounder corners */
body .kk-sh .kk-sh__card {
  background: rgba(255, 255, 255, 0.6) !important;
  -webkit-backdrop-filter: blur(20px) saturate(150%) !important;
  backdrop-filter: blur(20px) saturate(150%) !important;
  border: 1.5px solid rgba(255, 255, 255, 0.95) !important;
  /* design edge: light-grey rim on the right and bottom + soft drop shadow towards the bottom right */
  box-shadow:
    4px 5px 0 rgba(208, 208, 208, 0.9),
    10px 14px 26px rgba(0, 0, 0, 0.28) !important;
  border-radius: 30px !important;
  color: #111111;
}
body .kk-sh h2.kk-sh__title { font-weight: 900 !important; letter-spacing: 0.02em; }
body .kk-sh a.kk-sh__btn {
  justify-content: center;
  border: 1.5px solid #111111 !important;
  border-radius: 999px !important;
  background: transparent !important;
  font-weight: 400 !important;
  letter-spacing: 0.12em !important;
}

@media (min-width: 1025px) {
  /* size and text as in the design: ~555 x 290 */
  body .kk-sh .kk-sh__card { width: min(555px, 100%) !important; min-height: 0 !important; padding: 38px 47px 40px !important; }
  body .kk-sh .kk-sh__card--center { margin-left: calc(min(555px, 100%) / -2) !important; }
  body .kk-sh .kk-sh__eyebrow { font-size: 22px !important; font-weight: 400 !important; letter-spacing: 0 !important; line-height: 1.2 !important; }
  body .kk-sh h2.kk-sh__title { margin: 6px 0 14px !important; font-size: 33.7px !important; line-height: 1.2 !important; }
  body .kk-sh .kk-sh__text { margin: 0 0 24px !important; font-size: 22px !important; line-height: 1.3 !important; }
  body .kk-sh a.kk-sh__btn { width: 200px; height: 38px; padding: 0 !important; box-sizing: border-box; font-size: 16px !important; line-height: 1 !important; }
}
@media (max-width: 767px) {
  body .kk-sh .kk-sh__card { border-radius: 22px !important; }
}


/* =====================================================================
   13. Home: header stays pinned over the scroll video (tablet + desktop),
   then slides away with the page when the video section ends (kk-polish.js).
   Phones keep their own sticky header.
   ===================================================================== */
@media (min-width: 768px) {
  body #masthead.kp-hero-pin {
    position: -webkit-sticky !important;
    position: sticky !important;
    top: 0;
    z-index: 1000 !important;
    will-change: transform;
  }
  body.admin-bar #masthead.kp-hero-pin { top: 32px; }
}

/* Catalogue / category banners: same overlay as the Where to Buy banner (black, 63%) */
:root { --kk-hero-overlay: rgba(0, 0, 0, 0.63); }


/* =====================================================================
   14. Header language switch: ENGLISH first, then Arabic (as in the design);
   Arabic label in Cairo (loaded by the snippet)
   ===================================================================== */
body #masthead .gtranslate_wrapper {
  display: flex !important;
  flex-direction: row !important;
  align-items: center !important;
  justify-content: flex-start !important;
  gap: 16px !important;
}
body #masthead .gtranslate_wrapper a[data-gt-lang="en"] { order: 1 !important; }
body #masthead .gtranslate_wrapper a[data-gt-lang="ar"] { order: 2 !important; }
body #masthead .gtranslate_wrapper a[data-gt-lang]::after { line-height: 1 !important; display: inline-block; vertical-align: middle; }
body #masthead a[data-gt-lang="en"]::after {
  font-family: "Albula Pro", "Satoshi", Arial, sans-serif !important;
  font-weight: 700 !important;
  font-size: 16px !important;
  letter-spacing: 0.02em;
}
body #masthead a[data-gt-lang="ar"]::after {
  font-family: "Cairo", "Noto Sans Arabic", Tahoma, sans-serif !important;
  font-weight: 700 !important;
  font-size: 17px !important;
  opacity: 1 !important;
  position: relative; top: -1px;
}
/* space between the menu icon and the languages */
@media (min-width: 1025px) {
  body #masthead .elementor-element.elementor-element-f760a15 { gap: 40px !important; --gap: 40px; }
}
@media (min-width: 768px) and (max-width: 1024px) {
  body #masthead .elementor-element.elementor-element-f760a15 { gap: 24px !important; --gap: 24px; }
  body #masthead .gtranslate_wrapper { gap: 12px !important; }
  body #masthead a[data-gt-lang="en"]::after { font-size: 14px !important; }
  body #masthead a[data-gt-lang="ar"]::after { font-size: 15px !important; }
}
/* phones: the two labels stack, ENGLISH on top */
@media (max-width: 767px) {
  body #masthead .gtranslate_wrapper { flex-direction: column !important; align-items: flex-start !important; gap: 4px !important; }
  body #masthead a[data-gt-lang="en"]::after { font-size: 12px !important; }
  body #masthead a[data-gt-lang="ar"]::after { font-size: 13px !important; top: 0; }
}


/* =====================================================================
   15. Retailer logos: one continuous, seamless scroll (built in kk-polish.js)
   ===================================================================== */
.kp-logos { position: relative; width: 100%; overflow: hidden; -webkit-mask-image: linear-gradient(90deg, transparent 0, #000 6%, #000 94%, transparent 100%); mask-image: linear-gradient(90deg, transparent 0, #000 6%, #000 94%, transparent 100%); }
.kp-logos__track { display: flex; width: max-content; animation: kp-logos 38s linear infinite; will-change: transform; }
.kp-logos__item { flex: 0 0 auto; display: flex; align-items: center; justify-content: center; width: 230px; height: 96px; margin: 0 22px; }
.kp-logos__item img { display: block; max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; mix-blend-mode: multiply; }
@keyframes kp-logos { from { transform: translate3d(0, 0, 0); } to { transform: translate3d(-50%, 0, 0); } }
@media (max-width: 767px) {
  .kp-logos__track { animation-duration: 26s; }
  .kp-logos__item { width: 140px; height: 64px; margin: 0 14px; }
}
@media (prefers-reduced-motion: reduce) { .kp-logos__track { animation-duration: 90s; } }
.kp-logos-on .swiper, .kp-logos-on .elementor-image-carousel-wrapper { display: none !important; }

/* =====================================================================
   16. Home: SMART / FUNCTIONAL / RELIABLE – three equal columns, thin even dividers
   ===================================================================== */
@media (min-width: 768px) {
  body .elementor-43 .elementor-element.elementor-element-755069b {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr);
    align-items: stretch;
    gap: 0 !important;
  }
  body .elementor-43 :is(.elementor-element-85d3640, .elementor-element-63b71db, .elementor-element-704d045) {
    width: auto !important; max-width: none !important;
    margin: 0 !important;
    padding: 4px 28px !important;
    border: 0 !important;
  }
  body .elementor-43 :is(.elementor-element-85d3640, .elementor-element-63b71db) { border-right: 1px solid rgba(255, 255, 255, 0.75) !important; }
  body .elementor-43 :is(.elementor-element-85d3640, .elementor-element-63b71db, .elementor-element-704d045) .elementor-icon-box-description {
    max-width: 230px; margin: 0 auto;
    text-wrap: balance;
  }
}

/* =====================================================================
   17. Buttons: one consistent style across the site
   text always centred, Satoshi, even padding; pills for content buttons
   ===================================================================== */
body .elementor-button,
body .kk-sh .kk-sh__card a.kk-sh__btn,
body .sk-fs button.sk-fs__apply {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center;
  gap: 10px;
  box-sizing: border-box;
  line-height: 1 !important;
  font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
}
body .elementor-button .elementor-button-content-wrapper { align-items: center; line-height: 1; }
body .elementor-button .elementor-button-text { line-height: 1; padding-top: 1px; }

/* content buttons (READ MORE, DISCOVER THIS PRODUCT): pill, 44px, letter-spaced */
body .elementor-43 :is(.elementor-element-c03da2d, .elementor-element-041e5ca) .elementor-button {
  min-height: 44px;
  padding: 0 30px !important;
  border-radius: 999px !important;
  font-size: 14px !important;
  font-weight: 500 !important;
  letter-spacing: 0.1em !important;
  text-transform: uppercase;
}
/* header WHERE TO BUY */
body #masthead .elementor-element.elementor-element-0a2ca92 .elementor-button {
  min-height: 45px;
  padding: 0 22px !important;
  border-radius: 10px !important;
  font-weight: 700 !important;
  letter-spacing: 0.04em !important;
}
/* footer buttons: same height and padding */
body .elementor-30 .elementor-element.elementor-element-6e495df .elementor-button {
  min-height: 44px;
  padding: 0 22px !important;
  border-radius: 4px !important;
  font-weight: 600 !important;
}
/* area buttons keep their pill, just the font */
body .elementor-43 :is(.elementor-element-a682427, .elementor-element-3a87742, .elementor-element-e648c86, .elementor-element-0f74986) .elementor-button { letter-spacing: 0.06em !important; }
/* form buttons */
body .cf7-custom-form input[type="submit"], body .cf7-custom-form .wpcf7-submit,
body .sk-fs button.sk-fs__apply { font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important; font-weight: 500 !important; }
@media (max-width: 767px) {
  body .elementor-43 :is(.elementor-element-c03da2d, .elementor-element-041e5ca) .elementor-button { min-height: 42px; padding: 0 24px !important; font-size: 13px !important; }
}

/* =====================================================================
   18. Arabic version (after translation): Cairo for all text
   ===================================================================== */
html:is([lang^="ar"], .translated-rtl) body,
html:is([lang^="ar"], .translated-rtl) body :is(h1, h2, h3, h4, h5, h6, p, a, span, li, label, button, input, textarea, select, font, div, .elementor-heading-title, .elementor-button) {
  font-family: "Cairo", "Noto Sans Arabic", Tahoma, sans-serif !important;
  letter-spacing: 0 !important;
}


/* =====================================================================
   19. Green strip, matched to the design:
   dividers run exactly from the top of the icons to the last line of text;
   READ MORE / DISCOVER THIS PRODUCT: 38px pill, 48px side padding, regular, letter-spaced
   ===================================================================== */
@media (min-width: 768px) {
  body .elementor-43 .elementor-element.elementor-element-755069b { align-items: start !important; align-content: start !important; padding-top: 0 !important; padding-bottom: 0 !important; }
  body .elementor-43 :is(.elementor-element-85d3640, .elementor-element-63b71db, .elementor-element-704d045) { padding-top: 0 !important; padding-bottom: 0 !important; }
  body .elementor-43 :is(.elementor-element-85d3640, .elementor-element-63b71db, .elementor-element-704d045) :is(.elementor-widget-container, .elementor-icon-box-wrapper) { padding: 0 !important; margin: 0 !important; }
  body .elementor-43 :is(.elementor-element-85d3640, .elementor-element-63b71db, .elementor-element-704d045) .elementor-icon-box-description { margin-bottom: 0 !important; padding-bottom: 0 !important; }
  body .elementor-43 :is(.elementor-element-85d3640, .elementor-element-63b71db, .elementor-element-704d045) .elementor-icon-box-content > :last-child { margin-bottom: 0 !important; }
}
body .elementor-43 :is(.elementor-element-c03da2d, .elementor-element-041e5ca) .elementor-button {
  min-height: 38px !important;
  height: 38px;
  padding: 0 48px !important;
  font-size: 15px !important;
  font-weight: 400 !important;
  letter-spacing: 0.12em !important;
}
@media (max-width: 767px) {
  body .elementor-43 :is(.elementor-element-c03da2d, .elementor-element-041e5ca) .elementor-button { padding: 0 32px !important; font-size: 13px !important; }
}


/* =====================================================================
   20. Solution cards: new icons (Sweep / Mop / Wipe / Scrub / Dust)
   images in the media library: kk-solution-icon-*.png
   ===================================================================== */
body .elementor-43 :is(.elementor-element-be16fc7, .elementor-element-8327566, .elementor-element-8a4b513, .elementor-element-deef3eb, .elementor-element-6a9b900) .elementor-icon svg,
body .elementor-43 :is(.elementor-element-be16fc7, .elementor-element-8327566, .elementor-element-8a4b513, .elementor-element-deef3eb, .elementor-element-6a9b900) .elementor-icon i { display: none !important; }
body .elementor-43 :is(.elementor-element-be16fc7, .elementor-element-8327566, .elementor-element-8a4b513, .elementor-element-deef3eb, .elementor-element-6a9b900) .elementor-icon {
  display: block;
  width: 1em; height: 1em;
  background: center / contain no-repeat;
  border-radius: 50%;
}
body .elementor-43 .elementor-element.elementor-element-be16fc7 .elementor-icon { background-image: url("https://soharon.co.uk/off-29/wp-content/uploads/2026/11/kk-solution-icon-sweep.png") !important; }
body .elementor-43 .elementor-element.elementor-element-8327566 .elementor-icon { background-image: url("https://soharon.co.uk/off-29/wp-content/uploads/2026/11/kk-solution-icon-mop.png") !important; }
body .elementor-43 .elementor-element.elementor-element-8a4b513 .elementor-icon { background-image: url("https://soharon.co.uk/off-29/wp-content/uploads/2026/11/kk-solution-icon-wipe.png") !important; }
body .elementor-43 .elementor-element.elementor-element-deef3eb .elementor-icon { background-image: url("https://soharon.co.uk/off-29/wp-content/uploads/2026/11/kk-solution-icon-scrub.png") !important; }
body .elementor-43 .elementor-element.elementor-element-6a9b900 .elementor-icon { background-image: url("https://soharon.co.uk/off-29/wp-content/uploads/2026/11/kk-solution-icon-dust.png") !important; }


/* =====================================================================
   21. One Home: the mint section ends at the house image; a white gap follows
   (61px on desktop as in the design, 40px on phones)
   ===================================================================== */
body .elementor-43 .elementor-element.elementor-element-7149c5d { --padding-bottom: 0px; padding-bottom: 0 !important; margin-bottom: 61px !important; }
body .elementor-43 .elementor-element.elementor-element-7149c5d > .e-con-inner { padding-bottom: 0 !important; }
@media (max-width: 767px) {
  body .elementor-43 .elementor-element.elementor-element-7149c5d { margin-bottom: 40px !important; }
  body .elementor-43 .elementor-element.elementor-element-38068a6 { margin-bottom: 0 !important; }
}


/* =====================================================================
   22. Contact form: Submit styled like the site buttons, same hover
   (lift + shadow + shine sweep); fields get a matching hover / focus
   ===================================================================== */
body .cf7-custom-form .kp-submit {
  position: relative;
  display: block;
  width: 100%;
  overflow: hidden;
  border-radius: 999px;
  isolation: isolate;
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
}
body .cf7-custom-form .kp-submit::after {
  content: "";
  position: absolute;
  top: 0; left: -80%;
  z-index: 2;
  width: 45%; height: 100%;
  background: linear-gradient(100deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.38), rgba(255, 255, 255, 0));
  transform: skewX(-20deg);
  pointer-events: none;
}
body .cf7-custom-form input[type="submit"],
body .cf7-custom-form .wpcf7-submit {
  display: block;
  width: 100% !important;
  height: 44px !important;
  padding: 0 30px !important;
  border-radius: 999px !important;
  text-align: center !important;
  font-family: "Satoshi", "Helvetica Neue", Arial, sans-serif !important;
  font-size: 15px !important;
  font-weight: 500 !important;
  letter-spacing: 0.12em !important;
  text-transform: uppercase !important;
  line-height: 44px !important;
  transition: background-color 0.3s ease, border-color 0.3s ease !important;
}
/* the wrapper does the lift, so the button itself stays put */
body .cf7-custom-form .kp-submit input[type="submit"],
body .cf7-custom-form .kp-submit .wpcf7-submit { transform: none !important; box-shadow: none !important; }
@media (hover: hover) {
  body .cf7-custom-form .kp-submit:hover { transform: translateY(-3px); box-shadow: 0 12px 26px rgba(0, 0, 0, 0.18); }
  body .cf7-custom-form .kp-submit:hover::after { left: 140%; transition: left 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
  body .cf7-custom-form .kp-submit:hover input[type="submit"],
  body .cf7-custom-form .kp-submit:hover .wpcf7-submit { background: #007548 !important; border-color: #007548 !important; }

  body .cf7-custom-form :is(input[type="text"], input[type="email"], input[type="tel"], textarea):hover { border-color: #e0a010 !important; }
}
body .cf7-custom-form .kp-submit:active { transform: translateY(-1px); }
body .cf7-custom-form :is(input[type="text"], input[type="email"], input[type="tel"], textarea) { transition: border-color 0.3s ease, box-shadow 0.3s ease !important; }
body .cf7-custom-form :is(input[type="text"], input[type="email"], input[type="tel"], textarea):focus {
  border-color: #009160 !important;
  box-shadow: 0 0 0 3px rgba(0, 145, 96, 0.15) !important;
}
body .cf7-custom-form .wpcf7-submit:focus-visible { outline: 3px solid #f0b232; outline-offset: 3px; }


/* =====================================================================
   23. Catalogue cards: no empty space above the photo (it starts at the card's top edge)
   ===================================================================== */
body .kk-cat ul.products li.product { padding-top: 0 !important; overflow: hidden; }
body .kk-cat ul.products li.product a img,
body .kk-cat ul.products li.product img { margin-top: 0 !important; }


/* =====================================================================
   24. Who We Are: sparkle stars (new image) + rotating "No …" heading
   ===================================================================== */
body .elementor-153 :is(.elementor-element-1b9f740, .elementor-element-3686e69, .elementor-element-f5a2f5e, .elementor-element-e6c8adb) img.kp-star,
body .elementor-153 .elementor-element.elementor-element-e6c8adb img.kp-star,
body .elementor-153 .elementor-element.elementor-element-f5a2f5e img.kp-star {
  width: auto !important;
  height: 30px !important;
  max-width: none !important;
  margin: 0 auto;
  display: block;
}
@media (max-width: 767px) {
  body .elementor-153 :is(.elementor-element.elementor-element-1b9f740, .elementor-element.elementor-element-3686e69, .elementor-element.elementor-element-f5a2f5e, .elementor-element.elementor-element-e6c8adb) img.kp-star { height: 22px !important; }
}



/* =====================================================================
   25. Rotating "No …" heading (smooth): one line, centred; transform/opacity only
   ===================================================================== */
body .elementor-153 .elementor-element.elementor-element-fb87731 { width: 100% !important; max-width: 100% !important; }
body .elementor-153 .elementor-element.elementor-element-fb87731 .elementor-heading-title.kp-rot-ready {
  display: block;
  text-align: center;
  white-space: nowrap;
  position: relative;
  height: 1.2em;
  line-height: 1.2em !important;
  overflow: visible;
}
/* centred by its own midpoint, so it stays centred even while wider than the page during a change */
.kp-line {
  position: absolute;
  left: 50%;
  top: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 1.2em;
  white-space: nowrap;
  transform: translate3d(-50%, 0, 0);
  transform-origin: 50% 50%;
  will-change: transform;
}
.kp-no { flex: 0 0 auto; }
.kp-rot {
  position: relative;
  flex: 0 0 auto;
  height: 1.2em;
  overflow: hidden;
}
.kp-rot__item {
  position: absolute;
  left: 0; top: 0;
  height: 1.2em;
  line-height: 1.2em;
  white-space: nowrap;
  opacity: 0;
  transform: translate3d(0, 70%, 0);
  will-change: transform, opacity;
  backface-visibility: hidden;
  transition: transform 0.8s cubic-bezier(0.65, 0, 0.35, 1), opacity 0.6s ease;
}
.kp-rot__item.is-active { opacity: 1; transform: translate3d(0, 0, 0); transition-delay: 0.08s; }
.kp-rot__item.is-leaving { opacity: 0; transform: translate3d(0, -70%, 0); }
.kp-rot__txt { display: inline-block; }
/* measuring / first paint: no animation */
.kp-measuring .kp-rot__item, .kp-no-anim .kp-rot__item, .kp-no-anim .kp-rot, .kp-no-anim .kp-line { transition: none !important; }
/* phones: "No" on its own line, the rotating phrase centred below (long phrase wraps) */
@media (max-width: 767px) {
  body .elementor-153 .elementor-element.elementor-element-fb87731 .elementor-heading-title.kp-rot-ready { white-space: normal; height: auto; line-height: 1.1 !important; }
  .kp-line { position: static; display: block; height: auto; white-space: normal; transform: none !important; }
  .kp-no { display: block; }
  .kp-rot { display: block; width: 100% !important; height: 2.3em; }
  .kp-rot__item { left: 0; right: 0; height: 2.3em; line-height: 1.1; display: flex; align-items: center; justify-content: center; text-align: center; white-space: normal; }
  .kp-rot__txt { display: block; }
}
@media (prefers-reduced-motion: reduce) {
  .kp-line, .kp-rot, .kp-rot__item { transition: none !important; }
}


/* =====================================================================
   26. Hero step scrolling (kk-hero.js): two stacked videos (forward / reversed),
   poster fades away after the first card
   ===================================================================== */
.kk-sh.kk-step .kk-sh__video { opacity: 0; transition: none; }
.kk-sh.kk-step .kk-sh__video.is-on { opacity: 1; }
.kk-sh.kk-step .kk-sh__poster { opacity: 1; visibility: visible; transition: opacity 0.7s ease, visibility 0s; }
.kk-sh.kk-step .kk-sh__poster.is-off { opacity: 0; visibility: hidden; transition: opacity 0.7s ease, visibility 0s linear 0.7s; }
.kk-sh.kk-step .kk-sh__bar i { transition: none; }


/* =====================================================================
   27. Video cards: heading and paragraph 25% smaller (desktop + tablet)
   ===================================================================== */
@media (min-width: 1025px) {
  body .kk-sh h2.kk-sh__title { font-size: 25.3px !important; margin: 4px 0 10px !important; }   /* was 33.7px */
  body .kk-sh .kk-sh__text { font-size: 16.5px !important; margin: 0 0 20px !important; }        /* was 22px */
}
@media (min-width: 768px) and (max-width: 1024px) {
  body .kk-sh h2.kk-sh__title { font-size: 18px !important; }   /* was 24px */
  body .kk-sh .kk-sh__text { font-size: 13px !important; }      /* was 16px (13px keeps it readable) */
}
</style>
    <?php
}

add_action( 'wp_footer', 'kk_polish_js', 100 );
function kk_polish_js() {
    if ( is_admin() || isset( $_GET['elementor-preview'] ) ) {
        return;
    }
    ?>
<script id="kk-polish-js">
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

    /* ---------- 1g. Who We Are: "No" stays, the rest rotates smoothly ----------
       Widths are measured once at full size; the line only animates with transform / opacity
       and one width transition, all on the same timing, so there is no reflow jump. */
    (function () {
        var h = document.querySelector('.elementor-153 .elementor-element-fb87731 .elementor-heading-title');
        if (!h || h.classList.contains('kp-rot-ready')) { return; }
        var phrases = ['hype.', 'gimmicks.', 'over-engineering.', 'premium price without premium value.'];
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var narrow = window.matchMedia('(max-width: 767px)');

        h.setAttribute('aria-label', 'No ' + phrases.join(' No '));
        h.textContent = '';
        var line = document.createElement('span');
        line.className = 'kp-line';
        line.setAttribute('aria-hidden', 'true');
        var no = document.createElement('span');
        no.className = 'kp-no';
        no.textContent = 'No ';
        var rot = document.createElement('span');
        rot.className = 'kp-rot';
        var items = phrases.map(function (txt) {
            var s = document.createElement('span');
            s.className = 'kp-rot__item';
            var tx = document.createElement('span');
            tx.className = 'kp-rot__txt';
            tx.textContent = txt;
            s.appendChild(tx);
            s.__t = tx;
            rot.appendChild(s);
            return s;
        });
        line.appendChild(no);
        line.appendChild(rot);
        h.appendChild(line);
        h.classList.add('kp-rot-ready');

        var i = 2;   // start on "over-engineering"

        function fit() {
            h.classList.add('kp-measuring');
            items.forEach(function (it) { it.__t.style.fontSize = ''; });
            if (narrow.matches) {   // phones: stacked; a phrase that needs more than two lines is shrunk a little
                rot.style.width = '';
                line.style.transform = '';
                h.classList.remove('kp-measuring');
                items.forEach(function (it) {
                    var k = 100;
                    while (it.__t.offsetHeight > it.clientHeight + 1 && k > 55) { k -= 5; it.__t.style.fontSize = k + '%'; }
                });
                return;
            }
            var room = h.clientWidth - 8;
            line.style.transform = 'translate3d(-50%, 0, 0)';
            noW = no.getBoundingClientRect().width;
            items.forEach(function (it) {
                var w = Math.ceil(it.__t.getBoundingClientRect().width) + 2;
                it.__w = w;
                it.__k = Math.min(1, room / (noW + w));
            });
            h.classList.remove('kp-measuring');
            apply(i, true);
        }
        // the visible line width and the zoom move together in one animation, so the line never overshoots
        var cur = null, anim = 0, noW = 0;
        function setState(w, k) {
            rot.style.width = w.toFixed(2) + 'px';
            line.style.transform = 'translate3d(-50%, 0, 0)' + (k < 0.9999 ? ' scale(' + k.toFixed(5) + ')' : '');
            cur = { w: w, k: k };
        }
        function ease(x) { return x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2; }
        function apply(n, instant) {
            if (narrow.matches) { return; }
            var to = { w: items[n].__w, k: items[n].__k };
            cancelAnimationFrame(anim);
            if (instant || !cur || reduce || h.classList.contains('kp-no-anim')) { setState(to.w, to.k); return; }
            var from = { w: cur.w, k: cur.k };
            var v0 = from.k * (noW + from.w), v1 = to.k * (noW + to.w);   // visible widths
            var t0 = performance.now(), D = 800;
            (function step(now) {
                var p = Math.min(1, (now - t0) / D), e = ease(p);
                var k = from.k + (to.k - from.k) * e;
                var v = v0 + (v1 - v0) * e;
                setState(Math.max(0, v / k - noW), k);
                if (p < 1) { anim = requestAnimationFrame(step); }
            })(t0);
        }
        function show(n, first) {
            items.forEach(function (it, k) {
                it.classList.toggle('is-active', k === n);
                it.classList.toggle('is-leaving', !first && k === i && k !== n);
                if (k !== n && k !== i) { it.classList.remove('is-leaving'); }
            });
            i = n;
            apply(n);
        }

        // first layout without animation
        h.classList.add('kp-no-anim');
        fit();
        show(i, true);
        requestAnimationFrame(function () { requestAnimationFrame(function () { h.classList.remove('kp-no-anim'); }); });
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function () { h.classList.add('kp-no-anim'); fit(); requestAnimationFrame(function () { h.classList.remove('kp-no-anim'); }); });
        }
        var rt = 0;
        window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(function () { h.classList.add('kp-no-anim'); fit(); requestAnimationFrame(function () { h.classList.remove('kp-no-anim'); }); }, 150); });
        if (reduce) { return; }

        var timer = null;
        function start() { if (!timer) { timer = setInterval(function () { if (!document.hidden) { show((i + 1) % items.length); } }, 3000); } }
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
</script>
    <?php
}
