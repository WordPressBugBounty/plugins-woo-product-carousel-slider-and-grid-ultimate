<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode class.
 */
class WCPCSU_Shortcode {

	const SHORTCODE_TAG = 'wcpcsu';

	/**
	 * The WP_Query of the last render(), so callers can read found_posts / max_num_pages.
	 *
	 * @var WP_Query|null
	 */
	public $last_query = null;

	public function __construct () {
		add_shortcode( self::SHORTCODE_TAG, array( $this,'register' ) );
		add_action( 'wp_ajax_ajax_quick_view', array( $this, 'ajax_quick_view' ) );
		add_action( 'wp_ajax_nopriv_ajax_quick_view', array( $this, 'ajax_quick_view') );
	}

	/**
	 * Attributes the shortcode accepts, with their defaults. Anything else is dropped.
	 *
	 * @return array
	 */
	public function shortcode_defaults() {
		return array(
			'id' 						=>  0,
			'layout'                    => '',
            'theme'                     => '',
            'total_products'            => '',
            'h_title_show'              => '',
            'header_title'              => '',
            'header_position'           => '',
            'display_title'             => '',
            'products_type'             => '',
            'display_sale_ribbon'       => '',
            'sale_ribbon_text'          => '',
            'sale_ribbon_position'      => '',
            'display_featured_ribbon'   => '',
            'feature_ribbon_text'       => '',
            'featured_ribbon_position'  => '',
            'display_sold_out_ribbon'   => '',
            'sold_out_ribbon_text'      => '',
            'sold_out_ribbon_position'  => '',
            'display_discount_ribbon'   => '',
            'discount_ribbon_position'  => '',
            'display_price'             => '',
            'display_ratings'           => '',
            'display_cart'              => '',
            'img_crop'                  => '',
            'img_hover_swap'            => '',
            'crop_image_width'          => '',
            'crop_image_height'         => '',
            'auto_play'                 => '',
            'repeat_product'            => '',
            'stop_hover'                => '',
            'scrool_direction'          => '', // right_left | left_right (ticker only in Free; same key and values as Pro)
            'c_desktop'                 => '',
            'c_desktop_small'           => '',
            'c_tablet'                  => '',
            'c_mobile'                  => '',
            'slide_speed'               => '',
            'carousel_effect'           => '', // slide | fade (Pro: coverflow, flip)
            'slide_time'                => '',
            'nav_show'                  => '',
            'nav_position'              => '',
            'nav_arrow_color'           => '',
            'nav_back_color'            => '',
            'nav_border_color'          => '',
            'nav_arrow_hover_color'     => '',
            'nav_back_hover_color'      => '',
            'nav_border_hover'          => '',
            'carousel_pagination'       => '',
            'dots_color'                => '',
            'dots_active_color'         => '',
            'g_column'                  => '',
            'g_tablet'                  => '',
            'g_mobile'                  => '',
            'grid_pagination'           => '',
            'grid_pagination_type'      => '', // normal | ajax_numbers (grid only)
            'pagi_color'                => '',
            'pagi_border_color'         => '',
            'pagi_back_color'           => '',
            'pagi_hover_color'          => '',
            'pagi_hover_border_color'   => '',
            'pagi_hover_back_color'     => '',
            'pagi_active_border_color'  => '',
            'pagi_active_back_color'    => '',
            'header_font_size'          => '',
            'header_font_color'         => '',
            'title_font_size'           => '',
            'title_font_color'          => '',
            'title_hover_font_color'    => '',
            'price_font_size'           => '',
            'price_font_color'          => '',
            'ratings_size'              => '',
            'ratings_color'             => '',
            'cart_font_color'           => '',
            'cart_bg_color'             => '',
            'cart_button_hover_color'   => '',
            'cart_button_hover_font_color'    => '',
            'ribbon_bg_color'          	=> '',
            'handpicked_products'       => '', // comma-separated product IDs, in display order
            'related_fallback'          => '', // latest | none
            'filter_tabs'               => '', // yes | no: category tabs above the products
            'filter_categories'         => '', // comma-separated product_cat slugs shown as tabs, in order
            'filter_all_text'           => '', // label of the first ("All") tab
		);
	}

	public function register( $atts, $content = null ) {
		$atts = shortcode_atts( $this->shortcode_defaults(), $atts, self::SHORTCODE_TAG );
		
		$post_id = absint( $atts['id'] );

		// get the array of data from the post meta
		$enc_data   = get_post_meta( $post_id, 'wcpscu', true );
		$data_array = WCPCSU_Main::json_decoded( $enc_data );

		return $this->render( $data_array, $atts );
	}

	/**
	 * Render a carousel/grid from a settings array.
	 *
	 * The shortcode passes the saved settings; the admin live preview passes the
	 * unsaved form values, so both produce exactly the same markup.
	 *
	 * @param array $data_array Settings (the decoded `wcpscu` meta).
	 * @param array $atts       Shortcode attributes, already passed through shortcode_atts().
	 * @param array $context    Optional. `cards_only` (bool): print only the product cards, without
	 *                          header, wrapper, navigation, pagination or modal, and enqueue nothing.
	 *                          `paged` (int): the page to query, instead of the one in the URL.
	 *                          `category` (string): a product_cat slug to narrow the products to.
	 *                          The query used is kept in $this->last_query.
	 * @return string
	 */
	public function render( $data_array, $atts, $context = array() ) {
		$cards_only = ! empty( $context['cards_only'] );

		if ( ! $cards_only ) {
			$this->wcpcsu_style_files();
		}

		$value      = is_array( $data_array ) ? $data_array : array();

		// EXTR_SKIP: a settings key can't replace the arguments or $cards_only.
		extract( $value, EXTR_SKIP );

		$rand_id                 = rand();
		$total_products          = ! empty( $total_products ) ? intval( $total_products ) : 6;
		$layout                  = ! empty( $layout ) ? $layout : 'carousel';
		$products_type           = ! empty( $products_type ) ? $products_type : 'latest';
		$img_crop                = ! empty( $img_crop ) ? $img_crop : 'yes';
		$img_hover_swap          = ! empty( $img_hover_swap ) ? $img_hover_swap : 'no';
		$crop_image_width        = ! empty( $crop_image_width ) ? intval( $crop_image_width ) : 300;
		$crop_image_height       = ! empty( $crop_image_height ) ? intval( $crop_image_height ) : 300;
		$display_price           = ! empty( $display_price ) ? $display_price : 'yes';

		$display_cart            = ! empty( $display_cart ) ? $display_cart : 'yes';
		$ribbon                  = ! empty( $ribbon ) ? $ribbon : 'discount';
		$header_position         = ! empty( $header_position ) ? $header_position : 'middle';
		$h_title_show            = ! empty( $h_title_show ) ? $h_title_show : 'no';
		$header_font_size        = ! empty( $header_font_size ) ? $header_font_size : '24px';
		$header_font_color       = ! empty( $header_font_color ) ? $header_font_color : '#303030';
		$display_full_title      = ! empty( $display_full_title ) ? $display_full_title : 'no';
		$g_column                = ! empty( $g_column ) ? intval( $g_column ) : 3;
		$g_tablet                = ! empty( $g_tablet ) ? intval( $g_tablet ) : 2;
		$g_mobile                = ! empty( $g_mobile ) ? intval( $g_mobile ) : 1;
		$grid_pagination         = ! empty( $grid_pagination ) ? $grid_pagination : 'no';
		$grid_pagination_type    = ! empty( $grid_pagination_type ) ? $grid_pagination_type : 'normal';
		$slide_time              = ! empty( $slide_time )  ? $slide_time : '2000' ;

		$display_title           = ! empty( $display_title ) ? $display_title : 'yes';
		$display_cart            = ! empty( $display_cart ) ? $display_cart : 'yes';
		$display_price           = ! empty( $display_price ) ? $display_price : 'yes';
		$display_ratings         = ! empty( $display_ratings ) ? $display_ratings : 'yes';
		$quick_view              = ! empty( $quick_view ) ? $quick_view : 'yes';

		$theme                          = ! empty( $theme ) ? $theme : 'theme_1';
		$title_font_size                = ! empty( $title_font_size[ $theme ] ) ? $title_font_size[ $theme ] : '16';
		$title_font_color               = ! empty( $title_font_color[ $theme ] ) ? $title_font_color[ $theme ] : '#363940';
		$title_hover_font_color         = ! empty( $title_hover_font_color[ $theme ] ) ? $title_hover_font_color[ $theme ] : '#ff5500';
		$price_font_size                = ! empty( $price_font_size[ $theme ] ) ? $price_font_size[ $theme ] : '14';
		$price_font_color               = ! empty( $price_font_color[ $theme ] ) ? $price_font_color[ $theme ] : '#ff5500';
		$ratings_size                   = ! empty( $ratings_size[ $theme ] ) ? $ratings_size[ $theme ] : '16';
		$ratings_color                  = ! empty( $ratings_color[ $theme ] ) ? $ratings_color[ $theme ] : '#FEB507';
		$cart_font_color                = ! empty( $cart_font_color[ $theme ] ) ? $cart_font_color[ $theme ] : '#ffffff';
		$cart_bg_color                  = ! empty( $cart_bg_color[ $theme ] ) ? $cart_bg_color[ $theme ] : '#ff5500';
		$cart_button_hover_color        = ! empty( $cart_button_hover_color[ $theme ] ) ? $cart_button_hover_color[ $theme ] : '#9A9A9A';
		$cart_button_hover_font_color   = ! empty( $cart_button_hover_font_color[ $theme ] ) ? $cart_button_hover_font_color[ $theme ] : '#ffffff';
		$ribbon_bg_color                = ! empty( $ribbon_bg_color[ $theme ] ) ? $ribbon_bg_color[ $theme ] : '#A4C741';
		$quick_view_icon_color          = ! empty( $quick_view_icon_color[ $theme ] ) ? $quick_view_icon_color[ $theme ] : '#ffffff';
		$quick_view_icon_back_color     = ! empty( $quick_view_icon_back_color[ $theme ] ) ? $quick_view_icon_back_color[ $theme ] : '#ff5500';

		$paged                          = ! empty( $context['paged'] ) ? absint( $context['paged'] ) : wcpcsu_get_paged_num();
		$paged                          = ! empty( $paged ) ? $paged : '';
		$loop                           = $this->parse_query( $data_array, $atts, $context );
		$this->last_query               = $loop;

		// carousel settings
		$slide_speed                = ! empty( $slide_speed ) ? $slide_speed : '2000';
		$A_play                     = ! empty( $A_play ) ? $A_play : 'yes';
		$repeat_product             = ! empty( $repeat_product ) ? $repeat_product : 'yes';
		$stop_hover                 = ! empty( $stop_hover ) ? $stop_hover : true;
		$scrool_direction           = ! empty( $scrool_direction ) ? $scrool_direction : 'right_left';
		

		// carousel navigation settings
		$nav_show                   = ! empty( $nav_show ) ? $nav_show : 'yes';
		$nav_position               = ! empty( $nav_position ) ? $nav_position : 'middle';
		$nav_arrow_color            = ! empty( $nav_arrow_color ) ? $nav_arrow_color : '#333';
		$nav_back_color             = ! empty( $nav_back_color ) ? $nav_back_color : '#fff';
		$nav_border_color           = ! empty( $nav_border_color ) ? $nav_border_color : '#e4e4ed';
		$nav_arrow_hover_color      = ! empty( $nav_arrow_hover_color ) ? $nav_arrow_hover_color : '#fff';
		$nav_back_hover_color       = ! empty( $nav_back_hover_color ) ? $nav_back_hover_color : '#ff5500';
		$nav_border_hover           = ! empty( $nav_border_hover ) ? $nav_border_hover : '#ff5500';

		// carousel pagination settings
		$carousel_pagination        = ! empty( $carousel_pagination ) ? $carousel_pagination : 'no';
		$dots_color                 = ! empty( $dots_color ) ? $dots_color : '#333';
		$dots_active_color          = ! empty( $dots_active_color ) ? $dots_active_color : '#fff';
		//grid pagination settings
		$pagi_color                 = ! empty( $pagi_color ) ? $pagi_color : '#333';
		$pagi_border_color          = ! empty( $pagi_border_color ) ? $pagi_border_color : '#e4e4e4';
		$pagi_back_color            = ! empty( $pagi_back_color ) ? $pagi_back_color : '#fff';

		$pagi_hover_color           = ! empty( $pagi_hover_color ) ? $pagi_hover_color : '#fff';
		$pagi_hover_border_color    = ! empty( $pagi_hover_border_color ) ? $pagi_hover_border_color : '#ff5500';
		$pagi_hover_back_color      = ! empty( $pagi_hover_back_color ) ? $pagi_hover_back_color : '#ff5500';

		$pagi_active_color          = ! empty( $pagi_active_color ) ? $pagi_active_color : '#fff';
		$pagi_active_border_color   = ! empty( $pagi_active_border_color ) ? $pagi_active_border_color : '#ff5500';
		$pagi_active_back_color     = ! empty( $pagi_active_back_color ) ? $pagi_active_back_color : '#ff5500';

		$header_title     			= ! empty( $header_title ) ? $header_title : '';

		// Pro layouts (thumbnails, masonry, tabbed) fall back to carousel.
		$allowed_layouts 			= array( 'carousel', 'grid', 'list', 'ticker' );
		$allowed_themes  			= array( 'theme_1', 'theme_2', 'theme_3' );

		// Settings without a default above. A carousel without saved settings (no `id`, e.g. the
		// AJAX route or an id-less shortcode) doesn't define them; they get their defaults below.
		$display_sale_ribbon        = isset( $display_sale_ribbon ) ? $display_sale_ribbon : '';
		$sale_ribbon_text           = isset( $sale_ribbon_text ) ? $sale_ribbon_text : '';
		$sale_ribbon_position       = isset( $sale_ribbon_position ) ? $sale_ribbon_position : '';
		$display_featured_ribbon    = isset( $display_featured_ribbon ) ? $display_featured_ribbon : '';
		$feature_ribbon_text        = isset( $feature_ribbon_text ) ? $feature_ribbon_text : '';
		$featured_ribbon_position   = isset( $featured_ribbon_position ) ? $featured_ribbon_position : '';
		$display_sold_out_ribbon    = isset( $display_sold_out_ribbon ) ? $display_sold_out_ribbon : '';
		$sold_out_ribbon_text       = isset( $sold_out_ribbon_text ) ? $sold_out_ribbon_text : '';
		$sold_out_ribbon_position   = isset( $sold_out_ribbon_position ) ? $sold_out_ribbon_position : '';
		$display_discount_ribbon    = isset( $display_discount_ribbon ) ? $display_discount_ribbon : '';
		$discount_ribbon_position   = isset( $discount_ribbon_position ) ? $discount_ribbon_position : '';
		$c_desktop                  = isset( $c_desktop ) ? $c_desktop : '';
		$c_desktop_small            = isset( $c_desktop_small ) ? $c_desktop_small : '';
		$c_tablet                   = isset( $c_tablet ) ? $c_tablet : '';
		$c_mobile                   = isset( $c_mobile ) ? $c_mobile : '';
		$carousel_effect            = isset( $carousel_effect ) ? $carousel_effect : '';

		// shortcode attribute
        $layout                     = ! empty( $atts['layout'] ) ? $atts['layout'] : $layout;
		$layout 					= in_array( $layout, $allowed_layouts, true ) ? $layout : 'carousel';
		// The ticker uses the Carousel tab's columns and speed; the list is a one-column grid
		// (products per page and pagination from the Grid tab).
		$is_ticker 					= 'ticker' === $layout;
		$is_grid 					= in_array( $layout, array( 'grid', 'list' ), true );

        $theme                      = ! empty( $atts['theme'] ) ? $atts['theme'] : $theme;
		$theme 						= in_array( $theme, $allowed_themes, true ) ? $theme : 'theme_1';
        $total_products            = ! empty( $atts['total_products'] ) ? $atts['total_products'] : $total_products;
        $h_title_show               = ! empty( $atts['h_title_show'] ) ? $atts['h_title_show'] : $h_title_show;
        $header_title               = ! empty( $atts['header_title'] ) ? $atts['header_title'] : $header_title;
        $header_position            = ! empty( $atts['header_position'] ) ? $atts['header_position'] : $header_position;
        $display_title              = ! empty( $atts['display_title'] ) ? $atts['display_title'] : $display_title;
        $products_type              = ! empty( $atts['products_type'] ) ? $atts['products_type'] : $products_type;
        $display_sale_ribbon        = ! empty( $atts['display_sale_ribbon'] ) ? $atts['display_sale_ribbon'] : $display_sale_ribbon;
        $sale_ribbon_text           = ! empty( $atts['sale_ribbon_text'] ) ? $atts['sale_ribbon_text'] : $sale_ribbon_text;
        $sale_ribbon_position       = ! empty( $atts['sale_ribbon_position'] ) ? $atts['sale_ribbon_position'] : $sale_ribbon_position;
        $display_featured_ribbon    = ! empty( $atts['display_featured_ribbon'] ) ? $atts['display_featured_ribbon'] : $display_featured_ribbon;
        $feature_ribbon_text        = ! empty( $atts['feature_ribbon_text'] ) ? $atts['feature_ribbon_text'] : $feature_ribbon_text;
        $featured_ribbon_position   = ! empty( $atts['featured_ribbon_position'] ) ? $atts['featured_ribbon_position'] : $featured_ribbon_position;
        $display_sold_out_ribbon    = ! empty( $atts['display_sold_out_ribbon'] ) ? $atts['display_sold_out_ribbon'] : $display_sold_out_ribbon;
        $sold_out_ribbon_text       = ! empty( $atts['sold_out_ribbon_text'] ) ? $atts['sold_out_ribbon_text'] : $sold_out_ribbon_text;
        $sold_out_ribbon_position   = ! empty( $atts['sold_out_ribbon_position'] ) ? $atts['sold_out_ribbon_position'] : $sold_out_ribbon_position;
        $display_discount_ribbon    = ! empty( $atts['display_discount_ribbon'] ) ? $atts['display_discount_ribbon'] : $display_discount_ribbon;
        $discount_ribbon_position   = ! empty( $atts['discount_ribbon_position'] ) ? $atts['discount_ribbon_position'] : $discount_ribbon_position;
        $display_price              = ! empty( $atts['display_price'] ) ? $atts['display_price'] : $display_price;
        $display_ratings            = ! empty( $atts['display_ratings'] ) ? $atts['display_ratings'] : $display_ratings;
        $display_cart               = ! empty( $atts['display_cart'] ) ? $atts['display_cart'] : $display_cart;
        $img_crop                   = ! empty( $atts['img_crop'] ) ? $atts['img_crop'] : $img_crop;
        $img_hover_swap             = ! empty( $atts['img_hover_swap'] ) ? $atts['img_hover_swap'] : $img_hover_swap;
        $crop_image_width           = ! empty( $atts['crop_image_width'] ) ? $atts['crop_image_width'] : $crop_image_width;
        $crop_image_height          = ! empty( $atts['crop_image_height'] ) ? $atts['crop_image_height'] : $crop_image_height;
        $A_play                     = ! empty( $atts['auto_play'] ) ? $atts['auto_play'] : $A_play;
        $repeat_product             = ! empty( $atts['repeat_product'] ) ? $atts['repeat_product'] : $repeat_product;
        $stop_hover                 = ! empty( $atts['stop_hover'] ) ? $atts['stop_hover'] : $stop_hover;
        $scrool_direction           = ! empty( $atts['scrool_direction'] ) ? $atts['scrool_direction'] : $scrool_direction;
        $c_desktop                  = ! empty( $atts['c_desktop'] ) ? $atts['c_desktop'] : $c_desktop;
        $c_desktop_small            = ! empty( $atts['c_desktop_small'] ) ? $atts['c_desktop_small'] : $c_desktop_small;
        $c_tablet                   = ! empty( $atts['c_tablet'] ) ? $atts['c_tablet'] : $c_tablet;
        $c_mobile                   = ! empty( $atts['c_mobile'] ) ? $atts['c_mobile'] : $c_mobile;
        $slide_speed                = ! empty( $atts['slide_speed'] ) ? $atts['slide_speed'] : $slide_speed;
        $carousel_effect            = ! empty( $atts['carousel_effect'] ) ? $atts['carousel_effect'] : $carousel_effect;
		// Pro effects (coverflow, flip) fall back to slide. Fade shows one product at a time (Swiper's fade
		// can't show several), so it sets every breakpoint to one column below.
		$carousel_effect 			= in_array( $carousel_effect, array( 'slide', 'fade' ), true ) ? $carousel_effect : 'slide';
		$is_fade 					= 'carousel' === $layout && 'fade' === $carousel_effect;
        $slide_time                 = ! empty( $atts['slide_time'] ) ? $atts['slide_time'] : $slide_time;
        $nav_show                   = ! empty( $atts['nav_show'] ) ? $atts['nav_show'] : $nav_show;
        $nav_position               = ! empty( $atts['nav_position'] ) ? $atts['nav_position'] : $nav_position;
        $nav_arrow_color            = ! empty( $atts['nav_arrow_color'] ) ? $atts['nav_arrow_color'] : $nav_arrow_color;
        $nav_back_color             = ! empty( $atts['nav_back_color'] ) ? $atts['nav_back_color'] : $nav_back_color;
        $nav_border_color           = ! empty( $atts['nav_border_color'] ) ? $atts['nav_border_color'] : $nav_border_color;
        $nav_arrow_hover_color      = ! empty( $atts['nav_arrow_hover_color'] ) ? $atts['nav_arrow_hover_color'] : $nav_arrow_hover_color;
        $nav_back_hover_color       = ! empty( $atts['nav_back_hover_color'] ) ? $atts['nav_back_hover_color'] : $nav_back_hover_color;
        $nav_border_hover           = ! empty( $atts['nav_border_hover'] ) ? $atts['nav_border_hover'] : $nav_border_hover;
        $carousel_pagination        = ! empty( $atts['carousel_pagination'] ) ? $atts['carousel_pagination'] : $carousel_pagination;
        $dots_color                 = ! empty( $atts['dots_color'] ) ? $atts['dots_color'] : $dots_color;
        $dots_active_color          = ! empty( $atts['dots_active_color'] ) ? $atts['dots_active_color'] : $dots_active_color;
        $g_column                   = ! empty( $atts['g_column'] ) ? $atts['g_column'] : $g_column;
        $g_tablet                   = ! empty( $atts['g_tablet'] ) ? $atts['g_tablet'] : $g_tablet;
        $g_mobile                   = ! empty( $atts['g_mobile'] ) ? $atts['g_mobile'] : $g_mobile;
        $grid_pagination            = ! empty( $atts['grid_pagination'] ) ? $atts['grid_pagination'] : $grid_pagination;
        $grid_pagination_type       = ! empty( $atts['grid_pagination_type'] ) ? $atts['grid_pagination_type'] : $grid_pagination_type;
		// After the attribute override, so an attribute is checked too. Ajax numbers is grid/list only
		// (a carousel keeps dots/arrows); Pro values (load_more, infinite) fall back to normal in Free.
		$allowed_pagination_types 	= array( 'normal', 'ajax_numbers' );
		$grid_pagination_type 		= ( $is_grid && in_array( $grid_pagination_type, $allowed_pagination_types, true ) ) ? $grid_pagination_type : 'normal';
        $pagi_color                 = ! empty( $atts['pagi_color'] ) ? $atts['pagi_color'] : $pagi_color;
        $pagi_border_color          = ! empty( $atts['pagi_border_color'] ) ? $atts['pagi_border_color'] : $pagi_border_color;
        $pagi_back_color            = ! empty( $atts['pagi_back_color'] ) ? $atts['pagi_back_color'] : $pagi_back_color;
        $pagi_hover_color           = ! empty( $atts['pagi_hover_color'] ) ? $atts['pagi_hover_color'] : $pagi_hover_color;
        $pagi_hover_border_color    = ! empty( $atts['pagi_hover_border_color'] ) ? $atts['pagi_hover_border_color'] : $pagi_hover_border_color;
        $pagi_hover_back_color      = ! empty( $atts['pagi_hover_back_color'] ) ? $atts['pagi_hover_back_color'] : $pagi_hover_back_color;
        $pagi_active_border_color   = ! empty( $atts['pagi_active_border_color'] ) ? $atts['pagi_active_border_color'] : $pagi_active_border_color;
        $pagi_active_back_color     = ! empty( $atts['pagi_active_back_color'] ) ? $atts['pagi_active_back_color'] : $pagi_active_back_color;

		//style shortcode act 
        $header_font_size                       = ! empty( $atts['header_font_size'] ) ? $atts['header_font_size'] : $header_font_size;
        $header_font_color                      = ! empty( $atts['header_font_color'] ) ? $atts['header_font_color'] : $header_font_color;
        $title_font_size                        = ! empty( $atts['title_font_size'] ) ? $atts['title_font_size'] : $title_font_size;
        $title_font_color                       = ! empty( $atts['title_font_color'] ) ? $atts['title_font_color'] : $title_font_color;
        $title_hover_font_color                 = ! empty( $atts['title_hover_font_color'] ) ? $atts['title_hover_font_color'] : $title_hover_font_color;
        $price_font_size                        = ! empty( $atts['price_font_size'] ) ? $atts['price_font_size'] : $price_font_size;
        $price_font_color                       = ! empty( $atts['price_font_color'] ) ? $atts['price_font_color'] : $price_font_color;
        $ratings_size                           = ! empty( $atts['ratings_size'] ) ? $atts['ratings_size'] : $ratings_size;
        $ratings_color                          = ! empty( $atts['ratings_color'] ) ? $atts['ratings_color'] : $ratings_color;
        $cart_font_color                        = ! empty( $atts['cart_font_color'] ) ? $atts['cart_font_color'] : $cart_font_color;
        $cart_bg_color                          = ! empty( $atts['cart_bg_color'] ) ? $atts['cart_bg_color'] : $cart_bg_color;
        $cart_button_hover_color                = ! empty( $atts['cart_button_hover_color'] ) ? $atts['cart_button_hover_color'] : $cart_button_hover_color;
        $cart_button_hover_font_color           = ! empty( $atts['cart_button_hover_font_color'] ) ? $atts['cart_button_hover_font_color'] : $cart_button_hover_font_color;
        $ribbon_bg_color                        = ! empty( $atts['ribbon_bg_color'] ) ? $atts['ribbon_bg_color'] : $ribbon_bg_color;

		$display_sale_ribbon        = ! empty( $display_sale_ribbon ) ? $display_sale_ribbon : 'no';
        $sale_ribbon_text           = ! empty( $sale_ribbon_text ) ? $sale_ribbon_text : '';
        $sale_ribbon_position       = ! empty( $sale_ribbon_position ) ? $sale_ribbon_position : 'top_left';
        $display_featured_ribbon    = ! empty( $display_featured_ribbon ) ? $display_featured_ribbon : 'no';
        $feature_ribbon_text        = ! empty( $feature_ribbon_text ) ? $feature_ribbon_text : '';
        $featured_ribbon_position   = ! empty( $featured_ribbon_position ) ? $featured_ribbon_position : 'top_right';
        $display_sold_out_ribbon    = ! empty( $display_sold_out_ribbon ) ? $display_sold_out_ribbon : 'no';
        $sold_out_ribbon_text       = ! empty( $sold_out_ribbon_text ) ? $sold_out_ribbon_text : '';
        $sold_out_ribbon_position   = ! empty( $sold_out_ribbon_position ) ? $sold_out_ribbon_position : 'bottom_left';
        $display_discount_ribbon    = ! empty( $display_discount_ribbon ) ? $display_discount_ribbon : 'no';
        $discount_ribbon_position   = ! empty( $discount_ribbon_position ) ? $discount_ribbon_position : 'bottom_right';

		$ribbon_args = array(
			'display_sale_ribbon'        => $display_sale_ribbon,
            'sale_ribbon_text'           => $sale_ribbon_text,
            'sale_ribbon_position'       => $sale_ribbon_position,
            'display_featured_ribbon'    => $display_featured_ribbon,
            'feature_ribbon_text'        => $feature_ribbon_text,
            'featured_ribbon_position'   => $featured_ribbon_position,
            'display_sold_out_ribbon'    => $display_sold_out_ribbon,
            'sold_out_ribbon_text'       => $sold_out_ribbon_text,
            'sold_out_ribbon_position'   => $sold_out_ribbon_position,
            'display_discount_ribbon'    => $display_discount_ribbon,
            'discount_ribbon_position'   => $discount_ribbon_position,
		);

		$carousel_desktop_column    = ! empty( $c_desktop ) ? intval( $c_desktop ) : 4;
		$carousel_laptop_column     = ! empty( $c_desktop_small ) ? intval( $c_desktop_small ) : 3;
		$carousel_tablet_column     = ! empty( $c_tablet ) ? intval( $c_tablet ) : 2;
		$carousel_mobile_column     = ! empty( $c_mobile ) ? intval( $c_mobile ) : 1;
		if ( $is_fade ) {
			$carousel_desktop_column = $carousel_laptop_column = $carousel_tablet_column = $carousel_mobile_column = 1;
		}

		// Cards only (AJAX filters): no products prints nothing; the caller reports the empty result.
		if ( $cards_only ) {
			ob_start();
			if ( $loop->have_posts() ) {
				include WCPCSU_INC_DIR . 'template/cards.php';
			}
			return ob_get_clean();
		}

		// "Related" with fallback "none" prints nothing (not "No products found") outside single product pages.
		// Editors get a short note instead (frontend, live preview, block editor), so the empty spot is explained.
		$related_fallback = ! empty( $atts['related_fallback'] ) ? $atts['related_fallback'] : ( ! empty( $related_fallback ) ? $related_fallback : 'latest' );
		if ( 'related' == $products_type && 'none' == $related_fallback && ! $loop->have_posts() ) {
			return current_user_can( 'edit_posts' ) ? $this->related_none_notice() : '';
		}

		$header_class = '';

		if( 'middle' == $header_position ) {
			$header_class = 'wpcu-products__header--middle';
		} elseif( 'right' == $header_position ) {
			$header_class = 'wpcu-products__header--right';
		}

		// Category filter tabs: the categories that still exist, in the chosen order.
		$filter_terms = array();
		foreach ( WCPCSU_Products_Route::tab_categories( $data_array, $atts ) as $filter_slug ) {
			$filter_term = get_term_by( 'slug', $filter_slug, 'product_cat' );
			if ( $filter_term ) {
				$filter_terms[] = $filter_term;
			}
		}
		$filter_all_text = ! empty( $atts['filter_all_text'] ) ? $atts['filter_all_text'] : ( ! empty( $filter_all_text ) ? $filter_all_text : __( 'All', 'woocommerce-product-carousel-slider-and-grid-ultimate' ) );

		ob_start();

		if ( $loop->have_posts() ) { ?>

		<?php if ( 'yes' == $h_title_show && ! empty( $header_title ) ) { ?>
		<div class="wpcu-products__header <?php echo esc_attr( $header_class ); ?>" style="
		--wpcu-headerFontSize: <?php echo esc_attr( $header_font_size ); ?>px;
		--wpcu-headerFontColor: <?php echo esc_attr( $header_font_color ); ?>;
		"> <!-- .wpcu-products__header--middle /.wpcu-products__header--right -->
			<h2><?php echo esc_html( $header_title ); ?></h2>
		</div>
		<?php } ?>
<?php if ( $filter_terms ) {
			// The products route renders the cards of a tab from these signed attributes (see WCPCSU_Products_Route).
			// Accent: the title hover color, which every theme uses as readable text on white (theme_3's cart button is white).
			$filter_query = WCPCSU_Products_Route::sign( $atts );
			$filter_query['url']   = rest_url( WCPCSU_Products_Route::REST_NAMESPACE . '/products' );
			$filter_query['empty'] = __( 'No products found', 'woocommerce-product-carousel-slider-and-grid-ultimate' );
			?>
		<div class="wpcu-filter wpcu-filter--<?php echo esc_attr( $header_position ); ?>"
			style="--wpcu-filterActiveBg: <?php echo esc_attr( $title_hover_font_color ); ?>;"
			data-wpcu-filter="<?php echo esc_attr( wp_json_encode( $filter_query ) ); ?>">
			<div class="wpcu-filter__tabs" role="group" aria-label="<?php esc_attr_e( 'Product categories', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">
				<button type="button" class="wpcu-filter__tab wpcu-filter__tab--active" data-wpcu-category="" aria-pressed="true"><?php echo esc_html( $filter_all_text ); ?></button>
				<?php foreach ( $filter_terms as $filter_term ) { ?>
				<button type="button" class="wpcu-filter__tab" data-wpcu-category="<?php echo esc_attr( $filter_term->slug ); ?>" aria-pressed="false"><?php echo esc_html( $filter_term->name ); ?></button>
				<?php } ?>
			</div>
			<p class="wpcu-filter__status" role="status" hidden></p>
		</div>
		<?php } ?>

		<div
		class="wpcu-products wpcu-<?php echo esc_attr( $theme ); ?><?php echo ( 'carousel' == $layout ) ? ' wpcu-carousel' : ''; ?><?php echo $is_ticker ? ' wpcu-ticker' . ( 'true' == $stop_hover ? ' wpcu-ticker--pause' : '' ) . ( 'left_right' === $scrool_direction ? ' wpcu-ticker--reverse' : '' ) : ''; ?><?php echo ( 'list' === $layout ) ? ' wpcu-layout-list' : ''; ?><?php echo ( 'yes' === $img_crop ) ? ' wpcu-products--img-ratio' : ''; ?><?php echo ( 'yes' === $img_hover_swap ) ? ' wpcu-products--hover-swap' : ''; ?>"
		style="
			<?php if ( 'yes' === $img_crop ) { // Reserves the image height before it loads. ?>
			--wpcu-imgRatio: <?php echo esc_attr( (int) $crop_image_width . ' / ' . (int) $crop_image_height ); ?>;
			<?php } ?>
			<?php if ( 'carousel' == $layout || $is_ticker ) { // Columns per breakpoint: the carousel's layout before Swiper starts, the ticker's card width. ?>
			--wpcu-cols-sm: <?php echo esc_attr( (int) $carousel_mobile_column ); ?>;
			--wpcu-cols-md: <?php echo esc_attr( (int) $carousel_tablet_column ); ?>;
			--wpcu-cols-lg: <?php echo esc_attr( (int) $carousel_laptop_column ); ?>;
			--wpcu-cols-xl: <?php echo esc_attr( (int) $carousel_desktop_column ); ?>;
			<?php } ?>
			--wpcu-productTitleSize: <?php echo esc_attr( $title_font_size ); ?>px;
			--wpcu-productTitleColor: <?php echo esc_attr( $title_font_color ); ?>;
			--wpcu-productTitleColorHover: <?php echo esc_attr( $title_hover_font_color ); ?>;
			--wpcu-productPriceSize: <?php echo esc_attr( $price_font_size ); ?>px;
			--wpcu-productPriceColor: <?php echo esc_attr( $price_font_color ); ?>;
			--wpcu-productRatingSize: <?php echo esc_attr( $ratings_size ); ?>px;
			--wpcu-productRatingColor: <?php echo esc_attr( $ratings_color ); ?>;
			--wpcu-buttonColor: <?php echo esc_attr( $cart_font_color ); ?>;
			--wpcu-buttonColorHover: <?php echo esc_attr( $cart_button_hover_font_color ); ?>;
			--wpcu-buttonBgColor: <?php echo esc_attr( $cart_bg_color ); ?>;
			--wpcu-buttonBgColorHover: <?php echo esc_attr( $cart_button_hover_color ); ?>;
			--wpcu-ribbonBgColor: <?php echo esc_attr( $ribbon_bg_color ); ?>;
			--wpcu-qvIconColor: <?php echo esc_attr( $quick_view_icon_color ); ?>;
			--wpcu-qvBgColor: <?php echo esc_attr( $quick_view_icon_back_color ); ?>;
		"
		<?php if ( 'carousel' == $layout ) { ?>
		data-wpcu-items="4"
		data-wpcu-margin="30"
		data-wpcu-loop="<?php echo ( 'yes' == $repeat_product ) ? 'true' : 'false'; ?>"
		data-wpcu-perslide="1"
		data-wpcu-speed="<?php echo esc_attr( $slide_speed ); ?>"
		<?php if ( $is_fade ) { // build/carousel.js loads Swiper's fade module only for these. ?>
		data-wpcu-effect="fade"
		<?php } ?>
		data-wpcu-autoplay='
		<?php if( 'yes' == $A_play ) { ?>
		{
			"delay": "<?php echo esc_attr( $slide_time ); ?>",
			"pauseOnMouseEnter": <?php echo ( 'true' == $stop_hover ) ? "true" : "false"; ?>,
			"disableOnInteraction": false,
			"stopOnLastSlide": true
		}
		<?php } else { ?>
			false
		<?php } ?>
		'
		data-wpcu-responsive='{
			"0": {"slidesPerView": "<?php echo esc_attr( $carousel_mobile_column ); ?>", "spaceBetween": "20"},
			"768": {"slidesPerView": "<?php echo esc_attr( $carousel_tablet_column ); ?>", "spaceBetween": "30"},
			"992": {"slidesPerView": "<?php echo esc_attr( $carousel_laptop_column ); ?>", "spaceBetween": "30"},
			"1200": {"slidesPerView": "<?php echo esc_attr( $carousel_desktop_column ); ?>", "spaceBetween": "30"}
		}'
		<?php } elseif ( $is_ticker ) { // Time one card takes to scroll past (build/carousel.js, initTicker()). ?>
		data-wpcu-speed="<?php echo esc_attr( (int) $slide_speed ); ?>"
		<?php } ?>
		>
		<?php if( ( 'carousel' == $layout && 'yes' == $nav_show ) && ( 'top-left' == $nav_position || 'top-right' == $nav_position || 'top-middle' == $nav_position || 'middle' == $nav_position )  ) {
				include WCPCSU_INC_DIR . 'template/navigation.php';
		} ?>
		<?php
		if ( 'carousel' == $layout ) {
			$cards_class = 'swiper-wrapper';
		} elseif ( $is_ticker ) {
			$cards_class = 'wpcu-ticker__track';
		} elseif ( 'list' === $layout ) {
			$cards_class = 'wpcu-row wpcu-list';
		} else {
			$cards_class = 'wpcu-row wpcu-column-' . $g_column . ' wpcu-column-md-' . $g_tablet . ' wpcu-column-sm-' . $g_mobile;
		}
		?>
		<div class="<?php echo esc_attr( $cards_class ); ?>">
		<?php include WCPCSU_INC_DIR . 'template/cards.php'; ?>

			</div>
			<?php if( ( 'carousel' == $layout && 'yes' == $nav_show ) && ( 'bottom-left' == $nav_position || 'bottom-right' == $nav_position || 'bottom-middle' == $nav_position ) ) {
				include WCPCSU_INC_DIR . 'template/navigation.php';
			 } ?>
			 <?php if( ( $is_grid && 'yes' == $grid_pagination ) || ( 'carousel' == $layout && 'yes' ==  $carousel_pagination ) ) {
				include WCPCSU_INC_DIR . 'template/pagination.php';
			 } ?>
		</div><!-- ends: .wpcu-products -->
		<?php
			// Quick view only exists in theme_2; load its modal and assets only when it is shown.
			if( 'theme_2' == $theme && 'yes' == $quick_view ){
				$this->enqueue_quick_view_files();
				include WCPCSU_INC_DIR . 'template/quick-view.php';
			}
		?>
		<?php

		} else {

			esc_html_e( 'No products found', 'woocommerce-product-carousel-slider-and-grid-ultimate' );
		}

		return ob_get_clean();
	}

	/**
	 * Build the products query from the settings.
	 *
	 * @param array $data_array Settings (the decoded `wcpscu` meta).
	 * @param array $atts       Shortcode attributes, already passed through shortcode_atts().
	 * @param array $context    Optional. `paged` (int): query this page instead of the one in the URL,
	 *                          also for a carousel or a grid without pagination (AJAX filters, load more).
	 *                          `category` (string): only products in this product_cat slug (category tab).
	 * @return WP_Query
	 */
	public function parse_query( $data_array, $atts, $context = array() ) {
		$value = is_array( $data_array ) ? $data_array : array();
		extract( $value, EXTR_SKIP );
		$paged                          = ! empty( $context['paged'] ) ? absint( $context['paged'] ) : wcpcsu_get_paged_num();
		$paged                          = ! empty( $paged ) ? $paged : '';
		$total_products                 = ! empty( $atts['total_products'] ) ? $atts['total_products'] : ( isset( $total_products ) ? $total_products : '' );
        $products_type                  = ! empty( $atts['products_type'] ) ? $atts['products_type'] : ( isset( $products_type ) ? $products_type : '' );
		$layout                         = ! empty( $atts['layout'] ) ? $atts['layout'] : ( isset( $layout ) ? $layout : '' );
        $grid_pagination                = ! empty( $atts['grid_pagination'] ) ? $atts['grid_pagination'] : ( isset( $grid_pagination ) ? $grid_pagination : '' );
		$handpicked_products            = ! empty( $atts['handpicked_products'] ) ? $atts['handpicked_products'] : ( isset( $handpicked_products ) ? $handpicked_products : array() );
		$related_fallback               = ! empty( $atts['related_fallback'] ) ? $atts['related_fallback'] : ( ! empty( $related_fallback ) ? $related_fallback : 'latest' );
		$common_args = array(
			'post_type'      => 'product',
			'posts_per_page' => ! empty( $total_products ) ? intval( $total_products ) : 12,
			'post_status'    => 'publish',
		);
		if( ( in_array( $layout, array( 'grid', 'list' ), true ) && 'yes' == $grid_pagination ) || ! empty( $context['paged'] ) ) {
			$common_args['paged']    = $paged;
		}
		if( $products_type == "latest" ) {
			$args = $common_args;
		}
		elseif( $products_type == "older" ) {
			$older_args = array(
				'orderby'     => 'date',
				'order'       => 'ASC'
			);
			$args = array_merge( $common_args, $older_args );
		}
		elseif( $products_type == "featured" ) {
			$meta_query  = WC()->query->get_meta_query();
			$tax_query   = WC()->query->get_tax_query();

			$tax_query[] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
				'operator' => 'IN',
			);
			$featured_args = array(
				'meta_query' => $meta_query,
				'tax_query' => $tax_query,
			);
			$args = array_merge( $common_args, $featured_args );
		}
		elseif( $products_type == "handpicked" ) {
			$ids = wcpcsu_parse_id_list( $handpicked_products );
			$handpicked_args = array(
				'post__in' => $ids ? $ids : array( 0 ), // nothing picked yet: no products
				'orderby'  => 'post__in',            // keep the order the products were arranged in
			);
			// Show every picked product, unless the grid paginates them.
			if ( empty( $common_args['paged'] ) ) {
				$handpicked_args['posts_per_page'] = max( 1, count( $ids ) );
			}
			$args = array_merge( $common_args, $handpicked_args );
		}
		elseif( $products_type == "related" ) {
			$current_id = wcpcsu_current_product_id();
			// wc_get_related_products() is cached by WooCommerce per product (transient, flushed on product save).
			$ids        = $current_id ? wc_get_related_products( $current_id, $common_args['posts_per_page'] ) : array();

			if ( $ids ) {
				$args = array_merge( $common_args, array( 'post__in' => array_map( 'absint', $ids ), 'orderby' => 'post__in' ) );
			} elseif ( 'none' == $related_fallback ) {
				$args = array_merge( $common_args, array( 'post__in' => array( 0 ) ) );
			} else {
				$args = $common_args; // fallback: latest products
			}
		}

		else {
			$args = $common_args;
		}

		// Category tab (AJAX filter): narrows the saved product type to one category, children included.
		// The caller checks that the category is one of the carousel's tabs.
		if ( ! empty( $context['category'] ) ) {
			$args['tax_query']   = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
			$args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => (string) $context['category'],
			);
		}

		$loop = new WP_Query( $args );

		return $loop;
	}

	function aazz_show_discount_percentage() {

		global $product;

		if ( $product->is_on_sale() ) {

			if ( ! $product->is_type( 'variable' ) ) {
				if( 0 < $product->get_regular_price() && 0 < $product->get_sale_price()) {
					$max_percentage = ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100;
				}
			} else {

				$max_percentage = 0;
				$percentage = '';

				foreach ( $product->get_children() as $child_id ) {
					$variation = wc_get_product( $child_id );
					$price = $variation->get_regular_price();
					$sale = $variation->get_sale_price();
					if ( $price != 0 && ! empty( $sale ) ) $percentage = ( $price - $sale ) / $price * 100;
					if ( $percentage > $max_percentage ) {
						$max_percentage = $percentage;
					}
				}

			}

			return ! empty( $max_percentage ) ? round( $max_percentage ) . "%" : '';

		}

	}

	function get_total_reviews_count(){
		return get_comments(array(
			'status'   => 'approve',
			'post_status' => 'publish',
			'post_type'   => 'product',
			'count' => true
		));
	}

	/**
	 * Note shown to editors where a "Related" carousel with fallback "Show nothing" has nothing to show.
	 * Visitors get no output at all.
	 *
	 * @return string
	 */
	public function related_none_notice() {
		return '<div class="wpcu-products-notice" role="note">'
			. '<strong>' . esc_html__( 'Related products carousel', 'woocommerce-product-carousel-slider-and-grid-ultimate' ) . '</strong> '
			. esc_html__( 'This carousel shows products related to the product being viewed, so it only shows products on single product pages. Here it shows nothing, because "When There Is No Current Product" is set to "Show nothing".', 'woocommerce-product-carousel-slider-and-grid-ultimate' )
			. ' <em>' . esc_html__( 'Only logged-in editors see this note; visitors see nothing.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ) . '</em>'
			. '</div>';
	}

	public function wcpcsu_style_files () {
		wp_enqueue_style( 'wcpcsu-main' );
		wp_enqueue_style( 'wcpcsu-swiper' );

		// Swiper is bundled in this script.
		wp_enqueue_script( 'wcpcsu-main-js' );
	}

	/**
	 * Quick view modal assets, loaded only by carousels that show quick view buttons.
	 */
	public function enqueue_quick_view_files() {
		wp_enqueue_style( 'wcpcsu-swmodal' );
		wp_enqueue_script( 'wcpcsu-swmodal-js' );
	}

	/**
	 * Quick view modal content for one product.
	 *
	 * No nonce: it only returns what the product page shows anyone, and the buttons it serves
	 * come from cacheable markup (full-page caches, the public products route), where a
	 * per-user nonce can't be valid for every visitor. The product itself is checked instead.
	 * The buttons still print `data-nonce` for older markup; it is ignored.
	 */
	public function ajax_quick_view() {
		$product_id = ! empty( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- read-only public product data, see above.
		$product    = $product_id ? wc_get_product( $product_id ) : false;

		// Only products whose page a visitor could open: published, not a variation, not password protected.
		// Catalog visibility isn't checked: carousels show "hidden" products too, and their page is public.
		if ( ! $product || 'product' !== get_post_type( $product_id ) || 'publish' !== $product->get_status() || post_password_required( $product_id ) ) {
			wp_send_json_error();
		}

		// The modal shows the image at most 300px wide (swmodal.css), so let srcset pick a small file.
		$wpcsu_img = wp_get_attachment_image( get_post_thumbnail_id( $product_id ), 'large', false, array(
			'alt'      => get_the_title( $product_id ),
			'sizes'    => '300px',
			'decoding' => 'async',
		) );

		ob_start();
		?>
		<div>
			<h2 class='wpcu-modal__product-title'><?php echo esc_html( get_the_title( $product_id ) );?></h2>
			<?php if ( ! empty( $wpcsu_img ) ) { ?>
			<div class='wpcu-modal__product-image'>
				<?php echo $wpcsu_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from wp_get_attachment_image(). ?>
			</div>
			<?php } ?>
			<div class='wpcu-modal__product-description'>
				<?php echo wp_kses_post( $product->get_description() ); ?>
			</div>
			<div class='wpcu-modal__product-price'><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			<div class='wpcu-modal__product-action'><?php echo wp_kses_post( do_shortcode('[add_to_cart id="' . $product_id . '" show_price = "false"]') ); ?></div>
		</div>
		<?php
		$template = ob_get_clean();

		wp_send_json( $template );
	}
}
