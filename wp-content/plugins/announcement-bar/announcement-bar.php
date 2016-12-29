<?php
/*
Plugin Name: Announcement Bar
Plugin URI:  http://themify.me/
Description: Display announcements on your WordPress site. Requires WordPress 4.1 or higher.
Version:     1.2.2 
Author:      Themify
Author URI:  http://themify.me
Text Domain: announcement-bar
Domain Path: /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define Constant

$abar_plugin_data = get_file_data( __FILE__, array( 'Version' ) );
define( 'ANNOUNCEMENT_BAR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ANNOUNCEMENT_BAR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ANNOUNCEMENT_BAR_PLUGIN_FILE', __FILE__ );
define( 'ANNOUNCEMENT_BAR_CURRENT_VERSION', $abar_plugin_data[0] );

// Functions
include_once plugin_dir_path( __FILE__ ) . 'announcement-bar-functions.php';

// Include main class
if ( ! class_exists( 'Announcement_Model' ) ) {
	include_once plugin_dir_path( __FILE__ ) . 'classes/class-announcement-model.php';
}
if ( ! class_exists( 'Announcement_Bar' ) ) {
	include_once plugin_dir_path( __FILE__ ) . 'classes/class-announcement-bar.php';
}
if ( ! class_exists( 'Announcement_Post_Type' ) ) {
	include_once plugin_dir_path( __FILE__ ) . 'classes/class-announcement-post-type.php';
}
if ( ! class_exists( 'Announcement_Options' ) ) {
	include_once plugin_dir_path( __FILE__ ) . 'classes/class-announcement-options.php';
}
if ( ! class_exists( 'Announcement_Bar_Widget' ) ) {
	include_once plugin_dir_path( __FILE__ ) . 'classes/class-announcement-bar-widgets.php';
}

/**
 * Load themify functions
 */
function announcement_bar_themify_dependencies(){
	include ANNOUNCEMENT_BAR_PLUGIN_PATH . '/dependencies/themify-metabox/themify-metabox.php';
	add_action( 'init', 'announcement_bar_load_google_fonts_dep', 100 );
}
add_action( 'after_setup_theme', 'announcement_bar_themify_dependencies' );

function announcement_bar_load_google_fonts_dep() {
	include ANNOUNCEMENT_BAR_PLUGIN_PATH . '/google-fonts/functions.php';
}

// Initialize class
if ( class_exists( 'Announcement_Bar' ) ) {
	register_activation_hook( __FILE__, array( 'Announcement_Bar', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'Announcement_Bar', 'deactivate' ) );

	$Announcement_Bar = new Announcement_Bar();
}

if ( class_exists( 'Announcement_Post_Type' ) ) {
	register_activation_hook( __FILE__, array( 'Announcement_Post_Type', 'flush_post_type' ) );
	$Announcement_Post_Type = new Announcement_Post_Type();
}

if ( class_exists( 'Announcement_Options' ) && is_admin() ) {
	$Announcement_Options = new Announcement_Options();
}

if ( class_exists( 'Announcement_Bar_Widget' ) ) {
	// register Announcement_Bar_Widget
	function announcement_bar_register_widget() {
		register_widget( 'Announcement_Bar_Widget' );
	}
	add_action( 'widgets_init', 'announcement_bar_register_widget' );
}

// Updater
function announcement_bar_load_updater() {
	if ( is_admin() && current_user_can( 'update_plugins' ) ) {
		include_once plugin_dir_path( __FILE__ ) . 'announcement-bar-updater.php';
	}
}
add_action('init', 'announcement_bar_load_updater');