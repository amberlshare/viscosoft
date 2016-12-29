<?php
/**
  *
 * Adds Buttons functionality for Product page integration with WooCommerce
 *
 * @author      CandleStudio
 * @category    Admin
 * @package     ButtonVariations\Admin
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * WC_Admin_Meta_Boxes.
 */
class WBV_Admin_Meta_Boxes {

    public function __construct() {

        //reference this deprecated action only if exists in subsequent releases of WC.
        //otherwise reference the new one.
        if (has_action('woocommerce_product_write_panels')) {

            add_action('woocommerce_product_write_panels', array($this, 'wc_ea_product_write_panels'));
        } else {
            add_action('woocommerce_product_data_panels', array($this, 'wc_ea_product_write_panels'));
        }

        add_action('woocommerce_process_product_meta_variable', array($this, 'wc_ea_process_product_meta_variable'), 10, 1);

    }

     /**
     *
     * Hook function to save options at Product pages.
     * @param  [type] $post_id [description]
     * @param  [type] $post    [description]
     * @return void
     *
     */
    public function wc_ea_process_product_meta_variable($post_id)
    {
        $wc_ea_options = $_POST['wc_ea_options'];

        if (isset($wc_ea_options) && is_array($wc_ea_options)) {

            foreach ($wc_ea_options as $key => $option) {
                update_option($key, $option);
            }

            if (isset($wc_ea_options['wc_ea_disabled'])) {
                //Disabled.
                wbv_manage_enabling_product($post_id);
               
            } else {
                //Enabled
                wbv_manage_enabling_product($post_id, 'enable');
                //If Enabled for all except, remove from product_ids
                //If Disabled for all except, add to product_ids
            }
        }
    }

    /**
     *
     *
     * Add "Buttons" tab to Product page tabs.
     *
     * @return void
     *
     */
    public function wc_ea_product_add_panel_tab()
    {
        ?>
        <li class="custom_tab show_if_variable">
            <a href="#custom_tab_data_ctabs">
                <?php _e('Buttons', 'wc-ea-domain');?>
            </a>
        </li>
        <?php
}

    /**
     *
     * Add panels to the Product page admin.
     *
     * @return void
     *
     */
    public function wc_ea_product_write_panels()
    {

        $screen = get_current_screen();
        if ($screen->action == 'add') {
            ?>
            <div id="wc_ea_button_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
                <div id="wc_ea_button_data_inner">
                    <br />
                    <p><?php _e('To setup buttons, save your product as Variable first', 'wc-ea-domain');?> </p>
                </div>
            </div>
<?php
            return;
        }

        global $post, $wc_ea_att_options_prefix;

        $thepostid = $post->ID;

        $attributes = maybe_unserialize(get_post_meta($post->ID, '_product_attributes', true));

        // See if any are set
        $variation_attribute_found = false;

        if ($attributes) {

            foreach ($attributes as $attribute) {

                if (isset($attribute['is_variation'])) {
                    $variation_attribute_found = true;
                    break;
                }
            }
        }
        $enabled_for_product = wbv_is_product_enabled($thepostid);
        $is_category_managed = wbv_is_category_managed($thepostid);

        ?>
        <div id="wc_ea_button_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
            <div id="wc_ea_button_data_inner">
                <p class="toolbar">
                    <input type='checkbox' <?php echo ($is_category_managed) ? 'disabled="disabled"' : '' ?> class="wbv_disable_checkbox" name="wc_ea_options[wc_ea_disabled]" <?php checked($enabled_for_product, false);?> value='1' />
                    <span>

                        <?php 
                            if ($is_category_managed){
                                _e("Disable Buttons for this product. <i>(Locked. Product in enabled/disabled category. Change <a href='" . admin_url('edit.php?post_type=product&page=wc_ea_plugin_options') . "'>here</a>)</i>", 'wc-ea-domain');
                            } else {
                                _e('Disable Buttons for this product.', 'wc-ea-domain');
                            }
                            ?>
                        <img class="help_tip" data-tip='<?php _e('Disable Buttons for this product only, rest of products will not be affected.', 'wc-ea-domain');?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
                    </span>
                    <span style="float: right;">
                    <a href="#" class="close_all"><?php _e('Close all', 'wc-ea-domain');?></a>
                    <a href="#" class="expand_all"><?php _e('Expand all', 'wc-ea-domain');?></a>
                    </span>
                </p>

                <div class="wc-metaboxes">
<?php
        $attribute_taxonomies = wc_get_attribute_taxonomies();

        // Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
        $attributes = maybe_unserialize(get_post_meta($thepostid, '_product_attributes', true));

        // Taxonomies
        if ($attribute_taxonomies && !empty($attributes)) {

            echo '<h2 style="margin-bottom: 0; padding-bottom: 0; background-color: #FAFAFA;">' . __('Global attributes', 'wc-ea-domain') . '</h2>';
            echo '<p style="background-color: #FAFAFA; margin-top: 0; padding-top: 0; margin-bottom: 0; border-bottom: 1px solid #eee;"><i>Found in Products > Attributes</i></p>';
            $i = 0;
            foreach ($attribute_taxonomies as $tax) {

                // Get name of taxonomy we're now outputting (pa_xxx)
                $attribute_taxonomy_name = wc_attribute_taxonomy_name($tax->attribute_name);
                $wc_ea_tax_key           = $wc_ea_att_options_prefix . '_' . $attribute_taxonomy_name;
                $wc_ea_tax_value         = get_option($wc_ea_tax_key);

                // Ensure it exists
                if (!taxonomy_exists($attribute_taxonomy_name)) {
                    continue;
                }

                // Get product data values for current taxonomy - this contains ordering and visibility data
                if (isset($attributes[sanitize_title($attribute_taxonomy_name)])) {
                    $attribute = $attributes[sanitize_title($attribute_taxonomy_name)];
                }

                // Get terms of this taxonomy associated with current product
                $post_terms = wp_get_post_terms($thepostid, $attribute_taxonomy_name);

                // Any set?
                $has_terms = (is_wp_error($post_terms) || !$post_terms || sizeof($post_terms) == 0) ? 0 : 1;

                if ($has_terms) {
                    $i++;
                }

                //loop through the post_terms searching for the options associated with the term.
                ?>
                            <div class="woocommerce_attribute wc-metabox closed taxonomy <?php echo $attribute_taxonomy_name; ?>" <?php if (!$has_terms) {
                    echo 'style="display:none"';
                }
                ?>>
                                <h3>
                                    <div class="handlediv" title="<?php _e('Click to toggle', 'woocommerce');?>"></div>
                                    <strong class="attribute_name"><?php echo apply_filters('woocommerce_attribute_label', $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name, $tax->attribute_name); ?></strong>
                                </h3>
                                <table cellpadding="0" cellspacing="0" class="woocommerce_attribute_data wc_ea_attributes_table wc-metabox-content">
                                    <tbody>
                                        <tr class="wc-ea-term__item">
                                            <td class="wc-ea-term__title"><?php _e('Type:', 'wc-ea-domain');?></td>
                                            <td class="wc-ea-term__type">
                                                <select style="width:300px;" name="wc_ea_options[<?php echo $wc_ea_tax_key; ?>]" id="wc_ea_attribute">
                                                    <option value="text" <?php selected($wc_ea_tax_value, 'text')?>>Text</option>
                                                    <option value="color" <?php selected($wc_ea_tax_value, 'color')?>>Color</option>
                                                    <option value="image" <?php selected($wc_ea_tax_value, 'image')?>>Image</option>
                                                </select>
                                            </td>
                                        </tr>
<?php
                                        foreach ($post_terms as $term) {
                                            $this->process_terms($term->term_id, $term->name);
                                        }
?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
}

            if ($i == 0) {
                echo '<p>' . __('No variations using global attributes found.', 'wc-ea-domain') . '</p>';
            }

        } else {
            echo '<p><i>' . __('No attributes found, select them in the Attributes tab.', 'wc-ea-domain') . '</i></p>';
        }

        //Retrieve the product object and verify that has the required methods.
        global $post;
        $product = wc_get_product($post->ID);

        // Custom Attributes
        if (!empty($attributes) && method_exists($product, 'get_variation_attributes')) {
            $i = 0;
            echo '<h2 style="background-color: #FAFAFA; margin-bottom: 0; padding-bottom: 0;">' . __('Custom Attributes', 'wc-ea-domain') . '</h2>';
            echo '<p style="background-color: #FAFAFA;margin-top: 0; padding-top: 0; margin-bottom: 0; border-bottom: 1px solid #eee;"><i>' . __('For this product', 'wc-ea-domain') . '</i></p>';

            $mVariations = $product->get_variation_attributes();

            if ($mVariations) {
                foreach ($attributes as $attribute) {

                    if ($attribute['is_taxonomy']) {
                        continue;
                    }

                    if (isset($mVariations[$attribute['name']])){
                        $customAttributes = $mVariations[$attribute['name']];
                    } else {
                        continue;
                    }

                    $wc_ea_tax_key   = $wc_ea_att_options_prefix . '_' . $product->id . '_' . $attribute['name'];
                    $wc_ea_tax_value = get_option($wc_ea_tax_key);
                    ?>
                                <div class="woocommerce_attribute wc-metabox closed">
                                    <h3>
                                        <div class="handlediv" title="<?php _e('Click to toggle', 'wc-ea-domain');?>"></div>
                                        <strong class="attribute_name"><?php echo apply_filters('woocommerce_attribute_label', esc_html($attribute['name']), esc_html($attribute['name'])); ?></strong>
                                    </h3>
                                    <table cellpadding="0" cellspacing="0" class="woocommerce_attribute_data wc-metabox-content">
                                        <tbody>
                                            <tr>
                                                <td><?php _e('Type:', 'wc-ea-domain');?></td>
                                                <td>
                                                    <select style="width:300px;" name="wc_ea_options[<?php echo $wc_ea_tax_key; ?>]" id="wc_ea_attribute">
                                                        <option value="text" <?php selected($wc_ea_tax_value, 'text')?>>Text</option>
                                                        <option value="color" <?php selected($wc_ea_tax_value, 'color')?>>Color</option>
                                                        <option value="image" <?php selected($wc_ea_tax_value, 'image')?>>Image</option>
                                                    </select>
                                                </td>
                                            </tr>

<?php
                                        foreach ($customAttributes as $name) {
                                            //create a custom term id based on the product id and name.
                                            $this->process_terms($product->id . '_' . $name, $name);
                                            $i++;
                                        }
?>

                                        </tbody>
                                    </table>
                                </div>
                                <?php
}

                if ($i == 0) {
                    echo '<p>' . __('No variations using custom attributes found.', 'wc-ea-domain') . '</p>';
                }

            } else {
                echo '<p>' . __('Custom attributes could not be displayed, product has saved variations? or attributes are used in variations?', 'wc-ea-domain') . '</p>';
            }
        } else if (!empty($attributes) && !method_exists($product, 'get_variation_attributes')) {
            echo '<p>' . __('Custom attributes could not be displayed as this product has not been saved as Variable Product.', 'wc-ea-domain') . '</p>';
        }
        ?>

                </div>
                <p class="toolbar">
                    <button type="button" class="button button-primary save_wc_ea_attributes"><?php _e('Save Attributes', 'wc-ea-domain');?></button>
                    <button type="button" class="button save_wc_ea_attributes_reload"><?php _e('Reload', 'wc-ea-domain');?></button>
                </p>
            </div>
        </div>
        <?php
}

    /**
     *
     * Display term controls in Product page.
     *
     * @param  [string] $id  product id.
     * @param  [string] $name term name.
     *
     */
    public function process_terms($id, $name)
    {
        echo '<tr>';
        echo '<td class="wc-ea-term__title"><b>' . $name . '</b></td>';
        // echo '<td></td>';
        echo '</tr>';

        $term_options  = get_option('wc_ea_term_meta_' . $id);
        $wc_ea_options = array();

        if ($term_options && is_array($term_options)) {
            $wc_ea_options = array_merge(array('wc_ea_image' => '', 'wc_ea_color' => '', 'wc_ea_text' => ''), $term_options);
        } else {
            $wc_ea_options = array('wc_ea_image' => '', 'wc_ea_color' => '', 'wc_ea_text' => '');
        }

        //ksort( $wc_ea_options );

        echo '<tr class="wc_ea_term_controls wc-ea-term__item">';
        foreach ($wc_ea_options as $key => $value) {
            echo '<td>';
            $this->display_controls_product_page($key, $value, $id);
            echo '</td>';
        }
        echo '</tr>';
    }

    /**
     *
     * Display controls on the Product Page
     *
     * @param  [string] $key   array key for the options array for a term.
     * @param  [string] $value array value for the options array for a term.
     * @param  [string] $id    id for the term.
     * @return void
     *
     *
     */
    public function display_controls_product_page($key, $value, $id)
    {
        if ($key == 'wc_ea_color') {
            ?>
            <span>Color:</span>
            <div id="colorpicker">
                <input class="color-picker" id="wc_ea_color_<?php echo $id; ?>" name="wc_ea_options[wc_ea_term_meta_<?php echo $id; ?>][wc_ea_color]" type="text" value="<?php echo $value; ?>" />
            </div>
<?php
        } else if ($key == 'wc_ea_text') {
            ?>
            <span>Text:</span>
            <input class="wc_ea_text" id="wc_ea_text_<?php echo $id; ?>"  name="wc_ea_options[wc_ea_term_meta_<?php echo $id; ?>][wc_ea_text]" type="text" value="<?php echo $value; ?>" />

<?php
        } else if ($key == 'wc_ea_image') {

            $image = $value ? wp_get_attachment_thumb_url($value) : '';

            ?>
                <span class="upload_image">
                    <a href="#" class="upload_image_button <?php if ($value > 0) {
                echo 'remove';
            }
            ?>" data-tip="<?php echo __('Remove this image', 'wc-ea-domain'); ?>" rel="<?php echo esc_attr($id); ?>"><img src="<?php if (!empty($image)) {
                echo esc_attr($image);
            } else {
                echo esc_attr(wc_placeholder_img_src());
            }
            ?>" /><input type="hidden" name="wc_ea_options[wc_ea_term_meta_<?php echo $id; ?>][wc_ea_image]" class="upload_image_id" value="<?php echo esc_attr($value); ?>" /></a>
                </span>
<?php
        }
    }
}

new WBV_Admin_Meta_Boxes();
