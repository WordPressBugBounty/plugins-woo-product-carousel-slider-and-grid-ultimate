<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$layout                     = ! empty( $layout ) ? $layout : 'carousel';
$h_title_show               = ! empty( $h_title_show ) ? $h_title_show : 'no';
$display_full_title         = ! empty( $display_full_title ) ? $display_full_title : 'yes';
$ribbon                     = ! empty( $ribbon ) ? $ribbon : 'discount';
$header_position            = ! empty( $header_position ) ? $header_position : 'middle';
$total_products_label       = in_array( $layout, array( 'grid', 'list' ), true ) ? __( 'Products Per Page','woocommerce-product-carousel-slider-and-grid-ultimate' ) : __( 'Total Products to Display','woocommerce-product-carousel-slider-and-grid-ultimate' );
$theme                      = ! empty( $theme ) ? $theme : 'theme_1';
$display_sale_ribbon        = ! empty( $display_sale_ribbon ) ? $display_sale_ribbon : 'no';
$sale_ribbon_position       = ! empty( $sale_ribbon_position ) ? $sale_ribbon_position : 'top_left';
$display_featured_ribbon    = ! empty( $display_featured_ribbon ) ? $display_featured_ribbon : 'no';
$featured_ribbon_position   = ! empty( $featured_ribbon_position ) ? $featured_ribbon_position : 'top_right';
$display_sold_out_ribbon    = ! empty( $display_sold_out_ribbon ) ? $display_sold_out_ribbon : 'no';
$sold_out_ribbon_position   = ! empty( $sold_out_ribbon_position ) ? $sold_out_ribbon_position : 'bottom_left';
$display_discount_ribbon    = ! empty( $display_discount_ribbon ) ? $display_discount_ribbon : 'no';
$discount_ribbon_position   = ! empty( $discount_ribbon_position ) ? $discount_ribbon_position : 'bottom_right';
?>
<div id="lcsp-tab-5" class="lcsp-tab-content">
	<div class="cmb2-wrap form-table">
		<div id="cmb2-metabox" class="cmb2-metabox cmb-field-list">
			<div class="wcpscu-sections">
			<section class="wcpscu-section" data-wcpscu-section="layout">
			<h3 class="wcpscu-section__title"><?php esc_html_e( 'Layout', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></h3>


			<div class="cmb-row cmb-type-text-medium">
				<div class="cmb-th">
					<label for="lcsp_slider_title"><?php esc_html_e( 'Layout', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<?php
					// One tile per layout. data-tab: the settings tab the layout uses (wcpcs-admin.js).
					$layout_tiles = array(
						'carousel' => array( 'label' => __( 'Carousel', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 'img' => 'carousel.jpg', 'tab' => 'carousel' ),
						'grid'     => array( 'label' => __( 'Grid', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 'img' => 'grid.jpg', 'tab' => 'grid' ),
						'list'     => array( 'label' => __( 'List', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 'img' => 'list.svg', 'tab' => 'grid' ),
						'ticker'   => array( 'label' => __( 'Ticker', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 'img' => 'ticker.svg', 'tab' => 'carousel' ),
					);
					$pro_layout_tiles = array(
						'thumbnails' => array( 'label' => __( 'Thumbnails', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 'img' => 'thumbnails.svg' ),
						'masonry'    => array( 'label' => __( 'Masonry', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 'img' => 'masonry.svg' ),
						'tabbed'     => array( 'label' => __( 'Tabbed', 'woocommerce-product-carousel-slider-and-grid-ultimate' ), 'img' => 'tabbed.svg' ),
					);
					?>
					<div class="cmd-switch">
						<?php foreach ( $layout_tiles as $tile_layout => $tile ) { ?>
						<div class="cmd-switch-<?php echo esc_attr( $tile_layout ); ?> <?php echo ( $tile_layout == $layout ) ? 'active' : ''; ?>" data-layout="<?php echo esc_attr( $tile_layout ); ?>" data-tab="<?php echo esc_attr( $tile['tab'] ); ?>">
							<div class="cmd-switch-item cmd-switch-<?php echo esc_attr( $tile_layout ); ?>-img" data-value="<?php echo esc_attr( $tile_layout ); ?>">
								<span class="cmd-switch-item-icon">
									<span class="cmd-switch-item-icon__wrapper">
										<img class="svg_compile" src="<?php echo esc_url( WCPCSU_URL . 'assets/icons/square-check-solid.svg' ); ?>" >
									</span>
								</span>
								<img src="<?php echo esc_url( WCPCSU_URL . 'admin/img/' . $tile['img'] ); ?>" alt="<?php echo esc_attr( $tile_layout ); ?>">
								<input type="radio" name="wcpscu[layout]" class="wcpscu_radio_layout wcpscu_<?php echo esc_attr( $tile_layout ); ?>_layout" value="<?php echo esc_attr( $tile_layout ); ?>" <?php checked( $layout, $tile_layout ); ?>>
							</div>
							<p><?php echo esc_html( $tile['label'] ); ?></p>
						</div>
						<?php } ?>
						<?php foreach ( $pro_layout_tiles as $tile_layout => $tile ) { ?>
						<div class="cmd-switch-pro" title="<?php esc_attr_e( 'Available in the Pro version', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">
							<div class="cmd-switch-item">
								<img src="<?php echo esc_url( WCPCSU_URL . 'admin/img/' . $tile['img'] ); ?>" alt="<?php echo esc_attr( $tile_layout ); ?>">
								<a class="cmd-switch-pro-badge" href="https://wpwax.com/product/woocommerce-product-carousel-slider-grid-ultimate-pro" target="_blank"><?php esc_html_e( 'Pro', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></a>
							</div>
							<p><?php echo esc_html( $tile['label'] ); ?></p>
						</div>
						<?php } ?>
					</div>
					<p class="cmb2-metabox-description"><?php esc_html_e( 'Ticker: a row that scrolls at a constant speed (Carousel Settings). List: one product per row, image beside the details (Grid Settings).', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></p>
				</div>
			</div>
			<!--Select theme-->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label for="lcsp_ap"><?php esc_html_e( 'Select Theme', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<div class="cmb-theme-wrapper">
						<select id="theme_" class="wcpscu_theme" name="wcpscu[theme]">
							<option value="theme_1" <?php selected( $theme, 'theme_1'); ?> ><?php esc_html_e( 'Theme-1', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="theme_2" <?php selected( $theme, 'theme_2'); ?>><?php esc_html_e( 'Theme-2
							', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="theme_3" <?php selected( $theme, 'theme_3'); ?>><?php esc_html_e( 'Theme-3
							', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-4 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-5 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-6 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-7 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-8 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-9 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-10 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-11 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-12 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-13 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-14 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option disabled><?php esc_html_e( 'Theme-15 (Pro)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
						</select>
						<a target="_blank" ref="noopener" href="https://demo.wpwax.com/plugin/woocommerce-product-carousel-slider-and-grid-ultimate-demo-slider/"><?php esc_html_e( 'View All Themes', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></a>
					</div>
				</div>
			</div>

			<div class="cmb-row cmb-type-radio bolod">
				<div class="cmb-th">
					<label for="lcsp_ic">
						<?php esc_html_e( 'Display Header', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
					</label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li>
							<input type="radio" class="cmb2-option cmb2-radio-switch1" name="wcpscu[h_title_show]"
								id="wcpscu[h_title_show]1" value="yes" <?php checked( 'yes', $h_title_show, true );  ?>>
							<label for="wcpscu[h_title_show]1">
								<?php esc_html_e( 'Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
							</label>
						</li>
						<li>
							<input type="radio" class="cmb2-option cmb2-radio-switch2" name="wcpscu[h_title_show]"
								id="wcpscu[h_title_show]2" value="no" <?php checked( 'no', $h_title_show, true );  ?>>
							<label for="wcpscu[h_title_show]2">
								<?php esc_html_e( 'No', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
							</label>
						</li>
					</ul>
					<div class="cmb-td-medium">
						<label for="lcsp_slider_title"><?php esc_html_e( 'Header Title', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						<input type="text" class="cmb2-text-medium" name="wcpscu[header_title]" id="lcsp_slider_title"
							value="<?php echo ! empty( $header_title ) ? esc_attr( $header_title ) : ''; ?>">
					</div>
					<div class="cmb-td-multicheck">
						<label
							for="wcpscup_products_type"><?php esc_html_e( 'Header Position', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						<ul class="cmb2-radio-list cmb2-list">
							<li>
								<input type="radio" class="cmb2-option" name="wcpscu[header_position]" id="center" value="center"
								<?php checked( 'Center', $header_position, true );  ?>>
								<label for="center"><?php esc_html_e( 'Center', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
							</li>

							<li>
								<input type="radio" class="cmb2-option" name="wcpscu[header_position]" id="left" value="left"
									<?php checked( 'left', $header_position ); ?>>
								<label for="left"><?php esc_html_e( 'Left', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
							</li>

							<li>
								<input type="radio" class="cmb2-option" name="wcpscu[header_position]" id="right" value="right"
									<?php checked( 'right', $header_position ); ?>>
								<label for="right"><?php esc_html_e( 'Right', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
							</li>

						</ul>
					</div>
				</div>
			</div>

			</section>

			<section class="wcpscu-section" data-wcpscu-section="products">
			<h3 class="wcpscu-section__title"><?php esc_html_e( 'Products', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></h3>

			<!-- Total Products -->
			<div class="cmb-row cmb-type-text-medium">
				<div class="cmb-th">
					<label for="wcpscu_total_products"
						id="wcpscu_total_pdt"><?php echo ! empty( $total_products_label ) ? esc_html( $total_products_label ) : esc_html__( 'Total Products to Display','woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<input type="text" class="cmb2-text-small" name="wcpscu[total_products]" id="wcpscu_total_products"
						value="<?php echo esc_attr( ! empty( $total_products ) ? intval( $total_products ) : 12 ); ?>">
					<p class="cmb2-metabox-description">
						<?php esc_html_e( 'How many products to display in the carousel slider', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
					</p>
				</div>
			</div>

			<!-- Products type-->
			<div class="cmb-row cmb-type-multicheck wcpscu-products-types">
				<div class="cmb-th">
					<label for="wcpscu_products_type"><?php esc_html_e( 'Products Type', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list">
						<li><input type="radio" class="cmb2-option" name="wcpscu[products_type]"
								id="wcpscu_products_type" value="latest"
								<?php if ( ( !empty($products_type) && 'latest' == $products_type) || empty($products_type)) { echo 'checked';} ?>>
							<label
								for="wcpscu_products_type"><?php esc_html_e( 'Latest Products', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option" name="wcpscu[products_type]"
								id="wcpscu_products_type9" value="older"
								<?php if( ! empty( $products_type ) ) { checked( 'older', $products_type ); } ?>> <label
								for="wcpscu_products_type9"><?php esc_html_e('Older Products', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option" name="wcpscu[products_type]"
								id="wcpscu_products_type3" value="featured"
								<?php if( ! empty( $products_type ) ) { checked('featured', $products_type ); } ?>> <label
								for="wcpscu_products_type3"><?php esc_html_e('Featured Products', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option" name="wcpscu[products_type]"
								id="wcpscu_products_type_handpicked" value="handpicked"
								<?php if( ! empty( $products_type ) ) { checked( 'handpicked', $products_type ); } ?>> <label
								for="wcpscu_products_type_handpicked"><?php esc_html_e('Handpicked Products', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option" name="wcpscu[products_type]"
								id="wcpscu_products_type_related" value="related"
								<?php if( ! empty( $products_type ) ) { checked( 'related', $products_type ); } ?>> <label
								for="wcpscu_products_type_related"><?php esc_html_e('Related Products', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
					<!-- <p class="cmb2-metabox-description">
						<?php esc_html_e('What type of products to display in the carousel slider', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
					</p> -->
					<ul class="cmb2-radio-list-pro">
						<p>Following options
							available in <a
								href="https://wpwax.com/product/woocommerce-product-carousel-slider-grid-ultimate-pro"
								target="_blank"><?php esc_html_e( 'Pro Version', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></a>:</p>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="onsale"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('On Sale Products', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="bestselling"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Best Selling Products', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="bestselling"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Top Rated Products', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="bestselling"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Random Products', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="category"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Category Products', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li class="productsbyidw"><input disabled type="radio" class="cmb2-option"
								name="wcpscu_products_type" id="wcpscu_ds_products_type" value="productsbyid"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Products by ID', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="productsbysku"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Products by SKU', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="productsbytag"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Products by Tags', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="productsbyyear"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Products by Year', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="productsbymonth"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Products by Month', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="upsells"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Upsells', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="cross_sells"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Cross-sells', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="recently_viewed"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Recently Viewed Products', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="by_attribute"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Products by Attribute', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_products_type"
								id="wcpscu_ds_products_type" value="by_brand"> <label
								for="wcpscu_ds_products_type"><?php esc_html_e('Products by Brand', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
				</div>
			</div>

			<!-- Handpicked products: search + sortable list (shown for the "handpicked" type) -->
			<div class="cmb-row cmb-type-text wcpscu-products-type-depend" data-products-type="handpicked">
				<div class="cmb-th">
					<label for="wcpscu_handpicked_search"><?php esc_html_e( 'Handpicked Products', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<div class="wcpscu-handpicked">
						<?php // No name attribute: the search box itself is never saved, only the list below. ?>
						<select id="wcpscu_handpicked_search" class="wc-product-search" style="width: 100%;"
							data-placeholder="<?php esc_attr_e( 'Search for a product by name or SKU…', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>"
							data-action="woocommerce_json_search_products"
							data-allow_clear="true"></select>
						<ul class="wcpscu-handpicked__list" data-remove-label="<?php esc_attr_e( 'Remove', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">
							<?php
							foreach ( wcpcsu_parse_id_list( ! empty( $handpicked_products ) ? $handpicked_products : array() ) as $handpicked_id ) {
								$handpicked_product = wc_get_product( $handpicked_id );
								if ( ! $handpicked_product ) {
									continue;
								}
								?>
								<li class="wcpscu-handpicked__item">
									<span class="dashicons dashicons-menu wcpscu-handpicked__handle" aria-hidden="true"></span>
									<span class="wcpscu-handpicked__name"><?php echo esc_html( wp_strip_all_tags( $handpicked_product->get_formatted_name() ) ); ?></span>
									<button type="button" class="wcpscu-handpicked__remove" aria-label="<?php esc_attr_e( 'Remove', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">&times;</button>
									<input type="hidden" name="wcpscu[handpicked_products][]" value="<?php echo esc_attr( $handpicked_id ); ?>">
								</li>
							<?php } ?>
						</ul>
					</div>
					<p class="cmb2-metabox-description"><?php esc_html_e( 'Add products, then drag them into the order they should appear in. All picked products are shown.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></p>
				</div>
			</div>

			<!-- Related products fallback (shown for the "related" type) -->
			<div class="cmb-row cmb-type-radio wcpscu-products-type-depend" data-products-type="related">
				<div class="cmb-th">
					<label for="wcpscu_related_fallback"><?php esc_html_e( 'When There Is No Current Product', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list">
						<li><input type="radio" class="cmb2-option" name="wcpscu[related_fallback]"
								id="wcpscu_related_fallback" value="latest"
								<?php if ( empty( $related_fallback ) || 'latest' == $related_fallback ) { echo 'checked'; } ?>>
							<label for="wcpscu_related_fallback"><?php esc_html_e( 'Show latest products', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option" name="wcpscu[related_fallback]"
								id="wcpscu_related_fallback2" value="none"
								<?php if ( ! empty( $related_fallback ) ) { checked( 'none', $related_fallback ); } ?>>
							<label for="wcpscu_related_fallback2"><?php esc_html_e( 'Show nothing', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
					</ul>
					<p class="cmb2-metabox-description"><?php esc_html_e( 'Related products follow the product being viewed. On other pages (home, shop, the live preview) this is shown instead.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></p>
				</div>
			</div>

			<!-- Category filter tabs (not for handpicked / related: their products don't split by category) -->
			<div class="cmb-row cmb-type-radio wcpscu-products-type-depend" data-products-type="latest older featured">
				<div class="cmb-th">
					<label for="wcpscu_filter_tabs1"><?php esc_html_e( 'Category Filter Tabs', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li><input type="radio" class="cmb2-option cmb2-radio-switch1 wcpscu-filter-tabs" name="wcpscu[filter_tabs]"
								id="wcpscu_filter_tabs1" value="yes"
								<?php if ( ! empty( $filter_tabs ) ) { checked( 'yes', $filter_tabs ); } ?>>
							<label for="wcpscu_filter_tabs1"><?php esc_html_e( 'Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option cmb2-radio-switch2 wcpscu-filter-tabs" name="wcpscu[filter_tabs]"
								id="wcpscu_filter_tabs2" value="no"
								<?php if ( empty( $filter_tabs ) || 'no' === $filter_tabs ) { echo 'checked'; } ?>>
							<label for="wcpscu_filter_tabs2"><?php esc_html_e( 'No', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
					</ul>
					<p class="cmb2-metabox-description"><?php esc_html_e( 'Shoppers switch between categories above the products, without reloading the page.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></p>
				</div>
			</div>

			<div class="cmb-row cmb-type-text wcpscu-products-type-depend wcpscu-filter-tabs-depend" data-products-type="latest older featured">
				<div class="cmb-th">
					<label for="wcpscu_filter_categories_add"><?php esc_html_e( 'Tab Categories', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<div class="wcpscu-handpicked wcpscu-tab-categories">
						<?php // No name attribute: only the list below is saved. ?>
						<select id="wcpscu_filter_categories_add" style="width: 100%;">
							<option value=""><?php esc_html_e( 'Add a category…', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<?php
							$wcpscu_cat_terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
							if ( ! is_wp_error( $wcpscu_cat_terms ) ) {
								// Indented by depth, parents before their children.
								echo walk_category_dropdown_tree( $wcpscu_cat_terms, 0, array( 'value_field' => 'slug', 'selected' => '', 'show_count' => 0 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core walker, escapes names and values.
							}
							?>
						</select>
						<ul class="wcpscu-handpicked__list wcpscu-tab-categories__list" data-remove-label="<?php esc_attr_e( 'Remove', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">
							<?php
							foreach ( wcpcsu_parse_slug_list( ! empty( $filter_categories ) ? $filter_categories : array() ) as $wcpscu_cat_slug ) {
								$wcpscu_cat = get_term_by( 'slug', $wcpscu_cat_slug, 'product_cat' );
								if ( ! $wcpscu_cat ) {
									continue;
								}
								?>
								<li class="wcpscu-handpicked__item">
									<span class="dashicons dashicons-menu wcpscu-handpicked__handle" aria-hidden="true"></span>
									<span class="wcpscu-handpicked__name"><?php echo esc_html( $wcpscu_cat->name ); ?></span>
									<button type="button" class="wcpscu-handpicked__remove" aria-label="<?php esc_attr_e( 'Remove', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">&times;</button>
									<input type="hidden" name="wcpscu[filter_categories][]" value="<?php echo esc_attr( $wcpscu_cat->slug ); ?>">
								</li>
							<?php } ?>
						</ul>
					</div>
					<p class="cmb2-metabox-description"><?php esc_html_e( 'Each category becomes a tab, in this order (drag to reorder). A tab shows the products of the Products Type above that are in that category or its subcategories.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></p>
				</div>
			</div>

			<div class="cmb-row cmb-type-text wcpscu-products-type-depend wcpscu-filter-tabs-depend" data-products-type="latest older featured">
				<div class="cmb-th">
					<label for="wcpscu_filter_all_text"><?php esc_html_e( '"All" Tab Label', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<input type="text" class="cmb2-text-medium" name="wcpscu[filter_all_text]" id="wcpscu_filter_all_text"
						value="<?php echo ! empty( $filter_all_text ) ? esc_attr( $filter_all_text ) : ''; ?>"
						placeholder="<?php esc_attr_e( 'All', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">
					<p class="cmb2-metabox-description"><?php esc_html_e( 'The first tab, active when the page loads. It shows the products of the Products Type above (up to "Total Products to Display"), from every category.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></p>
				</div>
			</div>

			<!-- Pro-only filters: disabled teasers, named outside wcpscu[...] so they are never saved -->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label><?php esc_html_e( 'More Filters', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list-pro">
						<p><?php esc_html_e( 'Following options available in', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?> <a
								href="https://wpwax.com/product/woocommerce-product-carousel-slider-grid-ultimate-pro"
								target="_blank"><?php esc_html_e( 'Pro Version', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></a>:</p>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_filters[]" id="wcpscu_pro_filter_search" value="search">
							<label for="wcpscu_pro_filter_search"><?php esc_html_e( 'Search Box (shoppers type a keyword, results update as they type)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_filters[]" id="wcpscu_pro_filter_multi" value="multi">
							<label for="wcpscu_pro_filter_multi"><?php esc_html_e( 'Multi-Select Tabs (switch on several categories at once)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_filters[]" id="wcpscu_pro_filter_load_more" value="load_more">
							<label for="wcpscu_pro_filter_load_more"><?php esc_html_e( 'Load More Button / Infinite Scroll', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_filters[]" id="wcpscu_pro_filter_numbers" value="numbers">
							<label for="wcpscu_pro_filter_numbers"><?php esc_html_e( 'Prev/Next Buttons for Numbered Pagination', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
					</ul>
				</div>
			</div>

			</section>

			<section class="wcpscu-section" data-wcpscu-section="card">
			<h3 class="wcpscu-section__title"><?php esc_html_e( 'Card Content', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></h3>

			<!--Display Product Title-->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label
						for="wcpscu_display_title"><?php esc_html_e( 'Display Product Title', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li>
							<input type="radio" class="cmb2-option cmb2-radio-switch1" name="wcpscu[display_title]"
								id="wcpscu_display_title1" value="yes"
								<?php if ( empty( $display_title ) || 'yes' === $display_title ) { echo 'checked'; } ?>>
							<label for="wcpscu_display_title1"><?php esc_html_e( 'Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li>
							<input type="radio" class="cmb2-option cmb2-radio-switch2" name="wcpscu[display_title]"
								id="wcpscu_display_title2" value="no"
								<?php if ( ! empty( $display_title ) ) { checked( 'no', $display_title ); } ?>>
							<label for="wcpscu_display_title2"><?php esc_html_e( 'No', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
					</ul>
				</div>
			</div>

			<!--Display Product Price-->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label
						for="wcpscu_display_price"><?php esc_html_e('Display Product Price', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li><input type="radio" class="cmb2-option cmb2-radio-switch1" name="wcpscu[display_price]"
								id="wcpscu_display_price1" value="yes"
								<?php if(empty($display_price) || 'yes' === $display_price) { echo 'checked'; } ?>>
							<label for="wcpscu_display_price1"><?php esc_html_e('Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option cmb2-radio-switch2" name="wcpscu[display_price]"
								id="wcpscu_display_price2" value="no"
								<?php if ( ! empty( $display_price ) ) { checked( 'no', $display_price ) ; } ?>>
							<label for="wcpscu_display_price2"><?php esc_html_e('No', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
				</div>
			</div>

			<!--Display Product Ratings-->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label
						for="wcpscu_display_ratings"><?php esc_html_e('Display Product Ratings', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li>
							<input type="radio" class="cmb2-option cmb2-radio-switch1" name="wcpscu[display_ratings]"
								id="wcpscu_display_ratings1" value="yes"
								<?php if(empty($display_ratings) || 'yes' === $display_ratings) { echo 'checked'; } ?>>
							<label for="wcpscu_display_ratings1"><?php esc_html_e('Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option cmb2-radio-switch2" name="wcpscu[display_ratings]"
								id="wcpscu_display_ratings2" value="no"
								<?php if (!empty($display_ratings)) { checked('no', $display_ratings ); } ?>>
							<label for="wcpscu_display_ratings2"><?php _e('No', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
				</div>
			</div>

			<!--Display "Add to Cart" button-->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label
						for="wcpscu_display_cart"><?php esc_html_e('Display "Add to Cart" Button', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li>
							<input type="radio" class="cmb2-option cmb2-radio-switch1" name="wcpscu[display_cart]"
								id="wcpscu_display_cart1" value="yes"
								<?php if(empty($display_cart) || 'yes' === $display_cart) { echo 'checked'; } ?>>
							<label for="wcpscu_display_cart1"><?php esc_html_e('Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li>
							<input type="radio" class="cmb2-option cmb2-radio-switch2" name="wcpscu[display_cart]"
								id="wcpscu_display_cart2" value="no"
								<?php if( ! empty( $display_cart ) ) { checked('no', $display_cart ); } ?>>
							<label for="wcpscu_display_cart2"><?php esc_html_e('No', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
				</div>
			</div>
			<!-- Variation swatches (Pro): disabled teaser, named outside wcpscu[...] so it is never saved -->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label><?php esc_html_e( 'Variation Swatches', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list-pro">
						<p><?php esc_html_e( 'Following options available in', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?> <a
								href="https://wpwax.com/product/woocommerce-product-carousel-slider-grid-ultimate-pro"
								target="_blank"><?php esc_html_e( 'Pro Version', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></a>:</p>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_swatches" id="wcpscu_pro_swatches" value="yes">
							<label for="wcpscu_pro_swatches"><?php esc_html_e( 'Choose colour, size, … on the card and add that variation to the cart', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
					</ul>
				</div>
			</div>

			<!--Display "Quick View" button-->
			<div class="cmb-row cmb-type-radio theme_2">
				<div class="cmb-th">
					<label
						for="wcpscu_quick_view"><?php esc_html_e('Display "Quick View Icon"', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li>
							<input type="radio" class="cmb2-option cmb2-radio-switch1" name="wcpscu[quick_view]"
								id="wcpscu_quick_view1" value="yes"
								<?php if(empty($quick_view) || 'yes' === $quick_view) { echo 'checked'; } ?>>
							<label for="wcpscu_quick_view1"><?php esc_html_e('Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li>
							<input type="radio" class="cmb2-option cmb2-radio-switch2" name="wcpscu[quick_view]"
								id="wcpscu_quick_view2" value="no"
								<?php if( ! empty( $quick_view ) ) { checked( 'no', $quick_view ); } ?>>
							<label for="wcpscu_quick_view2"><?php esc_html_e('No', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
				</div>
			</div>

			</section>

			<section class="wcpscu-section" data-wcpscu-section="badges">
			<h3 class="wcpscu-section__title"><?php esc_html_e( 'Badges', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></h3>

			<!--Show "sale' Ribbon for the product on sale-->
			<div class="cmb-row cmb-type-radio" id="sale_ribbon_wrapper">
				<div class="cmb-th">
					<label
						for="wcpscu_display_sale_ribbon"><?php esc_html_e('Display "Sale" Badge', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li><input type="radio" class="cmb2-option cmb2-radio-switch1"
								name="wcpscu[display_sale_ribbon]" id="wcpscu_display_sale_ribbon1" value="yes"
								<?php checked( $display_sale_ribbon, 'yes' ); ?>>
							<label
								for="wcpscu_display_sale_ribbon1"><?php esc_html_e('Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option cmb2-radio-switch2"
								name="wcpscu[display_sale_ribbon]" id="wcpscu_display_sale_ribbon2" value="no"
								<?php checked( $display_sale_ribbon, 'no' ); ?>>
							<label
								for="wcpscu_display_sale_ribbon2"><?php esc_html_e('No', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
					<!-- <p class="cmb2-metabox-description">
						<?php esc_html_e('Whether to show the "Sale" ribbon for On-Sale products or not', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
					</p> -->

					<!--Text for the sale ribbon-->
					<div id="sale_ribbon_text_wrapper">
						<label for="wcpscu_sale_ribbon_text">
								<?php esc_html_e( 'Text', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
							</label>
						<input type="text" class="cmb2-text-medium" name="wcpscu[sale_ribbon_text]"
							id="wcpscu_sale_ribbon_text"
							value="<?php echo ! empty( $sale_ribbon_text ) ?  esc_attr( $sale_ribbon_text ) : esc_html__('Sale!', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>"
							placeholder="<?php esc_attr_e( 'e.g. Sale!', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">
					</div>

					<!--Position of sale ribbon -->
					<div id="sale_ribbon_text_wrapper">
						<label for="sale_ribbon_position">
								<?php esc_html_e( 'Position', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
						</label>
						<select id="sale_ribbon_position" class="sale_ribbon_position" name="wcpscu[sale_ribbon_position]">
							<option value="top_left" <?php selected( $sale_ribbon_position, 'top_left'); ?> >
								<?php esc_html_e( 'Top Left', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
							</option>
							<option value="top_right" <?php selected( $sale_ribbon_position, 'top_right'); ?>>
								<?php esc_html_e( 'Top Right', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
							</option>
							<option value="bottom_left" <?php selected( $sale_ribbon_position, 'bottom_left'); ?>>
								<?php esc_html_e( 'Bottom Left', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
							</option>
							<option value="bottom_right" <?php selected( $sale_ribbon_position, 'bottom_right'); ?>>
								<?php esc_html_e( 'Bottom Right', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?> 
							</option>
						</select>
					</div>

				</div>
			</div>



			<!--Display "Featured" Ribbon-->
			<div class="cmb-row cmb-type-radio" id="feature_ribbon_wrapper">
				<div class="cmb-th">
					<label for="wcpscu_display_featured_ribbon"><?php esc_html_e('Display "Featured" Badge', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li><input type="radio" class="cmb2-option cmb2-radio-switch1"
								name="wcpscu[display_featured_ribbon]" id="wcpscu_display_featured_ribbon1" value="yes"
								<?php checked( $display_featured_ribbon, 'yes' ); ?>>
							<label
								for="wcpscu_display_featured_ribbon1"><?php esc_html_e('Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option cmb2-radio-switch2"
								name="wcpscu[display_featured_ribbon]" id="wcpscu_display_featured_ribbon2" value="no"
								<?php checked( $display_featured_ribbon, 'no' ); ?>>
							<label
								for="wcpscu_display_featured_ribbon2"><?php esc_html_e('No', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
					<!-- <p class="cmb2-metabox-description">
						<?php esc_html_e('Whether to show the "Featured" ribbon for featured products or not.', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
					</p> -->
					<!-- Text for the feature ribbon -->
					<div id="feature_ribbon_text_wrapper">
						<label for="wcpscu_feature_ribbon_text"><?php esc_html_e( 'Text', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						<input type="text" class="cmb2-text-medium" name="wcpscu[feature_ribbon_text]"
							id="wcpscu_feature_ribbon_text"
							value="<?php echo ! empty( $feature_ribbon_text ) ? esc_attr( $feature_ribbon_text ) : esc_html__('Featured!', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>"
							placeholder="<?php esc_attr_e( 'e.g. Featured!', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">
						<p class="cmb2-metabox-description">
							<?php esc_html_e('Enter the text for the featured ribbon. Default text is "Featured!".', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
						</p>
					</div>

					<!--Position of Featured ribbon -->
					<div id="featured_ribbon_text_wrapper">
						<label for="featured_ribbon_position">
								<?php esc_html_e( 'Position', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
						</label>
						<select id="featured_ribbon_position" class="featured_ribbon_position" name="wcpscu[featured_ribbon_position]">
							<option value="top_left" <?php selected( $featured_ribbon_position, 'top_left'); ?> ><?php esc_html_e( 'Top Left', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="top_right" <?php selected( $featured_ribbon_position, 'top_right'); ?>><?php esc_html_e( 'Top Right', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="bottom_left" <?php selected( $featured_ribbon_position, 'bottom_left'); ?>><?php esc_html_e( 'Bottom Left', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="bottom_right" <?php selected( $featured_ribbon_position, 'bottom_right'); ?>><?php esc_html_e( 'Bottom Right', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
							</option>
						</select>
					</div>
				</div>
			</div>


			<!--Show ribbon for sold out products-->
			<div class="cmb-row cmb-type-radio" id="sold_out_ribbon_wrapper">
				<div class="cmb-th">
					<label
						for="wcpscu_display_sold_out_ribbon"><?php esc_html_e('Display the "Sold Out" Badge', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li><input type="radio" class="cmb2-option cmb2-radio-switch1"
								name="wcpscu[display_sold_out_ribbon]" id="wcpscu_display_sold_out_ribbon1" value="yes"
								<?php checked( $display_sold_out_ribbon, 'yes' ); ?>>
							<label
								for="wcpscu_display_sold_out_ribbon1"><?php esc_html_e('Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option cmb2-radio-switch2"
								name="wcpscu[display_sold_out_ribbon]" id="wcpscu_display_sold_out_ribbon2" value="no"
								<?php checked( $display_sold_out_ribbon, 'no' ); ?>>
							<label
								for="wcpscu_display_sold_out_ribbon2"><?php esc_html_e('No', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
					<!-- <p class="cmb2-metabox-description">
						<?php esc_html_e('Whether to display the "Sold Out" ribbon for out-of-stock products or not', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
					</p> -->

					<!-- Text for the sold out ribbon -->

					<div id="sold_out_ribbon_text_wrapper">
						<label for="wcpscu_sold_out_ribbon_text"><?php esc_html_e( 'Text', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						<input type="text" class="cmb2-text-medium" name="wcpscu[sold_out_ribbon_text]"
							id="wcpscu_sold_out_ribbon_text"
							value="<?php echo ! empty( $sold_out_ribbon_text ) ? esc_attr( $sold_out_ribbon_text ) : esc_html__('Sold Out!', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>"
							placeholder="<?php esc_attr_e( 'e.g. Sold Out!', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>">
						<p class="cmb2-metabox-description">
							<?php esc_html_e('Enter the text for the sold out ribbon. Default text is "Sold Out!".', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
						</p>
					</div>

					<!--Position of Featured ribbon -->
					<div id="sold_out_ribbon_text_wrapper">
						<label for="sold_out_ribbon_position">
								<?php esc_html_e( 'Position', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
						</label>
						<select id="sold_out_ribbon_position" class="sold_out_ribbon_position" name="wcpscu[sold_out_ribbon_position]">
							<option value="top_left" <?php selected( $sold_out_ribbon_position, 'top_left'); ?> ><?php esc_html_e( 'Top Left', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="top_right" <?php selected( $sold_out_ribbon_position, 'top_right'); ?>><?php esc_html_e( 'Top Right', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="bottom_left" <?php selected( $sold_out_ribbon_position, 'bottom_left'); ?>><?php esc_html_e( 'Bottom Left', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="bottom_right" <?php selected( $sold_out_ribbon_position, 'bottom_right'); ?>><?php esc_html_e( 'Bottom Right', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
							</option>
						</select>
					</div>

				</div>
			</div>

			<!--Show ribbon for sold out products-->
			<div class="cmb-row cmb-type-radio" id="discount_ribbon_wrapper">
				<div class="cmb-th">
					<label
						for="wcpscu_display_discount_ribbon"><?php esc_html_e('Display the "Discount" Badge', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li><input type="radio" class="cmb2-option cmb2-radio-switch1"
								name="wcpscu[display_discount_ribbon]" id="wcpscu_display_discount_ribbon1" value="yes"
								<?php checked( $display_discount_ribbon, 'yes' ); ?>>
							<label
								for="wcpscu_display_discount_ribbon1"><?php esc_html_e('Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option cmb2-radio-switch2"
								name="wcpscu[display_discount_ribbon]" id="wcpscu_display_discount_ribbon2" value="no"
								<?php checked( $display_discount_ribbon, 'no' ); ?>>
							<label
								for="wcpscu_display_discount_ribbon2"><?php esc_html_e('No', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
					<!-- <p class="cmb2-metabox-description">
						<?php esc_html_e('Whether to display the "Sold Out" ribbon for out-of-stock products or not', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
					</p> -->

					<!--Position of Featured ribbon -->
					<div id="discount_ribbon_text_wrapper">
						<label for="discount_ribbon_position">
								<?php esc_html_e( 'Position', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
						</label>
						<select id="discount_ribbon_position" class="discount_ribbon_position" name="wcpscu[discount_ribbon_position]">
							<option value="top_left" <?php selected( $discount_ribbon_position, 'top_left'); ?> ><?php esc_html_e( 'Top Left', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="top_right" <?php selected( $discount_ribbon_position, 'top_right'); ?>><?php esc_html_e( 'Top Right', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="bottom_left" <?php selected( $discount_ribbon_position, 'bottom_left'); ?>><?php esc_html_e( 'Bottom Left', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></option>
							<option value="bottom_right" <?php selected( $discount_ribbon_position, 'bottom_right'); ?>><?php esc_html_e( 'Bottom Right', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
							</option>
						</select>
					</div>

				</div>
			</div>

			</section>

			<section class="wcpscu-section" data-wcpscu-section="image">
			<h3 class="wcpscu-section__title"><?php esc_html_e( 'Image & Media', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></h3>

			<!--Hover image swap-->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label for="wcpscu_img_hover_swap"><?php esc_html_e('Swap Image on Hover', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li><input type="radio" class="cmb2-option cmb2-radio-switch1" name="wcpscu[img_hover_swap]"
								id="wcpscu_img_hover_swap1" value="yes"
								<?php if( ! empty( $img_hover_swap ) ) { checked( 'yes', $img_hover_swap ); } ?>>
							<label for="wcpscu_img_hover_swap1"><?php esc_html_e('Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option cmb2-radio-switch2" name="wcpscu[img_hover_swap]"
								id="wcpscu_img_hover_swap2" value="no"
								<?php if( empty( $img_hover_swap ) || 'no' === $img_hover_swap ) { echo 'checked'; } ?>>
							<label for="wcpscu_img_hover_swap2"><?php esc_html_e('No', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
					</ul>
					<p class="cmb2-metabox-description">
						<?php esc_html_e('Shows the first image of the product gallery when the card is hovered. Products without gallery images keep their image.', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
					</p>
				</div>
			</div>

			<!--Entrance animation (Pro teaser)-->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label><?php esc_html_e( 'Entrance Animation', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list-pro">
						<p><?php esc_html_e( 'Following options available in', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?> <a
								href="https://wpwax.com/product/woocommerce-product-carousel-slider-grid-ultimate-pro"
								target="_blank"><?php esc_html_e( 'Pro Version', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></a>:</p>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_pro_entrance" id="wcpscu_pro_entrance_fade_up" value="fade-up">
							<label for="wcpscu_pro_entrance_fade_up"><?php esc_html_e( 'Fade Up', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_pro_entrance" id="wcpscu_pro_entrance_zoom" value="zoom">
							<label for="wcpscu_pro_entrance_zoom"><?php esc_html_e( 'Zoom', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input disabled type="radio" class="cmb2-option" name="wcpscu_pro_entrance" id="wcpscu_pro_entrance_slide" value="slide">
							<label for="wcpscu_pro_entrance_slide"><?php esc_html_e( 'Slide In', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
					</ul>
					<p class="cmb2-metabox-description">
						<?php esc_html_e( 'Cards animate in, one after another, as they scroll into view.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?>
					</p>
				</div>
			</div>

			<!--Enable image cropping and resizing-->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label for="wcpscu_img_crop"><?php esc_html_e('Image Resize & Crop', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list cmb2-list cmb2-radio-switch">
						<li><input type="radio" class="cmb2-option cmb2-radio-switch1" name="wcpscu[img_crop]"
								id="wcpscu_img_crop1" value="yes"
								<?php if( empty( $img_crop ) || $img_crop === "yes" ) { echo "checked"; } ?>>
							<label for="wcpscu_img_crop1"><?php esc_html_e('Yes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
						</li>
						<li><input type="radio" class="cmb2-option cmb2-radio-switch2" name="wcpscu[img_crop]"
								id="wcpscu_img_crop2" value="no"
								<?php if( ! empty( $img_crop ) ) { checked( 'no', $img_crop ); } ?>> <label
								for="wcpscu_img_crop2"><?php esc_html_e('No', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label></li>
					</ul>
					<p class="cmb2-metabox-description">
						<?php esc_html_e('If the product images are not in the same size, this feature is helpful. It automatically resizes and crops.', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
					</p>
					<div class="cmb2-metabox-note">
						<p><?php esc_html_e( 'Note: your image must be higher than/equal to the cropping size set below.
							Otherwise, you may need to enable image upscaling feature from the settings below.', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></p>
					</div>
				</div>
			</div>

			<!--Image width-->
			<div class="cmb-row cmb-type-text-medium">
				<div class="cmb-th">
					<label for="wcpscu_crop_image_width"><?php esc_html_e('Image Size', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
				</div>
				<div class="cmb-td">
					<div class="cmb-td-sizes">
						<div class="cmb-td-size cmb-td-size-width">
							<label
								for="wcpscu_crop_image_width"><?php esc_html_e('Width', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
							<input type="text" class="cmb2-text-small" name="wcpscu[crop_image_width]"
								id="wcpscu_crop_image_width"
								value="<?php echo esc_attr( ! empty( $crop_image_width ) ? intval( $crop_image_width ) : 350 ); ?>">
							<p class="cmb2-metabox-description">
								<?php esc_html_e('Image cropping width', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
							</p>
						</div>
						<div class="cmb-td-size cmb-td-size-height">
							<label
								for="wcpscu_crop_image_height"><?php esc_html_e('Height', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?></label>
							<input type="text" class="cmb2-text-small" name="wcpscu[crop_image_height]"
								id="wcpscu_crop_image_height"
								value="<?php echo esc_attr( ! empty( $crop_image_height ) ? intval( $crop_image_height ) : 250 ); ?>">
							<p class="cmb2-metabox-description">
								<?php esc_html_e('Image cropping height', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
							</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Lightbox and product video (Pro): disabled teasers, named outside wcpscu[...] so they are never saved -->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label><?php esc_html_e( 'Lightbox & Video', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list-pro">
						<p><?php esc_html_e( 'Following options available in', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?> <a
								href="https://wpwax.com/product/woocommerce-product-carousel-slider-grid-ultimate-pro"
								target="_blank"><?php esc_html_e( 'Pro Version', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></a>:</p>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_media[]" id="wcpscu_pro_media_lightbox" value="lightbox">
							<label for="wcpscu_pro_media_lightbox"><?php esc_html_e( 'Image Lightbox (the product image and its gallery)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_media[]" id="wcpscu_pro_media_video" value="product_video">
							<label for="wcpscu_pro_media_video"><?php esc_html_e( 'Product Video (YouTube, Vimeo or MP4, on the card or in the lightbox)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
					</ul>
				</div>
			</div>

			</section>

			<section class="wcpscu-section" data-wcpscu-section="urgency">
			<h3 class="wcpscu-section__title"><?php esc_html_e( 'Urgency', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></h3>

			<!-- Sale countdown and stock bar (Pro): disabled teasers, named outside wcpscu[...] so they are never saved -->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label><?php esc_html_e( 'Urgency', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list-pro">
						<p><?php esc_html_e( 'Following options available in', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?> <a
								href="https://wpwax.com/product/woocommerce-product-carousel-slider-grid-ultimate-pro"
								target="_blank"><?php esc_html_e( 'Pro Version', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></a>:</p>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_extras[]" id="wcpscu_pro_extra_countdown" value="sale_countdown">
							<label for="wcpscu_pro_extra_countdown"><?php esc_html_e( 'Sale Countdown ("Ends in 2d 04:12:09" on products with a sale end date)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_extras[]" id="wcpscu_pro_extra_stock" value="stock_bar">
							<label for="wcpscu_pro_extra_stock"><?php esc_html_e( 'Stock Progress Bar ("Only 3 left" with a bar)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
					</ul>
				</div>
			</div>

			</section>

			<section class="wcpscu-section" data-wcpscu-section="rules">
			<h3 class="wcpscu-section__title"><?php esc_html_e( 'Display Rules', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></h3>

			<!-- Display rules (Pro): disabled teasers, named outside wcpscu[...] so they are never saved -->
			<div class="cmb-row cmb-type-radio">
				<div class="cmb-th">
					<label><?php esc_html_e( 'Display Rules', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
				</div>
				<div class="cmb-td">
					<ul class="cmb2-radio-list-pro">
						<p><?php esc_html_e( 'Following options available in', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?> <a
								href="https://wpwax.com/product/woocommerce-product-carousel-slider-grid-ultimate-pro"
								target="_blank"><?php esc_html_e( 'Pro Version', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></a>:</p>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_rules[]" id="wcpscu_pro_rule_schedule" value="schedule">
							<label for="wcpscu_pro_rule_schedule"><?php esc_html_e( 'Schedule (show only between a start and an end date, e.g. for a sale)', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_rules[]" id="wcpscu_pro_rule_devices" value="devices">
							<label for="wcpscu_pro_rule_devices"><?php esc_html_e( 'Hide on Desktop, Tablet or Mobile', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
						<li><input disabled type="checkbox" class="cmb2-option" name="wcpscu_pro_rules[]" id="wcpscu_pro_rule_visitors" value="visitors">
							<label for="wcpscu_pro_rule_visitors"><?php esc_html_e( 'Show only to logged-in users (or chosen roles) or to logged-out visitors', 'woocommerce-product-carousel-slider-and-grid-ultimate' ); ?></label>
						</li>
					</ul>
				</div>
			</div>
			</section>
			</div><!-- end wcpscu-sections -->
		</div>
	</div> <!-- end cmb2-metabox -->
</div> <!-- end cmb2-wrap -->
</div> <!-- end lcsp-tab-1 -->
