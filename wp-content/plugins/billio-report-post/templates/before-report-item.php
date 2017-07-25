<?php
/**
 * The Template for displaying before report item.
 *
 * Override this template by copying it to yourtheme/billio-report-post/before-report-item.php
 *
 * @author 		DeTheme
 * @package 	billio-report-post/templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $dt_report_query;

$i = is_int($dt_report_query->current_post) ? $dt_report_query->current_post : 0;
$j = $i % 2;
if ($j==0) {
?>
    <div class="row equal_height">
<?php
} //if ($j==0)
?>