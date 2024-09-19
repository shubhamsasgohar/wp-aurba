/**
 * Initalize Attributes
 * @param  object settings
 * @param  string name
 * @return void
 */
const createAttributes = ( settings, name ) => {

	if ( ! geotnav.allowed_blocks.includes( name ) ) {
		return settings;
	}

	settings.attributes = {

		includeMode: {
			type    : 'string',
			default : 'include',
		},
		countriesInput: {
			type    : 'string',
			default : '',
		},
		countriesRegion: {
			type    : 'array',
			default : [],
		},
		citiesInput: {
			type    : 'string',
			default : '',
		},
		citiesRegion: {
			type    : 'array',
			default : [],
		},
		statesInput: {
			type    : 'string',
			default : '',
		},
		statesRegion: {
			type    : 'array',
			default : [],
		},
		zipcodesInput: {
			type    : 'string',
			default : '',
		},
		zipcodesRegion: {
			type    : 'array',
			default : [],
		},
		radiusKm: {
			type    : 'string',
			default : '',
		},
		radiusLat: {
			type    : 'string',
			default : '',
		},
		radiusLng: {
			type    : 'string',
			default : '',
		},
		...settings.attributes
	};

	return settings;
}


/**
 * Attribute Control
 * @param  object BlockEdit
 * @return createElement
 */
const attributesControl = ( BlockEdit ) => {

    return ( props ) => {

    	const { name, attributes, setAttributes, className, focus, setFocus } = props;
		const { includeMode, countriesInput, countriesRegion, citiesRegion, citiesInput, statesRegion, statesInput, zipcodesRegion, zipcodesInput, radiusKm, radiusLat, radiusLng } = attributes;

		if ( ! geotnav.allowed_blocks.includes( name ) ) {
			return createElement( BlockEdit, { ...props } );
		}

        return createElement( fragmentElement, {},
        	createElement( BlockEdit, { key: 'edit', ...props } ),
			createElement( InspectorControls, {},
				createElement( PanelBody, { title: __('Geotargeting', 'geot') },
					createElement( PanelRow, {},
						createElement( RadioControl, {
							label: __( 'Visibility', 'geot'),
							options: [
								{ value : 'include', label : __( 'Only show menu item in', 'geot' ) },
								{ value : 'exclude', label : __( 'Never show menu item in', 'geot' ) },
							],
							selected: includeMode,
							onChange: function( newContent ) {
								setAttributes( { includeMode: newContent } );
							},
							help: __( 'Choose visibility', 'geot' ),
						}),
					),
					createElement( PanelRow, {},
						createElement( SelectControl, {
							label: __( 'Country Regions', 'geot' ),
							multiple: true,
							options: geotnav.regions_country,
							onChange: function( newContent ) {
								setAttributes( { countriesRegion: newContent } );
							},
							value: countriesRegion,
							help: __( 'Choose country region name to show/hide content to', 'geot' ),
							className: 'region-multiple',
						}),
					),
					createElement( PanelRow, {},
						createElement( TextControl, {
							label: __( 'Countries', 'geot' ),
							value: countriesInput,
							onChange: function( newContent ) {
								setAttributes( { countriesInput: newContent } );
							},
							help: __( 'Type country names or ISO codes separated by comma.', 'geot' )
						}),
					),

					geotnav.regions_city.length > 0 && (
					createElement( PanelRow, {},
						createElement( SelectControl, {
							label: __( 'Cities Regions', 'geot' ),
							multiple: true,
							options: geotnav.regions_city,
							onChange: function( newContent ) {
								setAttributes( { citiesRegion: newContent } );
							},
							value: citiesRegion,
							help: __( 'Choose city region name to show/hide content to', 'geot' ),
							className: 'region-multiple',
						}),
					) ),
					createElement( PanelRow, {},
						createElement( TextControl, {
							label: __( 'Cities', 'geot' ),
							value: citiesInput,
							onChange: function( newContent ) {
								setAttributes( { citiesInput: newContent } );
							},
							help: __( 'Type city names or ISO codes separated by comma.', 'geot' )
						}),
					),

					geotnav.regions_state.length > 0 && (
					createElement( PanelRow, {},
						createElement( SelectControl, {
							label: __( 'States Regions', 'geot' ),
							multiple: true,
							options: geotnav.regions_state,
							onChange: function( newContent ) {
								setAttributes( { statesRegion: newContent } );
							},
							value: statesRegion,
							help: __( 'Choose state region name to show/hide content to', 'geot' ),
							className: 'region-multiple',
						}),
					) ),
					createElement( PanelRow, {},
						createElement( TextControl, {
							label: __( 'States', 'geot' ),
							value: statesInput,
							onChange: function( newContent ) {
								setAttributes( { statesInput: newContent } );
							},
							help: __( 'Type state names or ISO codes separated by comma.', 'geot' )
						}),
					),

					geotnav.regions_zip.length > 0 && (
					createElement( PanelRow, {},
						createElement( SelectControl, {
							label: __( 'Zipcodes Regions', 'geot' ),
							multiple: true,
							options: geotnav.regions_zip,
							onChange: function( newContent ) {
								setAttributes( { zipcodesRegion: newContent } );
							},
							value: zipcodesRegion,
							help: __( 'Choose zipcode region name to show/hide content to', 'geot' ),
							className: 'region-multiple',
						}),
					) ),
					createElement( PanelRow, {},
						createElement( TextControl, {
							label: __( 'Zipcodes', 'geot' ),
							value: zipcodesInput,
							onChange: function( newContent ) {
								setAttributes( { zipcodesInput: newContent } );
							},
							help: __( 'Type zipcode names or ISO codes separated by comma.', 'geot' )
						}),
					),

					createElement( PanelRow, {},
						createElement( TextControl, {
							label: __( 'Radius km', 'geot' ),
							value: radiusKm,
							onChange: function( newContent ) {
								setAttributes( { radiusKm: newContent } );
							},
							help: __( 'Type radius km.', 'geot' )
						}),
					),
					createElement( PanelRow, {},
						createElement( TextControl, {
							label: __( 'Radius Latitude', 'geot' ),
							value: radiusLat,
							onChange: function( newContent ) {
								setAttributes( { radiusLat: newContent } );
							},
							help: __( 'Type radius latitude.', 'geot' )
						}),
					),
					createElement( PanelRow, {},
						createElement( TextControl, {
							label: __( 'Radius Longitude', 'geot' ),
							value: radiusLng,
							onChange: function( newContent ) {
								setAttributes( { radiusLng: newContent } );
							},
							help: __( 'Type radius longitude.', 'geot' )
						}),
					),
				),
			),
		);
    };
};


/**
 * Save Atributes
 * @param  {[type]} element  
 * @param  {[type]} blockType
 * @param  {[type]} attributes
 * @return void
 */
const saveAttributes = ( element, blockType, attributes ) => {

	// skip if element is undefined
    if ( ! element ) {
        return;
    }

    // only apply to cover blocks
    if ( ! geotnav.allowed_blocks.includes( blockType.name ) ) {
		return element;
	}

	// return the element wrapped in a div
    return element;
};


// Create Attributes
wp.hooks.addFilter( 'blocks.registerBlockType', 'geot/navigation', createAttributes );

// Add controls to new attributes
wp.hooks.addFilter( 'editor.BlockEdit', 'geot/navigation', attributesControl );

// Save new attributes
//wp.hooks.addFilter( 'blocks.getSaveElement', 'geot/navigation', saveAttributes );
