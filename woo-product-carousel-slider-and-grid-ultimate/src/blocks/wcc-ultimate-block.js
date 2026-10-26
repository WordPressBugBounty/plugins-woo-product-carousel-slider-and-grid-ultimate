import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { Fragment, useState, useEffect, useRef } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';
import { 
    useBlockProps, 
    InspectorControls, 
    BlockControls 
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
	TextControl,
	Toolbar,
	ToolbarButton,
	ColorPicker,
	ColorPalette
} from '@wordpress/components';
import blockAttributes from './attributes.json';
import HandpickedProducts from './handpicked-products';
import TabCategories from './tab-categories';

/**
 * Initialise the server-rendered preview inside `root`.
 *
 * Uses the frontend carousel script (build/carousel.js), which is loaded into the
 * editor iframe, so the preview behaves exactly like the frontend. Its init() is
 * idempotent, so it is safe to call on every preview re-render.
 */
function extra_js( root ) {
	const view = root.ownerDocument.defaultView;
	const api  = view.wpcuCarousel || window.wpcuCarousel;

	if ( api ) {
		api.init( root );
	}
}

registerBlockType( 'wcpcsup/block', {
    apiVersion: 2,

	title: __( 'WooCommerce Product Ultimate', 'woocommerce-product-carousel-slider-and-ultimate' ),

	category: 'widgets',

    keywords: [ 'woocommerce', 'product', 'ultimate', 'wcc' ],

    icon: 'slides',

    supports: {
		html: false
	},

    transforms: {

    },

    attributes: blockAttributes,

    edit({ attributes, setAttributes }){
		
			const previewRef = useRef();

			// Initialise the preview whenever ServerSideRender (re)inserts its markup.
			useEffect( () => {
				const node = previewRef.current;
				if ( ! node ) {
					return;
				}

				extra_js( node );

				const observer = new node.ownerDocument.defaultView.MutationObserver( () => extra_js( node ) );
				observer.observe( node, { childList: true, subtree: true } );

				return () => observer.disconnect();
			}, [] );

        let {
			layout,
			theme,
			h_title_show,
			header_title,
			header_position,
			display_title,
			products_type,
			handpicked_products,
			related_fallback,
			filter_tabs,
			filter_categories,
			filter_all_text,
			total_products,
			display_sale_ribbon,
			sale_ribbon_text,
			sale_ribbon_position,
			display_featured_ribbon,
			feature_ribbon_text,
			featured_ribbon_position,
			display_sold_out_ribbon,
			sold_out_ribbon_text,
			sold_out_ribbon_position,
			display_discount_ribbon,
			discount_ribbon_position,
			display_price,
			display_ratings,
			display_cart,
			A_play,
			repeat_product,
			stop_hover,
			scrool_direction,
			c_desktop,
			c_desktop_small,
			c_tablet,
			c_mobile,
			slide_time,
			slide_speed,
			nav_show,
			nav_position,
			carousel_pagination,
			g_column,
			g_tablet,
			g_mobile,
			grid_pagination,
			grid_pagination_type,
			img_crop,
			crop_image_width,
			crop_image_height
		} = attributes;
		return(
            <Fragment>
                <div { ...useBlockProps() }>
                    <InspectorControls>

                        <PanelBody title={ __( 'Layout', 'woocommerce-product-carousel-slider-and-ultimate' ) } initialOpen={ false }>

							<SelectControl
								label={ __( 'Layout', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								labelPosition='side'
								value={ layout }
								options={ [
									{ label: __( 'Carousel', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'carousel' },
									{ label: __( 'Grid', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'grid' },
									{ label: __( 'List', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'list' },
									{ label: __( 'Ticker', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'ticker' },
									{ label: __( 'Thumbnails (Pro)', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'thumbnails', disabled: true },
									{ label: __( 'Masonry (Pro)', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'masonry', disabled: true },
									{ label: __( 'Tabbed (Pro)', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'tabbed', disabled: true },
								] }
								onChange={ newState => setAttributes( { layout: newState } ) }
							/>

							<SelectControl
								label={ __( 'Theme', 'directorist' ) }
								labelPosition='side'
								value={ theme }
								options={ [
									{ label: __( 'Theme 1', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'theme_1' },
									{ label: __( 'Theme 2', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'theme_2' },
									{ label: __( 'Theme 3', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'theme_3' },
								] }
								onChange={ newState => setAttributes( { theme: newState } ) }
							/>

							<ToggleControl
								label={ __( 'Header Title', 'directorist' ) }
								checked={ h_title_show }
								onChange={ newState => setAttributes( { h_title_show: newState } ) }
							/>

							{ h_title_show ? <TextControl
								label={ __( 'Header Title', 'directorist' ) }
								type='text'
								value={ header_title }
								onChange={ newState => setAttributes( { header_title: newState } ) }
							/> : setAttributes( { header_title: '' } ) }

							{ h_title_show ? <SelectControl
								label={ __( 'Header position', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								labelPosition='side'
								value={ header_position }
								options={ [
									{ label: __( 'Middle', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'middle' },
									{ label: __( 'Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'left' },
									{ label: __( 'Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'right' },
								] }
								onChange={ newState => setAttributes( { header_position: newState } ) }
							/> : setAttributes( { header_position: 'middle' } ) }

							<ToggleControl
								label={ __( 'Display Title', 'directorist' ) }
								checked={ display_title }
								onChange={ newState => setAttributes( { display_title: newState } ) }
							/>
							
                        </PanelBody>

						<PanelBody title={ __( 'Query', 'woocommerce-product-carousel-slider-and-ultimate' ) } initialOpen={ false }>

							<SelectControl
								label={ __( 'Product Type', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								labelPosition='side'
								value={ products_type }
								options={ [
									{ label: __( 'Latest', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'latest' },
									{ label: __( 'Older', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'older' },
									{ label: __( 'Featured', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'featured' },
									{ label: __( 'Handpicked', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'handpicked' },
									{ label: __( 'Related', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'related' },
								] }
								onChange={ newState => setAttributes( { products_type: newState } ) }
							/>

							{ 'handpicked' === products_type && (
								<HandpickedProducts
									value={ handpicked_products }
									onChange={ newState => setAttributes( { handpicked_products: newState } ) }
								/>
							) }

							{ 'related' === products_type && (
								<SelectControl
									label={ __( 'When There Is No Current Product', 'woocommerce-product-carousel-slider-and-ultimate' ) }
									help={ __( 'Related products follow the product being viewed. Other pages (and this preview) show this instead.', 'woocommerce-product-carousel-slider-and-ultimate' ) }
									value={ related_fallback }
									options={ [
										{ label: __( 'Show latest products', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'latest' },
										{ label: __( 'Show nothing', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'none' },
									] }
									onChange={ newState => setAttributes( { related_fallback: newState } ) }
								/>
							) }

							<TextControl
								label={ __( 'Total Products to Display', 'directorist' ) }
								type='text'
								value={ total_products }
								onChange={ newState => setAttributes( { total_products: newState } ) }
							/>

							{ ! [ 'handpicked', 'related' ].includes( products_type ) && (
								<ToggleControl
									label={ __( 'Category Filter Tabs', 'woocommerce-product-carousel-slider-and-ultimate' ) }
									help={ __( 'Shoppers switch between categories above the products, without reloading the page.', 'woocommerce-product-carousel-slider-and-ultimate' ) }
									checked={ filter_tabs }
									onChange={ newState => setAttributes( { filter_tabs: newState } ) }
								/>
							) }

							{ filter_tabs && ! [ 'handpicked', 'related' ].includes( products_type ) && (
								<Fragment>
									<TabCategories
										value={ filter_categories }
										onChange={ newState => setAttributes( { filter_categories: newState } ) }
									/>

									<TextControl
										label={ __( '"All" Tab Label', 'woocommerce-product-carousel-slider-and-ultimate' ) }
										help={ __( 'The first tab, active when the page loads. It shows the products of the Product Type above (up to "Total Products to Display"), from every category.', 'woocommerce-product-carousel-slider-and-ultimate' ) }
										placeholder={ __( 'All', 'woocommerce-product-carousel-slider-and-ultimate' ) }
										value={ filter_all_text }
										onChange={ newState => setAttributes( { filter_all_text: newState } ) }
									/>
								</Fragment>
							) }

						</PanelBody>

						<PanelBody title={ __( 'Elements', 'woocommerce-product-carousel-slider-and-ultimate' ) } initialOpen={ false }>

							<ToggleControl
								label={ __( 'Display "Sale" Badge', 'directorist' ) }
								checked={ display_sale_ribbon }
								onChange={ newState => setAttributes( { display_sale_ribbon: newState } ) }
							/>

							<TextControl
								label={ __( 'Sale Ribbon Text', 'directorist' ) }
								type='text'
								value={ sale_ribbon_text }
								onChange={ newState => setAttributes( { sale_ribbon_text: newState } ) }
							/>

							<SelectControl
								label={ __( 'Sale Ribbon Position', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								labelPosition='side'
								value={ sale_ribbon_position }
								options={ [
									{ label: __( 'Top Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'top_left' },
									{ label: __( 'Top Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'top_right' },
									{ label: __( 'Bottom Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'bottom_left' },
									{ label: __( 'Bottom Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'bottom_right' },
								] }
								onChange={ newState => setAttributes( { sale_ribbon_position: newState } ) }
							/>

							<ToggleControl
								label={ __( 'Display "Featured" Badge', 'directorist' ) }
								checked={ display_featured_ribbon }
								onChange={ newState => setAttributes( { display_featured_ribbon: newState } ) }
							/>

							<TextControl
								label={ __( 'Featured Ribbon Text', 'directorist' ) }
								type='text'
								value={ feature_ribbon_text }
								onChange={ newState => setAttributes( { feature_ribbon_text: newState } ) }
							/>

							<SelectControl
								label={ __( 'Featured Ribbon Position', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								labelPosition='side'
								value={ featured_ribbon_position }
								options={ [
									{ label: __( 'Top Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'top_left' },
									{ label: __( 'Top Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'top_right' },
									{ label: __( 'Bottom Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'bottom_left' },
									{ label: __( 'Bottom Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'bottom_right' },
								] }
								onChange={ newState => setAttributes( { featured_ribbon_position: newState } ) }
							/>

							<ToggleControl
								label={ __( 'Display "Sold Out" Badge', 'directorist' ) }
								checked={ display_sold_out_ribbon }
								onChange={ newState => setAttributes( { display_sold_out_ribbon: newState } ) }
							/>

							<TextControl
								label={ __( 'Sold Out Ribbon Text', 'directorist' ) }
								type='text'
								value={ sold_out_ribbon_text }
								onChange={ newState => setAttributes( { sold_out_ribbon_text: newState } ) }
							/>

							<SelectControl
								label={ __( 'Sold Out Ribbon Position', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								labelPosition='side'
								value={ sold_out_ribbon_position }
								options={ [
									{ label: __( 'Top Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'top_left' },
									{ label: __( 'Top Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'top_right' },
									{ label: __( 'Bottom Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'bottom_left' },
									{ label: __( 'Bottom Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'bottom_right' },
								] }
								onChange={ newState => setAttributes( { sold_out_ribbon_position: newState } ) }
							/>

							<ToggleControl
								label={ __( 'Display "Discount Percentage" Badge', 'directorist' ) }
								checked={ display_discount_ribbon }
								onChange={ newState => setAttributes( { display_discount_ribbon: newState } ) }
							/>

							<SelectControl
								label={ __( 'Discount Ribbon Position', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								labelPosition='side'
								value={ discount_ribbon_position }
								options={ [
									{ label: __( 'Top Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'top_left' },
									{ label: __( 'Top Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'top_right' },
									{ label: __( 'Bottom Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'bottom_left' },
									{ label: __( 'Bottom Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'bottom_right' },
								] }
								onChange={ newState => setAttributes( { discount_ribbon_position: newState } ) }
							/>

							<ToggleControl
								label={ __( 'Display Product Price', 'directorist' ) }
								checked={ display_price }
								onChange={ newState => setAttributes( { display_price: newState } ) }
							/>

							<ToggleControl
								label={ __( 'Display Product Ratings', 'directorist' ) }
								checked={ display_ratings }
								onChange={ newState => setAttributes( { display_ratings: newState } ) }
							/>

							<ToggleControl
								label={ __( 'Display "Add to Cart" Button', 'directorist' ) }
								checked={ display_cart }
								onChange={ newState => setAttributes( { display_cart: newState } ) }
							/>

						</PanelBody>

						{ layout == 'carousel' || layout == 'ticker' ? <PanelBody title={ layout == 'ticker' ? __( 'Ticker', 'woocommerce-product-carousel-slider-and-ultimate' ) : __( 'Carousel', 'woocommerce-product-carousel-slider-and-ultimate' ) } initialOpen={ false }>

							{ layout == 'carousel' ? <ToggleControl
								label={ __( 'Auto Play', 'directorist' ) }
								checked={ A_play }
								onChange={ newState => setAttributes( { A_play: newState } ) }
							/> : '' }

							{ layout == 'carousel' ? <ToggleControl
								label={ __( 'Repeat Product', 'directorist' ) }
								checked={ repeat_product }
								onChange={ newState => setAttributes( { repeat_product: newState } ) }
							/> : '' }

							{ layout == 'ticker' ? <ToggleControl
								label={ __( 'Stop on Hover', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								checked={ stop_hover }
								onChange={ newState => setAttributes( { stop_hover: newState } ) }
							/> : '' }

							{ layout == 'ticker' ? <SelectControl
								label={ __( 'Scroll Direction', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								labelPosition='side'
								value={ scrool_direction }
								options={ [
									{ label: __( 'Right to Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'right_left' },
									{ label: __( 'Left to Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'left_right' },
								] }
								onChange={ newState => setAttributes( { scrool_direction: newState } ) }
							/> : '' }

							<TextControl
								label={ __( 'Products Columns', 'directorist' ) }
								type='text'
								value={ c_desktop }
								onChange={ newState => setAttributes( { c_desktop: newState } ) }
							/>

							<TextControl
								label={ __( 'Laptop Columns', 'directorist' ) }
								type='text'
								value={ c_desktop_small }
								onChange={ newState => setAttributes( { c_desktop_small: newState } ) }
							/>

							<TextControl
								label={ __( 'Tablet Columns', 'directorist' ) }
								type='text'
								value={ c_tablet }
								onChange={ newState => setAttributes( { c_tablet: newState } ) }
							/>

							<TextControl
								label={ __( 'Mobile Columns', 'directorist' ) }
								type='text'
								value={ c_mobile }
								onChange={ newState => setAttributes( { c_mobile: newState } ) }
							/>

							<TextControl
								label={ __( 'Slide Speed', 'directorist' ) }
								type='text'
								value={ slide_speed }
								help={ layout == 'ticker' ? __( 'Time one product takes to scroll past (1000 = 1 second). Higher is slower.', 'woocommerce-product-carousel-slider-and-ultimate' ) : undefined }
								onChange={ newState => setAttributes( { slide_speed: newState } ) }
							/>

							{ layout == 'carousel' ? <TextControl
								label={ __( 'Slide Timeout', 'directorist' ) }
								type='text'
								value={ slide_time }
								onChange={ newState => setAttributes( { slide_time: newState } ) }
							/> : '' }

							{ layout == 'carousel' ? <ToggleControl
								label={ __( 'Enable Navigation', 'directorist' ) }
								checked={ nav_show }
								onChange={ newState => setAttributes( { nav_show: newState } ) }
							/> : '' }

							{ layout == 'carousel' ? <SelectControl
								label={ __( 'Position', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								labelPosition='side'
								value={ nav_position }
								options={ [
									{ label: __( 'Top Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'top-left' },
									{ label: __( 'Top Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'top-right' },
									{ label: __( 'Middle', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'middle' },
									{ label: __( 'Bottom Left', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'bottom-left' },
									{ label: __( 'Bottom Right', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'bottom-right' }

								] }
								onChange={ newState => setAttributes( { nav_position: newState } ) }
							/> : '' }

							{ layout == 'carousel' ? <ToggleControl
								label={ __( 'Enable Pagination', 'directorist' ) }
								checked={ carousel_pagination }
								onChange={ newState => setAttributes( { carousel_pagination: newState } ) }
							/> : '' }
							
						</PanelBody> : '' }

						{ layout == 'grid' || layout == 'list' ? <PanelBody title={ layout == 'list' ? __( 'List', 'woocommerce-product-carousel-slider-and-ultimate' ) : __( 'Grid', 'woocommerce-product-carousel-slider-and-ultimate' ) } initialOpen={ false }>

							{ layout == 'grid' ? <TextControl
								label={ __( 'Grid Columns', 'directorist' ) }
								type='text'
								value={ g_column }
								onChange={ newState => setAttributes( { g_column: newState } ) }
							/> : '' }

							{ layout == 'grid' ? <TextControl
								label={ __( 'Select Columns for Tablet', 'directorist' ) }
								type='text'
								value={ g_tablet }
								onChange={ newState => setAttributes( { g_tablet: newState } ) }
							/> : '' }

							{ layout == 'grid' ? <TextControl
								label={ __( 'Select Columns for Mobile', 'directorist' ) }
								type='text'
								value={ g_mobile }
								onChange={ newState => setAttributes( { g_mobile: newState } ) }
							/> : '' }

							<ToggleControl
								label={ __( 'Display Pagination', 'directorist' ) }
								checked={ grid_pagination }
								onChange={ newState => setAttributes( { grid_pagination: newState } ) }
							/>

							{ grid_pagination ? <SelectControl
								label={ __( 'Pagination Type', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								labelPosition='side'
								value={ grid_pagination_type }
								options={ [
									{ label: __( 'Normal (page reload)', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'normal' },
									{ label: __( 'Ajax Numbers (no page reload)', 'woocommerce-product-carousel-slider-and-ultimate' ), value: 'ajax_numbers' },
								] }
								onChange={ newState => setAttributes( { grid_pagination_type: newState } ) }
							/> : '' }

						</PanelBody> : '' }

						<PanelBody title={ __( 'Image', 'woocommerce-product-carousel-slider-and-ultimate' ) } initialOpen={ false }>

							<ToggleControl
								label={ __( 'Image Resize & Crop', 'directorist' ) }
								checked={ img_crop }
								onChange={ newState => setAttributes( { img_crop: newState } ) }
							/>

							<TextControl
								label={ __( 'Cropping Width', 'directorist' ) }
								type='text'
								value={ crop_image_width }
								onChange={ newState => setAttributes( { crop_image_width: newState } ) }
							/>

							<TextControl
								label={ __( 'Cropping Height', 'directorist' ) }
								type='text'
								value={ crop_image_height }
								onChange={ newState => setAttributes( { crop_image_height: newState } ) }
							/>

						</PanelBody>

                    </InspectorControls>
                
			    	<div ref={ previewRef }>
			    		<ServerSideRender block="wcpcsup/block" attributes={ attributes } />
			    	</div>
                 </div>
            </Fragment>
		)
	}
} );

