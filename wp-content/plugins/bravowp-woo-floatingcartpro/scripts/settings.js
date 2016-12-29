//global bag for varaibles
var bwwoofc_globalbag = {};
bwwoofc_globalbag.zone_settings = 'general';

//Document load
jQuery(document).ready(function(){

	bw_woofc_admin_settings_menuclick("general");

	jQuery(".bw-woofc-colorpicker").spectrum( { showInput: true });

});

//Handles the clicks menu from settings sub menu
function bw_woofc_admin_settings_menuclick( menuKey )
{

	jQuery("#bwwoofc-admin-contentpane-control-settings-demomessage").hide();
	jQuery("#bwwoofc-admin-contentpane-control-settings-confirmmessage").hide();

	//sets the current zone
	bwwoofc_globalbag.zone_settings = menuKey;

	//remove the "active" status from the menu items
	jQuery("#bw-woofc-admin-contentpane-horizontalsubmenu-settings li").removeClass("bw-woofc-admin-contentpane-horizontalsubmenu-selecteditem");

	//hiding panels
	jQuery("#bw-woofc-admin-contentpane-settings-panel-general").hide();
	jQuery("#bw-woofc-admin-contentpane-settings-panel-pages").hide();
	jQuery("#bw-woofc-admin-contentpane-settings-panel-colors").hide();
	jQuery("#bw-woofc-admin-contentpane-settings-panel-texts").hide();
	jQuery("#bw-woofc-admin-contentpane-settings-panel-buttons").hide();
	jQuery("#bw-woofc-admin-contentpane-settings-panel-about").hide();

	//executes the default action after button click, if any
	if ( menuKey == "general")
	{

		jQuery("#bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-general").addClass("bw-woofc-admin-contentpane-horizontalsubmenu-selecteditem");
		jQuery("#bw-woofc-admin-contentpane-settings-panel-general").show();

	}
	if ( menuKey == "pages")
	{

		jQuery("#bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-pages").addClass("bw-woofc-admin-contentpane-horizontalsubmenu-selecteditem");
		jQuery("#bw-woofc-admin-contentpane-settings-panel-pages").show();

	}
	if ( menuKey == "colors")
	{

		jQuery("#bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-colors").addClass("bw-woofc-admin-contentpane-horizontalsubmenu-selecteditem");
		jQuery("#bw-woofc-admin-contentpane-settings-panel-colors").show();

	}
	if ( menuKey == "texts")
	{

		jQuery("#bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-texts").addClass("bw-woofc-admin-contentpane-horizontalsubmenu-selecteditem");
		jQuery("#bw-woofc-admin-contentpane-settings-panel-texts").show();

	}
	if ( menuKey == "buttons")
	{

		jQuery("#bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-buttons").addClass("bw-woofc-admin-contentpane-horizontalsubmenu-selecteditem");
		jQuery("#bw-woofc-admin-contentpane-settings-panel-buttons").show();

	}
	if ( menuKey == "about")
	{

		jQuery("#bw-woofc-admin-contentpane-horizontalsubmenu-settings-li-about").addClass("bw-woofc-admin-contentpane-horizontalsubmenu-selecteditem");
		jQuery("#bw-woofc-admin-contentpane-settings-panel-about").show();

	}

}


function bw_woofc_admin_settings_save_proversion()
{

	jQuery("#bwwoofc-admin-contentpane-control-settings-demomessage").hide();
	jQuery("#bwwoofc-admin-contentpane-control-settings-confirmmessage").hide();

	//general
	var param_general_position = jQuery("#bwwoofc-admin-contentpane-control-settings-position").val();
	var param_general_defaultmode = jQuery("#bwwoofc-admin-contentpane-control-settings-defaultmode").val();

	//colors
	var param_colors_header_text = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-header-text").spectrum("get").toHexString();
	var param_colors_header_subtotaltext = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-header-subtotaltext").spectrum("get").toHexString();
	var param_colors_header_background = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-header-background").spectrum("get").toHexString();
	var param_colors_body_text = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-body-text").spectrum("get").toHexString();
	var param_colors_body_background = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-body-background").spectrum("get").toHexString();
	var param_colors_subtotal_text = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-subtotal-text").spectrum("get").toHexString();
	var param_colors_subtotal_background = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-subtotal-background").spectrum("get").toHexString();
	var param_colors_viewcart_text = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-viewcart-text").spectrum("get").toHexString();
	var param_colors_viewcart_background = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-viewcart-background").spectrum("get").toHexString();
	var param_colors_checkout_text = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-checkout-text").spectrum("get").toHexString();
	var param_colors_checkout_background = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-checkout-background").spectrum("get").toHexString();
	var param_colors_footer_background = jQuery("#bwwoofc-admin-contentpane-control-settings-colors-footer-background").spectrum("get").toHexString();

	//Text
	var param_text_headermain = jQuery("#bwwoofc-admin-contentpane-control-settings-text-headermaintext").val();
	var param_text_headersubtotal = jQuery("#bwwoofc-admin-contentpane-control-settings-text-headersubtotaltext").val();
	var param_text_bodysubtotal = jQuery("#bwwoofc-admin-contentpane-control-settings-text-bodysubtotaltext").val();
	var param_text_viewcart = jQuery("#bwwoofc-admin-contentpane-control-settings-text-viewcarttext").val();
	var param_text_checkout = jQuery("#bwwoofc-admin-contentpane-control-settings-text-checkouttext").val();
	var param_text_emptycart = jQuery("#bwwoofc-admin-contentpane-control-settings-text-emptycarttext").val();
	var param_text_emptycartbodytext = jQuery("#bwwoofc-admin-contentpane-control-settings-text-emptycarttextbody").val();
	var param_text_item = jQuery("#bwwoofc-admin-contentpane-control-settings-text-item").val();
	var param_text_items = jQuery("#bwwoofc-admin-contentpane-control-settings-text-items").val();

	//Buttons
	var param_button_viewcart_show = jQuery("#bwwoofc-admin-contentpane-control-settings-buttons-show-viewcart").val();
	var param_button_checkout_show = jQuery("#bwwoofc-admin-contentpane-control-settings-buttons-show-checkout").val();

	//pages
	var param_pages = {};
	jQuery("[data-bwwoofc]").each( function( index, value ) {
		var indexName = "page_" + value.id;
		var valueValue = jQuery("#" + value.id).val();
		param_pages[indexName] = valueValue;
	});
	var param_pages_showurls = jQuery("#bwwoofc-admin-contentpane-control-settings-pages-forceshowurl-text").val();
	var param_pages_hideurls = jQuery("#bwwoofc-admin-contentpane-control-settings-pages-forcehideurl-text").val();

	//building object to send
	var dataToSend = new Object();

	dataToSend.general_position = param_general_position;
	dataToSend.general_defaultmode = param_general_defaultmode;

	dataToSend.colors_header_text = param_colors_header_text;
	dataToSend.colors_header_subtotaltext = param_colors_header_subtotaltext;
	dataToSend.colors_header_background = param_colors_header_background;
	dataToSend.colors_body_text = param_colors_body_text;
	dataToSend.colors_body_background = param_colors_body_background;
	dataToSend.colors_subtotal_text = param_colors_subtotal_text;
	dataToSend.colors_subtotal_background = param_colors_subtotal_background;
	dataToSend.colors_viewcart_text = param_colors_viewcart_text;
	dataToSend.colors_viewcart_background= param_colors_viewcart_background;
	dataToSend.colors_checkout_text = param_colors_checkout_text;
	dataToSend.colors_checkout_background = param_colors_checkout_background;
	dataToSend.colors_footer_background= param_colors_footer_background;

	dataToSend.text_headermain = param_text_headermain;
	dataToSend.text_headersubtotal = param_text_headersubtotal;
	dataToSend.text_bodysubtotal = param_text_bodysubtotal;
	dataToSend.text_viewcart = param_text_viewcart;
	dataToSend.text_checkout = param_text_checkout;
	dataToSend.text_emptycart = param_text_emptycart;
	dataToSend.text_emptycartbodytext = param_text_emptycartbodytext;
	dataToSend.text_item = param_text_item;
	dataToSend.text_items = param_text_items;

	dataToSend.button_viewcart_show = param_button_viewcart_show;
	dataToSend.button_checkout_show = param_button_checkout_show;

	dataToSend.pages = param_pages;
	dataToSend.pages_showurls = param_pages_showurls;
	dataToSend.pages_hideurls = param_pages_hideurls;

	jQuery.ajax
	(

		{
			url : bwwoofcvars.ajaxHandlerUrl,
			type : 'post',
			dataType: 'json',
			data :
			{

				action : 'bw_woofc_admin_savesettings_proversion',
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
