<?php
defined('ABSPATH') or die();
/**
 * The default template for displaying content link
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Billio
 * @since Billio 1.0
 */
?>

<?php 

	$bgstyle = '';
	$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),'full',false); 
	if (isset($featured_image[0])) {
		$bgstyle = ' style="background: url(\''.esc_url($featured_image[0]).'\') no-repeat; background-size: cover;"';
	} 
?>		

		<div class="row">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="col-xs-12">
					<div class="postcontent postcontent-link primary_color_bg" <?php echo esc_attr($bgstyle); ?>>
						<h2 class="blog-post-title"><a href="<?php echo esc_url(get_the_content()); ?>" target="_blank"><?php the_title();?></a></h2>
						<?php the_content(); ?>
					</div>


					<div class="postmetabottom">
						<div class="row">
							<div class="col-xs-12 text-right blog_info_share">
								<?php locate_template('pagetemplates/social-share.php',true,false); ?>
							</div>
						</div>

						<div class="postborder"></div>
					</div>

				</div>
			</article>
		</div><!--div class="row"-->