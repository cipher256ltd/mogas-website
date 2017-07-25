<?php
defined('ABSPATH') or die();
/**
 * Template Name: Blank Page
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

global $detheme_config, $billio_link_pages_args;

set_query_var('sidebar','nosidebar');

?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<?php locate_template('lib/page-options.php',true);?>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head();?>
</head>
<body <?php body_class(is_detheme_home(get_post())?"home dt_custom_body":"dt_custom_body"); print $detheme_config['body_tag'];?>>

<?php locate_template('pagetemplates/preloader.php',true);?>

<!-- start content -->
<div <?php post_class('content'); ?>>
<div class="nosidebar">
<?php 
while ( have_posts() ) : 
the_post();
?>
	<div class="post-article">
	<?php 
		the_content();
		wp_link_pages( $billio_link_pages_args );
	 ?>
	</div>

<?php
	if(comments_open()):?>
						<div class="container">
							<div class="comment-count">
								<h3><?php comments_number(__('No Comments','billio'),__('1 Comment','billio'),__('% Comments','billio')); ?></h3>
							</div>

							<div class="section-comment">
								<?php comments_template('/comments.php', true); ?>
							</div><!-- Section Comment -->
						</div>
<?php endif;?>

<?php endwhile; ?>
			</div>
	</div>
<!-- end content -->
<?php wp_footer(); ?>
</body>
</html>