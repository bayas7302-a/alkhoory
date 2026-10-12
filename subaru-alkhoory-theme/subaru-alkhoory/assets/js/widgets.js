/**
 * Subaru Al Khoory — classic Elementor widgets: Colour Configurator (crossfade + 360° drag),
 * Feature Hotspots, and editor re-initialisation for Hero Carousel / Spec Strip.
 */
(function () {
	'use strict';

	var d = document;
	var w = window;

	function $$(s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); }

	/* Colour configurator */
	function initConfigurator(root) {
		if (root.__saCC) { return; }
		root.__saCC = true;
		var frames = $$('.sa-cc__frame', root);
		var swatches = $$('.sa-cc__swatch', root);
		var nameEl = root.querySelector('.sa-cc__name');

		function select(i, focus) {
			i = (i + swatches.length) % swatches.length;
			swatches.forEach(function (s, k) {
				var on = k === i;
				s.classList.toggle('is-active', on);
				s.setAttribute('aria-checked', on ? 'true' : 'false');
				s.tabIndex = on ? 0 : -1;
			});
			frames.forEach(function (f, k) {
				var on = k === i;
				f.classList.toggle('is-active', on);
				if (on) { f.removeAttribute('aria-hidden'); } else { f.setAttribute('aria-hidden', 'true'); }
			});
			if (nameEl) { nameEl.textContent = swatches[i].getAttribute('data-name'); }
			if (focus) { swatches[i].focus(); }
		}
		swatches.forEach(function (s, k) {
			s.addEventListener('click', function () { select(k); });
			s.addEventListener('keydown', function (e) {
				if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); select(k + 1, true); }
				if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); select(k - 1, true); }
				if (e.key === 'Home') { e.preventDefault(); select(0, true); }
				if (e.key === 'End') { e.preventDefault(); select(swatches.length - 1, true); }
			});
		});

		// 360° frame sequences: drag / swipe horizontally to rotate; arrow keys when focused.
		frames.forEach(function (fig) {
			var list;
			try { list = JSON.parse(fig.getAttribute('data-frames') || '[]'); } catch (e) { list = []; }
			var img = fig.querySelector('img');
			if (list.length < 2 || !img) { return; }
			var idx = 0;
			var loaded = false;
			fig.tabIndex = 0;
			fig.setAttribute('role', 'img');
			fig.setAttribute('aria-label', (img.alt || '') + ' — 360°');
			function preload() {
				if (loaded) { return; }
				loaded = true;
				list.forEach(function (src) { var p = new Image(); p.src = src; });
			}
			function show(n) {
				idx = ((n % list.length) + list.length) % list.length;
				img.src = list[idx];
			}
			var startX = 0;
			var startIdx = 0;
			var dragging = false;
			fig.addEventListener('pointerdown', function (e) {
				preload();
				dragging = true;
				startX = e.clientX;
				startIdx = idx;
				fig.classList.add('is-dragging');
				if (fig.setPointerCapture) { fig.setPointerCapture(e.pointerId); }
			});
			fig.addEventListener('pointermove', function (e) {
				if (!dragging) { return; }
				var step = Math.max(6, fig.offsetWidth / (list.length * 1.5));
				var n = startIdx - Math.round((e.clientX - startX) / step);
				if (n !== idx) { show(n); fig.classList.add('was-dragged'); }
			});
			function end() { dragging = false; fig.classList.remove('is-dragging'); }
			fig.addEventListener('pointerup', end);
			fig.addEventListener('pointercancel', end);
			fig.addEventListener('mouseenter', preload);
			fig.addEventListener('keydown', function (e) {
				if (e.key === 'ArrowRight') { e.preventDefault(); preload(); show(idx + 1); fig.classList.add('was-dragged'); }
				if (e.key === 'ArrowLeft') { e.preventDefault(); preload(); show(idx - 1); fig.classList.add('was-dragged'); }
			});
		});
	}

	/* Feature hotspots */
	function initHotspots(root) {
		if (root.__saHot) { return; }
		root.__saHot = true;
		var spots = $$('.sa-hot__spot', root);
		function closeAll(except) {
			spots.forEach(function (s) {
				if (s !== except) { s.classList.remove('is-open'); s.querySelector('.sa-hot__dot').setAttribute('aria-expanded', 'false'); }
			});
		}
		spots.forEach(function (spot) {
			var btn = spot.querySelector('.sa-hot__dot');
			function set(on) {
				closeAll(spot);
				spot.classList.toggle('is-open', on);
				btn.setAttribute('aria-expanded', on ? 'true' : 'false');
			}
			btn.addEventListener('click', function (e) { e.stopPropagation(); set(!spot.classList.contains('is-open')); });
			if (w.matchMedia('(hover: hover)').matches) {
				spot.addEventListener('mouseenter', function () { set(true); });
				spot.addEventListener('mouseleave', function () { set(false); });
			}
			btn.addEventListener('keydown', function (e) { if (e.key === 'Escape') { set(false); } });
		});
		d.addEventListener('click', function (e) { if (!root.contains(e.target)) { closeAll(); } });
	}

	function initScope(scope) {
		scope = scope || d;
		$$('[data-sa-cc]', scope).forEach(initConfigurator);
		$$('[data-sa-hotspots]', scope).forEach(initHotspots);
		var api = w.SubaruTheme && w.SubaruTheme.api;
		if (api && api.init && scope !== d) { api.init(scope); }
	}

	if (d.readyState !== 'loading') { initScope(d); } else { d.addEventListener('DOMContentLoaded', function () { initScope(d); }); }

	// Elementor editor / dynamic rendering.
	function hookElementor() {
		if (!w.elementorFrontend || !w.elementorFrontend.hooks) { return; }
		['subaru-colour-configurator', 'subaru-hero-carousel', 'subaru-spec-strip', 'subaru-model-card', 'subaru-feature-hotspots'].forEach(function (name) {
			w.elementorFrontend.hooks.addAction('frontend/element_ready/' + name + '.default', function ($el) {
				initScope($el && $el[0] ? $el[0] : d);
			});
		});
	}
	if (w.elementorFrontend && w.elementorFrontend.hooks) { hookElementor(); } else { w.addEventListener('elementor/frontend/init', hookElementor); }
})();
