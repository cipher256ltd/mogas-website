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
		<div class="row">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php 
	global $more, $detheme_config, $billio_link_pages_args;
?>											

				<div class="col-xs-12">

					<?php locate_template('pagetemplates/postinfo.php',true,false); ?>

<?php if (is_single()) : ?>
					<div class="postcontent">
						<h2 class="blog-post-title"><?php the_title();?></h2>

						<?php locate_template('pagetemplates/postinfotag.php',true,false); ?>

						<?php the_content();

							wp_link_pages( $billio_link_pages_args );
						?>

						<?php locate_template('pagetemplates/postmetabottom_detail.php',true,false); ?>
					</div>

					<?php locate_template('pagetemplates/postaboutcomment.php',true,false); ?>

<?php else : //if (is_single()) ?>
					<div class="postcontent">
						<h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>

						<?php 

							$more = 0;
							//$content = get_the_content(' ');
							$content = apply_filters('the_content', get_the_content(' '));
							$content = billio_remove_shortcode_from_content($content);
							$content = str_replace( ']]>', ']]&gt;', $content );

							if (has_excerpt()) {
								$excerpt = apply_filters('the_excerpt', get_the_excerpt());
								print $excerpt . '<a class="more-link"></a>';	
							} else {
								print $content;
							}

						?>
					</div>

					<?php locate_template('pagetemplates/postmetabottom.php',true,false); ?>
<?php endif; ?>
				</div> 
			</article>
		</div><!--div class="row"-->
