<?php
defined('ABSPATH') or die();
?>
						<div class="postinfo">
							<?php $tags = get_the_tag_list(' ',', ',''); ?>
							<?php if (!empty($tags)) : ?>
							<div class="blog_info_tags"><?php echo $tags; ?></div>
							<?php endif;  ?>
						</div>