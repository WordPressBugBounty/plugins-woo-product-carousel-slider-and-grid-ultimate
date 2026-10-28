<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// A Pro layout saved while Pro was active shows (and renders) as carousel here.
$layout       = ( ! empty( $layout ) && in_array( $layout, array( 'carousel', 'grid', 'list', 'ticker' ), true ) ) ? $layout : 'carousel';
// List uses the Grid tab, ticker the Carousel tab. data-wcpscu-layout hides the rows a layout doesn't use (wcpcsu-admin.css).
$is_grid_tabs = in_array( $layout, array( 'grid', 'list' ), true );
?>
<!--TAB 1  Shortcode -->
<div id="lcsp-tabs-container" data-wcpscu-layout="<?php echo esc_attr( $layout ); ?>" data-wcpscu-effect="<?php echo esc_attr( ( ! empty( $carousel_effect ) && 'fade' === $carousel_effect ) ? 'fade' : 'slide' ); ?>">
    <div class="lcsp-tabs-menu-wrapper">
        <ul class="lcsp-tabs-menu">
            <li class="current">
                <a href="#lcsp-tab-1">
                    <img class="svg_compile" src="<?php echo WCPCSU_URL .'assets/icons/code-solid.svg' ?>" >
                    <?php esc_html_e('Shortcodes', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
                </a>
            </li>
            <li>
                <a href="#lcsp-tab-5">
                    <img class="svg_compile" src="<?php echo WCPCSU_URL .'assets/icons/gear-solid.svg' ?>" >
                    <?php esc_html_e('General Settings', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
                </a>
            </li>
            <li style="display: <?php echo $is_grid_tabs ? 'none' : 'block';?>;" id="tab2">
                <a href="#lcsp-tab-2">
                    <img class="svg_compile" src="<?php echo WCPCSU_URL .'assets/icons/sliders-solid.svg' ?>" >
                    <?php esc_html_e('Carousel Settings', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
                </a>
            </li>
            <li style="display: <?php echo $is_grid_tabs ? 'block' : 'none';?>;" id="tab3">
                <a href="#lcsp-tab-3">
                    <img class="svg_compile" src="<?php echo WCPCSU_URL .'assets/icons/grid.svg' ?>" >
                    <?php esc_html_e('Grid Settings', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
                </a>
            </li>
            <li>
                <a href="#lcsp-tab-4">
                    <img class="svg_compile" src="<?php echo WCPCSU_URL .'assets/icons/palette-solid.svg' ?>" >
                    <?php esc_html_e('Style Settings', 'woocommerce-product-carousel-slider-and-grid-ultimate'); ?>
                </a>
            </li>
        </ul>
        <a href="https://wpwax.com/contact/" class="lcsp-support">
            <img class="svg_compile" src="<?php echo WCPCSU_URL .'assets/icons/circle-support.svg' ?>" >
            support
        </a>
    </div>

    <div class="lcsp-tab">
        <?php
            require_once WCPCSU_INC_DIR . 'settings/shortcode.php';
            require_once WCPCSU_INC_DIR . 'settings/general.php';
            require_once WCPCSU_INC_DIR . 'settings/carousel.php';
            require_once WCPCSU_INC_DIR . 'settings/grid.php';
            require_once WCPCSU_INC_DIR . 'settings/style.php';
        ?>
    </div> <!-- end lcsp-tab -->
</div> <!-- end lcsp-tabs-container -->