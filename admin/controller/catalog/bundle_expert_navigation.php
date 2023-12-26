<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ControllerCatalogBundleExpertNavigation extends Controller {
    private $error = array();

    private $token_name = array();
    private $token_value = array();

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
        $this->load->language('catalog/bundle_expert_kit');



        $this->load->model('catalog/bundle_expert_kit');
        $this->load->model('setting/setting');

        $this->load->model('catalog/bundle_expert');

        if(!$this->model_catalog_bundle_expert->isModuleInstalled()) {
            $this->response->redirect($this->url->link('common/dashboard', $this->token_name . $this->token_value . '', 'SSL'));
        }

        $data = array();

        $url='';

        $this->bundle_expert->updateHelpTutorial();

        $help_data = $this->model_setting_setting->getSetting('bundle_expert_help');

        $data['href_kits'] = $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url, 'SSL');
        $data['href_widgets'] = $this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . $url, 'SSL');
        $data['href_settings'] = $this->url->link('catalog/bundle_expert', $this->token_name . $this->token_value . $url, 'SSL');
        $data['href_import'] = $this->url->link('catalog/bundle_expert_import', $this->token_name . $this->token_value . $url, 'SSL');
        $data['href_help'] = $help_data['bundle_expert_help_url'];

        $data['text_kits'] = $this->language->get('text_kits');
        $data['text_widgets'] = $this->language->get('text_widgets');
        $data['text_settings'] = $this->language->get('text_settings');
        $data['text_import'] = $this->language->get('text_import') . '<span style="font-size: 10px; opacity: 0.6;"> (beta 1.01)</span>';
        $data['text_help'] = $this->language->get('text_help');
        $data['text_show_save_settings_warning'] = $this->language->get('text_show_save_settings_warning');
        $data['text_check_footer_ocmod_warning'] = $this->language->get('text_check_footer_ocmod_warning');
        $data['button_filter'] = $this->language->get('button_filter');

        $data['text_module_name'] = $this->language->get('text_module_name');
        $data['entry_auto_filter_limit'] = $this->language->get('entry_auto_filter_limit');
        $data['text_auto_filter_title'] = $this->language->get('text_auto_filter_title');
        $data['button_save'] = $this->language->get('button_save');

        if($this->bundle_expert->market_id==1){
            $data['logo'] = 'view/image/bundle_expert_logo_en.png';
        }else{
            $data['logo'] = 'view/image/bundle_expert_logo_ru.png';
        }

        $data['active_button']='kits';

        if($this->request->get['route']=='catalog/bundle_expert_kit' || $this->request->get['route']=='catalog/bundle_expert_kit/edit' || $this->request->get['route']=='catalog/bundle_expert_kit/add')
            $data['active_button']='kits';

        if($this->request->get['route']=='catalog/bundle_expert_widget' || $this->request->get['route']=='catalog/bundle_expert_widget/edit' || $this->request->get['route']=='catalog/bundle_expert_widget/add')
            $data['active_button']='widgets';

        if($this->request->get['route']=='catalog/bundle_expert')
            $data['active_button']='settings';

        if($this->request->get['route']=='catalog/bundle_expert_import' || $this->request->get['route']=='catalog/bundle_expert_import/edit' || $this->request->get['route']=='catalog/bundle_expert_import/add') {
            $data['active_button'] = 'import';
        }

        if($this->request->get['route']=='module/bundle_expert_help')
            $data['active_button']='help';

        
        $bundle_expert_settings = $this->config->get('bundle_expert_settings');
        if(isset($this->request->get['route']) && $this->request->get['route']!='catalog/bundle_expert'){
            $checked_route = true;
        }else{
            $checked_route = false;
        }
        if ($this->bundle_expert->isLicensed() && !isset($bundle_expert_settings) && $checked_route) {
            $data['show_save_settings_warning'] = true;
        } else {
            $data['show_save_settings_warning'] = false;
        }

        
        
        
        
        $data['check_footer_ocmod'] = false;
        if ($this->bundle_expert->isLicensed() && isset($bundle_expert_settings)){
            if($this->bundle_expert->isEnabled() && !$this->config->get('config_maintenance')) {
                if (!isset($bundle_expert_settings['check_footer_ocmod_complete'])) {
                    $data['check_footer_ocmod'] = true;
                } else {
                    $data['check_footer_ocmod'] = false;
                }
            }
        }

        $data['check_footer_ocmod_url'] = HTTP_CATALOG;

        $import_extension_file = DIR_APPLICATION . 'controller/catalog/bundle_expert_import.php';
        if(file_exists($import_extension_file) && $this->user->hasPermission('access', 'catalog/bundle_expert_import')) {
            $data['import_enable'] = true;
        }else{
            $data['import_enable'] = false;
        }

        $bundle_expert_settings['check_footer_ocmod_complete'] = true;

        $auto_filter_fields = $this->get_auto_filter_fields();

        $bundle_expert_auto_filter = $this->model_setting_setting->getSetting('bundle_expert_auto_filter');

        if(!isset($bundle_expert_auto_filter) || empty($bundle_expert_auto_filter)){

            $bundle_expert_auto_filter = array();

            foreach ($auto_filter_fields as $key=>$title){
                $bundle_expert_auto_filter[$key] = true;
            }

            $bundle_expert_auto_filter['product'] = true;
            $bundle_expert_auto_filter['category'] = true;
            $bundle_expert_auto_filter['manufacturer'] = true;

            $bundle_expert_auto_filter_limit = 20;

            $this->model_setting_setting->editSetting('bundle_expert_auto_filter', array('bundle_expert_auto_filter'=>$bundle_expert_auto_filter, 'bundle_expert_auto_filter_limit'=>$bundle_expert_auto_filter_limit));

        }else{
            $bundle_expert_auto_filter_limit = $bundle_expert_auto_filter['bundle_expert_auto_filter_limit'];
            $bundle_expert_auto_filter = $bundle_expert_auto_filter['bundle_expert_auto_filter'];

        }

        $data['bundle_expert_auto_filter'] = $bundle_expert_auto_filter;
        $data['bundle_expert_auto_filter_limit'] = $bundle_expert_auto_filter_limit;

        $data['auto_filter_fields'] = $auto_filter_fields;

        if($this->request->get['route']=='catalog/bundle_expert_kit/edit' || $this->request->get['route']=='catalog/bundle_expert_kit/add'
        || $this->request->get['route']=='catalog/bundle_expert_widget/edit' || $this->request->get['route']=='catalog/bundle_expert_widget/add'
        ){
            $show_auto_filter = true;
        }else{
            $show_auto_filter = false;
        }


        $data['show_auto_filter'] = $show_auto_filter;


        $data['token_value'] = $this->token_value;

        $data['token_name'] = $this->token_name;

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $html = $this->load->view('catalog/bundle_expert/bundle_expert_navigation.tpl', $data);
        }else{
            $html = $this->load->view('catalog/bundle_expert/bundle_expert_navigation', $data);
        }

        return $html;

    }

    public function set_footer_ocmod_checked() {

        $this->load->model('setting/setting');

        $bundle_expert_settings = $this->config->get('bundle_expert_settings');

        $bundle_expert_settings['check_footer_ocmod_complete'] = true;

        $this->model_setting_setting->editSettingValue('bundle_expert', 'bundle_expert_settings', $bundle_expert_settings);

    }

    public function get_auto_filter_fields() {
        $fields= array(
            'product'=>$this->language->get('text_auto_filter_product'),
            'category'=>$this->language->get('text_auto_filter_category'),
            'manufacturer'=>$this->language->get('text_auto_filter_manufacturer'),
            'filter'=>$this->language->get('text_auto_filter_filter'),


            'model'=>$this->language->get('text_auto_filter_model'),
            'sku'=>$this->language->get('text_auto_filter_sku'),
            'upc'=>$this->language->get('text_auto_filter_upc'),
            'ean'=>$this->language->get('text_auto_filter_ean'),
            'jan'=>$this->language->get('text_auto_filter_jan'),
            'isbn'=>$this->language->get('text_auto_filter_isbn'),
            'mpn'=>$this->language->get('text_auto_filter_mpn'),
        );

        if($this->bundle_expert->isGroupsInstalled()) {
            $fields['group'] =  $this->language->get('text_auto_filter_group');
        }

        return $fields;
    }

    public function save_auto_filter() {
        $json = array();

        $this->load->language('catalog/bundle_expert_kit');

        if (isset($this->request->post['bundle_expert_auto_filter']) && $this->validateAutoFilterEdit()) {

            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('catalog/option');
            $this->load->model('setting/setting');

            $bundle_expert_auto_filter_data = $this->request->post['bundle_expert_auto_filter'];
            $bundle_expert_auto_filter_limit = (int)$this->request->post['bundle_expert_auto_filter_limit'];

            if($bundle_expert_auto_filter_limit<=0){
                $bundle_expert_auto_filter_limit = 20;
            }

            $auto_filter_fields = $this->get_auto_filter_fields();

            $bundle_expert_auto_filter = array();

            foreach ($auto_filter_fields as $key=>$title){
                $bundle_expert_auto_filter[$key] = isset($bundle_expert_auto_filter_data[$key]) ? true : false;
            }



            $this->model_setting_setting->editSettingValue('bundle_expert_auto_filter', 'bundle_expert_auto_filter', $bundle_expert_auto_filter);
            $this->model_setting_setting->editSettingValue('bundle_expert_auto_filter', 'bundle_expert_auto_filter_limit', $bundle_expert_auto_filter_limit);


            $json['success'] = true;

        }else{
            $json['error'] = $this->error;
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validateAutoFilterEdit() {
        if (!$this->user->hasPermission('modify', 'catalog/bundle_expert_kit')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }


}