<div class="wrap">

<?php
        if ($is_tab) {
            WBV_Admin_Settings::print_tabs();
        }

        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
            WBV_Admin_Notices::show_admin_notice();
        }

?>

        <form method="post" class="wc_ea_options_form" action="options.php">
<?php
        wp_nonce_field('update-options');
        
        settings_fields($tab);
        do_settings_sections($tab);

        if ($tab != $wc_ea_attributes_settings) {
            submit_button(__('Save changes', 'wc-ea-domain'));
        }
?>

        </form>
</div>
