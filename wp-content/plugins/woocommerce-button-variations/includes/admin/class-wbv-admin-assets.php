<?php
/**
 * Load assets
 *
 * @author      CandleStudio
 * @category    Admin
 * @package     ButtonVariations/Admin
 * @version     1.4.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WBV_Admin_Assets Class.
 */
class WBV_Admin_Assets
{

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'), 15);
        add_action('admin_enqueue_scripts', array($this, 'admin_styles'), 15);
    }

    public function admin_scripts()
    {
        //enqueue scripts only for plugin pages:
        global $wc_ea_plugin_screens, $wp_version;
        $suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG == 'true') ? '' : '.min';
        $screen = get_current_screen();

        //All BV pages.
        if (in_array($screen->id, $wc_ea_plugin_screens) || ($screen->base == 'edit-tags' && $screen->post_type == 'product') || ($screen->base == 'term' && $screen->post_type == 'product')) {

            //Product pages
            //Include Ajax to save Button tab settings.
            if ($screen->id == 'product') {

                global $post;

                wp_register_script(
                    'wc-ea-ajax',
                    WBV_PLUGIN_DIR . 'js/wc-ea-ajax' . $suffix . '.js',
                    array('jquery'),
                    WBV_VERSION
                );

                wp_enqueue_script('wc-ea-ajax');

                $params = array(
                    'post_id'                     => isset($post->ID) ? $post->ID : '',
                    'plugin_url'                  => WC()->plugin_url(),
                    'ajax_url'                    => admin_url('admin-ajax.php'),
                    'wc_ea_save_attributes_nonce' => wp_create_nonce("wc-ea-save-attributes"),
                );

                wp_localize_script('wc-ea-ajax', 'wc_ea_admin_meta_boxes', $params);
            }

            //Enqueue WP Media only for Attribute pages.
            if ($screen->id == 'product' || ($screen->base == 'edit-tags' && $screen->post_type == 'product') || ($screen->base == 'term' && $screen->post_type == 'product')) {

                wp_enqueue_script('wbv_image_upload', WBV_PLUGIN_DIR . 'js/wbv-upload-image' . $suffix . '.js', WBV_VERSION);

                $params = array(
                    'i18n_choose_image'               => esc_js(__('Choose an image', 'wc-ea-domain')),
                    'i18n_set_image'                  => esc_js(__('Set term image', 'wc-ea-domain')),
                    'woocommerce_placeholder_img_src' => wc_placeholder_img_src(),
                );

                wp_localize_script('wbv_image_upload', 'wc_ea_admin_settings', $params);
                wp_enqueue_media();
            }

            //Add Select2 and Enhanced Select2 only for Settings page.
            if (in_array($screen->id, $wc_ea_plugin_screens)) {

                wp_enqueue_script('select2');
                wp_enqueue_script('wc-enhanced-select');
            }

            //Color picker functionality for all WBV pages.

            //If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
            if (3.5 <= $wp_version) {
                //Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker');
            }
            //If the WordPress version is less than 3.5 load the older farbtasic color picker.
            else {
                //As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
                wp_enqueue_style('farbtastic');
                wp_enqueue_script('farbtastic');
            }

            wp_enqueue_script('wbv_settings', WBV_PLUGIN_DIR . 'js/wc-ea-settings' . $suffix . '.js', WBV_VERSION);
        }
    }

    public function admin_styles()
    {
        global $wc_ea_plugin_screens;
        $screen = get_current_screen();
        if (in_array($screen->id, $wc_ea_plugin_screens)) {
            wp_enqueue_style('woocommerce_admin_styles');
        }

        //Admin CSS
        wp_register_style('wc-ea-admin-css', WBV_PLUGIN_DIR . 'css/wc-ea-admin-css.css');
        wp_enqueue_style('wc-ea-admin-css');
    }
}

return new WBV_Admin_Assets();
