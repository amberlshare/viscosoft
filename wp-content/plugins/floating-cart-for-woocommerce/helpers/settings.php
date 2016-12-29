<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//this function will read the settings for this plugin and return an object for them
function bw_woofc_readsettings( )
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

        //putting defaults
        if (array_key_exists('show_on_pages', $result) == false) {
                $result['show_on_pages'] = 'allpages';
        }
        if (array_key_exists('general_defaultmode', $result) == false) {
                $result['general_defaultmode'] = 'extended';
        }

        //returning
        return $result;

}


//this function will save the settings for this plugin
//the parameters is an array of values
function bw_woofc_savesettings( $params )
{

        update_option( "bw_woofc_cartproperties", $params, true );

}


?>
