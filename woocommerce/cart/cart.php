<div id="km-wrapper-insider">
<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

<?php do_action( 'woocommerce_before_cart_table' ); ?>

<div class="kameleon-cart-products kameleon-woo-left-one">	
	<div class="kameleon-cart-pr-section">	
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>
		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<div class="kameleon-cart-sproduct">
					<div class="kameleon-cart-sp-remove">
						<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">x</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), esc_html__( 'Remove this item', 'kameleon' ) ), $cart_item_key );
						?>
					</div>
	
					<div class="kameleon-cart-sp-info">
							<div class="kameleon-cart-sp-img">
								<?php
									$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
									if ( ! $_product->is_visible() )
										echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
									else
										printf( '<a href="%s">%s</a>', $_product->get_permalink( $cart_item ), $thumbnail );
								?>								
							</div>
							<div class="kameleon-cart-sp-infotext">
								<strong>
									<?php
										if ( ! $_product->is_visible() )
											echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
										else
											echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s </a>', $_product->get_permalink( $cart_item ), $_product->get_title() ), $cart_item, $cart_item_key );								
										echo WC()->cart->get_item_data( $cart_item );
			               				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
			               					echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'kameleon' ) . '</p>';
									?>									
								</strong>
								<span class="kameleon-cart-sp-infosubtitle">
									<?php if(null !== get_post_meta($product_id,"product_subtitle",true)) echo  get_post_meta($product_id,"product_subtitle",true); ?>
								</span>
								<span class="kameleon-cart-sp-infoprice">
									<?php
										echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
									?>
								</span>
							</div>
						
					</div>				

					<div class="kameleon-cart-sp-quantity">
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
						?>
					</div>

					<div class="kameleon-cart-sp-subtotal">
						<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</div>
				</div>
				<?php
			}
		}

		do_action( 'woocommerce_cart_contents' );
		?>
		<div class="kameleon-update-cart">
			<a class="continue-shopping" href="<?php echo esc_url(get_permalink( woocommerce_get_page_id( 'shop' ))); ?>"> <?php esc_html_e( 'Continue Shopping', 'kameleon' ); ?></a>
			<input type="submit" class="button" name="update_cart" value="<?php esc_html_e( 'Update Cart', 'kameleon' ); ?>" id="km-cart-update" />
			<?php do_action( 'woocommerce_cart_actions' ); ?>
			<?php wp_nonce_field( 'woocommerce-cart' ); ?>
		</div>
		<?php do_action( 'woocommerce_after_cart_contents' ); ?>	
	</div>
</div>

<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>

<div class="kameleon-cart-collaterals kameleon-woo-right-one">
	<?php do_action( 'woocommerce_cart_collaterals' ); ?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>