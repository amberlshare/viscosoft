<?php
/**
 *
 *
 * @class WBV_Settings_Styling
 * Handles the Styling tab settings.
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
 * WBV_Settings_Styling class.
 */
class WBV_Settings_Styling extends WBV_Settings_Page
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        global $wbv_plugin_settings, $wc_ea_styling_settings;

        $this->label = _x('Styling', 'Styling settings tab', 'wc-ea-domain');
        $this->key   = $wc_ea_styling_settings;
        add_filter('wbv_settings_tabs_array', array($this, 'add_settings_page'), 20);
        add_action('admin_init', array(&$this, 'load_settings'));
        add_action('admin_init', array(&$this, 'register_settings'));
    }

    public function load_settings()
    {
        /**
         * Styling settings
         */
        $defaults = array(
            'enable_styling'              => '1',
            'text__color'                 => '#F4F4F4',
            'text__border_color'          => '#d4d4d4',
            'text__text_color'            => '#333333',
            'text__hover_color'           => '#1e8cbe',
            'text__hover_border_color'    => '#005684',
            'text__hover_text_color'      => '#FFFFFF',
            'text__selected_color'        => '#005684',
            'text__selected_border_color' => '#0074a2',
            'text__selected_text_color'   => '#FFFFFF',
            'button__size'                => '64',
            'button__radius'              => '0',
            'button__border'              => '1',
            'enable_opacity'              => '1',
            'opacity__value'              => '0.2',
            'text_underneath'             => '0',
            'show_checkmark'              => '1',
            'term__margin_left'           => '3',
            'term__margin_right'          => '3',
        );

        $settings = get_option($this->key);

        if (is_array($settings)) {
            if (!isset($settings['enable_styling'])) {
                $settings['enable_styling'] = '0';
            }

            if (!isset($settings['enable_opacity'])) {
                $settings['enable_opacity'] = '0';
            }

            if (!isset($settings['show_checkmark'])) {
                $settings['show_checkmark'] = '0';
            }
        }

        $this->settings = wp_parse_args($settings, $defaults);
        update_option($this->key, $this->settings);
    }

    public function register_settings()
    {
        /**
         *
         * Sections
         *
         */
        add_settings_section(
            'styling_section', 'Styling', array(&$this, 'description'), $this->key
        );

        add_settings_section(
            'styling_section_text_buttons', 'Text Buttons', array(&$this, 'styling_section_normal_description'), $this->key
        );

        /**
         *
         * fields
         *
         */
        add_settings_field(
            'enable_option', 'Enable styling', array(&$this, 'enable_styling_option'), $this->key, 'styling_section'
        );

        add_settings_field(
            'button_size', 'Button Size', array(&$this, 'button_size'), $this->key, 'styling_section'
        );

        add_settings_field(
            'button_radius', 'Button Radius', array(&$this, 'button_radius'), $this->key, 'styling_section'
        );

        add_settings_field(
            'term_margins', 'Item Margins', array(&$this, 'term_margins'), $this->key, 'styling_section'
        );

        add_settings_field(
            'show_checkmark', 'Show Checkmark', array(&$this, 'show_checkmark'), $this->key, 'styling_section'
        );

        add_settings_field(
            'enable_opacity', 'Item Opacity', array(&$this, 'enable_opacity'), $this->key, 'styling_section'
        );

        add_settings_field(
            'opacity__value', 'Opacity Value', array(&$this, 'opacity_value'), $this->key, 'styling_section'
        );

        add_settings_field(
            'text_underneath', 'Text Underneath', array(&$this, 'text_underneath'), $this->key, 'styling_section'
        );

        add_settings_field(
            'button_border', 'Border', array(&$this, 'button_border'), $this->key, 'styling_section'
        );

        add_settings_field(
            'button_color_border', 'Border Color', array(&$this, 'button_color_border'), $this->key, 'styling_section'
        );

        add_settings_field(
            'button_color_border_hover', 'Border Color on Hover', array(&$this, 'button_color_border_hover'), $this->key, 'styling_section'
        );

        add_settings_field(
            'button_color_border_selected', 'Border Color on Selected', array(&$this, 'button_color_border_selected'), $this->key, 'styling_section'
        );

        /*
        Text Buttons Section
         */

        add_settings_field(
            'preview_button', 'Button Preview', array(&$this, 'button_preview'), $this->key, 'styling_section_text_buttons'
        );

        add_settings_field(
            'button_color', 'Button Color', array(&$this, 'button_color'), $this->key, 'styling_section_text_buttons'
        );

        add_settings_field(
            'text_color', 'Text Color', array(&$this, 'text_color'), $this->key, 'styling_section_text_buttons'
        );

        add_settings_field(
            'button_color_hover', 'Button Color on Hover', array(&$this, 'button_color_hover'), $this->key, 'styling_section_text_buttons'
        );

        add_settings_field(
            'text_color_hover', 'Text Color on Hover', array(&$this, 'text_color_hover'), $this->key, 'styling_section_text_buttons'
        );

        add_settings_field(
            'button_color_selected', 'Button Color on Selected', array(&$this, 'button_color_selected'), $this->key, 'styling_section_text_buttons'
        );

        add_settings_field(
            'text_color_selected', 'Text Color on Selected', array(&$this, 'text_color_selected'), $this->key, 'styling_section_text_buttons'
        );

        register_setting(
            $this->key, $this->key
        );
    }

    public function description()
    {
        echo __('Setup your custom styling for your buttons, or disable it to use your own theme\'s styles.', 'wc-ea-domain');
    }

    /**
     *
     *  Normal style section description label
     *
     * @return void
     *
     */
    public function styling_section_normal_description()
    {

        echo __('Specific styles for Text Buttons.', 'wc-ea-domain');
    }

    /**
     *
     * Hover style section description label
     *
     * @return void
     *
     */
    public function styling_section_hover_description()
    {

        echo __('Styles when hovered with the mouse.', 'wc-ea-domain');
    }

    /**
     *
     * Selected style section description label
     * @return void
     *
     */
    public function styling_section_selected_description()
    {

        echo __('Styles when selected.', 'wc-ea-domain');
    }

    /**
     *
     * Enable styling checkbox.
     *
     * @return void
     *
     */
    public function enable_styling_option()
    {
        ?>
        <input type='checkbox' name="<?php echo $this->key; ?>[enable_styling]" <?php checked($this->settings['enable_styling'], 1);?> value='1' />
    <?php
}

    /**
     *
     * Display control for button color option
     *
     * @return void
     *
     */
    public function button_size()
    {

        $size              = ($this->settings['button__size'] != "") ? sanitize_text_field($this->settings['button__size']) : '64';
        $other_size_width  = ($this->settings['button__size_other_width'] != "") ? sanitize_text_field($this->settings['button__size_other_width']) : '';
        $other_size_height = ($this->settings['button__size_other_height'] != "") ? sanitize_text_field($this->settings['button__size_other_height']) : '';
        ?>
        <label title="32pixels">
            <input id="wc_ea_button_size_32" name="<?php echo $this->key; ?>[button__size]" type="radio" value="32" <?php echo ($size == '32') ? 'checked' : '' ?> >32 x 32 <i>(Width x Height)</i>
        </label>
        <br>
        <label title="48pixels">
            <input id="wc_ea_button_size_48" name="<?php echo $this->key; ?>[button__size]" type="radio" value="48" <?php echo ($size == '48') ? 'checked' : '' ?>  >48 x 32
        </label>
        <br>
        <label title="64pixels">
            <input id="wc_ea_button_size_64" name="<?php echo $this->key; ?>[button__size]" type="radio" value="64" <?php echo ($size == '64') ? 'checked' : '' ?> >64 x 32
        </label>
        <br>
        <label title="Other">
            <input id="wc_ea_button_size_other" name="<?php echo $this->key; ?>[button__size]" type="radio" value="Other" <?php echo ($size == 'Other') ? 'checked' : '' ?> >Other
        </label>
        <input type="text" size="3" name="<?php echo $this->key; ?>[button__size_other_width]" value="<?php echo $other_size_width ?>" />W
        x
        <input type="text" size="3" name="<?php echo $this->key; ?>[button__size_other_height]" value="<?php echo $other_size_height ?>" />H
        <p><i>For Image Button sizes go to the Image tab.</i></p>
        <?php
}

    public function button_radius()
    {
        $radius       = ($this->settings['button__radius'] != "") ? sanitize_text_field($this->settings['button__radius']) : '0';
        $other_radius = ($this->settings['button__radius_other'] != "") ? sanitize_text_field($this->settings['button__radius_other']) : '';
        ?>
        <label title="0pixels">
            <input class="" id="wc_ea_button_radius_0" name="<?php echo $this->key; ?>[button__radius]" type="radio" value="0" <?php echo ($radius == '0') ? 'checked' : '' ?> >No radius (square)
        </label>
        <br>
        <label title="2pixels">
            <input class="" id="wc_ea_button_radius_2" name="<?php echo $this->key; ?>[button__radius]" type="radio" value="2" <?php echo ($radius == '2') ? 'checked' : '' ?>  >2 <i>In pixels</i>
        </label>
        <br>
        <label title="4pixels">
            <input class="" id="wc_ea_button_radius_4" name="<?php echo $this->key; ?>[button__radius]" type="radio" value="4" <?php echo ($radius == '4') ? 'checked' : '' ?> >4
        </label>
        <br />
        <label title="rounded">
            <input class="" id="wc_ea_button_radius_rounded" name="<?php echo $this->key; ?>[button__radius]" type="radio" value="rounded" <?php echo ($radius == 'rounded') ? 'checked' : '' ?> >Rounded (If W & H are same).
        </label>
        <br>
        <label title="Other">
            <input class="" id="wc_ea_button_radius_other" name="<?php echo $this->key; ?>[button__radius]" type="radio" value="Other" <?php echo ($radius == 'Other') ? 'checked' : '' ?> >Other
        </label>
        <input type="text" size="3" name="<?php echo $this->key; ?>[button__radius_other]" value="<?php echo $other_radius ?>" /> px
        <p><i><a target="_blank" href="http://support.candlestudio.net/woocommerce/plugins/button-variations/button-variations-the-styling-tab/">Learn more</a> about rounded buttons.</i></p>
        <?php
}

    public function button_border()
    {
        $border       = ($this->settings['button__border'] != "") ? sanitize_text_field($this->settings['button__border']) : '1';
        $other_border = ($this->settings['button__border_other'] != "") ? sanitize_text_field($this->settings['button__border_other']) : '';
        ?>
        <label title="0pixels">
            <input class="" id="wc_ea_button_border_0" name="<?php echo $this->key; ?>[button__border]" type="radio" value="0" <?php echo ($border == '0') ? 'checked' : '' ?> >No border
        </label>
        <br>
        <label title="1pixels">
            <input class="" id="wc_ea_button_border_1" name="<?php echo $this->key; ?>[button__border]" type="radio" value="1" <?php echo ($border == '1') ? 'checked' : '' ?>  >1 <i>In pixels</i>
        </label>
        <br>
        <label title="2pixels">
            <input class="" id="wc_ea_button_border_2" name="<?php echo $this->key; ?>[button__border]" type="radio" value="2" <?php echo ($border == '2') ? 'checked' : '' ?> >2
        </label>
        <br>
        <label title="Other">
            <input class="" id="wc_ea_button_border_other" name="<?php echo $this->key; ?>[button__border]" type="radio" value="Other" <?php echo ($border == 'Other') ? 'checked' : '' ?> >Other
        </label>
        <input type="text" size="3" name="<?php echo $this->key; ?>[button__border_other]" value="<?php echo $other_border ?>" /> px

        <?php
}

    /**
     *
     * Display control for button border color option
     *
     * @return void
     *
     */
    public function button_color_border()
    {

        $color = ($this->settings['text__border_color'] != "") ? sanitize_text_field($this->settings['text__border_color']) : '#d4d4d4';
        ?>
        <input class="color-picker" id="wc_ea_button_color_border" name="<?php echo $this->key; ?>[text__border_color]" type="text" value="<?php echo $color; ?>" />
<?php
echo '<div id="colorpicker"></div>';
    }

    public function term_margins()
    {
        $term_margin_left  = $this->settings['term__margin_left'];
        $term_margin_right = $this->settings['term__margin_right'];
        ?>
        L<input title="Left" type="text" size="1" name="<?php echo $this->key; ?>[term__margin_left]" value="<?php echo $term_margin_left ?>" />
        — R<input title="Right" type="text" size="1" name="<?php echo $this->key; ?>[term__margin_right]" value="<?php echo $term_margin_right ?>" />
        <p><i>In pixels</i></p>
        <?php
}

    /**
     *
     * Show checkmark option
     *
     * @return void
     *
     */
    public function show_checkmark()
    {
        ?>
        <input type='checkbox' name="<?php echo $this->key; ?>[show_checkmark]" <?php checked($this->settings['show_checkmark'], 1);?> value='1' />
        <i>Show a checkmark image on Color and Image Buttons.</i>
    <?php
}

    /**
     *
     * Enable opacity for selected, disabled and Out of Stock variations.
     *
     */
    public function enable_opacity()
    {
        ?>
        <input type='checkbox' name="<?php echo $this->key; ?>[enable_opacity]" <?php checked($this->settings['enable_opacity'], 1);?> value='1' /> <i>Enable opacity for disabled variations. <br> Recommended.</i>
        <?php
}

    public function opacity_value()
    {
        $selected = $this->settings['opacity__value'];
        ?>
            <select name="<?php echo $this->key ?>[opacity__value]">
                <option <?php selected($selected, '0.1');?> value="0.1">0.1</option>
                <option <?php selected($selected, '0.2');?> value="0.2">0.2</option>
                <option <?php selected($selected, '0.3');?> value="0.3">0.3</option>
                <option <?php selected($selected, '0.4');?> value="0.4">0.4</option>
                <option <?php selected($selected, '0.5');?> value="0.5">0.5</option>
                <option <?php selected($selected, '0.6');?> value="0.6">0.6</option>
                <option <?php selected($selected, '0.7');?> value="0.7">0.7</option>
                <option <?php selected($selected, '0.8');?> value="0.8">0.8</option>
                <option <?php selected($selected, '0.9');?> value="0.9">0.9</option>
            </select>
            <p><i>Closer to 0 is more opaque.</i></p>
        <?php
    }

    public function text_underneath()
    {
        ?>
        <input type='checkbox' name="<?php echo $this->key; ?>[text_underneath]" <?php checked($this->settings['text_underneath'], 1);?> value='1' /> <i>Show related text underneath Color and Image buttons. <a target="_blank" href="http://support.candlestudio.net/woocommerce/plugins/button-variations/button-variations-the-styling-tab/">Learn more.</a></i>
        <?php
}

    /**
     *
     * Display control for button color option
     *
     * @return void
     *
     */
    public function button_color()
    {

        $color = ($this->settings['text__color'] != "") ? sanitize_text_field($this->settings['text__color']) : '#F4F4F4';
        ?>
        <input class="color-picker" id="wc_ea_button_color" name="<?php echo $this->key; ?>[text__color]" type="text" value="<?php echo $color; ?>" />
        <?php
echo '<div id="colorpicker"></div>';
    }

    /**
     *
     *
     *
     */
    public function button_preview()
    {

        /**
         *  basic styling
         */
        $color             = ($this->settings['text__color'] != "") ? sanitize_text_field($this->settings['text__color']) : '#F4F4F4';
        $size              = ($this->settings['button__size'] != "") ? sanitize_text_field($this->settings['button__size']) : '32';
        $other_size_width  = ($this->settings['button__size_other_width'] != "") ? sanitize_text_field($this->settings['button__size_other_width']) : '';
        $other_size_height = ($this->settings['button__size_other_height'] != "") ? sanitize_text_field($this->settings['button__size_other_height']) : '';
        $other_radius      = ($this->settings['button__radius_other'] != "") ? sanitize_text_field($this->settings['button__radius_other']) : '';
        $radius            = ($this->settings['button__radius'] != "") ? sanitize_text_field($this->settings['button__radius']) : '0';
        if ($radius !== 'rounded') {
            $radius .= 'px';
        } else {
            $radius = "50%";
        }
        $radius       = ($radius === 'Other') ? $other_radius . "px" : $radius;
        $border       = ($this->settings['button__border'] != "") ? sanitize_text_field($this->settings['button__border']) : '1';
        $other_border = ($this->settings['button__border_other'] != "") ? sanitize_text_field($this->settings['button__border_other']) : '';
        $border_color = ($this->settings['text__border_color'] != "") ? sanitize_text_field($this->settings['text__border_color']) : '#d4d4d4';
        $text_color   = ($this->settings['text__text_color'] != "") ? sanitize_text_field($this->settings['text__text_color']) : '#333333';
        $margin_left  = ($this->settings['term__margin_left'] != "") ? sanitize_text_field($this->settings['term__margin_left']) : '#333333';
        $margin_right = ($this->settings['term__margin_right'] != "") ? sanitize_text_field($this->settings['term__margin_right']) : '#333333';

        /**
         *
         * hover styles
         *
         */
        $hover_bg         = ($this->settings['text__hover_color'] != "") ? sanitize_text_field($this->settings['text__hover_color']) : '#FFFFFF';
        $hover_border     = ($this->settings['text__hover_border_color'] != "") ? sanitize_text_field($this->settings['text__hover_border_color']) : '#005684';
        $text_color_hover = ($this->settings['text__hover_text_color'] != "") ? sanitize_text_field($this->settings['text__hover_text_color']) : '#FFFFFF';

        /**
         *
         * selected, active and focus styles
         *
         */
        $selected_border     = ($this->settings['text__selected_border_color'] != "") ? sanitize_text_field($this->settings['text__selected_border_color']) : '#0074a2';
        $text_color_selected = ($this->settings['text__selected_text_color'] != "") ? sanitize_text_field($this->settings['text__selected_text_color']) : '#FFFFFF';
        $selected_bg         = ($this->settings['text__selected_color'] != "") ? sanitize_text_field($this->settings['text__selected_color']) : '#FFFFFF';
        ?>
        <style type="text/css">
            .wc_ea_button_preview {
                display:block;
                background-color: <?php echo $color ?>;
                width: <?php echo ($size == 'Other') ? $other_size_width : $size ?>px;
                height: <?php echo ($size == 'Other') ? $other_size_height : '32' ?>px;
                line-height: <?php echo ($size == 'Other') ? $other_size_height : '32' ?>px;
                border-radius: <?php echo $radius; ?>;
                -webkit-border-radius: <?php echo $radius; ?>;
                -moz-border-radius: <?php echo $radius; ?>;
                border-top-left-radius: <?php echo $radius; ?>;
                border-top-right-radius: <?php echo $radius; ?>;
                order-bottom-right-radius: <?php echo $radius; ?>;
                border-bottom-left-radius: <?php echo $radius; ?>;
                border: <?php echo ($border == 'Other') ? $other_border : $border ?>px;
                border-color: <?php echo $border_color ?>;
                border-style: solid;
                color: <?php echo ($text_color) ?>;
                text-decoration: none;
                vertical-align:middle;
                text-align: center;
                float: left;
                margin-left: <?php echo $margin_left; ?>px;
                margin-right: <?php echo $margin_right; ?>px;
            }
            .wc_ea_button_preview:hover {
                background-color: <?php echo $hover_bg ?>;
                border-color: <?php echo $hover_border ?>;
                color: <?php echo $text_color_hover ?>;
            }

            .wc_ea_button_preview:focus,
            .wc_ea_button_preview:active{
                background-color: <?php echo $selected_bg ?>;
                border-color: <?php echo $selected_border ?>;
                color: <?php echo $text_color_selected ?>;
                box-shadow: none;
            }

        </style>
        <a href="#" class="wc_ea_button_preview text_preview">Text</a>
        <a href="#" class="wc_ea_button_preview text_preview">Text</a>
        <a href="#" class="wc_ea_button_preview text_preview">Text</a>
        <div class="clear"></div>
        <p><i>Text button preview. <br> Change below and update to view your changes</i></p>
        <script type="text/javascript">
            jQuery('.wc_ea_button_preview').on('click', function(e){
                e.preventDefault();
        });
        </script>
        <?php
}
    /**
     *
     * Display control for button color on hover
     *
     * @return void
     *
     */
    public function button_color_hover()
    {

        $color = ($this->settings['text__hover_color'] != "") ? sanitize_text_field($this->settings['text__hover_color']) : '#1e8cbe';
        ?>
        <input class="color-picker" id="wc_ea_button_color_hover" name="<?php echo $this->key; ?>[text__hover_color]" type="text" value="<?php echo $color; ?>" />
        <?php
echo '<div id="colorpicker"></div>';
    }

    /**
     *
     * Display control for button border color on hover option
     *
     * @return void
     *
     */
    public function button_color_border_hover()
    {

        $color = ($this->settings['text__hover_border_color'] != "") ? sanitize_text_field($this->settings['text__hover_border_color']) : '#005684';
        ?>
        <input class="color-picker" id="wc_ea_button_color_border_hover" name="<?php echo $this->key; ?>[text__hover_border_color]" type="text" value="<?php echo $color; ?>" />
        <?php
echo '<div id="colorpicker"></div>';
    }

    /**
     *
     * Display control for button selected color option
     *
     * @return void
     *
     */
    public function button_color_selected()
    {

        $color = ($this->settings['text__selected_color'] != "") ? sanitize_text_field($this->settings['text__selected_color']) : '#005684';
        ?>
        <input class="color-picker" id="wc_ea_button_color_selected" name="<?php echo $this->key; ?>[text__selected_color]" type="text" value="<?php echo $color; ?>" />
        <?php
echo '<div id="colorpicker"></div>';
    }

    /**
     *
     * Display control for button border color on selected option
     *
     * @return void
     *
     */
    public function button_color_border_selected()
    {

        $color = ($this->settings['text__selected_border_color'] != "") ? sanitize_text_field($this->settings['text__selected_border_color']) : '#0074a2';
        ?>
        <input class="color-picker" id="wc_ea_button_color_border_selected" name="<?php echo $this->key; ?>[text__selected_border_color]" type="text" value="<?php echo $color; ?>" />
        <?php
echo '<div id="colorpicker"></div>';
    }

    /**
     *
     *  Display control for text color option
     *
     * @return void
     *
     */
    public function text_color()
    {

        $color = ($this->settings['text__text_color'] != "") ? sanitize_text_field($this->settings['text__text_color']) : '#333333';
        ?>
        <input class="color-picker" id="text__text_color" name="<?php echo $this->key; ?>[text__text_color]" type="text" value="<?php echo $color; ?>" />
        <?php
echo '<div id="colorpicker"></div>';
    }

    /**
     *
     * Display control for text color on hover option
     *
     * @return void
     *
     */
    public function text_color_hover()
    {
        $color = ($this->settings['text__hover_text_color'] != "") ? sanitize_text_field($this->settings['text__hover_text_color']) : '#FFFFFF';
        ?>
        <input class="color-picker" id="text__hover_text_color" name="<?php echo $this->key; ?>[text__hover_text_color]" type="text" value="<?php echo $color; ?>" />
        <?php
echo '<div id="colorpicker"></div>';
    }

    /**
     *
     * Display control for text color on selected option
     *
     * @return void
     *
     */
    public function text_color_selected()
    {

        $color = ($this->settings['text__selected_text_color'] != "") ? sanitize_text_field($this->settings['text__selected_text_color']) : '#FFFFFF';
        ?>
        <input class="color-picker" id="wc_ea_text_bg_color_selected" name="<?php echo $this->key; ?>[text__selected_text_color]" type="text" value="<?php echo $color; ?>" />
        <?php
echo '<div id="colorpicker"></div>';
    }

}

return new WBV_Settings_Styling();