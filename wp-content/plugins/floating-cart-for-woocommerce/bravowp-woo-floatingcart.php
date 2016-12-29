<?php

/**
* @package BravoWP-WooCommerce-FloatingCart
* @version 1.0.2
*/
/*
Plugin Name: BravoWP's WooCommerce Floating Cart
Plugin URI: http://wordpress.org/plugins/BravoWP-WooCommerce-FloatingCart
Description: A floating cart plugin for your WooCommerce
Author: BravoWP.com
Version: 1.0.2
Author URI: http://www.BravoWP.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//defining constants
define( 'bw_woofc_globals_plugin_version', '1.0.2');
define( 'bw_woofc_globals_plugin_path', plugin_dir_path( dirname(__FILE__) . '/bravowp-woo-floatingcart.php') );
define( 'bw_woofc_globals_plugin_url', plugin_dir_url( dirname(__FILE__) . '/bravowp-woo-floatingcart.php') );

//Globals
include_once('ajax/ajax-admin-settings.php');
include_once('business-logic/globals.php');
include_once('business-logic/resources.php');
include_once('business-logic/demo.php');
include_once('utils/log/logger.php');
include_once('helpers/woocommerce_cart.php');
include_once('helpers/settings.php');
include_once('helpers/rendering.php');

//init function
function bw_woofc_init()
{

        //Including front end client files, like css and scripts
         add_action( 'wp_enqueue_scripts', 'bw_woofc_includeresources_frontendpages' );

        //Adding menu pages in WP dashbaord
         add_action( 'admin_menu', 'bw_woofc_globals_adddashboardpage' );

}
add_action( 'init', 'bw_woofc_init' );


?>
