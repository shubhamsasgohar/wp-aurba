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
class Divi_GeoCity {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields['cities_mode'] = [
			'label'				=> esc_html__( 'Visibility', 'geot' ),
			'type'				=> 'select',
			'options'          => [
				'include'	=> esc_html__( 'Show', 'geot' ),
				'exclude'	=> esc_html__( 'Hide', 'geot' ),
			],
			'option_category'	=> 'configuration',
			'description'		=> esc_html__( 'Choose visibility.', 'geot' ),
			'toggle_slug'		=> 'city',
			'tab_slug'			=> 'geot',
		];

		$fields['cities_input'] = [
			'label'				=> esc_html__( 'Cities', 'geot' ),
			'type'				=> 'text',
			'option_category'	=> 'configuration',
			'description'		=> esc_html__( 'Type city names separated by comma.', 'geot' ),
			'toggle_slug'		=> 'city',
			'tab_slug'			=> 'geot',
		];

		$fields['cities_region'] = [
			'label'				=> esc_html__( 'Regions', 'geot' ),
			'type'				=> 'multiple_checkboxes',
			'option_category'	=> 'configuration',
			'description'		=> esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'			=> GeotWP_Divi::get_regions( 'city' ),
			'toggle_slug'		=> 'city',
			'tab_slug'			=> 'geot',
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

		$cities_mode = isset( $settings['cities_mode'] ) ? trim( $settings['cities_mode'] ) : 'include';

		$cities_input = isset( $settings['cities_input'] ) ? trim( $settings['cities_input'] ) : '';

		$cities_region = isset( $settings['cities_region'] ) ? trim( $settings['cities_region'] ) : '';

		$cities_region_i = GeotWP_Divi::format_regions( $cities_region, '|', $regions );

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
	static function ajax_render( $settings, $regions, $output ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::ajax_render_deprecated( $settings, $regions, $output );

		$cities_region_commas = '';

		$cities_mode = isset( $settings['cities_mode'] ) ? trim( $settings['cities_mode'] ) : 'include';

		$cities_input = isset( $settings['cities_input'] ) ? trim( $settings['cities_input'] ) : '';

		$cities_region = isset( $settings['cities_region'] ) ? trim( $settings['cities_region'] ) : '';

		$cities_region_i = GeotWP_Divi::format_regions( $cities_region, '|', $regions );

		if( empty( $cities_input ) && count( $cities_region_i ) == 0 )
			return $output;

		if( count( $cities_region_i ) > 0 )
			$cities_region_commas = implode( ',', $cities_region_i );
		
		if( $cities_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="" data-region="" data-ex_filter="' . $cities_input . '" data-ex_region="' . $cities_region_commas . '">' . $output . '</div>';
		}

		return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $cities_input . '" data-region="' . $cities_region_commas . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
	}



	/**
	 * Recover depreciate fields
	 * @param  array $module_props
	 * @param  array $attrs
	 * @return array
	 */
	static function recover_fields( $module_props = [], $attrs = [] ) {

		if( isset( $attrs['in_cities'] ) )
			$module_props['in_cities'] = $attrs['in_cities'];

		if( isset( $attrs['ex_cities'] ) )
			$module_props['ex_cities'] = $attrs['ex_cities'];

		if( isset( $attrs['in_region_cities'] ) )
			$module_props['in_region_cities'] = $attrs['in_region_cities'];

		if( isset( $attrs['ex_region_cities'] ) )
			$module_props['ex_region_cities'] = $attrs['ex_region_cities'];

		// New fields
		if( isset( $attrs['cities_mode'] ) )
			$module_props['cities_mode'] = $attrs['cities_mode'];

		if( isset( $attrs['cities_input'] ) )
			$module_props['cities_input'] = $attrs['cities_input'];

		if( isset( $attrs['cities_region'] ) )
			$module_props['cities_region'] = $attrs['cities_region'];

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
		return isset( $settings['in_cities'] ) || isset( $settings['ex_cities'] ) || isset( $settings['in_region_cities'] ) || isset( $settings['ex_region_cities'] );
	}


	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function is_render_deprecated( $settings, $regions ) {

		$in_cities = isset( $settings['in_cities'] ) ? trim( $settings['in_cities'] ) : '';
		$ex_cities = isset( $settings['ex_cities'] ) ? trim( $settings['ex_cities'] ) : '';

		$in_region_cities = isset( $settings['in_region_cities'] ) ? trim( $settings['in_region_cities'] ) : '';
		$ex_region_cities = isset( $settings['ex_region_cities'] ) ? trim( $settings['ex_region_cities'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_cities, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_cities, '|', $regions );

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
	 * @return array
	 */
	static function ajax_render_deprecated( $settings, $regions, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		$in_cities = isset( $settings['in_cities'] ) ? trim( $settings['in_cities'] ) : '';
		$ex_cities = isset( $settings['ex_cities'] ) ? trim( $settings['ex_cities'] ) : '';

		$in_region_cities = isset( $settings['in_region_cities'] ) ? trim( $settings['in_region_cities'] ) : '';
		$ex_region_cities = isset( $settings['ex_region_cities'] ) ? trim( $settings['ex_region_cities'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_cities, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_cities, '|', $regions );

		if( empty( $in_cities ) && empty( $ex_cities ) &&
			count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return $output;
		}

		if( count( $in_regions ) > 0 ) {
			$in_regions_commas = implode( ',', $in_regions );
		}

		if( count( $ex_regions ) > 0 ) {
			$ex_regions_commas = implode( ',', $ex_regions );
		}

		return '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $in_cities . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_cities . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}

}