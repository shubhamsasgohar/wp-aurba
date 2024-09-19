<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $country
 * @var $exclude_country
 * @var $region
 * @var $exclude_region
 * @var $this WPBakeryShortCode_VC_Geot
 */

$opts = geot_settings();

if( \GeotCore\is_builder() ) {
	echo wpb_js_remove_wpautop( $content );
} else {

	if( isset( $atts['country'] ) || isset( $atts['exclude_country'] ) || isset( $atts['region'] ) || isset( $atts['exclude_region'] ) ) {

		$country = isset( $atts['country'] ) ? trim( $atts['country'] ) : '';
		$exclude_country = isset( $atts['exclude_country'] ) ? trim( $atts['exclude_country'] ) : '';
		$region = isset( $atts['region'] ) ? trim( $atts['region'] ) : '';
		$exclude_region = isset( $atts['exclude_region'] ) ? trim( $atts['exclude_region'] ) : '';

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			echo '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $country . '" data-region="' . $region . '" data-ex_filter="' . $exclude_country . '" data-ex_region="' . $exclude_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
		
		} elseif( geot_target( $country, $region, $exclude_country, $exclude_region ) ) {
			echo wpb_js_remove_wpautop( $content );
		}

	} else {

		$atts 	= vc_map_get_attributes( $this->getShortcode(), $atts );

		$countries_mode = isset( $atts['countries_mode'] ) ? trim( $atts['countries_mode'] ) : 'include';
		$countries_input = isset( $atts['countries_input'] ) ? trim( $atts['countries_input'] ) : '';
		$countries_region = isset( $atts['countries_region'] ) ? trim( $atts['countries_region'] ) : '';

		if( $countries_mode == 'exclude' ) {

			if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

				echo '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="" data-region="" data-ex_filter="' . $countries_input . '" data-ex_region="' . $countries_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
			
			} elseif( geot_target( '', '', $countries_input, $countries_region ) ) {
				echo wpb_js_remove_wpautop( $content );
			}
		
		} else {

			if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				echo '<div class="geot-ajax geot-filter" data-action="country_filter" data-filter="' . $countries_input . '" data-region="' . $countries_region . '" data-ex_filter="" data-ex_region="">' . wpb_js_remove_wpautop( $content ) . '</div>';
			
			} elseif( geot_target( $countries_input, $countries_region ) ) {
				echo wpb_js_remove_wpautop( $content );
			}

		}
	}
}