<?php
defined('ABSPATH') or die();

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Billio
 * @since Billio 1.0
 */

global $detheme_config;

if ($detheme_config['dt_scrollingsidebar_on']) :
	if (!empty($detheme_config['dt_scrollingsidebar_margin'])) {
		$dt_scrollingsidebar_margin = is_int(intval($detheme_config['dt_scrollingsidebar_margin'])) ? $detheme_config['dt_scrollingsidebar_margin'] : 0;
	} else {
		$dt_scrollingsidebar_margin = '0';
	}

	if (!empty($detheme_config['dt_scrollingsidebar_top_margin'])) {
		$dt_scrollingsidebar_top_margin = is_int(intval($detheme_config['dt_scrollingsidebar_top_margin'])) ? $detheme_config['dt_scrollingsidebar_margin'] : 200;
	} else {
		$dt_scrollingsidebar_top_margin = '200';
	}

	$dt_scrollingsidebar_position = empty($detheme_config['dt_scrollingsidebar_position']) ? 'right' : $detheme_config['dt_scrollingsidebar_position'];

	if ($detheme_config['dt_scrollingsidebar_bg_type']) {
		$dt_scrollingsidebar_bg_color = empty($detheme_config['dt_scrollingsidebar_bg_color']) ? '#ecf0f1' : $detheme_config['dt_scrollingsidebar_bg_color'];
	} else {
		$dt_scrollingsidebar_bg_color = 'transparent';
	}
	
?>
<div id="floatMenu" data-top-margin="<?php echo esc_attr($dt_scrollingsidebar_top_margin);?>" data-bg-color="<?php echo esc_attr($dt_scrollingsidebar_bg_color);?>" data-position="<?php echo esc_attr($dt_scrollingsidebar_position); ?>" data-margin="<?php echo esc_attr($dt_scrollingsidebar_margin); ?>">
  <?php 
    dynamic_sidebar('detheme-scrolling-sidebar');
    do_action('detheme-scrolling-sidebar');
  ?>
</div>
<?php endif; ?>