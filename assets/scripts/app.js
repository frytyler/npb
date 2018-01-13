var app = (function ($) {

	var self = {
		init: function () {
			$('.btn-navbar').on('click', app.menuToggle);
			self.iePlaceholderFix();
		},
		menuToggle: function () {
			$('.main-nav').toggleClass('open');
		},
		plugins: {
			flexslider: function (el, settings) {
				$(el).flexslider(settings);
			}
		},
		iePlaceholderFix: function () {
			jQuery
				.each( jQuery( "[type=\"text\"]" ), function( k, v ) {
					jQuery( v ).val( jQuery( v ).attr( "placeholder" ) );
					jQuery( v )
						.on( "click", function( e ) {
							e.preventDefault( );

							if ( jQuery( v ).val( ) == jQuery( v ).attr( "placeholder" ) )
								jQuery( v ).val( "" );
						} )
					;
					jQuery( v )
						.on( "blur", function( e ) {
							e.preventDefault( );

							if ( jQuery( v ).val( ) == "" )
								jQuery( v ).val( jQuery( v ).attr( "placeholder" ) );
						} )
					;
				} )
			;
		}
	};

	return self;

}(jQuery));




