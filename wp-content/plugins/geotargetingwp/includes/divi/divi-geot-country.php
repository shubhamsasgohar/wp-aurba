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
class Divi_GeoCountry {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields['countries_mode'] = [
			'label'				=> esc_html__( 'Visibility', 'geot' ),
			'type'				=> 'select',
			'options'          => [
				'include'	=> esc_html__( 'Show', 'geot' ),
				'exclude'	=> esc_html__( 'Hide', 'geot' ),
			],
			'option_category'	=> 'configuration',
			'description'		=> esc_html__( 'Choose visibility.', 'geot' ),
			'toggle_slug'		=> 'country',
			'tab_slug'			=> 'geot',
		];

		$fields['countries_input'] = [
			'label'				=> esc_html__( 'Countries', 'geot' ),
			'type'				=> 'text',
			'option_category'	=> 'configuration',
			'description'		=> esc_html__( 'Type country names or ISO codes separated by comma.', 'geot' ),
			'toggle_slug'		=> 'country',
			'tab_slug'			=> 'geot',
		];

		$fields['countries_region'] = [
			'label'				=> esc_html__( 'Regions', 'geot' ),
			'type'				=> 'multiple_checkboxes',
			'option_category'	=> 'configuration',
			'description'		=> esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'			=> GeotWP_Divi::get_regions( 'country' ),
			'option_category'	=> 'configuration',
			'toggle_slug'		=> 'country',
			'tab_slug'			=> 'geot',
		];

		return $fields;
	}


	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render( $settings, $regions ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::is_render_deprecated( $settings, $regions );

		$countries_mode = isset( $settings['countries_mode'] ) ? trim( $settings['countries_mode'] ) : 'include';

		$countries_input = isset( $settings['countries_input'] ) ? trim( $settings['countries_input'] ) : '';
		
		$countries_region = isset( $settings['countries_region'] ) ? trim( $settings['countries_region'] ) : '';
		
		$countries_region_i = GeotWP_Divi::format_regions( $countries_region, '|', $regions );

		if( empty( $countries_input ) && count( $countries_region_i ) == 0 )
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
	static function ajax_render( $settings, $regions, $output ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::ajax_render_deprecated( $settings, $regions, $output );

		$countries_region_commas = '';

		$countries_mode = isset( $settings['countries_mode'] ) ? trim( $settings['countries_mode'] ) : 'include';

		$countries_input = isset( $settings['countries_input'] ) ? trim( $settings['countries_input'] ) : '';
		
		$countries_region = isset( $settings['countries_region'] ) ? trim( $settings['countries_region'] ) : '';
		
		$countries_region_i = GeotWP_Divi::format_regions( $countries_region, '|', $regions );

		if( empty( $countries_input ) && count( $countries_region_i ) == 0 )
			return $output;

		if ( count( $countries_region_i ) > 0 )
			$countries_region_commas = implode( ',', $countries_region_i );

		if( $countries_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="" data-region="" data-ex_filter="' . $countries_input . '" data-ex_region="' . $countries_region_commas . '">' . $output . '</div>';
		}

		return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $countries_input . '" data-region="' . $countries_region_commas . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
	}



	/**
	 * Recover depreciate fields
	 * @param  array $module_props
	 * @param  array $attrs
	 * @return array
	 */
	static function recover_fields( $module_props = [], $attrs = [] ) {

		// Depreciate fields
		if( isset( $attrs['in_countries'] ) )
			$module_props['in_countries'] = $attrs['in_countries'];

		if( isset( $attrs['ex_countries'] ) )
			$module_props['ex_countries'] = $attrs['ex_countries'];

		if( isset( $attrs['in_regions'] ) )
			$module_props['in_regions'] = $attrs['in_regions'];

		if( isset( $attrs['ex_regions'] ) )
			$module_props['ex_regions'] = $attrs['ex_regions'];


		// New fields
		if( isset( $attrs['countries_mode'] ) )
			$module_props['countries_mode'] = $attrs['countries_mode'];

		if( isset( $attrs['countries_input'] ) )
			$module_props['countries_input'] = $attrs['countries_input'];

		if( isset( $attrs['countries_region'] ) )
			$module_props['countries_region'] = $attrs['countries_region'];

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
		return isset( $settings['in_countries'] ) || isset( $settings['ex_countries'] ) || isset( $settings['in_regions'] ) || isset( $settings['ex_regions'] );
	}

	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render_deprecated( $settings, $regions ) {

		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_region_countries = isset( $settings['in_region_countries'] ) ? trim( $settings['in_region_countries'] ) : '';
		$ex_region_countries = isset( $settings['ex_region_countries'] ) ? trim( $settings['ex_region_countries'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_countries, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_countries, '|', $regions );

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
	 * @return array
	 */
	static function ajax_render_deprecated( $settings, $regions, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_region_countries = isset( $settings['in_region_countries'] ) ? trim( $settings['in_region_countries'] ) : '';
		$ex_region_countries = isset( $settings['ex_region_countries'] ) ? trim( $settings['ex_region_countries'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_countries, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_countries, '|', $regions );

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
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

		return '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}
}