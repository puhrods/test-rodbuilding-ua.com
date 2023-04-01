<?php
// *	@copyright		Pavel Kravchenko; https://opencartforum.com/profile/711752-paulkravchenko/
$_['heading_title'] = 'ProgRoman - CityManager+GeoIP Pro';

// Entry
$_['entry_default_city'] = 'Місто за замовчуванням';
$_['entry_use_geoip'] = 'Визначати місто по IP<span class="help" data-toggle="tooltip" title="Включити визначення міста за IP-адресою"></span>';
$_['entry_use_cookie'] = 'Зберігати регіон в cookie';
$_['entry_popup_cookie_time'] = 'Показувати "Вгадали місто"';
$_['entry_popup_view'] = 'Тип попапу';
$_['entry_key'] = 'Ключ';
$_['entry_zone'] = 'Місто / регіон / країна';
$_['entry_city'] = 'Місто';
$_['entry_sort'] = 'Сортування';
$_['entry_value'] = 'Значення';
$_['entry_subdomain'] = 'Піддомен';
$_['entry_country'] = 'Країна';
$_['entry_disable_autoredirect'] = 'Відключити авторедірект при першому заході<span class="help" data-toggle="tooltip" title="Перехід на піддомен тільки при виборі міста в попапі"></span>';
$_['entry_currency'] = 'Валюта';
$_['entry_domain'] = 'Основний домен <span class="help" data-toggle="tooltip" title="Вказуйте лише якщо відрізняється від поточного"></span>';
$_['entry_license'] = 'Ліцензія<span class="help" data-toggle="tooltip" title="Ключ, виданий автором"></span>';
$_['entry_status'] = 'Статус';
$_['entry_customer_group'] = 'Групи покупців';
$_['entry_customer_group_high_priority'] = 'Пріоритет над групою зареєстрованого користувача<span class="help" data-toggle="tooltip" title="Група завжди встановлюється в залежності від міста та перекриває групу зареєстрованого користувача"></span>';
$_['entry_customer_group_for_new_user'] = 'Присвоювати групу міста користувачеві при реєстрації<span class="help" data-toggle="tooltip" title="При реєстрації новому користувачеві присвоїти групу поточного міста, в подальшому у нього завжди буде ця група"></span>';
$_['entry_sub_enabled'] = 'Включено<span class="help" data-toggle="tooltip" title="Вимкніть, якщо не використовуєте даний функціонал, щоб уникнути додаткових запитів до БД"></span>';
$_['entry_bind_subdomain'] = 'Прив’язати місто до піддомену / URL<span class="help" data-toggle="tooltip" title="Місто залежить від піддомену і тільки, якщо його немає в списку, визначається за IP"></span>';
$_['entry_enabled_redirects'] = 'Включити редіректи';
$_['entry_fias_in_qs'] = 'Додавати ID міста в рядку запиту<span class="help" data-toggle="tooltip" title="Увімкніть, якщо магазин працює на різних доменах">';

$_['text_popup_cities'] = 'Міста для попап вибору міста';
$_['text_regions_info'] = 'Ці налаштування використовуються для зіставлення регіонів OpenCart і бази ФІАС, якщо у вас регіони відрізняються від стандартних OpenCart (наприклад, додані / відредаговані вручну). Переконайтесь, що регіони відповідають один одному!';
$_['text_module'] = 'Модулі';
$_['text_success'] = 'Налаштування модуля оновлені!';
$_['text_license'] = 'Не вказана ліцензія';

$_['text_every_visit'] = 'При кожному новому візиті';
$_['text_day'] = 'Раз на добу';
$_['text_week'] = 'Раз на тиждень';
$_['text_month'] = 'Раз на місяць';
$_['text_year'] = 'Раз на рік';

$_['text_sxgeo_upload'] = 'Завантажити';
$_['text_sxgeo_step_upload'] = 'Завантаження...';
$_['text_sxgeo_step_unzip'] = 'Розпакування архіву...';
$_['text_sxgeo_manual_upload'] = 'Завантажте файл вручну: <a href="http://sypexgeo.net/files/SxGeoCity_utf8.zip" target="_blank">завантажте zip-файл</a>, розархівуйте в %s';
$_['text_sxgeo_upload_success'] = 'База даних успішно завантажена';

$_['tab_popup'] = 'Попапи';
$_['tab_messages'] = 'Геоповідомлення';
$_['tab_redirects'] = 'Піддомени';
$_['tab_currencies'] = 'Валюта';
$_['tab_regions'] = 'Регіони';
$_['tab_groups'] = 'Групи покупців';

$_['button_customer_group'] = 'Створити групу покупців';

$_['error_permission'] = 'У вас немає прав для управління цім модулем!';
$_['error_key'] = 'Поле має містити тільки латинські букви, цифри і знаки "-", "_"';
$_['error_fias'] = 'Вкажіть зону';
$_['error_subdomain'] = 'Вкажіть піддомен у вигляді: http://abc.site.com/ або http://abc.site.com/path/to/';
$_['error_currency_country'] = 'Вкажіть країну';
$_['error_currency_code'] = 'Вкажіть валюту';
$_['error_license'] = 'Модуль не активований, отримайте ліцензійний ключ у автора модуля';
$_['error_sxgeo'] = 'База даних IP-адрес не завантажена';
$_['error_unique_regions'] = 'На вкладці "Регіони" не всі значення унікальні, перевірте значення для ';
$_['error_customer_group'] = 'Оберіть групу покупців';

$_['error_unzip'] = 'Zip архів не вдалося відкрити';
$_['error_create_file'] = 'Не вдалося створити файл';
$_['error_upload'] = 'Файл не вдалося завантажити';
$_['error_unknown'] = 'Невизначена помилка';
