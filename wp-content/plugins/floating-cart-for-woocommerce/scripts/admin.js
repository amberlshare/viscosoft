
//Saves the options into DB
function bravowp_woo_floatingcart_savesettings()
{

	jQuery("#bwwoofc-admin-contentpane-control-settings-confirmmessage").hide();
	jQuery("#bwwoofc-admin-contentpane-control-settings-demomessage").hide();

	//general
	var param_show_on_pages =jQuery("#bwwoofc-admin-contentpane-control-settings-showonwhatpages").val();
	var param_general_defaultmode = jQuery("#bwwoofc-admin-contentpane-control-settings-defaultmode").val();

	//building object to send
	var dataToSend = new Object();

	dataToSend.show_on_pages = param_show_on_pages;
	dataToSend.general_defaultmode = param_general_defaultmode;

	jQuery.ajax
	(

		{
			url : bwwoofcvars.ajaxHandlerUrl,
			type : 'post',
			dataType: 'json',
			data :
			{
				action : 'bw_woofc_admin_savesettings',
				security : bwwoofcvars.ajaxNonce,
				data : JSON.stringify(dataToSend)
			},
			success : function( response )
			{

				if ( response == "demoonly")
				{
					jQuery("#bwwoofc-admin-contentpane-control-settings-demomessage").show();
				}
				else
				{
					jQuery("#bwwoofc-admin-contentpane-control-settings-confirmmessage").show();
				}


			}
		}
	);


}
