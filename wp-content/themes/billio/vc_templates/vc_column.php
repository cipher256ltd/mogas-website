<?php
defined('ABSPATH') or die();

$output = $el_class = $width = '';
extract(shortcode_atts(array(
    'el_class' => '',
    'width' => '1/1',
    'css' => '',
	'offset' => ''
), $atts));

global $detheme_Style;

switch ($width) {
	case '1/5':
			$width="span_1_5";
		break;
	case '2/5':
			$width="span_2_5";
		break;
	case '3/5':
			$width="span_3_5";
		break;
	case '4/5':
			$width="span_4_5";
		break;
	default:
		break;
}
$el_class = $this->getExtraClass($el_class);
$width = wpb_translateColumnWidthToSpan($width);

if(function_exists('vc_column_offset_class_merge')){
	$width = vc_column_offset_class_merge($offset, $width);
}

$css_classes = array(
	$this->getExtraClass( $el_class ),
	'wpb_column',
	'vc_column_container',
	$width,
);

if (vc_shortcode_custom_css_has_property( $css, array('border', 'background') )) {
	$css_classes[]='vc_col-has-fill';
}

$wrapper_attributes = array();

$css_class = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

echo "\n\t".'<div ' . implode( ' ', $wrapper_attributes ) . '>';
echo "\n\t\t".'<div class="vc_column-inner ' . esc_attr( trim( vc_shortcode_custom_css_class( $css ) ) ) . '">';
echo "\n\t\t\t".'<div class="wpb_wrapper">';
echo "\n\t\t\t\t".wpb_js_remove_wpautop( $content );
echo "\n\t\t\t".'</div>';
echo "\n\t\t".'</div>';
echo "\n\t".'</div>';

if(!empty($css)){
	$detheme_Style[]=$css;
}