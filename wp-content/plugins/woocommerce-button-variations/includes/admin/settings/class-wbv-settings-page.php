<?php
/**
 *
 *
 * @class WBV_Settings_Page
 * Base class for Settings pages.
 * 
 * @author CandleStudio
 * @category Settings
 * @package ButtonVariations\Admin\Settings
 * @version 1.3.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * WBV_Settings_Page class.
 */
class WBV_Settings_Page
{
    /**
     * Setting page label.
     *
     * @var string
     */
    protected $label = '';

    /**
     * Setting page key.
     *
     * @var string
     */
    protected $key = '';

    /**
     * Setting page key.
     *
     * @var string
     */
    protected $settings = array();

     /**
     * Add this page to settings.
     */
    public function add_settings_page( $pages ) {
        $pages[ $this->key ] = $this->label;

        return $pages;
    }
}
