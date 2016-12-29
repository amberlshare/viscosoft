<?php


add_action( 'wp_ajax_bw_woofc_admin_savesettings', 'bw_woofc_admin_savesettings' );

function bw_woofc_admin_savesettings()
{

        bw_woofc_systemlog_addentry("FUNCTION","bw_woofc_admin_savesettings","Start");

	try
	{

		if (bw_woofc_demo_is_active() == 1)
		{
                        wp_send_json("demoonly");
		}

                bw_woofc_savesettings( stripslashes( $_POST['data'] ) );

                bw_woofc_systemlog_addentry("FUNCTION","ajax_admin_settings_save","End");

                wp_send_json("ok");



	}

	catch (Exception $e)
	{
		bw_woofc_systemlog_addentry("ERROR", "bw_woofc_admin_savesettings", $e->getMessage());
	}

	bw_woofc_systemlog_addentry("FUNCTION","bw_woofc_admin_savesettings","End");


}

?>
