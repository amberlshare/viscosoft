<?php
/**
 *
 *
 * @class WBV_Settings_General
 * Handles the General tab settings.
 *
 * @author CandleStudio
 * @category Settings
 * @package ButtonVariations\Admin\Settings
 * @version 1.3.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * WBV_Settings_General class.
 */
class WBV_Settings_General extends WBV_Settings_Page
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        global $wc_ea_general_settings;
        $this->label = _x('General', 'General settings tab', 'wc-ea-domain');
        $this->key   = $wc_ea_general_settings;

        add_filter('wbv_settings_tabs_array', array($this, 'add_settings_page'), 20);
        add_action('admin_init', array(&$this, 'load_settings'));
        add_action('admin_init', array(&$this, 'register_settings'));
    }

    public function load_settings()
    {
        $defaults = array(
            'enable_plugin' => '0',
            'switcher'      => '0',
        );
        $this->settings = wp_parse_args((array) get_option($this->key), $defaults);
        update_option($this->key, $this->settings);
    }

    public function register_settings()
    {

        register_setting(
            $this->key,
            $this->key
        );

        add_settings_section(
            'section_general', 'General settings', array(&$this, 'description'), $this->key
        );

        add_settings_field(
            'general_option', 'Enable', array(&$this, 'enable_general_option'), $this->key, 'section_general'
        );

        add_settings_field(
            'switcher', '', array(&$this, 'switcher'), $this->key, 'section_general'
        );

        add_settings_field(
            'products_ids', '', array(&$this, 'product_ids'), $this->key, 'section_general'
        );

        add_settings_field(
            'category_ids', '', array(&$this, 'category_ids'), $this->key, 'section_general'
        );

    }

    public function description()
    {
        echo __('Enabling will make your Variations to display as buttons, you can set each Variation (Attribute) to be shown as a color, text or image button. <br /><br /> It is recommended that before enabling you set colors, text and/or images in the Attributes tab or on Product pages.', 'wc-ea-domain');
    }

    public function enable_general_option()
    {
        ?>
        <input type='checkbox' name="<?php echo $this->key; ?>[enable_plugin]" <?php checked($this->settings['enable_plugin'], 1);?> value='1' />
        <?php
}

    public function switcher()
    {
        $selected = $this->settings['switcher'];
        ?>
            <select name="<?php echo $this->key ?>[switcher]">
                <option <?php selected($selected, '0');?> value="0">Enable for all except</option>
                <option <?php selected($selected, '1');?> value="1">Disable for all except</option>
            </select>
            <p><i>You can still enable or disable Button Variations per product in the Product's Page.</i></p>
        <?php
}

    public function product_ids()
    {

        $product_ids = isset($this->settings['product_ids']) ? $this->settings['product_ids'] : '';

        ?>
        <p>Products</p>
         <input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="<?php echo $this->key; ?>[product_ids]" data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'woocommerce');?>" data-action="woocommerce_json_search_products" data-selected="<?php
        $product_ids = array_filter(array_map('absint', explode(',', $product_ids)));
        $json_ids    = array();

        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if (is_object($product)) {
                $json_ids[$product_id] = wp_kses_post($product->get_formatted_name());
            }
        }

        echo esc_attr(json_encode($json_ids));
        ?>" value="<?php echo implode(',', array_keys($json_ids)); ?>" />
    <?php
}

    public function category_ids()
    {
        $category_ids = isset($this->settings['category_ids']) ? $this->settings['category_ids'] : '';
        if (!is_array($category_ids)){
            $category_ids = array();
        }

        ?>
        <p>Categories</p>
        <select id="product_categories" name="<?php echo $this->key; ?>[category_ids][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e('Any category', 'woocommerce');?>">
                    <?php
        $categories = get_terms('product_cat', 'orderby=name&hide_empty=0');

        if ($categories) {
            foreach ($categories as $cat) {
                echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id, $category_ids), true, false) . '>' . esc_html($cat->name) . '</option>';
            }
        }

        ?>
                </select>
        <?php
}

}

return new WBV_Settings_General();
