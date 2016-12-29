<?php
/**
 *
 * Settings class
 *
 * @since 2.0
 */
if ( ! class_exists( 'MP_WC_Variation_Details_On_Page_Product_Settings' ) ) {

	class MP_WC_Variation_Details_On_Page_Product_Settings extends WC_Settings_API {

		public $plugin_id;
		public $section_name;
		public $id;

		public function __construct() {
			global $mp_wc_vdopp;
			$this->plugin_id    = $mp_wc_vdopp->plugin_prefix;
			$this->section_name = &$this->plugin_id;
			add_action( 'admin_init', array( $this, 'verify_first_use' ) );
		}

		// Load the class
		public function load() {
			add_action( 'admin_init', array( $this, 'load_hooks' ) );
		}

		// Call for actions
		public function load_hooks() {
			add_filter( 'woocommerce_get_sections_products', array( &$this, 'add_settings_section' ) );
			add_action( 'woocommerce_get_settings_products', array( &$this, 'create_settings_page' ), 10, 2 );
		}

		// Start default settings
		public function verify_first_use() {
			if ( ! get_option( 'mp_wc_vdopp_data_hook' ) ) :
				add_option( 'mp_wc_vdopp_data_hook', '.variations' );
			endif;
			if ( ! get_option( 'mp_wc_vdopp_dom_selector' ) ) :
				add_option( 'mp_wc_vdopp_dom_selector', 'form.cart select' );
			endif;
			if ( ! get_option( 'mp_wc_vdopp_data_selector' ) ) :
				add_option( 'mp_wc_vdopp_data_selector', '.product_details' );
			endif;
		}

		// Create Variations section
		public function add_settings_section( $section ) {
			$section[ $this->section_name ] = __( 'Variations', 'woocommerce-variation-details-on-page-product' );
			return $section;
		}

		// Create settings page
		public function create_settings_page( $settings, $current_section ) {

			if ( $current_section === $this->section_name ) {

				$variation_settings = array();
				$variation_settings[] = array(
					'name'     => __( 'WooCommerce Variation Details on Page Product', 'woocommerce-variation-details-on-page-product' ),
					'type'     => 'title',
					'desc'     => __( 'This plugin has predefined settings outside the box. If you feel comfortable, you are welcome to update data to meet your requirements.', 'woocommerce-variation-details-on-page-product' ) . '<p>' . __( '<strong>Important:</strong> Use a . to identify a class and a # to identify an id.', 'woocommerce-variation-details-on-page-product' ) . '</p>',
				);

				$variation_settings[] = array(
					'name'     => __( 'Place holder for variation data', 'woocommerce-variation-details-on-page-product' ),
					'desc_tip' => __( 'Default value: .variations', 'woocommerce-variation-details-on-page-product' ),
					'id'       => $this->plugin_id . '_data_hook',
					'type'     => 'text',
					'default'  => '.variations',
					'desc'     => __( 'Choose a CSS class or id where you want to hook variation data.', 'woocommerce-variation-details-on-page-product' ),
				);

				$variation_settings[] = array(
					'name'     => __( 'DOM Selector', 'woocommerce-variation-details-on-page-product' ),
					'desc_tip' => __( 'Default value: form.cart select', 'woocommerce-variation-details-on-page-product' ),
					'id'       => $this->plugin_id . '_dom_selector',
					'type'     => 'text',
					'default'  => 'form.cart select',
					'desc'     => __( 'Define the selector that will trigger show data event.', 'woocommerce-variation-details-on-page-product' ),
				);

				$variation_settings[] = array(
					'name'     => __( 'Data Selector', 'woocommerce-variation-details-on-page-product' ),
					'desc_tip' => __( 'Default value: .product_details', 'woocommerce-variation-details-on-page-product' ),
					'id'       => $this->plugin_id . '_data_selector',
					'type'     => 'text',
					'desc'     => __( 'Choose the id/class of displayed data.', 'woocommerce-variation-details-on-page-product' ),
				);

				$variation_settings[] = array(
					'name'     => __( 'Before size', 'woocommerce-variation-details-on-page-product' ),
					'id'       => $this->plugin_id . '_before_size',
					'type'     => 'text',
					'desc'     => __( 'Set text before size.', 'woocommerce-variation-details-on-page-product' ),
				);

				$variation_settings[] = array(
					'name'     => __( 'Before weight', 'woocommerce-variation-details-on-page-product' ),
					'id'       => $this->plugin_id . '_before_weight',
					'type'     => 'text',
					'desc'     => __( 'Set text before weight.', 'woocommerce-variation-details-on-page-product' ),
				);

				$variation_settings[] = array(
					'name'     => __( 'After size', 'woocommerce-variation-details-on-page-product' ),
					'id'       => $this->plugin_id . '_after_size',
					'type'     => 'text',
					'desc'     => __( 'Set text after size.', 'woocommerce-variation-details-on-page-product' ),
				);

				$variation_settings[] = array(
					'name'     => __( 'After weight', 'woocommerce-variation-details-on-page-product' ),
					'id'       => $this->plugin_id . '_after_weight',
					'type'     => 'text',
					'desc'     => __( 'Set text after weight.', 'woocommerce-variation-details-on-page-product' ),
				);

				$variation_settings[] = array(
					'type'     => 'sectionend',
				);

				$variation_settings[] = array(
					'name'     => __( 'Donation', 'woocommerce-variation-details-on-page-product' ),
					'desc'     => __( 'Please, support further development of this plugin by buying the guy an extra dose of caffeine.', 'woocommerce-variation-details-on-page-product' ) . '<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=S626RA3BPS74S" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"></a></p>',
					'type'     => 'title',
				);

				return $variation_settings;

			} else {
				return $settings;
			}
		}
	}
}
