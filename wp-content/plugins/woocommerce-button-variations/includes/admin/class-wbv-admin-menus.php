<?php
/**
 * Setup menus in WP admin.
 *
 * @author   CandleStudio
 * @category Admin
 * @package  ButtonVariations\Admin
 * @version  1.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WBV_Admin_Menus Class.
 */
class WBV_Admin_Menus
{

    /**
     * Hook in tabs.
     */
    public function __construct()
    {
        add_action('admin_menu', array(&$this, 'add_admin_menus'));
    }

    /**
     *
     * Add admin menu page
     *
     * @return void
     *
     */
    public function add_admin_menus()
    {
        global $wc_ea_plugin_options;
        add_submenu_page('edit.php?post_type=product', __('Button Variations', 'wc-ea-domain'), __('Button Variations', 'wc-ea-domain'), 'manage_woocommerce', $wc_ea_plugin_options, array($this, 'admin_screen'));
    }

    /**
     *
     *  Display the Button Variations admin menu.
     *
     * @return void
     *
     */
    public function admin_screen()
    {
        WBV_Admin_Settings::output();
    }
}

return new WBV_Admin_Menus();