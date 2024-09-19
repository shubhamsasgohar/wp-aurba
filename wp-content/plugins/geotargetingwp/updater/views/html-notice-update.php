<?php
/**
 * Admin View: Notice - Update
 *
 * @package Admin / updater
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$update_url = wp_nonce_url(
	add_query_arg( 'do_update_geot', 'true', admin_url( 'admin.php?page=geot-settings' ) ),
	'geot_db_update',
	'geot_db_update_nonce'
);

?>
<div id="message" class="notice notice-warning">
	<p>
		<strong><?php esc_html_e( 'GeotargetingWP database update required', 'geot' ); ?></strong>
	</p>
	<p>
		<?php
			esc_html_e( 'GeotargetingWP has been updated! To keep things running smoothly, we have to update your database to the newest version. The database update process runs in the background and may take a little while, so please be patient.', 'geot' );
		?>
	</p>
	<p><strong>
		<?php
			esc_html_e( 'We strongly recommend to create a backup before updating the database.', 'geot' );
		?></strong>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( $update_url ); ?>" class="button button-primary">
			<?php esc_html_e( 'Update GeotargetingWP Database', 'geot' ); ?>
		</a>
	</p>
</div>
