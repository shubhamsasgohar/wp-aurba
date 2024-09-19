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
class Elementor_GeoCity {

	/**
	 *
	 * Get Fields in the Elementor Admin
	 *
	 * @param Class $control
	 *
	 */
	static function get_fields( $control ) {

		$control->start_controls_section( 'cities_section',	[
			'label'	=> esc_html__( 'Cities Settings', 'geot' ),
			'tab'	=> 'geot',
		] );

		$control->add_control( 'cities_mode', [
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

		$control->add_control( 'cities_help', [
			'type'				=> \Elementor\Controls_Manager::RAW_HTML,
			'raw'				=> esc_html__( 'Type city names separated by commas.', 'geot' ),
			'content_classes'	=> 'elementor-descriptor',
		] );

		$control->add_control( 'cities_input', [
			'label'			=> esc_html__( 'Cities', 'geot' ),
			'type'			=> \Elementor\Controls_Manager::TEXT,
			'input_type'	=> 'text',
		] );

		$control->add_control( 'cities_regions', [
			'label'		=> esc_html__( 'Regions', 'geot' ),
			'type'		=> \Elementor\Controls_Manager::SELECT2,
			'multiple'	=> true,
			'default'	=> '',
			'options'	=> GeotWP_Elementor::get_regions( 'cities' ),
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
		return !empty( $settings['in_cities'] ) || !empty( $settings['ex_cities'] ) || !empty( $settings['in_regions_cities'] ) || !empty( $settings['ex_regions_cities'] );
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

		$cities_mode = isset( $settings['cities_mode'] ) ? trim( $settings['cities_mode'] ) : 'include';
		$cities_input = isset( $settings['cities_input'] ) ? trim( $settings['cities_input'] ) : '';
		
		$cities_regions = isset( $settings['cities_regions'] ) && is_array( $settings['cities_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['cities_regions'] ), 'cities' ) : [];

		if ( empty( $cities_input ) && empty( $cities_regions ) )
			return true;

		if( $cities_mode == 'exclude' )
			return geot_target_city( '', '', $cities_input, $cities_regions );

		return geot_target_city( $cities_input, $cities_regions );
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

		$cities_regions_i = '';

		$cities_mode = isset( $settings['cities_mode'] ) ? trim( $settings['cities_mode'] ) : 'include';

		$cities_input = isset( $settings['cities_input'] ) ? trim( $settings['cities_input'] ) : '';
		
		$cities_regions = isset( $settings['cities_regions'] ) && is_array( $settings['cities_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['cities_regions'] ), 'cities' ) : [];


		if( empty( $cities_input ) && empty( $cities_regions ) )
			return;

		if( is_array( $cities_regions ) && count( $cities_regions ) > 0 )
			$cities_regions_i = implode( ',', $cities_regions );


		if( $cities_mode == 'exclude' ) {
			echo '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="" data-region="" data-ex_filter="' . $cities_input . '" data-ex_region="' . $cities_regions_i . '">';
		} else {
			echo '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $cities_input . '" data-region="' . $cities_regions_i . '" data-ex_filter="" data-ex_region="">';
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

		$cities_mode = isset( $settings['cities_mode'] ) ? trim( $settings['cities_mode'] ) : 'include';

		$cities_input = isset( $settings['cities_input'] ) ? trim( $settings['cities_input'] ) : '';
		
		$cities_regions = isset( $settings['cities_regions'] ) && is_array( $settings['cities_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['cities_regions'] ), 'cities' ) : [];

		if( empty( $cities_input ) && empty( $cities_regions ) )
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

		$in_cities = isset( $settings['in_cities'] ) ? trim( $settings['in_cities'] ) : '';
		$ex_cities = isset( $settings['ex_cities'] ) ? trim( $settings['ex_cities'] ) : '';

		$in_regions_cities = isset( $settings['in_regions_cities'] ) && is_array( $settings['in_regions_cities'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_cities'] ), 'cities' ) : [];
		$ex_regions_cities = isset( $settings['ex_regions_cities'] ) && is_array( $settings['ex_regions_cities'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_cities'] ), 'cities' ) : [];

		if ( empty( $in_cities ) && empty( $ex_cities ) &&
			empty( $in_regions_cities ) && empty( $ex_regions_cities )
		) {
			return true;
		}

		return geot_target_city( $in_cities, $in_regions_cities, $ex_cities, $ex_regions_cities );
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

		$in_cities = isset( $settings['in_cities'] ) ? trim( $settings['in_cities'] ) : '';
		$ex_cities = isset( $settings['ex_cities'] ) ? trim( $settings['ex_cities'] ) : '';

		$in_regions_cities = isset( $settings['in_regions_cities'] ) && is_array( $settings['in_regions_cities'] ) ?  GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_cities'] ), 'cities' ) : [];
		$ex_regions_cities = isset( $settings['ex_regions_cities'] ) && is_array( $settings['ex_regions_cities'] ) ?  GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_cities'] ), 'cities' ) : [];

		if( empty( $in_cities ) && empty( $ex_cities ) &&
			empty( $in_regions_cities ) && empty( $ex_regions_cities )
        ) {
			return;
        }

		if ( is_array( $in_regions_cities ) && count( $in_regions_cities ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions_cities );
		}

		if ( is_array( $ex_regions_cities ) && count( $ex_regions_cities ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions_cities );
		}

		echo '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $in_cities . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_cities . '" data-ex_region="' . $ex_regions_i . '">';
	}


	/**
	 *
	 * To Ajax mode, print HTML after
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_after_render_deprecated( $settings = [] ) {

		$in_cities = isset( $settings['in_cities'] ) ? trim( $settings['in_cities'] ) : '';
		$ex_cities = isset( $settings['ex_cities'] ) ? trim( $settings['ex_cities'] ) : '';

		$in_regions_cities = is_array( $settings['in_regions_cities'] ) ?  GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_cities'] ), 'cities' ) : [];
		$ex_regions_cities = is_array( $settings['ex_regions_cities'] ) ?  GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_cities'] ), 'cities' ) : [];


		if ( empty( $in_cities ) && empty( $ex_cities ) &&
			empty( $in_regions_cities ) && empty( $ex_regions_cities )
		) {
			return;
		}

		echo '</div>';
	}
}

?>