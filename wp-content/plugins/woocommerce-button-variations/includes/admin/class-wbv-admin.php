<?php
/**
 *  Entry point for Button Variations admin interface.
 *  
 *  1. Product page tabs integration
 *  2. Settings
 *  3. Menus
 *  4. Meta Boxes
 *  5. Notices
 *
 * @author CandleStudio
 * @category Admin
 * @package ButtonVariations\Admin
 * @version 1.3.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * WBV_Admin class.
 */
class WBV_Admin
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'includes'));
        //Add row meta in the plugins page
        add_filter('plugin_row_meta', array(__CLASS__, 'wc_ea_plugin_row_meta'), 10, 2);

        //Add filter for plugin action links in plugins page.
        add_filter('plugin_action_links_' . WBV_PLUGIN_BASENAME, array(__CLASS__, 'wc_ea_plugin_action_links'), 10, 2);

        //Meta
        //product pages integration.
        add_filter('woocommerce_product_data_tabs', array(&$this, 'wc_ea_add_data_tab'), 10, 3);
    }

    public function includes()
    {
        include_once 'class-wbv-admin-assets.php';
        include_once 'class-wbv-admin-notices.php';
        include_once 'class-wbv-admin-settings.php';
        include_once 'class-wbv-admin-menus.php';
        include_once 'class-wbv-admin-meta-boxes.php';
    }

    /**
     *
     * Plugin row meta in Plugins page.
     *
     * @param type $links
     * @param type $file
     * @return type
     */
    public static function wc_ea_plugin_row_meta($links, $file)
    {
        if ($file == WBV_PLUGIN_BASENAME) {
            $row_meta = array(
                'docs' => '<a target="_blank" href="' . esc_url('http://support.candlestudio.net/woocommerce/plugins/button-variations/') . '" title="' . esc_attr(__('View Button Variations Documentation', 'wc-ea-domain')) . '">' . __('View Docs', 'wc-ea-domain') . '</a>',
            );

            return array_merge($links, $row_meta);
        }

        return (array) $links;
    }

    /**
     *
     * Add plugin action links in Plugins page.
     *
     * @param type $links
     * @return type
     */
    public static function wc_ea_plugin_action_links($links)
    {
        $ea_links = array(
            '<a href="' . admin_url('edit.php?post_type=product&page=wc_ea_plugin_options') . '">Settings</a>',
        );
        return array_merge($ea_links, $links);
    }

    /**
     *
     * Add tab to Product Page.
     *
     * @param  [array] $tabs list of available tabs.
     * @return array
     *
     */
    public function wc_ea_add_data_tab($tabs)
    {
        $tabs = array_merge($tabs, array(
            'wc_ea_buttons' => array('label' => __('Buttons', 'wc-ea-domain'),
                'target'                         => 'wc_ea_button_data',
                'class'                          => array('show_if_variable'),
            ),
        ));
        return $tabs;
    }


}

return new WBV_Admin();
