let options_pro_cat = {};
let ajax_ready = [];
let current_option_value; //Текущее значение опции
let current_product_option_id; //Текущая опция

$(document).on('change', '.options_pro_form [name^="option"]', function () {
	let form_data = $(this).parents('form').serialize();
	let product_id = $(this).parents('form').find('input[name="product_id"]').val();
	let parentLayer = $(this).parents('.product-layout');
	let clicked_object = this;

	//Определяем ведующую опцию
	if ($(this).prop('checked') == true) {
		current_option_value = $(this).val();
		current_product_option_id = $(this).attr('name').replace(/\D+/g, "");
	}

	if (ajax_ready[product_id] === undefined) {
		ajax_ready[product_id] = true;
	}
	if (typeof options_pro_cat[product_id] === 'undefined' && ajax_ready[product_id]) {
		ajax_ready[product_id] = false;
		$.ajax({
			url: 'index.php?route=extension/module/neoseo_product_options_pro/getProductOptions',
			type: 'post',
			data: form_data,
			dataType: 'json',
			success: function (json) {
				if (json['success']) {
					options_pro_cat[product_id] = json[product_id];
					updateProductData(product_id, parentLayer,clicked_object);
				} else {
					options_pro_cat[product_id] = false;
					return;
				}
				ajax_ready[product_id] = true;
			}
		});
	} else if (options_pro_cat[product_id] !== undefined && options_pro_cat[product_id] !== false) {
		updateProductData(product_id, parentLayer,clicked_object);
	}
});

function updateProductData(product_id, parentLayer , clicked_object) {
	let cur_value = $(clicked_object).val();
	let element_name = $(clicked_object).attr('name');
	let all_selected = [];

	$.each($('#product_options_form_' + product_id + ' [name^="option"]'), function ($key,element){
		if($(element).is(':selected') || $(element).is(':checked') ){
			let val = $(element).val();
			all_selected.push(val);
		}
	});

	$.each($('#product_options_form_' + product_id + ' [name^="option"] option'), function ($key,element){
		if($(element).is(':selected') || $(element).is(':checked') ){
			let val = $(element).val();
			if(Number(val) > 0) all_selected.push(val);
		}
	});

	$('#product_options_form_' + product_id + ' [name^="option"]').attr('disabled','disabled');
	$('#product_options_form_' + product_id + ' select[name^="option"]').removeAttr('disabled');
	$('#product_options_form_' + product_id + ' select[name^="option"] option').attr('disabled','disabled');

	let next_iteration = options_pro_cat[product_id]['json_array'];
	$.each(all_selected,function (key,value) {
		$('#product_options_form_' + product_id + ' [name^="option"][value="'+value+'"]').removeAttr('disabled');
		$('#product_options_form_' + product_id + ' option[value="'+value+'"]').removeAttr('disabled');

		if(typeof (next_iteration[value]) != 'undefined'){
			next_iteration = next_iteration[value];
		}
	});
	$.each(next_iteration, function (key,value) {
		$('#product_options_form_' + product_id + ' [name^="option"][value="'+key+'"]').removeAttr('disabled');
		$('#product_options_form_' + product_id + ' option[value="'+key+'"]').removeAttr('disabled');
	});

	// Надо проверить есть ли цена / картинка у этой комбинации
	console.log(next_iteration);
	console.log(options_pro_cat[product_id][cur_value]);
	if(typeof(next_iteration['article']) != 'undefined') {
		$('.area-sku-'+product_id).html(next_iteration['article']);
	}
	if(typeof(next_iteration['price']) != 'undefined') {
		$('.price-area-'+product_id).html(next_iteration['price']);
	}
	if(typeof(next_iteration['model']) != 'undefined') {
		$('.area-sku-'+product_id).html(next_iteration['model']);
	}
	if(typeof(options_pro_cat[product_id][cur_value]) != 'undefined'
		&& typeof(options_pro_cat[product_id][cur_value]['images']) != 'undefined'
		&& options_pro_cat[product_id][cur_value]['images'].length > 0
	) {
		let img = options_pro_cat[product_id][cur_value]['images'][0];
		if(typeof(img['popup']) != 'undefined'){
			$('img[data-main-image*="."][data-product_id="'+product_id+'"]').attr('src',img['popup']);
			$('.zoomWindowContainer').hide();
		}
	}

	// Дадим возможность редактировать послдений выбор
	if(all_selected.length == 1){
		$('#product_options_form_' + product_id + ' [name="'+element_name+'"]').removeAttr('disabled');
		$('#product_options_form_' + product_id + ' [name="'+element_name+'"] option').removeAttr('disabled');
	} else {
		all_selected.splice(all_selected.indexOf(cur_value),1);
		next_iteration = options_pro_cat[product_id]['json_array'];
		$.each(all_selected,function (key,value) {
			next_iteration = next_iteration[value];
		});
		$.each(next_iteration, function (key,value) {
			$('#product_options_form_' + product_id + ' [name="'+element_name+'"][value="'+key+'"]').removeAttr('disabled')
			$('#product_options_form_' + product_id + ' option[value="'+key+'"]').removeAttr('disabled');
		});
	}
}

$( document ).ready(function() {
	//$("<a class='options-button-reset' onClick='clearOptions(this);'>Сбросить значения</a>").appendTo($('.options_pro_form [name^="option"]').last().closest('.form-group').parent());
	$('.options_pro_form').each(function (){ $(this).append("<a class='options-button-reset' onClick='clearOptions(this);'>Сбросить значения</a>") });
});

function addToCart(product_id, prodduct_minimum, current_obj) {

	var form = $(current_obj).parents('.product-thumb').find('form#product_options_form_' + product_id);

	//Если у товара нет опций, вызываем стандартную функцию добавления товара в корзину
	if (form.length === 0) {
		cart.add(product_id, prodduct_minimum);
		return;
	}

	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: form.serialize(),
		dataType: 'json',
		beforeSend: function () {
			$('#button-cart').button('loading');
		},
		complete: function () {
			$('#button-cart').button('reset');
		},
		success: function (json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');

			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = form.find('#input-option' + i.replace('_', '-'));

						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}

				if (json['error']['recurring']) {
					$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
				}

				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}

			if (json['success']) {
				$('#popup-quickview > div').modal('hide');
				if(typeof(showCart) == 'function'){
					showCart(json);
				}
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart-total').html(json['total']);
				}, 100);

				$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function clearOptions(current_obj) {
	$(current_obj).parent().find('[name^="option"]').each(function () {
		$(this).attr('disabled', false);
		$(this).prop('checked', false);
		$(this).prop('selected', false);
	});
	$(current_obj).parent().find('select[name^="option"]').each(function () {
		// Для правильной работы с селектами
		$(this).find("option:selected").removeAttr("selected");
	});
	$(current_obj).parents().find('.cart-add-button').show();
	$(current_obj).parents().find('.cart-add-button').attr('disabled', false);
	$('#button-cart, #quick_order_block').show();
	$("#product input[name='quantity']").parent().show();
	$('#snwa-send-btn').remove();
	$('.snwa-send-btn').remove();
}
