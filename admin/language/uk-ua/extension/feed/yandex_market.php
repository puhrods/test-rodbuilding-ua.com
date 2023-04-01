<?php
// *	@copyright		Pavel Kravchenko; https://opencartforum.com/profile/711752-paulkravchenko/

// Heading
$_['heading_title']          = 'Яндекс.Маркет';

// Text
$_['text_extension']   	     = 'Доповнення';
$_['text_feed']              = 'Канали просування';
$_['text_success']           = 'Налаштування змінені';
$_['text_edit']        	     = 'Редагування';
$_['text_width']             = 'Ширина';
$_['text_height']            = 'Висота';
$_['text_not_unload']        = 'Не вивантажувати';
$_['text_product_id']        = 'ID товару - product_id';
$_['text_name']        	     = 'Назва товару - name';
$_['text_meta_h1']        	 = 'HTML-тег H1 - meta_h1';
$_['text_meta_title']        = 'HTML-тег Title - meta_title';
$_['text_meta_keyword']      = 'Мета-тег Keywords - meta_keyword';
$_['text_meta_description']  = 'Мета-тег "Опис" - meta_description';
$_['text_model']        	 = 'Модель - model';
$_['text_sku']        	     = 'Артикул (SKU, код виробника) - sku';
$_['text_upc']        	     = 'UPC - upc';
$_['text_ean']        	     = 'EAN - ean';
$_['text_jan']        	     = 'JAN - jan';
$_['text_isbn']        	     = 'ISBN - isbn';
$_['text_mpn']        	     = 'MPN - mpn';
$_['text_location']        	 = 'Розташування';
$_['text_simplified']        = 'Спрощений тип опису';
$_['text_vendor_model']      = 'Довільний тип опису  - vendor.model';
$_['text_medicine']          = 'Ліки - medicine';
$_['text_books']             = 'Книги - books';
$_['text_audiobooks']        = 'Аудіокниги - audiobooks';
$_['text_artist_title']      = 'Музична та відеопродукція - artist.title';
$_['text_event_ticket']      = 'Квитки на події - event-ticket';
$_['text_tour']              = 'Тури - tour';

// Entry
$_['entry_status']           = 'Статус';
$_['entry_secret_key']       = 'Секретний ключ';
$_['entry_data_feed']        = 'Адрес';
$_['entry_shopname']         = 'Назва магазину';
$_['entry_company']          = 'Компанія';
$_['entry_id']               = 'Ідентифікатор пропозицій';
$_['entry_type']             = 'Тип пропозицій';
$_['entry_name']             = 'Брати тег name з поля';
$_['entry_model']        	 = 'Брати тег model з поля';
$_['entry_vendorcode']       = 'Брати тег vendorCode з поля';
$_['entry_image']            = 'Товар із зображеннями';
$_['entry_image_resize']     = 'Роздільна здатність зображень';
$_['entry_image_quantity']   = 'Кількість зображень товару';
$_['entry_main_category']    = 'Товар з головною категорією';
$_['entry_category']         = 'Категорії';
$_['entry_manufacturer']     = 'Виробники';
$_['entry_currency']         = 'Валюта пропозицій';
$_['entry_in_stock']         = 'Статус &quot;В наявності&quot;';
$_['entry_out_of_stock']     = 'Статус &quot;Немає в наявності&quot;';
$_['entry_quantity_status']  = 'Вивантажувати при кількості 0';

//Help
$_['help_secret_key']        = 'Ключ доступу до YML файлу, для захисту від DDoS атак і скачування бази товару';
$_['help_shopname']          = 'Коротка назва магазину (назва, яка виводиться в списку знайдених на Яндекс.Маркеті товарів. Не повинно містити більше 20 символів).';
$_['help_company']           = 'Повне найменування компанії, що володіє магазином. Не публікується, використовується для внутрішньої ідентифікації.';
$_['help_id']                = 'Ідентифікатор може містити тільки цифри і латинські букви. Максимальна довжина id - 20 символів. За замовчуванням ID товару.';
$_['help_type']              = 'Тип структури YML файлу під певну тематику товару';
$_['help_name']        	     = 'За замовчуванням назва товару';
$_['help_model']        	 = 'За замовчуванням модель';
$_['help_vendorcode']        = 'За замовчуванням артикул (SKU, код виробника)';
$_['help_image']             = 'Якщо так, то товари, що не мають зображень не будуть вивантажуватись';
$_['help_image_resize']      = 'Яндекс вимагає роздільну здатність зображень не менше 250х250px. Рекомендується 600х600px. Максимальний розмір 3500х3500px.';
$_['help_image_quantity']    = 'Скільки зображень товару експортувати. Яндекс приймає не більше 10.';
$_['help_main_category']     = 'Якщо так, то товари що не мають головній категорії не будуть вивантажуватись';
$_['help_category']          = 'Відзначте категорії з яких треба експортувати пропозиції для Яндекс.Маркет. Якщо не зазначено, то вивантажується весь товар.';
$_['help_manufacturer']      = 'Відзначте виробників з яких потрібно експортувати пропозиції для Яндекс.Маркет. Якщо не зазначено, то вивантажується весь товар';
$_['help_currency']          = 'Яндекс.Маркет приймає пропозиції у валюті RUR, RUB або UAH. Виберіть валюту в якій будуть передаватись пропозиції.';
$_['help_in_stock']          = 'При наявності товару на складі';
$_['help_out_of_stock']      = 'При залишку на складі 0.';
$_['help_quantity_status']   = 'Якщо так, то товари з нульовою кількістю вивантажуватимуться в YML файл';
$_['help_yandex_market']     = 'Тема на <a target="_blank" href="//forum.opencart.pro/topic/1059-yandexmarket-20/">Форумі</a>';

// Error
$_['error_image_width']      = 'Вкажіть ширину';
$_['error_image_height']     = 'Вкажіть висоту';
$_['error_image_width_min']  = 'Ширина зображення має бути не менш ніж 250 px';
$_['error_image_height_min'] = 'Висота зображення має бути не менш ніж 250 px';
$_['error_image_width_max']  = 'Ширина зображення має бути не більше ніж 3500 px';
$_['error_image_height_max'] = 'Висота зображення має бути не більше ніж 3500 px';
$_['error_permission']       = 'У вас недостатньо прав для внесення змін';