<?php
/*
Plugin Name: WooCommerce Pre-Orders
Plugin URI:  http://ignitewoo.com
Description: Allows buyers to pre-order products. Significantly enhances the built-in WooCommerce backorder subsystem.
Version: 2.4.4
Author: <strong>IgniteWoo.com - Custom Add-ons for WooCommerce!</strong>
Author URI: http://ignitewoo.com

Copyright (c) 2012 - IgniteWoo.com -- ALL RIGHTS RESERVED


=== LICENSE ===

This plugin comes with a single site license - unless you purchased
a multisite license. Please support us and help create more great plugins
by buying a license for each site that you use it on.

The software is distrbuted WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE. You use this software at your own risk.
*/
/**
TODO: 

Maybe add text to the SHOP page that shows that the product is PREORDER

*/



/**
* Required functions
*/
if ( ! function_exists( 'ignitewoo_queue_update' ) )
	require_once( dirname( __FILE__ ) . '/ignitewoo_updater/ignitewoo_update_api.php' );

$this_plugin_base = plugin_basename( __FILE__ );

add_action( "after_plugin_row_" . $this_plugin_base, 'ignite_plugin_update_row', 1, 2 );


/**
* Plugin updates
*/
ignitewoo_queue_update( plugin_basename( __FILE__ ), '451c8ec04bcc228ac10c25f3c62b36cf', '921' );




class IgniteWoo_PreOrders {

	var $plugin_url;

	function __construct() {

		$this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );
		
		$this->ran_complete = false;
		
		$this->ran_payment_complete = false;
		
		$this->ran_processing = false;

		add_action( 'init', array( &$this, 'load_plugin_textdomain' ) );
		
		add_action( 'init', array( &$this,'init' ), 999 );
		
		add_action( 'admin_head', array( &$this, 'admin_head' ) );

		add_action( 'woocommerce_process_product_meta', array( &$this, 'woocommerce_process_product_meta' ), 2, 2);
		
		add_filter( 'woocommerce_get_availability', array( &$this, 'woocommerce_get_availability' ), 999999, 2 );
		
                add_filter( 'woocommerce_stock_html', array( &$this, 'woocommerce_stock_html' ), 99999, 2 );

		add_action( 'woocommerce_order_status_changed', array( &$this, 'woocommerce_order_status_changed' ), 999999, 3 );
	
		add_filter( 'woocommerce_payment_complete_order_status', array( &$this, 'woocommerce_payment_complete_order_status' ), 999999, 2 );

		add_action( 'woocommerce_order_status_processing', array( &$this, 'status_to_processing' ), 999999, 1 );

		//add_action( 'woocommerce_payment_complete_order_status_completed', array( &$this, 'status_to_processing' ), 999999, 1 );
		//add_action( 'woocommerce_payment_complete_order_status_processing', array( &$this, 'status_to_processing' ), 999999, 1 );
		
		add_action( 'woocommerce_checkout_update_order_meta', array( &$this, 'woocommerce_checkout_update_order_meta' ), 1, 2 );

		add_action( 'wp_head', array( &$this, 'wp_head' ) );
		
		add_action( 'plugin_row_meta', array( &$this, 'add_meta_links' ), 10, 2 );

		add_action( 'woocommerce_after_shop_loop_item', array( &$this, 'add_catalog_notice' ), 1 );
		
		add_filter( 'woocommerce_order_is_download_permitted', array( &$this, 'maybe_permit_download' ), 9999, 2 );
		
		add_filter( 'woocommerce_customer_get_downloadable_products', array( &$this, 'available_downloads' ), 9999, 1 );
		
		add_filter( 'woocommerce_get_downloadable_file_urls', array( &$this, 'get_downloadable_file_urls' ), 999999, 4 );
		
		add_action( 'woocommerce_order_status_completed_notification', array( $this, 'trigger' ), -1, 1 );
		
		add_filter( 'woocommerce_email_heading_customer_completed_order', array( $this, 'email_heading' ), 999999, 2 ); 

		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 );
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_order_item_meta' ), 10, 2 );
		
		add_filter( 'wc_order_statuses', array( &$this, 'filter_statuses' ), 1 );

	}

	
	function load_plugin_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'ignitewoo_preorder' );

		load_textdomain( 'ignitewoo_preorder', WP_LANG_DIR.'/woocommerce/ignitewoo_preorder-'.$locale.'.mo' );

		$plugin_rel_path = apply_filters( 'ignitewoo_translation_file_rel_path', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		load_plugin_textdomain( 'ignitewoo_preorder', false, $plugin_rel_path );

	}
	
	
	
	// Filter for WC 2.2.x and newer
	function filter_statuses( $statuses = array() ) { 

		$statuses[ 'wc-preorder'] = _x( 'Preorder', 'Order status', 'woocommerce' );

		return $statuses;
			
	}
	


	function init() {
		global $wpdb;
		
		add_action( 'woocommerce_product_options_stock_fields', array( &$this, 'preorder_fields' ) );
					
		register_post_status( 'wc-preorder', array(
			'label'                     => _x( 'Pre-Order', 'Order status', 'woocommerce' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pre-Order <span class="count">(%s)</span>', 'Pre-Order <span class="count">(%s)</span>', 'woocommerce' )
		) );
			
		if ( is_admin() ) {

			if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '>=' ) ) { 

				if ( !get_option( 'ignitewoo_preorder_compat_221', false ) ) { 
					
					// Update previous pre-orders to new status
					$sql = "
					UPDATE {$wpdb->posts} as posts
					LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_ID
					LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
					LEFT JOIN {$wpdb->terms} AS term USING( term_id )
					SET posts.post_status = 'wc-preorder'
					WHERE posts.post_type = 'shop_order'
					AND posts.post_status = 'publish'
					AND tax.taxonomy = 'shop_order_status'
					AND	term.slug LIKE 'preorder%';
					";
					
					$wpdb->query( $sql );
					
					$sql = "
					UPDATE {$wpdb->posts} as posts
					SET posts.post_status = 'wc-preorder'
					WHERE posts.post_type = 'shop_order'
					AND posts.post_status = 'preorder'
					";

					$wpdb->query( $sql );

					update_option( 'ignitewoo_preorder_compat_221', 1 );
			
				}
				
			} else { 
			
				if ( !get_term_by( 'slug', 'preorder', 'shop_order_status' ) )
					wp_insert_term( 'preorder', 'shop_order_status' );
			}
			
		}

		$sql = 'select ID, m2.meta_value as timestamp from ' . $wpdb->posts . ' left join ' . $wpdb->postmeta . ' m1 on ID = m1.post_id
			left join ' . $wpdb->postmeta . ' m2 on ID = m2.post_id
			left join ' . $wpdb->postmeta . ' m3 on ID = m3.post_id
			where ( m1.meta_key = "_enable_preorders" and m1.meta_value = "yes" ) 
			and ( m2.meta_key = "_availability_timestamp" and m2.meta_value != "" )
			and ( m3.meta_key = "_availability_auto" and m3.meta_value = "yes" )
			and post_status = "publish"';

		$products = $wpdb->get_results( $sql );

		if ( !$products )
			return;

		foreach( $products as $p ) {

			if ( current_time( 'timestamp', false ) < $p->timestamp )
				continue;

			delete_post_meta( $p->ID, '_enable_preorders' );

			$stock_level = absint( get_post_meta( $p->ID, '_availability_stock_level', true ) );

			update_post_meta( $p->ID, '_stock', $stock_level );
			
			update_post_meta( $p->ID, '_stock_status', 'instock' );
			
		}
		
	}

	
	function wp_head() {
		global $post;
		
		if ( !is_product() )
			return;

		if ( 'yes' != get_post_meta( $post->ID, '_enable_preorders', true ) )
			return;
		
		wp_register_script( 'ign-jquery-countdown', $this->plugin_url . 'scripts/kkcountdown.js' );
		
		wp_enqueue_script( 'ign-jquery-countdown' );

		$display_timer_labels = get_post_meta( $post->ID, '_countdown_label', true );

		if ( !$display_timer_labels ) {
		
			$display_timer_labels = array( 'day' => 'day', 'days' => 'days', 'hours' => ':', 'mins' => ':', 'secs' => '' );
			
			update_post_meta( $post->ID, '_countdown_label', $display_timer_labels );
		}

		if ( 'yes' != get_post_meta( $post->ID, '_display_availability_timer', true ) )
			return;
	
		?>
		<script>
		jQuery( document ).ready( function() { 
			jQuery("#product_timer").kkcountdown({
				dayText : ' <?php echo $display_timer_labels['day'] ?> ',
				daysText : ' <?php echo $display_timer_labels['days'] ?> ',
				hoursText : '<?php echo $display_timer_labels['hours'] ?>',
				minutesText : '<?php echo $display_timer_labels['mins'] ?>',
				secondsText : '<?php echo $display_timer_labels['secs'] ?>',
				displayZeroDays : false,
				oneDayClass : 'one-day'
			});
		});
		</script>
		<?php
	}

	
	function admin_head() {
		global $typenow;

		if ( isset( $_GET['taxonomy'] ) )
			return;
			
		if (
			( 'product' == $typenow && isset( $_GET['action'] ) && 'edit' == $_GET['action'] )
			||
			( false !== strpos( $_SERVER['REQUEST_URI'], 'post-new.php' ) && 'product' == $typenow )
		) {

			wp_enqueue_script( 'jquery-ui' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_register_script( 'ign-jquery-timepicker', $this->plugin_url . 'scripts/jquery-ui-timepicker-addon.js' );
			wp_enqueue_script( 'ign-jquery-timepicker' );
			wp_register_script( 'ign-jquery-timepicker-slider', $this->plugin_url . 'scripts/jquery-ui-sliderAccess.js' );
			wp_enqueue_script( 'ign-jquery-timepicker-slider' );
			wp_enqueue_style( 'ign-jquery-timepicker-css', $this->plugin_url . '/scripts/jquery-ui-timepicker-addon.css' );
		}

		?>
		<style>
			#preorder_box {
				border-top: 1px solid #ccc;
				border-bottom: 1px solid #ccc;
			} 
			.widefat .column-order_status mark.preorder {
				background-color: #c5f9c5 !important;
				color: #333 !important;
				border: 1px solid #a0caa0;
			}
			
			<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) { ?>
			
				.widefat .column-order_status mark.preorder {
					background: url( "<?php echo $this->plugin_url . 'pending.png' ?>" ) no-repeat scroll 3px 1px rgba(0, 0, 0, 0);
					border-radius: 12px;
				}
				
			<?php } else if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0' ) > 0 ) { ?>

				.widefat .column-order_status mark.preorder {
					background-image: url( "<?php echo $this->plugin_url . '/pending.png' ?>" );
				}

			<?php } ?>
		</style>
		
		<?php 
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) 
			$js_obj_prefix = 'woocommerce_admin_meta_boxes';
		else
			$js_obj_prefix = 'woocommerce_writepanel_params';
		?>
		<?php 
		if (
			( 'product' == $typenow && isset( $_GET['action'] ) && 'edit' == $_GET['action'] )
			||
			( false !== strpos( $_SERVER['REQUEST_URI'], 'post-new.php' ) && 'product' == $typenow )
		) {
		
		?>
		<script>
		jQuery( document ).ready( function() {
		        jQuery( ".ign-date-picker" ).datepicker({
				dateFormat: "yy-mm-dd",
				numberOfMonths: 3,
				showButtonPanel: true,
				buttonImage: <?php echo $js_obj_prefix ?>.calendar_image,
				buttonImageOnly: true
			});

			jQuery( "#_availability_time" ).timepicker({
				timeFormat: 'HH:mm',
				hourGrid: 4,
				minuteGrid: 10,
			});
		});
		</script>
		<?php } ?>
		<?php
	}
	

	function preorder_fields() {
		global $post;

		$meta = get_post_custom( $post->ID, true );

		$enabled = isset( $meta['_enable_preorders'][0] ) ? $meta['_enable_preorders'][0]  : '';

		$adate = isset( $meta['_availability_date'][0] ) ? $meta['_availability_date'][0]  : '';
		
		$atime = isset( $meta['_availability_time'][0] ) ? $meta['_availability_time'][0]  : '';

		$auto_stock = isset( $meta['_availability_auto'][0] ) ? $meta['_availability_auto'][0]  : '';

		$stock_level = isset( $meta['_availability_stock_level'][0] ) ? $meta['_availability_stock_level'][0]  : '';

		$ddate = isset( $meta['_display_availability_date'][0] ) ? $meta['_display_availability_date'][0]  : '';
		
		$ddate_prefix = isset( $meta['_availability_date_prefix'][0] ) ? $meta['_availability_date_prefix'][0]  : '';

		$preorder_string = isset( $meta['_preorder_string'][0] ) ? $meta['_preorder_string'][0]  : '';
		
		$preorder_string_class = isset( $meta['_preorder_string_class'][0] ) ? $meta['_preorder_string_class'][0]  : '';

		$display_timer = isset( $meta['_display_availability_timer'][0] ) ? $meta['_display_availability_timer'][0]  : '';
		
		$display_timer_labels = isset( $meta['_countdown_label'][0] ) ? $meta['_countdown_label'][0]  : '';
		
		$display_timer_labels = maybe_unserialize( $display_timer_labels );
		
		$display_timer_prefix = isset( $meta['_display_availability_prefix'][0] ) ? $meta['_display_availability_prefix'][0]  : '';
		
		$display_timer_suffix = isset( $meta['_display_availability_suffix'][0] ) ? $meta['_display_availability_suffix'][0]  : '';

		if ( !$display_timer_labels ) {
		
			$display_timer_labels = array( 'day' => 'day', 'days' => 'days', 'hours' => ':', 'mins' => ':', 'secs' => '' );
			
			update_post_meta( $post->ID, '_countdown_label', $display_timer_labels );
		}

		?><div id="preorder_box"><p><strong>PreOrder Settings</strong></p><?php

		woocommerce_wp_checkbox( array( 'id' => '_enable_preorders', 'label' => __('Enable Preorders', 'ignitewoo_preorder'), 'cbvalue' => 'yes', 'value' => $enabled, 'desc_tip' => true, 'description' => __(' Check this box to enable the product to preordered', 'ignitewoo_preorder') ) );
		
		woocommerce_wp_text_input( array( 'id' => '_availability_date', 'class' => 'short ign-date-picker', 'label' => __('Availability Date', 'ignitewoo_preorder'), 'desc_tip' => true, 'value' => $adate, 'description' => __('If this is currently a preorder product, optionally enter your expected availability date. This date is for your information only, unless you check the box to display the availability date on the product page, or enable the countdown timer, or enable auto availability', 'ignitewoo_preorder') ) );

		woocommerce_wp_text_input( array( 'id' => '_availability_time', 'class' => 'short', 'label' => __('Availability Time', 'ignitewoo_preorder'), 'desc_tip' => true, 'value' => $atime, 'description' => __('Optional. This is informational only, unless you check the box to display the availability date on the product page, or enable the countdown timer, or enable auto availability', 'ignitewoo_preorder') ) );

		woocommerce_wp_select( array( 'id' => '_availability_auto', 'class' => 'select' , 'label' => __('Automatic Availability', 'ignitewoo_preorder'), 'value' => $auto_stock, 'options' => array( 'yes' => __('Yes', 'ignitewoo_preorder'), 'no' => __('No', 'ignitewoo_preorder') ),                               'desc_tip' => true, 'description' => __('When enabled, the product will become available at the date / time indicated above, with the stock level indicated below, and stock status will be set to "In Stock" NOTE: This only works if you set an availability date. If you set a date and leave the time blank then the time is assumed to be 00:01 ( one minute after midnight )', 'ignitewoo_preorder') ) );

		woocommerce_wp_text_input( array( 'id' => '_availability_stock_level', 'class' => 'short', 'label' => __('Auto Stock Level', 'ignitewoo_preorder'), 'desc_tip' => true, 'value' => $stock_level, 'description' => __('When Automatic Availability is enabled, the stock level will be set to this amount when the available date / time is reached. Set the value to a number other than zero, otherwise the product will appear to be out of stock!', 'ignitewoo_preorder') ) );

		woocommerce_wp_checkbox( array( 'id' => '_display_availability_date', 'label' => __('Date Display', 'ignitewoo_preorder'), 'cbvalue' => 'yes', 'value' => $ddate, 'desc_tip' => true, 'description' => __('Display the availability date on the product page', 'ignitewoo_preorder') ) );

		woocommerce_wp_text_input( array( 'id' => '_availability_date_prefix', 'class' => '', 'label' => __('Date Display Prefix', 'ignitewoo_preorder'), 'desc_tip' => true, 'value' => $ddate_prefix, 'description' => __('When displaying the date, this string will be used as a prefix to the date.', 'ignitewoo_preorder') ) );

		woocommerce_wp_checkbox( array( 'id' => '_display_availability_timer', 'label' => __('Countdown Display', 'ignitewoo_preorder'), 'cbvalue' => 'yes', 'value' => $display_timer, 'desc_tip' => true, 'description' => __('Display a countdown timer on the product page. You must set a date, and optionally set a time.', 'ignitewoo_preorder') ) );

		woocommerce_wp_text_input( array( 'id' => '_display_availability_prefix', 'class' => 'short', 'label' => __('Countdown Prefix', 'ignitewoo_preorder'), 'desc_tip' => true, 'value' => $display_timer_prefix, 'description' => __('Optional text displayed before the countdown timer', 'ignitewoo_preorder') ) );

		woocommerce_wp_text_input( array( 'id' => '_display_availability_suffix', 'class' => 'short', 'label' => __('Countdown Suffix', 'ignitewoo_preorder'), 'desc_tip' => true, 'value' => $display_timer_suffix, 'description' => __('Optional text displayed after the countdown timer', 'ignitewoo_preorder') ) );
		
		?>
		<p class="form-field">
			<label for="_availability_date"><?php _e( 'Countdown Labels', 'ignitewoo_preorder' ) ?></label>
			<?php _e( 'Day', 'ignitewoo_preorder' ) ?>
			<input type="text" value="<?php echo $display_timer_labels['day']?>" name="_countdown_label[day]" class="short" style="width:50px; float:none">
			<?php _e( 'Days', 'ignitewoo_preorder' ) ?> 
			<input type="text" value="<?php echo $display_timer_labels['days']?>" name="_countdown_label[days]" class="short" style="width:50px; float:none">
			<?php _e( 'Hours', 'ignitewoo_preorder' ) ?>
			<input type="text" value="<?php echo $display_timer_labels['hours']?>" name="_countdown_label[hours]" class="short" style="width:50px; float:none">
			<?php _e( 'Minutes', 'ignitewoo_preorder' ) ?>
			<input type="text" value="<?php echo $display_timer_labels['mins']?>" name="_countdown_label[mins]" class="short" style="width:50px; float:none">
			<?php _e( 'Seconds', 'ignitewoo_preorder' ) ?>
			<input type="text" value="<?php echo $display_timer_labels['secs']?>" name="_countdown_label[secs]" class="short" style="width:50px; float:none">
			<img src="<?php echo plugins_url() ?>/woocommerce/assets/images/help.png" class="help_tip" data-tip="<?php _e( 'Enter the labels or characters to display after each time element', 'ignitewoo_preorder' ) ?>" width="16" height="16">
		</p>
		<?php
		
		woocommerce_wp_text_input( array( 'id' => '_preorder_string', 'class' => 'short', 'label' => __('Availability Message', 'ignitewoo_preorder'), 'desc_tip' => true, 'value' => $preorder_string, 'description' => __('Enter the message displayed to users on the product page. Example: "Preorder yours now" ', 'ignitewoo_preorder') ) );

		woocommerce_wp_text_input( array( 'id' => '_preorder_string_class', 'class' => 'short', 'label' => __('Message CSS Class', 'ignitewoo_preorder'), 'desc_tip' => true, 'value' => $preorder_string_class, 'description' => __('For custom CSS styling of the availability message, enter a CSS class name and add the related CSS styles to your theme stylesheet.', 'ignitewoo_preorder') ) );
		
		?></div><?php 
	}


	function woocommerce_process_product_meta( $post_id, $post ) {

		update_post_meta( $post_id, '_enable_preorders', $_POST['_enable_preorders'] );

		update_post_meta( $post_id, '_availability_date', trim( $_POST['_availability_date'] ) );

		update_post_meta( $post_id, '_availability_time', trim( $_POST['_availability_time'] ) );

		update_post_meta( $post_id, '_availability_auto', $_POST['_availability_auto'] );

		update_post_meta( $post_id, '_availability_stock_level', trim( $_POST['_availability_stock_level'] ) );

		update_post_meta( $post_id, '_display_availability_date', $_POST['_display_availability_date'] );

		update_post_meta( $post_id, '_display_availability_timer', $_POST['_display_availability_timer'] );

		update_post_meta( $post_id, '_countdown_label', $_POST['_countdown_label'] );

		update_post_meta( $post_id, '_display_availability_prefix', $_POST['_display_availability_prefix'] );
		
		update_post_meta( $post_id, '_display_availability_suffix', $_POST['_display_availability_suffix'] );
		
		update_post_meta( $post_id, '_availability_date_prefix', $_POST['_availability_date_prefix'] );
		
		update_post_meta( $post_id, '_preorder_string', $_POST['_preorder_string'] );
		
		update_post_meta( $post_id, '_preorder_string_class', $_POST['_preorder_string_class'] );

		if ( '' != trim( $_POST['_availability_date'] ) ) {

			if ( '' ==  trim( $_POST['_availability_time'] ) )
				$time = '00:01';
			else
				$time = trim( $_POST['_availability_time'] );

			$datetime =  trim( $_POST['_availability_date'] ) . ' ' . $time;

			$datetime = strtotime( $datetime );

			update_post_meta( $post_id, '_availability_timestamp', $datetime );
			
		}

	}

	function woocommerce_get_availability( $args, $product ) {
		global $post;

		if ( 'yes' != get_post_meta( $post->ID, '_enable_preorders', true ) )
			return $args;

		$preorder_string = get_post_meta( $post->ID, '_preorder_string', true );
		
		$preorder_string_class = get_post_meta( $post->ID, '_preorder_string_class', true );

		if ( !$preorder_string )
			$preorder_string = '';
			
		if ( !empty( $preorder_string_class ) )
			$ret['class'] = $preorder_string_class;
		else
			$ret['class'] = $args['class'];

		$ret['availability'] = $preorder_string;
		
		return $ret; 
	
	}

	
	function woocommerce_stock_html( $msg, $availability ) {
		global $post;

		if ( 'yes' != get_post_meta( $post->ID, '_enable_preorders', true ) )
			return $msg;

		$adate = get_post_meta( $post->ID, '_availability_date', true );

		$ddate = get_post_meta( $post->ID, '_display_availability_date', true );

		if ( !$ddate || 'yes' != $ddate || empty( $adate ) || '' == trim( $adate ) )
			return $msg;

		$ddate_prefix = get_post_meta( $post->ID, '_availability_date_prefix', true );

		$date_format = get_option( 'date_format', false );

		if ( !$date_format )
			$date_format = 'M d, Y';

		$adate = date( $date_format, strtotime( $adate ) );

		$atime = get_post_meta( $post->ID, '_availability_time', true );

		$time_format = get_option( 'time_format', false );

		if ( !$time_format )
			$time_format = 'H:i';
			
		if ( !empty( $atime ) && '' != trim( $atime ) ) {
		
			$the_time = date( $time_format, strtotime( $atime ) );
			
			$the_date = '<span class="timer_date">' . $adate . '</span> <span class="timer_time">' . $the_time . '</span>';
			
		} else {
		
			$the_date = '<span class="timer_date">' . $adate . '</span>';
		}
		
		$display_timer = get_post_meta( $post->ID, '_display_availability_timer', true );

		if ( $display_timer ) {

			if ( empty( $atime ) || '' == trim( $atime ) || !$atime )
				$atime = '00:00:01';

			$adate = str_replace( ',', '', $adate ); // remove commas from dates
			
			//$adate_time = strtotime( $adate . ' ' . $atime . ' ' . get_option( 'gmt_offset' ) );
			$adate_time = strtotime( $adate . ' ' . $atime );
			
			$display_timer_prefix = get_post_meta( $post->ID, '_display_availability_prefix', true );
			
			$display_timer_suffix = get_post_meta( $post->ID, '_display_availability_suffix', true );

			if ( !$display_timer_prefix )
				$display_timer_prefix = '';

			if ( !$display_timer_suffix )
				$display_timer_suffix = '';
			
			$timer = '<p id="product_preorder_timer">' . $display_timer_prefix . ' <span id="product_timer" time="' . $adate_time . '"></span> ' . $display_timer_suffix . '</p>';
			
		} else
			$timer = '';
		
		return $msg . '<p class="availability_date ">' . $ddate_prefix . ' ' . $the_date . '</p>' . $timer;


	}


	function woocommerce_order_status_changed( $order_id, $current_status, $new_status ) { 
//echo '1';
		if ( $this->ran_payment_complete   ) 
			return;
//echo '2';
		$this->ran_payment_complete = true;
//echo '3';
		if ( empty( $order_id ) || absint( $order_id ) <= 0 ) 
			return;
//echo '4 ' . $new_status;
		if ( 'completed' != $new_status && 'processing' != $new_status )
			return;
//echo '5';
		remove_action( 'woocommerce_order_status_completed', array( &$this, 'woocommerce_order_status_changed' ), 999999, 1 );

		$this->woocommerce_payment_complete_order_status( 'completed', $order_id );
//die( 'test' );
		/*
		$order = new WC_Order( $order_id );

		foreach ( $order->get_items() as $item ) {

			foreach( $item['item_meta'] as $name => $value ) {
//vaR_dump( $name, $value ); 
				if ( !empty( $name ) && !empty( $value ) ) {

					if ( __( 'Pre-order', 'ignitewoo_preorder' ) == $name ) {
						
						$available_datetime = strtotime( $value[0] );

						if ( $available_datetime < current_time( 'timestamp', false ) )
							continue;
//echo '-----------> '. $value[0] . ' > ' . $order_id . ' ' . date( 'Y-m-d H:i:s', strtotime( $value[0] ) );
						remove_all_actions( 'woocommerce_payment_complete' );
						
						wp_set_object_terms( $order_id, array( 'preorder' ), 'shop_order_status', false );
//die( 'asdffff' );
						return;
					}

				}
			}
		}
//die( '1' );
		*/
	}


	function status_to_processing( $order_id ) {

		if ( $this->ran_processing )
			return;
			
		$this->ran_processing = true;
		
		remove_action( 'woocommerce_order_status_processing', array( &$this, 'status_to_processing' ), 999999, 1 );
	
		$this->woocommerce_payment_complete_order_status( 'processing', $order_id );
		
	}
	
	
	function woocommerce_payment_complete_order_status( $order_status, $order_id ) {
		global $woocommerce, $wpdb;

		if ( $this->ran_complete ) 
			return $order_status;
			
		$this->ran_complete = true;
		
		if ( ( 'processing' != $order_status && 'completed' != $order_status ) || absint( $order_id ) <= 0 )
			return $order_status; 
			
		remove_filter( 'woocommerce_payment_complete_order_status', array( &$this, 'woocommerce_payment_complete_order_status' ), 999999, 2 );
			
		$order = new WC_Order( $order_id );

		if ( !$order )
			return $order_status;
			
		$done = false;

		// Check all order items, if any are preorders then check the availability date. If the date is in the future then 
		// force the order status to "preorder" - which means "processing" and "completed" are disallowed as statuses until
		// the availability date / time is reached. 
		foreach ( $order->get_items() as $item ) {
//var_dump( $item['item_meta'] );
			foreach( $item['item_meta'] as $name => $value ) {

				if ( !empty( $name ) && !empty( $value ) ) {

					if ( __( 'Pre-order', 'ignitewoo_preorder' ) == $name ) {
//echo '<p> is preorder <p>';	
						$available_datetime = strtotime( $value[0] );
//var_dump( $name, $value, date( 'Y-m-d H:i:s', $available_datetime ), ( $available_datetime < current_time( 'timestamp', false ) ) ); die;
						// release time already come and gone? 
						if ( $available_datetime < current_time( 'timestamp', false ) )
							continue;
//echo '<p> future date <p>';
						//remove_all_actions( 'woocommerce_payment_complete' );
						
						if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '>=' ) ) { 
						
							$sql = 'update ' . $wpdb->posts . ' set post_status = "wc-preorder" where ID = ' . $order_id;
							
							$wpdb->query( $sql );
						
							// for backend order status changes
							$_POST['order_status'] = 'wc-preorder';
						
						} else { 
						
							wp_set_object_terms( $order_id, array( 'preorder' ), 'shop_order_status', false );
							
							// for backend order status changes
							$_POST['order_status'] = 'preorder';
							
						}
//echo( 'yes:' . $order_id );
						
						
						$done = true;
				
						//return 'preorder';
					}

				}
			}
		}

		if ( $done ) { 
			
			// Just to make sure classes are loaded and triggers are set up by WC
			$x = $woocommerce->mailer();
			
//var_dump( '--------> ' , $order_status, $order ); die;
			if ( 'completed' == $order_status ) {
			
				do_action( 'woocommerce_order_status_completed', $order_id );
				
				// Ensure the payments via Stripe trigger order emails - it does bizarre things via JS
				if ( 'stripe' ==  $order->payment_method )
					do_action( 'woocommerce_order_status_pending_to_completed_notification', $order_id );
				else 
					do_action( 'woocommerce_order_status_completed_notification', $order_id );
			}
			
			if ( 'processing' == $order_status ) {
			
				do_action( 'woocommerce_order_status_processing', $order_id );
				
				// Ensure the payments via Stripe trigger order emails - it does bizarre things via JS
				//if ( 'stripe' ==  $order->payment_method )
					do_action( 'woocommerce_order_status_pending_to_processing_notification', $order_id );
			}
			
			if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '>=' ) ) { 

				return 'wc-preorder';
				
			} else { 
//die;	
				return 'preorder';
				
			}
			
		}
//die;
		return $order_status;
		
//var_dump( '--------> ' , $order ); die;
//die( 'oops' );
	}


	function woocommerce_checkout_update_order_meta( $order_id, $posted ) {

		$order_items = get_post_meta( $order_id, '_order_items', true );

		if ( !$order_items )
			return;

		$preorders = false;
		
		foreach ( $order_items as $oi => $values ) {
			
			if ( 'yes' != get_post_meta( $values['id'], '_enable_preorders', true ) )
				continue;

			$date = get_post_meta( $values['id'], '_availability_date', true );

			if ( !$date )
				$date = '';
				
			$time = get_post_meta( $values['id'], '_availability_time', true );

			if ( !$time )
				$time = '';
				
			$when = $date . ' ' . $time;

			$order_items[ $oi ]['item_meta'][] = array( 'meta_name' => __( 'Pre-order', 'ignitewoo_preorder' ), 'meta_value' => __( 'Expected availability:', 'ignitewoo_preorder' ) . ' ' . $when  );
				
			$preorders = true;
		}

		if ( $preorders )
			update_post_meta( $order_id, '_order_items', $order_items );
			
	}


	function add_catalog_notice() {
		global $post;

		if ( !$post->ID )
			return;
			
		if ( 'yes' != get_post_meta( $post->ID, '_enable_preorders', true ) )
			return;

		echo '<p class="preorder_line_one">' . apply_filters( 'woocommerce_preorder_notice_line_one', __( 'Available by Pre-Order', 'ignitewoo_preorder' ) ) . '</p>';

		echo '<p class="preorder_line_two">' . apply_filters( 'woocommerce_preorder_notice_line_two', __( 'View product for details', 'ignitewoo_preorder' ) ) . '</p>';
	}


	function add_meta_links( $links, $file ) {

		$plugin_path = trailingslashit( dirname(__FILE__) );
                $plugin_dir = trailingslashit( basename( $plugin_path ) );

		if ( $file == $plugin_dir . 'woocommerce-preorders.php' ) {

			$links[]= '<a href="http://ignitewoo.com/contact-us"><strong>' . __( 'Support', 'ignitewoo_preorder' ) . '</strong></a>';
			$links[]= '<a href="http://ignitewoo.com">' . __( 'View Add-ons / Upgrades' ) . '</a>';
			$links[]= '<img style="height:24px;vertical-align:bottom;margin-left:12px" src="http://ignitewoo.com/wp-content/uploads/2012/02/ignitewoo-bar-black-bg-rounded2-300x86.png">';

		}
		return $links;
	}
	
	
	function maybe_permit_download( $permit, $order ) {

		if ( 'preorder' == $order->status || 'wc-preorder' == $order->status ) 
			return false;
			
		return $permit; 

	}

	
	function available_downloads( $downloads ) { 
	
		if ( empty( $downloads ) )
			return $downloads;
			
		for( $i = 0; $i < count( $downloads ); $i++ ) { 
		
			if ( !isset( $downloads[ $i ]['order_id'] ) )
				continue;
				
			$o = new WC_Order( $downloads[ $i ]['order_id'] );

			foreach ( $o->get_items() as $item ) {

				foreach( $item['item_meta'] as $name => $value ) {

					if ( !empty( $name ) && !empty( $value ) ) {

						if ( __( 'Pre-order', 'ignitewoo_preorder' ) == $name ) {

							$available_datetime = strtotime( $value[0] );
							
							if ( $available_datetime > current_time( 'timestamp', false ) )
								unset( $downloads[ $i ] );
								
						}
						
					}
				
				}
				
			}
	
			if ( 'preorder' == $o->status || 'wc-preorder' == $o->status )
				unset( $downloads[ $i ] );
		
		}

		return $downloads;
	}
	
	
	public function add_cart_item_data( $cart_item_meta, $product_id ) {
		global $woocommerce;

		$is_preorder = get_post_meta( $product_id, '_enable_preorders', true );

		if ( 'yes' != $is_preorder )
			return $cart_item_meta;
			
		$cart_item_meta['preorder'] = true;
			
		return $cart_item_meta;
	}

	
	public function get_cart_item_from_session( $cart_item, $values ) {

		if ( !empty( $values['preorder'] ) ) {
		
			$cart_item['preorder'] = true;
		}

		return $cart_item;
	}


	public function get_item_data( $item_data, $cart_item ) {

		$is_preorder = get_post_meta( $cart_item['data']->id, '_enable_preorders', true );
	
		if ( ! empty( $cart_item['preorder'] ) ) {
		
			$date = get_post_meta( $cart_item['data']->id, '_availability_date', true );

			if ( !$date )
				$date = '';
				
			$time = get_post_meta( $cart_item['data']->id, '_availability_time', true );

			if ( !$time )
				$time = '';
				
			$when = $date . ' ' . $time;

			if ( !empty( $when ) ) { 
			
				$item_data[] = array(
					'name'    => __( 'Pre-order', 'ignitewoo_preorder' ),
					'value'   => $when,
					'display' => $when
				);
			}
		}

		return $item_data;
	}


	public function add_cart_item( $cart_item ) {

		return $cart_item;
	}


	public function add_order_item_meta( $item_id, $cart_item ) {
	
		if ( ! empty( $cart_item['preorder'] ) ) {
		
			$date = get_post_meta( $cart_item['data']->id, '_availability_date', true );

			if ( !$date )
				$date = '';
				
			$time = get_post_meta( $cart_item['data']->id, '_availability_time', true );

			if ( !$time )
				$time = '';
				
			$when = $date . ' ' . $time;
		
			woocommerce_add_order_item_meta( $item_id, __( 'Pre-order', 'ignitewoo_preorder' ), $when );
				
		}
			
	}
	
	// Grab the order ID
	function trigger( $order_id ) { 
	
		$this->order_id = $order_id;
	
	}
	
	function get_downloadable_file_urls( $file_urls, $product_id, $variation_id, $item ) { 
		global $wpdb, $order;
		
		if ( !$order || !is_object( $order ) ) 
			$order = new WC_Order( $this->order_id );
		
		if ( 'preorder' != $order->status && 'wc-preorder' != $order->status )
			return $file_urls;
		
		$date = get_post_meta( $product_id, '_availability_date', true );

		if ( !$date )
			$date = '';
			
		$time = get_post_meta( $product_id, '_availability_time', true );

		if ( !$time )
			$time = '';
		
		if ( empty( $date ) && empty( $time ) ) 
			return $file_urls;
			
		$when = strtotime( $date . ' ' . $time );

		if ( $when > current_time( 'timestamp', false ) )
			return array();

		$download_file = $variation_id > 0 ? $variation_id : $product_id;
		
		$_product = get_product( $download_file );

		$results = $wpdb->get_results( $wpdb->prepare("
			SELECT download_id
			FROM " . $wpdb->prefix . "woocommerce_downloadable_product_permissions
			WHERE user_email = %s
			AND order_key = %s
			AND product_id = %s
		", $order->billing_email, $order->order_key, $download_file ) );

		$file_urls = array();
		
		foreach ( $results as $result ) {
		
			if ( $_product->has_file( $result->download_id ) ) {

				$file_urls[ $_product->get_file_download_path( $result->download_id ) ] = add_query_arg( array( 'download_file' => $download_file, 'order' => $order->order_key, 'email' => $order->billing_email, 'key' => $result->download_id ), trailingslashit( home_url() ) );

			}
		}

		return $file_urls;
	}
	
	
	function email_heading( $heading, $order ) {
	
		if ( 'preorder' != $order->status && 'wc-preorder' != $order->status )
			return $heading; 
			
		// This string is part of WooCommerce so leave the text domain set to 'woocommerce'
		return __( 'Your order is complete', 'woocommerce' );
	
	}
	
	
}

global $ignitewoo_preorders;

$ignitewoo_preorders = new IgniteWoo_PreOrders();