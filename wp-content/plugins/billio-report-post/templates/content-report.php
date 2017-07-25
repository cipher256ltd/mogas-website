<?php
/**
 * The Template for displaying all content report.
 *
 * Override this template by copying it to yourtheme/billio-report-post/content-report.php
 *
 * @author 		DeTheme
 * @package 	billio-report-post/templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $dt_report_query;

$imageurl = "";
$alt_image = "";
$document_url       = "";
$document_extension = "";
$document_icon      = "";
$pre_title 			= ""; 
$button_label 		= "";

if (isset($dt_report_query->post->ID)) {
	/* Get Image from featured image */
	$thumb_id = get_post_thumbnail_id($dt_report_query->post->ID);
	$featured_image = wp_get_attachment_image_src($thumb_id,'full',false); 
	if (isset($featured_image[0])) {
		$imageurl = $featured_image[0];
	}

	$alt_image = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);

	$post_id = $dt_report_query->post->ID;
    
    $document_url       = get_post_meta($post_id,'dt_report_document_url',true);
    $document_extension = get_post_meta($post_id,'dt_report_document_extension',true);
    $document_icon      = get_post_meta($post_id,'dt_report_document_icon',true);
    $pre_title 			= get_post_meta($post_id,'dt_report_pre_title',true); 
    $button_label 		= get_post_meta($post_id, 'dt_report_button_label', true );
}

$colsm = '';

?>

<?php do_action('dt_report_before_report_item',$dt_report_query); ?>

<div id="report-<?php the_ID(); ?>" <?php post_class('col-xs-12 col-sm-6 equal_height_item'); ?>>
	<div class="row dt_report_item"> 
<?php	if ($imageurl!="") { 
			$colsm = 'col-sm-6';
?>
		<div class="col-xs-12 col-sm-6">
			<img class="img-responsive" alt="<?php echo esc_attr($alt_image); ?>" src="<?php echo $imageurl; ?>">
		</div>

<?php 	} //if ($imageurl!="")?>
		
		<div class="col-xs-12 <?php echo sanitize_html_class($colsm); ?>">
			<h3 class="dt_report_pre_title"><?php echo $pre_title; ?></h3>
			<h2 class="dt_report_title"><?php the_title(); ?></h2>
			<div class="dt_report_content"><?php the_content(); ?></div>
<?php if (!empty($document_url)) { ?>
			<div class="dt_report_button">
				<a href="<?php echo esc_url($document_url); ?>" target="_blank"><i class="<?php echo sanitize_html_class($document_icon); ?>"></i><?php echo $button_label . ' ' . $document_extension; ?></a>
			</div>
<?php } ?>
		</div>
	</div>
</div>


<?php do_action('dt_report_after_report_item',$dt_report_query); ?>