/**
 * Extensões do editor para o bloco core/query.
 *
 * Fonte ESNext compilada via `npm run build` (wp-scripts).
 * O arquivo em build/index.js é a versão de produção compatível com wp.* globals.
 *
 * @package UniqueQueryLoopExtension
 */

import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ATTRIBUTE_NAME = 'uniqueOnPage';

addFilter(
	'blocks.registerBlockType',
	'unique-query-loop-extension/add-attribute',
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
							'Posts únicos na página',
							'unique-query-loop-extension'
						) }
						initialOpen={ false }
					>
						<ToggleControl
							label={ __(
								'Tornar posts únicos na página',
								'unique-query-loop-extension'
							) }
							help={ __(
								'Evita que posts já exibidos em outros Query Loops da mesma página apareçam novamente.',
								'unique-query-loop-extension'
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
	'unique-query-loop-extension/with-control',
	withUniqueOnPageControl
);
