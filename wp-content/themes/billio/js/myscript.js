var $ = jQuery.noConflict();
(function( window, $, undefined ){

  'use strict';

  var $event = $.event,
      dispatchMethod = $.event.handle ? 'handle' : 'dispatch',
      resizeTimeout;

  $event.special.smartresize = {
    setup: function() {
      $(this).bind( "resize", $event.special.smartresize.handler );
    },
    teardown: function() {
      $(this).unbind( "resize", $event.special.smartresize.handler );
    },
    handler: function( event, execAsap ) {
      // Save the context
      var context = this,
          args = arguments;

      // set correct event type
      event.type = "smartresize";


      if ( resizeTimeout ) { clearTimeout( resizeTimeout ); }
      resizeTimeout = setTimeout(function() {
        $event[ dispatchMethod ].apply( context, args );
      }, execAsap === "execAsap"? 0 : 100 );
    }
  };

  $.fn.smartresize = function( fn ) {
    return fn ? this.bind( "smartresize", fn ) : this.trigger( "smartresize", ["execAsap"] );
  };

})( window, jQuery );

/* --- Parallax Background Function--- */

jQuery(document).ready(function($){

    "use strict";

    var dtParallax=function() {

        "use strict";
        var $window = $(window);
        var minwidthparallax = 768;

        $('body[data-type="background"],section[data-type="background"],div[data-type="background"]').each(function(){

            var $bgobj = $(this); // assigning the object
            $window.scroll(function() {

               if ($(this).width()>minwidthparallax) {
                    var position=$bgobj.position();
                    var yPos = (($(document).scrollTop() - position.top) / $bgobj.data('speed'));
                    // Put together our final background position
                    var coords = '50% '+ yPos + 'px';
       
                   // Move the background
                    $bgobj.css({ backgroundPosition: coords });
                }
            }); 
        });    


    }

    dtParallax();

    //dtCounter();

    var setFooterWidget=function(){


        if ($('#footer-right .border-left').length) {

            var $item=$('#footer-right .border-left:first');
            var $col=$item.hasClass('col-2')?2:$item.hasClass('col-3')?3:$item.hasClass('col-4')?4:1;
            var rowHeight=$item.outerHeight(true);

            $('.border-left').matchHeight();
        }

    };

    
    if($('#menusearchform').length){

     $( ".search_btn" ).click(function() {
          $(this).parents('form').find(".popup_form").fadeToggle( "fast", "swing" );
          return false;
      });


    $('body').click(function(e){
      var $searchform=$('.popup_form');
      if(! ($searchform.has(e.target).length || $(e.target).is('.popup_form input'))){
        $searchform.fadeOut('fast','swing');
      }
    });

    }

    if ($('.social-share-link').length) {
        $('.social-share-link').each(function () {
            $(this).click(function (event) {
                event.preventDefault();
                var idtobedisplay = $(this).siblings('.list-social-icons').attr('id');

                $('.list-social-icons').each(function () {
                  if (idtobedisplay!=$(this).attr('id')) {
                    $(this).fadeOut();
                  }
                });

                $(this).siblings('.list-social-icons').toggle(200);
            });
        });
    }

    function dtCounto() {
        if ($('.dt-counto').length) {
            $('.dt-counto').each(function () {
              $(this).appear(function () {
                var $to = $(this).data('to'),$oldvalue=$(this).text(),$from = $(this).data('from');

                if($oldvalue){
                  $to =parseFloat($oldvalue);
                }

                $(this).countTo({
                    from: $from,
                    to: $to,
                    speed: 1500,
                    refreshInterval: 50,
                    onUpdate:function(value){
                      $(this).text(value.toFixed(0));
                    }
                });

              }, {
                  accX: 0,
                  accY: -200
              })

            });

        }
    }

    dtCounto();


    // Equal Heights


      try{

       $('.dt-iconboxes.layout-6').matchHeight();
       $('.dt-iconboxes-4.layout-4').matchHeight(); 
       $('.equal-height').matchHeight();
       $('.same-height').matchHeight();
       $('.dt-partner .partner-item').matchHeight();
      }
        catch(err){}

  
    // Sticky Menu


     try{

      if ($("#head-page.sticky").length) {
        $("#head-page.adminbar-not-here").sticky({ topSpacing: 0 });
        $("#head-page.adminbar-is-here").sticky({ topSpacing: 32 });
      }

    }
    catch(err){}


    $(window).smartresize(function(){

        setFooterWidget();

      // Full Screen Slider

      $('.slide-bg').css({
          marginLeft: - ($(window).width() - $('.slide-frame').outerWidth())/2,
          height: ($(window).height()),
          width : ($(window).width()) + 200
      }); 
   });

    // Sub Banner Top Padding

    $( window ).smartresize(function() {
        var topHeadPageHeight = $('.top-bar').height();
        var topBarHeight = $('#head-page').height();

        if ($('#banner-section')) {
          if (!$('#banner-section.vertical_menu_container')) {
            $('#banner-section').css('padding-top', topHeadPageHeight + topBarHeight);
          }
        }

    });

    // Set Menu Height on mobile to show scrolling inside menu
    $(window).smartresize(function() {
        var menuBarHeight = $('#head-page').height();
        // outerHeight is used for IOS
        var outerHeight = $(window).outerHeight() + menuBarHeight;
        var windowHeight = $(window).height();

        windowHeight = (outerHeight>windowHeight) ? outerHeight : windowHeight;        

        var windowWidth = $(window).width();
        if ($('#mobile-header').css('display')!='none') {
          if (windowWidth<991) {
              $('#dt-menu').css('height',windowHeight);
          }
        } else {
            $('#dt-menu').css('height','auto');
        }
    });

    // Set content position relative to menu
    $(window).smartresize(function() {
      var windowWidth = $(window).width();
      var topheadHeight = 0;
      var headpageHeight = 0;
      
      headpageHeight = $('#head-page').height();
      topheadHeight = $('#top-bar').height() + headpageHeight;
      if (windowWidth<=767) {
        //krn top-bar tdk ditampilkan di resolusi < 767px maka tinggi top-bar tidak perlu ditambahkan
        topheadHeight = $('#head-page').height();
      }

      var is_Vertical_Menu    = ($('.vertical_menu').length==0) ? false : true;
      var is_PageBar_Menu     = ($('.dt-menu-pagebar').length==0) ? false : true;
      var has_Banner_Section  = ($('#banner-section').length==0) ? false : true;
      var has_Top_Bar         = ($('#top-bar').length==0) ? false : true;

      var height_Top_Bar = 0;
      if (has_Top_Bar) { height_Top_Bar = $('#top-bar').height(); }

      var height_Banner = 0;
      if (has_Banner_Section) { height_Banner = $('#banner-section').height(); }

      if (has_Top_Bar && has_Banner_Section) {
            $('#banner-section').css('margin-top',-height_Top_Bar-1);
      }

      if (is_Vertical_Menu) {
          $('.top-head ~ .content').css('padding-top',40);
      } else if (is_PageBar_Menu) { //if (is_Vertical_Menu) {
            if (has_Banner_Section) {
              $('#dt_pagebar').css('margin-top',-height_Banner+headpageHeight+height_Top_Bar);
            } else {
              $('#dt_pagebar').css('margin-top',headpageHeight);
              var height_pagebar = $('#dt_pagebar').height();
              $('#dt_pagebar ~ .content').css('padding-top',headpageHeight + height_pagebar + 40);
            }

            var dt_pagebar_menu_height = $('#dt_pagebar').height()/2;
            $('.dt_pagebar_menu').css('height',dt_pagebar_menu_height);
            $('.dt_pagebar_wrapper').css('margin-top',-dt_pagebar_menu_height + 2);
            $('#dt_pagebar').css('z-index',99);
      } else { //if (is_Vertical_Menu) {

        if (has_Banner_Section) {
          $('.top-head ~ .content').css('padding-top',40);
        } else { //if (has_Banner_Section)
          if ($('.dt-boxed-container .top-head').length!=0) {
            $('.top-head ~ .content').css('padding-top',headpageHeight + 20);  
          } else {
            $('.top-head ~ .content').css('padding-top',topheadHeight + 40);
          }
        } //if (has_Banner_Section)

      } //if (is_Vertical_Menu) {

    });
    
  $(window).smartresize(function(){

  if ($('.dt-timeline').length) {
    $('.dt-timeline').each(function(){
      var timeline=$(this),hasleftchild=timeline.find('.time-item').hasClass('left'),hasrightchild=timeline.find('.time-item').hasClass('right');

      if(hasrightchild && !hasleftchild){
        if($(window).outerWidth() > 767){
          timeline.css('margin-left','50px');  
        }
        timeline.find('.time-item').css('margin-left','0%');
      }else if(!hasrightchild && hasleftchild){

        if($(window).outerWidth() > 767){
          timeline.css('margin-right','50px');  
         }
        timeline.find('.time-item').css('margin-right','0%');

      }
    });

    }
  });


  // Blog Social Share hover 
  $(".billio-ss-facebook51").hover(function() {
        $(this).addClass("billio-ss-facebook52");
        $(this).removeClass("billio-ss-facebook51");
    },function() {
        $(this).addClass("billio-ss-facebook51");
        $(this).removeClass("billio-ss-facebook52");
    }
  );

  $(".billio-ss-twitter44").hover(function() {
        $(this).addClass("billio-ss-twitter43");
        $(this).removeClass("billio-ss-twitter44");
    },function() {
        $(this).addClass("billio-ss-twitter44");
        $(this).removeClass("billio-ss-twitter43");
    }
  );

  $(".billio-ss-google115").hover(function() {
        $(this).addClass("billio-ss-google114");
        $(this).removeClass("billio-ss-google115");
    },function() {
        $(this).addClass("billio-ss-google115");
        $(this).removeClass("billio-ss-google114");
    }
  );

  $(".social-share-button-group li a").click(function(event) {
    if ($(this).attr('href')) {
      event.preventDefault();
      popup_social($(this).attr('href'), {url: $(this).data('url'), u: $(this).data('url')});
    }
  });

  // Target Parent menu on child hover
  $(".sub-nav").hover(function() {
        $(this).closest(".menu-item").toggleClass("hovered")
  });


  // SCROLLING SIDEBAR

  // Fix issue sticky scrolling bar on Safari Browser.
  /*
  $(document).scroll(function() {
    if ($("#floatMenu").length) {
      $("#floatMenu").css("left",$("#floatMenu-sticky-wrapper").offset().left);
    }
  });


  $("#floatMenu").sticky({topSpacing:150});
  */
  // /SCROLLING SIDEBAR

  // Cart Popup
 
  var cartbox = $('.cart-popup');
  $(".cart-click").click(function() {
      if($(window).width() >480 ){
          $( ".popup_form" ).fadeOut("fast", "swing");
          $('.md-modal').removeClass('md-show');

          if ($(".dt-menu-left .cart-popup").length>0) {
            if($(window).width() >767 ){
              $(".dt-menu-left .cart-popup").css('right',$(".dt-menu-left").width()-$(".dt-menu-left .bag").position().left-60);
            } else {
              $(".dt-menu-left .cart-popup").css('right',0);
            } 
          }

          if ($(".dt-menu-center .cart-popup").length>0) {
            if($(window).width() >767 ){
              $(".dt-menu-center .cart-popup").css('right',$(".dt-menu-center").width()-$(".dt-menu-center .bag").position().left-60);
            } else {
              $(".dt-menu-center .cart-popup").css('right',0);
            } 
          }

          return false;
      }
  });


  $('body').on('added_to_cart',function(){
      //var cartTotal=$('body').find('.total .amount:first'),catItemCount=$('.total:first').data('items');
      //$('.cart_amounts').html(cartTotal);
        $('.item_count').html($('#cart_contents_count').html());
      }).click(function(event){
      
        if (!$(event.target).is(".cart-popup,.cart-popup *,.popup_form, .popup_form *")) {
          //$( ".cart-popup,.popup_form" ).fadeOut("fast", "swing");
        }
  });

  // search "read more" on blog article, if find show button "read more"
  $(".blog .postcontent:has(.more-link)").each(function( index ) {
    $(this).next(".postmetabottom").children("div").children("div").children("a.btn-readmore").css("display","inline-block");
  });

   // scroll to anchor 

     var url_test=location.hash;

     if(url_test!=''){
        lets_Scroll($(url_test));
     }


    $("a[href*='#']:not([href='#'])").unbind('click').on("click", function(e) {

        if($(this).closest('.woocommerce-tabs').length || $(this).data('toggle')=='tab' || $(this).data('toggle')=='collapse' || $(this).data('slide')=='next' || $(this).data('slide')=='prev'
          || $(this).is('.woocommerce-review-link') || $(this).is('.ui-tabs-anchor') || typeof $(this).data('vc-container')=='string' || $(this).closest('.vc_tta-tabs-list').length || $(this).closest('.wpb_accordion_section').length || $(this).closest('.nav-tabs').length){
          return;
        }
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {

            var target = $(this.hash);
            if(target.length){
              e.preventDefault();
              lets_Scroll(target);
            }
        }

    });


    function lets_Scroll(target){

           var scroll,navbar=$('#head-page'),offset=0;
           var ua = window.navigator.userAgent;
           var msie = ua.indexOf("MSIE ");

            var target = target.length ? target : $("[id=" + this.hash.slice(1) + "]");
            scroll = target.offset().top;

            if(navbar.length){
               offset=navbar.outerHeight(true)+parseInt($('html').css('margin-top'));
            }

            if (target.length) {

                if (typeof document.body.style.transitionProperty === 'string' && !msie) {

                    var avail = $(document).height() - $(window).height();

                    if (scroll > avail) {
                        scroll = avail;
                    }


                    $("body").css({
                        "margin-top" : ( $(window).scrollTop() - scroll + offset) + "px",
                        "transition" : "1s ease-in-out"
                    }).data("transitioning", true);

                } else {
                    $("html, body").animate({
                        scrollTop: scroll-offset
                    }, 1000);
                    return;
                }
            }

        $("body").on("transitionend webkitTransitionEnd msTransitionEnd oTransitionEnd", function (e) {
        if (e.target == e.currentTarget && $(this).data("transitioning") === true) {
            $(this).removeAttr("style").data("transitioning", false);
            $("html, body").scrollTop(scroll-offset);
             return;
          }
        });
    }

  /* responsive iframe */

  $(window).smartresize();
  $(document).scroll();


}); //jQuery(document).ready 

function uncheckboxes(nav){
  var navarray = document.getElementsByName(nav);
  for(var i = 0; i < navarray.length; i++){
    navarray[i].checked = false
  }     
}

(function($) {
  /* scrolly v0.1 | (c) n33 | n33.co @n33co | MIT */

    (function(e){var t="click.scrolly";e.fn.scrolly=function(r,i){r||(r=1e3),i||(i=0),e(this).off(t).on(t,function(t){var n,s,o,u=e(this),a=u.attr("href");a.charAt(0)=="#"&&a.length>1&&(n=e(a)).length>0&&(s=n.offset().top,u.hasClass("scrolly-centered")?o=s-(e(window).height()-n.outerHeight())/2:(o=Math.max(s,0),i&&(typeof i=="function"?o-=i():o-=i)),t.preventDefault(),e("body,html").stop().animate({scrollTop:o},r,"swing"))})}})(jQuery);

  $(function() {
    var $body = $('body'),
      $window = $(window),
      $header = $('#head-page'),
      $banner = $('#banner-section');
      $tophead = $('.top-head');

    // Re-enable animations until we're done loading everything.
      $window.load(function() {
        $body.removeClass('loading');
      });

    // Scrolly links.
      $('.scrolly').scrolly(1000, -10);

    // Header.
    // If the header is using "alt" styling and #banner is present, use scrollwatch
    // to revert it back to normal styling once the user scrolls past the banner.
    // Note: This is disabled on touch devices and whenever the 'normal' breakpoint is
    // active (effectively disabling it on 'narrow', 'narrower', and 'mobile' as well).
      
      if ($header.hasClass('alt') && !$tophead.hasClass('vertical_menu')) {
        $window.on('load resize', function() {
          // scrollgress v0.2 | (c) n33 | n33.co @n33co | MIT
            (function(){var e="scrollwatch",t="length",n="top",r=null,i="scrollgress",s="data",o="scrollwatch-state",u="range",a="anchor",f="unscrollwatch",l="unscrollgress",c="removeData",h="element",p="-id",d="scroll.",v="height",m="scrollTop",g="center",y="bottom",b=$(window),w=$(document),E=1e3;$.fn[e]=function(f){var l,c,h,p;if(this[t]>1){for(l=0;l<this[t];l++)$(this[l])[e](f);return this}return c=$.extend({range:.5,anchor:n,init:r,on:r,off:r,delay:0},f),h=$(this),c.init&&c.init(h),h[s](o,-1)[i](function(e){window.clearTimeout(p),p=window.setTimeout(function(){var t=parseInt(h[s](o));if(t==0||t==-1)if(e>=-1*c[u]&&e<=c[u]){h[s](o,1),c.on&&c.on(h);return}if(t==1||t==-1)if(e<-1*c[u]||e>=c[u]){h[s](o,0),c.off&&c.off(h);return}},c.delay)},{anchor:c[a]},e),h},$.fn[f]=function(){var n,r;if(this[t]>1){for(n=0;n<this[t];n++)$(this[n])[f]();return this}return r=$(this),r[c](o,0)[l](e),r},$.fn[i]=function(e,r,o){var u,f,l,c,S;if(this[t]>1){for(u=0;u<this[t];u++)$(this[u])[i](e,r,o);return this}return o||(o=i),f=$.extend({anchor:n,direction:"both",scope:h,easing:0},r),l=$(this),l[s](o+p)||l[s](o+p,E++),c=l[s](o+p),S=d+o+"-"+c,b.off(S).on(S,function(){var t,r=l.offset()[n],i=l.outerHeight(),s=w[v]();switch(f.scope){default:case h:switch(f[a]){default:case n:t=(r-b[m]())/i*-1;break;case g:t=(r-b[m]()-(b[v]()-i)/2)/i*-1;break;case y:t=(r-b[m]()-(b[v]()-i))/i*-1}break;case"window":switch(f[a]){default:case n:t=(r-b[m]())/b[v]()*-1;break;case g:t=(r-b[m]()-(b[v]()-i)/2)/b[v]()*-1;break;case y:t=(r-b[m]()-(b[v]()-i))/b[v]()*-1}}f.direction=="forwards"?t=Math.max(0,t):f.direction=="backwards"&&(t=Math.min(0,t)),t>0?t=Math.max(0,t-f.easing/100):t<0&&(t=Math.min(0,t+f.easing/100)),e(t,l)}).trigger("scroll"),l},$.fn[l]=function(e){var n,r,o,u;if(this[t]>1){for(n=0;n<this[t];n++)$(this[n])[l](e);return this}return e||(e=i),r=$(this),r[s](e+p)?(o=r[s](e+p),u=d+e+"-"+o,b.off(u),r[c](e+p),r):r}})();
          // Apply scrollgress to banner.
          
            if ($('#banner-section').length>0) {
              $banner = $('#banner-section');
            } else if ($('.mainbanner').length>0) {
              $banner = $('.mainbanner');
            } else if ($('.crellyslider-slider').length>0) {
              $banner = $('.crellyslider-slider:first');
            } else if ($('.rev_slider_wrapper').length>0 && $('.rev_slider_wrapper:first').is(':visible')) {
              $banner = $('.rev_slider_wrapper:first');
            } else if ($('.wpb_revslider_element').length>0) {
              $banner = $('.wpb_revslider_element:first');
            } else if ($('.reveal-area').length>0) {
              $banner = $('.reveal-area');
            } else if ($('.blank-reveal-area').length>0) {
              $banner = $('.blank-reveal-area');
            }

            if ($banner.length > 0) {
              $banner.scrollwatch({
                delay:    0,
                range:    1,
                anchor:   'top',
                on:     function() { $header.addClass('alt reveal'); $tophead.removeClass('sticky'); },
                off:    function() { $header.removeClass('alt'); $tophead.addClass('sticky'); }
              });
            }
        });
      
      }
      
  });

})(jQuery);

function popup_social(url, params) {
    var k, popup, qs, v;

    if (params == null) {
      params = {};
    }
    popup = {
      width: 500,
      height: 350
    };

    popup.top = (screen.height / 2) - (popup.height / 2);
    popup.left = (screen.width / 2) - (popup.width / 2);
    qs = ((function() {
      var _results;
      _results = [];
      for (k in params) {
        v = params[k];
        _results.push("" + k + "=" + v);
      }
      return _results;
    }).call(this)).join('&');
    if (qs) {
      qs = "?" + qs;
    }

    return window.open(url + qs, 'targetWindow', "toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,left=" + popup.left + ",top=" + popup.top + ",width=" + popup.width + ",height=" + popup.height);

};