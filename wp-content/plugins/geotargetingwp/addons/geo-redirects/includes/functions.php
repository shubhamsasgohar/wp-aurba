<?php
function geotr_settings() {
	$defaults = ['redirect_message' => GeotWP_R_Settings::default_message(), 'opt_stats' => 1];

	$opts = wp_parse_args( get_option( 'geotr_settings' ), $defaults );

	return apply_filters( 'geotr/settings_page/opts', $opts );
}