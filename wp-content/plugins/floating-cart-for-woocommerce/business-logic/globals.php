<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//Adding menu page in Wordpress Dashboard, on WP hook (main .php file)
function bw_woofc_globals_adddashboardpage() {

	$capabilityName = 'manage_options'; //default

	if (bw_woofc_demo_is_active() == 1)
	{
		if (bw_woofc_demo_forceadmincapabilities() == 0)
		{
			$capabilityName = 'bravowp_woofloatingcart_demo';
		}
	}

	$my_page = add_menu_page( 'BravoWP Cart', 'BravoWP Cart', $capabilityName, 'bw_woofc', 'bw_woofc_globals_adddashboardpage_callback', bw_woofc_globals_plugin_url . '/images/pluginicon.png', 74 );
	add_action( 'load-' . $my_page, 'bw_woofc_includeresources_adminpages' );

}
function bw_woofc_globals_adddashboardpage_callback() {

	include( bw_woofc_globals_plugin_path . "/pages/admin.php" );

}


//Checks if the PRO plugin is installed and activated
function bw_woofc_globals_isproversion()
{

	bw_woofc_systemlog_addentry("FUNCTION","bw_woofc_globals_isproversion","Start");

	try
	{

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( is_plugin_active( "bravowp-woo-floatingcartpro/bravowp-woo-floatingcartpro.php" ) )
		{

			return true;

		}

		return false;

	}

	catch (Exception $e)
	{
		bw_woofc_systemlog_addentry("ERROR", "bw_woofc_globals_isproversion", $e->getMessage());
	}

	bw_woofc_systemlog_addentry("FUNCTION","bw_woofc_globals_isproversion","End");

}



?>
