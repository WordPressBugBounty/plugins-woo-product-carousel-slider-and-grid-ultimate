<?php
/*
Plugin Name: Product Carousel Slider & Grid Ultimate for WooCommerce
Plugin URI:  https://wpwax.com/product/woocommerce-product-carousel-slider-grid-ultimate-pro
Description: It is a fully responsive and mobile friendly WooCommerce Product Carousel, Slider and Grid plugin which comes with lots of features.
Version:     1.14.0
Author:      wpWax
Author URI:  https://wpwax.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages/
Text Domain: woo-product-carousel-slider-and-grid-ultimate
WC requires at least: 3.0
WC tested up to: 11.2
 */
defined('ABSPATH') || die('Direct access is not allow');

if( ! in_array('woocommerce-product-carousel-slider-grid-ultimate-pro/main.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {

    /**
     * Main Woocommerce Product Carousel Slider Ultimate class.
     *
     * @since 1.0.0
     */
    Final class WCPCSU_Main
    {

        /**
        *
        * @since 1.0.0
        */
        private static $instance;

        /**
         * all metabox
         * @since 1.0.0
         */
        public $metabox;

        /**
         *custom post
        *@since 1.0.0
        */
        public $custom_post;

        /**
         *all shortcode
        *@since 1.0.0
        */
        public $shortcode;

        /**
         * Live preview REST endpoint (WCPCSU_Preview).
         */
        public $preview;

        /**
         * Public products REST endpoint for the AJAX filters (WCPCSU_Products_Route).
         */
        public $products_route;

        /**
         * Main WCPCSU_Main Instance.
         *
         *
         * @since 1.0
         * @static
         * @static_var array $instance
         * @uses instanceof::adl_constants() Setup the constants needed.
         * @uses instanceof::wcpcsu_include() Include the required files.
         * @uses instanceof::wcpcsu_load_textdomain() load the language files.
         * @return object|WCPCSU_Main The one true WCPCSU_Main
         */
        public static function instance() {
            if(!isset(self::$instance) && !(self::$instance instanceof WCPCSU_Main)) {
                self::$instance = new WCPCSU_Main;
                //if woocmmerce plugin not activate
                if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                    add_action( 'admin_notices', array(self::$instance, 'WCPCSU_admin_notice') );
                }
                self::$instance->adl_constants();
                add_action('init',array( self::$instance,'wcpcsu_load_textdomain' ) );
                add_action('admin_enqueue_scripts',array(self::$instance, 'wcpcsu_enqueue_file'));
                add_action('template_redirect',array(self::$instance, 'template_enqueue_file'));
                add_action('wp_enqueue_scripts',array(self::$instance, 'enqueue_for_current_page'));
                add_action('admin_menu',array(self::$instance,'upgrade_to_pro'));

                add_action( 'elementor/preview/enqueue_styles', [ self::$instance, 'el_preview_style' ] );
                add_action( 'elementor/preview/enqueue_scripts', [ self::$instance, 'el_preview_script' ] );

                add_action( 'enqueue_block_editor_assets', [ self::$instance, 'enqueue_block_editor_assets' ] );

                if( empty( get_option('wcpcsu_dismiss_discount_notice') ) ) {
                    add_action( 'admin_notices', array( self::$instance, 'admin_notices') );
                }

                if( empty( get_option('migrate_serialize_to_json') ) ) {
                    add_action( 'admin_init', array( self::$instance, 'migrate_serialize_data') );
                }
                self::$instance->wcpcsu_include();
                self::$instance->custom_post = new Wcpcsu_Custom_Post();
                self::$instance->metabox = new Wcpcsu_Meta_Box();
                self::$instance->shortcode = new wcpcsu_Shortcode();
                self::$instance->preview = new WCPCSU_Preview();
                self::$instance->products_route = new WCPCSU_Products_Route();
                // Initialize appsero tracking
                self::$instance->init_appsero();
                add_action( 'before_woocommerce_init', array( self::$instance, 'declare_woocommerce_feature_compatibility' ) );
            }

            return self::$instance;
        }
        
        public function declare_woocommerce_feature_compatibility() {
            if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WCPCSU_FILE, true );
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', WCPCSU_FILE, false );
            }
        }

        public function migrate_serialize_data() {
            
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            $args = array(
                'post_type'      => WCPCSU_CUSTOM_POST_TYPE,
                'post_status'    => 'any',
                'posts_per_page' => -1,
            );

            $query = new WP_Query( $args );
            
            // Check if there are posts in the query
            if ( $query->have_posts() ) {
                // Loop through each post
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $wcpscu_data = get_post_meta( get_the_ID(), 'wcpscu', true );
                    
                    if ( ! empty( $wcpscu_data ) && ! is_json_encoded( $wcpscu_data ) ) {
                        $unserialized_data = unserialize( base64_decode( $wcpscu_data ) );
                        
                        $json_decode_data = WCPCSU_Main::json_encoded( $unserialized_data );
                        update_post_meta( get_the_ID(), 'wcpscu', $json_decode_data );
                    }
                }

                // Restore the global post data
                wp_reset_postdata();
            }
            update_option( 'migrate_serialize_to_json', true );

        }

        public function admin_notices() {
            global $pagenow, $typenow;
            if ( 'index.php' == $pagenow || 'plugins.php' == $pagenow || 'wcpcsu-custom-post' == $typenow ) {
                require_once WCPCSU_INC_DIR . '/notice.php';
            }
        }

        /**
         * Setup plugin constants.
         * @access private
         * @since 1.0
         * @return void
         */
        public function adl_constants() {
            // Plugin Folder Path.
            if ( ! defined( 'WCPCSU_DIR' ) ) { define( 'WCPCSU_DIR', plugin_dir_path( __FILE__ ) ); }
            // Plugin Folder URL.
            if ( ! defined( 'WCPCSU_URL' ) ) { define( 'WCPCSU_URL', plugin_dir_url( __FILE__ ) ); }
            // Plugin Root File.
            if ( ! defined( 'WCPCSU_FILE' ) ) { define( 'WCPCSU_FILE', __FILE__ ); }
            if ( ! defined( 'WCPCSU_BASE' ) ) { define( 'WCPCSU_BASE', plugin_basename( __FILE__ ) ); }
            // Plugin Text domain File.
            if ( ! defined( 'WCPCSU_TEXTDOMAIN' ) ) { define( 'WCPCSU_TEXTDOMAIN', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); }
            // Plugin Includes Path
            if ( !defined('WCPCSU_INC_DIR') ) { define('WCPCSU_INC_DIR', WCPCSU_DIR.'includes/'); }
            // Plugin Language File Path
            if ( !defined('WCPCSU_LANG_DIR') ) { define('WCPCSU_LANG_DIR', dirname(plugin_basename( __FILE__ ) ) . '/languages'); }
            //custom post type id
            if ( !defined('WCPCSU_CUSTOM_POST_TYPE') ) { define('WCPCSU_CUSTOM_POST_TYPE', 'wcpcsu-custom-post'); }
        }

        public function upgrade_to_pro () {
            add_submenu_page( 'edit.php?post_type=wcpcsu-custom-post', esc_html__( 'Support', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), esc_html__( 'Usage & Support', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 'manage_options', 'support', array( self::$instance, 'support_view' ) );
        }

        public function support_view () {
            include WCPCSU_INC_DIR . 'upgrade-pro.php';
        }

        /**
         * plugin text domain
         */
        public function wcpcsu_load_textdomain()
        {
            // Determine the current locale
            $locale = determine_locale();
            // Allow filters to modify the locale
            $locale = apply_filters( 'plugin_locale', $locale, 'woocommerce-product-carousel-slider-and-grid-ultimate' );
            load_textdomain( 'woocommerce-product-carousel-slider-and-grid-ultimate', WP_LANG_DIR . '/plugins/woocommerce-product-carousel-slider-and-grid-ultimate-' . $locale . '.mo' );
            load_plugin_textdomain( 'woocommerce-product-carousel-slider-and-grid-ultimate', false, WCPCSU_LANG_DIR );
        }

        /**
         * include all require files
         *
         * @access private
         * @since 1.0.0
         * @return void
         */
        public function wcpcsu_include() {
            require_once WCPCSU_INC_DIR . 'gutenberg/init.php';
            require_once WCPCSU_INC_DIR . 'elementor/init.php';
            require_once WCPCSU_INC_DIR . 'helper-functions.php';
            wpcsu_load_dependencies( 'all', WCPCSU_INC_DIR . 'classes/' );
        }

        public function WCPCSU_admin_notice() { ?>
            <div class="error">
                <p>
                    <?php
                    printf('%s <strong>%s</strong>', esc_html__('WooCommerce plugin is not activated. Please install and activate it to use', 'woocommerce-product-carousel-slider-and-grid-ultimate'), esc_html__('WooCommerce Product Carousel Slider Ultimate Plugin', 'woocommerce-product-carousel-slider-and-grid-ultimate') );
                    ?>
                </p>
            </div>
        <?php
        }

        public function wcpcsu_enqueue_file () {
            global $typenow, $pagenow;

            if( $typenow == WCPCSU_CUSTOM_POST_TYPE ) {
                wp_enqueue_style('wcpcsu-admin-cmb2', WCPCSU_URL . 'admin/css/cmb2.min.css');
                wp_enqueue_style('wcpcsu-admin', WCPCSU_URL . 'admin/css/wcpcsu-admin.css', array(), $this->file_version( 'admin/css/wcpcsu-admin.css' ) );
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker');
                // Handpicked products picker: WooCommerce's product search (selectWoo) + jQuery UI sortable.
                $admin_deps = array( 'jquery', 'jquery-ui-sortable' );
                if ( function_exists( 'WC' ) ) {
                    $admin_deps[] = 'wc-enhanced-select';
                    wp_enqueue_style( 'woocommerce_admin_styles' );
                }
                wp_enqueue_script( 'wcpcsu-admin-js', WCPCSU_URL . 'admin/js/wcpcs-admin.js', $admin_deps, $this->file_version( 'admin/js/wcpcs-admin.js' ) );
                // Section menus and help tooltips of the settings tabs (the same file in Free and Pro).
                wp_enqueue_script( 'wcpcsu-settings-ui-js', WCPCSU_URL . 'admin/js/wcpcsu-settings-ui.js', array( 'jquery', 'wcpcsu-admin-js' ), $this->file_version( 'admin/js/wcpcsu-settings-ui.js' ), true );
                wp_localize_script( 'wcpcsu-settings-ui-js', 'wcpcsuSettingsUi', array(
                    'help'     => __( 'Help', 'woocommerce-product-carousel-slider-and-grid-ultimate' ),
                    'sections' => __( 'Settings sections', 'woocommerce-product-carousel-slider-and-grid-ultimate' ),
                ) );

                // Live preview panel on the edit screen (WCPCSU_Preview REST endpoint).
                if ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) {
                    wp_enqueue_script( 'wcpcsu-preview-js', WCPCSU_URL . 'admin/js/wcpcsu-preview.js', array( 'jquery', 'wcpcsu-admin-js' ), $this->file_version( 'admin/js/wcpcsu-preview.js' ), true );
                    wp_localize_script( 'wcpcsu-preview-js', 'wcpcsuPreview', array(
                        'restUrl' => esc_url_raw( rest_url( WCPCSU_Preview::REST_NAMESPACE . '/preview' ) ),
                        'nonce'   => wp_create_nonce( 'wp_rest' ),
                        'i18n'    => array(
                            'updating' => __( 'Updating preview…', 'woocommerce-product-carousel-slider-and-grid-ultimate' ),
                            'error'    => __( 'The preview could not be loaded.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ),
                            'expired'  => __( 'Your session has expired. Reload the page to update the preview.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ),
                        ),
                    ) );
                }
            }

            if ( 'index.php' == $pagenow || 'plugins.php' == $pagenow || 'wcpcsu-custom-post' == $typenow ) {
                wp_enqueue_style('wcpcsu-notice', WCPCSU_URL . 'admin/css/wcpcsu-notice.css');
            }

        }

        public function template_enqueue_file () {
            wp_register_style( 'wcpcsu-main', WCPCSU_URL . 'assets/css/style.css', array(), $this->file_version( 'assets/css/style.css' ) );
            wp_register_style( 'wcpcsu-swmodal', WCPCSU_URL . 'assets/css/swmodal.css', array(), $this->file_version( 'assets/css/swmodal.css' ) );
            wp_register_style( 'wcpcsu-swiper', WCPCSU_URL . 'assets/css/swiper-bundle.min.css', array(), $this->file_version( 'assets/css/swiper-bundle.min.css' ) );

            // Carousel (Swiper 11 + init, no jQuery) and quick view, built from src/frontend/.
            $this->register_build_script( 'wcpcsu-main-js', 'carousel' );
            $this->register_build_script( 'wcpcsu-swmodal-js', 'quick-view' );

            // Kept for backward compatibility: Swiper is now bundled in the carousel script.
            wp_register_script( 'wcpcsu-swiper-js', false, array( 'wcpcsu-main-js' ), $this->file_version( 'build/carousel.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );

            wp_localize_script('wcpcsu-swmodal-js','wcpcsu_quick_view',array(
                'ajax_url'           => admin_url( 'admin-ajax.php' ),

            ));
        }

        /**
         * Enqueue the carousel CSS in <head> when the current page contains a carousel.
         *
         * Without this, styles enqueued while the shortcode renders are printed late in
         * the footer on classic themes, so the carousel can flash unstyled. Carousels
         * outside the post content (widgets, templates) still get their assets from the
         * shortcode itself.
         */
        public function enqueue_for_current_page() {
            if ( ! is_singular() ) {
                return;
            }

            $post = get_queried_object();
            if ( ! $post instanceof WP_Post ) {
                return;
            }

            $has_carousel = has_shortcode( $post->post_content, 'wcpcsu' ) || has_block( 'wcpcsup/block', $post );

            // Elementor stores its layout in post meta, not in post_content.
            if ( ! $has_carousel ) {
                $elementor_data = get_post_meta( $post->ID, '_elementor_data', true );
                $has_carousel   = is_string( $elementor_data ) && ( false !== strpos( $elementor_data, '"widgetType":"woocommerce_ultimate"' ) || false !== strpos( $elementor_data, '[wcpcsu' ) );
            }

            if ( $has_carousel ) {
                wp_enqueue_style( 'wcpcsu-main' );
                wp_enqueue_style( 'wcpcsu-swiper' );
            }
        }

        /**
         * Register a script built by wp-scripts into build/, using its generated asset file.
         *
         * @param string $handle Script handle.
         * @param string $name   Entry name (build/{$name}.js).
         */
        public function register_build_script( $handle, $name ) {
            $asset_file = WCPCSU_DIR . 'build/' . $name . '.asset.php';
            $asset      = file_exists( $asset_file ) ? require $asset_file : array( 'dependencies' => array(), 'version' => false );

            wp_register_script(
                $handle,
                WCPCSU_URL . 'build/' . $name . '.js',
                $asset['dependencies'],
                $asset['version'],
                array( 'in_footer' => true, 'strategy' => 'defer' )
            );
        }

        /**
         * Cache-busting version for a plugin file: its modification time.
         *
         * @param string $path Path relative to the plugin folder.
         * @return int|false
         */
        public function file_version( $path ) {
            return file_exists( WCPCSU_DIR . $path ) ? filemtime( WCPCSU_DIR . $path ) : false;
        }

        public function el_preview_style() {
            // Same handles as the frontend, so a page never loads the assets twice.
            wp_enqueue_style( 'wcpcsu-main' );
            wp_enqueue_style( 'wcpcsu-swmodal' );
            wp_enqueue_style( 'wcpcsu-swiper' );
		}

        public function el_preview_script() {
            wp_enqueue_script( 'wcpcsu-main-js' );
            wp_enqueue_script( 'wcpcsu-swmodal-js' );
		}

        public function enqueue_block_editor_assets() {
            wp_enqueue_style( 'wcpcsup-block-editor', WCPCSU_URL . 'admin/css/block-editor.css' );
        }

        /**
         * Encodes a PHP value into its JSON representation.
         * @param $data
         * @return string
         */
        public static function json_encoded( $data ) {
            return json_encode( $data );
        }

        /**
         * Decodes a JSON-encoded string into a PHP associative array.
         * @param string $data The JSON-encoded string to be decoded.
         * @return array Returns the decoded PHP associative array on success, or an empty array on failure.
         */
        public static function json_decoded( $data ) {
        
            $decoded_data = json_decode( $data, true );

            
            if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded_data ) ) {
                return $decoded_data;
            } else {
                return array();
            }
        }

        /**
         * Initialize appsero tracking.
         *
         * @see https://github.com/Appsero/client
         *
         * @return void
         */
        public function init_appsero() {
            if ( ! class_exists( '\Appsero\Client' ) ) {
                require_once WCPCSU_INC_DIR . 'appsero/src/Client.php';
            }

            $client = new \Appsero\Client( 'a39a0a19-5945-4527-84b0-a13bcfac1faa', 'Product Carousel Slider & Grid Ultimate for WooCommerce', __FILE__ );

            // Active insights
            $client->insights()->init();
        }

    } //end of class

    function wc_pcsu_plugin() {
        return WCPCSU_Main::instance();
    }

    // Get WCPCSU ( Woocommerce Product Carousel Slider Ultimate plugin ) Running.
    if( ! class_exists('Woocmmerce_Product_carousel_slider_ultimate_Pro') ){
        wc_pcsu_plugin();
    }

    function wc_pcsu_image_cropping( $attachmentId, $width, $height, $crop = true, $quality = 100 )
    {
        $resizer = new Wpcsu_Image_Resizer( $attachmentId );

        return $resizer->resize( $width, $height, $crop, $quality );
    }

}