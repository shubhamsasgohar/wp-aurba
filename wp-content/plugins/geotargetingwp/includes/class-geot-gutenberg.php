<?php

/**
 * Gutenberg Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_Gutenberg {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 */
	public function __construct() {
		global $wp_version;
		if( apply_filters( 'geot/deactivate_gutenberg_integration', false ) ) {
			return;
		}
		add_action( 'init', [ $this, 'register_init' ] );
		if ( version_compare( $wp_version, '5.8', '<' ) )
			add_filter( 'block_categories', [ $this, 'register_category' ], 10, 2 );
		else
			add_filter( 'block_categories_all', [ $this, 'register_category' ], 10, 2 );
		
		add_action( 'enqueue_block_editor_assets', [ $this, 'register_block' ] );
	}

	/**
	 * Register Category
	 * @param  array $categories
	 * @param  WP_Post $post
	 * @return array
	 */
	public function register_category( $categories = [], $post = null ) {

		return array_merge( $categories, [
			[
				'slug'  => 'geot-block',
				'title' => esc_html__( 'Geotargeting', 'geot' ),
				'icon'  => '',
			],
		] );
	}

	/**
	 * Register Blocks
	 * @var
	 */
	public function register_init() {

		if ( function_exists( 'register_block_type' ) ) {

			require_once GEOWP_PLUGIN_DIR . 'includes/gutenberg/gutenberg-geot-updater.php';

			register_block_type( 'geotargeting-pro/gutenberg-all',
				[ 'render_callback' => [ $this, 'save_gutenberg_all' ] ]
			);

			register_block_type( 'geotargeting-pro/gutenberg-country',
				[ 'render_callback' => [ $this, 'save_gutenberg_country' ] ]
			);

			register_block_type( 'geotargeting-pro/gutenberg-city',
				[ 'render_callback' => [ $this, 'save_gutenberg_city' ] ]
			);

			register_block_type( 'geotargeting-pro/gutenberg-state',
				[ 'render_callback' => [ $this, 'save_gutenberg_state' ] ]
			);

			register_block_type( 'geotargeting-pro/gutenberg-zipcode',
				[ 'render_callback' => [ $this, 'save_gutenberg_zipcode' ] ]
			);

			register_block_type( 'geotargeting-pro/gutenberg-radius',
				[ 'render_callback' => [ $this, 'save_gutenberg_radius' ] ]
			);
		}
	}

	/**
	 * Save Country Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_all( $atts, $content ) {

		// Countries
		$countries_mode = isset( $atts['countries_mode'] ) ? trim( $atts['countries_mode'] ) : 'include';
		$countries_input = isset( $atts['countries_input'] ) ? trim( $atts['countries_input'] ) : '';
		$countries_regions = isset( $atts['countries_region'] ) && is_array( $atts['countries_region'] ) ? array_map( 'trim', $atts['countries_region'] ) : [];

		$countries_regions_i = count( $countries_regions ) > 0 ? implode( ',', $countries_regions ) : '';

		


		// Cities
		$cities_mode = isset( $atts['cities_mode'] ) ? trim( $atts['cities_mode'] ) : 'include';
		$cities_input = isset( $atts['cities_input'] ) ? trim( $atts['cities_input'] ) : '';
		$cities_regions = isset( $atts['cities_region'] ) && is_array( $atts['cities_region'] ) ? array_map( 'trim', $atts['cities_region'] ) : [];

		$cities_regions_i = count( $cities_regions ) > 0 ? implode( ',', $cities_regions ) : '';

		
		// States
		$states_mode = isset( $atts['states_mode'] ) ? trim( $atts['states_mode'] ) : 'include';
		$states_input = isset( $atts['states_input'] ) ? trim( $atts['states_input'] ) : '';

		$states_regions = isset( $atts['states_region'] ) && is_array( $atts['states_region'] ) ? array_map( 'trim', $atts['states_region'] ) : [];

		$states_regions_i = count( $states_regions ) > 0 ? implode( ',', $states_regions ) : '';


		// Zipcodes
		$zipcodes_mode = isset( $atts['zipcodes_mode'] ) ? trim( $atts['zipcodes_mode'] ) : 'include';
		$zipcodes_input = isset( $atts['zipcodes_input'] ) ? trim( $atts['zipcodes_input'] ) : '';

		$zipcodes_regions = isset( $atts['zipcodes_region'] ) && is_array( $atts['zipcodes_region'] ) ? array_map( 'trim', $atts['zipcodes_region'] ) : [];

		$zipcodes_regions_i = count( $zipcodes_regions ) > 0 ? implode( ',', $zipcodes_regions ) : '';

		// Radius
		$radius_mode	= isset( $atts['radius_mode'] ) ? trim( $atts['radius_mode'] ) : 'include';
		$radius_km		= isset( $atts['radius_km'] ) ? trim( $atts['radius_km'] ) : '';
		$radius_lat		= isset( $atts['radius_lat'] ) ? trim( $atts['radius_lat'] ) : '';
		$radius_lng		= isset( $atts['radius_lng'] ) ? trim( $atts['radius_lng'] ) : '';
 
 		$args = [];
		$opts = geot_settings();

		// If ajax mode
		if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

			// Countries
			if( ! empty( $countries_input ) || ! empty( $countries_regions_i ) ) {
				
				$args[] = [
					'action'		=> 'country_filter',
					'filter'		=> $countries_mode == 'include' ? $countries_input : '',
					'region'		=> $countries_mode == 'include' ? $countries_regions_i : '',
					'ex_filter'		=> $countries_mode == 'exclude' ? $countries_input : '',
					'ex_region'		=> $countries_mode == 'exclude' ? $countries_regions_i : '',
				];
			}

			// Cities
			if( ! empty( $cities_input ) || ! empty( $cities_regions_i ) ) {
				
				$args[] = [
					'action'		=> 'city_filter',
					'filter'		=> $cities_mode == 'include' ? $cities_input : '',
					'region'		=> $cities_mode == 'include' ? $cities_regions_i : '',
					'ex_filter'		=> $cities_mode == 'exclude' ? $cities_input : '',
					'ex_region'		=> $cities_mode == 'exclude' ? $cities_regions_i : '',
				];
			}

			// States
			if( ! empty( $states_input ) || ! empty( $states_regions_i ) ) {
				
				$args[] = [
					'action'		=> 'state_filter',
					'filter'		=> $states_mode == 'include' ? $states_input : '',
					'region'		=> $states_mode == 'include' ? $states_regions_i : '',
					'ex_filter'		=> $states_mode == 'exclude' ? $states_input : '',
					'ex_region'		=> $states_mode == 'exclude' ? $states_regions_i : '',
				];
			}


			// Zipcodes
			if( ! empty( $zipcodes_input ) || ! empty( $zipcodes_regions_i ) ) {
				
				$args[] = [
					'action'		=> 'zip_filter',
					'filter'		=> $zipcodes_mode == 'include' ? $zipcodes_input : '',
					'region'		=> $zipcodes_mode == 'include' ? $zipcodes_regions_i : '',
					'ex_filter'		=> $zipcodes_mode == 'exclude' ? $zipcodes_input : '',
					'ex_region'		=> $zipcodes_mode == 'exclude' ? $zipcodes_regions_i : '',
				];
			}

			
			// Radius
			if( ! empty( $radius_km ) || ! empty( $radius_lat ) || ! empty( $radius_lng ) ) {
				
				$args[] = [
					'action'		=> 'radius_filter',
					'geo_mode'		=> $radius_mode,
					'filter'		=> $radius_km,
					'region'		=> $radius_lat,
					'ex_filter'		=> $radius_lng,
				];
			}

			// If $args is not empty
			if( count( $args ) > 0 ) {

				foreach( $args as $arg ) {
					
					$attrs = [];
					foreach( $arg as $key => $value ) {
						$attrs[] = sprintf(
							'data-%s = "%s"', esc_attr( $key ), esc_attr( $value )
						);
					}

					$content = sprintf(
						'<div class="geot-ajax geot-filter" %s>%s</div>',
						implode( ' ', $attrs ), $content
					);
				}
			}

			return $content;

		} else {

			// Countries
			if( ! empty( $countries_input ) || ! empty( $countries_regions_i ) ) {
				
				$args[] = [
					'action'		=> 'country',
					'in_input'		=> $countries_mode == 'include' ? $countries_input : '',
					'in_regions'	=> $countries_mode == 'include' ? $countries_regions_i : '',
					'ex_input'		=> $countries_mode == 'exclude' ? $countries_input : '',
					'ex_regions'	=> $countries_mode == 'exclude' ? $countries_regions_i : '',
				];
			}

			// Cities
			if( ! empty( $cities_input ) || ! empty( $cities_regions_i ) ) {
				
				$args[] = [
					'action'		=> 'city',
					'in_input'		=> $cities_mode == 'include' ? $cities_input : '',
					'in_regions'	=> $cities_mode == 'include' ? $cities_regions_i : '',
					'ex_input'		=> $cities_mode == 'exclude' ? $cities_input : '',
					'ex_regions'	=> $cities_mode == 'exclude' ? $cities_regions_i : '',
				];
			}

			// States
			if( ! empty( $states_input ) || ! empty( $states_regions_i ) ) {
				
				$args[] = [
					'action'		=> 'state',
					'in_input'		=> $states_mode == 'include' ? $states_input : '',
					'in_regions'	=> $states_mode == 'include' ? $states_regions_i : '',
					'ex_input'		=> $states_mode == 'exclude' ? $states_input : '',
					'ex_regions'	=> $states_mode == 'exclude' ? $states_regions_i : '',
				];
			}


			// Zipcodes
			if( ! empty( $zipcodes_input ) || ! empty( $zipcodes_regions_i ) ) {
				
				$args[] = [
					'action'		=> 'zip',
					'in_input'		=> $zipcodes_mode == 'include' ? $zipcodes_input : '',
					'in_regions'	=> $zipcodes_mode == 'include' ? $zipcodes_regions_i : '',
					'ex_input'		=> $zipcodes_mode == 'exclude' ? $zipcodes_input : '',
					'ex_regions'	=> $zipcodes_mode == 'exclude' ? $zipcodes_regions_i : '',
				];
			}


			// If $args is not empty
			if( count( $args ) > 0 ) {

				foreach( $args as $arg ) {

					if( ! geot_target(
							$arg['in_input'], $arg['in_regions'],
							$arg['ex_input'], $arg['ex_regions'], $arg['action']
						)
					) return '';
				}
			}


			// Radius
			if( ! empty( $radius_km ) || ! empty( $radius_lat ) || ! empty( $radius_lng ) ) {

				$target = geot_target_radius( $radius_lat, $radius_lng, $radius_km );
				
				if( ( $radius_mode == 'include' || $target ) && ( $radius_mode == 'exclude' || ! $target ) ) {
					return '';
				}
			}
		}

		return $content;
	}
	

	/**
	 * Save Country Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_country( $atts, $content ) {

		// use depreciate method
		if( $this->is_gutenberg_country_deprecated( $atts ) )
			return $this->save_gutenberg_country_deprecated( $atts, $content );

		$countries_regions_i = '';

		$countries_mode = isset( $atts['countries_mode'] ) ? trim( $atts['countries_mode'] ) : 'include';
		$countries_input = isset( $atts['countries_input'] ) ? trim( $atts['countries_input'] ) : '';

		$countries_regions = isset( $atts['countries_region'] ) && is_array( $atts['countries_region'] ) ? array_map( 'trim', $atts['countries_region'] ) : [];

		if ( count( $countries_regions ) > 0 )
			$countries_regions_i = implode( ',', $countries_regions );

		$opts = geot_settings();

		if( $countries_mode == 'exclude' ) {

			if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="" data-region="" data-ex_filter="' . $countries_input . '" data-ex_region="' . $countries_regions_i . '">' . $content . '</div>';
			
			} elseif( geot_target( '', '', $countries_input, $countries_regions_i ) ) {
				return $content;
			}
		
		} else {

			if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $countries_input . '" data-region="' . $countries_regions_i . '" data-ex_filter="" data-ex_region="">' . $content . '</div>';
			
			} elseif( geot_target( $countries_input, $countries_regions_i ) ) {
				return $content;
			}
		}

		return '';
	}

	/**
	 * Save City Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_city( $atts, $content ) {

		// use depreciate method
		if( $this->is_gutenberg_city_deprecated( $atts ) )
			return $this->save_gutenberg_city_deprecated( $atts, $content );

		$cities_regions_i = '';

		$cities_mode = isset( $atts['cities_mode'] ) ? trim( $atts['cities_mode'] ) : 'include';
		$cities_input = isset( $atts['cities_input'] ) ? trim( $atts['cities_input'] ) : '';

		$cities_regions = isset( $atts['cities_region'] ) && is_array( $atts['cities_region'] ) ? array_map( 'trim', $atts['cities_region'] ) : [];

		if ( count( $cities_regions ) > 0 )
			$cities_regions_i = implode( ',', $cities_regions );

		$opts = geot_settings();

		if( $cities_mode == 'exclude' ) {

			if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="" data-region="" data-ex_filter="' . $cities_input . '" data-ex_region="' . $cities_regions_i . '">' . $content . '</div>';
			
			} elseif( geot_target_city( '', '', $cities_input, $cities_regions_i ) ) {
				return $content;
			}
		} else {

			if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $cities_input . '" data-region="' . $cities_regions_i . '" data-ex_filter="" data-ex_region="">' . $content . '</div>';
			
			} elseif( geot_target_city( $cities_input, $cities_regions_i ) ) {
				return $content;
			}
		}

		return '';
	}

	/**
	 * Save State Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_state( $atts, $content ) {
		
		$states_regions_i = '';

		// use depreciate method
		if( $this->is_gutenberg_state_deprecated( $atts ) )
			return $this->save_gutenberg_state_deprecated( $atts, $content );

		$states_mode = isset( $atts['states_mode'] ) ? trim( $atts['states_mode'] ) : 'include';
		$states_input = isset( $atts['states_input'] ) ? trim( $atts['states_input'] ) : '';

		$states_regions = isset( $atts['states_region'] ) && is_array( $atts['states_region'] ) ? array_map( 'trim', $atts['states_region'] ) : [];

		if ( count( $states_regions ) > 0 )
			$states_regions_i = implode( ',', $states_regions );

		$opts = geot_settings();

		if( $states_mode == 'exclude' ) {

			if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="" data-region="" data-ex_filter="' . $states_input . '" data-ex_region="' . $states_regions_i . '">' . $content . '</div>';
			
			} elseif( geot_target_state( '', '', $states_input, $states_regions_i ) ) {
				return $content;
			}
		
		} else {

			if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $states_input . '" data-region="' . $states_regions_i . '" data-ex_filter="" data-ex_region="">' . $content . '</div>';
			
			} elseif( geot_target_state( $states_input, $states_regions_i ) ) {
				return $content;
			}
		}

		return '';
	}

	/**
	 * Save Zipcode Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_zipcode( $atts, $content ) {

		// use depreciate method
		if( $this->is_gutenberg_zipcode_deprecated( $atts ) )
			return $this->save_gutenberg_zipcode_deprecated( $atts, $content );

		$zipcodes_regions_i = '';

		$zipcodes_mode = isset( $atts['zipcodes_mode'] ) ? trim( $atts['zipcodes_mode'] ) : 'include';
		$zipcodes_input = isset( $atts['zipcodes_input'] ) ? trim( $atts['zipcodes_input'] ) : '';

		$zipcodes_regions = isset( $atts['zipcodes_region'] ) && is_array( $atts['zipcodes_region'] ) ? array_map( 'trim', $atts['zipcodes_region'] ) : [];

		if ( count( $zipcodes_regions ) > 0 )
			$zipcodes_regions_i = implode( ',', $zipcodes_regions );

		$opts = geot_settings();

		if( $zipcodes_mode == 'exclude' ) {

			if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="" data-region="" data-ex_filter="' . $zipcodes_input . '" data-ex_region="' . $zipcodes_regions_i . '">' . $content . '</div>';
			
			} elseif( geot_target_zip( '', '', $zipcodes_input, $zipcodes_regions_i ) ) {
				return $content;
			}

		} else {

			if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $zipcodes_input . '" data-region="' . $zipcodes_regions_i . '" data-ex_filter="" data-ex_region="">' . $content . '</div>';
			
			} elseif( geot_target_zip( $zipcodes_input, $zipcodes_regions_i ) ) {
				return $content;
			}
		}

		return '';
	}

	/**
	 * Save Radius Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_radius( $atts, $content ) {

		// use depreciate method
		//if( ! isset( $atts['radius_mode'] ) )
		//	return $this->save_gutenberg_radius_deprecated( $atts, $content );
		
		$radius_mode	= isset( $atts['radius_mode'] ) ? trim( $atts['radius_mode'] ) : 'include';
		$radius_km		= isset( $atts['radius_km'] ) ? trim( $atts['radius_km'] ) : '';
		$radius_lat		= isset( $atts['radius_lat'] ) ? trim( $atts['radius_lat'] ) : '';
		$radius_lng		= isset( $atts['radius_lng'] ) ? trim( $atts['radius_lng'] ) : '';

		$opts = geot_settings();


		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			return '<div class="geot-ajax geot-filter" data-action="radius_filter" data-geo_mode="' . $radius_mode . '" data-filter="' . $radius_km . '" data-region="' . $radius_lat . '" data-ex_filter="' . $radius_lng . '">' . $content . '</div>';
		} else {

			$target = geot_target_radius( $radius_lat, $radius_lng, $radius_km );

			if( ( $radius_mode == 'exclude' && ! $target ) || ( $radius_mode == 'include' && $target ) )
				return $content;
		}

		return '';
	}

	/**
	 * Register JS Blocks
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function register_block() {

		if( isset( get_current_screen()->id ) && 'widgets' ==  get_current_screen()->id ) {
			return;
		}
		/********************
		 * JS to Geot
		 *********************/
		$modules_geot = [
			'geotargeting-pro/gutenberg-all',
			'geotargeting-pro/gutenberg-country',
			'geotargeting-pro/gutenberg-city',
			'geotargeting-pro/gutenberg-state',
			'geotargeting-pro/gutenberg-zipcode',
			'geotargeting-pro/gutenberg-radius',
		];

		$localize_geot = [
			'icon_all'        => GEOWP_PLUGIN_URL . '/admin/img/world.png',
			'icon_country'    => GEOWP_PLUGIN_URL . '/admin/img/world.png',
			'icon_city'       => GEOWP_PLUGIN_URL . '/admin/img/cities.png',
			'icon_state'      => GEOWP_PLUGIN_URL . '/admin/img/states.png',
			'icon_zipcode'    => GEOWP_PLUGIN_URL . '/admin/img/states.png',
			'icon_radius'     => GEOWP_PLUGIN_URL . '/admin/img/world.png',
			'regions_country' => $this->get_regions( 'countries' ),
			'regions_state'   => $this->get_regions( 'states' ),
			'regions_city'    => $this->get_regions( 'cities' ),
			'regions_zip'     => $this->get_regions( 'zips' ),
			'modules'         => $modules_geot,
		];

		wp_enqueue_script(
			'gutenberg-geo',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot.js',
			[ 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ],
			GEOWP_VERSION,
			true
		);
		wp_localize_script( 'gutenberg-geo', 'gutgeot', $localize_geot );


		/**********************
		 * JS to ALL Geot
		 ***********************/
		wp_enqueue_script(
			'gutenberg-geo-all',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot-all.js',
			[ 'gutenberg-geo' ],
			GEOWP_VERSION,
			true
		);

		/**********************
		 * JS to Country
		 ***********************/
		wp_enqueue_script(
			'gutenberg-geo-country',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot-country.js',
			[ 'gutenberg-geo' ],
			GEOWP_VERSION,
			true
		);


		/**********************
		 * JS to City
		 ***********************/
		wp_enqueue_script(
			'gutenberg-geo-city',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot-city.js',
			[ 'gutenberg-geo' ],
			GEOWP_VERSION,
			true
		);


		/**********************
		 * JS to State
		 ***********************/
		wp_enqueue_script(
			'gutenberg-geo-state',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot-state.js',
			[ 'gutenberg-geo' ],
			GEOWP_VERSION,
			true
		);


		/**********************
		 * JS to Zipcode
		 ***********************/
		wp_enqueue_script(
			'gutenberg-geo-zipcode',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot-zipcode.js',
			[ 'gutenberg-geo' ],
			GEOWP_VERSION,
			true
		);

		/**********************
		 * JS to Radius
		 ***********************/
		wp_enqueue_script(
			'gutenberg-geo-radius',
			GEOWP_PLUGIN_URL . '/includes/gutenberg/gutenberg-geot-radius.js',
			[ 'gutenberg-geo' ],
			GEOWP_VERSION,
			true
		);
	}

	/**
	 * Get Regions
	 * @var    string $slug_region
	 */
	protected function get_regions( $slug_region = 'country' ) {

		$dropdown_values = [];

		switch ( $slug_region ) {
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
				if ( isset( $r['name'] ) ) {
					$dropdown_values[] = [ 'value' => $r['name'], 'label' => $r['name'] ];
				}
			}
		}

		return $dropdown_values;
	}


	/*
		depreaciate methods
		old params ( v: 3.4.0.0 )
	*/

	/**
	 * Conditional if it apply deprecated method
	 * 
	 * @param  array   $atts
	 * @return boolean
	 */
	private function is_gutenberg_country_deprecated( $atts = [] ) {
		return isset( $atts['in_countries'] ) || isset( $atts['ex_countries'] ) || isset( $atts['in_regions'] ) || isset( $atts['ex_regions'] );
	}

	/**
	 * Conditional if it apply deprecated method
	 * 
	 * @param  array   $atts
	 * @return boolean
	 */
	private function is_gutenberg_city_deprecated( $atts = [] ) {
		return isset( $atts['in_cities'] ) || isset( $atts['ex_cities'] ) || isset( $atts['in_regions'] ) || isset( $atts['ex_regions'] );
	}

	/**
	 * Conditional if it apply deprecated method
	 * 
	 * @param  array   $atts
	 * @return boolean
	 */
	private function is_gutenberg_state_deprecated( $atts = [] ) {
		return isset( $atts['in_states'] ) || isset( $atts['ex_states'] ) || isset( $atts['in_regions'] ) || isset( $atts['ex_regions'] );
	}

	/**
	 * Conditional if it apply deprecated method
	 * 
	 * @param  array   $atts
	 * @return boolean
	 */
	private function is_gutenberg_zipcode_deprecated( $atts = [] ) {
		return isset( $atts['in_zipcodes'] ) || isset( $atts['ex_zipcodes'] ) || isset( $atts['in_regions'] ) || isset( $atts['ex_regions'] );
	}


	/**
	 * Save Country Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_country_deprecated( $atts, $content ) {

		$in_regions_i = $ex_regions_i = '';

		$in_countries = isset( $atts['in_countries'] ) ? trim( $atts['in_countries'] ) : '';
		$ex_countries = isset( $atts['ex_countries'] ) ? trim( $atts['ex_countries'] ) : '';

		$in_regions = isset( $atts['in_regions'] ) && is_array( $atts['in_regions'] ) ? array_map( 'trim', $atts['in_regions'] ) : [];
		$ex_regions = isset( $atts['ex_regions'] ) && is_array( $atts['ex_regions'] ) ? array_map( 'trim', $atts['ex_regions'] ) : [];

		if ( count( $in_regions ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions );
		}

		if ( count( $ex_regions ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions );
		}

		$opts = geot_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions_i . '">' . $content . '</div>';
		} elseif( geot_target( $in_countries, $in_regions, $ex_countries, $ex_regions ) ) {
			return $content;
		}

		return '';
	}

	/**
	 * Save City Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_city_deprecated( $atts, $content ) {
		$in_regions_i = $ex_regions_i = '';

		$in_cities = isset( $atts['in_cities'] ) ? trim( rtrim( $atts['in_cities'], ',') ) : '';
		$ex_cities = isset( $atts['ex_cities'] ) ? trim( rtrim( $atts['ex_cities'], ',') ) : '';

		$in_regions = isset( $atts['in_regions'] ) && is_array( $atts['in_regions'] ) ? array_map( 'trim', $atts['in_regions'] ) : [];
		$ex_regions = isset( $atts['ex_regions'] ) && is_array( $atts['ex_regions'] ) ? array_map( 'trim', $atts['ex_regions'] ) : [];

		if ( count( $in_regions ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions );
		}

		if ( count( $ex_regions ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions );
		}

		$opts = geot_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $in_cities . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_cities . '" data-ex_region="' . $ex_regions_i . '">' . $content . '</div>';

		} elseif( geot_target_city( $in_cities, $in_regions, $ex_cities, $ex_regions ) ) {
		    return $content;
		}

		return '';
	}

	/**
	 * Save State Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_state_deprecated( $atts, $content ) {
		
		$in_regions_i = $ex_regions_i = '';

		$in_states = isset( $atts['in_states'] ) ? trim( rtrim( $atts['in_states'] ),',') : '';
		$ex_states = isset( $atts['ex_states'] ) ? trim( rtrim( $atts['ex_states'] ),',') : '';

		$in_regions = isset( $atts['in_regions'] ) && is_array( $atts['in_regions'] ) ? array_map( 'trim', $atts['in_regions'] ) : [];
		$ex_regions = isset( $atts['ex_regions'] ) && is_array( $atts['ex_regions'] ) ? array_map( 'trim', $atts['ex_regions'] ) : [];

		if( count( $in_regions ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions );
		}

		if( count( $ex_regions ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions );
		}

		$opts = geot_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_states . '" data-ex_region="' . $ex_regions_i . '">' . $content . '</div>';

		} elseif( geot_target_state( $in_states, $in_regions, $ex_states, $ex_regions ) ) {
			return $content;
		}

		return '';
	}

	/**
	 * Save Zipcode Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_zipcode_deprecated( $atts, $content ) {
		$in_regions_i = $ex_regions_i = '';

		$in_zipcodes = isset( $atts['in_zipcodes'] ) ? trim( rtrim(  $atts['in_zipcodes'] ), ',') : '';
		$ex_zipcodes = isset( $atts['ex_zipcodes'] ) ? trim( rtrim(  $atts['ex_zipcodes'] ), ',') : '';

		$in_regions = isset( $atts['in_regions'] ) && is_array( $atts['in_regions'] ) ? array_map( 'trim', $atts['in_regions'] ) : [];
		$ex_regions = isset( $atts['ex_regions'] ) && is_array( $atts['ex_regions'] ) ? array_map( 'trim', $atts['ex_regions'] ) : [];

		if( count( $in_regions ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions );
		}

		if( count( $ex_regions ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions );
		}

		$opts = geot_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zipcodes . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_zipcodes . '" data-ex_region="' . $ex_regions_i . '">' . $content . '</div>';
		
		} elseif( geot_target_zip( $in_zipcodes, $in_regions, $ex_zipcodes, $ex_regions ) ) {
			return $content;
		}

		return '';
	}

	/**
	 * Save Radius Block
	 * @var    string $attributes
	 * @var    string $content
	 */
	public function save_gutenberg_radius_deprecated( $atts, $content ) {
		
		$radius_km	= isset( $atts['radius_km'] ) ? trim( $atts['radius_km'] ) : '';
		$radius_lat = isset( $atts['radius_lat'] ) ? trim( $atts['radius_lat'] ) : '';
		$radius_lng = isset( $atts['radius_lng'] ) ? trim( $atts['radius_lng'] ) : '';

		$opts = geot_settings();

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			return '<div class="geot-ajax geot-filter" data-action="radius_filter" data-filter="' . $radius_km . '" data-region="' . $radius_lat . '" data-ex_filter="' . $radius_lng . '">' . $content . '</div>';
		
		} elseif( geot_target_radius( $radius_lat, $radius_lng, $radius_km ) ) {
			return $content;
		}

		return '';
	}
}
