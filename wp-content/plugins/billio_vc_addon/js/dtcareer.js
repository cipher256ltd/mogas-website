jQuery(document).ready(function($){
    "use strict";
    if ($('div.dtcareers').length) {
        $('div.dtcareers').each(function(){

            'use strict';

            var dtcareers=$(this);

            var gettMasonry=function(mod){
                'use strict';

                var $container=$('.dtcareers-container',mod),modwidth=$container.outerWidth(true),$gutter=($container.data('gutter'))?$container.data('gutter'):40,
                Column=($container.data('col'))?$container.data('col'):2,$layoutMode=($container.data('type'))?$container.data('type'):'masonry';

                    if($layoutMode!='masonry') $layoutMode='fitRows';

                    if($(window).width() >= 992 && $(window).width() < 1024){

                        Column=($container.data('col'))?$container.data('col')-1:2;

                    }else if($(window).width() >= 768 && $(window).width() < 992){
                        Column=($container.data('col')==6)?4: ($container.data('col')==5)?3:($container.data('col')==4)?2:2;

                    }else if($(window).width() >= 480 && $(window).width() < 768){
                        
                        Column=2;

                    }else if($(window).width() < 480){
                        Column=1;   
                    }

                    var masonryCol=Math.floor((modwidth-($gutter*(Column - 1)))/Column);

                    $(".career-item",$container)
                            .each(function(i,el){
                              $(this).width(masonryCol);
                              $(this).css('margin-bottom',$gutter);
                        });
                    
                    try{
                            var $isoparam={
                                  itemSelector: '.career-item',
                                  layoutMode:$layoutMode,
                                  masonry: { 
                                    columnWidth: masonryCol,
                                    gutter:$gutter  
                                  }},
                                  filter=$('.dt-career-filter a',mod);

                            if(!filter.hasClass('show-all')){
                                $isoparam.filter=filter.first().data('filter');
                            }

                            $container.isotope($isoparam); 

                            if(filter.length && $('.career-item',$container).length){
                                    filter.click(function(e){
                                    var selector = $(this).data('filter');
                                    if(selector!==undefined){
                                            e.preventDefault();
                                            if(selector=='*'){
                                                 $container.isotope( {filter:'*'}).isotope('reloadItems');

                                            }else{
                                                $container.isotope({filter:selector});
                                            }                           
                                    }
                                    $(this).parents('ul').find('a,li').removeClass('active');
                                    $(this).addClass('active').parent().addClass('active');
                                    return false;

                                });
                            }

                    }
                    catch(err){}
            }

            gettMasonry(dtcareers);

           $(window).smartresize(function(){
              gettMasonry(dtcareers);
            })

        });
       }

});

