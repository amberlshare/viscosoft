<?php
$custom_class = isset( $custom_class ) ? $custom_class : '';
$arr_classes = array( $announcement_layout, $ab_text_alignment, $ab_font_style, $ab_design_style, $custom_class );
if ( 'bar' == $announcement_layout && is_array( $ab_position ) ) { 
	$arr_classes = array_merge( $arr_classes, $ab_position );
}
if(isset($arr_classes['align'])){
    $arr_classes['align']='announcement_'.$arr_classes['align'];
}
$announce_class = implode( ' ', $arr_classes );

// Speed settings
switch ( $ab_speed ) {
	case 'slow':
		$ab_speed = 4;
	break;
	
	case 'fast':
		$ab_speed = '.5';
	break;

	default:
	 $ab_speed = 1;
	break;
}

// Print custom Stylesheet
if ( isset( $custom_style_appearance ) ) {
	echo $custom_style_appearance;
}

if ( $posts->have_posts() ):
?>
<div id="<?php echo $announcement_bar_id; ?>" class="themify_announcement <?php echo $announce_class; ?>">
	
	<?php if( isset( $ab_close_button ) && $ab_close_button == 'toggleable' ): ?>
	<div class="announcement_container close-container">
		<a href="#" class="toggle-close">d</a>
	</div>
	<?php endif; ?>

	<div class="announcement_container">
		<ul class="announcement_list" 
			data-id="<?php echo $announcement_bar_id; ?>" 
			data-visible="<?php echo $ab_visible; ?>" 
			data-effect="<?php echo $ab_effect; ?>" 
			data-speed="<?php echo $ab_speed; ?>" 
			data-scroll="<?php echo $ab_scroll; ?>" 
			data-auto-scroll="<?php echo $ab_auto_play; ?>" 
			data-wrap="<?php echo $ab_wrap; ?>" 
			data-arrow="<?php echo $ab_slider_nav; ?>" 
			data-pagination="<?php echo $ab_pager; ?>" 
			data-timer="<?php echo $ab_show_timer; ?>" 
			data-remember-close="<?php echo isset( $ab_remember_close_state) ? $ab_remember_close_state : 0; ?>">
			
			<?php global $post; $temp_post = $post; ?>
			<?php while( $posts->have_posts() ) : $posts->the_post(); ?>
			<?php

				// Skip post if end date has passed
				$end_date = get_post_meta( $post->ID, 'ab_end_at', true );
				$current_date = date_i18n( 'Y-m-d H:i:s' );
				if ( ! empty( $end_date ) && ( strtotime( $end_date ) < strtotime( $current_date ) ) ) {
					continue;
				}

				$content = announcement_bar_get_contents();

				// Check more link
				$more_link = sprintf( ' <a class="more-link" href="%s">%s</a>', get_permalink( get_the_id() ), $content['more_link'] );

				// Button Text
				$meta_button_text = get_post_meta( $post->ID, 'ab_button_text', true );
				$meta_button_link = get_post_meta( $post->ID, 'ab_button_link', true );
				$button_link = sprintf( '<a href="%s" class="action-button">%s</a>', esc_url( $meta_button_link ), $meta_button_text );
				$button_link = (!empty( $meta_button_text ) && !empty( $meta_button_link )) ? $button_link : '';

				$teaser = $content['teaser'] . $button_link;
				$teaser .= ! empty( $content['content'] ) ? $more_link : '';
				$content_more = $content['content'];
			?>
			<li>
				<div class="announcement_post">
					<?php if ( ! $ab_hide_title && ! announcement_bar_meta_check( get_the_id(), 'ab_hide_title' ) ): ?>
					<div class="announcement_title">
						<?php
							// Link
							$get_ext_link = get_post_meta( get_the_id(), 'ab_external_link', true );
							$link = $get_ext_link != '' ? '<a href="'.$get_ext_link.'">' : '';
							$link .= get_the_title();
							$link .= $get_ext_link != '' ? '</a>' : '';
							echo $link;
						?>
					</div>
					<?php endif; ?>
					
					<div class="announcement_content">
						<?php echo apply_filters( 'announcement_bar_teaser', $teaser ); ?>
					</div>
					<!-- /announcement_content -->
					<div class="more_wrap">
						<?php
							echo apply_filters( 'announcement_bar_the_content', $content_more );
						?>
					</div>
					<!-- /more_wrap -->
				</div>
				<!-- /.announcement_post -->
			</li>
		<?php endwhile; wp_reset_postdata(); $post = $temp_post; ?>
		</ul>
		<!-- /.announcement_list -->
		
		<?php if ( ( $ab_show_timer == 1 || $ab_show_timer == 'on' ) && 0 != $ab_auto_play ): ?>
		<div class="timer">
			<div class="timer-bar"></div>
		</div>
		<!-- /.timer -->
		<?php endif; ?>

		<?php
		$slider_navigation = 'yes' == $ab_slider_nav || 'yes' == $ab_pager ? '<div class="carousel-nav-wrap">' : '';
		$slider_navigation .= 'yes' == $ab_slider_nav ? '<a href="#" class="carousel-prev">«</a>' : '';
		$slider_navigation .= 'yes' == $ab_pager ? '<div class="carousel-pager"></div>' : '';
		$slider_navigation .= 'yes' == $ab_slider_nav ? '<a href="#" class="carousel-next">»</a>' : '';
		$slider_navigation .= 'yes' == $ab_slider_nav || 'yes' == $ab_pager ? '</div>' : '';
		
		echo $slider_navigation;
		?>
		
		<?php if( $ab_close_button != 'none'): ?>
		<a href="#" class="close" data-type="<?php echo $ab_close_button; ?>"><?php _e('c', 'announcement-bar') ?></a>
		<?php endif; ?>
	</div>
	<!-- /announcement_container -->
</div>
<!-- /themify_announcement -->

<?php else: ?>

<div class="themify_announcement <?php echo $announce_class; ?>">
	<div class="announcement_container">
		<p><?php _e('No Announcement posts', 'announcement-bar') ?></p>
	</div>
		<!-- /announcement_container -->
</div>
<!-- /themify_announcement -->

<?php endif; ?>