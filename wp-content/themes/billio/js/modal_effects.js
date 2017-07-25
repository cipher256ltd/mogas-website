/**
 * modalEffects.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2013, Codrops
 * http://www.codrops.com
 */
var ModalEffects = (function() {
	'use strict';
	function init() {

		var overlay = document.querySelector( '.md-overlay' );

		[].slice.call( document.querySelectorAll( '.md-trigger' ) ).forEach( function( el, i ) {
			
			var modal = document.querySelector( '#' + el.getAttribute( 'data-modal' ) ),
				close = modal.querySelector( '.md-close' );

			modal.loaded=false;

			function removeModal( hasPerspective ) {
				classie.remove( modal, 'md-show' );

				if( hasPerspective ) {
					classie.remove( document.documentElement, 'md-perspective' );
				}
			}

			function removeModalHandler() {
				removeModal( classie.has( el, 'md-setperspective' ) ); 
			}

			el.addEventListener( 'click', function( ev ) {

				ev.preventDefault();

				if(!jQuery('.modal_preloader').length){
					jQuery('body').prepend("<div class='modal_preloader'><div class='modal_spinner-container'><div class='modal_loader'></div></div></div>");
				}


				if(!modal.loaded){
					jQuery('.modal_loader').fadeIn('slow');
					jQuery('.modal_preloader').delay(350).fadeIn('slow'); 

					if(jQuery('img',modal).length){

						var all=jQuery('img',modal).length,elDone=1;

						var timeout = null;

						jQuery('img',modal).each(function(i,e){

							var im = jQuery(this).attr('rel'),
							    img = new Image();
							    jQuery(this).attr('src',im);
								img.src = im;
								img.onload = function() {
									timeout = setTimeout(function() {
										if (img.complete) {
											jQuery('.modal_loader').fadeOut('slow');
											jQuery('.modal_preloader').delay(350).fadeOut('slow'); 
											modal.loaded=true;
											classie.add( modal, 'md-show' );	
											clearTimeout(timeout);
										}
									},1000);
									elDone=elDone+1;
								};

								if(all=elDone){
								    //modal.loaded=true;
								    //classie.add( modal, 'md-show' );
								}
						});

					}
					else{
						modal.loaded=true;
					}
				}

				if(modal.loaded){
					jQuery('.modal_loader').fadeOut();
					jQuery('.modal_preloader').fadeOut(); 
					classie.add( modal, 'md-show' );
				}
				
				overlay.removeEventListener( 'click', removeModalHandler );
				overlay.addEventListener( 'click', removeModalHandler );

				if( classie.has( el, 'md-setperspective' ) ) {
					setTimeout( function() {
						classie.add( document.documentElement, 'md-perspective' );
					}, 25 );
				}
			});

			close.addEventListener( 'click', function( ev ) {
				ev.stopPropagation();
				removeModalHandler();
			});

		} );

	}

	init();

})();