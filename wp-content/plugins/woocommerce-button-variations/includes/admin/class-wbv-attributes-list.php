<?php

/**
 *  Display table of taxonomies in Attribute tab in admin menu page.
 * it extends the WP_List_Table core class.
 * 
 * @author CandleStudio
 * @category Admin
 * @package ButtonVariations\Admin
 * @version 1.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * WBV_Attributes_List Class.
 */
class WBV_Attributes_List extends WP_List_Table
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'wbv_attributes_list_item',
            'plural'   => 'wbv_attributes_list_items',
            'ajax'     => false,
        ));
    }

    public function get_columns()
    {
        $columns = array(
            'attribute_id'          => 'ID',
            'attribute_label'       => __('Label', 'wc-ea-domain'),
            'wc-ea-domain_att_type' => __('Extended Type', 'wc-ea-domain'),
            'add_edit'              => '',
        );
        return $columns;
    }

    public function extra_tablenav($which)
    {
        if ($which == "top") {

            echo __('Edit the type of button to display for Variations (Attributes).', 'wc-ea-domain');
        }
    }

    public function prepare_items()
    {

        if ($attribute_taxonomies = wc_get_attribute_taxonomies()) {

            $columns               = $this->get_columns();
            $hidden                = array();
            $sortable              = array();
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items           = $attribute_taxonomies;
        }

    }

    public function column_default($item, $column_name)
    {
        global $wc_ea_att_options_prefix;

        $name = wc_attribute_taxonomy_name($item->attribute_name);
        $link = '<a href=edit.php?post_type=product&page=wc_ea_plugin_options&act=tax_edit&taxonomy=' . $name . ' class="c_field_edit" title="' . __('Edit', 'wc-ea-domain') . '">' . __('Edit', 'wc-ea-domain') . '</a>';

        switch ($column_name) {
            case 'attribute_id':
            case 'attribute_label':
                return $item->$column_name;
            case 'wc-ea-domain_att_type':

                if ($option = get_option($wc_ea_att_options_prefix . '_' . $name)) {

                    if ($option === 'color') {
                        return __('Color', 'wc-ea-domain') . ' | ' . $link;
                    } else if ($option === 'text') {
                        return __('Text', 'wc-ea-domain') . ' | ' . $link;
                    } else {
                        return __('Image', 'wc-ea-domain') . ' | ' . $link;
                    }

                } else {
                    return __('Default (Text)', 'wc-ea-domain') . ' | ' . $link;
                }
            case 'add_edit':
                return '<a href=edit-tags.php?taxonomy=' . $name . '&post_type=product>' . __('View Terms', 'wc-ea-domain') . '</a>';

            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }
}
