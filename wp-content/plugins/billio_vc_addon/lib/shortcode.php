<?php
defined('ABSPATH') or die();

if(shortcode_exists('dt_map'))
    remove_shortcode('dt_map');

add_shortcode('dt_map', 'vc_dtmap_shortcode');

function vc_dtmap_shortcode($atts, $content = null){

    global $dt_el_id;

    extract( shortcode_atts( array(
        'lat'=>-7.2852292,
        'lang'=>112.6809869,
        'zoom'=>7,
        'zoomcontrol'=>true,
        'pancontrol'=>true,
        'streetcontrol'=>true,
        'scrollcontrol'=>true,
        'height'=>'400px',
        'width'=>'',
        'style'=>'pastel',
        'marker'=>'default',
        'image_marker'=>'',
        'title'=>''
    ), $atts ) );


    if(!isset($dt_el_id) || empty($dt_el_id)){
        $dt_el_id=0;
    }

    $height=empty($height)?"400px":$height;

    $dt_el_id++;
    if(vc_is_inline()){

        $dt_el_id=time().rand(1,99);
    }

    if(!is_admin()){
       wp_enqueue_script('gmap',"https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyAXXCx5pSNgkEifSuRdoPvaJ2K7bhdhwRg",array('jquery'));
    }

    $mapStyle=array(
        'shades'=>'[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]}]',
        'midnight'=>'[{"featureType":"water","stylers":[{"color":"#021019"}]},{"featureType":"landscape","stylers":[{"color":"#08304b"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#0c4152"},{"lightness":5}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#0b434f"},{"lightness":25}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#0b3d51"},{"lightness":16}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#000000"},{"lightness":13}]},{"featureType":"transit","stylers":[{"color":"#146474"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#144b53"},{"lightness":14},{"weight":1.4}]}]',
        'bluewater'=>'[{"featureType":"water","stylers":[{"color":"#46bcec"},{"visibility":"on"}]},{"featureType":"landscape","stylers":[{"color":"#f2f2f2"}]},{"featureType":"road","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]}]',
        'lightmonochrome'=>'[{"featureType":"water","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":-78},{"lightness":67},{"visibility":"simplified"}]},{"featureType":"landscape","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"hue":"#e9ebed"},{"saturation":-90},{"lightness":-8},{"visibility":"simplified"}]},{"featureType":"transit","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":10},{"lightness":69},{"visibility":"on"}]},{"featureType":"administrative.locality","elementType":"all","stylers":[{"hue":"#2c2e33"},{"saturation":7},{"lightness":19},{"visibility":"on"}]},{"featureType":"road","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":-2},{"visibility":"simplified"}]}]',
        'neutralblue'=>'[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#193341"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#2c5a71"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#29768a"},{"lightness":-37}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#406d80"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#406d80"}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#3e606f"},{"weight":2},{"gamma":0.84}]},{"elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"weight":0.6},{"color":"#1a3541"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#2c5a71"}]}]',
        'avocadoworld'=>'[{"featureType":"water","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#aee2e0"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#abce83"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#769E72"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#7B8758"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#EBF4A4"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#8dab68"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#5B5B3F"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#ABCE83"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#A4C67D"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#9BBF72"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#EBF4A4"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#87ae79"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#7f2200"},{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"},{"visibility":"on"},{"weight":4.1}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#495421"}]},{"featureType":"administrative.neighborhood","elementType":"labels","stylers":[{"visibility":"off"}]}]',
        'nature'=>'[{"featureType":"landscape","stylers":[{"hue":"#FFA800"},{"saturation":0},{"lightness":0},{"gamma":1}]},{"featureType":"road.highway","stylers":[{"hue":"#53FF00"},{"saturation":-73},{"lightness":40},{"gamma":1}]},{"featureType":"road.arterial","stylers":[{"hue":"#FBFF00"},{"saturation":0},{"lightness":0},{"gamma":1}]},{"featureType":"road.local","stylers":[{"hue":"#00FFFD"},{"saturation":0},{"lightness":30},{"gamma":1}]},{"featureType":"water","stylers":[{"hue":"#00BFFF"},{"saturation":6},{"lightness":8},{"gamma":1}]},{"featureType":"poi","stylers":[{"hue":"#679714"},{"saturation":33.4},{"lightness":-25.4},{"gamma":1}]}]',
        'pastel'=>'[{"featureType":"landscape","stylers":[{"saturation":-100},{"lightness":60}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","stylers":[{"visibility":"on"},{"lightness":30}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ef8c25"},{"lightness":40}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#b6c54c"},{"lightness":40},{"saturation":-40}]},{}]'
        );


    $mapOptions=array();
    $mapOptions['zoom']='zoom: '.$zoom;
    $markerOption="";

    if($marker!=='default'){

        $image_url=DETHEME_VC_DIR_URL.'images/map_marker.png';

        if($image_marker){

            $imageMarker = wp_get_attachment_image_src(trim($image_marker),'full',false); 
            if(!empty($imageMarker[0])){
                        $image_url=$imageMarker[0];
            }

        }

        $markerOption='var iconMarker = {url: \''.$image_url.'\'};';

    }

    if(!$zoomcontrol){$mapOptions['zoomControl']='zoomControl:false';}
    if(!$pancontrol){$mapOptions['panControl']='panControl:false';}
    if(!$streetcontrol){$mapOptions['streetViewControl']='streetViewControl:false';}
    if(!$scrollcontrol){$mapOptions['scrollwheel']='scrollwheel:false';}


    $compile="<div id=\"map-canvas".$dt_el_id."\" class=\"google-map\" style=\"height:".$height.((!empty($width))?";width:".$width."":"")."\"></div>";


    $compile.='<script type="text/javascript">';
    $compile.='jQuery(document).ready(function($) {
                try {
                    var map,center = new google.maps.LatLng('.$lat.','.$lang.'),'.(isset($mapStyle[$style])?"style=".$mapStyle[$style].",":"").'
                    mapOptions = {center: center,mapTypeControl: false,'.@implode(',',$mapOptions).(isset($mapStyle[$style])?",styles:style":"").'};
                    '.$markerOption.'
                    
                    map = new google.maps.Map(document.getElementById(\'map-canvas'.$dt_el_id.'\'),mapOptions);
                    var marker = new google.maps.Marker({
                        position: center,
                        map: map,
                      '.(!empty($markerOption)?"icon: iconMarker":"").'  
                    });
                    
                } catch ($err) {
                }
        });
</script>'."\n";




   return $compile;




}

if(shortcode_exists('dt_iconbox'))
    remove_shortcode('dt_iconbox');

add_shortcode('dt_iconbox', 'vc_iconbox_shortcode');

function vc_iconbox_shortcode($atts, $content = null) {

            global $dt_el_id,$detheme_Style;

            wp_enqueue_style('detheme-vc');
            wp_enqueue_style('scroll-spy');


            wp_register_script('jquery.appear',DETHEME_VC_DIR_URL."js/jquery.appear.js",array());
            wp_register_script('jquery.counto',DETHEME_VC_DIR_URL."js/jquery.counto.js",array());
            wp_register_script('dt-iconbox',DETHEME_VC_DIR_URL."js/dt_iconbox.js",array('jquery.appear','jquery.counto'));

            wp_enqueue_script('dt-iconbox');
            wp_enqueue_script('ScrollSpy');

            if (!isset($compile)) {$compile='';}

            extract( shortcode_atts( array(
                'iconbox_heading' => '',
                'color_heading'=>'',
                'button_link' => '',
                'button_text' => '',
                'icon_type' => '',
                'layout_type'=>'1',
                'target' => '_blank',
                'iconbox_text'=>'',
                'link' => '',
                'iconbox_number'=>100,
                'icon_size'=>'',
                'line_height'=>'',
                'spy'=>'none',
                'css'=>'',
                'spydelay'=>300
            ), $atts ) );

            $content=(empty($content) && !empty($iconbox_text))?$iconbox_text:$content;

            if(!isset($dt_el_id) || empty($dt_el_id))
                $dt_el_id=0;

            $iconbox_number=(int)$iconbox_number;
            $color_heading=(!empty($color_heading))?" style=\"color:".$color_heading."\"":"";

            $content=wpb_js_remove_wpautop($content,true);


             $scollspy="";
             $spydly=0;
            if('none'!==$spy && !empty($spy)){

                $spydly=$spydly+(int)$spydelay;

                $scollspy='data-uk-scrollspy="{cls:\''.$spy.'\', delay:'.$spydly.'}"';

            }

            switch($layout_type){
                case '2':
                    $compile='<div class="dt-iconboxes-2 layout-'.$layout_type.'" '. $scollspy.'>
                        <div class="dt-section-icon hi-icon-wrap hi-icon-effect-5 hi-icon-effect-5a">'.((strlen($link)>0) ?"<a target='".$target."' href='".$link."'>":"").'<i class="hi-icon '.$icon_type.'"></i>'.((strlen($link)>0) ?"</a>":"").'</div>
                        <h4'.$color_heading.'>'.$iconbox_heading.'</h4>'.'<div class="dt-iconboxes-text">'.                 
                        ((!empty($content))?do_shortcode($content):"").'</div>
                        </div>';
                    break;
                case '3':
                    $compile='<div class="dt-iconboxes layout-'.$layout_type.'" '. $scollspy.'>
                        <span>'.((strlen($link)>0) ?"<a target='".$target."' href='".$link."'>":"").'<i class="'.$icon_type.'"></i>'.((strlen($link)>0) ?"</a>":"").'</span>
                        <h3 class="dt-counter">'.$iconbox_number.'</h3>
                        <h4'.$color_heading.'>'.$iconbox_heading.'</h4><div class="dt-iconboxes-text">
                        '.((!empty($content))?do_shortcode($content):"").'</div></div>';

                    break;
                case '4':
                    $compile='<div '. $scollspy.'><div class="dt-iconboxes-4 layout-'.$layout_type.'">
                        <div class="dt-section-icon hi-icon-wrap hi-icon-effect-5 hi-icon-effect-5d">'.((strlen($link)>0) ?"<a target='".$target."' href='".$link."'>":"").'<i class="hi-icon '.$icon_type.'"></i>'.((strlen($link)>0) ?"</a>":"").'</div>
                        <h4'.$color_heading.'>'.$iconbox_heading.'</h4>'.                 
                        '<div class="dt-iconboxes-text">'.((!empty($content))?do_shortcode($content):"").'</div>'.'
                        </div></div>';
                    break;
                case '5':
                    $compile='<div class="dt-iconboxes-5 layout-'.$layout_type.'" '. $scollspy.'>
                        <div class="dt-section-icon hi-icon-wrap hi-icon-effect-5 hi-icon-effect-5a">'.((strlen($link)>0) ?"<a target='".$target."' href='".$link."'>":"").'<i class="hi-icon '.$icon_type.'"></i>'.((strlen($link)>0) ?"</a>":"").'</div>
                        <h4'.$color_heading.'>'.$iconbox_heading.'</h4>'.                 
                        '<div class="dt-iconboxes-text">'.((!empty($content))?do_shortcode($content):"").'</div>'.'
                        </div>';
                    break;
                case '6':
                    $compile='<div class="dt-iconboxes layout-'.$layout_type.'" '. $scollspy.'>
                        '.((strlen($link)>0) ?"<a target='".$target."' href='".$link."'>":"").'<i class="'.$icon_type.'"></i>'.((strlen($link)>0) ?"</a>":"").'
                        <h4'.$color_heading.'>'.$iconbox_heading.'</h4><div class="dt-iconboxes-text">
                        '.((!empty($content))?do_shortcode($content):"").'</div></div>';

                    break;
                case '7':
                case '8':
                    $compile='<div class="dt-iconboxes layout-'.$layout_type.'" '. $scollspy.'>
                        '.((strlen($link)>0) ?"<a target='".$target."' href='".$link."'>":"").'<i class="'.$icon_type.'"></i>'.((strlen($link)>0) ?"</a>":"").'
                        <div class="text-box"><h4'.$color_heading.'>'.$iconbox_heading.'</h4>
                        '.((!empty($content))?do_shortcode($content):"").'</div></div>';

                    break;
                default:
                    $compile='<div class="dt-iconboxes layout-'.$layout_type.'" '. $scollspy.'>
                        <span>'.((strlen($link)>0) ?"<a target='".$target."' href='".$link."'>":"").'<i class="'.$icon_type.'"></i>'.((strlen($link)>0) ?"</a>":"").'</span>
                        <h4'.$color_heading.'>'.$iconbox_heading.'</h4><div class="dt-iconboxes-text">'.
                        ((!empty($content))?do_shortcode($content):"").'</div>
                    </div>';

                    break;
            }

            $dt_el_id++;
            $excss="";
            if(''!=$css){
                $excss=vc_shortcode_custom_css_class($css);
                $detheme_Style[]=$css;
            }
            if($icon_size!=''){
               $detheme_Style[]="#module_dt_iconboxes_".$dt_el_id." i {font-size:".$icon_size."px;}";
            }
            if($line_height!=''){

               $detheme_Style[]="#module_dt_iconboxes_".$dt_el_id." i:before {line-height:".$line_height.";}";
            }



            return "<div id=\"module_dt_iconboxes_".$dt_el_id."\" class=\"module_dt_iconboxes".(''!=$excss?" ".$excss:"")."\">".$compile."</div>";
}


function vc_dt_team_custom_item($atts, $content = null){

        wp_enqueue_style('detheme-vc');

        if (!isset($compile)) {$compile='';}

        extract(shortcode_atts(array(
            'title' => '',
            'sub_title' => '',
            'text' => '',
            'layout_type'=>'fix',
            'image_url'=>'',
            'social_link'=>'show',
            'facebook'=>'',
            'twitter'=>'',
            'gplus'=>'',
            'pinterest'=>'',
            'linkedin'=>'',
            'website'=>'',
            'email'=>'',
            'spy'=>'none',
            'scroll_delay'=>300,
        ), $atts));

        $scollspy="";
        if('none'!==$spy && !empty($spy)){
            wp_enqueue_style('scroll-spy');
            wp_enqueue_script('ScrollSpy');

            $scollspy='data-uk-scrollspy="{cls:\''.$spy.'\', delay:'.$scroll_delay.'}"';

        }

        $social_lists="";
        if($social_link=='show'){

            $social_lists_options=array();

            if($facebook) $social_lists_options['facebook']="<a href=\"".esc_url($facebook)."\" target=\"_blank\"><i class=\"fontelloicon-facebook-squared\"></i></a>";
            if($twitter) $social_lists_options['twitter']="<a href=\"".esc_url($twitter)."\" target=\"_blank\"><i class=\"fontelloicon-twitter-squared\"></i></a>";
            if($gplus) $social_lists_options['gplus']="<a href=\"".esc_url($gplus)."\" target=\"_blank\"><i class=\"fontelloicon-gplus-squared\"></i></a>";
            if($linkedin) $social_lists_options['linkedin']="<a href=\"".esc_url($linkedin)."\" target=\"_blank\"><i class=\"fontelloicon-linkedin-squared\"></i></a>";
            if($pinterest) $social_lists_options['pinterest']="<a href=\"".esc_url($pinterest)."\" target=\"_blank\"><i class=\"fontelloicon-pinterest-squared\"></i></a>";
            if($website) $social_lists_options['website']="<a href=\"".esc_url($website)."\" target=\"_blank\"><i class=\"fontello-globe\"></i></a>";
            if($email) $social_lists_options['email']="<a href=mailto:".sanitize_email($email)." target=\"_blank\"><i class=\"fontelloicon-mail-alt\"></i></a>";



            if(count($social_lists_options))
                 $social_lists="<ul class=\"profile-scocial\"><li>".implode('</li><li>', $social_lists_options)."</li></ul>";

        }

        $alt_image="";
        if(!empty($image_url)){
            $alt_image = get_post_meta($image_url, '_wp_attachment_image_alt', true);
            $image = wp_get_attachment_image_src($image_url,'full',false); 
            $image_url=$image[0];
        }

        if('fix'==$layout_type){

            $compile='<div class="left-item"><img src="'.esc_url($image_url).'" alt="'.esc_attr($alt_image).'" /></div>
            <div class="right-item"><h2 class="profile-title">'.$title.'</h2><hr/><h3 class="profile-position">'.$sub_title.'</h3>
            '.(!empty($text)?'<div class="text">'.$text.'</div>':"").$social_lists.'
            </div>';

        }
        else{


        $compile='<div class="profile">
                <figure>
                    <div class="top-image">
                        <img src="'.$image_url .'" class="img-responsive" alt="'.esc_attr($alt_image).'"/>
                    </div>
                    <figcaption>
                        <h3><span class="profile-heading">'.$title.'</span></h3>
                        <span class="profile-subheading">'.$sub_title.'</span>
                        '.(!empty($text)?'<p>'.$text.'</p>':"");

         $compile.= $social_lists.'<div class="figcap"></div>
                    </figcaption>
                </figure>
            </div>';


        }



    return  $compile='<div class="dt_team_custom_item '.$layout_type.'" '.$scollspy.'>'.$compile.'</div>';

}

add_shortcode('dt_team_custom_item','vc_dt_team_custom_item');

function dt_circlebar_item_shortcode($atts, $content = null){


    wp_register_script('jquery.appear',DETHEME_VC_DIR_URL."js/jquery.appear.js",array());
    wp_register_script('jquery.counto',DETHEME_VC_DIR_URL."js/jquery.counto.js",array());
    wp_register_script('dt-chart',DETHEME_VC_DIR_URL."js/chart.js",array('jquery.appear','jquery.counto'));


    wp_enqueue_script('dt-chart');

    if (!isset($compile)) {$compile='';}



    extract(shortcode_atts(array(

        'unit' => '',
        'title' => '',
        'item_number'=>'1',
        'value' => '10',
        'size'=>'',
        'color'=>'#19bd9b',
        'bg'=>''

    ), $atts));

    $compile.='<div class="dt_circlebar">
                    <div class=\'pie_chart_holder normal\'>
                            <canvas class="doughnutChart" data-noactive="'.$bg.'" data-size="'.$size.'" data-unit="'.$unit.'" data-active="'.$color.'" data-percent=\''.$value.'\'></canvas>
                    </div>
                    <h4 class="pie-title">'.$title.'</h4>
                    <div class="pie-description"></div>
                </div>';

    return $compile;



}


if(shortcode_exists('dt_circlebar_item'))
    remove_shortcode('dt_circlebar_item');

add_shortcode('dt_circlebar_item', 'dt_circlebar_item_shortcode');


if(shortcode_exists('dt_twitter_slider'))
    remove_shortcode('dt_twitter_slider');

function dt_twitter_slider_shortcode($atts, $content = null){

    global $dt_el_id;

    wp_enqueue_script('dt-chart');

    if (!isset($compile)) {$compile='';}

    if(!isset($dt_el_id)){$dt_el_id=0;}

    $dt_el_id++;



    extract(shortcode_atts(array(

        'twitteraccount' => 'detheme',
        'numberoftweets' => 4,
        'dateformat'=>'%b. %d, %Y',
        'twittertemplate' => '{{date}}<br />{{tweet}}',
        'isautoplay'=>1,
        'transitionthreshold'=>500

    ), $atts));

        $twittertemplate=preg_replace('/\n/', '', trim($twittertemplate));

        if(!is_admin()){

            wp_enqueue_script( 'tweetie', DETHEME_VC_DIR_URL. 'lib/twitter_slider/tweetie.js', array( 'jquery' ), '1.0', false);
            wp_register_style('owl.carousel',DETHEME_VC_DIR_URL."css/owl.carousel.css",array());
            wp_enqueue_style('owl.carousel');
            wp_register_script( 'owl.carousel', DETHEME_VC_DIR_URL . 'js/owl.carousel.js', array('jquery'), '1.29', false );
            wp_enqueue_script('owl.carousel');

        }

        $compile.='<div id="dt_twitter_'.$dt_el_id.'" class="dt-twitter-slider"></div>';
        $compile.='<script type="text/javascript">';
        $compile.='jQuery(document).ready(function($) {
                \'use strict\';
                
                $(\'#dt_twitter_'.$dt_el_id.'\').twittie({
                    element_id: \'dt_twitter_slider_'.$dt_el_id.'\',
                    username: \''.$twitteraccount.'\',
                    count: '.$numberoftweets.',
                    hideReplies: false,
                    dateFormat: \''.$dateformat.'\',
                    template: \''.$twittertemplate.'\',
                    apiPath: \''. DETHEME_VC_DIR_URL. 'lib/twitter_slider/api/tweet.php\'
                },function(){
                    $(\'#dt_twitter_slider_'.$dt_el_id.'\').owlCarousel({
                        items       : 1, //10 items above 1000px browser width
                        itemsDesktop    : [1000,1], //5 items between 1000px and 901px
                        itemsDesktopSmall : [900,1], // 3 items betweem 900px and 601px
                        itemsTablet : [600,1], //2 items between 600 and 0;
                        itemsMobile : false, // itemsMobile disabled - inherit from itemsTablet option
                        pagination  : true,
                        autoPlay    : ' . ($isautoplay?"true":"false") . ',
                        slideSpeed  : 200,
                        paginationSpeed  : ' . $transitionthreshold . '
                    });
                });
            });</script>'."\n";

    return $compile;


}

add_shortcode('dt_twitter_slider', 'dt_twitter_slider_shortcode');

if (is_plugin_active('detheme-portfolio/detheme_port.php')) {

function dt_portfolio_shortcode($atts, $content = null){

    global $dt_el_id,$dt_revealData,$detheme_config;

        extract(shortcode_atts(array(

        'portfolio_cat' => '',
        'portfolio_num' => 10,
        'speed'=>800,
        'autoplay'=>'0',
        'spy'=>'none',
        'scroll_delay'=>300,
        'layout'=>'carousel',
        'column'=>4,
        'desktop_column'=>4,
        'small_column'=>4,
        'tablet_column'=>2,
        'mobile_column'=>1,


    ), $atts));

    $queryargs = array(
            'post_type' => 'port',
            'no_found_rows' => false,
            'meta_key' => '_thumbnail_id',
            'posts_per_page'=>$portfolio_num,
            'compile'=>'',
            'script'=>''
        );

    if(!empty($portfolio_cat)){

            $queryargs['tax_query']=array(
                            array(
                                'taxonomy' => 'portcat',
                                'field' => 'id',
                                'terms' =>@explode(",",$portfolio_cat)
                            )
                        );

    }

    $query = new WP_Query( $queryargs );    
    $compile="";

    if ( $query->have_posts() ) :

        if('none'!==$spy && !empty($spy)){


            wp_enqueue_style('scroll-spy');
            wp_enqueue_script('ScrollSpy');
        }

        $spydly=0;
        $portspty=0;


        if(is_admin()){

            }else{

                wp_register_style('owl.carousel',DETHEME_VC_DIR_URL."css/owl.carousel.css",array());
                wp_enqueue_style('owl.carousel');


                wp_register_script( 'owl.carousel', DETHEME_VC_DIR_URL . 'js/owl.carousel.js', array('jquery'), '1.29', false );
                wp_enqueue_script('owl.carousel');

        }
        if(!isset($dt_el_id))
                $dt_el_id=0;

        $dt_el_id++;

        if(vc_is_inline()){

            $dt_el_id.="_".time().rand(0,100);
        }


        $widgetID="dt_portfolio".$dt_el_id;
        $modal_effect = (empty($detheme_config['dt-select-modal-effects'])) ? 'md-effect-15' : $detheme_config['dt-select-modal-effects'];

        $compile='<div class="dt-portfolio-container portfolio-type-'.(($layout=='carousel')?"image":"text").'">
        <div class="owl-carousel-navigation prev-button">
           <a class="btn btn-owl prev btn-color-secondary skin-dark">'.__('<i class="icon-left-open-big"></i>','detheme').'</a>
        </div>
        <div class="owl-carousel" id="'.$widgetID.'">';

                while ( $query->have_posts() ) : 
                
                    $query->the_post();
                    
                    $terms = get_the_terms(get_the_ID(), 'portcat' );
                    $term_lists=array();

                    if ( !empty( $terms ) ) {
      
                          foreach ( $terms as $term ) {
                            $cssitem[] =sanitize_html_class($term->slug, $term->term_id);
                            $term_lists[]="<a href=\"".get_term_link( $term)."\">".$term->name."</a>";
                          }

                    }



                    $imageId=get_post_thumbnail_id(get_the_ID());
                    $featured_image  = get_post( $imageId );
                    $alt_image = get_post_meta($imageId, '_wp_attachment_image_alt', true);



                    if (isset($featured_image->guid)) {
                        $imgurl = aq_resize($featured_image->guid, 0, 300,true);

                        $spydly=$spydly+(int)$scroll_delay;

                        $scollspy="";


                       if('none'!==$spy && !empty($spy) && $portspty < 5){

                            $scollspy='data-uk-scrollspy="{cls:\''.$spy.'\', delay:'.$spydly.'}"';
                        }

                            $compile.='<div class="portfolio-item" '.$scollspy.'>';


                        if('carousel'==$layout){

                           $compile.='<div class="post-image-container">'.((isset($imgurl) && !empty($imgurl))?'<div class="post-image">
                                    <img src="'.esc_url($imgurl).'" alt="'.esc_attr($alt_image).'" /></div>':'').'
                                <div class="imgcontrol tertier_color_bg_transparent">
                                    <div class="portfolio-termlist">'.(count($term_lists)?@implode(', ',$term_lists):"").'</div>
                                    <div class="portfolio-title">'.get_the_title().'</div>
                                    <div class="imgbuttons">
                                        <a class="md-trigger btn icon-zoom-in secondary_color_button" data-modal="modal_portfolio_'.get_the_ID().'" onclick="return false;" '.
                                        'href="'.get_the_permalink().'"></a><a class="btn icon-link secondary_color_button " href="'.get_the_permalink().'"></a>
                                    </div>
                                </div>
                            </div>';
                        

                        }
                        else{

                           $compile.='<div class="post-image-container">'.((isset($imgurl) && !empty($imgurl))?'<div class="post-image">
                                    <img src="'.esc_url($imgurl).'" alt="'.esc_attr($alt_image).'" /></div>':'').'
                                <div class="imgcontrol tertier_color_bg_transparent">
                                    <div class="imgbuttons">
                                        <a class="md-trigger btn icon-zoom-in secondary_color_button" data-modal="modal_portfolio_'.get_the_ID().'" onclick="return false;" '.
                                        'href="'.get_the_permalink().'"></a><a class="btn icon-link secondary_color_button " href="'.get_the_permalink().'"></a>
                                    </div>
                                </div>
                            </div>';

                            $compile.='<div class="portfolio-description">';
                            $compile.='<div class="portfolio-termlist">'.(count($term_lists)?@implode(', ',$term_lists):"").'</div>';
                            $compile.='<div class="portfolio-title">'.get_the_title().'</div>';
                            $compile.='<div class="portfolio-excerpt"><p>'.get_the_excerpt().'</p>';
                            $compile.='<a href="'.get_the_permalink().'" class="read_more" title="'.esc_attr(sprintf(__( 'Detail to %s', 'detheme' ), get_the_title())).'">'.__('Read more', 'detheme').'<i class="icon-right-dir"></i></a>';
                            $compile.='</div></div>';

                        }

                            $compile.='</div>';

                        $modalcontent='<div id="modal_portfolio_'.get_the_ID().'" class="popup-gallery md-modal '.$modal_effect.'">
                                <div class="md-content">'.($featured_image?'<img src="#" rel="'.$featured_image->guid.'" class="img-responsive" alt="'.esc_attr($alt_image).'"/>':"").'     
                                    <div class="md-description secondary_color_bg">'.get_the_excerpt().'</div>
                                    <button class="secondary_color_button md-close"><i class="icon-cancel"></i></button>
                                </div>
                            </div>';

                            array_push($dt_revealData,$modalcontent);


                        $portspty++;




                      }
                endwhile;

         $compile.="</div>
                     <div class=\"owl-carousel-navigation next-button\">
                       <a class=\"btn btn-owl next btn-color-secondary skin-dark\">".__('<i class="icon-right-open-big"></i>','detheme')."</a>
        </div></div>";

        $script='<script type="text/javascript">'."\n".'jQuery(document).ready(function($) {
            \'use strict\';'."\n".'
            var '.$widgetID.' = jQuery("#'.$widgetID.'");
            try{
           '.$widgetID.'.owlCarousel({
                items       : '.$column.', 
                itemsDesktop    : [1200,'.$desktop_column.'], 
                itemsDesktopSmall : [1023,'.$small_column.'], // 3 items betweem 900px and 601px
                itemsTablet : [768,'.$tablet_column.'], //2 items between 600 and 0;
                itemsMobile : [600,'.$mobile_column.'], // itemsMobile disabled - inherit from itemsTablet option
                pagination  : false,'.($autoplay?'autoPlay:true,':'')."
                slideSpeed  : ".$speed.",paginationSpeed  : ".$speed.",rewindSpeed  : ".$speed.",";
          $script.='});'."\n".'
    '.$widgetID.'.parents(\'.dt-portfolio-container\').find(".next").click(function(){
        '.$widgetID.'.trigger(\'owl.next\');
      });
    '.$widgetID.'.parents(\'.dt-portfolio-container\').find(".prev").click(function(){
        '.$widgetID.'.trigger(\'owl.prev\');
      });';

         $script.='}
            catch(err){}
        });</script>';

     $compile.=$script;   
    endif;
    
    wp_reset_query();
    return $compile;

}

if(shortcode_exists('dt_portfolio'))
    remove_shortcode('dt_portfolio');

   add_shortcode('dt_portfolio', 'dt_portfolio_shortcode');

}

function dt_countdown_shortcode($atts, $content = null){

    global $dt_el_id,$detheme_Style;


   extract(shortcode_atts(array(

        'year'=>'',
        'month'=>'',
        'date'=>'',
        'time'=>date('H:s',current_time( 'timestamp', 0 )),
        'url'=>'',
        'countdown_type'=>'fixed',
        'countdown_box_color'=>'',
        'countdown_text_color'=>'',
        'relative_time'=>0,
        'cookie_lifetime'=>1,
        'countdown_label_color'=>'',
        'dot_color'=>''

    ), $atts));

    if(!stripos($time, ":")){
        $time.=":00";
    }

   if(!isset($dt_el_id)) $dt_el_id=0;

   $dt_el_id++;

   $message=str_replace(array("\n","\t"),array("",""), do_shortcode($content));
   $current_offset = get_option('gmt_offset');

   if($countdown_type=='evergreen'){
        $dateTo = "+".intval($relative_time)." minutes";

        $page_id=get_the_ID();
        $cookie_name='countdown_page'.$page_id."el".$dt_el_id;
        if(isset($_COOKIE[$cookie_name])){
            $timeCurrent=$_COOKIE[$cookie_name];
        }
        else{
            $timeCurrent=time();
            print '<script type="text/javascript">document.cookie="'.$cookie_name.'='.$timeCurrent.'; expires='.date('r',strtotime(intval($cookie_lifetime).' hours')).'; path=/";</script>';
        }
   }
   else{
       $dateTo = "$month $date $year $time";
       $timeCurrent = (!empty($current_offset))?time()+($current_offset*3600):time();
   }

   $timeTo= strtotime( $dateTo );


   $compile="";

   if(vc_is_inline()){
        $dt_el_id.="_".time();
   }

    if($countdown_type=='evergreen'){
         $until=($relative_time * 60) - (time() - $timeCurrent);
    }
    else{
        $until=$timeTo-$timeCurrent;
    }


   if( $until < 1 ){

    if(!empty($url)){

        $compile.='<meta http-equiv="refresh" content="5;URL='.$url.'" />';
    }



   $compile.='<div class="dt-countdown">'.
   '<div id="countdown_'.$dt_el_id.'" class="countdown">'.$message.'</div>'.
   '</div>';


   }
   else{


     $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    if(is_admin()){

    }else{

    wp_register_style('jquery.countdown',DETHEME_VC_DIR_URL."css/jquery.countdown.css",array(),false);
    wp_enqueue_style('jquery.countdown');

    wp_register_script('jquery.plugin',DETHEME_VC_DIR_URL."js/jquery.plugin".$suffix.".js",array('jquery'));
    wp_enqueue_script('jquery.plugin');
    wp_register_script('jquery.countdown',DETHEME_VC_DIR_URL."js/jquery.countdown".$suffix.".js",array('jquery.plugin'));
    wp_enqueue_script('jquery.countdown');

    wp_localize_script( 'jquery.countdown', 'i18countdown', array(
          'lbl_years'=>__('Years','detheme'),
          'lbl_months'=>__('Months','detheme'),
          'lbl_weeks'=>__('Weeks','detheme'),
          'lbl_days'=>__('Days','detheme'),
          'lbl_hours'=>__('Hours','detheme'),
          'lbl_minutes'=>__('Minutes','detheme'),
          'lbl_seconds'=>__('Seconds','detheme'),
          'lbl_year'=>__('Year','detheme'),
          'lbl_month'=>__('Month','detheme'),
          'lbl_week'=>__('Week','detheme'),
          'lbl_day'=>__('Day','detheme'),
          'lbl_hour'=>__('Hour','detheme'),
          'lbl_minute'=>__('Minute','detheme'),
          'lbl_second'=>__('Second','detheme'),
    ));

    }




    $compile.="<script type=\"text/javascript\">".'jQuery(document).ready(function($) {'.
            '\'use strict\';'.
            '$(\'#countdown_'.$dt_el_id.'\').countdown({until: +'.$until.
                ((!empty($message) && empty($url))?",expiryText:'".$message."'":"").
                ((!empty($url))?",expiryUrl:'".$url."'":"").'}); '.
            '});</script>';


   $compile.='<div class="dt-countdown">'.
   '<div id="countdown_'.$dt_el_id.'" class="countdown"></div></div>';

       if(!empty($countdown_box_color)){

            $detheme_Style[]='#countdown_'.$dt_el_id.' .countdown-amount{border-color: '.$countdown_box_color.';}';
        }


        if(!empty($countdown_text_color)){
            $detheme_Style[]='#countdown_'.$dt_el_id.' .countdown-amount{color: '.$countdown_text_color.';}';
        }

        if(!empty($countdown_label_color)){
            $detheme_Style[]='#countdown_'.$dt_el_id.' .countdown-period{color: '.$countdown_label_color.';}';
        }

        if(vc_is_inline()){
            $compile.="<style type=\"text/css\">".@implode("\n",$detheme_Style)."</style>";
        }
    }

   return $compile;

}

add_shortcode('dt_countdown', 'dt_countdown_shortcode');



if(shortcode_exists('dt_social'))
    remove_shortcode('dt_social');

function vc_dt_social_shortcode($atts, $content = null){

    global $dt_el_id,$detheme_Style;

   if(!isset($dt_el_id)) $dt_el_id=0;

   $dt_el_id++;

   extract(shortcode_atts(array(

    'facebook'=>'',
    'twitter'=>'',
    'gplus'=>'',
    'pinterest'=>'',
    'linkedin'=>'',
    'color'=>'',
    'shape'=>'circle',
    'size'=>'medium',
    'bg_color'=>'',
    'align'=>'center'

    ), $atts));

   $compile="";
   $colorstyle="";
   $backgroundcolor="";


   if(vc_is_inline()){
       if(''!=$color){
            $colorstyle=" style=\"color:".$color.";\"";
       }
       if(''!=$bg_color){
            $backgroundcolor=" style=\"background-color:".$bg_color.";\"";
       }

   }
   else{

       if(''!=$color){
            $detheme_Style[]="#social-".$dt_el_id." li a{color:".$color.";}";
       }
       if(''!=$bg_color){
            $detheme_Style[]="#social-".$dt_el_id." li{background-color:".$bg_color.";}";
       }
    }

    $compile.="<ul id=\"social-".$dt_el_id."\" class=\"dt-social shape-".$shape." size-".$size." align-".$align."\">".
    (($facebook)?"<li".$backgroundcolor."><a href=\"".$facebook."\" target=\"_blank\"".$colorstyle."><i class=\"fontelloicon-facebook\"></i></a></li>":"").
    (($twitter)?"<li".$backgroundcolor."><a href=\"".$twitter."\" target=\"_blank\"".$colorstyle."><i class=\"fontelloicon-twitter\"></i></a></li>":"").
    (($gplus)?"<li".$backgroundcolor."><a href=\"".$gplus."\" target=\"_blank\"".$colorstyle."><i class=\"fontelloicon-gplus\"></i></a></li>":"").
    (($linkedin)?"<li".$backgroundcolor."><a href=\"".$linkedin."\" target=\"_blank\"".$colorstyle."><i class=\"fontelloicon-linkedin\"></i></a></li>":"").
    (($pinterest)?"<li".$backgroundcolor."><a href=\"".$pinterest."\" target=\"_blank\"".$colorstyle."><i class=\"fontelloicon-pinterest\"></i></a></li>":"").
    "</ul>";


   return  $compile;

}

add_shortcode('dt_social', 'vc_dt_social_shortcode');
?>