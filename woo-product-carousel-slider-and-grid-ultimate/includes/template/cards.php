<?php
/**
 * The product cards: one theme template per product in $loop.
 *
 * Included by WCPCSU_Shortcode::render() inside the carousel/grid wrapper, and on its own
 * in cards-only mode (AJAX filters). It reads render()'s local scope.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Columns visible per breakpoint: the first row loads eagerly, the rest lazily.
if ( 'carousel' == $layout || 'ticker' == $layout ) {
	$image_columns = array( 'desktop' => $carousel_desktop_column, 'tablet' => $carousel_tablet_column, 'mobile' => $carousel_mobile_column );
} elseif ( 'list' == $layout ) {
	// The list image takes about a third of the row; full width when stacked on phones.
	$image_columns = array( 'desktop' => 3, 'tablet' => 3, 'mobile' => 1 );
} else {
	$image_columns = array( 'desktop' => $g_column, 'tablet' => $g_tablet, 'mobile' => $g_mobile );
}
// Cards appended to a later page are never in the first row.
$eager_items   = ( $cards_only && $paged > 1 ) ? 0 : (int) $image_columns['desktop'];
$item_index    = 0;

while( $loop->have_posts() ) : $loop->the_post();

	global $post, $product;

	$thumb = get_post_thumbnail_id();

	// Image tag with width/height, lazy loading and srcset; cropped when cropping is enabled.
	$wpcsu_img_html = wcpcsu_product_image_html( $thumb, array(
		'crop'    => 'yes' === $img_crop,
		'width'   => $crop_image_width,
		'height'  => $crop_image_height,
		'eager'   => $item_index < $eager_items,
		'alt'     => get_the_title(),
		'columns' => $image_columns,
	) );
	$item_index++;
	$sale_price = $product->get_sale_price();
	$ratings    = ( ( $product->get_average_rating() / 5 ) * 100 );

	include WCPCSU_INC_DIR . 'template/theme/' . $theme . '.php';

endwhile;

wp_reset_postdata();
