//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
var BundleExpert = {};

BundleExpert.constructor = function (widgets) {
    this.widgets = [];
    this.is_some_checkout_page = false;
    this.disable_cart_kit_edit_button = false;
    this.widget_total_block_width = 180;

    
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
    this.first_change_price = true;
    this.category_item_at_row = -1;
    this.window_scroll_selector='';
    this.slideshow_loop=true;
    this.control_change_quantity_by_stock=true;
}


BundleExpert.add_widgets = function (widgets) {
    for (var i = 0; i < widgets.length; i++) {
        this.widgets.push(widgets[i]);
    }

}

BundleExpert.init_bundle_expert = function () {
    bundle_expert.init_widgets();

    bundle_expert.init_event_modal_form_dismiss();

    
    
    
    
    
    
    
}

BundleExpert.init_widgets = function () {

    var slider_container = '';
    var item_html = ''
    var category_product_selector = '';

    for (var i = 0; i < this.widgets.length; i++) {
        var widget = this.widgets[i];

        var widget_html = $('#bundle-expert-container .kit-widget[data-widget-unique-id=' + widget['unique_id'] + ']').first();

        switch (widget['display_mode']) {
            case 'module':
            case 'module_in_category':
                widgets_container_unique_code = widget['config_module']['module_unique_id'];

                var slider_container_html = '';
                var widgets_container_selector = '';

                

                if (widget['slider_mode'] == 1) {
                    
                    slider_container_html = '<div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow"  data-autoplay-status="'+widget['slider_autoplay_status']+'" data-autoplay-time="'+widget['slider_autoplay_time']+'" style="opacity: 1;"></div>';
                    widgets_container_selector = '.bundle-expert-module-container[module-unique-id=' + widgets_container_unique_code + '] .bundle-expert-slideshow';
                } else {
                    
                    slider_container_html = '<div id="" class="bundle-expert-widget-items" style="opacity: 1;"></div>';
                    widgets_container_selector = '.bundle-expert-module-container[module-unique-id=' + widgets_container_unique_code + '] .bundle-expert-widget-items';
                }

                

                slider_container = $(widgets_container_selector).first();

                if ($(slider_container).length === 0) {
                    

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
                    slider_container_html = '<div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" data-autoplay-status="'+widget['slider_autoplay_status']+'" data-autoplay-time="'+widget['slider_autoplay_time']+'" style="opacity: 1;"></div></div>';
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
            case 'product_page':
                display_method = widget['config_product_page']['display_method'];
                selector = widget['config_product_page']['selector'];
                selector_mode = widget['config_product_page']['selector_mode'];

                widgets_container_unique_code = btoa(selector + selector_mode + widget['widget_id']);

                if (widget['slider_mode'] == 1) {
                    slider_container_html = '<div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" data-autoplay-status="'+widget['slider_autoplay_status']+'" data-autoplay-time="'+widget['slider_autoplay_time']+'" style="opacity: 1;"></div></div>';
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

                if (($(widget_html).hasClass('product-as-kit-mode') || $(widget_html).hasClass('product-as-kit-mode-light-mode')) && widget['main_mode'] === 'kit') {
                    bundle_expert.update_product_page_price_html(widget_html);
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

                
                var tt = parseFloat($(category_product_selector).first().width());
                var tt2 = parseFloat($(category_product_selector).first().parent().width());
                
                var width_percent = (100 * parseFloat($(category_product_selector).first().width() / parseFloat($(category_product_selector).first().parent().width())));
                
                var item_at_row = Math.floor(100 / width_percent);
                
                this.category_item_at_row = Math.floor(100 / width_percent);
                

                
                
                var product_position = this.find_product_position(widget['main_product_id'], widget['config_category_page']['selector']);
                var row_index = Math.floor(product_position / item_at_row);

                
                var element_index = item_at_row * rows_rate + item_at_row * rows_rate * row_index - 1;

                widgets_container_unique_code = btoa(selector + selector_mode + widget['widget_id']);

                
                
                slider_container = $('.bundle-expert-slideshow[row-index=' + row_index + ']').first();
                if ($(slider_container).length === 0) {
                    var html = '<div class="col-xs-12 col-12 bootstrap-style be-category-slider"><div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow" row-index="' + row_index + '"  data-autoplay-status="'+widget['slider_autoplay_status']+'" data-autoplay-time="'+widget['slider_autoplay_time']+'" style="opacity: 1;"></div></div></div>';

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
                    slider_container_html = '<div class="be-main-widgets-container be-lazy-load" data-widgets-container-unique-code="' + widgets_container_unique_code + '"><div id="" class="owl-carousel owl-carousel-bundle-expert bundle-expert-slideshow"  data-autoplay-status="'+widget['slider_autoplay_status']+'" data-autoplay-time="'+widget['slider_autoplay_time']+'" style="opacity: 1;"></div></div>';
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

            bundle_expert.setWidgetUpdateTotalEnable(widget_html, false);

            
            $(widget_html).find('.product-options-values .form-group').each(function (index, group) {
                bundle_expert.update_active_option_class(group);
            })

            bundle_expert.setWidgetUpdateTotalEnable(widget_html, true);
            
        }

        
        
        if(this.category_item_at_row>0) {
            if($('.be-has-category-slider').length>0){
                
                $(this.category_product_selector).addClass('be-clear-none');
                
                
                $(this.category_product_selector).each(function (index, element) {
                    var tt = index % bundle_expert.category_item_at_row;
                    if (index % bundle_expert.category_item_at_row == 0) {
                        $(element).removeClass('be-clear-none');
                        $(element).addClass('be-clear-left');
                    }
                })
            }


        }


        
        
        
        
        
        
        
        
        

        $(widget_html).removeClass("be-kit-has-stock-out");
        if($(widget_html).find('.kit-item.be-stock-out').length>0){
            $(widget_html).addClass("be-kit-has-stock-out");
        }

        be_custom.point('014', {'widget_html': widget_html});

        
        
        
        
        
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    
    
    
    
    
    
    
    

    $('.kit-widget').each(function (index, element) {
        bundle_expert.init_upload_file_buttons_in_widget(element);

    })

    bundle_expert.free_products_update_container_visible(widget_html);

    bundle_expert.free_products_update_container_items_visible(widget_html);

    bundle_expert.init_time_field_in_widgets();
}



BundleExpert.init_widgets_2 = function (){

    $('div[data-lazy-class=be-lazy-load]').each(function (index, element) {
        var widget_unique_id = $(element).attr('data-widget-lazy-container-unique-id');
        var widget = $('#bundle-expert-container .bundle-expert-widgets-container').find('.kit-widget[data-widget-unique-id='+widget_unique_id+']');
        if($(widget).length>0){
            $(element).replaceWith(widget);
            be_custom.point('043', {'widget': widget});
        }

    })

    
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

                    
                    bundle_expert.init_slideshow_widgets_product_carousel(slideshow, false);

                    $(slideshow).trigger('to.owl.carousel', [slideshow_item_index, 500, true]);
                }, 1000);


            });

            $(product_element).mouseleave(function () {
                clearTimeout(timer_control);
            });
        }
    })


    bundle_expert.init_slideshow('.bundle-expert-slideshow');

    
    var slideshows = $('.bundle-expert-widget-items, .bundle-expert-slideshow').each(function (index, slideshow) {
        

        if($(slideshow).hasClass('bundle-expert-slideshow')){
            
            
            
            var autoplay_status = $(slideshow).attr('data-autoplay-status');
            if(autoplay_status==="1"){
                bundle_expert.init_slideshow_widgets_product_carousel(slideshow, false);
            }else{
                bundle_expert.init_slideshow_widgets_product_carousel(slideshow, true);
            }
            
        }else{
            bundle_expert.init_slideshow_widgets_product_carousel(slideshow, false);
        }
        
        
        
        
        
        
        
        

    })

    
    
    
    
    
    
    
    
    

}

BundleExpert.init_widgets_ajax_filter = function (widgets_html, widgets_json) {
    $('.bundle-expert-widgets-container').html(widgets_html);

    var widgets = JSON.parse(widgets_json);

    bundle_expert.widgets = [];
    bundle_expert.add_widgets(widgets);
    bundle_expert.init_widgets();
}

BundleExpert.init_slideshow = function (selector) {
    if (typeof window['be_custom']['init_slideshow'] === 'function') {
        be_custom.init_slideshow(selector);
    }else{
        $(selector).each(function (index, element) {
            var length = $(element).find('.kit-widget').length;
            if(length>1){
                $(element).addClass('multi-kit-slideshow');
            }else{
                
                $(element).removeClass('owl-carousel').removeClass('bundle-expert-slideshow');

                
                bundle_expert.init_slideshow_widgets_product_carousel(element, false);

            }

            be_custom.point('041', {'widget': element});

        });
        var slideshow_elements = $(selector);
        if ($(slideshow_elements).length > 0) {

            $(slideshow_elements).each(function (index, slideshow_element) {
                var autoplay_status = $(slideshow_element).attr('data-autoplay-status');
                var autoplay_time = $(slideshow_element).attr('data-autoplay-time');
                if(autoplay_status==="1"){
                    autoplay_status = true;
                }else{
                    autoplay_status = false;
                }
                $(slideshow_element).owlCarouselBundleExpert({
                    items: 1,
                    autoplay: autoplay_status,
                    autoplayTimeout: autoplay_time,
                    nav: true,
                    navText: ['<i class="fa fa-angle-left fa-5x"></i>', '<i class="fa fa-angle-right  fa-5x"></i>'],
                    stopOnHover : true,
                    
                    
                    
                    
                    dots: true,
                    
                    rewind: bundle_expert.slideshow_loop,
                    onInitialized: bundle_expert.slideshowHasBeenInitialized
                });
            });


        }

    }




}

BundleExpert.slideshowHasBeenInitialized = function (event) {

    var element   = event.target;

    $(element).addClass('slideshow-initialized-complete');

    
    $(element).on('changed.owl.carousel', function(event) {

        


        bundle_expert_help.on_slideshow_change_item(event);
    });
    
    $(element).on('drag.owl.carousel', function(event) {
        var slideshow = $(this).closest('.owl-carousel');
        bundle_expert.init_slideshow_widgets_product_carousel(slideshow, false);
    });
    
    
    
    

    
    var next_button = $(element).find('.owl-nav .owl-next');
    $(next_button).bind('click', function () {
        var slideshow = $(this).closest('.owl-carousel');
        bundle_expert.init_slideshow_widgets_product_carousel(slideshow, false);

        var widget = $(slideshow).find('.owl-item.active .kit-widget');
        be_custom.point('044', {'widget': widget});
    })
    var dots_button = $(element).find('.owl-dots .owl-dot');
    $(dots_button).bind('click', function () {
        var slideshow = $(this).closest('.owl-carousel');
        bundle_expert.init_slideshow_widgets_product_carousel(slideshow, false);

        var widget = $(slideshow).find('.owl-item.active .kit-widget');
        be_custom.point('045', {'widget': widget});
    })

}


BundleExpert.init_slideshow_widgets_product_carousel = function (slideshow, only_first) {

    if(only_first){
        var widgets = $(slideshow).find('.kit-widget:not(#bundle-expert-container .kit-widget)');

        
        
        widgets = $(widgets).first();

        $(widgets).each(function (index, el) {
            var unique_id = $(el).attr('data-widget-unique-id');
            bundle_expert.init_carousel('#bundle-expert-widget-carousel-' + unique_id, -1)
        })
    }else{
        var not_initialized_carousels = $(slideshow).find('.kit-widget .owl-carousel:not(.owl-loaded)');

        if($(not_initialized_carousels).length>0){
            var widgets = $(not_initialized_carousels).closest('.kit-widget ');

            $(widgets).each(function (index, el) {
                var unique_id = $(el).attr('data-widget-unique-id');
                bundle_expert.init_carousel('#bundle-expert-widget-carousel-' + unique_id, -1)
            })
        }
    }

}





BundleExpert.init_carousel = function (selector, container_width) {
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
                
                container_width = Math.round($(container).width());
            }

            if (container_width < kit_items_count * kit_item_width) {
                var center = true;
                
            } else {
                var center = false;
            }


            
            
            var nav = false;
        } else {
            var kit_item_width = $(selector).closest('.kit-widget').attr('data-image-width') * 2;
            if (container_width === -1) {
                var container = $(selector).first();
                
                container_width = Math.round($(container).width());
            }
            container_width -= bundle_expert.widget_total_block_width;

            if (container_width + bundle_expert.widget_total_block_width < 478) {
                $(selector).closest('.kit-widget').addClass('widget-vertical-mode');
                container_width += bundle_expert.widget_total_block_width;
            }

            var center = false;
            var nav = true;

        }

        
        
        
        
        
        if (kit_items_count * kit_item_width > container_width) {
            
            items = Math.round(container_width / kit_item_width);
            if (items === 0)
                items = 1;
        } else {
            items = kit_items_count;
        }

        var carousel_elements = $(selector);
        if($(carousel_elements).length>0) {
            $(selector).owlCarouselBundleExpert({
                items: items,
                
                nav: nav,
                navText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
                dots: false,
                
                onInitialized: this.carouselHasBeenInitialized
            });
        }
    }


}

BundleExpert.carouselHasBeenInitialized = function (event) {
    var element   = event.target;
    var slideshow_element = $(element).closest('.bundle-expert-slideshow');
    
    if($(slideshow_element).hasClass('slideshow-initialized-complete')){
        
        

        bundle_expert_help.change_slideshow_side_buttons_height(slideshow_element);
    }


}

BundleExpert.init_lazy_sliders = function () {
    $('.be-lazy-load').each(function (index, element) {
        if (bundle_expert_help.isInViewport(element)) {



            $('.be-lazy-load').removeClass('be-lazy-load');

            $('.be-main-widgets-container').show();

            bundle_expert.init_widgets_2();

            return false;
        } else {
            
        }
    })
}


BundleExpert.find_product_position = function (product_id, selector) {

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

BundleExpert.get_product_widgets = function (product_id) {
    var widgets = [];

    for (var i = 0; i < this.widgets.length; i++) {
        var widget = this.widgets[i];

        if (widget['main_product_id'] === product_id) {
            widgets.push(widget);
        }
    }

    return widgets;
}

BundleExpert.add_to_cart_by_widget = function (element, cart_merge_confirm) {

    var widget_element = $(element).closest('.kit-widget');

    be_custom.point('021', {'widget': widget_element});

    $(widget_element).find('input[name=cart_merge_confirm]').val(cart_merge_confirm);

    
    var disabled = $(widget_element).find('input:disabled, select:disabled, textarea:disabled');

    disabled.prop('disabled', false);

    var data = $(widget_element).find('.kit-items .kit-item .product-selected input[type=\'text\'], .kit-items .kit-item .product-selected input[type=\'hidden\'], .kit-items .kit-item .product-selected input[type=\'radio\']:checked, .kit-items .kit-item .product-selected input[type=\'checkbox\']:checked, .kit-items .kit-item .product-selected select, .kit-items .kit-item .product-selected textarea, .kit-data input[type=\'hidden\']');

    

    data = $(data).serializeArray();


    disabled.prop('disabled', true);

    data = bundle_expert_help.normalize_options_names(data);

    
    var product_as_kit_mode = $(widget_element).hasClass('product-as-kit-mode');
    if (product_as_kit_mode) {
        var product_data = bundle_expert_help.get_product_page_data_for_send();
        var data = data.concat(product_data);

        var quantity = $(this.selectors.product_page.quantity).val();
        var data = data.concat({'name': 'product_as_kit_data[quantity]', 'value': quantity});

        var product_id = $(widget_element).find('.kit-data input[name=main_product_id]').val();
        var data = data.concat({'name': 'product_as_kit_data[product_id]', 'value': product_id});
    }

    
    var product_as_kit_mode = $(widget_element).hasClass('product-as-kit-mode-light-mode');
    if (product_as_kit_mode) {
        var product_data = bundle_expert_help.get_product_page_data_for_send('product_as_kit_data_le');
        var data = data.concat(product_data);

        var quantity = $(this.selectors.product_page.quantity).val();
        var data = data.concat({'name': 'product_as_kit_data_le[quantity]', 'value': quantity});

        var product_id = $(widget_element).find('.kit-data input[name=main_product_id]').val();
        var data = data.concat({'name': 'product_as_kit_data_le[product_id]', 'value': product_id});
    }
    

    
    var product_as_kit_mode_light_mode = $(widget_element).hasClass('product-as-kit-mode-light-mode');
    if (product_as_kit_mode_light_mode) {

        
        var product_data = bundle_expert_help.get_product_page_data_for_send_le();
        var data = data.concat(product_data);
        

        var quantity_input = $(bundle_expert.selectors.product_page.product_data).find('input[name=quantity]');
        if($(quantity_input).length>0){
            var quantity = $(quantity_input).val();
        }else{
            var quantity = 1;
        }
        var data = data.concat({'name': 'product_as_kit_light_mode[quantity]', 'value': quantity});
    }

    be_custom.point('031', {'widget': widget_element});

    var widget_unique_id = $(widget_element).attr('data-widget-unique-id');
    data = data.concat({'name': 'widget_unique_id', 'value': widget_unique_id});

    $(widget_element).find('#kit-items-container .alert, #kit-items-container .text-danger').remove();

    


    $.ajax({
        url: 'index.php?route=checkout/bundle_expert/add_to_cart',
        type: 'post',
        data: data,
        dataType: 'json',
        beforeSend: function () {
            if ($(element).hasClass('btn')) {
                $(element).button('loading');
            }
            if (product_as_kit_mode) {
                $('#button-cart-replaced').button('loading')
            }
        },
        complete: function () {
            if ($(element).hasClass('btn')) {
                $(element).button('reset');
            }
            if (product_as_kit_mode) {
                $('#button-cart-replaced').button('reset');
            }
        },
        success: function (json) {



            $('.alert, .text-danger').remove();
            $('.form-group').removeClass('has-error');

            be_custom.point('030', {'widget': widget_element});

            if (json['redirect']) {
                location = json['redirect'];
            }

            if (json['success']) {
                bundle_expert.update_cart_html(json);

                if (bundle_expert.is_some_checkout_page) {
                    location.reload();
                }

                
                be_custom.point('001', {'json': json});
            }

            if (json['error']) {

                bundle_expert.show_bundle_errors(widget_element, json);

                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                

            }else{

                if (json['error_text']) {
                    
                    bundle_expert.show_popup_message(json['error_text']);
                }
            }

            if (json['kit_enable_status_error']) {

                bundle_expert.show_popup_message(json['kit_enable_status_error_text']);
            }

            if (json['cart_merge_question']) {
                bundle_expert.show_cart_merge_question(element);
            }

            be_custom.point('040', {'json': json, 'widget': widget_element});

        }
    });
}

BundleExpert.add_to_cart_by_single_product = function (widget_unique_id, cart_merge_confirm) {
    var widget = $('.kit-widget[data-widget-unique-id=' + widget_unique_id + ']').first();
    if ($(widget).length > 0) {
        var element = $(widget).find('.kit-item').first();
        bundle_expert.add_to_cart_by_widget(element, cart_merge_confirm);
    }

    return;

}


BundleExpert.show_cart_merge_question = function (element) {
    var modal = $('#bundle-expert-cart-merge-question');

    $(modal).modal('show');

    $(modal).find('#add-to-cart-simple-button').unbind('click').bind('click', function () {
        $(modal).modal('hide');

        
        bundle_expert.add_to_cart_by_widget(element, 0);
    })

    $(modal).find('#add-to-cart-merge-button').unbind('click').bind('click', function () {
        $(modal).modal('hide');

        
        bundle_expert.add_to_cart_by_widget(element, 1);
    })
}

BundleExpert.show_add_to_cart_question = function (element) {
    var modal = $('#bundle-expert-question');

    $(modal).modal('show');

    $(modal).find('#setup-kit-button').unbind('click').bind('click', function () {
        $(modal).modal('hide');

        bundle_expert.show_kit_form(element);
    })

    $(modal).find('#add-kit-to-cart-button').unbind('click').bind('click', function () {
        $(modal).modal('hide');

        
        bundle_expert.add_to_cart_by_widget(element, 0);
    })
}

BundleExpert.show_popup_message = function (message) {
    var modal = $('#bundle-expert-message');

    $(modal).find('.body-content').html(message);

    $(modal).modal('show');

}

BundleExpert.show_kit_form = function (element) {

    if (!bundle_expert.admin_mode) {
        var widget_element = $(element).closest('.kit-widget');
        var widget_id = $(widget_element).attr('data-widget-id');
        var widget_unique_id = $(widget_element).attr('data-widget-unique-id');
        var kit_id = $(widget_element).attr('data-kit-id');
        var main_product_id = $(widget_element).attr('data-main-product-id');
        var main_product_in_cart = $(widget_element).find('.kit-data input[name=main_product_in_cart]').val();
        var cart_merge_enable = $(widget_element).find('.kit-data input[name=cart_merge_enable]').val();

        bundle_expert_form.show_modal_kit_form(kit_id, main_product_id, widget_unique_id, 0, -1, main_product_in_cart, cart_merge_enable);
    } else {
        var kit_id = $('#tab-product input[name=order_kit_id]').val();
        var main_product_id = '';
        var widget_unique_id = 'widget_in_admin';
        var main_product_in_cart = 0;
        var cart_merge_enable = 0;
        if (kit_id !== '')
            bundle_expert_form.show_modal_kit_form(kit_id, main_product_id, widget_unique_id, 0, -1, main_product_in_cart, cart_merge_enable);
    }

}


BundleExpert.show_kit_form_from_cart = function (element) {
    var kit_unique_id = $(element).attr('kit-unique-id');
    var item_position = $(element).attr('data-item-position');
    var item_position_free = $(element).attr('data-item-position-free');

    
    be_custom.point('002', {});

    bundle_expert_form.show_modal_kit_form_from_cart(kit_unique_id, item_position, item_position_free);
}

BundleExpert.show_remove_kit_from_cart_question = function (element) {
    var modal = $('#bundle-expert-remove-kit-from-cart-question');

    be_custom.point('035', {'element': element});

    $(modal).modal('show');

    $(modal).find('#remove-kit-from-cart-modal-button').unbind('click').bind('click', function () {

        be_custom.point('005', {});

        $(modal).modal('hide');

        bundle_expert.remove_kit_from_cart(element);
    })

}

BundleExpert.remove_kit_from_cart = function (element) {
    var widget_element = $(element).closest('.kit-widget');
    var kit_unique_id = $(element).closest('.edit-cart-kit-button').attr('kit-unique-id');

    $.ajax({
        url: 'index.php?route=checkout/bundle_expert/remove_from_cart',
        type: 'post',
        data: {'kit_unique_id': kit_unique_id},
        dataType: 'json',
        beforeSend: function () {
            $(element).button('loading');
        },
        complete: function () {
            $(element).button('reset');
        },
        success: function (json) {
            $('.alert, .text-danger').remove();

            if (json['redirect']) {
                location = json['redirect'];
            }

            if (json['success']) {

                if (bundle_expert.is_some_checkout_page) {
                    location.reload();
                }

                bundle_expert.update_cart_html(json)

                be_custom.point('004', {'json': json});

            }

            if (json['error']) {
            }
        }
    });
}








































































BundleExpert.init_event_modal_form_dismiss = function () {
    $('#bundle-expert-form-modal').on('hide.bs.modal', function () {
        bundle_expert_form.hide_kit_form();
    })
    $('#bundle-expert-form-modal').on('hidden.bs.modal', function () {
        be_custom.point('036', {'modal': this});
    })
    $(document).on('shown.bs.modal', '#bundle-expert-form-modal', function (event) {
        var kit_from_cart = $('#bundle-expert-form-modal').find('.modal-content').attr('kit_from_cart');

        if (kit_from_cart == 0) {
            bundle_expert_form.show_kit_form();
        } else {
            bundle_expert_form.show_kit_form_from_cart();
        }
    })

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}

BundleExpert.setCookie = function (name, value, options) {
    options = options || {};

    var expires = options.expires;

    if (typeof expires == "number" && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires * 1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }

    value = encodeURIComponent(value);

    var updatedCookie = name + "=" + value;

    for (var propName in options) {
        updatedCookie += "; " + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}

BundleExpert.load_bundle_expert_to_order_form = function () {
    
    var timeout = 1000;
    setTimeout(
        function()
        {
            if (!bundle_expert.admin_mode_new_api) {
                var url = 'index.php?route=catalog/bundle_expert/api&' + bundle_expert.api_token_name + bundle_expert.api_token_value + '&api=api/bundle_expert/getBundleExpert';
            } else {
                var url = bundle_expert.store_url + 'index.php?route=api/bundle_expert/getBundleExpert&' + bundle_expert.api_token_name + bundle_expert.api_token_value;
            }
            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                beforeSend: function () {
                    
                },
                complete: function () {
                    
                },
                success: function (json) {
                    $('.alert, .text-danger').remove();
                    $('.form-group').removeClass('has-error');

                    if (json['error']) {
                        $('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }

                    if (json['success']) {
                        $('#admin-bundle-expert-container').html(json['bundle_expert'])

                        bundle_expert.init_event_modal_form_dismiss();

                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }, timeout);


}


BundleExpert.check_changed_quantity_by_stock = function (kit_item, quantity) {
    if (bundle_expert.control_change_quantity_by_stock) {
        var product_max_quantity = parseInt($(kit_item).find('[data-id="input-max-quantity"]').val());

        if (quantity > product_max_quantity) {
            quantity = product_max_quantity;
        }

        $(kit_item).find('input[type=\'radio\']:checked, input[type=\'checkbox\']:checked').each(function (index, input) {
            if(typeof $(input).attr('data-max-quantity') !="undefined"){
                if(quantity>parseInt($(input).attr('data-max-quantity'))){
                    quantity=parseInt($(input).attr('data-max-quantity'));
                }
            }

        })

        $(kit_item).find('select option:selected').each(function (index, input) {
            if (typeof $(input).attr('data-max-quantity') != "undefined") {
                if (quantity > parseInt($(input).attr('data-max-quantity'))) {
                    quantity = parseInt($(input).attr('data-max-quantity'));
                }
            }
        })
    }

    return quantity;
}


BundleExpert.on_click_change_quantity = function (click_element, quantity) {
    var kit_item = $(click_element).closest('.kit-item');
    var old_quantity = parseInt($(kit_item).find('input.input-quantity-field').val());
    if (isNaN(old_quantity))
        old_quantity = 1;
    var new_quantity = old_quantity + quantity;

    
    if (new_quantity <= 0 && $(kit_item).find('input[data-id=input-is-free-product]').val() == 1) {
        
        
        
        
        
        
        
    }

    
    new_quantity = bundle_expert.check_changed_quantity_by_stock(kit_item, new_quantity);
    

    if (new_quantity <= 0) {
        new_quantity = 1;
    }

    $(kit_item).find('input.input-quantity-field').val(new_quantity);
    $(kit_item).find('input[data-id=input-quantity]').val(new_quantity);

    
    var kit_widget = $(click_element).closest('.kit-widget');
    var widget_unique_id = $(kit_widget).attr('data-widget-unique-id');
    var item_position = $(click_element).closest('.kit-item-product').attr('data-item-position');
    var item_position_free = $(click_element).closest('.kit-item-product').attr('data-item-position-free');
    var kit_form = $('#bundle-expert-container .bundle-expert-forms-container').find('div[data-widget-unique-id=' + widget_unique_id + '] .kit-form');
    bundle_expert_form.update_product_quantity(kit_form, item_position, item_position_free, new_quantity);

    
    bundle_expert.update_widget_kit_total(kit_widget);
}

BundleExpert.on_click_change_quantity_in_form = function (click_element, quantity) {
    var product_form = $(click_element).closest('.be-product-form');
    var item_position = $(product_form).attr('data-item-position');
    var item_position_free = $(product_form).attr('data-item-position-free');
    var kit_form = $(click_element).closest('.kit-form');
    var old_quantity = parseInt($(product_form).find('input.input-quantity-field').val());
    if (isNaN(old_quantity))
        old_quantity = 1;
    var new_quantity = old_quantity + quantity;



    
    new_quantity = bundle_expert.check_changed_quantity_by_stock(product_form, new_quantity);
    

    if (new_quantity <= 0) {
        new_quantity = 1;
    }

    $(click_element).closest('.quantity-buttons').find('input.input-quantity-field').val(new_quantity);
    

    
    
    
        
        bundle_expert_form.update_product_quantity(kit_form, item_position, item_position_free, new_quantity);

        
        bundle_expert_form.update_kit_form_total(kit_form);
    
}

BundleExpert.init_time_field_in_widgets = function () {
    if ($.fn.datetimepicker) {
        
        $('.kit-widget').find('.date').datetimepicker({
            pickTime: false
        }).on('dp.change', function (e) {
            bundle_expert_form.on_change_product_option($(this).find('input'));
        });

        $('.kit-widget').find('.datetime').datetimepicker({
            pickDate: true,
            pickTime: true
        }).on('dp.change', function (e) {
            bundle_expert_form.on_change_product_option($(this).find('input'));
        });

        $('.kit-widget').find('.time').datetimepicker({
            pickDate: false
        }).on('dp.change', function (e) {
            bundle_expert_form.on_change_product_option($(this).find('input'));
        });
    }


}

BundleExpert.init_time_field_in_widget_item = function (kit_widget, item_position, item_position_free) {
    
    if ($.fn.datetimepicker) {
        var kit_item = $(kit_widget).find('.kit-items').find('.kit-item[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']')
        $(kit_item).find('.date').datetimepicker({
            pickTime: false
        }).on('dp.change', function (e) {
            bundle_expert_form.on_change_product_option($(this).find('input'));
        });

        $(kit_item).find('.datetime').datetimepicker({
            pickDate: true,
            pickTime: true
        }).on('dp.change', function (e) {
            bundle_expert_form.on_change_product_option($(this).find('input'));
        });

        $(kit_item).find('.time').datetimepicker({
            pickDate: false
        }).on('dp.change', function (e) {
            bundle_expert_form.on_change_product_option($(this).find('input'));
        });
        ;
    }


}
















BundleExpert.free_products_update_container_items_visible = function (kit_widget) {

    var items = $(kit_widget).find('#free-item-products-container .free-products-group');
    $(items).each(function (index, item) {
        var item_position = $(item).attr('data-group-item-position');
        bundle_expert.updateFreeProductContainerItem(kit_widget, item_position)
    })


}

BundleExpert.free_products_update_container_visible = function (kit_widget) {

    var items = $(kit_widget).find('#free-item-products-container .kit-item.free-product');

    if($(items).length===0){
        $(kit_widget).find('#free-item-products-container').addClass('free-item-products-container-hidden');
    }else{
        $(kit_widget).find('#free-item-products-container').removeClass('free-item-products-container-hidden');
    }

}

BundleExpert.free_products_update_input_index = function (kit_widget, item_position) {
    var free_item_position = 0;
    
    $(kit_widget).find('.kit-items .kit-item.free-product[data-item-position=' + item_position + '] .product-selected input[data-id=input-free-product-in-kit][value=1]').closest('.kit-item').each(function (index, element) {

        var prev_free_item_position = $(element).attr('data-item-position-free');
        $(element).attr('data-item-position-free', free_item_position);
        $(element).find('.kit-item-product').attr('data-item-position-free', free_item_position);
        $(element).find('.kit-item-product input[data-id=input-item-position-free]').val(free_item_position);
        $(element).find('.kit-item-product input, .kit-item-product select, .kit-item-product textarea').each(function (index, element) {
            var attr_name = $(element).attr('name');
            
            attr_name = attr_name.replace('kit_items_free_w[' + item_position + '][' + prev_free_item_position + ']', 'kit_items_free_w[' + item_position + '][' + free_item_position + ']');
            $(element).attr('name', attr_name);
        });
        free_item_position++;
        
    })
}

BundleExpert.free_product_add_to_kit = function (kit_item, clear_kit_form) {
    if (typeof clear_kit_form === "undefined") {
        clear_kit_form = true;
    }
    

    var kit_widget = $(kit_item).closest('.kit-widget');

    
    var widget_unique_id = $(kit_widget).attr('data-widget-unique-id');
    kit_widget = $('.kit-widget[data-widget-unique-id='+widget_unique_id+']');

    var item_position = parseInt($(kit_item).attr('data-item-position'));

    
    var item_position_free = parseInt($(kit_widget).find('.kit-items .kit-item.free-product[data-item-position=' + item_position + '] input[data-id=input-free-product-in-kit][value=1]').length);

    if (isNaN(item_position_free)) {
        item_position_free = 0;
    } else {
        if (item_position_free > 0) {
            
        }
    }

    $(kit_item).attr('data-item-position', item_position);
    $(kit_item).attr('data-item-position-free', item_position_free);
    $(kit_item).find('.kit-item-product').attr('data-item-position', item_position);
    $(kit_item).find('.kit-item-product').attr('data-item-position-free', item_position_free);
    $(kit_item).find('.kit-item-product input[data-id=input-item-position]').val(item_position);
    $(kit_item).find('.kit-item-product input[data-id=input-item-position-free]').val(item_position_free);
    $(kit_item).find('.kit-item-product input, .kit-item-product textarea, .kit-item-product select').each(function (index, element) {
        var attr_name = $(element).attr('name');
        attr_name = attr_name.replace('kit_items_w[' + item_position + ']', 'kit_items_free_w[' + item_position + '][' + item_position_free + ']');
        $(element).attr('name', attr_name);
    });

    $(kit_item).attr('data-empty-item', 0);

    $(kit_item).find('input[data-id=input-free-product-in-kit]').val(1);
    $(kit_item).find('.kit-item-product').addClass('product-selected');

    $(kit_item).find('.select-product-button').hide();
    $(kit_item).find('.add-to-kit-free-product-button').hide();

    bundle_expert.free_products_update_input_index(kit_widget, item_position);

    
    
    if(!bundle_expert.is_widget_checkbox_mode(kit_widget)){
    
    

        $(kit_widget).find('.kit-items #kit-items-container').find('.free-product-container-in-kit[data-item-position=' + item_position + ']').append(kit_item);
        $(kit_widget).find('.kit-items #kit-items-container').find('.free-product-container-in-kit[data-item-position=' + item_position + ']').show();
    }

    bundle_expert.init_time_field_in_widget_item(kit_widget, item_position, item_position_free);

    
    if(clear_kit_form){
        var widget_unique_id = $(kit_widget).attr('data-widget-unique-id');
        $('.bundle-expert-forms-container').find('[data-widget-unique-id=' + widget_unique_id + ']').remove();

    }

    
    if ($(kit_widget).find('#free-item-products-container .free-items .kit-item[data-item-position=' + item_position + ']').length > 0) {
        $(kit_widget).find('#free-item-products-container .empty-list-text[data-item-position=' + item_position + ']').hide();
    } else {
        $(kit_widget).find('#free-item-products-container .empty-list-text[data-item-position=' + item_position + ']').show();
    }

    if ($(kit_widget).find('#free-item-products-container .free-items .kit-item[data-item-position=' + item_position + ']').length > 0) {
        $(kit_widget).find('#free-item-products-container [data-group-item-position=' + item_position + ']').show();
    } else {
        $(kit_widget).find('#free-item-products-container [data-group-item-position=' + item_position + ']').hide();
    }

    bundle_expert.updateFreeProductContainerItem(kit_widget, item_position);
    bundle_expert.free_products_update_container_visible(kit_widget);

    be_custom.point('019', {'kit_widget': kit_widget});

    
    this.update_widget_kit_total(kit_widget);
}

BundleExpert.free_product_remove_from_kit = function (kit_item, clear_kit_form) {

    if (typeof clear_kit_form === "undefined") {
        clear_kit_form = true;
    }

    var kit_widget = $(kit_item).closest('.kit-widget');

    
    var widget_unique_id = $(kit_widget).attr('data-widget-unique-id');
    kit_widget = $('.kit-widget[data-widget-unique-id='+widget_unique_id+']');

    var free_products_table_mode = parseInt($(kit_widget).attr('data-free-products-table-mode'));


    var item_position = parseInt($(kit_item).attr('data-item-position'));
    var item_position_free = parseInt($(kit_item).attr('data-item-position-free'));

    if (free_products_table_mode === 1) {
        $(kit_item).find('.kit-item-product input, select, textarea').each(function (index, element) {
            var attr_name = $(element).attr('name');
            attr_name = attr_name.replace('kit_items_free_w[' + item_position + '][' + item_position_free + ']', 'kit_items_w[' + item_position + ']');
            $(element).attr('name', attr_name);
        });

        $(kit_item).attr('data-empty-item', 1);

        $(kit_item).find('input[data-id=input-free-product-in-kit]').val(0);
        $(kit_item).find('.kit-item-product').removeClass('product-selected');
        $(kit_item).attr('data-item-position-free', 0);
        $(kit_item).find('.kit-item-product').attr('data-item-position-free', 0);
        $(kit_item).find('input[data-id=input-item-position-free]').val(0);

        $(kit_item).find('.selectable-item-container').removeClass('hidden');
        $(kit_item).find('.select-product-button').hide();
        $(kit_item).find('.add-to-kit-free-product-button').show();

        
        
        if(!bundle_expert.is_widget_checkbox_mode(kit_widget)){
        
            $(kit_widget).find('#free-item-products-container .free-items .free-products-group[data-item-position=' + item_position + ']').prepend(kit_item);
            
            var product_count = $(kit_widget).find('.free-product-container-in-kit[data-item-position=' + item_position + '] .kit-item-product').length;
            if (product_count == 0) {
                $(kit_widget).find('.free-product-container-in-kit[data-item-position=' + item_position + ']').hide();
            }

        }
    } else {
        $(kit_item).remove();
    }


    bundle_expert.free_products_update_input_index(kit_widget, item_position);


    
    if(clear_kit_form){
        var widget_unique_id = $(kit_widget).attr('data-widget-unique-id');
        $('.bundle-expert-forms-container').find('[data-widget-unique-id=' + widget_unique_id + ']').remove();

    }

    bundle_expert.updateFreeProductContainerItem(kit_widget, item_position);
    bundle_expert.free_products_update_container_visible(kit_widget);

    
    
    
    
    this.update_widget_kit_total(kit_widget)

    
    if ($(kit_widget).find('#free-item-products-container .free-items .kit-item[data-item-position=' + item_position + ']').length > 0) {
        $(kit_widget).find('#free-item-products-container .empty-list-text[data-item-position=' + item_position + ']').hide();
    } else {
        $(kit_widget).find('#free-item-products-container .empty-list-text[data-item-position=' + item_position + ']').show();
    }
}

BundleExpert.on_click_remove_product_button = function (click_element, clear_kit_form) {
    if (typeof clear_kit_form === "undefined") {
        clear_kit_form = true;
    }

    var kit_item = $(click_element).closest('.kit-item');
    var kit_widget = $(kit_item).closest('.kit-widget');

    
    if ($(kit_item).hasClass('free-product')) {
        this.free_product_remove_from_kit(kit_item);
    } else {
        
        if ($(kit_item).hasClass('selectable')) {
            $(kit_item).attr('data-empty-item', 1)
            $(kit_item).find('input[data-id=input-is-item-is-empty]').val(1);
            $(kit_item).find('.kit-item-product').removeClass('product-selected');
            $(kit_item).find('.kit-item-product').addClass('hidden');
            $(kit_item).find('.empty-kit-item-product').removeClass('hidden');

            $(kit_item).removeClass('be-stock-out');

            
            var widget_unique_id = $(kit_widget).attr('data-widget-unique-id');
            $('.bundle-expert-forms-container').find('[data-widget-unique-id=' + widget_unique_id + ']').remove();

            this.update_widget_kit_total(kit_widget)
        }
    }

    
    if(clear_kit_form) {
        var widget_unique_id = $(kit_widget).attr('data-widget-unique-id');
        $('.bundle-expert-forms-container').find('[data-widget-unique-id=' + widget_unique_id + ']').remove();
    }

    
    be_custom.point('041', {'widget': kit_widget});
}

BundleExpert.update_widget_kit_total = function (widget) {
    if(!this.checkWidgetUpdateTotalEnable(widget)){
        return;
    }

    

    var data = $(widget).find('.kit-data').find('input[type=\'text\'],  input[type=\'hidden\']');

    var kit_from_cart_unique_id = $(widget).find('.kit-data input[name=kit_from_cart_unique_id]').val()

    var data_products = $(widget).find('.kit-items .kit-item-product.product-selected').find('input[type=\'text\'],  input[type=\'hidden\'],  input[type=\'radio\']:checked,  input[type=\'checkbox\']:checked, select,  textarea, .kit-data input[type=\'hidden\']');

    
    var disabled = $(widget).find('.kit-items .kit-item-product.product-selected').find('input:disabled, select:disabled, textarea:disabled');
    disabled.prop('disabled', false);

    var serialized = $(data_products).serializeArray();

    disabled.prop('disabled', true);

    data = $(data).add(serialized);

    data = $(data).add({'kit_from_cart_unique_id': kit_from_cart_unique_id});

    
    if ($(widget).hasClass('product-as-kit-mode') || $(widget).hasClass('product-as-kit-mode-light-mode')) {
        var product_as_kit_quantity = $(bundle_expert.selectors.product_page.product_data).find('input[name=quantity]').val();
        if (typeof product_as_kit_quantity !== 'undefined') {
            data = $(data).add({'name': 'product_as_kit_quantity', 'value': product_as_kit_quantity});
        }
    }

    
    if ($(widget).hasClass('product-as-kit-mode')) {
        var product_data = bundle_expert_help.get_product_page_data_for_send();
        var data = data.add(product_data);

        var product_id = $(widget).find('.kit-data input[name=main_product_id]').val();
        var data = data.add({'name': 'product_as_kit_data[product_id]', 'value': product_id});
    }

    
    if ($(widget).hasClass('product-as-kit-mode-light-mode')) {
        var product_data = bundle_expert_help.get_product_page_data_for_send('product_as_kit_data_le');
        var data = data.add(product_data);

        var product_id = $(widget).find('.kit-data input[name=main_product_id]').val();
        var data = data.add({'name': 'product_as_kit_data_le[product_id]', 'value': product_id});
    }
    

    data = bundle_expert_help.normalize_options_names(data);

    var url = '';
    if (!bundle_expert.admin_mode) {
        url = 'index.php?route=checkout/bundle_expert/get_kit_total';
    } else {
        if (!bundle_expert.admin_mode_new_api) {
            url = 'index.php?route=catalog/bundle_expert/api&token=' + bundle_expert.admin_token + '&api=api/bundle_expert/getKitTotal';
        } else {
            url = bundle_expert.store_url + 'index.php?route=api/bundle_expert/getKitTotal&' + bundle_expert.api_token_name + bundle_expert.api_token_value; 
        }
        
    }

    

    $.ajax({
        url: url,
        type: 'post',
        data: data,
        dataType: 'json',
        beforeSend: function () {
            
            
        },
        complete: function () {
            
            
        },
        success: function (json) {

            if (json['error']) {

            }

            if (json['total_kit']) {

                $(widget).find('.be-price-old.total-default').html(json['total_default'])
                $(widget).find('.be-price-old.total-default').attr('data-price-value', json['total_default_value'])
                $(widget).find('.total-default-new').html(json['total_default_new'])
                $(widget).find('.total-default-new').attr('data-price-value', json['total_default_new_value'])
                if (json['total_default_value'] === json['total_default_new_value']) {
                    $(widget).find('.be-price-old.total-default').hide();
                }else{
                    $(widget).find('.be-price-old.total-default').show();
                }

                $(widget).find('.be-price-old.total-kit-old').html(json['total_kit_old'])
                $(widget).find('.be-price-old.total-kit-old').attr('data-price-value', json['total_kit_old_value']);
                $(widget).find('.total-kit').html(json['total_kit'])
                $(widget).find('.total-kit').attr('data-price-value', json['total_kit_value']);
                if (json['total_kit_old_value'] === json['total_kit_value']) {
                    $(widget).find('.be-price-old.total-kit-old').hide();
                }else{
                    $(widget).find('.be-price-old.total-kit-old').show();
                }

                if(json['total_default_value'] != json['total_kit_value']){
                    $(widget).find('.total-default-container').removeClass('hidden').show();
                }else{
                    $(widget).find('.total-default-container').addClass('hidden').hide();
                }

                
                for (var i = 0; i < json['products_price_data'].length; i++) {
                    var price_data = json['products_price_data'][i];

                    var kit_item_poduct = $(widget).find('.kit-items').find('.kit-item-product[data-item-position=' + price_data['item_position'] + '][data-item-position-free=' + price_data['item_position_free'] + '][data-product-id=' + price_data['product_id'] + ']');

                    if (price_data['special']) {
                        $(kit_item_poduct).find('.be-price .be-price-new').html(price_data['special']);
                        $(kit_item_poduct).find('.be-price .be-price-old').html(price_data['price']);
                    } else {
                        $(kit_item_poduct).find('.be-price').html(price_data['price']);
                    }

                    if($(kit_item_poduct).find('.be-row-price-total').length>0){
                        if (price_data['special_total']) {
                            $(kit_item_poduct).find('.be-price-total .be-price-new-total').html(price_data['special_total']);
                            $(kit_item_poduct).find('.be-price-total .be-price-old-total').html(price_data['price_total']);
                        } else {
                            $(kit_item_poduct).find('.be-price-total').html(price_data['price_total']);
                        }
                    }


                    if (price_data['price_discount_text']) {
                        $(kit_item_poduct).find('.item-price-discount').html(price_data['price_discount_text']);
                    }
                }

                
                if ($(widget).hasClass('product-as-kit-mode') || $(widget).hasClass('product-as-kit-mode-light-mode')) {
                    bundle_expert.update_product_page_price_html(widget, json);
                }

                if($(widget).find('.be-profit-price-row').length>0){
                    $(widget).find('.be-profit-price-row').find('.be-profit-price').html(json['profit_price'])
                    if(json['profit_value']){
                        $(widget).find('.be-profit-price-row').removeClass('hidden');
                    }else{
                        $(widget).find('.be-profit-price-row').addClass('hidden');
                    }
                }



                be_custom.point('018', {'widget': widget, 'json': json});

            }

        }
    });
}

BundleExpert.show_widget_error = function (widget, error) {

    var json = error;

    
    $(widget).find('#kit-items-container').prepend('<div class="col-sm-12 alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error_text'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

    
    
    

    var j;
    var i;

    for (j in json['error']) {
        if (json['error'][j]['option']) {

            var item_position = json['error'][j]['item_position'];
            var item_position_free = json['error'][j]['item_position_free'];

            var active_product_form = $(widget).find('#kit-items-container .kit-item-product[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']');
            ;

            $(widget).find('#kit-items-container').find('.kit-item-product[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']').find('.alert-icon').show();

            for (var k = 0; k < json['error'][j]['option'].length; k++) {
                var error_option = json['error'][j]['option'][k];

                for (i in error_option) {
                    var element = $(active_product_form).find('#input-option' + i.replace('_', '-'));

                    if (element.parent().hasClass('input-group')) {
                        element.parent().after('<div class="text-danger">' + error_option[i] + '</div>');
                    } else {
                        element.after('<div class="text-danger">' + error_option[i] + '</div>');
                    }
                }
            }


        }

        if (json['error'][j]['recurring']) {
            $(active_product_form).find('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error'][j]['recurring'] + '</div>');
        }


    }

    be_custom.point('039', {'widget': widget, 'error': error});
}

BundleExpert.show_product_as_kit_error = function (error) {
    var i;

    var product_element_data = $(bundle_expert.selectors.product_page.product_data).first();

    var top_option_position = 1000000;
    var top_element = -1;
    if (error['option']) {
        for (var k = 0; k < error['option'].length; k++) {
            var error_option = error['option'][k];

            for (i in error_option) {
                var element = $(product_element_data).find('#input-option' + i.replace('_', '-'));

                if (element.parent().hasClass('input-group')) {
                    element.parent().after('<div class="text-danger">' + error_option[i] + '</div>');
                } else {
                    element.after('<div class="text-danger">' + error_option[i] + '</div>');
                }

                var scroll_position_option = $(element).closest('.form-group').offset().top ;
                if(scroll_position_option<top_option_position){
                    top_option_position = scroll_position_option;
                    top_element = element;
                }
            }
        }
    }

    if (error['recurring']) {
        $(active_product_form).find('select[name=\'recurring_id\']').after('<div class="text-danger">' + error['recurring'] + '</div>');
    }


    if(top_option_position!=1000000){
        $([document.documentElement, document.body]).animate({
            
            scrollTop: top_option_position
        }, 1000);
    }





}


BundleExpert.product_as_kit_page_buttons_init = function (widget) {
    if(typeof window['be_custom']['product_as_kit_page_buttons_init'] !== 'function'){
        var button_selector = bundle_expert.selectors['product_page']['button'];

        $(button_selector).each(function (index, element) {
            
            var button = element;

            
            if ($(button).length > 0) {
                var button_clone = $(button).clone();
                var widget_unique_id = widget['unique_id'];
                var onclick_attr = "bundle_expert.add_to_cart_by_single_product('" + widget_unique_id + "', false);";
                var id_attr = $(button_clone).attr('id');
                if(typeof id_attr === 'undefined'){
                    id_attr = 'button-cart';
                }
                id_attr += '-replaced';
                $(button_clone).attr('onclick', onclick_attr);
                $(button_clone).attr('id', id_attr);
                $(button_clone).attr('data-widget-unique-id', widget_unique_id);
                $(button).replaceWith(button_clone);
            }
        })

        
        
        var product_as_kit_options = $(bundle_expert.selectors.product_page.product_data).find('input[type=\'text\'], input[name^=option][type=\'radio\'], input[name^=option][type=\'checkbox\'], select[name^=option], textarea');
        if (typeof product_as_kit_options !== 'undefined') {
            $(product_as_kit_options).each(function (index, option_element) {
                var onchange_text = 'bundle_expert.on_changed_product_as_kit_option(this);';
                $(option_element).attr('onchange', onchange_text);
                $(option_element).attr('data-widget-unique-id',  widget['unique_id']);
            })

        }


        
            if (bundle_expert.animate_price) {
                
                
                
                
                
                
                
                
                
                
                


                
                if (bundle_expert.selectors.product_page.button_plus !== '') {
                    var btn_plus_selector = bundle_expert.selectors.product_page.button_plus;
                    var btn_plus_clone = $(btn_plus_selector).first().clone();
                    $(btn_plus_selector).replaceWith(btn_plus_clone);
                    $(btn_plus_clone).unbind('click');
                    $(btn_plus_clone).attr('onclick', 'bundle_expert.product_as_kit_page_change_quantity(1);');

                }
                if (bundle_expert.selectors.product_page.button_minus !== '') {
                    var btn_minus_selector = bundle_expert.selectors.product_page.button_minus;
                    var btn_minus_clone = $(btn_minus_selector).first().clone();
                    $(btn_minus_selector).replaceWith(btn_minus_clone);
                    $(btn_minus_clone).unbind('click');
                    $(btn_minus_clone).attr('onclick', 'bundle_expert.product_as_kit_page_change_quantity(-1);');

                }
                if (bundle_expert.selectors.product_page.quantity !== '') {
                    var input_quantity_selector = bundle_expert.selectors.product_page.quantity;
                    var input_quantity_clone = $(input_quantity_selector).first().clone();

                    $(input_quantity_selector).replaceWith(input_quantity_clone);
                    $(input_quantity_selector).attr('data-widget-unique-id', widget['unique_id'])

                    $(input_quantity_clone).unbind('change');
                    $(input_quantity_clone).attr('onchange', 'bundle_expert.product_as_kit_page_quantity_changed();');
                    
                }

            }
        
    }else{
        be_custom.product_as_kit_page_buttons_init(widget);
    }


    be_custom.point('023', {'widget': widget});
}


BundleExpert.product_as_kit_page_change_quantity = function (value) {
    var input_quantity_selector = bundle_expert.selectors.product_page.quantity;

    var input_val = $(input_quantity_selector).val();
    var quantity = parseInt(input_val)+value;

    if (quantity <=0) {
        $(input_quantity_selector).val(1);
    }else{
        $(input_quantity_selector).val(quantity)
    }
    $(input_quantity_selector).change();
}


BundleExpert.product_as_kit_page_quantity_changed = function () {
    var input_quantity_selector = bundle_expert.selectors.product_page.quantity;

    var input_quantity = $(input_quantity_selector).first();

    
    $(input_quantity).unbind('change');

    var widget_unique_id = $(input_quantity).attr('data-widget-unique-id');
    var widget=$('.kit-widget[data-widget-unique-id='+widget_unique_id+']');
    bundle_expert.update_widget_kit_total(widget);
}

BundleExpert.update_product_page_price_html = function (widget, json) {


    if ($(widget).hasClass('product-as-kit-mode') || $(widget).hasClass('product-as-kit-mode-light-mode')) {

        if ('product_page' in bundle_expert.selectors) {
            var price_selector = bundle_expert.selectors.product_page.price;
            
            var special_selector = '.product-special-container';

            

            var price = $(widget).find('.be-price-old.total-default').html();
            var price_value = $(widget).find('.be-price-old.total-default').attr('data-price-value');

            if (price_value === '') {
                price = $(widget).find('.total-default-new').html();
                price_value = $(widget).find('.total-default-new').attr('data-price-value');
            }
            
            
            

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
                    'selectors': bundle_expert.selectors,
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
                    if(bundle_expert.selectors.product_page.price_parent!==''){
                        $(price_selector).closest(bundle_expert.selectors.product_page.price_parent).html(html);
                    }else{
                        $(price_selector).parent().replaceWith(html);
                    }
                } else {
                    $(price_selector).html(html);
                }


                if((!this.first_change_price && this.animate_price)){
                
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

BundleExpert.show_bundle_errors = function (widget_element, json) {


    if (json['error']) {

        if (typeof window['be_custom']['show_bundle_errors'] === 'function') {
            be_custom.show_bundle_errors(widget_element, json)
        } else {
            if (json['error']['product_as_kit']) {
                bundle_expert.show_product_as_kit_error(json['error']['product_as_kit']);
                delete json['error']['product_as_kit'];
                if(json['error'].length==0){
                    delete json['error'];
                    delete json['error_text'];
                    return;
                }
            }

            
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


    }
}

BundleExpert.update_cart_html = function (json) {

    if (be_custom_header_cart!=='' && typeof window['be_custom_header_cart']['update_cart_html'] === 'function') {
        be_custom_header_cart.update_cart_html(json);
    }else{
        if (typeof window['be_custom']['update_cart_html'] === 'function') {
            be_custom.update_cart_html(json)
        } else {
            $('#content').parent().before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

            
            setTimeout(function () {
                $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
            }, 100);

            $('html, body').animate({scrollTop: 0}, 'slow');

            $('#cart > ul').load('index.php?route=common/cart/info ul li');
        }
    }


    if (typeof window['be_popup_bundle'] !== 'undefined') {
        be_popup_bundle.close_after_cart();
    }


}

BundleExpert.update_active_option_class = function (form_group) {
    $(form_group).find('label').removeClass('be-selected-option');
    
    $(form_group).find('input[type="radio"]:checked, input[type="checkbox"]:checked').closest('label').addClass('be-selected-option');
    

    be_custom.point('020', {'form_group': form_group});
}


BundleExpert.init_upload_file_buttons_in_widget = function (widget) {
    $(widget).find('[id^=button-upload]').click(function () {
        bundle_expert.init_upload_file(this);
    })
}

BundleExpert.init_upload_file = function (element) {
    var node = element;

    var kit_form = $(element).closest('.kit-widget');

    $(kit_form).find('#form-upload').remove();

    $(kit_form).prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

    $(kit_form).find('#form-upload input[name=\'file\']').trigger('click');

    if (typeof bundle_expert.file_timer !== 'undefined') {
        clearInterval(bundle_expert.file_timer);
    }

    bundle_expert.file_timer = setInterval(function () {
        if ($(kit_form).find('#form-upload input[name=\'file\']').val() != '') {
            clearInterval(bundle_expert.file_timer);

            var url = bundle_expert.store_url + 'index.php?route=tool/upload';
            $.ajax({
                
                url: url,
                type: 'post',
                dataType: 'json',
                data: new FormData($(kit_form).find('#form-upload')[0]),
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $(node).button('loading');
                },
                complete: function () {
                    $(node).button('reset');
                },
                success: function (json) {
                    $('.text-danger').remove();

                    if (json['error']) {
                        $(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
                    }

                    if (json['success']) {
                        alert(json['success']);

                        $(node).parent().find('input').attr('value', json['code']);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    }, 500);
}

BundleExpert.on_changed_product_as_kit_option = function (element) {
    var widget_unique_id = $(element).attr('data-widget-unique-id');
    var widget = $('.kit-widget[data-widget-unique-id='+widget_unique_id+']');

    bundle_expert.setWidgetUpdateTotalEnable(widget, false);

    bundle_expert.copy_changed_product_as_kit_option(widget, element);

    bundle_expert.setWidgetUpdateTotalEnable(widget, true);

    be_custom.point('042', {'option':element, 'widget': widget});

    bundle_expert.update_widget_kit_total(widget);
}


BundleExpert.copy_changed_product_as_kit_option = function (widget, element) {

    var main_product_id = $(bundle_expert.selectors.product_page.product_data).find('input[name=product_id]');

    if($(main_product_id).length>0){
        main_product_id = $(main_product_id).val();
        var item_product = $(widget).find('.kit-item-product[data-product-id='+main_product_id+']');

        if($(item_product).length>0){
            var options_target_container = item_product;
            var input_val = $(element).val();
            var target_element = '';
            if ($(element).attr('type') === 'radio') {
                var checked = $(element).prop('checked');
                var option_id = $(element).attr('name');
                option_id = option_id.replace('option[', '');
                option_id = option_id.replace(']', '');
                var option_value_id = $(element).val();
                target_element = $(options_target_container).find('[data-product-option-id="' + option_id + '"][data-product-option-value-id=' + option_value_id + ']');
                $(target_element).prop('checked', checked);
            } else if ($(element).attr('type') === 'checkbox') {
                var checked = $(element).prop('checked');
                var option_id = $(element).closest('[id^=input-option]').attr('id');
                option_id = option_id.replace('input-option', '');
                var option_value_id = $(element).val();
                target_element = $(options_target_container).find('[data-product-option-id="' + option_id + '"][data-product-option-value-id=' + option_value_id + ']');
                $(target_element).prop('checked', checked);
            } else if ($(element).is('select')) {
                var value = $(element).val();
                target_element = $(options_target_container).find('[name="' + input_name + '"]');
                $(target_element).val(value);
            } else if ($(element).is('textarea')) {
                var value = $(element).val();
                var option_id = $(element).attr('id');
                option_id = option_id.replace('input-option', '');
                target_element = $(options_target_container).find('textarea[id=input-option' + option_id + ']');
                $(target_element).val(value);
            } else {
                var value = $(element).val();
                var option_id = $(element).attr('id');
                option_id = option_id.replace('input-option', '');
                target_element = $(options_target_container).find('[id=input-option' + option_id + ']');
                $(target_element).val(value);
            }
            bundle_expert_form.on_change_product_option(target_element);
        }

    }

}

BundleExpert.on_checkbox_in_kit_changed = function (element, clear_kit_form) {

    if (typeof clear_kit_form === "undefined") {
        clear_kit_form = true;
    }

    

    var kit_item = $(element).closest('.kit-item');

    var widget = $(element).closest('.kit-widget');

    var checkbox_value = $(element).prop('checked');

    
    if($(kit_item).hasClass('free-product')) {
        if (checkbox_value) {
            bundle_expert.free_product_add_to_kit(kit_item, clear_kit_form);
        } else {
            bundle_expert.free_product_remove_from_kit(kit_item, clear_kit_form);
        }
    }

    
    if($(kit_item).closest('.product-to-choice-container').length>0) {
        var container = $(kit_item).closest('.product-to-choice-container');

        
        
        if(checkbox_value){
            $(kit_item).find('.kit-item-product').addClass('product-selected');

            
            if($(kit_item).hasClass('empty-mode-can-be-empty')){
                $(kit_item).find('[data-id=input-is-item-is-empty]').val(0);
            }
            

            $(container).find('.kit-item').not(kit_item).each(function (index, item_element) {
                $(item_element).find('.kit-item-product').removeClass('product-selected');

                
                if($(item_element).hasClass('empty-mode-can-be-empty')){
                    $(item_element).find('[data-id=input-is-item-is-empty]').val(1);
                }
                

                $(item_element).find('.kit-item-in-checkbox input').prop('checked', false);
            })
        }else{

            if($(kit_item).hasClass('empty-mode-can-be-empty')){
                $(kit_item).find('.kit-item-product').removeClass('product-selected');
            }else{
                $(element).prop('checked', true);
            }
        }


        
        if(clear_kit_form){
            var widget_unique_id = $(widget).attr('data-widget-unique-id');
            $('.bundle-expert-forms-container').find('[data-widget-unique-id=' + widget_unique_id + ']').remove();

        }



    }

    be_custom.point('034', {'widget': widget,'kit_item': kit_item});

    bundle_expert.update_widget_kit_total(widget);
}

BundleExpert.add_product_id_to_category_products = function (widget) {
    category_product_selector = widget['config_category_page']['selector'];
    var first_empty_index = -1;
    $(category_product_selector).each(function (index, element) {
        var added = $(element).find('.bundle-expert-product-id');

        if($(added).length===0){
            if(first_empty_index<0){
                first_empty_index = index;
            }
            var html = '<div class="bundle-expert-product-id" style="display: none" data-product-id="'+bundle_expert.category_products[index-first_empty_index]+'"></div>';
            $(element).append(html);
        }else{

            
        }
    })
}

BundleExpert.add_product_id_to_category_products_old = function (widget) {
    category_product_selector = widget['config_category_page']['selector'];

    $(category_product_selector).each(function (index, element) {
        var added = $(element).find('.bundle-expert-product-id');
        if($(added).length===0){
            var html = '<div class="bundle-expert-product-id" style="display: none" data-product-id="'+bundle_expert.category_products[index]+'"></div>';
            $(element).append(html);
        }else{
            return false;
        }
    })
}

BundleExpert.is_widget_checkbox_mode = function (widget) {
    if($(widget).hasClass('checkbox-mode')){
        return true;
    }else{
        return false;
    }
}

BundleExpert.setWidgetUpdateTotalEnable = function (widget, enable) {
    if(enable){
        $(widget).removeClass('not-update-total');
    }else{
        $(widget).addClass('not-update-total');
    }
}

BundleExpert.checkWidgetUpdateTotalEnable = function (widget) {

    if($(widget).hasClass('not-update-total')){
        enable = false;
    }else{
        enable = true;
    }

    return enable;
}

BundleExpert.updateFreeProductContainerItem = function(widget, item_position){
    var kit_items = $(widget).find('#free-item-products-container .free-products-group[data-item-position=' + item_position + '] .kit-item');
    if($(kit_items).length>0){
        $(widget).find('#free-item-products-container [data-group-item-position=' + item_position + ']').show();
    }else{
        $(widget).find('#free-item-products-container [data-group-item-position=' + item_position + ']').hide();
    }
}
BundleExpert.addGroupToKit = function(button){
    var widget = $(button).closest('.kit-widget');

    var item_position = $(button).attr('data-item-position');

    var group = $(widget).find('#free-item-products-container').find('.kit-item[data-item-position='+item_position+']');

    bundle_expert.setWidgetUpdateTotalEnable(widget, false);

    $(group).each(function (index, item) {
        bundle_expert.free_product_add_to_kit(item);
    });

    bundle_expert.setWidgetUpdateTotalEnable(widget, true);
    bundle_expert.update_widget_kit_total(widget);

    bundle_expert.updateFreeProductContainerItem(widget, item_position);



}
BundleExpert.removeGroupFromKit = function(button){
    var widget = $(button).closest('.kit-widget');

    var item_position = $(button).attr('data-item-position');

    var group = $(widget).find('#free-products-container-in-kit').find('.kit-item[data-item-position='+item_position+']');

    bundle_expert.setWidgetUpdateTotalEnable(widget, false);

    $(group).each(function (index, item) {
        bundle_expert.free_product_remove_from_kit(item);
    });

    bundle_expert.setWidgetUpdateTotalEnable(widget, true);
    bundle_expert.update_widget_kit_total(widget);

    bundle_expert.updateFreeProductContainerItem(widget, item_position);

    

    

}

BundleExpert.open_product_page = function(element,blank){
    var href = $(element).attr('data-href');

    if(typeof href=="undefined"){
        bundle_expert_form.set_active_product(element);
    }else{
        if(blank){
            var win = window.open(href, '_blank');
            if (win) {
                
                win.focus();
            } else {
                
                window.location.href = href;
            }
        }else{
            window.location.href = href;

        }
    }

}

var BundleExpertForm = {};


BundleExpertForm.constructor = function () {
    
    this.items_carousel = '';
    this.select_item_products_mode = false;
    this.open_mode = '';
    this.form_error = '';
    this.just_show_free_product_by_id = '';
}


BundleExpertForm.hide_kit_form = function () {
    var modal = $('#bundle-expert-form-modal');

    var kit_form = $(modal).find('.modal-body .body-content .kit-form').first();

    if (kit_form.length > 0) {
        

        this.select_item_products_mode = false;
        bundle_expert_form.enable_buttons_on_select_item_product();
        bundle_expert_form.enable_buttons_on_load();
        $(kit_form).find('.kit-form-widget-main-container').show();
        $(kit_form).find('.kit-form-item-products-main-container').hide();
        $(modal).find('.cart-merge-popup-message').hide();

        
        $(modal).find('.owl-carousel-bundle-expert').trigger('destroy.owl.carousel');

        if ($.fn.datetimepicker) {
            $(kit_form).find('.time').datetimepicker('update');
            $(kit_form).find('.date').datetimepicker('update');
            $(kit_form).find('.datetime').datetimepicker('update');

            $('.kit-widget').find('.time').datetimepicker('update');
            $('.kit-widget').find('.date').datetimepicker('update');
            $('.kit-widget').find('.datetime').datetimepicker('update');
        }

        
        
        
        var stored_container = $(kit_form).find('.stored-total-values-before-select-product');
        if($(stored_container).find('.stored-total-default').html()!==''){
            $(kit_form).attr('total-default', $(stored_container).find('.stored-total-default').html());
            $(kit_form).attr('total-default-new', $(stored_container).find('.stored-total-default-new').html());
            $(kit_form).attr('total-kit', $(stored_container).find('.stored-total-kit').html());
            $(kit_form).attr('total-kit-old', $(stored_container).find('.stored-total-kit-old').html());
        }

        
        var widget_unique_id = $(modal).find('.modal-body .body-content').attr('data-widget-unique-id');

        
        bundle_expert_form.update_widget_by_form(kit_form);

        var html = $(kit_form).clone();

        
        this.fix_clone_select($(modal).find('.modal-body .body-content .kit-form'), html);

        $(modal).find('.modal-body .body-content').html('');


        
        $(html).find('.kit-form-item-products-container').html('');

        
        $(html).removeClass('select-product-mode');
        this.open_mode = "";
        

        $('.bundle-expert-forms-container').find('[data-widget-unique-id=' + widget_unique_id + ']').html(html);

        
        if (bundle_expert.admin_mode) {
            $('#bundle-expert-container .bundle-expert-forms-container').html('');
            $('#bundle-expert-container .bundle-expert-forms-container-from-cart').html('');
        }


        
        $(modal).find('.modal-footer button[data-dismiss=modal]').click();
    }
}

BundleExpertForm._get_active_item_position = function (kit_form) {

    var kit_form_widget = $(kit_form).find('.kit-form-widget-main-container').first();

    
    var kit_item_product_active = $(kit_form_widget).find('.kit-item-product.active');

    var index = $(kit_form_widget).find('.kit-item-product').index(kit_item_product_active)

    

    return index;
}

BundleExpertForm._get_active_item = function (kit_form) {

    var kit_form_widget = $(kit_form).find('.kit-form-widget-main-container').first();

    
    var kit_item_product_active = $(kit_form_widget).find('.kit-item-product.active');

    return kit_item_product_active;
}

BundleExpertForm._get_active_item_product_position = function (kit_form, item_positon) {

    var kit_form_widget = $(kit_form).find('.kit-form-item-products-main-container .kit-form-item-products[data-item-position=' + item_positon + ']').first();

    
    var kit_item_products = $(kit_form_widget).find('.kit-item-product');
    var kit_item_product_active = $(kit_form_widget).find('.kit-item-product.active');

    var index = $(kit_item_products).index(kit_item_product_active);

    return index;
}

BundleExpertForm._get_active_item_product_id = function (kit_form, item_positon, item_positon_free) {

    var kit_form_widget = $(kit_form).find('.kit-form-item-products-main-container .kit-form-item-products[data-item-position=' + item_positon + '][data-item-position-free=' + item_positon_free + ']').first();

    
    var kit_item_product_active = $(kit_form_widget).find('.kit-item-product.active');
    var product_id = $(kit_item_product_active).attr('data-product-id');

    return product_id;
}

BundleExpertForm._get_active_item_product = function (kit_form, item_positon) {

    var kit_form_widget = $(kit_form).find('.kit-form-item-products-main-container .kit-form-item-products[data-item-position=' + item_positon + ']').first();

    
    var kit_item_product_active = $(kit_form_widget).find('.kit-item-product.active');

    return kit_item_product_active;
}

BundleExpertForm.show_modal_kit_form = function (kit_id, main_product_id, widget_unique_id, active_item_index, active_item_index_free, main_product_in_cart, cart_merge_enable) {
    var modal = $('#bundle-expert-form-modal');

    $(modal).find('.modal-content').attr('kit_id', kit_id);
    $(modal).find('.modal-content').attr('main_product_id', main_product_id);
    $(modal).find('.modal-content').attr('widget_unique_id', widget_unique_id);
    $(modal).find('.modal-content').attr('active_item_index', active_item_index);
    $(modal).find('.modal-content').attr('active_item_index_free', active_item_index_free);
    $(modal).find('.modal-content').attr('main_product_in_cart', main_product_in_cart);
    $(modal).find('.modal-content').attr('cart_merge_enable', cart_merge_enable);
    $(modal).find('.modal-content').attr('kit_from_cart', 0);

    var tt = jQuery._data( $(modal), "events" );

    var events = [];

    $.each($._data($(modal)[0], "events"), function(i, event) {
        
        events[i]=event;
        $.each(event, function(j, h) {
            
        });
    });

    $(modal).modal('show');
}

BundleExpertForm.show_kit_form = function () {
    
    var owl_carousel_status = bundle_expert_help.get_owl_carousel_status();
    

    if(!owl_carousel_status){
        bundle_expert_help.load_owl_carousel('kit_form');
        return;
    }

    var modal = $('#bundle-expert-form-modal');

    var kit_id = $(modal).find('.modal-content').attr('kit_id');
    var main_product_id = $(modal).find('.modal-content').attr('main_product_id');
    var widget_unique_id = $(modal).find('.modal-content').attr('widget_unique_id');
    var active_item_index = $(modal).find('.modal-content').attr('active_item_index');
    var active_item_index_free = $(modal).find('.modal-content').attr('active_item_index_free');
    var main_product_in_cart = $(modal).find('.modal-content').attr('main_product_in_cart');
    var cart_merge_enable = $(modal).find('.modal-content').attr('cart_merge_enable');

    
    var kit_form = $('.bundle-expert-forms-container').find('div[data-widget-unique-id=' + widget_unique_id + ']').find('.kit-form[data-kit-id=' + kit_id + ']').first();

    var kit_title = $('.kit-widget[data-widget-unique-id=' + widget_unique_id + ']').attr('data-kit-title');
    $(modal).find('.modal-title').html(kit_title);

    if (kit_form.length !== 0) {

        

        if (this.open_mode === "select_product_mode") {
            $(kit_form).addClass('select-product-mode');
        }

        $(kit_form).find('.kit-form-product-container .loader-overlay').hide();

        $(modal).find('.modal-body .body-loading').hide();

        $(modal).find('.modal-body .body-content').html(kit_form);

        $(modal).find('.modal-footer .total-row .total-kit').html($(kit_form).attr('total-kit'));
        $(modal).find('.modal-footer .total-row .total').html($(kit_form).attr('total-default'));

        $(modal).find('.modal-body .body-content').attr('data-widget-unique-id', widget_unique_id);
        $(kit_form).find('input[name=main_product_in_cart]').val(main_product_in_cart);
        $(kit_form).find('input[name=cart_merge_enable]').val(cart_merge_enable);

        
        var kit_items = $(kit_form).find('.kit-form-widget-container .kit-item');
        var first_item = $(kit_items).find('.kit-item-product[data-item-position=' + active_item_index + '][data-item-position-free=' + active_item_index_free + ']');
        if ($(first_item).length == 0) {
            var first_item = $(kit_items).find('.kit-item-product[data-item-position=' + active_item_index + '][data-item-position-free=-1]');
        }

        bundle_expert_form.enable_buttons_on_load();

        
        this.items_carousel = $(kit_form).find('#bundle-expert-form-widget-carousel');
        $(this.items_carousel).removeClass('owl-hidden');
        bundle_expert.init_carousel(this.items_carousel, -1);

        bundle_expert_form.set_active_product(first_item);

        bundle_expert_form.update_kit_form_total(kit_form);

        if (this.open_mode === "select_product_mode") {
            bundle_expert_form.show_item_products(first_item)
        }

        if (this.form_error !== '') {
            this.show_form_error(kit_form, this.form_error);
        }

        be_custom.point('009', {'kit_form': kit_form});

    } else {
        $(modal).find('.modal-body .body-loading').show();

        $(modal).find('.modal-body .body-content').html('');

        var url = '';
        if (!bundle_expert.admin_mode) {
            url = 'index.php?route=module/bundle_expert/getKitForm';
        } else {
            if (!bundle_expert.admin_mode_new_api) {
                url = 'index.php?route=catalog/bundle_expert/api&token=' + bundle_expert.admin_token + '&api=api/bundle_expert/getKitForm';
            } else {
                url = bundle_expert.store_url + 'index.php?route=api/bundle_expert/getKitForm&' + bundle_expert.api_token_name + bundle_expert.api_token_value; 
            }
        }

        $(modal).find('.modal-body .kit-form-loading-overlay').html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
        $(modal).find('.modal-body .kit-form-loading-overlay').show();

        
        var widget = $('.kit-widget[data-widget-unique-id=' + widget_unique_id + ']');

        

        
        var free_products = $(widget).find('.kit-items .kit-item');
        var data = $(free_products).find('.product-selected input[type=\'text\'], .product-selected input[type=\'hidden\'], .product-selected input[type=\'radio\']:checked, .product-selected input[type=\'checkbox\']:checked, .product-selected select, .product-selected textarea').serializeArray();
        data = $(data).add({'name': 'kit_id', 'value': kit_id});
        data = $(data).add({'name': 'main_product_id', 'value': main_product_id});
        data = $(data).add({'name': 'main_product_in_cart', 'value': main_product_in_cart});



        data = bundle_expert_help.normalize_options_names(data);

        

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            
            dataType: 'json',
            beforeSend: function () {
                
            },
            complete: function () {
                
                $(modal).find('.modal-body .kit-form-loading-overlay').hide();
            },
            success: function (json) {
                $(modal).find('.modal-body .kit-form-loading-overlay').hide();
                if (json['html']) {

                    var html = '<div data-widget-unique-id="' + widget_unique_id + '">' + json['html'] + '</div>'
                    $('.bundle-expert-forms-container').append(html);

                    var form_element = $('.bundle-expert-forms-container').find('div[data-widget-unique-id=' + widget_unique_id + ']').find('.kit-form[data-kit-id=' + kit_id + ']').first();

                    bundle_expert_form.init_kit_form(form_element);

                    bundle_expert_form.show_kit_form(kit_id, main_product_id, widget_unique_id, active_item_index, main_product_in_cart);


                }

                if (json['kit_error']) {
                    $(modal).find('.modal-body .body-loading').hide();

                    var html = '<div id="kit-form-empty-message">' + json['kit_error'] + '</div>'

                    $(modal).find('.modal-body .body-content').html(html);
                }

            }
        });
    }
}

BundleExpertForm.show_modal_kit_form_from_cart = function (kit_unique_id, active_item_index, active_item_index_free) {
    var modal = $('#bundle-expert-form-modal');

    $(modal).find('.modal-content').attr('kit_unique_id', kit_unique_id);
    $(modal).find('.modal-content').attr('active_item_index', active_item_index);
    $(modal).find('.modal-content').attr('active_item_index_free', active_item_index_free);
    $(modal).find('.modal-content').attr('kit_from_cart', 1);

    $(modal).modal('show');
}

BundleExpertForm.show_kit_form_from_cart = function () {
    var modal = $('#bundle-expert-form-modal');

    var kit_unique_id = $(modal).find('.modal-content').attr('kit_unique_id');
    var active_item_index = $(modal).find('.modal-content').attr('active_item_index');
    var active_item_index_free = $(modal).find('.modal-content').attr('active_item_index_free');

    $(modal).find('.modal-title').html('...');

    
    var kit_form = $('.bundle-expert-forms-container-from-cart').find('div[kit-unique-id=' + kit_unique_id + ']').find('.kit-form').first();

    if (kit_form.length !== 0) {

        $(kit_form).find('.kit-form-product-container .loader-overlay').hide();

        $(modal).find('.modal-body .body-loading').hide();

        $(modal).find('.modal-title').html($(kit_form).find('input[name=kit_title]').val());

        $(modal).find('.modal-body .body-content').html(kit_form);

        $(modal).find('.modal-footer .total-row .total-kit').html($(kit_form).attr('total-kit'));
        $(modal).find('.modal-footer .total-row .total').html($(kit_form).attr('total-default'));

        $(modal).find('.modal-body .body-content').attr('kit-unique-id', kit_unique_id);
        

        var kit_items = $(kit_form).find('.kit-form-widget-container .kit-item');
        var first_item = $(kit_items[active_item_index]).find('.kit-item-product');

        bundle_expert_form.set_active_product(first_item);


        
        this.items_carousel = $(kit_form).find('#bundle-expert-form-widget-carousel');
        $(this.items_carousel).removeClass('owl-hidden');
        bundle_expert.init_carousel(this.items_carousel, -1);

        bundle_expert_form.update_kit_form_total(kit_form);

        be_custom.point('010', {'kit_form': kit_form});
    } else {
        $(modal).find('.modal-body .kit-form-loading-overlay').html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
        $(modal).find('.modal-body .kit-form-loading-overlay').show();

        $(modal).find('.modal-body .body-content').html('');

        var url = '';
        if (!bundle_expert.admin_mode) {
            url = 'index.php?route=module/bundle_expert/getKitFormFromCart';
        } else {
            if (!bundle_expert.admin_mode_new_api) {
                url = 'index.php?route=catalog/bundle_expert/api&token=' + bundle_expert.admin_token + '&api=api/bundle_expert/getKitFormFromCart';
                ;
            } else {
                url = bundle_expert.store_url + 'index.php?route=api/bundle_expert/getKitFormFromCart&' + bundle_expert.api_token_name + bundle_expert.api_token_value; 

                if (bundle_expert.debug_mode) bundle_expert.setCookie('XDEBUG_SESSION', 'XDEBUG_ECLIPSE');
            }
        }

        
        var owl_carousel_status = bundle_expert_help.get_owl_carousel_status();

        if(!owl_carousel_status){
            bundle_expert_help.load_owl_carousel('kit_form_from_cart');
            return;
        }

        $.ajax({
            url: url,
            type: 'post',
            data: {kit_unique_id: kit_unique_id},
            dataType: 'json',
            beforeSend: function () {
                
            },
            complete: function () {
                
                $(modal).find('.modal-body .kit-form-loading-overlay').hide();
            },
            success: function (json) {
                $(modal).find('.modal-body .kit-form-loading-overlay').hide();
                if (json['html']) {

                    var html = '<div kit-unique-id="' + kit_unique_id + '">' + json['html'] + '</div>'
                    $('.bundle-expert-forms-container-from-cart').append(html);

                    var form_element = $('.bundle-expert-forms-container-from-cart').find('div[kit-unique-id=' + kit_unique_id + ']').find('.kit-form').first();

                    bundle_expert_form.init_kit_form(form_element);

                    bundle_expert_form.show_kit_form_from_cart(kit_unique_id, active_item_index);
                }

                if (json['kit_error']) {
                    $(modal).find('.modal-body .body-loading').hide();

                    var html = '<div id="kit-form-empty-message">' + json['kit_error'] + '</div>'

                    $(modal).find('.modal-body .body-content').html(html);
                }

                if (json['kit_enable_status_error']) {
                    $(modal).find('.modal-body .body-loading').hide();

                    var html = '<div id="kit-form-empty-message">' + json['kit_enable_status_error_text'] + '</div>'

                    $(modal).find('.modal-body .body-content').html(html);
                }
            }
        });
    }
}

BundleExpertForm.init_kit_form = function (kit_form) {

    $(kit_form).find('.kit-form-product-container .be-product-form').each(function (index1, form_element) {
        bundle_expert_form.init_product_form(form_element);
    })

    
    var kit_form_product_container = $(kit_form).find('.kit-form-product-container .kit-form-product-item-conatiner .be-product-form ');

    
    $(kit_form_product_container).each(function (index, element) {
        var tt= $(element).find('#input-for-empty-mode-item-is-empty').val();
        if($(element).find('#input-for-empty-mode-item-is-empty').val()==1){
            $(element).removeClass('be-selected');
        }else{
            $(element).addClass('be-selected');
        }
    })

    

    

}

BundleExpertForm.init_product_form = function (product_form) {

    var unique_id = $(product_form).attr('unique-id');
    var kit_form = $(product_form).closest('.kit-form');
    var product_id = $(product_form).attr('data-product-id');

    var item_positon = $(product_form).attr('data-item-position');
    var item_positon_free = parseInt($(product_form).attr('data-item-position-free'));
    var item_product_positon = $(product_form).attr('data-item-product-position');

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    
    

    
    $(product_form).find('.nav.nav-tabs li a').each(function (index, element) {
        var href = $(element).attr('href');
        href = href + '-' + unique_id;
        $(element).attr('href', href);
    })

    $(product_form).find('.tab-content .tab-pane').each(function (index, element) {
        var id = $(element).attr('id');
        id = id + '-' + unique_id;
        $(element).attr('id', id);
    })

    
    
    
    

    
    bundle_expert_form.product_form_init_standart(product_form);

    
    $(product_form).find('#options-container .form-group').each(function (index, group) {
        bundle_expert.update_active_option_class(group);
    })

    be_custom.point('006', {'product_form': product_form});
}

BundleExpertForm.on_click_product_form_option = function (click_element) {
    be_custom.point('037', {'click_element': click_element});
}

BundleExpertForm.on_change_product_option = function (click_element) {
    var kit_form = $(click_element).closest('.kit-form');

    
    bundle_expert.update_active_option_class($(click_element).closest('.form-group'));


    
    if ($(kit_form).length > 0) {
        if (!bundle_expert_form.select_item_products_mode) {
            be_custom.point('008', {
                'product_form': $(click_element).closest('.be-product-form'),
                'click_element': click_element
            });

            bundle_expert_form.update_kit_form_total(kit_form)

            
            bundle_expert_form.clear_items_products(kit_form);
        } else {
            be_custom.point('012', {'product_form': $(click_element).closest('.be-product-form'), 'click_element': click_element});

            
            bundle_expert_form.update_kit_form_total(kit_form)
        }
    } else {
        
        var widget = $(click_element).closest('.kit-widget');
        var kit_item = $(click_element).closest('.kit-item-product');
        var widget_unique_id = $(widget).attr('data-widget-unique-id');
        var kit_form = $('#bundle-expert-container .bundle-expert-forms-container div[data-widget-unique-id=' + widget_unique_id + ']').find('.kit-form');
        if ($(kit_form).length > 0) {
            var item_position = $(kit_item).attr('data-item-position');
            var item_position_free = $(kit_item).attr('data-item-position-free');
            var product_id = $(kit_item).attr('data-product-id');
            var product_form = $(kit_form).find('.be-product-form[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + '][data-product-id=' + product_id + ']');

            if ($(product_form).length > 0) {
                
                var options_source = $(kit_item).find('.product-options-values').contents();
                var options_target_container = $(product_form).find('#options-container');

                bundle_expert_help.copy_options(options_source, options_target_container, 'to_form');
            }

        }

        
        bundle_expert.on_click_change_quantity($(kit_item).find('.input-quantity-field'),0);
        

        bundle_expert.update_widget_kit_total(widget);

        be_custom.point('007', {'widget': widget, 'click_element': click_element});

    }
}

BundleExpertForm.show_product_form = function (kit_form, kit_id, main_product_id, item_position, item_position_free, product_id, kit_from_cart_unique_id, completeCallback) {

    var product_form = $(kit_form).find('.kit-form-product-container').find('.be-product-form[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + '][data-product-id=' + product_id + ']');

    
    if (product_form.length > 0) {
        $(product_form).find('#carousel-kit-product-form-images .be-image-additional').on('click', function (event) {
            var image_href = $(this).find('a').attr('href-link');
            $(product_form).find('.thumbnail.main-image img').attr('src', image_href);

        });

        
        if (this.select_item_products_mode === true || this.open_mode == "select_product_mode") {
            
        } else {
            $(product_form).find('.quantity-buttons').show();
        }

        
        if (this.select_item_products_mode === true) {
            $(product_form).find('#content-empty').addClass('hidden');
            $(product_form).find('#content').removeClass('hidden');
        } else {
            var is_item_empty = parseInt($(kit_form).find('.kit-items').find('.kit-item[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']').attr('data-empty-item'));
            if (is_item_empty === 1) {
                $(product_form).find('#content').addClass('hidden');
                $(product_form).find('#content-empty').removeClass('hidden');
            } else {
                $(product_form).find('#content').removeClass('hidden');
                $(product_form).find('#content-empty').addClass('hidden');
            }
        }
        ;

        
        $(product_form).find('#options-container .form-group').each(function (index, group) {
            bundle_expert.update_active_option_class(group);
        })

        bundle_expert_form.product_form_init_standart(product_form);

        

        
        $(product_form).closest('.kit-form-product-item-conatiner').show();
        $(product_form).show();
        
        $(product_form).addClass('active-form');
        

        
        $(product_form).find('[id^=button-upload]').unbind('click');
        $(product_form).find('[id^=button-upload]').bind('click', function () {
            bundle_expert_form.init_upload_file(this);
        })

        
        be_custom.point('016', {'kit_form': kit_form, 'product_form': product_form});

        
        var image_carousel = $(product_form).find('#carousel-kit-product-form-images');
        $(image_carousel).removeClass('owl-hidden');
        $(image_carousel).owlCarouselBundleExpert({
            items: 4,
            autoPlay: false,

            mouseDrag: false,
            
            


            nav: true,
            navText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
            dots: false,
            center: false
        });

        if (typeof completeCallback !== 'undefined'){
            completeCallback(product_form);
        }

        
        
        if (this.select_item_products_mode === true) {
            
            var product_quantity = $(product_form).find('input.input-quantity-field').val();
            bundle_expert_form.update_product_quantity(kit_form, item_position, item_position_free, product_quantity);
            
            this.update_kit_form_total(kit_form);
        }

    } else {
        var url = '';
        if (!bundle_expert.admin_mode) {
            url = 'index.php?route=module/bundle_expert/getKitFormProduct';
        } else {
            if (!bundle_expert.admin_mode_new_api) {
                url = 'index.php?route=catalog/bundle_expert/api&token=' + bundle_expert.admin_token + '&api=api/bundle_expert/getKitFormProduct';
            } else {
                url = bundle_expert.store_url + 'index.php?route=api/bundle_expert/getKitFormProduct&' + bundle_expert.api_token_name + bundle_expert.api_token_value; 
            }
            
        }

        if (this.select_item_products_mode) {
            var is_free_product = 0;
        } else {
            var active_item = $(kit_form).find('.kit-form-widget-container .kit-items .kit-item-product.product-selected[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']');
            var is_free_product = $(active_item).find('input[data-id=input-is-free-product]').val();
        }

        $.ajax({
            url: url,
            type: 'post',
            data: {
                kit_id: kit_id,
                main_product_id: main_product_id,
                item_position: item_position,
                item_position_free: item_position_free,
                product_id: product_id,
                kit_from_cart_unique_id: kit_from_cart_unique_id,
                is_free_product: is_free_product
            },
            dataType: 'json',
            beforeSend: function () {
                $(kit_form).find('.kit-form-product-container .loader-overlay').show();
                bundle_expert_form.disable_buttons_on_load();
                bundle_expert_form.disable_kit_form_widget_items_on_product_load();
            },
            complete: function () {
                $(kit_form).find('.kit-form-product-container .loader-overlay').hide();
                bundle_expert_form.enable_buttons_on_load();
                bundle_expert_form.enable_kit_form_widget_items_on_product_load();

            },
            success: function (json) {

                if (json['kit_error']) {
                    var modal = $('#bundle-expert-form-modal');

                    $(modal).find('.modal-body .body-loading').hide();

                    var html = '<div id="kit-form-empty-message">' + json['kit_error'] + '</div>'

                    $(modal).find('.modal-body .body-content').html(html);
                }

                if (json['html']) {
                    
                    $(kit_form).find('.kit-form-product-container .kit-form-product-item-conatiner[data-item-position=' + item_position + ']').append(json['html'])

                    
                    product_form = $(kit_form).find('.kit-form-product-container').find('.be-product-form[data-item-position=' + item_position + '][data-product-id=' + product_id + ']');

                    if ($(product_form).length !== 0) {
                        bundle_expert_form.init_product_form(product_form);

                        bundle_expert_form.show_product_form(kit_form, kit_id, main_product_id, item_position, item_position_free, product_id, kit_from_cart_unique_id, completeCallback);
                        
                        
                    }

                }


            }
        });

    }
}

BundleExpertForm.on_click_select_item_product_button = function (click_element) {
    
    
    var kit_form = $(click_element).closest('.kit-form');

    if (kit_form.length > 0) {
        bundle_expert_form.open_mode = "";

        bundle_expert_form.set_active_product($(click_element).closest('.kit-item').find('.kit-item-product .be-product-thumb'));

        bundle_expert_form.show_item_products($(click_element).closest('.kit-item').find('.kit-item-product .be-product-thumb'));
    } else {
        bundle_expert_form.open_mode = "select_product_mode";

        bundle_expert_form.set_active_product($(click_element).closest('.kit-item').find('.kit-item-product .be-product-thumb'));
    }

}

BundleExpertForm.on_click_add_free_product_button = function (click_element) {
    var kit_item = $(click_element).closest('.kit-item');
    bundle_expert.free_product_add_to_kit(kit_item);

}







BundleExpertForm.set_active_product = function (click_element) {

    if ($(click_element).hasClass('not-clickable'))
        return;

    
    if ($(click_element).closest('#free-item-products-container').length > 0) {
        bundle_expert_form.open_mode = "select_product_mode";
    }

    var kit_form = $(click_element).closest('.kit-form');

    bundle_expert_form.item_products_filter_set_visible(false);


    if (kit_form.length == 0) {
        
        var widget = $(click_element).closest('.kit-widget');

        
        
        

        var kit_id = $(widget).attr('data-kit-id');

        
        var main_product_id = $(widget).find('.kit-data').find('input[name=main_product_id]').val();

        
        var main_product_in_cart = $(widget).find('.kit-data').find('input[name=main_product_in_cart]').val();

        var cart_merge_enable = $(widget).find('.kit-data').find('input[name=cart_merge_enable]').val();

        var widget_unique_id = $(widget).attr('data-widget-unique-id');

        var active_item_position = $(click_element).closest('.kit-item-product').attr('data-item-position');
        var active_item_position_free = $(click_element).closest('.kit-item-product').attr('data-item-position-free');

        
        var is_free_product = parseInt($(click_element).closest('.kit-item-product').find('input[data-id=input-is-free-product]').val());
        var free_product_in_kit = parseInt($(click_element).closest('.kit-item-product').find('input[data-id=input-free-product-in-kit]').val());
        if (is_free_product == 1 && free_product_in_kit == 0) {
            bundle_expert_form.open_mode = "select_product_mode";
            active_item_position_free = -1;
            bundle_expert_form.just_show_free_product_by_id = $(click_element).closest('.kit-item-product').attr('data-product-id');
        }

        bundle_expert_form.show_modal_kit_form(kit_id, main_product_id, widget_unique_id, active_item_position, active_item_position_free, main_product_in_cart, cart_merge_enable);

        return;
    }

    var widget = $(click_element).closest('.kit-form-widget-container, .kit-form-item-products');

    if ($(widget).hasClass('kit-form-item-products'))
        var is_item_products_widget = true;
    else
        var is_item_products_widget = false;

    var kit_id = $(kit_form).find('.kit-data input[name=kit_id]').val();

    var main_product_id = $(kit_form).find('.kit-data input[name=main_product_id]').val();

    var kit_from_cart_unique_id = $(kit_form).find('.kit-data input[name=kit_from_cart_unique_id]').val();

    var kit_item = $(click_element).closest('.kit-item');

    var kit_item_product = $(click_element).closest('.kit-item-product');

    
    var item_position = $(kit_item_product).attr('data-item-position');
    var item_position_free = $(kit_item_product).attr('data-item-position-free');

    var product_id = $(kit_item_product).attr('data-product-id');

    
    $(widget).find('.kit-item-product').removeClass('active');
    $(widget).find('.empty-kit-item-product').removeClass('active');
    $(kit_item_product).addClass('active');
    $(kit_item_product).closest('.kit-item').find('.empty-kit-item-product').addClass('active');

    
    if (parseInt($(kit_item).attr('data-empty-item')) === 1) {
        var name = $(kit_item).find('.empty-kit-item-product .be-product-title').html();
    } else {
        var name = $(kit_item).find('.be-product-title').html();
    }
    

    $(kit_form).find('.kit-form-arrows .be-product-title').html(name);
    var href = $(kit_form).find('.kit-form-arrows .be-product-title a').attr('data-href');
    $(kit_form).find('.kit-form-arrows .be-product-title a').attr('href', href);

    
    if ($(kit_item).hasClass('selectable')) {
        $(kit_form).find('.kit-form-arrows .product-select-button').show();
    } else {
        $(kit_form).find('.kit-form-arrows .product-select-button').hide();
    }

    
    
    if (bundle_expert_form.items_carousel !== '') {
        
        if (is_item_products_widget)
            var product_index = $(kit_item_product).attr('data-item-product-position');
        else
            var product_index = $(kit_item_product).attr('data-item-position');

        $(widget).find('.owl-carousel-bundle-expert').trigger('to.owl.carousel', [product_index, 500, true]);

    }


    
    $(kit_form).find('.kit-form-product-item-conatiner').hide();
    $(kit_form).find('.kit-form-product-container').find('.be-product-form').hide();
    
    $(kit_form).find('.kit-form-product-container').find('.be-product-form').removeClass('active-form');
    


    be_custom.point('015', {'kit_form': kit_form});

    
    this.show_product_form(kit_form, kit_id, main_product_id, item_position, item_position_free, product_id, kit_from_cart_unique_id);

}

BundleExpertForm.set_prev_active_product = function (click_element) {

    var kit_form = $(click_element).closest('.kit-form');
    var kit_form_widget = $(kit_form).find('.kit-form-widget-main-container').first();

    
    var kit_items = $(kit_form_widget).find('.kit-item');

    
    var index = this._get_active_item_position(kit_form);

    if (index > 0) {
        index--;
    } else {
        index = kit_items.length - 1;
        return;
    }

    this.set_active_product($(kit_items[index]).find('.kit-item-product').first());

}

BundleExpertForm.set_prev_active_select_product = function (click_element) {
    var kit_form = $(click_element).closest('.kit-form');

    
    var active_item_position = this._get_active_item_position(kit_form);
    var item_position = $(click_element).attr('data-item-position');

    var kit_form_widget = $(kit_form).find('.kit-form-item-products-main-container .kit-form-item-products[data-item-position=' + item_position + ']').first();

    
    var kit_item_products = $(kit_form_widget).find('.kit-item-product');

    
    var index = this._get_active_item_product_position(kit_form, item_position);

    if (index > 0) {
        index--;
    } else {
        index = kit_item_products.length - 1;
        return;
    }

    this.set_active_product($(kit_item_products[index]));

}

BundleExpertForm.set_next_active_product = function (click_element) {
    var kit_form = $(click_element).closest('.kit-form');
    var kit_form_widget = $(kit_form).find('.kit-form-widget-main-container').first();

    
    var kit_items = $(kit_form_widget).find('.kit-item');

    
    var index = this._get_active_item_position(kit_form);

    if (index >= kit_items.length - 1) {
        index = 0;
        return;
    } else {
        index++;
    }

    this.set_active_product($(kit_items[index]).find('.kit-item-product').first());

}

BundleExpertForm.set_next_active_select_product = function (click_element) {
    var kit_form = $(click_element).closest('.kit-form');

    
    var active_item_position = this._get_active_item_position(kit_form);
    var item_position = $(click_element).attr('data-item-position');

    var kit_form_widget = $(kit_form).find('.kit-form-item-products-main-container .kit-form-item-products[data-item-position=' + item_position + ']').first();

    
    var kit_item_products = $(kit_form_widget).find('.kit-item-product');

    
    var index = this._get_active_item_product_position(kit_form, item_position);

    if (index >= kit_item_products.length - 1) {
        index = 0;
        $(kit_form_widget).find('#more-button').click();
        return;
    } else {
        index++;
    }

    this.set_active_product($(kit_item_products[index]));

}

BundleExpertForm.show_item_products = function (click_element) {

    this.select_item_products_mode = true;

    bundle_expert_form.disable_buttons_on_select_item_product();

    var kit_form = $(click_element).closest('.kit-form');

    var kit_id = $(kit_form).find('.kit-data input[name=kit_id]').val();

    var main_product_id = $(kit_form).find('.kit-data input[name=main_product_id]').val();

    var kit_from_cart_unique_id = $(kit_form).find('.kit-data input[name=kit_from_cart_unique_id]').val();

    

    var kit_item_product = $(kit_form).find('.kit-form-widget-container .kit-item-product.active');

    
    var item_position = $(kit_item_product).attr('data-item-position');
    var item_position_free = $(kit_item_product).attr('data-item-position-free');

    
    var product_id = $(kit_item_product).attr('data-product-id');

    var item_products_main_container = $(kit_form).find('.kit-form-item-products-main-container').first();
    var item_products_container = $(kit_form).find('.kit-form-item-products-container').first();

    $(item_products_container).find('.kit-form-item-products').hide();
    

    $(item_products_main_container).attr('active-item-position', item_position);

    var item_products = $(item_products_container).find('.kit-form-item-products[data-item-position=' + item_position + ']');

    var select_button = $(kit_form).find('.kit-form-item-products-buttons #button-set-item-product-selected');
    $(select_button).attr('data-item-position', item_position);
    $(select_button).attr('data-item-position-free', item_position_free);
    var arrows_button = $(kit_form).find('.kit-form-item-products-main-container .arrows-container button');
    $(arrows_button).attr('data-item-position', item_position);
    $(arrows_button).attr('data-item-position-free', item_position_free);

    
    
    var stored_container =$(kit_form).find('.stored-total-values-before-select-product');
    $(stored_container).find('.stored-total-default').html($(kit_form).attr('total-default'));
    $(stored_container).find('.stored-total-default-new').html($(kit_form).attr('total-default-new'));
    $(stored_container).find('.stored-total-kit').html($(kit_form).attr('total-kit'));
    $(stored_container).find('.stored-total-kit-old').html($(kit_form).attr('total-kit-old'));



    if (item_products.length === 0) {

        bundle_expert_form.load_item_products(kit_form, kit_id, main_product_id, item_position, item_position_free, kit_from_cart_unique_id, 0);

    } else {
        
        var active_element = $(item_products_container).find('.kit-item-product[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + '][data-product-id=' + product_id + ']');
        if ($(active_element).length == 0) {
            active_element = $(item_products_container).find('.kit-item-product[data-item-position=' + item_position + ']').first();
        }
        bundle_expert_form.set_active_product(active_element);

        
        be_custom.point('027', {'active_element': active_element});
        

        
        $(item_products).show();
    }


    
    $(kit_form).find('.kit-form-widget-main-container').hide();
    $(item_products_main_container).show();

}

BundleExpertForm.load_item_products = function (kit_form, kit_id, main_product_id, item_position, item_position_free, kit_from_cart_unique_id, last_item_product_position) {
    var url = '';
    if (!bundle_expert.admin_mode) {
        url = 'index.php?route=module/bundle_expert/getKitItemProducts';
    } else {
        if (!bundle_expert.admin_mode_new_api) {
            url = 'index.php?route=catalog/bundle_expert/api&token=' + bundle_expert.admin_token + '&api=api/bundle_expert/getKitItemProducts';
        } else {
            url = bundle_expert.store_url + 'index.php?route=api/bundle_expert/getKitItemProducts&' + bundle_expert.api_token_name + bundle_expert.api_token_value; 
        }
    }


    var item_products_main_container = $(kit_form).find('.kit-form-item-products-main-container').first();
    var item_products_container = $(kit_form).find('.kit-form-item-products-container').first();
    var item_products = $(item_products_container).find('.kit-form-item-products[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']');
    var kit_item_product = $(kit_form).find('.kit-form-widget-container .kit-item-product.active');
    var product_id = $(kit_item_product).attr('data-product-id');
    var filter_title = $(kit_form).find('input[name=filter_title]').val();

    var data = bundle_expert_form._get_kit_form_data(kit_form);

    data = $(data).add({'name': 'kit_id', 'value': kit_id});
    data = $(data).add({'name': 'main_product_id', 'value': main_product_id});
    data = $(data).add({'name': 'item_position', 'value': item_position});
    data = $(data).add({'name': 'kit_from_cart_unique_id', 'value': kit_from_cart_unique_id});
    data = $(data).add({'name': 'last_item_product_position', 'value': last_item_product_position});
    data = $(data).add({'name': 'main_product_id', 'value': main_product_id});
    data = $(data).add({'name': 'filter_title', 'value': filter_title});

    $.ajax({
        url: url,
        type: 'post',
        
        data: data,
        dataType: 'json',
        beforeSend: function () {
            if (last_item_product_position == 0) {
                $(item_products_main_container).find('.loader-overlay').show();
                $(item_products_main_container).find('.kit-form-item-products-buttons').hide();
            } else {
                $(item_products).find('.item-more-button #more-button').hide();
                $(item_products).find('.item-more-button #loading-spinner').show();
            }

            bundle_expert_form.disable_buttons_on_load();
        },
        complete: function () {
            $(item_products_main_container).find('.loader-overlay').hide();
            $(item_products_main_container).find('.kit-form-item-products-buttons').show();
            
            

            item_products = $(item_products_container).find('.kit-form-item-products[data-item-position=' + item_position + ']');
            $(item_products).find('.item-more-button #more-button').show();
            $(item_products).find('.item-more-button #loading-spinner').hide();

            
            if ($(item_products).find('.item.product-item').length == 0)
                $(kit_form).find('#button-set-item-product-selected').prop('disabled', true);


        },
        success: function (json) {

            bundle_expert_form.enable_buttons_on_load();
            bundle_expert_form.disable_buttons_on_select_item_product();

            if (json['kit_error']) {
                var modal = $('#bundle-expert-form-modal');

                $(modal).find('.modal-body .body-loading').hide();

                var html = '<div id="kit-form-empty-message">' + json['kit_error'] + '</div>'

                $(modal).find('.modal-body .body-content').html(html);

                bundle_expert_form.hide_item_products($(modal).find('#button-set-item-product-selected').first())
            }

            if (json['html']) {
                
                if (last_item_product_position === 0) {
                    $(item_products_container).append(json['html'])

                    item_products = $(item_products_container).find('.kit-form-item-products[data-item-position=' + item_position + ']');

                    $(item_products).attr('data-last-item-product-position', json['last_item_product_position']);

                    
                    var active_element = $(item_products_container).find('.kit-item-product[data-item-position=' + item_position + '][data-product-id=' + product_id + ']');
                    if ($(active_element).length === 0) {
                        
                        if (bundle_expert_form.just_show_free_product_by_id !== '') {
                            active_element = $(item_products_container).find('.kit-item-product[data-item-position=' + item_position + '][data-product-id=' + bundle_expert_form.just_show_free_product_by_id + ']');
                            if ($(active_element).length == 0) {
                                active_element = $(item_products_container).find('.kit-item-product[data-item-position=' + item_position + ']').first();
                            }
                        } else {
                            
                            active_element = $(item_products_container).find('.kit-item-product[data-item-position=' + item_position + ']').first();
                        }
                        bundle_expert_form.just_show_free_product_by_id = '';
                    }



                    
                    if ($(item_products).find('.item.product-item').length != 0) {
                        var items_products_carousel = $(item_products).find('#carousel-kit-form-item-products');
                        var container_width = $(kit_form).width();
                        $(items_products_carousel).removeClass('owl-hidden');
                        bundle_expert.init_carousel(items_products_carousel, container_width);
                    } else {

                        $(item_products).html($(item_products).find('.empty-list-item'));
                    }

                    if ($(active_element).length !== 0) {
                        bundle_expert_form.set_active_product(active_element);

                        
                        be_custom.point('028', {'active_element': active_element});
                        
                    }

                    
                    $(item_products).show();

                } else {

                    var item_products = $(item_products_container).find('.kit-form-item-products[data-item-position=' + item_position + ']');

                    var carousel = $(item_products).find('#carousel-kit-form-item-products').first();

                    
                    

                    var more_button = $(item_products).find('#more-button').closest('.item');
                    var more_button_position = $(item_products).find('.item').index(more_button);
                    $(carousel).trigger('remove.owl.carousel', [more_button_position]);

                    $(item_products).attr('data-last-item-product-position', json['last_item_product_position']);

                    var items = $(json['html']).find('.item');
                    for (var i = 0; i < items.length; i++) {
                        $(carousel).trigger('add.owl.carousel', [items[i]]).trigger('refresh.owl.carousel');
                    }

                    
                    var new_active_item = $(item_products).find('.kit-item-product')[last_item_product_position];
                    if($(new_active_item).length>0){
                        bundle_expert_form.set_active_product(new_active_item);
                    }
                    

                }

                
                
                $(item_products).find('.kit-item-product').each(function (index, element) {
                    $(element).attr('data-item-product-position', index);
                    
                })
            }

            be_custom.point('038', {'kit_form': kit_form});
        }
    });
}

BundleExpertForm.load_more_item_products = function (click_element) {
    var kit_form = $(click_element).closest('.kit-form');

    var kit_id = $(kit_form).attr('data-kit-id');
    var main_product_id = $(kit_form).attr('data-main-product-id');
    var item_position = $(click_element).closest('.kit-form-item-products').attr('data-item-position');
    var item_position_free = $(click_element).closest('.kit-form-item-products').attr('data-item-position-free');
    var kit_from_cart_unique_id = $(kit_form).find('.kit-data input[name=kit_from_cart_unique_id]').val();
    var last_item_product_position = parseInt($(click_element).closest('.kit-form-item-products').attr('data-last-item-product-position')) + 1;

    bundle_expert_form.load_item_products(kit_form, kit_id, main_product_id, item_position, item_position_free, kit_from_cart_unique_id, last_item_product_position);

}

BundleExpertForm.item_products_filter_click = function () {
    var modal = $('#bundle-expert-form-modal');
    var kit_form = $(modal).find('.kit-form');

    var kit_form_arrows = $(kit_form).find('.kit-form-item-products-main-container .kit-form-arrows');

    if ($(kit_form_arrows).find('.product-title-filter').hasClass('hidden')) {
        bundle_expert_form.item_products_filter_set_visible(true);
        $(kit_form_arrows).find('.product-title-filter input').focus();
    } else {

        var kit_id = $(kit_form).attr('data-kit-id');
        var main_product_id = $(kit_form).attr('data-main-product-id');
        var item_position = $(kit_form).find('.kit-form-item-products-main-container').attr('active-item-position');
        var item_position_free = $(kit_form).find('.kit-form-item-products-main-container').attr('active-item-position-free');
        var kit_from_cart_unique_id = $(kit_form).find('.kit-data input[name=kit_from_cart_unique_id]').val();
        var last_item_product_position = 0;

        var item_products = $(kit_form).find('.kit-form-item-products-container .kit-form-item-products[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']');
        $(item_products).remove();

        bundle_expert_form.load_item_products(kit_form, kit_id, main_product_id, item_position, item_position_free, kit_from_cart_unique_id, last_item_product_position);
    }


}

BundleExpertForm.item_products_filter_reset = function () {
    var modal = $('#bundle-expert-form-modal');

    var kit_form = $(modal).find('.kit-form');

    

    

    var filter_title = $(kit_form).find('input[name=filter_title]').val();

    if (filter_title !== '') {
        $(kit_form).find('input[name=filter_title]').val('');
        bundle_expert_form.item_products_filter_click();
    }

    bundle_expert_form.item_products_filter_set_visible(false);

}

BundleExpertForm.item_products_filter_set_visible = function (visible) {
    var modal = $('#bundle-expert-form-modal');

    var kit_form = $(modal).find('.kit-form');

    var kit_form_arrows = $(kit_form).find('.kit-form-item-products-main-container .kit-form-arrows');

    if (visible) {
        $(kit_form_arrows).find('.be-product-title').addClass('hidden');
        $(kit_form_arrows).find('.product-title-filter').removeClass('hidden');
    } else {
        $(kit_form_arrows).find('.be-product-title').removeClass('hidden');
        $(kit_form_arrows).find('.product-title-filter').addClass('hidden');
    }


}

BundleExpertForm.update_kit_form_product_free_index = function (kit_form, item_position) {

    
    var selected_free_products = $(kit_form).find('.be-product-form.be-selected[data-item-position='+item_position+']:not(.product-form-free-product-plug)');

    $(selected_free_products).each(function (index0, product_form) {
        $(product_form).find('input, textarea, select').each(function (index1, element) {
            var attr_name = $(element).attr('name');
            var item_position_free = index0;
            attr_name = attr_name.replace(/\[[0-900]\]\[[0-900]\]/g, '['+item_position+']['+item_position_free+']');
            $(element).attr('name', attr_name);
        });
    })

    
   var selected_free_products = $(kit_form).find('.kit-form-widget-main-container .kit-items .kit-item:not(.free-product-template)[data-item-position=' + item_position + ']').closest('.kit-item');
    

    $(selected_free_products).each(function (index0, product_element) {
        $(product_element).find('input, textarea, select').each(function (index1, element) {
            var attr_name = $(element).attr('name');
            var item_position_free = index0;
            attr_name = attr_name.replace(/\[[0-900]\]\[[0-900]\]/g, '['+item_position+']['+item_position_free+']');
            $(element).attr('name', attr_name);
        });
    })

}

BundleExpertForm.set_kit_form_product_free_index = function (kit_form, product_id, item_position, item_position_free) {

    var product_form = $(kit_form).find('.be-product-form[data-item-position=' + item_position + '][data-product-id=' + product_id + ']');
    var widget_item = $(kit_form).find('.kit-form-widget-main-container .kit-items .kit-item-product[data-item-position=' + item_position + '][data-product-id=' + product_id + ']').closest('.kit-item');

    if ($(widget_item).hasClass('free-product')) {

        
        
        
        bundle_expert_form.update_kit_form_product_free_index(kit_form, item_position);
        
        
        
        
        
        
        
        
        


        
        
        
        
        
        
        
    }
}

BundleExpertForm.on_click_delete_item_from_kit = function (click_element) {
    var kit_form = $(click_element).closest('.kit-form');

    var kit_item = $(click_element).closest('.kit-item');
    var kit_item_product = $(kit_item).find('.kit-item-product');

    var product_id = $(kit_item_product).attr('data-product-id');

    if($(kit_form).length>0){

        var item_position = $(kit_item).attr('data-item-position');
        var item_position_free = $(kit_item).attr('data-item-position-free');

        var kit_form_product_container = $(kit_form).find('.kit-form-product-container').first();

        var product_form = $(kit_form_product_container).find('.be-product-form[data-item-position=' + item_position + '][data-product-id=' + product_id + ']');

        var is_free_product = $(kit_item).hasClass('free-product');

        
        
        

        if($(kit_item_product).hasClass('active')){
            if(is_free_product) {
                var prev = $(kit_item).closest('.owl-item').prev('.owl-item');
                if ($(prev).length > 0) {
                    $(prev).find('.be-product-thumb').click();
                } else {
                    var next = $(kit_item).closest('.owl-item').next('.owl-item');
                    if ($(next).length > 0) {
                        $(next).find('.be-product-thumb').click();
                    }
                }
            }
        }

        
        if(is_free_product) {
            $(product_form).replaceWith('');
        }else{
            $(product_form).replaceWith('');
            if($(kit_item_product).hasClass('active')) {
                var empty_form = $(kit_form_product_container).find('.be-product-form[data-item-position=' + item_position + '][data-product-id=' + '-1' + ']');


                $(empty_form).addClass('active-form');
            }
        }


        
        if(is_free_product){
            var carousel_item = $(kit_item).closest('.owl-item');
            bundle_expert_form.removeFreeProductFromCarousel(carousel_item);
        }else{

        }

        
        
        $(kit_form).find('.kit-form-item-products[data-item-position=' + item_position + ']').replaceWith('');

        
        var widget_unique_id = $(kit_form).closest('.body-content').attr('data-widget-unique-id');

        var widget = $('.kit-widget[data-widget-unique-id='+widget_unique_id+']').first();

        if($(widget).length>0){
            
            var widget_kit_item = $(widget).find('.kit-items .kit-item .kit-item-product[data-item-position='+item_position+'][data-product-id='+product_id+']');

            bundle_expert.setWidgetUpdateTotalEnable(widget, false);

            if($(widget).hasClass('checkbox-mode')){
                var check_box = $(widget_kit_item).find('.kit-item-in-checkbox input');

                $(check_box).prop('checked', false);

                bundle_expert.on_checkbox_in_kit_changed(check_box, false);
            }else{
                if($(widget).hasClass('kit-widget-carousel')){
                    var carousel_item = $(widget).find('.kit-item-product[data-item-position='+item_position+'][data-product-id='+product_id+']');;
                    if($(carousel_item).length>0){
                        carousel_item = $(carousel_item).closest('.owl-item');
                        bundle_expert_form.removeFreeProductFromCarousel(carousel_item);
                    }

                }else{
                    bundle_expert.on_click_remove_product_button(widget_kit_item, false);
                }

                
                
                
                
                
                
            }

            bundle_expert.setWidgetUpdateTotalEnable(widget, true);
        }

        
        
        if(is_free_product) {
            bundle_expert_form.update_kit_form_product_free_index(kit_form, item_position);
        }

        this.update_kit_form_total(kit_form);


    }
}

BundleExpertForm.set_item_product_selected = function (click_element) {
    var kit_form = $(click_element).closest('.kit-form');

    var select_button = $(kit_form).find('.kit-form-item-products-buttons #button-set-item-product-selected');
    var item_position = $(select_button).attr('data-item-position');
    var item_position_free = $(select_button).attr('data-item-position-free');

    var active_item_position = this._get_active_item_position(kit_form);
    var active_item = this._get_active_item(kit_form);

    var selected_item_product = $(kit_form).find('.kit-form-item-products[data-item-position=' + item_position + '] .kit-item-product.active').first();


    var is_free_product = $(selected_item_product).find('input[data-id=input-is-free-product]').val();

    var active_item_product = this._get_active_item_product(kit_form, item_position);

    var product_id = $(active_item_product).attr('data-product-id');

    var kit_form_product_container = $(kit_form).find('.kit-form-product-container').first();

    
    
    var product_forms = $(kit_form_product_container).find('.be-product-form[data-item-position=' + item_position + ']');
    var product_form = $(kit_form_product_container).find('.be-product-form[data-item-position=' + item_position + '][data-product-id=' + product_id + ']');

    
    if (is_free_product !== "1") {
        $(product_forms).removeClass('be-selected');
        $(product_form).addClass('be-selected');
    } else {
        $(product_form).addClass('be-selected');
    }

    $(product_form).find('#content').removeClass('hidden');
    $(product_form).find('#content-empty').addClass('hidden');
    $(product_form).find('input#input-for-empty-mode-item-is-empty').val(0);


    if (is_free_product === "1")


        if (is_free_product === "1") {

        }


    
    if (is_free_product === "1") {
        var item_position_free = parseInt($(kit_form).find('.kit-form-widget-container').find('.kit-items .kit-item.free-product[data-item-position=' + item_position + ']').last().attr('data-item-position-free'));
        if (isNaN(item_position_free)) {
            item_position_free = 0;
        } else {
            item_position_free++;
        }


        $(product_form).find('input[data-id=input-free-product-in-kit]').val(1);
        $(product_form).find('input[data-id=input-checkbox-in-kit-field]').val(1);
        $(product_form).attr('data-item-position-free', item_position_free);
        $(product_form).find('input[data-id=input-item-position-free]').val(item_position_free);
        $(product_form).find('input, textarea, select').each(function (index, element) {
            var attr_name = $(element).attr('name');
            attr_name = attr_name.replace('kit_items_free[0]', 'kit_items_free[' + item_position + '][' + item_position_free + ']');
            
        });


        var clone = $(selected_item_product).closest('.kit-item').clone();

        $(clone).find('input[data-id=input-free-product-in-kit]').val(1);
        $(clone).find('input[data-id=input-checkbox-in-kit-field]').val(1);

        $(clone).attr('data-item-position-free', item_position_free);
        $(clone).find('.kit-item-product').attr('data-item-position-free', item_position_free);
        $(clone).find('input[data-id=input-item-position-free]').val(item_position_free);
        $(clone).find('input[data-id=input-checkbox-in-kit-field]').prop('checked', true);
        $(clone).find('.plus-symbol').first().removeClass('hidden');
        
        
        
        
        
        

        this.addFreeProductToCarousel(kit_form, clone);

    } else {
        var clone = $(selected_item_product).clone();
        $(active_item).closest('.kit-item').attr('data-empty-item', 0);
        $(active_item).closest('.kit-item').find('.empty-kit-item-product').addClass('hidden');
        $(clone).find('.selectable-item-container').removeClass('hidden');
        $(active_item).replaceWith(clone);
    }

    var quantity = $(product_form).find('input.input-quantity-field').val();
    $(product_form).find('input.input-quantity-field').val(quantity);
    $(clone).find('[data-id=input-quantity]').val(quantity);


    this.set_kit_form_product_free_index(kit_form, product_id, item_position, item_position_free);

    
    this.set_active_product($(clone).find('.be-product-thumb'));

    
    
    
    
    
    
    
    

    
    var is_main_product_position = $(active_item_product).closest('.kit-item').attr('data-is-main-product-position');
    if (is_main_product_position === "1") {
        $(kit_form).find('.kit-data').find('input[name=main_product_id]').val(product_id);
    }

    bundle_expert_form.update_product_quantity(kit_form, item_position, item_position_free, parseInt($(clone).find('input[data-id=input-quantity]').val()));

    
    bundle_expert_form.clear_items_products(kit_form);

    
    this.update_kit_form_total(kit_form);

    
    

    
    var hide = be_custom.point('029', {'product_form': product_form});
    if (hide !== false) {
        this.hide_item_products(click_element);
    }

    
    
    if (hide !== false) {
        if ($(clone).hasClass('free-product')) {
            $(kit_form).find('.kit-form-item-products[data-item-position=' + item_position + ']').replaceWith('');

        }
    }

    


}

BundleExpertForm.addFreeProductToCarousel = function (kit_form, item) {
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    


    var item_position = $(item).attr('data-item-position');

    var carousel = $(kit_form).find('#bundle-expert-form-widget-carousel.owl-carousel-bundle-expert').first();

    var add_free_items = $(carousel).find('.free-product-template').closest('.owl-item');
    var add_free_items_new = [];
    for (var i = 0; i < $(add_free_items).length; i++) {
        var add_free_item = add_free_items[i];
        var add_free_button_position = $(carousel).find('.owl-item').index(add_free_item);
        add_free_items_new[i] = $(add_free_item).find('.kit-item').clone();
        $(carousel).trigger('remove.owl.carousel', [add_free_button_position]);
    }

    $(carousel).trigger('add.owl.carousel', [item]).trigger('refresh.owl.carousel');

    for (var i = 0; i < $(add_free_items_new).length; i++) {
        var add_free_item = add_free_items_new[i];
        $(carousel).trigger('add.owl.carousel', [add_free_item]);
    }

    $(carousel).trigger('refresh.owl.carousel');

    $(carousel).trigger('destroy.owl.carousel');

    
    
    var container_width = $(kit_form).find('.kit-form-item-products-main-container').width();

    bundle_expert.init_carousel(carousel, container_width);



}
BundleExpertForm.removeFreeProductFromCarousel = function (carousel_item) {
    

    var carousel = $(carousel_item).closest('.owl-carousel');


    var owl_index = $(carousel).find('.owl-item').index(carousel_item);

    $(carousel).trigger('remove.owl.carousel', [owl_index]);

    $(carousel).trigger('destroy.owl.carousel');

    
    $(carousel).removeClass('owl-hidden');
    bundle_expert.init_carousel(carousel, -1);

    
    
    


    return;
}

BundleExpertForm.clear_items_products = function (form) {
    if (bundle_expert.reload_item_products === 1) {
        $(form).find('.kit-form-item-products-container').html('');
    }
}

BundleExpertForm.hide_item_products = function (click_element) {
    this.select_item_products_mode = false;


    bundle_expert_form.enable_buttons_on_select_item_product();

    var kit_form = $(click_element).closest('.kit-form');

    var item_products_main_container = $(kit_form).find('.kit-form-item-products-main-container').first();

    
    $(kit_form).find('.kit-form-widget-main-container').show();
    $(item_products_main_container).hide();

    
    
    
    var stored_container = $(kit_form).find('.stored-total-values-before-select-product');
    $(stored_container).find('.stored-total-default').html('');
    $(stored_container).find('.stored-total-default-new').html('');
    $(stored_container).find('.stored-total-kit').html('');
    $(stored_container).find('.stored-total-kit-old').html('');

    if (this.open_mode === "select_product_mode") {
        var widget_unique_id = $(kit_form).closest('.modal-content').attr('widget_unique_id');
        
        bundle_expert_form.hide_kit_form();

        
        var widget = $('.kit-widget[data-widget-unique-id=' + widget_unique_id + ']');
        bundle_expert.update_widget_kit_total(widget);
    } else {
        
        var active_item = this._get_active_item(kit_form);
        this.set_active_product(active_item);

        
        this.update_kit_form_total(kit_form);
    }

}

BundleExpertForm._get_kit_form_data = function (kit_form) {
    var data = $(kit_form).find('.kit-data').find('input[type=\'text\'], input[type=\'hidden\']');

    var data_products = $(kit_form).find('.kit-form-product-container .be-product-form.be-selected:not(.product-form-free-product-plug)').find('input[type=\'text\'],  input[type=\'hidden\'],  input[type=\'radio\']:checked,  input[type=\'checkbox\']:checked, select,  textarea, .kit-data input[type=\'hidden\']');

    
    var disabled = $(kit_form).find('.kit-form-product-container .be-product-form.be-selected').find('input:disabled, select:disabled, textarea:disabled');
    disabled.prop('disabled', false);

    var serialized = $(data_products).serializeArray();

    disabled.prop('disabled', true);

    data = $(data).add(serialized);

    return data
}

BundleExpertForm.show_form_error = function (kit_form, form_error) {
    var json = [];
    json = form_error;

    $(kit_form).find('.bottom-block-header').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error_text'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

    setTimeout(function () {
        $(kit_form).find('.bottom-block .alert').fadeOut(1000, 0);
    }, 8000);

    var j;
    var i;
    

    for (j in json['error']) {
        if (json['error'][j]['option']) {
            
            

            var item_position = json['error'][j]['item_position'];
            var item_position_free = json['error'][j]['item_position_free'];

            var active_product_form = $(kit_form).find('.kit-form-product-container .kit-form-product-item-conatiner .be-product-form.be-selected[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']');
            ;

            $(kit_form).find('.kit-form-widget-main-container').find('.kit-item[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']').find('.alert-icon').show();

            for (var k = 0; k < json['error'][j]['option'].length; k++) {
                var error_option = json['error'][j]['option'][k];

                for (i in error_option) {
                    var element = $(active_product_form).find('#input-option' + i.replace('_', '-'));

                    if (element.parent().hasClass('input-group')) {
                        element.parent().after('<div class="text-danger">' + error_option[i] + '</div>');
                    } else {
                        element.after('<div class="text-danger">' + error_option[i] + '</div>');
                    }
                }
            }

            


        }

        if (json['error'][j]['recurring']) {
            $(active_product_form).find('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error'][j]['recurring'] + '</div>');
        }


    }


    bundle_expert_form.scroll_to_option_error(kit_form, json);

    this.form_error = '';
}

BundleExpertForm.add_to_cart = function (element, update_mode) {
    var kit_form = $(element).closest('.kit-form');

    var form_products_container = $(kit_form).find('.kit-form-product-container .be-product-form.be-selected:not(.product-form-free-product-plug)');
    

    be_custom.point('032', {'kit_form': kit_form});

    var data = bundle_expert_form._get_kit_form_data(kit_form)

    if (update_mode) {
        var kit_from_cart_unique_id = $(kit_form).find('.kit-data input[name=kit_from_cart_unique_id]').val();
        data = $(data).add({'name': 'kit_from_cart_unique_id', 'value': kit_from_cart_unique_id});
    }

    if (!update_mode) {
        var widget_unique_id = $(kit_form).closest('.body-content').attr('data-widget-unique-id');
        data = $(data).add({'name': 'widget_unique_id', 'value': widget_unique_id});
    }

    
    
    if (!update_mode) {
        if (bundle_expert_help.is_kit_form_for_product_as_kit(kit_form)) {
            var product_data = bundle_expert_help.get_product_page_data_for_send();
            $(product_data).each(function (index, element) {
                data = $(data).add({'name': element.name, 'value': element.value});
            })
            var product_id = $(kit_form).attr('data-main-product-id');
            data = $(data).add({'name': 'product_as_kit_data[product_id]', 'value': product_id});
        }

    }

    be_custom.point('033', {'kit_form': kit_form});

    
    if (bundle_expert.admin_mode) {
        var order_edit_quantity_field_element = $('#tab-product [id=input-quantity]');
        if($(order_edit_quantity_field_element).length>0){
            var order_edit_quantity_field = $(order_edit_quantity_field_element).val();
            data = $(data).add({'name': 'order_edit_form_quantity_field', 'value': order_edit_quantity_field});
        }
    }

    var url = '';
    if (!bundle_expert.admin_mode) {
        url = 'index.php?route=checkout/bundle_expert/add_to_cart';
    } else {
        if (!bundle_expert.admin_mode_new_api) {
            url = 'index.php?route=catalog/bundle_expert/api&token=' + bundle_expert.admin_token + '&api=api/bundle_expert/addToCart';
        } else {
            url = bundle_expert.store_url + 'index.php?route=api/bundle_expert/addToCart&' + bundle_expert.api_token_name + bundle_expert.api_token_value; 
        }
        if (bundle_expert.debug_mode) bundle_expert.setCookie('XDEBUG_SESSION', 'XDEBUG_ECLIPSE');
        
    }

    

    $.ajax({
        url: url,
        type: 'post',
        data: data,
        dataType: 'json',
        beforeSend: function () {
            $(kit_form).find('.bottom-block .alert').remove();
            
            bundle_expert_form.disable_buttons_on_load();
            $(kit_form).find('.kit-form-widget-main-container .alert-icon').hide();
        },
        complete: function () {
            $(element).button('reset');
            bundle_expert_form.enable_buttons_on_load();
        },
        success: function (json) {
            bundle_expert_form.enable_buttons_on_load();

            $('.alert, .text-danger').remove();
            $('.form-group').removeClass('has-error');
            

            if (json['redirect']) {
                location = json['redirect'];
            }

            if (json['error']) {
                if (json['error']['product_as_kit']) {
                    var product_as_kit_error = json['error']['product_as_kit'];
                    delete json['error']['product_as_kit'];
                } else {
                    
                }

                bundle_expert_form.show_form_error(kit_form, json);

                if (product_as_kit_error) {
                    if (Object.keys(json['error']).length == 0) {
                        bundle_expert_form.hide_kit_form();
                        bundle_expert.show_product_as_kit_error(product_as_kit_error);
                    }
                }

            }else{
                if (json['error_text']) {
                    $(kit_form).find('.bottom-block-header').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error_text'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    
                }
            }

            if (json['success']) {

                if (bundle_expert.is_some_checkout_page) {
                    location.reload();
                    return;
                }

                
                var widget_unique_id = $(kit_form).closest('.body-content').attr('data-widget-unique-id');
                $('#bundle-expert-container .bundle-expert-forms-container [data-widget-unique-id=' + widget_unique_id + ']').replaceWith('');

                
                bundle_expert_form.hide_kit_form();

                if (!bundle_expert.admin_mode) {

                    bundle_expert.update_cart_html(json)


                    
                    be_custom.point('003', {'json': json});
                } else {
                    $('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                    
                    $('#button-refresh').click();

                    be_custom.point('038', {'kit_form': kit_form});
                }

            }else{

            }

            if (json['kit_actuality_error']) {
                $(kit_form).find('.bottom-block-header').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['kit_actuality_error_text'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            }

            if (json['kit_enable_status_error']) {

                $(kit_form).find('.bottom-block-header').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['kit_enable_status_error_text'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                if (json['kit_enable_status_error']['error_items']) {
                    for (i in json['kit_enable_status_error']['error_items']) {
                        var item_position = json['kit_enable_status_error']['error_items'][i];
                        $(kit_form).find('.kit-form-widget-main-container').find('.kit-item[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']').find('.alert-icon').show();
                    }

                }
            }

            if (json['cart_merge_question']) {
                bundle_expert_form.show_cart_merge_question(json['cart_merge_question']);
            }


        }
    });
}


BundleExpertForm.product_form_init_standart = function (product_form) {

    if ($.fn.datetimepicker) {
        $(product_form).find('.date').datetimepicker({
            pickTime: false
        });

        $(product_form).find('.datetime').datetimepicker({
            pickDate: true,
            pickTime: true
        });

        $(product_form).find('.time').datetimepicker({
            pickDate: false
        });

    }

    
    $(product_form).find('select[name=\'recurring_id\'], input[name="quantity"]').change(function () {
        $.ajax({
            url: 'index.php?route=product/product/getRecurringDescription',
            type: 'post',
            data: $('input[name=\'product_id\'], input[name=\'quantity\'], select[name=\'recurring_id\']'),
            dataType: 'json',
            beforeSend: function () {
                $('#recurring-description').html('');
            },
            success: function (json) {
                $('.alert, .text-danger').remove();

                if (json['success']) {
                    $('#recurring-description').html(json['success']);
                }
            }
        });
    });
}


BundleExpertForm.update_kit_form_total = function (kit_form) {
    var form_products_container = $(kit_form).find('.kit-form-product-container .be-product-form.be-selected:not(.product-form-free-product-plug)');
    

    var data = $(kit_form).find('.kit-data').find('input[type=\'text\'],  input[type=\'hidden\']');

    var kit_from_cart_unique_id = $(kit_form).find('.kit-data input[name=kit_from_cart_unique_id]').val()

    
    
    
    
    
    
    if (bundle_expert_form.select_item_products_mode) {
        var active_product = $(kit_form).find('.be-product-form.active-form').first();
        var active_product_id = $(active_product).attr('data-product-id');
        var active_product_position = $(active_product).attr('data-item-position');
        var active_product_position_free = $(active_product).attr('data-item-position-free');
        var selected_product =$(kit_form).find('.be-product-form.be-selected[data-item-position='+active_product_position+'][data-item-position-free='+active_product_position_free+']').first();
        var selected_product_position_free = $(selected_product).attr('data-item-position-free');
        active_product_position_free = Number.parseInt(selected_product_position_free)+1;
        if(!$(selected_product).is(active_product)){

            
            if ($(active_product).find('input[data-id=input-is-free-product]').val() !== '1') {
                $(selected_product).removeClass('be-selected');
            }
            
            if ($(active_product).find('input[data-id=input-is-free-product]').val() === '1'){
                $(active_product).find('input, textarea, select').each(function (index, element) {
                    var attr_name = $(element).attr('name');
                    attr_name = attr_name.replace('kit_items_free[' + active_product_position + '][0]', 'kit_items_free[' + active_product_position + '][' + active_product_position_free + ']');
                    $(element).attr('name', attr_name);
                });
            }

            $(active_product).addClass('be-selected');
        }
    }

    var data_products = $(kit_form).find('.kit-form-product-container .be-product-form.be-selected:not(.product-form-free-product-plug)').find('input[type=\'text\'],  input[type=\'hidden\'],  input[type=\'radio\']:checked,  input[type=\'checkbox\']:checked, select,  textarea, .kit-data input[type=\'hidden\']');

    
    var disabled = $(kit_form).find('.kit-form-product-container .be-product-form.be-selected').find('input:disabled, select:disabled, textarea:disabled');
    disabled.prop('disabled', false);

    var serialized = $(data_products).serializeArray();

    disabled.prop('disabled', true);

    data = $(data).add(serialized);

    data = $(data).add({'kit_from_cart_unique_id': kit_from_cart_unique_id});

    
    
    if($(kit_form).closest('.modal-content').attr('kit_from_cart')!=="1"){
        var widget_unique_id = $(kit_form).closest('.modal-content').attr('widget_unique_id');
        var widget = $('.kit-widget[data-widget-unique-id='+widget_unique_id+']');
        if ($(widget).length>0 && $(widget).hasClass('product-as-kit-mode')) {
            var product_data = bundle_expert_help.get_product_page_data_for_send();
            var data = data.add(product_data);

            
            
        }
    }



    var url = '';
    if (!bundle_expert.admin_mode) {
        url = 'index.php?route=checkout/bundle_expert/get_kit_total';
    } else {
        if (!bundle_expert.admin_mode_new_api) {
            url = 'index.php?route=catalog/bundle_expert/api&token=' + bundle_expert.admin_token + '&api=api/bundle_expert/getKitTotal';
        } else {
            url = bundle_expert.store_url + 'index.php?route=api/bundle_expert/getKitTotal&' + bundle_expert.api_token_name + bundle_expert.api_token_value; 
        }
        
    }

    

    $.ajax({
        url: url,
        type: 'post',
        data: data,
        dataType: 'json',
        beforeSend: function () {
            $(kit_form).find('.modal-footer .total-row .total-kit, .modal-footer .total-row .total').addClass('loading');
            $(kit_form).find('.modal-footer .total-row .total-loading-icon').show();
        },
        complete: function () {
            $(kit_form).find('.modal-footer .total-row .total-kit, .modal-footer .total-row .total').removeClass('loading');
            $(kit_form).find('.modal-footer .total-row .total-loading-icon').hide();
        },
        success: function (json) {

            if (json['error']) {

            }

            if (json['total_kit']) {
                $(kit_form).attr('total-kit', json['total_kit']);
                $(kit_form).attr('total-kit-old', json['total_kit_old']);
                $(kit_form).closest('.modal-content').find('.modal-footer .total-row .total-kit').html(json['total_kit'])

                $(kit_form).attr('total-default', json['total_default']);
                $(kit_form).attr('total-default-new', json['total_default_new']);
                if (json['total_kit'] === json['total_default'])
                    $(kit_form).closest('.modal-content').find('.modal-footer .total-row .total').html('')
                else
                    $(kit_form).closest('.modal-content').find('.modal-footer .total-row .total').html(json['total_default'])

                

                
                for (var i = 0; i < json['products_price_data'].length; i++) {
                    var price_data = json['products_price_data'][i];

                    
                    if (!bundle_expert_form.select_item_products_mode) {
                        var kit_item_poduct = $(kit_form).find('.kit-form-widget-container').find('.kit-item-product[data-item-position=' + price_data['item_position'] + '][data-item-position-free=' + price_data['item_position_free'] + '][data-product-id=' + price_data['product_id'] + ']');
                    }else{
                        var kit_item_poduct = $(kit_form).find('.kit-form-item-products-container').find('.kit-item .kit-item-product[data-item-position=' + price_data['item_position'] + '][data-item-position-free=' + price_data['item_position_free'] + '][data-product-id=' + price_data['product_id'] + ']');
                    }
                    

                    var kit_poduct_form = $(kit_form).find('.be-product-form.be-selected[data-item-position=' + price_data['item_position'] + '][data-item-position-free=' + price_data['item_position_free'] + '][data-product-id=' + price_data['product_id'] + ']');

                    if (price_data['special']) {
                        $(kit_item_poduct).find('.be-price .be-price-new').html(price_data['special']);
                        $(kit_item_poduct).find('.be-price .be-price-old').html(price_data['price']);

                        $(kit_poduct_form).find('.be-price-new h2').html(price_data['special']);
                        $(kit_poduct_form).find('.be-price-old span').html(price_data['price']);
                    } else {
                        $(kit_item_poduct).find('.be-price').html(price_data['price']);

                        $(kit_poduct_form).find('.be-price h2').html(price_data['price']);
                        
                    }

                    if (price_data['price_discount_text']) {
                        $(kit_item_poduct).find('.item-price-discount').html(price_data['price_discount_text']);
                    }
                }

                
                
            }

            
            
            
            
            if (bundle_expert_form.select_item_products_mode) {
                if(!$(selected_product).is(active_product)){
                    if ($(active_product).find('input[data-id=input-is-free-product]').val() !== '1') {
                        $(selected_product).addClass('be-selected');
                    }
                    
                    if ($(active_product).find('input[data-id=input-is-free-product]').val() === '1'){
                        $(active_product).find('input, textarea, select').each(function (index, element) {
                            var attr_name = $(element).attr('name');
                            attr_name = attr_name.replace('kit_items_free[' + active_product_position + ']['+active_product_position_free+']', 'kit_items_free[' + active_product_position + '][0]');
                            $(element).attr('name', attr_name);
                        });
                    }
                    $(active_product).removeClass('be-selected');
                }
            }

        }
    });
}

BundleExpertForm.show_cart_merge_question = function () {
    var modal = $('#bundle-expert-form-modal');

    $(modal).find('.cart-merge-popup-message').show();

}

BundleExpertForm.hide_cart_merge_question = function () {
    var modal = $('#bundle-expert-form-modal');

    $(modal).find('.cart-merge-popup-message').hide();

}

BundleExpertForm.set_cart_merge_mode = function (mode) {
    var modal = $('#bundle-expert-form-modal');

    var kit_form = $(modal).find('.kit-form').first();

    $(kit_form).find('.kit-data input[name=cart_merge_confirm]').val(mode);

    bundle_expert_form.hide_cart_merge_question();

    bundle_expert_form.add_to_cart($(kit_form).find('#add-to-cart-button').first(), false);
}


BundleExpertForm.fix_clone_select = function (clone_source, clone) {
    var selects = $(clone_source).find("select");
    $(selects).each(function (i) {
        var select = this;
        $(clone).find("select").eq(i).val($(select).val());
    });
}

BundleExpertForm.fix_clone_select_in_widget = function (clone_source, clone) {
    var selects = $(clone_source).filter("select");
    $(selects).each(function (i) {
        var select = this;
        var name = $(select).attr('name');
        name = bundle_expert_help.normalize_option_name(name);
        var value = $(select).val();
        $(clone).filter("select[name='" + name + "']").val(value);
    });

    return clone;
}

BundleExpertForm.disable_kit_form_widget_items_on_product_load = function () {
    var modal = $('#bundle-expert-form-modal');

    var kit_form = $(modal).find('.kit-form').first();

    $(kit_form).find('.kit-form-item-products-container .be-product-thumb').addClass('not-clickable');
}

BundleExpertForm.enable_kit_form_widget_items_on_product_load = function () {
    var modal = $('#bundle-expert-form-modal');

    var kit_form = $(modal).find('.kit-form').first();

    $(kit_form).find('.kit-form-item-products-container .be-product-thumb').removeClass('not-clickable');
}

BundleExpertForm.disable_buttons_on_load = function () {
    var modal = $('#bundle-expert-form-modal');

    var kit_form = $(modal).find('.kit-form').first();

    $(kit_form).find('#button-set-item-product-selected').prop('disabled', true);
    $(kit_form).find('#add-to-cart-button').prop('disabled', true);
    $(kit_form).find('#add-to-cart-button').addClass('disabled');

    $(kit_form).find('.kit-form-arrows .btn').prop('disabled', true);
}

BundleExpertForm.enable_buttons_on_load = function () {
    var modal = $('#bundle-expert-form-modal');

    var kit_form = $(modal).find('.kit-form').first();

    $(kit_form).find('#button-set-item-product-selected').prop('disabled', false);

    
    if (this.select_item_products_mode === false) {
        $(kit_form).find('#add-to-cart-button').prop('disabled', false);
        $(kit_form).find('#add-to-cart-button').removeClass('disabled');
        
        
    }

    $(kit_form).find('.kit-form-arrows .btn').prop('disabled', false);

}

BundleExpertForm.disable_buttons_on_select_item_product = function () {
    var modal = $('#bundle-expert-form-modal');

    var kit_form = $(modal).find('.kit-form').first();

    $(kit_form).find('#add-to-cart-button').prop('disabled', true);
    $(kit_form).find('#add-to-cart-button').removeClass('disabled');

    
    
}

BundleExpertForm.enable_buttons_on_select_item_product = function () {
    var modal = $('#bundle-expert-form-modal');

    var kit_form = $(modal).find('.kit-form').first();

    $(kit_form).find('#add-to-cart-button').removeClass('disabled');
    $(kit_form).find('#add-to-cart-button').prop('disabled', false);

    
    

}

BundleExpertForm.init_upload_file = function (element) {
    var node = element;

    var kit_form = $(element).closest('.kit-form');

    $(kit_form).find('#form-upload').remove();

    $(kit_form).prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

    $(kit_form).find('#form-upload input[name=\'file\']').trigger('click');

    if (typeof bundle_expert_form.file_timer !== 'undefined') {
        clearInterval(bundle_expert_form.file_timer);
    }

    bundle_expert_form.file_timer = setInterval(function () {
        if ($(kit_form).find('#form-upload input[name=\'file\']').val() != '') {
            clearInterval(bundle_expert_form.file_timer);

            var url = bundle_expert.store_url + 'index.php?route=tool/upload';
            $.ajax({
                
                url: url,
                type: 'post',
                dataType: 'json',
                data: new FormData($(kit_form).find('#form-upload')[0]),
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $(node).button('loading');
                },
                complete: function () {
                    $(node).button('reset');
                },
                success: function (json) {
                    $('.text-danger').remove();

                    if (json['error']) {
                        $(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
                    }

                    if (json['success']) {
                        alert(json['success']);

                        $(node).parent().find('input').attr('value', json['code']);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    }, 500);
}


BundleExpertForm.update_widget_by_form = function (kit_form) {
    if ($(kit_form).find('input[name=kit_from_cart]').val() !== "1") {

        var modal = $('#bundle-expert-form-modal');
        var widget_unique_id = $(modal).find('.modal-body .body-content').attr('data-widget-unique-id');

        
        
        var widget = $('.kit-widget[data-widget-unique-id=' + widget_unique_id + ']');

        
        var form_products_container = $(kit_form).find('.kit-form-product-item-conatiner .be-product-form.be-selected:not(.product-form-free-product-plug)');
        

        var free_products_table_mode = parseInt($(widget).attr('data-free-products-table-mode'));

        
        if(bundle_expert.is_widget_checkbox_mode(widget) ){
            $(widget).find('.kit-item[data-item-mode=select_product]').find('.kit-item-in-checkbox input').prop('checked', false)
            $(widget).find('.kit-item[data-item-mode=select_product] .kit-item-product').removeClass('product-selected');
        }

        
        
        var products = $(kit_form).find('.kit-form-product-item-conatiner .be-product-form.be-selected:not(.product-form-free-product-plug)');
        var j;
        for (j = 0; j < products.length; j++) {
            var product = products[j];

            var item_position = $(product).attr('data-item-position');
            var item_position_free = $(product).attr('data-item-position-free');
            var options_data_container = $(product).find('.options-data-container');

            
            var kit_items = $(kit_form).find('.kit-form-widget-container .kit-items');
            var kit_item = $(kit_items).find('.kit-item-product[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']');

            var product_id = $(kit_item).attr('data-product-id');
            var title = $(kit_item).find('.be-product-title').html();
            var image = $(kit_item).find('.be-image img').attr('src');
            var price = $(kit_item).find('.be-price').html();
            var item_quantity= $(kit_item).find('.item-quantity').html();
            item_quantity = item_quantity.replace('x','');
            item_quantity = parseInt(item_quantity);
            var item_price_discount = $(kit_item).find('.item-price-discount').html();
            var quantity = $(kit_item).find('input[data-id=input-quantity]').val();
            var is_free_product = $(kit_item).find('input[data-id=input-is-free-product]').val();
            var free_product_in_kit = $(kit_item).find('input[data-id=input-free-product-in-kit]').val();
            var checkbox_in_kit_field = $(kit_item).find('input[data-id=input-checkbox-in-kit-field]').prop('checked');

            if (is_free_product === "1" && free_product_in_kit === "0")
                continue;

            if(bundle_expert.is_widget_checkbox_mode(widget) ) {
                var widget_product_item = $(widget).find('.kit-items .kit-item-product[data-item-position=' + item_position + '][data-product-id=' + product_id + ']');
            }else{
                var widget_product_item = $(widget).find('.kit-items .kit-item-product[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']');
            }
            var widget_item = $(widget_product_item).closest('.kit-item');

            if (is_free_product === "1" && free_product_in_kit === "1") {
                
                
                if ($(widget).find('#kit-items-container .kit-item.free-product .kit-item-product[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + '][data-product-id=' + product_id + ']').length > 0) {
                    widget_product_item = $(widget).find('#kit-items-container .kit-item.free-product .kit-item-product[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + '][data-product-id=' + product_id + ']');
                    widget_item = $(widget_product_item).closest('.kit-item');
                } else {

                    
                    
                    
                    if ($(widget).hasClass('kit-widget-carousel')) {
                        
                        
                        var widget_product_item = $(widget).find('.kit-item-product[data-item-position=' + item_position + '][data-product-id='+product_id+']').first();
                        if($(widget_product_item.length>0)){
                            var free_product_add_item = $(widget).find('.free-product-add-button[data-item-position=' + item_position + ']').first();
                            var widget_item = free_product_add_item;
                            var widget_product_item = $(widget_item).find('.kit-item-product');

                            
                            continue;
                        }


                    }else{
                        if (free_products_table_mode === 0) {
                            var free_product_add_item = $(widget).find('.free-product-add-button[data-item-position=' + item_position + ']').first();
                            var widget_item = free_product_add_item;
                            var widget_product_item = $(widget_item).find('.kit-item-product');
                            var widget_item_template = widget_item;
                            widget_item = $(widget_item).clone();
                            $(widget_item).removeClass('free-product-template').removeClass('free-product-add-button').addClass('free-product');
                            $(widget_item).find('.empty-kit-item-product').remove();
                            

                            $(widget).find('.kit-items #kit-items-container').find('.free-product-container-in-kit[data-item-position=' + item_position + ']').append(widget_item);
                            $(widget).find('.kit-items #kit-items-container').find('.free-product-container-in-kit[data-item-position=' + item_position + ']').show();
                        } else {
                            
                            var widget_product_item = $(widget).find('#kit-items-container').find('.kit-item-product[data-item-position=' + item_position + '][data-product-id=' + product_id + ']');
                            if ($(widget_product_item).length == 0) {
                                var widget_product_item = $(widget).find('#free-item-products-container').find('.kit-item-product[data-item-position=' + item_position + '][data-product-id=' + product_id + ']');
                                var widget_item = $(widget_product_item).closest('.kit-item');
                                
                                $(widget).find('.kit-items #kit-items-container').find('.free-product-container-in-kit[data-item-position=' + item_position + ']').append(widget_item);
                                $(widget).find('.kit-items #kit-items-container').find('.free-product-container-in-kit[data-item-position=' + item_position + ']').show();

                                
                                if ($(widget).find('#free-item-products-container .free-items .kit-item[data-item-position=' + item_position + ']').length > 0) {
                                    $(widget).find('#free-item-products-container .empty-list-text[data-item-position=' + item_position + ']').hide();
                                } else {
                                    $(widget).find('#free-item-products-container .empty-list-text[data-item-position=' + item_position + ']').show();
                                }
                            } else {
                            }


                            var widget_item = $(widget_product_item).closest('.kit-item');
                            $(widget_item).find('.product-options-list').html('');
                            $(widget_item).find('.product-options-values').html('');
                        }

                    }

                    $(widget_item).find('.kit-item-product input, .kit-item-product textarea, .kit-item-product select').each(function (index, element) {
                        var attr_name = $(element).attr('name');
                        attr_name = attr_name.replace('kit_items_w[' + item_position + ']', 'kit_items_free_w[' + item_position + '][' + item_position_free + ']');
                        $(element).attr('name', attr_name);
                    });

                    widget_product_item = $(widget_item).find('.kit-item-product');
                    $(widget_product_item).addClass('product-selected');

                    if (free_products_table_mode === 0) {
                        
                        $(widget).find('.free-product-add-button').each(function (index, element) {
                            $(element).remove();
                            $(widget).find('.kit-items #kit-items-container').append(element);
                        });
                    }


                }

                $(widget_product_item).find('input, textarea, select').each(function (index, element) {
                    var attr_name = $(element).attr('name');
                    attr_name = attr_name.replace('kit_items[' + item_position + ']', 'kit_items_free[' + item_position + '][' + item_position_free + ']');
                    $(element).attr('name', attr_name);
                });

                
                if(bundle_expert.is_widget_checkbox_mode(widget)){
                    $(widget_product_item).find('.kit-item-in-checkbox input').prop('checked', true)
                    $(widget_product_item).addClass('product-selected');
                }
                
                if(bundle_expert.is_widget_checkbox_mode(widget)){
                    $(widget_item).find('.kit-item-product input, .kit-item-product textarea, .kit-item-product select').each(function (index, element) {
                        var attr_name = $(element).attr('name');
                        attr_name = attr_name.replace('kit_items_w[' + item_position + ']', 'kit_items_free_w[' + item_position + '][' + item_position_free + ']');
                        $(element).attr('name', attr_name);
                    });
                }
            }

            
            if(bundle_expert.is_widget_checkbox_mode(widget) && $(widget_item).attr('data-item-mode')=='select_product' ){
                $(widget_product_item).find('.kit-item-in-checkbox input').prop('checked', true)
                $(widget_product_item).addClass('product-selected');
            }

            
            if(!bundle_expert.is_widget_checkbox_mode(widget) && $(widget_item).attr('data-item-mode')=='select_product' ){
                $(widget_product_item).addClass('product-selected');
                
                
                
                
                
                
            }
            

            var product_id_prev = $(widget_product_item).attr('data-product-id');

            $(widget_item).attr('data-item-position', item_position);
            $(widget_item).attr('data-item-position-free', item_position_free);
            $(widget_product_item).attr('data-product-id', product_id);
            $(widget_product_item).attr('data-item-position', item_position);
            $(widget_product_item).attr('data-item-position-free', item_position_free);
            $(widget_product_item).find('input[data-id=input-product-id]').val(product_id);
            $(widget_product_item).find('input[data-id=input-quantity]').val(quantity);
            $(widget_product_item).find('input.input-quantity-field').val(quantity);
            $(widget_product_item).find('input[data-id=input-item-position-free]').val(item_position_free);
            $(widget_product_item).find('input[data-id=input-is-free-product]').val(is_free_product);
            $(widget_product_item).find('input[data-id=input-free-product-in-kit]').val(free_product_in_kit);
            if (checkbox_in_kit_field)
                $(widget_product_item).find('input[data-id=input-checkbox-in-kit-field]').prop('checked', true);
            else
                $(widget_product_item).find('input[data-id=input-checkbox-in-kit-field]').prop('checked', false);
            $(widget_product_item).find('.be-product-title').html(title);
            $(widget_product_item).find('.be-image img').attr('src', image);
            $(widget_product_item).find('.be-price').html(price);
            $(widget_product_item).find('.item-quantity').html('x' + item_quantity);
            $(widget_product_item).find('.table-item-quantity input').val(item_quantity);
            $(widget_product_item).find('.item-price-discount').html(item_price_discount);

            if (parseInt(item_quantity) === 1)
                $(widget_product_item).find('.item-quantity').addClass('hidden');
            else
                $(widget_product_item).find('.item-quantity').removeClass('hidden');

            if ($(kit_item).find('input[data-id=input-for-quantity-edit]').val() === "1") {
                $(widget_product_item).find('input[data-id=input-for-quantity-edit]').val(1);
                $(widget_product_item).find('.edit-buttons-enable-container').removeClass('hidden');
                $(widget_product_item).find('.edit-buttons-disable-container').addClass('hidden');
            } else {
                $(widget_product_item).find('input[data-id=input-for-quantity-edit]').val(0)
                $(widget_product_item).find('.edit-buttons-enable-container').addClass('hidden');
                $(widget_product_item).find('.edit-buttons-disable-container').removeClass('hidden');
                $(widget_product_item).find('.edit-buttons-disable-container').html('x ' + quantity);
            }

            if (item_price_discount === '')
                $(widget_product_item).find('.item-price-discount').addClass('hidden');
            else
                $(widget_product_item).find('.item-price-discount').removeClass('hidden');

            if ($(widget).hasClass('kit-widget-table')) {
                var price_new_element = $(widget_product_item).find('.be-price .be-price-new');
                $(widget_product_item).find('.be-price').append(price_new_element);
            }

            var is_empty_item = parseInt($(kit_item).closest('.kit-item').attr('data-empty-item'));
            
            if (!bundle_expert_form.select_item_products_mode) {

                $(widget_item).closest('.kit-item').attr('data-empty-item', is_empty_item);
                if (is_empty_item === 1) {
                    $(widget_product_item).addClass('hidden');
                    $(widget_product_item).closest('.kit-item').find('.empty-kit-item-product').removeClass('hidden');
                } else {
                    $(widget_product_item).removeClass('hidden');
                    $(widget_product_item).closest('.kit-item').find('.empty-kit-item-product').addClass('hidden');
                }
            }

            
            if ($(kit_item).closest('.kit-item').hasClass('selectable')) {
                $(widget_item).addClass('selectable');
                $(widget_product_item).find('.selectable-item-container').removeClass('hidden')
            } else {
                $(widget_item).removeClass('selectable');
                $(widget_product_item).find('.selectable-item-container').addClass('hidden')
            }


            $(widget_product_item).find('input[data-id=input-is-item-is-empty]').val(is_empty_item);

            $(widget_item).removeClass('be-stock-out');
            if($(product).hasClass('be-stock-out')){
                $(widget_item).addClass('be-stock-out');
            }

            
            $(widget_product_item).find('.product-options-list').html('');
            
            var product_form = $(kit_form).find('.be-product-form.be-selected[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']');
            var product_data = $(product_form).find('input[type=\'text\'], input[type=\'radio\']:checked,  input[type=\'checkbox\']:checked, select, textarea');

            for (var i = 0; i < product_data.length; i++) {
                var input_name = $(product_data[i]).attr('name');
                if (is_free_product === "1") {
                    var opt_str = 'kit_items_free[' + item_position + '][' + item_position_free + '][option]';
                } else {
                    var opt_str = 'kit_items[' + item_position + '][option]';
                }
                if (input_name.startsWith(opt_str)) {
                    var option = product_data[i];

                    var option_id = $(product_data[i]).attr('product-option-id');

                    var product_option_value_id = '';

                    var option_type = $(product_data[i]).attr('type');

                    if(option_type==="radio" || option_type==="checkbox"){
                        var product_option_value_id = $(option).val();
                    }

                    if( $(product_data[i]).is( "select" )){
                        var product_option_value_id = $(option).val();
                    }

                    var option_title = ''
                    if (product_option_value_id!=='')
                        option_title = $(options_data_container).find('[product-option-id=' + option_id + '][product-option-value-id=' + product_option_value_id + ']').html();
                    else {
                        var value = $(option).val();

                        if (value !== "") {
                            var option_title_template = $(options_data_container).find('[product-option-id=' + option_id + ']');
                            $(option_title_template).find('.option-value').html(value);
                            option_title = $(option_title_template).html();
                        }
                    }


                    if (option_title !== '') {
                        var option_html = '<div>-<small>' + option_title + '</small></div>';
                        var option_input = '<input type="hidden" name="' + input_name + '" value="' + product_option_value_id + '">';

                        $(widget_product_item).find('.product-options-list').append(option_html);
                    }


                }

            }

            

            
            var attr_table = $(product_form).find('[id^=tab-specification] table');
            if ($(attr_table).length > 0) {
                attr_table = $(attr_table).clone();
                $(attr_table).removeClass('table').removeClass('table-bordered').addClass('widdget-item-attribute-table');
                $(widget_item).find('.attribute-table-container').html(attr_table);
            }

            
            var options_source = $(product_form).find('#options-container').contents();
            var options_target_container = $(widget_product_item).find('.product-options-values');
            bundle_expert_help.copy_options(options_source, options_target_container, 'to_widget')

            bundle_expert.init_time_field_in_widget_item(widget, item_position, item_position_free);

            
            
            var product_details = $(product_form).find('.be-product-details').html();
            if ($(product_details).length > 0) {
                $(widget_item).find('.be-product-details').html(product_details);
            }

            

            $(widget).removeClass("be-kit-has-stock-out");
            if($(widget).find('.kit-item.be-stock-out').length>0){
                $(widget).addClass("be-kit-has-stock-out");
            }

            be_custom.point('013', {'widget_item': widget_item, 'product_form': product_form, 'kit_item':kit_item});


        }

        
        if(bundle_expert.is_widget_checkbox_mode(widget)){
            $(widget).find('.kit-item.free-product').each(function (index, element) {
                var item_position = $(element).attr('data-item-position');
                bundle_expert.free_products_update_input_index(widget, item_position);
            })
        }

        bundle_expert.free_products_update_container_visible(widget);
        

        
        var total_default = $(kit_form).attr('total-default');
        var total_default_new = $(kit_form).attr('total-default-new');
        var total_kit = $(kit_form).attr('total-kit');
        var total_kit_old = $(kit_form).attr('total-kit-old');

        if(total_default_new !== total_default){

            
            
            $(widget).find('.total-default-container .be-price-old.total-default').show();
            $(widget).find('.total-default').show();
        }else{
            
            
            $(widget).find('.total-default-container  .be-price-old.total-default').hide();
            $(widget).find('.total-default').hide();
        }

        if (total_default === total_default_new)
            total_default = '';

        if (total_kit === total_kit_old || total_kit_old === "false")
            total_kit_old = '';

        $(widget).find('.total-default').html(total_default)
        $(widget).find('.total-default-new').html(total_default_new)

        $(widget).find('.total-kit').html(total_kit)
        $(widget).find('.total-kit-old').html(total_kit_old)



        be_custom.point('020', {'widget': widget, 'kit_form': kit_form});

        
        

        
        if ($(widget).hasClass('product-as-kit-mode') || $(widget).hasClass('product-as-kit-mode-light-mode')) {
            bundle_expert.update_product_page_price_html(widget);
            bundle_expert.update_widget_kit_total(widget);
        }


        
        if (free_products_table_mode === 1) {
            if ($(widget).find('#free-item-products-container .free-items .kit-item[data-item-position=' + item_position + ']').length > 0) {
                $(widget).find('#free-item-products-container .empty-list-text[data-item-position=' + item_position + ']').hide();
            } else {
                $(widget).find('#free-item-products-container .empty-list-text[data-item-position=' + item_position + ']').show();
            }
        }

        bundle_expert.init_upload_file_buttons_in_widget(widget);



        be_custom.point('011', {'widget': widget, 'product_form': product});

    }

}

BundleExpertForm.update_product_quantity = function (kit_form, item_position, item_position_free, new_quantity) {
    
    
    var product_form = $(kit_form).find('.kit-form-product-item-conatiner .be-product-form[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + '].active-form');

    $(product_form).find('input.input-quantity-field').val(new_quantity);

    
    
    if (!bundle_expert_form.select_item_products_mode) {
        var kit_item = $(kit_form).find('.bottom-block .kit-form-widget-container .kit-items').find('.kit-item .kit-item-product[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']');
    }else{
        var kit_item = $(kit_form).find('.bottom-block .kit-form-item-products-container').find('.kit-item .kit-item-product[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + '].active');
    }
    

    $(kit_item).find('.item-quantity').html(new_quantity + 'x');
    $(kit_item).find('input[data-id=input-quantity]').val(new_quantity);
    if (new_quantity > 1) {
        $(kit_item).find('.item-quantity').removeClass('hidden');
    } else {
        $(kit_item).find('.item-quantity').addClass('hidden');
    }
}

BundleExpertForm.scroll_to_option_error = function (kit_form, json) {
    var j;
    var i;
    var scroll_complete = false;
    var product_forms = $(kit_form).find('.kit-form-product-container .kit-form-product-item-conatiner .be-product-form.be-selected');

    for (j in json['error']) {
        if (json['error'][j]['option']) {
            
            
            var item_position = json['error'][j]['item_position'];
            var item_position_free = json['error'][j]['item_position_free'];

            var product_form = $(kit_form).find('.kit-form-product-container .kit-form-product-item-conatiner .be-product-form.be-selected[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + ']');
            ;


            

            var active_item = $(kit_form).find('.kit-form-widget-container .kit-item[data-item-position=' + item_position + '][data-item-position-free=' + item_position_free + '] .be-product-thumb').first();

            var option_element = '';

            for (var k = 0; k < json['error'][j]['option'].length; k++) {
                var error_option = json['error'][j]['option'][k];

                for (i in error_option) {
                    option_element = $(product_form).find('#input-option' + i.replace('_', '-')).parent();

                    if (option_element !== '') {
                        bundle_expert_form.set_active_product(active_item);

                        
                        
                        var screen_width = Math.round($('html').width());
                        ;
                        if (screen_width >= 768)
                            var right_mode = true;
                        else
                            var right_mode = false;

                        if (right_mode) {
                            var scroll_position_option = $(option_element).position().top;
                            var elem = $(option_element).closest('#be-product');
                            if($(elem).length>0){
                                var scroll_position_parent = $(elem).position().top;

                                var scroll_position = scroll_position_option - scroll_position_parent + 140;

                                $(kit_form).find('[id^=tab-main]').animate({
                                    scrollTop: scroll_position
                                }, 1000);
                            }
                        } else {
                            var scroll_position_option = $(option_element).position().top;
                            var scroll_position_parent = $(option_element).closest('#be-product').position().top;

                            var scroll_position = scroll_position_option + scroll_position_parent + 40;

                            
                            $(kit_form).find('.be-product-form #content').animate({
                                scrollTop: scroll_position
                            }, 1000);
                        }

                        scroll_complete = true;

                    }

                    if (scroll_complete)
                        break;

                }

                if (scroll_complete)
                    break;
            }

            if (scroll_complete)
                break;

        }


    }
}


BundleExpertForm.on_click_next_button = function (button) {
    var item_position = $(button).attr('data-item-position');

    var items_container = $(button).closest('.kit-form-widget-main-container');

    var visible_items_count = $(items_container).find('.owl-item.active').length;
    
    
    

    var last_visible_item = $(items_container).find('.owl-item.active').last();
    var last_visible_item_index = $(items_container).find('.owl-item').index(last_visible_item);

    var new_active_item_index = last_visible_item_index + 1;

    if (new_active_item_index > $(items_container).find('.owl-item').length) {
        new_active_item_index = $(items_container).find('.owl-item').length;
    }

    var new_active_item = $(items_container).find('.owl-item')[new_active_item_index];

    new_active_item = $(new_active_item).find('.kit-item .kit-item-product');

    if ($(new_active_item).length == 0) {
        $(items_container).find('#more-button').click();
        return;
    } else {
        this.set_active_product(new_active_item);
    }

}
BundleExpertForm.on_click_next_button_select_mode = function (button) {

    var item_position = $(button).attr('data-item-position');

    var items_container = $(button).closest('.kit-form-item-products-main-container').find('.kit-form-item-products[data-item-position='+item_position+']');

    var visible_items_count = $(items_container).find('.owl-item.active').length;

    var last_visible_item = $(items_container).find('.owl-item.active').last();
    var last_visible_item_index = $(items_container).find('.owl-item').index(last_visible_item);

    var new_active_item_index = last_visible_item_index + 1;

    if (new_active_item_index > $(items_container).find('.owl-item').length) {
        new_active_item_index = $(items_container).find('.owl-item').length;
    }

    var new_active_item = $(items_container).find('.owl-item')[new_active_item_index];

    new_active_item = $(new_active_item).find('.kit-item .kit-item-product');

    if ($(new_active_item).length == 0) {
        $(items_container).find('#more-button').click();
        return;
    } else {
        this.set_active_product(new_active_item);
    }


}


BundleExpertForm.on_click_prev_button = function (button) {

    
    var item_position = $(button).attr('data-item-position');

    var items_container = $(button).closest('.kit-form-widget-main-container');

    var visible_items_count = $(items_container).find('.owl-item.active').length;

    var first_visible_item = $(items_container).find('.owl-item.active').first();
    var first_visible_item_index = $(items_container).find('.owl-item').index(first_visible_item);

    var new_active_item_index = first_visible_item_index - visible_items_count;

    if (new_active_item_index < 0) {
        new_active_item_index = 0;
    }

    var new_active_item = $(items_container).find('.owl-item')[new_active_item_index];
    new_active_item = $(new_active_item).find('.kit-item .kit-item-product');
    if ($(new_active_item).length > 0) {
        this.set_active_product(new_active_item);
    }
}

BundleExpertForm.on_click_prev_button_select_mode = function (button) {
    
    var item_position = $(button).attr('data-item-position');

    var items_container = $(button).closest('.kit-form-item-products-main-container').find('.kit-form-item-products[data-item-position='+item_position+']');


    var visible_items_count = $(items_container).find('.owl-item.active').length;

    var first_visible_item = $(items_container).find('.owl-item.active').first();
    var first_visible_item_index = $(items_container).find('.owl-item').index(first_visible_item);

    var new_active_item_index = first_visible_item_index - visible_items_count;

    if (new_active_item_index < 0) {
        new_active_item_index = 0;
    }

    var new_active_item = $(items_container).find('.owl-item')[new_active_item_index];
    new_active_item = $(new_active_item).find('.kit-item .kit-item-product');
    if ($(new_active_item).length > 0) {
        this.set_active_product(new_active_item);
    }



}


var BundleExpertHelp = {};

BundleExpertHelp.constructor = function () {
    if (!String.prototype.startsWith) {
        String.prototype.startsWith = function(searchString, position) {
            position = position || 0;
            return this.indexOf(searchString, position) === position;
        };
    }
}



BundleExpertHelp.load_owl_carousel = function (source) {

    var head  = document.getElementsByTagName('head')[0];
    var link  = document.createElement('link');
    link.id   = 'be_owl_style_1';
    link.rel  = 'stylesheet';
    link.type = 'text/css';
    link.href = 'catalog/view/javascript/bundle-expert/owl.carousel.min.css';
    link.media = 'all';
    head.appendChild(link);
    
    var link  = document.createElement('link');
    link.id   = 'be_owl_style_2';
    link.rel  = 'stylesheet';
    link.type = 'text/css';
    link.href = 'catalog/view/javascript/bundle-expert/owl.theme.default.min.css';
    link.media = 'all';
    head.appendChild(link);

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    
    
    
    
    
    
    

    jQuery.ajax({
        url: 'catalog/view/javascript/bundle-expert/owl.carousel.js',
        dataType: 'script',
        success: function () {
            if(source=="kit_form"){

                bundle_expert_form.show_kit_form();

            }else{
                bundle_expert_form.show_kit_form_from_cart();
            }

        },
        async: true
    });

}
BundleExpertHelp.get_owl_carousel_status = function () {

    var active = 1;
    
    
    
    
    
    
    

    if ($.fn.owlCarouselBundleExpert) {
        
        active = 1;
    }else{
        active = 0;
    }

    return active;
}

BundleExpertHelp.get_product_page_data_for_send = function (prefix) {

    if(typeof prefix==="undefined"){
        var prefix = 'product_as_kit_data';
    }
    
    var product_data = $(bundle_expert.selectors.product_page.product_data).find('input[type=\'text\'], input[type=\'number\'], input[type=\'hidden\'], input[type=\'radio\']:checked, input[type=\'checkbox\']:checked, select, textarea');

    var product_data_serialized = $(product_data).serializeArray();

    
    $(product_data_serialized).each(function (index, value) {
        var name = $(value).attr('name');

        var n = name.indexOf("[");
        if (n >= 0)
            name = "[" + name.substring(0, n) + "]" + name.substring(n);
        else
            name = "[" + name + "]";

        
        var new_name = prefix + name;

        $(value).attr('name', new_name);

    });

    return product_data_serialized;
}



BundleExpertHelp.get_product_page_data_for_send_le = function () {

    var product_data = $(bundle_expert.selectors.product_page.product_data).find('input[type=\'text\'], input[type=\'number\'], input[type=\'hidden\'], input[type=\'radio\']:checked, input[type=\'checkbox\']:checked, select, textarea');

    var product_data_serialized = $(product_data).serializeArray();


    $(product_data_serialized).each(function (index, value) {
        var name = $(value).attr('name');

        var n = name.indexOf("[");
        if (n >= 0)
            name = "[" + name.substring(0, n) + "]" + name.substring(n);
        else
            name = "[" + name + "]";

        var new_name = 'product_as_kit_light_mode' + name;

        $(value).attr('name', new_name);

    });

    return product_data_serialized;
}


BundleExpertHelp.on_slideshow_change_item = function (event) {


    var element   = event.target;         
    if($(element).hasClass('bundle-expert-slideshow')){
        var name      = event.type;           
        var namespace = event.namespace;      
        var items     = event.item.count;     
        var item_index      = event.item.index;     
        
        var pages     = event.page.count;     
        var page      = event.page.index;     
        var size      = event.page.size;      

        var active_item = $(element).children('.owl-stage-outer').children('.owl-stage').children('.owl-item')[item_index];

        
        var height = $(active_item).height();

        var yy =  $(element).children('.owl-nav').find('button');
        
        $(element).children('.owl-nav').find('button').css({ 'height': height + "px" });

        var widget = $(active_item).find('.kit-widget');

        be_custom.point('044', {'widget': widget});
    }



}

BundleExpertHelp.change_slideshow_side_buttons_height = function (widget) {
    var height = $(widget).height();

    var slideshow = $(widget).closest('.bundle-expert-slideshow');

    if($(slideshow).length>0){
        var yy =  $(slideshow).children('.owl-nav').find('button');
        
        $(slideshow).children('.owl-nav').find('button').css({ 'height': height + "px" });
    }


}

BundleExpertHelp.normalize_option_name = function (name) {

    if (typeof name !== 'undefined') {
        name = name.replace('kit_items_w', 'kit_items');
        name = name.replace('kit_items_free_w', 'kit_items_free');
    }
    return name;
}
BundleExpertHelp.normalize_option_name_to_widget = function (name) {

    if (typeof name !== 'undefined') {
        if (name.indexOf('kit_items_free') >= 0) {
            name = name.replace('kit_items_free', 'kit_items_free_w');
        } else {
            name = name.replace('kit_items', 'kit_items_w');
        }

    }
    return name;
}


BundleExpertHelp.normalize_options_names = function (data) {

    
    $(data).each(function (index, element) {
        var name = $(element).attr('name');
        if (typeof name !== 'undefined') {
            name = name.replace('kit_items_w', 'kit_items');
            name = name.replace('kit_items_free_w', 'kit_items_free');
            $(element).attr('name', name);
        }
    })

    return data;
}


BundleExpertHelp.normalize_options_names_2 = function (data) {

    $(data).find('input, select, textarea').each(function (index, element) {
        
        
        var name = $(element).attr('name');
        if (typeof name !== 'undefined') {
            name = name.replace('kit_items_w', 'kit_items');
            name = name.replace('kit_items_free_w', 'kit_items_free');
            $(element).attr('name', name);
        }
    })

    return data;
}

BundleExpertHelp.normalize_options_names_to_widget = function (data) {

    $(data).find('input, select, textarea').each(function (index, element) {
        var name = $(element).attr('name');
        if (typeof name !== 'undefined') {

            if (name.indexOf('kit_items_free') >= 0) {
                name = name.replace('kit_items_free', 'kit_items_free_w');
            } else {
                name = name.replace('kit_items', 'kit_items_w');
            }

            $(element).attr('name', name);
        }

    })

    return data;
}

BundleExpertHelp.is_kit_form_for_product_as_kit = function (kit_form) {
    var is_product_as_kit = false;

    var widget_unique_id = $(kit_form).closest('.body-content').attr('data-widget-unique-id');
    var widget = $('.kit-widget[data-widget-unique-id=' + widget_unique_id + ']');
    if ($(widget).hasClass('product-as-kit-mode')) {
        is_product_as_kit = true;
    }

    return is_product_as_kit;
}

BundleExpertHelp.copy_options = function (options_source, options_target_container, direction) {

    if (direction === 'to_widget') {
        var options_source_clone = $(options_source).clone();
        bundle_expert_form.fix_clone_select(options_source, options_source_clone);
        options_source_clone = bundle_expert_help.normalize_options_names_to_widget(options_source_clone);
        $(options_target_container).html('');
        $(options_target_container).append(options_source_clone);
    } else if (direction === 'to_form') {
    }

    $(options_source).find('input[type=\'text\'],  input[type=\'hidden\'],  input[type=\'radio\']:checked,  input[type=\'checkbox\'], select,  textarea').each(function (index, element) {
        var input_name = $(element).attr('name');

        if (direction === 'to_form') {
            input_name = bundle_expert_help.normalize_option_name(input_name);
        } else if (direction === 'to_widget') {
            input_name = bundle_expert_help.normalize_option_name_to_widget(input_name);
        }

        var input_val = $(element).val();
        var target_element = '';
        if ($(element).attr('type') === 'radio') {
            var checked = $(element).prop('checked');
            
            target_element = $(options_target_container).find('[name="' + input_name + '"][value=' + input_val + ']');
            $(target_element).prop('checked', checked);
        } else if ($(element).attr('type') === 'checkbox') {
            var checked = $(element).prop('checked');
            
            target_element = $(options_target_container).find('[name="' + input_name + '"][value=' + input_val + ']');
            $(target_element).prop('checked', checked);
        } else if ($(element).is('select')) {
            var value = $(element).val();
            
            target_element = $(options_target_container).find('[name="' + input_name + '"]');
            $(target_element).val(value);
        } else if ($(element).is('textarea')) {
            var value = $(element).val();
            
            target_element = $(options_target_container).find('[name="' + input_name + '"]');
            $(target_element).val(value);
        } else {
            var value = $(element).val();
            
            target_element = $(options_target_container).find('[name="' + input_name + '"]').val(value);
            $(target_element).val(value);
        }

    });

}
BundleExpertHelp.uniqid = function () {
    return '' + Math.random().toString(36).substr(2, 9);
};

BundleExpertHelp.sameOptionsRemovePrefix = function (container) {

    

    
    

    $(container).find('input[type=\'text\'],  input[type=\'hidden\'],  input[type=\'radio\'],  input[type=\'checkbox\'],  select,  textarea, .kit-data input[type=\'hidden\']').filter("[name*='option']").each(function (index, value) {
        var name = $(value).attr('name');

        
        if (name.indexOf("option") >= 0) {
            var attr = $(value).attr('data-id');
            if (typeof attr !== typeof undefined && attr !== false) {
                var id = $(value).attr('data-id');
                name = name.replace('' + id + '_', '');

                
                value.name = name;
            }
        }



    });


};
BundleExpertHelp.sameOptionsAddPrefix = function (container) {

    
    
    $(container).find('input[type=\'text\'],  input[type=\'hidden\'],  input[type=\'radio\'],  input[type=\'checkbox\'],  select,  textarea, .kit-data input[type=\'hidden\']').filter("[name*='option']").each(function (index, value) {
        var name = $(value).attr('name');

        
        if (name.indexOf("option") >= 0) {
            var attr = $(value).attr('data-id');
            if (typeof attr !== typeof undefined && attr !== false) {
                var id = $(value).attr('data-id');
                name = '' + id + '_' + name;

                
                value.name = name;

            }

        }


    });

    
};

BundleExpertHelp.sameOptionsPrefixCheck = function (widget) {
    var attr = $(widget).attr('data-has-options-prefix');


    if (typeof attr !== typeof undefined && attr !== false) {
        return true;
    }else{
        return false;
    }
};

BundleExpertHelp.isInViewport = function (elem) {
    if( elem.length == 0 ) {
        return;
    }
    var $window = jQuery(window)
    var viewport_top = $window.scrollTop()
    var viewport_height = $window.height()
    var viewport_bottom = viewport_top + viewport_height

    var vis = $(elem).is(":visible");
    if (!vis)
        $(elem).show();  





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

    if (bundle_expert.currency.symbol_left!=='') {
        string += bundle_expert.currency.symbol_left;
    }

    string += be_animate_price.formatMoney(price, bundle_expert.currency.decimal_place, bundle_expert.currency.decimal_point, bundle_expert.currency.thousand_point);;

    if (bundle_expert.currency.symbol_right!=='') {
        string += bundle_expert.currency.symbol_right;
    }

    return string;
}

BundleExpertAnimatePrice.formatMoney = function (number, decPlaces, decSep, thouSep) {
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSep = typeof decSep === "undefined" ? "." : decSep;
    thouSep = typeof thouSep === "undefined" ? "," : thouSep;
    var sign = number < 0 ? "-" : "";
    var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
    
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
    
    $('.product-price-container .product-as-kit-price').html(be_animate_price.format_price(be_animate_price.price_start));
    if (be_animate_price.price_start !== be_animate_price.price_stop) {
        be_animate_price.price_timer_id = setTimeout(be_animate_price.animatePrice_callback, be_animate_price.animate_delay);
    }
}
BundleExpertAnimatePrice.animatePrice = function (price) {
    var quantity = Number($(bundle_expert.selectors['product_page']['quantity']).val());
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
    
    $('.product-price-container .product-as-kit-old-price').html(be_animate_price.format_price(be_animate_price.old_price_start));
    if (be_animate_price.old_price_start !== be_animate_price.old_price_stop) {
        be_animate_price.old_timer_id = setTimeout(be_animate_price.animateOldPrice_callback, be_animate_price.animate_delay);
    }
}

BundleExpertAnimatePrice.animateOldPrice = function (price) {
    var quantity = Number($(bundle_expert.selectors['product_page']['quantity']).val());
    be_animate_price.old_price_start = be_animate_price.old_price_stop;
    be_animate_price.old_price_stop = parseFloat(price) * quantity;
    be_animate_price.old_step = (be_animate_price.old_price_stop - be_animate_price.old_price_start) / 10;
    clearTimeout(be_animate_price.old_timer_id);
    be_animate_price.old_timer_id = setTimeout(be_animate_price.animateOldPrice_callback, be_animate_price.animate_delay);
}

var bundle_expert = '';
var bundle_expert_form = '';
var bundle_expert_help = '';
var be_animate_price = '';
var be_custom = '';

var BundleExpertCustomDefault = {};

BundleExpertCustomDefault.constructor = function (widgets) {
}
BundleExpertCustomDefault.point = function (v1, v2) {
}


var BundleExpertMainInit = {};

BundleExpertMainInit.constructor = function () {
}

BundleExpertMainInit.init = function () {

    BundleExpert.constructor();
    bundle_expert = BundleExpert;

    BundleExpertForm.constructor();
    bundle_expert_form = BundleExpertForm;

    BundleExpertHelp.constructor();
    bundle_expert_help = BundleExpertHelp;

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

    be_custom_header_cart = '';
    if (typeof BundleExpertCustomHeaderCart === 'function') {
        be_custom_header_cart = new BundleExpertCustomHeaderCart();
    } else {
        if (typeof BundleExpertCustomHeaderCart != "undefined") {
            BundleExpertCustomHeaderCart.constructor();
            be_custom_header_cart = BundleExpertCustomHeaderCart;
        }
    }

    if ($('#bundle-expert-container #bundle-expert-class-data').length > 0) {
        bundle_expert.reload_item_products = parseInt($('#becd-reload_item_products').html());
        bundle_expert.is_some_checkout_page = $('#becd-is_some_checkout_page').html() === 'true';
        bundle_expert.disable_cart_kit_edit_button = $('#becd-disable_cart_kit_edit_button').html() === 'true';
        bundle_expert.animate_price = $('#becd-animate_price').html() === 'true';
        bundle_expert.button_remove_kit = $('#becd-button_remove_kit').html();
        bundle_expert.button_edit_kit = $('#becd-button_edit_kit').html();
        
        bundle_expert.control_change_quantity_by_stock = $('#becd-control-change-quantity-by-stock').html() === 'true';
        

        var widgets_json = $('#bundle-expert-container #widgets-json-container').html();
        var widgets = JSON.parse(widgets_json);

        var selectors_json = $('#bundle-expert-container #selectors-json-container').html();
        var selectors = JSON.parse(selectors_json);

        bundle_expert.selectors = selectors;

        var currency_json = $('#bundle-expert-container #currency-json-container').html();
        var currency = JSON.parse(currency_json);

        var category_products_json = $('#bundle-expert-container #category-products-json-container').html();
        var category_products = JSON.parse(category_products_json);
        bundle_expert.category_products = category_products;


        bundle_expert.currency = currency;

        be_custom.point('024', {});

        bundle_expert.add_widgets(widgets);
    }


    be_custom.point('025', {});

    bundle_expert.init_bundle_expert();


    bundle_expert.init_lazy_sliders();

    $(window).on('resize scroll', function () {
        bundle_expert.init_lazy_sliders();
    });

    
    
    if(bundle_expert.window_scroll_selector!==''){
        $(bundle_expert.window_scroll_selector).on('resize scroll', function () {
            bundle_expert.init_lazy_sliders();
        });
    }

}

BundleExpertMainInit.init_check = function () {
    if(bundle_expert==''){
        BundleExpertMainInit.init();
    }
}

BundleExpertMainInit.init_cart_kit_edit_button = function () {

    
    var disable_cart_kit_edit_button = $('#becd-disable_cart_kit_edit_button').html() === 'true'
    
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
        
        if ($(element).closest('#tab-total').length > 0) {
            $(element).find('i').remove();
            var str = $(element).html();
            var parent = $(element).parent();
            $(parent).find(element).remove();
            $(parent).append(str);
        }


        
        if (!$(element).hasClass('button-init-complete')) {

            var kit_unique_id = $(element).attr('uid');
            var item_position = $(element).attr('pos');
            var item_position_free = $(element).attr('pos-free');
            $(element).attr('kit-unique-id', kit_unique_id);
            $(element).attr('data-item-position', item_position);
            $(element).attr('data-item-position-free', item_position_free);
            $(element).addClass('button-init-complete');
            $(element).addClass('edit-cart-kit-button');


            
            if (kit_unique_id_prev !== kit_unique_id) {
                $(element).find('b').replaceWith('<div><span class="edit-kit-button"><i class="fa fa-pencil" style="display:none"></i>' + $('#becd-button_edit_kit').html() + '</span><span class="remove-kit-button"><i class="fa fa-remove" style="display:none"></i>' + $('#becd-button_remove_kit').html() + '</span></div>')
                $(element).find('i').show();
            } else {
                $(element).find('b').replaceWith('');
            }

            $(element).find('.edit-kit-button').attr('onclick', "BundleExpertMainInit.init_check();bundle_expert.show_kit_form_from_cart($(this).closest('.edit-cart-kit-button'));");
            $(element).find('.remove-kit-button').attr('onclick', "BundleExpertMainInit.init_check();bundle_expert.show_remove_kit_from_cart_question(this)");

            
            
            
            
            
            
            

            
            var parent = $(element).closest('a');
            if ($(parent).length > 0)
                $(parent).after(element);

            

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




