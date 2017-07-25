<?php
	defined('ABSPATH') or die();

if(shortcode_exists('dt_reportlist'))
    remove_shortcode('dt_reportlist');

add_shortcode('dt_reportlist', 'vc_reportlist_shortcode');

function vc_reportlist_shortcode($atts, $content = null) {

            global $dt_el_id,$detheme_Style;

            if (defined('DETHEME_VC_DIR_URL')) {
                wp_enqueue_style('detheme-vc');
                wp_enqueue_style('scroll-spy');


                wp_register_script('jquery.appear',DETHEME_VC_DIR_URL."js/jquery.appear.js",array());
                wp_register_script('jquery.counto',DETHEME_VC_DIR_URL."js/jquery.counto.js",array());
                wp_register_script('dt-iconbox',DETHEME_VC_DIR_URL."js/dt_iconbox.js",array('jquery.appear','jquery.counto'));

                wp_enqueue_script('ScrollSpy');
            }

            if (!isset($compile)) {$compile='';}

            extract( shortcode_atts( array(
                'tag'               => '',
                'meta_key'          => 'dt_report_pre_title',
                'orderby'           => 'meta_value',
                'order'             => 'DESC',
                'columns'           => '',
                'posts_per_page'    => 10,
                'categories'        => '',
                'spy'               => 'none',
                'spydelay'          => 300,
                'css'               => ''
            ), $atts )  );

            $content=(empty($content) && !empty($iconbox_text))?$iconbox_text:$content;

            if(!isset($dt_el_id) || empty($dt_el_id))
                $dt_el_id=0;

            $orderby = ($orderby=='pretitle') ? 'meta_value' : $orderby;

            $tax_query = null;
            if(!empty($categories)) {
                $categories = explode(',',$categories);    
                $tax_query = array(array(
                        'taxonomy' => 'dtreportpostcat',
                        'field'    => 'id',
                        'terms'    => $categories,
                    ));
            }
            
            $queryargs = array(
                'post_type'         => 'dtreportpost',
                'meta_key'          => $meta_key,
                'orderby'           => $orderby,
                'order'             => $order,
                'posts_per_page'    => $posts_per_page,
                'tax_query'         => $tax_query,
                'paged'             => get_query_var( 'paged' )
            );

            $query = new WP_Query( $queryargs );    
            $compile="";

            if ( $query->have_posts() ) :
                $scollspy="";
                $spydly=0;

                if('none'!==$spy && !empty($spy)){
                    $spydly=$spydly+(int)$spydelay;
                    $scollspy='data-uk-scrollspy="{cls:\''.$spy.'\', delay:'.$spydly.'}"';
                }

                while ( $query->have_posts() ) :
                    $query->the_post();

                    $imageurl = "";
                    $alt_image = "";
                    $document_url       = "";
                    $document_extension = "";
                    $document_icon      = "";
                    $pre_title          = ""; 
                    $button_label       = "";

                    if (isset($query->post->ID)) {
                        /* Get Image from featured image */
                        $thumb_id = get_post_thumbnail_id($query->post->ID);
                        $featured_image = wp_get_attachment_image_src($thumb_id,'full',false); 
                        if (isset($featured_image[0])) {
                            $imageurl = $featured_image[0];
                        }

                        $alt_image = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);

                        $post_id = $query->post->ID;
                        
                        $document_url       = get_post_meta($post_id,'dt_report_document_url',true);
                        $document_extension = get_post_meta($post_id,'dt_report_document_extension',true);
                        $document_icon      = get_post_meta($post_id,'dt_report_document_icon',true);
                        $pre_title          = get_post_meta($post_id,'dt_report_pre_title',true); 
                        $button_label       = get_post_meta($post_id, 'dt_report_button_label', true );
                    }

                    $colsm = '';

                    // before report item
                    $i = is_int($query->current_post) ? $query->current_post : 0;
                    $j = $i % 2;
                    if ($j==0) {
                        $compile .= '<div class="row equal_height">';
                    }


                    // content report
                    $compile .= '<div id="report-'.$query->post->ID.'" class="col-xs-12 col-sm-6 equal_height_item" '.$scollspy.'>
                        <div class="row dt_report_item">'; 
                    if ($imageurl!="") { 
                        $colsm = 'col-sm-6';
                        $compile .= '<div class="col-xs-12 col-sm-6">
                                <img class="img-responsive" alt="'. esc_attr($alt_image) . '" src="'.$imageurl.'">
                            </div>';

                    } //if ($imageurl!="")
                            
                        $compile .= '<div class="col-xs-12 '. sanitize_html_class($colsm) . '">
                                <h3 class="dt_report_pre_title">'. $pre_title .'</h3>
                                <h2 class="dt_report_title">'. get_the_title($query->post->ID) .'</h2>
                                <div class="dt_report_content">'. get_the_content($query->post->ID) .'</div>';
                        if (!empty($document_url)) { 
                            $compile .= '<div class="dt_report_button">
                                    <a href="'. esc_url($document_url) .'" target="_blank"><i class="'. sanitize_html_class($document_icon).'"></i>'. $button_label . ' ' . $document_extension .'</a>
                                </div>';
                        }
                        
                        $compile .= '</div>
                        </div>
                    </div>';

                    // after report item
                    $count = is_int($query->post_count) ? $query->post_count : 0;
                    if (($j!=0)||($count==($i+1))) {                    
                        $compile .= '</div>';
                    }
                endwhile;

                $compile .= '<div class="dt_report_pagination" dir="ltr">';
                $pagination = paginate_links( apply_filters( 'dt_report_pagination_args', array(
                            'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
                            'format'       => '',
                            'add_args'     => '',
                            'current'      => max( 1, get_query_var( 'paged' ) ),
                            'total'        => $query->max_num_pages,
                            'prev_text'    => '<span></span>',
                            'next_text'    => '<span></span>',
                            //'prev_text'    => is_rtl() ? '&#9658;' : '&#9664;',
                            //'next_text'    => is_rtl() ? '&#9664;' : '&#9658;',
                            'type'         => 'array',
                            'end_size'     => 3,
                            'mid_size'     => 3
                        ) ) );

                if(is_array($pagination)){
                    $compile .= join("\n",is_rtl()?array_reverse($pagination):$pagination);
                }

                $compile .= '</div>';
            endif; //if ( $query->have_posts() )

            $dt_el_id++;

            $excss="";
            if((''!=$css)and(function_exists('vc_shortcode_custom_css_class'))){
                $excss=vc_shortcode_custom_css_class($css);
                $detheme_Style[]=$css;
            }

            $detheme_Style[]="#module_dt_reportlist_".$dt_el_id." {}";

            return "<div id=\"module_dt_reportlist_".$dt_el_id."\" class=\"module_dt_reportlist".(''!=$excss?" ".$excss:"")."\">".$compile."</div>";
}

?>