<?php
/**
 * Announcement Bar Functions
 */

if ( ! function_exists( 'announcement_bar_render' ) ) {
	function announcement_bar_render( $template, $settings, $echo = true ) {
		if ( $echo ) {
			announcement_bar_retrieve_template( $template, $settings, '', '', $echo );
		} else {
			return announcement_bar_retrieve_template( $template, $settings, '', '', $echo );
		}
	}
}

if ( ! function_exists( 'announcement_bar_retrieve_template' ) ) {
	/**
	 * Retrieve templates
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 * @param bool $echo
	 * @return string
	 */
	function announcement_bar_retrieve_template( $template_name, $args = array(), $template_path = '', $default_path = '', $echo = true ) {
		ob_start();
		announcement_bar_get_template( $template_name, $args, $template_path = '', $default_path = '' );
		if ( $echo )
			echo ob_get_clean();
		else
			return ob_get_clean();
	}
}

if ( ! function_exists( 'announcement_bar_get_template' ) ) {
	/**
	 * Get template
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 */
	function announcement_bar_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) )
			extract( $args );

		$located = announcement_bar_locate_template( $template_name, $template_path, $default_path );

		include( $located );
	}
}

if ( ! function_exists( 'announcement_bar_locate_template' ) ) {
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *		yourtheme		/	$template_path	/	$template_name
	 *		$default_path	/	$template_name
	 */
	function announcement_bar_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) $template_path = plugin_basename( dirname( __FILE__ ) ) . '/';
		if ( ! $default_path ) $default_path = plugin_dir_path( __FILE__ ) . 'templates/';

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name
			)
		);

		// Get default template
		if ( ! $template )
			$template = $default_path . $template_name;

		// Return what we found
		return apply_filters('announcement_bar_locate_template', $template, $template_name, $template_path);
	}
}

if ( ! function_exists( 'announcement_bar_meta_check' ) ) {
	function announcement_bar_meta_check($postid, $meta_key, $single=true) {
		$value = get_post_meta( $postid, $meta_key, $single );
		if ( $value !== '' ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'announcement_bar_trim_words' ) ) {
	/**
	 * Trim Words
	 * @param string $text 
	 * @param int $num_words 
	 * @param string $more 
	 * @return string
	 */
	function announcement_bar_trim_words( $text, $num_words = 55, $more = null, $meta = null ) {
		$original_text = $text;
		/* translators: If your word count is based on single characters (East Asian characters),
		   enter 'characters'. Otherwise, enter 'words'. Do not translate into your own language. */
		if ( 'characters' == _x( 'words', 'word count: words or characters?' ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
			$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
			preg_match_all( '/./u', $text, $words_array );
			$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
			$sep = '';
		} else {
			$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
			$sep = ' ';
		}
		if ( count( $words_array ) > $num_words ) {
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
			$text = $text . $meta . $more;
		} else {
			$text = implode( $sep, $words_array ) . $meta;
		}
		return balanceTags( $text, true );
	}
}

if ( ! function_exists( 'announcement_bar_get_content_more' ) ) {
	/**
	 * Get content more
	 * @param string $text 
	 * @param int $num_words 
	 * @return string
	 */
	function announcement_bar_get_content_more( $text, $num_words = 55 ) {
		/* translators: If your word count is based on single characters (East Asian characters),
		   enter 'characters'. Otherwise, enter 'words'. Do not translate into your own language. */
		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		if ( 'characters' == _x( 'words', 'word count: words or characters?' ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
			preg_match_all( '/./u', $text, $words_array );
			$words_array = array_slice( $words_array[0], $num_words );
			$sep = '';
		} else {
			$words_array = array_slice( explode(' ', $text), $num_words );
			$sep = ' ';
		}

		$text = implode( $sep, $words_array );
		return balanceTags( $text, true );
	}
}

if ( ! function_exists( 'announcement_bar_get_contents' ) ) {
	/**
	 * Get announcoment contents (teaser and content_more )
	 * @return array
	 */
	function announcement_bar_get_contents() {
		global $post;
		$announ_more = preg_match( '/<!--more(.*?)?-->/', $post->post_content, $readmore_matches );
		$output = array();
		$output['more_link'] = ( ! empty( $readmore_matches[1] ) ) ? $readmore_matches[1] : __( 'More', 'announcement-bar' );
		if ( $announ_more ) {
			$grab = explode( $readmore_matches[0], $post->post_content, 2 );
			$output['teaser'] = $grab[0];
			$output['content'] = $grab[1];
		} else {
			$output['teaser'] = announcement_bar_trim_words( get_the_content(''), apply_filters( 'ab_content_teaser_length', 15 ) );
			$output['content'] = announcement_bar_get_content_more( get_the_content(''), apply_filters( 'ab_content_teaser_length', 15 ) );
		}
		return apply_filters( 'announcement_bar_get_contents', $output );
	}
}

if ( ! function_exists( 'announcement_bar_flush_cache' ) ) {
	/**
	 * Clear all cache results in ABar
	 *
	 * @since 1.1.5
	 */
	function announcement_bar_flush_cache() {
		delete_transient( 'abar_display' );
		global $wpdb;
		$transients = $wpdb->get_results( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_abar_display_%' OR option_name LIKE '_transient_timeout_abar_display_%'" );
		if( is_array( $transients ) ) {
			foreach( $transients as $transient ) {
				delete_option( $transient->option_name );
			}
		}
	}
}

/**
 * Helper function to return "enable" string
 *
 * @since 1.2.0
 */
function __abar_return_enable() {
	return 'enable';
}

/**
 * Helper function to return "disable" string
 *
 * @since 1.2.0
 */
function __abar_return_disable() {
	return 'disable';
}

/**
 * Helper function to return "post" string
 *
 * @since 1.2.0
 */
function __abar_return_post() {
	return 'post';
}
