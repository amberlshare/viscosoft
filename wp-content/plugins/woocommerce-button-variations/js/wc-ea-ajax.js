jQuery(function($) {
    // Save attributes and update variations
    $('#wc_ea_button_data').on('click', '.button.save_wc_ea_attributes', function() {
        $('#wc_ea_button_data').block({
            message: null,
            overlayCSS: {
                background: '#fff no-repeat center',
                opacity: 0.6
            }
        });
        var data = {
            post_id: wc_ea_admin_meta_boxes.post_id,
            data: $('#wc_ea_button_data').find('input, select, textarea').serialize(),
            action: 'wc_ea_save_attributes',
            security: wc_ea_admin_meta_boxes.wc_ea_save_attributes_nonce
        };
        $.post(wc_ea_admin_meta_boxes.ajax_url, data, function(response) {
            $('#wc_ea_button_data').unblock();
        });
    });
    $('#wc_ea_button_data').on('click', 'button.save_wc_ea_attributes_reload', function() {
        $('#wc_ea_button_data').block({
            message: null,
            overlayCSS: {
                background: '#fff no-repeat center',
                opacity: 0.6
            }
        });
        var this_page = window.location.toString();
        this_page = this_page.replace('post-new.php?', 'post.php?post=' + wc_ea_admin_meta_boxes.post_id + '&action=edit&');
        $('#wc_ea_button_data').load(this_page + ' #wc_ea_button_data_inner', function() {
            $('.color-picker').wpColorPicker();
            $('.wc-metaboxes-wrapper').find('.wc-metabox > table').hide();
            $('#wc_ea_button_data').unblock();
            window.wc_ea_meta_boxes_product_variations_media.init();
        });
    });
});