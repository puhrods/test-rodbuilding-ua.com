//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

function getPoipProduct(custom_setPoipProductCustomMethods) {
	
	return (function($){
		var poip_product = {
			
			proxied : false,
			custom_methods : {},
			custom_data : {},
			$container : false,
			$container_of_options : false,
			
			product_option_ids : [],
			images : [],
			images_by_povs: [],
			module_settings : {},
			options_settings : {},
			theme_settings : {},
			default_image_title : '',
			
			option_prefix : "option",
			option_prefix_length : 0,
			main_image_default_src : '', 	// std_src
			main_image_default_href : '', // std_href
			
			timers : {
				update_images_on_option_change: 0,
			},
			works : {
				update_images_on_option_change: false,
				set_visible_images : false,
			},
			poip_ov : false,
			
		
			init : function(params) {
				
				poip_common.each(poip_product, function(setting_val, setting_key){
					if ( typeof(params[setting_key]) != 'undefined' && typeof(setting_val) != 'function' ) {
						poip_product[setting_key] = params[setting_key];
					}
				});
				
				if ( !poip_product.$container ) {
					poip_product.$container = poip_product.getDefaultContainer();
				}
				poip_product.$container.data('poip_product', poip_product);
				
				if ( poip_product.getContainerOfOptions().find('[name^="'+poip_product.option_prefix+'["]').length == 0 && poip_product.getContainerOfOptions().find('[name^="option_oc["]').length ) { // jcart or analog
					poip_product.option_prefix = 'option_oc';
				}
				
				poip_product.option_prefix_length = poip_product.option_prefix.length;
				
				poip_product.main_image_default_src 	= poip_product.getMainImage().attr('src');
				poip_product.main_image_default_href 	= poip_product.getMainImage().closest('a').attr('href');
				
				poip_product.addInitialVideosToImages();
				
				poip_product.getContainerOfOptions().on('change', poip_product.getSelectorOfOptions(), function(e){
					poip_product.eventOptionValueChange(e, $(this));
				});
				
				if ( poip_product.product_option_ids.length && poip_product.getElement( poip_product.getSelectorOfOptions() ).length ) {
					if ( !poip_product.setDefaultOptionsByURL() ) {
						poip_product.updateImagesOnProductOptionChange(poip_product.product_option_ids[0]);
					}
				} else { // no product option images
					poip_product.initActionWithNoOptionImages();
					
				}
			},
			
			addInitialVideosToImages : function(){
				
				// videos should be linked with all possible option values
				let po_ids = poip_product.getPOIdsHavingImages();
				let pov_ids = poip_product.getPOVIdsHavingImages();
				
				poip_product.getAdditionalImagesBlock().find('a').each(function(){
					let $a = $(this);
					if ( poip_product.elementIsVideo($a) ) {
						
						poip_product.images.splice($a.index(), 0, {
							is_video: true,
							image: $a.attr('href'),
							popup: $a.attr('href'),
							main: $a.attr('href'),
							thumb: $a.find('img').attr('src'),
							sort_order: $a.index(),
							product_option_id: po_ids.slice(0),
							product_option_value_id: pov_ids.slice(0),
						});
					}
				});
				
			},
			
			getPOIdsHavingImages: function() {
				let po_ids = [];
				poip_common.each(poip_product.images, function(poip_img){
					if ( poip_img.product_option_id ) {
						poip_common.each(poip_img.product_option_id, function(po_id){
							poip_common.addToArrayIfNotExists(po_id, po_ids);
						});
					}
				});
				return po_ids;
			},
			getPOVIdsHavingImages: function() {
				let pov_ids = [];
				poip_common.each(poip_product.images, function(poip_img){
					if ( poip_img.product_option_value_id ) {
						poip_common.each(poip_img.product_option_value_id, function(pov_id){
							poip_common.addToArrayIfNotExists(pov_id, pov_ids);
						});
					}
				});
				return pov_ids;
			},
			
			initActionWithNoOptionImages : function(svi_called){ // no product option images
				if ( !svi_called ) {
					poip_product.setVisibleImages( poip_product.getAdditionalImagesToDisplay() );
				}
				if ( poip_product.works.set_visible_images ) {
					setTimeout(function(){
						poip_product.initActionWithNoOptionImages(true);
					}, 10);
					return;
				}
				poip_product.updateImageAdditionalMouseOver();
				poip_product.updatePopupImages();
			},
			
			getDefaultContainer : function(){
				return $('body');
			},
			
			getElement : function(selector){
				if ( selector.indexOf(' ') == -1 && poip_product.$container.is(selector) ) {
					return poip_product.$container;
				} else {
					return poip_product.$container.find(selector);
				}
			},
			
			getContainerOfOptions : function(){
				if ( poip_product.$container_of_options && poip_product.$container_of_options.length ) {
					return poip_product.$container_of_options;
				} else if ( poip_product.$container && poip_product.$container.length ) {
					return poip_product.$container;
				} else {
					return poip_product.getElement('#product');
				}
				
			},
			
			getSelectorOfOptions : function(select_addition, radio_addition, checkbox_addition){
				
				select_addition 	= select_addition || '';
				radio_addition 		= radio_addition || '';
				checkbox_addition = checkbox_addition || '';
				
				var selector = '';
				selector+= 'select[name^="'+poip_product.option_prefix+'["]'+select_addition;
				selector+= ', input:radio[name^="'+poip_product.option_prefix+'["]'+radio_addition;
				selector+= ', input:checkbox[name^="'+poip_product.option_prefix+'["]'+checkbox_addition;
				return selector;
			},
			
			getSelectorOfOptionsChecked : function(){
				return poip_product.getSelectorOfOptions('', ':checked', ':checked');
			},
			
			getProductOptionIdByName : function(name) {
				return name.substr(poip_product.option_prefix_length+1, name.indexOf(']')-(poip_product.option_prefix_length+1) );
			},
			
			eventOptionValueChange : function( event, $option_element ) { // changeVisibleImages
			
				var product_option_id = poip_product.getProductOptionIdByName( $option_element.attr('name') );
				
				if ( $.inArray(product_option_id, poip_product.product_option_ids) != -1 ) {
					poip_product.updateImagesOnProductOptionChange(product_option_id, $option_element);
				}
				
			},
			
			updateImagesOnProductOptionChange : function(product_option_id, $option_element, param_visible_images) { // $option_element - optional
				
				clearTimeout( poip_product.timers.update_images_on_option_change );
				
				if ( (!param_visible_images && poip_product.works.update_images_on_option_change) || poip_product.works.set_visible_images ) {
					
					if ( param_visible_images ) {
						clearTimeout( poip_product.timers.update_images_on_option_change_second_stage );
						poip_product.timers.update_images_on_option_change_second_stage = setTimeout(function(){
							poip_product.updateImagesOnProductOptionChange(product_option_id, $option_element, param_visible_images);
						},10);
					} else {
						poip_product.timers.update_images_on_option_change = setTimeout(function(){
							poip_product.updateImagesOnProductOptionChange(product_option_id, $option_element, param_visible_images);
						},10);
					}	
					return;
				}
				
				poip_product.works.update_images_on_option_change = true;
				
				if ( $.inArray(product_option_id, poip_product.product_option_ids) != -1 ) {
					
					if ( !param_visible_images ) {
						
						let visible_additional_images = poip_product.updateAdditionalImages(product_option_id);
						
						// specific call to wait for possible delays in updateAdditionalImages (control by poip_product.works.set_visible_images)
						poip_product.updateImagesOnProductOptionChange(product_option_id, $option_element, visible_additional_images); 
						return;
					
					} else {
						
						let images_to_display = poip_product.getAdditionalImagesToDisplay();
						//let visible_additional_images = poip_product.updateAdditionalImages(product_option_id);
						
						// if list of visible images changed - restart updating (it can be changed with no onchange event, for example Journal3 set first option values selected on page loading)
						if ( images_to_display.toString() != param_visible_images.toString() ) { 
							poip_product.works.update_images_on_option_change = false;
							poip_product.updateImagesOnProductOptionChange(product_option_id, $option_element);
							return;
						}
						
						poip_product.updateImageAdditionalMouseOver();
						
						poip_product.updatePopupImages();
						poip_product.updateMainImage(product_option_id, param_visible_images);
						poip_product.updateImagesBelowOption(product_option_id, $option_element);
						poip_product.updateDependentThumbnails(param_visible_images);
			
					}
				}
				
				poip_product.works.update_images_on_option_change = false;
			},
			
			getSelectedProductOptionValueIds : function(product_option_id, p_product_options) { // getSelectedOptionValues
		
				var product_options = p_product_options || poip_product.getSelectedProductOptions();
				var values = [];
				
				if ( product_option_id ) {
					if ( typeof(product_options[product_option_id]) != 'undefined' ) {
						values = product_options[product_option_id];
					}
				} else {
					
					poip_common.each(product_options, function(pov_ids){
						values = poip_common.getConcatenationOfArraysUnique(values, pov_ids);
					});
				}
				return values;
			},
			
			getSelectedProductOptions : function(){
				var product_options = {};
				poip_product.getElement( poip_product.getSelectorOfOptionsChecked() ).each(function () {
					var product_option_id = poip_product.getProductOptionIdByName($(this).attr('name'));
					
					if ( $(this).val() ) {
						if ( $.inArray(product_option_id, poip_product.product_option_ids) != -1 ) {
							if ( typeof(product_options[product_option_id]) == 'undefined' ) {
								product_options[product_option_id] = [];
							}
							product_options[product_option_id].push($(this).val());
						}
					}
				});
				return product_options;
			},
			
			getSelectedProductOptionIds : function(){
				return Object.keys( poip_product.getSelectedProductOptions() );
			},
			
			getMainImagePOIP : function(){ 
				let poip_img_main = false;
				poip_common.each(poip_product.images, function(poip_img){
					if ( poip_img.product_image_id && poip_img.product_image_id == -1 ) {
						poip_img_main = poip_img;
						return false;
					}
				});
				return poip_img_main;
			},
		
			getBasicVisibleAdditionalImages : function() { // additional images (incliding the main image, if enabled)
				
				var images = [];
				
				// the main product image should be first
				// if it is linked to an option but this option is not selected
				// or it is linked to an option which is not set to be displayed in the list of additional images
				let poip_img_main = poip_product.getMainImagePOIP();
				if ( poip_img_main && poip_img_main.product_option_id && poip_img_main.product_option_id.length ) {
					let po_ids_selected = poip_product.getSelectedProductOptionIds();
					let po_ids_selected_main = poip_common.getIntersectionOfArrays(poip_img_main.product_option_id, po_ids_selected);
					if ( !po_ids_selected_main.length ) {
						poip_common.addToArrayIfNotExists(poip_img_main.popup, images);
					} else {
						let img_use = false;
						poip_common.each(po_ids_selected_main, function(po_id){
							img_use = img_use && poip_product.options_settings[po_id].img_use;
						});
						if ( !img_use ) {
							poip_common.addToArrayIfNotExists(poip_img_main.popup, images);
						}
					}
				}
				
				poip_common.each(poip_product.images, function(poip_img){
					if ( typeof(poip_img.product_image_id) != 'undefined' || poip_img.is_video ) { // main or additional image or video
						poip_common.addToArrayIfNotExists(poip_img.popup, images);
					}
				});
				return images;
				
			},
		
			getBasicImagesForMainImage : function() { // ???
				
				var images_for_main_image = poip_product.getBasicVisibleAdditionalImages();
				if ( poip_product.std_href ) {
					if ( $.inArray(poip_product.std_href, images_for_main_image) == -1 ) {
						images_for_main_image = [poip_product.std_href].concat(images_for_main_image);
					}
				}
				return images_for_main_image;
			},
			
			getImagesForProductOptionValueIds : function(pov_ids) {
				
				var images = [];
				poip_common.each(poip_product.images, function(poip_img){
					if ( poip_img.product_option_value_id ) {
						if ( poip_common.existsIntersectionOfArrays(poip_img.product_option_value_id, pov_ids) ) {
							poip_common.addToArrayIfNotExists(poip_img.popup, images);
						}
					}
				});
				return images;
			},
			
			getCommonImagesForProductOptionValueIds : function(pov_ids) {
				
				let images = false;
				poip_common.each(pov_ids, function(pov_id){
					
					let pov_poip_images = poip_product.images.filter(function(poip_img){
						return poip_img.product_option_value_id && $.inArray(pov_id, poip_img.product_option_value_id) != -1;
					});
					let pov_images = pov_poip_images.map(function(poip_img){
						return poip_img.popup;
					});
					
					images = images === false ? pov_images : poip_common.getIntersectionOfArrays(images, pov_images);
				});
				
				return images || [];
			},
			
			getImagesForProductOption : function(product_option_id) {
				var images = [];
				poip_common.each(poip_product.images, function(poip_img){
					if ( poip_img.product_option_id && $.inArray(product_option_id, poip_img.product_option_id) != -1 ) {
						poip_common.addToArrayIfNotExists(poip_img.popup, images);
					}
				});
				return images;
			},
			
			getImagesForNotSelectedProductOption : function(product_option_id) {
				
				var images = [];
				poip_common.each(poip_product.images, function(poip_img){
					if ( poip_img.product_option_value_id ) {
						// -product_option_id is used for images checked to be shown when option is not selected
						if ( $.inArray(-product_option_id, poip_img.product_option_value_id) != -1 ) {
							poip_common.addToArrayIfNotExists(poip_img.popup, images);
						}
					}
				});
				return images;
			},
			
			getImagesNotLinkedToProductOption : function(product_option_id) {
				
				var images = [];
				poip_common.each(poip_product.images, function(poip_img){
					if ( poip_img.product_option_value_id ) {
						if ( !poip_img.product_option_id || $.inArray(product_option_id, poip_img.product_option_id) == -1 ) {
							poip_common.addToArrayIfNotExists(poip_img.popup, images);
						}
					}
				});
				return images;
			},
			
			updatePopupImages : function() {
				
				if ( poip_product.getElement('li.image-additional').length ) { // default
					
					if ( typeof($.magnificPopup) != 'undefined' ) {
						
						let mfp_params = {
							type:'image',
							delegate: '.image-additional a:visible',
							gallery: {
								enabled:true
							}
						};
						if ( poip_product.getElement('.thumbnails').data('magnificPopup') && poip_product.getElement('.thumbnails').data('magnificPopup').callbacks ) {
							mfp_params.callbacks = poip_product.getElement('.thumbnails').data('magnificPopup').callbacks;
						}
						
						poip_product.getElement('.thumbnails').magnificPopup(mfp_params);
					}
					
					poip_product.getMainImage().off('click');
					poip_product.getMainImage().on('click', function(event) {
						poip_product.eventMainImgClick(event, this); }
					);
					if ( poip_product.getMainImage().closest('a').length ) {
						poip_product.getMainImage().closest('a').off('click').on('click', function(event){
							event.preventDefault();
							poip_product.getMainImage().click();
						});
					}
				}
			},
			
			eventMainImgClick : function(event, element) { // ???
				event.preventDefault();
				event.stopPropagation();
				
				let main_href = $(element).parent().attr('href');
				//var $additional_image = poip_product.getAdditionalImagesBlock().find('a
				let $additional_images = poip_product.getAdditionalImagesBlock().find('a');
				let $additional_image = $additional_images.filter('[href="'+main_href+'"]');
				
				
				if ( $additional_image.length ) {
					if ( poip_product.module_settings.img_click && $additional_image.closest('.thumbnails').length ) {
						$additional_image.closest('.thumbnails').magnificPopup('open', $additional_images.index($additional_image));
					} else {
						$additional_image.click();
					}
				}
			},
			
			sortImagesBySelectedOptions : function(p_images) {
				
				var images = [];
				
				if ( poip_product.module_settings.options_images_edit == 1 ) { // use basic sort order (set on the Image tab)
					
					poip_common.each(poip_product.images, function(poip_img){
						if ( $.inArray(poip_img.popup, p_images) != -1 ) {
							poip_common.addToArrayIfNotExists(poip_img.popup, images);
						}
					});
					
				} else { // use option sort order (from the Option tab)
				
					// standard/basic images first
					images = poip_common.getIntersectionOfArrays( poip_product.getBasicVisibleAdditionalImages(), p_images);
				
					var pov_ids = poip_product.getSelectedProductOptionValueIds();
					
					poip_common.each(pov_ids, function(pov_id){
					
						if (poip_product.images_by_povs && poip_product.images_by_povs[pov_id] && poip_product.images_by_povs[pov_id].length) {
							poip_common.each(poip_product.images_by_povs[pov_id], function(poip_img_by_pov){
							
								var poip_img = poip_product.getImageBySrc(poip_img_by_pov.image,'image');
								if ( poip_img && $.inArray(poip_img.popup, p_images) != -1) {
									poip_common.addToArrayIfNotExists(poip_img.popup, images);
								}
							});
						}
					});
					
				}
				
				poip_common.each(p_images, function(p_image){
				
					if ( $.inArray(p_image, images) == -1 ) {
						images.push(p_image);
					}
				});
			
				return images;
			},
			
			getCurrentlyVisibleImages : function(){
				images = [];
				poip_product.getAdditionalImagesBlock().find('a:visible').each(function(){
					var href = $(this).attr('href');
					if ( href ) {
						images.push(href);
					}
				});
				return images;
			},
			
			// fn_svi
			setVisibleImages : function(images) {
				
				poip_product.works.set_visible_images = true;
				
				var currently_visible_images = poip_product.getCurrentlyVisibleImages();
				if ( currently_visible_images.toString() != images.toString() ) {
					var $elements_to_remove = poip_product.getAdditionalImagesBlock();
				
					var html = '';
					poip_common.each(images, function(image){
						
						var poip_img = poip_product.getImageBySrc(image, 'popup');
						var title = poip_img.title || poip_product.default_image_title;
						html+= '<li class="image-additional" >';
						
						html+= '<a class="thumbnail" href="'+image+'" title="'+title+'"> <img src="'+poip_img.thumb+'" title="'+title+'" alt="'+title+'"></a>';
						html+= '</li>';
						
					});
					
					$elements_to_remove.remove();
					poip_product.getElement('.thumbnails').append(html);
					
				}
				
				poip_product.works.set_visible_images = false;
			},
			
			getDocumentReadyState : function() {
				return document.readyState == 'complete';
			},
			
			setMainImage : function(image) { // // updateZoomImage should be here too
				
				let poip_img = poip_product.getImageBySrc(image, 'popup', 'main');
				
				poip_product.getMainImage().attr('src', poip_img.main);
				poip_product.getMainImage().closest('a').attr('href', poip_img.popup);
		
			},
			
			displayImagesBelowOption : function( product_option_id, $p_option_element, images) {
				
				let $option_element = $p_option_element || poip_product.getElement(poip_product.getSelectorOfOptions()).filter('[name*="['+product_option_id+']"]').first();
				let below_container_id = '#option-images-'+product_option_id;
				
				poip_product.getElement(below_container_id).remove();
				if ( images.length ) {
					
					let html = '';
					poip_common.each(images, function(image){
						var poip_img = poip_product.getImageBySrc(image, 'popup');
						html+= '<a href="'+poip_img.popup+'" class="image-additional" style="margin: 5px;"><img src="'+poip_img.thumb+'" ></a>';
					});
				
					poip_product.updateContainerOfImagesBelowOptions(product_option_id, $option_element, html);
					
					if ( poip_product.getElement(below_container_id+' a').length ) {
						if ( $.magnificPopup ) {
							poip_product.getElement(below_container_id).magnificPopup({
								type:'image',
								delegate: 'a',
								gallery: {
									enabled:true
								}
							});
						}
					}
				}
			},
			
			updateContainerOfImagesBelowOptions : function(product_option_id, $option_element, html) {
				$option_element.parent().append('<div id="option-images-'+product_option_id+'" style="margin-top: 10px;display: block; width: 100%;">'+html+'</div>');
			},
			
			updateImagesBelowOption : function(product_option_id, $option_element) {
				
				if ( poip_product.options_settings[product_option_id].img_option ) {
					var images = poip_product.getImagesForProductOptionValueIds( poip_product.getSelectedProductOptionValueIds(product_option_id) );
					poip_product.displayImagesBelowOption(product_option_id, $option_element, images);
				}
			},
			
			getImagesFilteredByComb : function(selected_product_options) {
				
				let images = [];
				poip_common.each(poip_product.images, function(poip_img){
					
					if ( poip_img.product_option_id && poip_img.product_option_id.length ) {
						
						let count_option_fit = 0;
						poip_common.each(poip_img.product_option_id, function(po_id){
							let current_selected_povs = selected_product_options[po_id] ? selected_product_options[po_id] : [-po_id];
							if ( poip_common.getIntersectionOfArrays(current_selected_povs, poip_img.product_option_value_id).length ) {
								count_option_fit++;
							}
						});
						
						if ( count_option_fit == poip_img.product_option_id.length ) {
							images.push(poip_img.popup);
						}
					}
					
					
				});
				
				return images;
				
			},
			
			getProductOptionImagesToDisplay : function(product_option_id, p_selected_product_options) {
				
				let selected_product_options = p_selected_product_options ? $.extend({}, p_selected_product_options) : poip_product.getSelectedProductOptions();
				let images = [];
				
				
				if ( typeof(selected_product_options[product_option_id]) == 'undefined' ) { // no selected values
					selected_product_options[product_option_id] = [-product_option_id]; // -product_option_id is used instead of normal product_option_value_id in case of "no value"
				}
				
				if ( poip_product.module_settings.img_filter_by_comb ) { // always filter images by complete set of selected options
					images = poip_product.getImagesFilteredByComb( selected_product_options );
				} else { // classic
					images = poip_product.getImagesForProductOptionValueIds( selected_product_options[product_option_id] ); 
				}
				
				return images;
			},
			
			getAdditionalImagesToDisplay : function(){
				
				var basic_visible_images_initial = poip_product.getBasicVisibleAdditionalImages();
				var basic_visible_images = basic_visible_images_initial.slice();
				
				var selected_product_options = poip_product.getSelectedProductOptions();
				
				var images_to_filter_all_images			= false;
				var images_to_filter_option_images	= false;
				var option_images_add_to_additional = [];
				
				poip_common.each(poip_product.product_option_ids, function(product_option_id){
					var product_option_settings = poip_product.options_settings[product_option_id];
					
					var current_option_images_add_to_additional = [];
					if ( product_option_settings.img_use == 1 ) {
						current_option_images_add_to_additional = poip_product.getImagesForProductOption(product_option_id);
						
					} else if ( product_option_settings.img_use == 2 ) {
						current_option_images_add_to_additional = poip_product.getProductOptionImagesToDisplay(product_option_id, selected_product_options);
						
					} else if ( product_option_settings.img_use == 3 ) {
						
						if ( $.isEmptyObject(selected_product_options) ) { // exclide option images from the additional product images
							let product_option_images = poip_product.getImagesForProductOption(product_option_id);
							
							basic_visible_images = poip_common.excludeItemsFromArray(basic_visible_images, product_option_images);
							
							// show no additional images if nothing to show ?
							basic_visible_images_initial = poip_common.excludeItemsFromArray(basic_visible_images_initial, product_option_images);
							
							current_option_images_add_to_additional = [];
							
						} else {
							current_option_images_add_to_additional = poip_product.getProductOptionImagesToDisplay(product_option_id, selected_product_options);
						}
					}
					
					option_images_add_to_additional = poip_common.getConcatenationOfArraysUnique(option_images_add_to_additional, current_option_images_add_to_additional);
					
					current_images_to_filter_images = poip_product.getProductOptionImagesToDisplay(product_option_id, selected_product_options);
					
					if ( current_images_to_filter_images.length ) {
						
						if ( product_option_settings.img_limit == 1 ) { // filter all additionail images
							
							if ( images_to_filter_all_images === false ) {
								images_to_filter_all_images = current_images_to_filter_images;
							} else {
								images_to_filter_all_images = poip_common.getIntersectionOfArrays(images_to_filter_all_images, current_images_to_filter_images);
							}
							
						} else if ( product_option_settings.img_limit == 2 || product_option_settings.img_limit == 3 ) { // filter only option images
							if ( images_to_filter_option_images === false ) {
								images_to_filter_option_images = current_images_to_filter_images;
							} else {
								images_to_filter_option_images = poip_common.getIntersectionOfArrays(images_to_filter_option_images, current_images_to_filter_images);
							}
						} 
					}
					
					if ( product_option_settings.img_limit == 3 ) {
						let product_option_images = poip_product.getImagesForProductOption(product_option_id);
						basic_visible_images = poip_common.excludeItemsFromArray(basic_visible_images, product_option_images);
					}
					
				});
				
				if ( images_to_filter_all_images ) {
					basic_visible_images 				= poip_common.getIntersectionOfArrays(basic_visible_images, images_to_filter_all_images);
					option_images_add_to_additional = poip_common.getIntersectionOfArrays(option_images_add_to_additional, images_to_filter_all_images);
				}
				if ( images_to_filter_option_images ) {
					option_images_add_to_additional = poip_common.getIntersectionOfArrays(option_images_add_to_additional, images_to_filter_option_images);
				}
				
				var all_visible_images = poip_common.getConcatenationOfArraysUnique(basic_visible_images, option_images_add_to_additional);
				
				
				
				if ( !all_visible_images.length ) {
					all_visible_images = basic_visible_images_initial.slice();
				}
				
				return poip_product.sortImagesBySelectedOptions(all_visible_images);
			},
			
			updateAdditionalImages : function(product_option_id) { // changeAvailableImages
				
				if ($.inArray(product_option_id, poip_product.product_option_ids)==-1) {
					return;
				}
				
				var additional_images_to_display = poip_product.getAdditionalImagesToDisplay();
				poip_product.setVisibleImages(additional_images_to_display, 0, {product_option_id: product_option_id});
				
				return additional_images_to_display;
			},
			
			getImageToDisplayAsMain(product_option_id, p_visible_additional_images) {
				
				if ( $.inArray(product_option_id, poip_product.product_option_ids) != -1 ) {
					
					if ( poip_product.options_settings[product_option_id].img_change ) {
						
						let visible_additional_images = p_visible_additional_images || poip_product.getAdditionalImagesToDisplay();
						
						let all_options_selected_values = poip_product.getSelectedProductOptionValueIds();
						let images_of_all_selected_povs = all_options_selected_values.length ? poip_product.getCommonImagesForProductOptionValueIds(all_options_selected_values) : [];
						
						let current_option_selected_values = poip_product.getSelectedProductOptionValueIds(product_option_id);
						let images_of_current_pov 		= current_option_selected_values.length ? poip_product.getImagesForProductOptionValueIds(current_option_selected_values) : [];
						
						let image_to_display = '';
						
						if ( !image_to_display && images_of_all_selected_povs.length ) {
							let visible_images_of_all_selected_povs = visible_additional_images.length ? poip_common.getIntersectionOfArrays(visible_additional_images, images_of_all_selected_povs) : images_of_all_selected_povs.slice();
							if ( visible_images_of_all_selected_povs.length ) {
								image_to_display = poip_product.getFirstNotVideoFromArray(visible_images_of_all_selected_povs);
							}
						}
						
						if ( !image_to_display && images_of_current_pov.length ) {
							
							let visible_images_of_current_pov = poip_common.getIntersectionOfArrays(visible_additional_images, images_of_current_pov);
							if ( visible_images_of_current_pov.length ) {
								image_to_display = poip_product.getFirstNotVideoFromArray(visible_images_of_current_pov);
							}
						}
						
						if ( !image_to_display && images_of_all_selected_povs.length ) {
							if ( !poip_product.module_settings.img_filter_by_comb ) {
								image_to_display = poip_product.getFirstNotVideoFromArray(images_of_all_selected_povs);
							}
						}
						
						if ( !image_to_display && current_option_selected_values.length ) {
							if ( !poip_product.module_settings.img_filter_by_comb ) {
								image_to_display = poip_product.getFirstNotVideoFromArray(current_option_selected_values);
							}
						}
						
						if ( !current_option_selected_values.length ) {
							if ( visible_additional_images.length ) {
								image_to_display = visible_additional_images[0];
							}
						}
						
						return image_to_display || poip_product.main_image_default_src;
					}
				}
			},
			
			updateMainImage : function(product_option_id, p_visible_additional_images) {
				
				let image_to_display = poip_product.getImageToDisplayAsMain(product_option_id, p_visible_additional_images);
				
				if ( image_to_display ) {
					poip_product.setMainImage(image_to_display);
				}
				
			},
			
			getFirstNotVideoFromArray: function(images) {
				
				let first_image = images[0];
				poip_common.each(images, function(image){
					if ( !poip_product.hrefIsVideo(image) ) {
						first_image = image;
						return false;
					}
				});
				
				return first_image;
			},
			
			getProductOptionValueImages : function(product_option_value_id) {
				
				var images = [];
				poip_common.each(poip_product.images, function(poip_img){
					if ( poip_img.product_option_value_id && $.inArray(product_option_value_id, poip_img.product_option_value_id) !=-1) {
						poip_common.addToArrayIfNotExists(poip_img.popup, images);
					}
				});
				return images;
			},
			
			getImageSrc : function(image, src) {
				
				for (var i in poip_product.images) {
					if (poip_product.images[i].image == image) {
						return poip_product.images[i][src];
					}
				}
				return '';
			},
			
			getImageBySrc : function(image, src1, src2, src3, src4) {
				
				let poip_img_found = poip_product.getImageBySrcExact(image, src1, src2, src3, src4);
				if ( !poip_img_found && image.substr(-5).toLowerCase() == '.webp' ) {
					poip_img_found = poip_product.getImageBySrcExact(image.substr(0, image.length-5), src1, src2, src3, src4);
				}
				return poip_img_found;
			},
			getImageBySrcExact : function(image, src1, src2, src3, src4) {
				
				let search_in_extra_images = false;
				if ( !src1 && !src2 && !src3 && !src4 ) {
					src1 = 'popup';
					src2 = 'main';
					src3 = 'thumb';
					src4 = 'option_thumb';
					search_in_extra_images = true;
				}
				
				let poip_img_found = '';
				poip_common.each(poip_product.images, function(poip_img){
					if ( (src1 && poip_img[src1] == image) || (src2 && poip_img[src2] == image) || (src3 && poip_img[src3] == image) || (src4 && poip_img[src4] == image) || (search_in_extra_images && poip_img.extra_images && $.inArray(image, poip_img.extra_images) != -1 ) ) {
						poip_img_found = poip_img;
						return false;
					}
				});
				return poip_img_found;
			},
		
			getImagesBySrc : function(images, src1, src2, src3, src4) {
			  let poip_imgs = [];
			  poip_common.each(images, function(image){
				let poip_img = poip_product.getImageBySrc(image, src1, src2, src3, src4);
				if (poip_img) {
				  poip_imgs.push(poip_img);
				}
			  });
			  return poip_imgs;
			},
			
			
			getSrcImagesBySrc : function(src, images, src1, src2, src3, src4) {
				let poip_imgs = poip_product.getImagesBySrc(images, src1, src2, src3, src4);
				let src_images = [];
				poip_common.each(poip_imgs, function(poip_img){
					src_images.push(poip_img[src]);
				});
				return src_images;
			},
			
			getProductOptionValueIds : function(product_option_id) {
				var values = [];
				poip_product.getElement( poip_product.getSelectorOfOptions() ).filter(':radio, :checkbox').filter('[name*="['+product_option_id+']"]').each(function(){
					values.push( $(this).val() );
				});
				return values;
			},
			
			getProductOptionIdsDependentByImages : function(product_option_id) {
				
				var po_ids = [];
				poip_common.each( poip_product.images, function(poip_img){
					if ( poip_img.product_option_id && poip_img.product_option_id.length > 1 && $.inArray(product_option_id, poip_img.product_option_id) != -1 ) {
						poip_common.each(poip_img.product_option_id, function(current_po_id){
							if ( current_po_id != product_option_id ) {
								poip_common.addToArrayIfNotExists(current_po_id, po_ids);
							}
						});
					}
				});
				return po_ids;
			},
			
			getImagesRelevantToProductOptions : function(product_options){
				var images = false;
				
				poip_common.each(product_options, function(pov_ids){
					
					var current_images = poip_product.getImagesForProductOptionValueIds(pov_ids);
					if ( images === false ) {
						images = current_images;
					} else {
						images = poip_common.getIntersectionOfArrays(images, current_images);
					}
					
				});
				
				return images;
			},
			
			setVisibleProductOptionValueThumb : function(product_option_value_id, thumb){
				
				var $element = poip_product.getElement('[value="'+product_option_value_id+'"]').filter(':radio, :checkbox'); // usually the necessary element is already received by value, so this way works a bit faster
				if ( $element.next().is('img') ) {
					$element.next().attr('src', thumb);
				}
				
			},
			
			updateDependentThumbnails : function(visible_additional_images) {
				
				//var common_images_for_selected_options = false;
				poip_common.each(poip_product.product_option_ids, function(product_option_id){
		
					var product_option_settings = poip_product.options_settings[product_option_id];
		
					if ( product_option_settings.dependent_thumbnails ) {
		
						var pov_ids = poip_product.getProductOptionValueIds(product_option_id);
		
						if ( pov_ids.length ) {
							
							var dependend_product_option_ids = poip_product.getProductOptionIdsDependentByImages(product_option_id);
							var selected_product_options = poip_product.getSelectedProductOptions();
							
							
							
							var selected_dependent_product_options = {};
							poip_common.each(dependend_product_option_ids, function(dependent_po_id){
								if ( selected_product_options[dependent_po_id] ) {
									selected_dependent_product_options[dependent_po_id] = selected_product_options[dependent_po_id];
								}
							});
							
							var images_of_dependend_product_options = poip_product.getImagesRelevantToProductOptions(selected_dependent_product_options);
							
							poip_common.each(pov_ids, function(product_option_value_id){
								var pov_images 	= poip_product.getProductOptionValueImages(product_option_value_id);
								var pov_image 	= pov_images[0];
								
								if ( pov_images.length ) {
		
									var pov_images_visible = poip_common.getIntersectionOfArrays(visible_additional_images, pov_images);
									if ( pov_images_visible.length ) {
										pov_image = pov_images_visible[0];
										
									} else if ( images_of_dependend_product_options.length ) {
		
										var pov_images_common = poip_common.getIntersectionOfArrays(pov_images, images_of_dependend_product_options);
										if ( pov_images_common && pov_images_common.length ) {
											pov_image = pov_images_common[0];
										}
									}
								}
								if ( pov_image ) {
									var poip_img = poip_product.getImageBySrc(pov_image, 'popup');
									poip_product.setVisibleProductOptionValueThumb(product_option_value_id, poip_img.option_thumb);
								}
							});
						}
					}
				});
				
			},
			
			// return IMG element relevant to main image
			getMainImage : function() {
				return poip_product.getElement('ul.thumbnails li').not('.image-additional').find('a img');
			},
			
			// returns element/elements (div, ul, li etc, depend on theme) containing links to additional images (а)
			getAdditionalImagesBlock : function() {
				
				return poip_product.getElement('li.image-additional');
			
			},
			
			hrefIsVideo : function(href) {
				
				if ( href ) {
					if ( href.indexOf('https://www.youtube.com')==0 || href.indexOf('http://www.youtube.com')==0 || href.indexOf('https://youtube.com')==0 || href.indexOf('http://youtube.com')==0 || href.indexOf('https://www.vimeo.com')==0 || href.indexOf('http://www.vimeo.com')==0 || href.indexOf('https://vimeo.com')==0 || href.indexOf('http://vimeo.com')==0 || href.indexOf('www.youtube.com')==0 || href.indexOf('youtube.com')==0 || href.indexOf('//www.youtube.com')==0 || href.indexOf('//youtube.com')==0 || href.indexOf('www.vimeo.com')==0 || href.indexOf('vimeo.com')==0 || href.indexOf('#iproductvideo_local')!=-1 ) {
						return true;
					}
				}
				return false;
			},
			
			elementIsVideo : function($elem){
				if ( $elem.attr('data-ipvitem') ) {
					try {
						let ipvitem = $.parseJSON($elem.attr('data-ipvitem'));
						if ( ipvitem && ipvitem.ipv && ipvitem.ipv != 'false' ) {
							return true;
						}
					} catch(e) {
						// ignore
					}
				}
				if ( $elem.prop('nodeName') == 'A' ) {
					return poip_product.hrefIsVideo( $elem.attr('href') );
				}
			},
			
			getAdditionalImageSrc : function($element, attr_name) {
				
				var href = '';
				if ( attr_name ) {
					href = $element.attr(attr_name);
				} else {
					if ( $element.is('img') ) {
						href = $element.attr('src');
					} else {
						href = $element.attr('href');
					}
				}
				return href;
			},
			
			eventAdditionalImageMouseover : function(event, $element) {
				
				let image = poip_product.getAdditionalImageSrc($element);
				let poip_img = poip_product.getImageBySrc(image);
				
				if ( image ) {
					if ( poip_product.hrefIsVideo(poip_img.popup) ) {
						return;
					}
					poip_product.setMainImage(poip_img.popup);
				}
			},
			
			eventAdditionalImageClick : function(e, $element) {
				
				e.preventDefault();
				e.stopPropagation();
				
				poip_product.eventAdditionalImageMouseover(e, $element);
				
			},
			
			updateImageAdditionalMouseOver : function() {
				if ( poip_product.module_settings.img_hover ) {
					poip_product.getAdditionalImagesBlock().find('a').off('mouseover').on('mouseover', function(e){
						poip_product.eventAdditionalImageMouseover(e, $(this));
					});
				}
				if ( poip_product.module_settings.img_click ) {
					poip_product.getAdditionalImagesBlock().find('a').off('click').on('click', function(e){
						poip_product.eventAdditionalImageClick(e, $(this));
					});
				}
			},
			
			setDefaultOptionsByURL : function() {
				
				let pov_ids = poip_product.getDefaultOptionValuesToSet();
				
				if ( pov_ids.length ) {
					poip_product.setDefaultOptionValues(pov_ids);
					return true;
				}
		
			},
		
			setDefaultOptionValues : function(pov_ids) {
				poip_common.each(pov_ids, function(pov_id){
					poip_product.setProductOptionValue(pov_id);
				});
			},
		
			getDefaultOptionValuesToSet : function(){
				let pov_ids = [];
				if (poip_product.poip_ov) {
					pov_ids.push(poip_product.poip_ov);
				}
				
				// for Yandex sync module by Toporchillo
				var hash = window.location.hash;
				if (hash) {
					var hashpart = hash.split('#');
					var hashvals = hashpart[1].split('-');
					for (i=0; i<hashvals.length; i++) {
						if ( !hashvals.hasOwnProperty(i) ) continue;
						  
						if ( $.inArray(hashvals[i], pov_ids) == -1 ) {
						  pov_ids.push(hashvals[i]);
						}
					}
				}
				return pov_ids;
			},
			
			setProductOptionValue : function(value) {
			
				var $option_element = poip_product.getElement( poip_product.getSelectorOfOptions(' option') ).filter('[value="'+value+'"]:not(:disabled)');
				if ( !$option_element.length ) {
					return;
				}
				
				if ( $option_element.is('option') ) { // select
					$option_element.parent().val(value);
					$option_element.parent().trigger('change');
				} else { // radio or checkbox
					$option_element.prop('checked', true);
					$option_element.trigger('change');
				}
				
				// color option comp
				if ( value && poip_product.getElement('#color-option-'+value).length ) {
					poip_product.getElement('#color-option-'+value).click();
				}
				
				return true;
			},
			
			
			externalOptionChange : function() {
				if ( poip_product.product_option_ids.length ) {
					poip_product.updateImagesOnProductOptionChange(poip_product.product_option_ids[0]);
				}
			},
			
			elevateZoomDirectChange : function(img_click, timeout, elem_img) {
			
				if ( timeout ) {
					setTimeout(function(){
						poip_product.elevateZoomDirectChange(img_click, 0, elem_img);
					}, timeout);
				} else {
					$('.zoomContainer').find('.zoomWindowContainer').find('div').css({"background-image": 'url("'+img_click+'")'});
					let zoom_type = '';
					if ( poip_product.getMainImage().data('elevateZoom') && poip_product.getMainImage().data('elevateZoom').options ) {
						zoom_type = poip_product.getMainImage().data('elevateZoom').options.zoomType;
					}
					if ( zoom_type != 'window' ) {
						$('.zoomContainer').find('.zoomLens').css({"background-image": 'url("'+img_click+'")'});
					}
				}
			},
			
			theme_adaptation : {
				
				stored_image_elements_by_collections : [],
				
				// {$images: , attr_name: , getAttr: , parent_selector: , child_selector: , html_version:, getImageBySrc:, extra_actions:, getElementHTML: , store_clone }
				storeImageElementsToImageCollection : function(params) {
					
					let html_version = params.html_version || 'html';
					if ( $.inArray(html_version, poip_product.theme_adaptation.stored_image_elements_by_collections) == -1 ) {
						
						poip_product.theme_adaptation.stored_image_elements_by_collections.push(html_version);
						params.$images.each(function(){
							let $image = $(this);
							let image = '';
							if ( params.getAttr ) {
								image = params.getAttr($image);	
							} else {
								let attr_name = params.attr_name || ( $image.is('img') ? 'src' : 'href' );
								image = $image.attr(attr_name);
							}
							
							let poip_img = params.getImageBySrc ? params.getImageBySrc(image) : poip_product.getImageBySrc(image);
							
							if ( poip_img ) {
								
								if ( params.extra_actions ) {
									params.extra_actions(poip_img, $image);
								}
								
								let $element = $image;
								if ( params.child_selector ) {
									$element = $element.find(params.child_selector).first();
								} else if ( params.parent_selector ) {
									$element = $element.closest(params.parent_selector);
								} 
								let html = params.getElementHTML ? params.getElementHTML($element) : poip_common.getOuterHTML( $element );
									
								poip_img[html_version] = html;
								
								if ( params.store_clone ) {
									if ( !poip_img.html_clone ) {
										poip_img.html_clone = {};
									}
									poip_img.html_clone[html_version] = $element.clone(true, true);
								}
								
							}
						});
					}
				},
				
				getStoredImagesContainer : function($put_before_element, $carousel_items, data_key) {
					data_key = data_key || 'images';
					var selector = '[data-poip="'+data_key+'"]';
					if ( !poip_product.getElement(selector).length ) {
						$put_before_element.before('<div data-poip="'+data_key+'" style="display:none;!important"></div>');
						$carousel_items.each(function(){
							poip_product.getElement(selector).append( poip_common.getOuterHTML($(this)) );
						});
					}
					return poip_product.getElement(selector);
				},
			
				updateShouldBeProcessed : function($carousel_items, href_attr_name, images_to_check, images, counter, carousel_is_ready, ignore_image_coincidence, params, poip_img_src ) {
					
					if ( !poip_product.custom_data.set_visible_images_is_called ) {
						if ( !carousel_is_ready ) {
							poip_product.timers.set_visible_images = setTimeout(function(){
								poip_product.custom_methods['setVisibleImages.instead'](images, counter+1, params);
							}, 100);
							return false;
						}
					}
						
					
					if ( (poip_product.custom_data.set_visible_images_is_called && !ignore_image_coincidence) || ignore_image_coincidence === 'CHECKALWAYS' ) {
						if ( poip_product.theme_adaptation.checkDisplayedImagesAreActual($carousel_items, href_attr_name, images_to_check, poip_img_src) ) {
							poip_product.works.set_visible_images = false;
							return false; // nothing to change
						}
					}
					poip_product.custom_data.set_visible_images_is_called = true;
					
					return true;
				},
				
				checkDisplayedImagesAreActual: function($carousel_items, href_attr_name, images_to_check, poip_img_src) {
					
					let current_imgs = [];
					$carousel_items.each( function(){
						current_imgs.push($(this).attr(href_attr_name));
					});
					
					if (poip_img_src) {
						current_imgs = poip_product.getSrcImagesBySrc(poip_img_src, current_imgs);
					}
					
					return current_imgs.toString() == images_to_check.toString();
				},
				
			},
		};
		
		if ( custom_setPoipProductCustomMethods ) { // custom
			custom_setPoipProductCustomMethods(poip_product);
		} else if ( typeof(setPoipProductCustomMethods) == 'function' ) { // global
			setPoipProductCustomMethods(poip_product);
		}
		
		return poip_product;
	})(jQuery);
}
