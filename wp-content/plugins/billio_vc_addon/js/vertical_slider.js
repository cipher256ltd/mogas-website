jQuery(document).ready(function($) {

    'use strict';
    var initCnSlider=function () {
    "use strict";
        if ($('.dt_vertical').length) {
            $('.dt_vertical').each(function () {

                var $cn_list    = $('.cn_list',$(this));
                var $pages      = $cn_list.find('.cn_page');
                //how many pages
                var cnt_pages   = $pages.length;
                //the default page is the first one
                var page        = 1;
                //list of news (left items)
                var $items      = $cn_list.find('.cn_item');
                //the current item being viewed (right side)
                var $cn_preview = $('.cn_preview',$(this));
                //index of the item being viewed. 
                //the default is the first one
                var current     = 1;

                $items.each(function(i){
                        var $item = $(this),itemnum=$items.length,cn_height=$item.outerHeight(true),cn_preview=($cn_preview.data('height'))?$cn_preview.data('height'):$cn_list.outerHeight(true);

                        $item.data('idx',i+1);
                        $item.bind('click',function(){
                            var $this       = $(this);
                            $cn_list.find('.selected').removeClass('selected');
                            $this.addClass('selected');
                            var idx         = $(this).data('idx');
                            var $current    = $cn_preview.find('.cn_content:nth-child('+current+')');
                            var $next       = $cn_preview.find('.cn_content:nth-child('+idx+')');
                            
                           if(idx > current){
                                $current.stop().animate({'top':-cn_preview+'px'},300,'swing',function(){
                                    $(this).css({'top':cn_preview+'px'});
                                });
                                $next.css({'top':cn_preview+'px'}).stop().animate({'top':'0'},300,'swing');
                            }
                            else if(idx < current){
                                $current.stop().animate({'top':cn_preview+'px'},300,'swing',function(){
                                    $(this).css({'top':cn_preview+'px'});
                                });
                                $next.css({'top':-cn_preview+'px'}).stop().animate({'top':'0'},300,'swing');
                            }
                            current = idx;
                        });
                    });

           });
        }
    };

    initCnSlider();
});
