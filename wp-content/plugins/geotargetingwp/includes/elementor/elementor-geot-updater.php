<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Elementor Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */
class GeotWP_Elementor_Updater {

	public static $repeat_limit = 20;

	/**
	 * Prepare upgrade 340
	 * @return mixed
	 */
	public static function upgrade_3401_prepare() {

		global $wpdb;
		// we split in several queries because some db are timingout

		$values_to_search = [
			'in_countries',
			'in_regions',
			'ex_countries',
			'ex_regions',
			'in_cities',
			'in_regions_cities',
			'ex_cities',
			'ex_regions_cities',
			'in_states',
			'in_regions_states',
			'ex_states',
			'ex_regions_states',
			'in_zipcodes',
			'in_regions_zips',
			'ex_zipcodes',
			'ex_regions_zips'
		];
		$posts_ids = [];
		foreach ( $values_to_search as $search ) {
			$query = 'SELECT
				p.ID
			FROM
				' . $wpdb->posts . ' AS p
			INNER JOIN
				' . $wpdb->postmeta . ' AS m
			ON
				p.ID = m.post_id
			WHERE
				p.post_status = "publish" AND
				m.meta_key = "_elementor_data" AND 
					m.meta_value LIKE \'%"'.$search.'"%\';';

			$posts_ids = array_merge( $posts_ids, $wpdb->get_col( $query ) );
		}

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'Elementor Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
		}

		update_option( 'geot_elementor_upgrade_3401_prepare', $posts_ids );

		return [ 'status' => 'ok' ];
	}

	/**
	 * Action upgrade 340
	 * @return mixed
	 */
	public static function upgrade_3401_action() {

		$posts_ids = get_option( 'geot_elementor_upgrade_3401_prepare', false );

		if( empty( $posts_ids ) ) {
			throw new Exception( esc_html__( 'Elementor Updater: there is no posts to update ( version: 3.4.0.1 )', 'geot' ) );
		}

		$j = 0;
		$aux_post_ids = $posts_ids;

		foreach( $aux_post_ids as $key => $post_id ) {

			if( $j > self::$repeat_limit )
				break;

			// Clear WP cache for next step.
			wp_cache_flush();

			$do_update = false;

			$document = Elementor\Plugin::$instance->documents->get( $post_id );

			if ( ! $document )
				continue;

			$data = $document->get_elements_data();

			if ( empty( $data ) )
				continue;

			$data = Elementor\Plugin::$instance->db->iterate_data( $data, function( $element ) use ( &$do_update ) {

				// Countries
				if( ! empty( $element['settings']['ex_countries'] ) ) {
					$element['settings']['countries_input'] = $element['settings']['ex_countries'];

					$do_update = true;
					$countries_mode = 'exclude';
					unset( $element['settings']['ex_countries'] );
				}

				if( ! empty( $element['settings']['ex_regions'] ) ) {
					$element['settings']['countries_regions'] = $element['settings']['ex_regions'];

					$do_update = true;
					$countries_mode = 'exclude';
					unset( $element['settings']['ex_regions'] );
				}

				if( ! empty( $element['settings']['in_countries'] ) ) {
					$element['settings']['countries_input'] = $element['settings']['in_countries'];
					
					$do_update = true;
					$countries_mode = 'include';
					unset( $element['settings']['in_countries'] );
				}

				if( ! empty( $element['settings']['in_regions'] ) ) {
					$element['settings']['countries_regions'] = $element['settings']['in_regions'];
					
					$do_update = true;
					$countries_mode = 'include';
					unset( $element['settings']['in_regions'] );
				}

				if( isset( $countries_mode ) ) {
					$element['settings']['countries_mode'] = $countries_mode;
					unset( $countries_mode );
				}


				// Cities
				if( ! empty( $element['settings']['ex_cities'] ) ) {
					$element['settings']['cities_input'] = $element['settings']['ex_cities'];
					
					$do_update = true;
					$cities_mode = 'exclude';
					unset( $element['settings']['ex_cities'] );
				}

				if( ! empty( $element['settings']['ex_regions_cities'] ) ) {
					$element['settings']['cities_regions'] = $element['settings']['ex_regions_cities'];
					
					$do_update = true;
					$cities_mode = 'exclude';
					unset( $element['settings']['ex_regions_cities'] );
				}

				if( ! empty( $element['settings']['in_cities'] ) ) {
					$element['settings']['cities_input'] = $element['settings']['in_cities'];
					
					$do_update = true;
					$cities_mode = 'include';
					unset( $element['settings']['in_cities'] );
				}

				if( ! empty( $element['settings']['in_regions_cities'] ) ) {
					$element['settings']['cities_regions'] = $element['settings']['in_regions_cities'];
					
					$do_update = true;
					$cities_mode = 'include';
					unset( $element['settings']['in_regions_cities'] );
				}

				if( isset( $cities_mode ) ) {
					$element['settings']['cities_mode'] = $cities_mode;
					unset( $cities_mode );
				}


				// States
				if( ! empty( $element['settings']['ex_states'] ) ) {
					$element['settings']['states_input'] = $element['settings']['ex_states'];
					
					$do_update = true;
					$states_mode = 'exclude';
					unset( $element['settings']['ex_states'] );
				}

				if( ! empty( $element['settings']['ex_regions_states'] ) ) {
					$element['settings']['states_regions'] = $element['settings']['ex_regions_states'];
					
					$do_update = true;
					$states_mode = 'exclude';
					unset( $element['settings']['ex_regions_states'] );
				}

				if( ! empty( $element['settings']['in_states'] ) ) {
					$element['settings']['states_input'] = $element['settings']['in_states'];
					
					$do_update = true;
					$states_mode = 'include';
					unset( $element['settings']['in_states'] );
				}

				if( ! empty( $element['settings']['in_regions_states'] ) ) {
					$element['settings']['states_regions'] = $element['settings']['in_regions_states'];
					
					$do_update = true;
					$states_mode = 'include';
					unset( $element['settings']['in_regions_states'] );
				}

				if( isset( $states_mode ) ) {
					$element['settings']['states_mode'] = $states_mode;
					unset( $states_mode );
				}
				

				// Zipcodes
				if( ! empty( $element['settings']['ex_zipcodes'] ) ) {
					$element['settings']['zipcodes_input'] = $element['settings']['ex_zipcodes'];
					
					$do_update = true;
					$zipcodes_mode = 'exclude';
					unset( $element['settings']['ex_zipcodes'] );
				}

				if( ! empty( $element['settings']['ex_regions_zips'] ) ) {
					$element['settings']['zipcodes_regions'] = $element['settings']['ex_regions_zips'];
					
					$do_update = true;
					$zipcodes_mode = 'exclude';
					unset( $element['settings']['ex_regions_zips'] );
				}

				if( ! empty( $element['settings']['in_zipcodes'] ) ) {
					$element['settings']['zipcodes_input'] = $element['settings']['in_zipcodes'];
					
					$do_update = true;
					$zipcodes_mode = 'include';
					unset( $element['settings']['in_zipcodes'] );
				}

				if( ! empty( $element['settings']['in_regions_zips'] ) ) {
					$element['settings']['zipcodes_regions'] = $element['settings']['in_regions_zips'];
					
					$do_update = true;
					$zipcodes_mode = 'include';
					unset( $element['settings']['in_regions_zips'] );
				}

				if( isset( $zipcodes_mode ) ) {
					$element['settings']['zipcodes_mode'] = $zipcodes_mode;
					unset( $zipcodes_mode );
				}

				return $element;
			} );

			// Remove ID
			unset( $posts_ids[ $key ] );

			$j++;
	
			// Clear WP cache for next step.
			wp_cache_flush();

			// Only update if needed.
			if ( ! $do_update ) {
				continue;
			}

			// We need the `wp_slash` in order to avoid the unslashing during the `update_post_meta`
			$json_value = wp_slash( wp_json_encode( $data ) );

			update_metadata( 'post', $post_id, '_elementor_data', $json_value );
		}


		if( ! empty( $posts_ids ) ) {
			update_option( 'geot_elementor_upgrade_3401_prepare', $posts_ids );

			return [ 'status' => 'repeat' ];
		}

		delete_option( 'geot_elementor_upgrade_3401_prepare' );
		return [ 'status' => 'ok' ];
	}
}