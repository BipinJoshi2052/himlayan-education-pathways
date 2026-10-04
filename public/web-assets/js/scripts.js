/*
Author       : theme_ocean
Template Name: Eduleb - Education HTML Template
Version      : 1.0
*/
(function($) {
	'use strict';
	
	jQuery(document).on('ready', function(){
	
		/*PRELOADER JS*/
		$(window).on('load', function() { 
			setTimeout(function(){
				$('.preloaders').fadeToggle();
			}, 1500);
		}); 
		/*END PRELOADER JS*/		
		
		/*START MENU JS*/		
			$(".mobile_menu").simpleMobileMenu({			
				"menuStyle": "slide"
			});
			$(window).on('scroll', function(){
				if ( $(window).scrollTop() > 70 ) {
					$('.site-navigation, .header-white, .header').addClass('navbar-fixed');
				} else {
					$('.site-navigation, .header-white, .header').removeClass('navbar-fixed');
				}
			});	
		/*END MENU JS*/				

		/*START VIDEO JS*/	
		$('.magnific_popup').magnificPopup({
            type: 'iframe'
        });
		/*END VIDEO JS*/	

		/* START COUNTDOWN JS*/
		$('.counter_feature').on('inview', function(event, visible, visiblePartX, visiblePartY) {
			if (visible) {
				$(this).find('.counter-num').each(function () {
					var $this = $(this);
					var rawText = $this.text().trim();
					// Count-up only makes sense for a plain number ("4", "500").
					// Stat labels like "A1-B2" or "Kathmandu" aren't counts at
					// all - animating those drives jQuery's animate() to NaN,
					// which then overwrites the real label. Leave anything
					// that isn't purely numeric exactly as authored.
					if (!/^\d+$/.test(rawText)) {
						return;
					}
					$({ Counter: 0 }).animate({ Counter: parseInt(rawText, 10) }, {
						duration: 2000,
						easing: 'swing',
						step: function () {
							$this.text(Math.ceil(this.Counter));
						}
					});
				});
				$(this).unbind('inview');
			}
		});
		/* END COUNTDOWN JS */

		/*START TESTIMONIAL JS*/	
		$("#testimonial-slider").owlCarousel({
		   items:1,
			itemsDesktop:[1000,1],
			itemsDesktopSmall:[980,1],
			itemsTablet:[768,1],
			itemsMobile:[650,1],
			pagination:true,
			navigation:true,
			navigationText:["",""],
			slideSpeed:1000,
			autoPlay:false
		});
		/*END TESTIMONIAL JS*/	

		/*START TESTIMONIAL JS*/	
		$("#testimonial-slider2").owlCarousel({
		   items:2,
			itemsDesktop:[1000,2],
			itemsDesktopSmall:[980,2],
			itemsTablet:[768,2],
			itemsMobile:[650,1],
			pagination:true,
			navigation:true,
			navigationText:["",""],
			slideSpeed:1000,
			autoPlay:false
		});
		/*END TESTIMONIAL JS*/	

		/*START PARTNER LOGO*/
		$('.partner').owlCarousel({
		  autoPlay: 3000, //Set AutoPlay to 3 seconds
		  items : 4,
		  itemsDesktop : [1199,3],
		  itemsDesktopSmall : [979,3]
		});
		/*END PARTNER LOGO*/			

	}); 	
	
	/*START WOW ANIMATION JS*/
	  new WOW().init();	
	/*END WOW ANIMATION JS*/	
				
})(jQuery);




  

