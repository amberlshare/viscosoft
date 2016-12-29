<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $woocommerce, $product;
$sy_options = kameleon_get_options_name();

$product_layout = (isset($sy_options['w_psingleproduct_layout']) ) ? $sy_options['w_psingleproduct_layout'] : 'normal';
$product_layout = (null !== get_post_meta($product->id, 'w_psingleproduct_layout_unique',true) && get_post_meta($product->id, 'w_psingleproduct_layout_unique',true) == 1 ) 
? get_post_meta($product->id , 'w_psingleproduct_layout' , true) 
: $product_layout ;
$social_share =  (null !== get_post_meta($product->id, 'social_woocommerce_active',true) ) ? get_post_meta($product->id, 'social_woocommerce_active',true) : $sy_options['social_woocommerce_active'];
?>
<div class="images">

	<?php if ( has_post_thumbnail() ) { ?>
		<div class="km-product-im-con">

			<?php 
			$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
			$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
			$image       	= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'sayen-small-thumbnail-nocrop km-sin-woo-thumb' ), array(
				'title'	=> $image_title,
				'alt'	=> $image_title
				) );	
			if($social_share == 1 && $product_layout == "modern"):
?>
	<div class="km-product-share">
		<?php echo kameleon_social_share_box_p("product"); ?>
	</div>	
<?php 
	endif;
			$attachment_count = count( $product->get_gallery_attachment_ids() );
			$gallery     = $attachment_count > 0 ? '[product-gallery]' : '';


			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_caption, $image ), $post->ID );

		} else {

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), esc_html__( 'Placeholder', 'kameleon' ) ), $post->ID );

		}
	?>
</div>
	<?php do_action( 'woocommerce_product_thumbnails' ); ?>
	<?php 
		if($product_layout == "modern"): 
		$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
		$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
	?>	
	<div class="km-modern-cattag">
	<?php do_action( 'woocommerce_product_meta_start' ); ?>
	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
		<span class="sku_wrapper"><?php _e( 'SKU:', 'woocommerce' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce' ); ?></span></span>
	<?php endif; ?>
	<span class="km-modern-cattagicon"><i class="fa fa-tags"></i></span>
	<?php echo $product->get_categories( '', '<span class="km-modern-cats clsb-hover-parent">' . _n( '', '', $cat_count) . ' ', '</span>' ); ?>
	<?php echo $product->get_tags( '', '<span class="km-modern-cats clsb-hover-parent">' . _n( '', '', $tag_count) . ' ', '</span>' ); ?>
	<?php do_action( 'woocommerce_product_meta_end' ); ?>
	</div>
	<?php endif; ?>	

</div>
