<?php

/**
 * Handle the Product page ajax requests for Save and Reload buttons.
 *
 * @author   CandleStudio
 * @category Admin
 * @package  ButtonVariations\Admin
 * @version  1.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * WBV_Admin_Ajax Class.
 */
class WBV_Admin_Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_wc_ea_save_attributes', array(&$this, 'save_attributes'));
    }

    public function enqueue_scripts()
    {

        $screen = get_current_screen();
    }

    public function save_attributes()
    {
        check_ajax_referer('wc-ea-save-attributes', 'security');

        parse_str($_POST['data'], $data);

        if (isset($data['wc_ea_options'])) {
            $wc_ea_options = strip_slashes_recursive($data['wc_ea_options']);
            foreach ($wc_ea_options as $key => $option) {
                update_option($key, $option);
            }

            $post_id = $_POST['post_id'];

            //Attempt to make the changes.
            if (isset($wc_ea_options['wc_ea_disabled'])) {
                //Disable product.
                wbv_manage_enabling_product($post_id);

            } else {
                //Enable product.
                wbv_manage_enabling_product($post_id, 'enable');
                //If Enabled for all except, remove from product_ids
                //If Disabled for all except, add to product_ids
            }
        }

        die();
    }
}

return new WBV_Admin_Ajax();
