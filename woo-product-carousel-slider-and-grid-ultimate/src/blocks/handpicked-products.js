import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { BaseControl, Button, Spinner, TextControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Product IDs from the comma-separated block attribute, in order, without duplicates.
 *
 * @param {string} value e.g. "12,7,30".
 * @return {number[]} IDs.
 */
function parseIds( value ) {
	return String( value || '' )
		.split( ',' )
		.map( ( id ) => parseInt( id, 10 ) )
		.filter( ( id, index, all ) => id > 0 && all.indexOf( id ) === index );
}

/**
 * Handpicked products control: search (WooCommerce Store API), then order with up/down.
 * Stored in the `handpicked_products` attribute as comma-separated IDs, which the
 * shortcode reads as-is.
 *
 * @param {Object}   props
 * @param {string}   props.value    Comma-separated product IDs.
 * @param {Function} props.onChange Receives the new comma-separated IDs.
 */
export default function HandpickedProducts( { value, onChange } ) {
	const ids                         = parseIds( value );
	const [ names, setNames ]         = useState( {} );
	const [ search, setSearch ]       = useState( '' );
	const [ results, setResults ]     = useState( [] );
	const [ searching, setSearching ] = useState( false );

	// Names for IDs we haven't seen yet (saved blocks). Unpublished products aren't in the
	// Store API; they show as "#ID" (the frontend doesn't show them either).
	useEffect( () => {
		const missing = ids.filter( ( id ) => ! names[ id ] );

		if ( ! missing.length ) {
			return;
		}

		apiFetch( { path: addQueryArgs( '/wc/store/v1/products', { include: missing.join( ',' ), per_page: 100 } ) } )
			.then( ( products ) => products )
			.catch( () => [] )
			.then( ( products ) => {
				setNames( ( previous ) => {
					const next = { ...previous };
					products.forEach( ( product ) => {
						next[ product.id ] = decodeEntities( product.name );
					} );
					missing.forEach( ( id ) => {
						next[ id ] = next[ id ] || '#' + id;
					} );
					return next;
				} );
			} );
	}, [ value ] );

	// Debounced search.
	useEffect( () => {
		if ( search.trim().length < 2 ) {
			setResults( [] );
			setSearching( false );
			return;
		}

		let cancelled = false;
		setSearching( true );

		const timer = setTimeout( () => {
			apiFetch( { path: addQueryArgs( '/wc/store/v1/products', { search, per_page: 10 } ) } )
				.catch( () => [] )
				.then( ( products ) => {
					if ( ! cancelled ) {
						setResults( products );
						setSearching( false );
					}
				} );
		}, 300 );

		return () => {
			cancelled = true;
			clearTimeout( timer );
		};
	}, [ search ] );

	const update = ( list ) => onChange( list.join( ',' ) );

	const add = ( product ) => {
		setNames( ( previous ) => ( { ...previous, [ product.id ]: decodeEntities( product.name ) } ) );
		update( [ ...ids, product.id ] );
		setSearch( '' );
	};

	const move = ( index, offset ) => {
		const list = [ ...ids ];
		list.splice( index + offset, 0, list.splice( index, 1 )[ 0 ] );
		update( list );
	};

	const remove = ( index ) => update( ids.filter( ( id, i ) => i !== index ) );

	const available = results.filter( ( product ) => ! ids.includes( product.id ) );

	return (
		<BaseControl
			label={ __( 'Handpicked Products', 'woocommerce-product-carousel-slider-and-ultimate' ) }
			help={ __( 'All picked products are shown, in this order.', 'woocommerce-product-carousel-slider-and-ultimate' ) }
		>
			<TextControl
				value={ search }
				placeholder={ __( 'Search products…', 'woocommerce-product-carousel-slider-and-ultimate' ) }
				onChange={ setSearch }
			/>

			{ searching && <Spinner /> }

			{ available.length > 0 && (
				<ul className="wcpcsu-handpicked-block__results">
					{ available.map( ( product ) => (
						<li key={ product.id }>
							<Button variant="link" onClick={ () => add( product ) }>
								{ decodeEntities( product.name ) }{ product.sku ? ` (${ product.sku })` : '' }
							</Button>
						</li>
					) ) }
				</ul>
			) }

			{ ids.length > 0 && (
				<ul className="wcpcsu-handpicked-block__list">
					{ ids.map( ( id, index ) => (
						<li key={ id } className="wcpcsu-handpicked-block__item">
							<span className="wcpcsu-handpicked-block__name">{ names[ id ] || '…' }</span>
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
								disabled={ index === ids.length - 1 }
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
