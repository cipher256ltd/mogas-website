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

global $detheme_config,$wp_query,$paged;

get_header(); 

locate_template('pagetemplates/scrollingsidebar.php',true);

$sidebar_position=get_billio_sidebar_position();
$sidebar=is_active_sidebar( 'detheme-sidebar' )?'detheme-sidebar':false;

if(!$sidebar){
	$sidebar_position = "nosidebar";
}


set_query_var('sidebar',$sidebar);

$class_sidebar = $sidebar_position;

$class_vertical_menu = '';
if ($detheme_config['dt-header-type']=='leftbar') {
	$class_vertical_menu = ' vertical_menu_container';
}

?>


<div  <?php post_class('blog single-post content '.$class_sidebar.$class_vertical_menu); ?>>
<div class="container">
		<div class="row">
	<?php if ($sidebar_position=='nosidebar') { ?>
			<div class="col-xs-12">
<?php	} else { ?>
			<div class="col-xs-12 col-sm-8 <?php print ($sidebar_position=='sidebar-left')?" col-sm-push-4":"";?> col-md-9 <?php print ($sidebar_position=='sidebar-left')?" col-md-push-3":"";?>">
<?php	} ?>
<?php

$i = 0;

while ( have_posts() ) : 
	$i++;

	if ($i==1) : ?><div class="blank-reveal-area"></div><?php endif; //if ($i==1)
	the_post();
	locate_template( 'essential_grid/content.php',true);
?>

<?php endwhile;?>
</div><!-- content area col-9 -->

<?php if ('sidebar-right'==$sidebar_position) { ?>
			<div class="col-xs-12 col-sm-4 col-md-3 sidebar">
				<?php get_sidebar(); ?>
			</div>
<?php }
	elseif ($sidebar_position=='sidebar-left') { ?>
			<div class="col-xs-12 col-sm-4 col-md-3 sidebar col-sm-pull-8 col-md-pull-9">
				<?php get_sidebar(); ?>
			</div>
<?php }?>
	

		</div><!-- .row -->

	</div><!-- .container -->

</div>
<?php
get_footer();
?>