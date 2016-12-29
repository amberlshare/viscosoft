<?php
/**
 * Manage tabs and includes for settings.
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
 * WBV_Admin_Settings Class.
 */
class WBV_Admin_Settings
{

    /**
     * Setting pages.
     *
     * @var array
     */
    private static $settings = array();

    /**
     * Tabs
     */
    private static $tabs = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        self::get_settings_pages();
    }

    public static function get_settings_pages()
    {
        if (empty(self::$settings)) {
            $settings = array();

            include_once 'settings/class-wbv-settings-page.php';

            $settings[] = include 'settings/class-wbv-settings-general.php';
            $settings[] = include 'settings/class-wbv-settings-attributes.php';
            $settings[] = include 'settings/class-wbv-settings-styling.php';
            $settings[] = include 'settings/class-wbv-settings-images.php';

            self::$settings = $settings;
        }

        return self::$settings;
    }

    public static function output()
    {

        global $wc_ea_general_settings, $wc_ea_att_options_prefix, $wc_ea_attributes_settings;

        $is_tab = true;

        if (!$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '') {

            if (isset($_GET['act']) && $_GET['act'] == 'tax_edit') {
                $tab    = $wc_ea_att_options_prefix . '_' . sanitize_text_field($_GET['taxonomy']);
                $is_tab = false;
            } else {
                $tab = $wc_ea_general_settings;
            }
        }

        include 'views/html-admin-settings.php';
    }

    /**
     * Add admin menu tabs
     *
     * @return void
     */
    public static function print_tabs()
    {

        global $wc_ea_general_settings, $wc_ea_plugin_options;

        $tabs = apply_filters('wbv_settings_tabs_array', array());

        $current_tab = isset($_GET['tab']) ? $_GET['tab'] : $wc_ea_general_settings;

        screen_icon();

        echo '<h2 class="nav-tab-wrapper">';

        foreach ($tabs as $tab_key => $tab_caption) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?post_type=product&page=' . $wc_ea_plugin_options . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</h2>';
    }
}

return new WBV_Admin_Settings();
