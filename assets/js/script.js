jQuery(document).ready(function($){
	$.widget('custom.iconselectmenu', $.ui.selectmenu,{
		_renderItem: function(ul, item){
			var li = $('<li>');
			var wrapper = $('<div>',{
				text: item.label,
				'class': 'iti_admin ui-menu-item-wrapper'
			});
			if( item.disabled ){
				li.addClass('ui-state-disabled');
			}
			$('<span>',{
				'style': item.element.attr('data-style'),
				'class': 'iti__flag iti__' + item.element.val()
			}).prependTo(wrapper);

			return li.append(wrapper).appendTo(ul);
		}
	});

	$('.country-selector').iconselectmenu();
	$('.country-multiple').select2({
		templateResult: function(state){
			console.log('parsel');
			if (!state.id) {
				return state.text;
			}
			var $state = $('<span>',{
				'text' : state.text,
				'class': 'iti_admin',
			}) 
			$('<span>',{
				'class': 'iti__flag iti__' + state.element.value.toLowerCase()
			}).prependTo($state);
			return $state;
		}
	});

	var $tel = $("input[type='tel']");
	$.each($tel, function(i){
		$input = $(this);
		var initial = $input.data('initial_country');
		var $phone = $input.intlTelInput({
			utilsScript: intlTelInput.utils,
			hiddenInput: $input.data('hidden'),
			separateDialCode: $input.data('dial_code'),
			autoPlaceholder: $input.data('placeholder'),
			placeholderNumberType: $input.data('number_type'),
			initialCountry: initial,
			geoIpLookup: function(callback){
				if( 'auto' == initial ){
					if( cookie = getCountryCookie() ){
						callback(cookie);
					}else{
						$.get('http://ipinfo.io', function(){}, 'jsonp').always(function(resp){
							var countryCode = (resp && resp.country)? resp.country : '';
							setCountryCookie(countryCode);
							callback(countryCode);
						});
					}
				}else{
					if( 'none' == initial ) initial = null;
					callback(initial);
				}
			},
			preferredCountries: $input.data('preferred_countries'),
			excludeCountries: $input.data('exclude_countries'),
			onlyCountries: $input.data('only_countries'),
		});
		if( $input.data('validate') ){
			$input.on('blur', function(e){
				resetPhoneInput($(this));
				if( $(this).val().trim() ){
					if( $phone.intlTelInput('isValidNumber') ){
						$(this).parents('.iti').siblings('.text-success').removeClass('hidden');
					}else{
						$(this).parents('.form-group').addClass('has-error');
						var errorCode = $phone.intlTelInput('getValidationError');
						$(this).parents('.iti').siblings('.text-danger').html(intlTelInput.error[errorCode]).removeClass('hidden');
					}
				}
			});
		}
	});

	function resetPhoneInput(elem){
		elem.parents('.form-group').removeClass('has-error');
		elem.parents('.iti').siblings('.text-danger').html('').addClass('hidden');
		elem.parents('.iti').siblings('.text-success').addClass('hidden');
	}

	function setCountryCookie(code){
		document.cookie = 'loc='+code+';max-age=15768000;samesite=strict;';
	}

	function getCountryCookie(){
		return document.cookie.replace(/(?:(?:^|.*;\s*)loc\s*\=\s*([^;]*).*$)|^.*$/, "$1");
	}

});