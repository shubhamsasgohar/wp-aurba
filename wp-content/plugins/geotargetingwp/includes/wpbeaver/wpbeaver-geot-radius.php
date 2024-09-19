<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * WPBeaver Geo Module
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class WPBeaver_GeoRadius {

	/**
	 * Add the actual fields
	 *
	 * @return array
	 */
	static function get_fields() {

		$section = [

			'title' => esc_html__( 'Geo Radius', 'geot' ),
			'fields' => [
				'radius_mode' => [
					'type'			=> 'select',
					'multi-select'	=> false,
					'label'			=> esc_html__( 'Visibility', 'Geot' ),
					'options'		=> [
						'include'	=> esc_html__( 'Show', 'geot' ),
						'exclude'	=> esc_html__( 'Hide', 'geot' )
					],
					'help' => esc_html__( 'Choose visibility.', 'geot' ),
				],
				'radius_km' => [
					'type'			=> 'unit',
					'class'			=> 'geot_radius_km_input',
					'placeholder'	=> '100',
					'label'			=> esc_html__( 'Radius', 'Geot' ),
					'help'			=> esc_html__( 'Type the range.', 'geot' ),
				],
				'radius_lat' => [
					'type'		=> 'unit',
					'class'		=> 'geot_radius_lat_input',
					'label'		=> esc_html__( 'Latitude', 'Geot' ),
					'help'		=> esc_html__( 'Type the latitude.', 'geot' ),
				],
				'radius_lng' => [
					'type'		=> 'unit',
					'class'		=> 'geot_radius_lng_input',
					'label'		=> esc_html__( 'Longitude', 'Geot' ),
					'help'		=> esc_html__( 'Type the longitude.', 'geot' ),
				],
			],
		];

		return $section;
	}


	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render( $settings ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		// If not exist the new params, work with the old params
		if( ! isset( $settings['radius_mode'] ) )
			return self::is_render_deprecated( $settings );

		$radius_mode	= isset( $settings['radius_mode'] ) ? trim( $settings['radius_mode'] ) : 'include';
		$radius_km		= isset( $settings['radius_km'] ) ? trim( $settings['radius_km'] ) : '';
		$radius_lat 	= isset( $settings['radius_lat'] ) ? trim( $settings['radius_lat'] ) : '';
		$radius_lng 	= isset( $settings['radius_lng'] ) ? trim( $settings['radius_lng'] ) : '';

		if ( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) )
			return true;

		$target = geot_target_radius( $radius_lat, $radius_lng, $radius_km );

		if( $radius_mode == 'exclude' )
			return ! $target;

		return $target;
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render( $settings, $output ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		// If not exist the new params, work with the old params
		if( ! isset( $settings['radius_mode' ] ) )
			return self::ajax_render_deprecated( $settings, $output );

		$radius_mode	= isset( $settings['radius_mode'] ) ? trim( $settings['radius_mode'] ) : 'include';
		$radius_km 		= isset( $settings['radius_km'] ) ? trim( $settings['radius_km'] ) : '';
		$radius_lat 	= isset( $settings['radius_lat'] ) ? trim( $settings['radius_lat'] ) : '';
		$radius_lng 	= isset( $settings['radius_lng'] ) ? trim( $settings['radius_lng'] ) : '';

		if ( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) )
			return $output;

		return '<div class="geot-ajax geot-filter" data-action="radius_filter" data-geo_mode="' . $radius_mode . '" data-filter="' . $radius_km . '" data-region="' . $radius_lat . '" data-ex_filter="' . $radius_lng . '">' . $output . '</div>';
	}



	/*
		depreaciate methods
		old params ( v: 3.4.0.0 )
	*/

	/**
	 * Conditional if render
	 *
	 * @return array
	 */
	static function is_render_deprecated( $settings ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		$radius_km    = isset( $settings['radius_km'] ) ? trim( $settings['radius_km'] ) : '';
		$radius_lat = isset( $settings['radius_lat'] ) ? trim( $settings['radius_lat'] ) : '';
		$radius_lng = isset( $settings['radius_lng'] ) ? trim( $settings['radius_lng'] ) : '';

		if( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) ) {
			return true;
		}

		return geot_target_radius( $radius_lat, $radius_lng, $radius_km );
	}


	/**
	 * if is ajax, apply render
	 *
	 * @return array
	 */
	static function ajax_render_deprecated( $settings, $output ) {

		if( is_object( $settings ) )
			$settings = get_object_vars($settings);

		$radius_km    = isset( $settings['radius_km'] ) ? trim( $settings['radius_km'] ) : '';
		$radius_lat = isset( $settings['radius_lat'] ) ? trim( $settings['radius_lat'] ) : '';
		$radius_lng = isset( $settings['radius_lng'] ) ? trim( $settings['radius_lng'] ) : '';

		if( empty( $radius_km ) || empty( $radius_lat ) || empty( $radius_lng ) ) {
			return $output;
		}

		return '<div class="geot-ajax geot-filter" data-action="radius_filter" data-filter="' . $radius_km . '" data-region="' . $radius_lat . '" data-ex_filter="' . $radius_lng . '">' . $output . '</div>';
	}
}