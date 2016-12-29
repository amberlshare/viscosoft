<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Announcement Bar', 'announcement-bar') ?></h2>           
	<form id="announcement-bar-form" method="post" action="options.php">
	<?php
		// This prints out all hidden setting fields
		settings_fields( 'announcement_bar_group' );

		// Yes, no
		$confirmation = array(
			array( 'name' => __('Yes', 'announcement-bar'), 'value' => 'yes' ),
			array( 'name' => __('No', 'announcement-bar'), 'value' => 'no' )
		);
		$speeds = array(
			'normal' => __('Normal', 'announcement-bar'),
			'slow' => __('Slow', 'announcement-bar'), 
			'fast' => __('Fast', 'announcement-bar')
		);
		$number = array(0,1,2,3,4,5,6,7,8,9,10);
	?>


	<?php
	//********** ANNOUNCEMENT BAR *************//
	?>
	<table class="form-table">
		<tr valign="top" class="plugin-main-toggle">
			<th scope="row">
				<span><?php _e('Announcement Bar', 'announcement-bar') ?></span>
			</th>
			<td>
				<?php $checked = isset($options['announcement_bar']) ? $options['announcement_bar'] : 'enable'; ?>
				<input type="radio" id="announcement_bar_enable" name="<?php echo $option_name; ?>[announcement_bar]" value="enable" <?php checked( $checked, 'enable', true ); ?>> <label for="announcement_bar_enable"><?php _e('Announcements', 'announcement-bar') ?></label> 
				<input type="radio" id="announcement_bar_enable" name="<?php echo $option_name; ?>[announcement_bar]" value="post" <?php checked( $checked, 'post', true ); ?>> <label for="announcement_bar_enable"><?php _e('Posts', 'announcement-bar') ?></label> 
				<input type="radio" id="announcement_bar_disable" name="<?php echo $option_name; ?>[announcement_bar]" value="disable" <?php checked( $checked, 'disable', true ); ?>> <label for="announcement_bar_disable"><?php _e('Disable', 'announcement-bar') ?></label>
			</td>
		</tr>

		<tr valign="top" id="post-category">
			<th scope="row"></th>
			<td>
				<?php
				$categories = get_categories( array(
					'type' => 'post',
					'taxonomy' => 'category'
				) );
				$selected = isset( $options['abar_post_category'] ) ? $options['abar_post_category'] : '';
				?>
				<select name="<?php echo $option_name; ?>[abar_post_category]">
					<option value="0"><?php _e('All Categories', 'announcement-bar') ?></option>
					<?php foreach( $categories as $cat ): ?>
					<option value="<?php echo $cat->slug; ?>"<?php selected( $selected, $cat->slug );?>><?php echo $cat->name; ?></option>
					<?php endforeach; ?>
				</select>
				<br />
				<span class="description"><?php echo sprintf(__('Add more <a href="%s" target="_blank">Posts</a>','announcement-bar'), admin_url('post-new.php') ); ?></span>
			</td>
		</tr>

		<tr valign="top" id="announcement-category">
			<th scope="row"></th>
			<td>
				<?php
				$categories = get_categories( array(
					'type' => 'announcement',
					'taxonomy' => 'announcement-category'
				) );
				$selected = isset( $options['announcement_category'] ) ? $options['announcement_category'] : '';
				?>
				<select name="<?php echo $option_name; ?>[announcement_category]">
					<option value="0"><?php _e('All Categories', 'announcement-bar') ?></option>
					<?php foreach( $categories as $cat ): ?>
					<option value="<?php echo $cat->slug; ?>"<?php selected( $selected, $cat->slug );?>><?php echo $cat->name; ?></option>
					<?php endforeach; ?>
				</select>
				<br />
				<span class="description"><?php echo sprintf(__('Add more <a href="%s" target="_blank">Announcement Posts</a>','announcement-bar'), admin_url('post-new.php?post_type=announcement') ); ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$checked = isset( $options['ab_hide_title'] ) ? $options['ab_hide_title'] : '';
				?>
				<input type="checkbox" id="hide_post_title" name="<?php echo $option_name; ?>[ab_hide_title]" value="1"<?php checked( $checked, 1 ); ?>> 
				<label for="hide_post_title"><?php _e('Hide post title','announcement-bar') ?></label>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$value = isset( $options['ab_content_teaser_length'] ) && ! empty( $options['ab_content_teaser_length'] ) ? $options['ab_content_teaser_length'] : $default_content_teaser_length;
				?>
				<input type="text" id="ab_content_teaser_length" name="<?php echo $option_name; ?>[ab_content_teaser_length]" value="<?php echo $value; ?>" class="small-text"> 
				<span class="description"><?php _e('Words length displayed in the bar', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php 
					$order_by = array(
						'date' => __('Date', 'themify'),
						'id' => __('Id', 'themify'),
						'author' => __('Author', 'themify'),
						'title' => __('Title', 'themify'),
						'name' => __('Name', 'themify'),
						'modified' => __('Modified', 'themify'),
						'rand' => __('Rand', 'themify'),
						'comment_count' => __('Comment Count', 'themify')
					);
					$selected = isset( $options['ab_order_by'] ) ? $options['ab_order_by'] : '';
				?>
				<select id="ab_order_by" name="<?php echo $option_name; ?>[ab_order_by]">
					<?php foreach( $order_by as $k => $order ): ?>
					<option value="<?php echo $k; ?>"<?php selected( $selected, $k); ?>><?php echo $order; ?></option>
					<?php endforeach; ?>
				</select> 

				<span class="description"><?php _e('Order by', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
					$orders = array(
						'desc' => __('Descending', 'announcement-bar'),
						'asc' => __('Ascending', 'announcement-bar')
					);
					$selected = isset( $options['ab_order'] ) ? $options['ab_order'] : ''; 
				?>
				<select id="ab_order" name="<?php echo $option_name; ?>[ab_order]">
					<?php foreach( $orders as $k => $v ): ?>
					<option value="<?php echo $k; ?>"<?php selected( $selected, $k); ?>><?php echo $v; ?></option>
					<?php endforeach; ?>
				</select> 
				<span class="description"><?php _e('Display Order', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$query_numbers = array();
				foreach( $number as $no ){
					if($no == 0) continue;
					array_push($query_numbers, $no);
				}
				$selected = isset( $options['ab_query_number'] ) ? $options['ab_query_number'] : 3;
				?>
				
				<select name="<?php echo $option_name; ?>[ab_query_number]">
					<?php foreach( $query_numbers as $num ): ?>
					<option value="<?php echo $num; ?>"<?php selected( $selected, $num ); ?>><?php echo $num; ?></option>
					<?php endforeach; ?>
				</select>
				<span class="description"><?php _e('Number of posts to query','announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$visibilities = array(1,2,3,4,5,6,7);
				$selected = isset( $options['ab_visible'] ) ? $options['ab_visible'] : '';
				?>
				<select id="ab_visible" name="<?php echo $option_name; ?>[ab_visible]">
					<?php foreach( $visibilities as $v ): ?>
					<option value="<?php echo $v; ?>"<?php selected( $selected, $v); ?>><?php echo $v; ?></option>
					<?php endforeach; ?>
				</select>
				<span class="description"><?php _e('Visible', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$selected = isset( $options['ab_scroll'] ) ? $options['ab_scroll'] : '';
				?>
				<select id="ab_scroll" name="<?php echo $option_name; ?>[ab_scroll]">
					<?php foreach( $visibilities as $v ): ?>
					<option value="<?php echo $v; ?>"<?php selected( $selected, $v); ?>><?php echo $v; ?></option>
					<?php endforeach; ?>
				</select>
				<span class="description"><?php _e('Scroll', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$effects = array(
					array( 'name' => __('Slide', 'announcement-bar'), 'value' => 'slide' ),
					array( 'name' => __('Fade', 'announcement-bar'), 'value' => 'fade' ),
					array( 'name' => __('Continuously', 'announcement-bar'), 'value' => 'continuously' )
				);
				$selected = isset( $options['ab_effect'] ) ? $options['ab_effect'] : '';
				?>
				<select id="ab_effect" name="<?php echo $option_name; ?>[ab_effect]">
					<?php foreach( $effects as $effect ): ?>
					<option value="<?php echo $effect['value']; ?>"<?php selected( $selected, $effect['value']); ?>><?php echo $effect['name']; ?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Effect', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				// Auto Play
				$autoplay = array();
				foreach( $number as $an ) {
					$new_n = $an == 0 ? 'off' : $an . ' secs';
					$new_an = array( 'name' => $new_n, 'value' => $an );
					array_push( $autoplay, $new_an );
				}
				$selected = isset( $options['ab_auto_play'] ) ? $options['ab_auto_play'] : '';
				?>
				<select id="ab_auto_play" name="<?php echo $option_name; ?>[ab_auto_play]">
					<?php foreach( $autoplay as $auto ): ?>
					<option value="<?php echo $auto['value']; ?>"<?php selected( $selected, $auto['value']); ?>><?php echo $auto['name']; ?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Auto Play', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$selected = isset( $options['ab_wrap'] ) ? $options['ab_wrap'] : '';
				?>
				<select id="ab_wrap" name="<?php echo $option_name; ?>[ab_wrap]">
					<?php foreach( $confirmation as $confirm ): ?>
					<option value="<?php echo $confirm['value']; ?>"<?php selected( $selected, $confirm['value']); ?>><?php echo $confirm['name']; ?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Wrap', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$selected = isset( $options['ab_speed'] ) ? $options['ab_speed'] : '';
				?>
				<select id="ab_speed" name="<?php echo $option_name; ?>[ab_speed]">
					<?php foreach( $speeds as $val => $name ): ?>
					<option value="<?php echo $val; ?>"<?php selected( $selected, $val); ?>><?php echo $name; ?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Speed', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$selected = isset( $options['ab_slider_nav'] ) ? $options['ab_slider_nav'] : '';
				?>
				<select id="ab_slider_nav" name="<?php echo $option_name; ?>[ab_slider_nav]">
					<?php foreach( $confirmation as $confirm ): ?>
					<option value="<?php echo $confirm['value']; ?>"<?php selected( $selected, $confirm['value']); ?>><?php echo $confirm['name']; ?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Slider Nav', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$selected = isset( $options['ab_pager'] ) ? $options['ab_pager'] : '';
				?>
				<select id="ab_pager" name="<?php echo $option_name; ?>[ab_pager]">
					<?php foreach( $confirmation as $confirm ): ?>
					<option value="<?php echo $confirm['value']; ?>"<?php selected( $selected, $confirm['value']); ?>><?php echo $confirm['name']; ?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Pager', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$checked = isset( $options['ab_show_timer'] ) ? $options['ab_show_timer'] : '';
				?>
				<input type="checkbox" id="ab_show_timer" name="<?php echo $option_name; ?>[ab_show_timer]" value="1"<?php checked( $checked, 1 ); ?>> 
				<span class="description"><?php _e('Show Timer','announcement-bar') ?></span>
			</td>
		</tr>		
	</table>

	<table class="form-table">
		<tr>
			<td><hr class="meta_fields_separator"></td>
		</tr>
	</table>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<span class="label"><?php _e('Start at', 'announcement-bar') ?></span>
			</th>
			<td>
				<?php $value = isset( $options['ab_start_at'] ) ? $options['ab_start_at'] : '';  ?>
				<input id="ab_start_at" type="text" name="<?php echo $option_name; ?>[ab_start_at]" class="themifyDatePicker regular-text" value="<?php echo $value; ?>" data-label="<?php _e('Pick Date','announcement-bar') ?>" data-close="<?php _e('Done', 'announcement-bar') ?>" data-dateformat="yy-mm-dd" data-timeformat="HH:mm:ss" data-timeseparator=" " data-clear="clear-ab_start_at">
				<input type="button" id="clear-ab_start_at" data-picker="ab_start_at" value="<?php _e('Clear Date', 'announcement-bar') ?>" class="button themifyClearDate themifyOpacityTransition">
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<span class="label"><?php _e('End at', 'announcement-bar') ?></span>
			</th>
			<td>
				<?php $value = isset( $options['ab_end_at'] ) ? $options['ab_end_at'] : '';  ?>
				<input id="ab_end_at" type="text" name="<?php echo $option_name; ?>[ab_end_at]" class="themifyDatePicker regular-text" value="<?php echo $value; ?>" data-label="<?php _e('Pick Date','announcement-bar') ?>" data-close="<?php _e('Done', 'announcement-bar') ?>" data-dateformat="yy-mm-dd" data-timeformat="HH:mm:ss" data-timeseparator=" " data-clear="clear-ab_end_at">
				<input type="button" id="clear-ab_end_at" data-picker="ab_end_at" value="<?php _e('Clear Date', 'announcement-bar') ?>" class="button themifyClearDate themifyOpacityTransition" >
				<br />
				<span class="description"><?php _e('Leave blank will show all time', 'announcement-bar') ?></span>
			</td>
		</tr>
	</table>

	<table class="form-table">
		<tr>
			<td><hr class="meta_fields_separator"></td>
		</tr>
	</table>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<span class="label"><?php _e('Position', 'announcement-bar') ?></span>
			</th>
			<td>
				<?php
				$layouts = array(
					array( 'name' => __('Top', 'announcement-bar'), 'value' => 'top', 'src' => 'bar-top.png' ),
					array( 'name' => __('bottom', 'announcement-bar'), 'value' => 'bottom', 'src' => 'bar-bottom.png' )
				);
				$value = isset( $options['ab_position']['align'] ) ? $options['ab_position']['align'] : 'top';
				?>
				<?php foreach( $layouts as $layout ): ?>
				<?php $class = $value == $layout['value'] ? ' selected' : ''; ?>
				<a class="preview-icon<?php echo $class; ?>" href="#" title="<?php echo $layout['name']; ?>">
					<img alt="<?php echo $layout['value']; ?>" src="<?php echo plugin_dir_url( dirname(__FILE__) ) . '/images/layout-icons/'.$layout['src']; ?>">
				</a>
				<?php endforeach; ?>

				<input type="hidden" class="val" name="<?php echo $option_name; ?>[ab_position][align]" value="<?php echo $value; ?>"> <br />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php
				$position_state = array(
					array( 'name' => __('Absolute', 'announcement-bar'), 'value' => 'absolute' ),
					array( 'name' => __('Fixed', 'announcement-bar'), 'value' => 'fixed' )
				);
				$checked = isset( $options['ab_position']['state'] ) ? $options['ab_position']['state'] : 'absolute';
				?>
				<?php foreach( $position_state as $state ):  ?>
				<input id="<?php echo $option_name; ?>_position_state_<?php echo $state['value']; ?>" type="radio" name="<?php echo $option_name; ?>[ab_position][state]" value="<?php echo $state['value']; ?>"<?php checked( $checked, $state['value']); ?>> 
				<label for="<?php echo $option_name; ?>_position_state_<?php echo $state['value']; ?>"><?php echo $state['name']; ?></label> 
				<?php endforeach; ?>
			</td>
		</tr>
	</table>

	<table class="form-table">
		<tr>
			<td><hr class="meta_fields_separator"></td>
		</tr>
	</table>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<span><?php _e('Close Button', 'announcement-bar') ?></span>
			</th>
			<td>
				<?php
				$close_state = array(
					array( 'name' => __('Toggleable', 'announcement-bar'), 'value' => 'toggleable' ),
					array( 'name' => __('Close', 'announcement-bar'), 'value' => 'close' ),
					array( 'name' => __('None', 'announcement-bar'), 'value' => 'none' )
				);
				$checked = isset( $options['ab_close_button'] ) ? $options['ab_close_button'] : 'toggleable';
				?>
				<?php foreach( $close_state as $state ): ?>
				<input id="<?php echo $option_name; ?>_ab_close_button_<?php echo $state['value']; ?>" type="radio" name="<?php echo $option_name; ?>[ab_close_button]" value="<?php echo $state['value']; ?>"<?php checked( $checked, $state['value']); ?>> 
				<label for="<?php echo $option_name; ?>_ab_close_button_<?php echo $state['value']; ?>"><?php echo $state['name']; ?></label>
				<?php endforeach; ?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"></th>
			<td>
				<?php $checked = isset( $options['ab_remember_close_state'] ) ? $options['ab_remember_close_state'] : ''; ?>
				<input id="ab_remember_close_state" type="checkbox" name="<?php echo $option_name; ?>[ab_remember_close_state]" value="1"<?php checked( $checked, 1 )?>> 
				<label for="ab_remember_close_state"><?php _e('Remember state on user\'s browser with cookie') ?></label>
			</td>
		</tr>
	</table>

	<table class="form-table">
		<tr>
			<td><hr class="meta_fields_separator"></td>
		</tr>
	</table>

	<table class="form-table">
		
		<tr valign="top">
			<th scope="row">
				<span class="label"><?php _e('Text Alignment', 'announcement-bar') ?></span>
			</th>
			<td>
				<?php
				$layouts = array(
					array( 'name' => __('Left', 'announcement-bar'), 'value' => 'textleft', 'src' => 'text-align-left.png' ),
					array( 'name' => __('Center', 'announcement-bar'), 'value' => 'textcenter', 'src' => 'text-align-center.png' ),
					array( 'name' => __('Right', 'announcement-bar'), 'value' => 'textright', 'src' => 'text-align-right.png' )
				);
				$value = isset( $options['ab_text_alignment'] ) ? $options['ab_text_alignment'] : 'textleft';
				?>
				<?php foreach( $layouts as $layout ): ?>
				<?php $class = $value == $layout['value'] ? ' selected' : ''; ?>
				<a class="preview-icon<?php echo $class; ?>" href="#" title="<?php echo $layout['value']; ?>">
					<img alt="<?php echo $layout['value']; ?>" src="<?php echo plugin_dir_url( dirname(__FILE__) ) . '/images/layout-icons/'.$layout['src']; ?>">
				</a>
				<?php endforeach; ?>

				<input type="hidden" class="val" name="<?php echo $option_name; ?>[ab_text_alignment]" value="<?php echo $value; ?>">
			</td>
		</tr>

		<tr valign="top" class="enable_toggle">
			<th scope="row">
				<span class="label"><?php _e('Appearance', 'announcement-bar') ?></span>
			</th>
			<td>
				<?php
					$appearance = array(
						array( 'name' => __('Presets', 'announcement-bar'), 'value' => 'presets' ),
						array( 'name' => __('Custom', 'announcement-bar'), 'value' => 'custom' )
					);
					$checked = isset( $options['ab_appearance'] ) ? $options['ab_appearance'] : 'presets'; 
				?>
				<?php foreach( $appearance as $app ): ?>
				<input id="<?php echo $option_name; ?>_ab_appearance_<?php echo $app['value']; ?>" type="radio" name="<?php echo $option_name; ?>[ab_appearance]" value="<?php echo $app['value']; ?>"<?php checked( $checked, $app['value'] ); ?>> 
				<label for="<?php echo $option_name; ?>_ab_appearance_<?php echo $app['value']; ?>"><?php echo $app['name']; ?></label>
				<?php endforeach; ?>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle presets-toggle">
			<th scope="row">
				<span><?php _e('Font Style', 'announcement-bar') ?></span>
			</th>
			<td>
				<?php
				$font_styles = array(
					array( 'name' => __('Default', 'announcement-bar'), 'value' => 'default-font', 'src' => 'default.png' ),
					array( 'name' => __('Serif', 'announcement-bar'), 'value' => 'serif', 'src' => 'font-serif.png' ),
					array( 'name' => __('Old Style', 'announcement-bar'), 'value' => 'old-style', 'src' => 'font-old-style.png' ),
					array( 'name' => __('Slab Serif', 'announcement-bar'), 'value' => 'slab-serif', 'src' => 'font-slab-serif.png' ),
					array( 'name' => __('Script', 'announcement-bar'), 'value' => 'script', 'src' => 'font-script.png' )
				);
				$value = isset( $options['ab_font_style'] ) ? $options['ab_font_style'] : 'default-font';
				?>

				<?php foreach( $font_styles as $style ): ?>
				<?php $class = $value == $style['value'] ? ' selected' : ''; ?>
				<a class="preview-icon<?php echo $class; ?>" href="#" title="<?php echo $style['name']; ?>">
					<img alt="<?php echo $style['value']; ?>" src="<?php echo plugin_dir_url( dirname(__FILE__) ) . '/images/layout-icons/'.$style['src']; ?>">
				</a>
				<?php endforeach; ?>

				<input type="hidden" class="val" name="<?php echo $option_name; ?>[ab_font_style]" value="<?php echo $value; ?>">
			</td>
		</tr>

		<tr valign="top" class="themify-toggle presets-toggle">
			<th scope="row">
				<span class="label"><?php _e('Design Style', 'announcement-bar') ?></span>
			</th>
			<td>
				<?php
				$design_styles = array(
					array( 'name' => __('Default', 'announcement-bar'), 'value' => 'default-color', 'src' => 'color-default.png' ),
					array( 'name' => __('White', 'announcement-bar'), 'value' => 'white', 'src' => 'color-white.png' ),
					array( 'name' => __('Yellow', 'announcement-bar'), 'value' => 'yellow', 'src' => 'color-yellow.png' ),
					array( 'name' => __('Blue', 'announcement-bar'), 'value' => 'blue', 'src' => 'color-blue.png' ),
					array( 'name' => __('Green', 'announcement-bar'), 'value' => 'green', 'src' => 'color-green.png' ),
					array( 'name' => __('Pink', 'announcement-bar'), 'value' => 'pink', 'src' => 'color-pink.png' ),
					array( 'name' => __('Purple', 'announcement-bar'), 'value' => 'purple', 'src' => 'color-purple.png' ),
					array( 'name' => __('Orange', 'announcement-bar'), 'value' => 'orange', 'src' => 'color-orange.png' ),
					array( 'name' => __('Black', 'announcement-bar'), 'value' => 'black', 'src' => 'color-black.png' ),
					array( 'name' => __('Gray', 'announcement-bar'), 'value' => 'gray', 'src' => 'color-gray.png' ),
					array( 'name' => __('Paper', 'announcement-bar'), 'value' => 'paper', 'src' => 'design-paper.png' ),
					array( 'name' => __('Notes', 'announcement-bar'), 'value' => 'notes', 'src' => 'design-notes.png' ),
					array( 'name' => __('Paper Clip', 'announcement-bar'), 'value' => 'clip', 'src' => 'design-clip.png' ),
					array( 'name' => __('Bookmark', 'announcement-bar'), 'value' => 'bookmark', 'src' => 'design-bookmark.png' ),
				);
				$value = isset( $options['ab_design_style'] ) ? $options['ab_design_style'] : 'default-color';
				?>
				<?php foreach( $design_styles as $style ): ?>
				<?php $class = $value == $style['value'] ? ' selected' : ''; ?>
				<a class="preview-icon<?php echo $class; ?>" href="#" title="<?php echo $style['name']; ?>">
					<img alt="<?php echo $style['value']; ?>" src="<?php echo plugin_dir_url( dirname(__FILE__) ) . '/images/layout-icons/'.$style['src']; ?>">
				</a>
				<?php endforeach; ?>

				<input type="hidden" class="val" name="<?php echo $option_name; ?>[ab_design_style]" value="<?php echo $value; ?>">
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row">
				<span class="label"><?php _e('Bar Background', 'announcement-bar') ?></span>
			</th>
			<td>
				<?php $value = isset( $options['bar_background_color'] ) ? $options['bar_background_color'] : ''; ?>
				<span class="colorSelect"></span>
				<input type="text" name="<?php echo $option_name; ?>[bar_background_color]" value="<?php echo $value; ?>" class="colorSelectInput" size="16">
				<input type="button" class="button clearColor" value="<?php _e('Clear', 'announcement-bar') ?>">
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row">
			</th>
			<td>
				<?php $checked = isset( $options['bar_background_color_transparent'] ) ? $options['bar_background_color_transparent'] : '';  ?>
				<input type="checkbox" id="bar_background_color_transparent" name="<?php echo $option_name; ?>[bar_background_color_transparent]" value="1"<?php checked( $checked, 1 ); ?>> 
				<label for="bar_background_color_transparent"><?php _e('Transparent', 'announcement-bar') ?></label>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row">
			</th>
			<td class="themify_field">
				<?php $value = isset( $options['bar_background_image'] ) ? $options['bar_background_image'] : ''; ?>
				<div class="themify_upload_preview"></div>
				<input type="text" name="<?php echo $option_name; ?>[bar_background_image]" id="bar_background_image" class="regular-text" value="<?php echo $value; ?>">
				<?php echo themify_get_uploader('bar_background_image', array('tomedia' => true)); ?>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php
					$bar_bg_repeat = array(
						array('name' => __('Repeat', 'announcement-bar'), 'value' => 'repeat' ),
						array('name' => __('Repeat Horizontally', 'announcement-bar'), 'value' => 'repeat-x' ),
						array('name' => __('Repeat Vertically', 'announcement-bar'), 'value' => 'repeat-y' ),
						array('name' => __('Do not Repeat', 'announcement-bar'), 'value' => 'no-repeat' ),
					);
					$selected = isset( $options['bar_background_repeat'] ) ? $options['bar_background_repeat'] : ''; 
				?>

				<select id="bar_background_repeat" name="<?php echo $option_name; ?>[bar_background_repeat]">
					<?php foreach( $bar_bg_repeat as $repeat ): ?>
					<option value="<?php echo $repeat['value']; ?>"<?php selected( $selected, $repeat['value']); ?>><?php echo $repeat['name']; ?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Background Repeat') ?></span>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php
					$bar_bg_pos_x = array(
						array(
							'value' => 'left',
							'name' => __('Left', 'announcement-bar')
						),
						array(
							'value' => 'center',
							'name' => __('Center', 'announcement-bar')
						),
						array(
							'value' => 'right',
							'name' => __('Right', 'announcement-bar')
						)
					);
					$selected = isset( $options['bar_background_position']['x'] ) ? $options['bar_background_position']['x'] : ''; 
				?>

				<select name="<?php echo $option_name; ?>[bar_background_position][x]">
					<option></option>
				<?php foreach( $bar_bg_pos_x as $pos_x ): ?>
					<option value="<?php echo $pos_x['value']; ?>"<?php selected( $selected, $pos_x['value'] ); ?>><?php echo $pos_x['name']; ?></option>
				<?php endforeach; ?>
				</select>

				<?php
					$bar_bg_pos_y = array(
						array(
							'value' => 'top',
							'name' => __('Top', 'announcement-bar')
						),
						array(
							'value' => 'center',
							'name' => __('Center', 'announcement-bar')
						),
						array(
							'value' => 'bottom',
							'name' => __('Bottom', 'announcement-bar')
						)
					);
					$selected = isset( $options['bar_background_position']['y'] ) ? $options['bar_background_position']['y'] : '';
				?>

				<select name="<?php echo $option_name; ?>[bar_background_position][y]">
					<option></option>
					<?php foreach( $bar_bg_pos_y as $pos_y ): ?>
					<option value="<?php echo $pos_y['value']; ?>"<?php selected( $selected, $pos_y['value'] ); ?>><?php echo $pos_y['name']?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Background Position', 'announcement-bar') ?></span>
			</td>
		</tr>

	</table>

	<table class="form-table">
		<tr>
			<td><hr class="meta_fields_separator themify-toggle custom-toggle"></td>
		</tr>
	</table>

	<table class="form-table">
		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"><span class="label"><?php _e('Announcement Title', 'announcement-bar') ?></span></th>
			<td>
				<?php
					$fonts = themify_get_web_safe_font_list();
					$google_fonts_list = themify_get_google_web_fonts_list();
					$font_unit = array('px', 'em');
					$font_size_val = isset( $options['announcement_title_font']['size'] ) ? $options['announcement_title_font']['size'] : '';
					$font_unit_selected = isset( $options['announcement_title_font']['unit'] ) ? $options['announcement_title_font']['unit'] : '';
					$font_family_selected = isset( $options['announcement_title_font']['family'] ) ? $options['announcement_title_font']['family'] : '';
				?>
				<input type="text" size="5" name="<?php echo $option_name; ?>[announcement_title_font][size]" value="<?php echo $font_size_val; ?>">
				<select name="<?php echo $option_name; ?>[announcement_title_font][unit]">
					<?php foreach( $font_unit as $unit ): ?>
					<option value="<?php echo $unit; ?>"<?php selected( $font_unit_selected, $unit ); ?>><?php echo $unit; ?></option>
					<?php endforeach; ?>
				</select>

				<select name="<?php echo $option_name; ?>[announcement_title_font][family]">
					<option></option>
					<optgroup label="<?php _e('Web Safe Fonts', 'announcement-bar') ?>">
						<?php foreach( $fonts as $font ): ?>
							<?php if ( empty( $font['value'] ) || $font['value'] == 'default' ) continue; ?>
							<option value="<?php echo $font['value']; ?>"<?php selected( $font_family_selected, $font['value']); ?>><?php echo $font['name']; ?></option>
						<?php endforeach; ?>
					</optgroup>

					<?php if ( is_array( $google_fonts_list ) && sizeof( $google_fonts_list ) > 0 ): ?>
					<optgroup label="<?php _e('Google Fonts', 'announcement-bar') ?>">
						<?php foreach( $google_fonts_list as $font ):  ?>
						<?php if ( empty( $font['value'] ) ) continue; ?>
						<option value="<?php echo $font['value']; ?>"<?php selected( $font_family_selected, $font['value']); ?>><?php echo $font['name']; ?></option>
						<?php endforeach; ?>
					</optgroup>
					<?php endif; ?>
				</select>
				<span class="description"><?php _e('Font', 'announcement-bar') ?></span>
			</td>
		<tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php $value = isset( $options['announcement_title_color'] ) ? $options['announcement_title_color'] : '';  ?>
				<span class="colorSelect"></span>
				<input type="text" name="<?php echo $option_name; ?>[announcement_title_color]" value="<?php echo $value; ?>" class="colorSelectInput" size="16" value="<?php echo $value; ?>">
				<input type="button" class="button clearColor" value="<?php _e('Clear', 'announcement-bar') ?>">

				<span class="description"><?php _e('Color', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php $value = isset( $options['announcement_title_background_color'] ) ? $options['announcement_title_background_color'] : '';  ?>
				<span class="colorSelect"></span>
				<input type="text" name="<?php echo $option_name; ?>[announcement_title_background_color]" value="<?php echo $value; ?>" class="colorSelectInput" size="16" value="<?php echo $value; ?>">
				<input type="button" class="button clearColor" value="<?php _e('Clear', 'announcement-bar') ?>">

				<span class="description"><?php _e('Background', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></label></th>
			<td>
				<?php $checked = isset( $options['announcement_title_background_transparent'] ) ? $options['announcement_title_background_transparent'] : '';  ?>
				<input type="checkbox" id="announcement_title_background_transparent" name="<?php echo $option_name; ?>[announcement_title_background_transparent]" value="1"<?php checked( $checked, 1); ?>> 
				<label for="announcement_title_background_transparent"><?php _e('Transparent', 'announcement-bar') ?></label>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row">
			</th>
			<td class="themify_field">
				<?php $value = isset( $options['announcement_title_background_image'] ) ? $options['announcement_title_background_image'] : '';  ?>
				<div class="themify_upload_preview"></div>
				<input type="text" name="<?php echo $option_name; ?>[announcement_title_background_image]" id="announcement_title_background_image" class="regular-text" value="<?php echo $value; ?>">
				<?php echo themify_get_uploader('announcement_title_background_image', array('tomedia' => true)); ?>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php $selected = isset( $options['announcement_title_background_repeat'] ) ? $options['announcement_title_background_repeat'] : '';  ?>
				<select id="announcement_title_background_repeat" name="<?php echo $option_name; ?>[announcement_title_background_repeat]">
					<?php foreach( $bar_bg_repeat as $repeat ): ?>
					<option value="<?php echo $repeat['value']; ?>"<?php selected( $selected, $repeat['value'] ); ?>><?php echo $repeat['name']; ?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Background Repeat') ?></span>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php $selected = isset( $options['announcement_title_background_position']['x'] ) ? $options['announcement_title_background_position']['x'] : ''; ?>
				<select name="<?php echo $option_name; ?>[announcement_title_background_position][x]">
					<option></option>
				<?php foreach( $bar_bg_pos_x as $pos_x ): ?>
					<option value="<?php echo $pos_x['value']; ?>"<?php selected( $selected, $pos_x['value']); ?>><?php echo $pos_x['name']; ?></option>
				<?php endforeach; ?>
				</select>

				<?php $selected = isset( $options['announcement_title_background_position']['y'] ) ? $options['announcement_title_background_position']['y'] : ''; ?>
				<select name="<?php echo $option_name; ?>[announcement_title_background_position][y]">
					<option></option>
					<?php foreach( $bar_bg_pos_y as $pos_y ): ?>
					<option value="<?php echo $pos_y['value']; ?>"<?php selected( $selected, $pos_y['value']); ?>><?php echo $pos_y['name']?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Background Position', 'announcement-bar') ?></span>
			</td>
		</tr>

	</table>

	<table class="form-table">
		<tr>
			<td><hr class="meta_fields_separator themify-toggle custom-toggle"></td>
		</tr>
	</table>

	<table class="form-table">
		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"><span class="label"><?php _e('Announcement Content', 'announcement-bar') ?></span></th>
			<td>
				<?php
				$font_size_val = isset( $options['announcement_content_font']['size'] ) ? $options['announcement_content_font']['size'] : '';
				$font_unit_selected = isset( $options['announcement_content_font']['unit'] ) ? $options['announcement_content_font']['unit'] : '';
				$font_family_selected = isset( $options['announcement_content_font']['family'] ) ? $options['announcement_content_font']['family'] : '';
				?>
				<input type="text" size="5" name="<?php echo $option_name; ?>[announcement_content_font][size]" value="<?php echo $font_size_val; ?>">
				<select name="<?php echo $option_name; ?>[announcement_content_font][unit]">
					<?php foreach( $font_unit as $unit ): ?>
					<option value="<?php echo $unit; ?>"<?php selected( $font_unit_selected, $unit ); ?>><?php echo $unit; ?></option>
					<?php endforeach; ?>
				</select>

				<select name="<?php echo $option_name; ?>[announcement_content_font][family]">
					<option></option>
					<optgroup label="<?php _e('Web Safe Fonts', 'announcement-bar') ?>">
						<?php foreach( $fonts as $font ): ?>
							<?php if ( empty( $font['value'] ) || $font['value'] == 'default' ) continue; ?>
							<option value="<?php echo $font['value']; ?>"<?php selected( $font_family_selected, $font['value']); ?>><?php echo $font['name']; ?></option>
						<?php endforeach; ?>
					</optgroup>

					<?php if ( is_array( $google_fonts_list ) && sizeof( $google_fonts_list ) > 0 ): ?>
					<optgroup label="<?php _e('Google Fonts', 'announcement-bar') ?>">
						<?php foreach( $google_fonts_list as $font ):  ?>
						<?php if ( empty( $font['value'] ) ) continue; ?>
						<option value="<?php echo $font['value']; ?>"<?php selected( $font_family_selected, $font['value']); ?>><?php echo $font['name']; ?></option>
						<?php endforeach; ?>
					</optgroup>
					<?php endif; ?>
				</select>
				<span class="description"><?php _e('Font', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php $value = isset( $options['announcement_content_color'] ) ? $options['announcement_content_color'] : '';  ?>
				<span class="colorSelect"></span>
				<input type="text" name="<?php echo $option_name; ?>[announcement_content_color]" value="<?php echo $value; ?>" class="colorSelectInput" size="16">
				<input type="button" class="button clearColor" value="<?php _e('Clear', 'announcement-bar') ?>">

				<span class="description"><?php _e('Color', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php $value = isset( $options['announcement_content_link_color'] ) ? $options['announcement_content_link_color'] : '';  ?>
				<span class="colorSelect"></span>
				<input type="text" name="<?php echo $option_name; ?>[announcement_content_link_color]" value="<?php echo $value; ?>" class="colorSelectInput" size="16">
				<input type="button" class="button clearColor" value="<?php _e('Clear', 'announcement-bar') ?>">

				<span class="description"><?php _e('Link Color', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php 
					$link_decoration = array(
						array( 'name' => __('None', 'announcement-bar'), 'value' => 'none' ),
						array( 'name' => __('Underline', 'announcement-bar'), 'value' => 'underline' ),
						array( 'name' => __('Overline', 'announcement-bar'), 'value' => 'overline' ),
						array( 'name' => __('Line Through', 'announcement-bar'), 'value' => 'line-through' ),
					);
					$selected = isset( $options['announcement_content_link_decoration'] ) ? $options['announcement_content_link_decoration'] : '';
				?>
				<select id="announcement_content_link_decoration" name="<?php echo $option_name; ?>[announcement_content_link_decoration]">
					<option></option>
					<?php foreach( $link_decoration as $decor ):  ?>
					<option value="<?php echo $decor['value']; ?>"<?php selected( $selected, $decor['value']); ?>><?php echo $decor['name']; ?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Link Decoration') ?></span>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php $value = isset( $options['announcement_content_link_hover_color'] ) ? $options['announcement_content_link_hover_color'] : ''; ?>
				<span class="colorSelect"></span>
				<input id="announcement_content_link_hover_color" type="text" name="<?php echo $option_name; ?>[announcement_content_link_hover_color]" value="<?php echo $value; ?>" class="colorSelectInput" size="16">
				<input type="button" class="button clearColor" value="<?php _e('Clear', 'announcement-bar') ?>">

				<span class="description"><?php _e('Link Hover Color', 'announcement-bar') ?></span>
			</td>
		</tr>

		<tr valign="top" class="themify-toggle custom-toggle">
			<th scope="row"></th>
			<td>
				<?php $selected = isset( $options['announcement_content_link_hover_decoration'] ) ? $options['announcement_content_link_hover_decoration'] : ''; ?>
				<select id="announcement_content_link_hover_decoration" name="<?php echo $option_name; ?>[announcement_content_link_hover_decoration]">
					<option></option>
					<?php foreach( $link_decoration as $decor ):  ?>
					<option value="<?php echo $decor['value']; ?>"<?php selected( $selected, $decor['value']); ?>><?php echo $decor['name']; ?></option>
					<?php endforeach; ?>
				</select>

				<span class="description"><?php _e('Link Hover Decoration') ?></span>
			</td>
		</tr>

	</table>

	<table class="form-table">
		<tr>
			<td><hr class="meta_fields_separator"></td>
		</tr>
	</table>

	<table class="form-table">
		<tr valign="top" class="">
			<th scope="row"><span class="label"><?php _e('Google Map Key', 'announcement-bar') ?></span></th>
			<td>
				<?php
				$google_map_key = isset( $options['google_map_key'] ) ? $options['google_map_key'] : '';
				?>
				<input type="text" size="50" name="<?php echo $option_name; ?>[google_map_key]" value="<?php echo $google_map_key; ?>">
				<p class="description"><?php printf( __('Google API key is required to use [announcement_map] shortcode. <a href="%s">Generate an API key</a> and insert it here.', 'announcement-bar'), 'http://developers.google.com/maps/documentation/javascript/get-api-key#key' ); ?></p>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>
	</form>

	<!-- Themify Login -->
	<!-- alerts -->
	<div class="alert"></div> 
	<!-- /alerts -->
	
	<!-- prompts -->
	<div class="prompt-box">
		<div class="show-login">
			<form id="themify_update_form" method="post" action="<?php echo admin_url( 'admin.php?page=announcement-bar&action=upgrade&login=true' ); ?>">
			<p class="prompt-msg"><?php _e('Enter your Themify login info to upgrade', 'announcement-bar'); ?></p>
			<p><label><?php _e('Username', 'announcement-bar'); ?></label> <input type="text" name="username" class="username" value=""/></p>
			<p><label><?php _e('Password', 'announcement-bar'); ?></label> <input type="password" name="password" class="password" value=""/></p>
			<input type="hidden" value="true" name="login" />
			<p class="pushlabel"><input name="login" type="submit" value="Login" class="button ab-upgrade-login" /></p>
			</form>
		</div>
		<div class="show-error">
			<p class="error-msg"><?php _e('There were some errors updating the theme', 'announcement-bar'); ?></p>
		</div>
	</div>
	<!-- /prompts -->
</div>