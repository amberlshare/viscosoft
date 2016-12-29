<?php
/**
 * Announcement Post Type
 *
 * @package   Announcement_Bar
 * @author    announcement-bar
 * @link      http://themify.me
 * @copyright 2013 announcement-bar
 */

/**
 * Plugin class
 * @package Announcement_Bar
 */
class Announcement_Post_Type {
	var $post_type = 'announcement';
	var $is_themify_themes = false;

	/**
	 * Constructor
	 */
	public function __construct(){
		add_action( 'init', array( &$this, 'init' ) );
	}

	public function init() {
		
		// Check theme compatibility
		if ( class_exists( 'Themify' ) ) {
			$this->is_themify_themes = true;
		} else {
			$this->is_themify_themes = false;
		}

		add_filter( 'themify_do_metaboxes', array( &$this, 'create_meta_box_in_announ') );
		add_filter( 'themify_do_metaboxes', array( &$this, 'create_meta_box_in_cpts') );
		add_filter( 'themify_post_types', array( &$this, 'extend_post_types' ) );

		$this->create_post_type();
	}

	public function create_post_type(){
		$cpt = array(
			'plural' => __('Announcements', 'announcement-bar'),
			'singular' => __('Announcement', 'announcement-bar'),
			'supports' => array('title', 'editor', 'author', 'custom-fields')
		);
		register_post_type( 'announcement', array(
			'labels' => array(
				'name' => $cpt['plural'],
				'singular_name' => $cpt['singular'],
				'add_new' => __( 'Add New', 'announcement-bar' ),
				'add_new_item' => sprintf(__( 'Add New %s', 'announcement-bar' ), $cpt['singular']),
				'edit_item' => sprintf(__( 'Edit %s', 'announcement-bar' ), $cpt['singular']),
				'new_item' => sprintf(__( 'New %s', 'announcement-bar' ), $cpt['singular']),
				'view_item' => sprintf(__( 'View %s', 'announcement-bar' ), $cpt['singular']),
				'search_items' => sprintf(__( 'Search %s', 'announcement-bar' ), $cpt['plural']),
				'not_found' => sprintf(__( 'No %s found', 'announcement-bar' ), $cpt['plural']),
				'not_found_in_trash' => sprintf(__( 'No %s found in Trash', 'announcement-bar' ), $cpt['plural']),
				'menu_name' => $cpt['plural']
			),
			'supports' => isset($cpt['supports'])? $cpt['supports'] : array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'),
			'hierarchical' => false,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'rewrite' => array( 'slug' => isset($cpt['rewrite'])? $cpt['rewrite']: strtolower($cpt['singular']) ),
			'query_var' => true,
			'can_export' => true,
			'capability_type' => 'post',
			'menu_icon' => 'dashicons-megaphone'
		));
		register_taxonomy( 'announcement-category', array('announcement'), array(
			'labels' => array(
				'name' => sprintf(__( '%s Categories', 'announcement-bar' ), $cpt['singular']),
				'singular_name' => sprintf(__( '%s Category', 'announcement-bar' ), $cpt['singular']),
				'search_items' => sprintf(__( 'Search %s Categories', 'announcement-bar' ), $cpt['singular']),
				'popular_items' => sprintf(__( 'Popular %s Categories', 'announcement-bar' ), $cpt['singular']),
				'all_items' => sprintf(__( 'All Categories', 'announcement-bar' ), $cpt['singular']),
				'parent_item' => sprintf(__( 'Parent %s Category', 'announcement-bar' ), $cpt['singular']),
				'parent_item_colon' => sprintf(__( 'Parent %s Category:', 'announcement-bar' ), $cpt['singular']),
				'edit_item' => sprintf(__( 'Edit %s Category', 'announcement-bar' ), $cpt['singular']),
				'update_item' => sprintf(__( 'Update %s Category', 'announcement-bar' ), $cpt['singular']),
				'add_new_item' => sprintf(__( 'Add New %s Category', 'announcement-bar' ), $cpt['singular']),
				'new_item_name' => sprintf(__( 'New %s Category', 'announcement-bar' ), $cpt['singular']),
				'separate_items_with_commas' => sprintf(__( 'Separate %s Category with commas', 'announcement-bar' ), $cpt['singular']),
				'add_or_remove_items' => sprintf(__( 'Add or remove %s Category', 'announcement-bar' ), $cpt['singular']),
				'choose_from_most_used' => sprintf(__( 'Choose from the most used %s Category', 'announcement-bar' ), $cpt['singular']),
				'menu_name' => sprintf(__( '%s Category', 'announcement-bar' ), $cpt['singular']),
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => true,
			'rewrite' => true,
			'query_var' => true
		));
		add_filter('manage_edit-announcement-category_columns', array(&$this, 'taxonomy_header'), 10, 2);
		add_filter('manage_announcement-category_custom_column', array(&$this, 'taxonomy_column_id'), 10, 3);
	}

	/**
	 * Create metabox panel
	 */
	public function create_meta_box_in_announ($meta_boxes){
		$options = Announcement_Model::get_custom_field_announcements();
		return array_merge($meta_boxes, array(
			array(
				'name'	=> __('Announcement Options', 'announcement-bar'),
				'id' 		=> 'announcement-options',
				'options' => $options,
				'pages'	=> 'announcement'
			)
		));
	}

	/**
	 * Create metabox panel
	 */
	public function create_meta_box_in_cpts( $meta_boxes ){
		$options = Announcement_Model::get_custom_field_cpts();
		$custom_pages = Announcement_Model::get_available_post_types();
		$all_meta_boxes = array();
		foreach ($custom_pages as $type) {
			$all_meta_boxes[] = apply_filters( 'announcement_bar_write_panels_meta_boxes', array(
				'name'		=> __( 'Announcement Options', 'announcement-bar' ),
				'id' 		=> 'announcement-query-options',
				'options'	=> $options,
				'pages'    	=> $type
			) );
		}
		return array_merge( $meta_boxes, $all_meta_boxes);
	}

	/**
	 * Display an additional column in categories list
	 */
	public function taxonomy_header($cat_columns){
	    $cat_columns['cat_id'] = 'ID';
	    return $cat_columns;
	}

	/**
	 * Display ID in additional column in categories list
	 */
	public function taxonomy_column_id($null, $column, $termid){
		return $termid;
	}

	/**
	 * Includes new post types registered in theme to array of post types managed by Themify
	 * @param array
	 * @return array
	 */
	function extend_post_types( $types ) {
		$custom = Announcement_Model::get_available_post_types();
		return array_merge( $types, array( $this->post_type ), $custom );
	}

	/**
	 * Flush permalink
	 */
	public static function flush_post_type(){
		@self::create_post_type();
		flush_rewrite_rules();
	}
}