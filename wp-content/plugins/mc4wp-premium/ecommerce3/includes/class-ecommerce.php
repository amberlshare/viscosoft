<?php

/**
 * Class MC4WP_Ecommerce
 *
 * @since 4.0
 */
class MC4WP_Ecommerce {

	/**
	 * @const string
	 */
	const META_KEY = 'mc4wp_updated_at';

    /**
     * @var MC4WP_Ecommerce_Object_Transformer
     */
    public $transformer;

	/**
	 * Constructor
	 *
     * @param array $settings
	 * @param MC4WP_Ecommerce_Tracker $tracker
	 */
	public function __construct( array $settings, MC4WP_Ecommerce_Tracker $tracker ) {
        $this->transformer = new MC4WP_Ecommerce_Object_Transformer( $settings, $tracker );
    }

    /**
     * @param string $cart_id
     *
     * @return object
     */
    public function get_cart( $cart_id ) {
        $api = $this->get_api();
        $store_id = $this->get_store_id();

        return $api->get_ecommerce_store_cart( $store_id, $cart_id );
    }

    /**
     * Add OR update a cart in MailChimp.
     *
     * @param string $cart_id
     * @param array $cart_data
     *
     * @return bool
     */
	public function update_cart( $cart_id, array $cart_data ) {
        $api = $this->get_api();
        $store_id = $this->get_store_id();

        try {
            $api->update_ecommerce_store_cart( $store_id, $cart_id, $cart_data );
        } catch( MC4WP_API_Resource_Not_Found_Exception $e ) {
            $api->add_ecommerce_store_cart( $store_id, $cart_data );
        }

        return true;
    }

    /**
     * @param string $cart_id
     *
     * @return bool
     */
    public function delete_cart( $cart_id ) {
        $api = $this->get_api();
        $store_id = $this->get_store_id();
        return $api->delete_ecommerce_store_cart( $store_id, $cart_id );
    }

    /**
     * @param WP_User|WC_Order|object $customer_data
     *
     * @return string
     */
	public function update_customer( $customer_data ) {
	    $api = $this->get_api();
        $store_id = $this->get_store_id();

        // get customer data
        $customer_data = $this->transformer->customer( $customer_data );

        // add (or update) customer
        $api->add_ecommerce_store_customer( $store_id, $customer_data );

        return $customer_data['id'];
    }

	/**
	 * @param int|WC_Order $order
	 * @return boolean
     * @throws Exception
	 */
	public function update_order( $order ) {
	    // get & validate order
		$order = wc_get_order( $order );
        if( ! $order ) {
            throw new Exception( sprintf( "Order #%d is not a valid order ID.", $order ) );
        }

        // add or update customer in MailChimp
        $this->update_customer( $order );

        // get order data
        $data = $this->transformer->order( $order );

        // add OR update order in MailChimp
       return $this->is_object_tracked( $order->id ) ? $this->order_update( $order, $data ) : $this->order_add( $order, $data );
	}

    /**
     * @param int $order_id
     *
     * @return boolean
     *
     * @throws Exception
     */
    public function delete_order( $order_id ) {
        $api = $this->get_api();
        $store_id = $this->get_store_id();

        try {
            $success = $api->delete_ecommerce_store_order( $store_id, $order_id );
        } catch ( MC4WP_API_Resource_Not_Found_Exception $e ) {
            // good, order already non-existing
            $success = true;
        }

        // remove meta on success
        delete_post_meta( $order_id, self::META_KEY );

        return $success;
    }

    /**
     * @param WC_Order $order
     * @param array $data
     * @return bool
     *
     * @throws MC4WP_API_Exception
     */
	private function order_add( WC_Order $order, array $data ) {
        $api = $this->get_api();
        $store_id = $this->get_store_id();

        try {
            $response = $api->add_ecommerce_store_order( $store_id, $data );
        }  catch( MC4WP_API_Exception $e ) {

            // update order if it already exists
            if( stripos( $e->detail, 'already exists' ) ) {
                return $this->order_update( $order, $data );
            }

            // if campaign_id data is corrupted somehow, retry without campaign data.
            if( ! empty( $data['campaign_id'] ) && stripos( $e->detail, 'campaign with the provided ID does not exist' ) ) {
                unset( $data['campaign_id'] );
                return $this->order_add( $order, $data );
            }

            throw $e;
        }

        update_post_meta( $order->id, self::META_KEY, date( 'c' ) );
        return true;
    }

    /**
     * @param WC_Order $order
     * @param array $data
     *
     * @return bool
     */
	private function order_update( WC_Order $order, array $data ) {
        $api = $this->get_api();
        $store_id = $this->get_store_id();

        try {
            $response = $api->update_ecommerce_store_order( $store_id, $order->id, $data );
        } catch( MC4WP_API_Resource_Not_Found_Exception $e ) {
            return $this->order_add( $order, $data );
        }

        update_post_meta( $order->id, self::META_KEY, date( 'c' ) );
        return true;
    }

    /**
     * Add or update store in MailChimp.
     *
     * @param array $args
     * @throws MC4WP_API_Exception
     */
    public function update_store( array $args ) {
        $api = $this->get_api();
        $store_id = $this->get_store_id();
        $args['id'] = $store_id;

        // make sure we got a boolean value.
        if( isset( $args['is_syncing'] ) ) {
            $args['is_syncing'] = !!$args['is_syncing'];
        }

        try {
            $api->update_ecommerce_store( $store_id, $args );
        } catch( MC4WP_API_Resource_Not_Found_Exception $e ) {
            $api->add_ecommerce_store( $args );
        } catch( MC4WP_API_Exception $e ) {
            if( $e->status == 400 && stripos( $e->detail, "list may not be changed" ) !== false ) {
                // delete local tracking indficators
                delete_post_meta_by_key( MC4WP_Ecommerce::META_KEY );

                // add new store
                $api->add_ecommerce_store( $args );
            } else {
                throw $e;
            }
        }
    }

    /**
     * Add or update a product + variants in MailChimp.
     *
     * TODO: MailChimp interface does not yet reflect product "updates".
     *
     * @param int|WC_Product $product Post object or post ID of the product.
     * @return boolean
     * @throws Exception
     */
    public function update_product( $product ) {
        $product = wc_get_product( $product );

        // check if product exists
        if( ! $product ) {
            throw new Exception( sprintf( "#%d is not a valid product ID", $product ) );
        }

        // make sure product is not a product-variation
        if( $product instanceof WC_Product_Variation ) {
            throw new Exception( sprintf( "#%d is a variation of another product. Use the variable parent product instead.", $product->id ) );
        }

       $data = $this->transformer->product( $product );

        return $this->is_object_tracked( $product->id ) ? $this->product_update( $product, $data ) : $this->product_add( $product, $data );
    }

    /**
     * @param int $product_id
     * @return boolean
     *
     * @throws Exception
     */
    public function delete_product( $product_id ) {
        $api = $this->get_api();
        $store_id = $this->get_store_id();

        try {
            $success = $api->delete_ecommerce_store_product( $store_id, $product_id );
        } catch( MC4WP_API_Resource_Not_Found_Exception $e ) {
            // product or store already non-existing: good!
            $success = true;
        }

        delete_post_meta( $product_id, self::META_KEY );
        return $success;
    }


    /**
     * @param WC_Product $product
     * @param array $data
     *
     * @return bool
     *
     * @throws MC4WP_API_Exception
     */
    private function product_add( WC_Product $product, array $data ) {
        $api = $this->get_api();
        $store_id = $this->get_store_id();

        try {
            $response = $api->add_ecommerce_store_product( $store_id, $data );
        } catch( MC4WP_API_Exception $e ) {

            // update product if it already exists remotely.
            if( strpos( $e->detail, 'already exists' ) ) {
                return $this->product_update( $product, $data );
            }

            throw $e;
        }

        update_post_meta( $product->id, self::META_KEY, date( 'c' ) );
        return true;
    }

    /**
     * @param WC_Product $product
     * @param array $data
     *
     * @return bool
     */
    private function product_update( WC_Product $product, array $data ) {
        $api = $this->get_api();
        $store_id = $this->get_store_id();

        // TODO: PATCH product itself once MailChimp API supports that.

        try {
            // Add OR update each product variant.
            foreach ($data['variants'] as $variant_data) {
                $response = $api->add_ecommerce_store_product_variant($store_id, $product->id, $variant_data);
            }
        } catch( MC4WP_API_Resource_Not_Found_Exception $e ) {
            return $this->product_add( $product, $data );
        }

        update_post_meta( $product->id, self::META_KEY, date( 'c' ) );
        return true;
    }

    /**
     * @param int $object_id
     *
     * @return bool
     */
    public function is_object_tracked( $object_id ) {
        return !! get_post_meta( $object_id, self::META_KEY, true );
    }

    /**
     * @return MC4WP_API_v3
     */
    private function get_api() {
        return mc4wp('api');
    }

    /**
     * @return mixed
     */
    private function get_store_domain() {
        return parse_url( get_option('siteurl', ''), PHP_URL_HOST );
    }

    /**
     * @return string
     */
    public function get_store_id() {
        return (string) md5( $this->get_store_domain() );
    }
}