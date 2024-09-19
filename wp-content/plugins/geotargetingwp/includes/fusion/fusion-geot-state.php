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
class Fusion_GeoState {

	/**
	 * Geot fields to State
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields = [
			[
				'type'			=> 'radio_button_set',
				'heading'		=> esc_attr__( 'State Visibility', 'geot' ),
				'description'	=> esc_attr__( 'Choose visibility.', 'geot' ),
				'param_name'	=> 'states_mode',
				'default'		=> 'include',
				'value'	=> [
					'include'	=> esc_attr__( 'Show', 'geot' ),
					'exclude'	=> esc_attr__( 'Hide', 'geot' ),
				],
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'States', 'geot' ),
				'description'	=> esc_attr__( 'Type states names separated by commas.', 'geot' ),
				'param_name'	=> 'states_input',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'multiple_select',
				'heading'		=> esc_attr__( 'State Regions', 'geot' ),
				'description'	=> esc_attr__( 'Choose region name to show content to.', 'geot' ),
				'param_name'	=> 'states_region',
				'value'			=> GeotWP_Fusion::get_regions( 'state' ),
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
		return isset( $settings['geot_in_states'] ) || isset( $settings['geot_ex_states'] ) || isset( $settings['geot_in_region_states'] ) || isset( $settings['geot_ex_region_states'] );
	}


	/**
	 * Add the actual fields
	 *
	 * @return bool
	 */
	static function is_render( $attrs ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $attrs ) )
			return self::is_render_deprecated( $attrs );

		$states_mode = isset( $attrs['states_mode'] ) ? trim( $attrs['states_mode'] ) : 'include';
		$states_input = isset( $attrs['states_input'] ) ?  trim( $attrs['states_input'] ) : '';
		$states_region = isset( $attrs['states_region'] ) ? GeotWP_Fusion::clean_region( $attrs['states_region'] ) : [];

		if( empty( $states_input ) && count( $states_region ) == 0 )
			return true;

		if( $states_mode == 'exclude' )
			return geot_target_state( '', '', $states_input, $states_region );

		return geot_target_state( $states_input, $states_region );
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

		$states_mode = isset( $attrs['states_mode'] ) ? trim( $attrs['states_mode'] ) : 'include';
		$states_input = isset( $attrs['states_input'] ) ?  trim( $attrs['states_input'] ) : '';
		$states_region = isset( $attrs['states_region'] ) ? $attrs['states_region'] : '';

		if( empty( $states_input ) &&
			( empty( $states_region ) || 'null' == $states_region )
		) {
			return $output;
		}

		if( $states_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="" data-region="" data-ex_filter="' . $states_input . '" data-ex_region="' . $states_region . '">' . $output . '</div>';
		}

		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $states_input . '" data-region="' . $states_region . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
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
		
		$in_states = isset( $attrs['geot_in_states'] ) ?  trim( $attrs['geot_in_states'] ) : '';
		$ex_states = isset( $attrs['geot_ex_states'] ) ?  trim( $attrs['geot_ex_states'] ) : '';
        
		$in_regions = isset( $attrs['geot_in_region_states'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_in_region_states'] ) : [];
		$ex_regions = isset( $attrs['geot_ex_region_states'] ) ? GeotWP_Fusion::clean_region( $attrs['geot_ex_region_states'] ) : [];

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
	 * @return string
	 */
	static function ajax_render_deprecated( $attrs, $output ) {
		$in_states = isset( $attrs['geot_in_states'] ) ?  trim( $attrs['geot_in_states'] ) : '';
		$ex_states = isset( $attrs['geot_ex_states'] ) ?  trim( $attrs['geot_ex_states'] ) : '';

		$in_regions = isset( $attrs['geot_in_region_states'] ) ? $attrs['geot_in_region_states'] : '';
		$ex_regions = isset( $attrs['geot_ex_region_states'] ) ? $attrs['geot_ex_region_states'] : '';

		if( empty( $in_states ) && empty( $ex_states ) &&
			( empty( $in_regions ) || 'null' == $in_regions ) &&
			( empty( $ex_regions ) || 'null' == $ex_regions )
		) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-region="' . $in_regions . '" data-ex_filter="' . $ex_states . '" data-ex_region="' . $ex_regions . '">' . $output . '</div>';
	}
}