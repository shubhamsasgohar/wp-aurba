<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Elementor Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class Elementor_GeoState {


	/**
	 *
	 * Get Fields in the Elementor Admin
	 *
	 * @param Class $control
	 *
	 */
	static function get_fields( $control ) {

		$control->start_controls_section( 'states_section',	[
			'label'	=> esc_html__( 'States Settings', 'geot' ),
			'tab'	=> 'geot',
		] );

		$control->add_control( 'states_mode', [
			'label'			=> esc_html__( 'Visibility', 'geot' ),
			'type'			=> \Elementor\Controls_Manager::CHOOSE,
			'options' => [
				'include' => [
					'title'	=> esc_html__( 'Show', 'geot' ),
					'icon'	=> 'eicon eicon-preview-medium',
				],
				'exclude' => [
					'title'	=> esc_html__( 'Hide', 'geot' ),
					'icon'	=> 'eicon eicon-ban',
				],
			],
			'default' => 'include',
			'toggle' => true,
		] );

		$control->add_control( 'states_help', [
			'type'				=> \Elementor\Controls_Manager::RAW_HTML,
			'raw'				=> esc_html__( 'Type state names or ISO codes separated by commas.', 'geot' ),
			'content_classes'	=> 'elementor-descriptor',
		] );

		$control->add_control( 'states_input', [
			'label'			=> esc_html__( 'States', 'geot' ),
			'type'			=> \Elementor\Controls_Manager::TEXT,
			'input_type'	=> 'text',
		] );

		$control->add_control( 'states_regions', [
			'label'		=> esc_html__( 'Regions', 'geot' ),
			'type'		=> \Elementor\Controls_Manager::SELECT2,
			'multiple'	=> true,
			'default'	=> '',
			'options'	=> GeotWP_Elementor::get_regions( 'states' ),
		] );

		$control->end_controls_section();
	}


	/**
	 * Conditional if it apply deprecated method
	 * 
	 * @param  array   $settings
	 * @return boolean
	 */
	static function is_deprecated( $settings = [] ) {
		return !empty( $settings['in_states'] ) || !empty( $settings['ex_states'] ) || !empty( $settings['in_regions_states'] ) || !empty( $settings['ex_regions_states'] );
	}

	/**
	 *
	 * Conditional if it apply a render
	 *
	 * @param Array $settings
	 *
	 */
	static function is_render( $settings = [] ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::is_render_deprecated( $settings );

		$states_mode = isset( $settings['states_mode'] ) ? trim( $settings['states_mode'] ) : 'include';

		$states_input = isset( $settings['states_input'] ) ? trim( $settings['states_input'] ) : '';
		
		$states_regions = isset( $settings['states_regions'] ) && is_array( $settings['states_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['states_regions'] ), 'states' ) : [];

		if ( empty( $states_input ) && empty( $states_regions ) )
			return true;

		if( $states_mode == 'exclude' )
			return geot_target_state( '', '', $states_input, $states_regions );

		return geot_target_state( $states_input, $states_regions );
	}


	/**
	 *
	 * To Ajax mode, print HTML before
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_before_render( $settings = [] ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::ajax_before_render_deprecated( $settings );

		$states_regions_i = '';

		$states_mode = isset( $settings['states_mode'] ) ? trim( $settings['states_mode'] ) : 'include';

		$states_input = isset( $settings['states_input'] ) ? trim( $settings['states_input'] ) : '';
		
		$states_regions = isset( $settings['states_regions'] ) && is_array( $settings['states_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['states_regions'] ), 'states' ) : [];

		if ( empty( $states_input ) && empty( $states_regions ) )
			return;
		
		if ( is_array( $states_regions ) && count( $states_regions ) > 0 )
			$states_regions_i = implode( ',', $states_regions );


		if( $states_mode == 'exclude' ) {
			echo '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="" data-region="" data-ex_filter="' . $states_input . '" data-ex_region="' . $states_regions_i . '">';
		} else {
			echo '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $states_input . '" data-region="' . $states_regions_i . '" data-ex_filter="" data-ex_region="">';
		}
	}


	/**
	 *
	 * To Ajax mode, print HTML after
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_after_render( $settings = [] ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $settings ) )
			return self::ajax_after_render_deprecated( $settings );

		$states_mode = isset( $settings['states_mode'] ) ? trim( $settings['states_mode'] ) : 'include';

		$states_input = isset( $settings['states_input'] ) ? trim( $settings['states_input'] ) : '';
		
		$states_regions = isset( $settings['states_regions'] ) && is_array( $settings['states_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['states_regions'] ), 'states' ) : [];

		if ( empty( $states_input ) && empty( $states_regions ) )
			return;

		echo '</div>';
	}



	/*
		depreaciate methods
		old params ( v: 3.4.0.0 )
	*/


	/**
	 *
	 * Conditional if it apply a render
	 *
	 * @param Array $settings
	 *
	 */
	static function is_render_deprecated( $settings = [] ) {

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_regions_states = isset( $settings['in_regions_states'] ) && is_array( $settings['in_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_states'] ), 'states' ) : [];
		$ex_regions_states = isset( $settings['ex_regions_states'] ) && is_array( $settings['ex_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_states'] ), 'states' ) : [];

		if ( empty( $in_states ) && empty( $ex_states ) &&
			empty( $in_regions_states ) && empty( $ex_regions_states )
		) {
			return true;
		}

		return geot_target_state( $in_states, $in_regions_states, $ex_states, $ex_regions_states );
	}


	/**
	 *
	 * To Ajax mode, print HTML before
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_before_render_deprecated( $settings = [] ) {

		$in_regions_i = $ex_regions_i = '';

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_regions_states = isset( $settings['in_regions_states'] ) && is_array( $settings['in_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_states'] ), 'states' ) : [];
		$ex_regions_states = isset( $settings['ex_regions_states'] ) && is_array( $settings['ex_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_states'] ), 'states' ) : [];

		if ( empty( $in_states ) && empty( $ex_states ) &&
			empty( $in_regions_states ) && empty( $ex_regions_states )
		) {
			return;
		}

		if ( is_array( $in_regions_states ) && count( $in_regions_states ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions_states );
		}

		if ( is_array( $ex_regions_states ) && count( $ex_regions_states ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions_states );
		}

		echo '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $in_states . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_states . '" data-ex_region="' . $ex_regions_i . '">';
	}


	/**
	 *
	 * To Ajax mode, print HTML after
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_after_render_deprecated( $settings = [] ) {

		$in_states = isset( $settings['in_states'] ) ? trim( $settings['in_states'] ) : '';
		$ex_states = isset( $settings['ex_states'] ) ? trim( $settings['ex_states'] ) : '';

		$in_regions_states = is_array( $settings['in_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_states'] ), 'states' ) : [];
		$ex_regions_states = is_array( $settings['ex_regions_states'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_states'] ), 'states' ) : [];

		if ( empty( $in_states ) && empty( $ex_states ) &&
			empty( $in_regions_states ) && empty( $ex_regions_states )
		) {
			return;
		}

		echo '</div>';
	}
}

?>