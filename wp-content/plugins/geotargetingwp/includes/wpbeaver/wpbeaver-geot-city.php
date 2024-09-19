<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * WPBeaver Geo Module
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class WPBeaver_GeoCity {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$section = [

			'title' => esc_html__( 'Geo Cities', 'geot' ),
			'fields' => [
				'cities_mode' => [
					'type'			=> 'select',
					'multi-select'	=> false,
					'label'			=> esc_html__( 'Visibility', 'Geot' ),
					'options'		=> [
						'include'	=> esc_html__( 'Show', 'geot' ),
						'exclude'	=> esc_html__( 'Hide', 'geot' )
					],
					'help' => esc_html__( 'Choose visibility.', 'geot' ),
				],
				'cities_input' => [
					'type'	=> 'text',
					'label'	=> esc_html__( 'Cities', 'Geot' ),
					'help'	=> esc_html__( 'Type city names separated by comma.', 'geot' ),
				],
				'cities_region' => [
					'type'			=> 'select',
					'multi-select'	=> true,
					'label'			=> esc_html__( 'Regions', 'Geot' ),
					'options'		=> GeotWP_WPBeaver::get_regions( 'city' ),
					'help'			=> esc_html__( 'Choose region name to show content to.', 'geot' ),
				],
			],
		];

		return $section;
	}

	/**
	 * Conditional if it apply deprecated method
	 * 
	 * @param  array   $settings
	 * @return boolean
	 */
	static function is_deprecated( $settings = [] ) {
		return isset( $settings['in_cities'] ) || isset( $settings['ex_cities'] ) || isset( $settings['in_region_cities'] ) || isset( $settings['ex_region_cities'] );
	}


	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render( $settings = [] ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::is_render_deprecated( $settings );

		$cities_mode = isset( $settings['cities_mode'] ) ? trim( $settings['cities_mode'] ) : '';
		
		$cities_input = isset( $settings['cities_input'] ) ? trim( $settings['cities_input'] ) : '';

		$cities_region = isset( $settings['cities_region'] ) && is_array( $settings['cities_region'] ) ? array_map( 'trim', $settings['cities_region'] ) : [];

		$cities_region_i = ! empty( $cities_region ) && ! empty( $cities_region[0] ) ? $cities_region : [];

		if ( empty( $cities_input ) && count( $cities_region_i ) == 0 )
			return true;

		if( $cities_mode == 'exclude' )
			return geot_target_city( '', '', $cities_input, $cities_region_i );

		return geot_target_city( $cities_input, $cities_region_i );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings = [], $output = "") {

		$cities_region_commas = '';

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::ajax_render_deprecated( $settings, $output );

		$cities_mode = isset( $settings['cities_mode'] ) ? trim( $settings['cities_mode'] ) : '';
		
		$cities_input = isset( $settings['cities_input'] ) ? trim( $settings['cities_input'] ) : '';

		$cities_region = isset( $settings['cities_region'] ) && is_array( $settings['cities_region'] ) ? array_map( 'trim', $settings['cities_region'] ) : [];

		$cities_region_i = ! empty( $cities_region ) && ! empty( $cities_region[0] ) ? $cities_region : [];


		if ( empty( $cities_input ) && count( $cities_region_i ) == 0 )
			return $output;

		if ( count( $cities_region_i ) > 0 )
			$cities_region_commas = implode( ',', $cities_region_i );


		if( $cities_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="" data-region="" data-ex_filter="' . $cities_input . '" data-ex_region="' . $cities_region_commas . '">' . $output . '</div>';
		}

		return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $cities_input . '" data-region="' . $cities_region_commas . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
	}



	/*
		depreaciate methods
		old params ( v: 3.4.0.0 )
	*/

	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render_deprecated( $settings = [] ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		$in_cities = isset( $settings['in_cities'] ) ? trim( $settings['in_cities'] ) : '';
		$ex_cities = isset( $settings['ex_cities'] ) ? trim( $settings['ex_cities'] ) : '';

		$in_region_cities = isset( $settings['in_region_cities'] ) && is_array( $settings['in_region_cities'] ) ? array_map( 'trim', $settings['in_region_cities'] ) : [];
		$ex_region_cities = isset( $settings['ex_region_cities'] ) && is_array( $settings['ex_region_cities'] ) ? array_map( 'trim', $settings['ex_region_cities'] ) : [];

		$in_region_cities = !empty( $in_region_cities ) && !empty( $in_region_cities[0] ) ? $in_region_cities  : [];
		$ex_region_cities = !empty( $ex_region_cities ) && !empty( $ex_region_cities[0] ) ? $ex_region_cities  : [];


		if ( empty( $in_cities ) && empty( $ex_cities ) &&
			count( $in_region_cities ) == 0 && count( $ex_region_cities ) == 0
		) {
			return true;
		}

		return geot_target_city( $in_cities, $in_region_cities, $ex_cities, $ex_region_cities );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render_deprecated( $settings = [], $output = "" ) {

		$in_regions_commas = $ex_regions_commas = '';

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		$in_cities = isset( $settings['in_cities'] ) ? trim( $settings['in_cities'] ) : '';
		$ex_cities = isset( $settings['ex_cities'] ) ? trim( $settings['ex_cities'] ) : '';

		$in_region_cities = isset( $settings['in_region_cities'] ) && is_array( $settings['in_region_cities'] ) ? array_map( 'trim', $settings['in_region_cities'] ) : [];
		$ex_region_cities = isset( $settings['ex_region_cities'] ) && is_array( $settings['ex_region_cities'] ) ? array_map( 'trim', $settings['ex_region_cities'] ) : [];

		$in_region_cities = !empty( $in_region_cities ) && !empty( $in_region_cities[0] ) ? $in_region_cities  : [];
		$ex_region_cities = !empty( $ex_region_cities ) && !empty( $ex_region_cities[0] ) ? $ex_region_cities  : [];

		if( empty( $in_cities ) && empty( $ex_cities ) &&
			count( $in_region_cities ) == 0 && count( $ex_region_cities ) == 0
		) {
			return $output;
		}

		if( count( $in_region_cities ) > 0 ) {
			$in_regions_commas = implode( ',', $in_region_cities );
		}

		if( count( $ex_region_cities ) > 0 ) {
			$ex_regions_commas = implode( ',', $ex_region_cities );
		}

		return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}

}