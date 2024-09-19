<div class="geot-setup-content">
	<form action="" method="POST">
		<?php wp_nonce_field( 'geot-setup' ); ?>

		<?php do_action( 'geot/wizard/others/before' ); ?>

		<div class="location-row">
			<label for="region" class="location-label"><?php _e( 'Fallback Country', 'geot' ); ?></label>
			<select name="geot_settings[fallback_country]" class="geot-chosen-select"
			        data-placeholder="<?php _e( 'Type country name...', 'geot' ); ?>">
				<option value=""><?php _e( 'Choose One', 'geot' ); ?></option>

				<?php foreach ( $countries as $c ) : ?>
					<option value="<?php echo $c->iso_code ?>" <?php isset( $opts['fallback_country'] ) ? selected( $c->iso_code, $opts['fallback_country'] ) : ''; ?>> <?php echo $c->country; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="location-help"><?php _e( 'If the user IP is not detected, the plugin will fallback to this country. Simply choose the country which most of your content belongs to.', 'geot' ); ?></div>
		</div>


		<div class="location-row">
			<label for="bots" class="location-label"><?php _e( 'Bots Country', 'geot' ); ?></label>
			<select name="geot_settings[bots_country]" class="geot-chosen-select"
			        data-placeholder="<?php _e( 'Type country name...', 'geot' ); ?>">
				<option value=""><?php _e( 'Choose One', 'geot' ); ?></option>

				<?php foreach ( $countries as $c ) : ?>
					<option value="<?php echo $c->iso_code ?>" <?php isset( $opts['bots_country'] ) ? selected( $c->iso_code, $opts['bots_country'] ) : ''; ?>> <?php echo $c->country; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="location-help"><?php _e( 'Bots and crawlers will be treated as they were from this country. Usually the same country as above', 'geot' ); ?></div>
		</div>

		<?php do_action( 'geot/wizard/others/after' ); ?>

		<div class="location-row text-center">
			<input type="hidden" name="save_step" value="1"/>
			<button class="button-primary button button-hero button-next location-button"
			        name="geot_others[button]"><?php _e( 'Next', 'geot' ); ?></button>
		</div>
	</form>
</div>