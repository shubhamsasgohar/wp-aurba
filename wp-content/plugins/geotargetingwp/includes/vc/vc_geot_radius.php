<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $radius_km
 * @var $radius_lat
 * @var $radius_lng
 * @var $this WPBakeryShortCode_VC_GeotWP_Radius
 */
$opts = geot_settings();

if( \GeotCore\is_builder() ) {
	echo wpb_js_remove_wpautop( $content );
} else {

	if( ! isset( $atts['radius_mode'] ) ) {

		$radius_km 	= isset( $atts['radius_km'] ) ? trim( $atts['radius_km'] ) : '';
		$radius_lat = isset( $atts['radius_lat'] ) ? trim( $atts['radius_lat'] ) : '';
		$radius_lng = isset( $atts['radius_lng'] ) ? trim( $atts['radius_lng'] ) : '';

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			echo '<div class="geot-ajax geot-filter" data-action="radius_filter" data-filter="' . $radius_km . '" data-region="' . $radius_lat . '" data-ex_filter="' . $radius_lng . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
		} elseif ( geot_target_radius( $radius_lat, $radius_lng, $radius_km ) ) {
			echo wpb_js_remove_wpautop( $content );
		}

	} else {
		$atts  = vc_map_get_attributes( $this->getShortcode(), $atts );

		$radius_mode = isset( $atts['radius_mode'] ) ? trim( $atts['radius_mode'] ) : 'include';
		$radius_km 	= isset( $atts['radius_km'] ) ? trim( $atts['radius_km'] ) : '';
		$radius_lat = isset( $atts['radius_lat'] ) ? trim( $atts['radius_lat'] ) : '';
		$radius_lng = isset( $atts['radius_lng'] ) ? trim( $atts['radius_lng'] ) : '';

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {
			echo '<div class="geot-ajax geot-filter" data-action="radius_filter" data-geo_mode="'. $radius_mode .'" data-filter="' . $radius_km . '" data-region="' . $radius_lat . '" data-ex_filter="' . $radius_lng . '">' . wpb_js_remove_wpautop( $content ) . '</div>';
		} else {

			$target = geot_target_radius( $radius_lat, $radius_lng, $radius_km );
			
			if( ( $radius_mode == 'include' && $target ) || ( $radius_mode == 'exclude' && ! $target ) ) {
				echo wpb_js_remove_wpautop( $content );
			}
		}
	}

}