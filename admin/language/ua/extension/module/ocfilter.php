<?php

// COMMON
$_['heading_title'] = 'Фільтр товарів OCFilter <sup style="font-weight: normal;">' . (defined('OCF_VERSION') ? OCF_VERSION : '') . '</sup> <a href="http://feofan.club/" target="_blank" title="feofan.club" style="color:#ff7361"><i class="fa fa-download"></i></a> <a href="https://t.me/feofanchat/" target="_blank" title="HELP" style="color:#233746"><i class="fa fa-info-circle"></i></a>';
$_['heading_title_setting'] = 'Фільтр товарів OCFilter';
$_['button_apply'] = 'Застосувати';
$_['text_module'] = 'Модулі';
$_['text_edit'] = 'Налаштування фільтру товарів OCFilter <span class="badge" data-toggle="tooltip" data-placement="top" title="%s">%s</span>';
$_['text_select'] = '-- Оберіть --';
$_['text_selected'] = 'Обрано';
$_['text_success'] = 'Ви успішно оновили налаштування модуля &laquo;Фільтр товарів OCFilter&raquo;!';
$_['text_filter_list'] = 'Фільтри';
$_['text_filter_page_list'] = 'SEO Сторінки';
$_['text_faq'] = 'FAQ';
$_['text_documentation'] = 'Документація';
$_['text_ocfilter'] = 'OCFilter';
$_['text_ocfilter_filter'] = 'Фільтри';
$_['text_ocfilter_page'] = 'Сторінки';
$_['text_ocfilter_setting'] = 'Налаштування';
$_['text_loading'] = '<i class=\'fa fa-refresh fa-spin\'></i> Завантаження..';
$_['text_complete'] = '<i class=\'fa fa-check\'></i> Готово';

$_['text_filters'] = 'фильтрів';
$_['text_values'] = 'значень фільтра';

$_['entry_sort_order'] = 'Сортування';
$_['text_begin'] = 'На початку';
$_['text_after'] = 'В кінці';
$_['help_sort_order'] = 'Щоб вказати точну позицію клацніть по текстовому полю';

$_['entry_type'] = 'Тип';

// Error
$_['error_permission'] = 'Увага: у вас немає прав для редагування цього модуля!';
$_['error_copy_type'] = 'Будь ласка, вкажіть тип майбутніх фільтрів';
$_['error_license'] = 'Ліцензійний ключ невірний!';
$_['error_license_empty'] = 'Будь ласка, вкажіть ліцензійний ключ!';

// TAB GENERAL
$_['tab_general'] = 'Основне';

$_['entry_license'] = 'Ліцензійний ключ';
$_['help_license'] = 'Вкажіть отриманий при покупці ключ ліцензії. Якщо ви не отримали ключ або виникли інші проблеми з активацією модуля, напишіть на <a href="mailto:opencart.ocfilter@gmail.com?subject=Проблема%20с%20активацией%20OCFilter">opencart.ocfilter@gmail.com</a>';

$_['entry_status'] = 'Статус';
$_['help_status'] = 'Вмикає або вимикає модуль';

$_['entry_category_visibility'] = 'Видимість в категоріях';
$_['text_category_visibility_default'] = 'Як зазначено фільтрам';
$_['help_category_visibility_default'] = 'Фільтри будуть виводитися тільки в тих категоріях, які їм вказані в формі редагування фільтра';
$_['text_category_visibility_parent'] = 'Показувати в батьківських';
$_['help_category_visibility_parent'] = 'Виводити фільтри <b>з дочірніх</b> категорій <b>вбатьківські</b> навіть якщо вони їм не призначені явно';
$_['text_category_visibility_last_level'] = 'Показати тільки на останньому рівні';
$_['help_category_visibility_last_level'] = 'Модуль буде працювати тільки на <b>останньому рівні</b> вкладеності категорій';

$_['entry_hide_categories'] = 'Приховувати категорії при виборі фільтрів';

$_['entry_only_instock'] = 'Працювати тільки з товарами в наявності';
$_['help_only_instock'] = 'Це означає, що товари з нульовою кількістю не потраплятимуть в фільтри пошуку.<br />Якщо у значення фільтра усі товари не в наявності - воно також сховається.';

$_['entry_search_button'] = 'Фільтрувати по кнопці';
$_['help_search_button'] = 'Дозволяє спочатку вибрати необхідні фільтри, потім провести пошук товарів';

$_['entry_cache'] = 'Кешування даних';
$_['help_cache'] = 'Управління кешуванням даних модуля. Увага! Використовуйте відключення тільки в цілях налагодження. Включений кеш значно прискорює роботу модуля.';

$_['entry_cache_store'] = 'Сховище кешу';
$_['text_cache_db'] = 'База даних';
$_['text_cache_system'] = 'Як задано системою (%s)';

$_['entry_debug'] = 'Налагодження запитів';
$_['help_debug'] = '';

$_['nav_general_compatibility'] = 'Сумісність з іншими модулями';

$_['entry_module_hpm_group_products'] = 'Групувати знайдені товари';
$_['help_module_hpm_group_products'] = 'При пошуку фільтром показувати тільки згруповані товари';

$_['entry_module_hpm_group_counter'] = 'Групи в лічильнику товарів';
$_['help_module_hpm_group_counter'] = 'Лічильник товарів у кожного значення буде відображати кількість згрупованих товарів';

// TAB SPECIAL FILTERS
$_['tab_special_filter'] = 'Спеціальні фільтри';

// -------------------------------------------------------------- //
$_['tab_price'] = 'Ціна';

$_['entry_special_price'] = 'Фільтр по ціні';
$_['entry_special_price_logarithmic'] = 'Логарифмічна шкала ціни';

$_['entry_consider_tax'] = 'Враховувати податки';
$_['help_consider_tax'] = 'До цін будуть додаватися податки з урахуванням групи покупців';

$_['nav_price_source'] = 'Джерела цін <div class="small">можна комбінувати або залишити один</div>';

$_['entry_special_price_consider_regular_price'] = 'Звичайна ціна';
$_['help_special_price_consider_regular_price'] = 'Використання стандартної (звичайної) ціни товару';

$_['entry_special_price_consider_discount'] = 'Ціна зі знижкою';
$_['help_special_price_consider_discount'] = 'Включає ціни зі знижками';

$_['entry_special_price_consider_special'] = 'Акційна ціна';
$_['help_special_price_consider_special'] = 'Враховувати акційну ціну';

$_['entry_special_price_consider_option'] = 'Ціна опцій товару';
$_['help_special_price_consider_option'] = 'Діапазон цін розшириться до меж цін зазначених в опціях.<br />Доступні оператори для розрахунку ціни: +, -, *, /, =.';

// -------------------------------------------------------------- //
$_['tab_manufacturer'] = 'Виробник';

$_['entry_special_manufacturer'] = 'Фільтр по виробниках';
$_['help_special_manufacturer'] = 'Дозволяє фільтрувати товари за стандартними виробникам';

$_['entry_special_manufacturer_dropdown'] = 'Випадаючий список';
$_['entry_special_manufacturer_image'] = 'Виводити зображення';

// -------------------------------------------------------------- //
$_['tab_discount'] = 'Знижка';

$_['entry_special_discount'] = 'Тільки з знижкою';
$_['help_special_discount'] = 'Фільтр пропонує вибрати товари зі зниженою ціною';
$_['entry_special_discount_consider_special'] = 'Враховувати акції';
$_['entry_special_discount_consider_discount'] = 'Враховувати знижки';

// -------------------------------------------------------------- //
$_['tab_newest'] = 'Новинка';

$_['entry_special_newest'] = 'Тільки нові';
$_['help_special_newest'] = 'Фільтр пропонує вибрати тільки нові товари';

$_['entry_special_newest_interval'] = 'Ознака нового товару';
$_['text_special_newest_interval_hour'] = 'Часів';
$_['text_special_newest_interval_day'] = 'Днів';
$_['text_special_newest_interval_week'] = 'Тижнів';
$_['text_special_newest_interval_month'] = 'Місяців';
$_['help_special_newest_interval'] = 'Товар вважається новим, якщо доданий не пізніш зазначеного періоду';

// -------------------------------------------------------------- //
$_['tab_dimension'] = 'Розміри і вага';

$_['entry_special_weight'] = 'Вага товару';
$_['entry_special_width'] = 'Ширина';
$_['entry_special_height'] = 'Висота';
$_['entry_special_length'] = 'Довжина';

// -------------------------------------------------------------- //
$_['tab_stock'] = 'Склад';

$_['entry_special_stock'] = 'Фільтр по наявності на складі';
$_['help_special_stock'] = 'Дає можливість фільтрувати товари за наявністю на складі';

$_['entry_special_stock_method'] = 'Метод';
$_['text_special_stock_method_by_status_id'] = 'За статусом складу товару (stock_status_id)';
$_['text_special_stock_method_by_quantity'] = 'За кількістю товару';

$_['entry_special_stock_out_value'] = 'Показувати значення &laquo;Немає в наявності&raquo;';

// TAB SEO
$_['tab_seo'] = 'SEO';

// -------------------------------------------------------------- //
$_['nav_seo_page'] = 'Посадочні сторінки';

$_['entry_sitemap'] = 'Sitemap посадочних сторінок фільтра';
$_['entry_sitemap_link'] = 'Посилання на Sitemap';

$_['entry_page_category_link_status'] = 'Виводити посилання на сторінки категорій';
$_['help_page_category_link_status'] = 'Посилання на посадочні сторінки будуть виведені в категоріях у вигляді тегів. Назви посилань береться з поля &laquo;Назва&raquo;';

$_['entry_page_category_link_position'] = 'Позиція посилань в категорії';
$_['text_page_category_link_above'] = 'Над товарами';
$_['text_page_category_link_under'] = 'Під товарами';
$_['text_page_category_link_both'] = 'Розподілити порівну';

$_['entry_page_module_link_status'] = 'Виводити посилання в модулі';
$_['help_page_module_link_status'] = 'Посилання на посадочні сторінки будуть виведені у верхній частині модуля';

$_['entry_page_module_link_title'] = 'Назва блоку посилань';
$_['page_module_link_title'] = 'Популярні фільтри';

$_['entry_page_product_link_status'] = 'Виводити посилання в характеристиках товару';
$_['help_page_product_link_status'] = 'Посилання на посадочні сторінки можна прив\'язати до характеристик товару';

$_['entry_page_product_link_relation_type'] = 'Логіка прив\'язки сторінок до атрибутів';
$_['text_page_product_link_relation_complete'] = 'Повна відповідність';
$_['text_page_product_link_relation_partial'] = 'Часткова відповідність';
$_['help_page_product_link_relation_type'] = 'При <b>повній відповідності</b> до товару будуть застосовані ті посадочні сторінки, у яких всі фільтри збігаються з атрибутами товарів.<br />Наприклад, ви створили сторінку до фільтру &laquo;Колір: червоний&raquo;, &laquo;Розмір: середній&raquo;. Сторінка буде виводитися тільки в тих товарах, які мають атрибут<br />&laquo;Колір: червоний&raquo; <b>і</b> &laquo;Розмір: середній&raquo;.<br /><b>Часткова відповідність</b> зв\'яже товари зі сторінками за умови, що хоча б один фільтр посадкової сторінки буде згадуватися в атрибутах товару.';

$_['entry_url_suffix'] = 'Закінчення посилання';
$_['placeholder_url_suffix'] = 'Наприклад, .html';

// -------------------------------------------------------------- //
$_['nav_seo_meta'] = 'Автоматичні мета дані <div class="small">Ці дані потрібні тільки для ваших покупців і тільки для тих фільтрів, яким не вказана посадкова сторінка. Пошукова система їх не побачить</div>';

$_['entry_add_meta'] = 'Додавати в мета дані';
$_['text_add_meta_filter_value'] = 'Фільтри та значення';
$_['text_add_meta_value'] = 'Тільки значення';
$_['text_add_meta_disabled'] = 'Не додавати';

$_['entry_meta_filter_separator'] = 'Роздільник фільтрів';
$_['entry_meta_value_separator'] = 'Роздільник значень';
$_['entry_meta_lowercase'] = 'Малими літерами';
$_['entry_add_meta_limit'] = 'Додавати не більше';

// -------------------------------------------------------------- //
$_['nav_seo_misc'] = 'Різне';

$_['entry_category_breadcrumb'] = 'Додавати хлібну крихту в категорії';
$_['help_category_breadcrumb'] = 'Додавати хлібну крихту з вибраними фільтрами (або посадкової сторінкою) на сторінці категорії. Ефективність даної настройки для SEO не вивчена, тому до її активації краще проконсультуйтеся зі своїм SEO фахівцем';

$_['entry_product_breadcrumb'] = 'Додавати хлібну крихту в товарі';
$_['help_product_breadcrumb'] = 'Додавати хлібну крихту з вибраними фільтрами (або посадкової сторінкою) на сторінку товару між категорією і товаром. Як і в випадку вище, необхідність включення даної настройки вимагає уточнення';

// TAB APPEARANCE
$_['tab_appearance'] = 'Зовнішній вигляд';

// -------------------------------------------------------------- //
$_['nav_appearance_module'] = 'Модуль і мобільна версія';

$_['entry_module_heading_title'] = 'Заголовок модуля';
$_['module_heading_title'] = 'Фільтр';

$_['entry_mobile_button_text'] = 'Текст кнопки виклику мобільної версії';
$_['mobile_button_text'] = 'Фільтр';

$_['entry_mobile_button_position'] = 'Позиція кнопки мобільної версії';
$_['text_mobile_button_position_fixed'] = 'Плаваюча';
$_['text_mobile_button_position_static'] = 'Статична над товарами';
$_['text_mobile_button_position_both'] = 'Обидва варіанти';

$_['entry_mobile_max_width'] = 'Ширина екрана мобільної версії';
$_['help_mobile_max_width'] = 'Вкажіть максимальну ширину екрану (в пікселях), при якій модуль буде залишатися в режимі мобільної версії.<br />За замовчуванням це 767 пікселів, що дорівнює значенню перемикання &laquo;sm&raquo; для Bootstrap 3';

$_['entry_mobile_placement'] = 'Розташування мобільної версії';
$_['text_mobile_placement_left'] = 'Зліва';
$_['text_mobile_placement_right'] = 'Справа';

$_['entry_mobile_remember_state'] = 'Пам\'ятати стан вікна мобільній версії';
$_['help_mobile_remember_state'] = 'Включення цієї опції приведе до відновлення вікна мобільній версії після перезавантаження сторінки';

// -------------------------------------------------------------- //
$_['nav_appearance_filter'] = 'Фільтри';

$_['entry_theme'] = 'Тема';
$_['text_theme_light'] = 'Світла';
$_['text_theme_light_block'] = 'Світла блокова';

$_['entry_show_first_limit'] = 'Показувати тільки перші';
$_['help_show_filters_limit'] = 'Вкажіть ліміт кількості фільтрів, які будуть виводитися в модулі фільтра товарів. Щоб виводити всі фільтри, вкажіть 0';

$_['entry_hidden_filters_lazy_load'] = '&laquo;Ледаче&raquo; завантаження фільтрів';
$_['help_hidden_filters_lazy_load'] = 'Завантажувати приховані фільтри в фоновому режимі (AJAX).<br />Ця настройка може полегшити сторінки з великою кількістю фільтрів і підвищити показники Google PageSpeed.';

$_['entry_hide_single_value'] = 'Приховувати фільтри з одним значенням';
$_['help_hide_single_value'] = 'Не показувати фільтри у яких тільки одне активне значення';

$_['entry_slider_input'] = 'Поля введення для слайдерів';
$_['help_slider_input'] = 'Дозволяє вводити значення для слайдерів в окремі поля';

$_['entry_show_diagram'] = 'Діаграма';
$_['help_show_diagram'] = 'Графічне відображення відношення кількості товарів до значення діапазону';

$_['entry_slider_pips'] = 'Шкала зі значеннями';

$_['entry_show_selected'] = 'Показувати вибрані параметри';
$_['help_show_selected'] = 'Показує блок вибраних параметрів з можливістю виключення їх із запиту';

// -------------------------------------------------------------- //
$_['nav_appearance_filter_value'] = 'Значення';

$_['entry_show_counter'] = 'Показувати лічильник товарів';
$_['help_show_counter'] = 'Показує кількість товарів для кожного значення.<br />На швидкість завантаження сторінки цей параметр не впливає';

$_['help_show_values_limit'] = 'Вкажіть ліміт кількості значень, які будуть виводитися в модулі фільтра товарів для кожного фільтра. Щоб виводити все значення, вкажіть 0';

$_['entry_hidden_values_lazy_load'] = '&laquo;Ледаче&raquo; завантаження значень';
$_['help_hidden_values_lazy_load'] = 'Аналогічно фільтрам. При включенні цієї опції подгрузка прихованих значень фільтра здійснюється у фоновому режимі.';

$_['entry_hide_empty_values'] = 'Приховувати неактивні значення';
$_['help_hide_empty_values'] = 'Повністю приховує неактивні (з нульовим показником товарів) значення фільтрів. У разі, якщо будуть приховані все значення - ховається і сам фільтр';

$_['entry_values_auto_column'] = 'Розбивати значення на колонки';
$_['help_values_auto_column'] = 'Автоматично розбивати значення на колонки (2 або 3) в залежності від довжини їх назв.';

// TAB PLACEMENT
$_['tab_placement'] = 'Розміщення';
$_['text_placement'] = 'Вкажіть макети (схеми) і фільтри, які повинні виводитися на них.<br />Також необхідно додати модуль до відповідного макету в розділі <a href="%s" class="alert-link" target="_blank"><u>Дизайн - Макети (Схеми / Шаблони)</u></a><br /><b class="text-danger">Увага!</b> Не використовуйте це налаштування для виведення модуля в макетах &laquo;Категорія&raquo;, &laquo;Виробник&raquo;, &laquo;Акції&raquo; та &laquo;Пошук&raquo;. Просто додайте модуль в ці макети і все.';
$_['text_new_placement'] = '-- Нове --';

$_['button_add_placement'] = 'Додати розміщення';
$_['button_remove_placement'] = 'Видалити розміщення';

$_['entry_placement_layout'] = 'Вкажіть макет';
$_['entry_placement_filter'] = 'Додайте фільтри';
$_['placeholder_autocomplete'] = 'Починайте вводити назву';

// TAB COPY FILTERS
$_['tab_copy'] = 'Копіювання фільтрів';
$_['text_confirm_truncate_copy'] = 'Ви впевнені, що хочете очистити\nвсі існуючі фільтри OCFilter?';

// -------------------------------------------------------------- //
$_['nav_copy_source'] = 'Джерела фільтрів';

$_['entry_copy_attribute'] = 'Копіювати атрибути';
$_['text_copy_attribute_total'] = 'Атрибутів: <b>%s</b>, значень: <b>%s</b>';

$_['entry_copy_group_as_attribute'] = 'Групи атрибутів як фільтри';
$_['help_copy_group_as_attribute'] = 'Якщо <b>групи</b> атрибутів є <b>фільтрами</b>, атрибути - <b>значеннями</b>, а в формі товару у вкладці &laquo;Атрибути&raquo; поле &laquo;<b>Текст</b>&raquo; (праворуч) не заповнено - вкажіть &laquo;Так&raquo;';

$_['entry_copy_attribute_data'] = 'Дані для копіювання';
$_['help_copy_attribute_data'] = 'Вкажіть дані атрибутів для копіювання в фільтри.<br />Ви можете вибрати конкретні атрибути, категорії, або групи атрибутів (якщо використовується режим &laquo;<kbd>Групи атрибутів як фільтри</kbd>&raquo;).<br />Будь-які дані можна виключити з копіювання відповідної опцією.<br />Кнопка &laquo;<kbd>Авто</kbd>&raquo; дозволяє отримати список ймовірно відповідних атрибутів під обраний режим вибірки.<br />Якщо ці поля залишаться порожніми, то скопійовано буде всі атрибути.';

$_['entry_copy_exclude'] = 'Виключити';

$_['placeholder_copy_attribute_autocomplete'] = 'Атрибут';
$_['placeholder_copy_attribute_group_autocomplete'] = 'Група атрибута';
$_['placeholder_copy_category_autocomplete'] = 'Категорія';

$_['button_clear'] = 'Очистити';
$_['button_auto'] = 'Авто';

$_['entry_copy_filter'] = 'Копіювати стандартні фільтри';
$_['text_copy_filter_total'] = 'Фільтрів: <b>%s</b>, значень: <b>%s</b>';

$_['entry_copy_option'] = 'Копіювати опції товарів';
$_['text_copy_option_total'] = 'Опцій: <b>%s</b>, значень: <b>%s</b>';

$_['entry_copy_option_in_stock'] = 'Тільки в наявності';
$_['help_copy_option_in_stock'] = 'Копіювати опції тільки з позитивним залишком товарів';

// -------------------------------------------------------------- //
$_['nav_copy_filter'] = 'Налаштування результуючих фільтрів <div class="small">Які з\'являться після копіювання. На вже існуючі фільтри ці настройки не впливають.</div>';

$_['entry_copy_type'] = 'Тип скопійованих фільтрів';
$_['entry_copy_dropdown'] = 'Помістити в список, що випадає';

$_['entry_copy_status'] = 'Статус скопійованих фільтрів';
$_['help_copy_status'] = 'Статус нових фільтрів, які будуть створені із зазначених джерел.<br />Незалежно від обраного статусу, відключені будуть ті фільтри, у яких немає значень або вони не прив\'язані до товарів або категорій';

// -------------------------------------------------------------- //
$_['nav_copy_other'] = 'Інше';

$_['entry_copy_value_separator'] = 'Роздільник значень';
$_['placeholder_copy_value_separator'] = 'Наприклад, «/»';
$_['help_copy_value_separator'] = 'Щоб розбити одне складне значення фільтра на поодинокі, використовуйте роздільник значень фільтра.<br />Наприклад, для поділу значення &laquo;Зелений / Червоний / Синій&raquo; фільтра &laquo;Колір&raquo; на окремі кольори, використовуйте роздільник &laquo;/&raquo;.<br />Можна використовувати до 3-х роздільників одночасно';

$_['entry_copy_clear_filter'] = 'Очистити існуючі фільтри OCFilter';
$_['help_copy_clear_filter'] = 'Видалити тільки раніше скопійовані фільтри. Додані вручну залишаться недоторканими.';

$_['entry_copy_category'] = 'Прив\'язати фільтри до категорій';
$_['help_copy_category'] = 'При виборі цієї опції всі існуючі зв\'язки фільтрів OCFilter з категоріями будуть оновлені. Фільтри, додані вручну, свої зв\'язки з категоріям не змінять.';

// -------------------------------------------------------------- //
$_['nav_copy_auto'] = 'Автоматизація';

$_['text_copy_auto_code_php'] = 'PHP Код для виклику копіювання з поточними настройками';
$_['help_copy_auto_code_php'] = 'Вставте цей код в кінець сценарію імпорту товарів, скрипт парсинга або інше місце, де має сенс викликати копіювання.';

$_['text_copy_auto_code_js'] = 'JS Код для виклику копіювання з поточними настройками';
$_['help_copy_auto_code_js'] = 'Код можна розмістити в будь-якому місці шаблона, виклик за подією і т.д.';

$_['text_copy_auto_cron'] = 'Команда для виклику по cron (планувальник)';
$_['help_copy_auto_cron'] = 'Зручний редактор періоду для cron <a href="https://crontab.guru/" target="_blank">тут</a><br />Після вказівки параметрів копіювання <b>обов\'язково</b> збережіть налаштування';
$_['text_cron_select_period'] = 'Виберіть період копіювання<br />або вкажіть свій';
$_['text_cron_period'] = 'Період';
$_['text_cron_period_01'] = 'Щогодини';
$_['text_cron_period_02'] = 'Кожні 3 години';
$_['text_cron_period_03'] = 'Щодоби о 04:00';
$_['text_cron_period_04'] = 'Щонеділі о 04:00';
$_['text_cron_period_05'] = 'Кожні 5 годин на вихідні';
$_['text_cron_period_06'] = 'Кожного 1-го числа нового місяця';
$_['text_or'] = 'або';
$_['text_cron_period_manual'] = 'Свій період';
$_['text_cron_bin'] = 'Команда додавання виклику через PHP bin';
$_['text_cron_wget'] = 'Дозволити викликати через Wget';

$_['entry_copy_now'] = 'Копіювати зараз';
$_['button_copy'] = 'Копіювати';
$_['entry_copy_save_setting'] = 'І зберегти всі поточні настройки копіювання';

$_['error_install_modification_not_found'] = 'Модифікатор ' . DIR_SYSTEM . 'ocfilter.ocmod.xml не знайден. Скопіюйте модифікатор за вказаним шляхом i повторіть установку.';
$_['error_install_modification_update'] = 'Будь ласка, поновіть модифікатори із запущеною консоллю браузера (F12) і спробуйте знову налаштувати модуль.';
$_['error_install_tables'] = 'Будь ласка, видаліть модуль зі списку модулів і встановіть по кнопці &laquo;Встановити&raquo; з цього ж списку модулів.';