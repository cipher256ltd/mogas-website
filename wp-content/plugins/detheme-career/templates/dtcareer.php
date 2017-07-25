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
function render_dtcareer_jobs_field($content){

	$jobdesc=apply_filters('dtcareer_jobs_render_title',"<h2>".__('Job Description','detheme_career')."</h2>");
	$jobdesc.=apply_filters('dtcareer_jobs_render_before_field',"");

	foreach (get_dtcareer_jobs_value() as $key => $value) {

		$outfield=apply_filters('dtcareer_jobs_render_field',"",$value);

		if($outfield!=''){

			$jobdesc.=$outfield;

		}
		else{

			$jobdesc.="<div class=\"career-field\"><label for=\"".$key."\">".$value['label']."</label>: ".$value['value']."</div>";
		}
	}

	$jobdesc.=apply_filters('dtcareer_jobs_render_after_field',"");

	return $content.$jobdesc;

}

add_filter('the_content','render_dtcareer_jobs_field');
locate_template( 'index.php', true );
?>