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
class Elementor_GeoZipcode {

	/**
	 * Get Fields in the Elementor Admin
	 *
	 * @param Class $control
	 *
	 */
	static function get_fields( $control ) {

		$control->start_controls_section( 'zipcodes_section', [
			'label'	=> esc_html__( 'ZipCodes Settings', 'geot' ),
			'tab'	=> 'geot',
		] );

		$control->add_control( 'zipcodes_mode', [
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

		$control->add_control( 'zipcodes_help', [
			'type'				=> \Elementor\Controls_Manager::RAW_HTML,
			'raw'				=> esc_html__( 'Type zip codes separated by commas.', 'geot' ),
			'content_classes'	=> 'elementor-descriptor',
		] );

		$control->add_control( 'zipcodes_input', [
			'label'			=> esc_html__( 'ZipCodes', 'geot' ),
			'type'			=> \Elementor\Controls_Manager::TEXT,
			'input_type'	=> 'text',
		] );

		$control->add_control( 'zipcodes_regions', [
			'label'		=> esc_html__( 'Regions', 'geot' ),
			'type'		=> \Elementor\Controls_Manager::SELECT2,
			'multiple'	=> true,
			'default'	=> '',
			'options'	=> GeotWP_Elementor::get_regions( 'zips' ),
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
		return !empty( $settings['in_zipcodes'] ) || !empty( $settings['ex_zipcodes'] ) || !empty( $settings['in_regions_zips'] ) || !empty( $settings['ex_regions_zips'] );
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

		$zipcodes_mode = isset( $settings['zipcodes_mode'] ) ? trim( $settings['zipcodes_mode'] ) : 'include';

		$zipcodes_input = isset( $settings['zipcodes_input'] ) ? trim( $settings['zipcodes_input'] ) : '';
		
		$zipcodes_regions = isset( $settings['zipcodes_regions'] ) && is_array( $settings['zipcodes_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['zipcodes_regions'] ), 'zips' ) : [];


		if ( empty( $zipcodes_input ) && empty( $zipcodes_regions ) )
			return true;

		if( $zipcodes_mode == 'exclude' )
			return geot_target_zip( '', '', $zipcodes_input, $zipcodes_regions );

		return geot_target_zip( $zipcodes_input, $zipcodes_regions );
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

		$zipcodes_regions_i = '';

		$zipcodes_mode = isset( $settings['zipcodes_mode'] ) ? trim( $settings['zipcodes_mode'] ) : 'include';

		$zipcodes_input = isset( $settings['zipcodes_input'] ) ? trim( $settings['zipcodes_input'] ) : '';
		
		$zipcodes_regions = isset( $settings['zipcodes_regions'] ) && is_array( $settings['zipcodes_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['zipcodes_regions'] ), 'zips' ) : [];
		
		if ( empty( $zipcodes_input ) && empty( $zipcodes_regions ) )
			return;

		if ( is_array( $zipcodes_regions ) && count( $zipcodes_regions ) > 0 )
			$zipcodes_regions_i = implode( ',', $zipcodes_regions );

		if( $zipcodes_mode == 'exclude' ) {
			echo '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="" data-region="" data-ex_filter="' . $zipcodes_input . '" data-ex_region="' . $zipcodes_regions_i . '">';
		} else {
			echo '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $zipcodes_input . '" data-region="' . $zipcodes_regions_i . '" data-ex_filter="" data-ex_region="">';
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

		$zipcodes_mode = isset( $settings['zipcodes_mode'] ) ? trim( $settings['zipcodes_mode'] ) : 'include';

		$zipcodes_input = isset( $settings['zipcodes_input'] ) ? trim( $settings['zipcodes_input'] ) : '';
		
		$zipcodes_regions = isset( $settings['zipcodes_regions'] ) && is_array( $settings['zipcodes_regions'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['zipcodes_regions'] ), 'zips' ) : [];
		
		if ( empty( $zipcodes_input ) && empty( $zipcodes_regions ) )
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

		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_regions_zips = isset( $settings['in_regions_zips'] ) && is_array( $settings['in_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_zips'] ) , 'zips' ): [];
		$ex_regions_zips = isset( $settings['ex_regions_zips'] ) && is_array( $settings['ex_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_zips'] ) , 'zips') : [];

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			empty( $in_regions_zips ) && empty( $ex_regions_zips )
		) {
			return true;
		}
		
		return geot_target_zip( $in_zipcodes, $in_regions_zips, $ex_zipcodes, $ex_regions_zips );
	}

	/**
	 *
	 * To Ajax mode, print HTML before
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_before_render_deprecated( $settings = [] ) {

		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_regions_zips = isset( $settings['in_regions_zips'] ) && is_array( $settings['in_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_zips'] ), 'zips' ): [];
		
		$ex_regions_zips = isset( $settings['ex_regions_zips'] ) && is_array( $settings['ex_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_zips'] ), 'zips' ): [];

		$in_regions_i = $ex_regions_i = '';
		
		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			empty( $in_regions_zips ) && empty( $ex_regions_zips )
		) {
			return;
		}

		if( is_array( $in_regions_zips ) && count( $in_regions_zips ) > 0 ) {
			$in_regions_i = implode( ',', $in_regions_zips );
		}

		if( is_array( $ex_regions_zips ) && count( $ex_regions_zips ) > 0 ) {
			$ex_regions_i = implode( ',', $ex_regions_zips );
		}

		echo '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $in_zipcodes . '" data-region="' . $in_regions_i . '" data-ex_filter="' . $ex_zipcodes . '" data-ex_region="' . $ex_regions_i . '">';
	}


	/**
	 *
	 * To Ajax mode, print HTML after
	 *
	 * @param Array $settings
	 *
	 */
	static function ajax_after_render_deprecated( $settings = [] ) {

		$in_zipcodes = isset( $settings['in_zipcodes'] ) ? trim( $settings['in_zipcodes'] ) : '';
		$ex_zipcodes = isset( $settings['ex_zipcodes'] ) ? trim( $settings['ex_zipcodes'] ) : '';

		$in_regions_zips = is_array( $settings['in_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['in_regions_zips'] ), 'zips' ) : [];
		$ex_regions_zips = is_array( $settings['ex_regions_zips'] ) ? GeotWP_Elementor::filter_regions( array_map( 'trim', $settings['ex_regions_zips'] ), 'zips' ) : [];

		if ( empty( $in_zipcodes ) && empty( $ex_zipcodes ) &&
			empty( $in_regions_zips ) && empty( $ex_regions_zips )
		) {
			return;
		}

		echo '</div>';
	}

}

?>