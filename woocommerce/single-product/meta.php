<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;
$sy_options = kameleon_get_options_name();


$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
$social_share =  (null !== get_post_meta($product->id, 'social_woocommerce_active',true) ) ? get_post_meta($product->id, 'social_woocommerce_active',true) : $sy_options['social_woocommerce_active'];

$product_layout = (isset($sy_options['w_psingleproduct_layout']) ) ? $sy_options['w_psingleproduct_layout'] : 'normal';
$product_layout = (null !== get_post_meta($product->id, 'w_psingleproduct_layout_unique',true) && get_post_meta($product->id, 'w_psingleproduct_layout_unique',true) == 1 ) 
? get_post_meta($product->id , 'w_psingleproduct_layout' , true) 
: $product_layout ;

?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php _e( 'SKU:', 'woocommerce' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce' ); ?></span></span>

	<?php endif; ?>

	<?php echo $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', $cat_count, 'woocommerce' ) . ' ', '</span>' ); ?>

	<?php echo $product->get_tags( ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', $tag_count, 'woocommerce' ) . ' ', '</span>' ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>

<?php if($social_share == 1 && $product_layout =='normal'): ?>
<div class="km-social-share-normal">
	<?php echo kameleon_social_share_box_p("product"); ?>
</div>
<?php endif; ?>
