<?php
/**
 * Product quantity inputs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="kameleon-quantity-chooser">
	<div class="quantity kameleon_qty">
		<div class="kameleon_qty_but qty_but_minus" onclick="minus_number(this);">-</div>
		<input type="number" step="<?php echo esc_attr( $step ); ?>" <?php if ( is_numeric( $min_value ) ) : ?>min="<?php echo esc_attr( $min_value ); ?>"<?php endif; ?> <?php if ( is_numeric( $max_value ) ) : ?>max="<?php echo esc_attr( $max_value ); ?>"<?php endif; ?> name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'kameleon' ) ?>" class="input-text qty text" size="4" >
		<div class="kameleon_qty_but qty_but_plus" onclick="plus_number(this);">+</div>
	</div>	
</div>
