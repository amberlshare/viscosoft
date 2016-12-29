<?php
/**
 * Woocommerce Button Variations update functions
 *
 * Update functions.
 *
 * @author   CandleStudio
 * @category Updates
 * @package  ButtonVariations\Functions
 * @version  1.4.0
 */
function wbv_update_130_options()
{
    global $wc_ea_general_settings, $wc_ea_styling_settings, $wc_ea_image_settings;

    if (get_option($wc_ea_general_settings)) {
        /*
        General settings options.
         */
        $general_defaults = array(
            'enable_option' => '0',
        );

        $general_settings_current = wp_parse_args((array) get_option('wc_ea_general_settings'), $general_defaults);
        //Convert enable_option to enable_plugin
        $general_settings = array('enable_plugin' => $general_settings_current['enable_option']);
        update_option($wc_ea_general_settings, $general_settings);

        /*
        Images settings options
         */
        $image_settings = get_option($wc_ea_image_settings);

        if (is_array($image_settings)) {
            if (!isset($image_settings['image_button_selected_checkmark'])) {
                $image_settings['image_button_selected_checkmark'] = '0';
            }

            if (!isset($image_settings['image_button_selected_opacity'])) {
                $image_settings['image_button_selected_opacity'] = '0';
            }
        }

        //Convert image_button_size to image__size
        $image_settings['image__size'] = $image_settings['image_button_size'];
        unset($image_settings['image_button_size']);
        //Convert image_button_size_preset to image__size_preset
        $image_settings['image__size_preset'] = $image_settings['image_button_size_preset'];
        unset($image_settings['image_button_size_preset']);
        //Unset image_button_selected_border_width
        unset($image_settings['image_button_selected_border_width']);
        //Unset image_button_selected_border_color to image__selected_border_color
        unset($image_settings['image_button_selected_border_color']);
        //Convert image_button_selected_overlay to image__selected_overlay
        $image_settings['image__selected_overlay'] = $image_settings['image_button_selected_overlay'];
        unset($image_settings['image_button_selected_overlay']);
        //Unset image_button_selected_checkmark
        unset($image_settings['image_button_selected_checkmark']);
        //Unset image_button_selected_opacity
        unset($image_settings['image_button_selected_opacity']);
        //Convert image_button_size_other_width to image__size_other_width
        $image_settings['image__size_other_width'] = $image_settings['image_button_size_other_width'];
        unset($image_settings['image_button_size_other_width']);
        //Convert image_button_size_other_height to image__size_other_height
        $image_settings['image__size_other_height'] = $image_settings['image_button_size_other_height'];
        unset($image_settings['image_button_size_other_height']);
        //Unset image_button_selected_border_width_other
        unset($image_settings['image_button_selected_border_width_other']);

        update_option($wc_ea_image_settings, $image_settings);

        /*
        Styling settings
         */

        $styling_settings = get_option('wc_ea_styling_settings');

        if (is_array($styling_settings)) {
            if (!isset($styling_settings['enable_option'])) {
                $styling_settings['enable_option'] = '0';
            }

            if (!isset($styling_settings['enable_opacity'])) {
                $styling_settings['enable_opacity'] = '0';
            }

        }

        //Convert enable_option to enable_styling
        $styling_settings['enable_styling'] = $styling_settings['enable_option'];
        unset($styling_settings['enable_option']);
        //Convert button_color to text__color
        $styling_settings['text__color'] = $styling_settings['button_color'];
        unset($styling_settings['button_color']);
        //Convert button_color_border to text__border_color
        $styling_settings['text__border_color'] = $styling_settings['button_color_border'];
        unset($styling_settings['button_color_border']);
        //Convert text_color to text__text_color
        $styling_settings['text__text_color'] = $styling_settings['text_color'];
        unset($styling_settings['text_color']);
        //Convert button_color_hover to text__hover_color
        $styling_settings['text__hover_color'] = $styling_settings['button_color_hover'];
        unset($styling_settings['button_color_hover']);
        //Convert button_color_border_hover to text__hover_border_color
        $styling_settings['text__hover_border_color'] = $styling_settings['button_color_border_hover'];
        unset($styling_settings['button_color_border_hover']);
        //Convert text_color_hover to text__hover_text_color
        $styling_settings['text__hover_text_color'] = $styling_settings['text_color_hover'];
        unset($styling_settings['text_color_hover']);
        //Convert button_color_selected to text__selected_color
        $styling_settings['text__selected_color'] = $styling_settings['button_color_selected'];
        unset($styling_settings['button_color_selected']);
        //Convert button_color_border_selected to text__selected_border_color
        $styling_settings['text__selected_border_color'] = $styling_settings['button_color_border_selected'];
        unset($styling_settings['button_color_border_selected']);
        //Convert text_color_selected to text__selected_text_color
        $styling_settings['text__selected_text_color'] = $styling_settings['text_color_selected'];
        unset($styling_settings['text_color_selected']);
        //Convert button_size_other_width to button__size_other_width
        $styling_settings['button__size_other_width'] = $styling_settings['button_size_other_width'];
        unset($styling_settings['button_size_other_width']);
        //Convert button_size_other_height to button__size_other_height
        $styling_settings['button__size_other_height'] = $styling_settings['button_size_other_height'];
        unset($styling_settings['button_size_other_height']);
        //Convert button_radius_other to button__radius_other
        $styling_settings['button__radius_other'] = $styling_settings['button_radius_other'];
        unset($styling_settings['button_radius_other']);
        //Convert button_border_other to button__border_other
        $styling_settings['button__border_other'] = $styling_settings['button_border_other'];
        unset($styling_settings['button_border_other']);
        //Convert button_size to button__size
        $styling_settings['button__size'] = $styling_settings['button_size'];
        unset($styling_settings['button_size']);
        //Convert button_radius to button__radius
        $styling_settings['button__radius'] = $styling_settings['button_radius'];
        unset($styling_settings['button_radius']);
        //Convert button_border to button__border
        $styling_settings['button__border'] = $styling_settings['button_border'];
        unset($styling_settings['button_border']);

        $styling_settings['show_checkmark'] = '1';

        update_option($wc_ea_styling_settings, $styling_settings);
    }

}

function wbv_update_140_disabled_for()
{
    global $wpdb, $wc_ea_general_settings;
    $general_settings = get_option($wc_ea_general_settings);
    $general_settings['switcher'] = '0';
    //SELECT * FROM wp_options o WHERE o.option_name LIKE 'wc_ea_disabled_for%'
    //loop through option_name, removing "wc_ea_disabled_for"
    //adding it to an array, array[] = value
    //Deleting all with wc_ea_disabled_for
    $disabled_products = $wpdb->get_results("
        SELECT * FROM {$wpdb->options}
        WHERE option_name LIKE 'wc_ea_disabled_for_%' AND option_value = 1
    ");

    foreach ( $disabled_products as $disabled_product ) {
        $id = str_replace('wc_ea_disabled_for_', '', $disabled_product->option_name);
        wbv_handle_id($id);
        delete_option( 'wc_ea_disabled_for_' . $id);
    }
}
