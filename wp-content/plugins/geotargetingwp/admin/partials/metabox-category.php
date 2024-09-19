<tr class="form-field">
	<th colspan="2"><h2><?php esc_html_e( 'Geotargeting Country', 'geot' ); ?></h2></th>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-countries-mode"><?php esc_html_e( 'Visibility' ); ?></label>
	</th>
	<td>
		<label for="countries_mode_show">
			<input type="radio" name="geot[countries_mode]" value="include" <?php checked( 'include', $geot['countries_mode'] ); ?> />
			<?php esc_html_e( 'Show', 'geot' ); ?>
		</label>
		<label for="countries_mode_hide">
			<input type="radio" name="geot[countries_mode]" value="exclude" <?php checked( 'exclude', $geot['countries_mode'] ); ?> />
			<?php esc_html_e( 'Hide', 'geot' ); ?>
		</label>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-countries-input"><?php esc_html_e( 'Countries' ); ?></label>
	</th>
	<td>
		<input type="text" name="geot[countries_input]" class="geot_text selectize-input" value="<?php echo $geot['countries_input']; ?>" style="width:100%;">
		<br/>
		<span class="description"><?php esc_html_e( 'Type country names or ISO codes separated by commas.', 'geot' ); ?></span>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-countries-region"><?php esc_html_e( 'Regions' ); ?></label>
	</th>
	<td>
		<select name="geot[countries_region][]" class="geot-chosen-select-multiple"
		        multiple="multiple">
			<?php foreach ( $regions_countries as $region ) : ?>
				<option value="<?php echo $region; ?>" <?php selected( in_array( $region, $geot['countries_region'] ), true, true ); ?>><?php echo $region; ?></option>
			<?php endforeach; ?>
		</select>
	</td>
</tr>

<tr class="form-field">
	<th colspan="2"><h2><?php esc_html_e( 'Geotargeting City', 'geot' ); ?></h2></th>
</tr>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-cities-mode"><?php esc_html_e( 'Visibility' ); ?></label>
	</th>
	<td>
		<label for="cities_mode_show">
			<input type="radio" name="geot[cities_mode]" value="include" <?php checked( 'include', $geot['cities_mode'] ); ?> />
			<?php esc_html_e( 'Show', 'geot' ); ?>
		</label>
		<label for="cities_mode_hide">
			<input type="radio" name="geot[cities_mode]" value="exclude" <?php checked( 'exclude', $geot['cities_mode'] ); ?> />
			<?php esc_html_e( 'Hide', 'geot' ); ?>
		</label>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-cities-input"><?php esc_html_e( 'Cities' ); ?></label>
	</th>
	<td>
		<input type="text" name="geot[cities_input]" class="geot_text selectize-input" value="<?php echo $geot['cities_input']; ?>" style="width:100%;"><br/>
		<span class="description"><?php esc_html_e( 'Type city names separated by commas.', 'geot' ); ?></span>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-cities-region"><?php esc_html_e( 'Cities Regions' ); ?></label>
	</th>
	<td>
		<select name="geot[cities_region][]" class="geot-chosen-select-multiple" multiple="multiple">
			<?php foreach ( $regions_cities as $region ) : ?>
				<option value="<?php echo $region; ?>" <?php selected( in_array( $region, $geot['cities_region'] ), true, true ); ?>><?php echo $region; ?></option>
			<?php endforeach; ?>
		</select>
	</td>
</tr>


<tr class="form-field">
	<th colspan="2"><h2><?php esc_html_e( 'Geotargeting State', 'geot' ); ?></h2></th>
</tr>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-states-mode"><?php esc_html_e( 'Visibility' ); ?></label>
	</th>
	<td>
		<label for="states_mode_show">
			<input type="radio" name="geot[states_mode]" value="include" <?php checked( 'include', $geot['states_mode'] ); ?> />
			<?php esc_html_e( 'Show', 'geot' ); ?>
		</label>
		<label for="states_mode_hide">
			<input type="radio" name="geot[states_mode]" value="exclude" <?php checked( 'exclude', $geot['states_mode'] ); ?> />
			<?php esc_html_e( 'Hide', 'geot' ); ?>
		</label>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-states-input"><?php esc_html_e( 'States' ); ?></label>
	</th>
	<td>
		<input type="text" name="geot[states_input]" class="geot_text selectize-input"
		       value="<?php echo $geot['states_input']; ?>" style="width:100%;"><br/>
		<span class="description"><?php esc_html_e( 'Type state names or ISO codes separated by commas.', 'geot' ); ?></span>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-states-region"><?php esc_html_e( 'States Regions' ); ?></label>
	</th>
	<td>
		<select name="geot[states_region][]" id="geot-states-region" class="geot-chosen-select-multiple" multiple="multiple">
			<?php foreach ( $regions_states as $region ) : ?>
				<option value="<?php echo $region; ?>" <?php selected( in_array( $region, $geot['states_region'] ), true, true ); ?>><?php echo $region; ?></option>
			<?php endforeach; ?>
		</select>
	</td>
</tr>

<tr class="form-field">
	<th colspan="2"><h2><?php esc_html_e( 'Geotargeting Zipcode', 'geot' ); ?></h2></th>
</tr>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-zipcodes-mode"><?php esc_html_e( 'Visibility' ); ?></label>
	</th>
	<td>
		<label for="zipcodes_mode_show">
			<input type="radio" name="geot[zipcodes_mode]" value="include" <?php checked( 'include', $geot['zipcodes_mode'] ); ?> />
			<?php esc_html_e( 'Show', 'geot' ); ?>
		</label>
		<label for="zipcodes_mode_hide">
			<input type="radio" name="geot[zipcodes_mode]" value="exclude" <?php checked( 'exclude', $geot['zipcodes_mode'] ); ?> />
			<?php esc_html_e( 'Hide', 'geot' ); ?>
		</label>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-zipcodes-input"><?php esc_html_e( 'Zipcodes' ); ?></label>
	</th>
	<td>
		<input type="text" name="geot[zipcodes_input]" class="geot_text selectize-input"
		       value="<?php echo $geot['zipcodes_input']; ?>" style="width:100%;"><br/>
		<span class="description"><?php esc_html_e( 'Type zip codes separated by commas.', 'geot' ); ?></span>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="geot-zipcodes-region"><?php esc_html_e( 'Zipcode Regions' ); ?></label>
	</th>
	<td>
		<select name="geot[zipcodes_region][]" class="geot-chosen-select-multiple"
		        multiple="multiple">
			<?php foreach ( $regions_zips as $region ) : ?>
				<option value="<?php echo $region; ?>" <?php selected( in_array( $region, $geot['zipcodes_region'] ), true, true ); ?>><?php echo $region; ?></option>
			<?php endforeach; ?>
		</select>
	</td>
</tr>

<tr class="form-field">
	<th colspan="2"><h2><?php esc_html_e( 'Geotargeting by Radius', 'geot' ); ?></h2></th>
</tr>
<tr valign="top">
	<th><label for="gstates"><?php esc_html_e( 'Given Radius:', 'geot' ); ?></label></th>
	<td>
		<label for="radius_mode_show">
			<input type="radio" name="geot[radius_mode]" value="include" <?php checked( 'include', $geot['radius_mode'] ); ?> />
			<?php esc_html_e( 'Show', 'geot' ); ?>
		</label>
		<label for="radius_mode_hide">
			<input type="radio" name="geot[radius_mode]" value="exclude" <?php checked( 'exclude', $geot['radius_mode'] ); ?> />
			<?php esc_html_e( 'Hide', 'geot' ); ?>
		</label>
		<br /><br />
		<input type="text" id="radius_km" class="geot_text" name="geot[radius_km]"
		       value="<?php echo ! empty( $geot['radius_km'] ) ? $geot['radius_km'] : ''; ?>"
		       placeholder="<?php esc_html_e( '100', 'geot' ); ?>" style="width: 60px;"/> <?php echo \GeotCore\radius_unit();?> within
		<input type="text" id="radius_lat" class="geot_text" name="geot[radius_lat]"
		       value="<?php echo ! empty( $geot['radius_lat'] ) ? $geot['radius_lat'] : ''; ?>"
		       placeholder="<?php esc_html_e( 'Enter latitude', 'geot' ); ?>"/>
		<input type="text" id="radius_lng" class="geot_text" name="geot[radius_lng]"
		       value="<?php echo ! empty( $geot['radius_lng'] ) ? $geot['radius_lng'] : ''; ?>"
		       placeholder="<?php esc_html_e( 'Enter longitude', 'geot' ); ?>"/>

	</td>
</tr>