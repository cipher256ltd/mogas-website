<?php
/**
 * Shop breadcrumb
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 * @see         woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

function wc_billio_get_breadcrumbs_from_menu($post_id,&$breadcrumb,$iscurrent=true) {
   	$primary = get_nav_menu_locations();

	if (isset($primary['primary'])) {
    	$navs = wp_get_nav_menu_items($primary['primary']);
		foreach ($navs as $nav) {
			if (($nav->object_id)==$post_id) {
				if (!$iscurrent) {
					array_splice($breadcrumb, 1, 0, array(array($nav->title,$nav->url)));	
				} /*else {
					$breadcrumb[count($breadcrumb)-1][1]="";
				}*/

			  	if ($nav->menu_item_parent!=0) {
			    	//start recursive by menu parent
			    	wc_billio_get_breadcrumbs_from_menu_by_menuid($nav->menu_item_parent,$navs,$breadcrumb);
			  	}
			  	break;
			}
		} //foreach

    }
}

function wc_billio_get_breadcrumbs_from_menu_by_menuid($menu_id,$navs,&$breadcrumb) {
  	foreach ($navs as $nav) {
	    if (($nav->ID)==$menu_id) {
	    	array_splice($breadcrumb, 1, 0, array(array($nav->title,$nav->url)));

			if ($nav->menu_item_parent!=0) {
				//recursive by menu parent
				wc_billio_get_breadcrumbs_from_menu_by_menuid($nav->menu_item_parent,$navs,$breadcrumb);
			}
			break;
	    }
  	} 
}


if ( $breadcrumb ) {
	if (wc_get_page_id('shop')!=-1) {
		wc_billio_get_breadcrumbs_from_menu(wc_get_page_id('shop'),$breadcrumb,is_shop());
	} else if (is_cart()||is_checkout()) {
		$wc_permalinks = get_option('woocommerce_permalinks');
		$page = get_page_by_path($wc_permalinks['product_base']);
		if ($page) {
			wc_billio_get_breadcrumbs_from_menu($page->ID,$breadcrumb,is_shop());
		}
	} else {
		$post_type_data = get_post_type_object($post->post_type);
		$post_type_slug = $post_type_data->rewrite['slug'];
		$page = get_page_by_path($post_type_slug);

		if ($page) {
			wc_billio_get_breadcrumbs_from_menu($page->ID,$breadcrumb,is_shop());
		}
	}

	echo $wrap_before;

	if (is_rtl()) {
		$breadcrumb = array_reverse($breadcrumb);	
		//print_r($breadcrumb);
	}

	foreach ( $breadcrumb as $key => $crumb ) {
		
		if (is_rtl()) {	
			if (! empty( $crumb[1] ) && $key!==0) {
				echo $before;
				echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
			} else {
				echo $beforecurrent;
				echo esc_html( $crumb[0] );
			}
		} else { //is_rtl()
			if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
				echo $before;
				echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
			} else {
				echo $beforecurrent;
				echo esc_html( $crumb[0] );
			}
		}//is_rtl()

		echo $after;

		if ( sizeof( $breadcrumb ) !== $key + 1 ) {
			echo $delimiter;
		}

	}

	echo $wrap_after;

}