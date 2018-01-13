var app = (function ($) {

	var self = {
		init: function () {
			$('.btn-navbar').on('click', app.menuToggle);
		},
		menuToggle: function () {
			$('.main-nav').toggleClass('open');
		},
		plugins: {
			flexslider: function (el, settings) {
				$(el).flexslider(settings);
			}
		}
	};

	return self;

}(jQuery));






