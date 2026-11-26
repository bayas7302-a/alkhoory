(function () {
    /* Kress Kleen home hero – simple version:
       a full-screen background video (muted, looping) with the first card on top.
       No scroll control: the page scrolls normally. */
    var UP = 'https://soharon.co.uk/off-29/wp-content/uploads/2026/11/';
    var big = Math.max(window.screen.width || 0, window.innerWidth) * Math.min(window.devicePixelRatio || 1, 2) >= 1700 && window.innerWidth >= 1025;
    var SRC = UP + (big ? 'kk-hero-v2-1080-fwd.mp4' : 'kk-hero-v2-720-fwd.mp4');
    var FIRST = UP + (big ? 'kk-hero-v2-first-frame.jpg' : 'kk-hero-v2-first-frame-720.jpg');

    var root = document.getElementById('kk-sh');
    if (!root || root.kkShReady) { return; }
    root.kkShReady = true;
    root.classList.add('kk-simple');

    var video = root.querySelector('.kk-sh__video');
    var poster = root.querySelector('.kk-sh__poster');
    var cards = [].slice.call(root.querySelectorAll('.kk-sh__card'));

    // only the first card
    cards.forEach(function (c, i) { c.classList.toggle('is-active', i === 0); c.classList.remove('is-leaving'); });

    // opening image = the video's first frame, until the video is playing
    if (poster && poster.getAttribute('src') !== FIRST) { poster.removeAttribute('srcset'); poster.setAttribute('src', FIRST); }
    if (!video) { return; }
    video.muted = true; video.defaultMuted = true; video.playsInline = true; video.loop = true; video.autoplay = true;
    ['muted', 'playsinline', 'webkit-playsinline', 'loop', 'autoplay'].forEach(function (a) { video.setAttribute(a, ''); });
    video.setAttribute('poster', FIRST);
    video.preload = 'auto';
    if (video.getAttribute('src') !== SRC) { video.setAttribute('src', SRC); video.load(); }

    function hidePoster() { if (poster) { poster.classList.add('is-off'); } }
    video.addEventListener('playing', hidePoster);

    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    function play() {
        if (reduce) { return; }   // visitors who turned off motion see the still first frame
        var p; try { p = video.play(); } catch (e) {}
        if (p && p.catch) { p.catch(function () {}); }
    }
    play();
    // some phones only start a video after the first touch
    ['touchstart', 'pointerdown', 'scroll'].forEach(function (ev) {
        window.addEventListener(ev, function once() { window.removeEventListener(ev, once); if (video.paused) { play(); } }, { passive: true });
    });
    // save battery: pause while the hero is off screen
    if ('IntersectionObserver' in window && !reduce) {
        new IntersectionObserver(function (e) { if (e[0].isIntersecting) { play(); } else { video.pause(); } }).observe(root);
    }
})();
