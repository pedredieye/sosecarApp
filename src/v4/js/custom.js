jQuery(function ($) {
    'use strict';

	// Mean Menu
	$('.mean-menu').meanmenu({
		meanScreenWidth: "992"
	}); 

	// Others Option For Responsive JS
	$(".others-option-for-responsive .dot-menu").on("click", function(){
		$(".others-option-for-responsive .container .container").toggleClass("active");
	});

	// Preloader Area
	$(window).on('load', function() {
		$('.preloader').addClass('preloader-deactivate');
	});

	// Sticky, Go To Top JS
	$(window).on('scroll', function() { 
		// Header Sticky JS 
		if ($(this).scrollTop() >150){   
			$('.navbar-area').addClass("is-sticky");
		}
		else{
			$('.navbar-area').removeClass("is-sticky"); 
		};
	});

	// Hero Auto Slider
	$('.hero-auto-slider').owlCarousel({
		loop: true,
		margin: 0,
		nav: false, 
		dots: false,
		autoplay: true,
		smartSpeed: 2000,
		autoplayHoverPause: true,
		items:1,
	});
	// Hero Auto Slider Two
	$('.hero-auto-slider-two').owlCarousel({
		loop: true,
		margin: 0,
		nav: false,
		dots: false,
		autoplay: true,
		smartSpeed: 2000,
		autoplayHoverPause: true,
		items:1,
	});

	// Event Photo Slider
	$('.event-photo-slider').owlCarousel({
		loop: true,
		margin: 20,
		nav: true,
		dots: false,
		center: true,
		autoplay: true,
		smartSpeed: 2000,
		autoplayHoverPause: true,
		navText: [
			"<i class='bx bx-chevron-left'></i>",
			"<i class='bx bx-chevron-right'></i>"
		],
		responsivee:{
			0:{
				items:1
			},
			570:{
				items:1
			},
			768:{
				items:2
			},
			992:{
				items:2
			},
			1200:{
				items:2
			}
		}
	});

	// Blog Style Three Slider
	$('.blog-style-three-slider').owlCarousel({
		loop: true,
		margin: 20,
		nav: true,
		dots: false,
		center: true,
		autoplay: true,
		smartSpeed: 2000,
		autoplayHoverPause: true,
		navText: [
			"<i class='bx bx-chevron-left'></i>",
			"<i class='bx bx-chevron-right'></i>"
		],
		responsivee:{
			0:{
				items:1
			},
			570:{
				items:1
			},
			768:{
				items:2
			},
			992:{
				items:2
			},
			1200:{
				items:2
			},
			1500:{
				items:3
			}
		} 
	});

	// On Hover
	try {
        $(".blog-area .blog-hover-content ul li:nth-child(2) .single-hover-content").on("mouseenter", function() {
            $('.blog-area .blog-image img:nth-child(2)').addClass('active');
        });
        $(".blog-area .blog-hover-content ul li:nth-child(2) .single-hover-content").on("mouseleave", function() {
            $('.blog-area .blog-image img:nth-child(2)').removeClass('active');
        });
        $(".blog-area .blog-hover-content ul li:nth-child(3) .single-hover-content").on("mouseenter", function() {
            $('.blog-area .blog-image img:nth-child(3)').addClass('active');
        });
        $(".blog-area .blog-hover-content ul li:nth-child(3) .single-hover-content").on("mouseleave", function() {
            $('.blog-area .blog-image img:nth-child(3)').removeClass('active');
        });
    } catch (err) {}

	// Count Time JS
	function makeTimer() {
		var endTime = new Date("november  30, 2025 17:00:00 PDT");			
		var endTime = (Date.parse(endTime)) / 1000;
		var now = new Date();
		var now = (Date.parse(now) / 1000);
		var timeLeft = endTime - now;
		var days = Math.floor(timeLeft / 86400); 
		var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
		var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600 )) / 60);
		var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));
		if (hours < "10") { hours = "0" + hours; }
		if (minutes < "10") { minutes = "0" + minutes; }
		if (seconds < "10") { seconds = "0" + seconds; }
		$("#days").html(days + "<span>Days</span>");
		$("#hours").html(hours + "<span>Hours</span>");
		$("#minutes").html(minutes + "<span>Minutes</span>");
		$("#seconds").html(seconds + "<span>Seconds</span>");
	} 
	setInterval(function() { makeTimer(); }, 300); 

	// Odometer JS 
	$('.odometer').appear(function(e) {
		var odo = $(".odometer");
		odo.each(function() {
			var countNumber = $(this).attr("data-count");
			$(this).html(countNumber);
		});
	});  

	// scrollCue Animate
	scrollCue.init();

	// Live Auctions Countdown
	$('.live-auctions-countdown').downCount({
		date: '12/11/2023 08:00:00',
		offset: -2
	}, function () {
		//alert('WOOT WOOT, done!');
	});

	// Pricing Switcher
	let el = document.getElementById('filt-monthly');
	if(el){
		let e = document.getElementById("filt-monthly"),
		d = document.getElementById("filt-yearly"),
		t = document.getElementById("switcher"),
		m = document.getElementById("monthly"),
		y = document.getElementById("yearly");
		e.addEventListener("click", function(){
			t.checked = false;
			e.classList.add("toggler--is-active");
			d.classList.remove("toggler--is-active");
			m.classList.remove("hide");
			y.classList.add("hide");
		});
		d.addEventListener("click", function(){
			t.checked = true;
			d.classList.add("toggler--is-active");
			e.classList.remove("toggler--is-active");
			m.classList.add("hide");
			y.classList.remove("hide");
		});
		t.addEventListener("click", function(){
			d.classList.toggle("toggler--is-active");
			e.classList.toggle("toggler--is-active");
			m.classList.toggle("hide");
			y.classList.toggle("hide");
		});
	}

	// Go to Top
	$(function(){
		// Scroll Event
		$(window).on('scroll', function(){
			var scrolled = $(window).scrollTop();
			if (scrolled > 600) $('.go-top').addClass('active');
			if (scrolled < 600) $('.go-top').removeClass('active');
		});  
		// Click Event
		$('.go-top').on('click', function() {
			$("html, body").animate({ scrollTop: "0" },  500);
		});
	}); 

}(jQuery));


// function to set a given theme/color-scheme
function setTheme(themeName) {
    localStorage.setItem('nestu_theme', themeName);
    document.documentElement.className = themeName;
}

localStorage.setItem('nestu_theme',"theme-light");

// function to toggle between light and dark theme
function toggleTheme() {
    if (localStorage.getItem('nestu_theme') === 'theme-light') {
        setTheme('theme-dark');
    } else {
        setTheme('theme-light');
    }
}

// Immediately invoked function to set the theme on initial load
(function () {
    if (localStorage.getItem('nestu_theme') === 'theme-light') {
        setTheme('theme-light');
        //document.getElementById('slider').checked = false;
    } else {
        setTheme('theme-dark');
        //document.getElementById('slider').checked = true;
    }
})();