<?php
defined('ABSPATH') or die();

/**
 * Template Name: Woocommerce Page
 *
 * Used for single page.
 *
 * @package WordPress
 * @subpackage Billio
 * @since Billio 1.0
 */

global $detheme_config;

get_header();?>
<?php 

$sidebar=is_active_sidebar( 'shop-sidebar' )?'shop-sidebar':false;

locate_template('pagetemplates/scrollingsidebar.php',true);

$sidebar_position = get_billio_sidebar_position();
if(!$sidebar){
	$sidebar_position = "nosidebar";
}

set_query_var('sidebar',$sidebar);
$class_sidebar = $sidebar_position;

?>

<div <?php post_class('content'); ?>>
<div class="<?php echo sanitize_html_class($class_sidebar);?>">
	<div class="container">
		<div class="row">
		<?php if ($sidebar_position=='nosidebar') { ?>
			<div class="col-sm-12">
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

	<?php endif; //if ($i==1) 
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
<?php endwhile;?>
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

