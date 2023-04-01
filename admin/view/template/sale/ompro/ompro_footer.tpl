<footer class="main-footer">
  <div class="pull-right hidden-xs">
    <b>Version</b>
    <?php echo $version; ?>
  </div>
  <strong>Copyright &copy; 2020 <a href="https://opencartforum.com/files/file/5445-menedzher-zakazov/" target="_blank"><?php echo $heading_title; ?></a></strong>. All rights reserved.
</footer>
<aside class="control-sidebar control-sidebar-light">
  <div class="alert callout callout-warning">
    <p><i class="icon fa fa-ban"></i></p>
  </div>
</aside>
</div>

<div class="modal fade" id="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal Title</h4>
      </div>
      <div class="modal-body">
        <p>One fine body&hellip;</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/ompro/ompro.js?<?php echo $version; ?>"></script>

<!--  Widgets -->

<!-- jvectormap -->
<link href="view/javascript/ompro/AdminLTE/jvectormap/jquery-jvectormap-2.0.5.css" rel="stylesheet" />
<script src="view/javascript/ompro/AdminLTE/jvectormap/jquery-jvectormap-2.0.5.min.js"></script>

<script type="text/javascript"><!--

// https://jvectormap.com/documentation/javascript-api/jvm-map/
// https://github.com/10bestdesign/jqvmap
function getJVMap(url = '', widget_code = '', map_code = '') {
	if (url && widget_code) {
		if ($('[omprowidget="'+widget_code+'"]').html() !== '') { return false; }
		url = url.replace(/&amp;/g, "&");
		$.ajax({
			url: url,
			dataType: 'json',
			success: function(json) {
				data = {};
				for (i in json) {
					data[i] = json[i]['total'];
				}
				$('[omprowidget="'+widget_code+'"]').vectorMap({
					map: map_code,
					backgroundColor: '#FFFFFF',
					regionStyle: {
						initial: {
							fill: '#9FD5F1',
							'fill-opacity': 1,
							stroke: 'none',
							'stroke-width': 0,
							'stroke-opacity': 0.8
						}
					},
					series: {
						regions: [{
							values: data,
							scale: ['#9fd5f1', '#1065d2'],
							normalizeFunction: 'polynomial'
						}]
					},
					onRegionTipShow: function(e, label, code) {
						if (json[code]) {
							label.html('<strong>' + label.html() + '</strong><br />' + '<?php echo $text_order; ?> ' + json[code]['total'] + '<br />' + '<?php echo $text_sale; ?> ' + json[code]['amount']);
						}
					},
					onRegionClick: function(e, code) {
				//		$('[omprowidget="'+widget_code+'"]').vectorMap('set', 'focus', { region: code, animate: true });
					},
				});
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

function widgetsStart() {
	if ($('#chart-sale').length) {
		var url = '<?php echo $dashboard_chart_oc; ?>';
		url = url.replace(/&amp;/g, "&");

		var ocversion = '<?php echo $ocversion; ?>';

		if (ocversion < 230) {
			$('#chart-sale').load(url);
			$(document).ready(function(){
				$('#range .active a').trigger('click');
			});
		} else {
			$('#range a').on('click', function(e) {
				e.preventDefault();
				$(this).parent().parent().find('li').removeClass('active');
				$(this).parent().addClass('active');
				$.ajax({
					type: 'get',
					url: url + '&range=' + $(this).attr('href'),
					dataType: 'json',
					success: function(json) {
						if (typeof json['order'] == 'undefined') { return false; }
						var option = {
							shadowSize: 0,
							colors: ['#9FD5F1', '#1065D2'],
							bars: {
								show: true,
								fill: true,
								lineWidth: 1
							},
							grid: {
								backgroundColor: '#FFFFFF',
								hoverable: true
							},
							points: {
								show: false
							},
							xaxis: {
								show: true,
								ticks: json['xaxis']
							}
						}

						$.plot('#chart-sale', [json['order'], json['customer']], option);

						$('#chart-sale').bind('plothover', function(event, pos, item) {
							$('.tooltip').remove();

							if (item) {
								$('<div id="tooltip" class="tooltip top in"><div class="tooltip-arrow"></div><div class="tooltip-inner">' + item.datapoint[1].toFixed(2) + '</div></div>').prependTo('body');

								$('#tooltip').css({
									position: 'absolute',
									left: item.pageX - ($('#tooltip').outerWidth() / 2),
									top: item.pageY - $('#tooltip').outerHeight(),
									pointer: 'cusror'
								}).fadeIn('slow');

								$('#chart-sale').css('cursor', 'pointer');
							} else {
								$('#chart-sale').css('cursor', 'auto');
							}
						});
					},
					error: function(xhr, ajaxOptions, thrownError) {
					   alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});

			$('#range .active a').trigger('click');
		}
	}

	$('[omprowidget]').each(function(){
		var widget_code = $(this).attr('omprowidget');
		if (widget_code == 'map_ru_mill') {
			$.getScript("view/javascript/ompro/AdminLTE/jvectormap/jquery-jvectormap-ru-mill.js", function(data, textStatus, jqxhr) {
				getJVMap('<?php echo $ompro_widget_map_ru_mill; ?>', widget_code, 'ru_mill');
			});
		} else if (widget_code == 'map_europe_mill') {
			$.getScript("view/javascript/ompro/AdminLTE/jvectormap/jquery-jvectormap-europe-mill.js", function(data, textStatus, jqxhr) {
				getJVMap('<?php echo $ompro_widget_map_europe_mill; ?>', widget_code, 'europe_mill');
			});
		} else if (widget_code == 'map_asia_mill') {
			$.getScript("view/javascript/ompro/AdminLTE/jvectormap/jquery-jvectormap-asia-mill.js", function(data, textStatus, jqxhr) {
				getJVMap('<?php echo $ompro_widget_map_asia_mill; ?>', widget_code, 'asia_mill');
			});
		} else if (widget_code == 'map') {
			$.getScript("view/javascript/ompro/AdminLTE/jvectormap/jquery-jvectormap-world-mill-en.js", function(data, textStatus, jqxhr) {
				getJVMap('<?php echo $ompro_widget_map; ?>', widget_code, 'world_mill_en');
			});
		}
	});
}

//--></script>

<!-- iCheck -->
<link href="view/javascript/ompro/AdminLTE/iCheck/all.css" rel="stylesheet" />
<script src="view/javascript/ompro/AdminLTE/iCheck/fastclick.js"></script>
<script src="view/javascript/ompro/AdminLTE/iCheck/icheck.min.js"></script>
<!-- Toastr -->
<link href="view/javascript/ompro/AdminLTE/toastr/toastr.min.css" rel="stylesheet" />
<script src="view/javascript/ompro/AdminLTE/toastr/toastr.min.js"></script>
<!-- ResizeSensor -->
<script type="text/javascript" src="view/javascript/ompro/ResizeSensor.js"></script>
<script type="text/javascript" src="view/javascript/ompro/ElementQueries.js"></script>

<script type="text/javascript"><!--

function joystickStart() {
	if ($('.parent-width-scroll-fixed').length) {
		var scrollPos = 0;

		$('.parent-width-scroll-fixed').each(function() {
			if ($(this).hasClass('scroll-fixed')) {
				$('body').css({ 'background': 'url(\'view/javascript/ompro/n.gif\') no-repeat', 'background-attachment': 'fixed' });
			}

			var elem = $(this);
			elem.css({ 'margin-bottom': '20px' });
			var box = elem.parent();

			if (elem.outerWidth() > box.outerWidth()) {
				box.addClass('width-scroll-fixed');
				box.css({ 'overflow-x': 'hidden', position: 'relative', width: 'auto', 'min-height': '.01%' });

				if (!box.find('.width-scroll').length) {
					box.append('<div class="width-scroll" style="width:100%; height:auto; overflow-x:scroll; z-index: 2; transition:none;"><div class="scroll" style="min-height:1px;"></div></div>');
					box.find('.width-scroll .scroll').css({width: elem.outerWidth()});
				}

				var scrollbar = box.find('.width-scroll');
				var box_bigger = (box.offset().top + box.outerHeight()) > ($(window).height() + $(window).scrollTop() + 15);

				if (box_bigger) {
					scrollbar.css({position: 'absolute', top: $(window).height() + $(window).scrollTop() - box.offset().top - 15, bottom: 'auto'});
				} else {
					if (elem.outerHeight() > box.outerHeight() || (box.offset().top + box.outerHeight() < ($(window).height() + $(window).scrollTop()))) {
						scrollbar.css({position: 'sticky', top: 'auto', bottom: 0});
					} else {
						scrollbar.css({position: 'absolute', top: 'auto', bottom: 0});
					}
				}
			}

			var scrollbar = box.find('.width-scroll');
			box.find('.width-scroll .scroll').css({width: elem.outerWidth()});

			if (elem.outerWidth() > box.outerWidth()) {
				$(window).scroll(function() {
					var box_bigger = (box.offset().top + box.outerHeight()) > ($(window).height() + $(window).scrollTop() + 15);
					if (box_bigger) {
						scrollbar.css({position: 'absolute', top: ($(window).height() + $(window).scrollTop() - elem.offset().top - 15), bottom: 'auto', left: scrollbar.scrollLeft()});
					} else {
						if (elem.outerHeight() > box.outerHeight() || (box.offset().top + box.outerHeight() < ($(window).height() + $(window).scrollTop()))) {
							box.css({ 'overflow-x': 'hidden' });
							scrollbar.css({position: 'sticky', top: 'auto', bottom: 0, left:0});
						} else {
							box.css({ 'overflow-y': 'hidden' });
							scrollbar.css({position: 'absolute', top: ($(window).height() + $(window).scrollTop() - box.offset().top - 15), bottom: 'auto', left: scrollbar.scrollLeft()});
						}
					}
				});
			}

			$('.width-scroll-fixed').scroll(function() {
				var scrollPos = $(this).scrollTop();
				if (scrollPos) {
					var box = $(this);
					var elem =  box.find('.parent-width-scroll-fixed');
					var scrollbar = box.find('.width-scroll');
					if (elem.outerWidth() > box.outerWidth()) {
						var box_bigger = (box.offset().top + box.outerHeight()) > ($(window).height() + $(window).scrollTop() + 15);
						if (box_bigger) {
							scrollbar.css({position: 'absolute', top: $(window).height() + $(window).scrollTop() - elem.offset().top - 15, bottom: 'auto'});
						} else {
							if (elem.outerHeight() > box.outerHeight() || (box.offset().top + box.outerHeight() < ($(window).height() + $(window).scrollTop()))) {
								scrollbar.css({position: 'sticky', top: 'auto', bottom: 0});
								scrollbar.css({left:0});
							} else {
								scrollbar.css({position: 'absolute', top: 'auto', bottom: 0});
							}
						}
					}
				}
			});

			$('.width-scroll').each(function() {
				var scrollbar = $(this);
				scrollbar.scroll(function() {
					var position = scrollbar.css('position');
					scrollbar.closest('.width-scroll-fixed').scrollLeft(scrollbar.scrollLeft());
					if (position == 'sticky') {
						scrollbar.css({left:0});
					} else {
						scrollbar.css({left: scrollbar.scrollLeft()});
					}
				});
			});

			new ResizeSensor(jQuery('body'), function(size){
				if (elem.length && box.length) {
					box.find('.width-scroll .scroll').css({width: elem.outerWidth()});
					box.find('.width-scroll').trigger('scroll');
				}
			});
		});

		setTimeout(function() {
			$(window).trigger('scroll');
		}, 100);
	}

	if ($('.joystick-table-responsive').length) {
		var table = $('.joystick-table');
		var resp = table.closest('.joystick-table-responsive');
		var table_width = table.outerWidth();
		var resp_width = resp.outerWidth();
		if (table_width > resp_width) {
			var html = '<div class="joystick"><div class="joystick_left"><i class="fa fa-chevron-left fa-2x"></i></div><div class="joystick_right"><i class="fa fa-chevron-right fa-2x"></i></div></div>';
			resp.prepend(html);
			$('.joystick').hover(
				function() {
					$(this).animate({
						'opacity': '0.8'
					}, 300);
				},
				function() {
					$(this).animate({
						'opacity': '0.3'
					}, 300);
				}
			);
			$('.joystick_left, .joystick_right').click(function() {
				var that = $(this);
				var scroll_left = resp.scrollLeft();
				if (that.attr('class') == 'joystick_left') {
					resp.animate({
						'scrollLeft': (scroll_left - 500)
					}, 500);
				} else {
					resp.animate({
						'scrollLeft': (scroll_left + 500)
					}, 500);
				}
			});
		}
	}
}

$(window).resize(function() {
	joystickStart();
});

toastr.options = {
	"closeButton": false,
	"debug": false,
	"newestOnTop": true,
	"progressBar": true,
	"positionClass": "toast-top-right",
	"preventDuplicates": false,
	"onclick": null,
	"showDuration": "300",
	"hideDuration": "1000",
	"timeOut": "5000",
	"extendedTimeOut": "100",
	"showEasing": "swing",
	"hideEasing": "linear",
	"showMethod": "slideDown",
	"hideMethod": "fadeOut",
}

// https://github.com/fronteed/icheck

function iCheckStart() {
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_minimal-blue',
		radioClass: 'iradio_minimal-blue'
	});

	$('.check-all-selected').next('.iCheck-helper').on('click', function() {
		var trigger = $(this).prev('.check-all-selected');
		var clicks = trigger.data('clicks');
		var table = trigger.closest('table');
		var table_id = table.attr('id');
		if (table) {
			if (preventFunctionEditor()) { $(document).find("input[type='checkbox'].minimal").iCheck("uncheck"); return false; }
			if (clicks) {
				table.find("input[type='checkbox'].minimal").iCheck("uncheck");
			} else {
				table.find("input[type='checkbox'].minimal").iCheck("check");
			}
			trigger.data("clicks", !clicks);
		}
	});

	$('input[name^=\'selected\']').next('.iCheck-helper').on('click', function() {
		var table = $(this).closest('table');
		if (table && preventFunctionEditor()) {
			table.find("input[type='checkbox']").iCheck("uncheck"); return false;
		}
	});

	$('input[name^=\'selected\']').on('ifChanged', function() {
		var table = $(this).closest('table');
		var table_id = table.attr('id');
		if (preventFunctionEditor()) { return false; }

		if (table.hasClass('show-selected-orders-total')) {
			showOrdersTotal(table_id);
		}

		var selected = table.find('input[name^=\'selected\']:checked');
		$('[data-btnaction="apply_batch"]').each(function() {
			var btn = $(this);
			var text = btn.text();
			var btntext = btn.attr('data-title-text');

			if (btntext) {
			} else if (!btntext && text) {
				btn.attr('data-title-text', text);
			} else if (btn.attr('title').length) {
				btn.attr('data-title-text', btn.attr('title'));
			} else {
				btn.attr('data-title-text', 'Выполнить');
			}

			btntext = btn.attr('data-title-text');

			if (selected.length) {
				btn.html(btntext +' ('+selected.length+')');
				btn.prop('disabled', false);
			} else {
				btn.html(btntext);
				btn.prop('disabled', true);
			}
		});
	});

	if ($('.omanager-content').attr('id') == 'editor') {
		$('[data-btnaction^="apply_batch"]').prop('disabled', false);
	} else {
		$('[data-btnaction^="apply_batch"]').prop('disabled', true);
	}
}

function showOrdersTotal(table_id) {
	var table = $('#'+table_id);

	var decimals = '<?php echo $config_cur_decimals; ?>';
	var dec_point = '<?php echo $config_cur_decimal_point; ?>';
	var thousands_sep = '<?php echo $config_cur_thousand_point; ?>';
	var symbol_left = '<?php echo $config_cur_sym_left; ?>';
	var symbol_right = '<?php echo $config_cur_sym_right; ?>';

	var selected = table.find('input[name^=\'selected\']:checked');
	var qty = 0; var sum = 0;

	if (selected.length > 0) {
		selected.each(function() {
			var order_row = $(this).closest('tr.order-row');
			sum += Number(order_row.data('ordertotalvalue'));
			qty++;
		});
		var sum_format = numberFormat(sum, decimals, dec_point, thousands_sep);
		toastr.options.timeOut = 0;
		toastr.remove();
		toastr.info('<?php echo $text_selected_orders; ?>' + qty + '<?php echo $text_for_sum; ?>' + symbol_left + sum_format + symbol_right, '');
		toastr.options.timeOut = 5000;
	} else {
		toastr.remove();
	}
}

function mapStart(map_data) {
	var map_container = $('#order-map');

	if (map_container.length) {
		var param = map_data['apikey'] && map_data['apikey'] != '' ? 'apikey=' + map_data['apikey'] + '&lang=ru_RU' : 'lang=ru_RU';
		var api_url = 'https://api-maps.yandex.ru/2.1/?'+param;

		$.cachedScript(api_url).done(function(script, textStatus) {
			if (map_data['height_map']) {
				map_container.attr('style', 'height:'+map_data['height_map']+'px; overflow: hidden;');
			}

			var map_orders = map_data['map_orders'];
			var myMap = null;
			ymaps.ready(init);

			var shop_on_map = map_data['shop_on_map'] ? true : false;

			var map_zoom = map_data['map_zoom'] ? map_data['map_zoom'] : 14;
			var map_center = map_data['map_center'] ? map_data['map_center'] : 1;

			function init () {
				map_container.show();
				myMap = new ymaps.Map('order-map', {
					center: [map_data['shop_coords'][0], map_data['shop_coords'][1]],
					zoom: map_zoom,
					controls: ['geolocationControl', 'zoomControl', 'searchControl', 'typeSelector', 'fullscreenControl']
				}, {
					searchControlProvider: 'yandex#search',
					geolocationControlSize: 'small',
					searchControlSize: 'small',
					typeSelectorSize: 'small',
				}),

				myMap.behaviors.disable('scrollZoom');

				if (map_data['shop_on_map']) {
					shopPlacemark = new ymaps.Placemark([map_data['shop_coords'][0], map_data['shop_coords'][1]], {
						balloonContentBody: map_data['shop_ballooncontent'],
						hintContent: map_data['shop_name']
					}, {
						preset: 'islands#blueShoppingIcon',
						iconColor: 'brown'
					});
					myMap.geoObjects.add(shopPlacemark);
				}

				objectManager = new ymaps.ObjectManager({
					geoObjectOpenBalloonOnClick: false,
					clusterize: true,
					clusterIconLayout: 'default#pieChart',
					gridSize: 64
				});

				myMap.geoObjects.add(objectManager);
				objectManager.add(map_orders);

				function loadBalloonData(objectId) {
					var dataDeferred = ymaps.vow.defer();
					function resolveData() {
						$.ajax({
							url: 'index.php?route=sale/ompro/getMapBalloonData&<?php echo $strtoken; ?>&order_id='+objectId,
							type: "post",
							dataType: "json",
						}).done(function(data) {
							dataDeferred.resolve(data);
						});
					}
					window.setTimeout(resolveData, 100);
					return dataDeferred.promise();
				}

				function hasBalloonData(objectId) {
					return objectManager.objects.getById(objectId).properties.balloonContent;
				}

				objectManager.objects.events.add('click', function(e) {
					var objectId = e.get('objectId');
					if (hasBalloonData(objectId)) {
						objectManager.objects.balloon.open(objectId);
					} else {
						loadBalloonData(objectId).then(function(data) {
							var obj = objectManager.objects.getById(objectId);
							obj.properties.balloonContent = data;
							objectManager.objects.balloon.open(objectId);
						});
					}
				});

				objectManager.clusters.events.add('balloonopen', function(e) {
					var clasterId = e.get('objectId');
					var mcluster = objectManager.clusters.getById(e.get('objectId'));
					var cobjects = mcluster.properties.geoObjects;
					function setwait() {
						objectManager.clusters.state.set('activeObject', cobjects[1]);
						if (objectManager.clusters.balloon.isOpen(clasterId)) {
							setTimeout(function() {
								objectManager.clusters.state.set('activeObject', cobjects[0]);
							}, 100);
						}
					}
					$(cobjects).each(function(key, value) {
						var object_id = value.id;
						if (!value.properties.balloonContent) {
							loadBalloonData(object_id).then(function(data) {
								var cobject = cobjects[key];
								cobject.properties.balloonContent = data;
							});
							if (key == 0) {
								setwait();
							}
						}
					});
				});

				function onObjectEvent (e) {
					var objectId = e.get('objectId');
					if (e.get('type') == 'mouseenter') {
						objectManager.objects.setObjectOptions(objectId, {
							cursor: 'help'
						});
					} else {
						objectManager.objects.setObjectOptions(objectId, {
							preset: 'islands#dotIcon'
						});
					}
				}

				function onClusterEvent (e) {
					var objectId = e.get('objectId');
					if (e.get('type') == 'mouseenter') {
						objectManager.clusters.setClusterOptions(objectId, {
							iconPieChartCoreRadius: 15,
							iconPieChartStrokeStyle: 'silver',
							iconPieChartCoreFillStyle: 'silver',
						});
					} else {
						objectManager.clusters.setClusterOptions(objectId, {
							iconPieChartCoreRadius: 12,
							iconPieChartStrokeStyle: 'white',
							iconPieChartCoreFillStyle: 'white',
						});
					}
				}

				objectManager.objects.events.add(['mouseenter', 'mouseleave'], onObjectEvent);
				objectManager.clusters.events.add(['mouseenter', 'mouseleave'], onClusterEvent);

				if (objectManager.objects.getLength() < 1) {
					myMap.hint.open(myMap.getCenter(), '<div style="color: #fff; font-weight: bold; padding: 10px 10px; border: 1px solid #ddd; border-radius: 3px; background-color: #FF4500;"><?php echo $error_map_empty_orders; ?></div>', {
						openTimeout: 1500
					});
				}

				if (objectManager.objects.getLength() <= 1 && shop_on_map && map_center == 1) {
					myMap.setBounds(myMap.geoObjects.getBounds());
					var Zoom = myMap.getZoom();
					Zoom = Zoom > 20 ? 20 : Zoom;
					myMap.setZoom(Zoom);
				} else if (objectManager.objects.getLength() > 1 && map_center == 1) {
					myMap.setBounds(myMap.geoObjects.getBounds());
					var Zoom = myMap.getZoom();
					Zoom = Zoom > 20 ? 20 : Zoom;
					myMap.setZoom(Zoom);
				}
			}
		});
	}
}

function multiselectStart() {
	$(document).find('[multiselect]').each(function(){
		var param = {};
		if ($(this).hasAttr('multiselectParam')) {
			var param = JSON.parse(b64_to_utf8($(this).attr('multiselectParam')));
		}

		$(this).multiselect({
			enableClickableOptGroups: true,
			buttonWidth: '100%',
			buttonContainer: '<div multiselectBtnGroup class="btn-group" />',

			buttonClass: param['bCl'] ? param['bCl'] : 'btn btn-default',
            dropRight: param['dR'] && param['dR'] == 'true' ? true : false,
            dropUp: param['dU'] && param['dU'] == 'true' ? true : false,
			selectedClass: param['selCl'] ? param['selCl'] : 'active',
			maxHeight: param['mH'] ? param['mH'] : false,
			includeSelectAllOption: param['selAll'] && param['selAll'] == 'true' ? true : false,
			includeSelectAllIfMoreThan: param['selAllIfMo'] ? param['selAllIfMo'] : 0,
			selectAllText: param['selAllText'] ? param['selAllText'] : '<?php echo $text_select_all; ?>',
			selectAllNumber: param['selAllNum'] && param['selAllNum'] == 'true' ? true : false,

			nonSelectedText: param['noSelText'] ? param['noSelText'] : '<?php echo $text_not_selected; ?>',
			nSelectedText: param['nSelText'] ? param['nSelText'] : '<?php echo $text_selected; ?>',
			allSelectedText: param['allSelText'] ? param['allSelText'] : '<?php echo $text_all_selected; ?>',
			numberDisplayed: param['numDisp'] ? param['numDisp'] : 1,

			enableFiltering: param['enFilter'] && param['enFilter'] == 'true' ? true : false,
			enableCaseInsensitiveFiltering: param['caseIns'] && param['caseIns'] == 'true' ? true : false,
			filterPlaceholder: param['filterPH'] ? param['filterPH'] : 'Search',
			includeFilterClearBtn: param['fClearBtn'] && param['fClearBtn'] == 'true' ? true : false,

			includeResetOption: param['resetO'] && param['resetO'] == 'true' ? true : false,
			includeResetDivider: param['resetD'] && param['resetD'] == 'true' ? true : false,
			resetText: param['resetT'] ? param['resetT'] : 'Reset',

			delimiterText: ','
		}).show();
	});

	$('select[class*=\'multiselect\']').each(function(){
		$(this).multiselect({
			enableClickableOptGroups: true,
			enableCaseInsensitiveFiltering: true,
			filterPlaceholder: 'Search...',
			buttonClass: 'btn btn-default',
			buttonWidth: '100%',
			maxHeight: 300,
			nonSelectedText: '<?php echo $text_not_selected; ?>',
			nSelectedText: ' <?php echo $text_selected; ?>',
			allSelectedText: '<?php echo $text_all_selected; ?>',
			includeSelectAllOption: true,
			selectAllText: ' <?php echo $text_select_all; ?>',
			numberDisplayed: 1,
			delimiterText: ', '
		}).addClass('active').show();
	});
}

 <!-- datepicker, autocomplete -->
function dpStart() {
//	http://t1m0n.name/air-datepicker/docs/index-ru.html
	var prevVal, curVal;

	$(document).find('[airdatepicker]').each(function(){
		var param = {};
		if ($(this).hasAttr('dpParam')) {
			var param = JSON.parse(b64_to_utf8($(this).attr('dpParam')));
		}

		$('#'+ $(this).attr('id')).airdatepicker({
			dateFormat: param['dF'] ? param['dF'] : 'dd.mm.yyyy',
			position: param['pos'] ? param['pos'] : 'bottom left',
			autoClose: param['aCl'] && param['aCl'] == 'true' ? true : false,
			clearButton: param['clBtn'] && param['clBtn'] == 'true' ? true : false,
			multipleDates: param['mD'] && param['mD'] == 'true' ? true : false,
			multipleDatesSeparator: param['mDs'] ? param['mDs'] : ', ',
			classes: param['cls'] ? param['cls'] : '',
			inline: param['inl'] && param['inl'] == 'true' ? true : false,
			language: '<?php echo $langCode; ?>',
			todayButton: new Date(),
			onShow: function(dp, animationCompleted){
				if (!animationCompleted) {
					prevVal = dp.el.value;
				}
			},
			onHide: function(dp, animationCompleted){
				if (animationCompleted) {
					curVal = dp.el.value;
					if (prevVal == curVal) { return; }
					prevVal = curVal;
					$('#'+ dp.el.id).trigger('change');
				}
			}
		});
	});

	$('.datepicker_default').each(function(){
		var id = $(this).attr('id');

		$('#'+ id).airdatepicker({
			autoClose: true,
			clearButton: true,
			todayButton: new Date(),
			language: '<?php echo $langCode; ?>',
			navTitles: {
				days: 'MM, <i>yyyy</i>',
			},
			onShow: function(dp, animationCompleted){
				if (!animationCompleted) {
					prevVal = dp.el.value;
				}
			},
			onHide: function(dp, animationCompleted){
				if (animationCompleted) {
					curVal = dp.el.value;
					if (prevVal == curVal) { return; }
					prevVal = curVal;
					$('#'+ dp.el.id).trigger('change');
				}
			}
		});
	});
}

$(document).delegate('[autocompleteTarget]', 'focus keyup', function() {
	if (preventFunctionEditor()) { return false; }
	var elem = $('#'+$(this).attr('id'));
	var target = $(this).attr('autocompleteTarget');
	var limit = $(this).attr('autocompleteLimit') ? $(this).attr('autocompleteLimit') : 5;
	elem.autocomplete({
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=sale/ompro_helper/autocomplete&<?php echo $strtoken; ?>&target=' +  encodeURIComponent(target) + '&filter_name=' +  encodeURIComponent(request.term) + '&limit=' +  encodeURIComponent(limit),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) { return { label: item['name'] } }));
				}
			});
		},
		minLength: 2,
		select: function(event, item) {
			elem.val(item.item['label']);
		}
	});
});

--></script>

<!-- xEditable -->
<link type="text/css" rel="stylesheet" href="view/javascript/ompro/bootstrap3-editable/css/bootstrap-editable.css"/>
<script type="text/javascript" src="view/javascript/ompro/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<script type="text/javascript"><!--
// xEditable Project URL :  http://vitalets.github.io/x-editable/docs.html#gettingstarted
// https://github.com/RobinHerbots/Inputmask/
// http://t1m0n.name/air-datepicker/docs/index-ru.html

function xEditableStart() {
	var url = 'index.php?route=sale/ompro/xEditDbData&<?php echo $strtoken; ?>';

	$('[xedit="text"],[xedit="textarea"],[xedit="email"],[xedit="password"],[xedit="time"],[xedit="select"]').editable({ url: url });

	$('[xedit="mask"]').each(function() {
		var mask = $(this).data('mask');
		$(this).editable({ url: url,
			tpl: '<input type="text" style="width: 100%;" class="form-control input-sm xeditablemask">',
        }).on('shown',function(){
            $("input.xeditablemask").inputmask(mask);
        });
	});

	$('[xedit="airdatepicker"]').each(function() {
		var timepicker = false; var timeFormat = '';
		var dateFormatset = $(this).data('dateFormat');
		var dateFormat = dateFormatset ? dateFormatset : 'dd.mm.yyyy';
		var timeFormatset = $(this).data('timeFormat');
		if (timeFormatset) { timepicker = true; timeFormat = timeFormatset; }

		$(this).editable({  url: url, clear: false,
			tpl:'   <input type="text" style="width: 100%;" class="form-control input-sm airdatepicker">'
		}).on('shown',function(){
			var myDatepicker = $("input.airdatepicker").airdatepicker({
				autoClose: true, clearButton: true, inline: true, todayButton: new Date(),
				dateFormat: dateFormat, timepicker: timepicker, timeFormat: timeFormat, minutesStep: 10,
				language: '<?php echo $langCode; ?>', navTitles: { days: 'MM <i>yyyy</i>', }
			});

			var initDate = new Date($(this).data('value'));
			if (isValidDate(initDate)) {
				myDatepicker.data('airdatepicker').selectDate(initDate);
			}
		});
	});

	$('[xedit="checklist"]').each(function() {
		$(this).editable({ url: url,
			display: function(value, sourceData) {
				var html = [], checked = $.fn.editableutils.itemsByValue(value, sourceData);
				if(checked.length) {
					$.each(checked, function(i, v) { html.push($.fn.editableutils.escape(v.text)); });
					$(this).html(html.join(', '));
				} else { $(this).empty(); }
			}
		});
	});

	// file

	$('.btn-fileupload').on('click', function() {
		var suf = $(this).data('suf');

		$('#form-upload_' + suf).remove();
		$('body').prepend('<form enctype="multipart/form-data" id="form-upload_' + suf + '" style="display: none;"><input type="file" name="file" id="inputfile_' + suf + '" /></form>');
		$('#form-upload_' + suf + ' input[id=\'inputfile_' + suf + '\']').trigger('click');

		if (typeof timer != 'undefined') {
			clearInterval(timer);
		}

		timer = setInterval(function() {
			if ($('#form-upload_' + suf + ' input[id=\'inputfile_' + suf + '\']').val() != '') {
				clearInterval(timer);

				$.ajax({
					url: 'index.php?route=sale/ompro_helper/upload&<?php echo $strtoken; ?>',
					type: 'post', dataType: 'json',
					data: new FormData($('#form-upload_' + suf )[0]),
					cache: false, contentType: false, processData: false,
					beforeSend: function() {
						$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
					},
					success: function(json) {
						$('.text-loading').remove();
						if (json['error']) {
							toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
						}

						if (json['success']) {
							toastr.success(json['success'], '<?php echo $text_alert_success; ?>');

							$('span[id=\'name_' + suf +'\']').text(json['name']);
							$('span[id=\'name_' + suf +'\']').closest('.valid-block').append('<span class="error-span error-active"><?php echo $text_filedata_not_saved; ?></span>');

							$('input[id=\'filename_' + suf +'\']').val(json['filename']);
							$('input[id=\'filecode_' + suf +'\']').val(json['code']);
							$('button[id=\'filesave_'+suf+'\']').attr('disabled', false);
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}, 500);
	});

	$('button.filesave').on('click', function() {
		var suf = $(this).attr('data-suf');
		var name = $('span[id=\'name_' + suf +'\']').text();
		var code = $('input[id=\'filecode_' + suf +'\']').val();

		var data = {};
		data.order_id = $(this).attr('data-order_id');
		data.log = $(this).attr('data-log');
		data.action = $(this).attr('data-action');
		data.dbTable = $(this).attr('data-dbTable');
		data.name = $(this).attr('data-name');
		data.pk = $(this).attr('data-pk');
		data.pkname = $(this).attr('data-pkName');
		data.value = $('input[id=\'filename_' + suf +'\']').val();

		$.ajax({
			url: 'index.php?route=sale/ompro/xEditFile&<?php echo $strtoken; ?>',
			type: 'post', dataType: 'json',
			data: data,
			beforeSend: function() {
				$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
			},
			success: function(json) {
				$('.text-loading').remove();
				$('.error-span').remove();
				if (json['error']) {
					toastr.options.timeOut = 5000;
					toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
				}

				if (json['success']) {
					toastr.options.timeOut = 3000;
					toastr.success(json['success'], '<?php echo $text_alert_success; ?>');

					if ($(this).attr('pageReload') == '1') {
						getContent();
					} else {
						$('span[id=\'name_' + suf +'\']').html('<a href="index.php?route=sale/ompro_helper/download&<?php echo $strtoken; ?>&code='+code+'" >'+name+'</a>');
					}

					$('button[id=\'filesave_'+suf+'\']').attr('disabled', true);
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	$('[xedit]').on('save', function(e, params) {
		if ($(this).attr('pageReload') == '1') {
			var order_id = $(this).closest('.order-row').data('orderid');
			$(document).find('.table-orders').each(function() {
				var table = $(this); var table_id = table.attr('id');
				if (order_id && table_id) { orderReload(order_id, table_id); }
			});
		}
	}).on('shown', function(e, editable) {
		if (preventFunctionEditor()) { editable.hide(); return false; }
	});
}

--></script>

<!-- Content -->
<script type="text/javascript"><!--
function refreshPlugForOrders() {
	dpStart(); multiselectStart(); xEditableStart(); controlCheckedInput();
	$('[data-mask]').inputmask(); iCheckStart(); tooltipRefresh();
}

function pageidBtnActive(pageid) {
	$('[data-btnaction]').each(function(){
		var action = $(this).attr('data-btnaction');
		res = action.split(/_/);
		if (res[0] && res[0] == 'pageid' && res[1]) {
			if (res[1] !== pageid) {
				$('[data-btnaction="pageid_'+res[1]+'"]').removeClass('active');
			}
		}
	});
	$('[data-btnaction="pageid_'+pageid+'"]').addClass('active');
}

function orderTplView(order_id, tpl_code, target = 'modal', to_edit = 0, source_copy = '') {
	var url = '<?php echo $order_tpl_view; ?>';
	url = url.replace(/&amp;/g, "&");

	if (order_id && tpl_code && target) {
		url += '&order_id=' + encodeURIComponent(order_id) + '&template_id=' + encodeURIComponent(tpl_code);
		var pageid = getURLVar('pageid');
		if (pageid) { url += '&pageid=' + pageid; }
		if (to_edit == 1) { url += '&to_edit=' + to_edit; }

		var tempElem = $('<div class="tempElem"></div>');
		tempElem.load(url, function() {
			var text = ''; var text_source = null;
			if (source_copy) {
				var text_source = tempElem.find(source_copy);
			}
			if (target == 'copytext') {
			} else if (target == 'modal') {
				modalAlert('modal-default', '<?php echo $text_order_no; ?>' + order_id, tempElem.html(), '', 'modal-lg');
				refreshPlugForOrders();
			} else {
				$(target).hide();
				$(target).slideDown(500, function() {
					$(target).html(tempElem.html());
					if (target !== 'copytext') { refreshPlugForOrders(); }
					var top = $(target).offset().top;
					$('body,html').animate({scrollTop: top}, 'slow');
				});
			}

			if (text_source) {
				setTimeout(function() {
					var text = text_source.text();
					if (text.length) {
						var copytext = document.createElement('textarea');
						copytext.value = text;
						document.body.appendChild(copytext);
						copytext.select();
						document.execCommand('copy');
						document.body.removeChild(copytext);
						toastr.success('Текст успешно скопирован в буфер обмена!', '<?php echo $text_alert_success; ?>');
					}
				}, 100 );
			}

			tempElem.remove();
		});
	}
}

//	View orders with filters
function ordersTableView(tpl_code, filters = '', limit = 0, to_edit = 0, target_elem = 'modal', refresh = false) {
	var url = '<?php echo $orders_table_view; ?>';
	url = url.replace(/&amp;/g, "&");

	if (tpl_code && filters && target_elem) {
		url += '&template_id=' + encodeURIComponent(tpl_code);
		if (filters) { url += '&filters=' + utf8_to_b64(filters); }
		if (to_edit) { url += '&to_edit=1'; }
		if (limit) { url += '&limit=' + encodeURIComponent(limit); }

		var tempDom = $('<div><div id="orders_table_view_temp" data-tpl_code="'+tpl_code+'" data-filters="'+filters+'" data-limit="'+limit+'" data-to_edit="'+to_edit+'" data-target_elem="'+target_elem+'"></div></div>');

		tempDom.find('#orders_table_view_temp').load(url, function() {
			if (target_elem == 'modal') {
				if (refresh) {
					$('#orders_table_view_temp').replaceWith( tempDom.html() );
				} else {
					modalAlert('modal-default', 'Просмотр заказов', tempDom.html(), '', 'modal-lg');
				}
			} else {
				$(target_elem).hide();
				$(target_elem).slideDown(500, function() {
					$(target_elem).html(tempDom.html());
					var top = $(target_elem).offset().top;
					$('body,html').animate({scrollTop: top}, 'slow');
				});
			}

			$(document).delegate('#orders_table_view_temp .pagination a', 'click', function(e) {
				e.preventDefault();
				$('#orders_table_view_temp').load(this.href, function() {
					refreshPlugForOrders();
				});
			});
			tempDom.remove();
			refreshPlugForOrders();
		});
	}
}

function orderReload(order_id, table_id = '') {
	var url = '<?php echo $order_reload; ?>';
	url = url.replace(/&amp;/g, "&");

	if (table_id) {
		var table = $('#'+table_id);
	} else {
		var table = $('.order_row_'+order_id).closest('.table-orders');
	}

	if (table) {
		var order_row = table.find('.order_row_'+order_id);
		var pageid = getURLVar('pageid');
		if (pageid) {
			url += '&pageid=' + encodeURIComponent(pageid);
		}
		var template_id = order_row.closest('[data-orderTPL]').attr('data-orderTPL');

		if (order_row.length && template_id && order_id) {
			url += '&template_id=' + encodeURIComponent(template_id) + '&order_id=' + encodeURIComponent(order_id);

			var tempDom = $('<div id="order_temp"></div>');
			tempDom.load(url, function() {
				var new_order_row = tempDom.find('.order-row.order_row_'+order_id);
				if (new_order_row.length) {
					order_row.replaceWith(new_order_row);
					refreshPlugForOrders(); joystickStart(); prepareBatch();
				}
				tempDom.remove();
			});
		}
	}
}

function filter() {
	var data = {};
	data['filters'] = {};
	i = 0;

	$('[filter_input]:not(#select_order_limit, [selectoptions="orderLimitOptions"], [selectoptions="orderLimitShortOptions"])').each(function() {
		if ($(this).val() && $(this).val() != '') {
			data['filters'][i]  = {};
			data['filters'][i]['filter_id'] = $(this).attr('id');
			data['filters'][i]['value'] =  $(this).val();
			i++;
		}
	});
	return data;
}

function getContent(href = '', page = '',  error = '', warning = '', success = '') {

	var orders_staus = false;
	if ($('.template-editor').length || $('.omanager-content').attr('id') == 'omanager') {
		orders_staus = true;
	}

	if (!orders_staus && page == 'page') {
		var url = 'index.php?route=sale/ompro/orders&<?php echo $strtoken; ?>' + href;
		location = url; return false;
	}

	var data = {}; data['filters'] = {};

	var editor_staus = false;
	if ($('.template-editor').length || $('.omanager-content').attr('id') == 'editor') {
		editor_staus = true;
	}

	if (editor_staus) {
		var url = 'index.php?route=sale/ompro/content&<?php echo $strtoken; ?>' + href;
	} else {
		var url = 'index.php?route=sale/ompro/content&<?php echo $strtoken; ?>';

		if (href) {
			url += href;
		} else {
			var pageid = $('input[id=\'orders-pageid\']').val();
			if (pageid) {
				url += '&pageid=' + encodeURIComponent(pageid);
			}
			var sort = $('input[id=\'sort\']').val();
			if (sort) {
				url += '&sort=' + encodeURIComponent(sort);
			}
			var order = $('input[id=\'order\']').val();
			if (order) {
				url += '&order=' + encodeURIComponent(order);
			}
			var page = $('input[id=\'page\']').val();
			if (page) {
				url += '&page=' + encodeURIComponent(page);
			}
		}

		var limit = $('select[id=\'select_order_limit\']').val();
		if (limit) { url += '&limit=' + encodeURIComponent(limit); }

		var data = filter();
	}

	$.ajax({
		url: url,
		dataType: 'json',
		data: data,
		type: 'POST',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; z-index: 9999; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();

			if (json['content'] && json['content'] != '') {
				$('.omanager-content').html(json['content']);
				if (json['css_script'] && json['css_script'] != '') { $('#css_script').html(json['css_script']); }

				toastr.remove(); joystickStart(); widgetsStart(); prepareBatch();

				if (json['map_data']) {
					mapStart(json['map_data']);
				}

				if (editor_staus) {
					formatSortableStart();
					startValidationAdd();
				}
			} else {
				if (!editor_staus) {
					$('.omanager-content').html('');
					$('#css_script').html('');
					modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>Содержимое страницы не настроено или нет страницы для отображения! Перейдите в настройки групп пользователей и сделайте необходимые настройки для вашей группы!</p>', '');
					return false;
				}
			}

			refreshPlugForOrders();

			if (editor_staus) {
				$(document).delegate('.setdata, #form-editor', 'change', function() {
					if ($('#button_save').hasClass('btn-primary')) {
						$('#button_save').removeClass('btn-primary').addClass('btn-danger');
					}
				});
			} else {
				if (json['pageid']) {
					$('input[id=\'orders-pageid\']').val(json['pageid']);
					$('.pageid').removeClass('active');
					if (orders_staus) {
						$('#pageid_'+json['pageid']).addClass('active');
						if (!$('#pageid_'+json['pageid']).closest('.treeview').hasClass('active')) {
							$('#pageid_'+json['pageid']).closest('.treeview').addClass('active');
						}
					}
				}
			}

			if (json['filter_data']) {
				var filter_data = json['filter_data'];
				if (filter_data['pageid']) {
					pageidBtnActive(filter_data['pageid']);
					$('select[selectOptions="orderPageSelectOptions"]').val(filter_data['pageid']);
				}
				if (filter_data['limit']) {
					$('select[selectOptions=\'orderLimitOptions\']').val(filter_data['limit']);
				}
				if (filter_data['sort']) {
					$('input[id=\'sort\']').val(filter_data['sort']);
				}
				if (filter_data['order']) {
					$('input[id=\'order\']').val(filter_data['order']);
				}
				if (filter_data['page']) {
					$('input[id=\'page\']').val(filter_data['page']);
				}
				if ($('[input_quick_filter]').val() && filter_data['filters'] && filter_data['filters'][$('[input_quick_filter]').attr('id')]) {
					$('.quick-filter-trigger').removeClass('active hover');
					$('.quick-filter-trigger[data-filter_value="'+filter_data['filters'][$('[input_quick_filter]').attr('id')]+'"]').addClass('active hover');
				}
			}

			toastr.options.timeOut = 10000;
			if (error) {
				toastr.error(error, '<?php echo $text_alert_error; ?>');
			}
			if (warning) {
				toastr.warning(warning, '<?php echo $text_alert_warning; ?>');
			}
			if (success) {
				toastr.success(success, '<?php echo $text_alert_success; ?>');
			}

			$(window).trigger('load');

			toastr.options.timeOut = 5000;

			$(function () { window.validation.init({ container: '#form-editor' });});
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

$(document).delegate('[data-btnaction]', 'click', function(e) {
	var action = $(this).data('btnaction');

	if (action == 'orderpro_merge_orders' && isFunction(window["getmerge"])) { getmerge(); return false; }

	res = action.split(/_/);
	if (res[0] && res[0] == 'pageid' && res[1]) {
		if (preventFunctionEditor()) { return false; }
		var href = '&pageid=' + encodeURIComponent(res[1]);
		getContent(href); return false;
	}

	var export_notify_list = 'order_delete print_shipping print_invoice orderpro_invoice orderpro_export_orders print_orders print_orders_table print_products_table excel_orders excel_orders_products send_mail send_sms send_tlgrm'.split(/\s+/);

	// action
	if (action == 'filter_clear') { $('[filter_Input]').val(''); if (preventFunctionEditor()) { return false; } getContent(); }
	else if (action == 'filter_apply') { if (preventFunctionEditor()) { return false; } getContent(); }
	else if (action == 'clear_filter_this' || action == 'clear_filter_product_this') {
		if (action == 'clear_filter_this') {
			var filter = $(this).closest('.input-group').find('[filter_Input]');
		} else {
			var filter = $(this).closest('.input-group').find('[filter_product_Input]');
		}

		var filter_val = filter.val();

		if (filter.attr('id') == $('[input_quick_filter]').attr('id')) {
			$('[input_quick_filter]').val('');
		}

		if (filter_val && filter.hasAttr('multiselect') && Array.isArray(filter_val)) {
			filter.multiselect('clearSelection');
			filter.trigger('change');
		} else if (filter_val && !Array.isArray(filter_val)) {
			filter.val('');
			if (filter.hasAttr('multiselect')) {
				filter.multiselect('refresh');
			}
			filter.trigger('change');
		}
	}

	else if (action == 'apply_batch') { if (preventFunctionEditor()) { return false; } batch(); }
	else if (action == 'slide_on_id' && $(this).data('target')) { $('#'+$(this).attr('data-target')).slideToggle(); }
	else if (action == 'slide_on_class' && $(this).data('target')) {
		var btn = $(this); var target = btn.data('target');
		if (btn.hasClass("expanded")) { btn.removeClass("expanded"); }
		$('.'+target).each(function(){ var el = $(this);  if (!el.is(':hidden')) { btn.addClass("expanded"); } });
		if (btn.hasClass("expanded")) {
			$('.'+target).slideUp(); btn.removeClass("expanded");
			$('.'+target).each(function(){
				var this_ = $(this);
				if (this_.hasClass('box-body')) {
					var fa = this_.parent().find('.box-tools .fa-minus');
					if (!fa.length) { fa = this_.parent().find('.box-tools .fa-plus'); }
					if (fa.hasClass('fa-minus')) { fa.removeClass("fa-minus").addClass("fa-plus"); }
				}
			});
		} else {
			$('.'+target).slideDown(); btn.addClass("expanded");
			$('.'+target).each(function(){
				var this_ = $(this);
				if (this_.hasClass('box-body')) {
					var fa = this_.parent().find('.box-tools .fa-minus');
					if (!fa.length) { fa = this_.parent().find('.box-tools .fa-plus'); }
					if (fa.hasClass('fa-plus')) { fa.removeClass("fa-plus").addClass("fa-minus"); }
				}
			});
		}
	}

	// open url
	else if (action == 'order_add' || action == 'orderpro_add' || action == 'orderq_add') {
		if (preventFunctionEditor()) { return false; }
		if (action == 'order_add') {
			var url = '<?php echo $order_add; ?>';
		} else if (action == 'orderq_add') {
			var url = '<?php echo $orderq_edit; ?>';
		} else {
			var url = '<?php echo $orderpro_edit; ?>';
		}
		url = url.replace(/&amp;/g, "&");
		var target = $(this).data('target');
		if (target && target == '_blank') {
			window.open(url);
		} else {
			location = url;
		}
	}
	else if (action == 'order_info') {
		var url = '<?php echo $order_info; ?>';
		url = url.replace(/&amp;/g, "&");
		var order_id = $(this).closest('.order-row').data('orderid');
		if (order_id) {
			url += '&order_id=' + order_id;
			var target = $(this).data('target');
			if (target && target == '_blank') {
				window.open(url);
			} else {
				location = url;
			}
		}
	}
	else if (action == 'order_edit' || action == 'orderpro_edit' || action == 'orderq_edit') {
		if (action == 'order_edit') {
			var url = '<?php echo $order_edit; ?>';
		} else if (action == 'orderq_edit') {
			var url = '<?php echo $orderq_edit; ?>';
		} else {
			var url = '<?php echo $orderpro_edit; ?>';
		}

		url = url.replace(/&amp;/g, "&");
		var order_id = $(this).closest('.order-row').data('orderid');
		if (order_id) {
			url += '&order_id=' + order_id;
			var target = $(this).data('target');
			if (target && target == '_blank') {
				window.open(url);
			} else {
				location = url;
			}
		}
	}
	else if (action == 'customer_edit') {
		var customer_id = $(this).data('customer_id');
		if (customer_id) {
			var url = '<?php echo $customer_edit_href; ?>';
			url = url.replace(/&amp;/g, "&");
			url += '&customer_id=' + customer_id;
			var target = $(this).data('target');
			if (target && target == '_blank') {
				window.open(url);
			} else {
				location = url;
			}
		}
	}
	else if (action == 'customer_login') {
		var customer_id = $(this).data('customer_id');
		var store_id = $(this).closest('.order-row').data('storeid');
		if (customer_id && store_id !== '') {
			var url = '<?php echo $customer_login_href; ?>';
			url = url.replace(/&amp;/g, "&");
			url += '&customer_id=' + customer_id + '&store_id=' + store_id;
			var target = $(this).data('target');
			if (target && target == '_blank') {
				window.open(url);
			} else {
				location = url;
			}
		}
	}

	// ajax

	else if (action == 'order_quick_status') {
		if (preventFunctionEditor()) { return false; }
		var change_status_status = '<?php echo $change_status_status; ?>';

		if (!change_status_status) {
			toastr.options.preventDuplicates = true;
			toastr.warning('<?php echo $error_access_change_status; ?>', '<?php echo $text_alert_warning; ?>');
			return false;
		}

		var btn_order_quick_status = $(this);
		var order_id = btn_order_quick_status.data('orderid');
		var data = {};
		data.user_id = '<?php echo $user_id; ?>';
		data.selected =[];
		data.selected.push(order_id);
		data.order_status_id = btn_order_quick_status.attr('data-orderstatusid');
		data.notify_mail = 0;
		data.notify_sms = 0;
		data.notify_tlgrm = 0;
		data.override = 1;
		data.comment = '';

		$.ajax({
			url: '<?php echo $catalog; ?>index.php?route=api/ompro/history',
			type: 'post',
			dataType: 'json',
			data: data,
			beforeSend: function() {
				$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
			},
			success: function(json) {
				$('.text-loading').remove();
				var error, warning, success;
				if (json['error']) { error = json['error']; }
				if (json['warning']) { warning = json['warning']; }
				if (json['success']) { success = json['success']; }

				if (success) {
					if (btn_order_quick_status.closest('.order-row').length) {
						$(document).find('.table-orders').each(function() {
							var table = $(this); var table_id = table.attr('id');
							if (order_id && table_id) { orderReload(order_id, table_id); }
						});
					}

					toastr.success(success, '<?php echo $text_alert_success; ?>');
				} else {
					if (error) { toastr.error(error, '<?php echo $text_alert_error; ?>'); }
					if (warning) { toastr.warning(warning, '<?php echo $text_alert_warning; ?>'); }
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
		return false;
	}

	// load url
	else if (action == 'order_histories_oc') {
		var url = '<?php echo $order_histories_oc; ?>';
		url = url.replace(/&amp;/g, "&");
		var order_id = $(this).closest('.order-row').data('orderid');
		var target_id = $(this).data('targetid');
		if (order_id) {
			url += '&order_id=' + order_id;
			var tempDom = $('<div><div id="order_history_temp"></div></div>');
			tempDom.find('#order_history_temp').load(url, function() {
				if (target_id == 'modal-mode') {
					modalAlert('modal-default', '<?php echo $text_order_no; ?>' + order_id, tempDom.html(), '', 'modal-lg');
				} else {
					$('#'+target_id).html(tempDom.html()); tooltipRefresh();
				}
				$(document).delegate('#order_history_temp .pagination a', 'click', function(e) {
					e.preventDefault();
					$('#order_history_temp').load(this.href);
				});
			});
		}
	}

	else if (action == 'order_history_view') {
		if (preventFunctionEditor()) { return false; }
		var url = '<?php echo $order_histories; ?>';
		url = url.replace(/&amp;/g, "&");

		var order_id = $(this).closest('.order-row').data('orderid');
		if (!order_id) { var order_id = $(this).data('orderid'); }
		if (order_id) { url += '&order_id=' + order_id; }

		var template_id = $(this).data('templateid');
		if (template_id) { url += '&history_template_id=' + template_id; }

		var target_id = $(this).data('targetid');
		if (order_id && target_id) {
			var tempElem = $('<div class="tempElem"></div>');
			tempElem.load(url, function() {
				if (target_id == 'modal-mode') {
					modalAlert('modal-default', '<?php echo $text_order_no; ?>' + order_id, tempElem.html(), '', 'modal-lg');
				} else {
					$('#'+target_id).replaceWith(tempElem.html()); tooltipRefresh();
				}
				return false;
			});
		}
	}

	else if (action == 'customer_transaction_history_oc') {
		var url = '<?php echo $customer_transaction_history_oc; ?>';
		url = url.replace(/&amp;/g, "&");
		var customer = $(this).data('customer');
		var customer_id = $(this).data('customer_id');
		var target_id = $(this).data('targetid');

		if (customer_id < 1) {
			toastr.warning('<?php echo $text_error_register_customer; ?>', '<?php echo $text_alert_warning; ?>');
			return false;
		} else {
			url += '&customer_id=' + customer_id;
			var tempDom = $('<div><div id="customer_transaction_history_temp"></div></div>');
			tempDom.find('#customer_transaction_history_temp').load(url, function() {
				if (target_id == 'modal-mode') {
					modalAlert('modal-default', '<?php echo $text_customer_transaction_history; ?> ' + customer, tempDom.html());
				} else {
					$('#'+target_id).html(tempDom.html()); tooltipRefresh();
				}
				$(document).delegate('#customer_transaction_history_temp .pagination a', 'click', function(e) {
					e.preventDefault();
					$('#customer_transaction_history_temp').load(this.href);
				});
			});
		}
	}

	else if (action == 'customer_reward_history_oc') {
		var url = '<?php echo $customer_reward_history_oc; ?>';
		url = url.replace(/&amp;/g, "&");
		var customer = $(this).data('customer');
		var customer_id = $(this).data('customer_id');
		var target_id = $(this).data('targetid');

		if (customer_id < 1) {
			toastr.warning('<?php echo $text_error_register_customer; ?>', '<?php echo $text_alert_warning; ?>');
			return false;
		} else {
			url += '&customer_id=' + customer_id;
			var tempDom = $('<div><div id="customer_reward_history_temp"></div></div>');
			tempDom.find('#customer_reward_history_temp').load(url, function() {
				if (target_id == 'modal-mode') {
					modalAlert('modal-default', '<?php echo $text_customer_reward_history; ?> ' + customer, tempDom.html());
				} else {
					$('#'+target_id).html(tempDom.html()); tooltipRefresh();
				}
				$(document).delegate('#customer_reward_history_temp .pagination a', 'click', function(e) {
					e.preventDefault();
					$('#customer_reward_history_temp').load(this.href);
				});
			});
		}
	}

	else if (export_notify_list.in_array(action)) {

		var template_id = $(this).data(action+'_tpl');
		var order_row = $(this).closest('.order-row');

		if (order_row.length) {
			$(document).find('.table-orders input[type*="checkbox"].minimal').iCheck("uncheck");
			var table = $(this).closest('.table-orders');
			table.find("input[type*='checkbox'].minimal").iCheck("uncheck");
			order_row.find("input[type='checkbox'].minimal").iCheck("check");
		}

		var selected = $('input[name^=\'selected\']:checked');

		if (!selected.length) {
			modalAlert('modal-warning', '<?php echo $text_alert_warning; ?>', '<p><?php echo $text_alert_not_selected; ?></p>', '');
			return false;
		}
		else {
			// ajax
			if (action == 'order_delete') {
				if (preventFunctionEditor()) { return false; }
				if (confirm("<?php echo $text_delete_confirm; ?>")) {
					var url = '<?php echo $order_delete; ?>';
					url = url.replace(/&amp;/g, "&");

					$.ajax({
						url: 'index.php?route=sale/ompro_helper/deleteOrders&<?php echo $strtoken; ?>',
						data: selected,
						type: 'POST',
						dataType: 'json',
						beforeSend: function() {
							$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
						},
						success: function(json) {
							$('.text-loading').remove();
							if (json['error']) {
								modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
							}
							if (json['success']) {
								var tpl_code, filters, limit, to_edit, target_elem = '';
								var table_view_temp = $(document).find('#orders_table_view_temp');
								if (table_view_temp.length) {
									var tpl_code = table_view_temp.data('tpl_code');
									var filters = table_view_temp.data('filters');
									var limit = table_view_temp.data('limit');
									var to_edit = table_view_temp.data('to_edit');
									var target_elem = table_view_temp.data('target_elem');

									ordersTableView(tpl_code, filters, limit, to_edit, target_elem, true);
								}

								toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
								getContent();
							}
						},
						error: function(xhr, ajaxOptions, thrownError) {
							console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});

				} else { $(document).find('.table-orders input[type*="checkbox"].minimal').iCheck("uncheck"); return false; }
				return false;
			}

			else if (action == 'send_mail' || action == 'send_sms' || action == 'send_tlgrm') {
				if (action == 'send_sms' && !confirm("<?php echo $text_confirm_send_sms; ?>")) {  return false; }

				if (action == 'send_mail') {
					var url = '<?php echo $send_mail; ?>';
				} else if (action == 'send_sms') {
					var url = '<?php echo $send_sms; ?>';
				} else if (action == 'send_tlgrm') {
					var url = '<?php echo $send_tlgrm; ?>';
				}

				url = url.replace(/&amp;/g, "&");

				if (!template_id && !order_row.length) {
					if (action == 'send_mail') {
						template_id = $(this).closest('.custom-notify-block').find('select[selectOptions="sendMailOptions"]').val();
					} else if (action == 'send_sms') {
						template_id = $(this).closest('.custom-notify-block').find('select[selectOptions="sendSmsOptions"]').val();
					} else if (action == 'send_tlgrm') {
						template_id = $(this).closest('.custom-notify-block').find('select[selectOptions="sendTlgrmOptions"]').val();
					}
				}

				var sendto = $(this).data('sendto');
				if (!sendto) {
					var sendto = $(this).closest('.custom-notify-block').find('.custom_notify_target').val();
				}

				var recipients = '';
				if (sendto == 'custom') {
					var recipients = $(this).closest('.custom-notify-block').find('.custom_notify_values').val();
					recipients = recipients.trim() !='' ? recipients.trim() : '';
					if (!recipients) {
						toastr.warning('Выберите, или укажите получателя!', '<?php echo $text_alert_warning; ?>'); return false;
					}
					url += '&recipients=' + encodeURIComponent(recipients);
				} else if (sendto == 'user') { // ???
					var user_id = $(this).data('userid');
					url += '&user_id=' + encodeURIComponent(user_id);
				}

				var data = {};
				data.selected = [];
				$('input[name^=\'selected\']:checked').each(function() {
					data.selected.push(this.value);
				});

				if (template_id && sendto) {
					var order_id = $(this).closest('.order-row').data('orderid');
					if (order_id) {
						var comment = encodeURIComponent($('textarea.order-notify-comment' + order_id).val());
					} else {
						var comment = encodeURIComponent($('textarea.custom-notify-comment').val());
					}
					data.comment = comment && comment !='undefined' && comment.trim() !='' ? comment : '';
					url += '&template_id=' + template_id + '&sendto=' + sendto;
					$.ajax({
						url: url,
						data: data,
						type: 'POST',
						dataType: 'json',
						beforeSend: function() {
							$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
						},
						success: function(json) {
							$('.text-loading').remove();
							if (json['error']) {
								toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
							}
							if (json['success']) {
								toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
							}
							if (order_id) {
								$('textarea.order-notify-comment' + order_id).val('');
							} else {
								$('textarea.custom-notify-comment').val('');
							}
						},
						error: function(xhr, ajaxOptions, thrownError) {
							console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				}
				return false;
			}

			// form submit
			else if (action == 'print_shipping') {
				var url = '<?php echo $print_shipping; ?>';
				url = url.replace(/&amp;/g, "&");
			}
			else if (action == 'print_invoice') {
				var url = '<?php echo $print_invoice; ?>';
				url = url.replace(/&amp;/g, "&");
			}
			else if (action == 'orderpro_invoice') {
				var url = '<?php echo $orderpro_invoice; ?>';
				url = url.replace(/&amp;/g, "&");
			}
			else if (action == 'orderpro_export_orders') {
				var url = '<?php echo $orderpro_export_orders; ?>';
				url = url.replace(/&amp;/g, "&");
			}
			else if (action == 'print_orders') {
				var url = '<?php echo $print_orders; ?>';
				url = url.replace(/&amp;/g, "&");

				if (!template_id && !order_row.length) {
					template_id = $(this).closest('.btn-group-input').find('select[selectOptions="printOrdersOptions"]').val();
				}
				if ( $(this).data('viewmode') == 'pdf') {
					url += '&return_type=view_pdf';
				}
				if (template_id) {
					url += '&template_id=' + template_id;
				}
			}
			else if (action == 'print_orders_table') {
				var url = '<?php echo $print_orders_table; ?>';
				url = url.replace(/&amp;/g, "&");

				if (!template_id && !order_row.length) {
					template_id = $(this).closest('.btn-group-input').find('select[selectOptions="printOrdersTableOptions"]').val();
				}
				if ( $(this).data('viewmode') == 'pdf') {
					url += '&return_type=view_pdf';
				}
				if (template_id) {
					url += '&template_id=' + template_id;
				}
			}
			else if (action == 'print_products_table') {
				var url = '<?php echo $print_products_table; ?>';
				url = url.replace(/&amp;/g, "&");

				if (!template_id && !order_row.length) {
					template_id = $(this).closest('.btn-group-input').find('select[selectOptions="printProductsTableOptions"]').val();
				}
				if ( $(this).data('viewmode') == 'pdf') {
					url += '&return_type=view_pdf';
				}
				if (template_id) {
					url += '&template_id=' + template_id;
				}
			}
			else if (action == 'excel_orders') {
				var url = '<?php echo $excel_orders; ?>';
				url = url.replace(/&amp;/g, "&");

				if (!template_id && !order_row.length) {
					template_id = $(this).closest('.btn-group-input').find('select[selectOptions="excelOrdersOptions"]').val();
				}

				if (template_id) {
					url += '&template_id=' + template_id;
				}
			}
			else if (action == 'excel_orders_products') {
				var url = '<?php echo $excel_orders_products; ?>';
				url = url.replace(/&amp;/g, "&");

				if (!template_id && !order_row.length) {
					template_id = $(this).closest('.btn-group-input').find('select[selectOptions="excelOrdersProductsOptions"]').val();
				}

				if (template_id) {
					url += '&template_id=' + template_id;
				}
			}

			if (order_row.length && table.length) {
				table.wrap('<form id="form-temp" target="_blank" method="post" enctype="multipart/form-data" />');
				table.closest('#form-temp').attr('action', url).submit();
				table.unwrap();
			} else {
				$('#form-editor').attr('action', url).attr('target', '_blank').submit();
			}
		}
	}

	else if (action == 'copytext_to_clipboard') {
		var copytext = document.createElement('textarea');
		var text = $($(this).attr('source-copy')).text();
		if (text.length) {
			copytext.value = text;
			document.body.appendChild(copytext);
			copytext.select();
			document.execCommand('copy');
			document.body.removeChild(copytext);
			toastr.success('Текст успешно скопирован в буфер обмена!', '<?php echo $text_alert_success; ?>');
		}
	}
});

$(document).delegate('.box-slide-toggle-btn', 'click', function() {
	$(this).closest('.box').find('>.box-body, >.box-footer').slideToggle();
	$(this).find('.fa').toggleClass('fa-minus fa-plus');
});

$(document).delegate('.order-history-pagination .pagination a', 'click', function(e) {
	e.preventDefault();
	var url = this.href;
	var target_id = $(this).closest('.order-history-pagination').data('targetid');
	var target = $(document).find('#'+target_id);
	if (url && target.length) {
		var tempElem = $('<div class="tempElem"></div>');
		tempElem.load(url, function() {
			target.replaceWith(tempElem.html());
		});
	}
});

$(document).delegate('.omanager-content [selectOptions="orderPageSelectOptions"]', 'change', function() {
	if (preventFunctionEditor()) { return false; }
	var href = '&pageid=' + $(this).val();
	getContent(href);
});

$(document).delegate('.sort-orders, .pagination-row ul.pagination > li > a', 'click', function() {
	if (preventFunctionEditor()) { return false; }
	var href = $(this).attr('data-href');
	var pageid = $('input[id=\'orders-pageid\']').val();
	if (pageid) {
		href += '&pageid=' + pageid;
	}
	getContent(href);
});

$(document).delegate('[filter_Input]', 'change', function() {
	if (!window.validation.isValid({ container: '#form-editor' })) { return false; }
	if (preventFunctionEditor()) { return false; }
	var filter = $(this);
	$('[name=\''+filter.attr('name')+'\']').val(filter.val());
	if (filter.attr("filterReload") == 1) { getContent(); }
});

$(document).delegate('.quick-filter-trigger', 'click', function() {
	var filter_id = $(this).data('filter_id');
	if (filter_id) {
		$('[filter_Input]').val('');
		var input_quick_filter = $(document).find('[input_quick_filter]');
		input_quick_filter.attr('id', filter_id).attr('name', filter_id).val($(this).data('filter_value')).trigger('change');
	}
});

--></script>

<!-- Batch history -->
<script type="text/javascript"><!--

function prepareBatch() {
	var create_invoiceno_status = '<?php echo $create_invoiceno_status; ?>';

	$(document).undelegate('input[name=\'batch_invoice\']', 'change');
	$(document).delegate('input[name=\'batch_invoice\']', 'change', function() {
		var input = $(this), btn = input.closest('.btn');
		if (btn.hasClass('active')) { input.prop('checked', true); }
		if (input.prop('checked') && !create_invoiceno_status) {
			input.prop('checked', false);
			if (btn) { btn.removeClass('active'); setTimeout(function() { btn.removeClass('focus'); }, 100 ); }
			toastr.options.preventDuplicates = true;
			toastr.warning('<?php echo $error_access_create_invoiceno; ?>', '<?php echo $text_alert_warning; ?>');
			return false;
		} else {
			if (btn) { setTimeout(function() { btn.removeClass('focus'); }, 100 ); }
		}
	});

	if  (create_invoiceno_status) { $('input[name=\'batch_invoice\']').trigger('change'); }

	var change_status_status = '<?php echo $change_status_status; ?>';

	$(document).undelegate('select[name=\'batch_order_status_id\']', 'change');
	$(document).delegate('select[name=\'batch_order_status_id\']', 'change', function() {
		var order_status_id = $(this).val();

		if (order_status_id && order_status_id != '*' && !change_status_status) {
			$('select[name=\'batch_order_status_id\']').val('*');
			toastr.options.preventDuplicates = true;
			toastr.warning('<?php echo $error_access_change_status; ?>', '<?php echo $text_alert_warning; ?>');
			return false;
		}

		if ($('input[name=\'batch_status_tpl\']').prop('checked')) {
			$.ajax({
				url: 'index.php?route=sale/ompro_helper/getStatusTemplate&<?php echo $strtoken; ?>&order_status_id=' + order_status_id,
				dataType: 'json',
				beforeSend: function() {
					$('textarea[name=\'batch_comment\']').val('<?php echo $text_loading; ?>');
				},
				success: function(json) {
					if (json['template']) {
						$('textarea[name=\'batch_comment\']').val(json['template']);
					} else {
						$('textarea[name=\'batch_comment\']').val('');
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});

	$(document).undelegate('.batch_custom_comment_tpl', 'change');
	$(document).delegate('.batch_custom_comment_tpl', 'change', function() {
		var template_id = $(this).val();

		if (template_id) {
			$.ajax({
				url: 'index.php?route=sale/ompro_helper/getCommentTemplate&<?php echo $strtoken; ?>&template_id=' + template_id,
				dataType: 'json',
				beforeSend: function() {
					$('textarea[name=\'batch_comment\']').val('<?php echo $text_loading; ?>');
				},
				success: function(json) {
					if (json['template']) {
						$('textarea[name=\'batch_comment\']').val(json['template']);
					} else {
						$('textarea[name=\'batch_comment\']').val('');
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		} else {
			$('textarea[name=\'batch_comment\']').val('');
		}
	});

	var edit_reward_status = '<?php echo $edit_reward_status; ?>';

	$(document).undelegate('input[name=\'batch_reward\']', 'change');
	$(document).delegate('input[name=\'batch_reward\']', 'change', function() {
		if ($(this).val() && !edit_reward_status) {
			$('input[name=\'batch_reward\']').each(function() {
				var input = $(this), btn = input.closest('.btn');
				if (input.val() == '0') {
					input.prop('checked', true); if (btn) { btn.addClass('active'); }
				} else {
					input.prop('checked', false);
					if (btn) { btn.removeClass('active'); setTimeout(function() { btn.removeClass('focus'); }, 100 ); }
				}
			});

			toastr.options.preventDuplicates = true;
			toastr.warning('<?php echo $error_access_edit_reward; ?>', '<?php echo $text_alert_warning; ?>');
			return false;
		}
	});

	if  (edit_reward_status) { $('input[name=\'batch_reward\']:checked').trigger('change'); }

	var edit_comission_status = '<?php echo $edit_comission_status; ?>';

	$(document).undelegate('input[name=\'batch_commission\']', 'change');
	$(document).delegate('input[name=\'batch_commission\']', 'change', function() {
		if ($(this).val() && !edit_comission_status) {
			$('input[name=\'batch_commission\']').each(function() {
				var input = $(this), btn = input.closest('.btn');
				if (input.val() == '0') {
					input.prop('checked', true); if (btn) { btn.addClass('active'); }
				} else {
					input.prop('checked', false);
					if (btn) { btn.removeClass('active'); setTimeout(function() { btn.removeClass('focus'); }, 100 ); }
				}
			});

			toastr.options.preventDuplicates = true;
			toastr.warning('<?php echo $error_access_edit_comission; ?>', '<?php echo $text_alert_warning; ?>');
			return false;
		}
	});

	if  (edit_comission_status) { $('input[name=\'batch_commission\']:checked').trigger('change'); }
}

function batch(){
	var selected = $('input[name^=\'selected\']:checked');
	var data = {};
	data.user_id = '<?php echo $user_id; ?>';
	data.order_status_id = $('select[name=\'batch_order_status_id\']').val();
	data.notify_mail = $('input[name=\'batch_notify\']').prop('checked') ? 1 : 0;
	data.notify_sms = $('input[name=\'batch_notify_sms\']').prop('checked') ? 1 : 0;
	data.notify_tlgrm = $('input[name=\'batch_notify_tlgrm\']').prop('checked') ? 1 : 0;
	data.override = 1;
	data.comment = encodeURIComponent($('textarea[name=\'batch_comment\']').val());

	data.selected =[];
	selected.each(function(){
		data.selected.push($(this).val());
	});

	data.invoice = $('input[name=\'batch_invoice\']').prop('checked') ? 1 : 0;
	var reward = $('input[name=\'batch_reward\']:checked').val();
	data.reward = reward ? reward : 0;
	var commission = $('input[name=\'batch_commission\']:checked').val();
	data.transaction = commission ? commission : 0;

	if (data.order_status_id == '*' && data.invoice == 0 && data.reward == 0 && data.transaction == 0) {
		toastr.warning('<?php echo $text_action_not_select; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	if (data.order_status_id != '*' ) {
		$.ajax({
			url: '<?php echo $catalog; ?>index.php?route=api/ompro/history',
			type: 'post',
			dataType: 'json',
			crossDomain: true,
			data: data,
			beforeSend: function() {
				$('[data-btnaction="apply_batch"]').button('loading');
			},
			complete: function() {
				$('[data-btnaction="apply_batch"]').button('reset');
			},
			success: function(json) {
				var error = '';
				if (json['error']) { error = json['error']; }

				if (error && !json['success']) {
					toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
				}

				if (json['success']) {
					if (data.invoice == 0 && data.reward == 0 && data.transaction == 0) {
						getContent(href = '', page = '',  error, warning = '', json['success']);
					} else {
						applyBatch(data, error, warning = '', json['success']);
					}
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	} else if (data.invoice || data.reward || data.transaction) {
		applyBatch(data);
	}
}

function applyBatch(data, error = '', warning = '', success = '') {
	$.ajax({
		url: 'index.php?route=sale/ompro/batch&<?php echo $strtoken; ?>',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$('[data-btnaction="apply_batch"]').button('loading');
		},
		complete: function() {
			$('[data-btnaction="apply_batch"]').button('reset');
		},
		success: function(json) {
			toastr.options.timeOut = 0;

			if (json['error']) { error += json['error']; }
			if (json['warning']) { warning += json['warning']; }
			if (json['success']) { success += json['success']; }

			if (success) {
				getContent(href = '', page = '',  error, warning, success);
				return false;
			} else {
				if (error) { toastr.error(error, '<?php echo $text_alert_error; ?>'); }
				if (warning) { toastr.warning(warning, '<?php echo $text_alert_warning; ?>'); }
			}

			toastr.options.timeOut = 5000;
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

// Add history
$(document).delegate('.order_history_btn', 'click', function() {
	if (preventFunctionEditor()) { return false; }
	var change_status_status = '<?php echo $change_status_status; ?>';

	if (!change_status_status) {
		toastr.options.preventDuplicates = true;
		toastr.warning('<?php echo $error_access_change_status; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	var order_history_btn = $(this);
	var order_id = order_history_btn.data('orderid');
	var ceil = order_history_btn.closest('td');

	if (!$(this).closest('.modal-dialog').length) {
		var order_row = order_history_btn.closest('.order-row');
		if (order_row.length) {
			var find_class = 'must-be-find-this' + order_id;
			order_history_btn.addClass(find_class);
			order_row.find('> td').each(function(){
				if ($(this).find('.'+find_class).length) {
					var ceil = $(this);
				}
			});
			order_history_btn.removeClass(find_class);
		}
	}

	var data = {};
	data.user_id = '<?php echo $user_id; ?>';
	data.selected =[];
	data.selected.push(order_id);

	var comment_only =  false;

	if (order_history_btn.hasClass('direct-chat-history-btn')) {
		var chat = order_history_btn.closest('.direct-chat');
		comment_only =  true;
		data.order_status_id = order_history_btn.attr('data-orderstatusid');
		data.notify_mail = 0;
		data.notify_sms = 0;
		data.notify_tlgrm = 0;
		data.override = 1;
		var comment = chat.find('.direct_chat_comment_' + order_id).val();
		if (!comment.trim().length) {
			toastr.warning('Введите сообщение!', '<?php echo $text_alert_warning; ?>'); return false;
		} else {
			data.comment = encodeURIComponent(comment);
		}
	} else {
		if (ceil.length) {
			data.order_status_id = ceil.find('select[name=\'order_status_id' + order_id + '\']').val();
			data.notify_mail = ceil.find('#order_notify_email_' + order_id).prop('checked') ? 1 : 0;
			data.notify_sms = ceil.find('#order_notify_sms_' + order_id).prop('checked') ? 1 : 0;
			data.notify_tlgrm = ceil.find('#order_notify_tlgrm_' + order_id).prop('checked') ? 1 : 0;
			data.override = ceil.find('#order_override' + order_id).prop('checked') ? 1 : 0;
			var comment = ceil.find('textarea[name=\'comment' + order_id + '\']').val();
			data.comment = comment.trim().length ? encodeURIComponent(comment) : '';
			var filename = ceil.find('input[id=\'filename_' + order_id +'\']').val();
			if (filename) { data.file_name = filename; }
		} else {
			data.order_status_id = $('select[name=\'order_status_id' + order_id + '\']').val();
			data.notify_mail = $('#order_notify_email_' + order_id).prop('checked') ? 1 : 0;
			data.notify_sms = $('#order_notify_sms_' + order_id).prop('checked') ? 1 : 0;
			data.notify_tlgrm = $('#order_notify_tlgrm_' + order_id).prop('checked') ? 1 : 0;
			data.override = $('#order_override' + order_id).prop('checked') ? 1 : 0;
			var comment = $('textarea[name=\'comment' + order_id + '\']').val();
			data.comment = comment.trim().length ? encodeURIComponent(comment) : '';
			var filename = $('input[id=\'filename_' + order_id +'\']').val();
			if (filename) { data.file_name = filename; }
		}
	}

	$.ajax({
		url: '<?php echo $catalog; ?>index.php?route=api/ompro/history',
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			toastr.options.timeOut = 10000;
			var error, warning, success;
			if (json['error']) { error = json['error']; }
			if (json['warning']) { warning = json['warning']; }

			if (comment_only) {
				success = 'Комментарий успешно добавлен в историю заказа №' + order_id;
			} else {
				if (json['success']) { success = json['success']; }
			}

			if (success) {
				if ($('.history-template-page').length) {
					$('.history-template-page #btn-preview').trigger('click');
				} else {
					if (order_history_btn.closest('.order-row').length) {
						$(document).find('.table-orders').each(function() {
							var table = $(this); var table_id = table.attr('id');
							if (order_id && table_id) { orderReload(order_id, table_id); }
						});
					}
					if (order_history_btn.closest('.modal-dialog').length) {
						var history_view_btn = order_history_btn.closest('.modal-dialog').find('[data-btnaction="order_history_view"]');
						var url = '<?php echo $order_histories; ?>';
						url = url.replace(/&amp;/g, "&");
						if (order_id) { url += '&order_id=' + order_id; }
						var template_id = history_view_btn.data('templateid');
						if (template_id) { url += '&history_template_id=' + template_id; }
						var target_id = history_view_btn.data('targetid');
						if (order_id && target_id) {
							var tempElem = $('<div class="tempElem"></div>');
							tempElem.load(url, function() {
								if (target_id !== 'modal-mode') {
									$('#'+target_id).replaceWith(tempElem.html());
									refreshPlugForOrders(); return false;
								}
							});
						}
					}

					toastr.success(success, '<?php echo $text_alert_success; ?>');
				}
			} else {
				if (error) { toastr.error(error, '<?php echo $text_alert_error; ?>'); }
				if (warning) { toastr.warning(warning, '<?php echo $text_alert_warning; ?>'); }
			}
			toastr.options.timeOut = 5000;
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	return false;
});

$(document).delegate('.order_history_status', 'change', function() {
	var status_selector = $(this);
	var order_row = status_selector.closest('.order-row');
	var order_id = status_selector.attr('data-orderid');

	var ceil = status_selector.closest('td');
	var find_class = 'must-be-find-this' + order_id;
	status_selector.addClass(find_class);
	order_row.find('> td').each(function(){
		if ($(this).find('.'+find_class).length) {
			var ceil = $(this);
		}
	});
	status_selector.removeClass(find_class);

	var auto_comment = 1;
	if (ceil.find('#status_auto_comment_' + order_id).length) {
		auto_comment = ceil.find('#status_auto_comment_' + order_id).prop('checked') ? 1 : 0;
	}

	if (auto_comment) {
		var order_status_id = status_selector.val();
		$.ajax({
			url: 'index.php?route=sale/ompro_helper/getStatusTemplate&<?php echo $strtoken; ?>&order_id=' + order_id + '&order_status_id=' + order_status_id,
			dataType: 'json',
			beforeSend: function() {
				ceil.find('textarea[name=\'comment' + order_id + '\']').val('<?php echo $text_loading; ?>');
			},
			success: function(json) {
				if (json['template']) {
					ceil.find('textarea[name=\'comment' + order_id + '\']').val(json['template']);
				} else {
					ceil.find('textarea[name=\'comment' + order_id + '\']').val('');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
});

$(document).delegate('.custom_comment_tpl', 'change', function() {
	var comment_selector = $(this);
	var url = 'index.php?route=sale/ompro_helper/getCommentTemplate&<?php echo $strtoken; ?>';
	var order_id = comment_selector.attr('data-orderid');
	if (order_id) { url += '&order_id=' + order_id; }
	var template_id = comment_selector.val();
	if (template_id) { url += '&template_id=' + template_id; }

	if (order_id > 0)  {
		var ceil = comment_selector.closest('td');
		var find_class = 'must-be-find-this' + order_id;
		comment_selector.addClass(find_class);
		var order_row = comment_selector.closest('.order-row');
		order_row.find('> td').each(function(){
			if ($(this).find('.'+find_class).length) {
				var ceil = $(this);
			}
		});
		comment_selector.removeClass(find_class);
		if (comment_selector.hasClass('order-notify')) {
			var target = ceil.find('textarea.order-notify-comment' + order_id);
		} else {
			var target = ceil.find('textarea[name=\'comment' + order_id + '\']');
		}
	} else {
		var target = $(document).find('textarea.custom-notify-comment');
	}

	if (template_id) {
		$.ajax({
			url: url,
			dataType: 'json',
			beforeSend: function() {
				target.val('<?php echo $text_loading; ?>');
			},
			success: function(json) {
				if (json['template']) {
					target.val(json['template']);
				} else {
					target.val('');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	} else {
		target.val('');
	}
});

//	create invoice
$(document).delegate('.create-invoice', 'click', function() {
	var create_invoiceno_status = '<?php echo $create_invoiceno_status; ?>';
	if (!create_invoiceno_status) {
		toastr.options.preventDuplicates = true;
		toastr.warning('<?php echo $error_access_create_invoiceno; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	var order_id = $(this).attr('data-orderid');

	$.ajax({
		url: 'index.php?route=sale/ompro_helper/createInvoiceNo&<?php echo $strtoken; ?>&order_id=' + order_id,
		dataType: 'json',
		beforeSend: function() {
			$('#button-invoice' + order_id).button('loading');
		},
		complete: function() {
			$('#button-invoice' + order_id).button('reset');
		},
		success: function(json) {
			if (json['error']) {
				toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
			}

			if (json['invoice_no']) {
				$('#invoice'+ order_id).html(json['invoice_no']);

				$('#button-invoice' + order_id).replaceWith('<button disabled="disabled" class="btn btn-success btn-xs"><i class="fa fa-cog"></i></button>');

				tooltipRefresh();
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	return false;
});

//	reward-present
$(document).delegate('.present-reward', 'click', function() {
	var btn = $(this);
	var url = 'index.php?route=sale/ompro_helper/presentReward&<?php echo $strtoken; ?>';
	var order_id = btn.closest('.order-row').data('orderid');
	if (order_id) { url += '&order_id=' + order_id; }

	var input_points = btn.closest('.present-reward-group').find('input.present-reward-points');
	var input_description = btn.closest('.present-reward-group').find('input.present-reward-description');
	var points = input_points.val();
	var description = input_description.val();

	var edit_reward_status = '<?php echo $edit_reward_status; ?>';
	toastr.options.preventDuplicates = true;
	if (!edit_reward_status) {
		toastr.warning('<?php echo $error_access_edit_reward; ?>', '<?php echo $text_alert_warning; ?>');
		input_points.val(''); input_description.val(''); return false;
	}

	var customer_id = btn.attr('data-customer_id');
	if (customer_id < 1) {
		toastr.warning('<?php echo $text_error_register_customer; ?>', '<?php echo $text_alert_warning; ?>');
		input_points.val(''); input_description.val(''); return false;
	}

	if (points ==  '') {
		toastr.warning('<?php echo $text_error_empty_points; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	if (!confirm("<?php echo $text_confirm_reward_present; ?>" + points + '?!')) {
		input_points.val(''); input_description.val(''); return false;
	}

	if (customer_id) { url += '&customer_id=' + customer_id; }

	$.ajax({
		url: url,
		type: 'POST',
		data: 'description=' + encodeURIComponent(description) + '&points=' + encodeURIComponent(points),
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			input_points.val(''); input_description.val('');
			if (json['error']) {
				toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
			}

			if (json['success']) {
				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	return false;
});

//	credit-present
$(document).delegate('.present-credit', 'click', function() {
	var btn = $(this);
	var input_amount = btn.closest('.present-credit-group').find('input.present-credit-amount');
	var input_description = btn.closest('.present-credit-group').find('input.present-credit-description');
	var amount = input_amount.val();
	var description = input_description.val();

	var edit_reward_credit_status = '<?php echo $edit_reward_status; ?>';
	toastr.options.preventDuplicates = true;
	if (!edit_reward_credit_status) {
		toastr.warning('<?php echo $error_access_edit_reward; ?>', '<?php echo $text_alert_warning; ?>');
		input_amount.val(''); input_description.val(''); return false;
	}

	var customer_id = btn.attr('data-customer_id');
	if (customer_id < 1) {
		toastr.warning('<?php echo $text_error_register_customer; ?>', '<?php echo $text_alert_warning; ?>');
		input_amount.val(''); input_description.val(''); return false;
	}

	if (amount ==  '') {
		toastr.warning('<?php echo $text_error_empty_credit_amount; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	if (!confirm("<?php echo $text_confirm_credit_present; ?>" + amount + '?!')) {
		input_amount.val(''); input_description.val(''); return false;
	}

	$.ajax({
		url: 'index.php?route=sale/ompro_helper/addCredit&<?php echo $strtoken; ?>&customer_id=' + customer_id,
		type: 'POST',
		data: 'description=' + encodeURIComponent(description) + '&amount=' + encodeURIComponent(amount),
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			input_amount.val(''); input_description.val('');
			if (json['error']) {
				toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
			}

			if (json['success']) {
				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	return false;
});

//	reward-add
$(document).delegate('.reward-add', 'click', function() {
	var edit_reward_status = '<?php echo $edit_reward_status; ?>';
	toastr.options.preventDuplicates = true;
	if (!edit_reward_status) {
		toastr.warning('<?php echo $error_access_edit_reward; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	var order_id = $(this).attr('data-orderid');
	$.ajax({
		url: 'index.php?route=sale/ompro_helper/addReward&<?php echo $strtoken; ?>&order_id=' + order_id,
		dataType: 'json',
		beforeSend: function() {
			$('#button-reward-add' + order_id).button('loading');
		},
		complete: function() {
			$('#button-reward-add' + order_id).button('reset');
		},
		success: function(json) {
			if (json['error']) {
				toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
			}

			if (json['success']) {
				$('#button-reward-add' + order_id).replaceWith('<button id="button-reward-remove' + order_id + '" data-loading-text="..." data-orderid="' + order_id + '" data-toggle="tooltip" title="<?php echo $button_reward_remove; ?>" class="btn btn-danger btn-xs reward-remove"><i class="fa fa-minus-circle"></i></button>');

				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
				tooltipRefresh();
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	return false;
});

// reward-remove
$(document).delegate('.reward-remove', 'click', function() {
	var edit_reward_status = '<?php echo $edit_reward_status; ?>';
	toastr.options.preventDuplicates = true;
	if (!edit_reward_status) {
		toastr.warning('<?php echo $error_access_edit_reward; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	var order_id = $(this).attr('data-orderid');
	$.ajax({
		url: 'index.php?route=sale/ompro_helper/removeReward&<?php echo $strtoken; ?>&order_id=' + order_id,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#button-reward-remove' + order_id).button('loading');
		},
		complete: function() {
			$('#button-reward-remove' + order_id).button('reset');
		},
		success: function(json) {
			if (json['error']) {
				toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
			}

			if (json['success']) {
				$('#button-reward-remove' + order_id).replaceWith('<button id="button-reward-add' + order_id + '" data-loading-text="..." data-orderid="' + order_id + '" data-toggle="tooltip" title="<?php echo $button_reward_add; ?>" class="btn btn-success btn-xs reward-add"><i class="fa fa-plus-circle"></i></button>');

				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
				tooltipRefresh();
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	return false;
});

//	commission-add
$(document).delegate('.commission-add', 'click', function() {
	var edit_comission_status = '<?php echo $edit_comission_status; ?>';
	toastr.options.preventDuplicates = true;
	if (!edit_comission_status) {
		toastr.warning('<?php echo $error_access_edit_comission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	var order_id = $(this).attr('data-orderid');
	$.ajax({
		url: 'index.php?route=sale/ompro_helper/addCommission&<?php echo $strtoken; ?>&order_id=' + order_id,
		dataType: 'json',
		beforeSend: function() {
			$('#button-commission-add' + order_id).button('loading');
		},
		complete: function() {
			$('#button-commission-add' + order_id).button('reset');
		},
		success: function(json) {
			if (json['error']) {
				toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
			}

			if (json['success']) {
				$('#button-commission-add' + order_id).replaceWith('<button id="button-commission-remove' + order_id + '" data-loading-text="..." data-orderid="' + order_id + '" data-toggle="tooltip" title="<?php echo $button_commission_remove; ?>" class="btn btn-danger btn-xs commission-remove"><i class="fa fa-minus-circle"></i></button>');

				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
				tooltipRefresh();
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	return false;
});

// commission-remove
$(document).delegate('.commission-remove', 'click', function() {
	var edit_comission_status = '<?php echo $edit_comission_status; ?>';
	toastr.options.preventDuplicates = true;
	if (!edit_comission_status) {
		toastr.warning('<?php echo $error_access_edit_comission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	var order_id = $(this).attr('data-orderid');
	$.ajax({
		url: 'index.php?route=sale/ompro_helper/removeCommission&<?php echo $strtoken; ?>&order_id=' + order_id,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#button-commission-remove' + order_id).button('loading');
		},
		complete: function() {
			$('#button-commission-remove' + order_id).button('reset');
		},
		success: function(json) {
			if (json['error']) {
				toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
			}

			if (json['success']) {
				$('#button-commission-remove' + order_id).replaceWith('<button id="button-commission-add' + order_id + '" data-loading-text="..." data-orderid="' + order_id + '" data-toggle="tooltip" title="<?php echo $button_commission_add; ?>" class="btn btn-success btn-xs commission-add"><i class="fa fa-plus-circle"></i></button>');

				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
				tooltipRefresh();
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	return false;
});

// file-upload
$(document).delegate('.button-upload', 'click', function() {
	var order_id = $(this).data('orderid');

	$('#form-upload_' + order_id).remove();
	$('body').prepend('<form enctype="multipart/form-data" id="form-upload_' + order_id + '" style="display: none;"><input type="file" name="file" id="inputfile_' + order_id + '" /></form>');
	$('#form-upload_' + order_id + ' input[id=\'inputfile_' + order_id + '\']').trigger('click');

	if (typeof timer != 'undefined') {
		clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload_' + order_id + ' input[id=\'inputfile_' + order_id + '\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=sale/ompro_helper/upload&<?php echo $strtoken; ?>&upload_order=1&order_id=' + order_id,
				type: 'post', dataType: 'json',
				data: new FormData($('#form-upload_' + order_id )[0]),
				cache: false, contentType: false, processData: false,
				beforeSend: function() {
					$('#button-upload' + order_id).button('loading');
				},
				complete: function() {
					$('#button-upload' + order_id).button('reset');
				},
				success: function(json) {
					if (json['error']) {
						toastr.error(json['error'], '<?php echo $text_alert_error; ?>');
					}

					if (json['success']) {
						toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
						$('input[id=\'filename_' + order_id +'\']').val(json['filename']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});
--></script>

<script type="text/javascript"><!--

function modalAlert(type='', title='', content='', footer='', sizeclass='') {
//	sizeclass='modal-sm modal-lg modal-xl'
	if (!footer) {
		if (!type || type == 'modal-default') {
			footer = '<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo $text_close; ?></button>';
		} else {
			footer = '<button type="button" class="btn btn-outline pull-left" data-dismiss="modal"><?php echo $text_close; ?></button>';
		}
	}
	$('#modal .modal-title').html(title);
	$('#modal .modal-body').html(content);
	if (footer) { $('#modal .modal-footer').html(footer); }
	$('#modal .modal-dialog').attr('class', 'modal-dialog ' + sizeclass);
	$('#modal').attr('class', 'modal fade ' + type).modal();
}

$(document).delegate('[pp_trigger]', 'click', function() {
	var order_id = $(this).attr('data-orderID');
	var template_id = $(this).attr('data-ppTemplateID');

	if (order_id && template_id) {
		$.ajax({
			url: 'index.php?route=sale/ompro/getOrderProductsTable&<?php echo $strtoken; ?>&order_id=' + order_id+ '&template_id=' + template_id + '&to_edit=true',
			dataType: 'json',
			beforeSend: function() {
				$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
			},
			success: function(json) {
				$('.text-loading').remove();
				if (json['error']) {
					modalAlert('modal-default', json['title'], '<p>'+json['error']+'</p>', '', 'modal-lg');
				} else {
					modalAlert('modal-default', json['title'], '<p>'+json['content']+'</p>', '', 'modal-lg');
					xEditableStart();
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
});

function getTableCodes(table) {
	$.ajax({
		url: 'index.php?route=sale/ompro_helper/getTableCodes&<?php echo $strtoken; ?>&table=' + table,
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['body']) {
				modalAlert('modal-default', json['title'], '<p>'+json['body']+'</p>', json['footer'], 'modal-lg');
				tooltipRefresh();
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

$(document).ajaxStart(function() {
	Pace.restart();
});

//--></script>


<!-- Compatibility -->

<!-- OrderPro -->
<?php if ($orderpro_merge_orders_btn) { ?>
<div id="merge_block"></div>
<style type="text/css" title="ompro">
#merge_form .alert {text-align:center;font-size:13px;}
</style>
<script type="text/javascript">
$(document).delegate('#old_order', 'change', function() {
	if ($(this).val() == 1) {
		$('#merge_form .old-status').slideDown('slow');
	} else {
		$('#merge_form .old-status').slideUp('slow');
	}
});

function getmerge() {
	var url = '<?php echo $orderpro_get_merge; ?>';
	url = url.replace(/&amp;/g, "&");
	var selected = $('input[name^=\'selected\']:checked');

	if (!selected.length) {
		modalAlert('modal-warning', '<?php echo $text_alert_warning; ?>', '<p><?php echo $text_alert_not_selected; ?></p>', '');
		return false;
	}

	$('#merge_modal').remove();
    $.ajax({
		url: url,
		type: 'post',
		dataType: 'json',
		data: selected,
		beforeSend: function() {
			$('.alert').remove();
			$('#button-merge').children('.fa').addClass('fa-cog fa-spin');
		},
        success: function(json) {
			if (json['error']) {
				$('#button-merge').children('.fa').removeClass('fa-cog fa-spin');
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
			}
			if (json['html']) {
				$('#merge_block').html(json['html']);
				setTimeout (function() {
					$('#merge_modal').modal('show');
					$('#button-merge').children('.fa').removeClass('fa-cog fa-spin');
				}, 800);
			}
        },
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function merge_order() {
	var url = '<?php echo $orderpro_merge_orders; ?>';
	url = url.replace(/&amp;/g, "&");
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: $('#merge_form :input'),
		beforeSend: function() {
			$('#merge_modal .btn-success').remove();
		},
        success: function(json) {
			if (json['error']) {
				$('#merge_form').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' </div>');
			}
			if (json['success']) {
				$('#merge_form').prepend('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' </div>');
			}
        },
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
</script>
<!-- // for OrderPro -->
<?php } ?>

<!-- novaposhta, ukrposhta -->
<?php if ($nova_or_ukrposhta_list_btn) { ?>

<!-- START Shipping Data -->
<style type="text/css" title="ompro">
	.btn-novaposhta {
		color: #fff;
		background-color: #ff392e;
		border-color: #731610;
	}
	.btn-light-novaposhta {
		color: #333;
		background-color: #fff;
		border-color: #ff392e;
	}
	.btn-ukrposhta {
		color: #333;
		background-color: #ffce2f;
		border-color: #ccc;
	}
	.btn-light-ukrposhta {
		color: #333;
		background-color: #fff;
		border-color: #ffce2f;
	}
	.btn-justin {
		color: #fff;
		background-color: #104eff;
		border-color: #053bd4;
	}
	.btn-light-justin {
		color: #333;
		background-color: #fff;
		border-color: #104eff;
	}
</style>

<!-- START Modal assignment CN to order -->
<div class="modal fade" id="assignment-cn-to-order" tabindex="-1" role="dialog" aria-labelledby="assignment-cn-to-order-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="assignment-cn-to-order-label"><?php echo $heading_cn; ?></h4>
			</div>
			<div class="modal-body">
				<div class="form-group clearfix">
					<input type="hidden" name="cn_order_id" value="" id="cn_order_id" />
					<input type="hidden" name="cn_shipping_method" value="" id="cn_shipping_method" />
					<label class="col-sm-2 control-label" for="cn_number"><?php echo $entry_cn_number; ?></label>
					<div class="col-sm-10">
						<input type="text" name="cn_number" value="" placeholder="<?php echo $entry_cn_number; ?>" id="cn_number" class="form-control" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="assignmentCN();"><i class="fa fa-check"></i></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
			</div>
		</div>
	</div>
</div>
<!-- END Modal assignment CN to order -->

<script type="text/javascript"><!--
	function deleteCN(self, shipping_method) {
		var order_id = $(self).closest('.order-row').data('orderid');
		var post_data = 'order_id=' + order_id;

		$.ajax({
			url: 'index.php?route=extension/shipping/' + shipping_method + '/deleteCNFromOrder&<?php echo $strtoken; ?>',
			type: 'POST',
			data: post_data,
			dataType: 'json',
			beforeSend: function () {
				$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
			},
			success: function(json) {
				$('.text-loading').remove();
				if (json['error']) {
					modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
				}
				if (json['success']) {
					toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
					$(document).find('.table-orders').each(function() {
						var table = $(this); var table_id = table.attr('id');
						if (order_id && table_id) { orderReload(order_id, table_id); }
					});
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(textStatus);
			}
		});
	}

	function assignmentCN(self, shipping_method) {
		if (typeof(self) !== 'undefined') {
			$('#cn_order_id').val($(self).closest('.order-row').data('orderid'));
		}

		if (shipping_method) {
			$('#cn_shipping_method').val(shipping_method);
		}

		if ($('#assignment-cn-to-order').is(':hidden')) {
			$('#assignment-cn-to-order').modal('show');
		} else {
			var order_id = $('#cn_order_id').val();
			var post_data = 'order_id=' + order_id + '&cn_number=' + $('#cn_number').val();

			$.ajax({
				url: 'index.php?route=extension/shipping/' + $('#cn_shipping_method').val() + '/addCNToOrder&<?php echo $strtoken; ?>',
				type: 'POST',
				data: post_data,
				dataType: 'json',
				beforeSend: function () {
					$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
				},
				success: function(json) {
					$('.text-loading').remove();
					if (json['error']) {
						modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
					}
					if (json['success']) {
						toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
						$(document).find('.table-orders').each(function() {
							var table = $(this); var table_id = table.attr('id');
							if (order_id && table_id) { orderReload(order_id, table_id); }
						});
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(textStatus);
				}
			});

			$('#assignment-cn-to-order').modal('hide');
		}
	}

//--></script>
<!-- END Shipping Data -->
<?php } ?>
</body>
</html>