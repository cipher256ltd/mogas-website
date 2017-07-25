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

$headerType=$detheme_config['dt-header-type'];

$logo_width_style = "";
switch ($headerType) {
    case 'center':
    	$classmenu = 'dt-menu-center';
        break;
    case 'right' :
        $classmenu = 'dt-menu-left';
        break;
    case 'leftbar' :
        $classmenu = 'dt-menu-leftbar';
        break;
    case 'pagebar' :
        $classmenu = 'dt-menu-pagebar';
        $logo_width_style = (($detheme_config['logo-width']) ? " width:".(int)$detheme_config['logo-width']."px;":"");
        break;
    default:
        $classmenu = 'dt-menu-right';
}


  $logo = $detheme_config['dt-logo-image']['url'];
  $logo_transparent = $detheme_config['dt-logo-image-transparent']['url'];


  $logoContent=$searchContent=$shoppingCart="";

  if(!empty($logo)){
    $alt_image = "";
    if (isset($detheme_config['dt-logo-image']['id'])) {
      $alt_image = get_post_meta($detheme_config['dt-logo-image']['id'], '_wp_attachment_image_alt', true);
    }

    $mobilealt_image = "";
    if (isset($detheme_config['dt-logo-image-transparent']['id'])) {
      $mobilealt_image = get_post_meta($detheme_config['dt-logo-image-transparent']['id'], '_wp_attachment_image_alt', true);
    }

     $logoContent='<a href="'.home_url().'" style="'.$logo_width_style.'"><img id="logomenu" src="'.esc_url(maybe_ssl_url($logo)).'" alt="'.esc_attr($alt_image).'" class="img-responsive halfsize" '.(($detheme_config['logo-width'])?" width=\"".(int)$detheme_config['logo-width']."\"":"").'><img id="logomenureveal" src="'.esc_url(maybe_ssl_url($logo_transparent)).'" alt="'.esc_attr($mobilealt_image).'" class="img-responsive halfsize" '.(($detheme_config['logo-width'])?" width=\"".(int)$detheme_config['logo-width']."\"":"").'></a>';
   } else{
     $logoContent=(!empty($detheme_config['dt-logo-text']))?'<div class="header-logo><a class="navbar-brand-desktop" href="'.home_url().'">'.$detheme_config['dt-logo-text'].'</a></div>':"";
   }

   if($logoContent!=''){
        $logoContent = '<li class="logo-desktop"'.(($detheme_config['logo-width'])?" style=\"width:".(int)$detheme_config['logo-width']."px;\"":"").'>'.$logoContent.'</li>';
   }


    if($detheme_config['show-header-searchmenu']):
        $searchContent= '<li class="menu-item menu-item-type-search"><form class="searchform" id="menusearchform" method="get" action="' . home_url( '/' ) . '" role="search">
                <a class="search_btn"><i class="icon-search-6"></i></a>
                <div class="popup_form"><input type="text" class="form-control" id="sm" name="s" placeholder="'.__('Search','billio').'"></div>
              </form></li>';
    endif;

  if($detheme_config['show-header-shoppingcart'] && is_plugin_active('woocommerce/woocommerce.php')):
          if ( function_exists('WC') && sizeof( WC()->cart->get_cart() ) > 0 ) :
            $shoppingCart= '<li id="menu-item-999999" class="hidden-mobile bag menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-999999">
                      <a href="#">
                        <span><i class="icon-cart"></i> <span class="item_count">'. WC()->cart->get_cart_contents_count() . '</span></span>
                      </a>
                      <label for="fof999999" class="toggle-sub" onclick="">'.(is_rtl()?'&lsaquo;':'&rsaquo;').'</label>
                      <input id="fof999999" class="sub-nav-check" type="checkbox">
                      <ul id="fof-sub-999999" class="sub-nav">
                        <li class="sub-heading">'.__('Shopping Cart','billio').' <label for="fof999999" class="toggle" onclick="" title="'.esc_attr(__('Back','billio')).'">'.(is_rtl()?__('Back','detheme').' &rsaquo;':'&lsaquo; '.__('Back','detheme')).'</label></li>
                        <li>
                          <!-- popup -->
                          <div class="cart-popup"><div class="widget_shopping_cart_content"></div></div>  
                          <!-- end popup -->
                        </li>
                      </ul>

                    </li>';
          else:
              $shoppingCart= '<li id="menu-item-999999" class="hidden-mobile bag menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-999999">
                        <a href="#">
                          <span><i class="icon-cart"></i> <span class="item_count">0</span></span>
                        </a>
    
                        <label for="fof999999" class="toggle-sub" onclick="">'.(is_rtl()?'&lsaquo;':'&rsaquo;').'</label>
                        <input id="fof999999" class="sub-nav-check" type="checkbox">
                        <ul id="fof-sub-999999" class="sub-nav">
                          <li class="sub-heading">'.__('Shopping Cart','billio').' <label for="fof999999" class="toggle" onclick="" title="'.__('Back','billio').'">'.(is_rtl()?__('Back','detheme').' &rsaquo;':'&lsaquo; '.__('Back','detheme')).'</label></li>
                          <li>
                            <!-- popup -->
                            <div class="cart-popup"><div class="widget_shopping_cart_content"></div></div>  
                            <!-- end popup -->
                          </li>
                        </ul>

                      </li>';
          endif; 
    endif; 

$menuParams=array(
            	'theme_location' => 'primary',
            	'echo' => false,
            	'container_class'=>$classmenu,
            	'container_id'=>'dt-menu',
            	'menu_class'=>'',
            	'container'=>'div',
				      'before' => '',
            	'after' => '',
              'items_wrap' => '<label for="main-nav-check" class="toggle" onclick="" title="'.__('Close','billio').'"><i class="icon-cancel-1"></i></label><ul id="%1$s" class="%2$s">'.$logoContent.'%3$s'.$searchContent.$shoppingCart.'</ul><label class="toggle close-all" onclick="uncheckboxes(&#39;nav&#39;)"><i class="icon-cancel-1"></i></label>',
            	'fallback_cb'=>false,
            	'walker'  => new billio_mainmenu_walker()
				);

$menu=wp_nav_menu($menuParams);

global $detheme_config;

$logo = $detheme_config['dt-logo-image']['url'];
$logo_transparent = $detheme_config['dt-logo-image-transparent']['url'];

$logoContent="";

if(!empty($logo)){
  $alt_image = "";
  if (isset($detheme_config['dt-logo-image']['id'])) {
    $alt_image = get_post_meta($detheme_config['dt-logo-image']['id'], '_wp_attachment_image_alt', true);  
  }
  
  $mobilealt_image = "";
  if (isset($detheme_config['dt-logo-image-transparent']['id'])) {
    $mobilealt_image = get_post_meta($detheme_config['dt-logo-image-transparent']['id'], '_wp_attachment_image_alt', true);  
  }
  
  $logoContent='<a href="'.home_url().'" style=""><img id="logomenumobile" src="'.esc_url(maybe_ssl_url($logo)).'" rel="'.esc_url(maybe_ssl_url($logo_transparent)).'" alt="'.esc_attr($alt_image).'" class="img-responsive halfsize" '.(($detheme_config['logo-width'])?" width=\"".(int)$detheme_config['logo-width']."\"":"").'><img id="logomenurevealmobile" src="'.esc_url(maybe_ssl_url($logo_transparent)).'" alt="'.esc_attr($mobilealt_image).'" class="img-responsive halfsize" '.(($detheme_config['logo-width'])?" width=\"".(int)$detheme_config['logo-width']."\"":"").'></a>';
} else{
  $logoContent=(!empty($detheme_config['dt-logo-text']))?'<a class="navbar-brand-desktop" href="'.home_url().'">'.$detheme_config['dt-logo-text'].'</a>':"";
}

$sticky_menu = "";
if (isset($detheme_config['dt-sticky-menu']) && $detheme_config['dt-sticky-menu']) {
    $sticky_menu = "alt reveal";
}

$hasTopBar = "notopbar";
if (isset($detheme_config['showtopbar']) && $detheme_config['showtopbar']) {
    $hasTopBar = "hastopbar";
}

if(is_front_page() || is_detheme_home(get_post())){
    $backgroundType = ($detheme_config['homepage-background-type']) ? "solid" : "transparent";
    $backgroundTypeSticky = ($detheme_config['homepage-header-color-transparent-active']) ? "stickysolid" : "stickytransparent";
} else {
    $backgroundType = ($detheme_config['header-background-type']) ? "solid" : "transparent";
    $backgroundTypeSticky = ($detheme_config['header-background-transparent-active']) ? "stickysolid" : "stickytransparent";
}



?>

<div id="head-page" class="head-page<?php  print is_admin_bar_showing()?" adminbar-is-here":" adminbar-not-here";?> <?php print esc_attr($sticky_menu); ?> <?php print sanitize_html_class($hasTopBar); ?> <?php print sanitize_html_class($backgroundType); ?> <?php print sanitize_html_class($backgroundTypeSticky); ?> menu_background_color">
	<div class="container">
		<?php print ($menu)?$menu:"";?>
	</div>

	<div class="container">
		<div class="row">
			<div>
                <div id="mobile-header" class="hidden-sm-max col-sm-12">
                    <label for="main-nav-check" class="toggle" onclick="" title="Menu"><i class="icon-menu"></i></label>
                    <?php echo $logoContent ?>
				</div><!-- closing "#header" -->
			</div>
		</div>
	</div>
</div>