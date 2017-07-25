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

global $detheme_config,$post;


$sidebar=is_active_sidebar( 'shop-sidebar' )?'shop-sidebar':false;

get_header();?>

<?php locate_template('pagetemplates/scrollingsidebar.php',true); ?>

<?php 

$sidebar_position = get_billio_sidebar_position();	

if(!$sidebar){
	$sidebar_position = "nosidebar";
}

set_query_var('sidebar',$sidebar);

$class_sidebar = $sidebar_position;
?>

<div <?php post_class('content'); ?>>
<div class="<?php echo $class_sidebar;?>">
	<div class="container">
		<div class="row">
		<?php if ($sidebar_position=='nosidebar') { ?>
			<div class="col-sm-12">
		<?php	} else { ?>
			<div class="col-xs-12 col-sm-8 <?php print ($sidebar_position=='sidebar-left')?" col-sm-push-4":"";?> col-md-9 <?php print ($sidebar_position=='sidebar-left')?" col-md-push-3":"";?>">
		<?php	} ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="postcontent">
							<?php woocommerce_content(); ?>
						</div>
					</div>
				</div>

			</article>
			</div>

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
	</div><!-- .container -->
	</div>
</div><!-- .woocommerce -->
</div>
<?php
get_footer();
?>