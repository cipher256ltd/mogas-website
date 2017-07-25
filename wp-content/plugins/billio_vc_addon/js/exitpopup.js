jQuery(document).ready(function($) {

		if($('#exitpopup').length){

		$('#exitpopup').css('left', (window.innerWidth/2 - $('#exitpopup').width()/2));
		$('#exitpopup').css('top', (window.innerHeight/2 - $('#exitpopup').height()/2));

		$(document).mousemove(function(e) {


			if(e.clientY <= 5)
			{
				// Show the exit popup
				$('#exitpopup_bg').fadeIn();
				$('#exitpopup').fadeIn();

			}
		
		});

		$('body').click(function(e){
			var $el = $('#exitpopup');
			if(!($el.has(e.target).length || $(e.target).is('#exitpopup'))){
				$('#exitpopup_bg').fadeOut();
				$('#exitpopup').slideUp();
			}
		});

		}

});