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

$vertical_menu_container_class = ($detheme_config['dt-header-type']=='leftbar')?" vertical_menu_container":"";
?>
<section id="banner-section" class="<?php echo sanitize_html_class($vertical_menu_container_class); ?>">
<div class="container<?php print (empty($subtitle))?" no_subtitle":"";?>">
	<div class="row">
		<div class="col-xs-12">

<?php if (!is_single() || is_singular()) { ?>
<?php 	if(isset($detheme_config['page-title']) && isset($detheme_config['dt-show-banner-title']) && !empty($detheme_config['page-title'])) { ?>
			<div class="banner-title"><h1 class="page-title"><?php print $detheme_config['page-title']; ?></h1></div>
<?php	}  ?>
<?php }  ?>
<?php 	
billio_breadcrumb(array(
	'delimiter' => is_rtl()?'&nbsp;\&nbsp;':'&nbsp;/&nbsp;')
);
?>
			</div>
	</div>
</div>
</section>