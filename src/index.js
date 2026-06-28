/**
 * Extensões do editor para o bloco core/query.
 *
 * Fonte ESNext compilada via `npm run build` (wp-scripts).
 * O arquivo em build/index.js é a versão de produção compatível com wp.* globals.
 *
 * @package Sidpeql
 */

import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ATTRIBUTE_NAME = 'uniqueOnPage';

addFilter(
	'blocks.registerBlockType',
	'silvaitamar-duplicate-post-exclusion-query-loop/add-attribute',
	( settings, name ) => {
		if ( name !== 'core/query' ) {
			return settings;
		}

		return {
			...settings,
			attributes: {
				...settings.attributes,
				[ ATTRIBUTE_NAME ]: {
					type: 'boolean',
					default: false,
				},
			},
		};
	}
);

const withUniqueOnPageControl = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		if ( props.name !== 'core/query' ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;

		return (
			<>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody
						title={ __(
							'Unique posts on page',
							'silvaitamar-duplicate-post-exclusion-query-loop'
						) }
						initialOpen={ false }
					>
						<ToggleControl
							label={ __(
								'Make posts unique on page',
								'silvaitamar-duplicate-post-exclusion-query-loop'
							) }
							help={ __(
								'Prevents posts already shown in other Query Loops on the same page from appearing again.',
								'silvaitamar-duplicate-post-exclusion-query-loop'
							) }
							checked={ !! attributes[ ATTRIBUTE_NAME ] }
							onChange={ ( value ) =>
								setAttributes( { [ ATTRIBUTE_NAME ]: value } )
							}
						/>
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}, 'withUniqueOnPageControl' );

addFilter(
	'editor.BlockEdit',
	'silvaitamar-duplicate-post-exclusion-query-loop/with-control',
	withUniqueOnPageControl
);
