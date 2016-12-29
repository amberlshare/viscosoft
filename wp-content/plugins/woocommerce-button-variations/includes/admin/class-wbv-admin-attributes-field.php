<?php

/**
 * Class to handle fields in WC_Extended_Attributes
 *
 * @author   CandleStudio
 * @category Admin
 * @package  ButtonVariations\Admin
 * @version  1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WBV_Admin_Attributes_Field Class.
 */
class WBV_Admin_Attributes_Field {

  public $id;
  public $name;
  public $type;

  public function __construct($args) {

    if ( isset( $args['id'] ) ) { $this->id = $args['id']; }
    if ( isset( $args['name'] ) ) { $this->name = $args['name']; }
    if ( isset( $args['type'] ) ) { $this->type = $args['type']; }
    
  }

}