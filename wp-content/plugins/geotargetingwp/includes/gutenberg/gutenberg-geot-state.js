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


registerBlockType('geotargeting-pro/gutenberg-state', {
	title: __('Target States', 'geot'),
	description: __('You can place other blocks inside this container', 'geot'),
	icon: createElement('img', {width: 20, height: 20, src: gutgeot.icon_state}),
	category: 'geot-block',
	keywords: [__('inner-blocks'),],

	attributes: {
		states_mode: {
			type: 'string',
			default: 'include',
		},
		states_input: {
			type: 'string',
			default: '',
		},
		states_region: {
			type: 'array',
			default: [],
		},
	},

	edit: function (props) {
		const { attributes, setAttributes, className, focus, setFocus } = props;
		const { states_mode, states_input, states_region } = attributes;

		const ALLOWED_BLOCKS = [];

		getBlockTypes().forEach( function( blockType ) {
			if (gutgeot.modules.indexOf(blockType.name) == -1)
				ALLOWED_BLOCKS.push(blockType.name);
		});

		var block_top_msg = __('You can modify the settings of the block in the sidebar.', 'geot');
		var block_sign_msg = [];

		if( states_mode ) {
			const label_mode = states_mode == 'include' ? __('Show', 'geot') : __('Hide', 'geot');
			block_sign_msg.push( __('Visibility', 'geot') + ' : ' + label_mode );
		}

		if( states_input )
			block_sign_msg.push( __('States', 'geot') + ' : ' + states_input );

		if( states_region.length )
			block_sign_msg.push( __('Regions', 'geot') + ' : ' + states_region.join(' , ') );

		if (block_sign_msg.length != 0)
			block_top_msg = block_sign_msg.join(' , ');


		return createElement(fragmentElement, {},
			createElement(InspectorControls, {},
				createElement(PanelBody, {title: __('Target States Settings', 'geot')},
					createElement(PanelRow, {},
						createElement(RadioControl, {
							label: __('Visibility', 'geot'),
							options: [
								{ value : 'include', label : 'Show' },
								{ value : 'exclude', label : 'Hide' },
							],
							selected: states_mode,
							onChange: function( newContent ) {
								setAttributes( { states_mode: newContent } );
							},
							help: __('Choose visibility', 'geot'),
						}),
					),
					createElement(PanelRow, {},
						createElement(TextControl, {
							label: __('States', 'geot'),
							value: states_input,
							onChange: function( newContent ) {
								setAttributes( { states_input: newContent } );
							},
							help: __('Type state names or ISO codes separated by comma.', 'geot')
						}),
					),
					createElement(PanelRow, {},
						createElement(SelectControl, {
							label: __('Regions', 'geot'),
							multiple: true,
							options: gutgeot.regions_state,
							onChange: function( newContent ) {
								setAttributes( { states_region: newContent } );
							},
							value: states_region,
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