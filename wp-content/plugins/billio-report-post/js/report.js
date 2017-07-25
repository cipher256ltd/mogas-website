jQuery(document).ready(function($){
    "use strict";
    $('.equal_height').each(function(index,value){
    	var $this = $(this);
    	var $max_height = 0;
    	var $item_height = 0;
    	$this.children('.equal_height_item').each(function(){
    		$item_height = $(this).height();
    		$max_height	= ($item_height>$max_height) ? $item_height : $max_height;
    	});

    	$this.children('.equal_height_item').each(function(){
    		$(this).children('.dt_report_item').css('height',$max_height);
    	});
    });
});