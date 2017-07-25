<?php
defined('ABSPATH') or die();
/**
 * The default template for displaying content post gallery
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Billio
 * @since Billio 1.0
 */
?>

<?php 

	global $more, $detheme_config,$hasgallery, $billio_link_pages_args;
	$more = 1;
	$sharepos = 'sharepos';
?>

<?php if (is_single()) :
	$content=get_the_content();
	//Find video shotcode in content
	$pattern = get_shortcode_regex();

	$hasgallery = false;

	$content=preg_replace_callback('/'. $pattern .'/s',
		function($matches){

			global $hasgallery;
			static $id = 0;
			$id++;

			if($matches[2]=='gallery') {

				if($id==1){
					$hasgallery=$matches[3];
				}

			}
			else{
				return $matches[0];
			}
			return " ";

		}
	,$content,-1,$matches_count);


	$content = apply_filters( 'the_content', do_shortcode($content));
	$content = str_replace( ']]>', ']]&gt;', $content );
 ?>

				<div class="row">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="col-xs-12">

						<?php	if ( $hasgallery) { ?>
							<div class="postimage">
						<?php 						
							$gallery_shortcode_attr = shortcode_parse_atts($hasgallery);
							$attachment_image_ids = explode(',',$gallery_shortcode_attr['ids']);
						?>

								<div id="gallery-carousel-<?php echo get_the_ID(); ?>" class="carousel slide post-gallery-carousel" data-ride="carousel" data-interval="3000">
							        <div class="carousel-inner">
						<?php
							$i = 0;
							foreach ($attachment_image_ids as $attachment_id) {
    							$attached_img = wp_get_attachment_image_src($attachment_id,'large');
    							$alt_image = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

						?>
								<div class="item <?php echo ($i==0) ? 'active' : ''; ?>"><img src="<?php echo esc_url($attached_img[0]); ?>" alt="<?php echo esc_attr($alt_image); ?>" /></div>
						<?php
								$i++;
							}
						?>
					        		</div>

									<div class="post-gallery-carousel-nav">
										<div class="post-gallery-carousel-buttons">
									        <a href="#gallery-carousel-<?php echo get_the_ID(); ?>" data-slide="prev" class="icon-left-open-big">
									        </a>
									        <a href="#gallery-carousel-<?php echo get_the_ID(); ?>" data-slide="next" class="icon-right-open-big">
									        </a>
								    	</div>
							    	</div>
							    </div>			
							</div>
						<?php		$sharepos = '';
								} ?> 

							<?php locate_template('pagetemplates/postinfo.php',true,false); ?>

							<div class="postcontent">
								<h2 class="blog-post-title"><?php the_title();?></h2>

								<?php locate_template('pagetemplates/postinfotag.php',true,false); ?>

		                		<?php  print $content;

		                			wp_link_pages( $billio_link_pages_args );
		                		?>

								<?php locate_template('pagetemplates/postmetabottom_detail.php',true,false); ?>
							</div>

							<?php locate_template('pagetemplates/postaboutcomment.php',true,false); ?>

						</div>

					</article>
				</div><!--div class="row"-->


<?php else :  //if (is_single())

	$more = 0;


	$content = get_the_content(__(' ', 'billio'));
	$pattern = get_shortcode_regex();

	$hasgallery = false;

	$content=preg_replace_callback('/'. $pattern .'/s',
		function($matches){

			global $hasgallery;
			static $id = 0;
			$id++;

			if($matches[2]=='gallery') {

				if($id==1){
					$hasgallery=$matches[3];
				}

			}
			else{
				return $matches[0];
			}
			return " ";

		}
	,$content,-1,$matches_count);

	$content = apply_filters( 'the_content', billio_remove_shortcode_from_content($content));
	$content = str_replace( ']]>', ']]&gt;', $content );

	?>

		<div class="row">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	if ( $hasgallery  ) { 
?>
				<div class="col-xs-12">
					<div class="postimage">
						<?php 						
							$gallery_shortcode_attr = shortcode_parse_atts($hasgallery);
							$attachment_image_ids = explode(',',$gallery_shortcode_attr['ids']);
?>

						<div id="gallery-carousel-<?php echo get_the_ID(); ?>" class="carousel slide post-gallery-carousel" data-ride="carousel" data-interval="3000">

					        <div class="carousel-inner">
<?php
							$i = 0;
							foreach ($attachment_image_ids as $attachment_id) {
    							$attached_img = wp_get_attachment_image_src($attachment_id,'large');
    							$alt_image = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
?>
								<div class="item <?php echo($i==0) ? 'active' : ''; ?>"><img src="<?php echo esc_url($attached_img[0]); ?>" alt="<?php echo esc_attr($alt_image); ?>" /></div>
<?php
								$i++;
							}
?>
					        </div>

							<div class="post-gallery-carousel-nav">
								<div class="post-gallery-carousel-buttons">
							        <a href="#gallery-carousel-<?php echo get_the_ID(); ?>" data-slide="prev" class="icon-left-open-big">
							        </a>
							        <a href="#gallery-carousel-<?php echo get_the_ID(); ?>" data-slide="next" class="icon-right-open-big">
							        </a>
						    	</div>
					    	</div>
					    </div>			
					</div>
				</div>
<?php
	} 
?> 
				<div class="col-xs-12<?php echo ($hasgallery)?' col-md-push-0 margin_top_40_max_sm':'';?>">
					<div class="postcontent">
						<?php locate_template('pagetemplates/postinfo.php',true,false); ?>

						<h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>

						<?php locate_template('pagetemplates/postinfotag.php',true,false); ?>

						<?php 
							if (has_excerpt()) {
								$excerpt = apply_filters('the_excerpt', get_the_excerpt());
								print $excerpt . '<a class="more-link"></a>';	
							} else {
								print $content;
							}
						?>
					</div>

					<?php locate_template('pagetemplates/postmetabottom.php',true,false); ?>
				</div> 
			</article>
		</div><!--div class="row"-->

<?php endif; //if (is_single()) :?>