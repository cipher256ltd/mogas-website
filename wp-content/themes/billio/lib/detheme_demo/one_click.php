<?php
defined('ABSPATH') or die();
/* 
 * Plugin Name: Detheme Demo Installer
 * Plugin URI: http://www.detheme.com/
 * Description: Import all the detheme demo content and theme settings with ease
 * Version: 1.2.5
 * Author: detheme.com
 * Author URI: http://www.detheme.com/
 * Domain Path: /languages/
 *
 */


if(!class_exists('WP_Filesystem_Direct')){

	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
}

class detheme_one_click_demo{

	var $version;
	var $authors = array();
	var $posts = array();
	var $terms = array();
	var $categories = array();
	var $tags = array();
	var $base_url = '';

	// mappings from old information to new
	var $processed_authors = array();
	var $author_mapping = array();
	var $processed_terms = array();
	var $processed_posts = array();
	var $post_orphans = array();
	var $processed_menu_items = array();
	var $menu_item_orphans = array();
	var $missing_menu_items = array();
	var $skip_attachment=false;
	var $skip_woocommerce=false;
	var $skip_menu=false;

	var $url_remap = array();
	var $featured_images = array();
	var $wp_filesystem=NULL;

	function __construct(){

		if(!defined('DETHEME_ONE_PATH')){
			define('DETHEME_ONE_PATH',dirname(__FILE__));
		} 

		if(!defined('DETHEME_ONE_URI')){
			define('DETHEME_ONE_URI',get_template_directory_uri().'/lib/detheme_demo/');
		} 

	}

	function init(){
		add_action('init', array($this,'load_detheme_demo'));
   	}

   	function load_detheme_demo(){

		if(! is_admin() )
			return;

		$this->wp_filesystem= new WP_Filesystem_Direct(array());

		if(!class_exists('WXR_DT_Parser')){

			load_template( DETHEME_ONE_PATH.'/parsers.php',true); 
		}

		$locale = get_locale();
		load_textdomain('detheme_demo', DETHEME_ONE_PATH . '/languages/'.$locale.".mo");
	    add_action('admin_menu', array($this,'register_submenu_page'));
   		add_filter('import_option_accepted',array($this,'import_option_accepted'));
		add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
   	}

   	function enqueue_scripts(){

        wp_enqueue_script( 'bootstrap' , DETHEME_ONE_URI.'js/bootstrap.js', array( 'jquery' ), '3.0', true );
        wp_enqueue_script( 'detheme-one-click', DETHEME_ONE_URI . 'js/scripts.js', array('jquery'), '1.3.3');
        wp_enqueue_style( 'detheme-one-click',DETHEME_ONE_URI."css/style.css");
   	}


   	function do_install($package_name){

   		if(!wp_verify_nonce( isset($_POST['demo_install_setting'])?$_POST['demo_install_setting']:"", 'demo-install-setting'))
	    return false;

		$wp_filesystem=$this->wp_filesystem;

		$skip_content=isset($_POST['skip_content'])?intval($_POST['skip_content']):false;
		$skip_theme_option=isset($_POST['skip_theme_option'])?intval($_POST['skip_theme_option']):false;
		$skip_sidebar=isset($_POST['skip_sidebar'])?intval($_POST['skip_sidebar']):false;
		$skip_attachment=isset($_POST['skip_attachment'])?intval($_POST['skip_attachment']):false;
		$skip_menu=isset($_POST['skip_menu'])?intval($_POST['skip_menu']):false;

		$install_content=false;
		$install_theme_option=false;
		$install_sidebar_widgets=false;
		$install_slide=false;
		$install_ess_grid=false;

 		$packages=detheme_one_click_demo::get_packages();

 		if(!isset($packages[$package_name]))
 			return false;

 		$package=$packages[$package_name];

		do_action( 'detheme_import_start' );

   		add_action('wp_import_insert_post',array($this,'assign_home_page'),1,3);

   		$path= $package['path'];

   		if($wp_filesystem->exists( $path."functions.php")){

   			require_once ($path."functions.php");
   		} 

		$demo_xml=$path."demo_content.xml";
		$theme_option=$path."theme_option.json";
		$theme_widget=$path."widget_option.json";
		$slide_path=$path."revslider/";
		$ls_slide_path=$path."lslider/";
		$ess_path=$path."ess_grid/";


		if (is_plugin_active('woocommerce/woocommerce.php')){

			$skip_woocommerce=isset($_POST['skip_woocommerce'])?intval($_POST['skip_woocommerce']):false;

			if((bool) $skip_woocommerce){
				$this->skip_woocommerce=true;
			}


		}

		if((bool)$skip_attachment){
			$this->skip_attachment=true;
		}

		if((bool)$skip_menu){
			$this->skip_menu=true;
		}

		if(! (bool)$skip_content){

			$install_content=$this->install_content($demo_xml);
		}

		if(! (bool)$skip_theme_option){
			$install_theme_option=$this->install_theme_option($theme_option);
		}

		if(! (bool)$skip_sidebar){
			$install_sidebar_widgets=$this->install_sidebar_widgets($theme_widget);
		}

		if (is_plugin_active('revslider/revslider.php') && is_dir($slide_path)){

			load_template(DETHEME_ONE_PATH. '/revslider_importer.php',true);
			$skip_slide=isset($_POST['skip_slide'])?intval($_POST['skip_slide']):false;

			if(! (bool) $skip_slide){
				$install_slide=$this->install_revo_slider($slide_path);
			}


		}

		if (is_plugin_active('essential-grid/essential-grid.php') && is_dir($ess_path)){

			if(!class_exists('Essential_Grid_Import')){
				require_once(EG_PLUGIN_PATH . '/admin/includes/import.class.php');
			}
			$skip_ess=isset($_POST['skip_ess'])?intval($_POST['skip_ess']):false;
			if(! (bool) $skip_ess){

				$install_ess_grid=$this->install_ess_grid($ess_path);
			}

		}

		do_action('detheme_import_finish');	

		return

   		$install_content ||
  		$install_theme_option ||
  		$install_slide || $install_ess_grid || 
   		$install_sidebar_widgets;

   	}

   	function exctract_json($file){

   		$wp_filesystem=$this->wp_filesystem;

   		if(! $wp_filesystem->exists( $file)) return false;

		$file_contents = $wp_filesystem->get_contents( $file );

		return $import_data = maybe_unserialize(json_decode( $file_contents, true ));
   	}

   	function install_sidebar_widgets($theme_widget){

		global $wp_rewrite;

   		$import_data=$this->exctract_json($theme_widget);

   		if(! $import_data) return false;

		$options=$import_data['options'];

		foreach ($options as $option_name => $options) {

			if(! $this->is_allowed_import($option_name))
				continue;

			$option_value = maybe_unserialize( $options );

			if ( in_array( $option_name, $import_data['no_autoload'] ) ) {
				delete_option( $option_name );
				add_option( $option_name, $option_value, '', 'no' );
			} else {
				update_option( $option_name, $option_value );
			}

			if('permalink_structure'==$option_name && is_object($wp_rewrite)){
				$wp_rewrite->set_permalink_structure( $option_value );
				flush_rewrite_rules();
			}
		}

		return true;

   	}

   	function install_theme_option($theme_option){

   		$import_data=$this->exctract_json($theme_option);

  		if(! $import_data) return false;
		$option_name=apply_filters('theme_option_name','detheme_config');

		if(update_option( $option_name, $import_data )){

			do_action('theme_option_name_update',$import_data);

		}
		return true;
   	}

/*

	reference:
	wordpress importir plugin by wordpressdotorg
	Plugin URI: http://wordpress.org/extend/plugins/wordpress-importer/			

*/
	function install_content($demo_xml){

		$wp_filesystem=$this->wp_filesystem;

		if(! $wp_filesystem->exists( $demo_xml)) return false;

		$this->import_start( $demo_xml );

		wp_suspend_cache_invalidation( true );

		$this->process_categories();
		$this->process_tags();
		$this->process_terms();
		$this->process_posts();

		wp_suspend_cache_invalidation( false );

		$this->backfill_parents();
		$this->backfill_attachment_urls();
		$this->remap_featured_images();

		wp_cache_flush();
		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );


		return true;
	}

	function import_start( $file ) {
		if ( ! is_file($file) ) {
			echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'detheme_demo' ) . '</strong><br />';
			esc_html_e( 'The file does not exist, please try again.', 'detheme_demo' ) . '</p>';
			die();
		}

		$parser = new WXR_DT_Parser();



		$import_data = $parser->parse( $file );



		if ( is_wp_error( $import_data ) ) {
			echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'detheme_demo' ) . '</strong><br />'.
			esc_html( $import_data->get_error_message(),'detheme_demo' ) . '</p>';
			die();
		}

		$this->version = $import_data['version'];
		$this->posts = $import_data['posts'];
		$this->terms = $import_data['terms'];
		$this->categories = $import_data['categories'];
		$this->tags = $import_data['tags'];
		$this->base_url = esc_url( $import_data['base_url'] );

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
	}


	function process_categories() {

		$this->categories = apply_filters( 'wp_import_categories', $this->categories );

		if ( empty( $this->categories ) )
			return;

		foreach ( $this->categories as $cat ) {
			// if the category already exists leave it alone
			$term_id = term_exists( $cat['category_nicename'], 'category' );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($cat['term_id']) )
					$this->processed_terms[intval($cat['term_id'])] = (int) $term_id;
				continue;
			}

			$category_parent = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
			$category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';
			$catarr = array(
				'category_nicename' => $cat['category_nicename'],
				'category_parent' => $category_parent,
				'cat_name' => $cat['cat_name'],
				'category_description' => $category_description
			);

			$id = wp_insert_category( $catarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($cat['term_id']) )
					$this->processed_terms[intval($cat['term_id'])] = $id;
			} else {
				print '<div class="updated error">';
				printf( esc_html__( 'Failed to import category %s', 'detheme_demo' ), esc_html($cat['category_nicename']) );
				if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
					echo ': ' . $id->get_error_message();
				echo '<br />';
				print '</div>';
				continue;
			}
		}

		unset( $this->categories );
	}

	function process_tags() {
		$this->tags = apply_filters( 'wp_import_tags', $this->tags );

		if ( empty( $this->tags ) )
			return;

		foreach ( $this->tags as $tag ) {
			// if the tag already exists leave it alone
			$term_id = term_exists( $tag['tag_slug'], 'post_tag' );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($tag['term_id']) )
					$this->processed_terms[intval($tag['term_id'])] = (int) $term_id;
				continue;
			}

			$tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
			$tagarr = array( 'slug' => $tag['tag_slug'], 'description' => $tag_desc );

			$id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($tag['term_id']) )
					$this->processed_terms[intval($tag['term_id'])] = $id['term_id'];
			} else {
				print '<div class="updated error">';
				printf( esc_html__( 'Failed to import post tag %s', 'detheme_demo' ), esc_html($tag['tag_name']) );
				if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
					echo ': ' . $id->get_error_message();
				echo '<br />';
				print '</div>';
				continue;
			}
		}

		unset( $this->tags );
	}

	function process_terms() {

		$this->terms = apply_filters( 'wp_import_terms', $this->terms );


		if ( empty( $this->terms ) )
			return;

		foreach ( $this->terms as $term ) {

			if(! $this->is_allowed_term($term)){
				continue;
			}
			// if the term already exists in the correct taxonomy leave it alone
			$id = term_exists( $term['slug'], $term['term_taxonomy'] );
			if ( $id ) {

				if ( is_array($id) ) $term_id = $id['term_id'];
				if ( isset($term['term_id']) )
					$this->processed_terms[intval($term['term_id'])] = (int) $term_id;
					$this->assign_navigation_menu(wp_parse_args($id,$term));
					
				continue;
			}

			if ( empty( $term['term_parent'] ) ) {
				$parent = 0;
			} else {
				$parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
				if ( is_array( $parent ) ) $parent = $parent['term_id'];
			}
			$description = isset( $term['term_description'] ) ? $term['term_description'] : '';
			$termarr = array( 'slug' => $term['slug'], 'description' => $description, 'parent' => intval($parent) );

			$id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );

			
			if ( ! is_wp_error( $id ) ) {
				if ( isset($term['term_id']) )
					$this->processed_terms[intval($term['term_id'])] = $id['term_id'];
					$this->assign_navigation_menu(wp_parse_args($id,$term));
			} else {
				print '<div class="updated error">';
				printf( esc_html__( 'Failed to import %s %s', 'detheme_demo' ), esc_html($term['term_taxonomy']), esc_html($term['term_name']) );
				if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
					echo ': ' . $id->get_error_message();
				echo '<br />';
				print '</div>';
				continue;
			}
		}

		unset( $this->terms );
	}

	function process_posts() {
		$this->posts = apply_filters( 'wp_import_posts', $this->posts );

		foreach ( $this->posts as $post ) {
			$post = apply_filters( 'wp_import_post_data_raw', $post );

			if($this->skip_woocommerce && 'product'==$post['post_type']){
				continue;
			}

			if ( ! post_type_exists( $post['post_type'] ) ) {
				print '<div class="updated error">';
				printf( esc_html__( 'Failed to import &#8220;%s&#8221;: Invalid post type %s', 'detheme_demo' ),
					esc_html($post['post_title']), esc_html($post['post_type']) );
				echo '<br />';
				print '</div>';
				do_action( 'wp_import_post_exists', $post );
				continue;
			}

			if ( isset( $this->processed_posts[$post['post_id']] ) && ! empty( $post['post_id'] ) )
				continue;

			if ( in_array($post['status'], array('auto-draft','trash','draft')))
				continue;

			if ( 'nav_menu_item' == $post['post_type']) {
				$this->process_menu_item( $post );
				continue;
			}

			$post_type_object = get_post_type_object( $post['post_type'] );

			$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				print '<div class="updated error">';
				printf( esc_html__('%s &#8220;%s&#8221; already exists.', 'detheme_demo'), $post_type_object->labels->singular_name, esc_html($post['post_title']) );
				echo '<br />';
				print '</div>';
				$comment_post_ID = $post_id = $post_exists;

				$this->assign_home_page($post_id, $post['post_id'], $post);

			} else {
				$post_parent = (int) $post['post_parent'];
				if ( $post_parent ) {
					// if we already know the parent, map it to the new local ID
					if ( isset( $this->processed_posts[$post_parent] ) ) {
						$post_parent = $this->processed_posts[$post_parent];
					// otherwise record the parent for later
					} else {
						$this->post_orphans[intval($post['post_id'])] = $post_parent;
						$post_parent = 0;
					}
				}

				// map the post author
				$author = sanitize_user( $post['post_author'], true );
				if ( isset( $this->author_mapping[$author] ) )
					$author = $this->author_mapping[$author];
				else
					$author = (int) get_current_user_id();

				$postdata = array(
					'import_id' => $post['post_id'], 'post_author' => $author, 'post_date' => $post['post_date'],
					'post_date_gmt' => $post['post_date_gmt'], 'post_content' => $post['post_content'],
					'post_excerpt' => $post['post_excerpt'], 'post_title' => $post['post_title'],
					'post_status' => $post['status'], 'post_name' => $post['post_name'],
					'comment_status' => $post['comment_status'], 'ping_status' => $post['ping_status'],
					'guid' => $post['guid'], 'post_parent' => $post_parent, 'menu_order' => $post['menu_order'],
					'post_type' => $post['post_type'], 'post_password' => $post['post_password'],'is_home' => $post['is_home'],
					'is_front_page' => $post['is_front_page'],'is_shop' => $post['is_shop'],'is_cart' => $post['is_cart'],
					'is_checkout' => $post['is_checkout'],'is_myaccount' => $post['is_myaccount']
				);

				

				$original_post_ID = $post['post_id'];
				$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

				if ( 'attachment' == $postdata['post_type'] ) {

					$remote_url = ! empty($post['attachment_url']) ? $post['attachment_url'] : $post['guid'];

					// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
					// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
					$postdata['upload_date'] = $post['post_date'];
					if ( isset( $post['postmeta'] ) ) {
						foreach( $post['postmeta'] as $meta ) {
							if ( $meta['key'] == '_wp_attached_file' ) {
								if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) )
									$postdata['upload_date'] = $matches[0];
								break;
							}
						}
					}

					$comment_post_ID = $post_id = $this->process_attachment( $postdata, $remote_url );
				} else {
					$comment_post_ID = $post_id = wp_insert_post( $postdata, true );
					do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
				}

				if ( is_wp_error( $post_id ) ) {
					print '<div class="updated error">';
					printf( esc_html__( 'Failed to import %s &#8220;%s&#8221;', 'detheme_demo' ),
						$post_type_object->labels->singular_name, esc_html($post['post_title']) );
					if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
						echo ': ' . $post_id->get_error_message();
					echo '<br />';
					print '</div>';
					continue;
				}

				if ( $post['is_sticky'] == 1 )
					stick_post( $post_id );
			}

			// map pre-import ID to local ID
			$this->processed_posts[intval($post['post_id'])] = (int) $post_id;

			if ( ! isset( $post['terms'] ) )
				$post['terms'] = array();

			$post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

			// add categories, tags and other terms

			if ( ! empty( $post['terms'] ) ) {
				$terms_to_set = array();
				foreach ( $post['terms'] as $term ) {
					// back compat with WXR 1.0 map 'tag' to 'post_tag'
					$taxonomy = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
					$term_exists = term_exists( $term['slug'], $taxonomy );
					$term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;

					if ( ! $term_id ) {
						$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
						if ( ! is_wp_error( $t ) ) {
							$term_id = $t['term_id'];
							do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
						} else {
							print '<div class="updated error">';
							printf( esc_html__( 'Failed to import %s %s', 'detheme_demo' ), esc_html($taxonomy), esc_html($term['name']) );
	   

							
							if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
								echo ': ' . $t->get_error_message();
							echo '<br />';

							print '</div>';

							do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
							continue;
						}
					}

					$terms_to_set[$taxonomy][] = intval( $term_id );
				}


				foreach ( $terms_to_set as $tax => $ids ) {
					$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
					do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
				}
				unset( $post['terms'], $terms_to_set );
			}

			if ( ! isset( $post['comments'] ) )
				$post['comments'] = array();

			$post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

			// add/update comments
			if ( ! empty( $post['comments'] ) ) {
				$num_comments = 0;
				$inserted_comments = array();
				foreach ( $post['comments'] as $comment ) {
					$comment_id	= $comment['comment_id'];
					$newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
					$newcomments[$comment_id]['comment_author']       = $comment['comment_author'];
					$newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
					$newcomments[$comment_id]['comment_author_IP']    = $comment['comment_author_IP'];
					$newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
					$newcomments[$comment_id]['comment_date']         = $comment['comment_date'];
					$newcomments[$comment_id]['comment_date_gmt']     = $comment['comment_date_gmt'];
					$newcomments[$comment_id]['comment_content']      = $comment['comment_content'];
					$newcomments[$comment_id]['comment_approved']     = $comment['comment_approved'];
					$newcomments[$comment_id]['comment_type']         = $comment['comment_type'];
					$newcomments[$comment_id]['comment_parent'] 	  = $comment['comment_parent'];
					$newcomments[$comment_id]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
					if ( isset( $this->processed_authors[$comment['comment_user_id']] ) )
						$newcomments[$comment_id]['user_id'] = $this->processed_authors[$comment['comment_user_id']];
				}
				ksort( $newcomments );

				foreach ( $newcomments as $key => $comment ) {
					// if this is a new post we can skip the comment_exists() check
					if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
						if ( isset( $inserted_comments[$comment['comment_parent']] ) )
							$comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
						$comment = wp_filter_comment( $comment );
						$inserted_comments[$key] = wp_insert_comment( $comment );
						do_action( 'wp_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $post );

						foreach( $comment['commentmeta'] as $meta ) {
							$value = maybe_unserialize( $meta['value'] );
							add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
						}

						$num_comments++;
					}
				}
				unset( $newcomments, $inserted_comments, $post['comments'] );
			}

			if ( ! isset( $post['postmeta'] ) )
				$post['postmeta'] = array();

			$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

			// add/update post meta
			if ( ! empty( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					$key = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
					$value = false;

					if ( '_edit_last' == $key ) {
						if ( isset( $this->processed_authors[intval($meta['value'])] ) )
							$value = $this->processed_authors[intval($meta['value'])];
						else
							$key = false;
					}

					if ( $key ) {
						// export gets meta straight from the DB so could have a serialized string
						if ( ! $value )
							$value = maybe_unserialize( $meta['value'] );

						update_post_meta( $post_id, $key, $value );
						do_action( 'import_post_meta', $post_id, $key, $value );

						// if the post has a featured image, take note of this in case of remap
						if ( '_thumbnail_id' == $key )
							$this->featured_images[$post_id] = (int) $value;
					}
				}
			}
		}

		unset( $this->posts );
	}

	function process_menu_item( $item ) {
		// skip draft, orphaned menu items

		if($this->skip_menu)
			return;
		
		if ( 'draft' == $item['status'] )
			return;

		$menu_slug = false;
		if ( isset($item['terms']) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' == $term['domain'] ) {
					$menu_slug = $term['slug'];
					break;
				}
			}
		}

		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			esc_html_e( 'Menu item skipped due to missing menu slug', 'detheme_demo' );
			echo '<br />';
			return;
		}

		$menu_id = term_exists( $menu_slug, 'nav_menu' );
		if ( ! $menu_id ) {
			printf( esc_html__( 'Menu item skipped due to invalid menu slug: %s', 'detheme_demo' ), esc_html( $menu_slug ) );
			echo '<br />';
			return;
		} else {
			$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
		}

		foreach ( $item['postmeta'] as $meta )
			$$meta['key'] = $meta['value'];

		if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $this->processed_terms[intval($_menu_item_object_id)];
		} else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $this->processed_posts[intval($_menu_item_object_id)];
		} else if ( 'custom' != $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			$this->missing_menu_items[] = $item;
			return;
		}

		if ( isset( $this->processed_menu_items[intval($_menu_item_menu_item_parent)] ) ) {
			$_menu_item_menu_item_parent = $this->processed_menu_items[intval($_menu_item_menu_item_parent)];
		} else if ( $_menu_item_menu_item_parent ) {
			$this->menu_item_orphans[intval($item['post_id'])] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent = 0;
		}

		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
		if ( is_array( $_menu_item_classes ) )
			$_menu_item_classes = implode( ' ', $_menu_item_classes );

		$args = array(
			'menu-item-object-id' => $_menu_item_object_id,
			'menu-item-object' => $_menu_item_object,
			'menu-item-parent-id' => $_menu_item_menu_item_parent,
			'menu-item-position' => intval( $item['menu_order'] ),
			'menu-item-type' => $_menu_item_type,
			'menu-item-title' => $item['post_title'],
			'menu-item-url' => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title' => $item['post_excerpt'],
			'menu-item-target' => $_menu_item_target,
			'menu-item-classes' => $_menu_item_classes,
			'menu-item-xfn' => $_menu_item_xfn,
			'menu-item-status' => $item['status']
		);

		if(isset($_menu_item_dt_megamenu)){
			$args['menu_item_dt_megamenu']=$_menu_item_dt_megamenu;
		}

		if(isset($_menu_item_dt_megamenu_width_options)){
			$args['menu_item_dt_megamenu_width_options']=$_menu_item_dt_megamenu_width_options;
		}

		if(isset($_menu_item_dt_megamenu_width)){
			$args['menu_item_dt_megamenu_width']=$_menu_item_dt_megamenu_width;
		}

		if(isset($_menu_item_dt_megamenu_type)){
			$args['menu_item_dt_megamenu_type']=$_menu_item_dt_megamenu_type;
		}

		if(isset($_menu_item_dt_megamenu_logo)){
			$args['menu_item_dt_megamenu_logo']=$_menu_item_dt_megamenu_logo;
		}

		if(isset($_menu_item_dt_megamenu_background_url)){
			$args['menu_item_dt_megamenu_background_url']=$_menu_item_dt_megamenu_background_url;
		}

		if(isset($_menu_item_dt_megamenu_background_repeat)){
			$args['menu_item_dt_megamenu_background_repeat']=$_menu_item_dt_megamenu_background_repeat;
		}


		add_action( 'wp_update_nav_menu_item', array($this,'proccess_post_custom_meta' ),999,3);

		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( $id && ! is_wp_error( $id ) )
			$this->processed_menu_items[intval($item['post_id'])] = (int) $id;
	}


	function proccess_post_custom_meta($menu_id, $menu_item_db_id, $args){

		if(isset($args['menu_item_dt_megamenu'])){
				update_post_meta( $menu_item_db_id, '_menu_item_dt_megamenu', sanitize_key($args['menu_item_dt_megamenu']) );
		}

		if(isset($args['menu_item_dt_megamenu_width_options'])){
				update_post_meta( $menu_item_db_id, '_menu_item_dt_megamenu_width_options', $args['menu_item_dt_megamenu_width_options']);
		}

		if(isset($args['menu_item_dt_megamenu_width'])){
				update_post_meta( $menu_item_db_id, '_menu_item_dt_megamenu_width', sanitize_key($args['menu_item_dt_megamenu_width']) );
		}

		if(isset($args['menu_item_dt_megamenu_type'])){
				update_post_meta( $menu_item_db_id, '_menu_item_dt_megamenu_type', sanitize_key($args['menu_item_dt_megamenu_type']) );
		}

		if(isset($args['menu_item_dt_megamenu_logo'])){
				update_post_meta( $menu_item_db_id, '_menu_item_dt_megamenu_logo', sanitize_key($args['menu_item_dt_megamenu_logo']) );
		}

		if(isset($args['menu_item_dt_megamenu_background_url'])){
				update_post_meta( $menu_item_db_id, '_menu_item_dt_megamenu_background_url', esc_url($args['menu_item_dt_megamenu_background_url']) );
		}

		if(isset($args['menu_item_dt_megamenu_background_repeat'])){
				update_post_meta( $menu_item_db_id, '_menu_item_dt_megamenu_background_repeat', $args['menu_item_dt_megamenu_background_repeat']);
		}
	}

	function is_allowed_term($term){

		if($this->skip_woocommerce && in_array($term['term_taxonomy'],array('product_cat','product_type')))
			return false;
		return $term;
	}

	function process_attachment( $post, $url ) {

		if($this->skip_attachment)
			return $post['import_id'];

		// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
		if ( preg_match( '|^/[\w\W]+$|', $url ) )
			$url = rtrim( $this->base_url, '/' ) . $url;

		$upload = $this->fetch_remote_file( $url, $post );

		if ( is_wp_error( $upload ) )
			return $upload;

		if ( $info = wp_check_filetype( $upload['file'] ) )
			$post['post_mime_type'] = $info['type'];
		else
			return new WP_Error( 'attachment_processing_error', esc_html__('Invalid file type', 'detheme_demo') );

		$post['guid'] = $upload['url'];

		// as per wp-admin/includes/upload.php
		$post_id = wp_insert_attachment( $post, $upload['file'] );
		wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

		// remap resized image URLs, works by stripping the extension and remapping the URL stub.
		if ( preg_match( '!^image/!', $info['type'] ) ) {
			$parts = pathinfo( $url );
			$name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

			$parts_new = pathinfo( $upload['url'] );
			$name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

			$this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
		}

		return $post_id;
	}


	function fetch_remote_file( $url, $post ) {
		// extract the file name and extension from the url

		$file_name = basename( $url );
		$wp_filesystem=$this->wp_filesystem;

		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
		if ( $upload['error'] )
			return new WP_Error( 'upload_dir_error', $upload['error'] );

		// fetch the remote url and write it to the placeholder file

		if(version_compare($GLOBALS['wp_version'], '4.4.0', '>=')){
			$uploader= new WP_Http();
			$remote_file=$uploader->get($url);

			if($wp_filesystem->put_contents( $upload['file'], wp_remote_retrieve_body( $remote_file ))){
				$headers=wp_remote_retrieve_headers( $remote_file );
				$headers['response'] = '200';
			}
			else{
				$headers=null;
			}
		}
		else{
			$headers = wp_get_http( $url, $upload['file']);
		}



		if ( ! $headers ) {
			$wp_filesystem->delete($upload['file']);
			return new WP_Error( 'import_file_error', $url." :".esc_html__('Remote server did not respond', 'detheme_demo') );
		}

		if ( $headers['response'] != '200' ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', sprintf( esc_html__('Remote server returned error response %1$d %2$s', 'detheme_demo'), esc_html($headers['response']), get_status_header_desc($headers['response']) ) );
		}

		$filesize = $wp_filesystem->size( $upload['file'] );

		if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
			$wp_filesystem->delete($upload['file']);
			return new WP_Error( 'import_file_error', esc_html__('Remote file is incorrect size', 'detheme_demo') );
		}

		if ( 0 == $filesize ) {
			$wp_filesystem->delete($upload['file']);
			return new WP_Error( 'import_file_error', esc_html__('Zero size file downloaded', 'detheme_demo') );
		}

		$max_size = (int) $this->max_attachment_size();
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			$wp_filesystem->delete($upload['file']);
			return new WP_Error( 'import_file_error', sprintf(esc_html__('Remote file is too large, limit is %s', 'detheme_demo'), size_format($max_size) ) );
		}

		// keep track of the old and new urls so we can substitute them later
		$this->url_remap[$url] = $upload['url'];
		$this->url_remap[$post['guid']] = $upload['url']; // r13735, really needed?
		// keep track of the destination if the remote url is redirected somewhere else
		if ( isset($headers['x-final-location']) && $headers['x-final-location'] != $url )
			$this->url_remap[$headers['x-final-location']] = $upload['url'];

		return $upload;
	}

	function backfill_parents() {
		global $wpdb;

		// find parents for post orphans
		foreach ( $this->post_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = false;
			if ( isset( $this->processed_posts[$child_id] ) )
				$local_child_id = $this->processed_posts[$child_id];
			if ( isset( $this->processed_posts[$parent_id] ) )
				$local_parent_id = $this->processed_posts[$parent_id];

			if ( $local_child_id && $local_parent_id )
				$wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
		}

		// all other posts/terms are imported, retry menu items with missing associated object
		$missing_menu_items = $this->missing_menu_items;
		foreach ( $missing_menu_items as $item )
			$this->process_menu_item( $item );

		// find parents for menu item orphans
		foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = 0;
			if ( isset( $this->processed_menu_items[$child_id] ) )
				$local_child_id = $this->processed_menu_items[$child_id];
			if ( isset( $this->processed_menu_items[$parent_id] ) )
				$local_parent_id = $this->processed_menu_items[$parent_id];

			if ( $local_child_id && $local_parent_id )
				update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
		}
	}

	/**
	 * Use stored mapping information to update old attachment URLs
	 */
	function backfill_attachment_urls() {
		global $wpdb;
		// make sure we do the longest urls first, in case one is a substring of another
		uksort( $this->url_remap, array(&$this, 'cmpr_strlen') );

		foreach ( $this->url_remap as $from_url => $to_url ) {
			// remap urls in post_content
			$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url) );
			// remap enclosure urls
			$result = $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url) );
		}
	}

	/**
	 * Update _thumbnail_id meta to new, imported attachment IDs
	 */
	function remap_featured_images() {
		// cycle through posts that have a featured image
		foreach ( $this->featured_images as $post_id => $value ) {
			if ( isset( $this->processed_posts[$value] ) ) {
				$new_id = $this->processed_posts[$value];
				// only update if there's a difference
				if ( $new_id != $value )
					update_post_meta( $post_id, '_thumbnail_id', $new_id );
			}
		}
	}

	function is_allowed_import($option_name){

		if(empty($option_name)) return false;

		$option_accepted=apply_filters('import_option_accepted',array('permalink_structure'));

		return preg_match('/widget_/', $option_name) || in_array($option_name, $option_accepted);


	}


	function import_option_accepted($keys=array()){

		return array_merge($keys,array('sidebars_widgets'));
	}

	function assign_home_page($post_id, $original_post_ID, $postdata){

		if((bool)$postdata['is_home']){


			update_option('page_for_posts',$post_id);
			update_option('show_on_front','page');
		}

		if((bool)$postdata['is_front_page']){
			update_option('page_on_front',$post_id);
			update_option('show_on_front','page');
		}

		if((bool)$postdata['is_shop']){
			update_option('woocommerce_shop_page_id',$post_id);
		}

		if((bool)$postdata['is_cart']){
			update_option('woocommerce_cart_page_id',$post_id);
		}

		if((bool)$postdata['is_checkout']){
			update_option('woocommerce_checkout_page_id',$post_id);
		}

		if((bool)$postdata['is_myaccount']){
			update_option('woocommerce_myaccount_page_id',$post_id);
		}

	}

	function assign_navigation_menu($term){

		
		if($term['term_id'] && $term['term_menu_position']!=''){
			$oldmod=get_theme_mod('nav_menu_locations');
			if(is_array($oldmod)){
				$oldmod[$term['term_menu_position']]=$term['term_id'];
			}
			else{
				$oldmod=array($term['term_menu_position']=>$term['term_id']);
			}
			set_theme_mod('nav_menu_locations',$oldmod);
		}
	}


	function is_valid_meta_key( $key ) {
		// skip attachment metadata since we'll regenerate it from scratch
		// skip _edit_lock as not relevant for import
		if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) )
			return false;
		return $key;
	}

	function max_attachment_size() {
		return apply_filters( 'import_attachment_size_limit', 0 );
	}

	function register_submenu_page(){

		 if($packages=detheme_one_click_demo::get_packages()){
    		add_theme_page(esc_html__('Detheme One Click Demo Importer', 'detheme_demo'), esc_html__('Import Demo', 'detheme_demo'),'manage_options','detheme_demo_install', array($this,'demo_install_setting'));
    	}
    }

	function cmpr_strlen( $a, $b ) {
		return strlen($b) - strlen($a);
	}

    function demo_install_setting(){

   	$this->enqueue_scripts();

    $packages=detheme_one_click_demo::get_packages();

    $package="";

    ?>
    <div class="demo-install-wrap">
    <h1><?php esc_html_e('Detheme Demo Importer','detheme_demo');?></h1>
    <?php 

	if(isset($_POST['selected_package'])){
	    	$package=sanitize_text_field($_POST['selected_package']);
	}

	if($package!=''){
	   $result=$this->do_install($package);
	   ?>
	   <div class="updated success">
	   <?php esc_html_e('import demo content complete','detheme_demo');?>
		</div>
		<br/>

	   <a class="button-primary back-demo" href="<?php echo admin_url('themes.php?page=detheme_demo_install'); ?>"><?php esc_html_e( 'Return to Import Demo', 'detheme_demo' ); ?></a>
	
	   <?php

	}
	else{?>
    <h2><?php esc_html_e('WARNING:','detheme_demo');?></h2>
    <ol class="install-notice">
    	<li><?php esc_html_e('Recommended for fresh install only','detheme_demo');?></li>
    </ol>
<?php
	if (count($packages)):

		ksort($packages);


		$tabcontent=array();


		?>
    <form id="detheme-demo-form" action="<?php echo admin_url('themes.php?page=detheme_demo_install'); ?>" method="post">
    	<?php wp_nonce_field( 'demo-install-setting','demo_install_setting');?>
    	
    <ul class="demo-lists">
    	<?php 
    	foreach ($packages as $name => $package) { 
    		$tabcontent[$package['Category']][]='<li>
		<div class="package-screenshot">
			<img src="'.esc_url(((isset($package['Thumbnail']))? $package['Thumbnail']:"")).'" alt="">
		</div>
		<div class="package-description">'.$package['Description'].'</div>
		<div class="package-actions">
			<input type="radio" name="selected_package" value="'.$name.'" /> <span class="theme-name">'.$package['Name'].'</span>
		</div>
		</li>';
	   	?>
<?php
    	}

    	if(count($tabcontent)):?>

<ul class="nav nav-tabs" role="tablist" id="demo-content">
<?php 
$i=0;

foreach ($tabcontent as $tabname => $contents) {?>
<li role="presentation" class="<?php print ($i===0)?"active":""?>"><a href="#<?php print sanitize_key($tabname);?>" aria-controls="<?php print sanitize_key($tabname);?>" role="tab" data-toggle="tab"><?php print $tabname;?></a></li>
<?php 
$i++;

	}?>
</ul>
<div class="tab-content">
<?php 
$i=0;

foreach ($tabcontent as $tabname => $contents) {?>
  <div role="tabpanel" class="tab-pane<?php print ($i===0)?" active":""?>" id="<?php print sanitize_key($tabname);?>">
  	<?php print @implode('',$contents);?>
  </div>
<?php 
$i++;

}
?>
</div>
<?php
endif;
    ?>
    </ul>
    <h2><?php esc_html_e('Import Options','detheme_demo');?></h2>
    <ul class="demo-option">
    	<li><input type="checkbox" name="skip_content" value="1"> <?php esc_html_e( 'Skip content', 'detheme_demo' ); ?></li>
    	<li><input type="checkbox" name="skip_menu" value="1"> <?php esc_html_e( 'Skip menu', 'detheme_demo' ); ?></li>
    	<li><input type="checkbox" name="skip_theme_option" value="1"> <?php esc_html_e( 'Skip theme option', 'detheme_demo' ); ?></li>
    	<li><input type="checkbox" name="skip_sidebar" value="1"> <?php esc_html_e( 'Skip sidebar widget', 'detheme_demo' ); ?></li>
    	<li><input type="checkbox" name="skip_attachment" value="1"> <?php esc_html_e( 'Skip media', 'detheme_demo' ); ?></li>
<?php if (is_plugin_active('revslider/revslider.php')):?>
    	<li><input type="checkbox" name="skip_slide" value="1"> <?php esc_html_e( 'Skip revolution slider demo', 'detheme_demo' ); ?></li>
<?php endif;?>
<?php if (is_plugin_active('essential-grid/essential-grid.php')):?>
    	<li><input type="checkbox" name="skip_ess" value="1"> <?php esc_html_e( 'Skip Ess. Grid', 'detheme_demo' ); ?></li>
<?php endif;?>
<?php if (is_plugin_active('woocommerce/woocommerce.php')):?>
    	<li><input type="checkbox" name="skip_woocommerce" value="1"> <?php esc_html_e( 'Skip woocommerce demo', 'detheme_demo' ); ?></li>
<?php endif;?>
    </ul>
         <button type="submit" id="install-demo" class="button-primary"><?php esc_html_e( 'Install Demo', 'detheme_demo' ); ?></button>
    </form>
<?php else:?>

	<h2><?php esc_html_e('Demo package not found','detheme_demo');?></h2>
	<?php endif;
	}
?>
	</div>


<?php
    }

    static function get_package_info($file_info=''){

    	$wp_filesystem=new WP_Filesystem_Direct(array());

    	if($file_info=='' || ! $wp_filesystem->exists( $file_info))
    		return false;

    	$package=get_file_data($file_info,array('Name'=>'Theme Name','Description'=>'Description','Version'=>'Version','Category'=>'Category'),'');
    	return $package;
    }


    static function get_packages(){

    	$packages=array();
    	return apply_filters('get_detheme_packages',$packages);
    }


    function install_revo_slider($path){

    	$slides=$this->get_slides($path);

    	if(!count($slides)) return false;

    	foreach ($slides as $name => $slide) {
    		$this->import_slide($slide);
    	}

    	return true;

    }

    function get_ls_slides($path){

    	$packages=array();
    	$wp_filesystem=$this->wp_filesystem;

		  if($dirlist=$wp_filesystem->dirlist($path)){

			    foreach ($dirlist as $dirname => $dirattr) {

			       if($dirattr['type']=='f' && preg_match("/(\.json)$/", $dirname) ){

				       	$packages[$dirname]['name']=$dirname;
				       	$packages[$dirname]['path']=$path.$dirname;
			       	}

			    }
		  }
    	return $packages;
    }

    function get_slides($path){

    	$packages=array();
    	$wp_filesystem=$this->wp_filesystem;

		  if($dirlist=$wp_filesystem->dirlist($path)){

			    foreach ($dirlist as $dirname => $dirattr) {

			       if($dirattr['type']=='f' && preg_match("/(\.zip)$/", $dirname) ){

				       	$packages[$dirname]['name']=$dirname;
				       	$packages[$dirname]['path']=$path.$dirname;
			       	}

			    }
		  }
    	return $packages;
    }

    function import_slide( $slide ) {

    	$name=$slide['name'];
    	$path=$slide['path'];

		$slider = new RevSliderImporter();
		$response = $slider->importSliderFromFile($path);
		$sliderID = $response["sliderID"];


		if(!$response['success']){

			print '<div class="updated error">';
			printf( esc_html__( 'Failed to import slide %s: %s', 'detheme_demo' ), $name,$response['error']);
			print '</div>';

		}
    }

    function install_ess_grid($path){

    	if($path=='')
    		return false;

    	/*
		grids==>grids
		skins==>skins
		elements==>elements
		navigation-skins==>navigation_skins
		custom-meta==>custom_meta
		punch-fonts==>punch_fonts
		global-css==>global_styles
		*/

		
		$wp_filesystem=$this->wp_filesystem;
		$im = new Essential_Grid_Import();

		if($dirlist=$wp_filesystem->dirlist($path)){

				$return=false;

			    foreach ($dirlist as $dirname => $dirattr) {
			       if($dirattr['type']=='f' && preg_match("/(\.json)$/", $dirname) ){
			       	
			       		if($ess_grid_setting=$this->exctract_json($path.$dirname)){

			       			/* skins */
			       			if(isset($ess_grid_setting['skins']) && is_array($ess_grid_setting['skins'])){

			       				$skins=array();

			       				foreach ($ess_grid_setting['skins'] as $skin) {
			       					$skins[$skin['id']]=$skin;
			       				}

			       				$im->import_skins($skins,array_keys($skins));
			       				$return=true;
			       			}

			       			/* grids */
			       			if(isset($ess_grid_setting['grids']) && is_array($ess_grid_setting['grids'])){

			       				$grids=$overwrite_data=array();

			       				foreach ($ess_grid_setting['grids'] as $grid) {

			       					$grids[$grid['id']]=$grid;
			       				}
			       				$im->import_grids($grids,array_keys($grids));
			       				$return=true;
			       			}

			       			/* elements */
			       			if(isset($ess_grid_setting['elements']) && is_array($ess_grid_setting['elements'])){

			       				$elements=array();

			       				foreach ($ess_grid_setting['elements'] as $element) {

			       					$elements[$element['id']]=$element;
			       				}

			       				$im->import_elements($elements,array_keys($elements));
			       				$return=true;
			       			}

			       			/* navigation skins */
			       			if(isset($ess_grid_setting['navigation-skins']) && is_array($ess_grid_setting['navigation-skins']) && count($ess_grid_setting['navigation-skins'])){

			       				$navigations=array();

			       				foreach ($ess_grid_setting['navigation-skins'] as $navigation) {

			       					$navigations[$navigation['id']]=$navigation;
			       				}

			       				$im->import_navigation_skins($navigations,array_keys($navigations));
			       				$return=true;
			       			}

			       				
			       			/* custom meta */
			       			if(isset($ess_grid_setting['custom-meta']) && is_array($ess_grid_setting['custom-meta']) && count($ess_grid_setting['custom-meta'])){

			       				$custommetas=array();

			       				foreach ($ess_grid_setting['custom-meta'] as $custommeta) {

			       					$custommetas[$custommeta['handle']]=$custommeta;
			       				}


			       				$im->import_custom_meta($custommetas,array_keys($custommetas));
			       				$return=true;
			       			}

			       			/* custom meta */
			       			if(isset($ess_grid_setting['punch-fonts']) && is_array($ess_grid_setting['punch-fonts']) && count($ess_grid_setting['punch-fonts'])){

			       				$fonts=array();

			       				foreach ($ess_grid_setting['punch-fonts'] as $font) {

			       					$fonts[$font['handle']]=$font;
			       				}


			       				$im->import_punch_fonts($fonts,array_keys($fonts));
			       				$return=true;
			       			}

			       			/* custom css */

			       			if(isset($ess_grid_setting['global-css']) && $ess_grid_setting['global-css']!=''){

			       				$im->import_global_styles($ess_grid_setting['global-css']);
			       				$return=true;
			       			}

			       		}
			       	}

			    }

			    return $return;
		}

		return false;
    }



}

$detheme_demo =new detheme_one_click_demo();
$detheme_demo->init();
?>