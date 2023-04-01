/*********** Function viewport ***********/
function viewport() {
	var e = window,
		a = 'inner';
	if (!('innerWidth' in window)) {
		a = 'client';
		e = document.documentElement || document.body;
	}
	return {
		width: e[a + 'Width'],
		height: e[a + 'Height']
	};
}

/*********** Function preloader ***********/

function masked(element, status) {
  if (status == true) {
    $('<div/>').attr({ 'class':'masked' }).prependTo(element);
    $('<div class="masked_loading" />').insertAfter($('.masked'));
  } else {
    $('.masked').remove();
    $('.masked_loading').remove();
  }
}

/*********** Function Show Location Map ***********/

function octShowMap(content, selector) {

	var octMap = $(selector);

	if (octMap.hasClass('not_in')) {
		octMap.html(content);
		octMap.removeClass('not_in');
	}

}

/*********** Function popups ***********/

function octPopupCallPhone() {
	masked('body', true);
	$( ".modal-backdrop" ).remove();
	$.ajax({
        type: 'post',
        dataType: 'html',
        url: 'index.php?route=octemplates/module/oct_popup_call_phone',
        	cache: false,
        success: function(data) {
	        masked('body', false);
            $(".modal-holder").html(data);
            $("#us-callback-modal").modal("show");
        }
    });
}

function octPopupCart() {
	masked('body', true);
	$( ".modal-backdrop" ).remove();
	$.ajax({
        type: 'get',
        dataType: 'html',
        url: 'index.php?route=octemplates/module/oct_popup_cart',
        	cache: false,
        success: function(data) {
	        masked('body', false);
            $(".modal-holder").html(data);
            $("#us-cart-modal").modal("show");
        }
    });
}

function octPopupSubscribe() {
	if($(".modal-backdrop").length > 0) {
	    return;
	}
	masked('body', true);
	$( ".modal-backdrop" ).remove();
	$.ajax({
        type: 'post',
        dataType: 'html',
        url: 'index.php?route=octemplates/module/oct_subscribe',
        	cache: false,
        success: function(data) {
	        masked('body', false);
            $(".modal-holder").html(data);
            $("#us-subscribe-modal").modal("show");
        }
    });
}

function octPopupFoundCheaper(product_id) {
	masked('body', true);
	$( ".modal-backdrop" ).remove();
	$.ajax({
        type: 'post',
        dataType: 'html',
        url: 'index.php?route=octemplates/module/oct_popup_found_cheaper',
        data: 'product_id=' + product_id,
        	cache: false,
        success: function(data) {
	        masked('body', false);
            $(".modal-holder").html(data);
            $("#us-cheaper-modal").modal("show");
        }
    });
}

function octPopupLogin() {
	masked('body', true);
	$( ".modal-backdrop" ).remove();
    $.ajax({
        type: "post",
        url: 'index.php?route=octemplates/module/oct_popup_login',
        data: $(this).serialize(),
        	cache: false,
        success: function(data) {
	        masked('body', false);
            $(".modal-holder").html(data);
            $("#loginModal").modal("show");
        }
    });
}

function octPopUpView(product_id) {
	masked('body', true);
	$( ".modal-backdrop" ).remove();
	$.ajax({
        type: 'post',
        dataType: 'html',
        url: 'index.php?route=octemplates/module/oct_popup_view',
        data: 'product_id=' + product_id,
        	cache: false,
        success: function(data) {
	        masked('body', false);
            $(".modal-holder").html(data);
            $("#us-quickview-modal").modal("show");
        }
    });
}

function octPopPurchase(product_id) {
	masked('body', true);
	$( ".modal-backdrop" ).remove();
	$.ajax({
        type: 'post',
        dataType: 'html',
        url: 'index.php?route=octemplates/module/oct_popup_purchase',
        data: 'product_id=' + product_id,
        	cache: false,
        success: function(data) {
	        masked('body', false);
            $(".modal-holder").html(data);
            $("#us-one-click-modal").modal("show");
        }
    });
}

/*********** Button column ***********/

function octShowColumnProducts(octButtonPrevID, octButtonNextID, octModuleID) {
	const buttonPrevID = octButtonPrevID;
	const buttonNextID = octButtonNextID;
	const moduleID = octModuleID + ' > .us-item';
	$("#" + moduleID).slice(0, 1).show();
	$("#" + octButtonNextID).click(function () {
		const visibleProduct = $("#" + moduleID + ":visible");
		const NextProduct = visibleProduct.next();
		if (NextProduct.length > 0) {
			visibleProduct.css('display', 'none');
			NextProduct.fadeIn("slow");
		} else {
			visibleProduct.css('display', 'none');
			$("#" + moduleID + ":hidden:first").fadeIn('slow');
		}
	});
	$("#" + buttonPrevID).click(function () {
		const visibleProduct = $("#" + moduleID + ":visible");
		const NextProduct = visibleProduct.prev();
		if (NextProduct.length > 0) {
			visibleProduct.css('display', 'none');
			NextProduct.fadeIn("slow");
		} else {
			visibleProduct.css('display', 'none');
			$("#" + moduleID + ":hidden:last").fadeIn('slow');
		}
	});
}

function getOCTCookie(name) {
	var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));

	return matches ? decodeURIComponent(matches[1]) : 'undefined';
}

/*********** Animate scroll to element function ***********/

function scrollToElement(hrefTo) {
	const top = $(hrefTo).offset().top-50;
	$('body,html').animate({scrollTop: top}, 1000);
}

/*********** Notify function ***********/

function usNotify(type, text) {
	var iconType = 'info';
	switch (type) {
	  case 'success':
	    iconType = 'fas fa-check';
	    break;
	  case 'danger':
	    iconType = 'fas fa-times';
	    break;
	  case 'warning':
	    iconType = 'fas fa-exclamation';
	    break;
	}
	$.notify({
		message: text,
		icon: iconType
	},{
		type: type
	});
}

/*********** Function Show More Product in modules ***********/
function octShowMoreModule(module_id, lozadEnable = 0, button_id, html_module_id, page_id, path = 0) {
    let page = parseInt(document.querySelector("#" + page_id).value) + 1;

    $.ajax({
        type: 'post',
        dataType: 'html',
        url: 'index.php?route=extension/module/oct_product_modules/modulePage',
        data: 'module_id=' + module_id + '&page=' + page + '&oct_path=' + path,
        cache: false,
        beforeSend: function() {
            // enable loader
            masked('body', true);

            document.querySelector('#' + button_id).classList.add('oct-animated');
        },
        complete: function() {
            // check lozad
            if (lozadEnable == 1) {
                lozad('.oct-lazy').observe();
            }

            // remove loader
            masked('body', false);

            document.querySelector('#' + button_id).classList.remove('oct-animated');
        },
        success: function(octContent) {
            // parce octProducts
            let wrapper = document.createElement('div');
            wrapper.innerHTML = octContent;

            // find and write new products
            let productsToFind = wrapper.querySelectorAll('.us-item');
            const productsArray = Array.from(productsToFind);
            let newProducts = document.querySelector("#" + html_module_id);
            productsArray.forEach(element => newProducts.insertAdjacentElement('beforeend', element));

            // check and write new button
            let buttonToFind = wrapper.querySelectorAll("#" + button_id);
            const buttonArray = Array.from(buttonToFind);

            document.querySelector("#" + page_id).value = page;
			// remove old button
			let checkButton = wrapper.querySelector(".oct-load-more-button-wrapper");

			if (checkButton === null) {
				document.querySelector('#' + html_module_id).nextElementSibling.classList.add('d-none');
			}
        }
    });
}

/*********** Mask function ***********/
function usInputMask(selector, mask) {
	$(selector).inputmask({'mask': mask});
}

$(document).ready(function() {
	/*********** Desktop Dropdowns ***********/
	$('body').on('click', '.us-dropdown-box', function() {
		$(this).addClass('active');
		$('#us_overlay').addClass('active');
	});
	/*********** End of Desktop Dropdowns ***********/
	/*********** Header locations ***********/
    $('body').on('click', '.us-header-location', function() {
        $('.us-header-location').not(this).removeClass('active');
        $(this).addClass('active');
    });

	$('body').on('click', '.us-plus', function(){
        const oldVal = $(this).prev().val();
        var newVal = (parseInt($(this).prev().val(),10) +1);
		$(this).prev().val(newVal);
    });

	$('body').on('click', '.us-minus', function(){
        const oldVal = $(this).next().val();
        const minimum = $(this).parent().find('.min-qty').val();
        if (oldVal > 1) {
            var newVal = (parseInt($(this).next().val(),10) -1);
        } else {
	        newVal = 1;
        }
        if (newVal < minimum) {
	        newVal = minimum;
        }
        $(this).next().val(newVal);
    });

    $('body').on('click', '.us-cat-button-cart', function(){
        const productID = $(this).prev().find('input[name="product_id"]').val();
        const quantity = $(this).prev().find('.form-control').val();
        cart.add(productID, quantity);
    });

	function viewport() {
		var e = window,
			a = 'inner';
		if (!('innerWidth' in window)) {
			a = 'client';
			e = document.documentElement || document.body;
		}
		return {
			width: e[a + 'Width'],
			height: e[a + 'Height']
		};
	}

	/*********** To top button ***********/

	$("#back-top").hide(),
	$(function() {
	    $(window).scroll(function() {
	        $(this).scrollTop() > 450 ? $("#back-top").fadeIn() : $("#back-top").fadeOut()
	    }),
	    $("#back-top a").click(function() {
	        return $("body,html").animate({
	            scrollTop: 0
	        }, 800),
	        !1
	    })
	});

	/*********** Fixed contacts ***********/

	$('#us_fixed_contact_button').on('click', function () {
		$(this).toggleClass('clicked');
		$('.us-fixed-contact-dropdown').toggleClass('expanded');
		$('.us-fixed-contact-icon .fa-comment-dots').toggleClass('d-none');
		$('.us-fixed-contact-icon .fa-times').toggleClass('d-none');
		$('#us_fixed_contact_substrate').toggleClass('active');
	});

	$('#us_fixed_contact_substrate').on('click', function () {
		$(this).removeClass('active');
		$('.us-fixed-contact-dropdown').removeClass('expanded');
		$('.us-fixed-contact-icon .fa-comment-dots').removeClass('d-none');
		$('.us-fixed-contact-icon .fa-times').toggleClass('d-none');
		$('#us_fixed_contact_button').removeClass('clicked');
	});

	$('.shedule-dropdown-menu, .us-fixed-contact-dropdown').click(function(e){
		e.stopPropagation();
	});

	$('#us_overlay').on('click', function() {
		$(this).removeClass('active');
		$('.ocf-offcanvas, .us-dropdown-box').removeClass('active');
		$('body').removeClass('modal-open');
		clearLiveSearch();
	});

	/*********** Category column module ***********/

	$('.us-categories-toggle').on('click', function () {
		if ($(this).hasClass('clicked') || $(this).parent().parent().hasClass('active')) {
			$(this).parent().parent().removeClass('active');
			$(this).parent().next().removeClass('expanded');
			$(this).removeClass('clicked');
		} else {
			$(this).toggleClass('clicked');
			$(this).parent().next().toggleClass('expanded');
		}
	});

	$('.us-product-nav-item').on('click', function () {
		$('.us-product-nav-item').removeClass('us-product-nav-item-active');
		$(this).addClass('us-product-nav-item-active');
	});

	/*********** Mobile scprits ***********/

	var screenWidth = viewport().width;

	var resizeId;

	function makeResize() {
		// for ajax menu
		//$('#us_menu_mobile_content').append($('#oct-menu-ul'));
		$('#us_info_mobile .nav-dropdown-menu-content').prepend($('#currency'));
		$('#us_info_mobile .nav-dropdown-menu-content').prepend($('#language'));
	}

	function unResize() {
		// for ajax menu
		//$('#oct-menu-dropdown-menu').append($('#oct-menu-ul'));
		$('#top-links').prepend($('#currency'));
		$('#top-links').prepend($('#language'));
	}

	window.addEventListener('resize', function() {
	    clearTimeout(resizeId);
	    resizeId = setTimeout(doneResizing, 500);
	});

	function doneResizing(){
		var screenWidth = viewport().width;
	    if (screenWidth > 767 && screenWidth < 1024) {
			makeResize();
		}
		if (screenWidth > 1023) {
			unResize();
		}
		if (screenWidth < 768) {
			$('.us-footer-contact-box').append($('.us-footer-social'));
		} else {
			$('.us-footer-shedule-box').append($('.us-footer-social'));
		}
	}

	function mobileMenu() {
		var screenWidth = viewport().width;

		if (screenWidth < 992) {

			$('#us_menu_mobile_close').on('click', function () {
				$('#us_menu_mobile_box').removeClass('expanded');
				// $('body').removeClass('no-scroll');
				setTimeout(() => {
					$('.oct-menu-child-ul').removeClass('opened');
				}, 600);
			});

			$('.oct-menu-toggle').on('click', function () {
				$(this).parent().next().addClass('opened');
			});

			$('.oct-menu-back').on('click', function () {
				$(this).parent().removeClass('opened');
			});
		} else {
			$('.oct-menu-li').hover(function () {
				$(this).children('.oct-menu-child-ul').addClass('oct-menu-child-ul-active');
			});

			$('.oct-menu-li').on('mouseleave', function () {
				$(this).children('.oct-menu-child-ul').removeClass('oct-menu-child-ul-active');
			});

			$('.oct-mm-link').hover(function () {
				$(this).children('.oct-mm-dropdown').addClass('oct-mm-dropdown-active');
			});

			$('.oct-mm-link').on('mouseleave', function () {
				$(this).children('.oct-mm-dropdown').removeClass('oct-mm-dropdown-active');
			});
			unResize();
		}
	}

	/*
	for ajax menu
	$('#us_menu_mobile_button').on('click', function () {
		$('#us_menu_mobile_box').addClass('expanded');
		$('body').addClass('no-scroll');
	});
	*/

	$('#us_menu_mobile_button').on('click', function () {

		const $menuId = $('#us_menu_mobile_content').html().length == 0;

	    if (!$menuId) {

        	$('#us_menu_mobile_box').addClass('expanded');
			// $('body').addClass('no-scroll');

	    } else {
			masked('body', true);
			$.ajax({
		        type: 'post',
		        dataType: 'html',
		        url: 'index.php?route=octemplates/module/oct_megamenu',
		        data: 'mobile=1',
		        	cache: false,
		        success: function(data) {
			        $("#us_menu_mobile_content").html(data);
			        $('#us_menu_mobile_box').addClass('expanded');
					// $('body').addClass('no-scroll');

					mobileMenu();
					masked('body', false);
		        }
		    });
		}
	});

	// Info mobile

	$('body').on('click', '#dropdown_menu_info', function() {
		$('#us_info_mobile').addClass('expanded');
		// $('body').addClass('no-scroll');
	});
	$('body').on('click', '#us_info_mobile_close', function(e) {
		$('#us_info_mobile').removeClass('expanded');
		// $('body').removeClass('no-scroll');
		e.stopPropagation();
	});

	if (screenWidth < 992) {
		makeResize();

		$('body').on('click', '.us-footer-title', function() {
			$(this).next().slideToggle();
			$(this).toggleClass('open');
		});

	} else {
		unResize();
	}

	if (screenWidth < 768) {
		$('.us-footer-contact-box').append($('.us-footer-social'));
	} else {
		$('.us-footer-shedule-box').append($('.us-footer-social'));
	}

});
