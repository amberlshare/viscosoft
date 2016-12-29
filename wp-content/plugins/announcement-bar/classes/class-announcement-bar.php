<?php
/**
 * Announcement Bar
 *
 * @package   Announcement_Bar
 * @author    Themify
 * @link      http://themify.me
 * @copyright 2013 Themify
 */

/**
 * Plugin class
 * @package Announcement_Bar
 */
class Announcement_Bar {
	var $is_themify_themes = false;
	var $options = array();

	/**
	 * Constructor
	 */
	public function __construct(){

		$this->options = Announcement_Model::get_plugin_settings();

		// Actions
		add_action( 'init', array( &$this, 'init') );
		add_action( 'wp_enqueue_scripts', array( &$this, 'load_js_css' ) );
		add_action( 'wp_footer', array( &$this, 'load_custom_gfonts' ), 20 );

		// Shortcodes
		$shortcodes_with_content = array( 'button', 'col' );
		$shortcodes_single = array( 'bar', 'map' );
		foreach( $shortcodes_with_content as $shortcode ){
			add_shortcode( 'announcement_' . $shortcode, array( &$this, 'announcement_shortcode' ) );
		}
		foreach( $shortcodes_single as $shortcode ){
			add_shortcode( 'announcement_' . $shortcode, array( &$this, 'announcement_shortcode_' . $shortcode ) );
		}

		// Filter
		add_filter( 'announcement_bar_teaser', array( &$this, 'announcement_bar_teaser' ) );
		add_filter( 'announcement_bar_the_content', array( &$this, 'announcement_bar_the_content' ) );
		add_filter( 'ab_content_teaser_length', array( &$this, 'announcement_bar_teaser_length' ) );

		add_action( 'after_setup_theme', array( &$this, 'announcement_to_builder' ), 20 );

		add_action( 'save_post', array( $this, 'clear_cache' ), 10, 2 );
		add_action( 'after_switch_theme', 'announcement_bar_flush_cache' );

		add_action( 'init', array( $this, 'update_117_required_date_fields' ) );

		/**
		 * improve saving A.Bar's custom fields
		 * runs just before Themify_Metabox::save_postdata
		 */
		add_action( 'save_post', array( $this, 'save_post' ), 100 );

		do_action( 'announcement_bar_init' );
	}

	/**
	 * When "Announcement Bar option is set to Default or Disabled, prevent saving additional custom fields.
	 * Reduces clutter in wp_postmeta table when the saved data is actually not needed.
	 *
	 * @since 2.8.9
	 */
	function save_post() {
		global $post;
		if( isset( $_POST['announcement_bar'] ) && ( $_POST['announcement_bar'] == 'default' || $_POST['announcement_bar'] == 'disable' ) ) {
			$fields = Announcement_Model::get_custom_field_cpts();
			$keys = wp_list_pluck( themify_metabox_make_flat_fields_array( $fields ), 'name' );
			if( $_POST['announcement_bar'] != 'default' ) {
				unset( $keys[0] ); // the "announcement_bar" key, this should be saved
			}

			foreach( $keys as $key ) {
				if( isset( $_POST[$key] ) ) {
					$_POST[$key] = '';
				}
			}
		}
	}

	/**
	 * Init
	 */
	function init() {
		if ( file_exists( get_template_directory() . '/themify/themify-utils.php' ) ) {
			$this->is_themify_themes = true;
			add_action( 'themify_body_start', array( &$this, 'display_announcement' ) );
		} else {
			add_action( 'wp_footer', array( &$this, 'display_announcement' ) );
		}

		// Load plugin locale
		$this->load_plugin_textdomain();
	}

	/**
	 * Hook Builder content to announcement if builder active
	 */
	function announcement_to_builder() {
		if ( class_exists( 'Themify_Builder_Model' ) && Themify_Builder_Model::builder_check() ) {
			add_filter( 'announcement_bar_get_contents', array( &$this, 'announcement_bar_builder' ) );
		}
	}

	/**
	 * Activate
	 */
	public static function activate(){
		announcement_bar_flush_cache();
	}

	/**
	 * Deactivate
	 */
	public static function deactivate(){

	}

	/**
	 * Load plugin textdomain
	 */
	function load_plugin_textdomain() {
		load_plugin_textdomain( 'announcement-bar', false, plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Load frontend js and css
	 */
	function load_js_css() {
		global $version;

		// Google Web Fonts embedding
		$family = '?family=Open+Sans:300,700|Oswald|EB+Garamond|Roboto+Slab|Kaushan+Script';
		wp_enqueue_style( 'announcement-bar-default-gfonts', $this->https_esc( 'http://fonts.googleapis.com/css'). $family );

		wp_enqueue_style( 'announcement-bar-style', ANNOUNCEMENT_BAR_PLUGIN_URL . 'css/style.css', array(), ANNOUNCEMENT_BAR_CURRENT_VERSION );

		// Scripts
		if ( ! wp_script_is( 'announ-carousel-js', 'registered' ) ) {
			wp_register_script( 'announ-carousel-js', ANNOUNCEMENT_BAR_PLUGIN_URL . 'js/jquery.carousel.js', array( 'jquery' ) );
		}
		if ( ! wp_script_is( 'announcement-bar-js', 'registered' ) ) {
			wp_register_script( 'announcement-bar-js', ANNOUNCEMENT_BAR_PLUGIN_URL . 'js/scripts.js', array( 'jquery' ) );
		}
		wp_localize_script( 'announcement-bar-js', 'announcementBar', apply_filters( 'announcement_bar_script_vars', array(
			'margin_top_to_bar_height' => 'body',
		) ) );

		//Register map scripts
		if ( ! wp_script_is( 'themify-builder-map-script', 'registered' ) ) {
			$key = isset( $this->options['google_map_key'] ) ? '&key=' . $this->options['google_map_key'] : '';
			wp_register_script( 'themify-builder-map-script', $this->https_esc('http://maps.google.com/maps/api/js').'?sensor=false' . $key, array(), $version, true );
		}
		if ( ! wp_script_is( 'themify-map-shortcode', 'registered' ) ) {
			wp_register_script( 'themify-map-shortcode', ANNOUNCEMENT_BAR_PLUGIN_URL . 'themify/js/themify.mapa.js', array(), $version, true );
		}

		do_action( 'announcement_bar_register_assets' );
	}

	/**
	 * Display announcement hook
	 * @param string $content 
	 */
	function display_announcement( $content ) {
		if ( ! is_main_query() && ! in_the_loop() && ! ( is_page() || is_singular() ) ) return;
		global $post;

		$post_id = ( $post == null ) ? 0 : $post->ID;
		// check settings
		$data_from = get_post_meta( $post_id, 'announcement_bar', true );
		$data_from = empty( $data_from ) || $data_from == 'default' ? 'plugin' : 'custom';

		// global settings to display abar
		if( $data_from == 'plugin' ) {
			
			if( $this->get_setting( 'ab_start_at' ) != '' ) {
				if( ! ( strtotime( $this->get_setting( 'ab_start_at' ) . ' ' . get_option( 'timezone_string' ) ) < time() ) ) {
					return;
				}
			}
			if( $this->get_setting( 'ab_end_at' ) != '' ) {
				if( ! ( strtotime( $this->get_setting( 'ab_end_at' ) . ' ' . get_option( 'timezone_string' ) ) > time() ) ) {
					return;
				}
			}
		}

		$cache = apply_filters( 'announcement_bar_cache_enabled', true, $post_id, $data_from );

		if( $cache ) {
			$transient = $data_from == 'plugin' ? 'abar_display' : "abar_display_{$post_id}";
			if( defined( 'ICL_LANGUAGE_CODE' ) ) { // WPML compatibility: cache separately for different languages
				$transient .= '_' . ICL_LANGUAGE_CODE;
			}
			if ( false === ( $output = get_transient( $transient ) ) ) {
				$output = $this->get_announcement( $post_id, $data_from );
				set_transient( $transient, $output, apply_filters( 'announcement_bar_cache_time', 12 * HOUR_IN_SECONDS ) );
			}
		} else {
			$output = $this->get_announcement( $post_id, $data_from );
		}

		if( false != $output ) {
			$this->queue_assets();
		}
		echo $output;
	}

	function queue_assets() {
		// Enqueue JS
		if ( ! wp_script_is( 'announ-carousel-js' ) ) {
			wp_enqueue_script( 'announ-carousel-js' );
		}
		if ( ! wp_script_is( 'announcement-bar-js' ) ) {
			wp_enqueue_script( 'announcement-bar-js' );
		}

		do_action( 'announcement_bar_queue_assets' );
	}

	/**
	 * Get announcement content
	 * @param int $post_id
	 * @param string $data_from
	 */ 
	function get_announcement( $post_id, $data_from = 'plugin' ) {
		// Check if announcement enable
		$announcement = $this->get_setting('announcement_bar');
		$announcement = $announcement == '' ? 'enable' : $announcement;
		if ( 0 !== $post_id && $data_from == 'custom' ) {
			$announcement = get_post_meta( $post_id, 'announcement_bar', true );
			if( get_post_meta( $post_id, 'announcement_bar_display', true ) == 'post' ) {
				$announcement = 'post';
			}
		}

		/**
		 * $announcement variable can be:
		 *  - "enable"  : display announcement posts
		 *  - "post"    : display posts inside the announcement bar
		 *  - "disable" : do not display announcements
		 */
		$announcement = apply_filters( 'announcement_bar_display', $announcement, $post_id, $data_from );

		// Return if announcement disabled
		if ( $announcement === 'disable' || empty( $announcement ) ) return;

		$custom_fields = array(
			// Announcement Bar
			'announcement_category', 'abar_post_category', 'ab_hide_title', 'ab_order_by', 'ab_order', 'ab_query_number',
				// Effect
				'ab_visible', 'ab_scroll', 'ab_effect', 'ab_auto_play', 'ab_wrap', 'ab_speed', 'ab_slider_nav', 'ab_pager', 'ab_show_timer',
			// Date Range
			'ab_start_at', 'ab_end_at',
			'ab_position', 'ab_close_button', 'ab_remember_close_state', 'ab_text_alignment',
			// Appearance
			'ab_appearance',
				// Presets
				'ab_font_style', 'ab_design_style',
				// Custom
				'bar_background_color', 'bar_background_color_transparent', 'bar_background_image', 'bar_background_repeat', 'bar_background_position',
				'announcement_title_font', 'announcement_title_color', 'announcement_title_background_color', 'announcement_title_background_color_transparent', 'announcement_title_background_image', 'announcement_title_background_repeat', 'announcement_title_background_position',
				'announcement_content_font', 'announcement_content_color', 'announcement_content_link_color', 'announcement_content_link_decoration', 'announcement_content_link_hover_color', 'announcement_content_link_hover_decoration'
		);

		$settings = array();
		foreach( $custom_fields as $field ) {
			if ( $data_from == 'plugin' ) {
				$settings[ $field ] = $this->get_setting( $field );
				$fonts_custom = array( 'announcement_title_font', 'announcement_content_font' );
				if ( in_array( $field, $fonts_custom ) ) {
					$family = isset( $settings[ $field ]['family'] ) ? $settings[ $field ]['family'] : '';
					Announcement_Model::set_google_fonts( $family );
				}
			} else if ( 0 !== $post_id && $data_from == 'custom' ) {

				$values = get_post_meta( $post_id, $field, true );
				if ( $field == 'ab_position' ) {
					$values = array(
						'align' => get_post_meta( $post_id, 'ab_position_y', true ),
						'state' => get_post_meta( $post_id, $field, true )
					);
				} else if ( $field == 'bar_background_position' ) {
					$values = array(
						'x' => get_post_meta( $post_id, $field . '_x', true ),
						'y' => get_post_meta( $post_id, $field . '_y', true )
					);
				} else if ( $field == 'announcement_title_font' ) {
					$family = get_post_meta( $post_id, $field . '_family', true );
					$values = array(
						'size' => get_post_meta( $post_id, $field, true ),
						'unit' => get_post_meta( $post_id, $field . '_size_unit', true ),
						'family' => $family
					);
					Announcement_Model::set_google_fonts( $family );
				} else if ( $field == 'announcement_title_background_position' ) {
					$values = array(
						'x' => get_post_meta( $post_id, $field . '_x', true ),
						'y' => get_post_meta( $post_id, $field . '_y', true )
					);
				} else if ( $field == 'announcement_content_font' ) {
					$family = get_post_meta( $post_id, $field . '_family', true );
					$values = array(
						'size' => get_post_meta( $post_id, $field, true ),
						'unit' => get_post_meta( $post_id, $field . '_size_unit', true ),
						'family' => $family
					);
					Announcement_Model::set_google_fonts( $family );
				}

				$settings[ $field ] = $values;
			}
		}

		// global settings to display abar
		if( $settings['ab_start_at'] != '' ) {
			if( ! ( strtotime( $settings['ab_start_at'] . ' ' . get_option( 'timezone_string' ) ) < time() ) ) {
				return false;
			}
		}
		if( $settings['ab_end_at'] != '' ) {
			if( ! ( strtotime( $settings['ab_end_at'] . ' ' . get_option( 'timezone_string' ) ) > time() ) ) {
				return false;
			}
		}

		// Collect Announ Settings
		$settings['query_announcement'] = apply_filters( 'announcement_bar_cpt_query', array(
			'post_type' => 'announcement',
			'post_status' => 'publish',
			'posts_per_page' => $settings['ab_query_number'],
			'order' => $settings['ab_order'],
			'orderby' => $settings['ab_order_by']
		) );
		
		if ( ! empty( $settings['announcement_category'] ) ) {
			$terms = '0' == $settings['announcement_category'] || empty( $settings['announcement_category'] ) ? announcement_bar_get_all_terms_ids('announcement-category') : explode(',', str_replace(' ', '', $settings['announcement_category']));
			$settings['query_announcement']['tax_query'] = array(
				array(
					'taxonomy' => 'announcement-category',
					'field' => 'slug',
					'terms' => $terms
				)
			);
		}

		// QUERY DATE TIME RANGE
		$datenow = date_i18n('Y-m-d H:i:s');
		$settings['query_announcement']['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => 'ab_start_at',
				'value' => $datenow,
				'compare' => '<=',
				'type' => 'datetime'
			),
			array(
				'key' => 'ab_end_at',
				'value' => $datenow,
				'compare' => '>=',
				'type' => 'datetime'
			),
		);

		// modify the query to display posts
		if( $announcement == 'post' ) {
			unset( $settings['query_announcement']['meta_query'] );
			if( ! isset( $settings['abar_post_category'] ) || $settings['abar_post_category'] == '' || $settings['abar_post_category'] === '0' ) {
				unset( $settings['query_announcement']['tax_query'] );
			} else {
				$settings['query_announcement']['tax_query'] = array(
					array(
						'taxonomy' => 'category',
						'field' => 'slug',
						'terms' => explode( ',', str_replace( ' ', '', $settings['abar_post_category'] ) )
					)
				);
			}
			$settings['query_announcement']['post_type'] = 'post';
		}

		// Get Announcement Posts
		$settings['posts'] = new WP_Query( apply_filters( 'announcement_bar_query', $settings['query_announcement'], $settings, $post_id ) );
		if( $settings['posts']->post_count < 1 ) {
			return false;
		}

		// define id
		$settings['announcement_bar_id'] = 'announcement_bar_slider';
		if ( $data_from == 'custom' ) 
			$settings['announcement_bar_id'] = $settings['announcement_bar_id'] . '_' . $post_id;

		// Announcement Layout
		$settings['announcement_layout'] = 'bar';

		// Bar Style
		if ( $settings['ab_appearance'] == 'custom' || $settings['ab_appearance'] == 'custom-appearance' ) {
			// Reset Preset Style
			$settings['ab_font_style'] = '';
			$settings['ab_design_style'] = '';

			// Bar Background
			$styles = array(
				// Announcement Bar
				array(
					'selector' => '#' . $settings['announcement_bar_id'],
					'properties' => array(
						'background_color' => $settings['bar_background_color'] != '' ? '#'.$settings['bar_background_color'] : '',
						'background_image' => $settings['bar_background_image'] != '' ? 'url("'.$settings['bar_background_image'].'")' : '',
						'background_repeat' => $settings['bar_background_repeat'],
						'background_position' => implode(' ', $settings['bar_background_position'])
					)
				),
				// Announcement Title
				array(
					'selector' => '#' . $settings['announcement_bar_id'] . ' .announcement_title',
					'properties' => array(
						'font_family' => $settings['announcement_title_font']['family'],
						'font_size' => $settings['announcement_title_font']['size'] . $settings['announcement_title_font']['unit'],
						'color' => $settings['announcement_title_color'] != '' ? '#'.$settings['announcement_title_color'] : '',
						'background_color' => $settings['announcement_title_background_color'] != '' ? '#'.$settings['announcement_title_background_color'] : '',
						'background_image' => $settings['announcement_title_background_image'] != '' ? 'url("'.$settings['announcement_title_background_image'].'")' : '',
						'background_repeat' => $settings['announcement_title_background_repeat'],
						'background_position' => implode(' ', $settings['announcement_title_background_position'])
					)
				),
				array(
					'selector' => '#' . $settings['announcement_bar_id'] . ' .announcement_title:after',
					'properties' => array(
						'border_left_color' => $settings['announcement_title_background_color'] != '' ? '#'.$settings['announcement_title_background_color'] : ''
					)
				),
				// Announcement Content
				array(
					'selector' => '#' . $settings['announcement_bar_id'] . ' .announcement_content',
					'properties' => array(
						'font_family' => $settings['announcement_content_font']['family'],
						'font_size' => $settings['announcement_content_font']['size'] . $settings['announcement_content_font']['unit'],
						'color' => $settings['announcement_content_color'] != '' ? '#'.$settings['announcement_content_color'] : ''
					)
				),
				// Announcement Content Link
				array(
					'selector' => '#' . $settings['announcement_bar_id'] . ' .announcement_content a',
					'properties' => array(
						'color' => $settings['announcement_content_link_color'] != '' ? '#'.$settings['announcement_content_link_color'] : '',
						'text_decoration' => $settings['announcement_content_link_decoration']
					)
				),
				// Announcement Content Link Hover
				array(
					'selector' => '#' . $settings['announcement_bar_id'] . ' .announcement_content a:hover',
					'properties' => array(
						'color' => $settings['announcement_content_link_hover_color'] != '' ? '#'.$settings['announcement_content_link_hover_color'] : '',
						'text_decoration' => $settings['announcement_content_link_hover_decoration']
					)
				)
			);

			$settings['custom_style_appearance'] = '<style type="text/css">';
			foreach( $styles as $key => $prop ) {
				$settings['custom_style_appearance'] .= $prop['selector'] . '{';
				foreach( $prop['properties'] as $p => $val ) {
					$settings['custom_style_appearance'] .= str_replace('_', '-', $p) . ':' . $val . ';';
				}
				$settings['custom_style_appearance'] .= '}';
			}
			$settings['custom_style_appearance'] .= '</style>';
		} else {
			$settings['custom_style_appearance'] = '';
		}

		// Render Template
		return announcement_bar_render( 'announcement-bar-tpl.php', $settings, false );
	}

	/**
	 * Get options setting
	 */
	function get_setting( $field ) {
		$options = $this->options;
		if ( isset( $options[ $field ] ) && ! empty( $options[ $field ] ) ) {
			return $options[ $field ];
		} else {
			return '';
		}
	}

	/**
	 * Shortcode announcement_bar
	 * @param array $atts 
	 * @return string
	 */
	function announcement_shortcode_bar( $atts ) {
		extract( shortcode_atts( array(
			'post_type' => 'announcement',
			'category' => '0',
			'taxonomy' => 'announcement-category',
			'limit' => '5',
			'title' => 'yes',
			'order' => 'desc',
			'orderby' => 'date',
			'start' => '',
			'end' => '',
			'class' => '',
			'font_style' => 'default',
			'design_style' => 'default',

			// slider related
			'visible' => '1',
			'scroll' => '1',
			'effect' => 'slide',
			'auto' => '0',
			'wrap' => 'yes',
			'speed' => 'normal',
			'slider_nav' => 'yes',
			'pager' => 'yes',
			'show_timer' => ''
		), $atts, 'announcement_bar' ));

		$nums = rand(0, 10000);
		$settings = array(
			'announcement_bar_id' => 'announcement_bar_shortcode_' . $nums,
			'announcement_layout' => 'shortcode',
			'ab_hide_title' => $title == 'no' ? true : false,
			'ab_font_style' => $font_style,
			'ab_design_style' => $design_style,
			'ab_visible' => $visible,
			'ab_scroll' => $scroll,
			'ab_effect' => $effect,
			'ab_auto_play' => $auto,
			'ab_wrap' => $wrap,
			'ab_speed' => $speed,
			'ab_slider_nav' => $slider_nav,
			'ab_pager' => $pager,
			'ab_text_alignment' => '',
			'ab_position' => array( 'align' => 'left', 'state' => 'static'),
			'ab_show_timer' => $show_timer,
			'ab_close_button' => 'none',
			'custom_class' => $class
		);

		$settings['query_announcement'] = apply_filters( 'announcement_bar_shortcode_query', array(
			'post_type' => $post_type,
			'post_status' => 'publish',
			'posts_per_page' => $limit,
			'order' => $order,
			'orderby' => $orderby
		) );

		if ( ! empty( $category ) ) {
			$terms = '0' == $category || empty( $category ) ? announcement_bar_get_all_terms_ids('announcement-category') : explode(',', str_replace(' ', '', $category));
			$settings['query_announcement']['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => $terms
				)
			);
		}

		// QUERY DATE TIME RANGE
		if ( $start != '' && $end != '' ) {
			$settings['query_announcement']['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => 'ab_start_at',
					'value' => $start,
					'compare' => '>=',
					'type' => 'datetime'
				),
				array(
					'key' => 'ab_end_at',
					'value' => $end,
					'type' => 'datetime',
					'compare' => '<='
				)
			);
		} else if ( $start != '' && $end == '' ) {
			$settings['query_announcement']['meta_query'] = array(
				array(
					'key' => 'ab_start_at',
					'value' => $start,
					'compare' => '>=',
					'type' => 'datetime'
				)
			);
		} else if ( $start == '' && $end != '' ) {
			$settings['query_announcement']['meta_query'] = array(
				array(
					'key' => 'ab_end_at',
					'value' => $end,
					'type' => 'datetime',
					'compare' => '<='
				)
			);
		}

		if( $post_type == 'post' ) {
			unset( $settings['query_announcement']['meta_query'] );
		}

		// Get Announcement Posts
		$settings['posts'] = new WP_Query( $settings['query_announcement'] );
		if( $settings['posts']->post_count < 1 ) {
			return false;
		}

		$this->queue_assets();
		return announcement_bar_render( 'announcement-bar-tpl.php', $settings, false );
	}

	/**
	 * Shortcode announcement_map
	 * @param array $atts 
	 * @return string
	 */
	function announcement_shortcode_map( $atts ) {
		wp_enqueue_script( 'themify-builder-map-script' );
		wp_enqueue_script( 'themify-map-shortcode' );
		extract( shortcode_atts(
			array(
				'address' => '99 Blue Jays Way, Toronto, Ontario, Canada',
				'width' => '500px',
				'height' => '300px',
				'zoom' => 15,
				'type' => 'ROADMAP',
				'scroll_wheel' => 'yes',
			),
			$atts
		));
		$num = rand(0,10000);
		return '<script type="text/javascript">	
					jQuery(document).ready(function() {
				  		ThemifyMap.initialize("'.$address.'", '.$num.', '.$zoom.', "'.$type.'", "'.$scroll_wheel.'");
					});
				</script>
				<div class="shortcode map">
					<div id="themify_map_canvas_'.$num.'" style="display: block;width:'.$width.';height:'.$height.';" class="map-container">&nbsp;</div>
				</div>';
	}

	/**
	 * Shortcode announcement_button and announcement_col
	 * @param array $atts 
	 * @param string $content 
	 * @param string $code 
	 * @return string
	 */
	function announcement_shortcode( $atts, $content=null, $code="" ){
		switch ( $code ) {
			case 'announcement_col':
				extract( shortcode_atts( array( 'grid' => ""), $atts));
				return "<div class='shortcode col".$grid."'>".do_shortcode($content)."</div>";
			break;

			case 'announcement_button':
				extract( shortcode_atts( array(
					'color' => "",
					'size' 	=> "",
					'style'	=> "",
					'link' 	=> "#",
					'target'=> "",
					'text'	=> ""
				), $atts ) );
				if($color != ''){
					$color = "background-color: $color;";
				}
				if($text != ''){
					$text = "color: $text;";	
				}
				return '<a href="'.$link.'" class="shortcode button '.$style.' '.$size.'" style="'.$color.$text.'" target="'.$target.'">'.do_shortcode($content).'</a>';
			break;
		}
		return '';
	}

	/**
	 * Apply filter to announ teaser
	 * @param string $teaser 
	 * @return string
	 */
	function announcement_bar_teaser( $teaser ) {
		$teaser = do_shortcode( shortcode_unautop( $teaser ) );
		return $teaser;
	}

	/**
	 * Apply filter to announ more content
	 * @param string $content 
	 * @return string
	 */
	function announcement_bar_the_content( $content ) {
		global $wp_embed;
		$content = $wp_embed->run_shortcode( $content );
		$content = do_shortcode( shortcode_unautop( $content ) );
		$content = wpautop( $content );
		return $content;
	}

	/**
	 * Display builder in Announcement Bar
	 * @param array $output 
	 * @return array
	 */
	function announcement_bar_builder( $output ) {
		global $ThemifyBuilder, $post;

		$builder_data = $ThemifyBuilder->get_builder_data( $post->ID );

		if ( is_array( $builder_data ) && count( $builder_data ) > 0 ) {
			$output['content'] .= $ThemifyBuilder->retrieve_template( 'builder-output.php', array( 'builder_output' => $builder_data, 'builder_id' => $post->ID ), '', '', false );
		}
		return $output;
	}

	/**
	 * Load custom google fonts
	 */
	function load_custom_gfonts() {
		$custom_fonts = Announcement_Model::$ab_custom_google_fonts;
		if ( '' == $custom_fonts ) return;
		$fonts = substr( $custom_fonts, 0, -1 );
		echo sprintf( '<link id="ab-custom-style-google-fonts" href="%s" rel="stylesheet" type="text/css">', $this->https_esc( 'http://fonts.googleapis.com/css' ). '?family=' . $fonts );
	}

	function announcement_bar_teaser_length( $words_length ) {
		$custom_length = Announcement_Model::get_plugin_setting_by_name( 'ab_content_teaser_length' );
		return $custom_length;
	}

	function clear_cache( $post_id, $post ) {
		if ( !is_object( $post ) )
			$post = get_post();
		announcement_bar_flush_cache();
	}

	/**
	 * Force all announcement posts to have an start and end date
	 * This runs only once after the 1.1.7 upgrade and updates all announcements
	 *
	 * @since 1.1.7
	 */
	function update_117_required_date_fields() {
		if( get_option( 'announcement_bar_update_117_required_date_fields' ) == 'yes' )
			return;

		$past_date = date( 'Y-m-d H:i:s', strtotime( '-2 years' ) );
		$future_date = date( 'Y-m-d H:i:s', strtotime( '+2 years' ) );
		$posts = get_posts( array(
			'post_type' => 'announcement',
			'meta_query' => array(
				array(
					'key' => 'ab_start_at',
					'value' => ''
				)
			)
		) );
		if( is_array( $posts ) ) {
			foreach( $posts as $post ) {
				update_post_meta( $post->ID, 'ab_start_at', $past_date );
			}
		}
		$posts = get_posts( array(
			'post_type' => 'announcement',
			'meta_query' => array(
				array(
					'key' => 'ab_end_at',
					'value' => ''
				)
			)
		) );
		if( is_array( $posts ) ) {
			foreach( $posts as $post ) {
				update_post_meta( $post->ID, 'ab_end_at', $future_date );
			}
		}
		update_option( 'announcement_bar_update_117_required_date_fields', 'yes' );
	}

	function https_esc( $url ) {
		if ( is_ssl() ) {
			$url = preg_replace( '/^(http:)/i', 'https:', $url, 1 );
		}

		return $url;
	}
}