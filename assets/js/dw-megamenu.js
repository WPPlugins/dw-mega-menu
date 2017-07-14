jQuery(document).ready(function($) {
	var window_width = $(window).width();
	var nav = $('nav');
	var menu_width = nav.find('.dw-mega-menu-wrap').width();
	if( menu_width > 0 ) {
		var offset_left = nav.find('.dw-mega-menu-wrap').parent().offset().left;

		if( menu_width + offset_left > window_width ) {
			$('nav').find('.dw-mega-menu-wrap').css( 'left', -( menu_width + offset_left - window_width ) - 20 );
		}
	}

	$( '.dw-megamenu-tab-child > a' ).on( 'mouseenter', function() {
		$( '.dw-megamenu-tab-child > ul' ).css( 'display', 'none' );
		$(this).next().css('display','block');
	});

	$( '.dw-mega-menu-hover' ).on( 'mouseenter', function() {
		var t = $(this);
		if ( $(this).hasClass( 'dw-mega-menu-tab' ) ) {
			$(this).parent().find( '> li.dw-mega-menu-hover' ).removeClass('dw-mega-menu-tab-open');
			$(this).addClass('dw-mega-menu-tab-open');
		} else {
			$(this).parent().find( '> li.dw-mega-menu-hover' ).removeClass('dw-mega-menu-open');
			$(this).addClass( 'dw-mega-menu-open' );
		}
		
	});

	$( '.dw-mega-menu-hover' ).on( 'mouseleave', function() {
		$(this).parent().find( '> li.dw-mega-menu-hover' ).removeClass('dw-mega-menu-open');
	});

	$( '.dw-mega-menu-tab:first-child' ).addClass( 'dw-mega-menu-tab-open' );

	var height = [];

	$('.dw-mega-menu-tabs').each(function() {
		$(this).find( '.dw-mega-menu-tab' ).each(function() {
			var t = $(this);
			var length = $(this).html().length;
			var indexof = $(this).html().indexOf( '</a>' );
			var html = $(this).html().slice( 0, indexof );
			var html2 = $(this).html().slice( indexof + 4, length );
			var return_html = html + '</a><div class="dw-mega-menu-tab-content">' + html2 + '</div>';

			$(this).html( return_html );
		});
	});

	function resize_megamenu_tab() {
		var height = [];

		$('.dw-mega-menu-tabs').each(function() {
			height = [];

			var height_parent = $(this).parent().outerHeight();
			$(this).find( '.dw-mega-menu-tab' ).each(function() {
				var div_height = $(this).find( '.dw-mega-menu-tab-content' ).outerHeight();

				height.push( div_height ); 
			});

			var max = height[0];

		
			$.each( height, function( index, number ) {
				if ( max < number ) max = number;
			});

			if ( height_parent > max ) max = height_parent;

			$(this).css( 'min-height', max ).find( '.dw-mega-menu-tab-content' ).css( 'min-height', max );
		});
	}

	$(window).load(function() {
		resize_megamenu_tab();
	});

	// conflict with lazyload plugin
	$( '.dw-mega-menu-hover' ).each(function() {
		$(this).find('img[data-lazy-src]').each(function(){
   			lazy_load_image(this);
		});
	});

	function lazy_load_image(img) {
		var img = $(img), src = img.attr('data-lazy-src');
		img.unbind('scrollin' ).hide().removeAttr('data-lazy-src').attr('data-lazy-loaded', 'true');
		img.attr( 'src', src );
		img.fadeIn();
	}
});