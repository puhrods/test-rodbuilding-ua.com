<?php

// Heading
$_['heading_title']          = 'SEO Страницы OCFilter';

// Buttons
$_['button_add_page'] = 'Добавить страницу';
$_['button_apply_add'] = 'Сохранить и добавить еще';
$_['button_edit_pages'] = '<i class="fa fa-pencil"></i> Изменить страницы';
$_['button_add_pages'] = '<i class="fa fa-plus"></i> Добавить страницы';
$_['button_show'] = 'Открыть в каталоге';

// Tabs
$_['tab_general']            = 'Основное';
$_['tab_data']               = 'Данные';
$_['tab_relation']           = 'Связи';
$_['tab_display']            = 'Расположение';

// Text
$_['text_success']           = 'Вы успешно изменили SEO Страницы!';
$_['text_list']              = 'Список страниц';
$_['text_add']               = 'Добавить SEO страницу';
$_['text_edit']              = 'Редактировать SEO страницу';
$_['text_default']           = 'По умолчанию';
$_['text_dynamic']           = 'Динамическая страница';
$_['text_type_static']       = 'Статический';
$_['text_type_dynamic']      = 'Динамический';
$_['text_confirm_delete_page'] = '<p>Вы действительно хотите удалить выбранные страницы?</p><div class=\'text-right\'><button type=\'button\' data-dismiss=\'popover\' class=\'btn btn-default\'>Нет</button> <button type=\'button\' onclick=\'$(`#form-list`).submit()\' class=\'btn btn-danger\'>Да</button></div>';
$_['text_loading']              = '<i class=\'fa fa-refresh fa-spin\'></i> Загрузка..';
$_['text_faq'] = 'FAQ';
$_['text_documentation'] = 'Документация';
$_['text_mask_vars'] = 'Переменные автоподстановки';
$_['text_info_mask'] = '<p class="h4">Доступные переменные автоматической подстановки</p><p class="h4">Указанная в тексте переменная заменится на выбранные фильтры</p>';
$_['text_info_mask_static'] = '<i class="fa fa-exclamation-triangle"></i> В статическом режиме автоподстановка недоступна';
$_['text_info_mask_filter'] = '<i class="fa fa-info-circle"></i> С данным набором фильтров маска не используется';
$_['text_info_mask_filter_select'] = 'Необходимо выбрать фильтры';
$_['text_batch_edit'] = '<i class="fa fa-edit"></i>  Массовое редактирование';
$_['text_batch_add'] = '<i class="fa fa-plus"></i>  Массовое добавление';
$_['text_action_replace_text'] = 'Заменить текст';
$_['text_action_add_text'] = 'Добавить текст';
$_['text_action_update'] = 'Данные';
$_['text_action_delete'] = 'Удалить';
$_['text_replace_on'] = 'на';
$_['text_add_prepend'] = 'в начало';
$_['text_add_append'] = 'в конец';
$_['text_destination'] = 'в';
$_['text_destination_all'] = 'любом поле';
$_['text_destination_name'] = 'названии';
$_['text_destination_heading_title'] = 'заголовке';
$_['text_destination_description_top'] = 'верхнем описании';
$_['text_destination_description_bottom'] = 'нижнем описании';
$_['text_destination_meta_title'] = 'meta title';
$_['text_destination_meta_description'] = 'meta description';
$_['text_destination_meta_keyword'] = 'meta keyword';
$_['text_destination_seo_url'] = 'seo url псевдоним';
$_['text_target'] = 'для';
$_['text_target_all'] = 'всех';
$_['text_target_filter'] = 'всех по заданным фильтрам';
$_['text_target_selected'] = 'выбранных на этой странице';
$_['text_discard'] = 'Не изменять';
$_['text_select_categpry'] = 'Выберите категорию';
$_['text_select_filter'] = 'Выберите фильтры';
$_['text_display'] = 'вывод';
$_['text_slider_not_available'] = 'Слайдеры доступны в расширенной форме';

// Column
$_['column_name']            = 'Название';
$_['column_category']        = 'Категория';
$_['column_status']          = 'Статус';
$_['column_view']            = 'Вывод';
$_['column_action']          = 'Действие';

// Entry
$_['entry_type']             = 'Режим подстановки данных';
$_['entry_name']             = 'Название';
$_['entry_heading_title']    = 'Заголовок (H1)';
$_['entry_description_top']  = 'Верхнее описание';
$_['entry_description_bottom']  = 'Нижнее описание';
$_['entry_meta_title'] 	   = 'Title страницы';
$_['entry_meta_keyword'] 	   = 'Meta Keywords';
$_['entry_meta_description'] = 'Meta Description';
$_['entry_keyword']          = 'SEO URL псевдоним';
$_['entry_category']         = 'Категория';
$_['entry_filter']           = 'Фильтры';
$_['entry_filter_value']     = 'Значения фильтра';
$_['entry_status']           = 'Статус';
$_['entry_display_module']           = 'Выводить в модуле';
$_['entry_display_category']           = 'Выводить на странице категории';
$_['entry_display_product']           = 'Выводить на странице товара';
$_['entry_display_sitemap']           = 'Выводить в карте сайта';
$_['entry_display_code'] = 'Код вывода в любом месте';
$_['entry_store']            = 'Магазин';
$_['entry_layout']           = 'Макет (схема)';
$_['entry_edit_status'] = 'Статус';
$_['entry_edit_display_category'] = 'Категория';
$_['entry_edit_display_product'] = 'Товар';
$_['entry_edit_display_module'] = 'Модуль';
$_['entry_edit_display_sitemap'] = 'Карта';

// Help
$_['help_add'] = 'Название страницы является названием ссылки, ведущей на нее. Также это название выводится в списке страниц панели управления.<br>
В этом поле <b>поддерживаются</b> переменные автозамены. Если поле не заполнено, то будет использоваться заголовок (h1)';
$_['help_heading_title'] = 'Заловок страницы поддерживает переменные автозамены';
$_['help_name'] = '';
$_['help_meta_title'] = 'Этот текст будет выводиться в мета тег title, переменные также поддерживаются';
$_['help_description_top'] = 'Текст выводится над товарами категории';
$_['help_description_bottom'] = 'Текст выводится под товарами категории';

$_['help_keyword'] = 'Используйте буквы, цифры, знаки «_», «-» и переменные автозамены. Другие символы не допускаются. Псевдоним должен быть уникальным на всю систему или в рамках указанной категории страниц';

$_['help_add_keyword'] = 'Используйте буквы, цифры, знаки «_», «-» и переменные автозамены. Другие символы не допускаются. Псевдоним должен быть уникальным на всю систему или в рамках указанной категории страниц';

$_['help_type'] = 'Страница может быть статической или динамической.<br /><br />
<b>Статический тип</b>:<br />
- данные работают только для одного фиксированного набора фильтров<br />
- доступен вывод страницы в любом месте используя код ссылки из вкладки «расположение»<br />
- доступен вывод страницы в блоке фильтра<br /><br />
<b>Динамический тип</b>:<br />
- данные работают для множественных групп фильтров<br />
- доступны переменные автоподстановки выбранных фильтров';

$_['help_ocfilter_filter'] = 'Укажите фильтры с которыми будет работать данная SEO страница.<br />
<b class="text-primary">Синим цветом</b> выделены специальные фильтры, отключенные фильтры доступны в конце списка.<hr />

Если страница <b>динамическая</b>, то она сработает при выборе как минимум одного значения из каждого фильтра. Например:<br />
Выбранные для страницы фильтры:<br /><br />
- цвет: красный, синий<br />
- размер: M, L<br /><br />
Страница сработает при выборе<br />- цвет: [красный <b>и/или</b> синий] <br /><b>и</b><br />- размер: [M <b>и/или</b> L]<hr />

Если страница <b>статическая</b>, то она сработает при выборе всех указанных значений. Например:<br />
Выбранные для страницы фильтры:<br /><br />
- цвет: красный, синий<br />
- размер: M, L<br /><br />
Страница сработает при выборе<br />- цвет: красный <b>и</b> синий<br /><b>и</b><br />- размер: M <b>и</b> L';

// Placeholder
$_['placeholder_text'] = 'Текст';
$_['placeholder_display_code'] = 'В динамическом типе код вывода недоступен';

// Error
$_['error_warning']          = 'Проверьте форму на наличие ошибок!';
$_['error_permission']       = 'У вас нет прав на изменение SEO страниц!';
$_['error_name']             = 'Название SEO страницы должно быть до %s символов!';
$_['error_heading_title']    = 'Заголовок SEO страницы должен быть от %s до %s символов!';
$_['error_meta_title']       = 'Мета тег title должен быть от %s до %s символов!';
$_['error_keyword_exist']   = 'Указанный SEO URL псевдоним уже используется';
$_['error_keyword_exist_page_id'] = '<a href="%s" target="_blank">SEO страницей</a>';
$_['error_keyword_exist_filter_id'] = '<a href="%s" target="_blank">фильтром</a>';
$_['error_keyword_exist_value_id'] = '<a href="%s" target="_blank">значением фильтра</a>';
$_['error_keyword_exist_category_id'] = '<a href="%s" target="_blank">категорией</a>';
$_['error_keyword_exist_product_id'] = '<a href="%s" target="_blank">товаром</a>';
$_['error_keyword_exist_information_id'] = '<a href="%s" target="_blank">информационной страницей</a>';
$_['error_keyword_exist_manufacturer_id'] = '<a href="%s" target="_blank">производителем</a>';
$_['error_mask']             = 'Для этой комбинации фильтров требуется использование маски';
$_['error_category']         = 'Пожалуйста, укажите категорию!';
$_['error_filter']           = 'Пожалуйста, укажите фильтры!';
$_['error_target_empty']     = 'По выбранным условиям страницы не найдены';
$_['error_replace_text'] = 'Пожалуйста, укажите искомый текст';
$_['error_add_text'] = 'Пожалуйста, укажите добавляемый текст';
$_['error_add_text_position'] = 'Пожалуйста, укажите текст поиска позиции';