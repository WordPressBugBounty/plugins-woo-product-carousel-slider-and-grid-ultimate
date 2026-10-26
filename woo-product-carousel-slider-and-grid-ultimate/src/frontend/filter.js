/**
 * Category filter tabs and Ajax-number pagination (loaded on demand by carousel.js as the
 * carousel-filter chunk).
 *
 * Each `.wpcu-filter` bar sits right before its `.wpcu-products` wrapper. A tab click asks the
 * public products route for that category's cards (cards only, the same markup as the page)
 * and swaps them in: a carousel's Swiper is destroyed and started again, a grid just gets
 * the new cards. The "All" tab restores the cards the page was loaded with.
 *
 * A paginated grid with `grid_pagination_type = ajax_numbers` carries
 * `.wpcu-pagination--ajax`; its page buttons use the same route and swap the same cards.
 * Tabs, pages and the URL share one state per wrapper.
 */

/**
 * Query string key for a value: `wpcu_cat`, then `wpcu_cat_2`, … in page order.
 *
 * @param {string} prefix Base key.
 * @param {number} index  Position on the page.
 * @return {string} Key.
 */
function urlKey( prefix, index ) {
	return index ? `${ prefix }_${ index + 1 }` : prefix;
}

/**
 * Write a value to the address bar, so the filtered view can be shared.
 *
 * Best effort: the block editor's canvas is a `blob:` document (and previews may be
 * `about:srcdoc`), where replaceState() to a changed URL throws.
 *
 * @param {string} key   Query string key.
 * @param {string} value Value, '' to remove the key.
 */
function writeUrl( key, value ) {
	if ( ! /^https?:$/.test( window.location.protocol ) ) {
		return;
	}

	try {
		const url = new URL( window.location.href );

		if ( value ) {
			url.searchParams.set( key, value );
		} else {
			url.searchParams.delete( key );
		}
		window.history.replaceState( window.history.state, '', url );
	} catch ( e ) {
		// The filters still work; only the shareable URL is skipped.
	}
}

/**
 * Read a query string value.
 *
 * @param {string} key Query string key.
 * @return {string} Value, or ''.
 */
function readUrl( key ) {
	return new URL( window.location.href ).searchParams.get( key ) || '';
}

/**
 * Set up one products wrapper: its tabs (if any) and its Ajax-number pagination (if any).
 * Safe to call again for the same wrapper.
 *
 * @param {HTMLElement} products   `.wpcu-products` element.
 * @param {HTMLElement|null} bar   Its `.wpcu-filter` bar, or null.
 * @param {number}      catIndex   Position of the bar on the page.
 * @param {number}      pageIndex  Position of the Ajax pagination on the page.
 * @param {Function}    init       wpcuCarousel.init, to start a carousel again after a swap.
 */
function setupProducts( products, bar, catIndex, pageIndex, init ) {
	if ( products.dataset.wpcuFiltersInit ) {
		return;
	}

	const isCarousel = products.classList.contains( 'wpcu-carousel' );
	const isTicker   = products.classList.contains( 'wpcu-ticker' );
	const cards      = products.querySelector( isCarousel ? '.swiper-wrapper' : ( isTicker ? '.wpcu-ticker__track' : '.wpcu-row' ) );
	const pagination = products.querySelector( '.wpcu-pagination--ajax' );
	const reloadPagi = products.querySelector( '.wpcu-pagination:not(.wpcu-pagination--ajax)' );

	const filterConfig = bar ? JSON.parse( bar.dataset.wpcuFilter || '{}' ) : null;
	const pageConfig   = pagination ? JSON.parse( pagination.dataset.wpcuPagination || '{}' ) : null;
	const config       = filterConfig && filterConfig.url ? filterConfig : pageConfig;

	if ( ! cards || ! config || ! config.url ) {
		return;
	}
	products.dataset.wpcuFiltersInit = '1';

	const status     = bar ? bar.querySelector( '.wpcu-filter__status' ) : null;
	const tabs       = bar ? Array.from( bar.querySelectorAll( '.wpcu-filter__tab' ) ) : [];
	const catKey     = urlKey( 'wpcu_cat', catIndex );
	const pageKey    = urlKey( 'wpcu_page', pageIndex );
	const initialHtml = products.wpcuFilterHtml || cards.innerHTML;
	const initialPagi = pagination ? pagination.innerHTML : '';

	let activeCategory = pageConfig && pageConfig.category ? pageConfig.category : '';
	let currentPage    = pageConfig && pageConfig.page ? parseInt( pageConfig.page, 10 ) || 1 : 1;
	let request        = null;

	// `${category}|${page}` -> { html, pagination, max_pages, found }.
	const cache = new Map();
	cache.set( `${ activeCategory }|${ currentPage }`, {
		html: initialHtml,
		pagination: initialPagi,
		max_pages: pageConfig && pageConfig.max_pages ? parseInt( pageConfig.max_pages, 10 ) : 1,
		found: 1,
	} );

	const setActive = ( category ) => {
		tabs.forEach( ( tab ) => {
			const on = tab.dataset.wpcuCategory === category;
			tab.classList.toggle( 'wpcu-filter__tab--active', on );
			tab.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
		} );
	};

	const setLoading = ( loading ) => {
		products.classList.toggle( 'wpcu-products--loading', loading );
		if ( bar ) {
			bar.classList.toggle( 'wpcu-filter--loading', loading );
		}
		products.setAttribute( 'aria-busy', loading ? 'true' : 'false' );
	};

	const showPagination = ( result ) => {
		if ( ! pagination ) {
			return;
		}

		if ( typeof result.pagination === 'string' ) {
			pagination.innerHTML = result.pagination;
		}

		const empty = ! result.found || ! ( result.html || '' ).trim();
		pagination.hidden = empty || ( result.max_pages || 0 ) <= 1 || ! pagination.innerHTML.trim();
	};

	const show = ( category, page, result ) => {
		if ( isCarousel && products.swiper ) {
			// Not cleanStyles: it would also remove the wrapper's inline --wpcu-* variables
			// (image ratio, columns, colors). The slides are replaced anyway; only the
			// wrapper's transform needs resetting.
			products.swiper.destroy( true, false );
			cards.removeAttribute( 'style' );
		}
		delete products.dataset.wpcuInit;

		cards.innerHTML = result.html || '';

		const empty = ! result.found || ! ( result.html || '' ).trim();
		products.hidden = empty;

		if ( status ) {
			status.hidden = ! empty;
			status.textContent = empty ? config.empty || '' : '';
		}

		// The reload-based page links only fit "All"; the Ajax buttons are replaced above
		// and work inside a category too.
		if ( reloadPagi ) {
			reloadPagi.hidden = '' !== category;
		}
		if ( pagination ) {
			showPagination( result );
		}

		activeCategory = category;
		currentPage    = page;

		if ( bar ) {
			writeUrl( catKey, category );
		}
		if ( pagination ) {
			writeUrl( pageKey, page > 1 ? String( page ) : '' );
		}

		if ( isCarousel ) {
			// Dots are rebuilt by Swiper; clear the old ones first.
			const dots = products.querySelector( '.wpcu-carousel-pagination' );
			if ( dots ) {
				dots.innerHTML = '';
			}
			if ( ! empty ) {
				init( products.parentNode );
			}
		} else if ( isTicker ) {
			// The new cards replaced the copies too; the ticker copies and measures them again.
			init( products.parentNode );
		} else {
			init( products );
		}
	};

	const load = ( category, page, scroll ) => {
		const key = `${ category }|${ page }`;

		if ( cache.has( key ) ) {
			const previousCategory = activeCategory;
			const previousPage     = currentPage;

			setActive( category );
			show( category, page, cache.get( key ) );

			if ( scroll && previousCategory === category && previousPage !== page ) {
				( bar || products ).scrollIntoView( { behavior: 'smooth', block: 'start' } );
			}
			return;
		}

		if ( request ) {
			request.abort();
			request = null;
		}

		const previousCategory = activeCategory;
		const previousPage     = currentPage;
		const controller       = new AbortController();
		const url              = new URL( config.url, window.location.href );
		url.searchParams.set( 'q', config.q );
		url.searchParams.set( 'sig', config.sig );
		url.searchParams.set( 'category', category );
		url.searchParams.set( 'page', String( page ) );
		request = controller;

		setActive( category );
		setLoading( true );

		window.fetch( url, { signal: controller.signal, credentials: 'omit' } )
			.then( ( response ) => ( response.ok ? response.json() : Promise.reject( response ) ) )
			.then( ( data ) => {
				const result = {
					html: typeof data.html === 'string' ? data.html : '',
					pagination: typeof data.pagination === 'string' ? data.pagination : '',
					max_pages: parseInt( data.max_pages, 10 ) || 0,
					found: parseInt( data.found, 10 ) || 0,
				};
				cache.set( key, result );

				if ( request === controller ) {
					request = null;
					setLoading( false );
					show( category, page, result );

					if ( scroll ) {
						( bar || products ).scrollIntoView( { behavior: 'smooth', block: 'start' } );
					}
				}
			} )
			.catch( () => {
				if ( request !== controller ) {
					return; // aborted by a newer request
				}
				// Keep what is shown; put the previous tab/page back.
				request = null;
				setLoading( false );
				setActive( previousCategory );
				activeCategory = previousCategory;
				currentPage    = previousPage;
				if ( bar ) {
					writeUrl( catKey, previousCategory );
				}
				if ( pagination ) {
					writeUrl( pageKey, previousPage > 1 ? String( previousPage ) : '' );
				}
			} );
	};

	const selectCategory = ( category ) => {
		if ( category === activeCategory && currentPage === 1 ) {
			return;
		}
		load( category, 1, false );
	};

	const selectPage = ( page ) => {
		if ( page === currentPage ) {
			return;
		}
		load( activeCategory, page, true );
	};

	if ( bar ) {
		bar.addEventListener( 'click', ( event ) => {
			const tab = event.target.closest( '.wpcu-filter__tab' );

			// The admin live preview blocks clicks (it previews unsaved settings, the route uses saved ones).
			if ( ! tab || event.defaultPrevented ) {
				return;
			}
			selectCategory( tab.dataset.wpcuCategory || '' );
		} );
	}

	if ( pagination ) {
		pagination.addEventListener( 'click', ( event ) => {
			const button = event.target.closest( '[data-wpcu-page]' );

			if ( ! button || event.defaultPrevented ) {
				return;
			}
			event.preventDefault();
			selectPage( parseInt( button.dataset.wpcuPage, 10 ) || 1 );
		} );
	}

	// A shared link: open the category and page from the URL. The server rendered page 1 of
	// "All", so only a non-default state needs a request.
	const fromUrlCategory = readUrl( catKey );
	const fromUrlPage     = parseInt( readUrl( pageKey ), 10 ) || 1;
	const validCategory   = fromUrlCategory && tabs.some( ( tab ) => tab.dataset.wpcuCategory === fromUrlCategory ) ? fromUrlCategory : '';

	if ( validCategory || fromUrlPage > 1 ) {
		load( validCategory, fromUrlPage, false );
	}
}

/**
 * Set up every filter bar and Ajax pagination inside `root`.
 *
 * @param {ParentNode} root Element or document to search in.
 * @param {Function}   init wpcuCarousel.init.
 */
export function initFilters( root, init ) {
	const allBars   = Array.from( document.querySelectorAll( '.wpcu-filter' ) );
	const allPagers = Array.from( document.querySelectorAll( '.wpcu-pagination--ajax' ) );

	root.querySelectorAll( '.wpcu-products' ).forEach( ( products ) => {
		const bar = products.previousElementSibling && products.previousElementSibling.classList.contains( 'wpcu-filter' )
			? products.previousElementSibling
			: null;
		const pagination = products.querySelector( '.wpcu-pagination--ajax' );

		if ( ! bar && ! pagination ) {
			return;
		}

		setupProducts(
			products,
			bar,
			bar ? allBars.indexOf( bar ) : 0,
			pagination ? allPagers.indexOf( pagination ) : 0,
			init
		);
	} );
}
