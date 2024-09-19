<?php $container_element = ( ! empty( $col->settings->container_element ) ? $col->settings->container_element : 'div' ); ?>
<?php ob_start(); ?>
<<?php echo $container_element; ?><?php echo FLBuilder::render_column_attributes( $col ); ?>>
<div class="fl-col-content fl-node-content"><?php FLBuilder::render_modules( $col ); ?></div>
</<?php echo $container_element; ?>>
<?php
$out = ob_get_clean();
echo apply_filters( 'geot/wpbeaver/template/row', $out, $col );
?>