<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//Adding menu page in Wordpress Dashboard, on WP hook (main .php file)
function bw_woofcpro_globals_adddashboardpage() {

	$capabilityName = 'manage_options'; //default

	$my_page = add_menu_page( 'BravoWP Cart', 'BravoWP Cart', $capabilityName, 'bw_woofc', 'bw_woofcpro_globals_adddashboardpage_callback', bw_woofcpro_globals_plugin_url . '/images/pluginicon.png', 74 );
	add_action( 'load-' . $my_page, 'bw_woofcpro_globals_adddashboardpage_actionevent' );

}
function bw_woofcpro_globals_adddashboardpage_callback() {

	include( bw_woofcpro_globals_plugin_path . "/pages/admin.php" );

}
function bw_woofcpro_globals_adddashboardpage_actionevent()
{

	//do not remove

}


//Checks if the basic plugin is installed and activated
function bw_woofc_globals_checkbasicversionisactive()
{

	try
	{

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( is_plugin_active( "bravowp-woo-floatingcart/bravowp-woo-floatingcart.php" ) )
		{

			return true;

		}

		if ( is_plugin_active( "floating-cart-for-woocommerce/bravowp-woo-floatingcart.php" ) )
		{

			return true;

		}

		return false;

	}

	catch (Exception $e)
	{

	}

}



?>
