<?php
defined('ABSPATH') or die();
$output = $el_class = $bg_image = $bg_color = $bg_image_repeat = $font_color = $padding = $margin_bottom = $css = '';

extract(shortcode_atts(array(
    'el_class'        => '',
    'bg_image'        => '',
    'bg_color'        => '',
    'bg_image_repeat' => '',
    'font_color'      => '',
    'padding'         => '',
    'margin_bottom'   => '',
    'full_height'     =>'',
    'equal_height'    => '',
    'columns_placement' =>'',
    'content_placement' =>'',      
    'expanded'        =>0,
    'video_source'    =>'',
    'video_bg_url'    =>'https://www.youtube.com/watch?v=lMJXxhRFO1k',  
    'background_image'=>'',
    'background_video'=>'',
    'background_video_webm'=>'',
    'background_type' =>'image',
    'background_style'=>'no-repeat',
    'css' => '',
    'el_id'=>''
), $atts));

global $detheme_Style,$dt_el_id;


if(isset($_GET['post_id'])){
    $post_id=intval($_GET['post_id']);
}
elseif(isset($_POST['post_id'])){
    $post_id=intval($_POST['post_id']);
}else{
    $post_id=get_the_ID();
}

if('squeeze.php'==get_page_template_slug($post_id) || 'squeezeboxed.php'==get_page_template_slug($post_id) || 'fullwidth.php'==get_page_template_slug($post_id)){
    set_query_var('sidebar','nosidebar');
}

if(!isset($dt_el_id))
                $dt_el_id=0;

$dt_el_id++;

if(version_compare(WPB_VC_VERSION,'4.8.0','<')){

    $customcss="";
    $css=preg_replace('/!(important)/', '', $css);

    if(preg_match_all( '/{(.*)}/s', $css, $matches, PREG_SET_ORDER )){
        $customcss=$matches[0][1];
    }


    $excss=vc_shortcode_custom_css_class($css);

    if(''==$excss){
        $excss="vc_custom_detheme".(vc_is_inline()?time().rand(0,9):$dt_el_id);
    }

    $backgroundattr="";

    switch($background_style){
        case'parallax':
            $parallax=" data-speed=\"2\" data-type=\"background\" ";
            $backgroundattr="background-position: 0% 0%; background-repeat: no-repeat; background-size: cover;";
            break;
        case'cover':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important; background-size: cover!important;";
            break;
        case'no-repeat':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important;background-size:auto !important;";
            break;
        case'repeat':
            $parallax="";
            $backgroundattr="background-position: 0 0 !important;background-repeat: repeat !important;background-size:auto !important;";
            break;
        case'contain':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important;background-size: contain!important;";
            break;
        case 'fixed':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important; background-size: cover!important;background-attachment: fixed !important;";
            break;
        default:
            $parallax="";
            break;
    }

    if($background_type=='image' && ''!=$background_image && $background_image_replace=wp_get_attachment_image_src( $background_image, 'full' )){

        if(preg_match_all('/(background:)([^url].*?)url\((.*)\)/s', $customcss, $matches,PREG_SET_ORDER)){
            $customcss=preg_replace( '/(background:)([^url].*?)url\((.*)\)/s','$1$2url('.$background_image_replace[0].')!important;',$customcss);
        }elseif(preg_match_all('/(background-image:)([^url].*?)url\((.*)\)/s', $customcss, $matches,PREG_SET_ORDER)){
            $customcss=preg_replace( '/(background-image:)([^url].*?)url\((.*)\)/s','$1$2url('.$background_image_replace[0].')!important',$customcss);
        }else{
             $customcss.="background-image:url(".$background_image_replace[0].")!important;";
        }
        $customcss.=$backgroundattr;

    }

    if(''!==$customcss){
        $detheme_Style[]=".".$excss."{".$customcss."}";
    }


    if(''!=$font_color){
        $detheme_Style[]=".".$excss." .dt-counto:after {background-color: ".$font_color." }";
        $detheme_Style[]=".".$excss." h1,.".$excss." h2,.".$excss." h3,.".$excss." h4,.".$excss." h5,.".$excss." h6,.".$excss." .progress_number{color: ".$font_color." }";
    }

    wp_enqueue_style( 'js_composer_front' );
    wp_enqueue_script( 'wpb_composer_front_js' );
    wp_enqueue_style('js_composer_custom_css');

    $el_class = $this->getExtraClass($el_class);

    $style = $this->buildStyle($bg_image, $bg_color, $bg_image_repeat, $font_color, $padding, $margin_bottom);

    $style.=$parallax;

    $css_classes = array(
        'vc_row',
        'wpb_row', //deprecated
        'vc_row-fluid',
        get_row_css_class(),
        $el_class,
        $excss,
    );

    if ( ! empty( $full_height ) ) {
        $css_classes[] = ' vc_row-o-full-height';
        if ( ! empty( $content_placement ) ) {
            $css_classes[] = ' vc_row-o-content-' . $content_placement;
        }
    }

    $css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base']);


    if('vc_row'==$this->settings['base']){

        $video="";

        if($background_type=='video' && ($background_video!='' || $background_video_webm!='' || $video_bg_url!='')){

                if($video_source=='youtube'){

                    global $wp_embed;

                    // Ping WordPress for an embed.

                    $check_embed=wp_oembed_get($video_bg_url,array('controls'=>'0','autoplay'=>'1'));

                    if($check_embed!=''){
                        $check_embed = str_replace('?feature=oembed', '?feature=oembed&autoplay=1&controls=0&loop=1', $check_embed);
                        $check_embed=preg_replace('/player.vimeo.com\/video\/(\w+)"/', 'player.vimeo.com/video/$1?autoplay=1&loop=1&title=0"', $check_embed);
                        $video="<div class=\"dt-video-overlay\"></div><div class=\"dt-video-bg\">".$check_embed."</div>";
                    }

                }
                else{


                    $source_video=array();

                    if($background_video!=''){

                      $video_url=wp_get_attachment_url(intval($background_video));
                      $videodata=wp_get_attachment_metadata(intval($background_video));

                      if(''!=$video_url && $background_type=='video'){
                        $videoformat="video/mp4";
                        if(is_array($videodata) && $videodata['mime_type']=='video/webm'){
                             $videoformat="video/webm";
                        }

                        $source_video[]="<source src=\"".esc_url($video_url)."\" type=\"".$videoformat."\" />";
                     }
                    }

                    if($background_video_webm!=''){

                      $video_url=wp_get_attachment_url(intval($background_video_webm));
                      $videodata=wp_get_attachment_metadata(intval($background_video_webm));

                      if(''!=$video_url && $background_type=='video'){

                        $videoformat="video/mp4";
                        if(is_array($videodata) && $videodata['mime_type']=='video/webm'){
                             $videoformat="video/webm";
                        }

                        $source_video[]="<source src=\"".esc_url($video_url)."\" type=\"".$videoformat."\" />";
                       }
                    }

                    if(count($source_video)){

                      $video="<video class=\"video_background\" autoplay loop>\n".@implode("\n", $source_video)."</video>";

                    }
                }
        }

        if($expanded){
            $columnExpaded=$backgroundExpaded=false;

            $expanded=explode(',',$expanded);

            if(in_array('1', $expanded))
                 $columnExpaded=true;
            if(in_array('2', $expanded))
                 $backgroundExpaded=true;

            $ExpandClass="";


           if($columnExpaded && $backgroundExpaded){
                $ExpandClass=('nosidebar'==get_query_var('sidebar'))?
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")."class=\"box-container".($video!=''?" has-video":"")."\">".$video."<div class=\"".$css_class."\"".$style."><div>":
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")." class=\"".$css_class.($video!=''?" has-video":"")."\"".$style.">".$video."<div><div>";
            }elseif($columnExpaded && !$backgroundExpaded){
                $ExpandClass=('nosidebar'==get_query_var('sidebar'))?
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")."class=\"box-container ".$css_class.($video!=''?" has-video":"")."\"".$style.">".$video."<div><div>":
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")." class=\"".$css_class.($video!=''?" has-video":"")."\"".$style.">".$video."<div><div>";
            }elseif($backgroundExpaded && !$columnExpaded){

                $ExpandClass=('nosidebar'==get_query_var('sidebar'))?
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")."class=\"box-container ".$css_class.($video!=''?" has-video":"")."\"".$style."><div class=\"container dt-container\">".$video."<div class=\"row\">":
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")." class=\"".$css_class.($video!=''?" has-video":"")."\"".$style.">".$video."<div><div>";
            }

            $output.=$ExpandClass;
            $output .= wpb_js_remove_wpautop($content);
            $output.="</div></div>";
            $output .= '</div>'.$this->endBlockComment('row');

        }
        else{

            $output.=('nosidebar'==get_query_var('sidebar'))?'<div class="container dt-container">':"";
            $output .= '<div '.(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"").'class="'.$css_class.($video!=''?" has-video":"").'"'.$style.'>';
            $output .= $video.wpb_js_remove_wpautop($content);
            $output .= '</div>'.$this->endBlockComment('row');
            $output.=('nosidebar'==get_query_var('sidebar'))?'</div>':"";

        }


    }
    else{

        $output .= '<div '.(''!=$el_id?'id="'.$el_id.'" ':'').'class="'.$css_class.'"'.$style.'>';
        $output .= wpb_js_remove_wpautop($content);
        $output .= '</div>'.$this->endBlockComment('row');

    }
/* vc version 4.8 - 4.10 */
}
elseif(version_compare(WPB_VC_VERSION,'4.8.0','>=') && version_compare(WPB_VC_VERSION,'4.11','<')){

    $customcss="";
    $css=preg_replace('/!(important)/', '', $css);

    if(preg_match_all( '/{(.*)}/s', $css, $matches, PREG_SET_ORDER )){
        $customcss=$matches[0][1];
    }


    $excss=vc_shortcode_custom_css_class($css);

    if(''==$excss){
        $excss="vc_custom_detheme".(vc_is_inline()?time().rand(0,9):$dt_el_id);
    }

    $backgroundattr="";

    switch($background_style){
        case'parallax':
            $parallax=" data-speed=\"2\" data-type=\"background\" ";
            $backgroundattr="background-position: 0% 0%; background-repeat: no-repeat; background-size: cover;";
            break;
        case'cover':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important; background-size: cover!important;";
            break;
        case'no-repeat':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important;background-size:auto !important;";
            break;
        case'repeat':
            $parallax="";
            $backgroundattr="background-position: 0 0 !important;background-repeat: repeat !important;background-size:auto !important;";
            break;
        case'contain':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important;background-size: contain!important;";
            break;
        case 'fixed':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important; background-size: cover!important;background-attachment: fixed !important;";
            break;
        default:
            $parallax="";
            break;
    }

    if($background_type=='image' && ''!=$background_image && $background_image_replace=wp_get_attachment_image_src( $background_image, 'full' )){

        if(preg_match_all('/(background:)([^url].*?)url\((.*)\)/s', $customcss, $matches,PREG_SET_ORDER)){
            $customcss=preg_replace( '/(background:)([^url].*?)url\((.*)\)/s','$1$2url('.$background_image_replace[0].')!important;',$customcss);
        }elseif(preg_match_all('/(background-image:)([^url].*?)url\((.*)\)/s', $customcss, $matches,PREG_SET_ORDER)){
            $customcss=preg_replace( '/(background-image:)([^url].*?)url\((.*)\)/s','$1$2url('.$background_image_replace[0].')!important',$customcss);
        }else{
             $customcss.="background-image:url(".$background_image_replace[0].")!important;";
        }
        $customcss.=$backgroundattr;

    }

    if(''!==$customcss){
        $detheme_Style[]=".".$excss."{".$customcss."}";
    }


    if(''!=$font_color){
        $detheme_Style[]=".".$excss." .dt-counto:after {background-color: ".$font_color." }";
        $detheme_Style[]=".".$excss." h1,.".$excss." h2,.".$excss." h3,.".$excss." h4,.".$excss." h5,.".$excss." h6,.".$excss." .progress_number{color: ".$font_color." }";
    }

    wp_enqueue_style( 'js_composer_front' );
    wp_enqueue_script( 'wpb_composer_front_js' );
    wp_enqueue_style('js_composer_custom_css');

    $el_class = $this->getExtraClass($el_class);

    $css_classes = array(
        'vc_row',
        'wpb_row', //deprecated
        'vc_row-fluid',
        $el_class,
        $excss,
    );

    if ( ! empty( $full_height ) ) {
        $css_classes[] = ' vc_row-o-full-height';
        if ( ! empty( $content_placement ) ) {
            $css_classes[] = ' vc_row-o-content-' . $content_placement;
        }
    }

    if (!empty($atts['gap'])) {
        $css_classes[] = 'vc_column-gap-'.$atts['gap'];
    }

    if ( ! empty( $equal_height ) ) {
        $flex_row = true;
        $css_classes[] = 'vc_row-o-equal-height';
    }

    if ( ! empty( $content_placement ) ) {
        $flex_row = true;
        $css_classes[] = 'vc_row-o-content-' . $content_placement;
    }

    if ( ! empty( $flex_row ) ) {
        $css_classes[] = 'vc_row-flex';
    }

    $css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base']);


    if('vc_row'==$this->settings['base']){

        $video="";

        if($background_type=='video' && ($background_video!='' || $background_video_webm!='' || $video_bg_url!='')){

                if($video_source=='youtube'){

                    global $wp_embed;

                    // Ping WordPress for an embed.

                    $check_embed=wp_oembed_get($video_bg_url,array('controls'=>'0','autoplay'=>'1'));

                    if($check_embed!=''){
                        $check_embed = str_replace('?feature=oembed', '?feature=oembed&autoplay=1&controls=0&loop=1', $check_embed);
                        $check_embed=preg_replace('/player.vimeo.com\/video\/(\w+)"/', 'player.vimeo.com/video/$1?autoplay=1&loop=1&title=0"', $check_embed);
                        $video="<div class=\"dt-video-overlay\"></div><div class=\"dt-video-bg\">".$check_embed."</div>";
                    }

                }
                else{


                    $source_video=array();

                    if($background_video!=''){

                      $video_url=wp_get_attachment_url(intval($background_video));
                      $videodata=wp_get_attachment_metadata(intval($background_video));

                      if(''!=$video_url && $background_type=='video'){
                        $videoformat="video/mp4";
                        if(is_array($videodata) && $videodata['mime_type']=='video/webm'){
                             $videoformat="video/webm";
                        }

                        $source_video[]="<source src=\"".esc_url($video_url)."\" type=\"".$videoformat."\" />";
                     }
                    }

                    if($background_video_webm!=''){

                      $video_url=wp_get_attachment_url(intval($background_video_webm));
                      $videodata=wp_get_attachment_metadata(intval($background_video_webm));

                      if(''!=$video_url && $background_type=='video'){

                        $videoformat="video/mp4";
                        if(is_array($videodata) && $videodata['mime_type']=='video/webm'){
                             $videoformat="video/webm";
                        }

                        $source_video[]="<source src=\"".esc_url($video_url)."\" type=\"".$videoformat."\" />";
                       }
                    }

                    if(count($source_video)){

                      $video="<video class=\"video_background\" autoplay loop>\n".@implode("\n", $source_video)."</video>";

                    }
                }
        }

        if($expanded){
            $columnExpaded=$backgroundExpaded=false;

            $expanded=explode(',',$expanded);

            if(in_array('1', $expanded))
                 $columnExpaded=true;
            if(in_array('2', $expanded))
                 $backgroundExpaded=true;

            $ExpandClass="";


           if($columnExpaded && $backgroundExpaded){
                $ExpandClass=('nosidebar'==get_query_var('sidebar'))?
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")."class=\"box-container".($video!=''?" has-video":"")."\">".$video."<div class=\"".$css_class."\"".$parallax."><div>":
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")." class=\"".$css_class.($video!=''?" has-video":"")."\"".$parallax.">".$video."<div><div>";
            }elseif($columnExpaded && !$backgroundExpaded){
                $ExpandClass=('nosidebar'==get_query_var('sidebar'))?
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")."class=\"box-container ".$css_class.($video!=''?" has-video":"")."\"".$parallax.">".$video."<div><div>":
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")." class=\"".$css_class.($video!=''?" has-video":"")."\"".$parallax.">".$video."<div><div>";
            }elseif($backgroundExpaded && !$columnExpaded){

                $ExpandClass=('nosidebar'==get_query_var('sidebar'))?
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")."class=\"box-container ".$css_class.($video!=''?" has-video":"")."\"".$parallax."><div class=\"container dt-container\">".$video."<div class=\"row\">":
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")." class=\"".$css_class.($video!=''?" has-video":"")."\"".$parallax.">".$video."<div><div>";
            }

            $output.=$ExpandClass;
            $output .= wpb_js_remove_wpautop($content);
            $output.="</div></div>";
            $output .= '</div>'.$this->endBlockComment('row');

        }
        else{

            $output.=('nosidebar'==get_query_var('sidebar'))?'<div class="container dt-container">':"";
            $output .= '<div '.(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"").'class="'.$css_class.($video!=''?" has-video":"").'"'.$parallax.'>';
            $output .= $video.wpb_js_remove_wpautop($content);
            $output .= '</div>'.$this->endBlockComment('row');
            $output.=('nosidebar'==get_query_var('sidebar'))?'</div>':"";

        }


    }
    else{

        $output .= '<div '.(''!=$el_id?'id="'.$el_id.'" ':'').'class="'.$css_class.'"'.$parallax.'>';
        $output .= wpb_js_remove_wpautop($content);
        $output .= '</div>'.$this->endBlockComment('row');

    }



}
/* version  4.11 */

else{

    $customcss="";
    $css=preg_replace('/!(important)/', '', $css);

    if(preg_match_all( '/{(.*)}/s', $css, $matches, PREG_SET_ORDER )){
        $customcss=$matches[0][1];
    }


    $excss=vc_shortcode_custom_css_class($css);

    if(''==$excss){
        $excss="vc_custom_detheme".(vc_is_inline()?time().rand(0,9):$dt_el_id);
    }

    $backgroundattr="";

    switch($background_style){
        case'parallax':
            $parallax=" data-speed=\"2\" data-type=\"background\" ";
            $backgroundattr="background-position: 0% 0%; background-repeat: no-repeat; background-size: cover;";
            break;
        case'cover':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important; background-size: cover!important;";
            break;
        case'no-repeat':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important;background-size:auto !important;";
            break;
        case'repeat':
            $parallax="";
            $backgroundattr="background-position: 0 0 !important;background-repeat: repeat !important;background-size:auto !important;";
            break;
        case'contain':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important;background-size: contain!important;";
            break;
        case 'fixed':
            $parallax="";
            $backgroundattr="background-position: center !important; background-repeat: no-repeat !important; background-size: cover!important;background-attachment: fixed !important;";
            break;
        default:
            $parallax="";
            break;
    }

    if($background_type=='image' && ''!=$background_image && $background_image_replace=wp_get_attachment_image_src( $background_image, 'full' )){

        if(preg_match_all('/(background:)([^url].*?)url\((.*)\)/s', $customcss, $matches,PREG_SET_ORDER)){
            $customcss=preg_replace( '/(background:)([^url].*?)url\((.*)\)/s','$1$2url('.$background_image_replace[0].')!important;',$customcss);
        }elseif(preg_match_all('/(background-image:)([^url].*?)url\((.*)\)/s', $customcss, $matches,PREG_SET_ORDER)){
            $customcss=preg_replace( '/(background-image:)([^url].*?)url\((.*)\)/s','$1$2url('.$background_image_replace[0].')!important',$customcss);
        }else{
             $customcss.="background-image:url(".$background_image_replace[0].")!important;";
        }
        $customcss.=$backgroundattr;

    }

    if(''!==$customcss){
        $detheme_Style[]=".".$excss."{".$customcss."}";
    }


    if(''!=$font_color){
        $detheme_Style[]=".".$excss." .dt-counto:after {background-color: ".$font_color." }";
        $detheme_Style[]=".".$excss." h1,.".$excss." h2,.".$excss." h3,.".$excss." h4,.".$excss." h5,.".$excss." h6,.".$excss." .progress_number{color: ".$font_color." }";
    }

    wp_enqueue_style( 'js_composer_front' );
    wp_enqueue_script( 'wpb_composer_front_js' );
    wp_enqueue_style('js_composer_custom_css');

    $el_class = $this->getExtraClass($el_class);

    $css_classes = array(
        'vc_row',
        'wpb_row', //deprecated
        'vc_row-fluid',
        $el_class,
        $excss,
    );

    if ( ! empty( $full_height ) ) {
        $css_classes[] = 'vc_row-o-full-height';
        if ( ! empty( $columns_placement ) ) {
            $flex_row = true;
            $css_classes[] = 'vc_row-o-columns-' . $columns_placement;
            if ( 'stretch' === $columns_placement ) {
                $css_classes[] = 'vc_row-o-equal-height';
            }
        }
    }

    if (!empty($atts['gap'])) {
        $css_classes[] = 'vc_column-gap-'.$atts['gap'];
    }

    if ( ! empty( $equal_height ) ) {
        $flex_row = true;
        $css_classes[] = 'vc_row-o-equal-height';
    }

    if ( ! empty( $content_placement ) ) {
        $flex_row = true;
        $css_classes[] = 'vc_row-o-content-' . $content_placement;
    }

    if ( ! empty( $flex_row ) ) {
        $css_classes[] = 'vc_row-flex';
    }

    $css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base']);


    if('vc_row'==$this->settings['base']){

        $video="";

        if($background_type=='video' && ($background_video!='' || $background_video_webm!='' || $video_bg_url!='')){

                if($video_source=='youtube'){

                    global $wp_embed;

                    // Ping WordPress for an embed.

                    $check_embed=wp_oembed_get($video_bg_url,array('controls'=>'0','autoplay'=>'1'));

                    if($check_embed!=''){
                        $check_embed = str_replace('?feature=oembed', '?feature=oembed&autoplay=1&controls=0&loop=1', $check_embed);
                        $check_embed=preg_replace('/player.vimeo.com\/video\/(\w+)"/', 'player.vimeo.com/video/$1?autoplay=1&loop=1&title=0"', $check_embed);
                        $video="<div class=\"dt-video-overlay\"></div><div class=\"dt-video-bg\">".$check_embed."</div>";
                    }

                }
                else{


                    $source_video=array();

                    if($background_video!=''){

                      $video_url=wp_get_attachment_url(intval($background_video));
                      $videodata=wp_get_attachment_metadata(intval($background_video));

                      if(''!=$video_url && $background_type=='video'){
                        $videoformat="video/mp4";
                        if(is_array($videodata) && $videodata['mime_type']=='video/webm'){
                             $videoformat="video/webm";
                        }

                        $source_video[]="<source src=\"".esc_url($video_url)."\" type=\"".$videoformat."\" />";
                     }
                    }

                    if($background_video_webm!=''){

                      $video_url=wp_get_attachment_url(intval($background_video_webm));
                      $videodata=wp_get_attachment_metadata(intval($background_video_webm));

                      if(''!=$video_url && $background_type=='video'){

                        $videoformat="video/mp4";
                        if(is_array($videodata) && $videodata['mime_type']=='video/webm'){
                             $videoformat="video/webm";
                        }

                        $source_video[]="<source src=\"".esc_url($video_url)."\" type=\"".$videoformat."\" />";
                       }
                    }

                    if(count($source_video)){

                      $video="<video class=\"video_background\" autoplay loop>\n".@implode("\n", $source_video)."</video>";

                    }
                }
        }

        if($expanded){
            $columnExpaded=$backgroundExpaded=false;

            $expanded=explode(',',$expanded);

            if(in_array('1', $expanded))
                 $columnExpaded=true;
            if(in_array('2', $expanded))
                 $backgroundExpaded=true;

            $ExpandClass="";


           if($columnExpaded && $backgroundExpaded){
                $ExpandClass=('nosidebar'==get_query_var('sidebar'))?
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")."class=\"box-container".($video!=''?" has-video":"")."\">".$video."<div class=\"".$css_class." not-flex\"".$parallax."><div class=\"inner-flex\">":
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")." class=\"".$css_class.($video!=''?" has-video":"")." not-flex\"".$parallax.">".$video."<div><div class=\"inner-flex\">";
            }elseif($columnExpaded && !$backgroundExpaded){

                $ExpandClass=('nosidebar'==get_query_var('sidebar'))?
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")."class=\"box-container ".$css_class.($video!=''?" has-video":"")."\"".$parallax.">".$video."<div><div class=\"inner-flex\">":
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")." class=\"".$css_class.($video!=''?" has-video":"")."\"".$parallax.">".$video."<div><div class=\"inner-flex\">";
            }elseif($backgroundExpaded && !$columnExpaded){

                $ExpandClass=('nosidebar'==get_query_var('sidebar'))?
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")."class=\"box-container ".$css_class.($video!=''?" has-video":"")."\"".$parallax."><div class=\"container dt-container\">".$video."<div class=\"inner-flex\">":
                "<div ".(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"")." class=\"".$css_class.($video!=''?" has-video":"")."\"".$parallax.">".$video."<div><div class=\"inner-flex\">";
            }

            $output.=$ExpandClass;
            $output .= wpb_js_remove_wpautop($content);
            $output.="</div></div>";
            $output .= '</div>'.$this->endBlockComment('row');

        }
        else{

            $output.=('nosidebar'==get_query_var('sidebar'))?'<div class="container dt-container">':"";
            $output .= '<div '.(''!=$el_id?'id="'.$el_id.'" ':'').((vc_is_inline())?"style=\"".$customcss."\" ":"").'class="'.$css_class.($video!=''?" has-video":"").'"'.$parallax.'>';
            $output .= $video;
            $output .= "<div class=\"inner-flex\">".wpb_js_remove_wpautop($content);
            $output .= '</div></div>'.$this->endBlockComment('row');
            $output.=('nosidebar'==get_query_var('sidebar'))?'</div>':"";

        }


    }
    else{

        $output .= '<div '.(''!=$el_id?'id="'.$el_id.'" ':'').'class="'.$css_class.'"'.$parallax.'><div class="inner-flex">';
        $output .= wpb_js_remove_wpautop($content);
        $output .= '</div></div>'.$this->endBlockComment('row');

    }

}

echo $output;