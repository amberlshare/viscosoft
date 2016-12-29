<?php
//Run this after the admin has been initialized so they appear as standard WordPress notices.
if( isset( $_GET['page'] ) && ! isset( $_GET['action'] ) && $_GET['page'] == 'announcement-bar' )
	add_action('admin_notices', 'announcement_bar_check_version', 3);

if(defined('WP_DEBUG') && WP_DEBUG){
	delete_transient('announcement_bar_new_update');
	delete_transient('announcement_bar_check_update');
}

/**
 * Set transient saving the current date and time of last version checking
 */
function announcement_bar_set_update(){
	$current = new stdClass();
	$current->lastChecked = time();
	set_transient( 'announcement_bar_check_update', $current );
}

/**
 * Get remote version from server
 * @param string $name
 */
function announcement_bar_get_remote_plugin_version( $name ) {
	$xml = new DOMDocument;
	$versions_url = 'http://themify.me/versions/versions.xml';
	$response = wp_remote_get( $versions_url );
	if( is_wp_error( $response ) ) 
		return;

	$body = trim( wp_remote_retrieve_body( $response ) );
	$xml->loadXML($body);
	$xml->preserveWhiteSpace = false;
	$xml->formatOutput = true;
	$xpath = new DOMXPath($xml);
	$query = "//version[@name='".$name."']";
	$version = '';

	$elements = $xpath->query($query);

	if( $elements->length ) {
		foreach ($elements as $field) {
			$version = $field->nodeValue;
		}
	}
	return $version;
}

/**
 * Check for new update
 */
function announcement_bar_check_version() {
	$notifications = '<style type="text/css">.notifications p.update {background: #F9F2C6;border: 1px solid #F2DE5B;} .notifications p{width: 765px;margin: 15px 0 0 5px;padding: 10px;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;}</style>';
	$version = ANNOUNCEMENT_BAR_CURRENT_VERSION;

	// Check update transient
	$current = get_transient('announcement_bar_check_update'); // get last check transient
	$timeout = 60;
	$time_not_changed = isset( $current->lastChecked ) && $timeout > ( time() - $current->lastChecked );
	$newUpdate = get_transient('announcement_bar_new_update'); // get new update transient

	if ( is_object( $newUpdate ) && $time_not_changed ) {
		if ( version_compare( $version, $newUpdate->version, '<') ) {
			$notifications .= sprintf( __('<p class="update %s">Announcement Bar version %s is now available. <a href="%s" title="" class="%s" target="%s">Update now</a> or view the <a href="https://themify.me/logs/%s-changelogs" title="" class="themify_changelogs" target="_blank" data-changelog="http://themify.me/changelogs/announcement-bar.txt">change log</a> for details.</p>', 'announcement-bar'),
				$newUpdate->login,
				$newUpdate->version,
				$newUpdate->url,
				$newUpdate->class,
				$newUpdate->target,
				'announcement-bar'
			);
			echo '<div class="notifications">'. $notifications . '</div>';
		}
		return;
	}

	// get remote version
	$remote_version = announcement_bar_get_remote_plugin_version( 'announcement-bar' );

	// delete update checker transient
	delete_transient( 'announcement_bar_check_update' );

	$class = "";
	$target = "";
	$url = "#";
	
	$new = new stdClass();
	$new->login = 'login';
	$new->version = $remote_version;
	$new->url = $url;
	$new->class = 'announcement-bar-upgrade-plugin';
	$new->target = $target;

	if ( version_compare( $version, $remote_version, '<' ) ) {
		set_transient( 'announcement_bar_new_update', $new );
		$notifications .= sprintf( __('<p class="update %s">Announcement Bar version %s is now available. <a href="%s" title="" class="%s" target="%s">Update now</a> or view the <a href="https://themify.me/logs/%s-changelogs" title="" class="themify_changelogs" target="_blank" data-changelog="http://themify.me/changelogs/announcement-bar.txt">change log</a> for details.</p>', 'announcement-bar'),
			$new->login,
			$new->version,
			$new->url,
			$new->class,
			$new->target,
			'announcement-bar'
		);
	}

	// update transient
	announcement_bar_set_update();

	echo '<div class="notifications">'. $notifications . '</div>';
}

/**
 * Check if update available
 */
function announcement_bar_is_update_available() {
	$version = ANNOUNCEMENT_BAR_CURRENT_VERSION;
	$newUpdate = get_transient('announcement_bar_new_update'); // get new update transient

	if ( false === $newUpdate ) {
		$new_version = announcement_bar_get_remote_plugin_version( 'announcement-bar' );
	} else {
		$new_version = $newUpdate->version;
	}

	if ( version_compare( $version, $new_version, '<') ) {
		return true;
	} else {
		false;
	}
}

// **** AUTHENTICATION TO THE SERVER ****** //
/**
 * Updater called through wp_ajax_ action
 */
function announcement_bar_updater(){
	
	// check version
	if ( ! announcement_bar_is_update_available() ) {
		_e('The plugin is at the latest version.', 'announcement-bar');
		die();
	}

	//are we going to update a theme?
	$url = 'http://themify.me/files/announcement-bar/announcement-bar.zip';
	
	//If login is required
	if($_GET['login'] == 'true'){

			$response = wp_remote_post(
				'http://themify.me/member/login.php',
				array(
					'timeout' => 300,
					'headers' => array(),
					'body' => array(
						'amember_login' => $_POST['username'],
						'amember_pass'  => $_POST['password']
					)
			    )
			);

			//Was there some error connecting to the server?
			if( is_wp_error( $response ) ) {
				$errorCode = $response->get_error_code();
				echo 'Error: ' . $errorCode;
				die();
			}

			//Connection to server was successful. Test login cookie
			$amember_nr = false;
			foreach($response['cookies'] as $cookie){
				if($cookie->name == 'amember_nr'){
					$amember_nr = true;
				}
			}
			if(!$amember_nr){
				_e('You are not a Themify Member.', 'announcement-bar');
				die();
			}
	}
	
	//remote request is executed after all args have been set
	include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once( ANNOUNCEMENT_BAR_PLUGIN_PATH . 'classes/class-announcement-upgrader.php');

	$plugin_slug = 'announcement-bar/announcement-bar.php';
	$upgrader = new Announcement_Upgrader( new Plugin_Upgrader_Skin(
		array(
			'plugin' => $plugin_slug,
			'title' => __( 'Update Announcement Bar', 'announcement-bar' )
		)
	));
	$response_cookies = ( isset( $response ) && isset( $response['cookies'] ) ) ? $response['cookies'] : '';
	$upgrader->upgrade( $plugin_slug, $url, $response_cookies );

	//if we got this far, everything went ok!	
	die();
}

/**
 * Validate login credentials against Themify's membership system
 */
function announcement_bar_validate_login(){
	//check_ajax_referer( 'ajax-nonce', 'nonce' );
	$response = wp_remote_post(
		'http://themify.me/files/themify-login.php',
		array(
			'timeout' => 300,
			'headers' => array(),
			'body' => array(
				'amember_login' => $_POST['username'],
				'amember_pass'  => $_POST['password']
			)
	    )
	);

	//Was there some error connecting to the server?
	if( is_wp_error( $response ) ) {
		echo 'Error ' . $response->get_error_code() . ': ' . $response->get_error_message( $response->get_error_code() );
		die();
	}

	//Connection to server was successful. Test login cookie
	$amember_nr = false;
	foreach($response['cookies'] as $cookie){
		if($cookie->name == 'amember_nr'){
			$amember_nr = true;
		}
	}
	if(!$amember_nr){
		echo 'invalid';
		die();
	}

	$subs = json_decode($response['body'], true);
	$sub_match = 'unsuscribed';
	$plugin_data = get_plugin_data( ANNOUNCEMENT_BAR_PLUGIN_FILE );
	$nicename = $plugin_data['Name'];

	foreach ($subs as $key => $value) {
		if(stripos($value['title'], $nicename) !== false){
			$sub_match = 'subscribed';
			break;
		}
		if(stripos($value['title'], 'Master Club') !== false){
			$sub_match = 'subscribed';
			break;
		}
	}
	echo $sub_match;
	die();
}

//Executes themify_updater function using wp_ajax_ action hook
add_action('wp_ajax_announcement_bar_validate_login', 'announcement_bar_validate_login');

add_filter( 'update_plugin_complete_actions', 'announcement_bar_upgrade_complete', 10, 2 );
function announcement_bar_upgrade_complete($update_actions, $plugin) {
	if ( $plugin == 'announcement-bar/announcement-bar.php' ) {
		$update_actions['themify_complete'] = '<a href="' . self_admin_url('admin.php?page=announcement-bar') . '" title="' . __('Return to Announcement Bar Settings', 'announcement-bar') . '" target="_parent">' . __('Return to Announcement Bar Settings', 'announcement-bar') . '</a>';
	}
	return $update_actions;
}
?>