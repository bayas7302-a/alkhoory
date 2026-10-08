/* AIS Global – header menu behaviour (no dependencies). */
( function () {
	'use strict';
	var header = document.getElementById( 'site-header' );
	if ( ! header ) {
		return;
	}
	var burger = header.querySelector( '.ais-burger' );
	var nav = document.getElementById( 'ais-primary-nav' );

	if ( burger && nav ) {
		burger.addEventListener( 'click', function () {
			var open = burger.getAttribute( 'aria-expanded' ) === 'true';
			burger.setAttribute( 'aria-expanded', open ? 'false' : 'true' );
			nav.classList.toggle( 'is-open', ! open );
		} );
	}

	header.querySelectorAll( '.ais-sub-toggle' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			var li = btn.closest( 'li' );
			var open = li.classList.toggle( 'is-open' );
			btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
	} );

	// Parent items without a real link ("Our Solutions") open their submenu.
	header.querySelectorAll( '.menu-item-has-children > a' ).forEach( function ( a ) {
		var href = a.getAttribute( 'href' );
		if ( ! href || href === '#' ) {
			a.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				var btn = a.parentNode.querySelector( '.ais-sub-toggle' );
				if ( btn ) {
					btn.click();
				}
			} );
		}
	} );

	document.addEventListener( 'click', function ( e ) {
		if ( ! header.contains( e.target ) ) {
			header.querySelectorAll( '.ais-menu li.is-open' ).forEach( function ( li ) {
				li.classList.remove( 'is-open' );
			} );
		}
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' && nav && nav.classList.contains( 'is-open' ) ) {
			burger.click();
			burger.focus();
		}
	} );

	var onScroll = function () {
		header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
	};
	window.addEventListener( 'scroll', onScroll, { passive: true } );
	onScroll();
} )();
