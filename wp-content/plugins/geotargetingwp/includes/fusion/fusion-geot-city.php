<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Fusion Geo Module
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class Fusion_GeoCity {

	/**
	 * Geot fields to City
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields = [
			[
				'type'			=> 'radio_button_set',
				'heading'		=> esc_attr__( 'City Visibility', 'geot' ),
				'description'	=> esc_attr__( 'Choose visibility.', 'geot' ),
				'param_name'	=> 'cities_mode',
				'default'		=> 'include',
				'value'	=> [
					'include'	=> esc_attr__( 'Show', 'geot' ),
					'exclude'	=> esc_attr__( 'Hide', 'geot' ),
				],
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Cities', 'geot' ),
				'description'	=> esc_attr__( 'Type city names separated by commas.', 'geot' ),
				'param_name'	=> 'cities_input',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'City Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'cities_region',
				'value'			=> GeotWP_Fusion::get_regions( 'city' ),
				'default'		=> 'null',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],
		];

		return $fields;
	}

	/**
	 * Conditional if it apply deprecated method
	 * 
	 * @param  array   $settings
	 * @return boolean
	 */
	static function is_deprecated( $settings = [] ) {
		return isset( $settings['geot_in_cities'] ) || isset( $settings['geot_ex_cities'] ) || isset( $settings['geot_in_region_cities'] ) || isset( $settings['geot_ex_region_cities'] );
	}


	/**
	 * Conditional if render
	 *
	 * @return bool
	 */
	static function is_render( $attrs ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $attrs ) )
			return self::is_render_deprecated( $attrs );

		$cities_mode = isset( $attrs['cities_mode'] ) ? trim( $attrs['cities_mode'] ) : 'include';
		$cities_input = isset( $attrs['cities_input'] ) ?  trim( $attrs['cities_input'] ) : '';
		
		$cities_region = isset( $attrs['cities_region'] ) ? GeotWP_Fusion::clean_region( $attrs['cities_region'] ) : [];

		if ( empty( $cities_input ) && count( $cities_region ) == 0 )
			return true;

		if( $cities_mode == 'exclude' )
			return geot_target_city( '', '', $cities_input, $cities_region );


		return geot_target_city( $cities_input, $cities_region );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_render( $attrs, $output ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $attrs ) )
			return self::ajax_render_deprecated( $attrs, $output );

		$cities_mode = isset( $attrs['cities_mode'] ) ? trim( $attrs['cities_mode'] ) : 'include';

		$cities_input = isset( $attrs['cities_input'] ) ? trim( $attrs['cities_input'] ) : '';
		$cities_region = isset( $attrs['cities_region'] ) ? $attrs['cities_region'] : '';


		if( empty( $cities_input ) &&
			( empty( $cities_region ) || 'null' == $cities_region )
		) {
			return $output;
		}


		if( $cities_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="" data-region="" data-ex_filter="' . $cities_input . '" data-ex_region="' . $cities_region . '">' . $output . '</div>';	
		}

		return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $cities_input . '" data-region="' . $cities_region . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
	}


	/*
		depreaciate methods
		old params ( v: 3.4.0.0 )
	*/

	/**
	 * Conditional if render
	 *
	 * @return bool
	 */
	static function is_render_deprecated( $attrs = [] ) {
		
		$in_cities = isset( $attrs['geot_in_cities'] ) ?  trim( $attrs['geot_in_cities'] ) : '';
		$ex_cities = isset( $attrs['geot_ex_cities'] ) ?  trim( $attrs['geot_ex_cities'] ) : '';

		$in_regions = isset( $attrs['geot_in_region_cities'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_in_region_cities'] ) : [];
		$ex_regions = isset( $attrs['geot_ex_region_cities'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_ex_region_cities'] ) : [];

		if ( empty( $in_cities ) && empty( $ex_cities ) &&
			count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}

		return geot_target_city( $in_cities, $in_regions, $ex_cities, $ex_regions );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_render_deprecated( $attrs, $output ) {
		$in_countries = isset( $attrs['geot_in_countries'] ) ?  trim( $attrs['geot_in_countries'] ) : '';
		$ex_countries = isset( $attrs['geot_ex_countries'] ) ?  trim( $attrs['geot_ex_countries'] ) : '';
		$in_regions = isset( $attrs['geot_in_region_countries'] ) ? $attrs['geot_in_region_countries']  : '';
		$ex_regions = isset( $attrs['geot_ex_region_countries'] ) ? $attrs['geot_ex_region_countries']  : '';

		if( empty( $in_countries ) && empty( $ex_countries ) &&
			( empty( $in_regions ) || 'null' == $in_regions ) &&
			( empty( $ex_regions ) || 'null' == $ex_regions )
		) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions . '">' . $output . '</div>';
	}
	
}