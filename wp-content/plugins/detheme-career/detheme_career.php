<?php
defined('ABSPATH') or die();

/*
Plugin Name: DT Recruitment
Plugin URI: http://www.detheme.com/
Description: Simple Career
Version: 1.0.0
Author: detheme.com
Author URI: http://www.detheme.com/
Text Domain: detheme_career
Domain Path: /languages
*/


class detheme_career{

    function init(){

    load_plugin_textdomain('detheme_career', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    $admin      = get_role('administrator');
    $admin-> add_cap( 'portfolio_setting' );


    $career_settings=array(
            'labels' => array(
                'name' => __('Recruitments', 'detheme_career'),
                'singular_name' => __('Recruitment', 'detheme_career'),
                'add_new' => __('Add New', 'detheme_career')
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'rewrite' => array(
                'slug' => 'career',
                'with_front' => false
            ),
            'has_archive'=>true,
            'hierarchical' => true,
            'menu_position' => 5,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail'
            )
    );

    register_post_type('dtcareer', $career_settings);
    register_taxonomy('dtcareer_cat', 'dtcareer', array('hierarchical' => true, 'label' => sprintf(__('%s Category', 'detheme_career'),$career_settings['labels']['singular_name']), 'singular_name' => __('Category')));

//    add_filter("manage_edit-dtcareer_columns", array($this,'show_career_column'));
    add_action("manage_pages_custom_column", array($this,"career_custom_columns"));
    add_action('template_redirect', array($this, 'loadTemplate'),100);
    add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 'dtcareer',30 );
    add_action( 'save_post', array($this,'save_career_fields') );

    }

    function add_meta_boxes(){

         add_meta_box( 'dtcareerfield', __( 'Career Jobs Field', 'detheme_career' ),array($this,'post_custom_meta_box'), 'dtcareer','normal','high');
    }

    static function get_dtcareer_job_fields(){

        $default_fields=array(
            'type'=>array('label'=>__('Employment Type','detheme_career')),
            'specialist'=>array('label'=>__('Specialism','detheme_career')),
            'region'=>array('label'=>__('Region','detheme_career')),
            'location'=>array('label'=>__('Location','detheme_career')),
            'salary'=>array('label'=>__('Salary Description','detheme_career'))
            );


        return apply_filters('dtcareer_job_fields',$default_fields);
    }

    function save_career_fields($post_id){

            if ( (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || !isset($_POST['detheme_career_meta']))
             return $post_id;

             if ( ! wp_verify_nonce( $_POST['detheme_career_meta'], basename( __FILE__ ) ) )
             return $post_id;

             if($fields=detheme_career::get_dtcareer_job_fields()){

                foreach ($fields as $key => $field) {
                     $metaname="_".(is_int($key)?sanitize_key($field['label']):$key);
                     $old = get_post_meta( $post_id, $metaname, true );
                     $new = (isset($_POST[$metaname]))?$_POST[$metaname]:'';
                     update_post_meta( $post_id, $metaname, $new,$old );
                }
             }

            if ( isset( $_POST['c_mm'] ) ) {
                    $aa = $_POST['c_aa'];
                    $mm = $_POST['c_mm'];
                    $jj = $_POST['c_jj'];
                    $hh = $_POST['c_hh'];
                    $mn = $_POST['c_mn'];
                    $ss = $_POST['c_ss'];
                    $jj = ($jj > 31 ) ? 31 : $jj;
                    $hh = ($hh > 23 ) ? $hh -24 : $hh;
                    $mn = ($mn > 59 ) ? $mn -60 : $mn;
                    $ss = ($ss > 59 ) ? $ss -60 : $ss;
                    $new = "$aa-$mm-$jj $hh:$mn:$ss";

                    $old = get_post_meta( $post_id, '_career_close_date', true );
                    update_post_meta( $post_id, '_career_close_date', $new,$old );

             }

            if ( isset( $_POST['s_mm'] ) ) {
                    $aa = $_POST['s_aa'];
                    $mm = $_POST['s_mm'];
                    $jj = $_POST['s_jj'];
                    $hh = $_POST['s_hh'];
                    $mn = $_POST['s_mn'];
                    $ss = $_POST['s_ss'];
                    $jj = ($jj > 31 ) ? 31 : $jj;
                    $hh = ($hh > 23 ) ? $hh -24 : $hh;
                    $mn = ($mn > 59 ) ? $mn -60 : $mn;
                    $ss = ($ss > 59 ) ? $ss -60 : $ss;
                    $new = "$aa-$mm-$jj $hh:$mn:$ss";

                    $old = get_post_meta( $post_id, '_career_start_date', true );
                    update_post_meta( $post_id, '_career_start_date', $new,$old );

             }

    }

    function post_custom_meta_box($post) {

        global $wp_locale;


        $fields=detheme_career::get_dtcareer_job_fields();

        if(count($fields)):
    ?>
    <input type="hidden" name="detheme_career_meta" value="<?php print wp_create_nonce( basename( __FILE__ ) );?>" />
    <table border="0" cellpadding="0" width="100%">
<?php foreach ($fields as $key => $field) {

        $metaname="_".(is_int($key)?sanitize_key($field['label']):$key);
        $field_value = get_post_meta( $post->ID, $metaname, true );

        ?>
        <tr>
        <td width="200"><strong><?php _e($field['label'],'detheme_career'); ?></strong></td>
        <td><input name="<?php print $metaname;?>" type="text" id="<?php print $metaname;?>" class="widefat" value="<?php echo esc_attr( $field_value); ?>" /></td>
        </tr>
<?php };?>
        <tr>
        <td width="200"><strong><?php _e('Start Date','detheme_career'); ?></strong></td>
        <td>
<?php 

    $time_adj = current_time('timestamp');

    $start_date=get_post_meta( $post->ID, '_career_start_date', true );

    $jj = (''!=$start_date) ? mysql2date( 'd', $start_date, false ) : gmdate( 'd', $time_adj );
    $mm = (''!=$start_date) ? mysql2date( 'm', $start_date, false ) : gmdate( 'm', $time_adj );
    $aa = (''!=$start_date) ? mysql2date( 'Y', $start_date, false ) : gmdate( 'Y', $time_adj );
    $hh = (''!=$start_date) ? mysql2date( 'H', $start_date, false ) : gmdate( 'H', $time_adj );
    $mn = (''!=$start_date) ? mysql2date( 'i', $start_date, false ) : gmdate( 'i', $time_adj );
    $ss = (''!=$start_date) ? mysql2date( 's', $start_date, false ) : gmdate( 's', $time_adj );

    $cur_jj = gmdate( 'd', $time_adj );
    $cur_mm = gmdate( 'm', $time_adj );
    $cur_aa = gmdate( 'Y', $time_adj );
    $cur_hh = gmdate( 'H', $time_adj );
    $cur_mn = gmdate( 'i', $time_adj );

    $month = '<label for="mm" class="screen-reader-text">' . __( 'Month' ) . '</label><select name="s_mm"'.">\n";
    for ( $i = 1; $i < 13; $i = $i +1 ) {
        $monthnum = zeroise($i, 2);
        $month .= "\t\t\t" . '<option value="' . $monthnum . '" ' . selected( $monthnum, $mm, false ) . '>';
        /* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
        $month .= sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
    }
    $month .= '</select>';

    $day = '<label for="jj" class="screen-reader-text">' . __( 'Day' ) . '</label><input type="text" name="s_jj" value="' . $jj . '" size="2" maxlength="2" autocomplete="off" />';
    $year = '<label for="aa" class="screen-reader-text">' . __( 'Year' ) . '</label><input type="text" name="s_aa" value="' . $aa . '" size="4" maxlength="4" autocomplete="off" />';
    $hour = '<label for="hh" class="screen-reader-text">' . __( 'Hour' ) . '</label><input type="text" name="s_hh" value="' . $hh . '" size="2" maxlength="2" autocomplete="off" />';
    $minute = '<label for="mn" class="screen-reader-text">' . __( 'Minute' ) . '</label><input type="text" name="s_mn" value="' . $mn . '" size="2" maxlength="2" autocomplete="off" />';

    echo '<div class="timestamp-wrap">';
    /* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
    printf( __( '%1$s %2$s, %3$s @ %4$s : %5$s' ), $month, $day, $year, $hour, $minute );

    echo '</div><input type="hidden" name="s_ss" value="' . $ss . '" />';
    echo "\n\n";

    ?>
        </tr>
         <tr>
        <td width="200"><strong><?php _e('Close Date','detheme_career'); ?></strong></td>
        <td>
<?php 

    $time_adj = current_time('timestamp');

    $close_date=get_post_meta( $post->ID, '_career_close_date', true );

    $jj = (''!=$close_date) ? mysql2date( 'd', $close_date, false ) : gmdate( 'd', $time_adj );
    $mm = (''!=$close_date) ? mysql2date( 'm', $close_date, false ) : gmdate( 'm', $time_adj );
    $aa = (''!=$close_date) ? mysql2date( 'Y', $close_date, false ) : gmdate( 'Y', $time_adj );
    $hh = (''!=$close_date) ? mysql2date( 'H', $close_date, false ) : gmdate( 'H', $time_adj );
    $mn = (''!=$close_date) ? mysql2date( 'i', $close_date, false ) : gmdate( 'i', $time_adj );
    $ss = (''!=$close_date) ? mysql2date( 's', $close_date, false ) : gmdate( 's', $time_adj );

    $cur_jj = gmdate( 'd', $time_adj );
    $cur_mm = gmdate( 'm', $time_adj );
    $cur_aa = gmdate( 'Y', $time_adj );
    $cur_hh = gmdate( 'H', $time_adj );
    $cur_mn = gmdate( 'i', $time_adj );

    $month = '<label for="mm" class="screen-reader-text">' . __( 'Month' ) . '</label><select name="c_mm"'.">\n";
    for ( $i = 1; $i < 13; $i = $i +1 ) {
        $monthnum = zeroise($i, 2);
        $month .= "\t\t\t" . '<option value="' . $monthnum . '" ' . selected( $monthnum, $mm, false ) . '>';
        /* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
        $month .= sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
    }
    $month .= '</select>';

    $day = '<label for="jj" class="screen-reader-text">' . __( 'Day' ) . '</label><input type="text" name="c_jj" value="' . $jj . '" size="2" maxlength="2" autocomplete="off" />';
    $year = '<label for="aa" class="screen-reader-text">' . __( 'Year' ) . '</label><input type="text" name="c_aa" value="' . $aa . '" size="4" maxlength="4" autocomplete="off" />';
    $hour = '<label for="hh" class="screen-reader-text">' . __( 'Hour' ) . '</label><input type="text" name="c_hh" value="' . $hh . '" size="2" maxlength="2" autocomplete="off" />';
    $minute = '<label for="mn" class="screen-reader-text">' . __( 'Minute' ) . '</label><input type="text" name="c_mn" value="' . $mn . '" size="2" maxlength="2" autocomplete="off" />';

    echo '<div class="timestamp-wrap">';
    /* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
    printf( __( '%1$s %2$s, %3$s @ %4$s : %5$s' ), $month, $day, $year, $hour, $minute );

    echo '</div><input type="hidden" name="c_ss" value="' . $ss . '" />';
    echo "\n\n";

    ?>
        </tr>
    </table>
    

    <?php
        endif;
    }

    function show_career_column($columns)
    {


        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => __("Title", 'detheme_career'),
            "author" => __("Author", 'detheme_career'),
            "career-category" => __("Categories", 'detheme_career'),
            "date" => __("Date", 'detheme_career'));
        return $columns;
    }

    function career_custom_columns($column)

    {

        global $post;
        switch ($column) {
            case "career-category":
                echo get_the_term_list($post->ID, 'dtcareer', '', ', ', '');
                break;
        }
    }


    function loadTemplate(){

        global $post;

        if(!isset($post))
            return true;

        $standard_type=$post->post_type;
        $templateName=false;

        if(is_single() && $standard_type == 'dtcareer') {
           $templateName='dtcareer';
        }
        else{
            return true;
        }


        if ( $templateName ) {
            $template = locate_template( array( "{$templateName}.php","detheme-career/{$templateName}.php","templates/{$templateName}.php" ),false );
        }

        // Get default slug-name.php
        if ( ! $template && $templateName && file_exists( plugin_dir_path(__FILE__). "/templates/{$templateName}.php" ) ) {

            $template = plugin_dir_path(__FILE__). "templates/{$templateName}.php";
        }

        // Allow 3rd party plugin filter template file from their plugin
        $template = apply_filters( 'detheme_career_get_template_part', $template,$templateName );

        if ( $template ) {

            load_template( $template, false );
            exit;
        }
    }

}


/* Helper */
function get_dtcareer_jobs_value($post_id=0){

    $post_id = absint( $post_id );

    if ( ! $post_id )
        $post_id = get_the_ID();


    $fields=detheme_career::get_dtcareer_job_fields();

     foreach ($fields as $key => $field) {

             $metaname="_".(is_int($key)?sanitize_key($field['label']):$key);
             $value = get_post_meta( $post_id, $metaname, true );

             $fields[$key]['value']=$value;
    }

    return $fields;

}

function get_career_close_date( $d = '', $post_id=0 ) {
    $post_id = absint( $post_id );

    if ( ! $post_id )
        $post_id = get_the_ID();

    $close_date=get_post_meta( $post_id, '_career_close_date', true );


    if ( '' == $d ) {
        $the_date = mysql2date( get_option( 'date_format' ), $close_date );
    } else {
        $the_date = mysql2date( $d, $close_date );
    }

    return apply_filters( 'get_the_date', $the_date, $d);
}

function get_career_start_date( $d = '', $post_id=0 ) {
    $post_id = absint( $post_id );

    if ( ! $post_id )
        $post_id = get_the_ID();

    $close_date=get_post_meta( $post_id, '_career_start_date', true );


    if ( '' == $d ) {
        $the_date = mysql2date( get_option( 'date_format' ), $close_date );
    } else {
        $the_date = mysql2date( $d, $close_date );
    }

    return apply_filters( 'get_the_date', $the_date, $d);
}

add_action('init', array(new detheme_career(),'init'),1);
?>