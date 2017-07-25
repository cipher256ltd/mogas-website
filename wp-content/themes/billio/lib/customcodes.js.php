<?php
@header('Content-Type: application/x-javascript;');
$plugin_path=str_replace(basename(dirname(__FILE__))."/".basename(__FILE__),"",$_SERVER['PHP_SELF']);
?>
(function($) {
'use strict';

	

	tinymce.PluginManager.add( 'dtshortcode', function( ed, url ) {

		ed.addCommand("dtPopup", function ( a, params )
		{
		var popup = params.identifier;
			tb_show(ed.getLang('dtshortcode.insert_dt_shortcode'), ajaxurl+"?action=billio_get_shortcode&type="+popup+"&height=400&TB_iframe=true");
		});

		ed.addButton( 'dtshortcode', {
		    type: 'splitbutton',
		    icon: 'dt_code',
			title: ed.getLang('dtshortcode.dt_shortcode'),
			onclick : function(e) {},
				menu: [
				{text: ed.getLang('dtshortcode.icon_title'),icon: 'dt_icon',onclick:function(){
				ed.execCommand("dtPopup", false, {title: ed.getLang('dtshortcode.icon_title'),identifier: 'icon'})
				}},
				{text: ed.getLang('dtshortcode.button_title'),icon: 'dt_button',onclick:function(){
				ed.execCommand("dtPopup", false, {title: ed.getLang('dtshortcode.button_title'),identifier: 'button'})
				}},
				{text: ed.getLang('dtshortcode.counto_title'),icon: 'dt_counto',onclick:function(){
				ed.execCommand("dtPopup", false, {title: ed.getLang('dtshortcode.counto_title'),identifier: 'counto'})
				}}
				]
	         });
	         
	});
	         
 
})(jQuery);
