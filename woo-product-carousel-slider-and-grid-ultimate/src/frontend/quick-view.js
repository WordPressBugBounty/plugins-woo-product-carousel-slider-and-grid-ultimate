/**
 * Quick view modal (vanilla JS, no jQuery).
 *
 * Opens the modal named by the button's data-wpcu-target and loads the product
 * through the `ajax_quick_view` admin-ajax action. Clicks are delegated on the
 * document, so buttons added later (AJAX filters, block previews) work too.
 */
( function () {
	let lastRequest = 0;

	function openQuickView( button ) {
		const modal = document.querySelector( '.' + button.getAttribute( 'data-wpcu-target' ) );
		const ajax  = window.wcpcsu_quick_view;

		if ( ! modal || ! ajax || ! ajax.ajax_url ) {
			return;
		}

		const body    = modal.querySelector( '.wpcu-modal__body' );
		const request = ++lastRequest;

		modal.classList.add( 'wpcu-modal--loading', 'wpcu-show' );

		const data = new URLSearchParams( {
			action:     'ajax_quick_view',
			product_id: button.getAttribute( 'data-product-id' ) || '',
			nonce:      button.getAttribute( 'data-nonce' ) || '',
		} );

		fetch( ajax.ajax_url, { method: 'POST', body: data, credentials: 'same-origin' } )
			.then( ( response ) => response.json() )
			.then( ( html ) => {
				// Ignore responses for a product the shopper already moved away from.
				if ( request !== lastRequest ) {
					return;
				}
				if ( body ) {
					body.innerHTML = typeof html === 'string' ? html : '';
				}
			} )
			.catch( ( error ) => {
				console.log( error ); // eslint-disable-line no-console
			} )
			.finally( () => {
				if ( request === lastRequest ) {
					modal.classList.remove( 'wpcu-modal--loading' );
				}
			} );
	}

	document.addEventListener( 'click', function ( e ) {
		const target = e.target instanceof Element ? e.target : null;

		if ( ! target ) {
			return;
		}

		const button = target.closest( '.wpcu-btn-modal-js' );
		if ( button ) {
			e.preventDefault();
			openQuickView( button );
			return;
		}

		const close = target.closest( '.wpcu-modal-close-js' );
		if ( close ) {
			e.preventDefault();
			const modal = close.closest( '.wpcu-modal-js' );
			if ( modal ) {
				modal.classList.remove( 'wpcu-show' );
			}
			return;
		}

		// Click on the backdrop itself closes the modal.
		if ( target.classList.contains( 'wpcu-modal-js' ) ) {
			target.classList.remove( 'wpcu-show' );
		}
	} );
}() );
