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


registerBlockType('geotargeting-pro/gutenberg-all', {
	title: __('Target GeotWP', 'geot'),
	description: __('You can place other blocks inside this container', 'geot'),
	icon: createElement('img', {width: 20, height: 20, src: gutgeot.icon_all}),
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
		cities_mode: {
			type: 'string',
			default: 'include',
		},
		cities_input: {
			type: 'string',
			default: '',
		},
		cities_region: {
			type: 'array',
			default: [],
		},
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
		const {attributes, setAttributes, className, focus, setFocus} = props;
		const {countries_mode, countries_input, countries_region, cities_mode, cities_input, cities_region, states_mode, states_input, states_region, zipcodes_mode, zipcodes_input, zipcodes_region, radius_mode, radius_km, radius_lat, radius_lng } = attributes;

		const ALLOWED_BLOCKS = [];

		getBlockTypes().forEach(function (blockType) {
			if (gutgeot.modules.indexOf(blockType.name) == -1)
				ALLOWED_BLOCKS.push(blockType.name);
		});

		var block_top_msg = __('You can modify the settings of the block in the sidebar.', 'geot');
		var block_sign_msg = [];


		// Countries
		if( countries_input || countries_region.length ) {
			const label_mode = countries_mode == 'include' ? __('Show', 'geot') : __('Hide', 'geot');
			block_sign_msg.push( __('Visibility for Countries', 'geot') + ' : ' + label_mode );
		}

		if( countries_input )
			block_sign_msg.push( __('Countries', 'geot') + ' : ' + countries_input );

		if( countries_region.length )
			block_sign_msg.push( __('Country Regions', 'geot') + ' : ' + countries_region.join(' , ') );


		// Cities
		if( cities_input || cities_region.length ) {
			const label_mode = cities_mode == 'include' ? __('Show', 'geot') : __('Hide', 'geot');
			block_sign_msg.push( __('Visibility for Cities', 'geot') + ' : ' + label_mode );
		}

		if( cities_input )
			block_sign_msg.push( __('Cities', 'geot') + ' : ' + cities_input );

		if( cities_region.length )
			block_sign_msg.push( __('Cities Regions', 'geot') + ' : ' + cities_region.join(' , ') );


		// States
		if( states_input || states_region.length ) {
			const label_mode = states_mode == 'include' ? __('Show', 'geot') : __('Hide', 'geot');
			block_sign_msg.push( __('Visibility for States', 'geot') + ' : ' + label_mode );
		}

		if( states_input )
			block_sign_msg.push( __('States', 'geot') + ' : ' + states_input );

		if( states_region.length )
			block_sign_msg.push( __('States Regions', 'geot') + ' : ' + states_region.join(' , ') );


		// zipcodes
		if( zipcodes_input || zipcodes_region.length ) {
			const label_mode = zipcodes_mode == 'include' ? __('Show', 'geot') : __('Hide', 'geot');
			block_sign_msg.push( __('Visibility for Zipcodes', 'geot') + ' : ' + label_mode );
		}

		if( zipcodes_input )
			block_sign_msg.push( __('Zipcodes', 'geot') + ' : ' + zipcodes_input );

		if( zipcodes_region.length )
			block_sign_msg.push( __('Zipcodes Regions', 'geot') + ' : ' + zipcodes_region.join(' , ') );


		// Radius
		if( radius_km || radius_lat || radius_lng || radius_lng ) {
			const label_mode = radius_mode == 'include' ? __('Show', 'geot') : __('Hide', 'geot');
			block_sign_msg.push( __('Visibility for Radius', 'geot') + ' : ' + label_mode );
		}

		if( radius_km )
			block_sign_msg.push(__('Radius', 'geot') + ' : ' + radius_km);

		if( radius_lat )
			block_sign_msg.push(__('Latitude', 'geot') + ' : ' + radius_lat);

		if( radius_lng )
			block_sign_msg.push(__('Longitude', 'geot') + ' : ' + radius_lng);

		// All text
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
				createElement(PanelBody, {title: __('Target Cities Settings', 'geot')},
					createElement(PanelRow, {},
						createElement(RadioControl, {
							label: __('Visibility', 'geot'),
							options: [
								{ value : 'include', label : 'Show' },
								{ value : 'exclude', label : 'Hide' },
							],
							selected: cities_mode,
							onChange: function( newContent ) {
								setAttributes( { cities_mode: newContent } );
							},
							help: __('Choose visibility', 'geot'),
						}),
					),
					createElement(PanelRow, {},
						createElement(TextControl, {
							label: __('Cities', 'geot'),
							value: cities_input,
							onChange: function( newContent ) {
								setAttributes( { cities_input: newContent } );
							},
							help: __('Type city names separated by comma.', 'geot')
						}),
					),
					createElement(PanelRow, {},
						createElement(SelectControl, {
							label: __('Regions', 'geot'),
							multiple: true,
							options: gutgeot.regions_city,
							onChange: function( newContent ) {
								setAttributes( { cities_region: newContent } );
							},
							value: cities_region,
							help: __('Choose region name to show content to', 'geot'),
							className: 'region-multiple',
						} ),
					),
				),
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
				createElement(PanelBody, {title: __('Target Radius Settings', 'geot')},
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