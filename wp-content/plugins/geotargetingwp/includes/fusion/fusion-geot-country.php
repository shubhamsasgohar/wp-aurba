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
class Fusion_GeoCountry {

	/**
	 * Geot fields to Country
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields = [
			[
				'type'			=> 'radio_button_set',
				'heading'		=> esc_attr__( 'Country Visibility', 'geot' ),
				'description'	=> esc_attr__( 'Choose visibility.', 'geot' ),
				'param_name'	=> 'countries_mode',
				'default'		=> 'include',
				'value'	=> [
					'include'	=> esc_attr__( 'Show', 'geot' ),
					'exclude'	=> esc_attr__( 'Hide', 'geot' ),
				],
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Countries', 'geot' ),
				'description'	=> esc_attr__( 'Type country name or ISO code. Also you can write a comma separated list of countries.', 'geot' ),
				'param_name'	=> 'countries_input',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'Country Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'countries_region',
				'value'			=> GeotWP_Fusion::get_regions( 'country' ),
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
		return isset( $settings['geot_in_countries'] ) || isset( $settings['geot_ex_countries'] ) || isset( $settings['geot_in_region_countries'] ) || isset( $settings['geot_ex_region_countries'] );
	}


	/**
	 * Conditional if render
	 *
	 * @return bool
	 */
	static function is_render( $attrs = [] ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $attrs ) )
			return self::is_render_deprecated( $attrs );

		$countries_mode = isset( $attrs['countries_mode'] ) ? trim( $attrs['countries_mode'] ) : 'include';

		$countries_input = isset( $attrs['countries_input'] ) ?  trim( $attrs['countries_input'] ) : '';
		
		$countries_region = isset( $attrs['countries_region'] ) ? GeotWP_Fusion::clean_region( $attrs['countries_region'] ) : [];

		if ( empty( $countries_input ) && count( $countries_region ) == 0 )
			return true;

		if( $countries_mode == 'exclude' )
			return geot_target( '', '', $countries_input, $countries_region );

		return geot_target( $countries_input, $countries_region );
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

		$countries_mode = isset( $attrs['countries_mode'] ) ? trim( $attrs['countries_mode'] ) : 'include';

		$countries_input = isset( $attrs['countries_input'] ) ?  trim( $attrs['countries_input'] ) : '';
		
		$countries_region = isset( $attrs['countries_region'] ) ? $attrs['countries_region'] : '';


		if( empty( $countries_input ) &&
			( empty( $countries_region ) || 'null' == $countries_region ) 
		) {
			return $output;
		}


		if( $countries_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="" data-region="" data-ex_filter="' . $countries_input . '" data-ex_region="' . $countries_region . '">' . $output . '</div>';
		}

		return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $countries_input . '" data-region="' . $countries_region . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
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
		
		$in_countries = isset( $attrs['geot_in_countries'] ) ?  trim( $attrs['geot_in_countries'] ) : '';
		$ex_countries = isset( $attrs['geot_ex_countries'] ) ?  trim( $attrs['geot_ex_countries'] ) : '';

		$in_regions = isset( $attrs['geot_in_region_countries'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_in_region_countries'] ) : [];
		$ex_regions = isset( $attrs['geot_ex_region_countries'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_ex_region_countries'] ) : [];

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
			count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}

		return geot_target( $in_countries, $in_regions, $ex_countries, $ex_regions );
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