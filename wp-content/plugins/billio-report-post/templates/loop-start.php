<?php
/**
 * The Template for Loop Start.
 *
 * Override this template by copying it to yourtheme/billio-report-post/loop-start.php
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

<?php do_action('dt_report_before_loop_start'); ?>

<div class="row">
