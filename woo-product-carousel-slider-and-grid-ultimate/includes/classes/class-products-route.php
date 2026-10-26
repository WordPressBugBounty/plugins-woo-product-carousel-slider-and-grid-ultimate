<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public REST endpoint for the frontend AJAX filters (category tabs).
 *
 * GET /wp-json/wcpcsu/v1/products?q=…&sig=…&category=…&page=…
 *
 * - `q` is the carousel's shortcode attributes (base64url JSON), printed on the page by render().
 *   Blocks and Elementor widgets have no saved carousel: all their settings are attributes,
 *   so the attributes, not just the ID, are needed to render the same products.
 * - `sig` is an HMAC of `q` with the site's salts. Visitors can't change the attributes;
 *   they only choose `category` (one of the carousel's tabs) and `page`.
 * - No nonce, no user: the same URL always gives the same answer, so it can be cached.
 *
 * Returns { html, page, max_pages, found }. `html` holds only the product cards
 * (WCPCSU_Shortcode::render() in cards-only mode), the same markup as on the page.
 */
class WCPCSU_Products_Route {

	const REST_NAMESPACE = 'wcpcsu/v1';

	/** Longest accepted `q`, in characters. */
	const MAX_QUERY_LENGTH = 6000;

	/** Highest accepted `page`. */
	const MAX_PAGE = 100;

	/** Product types whose products don't make sense split by category: no tabs. */
	const NO_TAB_TYPES = array( 'handpicked', 'related' );

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			'/products',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'products' ),
				'permission_callback' => '__return_true', // Public; the signature limits what can be asked.
				'args'                => array(
					'q'        => array(
						'required'  => true,
						'type'      => 'string',
						'maxLength' => self::MAX_QUERY_LENGTH,
						'pattern'   => '^[A-Za-z0-9_-]+$',
					),
					'sig'      => array(
						'required' => true,
						'type'     => 'string',
						'pattern'  => '^[a-f0-9]{32}$',
					),
					'category' => array(
						'type'      => 'string',
						'default'   => '',
						'maxLength' => 200,
					),
					'page'     => array(
						'type'    => 'integer',
						'default' => 1,
						'minimum' => 1,
						'maximum' => self::MAX_PAGE,
					),
				),
			)
		);
	}

	/**
	 * The `q` and `sig` values for a carousel's attributes, for the page to send back.
	 *
	 * @param array $atts Shortcode attributes, already passed through shortcode_atts().
	 * @return array { q: string, sig: string }
	 */
	public static function sign( $atts ) {
		// Empty attributes are their defaults anyway; leaving them out keeps the URL short.
		$atts = array_filter( (array) $atts, static function ( $value ) {
			return '' !== $value && null !== $value && 0 !== $value && '0' !== $value;
		} );
		$q    = rtrim( strtr( base64_encode( wp_json_encode( $atts ) ), '+/', '-_' ), '=' );

		return array(
			'q'   => $q,
			'sig' => wp_hash( $q, 'nonce' ),
		);
	}

	/**
	 * The categories a carousel shows as tabs, in order; empty when tabs are off.
	 *
	 * Shared with render(), so the tabs on the page and the categories the route accepts
	 * always match.
	 *
	 * @param array $data_array Settings (the decoded `wcpscu` meta).
	 * @param array $atts       Shortcode attributes, already passed through shortcode_atts().
	 * @return string[] product_cat slugs.
	 */
	public static function tab_categories( $data_array, $atts ) {
		$setting = function ( $key, $default = '' ) use ( $data_array, $atts ) {
			if ( ! empty( $atts[ $key ] ) ) {
				return $atts[ $key ];
			}
			return ! empty( $data_array[ $key ] ) ? $data_array[ $key ] : $default;
		};

		if ( 'yes' !== $setting( 'filter_tabs', 'no' ) || in_array( $setting( 'products_type', 'latest' ), self::NO_TAB_TYPES, true ) ) {
			return array();
		}

		return wcpcsu_parse_slug_list( $setting( 'filter_categories', array() ) );
	}

	/**
	 * Page buttons for Ajax-number pagination.
	 *
	 * The page prints them as links (no-JS fallback); the route prints buttons, because it
	 * doesn't know the page URL. Both carry `data-wpcu-page`, which the frontend handles.
	 *
	 * @param int    $current   Current page.
	 * @param int    $max_pages Total pages.
	 * @param string $base_url  Optional. URL template with `%#%` for the page number.
	 * @return string
	 */
	public static function pagination_html( $current, $max_pages, $base_url = '' ) {
		$current   = max( 1, absint( $current ) );
		$max_pages = absint( $max_pages );

		if ( $max_pages <= 1 ) {
			return '';
		}

		$out = '<nav class="pagination" aria-label="' . esc_attr__( 'Products pagination', 'woocommerce-product-carousel-slider-and-grid-ultimate' ) . '"><div class="nav-links">';

		foreach ( self::pagination_pages( $current, $max_pages ) as $page ) {
			if ( 'dots' === $page ) {
				$out .= '<span class="page-numbers dots">&hellip;</span>';
			} elseif ( (int) $page === $current ) {
				$out .= '<span class="page-numbers current" aria-current="page">' . esc_html( $page ) . '</span>';
			} elseif ( $base_url ) {
				$out .= '<a class="page-numbers" href="' . esc_url( str_replace( '%#%', $page, $base_url ) ) . '" data-wpcu-page="' . esc_attr( $page ) . '">' . esc_html( $page ) . '</a>';
			} else {
				$out .= '<button type="button" class="page-numbers" data-wpcu-page="' . esc_attr( $page ) . '">' . esc_html( $page ) . '</button>';
			}
		}

		return $out . '</div></nav>';
	}

	/**
	 * Page numbers with ellipses: all pages up to 7, otherwise 1 … current±1 … last.
	 *
	 * @param int $current   Current page.
	 * @param int $max_pages Total pages.
	 * @return array<int|string>
	 */
	private static function pagination_pages( $current, $max_pages ) {
		if ( $max_pages <= 7 ) {
			return range( 1, $max_pages );
		}

		$pages = array( 1 );
		$start = max( 2, $current - 1 );
		$end   = min( $max_pages - 1, $current + 1 );

		// An ellipsis never hides a single page: 1 2 3 [4] is shorter to read than 1 … 3 [4].
		if ( 3 === $start ) {
			$start = 2;
		}
		if ( $max_pages - 2 === $end ) {
			$end = $max_pages - 1;
		}

		if ( $start > 2 ) {
			$pages[] = 'dots';
		}

		for ( $page = $start; $page <= $end; $page++ ) {
			$pages[] = $page;
		}

		if ( $end < $max_pages - 1 ) {
			$pages[] = 'dots';
		}

		$pages[] = $max_pages;

		return $pages;
	}

	/**
	 * A setting's value: the shortcode attribute wins, then the saved meta, then the default.
	 *
	 * @param array  $data_array Saved settings.
	 * @param array  $atts       Shortcode attributes.
	 * @param string $key        Setting key.
	 * @param mixed  $default    Default value.
	 * @return mixed
	 */
	private function setting( $data_array, $atts, $key, $default = '' ) {
		if ( isset( $atts[ $key ] ) && '' !== $atts[ $key ] ) {
			return $atts[ $key ];
		}

		return isset( $data_array[ $key ] ) && '' !== $data_array[ $key ] ? $data_array[ $key ] : $default;
	}

	/**
	 * Render one page of a carousel's cards, optionally in one of its tab categories.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function products( $request ) {
		// Only the documented arguments: anything else would only split the cache.
		$unknown = array_diff( array_keys( $request->get_query_params() ), array( 'q', 'sig', 'category', 'page', 'rest_route' ) );
		$unknown = array_filter( $unknown, static function ( $key ) {
			return '_' !== substr( (string) $key, 0, 1 ); // WordPress' own (_locale, _fields, …).
		} );
		if ( $unknown ) {
			return $this->error( 'wcpcsu_products_unknown_arg', __( 'Unknown argument.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 400 );
		}

		$q = (string) $request['q'];
		if ( ! hash_equals( wp_hash( $q, 'nonce' ), (string) $request['sig'] ) ) {
			return $this->error( 'wcpcsu_products_invalid_query', __( 'Invalid carousel.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 400 );
		}

		$decoded   = json_decode( base64_decode( strtr( $q, '-_', '+/' ) ), true );
		$shortcode = wc_pcsu_plugin()->shortcode;
		$atts      = shortcode_atts( $shortcode->shortcode_defaults(), is_array( $decoded ) ? $decoded : array(), WCPCSU_Shortcode::SHORTCODE_TAG );
		$post_id   = absint( $atts['id'] );

		// A saved carousel must still exist and be published.
		$data_array = array();
		if ( $post_id ) {
			if ( WCPCSU_CUSTOM_POST_TYPE !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
				return $this->error( 'wcpcsu_products_invalid_post', __( 'Invalid carousel.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 404 );
			}
			$data_array = WCPCSU_Main::json_decoded( get_post_meta( $post_id, 'wcpscu', true ) );
		}

		$category = (string) $request['category'];
		if ( '' !== $category && ! in_array( $category, self::tab_categories( $data_array, $atts ), true ) ) {
			return $this->error( 'wcpcsu_products_invalid_category', __( 'This category is not available in this carousel.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 400 );
		}

		$page = (int) $request['page'];
		$html = $shortcode->render(
			$data_array,
			$atts,
			array(
				'cards_only' => true,
				'paged'      => $page,
				'category'   => $category,
			)
		);
		$loop = $shortcode->last_query;

		// Ajax-number pagination: only a paginated grid (or list) returns the page buttons.
		$pagination_type = $this->setting( $data_array, $atts, 'grid_pagination_type', 'normal' );
		$is_ajax_grid    = in_array( $this->setting( $data_array, $atts, 'layout', 'carousel' ), array( 'grid', 'list' ), true )
			&& 'yes' === $this->setting( $data_array, $atts, 'grid_pagination', 'no' )
			&& 'ajax_numbers' === $pagination_type;

		return rest_ensure_response(
			array(
				'html'       => $html,
				'page'       => $page,
				'max_pages'  => $loop ? (int) $loop->max_num_pages : 0,
				'found'      => $loop ? (int) $loop->found_posts : 0,
				'pagination' => $is_ajax_grid ? self::pagination_html( $page, $loop ? (int) $loop->max_num_pages : 0 ) : '',
			)
		);
	}

	/**
	 * @param string $code    Error code.
	 * @param string $message Message.
	 * @param int    $status  HTTP status.
	 * @return WP_Error
	 */
	private function error( $code, $message, $status ) {
		return new WP_Error( $code, $message, array( 'status' => $status ) );
	}
}
