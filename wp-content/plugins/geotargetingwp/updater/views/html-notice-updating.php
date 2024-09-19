<?php
/**
 * Admin View: Notice - Updating
 *
 * @package Admin / updater
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//$pending_actions_url = admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=woocommerce_run_update&status=pending' );
$cron_disabled       = defined( 'DISABLE_WP_CRON' );
$cron_cta            = $cron_disabled ? __( 'You can manually run queued updates here.', 'geot' ) : __( 'View progress &rarr;', 'geot' );
?>
<div id="message" class="notice notice-warning">
	<p>
		<strong><?php esc_html_e( 'GeotargetingWP database update', 'geot' ); ?></strong><br>
		<?php esc_html_e( 'GeotargetingWP is updating the database in the background. The database update process may take a little while, so please be patient.', 'geot' ); ?>
		<?php
		if ( $cron_disabled ) {
			echo '<br>' . esc_html__( 'Note: WP CRON has been disabled on your install which may prevent this update from completing.', 'geot' );
		}
		?>
		<!--&nbsp;<a href="<?php //echo esc_url( $pending_actions_url ); ?>"><?php //echo esc_html( $cron_cta ); ?></a> -->
	</p>
</div>
