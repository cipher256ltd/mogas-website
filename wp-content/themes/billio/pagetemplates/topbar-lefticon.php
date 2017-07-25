<?php
defined('ABSPATH') or die();

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Billio
 * @since Billio 1.0
 */

global $detheme_config;

$menu=$detheme_config['dt-left-top-bar-menu'];

if(!empty($menu)):

	$menuParams=array(
            	'menu'=>$menu,
            	'echo' => false,
            	'container_class'=>'left-menu',
            	'menu_class'=>'nav navbar-nav topbar-icon',
            	'container'=>'div',
                  'theme_location'=>'left-menu',
			'before' => '',
            	'after' => '',
            	'fallback_cb'=>false,
                  'walker' => new billio_iconmenu_walker()
			);

	$menu=wp_nav_menu($menuParams);

	print ($menu)?$menu:"";
?>
<?php endif;?>
