<?php
/**
 * WooCommerce Drip Subscriptions Checkbox
 *
 * @package   WooCommerce Drip
 * @author    Bryce <bryce@bryce.se>
 * @license   GPL-2.0+
 * @link      http://bryce.se
 * @copyright 2014 Bryce Adams
 * @since     1.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Drip_Subscriptions Class
 *
 * @package  WooCommerce Drip
 * @author   Bryce <bryce@bryce.se>
 * @since    1.1.4
 */

if ( ! class_exists( 'WC_Drip_Subscriptions' ) ) {

	class WC_Drip_Subscriptions {

		protected static $instance = null;

		public function __construct() {

			// Settings Wrapper
			$wrapper = $this->wrapper();

			if ( ( $wrapper['subscribe_enable'] == 'yes' ) && $wrapper['subscribe_campaign'] ) {
				add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'subscribe_field' ), 5 );
				add_action( 'woocommerce_register_form', array( $this, 'subscribe_field' ), 5 );
				add_action( 'woocommerce_checkout_order_processed', array( $this, 'process_checkout_form' ), 5, 2 );
				add_action( 'woocommerce_created_customer', array( $this, 'process_register_form' ), 5, 3 );
			}

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
		 * newsletter_field function.
		 *
		 * @access public
		 * @param mixed $woocommerce_checkout
		 * @return void
         * @since 1.1.3
		 */
		public function subscribe_field( $woocommerce_checkout ) {

			$wrapper = $this->wrapper();

			// Get Campaign Name @TODO Transient
			$api_key = $wrapper['api_key'];
			$wcdrip_api = new Drip_API( $api_key );

			$account_id = $wrapper['account'];

			$params = array(
				'account_id' 	=> $account_id,
				'campaign_id'	=> $wrapper['subscribe_campaign'],
			);

			$campaigns = $wcdrip_api->fetch_campaign( $params );

			foreach ( $campaigns as $campaign ) {
				$campaign_name = $campaign['name'];
			}

			// Subscribe Text
			if ( $wrapper['subscribe_text'] ) {
				$subscribe_text_raw = $wrapper['subscribe_text'];
				$subscribe_text = str_replace( '{campaign_name}', $campaign_name, $subscribe_text_raw );
			} else {
				$subscribe_text = __( 'Subscribe to ', 'woocommerce-drip' ) . $campaign_name;
			}

			if ( is_user_logged_in() && get_user_meta( get_current_user_id(), '_wcdrip_subscribed', true ) ) {
				return;
			}

            // Output the subscribe checkbox
			woocommerce_form_field( 'wcdrip_subscribe', array(
					'type' 			=> 'checkbox',
					'class'			=> array('form-row-wide'),
					'label' 		=> $subscribe_text,
				), apply_filters( 'wcdrip_subscribe_default', false )
			);

			echo '<div class="clear"></div>';
		}

		/**
		 * process_newsletter_field function.
		 *
		 * @access public
		 * @param mixed $order_id
		 * @param mixed $posted
		 * @return void
		 */
		public function process_checkout_form( $order_id, $posted ) {

			if ( ! isset( $_POST['wcdrip_subscribe'] ) ) {
				return; // They don't want to subscribe
			}

			$wrapper = $this->wrapper();
			$api_key = $wrapper['api_key'];
			$account_id = $wrapper['account'];

			$wcdrip_api = new Drip_API( $api_key );

			$params = apply_filters( 'wcdrip_checkout_subscribe_params', array(
				'account_id'	=> $account_id,
				'campaign_id'	=> $wrapper['subscribe_campaign'],
				'email'			=> $posted['billing_email'],
			) );

            /**
             * Handle subscription: If user is logged in, and not subscribed before
             * (eg. through registration), subscribe them and update the user meta
             * for them. If not logged in, subscribe the user like normal.
             */
            if ( is_user_logged_in() ) {
                if ( get_user_meta( get_current_user_id(), '_wcdrip_subscribed', true ) !== '1' ) {
                    $wcdrip_api->subscribe_subscriber($params);
                    update_user_meta( get_current_user_id(), '_wcdrip_subscribed', 1 );
                }
            } else {
                $wcdrip_api->subscribe_subscriber($params);
            }

		}


        /**
         * process_register_form function.
         *
         * @access public
         * @param $customer_id
         * @throws Exception
         */
		public function process_register_form( $customer_id) {

			if ( ! isset( $_POST['wcdrip_subscribe'] ) ) {
				return; // They don't want to subscribe
			}

            $user = get_userdata( $customer_id );
            $email = $user->user_email;

			$wrapper = $this->wrapper();
			$api_key = $wrapper['api_key'];
			$account_id = $wrapper['account'];

			$wcdrip_api = new Drip_API( $api_key );

			$params = apply_filters( 'wcdrip_register_subscribe_params', array(
				'account_id'	=> $account_id,
				'campaign_id'	=> $wrapper['subscribe_campaign'],
				'email'			=> $email,
			) );

			$wcdrip_api->subscribe_subscriber( $params );

            update_user_meta( $customer_id, '_wcdrip_subscribed', 1 );

		}

		/**
		 * Settings Wrapper
		 *
		 * @package WooCommerce Drip
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function wrapper() {

			$WC_Drip_Settings = new WC_Drip_Settings();
			return $WC_Drip_Settings->wrapper();

		}

	}

}