<?php
/**
 * WooCommerce Drip Events / Notifications
 *
 * @package   WooCommerce Drip
 * @author    Bryce <bryce@bryce.se>
 * @license   GPL-2.0+
 * @link      http://bryce.se
 * @copyright 2014 Bryce Adams
 * @since     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * wcdrip Events Class
 *
 * @package  WooCommerce Drip
 * @author   Bryce <bryce@bryce.se>
 * @since    1.1.4
 */

if ( ! class_exists( 'WC_Drip_Events' ) ) {

  	class WC_Drip_Events {

    	protected static $instance = null;

    	public function __construct() {

			add_action( 'woocommerce_payment_complete', array( $this, 'new_order' ) );

	    }

	    /**
	     * Start the Class when called
	     *
	     * @package WooCommerce Drip
	     * @author  Bryce <bryce@bryce.se>
	     * @since   1.0.0
	     */

	    public static function get_instance() {

	      // If the single instance hasn't been set, set it now.
	      if ( null == self::$instance ) {
	        self::$instance = new self;
	      }

	      return self::$instance;

	    }


	    /**
	     * New Order Made
	     *
	     * @package WooCommerce Drip
	     * @author  Bryce <bryce@bryce.se>
	     * @since   1.1.0
	     */

	    public function new_order( $order_id ) {

			if ( 1 == get_post_meta( $order_id, '_wcdrip_tracked', true ) ) {
				return false;
			}

	    	$wrapper = $this->wrapper();

			if ( ! $wrapper['api_key'] || ! $wrapper['account'] ) {
				return;
			}

		   	$api_key = $wrapper['api_key'];
	  		$wcdrip_api = new Drip_Api( $api_key );
	  		$compat = new WC_Drip_WC_Plugin_Compatibility();

	  		if ( $wrapper['event_sale_name'] ) {
	  			$event_sale_name = $wrapper['event_sale_name'];
	  		} else {
	  			$event_sale_name = apply_filters( 'wcdrip_action_order', __( 'Purchase', 'woocommerce-drip' ) );
	  		}

      		// Order Variable
			$order = $compat->wc_get_order( $order_id );

			// Order Items
			$products = implode(', ', wp_list_pluck( $order->get_items(), 'name' ) );

		    // Customer ID
		    $customer_id = $order->get_user_id();

			// Fetch Parameters
			$fetch_params = array(
				'account_id'	=> $wrapper['account'],
				'subscriber_id'	=> $order->billing_email,
			);

			// $is_subscriber Variable
			$is_sub_action = $wcdrip_api->fetch_subscriber( $fetch_params );
			if ( $is_sub_action ) {
				$is_subscriber = $is_sub_action['id'];
			} else {
				$is_subscriber = false;
			}

			// Event Tag Parameters
			$event_params = array(
				'account_id'	=> $wrapper['account'],
				'email'			=> $order->billing_email,
				'action'		=> $event_sale_name,
				'properties'	=> $this->event_properties( $order->order_total, $products, $order_id ),
			);

			// Tags
			$tags = apply_filters( 'wcdrip_tag_customer', array(
				__( 'Customer', 'woocommerce-drip' ),
			) );

			// Subscriber Parameters
			$subscriber_params = array(
				'account_id'	=> $wrapper['account'],
				'email'			=> $order->billing_email,
				'custom_fields'	=> $this->custom_fields( $order, $customer_id ),
				'tags'			=> $tags,
			);

			// Check if subscriber exists and if so, send data to Drip
			if ( $is_subscriber ) {

				$wcdrip_api->record_event( $event_params );
				$wcdrip_api->create_or_update_subscriber( $subscriber_params );

			}

			update_post_meta( $order_id, '_wcdrip_tracked', 1 );
	    }


	    // Helper method for properties for sending an event
	    public function event_properties( $value, $products, $order_id ) {

	    	$content = array(
	    		'value'		=> $value*100,
	    		'price'		=> '$' . $value,
	    		'products'	=> $products,
	    		'order_id'	=> $order_id,
	    	);

	    	$obj = json_decode ( json_encode ( $content ), FALSE );

	    	return $obj;

	    }


        /**
         * Helper method for adding custom fields to the subscriber.
         * Includes: name, lifetime value, purchased products (, separated) and customer ID (if user).
         *
         * @since 1.1.4
         *
         * @param $order
         * @param $customer_id
         *
         * @return array
         * @throws Exception
         */
	    public function custom_fields( $order, $customer_id ) {

	    	// Variables
	    	$wrapper = $this->wrapper();
		   	$api_key = $wrapper['api_key'];
	  		$wcdrip_api = new Drip_Api( $api_key );
            $email = $order->billing_email;
			$value = $order->get_total();
            $products = $order->get_items();

	    	// Fetch Parameters
			$fetch_params = array(
				'account_id'	=> $wrapper['account'],
				'subscriber_id'	=> $email,
			);

			// Store lifetime_value field in variable
			$is_fetch_action = $wcdrip_api->fetch_subscriber( $fetch_params );
			if ( is_array( $is_fetch_action ) ) {
				$is_fetch_action = array_filter( $is_fetch_action );
			}

			$return_lifetime_value    = false;
			$return_previous_products = false;

			if ( ! empty( $is_fetch_action['custom_fields']['lifetime_value'] ) ) {
				$return_lifetime_value = $is_fetch_action['custom_fields']['lifetime_value'];
			}

			if ( ! empty( $is_fetch_action['custom_fields']['purchased_products'] ) ) {
				$return_previous_products = $is_fetch_action['custom_fields']['purchased_products'];
			}

			// Check for lifetime_value field
			if ( $return_lifetime_value ) {
				$lifetime_value = $return_lifetime_value;
			} else {
				$lifetime_value = 0;
			}

			// Add value to lifetime_value field
			$lifetime_value = $lifetime_value + $value;

		    // Product IDs
		    $product_ids = implode(', ', wp_list_pluck( $products, 'product_id' ) );

		    // Determine and build list of total products, purchased before and now
		    if ( $return_previous_products ) {
			    $previous_products = $return_previous_products . ', ';
			    $total_products = $previous_products . $product_ids;
		    } else {
			    $total_products = $product_ids;
		    }

		    // Build custom fields to attach to customer
	    	$content = apply_filters( 'wcdrip_custom_fields', array(
	    		'name'				    => $order->billing_first_name . ' ' . $order->billing_last_name,
	    		'lifetime_value'	    => $lifetime_value,
			    'purchased_products'    => $total_products,
	    	), $email, $lifetime_value, $products, $order );

	    	if ( $customer_id ) {
	    		$content['customer_id'] = $customer_id;
	    	}

	    	return $content;

	    }
	    /**
	     * Settings Wrapper
	     * @return  array
	     * @since   1.0.0
	     */

		public function wrapper() {

	    	$WC_Drip_Settings = new WC_Drip_Settings();
	    	return $WC_Drip_Settings->wrapper();

	    }

    }

}