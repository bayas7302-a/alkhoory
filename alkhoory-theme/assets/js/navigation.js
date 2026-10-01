/**
 * Mobile menu toggle for the site header.
 */
( function () {
	var header = document.getElementById( 'ak-header' );
	if ( ! header ) {
		return;
	}
	var toggle = header.querySelector( '.ak-header__toggle' );

	function setOpen( open ) {
		header.classList.toggle( 'is-open', open );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	}

	toggle.addEventListener( 'click', function () {
		setOpen( ! header.classList.contains( 'is-open' ) );
	} );

	header.addEventListener( 'click', function ( event ) {
		if ( event.target.closest( '.ak-nav a' ) ) {
			setOpen( false );
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && header.classList.contains( 'is-open' ) ) {
			setOpen( false );
			toggle.focus();
		}
	} );
} )();
