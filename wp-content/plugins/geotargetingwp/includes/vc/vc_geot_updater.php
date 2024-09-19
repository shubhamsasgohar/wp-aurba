<?php

/**
 * Visual Composer Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_VC_Updater {

	public static $repeat_limit = 20;

	/**
	 * Prepare upgrade 340
	 * @return mixed
	 */
	public static function upgrade_3401_prepare() {

		global $wpdb;

		$query = 'SELECT
				ID AS post_id
			FROM
				' . $wpdb->posts . '
			WHERE
				post_status = "publish" AND
				(
					post_content LIKE \'%vc_geotwp_country%\' OR
					post_content LIKE \'%vc_geotwp_city%\' OR
					post_content LIKE \'%vc_geotwp_state%\' OR
					post_content LIKE \'%vc_geotwp_zip%\' OR
					post_content LIKE \'%vc_geotwp_radius%\'
				);
		';

		$posts_ids = $wpdb->get_col( $query );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'VC Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
		}

		update_option( 'geot_vc_upgrade_3401_prepare', $posts_ids );

		return [ 'status' => 'ok' ];
	}
	

	/**
	 * Action upgrade 340
	 * @return mixed
	 */
	public static function upgrade_3401_action() {

		$posts_ids = get_option( 'geot_vc_upgrade_3401_prepare', false );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'VC Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
		}

		global $wpdb;
		
		$query = 'SELECT
				ID as post_id,
				post_content
			FROM
				' . $wpdb->posts . '
			WHERE
				ID IN ('.implode( ',', $posts_ids ).');
		';

		$j = 0;
		$posts_aux = $posts = $wpdb->get_results( $query, ARRAY_A );

		foreach( $posts_aux as $key => $post ) {

			if( $j > self::$repeat_limit )
				break;

			$content = $post['post_content'];

			// Countries
			$pattern = '/\[vc_geotwp_country(.*?)\]/';
			preg_match_all( $pattern, $content, $matches );

			// If there is no matches
			if( isset( $matches[0] ) && ! empty( $matches[0] ) ) {

				$newAttrs = [];
				foreach( $matches[0] as $match ) {

					$newTag = str_replace( [ '[', ']' ], [ '[ ', ' ]' ], $match );

					// Array parse
					$attrs = shortcode_parse_atts( $newTag );

					// Exclude countries
					if( ! empty( $attrs['exclude_country'] ) ) {
						$newAttrs['countries_input'] = $attrs['exclude_country'];
						$newAttrs['countries_mode'] = 'exclude';
					}

					// Exclude countries regions
					if( ! empty( $attrs['exclude_region'] ) ) {
						$newAttrs['countries_region'] = $attrs['exclude_region'];
						$newAttrs['countries_mode'] = 'exclude';
					}

					// Include countries
					if( ! empty( $attrs['country'] ) ) {
						$newAttrs['countries_input'] = $attrs['country'];
						$newAttrs['countries_mode'] = 'include';
					}

					// Include countries regions
					if( ! empty( $attrs['region'] ) ) {
						$newAttrs['countries_region'] = $attrs['region'];
						$newAttrs['countries_mode'] = 'include';
					}

					// Countries mode
					if( ! isset( $newAttrs['countries_mode'] ) )
						continue;

					$valuesAttrs = [];
					foreach( $newAttrs as $attr_key => $attr_value )
						$valuesAttrs[] = sprintf( '%s="%s"', $attr_key, $attr_value );

					$newShortcode = sprintf(
						'[vc_geotwp_country %s ]',
						implode( ' ', $valuesAttrs )
					);

					$content = str_replace( $match, $newShortcode, $content );
				}
			}


			// Cities
			$pattern = '/\[vc_geotwp_city(.*?)\]/';
			preg_match_all( $pattern, $content, $matches );

			// If there is no matches
			if( isset( $matches[0] ) && ! empty( $matches[0] ) ) {

				$newAttrs = [];
				foreach( $matches[0] as $match ) {

					$newTag = str_replace( [ '[', ']' ], [ '[ ', ' ]' ], $match );

					// Array parse
					$attrs = shortcode_parse_atts( $newTag );

					// Exclude cities
					if( ! empty( $attrs['exclude_city'] ) ) {
						$newAttrs['cities_input'] = $attrs['exclude_city'];
						$newAttrs['cities_mode'] = 'exclude';
					}

					// Exclude cities regions
					if( ! empty( $attrs['exclude_region'] ) ) {
						$newAttrs['cities_region'] = $attrs['exclude_region'];
						$newAttrs['cities_mode'] = 'exclude';
					}

					// Include cities
					if( ! empty( $attrs['city'] ) ) {
						$newAttrs['cities_input'] = $attrs['city'];
						$newAttrs['cities_mode'] = 'include';
					}

					// Include cities regions
					if( ! empty( $attrs['region'] ) ) {
						$newAttrs['cities_region'] = $attrs['region'];
						$newAttrs['cities_mode'] = 'include';
					}

					// cities mode
					if( ! isset( $newAttrs['cities_mode'] ) )
						continue;

					$valuesAttrs = [];
					foreach( $newAttrs as $attr_key => $attr_value )
						$valuesAttrs[] = sprintf( '%s="%s"', $attr_key, $attr_value );

					$newShortcode = sprintf(
						'[vc_geotwp_city %s ]',
						implode( ' ', $valuesAttrs )
					);

					$content = str_replace( $match, $newShortcode, $content );
				}
			}


			// States
			$pattern = '/\[vc_geotwp_state(.*?)\]/';
			preg_match_all( $pattern, $content, $matches );

			// If there is no matches
			if( isset( $matches[0] ) && ! empty( $matches[0] ) ) {

				$newAttrs = [];
				foreach( $matches[0] as $match ) {

					$newTag = str_replace( [ '[', ']' ], [ '[ ', ' ]' ], $match );

					// Array parse
					$attrs = shortcode_parse_atts( $newTag );

					// Exclude states
					if( ! empty( $attrs['exclude_state'] ) ) {
						$newAttrs['states_input'] = $attrs['exclude_state'];
						$newAttrs['states_mode'] = 'exclude';
					}

					// Exclude states regions
					if( ! empty( $attrs['exclude_region'] ) ) {
						$newAttrs['states_region'] = $attrs['exclude_region'];
						$newAttrs['states_mode'] = 'exclude';
					}

					// Include states
					if( ! empty( $attrs['state'] ) ) {
						$newAttrs['states_input'] = $attrs['state'];
						$newAttrs['states_mode'] = 'include';
					}

					// Include states regions
					if( ! empty( $attrs['region'] ) ) {
						$newAttrs['states_region'] = $attrs['region'];
						$newAttrs['states_mode'] = 'include';
					}

					// states mode
					if( ! isset( $newAttrs['states_mode'] ) )
						continue;

					$valuesAttrs = [];
					foreach( $newAttrs as $attr_key => $attr_value )
						$valuesAttrs[] = sprintf( '%s="%s"', $attr_key, $attr_value );

					$newShortcode = sprintf(
						'[vc_geotwp_state %s ]',
						implode( ' ', $valuesAttrs )
					);

					$content = str_replace( $match, $newShortcode, $content );
				}
			}


			// ZipCodes
			$pattern = '/\[vc_geotwp_zip(.*?)\]/';
			preg_match_all( $pattern, $content, $matches );

			// If there is no matches
			if( isset( $matches[0] ) && ! empty( $matches[0] ) ) {

				$newAttrs = [];
				foreach( $matches[0] as $match ) {

					$newTag = str_replace( [ '[', ']' ], [ '[ ', ' ]' ], $match );

					// Array parse
					$attrs = shortcode_parse_atts( $newTag );

					// Exclude zips
					if( ! empty( $attrs['exclude_zip'] ) ) {
						$newAttrs['zipcodes_input'] = $attrs['exclude_zip'];
						$newAttrs['zipcodes_mode'] = 'exclude';
					}

					// Exclude zips regions
					if( ! empty( $attrs['exclude_region'] ) ) {
						$newAttrs['zipcodes_region'] = $attrs['exclude_region'];
						$newAttrs['zipcodes_mode'] = 'exclude';
					}

					// Include zips
					if( ! empty( $attrs['zip'] ) ) {
						$newAttrs['zipcodes_input'] = $attrs['zip'];
						$newAttrs['zipcodes_mode'] = 'include';
					}

					// Include zips regions
					if( ! empty( $attrs['region'] ) ) {
						$newAttrs['zipcodes_region'] = $attrs['region'];
						$newAttrs['zipcodes_mode'] = 'include';
					}

					// zips mode
					if( ! isset( $newAttrs['zipcodes_mode'] ) )
						continue;

					$valuesAttrs = [];
					foreach( $newAttrs as $attr_key => $attr_value )
						$valuesAttrs[] = sprintf( '%s="%s"', $attr_key, $attr_value );

					$newShortcode = sprintf(
						'[vc_geotwp_zip %s ]',
						implode( ' ', $valuesAttrs )
					);

					$content = str_replace( $match, $newShortcode, $content );
				}
			}


			// Radius
			$pattern = '/\[vc_geotwp_radius(.*?)\]/';
			preg_match_all( $pattern, $content, $matches );

			// If there is no matches
			if( isset( $matches[0] ) && ! empty( $matches[0] ) ) {

				$newAttrs = [];
				foreach( $matches[0] as $match ) {

					$newTag = str_replace( [ '[', ']' ], [ '[ ', ' ]' ], $match );

					// Array parse
					$attrs = shortcode_parse_atts( $newTag );

					$newAttrs['radius_mode'] 	= 'include';
					$newAttrs['radius_km'] 		= $attrs['radius_km'];
					$newAttrs['radius_lat'] 	= $attrs['radius_lat'];
					$newAttrs['radius_lng'] 	= $attrs['radius_lng'];

					$valuesAttrs = [];
					foreach( $newAttrs as $attr_key => $attr_value )
						$valuesAttrs[] = sprintf( '%s="%s"', $attr_key, $attr_value );

					$newShortcode = sprintf(
						'[vc_geotwp_radius %s ]',
						implode( ' ', $valuesAttrs )
					);

					$content = str_replace( $match, $newShortcode, $content );
				}
			}

			// Update post
			wp_update_post( [ 'ID' => $post['post_id'], 'post_content' => $content ] );

			// Remove ID
			unset( $posts[ $key ] );

			$j++;
		}

		if( ! empty( $posts ) ) {
			$posts_ids = wp_list_pluck( $posts, 'post_id' );
			update_option( 'geot_vc_upgrade_3401_prepare', $posts_ids );
			return [ 'status' => 'repeat' ];
		}

		delete_option( 'geot_vc_upgrade_3401_prepare' );
		return [ 'status' => 'ok' ];
	}
}
