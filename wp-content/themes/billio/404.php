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
get_header();
?>
<div <?php post_class('content');?>>
<div class="nosidebar">
	<div class="container">
		<?php if (isset($detheme_config['dt-404-page'])&&!empty($detheme_config['dt-404-page']) && $post = get_post($detheme_config['dt-404-page'])) :
		if($detheme_config['dt-show-title-page']):?>
		<h1 class="page-title"><?php print $detheme_config['page-title'];?></h1>
		<?php endif;?>
		<div class="row">
			<div class="col-xs-12">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<div class="postcontent">
<?php
	print do_shortcode($post->post_content);	
?>
							</div>
						</div>
					</div>
					</article>
		</div>
		<?php else:?>
<div class="page-404">
<div class="page-404-heading1">404</div>
   <div class="page-404-subheading"><?php _e('OOPS! SOMETHING GOES WRONG','billio');?></div>
    <p><?php _e('This is not the page you are looking for.','billio');?></p>
    <div class="page-404-button"><a href="<?php print home_url();?>" class="btn btn-ghost skin-dark"><?php _e('back to homepage','billio');?></a></div>
</div>
		<?php endif;?>

	</div><!-- .container -->
</div>
</div><!-- .page -->
</div>

<?php wp_footer(); ?>
</body>
</html>
