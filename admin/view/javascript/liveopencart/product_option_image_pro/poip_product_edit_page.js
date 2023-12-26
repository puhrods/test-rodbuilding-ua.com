//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

var poip = (function($){
	
	let poip = {
	
		initialized : false,
		option_image_row : 0,
		call_on_init : [],
		events: {},
		
		poip_demo_images : [
			{img: 'catalog/demo/apple_cinema_30.jpg', ov_index: 0},
			{img: 'catalog/demo/compaq_presario.jpg', ov_index: 0},
			{img: 'catalog/demo/hp_1.jpg', ov_index: 0},
			{img: 'catalog/demo/hp_2.jpg', ov_index: 0},
			{img: 'catalog/demo/hp_3.jpg', ov_index: 0},
			{img: 'catalog/demo/hp_banner.jpg', ov_index: 0},
			{img: 'catalog/demo/canon_eos_5d_1.jpg', ov_index: 1},
			{img: 'catalog/demo/canon_eos_5d_3.jpg', ov_index: 1},
			{img: 'catalog/demo/ipod_nano_2.jpg', ov_index: 2},
		],
		
		each : function(collection, fn){
			for ( let i_item in collection ) {
				if ( !collection.hasOwnProperty(i_item) ) continue;
				if ( fn(collection[i_item], i_item) === false ) {
					return;
				}
			}
		},
		
		hideSettings: function($button_hide) {
			let $container = $button_hide.closest('[data-poip="settings"]');
			let $button_show = $container.find('button[data-poip="button-show-settings"]');
			$button_hide.hide();
			$button_show.show();
			$container.find('[data-poip="setting-inputs"]').hide();
			poip.updateNumberOfCustomSettings($button_show);
		},
		showSettings: function($button_show) {
			let $container = $button_show.closest('[data-poip="settings"]');
			let $button_hide = $container.find('button[data-poip="button-hide-settings"]');
			$button_show.hide();
			$button_hide.show();
			$container.find('[data-poip="setting-inputs"]').show();
		},
		updateNumberOfCustomSettings: function($button){
			let $container = $button.closest('[data-poip="settings"]');
			let cnt = 0;
			$container.find('select[name^="product_option["][name*="][poip_settings]"]').each(function(){
				if ( $(this).val() && $(this).val() != '0' ) {
					cnt++;
				}
			});
			let cnt_view = cnt ? ' ('+cnt+')' : '';
			$container.find('[data-poip="custom-settings-cnt"]').html(cnt_view);
		},
		
		init : function( params ) {
			
			var params_names = ['option_values', 'product_options', 'texts', 'settings_details', 'settings_enable_disable_options', 'saved_settings', 'image_placeholder', 'global_settings'];
			for ( let i_params_names in params_names ) {
				if ( !params_names.hasOwnProperty(i_params_names) ) continue;
				
				var param_name = params_names[i_params_names];
				if ( typeof(params[param_name]) != 'undefined' ) {
					poip[param_name] = params[param_name];
				} else {
					poip[param_name] = {};
				}
			}
			
			$(document).on('change', ':checkbox[name^="product_image["][name*="[poip]["][value!="0"]', function(){
				poip.setAvailabilityOfNoValueByName( $(this).attr('name') );
			});
			
			poip.initialized = true;
			
			$(document).on('click', 'button[data-poip="button-hide-settings"]', function(){
				poip.hideSettings($(this));
			});
			$(document).on('click', 'button[data-poip="button-show-settings"]', function(){
				poip.showSettings($(this));
			});
			
			poip.onEvent('addImage_after', function(){
				if ( poip.global_settings.options_images_edit == 1 ) {
					poip.displayImageOptions(image_row);
				}
			});
			
			$(document).trigger('init_after.poip', [poip]);
			poip.launchDelayedCalls();
		},
		
		launchDelayedCalls: function(){
			for (let i_call_on_init in poip.call_on_init) {
				if ( !poip.call_on_init.hasOwnProperty(i_call_on_init) ) continue;
				var call_on_init_item = poip.call_on_init[i_call_on_init];
				poip.callOnInitApply(call_on_init_item.fn, call_on_init_item.args);
			}
		},
		
		onEvent: function(event_name, fn) {
			//$(document).on(event_name+'.poip.liveopencart', fn);
			if ( event_name.indexOf(',') != -1 ) {
				poip.each(event_name.split(','), function(current_event_name){
					poip.onEvent( $.trim(current_event_name), fn);
				});
			}
			if ( typeof(poip.events[event_name]) == 'undefined' ) {
				poip.events[event_name] = [];
			}
			poip.events[event_name].push(fn);
		},
		
		triggerEvent: function(event_name, params) {
			let result = true;
			if ( typeof(poip.events[event_name]) != 'undefined' ) {
				poip.each(poip.events[event_name], function(fn){
					result = fn.apply(poip, params) && result;
				});
			}
			return result;
		},
		
		fillDemoImages : function () {
			poip.each(poip.poip_demo_images, function(poip_demo_image, poip_demo_image_i){
				
				if ( $('table#images [data-poip="option-column"]').length ) { // options for images
					addImage();
					
					let $tr = $('table#images tbody tr:last');
					$tr.find('input[type="hidden"][name^="product_image["]').val(poip_demo_image.img);
					$tr.find('td[data-poip-options] :checkbox').eq(poip_demo_image.ov_index).prop('checked', true);
					$tr.find('input[name$="][sort_order]"]').val( 1+parseInt(poip_demo_image_i) );
				} else { // images for options
					
					poip.addProductOptionImage(0, poip_demo_image.ov_index, '', poip_demo_image.img, poip_demo_image_i );
					
				}
			});
		},
		
		callOnInit : function(fn, args){
			if ( poip.initialized ) {
				poip.callOnInitApply(fn, args);
			} else {
				poip.call_on_init.push( {fn: fn, args: args} );
			}
		},
		
		callOnInitApply : function(fn, args) {
			fn.apply(null, args);
		},
		
		notIntializedWillCallLater : function(fn, p_arguments) {
			if ( !poip.initialized ) {
				poip.callOnInit(fn, p_arguments);
				return true;
			}
			return false;
		},
		
		getProductOptionValueName : function getProductOptionValueName(option_id, option_value_id) { // poip_get_product_option_value_name
			for (let i in poip.option_values[option_id]) {
				if (poip.option_values[option_id][i].option_value_id == option_value_id) {
					return poip.option_values[option_id][i].name;
				}
			}
			if ( option_value_id == 0 ) {
				return poip.texts.entry_no_value;
			} else if (option_value_id == -1 ) {
				return poip.texts.entry_any_value;
			}
		},
		
		getImageOptionCheckboxHTML : function(data, option_id, option_value_id, checkbox_name, title='') {
			let html = '';
			html+= '<div class="checkbox" >';
			if ( option_value_id == 0 || option_value_id == -1 ) {
				html+= '<i style="font-size: 90%;">';
			}
			html+= '<label '+(title ? ' title="'+title+'" ' : '')+'>';
			html+= '<input type="checkbox" name="'+checkbox_name+'" value="'+option_value_id+'"';
			if (data && data.poip && data.poip[option_id]) {
				if ( $.inArray(option_value_id, data.poip[option_id]) != -1 || $.inArray(option_value_id.toString(), data.poip[option_id]) != -1 ) {
					html+= ' checked ';
				}
			}
			html+= '>&nbsp;'+poip.getProductOptionValueName(option_id, option_value_id);
			html+= '</label>';
			if ( option_value_id == 0 || option_value_id == -1 ) {
				html+= '</i>';
			}
			html+= '</div>';
			return html;
		},
		
		displayImageOptions : function displayImageOptions(row, data) { // poip_show_image_options
			
			if ( poip.notIntializedWillCallLater( displayImageOptions, arguments) ) {
				return;
			}
			
			if ( !$('#image-row'+row).length ) return;
			if ( !$('#image-row'+row+' td[data-poip-options]').length ) {
				$('#image-row'+row+' td:first').after('<td class="poip-options-to-image text-left" data-poip-options ></td>');
			}
			
			var html = '';
			var checkbox_names = [];
			
			poip.each(poip.product_options, function(product_option){
				
				if ( $.inArray(product_option.type, ['select', 'radio', 'image', 'checkbox', 'color', 'block']) != -1) {
					
					html+= '<div class="text-left poip-option-to-image">';
					html+= '<b>'+product_option.name+'</b><br>';
					
					var checkbox_name = 'product_image['+row+'][poip]['+product_option.option_id+'][]';
					checkbox_names.push(checkbox_name);
					
					poip.each(product_option.product_option_value, function(product_option_value){
						
						html+= poip.getImageOptionCheckboxHTML(data, product_option.option_id, product_option_value.option_value_id, checkbox_name);
						
					});
					
					html+= poip.getImageOptionCheckboxHTML(data, product_option.option_id, -1, checkbox_name, poip.texts.entry_any_value_help);
					html+= poip.getImageOptionCheckboxHTML(data, product_option.option_id, 0, checkbox_name, poip.texts.entry_no_value_help);
					
					html+= '</div>';
				}
			});
			$('#image-row'+row+' td[data-poip-options]').html(html);
			
			for ( let i_checkbox_names in checkbox_names ) {
				if ( !checkbox_names.hasOwnProperty(i_checkbox_names) ) continue;
				let checkbox_name = checkbox_names[i_checkbox_names];
				poip.setAvailabilityOfNoValueByName(checkbox_name);
			}
			
		},
	
		// << visibility of the checkbox "no value"
		setAvailabilityOfNoValueByName : function(name) { //poip_setAvailabilityOfNoValueByName
			var no_value_disabled = ( $(':checkbox[name="'+name+'"][value!="0"][value!="-1"]:checked').length == 0 );
			$(':checkbox[name="'+name+'"][value="0"]').prop('disabled', no_value_disabled);
			if ( no_value_disabled ) {
				$(':checkbox[name="'+name+'"][value="0"]').prop('checked', false);
			}
			$(':checkbox[name="'+name+'"][value="0"]').parent().fadeTo('fast', (no_value_disabled ? 0.1 : 1) ); // label
		},
		// >> visibility of the checkbox "no value"
		
		displayProductOptionSettings : function displayProductOptionSettings(option_num, option_type, product_option_id) {
			
			if ( poip.notIntializedWillCallLater( displayProductOptionSettings, arguments) ) {
				return;
			}
			
			if ( $.inArray(option_type, ['select', 'radio', 'checkbox', 'color', 'block']) == -1 ) return;
			
			let product_option_settings = false;
			if ( typeof(product_option_id) != 'undefined' && typeof(poip.saved_settings[product_option_id]) != 'undefined' ) {
				product_option_settings = poip.saved_settings[product_option_id];
			}
			
			let html = '';
			html+= '<div class="form-group">';
			html+= '<div data-poip="settings">';
			
			html+= '<div class="col-sm-2" >';
			html+= '<div class="row">';
			html+= '<label class="col-sm-12 control-label">'+poip.texts.poip_module_name+'</label>';
			html+= '</div>';
			html+= '<div class="row poip-container-button-hide-settings">';
			html+= '<button type="button" class="btn btn-default pull-right" data-poip="button-hide-settings" style="display:none;">'+poip.texts.entry_hide_settings+'</button>';
			html+= '</div>';
			html+= '</div>';
			
			html+= '<div class="col-sm-10" >';
			html+= '<button type="button" class="btn btn-default" data-poip="button-show-settings">'+poip.texts.entry_show_settings+'<span data-poip="custom-settings-cnt"></span></button>';
			html+= '<div class="row" data-poip="setting-inputs" style="display:none;">';
			
			for ( let i_settings_details in poip.settings_details) {
				if ( !poip.settings_details.hasOwnProperty(i_settings_details) ) continue;
				var setting_details = poip.settings_details[i_settings_details];
				
				html+= '<div class="col-sm-4">';
				html+= setting_details.title;
				html+= '<select name="product_option['+option_num+'][poip_settings]['+setting_details.name+']" class="form-control">';
				if ( setting_details.values ) {
					html+= '<option value="0">'+poip.settings_enable_disable_options[0]+'</option>';
					for ( let setting_value in setting_details.values ) {
						if ( !setting_details.values.hasOwnProperty(setting_value) ) continue;
						
						setting_value = parseInt(setting_value);
						
						var setting_title = setting_details.values[setting_value];
						html+= '<option value="'+(setting_value+1)+'"';
						if ( product_option_settings && typeof(product_option_settings[setting_details.name] != 'undefined') && product_option_settings[setting_details.name] == (setting_value+1) ) {
							html+= ' selected ';
						}
						html+= '>'+setting_title+'</option>';
					}
				} else {
					for ( let setting_enable_disable_options_value in poip.settings_enable_disable_options ) {
						if ( !poip.settings_enable_disable_options.hasOwnProperty(setting_enable_disable_options_value) ) continue;
						
						var setting_enable_disable_options_value_title = poip.settings_enable_disable_options[setting_enable_disable_options_value];
						html+= '<option value="'+setting_enable_disable_options_value+'"';
						if ( product_option_settings && typeof(product_option_settings[setting_details.name] != 'undefined') && product_option_settings[setting_details.name] == setting_enable_disable_options_value ) {
							html+= ' selected ';
						}
						html+= '>'+setting_enable_disable_options_value_title+'</option>';
					}
				}
				html+= '</select>';
				html+= '</div>';
			}
					
			html+= '</div></div>';
			html+= '</div>';
	
			$('#tab-option'+option_num).prepend(html);
			
			
			poip.updateNumberOfCustomSettings( $('#tab-option'+option_num).find('button[data-poip="button-show-settings"]') );
			
		},
		
		addProductOptionImage : function addProductOptionImage(option_row, option_value_row, thumb, image, srt) { // add_option_image
			
			if ( poip.notIntializedWillCallLater( addProductOptionImage, arguments) ) {
				return;
			}
	
			var html = '';
			
			html += '<div id="div_option_image'+poip.option_image_row+'" class="poip-image-to-option">';
			
			let current_thumb = thumb || poip.image_placeholder;
			let current_image = image || '';
			let current_srt = srt || 0;
			
			if ( !current_srt ) {
				
				$('#option_images'+option_row+'_'+option_value_row).find('[name*="[srt]"]').each(function() {
					current_srt = Math.max(current_srt, ( parseInt($(this).attr('value')) || 0 ) );
				});
				current_srt++;
			}
			
			
			html += '<a href="" id="thumb-option-image'+poip.option_image_row+'" data-toggle="image" class="img-thumbnail" >';
			html += '<img height="100" width="100" src="'+current_thumb+'" alt="" title="" data-placeholder="'+poip.image_placeholder+'">';
			html += '</a>';
			html += '<input type="hidden" id="option_image'+poip.option_image_row+'" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][images]['+poip.option_image_row+'][image]" value="'+current_image+'">';
			html += '<div class="input-group">';
			html += '<input type="text" class="form-control"  title="'+poip.texts.entry_sort_order+'" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][images]['+poip.option_image_row+'][srt]" value="'+current_srt+'" size="3">';
			html += '<button class="btn btn-default" title="'+poip.texts.button_remove+'" onclick="$(\'#div_option_image' + poip.option_image_row + '\').remove();"><i class="fa fa-trash-o"></i></button>';
			html += '</div>';
			
			html += '</div>';
			
			$('#option_images'+option_row+'_'+option_value_row).append(html);
			
			poip.triggerEvent('addProductOptionImage_after', [{$grid: $('#option_images'+option_row+'_'+option_value_row)}]);
			
			poip.option_image_row++;
			
		},
		
		
		
	};
	
	return poip;
	
})(jQuery);	