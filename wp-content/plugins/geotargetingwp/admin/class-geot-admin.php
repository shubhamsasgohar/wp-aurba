<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.0.0
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/admin
 */


/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/admin
 * @author     Your Name <email@example.com>
 */
class GeotWP_Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', [ $this, 'register_tiny_buttons' ] );
		add_action( 'admin_init', [ $this, 'check_inactive' ] );
		add_action( 'admin_init', [ $this, 'download_debug_data' ] );
		add_action( 'wp_ajax_geot_get_popup', [ $this, 'add_editor' ] );

		add_filter( 'geot/plugin_version', function () {
			return GEOWP_VERSION;
		} );
		add_filter( 'geot/exclude/post_types', [ $this, 'exclude_posts' ], 10, 1 );

		//Rules
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'geot-inactive' ) {
			add_action( 'admin_menu', [ $this, 'admin_menus' ] );
			add_action('admin_enqueue_scripts',[ $this, 'inactive_scripts'] );
			add_action( 'admin_init', [ $this, 'inactive_page_html' ] );
		}
		add_action( 'wp_ajax_geot_deactivate', [ $this, 'deactivate_plugin' ] );
	}


	/**
	 * Register the JavaScript for the admin area.
	 * @since    1.0.0
	 */
	public function exclude_posts( $post_types ) {

		$post_types[] = 'geotr_cpt';
		$post_types[] = 'geobl_cpt';
		$post_types[] = 'wcgeoldelivery';

		return $post_types;
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow, $post;

		$post_types = apply_filters( 'geot/exclude/post_types', [] );

		if ( ! in_array( get_post_type(), $post_types ) || ! in_array( $pagenow, [
				'post-new.php',
				'edit.php',
				'post.php',
			] ) ) {
			return;
		}

		$post_id = isset( $post->ID ) ? $post->ID : '';

		wp_enqueue_script( 'geot-admin-js', plugin_dir_url( __FILE__ ) . 'js/geot-admin.js', [ 'jquery' ], GEOWP_VERSION, false );

		wp_enqueue_style( 'geot-admin-css', plugin_dir_url( __FILE__ ) . 'css/geot-admin.css', [], GEOWP_VERSION, 'all' );

		wp_localize_script( 'geot-admin-js', 'geot_js',
			apply_filters( 'geot/rules/vars_localize',
				[
					'admin_url' => admin_url(),
					'nonce'     => wp_create_nonce( 'geot_nonce' ),
					'l10n'      => [
						'or' => '<span>' . __( 'OR', 'geot' ) . '</span>',
					],
				]
			)
		);
	}

	public function inactive_scripts() {
		wp_enqueue_style( 'buttons' );
		wp_enqueue_style( 'geot-setup', GEOWP_PLUGIN_URL. 'includes/geot/Setting/css/wizard.css', [ 'buttons' ], GEOWP_VERSION );
	}
	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'geot-inactive', '' );
	}

	/**
	 * @return void
	 */
	public function check_inactive(){

		if( defined('DOING_AJAX') || ( isset($_GET['page']) && 'geot-inactive' == $_GET['page'] ) )
			return;

		$total = get_option('geot_inactive_user');
		if( $total > 5 ) {
			// Redirect to panel welcome
			wp_redirect(
				add_query_arg(
					[ 'page' => 'geot-inactive' ],
					admin_url( 'admin.php' )
				)
			);
			exit;
		}

	}

	public function deactivate_plugin() {
		deactivate_plugins( '/geotargetingwp/geotargetingwp.php' );
		wp_send_json_success();
	}

	public function inactive_page_html() {
		ob_start();
		set_current_screen();
		require_once dirname( __FILE__ ) . '/partials/inactive.php';
		exit;
	}
	/**
	 * Add filters for tinymce buttons
	 */
	public function register_tiny_buttons() {
		add_filter( "mce_external_plugins", [ $this, "add_button" ] );
		add_filter( 'mce_buttons', [ $this, 'register_button' ] );
	}

	/**
	 * Add buton js file
	 *
	 * @param [type] $plugin_array [description]
	 */
	function add_button( $plugin_array ) {

		$plugin_array['geot'] = plugins_url( 'js/geot-tinymce.js', __FILE__ );

		return $plugin_array;
	}

	/**
	 * Register button
	 *
	 * @param  [type] $buttons [description]
	 *
	 * @return [type]          [description]
	 */
	function register_button( $buttons ) {
		array_push( $buttons, '|', 'geot_button' ); // dropcap', 'recentposts

		return $buttons;
	}

	/**
	 * Add popup editor for
	 */
	public function add_editor() {

		include GEOWP_PLUGIN_DIR . 'admin/partials/tinymce-popup.php';
		wp_die();
	}

	/**
	 * Download debug data
	 * @return void
	 */
	public function download_debug_data(){
		if( isset( $_GET['geot_download'] ) && 'debug' == $_GET['geot_download'] ) {
			if ( isset( $_POST['geot-debug-button'] ) &&
			     isset( $_POST['geot-debug-content'] ) &&
			     isset( $_POST['geot-debug-nonce'] )
			) {
				nocache_headers();
				header( "Content-type: text/plain" );
				header( "Content-Disposition: attachment; filename=debug-data.txt" );

				echo  wp_strip_all_tags( $_POST['geot-debug-content'] );
				die();
			}
		}
	}
}
