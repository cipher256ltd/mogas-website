<?php
defined('ABSPATH') or die();
/**
 * @package WordPress
 * @subpackage Billio
 * @since Billio 1.0
 */

if(!function_exists('get_the_permalink')){

  function get_the_permalink( $id = 0, $leavename = false ) {
    return get_permalink( $id, $leavename );
  }
}


function get_billio_sidebar_position(){

  global $detheme_config;

  if(function_exists('is_shop') && is_shop()){

   $post_id=get_option( 'woocommerce_shop_page_id');
  }
  elseif(is_home()){
    $post_id=get_option( 'page_for_posts');
  }
  elseif (is_page()){
    $post_id= get_the_ID();
  }


  $sidebar_position = isset($post_id) ?get_post_meta( $post_id, '_sidebar_position', true ):'default';

  if(!isset($sidebar_position) || empty($sidebar_position) || $sidebar_position=='default'){

    switch ($detheme_config['layout']) {
      case 1:
        $sidebar_position = "nosidebar";
        break;
      case 2:
        $sidebar_position = "sidebar-left";
        break;
      case 3:
        $sidebar_position = "sidebar-right";
        break;
      default:
        $sidebar_position = "sidebar-left";
    }


  }

  return $sidebar_position;
}


add_filter('nav_menu_link_attributes','billio_formatMenuAttibute',2,2);

/* page attribute */
add_action( 'save_post', 'billio_save_sidebar_metaboxes' );

function billio_save_sidebar_metaboxes($post_id){

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    if(!wp_verify_nonce( isset($_POST['detheme_page_metaboxes'])?$_POST['detheme_page_metaboxes']:"", 'detheme_page_metaboxes'))
    return;

     $old = get_post_meta( $post_id, '_sidebar_position', true );
     $new = (isset($_POST['_sidebar_position']))?$_POST['_sidebar_position']:'';
     
     update_post_meta( $post_id, '_sidebar_position', $new,$old );

     $old = get_post_meta( $post_id, '_hide_title', true );
     $new = (isset($_POST['hide_title']))?$_POST['hide_title']:'';

     update_post_meta( $post_id, '_hide_title', $new,$old );

     $old = get_post_meta( $post_id, '_hide_loader', true );
     $new = (isset($_POST['hide_loader']))?$_POST['hide_loader']:'';

     update_post_meta( $post_id, '_hide_loader', $new,$old );

     $old = get_post_meta( $post_id, '_hide_banner', true );
     $new = (isset($_POST['hide_banner']))?sanitize_text_field($_POST['hide_banner']):'';

     update_post_meta( $post_id, '_hide_banner', $new,$old );


     if('page'==get_post_type()){

       $old = get_post_meta( $post_id, '_background_style', true );
       $new = (isset($_POST['background_style']))?$_POST['background_style']:'';

       update_post_meta( $post_id, '_background_style', $new,$old );

       $old = get_post_meta( $post_id, '_page_background', true );
       $new = (isset($_POST['page_background']))?$_POST['page_background']:'';

       update_post_meta( $post_id, '_page_background', $new,$old );

       if(isset($_POST['page_banner'])){

         $old = get_post_meta( $post_id, '_page_banner', true );
         $new = sanitize_text_field($_POST['page_banner']);
         update_post_meta( $post_id, '_page_banner', $new,$old );
       }    


    }
}

function billio_dtmedia_script_loader($hook){

  wp_register_script('detheme-media',get_template_directory_uri() . '/lib/js/media.min.js', array('jquery','media-views','media-editor'),'',true);
  wp_enqueue_script('detheme-media');

  wp_localize_script( 'detheme-media', 'dtb_i18nLocale', array(
      'select_image'=>__('Select Image','billio'),
      'insert_image'=>__('Insert Image','billio'),
  ));
}

add_action( 'dbx_post_advanced' , 'billio_dtmedia_script_loader' );

function billio_dtmenu_metaboxes() {

  remove_meta_box('pageparentdiv', 'page','side');
  add_meta_box('dtpageparentdiv',  __('Page Attributes','billio'), 'billio_page_attributes_meta_box', 'page', 'side', 'core');
}

function biliio_page_attibutes_metabox($posttypes){
  return array('page'=>$posttypes['page']);
}

add_filter('dt_page_metaboxes','biliio_page_attibutes_metabox');

function billio_formatMenuAttibute($atts, $item){

  global $dropdownmenu;

  if(in_array('dropdown', $item->classes)){
    $atts['class']="dropdown-toggle";
    $atts['data-toggle']="dropdown";
    $dropdownmenu=$item;
  }
  return $atts;
}

function billio_createFontelloIconMenu($css,$item,$args=array()){

  $css=@implode(" ",$css);
  $args->link_before="";
  $args->link_after="";
  
  if(preg_match('/([-_a-z-0-9]{0,})icon([-_a-z-0-9]{0,})/', $css, $matches)){
  
    $css=preg_replace('/'.$matches[0].'/', "", $css);
    $item->title="<i class=\"".$matches[0]."\"></i>";
  }
  return @explode(" ",$css);
}


function billio_createFontelloMenu($css,$item,$args=array()){

  $css=@implode(" ",$css);
  $args->link_before="";
  $args->link_after="";
  
  if(preg_match('/([-_a-z-0-9]{0,})icon([-_a-z-0-9]{0,})/', $css, $matches)){
  
    $css=preg_replace('/'.$matches[0].'/', "", $css);
    $args->link_before.="<i class=\"".$matches[0]."\"></i>";
  }

  $args->link_before.="<span>";
  $args->link_after="</span>";

  return @explode(" ",$css);
}

add_filter( 'nav_menu_css_class', 'billio_createFontelloMenu', 10, 3 );
add_filter( 'nav_menu_icon_css_class', 'billio_createFontelloIconMenu', 10, 3 );


class billio_iconmenu_walker extends Walker_Nav_Menu {
  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    $class_names = $value = '';

    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    $classes[] = 'menu-item-' . $item->ID;

    /**
     * Filter the CSS class(es) applied to a menu item's <li>.
     *
     * @since 3.0.0
     *
     * @param array  $classes The CSS classes that are applied to the menu item's <li>.
     * @param object $item    The current menu item.
     * @param array  $args    An array of arguments. @see wp_nav_menu()
     */
    $class_names = join( ' ', apply_filters('nav_menu_icon_css_class',array_filter( $classes ), $item, $args));
    $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';


    /**
     * Filter the ID applied to a menu item's <li>.
     *
     * @since 3.0.1
     *
     * @param string The ID that is applied to the menu item's <li>.
     * @param object $item The current menu item.
     * @param array $args An array of arguments. @see wp_nav_menu()
     */
    $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
    $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

    $output .= $indent . '<li' . $id . $value . $class_names .'>';

    $atts = array();
    $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
    $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
    $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
    $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

    /**
     * Filter the HTML attributes applied to a menu item's <a>.
     *
     * @since 3.6.0
     *
     * @param array $atts {
     *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
     *
     *     @type string $title  The title attribute.
     *     @type string $target The target attribute.
     *     @type string $rel    The rel attribute.
     *     @type string $href   The href attribute.
     * }
     * @param object $item The current menu item.
     * @param array  $args An array of arguments. @see wp_nav_menu()
     */
    $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

    $attributes = '';
    foreach ( $atts as $attr => $value ) {
      if ( ! empty( $value ) ) {
        $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
        $attributes .= ' ' . $attr . '="' . $value . '"';
      }
    }

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
    $item_output .= '</a>';
    $item_output .= $args->after;

    /**
     * Filter a menu item's starting output.
     *
     * The menu item's starting output only includes $args->before, the opening <a>,
     * the menu item's title, the closing </a>, and $args->after. Currently, there is
     * no filter for modifying the opening and closing <li> for a menu item.
     *
     * @since 3.0.0
     *
     * @param string $item_output The menu item's starting HTML output.
     * @param object $item        Menu item data object.
     * @param int    $depth       Depth of menu item. Used for padding.
     * @param array  $args        An array of arguments. @see wp_nav_menu()
     */
    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }    
}

class billio_mainmenu_walker extends Walker_Nav_Menu {
  protected $megamenu_parent_ids = array();
  private $curItem;

  function start_lvl( &$output, $depth = 0, $args = array() ) {
      $tem_output = $output . 'akhir';

      $found = preg_match_all('/<li (.*)<span>(.*?)<\/span><\/a>akhir/s', $tem_output, $matches);

      $foundid = preg_match_all('/<li id="menu\-item\-(.*?)"/s', $tem_output, $ids);

      $found_full_megamenu = preg_match_all('/class="(.*)dt\-megamenu(.*?)"/s', $tem_output, $full_megamenu);

      if ($found) {
        $menu_title = $matches[count($matches)-1][0];

        if (count($ids[1])>0) {
          $menu_id = $ids[1][count($ids[1])-1];
        } else {
          $menu_id = rand (1000,9999);
        }
        $class_sub = "";

        $output .= '<label for="fof'.$menu_id.'" class="toggle-sub" onclick="">'.(is_rtl()?'&lsaquo;':'&rsaquo;').'</label>
        <input id="fof'.$menu_id.'" class="sub-nav-check" type="checkbox">
        <ul id="fof-sub-'.$menu_id.'" class="sub-nav '. $class_sub .'"><li class="sub-heading">'. $menu_title .' <label for="fof'.$menu_id.'" class="toggle" onclick="" title="'.esc_attr(__('Back','billio')).'">'.(is_rtl()?__('Back','billio').' &rsaquo;':'&lsaquo; '.__('Back','billio')).'</label></li>';

      }
  }

  function end_lvl( &$output, $depth = 0, $args = array() ) {
    if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {
      if (isset($this->curItem)) {
        if ($this->curItem->megamenuType=='megamenu-column') {
          $output .= '</div></li><!--end of <li><div class="row">-->';// end of <li><div class="row">
          $output .= '<!--end_lvl1 '.$this->curItem->ID.' '. $this->curItem->megamenuType . ' -->';
          parent::end_lvl($output,$depth,$args);
        } else {
          $output .= '<!--end_lvl2 '.$this->curItem->ID.' '. $this->curItem->megamenuType . ' -->';
          parent::end_lvl($output,$depth,$args);
        }
      } else {
        $output .= '<!--end_lvl3-->';
        parent::end_lvl($output,$depth,$args);
      }
    } else {
      $output .= '<!--end_lvl4-->';
      parent::end_lvl($output,$depth,$args);
    }
  }

  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    global $detheme_Style;

    if(is_array($args) && $args['fallback_cb']=='wp_page_menu'){

      $item->title=$item->post_title;
      $item->url=get_permalink($item->ID);
    }

    if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {

      switch($item->megamenuType) {
        case 'megamenu-column':

          $classes = implode(" ",$item->classes);

          $output .= '<div class="'.$classes.' dt-megamenu-grid">';
          $output .= '  <ul class="dt-megamenu-sub-nav">';
        break;
        case 'megamenu-heading':
          parent::start_el($output,$item,$depth,$args,$id);
        break;
        case 'megamenu-content':
          parent::start_el($output,$item,$depth,$args,$id);
        break;
        default :
          parent::start_el($output,$item,$depth,(object)$args,$id);
        break;
      }

      if (is_array($item->classes) && in_array('dt-megamenu',$item->classes)) {
        $class_sub = "megamenu-sub";
        $style_sub = "";

        if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {
          if (isset($item->megamenuWidthOptions)) {
            if ($item->megamenuWidthOptions=='dt-megamenu-width-set sticky-left') {
              if (!empty($item->megamenuWidth)) {
                $class_sub .= " dt-megamenu-custom-width-".$item->ID;
                $detheme_Style[] = ".dt-megamenu-custom-width-".$item->ID."{ width:". $item->megamenuWidth . " !important; }";
                $detheme_Style[] = "@media ( max-width:991px ) {.dt-megamenu-custom-width-".$item->ID."{ width:270px !important; }}";
              }
            } else {
              $class_sub = "megamenu-sub ". $item->megamenuWidthOptions;
            }
          }
        }


        $menu_id = $item->ID;
        $this->megamenu_parent_ids[] = $menu_id;

        $background_id = '';
        if (isset($item->megamenuBackgroundURL)) {
          $background_id = 'megamenu_bg_'.$menu_id;
          $detheme_Style[] = '#megamenu_bg_' . $menu_id . ' {background: url('.$item->megamenuBackgroundURL.') '. $item->megamenuBackgroundHorizontalPosition . ' ' . $item->megamenuBackgroundVerticalPosition . ' ' . $item->megamenuBackgroundRepeat . ';}';

          $detheme_Style[] = '@media ( max-width:990px ) { #megamenu_bg_' . $menu_id . ' {background: none;}}';
        }

        $menu_title = $item->post_title;

        $output .= '<label for="fof'.$menu_id.'" class="toggle-sub" onclick="">'.(is_rtl()?'&lsaquo;':'&rsaquo;').'</label>
        <input id="fof'.$menu_id.'" class="sub-nav-check" type="checkbox">
        <ul id="fof-sub-'.$menu_id.'" class="sub-nav '. $class_sub .'"'.$style_sub.'><li class="sub-heading">'. $menu_title .' <label for="fof'.$menu_id.'" class="toggle" onclick="" title="'.esc_attr(__('Back','billio')).'">'.(is_rtl()?__('Back','billio').' &rsaquo;':'&lsaquo; '.__('Back','billio')).'</label></li>';

        $output .= '<li><div class="row" id="'.$background_id.'">';
      }

    } else {
      parent::start_el($output,$item,$depth,(object)$args,$id);
    }
    
  }

  function end_el( &$output, $item, $depth = 0, $args = array() ) {
    $this->curItem = $item;

    if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {
      switch($item->megamenuType) {
        case 'megamenu-column':
          $output .= '</div><!--end_el megamenu-column-->';
        break;
        case 'megamenu-heading':
          parent::end_el($output,$item,$depth,$args);
        break;
        case 'megamenu-content':
          parent::end_el($output,$item,$depth,$args);
        break;
        default :
          parent::end_el($output,$item,$depth,$args);
        break;
      }
    } else {

      parent::end_el($output,$item,$depth,$args);
    }
  }

}

class billio_topbarmenuright_walker extends Walker_Nav_Menu {
  function start_lvl( &$output, $depth = 0, $args = array() ) {
      $tem_output = $output . 'akhir';

      $found = preg_match_all('/<li (.*)<span>(.*?)<\/span><\/a>akhir/s', $tem_output, $matches);

      $foundid = preg_match_all('/<li id="menu\-item\-(.*?)"/s', $tem_output, $ids);

      if ($found) {
        $menu_title = $matches[count($matches)-1][0];

        if (count($ids[1])>0) {
          $menu_id = $ids[1][count($ids[1])-1];
        } else {
          $menu_id = rand (1000,9999);
        }


        //print_r($matches[count($matches)-1] . ' aha');
        $output .= '<label for="topright'.$menu_id.'" class="toggle-sub" onclick="">&rsaquo;</label>
        <input id="topright'.$menu_id.'" class="sub-nav-check" type="checkbox">
        <ul id="topright-sub-'.$menu_id.'" class="sub-nav"><li class="sub-heading">'. $menu_title .' <label for="topright'.$menu_id.'" class="toggle" onclick="" title="'.esc_attr(__('Back','billio')).'">&lsaquo; '.__('Back','billio').'</label></li>';
      }
  }

  function end_lvl( &$output, $depth = 0, $args = array() ) {
    if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {
      if (isset($this->curItem)) {
        if ($this->curItem->megamenuType=='megamenu-column') {
          $output .= '</div></li><!--end of <li><div class="row">-->';// end of <li><div class="row">
          $output .= '<!--end_lvl1 '.$this->curItem->ID.' '. $this->curItem->megamenuType . ' -->';
          parent::end_lvl($output,$depth,$args);
        } else {
          $output .= '<!--end_lvl2 '.$this->curItem->ID.' '. $this->curItem->megamenuType . ' -->';
          parent::end_lvl($output,$depth,$args);
        }
      } else {
        $output .= '<!--end_lvl3-->';
        parent::end_lvl($output,$depth,$args);
      }
    } else {
      $output .= '<!--end_lvl4-->';
      parent::end_lvl($output,$depth,$args);
    }
  }

  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    global $detheme_Style;

    if(is_array($args) && $args['fallback_cb']=='wp_page_menu'){

      $item->title=$item->post_title;
      $item->url=get_permalink($item->ID);
    }

    if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {

      switch($item->megamenuType) {
        case 'megamenu-column':

          $classes = implode(" ",$item->classes);

          $output .= '<div class=" dt-megamenu-grid">';
          $output .= '  <ul class="dt-megamenu-sub-nav">';
        break;
        case 'megamenu-heading':
          parent::start_el($output,$item,$depth,$args,$id);
        break;
        case 'megamenu-content':
          parent::start_el($output,$item,$depth,$args,$id);
        break;
        default :
          parent::start_el($output,$item,$depth,(object)$args,$id);
        break;
      }

      if (is_array($item->classes) && in_array('dt-megamenu',$item->classes)) {
        $class_sub = "megamenu-sub";
        $style_sub = "";

        if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {
          if (isset($item->megamenuWidthOptions)) {
            if ($item->megamenuWidthOptions=='dt-megamenu-width-set sticky-left') {
              if (!empty($item->megamenuWidth)) {
                $class_sub .= " dt-megamenu-custom-width-".$item->ID;
                $detheme_Style[] = ".dt-megamenu-custom-width-".$item->ID."{ width:". $item->megamenuWidth . " !important; }";
                $detheme_Style[] = "@media ( max-width:991px ) {.dt-megamenu-custom-width-".$item->ID."{ width:270px !important; }}";
              }
            } else {
              $class_sub = "megamenu-sub ". $item->megamenuWidthOptions;
            }
          }
        }


        $menu_id = $item->ID;
        $this->megamenu_parent_ids[] = $menu_id;

        $background_id = '';
        if (isset($item->megamenuBackgroundURL)) {
          $background_id = 'megamenu_bg_'.$menu_id;
          $detheme_Style[] = '#megamenu_bg_' . $menu_id . ' {background: url('.$item->megamenuBackgroundURL.') '. $item->megamenuBackgroundHorizontalPosition . ' ' . $item->megamenuBackgroundVerticalPosition . ' ' . $item->megamenuBackgroundRepeat . ';}';

          $detheme_Style[] = '@media ( max-width:990px ) { #megamenu_bg_' . $menu_id . ' {background: none;}}';
        }

        $menu_title = $item->post_title;

        $output .= '<label for="fof-topright-'.$menu_id.'" class="toggle-sub" onclick="">'.(is_rtl()?'&lsaquo;':'&rsaquo;').'</label>
        <input id="fof-topright-'.$menu_id.'" class="sub-nav-check" type="checkbox">
        <ul id="fof-sub-'.$menu_id.'" class="sub-nav '. $class_sub .'"'.$style_sub.'><li class="sub-heading">'. $menu_title .' <label for="fof-topright-'.$menu_id.'" class="toggle" onclick="" title="'.esc_attr(__('Back','billio')).'">'.(is_rtl()?__('Back','billio').' &rsaquo;':'&lsaquo; '.__('Back','billio')).'</label></li>';

        $output .= '<li><div class="row" id="'.$background_id.'">';
      }

    } else {
      parent::start_el($output,$item,$depth,(object)$args,$id);
    }
    
  }

  function end_el( &$output, $item, $depth = 0, $args = array() ) {
    $this->curItem = $item;

    if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {
      switch($item->megamenuType) {
        case 'megamenu-column':
          $output .= '</div><!--end_el megamenu-column-->';
        break;
        case 'megamenu-heading':
          parent::end_el($output,$item,$depth,$args);
        break;
        case 'megamenu-content':
          parent::end_el($output,$item,$depth,$args);
        break;
        default :
          parent::end_el($output,$item,$depth,$args);
        break;
      }
    } else {

      parent::end_el($output,$item,$depth,$args);
    }
  }

}

class billio_topbarmenuleft_walker extends Walker_Nav_Menu {
  function start_lvl( &$output, $depth = 0, $args = array() ) {
      $tem_output = $output . 'akhir';

      $found = preg_match_all('/<li (.*)<span>(.*?)<\/span><\/a>akhir/s', $tem_output, $matches);

      $foundid = preg_match_all('/<li id="menu\-item\-(.*?)"/s', $tem_output, $ids);

      if ($found) {
        $menu_title = $matches[count($matches)-1][0];

        if (count($ids[1])>0) {
          $menu_id = $ids[1][count($ids[1])-1];
        } else {
          $menu_id = rand (1000,9999);
        }


        $output .= '<label for="topleft'.$menu_id.'" class="toggle-sub" onclick="">&rsaquo;</label>
        <input id="topleft'.$menu_id.'" class="sub-nav-check" type="checkbox">
        <ul id="topleft-sub-'.$menu_id.'" class="sub-nav"><li class="sub-heading">'. $menu_title .' <label for="topleft'.$menu_id.'" class="toggle" onclick="" title="'.esc_attr(__('Back','billio')).'">&lsaquo; '.__('Back','billio').'</label></li>';
      }
  }

  function end_lvl( &$output, $depth = 0, $args = array() ) {
    if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {
      if (isset($this->curItem)) {
        if ($this->curItem->megamenuType=='megamenu-column') {
          $output .= '</div></li><!--end of <li><div class="row">-->';// end of <li><div class="row">
          $output .= '<!--end_lvl1 '.$this->curItem->ID.' '. $this->curItem->megamenuType . ' -->';
          parent::end_lvl($output,$depth,$args);
        } else {
          $output .= '<!--end_lvl2 '.$this->curItem->ID.' '. $this->curItem->megamenuType . ' -->';
          parent::end_lvl($output,$depth,$args);
        }
      } else {
        $output .= '<!--end_lvl3-->';
        parent::end_lvl($output,$depth,$args);
      }
    } else {
      $output .= '<!--end_lvl4-->';
      parent::end_lvl($output,$depth,$args);
    }
  }

  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    global $detheme_Style;

    if(is_array($args) && $args['fallback_cb']=='wp_page_menu'){

      $item->title=$item->post_title;
      $item->url=get_permalink($item->ID);
    }

    if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {

      switch($item->megamenuType) {
        case 'megamenu-column':

          $classes = implode(" ",$item->classes);

          $output .= '<div class=" dt-megamenu-grid">';
          $output .= '  <ul class="dt-megamenu-sub-nav">';
        break;
        case 'megamenu-heading':
          parent::start_el($output,$item,$depth,$args,$id);
        break;
        case 'megamenu-content':
          parent::start_el($output,$item,$depth,$args,$id);
        break;
        default :
          parent::start_el($output,$item,$depth,(object)$args,$id);
        break;
      }

      if (is_array($item->classes) && in_array('dt-megamenu',$item->classes)) {
        $class_sub = "megamenu-sub";
        $style_sub = "";

        if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {
          if (isset($item->megamenuWidthOptions)) {
            if ($item->megamenuWidthOptions=='dt-megamenu-width-set sticky-left') {
              if (!empty($item->megamenuWidth)) {
                $class_sub .= " dt-megamenu-custom-width-".$item->ID;
                $detheme_Style[] = ".dt-megamenu-custom-width-".$item->ID."{ width:". $item->megamenuWidth . " !important; }";
                $detheme_Style[] = "@media ( max-width:991px ) {.dt-megamenu-custom-width-".$item->ID."{ width:270px !important; }}";
              }
            } else {
              $class_sub = "megamenu-sub ". $item->megamenuWidthOptions;
            }
          }
        }


        $menu_id = $item->ID;
        $this->megamenu_parent_ids[] = $menu_id;

        $background_id = '';
        if (isset($item->megamenuBackgroundURL)) {
          $background_id = 'megamenu_bg_'.$menu_id;
          $detheme_Style[] = '#megamenu_bg_' . $menu_id . ' {background: url('.$item->megamenuBackgroundURL.') '. $item->megamenuBackgroundHorizontalPosition . ' ' . $item->megamenuBackgroundVerticalPosition . ' ' . $item->megamenuBackgroundRepeat . ';}';

          $detheme_Style[] = '@media ( max-width:990px ) { #megamenu_bg_' . $menu_id . ' {background: none;}}';
        }

        $menu_title = $item->post_title;

        $output .= '<label for="fof-topleft-'.$menu_id.'" class="toggle-sub" onclick="">'.(is_rtl()?'&lsaquo;':'&rsaquo;').'</label>
        <input id="fof-topleft-'.$menu_id.'" class="sub-nav-check" type="checkbox">
        <ul id="fof-sub-'.$menu_id.'" class="sub-nav '. $class_sub .'"'.$style_sub.'><li class="sub-heading">'. $menu_title .' <label for="fof-topleft-'.$menu_id.'" class="toggle" onclick="" title="'.esc_attr(__('Back','billio')).'">'.(is_rtl()?__('Back','billio').' &rsaquo;':'&lsaquo; '.__('Back','billio')).'</label></li>';

        $output .= '<li><div class="row" id="'.$background_id.'">';
      }

    } else {
      parent::start_el($output,$item,$depth,(object)$args,$id);
    }
    
  }

  function end_el( &$output, $item, $depth = 0, $args = array() ) {
    $this->curItem = $item;

    if ( is_plugin_active('billio-megamenu/billio-megamenu.php') ) {
      switch($item->megamenuType) {
        case 'megamenu-column':
          $output .= '</div><!--end_el megamenu-column-->';
        break;
        case 'megamenu-heading':
          parent::end_el($output,$item,$depth,$args);
        break;
        case 'megamenu-content':
          parent::end_el($output,$item,$depth,$args);
        break;
        default :
          parent::end_el($output,$item,$depth,$args);
        break;
      }
    } else {

      parent::end_el($output,$item,$depth,$args);
    }
  }

}

function billio_add_class_to_first_submenu($items) {
  $menuhaschild = array();

  foreach($items as $key => $item) {

    if (in_array('menu-item-has-children',$item->classes)) {
      $menuhaschild[] = $item->ID;
    }

  }

  foreach($menuhaschild as $key => $parent_id) {
    foreach($items as $key => $item) {
      if ($item->menu_item_parent==$parent_id) {
        $item->classes[] = 'menu-item-first-child';
        break;
      }
    }
  }


  return $items;
}

add_filter('wp_nav_menu_objects', 'billio_add_class_to_first_submenu');

function billio_tag_cloud_args($args=array()){
  $args['filter']=1;
  return $args;

}

function billio_tag_cloud($return="",$tags, $args = '' ){

  if(!count($tags))
    return $return;
  $return='<ul class="list-unstyled">';
  foreach ($tags as $tag) {
    $return.='<li class="tag"><a href="'.esc_url($tag->link).'">'.ucwords($tag->name).'</a></li>';
  }
  $return.='</ul>';
  return $return;
}

function billio_widget_title($title="",$instance=array(),$id=null){

  if(empty($instance['title']))
      return "";
  return $title;
}

add_filter('widget_tag_cloud_args','billio_tag_cloud_args');
add_filter('wp_generate_tag_cloud','billio_tag_cloud',1,3);
add_filter('widget_title','billio_widget_title',1,3);

function billio_get_avatar_url($get_avatar){
    preg_match("/src='(.*?)'/i", $get_avatar, $matches);
    if (isset($matches[1])) {
      return $matches[1];
    } else {
      return;
    }
}


// Comment Functions
function billio_comment_form( $args = array(), $post_id = null ) {
  if ( null === $post_id )
    $post_id = get_the_ID();
  else
    $id = $post_id;

  $commenter = wp_get_current_commenter();
  $user = wp_get_current_user();
  $user_identity = $user->exists() ? $user->display_name : '';

  $args = wp_parse_args( $args );
  if ( ! isset( $args['format'] ) )
    $args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';

  $req      = get_option( 'require_name_email' );
  $aria_req = ( $req ? " aria-required='true'" : '' );
  $html5    = 'html5' === $args['format'];
  
  $fields   =  array(
    'author' => '<div class="row">
                    <div class="form-group col-xs-12 col-sm-4">
                      <i class="icon-user-7"></i>
                      <input type="text" class="form-control" name="author" id="author" placeholder="'.esc_attr(__('full name','billio')).'" required>
                  </div>',
    'email' => '<div class="form-group col-xs-12 col-sm-4">
                      <i class="icon-mail-7"></i>
                      <input type="email" class="form-control"  name="email" id="email" placeholder="'.esc_attr(__('email address','billio')).'" required>
                  </div>',
    'url' => '<div class="form-group col-xs-12 col-sm-4">
                  <i class="icon-globe-6"></i>
                  <input type="text" class="form-control icon-user-7" name="url" id="url" placeholder="website">
                </div>
              </div>',
  );

  $required_text = sprintf( ' ' . __('Required fields are marked %s'), '<span class="required">*</span>' );
  $defaults = array(
    'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
    'comment_field'        => '<div class="row">
                                  <div class="form-group col-xs-12">
                                    <textarea class="form-control" rows="3" name="comment" id="comment" placeholder="'.__('your message','billio').'" required></textarea>
                                  </div>
                              </div>',
    'must_log_in'          => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.'), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
    'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>'), get_edit_user_link(), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
    'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.') . ( $req ? $required_text : '' ) . '</p>',
    'comment_notes_after'  => '',
    'id_form'              => 'commentform',
    'id_submit'            => 'submit',
    'title_reply'          => '<div class="comment-leave-title">'.__('Leave a Comment').'</div>',
    'title_reply_to'       => __( 'Leave a Comment to %s'),
    'cancel_reply_link'    => __( 'Cancel reply'),
    'label_submit'         => __( 'Submit','billio' ),
    'format'               => 'html5',
  );

  $args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

  ?>
    <?php if ( comments_open( $post_id ) ) : ?>

      <?php do_action( 'comment_form_before' ); ?>
      <section id="respond" class="comment-respond">
        <h3 id="reply-title" class="comment-reply-title"><?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?> <small><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></small></h3>
        <?php if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) : ?>
          <?php echo $args['must_log_in']; ?>
          <?php do_action( 'comment_form_must_log_in_after' ); ?>
        <?php else : ?>
          <form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="<?php echo esc_attr( $args['id_form'] ); ?>" class="comment-form"<?php echo $html5 ? ' novalidate' : ''; ?> data-abide>
            <?php do_action( 'comment_form_top' ); ?>
            <?php 
              if ( is_user_logged_in() ) :
                echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );
                do_action( 'comment_form_logged_in_after', $commenter, $user_identity );
                echo $args['comment_notes_before'];
              else : 
                do_action( 'comment_form_before_fields' );
                foreach ( (array) $args['fields'] as $name => $field ) {
                  echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
                }
                do_action( 'comment_form_after_fields' );
              endif; 
            ?>
            <?php echo apply_filters( 'comment_form_field_comment', $args['comment_field'] ); ?>
            <?php echo $args['comment_notes_after']; ?>
            <p class="form-submit">
              <input name="submit" type="submit" id="<?php echo esc_attr( $args['id_submit'] ); ?>" value="<?php echo esc_attr( $args['label_submit'] ); ?>" class="btn-lg primary_color_button btn btn-ghost skin-dark" />
              <?php comment_id_fields( $post_id ); ?>
            </p>
            <?php do_action( 'comment_form', $post_id ); ?>
          </form>
        <?php endif; ?>
      </section><!-- #respond -->
      <?php do_action( 'comment_form_after' ); ?>
    <?php else : ?>
      <?php do_action( 'comment_form_comments_closed' ); ?>
    <?php endif; ?>
  <?php
}

/**
 * Retrieve HTML content for reply to comment link.
 *
 * The default arguments that can be override are 'add_below', 'respond_id',
 * 'reply_text', 'login_text', and 'depth'. The 'login_text' argument will be
 * used, if the user must log in or register first before posting a comment. The
 * 'reply_text' will be used, if they can post a reply. The 'add_below' and
 * 'respond_id' arguments are for the JavaScript moveAddCommentForm() function
 * parameters.
 *
 * @since 2.7.0
 *
 * @param array $args Optional. Override default options.
 * @param int $comment Optional. Comment being replied to.
 * @param int $post Optional. Post that the comment is going to be displayed on.
 * @return string|bool|null Link to show comment form, if successful. False, if comments are closed.
 */
function billio_get_comment_reply_link($args = array(), $comment = null, $post = null) {
  global $user_ID;

  $defaults = array('add_below' => 'comment', 'respond_id' => 'respond', 'reply_text' => __('Reply','billio'),
    'login_text' => __('Log in to Reply','billio'), 'depth' => 0, 'before' => '', 'after' => '');

  $args = wp_parse_args($args, $defaults);

  if ( 0 == $args['depth'] || $args['max_depth'] <= $args['depth'] )
    return;

  extract($args, EXTR_SKIP);

  $comment = get_comment($comment);
  if ( empty($post) )
    $post = $comment->comment_post_ID;
  $post = get_post($post);

  if ( !comments_open($post->ID) )
    return false;

  $link = '';

  if ( get_option('comment_registration') && !$user_ID )
    $link = '<a rel="nofollow" class="comment-reply-login" href="' . esc_url( wp_login_url( get_permalink() ) ) . '">' . $login_text . '</a>';
  else 
    $link = "<a class='reply comment-reply-link btn btn-ghost skin-dark' href='#' onclick='return addComment.moveForm(\"$add_below-$comment->comment_ID\", \"$comment->comment_ID\", \"$respond_id\", \"$post->ID\")'>$reply_text</a>";
  
  return apply_filters('comment_reply_link', $before . $link . $after, $args, $comment, $post);
}

/**
 * Displays the HTML content for reply to comment link.
 *
 * @since 2.7.0
 * @see billio_get_comment_reply_link() Echoes result
 *
 * @param array $args Optional. Override default options.
 * @param int $comment Optional. Comment being replied to.
 * @param int $post Optional. Post that the comment is going to be displayed on.
 * @return string|bool|null Link to show comment form, if successful. False, if comments are closed.
 */
function billio_comment_reply_link($args = array(), $comment = null, $post = null) {
  echo billio_get_comment_reply_link($args, $comment, $post);
}

/**
 * Display or retrieve edit comment link with formatting.
 *
 * @since 1.0.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @return string|null HTML content, if $echo is set to false.
 */
if ( ! function_exists( 'billio_edit_comment_link' ) ) :
  function billio_edit_comment_link( $link = null, $before = '', $after = '' ) {
    global $comment;

    if ( !current_user_can( 'edit_comment', $comment->comment_ID ) )
      return;

    if ( null === $link )
      $link = __('Edit This','billio');

    $link = '<a class="comment-edit-link primary_color_button btn btn-ghost skin-dark" href="' . get_edit_comment_link( $comment->comment_ID ) . '">' . $link . '</a>';
    echo $before . apply_filters( 'edit_comment_link', $link, $comment->comment_ID ) . $after;
  }
endif; 

if ( ! function_exists( 'billio_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own billio_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Loopa 1.0
 */
function billio_comment( $comment, $args, $depth ) {

  $GLOBALS['comment'] = $comment;
  switch ( $comment->comment_type ) :
    case 'pingback' :
    case 'trackback' :
      // Display trackbacks differently than normal comments.
      ?>
      <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
      <p><?php _e( 'Pingback:', 'billio' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'billio' ), '<span class="edit-link">', '</span>' ); ?></p></li>
      <?php
    break;
  
    default :
      // Proceed with normal comments.

      ?>
              <div class="dt-reply-line"></div>
              <li class="comment_item media" id="comment-<?php print $comment->comment_ID; ?>">
                <div class="pull-<?php print is_rtl()?"right":"left";?> text-center">
                  <?php $avatar_url = billio_get_avatar_url(get_avatar( $comment, 100 )); ?>
                  <a href="<?php comment_author_url(); ?>"><img src="<?php echo esc_url($avatar_url); ?>" class="author-avatar img-responsive img-circle" alt="<?php comment_author(); ?>"></a>
                </div>
                <div class="media-body">
                  <div class="col-xs-12 col-sm-5<?php print is_rtl()?" col-sm-push-7":"";?> dt-comment-author"><?php comment_author(); ?></div>
                  <div class="col-xs-12 col-sm-7<?php print is_rtl()?" col-sm-pull-5":"";?> dt-comment-date secondary_color_text text-<?php print is_rtl()?"left":"right";?>"><?php comment_date('d.m.Y') ?></div>
                  <div class="col-xs-12 dt-comment-comment"><?php comment_text(); ?></div>
                  <div class="col-xs-12 text-<?php print is_rtl()?"left":"right";?> dt-comment-buttons">
                      <?php billio_comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'billio' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                      <?php billio_edit_comment_link( __( 'Edit', 'billio' ), '', '' ); ?>
                  </div>
                </div>
              </li>

      <?php
    break;
  endswitch; // end comment_type check
}
endif; 

if(!function_exists('nl2space')){
    function nl2space($str) {
        $arr=explode("\n",$str);
        $out='';

        for($i=0;$i<count($arr);$i++) {
            if(strlen(trim($arr[$i]))>0)
                $out.= trim($arr[$i]).' ';
        }
        return $out;
    }
}

// function to display number of posts.
function billio_get_post_views($postID){

    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return sprintf(__("%d View",'billio'),0);
    } elseif ($count<=1) {
        return sprintf(__("%d View",'billio'),$count);  
    }


    $output = str_replace('%', number_format_i18n($count),__('% Views'));
    return $output;
}

// function to count views.
function billio_set_post_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

function billio_post_view_column(){

  $post_types = get_post_types( array(),'names' );

      foreach ( $post_types as $post_type ) {
        if ( in_array($post_type,array('page','attachment','wpcf7_contact_form','vc_grid_item','nav_menu_item','revision')))
            continue;

          add_filter('manage_'.$post_type.'_posts_columns', 'billio_posts_column_views');
          add_action('manage_'.$post_type.'_posts_custom_column', 'billio_posts_custom_column_views',5,2);
    }
}

add_action('admin_init','billio_post_view_column');

function billio_posts_column_views($defaults){
    $defaults['post_views'] = __('Views','billio');
    return $defaults;
}

function billio_posts_custom_column_views($column_name, $id){

  if($column_name === 'post_views'){
        echo billio_get_post_views(get_the_ID());
    }
}

if(!function_exists('is_ssl_mode')){
function is_ssl_mode(){
  $ssl=strpos("a".site_url(),'https://');

  return (bool)$ssl;
}}

function maybe_ssl_url($url=""){
  return is_ssl_mode()?str_replace('http://', 'https://', $url):$url;
}

if (!function_exists('aq_resize')) {
  function aq_resize( $url, $width, $height = null, $crop = null, $single = true ) {

    if(!$url OR !($width || $height)) return false;

    //define upload path & dir
    $upload_info = wp_upload_dir();
    $upload_dir = $upload_info['basedir'];
    $upload_url = $upload_info['baseurl'];
    
    //check if $img_url is local
    /* Gray this out because WPML doesn't like it.
    if(strpos( $url, home_url() ) === false) return false;
    */
    
    //define path of image
    $rel_path = str_replace( str_replace( array( 'http://', 'https://' ),"",$upload_url), '', str_replace( array( 'http://', 'https://' ),"",$url));
    $img_path = $upload_dir . $rel_path;
    
    //check if img path exists, and is an image indeed
    if( !file_exists($img_path) OR !getimagesize($img_path) ) return false;
    
    //get image info
    $info = pathinfo($img_path);
    $ext = $info['extension'];
    list($orig_w,$orig_h) = getimagesize($img_path);
    
    $dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
    if(!$dims){
      return $single?$url:array('0'=>$url,'1'=>$orig_w,'2'=>$orig_h);
    }

    $dst_w = $dims[4];
    $dst_h = $dims[5];

    //use this to check if cropped image already exists, so we can return that instead
    $suffix = "{$dst_w}x{$dst_h}";
    $dst_rel_path = str_replace( '.'.$ext, '', $rel_path);
    $destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

    //if orig size is smaller
    if($width >= $orig_w) {

      if(!$dst_h) :
        //can't resize, so return original url
        $img_url = $url;
        $dst_w = $orig_w;
        $dst_h = $orig_h;
        
      else :
        //else check if cache exists
        if(file_exists($destfilename) && getimagesize($destfilename)) {
          $img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
        } 
        else {

          $imageEditor=wp_get_image_editor( $img_path );

          if(!is_wp_error($imageEditor)){

              $imageEditor->resize($width, $height, $crop );
              $imageEditor->save($destfilename);

              $resized_rel_path = str_replace( $upload_dir, '', $destfilename);
              $img_url = $upload_url . $resized_rel_path;


          }
          else{
              $img_url = $url;
              $dst_w = $orig_w;
              $dst_h = $orig_h;
          }

        }
        
      endif;
      
    }
    //else check if cache exists
    elseif(file_exists($destfilename) && getimagesize($destfilename)) {
      $img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
    } 
    else {

      $imageEditor=wp_get_image_editor( $img_path );

      if(!is_wp_error($imageEditor)){
          $imageEditor->resize($width, $height, $crop );
          $imageEditor->save($destfilename);

          $resized_rel_path = str_replace( $upload_dir, '', $destfilename);
          $img_url = $upload_url . $resized_rel_path;
      }
      else{
          $img_url = $url;
          $dst_w = $orig_w;
          $dst_h = $orig_h;
      }


    }
    
    if(!$single) {
      $image = array (
        '0' => $img_url,
        '1' => $dst_w,
        '2' => $dst_h
      );
      
    } else {
      $image = $img_url;
    }
    
    return $image;
  }
}


if (!function_exists('mb_strlen'))
{
  function mb_strlen($str="")
  {
    return strlen($str);
  }
}

function wp_trim_chars($text, $num_char = 55, $more = null){

  if ( null === $more )
    $more = '';
  $original_text = $text;
  $text = wp_strip_all_tags( $text );

  $words_array = preg_split( "/[\n\r\t ]+/", $text, $num_char + 1, PREG_SPLIT_NO_EMPTY );
  $text = @implode( ' ', $words_array );
  
  
  if ( strlen( $text ) > $num_char ) {
  
    $text = substr($text,0, $num_char );
    $text = $text . $more;
  }

  return apply_filters( 'wp_trim_chars', $text, $num_char, $more, $original_text );
}

if(!function_exists('hex2rgb')){
function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}
}

function billio_responsiveVideo($html, $data, $url) {

  $html=billio_add_video_wmode_transparent($html);

  if (!is_admin() && !preg_match("/flex\-video/mi", $html) /*&& preg_match("/youtube|vimeo/", $url)*/) {
    $html="<div class=\"flex-video widescreen\">".$html."</div>";
  }
  return $html;
}

add_filter('embed_handler_html', 'billio_responsiveVideo', 92, 3 ); 
add_filter('oembed_dataparse', 'billio_responsiveVideo', 90, 3 );
add_filter('embed_oembed_html', 'billio_responsiveVideo', 91, 3 );

function billio_add_video_wmode_transparent($html) {
   if (strpos($html, "<iframe " ) !== false) {
      $search = array('?feature=oembed');
      $replace = array('?feature=oembed&wmode=transparent&rel=0&autohide=1&showinfo=0');
      $html = str_replace($search, $replace, $html);

      return $html;
   } else {
      return $html;
   }
}

function billio_makeBottomWidgetColumn($params){

  global $detheme_config;

  if('detheme-bottom'==$params[0]['id']){

    $class="col-sm-4";

    if(isset($detheme_config['dt-footer-widget-column']) && $col=(int)$detheme_config['dt-footer-widget-column']){

      switch($col){

          case 2:
                $class='col-md-6 col-sm-6 col-xs-6';
            break;
          case 3:
                $class='col-md-4 col-sm-6 col-xs-6';
            break;
          case 4:
                $class='col-lg-3 col-md-4 col-sm-6 col-xs-6';
            break;
          case 1:
          default:
                $class='col-sm-12';
            break;
      }
    }


    $makerow="";


    $params[0]['before_widget']='<div class="border-left '.$class.' col-'.$col.'">'.$params[0]['before_widget'];
    $params[0]['after_widget']=$params[0]['after_widget'].'</div>'.$makerow;

 }

  return $params;

}

function billio_protected_meta($protected, $meta_key, $meta_type){

 $protected=(in_array($meta_key,
    array('vc_teaser','slide_template','pagebuilder','masonrycolumn','portfoliocolumn','portfoliotype','post_views_count','show_comment','show_social','sidebar_position','subtitle')
  ))?true:$protected;

  return $protected;
}

add_filter('is_protected_meta','billio_protected_meta',1,3);
add_filter( 'dynamic_sidebar_params', 'billio_makeBottomWidgetColumn' );

function billio_fill_width_dummy_widget (){

   global $detheme_config;
   $col=1;
   if(isset($detheme_config['dt-footer-widget-column']) && !empty($detheme_config['dt-footer-widget-column'])) {
      $col=(int)$detheme_config['dt-footer-widget-column'];
   }


   $sidebar = wp_get_sidebars_widgets();


   $itemCount=(isset($sidebar['detheme-bottom']))?count($sidebar['detheme-bottom']):0;

   switch($col){

          case 2:
                $class='col-md-6 col-sm-6 col-xs-6';
            break;
          case 3:
                $class='col-md-4 col-sm-6 col-xs-6';
            break;
          case 4:
                $class='col-lg-3 col-md-4 col-sm-6 col-xs-6';
            break;
          case 1:
          default:
                $class='col-sm-12';
            break;
  }


  if($itemCount % $col){
   print str_repeat("<div class=\"border-left dummy ".$class."\"></div>",$col - ($itemCount % $col));
 }



}

add_action('dynamic_sidebar_detheme-bottom','billio_fill_width_dummy_widget');

function billio_remove_shortcode_from_content($content) {
  // remove shortcodes
  $content = strip_shortcodes( $content );

  // remove images
  $content = preg_replace('/<img[^>]+./','', $content);
  
  return $content;
}

function billio_get_first_image_url_from_content() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  if (isset($post->post_content)) {
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    if (isset($matches[1][0])) {
      $first_img = $matches[1][0];
    }
  }

  return $first_img;
}

/* vc_set_as_theme */
if(function_exists('vc_set_as_theme'))
{

  if(!function_exists('vc_add_params')){
    function vc_add_params( $shortcode, $attributes ) {
      foreach ( $attributes as $attr ) {
        vc_add_param( $shortcode, $attr );
      }
    }

  }

  if(version_compare(WPB_VC_VERSION,'4.9.0','<')):

      function billio_vc_settings_general_callback(){


      $pt_array = ( $pt_array = get_option( 'wpb_js_content_types' ) ) ? ( $pt_array ) : vc_default_editor_post_types();

      $excludePostype=apply_filters( 'vc_settings_exclude_post_type',array( 'attachment', 'revision', 'nav_menu_item', 'mediapage' ));

      foreach ( get_post_types( array( 'public' => true )) as $pt) {
        if ( ! in_array( $pt, $excludePostype ) ) {
          $checked = ( in_array( $pt, $pt_array ) ) ? ' checked="checked"' : '';

           $post_type_object=get_post_type_object($pt);
           $label = $post_type_object->labels->singular_name;
          ?>
          <label>
            <input type="checkbox"<?php echo $checked; ?> value="<?php echo $pt; ?>"
                   id="wpb_js_post_types_<?php echo $pt; ?>"
                   name="wpb_js_content_types[]">
            <?php echo ucfirst(__( $label, 'js_composer' )); ?>
          </label><br>
        <?php
        }
      }
      ?>
      <p
        class="description indicator-hint"><?php _e( "Select for which content types Visual Composer should be available during post creation/editing.", "js_composer" ); ?></p>
    <?php
        }

      function vc_settings_general(){
          add_settings_field('wpb_js_content_types',__( "Content types", "js_composer" ),'billio_vc_settings_general_callback','vc_settings_general','wpb_js_composer_settings_general');
      }

      add_action('admin_init','vc_settings_general',9999);   

  endif;

  add_action('init','billio_dt_basic_grid_params');   

  function billio_dt_basic_grid_params(){

      $post_types = get_post_types( array(),'names' );

      $post_types_list = array();
      foreach ( $post_types as $post_type ) {
          if ( $post_type !== 'revision' && $post_type !== 'nav_menu_item' ) {
              
              $post_type_object=get_post_type_object($post_type);

              $label = $post_type_object->labels->singular_name;

              $post_types_list[] = array( $post_type, ucfirst(__( $label, 'js_composer' )) );
          }
      }

      $post_types_list[] = array( 'custom', __( 'Custom query', 'js_composer' ) );
      $post_types_list[] = array( 'ids', __( 'List of IDs', 'js_composer' ) );

      vc_add_param( 'vc_basic_grid', array(
              'type' => 'dropdown',
              'heading' => __( 'Data source', 'js_composer' ),
              'param_name' => 'post_type',
              'value' => $post_types_list,
              'description' => __( 'Select content type for your grid.', 'js_composer' )
      ));

  }


  if(is_plugin_active('billio_vc_addon/billio_vc_addon.php')){


      function billio_dt_section_header_params(){

          vc_add_param( 'section_header', array(
            'heading' => __( 'Layout type', 'billio' ),
            'param_name' => 'layout_type',
            'class' => '',
            'param_holder_class'=>'section-heading-style',
            'type' => 'select_layout',
             'value'=>array(
                '<img src="'.DETHEME_VC_DIR_URL.'lib/admin/images/section_heading_01.png" alt="'.esc_attr(__('Borderer','billio')).'" />' => 'section-heading-border',
                '<img src="'.DETHEME_VC_DIR_URL.'lib/admin/images/section_heading_06.png" alt="'.esc_attr(__('Color Background','billio')).'"/>' => 'section-heading-colorblock',
                '<img src="'.DETHEME_VC_DIR_URL.'lib/admin/images/section_heading_08.png" alt="'.esc_attr(__('Thick Border','billio')).'"/>' => 'section-heading-thick-border',
                '<img src="'.DETHEME_VC_DIR_URL.'lib/admin/images/section_heading_11.png" alt="'.esc_attr(__('Thin Border','billio')).'"/>' => 'section-heading-thin-border',
                '<img src="'.get_template_directory_uri().'/lib/images/section_heading_tripledots.jpg" alt="'.esc_attr(__('Triple Dots','billio')).'"/>' => 'section-heading-triple-dots',
                '<img src="'.get_template_directory_uri().'/lib/images/section_heading_tripledash.jpg" alt="'.esc_attr(__('Triple Dashes','billio')).'"/>' => 'section-heading-triple-dashes',
                '<img src="'.get_template_directory_uri().'/lib/images/section_heading_triplesquaredot.jpg" alt="'.esc_attr(__('Triple Square Dots','billio')).'"/>' => 'section-heading-triple-square-dots',
                '<img src="'.get_template_directory_uri().'/lib/images/section_heading_underline.jpg" alt="'.esc_attr(__('Underlined','billio')).'"/>' => 'section-heading-underlined',
                '<img src="'.get_template_directory_uri().'/lib/images/section_heading_horizontalline.jpg" alt="'.esc_attr(__('Horizontal Line Fullwidth','billio')).'"/>' => 'section-heading-horizontal-line-fullwidth',
                '<img src="'.get_template_directory_uri().'/lib/images/section_heading_horizontalline.jpg" alt="'.esc_attr(__('Horizontal Line','billio')).'"/>' => 'section-heading-horizontal-line',
                '<img src="'.get_template_directory_uri().'/lib/images/section_heading_roundedborder.jpg" alt="'.esc_attr(__('Rounded Border','billio')).'"/>' => 'section-heading-rounded',
                ),
            'dependency' => array( 'element' => 'use_decoration', 'value' => array('1')),        
          ));


          vc_add_param( 'section_header',array( 
            'heading' => __( 'Separator Color', 'detheme' ),
            'param_name' => 'separator_color',
            'param_holder_class'=>'width-3 inline-block',
            'value' => '',
            'type' => 'colorpicker',
            'std'=>'#444444',
            'dependency' => array( 'element' => 'use_decoration', 'value' => array('1')),        
             ));

        vc_add_params( 'section_header',array(
          array( 
          'heading' => __( 'Animation Type', 'detheme' ),
          'param_name' => 'spy',
          'class' => '',
          'value' => 
           array(
              __('Scroll Spy not activated','detheme') =>'none',
              __('The element fades in','detheme') => 'uk-animation-fade',
              __('The element scales up','detheme') => 'uk-animation-scale-up',
              __('The element scales down','detheme') => 'uk-animation-scale-down',
              __('The element slides in from the top','detheme') => 'uk-animation-slide-top',
              __('The element slides in from the bottom','detheme') => 'uk-animation-slide-bottom',
              __('The element slides in from the left','detheme') => 'uk-animation-slide-left',
              __('The element slides in from the right.','detheme') =>'uk-animation-slide-right',
           ),        
          'description' => __( 'Scroll spy effects', 'detheme' ),
          'type' => 'dropdown',
           ),
          array( 
          'heading' => __( 'Animation Delay', 'detheme' ),
          'param_name' => 'scroll_delay',
          'class' => '',
          'value' => '300',
          'description' => __( 'The number of delay the animation effect of the icon. in milisecond', 'detheme' ),
          'type' => 'textfield',
          'dependency' => array( 'element' => 'spy', 'value' => array( 'uk-animation-fade', 'uk-animation-scale-up', 'uk-animation-scale-down', 'uk-animation-slide-top', 'uk-animation-slide-bottom', 'uk-animation-slide-left', 'uk-animation-slide-right') )       
           )
        ));
      }


      add_action('init','billio_dt_section_header_params');   


      function billio_dt_timeline_params(){

        add_filter( "shortcode_atts_dt_timeline_item",create_function('$out','$out["icon_box"]="circle";return $out;'));

        vc_remove_param('dt_timeline_item','icon_box');

      }

      add_action('init','billio_dt_timeline_params');   


      add_action('init','billio_dt_progress_bar');   

      function billio_dt_progress_bar(){

        vc_remove_param('dt_progressbar_item','icon_type');

        function dt_progressbar_item_shortcode($atts, $content = null)

        {


            if(is_admin()){

            }else{
                wp_register_script('jquery.appear',DETHEME_VC_DIR_URL."js/jquery.appear.js",array());
                wp_register_script('jquery.counto',DETHEME_VC_DIR_URL."js/jquery.counto.js",array());
                wp_register_script('dt-chart',DETHEME_VC_DIR_URL."js/chart.js",array('jquery.appear','jquery.counto'));
                wp_enqueue_script('dt-chart');
            }
            
            extract( shortcode_atts( array(
                'title'=>''
            ), $atts ) );





            if (!isset($compile)) {$compile='';}



            extract(shortcode_atts(array(
                'width' => '',
                'title' => '',
                'unit' => '',
                'color'=>'#1abc9c',
                'bg'=>'#ecf0f1',
                'value' => '10',

            ), $atts));


            if(vc_is_inline()){

                $id="bar_".time()."_".rand(1,99);
            }

            $compile.='<div '.((vc_is_inline())?"id=\"".$id."\" ":"").'class=\'progress_bar\'>
                                    <div class="progress_info">
                                      <span class=\'progress_number\''.((vc_is_inline())?' style="opacity:1;"':"").'>
                                        <span>'.$value.'</span>
                                      </span>
                                      <span class="progres-unit">'.$unit.'</span>
                                      <span class=\'progress_title\'>'.$title.'</span>
                                    </div>
                                    <div '.((vc_is_inline())?'style="background:'.$bg.';"  ':"").'class=\'progress_content_outer\'>
                                        <div data-percentage=\''.$value.'\' '.((vc_is_inline())?'style="background:'.esc_attr($color).';width:'.$value.'%"  ':"").'data-active="'.esc_attr($color).'" data-nonactive="'.$bg.'" class=\'progress_content\'></div>
                                   </div>
                        </div>';


            $compile = "<div class='progress_bars'>".$compile."</div>";

            return $compile;

        }

        add_shortcode('dt_progressbar_item', 'dt_progressbar_item_shortcode');
      }


  }

  add_action('init','billio_vc_cta_2');   

  function billio_vc_cta_2(){

       vc_remove_param('vc_cta_button2','color');
        vc_add_param( 'vc_cta_button2', array( 
                "type" => "dropdown",
                "heading" => __("Button style", 'billio'),
                "param_name" => "btn_style",
                "value" => array(
                  __('Primary','billio')=>'color-primary',
                  __('Secondary','billio')=>'color-secondary',
                  __('Success','billio')=>'success',
                  __('Info','billio')=>'info',
                  __('Warning','billio')=>'warning',
                  __('Danger','billio')=>'danger',
                  __('Ghost Button','billio')=>'ghost',
                  __('Link','billio')=>'link',
                  ),
                "std" => 'default',
                "description" => __("Button style", 'billio')."."
                )
        );
     vc_add_param( 'vc_cta_button2',
        array(
          "type" => "dropdown",
          "heading" => __("Size", 'billio'),
          "param_name" => "size",
              "value" => array(
                __('Large','billio')=>'btn-lg',
                __('Default','billio')=>'btn-default',
                __('Small','billio')=>'btn-sm',
                __('Extra small','billio')=>'btn-xs'
                ),
          "std" => 'btn-default',
          "description" => __("Button size.", 'billio')
        ));
  }


  function billio_remove_meta_box_vc(){
    remove_meta_box( 'vc_teaser','page','side');
  }

  add_action('admin_init','billio_remove_meta_box_vc');   

  add_action('init','billio_vc_row');   

  function billio_vc_row(){

    function get_attach_video($settings,$value){

      $dependency =version_compare(WPB_VC_VERSION,'4.7.0','>=') ? "":vc_generate_dependencies_attributes( $settings );

      $value=intval($value);

      $video='';

      if($value){

       
        $media_url=wp_get_attachment_url($value);
        $mediadata=wp_get_attachment_metadata($value);


        $videoformat="video/mp4";

        if(is_array($mediadata) && $mediadata['mime_type']=='video/webm'){
             $videoformat="video/webm";
        }

        $video='<video class="attached_video" data-id="'.$value.'" autoplay width="266">
        <source src="'.esc_url($media_url).'" type="'.$videoformat.'" /></video>';
      }

      $param_line = '<div class="attach_video_field" '.$dependency.'>';
      $param_line .= '<input type="hidden" class="wpb_vc_param_value '.$settings['param_name'].' '.$settings['type'].'" name="'.$settings['param_name'].'" value="'.($value?$value:'').'"/>';
      $param_line .= '<div class="gallery_widget_attached_videos">';
      $param_line .= '<ul class="gallery_widget_attached_videos_list">';
      $param_line .= '<li><a class="gallery_widget_add_video" href="#" title="'.__('Add Video', "billio").'">'.($video!=''?$video:__('Add Video', "billio")).'</a>';
      $param_line .= '<a href="#" style="display:'.($video!=''?"block":"none").'" class="remove_attach_video">'.__('Remove Video').'</a></li>';
      $param_line .= '</ul>';
      $param_line .= '</div>';
      $param_line .= '</div>';

      return $param_line;

    }
 
    if(version_compare(WPB_VC_VERSION,'4.7.0','>=')){
      vc_add_shortcode_param( 'attach_video', 'get_attach_video',get_template_directory_uri()."/lib/js/vc_editor.min.js");
    }
    else{
      add_shortcode_param( 'attach_video', 'get_attach_video',get_template_directory_uri()."/lib/js/vc_editor.min.js");
    }

     vc_add_param( 'vc_row', array( 
          'heading' => __( 'Expand section width', 'billio' ),
          'param_name' => 'expanded',
          'class' => '',
          'value' => array(__('Expand Column','billio')=>'1',__('Expand Background','billio')=>'2'),
          'description' => __( 'Make section "out of the box".', 'billio' ),
          'type' => 'checkbox',
          'group'=>__('Extended options', 'billio')
      ) );

   
     vc_add_param( 'vc_row',   array( 
            'heading' => __( 'Background Type', 'billio' ),
            'param_name' => 'background_type',
            'value' => array('image'=>__( 'Image', 'billio' ),'video'=>__( 'Video', 'billio' )),
            'type' => 'radio',
            'group'=>__('Extended options', 'billio'),
            'std'=>'image'
         ));

     if(version_compare(WPB_VC_VERSION,'4.7.0','>=')){

          vc_remove_param('vc_row','full_width');
          vc_remove_param('vc_row','video_bg');
          vc_remove_param('vc_row','video_bg_url');
          vc_remove_param('vc_row','video_bg_parallax');
          vc_remove_param('vc_row','parallax');
          vc_remove_param('vc_row','parallax_image');

          if(version_compare(WPB_VC_VERSION,'4.11.0','>=') || version_compare(WPB_VC_VERSION,'4.11','>=')){
              vc_remove_param('vc_row','parallax_speed_video');
              vc_remove_param('vc_row','parallax_speed_bg');
          }

          vc_add_param( 'vc_row',   array( 
                  'heading' => __( 'Video Source', 'billio' ),
                  'param_name' => 'video_source',
                  'value' => array('local'=>__( 'Local Server', 'billio' ),'youtube'=>__( 'Youtube/Vimeo', 'billio' )),
                  'type' => 'radio',
                  'group'=>__('Extended options', 'billio'),
                  'std'=>'local',
                  'dependency' => array( 'element' => 'background_type', 'value' => array('video') )   
           ));


         vc_add_param( 'vc_row', array( 
              'heading' => __( 'Background Video (mp4)', 'billio' ),
              'param_name' => 'background_video',
              'type' => 'attach_video',
              'group'=>__('Extended options', 'billio'),
              'dependency' => array( 'element' => 'video_source', 'value' => array('local') )   
          ) );

         vc_add_param( 'vc_row', array( 
              'heading' => __( 'Background Video (webm)', 'billio' ),
              'param_name' => 'background_video_webm',
              'type' => 'attach_video',
              'group'=>__('Extended options', 'billio'),
              'dependency' => array( 'element' => 'video_source', 'value' => array('local') )   
          ) );

         vc_add_param( 'vc_row', array( 
              'heading' => __( 'Background Image', 'billio' ),
              'param_name' => 'background_image',
              'type' => 'attach_image',
              'group'=>__('Extended options', 'billio'),
              'dependency' => array( 'element' => 'background_type', 'value' => array('image') )   
          ) );

          vc_add_param( 'vc_row',
              array(
                'type' => 'textfield',
                'heading' => __( 'Video link', 'billio' ),
                'param_name' => 'video_bg_url',
                'group'=>__('Extended options', 'billio'),
                'description' => __( 'Add YouTube/Vimeo link', 'billio' ),
                'dependency' => array(
                  'element' => 'video_source',
                  'value' => array('youtube'),
                ),
           ));
      }
      else{

         vc_add_param( 'vc_row', array( 
              'heading' => __( 'Background Video (mp4)', 'billio' ),
              'param_name' => 'background_video',
              'type' => 'attach_video',
              'group'=>__('Extended options', 'billio'),
              'dependency' => array( 'element' => 'background_type', 'value' => array('video') )   
          ) );

         vc_add_param( 'vc_row', array( 
              'heading' => __( 'Background Video (webm)', 'billio' ),
              'param_name' => 'background_video_webm',
              'type' => 'attach_video',
              'group'=>__('Extended options', 'billio'),
              'dependency' => array( 'element' => 'background_type', 'value' => array('video') )   
          ) );

         vc_add_param( 'vc_row', array( 
              'heading' => __( 'Background Image', 'billio' ),
              'param_name' => 'background_image',
              'type' => 'attach_image',
              'group'=>__('Extended options', 'billio'),
              'dependency' => array( 'element' => 'background_type', 'value' => array('image') )   
          ) );


      }
 
     vc_add_param( 'vc_row', array( 
          'heading' => __( 'Extra id', 'billio' ),
          'param_name' => 'el_id',
          'type' => 'textfield',
          "description" => __("If you wish to add anchor id to this row. Anchor id may used as link like href=\"#yourid\"", "billio"),
      ) );


     vc_add_param( 'vc_row_inner', array( 
          'heading' => __( 'Extra id', 'billio' ),
          'param_name' => 'el_id',
          'type' => 'textfield',
          "description" => __("If you wish to add anchor id to this row. Anchor id may used as link like href=\"#yourid\"", "billio"),
      ) );

      vc_add_param( 'vc_row', array( 
          'heading' => __( 'Background Style', 'billio' ),
          'param_name' => 'background_style',
          'type' => 'dropdown',
          'value'=>array(
                __('No Repeat', 'wpb') => 'no-repeat',
                __("Cover", 'wpb') => 'cover',
                __('Contain', 'wpb') => 'contain',
                __('Repeat', 'wpb') => 'repeat',
                __("Parallax", 'billio') => 'parallax',
               __("Fixed", 'billio') => 'fixed',
              ),
          'group'=>__('Extended options', 'billio'),
          'dependency' => array( 'element' => 'background_type', 'value' => array('image') )       
      ) );
  }

 add_action('init','detheme_vc_single_image');   

  function detheme_vc_single_image(){

      vc_add_param( 'vc_single_image', array( 
          'heading' => __( 'Image Hover Option', 'billio' ),
          'param_name' => 'image_hover',
          'type' => 'radio',
          'value'=>array(
                'none'=>__("None", 'billio'),
                'image'=>__("Image", 'billio'),
                'text'=>__("Text", 'billio'),
              ),
          'group'=>__('Extended options', 'billio'),
          'std' => 'none'       
      ) );

      vc_add_param( 'vc_single_image', array( 
          'heading' => __( 'Image', 'billio' ),
          'param_name' => 'image_hover_src',
          'type' => 'attach_image',
          'value'=>"",
          'holder'=>'div',
          'param_holder_class'=>'image-hover',
          'group'=>__('Extended options', 'billio'),
          'dependency' => array( 'element' => 'image_hover','value'=>array('image'))       
      ) );

      vc_add_param( 'vc_single_image', array( 
          'heading' => __( 'Animation Style', 'billio' ),
          'param_name' => 'image_hover_type',
          'type' => 'dropdown',
          'value'=>array(
              __('Default','billio')=>'default',
              __('From Left','billio')=>'fromleft',
              __('From Right','billio')=>'fromright',
              __('From Top','billio')=>'fromtop',
              __('From Bottom','billio')=>'frombottom',
            ),
          'group'=>__('Extended options', 'billio'),
          'dependency' => array( 'element' => 'image_hover','value'=>array('image'))       
      ) );

      if(version_compare(WPB_VC_VERSION,'4.4.0','<')){
            vc_add_param( 'vc_single_image', array( 
                'heading' => __("Image style", "js_composer"),
                'param_name' => 'style',
                'type' => 'dropdown',
                'value'=>array(
                            "Default" => "",
                            "Rounded" => "vc_box_rounded",
                            "Border" => "vc_box_border",
                            "Outline" => "vc_box_outline",
                            "Shadow" => "vc_box_shadow",
                            "3D Shadow" => "vc_box_shadow_3d",
                            "Circle" => "vc_box_circle",
                            "Circle Border" => "vc_box_border_circle",
                            "Circle Outline" => "vc_box_outline_circle",
                            "Circle Shadow" => "vc_box_shadow_circle",
                            __("Diamond",'billio') => "dt_vc_box_diamond" //new from detheme
                        ),
            ) );

      }
      elseif(version_compare(WPB_VC_VERSION,'4.4.0','<=') && version_compare(WPB_VC_VERSION,'4.5.0','<')){
            vc_add_param( 'vc_single_image', array( 
                'heading' => __("Image style", "js_composer"),
                'param_name' => 'style',
                'type' => 'dropdown',
                'value'=>array(
                            "Default" => "",

                            'Rounded' => 'vc_box_rounded',
                            'Border' => 'vc_box_border',
                            'Outline' => 'vc_box_outline',
                            'Shadow' => 'vc_box_shadow',
                            'Bordered shadow' => 'vc_box_shadow_border',
                            '3D Shadow' => 'vc_box_shadow_3d',
                            'Circle' => 'vc_box_circle', //new
                            'Circle Border' => 'vc_box_border_circle', //new
                            'Circle Outline' => 'vc_box_outline_circle', //new
                            'Circle Shadow' => 'vc_box_shadow_circle', //new
                            'Circle Border Shadow' => 'vc_box_shadow_border_circle', //new
                            __("Diamond",'billio') => "dt_vc_box_diamond" //new from detheme
                        ),
            ) );
      }
      else{
            vc_add_param( 'vc_single_image', array( 
                'heading' => __("Image style", "js_composer"),
                'param_name' => 'style',
                'type' => 'dropdown',
                'value'=>array(
                            "Default" => "",
                            'Rounded' => 'vc_box_rounded',
                            'Border' => 'vc_box_border',
                            'Outline' => 'vc_box_outline',
                            'Shadow' => 'vc_box_shadow',
                            'Bordered shadow' => 'vc_box_shadow_border',
                            '3D Shadow' => 'vc_box_shadow_3d',
                            'Round' => 'vc_box_circle', //new
                            'Round Border' => 'vc_box_border_circle', //new
                            'Round Outline' => 'vc_box_outline_circle', //new
                            'Round Shadow' => 'vc_box_shadow_circle', //new
                            'Round Border Shadow' => 'vc_box_shadow_border_circle', //new
                            'Circle' => 'vc_box_circle_2', //new
                            'Circle Border' => 'vc_box_border_circle_2', //new
                            'Circle Outline' => 'vc_box_outline_circle_2', //new
                            'Circle Shadow' => 'vc_box_shadow_circle_2', //new
                            'Circle Border Shadow' => 'vc_box_shadow_border_circle_2', //new
                            __("Diamond",'billio') => "dt_vc_box_diamond" //new from detheme
                        ),
              'dependency' => array(
                'element' => 'source',
                'value' => array( 'media_library', 'featured_image' )
              ),

            ) );
      }

      vc_add_param( 'vc_single_image', array( 
          'heading' => __( 'Pre Title', 'billio' ),
          'param_name' => 'image_hover_pre_text',
          'type' => 'textfield',
          'value'=>"",
          'group'=>__('Extended options', 'billio'),
          'dependency' => array( 'element' => 'image_hover','value'=>array('text'))       
      ) );
      vc_add_param( 'vc_single_image', array( 
          'heading' => __( 'Title', 'billio' ),
          'param_name' => 'image_hover_text',
          'type' => 'textfield',
          'value'=>"",
          'group'=>__('Extended options', 'billio'),
          'dependency' => array( 'element' => 'image_hover','value'=>array('text'))       
      ) );
  }

}  

/* end vc_set_as_theme */

add_filter( 'get_search_form','billio_get_search_form', 10, 1 );

function billio_get_search_form( $form ) {
    $format = current_theme_supports( 'html5', 'search-form' ) ? 'html5' : 'xhtml';
    $format = apply_filters( 'search_form_format', $format );

    if ( 'html5' == $format ) {
      $form = '<form role="search" method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '">
        <label>
          <span class="screen-reader-text">' . _x( 'Search for:', 'label','billio' ) . '</span>
          <i class="icon-search-6"></i>
          <input type="search" class="search-field" placeholder="'.__('To search type and hit enter','billio').'" value="' . get_search_query() . '" name="s" title="' . esc_attr_x( 'Search for:', 'label','billio' ) . '" />
        </label>
        <input type="submit" class="search-submit" value="'. esc_attr_x( 'Search', 'submit button' ) .'" />
      </form>';
    } else {
      $form = '<form role="search" method="get" id="searchform" class="searchform" action="' . esc_url( home_url( '/' ) ) . '">
        <div>
          <label class="screen-reader-text" for="s">' . _x( 'Search for:', 'label','billio' ) . '</label>
          <i class="icon-search-6"></i>
          <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="'.__('To search type and hit enter','billio').'" />
          <input type="submit" id="searchsubmit" value="'. esc_attr_x( 'Search', 'submit button' ) .'" />
        </div>
      </form>';
    }

  return $form;
}

add_filter( 'get_product_search_form','billio_get_product_search_form', 10, 1 );

function billio_get_product_search_form( $form ) {
  $form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/'  ) ) . '">
      <div>
        <label class="screen-reader-text" for="s">' . __( 'Search for:', 'woocommerce' ) . '</label>
        <i class="icon-search-6"></i>
        <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __( 'Search for products', 'woocommerce' ) . '" />
        <input type="submit" id="searchsubmit" value="'. esc_attr__( 'Search', 'woocommerce' ) .'" />
        <input type="hidden" name="post_type" value="product" />
      </div>
    </form>';

  return $form;
}

function is_detheme_home($post=null){

  if(!isset($post)) $post=get_post();

  return apply_filters('is_detheme_home',false,$post);
}


function billio_remove_excerpt_more($excerpt_more=""){

  return "&hellip;";
}

add_filter('excerpt_more','billio_remove_excerpt_more');

function billio_prepost_vc_basic_grid_settings($content){

        $regexshortcodes=
        '\\['                              // Opening bracket
        . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
        . "(vc_basic_grid|vc_masonry_grid)"// 2: Shortcode name
        . '(?![\\w-])'                       // Not followed by word character or hyphen
        . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
        .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
        .     '(?:'
        .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
        .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
        .     ')*?'
        . ')'
        . '(?:'
        .     '(\\/)'                        // 4: Self closing tag ...
        .     '\\]'                          // ... and closing bracket
        . '|'
        .     '\\]'                          // Closing bracket
        .     '(?:'
        .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
        .             '[^\\[]*+'             // Not an opening bracket
        .             '(?:'
        .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
        .                 '[^\\[]*+'         // Not an opening bracket
        .             ')*+'
        .         ')'
        .         '\\[\\/\\2\\]'             // Closing shortcode tag
        .     ')?'
        . ')'
        . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

  $content=preg_replace('/'.$regexshortcodes.'/s', '[$2 $3 post_id="'.get_the_ID().'"]', $content);
  return $content;

}

function get_billio_pre_footer_page(){

  global $detheme_config;

  $args=wp_parse_args($detheme_config,array('showfooterpage'=>true,'footerpage'=>false));
  $post_ID=get_the_ID();

  $originalpost = $GLOBALS['post'];

  if(!$args['showfooterpage'] || !$args['footerpage'] || $post_ID==$args['footerpage'])
    return;

  $post = _get_wpml_post($args['footerpage']);
  if(!$post)  return;

  $old_sidebar=get_query_var('sidebar');

  set_query_var('sidebar','nosidebar');
  if($detheme_config['dt-header-type']=='leftbar'){
    $pre_footer_page="<div class=\"vertical_menu_container\">".do_shortcode($post->post_content)."</div>";

  }
  else if($post){


    $GLOBALS['post']=$post;
    $pre_footer_page=do_shortcode(billio_prepost_vc_basic_grid_settings($post->post_content));
    $GLOBALS['post']=$originalpost;


  }
  set_query_var('sidebar',$old_sidebar);
  print $pre_footer_page;

}


add_action('before_footer_section','get_billio_pre_footer_page'); 

function get_billio_post_footer_page(){

  global $detheme_config;

  $args=wp_parse_args($detheme_config,array('showfooterpage'=>true,'postfooterpage'=>false));
  $post_ID=get_the_ID();

  $originalpost = $GLOBALS['post'];


  if(!$args['showfooterpage'] || !$args['postfooterpage'] || $post_ID==$args['postfooterpage'])
    return;

  $post = _get_wpml_post($args['postfooterpage']);
  if(!$post)  return;

  $old_sidebar=get_query_var('sidebar');
  set_query_var('sidebar','nosidebar');
  if($detheme_config['dt-header-type']=='leftbar'){
    $post_footer_page="<div class=\"vertical_menu_container\">".do_shortcode($post->post_content)."</div>";

  }
  else if($post){

    $GLOBALS['post']=$post;
    $post_footer_page=do_shortcode(billio_prepost_vc_basic_grid_settings($post->post_content));
    $GLOBALS['post']=$originalpost;

  }
  set_query_var('sidebar',$old_sidebar);

  print $post_footer_page;

}

add_action('after_footer_section','get_billio_post_footer_page'); 

/*wpml translation */

function _get_wpml_post($post_id){

  if(!defined('ICL_LANGUAGE_CODE'))
        return get_post($post_id);

    global $wpdb;

   $postid = $wpdb->get_var(
      $wpdb->prepare("SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid=(SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id='%d' LIMIT 1) AND element_id!='%d' AND language_code='%s'", $post_id,$post_id,ICL_LANGUAGE_CODE)
   );

  if($postid)
      return get_post($postid);
  
  return get_post($post_id);
}

function get_billio_menu_pagebar(){

  global $detheme_config;

  if(is_front_page() || is_detheme_home(get_post())){
      $backgroundType = ($detheme_config['homepage-background-type']) ? "solid" : "transparent";
  } else {
      $backgroundType = ($detheme_config['header-background-type']) ? "solid" : "transparent";
  }

  $args=wp_parse_args($detheme_config,array('dt-header-type'=>'left','showpostmenupage'=>false,'postmenupage'=>false));

  $post_ID=get_the_ID();
  $originalpost = $GLOBALS['post'];


  if($args['dt-header-type']!='pagebar' || !$args['showpostmenupage'] || !$args['postmenupage'] || 
    ($post_ID==$args['postmenupage'] && !is_search())) {
      return;
  }

  $post = _get_wpml_post($args['postmenupage']);
  if(!$post)  return;

  $old_sidebar=get_query_var('sidebar');

  set_query_var('sidebar','nosidebar');

  $GLOBALS['post']=$post;
  print "<div id=\"".esc_attr('dt_pagebar')."\"><div class=\"dt_pagebar_menu\"><div class=\"menu_background_color ". $backgroundType ."\"></div></div><div class=\"dt_pagebar_wrapper\">".do_shortcode(billio_prepost_vc_basic_grid_settings($post->post_content))."</div></div>";

  $GLOBALS['post']=$originalpost;

  set_query_var('sidebar',$old_sidebar);
}

add_action('after_menu_section','get_billio_menu_pagebar'); 

if (is_plugin_active('detheme-career/detheme_career.php')) {


  function get_career_attachment($phpmailer){

     $attachment=$_FILES['attachment'];


      $filesize=$attachment['size'];
      $filesource=$attachment['tmp_name'];
      $filename=$attachment['name'];

      try {
        $phpmailer->AddAttachment($filesource,$filename);
      } catch ( phpmailerException $e ) {

      }
  }

  function billio_proccess_apply_career(){


    global $detheme_config;
    $career_id=intVal($_POST['career_id']);
    $fullname=sanitize_text_field($_POST['fullname']);
    $email=sanitize_email($_POST['email']);
    $notes=esc_textarea($_POST['notes']);
    $recipient=$detheme_config['career_email'];
    $attachment=$_FILES['attachment'];
    $thankyoumessage=wpautop($detheme_config['career_thankyou']);
    $career_attach_type=isset($detheme_config['career_attach_type'])?$detheme_config['career_attach_type']:false;


    if($recipient==''){

      $super_admins = get_super_admins();
        foreach ($super_admins as $admin) {
            $adminuser=get_user_by('login', $admin);
            $recipient[]=$adminuser->user_email;
        }

    }

    $career=get_post($career_id);

    if(!$career){
      print json_encode(array('error'=>__('Job position not found or closed','billio')));
      die();

    }

    $from_email = get_bloginfo( 'admin_email' );
    $attachlimit =(isset($detheme_config['career_attach_limit']) && ''!=$detheme_config['career_attach_limit'])? $detheme_config['career_attach_limit']:1024;


    if($attachment){

        if($attachment['size'] > $attachlimit*1024){
           print json_encode(array('error'=>__('Attachment size exceed allowed','billio')));
           die();

        }

        if($career_attach_type){
            $allowed=false;

            foreach (array_keys($career_attach_type) as $key) {
              if (wp_match_mime_types( $key, $attachment['type'] ) ){
                $allowed=true;
                break;
              }
            }

            if(!$allowed){
               print json_encode(array('error'=>sprintf(__('%s not allowed','billio'),$attachment['type'])));
               die();
            }

        }
        else{
           print json_encode(array('error'=>__('Attachment type not allowed','billio')));
           die();

        }
        add_action( 'phpmailer_init', 'get_career_attachment');
    }

    $subject = sprintf(__('Apply Job for Position %s','billio'),ucwords($career->post_name));
    $headers = "From: " . stripslashes_deep( html_entity_decode( $fullname, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
    $headers.= "Reply-To: ". $email . "\r\n";
    $headers.= "Return-Path: ".$email."\r\n";
    $headers.= "MIME-Version: 1.0\r\n"; 
    $headers.= "X-Priority: 1\r\n"; 


   // if ( $use_html ) {
      $headers .= "Content-Type: text/html\n";
    //}

    $notes.="\n\n".sprintf(__('This application for job position %s','billio'),'<a href="'.get_permalink($career_id).'">'.$career->post_name.'</a>');

    $body = wpautop($notes); 


    $sendmail=wp_mail( $recipient, $subject, $body, $headers);

    if($sendmail){
      print json_encode(array('success'=>$thankyoumessage));
    }
    else{
      print json_encode(array('error'=>__('Could not instantiate mail function','billio')));
    }
    die();
  }


  add_action('wp_ajax_billio_apply_career','billio_proccess_apply_career');
  add_action('wp_ajax_nopriv_billio_apply_career','billio_proccess_apply_career');


  function billio_proccess_send_friend_career(){


    global $detheme_config;
    $career_id=intVal($_POST['career_id']);
    $fullname=sanitize_text_field($_POST['fullname']);
    $email=sanitize_email($_POST['email']);
    $recipient=sanitize_email($_POST['email_to']);
    $notes=esc_textarea($_POST['notes']);
    $thankyoumessage=__('Your email has been sent','billio');

    $career=get_post($career_id);

    if(!$career){
      print json_encode(array('error'=>__('Job position not found or closed','billio')));
      die();

    }

    $from_email = $email;


    $subject = sprintf(__('%s tell you about job for Position %s','billio'),ucfirst($fullname),ucwords($career->post_name));
    $headers = "From: " . stripslashes_deep( html_entity_decode( $fullname, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
    $headers.= "Reply-To: ". $email . "\r\n";
    $headers.= "Return-Path: ".$email."\r\n";
    $headers.= "MIME-Version: 1.0\r\n"; 
    $headers.= "X-Priority: 1\r\n"; 


    $headers .= "Content-Type: text/html\n";


    $body = sprintf(__("Hi,\nYour friend %s about job position %s.\n",'billio'),$fullname,'<a href="'.get_permalink($career_id).'">'.$career->post_name.'</a>').
    "\n\n".
    wpautop($notes); 


    $sendmail=wp_mail( $recipient, $subject, $body, $headers);

    if($sendmail){
      print json_encode(array('success'=>$thankyoumessage));
    }
    else{
      print json_encode(array('error'=>__('Could not instantiate mail function','billio')));
    }
    die();

  }

  add_action('wp_ajax_billio_send_friend_career','billio_proccess_send_friend_career');
  add_action('wp_ajax_nopriv_billio_send_friend_career','billio_proccess_send_friend_career');




  if (is_plugin_active('billio_vc_addon/billio_vc_addon.php') && function_exists('vc_set_as_theme')) {

    function billio_vc_billio_career_params(){

      $fields=detheme_career::get_dtcareer_job_fields();
      $jobsoptions=array();

      if($fields){
        foreach ($fields as $key => $field) {
          $jobsoptions[$field['label']]=$key;
        }
      }

        vc_add_param( 'dt_career', array( 
          'heading' => __( 'Job Fields Shown', 'billio' ),
          'param_name' => 'jobs',
          'class' => '',
          'value' => $jobsoptions,
          'type' => 'checkbox'
         ));
    }

    add_action('init','billio_vc_billio_career_params');
  }

}


/* detheme-post & detheme-career handle */

function billio_loadDethemePostTemplate(){

    global $post,$wp_query,$GLOBALS;

    if(!isset($post) || isset($_GET['type']))
        return true;

    $standard_type=$post->post_type;

    if(is_archive() && in_array($standard_type,array('dtpost','dtcareer','essential_grid'))){

        $post_type_data = get_post_type_object( $standard_type);

        $post_type_slug = $post_type_data->rewrite['slug'];

        if(!$page = get_page_by_path($post_type_slug))
        return true;

        $query_vars=array(
        'post_type' => 'page',
        'page_id'=>$page->ID,
        'posts_per_page'=>1
        );

       $original_query_vars=$wp_query->query_vars;

       $wp_query->query($query_vars);
       if(!$wp_query->have_posts()){
           $wp_query->query($original_query_vars);
           return true;
       }

      $GLOBALS['post']=$page;
    }
    else{
      return true;
    }
}

add_action('template_redirect', 'billio_loadDethemePostTemplate');


/* essential grid post handle */

if (is_plugin_active('essential-grid/essential-grid.php')) {

  function billio_essential_grid_labels($labels){

    $dtpost_settings=get_option('essential_grid_settings');

    if(!$dtpost_settings || !is_array($dtpost_settings)){
      return $labels;
    }

    if(isset($dtpost_settings['label']) && ''!=$dtpost_settings['label']){

      $labels->label=$dtpost_settings['label'];
      $labels->all_items=$dtpost_settings['label'];
      $labels->menu_name=$dtpost_settings['label'];
      $labels->name=$dtpost_settings['label'];

    }

    if(isset($dtpost_settings['singular_label']) && ''!=$dtpost_settings['singular_label']){

      $labels->singular_label=$dtpost_settings['singular_label'];
      $labels->singular_name=$dtpost_settings['singular_label'];

    }

    if(isset($dtpost_settings['slug']) && ''!=$dtpost_settings['slug']){

      $labels->rewrite['slug']=$dtpost_settings['slug'];

    }
    return $labels;
  }

  function billio_essential_grid_setting_page($post){


    $dtpost_settings=get_option('essential_grid_settings',array('label'=>__("Ess. Grid Posts", EG_TEXTDOMAIN),'singular_label'=>__("Ess. Grid Post", EG_TEXTDOMAIN),'slug'=>''));

    if(wp_verify_nonce( isset($_POST['essential_grid-setting'])?$_POST['essential_grid-setting']:"", 'essential_grid-setting')){

         $dtpost_name=(isset($_POST['dtpost_name']))?$_POST['dtpost_name']:'';
         $singular_name=(isset($_POST['singular_name']))?$_POST['singular_name']:'';
         $rewrite_slug=(isset($_POST['dtpost_slug']))?$_POST['dtpost_slug']:'';

         $do_update=false;

         if($dtpost_name!=$dtpost_settings['label'] && ''!=$dtpost_name){
            $dtpost_settings['label']=$dtpost_name;
            $do_update=true;
         }

         if($singular_name!=$dtpost_settings['singular_label'] && ''!=$singular_name){
            $dtpost_settings['singular_label']=$singular_name;
            $do_update=true;
           
         }

         if($rewrite_slug!=$dtpost_settings['slug']){
            $dtpost_settings['slug']=$rewrite_slug;
            $do_update=true;
         
         }

         if($do_update){
             update_option('essential_grid_settings',$dtpost_settings);
         }

    }



    $args = array( 'page' => 'essential_grid_setting');
    $url = esc_url(add_query_arg( $args, admin_url( 'admin.php' )));

    $dtpost_name=$dtpost_settings['label'];
    $singular_name=$dtpost_settings['singular_label'];
    $slug=$dtpost_settings['slug'];
?>
<div class="dtpost-panel">
<h2><?php printf(__('%s Settings', 'billio'),ucwords($dtpost_name));?></h2>
<form method="post" action="<?php print esc_url($url);?>">
<?php wp_nonce_field( 'essential_grid-setting','essential_grid-setting');?>
<input name="option_page" value="reading" type="hidden"><input name="action" value="update" type="hidden">
<table class="form-table">
<tbody>
<tr>
<th scope="row"><label for="dtpost_name"><?php _e('Label Name','billio');?></label></th>
<td>
<input name="dtpost_name" id="dtpost_name" max-length="50" value="<?php print $dtpost_name;?>" class="" type="text"></td>
</tr>
<tr>
<th scope="row"><label for="singular_name"><?php _e('Singular Name','billio');?></label></th>
<td>
<input name="singular_name" id="singular_name" max-length="50" value="<?php print $singular_name;?>" class="" type="text"></td>
</tr>
<tr>
<th scope="row"><label for="dtpost_slug"><?php _e('Rewrite Slug','billio');?></label></th>
<td>
<input name="dtpost_slug" id="dtpost_slug" max-length="50" value="<?php print $slug;?>" class="" type="text"></td>
</tr>
</tbody></table>


<p class="submit"><input name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes');?>" type="submit"></p></form>
</div>
<?php
  }

  function billio_essential_grid_seeting_menu(){
        add_submenu_page( 'edit.php?post_type=essential_grid', __('Settings', 'billio'), __('Settings', 'billio'),'manage_options','essential_grid_setting','billio_essential_grid_setting_page');
  }

  add_action('admin_menu', 'billio_essential_grid_seeting_menu');

  add_filter( 'post_type_labels_essential_grid', 'billio_essential_grid_labels');

  function billio_related_query_post_grid($query){

    $query['post__not_in']=(isset($query['post__not_in']) && is_array($query['post__not_in']))? array_push(get_the_id(),$query['post__not_in']):array(get_the_id());
    return $query;
  }

  add_filter('essgrid_get_posts','billio_related_query_post_grid');

  add_filter('essgrid_query_caching','__return_false');


  function billio_ess_grid_post_type($post_type, $args){

    global $wp_post_types;

    if($post_type!='essential_grid') return true;

     $dtpost_settings = get_option('essential_grid_settings');

     if(!$dtpost_settings || !isset($dtpost_settings['slug']) || $dtpost_settings['slug']=='') return true;



     $essential_post=$wp_post_types['essential_grid'];
     $essential_post->has_archive=true;
     $essential_post->rewrite['slug']=$dtpost_settings['slug'];

     $wp_post_types['essential_grid']=$essential_post;

     add_rewrite_tag( "%$post_type%", '(.+?)', $args->query_var ? "{$args->query_var}=" : "post_type=$post_type&pagename=" );

     add_rewrite_rule( "{$dtpost_settings['slug']}/?$", "index.php?post_type=$post_type", 'top' );


     $permastruct_args = $args->rewrite;

     $permastruct_args['feed'] = isset($permastruct_args['feeds'])?$permastruct_args['feeds']:false;
     add_permastruct( $post_type, $dtpost_settings['slug']."/%$post_type%", $permastruct_args );
  }

  add_action( 'registered_post_type', 'billio_ess_grid_post_type',999,2);
}


/* comment setting */

function billio_is_comment_open($open, $post_id){

  global $detheme_config;

  $post_type = get_post_type($post_id);

  if(!in_array($post_type,billio_post_use_comment()) && isset($detheme_config['comment-open-'.$post_type])){
    return ((bool)$detheme_config['comment-open-'.$post_type]) && $open;
  }

  return $open;
}

add_filter( 'comments_open','billio_is_comment_open',0,2);

/* dt carousel image size */

function billio_create_carousel_size($out, $id){

  if(!$id) return $out;

  $img_url = wp_get_attachment_url($id);
  if($newsize=aq_resize($img_url,350,230,true,false)){
    return $newsize;
  }
  return $out;
}

add_filter('dt_carousel_pagination_image','billio_create_carousel_size',1,2);

/* document viewer download button */

function billio_document_download_button($button,$mediaurl){

  return "<a class=\"dt-download-button btn btn-color-primary skin-dark\" href=\"javascript:;\" onClick=\"javascript:window.location.assign('".$mediaurl."');\" target=\"_blank\">".__('Download','detheme')."</a>";
}

add_filter('dt-download-button','billio_document_download_button',1,2);



//add_action('get_breadcrumb','billio_breadcrumbs');
/** Breadcrumbs **/
/** http://dimox.net/wordpress-breadcrumbs-without-a-plugin/ **/
function dimox_breadcrumbs() {
  /* === OPTIONS === */
  $text['home']     = __('Home','billio'); // text for the 'Home' link
  $text['category'] = '%s'; // text for a category page
  $text['search']   = '%s'; // text for a search results page
  $text['tag']      = '%s'; // text for a tag page
  $text['author']   = '%s'; // text for an author page
  $text['404']      = __('Error 404','billio'); // text for the 404 page

  $show_current   = 1; // 1 - show current post/page/category title in breadcrumbs, 0 - don't show
  $show_on_home   = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
  $show_home_link = 1; // 1 - show the 'Home' link, 0 - don't show
  $show_title     = 1; // 1 - show the title for the links, 0 - don't show
  $delimiter      = '&nbsp;/&nbsp;'; // delimiter between crumbs
  $before         = '<span class="current">'; // tag before the current crumb
  $after          = '</span>'; // tag after the current crumb
  /* === END OF OPTIONS === */

  global $post;
  $home_link    = home_url('/');
  $link_before  = '<span typeof="v:Breadcrumb">';
  $link_after   = '</span>';
  $link_attr    = ' rel="v:url" property="v:title"';
  $link         = $link_before . '<a' . esc_attr($link_attr) . ' href="%1$s">%2$s</a>' . $link_after;

  if ($post) {
    $parent_id    = $parent_id_2 = $post->post_parent;
  }
  $frontpage_id = get_option('page_on_front');

  if (is_home() || is_front_page()) {

    if ($show_on_home == 1) echo '<div class="breadcrumbs"><a href="' . esc_url($home_link) . '">' . $text['home'] . '</a></div>';

  } else {

    echo '<div class="breadcrumbs">';
    if ($show_home_link == 1) {
      echo '<a href="' . esc_url($home_link) . '" rel="v:url" property="v:title">' . $text['home'] . '</a>';

      if ( is_search() ) {
        echo $delimiter;
      } else {
        if ($frontpage_id == 0 || $parent_id != $frontpage_id) echo $delimiter;
      }
    }

    $page_number = "";
    if ( get_query_var('paged') ) {
      $page_number .=  ' (' . __('Page','billio') . ' ' . get_query_var('paged') . ') ';
    }

    $after = $page_number . $after;


    if ( is_category() ) {
      $this_cat = get_category(get_query_var('cat'), false);
      if ($this_cat->parent != 0) {
        $cats = get_category_parents($this_cat->parent, TRUE, $delimiter);
        if ($show_current == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
        $cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
        $cats = str_replace('</a>', '</a>' . $link_after, $cats);
        if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
        echo $cats;
      }
      if ($show_current == 1) echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;

    } elseif ( is_search() ) {
      echo $before . sprintf($text['search'], get_search_query()) . $after;

    } elseif ( is_day() ) {
      echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
      echo sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
      echo $before . get_the_time('d') . $after;

    } elseif ( is_month() ) {
      echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
      echo $before . get_the_time('F') . $after;

    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;

    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        printf($link, $home_link . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
        if ($show_current == 1) echo $delimiter . $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, $delimiter);
        if ($show_current == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
        $cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
        $cats = str_replace('</a>', '</a>' . $link_after, $cats);
        if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
        echo $cats;
        if ($show_current == 1) echo $before . get_the_title() . $after;
      }

    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;

    } elseif ( is_attachment() ) {
      $parent = get_post($parent_id);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      if ($cat) {
        $cats = get_category_parents($cat, TRUE, $delimiter);
        $cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
        $cats = str_replace('</a>', '</a>' . $link_after, $cats);
        if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
        echo $cats;
      }
      printf($link, get_permalink($parent), $parent->post_title);
      if ($show_current == 1) echo $delimiter . $before . get_the_title() . $after;

    } elseif ( is_page() && !$parent_id ) {
      if ($show_current == 1) echo $before . get_the_title() . $after;

    } elseif ( is_page() && $parent_id ) {
      if ($parent_id != $frontpage_id) {
        $breadcrumbs = array();
        while ($parent_id) {
          $page = get_page($parent_id);
          if ($parent_id != $frontpage_id) {
            $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
          }
          $parent_id = $page->post_parent;
        }
        $breadcrumbs = array_reverse($breadcrumbs);
        for ($i = 0; $i < count($breadcrumbs); $i++) {
          echo $breadcrumbs[$i];
          if ($i != count($breadcrumbs)-1) echo $delimiter;
        }
      }
      if ($show_current == 1) {
        if ($show_home_link == 1 || ($parent_id_2 != 0 && $parent_id_2 != $frontpage_id)) echo $delimiter;
        echo $before . get_the_title() . $after;
      }

    } elseif ( is_tag() ) {
      echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;

    } elseif ( is_author() ) {
      global $author;
      $userdata = get_userdata($author);
      echo $before . sprintf($text['author'], $userdata->display_name) . $after;

    } elseif ( is_404() ) {
      echo $before . $text['404'] . $after;

    } elseif ( has_post_format() && !is_singular() ) {
      echo get_post_format_string( get_post_format() );
    }

    echo '</div><!-- .breadcrumbs -->';

  }
} // end dimox_breadcrumbs()

/** Billio Breadcrumbs **/

function billio_breadcrumb($args=array()){

  $args=wp_parse_args($args,array(
    'wrap_before' => '<div class="breadcrumbs">',
    'wrap_after' => '</div>',
    'format' => '<span%s>%s</span>',
    'delimiter'=>'/',
    'current_class' => 'current',
    'home_text' => __('Home','billio'), 
    'home_link' => home_url('/')
   ));

   $breadcrumbs=billio_get_breadcrumbs($args);

    if (is_plugin_active('woocommerce/woocommerce.php') && (is_product()||is_cart()||is_checkout()||is_shop()||is_product_category())) {
      // do nothing
      // woocomerce has different breadcrumb method
    } else {
       $out=$args['wrap_before'];
       $out.=join($args['delimiter']."\n",is_rtl()?array_reverse($breadcrumbs):$breadcrumbs);
       $out.=$args['wrap_after'];
       print $out;
    }
}

if ( ! function_exists( 'billio_woocommerce_breadcrumb' ) ) {

  /**
   * Output the WooCommerce Breadcrumb
   */
  function billio_woocommerce_breadcrumb(&$breadcrumbs, $args = array() ) {
    $wc_breadcrumb_args = array(
      'delimiter' => $args['delimiter'],
      'wrap_before' => '<div class="breadcrumbs">',
      'wrap_after' => '</div>',
      'before' => '<span>',
      'beforecurrent' => '<span class="current">',
      'after' => '</span>',
      'home' => $args['home_text'],
    );

    woocommerce_breadcrumb($wc_breadcrumb_args);

  }
}



function billio_get_breadcrumbs($breadcrumb_args) {
  global $post;

   $breadcrumbs[]=sprintf($breadcrumb_args['format'],is_front_page()?' class="current"':'','<a href="'.$breadcrumb_args['home_link'].'" title="'.$breadcrumb_args['home_text'].'">'.$breadcrumb_args['home_text'].'</a>');

  if (is_front_page()) { // home page
  } elseif (is_home()) { // blog page
      billio_get_breadcrumbs_from_menu(get_option('page_for_posts'),$breadcrumbs,$breadcrumb_args);

  } elseif (is_singular('dtpost')||is_singular('dtcareer')||is_singular('dtreportpost')||is_singular('essential_grid')) {


      $post_type_data = get_post_type_object($post->post_type);
      $post_type_slug = $post_type_data->rewrite['slug'];
      $page = get_page_by_path($post_type_slug);

      if ($page) {
        billio_get_breadcrumbs_from_menu($page->ID,$breadcrumbs,$breadcrumb_args,false);
      }

      array_push($breadcrumbs, sprintf($breadcrumb_args['format']," class=\"".$breadcrumb_args['current_class']."\"",$post->post_title));

  } elseif (is_singular()) {
        if (is_plugin_active('woocommerce/woocommerce.php') && (is_product()||is_cart()||is_checkout())) {

            billio_woocommerce_breadcrumb($breadcrumbs,$breadcrumb_args);
        } else if (is_single()) {
            billio_get_breadcrumbs_from_menu(get_option('page_for_posts'),$breadcrumbs,$breadcrumb_args,false);
            array_push($breadcrumbs, sprintf($breadcrumb_args['format']," class=\"".$breadcrumb_args['current_class']."\"",$post->post_title));
        } else {
            billio_get_breadcrumbs_from_menu($post->ID,$breadcrumbs,$breadcrumb_args);
            if (count($breadcrumbs) < 2 ) {
              array_push($breadcrumbs, sprintf($breadcrumb_args['format']," class=\"".$breadcrumb_args['current_class']."\"",$post->post_title));
            }
        }
  } else {
      $post_id = -1;
      if (is_plugin_active('woocommerce/woocommerce.php') && (is_shop()||is_product_category())) {

        billio_woocommerce_breadcrumb($breadcrumbs,$breadcrumb_args);

      } else {

        if(is_category()){
          $breadcrumbs[]=sprintf($breadcrumb_args['format']," class=\"".$breadcrumb_args['current_class']."\"",single_cat_title(' ',false));
        }
        elseif(is_archive()){
          $breadcrumbs[]=sprintf($breadcrumb_args['format']," class=\"".$breadcrumb_args['current_class']."\"",is_tag()||is_tax()?single_tag_title(' ',false):single_month_title( ' ', false ));
        }
        else{
          if (isset($post->ID)) {
            $post_id = $post->ID;
            billio_get_breadcrumbs_from_menu($post_id,$breadcrumbs,$breadcrumb_args);
          }
        }
      }
  }

  return apply_filters('billio_breadcrumbs',$breadcrumbs,$breadcrumb_args);
}


function billio_get_breadcrumbs_from_menu($post_id,&$breadcrumbs,$args,$iscurrent=true) {
  $primary = get_nav_menu_locations();

  if (isset($primary['primary'])) {
    $navs = wp_get_nav_menu_items($primary['primary']);

    foreach ($navs as $nav) {
      if (($nav->object_id)==$post_id) {

        if ($nav->menu_item_parent!=0) {
          //start recursive by menu parent
          billio_get_breadcrumbs_from_menu_by_menuid($nav->menu_item_parent,$breadcrumbs,$args);
        }

        if ($iscurrent) {
          array_push($breadcrumbs, sprintf($args['format']," class=\"".$args['current_class']."\"",$nav->title));
        } else {
          array_push($breadcrumbs, sprintf($args['format'],"", '<a href="'.$nav->url.'" title="'.$nav->title.'">'.$nav->title .'</a>' ));
        }

        break;
      }
    } 
  }  
}

function billio_get_breadcrumbs_from_menu_by_menuid($menu_id,&$breadcrumbs,$args) {
  $primary = get_nav_menu_locations();

  if (isset($primary['primary'])) {
    $navs = wp_get_nav_menu_items($primary['primary']);

    foreach ($navs as $nav) {
      if (($nav->ID)==$menu_id) {

        if ($nav->menu_item_parent!=0) {
          //recursive by menu parent
          billio_get_breadcrumbs_from_menu_by_menuid($nav->menu_item_parent,$breadcrumbs,$args);
        }

        if ( ($nav->url=='#MegaMenuColumn') or ($nav->url=='#MegaMenuHeading') or ($nav->url=='#MegaMenuContent') ) {
          break;
        }

        array_push($breadcrumbs, sprintf($args['format'],"",'<a href="'.$nav->url.'" title="'.$nav->title.'">'.$nav->title .'</a>'));

        break;
      }
    } 
  } 
}


function billio_remove_blog_slug( $wp_rewrite ) {
  if ( ! is_multisite() )
    return;
  // check multisite and main site
  if ( ! is_main_site() )
    return;

  // set checkup
  $rewrite = FALSE;

  // update_option
  $wp_rewrite->permalink_structure = preg_replace( '!^(/)?blog/!', '$1', $wp_rewrite->permalink_structure );
  update_option( 'permalink_structure', $wp_rewrite->permalink_structure );

  // update the rest of the rewrite setup
  $wp_rewrite->author_structure = preg_replace( '!^(/)?blog/!', '$1', $wp_rewrite->author_structure );
  $wp_rewrite->date_structure = preg_replace( '!^(/)?blog/!', '$1', $wp_rewrite->date_structure );
  $wp_rewrite->front = preg_replace( '!^(/)?blog/!', '$1', $wp_rewrite->front );

  // walk through the rules
  $new_rules = array();
  foreach ( $wp_rewrite->rules as $key => $rule )
    $new_rules[ preg_replace( '!^(/)?blog/!', '$1', $key ) ] = $rule;
  $wp_rewrite->rules = $new_rules;

  // walk through the extra_rules
  $new_rules = array();
  foreach ( $wp_rewrite->extra_rules as $key => $rule )
    $new_rules[ preg_replace( '!^(/)?blog/!', '$1', $key ) ] = $rule;
  $wp_rewrite->extra_rules = $new_rules;

  // walk through the extra_rules_top
  $new_rules = array();
  foreach ( $wp_rewrite->extra_rules_top as $key => $rule )
    $new_rules[ preg_replace( '!^(/)?blog/!', '$1', $key ) ] = $rule;
  $wp_rewrite->extra_rules_top = $new_rules;

  // walk through the extra_permastructs
  $new_structs = array();
  foreach ( $wp_rewrite->extra_permastructs as $extra_permastruct => $struct ) {
    $struct[ 'struct' ] = preg_replace( '!^(/)?blog/!', '$1', $struct[ 'struct' ] );
    $new_structs[ $extra_permastruct ] = $struct;
  }
  $wp_rewrite->extra_permastructs = $new_structs;
} 

add_action( 'generate_rewrite_rules', 'billio_remove_blog_slug' );


function billio_woocommerce_product_settings($settings=array()){


  if(is_array($settings) && count($settings)){

    $newsettings=array();

    foreach ($settings as $key => $setting) {

      array_push($newsettings, $setting);
      if('woocommerce_shop_page_id'==$setting['id']){

                array_push($newsettings,
                      
                        array(
                        'title'    => __( 'Product Display Columns', 'billio' ),
                        'desc'     => __( 'This controls num column product display', 'billio' ),
                        'id'       => 'loop_shop_columns',
                        'class'    => 'wc-enhanced-select',
                        'css'      => 'min-width:300px;',
                        'default'  => '4',
                        'type'     => 'select',
                        'options'  => array(
                          '2' => __( 'Two Columns', 'billio' ),
                          '3' => __( 'Three Columns', 'billio' ),
                          '4' => __( 'Four Columns', 'billio' ),
                          '5' => __( 'Five Columns', 'billio' ),
                        ),
                        'desc_tip' =>  true,
                      )
                );
      }


    }

    return $newsettings;

  }

  return $settings;
}

add_filter( 'loop_shop_columns',create_function('$column','return ($col=get_option(\'loop_shop_columns\'))?$col:$column;'));
add_filter('woocommerce_product_settings','billio_woocommerce_product_settings');
?>