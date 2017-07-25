<?php
defined('ABSPATH') or die();

/*
Plugin Name: Detheme Post
Plugin URI: http://www.detheme.com/
Description: Detheme Custom post
Version: 1.0.5
Author: detheme.com
Author URI: http://www.detheme.com/
*/

class detheme_custompost{

    function init(){


    load_plugin_textdomain('detheme_post', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    $admin      = get_role('administrator');
    $admin-> add_cap( 'detheme_setting' );


    $dtpost_settings_default=array(
            'labels' => array(
                'name' => __('Detheme Posts', 'detheme_post'),
                'singular_name' => __('dtpost', 'detheme_post'),
                'add_new' => __('Add New', 'detheme_post')
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'rewrite' => array(
                'slug' => 'dt_post',
                'with_front' => false
            ),
            'has_archive'=>true,
            'taxonomies'=>array('post_tag'),
            'hierarchical' => true,
            'menu_position' => 5,
            'supports' => array(
                'title',
                'comments',
                'page-attributes',
                'custom-fields',
                'editor',
                'excerpt',
                'thumbnail'
            )
    );

    $dtpost_settings=get_option('dt_post_settings');
    if(!$dtpost_settings){
        update_option('dt_post_settings',$dtpost_settings_default);

    }else{
        $dtpost_settings=wp_parse_args($dtpost_settings,$dtpost_settings_default);
        $dtpost_settings['labels']['add_new']=$dtpost_settings_default['labels']['add_new'];
    }



    if(wp_verify_nonce( isset($_POST['dtpost-setting'])?$_POST['dtpost-setting']:"", 'dtpost-setting')){

         $dtpost_name=(isset($_POST['dtpost_name']))?$_POST['dtpost_name']:'';
         $singular_name=(isset($_POST['singular_name']))?$_POST['singular_name']:'';
         $rewrite_slug=(isset($_POST['dtpost_slug']))?$_POST['dtpost_slug']:'';

         $do_update=false;

         if($dtpost_name!=$dtpost_settings['labels']['name'] && ''!=$dtpost_name){
            $dtpost_settings['labels']['name']=$dtpost_name;
            $do_update=true;
         }

         if($singular_name!=$dtpost_settings['labels']['singular_name'] && ''!=$singular_name){
            $dtpost_settings['labels']['singular_name']=$singular_name;
            $do_update=true;
           
         }

         if($rewrite_slug!=$dtpost_settings['rewrite']['slug'] && ''!=$rewrite_slug){
            $dtpost_settings['rewrite']['slug']=$rewrite_slug;
            $do_update=true;
           
         }

         if($do_update){
             update_option('dt_post_settings',$dtpost_settings);
         }

    }
    register_post_type('dtpost', $dtpost_settings);
    register_taxonomy('dtpostcat', 'dtpost', array('hierarchical' => true, 'label' => sprintf(__('%s Category', 'detheme_post'),ucwords($dtpost_settings['labels']['singular_name'])), 'singular_name' => __('Category')));

    add_filter("manage_edit-dtpost_columns", array($this,'show_dtpost_column'));
    add_action("manage_dtpost_posts_custom_column", array($this,"port_custom_columns"));
    add_action('template_redirect', array($this, 'loadTemplate'),100);
    add_action('admin_menu', array($this,'register_submenu_page'));

    }

    function register_submenu_page(){

    add_submenu_page( 'edit.php?post_type=dtpost', __('Detheme Post Settings', 'detheme_post'), __('Settings', 'detheme_post'),'detheme_setting','dtpost-setting', array($this,'custom_post_setting'));
    }

    function custom_post_setting(){


    $dtpost_settings=get_option('dt_post_settings');

    $args = array( 'page' => 'dtpost-setting');
    $url = add_query_arg( $args, admin_url( 'admin.php' ));

    $dtpost_name=$dtpost_settings['labels']['name'];
    $singular_name=$dtpost_settings['labels']['singular_name'];
    $slug=$dtpost_settings['rewrite']['slug'];
?>
<div class="dtpost-panel">
<h2><?php printf(__('%s Settings', 'detheme_post'),ucwords($dtpost_name));?></h2>
<form method="post" action="<?php print esc_url($url);?>">
<?php wp_nonce_field( 'dtpost-setting','dtpost-setting');?>
<input name="option_page" value="reading" type="hidden"><input name="action" value="update" type="hidden">
<table class="form-table">
<tbody>
<tr>
<th scope="row"><label for="dtpost_name"><?php _e('Label Name','detheme_post');?></label></th>
<td>
<input name="dtpost_name" id="dtpost_name" max-length="50" value="<?php print $dtpost_name;?>" class="" type="text"></td>
</tr>
<tr>
<th scope="row"><label for="singular_name"><?php _e('Singular Name','detheme_post');?></label></th>
<td>
<input name="singular_name" id="singular_name" max-length="50" value="<?php print $singular_name;?>" class="" type="text"></td>
</tr>
<tr>
<th scope="row"><label for="dtpost_slug"><?php _e('Rewrite Slug','detheme_post');?></label></th>
<td>
<input name="dtpost_slug" id="dtpost_slug" max-length="50" value="<?php print $slug;?>" class="" type="text"></td>
</tr>
</tbody></table>

<p class="submit"><input name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes');?>" type="submit"></p></form>
</div>
<?php
    }
    function show_dtpost_column($columns)
    {

        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "image" => __("Image", 'detheme_post'),
            "title" => __("Title", 'detheme_post'),
            "author" => __("Author", 'detheme_post'),
            "dtpost-category" => __("Categories", 'detheme_post'),
            "date" => __("Date", 'detheme_post'));
        return $columns;
    }

    function port_custom_columns($column)

    {

        global $post;
        switch ($column) {
            case "dtpost-category":
                echo get_the_term_list($post->ID, 'dtpostcat', '', ', ', '');
                break;
            case "image":
                $attachment_id=get_the_post_thumbnail($post->ID,'thumbnail');
                print ($attachment_id)?$attachment_id:"";
                break;
        }
    }


    function loadTemplate(){

        global $post;

        if(!isset($post))
            return true;

        $standard_type=$post->post_type;
        $templateName=false;

        if(is_single() && $standard_type == 'dtpost') {
           $templateName='dtpost';
        }
        else{
            return true;
        }


        if ( $templateName ) {
            $template = locate_template( array( "{$templateName}.php","detheme-post/{$templateName}.php","templates/{$templateName}.php" ),false );
        }

        // Get default slug-name.php
        if ( ! $template && $templateName && file_exists( plugin_dir_path(__FILE__). "/templates/{$templateName}.php" ) ) {

            $template = plugin_dir_path(__FILE__). "templates/{$templateName}.php";
        }

        // Allow 3rd party plugin filter template file from their plugin
        $template = apply_filters( 'detheme_post_get_template_part', $template,$templateName );

        if ( $template ) {

            load_template( $template, false );
            exit;
        }

    }

}
add_action('init', array(new detheme_custompost(),'init'),1);
?>