<?php
/**
 * Woocommerce Button Variations functions
 *
 * Frontend functions and actions.
 *
 * @author   CandleStudio
 * @category FrontEnd
 * @package  ButtonVariations\Functions
 * @version  1.4.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!function_exists('wbv_display_buttons')) {
    /**
     * Display the buttons in Product page
     * @param  array  $args options
     * @return void
     */
    function wbv_display_buttons($args = array())
    {

        global $wc_ea_att_options_prefix, $wc_ea_image_settings, $wc_ea_term_meta, $wc_ea_styling_settings;

        $args = wp_parse_args(apply_filters('woocommerce_dropdown_variation_attribute_options_args', $args), array(
            'options'          => false,
            'attribute'        => false,
            'product'          => false,
            'selected'         => false,
            'name'             => '',
            'id'               => '',
            'class'            => '',
            'show_option_none' => __('Choose an option', 'woocommerce'),
        ));

        $options   = $args['options'];
        $product   = $args['product'];
        $attribute = $args['attribute'];
        $name      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title($attribute);
        $id        = $args['id'] ? $args['id'] : sanitize_title($attribute);
        $class     = $args['class'];

        if (empty($options) && !empty($product) && !empty($attribute)) {
            $attributes = $product->get_variation_attributes();
            $options    = $attributes[$attribute];
        }

        $image_settings   = get_option($wc_ea_image_settings);
        $styling_settings = get_option($wc_ea_styling_settings);
        $image_size       = $image_settings['image__size'];
        $checkmark        = $styling_settings['show_checkmark'] == '1' ? 'checkmark' : '';
        $image_overlay    = $image_settings['image__selected_overlay'] == '1' ? 'masked' : '';

        if ($image_size == 'preset') {
            $image_size_preset = $image_settings['image__size_preset'];
        } else {
            $image_width       = $image_settings['image__size_other_width'];
            $image_height      = $image_settings['image__size_other_height'];
            $image_size_preset = 'full';
        }

        ?>  <div class="wc_ea_container" id="<?php echo esc_attr(sanitize_title($attribute)); ?>" name="attribute_<?php echo sanitize_title($attribute); ?>"> <?php
if (is_array($options) && !empty($options)) {

            // Get terms if this is a taxonomy - ordered
            if ($product && taxonomy_exists(sanitize_title($attribute))) {

                $wc_ea_att_option = get_option($wc_ea_att_options_prefix . '_' . $attribute);

                //$terms = get_terms( sanitize_title( $attribute ), $args );
                $terms = wc_get_product_terms($product->id, $attribute, array('fields' => 'all'));

                foreach ($terms as $term) {

                    if (!in_array($term->slug, $options)) {
                        continue;
                    }

                    $term_value = get_option($wc_ea_term_meta . $term->term_id);

                    $selected_classes = ($term->slug === $args['selected']) ? 'selected active' : '';

                    if ($wc_ea_att_option == 'color') {

                        $classes = "wc_ea_button wc_ea_color " . $selected_classes . " " . $checkmark;

                        /*
                        Text Underneath
                         */
                        $text_value = "";
                        if (isset($styling_settings['text_underneath']) && $styling_settings['text_underneath'] === '1') {
                            if ($term_value['wc_ea_text']) {
                                $text_value = $term_value['wc_ea_text'];
                            } else {
                                $text_value = $term->name;
                            }
                        }
                        $text_below = '<span class="wc_ea_text_below">' . $text_value . '</span>';

                        echo '<div class="wc_ea_button_wrapper"><a href="#" name="' . esc_attr($term->slug) . '" class="' . $classes . '" title="' . $term->name . '" style="background-color: ' . $term_value['wc_ea_color'] . '"></a>' . $text_below . '</div>';

                    } else if ($wc_ea_att_option == 'text') {

                        if ($term_value['wc_ea_text']) {
                            $text_value = $term_value['wc_ea_text'];
                        } else {
                            $text_value = $term->name;
                        }

                        $classes = "wc_ea_button wc_ea_text " . $selected_classes;
                        echo '<div class="wc_ea_button_wrapper"><a href="#" name="' . esc_attr($term->slug) . '" class="' . $classes . '" title="' . $text_value . '">' . $text_value . '</a></div>';

                    } else if ($wc_ea_att_option == 'image') {

                        $classes       = "wc_ea_button wc_ea_image " . $selected_classes . " " . $checkmark . " " . $image_overlay;
                        $attachment_id = $term_value['wc_ea_image'];

                        $img_src = wp_get_attachment_image_src($attachment_id, $image_size_preset);

                        /*
                        Text Underneath
                         */
                        $text_value = "";
                        if (isset($styling_settings['text_underneath']) && $styling_settings['text_underneath'] === '1') {
                            if ($term_value['wc_ea_text']) {
                                $text_value = $term_value['wc_ea_text'];
                            } else {
                                $text_value = $term->name;
                            }
                        }
                        $text_below = '<span class="wc_ea_text_below">' . $text_value . '</span>';

                        $add_width_height = '';
                        if ($image_size === 'other') {
                            $add_width_height = 'width="' . $image_width . '" height="' . $image_height . '"';
                        }
                        echo '<div class="wc_ea_button_wrapper"><a href="#" name="' . esc_attr($term->slug) . '" class="' . $classes . '" title="' . $term->name . '"><img ' . $add_width_height . ' src="' . $img_src[0] . '" /></a>' . $text_below . '</div>';
                    }
                }
            } else {

                $wc_ea_att_option = get_option($wc_ea_att_options_prefix . '_' . $product->id . '_' . $attribute);

                foreach ($options as $option) {

                    $term_value = get_option($wc_ea_term_meta . $product->id . '_' . $option);

                    $selected_classes = ($option === $args['selected']) ? 'selected active' : '';

                    if ($wc_ea_att_option == 'color') {

                        $classes = "wc_ea_button wc_ea_color " . $selected_classes . " " . $checkmark;

                        /*
                        Text Underneath
                         */
                        $text_value = "";
                        if (isset($styling_settings['text_underneath']) && $styling_settings['text_underneath'] === '1') {
                            if ($term_value['wc_ea_text']) {
                                $text_value = $term_value['wc_ea_text'];
                            } else {
                                $text_value = $option;
                            }
                        }
                        $text_below = '<span class="wc_ea_text_below">' . $text_value . '</span>';

                        echo '<div class="wc_ea_button_wrapper"> <a href="#" name="' . esc_attr($option) . '" class="' . $classes . '" title="' . $option . '" style="background-color: ' . $term_value['wc_ea_color'] . '">&nbsp;</a>' . $text_below . '</div>';

                    } else if ($wc_ea_att_option == 'text') {

                        if ($term_value['wc_ea_text']) {
                            $text_value = $term_value['wc_ea_text'];
                        } else {
                            $text_value = $option;
                        }
                        $classes = "wc_ea_button wc_ea_text " . $selected_classes;
                        echo '<div class="wc_ea_button_wrapper"><a href="#" name="' . esc_attr($option) . '" class="' . $classes . '" title="' . $text_value . '">' . $text_value . '</a></div>';

                    }if ($wc_ea_att_option == 'image') {

                        $classes       = "wc_ea_button wc_ea_image " . $selected_classes . " " . $checkmark . " " . $image_overlay;
                        $attachment_id = $term_value['wc_ea_image'];

                        $img_src = wp_get_attachment_image_src($attachment_id, $image_size_preset);

                        /*
                        Text Underneath
                         */
                        $text_value = "";
                        if (isset($styling_settings['text_underneath']) && $styling_settings['text_underneath'] === '1') {
                            if ($term_value['wc_ea_text']) {
                                $text_value = $term_value['wc_ea_text'];
                            } else {
                                $text_value = $term->name;
                            }
                        }
                        $text_below = '<p class="wc_ea_text_below">' . $text_value . '</p>';

                        $add_width_height = '';
                        if ($image_size === 'other') {
                            $add_width_height = 'width="' . $image_width . '" height="' . $image_height . '"';
                        }
                        echo '<div class="wc_ea_button_wrapper"><a href="#" name="' . esc_attr($option) . '" class="' . $classes . '" title="' . esc_attr($option) . '"><img ' . $add_width_height . ' src="' . $img_src[0] . '" /></a>' . $text_below . '</div>';
                    }
                }
            }
        }
        ?>
            <input type="hidden" name="attribute_<?php echo esc_attr(sanitize_title($attribute)) ?>" value="" />
        </div>
        <?php
}
}
/**
 * Recursively strip slashes for an array or string.
 *
 * @param  mixed $variable [description]
 * @return mixed    Slashes-free array or string.
 *
 */
if (!function_exists('strip_slashes_recursive')) {

    function strip_slashes_recursive($variable)
    {
        if (is_string($variable)) {
            return stripslashes($variable);
        }
        if (is_array($variable)) {
            foreach ($variable as $i => $value) {
                $variable[$i] = strip_slashes_recursive($value);
            }
        }

        return $variable;
    }
}

if (!function_exists('wbv_styling')) {

    /**
     *
     *
     * Build styling css for buttons.
     *
     *
     * @param  array $styles options
     * @return string string containing the css to be inserted into the page
     */
    function wbv_styling($styles)
    {

        $css = ".wc_ea_button {
        text-decoration: none; }";

        if ($styles['enable_styling']) {

            /**
             * height and width
             */
            if ($styles['button__size'] === 'Other') {
                $button_height = $styles['button__size_other_height'];
                $button_width  = $styles['button__size_other_width'];
            } else {
                $button_height = '32';
                $button_width  = $styles['button__size'];
            }

            /**
             * radius
             */
            if ($styles['button__radius'] === 'Other') {
                $button_radius = $styles['button__radius_other'] . "px";
            } elseif ($styles['button__radius'] === 'rounded') {
                $button_radius = "50%";
            } else {
                $button_radius = $styles['button__radius'] . "px";
            }

            /**
             * border
             */
            if ($styles['button__border'] === 'Other') {
                $button_border = $styles['button__border_other'];
            } else {
                $button_border = $styles['button__border'];
            }

            /**
             * opacity
             */
            if ($styles['enable_opacity']) {
                $css .= "

                .variations .wc_ea_button.disabled,
                .variations a[disabled='disabled']
                    {
                        text-decoration: line-through;
                        opacity:" . $styles['opacity__value'] . ";
                    }
                ";
            }

            $css .= "

            .wc_ea_button_wrapper{
                margin-left: " . $styles['term__margin_left'] . "px;
                margin-right: " . $styles['term__margin_right'] . "px;
            }

            .wc_ea_button {
                min-height:" . $button_height . "px;
                min-width:" . $button_width . "px;
                line-height:" . $button_height . "px;
                border: " . $button_border . "px solid;
                -webkit-border-radius: " . $button_radius . ";
                -moz-border-radius: " . $button_radius . ";
                border-top-left-radius: " . $button_radius . ";
                border-top-right-radius: " . $button_radius . ";
                border-bottom-right-radius: " . $button_radius . ";
                border-bottom-left-radius: " . $button_radius . ";
                border-radius: " . $button_radius . ";
                border-color: " . $styles['text__border_color'] . ";
                padding: 0;
            }
            .wc_ea_button.wc_ea_color, .wc_ea_button.wc_ea_text{
                height:" . $button_height . "px;
                width:" . $button_width . "px;
                line-height:" . $button_height . "px;
            }

            .wc_ea_button:hover{
                border-color: " . $styles['text__hover_border_color'] . ";
            }

            .wc_ea_button.disabled:hover{
                border-color: " . $styles['text__border_color'] . ";
            }

            .wc_ea_button.selected{
                border-color: " . $styles['text__selected_border_color'] . ";
            }

            .wc_ea_button.wc_ea_text, .wc_ea_button.wc_ea_text.disabled:hover{
                background-color: " . $styles['text__color'] . ";
                color: " . $styles['text__text_color'] . " !important;
            }

            .wc_ea_button.wc_ea_text:hover{
                background-color: " . $styles['text__hover_color'] . ";
                color: " . $styles['text__hover_text_color'] . " !important;
            }

            .wc_ea_button.wc_ea_text.selected{
                background-color: " . $styles['text__selected_color'] . ";
                color: " . $styles['text__selected_text_color'] . "!important;
            }

            ";

        }

        return $css;
    }
}

/**
 * Build styles for image buttons.
 * @param  [type] $styles [description]
 * @return [type]         [description]
 */
function wbv_images_styling($styles)
{
    if ($styles['image__radius'] === 'Other') {
        $image_radius = $styles['image__radius_other'] . 'px';
    } elseif ($styles['image__radius'] === 'rounded') {
        $image_radius = "50%";
    } else {
        $image_radius = $styles['image__radius'] . 'px';
    }

    if ($styles['image__size'] !== 'preset') {
        if ($styles['image__aspect_ratio'] === '1') {
            $aspect_ratio = "height: auto; max-width: 100%;";
        } else {
            $image_width  = $styles['image__size_other_width'];
            $image_height = $styles['image__size_other_height'];
            $aspect_ratio = "height: " . $image_width . "px ; width: " . $image_height . "px;";
        }
    }

    $css = "
            .wc_ea_button.wc_ea_image, .wc_ea_button img{
                -webkit-border-radius: " . $image_radius . ";
                -moz-border-radius: " . $image_radius . ";
                border-top-left-radius: " . $image_radius . ";
                border-top-right-radius: " . $image_radius . ";
                border-bottom-right-radius: " . $image_radius . ";
                border-bottom-left-radius: " . $image_radius . ";
                border-radius: " . $image_radius . "px;
            }

            .variations .wc_ea_image.selected.masked:after{
                -webkit-border-radius: " . $image_radius . ";
                -moz-border-radius: " . $image_radius . ";
                border-top-left-radius: " . $image_radius . ";
                border-top-right-radius: " . $image_radius . ";
                border-bottom-right-radius: " . $image_radius . ";
                border-bottom-left-radius: " . $image_radius . ";
                border-radius: " . $image_radius . "px;
            }

            ";

        if (isset($aspect_ratio)){
            $css .= "
             .wc_ea_button.wc_ea_image img{
                " . $aspect_ratio . "
            }

            ";
        }

    return $css;
}
/**
 * Callback function for
 * woocommerce_dropdown_variation_attribute_options_html
 * filter.
 *
 * This filter gets called just before echoing the html for the
 * variations dropdowns. So Button Variations overrides it.
 * Allowing to just impact those product types that use the
 * variation F.
 *
 * Located at woocommerce\includes\wc-template-functions.php.
 *
 * @param  string $html dropdowns html, to be replaced with buttons.
 * @param  array $args options collected for the variations.
 * @return void outputting through echo
 */
function wbv_button_variations_attribute_options_html($html, $args)
{
    global $product;

    if (wbv_is_product_enabled($product->id)) {
        //Display the buttons
        wbv_display_buttons($args);

    } else {
        return $html;
    }
}

/**
 * add styles and scripts
 * @return void
 */
function wbv_register_frontend_scripts_styles()
{
    $suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG == 'true') ? '' : '.min';

    $stylesheet = get_stylesheet_directory() . '/' . WC()->template_path() . 'woocommerce-button-variations/wc-ea-css.css';

    //For theming purposes.
    //If the file exists in the theme, use it
    //(/themes/themeXYZ/woocommerce/woocommerce-button-variations/wc-ea-css.css)
    //or fallback to plugin's own css.
    if (file_exists($stylesheet)) {
        $url = get_stylesheet_directory_uri() . '/' . WC()->template_path() . 'woocommerce-button-variations/wc-ea-css.css';
    } else {
        $url = WBV_PLUGIN_DIR . 'css/wc-ea-css.css';
    }

    wp_register_style('wc-ea-css', $url);

    wp_register_script(
        'wc-ea-add-to-cart-variation', WBV_PLUGIN_DIR . 'js/wc-ea-add-to-cart-variation' . $suffix . '.js', array('jquery', 'wp-util'), WBV_VERSION
    );
}

function wbv_init()
{
    global $product, $wc_ea_styling_settings, $wc_ea_image_settings;

    // Verify if not disabled for this product.
    if (wbv_is_product_enabled($product->id)) {

        // Enqueue general button styles
        wp_enqueue_style('wc-ea-css');

        // Add button styling customized css
        // (from Styling tab settings).
        $css = '';
        if ($styling_settings = get_option($wc_ea_styling_settings)) {
            $css = wbv_styling($styling_settings);
        }

        // Styling for image buttons.
        $image_settings = get_option($wc_ea_image_settings);
        $img_css        = wbv_images_styling($image_settings);
        wp_add_inline_style('wc-ea-css', $css . $img_css);

        // Deregister WCs own variations' js
        wp_deregister_script('wc-add-to-cart-variation');
        // Enqueue plugin's js
        wp_enqueue_script('wc-ea-add-to-cart-variation');

        //Add necessary javascript variables
        if (wp_script_is('wc-ea-add-to-cart-variation')) {
            //Dependency on WC template.
            wc_get_template('single-product/add-to-cart/variation.php');
            wp_localize_script('wc-ea-add-to-cart-variation', 'wc_add_to_cart_variation_params', apply_filters('wc_add_to_cart_variation_params', array(
                'i18n_no_matching_variations_text' => esc_attr__('Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce'),
                'i18n_unavailable_text'            => esc_attr__('Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce'),
                'i18n_make_a_selection_text'       => esc_attr__('Select product options before adding this product to your cart.', 'woocommerce'),
            )));
        }
    }
}

function wbv_is_product_enabled($id)
{
    global $wc_ea_general_settings;
    // Verify if not disabled for this product.
    $general_settings = get_option($wc_ea_general_settings);
    $product_ids      = array_filter(array_map('absint', explode(',', $general_settings['product_ids'])));
    $found            = false;

    if (
        isset($general_settings['product_ids']) &&
        !empty($general_settings['product_ids']) &&
        false !== array_search($id, $product_ids)
    ) {
        $found = true;
    } else {
        if (isset($general_settings['category_ids'])) {
            $product_categories = get_the_terms($id, 'product_cat');
            if ($product_categories && !is_wp_error($product_categories)) {
                foreach ($product_categories as $category) {
                    if (false !== array_search($category->term_id, $general_settings['category_ids'])) {
                        $found = true;
                        break;
                    }
                }
            }

        }
    }

    if ($general_settings['switcher'] === '0') {
        //Enabled for all except.
        return !$found;

    } else {
        //Disabled for all except.
        return $found;
    }

}

function wbv_is_category_managed($id)
{
    global $wc_ea_general_settings;
    $general_settings = get_option($wc_ea_general_settings);
    $found = false;
    if (isset($general_settings['category_ids'])) {
        $product_categories = get_the_terms($id, 'product_cat');
        if ($product_categories && !is_wp_error($product_categories)) {
            foreach ($product_categories as $category) {
                if (false !== array_search($category->term_id, $general_settings['category_ids'])) {
                    $found = true;
                    break;
                }
            }
        }
    } 
    return $found;
}

function wbv_manage_enabling_product($post_id, $action = "disable")
{
    global $wc_ea_general_settings;
    $general_settings = get_option($wc_ea_general_settings);

    if ($action === 'disable') {

        if ($general_settings['switcher'] === '0') {
            //Enabled for all except.
            //If Enabled for all except, then add to product_ids.

            wbv_handle_id($post_id);
        } else {
            //Disabled for all except.
            //If Disabled for all except, we remove from product_ids
            wbv_handle_id($post_id, 'remove');
        }
    } else {

        if ($general_settings['switcher'] === '0') {
            //Enabled for all except.
            //If Enabled for all except, remove from product_ids
            wbv_handle_id($post_id, 'remove');
        } else {

            //Disabled for all except.
            //If Disabled for all except, add to product_ids
            wbv_handle_id($post_id);
        }
    }
}

function wbv_handle_id($post_id, $action = 'add')
{
    global $wc_ea_general_settings;
    $general_settings = get_option($wc_ea_general_settings);

    if (isset($general_settings['product_ids'])) {
        $product_ids = array_filter(array_map('absint', explode(',', $general_settings['product_ids'])));
    } else {
        $product_ids = array();
    }

    $key = array_search($post_id, $product_ids);
    if (false !== $key) {
        unset($product_ids[$key]);
    }
    if ($action === 'add') {
        $product_ids[] = $post_id;
    }
    $general_settings['product_ids'] = implode(',', $product_ids);
    update_option($wc_ea_general_settings, $general_settings);
}
