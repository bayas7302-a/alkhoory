/* AB Productions — front-end behaviour (no dependencies). */
(function () {
	'use strict';

	var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
	var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };
	var mobile = window.matchMedia('(max-width: 767px)');

	/* ---------------------------------------------------------------- Header */
	var header = $('#abp-header');
	if (header) {
		var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 20); };
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	var burger = $('.abp-burger');
	var nav = $('#abp-nav');
	if (burger && nav) {
		var setMenu = function (open) {
			nav.classList.toggle('is-open', open);
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
			document.body.classList.toggle('abp-menu-open', open);
		};
		burger.addEventListener('click', function () { setMenu(!nav.classList.contains('is-open')); });
		nav.addEventListener('click', function (e) { if (e.target.closest('a')) { setMenu(false); } });
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setMenu(false); } });
	}

	/* Mark in-page menu links (e.g. /about/#clientele) active while their section is in view. */
	var anchorLinks = $$('.abp-nav__list a[href*="#"]').filter(function (a) {
		return a.pathname.replace(/\/$/, '') === location.pathname.replace(/\/$/, '') && a.hash.length > 1;
	});
	if (anchorLinks.length && 'IntersectionObserver' in window) {
		anchorLinks.forEach(function (a) {
			var target = document.getElementById(a.hash.slice(1));
			if (!target) { return; }
			new IntersectionObserver(function (entries) {
				entries.forEach(function (en) { a.classList.toggle('is-active', en.isIntersecting); });
			}, { rootMargin: '-40% 0px -50% 0px' }).observe(target);
		});
	}

	/* --------------------------------------------------------------- Projects */
	function visibleProjects(grid) {
		return $$('.abp-project', grid).filter(function (p) {
			return !p.classList.contains('is-filtered-out') && !p.classList.contains('is-paged-out');
		});
	}

	function applyFilter(grid, slug) {
		var perPage = parseInt(grid.getAttribute('data-per-page'), 10) || 0;
		var shown = 0;
		$$('.abp-project', grid).forEach(function (p) {
			var cats = (p.getAttribute('data-cats') || '').split(' ');
			var match = slug === '*' || cats.indexOf(slug) !== -1;
			p.classList.toggle('is-filtered-out', !match);
			p.classList.remove('is-paged-out', 'is-entering');
			if (match) {
				shown++;
				if (perPage && shown > perPage) { p.classList.add('is-paged-out'); }
				else { void p.offsetWidth; p.classList.add('is-entering'); }
			}
		});
		grid.classList.toggle('is-filtering', slug !== '*');
		updateLoadMore(grid);
	}

	function updateLoadMore(grid) {
		var btn = $('.abp-loadmore[data-target="' + grid.id + '"]');
		if (btn) { btn.hidden = !$('.abp-project.is-paged-out:not(.is-filtered-out)', grid); }
	}

	$$('.abp-filters').forEach(function (bar) {
		var target = bar.getAttribute('data-abp-filter-for');
		bar.addEventListener('click', function (e) {
			var btn = e.target.closest('.abp-filter');
			if (!btn) { return; }
			$$('.abp-filter', bar).forEach(function (b) {
				var on = b === btn;
				b.classList.toggle('is-active', on);
				b.setAttribute('aria-selected', on ? 'true' : 'false');
			});
			var grid = document.getElementById(target);
			if (grid) { applyFilter(grid, btn.getAttribute('data-filter')); }
		});
	});

	$$('.abp-loadmore').forEach(function (btn) {
		var grid = document.getElementById(btn.getAttribute('data-target'));
		if (!grid) { return; }
		var perPage = parseInt(grid.getAttribute('data-per-page'), 10) || 8;
		btn.addEventListener('click', function () {
			$$('.abp-project.is-paged-out:not(.is-filtered-out)', grid).slice(0, perPage).forEach(function (p) {
				p.classList.remove('is-paged-out');
				p.classList.add('is-entering');
			});
			updateLoadMore(grid);
		});
	});

	/* --------------------------------------------------------------- Lightbox */
	var lb = $('#abp-lightbox');
	if (lb) {
		var img = $('.abp-lightbox__img', lb);
		var list = [];
		var index = 0;
		var lastFocus = null;

		var show = function (i) {
			index = (i + list.length) % list.length;
			var item = list[index];
			lb.classList.remove('is-loaded');
			img.onload = function () { lb.classList.add('is-loaded'); };
			img.src = item.getAttribute('data-full');
			img.alt = item.getAttribute('data-title') || '';
			if (img.complete && img.naturalWidth) { lb.classList.add('is-loaded'); }
		};
		var open = function (item) {
			if (!item.getAttribute('data-full')) { return; }
			var grid = item.closest('.abp-projects');
			list = visibleProjects(grid).filter(function (p) { return p.getAttribute('data-full'); });
			lastFocus = document.activeElement;
			lb.hidden = false;
			lb.classList.toggle('is-single', list.length < 2);
			document.body.classList.add('abp-lb-open');
			requestAnimationFrame(function () { lb.classList.add('is-open'); });
			show(list.indexOf(item));
			$('.abp-lightbox__close', lb).focus();
		};
		var close = function () {
			lb.classList.remove('is-open', 'is-loaded');
			document.body.classList.remove('abp-lb-open');
			setTimeout(function () { lb.hidden = true; img.removeAttribute('src'); }, 300);
			if (lastFocus) { lastFocus.focus(); }
		};

		document.addEventListener('click', function (e) {
			var item = e.target.closest('.abp-project');
			if (item && !lb.contains(item)) { e.preventDefault(); open(item); }
		});
		document.addEventListener('keydown', function (e) {
			var item = e.target.closest && e.target.closest('.abp-project');
			if (item && (e.key === 'Enter' || e.key === ' ')) { e.preventDefault(); open(item); return; }
			if (lb.hidden) { return; }
			if (e.key === 'Escape') { close(); }
			if (e.key === 'ArrowLeft') { show(index - 1); }
			if (e.key === 'ArrowRight') { show(index + 1); }
		});
		lb.addEventListener('click', function (e) {
			var action = e.target.closest('[data-abp-lb]');
			if (action) {
				var a = action.getAttribute('data-abp-lb');
				if (a === 'close') { close(); } else if (a === 'prev') { show(index - 1); } else { show(index + 1); }
				return;
			}
			if (e.target === lb || e.target.classList.contains('abp-lightbox__figure')) { close(); }
		});

		/* Swipe on touch devices */
		var x0 = null;
		lb.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
		lb.addEventListener('touchend', function (e) {
			if (x0 === null) { return; }
			var dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 50 && list.length > 1) { show(index + (dx < 0 ? 1 : -1)); }
			x0 = null;
		});
	}

	/* ---------------------------------------------- Services tabs / accordion */
	$$('[data-abp-tabs]').forEach(function (box) {
		var tabs = $$('.abp-svc__tab', box);
		var select = function (tab, scroll) {
			var isOpen = tab.classList.contains('is-active');
			// Mobile accordion: tapping the open item closes it.
			if (mobile.matches && isOpen) {
				tab.classList.remove('is-active');
				tab.setAttribute('aria-expanded', 'false');
				document.getElementById(tab.getAttribute('aria-controls')).hidden = true;
				return;
			}
			tabs.forEach(function (t) {
				var on = t === tab;
				var panel = document.getElementById(t.getAttribute('aria-controls'));
				t.classList.toggle('is-active', on);
				t.setAttribute('aria-expanded', on ? 'true' : 'false');
				panel.classList.toggle('is-active', on);
				panel.hidden = !on;
			});
			if (scroll && mobile.matches) { tab.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
		};
		box.addEventListener('click', function (e) {
			var tab = e.target.closest('.abp-svc__tab');
			if (tab) { select(tab, false); }
		});
		var fromHash = function () {
			var slug = location.hash.replace('#', '');
			var tab = slug && box.querySelector('.abp-svc__tab[data-slug="' + slug + '"]');
			if (tab) {
				if (!tab.classList.contains('is-active')) { select(tab, false); }
				box.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		};
		fromHash();
		window.addEventListener('hashchange', fromHash);
	});

	/* --------------------------------------------------------------- Find us */
	$$('[data-abp-findus]').forEach(function (box) {
		var map = $('.abp-findus__map', box);
		box.addEventListener('click', function (e) {
			var btn = e.target.closest('.abp-findus__toggle button');
			if (!btn || btn.classList.contains('is-active')) { return; }
			$$('.abp-findus__toggle button', box).forEach(function (b) {
				b.classList.toggle('is-active', b === btn);
				b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
			});
			var q = btn.getAttribute('data-map');
			$('.abp-findus__eyebrow', box).textContent = btn.getAttribute('data-eyebrow');
			$('.abp-findus__title', box).textContent = btn.getAttribute('data-title');
			$('.abp-findus__address', box).innerHTML = btn.getAttribute('data-address')
				.replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; })
				.replace(/\n/g, '<br>');
			$('.abp-findus__btn', box).href = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(q);
			map.src = 'https://www.google.com/maps?q=' + encodeURIComponent(q) + '&output=embed';
		});
	});

	/* ---------------------------------------------------------- Scroll reveal */
	if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); }
			});
		}, { rootMargin: '0px 0px -8% 0px' });
		$$('.abp-reveal').forEach(function (el) { io.observe(el); });
	} else {
		$$('.abp-reveal').forEach(function (el) { el.classList.add('is-visible'); });
	}
})();
