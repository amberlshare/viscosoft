/*!
 * Variations Plugin
 */
;
(function($, window, document, undefined) {
    $.fn.wc_variation_form = function() {
        var $form = this,
            $single_variation = $form.find('.single_variation'),
            $product = $form.closest('.product'),
            $product_id = parseInt($form.data('product_id'), 10),
            $product_variations = $form.data('product_variations'),
            $use_ajax = $product_variations === false,
            $xhr = false,
            $reset_variations = $form.find('.reset_variations'),
            template = wp.template('variation-template'),
            unavailable_template = wp.template('unavailable-variation-template'),
            $single_variation_wrap = $form.find('.single_variation_wrap');
        // Always visible since 2.5.0
        $single_variation_wrap.show();
        // Unbind any existing events
        $form.unbind('check_button_variations update_variation_values found_variation');
        $form.find('.reset_variations').unbind('click');
        $form.find('.variations .wc_ea_button').unbind('click');
        // Bind new events to form
        $form.on('click', '.reset_variations', function(event) {
                event.preventDefault();
                $form.find('.variations .wc_ea_container .selected').removeClass('selected');
                $form.trigger('clicked_button');
                $form.trigger('reset_data');
            }).on('click', '.variations .wc_ea_button', function(event) {
                // On clicking a variation button
                event.preventDefault();

                if ($(this).hasClass('disabled') || $(this).hasClass('selected')){
                    
                    $form.find('.variations .wc_ea_container .selected').removeClass('selected');
                    $form.trigger('clicked_button');
                    $form.trigger('reset_data');
                    $form.find('.variations .wc_ea_container .disabled').removeClass('disabled');
                } else {
                    $(this).closest('.wc_ea_container').find('.selected').removeClass('selected');
                    $(this).addClass('selected');
                    $form.trigger('clicked_button');
                }
            }).on('clicked_button', function(event) {

                $form.find('input[name=variation_id]').val('').change();
                $form.find('.wc-no-matching-variations').remove();
                if ($use_ajax) {
                    if ($xhr) {
                        $xhr.abort();
                    }
                    var all_attributes_chosen = true,
                        some_attributes_chosen = false,
                        data = {};
                    $form.find('.variations .wc_ea_container ').each(function() {
                        var attribute = $(this).data('attribute_name') || $(this).attr('name');
                        var value = $(this).find('.selected');
                        if (value.length === 0) {
                            all_attributes_chosen = false;
                        } else {
                            some_attributes_chosen = true;
                        }
                        //value, not attribute name (key)
                        data[attribute] = value.attr('name');
                    });
                    if (all_attributes_chosen) {
                        
                        data.product_id = $product_id;
                        data.custom_data = $form.data( 'custom_data' );

                        $form.block( {
                            message: null,
                            overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                            }
                        } );


                        $xhr = $.ajax({
                            url: wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_variation'),
                            type: 'POST',
                            data: data,
                            success: function(variation) {
                                if (variation) {
                                    $form.trigger('found_variation', [variation]);
                                } else {
                                    $form.trigger('reset_data');
                                    $form.find('.single_variation').after('<p class="wc-no-matching-variations woocommerce-info">' + wc_add_to_cart_variation_params.i18n_no_matching_variations_text + '</p>');
                                    $form.find('.wc-no-matching-variations').slideDown(200);
                                }
                            },
                            complete: function() {
                            $form.unblock();
                            }
                        });
                    } else {
                        $form.trigger('reset_data');
                    }
                    if (some_attributes_chosen) {
                        if ($reset_variations.css('visibility') === 'hidden') {
                            $reset_variations.css('visibility', 'visible').hide().fadeIn();
                        }
                    } else {
                        $reset_variations.css('visibility', 'hidden');
                    }
                } else {
                    //$form.trigger('woocommerce_variation_select_change');
                    $form.trigger('check_button_variations', [$(this).parent().attr('name'), $(this)]);
                }
                // added to get around variation image flicker issue
                $('.product.has-default-attributes > .images').fadeTo(200, 1);
                // Custom event for when variation selection has been changed
                $form.trigger('woocommerce_variation_has_changed');
            }).on('check_button_variations', function(event, exclude, origin) {
                if ($use_ajax) {
                    return;
                }
                var all_attributes_chosen = true,
                    some_attributes_chosen = false,
                    current_settings = {},
                    $form = $(this),
                    $reset_variations = $form.find('.reset_variations');
                $form.find('.variations .wc_ea_container ').each(function() {
                    var attribute = $(this).attr('name');
                    var value = $(this).find('.selected');
                    if (value.length > 0) {
                        some_attributes_chosen = true;
                        current_settings[attribute] = value.attr('name');
                        $form.find('input[name=' + attribute + ']').val(value.attr('name')).change();
                    } else {
                        all_attributes_chosen = false;
                    }
                });
                
                var matching_variations = wc_variation_form_matcher.find_matching_variations($product_variations, current_settings);
                
                if (all_attributes_chosen) {
                    var variation = matching_variations.shift();

                    if (variation) {
                        // Found - set ID
                        $form.find('input[name=variation_id]').val(variation.variation_id).change();
                        $form.trigger('found_variation', [variation]);
                    } else {
                        // Nothing found - reset fields
                        $form.trigger('reset_data');
                        origin.click();
                        window.alert(wc_add_to_cart_variation_params.i18n_no_matching_variations_text);
                    }
                } else {
                    
                    $form.trigger('update_button_variation_values', [matching_variations]);
                    if (!variation) {
                        // Nothing found - reset fields
                        $form.trigger('reset_data');
                        origin.click();
                    }
                    if (!exclude) {
                        $single_variation.slideUp(200).trigger('hide_variation');
                    }
                }
                if (some_attributes_chosen) {
                    if ($reset_variations.css('visibility') === 'hidden') {
                        $reset_variations.css('visibility', 'visible').hide().fadeIn();
                    }
                } else {
                    $reset_variations.css('visibility', 'hidden');
                }
            }).on('hide_variation', function(event) {
                // When the variation is hidden
                event.preventDefault();
                $form.find('.single_add_to_cart_button').attr('disabled', 'disabled').attr('title', wc_add_to_cart_variation_params.i18n_make_a_selection_text);
            }).on('show_variation', function(event, variation, purchasable) {
                // When the variation is revealed
                event.preventDefault();
                $single_variation.show();
                if (purchasable) {
                    $form.find('.single_add_to_cart_button').removeAttr('disabled').removeAttr('title');
                } else {
                    $form.find('.single_add_to_cart_button').attr('disabled', 'disabled').attr('title', wc_add_to_cart_variation_params.i18n_unavailable_text);
                }
            }).on('reset_data', function(element) {
                // Reset product data
                $('.sku').wc_reset_content();
                $('.product_weight').wc_reset_content();
                $('.product_dimensions').wc_reset_content();
                $form.trigger('reset_image');
                $single_variation.slideUp(200).trigger('hide_variation');
            }).on('reset_image', function(event) {
                // Reset product image
                $form.wc_variations_image_update(false);
            }).on('update_button_variation_values', function(event, variations) {
                if ($use_ajax) {
                    return;
                }

                // Loop through selects and disable/enable options based on selections
                $form.find('.variations .wc_ea_container').each(function(index, el) {

                    var current_attr_name, current_attr_select = $(el);

                    // Reset options
                    if (!current_attr_select.data('attribute_options')) {
                        current_attr_select.data('attribute_options', current_attr_select.find('.wc_ea_button_wrapper').get());
                    }

                    current_attr_select.find('.wc_ea_button_wrapper').remove();
                    current_attr_select.append(current_attr_select.data('attribute_options'));
                    current_attr_select.find('.wc_ea_button').removeClass('attached');
                    current_attr_select.find('.wc_ea_button').removeClass('enabled');
                    current_attr_select.find('.wc_ea_button').removeClass('disabled');
                    current_attr_select.find('.wc_ea_button').removeAttr('disabled');

                    // Get name from data-attribute_name, or from input name if it doesn't exist
                    if (typeof(current_attr_select.data('attribute_name')) !== 'undefined') {
                        current_attr_name = current_attr_select.data('attribute_name');
                    } else {
                        current_attr_name = current_attr_select.attr('name');
                    }
                    

                    // Loop through variations
                    for (var num in variations) {
                        
                        if (typeof(variations[num]) !== 'undefined') {

                            var attributes = variations[num].attributes;
                            
                            for (var attr_name in attributes) {
                                if (attributes.hasOwnProperty(attr_name)) {
                                    
                                    var attr_val = attributes[attr_name];

                                    if (attr_name === current_attr_name) {

                                        var variation_active = '';

                                        if (variations[num].variation_is_active) {
                                            variation_active = 'enabled';
                                        }

                                        if (!variations[num].is_in_stock){
                                            variation_active += ' out-of-stock';
                                        }

                                        if (attr_val) {

                                            // Decode entities
                                            attr_val = $('<div/>').html(attr_val).text();

                                            // Add slashes
                                            attr_val = attr_val.replace(/'/g, '\\\'');
                                            attr_val = attr_val.replace(/"/g, '\\\"');

                                            // Compare the meerkat
                                            current_attr_select.find('.wc_ea_button[name="' + attr_val + '"]').addClass('attached ' + variation_active);

                                        } else {

                                            current_attr_select.find('.wc_ea_button').addClass('attached ' + variation_active);
                                        }
                                    }
                                }
                            }
                        }
                    }

                // Add disabled class to unattached
                current_attr_select.find( '.wc_ea_button:not(.attached)' ).addClass('disabled');

                // Grey out disabled
                //current_attr_select.find( '.wc_ea_button:not(.enabled)' ).attr( 'disabled', 'disabled' );

                });
                // Custom event for when variations have been updated
                $form.trigger('woocommerce_update_variation_values');
            }).on('found_variation', function(event, variation) {
                // Show single variation details (price, stock, image)
                var $variation_form = $(this),
                    $sku = $product.find('.product_meta').find('.sku'),
                    $weight = $product.find('.product_weight'),
                    $dimensions = $product.find('.product_dimensions'),
                    $qty = $single_variation_wrap.find('.quantity'),
                    purchasable = true;
                $variation_form.find('.variations_button').show();
                $variation_form.find('.single_variation').html(variation.price_html + variation.availability_html);
                if (variation.sku) {
                    $sku.wc_set_content(variation.sku);
                } else {
                    $sku.wc_reset_content();
                }
                // Display weight
                if (variation.weight) {
                    $weight.wc_set_content(variation.weight);
                } else {
                    $weight.wc_reset_content();
                }
                // Display dimensions
                if (variation.dimensions) {
                    $dimensions.wc_set_content(variation.dimensions);
                } else {
                    $dimensions.wc_reset_content();
                }
                // Show images
                $form.wc_variations_image_update(variation);
                // Output correct templates
                var $template_html = '';
                if (!variation.variation_is_visible) {
                    $template_html = unavailable_template();
                    // w3 total cache inline minification adds CDATA tags around our HTML (sigh)
                    $template_html = $template_html.replace('/*<![CDATA[*/', '');
                    $template_html = $template_html.replace('/*]]>*/', '');
                    $single_variation.html($template_html);
                    $form.find('input[name="variation_id"], input.variation_id').val('').change();
                } else {
                    $template_html = template({
                        variation: variation
                    });
                    // w3 total cache inline minification adds CDATA tags around our HTML (sigh)
                    $template_html = $template_html.replace('/*<![CDATA[*/', '');
                    $template_html = $template_html.replace('/*]]>*/', '');
                    $single_variation.html($template_html);
                    $form.find('input[name="variation_id"], input.variation_id').val(variation.variation_id).change();
                }
                // Hide or show qty input
                if (variation.is_sold_individually === 'yes') {
                    $qty.find('input.qty').val('1').attr('min', '1').attr('max', '');
                    $qty.hide();
                } else {
                    $qty.find('input.qty').attr('min', variation.min_qty).attr('max', variation.max_qty);
                    $qty.show();
                }
                // Enable or disable the add to cart button
                if (!variation.is_purchasable || !variation.is_in_stock || !variation.variation_is_visible) {
                    purchasable = false;
                }
                // Reveal
                if ($.trim($single_variation.text())) {
                    $single_variation.slideDown(200).trigger('show_variation', [variation, purchasable]);
                } else {
                    $single_variation.show().trigger('show_variation', [variation, purchasable]);
                }
            });
        //$form.trigger('wc_variation_form');
        return $form;
    };
    /**
     * Matches inline variation objects to chosen attributes
     * @type {Object}
     */
    var wc_variation_form_matcher = {
        find_matching_variations: function(product_variations, settings) {
            var matching = [];
            for (var i = 0; i < product_variations.length; i++) {
                var variation = product_variations[i];
                if (wc_variation_form_matcher.variations_match(variation.attributes, settings)) {
                    matching.push(variation);
                }
            }
            return matching;
        },
        variations_match: function(attrs1, attrs2) {
            var match = true;
            for (var attr_name in attrs1) {
                if (attrs1.hasOwnProperty(attr_name)) {
                    var val1 = attrs1[attr_name];
                    var val2 = attrs2[attr_name];
                    if (val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2) {
                        match = false;
                    }
                }
            }
            return match;
        }
    };
    /**
     * Stores the default text for an element so it can be reset later
     */
    $.fn.wc_set_content = function(content) {
        if (undefined === this.attr('data-o_content')) {
            this.attr('data-o_content', this.text());
        }
        this.text(content);
    };
    /**
     * Stores the default text for an element so it can be reset later
     */
    $.fn.wc_reset_content = function() {
        if (undefined !== this.attr('data-o_content')) {
            this.text(this.attr('data-o_content'));
        }
    };
    /**
     * Reset a default attribute for an element so it can be reset later
     */
    $.fn.wc_reset_variation_attr = function(attr) {
        if (undefined !== this.attr('data-o_' + attr)) {
            this.attr(attr, this.attr('data-o_' + attr));
        }
    };
    /**
     * Stores a default attribute for an element so it can be reset later
     */
    $.fn.wc_set_variation_attr = function(attr, value) {
        if (undefined === this.attr('data-o_' + attr)) {
            this.attr('data-o_' + attr, (!this.attr(attr)) ? '' : this.attr(attr));
        }
        this.attr(attr, value);
    };
    /**
     * Sets product images for the chosen variation
     */
    $.fn.wc_variations_image_update = function(variation) {
        var $form = this,
            $product = $form.closest('.product'),
            $product_img = $product.find('div.images img:eq(0)'),
            $product_link = $product.find('div.images a.zoom:eq(0)');
        if (variation && variation.image_src && variation.image_src.length > 1) {
            $product_img.wc_set_variation_attr('src', variation.image_src);
            $product_img.wc_set_variation_attr('title', variation.image_title);
            $product_img.wc_set_variation_attr('alt', variation.image_title);
            $product_img.wc_set_variation_attr('srcset', variation.image_srcset);
            $product_img.wc_set_variation_attr('sizes', variation.image_sizes);
            $product_link.wc_set_variation_attr('href', variation.image_link);
            $product_link.wc_set_variation_attr('title', variation.image_caption);
        } else {
            $product_img.wc_reset_variation_attr('src');
            $product_img.wc_reset_variation_attr('title');
            $product_img.wc_reset_variation_attr('alt');
            $product_img.wc_reset_variation_attr('srcset');
            $product_img.wc_reset_variation_attr('sizes');
            $product_link.wc_reset_variation_attr('href');
            $product_link.wc_reset_variation_attr('title');
        }
    };
    $(function() {
        // wc_add_to_cart_variation_params is required to continue, ensure the object exists
        if (typeof wc_add_to_cart_variation_params === 'undefined') return false;
        $('.variations_form').wc_variation_form().trigger('clicked_button');
    });
})(jQuery, window, document);