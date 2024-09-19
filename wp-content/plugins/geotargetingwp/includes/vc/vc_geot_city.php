<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $city
 * @var $exclude_city
 * @var $region
 * @var $exclude_region
 * @var $this WPBakeryShortCode_VC_Geot
 */

$opts = geot_settings();

if( \GeotCore\is_builder() ) {
	echo wpb_js_remove_wpautop( $content );
} else {

	if( isset( $atts['city'] ) || isset( $atts['exclude_city'] ) || isset( $atts['region'] ) || isset( $atts['exclude_region'] ) ) {
		
		$city = isset( $atts['city'] ) ? trim( $atts['city'] ) : '';
		$exclude_city = isset( $atts['exclude_city'] ) ? trim( $atts['exclude_city'] ) : '';

		$region = isset( $atts['region'] ) ? trim( $atts['region'] ) : '';
		$exclude_region = isset( $atts['exclude_region'] ) ? trim( $atts['exclude_region'] ) : '';

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			echo '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $city . '" data-region="' . $region . '" data-ex_filter="' . $exclude_city . '" data-ex_region="' . $exclude_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
		
		} elseif( geot_target_city( $city, $region, $exclude_city, $exclude_region ) ) {
			echo wpb_js_remove_wpautop( $content );
		}
		
	} else {

		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

		$cities_mode = isset( $atts['cities_mode'] ) ? trim( $atts['cities_mode'] ) : 'include';
		$cities_input = isset( $atts['cities_input'] ) ? trim( $atts['cities_input'] ) : '';
		$cities_region = isset( $atts['cities_region'] ) ? trim( $atts['cities_region'] ) : '';

		if( $cities_mode == 'exclude' ) {

			if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

				echo '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="" data-region="" data-ex_filter="' . $cities_input . '" data-ex_region="' . $cities_region . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
			
			} elseif( geot_target_city( '', '', $cities_input, $cities_region ) ) {
				echo wpb_js_remove_wpautop( $content );
			}
		
		} else {

			if( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
				echo '<div class="geot-ajax geot-filter" data-action="city_filter" data-filter="' . $cities_input . '" data-region="' . $cities_region . '" data-ex_filter="" data-ex_region="">' . wpb_js_remove_wpautop( $content ) . '</div>';
			
			} elseif( geot_target_city( $cities_input, $cities_region ) ) {
				echo wpb_js_remove_wpautop( $content );
			}
		}
	}
}