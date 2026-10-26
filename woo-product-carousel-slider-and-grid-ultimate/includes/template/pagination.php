<?php
if( $is_grid ) {
    $pagination_style = "
    --wpcu-pagColor: " . esc_attr( $pagi_color ) . ";
    --wpcu-pagColorHover: " . esc_attr( $pagi_hover_color ) . ";
    --wpcu-pagBorderColor: " . esc_attr( $pagi_border_color ) . ";
    --wpcu-pagBorderColorHover: " . esc_attr( $pagi_hover_border_color ) . ";
    --wpcu-pagBgColor: " . esc_attr( $pagi_back_color ) . ";
    --wpcu-pagBgColorHover: " . esc_attr( $pagi_hover_back_color ) . ";
    --wpcu-pagActiveColor: " . esc_attr( $pagi_active_color ) . ";
    --wpcu-pagActiveBorderColor: " . esc_attr( $pagi_active_border_color ) . ";
    --wpcu-pagActiveBgColor: " . esc_attr( $pagi_active_back_color ) . ";
    ";

    if ( 'ajax_numbers' === $grid_pagination_type ) {
        $pagination_sign   = WCPCSU_Products_Route::sign( $atts );
        $pagination_config = array(
            'url'       => rest_url( WCPCSU_Products_Route::REST_NAMESPACE . '/products' ),
            'q'         => $pagination_sign['q'],
            'sig'       => $pagination_sign['sig'],
            'category'  => '',
            'page'      => max( 1, (int) $paged ),
            'max_pages' => (int) $loop->max_num_pages,
        );
        $pagination_base   = str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) );
        ?>
<!-- .wpcu-pagination--ajax -->
<div class="wpcu-pagination wpcu-pagination--ajax" style="<?php echo esc_attr( $pagination_style ); ?>"
    data-wpcu-pagination="<?php echo esc_attr( wp_json_encode( $pagination_config ) ); ?>"
    <?php echo ( (int) $loop->max_num_pages <= 1 ) ? 'hidden' : ''; ?>>
    <?php echo WCPCSU_Products_Route::pagination_html( max( 1, (int) $paged ), (int) $loop->max_num_pages, $pagination_base ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
</div>
    <?php } else { ?>
<!-- .wpcu-pagination--left/.wpcu-pagination--right -->
<div class="wpcu-pagination" style="<?php echo esc_attr( $pagination_style ); ?>">
    <?php
    echo wcpcsu_pagination( $loop, $paged );
    ?>
</div>
    <?php }
} else { ?>
<!-- wpcu-carousel-pagination -->
<div class="wpcu-carousel-pagination" style="
    --wpcu-dotsColor: <?php echo esc_attr( $dots_color ); ?>;
    --wpcu-dotsActiveColor: <?php echo esc_attr( $dots_active_color ); ?>;
"></div>
<?php } ?>
