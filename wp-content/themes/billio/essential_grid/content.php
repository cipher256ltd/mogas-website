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
	global $more, $dt_revealData, $detheme_config;
	$more = 1;
?>
<div class="row">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="col-xs-12">
			<div class="postcontent row">
				<?php if($detheme_config['essential-title']):?>
				<div class="col-xs-12">
					<h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>
				</div>
        		<?php endif;?>
        		<?php
					the_content();
				?>
			</div>
			<?php locate_template('essential_grid/postaboutcomment.php',true,false); ?>
		</div>

	</article>
</div>
