<?php

use function GeotCore\check_key;
use function GeotCore\is_rest_request;

/**
 * Adds GeoTarget to categories
 * @since  1.8
 */
class GeotWP_Taxonomies {

	/**
	 * @since   1.6
	 * @access  private
	 * @var     Array of plugin settings
	 */
	private $opts;
	private $geot_opts;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string $GeoTarget The name of this plugin.
	 * @var      string $version The version of this plugin.
	 */
	public function __construct() {

		$defaults = [ 'enable_taxonomies' => [ 'category', 'product_cat'] ];

		$this->geot_opts = wp_parse_args(geotwp_settings(), $defaults);
		$this->opts = geot_settings();

		// Categories only if ajax mode is disabled
		if ( empty( $this->opts['ajax_mode'] ) ) {
			add_action( 'init', [ $this, 'init' ], 50 );
		}
	}

	public function init() {

		if( empty( $this->geot_opts['enable_taxonomies'] ) )
			return;


		foreach( $this->geot_opts['enable_taxonomies'] as $tax_slug ) {

			if( $tax_slug == 'product_cat' )
				continue;

			add_action( $tax_slug.'_edit_form_fields', [ $this, 'edit_tax_fields' ], 10, 2 );
			add_action( 'edited_'.$tax_slug, [ $this, 'save_tax_fields' ], 10, 2 );
		}

		add_filter( 'posts_where', [ $this, 'posts_where_posts' ], PHP_INT_MAX, 2 );
		add_action( 'pre_get_posts', [ $this, 'pre_get_categories' ], 10, 1 );
		add_action( 'get_terms', [ $this, 'get_terms' ], 10, 4 );

		
		// Woocommerce - Categories Products
		if( in_array( 'product_cat', $this->geot_opts['enable_taxonomies'] ) ) {
			add_action( 'product_cat_edit_form_fields', [ $this, 'woo_edit_category_fields' ], 20 );
			add_action( 'edited_product_cat', [ $this, 'woo_save_category_fields' ], 10, 1 );
			add_action( 'woocommerce_product_query', [ $this, 'woo_pre_get_posts' ], 10, 1 );
		}
	}

	/**
	 * Render settings Category
	 * @since    1.0.0
	 * @param OBJECT $tag
	 */
	public function edit_tax_fields( $tag, $tax ) {

		$extra = get_term_meta( $tag->term_id, 'geot', true );
		$geot  = geotwp_format( $extra );


		$regions_countries	= wp_list_pluck( geot_country_regions(), 'name' );
		$regions_cities		= wp_list_pluck( geot_city_regions(), 'name' );
		$regions_states		= wp_list_pluck( geot_state_regions(), 'name' );
		$regions_zips		= wp_list_pluck( geot_zip_regions(), 'name' );

		include_once GEOWP_PLUGIN_DIR . 'admin/partials/metabox-category.php';
	}


	/**
	 * Save Settings Category
	 * @since    1.0.0
	 * @param INT $term_id
	 */
	public function save_tax_fields( $term_id, $tax_id ) {
		if( ! isset( $_POST['geot'] ) )
			return;

		$array_geot = geotwp_format( $_POST['geot'] );
		$without = array_filter( array_values( $array_geot ) );

		if ( ! empty( $without ) )
			update_term_meta( $term_id, 'geot', $array_geot );
		else
			delete_term_meta( $term_id, 'geot' );
		
		return true;
	}

	/**
	 * [posts_where_posts description]
	 * @param  string $where
	 * @param  WP_Query  $q
	 * @return string
	 */
	public function posts_where_posts( $where, $q ) {

		if( is_admin() || ! $q->is_main_query() || ! $q->is_single || empty( $q->query['name'] ) )
			return $where;

		if( empty( $this->geot_opts['enable_taxonomies'] ) )
			return $where;

		$object	= isset( $q->query['post_type'] ) ? $q->query['post_type'] : 'post';

		// Get Post object
		$post = get_page_by_path( $q->query['name'], OBJECT, $object );
		$post_exclude = $q->get( 'post__not_in' );

		if( ! $post || in_array( $post->ID, $post_exclude ) )
			return $where;

		// Get Taxonomies
		$a_taxs = get_object_taxonomies( $object, 'names' );
		$taxonomies = array_intersect( $a_taxs, $this->geot_opts['enable_taxonomies'] );

		if( empty( $taxonomies ) )
			return $where;

		foreach( $taxonomies as $tax_slug ) {
	
			// Get terms by taxonomy
			$term_ids = wp_get_object_terms(
				$post->ID,
				$tax_slug,
				[ 'fields' => 'ids', 'geot' => true ]
			);

			// Bucle terms
			foreach ( $term_ids as $term_id ) {
				$geot = get_term_meta( $term_id, 'geot', true );

				if ( ! $geot ) continue;

				if( ! $this->verify_geot($geot) ) {
					$post_exclude[] = $post->ID;
					break;
				}
			}
		}

		// Verify if there is posts exclude
		if ( count( $post_exclude ) == 0 )
			return $where;

		global $wpdb;
			
		$where .= sprintf(
			' AND %s.ID NOT IN ("%s")',
			$wpdb->posts,
			implode( '","', $post_exclude )
		);

		return $where;
	}


	/**
	 * Modify query to category
	 * @since    1.0.0
	 * @param OBJECT $q
	 */
	public function pre_get_categories( $q ) {

		if ( is_admin() || ! $q->is_main_query() )
			return;
		
		if( apply_filters('geot/apply_on_taxonomy_pages_only', false ) ) {
			if( ! $q->is_tax && ! $q->is_category ) {
				return;
			}
		}

		if( empty( $this->geot_opts['enable_taxonomies'] ) )
			return;


		$tax_exclude = [];

		foreach( $this->geot_opts['enable_taxonomies'] as $tag_slug ) {
			
			$tax_term_exclude = [];
			$taxs_ids = get_terms( [ 'taxonomy' => $tag_slug, 'fields' => 'ids', 'geot' => true ] );

			foreach ( $taxs_ids as $term_id ) {
				$geot = get_term_meta( $term_id, 'geot', true );

				if ( ! $geot )
					continue;

				if( ! $this->verify_geot($geot) )
					$tax_term_exclude[] = $term_id;
			}

			// 
			if( count( $tax_term_exclude ) > 0 ) {
				$tax_exclude[] = [
					'taxonomy'	=> $tag_slug,
					'field'		=> 'id',
					'operator'	=> 'NOT IN',
					'terms'		=> $tax_term_exclude,
				];
			}
		}

		if( count( $tax_exclude ) > 0 ) {
			$q->set( 'tax_query', $tax_exclude );
		}
	}


	/**
	 * Get Terms Hook
	 * @since    1.0.0
	 * @param ARRAY $terms
	 * @param ARRAY $taxonomies
	 * @param ARRAY $args
	 * @param OBJECT $term_query
	 * @return ARRAY $terms
	 */
	public function get_terms( $terms, $taxonomies, $args, $term_query = null ) {

		// If is admin or if geot param is set
		if( is_admin() || isset( $args['geot'] ) || ! is_array( $taxonomies ) || is_rest_request() )
			return $terms;

		// If there isnt taxonomies from settings
		if( empty( $this->geot_opts['enable_taxonomies'] ) )
			return $terms;

		$a_taxs = array_intersect( $taxonomies, $this->geot_opts['enable_taxonomies'] );

		// If there isnt intersection
		if( empty( $a_taxs ) )
			return $terms;


		// bucle from terms
		foreach ( $terms as $id => $term ) {
			if ( ! isset( $term->term_id ) ) {
				continue;
			}

			$geot = get_term_meta( $term->term_id, 'geot', true );

			if ( ! $geot ) {
				continue;
			}

			if( ! $this->verify_geot($geot) )
				unset( $terms[ $id ] );
		}

		return $terms;
	}


	/**
	 * Edit category thumbnail field.
	 * @since    1.0.0
	 * @param OBJECT $tag
	 */
	public function woo_edit_category_fields( $tag ) {
		$extra = get_term_meta( $tag->term_id, 'geot', true );
		$geot  = geotwp_format( $extra );

		$regions_countries	= wp_list_pluck( geot_country_regions(), 'name' );
		$regions_cities		= wp_list_pluck( geot_city_regions(), 'name' );
		$regions_states		= wp_list_pluck( geot_state_regions(), 'name' );
		$regions_zips		= wp_list_pluck( geot_zip_regions(), 'name' );

		include_once GEOWP_PLUGIN_DIR . 'admin/partials/metabox-woo-category.php';
	}

	/**
	 * Save category fields
	 * @since    1.0.0
	 * @param int $term_id Term ID being saved.
	 */
	public function woo_save_category_fields( $term_id ) {

		if( ! isset( $_POST['geot'] ) )
			return;

		$array_geot = geotwp_format( $_POST['geot'] );
		$without = array_filter( array_values( $array_geot ) );

		if ( ! empty( $without ) )
			update_term_meta( $term_id, 'geot', $array_geot );
		else
			delete_term_meta( $term_id, 'geot' );

		return true;
	}

	/**
	 * Pre Get Post to Woocommerce
	 * @since    1.0.0
	 * @param OBJECT $q
	 */
	public function woo_pre_get_posts( $q ) {

		$cat_exclude = [];
		$cats_ids    = get_categories( [ 'fields' => 'ids', 'taxonomy' => 'product_cat', 'geot' => true ] );

		foreach( $cats_ids as $term_id ) {
			$geot = get_term_meta( $term_id, 'geot', true );

			if ( ! $geot ) {
				continue;
			}

			if( ! $this->verify_geot($geot) )
				$cat_exclude[] = $term_id;
		}


		if ( count( $cat_exclude ) > 0 ) {

			$tax_query = (array) $q->get( 'tax_query' );

			$tax_query[] = [
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $cat_exclude,
				'operator' => 'NOT IN',
			];

			$q->set( 'tax_query', $tax_query );
		}
	}

	/**
	 * Verify if geotargeting
	 * @since    1.0.0
	 * @param ARRAY $geot
	 * @return boolean
	 */
	protected function verify_geot( $geot = [] ) {
		$geot_country = $geot_city = $geot_state = $geot_zipcode = $geot_radius = true;

		$countries = check_key( $geot, 'countries_input');
		$country_regions = check_key( $geot, 'countries_region');

		// Country
		if( ! empty( $countries ) || ! empty( $country_regions ) ) {
			if( $geot['countries_mode'] == 'exclude'  )
				$geot_country = geot_target( '', '', $countries, $country_regions );
			else
				$geot_country = geot_target( $countries, $country_regions );
		}

		$cities = check_key( $geot, 'cities_input');
		$city_regions = check_key( $geot, 'cities_region');

		// City
		if( ! empty( $cities ) || ! empty( $city_regions ) ) {
			if( $geot['cities_mode'] == 'exclude'  )
				$geot_city = geot_target_city( '', '', $cities, $city_regions );
			else
				$geot_city = geot_target_city( $cities, $city_regions );
		}

		$states = check_key( $geot, 'states_input');
		$state_regions = check_key( $geot, 'states_region');

		// State
		if( ! empty( $states ) || ! empty( $state_regions ) ) {
			if( $geot['states_mode'] == 'exclude'  )
				$geot_state = geot_target_state( '', '', $states, $state_regions );
			else
				$geot_state = geot_target_state( $states, $state_regions );
		}

		$zips = check_key( $geot, 'zipcodes_input');
		$zip_regions = check_key( $geot, 'zipcodes_region');
		// Zipcode
		if( ! empty( $zips ) || ! empty( $zip_regions ) ) {
			if( $geot['zipcodes_mode'] == 'exclude'  )
				$geot_zipcode = geot_target_zip( '', '', $zips, $zip_regions );
			else
				$geot_zipcode = geot_target_zip( $zips, $zip_regions );
		}


		// Radius
		if( ! empty( $geot['radius_km'] ) && ! empty( $geot['radius_lat'] ) && ! empty( $geot['radius_lng'] ) ) {

			$geot_aux_radius =  geot_target_radius( $geot['radius_lat'], $geot['radius_lng'], $geot['radius_km'] );

			$geot_radius = $geot['radius_mode'] == 'include' ? $geot_aux_radius : ! $geot_aux_radius;
		}


		// Verify
		if( ! $geot_country || ! $geot_city || ! $geot_state || ! $geot_zipcode || ! $geot_radius )
			return false;

		return true;
	}
}
