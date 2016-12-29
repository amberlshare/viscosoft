<?php 	
	//WooCommerce Home Page
	get_header(); 
	$sy_options = kameleon_get_options_name();
	$sb_active = get_post_meta(get_the_ID(), 'sidebar_active',true);
	$sb_pos = get_post_meta(get_the_ID(), 'sidebar_position',true);	
	if(is_shop()){
		$sb_active = $sy_options['sidebar_woocommerce_active'];
		$sb_pos = $sy_options['sidebar_woocommerce_position'];		
	}
	$sb_position = "none";
	if($sb_active == 1){
		$sb_position = $sb_pos;
	}

	$pn_enabled = $pn_stype = $pn2_full = $pn2_position = '';
	
	$product_layout = (isset($sy_options['w_psingleproduct_layout']) ) ? $sy_options['w_psingleproduct_layout'] : 'normal';
	//Check fo WooCommerce Single Page Modern Look
	if(is_product()){
		$product_id = get_the_ID();				
		$product_layout = (null !== get_post_meta($product_id, 'w_psingleproduct_layout_unique',true) && get_post_meta($product_id, 'w_psingleproduct_layout_unique',true) == 1 ) 
		? get_post_meta($product_id , 'w_psingleproduct_layout' , true) 
		: $product_layout ;
		$pn_enabled = (null !== get_post_meta($product_id,'product-pn_enabled',true)) ? get_post_meta($product_id,'product-pn_enabled',true) : $sy_options['product-pn_enabled'] ;
		$pn_stype = (null !== get_post_meta($product_id,'product-pn_stype',true)) ? get_post_meta($product_id,'product-pn_stype',true) : $sy_options['product-pn_stype'] ;
	$pn2_full  	 =  (get_post_meta($product_id,'nextprevious_unique' ,true) == 1 && null !== get_post_meta($product_id,'pn_type2_full',true) ) ? get_post_meta($product_id,'pn_type2_full',true) : $sy_options['product-pn_type2_full'] ;
	$pn2_position = (get_post_meta($product_id,'nextprevious_unique' ,true) == 1 && null !== get_post_meta($product_id,'pn_type2_position',true) ) ? get_post_meta($product_id,'pn_type2_position',true) : $sy_options['product-pn_type2_position'] ;
	}


?>

<div id="km-content" data-product-layout="<?php echo esc_attr($product_layout); ?>">
	<div id="km-checker" data-sidebar-position="<?php echo esc_attr($sb_position); ?>">		
		<div id="km-data" class="center-content">
			<?php woocommerce_content();  ?>
		</div>
		
		<?php if($sb_active == 1){ ?>
			<div id="kameleon-sidebar" >
				<?php get_sidebar(); ?>
			</div>
		<?php } ?>

	</div>
</div>
<?php 
	if($pn_enabled == 1 && $pn_stype == 'type_2' && $pn2_full == 1 && $pn2_position == 'bottom')
			kameleon_next_previous_wrapper('product',$pn_stype); 
?>

<?php get_footer();?>