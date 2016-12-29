<?php
/**
 *
 * @class WBV_Settings_Attributes
 * Handles the Attributes tab settings
 * .
 * @author CandleStudio
 * @category Settings
 * @package ButtonVariations\Admin\Settings
 * @version 1.3.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * WBV_Settings_Attributes class.
 */
class WBV_Settings_Attributes extends WBV_Settings_Page
{
    /**
     * Constructor.
     */
    public function __construct()
    {

        global $wc_ea_attributes_settings;
        $this->key    = $wc_ea_attributes_settings;
        $this->label = _x('Attributes', 'Attribute settings tab', 'wc-ea-domain');
        add_filter('wbv_settings_tabs_array', array($this, 'add_settings_page'), 20);
        add_action('admin_init', array(&$this, 'register_settings'));
        add_action('admin_init', array(&$this, 'register_edit_tax'));
    }

    public function register_settings()
    {
        add_settings_section(
            'section_general', 'Attribute Settings', array(&$this, 'description'), $this->key);
    }

    /**
     *
     *
     * Attribute tab
     * Render tax table using WP_List_Table
     *
     * @return void
     *
     *
     */
    public function description()
    {

        $wp_list_table = new WBV_Attributes_List();
        $wp_list_table->prepare_items();
        $wp_list_table->display();
    }

    /**
     *
     * Register settings for each Attribute/Taxonomy.
     *
     * @return void
     *
     */
    public function register_edit_tax()
    {

        global $woocommerce, $wc_ea_att_options_prefix;

        if ($attribute_taxonomies = wc_get_attribute_taxonomies()) {

            foreach ($attribute_taxonomies as $tax) {

                if ($name = wc_attribute_taxonomy_name($tax->attribute_name)) {

                    register_setting(
                        $wc_ea_att_options_prefix . '_' . $name, $wc_ea_att_options_prefix . '_' . $name
                    );

                    add_settings_section(
                        'edit_tax_section_' . $name, 'Edit ' . $tax->attribute_label, array(&$this, 'edit_tax_section_desc'), $wc_ea_att_options_prefix . '_' . $name
                    );

                    add_settings_field(
                        'wc_ea_att_type_' . $name, 'Display Type', array(&$this, 'attribute_option'), $wc_ea_att_options_prefix . '_' . $name, 'edit_tax_section_' . $name, array('name' => $name)
                    );
                }
            }
        }
    }

    /**
     * Display label for Attribute editing.
     *
     * @return null
     */
    public function edit_tax_section_desc()
    {

        echo '<p>' . __('Text types will use Term Label if no text is defined.', 'wc-ea-domain') . '</p>';
    }

    /**
     * Dynamic display of settings.
     *
     * @param  [array] $args array with the term name.
     * @return void
     */
    public function attribute_option($args)
    {
        global $wc_ea_att_options_prefix;
        $option = get_option($wc_ea_att_options_prefix . '_' . $args['name']);
        ?>

        <select style="width:300px;" name="<?php echo $wc_ea_att_options_prefix . '_' . $args['name'] ?>" id="wc_ea_attribute">
            <option value="color" <?php selected($option, 'color')?>>Color</option>
            <option value="text" <?php selected($option, 'text')?>>Text</option>
            <option value="image" <?php selected($option, 'image')?>>Image</option>
        </select>
        <?php $link = 'edit-tags.php?taxonomy=' . $args['name'] . '&post_type=product';?>
        <p><?php printf(__('Setup colors or extra text for this Attribute <a href="%s">here.</a>', 'wc-ea-domain'), $link);?></p>
        <?php
}

}

return new WBV_Settings_Attributes();