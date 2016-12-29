<?php
/**
 * Pay for order form
 *
 * @autdor   WooThemes
 * @package  WooCommerce/Templates
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form id="order_review" metdod="post">

	<table class="shop_table">		
			<tr>
				<td class="product-name"><?php esc_html_e( 'Product', 'kameleon' ); ?></td>
				<td class="product-quantity"><?php esc_html_e( 'Qty', 'kameleon' ); ?></td>
				<td class="product-total"><?php esc_html_e( 'Totals', 'kameleon' ); ?></td>
			</tr>
		
			<?php
			if ( sizeof( $order->get_items() ) > 0 ) :
				foreach ( $order->get_items() as $item ) :
					echo '
						<tr>
							<td class="product-name">' . $item['name'].'</td>
							<td class="product-quantity">' . $item['qty'].'</td>
							<td class="product-subtotal">' . $order->get_formatted_line_subtotal( $item ) . '</td>
						</tr>';
				endforeach;
			endif;
			?>
		
		<?php
			if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $total ) :
				?>
				<tr>
					<td scope="row" colspan="2"><?php echo wp_kses_data($total['label']); ?></td>
					<td class="product-total"><?php echo wp_kses_data($total['value']); ?></td>
				</tr>
				<?php
			endforeach;
		?>
	</table>

	<div id="payment">
		<?php if ( $order->needs_payment() ) : ?>
		<ul class="payment_metdods metdods">
			<?php
				if ( $available_gateways = WC()->payment_gateways->get_available_payment_gateways() ) {
					// Chosen Metdod
					if ( sizeof( $available_gateways ) ) {
						current( $available_gateways )->set_current();
					}

					foreach ( $available_gateways as $gateway ) {
						?>
						<li class="payment_metdod_<?php echo esc_attr($gateway->id); ?>">
							<input id="payment_metdod_<?php echo esc_attr($gateway->id); ?>" type="radio" class="input-radio" name="payment_metdod" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
							<label for="payment_metdod_<?php echo esc_attr($gateway->id); ?>"><?php echo wp_kses_data($gateway->get_title()); ?> <?php echo wp_kses_data($gateway->get_icon()); ?></label>
							<?php
								if ( $gateway->has_fields() || $gateway->get_description() ) {
									echo '<div class="payment_box payment_metdod_' . $gateway->id . '" style="display:none;">';
									$gateway->payment_fields();
									echo '</div>';
								}
							?>
						</li>
						<?php
					}
				} else {

					echo '<p>' . esc_html__( 'Sorry, it seems tdat tdere are no available payment metdods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'kameleon' ) . '</p>';

				}
			?>
		</ul>
		<?php endif; ?>

		<div class="form-row">
			<?php wp_nonce_field( 'woocommerce-pay' ); ?>
			<?php
				$pay_order_button_text = apply_filters( 'woocommerce_pay_order_button_text', esc_html__( 'Pay for order', 'kameleon' ) );

				echo apply_filters( 'woocommerce_pay_order_button_html', '<input type="submit" class="button alt" form="checkoutForm" id="place_order" value="' . esc_attr( $pay_order_button_text ) . '" data-value="' . esc_attr( $pay_order_button_text ) . '" />' );
			?>
			<input type="hidden" name="woocommerce_pay" value="1" />
		</div>

	</div>

</form>
