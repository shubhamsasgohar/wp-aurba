<?php

if ( isset( $_POST['geot-debug-button'] ) &&
     isset( $_POST['geot-debug-content'] ) &&
     isset( $_POST['geot-debug-nonce'] )
) {
	nocache_headers();
	header( "Content-type: text/plain" );
	header( "Content-Disposition: attachment; filename=debug-data.txt" );

	echo  wp_strip_all_tags( $_POST['geot-debug-content'] );
	wp_die();
}
