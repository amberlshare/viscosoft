<?php
/**
 * Checkout coupon form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! WC()->cart->coupons_enabled() ) {
	return;
}

?>
<?php if ( WC()->cart->coupons_enabled() ) { ?>
<form method="post" id="kameleonFormCouponCheckout" class="checkout_coupon" style="display: none;">
		<input type="hidden" name="coupon_code" class="input-text" id="coupon_code"/> 
		<input class="apply_coupon" type="submit" name="apply_coupon" id="submit-coupon" style="display: none;">
		<?php do_action( 'woocommerce_cart_actions' ); ?>
		<?php wp_nonce_field( 'woocommerce-cart' ); ?>	
</form>

<?php } ?>	