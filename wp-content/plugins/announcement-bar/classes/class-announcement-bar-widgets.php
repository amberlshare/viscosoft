<?php
/**
 * Announcement Bar Widget
 */
class Announcement_Bar_Widget extends WP_Widget {

	var $defaults = array(
		'widget_title' => '',
		'category' => '0',
		'post_type' => 'announcement',
		'abar_post_category' => '0',
		'limit' => '5',
		'title' => 'yes',
		'order' => 'DESC',
		'orderby' => 'date',
		'start' => '',
		'end' => '',
		'class' => '',
		
		// Slider Related
		'visible' => '1',
		'scroll' => '1',
		'effect' => 'slide',
		'auto' => '0',
		'wrap' => 'yes',
		'speed' => 'normal',
		'slider_nav' => 'yes',
		'pager' => 'yes',
		'font_style' => 'default-font',
		'design_style' => 'default-color'
	);

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'announcement_bar_widget', // Base ID
			__('Announcement Bar', 'announcement-bar'), // Name
			array( 'description' => __( 'Display any announcement in your widget', 'announcement-bar' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$settings = array(
			'announcement_bar_id' => $this->id . '-' . rand(0,10000),
			'announcement_layout' => 'widget',
			'ab_hide_title' => $instance['title'] == 'no' ? 1 : '',
			'ab_font_style' => 'default',
			'ab_design_style' => 'default',
			'ab_text_alignment' => '',
			// Slider
			'ab_visible' => isset( $instance['visible'] ) ? $instance['visible'] : '1',
			'ab_scroll' => isset( $instance['scroll'] ) ? $instance['scroll'] : '1',
			'ab_effect' => isset( $instance['effect'] ) ? $instance['effect'] : 'slide',
			'ab_auto_play' => isset( $instance['auto'] ) ? $instance['auto'] : '0',
			'ab_wrap' => isset( $instance['wrap'] ) ? $instance['wrap'] : 'yes',
			'ab_speed' => isset( $instance['speed'] ) ? $instance['speed'] : 'normal',
			'ab_slider_nav' => isset( $instance['slider_nav'] ) ? $instance['slider_nav'] : 'yes',
			'ab_pager' => isset( $instance['pager'] ) ? $instance['pager'] : 'yes',
			'ab_show_timer' => '',
			'ab_close_button' => 'none',
			'ab_font_style' => isset( $instance['font_style'] ) ? $instance['font_style'] : 'default-font',
			'ab_design_style' => isset( $instance['design_style'] ) ? $instance['design_style'] : 'default-color'
		);

		$settings['query_announcement'] = apply_filters( 'announcement_bar_shortcode_query', array(
			'post_type' => 'announcement',
			'post_status' => 'publish',
			'posts_per_page' => $instance['limit'],
			'order' => $instance['order'],
			'orderby' => $instance['orderby']
		) );

		if ( isset( $instance['category'] ) && ! empty( $instance['category'] ) ) {
			$terms = '0' == $instance['category'] || empty( $instance['category'] ) ? announcement_bar_get_all_terms_ids('announcement-category') : explode(',', str_replace(' ', '', $instance['category'] ));
			$settings['query_announcement']['tax_query'] = array(
				array(
					'taxonomy' => 'announcement-category',
					'field' => 'slug',
					'terms' => $terms
				)
			);
		}
		// QUERY DATE TIME RANGE
		if ( $instance['start'] != '' && $instance['end'] != '' ) {
			$settings['query_announcement']['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => 'ab_start_at',
					'value' => $instance['start'],
					'compare' => '>=',
					'type' => 'datetime'
				),
				array(
					'key' => 'ab_end_at',
					'value' => $instance['end'],
					'type' => 'datetime',
					'compare' => '<='
				)
			);
		} else if ( $instance['start'] != '' && $instance['end'] == '' ) {
			$settings['query_announcement']['meta_query'] = array(
				array(
					'key' => 'ab_start_at',
					'value' => $instance['start'],
					'compare' => '>=',
					'type' => 'datetime'
				)
			);
		} else if ( $instance['start'] == '' && $instance['end'] != '' ) {
			$settings['query_announcement']['meta_query'] = array(
				array(
					'key' => 'ab_end_at',
					'value' => $instance['end'],
					'type' => 'datetime',
					'compare' => '<='
				)
			);
		}

		// modify the query to display posts
		if( isset( $instance['post_type'] ) && $instance['post_type'] == 'post' ) {
			unset( $settings['query_announcement']['meta_query'] );
			if( ! isset( $instance['abar_post_category'] ) || $instance['abar_post_category'] === '0' ) {
				unset( $settings['query_announcement']['tax_query'] );
			} else {
				$settings['query_announcement']['tax_query'] = array(
					array(
						'taxonomy' => 'category',
						'field' => 'slug',
						'terms' => $instance['abar_post_category']
					)
				);
			}
			$settings['query_announcement']['post_type'] = 'post';
		}

		// Get Announcement Posts
		$settings['posts'] = new WP_Query( $settings['query_announcement'] );
		if( $settings['posts']->post_count < 1 ) {
			return false;
		}

		// Google Web Fonts embedding
		$family = '?family=Open+Sans:300,700|Oswald';
		if ( $settings['ab_font_style'] == 'old-style' ) {
			$family = '?family=EB+Garamond';
		} else if ( $settings['ab_font_style'] == 'slab-serif' ) {
			$family = '?family=Roboto+Slab';
		} else if ( $settings['ab_font_style'] == 'script' ) {
			$family = '?family=Kaushan+Script';
		}
		wp_enqueue_style( 'announcement-bar-widget-google-fonts-' . $settings['announcement_bar_id'], themify_https_esc( 'http://fonts.googleapis.com/css'). $family );

		global $Announcement_Bar;
		$Announcement_Bar->queue_assets();
		
		$widget_title = apply_filters( 'widget_title', $instance['widget_title'] );

		echo $args['before_widget'];
		if ( ! empty( $widget_title ) )
			echo $args['before_title'] . $widget_title . $args['after_title'];

		announcement_bar_render( 'announcement-bar-tpl.php', $settings );

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		
		// Category
		$categories = get_terms( 'announcement-category' );
		$terms_list = array();
		$terms_list['0'] = array(
			'title' => __('All Categories', 'announcement-bar'),										
			'slug'	=> '0'
		);
		foreach ($categories as $term) {
			$terms_list[$term->term_id] = array(
				'title' => $term->name,
				'slug'	=> $term->slug
			);
		}

		$post_categories = get_terms( 'category' );
		$category_terms_list = array();
		$category_terms_list['0'] = array(
			'title' => __('All Categories', 'announcement-bar'),										
			'slug'	=> '0'
		);
		foreach ($post_categories as $term) {
			$category_terms_list[$term->term_id] = array(
				'title' => $term->name,
				'slug'	=> $term->slug
			);
		}

		// Yes, no
		$confirmation = array(
			array( 'name' => __('Yes', 'announcement-bar'), 'val' => 'yes' ),
			array( 'name' => __('No', 'announcement-bar'), 'val' => 'no' )
		);
		$orders = array(
			array( 'name' => __('DESC', 'announcement-bar'), 'val' => 'desc' ),
			array( 'name' => __('ASC', 'announcement-bar'), 'val' => 'asc' )
		);
		$order_by = array(
			'date' => __('Date', 'announcement-bar'),
			'id' => __('Id', 'announcement-bar'),
			'author' => __('Author', 'announcement-bar'),
			'title' => __('Title', 'announcement-bar'),
			'name' => __('Name', 'announcement-bar'),
			'modified' => __('Modified', 'announcement-bar'),
			'rand' => __('Rand', 'announcement-bar'),
			'comment_count' => __('Comment Count', 'announcement-bar')
		);
		$effects = array(
			'slide' => __('Slide', 'announcement-bar'),
			'fade' => __('Fade', 'announcement-bar'), 
			'continuously' => __('Continuously', 'announcement-bar')
		);
		$number_range = array(0,1,2,3,4,5,6,7,8,9,10);
		$autos = array();
		foreach( $number_range as $num ) {
			$new_num = $num == 0 ? array('name' => __('Off', 'announcement-bar'), 'val' => '0' ) : 
						array('name' => $num . ' sec', 'val'=> $num);
			array_push($autos, $new_num);
		}
		$speeds = array(
			'normal' => __('Normal', 'announcement-bar'),
			'slow' => __('Slow', 'announcement-bar'), 
			'fast' => __('Fast', 'announcement-bar')
		);

		$font_style = array(
			array('value' => 'default-font', 'title' => __('Default', 'announcement-bar')),
			array('value' => 'serif', 'title' => __('Serif', 'announcement-bar')),
			array('value' => 'old-style', 'title' => __('Old Style', 'announcement-bar')),
			array('value' => 'slab-serif', 'title' => __('Slab Serif', 'announcement-bar')),
			array('value' => 'script', 'title' => __('Script', 'announcement-bar'))
		);

		$design_style = array(
			array('value' => 'default-color', 'title' => __('Default', 'announcement-bar')),
			array('value' => 'white', 'title' => __('White', 'announcement-bar')),
			array('value' => 'yellow', 'title' => __('Yellow', 'announcement-bar')),
			array('value' => 'blue', 'title' => __('Blue', 'announcement-bar')),
			array('value' => 'green', 'title' => __('Green', 'announcement-bar')),
			array('value' => 'orange', 'title' => __('Orange', 'announcement-bar')),
			array('value' => 'pink', 'title' => __('Pink', 'announcement-bar')),
			array('value' => 'purple', 'title' => __('Purple', 'announcement-bar')),
			array('value' => 'black',  'title' => __('Black', 'announcement-bar')),
			array('value' => 'gray', 'title' => __('Gray', 'announcement-bar')),
			array('value' => 'paper', 'title' => __('Paper', 'announcement-bar')),
			array('value' => 'notes', 'title' => __('Notes', 'announcement-bar')),
			array('value' => 'clip', 'title' => __('Clip', 'announcement-bar')),
			array('value' => 'bookmark', 'title' => __('Bookmark', 'announcement-bar'))
		);

		$post_types = array(
			'announcement' => __( 'Announcements', 'announcement-bar' ),
			'post' => __( 'Posts', 'announcement-bar' ),
		);

		?>
		<div class="<?php echo $this->id; ?>-form">

		<p>
			<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php _e( 'Title:', 'announcement-bar' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" type="text" value="<?php echo esc_attr( $instance['widget_title'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Display:', 'announcement-bar' ); ?></label>
			<select class="announcement-post-type" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
				<?php foreach( $post_types as $id => $value ): ?>
				<option value="<?php echo $id; ?>"<?php selected( $instance['post_type'], $id ); ?>><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="abar-category">
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
				<?php foreach( $terms_list as $term_id => $term ): ?>
				<option value="<?php echo $term['slug']; ?>"<?php selected( $instance['category'], $term['slug'] ); ?>><?php echo $term['title']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="abar-post-category">
			<label for="<?php echo $this->get_field_id( 'abar_post_category' ); ?>"><?php _e( 'Post Category:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id('abar_post_category'); ?>" name="<?php echo $this->get_field_name('abar_post_category'); ?>">
				<?php foreach( $category_terms_list as $term_id => $term ): ?>
				<option value="<?php echo $term['slug']; ?>"<?php selected( $instance['category'], $term['slug'] ); ?>><?php echo $term['title']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'announcement-bar' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $instance['limit'] ); ?>" size="2" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Show Post Title:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>">
				<?php foreach( $confirmation as $confirm ): ?>
				<option value="<?php echo $confirm['val']; ?>"<?php selected( $instance['title'], $confirm['val'] ); ?>><?php echo $confirm['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
				<?php foreach( $orders as $order ): ?>
				<option value="<?php echo $order['val']; ?>"<?php selected( $instance['order'], $order['val'] ); ?>><?php echo $order['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order By:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
				<?php foreach( $order_by as $val => $name ): ?>
				<option value="<?php echo $val; ?>"<?php selected( $instance['orderby'], $val ); ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'start' ); ?>"><?php _e( 'Start at:', 'announcement-bar' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'start' ); ?>" name="<?php echo $this->get_field_name( 'start' ); ?>" type="text" value="<?php echo esc_attr( $instance['start'] ); ?>" class="widefat" />
			<span class="description"><?php _e('Format: YYYY-mm-dd H:i', 'announcement-bar') ?></span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'end' ); ?>"><?php _e( 'End at:', 'announcement-bar' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'end' ); ?>" name="<?php echo $this->get_field_name( 'end' ); ?>" type="text" value="<?php echo esc_attr( $instance['end'] ); ?>" class="widefat" />
			<span class="description"><?php _e('Format: YYYY-mm-dd H:i', 'announcement-bar') ?></span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php _e( 'CSS Class:', 'announcement-bar' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" type="text" value="<?php echo esc_attr( $instance['class'] ); ?>" class="widefat" />
			<span class="description"><?php _e('Custom CSS Class', 'announcement-bar') ?></span>
		</p>

		<h4><?php _e('Slider Options', 'announcement-bar') ?></h4>
		<hr />

		<p>
			<label for="<?php echo $this->get_field_id( 'visible' ); ?>"><?php _e( 'Visible:', 'announcement-bar' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'visible' ); ?>" name="<?php echo $this->get_field_name( 'visible' ); ?>" type="text" value="<?php echo esc_attr( $instance['visible'] ); ?>" size="2" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'scroll' ); ?>"><?php _e( 'Scroll:', 'announcement-bar' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'scroll' ); ?>" name="<?php echo $this->get_field_name( 'scroll' ); ?>" type="text" value="<?php echo esc_attr( $instance['scroll'] ); ?>" size="2" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'effect' ); ?>"><?php _e( 'Effect:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'effect' ); ?>" name="<?php echo $this->get_field_name( 'effect' ); ?>">
				<?php foreach( $effects as $val => $name ): ?>
				<option value="<?php echo $val; ?>"<?php selected( $instance['effect'], $val ); ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'auto' ); ?>"><?php _e( 'Auto:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'auto' ); ?>" name="<?php echo $this->get_field_name( 'auto' ); ?>">
				<?php foreach( $autos as $name ): ?>
				<option value="<?php echo $name['val']; ?>"<?php selected( $instance['auto'], $name['val'] ); ?>><?php echo ucfirst( $name['name'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'wrap' ); ?>"><?php _e( 'Wrap:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'wrap' ); ?>" name="<?php echo $this->get_field_name( 'wrap' ); ?>">
				<?php 
				foreach( $confirmation as $confirm ): ?>
				<option value="<?php echo $confirm['val']; ?>"<?php selected( $instance['wrap'], $confirm['val'] ); ?>><?php echo $confirm['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'speed' ); ?>"><?php _e( 'Speed:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'speed' ); ?>" name="<?php echo $this->get_field_name( 'speed' ); ?>">
				<?php foreach( $speeds as $k => $name ): ?>
				<option value="<?php echo $k; ?>"<?php selected( $instance['speed'], $k ); ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'slider_nav' ); ?>"><?php _e( 'Slider Nav:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'slider_nav' ); ?>" name="<?php echo $this->get_field_name( 'slider_nav' ); ?>">
				<?php foreach( $confirmation as $confirm ): ?>
				<option value="<?php echo $confirm['val']; ?>"<?php selected( $instance['slider_nav'], $confirm['val'] ); ?>><?php echo $confirm['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'pager' ); ?>"><?php _e( 'Pager:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'pager' ); ?>" name="<?php echo $this->get_field_name( 'pager' ); ?>">
				<?php foreach( $confirmation as $confirm ): ?>
				<option value="<?php echo $confirm['val']; ?>"<?php selected( $instance['pager'], $confirm['val'] ); ?>><?php echo $confirm['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<h4><?php _e( 'Appearance', 'announcement-bar' ) ?></h4>
		<hr />

		<p>
			<label for="<?php echo $this->get_field_id( 'font_style' ); ?>"><?php _e( 'Font Style:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'font_style' ); ?>" name="<?php echo $this->get_field_name( 'font_style' ); ?>">
				<?php foreach( $font_style as $style ): ?>
				<option value="<?php echo $style['value']; ?>"<?php selected( $instance['font_style'], $style['value'] ); ?>><?php echo $style['title']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'design_style' ); ?>"><?php _e( 'Design Style:', 'announcement-bar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'design_style' ); ?>" name="<?php echo $this->get_field_name( 'design_style' ); ?>">
				<?php foreach( $design_style as $style ): ?>
				<option value="<?php echo $style['value']; ?>"<?php selected( $instance['design_style'], $style['value'] ); ?>><?php echo $style['title']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		</div><!-- .<?php echo $this->id; ?>-form -->

		<script>
			jQuery( '.announcement-post-type', '.<?php echo $this->id; ?>-form' ).change(function(){
				var $ = jQuery,
					container = $( this ).closest( '.widget-content' );
				if( $( this ).val() == 'post' ) {
					$( '.abar-category', container ).hide();
					$( '.abar-post-category', container ).show();
				} else {
					$( '.abar-category', container ).show();
					$( '.abar-post-category', container ).hide();
				}
			}).change();
		</script>
		
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		foreach( $this->defaults as $field => $value ) {
			$instance[ $field ] = ( '' != $new_instance[ $field ] ) ? strip_tags( $new_instance[ $field ] ) : '';
		}
		return $instance;
	}

} // class Announcement_Bar_Widget
?>