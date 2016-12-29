<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

?>
<li <?php post_class(); ?>>
		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>	
		<?php if ( $product->is_on_sale() ) : ?>
			<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'kameleon' ) . '</span>', $post, $product ); ?>
		<?php endif; ?>
			<div class="km-woo-img-wrapper">
				<a class="km-woo-link-wrapper" href="<?php echo esc_url(get_the_permalink()); ?>"></a>
				<?php 
					$attachment_ids = $product->get_gallery_attachment_ids();
					echo $product->get_image('kameleon-small-thumbnail-nocrop'); 
					if ($attachment_ids) {
						echo '<div class="km-woo-im">' .wp_get_attachment_image($attachment_ids[0], 'kameleon-small-thumbnail-nocrop') .'</div>';						
					}
					?>
				<div class="km-woo-action-area">
					<?php do_action( 'woocommerce_after_shop_loop_item' );?>	

				</div>
			</div>

		<div class="km-woo-title">
			<?php 
				the_title();
				do_action( 'woocommerce_after_shop_loop_item_title' ); 
			?>
		</div>
		<div class="km-product-subtile"><?php if(null !== get_post_meta(get_the_ID(),"product_subtitle",true)) echo  get_post_meta(get_the_ID(),"product_subtitle",true); ?></div>
		
		<div>
			<?php 				
				if ( $rating_html = $product->get_rating_html() ) : ?>
					<div class="km-woo-rating">
						<?php echo $rating_html; ?>
					</div>				
				<?php endif; ?>
		</div>
	
</li>


