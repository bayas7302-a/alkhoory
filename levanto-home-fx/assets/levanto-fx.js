/* Levanto Home FX — behaviour layer. No dependencies. */
(function () {
	'use strict';

	var inEditor = document.body.classList.contains('elementor-editor-active') ||
		/[?&]elementor-preview=/.test(window.location.search);
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

	/* ---------- 1. Anchor ids for the one-page nav ---------- */
	var anchors = {
		'lv-sec-hero': 'top', 'lv-sec-about': 'about', 'lv-sec-products': 'products', 'lv-sec-services': 'services',
		'lv-sec-industries': 'industries', 'lv-sec-why': 'why', 'lv-sec-projects': 'projects',
		'lv-sec-brands': 'brands', 'lv-sec-contact': 'contact', 'lv-sec-footer': 'footer'
	};
	Object.keys(anchors).forEach(function (cls) {
		var el = document.querySelector('.' + cls);
		if (el && !document.getElementById(anchors[cls])) { el.id = anchors[cls]; }
	});

	/* ---------- 2. Scroll progress bar ---------- */
	var bar = document.createElement('div');
	bar.className = 'lv-progress';
	document.body.appendChild(bar);

	/* ---------- 3. Sticky nav pill + current-section highlight ---------- */
	var pill = document.querySelector('.lv-nav-pill');
	var header = pill && pill.parentElement;
	var links = $$('.lv-navlink');
	var linkOf = function (el) { return el.closest('a') || el.querySelector('a') || el; };

	function onScroll() {
		var y = window.scrollY || window.pageYOffset;
		var max = document.documentElement.scrollHeight - window.innerHeight;
		bar.style.transform = 'scaleX(' + (max > 0 ? y / max : 0) + ')';
		if (pill && !inEditor) {
			if (y > 220 && !pill.classList.contains('lv-is-fixed')) {
				header.style.minHeight = header.offsetHeight + 'px';
				pill.classList.add('lv-is-fixed');
			} else if (y <= 220 && pill.classList.contains('lv-is-fixed')) {
				pill.classList.remove('lv-is-fixed');
				header.style.minHeight = '';
			}
		}
	}
	window.addEventListener('scroll', onScroll, { passive: true });
	onScroll();

	if ('IntersectionObserver' in window && links.length) {
		var byId = {};
		links.forEach(function (a) {
			var href = linkOf(a).getAttribute('href') || '';
			if (href.charAt(0) === '#') { byId[href.slice(1)] = a; }
		});
		var secObs = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				if (e.isIntersecting && byId[e.target.id]) {
					links.forEach(function (l) { l.classList.remove('is-current'); });
					byId[e.target.id].classList.add('is-current');
				}
			});
		}, { rootMargin: '-45% 0px -50% 0px' });
		Object.keys(byId).forEach(function (id) { var s = document.getElementById(id); if (s) { secObs.observe(s); } });
	}

	/* ---------- 4. Mobile menu ---------- */
	if (pill && links.length) {
		var burger = document.createElement('button');
		burger.type = 'button';
		burger.className = 'lv-burger';
		burger.setAttribute('aria-label', 'Open menu');
		burger.setAttribute('aria-expanded', 'false');
		burger.innerHTML = '<span></span>';
		var menu = document.createElement('nav');
		menu.className = 'lv-mobile-menu';
		menu.setAttribute('aria-label', 'Mobile');
		links.forEach(function (a) {
			var item = document.createElement('a');
			item.href = linkOf(a).getAttribute('href') || '#';
			item.textContent = a.textContent.trim();
			menu.appendChild(item);
		});
		var quote = pill.querySelector('.lv-btn');
		pill.insertBefore(burger, quote || null);
		pill.appendChild(menu);
		var toggle = function (open) {
			pill.classList.toggle('lv-menu-open', open);
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
		};
		burger.addEventListener('click', function () { toggle(!pill.classList.contains('lv-menu-open')); });
		menu.addEventListener('click', function (e) { if (e.target.tagName === 'A') { toggle(false); } });
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { toggle(false); } });
	}

	/* ---------- 5. Marquee (duplicate content for a seamless loop) ---------- */
	$$('.lv-marquee-track').forEach(function (track) {
		if (inEditor || track.dataset.lvReady) { return; }
		track.dataset.lvReady = '1';
		var kids = Array.prototype.slice.call(track.children);
		kids.forEach(function (k) {
			var c = k.cloneNode(true);
			c.setAttribute('aria-hidden', 'true');
			c.removeAttribute('data-interaction-id');
			c.removeAttribute('data-id');
			track.appendChild(c);
		});
		var measure = function () {
			var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 0;
			var shift = track.scrollWidth / 2 + gap / 2;
			track.style.setProperty('--lv-marquee-shift', shift + 'px');
			track.style.setProperty('--lv-marquee-dur', Math.max(20, shift / 70) + 's');
		};
		measure();
		window.addEventListener('load', measure);
		window.addEventListener('resize', measure);
	});

	/* ---------- 6. Product accordion ---------- */
	$$('.lv-accordion').forEach(function (acc) {
		var panels = $$('.lv-panel', acc);
		if (!panels.length) { return; }
		var hoverCapable = window.matchMedia('(hover: hover) and (min-width: 768px)').matches;
		var open = function (p) {
			panels.forEach(function (x) {
				var on = x === p;
				x.classList.toggle('is-open', on);
				x.setAttribute('aria-expanded', on ? 'true' : 'false');
			});
		};
		panels.forEach(function (p) {
			p.setAttribute('tabindex', '0');
			p.setAttribute('role', 'button');
			p.addEventListener('click', function () { open(p); });
			p.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(p); } });
			if (hoverCapable) {
				var t;
				p.addEventListener('mouseenter', function () { clearTimeout(t); t = setTimeout(function () { open(p); }, 90); });
				p.addEventListener('mouseleave', function () { clearTimeout(t); });
			}
		});
		open(panels.filter(function (p) { return p.classList.contains('lv-panel-open'); })[0] || panels[0]);
	});

	/* ---------- 7. Services rows: active row follows the pointer ---------- */
	$$('.lv-svc-list').forEach(function (list) {
		var rows = $$('.lv-svc', list);
		var setActive = function (r) { rows.forEach(function (x) { x.classList.toggle('is-active', x === r); }); };
		setActive(rows.filter(function (r) { return r.classList.contains('lv-svc-active'); })[0] || rows[0]);
		rows.forEach(function (r) {
			r.addEventListener('mouseenter', function () { setActive(r); });
			r.addEventListener('focusin', function () { setActive(r); });
		});
	});

	/* ---------- 8. Industry cards: gentle 3D tilt ---------- */
	if (!reduceMotion && window.matchMedia('(hover: hover)').matches) {
		$$('.lv-ind-card').forEach(function (card) {
			card.addEventListener('mousemove', function (e) {
				var r = card.getBoundingClientRect();
				var x = (e.clientX - r.left) / r.width - 0.5;
				var y = (e.clientY - r.top) / r.height - 0.5;
				card.style.transform = 'perspective(900px) translateY(-10px) rotateX(' + (-y * 6) + 'deg) rotateY(' + (x * 8) + 'deg)';
			});
			card.addEventListener('mouseleave', function () { card.style.transform = ''; });
		});
	}

	/* ---------- 9. Counters ---------- */
	var counters = $$('.lv-counter');
	var runCounter = function (el) {
		if (el.dataset.lvCounted) { return; }
		el.dataset.lvCounted = '1';
		var txt = el.textContent.trim();
		var m = txt.match(/^([^0-9]*)([0-9][0-9,]*)(.*)$/);
		if (!m || reduceMotion) { return; }
		var target = parseInt(m[2].replace(/,/g, ''), 10);
		var useComma = m[2].indexOf(',') > -1;
		var start = null, dur = 1600 + Math.min(target, 3000) / 3;
		var fmt = function (n) { return useComma ? n.toLocaleString('en-US') : String(n); };
		var step = function (ts) {
			if (!start) { start = ts; }
			var t = Math.min(1, (ts - start) / dur);
			var eased = 1 - Math.pow(1 - t, 4);
			el.textContent = m[1] + fmt(Math.round(target * eased)) + m[3];
			if (t < 1) { requestAnimationFrame(step); } else { el.textContent = txt; }
		};
		el.textContent = m[1] + '0' + m[3];
		requestAnimationFrame(step);
	};
	if ('IntersectionObserver' in window) {
		var cObs = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) { if (e.isIntersecting) { runCounter(e.target); cObs.unobserve(e.target); } });
		}, { threshold: 0.6 });
		counters.forEach(function (c) { cObs.observe(c); });
	}

	/* ---------- 10. Parallax on decorative patterns ---------- */
	if (!reduceMotion) {
		var decos = $$('.lv-rings, .lv-glow, .lv-dots, .lv-flow').filter(function (d) { return !d.classList.contains('lv-cta-ring'); });
		var ticking = false;
		var parallax = function () {
			var vh = window.innerHeight;
			decos.forEach(function (d, i) {
				var r = d.parentElement.getBoundingClientRect();
				if (r.bottom < -200 || r.top > vh + 200) { return; }
				var progress = (r.top + r.height / 2 - vh / 2) / vh;
				d.style.translate = '0 ' + (progress * (i % 2 ? -40 : 40)).toFixed(1) + 'px';
			});
			ticking = false;
		};
		window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(parallax); } }, { passive: true });
		parallax();
	}
})();
