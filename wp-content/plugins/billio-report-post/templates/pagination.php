<?php
/**
 * The Template for pagination.
 *
 * Override this template by copying it to yourtheme/billio-report-post/pagination.php
 *
 * @author 		DeTheme
 * @package 	billio-report-post/templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $dt_report_query;

if ( $dt_report_query->max_num_pages <= 1 ) {
	//return;
}

?>

<div class="dt_report_pagination" dir="ltr">
	<?php
		$pagination = paginate_links( apply_filters( 'dt_report_pagination_args', array(
			'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
			'format'       => '',
			'add_args'     => '',
			'current'      => max( 1, get_query_var( 'paged' ) ),
			'total'        => $dt_report_query->max_num_pages,
//			'prev_text'    => is_rtl() ? '&#9654;' : '&#9664;',
//			'next_text'    => is_rtl() ? '&#9664;' : '&#9654;',
			'prev_text'    => '<span></span>',
			'next_text'    => '<span></span>',
			'type'         => 'array',
			'end_size'     => 3,
			'mid_size'     => 3
		) ) );

	if(is_array($pagination)){
		print join("\n",is_rtl()?array_reverse($pagination):$pagination);
	}

	?>
</div>
