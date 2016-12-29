<?php
/**
 * Announcement Model
 *
 * @package   Announcement_Bar
 * @author    Themify
 * @link      http://themify.me
 * @copyright 2013 announcement-bar
 */

/**
 * Model class
 * Data handling class that deals with all db operations
 * @package Announcement_Bar
 */

final class Announcement_Model {
	static public $option_name = 'announcement_bar_options';
	static public $slug = 'announcement-bar';
	static public $prefix_field = 'ab_';
	static public $default_options = array(
		'announcement_bar' => 'enable',
		'announcement_category' => 0,
		'abar_post_category' => 0,
		'ab_hide_title' => false,
		'ab_content_teaser_length' => 15,
		'ab_order_by' => 'date',
		'ab_order' => 'desc',
		'ab_query_number' => 10,
		'ab_visible' => 1,
		'ab_scroll' => 1,
		'ab_effect' => 'slide',
		'ab_auto_play' => 0,
		'ab_wrap' => 'yes',
		'ab_speed' => 'normal',
		'ab_slider_nav' => 'yes',
		'ab_pager' => 'yes',
		'ab_start_at' => '',
		'ab_end_at' => '',
		'ab_position' => array(
			'align' => 'top',
			'state' => 'absolute'
		),
		'ab_close_button' => 'toggleable',
		'ab_remember_close_state' => 0,
		'ab_text_alignment' => 'textleft',
		'ab_appearance' => 'presets',
		'ab_font_style' => 'default-font',
		'ab_design_style' => 'default-color'
	);
	static public $ab_custom_google_fonts;

	/**
	 * Plugin settings
	 * @return array
	 */
	static public function get_plugin_settings() {
		return get_option( self::$option_name, self::$default_options );
	}

	static public function get_plugin_setting_by_name( $name ) {
		$options = self::get_plugin_settings();
		$value = self::get_default_option( $name );
		if ( isset( $options[ $name ] ) && ! empty( $options[ $name ] ) ) {
			$value = $options[ $name ];
		}
		return $value;
	}

	/**
	 * Custom metabox settings in Announcement Post
	 * @return array
	 */
	static public function get_custom_field_announcements() {
		return $options = array(
			array(
				'type' => 'multi',
				'name' => '_multi_ab_hide_title',
				'title' => __('Post Title', 'announcement-bar'),
				'meta' => array(
					'fields' => array(
						// Background horizontal position
						array(
							'name'  => 'ab_hide_title',
							'label' => __('Hide post title', 'announcement-bar'),
							'description' => '',
							'type' 	=> 'checkbox',
							'meta'	=> array(),
							'before' => '',
							'after'  => ''
						)
					),
					'after' => ''
				),
				'before' => '',
				'after' => ''
			),
			array(
				'name' 		=> 'ab_start_at',
				'title' 	=> __('Start at', 'announcement-bar'),
				'description' => '',
				'type' 		=> 'date',
				'meta'		=> array(
					'default' => '',
					'pick' => __( 'Pick Date', 'themify' ),
					'close' => __( 'Done', 'themify' ),
					'clear' => __( 'Clear Date', 'themify' ),
					'time_format' => 'HH:mm:ss',
					'date_format' => 'yy-mm-dd',
					'timeseparator' => ' ',
					'required' => true
				)
			),
			array(
				'name' 		=> 'ab_end_at',
				'title' 	=> __('End at', 'announcement-bar'),
				'description' => __('Start and End Date fields are required', 'announcement-bar'),
				'type' 		=> 'date',
				'meta'		=> array(
					'default' => '',
					'pick' => __( 'Pick Date', 'themify' ),
					'close' => __( 'Done', 'themify' ),
					'clear' => __( 'Clear Date', 'themify' ),
					'time_format' => 'HH:mm:ss',
					'date_format' => 'yy-mm-dd',
					'timeseparator' => ' ',
					'required' => true
				)
			),
			array(
				'name' 		=> 'ab_external_link',
				'title' 	=> __('External Link', 'announcement-bar'),
				'description' => __('External link will add to the post title', 'announcement-bar'),
				'type' 		=> 'textbox',
				'meta'		=> array()
			),
			array(
				'name' 		=> 'ab_button_text',
				'title' 	=> __('Button Text', 'announcement-bar'),
				'description' => '',
				'type' 		=> 'textbox',
				'meta'		=> array()
			),
			array(
				'name' 		=> 'ab_button_link',
				'title' 	=> __('Button Link', 'announcement-bar'),
				'description' => '',
				'type' 		=> 'textbox',
				'meta'		=> array()
			)
		);
	}

	/**
	 * Custom Fields metabox settings in CPTS
	 * @return array
	 */
	static public function get_custom_field_cpts() {
		// Query Number
		$query_number = array();
		$number = array(1,2,3,4,5,6,7,8,9,10);
		foreach( $number as $n ) {
			$new_n = array( 'name' => $n, 'value' => $n );
			array_push( $query_number, $new_n );
		}

		// Auto Play
		$autoplay = array();
		array_push( $autoplay, array( 'name' => 'off', 'value' => 0) );
		foreach( $number as $an ) {
			$new_n = $an . ' secs';
			$new_an = array( 'name' => $new_n, 'value' => $an );
			array_push( $autoplay, $new_an );
		}

		$options = array(
			array(
				"name" 		=> "announcement_bar",	
				"title" 		=> __('Announcement Bar', 'announcement-bar'), 	
				"description" => "",
				"type" 		=> "radio",
				"enable_toggle" => true,
				"meta"		=> array(
					array('value' => 'default', 'name' => __('Default','announcement-bar'), 'selected' => true),
					array('value' => 'custom', 	'name' => __('Custom','announcement-bar')),
					array('value' => 'disable', 'name' => __('Disable','announcement-bar'))
				),
				'default_selected' => '',
				'class' => 'query-field-visible',
			),
			array(
				"name" 		=> "announcement_bar_display",	
				"title" 		=> __('Display', 'announcement-bar'), 	
				"description" => "",
				"type" 		=> "radio",
				"enable_toggle" => true,
				"meta"		=> array(
					array('value' => 'announcement', 'name' => __('Announcement','announcement-bar'), 'selected' => true),
					array('value' => 'post', 'name' => __('Post','announcement-bar')),
				),
				'default_selected' => '',
				'toggle'	=> array('custom-toggle')
			),
			// Description Default
			array(
				'name' 		=> 'default_notif',
				'title' 	=> false,
				'description' => sprintf( __('Configure <a href="%s" target="_blank">Announcement Bar</a> in the setting page', 'announcement-bar'), admin_url( 'admin.php?page=announcement-bar' ) ),
				'type' 		=> 'post_id_info',
				'toggle'	=> 'default-toggle'
			),
			// Announement Category
			array(
				'name' 		=> 'announcement_category',
				'title'		=> false,
				'description'	=> sprintf( __('Select a announcement category or enter multiple announcement category IDs (eg. 2,5,6). Enter 0 to display all announcement categories.<br />Add more <a href="%s" target="_blank">Announcement Post</a>', 'announcement-bar'), admin_url('post-new.php?post_type=announcement') ),
				'type'		=> 'query_category',
				'meta'		=> array('taxonomy' => 'announcement-category'),
				'toggle'	=> array('custom-toggle', 'announcement-toggle')
			),
			// Post Category
			array(
				'name' 		=> 'abar_post_category',
				'title'		=> false,
				'type'		=> 'query_category',
				'description' => '',
				'toggle'	=> array('custom-toggle', 'post-toggle')
			),
			// Hide Title
			array(
				'name' 		=> 'ab_hide_title',
				'title' 	=> __('Hide Post Title', 'announcement-bar'),
				'description' => '',
				'type' 		=> 'checkbox',
				'meta'		=> array(),
				'toggle'	=> array('custom-toggle')
			),
			// Order By
			array(
				'name' 		=> 'ab_order_by',
				'title'		=> __('Order By', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> array(
					array('name' => __('Date', 'announcement-bar'), 'value' => 'date', 'selected' => true),
					array('name' => __('Random', 'announcement-bar'), 'value' => 'rand'),
					array('name' => __('Author', 'announcement-bar'), 'value' => 'author'),
					array('name' => __('Post Title', 'announcement-bar'), 'value' => 'title'),
					array('name' => __('Comments Number', 'announcement-bar'), 'value' => 'comment_count'),
					array('name' => __('Modified Date', 'announcement-bar'), 'value' => 'modified'),
					array('name' => __('Post Slug', 'announcement-bar'), 'value' => 'name'),
					array('name' => __('Post ID', 'announcement-bar'), 'value' => 'ID')
				),
				'toggle' 	=> array('custom-toggle')
			),
			// Descending or Ascending Order
			array(
				'name' 		=> 'ab_order',
				'title'		=> __('Order', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> array(
					array('name' => __('Descending', 'announcement-bar'), 'value' => 'desc', 'selected' => true),
					array('name' => __('Ascending', 'announcement-bar'), 'value' => 'asc')
				),
				'toggle'	=> array('custom-toggle')
			),
			// Query Number
			array(
				'name' 		=> 'ab_query_number',
				'title'		=> __('Number of posts to query', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> $query_number,
				'toggle'	=> array('custom-toggle')
			),

			// Separator
			array(
				'name' => 'separator_slider',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<hr class="meta_fields_separator"/>'),
				'toggle' => array('custom-toggle')
			),

			// Slider Related
			array(
				'name' 		=> 'ab_visible',
				'title'		=> __('Number of visible posts at the same time', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> array(
					array( 'name' => '1', 'value' => 1),
					array( 'name' => '2', 'value' => 2),
					array( 'name' => '3', 'value' => 3),
					array( 'name' => '4', 'value' => 4),
					array( 'name' => '5', 'value' => 5),
					array( 'name' => '6', 'value' => 6),
					array( 'name' => '7', 'value' => 7)
				),
				'toggle'	=> array('custom-toggle')
			),
			array(
				'name' 		=> 'ab_scroll',
				'title'		=> __('Number of scroll posts at the same time', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> array(
					array( 'name' => '1', 'value' => 1),
					array( 'name' => '2', 'value' => 2),
					array( 'name' => '3', 'value' => 3),
					array( 'name' => '4', 'value' => 4),
					array( 'name' => '5', 'value' => 5),
					array( 'name' => '6', 'value' => 6),
					array( 'name' => '7', 'value' => 7)
				),
				'toggle'	=> array('custom-toggle')
			),
			array(
				'name' 		=> 'ab_effect',
				'title'		=> __('Transition Effect', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> array(
					array( 'name' => __('Slide', 'announcement-bar'), 'value' => 'slide', 'selected' => true ),
					array( 'name' => __('Fade', 'announcement-bar'), 'value' => 'fade' ),
					array( 'name' => __('Continuously', 'announcement-bar'), 'value' => 'continuously' )
				),
				'toggle'	=> array('custom-toggle')
			),
			array(
				'name' 		=> 'ab_auto_play',
				'title'		=> __('Auto Play', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> $autoplay,
				'toggle'	=> array('custom-toggle')
			),
			array(
				'name' 		=> 'ab_wrap',
				'title'		=> __('Wrap', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> array(
					array( 'name' => __('Yes', 'announcement-bar'), 'value' => 'yes' ),
					array( 'name' => __('No', 'announcement-bar'), 'value' => 'no' )
				),
				'toggle'	=> array('custom-toggle')
			),
			array(
				'name' 		=> 'ab_speed',
				'title'		=> __('Speed', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> array(
					array( 'name' => __('Normal', 'announcement-bar'), 'value' => 'normal' ),
					array( 'name' => __('Slow', 'announcement-bar'), 'value' => 'slow' ),
					array( 'name' => __('Fast', 'announcement-bar'), 'value' => 'fast' )
				),
				'toggle'	=> array('custom-toggle')
			),
			array(
				'name' 		=> 'ab_slider_nav',
				'title'		=> __('Slider Nav', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> array(
					array( 'name' => __('Yes', 'announcement-bar'), 'value' => 'yes' ),
					array( 'name' => __('No', 'announcement-bar'), 'value' => 'no' )
				),
				'toggle'	=> array('custom-toggle')
			),
			array(
				'name' 		=> 'ab_pager',
				'title'		=> __('Pager', 'announcement-bar'),
				'description'	=> '',
				'type'		=> 'dropdown',
				'meta'		=> array(
					array( 'name' => __('Yes', 'announcement-bar'), 'value' => 'yes' ),
					array( 'name' => __('No', 'announcement-bar'), 'value' => 'no' )
				),
				'toggle'	=> array('custom-toggle')
			),
			array(
				'name' 		=> 'ab_show_timer',
				'title' 	=> __('Show Timer', 'announcement-bar'),
				'description' => '',
				'type' 		=> 'checkbox',
				'meta'		=> array(),
				'toggle'	=> array('custom-toggle')
			),
			
			// Separator
			array(
				'name' => 'separator_datetime',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<hr class="meta_fields_separator"/>'),
				'toggle' => array('custom-toggle')
			),
			array(
				'name' 		=> 'ab_start_at',
				'title' 	=> __('Start at', 'announcement-bar'),
				'description' => '',
				'type' 		=> 'date',
				'meta'		=> array(
					'default' => '',
					'pick' => __( 'Pick Date', 'themify' ),
					'close' => __( 'Done', 'themify' ),
					'clear' => __( 'Clear Date', 'themify' ),
					'time_format' => 'HH:mm:ss',
					'date_format' => 'yy-mm-dd',
					'timeseparator' => ' '
				),
				'toggle'	=> array('custom-toggle'),
				'force_save' => true,
			),
			array(
				'name' 		=> 'ab_end_at',
				'title' 	=> __('End at', 'announcement-bar'),
				'description' => __('Start and End Date fields are required', 'announcement-bar'),
				'type' 		=> 'date',
				'meta'		=> array(
					'default' => '',
					'pick' => __( 'Pick Date', 'themify' ),
					'close' => __( 'Done', 'themify' ),
					'clear' => __( 'Clear Date', 'themify' ),
					'time_format' => 'HH:mm:ss',
					'date_format' => 'yy-mm-dd',
					'timeseparator' => ' '
				),
				'toggle'	=> array('custom-toggle'),
				'force_save' => true,
			),

			// Separator
			array(
				'name' => 'separator_position',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<hr class="meta_fields_separator"/>'),
				'toggle' => array('custom-toggle')
			),

			// Position
			array(
				'name' 			=> 'ab_position_y',
				'title'			=> __('Position', 'announcement-bar'),
				'description'	=> '',
				'type'			=> 'layout',
				'show_title' 	=> true,
				'meta'			=>  array(
					array('value' => 'top', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/bar-top.png', 'selected' => true, 'title' => __('Top', 'announcement-bar')),
					array('value' => 'bottom', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/bar-bottom.png', 'title' => __('Bottom', 'announcement-bar'))
				),
				'toggle' => array('custom-toggle')
			),
			array(
				"name" 		=> "ab_position",	
				"title" 	=> false, 	
				"description" => "",
				"type" 		=> "radio",
				"meta"		=> array(
					array('value' => 'absolute', 'name' => __('Absolute','announcement-bar'), 'selected' => true),
					array('value' => 'fixed', 'name' => __('Fixed','announcement-bar'))
				),
				'default_selected' => '',
				'toggle' => array('custom-toggle')
			),

			// Separator
			array(
				'name' => 'separator_closebtn',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<hr class="meta_fields_separator"/>'),
				'toggle' => array('custom-toggle')
			),
			// Close Button
			array(
				"name" 		=> "ab_close_button",	
				"title" 		=> __('Close Button', 'announcement-bar'), 	
				"description" => "",
				"type" 		=> "radio",
				"meta"		=> array(
					array('value' => 'toggleable', 'name' => __('Toggleable','announcement-bar'), 'selected' => true),
					array('value' => 'close', 	'name' => __('Close','announcement-bar')),
					array('value' => 'none', 'name' => __('None','announcement-bar'))
				),
				'default_selected' => '',
				'toggle' => array('custom-toggle')
			),
			array(
				'name' 		=> 'ab_remember_close_state',
				'title' 	=> false,
				'description' => __('Remember state on user\'s browser with cookie', 'announcement-bar'),
				'type' 		=> 'checkbox',
				'meta'		=> array(),
				'toggle'	=> array('custom-toggle')
			),

			// Separator
			array(
				'name' => 'separator_textalign',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<hr class="meta_fields_separator"/>'),
				'toggle' => array('custom-toggle')
			),

			array(
				'name' 			=> 'ab_text_alignment',
				'title'			=> __('Text Alignment', 'announcement-bar'),
				'description'	=> '',
				'type'			=> 'layout',
				'show_title' 	=> true,
				'meta'			=>  array(
					array('value' => 'textleft', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/text-align-left.png', 'selected' => true, 'title' => __('Left', 'announcement-bar')),
					array('value' => 'textcenter', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/text-align-center.png', 'title' => __('Center', 'announcement-bar')),
					array('value' => 'textright', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/text-align-right.png', 'title' => __('Right', 'announcement-bar'))
				),
				'toggle' => array('custom-toggle')
			),
			array(
				"name" 		=> "ab_appearance",	
				"title" 		=> __('Appearance', 'announcement-bar'), 	
				"description" => "",
				"type" 		=> "radio",
				"enable_toggle" => true,
				"meta"		=> array(
					array('value' => 'presets', 'name' => __('Default','announcement-bar'), 'selected' => true),
					array('value' => 'custom-appearance', 	'name' => __('Custom','announcement-bar'))
				),
				'default_selected' => '',
				'toggle'	=> array('enable_toggle_child', 'custom-toggle')
			),

			// Presets
			array(
				'name' 			=> 'ab_font_style',
				'title'			=> __('Font Style', 'announcement-bar'),
				'description'	=> '',
				'type'			=> 'layout',
				'show_title' 	=> true,
				'meta'			=>  array(
					array('value' => 'default-font', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/default.png', 'selected' => true, 'title' => __('Default', 'announcement-bar')),
					array('value' => 'serif', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/font-serif.png', 'title' => __('Serif', 'announcement-bar')),
					array('value' => 'old-style', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/font-old-style.png', 'title' => __('Old Style', 'announcement-bar')),
					array('value' => 'slab-serif', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/font-slab-serif.png', 'title' => __('Slab Serif', 'announcement-bar')),
					array('value' => 'script', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/font-script.png', 'title' => __('Script', 'announcement-bar'))
				),
				'toggle' => array('presets-toggle', 'custom-toggle')
			),
			array(
				'name' 			=> 'ab_design_style',
				'title'			=> __('Design Style', 'announcement-bar'),
				'description'	=> '',
				'type'			=> 'layout',
				'show_title' 	=> true,
				'meta'			=>  array(
					array('value' => 'default-color', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/color-default.png', 'selected' => true, 'title' => __('Default', 'announcement-bar')),
					array('value' => 'white', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/color-white.png', 'title' => __('White', 'announcement-bar')),
					array('value' => 'yellow', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/color-yellow.png', 'title' => __('Yellow', 'announcement-bar')),
					array('value' => 'blue', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/color-blue.png', 'title' => __('Blue', 'announcement-bar')),
					array('value' => 'green', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/color-green.png', 'title' => __('Green', 'announcement-bar')),
					array('value' => 'orange', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/color-orange.png', 'title' => __('Orange', 'announcement-bar')),
					array('value' => 'pink', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/color-pink.png', 'title' => __('Pink', 'announcement-bar')),
					array('value' => 'purple', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/color-purple.png', 'title' => __('Purple', 'announcement-bar')),
					array('value' => 'black', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/color-black.png', 'title' => __('Black', 'announcement-bar')),
					array('value' => 'gray', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/color-gray.png', 'title' => __('Gray', 'announcement-bar')),
					array('value' => 'paper', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/design-paper.png', 'title' => __('Paper', 'announcement-bar')),
					array('value' => 'notes', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/design-notes.png', 'title' => __('Notes', 'announcement-bar')),
					array('value' => 'clip', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/design-clip.png', 'title' => __('Clip', 'announcement-bar')),
					array('value' => 'bookmark', 'img' => ANNOUNCEMENT_BAR_PLUGIN_URL . 'images/layout-icons/design-bookmark.png', 'title' => __('Bookmark', 'announcement-bar'))
				),
				'toggle' => array('presets-toggle', 'custom-toggle')
			),

			// Custom
			array(
				'name' => 'bar_background_color',
				'title' => __('Bar Background', 'announcement-bar'),
				'description' => '',
				'type' => 'color',
				'meta' => array('default'=>null),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			array(
				'name' 		=> 'bar_background_color_transparent',
				'title' 	=> false,
				'description' => __('Transparent', 'announcement-bar'),
				'type' 		=> 'checkbox',
				'meta'		=> array(),
				'toggle'	=> array('custom-appearance-toggle', 'custom-toggle')
			),
			// Background image
			array(
				'name' 	=> 'bar_background_image',
				'title' => false,
				'type' 	=> 'image',
				'description' => '',
				'meta'	=> array(),
				'before' => '',
				'after' => '',
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			// Background repeat
			array(
				'name' 		=> 'bar_background_repeat',
				'title'		=> __('Background Repeat', 'announcement-bar'),
				'description'	=> '',
				'type' 		=> 'dropdown',
				'meta'		=> array(
					array('value' => 'repeat', 'name' => __('Repeat', 'announcement-bar'), 'selected' => true),
					array('value' => 'repeat-x', 'name' => __('Repeat horizontally', 'announcement-bar')),
					array('value' => 'repeat-y', 'name' => __('Repeat vertically', 'announcement-bar')),
					array('value' => 'no-repeat', 'name' => __('Do not repeat', 'announcement-bar'))
				),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			// Multi field: Background Position
			array(
				'type' => 'multi',
				'name' => '_multi_bar_bg_position',
				'title' => __('Background Position', 'announcement-bar'),
				'meta' => array(
					'fields' => array(
						// Background horizontal position
						array(
							'name'  => 'bar_background_position_x',
							'label' => '',
							'description' => '',
							'type' 	=> 'dropdown',
							'meta'	=> array(
								array('value' => '',   'name' => '', 'selected' => true),
								array('value' => 'left',   'name' => __('Left', 'announcement-bar')),
								array('value' => 'center', 'name' => __('Center', 'announcement-bar')),
								array('value' => 'right',  'name' => __('Right', 'announcement-bar'))
							),
							'before' => '',
							'after'  => ''
						),
						// Background vertical position
						array(
							'name'  => 'bar_background_position_y',
							'label' => '',
							'description' => '',
							'type' 	=> 'dropdown',
							'meta'	=> array(
								array('value' => '',   'name' => '', 'selected' => true),
								array('value' => 'top',   'name' => __('Top', 'announcement-bar')),
								array('value' => 'center', 'name' => __('Center', 'announcement-bar')),
								array('value' => 'bottom',  'name' => __('Bottom', 'announcement-bar'))
							),
							'before' => '',
							'after'  => ''
						),
					),
					'description' => '',
					'before' => '',
					'after' => '',
					'separator' => ''
				),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),

			// Separator
			array(
				'name' => 'separator_annountitle',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Announcement Title', 'announcement-bar').'</h4><hr class="meta_fields_separator"/>'),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),

			// Multi field: Font
			array(
				'type' => 'multi',
				'name' => '_announcement_title_font',
				'title' => __('Font', 'announcement-bar'),
				'meta' => array(
					'fields' => array(
						// Font size
						array(
							'name' => 'announcement_title_font',
							'label' => '',
							'description' => '',
							'type' => 'textbox',
							'meta' => array('size'=>'small'),
							'before' => '',
							'after' => ''
						),
						// Font size unit
						array(
							'name' 	=> 'announcement_title_font_size_unit',
							'label' => '',
							'type' 	=> 'dropdown',
							'meta'	=> array(
								array('value' => 'px', 'name' => __('px', 'announcement-bar'), 'selected' => true),
								array('value' => 'em', 'name' => __('em', 'announcement-bar'))
							),
							'before' => '',
							'after' => ''
						),
						// Font family
						array(
							'name' 	=> 'announcement_title_font_family',
							'label' => '',
							'type' 	=> 'dropdown',
							'meta'	=> array_merge( themify_get_web_safe_font_list(), themify_get_google_web_fonts_list() ),
							'before' => '',
							'after' => '',
						),
					),
					'description' => '',
					'before' => '',
					'after' => '',
					'separator' => ''
				),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),

			// Font Color
			array(
				'name' => 'announcement_title_color',
				'title' => __('Color', 'announcement-bar'),
				'description' => '',
				'type' => 'color',
				'meta' => array('default'=>null),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			// Link Color
			array(
				'name' => 'announcement_title_background_color',
				'title' => __('Background', 'announcement-bar'),
				'description' => '',
				'type' => 'color',
				'meta' => array('default'=>null),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			array(
				'name' 		=> 'announcement_title_background_color_transparent',
				'title' 	=> false,
				'description' => __('Transparent', 'announcement-bar'),
				'type' 		=> 'checkbox',
				'meta'		=> array(),
				'toggle'	=> array('custom-appearance-toggle', 'custom-toggle')
			),
			// Background image
			array(
				'name' 	=> 'announcement_title_background_image',
				'title' => false,
				'type' 	=> 'image',
				'description' => '',
				'meta'	=> array(),
				'before' => '',
				'after' => '',
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			// Background repeat
			array(
				'name' 		=> 'announcement_title_background_repeat',
				'title'		=> __('Background Repeat', 'announcement-bar'),
				'description'	=> '',
				'type' 		=> 'dropdown',
				'meta'		=> array(
					array('value' => 'repeat', 'name' => __('Repeat', 'announcement-bar'), 'selected' => true),
					array('value' => 'repeat-x', 'name' => __('Repeat horizontally', 'announcement-bar')),
					array('value' => 'repeat-y', 'name' => __('Repeat vertically', 'announcement-bar')),
					array('value' => 'no-repeat', 'name' => __('Do not repeat', 'announcement-bar'))
				),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			// Multi field: Background Position
			array(
				'type' => 'multi',
				'name' => '_multi_announcement_title_bg_position',
				'title' => __('Background Position', 'announcement-bar'),
				'meta' => array(
					'fields' => array(
						// Background horizontal position
						array(
							'name'  => 'announcement_title_background_position_x',
							'label' => '',
							'description' => '',
							'type' 	=> 'dropdown',
							'meta'	=> array(
								array('value' => '',   'name' => '', 'selected' => true),
								array('value' => 'left',   'name' => __('Left', 'announcement-bar')),
								array('value' => 'center', 'name' => __('Center', 'announcement-bar')),
								array('value' => 'right',  'name' => __('Right', 'announcement-bar'))
							),
							'before' => '',
							'after'  => ''
						),
						// Background vertical position
						array(
							'name'  => 'announcement_title_background_position_y',
							'label' => '',
							'description' => '',
							'type' 	=> 'dropdown',
							'meta'	=> array(
								array('value' => '',   'name' => '', 'selected' => true),
								array('value' => 'top',   'name' => __('Top', 'announcement-bar')),
								array('value' => 'center', 'name' => __('Center', 'announcement-bar')),
								array('value' => 'bottom',  'name' => __('Bottom', 'announcement-bar'))
							),
							'before' => '',
							'after'  => ''
						),
					),
					'description' => '',
					'before' => '',
					'after' => '',
					'separator' => ''
				),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),

			// Separator
			array(
				'name' => 'separator_announ_content',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Announcement Content', 'announcement-bar').'</h4><hr class="meta_fields_separator"/>'),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),

			// Multi field: Font
			array(
				'type' => 'multi',
				'name' => '_announcement_content_font',
				'title' => __('Font', 'announcement-bar'),
				'meta' => array(
					'fields' => array(
						// Font size
						array(
							'name' => 'announcement_content_font',
							'label' => '',
							'description' => '',
							'type' => 'textbox',
							'meta' => array('size'=>'small'),
							'before' => '',
							'after' => ''
						),
						// Font size unit
						array(
							'name' 	=> 'announcement_content_font_size_unit',
							'label' => '',
							'type' 	=> 'dropdown',
							'meta'	=> array(
								array('value' => 'px', 'name' => __('px', 'announcement-bar'), 'selected' => true),
								array('value' => 'em', 'name' => __('em', 'announcement-bar'))
							),
							'before' => '',
							'after' => ''
						),
						// Font family
						array(
							'name' 	=> 'announcement_content_font_family',
							'label' => '',
							'type' 	=> 'dropdown',
							'meta'	=> array_merge( themify_get_web_safe_font_list(), themify_get_google_web_fonts_list() ),
							'before' => '',
							'after' => '',
						),
					),
					'description' => '',
					'before' => '',
					'after' => '',
					'separator' => ''
				),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),

			// Font Color
			array(
				'name' => 'announcement_content_color',
				'title' => __('Color', 'announcement-bar'),
				'description' => '',
				'type' => 'color',
				'meta' => array('default'=>null),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			// Link Color
			array(
				'name' => 'announcement_content_link_color',
				'title' => __('Link Color', 'announcement-bar'),
				'description' => '',
				'type' => 'color',
				'meta' => array('default'=>null),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			array(
				'name'  => 'announcement_content_link_decoration',
				'title' => __('Link Decoration', 'announcement-bar'),
				'description' => '',
				'type' 	=> 'dropdown',
				'meta'	=> array(
					array('value' => '',   'name' => '', 'selected' => true),
					array('value' => 'none',   'name' => __('None', 'announcement-bar')),
					array('value' => 'underline', 'name' => __('Underline', 'announcement-bar')),
					array('value' => 'overline',  'name' => __('Overline', 'announcement-bar')),
					array('value' => 'line-through',  'name' => __('Line Through', 'announcement-bar'))
				),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			// Link Color
			array(
				'name' => 'announcement_content_link_hover_color',
				'title' => __('Link Hover Color', 'announcement-bar'),
				'description' => '',
				'type' => 'color',
				'meta' => array('default'=>null),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			),
			array(
				'name'  => 'announcement_content_link_hover_decoration',
				'title' => __('Link Hover Decoration', 'announcement-bar'),
				'description' => '',
				'type' 	=> 'dropdown',
				'meta'	=> array(
					array('value' => '',   'name' => '', 'selected' => true),
					array('value' => 'none',   'name' => __('None', 'announcement-bar')),
					array('value' => 'underline', 'name' => __('Underline', 'announcement-bar')),
					array('value' => 'overline',  'name' => __('Overline', 'announcement-bar')),
					array('value' => 'line-through',  'name' => __('Line Through', 'announcement-bar'))
				),
				'toggle' => array('custom-appearance-toggle', 'custom-toggle')
			)
		);
		return $options;
	}

	/**
	 * Get available post types
	 * @return array
	 */
	static public function get_available_post_types(){
		$custom = get_post_types( array( 'public' => true, '_builtin' => false ) );
		$custom_post_types = array();
		$builtin = array( 'page', 'post' );
		$exclude_post_types = array( 'tbuilder_layout', 'tbuilder_layout_part' );
		foreach ( $custom as $c ) {
			if ( $c !== 'announcement' && ! in_array( $c, $exclude_post_types ) ) {
				array_push( $custom_post_types, $c );
			}
		}
		return apply_filters( 'announcement_bar_available_post_types', array_merge( $custom_post_types, $builtin ) );
	}

	/**
	 * Set Google Fonts
	 * @param string $font 
	 * @return string
	 */
	static public function set_google_fonts( $font ) {
		if( empty( $font ) || 'default' == $font ) return;
		if ( ! in_array( $font, themify_get_web_safe_font_list( true ) ) ) {
			self::$ab_custom_google_fonts .= str_replace( ' ', '+', $font.'|' );
		}
	}

	/**
	 * Get default settings
	 * @param string $name 
	 * @return string|int|bool
	 */
	static public function get_default_option( $name ) {
		return self::$default_options[ $name ];
	}

}