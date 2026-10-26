<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wcpcsu_pagination' ) ) {
	/**
	 * Prints pagination for custom post
	 * @param object|WP_Query $custom_post_query
	 * @param int $paged
	 *
	 * @return string
	 */
	function wcpcsu_pagination( $custom_post_query, $paged = 1 ) {
		$navigation = '';
		$largeNumber = 999999999; // we need a large number here
		$links = paginate_links( array(
			'base'      => str_replace( $largeNumber, '%#%', esc_url( get_pagenum_link( $largeNumber ) ) ),
			'format'    => '?paged=%#%',
			'current'   => max( 1, $paged ),
			'total'     => $custom_post_query->max_num_pages,
			'prev_text' => apply_filters( 'wcpcsu_pagination_prev_text', '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="6" height="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 576 1024" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);"><path d="M528 0q14 0 24 10q4 3 6 7t3 8.5t1 9t-1 8.5t-3 8t-6 8L96 515l450 450q6 6 8.5 15t0 18t-8.5 15q-10 10-24.5 10t-24.5-10L23 539q-10-10-10-24t10-24L504 10q10-10 24-10z"/><rect x="0" y="0" width="576" height="1024" fill="rgba(0, 0, 0, 0)" /></svg>'),
			'next_text' => apply_filters( 'wcpcsu_pagination_next_text', '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="6" height="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 576 1024" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);"><path d="M48 1023q-14 0-24.5-10T13 989t10-24l457-457L30 59q-6-7-8.5-16t0-17.5T30 10Q40 0 54.5 0T79 10l473 474q5 5 7.5 11.5t2.5 13t-2.5 12.5t-7.5 11L72 1013q-5 5-11.5 7.5T48 1023z"/><rect x="0" y="0" width="576" height="1024" fill="rgba(0, 0, 0, 0)" /></svg>'),
		) );

		if ( $links ) {
			$navigation = _navigation_markup( $links, 'pagination', ' ' );
		}

		return apply_filters('wcpcsu_pagination', $navigation, $links, $custom_post_query, $paged);
	}
}

if ( ! function_exists( 'wcpcsu_get_paged_num' ) ) {
	/**
	 * Get current page number for the pagination.
	 *
	 * @since    1.0.0
	 *
	 * @return    int    $paged    The current page number for the pagination.
	 */
	function wcpcsu_get_paged_num() {
		global $paged;

		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
			$paged = get_query_var( 'page' );
		} else {
			$paged = 1;
		}

		return absint( $paged );
	}
}

if ( ! function_exists('wpcsu_load_dependencies') ) :
	function wpcsu_load_dependencies( $files = 'all', $directory = WCPCSU_INC_DIR, $ext = '.php' ) {
		if ( ! file_exists( $directory ) ) {
			return; // vail if the directory does not exist
		}

		switch( $files ) {
			case is_array( $files ) && 'all' !== strtolower( $files[0] ):
				// include one or more file looping through the $files array
				load_some_file( $files, $directory );
				break;
			case ! is_array( $files ) && 'all' !== $files:
				//load a single file here
				( file_exists( $directory . $files . $ext ) ) ? require_once $directory . $files . $ext : null;
				break;
			case 'all' == $files || 'all' == strtolower( $files[0] ):
				// load all php file here
				load_all_files( $directory );
				break;
		}

		return false;

	}
endif;

if ( ! function_exists('load_all_files') ):
	function load_all_files( $dir = '', $ext = '.php' )
	{
		if ( ! file_exists( $dir ) ) return;
		foreach ( scandir( $dir ) as $file ) {
			// require once all the files with the given ext. eg. .php
			if ( preg_match( "/{$ext}$/i", $file ) ) {
				require_once( $dir . $file );
			}
		}
	}
endif;

if ( ! function_exists('load_some_file') ):

	/**
	 * It loads one or more files but not all files that has the $ext from the $dir
	 * @param string|array $files the array of files that should be loaded
	 * @param string $dir Name of the directory
	 * @param string $ext Name of the extension of the files to be loaded
	 */
	function load_some_file( $files = array(), $dir = '', $ext = '.php' )
	{
		if ( ! file_exists( $dir ) ) return; // vail if directory does not exist

		if ( is_array( $files ) ) {  // if the given files is an array then
			$files_to_loads = array_map( function ( $i ) use ( $ext ) {
				return $i . $ext;
			}, $files );// add '.php' to the end of all files
			$found_files = scandir( $dir ); // get the list of all the files in the given $dir
			foreach ( $files_to_loads as $file_to_load ) {
				in_array( $file_to_load, $found_files ) ? require_once $dir . $file_to_load : null;
			}
		}

	}
endif;

if ( ! function_exists('wpcsu_ribbon_badge') ) :
    function wpcsu_ribbon_badge( $ribbon_args, $discount )
    {
        global $product;
        $value = is_array( $ribbon_args ) ? $ribbon_args : array();
        extract( $value );


        if( ( 'yes' == $display_sale_ribbon && 'top_left' == $sale_ribbon_position && $product->is_on_sale() ) || ( 'yes' == $display_featured_ribbon && 'top_left' == $featured_ribbon_position && $product->is_featured() ) || ( 'yes' == $display_sold_out_ribbon && 'top_left' == $sold_out_ribbon_position && ! $product->is_in_stock() ) || ( 'yes' == $display_discount_ribbon && 'top_left' == $discount_ribbon_position && $product->get_sale_price() ) ) { ?>
            <div class="wpcu-product__cover-content wpcu-product__cover-content--top-left">

                <?php if( 'yes' == $display_sale_ribbon && 'top_left' == $sale_ribbon_position && $product->is_on_sale() ) { ?>
                <span class="wpcu-badge wpcu-sale wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $sale_ribbon_text ) ? $sale_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_featured_ribbon && 'top_left' == $featured_ribbon_position && $product->is_featured() ) { ?>
                <span class="wpcu-badge wpcu-feature wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $feature_ribbon_text ) ? $feature_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_sold_out_ribbon && 'top_left' == $sold_out_ribbon_position && ! $product->is_in_stock() ) { ?>
                <span class="wpcu-badge wpcu-sold_out wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $sold_out_ribbon_text ) ? $sold_out_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_discount_ribbon && 'top_left' == $discount_ribbon_position && $product->get_sale_price() ) { ?>
                <span class="wpcu-badge wpcu-discount wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $discount ) ? '-'.$discount : ''; ?></span>
                <?php } ?>

            </div>
            <?php } ?>

            <?php if( ( 'yes' == $display_sale_ribbon && 'top_right' == $sale_ribbon_position && $product->is_on_sale() ) || ( 'yes' == $display_featured_ribbon && 'top_right' == $featured_ribbon_position && $product->is_featured() ) || ( 'yes' == $display_sold_out_ribbon && 'top_right' == $sold_out_ribbon_position && ! $product->is_in_stock() ) || ( 'yes' == $display_discount_ribbon && 'top_right' == $discount_ribbon_position ) && $product->get_sale_price() ) { ?>
            <div class="wpcu-product__cover-content wpcu-product__cover-content--top-right">

                <?php if( 'yes' == $display_sale_ribbon && 'top_right' == $sale_ribbon_position && $product->is_on_sale() ) { ?>
                <span class="wpcu-badge wpcu-sale wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $sale_ribbon_text ) ? $sale_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_featured_ribbon && 'top_right' == $featured_ribbon_position && $product->is_featured() ) { ?>
                <span class="wpcu-badge wpcu-feature wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $feature_ribbon_text ) ? $feature_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_sold_out_ribbon && 'top_right' == $sold_out_ribbon_position && ! $product->is_in_stock() ) { ?>
                <span class="wpcu-badge wpcu-sold_out wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $sold_out_ribbon_text ) ? $sold_out_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_discount_ribbon && 'top_right' == $discount_ribbon_position && $product->get_sale_price() ) { ?>
                <span class="wpcu-badge wpcu-discount wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $discount ) ? '-'.$discount : ''; ?></span>
                <?php } ?>

            </div>
            <?php } ?>

            <?php if( ( 'yes' == $display_sale_ribbon && 'bottom_left' == $sale_ribbon_position && $product->is_on_sale() ) || ( 'yes' == $display_featured_ribbon && 'bottom_left' == $featured_ribbon_position && $product->is_featured() ) || ( 'yes' == $display_sold_out_ribbon && 'bottom_left' == $sold_out_ribbon_position && ! $product->is_in_stock() ) || ( 'yes' == $display_discount_ribbon && 'bottom_left' == $discount_ribbon_position ) && $product->get_sale_price() ) { ?>
            <div class="wpcu-product__cover-content wpcu-product__cover-content--bottom-left">

                <?php if( 'yes' == $display_sale_ribbon && 'bottom_left' == $sale_ribbon_position && $product->is_on_sale() ) { ?>
                <span class="wpcu-badge wpcu-sale wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $sale_ribbon_text ) ? $sale_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_featured_ribbon && 'bottom_left' == $featured_ribbon_position && $product->is_featured() ) { ?>
                <span class="wpcu-badge wpcu-feature wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $feature_ribbon_text ) ? $feature_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_sold_out_ribbon && 'bottom_left' == $sold_out_ribbon_position && ! $product->is_in_stock() ) { ?>
                <span class="wpcu-badge wpcu-sold_out wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $sold_out_ribbon_text ) ? $sold_out_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_discount_ribbon && 'bottom_left' == $discount_ribbon_position && $product->get_sale_price() ) { ?>
                <span class="wpcu-badge wpcu-discount wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $discount ) ? '-'.$discount : ''; ?></span>
                <?php } ?>

            </div>
            <?php } ?>

            <?php if( ( 'yes' == $display_sale_ribbon && 'bottom_right' == $sale_ribbon_position && $product->is_on_sale() ) || ( 'yes' == $display_featured_ribbon && 'bottom_right' == $featured_ribbon_position && $product->is_featured() ) || ( 'yes' == $display_sold_out_ribbon && 'bottom_right' == $sold_out_ribbon_position && ! $product->is_in_stock() ) || ( 'yes' == $display_discount_ribbon && 'bottom_right' == $discount_ribbon_position ) && $product->get_sale_price()) { ?>
            <div class="wpcu-product__cover-content wpcu-product__cover-content--bottom-right">

                <?php if( 'yes' == $display_sale_ribbon && 'bottom_right' == $sale_ribbon_position && $product->is_on_sale() ) { ?>
                <span class="wpcu-badge wpcu-sale wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $sale_ribbon_text ) ? $sale_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_featured_ribbon && 'bottom_right' == $featured_ribbon_position && $product->is_featured() ) { ?>
                <span class="wpcu-badge wpcu-feature wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $feature_ribbon_text ) ? $feature_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_sold_out_ribbon && 'bottom_right' == $sold_out_ribbon_position && ! $product->is_in_stock() ) { ?>
                <span class="wpcu-badge wpcu-sold_out wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $sold_out_ribbon_text ) ? $sold_out_ribbon_text : ''; ?></span>
                <?php } ?>

                <?php if( 'yes' == $display_discount_ribbon && 'bottom_right' == $discount_ribbon_position && $product->get_sale_price() ) { ?>
                <span class="wpcu-badge wpcu-discount wpcu-badge--primary wpcu-badge--text-lg wpcu-badge--rounded-circle"><?php echo ! empty( $discount ) ? '-'.$discount : ''; ?></span>
                <?php } ?>

            </div>
            <?php }


    }
endif;

if ( ! function_exists( 'wcpcsu_product_image_html' ) ) {
	/**
	 * Build the <img> tag for a product card.
	 *
	 * Always outputs width/height (so the browser reserves space and nothing jumps)
	 * and native lazy loading for images that are not in the first visible row.
	 *
	 * @param int   $attachment_id Product thumbnail ID (0 when the product has none).
	 * @param array $args {
	 *     @type bool   $crop    Use the plugin's cropped size instead of WordPress' 'large' size.
	 *     @type int    $width   Crop width.
	 *     @type int    $height  Crop height.
	 *     @type bool   $eager   Load immediately (first visible row) instead of lazily.
	 *     @type string $alt     Alt text.
	 *     @type array  $columns Visible columns per breakpoint: [ 'desktop' => 4, 'tablet' => 2, 'mobile' => 1 ].
	 * }
	 * @return string
	 */
	function wcpcsu_product_image_html( $attachment_id, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'crop'    => true,
			'width'   => 300,
			'height'  => 300,
			'eager'   => false,
			'alt'     => '',
			'columns' => array(),
		) );

		$loading = $args['eager'] ? 'eager' : 'lazy';

		// How wide the card is per breakpoint, so the browser picks a large enough file from srcset.
		$sizes = '';
		if ( ! empty( $args['columns'] ) ) {
			$columns = wp_parse_args( $args['columns'], array( 'desktop' => 4, 'tablet' => 2, 'mobile' => 1 ) );
			$sizes   = sprintf(
				'(max-width: 767px) %1$dvw, (max-width: 1199px) %2$dvw, %3$dvw',
				max( 1, (int) ( 100 / max( 1, (int) $columns['mobile'] ) ) ),
				max( 1, (int) ( 100 / max( 1, (int) $columns['tablet'] ) ) ),
				max( 1, (int) ( 100 / max( 1, (int) $columns['desktop'] ) ) )
			);

			// Lazy images: let the browser use the real rendered width (sizes="auto"), with the
			// column estimate as fallback; vw overestimates cards inside a narrow content area.
			if ( ! $args['eager'] ) {
				$sizes = 'auto, ' . $sizes;
			}
		}

		// No thumbnail: WooCommerce placeholder, sized like the other cards.
		if ( ! $attachment_id ) {
			return sprintf(
				'<img src="%s" alt="%s" width="%d" height="%d" loading="%s" decoding="async">',
				esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ),
				esc_attr( $args['alt'] ),
				absint( $args['width'] ),
				absint( $args['height'] ),
				esc_attr( $loading )
			);
		}

		// Uncropped: WordPress' own markup gives srcset, sizes, width and height.
		if ( ! $args['crop'] ) {
			$attrs = array(
				'alt'      => $args['alt'],
				'loading'  => $loading,
				'decoding' => 'async',
			);

			if ( $sizes ) {
				$attrs['sizes'] = $sizes;
			}

			$html = wp_get_attachment_image( $attachment_id, 'large', false, $attrs );
			if ( $html ) {
				return $html;
			}
		}

		// Cropped: the plugin's own resized file, plus a 2x version for high-density screens
		// when the original is large enough.
		$image = wc_pcsu_image_cropping( $attachment_id, $args['width'], $args['height'], true, 100 );
		if ( empty( $image['url'] ) ) {
			return '';
		}

		$image_width = absint( ! empty( $image['width'] ) ? $image['width'] : $args['width'] );
		$srcset      = '';
		$metadata    = wp_get_attachment_metadata( $attachment_id );
		if ( ! empty( $metadata['width'] ) && ! empty( $metadata['height'] )
			&& $metadata['width'] >= 2 * $args['width'] && $metadata['height'] >= 2 * $args['height'] ) {
			$retina = wc_pcsu_image_cropping( $attachment_id, 2 * $args['width'], 2 * $args['height'], true, 100 );
			if ( ! empty( $retina['url'] ) ) {
				// Width descriptors + sizes: a card wider than the crop also gets the larger file.
				$srcset = sprintf(
					' srcset="%s %dw, %s %dw"%s',
					esc_url( $image['url'] ),
					$image_width,
					esc_url( $retina['url'] ),
					2 * $image_width,
					$sizes ? ' sizes="' . esc_attr( $sizes ) . '"' : ''
				);
			}
		}

		return sprintf(
			'<img src="%s"%s alt="%s" width="%d" height="%d" loading="%s" decoding="async">',
			esc_url( $image['url'] ),
			$srcset,
			esc_attr( $args['alt'] ),
			absint( ! empty( $image['width'] ) ? $image['width'] : $args['width'] ),
			absint( ! empty( $image['height'] ) ? $image['height'] : $args['height'] ),
			esc_attr( $loading )
		);
	}
}

if ( ! function_exists( 'wcpcsu_parse_id_list' ) ) {
	/**
	 * Product IDs from a saved list or a comma-separated attribute, in their given order.
	 *
	 * @param array|string $value IDs as an array or "12, 7, 30".
	 * @return int[] Unique positive IDs.
	 */
	function wcpcsu_parse_id_list( $value ) {
		$ids = is_array( $value ) ? $value : explode( ',', (string) $value );

		return array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );
	}
}

if ( ! function_exists( 'wcpcsu_parse_slug_list' ) ) {
	/**
	 * Term slugs from a saved list or a comma-separated attribute, in their given order.
	 *
	 * @param array|string $value Slugs as an array or "shoes, bags".
	 * @return string[] Unique non-empty slugs.
	 */
	function wcpcsu_parse_slug_list( $value ) {
		$slugs = is_array( $value ) ? $value : explode( ',', (string) $value );

		return array_values( array_unique( array_filter( array_map( 'sanitize_text_field', array_map( 'trim', $slugs ) ) ) ) );
	}
}

if ( ! function_exists( 'wcpcsu_current_product_id' ) ) {
	/**
	 * The product that "related" carousels relate to: the single product being viewed.
	 *
	 * The global $product isn't used: after another product loop on the page it still
	 * holds that loop's last product, which would make home/shop pages show unrelated
	 * products instead of the fallback.
	 *
	 * @return int Product ID, or 0 when the page isn't a single product.
	 */
	function wcpcsu_current_product_id() {
		$product_id = is_singular( 'product' ) ? get_queried_object_id() : 0;

		/**
		 * Filters the product used by "related" carousels (e.g. a preview product).
		 *
		 * @param int $product_id Product ID, or 0.
		 */
		return absint( apply_filters( 'wcpcsu_current_product_id', $product_id ) );
	}
}

if ( ! function_exists( 'wcpcsu_sanitize_array' ) ) {
	/**
	 * It sanitize a multi-dimensional array
	 * @param array &$array The array of the data to sanitize
	 * @return mixed
	 */
	function wcpcsu_sanitize_array( &$array ) {
		foreach ( $array as &$value ) {
			if ( ! is_array( $value ) ) {
				// sanitize if value is not an array
				$value = sanitize_text_field( $value );
			} else {
				// go inside this function again
				wcpcsu_sanitize_array( $value );
			}
		}
		return $array;
	}
}

/**
 * Checks if a string is a valid JSON-encoded string.
 *
 * @param string $data The string to be checked for JSON encoding.
 *
 * @return bool Returns true if the string is a valid JSON-encoded string, false otherwise.
 */
if ( ! function_exists( 'is_json_encoded' ) ) {
	function is_json_encoded( $data ) {
		json_decode( $data );
		return (json_last_error() == JSON_ERROR_NONE);
	}
}
