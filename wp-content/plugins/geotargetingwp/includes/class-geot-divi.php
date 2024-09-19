<?php

/**
 * Divi Extension
 *
 * @link       https://geotargetingwp.com/geotargeting-pro
 * @since      1.6.3
 *
 * @package    GeoTarget
 * @subpackage GeoTarget/includes
 * @author     Damian Logghe
 */


class GeotWP_Divi {

	/**
	 * Module slugs.
	 *
	 * @since   1.1.0
	 * @access  private
	 *
	 * @var     array
	 */
	private static $included_modules = array();
	private static $excluded_modules = array();


	/**
	 * Module flags.
	 *
	 * @since   1.1.0
	 * @access  private
	 *
	 * @var     boolean
	 */
	private static $parent_toggles_added = false;
	private static $child_toggles_added  = false;


	/**
	 * Class constructor.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @return  void
	 */
	public function __construct() {

		if( apply_filters( 'geot/deactivate_divi_integration', false ) ) {
			return;
		}

		add_action( 'init', [ $this, 'module_init' ], 10, 1 );
		add_filter( 'et_builder_main_tabs', [ $this, 'add_tabs' ], 10, 1 );

		// Prepare module slugs.
		self::prepare_excluded_modules();
		self::prepare_included_modules();

		// Add toggles to modules.
		add_filter('et_builder_get_parent_modules', array(__CLASS__, 'add_parent_module_toggle'));
		add_filter('et_builder_get_child_modules', array(__CLASS__, 'add_child_module_toggle'));

		// Add fields to modules.
		foreach (self::$included_modules as $slug) {
			add_filter("et_pb_all_fields_unprocessed_{$slug}", array(__CLASS__, 'add_module_fields'));
		}
		// render
		add_filter( 'et_module_shortcode_output', [ $this, 'render' ], 10, 3 );
		// Recover depreciate fields
		add_filter( 'et_pb_module_shortcode_attributes', [ $this, 'recover_fields' ], 10, 2 );
		add_action( 'init', [ $this, 'updater_init' ] );
	}


	/**
	 * Prepares an array of excluded module slugs.
	 *
	 * @since   1.1.0
	 * @access  private
	 *
	 * @return  void
	 */
	private static function prepare_excluded_modules() {

		// Excluded modules.
		$slugs = apply_filters( 'geot/divi/excluded_modules', '');

		// Prepare slugs as array.
		$slugs = self::get_module_slugs_array($slugs);
		self::$excluded_modules = $slugs;
	}


	/**
	 * Prepares an array of included module slugs.
	 *
	 * @since   3.0.0
	 * @access  private
	 *
	 * @return  void
	 */
	private static function prepare_included_modules() {

		// Parent modules.
		$slugs = 'et_pb_section, et_pb_row, et_pb_row_inner, et_pb_accordion, et_pb_audio, et_pb_counters, et_pb_blog, et_pb_blurb, et_pb_button, et_pb_circle_counter, et_pb_code, et_pb_comments, et_pb_contact_form, et_pb_countdown_timer, et_pb_cta, et_pb_divider, et_pb_filterable_portfolio, et_pb_fullwidth_code, et_pb_fullwidth_header, et_pb_fullwidth_image, et_pb_fullwidth_map, et_pb_fullwidth_menu, et_pb_fullwidth_portfolio, et_pb_fullwidth_post_content, et_pb_fullwidth_post_slider, et_pb_fullwidth_post_title, et_pb_fullwidth_slider, et_pb_gallery, et_pb_icon, et_pb_image, et_pb_login, et_pb_map, et_pb_menu, et_pb_number_counter, et_pb_portfolio, et_pb_post_content, et_pb_post_slider, et_pb_post_title, et_pb_post_nav, et_pb_pricing_tables, et_pb_search, et_pb_sidebar, et_pb_signup, et_pb_slider, et_pb_social_media_follow, et_pb_tabs, et_pb_team_member, et_pb_testimonial, et_pb_text, et_pb_toggle, et_pb_video, et_pb_video_slider,';

		// Child modules.
		$slugs .= 'et_pb_column, et_pb_column_inner, et_pb_accordion_item, et_pb_counter, et_pb_contact_field, et_pb_signup_custom_field, et_pb_map_pin, et_pb_pricing_table, et_pb_slide, et_pb_social_media_follow_network, et_pb_tab, et_pb_video_slider_item,';

		// Woocommerce modules.
		$slugs .= 'et_pb_wc_additional_info, et_pb_wc_add_to_cart, et_pb_wc_breadcrumb, et_pb_wc_cart_notice, et_pb_wc_cart_products, et_pb_wc_cart_totals, et_pb_wc_checkout_additional_info, et_pb_wc_checkout_billing, et_pb_wc_checkout_order_details, et_pb_wc_checkout_payment_info, et_pb_wc_checkout_shipping, et_pb_wc_cross_sells, et_pb_wc_description, et_pb_wc_gallery, et_pb_wc_images, et_pb_wc_meta, et_pb_wc_price, et_pb_wc_rating, et_pb_wc_related_products, et_pb_wc_reviews, et_pb_shop, et_pb_wc_stock, et_pb_wc_tabs, et_pb_wc_title, et_pb_wc_upsells,';

		// Divi-Modules modules.
		$slugs .= 'dvmd_simple_heading, dvmd_table_maker, dvmd_typewriter, dvmd_image_box, dvmd_text_on_a_path, dvmd_tablepress_styler,';

		// Plugins

		$slugs .= 'dizo_image_hover,';

		// Additional modules.
		$options = apply_filters( 'geot/divi/included_modules', '');
		if ( ! empty($options) ) {
			$slugs .= $options;
		}

		// Prepare slugs as array.
		$slugs = self::get_module_slugs_array($slugs);
		$slugs = array_diff($slugs, self::$excluded_modules);
		self::$included_modules = $slugs;
	}


	/**
	 * Gets module slugs as array.
	 *
	 * @since   1.1.0
	 * @access  private
	 *
	 * @param   string  $slugs  Comma separated list of slugs.
	 *
	 * @return  void
	 */
	private static function get_module_slugs_array($slugs) {
		$slugs = preg_replace('/\s+/', '', $slugs); // Remove white spaces.
		$slugs = trim($slugs,',');                  // Trim commas.
		$slugs = explode(',', $slugs);              // Create array.
		$slugs = array_filter($slugs);              // Remove empty.
		$slugs = array_unique($slugs);              // Remove duplicates.
		return $slugs;
	}


	/**
	 * Adds Hide & Show PRO toggle to parent-modules.
	 * Ensure we run this code only once because it's expensive.
	 *
	 * @since   1.1.0
	 * @access  public
	 *
	 * @return  array
	 */
	public static function add_parent_module_toggle($modules) {
		if (self::$parent_toggles_added) return $modules;
		if (empty($modules)) return $modules;
		self::$parent_toggles_added = true;
		return self::add_module_settings($modules);
	}


	/**
	 * Adds Hide & Show PRO toggle to child-modules.
	 * Ensure we run this code only once because it's expensive.
	 *
	 * @since   1.1.0
	 * @access  public
	 *
	 * @return  array
	 */
	public static function add_child_module_toggle($modules) {
		if (self::$child_toggles_added) return $modules;
		if (empty($modules)) return $modules;
		self::$child_toggles_added = true;
		return self::add_module_settings($modules);
	}


	/**
	 * Adds toggles to parent-modules and child-modules.
	 * See: https://github.com/elegantthemes/create-divi-extension/issues/462
	 *
	 * @since   1.1.0
	 * @access  private
	 *
	 * @return array
	 */
	private static function add_module_settings($modules) {

		// Each module.
		foreach ($modules as $slug => $module) {

			// Bail.
			if (!in_array($slug, self::$included_modules)) continue;
			if (!isset($module->settings_modal_toggles )) continue;

			// Add toggle.
			$toggles = $module->settings_modal_toggles;
			if ( ! isset($toggles['geot']) ||  empty($toggles['geot']['toggles'])) {

				// Add new Toggle.
				$toggles['geot']['toggles']['country'] = [
					'title'    => esc_html__( 'Country', 'geot' ),
					'priority' => 220,
				];

				$toggles['geot']['toggles']['city'] = [
					'title'    => esc_html__( 'City', 'geot' ),
					'priority' => 230,
				];

				$toggles['geot']['toggles']['state'] = [
					'title'    => esc_html__( 'State', 'geot' ),
					'priority' => 240,
				];

				$toggles['geot']['toggles']['zipcode'] = [
					'title'    => esc_html__( 'Zipcode', 'geot' ),
					'priority' => 250,
				];

				$toggles['geot']['toggles']['radius'] = [
					'title'    => esc_html__( 'Radius', 'geot' ),
					'priority' => 260,
				];

				// Add toggle.
				$module->settings_modal_toggles = $toggles;
			}
		}

		// Return.
		return $modules;
	}


	/**
	 * Adds fields to each module.
	 *
	 * @since   1.1.0
	 * @access  public
	 * @hook    et_pb_all_fields_unprocessed_{$slug}
	 *
	 * @param   array  $unprocessed_fields  The current module’s fields.
	 *
	 * @return  array
	 */
	public static function add_module_fields($unprocessed_fields) {

		$fields_country		= Divi_GeoCountry::get_fields();
		$fields_city		= Divi_GeoCity::get_fields();
		$fields_states		= Divi_GeoState::get_fields();
		$fields_zipcodes	= Divi_GeoZipcode::get_fields();
		$fields_radius		= Divi_GeoRadius::get_fields();

		$fields_geot = array_merge(
			$fields_country,
			$fields_city,
			$fields_states,
			$fields_zipcodes,
			$fields_radius
		);

		// Return.
		return array_merge( $unprocessed_fields, $fields_geot);
	}


	/**
	 * Format regions and normalize
	 *
	 * @param $check_multi
	 * @param string $separator
	 * @param $regions
	 *
	 * @return array
	 */
	static function format_regions( $check_multi, $separator = '|', $regions = [] ) {

		if ( empty( $check_multi ) || empty( $regions ) || strpos( $check_multi, $separator ) === false ) {
			return [];
		}

		$output_regions = [];

		foreach ( explode( $separator, $check_multi ) as $key => $onoff ) {
			if ( strtolower( $onoff ) == 'on' && isset( $regions[ $key ] ) ) {
				$output_regions[] = $regions[ $key ];
			}
		}

		return $output_regions;
	}


	public function updater_init() {
		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-updater.php';
	}

	/**
	 * Module Init
	 *
	 * @return array
	 */
	public function module_init() {

		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-country.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-city.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-state.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-zipcode.php';
		require_once GEOWP_PLUGIN_DIR . 'includes/divi/divi-geot-radius.php';
	}


	/**
	 * Register Tabs
	 * @return array
	 * @var
	 */
	public function add_tabs( $tabs ) {

		$new_tab         = [];
		$new_tab['geot'] = esc_html__( 'Geotargeting', 'geot' );

		return apply_filters( 'geot/divi/add_tabs', array_merge( $tabs, $new_tab ) );
	}


	/**
	 * Recover fields
	 * @param  array  $module_props
	 * @param  array  $attrs
	 * @return mixed
	 */
	public function recover_fields( $module_props = [], $attrs = [] ) {

		$module_props = Divi_GeoCountry::recover_fields( $module_props, $attrs );
		$module_props = Divi_GeoCity::recover_fields( $module_props, $attrs );
		$module_props = Divi_GeoState::recover_fields( $module_props, $attrs );
		$module_props = Divi_GeoZipcode::recover_fields( $module_props, $attrs );

		return Divi_GeoRadius::recover_fields( $module_props, $attrs );
	}

	/**
	 * @param $output
	 * @param $render_slug
	 * @param $module
	 *
	 * @return string
	 */
	public function render( $output, $render_slug, $module ) {

		global $et_fb_processing_shortcode_object;

		// if is builder / edit mode
		if ( $et_fb_processing_shortcode_object == 1 || ! $this->has_geot_opts( $module->props ) ) {
			return $output;
		}
		if ( is_array($output) ){
			return $output;   // We’re loading from the divi library.
		}
		if ( ! in_array($render_slug, self::$included_modules) ) {
			return $output;
		}

		$opts 			= geot_settings();
		$reg_countries 	= array_values( self::get_regions( 'country' ) );
		$reg_cities 	= array_values( self::get_regions( 'city' ) );
		$reg_states		= array_values( self::get_regions( 'state' ) );
		$reg_zips 		= array_values( self::get_regions( 'zip' ) );

		if ( isset( $opts['ajax_mode'] ) && $opts['ajax_mode'] == '1' ) {

			$output = Divi_GeoRadius::ajax_render( $module->props, $output );
			$output = Divi_GeoZipcode::ajax_render( $module->props, $reg_zips, $output );
			$output = Divi_GeoState::ajax_render( $module->props, $reg_states, $output );
			$output = Divi_GeoCity::ajax_render( $module->props, $reg_cities, $output );
			$output = Divi_GeoCountry::ajax_render( $module->props, $reg_countries, $output );

		} else {

			if ( ! Divi_GeoCountry::is_render( $module->props, $reg_countries ) ||
			     ! Divi_GeoCity::is_render( $module->props, $reg_cities ) ||
			     ! Divi_GeoState::is_render( $module->props, $reg_states ) ||
			     ! Divi_GeoZipcode::is_render( $module->props, $reg_zips ) ||
			     ! Divi_GeoRadius::is_render( $module->props )
			) {
				return '';
			}
		}

		return $output;
	}

	/**
	 * Check if values are set
	 *
	 * @param $props
	 *
	 * @return bool
	 */
	private function has_geot_opts( $props ) {
		$keys = [
			'countries_mode',
			'countries_input',
			'countries_region',
			'cities_mode',
			'cities_input',
			'cities_region',
			'states_mode',
			'states_input',
			'states_region',
			'zipcodes_mode',
			'zipcodes_input',
			'zipcodes_region',
			'radius_mode',
			'radius_km',
			'radius_lat',
			'radius_lng',
			'in_countries',
			'in_region_countries',
			'ex_countries',
			'ex_region_countries',
			'in_states',
			'in_region_states',
			'ex_states',
			'ex_region_states',
			'in_cities',
			'in_region_cities',
			'ex_cities',
			'ex_region_cities',
			'in_zipcodes',
			'in_region_zips',
			'ex_zipcodes',
			'ex_region_zips',
		];

		// check if any of the valid key has a value
		foreach ( $keys as $key ) {
			if ( ! empty( $props[ $key ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get Regions
	 *
	 * @param string $slug_region
	 *
	 * @return array
	 */
	static function get_regions( $slug_region = 'country' ) {

		$dropdown_values = [];

		switch ( $slug_region ) {
			case 'city':
				$regions = geot_city_regions();
				break;
			case 'state':
				$regions = geot_state_regions();
				break;
			case 'zip':
				$regions = geot_zip_regions();
				break;
			default:
				$regions = geot_country_regions();
		}

		if ( ! empty( $regions ) ) {
			foreach ( $regions as $r ) {
				if ( isset( $r['name'] ) ) {
					$dropdown_values[ $r['name'] ] = $r['name'];
				}
			}
		}

		return $dropdown_values;
	}

}