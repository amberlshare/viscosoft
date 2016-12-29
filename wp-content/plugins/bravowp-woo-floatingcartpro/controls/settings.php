<?php

//reading settings
$settings = bw_woofc_readsettings_proversion();

?>


<div class="col-md-12" >

        <div class="bw-woofc-admin-contentpane-page-wrapper" >

                <div class="row">

                        <div class="col-md-12">

                                <div class="bw-woofc-admin-contentpane-horizontalsubmenu">

                                        <ul class="menu" id="bw-woofc-admin-contentpane-horizontalsubmenu-settings">

                                                <li id="bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-general" class="bw-woofc-admin-contentpane-horizontalsubmenu-selecteditem" onclick="bw_woofc_admin_settings_menuclick('general');">
                                                        <a><i class="fa fa-cogs"></i> <?php _e("General", "bw-woofc"); ?></a>
                                                </li>
                                                <li id="bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-pages" onclick="bw_woofc_admin_settings_menuclick('pages');">
                                                        <a><i class="fa fa-list"></i> <?php _e("Pages", "bw-woofc"); ?></a>
                                                </li>
                                                <li id="bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-colors" onclick="bw_woofc_admin_settings_menuclick('colors');">
                                                        <a><i class="fa fa-file-image-o"></i> <?php _e("Colors", "bw-woofc"); ?></a>
                                                </li>
                                                <li id="bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-texts" onclick="bw_woofc_admin_settings_menuclick('texts');">
                                                        <a><i class="fa fa-italic"></i> <?php _e("Texts", "bw-woofc"); ?></a>
                                                </li>
                                                <li id="bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-buttons" onclick="bw_woofc_admin_settings_menuclick('buttons');">
                                                        <a><i class="fa fa-hand-pointer-o"></i> <?php _e("Buttons", "bw-woofc"); ?></a>
                                                </li>
                                                <li id="bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-about" onclick="bw_woofc_admin_settings_menuclick('about');">
                                                        <a><i class="fa fa-info-circle"></i> <?php _e("About", "bw-woofc"); ?></a>
                                                </li>

                                        </ul>

                                </div>

                        </div>

                        <div class="clear"></div>

                </div>



                <div id="bw-woofc-admin-contentpane-settings-panel-general" class="bw-woofc-admin-contentpane-panel bw-woofc-admin-contentpane-panel-default" >

                        <div class="bw-woofc-admin-contentpane-panel-title">
                                <?php _e("Basic Settings", "bw-woofc"); ?>
                        </div>

                        <form class="form-horizontal">

                                <div class="form-group bw-woofc-admin-settings-group">
                                        <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Cart Position:", "bw-woofc"); ?></label>
                                        <div class="col-sm-5">
                                                <select class="form-control form-control-force-auto-width" id="bwwoofc-admin-contentpane-control-settings-position" style="width:auto;" >
                                                        <option value="right"<?php if ( $settings['general_position'] == "right" ) { echo "selected"; } ?>><?php _e("Bottom Right", "bw-woofc"); ?></option>
                                                        <option value="left" <?php if ( $settings['general_position'] == "left" ) { echo "selected"; } ?>><?php _e("Bottom Left", "bw-woofc"); ?></option>
                                                </select>
                                        </div>
                                        <div class="col-sm-4">
                                                <span class="bw-woofc-admin-setting-helplabel"><?php _e("Sets the position of the page where to render the floating cart. ", "bravowp-helpdesk"); ?></span>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                                <div class="form-group bw-woofc-admin-settings-group">
                                        <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Default Rendering Mode:", "bw-woofc"); ?></label>
                                        <div class="col-sm-5">
                                                <select class="form-control form-control-force-auto-width" id="bwwoofc-admin-contentpane-control-settings-defaultmode" style="width:auto;" >
                                                        <option value="extended" <?php if ( $settings['general_defaultmode'] == "extended" ) { echo "selected"; } ?>><?php _e("Extended", "bw-woofc"); ?></option>
                                                        <option value="collapsed"<?php if ( $settings['general_defaultmode'] == "collapsed" ) { echo "selected"; } ?>><?php _e("Collapsed", "bw-woofc"); ?></option>
                                                </select>
                                        </div>
                                        <div class="col-sm-4">
                                                <span class="bw-woofc-admin-setting-helplabel"><?php _e("Sets the default rendering mode of the floating cart. Users can change the mode by clicking on the cart title bar.", "bw-woofc"); ?></span>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                        </form>

                </div>

                <div id="bw-woofc-admin-contentpane-settings-panel-pages" class="bw-woofc-admin-contentpane-panel bw-woofc-admin-contentpane-panel-default" >

                        <div class="bw-woofc-admin-contentpane-panel-title">
                                <?php _e("Pages Settings", "bw-woofc"); ?>
                        </div>

                        <form class="form-horizontal">

                                <?php

                                $pages = get_pages();

                                $pagesSettings =  $settings['pages'] ;

                                $pageIdCart = get_option( 'woocommerce_cart_page_id' );
                                $pageIdCheckout = get_option( 'woocommerce_checkout_page_id' );
                                $pageIdShop = get_option( 'woocommerce_shop_page_id' );

                                foreach ( $pages as $page )
                                {

                                        //reading setting
                                        $showThisPage = "yes"; //default
                                        if ($pagesSettings != null)
                                        {

                                                if ($pagesSettings != '{}')
                                                {
                                                        if ( array_key_exists( "page_bwwoofc-admin-contentpane-control-settings-pageshow-" . $page->ID, $pagesSettings ) == true)
                                                        {
                                                                if ($pagesSettings['page_bwwoofc-admin-contentpane-control-settings-pageshow-' . $page->ID] == 'no')
                                                                {
                                                                        $showThisPage = "no";
                                                                }
                                                        }

                                                        //always no for checkout and cart page
                                                        if ( $page->ID == $pageIdCart || $page->ID == $pageIdCheckout )
                                                        {
                                                                $showThisPage = "no";
                                                        }

                                                        //always no for shop page (must always be rendered in this page)
                                                        if ( $page->ID == $pageIdShop )
                                                        {
                                                                $showThisPage = "yes";
                                                        }

                                                }
                                        }

                                        ?>

                                        <div class="form-group bw-woofc-admin-settings-group">
                                                <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php echo $page->post_title ?></label>
                                                <div class="col-sm-5">
                                                        <select data-bwwoofc="<?php echo $page->ID; ?>" class="form-control form-control-force-auto-width" id="bwwoofc-admin-contentpane-control-settings-pageshow-<?php echo $page->ID; ?>" style="width:auto;" <?php if ( $page->ID == $pageIdCart || $page->ID == $pageIdCheckout || $page->ID == $pageIdShop ) { echo " disabled "; } ?> >
                                                                <option value="yes" <?php if ( $showThisPage == "yes" ) { echo "selected"; } ?>><?php _e("Show Cart", "bw-woofc"); ?></option>
                                                                <option value="no"<?php if ( $showThisPage == "no" ) { echo "selected"; } ?>><?php _e("Do Not Show Cart", "bw-woofc"); ?></option>
                                                        </select>
                                                </div>
                                                <div class="clear"></div>
                                        </div>

                                        <?php

                                }
                                ?>

                        </form>

                        <div class="bw-woofc-admin-contentpane-panel-title" style="margin-top:20px;">
                                <?php _e("Explicit URLs to Show or Hide Floating Cart", "bw-woofc"); ?>
                        </div>

                        <form class="form-horizontal">

                                <div class="form-group bw-woofc-admin-settings-group">
                                        <div class="col-sm-3">
                                                <label class="control-label bw-woofc-admin-setting-label"><?php _e("Show the Cart on these URLs", "bw-woofc"); ?></label>
                                                <span class="control-label bw-woofc-admin-setting-helplabel" style="text-align:left;"><?php _e("(Separate URLs in rows by using CTRL+ENTER)", "bw-woofc"); ?></span>
                                        </div>
                                        <div class="col-sm-9">
                                                <textarea id="bwwoofc-admin-contentpane-control-settings-pages-forceshowurl-text" style="width:100%;height:100px;"><?php echo( $settings['pages_showurls'] ); ?></textarea>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                                <div class="form-group bw-woofc-admin-settings-group">
                                        <div class="col-sm-3">
                                                <label class="control-label bw-woofc-admin-setting-label"><?php _e("Hide the Cart on these URLs", "bw-woofc"); ?></label>
                                                <span class="control-label bw-woofc-admin-setting-helplabel" style="text-align:left;"><?php _e("(Separate URLs in rows by using CTRL+ENTER)", "bw-woofc"); ?></span>
                                        </div>
                                        <div class="col-sm-9">
                                                <textarea id="bwwoofc-admin-contentpane-control-settings-pages-forcehideurl-text" style="width:100%;height:100px;"><?php echo( $settings['pages_hideurls'] ); ?></textarea>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                        </form>

                </div>

                <div id="bw-woofc-admin-contentpane-settings-panel-colors" class="bw-woofc-admin-contentpane-panel bw-woofc-admin-contentpane-panel-default" >

                        <div class="bw-woofc-admin-contentpane-panel-title">
                                <?php _e("Colors Settings", "bw-woofc"); ?>
                        </div>

                        <form class="form-horizontal">

                                <div class="form-group bw-woofc-admin-settings-group">
                                        <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Header colors:", "bw-woofc"); ?></label>
                                        <div class="col-sm-9">
                                                <div class="row">
                                                        <div class="col-md-3">
                                                                <div><?php _e("Main Text Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_header_text'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-header-text" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                                <div><?php _e("Subtotal Text Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_header_subtotaltext'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-header-subtotaltext" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                                <div><?php _e("Background Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_header_background'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-header-background" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                                <div class="form-group bw-woofc-admin-settings-group">
                                        <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Body colors:", "bw-woofc"); ?></label>
                                        <div class="col-sm-9">
                                                <div class="row">
                                                        <div class="col-md-3">
                                                                <div><?php _e("Text Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_body_text'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-body-text" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                                <div><?php _e("Background Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_body_background'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-body-background" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                                <div class="form-group bw-woofc-admin-settings-group">
                                        <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("SubTotal colors:", "bw-woofc"); ?></label>
                                        <div class="col-sm-9">
                                                <div class="row">
                                                        <div class="col-md-3">
                                                                <div><?php _e("Text Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_subtotal_text'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-subtotal-text" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                                <div><?php _e("Background Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_subtotal_background'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-subtotal-background" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                                <div class="form-group bw-woofc-admin-settings-group">
                                        <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("View Cart Button colors:", "bw-woofc"); ?></label>
                                        <div class="col-sm-9">
                                                <div class="row">
                                                        <div class="col-md-3">
                                                                <div><?php _e("Text Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_viewcart_text'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-viewcart-text" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                                <div><?php _e("Background Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_viewcart_background'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-viewcart-background" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                                <div class="form-group bw-woofc-admin-settings-group">
                                        <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Checkout Button colors:", "bw-woofc"); ?></label>
                                        <div class="col-sm-9">
                                                <div class="row">
                                                        <div class="col-md-3">
                                                                <div><?php _e("Text Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_checkout_text'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-checkout-text" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                                <div><?php _e("Background Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_checkout_background'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-checkout-background" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                                <div class="form-group bw-woofc-admin-settings-group">
                                        <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Footer colors:", "bw-woofc"); ?></label>
                                        <div class="col-sm-9">
                                                <div class="row">
                                                        <div class="col-md-3">
                                                                <div><?php _e("Background Color:", "bw-woofc"); ?></div>
                                                                <input value="<?php echo( $settings['colors_footer_background'] ); ?>" type='text' id="bwwoofc-admin-contentpane-control-settings-colors-footer-background" class="bw-woofc-colorpicker" />
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                        </div>

                        <div id="bw-woofc-admin-contentpane-settings-panel-texts" class="bw-woofc-admin-contentpane-panel bw-woofc-admin-contentpane-panel-default" >

                                <div class="bw-woofc-admin-contentpane-panel-title">
                                        <?php _e("Texts Settings", "bw-woofc"); ?>
                                </div>

                                <form class="form-horizontal">

                                        <div class="form-group bw-woofc-admin-settings-group">
                                                <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Header Main Text:", "bw-woofc"); ?></label>
                                                <div class="col-sm-3">
                                                        <input value="<?php echo( $settings['text_headermain'] ); ?>" id="bwwoofc-admin-contentpane-control-settings-text-headermaintext" type="text" class="form-control" />
                                                </div>
                                                <div class="col-sm-6">
                                                        <span class="bw-woofc-admin-setting-helplabel"><?php _e("Title of the floating cart. ", "bw-woofc"); ?></span>
                                                </div>
                                                <div class="clear"></div>
                                        </div>

                                        <div class="form-group bw-woofc-admin-settings-group">
                                                <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Header 'SubTotal':", "bw-woofc"); ?></label>
                                                <div class="col-sm-3">
                                                        <input value="<?php echo( $settings['text_headersubtotal'] ); ?>" id="bwwoofc-admin-contentpane-control-settings-text-headersubtotaltext" type="text" class="form-control" />
                                                </div>
                                                <div class="col-sm-6">
                                                        <span class="bw-woofc-admin-setting-helplabel"><?php _e("The 'SubTotal' label in the cart header. ", "bw-woofc"); ?></span>
                                                </div>
                                                <div class="clear"></div>
                                        </div>

                                        <div class="form-group bw-woofc-admin-settings-group">
                                                <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Body 'SubTotal':", "bw-woofc"); ?></label>
                                                <div class="col-sm-3">
                                                        <input value="<?php echo( $settings['text_bodysubtotal'] ); ?>" id="bwwoofc-admin-contentpane-control-settings-text-bodysubtotaltext" type="text" class="form-control" />
                                                </div>
                                                <div class="col-sm-6">
                                                        <span class="bw-woofc-admin-setting-helplabel"><?php _e("The 'SubTotal' label in the label under the items list. ", "bw-woofc"); ?></span>
                                                </div>
                                                <div class="clear"></div>
                                        </div>

                                        <div class="form-group bw-woofc-admin-settings-group">
                                                <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("'View Cart' button text:", "bw-woofc"); ?></label>
                                                <div class="col-sm-3">
                                                        <input value="<?php echo( $settings['text_viewcart'] ); ?>" id="bwwoofc-admin-contentpane-control-settings-text-viewcarttext" type="text" class="form-control" />
                                                </div>
                                                <div class="col-sm-6">
                                                        <span class="bw-woofc-admin-setting-helplabel"><?php _e("The text of the 'View Cart' button. ", "bw-woofc"); ?></span>
                                                </div>
                                                <div class="clear"></div>
                                        </div>

                                        <div class="form-group bw-woofc-admin-settings-group">
                                                <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("'Checkout' button text:", "bw-woofc"); ?></label>
                                                <div class="col-sm-3">
                                                        <input value="<?php echo( $settings['text_checkout'] ); ?>" id="bwwoofc-admin-contentpane-control-settings-text-checkouttext" type="text" class="form-control" />
                                                </div>
                                                <div class="col-sm-6">
                                                        <span class="bw-woofc-admin-setting-helplabel"><?php _e("The text of the 'Checkout' button. ", "bw-woofc"); ?></span>
                                                </div>
                                                <div class="clear"></div>
                                        </div>

                                        <div class="form-group bw-woofc-admin-settings-group">
                                                <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Button Text if the cart is Empty:", "bw-woofc"); ?></label>
                                                <div class="col-sm-3">
                                                        <input value="<?php echo( $settings['text_emptycart'] ); ?>" id="bwwoofc-admin-contentpane-control-settings-text-emptycarttext" type="text" class="form-control" />
                                                </div>
                                                <div class="col-sm-6">
                                                        <span class="bw-woofc-admin-setting-helplabel"><?php _e("Default: 'Start Shopping!' ", "bw-woofc"); ?></span>
                                                </div>
                                                <div class="clear"></div>
                                        </div>

                                        <div class="form-group bw-woofc-admin-settings-group">
                                                <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Cart Text if the cart is Empty:", "bw-woofc"); ?></label>
                                                <div class="col-sm-3">
                                                        <input value="<?php echo( $settings['text_emptycartbodytext'] ); ?>" id="bwwoofc-admin-contentpane-control-settings-text-emptycarttextbody" type="text" class="form-control" />
                                                </div>
                                                <div class="col-sm-6">
                                                        <span class="bw-woofc-admin-setting-helplabel"><?php _e("Default: 'Your Shopping Cart is empty' ", "bw-woofc"); ?></span>
                                                </div>
                                                <div class="clear"></div>
                                        </div>

                                        <div class="form-group bw-woofc-admin-settings-group">
                                                <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Item (singular):", "bw-woofc"); ?></label>
                                                <div class="col-sm-3">
                                                        <input value="<?php echo( $settings['text_item'] ); ?>" id="bwwoofc-admin-contentpane-control-settings-text-item" type="text" class="form-control" />
                                                </div>
                                                <div class="col-sm-6">
                                                        <span class="bw-woofc-admin-setting-helplabel"><?php _e("Default: 'item' ", "bw-woofc"); ?></span>
                                                </div>
                                                <div class="clear"></div>
                                        </div>

                                        <div class="form-group bw-woofc-admin-settings-group">
                                                <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Items (plural):", "bw-woofc"); ?></label>
                                                <div class="col-sm-3">
                                                        <input value="<?php echo( $settings['text_items'] ); ?>" id="bwwoofc-admin-contentpane-control-settings-text-items" type="text" class="form-control" />
                                                </div>
                                                <div class="col-sm-6">
                                                        <span class="bw-woofc-admin-setting-helplabel"><?php _e("Default: 'items' ", "bw-woofc"); ?></span>
                                                </div>
                                                <div class="clear"></div>
                                        </div>

                                        </form>

                                </div>

                                <div id="bw-woofc-admin-contentpane-settings-panel-buttons" class="bw-woofc-admin-contentpane-panel bw-woofc-admin-contentpane-panel-default" >

                                        <div class="bw-woofc-admin-contentpane-panel-title">
                                                <?php _e("Buttons Settings", "bw-woofc"); ?>
                                        </div>

                                        <form class="form-horizontal">

                                                <div class="form-group bw-woofc-admin-settings-group">
                                                        <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Show View Cart button:", "bw-woofc"); ?></label>
                                                        <div class="col-sm-5">
                                                                <select class="form-control form-control-force-auto-width" id="bwwoofc-admin-contentpane-control-settings-buttons-show-viewcart" style="width:auto;" >
                                                                        <option value="yes" <?php if ( $settings['button_viewcart_show'] == "yes" ) { echo "selected"; } ?>><?php _e("Yes", "bw-woofc"); ?></option>
                                                                        <option value="no"<?php if ( $settings['button_viewcart_show'] == "no" ) { echo "selected"; } ?>><?php _e("No", "bw-woofc"); ?></option>
                                                                </select>
                                                        </div>
                                                        <div class="col-sm-4">
                                                                <span class="bw-woofc-admin-setting-helplabel"><?php _e("Shows or hides the 'View Cart' button.", "bw-woofc"); ?></span>
                                                        </div>
                                                        <div class="clear"></div>
                                                </div>

                                                <div class="form-group bw-woofc-admin-settings-group">
                                                        <label class="col-sm-3 control-label bw-woofc-admin-setting-label"><?php _e("Show Checkout button:", "bw-woofc"); ?></label>
                                                        <div class="col-sm-5">
                                                                <select class="form-control form-control-force-auto-width" id="bwwoofc-admin-contentpane-control-settings-buttons-show-checkout" style="width:auto;" >
                                                                        <option value="yes" <?php if ( $settings['button_checkout_show'] == "yes" ) { echo "selected"; } ?>><?php _e("Yes", "bw-woofc"); ?></option>
                                                                        <option value="no"<?php if ( $settings['button_checkout_show'] == "no" ) { echo "selected"; } ?>><?php _e("No", "bw-woofc"); ?></option>
                                                                </select>
                                                        </div>
                                                        <div class="col-sm-4">
                                                                <span class="bw-woofc-admin-setting-helplabel"><?php _e("Shows or hides the 'Checkout' button.", "bw-woofc"); ?></span>
                                                        </div>
                                                        <div class="clear"></div>
                                                </div>

                                        </form>

                                </div>

                                <div id="bw-woofc-admin-contentpane-settings-panel-about" class="bw-woofc-admin-contentpane-panel bw-woofc-admin-contentpane-panel-default" >

                                        <div class="bw-woofc-admin-contentpane-panel-title">
                                                <?php _e("About this Plugin", "bw-woofc"); ?>
                                        </div>

                                        Find information and support on <a target="new" href="http://www.bravowp.com">www.bravowp.com</a>
                                        <br>
                                        Thank you!

                                </div>

                        </div>

                </div>

                <div class="col-md-12">

                        <div class="bw-woofc-admin-contentpane-page-wrapper" style="padding-top:0px;" >

                                <div id="bw-woofc-admin-contentpane-settings-panel-general" class="bw-woofc-admin-contentpane-panel bw-woofc-admin-contentpane-panel-default" >

                                        <div class="col-md-12" style="text-align:center;">
                                                <a class="btn btn-success" onclick="bw_woofc_admin_settings_save_proversion();"><i class="fa fa-floppy-o" aria-hidden="true"></i><?php _e("Save Settings", "bw-woofc"); ?></a>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="col-md-12" >
                                                <div id="bwwoofc-admin-contentpane-control-settings-confirmmessage" class="alert alert-success" style="margin-top:15px;text-align:center;display:none;margin-bottom: 0px !important;"><?php _e("Settings have been updated.", "bw-woofc"); ?></div>
                                                <div id="bwwoofc-admin-contentpane-control-settings-demomessage" class="alert alert-warning" style="margin-top:15px;text-align:center;display:none;"><?php _e("Settings will not be saved in Online Demo. Thank you!", "bw-woofc"); ?></div>
                                        </div>
                                        <div class="clear"></div>
                                </div>

                        </div>

                </div>
