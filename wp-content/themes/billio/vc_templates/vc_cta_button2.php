<?php
// extract(shortcode_atts(array(
//     'color' => 'wpb_button',
//     'icon' => 'none',
//     'size' => '',
//     'target' => '',
//     'href' => '',
//     'title' => __('Text on the button', "js_composer"),
//     'call_text' => '',
//     'position' => 'cta_align_right',
//     'el_class' => ''
// ), $atts));
extract(shortcode_atts(array(
    'h2' => '',
    'h4' => '',
    'position' => '',
    'el_width' => '',
    'style' => '',
    'txt_align' => '',
    'accent_color' => '',
    'link' => '',
    'title' => __('Text on the button', "js_composer"),
    'icon' => '',
    'size' => 'btn-default',
    'btn_style' => 'default',
    'el_class' => '',
    'css_animation' => ''
), $atts));

$class = "vc_call_to_action wpb_content_element";
// $position = 'left';
// $width = '90';
// $style = '';
// $txt_align = 'right';
$link = ($link=='||') ? '' : $link;
$link = vc_build_link($link);

$a_href = $link['url'];
$a_title = $link['title'];
$a_target = $link['target'];

$class .= ($position!='') ? ' vc_cta_btn_pos_'.$position : '';
$class .= ($el_width!='') ? ' vc_el_width_'.$el_width : '';
$class .= ($style!='') ? ' vc_cta_'.$style : '';
$class .= ($txt_align!='') ? ' vc_txt_align_'.$txt_align : '';

$inline_css = ($accent_color!='') ? ' style="'.vc_get_css_color('background-color', $accent_color).vc_get_css_color('border-color', $accent_color).'"' : '';

$class .= $this->getExtraClass($el_class);
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $this->settings['base']);
$css_class .= $this->getCSSAnimation($css_animation);
?>
<div<?php echo $inline_css; ?> class="<?php echo esc_attr(trim($css_class)); ?>">
    <?php if ($position!='bottom') 

        print  '<a '.(!empty($a_href)?"href=\"".$a_href."\"":"").'class="vc_cta_btn btn btn-'.$btn_style.' '.$size.'"'.($a_title!=''?" title=\"".$a_title."\"":"").($a_target!=''?" target=\"".trim($a_target)."\"":"").'>'.$title.'</a>';

         ?>
<?php if ($h2!='' || $h4!=''): ?>
    <!--hgroup-->
        <?php if ($h2!=''): ?><h2 class="wpb_heading"><?php echo $h2; ?></h2><?php endif; ?>
        <?php if ($h4!=''): ?><h4 class="wpb_heading"><?php echo $h4; ?></h4><?php endif; ?>
    <!--/hgroup-->
<?php endif; ?>
    <?php echo wpb_js_remove_wpautop($content, true); ?>
    <?php if ($position=='bottom') 
            print  '<a '.(!empty($a_href)?"href=\"".$a_href."\"":"").'class="vc_cta_btn btn btn-'.$btn_style.' '.$size.'"'.($a_title!=''?" title=\"".$a_title."\"":"").($a_target!=''?" target=\"".trim($a_target)."\"":"").'>'.$title.'</a>';
 ?>
</div>
<?php $this->endBlockComment('.vc_call_to_action') . "\n";