<?php
/**
 * The Template for displaying after report item.
 *
 * Override this template by copying it to yourtheme/billio-report-post/after-report-item.php
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
$count = is_int($dt_report_query->post_count) ? $dt_report_query->post_count : 0;
$j = $i % 2;
if (($j!=0)||($count==($i+1))) {
?>
    </div>
<?php
} //if (($j!=0)||($count==($i+1)))
?>