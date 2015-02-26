jQuery(document).ready(function($) {
	
	$(function () {
		var slidespeed1 =  $(".tf-notification-sc ul.slide").attr("data-speed");
		var list_slideshow = $('.tf-notification-sc ul.slide').bxSlider({
		  auto: true,
		  autoControls: false,
		  pager: false,
		  controls: false,
		  pause: slidespeed1
		});
	});
	
	$(function () {
		var slidespeed2 =  $(".tf-notification-sc ul.fade").attr("data-speed");
		var list_slideshow = $('.tf-notification-sc ul.fade').bxSlider({
		  auto: true,
		  autoControls: false,
		  pager: false,
		  controls: false,
		  pause: slidespeed2,
		  mode: "fade"
		});
	});
	
	$(function () {
		var list_ticker = $('.tf-notification-sc ul.ticker').bxSlider({
		  auto: true,
		  autoControls: false,
		  pager: false,
		  controls: false,
		  speed: 10000,
		  ticker: true,
		  tickerHover: true,
		  useCSS: false
		});
	});

});