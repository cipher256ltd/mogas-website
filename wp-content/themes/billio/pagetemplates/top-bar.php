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

$vertical_menu_container_class = "";

if ($detheme_config['dt-header-type']=='leftbar') {
	$vertical_menu_container_class = "vertical_menu_container";
}
?>
<div id="top-bar" class="<?php echo sanitize_html_class($vertical_menu_container_class); ?>">
	<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<?php get_template_part( 'pagetemplates/topbar', "left".$detheme_config['dt-left-top-bar'] );?>
			<?php get_template_part( 'pagetemplates/topbar', "right".$detheme_config['dt-right-top-bar'] );?>
		</div>
	</div>
</div>
</div>
