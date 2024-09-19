<?php

/**
 * Grab geot settings
 * @return mixed|void
 */
function geotwp_settings() {
	return apply_filters( 'geot_pro/settings_page/opts', get_option( 'geot_pro_settings' ) );
}

/**
 * Get Geot Addons
 * @return ARRAY $opts
 */
function geotwp_addons() {
	$defaults = apply_filters( 'geot/addons/defaults', [
		'geo-flags'     => '0',
		'geo-links'     => '0',
		'geo-redirects' => '0',
		'geo-blocker'   => '0',
	] );
	$opts = get_option( 'geot_pro_addons' );
	$opts = geotwp_parse_args( $opts, $defaults );
	return apply_filters( 'geot_pro/settings_page/addons', $opts );
}

/**
 * Get Geot Stats
 * @return ARRAY $opts
 */
function geotwp_others() {
	$defaults = apply_filters( 'geot/others/defaults', [
		'geo-stats'     => 'no',
	] );
	$opts = get_option( 'geot_pro_others' );
	$opts = geotwp_parse_args( $opts, $defaults );
	return apply_filters( 'geot_pro/settings_page/others', $opts );
}


/**
 * Intercept Geot
 *
 * @param $geot
 *
 * @return mixed
 */
function geotwp_format( $geot ) {
	$output = [];
	foreach ( geotwp_default() as $key => $value ) {
		if ( isset( $geot[ $key ] ) ) {
			$output[ $key ] = is_array( $geot[ $key ] ) ? array_map( 'esc_html', $geot[ $key ] ) : esc_html( $geot[ $key ] );
		} else {
			$output[ $key ] = $value;
		}
	}

	return $output;
}

/**
 * @return mixed|void
 */
function geotwp_default() {
	$default = [
		'countries_mode'	=> 'include',
		'countries_input'	=> '',
		'countries_region'	=> [],
		'cities_mode'		=> 'include',
		'cities_input'		=> '',
		'cities_region'		=> [],
		'states_mode'		=> 'include',
		'states_input'		=> '',
		'states_region'		=> [],
		'zipcodes_mode'		=> 'include',
		'zipcodes_input'	=> '',
		'zipcodes_region'	=> [],
		'radius_mode'		=> 'include',
		'radius_km'			=> '100',
		'radius_lat'		=> '',
		'radius_lng'		=> '',
	];

	return apply_filters( 'geot_pro/global/default', $default );
}


function geotwp_parse_args( &$a, $b ) {
	$a      = (array) $a;
	$b      = (array) $b;
	$result = $b;
	foreach ( $a as $k => &$v ) {
		if ( is_array( $v ) && isset( $result[ $k ] ) ) {
			$result[ $k ] = geotwp_parse_args( $v, $result[ $k ] );
		} else {
			$result[ $k ] = $v;
		}
	}

	return $result;
}

function geotwp_version_compare( $version1, $version2, $operator = null ) {
	$p        = '#(\.0+)+($|-)#';
	$version1 = preg_replace( $p, '', $version1 );
	$version2 = preg_replace( $p, '', $version2 );

	return isset( $operator ) ?
		version_compare( $version1, $version2, $operator ) :
		version_compare( $version1, $version2 );
}


/**
 * Geot SQL to upgrade
 * @param  ARRAY $args
 * @return mixed
 */
function geotwp_update_like($args = []) {
	global $wpdb;

	if( empty($args) || count($args) == 0 )
		return;	

	$ini_find 		= isset( $args['ini_find'] ) ? $args['ini_find'] : '';
	$ini_replace 	= isset( $args['ini_replace'] ) ? $args['ini_replace'] : '';
	$fin_find 		= isset( $args['fin_find'] ) ? $args['fin_find'] : '';
	$fin_replace 	= isset( $args['fin_replace'] ) ? $args['fin_replace'] : '';
	$like 			= isset( $args['like'] ) ? $args['like'] : '';
	$notlike 		= isset( $args['notlike'] ) ? $args['notlike'] : '';

	$update = 'UPDATE
					'.$wpdb->posts.'
				SET
					post_content = REPLACE(post_content, %s, %s),
					post_content = REPLACE(post_content, %s, %s)
				WHERE
					post_content LIKE "%s" AND
					post_content NOT LIKE "%s"';

	$query = $wpdb->prepare($update, $ini_find, $ini_replace, $fin_find, $fin_replace, $like, $notlike);
	
	$wpdb->query($query);
}

/**
 * Geot replace spaces by hyphen
 * @param  STRING $string
 * @return STRING
 */
function geotwp_spaces_by_hyphen($string) {

	return str_replace(' ', '-', $string);
}


if( ! function_exists('geot_dropdown') ) {
	/**
	 * Display Widget with flags
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	function geot_dropdown( $atts ) {

		extract( shortcode_atts( [
			'regions' => '',
			'flags'   => 1,
		], $atts ) );

		$region_ids    = [];
		$flags_id      = 1;
		$saved_regions = geot_country_regions();
		$regions       = ! empty( $regions ) ? array_map('trim', explode( ',', $regions ) ) : [];


		if ( ! empty( $flags ) ) {
			switch ( $flags ) {
				case 'no' :
					$flags_id = '';
					break;
				default:
					$flags_id = 1;
					break;
			}
		}

		if ( ! empty( $regions ) && ! empty( $saved_regions ) ) {

			$all_regions = wp_list_pluck( $saved_regions, 'name' );

			foreach ( $regions as $nregion ) {

				if ( is_numeric( $nregion ) ) {
					$region_ids[] = (int) $nregion;
				} else {
					$region_ids[] = (int) array_search( $nregion, $all_regions );
				}
			}
		}

		$instance = [
			'flags'   => $flags_id,
			'regions' => $region_ids,
		];

		$args = [ 'before_widget' => '', 'after_widget' => '' ];


		ob_start();
		the_widget( 'GeotWP_Widget', $instance, $args );
		$output = ob_get_clean();

		return $output;
	}
}

/**
 * @return bool
 */
function geot_is_local() {
	$opts = geot_settings();
	if (
		( isset( $opts['wpengine'] ) && $opts['wpengine'] == '1' )
		|| ( isset( $opts['maxmind'] ) && $opts['maxmind'] == '1' )
		|| ( isset( $opts['ip2location'] ) && $opts['ip2location'] == '1' )
		|| ( isset( $opts['kinsta'] ) && $opts['kinsta'] == '1' )
		|| ( isset( $opts['litespeed'] ) && $opts['litespeed'] == '1' )
		|| ( isset( $opts['hosting_db'] ) && $opts['hosting_db'] == '1' )
	) {
		return true;
	}
	return false;
}


function geot_current_admin_url() {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );

	if ( ! $uri )
		return '';

	return remove_query_arg( array( '_wpnonce', '_wc_notice_nonce', 'wc_db_update', 'wc_db_update_nonce', 'wc-hide-notice' ), admin_url( $uri ) );
}


/**
 * Replace only the first match
 * @param  string $search 
 * @param  string $replace
 * @param  string $subject
 * @return string
 */
function geotReplaceFirst( string $search, string $replace, string $subject ): string {
    $search = '/'.preg_quote( $search, '/' ).'/';
    return preg_replace( $search, $replace, $subject, 1 );
}