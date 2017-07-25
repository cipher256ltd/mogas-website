<?php
defined('ABSPATH') or die();

add_action( 'save_post', 'save_detheme_metaboxes' );
function save_detheme_metaboxes($post_id){

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    if(!wp_verify_nonce( isset($_POST['detheme_page_metaboxes'])?sanitize_text_field($_POST['detheme_page_metaboxes']):"", 'detheme_page_metaboxes'))
    return;

     $old = get_post_meta( $post_id, '_sidebar_position', true );
     $new = (isset($_POST['_sidebar_position']))?sanitize_text_field($_POST['_sidebar_position']):'';
     
     update_post_meta( $post_id, '_sidebar_position', $new,$old );

     $old = get_post_meta( $post_id, '_hide_title', true );
     $new = (isset($_POST['hide_title']))?sanitize_text_field($_POST['hide_title']):'';

     update_post_meta( $post_id, '_hide_title', $new,$old );

     $old = get_post_meta( $post_id, '_hide_loader', true );
     $new = (isset($_POST['hide_loader']))?sanitize_text_field($_POST['hide_loader']):'';

     update_post_meta( $post_id, '_hide_loader', $new,$old );

     $old = get_post_meta( $post_id, '_hide_banner', true );
     $new = (isset($_POST['hide_banner']))?sanitize_text_field($_POST['hide_banner']):'';

     update_post_meta( $post_id, '_hide_banner', $new,$old );

     if('page'==get_post_type()){

       $old = get_post_meta( $post_id, '_background_style', true );
       $new = (isset($_POST['background_style']))?sanitize_text_field($_POST['background_style']):'';

       update_post_meta( $post_id, '_background_style', $new,$old );

       $old = get_post_meta( $post_id, '_page_background', true );
       $new = (isset($_POST['page_background']))?sanitize_text_field($_POST['page_background']):'';

       update_post_meta( $post_id, '_page_background', $new,$old );
    }


     if(isset($_POST['page_banner'])){

       $old = get_post_meta( $post_id, '_page_banner', true );
       $new = sanitize_text_field($_POST['page_banner']);
       update_post_meta( $post_id, '_page_banner', $new,$old );
     }    
}


function dt_page_metaboxes() {

  $defaultpost=array(
    'page'=>__('Page Attributes','billio'),
    'post'=>__('Page Attributes','billio'),
    'port'=>__('Page Attributes','billio'),
    'product'=>__('Page Attributes','billio')
  );

  $posttypes=apply_filters('dt_page_metaboxes',$defaultpost);

  if(count($posttypes)){
    foreach ($posttypes as $posttype => $title) {
      remove_meta_box('pageparentdiv', $posttype,'side');
      add_meta_box('dtpageparentdiv',  ($title==""?__('Page Attributes'):$title), 'dt_page_attributes_meta_box', $posttype, 'side', 'core');
    }

  }
}


add_action( 'admin_menu' , 'dt_page_metaboxes');

function dt_page_attributes_meta_box($post) {

  wp_register_script('detheme-media',get_template_directory_uri() . '/lib/js/media.min.js', array('jquery','media-views','media-editor'),'',true);
  wp_enqueue_script('detheme-media');

  wp_localize_script( 'detheme-media', 'dtb_i18nLocale', array(
      'select_image'=>__('Select Image','billio'),
      'insert_image'=>__('Insert Image','billio'),
  ));

  wp_nonce_field( 'detheme_page_metaboxes','detheme_page_metaboxes');

  do_action('dt_page_attribute_metaboxes',$post);
  do_action('after_dt_page_attribute');
}


function dt_page_attribute_post_parent($post){

  $post_type_object = get_post_type_object($post->post_type);
  if ( $post_type_object->hierarchical ) {

    $dropdown_args = array(
      'post_type'        => $post->post_type,
      'exclude_tree'     => $post->ID,
      'selected'         => $post->post_parent,
      'name'             => 'parent_id',
      'show_option_none' => __('(no parent)'),
      'sort_column'      => 'menu_order, post_title',
      'echo'             => 0,
    );

    $dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $dropdown_args, $post );
    $pages = wp_dropdown_pages( $dropdown_args );

  if ( ! empty($pages) ) {?>
<p><strong><?php _e('Parent') ?></strong></p>
<label class="screen-reader-text" for="parent_id"><?php _e('Parent') ?></label>
<?php echo $pages; 
    } // end empty pages check
  } // end hierarchical check.

}

function dt_page_attribute_checkbox($post){

  global $detheme_config;

?>
<?php if ( 'product' != $post->post_type):?>
<p><input type="checkbox" name="hide_title" id="hide_title" value="1" <?php echo ($post->_hide_title)?'checked="checked"':""?>/> <?php _e('Hide title','billio') ?></strong></p>
<?php endif;?>
<?php if ( 'page' == $post->post_type && $detheme_config['show-banner-area'] ):?>
<p><input type="checkbox" name="hide_banner" id="hide_banner" value="1" <?php echo ($post->_hide_banner)?'checked="checked"':""?>/> <?php _e('Hide banner','billio') ?></strong></p>

<script type="text/javascript">
jQuery(document).ready(function($) {
  'use strict'; 

  var hide_banner=$('#hide_banner');
    
  if(hide_banner.length){
    hide_banner.on('change',function(){
      if(hide_banner.prop('checked')){
        $('.page-banner').hide();
      }
      else{
        $('.page-banner').show();
      }

    })
    .trigger('change');
  }
});

</script>
<?php endif;?>
<?php if ( 'page' == $post->post_type && $detheme_config['page_loader'] ):?>
<p><input type="checkbox" name="hide_loader" id="hide_loader" value="1" <?php echo ($post->_hide_loader)?'checked="checked"':""?>/> <?php _e('Disable Page Loader','billio') ?></strong></p>
<?php 
endif;

}

function dt_page_attribute_page_template($post){

  if ( 'page' != $post->post_type )
      return true;

  $template = !empty($post->page_template) ? $post->page_template : false;
  $templates = get_page_templates();
  $sidebar_position=array('sidebar-left'=>__('Left','billio'),'sidebar-right'=>__('Right','billio'),'nosidebar'=>__('No Sidebar','billio'));

  ksort( $templates );
   ?>
<p><strong><?php _e('Template','billio') ?></strong></p>
<label class="screen-reader-text" for="page_template"><?php _e('Page Template','billio'); ?></label><select name="page_template" id="page_template">
<option value='default'><?php _e('Default Template','billio'); ?></option>
<?php 

if(count($templates)):

foreach (array_keys( $templates ) as $tmpl )
    : if ( $template == $templates[$tmpl] )
      $selected = " selected='selected'";
    else
      $selected = '';
  echo "\n\t<option value='".$templates[$tmpl]."' $selected>".__($tmpl,'billio')."</option>";
  endforeach;
  endif;?>
 ?>
</select>
<div id="custommeta">
<p id="sidebar_option">
  <strong><?php _e('Sidebar Position','billio') ?></strong>&nbsp;
<select name="_sidebar_position" id="sidebar_position">
<option value='default'><?php _e('Default','billio'); ?></option>
<?php foreach ($sidebar_position as $position=>$label) {
  print "<option value='".$position."'".(($post->_sidebar_position==$position)?" selected":"").">".ucwords($label)."</option>";

}?>
</select>
</p>
</div>
<p><strong><?php _e('Order'); ?></strong></p>
<p><label class="screen-reader-text" for="menu_order"><?php _e('Order'); ?></label><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order); ?>" /></p>
<p><?php _e( 'Need help? Use the Help tab in the upper right of your screen.'); ?></p>
<script type="text/javascript">
jQuery(document).ready(function($) {
  'use strict'; 

  var $select = $('select#page_template'),$custommeta = $('#custommeta'),$background_style=$('#background_style');
    
  $select.live('change',function(){
    var this_value = $(this).val();
    switch ( this_value ) {
      case 'squeeze.php':
            $custommeta.find('#sidebar_option').fadeOut('slow');
        break;
      case 'fullwidth.php':
            $custommeta.find('#sidebar_option').fadeOut('slow');
        break;
      default:
         $custommeta.find('#sidebar_option').fadeIn('slow');
    }

  });
  $select.trigger('change');
});
</script>
<?php  
}

function dt_page_attribute_page_background($post){

  if ( 'page' != $post->post_type)
  return true;

  $background_image=get_post_meta($post->ID, '_page_background', true);
  $background_style=get_post_meta( $post->ID, '_background_style', true );
  $image="";
  $styles = array(
      __("Cover", 'wpb') => 'cover',
      __("Cover All", 'wpb') => 'cover_all',
      __('Contain', 'wpb') => 'contain',
      __('No Repeat', 'wpb') => 'no-repeat',
      __('Repeat', 'wpb') => 'repeat',
      __("Parallax", 'billio') => 'parallax',
      __("Parallax All", 'billio') => 'parallax_all',
      __("Fixed", 'billio') => 'fixed',
    );

  if($background_image){

    $image = wp_get_attachment_image( $background_image, array( 266,266 ));
  }

  ?>
<div class="detheme-field-image page-background">
  <p><strong><?php _e('Background Image','billio');?></strong>
  <input type="hidden" name="page_background" value="<?php print $background_image;?>" />
  <p class="preview-image">
  <a title="<?php _e('Set background image','billio');?>" href="#" id="set-page-background" class="add_detheme_image"><?php echo (""!==$image)?$image:__('Set background image','billio');?></a>
  </p>
  <a title="<?php _e('Remove background image','billio');?>" style="display:<?php echo (""==$image)?"none":"block";?>" href="#" id="clear-page-background" class="remove_detheme_image"><?php _e('Remove background image','billio');?></a>
</div>
 <div  id="background_style"><strong><?php _e('Background Style','billio');?></strong>&nbsp;
  <select name="background_style">
  <option value="default"><?php _e('Default','billio');?></option>
  <?php 
  foreach ($styles as $name=>$style) {
    print "<option value='".$style."'".(($background_style==$style)?" selected":"").">".ucwords($name)."</option>";

  }
  ?>
  </select>
</div>
<?php   
}

function dt_page_attribute_page_banner($post){

  global $detheme_config;

  if($detheme_config['dt-show-banner-page']!='featured' || !$detheme_config['show-banner-area'])
    return true;

  $banner_image=get_post_meta($post->ID, '_page_banner', true);
  $banner_image_url="";

  if($banner_image){

    $banner_image_url = wp_get_attachment_image( $banner_image, array( 266,266 ));
  }
?>
<div class="detheme-field-image page-banner">
  <p><strong><?php _e('Banner Image','billio');?></strong>
  <input type="hidden" name="page_banner" value="<?php print $banner_image;?>" />
  <p class="preview-image">
  <a title="<?php _e('Set Page Banner','billio');?>" href="#" id="set-page-banner" class="add_detheme_image"><?php echo (""!==$banner_image_url)?$banner_image_url:__('Set Page Banner','billio','billio');?></a>
  </p>
  <a title="<?php _e('Remove Page Banner','billio');?>" style="display:<?php echo (""==$banner_image_url)?"none":"block";?>" href="#" id="clear-page-banner" class="remove_detheme_image"><?php _e('Remove Page Banner','billio');?></a>
</div>
<?php
}

add_action ('dt_page_attribute_metaboxes','dt_page_attribute_checkbox');
add_action ('dt_page_attribute_metaboxes','dt_page_attribute_post_parent');
add_action ('dt_page_attribute_metaboxes','dt_page_attribute_page_template');
add_action ('dt_page_attribute_metaboxes','dt_page_attribute_page_banner');
add_action ('dt_page_attribute_metaboxes','dt_page_attribute_page_background');
?>