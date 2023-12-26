<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ControllerExtensionModuleBundleExpert extends Controller {
    private $error = array();

    private $token_name = array();
    private $token_value = array();
    private $path_extension_module = '';

    public function __construct($registry) {

        parent::__construct($registry);

        if (version_compare(VERSION, '3.0.0.0', '<')) {
            $this->token_name = 'token=';
            $this->token_value = $this->session->data['token'];
        }else{
            $this->token_name = 'user_token=';
            $this->token_value = $this->session->data['user_token'];
        }

        if (version_compare(VERSION, '2.3.0.2', '<')) {
            $this->path_extension_module = 'extension/module';
        }else {
            if (version_compare(VERSION, '3.0.0.0', '<')) {
                $this->path_extension_module = 'extension/extension';
            } else {
                $this->path_extension_module = 'marketplace/extension';
            }
        }
    }

    public function index() {
        if(!isset($this->request->get['module_id'])){
            $this->response->redirect($this->url->link('catalog/bundle_expert', $this->token_name . $this->token_value . '', true));
            return;
        }

        $this->load->language('catalog/bundle_expert_kit');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('catalog/bundle_expert_widget');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_status'] = $this->language->get('entry_status');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['text_cart_kit_color_1'] = $this->language->get('text_cart_kit_color_1');
        $data['text_cart_kit_color_2'] = $this->language->get('text_cart_kit_color_2');
        $data['text_cart_kit_color_reset'] = $this->language->get('text_cart_kit_color_reset');

        
        $data['text_license_getting_key'] =  $this->language->get('text_license_getting_key');
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
        $data['text_license_'] =  '';
        $data['text_license_'] =  '';


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

        $data['cancel'] = $this->url->link($this->path_extension_module, $this->token_name . $this->token_value, 'SSL');

        $data['token_value'] = $this->token_value;
        $data['token_name'] = $this->token_name;

        $data['bundle_expert_navigation'] = $this->load->controller('catalog/bundle_expert_navigation');

        if (version_compare(VERSION, '3.0.0.0', '<')) {
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

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if (version_compare(VERSION, '3.0.0.0', '<')) {
            $this->response->setOutput($this->load->view('extension/module/bundle_expert.tpl', $data));
        }else{
            $this->response->setOutput($this->load->view('extension/module/bundle_expert', $data));
        }

    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/bundle_expert')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    public function install() {
        return $this->load->controller('catalog/bundle_expert/install');
    }

    public function uninstall() {
        return $this->load->controller('catalog/bundle_expert/uninstall');
    }
}