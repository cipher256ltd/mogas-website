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

$menu=$detheme_config['dt-right-top-bar-menu'];

if(!empty($menu)):
    
   	$menuParams=array(
            	'menu'=>$menu,
            	'echo' => false,
                  'container_id'=>'dt-topbar-menu-right',
            	'menu_class'=>'topbar-menu',
            	'container'=>'div',
			'before' => '',
            	'after' => '',
                  'items_wrap' => '<label for="main-nav-check" class="toggle" onclick="" title="'.__('Close','billio').'"><i class="icon-cancel-1"></i></label><ul id="%1$s" class="%2$s">%3$s</ul></ul><label class="toggle close-all" onclick="uncheckboxes(&#39;nav-top-right&#39;)"><i class="icon-cancel-1"></i></label>',
            	'fallback_cb'=>false,
                  'theme_location'=>'right-menu',
                  'walker'  => new billio_topbarmenuright_walker()
			);

      $menuParamsNoSubmenu=array(
                  'menu'=>$menu,
                  'echo' => false,
                  'container_id'=>'dt-topbar-menu-nosub',
                  'menu_class'=>'nav navbar-nav topbar-menu-nosub',
                  'container'=>'div',
                  'items_wrap' => '<label for="main-nav-check" class="toggle" onclick="" title="'.__('Close','billio').'"><i class="icon-cancel-1"></i></label><ul id="%1$s" class="%2$s">%3$s</ul></ul><label class="toggle close-all" onclick="uncheckboxes(&#39;nav-top-right&#39;)"><i class="icon-cancel-1"></i></label>',
                  'before' => '',
                  'after' => '',
                  'theme_location'=>'right-menu',
                  'fallback_cb'=>false
                  );

	$menu=wp_nav_menu($menuParams);

      $found = preg_match_all('/menu\-item\-has\-children/s', $menu, $matches);
      // if one of menu items has children
      if ($found>0) {
?>
      <div class="right-menu">
            <input type="checkbox" name="nav-top-right" id="main-nav-check-top-right">
            <?php print ($menu)?$menu:"";?>
            <div id="mobile-header-top-right" class="hidden-sm-max col-sm-12">
                  <label for="main-nav-check-top-right" class="toggle" onclick="" title="<?php echo esc_attr(__('Menu','billio'));?>"><i class="icon-menu"></i></label>
            </div><!-- closing "#header" -->
      </div>
<?php
      } else {
            $menu=wp_nav_menu($menuParamsNoSubmenu);
?>
      <div class="right-menu"><?php print ($menu)?$menu:"";?></div>
<?php
      }
	
?>

<?php endif;?>
