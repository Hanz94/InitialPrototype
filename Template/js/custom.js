(function($) {
	
	
	
	//Cache jQuery Selector
	var $window		= $(window),
		$carousel	= $('.carousel'),   			// 2. Bootstrap slider pause time
		$dropdown	= $('.dropdown'),				// 3. dropdown submenu on hover in desktopand dropdown sub menu on click in mobile
		$header		= $('#header'),					// 4. Update Header Style + Scroll to Top
		$projectimg	= $('.project-images'),			// 5. Single project slider click + Show large image in top box
		$our_team	= $('.our_team_member'),		// 8. Our Team Member Carousel
		$client		= $('.testimonials-carousel'),	// 9. Testimonials Carousel Slider
		$srv_slide	= $('.service-slider'),			// 10. Single service Slider
		$sidebar	= $('.feedback-sidebar'),		// 11. Testimonials Carousel in sidebar small
		$projectnav	= $('.project-thumbnail'),		// 12. Single Project slider carosure nav image slider
		$brand		= $('.partner-slider'),			// 13. Our Partner Logos Slider Auto
		$contact	= $('#contact-form')			// 22. Contact Form Validation
		
		
		
	/* Table of jQuery settings list
		
		1. Preloader For Hide loader
		2. Bootstrap slider pause time
		3. dropdown submenu on hover in desktopand dropdown sub menu on click in mobile
		4. Update Header Style + Scroll to Top
		5. Single project slider click + Show large image in top box
		6. Submenu Dropdown Toggle
		7. Revolution Slider
		8. Our Team Carousel
		9. Testimonials Carousel Slider Home
		10. Single service Slider
		11. Testimonials Carousel in sidebar small
		12. Project image slider carosure nav image slide
		13. Our Partner Logos Slider Auto
		14. Use for Accordion Box
		15. Fact Counter For Achivement Counting
		16. LightBox / Fancybox
		17. Sortable Masonary with Filters
		18. Gallery With Filters List
		19. Date Picker
		20. Scroll to a Specific Div
		21. Click Search Icon and Open Search Field
		22. Contact Form Validation
		23. Elements Animation
		24. When document is Scrollig, do
		25. When document is loading, do
		26. Youtube and Vimeo video popup control
	
	*/
	
	
	
	
	
	// 1. Preloader For Hide loader
	function handlePreloader() {
		if($('.preloader').length){
			$('.preloader').delay(500).fadeOut(500);
			$('body').removeClass('page-load');
		}
	}
		
	
	
	// 2. Bootstrap slider pause time
	$('.carousel').carousel({
	  interval: 6000,  // Slider Pause time
	  pause: "hover"
	});
	
	
	// 3. dropdown submenu on hover in desktopand dropdown sub menu on click in mobile
	
	
	/* $('#bs-example-navbar-collapse-1').each(function() {
		$dropdown.on('show.bs.dropdown', function(e){
		  	$(this).find('.dropdown-menu').first().stop(true, true).slideDown(300);
		});
		
		$dropdown.on('hide.bs.dropdown', function(e){
		  	$(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
		});	
	}); */
	
	$('#bs-example-navbar-collapse-1').each(function() {
		$dropdown.on('click', function(){
			if($window.width() < 992){
				if($(this).hasClass('open')){
					$(this).find('.dropdown-menu').first().stop(true, true).slideUp(300);
				}
				else{
					$('.nav .dropdown-menu').stop(true, true).slideUp(300);
					$(this).find('.dropdown-menu').stop(true, true).slideDown(300);
				}
			}
		});
		
		$window.on('resize', function(){
			if($window.width() > 991){
				$dropdown.find('.dropdown-menu').removeAttr('style');
			}
		});
		
	});
	
	
	
	// 4. Update Header Style + Scroll to Top
	function headerStyle() {
		if($header.length){
			var windowpos = $window.scrollTop();
			if (windowpos >= 180) {
				$header.addClass('fixed-header');
			} else {
				$header.removeClass('fixed-header');
			}
		}
	}
	
	
	// 5. Single project slider click + Show large image in top box
	$projectimg.each(function() {
		$(this).on('click','.item a',function(event) {
			event.preventDefault();
			$(this).parents('.project-images').find(".item a").removeClass('onlive');
			$(this).addClass('onlive');
			$(this).parents('.project-images').find(".slide-project img").attr("src", $(this).attr("href"));
		});
	});
	
	// 6. Submenu Dropdown Toggle
	if($('.main-menu li.dropdown ul').length){
		$('.main-menu li.dropdown').append('<div class="dropdown-btn"></div>');
		
		//Dropdown Button
		$('.main-menu li.dropdown .dropdown-btn').on('click', function() {
			$(this).prev('ul').slideToggle(500);
		});
		
		//Disable dropdown parent link
		$('.navigation li.dropdown > a').on('click', function(e) {
			e.preventDefault();
		});
	}
	
	
	
	// 7. Revolution Slider
	if($('.revolution-slider .tp-banner').length){

		jQuery('.revolution-slider .tp-banner').show().revolution({
		  
		  delay:10000,
		  startwidth:1200,
		  startheight:720,
		  hideThumbs:600,
	
		  thumbWidth:80,
		  thumbHeight:50,
		  thumbAmount:5,
	
		  navigationType:"bullet",
		  navigationArrows:"0",
		  navigationStyle:"preview4",
	
		  touchenabled:"on",
		  onHoverStop:"off",
	
		  swipe_velocity: 0.7,
		  swipe_min_touches: 1,
		  swipe_max_touches: 1,
		  drag_block_vertical: false,
	
		  parallax:"mouse",
		  parallaxBgFreeze:"on",
		  parallaxLevels:[7,4,3,2,5,4,3,2,1,0],
	
		  keyboardNavigation:"off",
	
		  navigationHAlign:"center",
		  navigationVAlign:"bottom",
		  navigationHOffset:0,
		  navigationVOffset:20,
	
		  soloArrowLeftHalign:"left",
		  soloArrowLeftValign:"center",
		  soloArrowLeftHOffset:20,
		  soloArrowLeftVOffset:0,
	
		  soloArrowRightHalign:"right",
		  soloArrowRightValign:"center",
		  soloArrowRightHOffset:20,
		  soloArrowRightVOffset:0,
	
		  shadow:0,
		  fullWidth:"on",
		  fullScreen:"off",
	
		  spinner:"spinner4",
	
		  stopLoop:"off",
		  stopAfterLoops:-1,
		  stopAtSlide:-1,
	
		  shuffle:"off",
	
		  autoHeight:"off",
		  forceFullWidth:"on",
	
		  hideThumbsOnMobile:"on",
		  hideNavDelayOnMobile:1500,
		  hideBulletsOnMobile:"on",
		  hideArrowsOnMobile:"on",
		  hideThumbsUnderResolution:0,
	
		  hideSliderAtLimit:0,
		  hideCaptionAtLimit:0,
		  hideAllCaptionAtLilmit:0,
		  startWithSlide:0,
		  videoJsPath:"",
		  fullScreenOffsetContainer: ""
	  });

		
	}
	
	
	// 8. Our Team Carousel
	if ($our_team.length) {
		$our_team.owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			autoplayHoverPause:false,
			slideBy: 3,
			smartSpeed: 300,
			autoplay: false,
			navText: [ '<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>' ],
			responsive:{
				0:{
					items:1
				},
				600:{
					items:2
				},
				1024:{
					items:3
				},
				1200:{
					items:3
				}
			}
		});    		
	}
	
	
	// 9. Testimonials Carousel Slider Home
	if ($client.length) {
		$client.owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			autoplayHoverPause:false,
			autoplay: true,
			smartSpeed: 700,
			navText: [ '<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>' ],
			responsive:{
				0:{
					items:1
				},
				600:{
					items:1
				},
				760:{
					items:1
				},
				1024:{
					items:1
				},
				1200:{
					items:1
				}
			}
		});    		
	}
	
	
	// 10. Single Project Slider
	if ($srv_slide.length) {
		$srv_slide.owlCarousel({
			loop:true,
			margin:0,
			nav:true,
			dots: false,
			autoplayHoverPause:false,
			autoplay: true,
			smartSpeed: 700,
			responsive:{
				0:{
					items:1
				},
				600:{
					items:1
				},
				760:{
					items:1
				},
				1024:{
					items:1
				},
				1200:{
					items:1
				}
			}
		});    		
	}
	
	
	// 11. Testimonials Carousel in sidebar small
	if ($sidebar.length) {
		$sidebar.owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			autoplayHoverPause:true,
			autoplay: true,
			smartSpeed: 700,
			navText: [ '<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>' ],
			responsive:{
				0:{
					items:1
				},
				600:{
					items:1
				},
				760:{
					items:1
				},
				1024:{
					items:1
				},
				1200:{
					items:1
				}
			}
		});    		
	}
	
	
	// 12. Single Project slider carosure nav image slider
	if ($projectnav.length) {
		$projectnav.owlCarousel({
			loop:false,
			margin:20,
			nav:true,
			autoplayHoverPause:false,
			autoplay: true,
			smartSpeed: 400,
			navText: [ '<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>' ],
			responsive:{
				0:{
					items:3
				},
				600:{
					items:4
				},
				760:{
					items:5
				},
				1024:{
					items:5
				},
				1100:{
					items:6
				}
			}
		});    		
	}
	
	
	// 13. Our Partner Logos Slider Auto
	if ($brand.length) {
		$brand.owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			dots: false,
			smartSpeed: 500,
			autoplay: 4000,
			responsive:{
				0:{
					items:1
				},
				480:{
					items:2
				},
				600:{
					items:3
				},
				800:{
					items:4
				},
				1200:{
					items:4
				}
			}
		});    		
	}
	
	
	// 14. Use for Accordion Box
	if($('.according_area').length){
		$('.according_title').on('click', function() {
			
		
			if($(this).hasClass('active')){
				$(this).addClass('active');			
			}
			
			//if ($(this).next('.according_details').is(':visible')){
//				$(this).removeClass('active');
//			}
			else{
				$('.according_title').removeClass('active');
				$('.according_details').slideUp(300);
				$(this).addClass('active');
				$(this).next('.according_details').slideDown(300);	
			}
		});	
	}
	
	
	
	// 15. Fact Counter For Achivement Counting
	function factCounter() {
		if($('.fact-counter').length){
			$('.fact-counter .achievement.animated').each(function() {
		
				var $t = $(this),
					n = $t.find(".counting").attr("data-stop"),
					r = parseInt($t.find(".counting").attr("data-speed"), 10);
					
				if (!$t.hasClass("counted")) {
					$t.addClass("counted");
					$({
						countNum: $t.find(".count-text").text()
					}).animate({
						countNum: n
					}, {
						duration: r,
						easing: "linear",
						step: function() {
							$t.find(".counting").text(Math.floor(this.countNum));
						},
						complete: function() {
							$t.find(".counting").text(this.countNum);
						}
					});
				}
				
				//set skill building height


					var size = $(this).children('.progress-bar').attr('aria-valuenow');
					$(this).children('.progress-bar').css('width', size+'%');

				
			});
		}
	}
	
	
	// 16. LightBox / Fancybox
	if($('.photo_gallery a').length) {
		$('.photo_gallery a').fancybox({
			openEffect  : 'elastic',
			closeEffect : 'elastic',
			helpers : {
				media : {}
			}
		});
	}
	
	
	// 17. Sortable Masonary with Filters
	function enableMasonry() {
		if($('.sortable-masonry').length){
	
			var winDow = $(window);
			// Needed variables
			var $container=$('.sortable-masonry .items-container');
			var $filter=$('.sortable-masonry .filter-btns');
	
			$container.isotope({
				filter:'*',
				 masonry: {
					columnWidth : 1 
				 },
				animationOptions:{
					duration:1000,
					easing:'linear'
				}
			});
			
	
			// Isotope Filter 
			$filter.find('li').on('click', function(){
				var selector = $(this).attr('data-filter');
	
				try {
					$container.isotope({ 
						filter	: selector,
						animationOptions: {
							duration: 1000,
							easing	: 'linear',
							queue	: false
						}
					});
				} catch(err) {
	
				}
				return false;
			});
	
	
			winDow.bind('resize', function(){
				var selector = $filter.find('li.active').attr('data-filter');

				$container.isotope({ 
					filter	: selector,
					animationOptions: {
						duration: 1000,
						easing	: 'linear',
						queue	: false
					}
				});
			});
	
	
			var filterItemA	= $('.sortable-masonry .filter-btns li');
	
			filterItemA.on('click', function(){
				var $this = $(this);
				if ( !$this.hasClass('active')) {
					filterItemA.removeClass('active');
					$this.addClass('active');
				}
			});
		}
	}
	
	enableMasonry();
	
	
	
	// 18. Gallery With Filters List
	if($('.filter-list').length){
		$('.filter-list').mixItUp({});
	}
	
	
	// 19. Date Picker
	  if($('.datepicker').length){
		$( ".datepicker" ).datepicker();
	  }
	
	
	// 20. Scroll to a Specific Div
	if($('.scroll-to-target').length){
		$(".scroll-to-target").on('click', function() {
			var HeaderHeight = $('.header-lower').height();
			var target = $(this).attr('data-target');
		   // animate
		   $('html, body').animate({
			   scrollTop: $(target).offset().top - HeaderHeight
			 }, 1000);
	
		});
	}
	
	
	// 21. Click Search Icon and Open Search Field	
	var $srcicon = $('.hvr-src a'),
		$srcfield = $('#search');
	$srcicon.on('click', function(event){
		event.preventDefault();
		$srcfield.addClass('visible');
		event.stopPropagation();
	});
	
	$('.src-close').on('click', function(){
		$srcfield.removeClass('visible');
	});
	
	$srcfield.on('click', function(event){
		event.stopPropagation();
	});
	
	$window.on('click', function(e){
		$srcfield.removeClass('visible');
	});
	
	
	// 22. Contact Form Validation
	if($contact.length){
		$contact.validate({  //#contact-form contact form id
			rules: {
				name: {
					required: true    // Field name here
				},
				email: {
					required: true, // Field name here
					email: true
				},
				subject: {
					required: true
				},
				message: {
					required: true
				}
			},
			
			messages: {
                name: "Please enter your Name", //Write here your error message that you want to show in contact form
                email: "Please enter valid Email", //Write here your error message that you want to show in contact form
                subject: "Please enter your Subject", //Write here your error message that you want to show in contact form
				message: "Please write your Message" //Write here your error message that you want to show in contact form
            },

            submitHandler: function (form) {
                $('#send').attr({'disabled' : 'true', 'value' : 'Sending...' });
                $.ajax({
                    type: "POST",
                    url: "email.php",
                    data: $(form).serialize(),
                    success: function () {
                        $('#send').removeAttr('disabled').attr('value', 'Send');
                        $( "#success").slideDown( "slow" );
                        setTimeout(function() {
                        $( "#success").slideUp( "slow" );
                        }, 5000);
                        form.reset();
                    },
                    error: function() {
                        $('#send').removeAttr('disabled').attr('value', 'Send');
                        $( "#error").slideDown( "slow" );
                        setTimeout(function() {
                        $( "#error").slideUp( "slow" );
                        }, 5000);
                    }
                });
                return false; // required to block normal submit since you used ajax
            }

		});
	} 

	
	
	// 23. Elements Animation
	if($('.wow').length){
		var wow = new WOW(
		  {
			boxClass:     'wow',      // animated element css class (default is wow)
			animateClass: 'animated', // animation css class (default is animated)
			offset:       0,          // distance to the element when triggering the animation (default is 0)
			mobile:       true,       // trigger animations on mobile devices (default is true)
			live:         true       // act on asynchronously loaded content (default is true)
		  }
		);
		wow.init();
	}

   // 24. When document is Scrollig, do
	
	$window.on('scroll', function() {
		headerStyle();
		factCounter();
	});
	

   // 25. When document is loading, do
	
	$window.on('load', function() {
		handlePreloader();
		enableMasonry();
	});
	
	
	// 26. Youtube and Vimeo video popup control
	jQuery(function(){
		jQuery("a.video-popup").YouTubePopUp();
		//jQuery("a.video-popup").YouTubePopUp( { autoplay: 0 } ); // Disable autoplay
	});
	

	

})(window.jQuery);