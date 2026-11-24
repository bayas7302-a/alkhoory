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

	/* ---------- 9b. Vector patterns with orange runners ---------- */
	var SVGNS = 'http://www.w3.org/2000/svg';
	var mk = function (tag, attrs, parent) {
		var el = document.createElementNS(SVGNS, tag);
		Object.keys(attrs || {}).forEach(function (k) { el.setAttribute(k, attrs[k]); });
		if (parent) { parent.appendChild(el); }
		return el;
	};
	var rand = function (a, b) { return a + Math.random() * (b - a); };
	var whenVisible = function (el, start) {
		if (!('IntersectionObserver' in window)) { start(true); return; }
		new IntersectionObserver(function (es) { es.forEach(function (e) { start(e.isIntersecting); }); }, { rootMargin: '100px' }).observe(el);
	};
	// Animate an orange dash along `path`; resolves when done.
	var runDash = function (svg, d, opts) {
		var p = mk('path', { d: d, fill: 'none', stroke: opts.color || '#f0652a', 'stroke-width': opts.width || 3, 'stroke-linecap': 'round', 'class': 'lv-runner' }, svg);
		var total = p.getTotalLength(), len = Math.min(opts.len || 110, total * 0.6);
		p.style.strokeDasharray = len + ' ' + (total + len);
		p.style.strokeDashoffset = len;
		var anim = p.animate([{ strokeDashoffset: len }, { strokeDashoffset: -total }],
			{ duration: Math.max(1600, total / (opts.speed || 0.12)), easing: 'cubic-bezier(.45,.05,.55,.95)' });
		return new Promise(function (res) { anim.onfinish = function () { p.remove(); res(); }; });
	};
	// Keep up to `max` runners alive at random intervals while the element is on screen.
	var runnerLoop = function (el, svg, pick, opts) {
		var live = 0, on = false;
		var spawn = function () {
			if (!on || live >= opts.max) { return; }
			live++;
			runDash(svg, pick(), opts).then(function () { live--; setTimeout(spawn, rand(opts.gap[0], opts.gap[1])); });
		};
		whenVisible(el, function (v) {
			on = v;
			if (v) { for (var i = live; i < opts.max; i++) { setTimeout(spawn, rand(0, opts.gap[1])); } }
		});
	};

	if (!reduceMotion && window.Element && Element.prototype.animate) {
		/* Industries: six lines that turn a corner (redrawn cleanly from the Figma geometry) */
		$$('.lv-flow').forEach(function (el) {
			var W = 540, H = 320, lines = [];
			for (var k = 0; k < 6; k++) {
				var yt = 20 + 22 * k, xv = 336 - 22 * k, yb = 286 - 22 * k;
				var R = Math.min(36, (yb - yt) / 2);
				lines.push('M-40 ' + yt + 'H' + (xv - R) + 'A' + R + ' ' + R + ' 0 0 1 ' + xv + ' ' + (yt + R) +
					'V' + (yb - R) + 'A' + R + ' ' + R + ' 0 0 0 ' + (xv + R) + ' ' + yb + 'H' + (W + 20));
			}
			var svg = mk('svg', { viewBox: '0 0 ' + W + ' ' + H, preserveAspectRatio: 'xMaxYMin meet', 'class': 'lv-svgfx', 'aria-hidden': 'true' });
			lines.forEach(function (d) { mk('path', { d: d, fill: 'none', stroke: 'rgba(12,43,75,0.13)', 'stroke-width': 1.6 }, svg); });
			el.style.backgroundImage = 'none';
			el.classList.add('lv-has-svg');
			el.appendChild(svg);
			runnerLoop(el, svg, function () { return lines[Math.floor(Math.random() * lines.length)]; },
				{ max: 3, gap: [250, 1800], len: 90, width: 2.2, speed: 0.16 });
		});

		/* Why Levanto: truchet flow grid (4x3 tiles of 160px, lanes at r = 60/80/100) */
		var TYPES = [['TL', 'TL', 'TR', 'TL'], ['TR', 'TL', 'TR', 'TR'], ['TL', 'TR', 'TR', 'TL']];
		var tileType = function (c, r) { return TYPES[((r % 3) + 3) % 3][((c % 4) + 4) % 4]; };
		var cornersOf = function (c, r) {
			var x = c * 160, y = r * 160;
			return tileType(c, r) === 'TL' ? [[x, y], [x + 160, y + 160]] : [[x + 160, y], [x, y + 160]];
		};
		var arcD = function (C, S, E, R) {
			var cross = (S[0] - C[0]) * (E[1] - C[1]) - (S[1] - C[1]) * (E[0] - C[0]);
			return 'A' + R + ' ' + R + ' 0 0 ' + (cross > 0 ? 1 : 0) + ' ' + E[0] + ' ' + E[1];
		};
		$$('.lv-flowgrid').forEach(function (el) {
			var box = el.getBoundingClientRect();
			var cols = Math.ceil(box.width / 160) + 5, rows = Math.ceil(box.height / 160) + 4;
			var svg = mk('svg', { width: '100%', height: '100%', 'class': 'lv-svgfx', 'aria-hidden': 'true' });
			var g = mk('g', { 'class': 'lv-drift' }, svg);
			var d = '';
			for (var r = 0; r < rows; r++) {
				for (var c = 0; c < cols; c++) {
					cornersOf(c, r).forEach(function (C) {
						var sx = C[0] === c * 160 ? 1 : -1, sy = C[1] === r * 160 ? 1 : -1;
						[60, 80, 100].forEach(function (R) {
							d += 'M' + (C[0] + sx * R) + ' ' + C[1] + 'A' + R + ' ' + R + ' 0 0 ' + (sx * sy > 0 ? 1 : 0) + ' ' + C[0] + ' ' + (C[1] + sy * R);
						});
					});
				}
			}
			mk('path', { d: d, fill: 'none', stroke: '#164974', 'stroke-width': 4, opacity: 0.35 }, g);
			// static flame accents from the design (tile 2-2 of every 640x480 block)
			for (var br = 0; br < rows; br += 3) {
				for (var bc = 0; bc < cols; bc += 4) {
					var ox = bc * 160 + 160, oy = br * 160 + 160;
					mk('path', { d: 'M' + (ox + 80) + ' ' + oy + 'A80 80 0 0 1 ' + ox + ' ' + (oy + 80), fill: 'none', stroke: '#b74712', 'stroke-width': 4, opacity: 0.4 }, g);
				}
			}
			// Random route: follow one lane through consecutive tiles.
			var route = function () {
				var c = 1 + Math.floor(Math.random() * (cols - 3)), r = 1 + Math.floor(Math.random() * (rows - 3));
				var R = [60, 80, 100][Math.floor(Math.random() * 3)];
				var C = cornersOf(c, r)[Math.floor(Math.random() * 2)];
				var sx = C[0] === c * 160 ? 1 : -1, sy = C[1] === r * 160 ? 1 : -1;
				var P = [C[0] + sx * R, C[1]];                 // start on the horizontal edge
				var out = 'M' + P[0] + ' ' + P[1];
				for (var step = 0; step < 9; step++) {
					var E = P[1] === C[1] ? [C[0], C[1] + sy * R] : [C[0] + sx * R, C[1]];
					out += arcD(C, P, E, R);
					// cross into the neighbouring tile through the edge E lies on
					if (E[0] === C[0]) { c += (E[0] === c * 160) ? -1 : 1; } else { r += (E[1] === r * 160) ? -1 : 1; }
					if (c < 0 || r < 0 || c >= cols || r >= rows) { break; }
					var next = null;
					cornersOf(c, r).forEach(function (K) {
						var onEdge = (K[0] === E[0] || K[1] === E[1]);
						var dist = Math.abs(K[0] - E[0]) + Math.abs(K[1] - E[1]);
						if (onEdge && (dist === 60 || dist === 80 || dist === 100)) { next = { K: K, R: dist }; }
					});
					if (!next) { break; }
					C = next.K; R = next.R; P = E;
					sx = C[0] === c * 160 ? 1 : -1; sy = C[1] === r * 160 ? 1 : -1;
				}
				return out;
			};
			el.style.backgroundImage = 'none';
			el.classList.add('lv-has-svg');
			el.appendChild(svg);
			runnerLoop(el, g, route, { max: 4, gap: [200, 1500], len: 140, width: 4, speed: 0.2 });
		});
	}

	/* ---------- 9c. Clean outline filter for the footer wordmark ---------- */
	var marks = $$('.lv-wordmark');
	if (marks.length) {
		var defs = mk('svg', { width: 0, height: 0, 'aria-hidden': 'true', style: 'position:absolute;width:0;height:0;overflow:hidden' });
		var f = mk('filter', { id: 'lv-outline', x: '-2%', y: '-10%', width: '104%', height: '120%', 'color-interpolation-filters': 'sRGB' }, defs);
		mk('feMorphology', { 'in': 'SourceGraphic', operator: 'dilate', radius: 1.3, result: 'grow' }, f);
		mk('feComposite', { 'in': 'grow', in2: 'SourceAlpha', operator: 'out', result: 'ring' }, f);
		mk('feColorMatrix', { 'in': 'SourceGraphic', type: 'matrix', values: '1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 0.07 0', result: 'tint' }, f);
		var merge = mk('feMerge', {}, f);
		mk('feMergeNode', { 'in': 'tint' }, merge);
		mk('feMergeNode', { 'in': 'ring' }, merge);
		document.body.appendChild(defs);
		marks.forEach(function (m) { m.classList.add('lv-outlined'); });
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
