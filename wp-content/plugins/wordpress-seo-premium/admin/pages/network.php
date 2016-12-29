<?php
/**
 * @package WPSEO\Admin
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

$yform = Yoast_Form::get_instance();

$options = get_site_option( 'wpseo_ms' );

if ( isset( $_POST['wpseo_submit'] ) ) {
	check_admin_referer( 'wpseo-network-settings' );

	foreach ( array( 'access', 'defaultblog' ) as $opt ) {
		$options[ $opt ] = $_POST['wpseo_ms'][ $opt ];
	}
	unset( $opt );
	WPSEO_Options::update_site_option( 'wpseo_ms', $options );
	add_settings_error( 'wpseo_ms', 'settings_updated', __( 'Settings Updated.', 'wordpress-seo' ), 'updated' );
}

if ( isset( $_POST['wpseo_restore_blog'] ) ) {
	check_admin_referer( 'wpseo-network-restore' );
	if ( isset( $_POST['wpseo_ms']['restoreblog'] ) && is_numeric( $_POST['wpseo_ms']['restoreblog'] ) ) {
		$restoreblog = (int) WPSEO_Utils::validate_int( $_POST['wpseo_ms']['restoreblog'] );
		$blog        = get_blog_details( $restoreblog );

		if ( $blog ) {
			WPSEO_Options::reset_ms_blog( $restoreblog );
			add_settings_error( 'wpseo_ms', 'settings_updated', sprintf( __( '%s restored to default SEO settings.', 'wordpress-seo' ), esc_html( $blog->blogname ) ), 'updated' );
		}
		else {
			add_settings_error( 'wpseo_ms', 'settings_updated', sprintf( __( 'Blog %s not found.', 'wordpress-seo' ), esc_html( $restoreblog ) ), 'error' );
		}
		unset( $restoreblog, $blog );
	}
}

/* Set up selectbox dropdowns for smaller networks (usability) */
$use_dropdown = true;
if ( get_blog_count() > 100 ) {
	$use_dropdown = false;
}
else {
	$sites = wp_get_sites( array( 'deleted' => 0 ) );
	if ( is_array( $sites ) && $sites !== array() ) {
		$dropdown_input = array(
			'-' => __( 'None', 'wordpress-seo' ),
		);

		foreach ( $sites as $site ) {
			$dropdown_input[ $site['blog_id'] ] = $site['blog_id'] . ': ' . $site['domain'];

			$blog_states = array();
			if ( $site['public'] === '1' ) {
				$blog_states[] = __( 'public', 'wordpress-seo' );
			}
			if ( $site['archived'] === '1' ) {
				$blog_states[] = __( 'archived', 'wordpress-seo' );
			}
			if ( $site['mature'] === '1' ) {
				$blog_states[] = __( 'mature', 'wordpress-seo' );
			}
			if ( $site['spam'] === '1' ) {
				$blog_states[] = __( 'spam', 'wordpress-seo' );
			}
			if ( $blog_states !== array() ) {
				$dropdown_input[ $site['blog_id'] ] .= ' [' . implode( ', ', $blog_states ) . ']';
			}
		}
		unset( $site, $blog_states );
	}
	else {
		$use_dropdown = false;
	}
	unset( $sites );
}

$yform->admin_header( false, 'wpseo_ms' );

echo '<h2>', __( 'MultiSite Settings', 'wordpress-seo' ), '</h2>';
echo '<form method="post" accept-charset="', esc_attr( get_bloginfo( 'charset' ) ), '">';
wp_nonce_field( 'wpseo-network-settings', '_wpnonce', true, true );

/* @internal Important: Make sure the options added to the array here are in line with the options set in the WPSEO_Optio