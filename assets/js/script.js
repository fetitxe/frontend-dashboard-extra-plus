jQuery(document).ready(function($){
	$.widget('custom.iconselectmenu', $.ui.selectmenu,{
		_renderButtonItem: function(item) {
			var buttonItem = $('<span>', {
				'class': 'iti_admin'
			});

			this._setText( buttonItem, item.label );
			this._addClass( buttonItem, "ui-selectmenu-text" );
			
			$('<span>',{
				'style': item.element.attr('data-style'),
				'class': 'iti__flag iti__' + item.element.val()
			}).prependTo(buttonItem);

			return buttonItem;
		},
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
		},
		
		_drawButton: function(){
			var icon,
				that = this,
				item = this._parseOption(
					this.element.find("option:selected"),
					this.element[0].selectedIndex
				);

			// Associate existing label with the new button
			this.labels = this.element.labels().attr("for", this.ids.button);
			this._on(this.labels, {
				click: function(event){
					this.button.focus();
					event.preventDefault();
				}
			});

			// Hide original select element
			this.element.hide();

			// Create button
			this.button = $("<span>", {
				'class': 'countrySelector',
				tabindex: this.options.disabled ? -1 : 0,
				id: this.ids.button,
				role: "combobox",
				"aria-expanded": "false",
				"aria-autocomplete": "list",
				"aria-owns": this.ids.menu,
				"aria-haspopup": "true",
				title: this.element.attr("title")
			}).insertAfter(this.element);

			this._addClass(this.button, "ui-selectmenu-button ui-selectmenu-button-closed", "ui-button ui-widget");

			icon = $("<span>" ).appendTo(this.button);
			this._addClass(icon, "ui-selectmenu-icon", "ui-icon " + this.options.icons.button);
			this.buttonItem = this._renderButtonItem(item).appendTo(this.button);

			if( this.options.width !== false ){
				this._resizeButton();
			}

			this._on(this.button, this._buttonEvents);
			this.button.one("focusin", function(){

				// Delay rendering the menu items until the button receives focus.
				// The menu may have already been rendered via a programmatic open.
				if( !that._rendered ){
					that._refreshMenu();
				}
			} );
		},
		
	});

	$('.country-selector').iconselectmenu();
	$('.country-multiple').select2({
		templateResult: function(state){
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
	$('.fed_extra_plus_icon').on('click', function(e){
		var target = $(this).parents('.modal').data('target');
		$(target).val( $(this).data('id') );
	});

	// Telephone field
	var $tel = $("input[type='tel']");
	if( 0 < $tel.length ){
		$.each($tel, function(i){
			$input = $(this);
			var initial = $input.data('initial_country');
			var $phone = $input.intlTelInput({
				utilsScript: fedep.utils,
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
							$(this).parents('.iti').siblings('.text-danger').html(fedep.error[errorCode]).removeClass('hidden');
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
	}

	//Geolocation field
	var $geopos = $('.geopos');
	if( 0 < $geopos.length ){
		$.each($geopos, function(i){
			$group = $(this);
			$group.find('.setLocation').on('click', function(e){
				var stored = {
					lat: parseFloat($group.find('.latitude').val()),
					lng: parseFloat($group.find('.longitude').val())
				};
				var isStored = ( isNaN(stored.lat) || isNaN(stored.lng) )? false : true;
				var position = {
					lat: !isNaN(stored.lat) ? stored.lat : 0, 
					lng: !isNaN(stored.lng) ? stored.lng : 0
				};
				var infoWindow = new google.maps.InfoWindow;
				var $map = new google.maps.Map($group.find('.map')[0],{
					center: position,
					zoom: isStored? 14 : 3
				});
				var marker = new google.maps.Marker({
					map: $map,
					position: position,
					animation: google.maps.Animation.DROP,
					draggable: true
				});

				google.maps.event.addListener($map, 'click', function(event){
					var clickedLocation = event.latLng;
					marker.setOptions({
						animation: google.maps.Animation.BOUNCE,
						label: '',
						position: clickedLocation
					});
					$map.setCenter(clickedLocation);
					getPositionMarker($group, marker);
				});

				google.maps.event.addListener(marker, 'dragend', function(event){
					var dragLocation = event.latLng;
					marker.setOptions({
						animation: null,
						label: '',
						position: dragLocation
					});
					getPositionMarker($group, marker);
				});

				if( $group.data('locate') && !isStored && navigator.geolocation ){
					navigator.geolocation.getCurrentPosition(
						function(success){
							position.lat = success.coords.latitude;
							position.lng = success.coords.longitude;
							marker.setOptions({
								animation: google.maps.Animation.BOUNCE,
								label: {
									text: fedep.maps.locate,
									fontWeight: 'bold'
								},
								position: position
							});
							$map.setOptions({
								center: position,
								zoom: 14
							});
							getPositionMarker($group, marker);
						},
						function(){
							infoWindow.setPosition(position);
							infoWindow.setContent(fedep.maps.unlocate);
							infoWindow.open($map);
						}
					);
				}

				$group.find('.resetLocation').on('click', function(){
					var zero = {lat:0,lng:0}
					marker.setOptions({
						position: zero,
						animation: google.maps.Animation.DROP,
						label: ''
					});
					$map.setOptions({
						center: zero,
						zoom: 3
					});
				$group.find('.latitude, .longitude, .locationFloat').val('');
				});
			});
		});

		function getPositionMarker(elem, marker){
			var currentLocation = marker.getPosition();
			elem.find('.latitude').val(currentLocation.lat());
			elem.find('.longitude').val(currentLocation.lng());
			elem.find('.locationFloat').val(currentLocation.lat()+','+currentLocation.lng());
		}
	}

	// Avanced file
	var $avanced = $('.fed_extra_plus_upload_wrapper');
	if( 0 < $avanced.length ){
		$.each($avanced, function(i){
			var $upload = $(this);
			var $placeholder = $('<span class="fed_extra_plus_upload_icon '+ $(this).data('icon') +' fa-3x"></span>');
			$upload.on('click', '.fed_extra_plus_remove_image', function(e){
				e.preventDefault();
				var $attach = $(this).parent('.thumbnail');
				if( 1 < $upload.find('.thumbnail').length && !$attach.hasClass('empty')){
					$attach.remove();
				}else{
					$attach.find('.fed_extra_plus_upload_input').val('');
					$attach.find('.fed_extra_plus_upload_image_container').html($placeholder);
				}
			});

			$upload.on('click', '.fed_extra_plus_upload_container', function(e){
				e.preventDefault();
				var custom_uploader;
				var $controler = $(this).parent('.thumbnail');
				custom_uploader = wp.media.frames.file_frame = wp.media({
					title: $upload.data('label'),
					button: {
						text: $upload.data('button'),
					},
					multiple: false
				});
				custom_uploader.on('select', function(){
					var attachment = custom_uploader.state().get('selection').first().toJSON();
					var allowed = $upload.data('allow').split(',');
					if( allowed.includes(attachment.mime) ){
						$.ajax({
							type: 'POST',
							url: $upload.data('url'),
							data: {
								action: 'fed_extra_plus_ajax_get_attachment_avanced_file',
								fed_nonce: $upload.data('nonce'),
								attached: attachment.id,
								meta: $upload.data('meta'),
								multiple: $upload.data('multiple'),
								icon: $upload.data('icon'),
							},
							success: function(results){
								if(results.success){
									var data = results.data.data;
									$controler.find('.fed_extra_plus_upload_input').val(attachment.id);
									$controler.find('.fed_extra_plus_upload_image_container').html(data['img']);
									$controler.find('.fed_extra_plus_upload_icon').remove();
									$controler.removeClass('empty');
									if(data['append']){
										$upload.append($(data['append']));
									}
								}
							},
							error: function (xhr, textStatus, errorThrown){
								alert(errorThrown);
							}
						});
					}
				});
				custom_uploader.open();
			});
		});
	}

});