<?php

//called from main module in case the pro version is activated
function bw_woofc_includeresources_adminpages_scripts_pro()
{

        wp_enqueue_style( 'spectrum-css', bw_woofcpro_globals_plugin_url . '/css/vendors/spectrum.css');

}

//called from main module in case the pro version is activated
function bw_woofc_includeresources_adminpages_css_pro()
{

        $propagesettings_js_ver  = date("ymd-Gis", filemtime(bw_woofcpro_globals_plugin_path . '/scripts/settings.js' ));
        wp_register_script( 'bw-woofc-adminpro-js', bw_woofcpro_globals_plugin_url . '/scripts/settings.js', array(), $propagesettings_js_ver, false);
        wp_localize_script( 'bw-woofc-adminpro-js', 'bwwoofcvars', bw_woofc_buildarrayconstantsforscripts());
        wp_enqueue_script( 'bw-woofc-adminpro-js', bw_woofcpro_globals_plugin_url . '/scripts/settings.js', array(), $propagesettings_js_ver, false );

        $spectur_js_ver  = date("ymd-Gis", filemtime( bw_woofcpro_globals_plugin_path . '/scripts/vendors/spectrum.js' ));
        wp_register_script( 'bw-woofc-spectrum-js', bw_woofcpro_globals_plugin_url . '/scripts/vendors/spectrum.js', array(), $spectur_js_ver, false);
        wp_localize_script( 'bw-woofc-spectrum-js', 'bwwoofcvars', bw_woofc_buildarrayconstantsforscripts());
        wp_enqueue_script( 'bw-woofc-spectrum-js', bw_woofcpro_globals_plugin_url . '/scripts/vendors/spectrum.js', array(), $spectur_js_ver, false );

}


?>
