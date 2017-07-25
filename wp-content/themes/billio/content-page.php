<?php
defined('ABSPATH') or die();
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Billio
 * @since Billio 1.0
 */
?>

<?php 
	global $more, $dt_revealData, $detheme_config, $billio_link_pages_args;
	$more = 1;

	$imageurl = "";

	/* Get Image from featured image */
	if (isset($post->ID)) {
		$thumb_id = get_post_thumbnail_id($post->ID);
		$featured_image = wp_get_attachment_image_src($thumb_id,'full',false); 
		if (isset($featured_image[0])) {
			$imageurl = $featured_image[0];
		}

		$alt_image = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
	}
	

	$nohead = '';
	$sharepos = 'sharepos';
?>

<?php if (is_single()) : ?>

				<div class="row">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="col-xs-12">

<?php	if ($imageurl!="") { ?>											
							<div class="postimagecontent">
								<a href="<?php the_permalink(); ?>" title="<?php print esc_attr(get_the_title());?>"><img class="img-responsive" alt="<?php echo esc_attr($alt_image); ?>" src="<?php echo esc_url($imageurl); ?>"></a>
							</div>
<?php
			$nohead = '';
			$sharepos = '';
		} 
?>											
							<?php locate_template('pagetemplates/postinfo.php',true,false); ?>

							<div class="postcontent">
								<h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>

							<?php locate_template('pagetemplates/postinfotag.php',true,false); ?>

		                		<?php
									the_content();

							        wp_link_pages( $billio_link_pages_args );
								?>

								<?php locate_template('pagetemplates/postmetabottom_detail.php',true,false); ?>
							</div>

							<?php locate_template('pagetemplates/postaboutcomment.php',true,false); ?>
						</div>

					</article>
				</div><!--div class="row"-->

<?php
else : 
?>

		<div class="row">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	if ($imageurl!="") {
?>											
				<div class="col-xs-12">
					<div class="postimagecontent">
						<a href="<?php the_permalink(); ?>" title="<?php print esc_attr(get_the_title());?>"><img class="img-responsive" alt="<?php echo esc_attr($alt_image); ?>" src="<?php echo esc_url($imageurl); ?>"></a>
					</div>
				</div>
<?php
	} 
?>											
				<div class="col-xs-12<?php print ($imageurl!='')?' col-md-push-0 margin_top_40_max_sm':'';?>">
					<div class="postcontent">

						<?php locate_template('pagetemplates/postinfo.php',true,false); ?>

						<h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>
						
						<?php locate_template('pagetemplates/postinfotag.php',true,false); ?>

						<div class="blog_info_author"><?php the_author_link(); ?></div>
					</div>

					<?php locate_template('pagetemplates/postmetabottom.php',true,false); ?>

				</div> 
			</article>
		</div>

<?php endif; ?>
