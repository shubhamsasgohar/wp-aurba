<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $state
 * @var $exclude_state
 * @var $this WPBakeryShortCode_VC_Geot
 */
$opts = geot_settings();

if( \GeotCore\is_builder() ) {
	echo wpb_js_remove_wpautop( $content );
} else {

	if( isset( $atts['state'] ) || isset( $atts['exclude_state'] ) || isset( $atts['region'] ) || isset( $atts['exclude_region'] ) ) {

		$state = isset( $atts['state'] ) ? trim( $atts['state'] ) : '';
		$exclude_state = isset( $atts['exclude_state'] ) ? trim( $atts['exclude_state'] ) : '';

		$region = isset( $atts['region'] ) ? trim( $atts['region'] ) : '';
		$exclude_region = isset( $atts['exclude_region'] ) ? trim( $atts['exclude_region'] ) : '';

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
        	echo '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $state . '" data-region="' . $region . '" data-ex_filter="' . $exclude_state . '" data-ex_region="' . $exclude_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
    	} elseif ( geot_target_state( $state, $region, $exclude_state, $exclude_region ) ) {
			echo wpb_js_remove_wpautop( $content );
    	}

	} else {

		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

		$states_mode = isset( $atts['states_mode'] ) ? trim( $atts['states_mode'] ) : 'include';
		$states_input = isset( $atts['states_input'] ) ? trim( $atts['states_input'] ) : '';
		$states_region = isset( $atts['states_region'] ) ? trim( $atts['states_region'] ) : '';

		if( $states_mode == 'exclude' ) {

			if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

				echo '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="" data-region="" data-ex_filter="' . $states_input . '" data-ex_region="' . $states_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
			
			} elseif( geot_target_state( '', '', $states_input, $states_region ) ) {
				echo wpb_js_remove_wpautop( $content );
			}
		
		} else {

			if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				echo '<div class="geot-ajax geot-filter" data-action="state_filter" data-filter="' . $states_input . '" data-region="' . $states_region . '" data-ex_filter="" data-ex_region="">' . wpb_js_remove_wpautop( $content ) . '</div>';
			
			} elseif( geot_target_state( $states_input, $states_region ) ) {
				echo wpb_js_remove_wpautop( $content );
			}
		}
	}
}