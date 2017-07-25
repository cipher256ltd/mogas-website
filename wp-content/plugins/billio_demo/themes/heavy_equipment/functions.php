<?php
defined('ABSPATH') or die();

function equipment_option_import($allowed){

	return array_merge($allowed, array('dt_post_settings','dt_report_post_settings','essential_grid_settings','wpb_js_content_types','wpb_js_templates'));
}

add_filter('import_option_accepted','equipment_option_import');
?>