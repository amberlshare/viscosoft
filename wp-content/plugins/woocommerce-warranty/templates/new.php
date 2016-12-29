<?php global $wc_warranty; ?>
<div class="wrap woocommerce">

    <h2><?php _e('New Warranty Request', 'wc_warranty'); ?></h2>

    <div id="search_form" <?php if ($searched || $form_view) echo 'style="display:none;"'; ?>>
        <form action="admin.php" id="search_form" method="get">
            <h4><?php _e('Search for an Order', 'wc_warranty'); ?></h4>

            <input type="hidden" name="page" value="warranties-new" />

            <p>
                <select name="search_key" id="search_key">
                    <option value="order_id"><?php _e('Order Number', 'wc_warranty'); ?></option>
                    <option value="customer"><?php _e('Customer Name or Email', 'wc_warranty'); ?></option>
                </select>

                <input type="text" name="search_term" id="search_term" value="" class="short" />
                <input
                    type="hidden"
                    id="select2_search_term"
                    data-placeholder="<?php _e('Search for a customer...', 'wc_warranty'); ?>"
                    style="width: 400px; display: inline-block !important;" />

                <input type="submit" id="order_search_button" class="button-primary" value="<?php _e('Search', 'wc_warranty'); ?>" />
            </p>
        </form>
    </div>
<?php if ($searched || $form_view): ?>
    <p><input type="button" class="toggle_search_form button" value="Show Search Form" /></p>
<?php endif; ?>

<?php if ( $searched && empty($orders) ): ?>
    <div class="error"><p><?php _e('No orders found', 'wc_warranty'); ?></p></div>
<?php endif; ?>  

<?php if ( !empty($orders) ): ?>
    <table class="wp-list-table widefat fixed warranty" cellspacing="0">
        <thead>
            <tr>
                <th scope="col" id="order_id" class="manage-column column-order_id"><?php _e('Order ID', 'wc_warranty'); ?></th>
                <th scope="col" id="order_customer" class="manage-column column-order_customer"><?php _e('Customer', 'wc_warranty'); ?></th>
                <th scope="col" id="order_status" class="manage-column column-status"><?php _e('Order Status', 'wc_warranty'); ?></th>
                <th scope="col" id="order_items" class="manage-column column-order_items"><?php _e('Order Items', 'wc_warranty'); ?></th>
                <th scope="col" id="order_date" class="manage-column column-order_items"><?php _e('Date', 'wc_warranty'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ( $orders as $order_id):
                $order = WC_Warranty_Compatibility::wc_get_order( $order_id );

                if ( !$order ) {
                    continue;
                }

                $has_warranty = Warranty_Order::order_has_warranty($order);

            ?>
            <tr class="alternate">
                <td class="order_id column-order_id"><a href="post.php?post=<?php echo $order->id; ?>&action=edit"><?php echo $order->get_order_number(); ?></a></td>
                <td class="order_id column-order_customer"><?php echo $order->billing_first_name .' '. $order->billing_last_name; ?></td>
                <td class="order_status column-status"><?php echo WC_Warranty_Compatibility::get_order_status( $order ); ?></td>
                <td class="order_items column-order_items">
                    <form method="get" action="admin.php">
                        <ul class="order-items">
                        <?php
                        foreach ( $order->get_items() as $item_idx => $item ):
                            $item_id = (isset($item['product_id'])) ? $item['product_id'] : $item['id'];

                            // variation support
                            if ( isset($item['variation_id']) && $item['variation_id'] > 0 ) {
                                $item_id = $item['variation_id'];
                            }

                            $product = WC_Warranty_Compatibility::wc_get_product($item_id);

                            if ( $has_warranty && $item['qty'] > 1 ) {
                                $max = warranty_get_quantity_remaining( $order->id, $item_id, $item_idx );
                            } else {
                                $max = $item['qty'] - warranty_count_quantity_used( $order->id, $item_id, $item_idx );
                            }

                            if ( $max < 1 ) {
                                continue;
                            }
                        ?>
                            <li>
                                <input type="checkbox" name="idx[]" value="<?php echo $item_idx; ?>" />
                                <?php echo $product->get_title(); ?>
                                <?php if (isset($item['Warranty'])): ?>
                                <span class="description">(Warranty: <?php echo $item['Warranty']; ?>)</span>
                                <?php endif; ?>
                                &times;
                                <?php echo $item['qty']; ?>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                        <input type="hidden" name="page" value="warranties-new" />
                        <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
                        <input type="submit" class="button" value="<?php _e('Create Request', 'wc_warranty'); ?>" />
                    </form>
                </td>
                <td class="order_id column-order_date"><?php echo $order->order_date; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
if ( isset($_GET['order_id'],  $_GET['idx']) ):
    if ( isset($_GET['error']) ) {
        echo '<div class="error"><p>'. $_GET['error'] .'</p></div>';
    }

    $order          = WC_Warranty_Compatibility::wc_get_order( $_GET['order_id'] );
    $has_warranty   = Warranty_Order::order_has_warranty($order);
    $items          = $order->get_items();

    include WooCommerce_Warranty::$base_path .'/templates/admin/new-warranty-form.php';
endif;
?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $(".toggle_search_form").click(function() {
            if ( $("#search_form").is(":visible") ) {
                $(this).val("Show Search Form");
                $("#search_form").hide();
            } else {
                $(this).val("Hide Search Form");
                $("#search_form").show();
            }
        });

        $("#search_key").change(function() {
            if ($(this).val() == "order_id") {
                $("#search_term").show();
                $("#select2_search_term")
                    .removeClass("wc-user-search")
                    .removeClass("enhanced")
                    .select2("destroy");
            } else {
                $("#search_term").hide();
                $("#select2_search_term").addClass("wc-user-search");
                $("body").trigger("wc-enhanced-select-init");
            }
        }).change();

        $(".help_tip").tipTip();

        $("#search_form").submit(function() {
            if ( $("#search_key").val() == "customer" ) {
                $("#search_term").val( $("#select2_search_term").val() );
            }
        });
    });
    </script>
</div>