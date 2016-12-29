<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();



// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', esc_html__( 'You must be logged in to checkout.', 'kameleon' ) );
	return;
}

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ); ?>
<div id="km-wrapper-insider">
	
	<form name="checkout" method="post" class="checkout woocommerce-checkout kameleon-woo-left-one" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">
		<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>
		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
			<div class="kameleon-cart-products kameleon-billing-details">
			    <div><?php do_action( 'woocommerce_checkout_billing' ); ?></div>
			    <div><?php do_action( 'woocommerce_checkout_shipping' ); ?></div>
				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>		
			</div>
		<?php endif; ?>	 
		<div class="kameleon-cart-collaterals kameleon-billing-review kameleon-woo-right-one">
			<div class="shop_table">
				<?php if ( WC()->cart->coupons_enabled() ) { ?>
					<div class="kameleon-checkout-coupon">		
						<h3><?php echo esc_html__('Coupon Code','kameleon'); ?></h3>			
						<div class="km-coupon-code-sc">
							<div class="km-coupon-code-inputs">
								<input type="text"  onkeyup="kameleon_copy_input(this,'#coupon_code',event,'#submit-coupon')" class="input-text" id="coupon_codeFake" value="" placeholder="<?php esc_html_e( 'Coupon code', 'kameleon' ); ?>" /> 				
								<div class="button" id="submit-coupon-fake"  onClick="document.getElementById('submit-coupon').click();"><?php esc_html_e( 'Apply Coupon', 'kameleon' ); ?></div>
								<?php do_action( 'woocommerce_cart_coupon' ); ?>
							</div>						
						</div>
					</div>
				<?php } ?>	
				

			   	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
			   	<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			   	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>		
			   	<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>		    				
			</div>
		</div>
	</form>
	<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>