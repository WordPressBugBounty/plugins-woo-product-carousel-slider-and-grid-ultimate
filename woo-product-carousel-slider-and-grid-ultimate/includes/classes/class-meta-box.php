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
