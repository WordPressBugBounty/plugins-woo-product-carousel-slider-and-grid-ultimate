<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Metabox class.
 */
class WCPCSU_Meta_Box {

	public function __construct () {
		if ( is_admin() ) {
			add_action( 'add_meta_boxes_' . WCPCSU_CUSTOM_POST_TYPE, array( $this, 'register_meta_box' ) );
			add_action( 'edit_post', array( $this, 'update_meta_data' ) );
		}
		add_filter( 'wp_insert_post_data', array( $this, 'insert_post_data' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
	}

	public function post_row_actions( $actions, $post ) {
		// Check if the post type is your custom post type
		if ( 'wcpcsu-custom-post' === get_post_type( $post ) ) {
			// Remove the 'View' action
			unset( $actions['view'] );
		}
		return $actions;
	}

	public function insert_post_data( $data, $postarr ) {
		if ( 'wcpcsu-custom-post' === $data['post_type'] ) {
			$data['post_title'] = sanitize_text_field( $data['post_title'] );
		}
		return $data;
	}

	public function register_meta_box() {
		add_meta_box(
			'wcpcsu_meta_box',
			__( 'Settings & Shortcode Generator', 'woocommerce-product-carousel-slider-and-grid-ultimate' ),
			array( $this, 'show_meta_box' ),
			WCPCSU_CUSTOM_POST_TYPE,
			'normal'
		);

		add_meta_box(
			'wcpcsu_preview_meta_box',
			__( 'Live Preview', 'woocommerce-product-carousel-slider-and-grid-ultimate' ),
			array( $this, 'show_preview_meta_box' ),
			WCPCSU_CUSTOM_POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Live preview panel. admin/js/wcpcsu-preview.js renders the unsaved form
	 * values into the iframe through the WCPCSU_Preview REST endpoint.
	 */
	public function show_preview_meta_box() {
		// Viewport widths match the breakpoints in data-wpcu-responsive and style.css (768/992/1200).
		$devices = array(
			'desktop' => array( 1280, 'dashicons-desktop', __( 'Desktop', 'woocommerce-product-carousel-slider-and-grid-ultimate' ) ),
			'laptop'  => array( 1024, 'dashicons-laptop', __( 'Laptop', 'woocommerce-product-carousel-slider-and-grid-ultimate' ) ),
			'tablet'  => array( 768, 'dashicons-tablet', __( 'Tablet', 'woocommerce-product-carousel-slider-and-grid-ultimate' ) ),
			'mobile'  => array( 375, 'dashicons-smartphone', __( 'Mobile', 'woocommerce-product-carousel-slider-and-grid-ultimate' ) ),
		);
		?>
		<div class="wcpcsu-preview">
			<div class="wcpcsu-preview__toolbar">
				<div class="wcpcsu-preview__devices" role="group" aria-label="<?php esc_attr_e( 'Preview device', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">
					<?php foreach ( $devices as $device => $info ) { ?>
						<button type="button" class="wcpcsu-preview__device<?php echo 'desktop' === $device ? ' is-active' : ''; ?>" data-width="<?php echo esc_attr( $info[0] ); ?>" aria-pressed="<?php echo 'desktop' === $device ? 'true' : 'false'; ?>">
							<span class="dashicons <?php echo esc_attr( $info[1] ); ?>" aria-hidden="true"></span>
							<?php echo esc_html( $info[2] ); ?>
						</button>
					<?php } ?>
				</div>
				<span class="wcpcsu-preview__status" aria-live="polite"></span>
			</div>
			<div class="wcpcsu-preview__stage">
				<div class="wcpcsu-preview__frame-wrap">
					<iframe class="wcpcsu-preview__frame" title="<?php esc_attr_e( 'Carousel preview', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>"></iframe>
				</div>
			</div>
			<p class="wcpcsu-preview__note">
				<?php esc_html_e( 'Shows your unsaved changes. Links and buttons are disabled here, and your theme\'s fonts and styles are not applied.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
			</p>
		</div>
		<?php
	}

	public function show_meta_box( $post ) {
		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'wcpscu_action', 'wcpscu_nonce' );

		$lcg_svalue = get_post_meta( $post->ID, 'wcpscu', true );
		$s_value    = WCPCSU_Main::json_decoded( $lcg_svalue );
		$value      = is_array( $s_value ) ? $s_value : array();

		extract( $value );

		require_once WCPCSU_INC_DIR . 'settings/settings.php';
	}

	public function update_meta_data( $post_id ) {
		// vail if the security check fails
		if ( ! $this->wcpscu_security_check( 'wcpscu_nonce', 'wcpscu_action', $post_id ) ) {
			return;
		}

		// save the meta data if it is our post type lcg_mainpost post type
		if ( empty( $_POST['post_type'] ) || WCPCSU_CUSTOM_POST_TYPE !== $_POST['post_type'] ) {
			return;
		}

		if ( ! empty( $_POST['wcpscu'] ) ) {
			$wcpscu = WCPCSU_Main::json_encoded( wcpcsu_sanitize_array( $_POST['wcpscu'] ) );
			//save the meta value
			update_post_meta( $post_id, 'wcpscu', $wcpscu );
		} else {
			delete_post_meta( $post_id, 'wcpscu' );
		}
	}

	//security check
	private function wcpscu_security_check( $nonce_name, $action, $post_id ) {
		// checks are divided into 3 parts for readability.
		if ( empty( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( $_POST[ $nonce_name ], $action ) ) {
			return false;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything. returns false
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
	}
}
