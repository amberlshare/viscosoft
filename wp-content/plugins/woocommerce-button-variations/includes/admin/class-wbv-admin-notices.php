<?php
/**
 * Display notices in admin
 *
 * @author      CandleStudio
 * @category    Admin
 * @package     ButtonVariations\Admin
 * @version     1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WC_Admin_Notices Class.
 */
class WBV_Admin_Notices {
        /**
     *
     * Display the admin notice after saving settings.
     *
     * @return void
     */
    public static function show_admin_notice()
    {
        ?>
        <div class="updated" style="margin-top:10px;">
            <p><?php echo __('Your settings have been saved.', 'wc-ea-domain'); ?></p>
<?php       if (isset($_GET['act'])) { 
                global $wc_ea_attributes_settings 
?>
            <a style="display: inline-block; padding-bottom: 1em;" href="<?php echo admin_url('edit.php?post_type=product&page=wc_ea_plugin_options&tab=' . $wc_ea_attributes_settings); ?>"><?php _e('← Back to Attributes', 'wc-ea-domain');?></a>
<?php       
            } 
?>
        </div>
        <?php
    }
}
