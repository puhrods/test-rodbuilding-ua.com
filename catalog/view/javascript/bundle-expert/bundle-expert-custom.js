//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
var BundleExpertCustom = {}

    BundleExpertCustom.constructor = function() {

    }

	BundleExpertCustom.update_cart_html = function(json){
				octPopupCart();

				if(typeof octYandexEcommerce == 'function') {
					octYandexEcommerce(json);
		        }

				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#oct-cart-quantity, .header-cart-index, #mobile_cart_index').html(json['total_count']);
					$('.us-cart-text').html(json['total_price']);
				}, 100);
    }
	
	BundleExpertCustom._update_product_page_price_html = function(pointer){
		var widget = pointer.widget
        var json = pointer.json
        var price = pointer.price
        var price_value = pointer.price_value;
        var special = pointer.special;
        var special_value = pointer.special_value;


        var price_selector = pointer.selectors.product_page.price;
        var special_selector = pointer.selectors.product_page.special;

        var html = '';

        if (typeof json !== 'undefined') {
			price = json['total_default'];
            price_value = json['total_default_value'];
            special = json['total_kit'];
            special_value = json['total_kit_value'];
			
			 if (typeof json['product_as_kit_total'] !== 'undefined') {
				price = json['product_as_kit_total_default'];
				special = json['product_as_kit_total'];
			}
        }

        if (price_value === special_value) {
			var html = '<span class="product-as-kit-price">' + price + '</span>'
		} else {
			var html = '';
			
			if (price_value !== '') {
				html += '<span class="product-as-kit-old-price">' + price + '</span>';
			}
			
			html += '<span class="product-as-kit-price be-product-as-kit-price-new">' + special + '</span>';
		}

		if (bundle_expert.first_change_price) {
			html = '<span class="product-price-container">' + html + '</span>';
			if(bundle_expert.selectors.product_page.price_parent!==''){
				$(price_selector).closest(bundle_expert.selectors.product_page.price_parent).html(html);
			}else{
				$(price_selector).parent().replaceWith(html);
			}
		} else {
			$(price_selector).html(html);
		}


		if((!bundle_expert.first_change_price && bundle_expert.animate_price)){
		// if (this.animate_price) {
			if (price_value === special_value) {
				be_animate_price.animatePrice(price_value);
			} else {
				be_animate_price.animatePrice(special_value);
				be_animate_price.animateOldPrice(price_value);
			}
		}

		bundle_expert.first_change_price = false;
		
	}
    
	
	BundleExpertCustom.show_bundle_errors = function (widget_element, json) {
    if (json['error']['product_as_kit']) {
        bundle_expert.show_product_as_kit_error(json['error']['product_as_kit']);
        delete json['error']['product_as_kit'];
        var t = 1;
    }

    //Если таблица с опциями, то ошибки выводим прямо в таблице
    if ($(widget_element).hasClass('kit-widget-table') && $(widget_element).hasClass('display-options-input')) {
        var table_mode_with_options = true;
    } else {
        var table_mode_with_options = false;
    }
    if (table_mode_with_options) {
        bundle_expert.show_widget_error(widget_element, json);
    } else {
        bundle_expert_form.form_error = json;
        var element = $(widget_element).find('.kit-item').first();
        bundle_expert.show_kit_form(element);
    }
}
	
    BundleExpertCustom.point = function(point_namber, pointer) {
        var func_name = 'point_' + point_namber;

        if (typeof window['be_custom'][func_name] === 'function'){
            return window['be_custom'][func_name](pointer);
        }
    }

    BundleExpertCustom.point_001 = function(pointer){
    }

	//Кнопка в корзине "Изменить комплект"
	//BundleExpertCustom.point_002 = function(pointer){
	//	$('#cart-popup').find('button.mfp-close').click();
    //}
	
	//BundleExpertCustom.point_022 = function(pointer){
		//var slider_container = pointer.slider_container;
		//$(slider_container).closest('.row.products').addClass('row-bundle-in-category');
    //}
	
	//Замена кнопок +-
	BundleExpertCustom.point_023 = function(pointer){
		
		var widget = pointer.widget;
		return;
		
		be_custom.unbind_quantity_buttons(true);
		
		var input_quantity_selector = bundle_expert.selectors.product_page.quantity;
		
		$(input_quantity_selector).unbind('change');
		
		$(input_quantity_selector).attr('onchange','be_custom.product_as_kit_quantity_changed();');
		//$(input_quantity_selector).attr('onkeyup','be_custom.product_as_kit_quantity_changed();');
		
		
    }

	//Обработчик замененых кнопок +-
	BundleExpertCustom.change_quantity = function(value){
		be_custom.unbind_quantity_buttons(false);
		
		var input_quantity_selector = bundle_expert.selectors.product_page.quantity;
		
		 var input_val = $(input_quantity_selector).val();
		 var quantity = parseInt(input_val)+value;

          if (quantity <=0) {
			quantity = $(input_quantity_selector).val(1);
        
      }else{
		  $(input_quantity_selector).val(quantity)
	  }
	  $(input_quantity_selector).change();
	}
	
	BundleExpertCustom.unbind_quantity_buttons = function(bind_mode){
		var btn_plus_selector = bundle_expert.selectors.product_page.button_plus;
		var btn_minus_selector = bundle_expert.selectors.product_page.button_minus;

		var input_quantity_selector = bundle_expert.selectors.product_page.quantity;
		
		var btn_plus = $(btn_plus_selector);
		var btn_minus = $(btn_minus_selector);
		
		
		$(btn_plus).unbind('click');
		$(btn_minus).unbind('click');
		
		if(bind_mode){
			$(btn_plus).attr('onclick','be_custom.change_quantity(1);');
			$(btn_minus).attr('onclick','be_custom.change_quantity(-1);');
		}
	}
	
	//Замена доп.кнопок "В коризину" для product_as_kit
	BundleExpertCustom.point_017 = function(pointer){
		var widget = pointer.widget;
		var repalced_button = $('#button-cart-replaced').first();
		if($(repalced_button).length>0){
			//$('#button-cart.head_title_button').attr('onclick','$("#button-cart-replaced").click();');
			
			
			//var clone = $(repalced_button).clone();
			//$('.cart_popup_img #button-cart').replaceWith(clone);
		}
    }
	
	//На странице товара product_as_kit изменено количество, обновляем цену
	//Надо вставить этот метод в нужное место, там где обрабатывается изменение количества	
	BundleExpertCustom.product_as_kit_quantity_changed = function(){
		var widget_unique_id = $('#button-cart-replaced').attr('data-widget-unique-id');
		var widget=$('.kit-widget[data-widget-unique-id='+widget_unique_id+']');
		bundle_expert.update_widget_kit_total(widget);
	}

