<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Warranty_Order {

    public function __construct() {
        // order status changed
        add_action( 'woocommerce_order_status_changed', array($this, 'order_status_changed'), 10, 3 );
        add_action( 'woocommerce_order_status_completed', array($this, 'order_completed') );

        // Add meta to order item
        add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 10, 2 );
    }

    /**
     * Listens to order status changes and sets the completed date if the current
     * order status matches the start status of the warranty period
     *
     * @param int       $order_id
     * @param string    $old_status
     * @param string    $new_status
     */
    public function order_status_changed( $order_id, $old_status, $new_status ) {
        global $woocommerce;

        $start_status   = get_option( 'warranty_start_status', 'completed' );
        $order          = WC_Warranty_Compatibility::wc_get_order( $order_id );

        if ( $new_status != $start_status ) {
            return;
        }

        $items          = $order->get_items();
        $has_warranty   = false;
        $now            = current_time('mysql');

        foreach ( $items as $item ) {
            $warranty       = false;
            $addon_index    = false;
            $metas          = (isset($item['item_meta'])) ? $item['item_meta'] : array();

            foreach ( $metas as $key => $value ) {
                if ( $key == '_item_warranty' ) {
                    $warranty = maybe_unserialize( $value[0] );
                }
            }

            if ( $warranty ) {
                // update order's date of completion
                update_post_meta( $order->id, '_completed_date', $now );
                break; // only need to update once per order

            }
        }

    }

    /**
     * Record the date and time that an order has been marked as completed
     *
     * @param int $order_id
     */
    function order_completed( $order_id ) {
        update_post_meta( $order_id, '_order_date_completed', current_time('timestamp') );
    }

    /**
     * Adds the warranty to the item as item meta
     *
     * @access public
     * @param mixed $item_id
     * @param mixed $values
     * @return void
     */
    function order_item_meta( $item_id, $values ) {
        $_product       = $values['data'];
        $_prod_id       = ($_product->variation_id) ? $_product->variation_id : $_product->id;
        $warranty       = warranty_get_product_warranty( $_prod_id );
        $warranty_label = $warranty['label'];

        if ( $warranty ) {

            if ( $warranty['type'] == 'addon_warranty' ) {
                $warranty_index = isset($values['warranty_index']) ? $values['warranty_index'] : false;

                woocommerce_add_order_item_meta( $item_id, '_item_warranty_selected', $warranty_index );
            }

            woocommerce_add_order_item_meta( $item_id, '_item_warranty', $warranty );

        }

    }

    /**
     * Check if an order contain items that have valid warranties
     *
     * @param WC_Order $order
     * @return bool
     */
    public static function order_has_warranty( $order ) {
        global $woocommerce;

        $items          = $order->get_items();
        $has_warranty   = false;
        $now            = current_time('timestamp');
        $warranty       = false;
        $addon_index    = null;

        foreach ( $items as $item ) {
            $metas = (isset($item['item_meta'])) ? $item['item_meta'] : array();

            foreach ( $metas as $key => $value ) {
                if ( $key == '_item_warranty' ) {
                    $warranty = maybe_unserialize( $value[0] );
                } elseif ( $key == '_item_warranty_selected' ) {
                    $addon_index = $value[0];
                }
            }

            if ( $warranty !== false ) {
                if ( $warranty['type'] == 'addon_warranty' ) {
                    $valid_until    = false;
                    $addon          = (isset($warranty['addons'][$addon_index])) ? $warranty['addons'][$addon_index] : null;

                    if (! $addon ) {
                        continue;
                    }

                    // order's date of completion must be within the warranty period
                    $completed      = get_post_meta( $order->id, '_completed_date', true);

                    if (! empty($completed) ) {
                        $valid_until = strtotime( $completed .' +'. $addon['value'] .' '. $addon['duration'] );
                    }

                    if ( $valid_until && current_time('timestamp') < $valid_until ) {
                        $has_warranty = true;
                        break;
                    }
                } elseif ( $warranty['type'] == 'included_warranty' ) {
                    if ( $warranty['length'] == 'lifetime' ) {
                        $has_warranty = true;
                        break;
                    } else {
                        // order's date of completion must be within the warranty period
                        $valid_until    = false;
                        $completed      = get_post_meta( $order->id, '_completed_date', true);

                        if (! empty($completed) ) {
                            $valid_until = strtotime( $completed .' +'. $warranty['value'] .' '. $warranty['duration'] );
                        }

                        if ( $valid_until && current_time('timestamp') < $valid_until ) {
                            $has_warranty = true;
                            break;
                        }
                    }
                }
            }
        }

        if (! $has_warranty ) {
            $query_args     = array(
                'post_type'         => 'warranty_request',
                'orderby'           => 'date',
                'order'             => 'DESC'
            );

            $query_args['meta_query'][] = array(
                'key'       => '_order_id',
                'value'     => $order->id,
                'compare'   => '='
            );

            $wp_query = new WP_Query();
            $wp_query->query($query_args);

            $total_items = $wp_query->found_posts;
            wp_reset_postdata();

            if ( $total_items > 0 ) {
                $has_warranty = true;
            }
        }

        return apply_filters( 'order_has_warranty', $has_warranty, $order );
    }

}

new Warranty_Order();