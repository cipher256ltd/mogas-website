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

global $detheme_config,$wp_query,$paged,$posts_per_page;

get_header();?>
<?php 

locate_template('pagetemplates/scrollingsidebar.php',true);

$sidebar=is_active_sidebar( 'detheme-sidebar' )?'detheme-sidebar':false;
$sidebar_position=get_billio_sidebar_position();

if(!$sidebar){
	$sidebar_position = "nosidebar";
}

set_query_var('sidebar',$sidebar);
$class_sidebar = " ".$sidebar_position;
$vertical_menu_container_class = ($detheme_config['dt-header-type']=='leftbar')?" vertical_menu_container":"";
?>
<div <?php post_class('content '.$class_sidebar.$vertical_menu_container_class);?>>
	<div class="container">
		<div class="row">
<?php if ($sidebar_position=='nosidebar') { ?>
			<div class="col-xs-12">
<?php	} else { ?>
			<div class="col-xs-12 col-sm-8 <?php print ($sidebar_position=='sidebar-left')?" col-sm-push-4":"";?> col-md-9 <?php print ($sidebar_position=='sidebar-left')?" col-md-push-3":"";?>">
<?php	} ?>
<?php

				if ( have_posts() ) :
					
					$i = 0;

					while ( have_posts() ) : the_post();
						$i++;

						if ($i==1) :
						?>
						<div class="blank-reveal-area"></div>
						<?php endif;?>
						<?php get_template_part( 'content', get_post_format() ); ?>
						<div class="clearfix">
							<div class="col-xs-12 postseparator"></div>
						</div>
						<?php 
					endwhile;
				else :
					get_template_part( 'content', 'none' );
				endif;
?>
				<!-- Pagination -->
				<div class="row">
					<?php locate_template('pagetemplates/pagination.php',true,false); ?>
				</div>
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
		</div>			
	</div>
</div>
<?php
get_footer();
?>