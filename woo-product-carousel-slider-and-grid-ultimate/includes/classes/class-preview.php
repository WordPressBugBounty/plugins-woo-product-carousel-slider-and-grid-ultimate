<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Live preview REST endpoint for the carousel edit screen.
 *
 * POST /wp-json/wcpcsu/v1/preview { post_id, settings }
 *
 * Renders the unsaved settings with the same renderer as the shortcode
 * (WCPCSU_Shortcode::render()), so the preview always matches the frontend.
 * Nothing is saved.
 */
class WCPCSU_Preview {

	const REST_NAMESPACE = 'wcpcsu/v1';

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			'/preview',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'preview' ),
				'permission_callback' => array( $this, 'can_preview' ),
				'args'                => array(
					'post_id'  => array(
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
					'settings' => array(
						'required' => false,
						'type'     => 'object',
						'default'  => array(),
					),
				),
			)
		);
	}

	/**
	 * Only users who can edit this carousel may preview it.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return bool|WP_Error
	 */
	public function can_preview( $request ) {
		$post_id = (int) $request['post_id'];

		if ( ! $post_id || WCPCSU_CUSTOM_POST_TYPE !== get_post_type( $post_id ) ) {
			return new WP_Error( 'wcpcsu_preview_invalid_post', __( 'Invalid carousel.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), array( 'status' => 404 ) );
		}

		return current_user_can( 'edit_post', $post_id );
	}

	/**
	 * Render the posted (unsaved) settings.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function preview( $request ) {
		$settings = $request['settings'];
		$settings = is_array( $settings ) ? $settings : array();

		// Sanitized exactly like WCPCSU_Meta_Box::update_meta_data() does on save.
		$settings = wcpcsu_sanitize_array( $settings );

		$shortcode = wc_pcsu_plugin()->shortcode;
		$atts      = shortcode_atts( $shortcode->shortcode_defaults(), array( 'id' => (int) $request['post_id'] ), WCPCSU_Shortcode::SHORTCODE_TAG );

		return rest_ensure_response(
			array(
				'html'   => $shortcode->render( $settings, $atts ),
				'assets' => $this->assets(),
			)
		);
	}

	/**
	 * CSS/JS the preview iframe needs to look and behave like the frontend.
	 *
	 * @return array { styles: string[], scripts: string[] }
	 */
	private function assets() {
		$plugin = wc_pcsu_plugin();
		$styles = array();

		// WooCommerce's frontend styles (prices, add-to-cart buttons, star ratings).
		if ( function_exists( 'WC' ) ) {
			$wc_css = WC()->plugin_url() . '/assets/css/';
			$styles[] = add_query_arg( 'ver', WC()->version, $wc_css . 'woocommerce-layout.css' );
			$styles[] = add_query_arg( 'ver', WC()->version, $wc_css . 'woocommerce.css' );
		}

		foreach ( array( 'assets/css/swiper-bundle.min.css', 'assets/css/swmodal.css', 'assets/css/style.css' ) as $path ) {
			$styles[] = add_query_arg( 'ver', $plugin->file_version( $path ), WCPCSU_URL . $path );
		}

		$asset_file = WCPCSU_DIR . 'build/carousel.asset.php';
		$asset      = file_exists( $asset_file ) ? require $asset_file : array( 'version' => false );

		return array(
			'styles'  => $styles,
			'scripts' => array( add_query_arg( 'ver', $asset['version'], WCPCSU_URL . 'build/carousel.js' ) ),
		);
	}
}
