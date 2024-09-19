<?php

/**
 * Update 1.8.0 version
 * Add mising _geot_post introduced in 1.8 to old posts
 * @return mixed
 */
function geot_upgrade_180() {
	global $wpdb;

	// grab all publish posts without _geot_post postmeta
	$posts = $wpdb->get_results( "SELECT p.ID, pm.meta_value as geot_options FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID  WHERE p.post_status = 'publish' AND pm.meta_key = 'geot_options'  AND p.ID NOT IN (  SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_geot_post' GROUP BY post_id ) " );
	// Loop all posts and check if( !empty( $opts['country_code'] ) || !empty( $opts['region'] ) || !empty( $opts['cities'] ) || !empty( $opts['state'] ) )
	$to_migrate = [];
	if ( $posts ) {
		foreach ( $posts as $p ) {
			$opts = unserialize( $p->geot_options );
			if ( ! empty( $opts['country_code'] ) || ! empty( $opts['region'] ) || ! empty( $opts['cities'] ) || ! empty( $opts['state'] ) ) {
				$to_migrate[] = $p->ID;
			}
		}
	}
	// Save post meta to those posts
	if ( ! empty( $to_migrate ) ) {
		$sql_string = [];
		foreach ( $to_migrate as $id ) {
			$sql_string[] = "('$id', '_geot_post', '1' )";
		}
		$sql = "INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value) VALUES " . implode( ',', $sql_string ) . ";";

		$wpdb->query( $sql );
	}

	return [ 'status' => 'ok' ];
}

/**
 * Update 1.8.0 version
 * @return mixed
 */
function geot_update_180_db_version() {
	GeotWP_Updater::update_db_version( '1.8.0' );
	return [ 'status' => 'ok' ];
}

/**
 * Update 2.6.0 version
 * @return mixed
 */
function geot_upgrade_260() {
	global $wpdb;

	$array_insert = [];
	$city_regions = wp_list_pluck( geot_city_regions(), 'name' );

	$geot_posts = GeotWP_Helper::get_geotarget_posts();

	if ( $geot_posts ) {
		foreach ( $geot_posts as $p ) {

			$to_city = $to_region_city = [];
			$opts    = maybe_unserialize( $p->geot_options );

			if ( empty( $opts['cities'] ) || isset( $opts['city_region'] ) ) {
				continue;
			}

			$list_cites = GeotCore\toArray( $opts['cities'] );

			foreach ( $list_cites as $city ) {
				if ( in_array( $city, $city_regions ) ) {
					$to_region_city[] = $city;
				} else {
					$to_city[] = $city;
				}
			}

			if ( count( $to_region_city ) == 0 ) {
				continue;
			}

			$opts['cities']      = implode( ',', $to_city );
			$opts['city_region'] = $to_region_city;

			$options = maybe_serialize( $opts );

			$array_insert[] = '(' . $p->geot_meta_id . ', ' . $p->ID . ', \'geot_options\', \'' . $options . '\')';
		}


		if ( count( $array_insert ) > 0 ) {
			$sql = 'INSERT INTO ' . $wpdb->postmeta . ' (meta_id, post_id, meta_key, meta_value) VALUES ' . implode( ',', $array_insert ) . ' ON DUPLICATE KEY UPDATE meta_value=VALUES(meta_value)';
			$wpdb->query( $sql );
		}

	}

	return [ 'status' => 'ok' ];
}

/**
 * Update 2.6.0 version
 * @return mixed
 */
function geot_update_260_db_version() {
	GeotWP_Updater::update_db_version( '2.6.0' );
	return [ 'status' => 'ok' ];
}

/**
 * Update 3.4.0 version
 * Check this option if cache mode was enabled
 * @return mixed
 */
function update_wp_rocket_cache() {
	$opts = geot_settings();
	if( isset($opts['cache_mode']) && '1' == $opts['cache_mode'] ) {
		$opts['wp_rocket_cache'] = 1;
	}

	update_option('geot_settings', $opts);

	return [ 'status' => 'ok' ];
}


/**
 * disable this option for old users, to not change the current behaviour
 */
function update_disable_remove_post() {
	$opts = geot_settings();
	$opts['disable_remove_post'] = 0;
	update_option('geot_settings', $opts);

	return [ 'status' => 'ok' ];
}

/**
 * Update 3.4.0 version
 * @return mixed
 */
function geot_upgrade_3401_builder_prepare() {

	try {
		// Elementor
		if( did_action( 'elementor/loaded' ) ) {
			$builder = 'elementor';
			$result = GeotWP_Elementor_Updater::upgrade_3401_prepare();
		} elseif( defined( 'WPB_VC_VERSION' ) ) {
			$builder = 'vc';
			$result = GeotWP_VC_Updater::upgrade_3401_prepare();
		} elseif( class_exists( 'FLBuilder' ) ) {
			$builder = 'wpbeaver';
			$result = GeotWP_WPBeaver_Updater::upgrade_3401_prepare();
		} elseif( class_exists( 'FusionBuilder' ) ) {
			$builder = 'fusion';
			$result = GeotWP_Fusion_Updater::upgrade_3401_prepare();
		} elseif( defined( 'ET_BUILDER_VERSION' ) ) {
			$builder = 'divi';
			$result = GeotWP_Divi_Updater::upgrade_3401_prepare();
		} else {
			$builder = 'gutenberg';
			$result = GeotWP_Gutenberg_Updater::upgrade_3401_prepare();
		}
	} catch( Exception $e ) {
		return [ 'status' => 'error', 'msg' => $e->getMessage() ];
	}

	update_option( 'geot_upgrade_3401_builder_running', $builder);

	return $result;
}

function geot_upgrade_3401_builder_action() {

	$builder = get_option( 'geot_upgrade_3401_builder_running' );

	if( $builder === FALSE ) {
		return [ 'status' => 'error', 'msg' => esc_html__( 'There is no builder to update', 'geot' ) ];
	}

	try {
		switch( $builder ) {
			// Elementor
			case 'elementor'	: $result = GeotWP_Elementor_Updater::upgrade_3401_action(); break;
			case 'vc' 			: $result = GeotWP_VC_Updater::upgrade_3401_action(); break;
			case 'wpbeaver' 	: $result = GeotWP_WPBeaver_Updater::upgrade_3401_action(); break;
			case 'fusion' 		: $result = GeotWP_Fusion_Updater::upgrade_3401_action(); break;
			case 'divi' 		: $result = GeotWP_Divi_Updater::upgrade_3401_action(); break;
			default 			: $result = GeotWP_Gutenberg_Updater::upgrade_3401_action();
		}
	} catch( Exception $e ) {

		delete_option( 'geot_upgrade_3401_builder_running' );
		return [ 'status' => 'error', 'msg' => $e->getMessage() ];
	}

	if( $result['status'] == 'repeat' )
		update_option( 'geot_upgrade_3401_builder_running', $builder );
	else
		delete_option( 'geot_upgrade_3401_builder_running' );

	return $result;
}

/**
 * Change taxonomies variables
 * @return mixed
 */
function geot_upgrade_3401_taxonomies() {

	global $wpdb;

	$query = 'SELECT
			term_id
		FROM
			' . $wpdb->termmeta . '
		WHERE
			meta_key = "geot" AND (
				meta_value LIKE \'%"in_countries"%\' OR
				meta_value LIKE \'%"in_countries_regions"%\' OR
				meta_value LIKE \'%"ex_countries"%\' OR
				meta_value LIKE \'%"ex_countries_regions"%\' OR
				meta_value LIKE \'%"in_cities"%\' OR
				meta_value LIKE \'%"in_cities_regions"%\' OR
				meta_value LIKE \'%"ex_cities"%\' OR
				meta_value LIKE \'%"ex_cities_regions"%\' OR
				meta_value LIKE \'%"in_states"%\' OR
				meta_value LIKE \'%"in_states_regions"%\' OR
				meta_value LIKE \'%"ex_states"%\' OR
				meta_value LIKE \'%"ex_states_regions"%\' OR
				meta_value LIKE \'%"in_zipcodes"%\' OR
				meta_value LIKE \'%"in_zips_regions"%\' OR
				meta_value LIKE \'%"ex_zipcodes"%\' OR
				meta_value LIKE \'%"ex_zips_regions"%\'
			);
	';

	$term_ids = $wpdb->get_col( $query );

	foreach( $term_ids as $term_id ) {

		$geot = get_term_meta( $term_id, 'geot', true );

		// Countries input exclude
		if( isset( $geot['ex_countries'] ) ) {

			if( ! empty( $geot['ex_countries'] ) ) {
				$geot['countries_input'] = $geot['ex_countries'];
				$geot['countries_mode'] = 'exclude';
			}

			unset( $geot['ex_countries'] );
		}

		// Countries regions exclude
		if( isset( $geot['ex_countries_regions'] ) ) {

			if( ! empty( $geot['ex_countries_regions'] ) ) {
				$geot['countries_region'] = $geot['ex_countries_regions'];
				$geot['countries_mode'] = 'exclude';
			}

			unset( $geot['ex_countries_regions'] );
		}


		// Countries input include
		if( isset( $geot['in_countries'] ) ) {

			if( ! empty( $geot['in_countries'] ) ) {
				$geot['countries_input'] = $geot['in_countries'];
				$geot['countries_mode'] = 'include';
			}

			unset( $geot['in_countries'] );
		}

		// Countries regions include
		if( isset( $geot['in_countries_regions'] ) ) {

			if( ! empty( $geot['in_countries_regions'] ) ) {
				$geot['countries_region'] = $geot['in_countries_regions'];
				$geot['countries_mode'] = 'include';
			}

			unset( $geot['in_countries_regions'] );
		}



		// cities input exclude
		if( isset( $geot['ex_cities'] ) ) {

			if( ! empty( $geot['ex_cities'] ) ) {
				$geot['cities_input'] = $geot['ex_cities'];
				$geot['cities_mode'] = 'exclude';
			}

			unset( $geot['ex_cities'] );
		}

		// cities regions exclude
		if( isset( $geot['ex_cities_regions'] ) ) {

			if( ! empty( $geot['ex_cities_regions'] ) ) {
				$geot['cities_region'] = $geot['ex_cities_regions'];
				$geot['cities_mode'] = 'exclude';
			}

			unset( $geot['ex_cities_regions'] );
		}


		// cities input include
		if( isset( $geot['in_cities'] ) ) {

			if( ! empty( $geot['in_cities'] ) ) {
				$geot['cities_input'] = $geot['in_cities'];
				$geot['cities_mode'] = 'include';
			}

			unset( $geot['in_cities'] );
		}

		// cities regions include
		if( isset( $geot['in_cities_regions'] ) ) {

			if( ! empty( $geot['in_cities_regions'] ) ) {
				$geot['cities_region'] = $geot['in_cities_regions'];
				$geot['cities_mode'] = 'include';
			}

			unset( $geot['in_cities_regions'] );
		}



		// states input exclude
		if( isset( $geot['ex_states'] ) ) {

			if( ! empty( $geot['ex_states'] ) ) {
				$geot['states_input'] = $geot['ex_states'];
				$geot['states_mode'] = 'exclude';
			}

			unset( $geot['ex_states'] );
		}

		// states regions exclude
		if( isset( $geot['ex_states_regions'] ) ) {

			if( ! empty( $geot['ex_states_regions'] ) ) {
				$geot['states_region'] = $geot['ex_states_regions'];
				$geot['states_mode'] = 'exclude';
			}

			unset( $geot['ex_states_regions'] );
		}


		// states input include
		if( isset( $geot['in_states'] ) ) {

			if( ! empty( $geot['in_states'] ) ) {
				$geot['states_input'] = $geot['in_states'];
				$geot['states_mode'] = 'include';
			}

			unset( $geot['in_states'] );
		}

		// states regions include
		if( isset( $geot['in_states_regions'] ) ) {

			if( ! empty( $geot['in_states_regions'] ) ) {
				$geot['states_region'] = $geot['in_states_regions'];
				$geot['states_mode'] = 'include';
			}

			unset( $geot['in_states_regions'] );
		}


		// zipcodes input exclude
		if( isset( $geot['ex_zipcodes'] ) ) {

			if( ! empty( $geot['ex_zipcodes'] ) ) {
				$geot['zipcodes_input'] = $geot['ex_zipcodes'];
				$geot['zipcodes_mode'] = 'exclude';
			}

			unset( $geot['ex_zipcodes'] );
		}

		// zipcodes regions exclude
		if( isset( $geot['ex_zips_regions'] ) ) {

			if( ! empty( $geot['ex_zips_regions'] ) ) {
				$geot['zipcodes_region'] = $geot['ex_zips_regions'];
				$geot['zipcodes_mode'] = 'exclude';
			}

			unset( $geot['ex_zips_regions'] );
		}


		// zipcodes input include
		if( isset( $geot['in_zipcodes'] ) ) {

			if( ! empty( $geot['in_zipcodes'] ) ) {
				$geot['zipcodes_input'] = $geot['in_zipcodes'];
				$geot['zipcodes_mode'] = 'include';
			}

			unset( $geot['in_zipcodes'] );
		}

		// zipcodes regions include
		if( isset( $geot['in_zips_regions'] ) ) {

			if( ! empty( $geot['in_zips_regions'] ) ) {
				$geot['zipcodes_region'] = $geot['in_zips_regions'];
				$geot['zipcodes_mode'] = 'include';
			}

			unset( $geot['in_zips_regions'] );
		}


		// Radius
		$geot['radius_mode'] = 'include';

		update_term_meta( $term_id, 'geot', $geot );
	}

	return [ 'status' => 'ok' ];
}


function geot_upgrade_3401_remove_actions() {
	global $wpdb;

	$table_actions 	= $wpdb->prefix . 'actionscheduler_actions';
	$table_logs 	= $wpdb->prefix . 'actionscheduler_logs';

	$sql = $wpdb->prepare(
		'SELECT action_id FROM ' . $table_actions . ' WHERE hook = %s && status = %s',
		'geot_run_update_callback',
		'complete'
	);

	$action_ids = $wpdb->get_col( $sql );

	$ids_commas = implode( ',', array_map( 'absint', $action_ids ) );
	if( !empty( $ids_commas ) ) {
		$wpdb->query( 'DELETE FROM ' . $table_actions . ' WHERE action_id IN (' . $ids_commas . ')' );
		$wpdb->query( 'DELETE FROM ' . $table_logs . ' WHERE action_id IN (' . $ids_commas . ')' );
	}
	return [ 'status' => 'ok' ];
}

/**
 * Update 3.4.0 version
 * @return mixed
 */
function geot_update_3401_db_version() {

	$builder = delete_option( 'geot_upgrade_3401_builder_running' );

	if( $builder !== false ) {
		return [ 'status' => 'repeat' ];
	}

	GeotWP_Updater::update_db_version( '3.4.0.2' );
	return [ 'status' => 'ok' ];
}

/**
 * Update 3.3.8.2 version
 * @return mixed
 */
function geot_notice_3382() {

	if( defined( 'ET_BUILDER_VERSION' ) ) {

		echo '<div id="message" class="notice notice notice-info">';
	
			echo '<h2>'.esc_html__( 'Update GeotargetingWP', 'geot' ).'</h2>';
			echo '<p>'.__( '<p>Due to a bug in how Divi handle checkboxes, every time a new country region was added the order was being changed and wrong regions were selected.</p>
<p>Because the bug it\'s open since 2019 without a fix, we changed how we save the regions as a workaround while Divi changes the way they handle checkboxes.</p>
<p>Unfortunately, this means your current country regions may be changed. <strong>Please, review your modules if you are using country regions to check the right ones.</strong></p>', 'geot' ).'</p>';

			echo '<p class="submit">';
			echo '<a class="button button-primary" href="'. esc_url( wp_nonce_url( add_query_arg( 'geot-hide-notice', 'update', remove_query_arg( 'do_update_geot' ) ), 'geot_hide_notices_nonce', '_geot_notice_nonce' ) ). '">'.esc_html__( 'Dismiss', 'geot' ).'</a>';
			echo '</p>';
		echo '</div>';
		
	} else
		delete_option( 'geot_notice_update' );

	return;
}

