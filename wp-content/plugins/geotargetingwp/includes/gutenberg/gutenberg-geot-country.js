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

registerBlockType('geotargeting-pro/gutenberg-country', {
	title: __('Target Countries', 'geot'),
	description: __('You can place other blocks inside this container', 'geot'),
	icon: createElement('img', {width: 20, height: 20, src: gutgeot.icon_country}),
	category: 'geot-block',
	keywords: [__('inner-blocks'),],

	attributes: {
		countries_mode: {
			type: 'string',
			default: 'include',
		},
		countries_input: {
			type: 'string',
			default: '',
		},
		countries_region: {
			type: 'array',
			default: [],
		},
	},

	edit: function (props) {
		const {attributes, setAttributes, className, focus, setFocus} = props;
		const {countries_mode, countries_input, countries_region } = attributes;

		const ALLOWED_BLOCKS = [];

		getBlockTypes().forEach(function (blockType) {
			if (gutgeot.modules.indexOf(blockType.name) == -1)
				ALLOWED_BLOCKS.push(blockType.name);
		});

		var block_top_msg = __('You can modify the settings of the block in the sidebar.', 'geot');
		var block_sign_msg = [];


		if( countries_mode ) {
			const label_mode = countries_mode == 'include' ? __('Show', 'geot') : __('Hide', 'geot');
			block_sign_msg.push( __('Visibility', 'geot') + ' : ' + label_mode );
		}

		if( countries_input )
			block_sign_msg.push( __('Countries', 'geot') + ' : ' + countries_input );

		if( countries_region.length )
			block_sign_msg.push( __('Regions', 'geot') + ' : ' + countries_region.join(' , ') );

		if( block_sign_msg.length != 0 )
			block_top_msg = block_sign_msg.join(' , ');

		return createElement(fragmentElement, {},
			createElement(InspectorControls, {},
				createElement(PanelBody, {title: __('Target Countries Settings', 'geot')},
					createElement(PanelRow, {},
						createElement(RadioControl, {
							label: __('Visibility', 'geot'),
							options: [
								{ value : 'include', label : __('Show', 'geot') },
								{ value : 'exclude', label : __('Hide', 'geot') },
							],
							selected: countries_mode,
							onChange: function( newContent ) {
								setAttributes( { countries_mode: newContent } );
							},
							help: __('Choose visibility', 'geot'),
						}),
					),
					createElement(PanelRow, {},
						createElement(TextControl, {
							label: __('Countries', 'geot'),
							value: countries_input,
							onChange: function( newContent ) {
								setAttributes( { countries_input: newContent } );
							},
							help: __('Type country names or ISO codes separated by comma.', 'geot')
						}),
					),
					createElement(PanelRow, {},
						createElement(SelectControl, {
							label: __('Regions', 'geot'),
							multiple: true,
							options: gutgeot.regions_country,
							onChange: function( newContent ) {
								setAttributes( { countries_region: newContent } );
							},
							value: countries_region,
							help: __('Choose region name to show content to', 'geot'),
							className: 'region-multiple',
						}),
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