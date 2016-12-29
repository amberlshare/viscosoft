<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//builds the html structure of the floating cart, given the cart data
function bw_woofc_build_floatingcartcontent( $cartdata, $settings )
{

        $result = "";

        //change the line below to customize your cart
        $color_main = "#337ab7";

        //buttons links
        $linkCheckOutPage = $cartdata['checkoutUrl'];
        $linkCartPage = $cartdata['cartUrl'];
        $linkCheckOutPageAlertDemo = "";
        $linkCartPageAlertDemo = "";
        if (bw_woofc_demo_is_active() == 1)
        {
                $linkCheckOutPageAlertDemo = " onclick='alert(\"For the Demo, these links are disabled. Thank you!\")'";
                $linkCheckOutPage = "#";
                $linkCartPageAlertDemo = " onclick='alert(\"For the Demo, these links are disabled. Thank you!\")'";
                $linkCartPage = "#";
        }

        $option_heightbodypane = "";
        $option_displayarrowup = "";
        $option_displayarrowdown = "";

        if ( $settings['general_defaultmode'] == 'extended')
        {
                $option_heightbodypane = "height:100%";
                $option_displayarrowup = "display:none";
                $option_displayarrowdown = "display:block";
        }
        else
        {
                $option_heightbodypane = "height:0px";
                $option_displayarrowup = "display:block";
                $option_displayarrowdown = "display:none";
        }


        //Main box
        $result .= "<div id=\"bravowp-woo-floatingcart\" style=\" z-index:10000; min-width: 200px;background-color:#fff;position:fixed;bottom:5px;right:10px;border:1px solid #b3b3b3;box-shadow: 0 2px 1em 0 rgba(0, 0, 0, 0.4);border-radius:4px; \" >";


        //Title pane
        $result .= "<div style=\" cursor:pointer;background-color: " . $color_main . "; color: #fff; font-size: 14px; padding: 8px 10px; \" id='bravowp-woo-floatingcart-titlepane' onclick='bravowp_woo_floatingcart_togglecart();' >";
        $result .= "Your Shopping Cart";
        $result .= "<div style=\" font-size:12px;margin-top:3px; \">Subtotal: " . $cartdata["cartTotal"] . "&nbsp;&nbsp;(" . $cartdata["itemsCount"] . " items)</div>";
        $result .= "<img src='" . bw_woofc_globals_plugin_url . "\images\arrow-down-icon.png' id='bravowp-woo-floatingcart-titlepane-downicon' style='float: right; right: 0px; top: 0px; position: absolute; margin-right: 10px; margin-top: 10px;width:16px;" . $option_displayarrowdown . ";' >";
        $result .= "<img src='" . bw_woofc_globals_plugin_url . "\images\arrow-up-icon.png' id='bravowp-woo-floatingcart-titlepane-upicon' style='float: right; right: 0px; top: 0px; position: absolute; margin-right: 10px; margin-top: 10px;width:16px;" . $option_displayarrowup . ";' >";
        $result .= "</div>";

        //Body pane
        $result .= "<div style=\" background-color:#fff;position:relative;overflow: hidden;" . $option_heightbodypane .  ";\"  id='bravowp-woo-floatingcart-bodypane' >";


        //ajax loader
        $result .= "<div id='bravowp-woo-floatingcart-loader' class='bravowp-woo-floatingcart-ajaxloader' style=\" display:none; \">";
        $result .= "<img src='" . bw_woofc_globals_plugin_url . "\images\loading.gif' >";
        $result .= "</div>";

        //Items pane
        $result .= "<div style=\" color:black;padding: 10px 10px 0px 10px; \" >";


        //listing products in the cart
        foreach($cartdata["products"] as $Item )
        {

                $result .= "<div>";

                //product image
                $result .= "<div style=\"width:32px;height:32px;float:left;margin-right:10px;\" >";
                $result .= $Item["image"];
                $result .= "</div>";

                //product data
                $result .= "<div style=\"margin-left:42px;\" >";

                $result .= "<div style=\"white-space:nowrap;font-size:12px;\" >";
                $result .= $Item["productTitle"];
                $result .= " <a onclick=\"bravowp_woo_floatingcart_displayloadingimage();\" href='" . $Item["deleteItemUtl"] . "'> <img style=\"width:10px;\" src='" . bw_woofc_globals_plugin_url .  "\images\icon-delete-1.png'> </a> ";
                $result .= "</div>";

                $result .= "<div style=\"float:left;font-size:10px;\" >";
                $result .= "<span>" . $Item["quantity"] . "<span>";
                $result .= "<span>  x  <span>";
                $result .= "<span>" . $Item["price"] . "<span>";
                $result .= "</div>";

                $result .= "</div>";

                $result .= "</div>";

                $result .= "<div style=\" clear:both;height:10px; \" ></div>";

        }

        //Items pane
        $result .= "</div>";


        if ( count( $cartdata["products"] ) > 0 )
        {
                //Subtotal pane
                $result .= "<div style=\" padding-bottom:10px; \" >";
                $result .= "<div style=\" margin: 0px 5%; text-align: center; border-radius: 4px; padding: 4px 0px; font-size: 14px; font-weight: bold; background-color: #f2f2f2; color: #5f5f5f; \" >";
                $result .= "Subtotal: " . $cartdata["cartTotal"];
                $result .= "</div>";
                $result .= "</div>";
        }
        else
        {
                $result .= "<div style=\" margin:0px 10px 10px 10px;font-size:12px; \">Your shopping cart is empty</div>";
        }


        if ( count( $cartdata["products"] ) > 0 )
        {
                //Buttons pane
                $result .= "<div style=\" padding-bottom:15px;text-align: center;margin-right: 10px; margin-left: 10px; \" >";
                $result .= "<a href='" . $linkCartPage . "' " . $linkCartPageAlertDemo . " style=\" margin-right:5px;background-color: #5cb85c;border-color: #4cae4c;color: #ffffff;border-radius: 4px;padding: 8px 12px;text-align: center;font-size:12px;cursor:pointer;text-decoration:none;font-weight: bold; \" >";
                $result .= "<img style=\"width: 16px; margin-right: 4px; position: relative; vertical-align: middle !important; top: -2px;\" src='" . bw_woofc_globals_plugin_url .  "\images\icon-cart-1.png'>";
                $result .= "View Cart";
                $result .= "</a>";
                $result .= "<a href='" . $linkCheckOutPage . "' " . $linkCheckOutPageAlertDemo . " style=\" background-color: #5cb85c;border-color: #4cae4c;color: #ffffff;border-radius: 4px;padding: 8px 12px;text-align: center;font-size:12px;cursor:pointer;text-decoration:none;font-weight: bold; \" >";
                $result .= "<img style=\"width: 16px; margin-right: 4px; position: relative; vertical-align: middle !important; top: -2px;\" src='" . bw_woofc_globals_plugin_url .  "\images\icon-payment-1.png'>";
                $result .= "Checkout";
                $result .= "</a>";
                $result .= "</div>";
        }
        else
        {
                $result .= "<div style=\" padding-bottom:15px;text-align: center;margin-right: 10px; margin-left: 10px; \" >";
                $result .= "<a href='" . $cartdata['shopUrl'] . "'  style=\" background-color: #5cb85c;border-color: #4cae4c;color: #ffffff;border-radius: 4px;padding: 8px 12px;text-align: center;font-size:12px;cursor:pointer;text-decoration:none;font-weight: bold; \" >";
                $result .= "<img style=\"width: 16px; margin-right: 4px; position: relative; vertical-align: middle !important; top: -2px;\" src='" . bw_woofc_globals_plugin_url .  "\images\icon-cart-1.png'>";
                $result .= "Start Shopping!";
                $result .= "</a>";
                $result .= "</div>";
        }



        //Body pane
        $result .= "</div>";



        //Footer pane
        $result .= "<div style=\" height:10px;background-color:" . $color_main . "; \" id='bravowp-woo-floatingcart-footerpane' >";
        $result .= "</div>";


        //Main box
        $result .= "</div>";


        return $result;

}


//returns 1 if the cart must be displayed
function bw_woofc_showforcurrentpage()
{

        $result = 0;

        //getting settings
        $settings = bw_woofc_readsettings();

        //shows the cart only in the correct pages, according to settings
        if ( $settings['show_on_pages'] == "allpages")
        {
                //show on all pages, but can never be displayed on cart or checkout
                if ( ! is_cart() && ! is_checkout() )
                {
                        $result = 1;
                }
        }
        else
        {
                //must be shown only on SHOP page of woocommerce
                if ( is_shop() )
                {
                        $result = 1;
                }
        }

        return $result;

}




?>
