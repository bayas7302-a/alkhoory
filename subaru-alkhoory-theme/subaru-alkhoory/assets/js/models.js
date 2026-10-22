/**
 * Subaru model components (vanilla JS, no dependencies). Markup: template-parts/configurator.php
 * and template-parts/gallery.php; data: inc/subaru-models.php (passed as JSON).
 *
 * 1. Configurator: exterior colour swatches with cross-fade, ?colour= in the URL, 360° view
 *    (drag / swipe / arrow keys, frames lazy-loaded on first use), interior grade + trim swatches,
 *    grade cards on the page select their interior.
 * 2. Galleries: model filter (masonry) and "all photos" lightbox (feature layout), using the theme lightbox.
 */
(function () {
	'use strict';

	var d = document;
	var w = window;
	var cfg = w.SubaruModels || {};
	var t = cfg.i18n || {};
	var reduced = w.matchMedia('(prefers-reduced-motion: reduce)');

	function $(s, c) { return (c || d).querySelector(s); }
	function $$(s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); }
	function fmt(s) {
		var a = arguments;
		var i = 0;
		return String(s || '').replace(/%(\d+\$)?s/g, function (m, n) { return a[n ? parseInt(n, 10) : ++i]; });
	}
	function isRtl(el) { return getComputedStyle(el).direction === 'rtl'; }
	function norm(s) { return String(s || '').toLowerCase().replace(/[^a-z0-9]/g, ''); }

	/* Cross-fade a stage to a new image without layout shift (the stage has a fixed aspect ratio). */
	function swapImage(stage, src, srcset, alt) {
		var layers = $('.sa-cfg__layers', stage);
		var cur = $('.sa-cfg__img.is-on', layers);
		if (cur && cur.getAttribute('src') === src) { return; }
		var img = d.createElement('img');
		img.className = 'sa-cfg__img';
		img.decoding = 'async';
		img.alt = alt || '';
		if (cur && cur.sizes) { img.sizes = cur.sizes; }
		if (srcset) { img.srcset = srcset; }
		img.src = src;
		layers.appendChild(img);
		function show() {
			img.classList.add('is-on');
			$$('.sa-cfg__img', layers).forEach(function (o) {
				if (o === img) { return; }
				o.classList.remove('is-on');
				o.classList.add('is-off');
				setTimeout(function () { if (o.parentNode) { o.parentNode.removeChild(o); } }, reduced.matches ? 0 : 450);
			});
		}
		if (img.complete) { show(); } else { img.addEventListener('load', show, { once: true }); img.addEventListener('error', show, { once: true }); }
	}

	function preload(src, srcset) {
		var i = new Image();
		if (srcset) { i.srcset = srcset; i.sizes = '(max-width: 1250px) 100vw, 1250px'; }
		i.src = src;
	}

	/* Button group with roving focus: arrows move and select, Home/End jump. */
	function rovingKey(e, items, select) {
		var i = items.indexOf(d.activeElement);
		if (i < 0) { return; }
		var back = isRtl(e.currentTarget) ? 'ArrowRight' : 'ArrowLeft';
		var fwd = isRtl(e.currentTarget) ? 'ArrowLeft' : 'ArrowRight';
		var n = -1;
		if (e.key === fwd || e.key === 'ArrowDown') { n = (i + 1) % items.length; }
		else if (e.key === back || e.key === 'ArrowUp') { n = (i - 1 + items.length) % items.length; }
		else if (e.key === 'Home') { n = 0; }
		else if (e.key === 'End') { n = items.length - 1; }
		if (n < 0) { return; }
		e.preventDefault();
		items[n].focus();
		select(n);
	}
	function rovingGroup(group, items, select) {
		group.addEventListener('keydown', function (e) { rovingKey(e, items, select); });
	}
	function setPressed(items, n) {
		items.forEach(function (b, k) {
			b.setAttribute('aria-pressed', k === n ? 'true' : 'false');
			b.setAttribute('tabindex', k === n ? '0' : '-1');
		});
	}

	/* ------------------------------------------------------------ configurator */
	function initConfigurator(root) {
		var dataEl = $('.sa-cfg__data', root);
		if (!dataEl) { return; }
		var data;
		try { data = JSON.parse(dataEl.textContent); } catch (e) { return; }
		var colours = data.colours || [];
		var ext = $('[data-stage="ext"]', root);
		var extName = $('.sa-cfg__name', ext);
		var swatches = $$('.sa-cfg__swatch', root);
		var current = Math.max(0, colours.findIndex(function (c) { return c.key === data.default; }));

		/* --- 360° view --- */
		var spinBtn = $('.sa-cfg__spin-btn', ext);
		var hint = $('.sa-cfg__hint', ext);
		var progress = $('.sa-cfg__progress', ext);
		var spin = { on: false, frame: 0, cache: {}, img: null, used: false };

		function framesOf(i) { return (colours[i] && colours[i].frames) || []; }
		function loadFrames(i) {
			var list = framesOf(i);
			var key = colours[i].key;
			if (spin.cache[key]) { return spin.cache[key]; }
			var entry = { imgs: [], loaded: 0, total: list.length };
			spin.cache[key] = entry;
			// Current frame first, then outwards, so dragging works while the rest loads.
			var order = list.map(function (u, k) { return k; }).sort(function (a, b) {
				var n = list.length;
				var da = Math.min(Math.abs(a - spin.frame), n - Math.abs(a - spin.frame));
				var db = Math.min(Math.abs(b - spin.frame), n - Math.abs(b - spin.frame));
				return da - db;
			});
			order.forEach(function (k) {
				var im = new Image();
				im.decoding = 'async';
				im.onload = im.onerror = function () {
					im.dataset.done = '1';
					entry.loaded++;
					if (colours[current].key === key) { updateProgress(); if (k === spin.frame) { drawFrame(); } }
				};
				im.src = list[k];
				entry.imgs[k] = im;
			});
			return entry;
		}
		function updateProgress() {
			if (!progress) { return; }
			var e = spin.cache[colours[current].key];
			if (!spin.on || !e || e.loaded >= e.total) { progress.hidden = true; return; }
			progress.hidden = false;
			progress.firstElementChild.style.width = Math.round((e.loaded / e.total) * 100) + '%';
			progress.setAttribute('aria-label', t.loading || 'Loading 360° view');
		}
		function drawFrame() {
			var e = spin.cache[colours[current].key];
			if (!e || !spin.img) { return; }
			var n = e.total;
			// Show the nearest loaded frame while the requested one is still loading.
			for (var off = 0; off < n; off++) {
				var k = (spin.frame + off) % n;
				if (e.imgs[k] && e.imgs[k].dataset.done) { spin.img.src = e.imgs[k].src; break; }
			}
			ext.setAttribute('aria-valuenow', String(spin.frame + 1));
			ext.setAttribute('aria-valuetext', fmt(t.frame || 'Frame %1$s of %2$s', spin.frame + 1, n));
		}
		function setSpinAvailability() {
			if (!spinBtn) { return; }
			var has = framesOf(current).length > 1;
			spinBtn.hidden = false;
			spinBtn.disabled = !has;
			spinBtn.title = has ? '' : (t.spinMissing || '');
			if (!has && spin.on) { toggleSpin(false); }
		}
		function toggleSpin(on) {
			if (!spinBtn) { return; }
			spin.on = on;
			spinBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
			$('span', spinBtn).textContent = on ? (t.spinOff || 'Exit 360° view') : (t.spinOn || '360° view');
			ext.classList.toggle('is-spinning', on);
			if (on) {
				var n = framesOf(current).length;
				spin.frame = Math.min(spin.frame, n - 1);
				if (!spin.img) {
					spin.img = d.createElement('img');
					spin.img.className = 'sa-cfg__spin';
					spin.img.alt = '';
					spin.img.draggable = false;
					if (colours[current].fw) { spin.img.width = colours[current].fw; spin.img.height = colours[current].fh; }
					$('.sa-cfg__layers', ext).appendChild(spin.img);
				}
				spin.img.hidden = false;
				ext.setAttribute('tabindex', '0');
				ext.setAttribute('role', 'slider');
				ext.setAttribute('aria-label', fmt(t.spinLabel || '360° view of the Subaru %s.', data.name));
				ext.setAttribute('aria-valuemin', '1');
				ext.setAttribute('aria-valuemax', String(n));
				loadFrames(current);
				drawFrame();
				updateProgress();
				if (hint && !spin.used) { hint.hidden = false; }
			} else {
				if (spin.img) { spin.img.hidden = true; }
				['tabindex', 'role', 'aria-label', 'aria-valuemin', 'aria-valuemax', 'aria-valuenow', 'aria-valuetext'].forEach(function (a) { ext.removeAttribute(a); });
				if (hint) { hint.hidden = true; }
				if (progress) { progress.hidden = true; }
			}
		}
		function step(delta) {
			var n = framesOf(current).length;
			if (!n) { return; }
			spin.frame = ((spin.frame + delta) % n + n) % n;
			drawFrame();
			if (!spin.used) { spin.used = true; if (hint) { hint.hidden = true; } }
		}
		if (spinBtn) {
			spinBtn.addEventListener('click', function () { toggleSpin(!spin.on); });
			var drag = null;
			ext.addEventListener('pointerdown', function (e) {
				if (!spin.on || e.target.closest('button')) { return; }
				drag = { x: e.clientX, acc: 0 };
				ext.setPointerCapture(e.pointerId);
				ext.classList.add('is-dragging');
			});
			ext.addEventListener('pointermove', function (e) {
				if (!drag) { return; }
				var n = framesOf(current).length;
				// One full drag across the stage = one rotation.
				var px = Math.max(6, ext.clientWidth / n);
				drag.acc += e.clientX - drag.x;
				drag.x = e.clientX;
				var dir = isRtl(ext) ? 1 : -1;
				while (Math.abs(drag.acc) >= px) {
					step(drag.acc > 0 ? dir : -dir);
					drag.acc += drag.acc > 0 ? -px : px;
				}
			});
			function endDrag() { drag = null; ext.classList.remove('is-dragging'); }
			ext.addEventListener('pointerup', endDrag);
			ext.addEventListener('pointercancel', endDrag);
			ext.addEventListener('keydown', function (e) {
				if (!spin.on || e.target !== ext) { return; }
				var fwd = isRtl(ext) ? 'ArrowLeft' : 'ArrowRight';
				var back = isRtl(ext) ? 'ArrowRight' : 'ArrowLeft';
				if (e.key === fwd) { e.preventDefault(); step(1); }
				else if (e.key === back) { e.preventDefault(); step(-1); }
				else if (e.key === 'Home') { e.preventDefault(); spin.frame = 0; drawFrame(); }
				else if (e.key === 'Escape') { toggleSpin(false); spinBtn.focus(); }
			});
		}

		/* --- colours --- */
		function selectColour(i, fromUrl) {
			if (!colours[i]) { return; }
			var prevN = framesOf(current).length;
			current = i;
			var c = colours[i];
			setPressed(swatches, i);
			extName.textContent = c.name;
			swapImage(ext, c.src, c.srcset, c.alt);
			if (spinBtn) {
				var n = framesOf(i).length;
				// Keep the viewing angle when the new colour has a different frame count.
				if (prevN && n && prevN !== n) { spin.frame = Math.round(spin.frame * n / prevN) % n; }
				setSpinAvailability();
				if (spin.on) {
					ext.setAttribute('aria-valuemax', String(n));
					loadFrames(i);
					drawFrame();
					updateProgress();
				}
			}
			if (!fromUrl && w.history && w.history.replaceState) {
				try {
					var u = new URL(w.location.href);
					u.searchParams.set('colour', c.key);
					w.history.replaceState(w.history.state, '', u.pathname + u.search + u.hash);
				} catch (e) { /* ignore */ }
			}
		}
		swatches.forEach(function (b, k) {
			b.addEventListener('click', function () { selectColour(k); });
			['pointerenter', 'focus'].forEach(function (ev) {
				b.addEventListener(ev, function () { if (colours[k]) { preload(colours[k].src, colours[k].srcset); } }, { once: true });
			});
		});
		rovingGroup($('.sa-cfg__swatches', ext.parentNode), swatches, selectColour);
		try {
			var want = new URL(w.location.href).searchParams.get('colour');
			var wi = want ? colours.findIndex(function (c) { return c.key.toLowerCase() === want.toLowerCase(); }) : -1;
			if (wi >= 0 && wi !== current) { selectColour(wi, true); }
		} catch (e) { /* ignore */ }
		setSpinAvailability();

		/* --- exterior / interior tabs --- */
		var tabs = $$('.sa-cfg__tab', root);
		var panels = tabs.map(function (tb) { return d.getElementById(tb.getAttribute('aria-controls')); });
		function selectTab(n, focus) {
			tabs.forEach(function (tb, k) {
				tb.setAttribute('aria-selected', k === n ? 'true' : 'false');
				tb.setAttribute('tabindex', k === n ? '0' : '-1');
				if (panels[k]) { panels[k].hidden = k !== n; }
			});
			if (focus) { tabs[n].focus(); }
			if (n !== 0 && spin.on) { toggleSpin(false); }
		}
		tabs.forEach(function (tb, k) { tb.addEventListener('click', function () { selectTab(k); }); });
		if (tabs.length) {
			tabs[0].parentNode.addEventListener('keydown', function (e) {
				var i = tabs.indexOf(d.activeElement);
				if (i < 0) { return; }
				var fwd = isRtl(root) ? 'ArrowLeft' : 'ArrowRight';
				var back = isRtl(root) ? 'ArrowRight' : 'ArrowLeft';
				if (e.key === fwd) { e.preventDefault(); selectTab((i + 1) % tabs.length, true); }
				else if (e.key === back) { e.preventDefault(); selectTab((i - 1 + tabs.length) % tabs.length, true); }
			});
		}

		/* --- interior --- */
		var sets = data.interiors || [];
		var intStage = $('[data-stage="int"]', root);
		var trimBox = $('.sa-cfg__swatches--trim', root);
		var gradeBtns = $$('.sa-cfg__grade', root);
		var setIdx = 0;
		if (trimBox) { trimBox.addEventListener('keydown', function (e) { rovingKey(e, $$('.sa-cfg__swatch', trimBox), selectView); }); }
		function selectView(v) {
			var set = sets[setIdx];
			var view = set && set.views[v];
			if (!view) { return; }
			setPressed($$('.sa-cfg__swatch', trimBox), v);
			$('.sa-cfg__name', intStage).textContent = view.label;
			swapImage(intStage, view.src, view.srcset, view.alt);
		}
		function buildTrims() {
			var set = sets[setIdx];
			trimBox.innerHTML = '';
			trimBox.hidden = !set || set.views.length < 2;
			if (!set) { return []; }
			var btns = set.views.map(function (v, k) {
				var b = d.createElement('button');
				b.type = 'button';
				b.className = 'sa-cfg__swatch sa-cfg__swatch--' + (v.trim === 'view' ? 'view' : 'trim');
				if (v.hex) { b.style.setProperty('--sw', v.hex); }
				b.setAttribute('aria-label', v.label);
				b.title = v.label;
				if (v.trim === 'view') {
					b.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 15a8 8 0 0 1 16 0M12 15l3-4M7 18h10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
				}
				b.addEventListener('click', function () { selectView(k); });
				b.addEventListener('pointerenter', function () { preload(v.src, v.srcset); }, { once: true });
				trimBox.appendChild(b);
				return b;
			});
			setPressed(btns, 0);
			return btns;
		}
		function selectSet(i) {
			if (!sets[i]) { return; }
			setIdx = i;
			setPressed(gradeBtns, i);
			buildTrims();
			selectView(0);
		}
		if (intStage && sets.length) {
			gradeBtns.forEach(function (b, k) { b.addEventListener('click', function () { selectSet(k); }); });
			if (gradeBtns.length) { rovingGroup(gradeBtns[0].parentNode, gradeBtns, selectSet); }
			buildTrims();
			// Grade cards elsewhere on the page (matched by their heading) select their interior.
			var grades = data.grades || [];
			$$('.sub-card').forEach(function (card) {
				var h = $('h2, h3, h4', card);
				if (!h) { return; }
				var g = grades.filter(function (x) { return norm(x.name) === norm(h.textContent); })[0];
				var si = g ? sets.findIndex(function (s) { return s.id === g.interior; }) : -1;
				if (si < 0) { return; }
				card.addEventListener('click', function (e) {
					if (e.target.closest('a, button')) { return; }
					selectSet(si);
				});
			});
		}
	}

	/* --------------------------------------------------------------- galleries */
	function initGallery(root) {
		var api = w.SubaruTheme && w.SubaruTheme.api;
		var items = $$('.sa-gal__item', root);
		var filters = $$('.sa-gal__filter', root);
		var count = $('.sa-gal__count', root);
		if (filters.length) {
			var apply = function (n) {
				var f = filters[n].getAttribute('data-filter');
				var shown = 0;
				setPressed(filters, n);
				filters.forEach(function (b) { b.removeAttribute('tabindex'); });
				items.forEach(function (it) {
					var on = f === 'all' || it.getAttribute('data-model') === f;
					it.hidden = !on;
					if (on) { shown++; }
				});
				if (count) { count.textContent = fmt(t.showing || 'Showing %s photos', shown); }
			};
			filters.forEach(function (b, k) { b.addEventListener('click', function () { apply(k); }); });
		}
		// The feature layout steps through every photo of the model (including those not shown as tiles);
		// the masonry layout through the photos left by the current filter.
		var all = root.classList.contains('sa-gal--feature');
		if (api && api.lightbox) {
			root.addEventListener('click', function (e) {
				var a = e.target.closest('.sa-gal__link');
				if (!a || e.ctrlKey || e.metaKey || e.shiftKey) { return; }
				e.preventDefault();
				var imgs = items.filter(function (it) { return all || !it.hidden; }).map(function (it) { return $('img', it); });
				api.lightbox(imgs, Math.max(0, imgs.indexOf($('img', a))), a);
			});
		}
	}

	function boot() {
		$$('[data-sa-cfg]').forEach(initConfigurator);
		$$('[data-sa-gal]').forEach(initGallery);
	}
	if (d.readyState === 'loading') { d.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
