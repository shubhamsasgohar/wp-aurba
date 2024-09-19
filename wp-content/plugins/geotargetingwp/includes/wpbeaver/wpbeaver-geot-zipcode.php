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
class WPBeaver_GeoZipcode {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$section = [

			'title' => esc_html__( 'Geo ZipCodes', 'geot' ),
			'fields' => [
				'zipcodes_mode' => [
					'type'			=> 'select',
					'multi-select'	=> false,
					'label'			=> esc_html__( 'Visibility', 'Geot' ),
					'options'		=> [
						'include'	=> esc_html__( 'Show', 'geot' ),
						'exclude'	=> esc_html__( 'Hide', 'geot' )
					],
					'help' => esc_html__( 'Choose visibility.', 'geot' ),
				],
				'zipcodes_input' => [
					'type'	=> 'text',
					'label'	=> esc_html__( 'ZipCodes', 'Geot' ),
					'help'	=> esc_html__( 'Type zip codes separated by commas.', 'geot' ),
				],
				'zipcodes_region' => [
					'type'			=> 'select',
					'multi-select'	=> true,
					'label'			=> esc_html__( 'Regions', 'Geot' ),
					'options'		=> GeotWP_WPBeaver::get_regions( 'zip' ),
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
		return isset( $settings['in_zipcodes'] ) || isset( $settings['ex_zipcodes'] ) || isset( $settings['in_region_zips'] ) || isset( $settings['ex_region_zips'] );
	}


	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render( $settings ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::is_render_deprecated( $settings );

		$zipcodes_mode = isset( $settings['zipcodes_mode'] ) ? trim( $settings['zipcodes_mode'] ) : '';

		$zipcodes_input = isset( $settings['zipcodes_input'] ) ? trim( $settings['zipcodes_input'] ) : '';

		$zipcodes_region = isset( $settings['zipcodes_region'] ) && is_array( $settings['zipcodes_region'] ) ? array_map( 'trim', $settings['zipcodes_region'] ) : [];

		$zipcodes_region_i = !empty( $zipcodes_region ) &&  !empty( $zipcodes_region[0] ) ? $zipcodes_region  : [];

		if ( empty( $zipcodes_input ) && count( $zipcodes_region_i ) == 0 )
			return true;

		if( $zipcodes_mode == 'exclude' ) {
			return geot_target_zip( '', '', $zipcodes_input, $zipcodes_region_i );
		}

		return geot_target_zip( $zipcodes_input, $zipcodes_region_i );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings, $output ) {

		$zipcodes_region_commas = '';

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::ajax_render_deprecated( $settings, $output );

		$zipcodes_mode = isset( $settings['zipcodes_mode'] ) ? trim( $settings['zipcodes_mode'] ) : '';

		$zipcodes_input = isset( $settings['zipcodes_input'] ) ? trim( $settings['zipcodes_input'] ) : '';

		$zipcodes_region = isset( $settings['zipcodes_region'] ) && is_array( $settings['zipcodes_region'] ) ? array_map( 'trim', $settings['zipcodes_region'] ) : [];

		$zipcodes_region_i = !empty( $zipcodes_region ) &&  !empty( $zipcodes_region[0] ) ? $zipcodes_region  : [];


		if ( empty( $zipcodes_input ) && count( $zipcodes_region_i ) == 0 )
			return $output;


		if ( count( $zipcodes_region_i ) > 0 )
			$zipcodes_region_commas = implode( ',', $zipcodes_region_i );

		
		if( $zipcodes_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="" data-region="" data-ex_filter="' . $zipcodes_input . '" data-ex_region="' . $zipcodes_region_commas . '">' . $output . '</div>';
		}

		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $zipcodes_input . '" data-region="' . $zipcodes_region_commas . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
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
	static function is_render_deprecated( $settings ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_region_zips = isset( $settings['in_region_zips'] ) && is_array( $settings['in_region_zips'] ) ? array_map( 'trim', $settings['in_region_zips'] ) : [];
		$ex_region_zips = isset( $settings['ex_region_zips'] ) && is_array( $settings['ex_region_zips'] ) ? array_map( 'trim', $settings['ex_region_zips'] ) : [];

		$in_region_zips = !empty( $in_region_zips ) &&  !empty( $in_region_zips[0] ) ? $in_region_zips  : [];
		$ex_region_zips = !empty( $ex_region_zips ) &&  !empty( $ex_region_zips[0] ) ? $ex_region_zips  : [];

		if( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			count( $in_region_zips ) == 0 && count( $ex_region_zips ) == 0
		) {
			return true;
		}

		return geot_target_zip( $in_zipcodes, $in_region_zips, $ex_zipcodes, $ex_region_zips );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render_deprecated( $settings, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_region_zips = isset( $settings['in_region_zips'] ) && is_array( $settings['in_region_zips'] ) ? array_map( 'trim', $settings['in_region_zips'] ) : [];
		$ex_region_zips = isset( $settings['ex_region_zips'] ) && is_array( $settings['ex_region_zips'] ) ? array_map( 'trim', $settings['ex_region_zips'] ) : [];

		$in_region_zips = !empty( $in_region_zips ) &&  !empty( $in_region_zips[0] ) ? $in_region_zips  : [];
		$ex_region_zips = !empty( $ex_region_zips ) &&  !empty( $ex_region_zips[0] ) ? $ex_region_zips  : [];

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			count( $in_region_zips ) == 0 && count( $ex_region_zips ) == 0
		) {
			return $output;
		}

		if ( count( $in_region_zips ) > 0 ) {
			$in_regions_commas = implode( ',', $in_region_zips );
		}

		if ( count( $ex_region_zips ) > 0 ) {
			$ex_regions_commas = implode( ',', $ex_region_zips );
		}

		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zipcodes . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_zipcodes . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}
}