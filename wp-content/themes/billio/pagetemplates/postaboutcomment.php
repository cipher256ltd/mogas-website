<?php
defined('ABSPATH') or die();
global $detheme_config; 
?>
							<div class="about-author bg_gray_3">
								<div class="media">
									<div class="pull-<?php print is_rtl()?"right":"left";?> text-center">
										<?php 
											$avatar_url = billio_get_avatar_url(get_avatar( get_the_author_meta( 'ID' ), 130 )); 
											if (isset($avatar_url)) {
										?>					
										<img src="<?php echo esc_url($avatar_url); ?>" class="author-avatar img-responsive img-circle" alt="<?php echo esc_attr(get_the_author_meta( 'nickname' )); ?>">
										<?php 
											} 
										?>											
									</div>
									<div class="media-body">
										<h4><?php echo sprintf(__('About %s','billio'),get_the_author_meta( 'nickname' )); ?></h4>
										<?php echo get_the_author_meta( 'description' ); ?>
									</div>
								</div>
							</div>

<?php 
if(comments_open()):?>
	<div class="comment-count">
		<h3><?php comments_number(__('No Comments','billio'),__('1 Comment','billio'),__('% Comments','billio')); ?></h3>
	</div>

	<div class="section-comment">
		<?php comments_template('/comments.php', true); ?>
	</div><!-- Section Comment -->
<?php endif;?>
