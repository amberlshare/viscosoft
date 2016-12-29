<?php

/*
Plugin Name: WooCommerce Button Variations
Plugin URI: http://candlestudio.net/woocommerce/plugins/button-variations/
Description: Add color buttons and custom text to variations (attributes) of Variable Products in WooCommerce.
Version: 1.4.1
Author: Candle Studio
Author URI: http://candlestudio.net
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
 * Check if Woocommerce exists and is active
 *
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    /*
     *
     * CONSTANTS
     *
     */
    define('WBV_VERSION', '1.4.1');
    define('WBV_PLUGIN_BASENAME', plugin_basename(__FILE__));
    define('WBV_PLUGIN_DIR', plugin_dir_url(__FILE__));

    /*
     *
     * Globals
     *
     */
    $wc_ea_att_options_prefix  = 'wc_ea_att_opt';
    $wc_ea_general_settings    = 'wc_ea_general_settings';
    $wc_ea_styling_settings    = 'wc_ea_styling_settings';
    $wc_ea_attributes_settings = 'wc_ea_attributes_settings';
    $wc_ea_image_settings      = 'wc_ea_image_settings';
    $wc_ea_term_meta           = 'wc_ea_term_meta_';
    $wc_ea_plugin_options      = 'wc_ea_plugin_options';
    $wc_ea_plugin_screens      = array('product_page_wc_ea_plugin_options', 'product');
    $wbv_plugin_settings       = 'wbv_plugin_settings';
    
    include_once 'includes/class-wbv-install.php';
    include_once 'includes/wbv-functions.php';

    /*
     *
     * Initialize term management and admin interface for
     * Button Variations only if logged in as admin.
     *
     */
    if (is_admin()) {
        include_once 'includes/admin/class-wbv-admin-taxonomies.php';
        include_once 'includes/admin/class-wbv-admin-ajax.php';
        include_once 'includes/admin/class-wbv-admin.php';
    }

    /** Uncomment for debugging js & css */
    //define('SCRIPT_DEBUG', true);

    $enabled = get_option($wc_ea_general_settings);

    /**
     * Verify if plugin is enabled.
     */
    if (isset($enabled['enable_plugin']) && $enabled['enable_plugin']) {

        add_action('wp_enqueue_scripts', 'wbv_register_frontend_scripts_styles');
        add_action('woocommerce_before_single_product', 'wbv_init');
        add_filter('woocommerce_dropdown_variation_attribute_options_html', 'wbv_button_variations_attribute_options_html', 10, 2);
    }
}
