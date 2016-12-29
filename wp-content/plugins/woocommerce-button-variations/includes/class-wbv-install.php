<?php
/**
 * Installation related functions and actions.
 *
 * @author   CandleStudio
 * @category Admin
 * @package  ButtonVariations\Admin
 * @version  1.4.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WBV_Install Class.
 */
class WBV_Install
{

    private static $code_updates = array(
        '1.3.0' => array(
            'wbv_update_130_options',
        ),
        '1.4.0' => array(
            'wbv_update_140_disabled_for',
        ),
    );

    /**
     * Hook in tabs.
     */
    public static function init()
    {
        add_action('init', array(__CLASS__, 'check_version'), 5);
    }

    /**
     * Check WooCommerce version and run the updater is required.
     *
     * This check is done on all requests and runs if he versions do not match.
     */
    public static function check_version()
    {
        if (get_option('wbv_version') !== WBV_VERSION) {
            self::update();
        }
    }

    private static function update()
    {
        include_once 'wbv-update-functions.php';
        $current_wbv_version = get_option('wbv_version');
        foreach (self::$code_updates as $version => $update_callbacks) {
            if (version_compare($current_wbv_version, $version, '<')) {
                foreach ($update_callbacks as $update_callback) {
                    call_user_func($update_callback);
                }
            }
        }

        self::update_wbv_version();
    }

    /**
     * Update WBV version to current.
     */
    private static function update_wbv_version()
    {
        delete_option('wbv_version');
        add_option('wbv_version', WBV_VERSION);
    }
}

WBV_Install::init();
