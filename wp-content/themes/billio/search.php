<?php
defined('ABSPATH') or die();
/**
 * The template for displaying Search Results pages
 *
 * @package WordPress
 * @subpackage Billio
 * @since Billio 1.0
 */

global $detheme_config;

get_header();?>
<?php 
locate_template('pagetemplates/scrollingsidebar.php',true);


$sidebar_position=get_billio_sidebar_position();
$sidebar=is_active_sidebar( 'detheme-sidebar' )?'detheme-sidebar':false;

if(!$sidebar){
	$sidebar_position = "nosidebar";
}

$class_sidebar = $sidebar_position;
?>

<div <?php post_class('content'); ?>>
<div class="<?php echo $class_sidebar;?>">
	<div class="container">
		<div class="row">
<?php if ($sidebar_position=='nosidebar') { ?>
			<div class="col-sm-12">
<?php	} else { ?>
			<div class="col-sm-8 <?php print ($sidebar_position=='sidebar-left')?" col-sm-push-4":"";?> col-md-9 <?php print ($sidebar_position=='sidebar-left')?" col-md-push-3":"";?>">
<?php	} ?>
				<header class="archive-header">

				<h2 class="archive-title"><?php printf( __( 'Search Results for: %s', 'billio' ), get_search_query() ); ?></h2>
				</header>
<?php
				if ( have_posts() ) :
					// Start the Loop.
					while ( have_posts() ) : the_post();

						//the_content();

						/*
						 * Include the post format-specific template for the content. If you want to
						 * use this in a child theme, then include a file called called content-___.php
						 * (where ___ is the post format) and that will be used instead.
						 */
						get_template_part( 'content', 'post'==get_post_type()?get_post_format():"page");
					?>
					<div class="clearfix">
						<div class="col-xs-12 postseparator"></div>
					</div>
					<?php
					endwhile;
					// Previous/next post navigation.
					//twentyfourteen_paging_nav();

				else :
					// If no content, include the "No posts found" template.
					get_template_part( 'content', 'none' );

				endif;
?>
				<!-- Pagination -->
				<div class="row">
					<?php locate_template('pagetemplates/pagination.php',true,false); ?>
				</div>
				<!-- /Pagination -->
				
			</div>


<?php 

set_query_var('sidebar',$sidebar);

if ('sidebar-right'==$sidebar_position) { ?>
			<div class="col-sm-4 col-md-3 sidebar">
				<?php get_sidebar(); ?>
			</div>
<?php }
	elseif ($sidebar_position=='sidebar-left') { ?>
			<div class="col-sm-4 col-md-3 sidebar col-sm-pull-8 col-md-pull-9">
				<?php get_sidebar(); ?>
			</div>
<?php }?>
		
		</div>			
	</div>
</div>
</div>
<?php
get_footer();
?>