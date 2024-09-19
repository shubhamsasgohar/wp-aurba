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
class GeotWP_Gutenberg_Updater {

	public static $repeat_limit = 20;

	/**
	 * Upgrade Builder
	 */
	public static function upgrade_3401_prepare() {

		// Check the permissions
		if( ! current_user_can( 'manage_options' ) )
			return false;

		$tags = [
			'geotargeting-pro/gutenberg-country',
			'geotargeting-pro/gutenberg-city',
			'geotargeting-pro/gutenberg-state',
			'geotargeting-pro/gutenberg-zipcode',
			'geotargeting-pro/gutenberg-radius',
		];

		global $wpdb;

		$query = 'SELECT
				ID as post_id
			FROM
				' . $wpdb->posts . '
			WHERE
				post_status = "publish" AND (
				post_content LIKE "%in_countries%" OR
				post_content LIKE "%in_regions%" OR
				post_content LIKE "%ex_countries%" OR
				post_content LIKE "%ex_regions%" OR
				post_content LIKE "%in_cities%" OR
				post_content LIKE "%ex_cities%" OR
				post_content LIKE "%in_states%" OR
				post_content LIKE "%ex_states%" OR
				post_content LIKE "%in_zipcodes%" OR
				post_content LIKE "%ex_zipcodes%" OR
				post_content LIKE "%radius_km%" 
			);
		';

		$posts_ids = $wpdb->get_col( $query );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'Gutenberg Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
		}

		update_option( 'geot_gutenberg_upgrade_3401_prepare', $posts_ids );

		return [ 'status' => 'ok' ];
	}


	/**
	 * Action upgrade 340
	 * @return mixed
	 */
	public static function upgrade_3401_action() {

		$posts = get_option( 'geot_gutenberg_upgrade_3401_prepare', false );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'Gutenberg Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
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

			if( ! has_blocks( $post['post_content'] ) ) {
				unset( $posts[ $key ] );
				continue;
			}

			if( $j > self::$repeat_limit )
				break;

			// Convert blocks to array
			$blocks = parse_blocks( $post['post_content'] );

			foreach( $blocks as $key => $block ) {
				
				// Countries
				if( $block['blockName'] == 'geotargeting-pro/gutenberg-country' ) {

					// Exclude Countries
					if( isset( $block['attrs']['ex_countries'] ) ) {
						$block['attrs']['countries_input'] 	= $block['attrs']['ex_countries'];
						$block['attrs']['countries_mode'] 	= 'exclude';
						unset( $block['attrs']['ex_countries'] );
					}

					// Exclude Countries Regions
					if( isset( $block['attrs']['ex_regions'] ) ) {
						$block['attrs']['countries_region'] = $block['attrs']['ex_regions'];
						$block['attrs']['countries_mode'] 	= 'exclude';
						unset( $block['attrs']['ex_regions'] );
					}


					// Include Countries
					if( isset( $block['attrs']['in_countries'] ) ) {
						$block['attrs']['countries_input'] 	= $block['attrs']['in_countries'];
						$block['attrs']['countries_mode'] 	= 'include';
						unset( $block['attrs']['in_countries'] );
					}

					// Include Countries Regions
					if( isset( $block['attrs']['in_regions'] ) ) {
						$block['attrs']['countries_region'] = $block['attrs']['in_regions'];
						$block['attrs']['countries_mode'] 	= 'include';
						unset( $block['attrs']['in_regions'] );
					}
				}


				// Cities
				if( $block['blockName'] == 'geotargeting-pro/gutenberg-city' ) {

					// Exclude cities
					if( isset( $block['attrs']['ex_cities'] ) ) {
						$block['attrs']['cities_input'] 	= $block['attrs']['ex_cities'];
						$block['attrs']['cities_mode'] 	= 'exclude';
						unset( $block['attrs']['ex_cities'] );
					}

					// Exclude cities Regions
					if( isset( $block['attrs']['ex_regions'] ) ) {
						$block['attrs']['cities_region'] = $block['attrs']['ex_regions'];
						$block['attrs']['cities_mode'] 	= 'exclude';
						unset( $block['attrs']['ex_regions'] );
					}


					// Include cities
					if( isset( $block['attrs']['in_cities'] ) ) {
						$block['attrs']['cities_input'] 	= $block['attrs']['in_cities'];
						$block['attrs']['cities_mode'] 	= 'include';
						unset( $block['attrs']['in_cities'] );
					}

					// Include cities Regions
					if( isset( $block['attrs']['in_regions'] ) ) {
						$block['attrs']['cities_region'] = $block['attrs']['in_regions'];
						$block['attrs']['cities_mode'] 	= 'include';
						unset( $block['attrs']['in_regions'] );
					}
				}


				// States
				if( $block['blockName'] == 'geotargeting-pro/gutenberg-state' ) {

					// Exclude states
					if( isset( $block['attrs']['ex_states'] ) ) {
						$block['attrs']['states_input'] 	= $block['attrs']['ex_states'];
						$block['attrs']['states_mode'] 	= 'exclude';
						unset( $block['attrs']['ex_states'] );
					}

					// Exclude states Regions
					if( isset( $block['attrs']['ex_regions'] ) ) {
						$block['attrs']['states_region'] = $block['attrs']['ex_regions'];
						$block['attrs']['states_mode'] 	= 'exclude';
						unset( $block['attrs']['ex_regions'] );
					}


					// Include states
					if( isset( $block['attrs']['in_states'] ) ) {
						$block['attrs']['states_input'] 	= $block['attrs']['in_states'];
						$block['attrs']['states_mode'] 	= 'include';
						unset( $block['attrs']['in_states'] );
					}

					// Include states Regions
					if( isset( $block['attrs']['in_regions'] ) ) {
						$block['attrs']['states_region'] = $block['attrs']['in_regions'];
						$block['attrs']['states_mode'] 	= 'include';
						unset( $block['attrs']['in_regions'] );
					}
				}


				// Zipcode
				if( $block['blockName'] == 'geotargeting-pro/gutenberg-zipcode' ) {

					// Exclude zipcodes
					if( isset( $block['attrs']['ex_zipcodes'] ) ) {
						$block['attrs']['zipcodes_input'] 	= $block['attrs']['ex_zipcodes'];
						$block['attrs']['zipcodes_mode'] 	= 'exclude';
						unset( $block['attrs']['ex_zipcodes'] );
					}

					// Exclude zipcodes Regions
					if( isset( $block['attrs']['ex_regions'] ) ) {
						$block['attrs']['zipcodes_region'] = $block['attrs']['ex_regions'];
						$block['attrs']['zipcodes_mode'] 	= 'exclude';
						unset( $block['attrs']['ex_regions'] );
					}


					// Include zipcodes
					if( isset( $block['attrs']['in_zipcodes'] ) ) {
						$block['attrs']['zipcodes_input'] 	= $block['attrs']['in_zipcodes'];
						$block['attrs']['zipcodes_mode'] 	= 'include';
						unset( $block['attrs']['in_zipcodes'] );
					}

					// Include zipcodes Regions
					if( isset( $block['attrs']['in_regions'] ) ) {
						$block['attrs']['zipcodes_region'] = $block['attrs']['in_regions'];
						$block['attrs']['zipcodes_mode'] 	= 'include';
						unset( $block['attrs']['in_regions'] );
					}
				}

				// Radius
				if( $block['blockName'] == 'geotargeting-pro/gutenberg-radius' ) {
					$block['attrs']['radius_mode'] 	= 'include';
				}

				$content[ $key ] = ! empty( $block['blockName'] ) ? serialize_block( $block ) : '';
			}


			if( isset( $content ) ) {
				wp_update_post( [
					'ID' 			=> $post['post_id'],
					'post_content' 	=> implode( "\n", $content ),
				] );
			}

			// Remove ID
			unset( $posts[ $key ] );

			$j++;
		}

		if( ! empty( $posts ) ) {
			$posts_ids = wp_list_pluck( $posts, 'post_id' );
			update_option( 'geot_gutenberg_upgrade_3401_prepare', $posts_ids );
			return [ 'status' => 'repeat' ];
		}

		delete_option( 'geot_gutenberg_upgrade_3401_prepare' );
		return [ 'status' => 'ok' ];
	}
}