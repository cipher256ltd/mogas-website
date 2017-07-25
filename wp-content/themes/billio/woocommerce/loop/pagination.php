<?php
/**
 * Pagination - Show numbered pagination for catalog pages.
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) {
	return;
}
?>
<nav class="woocommerce-pagination">
	<?php

	$pagination= paginate_links( apply_filters( 'woocommerce_pagination_args', array(
			'base'         => esc_url( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
			'format'       => '',
			'current'      => max( 1, get_query_var( 'paged' ) ),
			'total'        => $wp_query->max_num_pages,
			'prev_text'=>'<span></span>',
			'next_text'=>'<span></span>',
			'type'         => 'array',
			'end_size'     => 3,
			'mid_size'     => 3
		) ) );


	if(is_array($pagination)){
		print "<ul class=\"page-numbers\"><li>";
		print join("</li>\n<li>",is_rtl()?array_reverse($pagination):$pagination);
		print "</li></ul>";
	}
	?>
</nav>
