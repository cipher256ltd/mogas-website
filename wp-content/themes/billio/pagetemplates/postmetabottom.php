<?php
defined('ABSPATH') or die();

global $detheme_config; 
?>
					<div class="postmetabottom">
						<div class="row">
						<?php if(is_rtl()):?>
							<div class="col-xs-8 text-left blog_info_share">
								<?php locate_template('pagetemplates/social-share.php',true,false); ?>
							</div>
							<div class="col-xs-4">
								<a target="_self" class="btn btn-default btn-ghost skin-dark btn-readmore" href="<?php the_permalink(); ?>"><?php echo __('Read More','billio') ?></a>
							</div>
						<?php else:?>
							<div class="col-xs-4">
								<a target="_self" class="btn btn-default btn-ghost skin-dark btn-readmore" href="<?php the_permalink(); ?>"><?php echo __('Read More','billio') ?></a>
							</div>
							<div class="col-xs-8 text-right blog_info_share">
								<?php locate_template('pagetemplates/social-share.php',true,false); ?>
							</div>
						<?php endif;?>
						</div>

						<div class="row">
							<div class="col-xs-12 text-<?php print is_rtl()?"left":"right";?> blog_info_comments">
<?php if(comments_open()):?>
									<?php comments_number(__('No Comments','billio'),__('1 Comment','billio'),__('% Comments','billio')); ?>
<?php endif;?>
							</div>
						</div>

						<div class="postborder"></div>
					</div>

