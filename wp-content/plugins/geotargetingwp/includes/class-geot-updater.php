<?php

use GeotWP\Exception\InvalidLicenseException;
use GeotWP\Exception\InvalidSubscriptionException;
use GeotWP\GeotargetingWP;
use GeotCore\GeotUpdates;
use function GeotCore\is_rest_request;

/**
 * Fired during plugin updating
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 */

/**
 * Fired during plugin updating.
 *
 * This class defines all code necessary to run during the plugin's updating.
 *
 * @since      1.0.0
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Your Name <email@example.com>
 */
class GeotWP_Updater {

	static $error_limit = 20;

	static $notice_custom = false;
	static $notice_params = [];

	private static $db_updates = [
		'1.8.0' => [
			'geot_upgrade_180',
			'geot_update_180_db_version'
		],
		'2.6.0' => [
			'geot_upgrade_260',
			'geot_update_260_db_version'
		],
		'3.4.0.2' => [
			'geot_upgrade_3401_remove_actions',
			'update_wp_rocket_cache',
			'update_disable_remove_post',
			'geot_upgrade_3401_taxonomies',
			'geot_upgrade_3401_builder_prepare',
			'geot_upgrade_3401_builder_action',
			'geot_update_3401_db_version'
		],
	];


	private static $db_notices = [
		'3.3.8.2' => 'geot_notice_3382'
	];

	public function __construct() {

		add_action( 'init', [ __CLASS__, 'check_version' ], 5 );
		add_action( 'admin_init', [ __CLASS__, 'install_actions' ] );

		// Notices
		add_action( 'admin_notices', [ __CLASS__, 'update_notice' ] );
		add_action( 'wp_loaded', [ __CLASS__, 'hide_notices' ] );

		// Run update
		add_action( 'geot_run_update_callback', [ __CLASS__, 'run_update_callback' ] );
		// Run Daily check
		add_action( 'geot_daily_check', [ __CLASS__, 'geot_daily_check_callback' ]);
	}


	public static function check_version() {
		// Load action scheduler
		require_once GEOWP_PLUGIN_DIR . 'vendor/woocommerce/action-scheduler/action-scheduler.php';

		if (
			( defined( 'DOING_AJAX' ) && DOING_AJAX )
		     || is_rest_request()
		     || isset( $_GET['wc_ajax'] )
		     || isset( $_GET['wc-ajax'] )
		     || ( defined( 'REST_REQUEST' ) && REST_REQUEST )
		) {
			return;
		}
		$opts = geot_settings();

		// Upgrade functions
		include_once GEOWP_PLUGIN_DIR . 'updater/geot-update-functions.php';

		// Setup the plugin updater
		$GeoUpdate = new GeotUpdates( GEOWP_PLUGIN_FILE, [
				'version' => GEOWP_VERSION,
				'license' => isset( $opts['license'] ) ? $opts['license'] : '',
			]
		);

		if( version_compare( get_option( 'geot_version' ), GEOWP_VERSION, '<' ) ) {

			self::update_geot_version();
			self::maybe_update_db_version();
			do_action( 'geot_updated' );
		}

		self::daily_check();
	}

	private static function daily_check(){
		if ( false === as_has_scheduled_action( 'geot_daily_check' ) ) {
			as_schedule_recurring_action( strtotime( 'tomorrow' ), DAY_IN_SECONDS, 'geot_daily_check', [], '', true );
		}
	}

	public static function geot_daily_check_callback() {

			if ( ! geot_is_local() ) {
				return true;
			}
			$opts = geot_settings();
			// It wasn't there, so regenerate the data and save the transient
			$active_user = GeotargetingWP::checkSubscription( $opts['license'] );
			$result      = json_decode( $active_user );
			if ( ! isset( $result->success ) ) {
				if( defined('GEOT_DEBUG') ) {
					error_log('geotargeting-wp'. print_r( $result, 1 ) );
				}
				$total = get_option('geot_inactive_user', 0);
				$total++;
				update_option( 'geot_inactive_user', $total );
			} else {
				update_option( 'geot_inactive_user', 0 );
			}

			return true;
	}

	/**
	 * See if we need to show or run database updates during install.
	 *
	 * @since 3.2.0
	 */
	private static function maybe_update_db_version() {

		if( self::needs_db_update() || self::needs_db_notice() )
			update_option( 'geot_notice_update', true );
		else
			self::update_db_version();
	}


	/**
	 * Update WC version to current.
	 */
	public static function update_db_version( $version = null ) {
		update_option( 'geot_db_version', is_null( $version ) ? GEOWP_VERSION : $version );
	}

	/**
	 * Update WC version to current.
	 */
	private static function update_geot_version() {
		update_option( 'geot_version', GEOWP_VERSION );
	}


	/**
	 * Install actions when a update button is clicked within the admin area.
	 *
	 * This function is hooked into admin_init to affect admin only.
	 */
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_geot'] ) ) { // WPCS: input var ok.
			check_admin_referer( 'geot_db_update', 'geot_db_update_nonce' );
			self::update();
			update_option( 'geot_notice_update', true );
		}
	}


	/**
	 * Run an update callback when triggered by ActionScheduler.
	 *
	 * @param string $update_callback Callback name.
	 *
	 * @since 3.6.0
	 */
	public static function run_update_callback( $update_callback ) {

		if ( is_callable( $update_callback ) ) {
			self::run_update_callback_start( $update_callback );
			$result = call_user_func( $update_callback );
			self::run_update_callback_end( $update_callback, $result );
		}
	}

	/**
	 * Triggered when a callback will run.
	 *
	 * @since 3.6.0
	 * @param string $callback Callback name.
	 */
	protected static function run_update_callback_start( $callback ) {
		if( ! defined( 'GEOT_UPDATING' ) )
			define( 'GEOT_UPDATING', true );
	}

	/**
	 * Triggered when a callback has ran.
	 *
	 * @since 3.6.0
	 * @param string $callback Callback name.
	 * @param bool   $result Return value from callback. True : it is ok, False : run again.
	 */
	protected static function run_update_callback_end( $callback, $result ) {

		switch( $result['status'] ) {

			case 'repeat' :

				as_schedule_single_action(
					time(),
					'geot_run_update_callback',
					[ 'update_callback' => $callback ],
					'geot-db-updates'
				);
				break;

			case 'error' :
				$errors = get_option( 'geot_updater_error', [] );
				$errors[] = [ 'date' => date( 'Y-m-d H:i:s' ), 'msg' => $result['msg'] ];

				if( count( $errors ) > self::$error_limit )
					array_shift( $errors );

				update_option( 'geot_updater_error', $errors );
				break;
		}
	}


	/**
	 * Is a DB update needed?
	 *
	 * @since  3.2.0
	 * @return boolean
	 */
	public static function needs_db_update() {
		$current_db_version = get_option( 'geot_db_version', null );
		$updates            = self::get_db_update_callbacks();
		$update_versions    = array_keys( $updates );
		usort( $update_versions, 'version_compare' );

		if( is_null( $current_db_version ) ||  version_compare( $current_db_version, end( $update_versions ), '<' ) )
			return true;
		return false;
	}


	/**
	 * Is a DB update needed?
	 *
	 * @since  3.2.0
	 * @return boolean
	 */
	public static function needs_db_notice() {
		$current_version 	= get_option( 'geot_version', null );
		$notices            = self::get_db_notice_callbacks();

		return isset( $notices[ $current_version ] );
	}
	

	/**
	 * Get list of DB update callbacks.
	 *
	 * @since  3.0.0
	 * @return array
	 */
	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}


	/**
	 * Get list of DB notices callbacks.
	 *
	 * @since  3.0.0
	 * @return array
	 */
	public static function get_db_notice_callbacks() {
		return self::$db_notices;
	}


	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	private static function update() {
		$current_db_version = get_option( 'geot_db_version' );
		$loop               = 0;

		foreach( self::get_db_update_callbacks() as $version => $update_callbacks ) {
			if( version_compare( $current_db_version, $version, '<' ) ) {

				foreach( $update_callbacks as $update_callback ) {

					as_schedule_single_action(
						time() + $loop,
						'geot_run_update_callback',
						[ 'update_callback' => $update_callback ],
						'geot-db-updates'
					);

					$loop++;
				}
			}
		}
	}


	/**
	 * Custom notice
	 * @return mixed
	 */
	public static function update_notice() {

		if( ! get_option( 'geot_notice_update', false ) )
			return;

		$version 	= get_option( 'geot_version' );
		$notices 	= self::get_db_notice_callbacks();

		if( isset( $notices[ $version ] ) ) {
			call_user_func( $notices[ $version ] );
			return;
		}


		if( self::needs_db_update() ) {

			$next = as_next_scheduled_action( 'geot_run_update_callback', null, 'geot-db-updates' );

			if( $next === true || is_numeric( $next ) || ! empty( $_GET['do_update_geot'] ) )
				include GEOWP_PLUGIN_DIR . 'updater/views/html-notice-updating.php';
			else
				include GEOWP_PLUGIN_DIR . 'updater/views/html-notice-update.php';
		} else {
			include GEOWP_PLUGIN_DIR . 'updater/views/html-notice-updated.php';
		}
	}


	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function hide_notices() {
		if ( isset( $_GET['geot-hide-notice'] ) && isset( $_GET['_geot_notice_nonce'] ) ) { // WPCS: input var ok, CSRF ok.
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_geot_notice_nonce'] ) ), 'geot_hide_notices_nonce' ) ) { // WPCS: input var ok, CSRF ok.
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'geot' ) );
			}

			if ( ! current_user_can( 'administrator' ) ) {
				wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'geot' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['geot-hide-notice'] ) ); // WPCS: input var ok, CSRF ok.
			
			delete_option( 'geot_notice_update' );
		}

		return true;
	}
}

new GeotWP_Updater();