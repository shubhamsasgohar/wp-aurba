<?php

/**
 * Divi Updater
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_Divi_Updater {

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
				post_content LIKE "%in_countries%" OR
				post_content LIKE "%in_region_countries%" OR
				post_content LIKE "%ex_countries%" OR
				post_content LIKE "%ex_region_countries%" OR
				post_content LIKE "%in_cities%" OR
				post_content LIKE "%in_region_cities%" OR
				post_content LIKE "%ex_cities%" OR
				post_content LIKE "%ex_region_cities%" OR
				post_content LIKE "%in_states%" OR
				post_content LIKE "%in_region_states%" OR
				post_content LIKE "%ex_states%" OR
				post_content LIKE "%ex_region_states%" OR
				post_content LIKE "%in_zips%" OR
				post_content LIKE "%in_region_zips%" OR
				post_content LIKE "%ex_zips%" OR
				post_content LIKE "%ex_region_zips%" OR
				post_content LIKE "%radius_mode%"
			);
		';

		$posts_ids = $wpdb->get_col( $query );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'DIVI Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
		}

		update_option( 'geot_divi_upgrade_3401_prepare', $posts_ids );

		return [ 'status' => 'ok' ];
	}


	/**
	 * Action upgrade 340
	 * @return mixed
	 */
	public static function upgrade_3401_action() {

		$posts_ids = get_option( 'geot_divi_upgrade_3401_prepare', false );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'DIVI Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
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

				// Exclude countries
				if( isset( $attrs['ex_countries'] ) ) {
					
					if( ! empty( $attrs['ex_countries'] ) ) {
						$attrs['countries_input'] = $attrs['ex_countries'];
						$attrs['countries_mode'] = 'exclude';
					}

					unset( $attrs['ex_countries'] );
				}

				// Exclude countries regions
				if( isset( $attrs['ex_region_countries'] ) ) {

					if( ! empty( $attrs['ex_region_countries'] ) ) {
						$attrs['countries_region'] = $attrs['ex_region_countries'];
						$attrs['countries_mode'] = 'exclude';
					}

					unset( $attrs['ex_region_countries'] );
				}

				// Include countries
				if( isset( $attrs['in_countries'] ) ) {
					
					if( ! empty( $attrs['in_countries'] ) ) {
						$attrs['countries_input'] = $attrs['in_countries'];
						$attrs['countries_mode'] = 'include';
					}

					unset( $attrs['in_countries'] );
				}

				// Include countries regions
				if( isset( $attrs['in_region_countries'] ) ) {

					if( ! empty( $attrs['in_region_countries'] ) ) {
						$attrs['countries_region'] = $attrs['in_region_countries'];
						$attrs['countries_mode'] = 'include';
					}

					unset( $attrs['in_region_countries'] );
				}


				// Exclude cities
				if( isset( $attrs['ex_cities'] ) ) {
					
					if( ! empty( $attrs['ex_cities'] ) ) {
						$attrs['cities_input'] = $attrs['ex_cities'];
						$attrs['cities_mode'] = 'exclude';
					}

					unset( $attrs['ex_cities'] );
				}

				// Exclude cities regions
				if( isset( $attrs['ex_region_cities'] ) ) {

					if( ! empty( $attrs['ex_region_cities'] ) ) {
						$attrs['cities_region'] = $attrs['ex_region_cities'];
						$attrs['cities_mode'] = 'exclude';
					}

					unset( $attrs['ex_region_cities'] );
				}

				// Include cities
				if( isset( $attrs['in_cities'] ) ) {
					
					if( ! empty( $attrs['in_cities'] ) ) {
						$attrs['cities_input'] = $attrs['in_cities'];
						$attrs['cities_mode'] = 'include';
					}

					unset( $attrs['in_cities'] );
				}

				// Include cities regions
				if( isset( $attrs['in_region_cities'] ) ) {

					if( ! empty( $attrs['in_region_cities'] ) ) {
						$attrs['cities_region'] = $attrs['in_region_cities'];
						$attrs['cities_mode'] = 'include';
					}

					unset( $attrs['in_region_cities'] );
				}


				// Exclude states
				if( isset( $attrs['ex_states'] ) ) {
					
					if( ! empty( $attrs['ex_states'] ) ) {
						$attrs['states_input'] = $attrs['ex_states'];
						$attrs['states_mode'] = 'exclude';
					}

					unset( $attrs['ex_states'] );
				}

				// Exclude states regions
				if( isset( $attrs['ex_region_states'] ) ) {

					if( ! empty( $attrs['ex_region_states'] ) ) {
						$attrs['states_region'] = $attrs['ex_region_states'];
						$attrs['states_mode'] = 'exclude';
					}

					unset( $attrs['ex_region_states'] );
				}

				// Include states
				if( isset( $attrs['in_states'] ) ) {
					
					if( ! empty( $attrs['in_states'] ) ) {
						$attrs['states_input'] = $attrs['in_states'];
						$attrs['states_mode'] = 'include';
					}

					unset( $attrs['in_states'] );
				}

				// Include states regions
				if( isset( $attrs['in_region_states'] ) ) {

					if( ! empty( $attrs['in_region_states'] ) ) {
						$attrs['states_region'] = $attrs['in_region_states'];
						$attrs['states_mode'] = 'include';
					}

					unset( $attrs['in_region_states'] );
				}



				// Exclude zips
				if( isset( $attrs['ex_zipcodes'] ) ) {
					
					if( ! empty( $attrs['ex_zipcodes'] ) ) {
						$attrs['zipcodes_input'] = $attrs['ex_zipcodes'];
						$attrs['zipcodes_mode'] = 'exclude';
					}

					unset( $attrs['ex_zipcodes'] );
				}

				// Exclude zips regions
				if( isset( $attrs['ex_region_zips'] ) ) {

					if( ! empty( $attrs['ex_region_zips'] ) ) {
						$attrs['zipcodes_region'] = $attrs['ex_region_zips'];
						$attrs['zipcodes_mode'] = 'exclude';
					}

					unset( $attrs['ex_region_zips'] );
				}

				// Include zips
				if( isset( $attrs['in_zipcodes'] ) ) {
					
					if( ! empty( $attrs['in_zipcodes'] ) ) {
						$attrs['zipcodes_input'] = $attrs['in_zipcodes'];
						$attrs['zipcodes_mode'] = 'include';
					}

					unset( $attrs['in_zipcodes'] );
				}

				// Include zips regions
				if( isset( $attrs['in_region_zips'] ) ) {

					if( ! empty( $attrs['in_region_zips'] ) ) {
						$attrs['zipcodes_region'] = $attrs['in_region_zips'];
						$attrs['zipcodes_mode'] = 'include';
					}

					unset( $attrs['in_region_zips'] );
				}


				// Radius Mode
				if( isset( $attrs['radius_mode'] ) ) {
					$attrs['radius_mode'] = $attrs['radius_mode'] == 'hide' ? 'exclude' : 'include';
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

			wp_update_post( [ 'ID' => $post['post_id'], 'post_content' => $content ] );

			// Remove ID
			unset( $posts[ $key ] );

			$j++;
		}

		if( ! empty( $posts ) ) {
			$posts_ids = wp_list_pluck( $posts, 'post_id' );
			update_option( 'geot_divi_upgrade_3401_prepare', $posts_ids );
			return [ 'status' => 'repeat' ];
		}

		delete_option( 'geot_divi_upgrade_3401_prepare' );
		return [ 'status' => 'ok' ];
	}
}