<?php
/**
 * Announcement Bar
 *
 * @package   Announcement_Bar
 * @author    Themify
 * @link      http://themify.me
 * @copyright 2013 Themify
 */

/**
 * Class Announcement_Options
 * @package Announcement_Bar
 */
 class Announcement_Options {

	function __construct(){
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );

		add_action( 'themify_metabox_enqueue_assets', array( $this, 'load_enqueue_scripts' ) );
	}

	function add_plugin_page(){
		$hook = add_menu_page( __( 'Announcement Bar', 'announcement-bar' ), __( 'Announcement Bar', 'announcement-bar' ), 'manage_options', 'announcement-bar', array( $this, 'create_admin_page'), plugins_url( 'announcement-bar/images/favicon.png' ) );
		add_action( "admin_print_styles-{$hook}", array( Themify_Metabox::get_instance(), 'enqueue' ) );
	}

	function create_admin_page(){
		if ( ! current_user_can( 'manage_options' ) ) 
			wp_die( __('You do not have sufficient permissions to access this page.', 'announcement-bar' ) );
		
		$options = Announcement_Model::get_plugin_settings();
		$option_name = Announcement_Model::$option_name;
		$default_content_teaser_length = Announcement_Model::get_default_option( 'ab_content_teaser_length' );

		if ( isset( $_GET['action'] ) ) {
			$action = 'upgrade';
			announcement_bar_updater();
		}

		announcement_bar_flush_cache();
		include( sprintf( "%s/views/settings.php", plugin_dir_path( dirname( __FILE__ ) ) ) );
	}

	function page_init(){
		$option_name = Announcement_Model::$option_name;
		register_setting( 'announcement_bar_group', $option_name );
	}

	function load_enqueue_scripts( $page ) {
		global $wp_scripts, $typenow;

		wp_enqueue_script( 'jquery-ui-sortable' );

		$ui = $wp_scripts->query('jquery-ui-core');
		$protocol = is_ssl() ? 'https': 'http';
		$url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css";
		wp_enqueue_style('jquery-ui-smoothness', $url, false, null);

		wp_enqueue_script( 'announcement-bar-admin-scripts', ANNOUNCEMENT_BAR_PLUGIN_URL . '/js/admin-scripts.js', array('jquery'), false, true );
		wp_enqueue_style( 'announcement-bar-admin-styles', ANNOUNCEMENT_BAR_PLUGIN_URL . '/css/admin.css' );
	}

}