<?php
$theme_includes = apply_filters( 'themify_theme_includes',
	array(	'../themify-float/themify/themify-database.php',
			'../themify-float/themify/class-themify-config.php',
			'../themify-float/themify/themify-utils.php',
			'../themify-float/themify/themify-config.php',
			'../themify-float/themify/themify-modules.php',
			'../themify-float/theme-options.php',
			'../themify-float/theme-modules.php',
			'../themify-float/theme-functions.php',
			'../themify-float/custom-modules.php',
			'../themify-float/custom-functions.php',
			'../themify-float/themify/themify-widgets.php' ));
			
foreach ( $theme_includes as $include ) { locate_template( $include, true ); }

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

/**
 * @snippet       Disable Variable Product Price Range
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/disable-variable-product-price-range-woocommerce/
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 2.4.7
 */
 
add_filter( 'woocommerce_variable_sale_price_html', 'bbloomer_variation_price_format', 10, 2 );
 
add_filter( 'woocommerce_variable_price_html', 'bbloomer_variation_price_format', 10, 2 );
 
function bbloomer_variation_price_format( $price, $product ) {
 
// Main Price
$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
$price = $prices[0] !== $prices[1] ? sprintf( __( '', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
 
// Sale Price
$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
sort( $prices );
$saleprice = $prices[0] !== $prices[1] ? sprintf( __( '', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
 
if ( $price !== $saleprice ) {
$price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
}
return $price;
}

add_action( 'init', 'jk_remove_wc_breadcrumbs' );
function jk_remove_wc_breadcrumbs() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}

add_filter( 'woocommerce_get_breadcrumb', '__return_false' );


function register_my_menu() {
  register_nav_menu('product-menu',__( 'Product Menu' ));
}
add_action( 'init', 'register_my_menu' );

add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);

function special_nav_class ($classes, $item) {
    if (in_array('current-menu-item', $classes) ){
        $classes[] = 'active';
    }
    return $classes;
}


//Warranty Info

add_action( 'woocommerce_after_add_to_cart_button', 'content_after_addtocart_button' , 45 );
 
function content_after_addtocart_button() {
if ( is_single( $post = 'the-mattress' ) ) {
    echo '<div id="warrantyinfo"><img src="https://s3.amazonaws.com/viscosoft/wp-content/uploads/2016/10/10174451/SHOP.MATRESS.10.year_.warranty.web_.png" width="300px" /></div>';
  }
  elseif ( is_single( $post = 'pillow' ) ) {
    echo '<div id="warrantyinfo"><img align="center" src="https://s3.amazonaws.com/viscosoft/wp-content/uploads/2016/10/10174602/SHOP.3.year_.warranty.web_.png" width="120px" /></div>';
  }
  elseif ( is_single( $post = 'protector' ) ) {
    echo '<div id="warrantyinfo"><img align="center" src="https://s3.amazonaws.com/viscosoft/wp-content/uploads/2016/10/10174451/SHOP.MATRESS.10.year_.warranty.web_.png" width="300px" /></div>';
  } 
}
  
  
// Move WooCommerce price
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

// Display Price For Variable Product With Same Variations Prices
add_filter('woocommerce_available_variation', function ($value, $object = null, $variation = null) {
    if ($value['price_html'] == '') {
        $value['price_html'] = '<span class="price">' . $variation->get_price_html() . '</span>';
    }
    return $value;
}, 10, 3);


// Remove buttons for mattress size
//remove_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'wbv_button_variations_attribute_options_html', 10);
//add_filter('woocommerce_dropdown_variation_attribute_options_html', 'wbv_button_disable_for_specific_attribute', 11, 2);
/**
 * Causes the function to return the default HTML if the attribute is the one we're attempting to AVOID creating the buttons for.
* You could also make this more specific if that attribute is used elsewhere - just look in the $args array and make it match a product ID as well as the attribute name.
* Note that we are replacing the function found in wbv-functions.php line 444. See that function for additional documentation
         
 * @param  [type] $html [description]
 * @param  [type] $args [description]
 * @return [type]       [description]
 */
//function wbv_button_disable_for_specific_attribute( $html, $args ){
//{
        
  //      global $product;
       
        
  //      $product_id = $args['product']->post->ID;
        
        //Use this line instead of 101 if you need to target a specific product
        //if( $args['attribute'] != 'Firmness' && $product_id = 'whichever_product_you_are_modifying' ){ 
        
        
  //      if( $args['attribute'] != 'pa_mattress' && is_single( $post = 'the-mattress' ) ){
            return $html;
   //     }

  //      if (wbv_is_product_enabled($product->id)) {
            //Display the buttons
   //         wbv_display_buttons($args);


   //     } else {
   //         return $html;
   //     }
  //  }
//}

