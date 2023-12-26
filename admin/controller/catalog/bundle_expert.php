<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ControllerCatalogBundleExpert extends Controller {
    private $error = array();
    private $token_name = array();
    private $token_value = array();
    private $path_extension_module = '';
    private $show_reg_tab = 0;

    public function __construct($registry) {

        parent::__construct($registry);

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->token_name = 'token=';
            $this->token_value = $this->session->data['token'];
        }else{
            $this->token_name = 'user_token=';
            $this->token_value = $this->session->data['user_token'];
        }

        if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<')) {
            $this->path_extension_module = 'extension/module';
        }else {
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $this->path_extension_module = 'extension/extension';
            } else {
                $this->path_extension_module = 'marketplace/extension';
            }
        }
    }

    public function index() {
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');
        $this->load->language('catalog/bundle_expert');

        $this->document->addScript('view/javascript/bundle-expert/color-picker/js/bootstrap-colorpicker.js');
        $this->document->addStyle('view/javascript/bundle-expert/color-picker/css/bootstrap-colorpicker.css');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('catalog/bundle_expert_widget');

        
        $bundle_expert_version_prev = $this->config->get('bundle_expert_version');
        
        
        
       
        $this->load->model('catalog/bundle_expert');
        $this->model_catalog_bundle_expert->update();

        if(!isset($bundle_expert_version_prev) || version_compare($bundle_expert_version_prev, $this->bundle_expert->bundle_expert_version, '<')){



            $this->cache->delete('product.bundle_expert');
            $this->bundle_expert->dbCacheClear();

            $this->bundle_expert->deleteMinifyJsCss();


            
        }else{
            
        }

        
        








        
        if(isset($bundle_expert_version_prev) && version_compare($bundle_expert_version_prev, '1.5.5.0', '<')){
            $this->load->model('catalog/bundle_expert');
            $this->model_catalog_bundle_expert->update_files();
        }

        
        

        $this->model_setting_setting->editSetting('bundle_expert_version', array('bundle_expert_version'=>$this->bundle_expert->bundle_expert_version));










        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            
            foreach ($this->request->post['bundle_expert_settings']['checkout_pages'] as $index=>$checkout_page){
                $checkout_page = trim($checkout_page);
                $checkout_page = trim($checkout_page,'\\');
                $checkout_page = trim($checkout_page,'/');
                $this->request->post['bundle_expert_settings']['checkout_pages'][$index] = $checkout_page;
            }

            $css_js_files_list = array(
                'magnific_popup',
                'moment',
                'datetimepicker',
                'bootstrap_3',
                'bootstrap_3_js',
                'font_awesome',
            );

            $css_js_files = array();

            foreach ($css_js_files_list as $key){
                if(isset($this->request->post['bundle_expert_settings']['css_js_files'][$key])){
                    $css_js_files[$key] = 1;
                }else{
                    $css_js_files[$key] = 0;
                }
            }
            $this->request->post['bundle_expert_settings']['css_js_files'] = $css_js_files;


            $this->model_setting_setting->editSetting('bundle_expert', $this->request->post);

            
            $this->model_setting_setting->editSetting('module_bundle_expert', $this->request->post);

            $this->bundle_expert->deleteMinifyJsCss();

            if($this->request->post['bundle_expert_settings']['custom_header_cart_js_status']) {

                $this->load->model('catalog/bundle_expert_custom_js');

                $this->model_catalog_bundle_expert_custom_js->createHeaderCartJsFile($this->request->post['bundle_expert_settings']['custom_header_cart_js']);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link($this->path_extension_module, $this->token_name . $this->token_value. '&type=module', 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_status_customer'] = $this->language->get('entry_status_customer');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['text_checkout_pages'] = $this->language->get('text_checkout_pages');
        $data['text_cart_kit_color_1'] = $this->language->get('text_cart_kit_color_1');
        $data['text_cart_kit_color_2'] = $this->language->get('text_cart_kit_color_2');
        $data['text_cart_kit_color_reset'] = $this->language->get('text_cart_kit_color_reset');

        
        $data['text_license_getting_key'] =  $this->language->get('text_license_getting_key');
        $data['text_license_market_id'] =  $this->language->get('text_license_market_id');
        $data['text_license_order_number'] =  $this->language->get('text_license_order_number');
        $data['text_license_domain_name'] =  $this->language->get('text_license_domain_name');
        $data['text_license_paste_domain'] =  $this->language->get('text_license_paste_domain');
        $data['text_license_get_key_description'] =  $this->language->get('text_license_get_key_description');
        $data['text_license_button_get_key'] =  $this->language->get('text_license_button_get_key');
        $data['text_license_register'] =  $this->language->get('text_license_register');
        $data['text_license_license_key'] =  $this->language->get('text_license_license_key');
        $data['text_license_register_description'] =  $this->language->get('text_license_register_description');
        $data['text_license_button_register'] =  $this->language->get('text_license_button_register');
        $data['text_license_update_page'] =  $this->language->get('text_license_update_page');
        $data['text_license_info_text'] =  $this->language->get('text_license_info_text');
        $data['text_license_info_description'] =  $this->language->get('text_license_info_description');
        $data['text_license_button_reset_register'] =  $this->language->get('text_license_button_reset_register');
        $data['text_product_item_quantity_edit_default_value'] = $this->language->get('text_product_item_quantity_edit_default_value');
        $data['entry_settings'] =  $this->language->get('entry_settings');
        $data['entry_register'] =  $this->language->get('entry_register');
        $data['text_cart_items_limit'] =  $this->language->get('text_cart_items_limit');
        $data['text_cart_items_limit_help'] =  $this->language->get('text_cart_items_limit_help');
        $data['text_order_info_format'] =  $this->language->get('text_order_info_format');
        $data['text_order_info_format_help'] =  $this->language->get('text_order_info_format_help');

        $data['text_product_page'] =  $this->language->get('text_product_page');
        $data['text_add_to_cart_button'] =  $this->language->get('text_add_to_cart_button');
        $data['text_product_data'] =  $this->language->get('text_product_data');
        $data['text_price'] =  $this->language->get('text_price');
        $data['text_special'] =  $this->language->get('text_special');
        $data['entry_selectors'] =  $this->language->get('text_selectors');
        $data['entry_other'] =  $this->language->get('entry_other');
        $data['text_order_info_format_none'] =  $this->language->get('text_order_info_format_none');
        $data['text_order_info_format_list'] =  $this->language->get('text_order_info_format_list');
        $data['text_order_info_format_one_item'] =  $this->language->get('text_order_info_format_one_item');
        $data['text_price_parent'] =  $this->language->get('text_price_parent');
        $data['text_quantity'] =  $this->language->get('text_quantity');
        $data['text_js_library'] =  $this->language->get('text_js_library');
        $data['text_minify_js_css'] =  $this->language->get('text_minify_js_css');
        $data['text_custom_header_cart_js_status'] =  $this->language->get('text_custom_header_cart_js_status');
        $data['text_custom_header_cart_js_template'] =  $this->language->get('text_custom_header_cart_js_template');
        $data['text_custom_header_cart_js_template_select'] =  $this->language->get('text_custom_header_cart_js_template_select');
        $data['text_dynamic_update_product_as_kit_price'] =  $this->language->get('text_dynamic_update_product_as_kit_price');
        $data['text_dynamic_update_product_as_kit_price_button'] =  $this->language->get('text_dynamic_update_product_as_kit_price_button');
        $data['text_dynamic_update_product_as_kit_price_le'] =  $this->language->get('text_dynamic_update_product_as_kit_price_le');
        $data['text_dynamic_update_product_as_kit_price_le_button'] =  $this->language->get('text_dynamic_update_product_as_kit_price_le_button');
        $data['text_dynamic_update_cache_button'] =  $this->language->get('text_dynamic_update_cache_button');
        $data['text_dynamic_clear_cache_button'] =  $this->language->get('text_dynamic_clear_cache_button');
        $data['text_update_button'] =  $this->language->get('text_update_button');
        $data['text_clear_button'] =  $this->language->get('text_clear_button');
        $data['text_license_button_add_register'] =  $this->language->get('text_license_button_add_register');
        $data['text_button_minus'] =  $this->language->get('text_button_minus');
        $data['text_button_plus'] =  $this->language->get('text_button_plus');
        $data['text_product_as_kit_add_from_category_enable'] =  $this->language->get('text_product_as_kit_add_from_category_enable');
        $data['text_smart_js'] =  $this->language->get('text_smart_js');
        $data['text_smart_js_default'] =  $this->language->get('text_smart_js_default');
        $data['text_smart_js_variant_1'] =  $this->language->get('text_smart_js_variant_1');
        $data['text_disable_bundle_discount_in_customer_group'] =  $this->language->get('text_disable_bundle_discount_in_customer_group');
        $data['text_cache_widgets'] =  $this->language->get('text_cache_widgets');

        $data['add_subdomain_button_url'] =  $this->language->get('add_subdomain_button_url');


        $data['text_bundle_expert_version'] =  'BundleExpert v.'.$this->bundle_expert->bundle_expert_version;


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->token_name . $this->token_value, 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link($this->path_extension_module, $this->token_name . $this->token_value, 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/bundle_expert', $this->token_name . $this->token_value, 'SSL')
        );

        $data['action'] = $this->url->link('catalog/bundle_expert', $this->token_name . $this->token_value, 'SSL');

        $data['cancel'] = $this->url->link($this->path_extension_module, $this->token_name . $this->token_value. '&type=module', 'SSL');

        $data['token_value'] = $this->token_value;
        $data['token_name'] = $this->token_name;


        $data['bundle_expert_navigation'] = $this->load->controller('catalog/bundle_expert_navigation');

        if(isset($this->request->get['module_id'])){
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $this->load->model('extension/module');
                $module = $this->model_extension_module->getModule($this->request->get['module_id']);
            }else{
                $this->load->model('setting/module');
                $module = $this->model_setting_module->getModule($this->request->get['module_id']);
            }

            if($module){
                $widget = $this->model_catalog_bundle_expert_widget->getWidget($module['widget_id']);

                $widget_url = $this->url->link('catalog/bundle_expert_widget/edit', $this->token_name . $this->token_value . '&widget_id=' . $module['widget_id'] . '', 'SSL');

                $data['text_module_created_by_widget'] = sprintf($this->language->get('text_module_created_by_widget'), $widget_url, $widget['name']);
            }

            $data['open_mode'] = 'opencart_module_mode';

        }else{








            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $module_status_name = "bundle_expert_status";
            }else{
                $module_status_name = "module_bundle_expert_status";
            }
            $data['module_status_name'] = $module_status_name;
            if (isset($this->request->post[$module_status_name])) {
                $data['bundle_expert_status'] = $this->request->post[$module_status_name];
            } else {
                $data['bundle_expert_status'] = $this->config->get($module_status_name);
                if ($data['bundle_expert_status'] == null)
                    $data['bundle_expert_status'] = 1;
            }

            if (isset($this->request->post['bundle_expert_status_for_customer'])) {
                $data['bundle_expert_status_for_customer'] = $this->request->post['bundle_expert_status_for_customer'];
            } else {
                $data['bundle_expert_status_for_customer'] = $this->config->get('bundle_expert_status_for_customer');
                if ($data['bundle_expert_status_for_customer'] == null)
                    $data['bundle_expert_status_for_customer'] = 1;
            }



            if (isset($this->request->post['bundle_expert_settings'])) {
                $data['bundle_expert_settings'] = $this->request->post['bundle_expert_settings'];
            } else {
                $data['bundle_expert_settings'] = $this->config->get('bundle_expert_settings');
            }

            if ($data['bundle_expert_settings'] == null) {
                $data['bundle_expert_settings'] = array();
            }

            if(!isset($data['bundle_expert_settings']['checkout_pages'])){
                $data['bundle_expert_settings']['checkout_pages'] = array();
                $data['bundle_expert_settings']['checkout_pages'][]='checkout/cart';
                $data['bundle_expert_settings']['checkout_pages'][]='checkout/checkout';
            }

            if(!isset($data['bundle_expert_settings']['cart_color_handler_1'])){
                $data['bundle_expert_settings']['cart_color_handler_1'] = '#e1f6e0a8';
            }

            if(!isset($data['bundle_expert_settings']['cart_color_handler_2'])){
                $data['bundle_expert_settings']['cart_color_handler_2'] = '#f6e0ef6b';
            }

            if(!isset($data['bundle_expert_settings']['cart_items_limit'])){
                $data['bundle_expert_settings']['cart_items_limit'] =  array('status' => 1, 'limit' => '200');
            }

            if(!isset($data['bundle_expert_settings']['order_info_format'])){
                $data['bundle_expert_settings']['order_info_format'] =  'one_item';
            }

            if(!isset($data['bundle_expert_settings']['selectors']['product_page']['price'])){
                $data['bundle_expert_settings']['selectors']['product_page']['price'] = '#content .product-price-container';
            }
            if(!isset($data['bundle_expert_settings']['selectors']['product_page']['special'])){
                $data['bundle_expert_settings']['selectors']['product_page']['special'] = '#content .product-special-container';
            }
            if(!isset($data['bundle_expert_settings']['selectors']['product_page']['button'])){
                $data['bundle_expert_settings']['selectors']['product_page']['button'] = '#product #button-cart';
            }
            if(!isset($data['bundle_expert_settings']['selectors']['product_page']['product_data'])){
                $data['bundle_expert_settings']['selectors']['product_page']['product_data'] = '#content #product';
            }
            if(!isset($data['bundle_expert_settings']['selectors']['product_page']['quantity'])){
                $data['bundle_expert_settings']['selectors']['product_page']['quantity'] = '#product input[name=quantity]';
            }
            if(!isset($data['bundle_expert_settings']['selectors']['product_page']['price_parent'])){
                $data['bundle_expert_settings']['selectors']['product_page']['price_parent'] = '';
            }
            if(!isset($data['bundle_expert_settings']['selectors']['product_page']['button_plus'])){
                $data['bundle_expert_settings']['selectors']['product_page']['button_plus'] = '';
            }
            if(!isset($data['bundle_expert_settings']['selectors']['product_page']['button_minus'])){
                $data['bundle_expert_settings']['selectors']['product_page']['button_minus'] = '';
            }
            if(!isset($data['bundle_expert_settings']['product_as_kit_add_from_category_enable'])){
                $data['bundle_expert_settings']['product_as_kit_add_from_category_enable'] = 0;
            }

            if(!isset($data['bundle_expert_settings']['smart_js'])){
                $data['bundle_expert_settings']['smart_js'] = 0;
            }

            if(!isset($data['bundle_expert_settings']['css_js_files'])){
                $css_js_files = array(
                    'magnific_popup' => 1,
                    'moment' => 1,
                    'datetimepicker' => 1,
                    'bootstrap_3' => 0,
                    'bootstrap_3_js' => 0,
                    'font_awesome' => 0,
                );
            }else{
                $css_js_files = $data['bundle_expert_settings']['css_js_files'];
                if(!isset($css_js_files['bootstrap_3'])){
                    $css_js_files['bootstrap_3'] = 0;
                }
                if(!isset($css_js_files['bootstrap_3_js'])){
                    $css_js_files['bootstrap_3_js'] = 0;
                }
                if(!isset($css_js_files['font_awesome'])){
                    $css_js_files['font_awesome'] = 0;
                }
            }

            $data['css_js_files'] = array();

            foreach ($css_js_files as $key=>$enable){

                switch ($key){
                    case 'magnific_popup':
                        $data['css_js_files'][] = array(
                            'key'=>$key,
                            'enable'=>$enable,
                            'name'=>'magnific_popup.js',
                        );
                        break;
                    case 'moment':
                        $data['css_js_files'][] = array(
                            'key'=>$key,
                            'enable'=>$enable,
                            'name'=>'moment.js',
                        );
                        break;
                    case 'datetimepicker':
                        $data['css_js_files'][] = array(
                            'key'=>$key,
                            'enable'=>$enable,
                            'name'=>'datetimepicker.js',
                        );
                        break;
                    case 'bootstrap_3':
                        $data['css_js_files'][] = array(
                            'key'=>$key,
                            'enable'=>$enable,
                            'name'=>'bootstrap_335.min.css',
                        );
                        break;
                    case 'bootstrap_3_js':
                        $data['css_js_files'][] = array(
                            'key'=>$key,
                            'enable'=>$enable,
                            'name'=>'bootstrap_335.min.js',
                        );
                        break;
                    case 'font_awesome':
                        $data['css_js_files'][] = array(
                            'key'=>$key,
                            'enable'=>$enable,
                            'name'=>'font_awesome',
                        );
                        break;
                }
            }

            if(!isset($data['bundle_expert_settings']['css_js_minify'])){
                $data['bundle_expert_settings']['css_js_minify'] = 0;
            }

            if(!isset($data['bundle_expert_settings']['custom_header_cart_js_status'])){
                $data['bundle_expert_settings']['custom_header_cart_js_status'] = 0;
            }

            if(!isset($data['bundle_expert_settings']['custom_header_cart_js_template_id'])){
                $data['bundle_expert_settings']['custom_header_cart_js_template_id'] = -1;
            }

            if(!isset($data['bundle_expert_settings']['custom_header_cart_js'])){
                $data['bundle_expert_settings']['custom_header_cart_js'] = '';
            }

            $this->load->model('catalog/bundle_expert_custom_js');
            $data['custom_header_cart_js_templates'] = $this->model_catalog_bundle_expert_custom_js->getHeaderCartJsTemplates();

            if(!isset($data['bundle_expert_settings']['dynamic_update_product_as_kit_price_in_db'])){
                $data['bundle_expert_settings']['dynamic_update_product_as_kit_price_in_db'] = 0;
            }

            if(!isset($data['bundle_expert_settings']['dynamic_update_product_as_kit_price_in_db_le'])){
                $data['bundle_expert_settings']['dynamic_update_product_as_kit_price_in_db_le'] = 0;
            }

            if(!isset($data['bundle_expert_settings']['cache_widgets'])){
                $data['bundle_expert_settings']['cache_widgets'] = 0;
            }

            $data['display_kit_discount_not_customer_group'] = false;

            if(!isset($data['bundle_expert_settings']['kit_discount_not_customer_group'])){
                $data['bundle_expert_settings']['kit_discount_not_customer_group'] = array();
            }

            $this->load->model('customer/customer_group');

            $customer_groups = $this->model_customer_customer_group->getCustomerGroups();

            $data['customer_groups'] = array();

            $guest_group = array(
                'customer_group_id'=>-1,
                'name' =>$this->language->get('text_disable_bundle_discount_in_customer_guest_group')
            );

            array_unshift($customer_groups,$guest_group);

            foreach ($customer_groups as $customer_group){
                if(in_array($customer_group['customer_group_id'], $data['bundle_expert_settings']['kit_discount_not_customer_group'])){
                    $customer_group['discount_disable'] = true;
                }else{
                    $customer_group['discount_disable'] = false;
                }
                $data['customer_groups'][] = $customer_group;


            }

            if (isset($this->request->post['bundle_expert_license'])) {
                $data['bundle_expert_license'] = $this->request->post['bundle_expert_license'];
            } else {
                $data['bundle_expert_license'] = $this->config->get('bundle_expert_license');
            }

            if ($data['bundle_expert_license'] == null) {
                $data['bundle_expert_license'] = array();
            }

            if(!isset($data['bundle_expert_license']['license_order_number'])){
                $data['bundle_expert_license']['license_order_number'] = '';
            }

            if(!isset($data['bundle_expert_license']['license_domain'])){
                $data['bundle_expert_license']['license_domain'] = $_SERVER['SERVER_NAME'];
            }

            if(!isset($data['bundle_expert_license']['license_key'])){
                $data['bundle_expert_license']['license_key'] = '';
            }

            if(!isset($data['bundle_expert_license']['market_id'])){

                $data['bundle_expert_license']['market_id'] = $this->bundle_expert->market_id;
            }

            if(!isset($data['bundle_expert_license']['key'])){
                $data['bundle_expert_license']['key'] = '';
            }

            if(!isset($data['bundle_expert_license']['license_app'])){
                $data['bundle_expert_license']['license_app'] = '';
            }

            $data['order_info_format_values'] =  array(
                'none' => $this->language->get('text_order_info_format_none'),
                'list' => $this->language->get('text_order_info_format_list'),
                'one_item' => $this->language->get('text_order_info_format_one_item'),
            );

            $data['is_registered'] = $this->bundle_expert->isLicensed();

            $data['market_ids'] = $this->model_catalog_bundle_expert->getOrderMarkeplaces();

            $data['open_mode'] = 'default';

            $data['text_register_domain'] = '';

            $licenses_data = $this->config->get('bundle_expert_license');
            if(isset($licenses_data) && isset($licenses_data['license_domain'])) {
                $data['text_register_domain'] .= sprintf($this->language->get('text_register_domain'), $licenses_data['license_domain']);
                $data['text_register_domain'] .= '<br>';
            }




            $data['reset_button_url'] = $this->url->link('catalog/bundle_expert/resetRegister', $this->token_name . $this->token_value , 'SSL');

            if(!isset($data['bundle_expert_settings']['product_item_quantity_edit_default_value'])){
                $data['bundle_expert_settings']['product_item_quantity_edit_default_value'] = 0;
            }
        }


        $data['show_reg_tab'] = $this->show_reg_tab;
        $this->load->model('setting/store');
        $store_total = $this->model_setting_store->getTotalStores()+1;
        if($store_total>1) {
            $data['show_reg_tab'] = true;
            $data['show_reg_tab'] = false;
        }

        $license_data = $this->config->get('bundle_expert_license');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert.tpl', $data));
        }else{
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert', $data));
        }

    }

    public function addOrderFormBundleExpertData($params){

        $data = $params[0];
        $products = $params[1];

        $data['bundle_expert_installed'] = $this->model_catalog_bundle_expert->isModuleInstalled();
        $data['bundle_expert_licensed'] = $this->bundle_expert->isLicensed();

        if(!$this->model_catalog_bundle_expert->isModuleInstalled() || !$this->bundle_expert->isEnabled()) {
            $data['html_bundle_expert_order_form_add_fieldset'] = '';
            $data['html_bundle_expert_order_form_block'] = '';
            return $data;
        }

        $this->load->model('catalog/bundle_expert');
        $this->load->model('catalog/bundle_expert_kit');

        $param = array();
        $param['store_url'] = isset($data['store_url'])?$data['store_url']:'';

        if(isset($data['api_token'])) $param['api_token'] = $data['api_token']; 


        $data['html_bundle_expert_order_form_add_fieldset'] = $this->getOrderFormAddKitFieldset();
        $data['html_bundle_expert_order_form_block'] = $this->getOrderFormBlock($param);

        $product_as_kit_quantity = array();
        $product_as_kit_just_first = array();

        foreach ($data['order_products'] as $index => $product) {
            $cart_kit_info = $this->model_catalog_bundle_expert_kit->getOrderProductKitInfo($this->request->get['order_id'], $products[$index]['order_product_id'], $products[$index]['product_id']);
            $data['order_products'][$index]['cart_kit_info_encode'] = '';
            if ($cart_kit_info) {
                
                
                if(isset($cart_kit_info['has_product_as_kit_unique_id']) && !empty($cart_kit_info['has_product_as_kit_unique_id'])){















                    unset($data['order_products'][$index]);
                }else{
                    $data['order_products'][$index]['cart_kit_info_encode'] = base64_encode(serialize($cart_kit_info));
                    $data['order_products'][$index]['product_as_kit_info_encode'] = '';
                    $data['order_products'][$index]['order_id'] = $data['order_id'];

                }
            }
            $product_as_kit_info = $this->model_catalog_bundle_expert_kit->getOrderProductAsKitInfo($this->request->get['order_id'], $products[$index]['order_product_id'], $products[$index]['product_id']);
            if ($product_as_kit_info) {
                $data['order_products'][$index]['product_as_kit_info_encode'] = base64_encode(serialize($product_as_kit_info));
                $product_as_kit_quantity[$product_as_kit_info['product_as_kit_unique_id']] = $product['quantity'];
                $data['order_products'][$index]['order_id'] = $data['order_id'];
            }
        }



        return $data;
    }

    private function getOrderFormAddKitFieldset(){

        if(!$this->model_catalog_bundle_expert->isModuleInstalled() || !$this->bundle_expert->isEnabled()) {
            return '';
        }

        $this->load->language('catalog/bundle_expert');

        $data['text_add_kit'] = $this->language->get('text_add_kit');
        $data['text_choose_kit'] = $this->language->get('text_choose_kit');
        $data['text_edit_kit'] = $this->language->get('text_edit_kit');
        $data['text_loading'] = $this->language->get('text_loading');


        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $html =  $this->load->view('catalog/bundle_expert/bundle_expert_order_form_add_fieldset.tpl', $data);
        }else{
            $html =  $this->load->view('catalog/bundle_expert/bundle_expert_order_form_add_fieldset', $data);
        }

        return $html;
    }

    private function getOrderFormBlock($param){

        if(!$this->model_catalog_bundle_expert->isModuleInstalled() || !$this->bundle_expert->isEnabled()) {
            return '';
        }

        $store_url = $param['store_url'];

        $this->load->language('catalog/bundle_expert_kit');

        $this->load->model('catalog/bundle_expert');

        $data = array();

        $data['store_url'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

        $data['token'] = $this->token_value;

        $data['button_remove_kit'] = $this->language->get('button_remove_kit');;
        $data['button_edit_kit'] = $this->language->get('button_edit_kit');;
        $data['text_'] = 'text';

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
            $data['is_new_api'] = false;
        }else {
            $data['is_new_api'] = true;
        }

        $data['https_catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

        $data['token_value'] = $this->token_value;
        $data['token_name'] = $this->token_name;

        $data['script_version'] = $this->bundle_expert->script_version;

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $data['api_token_name'] = $this->token_name;
            $data['api_token_value'] = $this->token_value;
        }else{
            $data['api_token_name'] = 'api_token=';
            $data['api_token_value'] = $param['api_token'];
        }

        $custom_style_file = DIR_CATALOG . 'view/theme/default/stylesheet/bundle_expert_custom.css';
        if(file_exists($custom_style_file)) {
            $data['add_bundle_expert_custom_css']= true;
        }else{
            $data['add_bundle_expert_custom_css']= false;
        }

        $custom_js_file =DIR_CATALOG . 'view/javascript/bundle-expert/bundle-expert-custom.js';
        if(file_exists($custom_js_file)) {
            $data['add_bundle_expert_custom_js']= true;
        }else{
            $data['add_bundle_expert_custom_js']= false;
        }

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $html =  $this->load->view('catalog/bundle_expert/bundle_expert_order_form_block.tpl', $data);
        }else{
            $html =  $this->load->view('catalog/bundle_expert/bundle_expert_order_form_block', $data);
        }

        return $html;
    }

    public function getProductFormBundleTab($params){

        $this->load->model('catalog/bundle_expert');
        $this->load->model('catalog/bundle_expert_kit');
        $this->load->model('catalog/bundle_expert_widget');

        $data = $params[0];
        $url = $params[1];
        $product_id = $params[2];

        $data['bundle_expert_installed'] = $this->model_catalog_bundle_expert->isModuleInstalled();
        $data['bundle_expert_licensed'] = $this->bundle_expert->isLicensed();

        if(!$this->model_catalog_bundle_expert->isModuleInstalled() || !$this->bundle_expert->isEnabled()) {
            $data['html_bundle_expert_tab_header'] = '';
            $data['html_bundle_expert_tab_content'] = '';
            return $data;
        }

        $param = array();
        $param['store_url'] = isset($data['store_url'])?$data['store_url']:'';

        if(isset($data['api_token'])) $param['api_token'] = $data['api_token']; 


        $data['html_bundle_expert_tab_header'] = $this->getProductFormBundleTabHeaderHtml();
        $data['html_bundle_expert_tab_content'] = $this->getProductFormBundleTabContentHtml($product_id, $url);

        return $data;
    }

    private function getProductFormBundleTabHeaderHtml(){

        if(!$this->model_catalog_bundle_expert->isModuleInstalled() || !$this->bundle_expert->isEnabled()) {
            return '';
        }

        $this->load->language('catalog/bundle_expert_kit');

        $data = array();

        $data['store_url'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

        $data['token'] = $this->token_value;

        $data['button_remove_kit'] = $this->language->get('button_remove_kit');;
        $data['button_edit_kit'] = $this->language->get('button_edit_kit');;
        $data['tab_bundle_expert'] = $this->language->get('tab_bundle_expert');;
        $data['text_kits'] = $this->language->get('text_kits');;
        $data['text_'] = 'text';

        $data['https_catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $html =  $this->load->view('catalog/bundle_expert/bundle_expert_product_form_tab_header.tpl', $data);
        }else{
            $html =  $this->load->view('catalog/bundle_expert/bundle_expert_product_form_tab_header', $data);
        }

        return $html;
    }

    private function getProductFormBundleTabContentHtml($product_id, $url){

        if(!$this->model_catalog_bundle_expert->isModuleInstalled() || !$this->bundle_expert->isEnabled()) {
            return '';
        }

        $this->load->language('catalog/bundle_expert_kit');

        $data = array();

        $data['store_url'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

        $data['token'] = $this->token_value;

        $data['button_remove_kit'] = $this->language->get('button_remove_kit');;
        $data['button_edit_kit'] = $this->language->get('button_edit_kit');;
        $data['button_add'] = $this->language->get('button_edit_kit');
        $data['text_save_product_before_add_kits'] = $this->language->get('text_save_product_before_add_kits');

        $data['text_bundle'] = $this->language->get('entry_name');
        $data['text_name'] = $this->language->get('text_heading_title');
        $data['text_status'] = $this->language->get('column_status');
        $data['text_action'] = $this->language->get('column_action');
        $data['text_'] = 'text';

        $data['https_catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

        $product_kits = $this->model_catalog_bundle_expert_kit->getKitsByProduct($product_id);

        $data['product_kits'] = array();

        if(empty($product_id)){
            $data['disable_add_button'] = true;
        }else{
            $data['disable_add_button'] = false;
        }

        foreach ($product_kits as $kit) {
            $kit_description = $this->model_catalog_bundle_expert_kit->getKitDescriptions($kit['kit_id']);
            $data['product_kits'][] = array(
                'kit_id' => $kit['kit_id'],
                'name' => $kit['name'],
                'title' => $kit_description[$this->config->get('config_language_id')]['title'],
                'status'  => ($kit['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'href' => $this->url->link('catalog/bundle_expert_kit/edit', $this->token_name . $this->token_value . '&kit_id=' . $kit['kit_id']. '&from_product_id=' . $product_id . $url, 'SSL'),
            );
        }

        $data['href_button_add'] = $this->url->link('catalog/bundle_expert_kit/edit', $this->token_name . $this->token_value . '&from_product_id=' . $product_id . $url, 'SSL');


        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $html =  $this->load->view('catalog/bundle_expert/bundle_expert_product_form_tab_content.tpl', $data);
        }else{
            $html =  $this->load->view('catalog/bundle_expert/bundle_expert_product_form_tab_content', $data);
        }

        return $html;
    }


    public function api() {
        $this->load->language('module/bundle_expert');

        if ($this->validate()) {
            
            if (isset($this->request->get['store_id'])) {
                $store_id = $this->request->get['store_id'];
            } else {
                $store_id = 0;
            }

            $this->load->model('setting/store');

            $store_info = $this->model_setting_store->getStore($store_id);

            if ($store_info) {
                $url = $store_info['ssl'];
            } else {
                $url = HTTPS_CATALOG;
            }

            if (isset($this->session->data['cookie']) && isset($this->request->get['api'])) {

                
                $url_data = array();

                foreach($this->request->get as $key => $value) {
                    if ($key != 'route' && $key != 'token' && $key != 'store_id') {
                        $url_data[$key] = $value;
                    }
                }

                $curl = curl_init();

                
                if (substr($url, 0, 5) == 'https') {
                    curl_setopt($curl, CURLOPT_PORT, 443);
                }

                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, $url . 'index.php?route=' . $this->request->get['api'] . ($url_data ? '&' . http_build_query($url_data) : '') . '&admin_mode_bundle_expert=true');

                if ($this->request->post) {
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->request->post));
                }

                curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');


                $json = curl_exec($curl);

                curl_close($curl);
            }
        } else {
            $response = array();

            $response['error'] = $this->error;

            $json = json_encode($response);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($json);
    }

    public function resetRegister() {
        $this->load->model('setting/setting');

        $bundle_expert_license = $this->config->get('bundle_expert_license');

        $bundle_expert_license['license_order_number'] = '';
        $bundle_expert_license['license_domain'] = '';
        $bundle_expert_license['license_key'] = '';
        $bundle_expert_license['license_app'] = '';
        $bundle_expert_license['key'] = '';
        $bundle_expert_license['market_id'] = '';

        $this->config->set('bundle_expert_license', $bundle_expert_license);


        $this->model_setting_setting->editSetting('bundle_expert_license', array('bundle_expert_license'=>$bundle_expert_license));
        

        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = 'bundle_expert' AND `key` = 'bundle_expert_license'");

        $this->response->redirect($this->url->link('catalog/bundle_expert', $this->token_name . $this->token_value, 'SSL'));
    }

    private function _resetRegister(){
        $bundle_expert_licenses = array();











        $this->config->set('bundle_expert_license', array());
        $this->model_setting_setting->editSettingValue('bundle_expert', 'bundle_expert_license', array());


        $license_data = $this->config->get('bundle_expert_license');

    }

    public function getLicenseKey() {
        $json = array(
            'error'=>'',
            'license_key'=>''
        );

        $this->load->language('catalog/bundle_expert_kit');
        $this->load->model('setting/setting');


        if (isset($this->request->get['order_number']) && isset($this->request->get['domain_name'])) {

            $domain_name = $this->request->get['domain_name'];
            $order_number = $this->request->get['order_number'];
            $market_id = $this->request->get['market_id'];

            $result = $this->bundle_expert->getLicenseKeyFromLicenseServer($domain_name, $order_number, $market_id);

            if($result['error']){
                $json['error'] = $result['error'];
            }else{
                $json['license_key'] = $result['license_key'];
                $json['success'] = $this->language->get('text_license_key_get_success');

                $bundle_expert_license = $this->config->get('bundle_expert_license');

                $bundle_expert_license['license_order_number'] = $order_number;
                $bundle_expert_license['license_domain'] = $domain_name;
                $bundle_expert_license['license_key'] = $result['license_key'];
                $bundle_expert_license['market_id'] = $market_id;

                $this->model_setting_setting->editSetting('bundle_expert_license', array('bundle_expert_license'=>$bundle_expert_license));


            }
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function registerModuleByManual() {
        $json = array(
            'error'=>'',
        );

        $this->load->language('catalog/bundle_expert_kit');

        $this->load->model('setting/setting');

        if (isset($this->request->get['manual_register'])) {

            $manual_register = $this->request->get['manual_register'];
            $bundle_expert_license_data = $this->request->get['bundle_expert_license'];

            $license_key = $manual_register['license_key'];
            $license_app = $manual_register['license_app'];
            $key = $manual_register['key'];

            $order_number = $bundle_expert_license_data['license_order_number'];
            $domain_name = $bundle_expert_license_data['license_domain'];
            $market_id = $this->bundle_expert->market_id;

            $bundle_expert_license_new = array();
            $bundle_expert_license_new['license_order_number'] = $order_number;
            $bundle_expert_license_new['license_domain'] = $domain_name;
            $bundle_expert_license_new['license_key'] = $license_key;
            $bundle_expert_license_new['market_id'] = $market_id;
            $bundle_expert_license_new['license_app'] = $license_app;
            $bundle_expert_license_new['key'] = $key;

















            $this->model_setting_setting->editSetting('bundle_expert_license', array('bundle_expert_license'=> $bundle_expert_license_new));







            

        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function registerModuleByLicenseKey() {
        $json = array(
            'error'=>'',
            'license_key'=>'',
            'license_app'=>'',
        );

        $this->load->language('catalog/bundle_expert_kit');

        $this->load->model('setting/setting');

        if (isset($this->request->get['license_key'])) {

            $license_key = $this->request->get['license_key'];

            $result = $this->bundle_expert->registerModuleByLicenseKeyAtLicenseServer($license_key);

            if($result['error']){
                $json['error'] = $result['error'];
            }else{
                $json['license_app'] = $result['license_app'];
                $json['success'] = $this->language->get('text_license_register_success');


                $bundle_expert_license = $this->config->get('bundle_expert_license');

                $bundle_expert_license['license_app'] = $result['license_app'];

                $bundle_expert_license['key'] = $result['key'];

                $this->model_setting_setting->editSetting('bundle_expert_license', array('bundle_expert_license'=>$bundle_expert_license));


            }
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


























    public function updateProductAsKitAllPricesLe(){
        $json = array(
            'error'=>'',
        );

        if ($this->validate()) {

            $this->load->language('catalog/bundle_expert_kit');

            $this->load->model('setting/setting');

            if(!isset($this->session->data['currency'])){

                $this->bundle_expert->initCurrencieSession();
            }

            $this->bundle_expert->updateAllProductAsKitPriceInDB_LE();

            $json['success'] = $this->language->get('text_update_all_product_as_kit_complete');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function updateProductAsKitAllPrices(){
        $json = array(
            'error'=>'',
        );

        if ($this->validate()) {

            $this->load->language('catalog/bundle_expert_kit');

            $this->load->model('setting/setting');

            if(!isset($this->session->data['currency'])){

                $this->bundle_expert->initCurrencieSession();
            }

            $this->bundle_expert->updateAllProductAsKitPriceInDB();

            $json['success'] = $this->language->get('text_update_all_product_as_kit_complete');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function clearCache(){
        $json = array(
            'error'=>'',
        );

        if ($this->validate()) {

            $this->load->language('catalog/bundle_expert_kit');

            $this->load->model('setting/setting');

            $this->cache->delete('product.bundle_expert');
            $this->bundle_expert->dbCacheClear();
            $this->bundle_expert->deleteMinifyJsCss();

            $json['success'] = $this->language->get('text_update_all_product_as_kit_complete');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function updateCache(){
        $json = array(
            'error'=>'',
        );

        if ($this->validate()) {

            $this->load->language('catalog/bundle_expert_kit');

            $this->load->model('setting/setting');
            $this->load->model('catalog/bundle_expert_kit');

            $this->cache->delete('product.bundle_expert');

            $this->cache->delete('product.bundle_expert');
            $this->bundle_expert->dbCacheClear();
            $this->bundle_expert->deleteMinifyJsCss();

            if(!isset($this->session->data['currency'])){
                $this->bundle_expert->initCurrencieSession();
            }

            $filter_data = array(
                'filter_name'	  => '',
                'filter_product_id'	  => '',
                'sort'  => '',
                'order' => '',
                'start' => 0,
                'limit' => 1000000
            );

            $results = $this->model_catalog_bundle_expert_kit->getKits($filter_data);

            foreach ($results as $result){



                $this->bundle_expert->updateKitCache($result['kit_id']);

                $this->bundle_expert->updateKitAsProductMainProductPrice($result['kit_id']);
                $this->bundle_expert->updateKitAsProductMainProductDataCache($result['kit_id']);
                $this->bundle_expert->updateKitAsProductMainProductDataCache_LE($result['kit_id']);
                $this->bundle_expert->updateKitAsProductMainProductPrice_LE($result['kit_id']);

            }

            $json['success'] = $this->language->get('text_update_all_product_as_kit_complete');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function updateCacheWithProgress(){
        $json = array(
            'error'=>'',
        );

        if ($this->validate()) {

            $this->load->language('catalog/bundle_expert_kit');

            $this->load->model('setting/setting');
            $this->load->model('catalog/bundle_expert_kit');

            if (isset($this->request->get['position'])) {
                $position = $this->request->get['position'];
            } else {
                $position = 0;
            }

            if($position == 0) {
                $this->cache->delete('product.bundle_expert');

                $this->cache->delete('product.bundle_expert');
                $this->bundle_expert->dbCacheClear();
                $this->bundle_expert->deleteMinifyJsCss();
            }

            if(!isset($this->session->data['currency'])){
                $this->bundle_expert->initCurrencieSession();
            }

            $limit = 5;




            $filter_data = array(
                'filter_name'	  => '',
                'filter_product_id'	  => '',
                'sort'  => '',
                'order' => '',
                'start' => $position,
                'limit' => $limit
            );

            $results = $this->model_catalog_bundle_expert_kit->getKits($filter_data);

            foreach ($results as $result){

                $position++;


                
                if(!$result['status']){
                    continue;
                }

                $this->bundle_expert->updateKitCache($result['kit_id']);

                
                $this->bundle_expert->updateProductAsKitPricesProcess($result);
                




































            }

            $filter_data = array(
                'filter_name'	  => '',
                'filter_product_id'	  => '',
                'sort'  => '',
                'order' => '',
                'start' => 0,
                'limit' => 1000000000
            );

            $results = $this->model_catalog_bundle_expert_kit->getKits($filter_data);

            $size = count($results);

            if($size){
                $json['total'] = round(($position / $size) * 100);

            }else{
                $json['total'] = 100;

            }

            if ($position<$size) {
                $json['next'] = str_replace('&amp;', '&', $this->url->link('catalog/bundle_expert/updateCacheWithProgress', $this->token_name . $this->token_value . '&position=' . $position, true));

            } else {
                
                $json['success'] = $this->language->get('text_update_all_product_as_kit_complete');

            }

        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCustomJsTemplate() {
        $json = array(
            'error'=>'',
        );

        if (isset($this->request->get['id'])) {

            $id = $this->request->get['id'];

            $this->load->model('catalog/bundle_expert_custom_js');

            if($id==-1){

            }else{
                $result = $this->model_catalog_bundle_expert_custom_js->getHeaderCartJsTemplatesById($id);
            }



            if(isset($result)){
                $json['success'] = "true";
                $json['script'] = $result['script'];
            }


        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }



    protected function validate() {
        if (!$this->user->hasPermission('modify', 'catalog/bundle_expert')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function install() {
        $this->load->model('catalog/bundle_expert');
        $this->model_catalog_bundle_expert->install();
    }

    public function uninstall() {
        $this->load->model('catalog/bundle_expert');
        $this->model_catalog_bundle_expert->uninstall();
    }
}