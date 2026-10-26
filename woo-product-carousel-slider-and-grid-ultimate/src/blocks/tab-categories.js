import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { BaseControl, Button, SelectControl, Spinner } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Category slugs from the comma-separated block attribute, in order, without duplicates.
 *
 * @param {string} value e.g. "bags,footwear".
 * @return {string[]} Slugs.
 */
function parseSlugs( value ) {
	return String( value || '' )
		.split( ',' )
		.map( ( slug ) => slug.trim() )
		.filter( ( slug, index, all ) => slug && all.indexOf( slug ) === index );
}

/**
 * Categories in tree order, each with its depth (like the settings page's category select).
 *
 * @param {Object[]} terms Store API categories (id, name, slug, parent).
 * @return {Object[]} { slug, name, depth }.
 */
function treeOrder( terms ) {
	const out  = [];
	const walk = ( parent, depth ) => {
		terms
			.filter( ( term ) => term.parent === parent )
			.forEach( ( term ) => {
				out.push( { slug: term.slug, name: decodeEntities( term.name ), depth } );
				walk( term.id, depth + 1 );
			} );
	};
	walk( 0, 0 );

	return out;
}

/**
 * Tab categories control: pick categories (WooCommerce Store API), then order with up/down.
 * Stored in the `filter_categories` attribute as comma-separated slugs, which the
 * shortcode reads as-is.
 *
 * @param {Object}   props
 * @param {string}   props.value    Comma-separated category slugs.
 * @param {Function} props.onChange Receives the new comma-separated slugs.
 */
export default function TabCategories( { value, onChange } ) {
	const slugs               = parseSlugs( value );
	const [ terms, setTerms ] = useState( null );

	useEffect( () => {
		apiFetch( { path: '/wc/store/v1/products/categories?per_page=100' } )
			.catch( () => [] )
			.then( ( result ) => setTerms( treeOrder( Array.isArray( result ) ? result : [] ) ) );
	}, [] );

	const nameOf = ( slug ) => {
		const term = ( terms || [] ).find( ( item ) => item.slug === slug );
		return term ? term.name : slug; // a deleted category (the frontend skips it)
	};

	const update = ( list ) => onChange( list.join( ',' ) );

	const move = ( index, offset ) => {
		const list = [ ...slugs ];
		list.splice( index + offset, 0, list.splice( index, 1 )[ 0 ] );
		update( list );
	};

	const remove = ( index ) => update( slugs.filter( ( slug, i ) => i !== index ) );

	const options = [
		{ label: __( 'Add a category…', 'woocommerce-product-carousel-slider-and-ultimate' ), value: '' },
		...( terms || [] ).map( ( term ) => ( {
			label: '— '.repeat( term.depth ) + term.name,
			value: term.slug,
			disabled: slugs.includes( term.slug ),
		} ) ),
	];

	return (
		<BaseControl
			label={ __( 'Tab Categories', 'woocommerce-product-carousel-slider-and-ultimate' ) }
			help={ __( 'Each category becomes a tab, in this order. A tab shows the products of the Product Type above that are in that category or its subcategories.', 'woocommerce-product-carousel-slider-and-ultimate' ) }
		>
			{ null === terms ? (
				<Spinner />
			) : (
				<SelectControl
					value=""
					options={ options }
					onChange={ ( slug ) => slug && update( [ ...slugs, slug ] ) }
				/>
			) }

			{ slugs.length > 0 && (
				<ul className="wcpcsu-handpicked-block__list">
					{ slugs.map( ( slug, index ) => (
						<li key={ slug } className="wcpcsu-handpicked-block__item">
							<span className="wcpcsu-handpicked-block__name">{ terms ? nameOf( slug ) : '…' }</span>
							<Button
								size="small"
								icon="arrow-up-alt2"
								label={ __( 'Move up', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								disabled={ index === 0 }
								onClick={ () => move( index, -1 ) }
							/>
							<Button
								size="small"
								icon="arrow-down-alt2"
								label={ __( 'Move down', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								disabled={ index === slugs.length - 1 }
								onClick={ () => move( index, 1 ) }
							/>
							<Button
								size="small"
								icon="no-alt"
								label={ __( 'Remove', 'woocommerce-product-carousel-slider-and-ultimate' ) }
								onClick={ () => remove( index ) }
							/>
						</li>
					) ) }
				</ul>
			) }
		</BaseControl>
	);
}
