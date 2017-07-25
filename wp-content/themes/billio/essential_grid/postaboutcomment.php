<?php
defined('ABSPATH') or die();
global $detheme_config; 
?>
<?php 
if(comments_open()):?>
	<div class="comment-count">
		<h3><?php comments_number(__('No Comments','billio'),__('1 Comment','billio'),__('% Comments','billio')); ?></h3>
	</div>

	<div class="section-comment">
		<?php comments_template('/comments.php', true); ?>
	</div><!-- Section Comment -->
<?php endif;?>
