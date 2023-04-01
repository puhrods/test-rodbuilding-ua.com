<?php

// COMMON
$_['heading_title'] = 'OCFilter product filter <sup style="font-weight: normal;">' . (defined('OCF_VERSION') ? OCF_VERSION : '') . '</sup> <a href="http://feofan.club/" target="_blank" title="feofan.club" style="color:#ff7361"><i class="fa fa-download"></i></a> <a href="https://t.me/feofanchat/" target="_blank" title="HELP" style="color:#233746"><i class="fa fa-info-circle"></i></a>';
$_['heading_title_setting'] = 'OCFilter product filter';
$_['button_apply'] = 'Apply';
$_['text_module'] = 'Module';
$_['text_edit'] = 'OCFilter product filter settings <span class="badge" data-toggle="tooltip" data-placement="top" title="%s">%s</span>';
$_['text_select'] = '-- Select --';
$_['text_selected'] = 'Selected';
$_['text_success'] = 'You have successfully updated the settings for the &laquo;OCFilter Product Filter&raquo;!';
$_['text_filter_list'] = 'Filters';
$_['text_filter_page_list'] = 'SEO Pages';
$_['text_faq'] = 'FAQ';
$_['text_documentation'] = 'Documentation';
$_['text_ocfilter'] = 'OCFilter';
$_['text_ocfilter_filter'] = 'Filters';
$_['text_ocfilter_page'] = 'Pages';
$_['text_ocfilter_setting'] = 'Settings';
$_['text_loading'] = '<i class=\'fa fa-refresh fa-spin\'></i> Loading..';
$_['text_complete'] = '<i class=\'fa fa-check\'></i> Done';

$_['text_filters'] = 'filters';
$_['text_values'] = 'filter values';

$_['entry_sort_order'] = 'Sorting';
$_['text_begin'] = 'Beginning';
$_['text_after'] = 'At the end';
$_['help_sort_order'] = 'Click the text field to specify the exact position';

$_['entry_type'] = 'Type';

// Error
$_['error_permission'] = 'Warning: you do not have permission to edit this module!';
$_['error_copy_type'] = 'Please specify the type of future filters';
$_['error_license'] = 'The license key is invalid!';
$_['error_license_empty'] = 'Please enter a license key!';

// TAB GENERAL
$_['tab_general'] = 'General';

$_['entry_license'] = 'License Key';
$_['help_license'] = 'Please enter the license key you received upon purchase. If you have not received the key or if you have other problems with activating the module, write to <a href="mailto:opencart.ocfilter@gmail.com?subject=Problem%20with%20OCFilter%20Activation">opencart.ocfilter@gmail.com</a>';

$_['entry_status'] = 'Status';
$_['help_status'] = 'Enables or disables the module';

$_['entry_category_visibility'] = 'Visibility in categories';
$_['text_category_visibility_default'] = 'As specified by filters';
$_['help_category_visibility_default'] = 'Filters will be displayed only in the categories specified in the filter edit form';
$_['text_category_visibility_parent'] = 'Show in parent';
$_['help_category_visibility_parent'] = 'Make filters <b>from child</b> categories <b>to parent</b> categories even if they are not explicitly assigned to them';
$_['text_category_visibility_last_level'] = 'Display only at the last level';
$_['help_category_visibility_last_level'] = 'The module will only work at the <b>last level</b> of category nesting';

$_['entry_hide_categories'] = 'Hide categories when filters are selected';

$_['entry_only_instock'] = 'Only work with items in stock';
$_['help_only_instock'] = 'This means that products with zero quantity will not be included in the search filters.<br />If a filter value has all products out of stock, it will also be hidden.';

$_['entry_search_button'] = 'Filter by button';
$_['help_search_button'] = 'Allows you to first select the required filters, then search for products';

$_['entry_cache'] = 'Data Caching';
$_['help_cache'] = 'Manage module data caching. Attention! Use disconnect for debugging purposes only. The included cache significantly speeds up the operation of the module';

$_['entry_cache_store'] = 'Cache Store';
$_['text_cache_db'] = 'Database';
$_['text_cache_system'] = 'As set by the system (%s)';

$_['entry_debug'] = 'Debugging DB queries';
$_['help_debug'] = '';

$_['nav_general_compatibility'] = 'Compatibility with other modules';

$_['entry_module_hpm_group_products'] = 'Group found products';
$_['help_module_hpm_group_products'] = 'When searching by filter, show only grouped products';

$_['entry_module_hpm_group_counter'] = 'Groups in the product counter';
$_['help_module_hpm_group_counter'] = 'The counter of products for each value will display the number of grouped products';

// TAB SPECIAL FILTERS
$_['tab_special_filter'] = 'Special Filters';

// -------------------------------------------------------------- //
$_['tab_price'] = 'Price';

$_['entry_special_price'] = 'Filter by price';
$_['entry_special_price_logarithmic'] = 'Logarithmic price scale';

$_['entry_consider_tax'] = 'Include taxes';
$_['help_consider_tax'] = 'Taxes will be added to prices based on customer group';

$_['nav_price_source'] = 'Price sources <div class="small">can be combined or left alone</div>';

$_['entry_special_price_consider_regular_price'] = 'Regular price';
$_['help_special_price_consider_regular_price'] = 'Use the standard (regular) price of the product';

$_['entry_special_price_consider_discount'] = 'Discounted price';
$_['help_special_price_consider_discount'] = 'Includes discounted prices';

$_['entry_special_price_consider_special'] = 'Promotional price';
$_['help_special_price_consider_special'] = 'Consider Promotional Price';

$_['entry_special_price_consider_option'] = 'Product options price';
$_['help_special_price_consider_option'] = 'The price range will expand to the price range specified in the options.<br />Available operators for price calculation: +, -, *, /, =';

// -------------------------------------------------------------- //
$_['tab_manufacturer'] = 'Manufacturer';

$_['entry_special_manufacturer'] = 'Filter by Manufacturer';
$_['help_special_manufacturer'] = 'Allows you to filter products by standard manufacturer';

$_['entry_special_manufacturer_dropdown'] = 'Dropdown List';
$_['entry_special_manufacturer_image'] = 'Display images';

// -------------------------------------------------------------- //
$_['tab_discount'] = 'Discount';

$_['entry_special_discount'] = 'Discount Only';
$_['help_special_discount'] = 'The filter offers to select products with a discounted price';
$_['entry_special_discount_consider_special'] = 'Consider promotions';
$_['entry_special_discount_consider_discount'] = 'Consider discounts';

// -------------------------------------------------------------- //
$_['tab_newest'] = 'New';

$_['entry_special_newest'] = 'Newest only';
$_['help_special_newest'] = 'The filter offers to select only new products';

$_['entry_special_newest_interval'] = 'Feature of a new product';
$_['text_special_newest_interval_hour'] = 'Hours';
$_['text_special_newest_interval_day'] = 'Days';
$_['text_special_newest_interval_week'] = 'Weeks';
$_['text_special_newest_interval_month'] = 'Months';
$_['help_special_newest_interval'] = 'A product is considered new if it is added no later than the specified period';

// -------------------------------------------------------------- //
$_['tab_dimension'] = 'Dimensions and Weight';

$_['entry_special_weight'] = 'Product Weight';
$_['entry_special_width'] = 'Width';
$_['entry_special_height'] = 'Height';
$_['entry_special_length'] = 'Length';

// -------------------------------------------------------------- //
$_['tab_stock'] = 'Stock';

$_['entry_special_stock'] = 'Filter by stock availability';
$_['help_special_stock'] = 'Allows you to filter products by stock availability';

$_['entry_special_stock_method'] = 'Method';
$_['text_special_stock_method_by_status_id'] = 'By item stock status';
$_['text_special_stock_method_by_quantity'] = 'By product quantity';

$_['entry_special_stock_out_value'] = 'Show &laquo;Out of stock&raquo; value';

// TAB SEO
$_['tab_seo'] = 'SEO';

// -------------------------------------------------------------- //
$_['nav_seo_page'] = 'SEO Pages';

$_['entry_sitemap'] = 'Sitemap of filter SEO pages';
$_['entry_sitemap_link'] = 'Link to Sitemap';

$_['entry_page_category_link_status'] = 'Display links to category pages';
$_['help_page_category_link_status'] = 'Links to SEO pages will be displayed in categories as tags. The names of the links are taken from the &laquo;Name&raquo; field';

$_['entry_page_category_link_position'] = 'Position of links in a category';
$_['text_page_category_link_above'] = 'Above Products';
$_['text_page_category_link_under'] = 'Under Products';
$_['text_page_category_link_both'] = 'Distribute equally';

$_['entry_page_module_link_status'] = 'Display links in module';
$_['help_page_module_link_status'] = 'Links to SEO pages will be displayed at the top of the module';

$_['entry_page_module_link_title'] = 'Link block title';
$_['page_module_link_title'] = 'Popular Filters';

$_['entry_page_product_link_status'] = 'Display links in product characteristics tab';
$_['help_page_product_link_status'] = 'Links to SEO pages can be linked to product characteristics';

$_['entry_page_product_link_relation_type'] = 'The logic for linking pages to attributes';
$_['text_page_product_link_relation_complete'] = 'Full match';
$_['text_page_product_link_relation_partial'] = 'Partial match';
$_['help_page_product_link_relation_type'] = '<b>Full match</b> the product will be applied to the landing pages with all filters that match the product attributes.<br />For example, you created a page for the filter &laquo;Color: Red&raquo;, &laquo;Size: Medium&raquo;. The page will be displayed only in those products that have the attribute <br />&laquo;Color: red&raquo; <b>and</b> &laquo;Size: medium&raquo;.<br /><b>Partial Match</b> will link products to pages, provided that at least one SEO page filter appears in the product attributes.';

$_['entry_url_suffix'] = 'End link';
$_['placeholder_url_suffix'] = 'For example, .html';

// -------------------------------------------------------------- //
$_['nav_seo_meta'] = 'Automatic meta data <div class="small">This data is only needed for your customers and only for those filters that do not have a landing page specified. The search engine will not see them</div>';

$_['entry_add_meta'] = 'Add to meta data';
$_['text_add_meta_filter_value'] = 'Filters and Values';
$_['text_add_meta_value'] = 'Values ​​Only';
$_['text_add_meta_disabled'] = 'Don\'t add';

$_['entry_meta_filter_separator'] = 'Filter separator';
$_['entry_meta_value_separator'] = 'Value separator';
$_['entry_meta_lowercase'] = 'In lower case';
$_['entry_add_meta_limit'] = 'Add no more than';

// -------------------------------------------------------------- //
$_['nav_seo_misc'] = 'Miscellaneous';

$_['entry_category_breadcrumb'] = 'Add category breadcrumb';
$_['help_category_breadcrumb'] = 'Add breadcrumb with selected filters (or SEO page) on the category page. The effectiveness of this setting for SEO has not been studied, so before activating it, it is better to consult with your SEO specialist';

$_['entry_product_breadcrumb'] = 'Add product breadcrumb';
$_['help_product_breadcrumb'] = 'Add breadcrumb with selected filters (or SEO page) to product page between category and product. As in the case above, the need to enable this setting requires clarification';

// TAB APPEARANCE
$_['tab_appearance'] = 'Appearance';

// -------------------------------------------------------------- //
$_['nav_appearance_module'] = 'Module and mobile version';

$_['entry_module_heading_title'] = 'Module Title';
$_['module_heading_title'] = 'Filter';

$_['entry_mobile_button_text'] = 'Text of the button for calling the mobile version';
$_['mobile_button_text'] = 'Filter';

$_['entry_mobile_button_position'] = 'Position of the mobile version button';
$_['text_mobile_button_position_fixed'] = 'Floating';
$_['text_mobile_button_position_static'] = 'Static over products';
$_['text_mobile_button_position_both'] = 'Both options';

$_['entry_mobile_max_width'] = 'Mobile screen width';
$_['help_mobile_max_width'] = 'Specify the maximum screen width (in pixels) at which the module will remain in mobile mode.<br />The default is 767 pixels, which is equal to the &laquo;sm&raquo; toggle value for Bootstrap 3';

$_['entry_mobile_placement'] = 'Mobile version placement';
$_['text_mobile_placement_left'] = 'Left';
$_['text_mobile_placement_right'] = 'Right';

$_['entry_mobile_remember_state'] = 'Remember the state of the window of the mobile version';
$_['help_mobile_remember_state'] = 'Enabling this setting will restore the mobile version window after page reload';

// -------------------------------------------------------------- //
$_['nav_appearance_filter'] = 'Filters';

$_['entry_theme'] = 'Theme';
$_['text_theme_light'] = 'Light';
$_['text_theme_light_block'] = 'Light Block';

$_['entry_show_first_limit'] = 'Show first ones only';
$_['help_show_filters_limit'] = 'Specify the limit for the number of filters that will be displayed in the product filter module. To display all filters, specify 0';

$_['entry_hidden_filters_lazy_load'] = '&laquo;Lazy&raquo; loading filters';
$_['help_hidden_filters_lazy_load'] = 'Load hidden filters in the background (AJAX).<br />This setting can lighten pages with more filters and improve Google PageSpeed';

$_['entry_hide_single_value'] = 'Hide single value filters';
$_['help_hide_single_value'] = 'Does not display filters with only one active value';

$_['entry_slider_input'] = 'Entry fields for sliders';
$_['help_slider_input'] = 'Allows you to enter values ​​for sliders in separate inputs';

$_['entry_show_diagram'] = 'Diagram';
$_['help_show_diagram'] = 'Graphical display of the ratio of the number of products to the value of the range';

$_['entry_slider_pips'] = 'Scale with values';

$_['entry_show_selected'] = 'Show selected options';
$_['help_show_selected'] = 'Displays a block of selected parameters with the option to exclude them from the request';

// -------------------------------------------------------------- //
$_['nav_appearance_filter_value'] = 'Values';

$_['entry_show_counter'] = 'Show product counter';
$_['help_show_counter'] = 'Displays the number of products for each value.<br />This parameter does not affect page load speed';

$_['help_show_values_limit'] = 'Specify the limit for the number of values ​​that will be displayed in the product filter module for each filter. To display all values, specify 0';

$_['entry_hidden_values_lazy_load'] = '&laquo;Lazy&raquo; loading values';
$_['help_hidden_values_lazy_load'] = 'Same as filters. When this option is enabled, the hidden filter values ​​are loaded in the background.';

$_['entry_hide_empty_values'] = 'Hide inactive values';
$_['help_hide_empty_values'] = 'Completely hides inactive (with zero product value) filter values. If all values ​​are hidden, the filter itself is hidden';

$_['entry_values_auto_column'] = 'Split Values ​​Into Columns';
$_['help_values_auto_column'] = 'Automatically split values ​​into columns (2 or 3) depending on the length of their names.';

// TAB PLACEMENT
$_['tab_placement'] = 'Placement';
$_['text_placement'] = 'Specify layouts and filters that should be displayed on them.<br />You also need to add the module to the corresponding layout in the <a href="%s" class="alert-link" target="_blank"><u>Design - Layouts</u></a><br /><b class="text-danger">Warning!</b> Do not use this setting to display the module in the &laquo;Category&raquo;, &laquo;Manufacturer&raquo;, &laquo;Specials&raquo; and &laquo;Search&raquo; layouts. Just add a module to these layouts and that\'s it.';
$_['text_new_placement'] = '-- New --';

$_['button_add_placement'] = 'Add placement';
$_['button_remove_placement'] = 'Remove placement';

$_['entry_placement_layout'] = 'Please enter a layout';
$_['entry_placement_filter'] = 'Add filters';
$_['placeholder_autocomplete'] = 'Start typing the name';

// TAB COPY FILTERS
$_['tab_copy'] = 'Copy Filters';
$_['text_confirm_truncate_copy'] = 'Are you sure you want to clear\nall existing OCFilter filters?';

// -------------------------------------------------------------- //
$_['nav_copy_source'] = 'Filter Sources';

$_['entry_copy_attribute'] = 'Copy Attributes';
$_['text_copy_attribute_total'] = 'Attributes: <b>%s</b>, Values: <b>%s</b>';

$_['entry_copy_group_as_attribute'] = 'Attribute groups as filters';
$_['help_copy_group_as_attribute'] = 'If <b>attribute groups</b> are <b>filters</b>, attributes are <b>values</b>, and in the product form in the &laquo;Attributes&raquo; tab field &laquo;<b>Text</b>&raquo; (right) not filled in - select &laquo;Yes&raquo;';

$_['entry_copy_attribute_data'] = 'Data to copy';
$_['help_copy_attribute_data'] = 'Specify the attribute data to copy to filters.<br />You can select specific attributes, categories, or attribute groups (if the mode is &laquo;<kbd>Attribute groups as filters</kbd>&raquo;).<br />Any data can be excluded from copying by the corresponding option.<br />Button <kbd>Auto</kbd> allows you to get a list of the most likely suitable attributes for the selected selection mode.<br />If these fields remain empty, all attributes will be copied.';

$_['entry_copy_exclude'] = 'Exclude';

$_['placeholder_copy_attribute_autocomplete'] = 'Attribute';
$_['placeholder_copy_attribute_group_autocomplete'] = 'Attribute Group';
$_['placeholder_copy_category_autocomplete'] = 'Category';

$_['button_clear'] = 'Clear';
$_['button_auto'] = 'Auto';

$_['entry_copy_filter'] = 'Copy default filters';
$_['text_copy_filter_total'] = 'Filters: <b>%s</b>, Values: <b>%s</b>';

$_['entry_copy_option'] = 'Copy product options';
$_['text_copy_option_total'] = 'Options: <b>%s</b>, Values: <b>%s</b>';

$_['entry_copy_option_in_stock'] = 'In Stock Only';
$_['help_copy_option_in_stock'] = 'Copy options with positive stock only';

// -------------------------------------------------------------- //
$_['nav_copy_filter'] = 'Settings for the resulting filters <div class="small">Which will appear after copying. These settings do not affect existing filters.</div>';

$_['entry_copy_type'] = 'Type of copied filters';
$_['entry_copy_dropdown'] = 'Dropdown';

$_['entry_copy_status'] = 'Status of copied filters';
$_['help_copy_status'] = 'The status of new filters that will be created from the specified sources.<br />Regardless of the selected status, those filters that have no values ​​or are not tied to products or categories will be disabled';

// -------------------------------------------------------------- //
$_['nav_copy_other'] = 'Other';

$_['entry_copy_value_separator'] = 'Value separator';
$_['placeholder_copy_value_separator'] = 'For example, «/»';
$_['help_copy_value_separator'] = 'To split one composite filter value into single values, use the filter separator.<br />For example, to separate the value &laquo;Green / Red / Blue&raquo; into separate colors, use the separator &laquo;/&raquo;.<br />You can use up to 3 separators at the same time';

$_['entry_copy_clear_filter'] = 'Clear existing OCFilter filters';
$_['help_copy_clear_filter'] = 'Only previously copied filters will be removed. Manually added ones will remain intact';

$_['entry_copy_category'] = 'Bind filters to categories';
$_['help_copy_category'] = 'When this option is selected, all existing OCFilter links to categories will be updated. Filters added manually will not change their links to categories.';

// -------------------------------------------------------------- //
$_['nav_copy_auto'] = 'Automation';

$_['text_copy_auto_code_php'] = 'PHP Code to call copy with current settings';
$_['help_copy_auto_code_php'] = 'Paste this code at the end of the product import script, parsing script or any other place where it makes sense to cause copying.';

$_['text_copy_auto_code_js'] = 'JS Code to call copy with current settings';
$_['help_copy_auto_code_js'] = 'The code can be placed anywhere in the template, invoked by event, etc.';

$_['text_copy_auto_cron'] = 'Command to be called by cron (scheduler)';
$_['help_copy_auto_cron'] = 'Convenient cron period editor <a href="https://crontab.guru/" target="_blank">here</a><br />After specifying copy parameters <b>make sure</b> save the settings';
$_['text_cron_select_period'] = 'Select a copy period<br />or enter your own';
$_['text_cron_period'] = 'Period';
$_['text_cron_period_01'] = 'Every hour';
$_['text_cron_period_02'] = 'Every 3 hours';
$_['text_cron_period_03'] = 'Every day at 4 AM';
$_['text_cron_period_04'] = 'Every Sunday at 4 AM';
$_['text_cron_period_05'] = 'Every 5 hours on weekends';
$_['text_cron_period_06'] = 'Every 1st day of a new month';
$_['text_or'] = 'or';
$_['text_cron_period_manual'] = 'Custom period';
$_['text_cron_bin'] = 'Command for adding a call via PHP bin';
$_['text_cron_wget'] = 'Allow Calling Through Wget';

$_['entry_copy_now'] = 'Copy Now';
$_['button_copy'] = 'Copy';
$_['entry_copy_save_setting'] = 'And save all current copy settings';

$_['error_install_modification_not_found'] = 'The ' . DIR_SYSTEM . 'ocfilter.ocmod.xml modificator was not found. Copy the modificator to the specified path and retry the installation.';
$_['error_install_modification_update'] = 'Please update the OCMOD modificators with the browser console running (F12) and try configuring the module again.';
$_['error_install_tables'] = 'Please remove the module from the list of modules and install by clicking the &laquo;Install&raquo; button from the same list of modules.';