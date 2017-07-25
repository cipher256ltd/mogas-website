jQuery(document).ready(function($){
	'use strict';

var $swicth_type = $("fieldset[data-id*='detheme-icon-']");

  if($swicth_type.length){

    $swicth_type.each(function(i){


        var idname=$(this).data('id'),$wrapper=$(this).closest('tr'),$button=$('.cb-enable,.cb-disable',$(this)),
        $layoutname=idname.replace('detheme-icon-',''), $layouticon=$("[data-id='"+$layoutname+"-detheme-layout']").closest('.form-table tr');

      $button.live('click',function(e){

        e.preventDefault();
        if($(this).hasClass('cb-enable')){
          if($(this).hasClass('selected')){
            $layouticon.fadeIn('fast');
            $wrapper.css('border-bottom-width','1px');
          }

        }else{
          if($(this).hasClass('selected')){
            $layouticon.fadeOut('fast');
            $wrapper.css('border-bottom-width','0px');
          }
        }

      }).live('change',function(e){

        e.preventDefault();
        if($(this).hasClass('cb-enable')){
          if($(this).hasClass('selected')){
            $layouticon.fadeIn('fast');
            $wrapper.css('border-bottom-width','1px');
          }
        }else{
          if($(this).hasClass('selected')){
           $layouticon.fadeOut('fast');
            $wrapper.css('border-bottom-width','0px');
          }
        }

      });


     $button.trigger('change');
    });


  }

 });
