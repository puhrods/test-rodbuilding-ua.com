var BundleExpertMini = {};
//22
BundleExpertMini.constructor = function (widgets) {
    this.widgets = [];
    this.is_some_checkout_page = false;//если страница корзины, то после добавления комплекта страницу надо побновить
    this.disable_cart_kit_edit_button = false;//В некоторых местах надо запрещать кнопку редактирования комплекта из корзины
    this.widget_total_block_width = 180;

    //Спец переменные для работы из админки
    this.admin_mode = false;
    this.admin_token = '';
    this.api_token_name = '';
    this.api_token_value = '';
    this.admin_mode_new_api = false;
    this.store_url = '';
    this.debug_mode = true;
    this.button_edit_kit = '';
    this.button_remove_kit = '';
    this.selectors = [];
    this.currency = [];
    this.animate_price = true;
    this.first_change_price = true;//в первый раз надо делать без анимации
    this.category_item_at_row = -1;
    this.window_scroll_selector='';//На накоторых шаблонах проверка lazy_load не срабатывает для windowz
    this.slideshow_loop=true;
}


BundleExpertMini.add_widgets = function (widgets) {
    for (var i = 0; i < widgets.length; i++) {
        this.widgets.push(widgets[i]);
    }

}

BundleExpertMini.init_bundle_expert_mini = function () {
    bundle_expert_mini.init_widgets();

    bundle_expert_mini.init_event_modal_form_dismiss();

}

BundleExpertMini.init_widgets = function () {

    var slider_container = '';
    var item_html = ''
    var category_product_selector = '';

    for (var i = 0; i < this.widgets.length; i++) {
        var widget = this.widgets[i];

        var widget_html = $('#bundle-expert-container .kit-widget[data-widget-unique-id=' + widget['unique_id'] + ']').first();

        switch (widget['display_mode']) {
            case 'module':

                widgets_container_unique_code = widget['config_module']['module_unique_id'];

                var slider_container_html = '';
                var widgets_container_selector = '';

                //var widgets_container_unique_code = btoa(widget['widget_id']);

                if (widget['slider_mode'] == 1) {
                    // slider_container_html = '<div class="" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" style="opacity: 1;"></div></div>';
                    slider_container_html = '<div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" style="opacity: 1;"></div>';
                    widgets_container_selector = '.bundle-expert-module-container[module-unique-id=' + widgets_container_unique_code + '] .bundle-expert-slideshow';
                } else {
                    // slider_container_html = '<div class="" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="bundle-expert-widget-items" style="opacity: 1;"></div></div>';
                    slider_container_html = '<div id="" class="bundle-expert-widget-items" style="opacity: 1;"></div>';
                    widgets_container_selector = '.bundle-expert-module-container[module-unique-id=' + widgets_container_unique_code + '] .bundle-expert-widget-items';
                }

                // widgets_container_selector = '.bundle-expert-module-container[module-unique-id=' + widgets_container_unique_code + '] .bundle-expert-slideshow';

                slider_container = $(widgets_container_selector).first();

                if ($(slider_container).length === 0) {
                    // slider_container_html = '<div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" style="opacity: 1;"></div>';

                    $('.bundle-expert-module-container[module-unique-id=' + widgets_container_unique_code + ']').append(slider_container_html);
                }

                slider_container = $(widgets_container_selector).first();

                item_html = '<div class="item">' + $(widget_html).get(0).outerHTML + '</div>';

                $(widget_html).remove();

                $(slider_container).append(item_html);

                break;
            case 'custom_page':
                var display_method = widget['config_custom_page']['display_method'];
                var selector = widget['config_custom_page']['selector'];
                var selector_mode = widget['config_custom_page']['selector_mode'];

                var widgets_container_unique_code = btoa(selector + selector_mode + widget['widget_id']);

                if (widget['slider_mode'] == 1) {
                    slider_container_html = '<div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" style="opacity: 1;"></div></div>';
                    widgets_container_selector = '[data-widgets-container-unique-code=\'' + widgets_container_unique_code + '\'] .bundle-expert-slideshow';
                } else {
                    slider_container_html = '<div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="bundle-expert-widget-items" style="opacity: 1;"></div></div>';
                    widgets_container_selector = '[data-widgets-container-unique-code=\'' + widgets_container_unique_code + '\'] .bundle-expert-widget-items';
                }

                // var slider_container_html = '<div class="col-xs-12" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" style="opacity: 1;"></div></div>';
                // var widgets_container_selector = '[data-widgets-container-unique-code=\'' + widgets_container_unique_code + '\'] .bundle-expert-slideshow';

                slider_container = $(widgets_container_selector).first();

                if ($(slider_container).length === 0) {
                    switch (display_method) {
                        case 'block':
                            switch (selector_mode) {
                                case 'after':
                                    $(selector).after(slider_container_html);
                                    break;
                                case 'before':
                                    $(selector).before(slider_container_html);
                                    break;
                                case 'insert':
                                    $(selector).html(slider_container_html);
                                    break;
                                case 'replace':
                                    $(selector).replaceWith(slider_container_html);
                                    break;
                            }

                            break;
                    }
                }

                slider_container = $(widgets_container_selector).first();

                $(slider_container).append(widget_html);

                break;
            case 'product_page':
                display_method = widget['config_product_page']['display_method'];
                selector = widget['config_product_page']['selector'];
                selector_mode = widget['config_product_page']['selector_mode'];

                widgets_container_unique_code = btoa(selector + selector_mode + widget['widget_id']);

                if (widget['slider_mode'] == 1) {
                    slider_container_html = '<div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" style="opacity: 1;"></div></div>';
                    widgets_container_selector = '[data-widgets-container-unique-code=\'' + widgets_container_unique_code + '\'] .bundle-expert-slideshow';
                } else {
                    slider_container_html = '<div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="bundle-expert-widget-items" style="opacity: 1;"></div></div>';
                    widgets_container_selector = '[data-widgets-container-unique-code=\'' + widgets_container_unique_code + '\'] .bundle-expert-widget-items';
                }

                // slider_container_html = '<div class="" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" style="opacity: 1;"></div></div>';
                // widgets_container_selector = '[data-widgets-container-unique-code=\'' + widgets_container_unique_code + '\'] .bundle-expert-slideshow';

                slider_container = $(widgets_container_selector).first();

                if ($(slider_container).length === 0) {
                    switch (display_method) {
                        case 'block':
                            switch (selector_mode) {
                                case 'after':
                                    $(selector).after(slider_container_html);
                                    break;
                                case 'before':
                                    $(selector).before(slider_container_html);
                                    break;
                                case 'insert':
                                    $(selector).html(slider_container_html);
                                    break;
                                case 'replace':
                                    $(selector).replaceWith(slider_container_html);
                                    break;
                            }

                            break;
                    }
                }

                if (($(widget_html).hasClass('product-as-kit-mode') || $(widget_html).hasClass('product-as-kit-mode-light-mode')) && widget['main_mode'] === 'kit') {
                    BundleExpertMini.update_product_page_price_html(widget_html);
                }

                slider_container = $(widgets_container_selector).first();

                var widget_html_lazy = '<div data-lazy-class="be-lazy-load" data-widget-lazy-container-unique-id="' + widget['unique_id'] + '"></div>'


                $(slider_container).append(widget_html_lazy);

                if ((widget['kit_as_product'] === "1" || widget['kit_as_product_light_mode'] === "1" ) && widget['main_mode'] === 'kit') {

                    if(typeof window['be_custom']['update_product_page_price_html'] !== 'function'){
                        $('.autocalc-product-special, .autocalc-product-price').hide();
                    }

                    this.product_as_kit_page_buttons_init(widget);



                }



                be_custom.point('017', {'widget': widget});


                break;
            case 'category_page':

                this.add_product_id_to_category_products(widget);

                category_product_selector = widget['config_category_page']['selector'];
                this.category_product_selector = category_product_selector;
                var rows_rate = 1;

                //Вычисляем сколько товаров в строке
                var tt = parseFloat($(category_product_selector).first().width());
                var tt2 = parseFloat($(category_product_selector).first().parent().width());
                //var width_percent = (100 * parseFloat($(category_product_selector).first().css('width')) / parseFloat($(category_product_selector).first().parent().css('width')));
                var width_percent = (100 * parseFloat($(category_product_selector).first().width() / parseFloat($(category_product_selector).first().parent().width())));
                // var item_at_row = Math.round(100 / width_percent);
                var item_at_row = Math.floor(100 / width_percent);
                var item_at_row = Math.round(100 / width_percent);
                this.category_item_at_row = Math.floor(100 / width_percent);
                this.category_item_at_row = Math.round(100 / width_percent);

                //Теперь выводим виджет рядом с товаром, после последнего товара в строке
                //Ищем товар по id и вычисляем номер его строки
                var product_position = this.find_product_position(widget['main_product_id'], widget['config_category_page']['selector']);
                var row_index = Math.floor(product_position / item_at_row);

                //Индекс последнего товара в строке
                var element_index = item_at_row * rows_rate + item_at_row * rows_rate * row_index - 1;

                widgets_container_unique_code = btoa(selector + selector_mode + widget['widget_id']);

                //Вставляем
                //Если еще нет slider container, то вставляем его
                slider_container = $('.bundle-expert-slideshow[row-index=' + row_index + ']').first();
                if ($(slider_container).length === 0) {
                    var html = '<div class="col-xs-12 col-12 bootstrap-style be-category-slider"><div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" row-index="' + row_index + '" style="opacity: 1;"></div></div></div>';

                    for (var j = 0; j < item_at_row; j++) {
                        var last_row_product = $(category_product_selector)[element_index - j];
                        if (typeof last_row_product !== 'undefined')
                            break;
                    }


                    $(last_row_product).after(html);

                    $(last_row_product).parent().addClass('be-has-category-slider');

                }

                slider_container = $('.bundle-expert-slideshow[row-index=' + row_index + ']').first();

                var widget_html_lazy = '<div data-lazy-class="be-lazy-load" data-widget-lazy-container-unique-id="' + widget['unique_id'] + '"></div>'
                item_html = '<div class="item">' + widget_html_lazy + '</div>';
                $(slider_container).append(item_html);

                be_custom.point('022', {'slider_container': slider_container});

                break;
            case 'cart_page':
            case 'checkout_page':
                if (widget['display_mode'] === 'cart_page') {
                    display_method = widget['config_cart_page']['display_method'];
                    selector = widget['config_cart_page']['selector'];
                    selector_mode = widget['config_cart_page']['selector_mode'];
                } else {
                    display_method = widget['config_checkout_page']['display_method'];
                    selector = widget['config_checkout_page']['selector'];
                    selector_mode = widget['config_checkout_page']['selector_mode'];
                }

                widgets_container_unique_code = btoa(selector + selector_mode + widget['widget_id']);

                if (widget['slider_mode'] == 1) {
                    slider_container_html = '<div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" style="opacity: 1;"></div></div>';
                    widgets_container_selector = '[data-widgets-container-unique-code=\'' + widgets_container_unique_code + '\'] .bundle-expert-slideshow';
                } else {
                    slider_container_html = '<div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="bundle-expert-widget-items" style="opacity: 1;"></div></div>';
                    widgets_container_selector = '[data-widgets-container-unique-code=\'' + widgets_container_unique_code + '\'] .bundle-expert-widget-items';
                }

                slider_container = $(widgets_container_selector).first();

                if ($(slider_container).length === 0) {
                    switch (display_method) {
                        case 'block':
                            switch (selector_mode) {
                                case 'after':
                                    $(selector).after(slider_container_html);
                                    break;
                                case 'before':
                                    $(selector).before(slider_container_html);
                                    break;
                                case 'insert':
                                    $(selector).html(slider_container_html);
                                    break;
                                case 'replace':
                                    $(selector).replaceWith(slider_container_html);
                                    break;
                            }

                            break;
                    }
                }

                slider_container = $(widgets_container_selector).first();

                $(slider_container).append(widget_html);

                break;

        }

        if (widget['main_mode'] !== 'series') {

            bundle_expert_mini.setWidgetUpdateTotalEnable(widget_html, false);

            //Для выбранных опций назначаем класс be-selected-option
            $(widget_html).find('.product-options-values .form-group').each(function (index, group) {
                bundle_expert_mini.update_active_option_class(group);
            })

            bundle_expert_mini.setWidgetUpdateTotalEnable(widget_html, true);
            //bundle_expert_mini.update_widget_kit_total(widget);
        }

        //В категориях для товаров добавляем специальные классы, чтобы  товары отображались корректно
        //когда между ними есть комплекты
        if(this.category_item_at_row>0) {
            $('.be-has-category-slider').find(this.category_product_selector).addClass('be-clear-none');
            // $('.be-has-category-slider').find('.be-category-slider').next().removeClass('be-clear-none').addClass('be-clear-left');
            $('.be-has-category-slider').find(this.category_product_selector).each(function (index, element) {
                var tt = index % bundle_expert_mini.category_item_at_row;
                if (index % bundle_expert_mini.category_item_at_row == 0) {
                    $(element).removeClass('be-clear-none');
                    $(element).addClass('be-clear-left');
                }
            })
        }

        be_custom.point('014', {'widget_html': widget_html});


    }



    // $('.kit-widget').each(function (index, element) {
    //     bundle_expert.init_upload_file_buttons_in_widget(element);
    //
    // })
    // bundle_expert.init_time_field_in_widgets();
}

BundleExpertMini.update_product_page_price_html = function (widget, json) {


    if ($(widget).hasClass('product-as-kit-mode') || $(widget).hasClass('product-as-kit-mode-light-mode')) {

        if ('product_page' in bundle_expert_mini.selectors) {
            var price_selector = bundle_expert_mini.selectors.product_page.price;
            // var special_selector = bundle_expert_mini.selectors.product_page.special;
            var special_selector = '.product-special-container';

            //$(special_selector).hide();

            var price = $(widget).find('.be-price-old.total-default').html();
            var price_value = $(widget).find('.be-price-old.total-default').attr('data-price-value');

            if (price_value === '') {
                price = $(widget).find('.total-default-new').html();
                price_value = $(widget).find('.total-default-new').attr('data-price-value');
            }
            //if (typeof price === 'undefined') {
            //    price = $(widget).find('.be-price-old.total-kit-old').html();
            //}

            var special = $(widget).find('.total-kit').html();
            var special_value = $(widget).find('.total-kit').attr('data-price-value');

            $(price_selector).attr('widget-handler-id', $(widget).attr('data-widget-unique-id'));

            if (typeof window['be_custom']['update_product_page_price_html'] === 'function') {

                if (typeof json !== 'undefined') {
                    price = json['product_as_kit_total_default'];
                    special = json['product_as_kit_total'];
                }

                var pointer = {
                    'widget': widget,
                    'selectors': bundle_expert_mini.selectors,
                    'price': price,
                    'price_value': price_value,
                    'special': special,
                    'special_value': special_value
                };

                if (typeof json !== 'undefined') {
                    pointer.json = json;
                }

                be_custom.update_product_page_price_html(pointer);
            } else {

                $(special_selector).hide();

                if (price_value === special_value) {
                    var html = '<span class="product-as-kit-price">' + price + '</span>'
                } else {
                    var html = '';
                    if (price_value !== '') {
                        html += '<span class="product-as-kit-old-price">' + price + '</span>';
                    }
                    html += '<span class="product-as-kit-price be-product-as-kit-price-new">' + special + '</span>';
                }

                if (this.first_change_price) {
                    html = '<span class="product-price-container">' + html + '</span>';
                    if(bundle_expert_mini.selectors.product_page.price_parent!==''){
                        $(price_selector).closest(bundle_expert_mini.selectors.product_page.price_parent).html(html);
                    }else{
                        $(price_selector).parent().replaceWith(html);
                    }
                } else {
                    $(price_selector).html(html);
                }


                if((!this.first_change_price && this.animate_price)){
                    // if (this.animate_price) {
                    if (price_value === special_value) {
                        be_animate_price.animatePrice(price_value);
                    } else {
                        be_animate_price.animatePrice(special_value);
                        be_animate_price.animateOldPrice(price_value);
                    }
                }

                this.first_change_price = false;
            }
        }
    }

}

//Замена кнопопк на странице товара для режима "Комплект как товар"
BundleExpertMini.product_as_kit_page_buttons_init = function (widget) {
    if(typeof window['be_custom']['product_as_kit_page_buttons_init'] !== 'function'){
        var button_selector = bundle_expert_mini.selectors['product_page']['button'];

        $(button_selector).each(function (index, element) {
            //var button = $(button_selector).first();
            var button = element;

            //Замена кнопки "Купить"
            if ($(button).length > 0) {
                var button_clone = $(button).clone();
                var widget_unique_id = widget['unique_id'];
                var onclick_attr = "bundle_expert.add_to_cart_by_single_product('" + widget_unique_id + "', false);";
                var id_attr = $(button_clone).attr('id') + '-replaced';
                $(button_clone).attr('onclick', onclick_attr);
                $(button_clone).attr('id', id_attr);
                $(button_clone).attr('data-widget-unique-id', widget_unique_id);
                $(button).replaceWith(button_clone);
            }
        })

        //1580
        //Обработчик при смене опций главного товара
        var product_as_kit_options = $(bundle_expert_mini.selectors.product_page.product_data).find('input[name^=option][type=\'radio\'], input[name^=option][type=\'checkbox\'], select[name^=option]');
        if (typeof product_as_kit_options !== 'undefined') {
            $(product_as_kit_options).each(function (index, option_element) {
                var onchange_text = 'bundle_expert.on_changed_product_as_kit_option(this);';
                $(option_element).attr('onchange', onchange_text);
                $(option_element).attr('data-widget-unique-id',  widget['unique_id']);
            })

        }


        //if(typeof window['be_custom']['update_product_page_price_html'] !== 'function'){
        if (bundle_expert_mini.animate_price) {

            //Замена кнопок "Плюс" "Минус"
            if (bundle_expert_mini.selectors.product_page.button_plus !== '') {
                var btn_plus_selector = bundle_expert_mini.selectors.product_page.button_plus;
                var btn_plus_clone = $(btn_plus_selector).first().clone();
                $(btn_plus_selector).replaceWith(btn_plus_clone);
                $(btn_plus_clone).unbind('click');
                $(btn_plus_clone).attr('onclick', 'bundle_expert.product_as_kit_page_change_quantity(1);');

            }
            if (bundle_expert_mini.selectors.product_page.button_minus !== '') {
                var btn_minus_selector = bundle_expert_mini.selectors.product_page.button_minus;
                var btn_minus_clone = $(btn_minus_selector).first().clone();
                $(btn_minus_selector).replaceWith(btn_minus_clone);
                $(btn_minus_clone).unbind('click');
                $(btn_minus_clone).attr('onclick', 'bundle_expert.product_as_kit_page_change_quantity(-1);');

            }
            if (bundle_expert_mini.selectors.product_page.quantity !== '') {
                var input_quantity_selector = bundle_expert_mini.selectors.product_page.quantity;
                var input_quantity_clone = $(input_quantity_selector).first().clone();

                $(input_quantity_selector).replaceWith(input_quantity_clone);
                $(input_quantity_selector).attr('data-widget-unique-id', widget['unique_id'])

                $(input_quantity_clone).unbind('change');
                $(input_quantity_clone).attr('onchange', 'bundle_expert.product_as_kit_page_quantity_changed();');
                //$(input_quantity_clone).attr('onkeyup', 'bundle_expert.product_as_kit_page_quantity_changed();');
            }

        }
        //}
    }else{
        be_custom.product_as_kit_page_buttons_init(widget);
    }


    be_custom.point('023', {'widget': widget});
}

BundleExpertMini.add_product_id_to_category_products = function (widget) {
    category_product_selector = widget['config_category_page']['selector'];

    $(category_product_selector).each(function (index, element) {
        var added = $(element).find('.bundle-expert-product-id');
        if($(added).length===0){
            var html = '<div class="bundle-expert-product-id" style="display: none" data-product-id="'+bundle_expert_mini.category_products[index]+'"></div>';
            $(element).append(html);
        }else{
            return false;
        }
    })
}

//На странице категории ищем элемент продукта
BundleExpertMini.find_product_position = function (product_id, selector) {

    var product_position = -1;

    $(selector).find('.bundle-expert-product-id').each(function (index, element) {
        if (product_position < 0) {
            var item_product_id = $(element).attr('data-product-id');

            if (item_product_id === product_id) {
                product_position = index;
            }
        }
    });

    return product_position;
}

BundleExpertMini.setWidgetUpdateTotalEnable = function (widget, enable) {
    if(enable){
        $(widget).removeClass('not-update-total');
    }else{
        $(widget).addClass('not-update-total');
    }
}

BundleExpertMini.update_active_option_class = function (form_group) {
    $(form_group).find('label').removeClass('be-selected-option');
    $(form_group).find('input[type="radio"]:checked, input[type="checkbox"]:checked').closest('label').addClass('be-selected-option');

    be_custom.point('020', {'form_group': form_group});
}


BundleExpertMini.init_widgets_2 = function (){

    $('div[data-lazy-class=be-lazy-load]').each(function (index, element) {
        var widget_unique_id = $(element).attr('data-widget-lazy-container-unique-id');
        var widget = $('#bundle-expert-container .bundle-expert-widgets-container').find('.kit-widget[data-widget-unique-id='+widget_unique_id+']');
        if($(widget).length>0){
            $(element).replaceWith(widget);
        }

    })

    //Устанавливаем максимальное значение ширины слайдеров в зависимости от максимальной ширины виджетов внутри
    $('.be-main-widgets-container, .bundle-expert-module-container').each(function (index, slider) {
        var slider_max_width = -1;
        $(slider).find('.kit-widget').each(function (index2, widget) {
            var max_width = parseInt($(widget).attr('data-max-width'));
            if(!isNaN(max_width)) {
                if (max_width > slider_max_width) {
                    slider_max_width = max_width;
                }
            }
        })

        if (slider_max_width > 0) {
            $(slider).css("max-width", slider_max_width + "px");
        }

    })

    //При наведении указателя на товар показываем соотв комплект в слайдере
    category_product_selector = this.category_product_selector;

    $(category_product_selector).each(function (index, element) {
        var product_element = element;

        var product_id = $(product_element).find('.bundle-expert-product-id').attr('data-product-id');

        var product_widget = $('.owl-carousel-bundle-expert.bundle-expert-slideshow').find('.kit-widget[data-main-product-id=' + product_id + ']').first();

        if ($(product_widget).length > 0) {

            var timer_control = '';
            $(product_element).mouseenter(function () {


                timer_control = setTimeout(function () {
                    var product_kit_widget = $('.owl-carousel-bundle-expert.bundle-expert-slideshow').find('.kit-widget[data-main-product-id=' + product_id + ']').first();
                    var slideshow = $(product_kit_widget).closest('.owl-carousel-bundle-expert');
                    var slideshow_item = $(product_kit_widget).closest('.owl-item');
                    var slideshow_items = $(slideshow_item).closest('.owl-stage').children('.owl-item');
                    var slideshow_item_index = $(slideshow_items).index(slideshow_item);

                    //Инициализация карусели товаров в виджетах слайдшоу
                    bundle_expert_mini.init_slideshow_widgets_product_carousel(slideshow, false);

                    $(slideshow).trigger('to.owl.carousel', [slideshow_item_index, 500, true]);
                }, 1000);


            });

            $(product_element).mouseleave(function () {
                clearTimeout(timer_control);
            });
        }
    })


    bundle_expert_mini.init_slideshow('.bundle-expert-slideshow');

    var slideshows = $('.bundle-expert-widget-items, .bundle-expert-slideshow').each(function (index, slideshow) {

        if($(slideshow).hasClass('bundle-expert-slideshow')){
            bundle_expert_mini.init_slideshow_widgets_product_carousel(slideshow, true);
        }else{
            bundle_expert_mini.init_slideshow_widgets_product_carousel(slideshow, false);
        }

    })


}

BundleExpertMini.init_widgets_ajax_filter = function (widgets_html, widgets_json) {
    $('.bundle-expert-widgets-container').html(widgets_html);

    var widgets = JSON.parse(widgets_json);

    bundle_expert_mini.widgets = [];
    bundle_expert_mini.add_widgets(widgets);
    bundle_expert_mini.init_widgets();
}

BundleExpertMini.init_slideshow = function (selector) {
    if (typeof window['be_custom']['init_slideshow'] === 'function') {
        be_custom.init_slideshow(selector);
    }else{
        $(selector).each(function (index, element) {
            var length = $(element).find('.kit-widget').length;
            if(length>1){
                $(element).addClass('multi-kit-slideshow');
            }else{
                //1576
                $(element).removeClass('owl-carousel').removeClass('bundle-expert-slideshow');

                //Инициализация карусели товаров здесь, т.к в слайдшоу только один виджет
                bundle_expert_mini.init_slideshow_widgets_product_carousel(element, false);

            }

        });
        var slideshow_elements = $(selector);
        if($(slideshow_elements).length>0) {
            $(selector).owlCarouselBundleExpert({
                items: 1,
                autoplay: false,
                nav: true,
                navText: ['<i class="fa fa-angle-left fa-5x"></i>', '<i class="fa fa-angle-right  fa-5x"></i>'],
                // singleItem: true,
                // navigation: true,
                // navigationText: ['<i class="fa fa-arrow-left fa-5x"></i>', '<i class="fa fa-arrow-right fa-5x"></i>'],
                dots: true,
                //loop:bundle_expert_mini.slideshow_loop,
                rewind: bundle_expert_mini.slideshow_loop,
                onInitialized: this.slideshowHasBeenInitialized
            });
        }




    }




}

BundleExpertMini.slideshowHasBeenInitialized = function (event) {

    var element   = event.target;

    $(element).addClass('slideshow-initialized-complete');

    //Изменяем высоту боковых кнопок
    $(element).on('changed.owl.carousel', function(event) {

        //var slideshow = $(this).closest('.owl-carousel');


        bundle_expert_help.on_slideshow_change_item(event);
    });
    //1581
    $(element).on('drag.owl.carousel', function(event) {
        var slideshow = $(this).closest('.owl-carousel');
        bundle_expert_mini.init_slideshow_widgets_product_carousel(slideshow, false);
    });
    // $(element).on('next.owl.carousel', function(event) {
    //     var slideshow = $(this).closest('.owl-carousel');
    //     bundle_expert_mini.init_slideshow_widgets_product_carousel(slideshow, false);
    // });

    //При нажатии кнопки Инициализируем карусели для всех виджетов кроме первого, он был инициализирован сразу
    var next_button = $(element).find('.owl-nav .owl-next');
    $(next_button).bind('click', function () {
        var slideshow = $(this).closest('.owl-carousel');
        bundle_expert_mini.init_slideshow_widgets_product_carousel(slideshow, false);
    })
    var dots_button = $(element).find('.owl-dots .owl-dot');
    $(dots_button).bind('click', function () {
        var slideshow = $(this).closest('.owl-carousel');
        bundle_expert_mini.init_slideshow_widgets_product_carousel(slideshow, false);
    })

}

//Инициализация карусели товаров в виджетах слайдшоу
BundleExpertMini.init_slideshow_widgets_product_carousel = function (slideshow, only_first) {

    if(only_first){
        var widgets = $(slideshow).find('.kit-widget:not(#bundle-expert-container .kit-widget)');

        //Только первый превращаем в карусель, остальные потом по нажатию на кнопку вправо
        //в методе BundleExpert.slideshowHasBeenInitialized
        widgets = $(widgets).first();

        $(widgets).each(function (index, el) {
            var unique_id = $(el).attr('data-widget-unique-id');
            bundle_expert_mini.init_carousel('#bundle-expert-widget-carousel-' + unique_id, -1)
        })
    }else{
        var not_initialized_carousels = $(slideshow).find('.kit-widget .owl-carousel:not(.owl-loaded)');

        if($(not_initialized_carousels).length>0){
            var widgets = $(not_initialized_carousels).closest('.kit-widget ');

            $(widgets).each(function (index, el) {
                var unique_id = $(el).attr('data-widget-unique-id');
                bundle_expert_mini.init_carousel('#bundle-expert-widget-carousel-' + unique_id, -1)
            })
        }
    }

}



//Инициализация карусели
//Если container_width==-1, значит ширина контейнера уже задана, иначе надо вычислять ширину контейнера
BundleExpertMini.init_carousel = function (selector, container_width) {
    if (typeof window['be_custom']['init_carousel'] === 'function') {
        be_custom.init_carousel(selector, container_width);
    }else{
        var items = 3;

        var is_kit_form = $(selector).closest('.kit-form').length;

        var kit_items_count = $(selector).find('.kit-item').length;

        if (is_kit_form) {
            var kit_item_width = $(selector).closest('.bottom-block-content').attr('data-image-width') * 2
            if (container_width === -1) {
                var container = $(selector).first();
                // container_width = Math.trunc($(container).width());
                container_width = Math.round($(container).width());
            }

            if (container_width < kit_items_count * kit_item_width) {
                var center = true;
                // var center = false;
            } else {
                var center = false;
            }


            // var center = true;
            // var center = false;
            var nav = false;
        } else {
            var kit_item_width = $(selector).closest('.kit-widget').attr('data-image-width') * 2;
            if (container_width === -1) {
                var container = $(selector).first();
                // container_width = Math.trunc($(container).width());
                container_width = Math.round($(container).width());
            }
            container_width -= bundle_expert_mini.widget_total_block_width;

            if (container_width + bundle_expert_mini.widget_total_block_width < 478) {
                $(selector).closest('.kit-widget').addClass('widget-vertical-mode');
                container_width += bundle_expert_mini.widget_total_block_width;
            }

            var center = false;
            var nav = true;

        }

        // //если элемент 1, то центрируем его
        // if(kit_items_count==1)
        //     var center = true;
        //
        // var center = false;
        if (kit_items_count * kit_item_width > container_width) {
            // items = Math.trunc(container_width / kit_item_width);
            items = Math.round(container_width / kit_item_width);
            if (items === 0)
                items = 1;
        } else {
            items = kit_items_count;
        }

        var slideshow_elements = $(selector);
        if($(slideshow_elements).length>0) {
            $(selector).owlCarouselBundleExpert({
                items: items,
                // autoPlay: false,
                nav: nav,
                navText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
                dots: false,
                // center: false,
                onInitialized: this.carouselHasBeenInitialized
            });
        }
    }


}

BundleExpertMini.carouselHasBeenInitialized = function (event) {
    var element   = event.target;
    var slideshow_element = $(element).closest('.bundle-expert-slideshow');
    //Изменяем высоту боковых кнопок
    if($(slideshow_element).hasClass('slideshow-initialized-complete')){
        //$(slideshow_element).trigger('refresh.owl.carousel')
        // $(slideshow_element).trigger('changed.owl.carousel');

        bundle_expert_help.change_slideshow_side_buttons_height(slideshow_element);
    }


}

BundleExpertMini.init_event_modal_form_dismiss = function () {
    $('#bundle-expert-form-modal').on('hide.bs.modal', function () {
        bundle_expert_form.hide_kit_form();
    })
    $('#bundle-expert-form-modal').on('hidden.bs.modal', function () {
        be_custom.point('036', {'modal': this});
    })
    $('#bundle-expert-form-modal').on('shown.bs.modal', function () {
        var kit_from_cart = $('#bundle-expert-form-modal').find('.modal-content').attr('kit_from_cart');

        if (kit_from_cart == 0) {
            bundle_expert_form.show_kit_form();
        } else {
            bundle_expert_form.show_kit_form_from_cart();
        }


    })
}

BundleExpertMini.init_lazy_sliders = function () {
    $('.be-lazy-load').each(function (index, element) {
        if (bundle_expert_mini_help.isInViewport(element)) {



            $('.be-lazy-load').removeClass('be-lazy-load');

            $('.be-main-widgets-container').show();

            bundle_expert_mini.init_widgets_2();

            return false;
        } else {
            // do something else
        }
    })
}



var BundleExpertMiniHelp = {};

BundleExpertMiniHelp.constructor = function () {
    if (!String.prototype.startsWith) {
        String.prototype.startsWith = function(searchString, position) {
            position = position || 0;
            return this.indexOf(searchString, position) === position;
        };
    }
}




BundleExpertMiniHelp.change_slideshow_side_buttons_height = function (widget) {
    var height = $(widget).height();

    var slideshow = $(widget).closest('.bundle-expert-slideshow');

    if($(slideshow).length>0){
        var yy =  $(slideshow).children('.owl-nav').find('button');
        // $(element).children('.owl-nav').find('button').height(height);
        $(slideshow).children('.owl-nav').find('button').css({ 'height': height + "px" });
    }


}

BundleExpertMiniHelp.uniqid = function () {
    return '' + Math.random().toString(36).substr(2, 9);
};



BundleExpertMiniHelp.isInViewport = function (elem) {
    if( elem.length == 0 ) {
        return;
    }
    var $window = jQuery(window)
    var viewport_top = $window.scrollTop()
    var viewport_height = $window.height()
    var viewport_bottom = viewport_top + viewport_height

    var vis = $(elem).is(":visible");
    if (!vis)
        $(elem).show();  // must be visible to get .position





    var $elem = jQuery(elem)
    var top = $elem.offset().top
    var height = $elem.height()
    var bottom = top + height

    if (!vis)
        $(elem).hide();

    return (top >= viewport_top && top < viewport_bottom) ||
        (bottom > viewport_top && bottom <= viewport_bottom) ||
        (height > viewport_height && top <= viewport_top && bottom >= viewport_bottom)
};

var BundleExpertAnimatePrice = {};

BundleExpertAnimatePrice.constructor = function () {
    this.animate_delay = 20;
    this.price_stop = 0;
    this.price_start = 0;
    this.price_step = 0;
    this.price_timer_id = 0;

    this.old_price_stop = 0;
    this.old_price_start = 0;
    this.old_step = 0;
    this.old_timer_id = 0;
}

BundleExpertAnimatePrice.format_price = function (price) {
    var string = '';

    if (bundle_expert_mini.currency.symbol_left!=='') {
        string += bundle_expert_mini.currency.symbol_left;
    }

    string += be_animate_price.formatMoney(price, bundle_expert_mini.currency.decimal_place, bundle_expert_mini.currency.decimal_point, bundle_expert_mini.currency.thousand_point);;

    if (bundle_expert_mini.currency.symbol_right!=='') {
        string += bundle_expert_mini.currency.symbol_right;
    }

    return string;
}

BundleExpertAnimatePrice.formatMoney = function (number, decPlaces, decSep, thouSep) {
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSep = typeof decSep === "undefined" ? "." : decSep;
    thouSep = typeof thouSep === "undefined" ? "," : thouSep;
    var sign = number < 0 ? "-" : "";
    var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
    // var j = (j = i.length) > 3 ? j % 3 : 0;
    var j = (j = i.length) > 3 ? j - 3 : 0;

    var result =  sign +
        (j ? i.substr(0, j) + thouSep : "") +
        i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) +
        (decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");

    return result;
 }

BundleExpertAnimatePrice.animatePrice_callback = function () {
    be_animate_price.price_start += be_animate_price.price_step;
    if ((be_animate_price.price_step > 0) && (be_animate_price.price_start > be_animate_price.price_stop)) {
        be_animate_price.price_start = be_animate_price.price_stop;
    } else if ((be_animate_price.price_step < 0) && (be_animate_price.price_start < be_animate_price.price_stop)) {
        be_animate_price.price_start = be_animate_price.price_stop;
    } else if (be_animate_price.price_step === 0) {
        be_animate_price.price_start = be_animate_price.price_stop;
    }
    // $('.autocalc-product-price').html(price_format(this.price_start));
    $('.product-price-container .product-as-kit-price').html(be_animate_price.format_price(be_animate_price.price_start));
    if (be_animate_price.price_start !== be_animate_price.price_stop) {
        be_animate_price.price_timer_id = setTimeout(be_animate_price.animatePrice_callback, be_animate_price.animate_delay);
    }
}
BundleExpertAnimatePrice.animatePrice = function (price) {
    var quantity = Number($(bundle_expert_mini.selectors['product_page']['quantity']).val());
    be_animate_price.price_start = be_animate_price.price_stop;
    be_animate_price.price_stop = parseFloat(price) * quantity;
    be_animate_price.price_step = (be_animate_price.price_stop - be_animate_price.price_start) / 10;
    clearTimeout(be_animate_price.price_timer_id);
    be_animate_price.price_timer_id = setTimeout(be_animate_price.animatePrice_callback, be_animate_price.animate_delay);
}

BundleExpertAnimatePrice.animateOldPrice_callback = function () {
    be_animate_price.old_price_start += be_animate_price.old_step;
    if ((be_animate_price.old_step > 0) && (be_animate_price.old_price_start > be_animate_price.old_price_stop)) {
        be_animate_price.old_price_start = be_animate_price.old_price_stop;
    } else if ((be_animate_price.old_step < 0) && (be_animate_price.old_price_start < be_animate_price.old_price_stop)) {
        be_animate_price.old_price_start = be_animate_price.old_price_stop;
    } else if (be_animate_price.old_step === 0) {
        be_animate_price.old_price_start = be_animate_price.old_price_stop;
    }
    // $('.autocalc-product-price').html(price_format(this.price_start));
    $('.product-price-container .product-as-kit-old-price').html(be_animate_price.format_price(be_animate_price.old_price_start));
    if (be_animate_price.old_price_start !== be_animate_price.old_price_stop) {
        be_animate_price.old_timer_id = setTimeout(be_animate_price.animateOldPrice_callback, be_animate_price.animate_delay);
    }
}

BundleExpertAnimatePrice.animateOldPrice = function (price) {
    var quantity = Number($(bundle_expert_mini.selectors['product_page']['quantity']).val());
    be_animate_price.old_price_start = be_animate_price.old_price_stop;
    be_animate_price.old_price_stop = parseFloat(price) * quantity;
    be_animate_price.old_step = (be_animate_price.old_price_stop - be_animate_price.old_price_start) / 10;
    clearTimeout(be_animate_price.old_timer_id);
    be_animate_price.old_timer_id = setTimeout(be_animate_price.animateOldPrice_callback, be_animate_price.animate_delay);
}

var bundle_expert_mini = '';

var bundle_expert_mini_help = '';
var be_animate_price = '';
var be_custom = '';

var BundleExpertCustomDefault = {};

BundleExpertCustomDefault.constructor = function (widgets) {
}
BundleExpertCustomDefault.point = function (v1, v2) {
}




BundleExpertMini.init = function () {

    BundleExpertMini.constructor();
    bundle_expert_mini = BundleExpertMini;

    // BundleExpertForm.constructor();
    // bundle_expert_form = BundleExpertForm;

    BundleExpertMiniHelp.constructor();
    bundle_expert_mini_help = BundleExpertMiniHelp;

    be_animate_price = BundleExpertAnimatePrice;
    be_animate_price.constructor();

    if (typeof BundleExpertCustom === 'function') {
        be_custom = new BundleExpertCustom();
    } else {
        if (typeof BundleExpertCustom != "undefined") {
            BundleExpertCustom.constructor();
            be_custom = BundleExpertCustom;
        } else {
            BundleExpertCustomDefault.constructor();
            be_custom = BundleExpertCustomDefault;
        }
    }


    if ($('#bundle-expert-container #bundle-expert-class-data').length > 0) {
        bundle_expert_mini.reload_item_products = parseInt($('#becd-reload_item_products').html());
        bundle_expert_mini.is_some_checkout_page = $('#becd-is_some_checkout_page').html() === 'true';
        bundle_expert_mini.disable_cart_kit_edit_button = $('#becd-disable_cart_kit_edit_button').html() === 'true';
        bundle_expert_mini.animate_price = $('#becd-animate_price').html() === 'true';
        bundle_expert_mini.button_remove_kit = $('#becd-button_remove_kit').html();
        bundle_expert_mini.button_edit_kit = $('#becd-button_edit_kit').html();

        var widgets_json = $('#bundle-expert-container #widgets-json-container').html();
        var widgets = JSON.parse(widgets_json);

        var selectors_json = $('#bundle-expert-container #selectors-json-container').html();
        var selectors = JSON.parse(selectors_json);

        bundle_expert_mini.selectors = selectors;

        var currency_json = $('#bundle-expert-container #currency-json-container').html();
        var currency = JSON.parse(currency_json);

        var category_products_json = $('#bundle-expert-container #category-products-json-container').html();
        var category_products = JSON.parse(category_products_json);
        bundle_expert_mini.category_products = category_products;


        bundle_expert_mini.currency = currency;

        be_custom.point('024', {});

        bundle_expert_mini.add_widgets(widgets);
    }


    be_custom.point('025', {});

    bundle_expert_mini.init_bundle_expert_mini();


    bundle_expert_mini.init_lazy_sliders();

    $(window).on('resize scroll', function () {
        bundle_expert_mini.init_lazy_sliders();
    });

    //На некоторых шаблонах предыдущий может не срабатывать, поэтому определяем конкретный селектор
    //(определить его в 025)
    if(bundle_expert_mini.window_scroll_selector!==''){
        $(bundle_expert_mini.window_scroll_selector).on('resize scroll', function () {
            bundle_expert_mini.init_lazy_sliders();
        });
    }

}


BundleExpertMini.init_main_check = function () {
    if(bundle_expert===''){
        BundleExpert.init();
    }
}

BundleExpertMini.init_cart_kit_edit_button = function () {

    //В некоторых местах надо запрещать кнопку редактирования комплекта из корзины
    var disable_cart_kit_edit_button = $('#becd-disable_cart_kit_edit_button').html() === 'true'
    // if (bundle_expert_mini.disable_cart_kit_edit_button) {
    if (disable_cart_kit_edit_button) {
        $('#content').find('.eckb').each(function (index, element) {

            $(element).find('i').remove();
            var str = $(element).html();
            var parent = $(element).parent();
            $(parent).find(element).remove();
            $(parent).append(str);
        })
    }

    var kit_unique_id_prev = "";
    $('.eckb').each(function (index, element) {
        //На странице редактирования заказа на вкладке total не должно работать
        if ($(element).closest('#tab-total').length > 0) {
            $(element).find('i').remove();
            var str = $(element).html();
            var parent = $(element).parent();
            $(parent).find(element).remove();
            $(parent).append(str);
        }


        //Вместо сокращенных данных делаем полные
        if (!$(element).hasClass('button-init-complete')) {

            var kit_unique_id = $(element).attr('uid');
            var item_position = $(element).attr('pos');
            var item_position_free = $(element).attr('pos-free');
            $(element).attr('kit-unique-id', kit_unique_id);
            $(element).attr('data-item-position', item_position);
            $(element).attr('data-item-position-free', item_position_free);
            $(element).addClass('button-init-complete');
            $(element).addClass('edit-cart-kit-button');


            //Иконку редактирования добавляем только для первого элемента комплекта
            if (kit_unique_id_prev !== kit_unique_id) {
                $(element).find('b').replaceWith('<div><span class="edit-kit-button"><i class="fa fa-pencil" style="display:none"></i>' + $('#becd-button_edit_kit').html() + '</span><span class="remove-kit-button"><i class="fa fa-remove" style="display:none"></i>' + $('#becd-button_remove_kit').html() + '</span></div>')
                $(element).find('i').show();
            } else {
                $(element).find('b').replaceWith('');
            }

            $(element).find('.edit-kit-button').attr('onclick', "BundleExpertMini.init_main_check();bundle_expert.show_kit_form_from_cart($(this).closest('.edit-cart-kit-button'));");
            $(element).find('.remove-kit-button').attr('onclick', "BundleExpertMini.init_main_check();bundle_expert.show_remove_kit_from_cart_question(this)");

            // $(element).find('.edit-kit-button').bind('click', function () {
            //     bundle_expert.show_kit_form_from_cart(element)
            // });
            //
            // $(element).find('.remove-kit-button').bind('click', function () {
            //     bundle_expert.show_remove_kit_from_cart_question(element);
            // });

            //Если внутри ссылки, то выводим за ссылку
            var parent = $(element).closest('a');
            if ($(parent).length > 0)
                $(parent).after(element);

            //Убираем тэги в картинках

            var parent_row_selector = $('#becd-cart-product-parent-row-selector').html();
            $(element).closest(parent_row_selector).find('img').each(function (index, el) {
                var img_alt = $(el).attr('alt');
                if (typeof img_alt !== "undefined") {
                    img_alt = img_alt.replace(/<\/?[^>]+(>|$)/g, "");
                }
                $(el).attr('alt', img_alt);

                var img_title = $(el).attr('title');
                if (typeof img_title !== "undefined") {
                    img_title = img_title.replace(/<\/?[^>]+(>|$)/g, "");
                }
                $(el).attr('title', img_title);
            });



            $(element).show();
        }


    })
}




