jQuery(document).ready(function($){

   if((typeof window.themecolor) =='undefined'){
        window.themecolor="#1abc9c";
     }


    function adjustIconboxes(themeColor){
        'use strict';

        $('.module_dt_iconboxes').each(function () {
            var iconbox=$(this),moduleid=iconbox.attr('id');

            if($('.dt-iconboxes span',iconbox)){
                var icon=$('.dt-iconboxes span',iconbox);
                var parnt=icon.parent(),color=icon.css("background-color");

                if('undefined'!==color && ('transparent'==color || 'rgba(0, 0, 0, 0)'==color) )
                {
                    for (var i=0;i<10;i++){
                        var parnt2=$(parnt).parent();
                        color=parnt2.css("background-color");
                        parnt=parnt2;
                       if('transparent'!==color && 'rgba(0, 0, 0, 0)'!==color.toString() && 'undefined'!==color)
                            break;
                    }

                   if('transparent'!==color.toString() && 'rgba(0, 0, 0, 0)'!==color.toString() && 'undefined'!==color ){
                        var style='';

                        style+='#'+moduleid+' .dt-iconboxes span:after{border-top-color:'+color+' !important;}';
                        style+='#'+moduleid+' .dt-iconboxes span:hover:after{border-top-color:'+themeColor+' !important;}';
                       $('<style/>', {text: style}).appendTo('body');
                    }


               }

            }

       });

        //dt-iconboxes span
    }


    adjustIconboxes(window.themecolor);

       /* --- Counter Functions--- */

    function dtDoCounter($this,$to) {

        "use strict";
        $($this).css('opacity', '1');
        $($this).countTo({
            from: 0,
            to: $to,
            speed: 1500,
            refreshInterval: 50
        })
    }


    function dtCounter() {

        "use strict";
        if ($('.dt-counter').length) {
            $('.dt-counter').each(function () {

                $(this).appear(function () {
                    var $countTo = $(this).text();
                    if ($(window).width()>480) {
                       dtDoCounter($(this),$countTo);
                    }
                }, {
                    accX: 0,
                    accY: -100
                })
            })
        }
    }

    dtCounter();

});

