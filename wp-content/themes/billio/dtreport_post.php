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

get_header();

locate_template('pagetemplates/scrollingsidebar.php',true);


$sidebar_position=get_billio_sidebar_position();
$sidebar=is_active_sidebar( 'detheme-sidebar' )?'detheme-sidebar':false;

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
					// Start the Loop.
					$i = 0;
?>
				
<?php
					while ( have_posts() ) : the_post();

						$i = is_int($wp_query->current_post) ? $wp_query->current_post : 0;
						$j = $i % 2;
						if ($j==0) {
?>
						<div class="row equal_height"> 
<?php
						} //if ($j==0)

						$post_id = get_the_ID();

						$imageurl = "";
						$alt_image = "";
						if (isset($post_id)) {
							$thumb_id = get_post_thumbnail_id($post_id);
							$featured_image = wp_get_attachment_image_src($thumb_id,'full',false); 
							if (isset($featured_image[0])) {
								$imageurl = $featured_image[0];
							}

							$alt_image = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
						}

						$dt_report_pre_title 			= get_post_meta($post_id,'dt_report_pre_title',true);
        				$dt_report_button_label 		= get_post_meta($post_id,'dt_report_button_label',true);
        				$dt_report_document_url 		= get_post_meta($post_id,'dt_report_document_url',true);
    					$dt_report_document_extension 	= get_post_meta($post_id,'dt_report_document_extension',true);
    					$dt_report_document_icon      	= get_post_meta($post_id,'dt_report_document_icon',true);

						$i++;

						if ($i==1) :
?>
						<div class="blank-reveal-area"></div>
						<?php endif; //if ($i==1)?>


						<div id="report-<?php the_ID(); ?>" <?php post_class('col-xs-12 col-sm-6 equal_height_item'); ?>>
							<div class="row dt_report_item"> 
						<?php	

								if ($imageurl!="") { 
						?>
								<div class="col-xs-12 col-sm-6">
									<img class="img-responsive" alt="<?php echo esc_attr($alt_image); ?>" src="<?php echo esc_url($imageurl); ?>">
								</div>

						<?php 	} //if ($imageurl!="")?>
								
								<div class="col-xs-12<?php echo ($imageurl!="")?"col-sm-6":""; ?>">
									<h3 class="dt_report_pre_title"><?php echo $dt_report_pre_title; ?></h3>
									<h2 class="dt_report_title"><?php the_title(); ?></h2>
									<div class="dt_report_content"><?php the_content(); ?></div>
						<?php if (!empty($dt_report_document_url)) { ?>
									<div class="dt_report_button">
										<a href="<?php echo esc_url($dt_report_document_url); ?>" target="_blank"><i class="<?php echo sanitize_html_class($dt_report_document_icon); ?>"></i><?php echo $dt_report_button_label . ' ' . $dt_report_document_extension; ?></a>
									</div>
						<?php } ?>
								</div>
							</div>
						</div>
<?php
						$i = is_int($wp_query->current_post) ? $wp_query->current_post : 0;
						$count = is_int($wp_query->post_count) ? $wp_query->post_count : 0;
						$j = $i % 2;
						if (($j!=0)||($count==($i+1))) {
?>
    					</div>
<?php
						} //if (($j!=0)||($count==($i+1)))
?>

<?php
	if(comments_open() and is_single()):?>
							<div class="comment-count">
								<h3><?php comments_number(__('No Comments','billio'),__('1 Comment','billio'),__('% Comments','billio')); ?></h3>
							</div>

							<div class="section-comment">
								<?php comments_template('/comments.php', true); ?>
							</div><!-- Section Comment -->
<?php endif;?>
<?php					endwhile;
				else :
					// If no content, include the "No posts found" template.
					get_template_part( 'dt_report_post', 'none' );
				endif;
?>
				<!-- Pagination -->
				<div class="row">
					<div class="dt_report_pagination col-xs-12">
						<?php
							echo paginate_links( apply_filters( 'dt_report_pagination_args', array(
								'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
								'format'       => '',
								'add_args'     => '',
								'current'      => max( 1, get_query_var( 'paged' ) ),
								'total'        => $wp_query->max_num_pages,
								'prev_text'    => '&#9664;',
								'next_text'    => '&#9658;',
								'type'         => 'plain',
								'end_size'     => 3,
								'mid_size'     => 3
							) ) );
						?>
					</div>
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