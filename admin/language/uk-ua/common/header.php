<?php
// *	@copyright		Pavel Kravchenko; https://opencartforum.com/profile/711752-paulkravchenko/

// Heading
if (file_exists(DIR_LANGUAGE.'ru-ru/common/header.php')) {
	include(DIR_LANGUAGE.'ru-ru/common/header.php');
	$_['heading_title'];
} elseif (file_exists(DIR_LANGUAGE.'en-gb/common/header.php')) {
	include(DIR_LANGUAGE.'en-gb/common/header.php');
	$_['heading_title'];
} else {
	$_['heading_title']  = 'OpenCart';
}

// Text
$_['text_profile']      		  = 'Ваш профіль';
$_['text_store']           	 	  = 'Магазини';
$_['text_help']            	 	  = 'Допомога';
$_['text_homepage']        	 	  = 'Сайт проекту';
$_['text_support']         	 	  = 'Форум підтримки';
$_['text_documentation']   	 	  = 'Документація';
$_['text_logout']          	 	  = 'Вихід';
$_['text_search_options']  		  = 'Опції пошуку';
$_['text_new']  		   		  = 'Додати';
$_['text_new_category']    		  = 'Категорію';
$_['text_new_customer']    		  = 'Покупця';
$_['text_new_download']           = 'Завантаження';
$_['text_new_manufacturer']		  = 'Виробника';
$_['text_new_product']     		  = 'Товар';

$_['text_order']             	  = 'Замовлення';
$_['text_processing_status'] 	  = 'Обробка';
$_['text_complete_status'] 	 	  = 'Завершено';
$_['text_customer']        	 	  = 'Покупці';
$_['text_online']          	 	  = 'Покупці онлайн';
$_['text_approval']        	 	  = 'Очікують схвалення';
$_['text_product']         	 	  = 'Товари';
$_['text_stock']           	 	  = 'Немає в наявності';
$_['text_review']          	 	  = 'Відгуки';
$_['text_return']          	 	  = 'Повернення товару';
$_['text_affiliate']       	 	  = 'Партнерська програма';
$_['text_front']           	 	  = 'Магазин';
$_['button_clearallcache']        = 'Видалити весь кеш';
$_['button_clearcache']           = 'Видалити кеш зображень';
$_['button_clearsystemcache']     = 'Видалити системний кеш';
