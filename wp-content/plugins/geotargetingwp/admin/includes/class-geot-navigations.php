<?php

/**
 * Adds GeoTarget to menus
 * @since  1.8
 */
class GeotWP_Navigations {

	/**
	 * Blocks allowed to menu section
	 * @var array
	 */
	public array $allowedBlocks = [
		'core/navigation-link',
		'core/navigation-submenu',
		'core/home-link',
	];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 */
	public function __construct() {

		$settings = geotwp_settings();

		if ( empty( $settings['disable_menu_integration'] ) ) {
			add_action( 'enqueue_block_editor_assets', [ $this, 'enqueueScripts' ] );
			add_filter( 'render_block', [ $this, 'renderMenu' ], 10, 3 );
		}
	}

	/**
	 * Enqueue scripts to Gutenberg block
	 * @param  string $screen
	 * @return void
	 */
	public function enqueueScripts( string $screen ): void {

		$screen = get_current_screen();

		if ( $screen->base !== 'site-editor' ) {
			return;
		}

		$localizeNav = [
			'allowed_blocks'  => $this->allowedBlocks,
			'regions_country' => $this->getRegions( 'countries' ),
			'regions_state'   => $this->getRegions( 'states' ),
			'regions_city'    => $this->getRegions( 'cities' ),
			'regions_zip'     => $this->getRegions( 'zips' ),
		];

		wp_enqueue_script(
			'navigation-block',
			GEOWP_PLUGIN_URL . 'admin/js/navigation-block.js',
			[ 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'gutenberg-geo' ]
		);

		wp_localize_script( 'navigation-block', 'geotnav', $localizeNav );
	}


	/**
	 * Show/Hide navigation link
	 * @param  string    $blockContent
	 * @param  array     $parsedBlock
	 * @param  \WP_Block $block 
	 * @return string
	 */
	public function renderMenu( string $blockContent, array $parsedBlock, WP_Block $block ): string {
		
		if ( ! in_array( $block->name, $this->allowedBlocks ) ) {
			return $blockContent;
		}

		if ( ! isset( $parsedBlock['attrs'] ) ) {
			return $blockContent;
		}

		$settings = geot_settings();
		$attrs    = $parsedBlock['attrs'];

		$targeted = [
			'country_code' => $attrs['countriesInput'] ?? '',
			'region'       => isset( $attrs['countriesRegion'] ) ? implode( ',', $attrs['countriesRegion'] ) : '',
			'cities'       => $attrs['citiesInput'] ?? '',
			'city_region'  => isset( $attrs['citiesRegion'] ) ? implode( ',', $attrs['citiesRegion'] ) : '',
			'states'       => $attrs['statesInput'] ?? '',
			'state_region' => isset( $attrs['statesRegion'] ) ? implode( ',', $attrs['statesRegion'] ) : '',
			'zipcodes'     => $attrs['zipcodesInput'] ?? '',
			'zip_region'   => isset( $attrs['zipcodesRegion'] ) ? implode( ',', $attrs['zipcodesRegion'] ) : '',
			'radius_km'    => $attrs['radiusKm'] ?? '',
			'radius_lat'   => $attrs['radiusLat'] ?? '',
			'radius_lng'   => $attrs['radiusLng'] ?? '',
		];

		
		if ( isset( $settings['ajax_mode'] ) && $settings['ajax_mode'] == '1' ) {

			$targeted[ 'geot_include_mode' ] = $attrs['includeMode'] ?? 'include';

			$dataAttrs   = [];
			$dataAttrs[] = 'data-action="menu_filter"';
			$dataAttrs[] = sprintf( 'data-filter="%s"', base64_encode( serialize( $targeted ) ) );
			$dataAttrs[] = sprintf( 'data-ex_filter="%s"', $this->generateBlockID( $attrs ) );

			$blockContent = geotReplaceFirst( 'class="', 'class="geot-ajax geot_menu_item ', $blockContent );
			$blockContent = geotReplaceFirst( '<a', '<a '. implode( ' ', $dataAttrs ), $blockContent );
			

		} else {

			if ( empty( \array_filter( $targeted ) ) ) {
				return $blockContent;
			}

			$targeted[ 'geot_include_mode' ] = $attrs['includeMode'] ?? 'include';

			if ( GeotWP_Helper::user_is_targeted( $targeted, $this->generateBlockID( $attrs ) ) ) {
				return '';
			}
		}

		return $blockContent;
	}


	/**
	 * Get Regions
	 * @param  string $slug
	 * @return array 
	 */
	protected function getRegions( string $slug = 'country' ): array {

		$dropdownRegions = [];

		switch ( $slug ) {
			case 'cities':
				$regions = geot_city_regions();
				break;
			case 'states':
				$regions = geot_state_regions();
				break;
			case 'zips':
				$regions = geot_zip_regions();
				break;
			default:
				$regions = geot_country_regions();
		}

		if ( ! empty( $regions ) ) {
			foreach ( $regions as $r ) {
				if ( ! isset( $r['name'] ) || empty( $r['name'] ) ) {
					continue;
				}

				$dropdownRegions[] = [
					'value' => $r['name'],
					'label' => $r['name']
				];
			}
		}

		return $dropdownRegions;
	}


	/**
	 * Generate Block ID
	 * @param  array  $attributes
	 * @return string
	 */
	public function generateBlockID( array $attributes ): string {

		if ( ! empty( $attributes['id'] ) ) {
			return $attributes['id'];
		}

		// Remove all empty string values as they're not present in JS hash building.
		foreach ( $attributes as $key => $value ) {
			if ( '' === $value ) {
				unset( $attributes[ $key ] );
			}
		}

		// Check if data is empty and remove it if so to match JS hash building.
		if ( isset( $attributes['data'] ) && empty( $attributes['data'] ) ) {
			unset( $attributes['data'] );
		}

		ksort( $attributes );

		return md5( wp_json_encode( $attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	}
}