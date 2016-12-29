<?php
/**
 *
 * @class WBV_Settings_Images
 * Handles the Image tab settings.
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
 * WBV_Settings_Images Class.
 */
class WBV_Settings_Images extends WBV_Settings_Page
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        global $wc_ea_image_settings;
        $this->label = _x('Images', 'Images settings tab', 'wc-ea-domain');
        $this->key   = $wc_ea_image_settings;

        add_filter('wbv_settings_tabs_array', array($this, 'add_settings_page'), 20);
        add_action('admin_init', array(&$this, 'load_settings'));
        add_action('admin_init', array(&$this, 'register_settings'));
    }

    public function register_settings()
    {
        register_setting(
            $this->key, $this->key
        );

        /**
         *
         * Size section & fields
         */
        add_settings_section(
            'image_button_size', 'Image Size', array(&$this, 'section_image_button_size_desc'), $this->key
        );

        add_settings_field(
            'image_button_size', 'Size', array(&$this, 'image_button_size'), $this->key, 'image_button_size'
        );

        add_settings_field(
            'image__radius', 'Radius', array(&$this, 'image_radius'), $this->key, 'image_button_size'
        );

        /**
         * Selected section & fields
         */
        add_settings_section(
            'image_button_selected', 'Selected Style', array(&$this, 'section_image_button_selected_desc'), $this->key
        );

        add_settings_field(
            'image_button_selected_overlay', 'Image Overlay', array(&$this, 'image_button_selected_overlay'), $this->key, 'image_button_selected'
        );

    }

    public function load_settings()
    {
        /**
         * Image settings
         */
        $defaults = array(
            'image__size'             => 'preset',
            'image__size_preset'      => 'thumbnail',
            'image__selected_overlay' => '0',
            'image__radius'           => '0',
            'image__aspect_ratio'     => '1',
        );

        $settings = get_option($this->key);

        if (is_array($settings)) {
            if (!isset($settings['image__aspect_ratio'])) {
                $settings['image__aspect_ratio'] = '0';
            }
        }

        $this->settings = wp_parse_args($settings, $defaults);
        update_option($this->key, $this->settings);
    }

    /**
     *
     * Image Options description label
     *
     * @return void
     *
     */
    public function section_image_button_size_desc()
    {

        echo __('Set the <strong>max</strong> image button dimensions', 'wc-ea-domain');
    }

    public function section_image_button_selected_desc()
    {

        echo __('Styles when an image is clicked/selected', 'wc-ea-domain');
    }

    public function image_button_size()
    {
        global $_wp_additional_image_sizes;

        $sizes                        = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info
        foreach ($get_intermediate_image_sizes as $_size) {
            if (in_array($_size, array('thumbnail', 'medium', 'large'))) {
                $sizes[$_size]['width']  = get_option($_size . '_size_w');
                $sizes[$_size]['height'] = get_option($_size . '_size_h');
                $sizes[$_size]['crop']   = (bool) get_option($_size . '_crop');
            } elseif (isset($_wp_additional_image_sizes[$_size])) {
                $sizes[$_size] = array(
                    'width'  => $_wp_additional_image_sizes[$_size]['width'],
                    'height' => $_wp_additional_image_sizes[$_size]['height'],
                    'crop'   => $_wp_additional_image_sizes[$_size]['crop'],
                );
            }
        }

        $image_size = $this->settings['image__size'];

        $preset = ($this->settings['image__size_preset'] != "") ? sanitize_text_field($this->settings['image__size_preset']) : 'thumbnail';

        $other_size_width  = ($this->settings['image__size_other_width'] != "") ? sanitize_text_field($this->settings['image__size_other_width']) : '';
        $other_size_height = ($this->settings['image__size_other_height'] != "") ? sanitize_text_field($this->settings['image__size_other_height']) : '';

        ?>
        <label title="Image Size Preset">
            <input class="" id="wc_ea_image_size_preset" <?php echo ($image_size == 'preset') ? 'checked' : '' ?> name="<?php echo $this->key; ?>[image__size]" type="radio" value="preset" ?> Presets
        </label>

        <select name="<?php echo $this->key ?>[image__size_preset]">
<?php

        foreach ($sizes as $key => $value) {

            $title = str_replace('_', ' ', $key);
            $title = ucwords($title);
            $title .= ' (' . $value['width'] . 'px - ' . $value['height'] . 'px)    ';
            $selected = ($preset == $key) ? 'selected' : '';
            echo '<option ' . $selected . ' value="' . $key . '" >' . $title . '</option>';
        }
        if ($preset == 'full') {
            $selected = 'selected';
        } else {
            $selected = '';
        }

        ?>
            <option <?php echo $selected ?> value="full">Full</option>
        </select>
        <br />
        <label title="Image Size Other">
            <input id="wc_ea_image_size_other"  <?php echo ($image_size == 'other') ? 'checked' : '' ?> name="<?php echo $this->key; ?>[image__size]" type="radio" value="other" ?> Other
        </label>

        <input type="text" size="3" name="<?php echo $this->key; ?>[image__size_other_width]" value="<?php echo $other_size_width ?>" />W
        x
        <input type="text" size="3" name="<?php echo $this->key; ?>[image__size_other_height]" value="<?php echo $other_size_height ?>" />H
        <br />
        <input style="margin-left: 1.5em;" type='checkbox' name="<?php echo $this->key; ?>[image__aspect_ratio]" <?php checked($this->settings['image__aspect_ratio'], 1);?> value='1' /> <i>Try keeping aspect ratio on fixed sizes.
    <?php
    }

    /**
     *
     * Enable opacity for selected images
     *
     */
    public function image_button_selected_overlay()
    {
        ?>
        <input type='checkbox' name="<?php echo $this->key; ?>[image__selected_overlay]" <?php checked($this->settings['image__selected_overlay'], 1);?> value='1' /> <i>Check to have an overlay on selected images.</i>
        <?php
    }

    public function image_radius()
    {

        $radius       = $this->settings['image__radius'];
        $other_radius = ($this->settings['image__radius_other'] != "") ? sanitize_text_field($this->settings['image__radius_other']) : '';
        ?>
        <label title="0pixels">
            <input class="" id="wc_ea_button_radius_0" name="<?php echo $this->key; ?>[image__radius]" type="radio" value="0" <?php echo ($radius == '0') ? 'checked' : '' ?> >No radius (square)
        </label>
        <br>
        <label title="2pixels">
            <input class="" id="wc_ea_button_radius_2" name="<?php echo $this->key; ?>[image__radius]" type="radio" value="2" <?php echo ($radius == '2') ? 'checked' : '' ?>  >2 <i>In pixels</i>
        </label>
        <br>
        <label title="4pixels">
            <input class="" id="wc_ea_button_radius_4" name="<?php echo $this->key; ?>[image__radius]" type="radio" value="4" <?php echo ($radius == '4') ? 'checked' : '' ?> >4
        </label>
        <br>
        <label title="rounded">
            <input class="" id="wc_ea_button_radius_rounded" name="<?php echo $this->key; ?>[image__radius]" type="radio" value="rounded" <?php echo ($radius == 'rounded') ? 'checked' : '' ?> >Rounded (If W & H are same).
        </label>
        <br>
        <label title="Other">
            <input class="" id="wc_ea_button_radius_other" name="<?php echo $this->key; ?>[image__radius]" type="radio" value="Other" <?php echo ($radius == 'Other') ? 'checked' : '' ?> >Other
        </label>
        <input type="text" size="3" name="<?php echo $this->key; ?>[image__radius_other]" value="<?php echo $other_radius ?>" /> px
        <p><i><a target="_blank" href="http://support.candlestudio.net/woocommerce/plugins/button-variations/8-the-images-tab/">Learn more</a> about rounded image buttons.</i></p>
        <?php

    }
}

return new WBV_Settings_Images();