<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Fusion Geo Module
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class Fusion_GeoRadius {

	/**
	 * Geot fields to State
	 *
	 * @return array
	 */
	static function get_fields() {

		$fields = [
			[
				'type'			=> 'radio_button_set',
				'heading'		=> esc_attr__( 'Radius Visibility', 'geot' ),
				'description'	=> esc_attr__( 'Choose visibility.', 'geot' ),
				'param_name'	=> 'radius_mode',
				'default'		=> 'include',
				'value'	=> [
					'include'	=> esc_attr__( 'Show', 'geot' ),
					'exclude'	=> esc_attr__( 'Hide', 'geot' ),
				],
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Radius', 'geot' ),
				'description'	=> esc_attr__( 'Type the range.', 'geot' ),
				'param_name'	=> 'radius_km',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Latitude', 'geot' ),
				'description'	=> esc_attr__( 'Type the latitude.', 'geot' ),
				'param_name'	=> 'radius_lat',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			],[
				'type'			=> 'textfield',
				'heading'		=> esc_attr__( 'Longitude', 'geot' ),
				'description'	=> esc_attr__( 'Type the longitude.', 'geot' ),
				'param_name'	=> 'radius_lng',
				'default'		=> '',
				'group'			=> esc_attr__( 'GeoTargeting', 'geot' ),
			]
		];

		return $fields;
	}

	/**
	 * Conditional if it apply deprecated method
	 * 
	 * @param  array   $settings
	 * @return boolean
	 */
	static function is_deprecated( $settings = [] ) {
		return isset( $settings['geot_radius_km'] ) || isset( $settings['geot_radius_lat'] ) || isset( $settings['geot_radius_lng'] );
	}


	/**
	 * Add the actual fields
	 *
	 * @return bool
	 */
	static function is_render( $attrs = [] ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $attrs ) )
			return self::is_render_deprecated( $attrs );

		$radius_mode	= isset( $attrs['radius_mode'] ) ? trim( $attrs['radius_mode'] ) : 'include';
		$radius_km		= isset( $attrs['radius_km'] ) ? trim( $attrs['radius_km'] ) : '';
		$radius_lat 	= isset( $attrs['radius_lat'] ) ? trim( $attrs['radius_lat'] ) : '';
		$radius_lng		= isset( $attrs['radius_lng'] ) ? trim( $attrs['radius_lng'] ) : '';

		if( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) )
			return true;

		$target = geot_target_radius( $geot_radius_lat, $geot_radius_lng, $geot_radius_km );

		if( $radius_mode == 'exclude' )
			return ! $target;

		return $target;
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_render( $attrs, $output ) {

		// If not exist the new params, work with the old params
		if( self::is_deprecated( $attrs ) )
			return self::ajax_render_deprecated( $attrs, $output );

		$radius_mode	= isset( $attrs['radius_mode'] ) ? trim( $attrs['radius_mode'] ) : 'include';
		$radius_km		= isset( $attrs['radius_km'] ) ? trim( $attrs['radius_km'] ) : '';
		$radius_lat 	= isset( $attrs['radius_lat'] ) ? trim( $attrs['radius_lat'] ) : '';
		$radius_lng		= isset( $attrs['radius_lng'] ) ? trim( $attrs['radius_lng'] ) : '';

		if( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) )
			return $output;

		return '<div class="geot-ajax geot-filter" data-geo_mode="' . $radius_mode . '" data-action="radius_filter" data-filter="' . $radius_km . '" data-region="' . $radius_lat . '" data-ex_filter="' . $radius_lng . '">' . $output . '</div>';
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
	static function is_render_deprecated( $attrs = [] ) {
		
		$geot_radius_km		= isset( $attrs['geot_radius_km'] ) ? trim( $attrs['geot_radius_km'] ) : '';
        $geot_radius_lat	= isset( $attrs['geot_radius_lat'] ) ? trim( $attrs['geot_radius_lat'] ) : '';
        $geot_radius_lng	= isset( $attrs['geot_radius_lng'] ) ? trim( $attrs['geot_radius_lng'] ) : '';

		if( empty( $geot_radius_km ) || empty( $geot_radius_lat ) || empty( $geot_radius_lng ) ) {
			return true;
		}

		return geot_target_radius( $geot_radius_lat, $geot_radius_lng, $geot_radius_km );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return string
	 */
	static function ajax_render_deprecated( $attrs, $output ) {
		$geot_radius_km		= isset( $attrs['geot_radius_km'] ) ? trim( $attrs['geot_radius_km'] ) : '';
		$geot_radius_lat	= isset( $attrs['geot_radius_lat'] ) ? trim( $attrs['geot_radius_lat'] ) : '';
		$geot_radius_lng	= isset( $attrs['geot_radius_lng'] ) ? trim( $attrs['geot_radius_lng'] ) : '';

		if ( empty( $geot_radius_km ) || empty( $geot_radius_lat ) || empty( $geot_radius_lng ) ) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="radius_filter" data-filter="' . $geot_radius_km . '" data-region="' . $geot_radius_lat . '" data-ex_filter="' . $geot_radius_lng . '">' . $output . '</div>';
	}
}