<?php
/** @var WPBakeryShortCode_VC_Basic_Grid $this */
defined('ABSPATH') or die();

if(version_compare(WPB_VC_VERSION,'4.7.0','<=')){

$isotope_options = $posts = $filter_terms = array();

if(isset($atts['post_id']) && $atts['post_id']!=''){
	$this->post_id=$atts['post_id'];
	unset($atts['post_id']);
}

$this->buildAtts( $atts, $content );

$css_classes = ' ' . $this->shortcode;
wp_enqueue_script( 'prettyphoto' );
wp_enqueue_style( 'prettyphoto' );

// $isotope_options = $this->isotopeOptions( $layout, 'vertical' );
/*
if ( $this->atts['style'] == 'lazy' || $this->atts['style'] == 'load-more' ) {
	$this->buildItems();
}
*/
$this->buildGridSettings();
if ( $this->atts['style'] == 'pagination' ) {
	wp_enqueue_script( 'twbs-pagination' );
}
$this->enqueueScripts();
?>
<div class="vc_grid-container wpb_content_element<?php echo esc_attr( $css_classes ) ?>" data-vc-<?php echo $this->pagable_type ?>-settings="<?php echo esc_attr( json_encode( $this->grid_settings ) ) ?>" data-vc-request="<?php echo esc_attr( admin_url( 'admin-ajax.php', 'relative' ) ) ?>" data-vc-post-id="<?php echo esc_attr(get_the_ID()) ?>">
</div>
<?php

}
else{

	$css = $el_class = '';
$isotope_options = $posts = $filter_terms = array();

if(isset($atts['post_id']) && $atts['post_id']!=''){
	$this->post_id=$atts['post_id'];
	unset($atts['post_id']);
}

$this->buildAtts( $atts, $content );

$css = isset( $atts['css'] ) ? $atts['css'] : '';
$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

$class_to_filter = 'vc_grid-container vc_clearfix wpb_content_element ' . $this->shortcode;
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

wp_enqueue_script( 'prettyphoto' );
wp_enqueue_style( 'prettyphoto' );

$this->buildGridSettings();
if ( isset( $this->atts['style'] ) && 'pagination' === $this->atts['style'] ) {
	wp_enqueue_script( 'twbs-pagination' );
}
$this->enqueueScripts();
?>
<div class="vc_grid-container-wrapper vc_clearfix">
	<div class="<?php echo esc_attr( $css_class ) ?>"
		 data-vc-<?php echo esc_attr( $this->pagable_type ); ?>-settings="<?php echo esc_attr( json_encode( $this->grid_settings ) ); ?>"
		 data-vc-request="<?php echo esc_attr( admin_url( 'admin-ajax.php', 'relative' ) ); ?>"
		 data-vc-post-id="<?php echo esc_attr( get_the_ID() ); ?>"
		 data-vc-public-nonce="<?php echo vc_generate_nonce( 'vc-public-nonce' ); ?>">
	</div>
</div>
<?php }?>