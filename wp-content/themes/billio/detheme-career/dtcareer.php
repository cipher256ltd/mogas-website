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
 * @subpackage Detheme Simple Career
 * @since Detheme Simple Career 1.0
 */
global $detheme_config,$wp_query,$dt_revealData;
get_header(); 

the_post();

locate_template('pagetemplates/scrollingsidebar.php',true);

$sidebar_position=get_billio_sidebar_position();
$sidebar=is_active_sidebar( 'detheme-sidebar' )?'detheme-sidebar':false;


if(!$sidebar){
	$sidebar_position = "nosidebar";
}


set_query_var('sidebar',$sidebar);

$class_sidebar = $sidebar_position;

$class_vertical_menu = '';
if ($detheme_config['dt-header-type']=='leftbar') {
	$class_vertical_menu = ' vertical_menu_container';
}

?>


<div  <?php post_class('blog single-post content '.$class_sidebar.$class_vertical_menu); ?>>
<div class="container">
		<div class="row">
	<?php if ($sidebar_position=='nosidebar') { ?>
			<div class="col-xs-12">
<?php	} else { ?>
			<div class="col-xs-12 col-sm-8 <?php print ($sidebar_position=='sidebar-left')?" col-sm-push-4":"";?> col-md-9 <?php print ($sidebar_position=='sidebar-left')?" col-md-push-3":"";?>">
<?php	} 

$attachlimit =(isset($detheme_config['career_attach_limit']) && ''!=$detheme_config['career_attach_limit'])? $detheme_config['career_attach_limit']:1024;
$attachlimit=$attachlimit/1024;

$career_attach_type=isset($detheme_config['career_attach_type'])?$detheme_config['career_attach_type']:false;
$attachmenttype=array();
if($career_attach_type){

	$fileoptions=array('image'=>".jpg, .jpeg, .png, .gif",'zip'=>".zip",'rtf'=> '.rtf','pdf'=> '.pdf','text'=> ".txt",'html'=> ".html",'htm'=> ".htm",'msword'=> '.doc','openxmlformats'=> ".docx");
	foreach (array_keys($career_attach_type) as $key) {
		$attachmenttype[$key]=$fileoptions[$key];
	}

}

$headText=isset($detheme_config['career-apply-head-text'])?$detheme_config['career-apply-head-text']:"";
$headText=preg_replace(array('/\{job_title\}/','/\{job_link\}/'), array(get_the_title(),get_the_permalink()), $headText);
?>
<div id="career_apply_<?php print get_the_ID();?>" class="career-form modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	    <div class="modal-content">
	    	<div class="md-description">
	    		<div class="heading-career-form"><?php print $headText;?></div>
	    		<h2 class="title-career-form"><?php _e('Apply Now','billio');?></h2>
	    		<form id="career-form" method="post" action="" enctype="multipart/form-data">
				  <div class="form-group">
				    <label for="fullname"><?php _e('Full Name','billio');?>:</label>
				    <input type="text" name="fullname" class="form-control" id="fullname" placeholder="<?php _e('e.g. John Smith','billio');?>" required>
				  </div>
				  <div class="form-group">
				    <label for="email_address"><?php _e('Email','billio');?>:</label>
				    <input type="email" name="email_address" class="form-control" id="email_address" placeholder="<?php _e('e.g. john.smith@hotmail.com','billio');?>" required>
				  </div>
				  <div class="form-group">
				    <label for="note"><?php _e('Covering Note','billio');?>:</label>
				    <textarea class="form-control" name="note" rows="5" required></textarea>
				  </div>
				  <div class="form-group">
				    <label for="file_cv"><?php _e('Upload CV','billio');?>:</label>
				    <input type="file" name="file_cv" id="file_cv" required>
				    <p class="help-block"><?php printf(__('Maximum file size %.2fMb','billio'),$attachlimit);?></p>
				  </div>
				  <button type="submit" class="btn btn-color-secondary"><?php _e('Apply Now','billio');?></button>
				  <input type="hidden" name="career_id" id="career_id" value="<?php the_ID();?>"/>
				</form>
	    	</div>
		    <button class="button right btn-cross md-close" data-dismiss="modal"><i class="icon-cancel"></i></button>
	     </div>
 	</div>
</div>
<?php 
$headText=isset($detheme_config['career-emailfriend-head-text'])?$detheme_config['career-emailfriend-head-text']:"";
$headText=preg_replace(array('/\{job_title\}/','/\{job_link\}/'), array(get_the_title(),get_the_permalink()), $headText);

?>
<div id="career_send_<?php print get_the_ID();?>" class="career-form modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	    <div class="modal-content">
	    	<div class="md-description">
	    		<div class="heading-career-form"><?php print $headText;?></div>
	    		<h2 class="title-career-form"><?php _e('Email To a Friend','billio');?></h2>
	    		<form id="career-send-form" method="post" action="">
				  <div class="form-group">
				    <label for="fullname"><?php _e('Full Name','billio');?>:</label>
				    <input type="text" name="fullname" class="form-control" id="fullname" placeholder="<?php _e('e.g. John Smith','billio');?>" required>
				  </div>
				  <div class="form-group">
				    <label for="email_address"><?php _e('Email','billio');?>:</label>
				    <input type="email" name="email_address" class="form-control" id="email_address" placeholder="<?php _e('e.g. john.smith@hotmail.com','billio');?>" required>
				  </div>
				  <div class="form-group">
				    <label for="friend_email_address"><?php _e('Friend Email','billio');?>:</label>
				    <input type="email" name="friend_email_address" class="form-control" id="friend_email_address" placeholder="<?php _e('e.g. alice@hotmail.com','billio');?>" required>
				  </div>
				  <div class="form-group">
				    <label for="note"><?php _e('Quick Note','billio');?>:</label>
				    <textarea class="form-control" name="note" rows="5"></textarea>
				    <p class="help-block"><?php _e('Type a quick message directed to your friend. Please avoid content that could be considered as spam as this could result in a ban from the site.','billio');?></p>
				  </div>
				  <button type="submit" class="btn btn-color-secondary"><?php _e('Send Message','billio');?></button>
				  <input type="hidden" name="career_id" id="career_id" value="<?php the_ID();?>"/>
				</form>
	    	</div>
		    <button class="button md-close right btn-cross" data-dismiss="modal"><i class="icon-cancel"></i></button>
	     </div>
	  </div>
</div>
<div class="career-detail">
	<h1><?php _e('Jobs Description','billio');?></h1>
	<div class="row">
		<div class="col-sm-8<?php print is_rtl()?" col-sm-push-4":"";?>">
			<div class="row">
				<div class="col-xs-12">
					<ul class="career-detail-list">
							<li class="career-field"><label for="job-position"><?php _e('Job Position','billio');?></label><span class="career-value"><?php print get_the_title();?></span></li>
					<?php 
					foreach (get_dtcareer_jobs_value() as $key => $field) {?>
							<li class="career-field"><label for="<?php print $key;?>"><?php print function_exists('icl_t') ? icl_t('billio', $field['label'], $field['label']):$field['label'];?></label><span class="career-value"><?php print $field['value'];?></span></li>
						<?php
					}
					?>
							<li class="career-field"><label for="posted-date"><?php _e('Posted','billio');?></label><span class="career-value"><?php print get_the_date();?></span></li>
							<li class="career-field"><label for="start-date"><?php _e('Start Date','billio');?></label><span class="career-value"><?php print get_career_start_date();?></span></li>
							<li class="career-field"><label for="end-date"><?php _e('End Date','billio');?></label><span class="career-value"><?php print get_career_close_date();?></span></li>
					</ul>
				</div>
			</div>
			<div class="row">
				<?php if(is_rtl()):?>
				<div class="col-xs-12 col-sm-4 text-left blog_info_share">
					<?php locate_template('pagetemplates/social-share.php',true,false); ?>
				</div>
				<div class="col-xs-12 col-sm-8 career-action-button" ><a class="btn btn-color-secondary" data-toggle="modal" id="apply-career" data-target="#career_apply_<?php print get_the_ID();?>" href="javascript:;"><?php _e('Apply Now','billio');?></a> <a class="btn btn-color-secondary" id="send-career-to-friend"  data-toggle="modal"  data-target="#career_send_<?php print get_the_ID();?>" href="javascript:;"><?php _e('Email To a Friend','billio');?></a></div>
				<?php else:?>
				<div class="col-xs-12 col-sm-8 career-action-button" ><a class="btn btn-color-secondary" data-toggle="modal" id="apply-career" data-target="#career_apply_<?php print get_the_ID();?>" href="javascript:;"><?php _e('Apply Now','billio');?></a> <a class="btn btn-color-secondary" id="send-career-to-friend"  data-toggle="modal"  data-target="#career_send_<?php print get_the_ID();?>" href="javascript:;"><?php _e('Email To a Friend','billio');?></a></div>
				<div class="col-xs-12 col-sm-4 text-right blog_info_share">
					<?php locate_template('pagetemplates/social-share.php',true,false); ?>
				</div>
				<?php endif;?>
			</div>
		</div>
		<div class="col-sm-4<?php print is_rtl()?" col-sm-pull-8":"";?>"><?php the_content();?></div>
	</div>
</div>



</div><!-- content area col-9 -->

<?php if ('sidebar-right'==$sidebar_position) { ?>
			<div class="col-xs-12 col-sm-4 col-md-3 sidebar">
				<?php get_sidebar(); ?>
			</div>
<?php }
	elseif ($sidebar_position=='sidebar-left') { ?>
			<div class="col-xs-12 col-sm-4 col-md-3 sidebar col-sm-pull-8 col-md-pull-9">
				<?php get_sidebar(); ?>
			</div>
<?php }?>
	

		</div><!-- .row -->

	</div><!-- .container -->

</div>
<?php
get_footer();
?>