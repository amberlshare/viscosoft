<?php
/*
Plugin Name: BravoWP's WooCommerce Floating Cart PRO
Plugin URI: http://www.bravowp.com/woocommerce-floating-cart-professional-live-demo/
Description: A floating cart plugin for your WooCommerce. NOTE: this plugin REQUIRES the basic plugin version to be installed and activated. You can download for free at this page: https://wordpress.org/plugins/floating-cart-for-woocommerce.
Author: BravoWP.com
Version: 1.0.2
Author URI: http://www.BravoWP.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//defining constants
define( 'bw_woofcpro_globals_plugin_version', '1.0.2');
define( 'bw_woofcpro_globals_plugin_path', plugin_dir_path( dirname(__FILE__) . '/bravowp-woo-floatingcartpro.php') );
define( 'bw_woofcpro_globals_plugin_url', plugin_dir_url( dirname(__FILE__) . '/bravowp-woo-floatingcartpro.php') );

//Including files
include_once('business-logic/globals.php');
include_once('controls/resources.php');
include_once('helpers/settings.php');
include_once('helpers/rendering.php');
include_once('ajax/ajax-admin-settings.php');


//init function
function bw_woofcpro_init()
{

	//Checks if the basic version of the plugin is installed and activated
	$basicVersionActive = bw_woofc_globals_checkbasicversionisactive();
	if ( $basicVersionActive == false )
	{
		//Adding menu pages in WP dashbaord, that will warn about the missing plugin
		 add_action( 'admin_menu', 'bw_woofcpro_globals_adddashboardpage' );
	}

}
add_action( 'init', 'bw_woofcpro_init' );

?>
