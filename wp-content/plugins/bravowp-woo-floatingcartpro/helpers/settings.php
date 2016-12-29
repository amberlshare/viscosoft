<?php


//this is called in the main plugin page and includes the pro version settings page instead of the basic version content
function bw_woofc_inject_prosettingspage()
{
        include( bw_woofcpro_globals_plugin_path . '/controls/settings.php');
}


//this function will read the settings for this plugin and return an object for them
function bw_woofc_readsettings_proversion()
{

        //getting from db
        $optionsFromDB = get_option("bw_woofc_cartproperties","");

        //result object
        $result = [];

        //deserializing into an object
        if ($optionsFromDB != "")
        {

                $result = json_decode( $optionsFromDB, true);

        }

        if ( $result == null )
        {
                $result = [];
        }


        //putting defaults (PRO)
        if (array_key_exists('general_position', $result) == false) {
                $result['general_position'] = 'right';
        }
        if (array_key_exists('general_defaultmode', $result) == false) {
                $result['general_defaultmode'] = 'extended';
        }

        if (array_key_exists('colors_header_text', $result) == false) {
                $result['colors_header_text'] = '#fff';
        }
        if (array_key_exists('colors_header_subtotaltext', $result) == false) {
                $result['colors_header_subtotaltext'] = '#fff';
        }
        if (array_key_exists('colors_header_background', $result) == false) {
                $result['colors_header_background'] = '#337ab7';
        }

        if (array_key_exists('colors_body_text', $result) == false) {
                $result['colors_body_text'] = '#5f5f5f';
        }
        if (array_key_exists('colors_body_background', $result) == false) {
                $result['colors_body_background'] = '#ffffff';
        }

        if (array_key_exists('colors_subtotal_text', $result) == false) {
                $result['colors_subtotal_text'] = '#5f5f5f';
        }
        if (array_key_exists('colors_subtotal_background', $result) == false) {
                $result['colors_subtotal_background'] = '#f2f2f2';
        }

        if (array_key_exists('colors_viewcart_text', $result) == false) {
                $result['colors_viewcart_text'] = ' #ffffff';
        }
        if (array_key_exists('colors_viewcart_background', $result) == false) {
                $result['colors_viewcart_background'] = '#5cb85c';
        }

        if (array_key_exists('colors_checkout_text', $result) == false) {
                $result['colors_checkout_text'] = ' #ffffff';
        }
        if (array_key_exists('colors_checkout_background', $result) == false) {
                $result['colors_checkout_background'] = '#5cb85c';
        }

        if (array_key_exists('colors_footer_background', $result) == false) {
                $result['colors_footer_background'] = '#337ab7';
        }

        if (array_key_exists('text_headermain', $result) == false) {
                $result['text_headermain'] = 'Your Shopping Cart';
        }
        if (array_key_exists('text_headersubtotal', $result) == false) {
                $result['text_headersubtotal'] = 'Subtotal';
        }
        if (array_key_exists('text_bodysubtotal', $result) == false) {
                $result['text_bodysubtotal'] = 'Subtotal';
        }
        if (array_key_exists('text_viewcart', $result) == false) {
                $result['text_viewcart'] = 'View Cart';
        }
        if (array_key_exists('text_checkout', $result) == false) {
                $result['text_checkout'] = 'Checkout';
        }
        if (array_key_exists('text_emptycart', $result) == false) {
                $result['text_emptycart'] = 'Start Shopping!';
        }
        if (array_key_exists('text_emptycartbodytext', $result) == false) {
                $result['text_emptycartbodytext'] = 'Your Shopping Cart is empty';
        }
        if (array_key_exists('text_item', $result) == false) {
                $result['text_item'] = 'item';
        }
        if (array_key_exists('text_items', $result) == false) {
                $result['text_items'] = 'items';
        }


        if (array_key_exists('button_viewcart_show', $result) == false) {
                $result['button_viewcart_show'] = 'yes';
        }
        if (array_key_exists('button_checkout_show', $result) == false) {
                $result['button_checkout_show'] = 'yes';
        }

        if (array_key_exists('pages', $result) == false) {
                $result['pages'] = [];
        }

        if (array_key_exists('pages_showurls', $result) == false) {
                $result['pages_showurls'] = '';
        }

        if (array_key_exists('pages_hideurls', $result) == false) {
                $result['pages_hideurls'] = '';
        }

        //returning
        return $result;

}


//this function will save the settings for this plugin
//the parameters is an array of values
function bw_woofc_savesettings_proversion( $params )
{

        //updating
        update_option( "bw_woofc_cartproperties", $params, true );

}


?>
