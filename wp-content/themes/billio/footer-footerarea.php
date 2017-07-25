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

$footertext=function_exists('icl_t') ? icl_t('billio', 'footer-text', $detheme_config['footer-text']):$detheme_config['footer-text'];

?>
<footer id="footer" class="tertier_color_bg <?php print ($detheme_config['dt-header-type']=='leftbar')?" vertical_menu_container":"";?>">
<div class="container footer-section">
		<?php if((!empty($footertext) || strlen(strip_tags($footertext)) > 1) && $detheme_config['showfooterwidget'] && is_active_sidebar( 'detheme-bottom' )){?> 
		<div class="col-md-9<?php print isset($detheme_config['dt-footer-position']) && $detheme_config['dt-footer-position']=='right'?"":" col-md-push-3";?> col-sm-12 col-xs-12 footer-right">
			<div id="footer-right">
				<?php 
				dynamic_sidebar('detheme-bottom');
				do_action('dynamic_sidebar_detheme-bottom');
				 ?>
			</div>
		</div>			
		<div class="col-md-3<?php print isset($detheme_config['dt-footer-position']) && $detheme_config['dt-footer-position']=='right'?"":" col-md-pull-9";?> col-sm-12 col-xs-12 footer-left">
			<div id="footer-left">
				<?php echo do_shortcode($footertext); ?>
			</div>
		</div>
		<?php }
		elseif((!empty($footertext) || strlen(strip_tags($footertext)) > 1) && (!$detheme_config['showfooterwidget'] || !is_active_sidebar( 'detheme-bottom' )))
		{
		?> 
		<div class="col-md-12 footer-left equal-height">
			<div id="footer-left">
				<?php echo do_shortcode($footertext); ?>
			</div>
		</div>	
		<?php }
		elseif($detheme_config['showfooterwidget'] && is_active_sidebar( 'detheme-bottom' )){
			?>
		<div class="col-md-12 footer-right equal-height">
			<div id="footer-right">
				<?php dynamic_sidebar('detheme-bottom');
				do_action('dynamic_sidebar_detheme-bottom');
				 ?>
			</div>
		</div>	
		<?php
		 }
		?>
</div>
</footer>