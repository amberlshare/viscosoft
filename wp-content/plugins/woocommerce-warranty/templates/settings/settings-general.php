<?php
$warranty_page_id       = woocommerce_get_page_id('warranty');
$order_status_options   = array();
$warranty_statuses      = warranty_get_statuses();
$warranty_status_options= array();

$saved_rma  = get_option( 'warranty_saved_rma', 0 );
$last_rma   = get_option( 'warranty_last_rma', 0 );

if ( WC_Warranty_Compatibility::is_wc_version_gte_2_2() ) {
    $statuses = wc_get_order_statuses();

    foreach ( $statuses as $key => $status ) {
        $key = str_replace( 'wc-', '', $key );
        $order_status_options[ $key ] = $key;
    }
} else {
    $statuses = get_terms( 'shop_order_status', array('hide_empty' => false) );

    foreach ( $statuses as $status ) {
        $order_status_options[ $status->name ] = $status->name;
    }
}

foreach ( $warranty_statuses as $warranty_status ) {
    $warranty_status_options[ $warranty_status->slug ] = $warranty_status->name;
}

?>
<div id="warranty_settings_general">

    <?php WC_Admin_Settings::output_fields( $settings['general'] ); ?>

</div>