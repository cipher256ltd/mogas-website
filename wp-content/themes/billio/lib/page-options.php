<?php 
defined('ABSPATH') or die();

global $detheme_config;
/* title */

$detheme_config['body_background']=$detheme_config['body_tag']="";
$post_id=get_the_id();

if(is_single() && !in_array($post_type=get_post_type(),billio_post_use_sidebar_layout()) && isset($detheme_config['layout_'.$post_type])){
	$detheme_config['layout']=$detheme_config['layout_'.$post_type];
}

if(is_single()){
	billio_set_post_views(get_the_ID()); 
}

$sidebars=wp_get_sidebars_widgets();
if(!isset($sidebars['detheme-scrolling-sidebar']) || !count($sidebars['detheme-scrolling-sidebar'])){
    $detheme_config['dt_scrollingsidebar_on']=false;
}

if(is_front_page()){

	$post_id = get_option('page_on_front');

	if(function_exists('is_shop') && is_shop()){
		 $post_id=get_option( 'woocommerce_shop_page_id');
	}

	if($post_id){

		 $detheme_config['page-title']=get_the_title($post_id);
		
		if(get_post_meta( $post_id,'_hide_loader', true )){
			$detheme_config['page_loader']=false;
		}

		$detheme_config['show-banner-area']=$detheme_config['show-banner-area'] && $detheme_config['show-banner-homepage'] && !get_post_meta( $post_id,'_hide_banner', true );

		if($hide_title=get_post_meta( $post_id, '_hide_title', true )){
			$detheme_config['dt-show-title-page']=false;
		}

		$page_background=get_post_meta( $post_id,'_page_background', true );
		$background_style=get_post_meta( $post_id, '_background_style', true );

		if($page_background){

			$style=omnpress_getBackgroundStyle($page_background,$background_style);

			$detheme_config['body_background']="body{".$style['css']."}";
			$detheme_config['body_tag']=($background_style!='default')?$style['body']:
			($detheme_config['body_background_style']=='parallax'|| $detheme_config['body_background_style']=='parallax_all')?" data-speed=\"3\" data-type=\"background\" ":"";

		}

	}
	else{
		 $detheme_config['page-title']=get_bloginfo('name');
		 $detheme_config['show-banner-area']=$detheme_config['show-banner-area']  && $detheme_config['show-banner-homepage'];
	}



}
elseif(is_page()){

	$detheme_config['page-title']=the_title('','',false);

	if(get_post_meta( $post_id,'_hide_loader', true )){
		$detheme_config['page_loader']=false;
	}

	if(get_post_meta( $post_id,'_hide_banner', true )){
		$detheme_config['show-banner-area']=false;
	}

	if($hide_title=get_post_meta( $post_id, '_hide_title', true )){
		$detheme_config['dt-show-title-page']=false;
	}

	$page_background=get_post_meta( $post_id,'_page_background', true );
	$background_style=get_post_meta( $post_id, '_background_style', true );


	if($page_background){

		$style=omnpress_getBackgroundStyle($page_background,$background_style);
		$detheme_config['body_background']="body{".$style['css']."}";
		$detheme_config['body_tag']=($background_style!='default')?$style['body']:
		($detheme_config['body_background_style']=='parallax'|| $detheme_config['body_background_style']=='parallax_all')?" data-speed=\"3\" data-type=\"background\" ":"";

	}

}
elseif(is_category()){


	//$detheme_config['page-title']=sprintf(__('Category : %s','billio'), single_cat_title( ' ', false ));
	$detheme_config['page-title']=sprintf(__('%s','billio'), single_cat_title( ' ', false ));

}
elseif(is_archive()){

	if(is_tag()){
		$title=sprintf(__('Tag : %s','billio'), single_tag_title( ' ', false ));
	}
	elseif(is_tax()){
		$title=single_tag_title( ' ', false );
	}
	elseif(function_exists('is_shop') && is_shop()){

		if (!empty($detheme_config['dt-shop-title-page'])) {
			$title = $detheme_config['dt-shop-title-page'];
		} else {
			$title=woocommerce_page_title(false);	
		}

		$post_id=get_option( 'woocommerce_shop_page_id');

		if(get_post_meta( $post_id,'_hide_loader', true )){
			$detheme_config['page_loader']=false;
		}

		if(get_post_meta( $post_id,'_hide_banner', true )){
			$detheme_config['show-banner-area']=false;
		}

		if($hide_title=get_post_meta( $post_id, '_hide_title', true )){
			$detheme_config['dt-show-title-page']=false;
			add_filter('woocommerce_show_page_title',create_function('','return false;'));
		}

		$page_background=get_post_meta( $post_id,'_page_background', true );
		$background_style=get_post_meta( $post_id, '_background_style', true );

		if($page_background){

			$style=omnpress_getBackgroundStyle($page_background,$background_style);
			$detheme_config['body_background']="body{".$style['css']."}";
			$detheme_config['body_tag']=($background_style!='default')?$style['body']:
			($detheme_config['body_background_style']=='parallax'|| $detheme_config['body_background_style']=='parallax_all')?" data-speed=\"3\" data-type=\"background\" ":"";

		}

	}	
	else{
		$title=sprintf((is_rtl()?__('%s : Archive','billio'):__('Archive : %s','billio')), single_month_title( ' ', false ));

	}

	$detheme_config['page-title']=$title;

}
elseif(is_search()){
		$detheme_config['page-title']=__('Search','billio');
}
elseif(is_home()){

	 $post_id=get_option( 'page_for_posts');
	 $title=get_the_title($post_id);
	 $detheme_config['page-title']=$title;

	if(get_post_meta( $post_id,'_hide_loader', true )){
		$detheme_config['page_loader']=false;
	}

	if(get_post_meta( $post_id,'_hide_banner', true )){
		$detheme_config['show-banner-area']=false;
	}

	if($hide_title=get_post_meta( $post_id, '_hide_title', true )){
		$detheme_config['dt-show-title-page']=false;
	}

	$page_background=get_post_meta( $post_id,'_page_background', true );
	$background_style=get_post_meta( $post_id, '_background_style', true );

	if($page_background){

		$style=omnpress_getBackgroundStyle($page_background,$background_style);
		$detheme_config['body_background']="body{".$style['css']."}";
		$detheme_config['body_tag']=($background_style!='default')?$style['body']:
		($detheme_config['body_background_style']=='parallax'|| $detheme_config['body_background_style']=='parallax_all')?" data-speed=\"3\" data-type=\"background\" ":"";

	}
}
elseif(is_404()){
	$page_background=get_post_meta( $post_id,'_page_background', true );
	$background_style=get_post_meta( $post_id, '_background_style', true );

	if($page_background){

		$style=omnpress_getBackgroundStyle($page_background,$background_style);
		$detheme_config['body_background']="body{".$style['css']."}";
		$detheme_config['body_tag']=($background_style!='default')?$style['body']:
		($detheme_config['body_background_style']=='parallax'|| $detheme_config['body_background_style']=='parallax_all')?" data-speed=\"3\" data-type=\"background\" ":"";

	}
}
elseif(function_exists('is_product')  && (is_product() || is_product_category())){

	$detheme_config['page-title']=$detheme_config['dt-shop-title-page'];

}
else{

	$detheme_config['page-title']=the_title('','',false);
}

/* banner section */

if($detheme_config['show-banner-area']){
	
	$detheme_config['banner']="";
	$detheme_config['bannercolor']="";
	add_filter('woocommerce_show_page_title',create_function('','return false;'));

	switch ($detheme_config['dt-show-banner-page']) {
		case 'featured':
				if(function_exists('is_product')  && (is_product() || is_product_category())){
					$banner=$detheme_config['dt-shop-banner-image'];
					if($banner && $image=wp_get_attachment_image_src( $banner['id'], 'full' )){
						$detheme_config['banner']=$image[0];
					}else{
						$detheme_config['bannercolor']=(!empty($detheme_config['banner-color']))?$detheme_config['banner-color']:"";
					}
				}
				elseif(function_exists('is_shop') && is_shop()){

					$post_id=get_option( 'woocommerce_shop_page_id');
					$banner=$detheme_config['dt-shop-banner-image'];


					if ($page_banner=get_post_meta( $post_id, '_page_banner', true )) {

							$featured_img_fullsize_url = wp_get_attachment_image_src( $page_banner, 'full' );
							$banner=(!empty($featured_img_fullsize_url['0']))?$featured_img_fullsize_url['0']:"";
							if(!empty($banner)) $detheme_config['banner']=$banner;

					}
					elseif(isset($banner['id']) && $image=wp_get_attachment_image_src( $banner['id'], 'full' )){
						$detheme_config['banner']=$image[0];
					}else{
						$detheme_config['bannercolor']=(!empty($detheme_config['banner-color']))?$detheme_config['banner-color']:"";
					}

				}
				elseif(is_page() || is_single()){

					if ($page_banner=get_post_meta( $post_id, '_page_banner', true )) {

						$featured_img_fullsize_url = wp_get_attachment_image_src( $page_banner, 'full' );
						$banner=(!empty($featured_img_fullsize_url['0']))?$featured_img_fullsize_url['0']:"";
						if(!empty($banner)) $detheme_config['banner']=$banner;
					} else {
						$banner=$detheme_config['dt-banner-image']['url'];
						if(!empty($banner)) {
							$detheme_config['banner']=$banner;
						} else {
							$detheme_config['bannercolor']=(!empty($detheme_config['banner-color']))?$detheme_config['banner-color']:"";
						}
					}
			
				}
				elseif(is_home()){

					 $post_id=get_option( 'page_for_posts');

					if ($page_banner=get_post_meta( $post_id, '_page_banner', true )) {

						$featured_img_fullsize_url = wp_get_attachment_image_src( $page_banner, 'full' );
						$banner=(!empty($featured_img_fullsize_url['0']))?$featured_img_fullsize_url['0']:"";
						if(!empty($banner)) $detheme_config['banner']=$banner;
					} else {
						$banner=$detheme_config['dt-banner-image'];
						if($banner && $image=wp_get_attachment_image_src( $banner['id'], 'full' )) {
							$detheme_config['banner']=$image[0];
						} else {
							$detheme_config['bannercolor']=(!empty($detheme_config['banner-color']))?$detheme_config['banner-color']:"";
						}
					}
				}
				elseif(is_category() || is_archive() || is_search() || is_front_page()){
					$banner=$detheme_config['dt-banner-image'];
					if($banner && $image=wp_get_attachment_image_src( $banner['id'], 'full' )) {
							$detheme_config['banner']=$image[0];
					} else {
						$detheme_config['bannercolor']=(!empty($detheme_config['banner-color']))?$detheme_config['banner-color']:"";
					}
				}

			break;
		case 'image':
	
				$banner=$detheme_config['dt-banner-image'];

				if(function_exists('is_product')  && (is_product() || is_shop() || is_cart() || is_checkout() || is_account_page() || is_product_category())){
					$banner=$detheme_config['dt-shop-banner-image'];
				}
				elseif(function_exists('is_shop') && is_shop()){
					$banner=$detheme_config['dt-shop-banner-image'];
				}

				if($banner && $image=wp_get_attachment_image_src( $banner['id'], 'full' )) { 
					$detheme_config['banner']=$image[0];
				} else {
					$detheme_config['bannercolor']=(!empty($detheme_config['banner-color']))?$detheme_config['banner-color']:"";
				}
			break;
		case 'color':
				$detheme_config['bannercolor']=(!empty($detheme_config['banner-color']))?$detheme_config['banner-color']:"";
			break;
		case 'none':
		default:
			break;
	}

	if($detheme_config['dt-show-title-page']){
		$detheme_config['dt-show-banner-title']=true;
		$detheme_config['dt-show-title-page']=false;
	}

}
else{
	$detheme_config['dt-show-banner-title']=false;
	$detheme_config['banner']="";
	$detheme_config['bannercolor']="";

}

/* header section */
if($detheme_config['dt-show-header']){
	if(is_front_page() || is_detheme_home(get_post())){
		$detheme_config['dt-logo-image']=$detheme_config['homepage-dt-logo-image'];
		$detheme_config['dt-logo-image-transparent']=$detheme_config['homepage-dt-logo-image-transparent'];
	}

	$detheme_config['logo-width']=(!empty($detheme_config['dt-logo-image']['url']) && (int)$detheme_config['dt-logo-width'] > 0 )?$detheme_config['dt-logo-width']:"";
	$detheme_config['logo-top']=(!empty($detheme_config['dt-logo-margin']) && (int)$detheme_config['dt-logo-margin'] !== '0' )?(int)$detheme_config['dt-logo-margin']:"";
	$detheme_config['logo-left']=(!empty($detheme_config['dt-logo-leftmargin']) && (int)$detheme_config['dt-logo-leftmargin'] !== '0' )?(int)$detheme_config['dt-logo-leftmargin']:"";
}
else{
	$detheme_config['logo-width']="";
	$detheme_config['logo-top']="";
	$detheme_config['logo-left']="";
}

/* top-bar section */

if($detheme_config['showtopbar']){
	$detheme_config['showtopbar']=( 
		(
			(($detheme_config['dt-left-top-bar']=='menu' || $detheme_config['dt-left-top-bar']=='icon') && $detheme_config['dt-left-top-bar-menu']!='') ||
			$detheme_config['dt-left-top-bar']=='text' && $detheme_config['dt-left-top-bar-text']!=''
		)
		|| 
		(
			(($detheme_config['dt-right-top-bar']=='menu' || $detheme_config['dt-right-top-bar']=='icon') && $detheme_config['dt-right-top-bar-menu']!='') ||
			$detheme_config['dt-right-top-bar']=='text' && $detheme_config['dt-right-top-bar-text']!=''
		)

		)?true:false;
}

function omnpress_getBackgroundStyle($image_id,$background_style=""){

	$featured_img_fullsize_url = wp_get_attachment_image_src( $image_id, 'full' );
	$css_background="background-image:url('".esc_url($featured_img_fullsize_url[0])."') !important;";

	switch($background_style){
	    case'parallax':
	        $parallax=" data-speed=\"3\" data-type=\"background\" ";
	        $backgroundattr="background-position: 0% 0%; background-repeat: no-repeat; background-size: cover";
	        break;
	    case'parallax_all':
	        $parallax=" data-speed=\"3\" data-type=\"background\" ";
	        $backgroundattr="background-position: 0% 0%; background-repeat: repeat; background-size: cover";
	        break;
	    case'cover':
	        $parallax="";
	        $backgroundattr="background-position: center !important; background-repeat: no-repeat !important; background-size: cover!important";
	        break;
	    case'cover_all':
	        $parallax="";
	        $backgroundattr="background-position: center !important; background-repeat: repeat !important; background-size: cover!important";
	        break;
	    case'no-repeat':
	        $parallax="";
	        $backgroundattr="background-position: center !important; background-repeat: no-repeat !important;background-size:auto !important";
	        break;
	    case'repeat':
	        $parallax="";
	        $backgroundattr="background-position: 0 0 !important;background-repeat: repeat !important;background-size:auto !important";
	        break;
	    case'contain':
	        $parallax="";
	        $backgroundattr="background-position: center !important; background-repeat: no-repeat !important;background-size: contain!important";
	        break;
	    case 'fixed':
	        $parallax="";
	        $backgroundattr="background-position: center !important; background-repeat: no-repeat !important; background-size: cover!important;background-attachment: fixed !important";
	        break;
	    default:
	        $parallax=$backgroundattr="";
	        break;
	}

	return array('css'=>$css_background.$backgroundattr,'body'=>$parallax);
}
?>