<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geobl
 * @subpackage Geobl/public
 */

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use function GeotCore\is_backend;
use function GeotCore\is_builder;
use function GeotCore\is_rest_request;
use function GeotCore\textarea_to_array;
use function GeotWP\getUserIP;
use function GeotWP\is_session_started;

/**
 * @package    Geobl
 * @subpackage Geobl/public
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeotWP_Bl_Public {
	/**
	 * @var bool to ajaxmode
	 */
	public $ajax_call = false;
	/**
	 * @var Array of Redirection posts
	 */
	private $blocks;

	/**
	 * Construct
	 * @return bool
	 */
	public function __construct() {

		if ( ! is_admin() && ! is_backend() && ! defined( 'DOING_AJAX' ) && ! defined( 'DOING_CRON' ) && ! is_builder() ) {
			add_action( apply_filters( 'geotr/action_hook', 'wp' ), [ $this, 'handle_blockers' ] );
		}
		add_action( 'wp_ajax_geo_template', [ $this, 'view_template' ], 1 );
	}


	/**
	 *
	 */
	public function handle_blockers() {

		GeotWP_R_ules::init();
		$this->blocks = $this->get_blocks();
		$opts_geot    = geot_settings();
		if ( ! empty( $opts_geot['ajax_mode'] ) ) {
			add_action( 'wp_footer', [ $this, 'ajax_placeholder' ] );
		} else {
			$this->check_for_rules();
		}
	}

	/**
	 * Grab all blocks posts and associated rules
	 * @return mixed
	 */
	private function get_blocks() {
		global $wpdb;
		$blocks = wp_cache_get('geot.get_blocks', 'geot', false, $found);
		if( ! $found || ! empty( $_GET['geot_debug'] ) ) {
			$sql = "SELECT ID, 
		MAX(CASE WHEN pm1.meta_key = 'geobl_rules' then pm1.meta_value ELSE NULL END) as geobl_rules,
		MAX(CASE WHEN pm1.meta_key = 'geobl_rules_global' then pm1.meta_value ELSE NULL END) as geobl_rules_global,
		MAX(CASE WHEN pm1.meta_key = 'geobl_options' then pm1.meta_value ELSE NULL END) as geobl_options
        FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm1 ON ( pm1.post_id = p.ID)  WHERE post_type='geobl_cpt' AND post_status='publish' GROUP BY p.ID";

			$blocks = $wpdb->get_results( $sql, OBJECT );
			wp_cache_set('geot.get_blocks', $blocks, 'geot', 3600);
		}

		return $blocks;

	}

	/**
	 * Check for rules and block if needed
	 * This will be normal behaviour on site where cache is not active
	 */
	private function check_for_rules() {
		if ( ! empty( $this->blocks ) ) {
			foreach ( $this->blocks as $r ) {
				if ( ! $this->pass_basic_rules( $r ) ) {
					continue;
				}
				$rules          = ! empty( $r->geobl_rules ) ? unserialize( $r->geobl_rules ) : [];
				$global_rules   = ! empty( $r->geobl_rules_global ) ? unserialize( $r->geobl_rules_global ) : [];
				$do_block = GeotWP_R_ules::is_ok( $rules, $global_rules );

				if ( $do_block ) {
					return $this->perform_block( $r );
					break;
				}
			}
		}

		return false;
	}

	/**
	 * Before Even checking rules, we need some basic validation
	 *
	 * @param $block
	 *
	 * @return bool
	 */
	private function pass_basic_rules( $block ) {
		if ( empty( $block->geobl_options ) ) {
			return false;
		}

		$opts = maybe_unserialize( $block->geobl_options );

		// check user IP
		if ( ! empty( $opts['whitelist'] ) && $this->user_is_whitelisted( $opts['whitelist'] ) ) {
			return false;
		}
		// check for crawlers
		if ( isset( $opts['exclude_se'] ) && 1 === absint( $opts['exclude_se'] ) ) {
			$detect = new CrawlerDetect();
			if ( $detect->isCrawler() ) {
				return false;
			}
		}
		// dont block on rest
		if( is_rest_request() ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if current user IP is whitelisted
	 *
	 * @param $ips
	 *
	 * @return bool
	 */
	private function user_is_whitelisted( $ips ) {
		$ips = textarea_to_array( $ips );
		if ( in_array( getUserIP(), apply_filters( 'geobl/whitelist_ips', $ips ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Perform the actual block
	 *
	 * @param $block
	 */
	private function perform_block( $block ) {
		$opts = maybe_unserialize( $block->geobl_options );

		$opts['block_message'] = do_shortcode( $opts['block_message'] );
		//last chance to abort
		if ( ! apply_filters( 'geobl/cancel_block', false, $opts, $block ) ) {

			if ( $this->ajax_call ) {
				return GeotWP_Bl_Helper::get_template( $block->ID );
			} else {
				echo GeotWP_Bl_Helper::get_template( $block->ID );
				die();
			}
		}
	}


	/**
	 * Handle Ajax call for blocks, Basically
	 * we call normal block logic but cancel it and print results
	 */
	public function handle_ajax_blockers() {
		GeotWP_R_ules::init();
		$this->ajax_call = true;
		$this->blocks    = $this->get_blocks();

		return $this->check_for_rules();
		die();
	}


	/**
	 * Print default template
	 *
	 * @param none
	 */
	public function view_template() {

		if ( isset( $_GET['wp-nonce'] ) && wp_verify_nonce( $_REQUEST['wp-nonce'], 'nonce-template' ) &&
		     isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) {

			echo GeotWP_Bl_Helper::get_template( intval( $_GET['id'] ) );
		}
		die();
	}

	public function ajax_placeholder() {
		echo '<div class="geobl-ajax" style="display: none"></div>';
	}

}