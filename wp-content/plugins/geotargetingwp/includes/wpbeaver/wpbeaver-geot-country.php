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
class WPBeaver_GeoCountry {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$section = [

			'title' => esc_html__( 'Geo Countries', 'geot' ),
			'fields' => [
				'countries_mode' => [
					'type'			=> 'select',
					'multi-select'	=> false,
					'label'			=> esc_html__( 'Visibility', 'Geot' ),
					'options'		=> [
						'include'	=> esc_html__( 'Show', 'geot' ),
						'exclude'	=> esc_html__( 'Hide', 'geot' )
					],
					'help' => esc_html__( 'Choose visibility.', 'geot' ),
				],
				'countries_input' => [
					'type'	=> 'text',
					'label'	=> esc_html__( 'Countries', 'Geot' ),
					'help'	=> esc_html__( 'Type country names or ISO codes separated by comma.', 'geot' ),
				],
				'countries_region' => [
					'type'			=> 'select',
					'multi-select'	=> true,
					'label'			=> esc_html__( 'Regions', 'Geot' ),
					'options'		=> GeotWP_WPBeaver::get_regions( 'country' ),
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
		return isset( $settings['in_countries'] ) || isset( $settings['ex_countries'] ) || isset( $settings['in_region_countries'] ) || isset( $settings['ex_region_countries'] );
	}


	/**
	 * Conditional if render
	 *
	 * @param $settings
	 *
	 * @return bool
	 */
	static function is_render( $settings = [] ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::is_render_deprecated( $settings );

		$countries_mode = isset( $settings['countries_mode'] ) ? trim( $settings['countries_mode'] ) : '';
		
		$countries_input = isset( $settings['countries_input'] ) ? trim( $settings['countries_input'] ) : '';

		$countries_region = isset( $settings['countries_region'] ) && is_array( $settings['countries_region'] ) ? array_map( 'trim', $settings['countries_region'] ) : [];

		$countries_region_i = ! empty( $countries_region ) && ! empty( $countries_region[0] ) ? $countries_region : [];

		if ( empty( $countries_input ) && count( $countries_region_i ) == 0 )
			return true;

		if( $countries_mode == 'exclude' )
			return geot_target( '', '', $countries_input, $countries_region_i );

		return geot_target( $countries_input, $countries_region_i );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings = [], $output = "" ) {

		$countries_regions_commas = '';

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::ajax_render_deprecated( $settings, $output );

		$countries_mode = isset( $settings['countries_mode'] ) ? trim( $settings['countries_mode'] ) : '';
		
		$countries_input = isset( $settings['countries_input'] ) ? trim( $settings['countries_input'] ) : '';

		$countries_region = isset( $settings['countries_region'] ) && is_array( $settings['countries_region'] ) ? array_map( 'trim', $settings['countries_region'] ) : [];

		$countries_region_i = ! empty( $countries_region ) && ! empty( $countries_region[0] ) ? $countries_region : [];

		if ( empty( $countries_input ) && count( $countries_region_i ) == 0 )
			return $output;

		if ( count( $countries_region_i ) > 0 )
			$countries_regions_commas = implode( ',', $countries_region_i );

		if( $countries_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="" data-region="" data-ex_filter="' . $countries_input . '" data-ex_region="' . $countries_regions_commas . '">' . $output . '</div>';
		}

		return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $countries_input . '" data-region="' . $countries_regions_commas . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
	}


	/*
		depreaciate methods
		old params ( v: 3.4.0.0 )
	*/

	/**
	 * Conditional if render
	 *
	 * @param $settings
	 *
	 * @return bool
	 */
	static function is_render_deprecated( $settings = [] ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_region_countries = isset( $settings['in_region_countries'] ) && is_array( $settings['in_region_countries'] ) ? array_map( 'trim', $settings['in_region_countries'] ) : [];
		$ex_region_countries = isset( $settings['ex_region_countries'] ) && is_array( $settings['ex_region_countries'] ) ? array_map( 'trim', $settings['ex_region_countries'] ) : [];

		$in_region_countries = !empty( $in_region_countries )  &&  !empty( $in_region_countries[0] ) ? $in_region_countries  : [];
		$ex_region_countries = !empty( $ex_region_countries )  &&  !empty( $ex_region_countries[0] ) ? $ex_region_countries  : [];

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
			count( $in_region_countries ) == 0 && count( $ex_region_countries ) == 0
		) {
			return true;
		}

		return geot_target( $in_countries, $in_region_countries, $ex_countries, $ex_region_countries );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render_deprecated( $settings = [], $output = "" ) {

		$in_regions_commas = $ex_regions_commas = '';

		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_region_countries = isset( $settings['in_region_countries'] ) && is_array( $settings['in_region_countries'] ) ? array_map( 'trim', $settings['in_region_countries'] ) : [];
		$ex_region_countries = isset( $settings['ex_region_countries'] ) && is_array( $settings['ex_region_countries'] ) ? array_map( 'trim', $settings['ex_region_countries'] ) : [];

		$in_region_countries = !empty( $in_region_countries )  &&  !empty( $in_region_countries[0] ) ? $in_region_countries  : [];
		$ex_region_countries = !empty( $ex_region_countries )  &&  !empty( $ex_region_countries[0] ) ? $ex_region_countries  : [];

		if( empty( $in_countries ) && empty( $ex_countries ) &&
			count( $in_region_countries ) == 0 && count( $ex_region_countries ) == 0
		) {
			return $output;
		}

		if( count( $in_region_countries ) > 0 ) {
			$in_regions_commas = implode( ',', $in_region_countries );
		}

		if( count( $ex_region_countries ) > 0 ) {
			$ex_regions_commas = implode( ',', $ex_region_countries );
		}

		return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $countries_input . '" data-region="' . $countries_regions_commas . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
	}
}