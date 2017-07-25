<?php
defined('ABSPATH') or die();

function billio_add_font_selector($buttons) {    
    
    $buttons[] = 'fontsizeselect';

    return $buttons;
}

add_filter('mce_buttons_2', 'billio_add_font_selector');

function billio_get_font_sizes($in){
    $in['fontsize_formats']=__("Bigger","billio")."=1.2em ".__('Big','billio')."=1.1em ".__("Small","billio")."=0.9em ".__("Smaller","billio")."=0.8em";
 return $in;
}

add_filter('tiny_mce_before_init', 'billio_get_font_sizes');
?>