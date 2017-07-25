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
global $detheme_config,$post, $billio_link_pages_args;

get_header();?>
<?php locate_template('pagetemplates/scrollingsidebar.php',true); ?>
<?php 

$sidebar_position=get_billio_sidebar_position();
$sidebar=is_active_sidebar( 'detheme-sidebar' )?'detheme-sidebar':false;

if(!$sidebar){
	$sidebar_position = "nosidebar";
}

set_query_var('sidebar',$sidebar);
$class_sidebar = $sidebar_position;
$vertical_menu_container_class = ($detheme_config['dt-header-type']=='leftbar')?" vertical_menu_container":"";
?>
<div <?php post_class('content'.$vertical_menu_container_class);?>>
<div class="<?php echo $class_sidebar;?>">
	<div class="container">
		<?php if($detheme_config['dt-show-title-page']):?>
		<h1 class="page-title"><?php print $detheme_config['page-title'];?></h1>
		<?php endif;?>
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
	if ($i==1) :
	?>

	<div class="blank-reveal-area"></div>

	<?php endif; 
the_post();
?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<div class="postcontent">
<?php the_content(); 
	wp_link_pages( $billio_link_pages_args );
?>
							</div>
						</div>
					</div>

<?php
	if(comments_open()):?>
							<div class="comment-count">
								<h3><?php comments_number(__('No Comments','billio'),__('1 Comment','billio'),__('% Comments','billio')); ?></h3>
							</div>

							<div class="section-comment">
								<?php comments_template('/comments.php', true); ?>
							</div><!-- Section Comment -->
<?php endif;?>


					</article>
<?php endwhile; ?>
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
</div><!-- .page -->
</div>

<?php
get_footer();
?>