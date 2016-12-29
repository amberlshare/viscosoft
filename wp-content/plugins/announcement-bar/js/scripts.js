(function($){

	!function(e){var t,n,r,s=e.event,i={_:0},h=0;t=s.special.throttledresize={setup:function(){e(this).on("resize",t.handler)},teardown:function(){e(this).off("resize",t.handler)},handler:function(o,a){var l=this,d=arguments;n=!0,r||(setInterval(function(){h++,(h>t.threshold&&n||a)&&(o.type="throttledresize",s.dispatch.apply(l,d),n=!1,h=0),h>9&&(e(i).stop(),r=!1,h=0)},30),r=!0)},threshold:0}}(jQuery);

	// Functions
	function announcement_bar_set_body_margin() {
		$('.themify_announcement.bar').each(function(){
			var $this = $(this),
				h = $this.outerHeight(true);
			$margin_top_to_bar_height = $( announcementBar.margin_top_to_bar_height );

			// set body margin and classes
			if($this.hasClass('announcement_bottom')){
				if ( $this.find('.announcement_list').length > 0 ) {
					$margin_top_to_bar_height.css('marginBottom', h);
				}
				$('body').addClass('announcement-bottom');
			} else {
				if ( $this.find('.announcement_list').length > 0 && $margin_top_to_bar_height != 'null' ) {
					$margin_top_to_bar_height.css('marginTop', h);
				}
				$('body').addClass('announcement-top');
			}

			$( 'body' ).trigger( 'announcement_bar_position', [ $this ] );

			if( $this.hasClass( 'fixed' ) ) {
				$( 'body' ).addClass( 'fixed-announcement-on' );
			}

			if ( $('.themify_announcement.bar').length > 0 && $('.themify_announcement.bar').is(':visible') ) {
				$('body').addClass('announcement-bar-showing');
			}
		});
	}

	function announToggleBodyMargin(state) {
		var h = $('.themify_announcement.bar').outerHeight(true),
			style = $('.themify_announcement.bar').hasClass('announcement_bottom') ? {'marginBottom': h} : {'marginTop': h},
			styleClose = $('.themify_announcement.bar').hasClass('announcement_bottom') ? {'marginBottom': ''} : {'marginTop': ''};
		
		if(state == 'close'){
			$(announcementBar.margin_top_to_bar_height).addClass('announcement-bar-collapsed').animate(styleClose, 400, function(){
				$(this).removeClass('announcement-bar-showing');
			});
		} else {
			$(announcementBar.margin_top_to_bar_height).removeClass('announcement-bar-collapsed').animate(style, 400, function(){
				$(this).addClass('announcement-bar-showing');
			});
		}
	}

	function announSetCookie(name,value) {
		document.cookie = name+"="+value+"; path=/";
	}
	function announGetCookie(name) {
		name = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i < ca.length; i++) {
			var c = ca[i];
			while (' ' == c.charAt(0)) c = c.substring(1,c.length);
			if (0 == c.indexOf(name)) return c.substring(name.length,c.length);	}
		return null;
	}

	// Remember State Close Btn
	function announcement_remember_state_close(element_id){
		var $this = $('#'+element_id).find('.announcement_list'),
			$container = $('#'+element_id);
		if(typeof $this.data('remember-close') == 'undefined' || $this.data('remember-close') == 0){
			return;
		}

		if( typeof(announGetCookie('announ-toggle-close')) != 'undefined' && announGetCookie('announ-toggle-close') !== null && announGetCookie('announ-toggle-close') == 'collapsed' ){
			announToggleBodyMargin( 'close' );
			$container.addClass('collapsed')
			.find('.announcement_container:not(.close-container)').hide();

			// Toggle Button
			var closeBtnHeight = $container.find('.toggle-close').height();
			if($container.hasClass('announcement_bottom') ){
				var anime = {top: '-' + closeBtnHeight + 'px'};
			} else {
				var anime = {bottom: '-' + closeBtnHeight + 'px'};
			}
			$container.find('.toggle-close').show().animate(anime, 400);
		}
	}

	// Create Carousel function
	function announcement_bar_create_carousel( $context ) {
		$context = $context || $('body');
		$('.announcement_list', $context).each(function(){

			// Dont initialize when it already init
			if ( $(this).closest('.announcement_container').find('.announCarouFredSel_wrapper').length > 0 ) {
				return;
			}

			// Remove announcement bar if the items doesn't founded
			if ( $(this).find('li').length == 0 ) {
				$(this).closest('.themify_announcement').remove();
				return;
			}

			var $this = $(this),
				$secondOpts = {},
				$opts = {
					responsive: true,
					circular: true,
					infinite: true,
					items: {
						visible: { min: 1, max: $this.data('visible') },
						width: 222
					},
					swipe: true,
					onCreate: function(items){
						$this.closest('.themify_announcement').fadeIn().end()
						.trigger('updateSizes').trigger('configuration', ['reInit', true]);
						announcement_bar_set_body_margin();
						announcement_remember_state_close($this.data('id'));

						$(window).on('throttledresize', function( event ) {
							var heights = [];
							$.each(items.items, function(){
								heights.push( $(this).outerHeight() );
							});
							$this.closest('.announcement_container').find('.announcement_list,.announCarouFredSel_wrapper').outerHeight( Math.max.apply( Math, heights ) );
						});
					}
				};

			// Auto
			if(parseInt($this.data('auto-scroll')) > 0) {
				$opts.auto = {
					play: true,
					timeoutDuration: parseInt($this.data('auto-scroll') * 1000),
					pauseOnHover: 'resume'
				};
			}
			else if($this.data('effect') !== 'continuously' && ( typeof $this.data('auto-scroll') !== 'undefined' || parseInt($this.data('auto-scroll')) == 0 )  ){
				$opts.auto = false;
			}

			// Scroll
			if($this.data('effect') == 'continuously'){
				if(typeof $opts.auto !== 'undefined'){
					delete $opts.auto;
				}
				$opts.align = false;
				$opts.scroll = {
					delay: 1000,
					easing: 'linear',
					items: $this.data('scroll'),
					duration: 0.07,
					timeoutDuration: 0,
					pauseOnHover: 'immediate'
				};
			} else {
				$opts.scroll = {
					items: $this.data('scroll'),
					pauseOnHover: 'resume',
					duration: parseInt($this.data('speed') * 1000),
					fx: $this.data('effect')
				};
			}

			if($this.data('arrow') == 'yes') {
				$opts.prev = '#' + $this.data('id') + ' .carousel-prev';
				$opts.next = '#' + $this.data('id') + ' .carousel-next';
			}
			if($this.data('pagination') == 'yes') {
				$opts.pagination = { container: '#' + $this.data('id') + ' .carousel-pager' };
			}
			if($this.data('wrap') == 'no') {
				$secondOpts.wrapper = 'parent';
			}

			$opts.scroll.onAfter = function(){
				announcement_bar_set_body_margin();
				$( 'body' ).trigger( 'announcement_bar_scroll_on_after' );
			}

			// Timer
			if(typeof $this.data('timer') !== 'undefined' || $this.data('timer') > 0 || $this.data('timer') == 'on'){
				if(typeof $opts.auto !== 'undefined') {
					$opts.auto.progress = {
						bar: '#' + $this.data('id') + ' .timer .timer-bar'
					};
				}
			}

			$this.announCarouFredSel($opts, $secondOpts);
		});
	}

	$(function(){
		// Run on DOM ready
		$('.announcement_content .more-link').on('click', function(e){
			e.preventDefault();
			var $this = $(this);
			$(this).toggleClass('active').closest('.announcement_post').find('.more_wrap').slideToggle(function(){
				var $announcement_list = $(this).closest('.announcement_list');

				if( parseInt($announcement_list.data('auto-scroll')) > 0 ){
					if ( $this.hasClass('active') ) {
						$announcement_list.trigger('stop');
					} else {
						$announcement_list.trigger('play', true);
					}
				}

				$announcement_list.trigger('updateSizes')
				.trigger('configuration', ['reInit', true]);
			});
		});

		$('.themify_announcement .close').on('click', function(e){
			e.preventDefault();
			var type = $(this).data('type');
			if(type == 'close'){
				$(this).closest('.themify_announcement').slideUp('slow');
				// set cookie
				announSetCookie('announ-toggle-close', 'collapsed');
				if($(this).closest('.themify_announcement').hasClass('bar')){
					announToggleBodyMargin('close');
				}
			} else if(type == 'toggleable'){
				var closeBtnHeight = $(this).closest('.themify_announcement').find('.toggle-close').height();
				$(this).closest('.announcement_container').animate({
					height:'toggle'
				}, 400, function(){
					$(this).closest('.themify_announcement').toggleClass('collapsed');
					// Set cookie
					if($(this).closest('.themify_announcement').hasClass('collapsed')){
						announSetCookie('announ-toggle-close', 'collapsed');
					} else {
						announSetCookie('announ-toggle-close', 'expanded');
					}
					// Toggle Button
					if( $(this).closest('.themify_announcement').hasClass('announcement_bottom') ){
						var anime = {top: '-' + closeBtnHeight + 'px'};
					} else {
						var anime = {bottom: '-' + closeBtnHeight + 'px'};
					}
					$(this).closest('.themify_announcement').find('.toggle-close').show()
					.animate(anime, 400);
					if($(this).closest('.themify_announcement').hasClass('bar')){
						announToggleBodyMargin('close');
					}
				});
			}

		});

		$('.themify_announcement .toggle-close').on('click', function(e){
			e.preventDefault();
			var barPos = $(this).closest('.themify_announcement').hasClass('announcement_bottom') ? 'top' : 'bottom',
                            toggleAble = barPos == 'top' ? {top: 'toggle'} : {bottom: 'toggle'};

			// Hide the close toggle
			$(this).animate(toggleAble, 400, function(){
				$(this).removeAttr('style').closest('.themify_announcement').toggleClass('collapsed')
				.find('.announcement_container:not(.close-container)').animate({
					height: 'toggle'
				}, 400, function(){
					// Set cookie
					if($(this).closest('.themify_announcement').hasClass('collapsed')){
						announSetCookie('announ-toggle-close', 'collapsed');
					} else {
						announSetCookie('announ-toggle-close', 'expanded');
					}

					// set body margin
					if($(this).closest('.themify_announcement').hasClass('bar')){
						announToggleBodyMargin('expand');
					}

					// reinit carousel
					$(this).find('.announcement_list').trigger('updateSizes').trigger('configuration', ['reInit', true]);
				});
			});
		});
		
		var resizeId;
		$(window).on('resize', function(){
			clearTimeout(resizeId);
			resizeId = setTimeout(function(){
				$('.themify_announcement.bar').find('.announcement_list').trigger('configuration', ['reInit', true]);
				announcement_bar_set_body_margin();
			}, 500);
		});
	});

	// Run on WINDOW load
	$(window).load(function(){
		announcement_bar_create_carousel();
		$('body').on( 'builder_load_module_partial', function( event, $newElemn ) {
			announcement_bar_create_carousel( $newElemn );
		}).on('builder_toggle_frontend', function(){
			announcement_bar_create_carousel();
		});
	});
})(jQuery);