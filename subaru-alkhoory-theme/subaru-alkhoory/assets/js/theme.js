/**
 * Subaru Al Khoory — theme behaviours (vanilla JS, no dependencies).
 *
 * Works on top of Elementor V4 atomic markup: global class labels render as literal classes
 * (e.g. .sub-hero-carousel), e-tabs are driven by Elementor's Alpine handler (we click tabs or,
 * when Alpine is absent, toggle states ourselves). Elementor's own [data-interaction-id]
 * animations are never touched: we only add classes / CSS custom properties / `translate`.
 */
(function () {
	'use strict';

	var d = document;
	var w = window;
	var html = d.documentElement;
	var cfg = w.SubaruTheme || {};
	var t = cfg.i18n || {};
	var mqReduce = w.matchMedia('(prefers-reduced-motion: reduce)');
	var mqFine = w.matchMedia('(hover: hover) and (pointer: fine)');

	/* ------------------------------------------------------------------ utils */
	function $(s, c) { return (c || d).querySelector(s); }
	function $$(s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); }
	function reduced() { return mqReduce.matches || html.classList.contains('sa-rm'); }
	function clamp(v, a, b) { return Math.min(b, Math.max(a, v)); }
	function once(el, key) {
		if (!el || el['__sa_' + key]) { return false; }
		el['__sa_' + key] = true;
		return true;
	}
	function ready(fn) {
		if (d.readyState !== 'loading') { fn(); } else { d.addEventListener('DOMContentLoaded', fn); }
	}
	function slug(s) {
		return String(s || '').toLowerCase().normalize('NFKD').replace(/[̀-ͯ]/g, '')
			.replace(/&/g, ' and ').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
	}
	function isVisible(el) { return !!(el && (el.offsetWidth || el.offsetHeight || el.getClientRects().length)); }
	function focusables(root) {
		return $$('a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])', root).filter(isVisible);
	}
	function trapTab(e, root) {
		if (e.key !== 'Tab') { return; }
		var f = focusables(root);
		if (!f.length) { return; }
		var first = f[0];
		var last = f[f.length - 1];
		if (e.shiftKey && d.activeElement === first) { e.preventDefault(); last.focus(); }
		else if (!e.shiftKey && d.activeElement === last) { e.preventDefault(); first.focus(); }
	}
	var lockCount = 0;
	function lockScroll(on) {
		lockCount = Math.max(0, lockCount + (on ? 1 : -1));
		html.classList.toggle('sa-locked', lockCount > 0);
	}
	var rafScroll = [];
	var rafQueued = false;
	function onScrollFrame(fn) { rafScroll.push(fn); }
	function runScrollFrame() {
		rafQueued = false;
		for (var i = 0; i < rafScroll.length; i++) { rafScroll[i](); }
	}
	w.addEventListener('scroll', function () {
		if (!rafQueued) { rafQueued = true; w.requestAnimationFrame(runScrollFrame); }
	}, { passive: true });
	w.addEventListener('resize', function () {
		if (!rafQueued) { rafQueued = true; w.requestAnimationFrame(runScrollFrame); }
	});

	/* ------------------------------------------- 1. .sub-id-<name> → id="<name>" */
	function initIds(scope) {
		$$('[class*="sub-id-"]', scope).forEach(function (el) {
			if (el.id && !/^\d+$/.test(el.id)) { return; }
			var cls = Array.prototype.find.call(el.classList, function (c) { return c.indexOf('sub-id-') === 0 && c.length > 7; });
			if (!cls) { return; }
			var id = cls.slice(7);
			if (!d.getElementById(id)) { el.id = id; }
		});
	}

	/* ------------------------------------------------------------- 2. header */
	var header = $('#sa-header');
	var topbar = header ? $('.sa-topbar', header) : null;
	var mainbar = header ? $('.sa-mainbar', header) : null;
	var subnav = null;
	var H = { admin: 0, top: 0, nav: 84, lastY: 0, hidden: false, compact: false, lock: 0, hold: false };

	function measureHeader() {
		if (!header) { return; }
		var bar = $('#wpadminbar');
		var y = w.pageYOffset;
		H.admin = 0;
		if (bar) {
			H.admin = getComputedStyle(bar).position === 'fixed' ? bar.offsetHeight : Math.max(0, bar.offsetHeight - y);
		}
		H.top = topbar && isVisible(topbar) ? topbar.offsetHeight : 0;
		H.nav = mainbar ? mainbar.offsetHeight : 0;
		var s = html.style;
		s.setProperty('--sa-admin', H.admin + 'px');
		s.setProperty('--sa-topbar-h', H.top + 'px');
		s.setProperty('--sa-nav-h', H.nav + 'px');
		s.setProperty('--sa-header-h', (H.top + H.nav) + 'px');
		if (subnav) { s.setProperty('--sa-subnav-h', subnav.offsetHeight + 'px'); }
	}

	/** Bottom edge of the visible header (px from viewport top). */
	function headerBottom() {
		if (!header) { return 0; }
		if (H.hidden) { return H.admin; }
		if (H.compact) { return H.admin + H.nav; }
		return H.admin + Math.max(H.nav, H.top + H.nav - Math.max(0, w.pageYOffset));
	}

	function updateHeader() {
		if (!header) { return; }
		var y = Math.max(0, w.pageYOffset);
		var bar = $('#wpadminbar');
		if (bar && getComputedStyle(bar).position !== 'fixed') {
			H.admin = Math.max(0, bar.offsetHeight - y);
			html.style.setProperty('--sa-admin', H.admin + 'px');
		}
		var busy = H.hold || header.classList.contains('menu-open') || !!$('.sa-nav__item.is-open', header);
		var dy = y - H.lastY;
		H.compact = y > H.top + 2;
		if (H.lock) {
			H.hidden = false;
		} else if (!busy && y > H.top + H.nav + 160 && dy > 6) {
			H.hidden = true;
		} else if (dy < -6 || y <= H.top + H.nav) {
			H.hidden = false;
		}
		if (Math.abs(dy) > 6 || y <= H.top + H.nav) { H.lastY = y; }
		header.classList.toggle('is-top', y < 8);
		header.classList.toggle('is-compact', H.compact);
		header.classList.toggle('is-hidden', H.hidden);
		html.style.setProperty('--sa-offset', headerBottom() + 'px');
	}

	/** Detect pages whose first section is a hero → transparent header. */
	function detectHero() {
		var body = d.body;
		if (body.classList.contains('sa-hero-first')) { return; }
		var root = $$('[data-elementor-type]').filter(function (el) {
			var type = el.getAttribute('data-elementor-type');
			return type !== 'header' && type !== 'footer' && !el.closest('.sa-header, .sa-footer');
		})[0];
		if (!root) { return; }
		var el = root.firstElementChild;
		var depth = 0;
		while (el && depth < 4) {
			if (el.matches('.sub-hero, .sub-hero-carousel, [data-sa-hero], .elementor-widget-subaru-hero-carousel')) { break; }
			var cs = getComputedStyle(el);
			if (cs.backgroundImage && cs.backgroundImage !== 'none' && el.offsetHeight >= 460 && el.getBoundingClientRect().top < 260) { break; }
			el = el.firstElementChild;
			depth++;
		}
		if (!el || depth >= 4) { body.classList.add('sa-no-hero'); return; }
		// Only go transparent when the hero leaves room for the header above its content.
		var first = $$('h1, h2, h3, p, .e-button-base', el).filter(function (n) { return isVisible(n) && !n.closest('[role="tablist"]'); })[0];
		var room = first ? first.getBoundingClientRect().top - el.getBoundingClientRect().top : 0;
		var need = (topbar && isVisible(topbar) ? topbar.offsetHeight : 0) + (mainbar ? mainbar.offsetHeight : 84) + 8;
		if (room >= need) { body.classList.add('sa-hero-first'); } else { body.classList.add('sa-no-hero'); }
	}

	function initHeader() {
		if (!header) { return; }
		measureHeader();
		H.lastY = w.pageYOffset;
		updateHeader();
		onScrollFrame(updateHeader);
		w.addEventListener('resize', function () { measureHeader(); updateHeader(); });
		if (w.ResizeObserver) { new ResizeObserver(function () { measureHeader(); }).observe(header); }

		if (mqFine.matches) {
			header.addEventListener('mouseenter', function () { header.classList.add('is-hovered'); });
			header.addEventListener('mouseleave', function () { if (!$('.sa-nav__item.is-open', header)) { header.classList.remove('is-hovered'); } });
		}

		// Dropdowns & mega menu.
		var items = $$('.sa-nav__item.has-children', header);
		function closeAll(except) {
			items.forEach(function (li) {
				if (li !== except && li.classList.contains('is-open')) {
					li.classList.remove('is-open');
					var b = $('.sa-nav__toggle', li);
					if (b) { b.setAttribute('aria-expanded', 'false'); }
				}
			});
			if (!except && !header.matches(':hover')) { header.classList.remove('is-hovered'); }
		}
		function open(li) {
			closeAll(li);
			li.classList.add('is-open');
			header.classList.add('is-hovered');
			var b = $('.sa-nav__toggle', li);
			if (b) { b.setAttribute('aria-expanded', 'true'); }
		}
		items.forEach(function (li) {
			var timer = 0;
			var btn = $('.sa-nav__toggle', li);
			if (mqFine.matches) {
				li.addEventListener('mouseenter', function () { clearTimeout(timer); timer = setTimeout(function () { open(li); }, 60); });
				li.addEventListener('mouseleave', function () { clearTimeout(timer); timer = setTimeout(function () { li.classList.remove('is-open'); if (btn) { btn.setAttribute('aria-expanded', 'false'); } }, 160); });
			}
			if (btn) {
				btn.addEventListener('click', function (e) {
					e.preventDefault();
					if (li.classList.contains('is-open')) { closeAll(); } else { open(li); }
				});
			}
			li.addEventListener('focusout', function (e) {
				if (!li.contains(e.relatedTarget)) { li.classList.remove('is-open'); if (btn) { btn.setAttribute('aria-expanded', 'false'); } }
			});
			li.addEventListener('keydown', function (e) {
				if (e.key === 'Escape' && li.classList.contains('is-open')) {
					closeAll();
					if (btn) { btn.focus(); }
				}
				if (e.key === 'ArrowDown' && e.target === btn) {
					e.preventDefault();
					open(li);
					var first = $('.sa-dropdown a, .sa-mega a', li);
					if (first) { first.focus(); }
				}
			});
		});
		d.addEventListener('click', function (e) { if (!e.target.closest('.sa-nav__item.has-children')) { closeAll(); } });

		initSearch();
		initMobileMenu();
	}

	function initSearch() {
		var box = $('#sa-search');
		var toggle = $('.sa-search-toggle');
		if (!box || !toggle) { return; }
		var input = $('input[type="search"]', box);
		function openSearch() {
			box.hidden = false;
			void box.offsetWidth;
			box.classList.add('is-open');
			toggle.setAttribute('aria-expanded', 'true');
			lockScroll(true);
			setTimeout(function () { if (input) { input.focus(); } }, 60);
		}
		function closeSearch() {
			if (box.hidden) { return; }
			box.classList.remove('is-open');
			toggle.setAttribute('aria-expanded', 'false');
			lockScroll(false);
			setTimeout(function () { box.hidden = true; }, reduced() ? 0 : 450);
			toggle.focus();
		}
		toggle.addEventListener('click', openSearch);
		$$('[data-sa-close]', box).forEach(function (b) { b.addEventListener('click', closeSearch); });
		box.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { closeSearch(); }
			trapTab(e, box);
		});
	}

	function initMobileMenu() {
		var menu = $('#sa-mmenu');
		var burger = $('.sa-burger');
		if (!menu || !burger) { return; }
		var isOpen = false;
		function setOpen(on) {
			if (on === isOpen) { return; }
			isOpen = on;
			burger.setAttribute('aria-expanded', on ? 'true' : 'false');
			header.classList.toggle('menu-open', on);
			lockScroll(on);
			if (on) {
				menu.hidden = false;
				void menu.offsetWidth;
				menu.classList.add('is-open');
				H.hidden = false;
				updateHeader();
			} else {
				menu.classList.remove('is-open');
				setTimeout(function () { if (!isOpen) { menu.hidden = true; } }, reduced() ? 0 : 700);
			}
		}
		burger.addEventListener('click', function () { setOpen(!isOpen); });
		$$('.sa-mnav__acc', menu).forEach(function (btn) {
			btn.addEventListener('click', function () {
				var li = btn.closest('.sa-mnav__item');
				var on = !li.classList.contains('is-expanded');
				$$('.sa-mnav__item.is-expanded', menu).forEach(function (o) {
					if (o !== li) { o.classList.remove('is-expanded'); $('.sa-mnav__acc', o).setAttribute('aria-expanded', 'false'); }
				});
				li.classList.toggle('is-expanded', on);
				btn.setAttribute('aria-expanded', on ? 'true' : 'false');
			});
		});
		menu.addEventListener('click', function (e) { if (e.target.closest('a')) { setOpen(false); } });
		d.addEventListener('keydown', function (e) {
			if (!isOpen) { return; }
			if (e.key === 'Escape') { setOpen(false); burger.focus(); }
			if (e.key === 'Tab') {
				// Trap between the header (logo, burger) and the menu.
				var f = focusables(header).concat(focusables(menu));
				var first = f[0];
				var last = f[f.length - 1];
				if (e.shiftKey && d.activeElement === first) { e.preventDefault(); last.focus(); }
				else if (!e.shiftKey && d.activeElement === last) { e.preventDefault(); first.focus(); }
			}
		});
		w.addEventListener('resize', function () { if (isOpen && w.innerWidth > 1140) { setOpen(false); } });
	}

	/* ------------------------------------------------- 3. progress + to-top */
	function initProgress() {
		var totop = $('.sa-totop');
		function update() {
			var max = d.documentElement.scrollHeight - w.innerHeight;
			var p = max > 0 ? clamp(w.pageYOffset / max, 0, 1) : 0;
			html.style.setProperty('--sa-progress', p.toFixed(4));
			if (totop) { totop.classList.toggle('is-visible', w.pageYOffset > 640); }
		}
		update();
		onScrollFrame(update);
		if (totop) {
			totop.addEventListener('click', function () {
				w.scrollTo({ top: 0, behavior: reduced() ? 'auto' : 'smooth' });
				var main = $('#sa-main');
				if (main) { main.focus({ preventScroll: true }); }
			});
		}
	}

	/* ------------------------------------------------ 4. smooth anchor scroll */
	function scrollOffsetFor(target) {
		var off = H.admin + (header ? H.nav : 0) + 12;
		if (subnav && isVisible(subnav)) {
			var subTop = subnav.getBoundingClientRect().top + w.pageYOffset;
			var tTop = target.getBoundingClientRect().top + w.pageYOffset;
			if (tTop > subTop) { off += subnav.offsetHeight; }
		}
		return off;
	}

	function scrollToEl(target, instant) {
		if (!target) { return; }
		if (openTabFor(target)) {
			// Let Elementor's Alpine handler reveal the panel before measuring.
			setTimeout(function () { scrollToEl(target, instant); }, 80);
			return;
		}
		var y = target.getBoundingClientRect().top + w.pageYOffset - scrollOffsetFor(target);
		H.lock++;
		H.hidden = false;
		updateHeader();
		w.scrollTo({ top: Math.max(0, y), behavior: (instant || reduced()) ? 'auto' : 'smooth' });
		var done = function () { H.lock = Math.max(0, H.lock - 1); };
		var fired = false;
		var finish = function () { if (!fired) { fired = true; setTimeout(done, 120); } };
		if ('onscrollend' in w) { w.addEventListener('scrollend', finish, { once: true }); }
		setTimeout(finish, 1400);
	}

	/** If the target sits inside an unselected tab panel, select that tab first. */
	function openTabFor(target) {
		var changed = false;
		var panel = target.closest('[role="tabpanel"]');
		while (panel) {
			if (!panel.classList.contains('e--selected') || panel.hidden || getComputedStyle(panel).display === 'none') {
				var tabId = panel.getAttribute('aria-labelledby') || (panel.id || '').replace('-tab-content-', '-tab-');
				var tab = tabId ? d.getElementById(tabId) : null;
				if (tab && tab.getAttribute('aria-selected') !== 'true') { tab.click(); changed = true; }
			}
			panel = panel.parentElement ? panel.parentElement.closest('[role="tabpanel"]') : null;
		}
		return changed;
	}

	/** A hash may name a tab (by its label slug) rather than an element id. */
	function tabForHash(hash) {
		var s = slug(hash);
		if (!s) { return null; }
		return $$('[role="tab"]').filter(function (tab) { return slug(tab.textContent) === s; })[0] || null;
	}

	function resolveHash(hash) {
		var id = decodeURIComponent(String(hash || '').replace(/^#/, ''));
		if (!id) { return null; }
		var el = d.getElementById(id);
		if (el) { return el; }
		var tab = tabForHash(id);
		if (tab) {
			tab.click();
			return tab.closest('[data-element_type="e-tabs"]') || tab;
		}
		return null;
	}

	function initAnchors() {
		d.addEventListener('click', function (e) {
			if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
			var a = e.target.closest('a[href*="#"]');
			if (!a || a.hasAttribute('data-no-smooth') || a.closest('[role="tablist"]')) { return; }
			var href = a.getAttribute('href');
			if (href === '#' || href.indexOf('#elementor-action') === 0 || /^#?$/.test(a.hash)) { return; }
			if (a.pathname.replace(/\/$/, '') !== w.location.pathname.replace(/\/$/, '') || a.host !== w.location.host) { return; }
			var target = resolveHash(a.hash);
			if (!target) { return; }
			e.preventDefault();
			scrollToEl(target);
			if (w.history && w.history.pushState) { w.history.pushState(null, '', a.hash); }
		});
		w.addEventListener('hashchange', function () { var t2 = resolveHash(w.location.hash); if (t2) { scrollToEl(t2); } });
	}

	function initialHash() {
		if (!w.location.hash) { return; }
		var target = resolveHash(w.location.hash);
		if (target) {
			setTimeout(function () { scrollToEl(target, true); }, 250);
		}
	}

	/* ---------------------------------------------------- 5. hero carousel */
	function initHero(scope) {
		$$('.sub-hero-carousel', scope).forEach(function (root) {
			if (!once(root, 'hero')) { return; }
			var tablist = $('[role="tablist"]', root);
			if (!tablist) { return; }
			var tabs = $$('[role="tab"]', tablist);
			var panels = $$('[role="tabpanel"]', root).filter(function (p) { return p.closest('.sub-hero-carousel') === root; });
			if (tabs.length < 2 || panels.length !== tabs.length) { return; }
			var manual = !root.hasAttribute('x-data');
			var interval = parseInt(root.getAttribute('data-interval'), 10) || 6000;
			var autoplay = root.getAttribute('data-autoplay') !== '0' && !reduced();
			var cur = Math.max(0, tabs.findIndex(function (tb) { return tb.getAttribute('aria-selected') === 'true'; }));
			var elapsed = 0;
			var last = 0;
			var paused = { hover: false, focus: false, view: false, doc: false };

			root.classList.add('sa-hc');

			// Progress bars.
			var bars = tabs.map(function (tab) {
				var bar = d.createElement('span');
				bar.className = 'sa-hc-bar';
				bar.setAttribute('aria-hidden', 'true');
				bar.appendChild(d.createElement('i'));
				tab.insertBefore(bar, tab.firstChild);
				return bar.firstChild;
			});

			// Ken Burns layer + staggered text.
			panels.forEach(function (panel) {
				var cs = getComputedStyle(panel);
				if (cs.backgroundImage && cs.backgroundImage !== 'none' && cs.backgroundImage.indexOf('url(') !== -1) {
					var kb = d.createElement('div');
					kb.className = 'sa-kb';
					kb.setAttribute('aria-hidden', 'true');
					kb.style.backgroundImage = cs.backgroundImage;
					kb.style.backgroundPosition = cs.backgroundPosition;
					panel.style.backgroundImage = 'none';
					panel.insertBefore(kb, panel.firstChild);
				}
				var i = 0;
				var seenRows = [];
				$$('h1, h2, h3, h4, p, .e-button-base, .sa-btn, .sub-btn', panel).forEach(function (el) {
					if (el.closest('.sa-anim')) { return; }
					var target = el;
					if (el.matches('.e-button-base, .sa-btn, .sub-btn')) {
						target = el.parentElement;
						if (seenRows.indexOf(target) !== -1 || target === panel) { return; }
						seenRows.push(target);
					}
					target.classList.add('sa-anim');
					target.style.setProperty('--i', i++);
				});
			});

			function setBars() {
				bars.forEach(function (b, k) {
					var p = k === cur ? (autoplay ? clamp(elapsed / interval, 0, 1) : 1) : 0;
					b.style.setProperty('--p', p.toFixed(4));
				});
			}

			function onChange(i) {
				cur = i;
				elapsed = 0;
				setBars();
				var panel = panels[i];
				panel.classList.remove('sa-text-in');
				void panel.offsetWidth;
				panel.classList.add('sa-text-in');
			}

			function manualSet(i) {
				tabs.forEach(function (tb, k) {
					var sel = k === i;
					tb.setAttribute('aria-selected', sel ? 'true' : 'false');
					tb.tabIndex = sel ? 0 : -1;
					tb.classList.toggle('e--selected', sel);
				});
				panels.forEach(function (p, k) {
					p.classList.toggle('e--selected', k === i);
					p.hidden = k !== i;
				});
			}

			function go(i) {
				i = (i + tabs.length) % tabs.length;
				if (i === cur) { return; }
				if (manual) { manualSet(i); onChange(i); return; }
				tabs[i].click();
				w.requestAnimationFrame(function () {
					if (tabs[i].getAttribute('aria-selected') !== 'true') { manualSet(i); }
					if (cur !== i) { onChange(i); }
				});
			}

			// Tab changes from any source (Alpine click, keyboard, manual).
			var mo = new MutationObserver(function () {
				var i = tabs.findIndex(function (tb) { return tb.getAttribute('aria-selected') === 'true'; });
				if (i !== -1 && i !== cur) { onChange(i); }
			});
			tabs.forEach(function (tb) { mo.observe(tb, { attributes: true, attributeFilter: ['aria-selected'] }); });
			if (manual) {
				tabs.forEach(function (tb, k) { tb.addEventListener('click', function () { go(k); }); });
			}

			// Keyboard: arrows anywhere inside the carousel.
			root.addEventListener('keydown', function (e) {
				if (e.target.closest('input, textarea, select')) { return; }
				if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
					var next = cur + (e.key === 'ArrowRight' ? 1 : -1);
					if (!e.target.closest('[role="tab"]') || manual) { e.preventDefault(); }
					go(next);
					if (e.target.closest('[role="tab"]')) { tabs[(next + tabs.length) % tabs.length].focus(); }
				}
			});

			// Pause on hover / focus / hidden / out of view.
			root.addEventListener('mouseenter', function () { paused.hover = true; root.classList.add('is-paused'); });
			root.addEventListener('mouseleave', function () { paused.hover = false; root.classList.remove('is-paused'); });
			root.addEventListener('focusin', function () { paused.focus = true; });
			root.addEventListener('focusout', function (e) { if (!root.contains(e.relatedTarget)) { paused.focus = false; } });
			d.addEventListener('visibilitychange', function () { paused.doc = d.hidden; });
			if (w.IntersectionObserver) {
				new IntersectionObserver(function (en) { paused.view = !en[0].isIntersecting; }, { threshold: 0.15 }).observe(root);
			}

			// Swipe (touch).
			var sx = 0;
			var sy = 0;
			root.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; sy = e.touches[0].clientY; }, { passive: true });
			root.addEventListener('touchend', function (e) {
				var dx = e.changedTouches[0].clientX - sx;
				var dy = e.changedTouches[0].clientY - sy;
				if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.3) { go(cur + (dx < 0 ? 1 : -1)); }
			}, { passive: true });

			function frame(now) {
				if (!last) { last = now; }
				var dt = Math.min(100, now - last);
				last = now;
				if (autoplay && !paused.hover && !paused.focus && !paused.view && !paused.doc) {
					elapsed += dt;
					if (elapsed >= interval) { go(cur + 1); elapsed = 0; }
					setBars();
				}
				w.requestAnimationFrame(frame);
			}
			onChange(cur);
			w.requestAnimationFrame(frame);
		});
	}

	/* --------------------------------------------------- 6. colour swatches */
	function initSwatches(scope) {
		$$('.sub-swatch', scope).forEach(function (sw) {
			if (!once(sw, 'sw')) { return; }
			var name = (sw.textContent || '').replace(/\s+/g, ' ').trim();
			if (!name) { return; }
			sw.setAttribute('data-name', name);
			// Pin the swatch colour inline so Elementor's default tab :hover/:focus grey can't replace it.
			var bg = getComputedStyle(sw).backgroundColor;
			if (bg && bg !== 'rgba(0, 0, 0, 0)' && !sw.style.backgroundColor) { sw.style.setProperty('background-color', bg, 'important'); }
			if (!sw.getAttribute('aria-label')) { sw.setAttribute('aria-label', name); }
			var tip = d.createElement('span');
			tip.className = 'sa-tip';
			tip.setAttribute('aria-hidden', 'true');
			tip.textContent = name;
			sw.appendChild(tip);
		});
		$$('.sub-swatch-menu', scope).forEach(function (menu) {
			if (!once(menu, 'swm')) { return; }
			var tabsRoot = menu.closest('[data-element_type="e-tabs"]') || menu.parentElement;
			var swatches = $$('.sub-swatch', menu);
			if (!swatches.length) { return; }
			// Show the selected colour name next to the swatches unless the stage already shows it.
			var stageText = $$('.sub-colour-stage', tabsRoot).map(function (p) { return p.textContent; }).join(' ');
			var hasNames = swatches.every(function (s) { return stageText.indexOf(s.getAttribute('data-name')) !== -1; });
			if (hasNames) { return; }
			var label = d.createElement('p');
			label.className = 'sa-swatch-current';
			label.setAttribute('aria-live', 'polite');
			menu.parentNode.insertBefore(label, menu.nextSibling);
			function update() {
				var sel = swatches.filter(function (s) { return s.getAttribute('aria-selected') === 'true'; })[0] || swatches[0];
				label.innerHTML = '';
				label.appendChild(d.createTextNode((t.colour || 'Colour') + ':'));
				var st = d.createElement('strong');
				st.textContent = sel.getAttribute('data-name');
				label.appendChild(st);
			}
			update();
			var mo = new MutationObserver(update);
			swatches.forEach(function (s) { mo.observe(s, { attributes: true, attributeFilter: ['aria-selected'] }); });
		});
	}

	/* ------------------------------------------------------- 7. model sub-nav */
	function initSubnav() {
		subnav = $('.sub-subnav');
		if (!subnav) { return; }
		measureHeader();
		var links = $$('.sub-subnav-link', subnav).filter(function (a) { return a.hash && a.hash.length > 1; });
		var items = links.map(function (a) {
			return { a: a, el: d.getElementById(decodeURIComponent(a.hash.slice(1))) };
		}).filter(function (x) { return x.el; });
		var scroller = links.length ? links[0].parentElement : null;
		var current = null;
		function update() {
			var off = headerBottom();
			var r = subnav.getBoundingClientRect();
			subnav.classList.toggle('is-stuck', r.top <= off + 1 && w.pageYOffset > 10);
			var line = off + subnav.offsetHeight + Math.min(160, w.innerHeight * 0.2);
			var active = null;
			var best = -Infinity;
			items.forEach(function (it) {
				var top = it.el.getBoundingClientRect().top;
				if (top <= line && top > best && isVisible(it.el)) { best = top; active = it; }
			});
			if (w.innerHeight + w.pageYOffset >= d.documentElement.scrollHeight - 4 && items.length) {
				active = items.reduce(function (a, b) { return b.el.getBoundingClientRect().top > a.el.getBoundingClientRect().top ? b : a; });
			}
			if (active === current) { return; }
			current = active;
			items.forEach(function (it) {
				var on = it === active;
				it.a.classList.toggle('is-active', on);
				if (on) { it.a.setAttribute('aria-current', 'true'); } else { it.a.removeAttribute('aria-current'); }
			});
			if (active && scroller && scroller.scrollWidth > scroller.clientWidth + 4) {
				var aR = active.a.getBoundingClientRect();
				var sR = scroller.getBoundingClientRect();
				scroller.scrollTo({ left: scroller.scrollLeft + (aR.left - sR.left) - (sR.width - aR.width) / 2, behavior: reduced() ? 'auto' : 'smooth' });
			}
		}
		update();
		onScrollFrame(update);
	}

	/* ------------------------------------------------------------ 8. counters */
	var NUM_RE = /(\d{1,3}(?:,\d{3})+|\d+)(\.\d+)?/;
	function prepCounter(el) {
		var walker = d.createTreeWalker(el, NodeFilter.SHOW_TEXT, null);
		var node;
		while ((node = walker.nextNode())) {
			var m = NUM_RE.exec(node.nodeValue);
			if (!m) { continue; }
			var intPart = m[1];
			var dec = m[2] ? m[2].length - 1 : 0;
			var commas = intPart.indexOf(',') !== -1;
			var value = parseFloat(intPart.replace(/,/g, '') + (m[2] || ''));
			var isYear = !commas && !dec && /^(19|20)\d\d$/.test(intPart);
			return {
				node: node,
				pre: node.nodeValue.slice(0, m.index),
				post: node.nodeValue.slice(m.index + m[0].length),
				orig: node.nodeValue,
				value: value,
				from: isYear ? value - 50 : 0,
				dec: dec,
				commas: commas
			};
		}
		return null;
	}
	function fmt(c, v) {
		var s = v.toFixed(c.dec);
		if (c.commas) {
			var parts = s.split('.');
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
			s = parts.join('.');
		}
		return s;
	}
	function runCounter(c) {
		var start = 0;
		var dur = 1600;
		function step(now) {
			if (!start) { start = now; }
			var p = clamp((now - start) / dur, 0, 1);
			var e = p === 1 ? 1 : 1 - Math.pow(2, -10 * p);
			c.node.nodeValue = c.pre + fmt(c, c.from + (c.value - c.from) * e) + c.post;
			if (p < 1) { w.requestAnimationFrame(step); } else { c.node.nodeValue = c.orig; }
		}
		w.requestAnimationFrame(step);
	}
	function initCounters(scope) {
		if (reduced() || !w.IntersectionObserver) { return; }
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (!en.isIntersecting) { return; }
				io.unobserve(en.target);
				if (en.target.__saCounter) { runCounter(en.target.__saCounter); }
			});
		}, { threshold: 0.4 });
		$$('.sub-counter', scope).forEach(function (el) {
			if (!once(el, 'counter')) { return; }
			var c = prepCounter(el);
			if (!c) { return; }
			el.__saCounter = c;
			c.node.nodeValue = c.pre + fmt(c, c.from) + c.post;
			io.observe(el);
		});
	}

	/* ------------------------------------------------------------ 9. parallax */
	var pxItems = [];
	function initParallax(scope) {
		if (reduced() || w.innerWidth < 768) { return; }
		$$('.sub-parallax', scope).forEach(function (el) {
			if (!once(el, 'px')) { return; }
			var mover = null;
			var factor = 0.08;
			if (el.tagName === 'IMG') {
				mover = el;
				el.classList.add('sa-px-img');
			} else {
				var cs = getComputedStyle(el);
				var img = $('img', el);
				if (cs.backgroundImage && cs.backgroundImage.indexOf('url(') !== -1) {
					var layer = d.createElement('div');
					layer.className = 'sa-px-layer';
					layer.setAttribute('aria-hidden', 'true');
					layer.style.backgroundImage = cs.backgroundImage;
					layer.style.backgroundPosition = cs.backgroundPosition;
					el.classList.add('sa-px-host');
					el.style.backgroundImage = 'none';
					el.insertBefore(layer, el.firstChild);
					mover = layer;
					factor = 0.12;
				} else if (img) {
					mover = img;
					img.classList.add('sa-px-img');
				}
			}
			if (mover) { pxItems.push({ el: el, mover: mover, f: factor, on: true }); }
		});
		if (!pxItems.length) { return; }
		if (w.IntersectionObserver) {
			var io = new IntersectionObserver(function (en) {
				en.forEach(function (e) { pxItems.forEach(function (it) { if (it.el === e.target) { it.on = e.isIntersecting; } }); });
			}, { rootMargin: '120px 0px' });
			pxItems.forEach(function (it) { io.observe(it.el); });
		}
		function update() {
			var vh = w.innerHeight;
			pxItems.forEach(function (it) {
				if (!it.on) { return; }
				var r = it.el.getBoundingClientRect();
				var y = clamp((r.top + r.height / 2 - vh / 2) * -it.f, -70, 70);
				it.mover.style.translate = '0 ' + y.toFixed(1) + 'px';
			});
		}
		update();
		onScrollFrame(update);
	}

	/* ---------------------------------------------------- 10. gallery lightbox */
	function largestSrc(img) {
		var best = img.currentSrc || img.src;
		var max = 0;
		(img.getAttribute('srcset') || '').split(',').forEach(function (c) {
			var p = c.trim().split(/\s+/);
			var wv = parseInt(p[1], 10) || 0;
			if (p[0] && wv > max) { max = wv; best = p[0]; }
		});
		var a = img.closest('a[href]');
		if (a && /\.(jpe?g|png|webp|gif|avif)(\?.*)?$/i.test(a.href)) { best = a.href; }
		return best;
	}
	function galleryImages(img) {
		var g = img.closest('.sub-gallery');
		return $$('img', g).filter(function (i) { return isVisible(i) && !i.closest('.sa-kb'); });
	}
	var lb = null;
	function openLightbox(list, index, opener) {
		if (lb) { return; }
		var idx = index;
		lb = d.createElement('div');
		lb.className = 'sa-lightbox';
		lb.setAttribute('role', 'dialog');
		lb.setAttribute('aria-modal', 'true');
		lb.setAttribute('aria-label', t.gallery || 'Image gallery');
		lb.innerHTML = '<p class="sa-lightbox__count" aria-live="polite"></p>' +
			'<figure class="sa-lightbox__fig"><img class="sa-lightbox__img" alt=""><figcaption class="sa-lightbox__cap"></figcaption></figure>' +
			'<button type="button" class="sa-lightbox__btn sa-lightbox__close"><svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>' +
			'<button type="button" class="sa-lightbox__btn sa-lightbox__prev"><svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path d="m15 6-6 6 6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>' +
			'<button type="button" class="sa-lightbox__btn sa-lightbox__next"><svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>';
		var img = $('.sa-lightbox__img', lb);
		var cap = $('.sa-lightbox__cap', lb);
		var count = $('.sa-lightbox__count', lb);
		var bClose = $('.sa-lightbox__close', lb);
		var bPrev = $('.sa-lightbox__prev', lb);
		var bNext = $('.sa-lightbox__next', lb);
		bClose.setAttribute('aria-label', t.close || 'Close');
		bPrev.setAttribute('aria-label', t.prev || 'Previous image');
		bNext.setAttribute('aria-label', t.next || 'Next image');
		if (list.length < 2) { bPrev.hidden = true; bNext.hidden = true; }
		function show(i, dir) {
			idx = (i + list.length) % list.length;
			var src = list[idx];
			img.classList.remove('is-next', 'is-prev');
			void img.offsetWidth;
			if (dir) { img.classList.add(dir > 0 ? 'is-next' : 'is-prev'); }
			img.src = largestSrc(src);
			img.alt = src.alt || '';
			cap.textContent = src.alt || '';
			count.textContent = list.length > 1 ? (idx + 1) + ' / ' + list.length : '';
			// Preload neighbours.
			[idx + 1, idx - 1].forEach(function (n) { var p = new Image(); p.src = largestSrc(list[(n + list.length) % list.length]); });
		}
		function close() {
			if (!lb) { return; }
			var el = lb;
			lb = null;
			el.classList.remove('is-open');
			lockScroll(false);
			d.removeEventListener('keydown', onKey);
			setTimeout(function () { el.remove(); }, 300);
			if (opener) { opener.focus(); }
		}
		function onKey(e) {
			if (e.key === 'Escape') { close(); }
			else if (e.key === 'ArrowRight') { show(idx + 1, 1); }
			else if (e.key === 'ArrowLeft') { show(idx - 1, -1); }
			else { trapTab(e, lb); }
		}
		bClose.addEventListener('click', close);
		bPrev.addEventListener('click', function () { show(idx - 1, -1); });
		bNext.addEventListener('click', function () { show(idx + 1, 1); });
		lb.addEventListener('click', function (e) { if (e.target === lb || e.target.classList.contains('sa-lightbox__fig')) { close(); } });
		var sx = 0;
		lb.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
		lb.addEventListener('touchend', function (e) {
			var dx = e.changedTouches[0].clientX - sx;
			if (Math.abs(dx) > 50) { show(idx + (dx < 0 ? 1 : -1), dx < 0 ? 1 : -1); }
		}, { passive: true });
		d.addEventListener('keydown', onKey);
		d.body.appendChild(lb);
		lockScroll(true);
		show(idx);
		void lb.offsetWidth;
		lb.classList.add('is-open');
		bClose.focus();
	}
	function initGallery(scope) {
		$$('.sub-gallery img', scope).forEach(function (img) {
			if (!once(img, 'lb')) { return; }
			if (!img.closest('a[href]')) {
				img.setAttribute('tabindex', '0');
				img.setAttribute('role', 'button');
				img.setAttribute('aria-label', (t.openImg || 'Open image') + (img.alt ? ': ' + img.alt : ''));
			}
		});
		if (!once(d, 'lbdelegate')) { return; }
		function trigger(e) {
			var img = e.target.closest('.sub-gallery img');
			if (!img) { return; }
			var a = img.closest('a[href]');
			if (a && !/\.(jpe?g|png|webp|gif|avif)(\?.*)?$/i.test(a.href)) { return; }
			e.preventDefault();
			var list = galleryImages(img);
			openLightbox(list, Math.max(0, list.indexOf(img)), img);
		}
		d.addEventListener('click', trigger);
		d.addEventListener('keydown', function (e) {
			if ((e.key === 'Enter' || e.key === ' ') && e.target.matches && e.target.matches('.sub-gallery img')) { trigger(e); }
		});
	}

	/* ------------------------------------------------------ 11. FAQ accordion */
	function initFaq(scope) {
		// Browsers with interpolate-size animate <details> natively via Elementor's CSS.
		var native = w.CSS && CSS.supports && CSS.supports('interpolate-size', 'allow-keywords');
		if (native || reduced()) { return; }
		$$('details.sub-faq-item, .sub-faq details', scope).forEach(function (det) {
			if (!once(det, 'faq')) { return; }
			var sum = $('summary', det);
			if (!sum) { return; }
			var anim = null;
			function animate(from, to, after) {
				if (anim) { anim.cancel(); }
				det.style.overflow = 'hidden';
				anim = det.animate({ height: [from + 'px', to + 'px'] }, { duration: 380, easing: 'cubic-bezier(.2,.7,.2,1)' });
				anim.onfinish = function () { anim = null; det.style.overflow = ''; det.style.height = ''; if (after) { after(); } };
			}
			sum.addEventListener('click', function (e) {
				e.preventDefault();
				var start = det.offsetHeight;
				if (det.open) {
					animate(start, sum.offsetHeight, function () { det.open = false; });
				} else {
					var name = det.getAttribute('name');
					if (name) {
						$$('details[name="' + name + '"][open]').forEach(function (o) {
							if (o !== det) { o.removeAttribute('open'); }
						});
						det.removeAttribute('name');
						det.open = true;
						det.setAttribute('name', name);
					} else {
						det.open = true;
					}
					animate(start, det.scrollHeight);
				}
			});
		});
	}

	/* ----------------------------------------------- 12. polish & micro-UX */
	function initMagnetic(scope) {
		if (!mqFine.matches || reduced()) { return; }
		$$('.sub-btn, .sa-btn', scope).forEach(function (btn) {
			if (!once(btn, 'mag') || btn.closest('[role="tablist"]')) { return; }
			btn.classList.add('sa-magnetic');
			btn.addEventListener('mousemove', function (e) {
				var r = btn.getBoundingClientRect();
				var x = e.clientX - r.left;
				var y = e.clientY - r.top;
				btn.style.translate = clamp((x - r.width / 2) * 0.18, -8, 8).toFixed(1) + 'px ' + clamp((y - r.height / 2) * 0.25, -5, 5).toFixed(1) + 'px';
				btn.style.setProperty('--mx', x + 'px');
				btn.style.setProperty('--my', y + 'px');
			});
			btn.addEventListener('mouseleave', function () { btn.style.translate = ''; });
		});
	}

	function initGlow(scope) {
		if (!mqFine.matches || reduced()) { return; }
		$$('.sub-dark', scope).forEach(function (sec) {
			if (!once(sec, 'glow') || sec.closest('.sub-hero-carousel, .sa-footer')) { return; }
			if (getComputedStyle(sec).position === 'static') { sec.style.position = 'relative'; }
			sec.classList.add('sa-glow');
			var raf = 0;
			sec.addEventListener('mousemove', function (e) {
				if (raf) { return; }
				raf = w.requestAnimationFrame(function () {
					raf = 0;
					var r = sec.getBoundingClientRect();
					sec.style.setProperty('--gx', (e.clientX - r.left) + 'px');
					sec.style.setProperty('--gy', (e.clientY - r.top) + 'px');
				});
				sec.classList.add('is-glowing');
			});
			sec.addEventListener('mouseleave', function () { sec.classList.remove('is-glowing'); });
		});
	}

	function initImageFade(scope) {
		$$('.elementor img, .sa-main img', scope).forEach(function (img) {
			if (!once(img, 'fade') || img.complete || img.closest('.sub-hero-carousel, .sa-header, .sa-lightbox')) { return; }
			img.classList.add('sa-fade');
			var done = function () { img.classList.add('is-loaded'); };
			img.addEventListener('load', done, { once: true });
			img.addEventListener('error', done, { once: true });
			setTimeout(done, 4000);
		});
	}

	var revealIO = null;
	function initReveal(scope) {
		var els = $$('[data-sa-reveal]', scope).filter(function (el) { return once(el, 'rev'); });
		if (!els.length) { return; }
		if (!w.IntersectionObserver || reduced()) { els.forEach(function (el) { el.classList.add('is-in'); }); return; }
		if (!revealIO) {
			revealIO = new IntersectionObserver(function (entries) {
				entries.forEach(function (en) {
					if (en.isIntersecting) { en.target.classList.add('is-in'); revealIO.unobserve(en.target); }
				});
			}, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
		}
		els.forEach(function (el) { revealIO.observe(el); });
	}

	function initCookie() {
		var bar = $('.sa-cookie');
		if (!bar) { return; }
		var ok = false;
		try { ok = !!w.localStorage.getItem('saCookieOk'); } catch (e) { ok = false; }
		if (ok) { bar.remove(); return; }
		setTimeout(function () {
			bar.hidden = false;
			void bar.offsetWidth;
			bar.classList.add('is-visible');
		}, 1200);
		$('.sa-cookie__ok', bar).addEventListener('click', function () {
			try { w.localStorage.setItem('saCookieOk', '1'); } catch (e) { /* storage unavailable */ }
			bar.classList.remove('is-visible');
			setTimeout(function () { bar.remove(); }, 600);
		});
	}

	function initCopy() {
		d.addEventListener('click', function (e) {
			var b = e.target.closest('[data-sa-copy]');
			if (!b) { return; }
			var url = b.getAttribute('data-sa-copy');
			var tip = $('.sa-share__tip', b);
			var done = function () {
				b.classList.add('is-copied');
				if (tip) { tip.textContent = t.copied || 'Link copied'; }
				setTimeout(function () { b.classList.remove('is-copied'); if (tip) { tip.textContent = ''; } }, 2000);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(done, done);
			} else {
				var ta = d.createElement('textarea');
				ta.value = url;
				d.body.appendChild(ta);
				ta.select();
				try { d.execCommand('copy'); } catch (err) { /* ignore */ }
				ta.remove();
				done();
			}
		});
	}

	function initIntro() {
		if (!html.classList.contains('sa-intro')) { return; }
		setTimeout(function () { html.classList.remove('sa-intro'); }, 950);
	}

	/* --------------------------------------------------------------- boot */
	function initScope(scope) {
		scope = scope || d;
		initIds(scope);
		initHero(scope);
		initSwatches(scope);
		initCounters(scope);
		initParallax(scope);
		initGallery(scope);
		initFaq(scope);
		initMagnetic(scope);
		initGlow(scope);
		initImageFade(scope);
		initReveal(scope);
	}

	cfg.api = {
		init: initScope,
		scrollTo: scrollToEl,
		counters: initCounters,
		reveal: initReveal,
		headerOffset: headerBottom
	};
	w.SubaruTheme = cfg;

	initIntro();
	ready(function () {
		detectHero();
		initHeader();
		initScope(d);
		initSubnav();
		initProgress();
		initAnchors();
		initCookie();
		initCopy();
		initialHash();
		// Late layout changes (fonts, images) affect header/sub-nav heights.
		w.addEventListener('load', function () { measureHeader(); updateHeader(); });
	});
})();
