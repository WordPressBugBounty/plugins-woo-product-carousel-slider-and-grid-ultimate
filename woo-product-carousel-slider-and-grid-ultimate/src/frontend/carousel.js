/**
 * Frontend carousel script (built to build/carousel.js).
 *
 * Vanilla replacement for assets/js/main.js: no jQuery, and only the Swiper 11
 * parts the carousel uses instead of the full prebuilt swiper-bundle. The
 * Pagination module (dots) and the fade effect are loaded on demand, only when
 * a carousel on the page uses them.
 */
import Swiper from 'swiper';
import { Navigation, Autoplay } from 'swiper/modules';

let swiperModules;
let filterModule;

/**
 * Load Swiper's optional modules once, as a separate chunk (carousel-modules): Pagination
 * for carousels with dots, EffectFade for fade carousels. Both come from one import of
 * 'swiper/modules' (the package exports no deeper path), so they share the chunk.
 *
 * @return {Promise} Resolves with { Pagination, EffectFade }.
 */
function loadSwiperModules() {
	if ( ! swiperModules ) {
		swiperModules = import(
			/* webpackChunkName: "carousel-modules" */
			'swiper/modules'
		).then( ( { Pagination, EffectFade } ) => ( { Pagination, EffectFade } ) );
	}

	return swiperModules;
}

/**
 * Load the category filter code once, as a separate chunk (only pages with filter tabs need it).
 *
 * @return {Promise} Resolves with the filter module.
 */
function loadFilter() {
	if ( ! filterModule ) {
		filterModule = import(
			/* webpackChunkName: "carousel-filter" */
			'./filter'
		);
	}

	return filterModule;
}

/**
 * Keep the cards of each filtered carousel/grid as the server printed them, before Swiper
 * changes the slides, so the "All" tab can bring them back without a request.
 *
 * @param {ParentNode} root Element or document to search in.
 */
function stashFilterCards( root ) {
	root.querySelectorAll( '.wpcu-products' ).forEach( ( products ) => {
		const bar   = products.previousElementSibling && products.previousElementSibling.classList.contains( 'wpcu-filter' );
		const pager = products.querySelector( '.wpcu-pagination--ajax' );

		if ( ! bar && ! pager ) {
			return;
		}

		const cards = products.querySelector( '.swiper-wrapper, .wpcu-row, .wpcu-ticker__track' );

		if ( cards && typeof products.wpcuFilterHtml === 'undefined' ) {
			products.wpcuFilterHtml = cards.innerHTML;
		}
	} );
}

/**
 * Parse a JSON data attribute, falling back when it is missing or invalid.
 *
 * @param {string|undefined} value    Raw attribute value.
 * @param {*}                fallback Value used when parsing fails.
 * @return {*} Parsed value.
 */
function parseJSON( value, fallback ) {
	if ( typeof value === 'undefined' ) {
		return fallback;
	}

	try {
		return JSON.parse( value );
	} catch ( e ) {
		return fallback;
	}
}

/**
 * Parse an integer data attribute, falling back when it is missing or invalid.
 *
 * @param {string|undefined} value    Raw attribute value.
 * @param {number}           fallback Value used when parsing fails.
 * @return {number} Parsed value.
 */
function parseNumber( value, fallback ) {
	const number = parseInt( value, 10 );

	return Number.isNaN( number ) ? fallback : number;
}

/**
 * Create the Swiper instance for one `.wpcu-carousel` element.
 *
 * Settings come from the data-wpcu-* attributes printed by the shortcode.
 *
 * @param {HTMLElement} el Carousel element.
 */
function initCarousel( el ) {
	if ( el.swiper || el.dataset.wpcuInit ) {
		return;
	}
	el.dataset.wpcuInit = '1';

	const data          = el.dataset;
	const paginationEl  = el.querySelector( '.wpcu-carousel-pagination' );
	const isFade        = 'fade' === data.wpcuEffect;
	const createSwiper  = ( modules, fade ) => new Swiper( el, {
		modules,
		// Fade shows one slide; the shortcode prints one column at every breakpoint for it.
		...( fade ? { effect: 'fade', fadeEffect: { crossFade: true } } : {} ),
		slidesPerView:  parseNumber( data.wpcuItems, 4 ),
		spaceBetween:   parseNumber( data.wpcuMargin, 30 ),
		loop:           parseJSON( ( data.wpcuLoop || 'false' ).toLowerCase(), false ),
		slidesPerGroup: parseNumber( data.wpcuPerslide, 1 ),
		speed:          parseNumber( data.wpcuSpeed, 3000 ),
		autoplay:       parseJSON( data.wpcuAutoplay, false ),
		navigation: {
			nextEl: el.querySelector( '.wpcu-carousel-nav__btn--next' ),
			prevEl: el.querySelector( '.wpcu-carousel-nav__btn--prev' ),
		},
		pagination: {
			el:        paginationEl,
			type:      'bullets',
			clickable: true,
		},
		breakpoints: parseJSON( data.wpcuResponsive, {} ),
	} );

	// The pagination container is only printed when dots are enabled. If the chunk fails to
	// load, the carousel starts without dots and slides instead of fading.
	if ( paginationEl || isFade ) {
		loadSwiperModules()
			.then( ( { Pagination, EffectFade } ) => {
				const modules = [ Navigation, Autoplay ];
				if ( paginationEl ) {
					modules.push( Pagination );
				}
				if ( isFade ) {
					modules.push( EffectFade );
				}
				createSwiper( modules, isFade );
			} )
			.catch( () => createSwiper( [ Navigation, Autoplay ], false ) );
	} else {
		createSwiper( [ Navigation, Autoplay ], false );
	}
}

/**
 * Start one `.wpcu-ticker`: a row of cards scrolling at a constant speed, without Swiper.
 *
 * The cards are copied after the originals until the row is wider than the ticker plus one
 * set, and a CSS animation (see _general.scss) moves the row by exactly one set, so the loop
 * has no jump. The distance is measured again when the ticker's width changes. With reduced
 * motion nothing is copied and the row can be scrolled by hand instead.
 *
 * @param {HTMLElement} el Ticker element.
 */
function initTicker( el ) {
	if ( el.dataset.wpcuInit ) {
		return;
	}
	el.dataset.wpcuInit = '1';

	const track = el.querySelector( '.wpcu-ticker__track' );

	if ( el.wpcuTickerObserver ) {
		el.wpcuTickerObserver.disconnect(); // the cards were swapped by a category tab
	}
	if ( ! track || ! track.children.length || window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	const items = Array.from( track.children );
	const speed = parseNumber( el.dataset.wpcuSpeed, 2000 );
	let width   = 0;

	const fill = () => {
		if ( el.clientWidth === width ) {
			return;
		}
		width = el.clientWidth;

		track.querySelectorAll( '[data-wpcu-clone]' ).forEach( ( clone ) => clone.remove() );

		const first   = items[ 0 ].offsetLeft;
		const last    = items[ items.length - 1 ];
		const gap     = parseFloat( window.getComputedStyle( last ).marginRight ) || 0;
		const setSize = last.offsetLeft + last.offsetWidth + gap - first;

		if ( setSize <= 0 ) {
			return;
		}

		// Copies are for the eye only: hidden from screen readers and skipped by the Tab key.
		for ( let copies = Math.max( 1, Math.ceil( width / setSize ) ); copies > 0; copies-- ) {
			items.forEach( ( item ) => {
				const clone = item.cloneNode( true );
				clone.dataset.wpcuClone = '1';
				clone.setAttribute( 'aria-hidden', 'true' );
				clone.querySelectorAll( 'a, button, input, select, textarea, [tabindex]' ).forEach( ( focusable ) => {
					focusable.setAttribute( 'tabindex', '-1' );
				} );
				track.appendChild( clone );
			} );
		}

		el.style.setProperty( '--wpcu-tickerDistance', `-${ setSize }px` );
		el.style.setProperty( '--wpcu-tickerDuration', `${ items.length * speed }ms` );
		el.classList.add( 'wpcu-ticker--running' );
	};

	fill();

	if ( typeof window.ResizeObserver === 'function' ) {
		el.wpcuTickerObserver = new window.ResizeObserver( fill );
		el.wpcuTickerObserver.observe( el );
	}
}

/**
 * Initialise every carousel/grid inside `root`. Safe to call repeatedly.
 *
 * @param {ParentNode} root Element or document to search in.
 */
function init( root = document ) {
	// Style reset for the add-to-cart button wrapper.
	root.querySelectorAll( '.wpcu-button p.woocommerce' ).forEach( ( el ) => {
		el.setAttribute( 'style', 'none' );
	} );

	// Remove the old loading state (markup cached before the class was dropped from the shortcode).
	root.querySelectorAll( '.wpcu-products.wpcu-lazy-load' ).forEach( ( el ) => {
		el.classList.remove( 'wpcu-lazy-load' );
	} );

	const hasFilters = root.querySelector( '.wpcu-filter, .wpcu-pagination--ajax' );
	if ( hasFilters ) {
		stashFilterCards( root );
	}

	root.querySelectorAll( '.wpcu-carousel' ).forEach( initCarousel );
	root.querySelectorAll( '.wpcu-ticker' ).forEach( initTicker );

	if ( hasFilters ) {
		loadFilter()
			.then( ( { initFilters } ) => initFilters( root, init ) )
			.catch( () => {} ); // the tabs then simply do nothing
	}
}

/**
 * Re-run init for widgets rendered in the Elementor editor.
 *
 * Elementor fires its frontend init through jQuery, so jQuery is only used here
 * and only when Elementor provides it.
 */
function bindElementor() {
	const onElementorReady = () => {
		const { elementorFrontend } = window;

		if ( ! elementorFrontend || ! elementorFrontend.hooks ) {
			return;
		}

		elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', ( $scope ) => {
			init( $scope && $scope[ 0 ] ? $scope[ 0 ] : document );
		} );
	};

	if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
		onElementorReady();
	} else if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', onElementorReady );
	}
}

// Keep the global the prebuilt bundle exposed, without overriding another plugin's Swiper.
if ( ! window.Swiper ) {
	window.Swiper = Swiper;
}

// Public API for the block/Elementor previews and later features (AJAX filter).
window.wpcuCarousel = { init };

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', () => init() );
} else {
	init();
}

bindElementor();
