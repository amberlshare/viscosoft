<?php

add_action( 'wp_ajax_bw_woofc_admin_savesettings_proversion', 'bw_woofc_admin_savesettings_proversion' );

function bw_woofc_admin_savesettings_proversion()
{

        bw_woofc_systemlog_addentry("FUNCTION","bw_woofc_admin_savesettings_proversion","Start");

        try
        {

                if (bw_woofc_demo_is_active() == 1)
		{
			wp_send_json("demoonly");
		}

                bw_woofc_savesettings_proversion( stripslashes( $_POST['data'] ) );

                bw_woofc_systemlog_addentry("FUNCTION","bw_woofc_admin_savesettings_proversion","End");

                wp_send_json("okproversion");

        }

        catch (Exception $e)
        {
                bw_woofc_systemlog_addentry("ERROR", "bw_woofc_admin_savesettings_proversion", $e->getMessage());
        }

        bw_woofc_systemlog_addentry("FUNCTION","bw_woofc_admin_savesettings_proversion","End");


}

?>
