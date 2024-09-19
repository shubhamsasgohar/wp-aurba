<?php

/**
 * WpBeaver Updater
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_WPBeaver_Updater {

	public static $repeat_limit = 20;

	/**
	 * Prepare upgrade 340
	 * @return mixed
	 */
	public static function upgrade_3401_prepare() {

		global $wpdb;

		$query = 'SELECT
				p.ID as post_ids
			FROM
				' . $wpdb->posts . ' AS p
			INNER JOIN
				' . $wpdb->postmeta . ' AS m
			ON
				p.ID = m.post_id
			WHERE
				p.post_status = "publish" AND
				m.meta_key = "_fl_builder_data" AND (
					m.meta_value LIKE \'%"in_countries"%\' OR
					m.meta_value LIKE \'%"in_regions"%\' OR
					m.meta_value LIKE \'%"ex_countries"%\' OR
					m.meta_value LIKE \'%"ex_regions"%\' OR
					m.meta_value LIKE \'%"in_cities"%\' OR
					m.meta_value LIKE \'%"in_regions_cities"%\' OR
					m.meta_value LIKE \'%"ex_cities"%\' OR
					m.meta_value LIKE \'%"ex_regions_cities"%\' OR
					m.meta_value LIKE \'%"in_states"%\' OR
					m.meta_value LIKE \'%"in_regions_states"%\' OR
					m.meta_value LIKE \'%"ex_states"%\' OR
					m.meta_value LIKE \'%"ex_regions_states"%\' OR
					m.meta_value LIKE \'%"in_zipcodes"%\' OR
					m.meta_value LIKE \'%"in_regions_zips"%\' OR
					m.meta_value LIKE \'%"ex_zipcodes"%\' OR
					m.meta_value LIKE \'%"ex_regions_zips"%\'
				);
		';

		$posts_ids = $wpdb->get_col( $query );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'Fusion Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
		}

		update_option( 'geot_wpbeaver_upgrade_3401_prepare', $posts_ids );

		return [ 'status' => 'ok' ];
	}

	/**
	 * Action upgrade 340
	 * @return mixed
	 */
	public static function upgrade_3401_action() {
		
		$posts_ids = get_option( 'geot_wpbeaver_upgrade_3401_prepare', false );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'Fusion Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
		}

		$j = 0;
		$aux_post_ids = $posts_ids;

		foreach( $aux_post_ids as $key => $post_id ) {

			if( $j > self::$repeat_limit )
				break;
			
			// Clear WP cache for next step.
			wp_cache_flush();
			
			$post_data = get_post_meta( $post_id, '_fl_builder_data', true );
			$post_settings = get_post_meta( $post_id, '_fl_builder_data_settings', true );

			if( empty( $post_data ) || empty( $post_settings ) ) {
				unset( $posts_ids[ $key ] );
				continue;
			}
			
			foreach( $post_data as &$data ) {
				if( ! isset( $data->settings ) )
					continue;

				// Countries
				$data->settings->countries_mode = 'include';
				$data->settings->countries_input = '';
				$data->settings->countries_region = [];

				// Countries Exclude
				if( isset( $data->settings->ex_countries ) ) {

					if( ! empty( $data->settings->ex_countries ) ) {
						$data->settings->countries_input = $data->settings->ex_countries;
						$data->settings->countries_mode = 'exclude';
					}

					unset( $data->settings->ex_countries );
				}

				// Countries Regions Exclude
				if( isset( $data->settings->ex_region_countries ) ) {

					if( ! empty( $data->settings->ex_region_countries ) ) {
						$data->settings->countries_region = $data->settings->ex_region_countries;
						$data->settings->countries_mode = 'exclude';
					}

					unset( $data->settings->ex_region_countries );
				}

				// Countries Include
				if( isset( $data->settings->in_countries ) ) {

					if( ! empty( $data->settings->in_countries ) ) {
						$data->settings->countries_input = $data->settings->in_countries;
						$data->settings->countries_mode = 'include';
					}

					unset( $data->settings->in_countries );
				}

				// Countries Regions Exclude
				if( isset( $data->settings->in_region_countries ) ) {

					if( ! empty( $data->settings->in_region_countries ) ) {
						$data->settings->countries_region = $data->settings->in_region_countries;
						$data->settings->countries_mode = 'include';
					}

					unset( $data->settings->in_region_countries );
				}

				// Initialize Cities
				$data->settings->cities_mode = 'include';
				$data->settings->cities_input = '';
				$data->settings->cities_region = [];

				// Cities Exclude
				if( isset( $data->settings->ex_cities ) ) {

					if( ! empty( $data->settings->ex_cities ) ) {
						$data->settings->cities_input = $data->settings->ex_cities;
						$data->settings->cities_mode = 'exclude';
					}

					unset( $data->settings->ex_cities );
				}

				// Cities Regions Exclude
				if( isset( $data->settings->ex_region_cities ) ) {

					if( ! empty( $data->settings->ex_region_cities ) ) {
						$data->settings->cities_region = $data->settings->ex_region_cities;
						$data->settings->cities_mode = 'exclude';
					}

					unset( $data->settings->ex_region_cities );
				}

				// Cities Include
				if( isset( $data->settings->in_cities ) ) {

					if( ! empty( $data->settings->in_cities ) ) {
						$data->settings->cities_input = $data->settings->in_cities;
						$data->settings->cities_mode = 'include';
					}

					unset( $data->settings->in_cities );
				}

				// cities Regions Exclude
				if( isset( $data->settings->in_region_cities ) ) {

					if( ! empty( $data->settings->in_region_cities ) ) {
						$data->settings->cities_region = $data->settings->in_region_cities;
						$data->settings->cities_mode = 'include';
					}

					unset( $data->settings->in_region_cities );
				}


				// States
				$data->settings->states_mode = 'include';
				$data->settings->states_input = '';
				$data->settings->states_region = [];

				// states Exclude
				if( isset( $data->settings->ex_states ) ) {

					if( ! empty( $data->settings->ex_states ) ) {
						$data->settings->states_input = $data->settings->ex_states;
						$data->settings->states_mode = 'exclude';
					}

					unset( $data->settings->ex_states );
				}

				// states Regions Exclude
				if( isset( $data->settings->ex_region_states ) ) {

					if( ! empty( $data->settings->ex_region_states ) ) {
						$data->settings->states_region = $data->settings->ex_region_states;
						$data->settings->states_mode = 'exclude';
					}

					unset( $data->settings->ex_region_states );
				}

				// states Include
				if( isset( $data->settings->in_states ) ) {

					if( ! empty( $data->settings->in_states ) ) {
						$data->settings->states_input = $data->settings->in_states;
						$data->settings->states_mode = 'include';
					}

					unset( $data->settings->in_states );
				}

				// states Regions Exclude
				if( isset( $data->settings->in_region_states ) ) {

					if( ! empty( $data->settings->in_region_states ) ) {
						$data->settings->states_region = $data->settings->in_region_states;
						$data->settings->states_mode = 'include';
					}

					unset( $data->settings->in_region_states );
				}


				// Zipcodes
				$data->settings->zipcodes_mode = 'include';
				$data->settings->zipcodes_input = '';
				$data->settings->zipcodes_region = [];

				// zipcodes Exclude
				if( isset( $data->settings->ex_zipcodes ) ) {

					if( ! empty( $data->settings->ex_zipcodes ) ) {
						$data->settings->zipcodes_input = $data->settings->ex_zipcodes;
						$data->settings->zipcodes_mode = 'exclude';
					}

					unset( $data->settings->ex_zipcodes );
				}

				// zipcodes Regions Exclude
				if( isset( $data->settings->ex_region_zips ) ) {

					if( ! empty( $data->settings->ex_region_zips ) ) {
						$data->settings->zipcodes_region = $data->settings->ex_region_zips;
						$data->settings->zipcodes_mode = 'exclude';
					}

					unset( $data->settings->ex_region_zips );
				}

				// zipcodes Include
				if( isset( $data->settings->in_zipcodes ) ) {

					if( ! empty( $data->settings->in_zipcodes ) ) {
						$data->settings->zipcodes_input = $data->settings->in_zipcodes;
						$data->settings->zipcodes_mode = 'include';
					}

					unset( $data->settings->in_zipcodes );
				}

				// zipcodes Regions Include
				if( isset( $data->settings->in_region_zips ) ) {

					if( ! empty( $data->settings->in_region_zips ) ) {
						$data->settings->zipcodes_region = $data->settings->in_region_zips;
						$data->settings->zipcodes_mode = 'include';
					}

					unset( $data->settings->in_region_zips );
				}

				// Radius
				$data->settings->radius_mode = 'include';
			}


			// Countries
			if( isset( $post_settings->in_countries ) )
				unset( $post_settings->in_countries );

			if( isset( $post_settings->in_region_countries ) )
				unset( $post_settings->in_region_countries );

			if( isset( $post_settings->ex_countries ) )
				unset( $post_settings->ex_countries );

			if( isset( $post_settings->ex_region_countries ) )
				unset( $post_settings->ex_region_countries );


			// Cities
			if( isset( $post_settings->in_cities ) )
				unset( $post_settings->in_cities );

			if( isset( $post_settings->in_region_cities ) )
				unset( $post_settings->in_region_cities );

			if( isset( $post_settings->ex_cities ) )
				unset( $post_settings->ex_cities );

			if( isset( $post_settings->ex_region_cities ) )
				unset( $post_settings->ex_region_cities );


			// states
			if( isset( $post_settings->in_states ) )
				unset( $post_settings->in_states );

			if( isset( $post_settings->in_region_states ) )
				unset( $post_settings->in_region_states );

			if( isset( $post_settings->ex_states ) )
				unset( $post_settings->ex_states );

			if( isset( $post_settings->ex_region_states ) )
				unset( $post_settings->ex_region_states );


			// Zipcodes
			if( isset( $post_settings->in_zipcodes ) )
				unset( $post_settings->in_zipcodes );

			if( isset( $post_settings->in_region_zips ) )
				unset( $post_settings->in_region_zips );

			if( isset( $post_settings->ex_zipcodes ) )
				unset( $post_settings->ex_zipcodes );

			if( isset( $post_settings->ex_region_zips ) )
				unset( $post_settings->ex_region_zips );

			$post_settings->countries_mode = '';
			$post_settings->countries_input = '';
			$post_settings->countries_regions = '';

			$post_settings->cities_mode = '';
			$post_settings->cities_input = '';
			$post_settings->cities_regions = '';

			$post_settings->states_mode = '';
			$post_settings->states_input = '';
			$post_settings->states_regions = '';

			$post_settings->zipcodes_mode = '';
			$post_settings->zipcodes_input = '';
			$post_settings->zipcodes_regions = '';

			$post_settings->radius_mode = '';

			update_post_meta( $post_id, '_fl_builder_data', $post_data );
			update_post_meta( $post_id, '_fl_builder_draft', $post_data );

			update_post_meta( $post_id, '_fl_builder_data_settings', $post_settings );
			update_post_meta( $post_id, '_fl_builder_draft_settings', $post_settings );
			
			// Clear WP cache for next step.
			wp_cache_flush();

			// Remove ID
			unset( $posts_ids[ $key ] );

			$j++;
		}

		if( ! empty( $posts_ids ) ) {
			update_option( 'geot_wpbeaver_upgrade_3401_prepare', $posts_ids );
			return [ 'status' => 'repeat' ];
		}

		delete_option( 'geot_wpbeaver_upgrade_3401_prepare' );
		return [ 'status' => 'ok' ];
	}
}