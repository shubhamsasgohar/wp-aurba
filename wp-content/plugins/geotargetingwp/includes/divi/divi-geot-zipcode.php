<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Divi Geo Module
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class Divi_GeoZipcode {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields['zipcodes_mode'] = [
			'label'				=> esc_html__( 'Visibility', 'geot' ),
			'type'				=> 'select',
			'options'          => [
				'include'	=> esc_html__( 'Show', 'geot' ),
				'exclude'	=> esc_html__( 'Hide', 'geot' ),
			],
			'option_category'	=> 'configuration',
			'description'		=> esc_html__( 'Choose visibility.', 'geot' ),
			'toggle_slug'		=> 'zipcode',
			'tab_slug'			=> 'geot',
		];

		$fields['zipcodes_input'] = [
			'label'           => esc_html__( 'ZipCodes', 'geot' ),
			'type'            => 'text',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Type zip codes separated by commas.', 'geot' ),
			'toggle_slug'		=> 'zipcode',
			'tab_slug'        => 'geot',
		];

		$fields['zipcodes_region'] = [
			'label'           => esc_html__( 'Regions', 'geot' ),
			'type'            => 'multiple_checkboxes',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'         => GeotWP_Divi::get_regions( 'zip' ),
			'toggle_slug'		=> 'zipcode',
			'tab_slug'        => 'geot',
		];

		return $fields;
	}


	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function is_render( $settings, $regions ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::is_render_deprecated( $settings, $regions );

		$zipcodes_mode = isset( $settings['zipcodes_mode'] ) ? trim( $settings['zipcodes_mode'] ) : 'include';

		$zipcodes_input = isset( $settings['zipcodes_input'] ) ? trim( $settings['zipcodes_input'] ) : '';

		$zipcodes_region = isset( $settings['zipcodes_region'] ) ? trim( $settings['zipcodes_region'] ) : '';

		$zipcodes_region_i = GeotWP_Divi::format_regions( $zipcodes_region, '|', $regions );

		if( empty( $zipcodes_input ) && count( $zipcodes_region_i ) == 0 )
			return true;

		if( $zipcodes_mode == 'exclude' )
			return geot_target_zip( '', '', $zipcodes_input, $zipcodes_region_i );

		return geot_target_zip( $zipcodes_input, $zipcodes_region_i );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings, $regions, $output ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::ajax_render_deprecated( $settings, $regions, $output );

		$zipcodes_regions_commas = '';

		$zipcodes_mode = isset( $settings['zipcodes_mode'] ) ? trim( $settings['zipcodes_mode'] ) : 'include';

		$zipcodes_input = isset( $settings['zipcodes_input'] ) ? trim( $settings['zipcodes_input'] ) : '';

		$zipcodes_region = isset( $settings['zipcodes_region'] ) ? trim( $settings['zipcodes_region'] ) : '';

		$zipcodes_region_i = GeotWP_Divi::format_regions( $zipcodes_region, '|', $regions );

		if( empty( $zipcodes_input ) && count( $zipcodes_region_i ) == 0 )
			return $output;

		if( count( $zipcodes_region_i ) > 0 )
			$zipcodes_region_commas = implode( ',', $zipcodes_region_i );

		if( $zipcodes_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="" data-region="" data-ex_filter="' . $zipcodes_input . '" data-ex_region="' . $zipcodes_region_commas . '">' . $output . '</div>';
		}

		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $zipcodes_input . '" data-region="' . $zipcodes_region_commas . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
	}


	/**
	 * Recover depreciate fields
	 * @param  array $module_props
	 * @param  array $attrs
	 * @return array
	 */
	static function recover_fields( $module_props = [], $attrs = [] ) {

		// Depreciate fields
		if( isset( $attrs['in_zipcodes'] ) )
			$module_props['in_zipcodes'] = $attrs['in_zipcodes'];

		if( isset( $attrs['ex_zipcodes'] ) )
			$module_props['ex_zipcodes'] = $attrs['ex_zipcodes'];

		if( isset( $attrs['in_region_zips'] ) )
			$module_props['in_region_zips'] = $attrs['in_region_zips'];

		if( isset( $attrs['ex_region_zips'] ) )
			$module_props['ex_region_zips'] = $attrs['ex_region_zips'];


		// New fields
		if( isset( $attrs['zipcodes_mode'] ) )
			$module_props['zipcodes_mode'] = $attrs['zipcodes_mode'];

		if( isset( $attrs['zipcodes_input'] ) )
			$module_props['zipcodes_input'] = $attrs['zipcodes_input'];

		if( isset( $attrs['zipcodes_region'] ) )
			$module_props['zipcodes_region'] = $attrs['zipcodes_region'];

		return $module_props;
	}


	/*
		depreaciate methods
		old params ( v: 3.4.0.0 )
	*/

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
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function is_render_deprecated( $settings, $regions ) {

		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_region_zips = isset( $settings['in_region_zips'] ) ? trim( $settings['in_region_zips'] ) : '';
		$ex_region_zips = isset( $settings['ex_region_zips'] ) ? trim( $settings['ex_region_zips'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_zips, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_zips, '|', $regions );

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}

		return geot_target_zip( $in_zipcodes, $in_regions, $ex_zipcodes, $ex_regions );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render_deprecated( $settings, $regions, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_region_zips = isset( $settings['in_region_zips'] ) ? trim( $settings['in_region_zips'] ) : '';
		$ex_region_zips = isset( $settings['ex_region_zips'] ) ? trim( $settings['ex_region_zips'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_zips, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_zips, '|', $regions );

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return $output;
		}

		if ( count( $in_regions ) > 0 ) {
			$in_regions_commas = implode( ',', $in_regions );
		}

		if ( count( $ex_regions ) > 0 ) {
			$ex_regions_commas = implode( ',', $ex_regions );
		}

		return '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zipcodes . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_zipcodes . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}
}