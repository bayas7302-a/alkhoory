/* ONORA theme behaviour: mobile menu, sticky header state, newsletter mailto, products tabs. */
(function () {
	'use strict';

	var header = document.getElementById('onora-header');
	var burger = document.querySelector('.onora-burger');
	var nav = document.getElementById('onora-nav');

	// Keep --on-header-h in sync with the real sticky header height (used by the products tabs).
	function syncHeaderHeight() {
		if (header) document.documentElement.style.setProperty('--on-header-h', header.offsetHeight + 'px');
	}
	syncHeaderHeight();
	window.addEventListener('resize', syncHeaderHeight);

	window.addEventListener('scroll', function () {
		if (header) header.classList.toggle('is-scrolled', window.scrollY > 40);
	}, { passive: true });

	if (burger && nav) {
		burger.addEventListener('click', function () {
			var open = burger.getAttribute('aria-expanded') !== 'true';
			burger.setAttribute('aria-expanded', String(open));
			nav.classList.toggle('is-open', open);
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) {
				burger.setAttribute('aria-expanded', 'false');
				nav.classList.remove('is-open');
				burger.focus();
			}
		});
	}

	// Footer newsletter: no mailing-list service yet, so open a pre-filled email.
	var form = document.querySelector('.onora-newsletter');
	if (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var email = form.querySelector('input[type="email"]').value;
			window.location.href = 'mailto:' + form.getAttribute('data-mailto') +
				'?subject=' + encodeURIComponent('Newsletter signup') +
				'&body=' + encodeURIComponent('Please add ' + email + ' to the ONORA newsletter.');
		});
	}

	// Products page tabs: "All" shows every collection, a collection tab shows only that section.
	var tabsBar = document.getElementById('product-tabs');
	if (!tabsBar) return;
	var tabs = Array.prototype.slice.call(tabsBar.querySelectorAll('a[href*="#"]'));
	var slugs = tabs.map(function (a) { return a.hash.slice(1); }).filter(function (s) { return s && s !== 'all'; });
	var sections = slugs.map(function (s) { return document.getElementById(s); }).filter(Boolean);

	function activate(slug, scroll) {
		if (slugs.indexOf(slug) === -1) slug = 'all';
		tabs.forEach(function (a) {
			var on = a.hash.slice(1) === slug;
			(a.closest('p') || a).classList.toggle('is-active', on);
			if (on) {
				a.setAttribute('aria-current', 'true');
				a.scrollIntoView({ block: 'nearest', inline: 'center' });
			} else {
				a.removeAttribute('aria-current');
			}
		});
		sections.forEach(function (sec) {
			sec.classList.toggle('onora-collection-hidden', slug !== 'all' && sec.id !== slug);
		});
		if (scroll) {
			var y = tabsBar.getBoundingClientRect().top + window.scrollY - (header ? header.offsetHeight : 0);
			window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
		}
	}

	tabs.forEach(function (a) {
		a.addEventListener('click', function (e) {
			e.preventDefault();
			var slug = a.hash.slice(1);
			history.replaceState(null, '', slug === 'all' ? location.pathname : '#' + slug);
			activate(slug, true);
		});
	});
	window.addEventListener('hashchange', function () { activate(location.hash.slice(1), true); });
	activate(location.hash.slice(1), slugs.indexOf(location.hash.slice(1)) !== -1);
})();
