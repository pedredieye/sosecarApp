(function ($) {
	"use strict";

	/*------------------------------------
			Preloader
		--------------------------------------*/
	$(window).on('load', function () {
		$('#preloader').delay(350).fadeOut('slow');
		$('body').delay(350).css({ 'overflow': 'visible' });
	});


	/*------------------------------------
		Mobile Menu
	--------------------------------------*/

	$('#mobile-menu-active').metisMenu();

	$('#mobile-menu-active .has-dropdown > a').on('click', function (e) {
		e.preventDefault();
	});

	$(".hamburger-menu > a").on("click", function (e) {
		e.preventDefault();
		$(".slide-bar").toggleClass("show");
		$("body").addClass("on-side");
		$('.body-overlay').addClass('active');
		$(this).addClass('active');
	});

	$(".close-mobile-menu > a").on("click", function (e) {
		e.preventDefault();
		$(".slide-bar").removeClass("show");
		$("body").removeClass("on-side");
		$('.body-overlay').removeClass('active');
		$('.hamburger-menu > a').removeClass('active');
	});

	$('.body-overlay').on('click', function () {
		$(this).removeClass('active');
		$(".slide-bar").removeClass("show");
		$("body").removeClass("on-side");
		$('.hamburger-menu > a').removeClass('active');
	});

	/* Search
		-------------------------------------------------------*/
	var $searchWrap = $('.search-wrap');
	var $navSearch = $('.nav-search');
	var $searchClose = $('#search-close');

	$('.search-trigger').on('click', function (e) {
		e.preventDefault();
		$searchWrap.animate({ opacity: 'toggle' }, 500);
		$navSearch.add($searchClose).addClass("open");
	});

	$('.search-close').on('click', function (e) {
		e.preventDefault();
		$searchWrap.animate({ opacity: 'toggle' }, 500);
		$navSearch.add($searchClose).removeClass("open");
	});

	function closeSearch() {
		$searchWrap.fadeOut(200);
		$navSearch.add($searchClose).removeClass("open");
	}

	$(document.body).on('click', function (e) {
		closeSearch();
	});

	$(".search-trigger, .main-search-input").on('click', function (e) {
		e.stopPropagation();
	});


	//sticky-menu
	$(window).on('scroll', function () {
		var scroll = $(window).scrollTop();
		if (scroll < 200) {
			$(".main-header-area").removeClass("sticky");
		} else {
			$(".main-header-area").addClass("sticky");
		}
	});




	// mainSlider
	function mainSlider() {
		var BasicSlider = $('.slider-active');
		BasicSlider.on('init', function (e, slick) {
			var $firstAnimatingElements = $('.single-slider:first-child').find('[data-animation]');
			doAnimations($firstAnimatingElements);
		});
		BasicSlider.on('beforeChange', function (e, slick, currentSlide, nextSlide) {
			var $animatingElements = $('.single-slider[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
			doAnimations($animatingElements);
		});
		BasicSlider.slick({
			infinite: true,
			autoplay: true,
			autoplaySpeed: 3000,
			dots: false,
			fade: true,
			speed: 1000,
			arrows: false,
			cssEase: 'linear',
			prevArrow: '<button type="button" class="slick-prev"><i class="far fa-chevron-left"></i></button>',
			nextArrow: '<button type="button" class="slick-next"><i class="far fa-chevron-right"></i></button>',
			responsive: [
				{
					breakpoint: 850,
					settings: { dots: false, arrows: false }
				}
			]
		});

		function doAnimations(elements) {
			var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
			elements.each(function () {
				var $this = $(this);
				var $animationDelay = $this.data('delay');
				var $animationType = 'animated ' + $this.data('animation');
				$this.css({
					'animation-delay': $animationDelay,
					'-webkit-animation-delay': $animationDelay
				});
				$this.addClass($animationType).one(animationEndEvents, function () {
					$this.removeClass($animationType);
				});
			});
		}
	}
	mainSlider();



	//feedback-active
	$('.feedback-active').owlCarousel({
		loop: true,
		margin: 30,
		items: 2,
		autoplay: true,
		autoplaySpeed: 2000,
		navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
		nav: false,
		dots: true,
		stagePadding: 130,
		responsive: {
			0: {
				items: 1,
				stagePadding: 0,
				margin: 0,
			},
			767: {
				items: 2,
				stagePadding: 0,
			},
			992: {
				items: 2,
				stagePadding: 0,
			},
			1201: {
				items: 2
			}

		}
	})


	// feedback-active2
	$('.feedback-active2').owlCarousel({
		loop: true,
		margin: 55,
		items: 1,
		autoplay: true,
		autoplaySpeed: 2000,
		navText: ['<img src="assets/img/icon/left.svg" alt="" />', '<img src="assets/img/icon/right.svg" alt="" />'],
		nav: true,
		dots: false,
		stagePadding: 385,
		responsive: {
			0: {
				items: 1,
				stagePadding: 0,
				margin: 0,
				nav: false,
			},
			767: {
				items: 1,
				stagePadding: 0,
			},
			992: {
				items: 1,
				stagePadding: 0,
			},
			1366: {
				items: 1,
				stagePadding: 280,
			},
			1420: {
				items: 1
			}

		}
	})

	//feedback-active3
	$('.feedback-active3').slick({
		infinite: true,
		arrows: true,
		autoplay: true,
		centerMode: true,
		centerPadding: '337px',
		autoplaySpeed: 3000,
		slidesToShow: 1,
		slidesToScroll: 2,
		prevArrow: '<button type="button" class="slick-prev"><img src="assets/img/icon/left.svg" alt="" /></button>',
		nextArrow: '<button type="button" class="slick-next"><img src="assets/img/icon/right.svg" alt="" /></button>',
		responsive: [
			{
				breakpoint: 960,
				settings: {
					slidesToShow: 1,
					centerPadding: '0',
					arrows: false,
				}
			},
			{
				breakpoint: 1201,
				settings: {
					centerPadding: '150px'
				}
			},
			{
				breakpoint: 1400,
				settings: {
					centerPadding: '250px'
				}
			},
		]
	});


	// feedback-active3s
	$('.feedback-active3s').owlCarousel({
		center: true,
		loop: true,
		margin: 55,
		items: 1,
		autoplay: true,
		autoplaySpeed: 2000,
		navText: ['<img src="assets/img/icon/left.svg" alt="" />', '<img src="assets/img/icon/right.svg" alt="" />'],
		nav: true,
		dots: false,
		stagePadding: 415,
		responsive: {
			0: {
				items: 1,
				stagePadding: 0,
				margin: 0,
			},
			767: {
				items: 2,
				stagePadding: 0,
			},
			992: {
				items: 1,
				stagePadding: 0,
			},
			1024: {
				items: 1
			}

		}
	})


	// team-active
	$('.team-active').owlCarousel({
		loop: true,
		margin: 30,
		items: 4,
		autoplay: true,
		autoplaySpeed: 2000,
		navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
		nav: false,
		dots: true,
		responsive: {
			0: {
				items: 1,
				margin: 0,
			},
			767: {
				items: 2,
			},
			992: {
				items: 3,
			},
			1366: {
				items: 4
			}

		}
	})



	// feedback-active4
	$('.feedback-active4').owlCarousel({
		center: true,
		loop: true,
		items: 1,
		autoplay: true,
		autoplaySpeed: 2000,
		navText: ['<i class="flaticon-left-arrow"></i>', '<i class="flaticon-right-arrow"></i>'],
		nav: true,
		dots: false,
		responsive: {
			0: {
				items: 1,
				margin: 0,
			},
			767: {
				items: 1,
			},
			992: {
				items: 1,
			},
			1024: {
				items: 1,
				nav: true,
			}

		}
	})

	// -------------------- Remove Placeholder When Focus Or Click
	$("input,textarea").each(function () {
		$(this).data('holder', $(this).attr('placeholder'));
		$(this).on('focusin', function () {
			$(this).attr('placeholder', '');
		});
		$(this).on('focusout', function () {
			$(this).attr('placeholder', $(this).data('holder'));
		});
	});


	/* magnificPopup img view */
	$('.popup-image').magnificPopup({
		type: 'image',
		gallery: {
			enabled: true
		}
	});

	/* magnificPopup video view */
	$('.popup-video').magnificPopup({
		type: 'iframe'
	});

	// active-class
	$('.do-box, .s-services').on('mouseenter', function () {
		$(this).addClass('active').parent().siblings().find('.do-box, .s-services').removeClass('active');
	})

	// isotop
	$('.grid').imagesLoaded(function () {
		// init Isotope
		var $grid = $('.grid').isotope({
			itemSelector: '.grid-item',
			percentPosition: true,
			masonry: {
				// use outer width of grid-sizer for columnWidth
				columnWidth: 1,
				gutter: 0
			}
		});
		// filter items on button click
		$('.portfolio-menu').on('click', 'button', function () {
			var filterValue = $(this).attr('data-filter');
			$grid.isotope({ filter: filterValue });
		});
	});

	//for menu active class
	$('.portfolio-menu button').on('click', function (event) {
		$(this).siblings('.active').removeClass('active');
		$(this).addClass('active');
		event.preventDefault();
	});

	//counter
	$('.counter').counterUp({
		delay: 10,
		time: 3000
	});



	// scrollToTop
	$.scrollUp({
		scrollName: 'scrollUp', // Element ID
		topDistance: '300', // Distance from top before showing element (px)
		topSpeed: 500, // Speed back to top (ms)
		animation: 'fade', // Fade, slide, none
		animationInSpeed: 300, // Animation in speed (ms)
		animationOutSpeed: 300, // Animation out speed (ms)
		scrollText: '<i class="fas fa-chevron-double-up"></i>', // Text for element
		activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
	});
	// wow animation - start
	// --------------------------------------------------
	// function wowAnimation() {
	// 	new WOW({
	// 		offset: 100,
	// 		mobile: true
	// 	}).init()
	// }
	// wowAnimation();




	//progress-bar
	$('.chart1').easyPieChart({
		barColor: '#e5aa8f',
		trackColor: '#fcf6f4',
		lineWidth: 10,
		lineCap: 'square',
		scaleColor: 0,
		scaleLength: 0,
		size: 120,
		animate: {
			duration: 2000,
			enabled: true,
		},
	});


	//nice-select
	$(document).ready(function () {
		$('select').niceSelect();
	});

	

})(jQuery);
