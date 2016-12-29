<?php
/**
 * Show error messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ){
	return;
}

?>
<div class="kameleon-woo-error woo-error ">
	<i class="fa fa-times kameleon-woo-error-closer" onclick="kameleon_close_parent(this);"></i>
	<?php foreach ( $messages as $message ) : ?>
		<?php echo wp_kses_post( $message ); ?>
	<?php endforeach; ?>
</div>
