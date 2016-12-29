<?php
/**
 *
 * Manage Button Variations integration with Product > Attributes.
 * @author   CandleStudio
 * @category Admin
 * @package  ButtonVariations\Admin
 * @version  1.3.0
 *
 */

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * WBV_Admin_Taxonomies Class.
 */
class WBV_Admin_Taxonomies
{

    protected $_attributes;
    protected $_fields;
    protected $_form_type;
    protected $wc_ea_term_meta;

    /**
     *
     * 1. Init the types of buttons allowed (color/swatches, text).
     * 2. Init the admin panel.
     * 3. Init the ajax functionality.
     *
     */
    public function __construct()
    {
        add_action('init', array($this, 'includes'));
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'add'));
    }

    public function init()
    {
        $this->_fields   = array();
        $newColorField   = new WBV_Admin_Attributes_Field(array('name' => 'Color', 'type' => 'color', 'id' => 'wc_ea_color'));
        $this->_fields[] = $newColorField;

        $newTextField    = new WBV_Admin_Attributes_Field(array('name' => 'Text', 'type' => 'text', 'id' => 'wc_ea_text'));
        $this->_fields[] = $newTextField;

        $newImageField   = new WBV_Admin_Attributes_Field(array('name' => 'Image', 'type' => 'image', 'id' => 'wc_ea_image'));
        $this->_fields[] = $newImageField;
    }

    public function includes()
    {
        include_once 'class-wbv-admin-attributes-field.php';
        include_once 'class-wbv-attributes-list.php';
    }

    /**
     *
     * Hook tax actions.
     *
     * @return  void
     *
     */
    public function add()
    {

        global $woocommerce, $wc_ea_term_meta;

        $wc_product_attributes = array();

        $this->wc_ea_term_meta = $wc_ea_term_meta ? $wc_ea_term_meta : 'wc_ea_term_meta_';

        if ($attribute_taxonomies = wc_get_attribute_taxonomies()) {
            foreach ($attribute_taxonomies as $tax) {
                if ($name = wc_attribute_taxonomy_name($tax->attribute_name)) {

                    //add fields to edit form
                    add_action($name . '_edit_form_fields', array($this, 'show_edit_form'));

                    //add fields to add new form
                    add_action($name . '_add_form_fields', array($this, 'show_new_form'));

                    // this saves the edit fields
                    add_action('edited_' . $name, array($this, 'save'), 10, 2);

                    // this saves the add fields
                    add_action('created_' . $name, array($this, 'save'), 10, 2);

                    // manage and edit columns for taxonomy
                    //TODO check if these are the filter and action we need to use.
                    add_filter('manage_edit-' . $name . '_columns', array($this, 'manage_posts_columns'));
                    add_action('manage_' . $name . '_custom_column', array($this, 'manage_custom_column'), 10, 3);

                }
            }
        }
    }

    /**
     *
     * Hooked function.
     * Add columns to taxonomy management table.
     *
     * @param  array $columns list of table columns.
     * @return array $columns  list of table columns.
     *
     */
    public function manage_posts_columns($columns)
    {
        foreach ($this->_fields as $field) {
            $meta = $this->wc_ea_get_term_meta($term_id, $field->id);

            $columns['col_' . $field->id] = $field->name;

        }

        return $columns;
    }

    /**
     *
     * Hooked function.
     * Set the values for the default taxonomy manager table.
     *
     * @param  [type] $out     [description]
     * @param  [type] $column  [description]
     * @param  [type] $term_id [description]
     * @return [type]          [description]
     */
    public function manage_custom_column($out, $column, $term_id)
    {

        foreach ($this->_fields as $field) {
            $meta = $this->wc_ea_get_term_meta($term_id, $field->id);

            if ($column == 'col_' . $field->id) {

                $show_col_func = 'show_column_' . $field->type;
                if (method_exists($this, $show_col_func)) {
                    $out = call_user_func(array($this, 'show_column_' . $field->type), $meta);
                }
            }
        }

        return $out;
    }

    /**
     *
     * Hooked function.
     * Display color value in default taxonomy manager table column.
     *
     * @param  string $meta HEX value
     * @return HTML
     *
     */
    public function show_column_color($meta)
    {
        return '<div class="wc-ea-color-box" style="text-align:center; margin:0 auto;background-color:' . $meta . '; height:32px;width:32px;"</div>';
    }

    /**
     *
     * Hooked function.
     * Display text value in default taxonomy manager table column.
     *
     * @param  string $meta String value
     * @return HTML
     *
     */
    public function show_column_text($meta)
    {
        return '<div class="wc-ea-text-field"><span>' . $meta . '</span></div>';
    }

    /**
     *
     * Hooked function.
     * Display image in default taxonomy manager table column.
     *
     * @param  string $meta String value
     * @return HTML
     *
     */
    public function show_column_image($meta)
    {
        if ($meta) {
            $image = wp_get_attachment_thumb_url($meta);
            return '<div class="wc-ea-image-field"><span style="display: block; width: 48px; height: 48px;"><img style="height: auto;    width: 100%;" src="' . $image . '" /></span></div>';
        }
    }

    /**
     *
     * Display tax edit form.
     *
     * @param  int $term_id
     * @return void
     *
     */
    public function show_edit_form($term_id)
    {

        $this->_form_type = 'edit';
        $this->show($term_id);
    }

    /**
     *
     * Display new tax form.
     *
     * @param  int $term_id
     * @return void
     *
     */
    public function show_new_form($term_id)
    {

        $this->_form_type = 'new';
        $this->show($term_id);
    }

    /**
     * Callback function to show fields in meta box.
     *
     * @since 1.0
     * @access public
     *
     */
    public function show($term_id)
    {
        wp_nonce_field(basename(__FILE__), 'wbv_attributes_nonce');
        foreach ($this->_fields as $field) {
            $meta = $this->wc_ea_get_term_meta($term_id, $field->id);

            $save_func = 'show_field_' . $field->type;
            if (method_exists($this, $save_func)) {
                call_user_func(array($this, 'show_field_' . $field->type), $field, $meta);
            }
        }
    }

    /**
     *
     * save attributes function.
     *
     * @param  [type] $term_id [description]
     * @return [type]          [description]
     *
     */
    public function save($term_id)
    {

        // check if the we are coming from quick edit issue #38 props to Nicola Peluchetti.
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'inline-save-tax') {
            return $term_id;
        }

        if (!isset($term_id) // Check Revision
             || (!isset($_POST['taxonomy'])) // Check if current taxonomy type is set.
            //|| ( ! in_array( $_POST['taxonomy'], $this->_meta_box['pages'] ) )              // Check if current taxonomy type is supported.
             || (!check_admin_referer(basename(__FILE__), 'wbv_attributes_nonce')) // Check nonce - Security
             || (!current_user_can('manage_categories'))) // Check permission
        {
            return $term_id;
        }

        foreach ($this->_fields as $field) {

            $old       = $this->wc_ea_get_term_meta($term_id, $field->id);
            $new       = (isset($_POST[$field->id])) ? $_POST[$field->id] : '';
            $save_func = 'save_field_' . $field->type;
            if (method_exists($this, $save_func)) {
                call_user_func(array($this, 'save_field_' . $field->type), $term_id, $field, $old, $new);
            } else {
                $this->save_field($term_id, $field->id, $old, $new);
            }

        }

    }

    /**
     *
     * [save_field description]
     * @param  [type] $term_id  [description]
     * @param  [type] $field_id [description]
     * @param  [type] $old      [description]
     * @param  [type] $new      [description]
     * @return [type]           [description]
     */
    public function save_field($term_id, $field_id, $old, $new)
    {

        $this->wc_ea_delete_term_meta($term_id, $field_id);
        if ($new === '' || $new === array()) {
            return;
        }

        $this->wc_ea_update_term_meta($term_id, $field_id, $new);
    }

    /**
     *
     * Fetch the term meta from Options API
     *
     * @param  [type] $term_id  [description]
     * @param  [type] $field_id [description]
     * @return [type]           [description]
     *
     */
    public function wc_ea_get_term_meta($term_id, $field_id)
    {

        $t_id = (is_object($term_id)) ? $term_id->term_id : $term_id;

        $m = get_option($this->wc_ea_term_meta . $t_id);

        if (isset($m[$field_id])) {
            return $m[$field_id];
        } else {
            return '';
        }
    }

    /**
     *
     * [wc_ea_delete_term_meta description]
     * @param  [type] $term_id  [description]
     * @param  [type] $field_id [description]
     * @return [type]           [description]
     */
    public function wc_ea_delete_term_meta($term_id, $field_id)
    {
        $m = get_option($this->wc_ea_term_meta . $term_id);
        if (isset($m[$field_id])) {
            unset($m[$field_id]);
        }
        update_option($this->wc_ea_term_meta . $term_id, $m);
    }

    /**
     *
     * [wc_ea_update_term_meta description]
     * @param  [type] $term_id  [description]
     * @param  [type] $field_id [description]
     * @param  [type] $value    [description]
     * @return [type]           [description]
     */
    public function wc_ea_update_term_meta($term_id, $field_id, $value)
    {
        $m            = get_option($this->wc_ea_term_meta . $term_id);
        $m[$field_id] = $value;
        update_option($this->wc_ea_term_meta . $term_id, $m);
    }

    /**
     *
     * Display the color control.
     *
     * @param  object $field instance of WC_Extended_Attributes_Field.
     * @param  string $meta  Value of text.
     * @return null
     *
     */
    public function show_field_color($field, $meta)
    {
        $this->show_field_begin($field->type);
        ?>
      <td>
        <div id="colorpicker">
            <input class="color-picker" id="<?php echo $field->id ?>" name="<?php echo $field->id ?>" type="text" value="<?php echo $meta ?>" />
        </div>
        <p class="description"><?php _e('This color will be associated with this term in Button Variations.', 'wc-ea-domain');?></p>
        <br /><br />
      </td>

<?php
$this->show_field_end();
    }

    /**
     *
     * Display the text field in terms editing page.
     * @param  [type] $field [description]
     * @param  [type] $meta  [description]
     * @return [type]        [description]
     */
    public function show_field_text($field, $meta)
    {
        $this->show_field_begin($field->type);
        ?>
      <td>

          <input class="" id="<?php echo $field->id ?>" name="<?php echo $field->id ?>" type="text" value="<?php echo $meta ?>" />

          <p class="description"><?php _e('This text will be associated with this term in Button Variations.', 'wc-ea-domain');?></>
          <br /><br />
      </td>
<?php
$this->show_field_end();
    }

    /**
     *
     * Display the image in terms editing page.
     * @param  [type] $field [description]
     * @param  [type] $meta  [description]
     * @return [type]        [description]
     */
    public function show_field_image($field, $meta)
    {
        $this->show_field_begin($field->type);
        $image = $meta ? wp_get_attachment_thumb_url($meta) : '';
        ?>
      <td>
        <div class="upload_image">

           <a href="#" id="<?php echo $field->id ?>" class="upload_image_button <?php if ($meta > 0) {
            echo 'remove';
        }
        ?>" data-tip="<?php echo __('Remove this image', 'woocommerce'); ?>"><img src="<?php if (!empty($image)) {
            echo esc_attr($image);
        } else {
            echo esc_attr(wc_placeholder_img_src());
        }
        ?>" /><input type="hidden" name="<?php echo $field->id; ?>" class="upload_image_id" value="<?php echo esc_attr($meta); ?>" /></a>

        </div>
          <p class="description"><?php _e('This image will be associated with this term in Button Variations.', 'wc-ea-domain');?></p>
          <br /><br />
        </td>
<?php
$this->show_field_end();
    }

    /**
     *
     *
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function show_field_begin($name)
    {
        if ($this->_form_type == 'new') {
            ?>
         <div class="wc_ea_term_controls">
<?php
} else {

        }
        ?>
      <tr class="form-field wc_ea_term_controls">
        <th scope="row" valign="top">
          <label for="meta-color"><?php _e('Term ' . ucfirst(strtolower($name)));?></label>
        </th>

<?php
}

    /**
     *
     * [show_field_end description]
     * @return [type] [description]
     */
    public function show_field_end()
    {
        if ($this->_form_type == 'new') {
            ?>
      </div>
<?php
} else {
            ?>
      </tr>
<?php
}
    }

}

return new WBV_Admin_Taxonomies();