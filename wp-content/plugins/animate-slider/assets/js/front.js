(function($){
	// USE STRICT
	"use strict";

	$(window).load(function(){

		if($('.as-slider').length > 0 ) {

			$('.as-slider').each(function(){

				var cap = $(this).find('.as-slide-caption');
				var img = $(this).find('.as-slide-image');

				cap.each(function(){
					var cap_h = $(this).outerHeight();
					var cap_more_h = 0;
					console.log($(window).width());
					var w_width = ( $(window).width() <= 320 ) ? true : false; 
					var w_case = ( typeof Modernizr == 'undefined' ) ? $(window).width: Modernizr.mq('only screen and (max-width: 320px)');
					if($(this).find('.as-slide-more.default').length > 0 && !w_case){
						cap_more_h += 49;
					}
					$(this).css({
						'top' : '50%',
						'margin-top' : -((cap_h - cap_more_h) / 2) 
					});
				});

				img.each(function(){
					var img_h = $(this).parents('.as-slide-item').outerHeight();
					$(this).css({
						'line-height': img_h + 'px'
					})
				});
				
				$(this).bxSlider({
					slideSelector: '.as-slide-item',
					pager: $(this).data('pager') ? $(this).data('pager') : false,
					controls: $(this).data('controls') ? $(this).data('controls') : false,
					nextSelector: "#" + $(this).parent().find('.as-slider-control').attr('id'),
					prevSelector: "#" + $(this).parent().find('.as-slider-control').attr('id'),
					auto: $(this).data('auto') ? $(this).data('auto') : false,
					autoStart: true,
					pause: $(this).data('pause') ? $(this).data('pause') : 5000,
					speed: $(this).data('duration') ? $(this).data('duration') : 5000,
					mode: $(this).data('mode') == 'fade' ? "fade": "horizontal" ,
					nextText: '<i class="as-icon as-icon-chevron-right"></i>',
					prevText: '<i class="as-icon as-icon-chevron-left"></i>',
					adaptiveHeight: true,
					onSliderLoad: function( index ) {
						if( this.mode != 'fade') {
							index = index + 1;
						}

						$(this.slideSelector).each(function( i ){
							if( i != index ) {
							var a = $(this).find('.as-slide-more'),
							    b = $(this).find('.as-slide-title'),
							    c = $(this).find('.as-slide-content'),
							    d = $(this).find('.as-slide-image'),
							    e = a.data('in-anim'),
							    f = a.data('out-anim'),
							    h = b.data('in-anim'),
							    i = b.data('out-anim'),
							    j = c.data('in-anim'),
							    k = c.data('out-anim'),
							    l = d.data('in-anim'),
							    m = d.data('out-anim');
							    a.removeClass(e).addClass(f).hide();
							    b.removeClass(h).addClass(i).hide();
							    c.removeClass(j).addClass(k).hide();
							    d.removeClass(l).addClass(m).hide().find('img').hide();
							}
						});

						var img = $(this.slideSelector).eq(index).find('.as-slide-image');
						img.each(function(){
							var img_h = $(this).parents('.as-slide-item').outerHeight();
							$(this).css({
								'line-height': img_h + 'px'
							})
						});

						$(this.slideSelector).eq(index).delay(80).queue(function(){
							$(this).addClass('active');
							$(this).dequeue();
						});
						
						animClass( this.slideSelector, index, null, "start");
					},
					onSlideBefore: function($elem, oldI, newI){
						if( this.mode != 'fade') {
							newI = newI + 1;
							oldI = oldI + 1;
						}

						var img = $(this.slideSelector).eq(newI).find('.as-slide-image');
						img.each(function(){
							var img_h = $(this).parents('.as-slide-item').outerHeight();
							$(this).css({
								'line-height': img_h + 'px'
							})
						});
						
						$(this.slideSelector).eq(oldI).delay(80).queue(function(){
							$(this).removeClass('active');
							$(this).dequeue();
						});
						animClass( this.slideSelector, oldI, newI, "end");
					},
					onSlideAfter: function($elem, oldI, newI){
						if( this.mode != 'fade') {
							newI = newI + 1;
							oldI = oldI + 1;
						}

						var img = $(this.slideSelector).eq(newI).find('.as-slide-image');
						img.each(function(){
							var img_h = $(this).parents('.as-slide-item').outerHeight();
							$(this).css({
								'line-height': img_h + 'px'
							})
						});

						$(this.slideSelector).eq(newI).delay(80).queue(function(){
							$(this).addClass('active');
							$(this).dequeue();
						});
						animClass( this.slideSelector, newI, oldI, "start");
					}
				});
			});
		}
	});

	function animClass(selector, index, index2, state) {

		var b = $(selector).eq(index).find('.as-slide-more'),
			t = $(selector).eq(index).find('.as-slide-title'),
			c = $(selector).eq(index).find('.as-slide-content'),
			i = $(selector).eq(index).find('.as-slide-image'),
			ti = t.data('in-anim'),
			to = t.data('out-anim'),
			ci = c.data('in-anim'),
			co = c.data('out-anim'),
			bi = b.data('in-anim'),
			bo = b.data('out-anim'),
			ii = i.data('in-anim'),
			io = i.data('out-anim');

		if( state == "start") {

			if( "undefined" !== typeof ti && t.length > 0 ) {
				t.removeClass(to).show().addClass(ti);
			}

			if( "undefined" !== typeof ci && c.length > 0 ) {
				c.removeClass(co).show().addClass(ci);
			}

			if( "undefined" !== typeof bi && b.length > 0 ) {
				b.removeClass(bo).show().addClass(bi);
			}

			if( "undefined" !== typeof ii && i.length > 0 ) {
				i.removeClass(io).show().find('img').show().end().addClass(ii);
			}

		} else {

			if( index2 !== null ) {
				var t2 = $(selector).eq(index2).find('.as-slide-title'),
					b2 = $(selector).eq(index2).find('.as-slide-more'),
					c2 = $(selector).eq(index2).find('.as-slide-content'),
					i2 = $(selector).eq(index2).find('.as-slide-image');

					t2.hide();b2.hide();c2.hide();i2.hide().find('img').hide();
			}
			if( "undefined" !== typeof to && t.length > 0 ) {

				t.removeClass(ti).addClass(to);
			}

			if( "undefined" !== typeof co && c.length > 0 ) {
				c.removeClass(ci).addClass(co);
			}

			if( "undefined" !== typeof bo && b.length > 0 ) {
				b.removeClass(bi).addClass(bo);
			}

			if( "undefined" !== typeof io && i.length > 0 ) {
				i.removeClass(ii).addClass(io);
			}
		}
	}

})(jQuery);