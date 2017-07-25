<?php
defined('ABSPATH') or die();

function billio_year_shortcode() {
	$year = date('Y');
	return $year;
}

add_shortcode('current-year', 'billio_year_shortcode');

function billio_site_title_shortcode() {
	$result = get_bloginfo('name');
	return $result;
}

add_shortcode('site-title', 'billio_site_title_shortcode');

function billio_site_tagline_shortcode() {
	$result = get_bloginfo('description');
	return $result;
}

add_shortcode('site-tagline', 'billio_site_tagline_shortcode');

function billio_site_url_shortcode($atts) {
	extract( shortcode_atts( array(
		'title' => home_url(),
		'target' => '',
		'class' => '',
	), $atts, 'site-url' ) );

	$result = '<a href="'.home_url().'" title="'.esc_attr($title).'" target="'.esc_attr($target).'" class="'.sanitize_html_class($class).'">'.$title.'</a>';
	return $result;
}

add_shortcode('site-url', 'billio_site_url_shortcode');

function billio_wp_url_shortcode($atts) {
	extract( shortcode_atts( array(
		'title' => site_url(),
		'target' => '',
		'class' => '',
	), $atts, 'wp-url' ) );

	$result = '<a href="'.site_url().'" title="'.esc_attr($title).'" target="'.esc_attr($target).'" class="'.sanitize_html_class($class).'">'.$title.'</a>';
	return $result;
}

add_shortcode('wp-url', 'billio_wp_url_shortcode');

function billio_theme_url_shortcode($atts) {
	extract( shortcode_atts( array(
		'title' => get_template(),
		'target' => '',
		'class' => '',
	), $atts, 'theme-url' ) );

	$result = '<a href="'.get_template_directory_uri().'" title="'.esc_attr($title).'" target="'.esc_attr($target).'" class="'.sanitize_html_class($class).'">'.$title.'</a>';
	return $result;
}

add_shortcode('theme-url', 'billio_theme_url_shortcode');

function billio_login_url_shortcode($atts) {
	extract( shortcode_atts( array(
		'title' => wp_login_url(),
		'target' => '',
		'class' => '',
	), $atts, 'login-url' ) );

	$result = '<a href="'.wp_login_url().'" title="'.esc_attr($title).'" target="'.esc_attr($target).'" class="'.sanitize_html_class($class).'">'.$title.'</a>';
	return $result;
}

add_shortcode('login-url', 'billio_login_url_shortcode');

function billio_logout_url_shortcode($atts) {
	extract( shortcode_atts( array(
		'title' => wp_logout_url(),
		'target' => '',
		'class' => '',
	), $atts, 'logout-url' ) );

	$result = '<a href="'.wp_logout_url().'" title="'.esc_attr($title).'" target="'.esc_attr($target).'" class="'.sanitize_html_class($class).'">'.$title.'</a>';
	return $result;
}
add_shortcode('logout-url', 'billio_logout_url_shortcode');


if (is_plugin_active('woocommerce/woocommerce.php')) {

function billio_featured_products($atts, $content = null){

		global $woocommerce_loop,$dt_featured,$detheme_Scripts;

        if(!isset($dt_featured)){

            $dt_featured=1;

        }

        else{

            $dt_featured++;

        }


		extract( shortcode_atts( array(
			'per_page' 	=> '12',
			'columns' 	=> '4',
			'orderby' 	=> 'date',
			'order' 	=> 'desc'
		), $atts ) );

		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $per_page,
			'orderby' 				=> $orderby,
			'order' 				=> $order,
			'meta_query'			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array('catalog', 'visible'),
					'compare'	=> 'IN'
				),
				array(
					'key' 		=> '_featured',
					'value' 	=> 'yes'
				)
			)
		);

		if(!in_array($columns,array(1,2,3,4,6))){
			$columns=3;
		}

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		$widgetID="featured".$dt_featured;
		$woocommerce_loop['columns'] = 1;

		if ( $products->have_posts() ) :

            wp_register_script( 'owl.carousel', get_template_directory_uri() . '/js/owl.carousel.js', array( 'jquery' ), '', true );
            wp_enqueue_script( 'owl.carousel');


	          $compile='<div class="dt-featured-product">
               <div class="row"><div id="'.$widgetID.'" class="products">';

			while ( $products->have_posts() ) : $products->the_post(); 

					ob_start();
					wc_get_template_part( 'content', 'product-carousel' );
					$wooitem=ob_get_contents();
					ob_end_clean();
					$compile.=$wooitem;

				endwhile; // end of the loop.



			wp_reset_postdata();

            $compile.='</div></div></div>';

            $script='jQuery(document).ready(function($) {
            \'use strict\';
            var '.$widgetID.' = $("#'.$widgetID.'.products");
		    var navigation=$(\'<div></div>\').addClass(\'owl-carousel-navigation\'),
	        prevBtn=$(\'<a></a>\').addClass(\'btn btn-owl\'),
	        nextBtn=prevBtn.clone();
	        navigation.append(prevBtn.addClass(\'button prev\'),nextBtn.addClass(\'button next\'));
	        '.$widgetID.'.parent().append(navigation);

            try{
           '.$widgetID.'.owlCarousel({
                items       : '.$columns.', itemsDesktop    : [1200,'.max(min('3',$columns-1),1).'], itemsDesktopSmall : [1023,'.max(min('2',$columns-1),1).'], itemsTablet : [768,'.max(min('2',$columns-1),1).'], itemsMobile : [600,1], pagination  : false, slideSpeed  : 400});
            nextBtn.click(function(){
                '.$widgetID.'.trigger(\'owl.next\');
              });
            prevBtn.click(function(){
                '.$widgetID.'.trigger(\'owl.prev\');
              });
            '.$widgetID.'.owlCarousel(\'reload\');
            }
            catch(err){}

            });';

        array_push($detheme_Scripts,$script);

		return '<div class="container woocommerce">' . $compile . '</div>';
		endif;
		wp_reset_postdata();

		return "";
}

function billio_product_categories($atts, $content = null){

		global $woocommerce_loop,$dt_featured,$detheme_Scripts;

        if(!isset($dt_featured)){

            $dt_featured=1;

        }

        else{

            $dt_featured++;

        }

		extract( shortcode_atts( array(
			'number'     => null,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'columns' 	 => '4',
			'hide_empty' => 1,
			'parent'     => ''
		), $atts ) );

		if ( isset( $atts[ 'ids' ] ) ) {
			$ids = explode( ',', $atts[ 'ids' ] );
			$ids = array_map( 'trim', $ids );
		} else {
			$ids = array();
		}

		$hide_empty = ( $hide_empty == true || $hide_empty == 1 ) ? 1 : 0;

		// get terms and workaround WP bug with parents/pad counts
		$args = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
			'include'    => $ids,
			'pad_counts' => true,
			'child_of'   => $parent
		);

		$product_categories = get_terms( 'product_cat', $args );

		if ( $parent !== "" ) {
			$product_categories = wp_list_filter( $product_categories, array( 'parent' => $parent ) );
		}

		if ( $hide_empty ) {
			foreach ( $product_categories as $key => $category ) {
				if ( $category->count == 0 ) {
					unset( $product_categories[ $key ] );
				}
			}
		}

		if ( $number ) {
			$product_categories = array_slice( $product_categories, 0, $number );
		}

		$widgetID="featured".$dt_featured;
		$woocommerce_loop['columns'] = 1;
		$compile='';


		$woocommerce_loop['loop'] = $woocommerce_loop['column'] = '';

		if ( $product_categories ) {


            wp_register_script( 'owl.carousel', get_template_directory_uri() . '/js/owl.carousel.js', array( 'jquery' ), '', true );
            wp_enqueue_script( 'owl.carousel');

            wp_register_style('owl.carousel',get_template_directory_uri() . '/css/owl.carousel.css', array(), '', 'all');
	        wp_enqueue_style('owl.carousel');


	        $compile='<div class="dt-shop-category" dir="ltr">
               <div class="row"><div id="'.$widgetID.'" class="category-items">';


			foreach ( $product_categories as $category ) {

					ob_start();
					wc_get_template( 'content-product_cat_carousel.php', array(
					'category' => $category
				) );

					$wooitem=ob_get_contents();
					ob_end_clean();
					$compile.=$wooitem;


			}

			woocommerce_reset_loop();

	        $compile.='</div></div></div>';

            $script='jQuery(document).ready(function($) {
            \'use strict\';
            var '.$widgetID.' = $("#'.$widgetID.'.category-items");
		    var navigation=$(\'<div></div>\').addClass(\'owl-carousel-navigation\'),
	        prevBtn=$(\'<span></span>\').addClass(\'btn btn-owl\'),
	        nextBtn=$(\'<span></span>\').addClass(\'btn btn-owl\')
	        navigation.append(prevBtn.addClass(\'button prev\'),nextBtn.addClass(\'button next\'));
	        '.$widgetID.'.parent().append(navigation);

            try{
           '.$widgetID.'.owlCarousel({
                items       : '.$columns.', itemsDesktop    : [1200,'.max(min('3',$columns-1),1).'], itemsDesktopSmall : [1023,'.max(min('2',$columns-1),1).'], itemsTablet : [768,'.max(min('2',$columns-1),1).'], itemsMobile : [600,1], pagination  : false, slideSpeed  : 400});
            nextBtn.click(function(){
                '.$widgetID.'.trigger(\'owl.next\');
              });
            prevBtn.click(function(){
                '.$widgetID.'.trigger(\'owl.prev\');
              });
            '.$widgetID.'.owlCarousel(\'reload\');
            }
            catch(err){}

            });';

        array_push($detheme_Scripts,$script);

		return '<div class="woocommerce">' . $compile . '</div>';

		}

		woocommerce_reset_loop();

		return '';

}

function add_billio_featured_shortcode($content){

	add_shortcode('featured_products', 'billio_featured_products');
	add_shortcode('product_categories', 'billio_product_categories');
	return $content;
}

add_filter('the_content', 'add_billio_featured_shortcode', 1); 

}

function billio_shortcode_lang( $arr )
{
    $arr[] = dirname(__FILE__). '/customcodes.string.php';
    return $arr;
}


function add_billio_shortcode_plugin($plugin_array) { 

	if ( floatval(get_bloginfo('version')) >= 3.9){
	   $plugin_array['dtshortcode']=get_template_directory_uri().'/lib/customcodes.js.php';
	}else{
	   $plugin_array['dtshortcode']=get_template_directory_uri().'/lib/customcodes.js.old.php';
	}

	add_filter( 'mce_external_languages', 'billio_shortcode_lang', 10, 1 );
   return $plugin_array;  
}

function register_billio_shortcode_button($buttons) {  
   array_push($buttons, "dtshortcode");  
   return $buttons;  
}  



function add_billio_shortcode_button() {  

       if ( !current_user_can('edit_posts') &&  !current_user_can('edit_pages') )  {
       	return;
       }

		if ( 'true' == get_user_option( 'rich_editing' ) ) {
         add_filter('mce_external_plugins', 'add_billio_shortcode_plugin');  
         add_filter('mce_buttons', 'register_billio_shortcode_button');  
       }  
} 

add_action('admin_head', 'add_billio_shortcode_button'); 
add_action('wp_ajax_billio_get_shortcode','render_billio_shortcode_panel');


function render_billio_shortcode_panel(){

	locate_template('lib/shortcode_panel.php',true);
	die();
}


function billio_icon_shortcode($atts) {
	extract( shortcode_atts( array(
		'ico' => '',
		'color' => '',
		'size' => '',
		'style' => '',
	), $atts));

	$result="";
	$class=array();
	if(!empty($ico)) $class[]=$ico;
	if(!empty($size)) $class[]=$size;
	if(!empty($style)) $class[]="dt-icon-".$style;
	if(!empty($color) && $style!=='ghost') $class[]=$color."-color";

	if(count($class)){

		$result = '<i class="dt-icon '.@implode(" ",$class).'"></i>';
	}

	return $result;
}

add_shortcode('dticon', 'billio_icon_shortcode');


function billio_button_shortcode($atts, $content = null, $base = '') {

    extract( shortcode_atts( array(
      'url' => '',
      'target' => '',
      'size' => '',
      'style' => 'ghost',
      'skin' => '',
    ), $atts));

    $result="";

    $class=array('btn');

    if(!empty($ico)) $class[]=$ico;
    if(!empty($size)) $class[]=$size;
    if(!empty($style)) $class[]="btn-".$style;
    if(!empty($skin)) $class[]="skin-".$skin;

    if(count($class)){

      $result = '<a '.(!empty($url)?"href=\"".esc_url($url)."\"":"").' class="'.@implode(" ",$class).'" target="'.esc_attr($target).'">'.$content.'</a>';
    }

    return $result;

}

add_shortcode('dt_button', 'billio_button_shortcode');

function billio_counto_shortcode($atts) {

	extract( shortcode_atts( array(
		'to' => '100',
		'from' => 0,
		'sepcolor'=>'',
		'el_id'=>'',
	), $atts, 'dt_counto' ) );

	if($sepcolor!=''){
		$sepcolor = ltrim( $sepcolor, '#' );

		if(preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', "#".$sepcolor ) ){

			if($el_id==''){

				global $dt_featured,$detheme_Style;

   				if(!isset($dt_featured)){
   					$dt_featured=1;
   				}	else
   					{
					$dt_featured++;
				}

				$el_id="counto_".$dt_featured;
			}
			$detheme_Style[]="#$el_id:after{background-color:#$sepcolor;}";

		}

	}

	$result = '<span '.($el_id!=''?'id="'.$el_id.'" ':"").'class="dt-counto" data-to="'.intval($to).'"></span>';
	return $result;
}

add_shortcode('dt_counto', 'billio_counto_shortcode');

if (is_plugin_active('billio_vc_addon/billio_vc_addon.php')) {

function billio_section_header_shortcode($atts, $content = null, $base = ''){


        wp_enqueue_style('detheme-vc');

        $compile='';
        global $dt_main_heading_ID,$detheme_Style;

        if(!isset($dt_main_heading_ID)) $dt_main_heading_ID=0;

        $dt_main_heading_ID++;

        extract(shortcode_atts(array(
            'main_heading' => '',
            'layout_type'=>'section-heading-border',
            'text_align'=>'center',
            'color'=>'',
            'font_size'=>'default',
            'font_weight'=>'',
            'font_style'=>'',
            'custom_font_size'=>'',
            'separator_position'=>'center',
            'use_decoration'=>false,
            'line_height'=>'',
            'separator_color'=>'#444444',
            'spy'=>'',
            'scroll_delay'=>300,
            'css'=>''
        ), $atts));

        $heading_style=array();
        $sectionid="section-".$dt_main_heading_ID;

        $heading_css=array('dt-section-head',$text_align);

       if($el_class = function_exists('vc_set_as_theme') ? vc_shortcode_custom_css_class($css):false){
            $detheme_Style[]=$css;
	        array_push($heading_css,$el_class);
       }

        if(!empty($color)){
            $heading_style['color']="color:".$color;
        }


        if(!empty($font_weight) && $font_weight!='default'){
            $heading_style['font-weight']="font-weight:".$font_weight;
        }


        if(!empty($line_height) && $line_height!='default'){
            $heading_style['line-height']="line-height:".$line_height."px";
        }

        if(!empty($font_style) && $font_style!='default'){
            $heading_style['font-style']="font-style:".$font_style;
        }

        if(!empty($custom_font_size) && $font_size=='custom'){
            $custom_font_size=(preg_match('/(px|pt)$/', $custom_font_size))?$custom_font_size:$custom_font_size."px";
            $heading_style['font-size']="font-size:".$custom_font_size;
        }

		if('default'!=$font_size || 'custom'!=$font_size){
			array_push($heading_css,"size-".$font_size);
		}

        $scollspy="";


        if('none'!==$spy && !empty($spy)){
            wp_enqueue_style('scroll-spy');
            wp_enqueue_script('ScrollSpy');

            $scollspy=' data-uk-scrollspy="{cls:\''.$spy.'\''.(($scroll_delay)?', delay:'.$scroll_delay:"").'}"';
        }


        if($use_decoration){

            $decoration_position=$after_heading="";

            if($layout_type=='section-heading-polkadot-two-bottom'){
                $decoration_position="polka-".$separator_position;
            }
            elseif($layout_type=='section-heading-thick-border'){
                $decoration_position="thick-".$separator_position;
            }
            elseif($layout_type=='section-heading-thin-border'){
                $decoration_position="thin-".$separator_position;
            }
            elseif($layout_type=='section-heading-double-border-bottom'){
                $decoration_position="double-border-bottom-".$separator_position;
            }
            elseif($layout_type=='section-heading-thin-border-top-bottom'){
                $decoration_position="top-bottom-".$separator_position;
            }

            if('section-heading-triple-dots'==$layout_type || 'section-heading-triple-dashes'==$layout_type || 'section-heading-triple-square-dots'==$layout_type){
            	$decoration_position="decoration-".$separator_position;	
            }

            if($layout_type=='section-heading-swirl' || $layout_type=='section-heading-wave'){
				array_push($heading_css,$layout_type);
			}

			if('section-heading-polkadot-left-right'==$layout_type || 
				'section-heading-horizontal-line-fullwidth'==$layout_type){
				array_push($heading_css,'hide-overflow');
			}


           if(!empty($separator_color)){
                $heading_style['border-color']="border-color:".$separator_color;

                switch ($layout_type) {
                    case 'section-heading-border-top-bottom':
                    case 'section-heading-thin-border-top-bottom':
                    case 'section-heading-thick-border':
                    case 'section-heading-thin-border':
                    case 'section-heading-double-border-bottom':
                    case 'section-heading-swirl':
                        $detheme_Style[]="#".$sectionid." h2:after,#".$sectionid." h2:before{background-color:".$separator_color.";}";
                        break;
                    case 'section-heading-colorblock':
                        $detheme_Style[]="#".$sectionid." h2{background-color:".$separator_color.";}";
                        break;
                    case 'section-heading-point-bottom':
                        $detheme_Style[]="#".$sectionid." h2:before{border-top-color:".$separator_color.";}";
                        break;
                    case 'section-heading-horizontal-line':
                    case 'section-heading-horizontal-line-fullwidth':
                        $detheme_Style[]="#".$sectionid." .".$layout_type.":before,#".$sectionid." .".$layout_type.":after{background-color:".$separator_color.";}";
                        break;
                    case 'section-heading-triple-dots':
                    case 'section-heading-triple-dashes':
        			case 'section-heading-triple-square-dots':
                        $detheme_Style[]="#".$sectionid." h2:after{color:".$separator_color.";}";
        				break;
                    default:
                        break;
                }

            }

            if($layout_type=='section-heading-swirl'){

                $after_heading.='<svg viewBox="0 0 '.(($text_align=='left')?"104":($text_align=='right'?"24":"64")).' 22"'.($separator_color!=''?" style=\"color:".$separator_color."\"":"").'>
                <use xlink:href="'.DETHEME_VC_DIR_URL.'images/source.svg#swirl"></use>
            </svg>';
            }elseif($layout_type=='section-heading-wave'){
                $after_heading.='<svg viewBox="0 0 '.(($text_align=='left')?"126":($text_align=='right'?"2":"64")).' 30"'.($separator_color!=''?" style=\"color:".$separator_color."\"":"").'>
                <use xlink:href="'.DETHEME_VC_DIR_URL.'images/source.svg#wave"></use>
            </svg>';
            }


            $compile.=
            '<div id="'.$sectionid.'" class="'.@implode(" ",$heading_css).'" dir="ltr"'.$scollspy.'>
            <div class="dt-section-container"><h2 class="section-main-title '.$layout_type.' '.$decoration_position.'"'.(count($heading_style)?" style=\"".@implode(";",$heading_style)."\"":"").'>
                '.$main_heading.'
            </h2>'.$after_heading.'
            </div></div>';

        }
        else{

        $compile.='<div id="'.$sectionid.'" class="'.@implode(" ",$heading_css).'" dir="ltr"'.$scollspy.'>
                <div>'.
                ((!empty($main_heading))?'<h2'.(count($heading_style)?" style=\"".@implode(";",$heading_style)."\"":"").' class="section-main-title">'.$main_heading.'</h2>':'').
                '</div>
        </div>';              
        }

        return $compile;

}

add_shortcode('section_header', 'billio_section_header_shortcode');

}

function billio_dt_career_shortcode($atts, $content = null, $base = ''){


            global $dt_career_id;

            extract(shortcode_atts(array(

            'cat' => '',
            'career_num' => 10,
            'speed'=>800,
            'autoplay'=>'0',
            'spy'=>'none',
            'scroll_delay'=>300,
            'layout'=>'carousel',
            'posts_per_page'=>'3',
            'column'=>'4',
            'full_column'=>4,
            'desktop_column'=>3,
            'small_column'=>2,
            'tablet_column'=>2,
            'mobile_column'=>1,
            'show_link'=>'yes',
            'isotope_type'=>'masonry',
            'gutter'=>40,
            'show_all_field'=>'yes',
            'show_all_label'=>'',
            'jobs'=>'',
            'show_filter'=>'no'

            ), $atts));


          if(!isset($dt_career_id))
             $dt_career_id=0;

          $dt_career_id++;

          $show_all_field=$show_all_field=='yes';

          if(vc_is_inline()){

              $dt_career_id.="_".time().rand(0,100);
          }

          $widgetID="dt_career".$dt_career_id;

          if(preg_match('/^,/i', $cat)){
            $cat="";
          }



          $queryargs = array(
                  'post_type' => 'dtcareer',
                  'no_found_rows' => false,
                  'posts_per_page'=>$career_num,
                  'compile'=>'',
                  'script'=>''
              );

          if(!empty($cat)){
                  $queryargs['tax_query']=array(
                                  array(
                                      'taxonomy' => 'dtcareer_cat',
                                      'field' => 'id',
                                      'terms' =>@explode(',',$cat)
                                  )
                              );

          }

          $query = new WP_Query( $queryargs );  
          $compile="";

          if ( $query->have_posts() ){


            if('carousel'==$layout){

                    if(!is_admin()){
                        wp_register_style('owl.carousel',DETHEME_VC_DIR_URL."css/owl.carousel.css",array());
                        wp_enqueue_style('owl.carousel');


                        wp_register_script( 'owl.carousel', DETHEME_VC_DIR_URL . 'js/owl.carousel.js', array('jquery'), '1.29', true );
                        wp_enqueue_script('owl.carousel');

                       if('none'!==$spy && !empty($spy)){
                                wp_enqueue_style('scroll-spy');
                                wp_enqueue_script('ScrollSpy');
                       }

                    }

                  $compile='<div class="dt-career-container">
                <div class="owl-carousel" id="'.$widgetID.'">';


                      $spydly=0;
                      $portspty=1;


                      while ( $query->have_posts() ) : 
                      $query->the_post();

                          $owlitem = apply_filters( 'vc_career_get_item',"",$query->post);


                          $spydly=$spydly+(int)$scroll_delay;
                          $scollspy="";


                         if('none'!==$spy && !empty($spy) && $portspty < $full_column ){

                              $scollspy='data-uk-scrollspy="{cls:\''.$spy.'\', delay:'.$spydly.'}"';
                          }

                          if($owlitem!=''){
                            $compile.='<div class="career-item"'.$scollspy.'><div class="career-item-wrap">'.$owlitem.'</div></div>';

                          }
                          else{

							  $fielsshow=get_dtcareer_jobs_value();

							  $jobfields=@explode(",",trim($jobs));


	                          $out="<h2 class=\"career-isotope-title\">".get_the_title()."</h2>";
						      $out.="<div class=\"career-isotope-excerpt\">".get_the_excerpt()."</div>";

						      if(count($jobfields) && count($fielsshow)) {
							      $out.="<ul class=\"career-isotope-job-field\">";

							      foreach ($fielsshow as $key => $jobfield) {
							      	if(in_array($key,$jobfields) && $jobfield['value']!=''){
								      	$out.="<li class=\"field-".$key."\">".(isset($jobfield['icon']) && $jobfield['icon']!=''?"<i class=\"".$jobfield['icon']."\"></i>":"").$jobfield['value']."</li>";
							      	}
							      }

							      $out.="</ul>";
						      }

						      $out.="<a href=\"".get_the_permalink()."\" class=\"btn btn-md btn-ghost skin-dark career-isotope-button\">".__("i'm Interested",'billio')."</a>";


	                           $compile.='<div class="career-item"'.$scollspy.'><div class="career-item-wrap">'.$out.'</div></div>';

                          }
                       endwhile;

                       $compile.="</div></div>";

                      $script='<script type="text/javascript">'."\n".'jQuery(document).ready(function($) {
                          \'use strict\';'."\n".'
                          var '.$widgetID.' = jQuery("#'.$widgetID.'");
                          try{
                         '.$widgetID.'.owlCarousel({
                              items       : '.$full_column.', 
                              itemsDesktop    : [1200,'.$desktop_column.'], 
                              itemsDesktopSmall : [1023,'.$small_column.'], // 3 items betweem 900px and 601px
                              itemsTablet : [768,'.$tablet_column.'], //2 items between 600 and 0;
                              itemsMobile : [600,'.$mobile_column.'], // itemsMobile disabled - inherit from itemsTablet option
                              '.($autoplay?'autoPlay:'.($speed+1000).',':'')."
                              slideSpeed  : ".$speed.",";
                        $script.='});';

                       $script.='}
                          catch(err){}
                      });</script>';

                   $compile.=$script;   

              }
              else{

                wp_enqueue_script( 'isotope.pkgd' , DETHEME_VC_DIR_URL. '/js/isotope.pkgd.js', array( ), '2.0.0', true );
                wp_enqueue_script( 'dt-career' , DETHEME_VC_DIR_URL. '/js/dtcareer.js', array('jquery'), '2.0.0', true );

                $career_items=$categories_filter=array();
                $navbar="";

                 while ( $query->have_posts() ) : 
                      $query->the_post();

                          $careeritem = apply_filters( 'vc_career_get_item',"",$query->post);
                          $terms = get_the_terms(get_the_ID(), 'dtcareer_cat' );
                          $itemClas=array();

                          if($terms && count($terms)){
                              foreach ($terms as $term_id=>$term) {

                                $itemClas[$term->slug]=sanitize_html_class($term->slug);

                                if($show_filter=='yes'){
                                  $categories_filter[$term->slug]='<li><a href="#" data-filter=".'.sanitize_html_class($term->slug).'">'.$term->name.'</a></li>';
                                }
                              }
                            }

                          if($careeritem!=''){
                            $career_items[]='<div class="career-item '.@implode(' ', $itemClas).'"><div class="career-item-wrap">'.$careeritem.'</div></div>';

                          }
                          else{

							  $fielsshow=get_dtcareer_jobs_value();

							  $jobfields=@explode(",",trim($jobs));


	                          $out="<h2 class=\"career-isotope-title\">".get_the_title()."</h2>";
						      $out.="<div class=\"career-isotope-excerpt\">".get_the_excerpt()."</div>";

						      if(count($jobfields) && count($fielsshow)) {
							      $out.="<ul class=\"career-isotope-job-field\">";

							      foreach ($fielsshow as $key => $jobfield) {
							      	if(in_array($key,$jobfields) && $jobfield['value']!=''){
								      	$out.="<li class=\"field-".$key."\">".(isset($jobfield['icon']) && $jobfield['icon']!=''?"<i class=\"".$jobfield['icon']."\"></i>":"").$jobfield['value']."</li>";
							      	}
							      	

							      }
							      
							      $out.="</ul>";
						      }
						      $out.="<a href=\"".get_the_permalink()."\" class=\"btn btn-md btn-ghost skin-dark career-isotope-button\">".__("i'm Interested",'billio')."</a>";


                          $career_items[]='<div class="career-item '.@implode(' ', $itemClas).'"><div class="career-item-wrap">'.$out.'</div></div>';

                          }


                  endwhile;

                if(count($career_items)):


                  if($show_filter=='yes' && count($categories_filter)){


                      $navbar='<nav id="career-work-navbar" class="navbar navbar-default" role="navigation">
                      <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#dt-career-filter">
                          <span class="sr-only">'.__('Toggle navigation','billio').'</span>
                          <span class="icon-menu"></span>
                        </button>
                      </div>
                     <div class="collapse navbar-collapse" id="dt-career-filter">
                      <ul id="career-filter" data-isotope="dtcareers" class="dt-career-filter nav navbar-nav">
                      '.($show_all_field?'<li class="active"><a href="#" data-filter="*" class="active show-all">'.($show_all_label!=''? $show_all_label :__('All Jobs','billio')).'</a></li>':'');
                      $navbar.=@implode("\n",$categories_filter);
                      $navbar.='</ul></div></nav>';
                    }


                  $compile.='<div class="dtcareers">';
                  $compile.=$navbar;
                  $compile.='<div id="dtcareers" data-gutter="'.intVal($gutter).'" data-type="'.$isotope_type.'" data-col="'.esc_attr($column).'" class="dtcareers-container">';
                  $compile.=@implode("",$career_items);
                  $compile.='</div></div>';
    
                 endif;
    
              }
          }
          wp_reset_query();
          return $compile;
}

add_shortcode('dt_career','billio_dt_career_shortcode');

/* render shortcode on widget text */
add_filter('widget_text',create_function('$s', 'return do_shortcode($s);'));

?>