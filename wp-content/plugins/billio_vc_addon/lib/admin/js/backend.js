jQuery(document).ready(function ($) {

	$('.wpb_el_type_iconlists').each(function(){

		var $elem=$(this),$button=$('.toggle-iconlist',$elem);

		$elem.find('i.stand_icon').on('click',function(){
	        var clicked_icon = $(this).attr("data-icon-code");

	        $elem.find('.active').removeClass('active');
	        $(this).addClass('active').parent().addClass('active');
	        $elem.find('.icon-selection-value').val(clicked_icon);
	        $('.edit_form_icon',$elem).hide();
	        $button.attr('class','toggle-iconlist '+clicked_icon);

		});

		$button.on('click',function (el) {
			$('.edit_form_icon',$elem).toggle();
		});
	});

	$('.wpb_el_type_iconlists_multi').each(function(){

		var $elem=$(this),$button=$('.toggle-iconlist',$elem),$choices=$('.edit_form_icon_choice',$elem);

		$('li',$choices).click(function(){

			this.remove();

		});

		$elem.find('i.stand_icon').on('click',function(){
	        var clicked_icon = $(this).attr("data-icon-code");
	        var newicon=$('<li><i data-icon="'+clicked_icon+'" class="'+clicked_icon+'"></i></li>');
	        newicon.click(function(){
	        	this.remove();
	        });
	        $choices.append(newicon);
	        saveIcon();

		});

		var saveIcon=function(){

			var theicon=$('i',$choices);
			var select=[];


			theicon.each(function (index,el){

				select.push($(el).data('icon'));

			});

			$elem.find('.icon-selection-value').val(select.join(','));
		}

		$button.on('click',function (el) {

			$('.edit_form_icon',$elem).toggle();

			$(this).toggleClass('icon-minus-1');
		});



	});

	if($('.radio-options').length){
		$('.radio-options').each(function(){
			var $options=$(this),$option=$('.radio-option',$options),
			$input=$('input.wpb_vc_param_value',$options);
			$option.on('change',function(){
				$input.val($(this).val()).trigger('change');
			});
		});

	}

	if($('.option-select').length){
		$('.option-select').each(function(){

			var $elem=$(this),$input=$('input',$elem);


				$('.layout-option',$elem).click(function(){
					$input.val($(this).data('option'));
					$('.layout-option',$elem).removeClass('active');
					$(this).addClass('active');
					$input.trigger('change');
				});
					
		});
	}

	if($('.wpb-select-multiple').length){
		$('.wpb-select-multiple').each(function(){
			var $elem=$(this),$input=$('input',$elem.parents('.portcat'));

				$elem.change(function(){
					$input.val($(this).val());
					$input.trigger('change');
				});
					
		});
	}

	if($('input[name=icon_size]').length){

		$('input[name=icon_size]').on( "change", function( event, ui ) {
			var icon_prev=$('input[name=icon_size]').closest('.vc_edit-form-tab').find('.icon-selection-preview i');
			if(icon_prev.length){
				icon_prev.css('font-size',$(this).val()+'px');
			}
		} ).trigger('change');
	}


	if($('.input-slider').length){
		    $('.input-slider').each(function(){

		    	'use strict';

				var slide=$(this);
				var input_slider=slide.closest('.input-slider-container').find('input[type=text]');

				slide.on('change',function(){
					input_slider.val($(this).val()).trigger('change');
				}).trigger('change');

				input_slider.on('keyup',function(){

					if($(this).val() > parseFloat(slide.attr('max'))){
						$(this).val(parseFloat(slide.attr('max')));
					}

					slide.val($(this).val());

					$(this).trigger('change');
				});

			});
	}

    if($('.carousel-preview').length){

    	var carouselPreview=$('.carousel-preview');

   		$('input[name=pagination_size]').on('change',function(){
   			$('.owl-page span',carouselPreview).css({'width':$(this).val()+'px','height':$(this).val()+'px'});
   		}).trigger('change');

   		$('.vc-color-control[name=pagination_color]').wpColorPicker({
   			defaultColor:'#869791',
	        change:function(event,ui){
				$('.owl-page span',carouselPreview).css({'background-color':ui.color.toString()});
	        }
    	}).on('change',function(){
    		$('.owl-page span',carouselPreview).css({'background-color':$(this).val()});
    	}).trigger('change');

    	$('select[name=pagination_type]').live('change',function(){

    		if($(this).val()=='bullet'){
    			carouselPreview.parents('.vc-shortcode-param').show();
    		}
    		else{
    			carouselPreview.parents('.vc-shortcode-param').hide();
    		}

    	}).trigger('change');



	 }


	$('input[name=layout_type]').on('change',function(){

		var layout_type=$(this).val(),separator_position=$('select[name=separator_position]');

		if(layout_type=='section-heading-polkadot-two-bottom'
		|| layout_type=='section-heading-thick-border'
		|| layout_type=='section-heading-thin-border'
		|| layout_type=='section-heading-double-border-bottom'
		|| layout_type=='section-heading-thin-border-top-bottom'
		){
			separator_position.parents('.vc-shortcode-param').show();

		}
		else{
			separator_position.parents('.vc-shortcode-param').hide();
		}

	}).trigger('change');



});
