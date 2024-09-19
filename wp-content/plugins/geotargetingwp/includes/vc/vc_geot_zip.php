<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $zip
 * @var $exclude_zip
 * @var $this WPBakeryShortCode_VC_Geot
 */
$opts = geot_settings();

if( \GeotCore\is_builder() ) {
	echo wpb_js_remove_wpautop( $content );
} else {

	if( isset( $atts['zip'] ) || isset( $atts['exclude_zip'] ) || isset( $atts['region'] ) || isset( $atts['exclude_region'] ) ) {

		$zip = isset( $atts['zip'] ) ? trim( $atts['zip'] ) : '';
		$exclude_zip = isset( $atts['exclude_zip'] ) ? trim( $atts['exclude_zip'] ) : '';

		$region = isset( $atts['region'] ) ? trim( $atts['region'] ) : '';
		$exclude_region = isset( $atts['exclude_region'] ) ? trim( $atts['exclude_region'] ) : '';

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			echo '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $zip . '" data-region="' . $region . '" data-ex_filter="' . $exclude_zip . '" data-ex_region="' . $exclude_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
		} elseif ( geot_target_zip( $zip, $region, $exclude_zip, $exclude_region ) ) {
			echo wpb_js_remove_wpautop( $content );
		}

	} else {

		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

		$zipcodes_mode = isset( $atts['zipcodes_mode'] ) ? trim( $atts['zipcodes_mode'] ) : 'include';
		$zipcodes_input = isset( $atts['zipcodes_input'] ) ? trim( $atts['zipcodes_input'] ) : '';
		$zipcodes_region = isset( $atts['zipcodes_region'] ) ? trim( $atts['zipcodes_region'] ) : '';


		if( $zipcodes_mode == 'exclude' ) {

			if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

				echo '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="" data-region="" data-ex_filter="' . $zipcodes_input . '" data-ex_region="' . $zipcodes_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
			
			} elseif( geot_target_zip( '', '', $zipcodes_input, $zipcodes_region ) ) {
				echo wpb_js_remove_wpautop( $content );
			}
		
		} else {

			if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				echo '<div class="geot-ajax geot-filter" data-action="zip_filter" data-filter="' . $zipcodes_input . '" data-region="' . $zipcodes_region . '" data-ex_filter="" data-ex_region="">' . wpb_js_remove_wpautop( $content ) . '</div>';
			
			} elseif( geot_target_zip( $zipcodes_input, $zipcodes_region ) ) {
				echo wpb_js_remove_wpautop( $content );
			}
		}
	}
}