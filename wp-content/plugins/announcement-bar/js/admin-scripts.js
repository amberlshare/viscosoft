;(function($, window, document, undefined) {
	// Toggle Plugin Settings
	function pluginSettingToggle(obj) {
		var $parent = obj.closest('.plugin-main-toggle'),
			value = obj.val();

		if( value == '0' || value == 'disable' ) {
			$parent.siblings().hide().end().closest('.form-table').nextAll('.form-table').hide();
		} else {
			$parent.siblings().show().end().closest('.form-table').nextAll('.form-table').show();
			if( value == 'enable' ) {
				$( '#post-category' ).hide();
			} else if( value == 'post' ) {
				$( '#announcement-category' ).hide();
			}
		}
	}

	/** patch field visibility in the post metabox
	 * @todo: this should be removed eventually
	 */
	$(function(){
		$( '#announcement_bar-default' ).closest( '.themify_field_row' ).find( ':checked' ).click();
	});

	function showLogin(status){
		$('.prompt-box .show-login').show();
		$('.prompt-box .show-error').hide();
		if(status == 'error'){
			if($('.prompt-box .prompt-error').length == 0){
				$('.prompt-box .prompt-msg').after('<p class="prompt-error">' + themify_lang.invalid_login + '</p>');
			}
		} else {
			$('.prompt-box .prompt-error').remove();
		}
		$(".overlay, .prompt-box").fadeIn(500);	
	}	
	function hideLogin(){
		$('.overlay, .prompt-box').fadeOut(500, function(){
			var $prompt = $('.prompt-box'), $iframe = $prompt.find('iframe');
			if ( $iframe.length > 0 ) {
				$iframe.remove();
			}
			$prompt.removeClass('show-changelog');
		});
	}
	function showAlert(){
		$(".alert").addClass("busy").fadeIn(800);
	}
	function hideAlert(status){
		if(status == 'error'){
			status = 'error';
			showErrors();
		} else {
			status = 'done';	
		}
		$(".alert").removeClass("busy").addClass(status).delay(800).fadeOut(800, function(){
			$(this).removeClass(status);											   
		});
	}
	function showErrors(verbose){
		$(".overlay, .prompt-box").delay(900).fadeIn(500);	
		$('.prompt-box .show-error').show();
		$('.prompt-box .show-error p').remove();
		$('.prompt-box .error-msg').after('<p class="prompt-error">' + verbose + '</p>');
		$('.prompt-box .show-login').hide();
	}

	// Doc ready
	$(function(){
		$('.plugin-main-toggle input[type=radio]').on('click', function(){
			pluginSettingToggle($(this));
		});
		$('.plugin-main-toggle input[type=radio]:checked').each(function(){
			pluginSettingToggle($(this));
		});

		// fix Appearance option toggle
		$( '.enable_toggle input[type=radio]:checked', '#announcement-bar-form' ).click();

		//
		// Upgrade Theme / Framework
		//
		$(".announcement-bar-upgrade-plugin").on('click', function(e){
			e.preventDefault();
			showLogin();
		});
		
		//
		// Login Validation
		//
		$(".ab-upgrade-login").on('click', function(e){
			e.preventDefault();										   
			var el = $(this), 
				username = el.parent().parent().find('.username').val(),
				password = el.parent().parent().find('.password').val(),
				login = $(".upgrade-theme").parent().hasClass('login');
			if(username != "" && password != ""){
				hideLogin();
				showAlert();
				$.post(
					ajaxurl,
					{
						'action':'announcement_bar_validate_login',
						'type':'plugin',
						'login':login,
						'username':username,
						'password':password
					},
					function(data){
						data = $.trim(data);
						if('subscribed' == data){
							hideAlert();
							$('#themify_update_form').submit();
						} else if('invalid' == data) {
							hideAlert('error');
							showLogin('error');
						} else if('unsuscribed' == data) {
							hideAlert('error');
							showLogin('unsuscribed');
						}
					}
				);																					
			} else {
				hideAlert('error');	
				showLogin('error');							   
			}
		});

		/**
		 * Hide Overlay
		 */
		$('body').on('click', '.overlay', function(){
			hideLogin();
		});

		/**
		 * Changelogs
		 */
		$('.themify_changelogs').on('click', function(e){
			e.preventDefault();
			var $self = $(this),
				url = $self.data('changelog'),
				$body = $('body');

			if ( $('.overlay').length <= 0 ) {
				$body.prepend('<div class="overlay" />');
			}

			$('.show-login, .show-error').hide();
			$('.alert').addClass('busy').fadeIn(300);
			$('.overlay, .prompt-box').fadeIn(300);
			var $iframe = $('<iframe src="'+url+'" />');
			$iframe.on('load', function(){
				$('.alert').removeClass('busy').fadeOut(300);
			}).prependTo( '.prompt-box' );
			$('.prompt-box').addClass('show-changelog');

		});
	});
}(jQuery, window, document));