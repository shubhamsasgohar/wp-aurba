<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php esc_html_e( 'Geotargeting &rsaquo; Setup Wizard', 'geot' ); ?></title>
	<?php do_action( 'admin_enqueue_scripts' ); ?>
	<?php wp_print_scripts( 'geot-setup' ); ?>
	<?php do_action( 'admin_print_styles' ); ?>
	<?php do_action( 'admin_head' ); ?>
</head>
<body class="wp-core-ui">
<h1 class="geot-logo">
	<a href="https://geotargetingwp.com/">
		<img src="<?php echo esc_url(  GEOWP_PLUGIN_URL. 'includes/geot/Setting' ); ?>/img/geot-logo.png" alt="Geot"/>
	</a>
</h1>

<div class="geot-setup">
	<div class="geot-setup-content">
		<form action="" method="POST">
			<?php wp_nonce_field( 'geot-setup' );
			$opts     = geot_settings();?>
			<?php do_action( 'geot/wizard/basic/before' ); ?>
			<div class="location-row text-center">
				<img width="150px" src="<?php echo esc_url(  GEOWP_PLUGIN_URL. 'admin/img/warning.png');?>" alt="error"/>
			</div>
			<p style="text-align: center"><?php _e( sprintf( 'Your <a href="%s">subscription</a> is not longer active or your trial is over, please check subscription or deactive the plugin to continue.','https://geotargetingwp.com/dashboard/subscription' ), 'geot' ); ?></p>
			<div class="location-row">
				<label for="license" class="location-label"><?php _e( 'Enter your API key', 'geot' ); ?></label>
				<input type="text" id="license" name="geot_settings[license]" value="<?php echo $opts['license']; ?>"
				       class="location-input api-keys"/>
				<!--button class="button-secondary button button-hero button-next location-button-secondary"><?php //_e('Check Credits/Subscriptions','geot') ?></button-->
				<div class="location-help"><?php _e( 'Enter your api key in order to connect with the API and also get automatic updates', 'geot' ); ?></div>
			</div>

			<div class="location-row text-center">
				<input type="hidden" name="geot_inactive" value="1"/>
				<button class="button-primary button button-hero button-next geot-inactive check-license"
				        name="geot_settings[button]"><?php _e( 'Check license', 'geot' ); ?></button>
				<button class="button-secondary button button-hero button-next geot-deactivate"
				        name="geot_settings[button]"><?php _e( 'Deactivate', 'geot' ); ?></button>
				<div id="response_error"></div>
			</div>
		</form>
	</div>

</div>
<div class="text-center">
	<a class="geot-setup-footer-links"
	   href="<?php echo esc_url( admin_url( 'admin.php?page=geot-settings' ) ); ?>"><?php esc_html_e( 'Cancel wizard', 'geot' ); ?></a>
</div>
<script>
    ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    adminurl = "<?php echo admin_url(); ?>";
</script>
<?php wp_print_scripts(); ?>
</body>
</html>