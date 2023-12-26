<?php

//  Live Price / Живая цена
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

// Heading
$_['heading_title']       = 'LIVEOPENCART: '.$_['module_name'];
$_['text_edit']           = 'Настройки модуля: '.$_['module_name'];

// Text
$_['text_module']        			 = 'Модули';
$_['text_success']       			 = 'Модуль "'.$_['heading_title'].'" успешно обновлен!';
$_['text_content_top']    			 = 'Верх страницы';
$_['text_content_bottom']			 = 'Низ страницы';
$_['text_column_left']   			 = 'Левая колонка';
$_['text_column_right']   			 = 'Правая колонка';
$_['text_category_all']   			 = '-- все категории --';
$_['text_manufacturer_all'] 		 = '-- все производители --';
$_['text_filter_all']   			 = '-- все фильтры --';
$_['liveprice_all_customers_groups'] = '-- все группы --';

$_['text_edit_position'] = 'Изменить размещение';

// VALUES
$_['text_value_enabled']           				  	  = 'Включено';
$_['text_value_disabled']          				      = 'Отключено';
$_['text_value_starting_from_required'] 			  = 'Для обязательных опций';
$_['text_value_starting_from_all']   				  = 'Для всех опций';
$_['text_value_show_from_min']       				  = 'Для минимальных цен';
$_['text_value_show_from_all']         				  = 'Для всех товаров';
$_['text_value_show_from_with_options'] 			  = 'Для товаров с выбирамыми опциями (Переключатель, Список, Флажок)';
$_['text_value_show_from_with_option_prices']		  = 'Для товаров с выбирамыми опциями имеющими цены';
$_['text_show_from_current_enabled_until_no_options'] = 'Включено (пока не выбрана хотя бы одна опция)';

// Entry
$_['entry_filter']   	  				 = 'Фильтр';
$_['entry_sort_order']   				 = 'Порядок сортировки:';
$_['entry_discount_quantity'] 			 = 'Количество для скидок:';
$_['text_discount_quantity_0']			 = 'всего по товару';
$_['text_discount_quantity_1'] 			 = 'отдельно для каждой комбинации опций';
$_['text_discount_quantity_2'] 			 = 'отдельно для каждой комбинации связанных опций';
$_['entry_discount_quantity_spec']		 = 'Количество для скидок:';
$_['entry_multiplied_price'] 			 = 'Показывать цену умноженной на количество:';
$_['entry_about'] 						 = 'О модуле';
$_['entry_settings'] 					 = 'Настройки';
$_['entry_layout']       				 = 'Схема:';
$_['entry_position']     				 = 'Расположение:';
$_['entry_status']        				 = 'Статус:';
$_['entry_discounts'] 					 = 'Глобальные скидки';
$_['text_discounts_description'] 		 = 'Глобальные скидки применяются только для товаров у которых нет собственных скидок (когда список скидок товара пуст). Условие категории работает только для товаров напрямую привязанных к выбранной категории.';
$_['entry_specials'] 					 = 'Глобальные акции';
$_['text_specials_description'] 		 = 'Глобальные акции применяются только для товаров у которых нет собственных акций (когда список акций товара пуст). Условие категории работает только для товаров напрямую привязанных к выбранной категории.';
$_['entry_customize_discounts']    		 = 'Количество для скидок (настройки товаров)';
$_['entry_add_customize_discounts']      = 'Добавить настройку количества для скидки';
$_['entry_ropro_discounts_addition'] 	 = 'Применять префиксы цен<br> к скидкам связанных опций:';
$_['text_ropro_discounts_addition_help'] = 'Применять префиксы цен указанные для комбинаций связанных опций к скидкам, аналогично тому как эта функция работает для цен товаров (= + -)';
$_['entry_ropro_specials_addition'] 	 = 'Применять префиксы цен<br> к акциям связанных опций:';
$_['text_ropro_specials_addition_help']  = 'Применять префиксы цен указанные для комбинаций связанных опций к акциям, аналогично тому как эта функция работает для цен товаров (= + -)';

$_['entry_manufacturers_spec'] = 'Производители';
$_['entry_categories_spec']    = 'Категории';
$_['entry_products_spec'] 	   = 'Товары';

$_['entry_percent_discount_to_total'] 			 = 'Применять процентные скидки к цене с опциями';
$_['entry_entry_percent_discount_to_total_help'] = 'Применять процентные скидки к полной цене товара с учетом опций';

$_['entry_show_calculated_percentage_discounts'] 		= 'Рассчитывать фактические скидки';
$_['entry_show_calculated_percentage_discounts_help'] 	= 'Рассчитывать фактические значения цен для процентных скидок (иначе - показывается сам процент скидки)';

$_['entry_percent_special_to_total'] 			= 'Применять процентные акции к цене с опциями';
$_['entry_entry_percent_special_to_total_help'] = 'Применять процентные акции к полной цене товара с учетом опций';

$_['entry_default_price']      = 'Показывать цены с учетом опций "по умолчанию"';
$_['entry_default_price_help'] = 'Показывать цены в списках товаров (категории, производители, новинки, рекомендации и т.д.) с учетом значений опций "по умолчанию" (требуется модуль "Расширенные опции 3")';
$_['entry_default_price_mods'] = 'Значения опций "по умолчанию" должны быть указаны с помощью модуля <a href="https://liveopencart.ru/opencart-moduli-shablony/moduli/opcii/rasshirennyie-optsii-3" target="_blank">Расширенные опции 3</a>.';

$_['entry_starting_from']      = 'Минимально возможные цены в списках';
$_['entry_starting_from_help'] = 'Показывать в категориях, модулях и прочих списках товаров минимально возможные цены товаров с учетом опций (учитываются акции, но не скидки от количества)';

$_['entry_show_from']          = 'Префикс "от" в списках товаров';
$_['entry_show_from_help']     = 'Добавлять префикс "от" ценам в списках товаров (категории, производители, новинки, рекомендации и т.д.)';

$_['entry_starting_from_current']      = 'Минимальная цена на странице товара';
$_['entry_starting_from_help'] 		   = 'Показывать на странице товара минимально возможную цену с учетом выбранных опций (учитываются акции, но не скидки от количества)';

$_['entry_show_from_current']          = 'Префикс "от" на странице товара';
$_['entry_show_from_current_help']     = 'Добавлять префикс "от" при отображении минимально возможной с учетом выбранных опций цены на странице товара';

$_['entry_discount_like_special']          = 'Показывать скидки в стиле акций';
$_['entry_discount_like_special_help']     = 'Отображать доступную/используемую скидку на странице товара используя оформление акций';

$_['entry_ignore_cart']      = 'Не учитывать корзину';
$_['entry_ignore_cart_help'] = 'Отключить учет количества товара уже добавленного в корзину при расчете цены на странице товара';

$_['entry_ignore_greater_special']					= 'Скрывать акции выше цен';
$_['entry_ignore_greater_special_help'] 			= 'Не отображать акционую цену, если цена по акции выше обычной цены';

$_['entry_hide_tax']      		= 'Скрывать налог';
$_['entry_hide_tax_help']		= 'Скрывать налог при обновлении цены на странице товара';

$_['entry_calculate_once']      = 'Живая цена: Учитывать однократно';
$_['entry_calculate_once_help'] = 'учитывать цены (вес, баллы) опций однократно, вне зависимости от выбранного количества товара. ';

$_['entry_animation']      		= 'Анимация цены';
$_['entry_animation_help']      = 'Анимация при смене цены (fading), работает не со всеми шаблонами';

$_['text_success'] = 'Настройки обновлены!';
$_['text_update_alert']     = '(доступна новая версия)';

$_['text_relatedoptions_notify'] = 'Должен быть установлен модуль <a href="https://liveopencart.ru/opencart-moduli-shablony/moduli/opcii/svyazannyie-optsii-dlya-opencart-3" target="_blank">Связанные опции</a> или <a href="https://liveopencart.ru/opencart-moduli-shablony/moduli/opcii/svyazannyie-optsii-pro-3" target="_blank">Связанные опции PRO</a> ';
$_['text_relatedoptions_pro_notify'] = 'Должен быть установлен модуль: <a href="https://liveopencart.ru/opencart-moduli-shablony/moduli/opcii/svyazannyie-optsii-pro-3" target="_blank">Связанные опции PRO</a>';

$_['module_description']    = 'Модуль "'.$_['module_name'].'" предназначен для расширения функциональности механизма ценообразования в OpenCart.<br><br>
Основные функции модуля:
<ul>
<li>динамическое обновление цены на странице товара в зависимости от выбранных покупателем опций и введенного количества</li>
<li>назначение скидок и акций в процентах, возможность указывать общие скидки и акции для всех групп покупателей</li>
<li>глобальные списки скидок и акций (по категории, производителю, группе покупателей</li>
<li>однократный учет цены опции при подсчете цены товара, вне зависимости от указанного количества (опционально, указывается для каждой опции)</li>
<li>вывод минимальной цены товара с учетом опций в списках (на страницах категорий, производителей, в модулях "Новинки", "Рекомендации" и т.п.)</li>
<li>учет количества товара уже добавленного в корзину при определении доступной скидки на странице товара (опционально)</li>
<li>отображение цены на странице товара умноженной на указанное количество (опционально)</li>
<li>дополнительные префиксы цены для опций ( * / = % )</li>
</ul>
';

$_['text_conversation'] = 'Есть вопросы по работе модуля? Требуется интеграция с шаблоном или доработка? Пишите: <b><a href="mailto:help@liveopencart.ru">help@liveopencart.ru</a></b>.';

$_['entry_we_recommend'] 	  = 'Также рекомендуем:';
$_['entry_show_we_recommend'] = 'показать';
$_['text_we_recommend']		  = '

';

$_['module_copyright'] = 'Модуль "'.$_['module_name'].'" это коммерческое дополнение. Не выкладывайте его на сайтах для скачивания и не передавайте его копии другим лицам.<br>
Приобретая модуль, Вы приобретаете право его использования на одном сайте. <br>Если Вы хотите использовать модуль на нескольких сайтах, следует приобрести отдельную копию модуля для каждого сайта.<br>';

$_['text_module_version'] = $_['module_name'].', версия';
$_['text_module_support'] = 'Разработка: <a href="http://19th19th.ru" target="_blank">19th19th.ru</a> | Поддержка: help@liveopencart.ru';

// Error
$_['error_permission']    = 'У Вас нет прав для изменения модуля "'.$_['heading_title'].'"!';

// Extension Manager API
$_['ll_icon']                   = 'fa fa-leaf';
$_['ll_color']                  = '#01A121';
$_['ll_buttons']                = '[{"style":"success","icon":"fa fa-shopping-cart","title":"Магазин модулей","position":"1","link":"https://liveopencart.ru","target":"0","sort":"3","id":"","before":"","after":" | "},{"style":"success","icon":"fa fa-envelope-o","title":"Помощь по модулю","position":"1","link":"mailto:help@liveopencart.ru?subject=Помощь по модулю","target":"1","sort":"4","id":"","before":"","after":" | "}]';