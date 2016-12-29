<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//This function is called from the rendering of the front end page
//Call for front end only
function bw_woofc_includeresources_frontendpages() {

	require_once(ABSPATH .'wp-includes/pluggable.php');

	//css
	$reset_css_ver  = date("ymd-Gis", filemtime( bw_woofc_globals_plugin_path . 'css/reset.css' ));
	wp_enqueue_style( 'bw-woofc-resetcss', bw_woofc_globals_plugin_url . '/css/reset.css', array(), $reset_css_ver);
	$floatingcart_css_ver  = date("ymd-Gis", filemtime( bw_woofc_globals_plugin_path . 'css/floatingcart.css' ));
	wp_enqueue_style( 'bw-woofc-floatingcartcss', bw_woofc_globals_plugin_url . '/css/floatingcart.css', array(), $floatingcart_css_ver);

	//scripts vendors
	wp_enqueue_script( 'jquery' );

	//plugin scripts
	$main_js_ver  = date("ymd-Gis", filemtime( bw_woofc_globals_plugin_path . 'scripts/bravowp-woo-floatingcart.js' ));
	wp_register_script( 'bw-woofc-frontend-js', bw_woofc_globals_plugin_url . '/scripts/bravowp-woo-floatingcart.js', array(), $main_js_ver, false);
	wp_localize_script( 'bw-woofc-frontend-js', 'bwwoofcvars', bw_woofc_buildarrayconstantsforscripts());
	wp_enqueue_script( 'bw-woofc-frontend-js', bw_woofc_globals_plugin_url . '/scripts/bravowp-woo-floatingcart.js', array(), $main_js_ver, false );

}


//This function is called from the admin pages
function bw_woofc_includeresources_adminpages() {

	require_once(ABSPATH .'wp-includes/pluggable.php');

	//vendors
	$bootstrap_css_ver  = date("ymd-Gis", filemtime( bw_woofc_globals_plugin_path . 'css/vendors/bootstrap.min.css' ));
	wp_enqueue_style( 'bw-woofc-bootstrap', bw_woofc_globals_plugin_url . '/css/vendors/bootstrap.min.css', array(), $bootstrap_css_ver);
	$fontawesome_css_ver  = date("ymd-Gis", filemtime( bw_woofc_globals_plugin_path . 'css/vendors/font-awesome.min.css' ));
	wp_enqueue_style( 'bw-woofc-fontawesome', bw_woofc_globals_plugin_url . '/css/vendors/font-awesome.min.css', array(), $fontawesome_css_ver);

	//css
	$admin_css_ver  = date("ymd-Gis", filemtime( bw_woofc_globals_plugin_path . 'css/admin.css' ));
	wp_enqueue_style( 'bw-woofc-admin', bw_woofc_globals_plugin_url . '/css/admin.css', array(), $admin_css_ver);

	//scripts vendors
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'bootstrap-js', bw_woofc_globals_plugin_url . '/scripts/vendors/bootstrap.min.js');

	if ( bw_woofc_globals_isproversion() )
	{
		bw_woofc_includeresources_adminpages_scripts_pro();
	}


	//plugin scripts
	$main_js_ver  = date("ymd-Gis", filemtime( bw_woofc_globals_plugin_path . 'scripts/bravowp-woo-floatingcart.js' ));
	wp_register_script( 'bw-woofc-frontend-js', bw_woofc_globals_plugin_url . '/scripts/bravowp-woo-floatingcart.js', array(), $main_js_ver, false);
	wp_localize_script( 'bw-woofc-frontend-js', 'bwwoofcvars', bw_woofc_buildarrayconstantsforscripts());
	wp_enqueue_script( 'bw-woofc-frontend-js', bw_woofc_globals_plugin_url . '/scripts/bravowp-woo-floatingcart.js', array(), $main_js_ver, false );

	if ( bw_woofc_globals_isproversion() == false)
	{
		$admin_js_ver  = date("ymd-Gis", filemtime( bw_woofc_globals_plugin_path . 'scripts/admin.js' ));
		wp_register_script( 'bw-woofc-admin-js', bw_woofc_globals_plugin_url . '/scripts/admin.js', array(), $admin_js_ver, false);
		wp_localize_script( 'bw-woofc-admin-js', 'bwwoofcvars', bw_woofc_buildarrayconstantsforscripts());
		wp_enqueue_script( 'bw-woofc-admin-js', bw_woofc_globals_plugin_url . '/scripts/admin.js', array(), $admin_js_ver, false );
	}
	else
	{

		bw_woofc_includeresources_adminpages_css_pro();

	}

}



//returns the array that will be passed to javascripts files (constants)
function bw_woofc_buildarrayconstantsforscripts()
{

	$ajax_nonce = wp_create_nonce( "bw-woofc" );

	return array( 'ajaxNonce' => $ajax_nonce, 'ajaxHandlerUrl' => admin_url( 'admin-ajax.php' ) );

}


?>
