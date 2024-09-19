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
class Divi_GeoState {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields['states_mode'] = [
			'label'				=> esc_html__( 'Visibility', 'geot' ),
			'type'				=> 'select',
			'options'          => [
				'include'	=> esc_html__( 'Show', 'geot' ),
				'exclude'	=> esc_html__( 'Hide', 'geot' ),
			],
			'option_category'	=> 'configuration',
			'description'		=> esc_html__( 'Choose visibility.', 'geot' ),
			'toggle_slug'		=> 'state',
			'tab_slug'			=> 'geot',
		];

		$fields['states_input'] = [
			'label'           => esc_html__( 'States', 'geot' ),
			'type'            => 'text',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Type state names or ISO codes separated by comma.', 'geot' ),
			'toggle_slug'		=> 'state',
			'tab_slug'        => 'geot',
		];

		$fields['states_region'] = [
			'label'           => esc_html__( 'Regions', 'geot' ),
			'type'            => 'multiple_checkboxes',
			'option_category' => 'configuration',
			'description'     => esc_html__( 'Choose region name to show content to.', 'geot' ),
			'options'         => GeotWP_Divi::get_regions( 'state' ),
			'toggle_slug'		=> 'state',
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

		$states_mode = isset( $settings['states_mode'] ) ? trim( $settings['states_mode'] ) : 'include';

		$states_input = isset( $settings['states_input'] ) ? trim( $settings['states_input'] ) : '';

		$states_region = isset( $settings['states_region'] ) ? trim( $settings['states_region'] ) : '';

		$states_region_i = GeotWP_Divi::format_regions( $states_region, '|', $regions );

		if( empty( $states_input ) && count( $states_region_i ) == 0 )
			return true;

		if( $states_mode == 'exclude' )
			return geot_target_state( '', '', $states_input, $states_region_i );

		return geot_target_state( $states_input, $states_region_i );
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

		$states_region_commas = '';

		$states_mode = isset( $settings['states_mode'] ) ? trim( $settings['states_mode'] ) : 'include';

		$states_input = isset( $settings['states_input'] ) ? trim( $settings['states_input'] ) : '';

		$states_region = isset( $settings['states_region'] ) ? trim( $settings['states_region'] ) : '';

		$states_region_i = GeotWP_Divi::format_regions( $states_region, '|', $regions );

		if( empty( $states_input ) && count( $states_region_i ) == 0 )
			return $output;

		if( count( $states_region_i ) > 0 )
			$states_region_commas = implode( ',', $states_region_i );

		if( $states_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="" data-region="" data-ex_filter="' . $states_input . '" data-ex_region="' . $states_region_commas . '">' . $output . '</div>';
		}

		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $states_input . '" data-region="' . $states_region_commas . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
	}


	/**
	 * Recover depreciate fields
	 * @param  array $module_props
	 * @param  array $attrs
	 * @return array
	 */
	static function recover_fields( $module_props = [], $attrs = [] ) {

		// Depreciate fields
		if( isset( $attrs['in_states'] ) )
			$module_props['in_states'] = $attrs['in_states'];

		if( isset( $attrs['ex_states'] ) )
			$module_props['ex_states'] = $attrs['ex_states'];

		if( isset( $attrs['in_region_states'] ) )
			$module_props['in_region_states'] = $attrs['in_region_states'];

		if( isset( $attrs['ex_region_states'] ) )
			$module_props['ex_region_states'] = $attrs['ex_region_states'];


		// New fields
		if( isset( $attrs['states_mode'] ) )
			$module_props['states_mode'] = $attrs['states_mode'];

		if( isset( $attrs['states_input'] ) )
			$module_props['states_input'] = $attrs['states_input'];

		if( isset( $attrs['states_region'] ) )
			$module_props['states_region'] = $attrs['states_region'];

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
		return isset( $settings['in_states'] ) || isset( $settings['ex_states'] ) || isset( $settings['in_region_states'] ) || isset( $settings['ex_region_states'] );
	}

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function is_render_deprecated( $settings, $regions ) {

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_region_states = isset( $settings['in_region_states'] ) ? trim( $settings['in_region_states'] ) : '';
		$ex_region_states = isset( $settings['ex_region_states'] ) ? trim( $settings['ex_region_states'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_states, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_states, '|', $regions );

		if ( empty( $in_states ) && empty( $ex_states ) &&
			count( $in_regions ) == 0 && count( $ex_regions ) == 0
		) {
			return true;
		}

		return geot_target_state( $in_states, $in_regions, $ex_states, $ex_regions );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render_deprecated( $settings, $regions, $output ) {

		$in_regions_commas = $ex_regions_commas = '';

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_region_states = isset( $settings['in_region_states'] ) ? trim( $settings['in_region_states'] ) : '';
		$ex_region_states = isset( $settings['ex_region_states'] ) ? trim( $settings['ex_region_states'] ) : '';

		$in_regions = GeotWP_Divi::format_regions( $in_region_states, '|', $regions );
		$ex_regions = GeotWP_Divi::format_regions( $ex_region_states, '|', $regions );

		if ( empty( $in_states ) && empty( $ex_states ) &&
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

		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_states . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}
}