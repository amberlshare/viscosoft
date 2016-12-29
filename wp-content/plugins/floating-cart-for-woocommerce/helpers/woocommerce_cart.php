<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//these functions will add data to the fragments result
function bw_woofc_add_to_cart_fragment( $fragments )
{

	if ( class_exists( 'WooCommerce' ) )
	{
		if (bw_woofc_globals_isproversion())
		{
			//pro
			$settings = bw_woofc_readsettings_proversion();
			$dataStructureFromCart = bw_woofc_build_data_fromcurrentcart();
			$cartContent = bw_woofc_build_floatingcartcontent_pro( $dataStructureFromCart, $settings );
		}
		else
		{
			//reading settings
			$settings = bw_woofc_readsettings();
			$dataStructureFromCart = bw_woofc_build_data_fromcurrentcart();
			$cartContent = bw_woofc_build_floatingcartcontent( $dataStructureFromCart, $settings );
		}


		//capturing
		ob_start();

		//this will be output to ob;
		echo $cartContent;

		//adding to fragment array
		$fragments['bw_wwo_floatingcartdata'] = ob_get_clean();

		//returning the object
		return $fragments;

	}

}
//attaching the above function
add_filter( 'add_to_cart_fragments', 'bw_woofc_add_to_cart_fragment' );






//adds the plugin in the wp_folder so it does not interferce with woo_commerce shopping page ajax calls
function bw_woofc_injectcontent()
{


	if ( class_exists( 'WooCommerce' ) )
	{


		if (bw_woofc_globals_isproversion())
		{
			//pro version
			if ( bw_woofc_showforcurrentpage_pro() == 1 )
			{

				$settings = bw_woofc_readsettings_proversion();
				//loading cart even without Add to Cart events, on load
				$dataStructureFromCart = bw_woofc_build_data_fromcurrentcart();
				$cartContent = bw_woofc_build_floatingcartcontent_pro( $dataStructureFromCart, $settings );

			}
		}
		else
		{
			//normal version
			if ( bw_woofc_showforcurrentpage() == 1 )
			{
				//reading settings
				$settings = bw_woofc_readsettings();
				//loading cart even without Add to Cart events, on load
				$dataStructureFromCart = bw_woofc_build_data_fromcurrentcart();
				$cartContent = bw_woofc_build_floatingcartcontent( $dataStructureFromCart, $settings );
			}
		}



		//the content is added to the html of the page directly, in this case.
		if (isset($cartContent))
		{
			echo $cartContent;
		}

	}

}
add_action( 'wp_footer', 'bw_woofc_injectcontent');





//builds the data structure with information that will be given to the construction function later
function bw_woofc_build_data_fromcurrentcart( )
{

	global $woocommerce;

	//getting cart data
	if ( is_object($woocommerce->cart ) )
	{

		$cartItems = $woocommerce->cart->get_cart();

		//array holding the data that will be attached to fragments
		$arrayForFragments = [];

		//generic information about the cart
		$arrayForFragments["cartTotal"] = $woocommerce->cart->get_cart_total();
		$arrayForFragments["cartUrl"] = $woocommerce->cart->get_cart_url();
		$arrayForFragments["checkoutUrl"] = $woocommerce->cart->get_checkout_url();
		$arrayForFragments["shopUrl"] = get_permalink( woocommerce_get_page_id( 'shop' ) );
		$arrayForFragments["currencySymbol"] = get_woocommerce_currency_symbol();
		$arrayForFragments["itemsCount"] = WC()->cart->get_cart_contents_count();

		//building data set for products in the cart
		$arrayForFragmentsProducts = [];
		foreach($cartItems as $cartItemkey => $cartItem  )
		{

			//getting data from the Post of this Woo Product
			$cartItemProduct = $cartItem['data']->post;

			//storing only the informations we need
			$productToAddToCart = [];
			$productToAddToCart["productTitle"] = $cartItemProduct->post_title;
			$productToAddToCart["quantity"] = $cartItem['quantity'];
			$productToAddToCart["price"] = wc_price( $cartItem['data']->price );
			$productToAddToCart["deleteItemUtl"] = $woocommerce->cart->get_remove_url( $cartItemkey );


			//getting product image
			$productObject = wc_get_product( $cartItemProduct->ID );
			$productToAddToCart["image"] = $productObject->get_image( 32 );

			//storing this product in the array of products
			array_push( $arrayForFragmentsProducts, $productToAddToCart );

		}

		//pushing the products in the array of data
		$arrayForFragments["products"] = $arrayForFragmentsProducts;

		return $arrayForFragments;

	}

}




?>
