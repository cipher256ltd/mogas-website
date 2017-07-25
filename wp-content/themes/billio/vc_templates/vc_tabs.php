<?php
$output = $title = $interval = $el_class = '';
extract(shortcode_atts(array(
    'title' => '',
    'interval' => 0,
    'el_class' => ''
), $atts));

wp_enqueue_script('jquery-ui-tabs');

$el_class = $this->getExtraClass($el_class);

$element = 'wpb_tabs';
if ( 'vc_tour' == $this->shortcode) $element = 'wpb_tour';

// Extract tab titles
preg_match_all( '/vc_tab title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE );
$tab_titles = array();



$regexshortcodes=
'\\['                              	// Opening bracket
. '(\\[?)'                          // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
. "(vc_tab)"                     	// 2: Shortcode name
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


if(!preg_match_all( '/' . $regexshortcodes . '/s', $content, $matches, PREG_SET_ORDER ))
return "";

//shortcode_parse_atts
/**
 * vc_tabs
 *
 */
//if ( count($matches) ) { $tab_titles = $matches[0]; }
$tabs_nav = '';
$tabs_nav .= '<ul class="wpb_tabs_nav ui-tabs-nav vc_clearfix">';
if(count($matches)):
foreach ( $matches as $tab ) {
	$tab_stts=wp_parse_args(shortcode_parse_atts($tab[3]),array('title'=>'','tab_id'=>''));
    $tabs_nav .= '<li><a href="#tab-'. (''!=$tab_stts['tab_id'] ? $tab_stts['tab_id'] : sanitize_title( $tab_stts['title']) ) .'">' . $tab_stts['title'] . '</a></li>';
}
endif;

$tabs_nav .= '</ul>'."\n";

$css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, trim($element.' wpb_content_element '.$el_class), $this->settings['base']);

$output .= "\n\t".'<div class="'.$css_class.'" data-interval="'.$interval.'">';
$output .= "\n\t\t".'<div class="wpb_wrapper wpb_tour_tabs_wrapper ui-tabs vc_clearfix">';
$output .= wpb_widget_title(array('title' => $title, 'extraclass' => $element.'_heading'));
$output .= "\n\t\t\t".$tabs_nav;
$output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
if ( 'vc_tour' == $this->shortcode) {
    $output .= "\n\t\t\t" . '<div class="wpb_tour_next_prev_nav vc_clearfix"> <span class="wpb_prev_slide"><a href="#prev" title="'.__('Previous slide', 'js_composer').'">'.__('Previous slide', 'js_composer').'</a></span> <span class="wpb_next_slide"><a href="#next" title="'.__('Next slide', 'js_composer').'">'.__('Next slide', 'js_composer').'</a></span></div>';
}
$output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> '.$this->endBlockComment($element);

echo $output;