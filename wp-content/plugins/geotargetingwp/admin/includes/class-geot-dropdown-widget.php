<?php


/**
 * Adds GeoTarget Widget
 * @since  1.0.0
 */
class GeotWP_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'geot_widget', // Base ID
			__( 'Geotarget Dropdown', 'geot' ), // Name
			[ 'description' => __( 'Display a dropdown to let users change country', 'geot' ), ] // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 *
	 * @see WP_Widget::widget()
	 *
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		$regions       = ! empty( $instance['regions'] ) ? \GeotCore\toArray( $instance['regions'] ) : [];
		$flags         = ! empty( $instance['flags'] ) ? $instance['flags'] : '';
		$countries     = geot_countries();
		$saved_regions = geot_country_regions();

		if ( ! empty( $regions ) ) {
			$country_codes = [];
			//get all countries in selected regions
			foreach ( $regions as $key ) {
				if ( isset( $saved_regions[ $key ]['countries'] ) && is_array( $saved_regions[ $key ]['countries'] ) ) {
					$country_codes = array_merge( $saved_regions[ $key ]['countries'], $country_codes );
				}
			}
		}

		// If we have country codes search in all countries and save them into a new array
		if ( ! empty( $country_codes ) ) {

			$country_regions = [];

			foreach ( $countries as $country ) {
				if ( in_array( $country->iso_code, $country_codes ) ) {
					$country_regions[] = $country;
				}
			}
			// if we have matches , replace countries array with new one
			if ( ! empty( $country_regions ) ) {
				$countries = $country_regions;
			}
		}

		$countries = apply_filters( 'geot/dropdown_widget/countries', $countries );
		$opts = geot_settings();
		$ajax_mode = \GeotCore\check_key($opts, 'ajax_mode', false );
		if(  ! $ajax_mode ) {
			$user_country     = geot_user_country();
			// refers to country by ip, not cookie
			$original_country = apply_filters( 'geot/dropdown_widget/original_country', geot_country(true ) );
		}

		?>
		<div class="geot_dropdown_container">
			<select class="geot_dropdown geot-ddslick" name="geot_dropdown" id="geot_dropdown"
			        data-flags="<?php echo $flags; ?>">
				<option value=""><?= __( 'Change your location', 'geot' ); ?></option>
				<?php
				$user_country_in_dropdown = false;
				foreach ( $countries as $c ) {

					$selected = '';
					// if we are in ajax mode don't add original country
					if (  $ajax_mode || ( $original_country && $original_country->iso_code == $c->iso_code ) ) {
						$user_country_in_dropdown = true;
					}

					?>
					<option value="<?php echo $c->iso_code; ?>" <?php echo ! $ajax_mode && $user_country->iso_code == $c->iso_code ? 'selected="selected"' : ''; ?>
					        data-imagesrc="geot-flag flag-<?php echo strtolower( $c->iso_code ); ?>"><?php
						echo $c->country;
						?></option>
					<?php
				}
				// if the user country is not in dropdown add it
				if ( ! $user_country_in_dropdown && apply_filters( 'geot/dropdown_widget/original_country_in_dropdown', false ) ) {
					?>
					<option value="<?php echo $original_country->iso_code ?>" <?php echo $user_country->iso_code == $original_country->iso_code ? 'selected="selected"' : ''; ?>
					        data-imagesrc="geot-flag flag-<?php echo strtolower( $original_country->iso_code ); ?>"><?php
						echo $original_country->name
						?></option>
					<?php
				}
				?>
			</select>
		</div>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return string|void
	 * @see WP_Widget::form()
	 *
	 */
	public function form( $instance ) {
		if ( isset( $instance['regions'] ) ) {
			$regions = (array) $instance['regions'];
		} else {
			$regions = []; //empty array
		}

		$flags = isset( $instance['flags'] ) ? $instance['flags'] : 1;

		$saved_regions = geot_country_regions();
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'regions' ); ?>"><?php _e( 'Choose regions to display in widget:' ); ?></label>
			<select style="min-height: 200px" multiple="multiple" class="widefat" id="<?php echo $this->get_field_id( 'regions' ); ?>"
			        name="<?php echo $this->get_field_name( 'regions' ); ?>[]">
				<?php foreach ( $saved_regions as $key => $value ) {
					echo '<option value="' . $key . '"';
					if ( in_array( $key, $regions ) ) {
						echo 'selected="selected";';
					}
					echo '>' . $value['name'] . '</option>';
				}
				?>

			</select>
			<span style="font-size:12px"><?php _e( 'Select none to show all countries (not recommended)', 'geot' ); ?></span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'flags' ); ?>"><?php _e( 'Display flags ?' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'flags' ); ?>"
			        name="<?php echo $this->get_field_name( 'flags' ); ?>">
				<option value="1" <?php selected( $flags, 1 ); ?>><?php _e( 'Yes' ); ?></option>
				<option value="0" <?php selected( $flags, 0 ); ?>><?php _e( 'No' ); ?></option>
			</select>
		</p>

		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 * @see WP_Widget::update()
	 *
	 */
	public function update( $new_instance, $old_instance ) {
		$instance            = [];
		$instance['regions'] = ( ! empty( $new_instance['regions'] ) ) ? $new_instance['regions'] : '';
		$instance['flags']   = ( ! empty( $new_instance['flags'] ) ) ? $new_instance['flags'] : '0';

		return $instance;
	}

} // class Foo_Widget