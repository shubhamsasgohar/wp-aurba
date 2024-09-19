<?php
/**
 * Admin View: Notice - Updated.
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="notice notice-success">
	
	<p><?php esc_html_e( 'GeotargetingWP database update complete. Thank you for updating to the latest version!', 'geot' ); ?></p>

	<p class="submit">
		<a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'geot-hide-notice', 'update', remove_query_arg( 'do_update_geot' ) ), 'geot_hide_notices_nonce', '_geot_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'geot' ); ?></a>
	</p>
</div>
