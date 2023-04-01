<?php

// Heading
$_['heading_title']          = 'OCFilter SEO Pages';

// Buttons
$_['button_add_page'] = 'Add Page';
$_['button_apply_add'] = 'Save and add more';
$_['button_edit_pages'] = '<i class="fa fa-pencil"></i> Edit Pages';
$_['button_add_pages'] = '<i class="fa fa-plus"></i> Add Pages';
$_['button_show'] = 'Open in catalog';

// Tabs
$_['tab_general']            = 'General';
$_['tab_data']               = 'Data';
$_['tab_relation']           = 'Relation';
$_['tab_display']            = 'Location';

// Text
$_['text_success']           = 'You have successfully changed your SEO Pages!';
$_['text_list']              = 'List of pages';
$_['text_faq'] = 'FAQ';
$_['text_documentation'] = 'Documentation';
$_['text_add']               = 'Add SEO Page';
$_['text_edit']              = 'Edit SEO Page';
$_['text_default']           = 'Default';
$_['text_dynamic']           = 'Dynamic page';
$_['text_type_static']       = 'Static';
$_['text_type_dynamic']      = 'Dynamic';
$_['text_confirm_delete_page'] = '<p>Are you sure you want to delete the selected pages?</p><div class=\'text-right\'><button type=\'button\' data-dismiss=\'popover\' class=\'btn btn-default\'>No</button> <button type=\'button\' onclick=\'$(`#form-list`).submit()\' class=\'btn btn-danger\'>Yes</button></div>';
$_['text_loading']              = '<i class=\'fa fa-refresh fa-spin\'></i> Loading..';
$_['text_mask_vars'] = 'Auto Substitution Variables';
$_['text_info_mask'] = '<p class="h4">Available auto-substitution variables</p><p class="h4">The variable specified in the text will be replaced with the selected filters</p>';
$_['text_info_mask_static'] = '<i class="fa fa-exclamation-triangle"></i> Auto-substitution is not available in static mode';
$_['text_info_mask_filter'] = '<i class="fa fa-info-circle"></i> The mask is not used with this set of filters';
$_['text_info_mask_filter_select'] = 'Filters must be selected';
$_['text_batch_edit'] = '<i class="fa fa-edit"></i> Bulk edit';
$_['text_batch_add'] = '<i class="fa fa-plus"></i> Bulk add';
$_['text_action_replace_text'] = 'Replace text';
$_['text_action_add_text'] = 'Add text';
$_['text_action_update'] = 'Data';
$_['text_action_delete'] = 'Delete';
$_['text_replace_on'] = 'on';
$_['text_add_prepend'] = 'to the beginning';
$_['text_add_append'] = 'to the end';
$_['text_destination'] = 'to';
$_['text_destination_all'] = 'any field';
$_['text_destination_name'] = 'name';
$_['text_destination_heading_title'] = 'title';
$_['text_destination_description_top'] = 'top description';
$_['text_destination_description_bottom'] = 'bottom description';
$_['text_destination_meta_title'] = 'meta title';
$_['text_destination_meta_description'] = 'meta description';
$_['text_destination_meta_keyword'] = 'meta keyword';
$_['text_destination_seo_url'] = 'seo url alias';
$_['text_target'] = 'for';
$_['text_target_all'] = 'all';
$_['text_target_filter'] = 'all by specified filters';
$_['text_target_selected'] = 'selected on this page';
$_['text_discard'] = 'Don\'t change';
$_['text_select_categpry'] = 'Select a Category';
$_['text_select_filter'] = 'Select Filters';
$_['text_display'] = 'output';
$_['text_slider_not_available'] = 'Sliders are available in advanced form';

// Column
$_['column_name']            = 'Name';
$_['column_category']        = 'Category';
$_['column_status']          = 'Status';
$_['column_view']            = 'Output';
$_['column_action']          = 'Action';

// Entry
$_['entry_type']             = 'Data Substitution Mode';
$_['entry_name']             = 'Name';
$_['entry_heading_title']    = 'Heading (H1)';
$_['entry_description_top']  = 'Top Description';
$_['entry_description_bottom']  = 'Bottom description';
$_['entry_meta_title'] 	   = 'Page Title';
$_['entry_meta_keyword'] 	   = 'Meta Keywords';
$_['entry_meta_description'] = 'Meta Description';
$_['entry_keyword']          = 'SEO URL alias';
$_['entry_category']         = 'Category';
$_['entry_filter']           = 'Filters';
$_['entry_filter_value']     = 'Filter values';
$_['entry_status']           = 'Status';
$_['entry_display_module']           = 'Display in module';
$_['entry_display_category']           = 'Display on category page';
$_['entry_display_product']           = 'Display on product page';
$_['entry_display_sitemap']           = 'Display in sitemap';
$_['entry_display_code'] = 'Display code anywhere';
$_['entry_store']            = 'Store';
$_['entry_layout']           = 'Layout';
$_['entry_edit_status'] = 'Status';
$_['entry_edit_display_category'] = 'Category';
$_['entry_edit_display_product'] = 'Product';
$_['entry_edit_display_module'] = 'Module';
$_['entry_edit_display_sitemap'] = 'Sitemap';

// Help
$_['help_add'] = 'The name of the page is the name of the link that leads to it. This name is also displayed in the list of pages in the control panel.<br>
This field <b>supports</b> substitution variables. If the field is empty, then the heading (h1) will be used';
$_['help_heading_title'] = 'The page header supports autocorrect variables';
$_['help_name'] = '';
$_['help_meta_title'] = 'This text will be displayed in the meta title tag, variables are also supported';
$_['help_description_top'] = 'The text is displayed above the category products';
$_['help_description_bottom'] = 'The text is displayed under the category products';

$_['help_keyword'] = 'Use letters, numbers, _, -, and autocorrect variables. Other characters are not allowed. The alias must be unique for the whole system or within the specified category of pages';

$_['help_add_keyword'] = 'Use letters, numbers, _, -, and autocorrect variables. Other characters are not allowed. The alias must be unique for the whole system or within the specified category of pages';

$_['help_type'] = 'The page can be static or dynamic.<br /><br />
<b>Static type</b>:<br />
- data only works for one fixed set of filters<br />
- the page can be displayed anywhere using the link code from the «location» tab<br />
- the page can be displayed in the module block<br /><br />
<b>Dynamic type</b>:<br />
- data works for multiple filter groups<br />
- auto-substitution variables for selected filters are available';

$_['help_ocfilter_filter'] = 'Specify the filters that this SEO page will work with.<br />
Special filters are <b class="text-primary">highlighted in blue</b>, disabled filters are available at the end of the list.<hr />

If the page is <b>dynamic</b>, then it will be triggered when at least one value is selected from each filter. For example:<br />
Filters selected for the page:<br /><br />
- color: red, blue<br />
- size: M, L<br /><br />
The page will work when you select<br />- color: [red <b>and/or</b> blue] <br /><b>and</b><br />- size: [M <b>and/or</b> L]<hr />

If the page is <b>static</b>, then it will work when all the specified values ​​are selected. For example:<br />
Filters selected for the page:<br /><br />
- color: red, blue<br />
- size: M, L<br /><br />
The page will be triggered when you select<br />- color: red <b>and</b> blue<br /><b>and</b><br />- size: M <b>and</b> L';

// Placeholder
$_['placeholder_text'] = 'Text';
$_['placeholder_display_code'] = 'Display code is not available in dynamic mode';

// Error
$_['error_warning']          = 'Check the form for errors!';
$_['error_permission']       = 'You do not have permission to change SEO pages!';
$_['error_name']             = 'SEO page name must be up to %s characters!';
$_['error_heading_title']    = 'The title of the SEO page must be between %s and %s characters!';
$_['error_meta_title']       = 'Meta title must be between %s and %s characters!';
$_['error_keyword_exist']   = 'The specified SEO URL alias is already in use';
$_['error_keyword_exist_page_id'] = '<a href="%s" target="_blank">SEO page</a>';
$_['error_keyword_exist_category_id'] = '<a href="%s" target="_blank">category</a>';
$_['error_keyword_exist_product_id'] = '<a href="%s" target="_blank">product</a>';
$_['error_keyword_exist_information_id'] = '<a href="%s" target="_blank">information page</a>';
$_['error_keyword_exist_manufacturer_id'] = '<a href="%s" target="_blank">by manufacturer</a>';
$_['error_mask']             = 'This filter combination requires a mask';
$_['error_category']         = 'Please enter a category!';
$_['error_filter']           = 'Please specify filters!';
$_['error_target_empty']     = 'No pages found for the selected conditions';
$_['error_replace_text'] = 'Please enter the text you are looking for';
$_['error_add_text'] = 'Please enter the added text';
$_['error_add_text_position'] = 'Please enter a position search text';