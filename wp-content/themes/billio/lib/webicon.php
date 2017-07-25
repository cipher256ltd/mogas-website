<?php
defined('ABSPATH') or die();
/* 
 * Plugin Name: Detheme Icon Font Installer
 * Plugin URI: http://detheme.com
 * Description: Add icon font for detheme theme
 * Version: 1.0.1
 * Author: support@detheme.com
 * Author URI: http://detheme.com
 * Domain Path: /languages/
 *
 */

class detheme_icon{

	public static $install=false;

	function __construct(){

		$tpl=wp_get_theme();

		if(!preg_match("/(detheme.com)/i", $tpl->get('AuthorURI'))) 
			return self::$install=false;

		$version=$tpl->get('Version');
		self::$install=true;

	}

	function init(){

		if(! self::$install)
			return true;

		add_action('init', array($this,'load_icon_setting'));
		add_action('wp_print_scripts', array($this,'load_front_css_style' ),999);
		add_filter('detheme_font_list',array($this,'get_icofont_list'));
		add_action('admin_enqueue_scripts',array($this,'load_front_css_style'),999);
   	}

   	function load_icon_setting(){

   		add_filter('redux-sections',array($this,'add_detheme_icon_section'));

   	}

   	function load_front_css_style(){

   		global $detheme_config;
   		$icontypes=detheme_icon::get_packages();
   		$ssl= is_ssl_mode();
   		if(count($icontypes)){

   			print "<style type=\"text/css\">\n";

   			foreach ($icontypes as $name => $icontype) {

   				if( isset($detheme_config["detheme-icon-".$name]) && $detheme_config["detheme-icon-".$name]=='1' && ''!=$icontype['css'])
					print "@import url(". trailingslashit(untrailingslashit(maybe_ssl_url($icontype['uri']))).$icontype['css'] . ');'."\n";
   			}
   			print "</style>\n";
   		}
   	}

   	function get_icofont_list($icons){

   		global $detheme_config;

   		$icontypes=detheme_icon::get_packages();
   		if(count($icontypes)){
   			$icons=array();
   			foreach ($icontypes as $name => $icontype) {
   				if( isset($detheme_config["detheme-icon-".$name]) && $detheme_config["detheme-icon-".$name]=='1' && ''!=$icontype['css'] && $newicons=dt_exctract_DTicon($icontype['path']."/".$icontype['css'])){
				    $icons=array_merge($icons,$newicons);
   				}
   			}
   		}
   		return $icons;
   	}

    static function get_packages(){

    	global $pagenow;

    	$icon_types = get_site_transient( 'detheme_icon_types' );

    	if(!$icon_types || ! is_object($icon_types)){

    		$icon_types = new stdClass;
    		$icon_types->types = array();
    		$icon_types->last_checked = time();

    	}
    	else{

    		if(!(is_admin() && $pagenow=='themes.php')) 
    			return $icon_types->types;
    	}

    	$path= get_template_directory()."/iconfonts/";
    	$packages=array();
		$wp_filesystem=new WP_Filesystem_Direct(array());

		if($dirlist=$wp_filesystem->dirlist($path)){
		    foreach ($dirlist as $dirname => $dirattr) {

		       if($dirattr['type']!='d')
		       		continue;

		       	if(!$dirfont=$wp_filesystem->dirlist($path.$dirname."/"))
		       		continue;


		       	foreach ($dirfont as $filename => $fileattr) {
	              if(preg_match("/(\.css)$/", $filename)){
	              	$package['css']=$filename;
	                break;
	              }
	             
	            }

	       		$package['path']=$path.$dirname."/";
	       		$package['name']=$dirname;
	       		$package['title']=ucwords(str_replace('-',' ', $dirname));
	       		$package['uri']=trailingslashit(get_template_directory_uri()."/iconfonts/".$dirname);
	       		$packages[$dirname]=$package;
		    }
		  }

		$packages=apply_filters('detheme_get_icon_types',$packages);

		$icon_types->types=$packages;
		set_site_transient( 'detheme_icon_types', $icon_types );

		return $packages;

    }


	function add_detheme_icon_section($sections){

		global $pagenow;


		if(!(is_admin() && $pagenow=='themes.php')) return $sections;


		$fields=$icon=$icons=array();

		$icontypes=detheme_icon::get_packages();

		$wp_filesystem=new WP_Filesystem_Direct(array());

		if(!count($icontypes)) return $sections;

		foreach ($icontypes as $icontype) {

			if($icontype['path']=='' || $icontype['css']=='')
				continue;

                if($icon=dt_exctract_DTicon($icontype['path']."/".$icontype['css'])){
	                  $icons=@array_merge($icon,$icons);
	                  wp_enqueue_style( $icontype['name'], $icontype['uri'].$icontype['css']);
                }

	            if(!count($icon))
	            	continue;


				$description="<ul class=\"iconlist\">";

	            foreach ($icon as $n => $ico) {
	            	$description.="<li><i class=\"".$ico."\"></i><span class=\"\">.".$ico."</span></li>";

	            }

	            $description.="</ul>";

		       	array_push($fields,
				array(
					'id'=>"detheme-icon-".$icontype['name'],
					'type' => 'switch', 
					'title' => $icontype['title'],
					"default"=> 1,
					'on' => __('On', 'redux-framework'),
					'off' => __('Off', 'redux-framework')
					)
				);

		       	array_push($fields,
				array(
					'id'=>$icontype['name']."-detheme-layout",
					'type' => 'raw', 
					'content'=> $description,
					)
				);

		}

		$sections['icon-font'] = array(
			'icon' => 'el-icon-magic',
			'title' => __('Icon Settings', 'redux-framework'),
			'fields' => $fields
		);

		wp_enqueue_script( 'detheme-icon-panel',get_template_directory_uri().'/lib/js/iconpanel.js',array('jquery'));
		return $sections;
		
	}


}

$detheme_icon = new detheme_icon();
$detheme_icon->init();

if(!function_exists('dt_exctract_DTicon')){

  function dt_exctract_DTicon($file="",$pref=""){

    $wp_filesystem=new WP_Filesystem_Direct(array());

    if(!$wp_filesystem->is_file($file) || !$wp_filesystem->exists($file))
        return false;

     if ($buffers=$wp_filesystem->get_contents_array($file)) {
       $icons=array();

      foreach ($buffers as $line => $buffer) {

        if(preg_match("/^(\.".$pref.")([^:\]\"].*?):before/i",$buffer,$out)){

          if($out[2]!==""){
              $icons[$pref.$out[2]]=$pref.$out[2];
          }
        }
      }
      return $icons;

    }else{

      return false;
    }
  }
}
?>