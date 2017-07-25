<?php
defined('ABSPATH') or die();
/** DT_Tabs **/
class DT_Tabs extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => 'dt_widget_tabs', 'description' => __( "Display popular posts, recent posts, and recent comments in Tabulation.",'billio') );
		parent::__construct('dt-tabs', __('DT Tabs','billio'), $widget_ops);
		$this->alt_option_name = 'dt_widget_tabs';
		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}
	function widget($args, $instance) {
		global $detheme_Scripts;
		global $detheme_config;
		$cache = wp_cache_get('dt_widget_tabs', 'widget');
		if ( !is_array($cache) )
			$cache = array();
		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;
		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}
		extract($args);
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts','billio');
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 3;
		if ( ! $number ) $number = 3;
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<!-- Nav tabs -->
		<ul class="nav nav-tabs nav-justified">
		  <li class="active"><a href="#home_<?php echo sanitize_key($this->get_field_id('dt')); ?>" data-toggle="tab"><?php _e('Popular','billio');?></a></li>
		  <li><a href="#recent_<?php echo sanitize_key($this->get_field_id('dt')); ?>" data-toggle="tab"><?php _e('Recent','billio');?></a></li>
		  <li><a href="#comments_<?php echo sanitize_key($this->get_field_id('dt')); ?>" data-toggle="tab"><?php _e('Comments','billio');?></a></li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
		  	<div class="tab-pane fade in active" id="home_<?php echo sanitize_key($this->get_field_id('dt')); ?>">
<?php
				$r = new WP_Query(array( 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'meta_key' => 'post_views_count', 'orderby' => 'meta_value', 'order' => 'DESC' ) );
				if ($r->have_posts()) :
					$i = 0;
					while ( $r->have_posts() ) : $r->the_post();
						//if ($i>0) {echo '<hr>';}
?>
				<div class="row">
					<div class="rowlist">
					<?php
						$imgurl = "";
						$col_post_info = 'col-xs-12';
						$col_image_info = ''; 
						$attachment_id=get_post_thumbnail_id(get_the_ID());
						$featured_image = wp_get_attachment_image_src($attachment_id,'thumbnail',false); 
						if (isset($featured_image[0])) {
							$imgurl = $featured_image[0];
							$col_image_info = is_rtl()?'col-xs-5 col-xs-push-7':'col-xs-5';
							$col_post_info = is_rtl()?'col-xs-7 col-xs-pull-5':'col-xs-7'; 
						} else {
							if (!empty($detheme_config['dt-default-single-post-image']['url'])) : 
								$imgurl = $detheme_config['dt-default-single-post-image']['thumbnail'];
								$col_image_info = is_rtl()?'col-xs-5 col-xs-push-7':'col-xs-5';
								$col_post_info = is_rtl()?'col-xs-7 col-xs-pull-5':'col-xs-7'; 

								$attachment_id = array_key_exists('id', $detheme_config['dt-default-single-post-image'] ) ? $detheme_config['dt-default-single-post-image']['id']:0;
							endif; 
						} 
					?>											

					<?php 
						if (!empty($imgurl)) :

							$alt_image = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
					?>
					<div class="<?php echo $col_image_info; ?> image-info">
						<a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($imgurl); ?>" class="widget-post-thumb img-responsive" alt="<?php print esc_attr($alt_image);?>" /></a>
					</div>
					<?php
						endif;
					?>

						<div class="<?php echo $col_post_info; ?> post-info">
							<a href="<?php the_permalink(); ?>" class="widget-post-title"><?php get_the_title() ? the_title() : the_ID(); ?></a>
						</div>
						<div class="meta-info col-xs-12">
						<?php if(is_rtl()):?>
							<div class="float-right">
								<i class="icon-clock"></i> <?php echo get_the_date(); ?>
							</div>
							<div class="float-left">
								<i class="icon-comment-empty"></i> <?php echo get_comments_number(); ?>
							</div>
						<?php else:?>

							<div class="float-left">
								<i class="icon-clock"></i> <?php echo get_the_date(); ?>
							</div>
							<div class="float-right">
								<i class="icon-comment-empty"></i> <?php echo get_comments_number(); ?>
							</div>
						<?php endif;?>	
						</div>

					</div>
				</div>
<?php
						$i++;
					endwhile; 
				wp_reset_postdata();
				endif; 
?>
		  	</div>
		  	<div class="tab-pane fade" id="recent_<?php echo sanitize_key($this->get_field_id('dt')); ?>">
<?php
				$r = new WP_Query(array( 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'orderby' => 'date', 'order' => 'DESC' ) );
				if ($r->have_posts()) :
					$i = 0;
					while ( $r->have_posts() ) : $r->the_post();
?>
				<div class="row">
				<div class="rowlist gray_border_bottom">
					<?php
						$imgurl = "";
						$col_post_info = 'col-xs-12'; 
						$attachment_id=get_post_thumbnail_id(get_the_ID());
						$featured_image = wp_get_attachment_image_src($attachment_id,'thumbnail',false); 
						if (isset($featured_image[0])) {
							$imgurl = $featured_image[0];
							$col_image_info = is_rtl()?'col-xs-5 col-xs-push-7':'col-xs-5';
							$col_post_info = is_rtl()?'col-xs-7 col-xs-pull-5':'col-xs-7'; 
						} else {
							if (!empty($detheme_config['dt-default-single-post-image']['url'])) : 
								$imgurl = $detheme_config['dt-default-single-post-image']['thumbnail'];
								$col_image_info = is_rtl()?'col-xs-5 col-xs-push-7':'col-xs-5';
								$col_post_info = is_rtl()?'col-xs-7 col-xs-pull-5':'col-xs-7'; 
								$attachment_id = array_key_exists('id', $detheme_config['dt-default-single-post-image'] ) ? $detheme_config['dt-default-single-post-image']['id']:0;
							endif;
						}
					?>											

					<?php 
						if (!empty($imgurl)) :

							$alt_image = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
					?>
					<div class="<?php echo $col_image_info; ?> image-info">
						<a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($imgurl); ?>" class="widget-post-thumb img-responsive" alt="<?php print esc_attr($alt_image);?>" /></a>
					</div>
					<?php
						endif;
					?>
					<div class="<?php echo $col_post_info; ?> post-info">
						<a href="<?php the_permalink(); ?>" class="widget-post-title"><?php get_the_title() ? the_title() : the_ID(); ?></a>
					</div>
					<div class="meta-info col-xs-12">
						<?php if(is_rtl()):?>
							<div class="float-right">
								<i class="icon-clock"></i> <?php echo get_the_date(); ?>
							</div>
							<div class="float-left">
								<i class="icon-comment-empty"></i> <?php echo get_comments_number(); ?>
							</div>
						<?php else:?>

							<div class="float-left">
								<i class="icon-clock"></i> <?php echo get_the_date(); ?>
							</div>
							<div class="float-right">
								<i class="icon-comment-empty"></i> <?php echo get_comments_number(); ?>
							</div>
						<?php endif;?>	
					</div>

				</div>
				</div>
<?php
						$i++;
					endwhile; 
				wp_reset_postdata();
				endif;
?>
		  	</div>
		  	<div class="tab-pane fade" id="comments_<?php echo sanitize_key($this->get_field_id('dt')); ?>">
<?php
				$args = array(
					'status' => 'approve',
					'number' => $number
				);
				$comments = get_comments($args);
				$i = 0;
				foreach($comments as $comment) :
?>
				<div class="row">
				<div class="rowlist gray_border_bottom">
					<div class="col-xs-5 image-info">
						<?php 
							$avatar_url = billio_get_avatar_url(get_avatar( $comment->user_id, 92 )); 
							if (isset($avatar_url)) {
						?>
						<a href="<?php echo get_permalink($comment->comment_post_ID); ?>"><img src="<?php echo esc_url($avatar_url); ?>" alt="<?php print esc_attr($comment->comment_author);?>" class="widget-post-thumb img-responsive" /></a>
						<?php 
							} 
						?>
					</div>
					<div class="col-xs-7 post-info">
						<a href="<?php echo get_permalink($comment->comment_post_ID); ?>" class="widget-post-title">
							<?php echo $comment->comment_author; ?>
						</a>
						<p class="comment"><?php echo $comment->comment_content; ?></p>
					</div>
				</div>
				</div>
<?php
					$i++;
				endforeach;
?>
		  	</div>
		</div>					
		
		<?php echo $after_widget; ?>
<?php
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
				
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['dt_widget_tabs']) )
			delete_option('dt_widget_tabs');
		return $instance;
	}
	function flush_widget_cache() {
		wp_cache_delete('dt_widget_tabs', 'widget');
	}
	function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 3;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','billio'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts/comments to show:','billio'); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}
/** /DT_Tabs **/

class Walker_DT_Category extends Walker_Category {
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);
		$cat_name = esc_attr( $category->name );
		$cat_name = apply_filters( 'list_cats', $cat_name, $category );
		$link = '<a href="' . esc_url( get_term_link($category) ) . '" ';
		if ( $use_desc_for_title == 0 || empty($category->description) )
			$link .= 'title="' . esc_attr( sprintf(__( 'View all posts filed under %s','billio' ), $cat_name) ) . '"';
		else
			$link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
		$link .= '>';
		$link .= $cat_name . '</a>';
		if ( !empty($feed_image) || !empty($feed) ) {
			$link .= ' ';
			if ( empty($feed_image) )
				$link .= '(';
			$link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $feed_type ) ) . '"';
			if ( empty($feed) ) {
				$alt = ' alt="' . sprintf(__( 'Feed for all posts filed under %s','billio' ), $cat_name ) . '"';
			} else {
				$title = ' title="' . $feed . '"';
				$alt = ' alt="' . $feed . '"';
				$name = $feed;
				$link .= $title;
			}
			$link .= '>';
			if ( empty($feed_image) )
				$link .= $name;
			else
				$link .= "<img src='$feed_image'$alt$title" . ' />';
			$link .= '</a>';
			if ( empty($feed_image) )
				$link .= ')';
		}
		if ( !empty($show_count) )
			$link .= ' (' . intval($category->count) . ')';
		if ( 'list' == $args['style'] ) {
			$output .= "\t<li";
			$class = 'cat-item cat-item-' . $category->term_id;
			if ( !empty($current_category) ) {
				$_current_category = get_term( $current_category, $category->taxonomy );
				if ( $category->term_id == $current_category )
					$class .=  ' current-cat';
				elseif ( $category->term_id == $_current_category->parent )
					$class .=  ' current-cat-parent';
			}
			$output .=  ' class="' . $class . '"';
			$output .= ">$link\n";
		} else {
			$output .= "\t$link<br />\n";
		}
	}
}
class WP_DT_Widget_Recent_Posts  extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'dt_widget_recent_post', 'description' => __( "Your site&#8217;s most recent Posts.") );
		parent::__construct('dt-recent-posts', __('DT Recent Posts'), $widget_ops);
		$this->alt_option_name = 'dt_widget_recent_post';

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {

		$cache = wp_cache_get('dt_widget_recent_post', 'widget');
		if ( !is_array($cache) )
			$cache = array();
		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;
		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}
		ob_start();
		extract($args);
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts','billio' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 10;
		if ( ! $number )
 			$number = 10;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
		$r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
			<?php if ( $show_date ) : ?>
				<br/><?php if(is_rtl()): ?>
				<span class="post-date"><?php echo get_the_date(); ?></span> - <span class="post-author"><?php echo get_the_author_meta( 'nickname' ); ?></span>
			<?php else:?>
				<span class="post-author"><?php echo get_the_author_meta( 'nickname' ); ?></span> - <span class="post-date"><?php echo get_the_date(); ?></span>
			<?php endif; 
			endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();
		endif;
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('dt_widget_recent_post', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['dt_widget_recent_post']) )
			delete_option('dt_widget_recent_post');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('dt_widget_recent_post', 'widget');
	}

	function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
<?php
	}

}
function billio_create_category_walker($args){
	$args['walker']=new Walker_DT_Category();
	return $args;
}

add_filter('widget_categories_args', 'billio_create_category_walker');

function billio_format_widget_archive($args){
	return $args;
}

add_filter('widget_archives_args','billio_format_widget_archive');

class WP_DT_Widget_Recent_Comments extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'dt_widget_recent_comments', 'description' => __( 'Your site&#8217;s most recent comments.' ) );
		parent::__construct('dt-recent-comments', __('DT Recent Comments'), $widget_ops);
		$this->alt_option_name = 'dt_widget_recent_comments';

		if ( is_active_widget(false, false, $this->id_base) )
			add_action( 'wp_head', array($this, 'recent_comments_style') );

		add_action( 'comment_post', array($this, 'flush_widget_cache') );
		add_action( 'edit_comment', array($this, 'flush_widget_cache') );
		add_action( 'transition_comment_status', array($this, 'flush_widget_cache') );
	}

	function recent_comments_style() {

		/**
		 * Filter the Recent Comments default widget styles.
		 *
		 * @since 3.1.0
		 *
		 * @param bool   $active  Whether the widget is active. Default true.
		 * @param string $id_base The widget ID.
		 */
		if ( ! current_theme_supports( 'widgets' ) // Temp hack #14876
			|| ! apply_filters( 'show_recent_comments_widget_style', true, $this->id_base ) )
			return;
		?>
	<style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>
<?php
	}

	function flush_widget_cache() {
		wp_cache_delete('dt_widget_recent_comments', 'widget');
	}


	function widget( $args, $instance ) {
		global $comments, $comment;
		$cache = wp_cache_get('dt_widget_recent_comments', 'widget');
		if ( ! is_array( $cache ) )
			$cache = array();
		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;
		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}
 		extract($args, EXTR_SKIP);
 		$output = '';
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Comments','billio' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		$show_comment = isset( $instance['show_comment'] ) ? $instance['show_comment'] : false;
		if ( ! $number )
 			$number = 5;
		$comments = get_comments( apply_filters( 'widget_comments_args', array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish' ) ) );
		$output .= $before_widget;
		if ( $title )
			$output .= $before_title . $title . $after_title;
		$output .= '<ul id="recentcomments">';
		if ( $comments ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );
			foreach ( (array) $comments as $comment) {
				$output .=  '<li class="recentcomments"><span class="comment-author">' .get_comment_author().'</span><a class="clearfix" href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">' .(($show_comment)?$comment->comment_content:get_the_title($comment->comment_post_ID)) . '</a></li>';
			}
 		}
		$output .= '</ul>';
		$output .= $after_widget;
		echo $output;
		$cache[$args['widget_id']] = $output;
		wp_cache_set('dt_widget_recent_comments', $cache, 'widget');
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = absint( $new_instance['number'] );
		$instance['show_comment'] = isset( $new_instance['show_comment'] ) ? (bool) $new_instance['show_comment'] : false;
		$this->flush_widget_cache();
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['dt_widget_recent_comments']) )
			delete_option('dt_widget_recent_comments');
		return $instance;
	}
	function form( $instance ) {
		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_comment = isset( $instance['show_comment'] ) ? (bool) $instance['show_comment'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','billio' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of comments to show:','billio' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
		<p><input class="checkbox" type="checkbox" <?php checked( $show_comment ); ?> id="<?php echo $this->get_field_id( 'show_comment' ); ?>" name="<?php echo $this->get_field_name( 'show_comment' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_comment' ); ?>"><?php _e( 'Display comment content?','billio' ); ?></label></p>
<?php
	}
}
/** DT_Twitter_Slider **/
class DT_Twitter_Slider extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => 'dt_widget_twitter_slider', 'description' => __( "Display most recent tweets from twitter in slider.",'billio') );
		parent::__construct('dt_twitter_slider', __('DT Twitter Slider','billio'), $widget_ops);
		$this->alt_option_name = 'dt_widget_twitter_slider';
		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}
	function flush_widget_cache() {
		wp_cache_delete('dt_widget_twitter_slider', 'widget');
	}
	function form( $instance ) {
		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$twitteraccount  = isset( $instance['twitteraccount'] ) ? esc_attr( $instance['twitteraccount'] ) : 'envato';
		$numberoftweets = isset( $instance['numberoftweets'] ) ? absint( $instance['numberoftweets'] ) : 4;
		$dateformat  = isset( $instance['dateformat'] ) ? esc_attr( $instance['dateformat'] ) : '%b. %d, %Y';
		$twittertemplate  = isset( $instance['twittertemplate'] ) ? esc_attr( $instance['twittertemplate'] ) : '{{date}}<br />{{tweet}}';
		$isautoplay = isset( $instance['isautoplay'] ) ? (bool) $instance['isautoplay'] : true;
		$transitionthreshold = isset( $instance['transitionthreshold'] ) ? absint( $instance['transitionthreshold'] ) : 500;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','billio' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'twitteraccount' ); ?>"><?php _e( 'Twitter Account:','billio'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'twitteraccount' ); ?>" name="<?php echo $this->get_field_name( 'twitteraccount' ); ?>" type="text" value="<?php echo $twitteraccount; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'numberoftweets' ); ?>"><?php _e( 'Number of tweets to show:','billio' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'numberoftweets' ); ?>" name="<?php echo $this->get_field_name( 'numberoftweets' ); ?>" type="text" value="<?php echo $numberoftweets; ?>" size="3" /></p>
		<p><label for="<?php echo $this->get_field_id( 'dateformat' ); ?>"><?php _e( 'Date Format:','billio' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'dateformat' ); ?>" name="<?php echo $this->get_field_name( 'dateformat' ); ?>" type="text" value="<?php echo $dateformat; ?>" /><br />
		<?php echo __('%d : day, %m: month in number, %b: textual month abbreviation, %B: textual month, %y: 2 digit year, %Y: 4 digit year','billio'); ?></p>
		<p><label for="<?php echo $this->get_field_id( 'twittertemplate' ); ?>"><?php _e( 'Template :','billio' ); ?><br /></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'twittertemplate' ); ?>" name="<?php echo $this->get_field_name( 'twittertemplate' ); ?>" type="text" value="<?php echo $twittertemplate; ?>" /><br />
		<?php echo __('{{date}}: Post Date, {{tweet}}: tweet text','billio'); ?>
		</p>
		<p><label for="<?php echo $this->get_field_id( 'isautoplay' ); ?>"><?php _e( 'Auto Play:','billio' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'isautoplay' ); ?>" name="<?php echo $this->get_field_name( 'isautoplay' ); ?>">
			<option value="1" <?php if ($isautoplay==1) echo 'selected="true"' ?>>true</option>
			<option value="0" <?php if ($isautoplay!=1) echo 'selected="true"' ?>>false</option>
		</select>
		</p>
		<p><label for="<?php echo $this->get_field_id( 'transitionthreshold' ); ?>"><?php _e( 'Transition Threshold (msec):','billio' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'transitionthreshold' ); ?>" name="<?php echo $this->get_field_name( 'transitionthreshold' ); ?>" type="text" value="<?php echo $transitionthreshold; ?>" size="3" /></p>
<?php
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['twitteraccount'] = strip_tags($new_instance['twitteraccount']);
		$instance['numberoftweets'] = absint( $new_instance['numberoftweets'] );
		$instance['dateformat'] = $new_instance['dateformat'];
		$instance['twittertemplate'] = $new_instance['twittertemplate'];
		$instance['isautoplay'] = absint($new_instance['isautoplay']);
		$instance['transitionthreshold'] = absint( $new_instance['transitionthreshold'] );
		$this->flush_widget_cache();
		return $instance;
	}
	function widget( $args, $instance ) {
		global $detheme_Scripts;
		extract($args);
		if ( ! isset( $args['widget_id'] ) ) $args['widget_id'] = $this->id;
		
	    $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$widget_id = $args['widget_id'];
		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$twitteraccount  = isset( $instance['twitteraccount'] ) ? esc_attr( $instance['twitteraccount'] ) : 'envato';
		$numberoftweets = isset( $instance['numberoftweets'] ) ? absint( $instance['numberoftweets'] ) : 4;
		$dateformat  = isset( $instance['dateformat'] ) ? esc_attr( $instance['dateformat'] ) : '%b. %d, %Y';
		$twittertemplate  = isset( $instance['twittertemplate'] ) ? $instance['twittertemplate'] : '{{date}}<br />{{tweet}}';
		$isautoplay = isset( $instance['isautoplay'] ) ? absint($instance['isautoplay']) : 1;
		$strautoplay = ($isautoplay==1) ? 'true' : 'false'; 
		$transitionthreshold = isset( $instance['transitionthreshold'] ) ? absint( $instance['transitionthreshold'] ) : 500;

	    wp_enqueue_script( 'tweetie', get_template_directory_uri() . '/lib/twitter_slider/tweetie.js', array( 'jquery' ), '1.0', true);
       	wp_register_script( 'owl.carousel', get_template_directory_uri() . '/js/owl.carousel'.$suffix.'.js', array( 'jquery' ), '1.29', true );
       	wp_enqueue_script( 'owl.carousel');

		wp_enqueue_style( 'owl.carousel',get_template_directory_uri() . '/css/owl.carousel.css', array(), '', 'all' );

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] :"";
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		
		echo '
       	<div class="row">
            <div class="col col-xs-12">
                <div id="'.$widget_id.'" class="sequence-twitter"></div>  
            </div>
        </div>';
		$widgetID = $this->get_field_id('dt');
        $script='jQuery(document).ready(function($) {
    		\'use strict\';
			
            $(\'#'.$widget_id.'\').twittie({
            	element_id: \''.$this->get_field_id('dt').'\',
                username: \''.$twitteraccount.'\',
                count: '.$numberoftweets.',
                hideReplies: false,
                dateFormat: \''.$dateformat.'\',
                template: \''.$twittertemplate.'\',
                apiPath: \''. get_template_directory_uri() . '/lib/twitter_slider/api/tweet.php\'
            },function(){
	        	$(\'#'.$widgetID.'\').owlCarousel({
	                items       : 1, //10 items above 1000px browser width
	                itemsDesktop    : [1000,1], //5 items between 1000px and 901px
	                itemsDesktopSmall : [900,1], // 3 items betweem 900px and 601px
	                itemsTablet : [600,1], //2 items between 600 and 0;
	                itemsMobile : false, // itemsMobile disabled - inherit from itemsTablet option
	                pagination  : true,
	                autoPlay	: ' . $strautoplay . ',
	                slideSpeed	: 200,
	                paginationSpeed  : ' . $transitionthreshold . '
	            });
            });
    	});';
    	array_push($detheme_Scripts,$script);
        echo $after_widget;
	}
}

/** DT_Accordion **/
class DT_Accordion extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => 'dt_widget_accordion', 'description' => __( "Display information in accordion style.",'billio') );
		parent::__construct('dt_accordion', __('DT Accordion','billio'), $widget_ops);
		$this->alt_option_name = 'dt_widget_accordion';
		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}
	function flush_widget_cache() {
		wp_cache_delete('dt_widget_accordion', 'widget');
	}
	function form( $instance ) {
		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$acctitle1  = isset( $instance['acctitle1'] ) ? esc_attr( $instance['acctitle1'] ) : '';
		$acctitle2  = isset( $instance['acctitle2'] ) ? esc_attr( $instance['acctitle2'] ) : '';
		$acctitle3  = isset( $instance['acctitle3'] ) ? esc_attr( $instance['acctitle3'] ) : '';
		$acctitle4  = isset( $instance['acctitle4'] ) ? esc_attr( $instance['acctitle4'] ) : '';
		$accdesc1  = isset( $instance['accdesc1'] ) ? esc_textarea( $instance['accdesc1'] ) : '';
		$accdesc2  = isset( $instance['accdesc2'] ) ? esc_textarea( $instance['accdesc2'] ) : '';
		$accdesc3  = isset( $instance['accdesc3'] ) ? esc_textarea( $instance['accdesc3'] ) : '';
		$accdesc4  = isset( $instance['accdesc4'] ) ? esc_textarea( $instance['accdesc4'] ) : '';
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','billio' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'acctitle1' ); ?>"><?php _e( 'Accordion Title 1:','billio' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'acctitle1' ); ?>" name="<?php echo $this->get_field_name( 'acctitle1' ); ?>" type="text" value="<?php echo $acctitle1; ?>" /></p>
		<label for="<?php echo $this->get_field_id( 'accdesc1' ); ?>"><?php _e( 'Accordion Description 1:','billio' ); ?></label>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('accdesc1'); ?>" name="<?php echo $this->get_field_name('accdesc1'); ?>"><?php echo $accdesc1; ?></textarea>
		<p><label for="<?php echo $this->get_field_id( 'acctitle2' ); ?>"><?php _e( 'Accordion Title 2:','billio' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'acctitle2' ); ?>" name="<?php echo $this->get_field_name( 'acctitle2' ); ?>" type="text" value="<?php echo $acctitle2; ?>" /></p>
		<label for="<?php echo $this->get_field_id( 'accdesc2' ); ?>"><?php _e( 'Accordion Description 2:','billio' ); ?></label>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('accdesc2'); ?>" name="<?php echo $this->get_field_name('accdesc2'); ?>"><?php echo $accdesc2; ?></textarea>
		<p><label for="<?php echo $this->get_field_id( 'acctitle3' ); ?>"><?php _e( 'Accordion Title 3:','billio' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'acctitle3' ); ?>" name="<?php echo $this->get_field_name( 'acctitle3' ); ?>" type="text" value="<?php echo $acctitle3; ?>" /></p>
		<label for="<?php echo $this->get_field_id( 'accdesc3' ); ?>"><?php _e( 'Accordion Description 3:','billio' ); ?></label>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('accdesc3'); ?>" name="<?php echo $this->get_field_name('accdesc3'); ?>"><?php echo $accdesc3; ?></textarea>
		<p><label for="<?php echo $this->get_field_id( 'acctitle4' ); ?>"><?php _e( 'Accordion Title 4:','billio' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'acctitle4' ); ?>" name="<?php echo $this->get_field_name( 'acctitle4' ); ?>" type="text" value="<?php echo $acctitle4; ?>" /></p>
		<label for="<?php echo $this->get_field_id( 'accdesc4' ); ?>"><?php _e( 'Accordion Description 4:','billio' ); ?></label>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('accdesc4'); ?>" name="<?php echo $this->get_field_name('accdesc4'); ?>"><?php echo $accdesc4; ?></textarea>
<?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['acctitle1'] = strip_tags($new_instance['acctitle1']);
		$instance['acctitle2'] = strip_tags($new_instance['acctitle2']);
		$instance['acctitle3'] = strip_tags($new_instance['acctitle3']);
		$instance['acctitle4'] = strip_tags($new_instance['acctitle4']);
		if ( current_user_can('unfiltered_html') ) {
			$instance['accdesc1'] =  $new_instance['accdesc1'];
			$instance['accdesc2'] =  $new_instance['accdesc2'];
			$instance['accdesc3'] =  $new_instance['accdesc3'];
			$instance['accdesc4'] =  $new_instance['accdesc4'];
		} else {
			$instance['accdesc1'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['accdesc1']) ) ); // wp_filter_post_kses() expects slashed
			$instance['accdesc2'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['accdesc2']) ) ); // wp_filter_post_kses() expects slashed
			$instance['accdesc3'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['accdesc3']) ) ); // wp_filter_post_kses() expects slashed
			$instance['accdesc4'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['accdesc4']) ) ); // wp_filter_post_kses() expects slashed
		}
		$this->flush_widget_cache();
		return $instance;
	}

	function widget( $args, $instance ) {
		global $detheme_Scripts;
		extract($args);
		if ( ! isset( $args['widget_id'] ) ) $args['widget_id'] = $this->id;
		
		$widget_id = $args['widget_id'];
		$acctitle1  = isset( $instance['acctitle1'] ) ? esc_attr( $instance['acctitle1'] ) : '';
		$acctitle2  = isset( $instance['acctitle2'] ) ? esc_attr( $instance['acctitle2'] ) : '';
		$acctitle3  = isset( $instance['acctitle3'] ) ? esc_attr( $instance['acctitle3'] ) : '';
		$acctitle4  = isset( $instance['acctitle4'] ) ? esc_attr( $instance['acctitle4'] ) : '';
		$accdesc1 = apply_filters( 'widget_text', empty( $instance['accdesc1'] ) ? '' : $instance['accdesc1'], $instance );
		$accdesc2 = apply_filters( 'widget_text', empty( $instance['accdesc2'] ) ? '' : $instance['accdesc2'], $instance );
		$accdesc3 = apply_filters( 'widget_text', empty( $instance['accdesc3'] ) ? '' : $instance['accdesc3'], $instance );
		$accdesc4 = apply_filters( 'widget_text', empty( $instance['accdesc4'] ) ? '' : $instance['accdesc4'], $instance );

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] :"";
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		
		echo '<div class="panel-group custom-accordion" id="'.$widget_id.'">';
		if (!empty($acctitle1)) :		  
		echo '<div class="panel panel-default">
			    <div class="panel-heading openedup" data-toggle="collapse" data-parent="#'.$widget_id.'">
			      <h4 class="panel-title">'.$acctitle1.'</h4>
			      <a class="btn-accordion opened" data-toggle="collapse" data-parent="#'.$widget_id.'" href="#collapseOne'.$widget_id.'"><i class="icon-minus-1"></i></a>
			    </div>
			    <div id="collapseOne'.$widget_id.'" class="panel-collapse collapse in">
			      <div class="panel-body">'. wpautop( $accdesc1 ) . '</div>
			    </div>
			  </div>';
		endif;
		
		if (!empty($acctitle2)) :	  
		echo '<div class="panel panel-default">
			    <div class="panel-heading" data-toggle="collapse" data-parent="#'.$widget_id.'">
			      <h4 class="panel-title">'.$acctitle2.'</h4>
			      <a class="btn-accordion" data-toggle="collapse" data-parent="#'.$widget_id.'" href="#collapseTwo'.$widget_id.'"><i class="icon-plus-1"></i></a>
			    </div>
			    <div id="collapseTwo'.$widget_id.'" class="panel-collapse collapse">
			      <div class="panel-body">'. wpautop( $accdesc2 ) . '</div>
			    </div>
			  </div>';
		endif;
		if (!empty($acctitle3)) :
		echo '<div class="panel panel-default">
			    <div class="panel-heading" data-toggle="collapse" data-parent="#'.$widget_id.'">
			      <h4 class="panel-title">'.$acctitle3.'</h4>
			      <a class="btn-accordion" data-toggle="collapse" data-parent="#'.$widget_id.'" href="#collapseThree'.$widget_id.'"><i class="icon-plus-1"></i></a>
			    </div>
			    <div id="collapseThree'.$widget_id.'" class="panel-collapse collapse">
			      <div class="panel-body">'. wpautop( $accdesc3 ) . '</div>
			    </div>
			  </div>';
		endif;
		if (!empty($acctitle4)) :
		echo '<div class="panel panel-default">
			    <div class="panel-heading" data-toggle="collapse" data-parent="#'.$widget_id.'">
			      <h4 class="panel-title">'.$acctitle4.'</h4>
			      <a class="btn-accordion" data-toggle="collapse" data-parent="#'.$widget_id.'" href="#collapseFour'.$widget_id.'"><i class="icon-plus-1"></i></a>
			    </div>
			    <div id="collapseFour'.$widget_id.'" class="panel-collapse collapse">
			      <div class="panel-body">'. wpautop( $accdesc4 ) . '</div>
			    </div>
			  </div>';
		endif;
		echo '</div>';
        echo $after_widget;
	}
}
function billio_widgets_init(){
	 register_widget('DT_Tabs');
	 register_widget('DT_Twitter_Slider');
	 register_widget('DT_Accordion');
	 register_widget('WP_DT_Widget_Recent_Posts');
	 register_widget('WP_DT_Widget_Recent_Comments');
}
// widget init
add_action('widgets_init', 'billio_widgets_init',1);
?>
