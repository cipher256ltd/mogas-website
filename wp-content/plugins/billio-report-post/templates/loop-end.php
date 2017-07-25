<?php
/**
 * The Template for Loop End.
 *
 * Override this template by copying it to yourtheme/billio-report-post/loop-end.php
 *
 * @author 		DeTheme
 * @package 	billio-report-post/templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $dt_report_query;

?>

</div><!--div class="row"-->

<?php //print_r($dt_report_query); ?>

<?php do_action('dt_report_after_loop_end'); ?>

<?php do_action('dt_report_load_pagination'); ?>