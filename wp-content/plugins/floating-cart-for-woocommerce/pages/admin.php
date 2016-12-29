<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//checking if pro version
$proversion = 0;
if (bw_woofc_globals_isproversion())
{

        $proversion = 1;


}
else
{

        //reading settings
        $settings = bw_woofc_readsettings();

}

?>


<div class="bw-woofc container bw-woofc-admin-container">

        <div class="bw-woofc-admin-topbar">

                <img src="<?php echo bw_woofc_globals_plugin_url . "/images/banner.png"; ?>" style="height:25px;margin-top: 8px;">

        </div>

        <div class="row">

                <div class="col-md-12">

                        <div class="bw-woofc-admin-contentpane">

                                <!-- Title white bar -->
                                <div class="row">

                                        <div class="col-md-12">

                                                <div class="bw-woofc-admin-contentpane-page-header">

                                                        <?php
                                                        if ( $proversion == 0)
                                                        {
                                                                ?>
                                                                <div class="bw-woofc-admin-contentpane-page-header-title"><?php _e("WooCommerce Floating Cart Settings (Basic Version)", "bw-woofc"); ?></div>
                                                                <?php
                                                        }
                                                        else
                                                        {
                                                                ?>
                                                                <div class="bw-woofc-admin-contentpane-page-header-title"><?php _e("WooCommerce Floating Cart Settings (PRO Version)", "bw-woofc"); ?></div>
                                                                <?php
                                                        }
                                                        ?>

                                                </div>

                                        </div>

                                        <?php

                                        if ( $proversion == 0)
                                        {
                                                ?>

                                                <div class="col-md-6" style="padding-right:0px !important;">

                                                        <div class="bw-woofc-admin-contentpane-page-wrapper" style="padding-right:5px !important;">

                                                                <div class="bw-woofc-admin-contentpane-panel bw-woofc-admin-contentpane-panel-default" >

                                                                        <div class="bw-woofc-admin-contentpane-panel-title">
                                                                                <?php _e("Basic Settings", "bw-woofc"); ?>
                                                                        </div>

                                                                        <form class="form-horizontal">

                                                                                <div class="form-group bw-woofc-admin-settings-group">
                                                                                        <label class="col-sm-4 control-label bw-woofc-admin-setting-label"><?php _e("Show on what page(s):", "bw-woofc"); ?></label>
                                                                                        <div class="col-sm-8">
                                                                                                <select class="form-control form-control-force-auto-width" id="bwwoofc-admin-contentpane-control-settings-showonwhatpages" style="width:auto;" >
                                                                                                        <option value="allpages" <?php if ( $settings['show_on_pages'] == "allpages" ) { echo "selected"; } ?>><?php _e("Show on all pages", "bw-woofc"); ?></option>
                                                                                                        <option value="onlyshoppage"<?php if ( $settings['show_on_pages'] == "onlyshoppage" ) { echo "selected"; } ?>><?php _e("Show only in Shop page", "bw-woofc"); ?></option>
                                                                                                </select>
                                                                                                <span class="bw-woofc-admin-setting-helplabel"><?php _e("Sets the pages where the floating cart will be displayed. Note that the floating cart cannot be rendered on the 'Cart' and 'Checkout' page of WooCommerce. ", "bw-woofc"); ?></span>
                                                                                        </div>
                                                                                        <div class="clear"></div>
                                                                                </div>

                                                                                <div class="form-group bw-woofc-admin-settings-group">
                                                                                        <label class="col-sm-4 control-label bw-woofc-admin-setting-label"><?php _e("Default Mode:", "bw-woofc"); ?></label>
                                                                                        <div class="col-sm-8">
                                                                                                <select class="form-control form-control-force-auto-width" id="bwwoofc-admin-contentpane-control-settings-defaultmode" style="width:auto;" >
                                                                                                        <option value="extended" <?php if ( $settings['general_defaultmode'] == "extended" ) { echo "selected"; } ?>><?php _e("Extended", "bw-woofc"); ?></option>
                                                                                                        <option value="collapsed"<?php if ( $settings['general_defaultmode'] == "collapsed" ) { echo "selected"; } ?>><?php _e("Collapsed", "bw-woofc"); ?></option>
                                                                                                </select>
                                                                                                <span class="bw-woofc-admin-setting-helplabel"><?php _e("Sets the default rendering mode of the floating cart. Users can change the mode by clicking on the cart title bar.", "bw-woofc"); ?></span>
                                                                                        </div>
                                                                                        <div class="clear"></div>
                                                                                </div>

                                                                                <div class="form-group bw-woofc-admin-settings-group">
                                                                                        <label class="col-sm-4 control-label bw-woofc-admin-setting-label"><?php _e("Customize Cart:", "bw-woofc"); ?></label>
                                                                                        <div class="col-sm-8">
                                                                                                <span style="margin-top:0px;" class="bw-woofc-admin-setting-helplabel"><?php _e("You can customize the colors or the elements of the floating cart by manually edit the file 'woocommerce_cart.php' in the folder 'helpers' of the plugin. Alternatively, upgrade to the PRO version to use the composer utility to configure your floating Cart.", "bw-woofc"); ?></span>
                                                                                        </div>
                                                                                        <div class="clear"></div>
                                                                                </div>

                                                                                <div class="form-group">
                                                                                        <div class="col-sm-4" >
                                                                                        </div>
                                                                                        <div class="col-sm-8" >
                                                                                                <a class="btn btn-success" onclick="bravowp_woo_floatingcart_savesettings();"><i class="fa fa-floppy-o" aria-hidden="true"></i><?php _e("Save Basic Settings", "bw-woofc"); ?></a>
                                                                                        </div>
                                                                                        <div class="clear"></div>
                                                                                        <div class="col-sm-12" >
                                                                                                <div id="bwwoofc-admin-contentpane-control-settings-confirmmessage" class="alert alert-success" style="margin-top:15px;text-align:center;display:none;"><?php _e("Settings have been updated.", "bw-woofc"); ?></div>
												<div id="bwwoofc-admin-contentpane-control-settings-demomessage" class="alert alert-warning" style="margin-top:15px;text-align:center;display:none;"><?php _e("Settings will not be saved in Online Demo. Thank you!", "bw-woofc"); ?></div>
                                                                                        </div>
                                                                                        <div class="clear"></div>
                                                                                </div>

                                                                        </form>

                                                                </div>

                                                        </div>

                                                </div>

                                                <div class="col-md-6" style="padding-left:0px !important;">

                                                        <div class="bw-woofc-admin-contentpane-page-wrapper" style="padding-left:5px !important;">

                                                                <div class="bw-woofc-admin-contentpane-panel bw-woofc-admin-contentpane-panel-default" >

                                                                        <div class="bw-woofc-admin-contentpane-panel-title">
                                                                                <?php _e("Floating Cart Professional Version", "bw-woofc"); ?>
                                                                        </div>

                                                                        <div style="border:1px solid #eeeeee;padding:20px;text-align:center;background-color: #f0f7fb;">
                                                                                <?php _e("Do you need a faster and easier way to customize your floating cart?", "bw-woofc"); ?>
                                                                                <br><br>
                                                                                <?php _e("Not really comfortable with editing PHP & CSS?", "bw-woofc"); ?>
                                                                                <br><br>
                                                                                Checkout the Professional version of Flaoting Cart for WooCommerce:<br><br>
                                                                                <a target="new" class="btn btn-primary" href="http://www.bravowp.com/woocommerce-floating-cart-professional-live-demo/">Professional Floating Cart</a><br>
                                                                                <?php _e("(Live Demo!)", "bw-woofc"); ?>
                                                                        </div>

                                                                </div>

                                                        </div>

                                                </div>

                                        </div>

                                        <div class="clear"></div>

                                        <?php
				 }
                                 else
                                 {
                                         bw_woofc_inject_prosettingspage();
                                 }
                                 ?>



                        </div>
                        <!-- Title white bar -->

                        <div class="clear"></div>

                </div>

        </div>

</div>

<div class="clear"></div>

<div id="bw-woofc-admin-footer">

        <div class="row">

                <div class="col-md-12">

                        <?php _e("BravoWP WooCommerce Floating Cart - Documentation & Support: <a href='http://www.bravowp.com' target='_blank'>www.bravowp.com</a>", "bw-woofc"); ?>

                </div>

                <div class="clear"></div>

        </div>

</div>

</div>
