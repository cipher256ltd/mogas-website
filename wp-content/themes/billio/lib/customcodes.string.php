<?php
defined('ABSPATH') or die();

function my_custom_tinymce_plugin_translation() {
    $strings = array(
        'insert_dt_shortcode' => __('Insert Billio Shortcode', 'billio'),
        'dt_shortcode' => __('Billio Shortcode', 'billio'),
        'icon_title' => __('Icon', 'billio'),
        'button_title'=>__('Buttons','billio'),
        'counto_title'=>__('Count To','billio'),
    );
    $locale = _WP_Editors::$mce_locale;

    $translated = 'tinyMCE.addI18n("' . $locale . '.dtshortcode", ' . json_encode( $strings ) . ");\n";

     return $translated;
}

$strings = my_custom_tinymce_plugin_translation();

?>