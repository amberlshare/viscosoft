<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$pn_enabled = $pn_stype = $pn2_full = $pn2_position = '';
$sy_options = kameleon_get_options_name();
$post_id = get_the_ID();
if(is_product()){
	$pn_enabled = (null !== get_post_meta($post_id,'product-pn_enabled',true)) ? get_post_meta($post_id,'product-pn_enabled',true) : $sy_options['product-pn_enabled'] ;
	$pn_stype = (null !== get_post_meta($post_id,'product-pn_stype',true)) ? get_post_meta($post_id,'product-pn_stype',true) : $sy_options['product-pn_stype'] ;
	
	$pn2_full  	 =  (get_post_meta($post_id,'nextprevious_unique' ,true) == 1 && null !== get_post_meta($post_id,'pn_type2_full',true) ) ? get_post_meta($post_id,'pn_type2_full',true) : $sy_options['product-pn_type2_full'] ;
	$pn2_position = (get_post_meta($post_id,'nextprevious_unique' ,true) == 1 && null !== get_post_meta($post_id,'pn_type2_position',true) ) ? get_post_meta($post_id,'pn_type2_position',true) : $sy_options['product-pn_type2_position'] ;	
	
	

		//For Edge and Wide Previous Next Styling
	if($pn_enabled == 1 && ($pn_stype == 'type_1' || $pn_stype == 'type_3'))
			kameleon_next_previous_wrapper('product',$pn_stype);		

	if($pn_enabled == 1 && $pn_stype == 'type_2' && ($pn2_full == 0 || $pn2_full == 1 ) && $pn2_position == 'top')
			kameleon_next_previous_wrapper('product',$pn_stype);
	
		
	
}

?>

<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="km-woo-single-insider">	
		<?php
			/**
			 * woocommerce_before_single_product_summary hook.
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );	
		?>

		<div class="summary entry-summary">
			<div class="center-content">
				<?php
					/**
					 * woocommerce_single_product_summary hook.
					 *
					 * @hooked woocommerce_template_single_title - 5
					 * @hooked woocommerce_template_single_rating - 10
					 * @hooked woocommerce_template_single_price - 10
					 * @hooked woocommerce_template_single_excerpt - 20
					 * @hooked woocommerce_template_single_add_to_cart - 30
					 * @hooked woocommerce_template_single_meta - 40
					 * @hooked woocommerce_template_single_sharing - 50
					 */
					do_action( 'woocommerce_single_product_summary' );
				?>
				
			</div>

		</div><!-- .summary -->
	</div>
		<?php 
			if($pn_enabled == 1 && $pn_stype == 'type_2' && $pn2_full == 0 && $pn2_position == 'bottom')
				kameleon_next_previous_wrapper('product',$pn_stype); 
		?>
	<?php
		/**
		 * woocommerce_after_single_product_summary hook.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>
	<meta itemprop="url" content="<?php the_permalink(); ?>" />
</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>