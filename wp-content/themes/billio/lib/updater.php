<?php
defined('ABSPATH') or die();

if(!function_exists('wp_get_installed_translations')){
/* wp below 3.7 */
	function wp_get_installed_translations( $type ) {
		if ( $type !== 'themes' && $type !== 'plugins' && $type !== 'core' )
			return array();

		$dir = 'core' === $type ? '' : "/$type";

		if ( ! is_dir( WP_LANG_DIR ) )
			return array();

		if ( $dir && ! is_dir( WP_LANG_DIR . $dir ) )
			return array();

		$files = scandir( WP_LANG_DIR . $dir );
		if ( ! $files )
			return array();

		$language_data = array();

		foreach ( $files as $file ) {
			if ( '.' === $file[0] || is_dir( $file ) )
				continue;
			if ( substr( $file, -3 ) !== '.po' )
				continue;
			if ( ! preg_match( '/(?:(.+)-)?([A-Za-z_]{2,6}).po/', $file, $match ) )
				continue;

			list( , $textdomain, $language ) = $match;
			if ( '' === $textdomain )
				$textdomain = 'default';
			$language_data[ $textdomain ][ $language ] = wp_get_pomo_file_data( WP_LANG_DIR . "$dir/$file" );
		}
		return $language_data;
	}

}

function _maybe_detheme_update(){
	$current = get_site_transient( 'detheme_update_themes' );

	if ( isset( $current->last_checked ) && 24 * HOUR_IN_SECONDS > ( time() - $current->last_checked ) )
		return;

	check_for_detheme_update();
}

function check_for_detheme_update(){
	include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version

	if ( defined( 'WP_INSTALLING' ) )
		return false;

	$last_update = get_site_transient( 'detheme_update_themes' );
	if ( ! is_object($last_update) )
		$last_update = new stdClass;
	$installed_themes = wp_get_themes();
	$translations = wp_get_installed_translations( 'themes' );

	$themes = $checked = $request = array();
	$request['active'] = get_option( 'stylesheet' );

	foreach ( $installed_themes as $theme ) {

		if(preg_match("/(detheme.com)/i", $theme->get('AuthorURI')) && ''!=get_option("detheme_license_".$theme->get_template())) {

			$themes[ $theme->get_stylesheet() ] = array(
				'Name'       => $theme->get('Name'),
				'Title'      => $theme->get('Name'),
				'Version'    => $theme->get('Version'),
				'Author'     => $theme->get('Author'),
				'Author URI' => $theme->get('AuthorURI'),
				'Template'   => $theme->get_template(),
				'Stylesheet' => $theme->get_stylesheet(),
				'License'    => get_option("detheme_license_".$theme->get_template()),
			);
		}
	}

	// Check for update on a different schedule, depending on the page.
	switch ( current_filter() ) {
		case 'upgrader_process_complete' :
			$timeout = 0;
			break;
		case 'load-update-core.php' :
			$timeout = MINUTE_IN_SECONDS;
			break;
		case 'load-themes.php' :
		case 'load-update.php' :
			$timeout = HOUR_IN_SECONDS;
			break;
		default :
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				$timeout = 0;
			} else {
				$timeout = 12 * HOUR_IN_SECONDS;
			}
	}

	$time_not_changed = isset( $last_update->last_checked ) && $timeout > ( time() - $last_update->last_checked );

	if($time_not_changed)
		return false;

	$request['themes'] = $themes;

	$locales = array( get_locale() );

	/**
	 * Filter the locales requested for theme translations.
	 *
	 * @since 3.7.0
	 *
	 * @param array $locales Theme locale. Default is current locale of the site.
	 */
	$locales = apply_filters( 'themes_update_check_locales', $locales );

	$options = array(
		'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3),
		'body' => array(
			'themes'       => json_encode( $request ),
			'translations' => json_encode( $translations ),
			'locale'       => json_encode( $locales ),
		),
		'user-agent'	=> 'WordPress/' . $wp_version . '; '. home_url()
	);


	$url = $http_url = 'http://repo.detheme.com/template/';
	if ( $ssl = wp_http_supports( array( 'ssl' ) ) )
		$url = set_url_scheme( $url, 'https' );


	$raw_response = wp_remote_post( $url, $options );

	if ( $ssl && is_wp_error( $raw_response ) ) {
		$raw_response = wp_remote_post( $http_url, $options );
	}

	if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) )
		return false;

	$new_update = new stdClass;
	$new_update->last_checked = time();

	$response = json_decode( wp_remote_retrieve_body( $raw_response ), true );

	if ( is_array( $response ) ) {
		$new_update->response     = $response['themes'];
		$new_update->translations = '';
	}

	set_site_transient( 'detheme_update_themes', $new_update );
}

function add_site_transient_update_themes($value){
	$detheme_update = get_site_transient( 'detheme_update_themes' );
	if ( ! is_object($detheme_update) || !isset($value->response) || !isset($detheme_update->response) )
			return $value;
	$detheme_update_themes=$detheme_update->response;

	$value->response=@array_merge($value->response,$detheme_update_themes);

	return $value;
}

add_filter( 'pre_set_site_transient_update_themes','add_site_transient_update_themes');
add_action( 'admin_init', '_maybe_detheme_update' );
add_action( 'load-themes.php', 'check_for_detheme_update' );
add_action( 'load-update.php', 'check_for_detheme_update' );
add_action( 'load-update-core.php', 'check_for_detheme_update' );
add_action( 'wp_update_themes', 'check_for_detheme_update' );


function detheme_update_complete($theme,$hook_extra){

	if('theme'==$hook_extra['type'] && 'update'==$hook_extra['action']){

		$themeplate=$theme->skin->theme;
		$detheme_update = get_site_transient( 'detheme_update_themes' );
		$theme_update = get_site_transient( 'update_themes' );

		if ( ! is_object($theme_update) )
			$theme_update = new stdClass;

		$theme_update->response = array();
		$theme_update->translations = array();
		$theme_update->last_checked = time();


		if(isset($detheme_update->response[$themeplate])){

			unset($detheme_update->response[$themeplate]);
			set_site_transient( 'detheme_update_themes',$detheme_update );
		}

		set_site_transient( 'update_themes', $theme_update );
	}

	if('plugin'==$hook_extra['type'] && 'update'==$hook_extra['action']){


		$themeplate=$theme->skin->plugin;
		$detheme_update = get_site_transient( 'detheme_update_plugins' );

		$plugin_update = get_site_transient( 'update_plugins' );

		if ( ! is_object($plugin_update) )
			$plugin_update = new stdClass;

		$plugin_update->response = array();
		$plugin_update->translations = array();
		$plugin_update->last_checked = time();


		if(isset($detheme_update->response[$themeplate])){

			unset($detheme_update->response[$themeplate]);
			set_site_transient( 'detheme_update_plugins',$detheme_update );
		}

		set_site_transient( 'update_plugins',$plugin_update );
	}

}

add_action('upgrader_process_complete','detheme_update_complete',1,2);

function _maybe_detheme_plugins_update(){

	$current = get_site_transient( 'detheme_update_plugins' );

	if ( isset( $current->last_checked ) && 24 * HOUR_IN_SECONDS > ( time() - $current->last_checked ) )
		return;
	check_for_detheme_plugins_update();

}

function check_for_detheme_plugins_update(){

	include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version

	if ( defined( 'WP_INSTALLING' ) )
		return false;

	$tm=wp_get_theme();

	$theme_name = $tm->get('Name'); 
	$theme_template=$tm->get_template();

	$sn=get_option("detheme_license_".$tm->get_template());
	if(empty($sn))
		return false;

	// If running blog-side, bail unless we've not checked in the last 12 hours
	if ( !function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$plugins = get_plugins();
	$translations = wp_get_installed_translations( 'plugins' );

	$active  = get_option( 'active_plugins', array() );
	$current = get_site_transient( 'detheme_update_plugins' );
	if ( ! is_object($current) )
		$current = new stdClass;

	$new_option = new stdClass;
	$new_option->last_checked = time();

	// Check for update on a different schedule, depending on the page.
	switch ( current_filter() ) {
		case 'upgrader_process_complete' :
			$timeout = 0;
			break;
		case 'load-update-core.php' :
			$timeout = MINUTE_IN_SECONDS;
			break;
		case 'load-plugins.php' :
		case 'load-update.php' :
			$timeout = HOUR_IN_SECONDS;
			break;
		default :
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				$timeout = 0;
			} else {
				$timeout = 12 * HOUR_IN_SECONDS;
			}
	}

	$time_not_changed = isset( $current->last_checked ) && $timeout > ( time() - $current->last_checked );
	if($time_not_changed)
		return false;

	$updates=array();

	foreach ( $plugins as $file => $p ) {

			if( preg_match("/(detheme.com|wpbakery.com|themepunch.com)/i", $p['AuthorURI'])) {
			$updates[$file]=$p;
			}
	}

	$updates=apply_filters('get_updated_plugins',$updates);

	if ( !count($updates ) ) {

		$new_option->response = array();
		$new_option->translations = array();
		set_site_transient( 'detheme_update_plugins', $new_option );
		return false;
	}

	$current->last_checked = time();
	set_site_transient( 'detheme_update_plugins', $current );

	$locales = array( get_locale() );
	/**
	 * Filter the locales requested for plugin translations.
	 *
	 * @since 3.7.0
	 *
	 * @param array $locales Plugin locale. Default is current locale of the site.
	 */
	$locales = apply_filters( 'plugins_update_check_locales', $locales );

	$options = array(
		'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3),
		'body' => array(
			'plugins'      => json_encode( $updates ),
			'translations' => json_encode( $translations ),
			'locale'       => json_encode( $locales ),
			'license'      => $sn,
		),
		'user-agent' => 'WordPress/' . $wp_version . '; '. home_url()
	);

	$url = $http_url = 'http://repo.detheme.com/plugin/';
	if ( $ssl = wp_http_supports( array( 'ssl' ) ) )
		$url = set_url_scheme( $url, 'https' );


	$raw_response = wp_remote_post( $url, $options );

	if ( $ssl && is_wp_error( $raw_response ) ) {
		$raw_response = wp_remote_post( $http_url, $options );
	}

	if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) )
		return false;

	$response = json_decode( wp_remote_retrieve_body( $raw_response ), true );

	if ( is_array( $response ) ) {
		foreach ( $response['plugins'] as &$plugin ) {
			$plugin = (object) $plugin;
		}
		unset( $plugin );

		$new_option->response = $response['plugins'];
		$new_option->translations = '';
	} else {
		$new_option->response = array();
		$new_option->translations = array();
	}


	set_site_transient( 'detheme_update_plugins', $new_option );

}

function add_site_transient_update_plugins($transient){
	$detheme_update = get_site_transient( 'detheme_update_plugins' );
	if ( ! is_object($detheme_update) || !isset($transient->response) || !isset($detheme_update->response) )
			return $transient;
	$detheme_update_themes=$detheme_update->response;
	$transient->response=@array_merge($transient->response,$detheme_update_themes);

	return $transient;
}


function billio_check_for_update($field, $value="", $existing_value){

		include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version

	    if(''==$value){
	    	return array('error'=>$field,'value'=>'');
	    }
	    else{
	    	return array('value'=>$value);
	    }
}

add_filter( 'pre_set_site_transient_update_plugins','add_site_transient_update_plugins');
add_action( 'admin_init', '_maybe_detheme_plugins_update' );
add_action( 'load-plugins.php', 'check_for_detheme_plugins_update' );
add_action( 'load-update.php', 'check_for_detheme_plugins_update' );
add_action( 'load-update-core.php', 'check_for_detheme_plugins_update' );
add_action( 'wp_update_plugins', 'check_for_detheme_plugins_update' );
add_filter( 'auto_update_theme', '__return_false' );
add_filter( 'auto_update_plugin', '__return_false' );
if(function_exists('vc_set_as_theme')){
	function js_composer_check_update($plugins){

		if($plugin = get_plugin_data(WP_PLUGIN_DIR.'/js_composer/js_composer.php')){
			$plugin['Description']="";
			$plugins['js_composer/js_composer.php']=$plugin;
		}
		return $plugins;
	}

	add_filter('get_updated_plugins','js_composer_check_update');
}

if (is_plugin_active('revslider/revslider.php')) {
	function revslider_check_update($plugins){

		if($plugin = get_plugin_data(WP_PLUGIN_DIR.'/revslider/revslider.php')){
			$plugin['Description']="";
			$plugins['revslider/revslider.php']=$plugin;
		}
		return $plugins;
	}

	add_filter('get_updated_plugins','revslider_check_update');
}

?>