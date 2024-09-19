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
class WPBeaver_GeoState {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$section = [

			'title' => esc_html__( 'Geo States', 'geot' ),
			'fields' => [
				'states_mode' => [
					'type'			=> 'select',
					'multi-select'	=> false,
					'label'			=> esc_html__( 'Visibility', 'Geot' ),
					'options'		=> [
						'include'	=> esc_html__( 'Show', 'geot' ),
						'exclude'	=> esc_html__( 'Hide', 'geot' )
					],
					'help' => esc_html__( 'Choose visibility.', 'geot' ),
				],
				'states_input' => [
					'type'	=> 'text',
					'label'	=> esc_html__( 'States', 'Geot' ),
					'help'	=> esc_html__( 'Type state names or ISO codes separated by comma.', 'geot' ),
				],
				'states_region' => [
					'type'			=> 'select',
					'multi-select'	=> true,
					'label'			=> esc_html__( 'Regions', 'Geot' ),
					'options'		=> GeotWP_WPBeaver::get_regions( 'state' ),
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
		return isset( $settings['in_states'] ) || isset( $settings['ex_states'] ) || isset( $settings['in_region_states'] ) || isset( $settings['ex_region_states'] );
	}


	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render( $settings = [] ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::is_render_deprecated( $settings );

		$states_mode = isset( $settings['states_mode'] ) ? trim( $settings['states_mode'] ) : '';
		
		$states_input = isset( $settings['states_input'] ) ? trim( $settings['states_input'] ) : '';

		$states_region = isset( $settings['states_region'] ) && is_array( $settings['states_region'] ) ? array_map( 'trim', $settings['states_region'] ) : [];

		$states_region_i = !empty( $states_region ) &&  !empty( $states_region[0] ) ? $states_region  : [];

		if ( empty( $states_input ) && count( $states_region_i ) == 0 )
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
	static function ajax_render( $settings = [], $output = "") {

		$states_region_commas = '';

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::ajax_render_deprecated( $settings, $output );


		$states_mode = isset( $settings['states_mode'] ) ? trim( $settings['states_mode'] ) : '';
		
		$states_input = isset( $settings['states_input'] ) ? trim( $settings['states_input'] ) : '';

		$states_region = isset( $settings['states_region'] ) && is_array( $settings['states_region'] ) ? array_map( 'trim', $settings['states_region'] ) : [];

		$states_region_i = !empty( $states_region ) &&  !empty( $states_region[0] ) ? $states_region  : [];


		if ( empty( $states_input ) && count( $states_region_i ) == 0 )
			return $output;

		if ( count( $states_region_i ) > 0 )
			$states_region_commas = implode( ',', $states_region_i );


		if( $states_mode == 'exclude' ) {
			return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="" data-region="" data-ex_filter="' . $states_input . '" data-ex_region="' . $states_region_commas . '">' . $output . '</div>';	
		}

		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $states_input . '" data-region="' . $states_region_commas . '" data-ex_filter="" data-ex_region="">' . $output . '</div>';
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
	static function is_render_deprecated( $settings = [] ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_region_states = isset( $settings['in_region_states'] ) && is_array( $settings['in_region_states'] ) ? array_map( 'trim', $settings['in_region_states'] ) : [];
		$ex_region_states = isset( $settings['ex_region_states'] ) && is_array( $settings['ex_region_states'] ) ? array_map( 'trim', $settings['ex_region_states'] ) : [];

		$in_region_states = !empty( $in_region_states ) &&  !empty( $in_region_states[0] ) ? $in_region_states  : [];
		$ex_region_states = !empty( $ex_region_states ) &&  !empty( $ex_region_states[0] ) ? $ex_region_states  : [];

		if( empty( $in_states ) && empty( $ex_states ) &&
			count( $in_region_states ) == 0 && count( $ex_region_states ) == 0
		) {
			return true;
		}

		return geot_target_state( $in_states, $in_region_states, $ex_states, $ex_region_states );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render_deprecated( $settings = [], $output = "" ) {

		$in_regions_commas = $ex_regions_commas = '';

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_region_states = isset( $settings['in_region_states'] ) && is_array( $settings['in_region_states'] ) ? array_map( 'trim', $settings['in_region_states'] ) : [];
		$ex_region_states = isset( $settings['ex_region_states'] ) && is_array( $settings['ex_region_states'] ) ? array_map( 'trim', $settings['ex_region_states'] ) : [];

		$in_region_states = !empty( $in_region_states ) &&  !empty( $in_region_states[0] ) ? $in_region_states  : [];
		$ex_region_states = !empty( $ex_region_states ) &&  !empty( $ex_region_states[0] ) ? $ex_region_states  : [];

		if( empty( $in_states ) && empty( $ex_states ) &&
			count( $in_region_states ) == 0 && count( $ex_region_states ) == 0
		) {
			return $output;
		}

		if( count( $in_region_states ) > 0 ) {
			$in_regions_commas = implode( ',', $in_region_states );
		}
		
		if( count( $ex_region_states ) > 0 ) {
			$ex_regions_commas = implode( ',', $ex_region_states );
		}

		return '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-region="' . $in_regions_commas . '" data-ex_filter="' . $ex_states . '" data-ex_region="' . $ex_regions_commas . '">' . $output . '</div>';
	}
}