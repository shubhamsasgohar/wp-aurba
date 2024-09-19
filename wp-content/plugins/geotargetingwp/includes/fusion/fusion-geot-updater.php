<?php

/**
 * Fusion Updater
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_Fusion_Updater {

	public static $repeat_limit = 20;

	/**
	 * Upgrade Builder
	 */
	public static function upgrade_3401_prepare() {

		global $wpdb;

		$query = 'SELECT
				ID as post_id
			FROM
				' . $wpdb->posts . '
			WHERE
				post_status = "publish" AND (
				post_content LIKE "%geot_in_countries%" OR
				post_content LIKE "%geot_in_region_countries%" OR
				post_content LIKE "%geot_ex_countries%" OR
				post_content LIKE "%geot_ex_region_countries%" OR
				post_content LIKE "%geot_in_cities%" OR
				post_content LIKE "%geot_in_region_cities%" OR
				post_content LIKE "%geot_ex_cities%" OR
				post_content LIKE "%geot_ex_region_cities%" OR
				post_content LIKE "%geot_in_states%" OR
				post_content LIKE "%geot_in_region_states%" OR
				post_content LIKE "%geot_ex_states%" OR
				post_content LIKE "%geot_ex_region_states%" OR
				post_content LIKE "%geot_in_zips%" OR
				post_content LIKE "%geot_in_region_zips%" OR
				post_content LIKE "%geot_ex_zips%" OR
				post_content LIKE "%geot_ex_region_zips%"
			);
		';

		$posts_ids = $wpdb->get_col( $query );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'Fusion Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
		}

		update_option( 'geot_fusion_upgrade_3401_prepare', $posts_ids );

		return [ 'status' => 'ok' ];
	}


	/**
	 * Action upgrade 340
	 * @return mixed
	 */
	public static function upgrade_3401_action() {

		$posts_ids = get_option( 'geot_fusion_upgrade_3401_prepare', false );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'Fusion Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
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
			
			$pattern = '/\[(.*?)\]/';
			preg_match_all( $pattern, $content, $matches );

			if( ! isset( $matches[0] ) || empty( $matches[0] ) ) {
				unset( $posts[ $key ] );
				continue;
			}

			foreach( $matches[0] as $i => $match ) {

				// If it is a closet tag
				if( substr( $match, 0, 2 ) == '[/' )
					continue;

				$allTag = str_replace( [ '[', ']' ], [ '[ ', ' ]' ], $match );

				// Array parse
				$params = shortcode_parse_atts( $allTag );

				$tagName = $params[1];
				$attrs = array_diff_key(
					$params,
					array_filter( $params, 'is_numeric', ARRAY_FILTER_USE_KEY )
				);


				if( empty( $attrs ) )
					continue;

				// Initialize Countries
				$attrs['countries_mode'] 	= 'include';
				$attrs['countries_input'] 	= '';
				$attrs['countries_region'] 	= 'null';

				// Initialize Cities
				$attrs['cities_mode'] 		= 'include';
				$attrs['cities_input'] 		= '';
				$attrs['cities_region'] 	= 'null';

				// Initialize States
				$attrs['states_mode'] 		= 'include';
				$attrs['states_input'] 		= '';
				$attrs['states_region'] 	= 'null';

				// Initialize Zipcodes
				$attrs['zipcodes_mode'] 	= 'include';
				$attrs['zipcodes_input'] 	= '';
				$attrs['zipcodes_region'] 	= 'null';

				// Initialize Radius
				$attrs['radius_mode'] 	= 'include';


				// Exclude countries
				if( isset( $attrs['geot_ex_countries'] ) ) {
					
					if( ! empty( $attrs['geot_ex_countries'] ) ) {
						$attrs['countries_input'] = $attrs['geot_ex_countries'];
						$attrs['countries_mode'] = 'exclude';
					}

					unset( $attrs['geot_ex_countries'] );
				}

				// Exclude countries regions
				if( isset( $attrs['geot_ex_region_countries'] ) ) {

					if( ! empty( $attrs['geot_ex_region_countries'] ) &&
						$attrs['geot_ex_region_countries'] != 'null'
					) {
						$attrs['countries_region'] = $attrs['geot_ex_region_countries'];
						$attrs['countries_mode'] = 'exclude';
					}

					unset( $attrs['geot_ex_region_countries'] );
				}

				// Include countries
				if( isset( $attrs['geot_in_countries'] ) ) {
					
					if( ! empty( $attrs['geot_in_countries'] ) ) {
						$attrs['countries_input'] = $attrs['geot_in_countries'];
						$attrs['countries_mode'] = 'include';
					}

					unset( $attrs['geot_in_countries'] );
				}

				// Include countries regions
				if( isset( $attrs['geot_in_region_countries'] ) ) {

					if( ! empty( $attrs['geot_in_region_countries'] ) &&
						$attrs['geot_in_region_countries'] != 'null'
					) {
						$attrs['countries_region'] = $attrs['geot_in_region_countries'];
						$attrs['countries_mode'] = 'include';
					}

					unset( $attrs['geot_in_region_countries'] );
				}


				// Exclude cities
				if( isset( $attrs['geot_ex_cities'] ) ) {
					
					if( ! empty( $attrs['geot_ex_cities'] ) ) {
						$attrs['cities_input'] = $attrs['geot_ex_cities'];
						$attrs['cities_mode'] = 'exclude';
					}

					unset( $attrs['geot_ex_cities'] );
				}

				// Exclude cities regions
				if( isset( $attrs['geot_ex_region_cities'] ) ) {

					if( ! empty( $attrs['geot_ex_region_cities'] ) &&
						$attrs['geot_ex_region_cities'] != 'null'
					) {
						$attrs['cities_region'] = $attrs['geot_ex_region_cities'];
						$attrs['cities_mode'] = 'exclude';
					}

					unset( $attrs['geot_ex_region_cities'] );
				}

				// Include cities
				if( isset( $attrs['geot_in_cities'] ) ) {
					
					if( ! empty( $attrs['geot_in_cities'] ) ) {
						$attrs['cities_input'] = $attrs['geot_in_cities'];
						$attrs['cities_mode'] = 'include';
					}

					unset( $attrs['geot_in_cities'] );
				}

				// Include cities regions
				if( isset( $attrs['geot_in_region_cities'] ) ) {

					if( ! empty( $attrs['geot_in_region_cities'] ) &&
						$attrs['geot_in_region_cities'] != 'null'
					) {
						$attrs['cities_region'] = $attrs['geot_in_region_cities'];
						$attrs['cities_mode'] = 'include';
					}

					unset( $attrs['geot_in_region_cities'] );
				}


				// Exclude states
				if( isset( $attrs['geot_ex_states'] ) ) {
					
					if( ! empty( $attrs['geot_ex_states'] ) ) {
						$attrs['states_input'] = $attrs['geot_ex_states'];
						$attrs['states_mode'] = 'exclude';
					}

					unset( $attrs['geot_ex_states'] );
				}

				// Exclude states regions
				if( isset( $attrs['geot_ex_region_states'] ) ) {

					if( ! empty( $attrs['geot_ex_region_states'] ) &&
						$attrs['geot_ex_region_states'] != 'null'
					) {
						$attrs['states_region'] = $attrs['geot_ex_region_states'];
						$attrs['states_mode'] = 'exclude';
					}

					unset( $attrs['geot_ex_region_states'] );
				}

				// Include states
				if( isset( $attrs['geot_in_states'] ) ) {
					
					if( ! empty( $attrs['geot_in_states'] ) ) {
						$attrs['states_input'] = $attrs['geot_in_states'];
						$attrs['states_mode'] = 'include';
					}

					unset( $attrs['geot_in_states'] );
				}

				// Include states regions
				if( isset( $attrs['geot_in_region_states'] ) ) {

					if( ! empty( $attrs['geot_in_region_states'] ) &&
						$attrs['geot_in_region_states'] != 'null'
					) {
						$attrs['states_region'] = $attrs['geot_in_region_states'];
						$attrs['states_mode'] = 'include';
					}

					unset( $attrs['geot_in_region_states'] );
				}



				// Exclude zips
				if( isset( $attrs['geot_ex_zips'] ) ) {
					
					if( ! empty( $attrs['geot_ex_zips'] ) ) {
						$attrs['zipcodes_input'] = $attrs['geot_ex_zips'];
						$attrs['zipcodes_mode'] = 'exclude';
					}

					unset( $attrs['geot_ex_zips'] );
				}

				// Exclude zips regions
				if( isset( $attrs['geot_ex_region_zips'] ) ) {

					if( ! empty( $attrs['geot_ex_region_zips'] ) &&
						$attrs['geot_ex_region_zips'] != 'null'
					) {
						$attrs['zipcodes_region'] = $attrs['geot_ex_region_zips'];
						$attrs['zipcodes_mode'] = 'exclude';
					}

					unset( $attrs['geot_ex_region_zips'] );
				}

				// Include zips
				if( isset( $attrs['geot_in_zips'] ) ) {
					
					if( ! empty( $attrs['geot_in_zips'] ) ) {
						$attrs['zipcodes_input'] = $attrs['geot_in_zips'];
						$attrs['zipcodes_mode'] = 'include';
					}

					unset( $attrs['geot_in_zips'] );
				}

				// Include zips regions
				if( isset( $attrs['geot_in_region_zips'] ) ) {

					if( ! empty( $attrs['geot_in_region_zips'] ) &&
						$attrs['geot_in_region_zips'] != 'null'
					) {
						$attrs['zipcodes_region'] = $attrs['geot_in_region_zips'];
						$attrs['zipcodes_mode'] = 'include';
					}

					unset( $attrs['geot_in_region_zips'] );
				}


				// Radius KM
				if( isset( $attrs['geot_radius_km'] ) ) {

					if( ! empty( $attrs['geot_radius_km'] ) )
						$attrs['radius_km'] = $attrs['geot_radius_km'];

					unset( $attrs['geot_radius_km'] );
				}

				// Radius Latitude
				if( isset( $attrs['geot_radius_lat'] ) ) {

					if( ! empty( $attrs['geot_radius_lat'] ) )
						$attrs['radius_lat'] = $attrs['geot_radius_lat'];

					unset( $attrs['geot_radius_lat'] );
				}

				// Radius Longitude
				if( isset( $attrs['geot_radius_lng'] ) ) {

					if( ! empty( $attrs['geot_radius_lng'] ) )
						$attrs['radius_lng'] = $attrs['geot_radius_lng'];

					unset( $attrs['geot_radius_lng'] );
				}


				$valuesAttrs = [];
				foreach( $attrs as $attr_key => $attr_value )
					$valuesAttrs[] = sprintf( '%s="%s"', $attr_key, $attr_value );


				$newShortcode = sprintf(
					'[%s %s ]',
					$tagName,
					implode( ' ', $valuesAttrs )
				);

				$content = str_replace( $match, $newShortcode, $content );
			}

			// Update post
			wp_update_post( [ 'ID' => $post['post_id'], 'post_content' => $content ] );

			// Remove ID
			unset( $posts[ $key ] );

			$j++;
		}

		if( ! empty( $posts ) ) {
			$posts_ids = wp_list_pluck( $posts, 'post_id' );
			update_option( 'geot_fusion_upgrade_3401_prepare', $posts_ids );
			return [ 'status' => 'repeat' ];
		}

		delete_option( 'geot_fusion_upgrade_3401_prepare' );
		return [ 'status' => 'ok' ];
	}
}