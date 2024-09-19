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
class Elementor_GeoCountry {

	/**
	 *
	 * Get Fields in the Elementor Admin
	 *
	 * @param Class $control
	 *
	 */
	static function get_fields( $control ) {

		$control->start_controls_section( 'countries_section', [
			'label'	=> esc_html__( 'Countries Settings', 'geot' ),
			'tab'	=> 'geot',
		] );

		$control->add_control( 'countries_mode', [
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
			'default'	=> 'include',
			'toggle'	=> true,
		] );

		$control->add_control( 'countries_help', [
			'type'				=> \Elementor\Controls_Manager::RAW_HTML,
			'raw'				=> esc_html__( 'Type country names or ISO codes separated by commas.', 'geot' ),
			'content_classes'	=> 'elementor-descriptor',
		] );


		$control->add_control( 'countries_input', [
			'label'			=> esc_html__( 'Countries', 'geot' ),
			'type'			=> \Elementor\Controls_Manager::TEXT,
			'input_type'	=> 'text',
		] );


		$control->add_control( 'countries_regions', [
			'label'    => esc_html__( 'Regions', 'geot' ),
			'type'     => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
			'default'  => '',
			'options'  => GeotWP_Elementor::get_regions( 'countries' ),
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
		return !empty( $settings['in_countries'] ) || !empty( $settings['ex_countries'] ) || !empty( $settings['in_regions'] ) || !empty( $settings['ex_regions'] );
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

		$countries_mode = isset( $settings['countries_mode'] ) ? trim( $settings['countries_mode'] ) : 'include';

		$countries_input = isset( $settings['countries_input'] ) ? trim( $settings['countries_input'] ) : '';
		
		$countries_regions = isset( $settings['countries_regions'] ) && is_array( $settings['countries_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['countries_regions'] ), 'countries' ) : [];

		// If it is empty
		if ( empty( $countries_input ) && empty( $countries_regions ) )
			return true;

		if( $countries_mode == 'exclude' )
			return geot_target( '', '', $countries_input, $countries_regions );

		return geot_target( $countries_input, $countries_regions );		
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

		$countries_regions_i = '';

		$countries_mode = isset( $settings['countries_mode'] ) ? trim( $settings['countries_mode'] ) : 'include';

		$countries_input = isset( $settings['countries_input'] ) ? trim( $settings['countries_input'] ) : '';
		
		$countries_regions = isset( $settings['countries_regions'] ) && is_array( $settings['countries_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['countries_regions'] ), 'countries' ) : [];

		if( empty( $countries_input ) && empty( $countries_regions ) )
			return;

		if( is_array( $countries_regions ) && count( $countries_regions ) > 0 )
			$countries_regions_i = implode( ',', $countries_regions );


		if( $countries_mode == 'exclude' ) {
			echo '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="" data-region="" data-ex_filter="' . $countries_input . '" data-ex_region="' . $countries_regions_i . '">';
		} else {
			echo '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $countries_input . '" data-region="' . $countries_regions_i . '" data-ex_filter="" data-ex_region="">';
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


		$countries_mode = isset( $settings['countries_mode'] ) ? trim( $settings['countries_mode'] ) : 'include';

		$countries_input = isset( $settings['countries_input'] ) ? trim( $settings['countries_input'] ) : '';
		
		$countries_regions = isset( $settings['countries_regions'] ) && is_array( $settings['countries_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['countries_regions'] ), 'countries' ) : [];

		if( empty( $countries_input ) && empty( $countries_regions ) )
			return;

		echo '</div>';
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
	static function is_render_deprecated( $settings = [] ) {
		
		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_regions = isset( $settings['in_regions'] ) && is_array( $settings['in_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions'] ), 'countries' ) : [];
        $ex_regions = isset( $settings['ex_regions'] ) && is_array( $settings['ex_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions'] ), 'countries' ) : [];

		if ( empty( $in_countries ) && empty( $ex_countries ) &&
             empty( $in_regions ) && empty( $ex_regions )
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
	static function ajax_before_render_deprecated( $settings = [] ) {

		$in_regions_i = $ex_regions_i = '';
	
		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_regions = isset( $settings['in_regions'] ) && is_array( $settings['in_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions'] ), 'countries' ) : [];
		$ex_regions = isset( $settings['ex_regions'] ) && is_array( $settings['ex_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions'] ), 'countries' ) : [];


		if ( empty( $in_countries ) && empty( $ex_countries ) &&
			empty( $in_regions ) && empty( $ex_regions )
		) {
			return;
		}

		if( is_array( $in_regions ) && count( $in_regions ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions );
		}

		if( is_array( $ex_regions ) && count( $ex_regions ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions );
		}

		echo '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $in_countries . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_countries . '" data-ex_region="' . $ex_regions_i . '">';
	}



	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_after_render_deprecated( $settings = [] ) {

		$in_countries = isset( $settings['in_countries'] ) ? trim( $settings['in_countries'] ) : '';
		$ex_countries = isset( $settings['ex_countries'] ) ? trim( $settings['ex_countries'] ) : '';

		$in_regions = isset($settings['in_regions']) && is_array( $settings['in_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions'] ), 'countries' ) : [];
		$ex_regions = isset($settings['ex_regions']) && is_array( $settings['ex_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions'] ), 'countries' ) : [];


		if ( empty( $in_countries ) && empty( $ex_countries ) &&
             empty( $in_regions ) && empty( $ex_regions )
        ) {
			return;
		}

		echo '</div>';
	}
}
?>