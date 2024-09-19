/**
 * Register: Geotargenting Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */

registerBlockType('geotargeting-pro/gutenberg-zipcode', {
	title: __('Target Zipcodes', 'geot'),
	description: __('You can place other blocks inside this container', 'geot'),
	icon: createElement('img', {width: 20, height: 20, src: gutgeot.icon_zipcode}),
	category: 'geot-block',
	keywords: [__('inner-blocks'),],

	attributes: {
		zipcodes_mode: {
			type: 'string',
			default: 'include',
		},
		zipcodes_input: {
			type: 'string',
			default: '',
		},
		zipcodes_region: {
			type: 'array',
			default: [],
		},
	},

	edit: function (props) {
		const { attributes, setAttributes, className, focus, setFocus } = props;
		const { zipcodes_mode, zipcodes_input, zipcodes_region } = attributes;

		const ALLOWED_BLOCKS = [];

		getBlockTypes().forEach(function (blockType) {
			if (gutgeot.modules.indexOf(blockType.name) == -1)
				ALLOWED_BLOCKS.push(blockType.name);
		});

		var block_top_msg = __('You can modify the settings of the block in the sidebar.', 'geot');
		var block_sign_msg = [];

		if( zipcodes_mode ) {
			const label_mode = zipcodes_mode == 'include' ? __('Show', 'geot') : __('Hide', 'geot');
			block_sign_msg.push( __('Visibility', 'geot') + ' : ' + label_mode );
		}

		if( zipcodes_input )
			block_sign_msg.push( __('Zipcodes', 'geot') + ' : ' + zipcodes_input );

		if( zipcodes_region.length )
			block_sign_msg.push( __('Regions', 'geot') + ' : ' + zipcodes_region.join(' , ') );

		if (block_sign_msg.length != 0)
			block_top_msg = block_sign_msg.join(' , ');


		return createElement(fragmentElement, {},
			createElement(InspectorControls, {},
				createElement(PanelBody, {title: __('Target Zipcodes Settings', 'geot')},
					createElement(PanelRow, {},
						createElement(RadioControl, {
							label: __('Visibility', 'geot'),
							options: [
								{ value : 'include', label : 'Show' },
								{ value : 'exclude', label : 'Hide' },
							],
							selected: zipcodes_mode,
							onChange: function( newContent ) {
								setAttributes( { zipcodes_mode: newContent } );
							},
							help: __('Choose visibility', 'geot'),
						}),
					),
					createElement(PanelRow, {},
						createElement(TextControl, {
							label: __('Zipcodes', 'geot'),
							value: zipcodes_input,
							onChange: function( newContent ) {
								setAttributes( { zipcodes_input: newContent } );
							},
							help: __('Type zip codes separated by commas.', 'geot')
						}),
					),
					createElement(PanelRow, {},
						createElement(SelectControl, {
							label: __('Regions', 'geot'),
							multiple: true,
							options: gutgeot.regions_zip,
							onChange: function( newContent ) {
								setAttributes( { zipcodes_region: newContent } );
							},
							value: zipcodes_region,
							help: __('Choose region name to show content to', 'geot'),
							className: 'region-multiple',
						} ),
					),
				),
			),
			createElement('div', {className: className},
				createElement('div', {}, block_top_msg),
				createElement(InnerBlocks, {allowedBlocks: ALLOWED_BLOCKS})
			)
		);
	},
	save: function () {
		return createElement('div', {}, createElement(InnerBlocks.Content));
	}
});