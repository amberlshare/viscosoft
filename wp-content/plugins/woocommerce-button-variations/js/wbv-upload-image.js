jQuery( function( $ ){

  window.wc_ea_meta_boxes_product_variations_media = {

    /**
     * wp.media frame object
     *
     * @type {Object}
     */
    variable_image_frame: null,

    /**
     * Variation image ID
     *
     * @type {Int}
     */
    setting_variation_image_id: null,

    /**
     * Variation image object
     *
     * @type {Object}
     */
    setting_variation_image: null,

    /**
     * wp.media post ID
     *
     * @type {Int}
     */
    wp_media_post_id: wp.media.model.settings.post.id,

    /**
     * Initialize media actions
     */
    init: function() {
      $( '.wc_ea_term_controls' ).on( 'click', '.upload_image_button', this.add_image );
      //$( 'a.add_media' ).on( 'click', this.restore_wp_media_post_id );
    },

    /**
     * Added new image
     *
     * @param {Object} event
     */
    add_image: function( event ) {
      var $button = $( this ),
        post_id = $button.attr( 'rel' ),
        $parent = $button.closest( '.upload_image' );

      wc_ea_meta_boxes_product_variations_media.setting_variation_image    = $parent;
      wc_ea_meta_boxes_product_variations_media.setting_variation_image_id = post_id;

      event.preventDefault();

      if ( $button.is( '.remove' ) ) {

        $( '.upload_image_id', wc_ea_meta_boxes_product_variations_media.setting_variation_image ).val( '' ).change();
        wc_ea_meta_boxes_product_variations_media.setting_variation_image.find( 'img' ).eq( 0 ).attr( 'src', wc_ea_admin_settings.woocommerce_placeholder_img_src );
        wc_ea_meta_boxes_product_variations_media.setting_variation_image.find( '.upload_image_button' ).removeClass( 'remove' );

      } else {

        // If the media frame already exists, reopen it.  
        if ( wc_ea_meta_boxes_product_variations_media.variable_image_frame ) {
          wc_ea_meta_boxes_product_variations_media.variable_image_frame.uploader.uploader.param( 'post_id', wc_ea_meta_boxes_product_variations_media.setting_variation_image_id );
          wc_ea_meta_boxes_product_variations_media.variable_image_frame.open();
          return;
        } else {
          wp.media.model.settings.post.id = wc_ea_meta_boxes_product_variations_media.setting_variation_image_id;
        }

        // Create the media frame.
        wc_ea_meta_boxes_product_variations_media.variable_image_frame = wp.media.frames.variable_image = wp.media({
          // Set the title of the modal.
          title: wc_ea_admin_settings.i18n_choose_image,
          button: {
            text: wc_ea_admin_settings.i18n_set_image
          },
          states: [
            new wp.media.controller.Library({
              title: wc_ea_admin_settings.i18n_choose_image,
              filterable: 'all'
            })
          ]
        });

        // When an image is selected, run a callback.
        wc_ea_meta_boxes_product_variations_media.variable_image_frame.on( 'select', function () {

          var attachment = wc_ea_meta_boxes_product_variations_media.variable_image_frame.state().get( 'selection' ).first().toJSON(),
            url = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

          $( '.upload_image_id', wc_ea_meta_boxes_product_variations_media.setting_variation_image ).val( attachment.id ).change();
          wc_ea_meta_boxes_product_variations_media.setting_variation_image.find( '.upload_image_button' ).addClass( 'remove' );
          wc_ea_meta_boxes_product_variations_media.setting_variation_image.find( 'img' ).eq( 0 ).attr( 'src', url );

          wp.media.model.settings.post.id = wc_ea_meta_boxes_product_variations_media.wp_media_post_id;
        });

        // Finally, open the modal.
        wc_ea_meta_boxes_product_variations_media.variable_image_frame.open();
      }
    },

    /**
     * Restore wp.media post ID.
     */
    // restore_wp_media_post_id: function() {
    //   wp.media.model.settings.post.id = wc_ea_meta_boxes_product_variations_media.wp_media_post_id;
    // }
  };

  window.wc_ea_meta_boxes_product_variations_media.init();
});