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
class Fusion_GeoZip {

	/**
	 * Geot fields to Zip
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields = [
			[
				'type'			=> 'radio_button_set',
				'heading'		=> esc_attr__( 'Zips Visibility', 'geot' ),
				'description'	=> esc_attr__( 'Choose visibility.', 'geot' ),
				'param_name'	=> 'zipcodes_mode',
				'default'		=> 'include',
				'value'	=> [
					'include'	=> esc_attr__( 'Show', 'geot' ),
					'exclude'	=> esc_attr__( 'Hide', 'geot' ),
				],
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Zips', 'geot' ),
				'description'	=> esc_attr__( 'Type Zip codes separated by commas.', 'geot' ),
				'param_name'	=> 'zipcodes_input',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'Zip Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'zipcodes_region',
				'value'			=> GeotWP_Fusion::get_regions( 'zip' ),
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
		return isset( $settings['geot_in_zips'] ) || isset( $settings['geot_ex_zips'] ) || isset( $settings['geot_in_region_zips'] ) || isset( $settings['geot_ex_region_zips'] );
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

		$zipcodes_mode = isset( $attrs['zipcodes_mode'] ) ? trim( $attrs['zipcodes_mode'] ) : 'include';

		$zipcodes_input = isset( $attrs['zipcodes_input'] ) ?  trim( $attrs['zipcodes_input'] ) : '';
		
		$zipcodes_region = isset( $attrs['zipcodes_region'] ) ? GeotWP_Fusion::clean_region( $attrs['zipcodes_region'] ) : [];

		if( empty( $zipcodes_input ) && count( $zipcodes_region ) == 0 )
			return true;

		if( $zipcodes_mode == 'exclude' )
			return geot_target_zip( '', '', $zipcodes_input, $zipcodes_region );

		return geot_target_zip( $zipcodes_input, $zipcodes_region );
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

		$zipcodes_mode = isset( $attrs['zipcodes_mode'] ) ? trim( $attrs['zipcodes_mode'] ) : 'include';
		$zipcodes_input = isset( $attrs['zipcodes_input'] ) ?  trim( $attrs['zipcodes_input'] ) : '';
		$zipcodes_region = isset( $attrs['zipcodes_region'] ) ? $attrs['zipcodes_region'] : '';

		if( empty( $zipcodes_input ) &&
		    ( empty( $zipcodes_region ) || 'null' == $zipcodes_region )
		) {
			return $output;
		}


		if( $zipcodes_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="" data-region="" data-ex_filter="' . $zipcodes_input . '" data-ex_region="' . $zipcodes_region . '">' . $output . '</div>';
		}

		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $zipcodes_input . '" data-region="' . $zipcodes_region . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
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
		
		$in_zips = isset( $attrs['geot_in_zips'] ) ?  trim( $attrs['geot_in_zips'] ) : '';
		$ex_zips = isset( $attrs['geot_ex_zips'] ) ?  trim( $attrs['geot_ex_zips'] ) : '';
        
		$in_regions = isset( $attrs['geot_in_region_zips'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_in_region_zips'] ) : [];
		$ex_regions = isset( $attrs['geot_ex_region_zips'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_ex_region_zips'] ) : [];

		if( empty( $in_zips ) && empty( $ex_zips ) &&
			count( $in_regions ) == 0 && count( $ex_regions ) == 0
        ) {
			return true;
		}

		return geot_target_zip( $in_zips, $in_regions, $ex_zips, $ex_regions );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_render_deprecated( $attrs, $output ) {
		$in_zips = isset( $attrs['geot_in_zips'] ) ?  trim( $attrs['geot_in_zips'] ) : '';
		$ex_zips = isset( $attrs['geot_ex_zips'] ) ?  trim( $attrs['geot_ex_zips'] ) : '';

		$in_regions = isset( $attrs['geot_in_region_zips'] ) ? $attrs['geot_in_region_zips'] : '';
		$ex_regions = isset( $attrs['geot_ex_region_zips'] ) ? $attrs['geot_ex_region_zips'] : '';

		if( empty( $in_zips ) && empty( $ex_zips ) &&
			( empty( $in_regions ) || 'null' == $in_regions ) &&
			( empty( $ex_regions ) || 'null' == $ex_regions )
		) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zips . '" data-region="' . $in_regions . '" data-ex_filter="' . $ex_zips . '" data-ex_region="' . $ex_regions . '">' . $output . '</div>';
	}

}