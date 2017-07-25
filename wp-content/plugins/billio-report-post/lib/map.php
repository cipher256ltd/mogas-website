<?php
defined('ABSPATH') or die();

$check_categories = array();
$report_categories = get_categories(array('taxonomy' => 'dtreportpostcat'));
foreach ($report_categories as $report_category) {
    $check_categories[$report_category->name]=$report_category->term_id;
}

vc_map( 
    array( 
        'name' => __( 'Report List', 'billio_report_post' ),
        'base' => 'dt_reportlist',
        'class' => '',
        'controls' => 'full',
        'icon' => 'admin-icon-box',
        'category' => __( 'deTheme', 'billio_report_post' ),
        'params' => 
            array(  
        		array(
        			'type' => 'checkbox',
        			'heading' => __( 'Report Categories', 'billio_report_post' ),
        			'param_name' => 'categories',
        			'value' => $check_categories
                ),
                array( 
                'heading' => __( 'Number of reports per page', 'billio_report_post' ),
                'param_name' => 'posts_per_page',
                'class' => '',
                'value' => '10',
                'type' => 'textfield',
                 ),         
                array( 
                'heading' => __( 'Order By', 'billio_report_post' ),
                'param_name' => 'orderby',
                'class' => '',
                'value' => array(__('None','billio_report_post') =>'none', 
                	__('ID','billio_report_post') => 'ID',
                	__('Author','billio_report_post') => 'author',
                	__('Title','billio_report_post') => 'title',
                	__('Pre Title','billio_report_post') => 'pretitle',
                	__('Name','billio_report_post') => 'name',
                	__('Date','billio_report_post') => 'date',
                	__('Modified','billio_report_post') => 'modified',
                	__('Parent','billio_report_post') => 'parent',
                	__('Random','billio_report_post') => 'rand',
                	__('Comment Count','billio_report_post') => 'comment_count'
                	),
                'description' => __( 'Order By', 'billio_report_post' ),
                'type' => 'dropdown',
                 ),     
                array( 
                'heading' => __( 'Order', 'billio_report_post' ),
                'param_name' => 'order',
                'class' => '',
                'value' => array(__("ASC",'billio_report_post') =>"asc", __("DESC","detheme") => "desc"),
                'description' => __( 'Order', 'billio_report_post' ),
                'type' => 'dropdown',
                 ),     
                array( 
                    'heading' => __( 'Animation Type', 'billio_report_post' ),
                    'param_name' => 'spy',
                    'class' => '',
                    'value' => 
                     array(
                        __('Scroll Spy not activated','billio_report_post') =>'none',
                        __('The element fades in','billio_report_post') => 'uk-animation-fade',
                        __('The element scales up','billio_report_post') => 'uk-animation-scale-up',
                        __('The element scales down','billio_report_post') => 'uk-animation-scale-down',
                        __('The element slides in from the top','billio_report_post') => 'uk-animation-slide-top',
                        __('The element slides in from the bottom','billio_report_post') => 'uk-animation-slide-bottom',
                        __('The element slides in from the left','billio_report_post') => 'uk-animation-slide-left',
                        __('The element slides in from the right.','billio_report_post') =>'uk-animation-slide-right',
                     ),        
                    'description' => __( 'Scroll spy effects', 'billio_report_post' ),
                    'type' => 'dropdown',
                 ),     
                array( 
                    'heading' => __( 'Animation Delay', 'billio_report_post' ),
                    'param_name' => 'scroll_delay',
                    'class' => '',
                    'value' => '300',
                    'description' => __( 'The number of delay the animation effect of the icon. in milisecond', 'billio_report_post' ),
                    'type' => 'textfield',
                    'dependency' => array( 'element' => 'spy', 'value' => array( 'uk-animation-fade', 'uk-animation-scale-up', 'uk-animation-scale-down', 'uk-animation-slide-top', 'uk-animation-slide-bottom', 'uk-animation-slide-left', 'uk-animation-slide-right') )       
                 ),     
                array(
                    "type" => "css_editor",
                    "heading" => __('Css', "billio_report_post"),
                    "param_name" => "css",
                    "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "billio_report_post"),
                    "group" => __('Design options', 'billio_report_post')
                )
        ) 
    )
);

?>