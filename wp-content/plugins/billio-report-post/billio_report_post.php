<?php
defined('ABSPATH') or die();

/*
Plugin Name: Billio Report Post
Plugin URI: http://www.detheme.com/
Description: Billio Report Post
Version: 1.0.0
Author: detheme.com
Author URI: http://www.detheme.com/
*/


class billio_report_post{

    private $templates;

    function init(){
        load_plugin_textdomain('billio_report_post', false, dirname(plugin_basename(__FILE__)) . '/languages/');

        $admin = get_role('administrator');
        $admin-> add_cap( 'detheme_setting' );

        $report_settings_default=array(
                'labels' => array(
                    'name' => __('Reports', 'billio_report_post'),
                    'singular_name' => __('Report', 'billio_report_post'),
                    'add_new' => __('Add New', 'billio_report_post')
                ),
                'public' => true,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'rewrite' => array(
                    'slug' => 'company-report',
                    'with_front' => false
                ),
                'has_archive'=>true,
                'taxonomies'=>array('post_tag,dtreportpostcat'),
                'hierarchical' => true,
                'menu_position' => 5,
                'supports' => array(
                    'title',
                    'page-attributes',
                    'editor',
                    'thumbnail'
                )
        );


        $report_settings=get_option('dt_report_post_settings');
        if(!$report_settings){
            update_option('dt_report_post_settings',$report_settings_default);

        }else{
            $report_settings=wp_parse_args($report_settings,$report_settings_default);
            $report_settings['labels']['add_new']=$report_settings_default['labels']['add_new'];
        }

        if(wp_verify_nonce( isset($_POST['dtreportpost-setting'])?$_POST['dtreportpost-setting']:"", 'dtreportpost-setting')){

             $dtpost_name=(isset($_POST['dtpost_name']))?$_POST['dtpost_name']:'';
             $singular_name=(isset($_POST['singular_name']))?$_POST['singular_name']:'';
             $rewrite_slug=(isset($_POST['dtpost_slug']))?$_POST['dtpost_slug']:'';

             $do_update=false;

             if($dtpost_name!=$report_settings['labels']['name'] && ''!=$dtpost_name){
                $report_settings['labels']['name']=$dtpost_name;
                $do_update=true;
             }

             if($singular_name!=$report_settings['labels']['singular_name'] && ''!=$singular_name){
                $report_settings['labels']['singular_name']=$singular_name;
                $do_update=true;
               
             }

             if($rewrite_slug!=$report_settings['rewrite']['slug'] && ''!=$rewrite_slug){
                $report_settings['rewrite']['slug']=$rewrite_slug;
                $do_update=true;
               
             }

             if($do_update){
                 update_option('dt_report_post_settings',$report_settings);
             }

        }

        register_post_type('dtreportpost', $report_settings);
        register_taxonomy('dtreportpostcat', 'dtreportpost', array('hierarchical' => true, 'label' => sprintf(__('%s Category', 'billio_report_post'),ucwords($report_settings['labels']['singular_name'])), 'singular_name' => __('Category')));

        add_filter('manage_dtreportpost_posts_columns', array($this,'show_report_column'));
        add_action('manage_dtreportpost_posts_custom_column', array($this,'report_custom_columns'), 10 ,2);

        add_action('template_redirect', array($this, 'loadTemplate'));
        add_action('add_meta_boxes',array($this,'dt_report_add_meta_box'));
        add_action('save_post',array($this,'dt_report_save_meta_box'));

        // Icon font
        wp_register_style( 'billio_report_post_icons', plugins_url('/css/flaticon.css', __FILE__) );
        wp_enqueue_style('billio_report_post_icons');

        add_action('wp_enqueue_scripts', array($this,'load_frontend_css_style' ));
        add_action('admin_enqueue_scripts', array($this,'load_backend_css_style' ));

        wp_enqueue_script( 'billio_report_post_js' , plugins_url('/js/report.js', __FILE__), array( 'jquery'), '1.0', false );

        add_shortcode( 'dt_report_list', array($this,'get_dt_report_list'));
        add_action('dt_report_load_pagination', array($this,'get_dt_report_load_pagination'));

        add_action('dt_report_before_report_item', array($this,'do_dt_report_before_report_item'));
        add_action('dt_report_after_report_item', array($this,'do_dt_report_after_report_item'));


        $this->templates = array(
            "before-report-item" => plugin_dir_path(__FILE__). "/templates/before-report-item.php",
            "after-report-item" => plugin_dir_path(__FILE__). "/templates/after-report-item.php",
            "pagination" => plugin_dir_path(__FILE__). "/templates/pagination.php",
            "loop-start" => plugin_dir_path(__FILE__). "/templates/loop-start.php",
            "loop-end" => plugin_dir_path(__FILE__). "/templates/loop-end.php",
            "content-report" => plugin_dir_path(__FILE__). "/templates/content-report.php"
        );

        $this->set_custom_template_path();

        add_action('admin_menu', array($this,'register_submenu_page'));
    
        if (function_exists('vc_set_as_theme')) {
            //define('DETHEME_VC_BASENAME',dirname(plugin_basename(__FILE__)));
            //define('DETHEME_VC_DIR',plugin_dir_path(__FILE__));
            //define('DETHEME_VC_DIR_URL',plugin_dir_url(__FILE__));


            if(version_compare(WPB_VC_VERSION,"4.2.3",'<')){
              //require_once(plugin_dir_path(__FILE__)."lib/map.old.php");
              require_once(plugin_dir_path(__FILE__)."lib/map.php");
            } else{
              require_once(plugin_dir_path(__FILE__)."lib/map.php");
            }

            require_once(plugin_dir_path(__FILE__)."lib/shortcode.php");
            add_filter( 'plugin_row_meta', array($this,'vc_compatible_version'),1,4);//
        } //if (function_exists('vc_set_as_theme'))
    } //function init()

    function vc_compatible_version($plugin_meta, $plugin_file, $plugin_data, $status){

      if('js_composer/js_composer.php'!=$plugin_file)
        return $plugin_meta;
      $plugin_meta=array();

      if ( !empty( $plugin_data['Version'] ) )
        $plugin_meta[] = sprintf( __( 'Version %s' ), $plugin_data['Version'] );
      if ( !empty( $plugin_data['Author'] ) ) {
        $plugin_meta[] = sprintf( __( 'By %s' ), $plugin_data['Author'] );
      }


      return $plugin_meta;
    }

    function load_frontend_css_style() {
        wp_register_style( 'billio_report_post_style', plugins_url('/css/style.css', __FILE__) );
        wp_enqueue_style('billio_report_post_style');
    }

    function load_backend_css_style() {
        wp_register_style( 'billio_report_backend_post_style', plugins_url('/css/backend.css', __FILE__) );
        wp_enqueue_style('billio_report_backend_post_style');
    }

    function register_submenu_page(){
        add_submenu_page('edit.php?post_type=dtreportpost', __('Billio Report Post Settings', 'billio_report_post'), __('Settings', 'billio_report_post'),'detheme_setting','dtreportpost-setting', array($this,'custom_post_setting'));
    }

    function custom_post_setting(){
        $dtreportpost_settings=get_option('dt_report_post_settings');

        $args = array( 'page' => 'dtreportpost-setting');
        $url = add_query_arg( $args, admin_url( 'admin.php' ));

        $dtpost_name=$dtreportpost_settings['labels']['name'];
        $singular_name=$dtreportpost_settings['labels']['singular_name'];
        $slug=$dtreportpost_settings['rewrite']['slug'];
        ?>
        <div class="dtpost-panel">
        <h2><?php printf(__('%s Settings', 'billio_report_post'),ucwords($dtpost_name));?></h2>
        <form method="post" action="<?php print esc_url($url);?>">
        <?php wp_nonce_field( 'dtreportpost-setting','dtreportpost-setting');?>
        <input name="option_page" value="reading" type="hidden"><input name="action" value="update" type="hidden">
        <table class="form-table">
        <tbody>
        <tr>
        <th scope="row"><label for="dtpost_name"><?php _e('Label Name','billio_report_post');?></label></th>
        <td>
        <input name="dtpost_name" id="dtpost_name" max-length="50" value="<?php print $dtpost_name;?>" class="" type="text"></td>
        </tr>
        <tr>
        <th scope="row"><label for="singular_name"><?php _e('Singular Name','billio_report_post');?></label></th>
        <td>
        <input name="singular_name" id="singular_name" max-length="50" value="<?php print $singular_name;?>" class="" type="text"></td>
        </tr>
        <tr>
        <th scope="row"><label for="dtpost_slug"><?php _e('Rewrite Slug','billio_report_post');?></label></th>
        <td>
        <input name="dtpost_slug" id="dtpost_slug" max-length="50" value="<?php print $slug;?>" class="" type="text"></td>
        </tr>
        </tbody></table>


        <p class="submit"><input name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes');?>" type="submit"></p></form>
        </div>
<?php
    } //function custom_post_setting

    function do_dt_report_before_report_item($report_query) {
        load_template($this->templates['before-report-item'],true);
    }

    function do_dt_report_after_report_item($report_query) {
        load_template($this->templates['after-report-item'],true);
    }

    function get_dt_report_load_pagination() {
        load_template($this->templates['pagination'],true);
    }

    function get_dt_report_list( $atts ) {
        global $dt_report_query;

        extract( shortcode_atts( array(
            'report_cat_name' => '',
            'tag'           => '',
            'meta_key'      => 'dt_report_pre_title',
            'orderby'       => 'meta_value',
            'order'         => 'DESC',
            'columns'       => '',
            'posts_per_page'=> 10,
            'dtreportpostcat' => ''
        ), $atts ) );

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $args = array( 
            'post_type'     => 'dtreportpost', 
            'tag'           => $tag,
            'meta_key'      => $meta_key,
            'orderby'       => $orderby,
            'order'         => $order,
            'posts_per_page'=> $posts_per_page,
            'paged'         => $paged,
            'dtreportpostcat' => $dtreportpostcat
        );
        

        $dt_report_query = new WP_Query($args);
        $return = '';


        ob_start();
        load_template($this->templates['loop-start'],false);
        while ( $dt_report_query->have_posts() ) : $dt_report_query->the_post();
            load_template($this->templates['content-report'],false);
        endwhile;
        load_template($this->templates['loop-end'],false);
        $return = ob_get_contents();
        ob_end_clean();

        return $return;

    }

    function set_custom_template_path() {
        if( file_exists(get_template_directory()."/billio-report-post/before-report-item.php") ) {
            $this->templates['before-report-item'] = get_template_directory()."/billio-report-post/before-report-item.php";
        }
        
        if( file_exists(get_template_directory()."/billio-report-post/after-report-item.php") ) {
            $this->templates['after-report-item'] = get_template_directory()."/billio-report-post/after-report-item.php";
        }

        if( file_exists(get_template_directory()."/billio-report-post/pagination.php") ) {
            $this->templates['pagination'] = get_template_directory()."/billio-report-post/pagination.php";
        }

        if( file_exists(get_template_directory()."/billio-report-post/loop-start.php") ) {
            $this->templates['loop-start'] = get_template_directory()."/billio-report-post/loop-start.php";
        }

        if( file_exists(get_template_directory()."/billio-report-post/loop-end.php") ) {
            $this->templates['loop-end'] = get_template_directory()."/billio-report-post/loop-end.php";
        }

        if( file_exists(get_template_directory()."/billio-report-post/content-report.php") ) {
            $this->templates['content-report'] = get_template_directory()."/billio-report-post/content-report.php";
        }
    }

    function get_report_icon($doc_ext) {
        $document_icon = '';

        switch($doc_ext) {
            case 'ai':
                $document_icon = "flaticondr-ai1";
                break;
            case 'docx':
                $document_icon = "flaticondr-docx1";
                break;
            case 'html':
                $document_icon = "flaticondr-html8";
                break;
            case 'jpg':
                $document_icon = "flaticondr-jpg2";
                break;
            case 'jpeg':
                $document_icon = "flaticondr-jpg2";
                break;
            case 'mp3':
                $document_icon = "flaticondr-mp34";
                break;
            case 'mp4':
                $document_icon = "flaticondr-mp42";
                break;
            case 'pdf':
                $document_icon = "flaticondr-pdf17";
                break;
            case 'psd':
                $document_icon = "flaticondr-photoshop";
                break;
            case 'png':
                $document_icon = "flaticondr-png4";
                break;
            case 'ppt':
                $document_icon = "flaticondr-ppt2";
                break;
            case 'pptx':
                $document_icon = "flaticondr-pptx";
                break;
            case 'rar':
                $document_icon = "flaticondr-rar";
                break;
            case 'txt':
                $document_icon = "flaticondr-txt";
                break;
            case 'xls':
                $document_icon = "flaticondr-xls2";
                break;
            case 'xlsx':
                $document_icon = "flaticondr-xlsx1";
                break;
            case 'xml':
                $document_icon = "flaticondr-xml6";
                break;
            case 'zip':
                $document_icon = "flaticondr-zip5";
                break;
            case '':
                $document_icon = "";
                break;
            default:
                $document_icon = "flaticondr-doc";
        }

        return $document_icon;
    }

    function report_custom_columns( $column, $post_id ) {
        switch ($column) {
            case 'pre_title' :
                echo get_post_meta( $post_id , 'dt_report_pre_title' , true ); 
                break;
            case 'document' :
                $document_url       = get_post_meta( $post_id, 'dt_report_document_url', true );
                $document_extension = get_post_meta( $post_id, 'dt_report_document_extension', true );
                $document_filename  = pathinfo($document_url, PATHINFO_FILENAME);
                if (!empty($document_url)) {
                    echo '<a href="'.esc_url($document_url).'" target="_blank">'.esc_html($document_filename.'.'.$document_extension).'</a>';     
                }
                break;
            case 'report_category' :
                $terms = get_the_term_list($post_id , 'dtreportpostcat');
                if (is_string($terms )) echo $terms;
                break;
        }
    }

    function show_report_column($columns)
    {
        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => __("Title", 'billio_report_post'),
            "pre_title" => __("Pre Title", 'billio_report_post'),
            "document" => __("Document", 'billio_report_post'),
            "author" => __("Author", 'billio_report_post'),
            "report_category" => __("Categories", 'billio_report_post'),
            "date" => __("Date", 'billio_report_post'));
        return $columns;
    }

    function loadTemplate(){
        global $post;


        if(!isset($post))
            return true;

        $standard_type=$post->post_type;

        if($standard_type == 'dtreportpost') {
            $templateName='dtreport_post';    
        } else {
            return true;
        }

        if ( $templateName ) {
            $template = locate_template( array( "{$templateName}.php", "templates/detheme/{$templateName}.php" ),false );
        }


        // Get default slug-name.php
        if ( ! $template && $templateName && file_exists( plugin_dir_path(__FILE__). "/templates/{$templateName}.php" ) ) {
            $template = locate_template(plugin_dir_path(__FILE__). "/templates/{$templateName}.php",false);
        }

        // Allow 3rd party plugin filter template file from their plugin
        $template = apply_filters( 'detheme_get_template_part', $template,$templateName );

        if ( $template ) {
            load_template( $template, false );
            exit;
        }

    }

    function dt_report_add_meta_box() {

        $screens = array( 'dtreportpost' );

        foreach ( $screens as $screen ) {
            add_meta_box(
                'report_sectionid',
                __( 'Document Options', 'billio_report_post' ),
                array($this,'report_meta_box_callback'),
                $screen
            );
        }
    }


    function report_meta_box_callback( $post ) {
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'dt_report_meta_box', 'dt_report_meta_box_nonce' );

        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        $pre_title          = get_post_meta( $post->ID, 'dt_report_pre_title', true );
        $button_label       = get_post_meta( $post->ID, 'dt_report_button_label', true );
        $document_url       = get_post_meta( $post->ID, 'dt_report_document_url', true );
        $document_extension = get_post_meta( $post->ID, 'dt_report_document_extension', true );
        $document_filename  = pathinfo($document_url, PATHINFO_FILENAME);
        $document_icon      = get_post_meta( $post->ID, 'dt_report_document_icon', true );;

?>
        <script>
        ( function( $ ) {
            $(document).ready(
                function() {
                    $('#upload_document_link').click(
                        function() {
                              tb_show('', 'media-upload.php?post_id=<?php  echo $post->ID; ?>&amp;TB_iframe=true');
                              return false;
                        }
                    );

                    window.send_to_editor = function(html) {
                        var doc_url =  $(html).attr('href');
                        var doc_filename = doc_url.substr((doc_url.lastIndexOf('/') +1) );
                        var doc_extension = doc_url.substr((doc_url.lastIndexOf('.') +1) );
                        var doc_icon = '';
                        
                        //$('#document_name').html(doc_filename);
                        $('#document_name a').attr('href',doc_url);
                        $('#document_name a span').html(doc_filename);

                        $('#document_url').val(doc_url);

                        switch(doc_extension) {
                            case 'ai':
                                doc_icon = "flaticondr-ai1";
                                break;
                            case 'docx':
                                doc_icon = "flaticondr-docx1";
                                break;
                            case 'html':
                                doc_icon = "flaticondr-html8";
                                break;
                            case 'jpg':
                                doc_icon = "flaticondr-jpg2";
                                break;
                            case 'jpeg':
                                doc_icon = "flaticondr-jpg2";
                                break;
                            case 'mp3':
                                doc_icon = "flaticondr-mp34";
                                break;
                            case 'mp4':
                                doc_icon = "flaticondr-mp42";
                                break;
                            case 'pdf':
                                doc_icon = "flaticondr-pdf17";
                                break;
                            case 'psd':
                                doc_icon = "flaticondr-photoshop";
                                break;
                            case 'png':
                                doc_icon = "flaticondr-png4";
                                break;
                            case 'ppt':
                                doc_icon = "flaticondr-ppt2";
                                break;
                            case 'pptx':
                                doc_icon = "flaticondr-pptx";
                                break;
                            case 'rar':
                                doc_icon = "flaticondr-rar";
                                break;
                            case 'txt':
                                doc_icon = "flaticondr-txt";
                                break;
                            case 'xls':
                                doc_icon = "flaticondr-xls2";
                                break;
                            case 'xlsx':
                                doc_icon = "flaticondr-xlsx1";
                                break;
                            case 'xml':
                                doc_icon = "flaticondr-xml6";
                                break;
                            case 'zip':
                                doc_icon = "flaticondr-zip5";
                                break;
                            case '':
                                doc_icon = "";
                                break;
                            default:
                                doc_icon = "flaticondr-doc";
                        }

                        $('#document_icon').attr('class','');
                        $('#document_icon').addClass(doc_icon);

                        tb_remove();
                    }

                    $('#remove_document_link').click(
                        function() {
                            $('#document_url').val('');
                            $('#document_name a').attr('href','javascript:;');
                            $('#document_name a span').html('');
                           

                           $('#document_icon').attr('class','');
                        }
                    );
                }
            );
          
        })(jQuery);
        </script>
          
        <div class="media-upload">
            <div><?php _e('Pre Title','billio_report_post'); ?></div>
            <input type="text" id="pre_title" name="pre_title" value="<?php echo esc_attr($pre_title); ?>" /><br /><br />
            
            <div><?php _e('Document File','billio_report_post' ); ?></div>
            
            <div id="document_name"><a href="<?php echo esc_url($document_url); ?>"><i id="document_icon" class="<?php echo esc_attr($document_icon); ?>"></i><br /><span><?php echo esc_html($document_filename.$document_extension); ?></span></a></div>
            
            <a href="javascript:;" id="upload_document_link"><?php _e( 'Upload Document', 'billio_report_post' ); ?></a> - <a href="javascript:;" id="remove_document_link"><?php _e( 'Remove Document', 'billio_report_post' ); ?></a>
            <input type="hidden" id="document_url" name="document_url" value="<?php echo esc_attr($document_url); ?>" /><br /><br />

            <div><?php _e( 'Download Button Label', 'billio_report_post' ); ?></div>
            <input type="text" id="button_label" name="button_label" value="<?php echo esc_attr( $button_label );?>" />
        </div>
<?php 
    } //function report_meta_box_callback

    function dt_report_save_meta_box($post_id) {
        if (!isset($_POST['dt_report_meta_box_nonce']))
            return $post_id;

        if ( !wp_verify_nonce( $_POST['dt_report_meta_box_nonce'], 'dt_report_meta_box' ) )
            return $post_id;

        if ( !current_user_can( 'edit_post', $post_id ))
            return $post_id;

        $document_extension = pathinfo($_POST['document_url'], PATHINFO_EXTENSION);

        update_post_meta($post_id,'dt_report_pre_title',$_POST['pre_title']);
        update_post_meta($post_id,'dt_report_button_label',$_POST['button_label']);
        update_post_meta($post_id,'dt_report_document_url',$_POST['document_url']);
        update_post_meta($post_id,'dt_report_document_extension',$document_extension);
        update_post_meta($post_id,'dt_report_document_icon',$this->get_report_icon($document_extension));
    } //function dt_report_save_meta_box

} //class billio_report_post

add_action('init', array(new billio_report_post(),'init'),1);


?>