<?php

//builds the html structure of the floating cart, given the cart data
function bw_woofc_build_floatingcartcontent_pro( $cartdata, $settings )
{

        $result = "";

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

        $option_positioningstyle = "";

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

        if ( $settings['general_position'] == 'right')
        {
                $option_positioningstyle = "right:10px";
        }
        if ( $settings['general_position'] == 'left')
        {
                $option_positioningstyle = "left:10px";
        }


        //Main box
        $result .= "<div id=\"bravowp-woo-floatingcart\" style=\" z-index:10000; background-color:#fff;position:fixed;top:175px;" . $option_positioningstyle . ";border:1px solid #d9d9d9; \" >";


        //Title pane
        $result .= "<div style=\" cursor:pointer;background-color: " . $settings['colors_header_background'] . "; color: " . $settings['colors_header_text'] . "; font-size: 14px; padding: 10px 20px; \">";
        $result .= $settings['text_headermain'];
        $itemsText = $settings['text_items'];
        if ( $cartdata["itemsCount"] == 1 )
        {
                $itemsText = $settings['text_item'];
        }
        $result .= "<div style=\" font-size:12px;margin-top:3px;color:" . $settings['colors_header_subtotaltext'] . " \">" . $settings['text_headersubtotal'] . ": " . $cartdata["cartTotal"] . "&nbsp;&nbsp;(" . $cartdata["itemsCount"] . " " . $itemsText . ")</div>";
        $result .= "</div>";
        //Body pane
        $result .= "<div style=\" color: " . $settings['colors_body_text'] . ";background-color:" . $settings['colors_body_background'] . ";position:relative;overflow: hidden;" . $option_heightbodypane .  ";\"  id='bravowp-woo-floatingcart-bodypane' >";


        //ajax loader
        $result .= "<div id='bravowp-woo-floatingcart-loader' class='bravowp-woo-floatingcart-ajaxloader' style=\" display:none; \">";
        $result .= "<img src='" . bw_woofc_globals_plugin_url . "\images\loading.gif' >";
        $result .= "</div>";

        //Items pane
        $result .= "<div style=\" color:black;padding: 10px 20px 0px 20px; \" >";

        if ( $cartdata["products"] != null )
        {
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
        }

        //Items pane
        $result .= "</div>";


        if ( count( $cartdata["products"] ) > 0 )
        {
                //Subtotal pane
                $result .= "<div style=\" padding-bottom:10px; \" >";
                $result .= "<div style=\" margin: 0px 5%; text-align: center; border-radius: 0px; padding: 4px 0px; font-size: 14px; font-weight: bold; background-color: " . $settings['colors_subtotal_background'] . "; color: " . $settings['colors_subtotal_text'] . "; \" >";
                $result .= $settings['text_bodysubtotal'] . $cartdata["cartTotal"];
                $result .= "</div>";
                $result .= "</div>";
        }
        else
        {
                $result .= "<div style=\" margin:0px 10px 10px 10px;font-size:12px; \">" . $settings['text_emptycartbodytext'] . "</div>";
        }


        if ( count( $cartdata["products"] ) > 0 )
        {
                //Buttons pane
                $result .= "<div style=\" padding-bottom:15px;text-align: center;margin-right: 10px; margin-left: 10px; \" >";
                if ( $settings['button_viewcart_show'] == 'yes' )
                {
                        $result .= "<a href='" . $cartdata['cartUrl'] . "' " . $linkCartPageAlertDemo . " style=\" margin-right:5px;background-color: " . $settings['colors_viewcart_background'] . ";border-color: #d9d9d9;color: " . $settings['colors_viewcart_text'] . ";padding: 8px 12px;text-align: center;font-size:12px;cursor:pointer;text-decoration:none;font-weight: bold; \" >";
                        $result .= $settings['text_viewcart'];
                        $result .= "</a>";
                }
                if ( $settings['button_checkout_show'] == 'yes' )
                {
                        $result .= "<a href='" . $cartdata['checkoutUrl'] . "' " . $linkCheckOutPageAlertDemo . " style=\" background-color: " . $settings['colors_checkout_background'] . ";border-color: #d9d9d9;color: " . $settings['colors_checkout_text'] . ";padding: 8px 12px;text-align: center;font-size:12px;cursor:pointer;text-decoration:none;font-weight: bold; \" >";
                        $result .= $settings['text_checkout'];
                        $result .= "</a>";
                }
                $result .= "</div>";
        }
        else
        {
                $result .= "<div style=\" padding-bottom:15px;text-align: center;margin-right: 10px; margin-left: 10px; \" >";
                $result .= "<a href='" . $cartdata['shopUrl'] . "' style=\" background-color: #6DABE4;border-color: #4cae4c;color: #ffffff;border-radius: 0px;padding: 8px 12px;text-align: center;font-size:12px;cursor:pointer;text-decoration:none;font-weight: bold; \" >";
                $result .= $settings['text_emptycart'];
                $result .= "</a>";
                $result .= "</div>";
        }



        //Body pane
        $result .= "</div>";



        //Footer pane
        $result .= "<div style=\" height:10px;background-color:" . $settings['colors_footer_background'] . "; \" id='bravowp-woo-floatingcart-footerpane' >";
        $result .= "</div>";


        //Main box
        $result .= "</div>";


        return $result;

}


//returns 1 if the cart must be displayed
function bw_woofc_showforcurrentpage_pro()
{


        $url = explode('?', 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        $pageId = url_to_postid($url[0]);
        $currentURL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        bw_woofc_systemlog_addentry("INFO","bw_woofc_showforcurrentpage_pro","PageID: " . $pageId);

        //getting settings
        $settings = bw_woofc_readsettings_proversion();
        $pagesSettings = $settings['pages'];
        $pagesSettingsShowURLS = $settings['pages_showurls'];
        $pagesSettingsHideURLS = $settings['pages_hideurls'];


        if (array_key_exists('page_bwwoofc-admin-contentpane-control-settings-pageshow-' . $pageId, $pagesSettings) == false) {
                $pagesSettings['page_bwwoofc-admin-contentpane-control-settings-pageshow-' . $pageId] = 'yes';
        }

        //shows the cart only in the correct pages, according to settings
        if ( $pagesSettings['page_bwwoofc-admin-contentpane-control-settings-pageshow-' . $pageId]  == "yes")
        {
                $result = 1;
        }
        else
        {
                $result = 0;
        }

        //checks if it is in the forced show urls list
        if ( $pagesSettingsShowURLS != '' )
        {
                $pagesSettingsShowURLS_array = preg_split("/\r\n|\n|\r/", $pagesSettingsShowURLS);
                $isContained = in_array(strtolower($currentURL), array_map('strtolower', $pagesSettingsShowURLS_array));
                if ($isContained)
                {
                        $result = 1;
                }
        }

        //checks if it is in the forced hide urls list
        if ( $pagesSettingsHideURLS != '' )
        {
                $pagesSettingsHideURLS_array = preg_split("/\r\n|\n|\r/", $pagesSettingsHideURLS);
                $isContained = in_array(strtolower($currentURL), array_map('strtolower', $pagesSettingsHideURLS_array));
                if ($isContained)
                {
                        $result = 0;
                }
        }

        //show on all pages, but can never be displayed on cart or checkout
        if (  is_cart() ||  is_checkout() )
        {
                $result = 0;
        }

        return $result;

}




?>
