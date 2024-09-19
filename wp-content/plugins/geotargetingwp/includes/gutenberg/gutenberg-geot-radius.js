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

registerBlockType('geotargeting-pro/gutenberg-radius', {
	title: __('Radius', 'geot'),
	description: __('You can place other blocks inside this container', 'geot'),
	icon: createElement('img', {width: 20, height: 20, src: gutgeot.icon_radius}),
	category: 'geot-block',
	keywords: [__('inner-blocks'),],

	attributes: {
		radius_mode: {
			type: 'string',
			default: 'include',
		},
		radius_km: {
			type: 'string',
			default: '',
		},
		radius_lat: {
			type: 'string',
			default: '',
		},
		radius_lng: {
			type: 'string',
			default: '',
		}
	},

	edit: function (props) {
		const { attributes, setAttributes, className, focus, setFocus } = props;
		const { radius_mode, radius_km, radius_lat, radius_lng } = attributes;

		const ALLOWED_BLOCKS = [];

		getBlockTypes().forEach(function (blockType) {
			if (gutgeot.modules.indexOf(blockType.name) == -1)
				ALLOWED_BLOCKS.push(blockType.name);
		});

		var block_top_msg = __('You can modify the settings of the block in the sidebar.', 'geot');
		var block_sign_msg = [];

		if( radius_mode ) {
			const label_mode = radius_mode == 'include' ? __('Show', 'geot') : __('Hide', 'geot');
			block_sign_msg.push( __('Visibility', 'geot') + ' : ' + label_mode );
		}

		if( radius_km )
			block_sign_msg.push(__('Radius', 'geot') + ' : ' + radius_km);

		if( radius_lat )
			block_sign_msg.push(__('Latitude', 'geot') + ' : ' + radius_lat);

		if( radius_lng )
			block_sign_msg.push(__('Longitude', 'geot') + ' : ' + radius_lng);

		if (block_sign_msg.length != 0)
			block_top_msg = block_sign_msg.join(' , ');


		return createElement(fragmentElement, {},
			createElement(InspectorControls, {},
				createElement(PanelBody, {title: __('Radius Settings', 'geot')},
					createElement(PanelRow, {},
						createElement(RadioControl, {
							label: __('Visibility', 'geot'),
							options: [
								{ value : 'include', label : 'Show' },
								{ value : 'exclude', label : 'Hide' },
							],
							selected: radius_mode,
							onChange: function( newContent ) {
								setAttributes( { radius_mode: newContent } );
							},
							help: __('Choose visibility', 'geot'),
						}),
					),
					createElement(PanelRow, {},
						createElement(TextControl, {
							label: __('Radius (km)', 'geot'),
							value: radius_km,
							onChange: function( newContent ) {
								setAttributes( { radius_km: newContent } );
							},
							help: __('Type the range.', 'geot')
						}),
					),
					createElement(PanelRow, {},
						createElement(TextControl, {
							label: __('Latitude', 'geot'),
							value: radius_lat,
							onChange: function( newContent ) {
								setAttributes( { radius_lat: newContent } );
							},
							help: __('Type the latitude.', 'geot'),
						}),
					),
					createElement(PanelRow, {},
						createElement(TextControl, {
							label: __('Longitude', 'geot'),
							value: radius_lng,
							onChange: function( newContent ) {
								setAttributes( { radius_lng: newContent } );
							},
							help: __('Type the Longitude.', 'geot'),
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