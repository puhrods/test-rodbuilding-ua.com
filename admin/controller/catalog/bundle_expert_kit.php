<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ControllerCatalogBundleExpertKit extends Controller {
    private $error = array();

    private $token_name = array();
    private $token_value = array();
    private $path_extension_module = '';

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

        if(!$this->bundle_expert->isLicensed()) {
            $this->response->redirect($this->url->link('catalog/bundle_expert', $this->token_name . $this->token_value . '', 'SSL'));
        }

        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/bundle_expert_kit');
        $this->load->model('catalog/bundle_expert');

        if(!$this->model_catalog_bundle_expert->isModuleInstalled()) {
            $this->response->redirect($this->url->link('common/dashboard', $this->token_name . $this->token_value . '', 'SSL'));
        }

        $this->getList();
    }

    public function add() {
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/bundle_expert_kit');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_bundle_expert_kit->addKit($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if(isset($this->request->get['from_product_id'])){
                $this->response->redirect($this->url->link('catalog/product/edit', $this->token_name . $this->token_value . $url . '&product_id=' . $this->request->get['from_product_id'], 'SSL'));
            }else{
                $this->response->redirect($this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url, 'SSL'));
            }
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/bundle_expert_kit');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_bundle_expert_kit->editKit($this->request->get['kit_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_product_id'])) {
                $url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if(isset($this->request->get['from_product_id'])){
                $this->response->redirect($this->url->link('catalog/product/edit', $this->token_name . $this->token_value . $url . '&product_id=' . $this->request->get['from_product_id'], 'SSL'));
            }else{
                $this->response->redirect($this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url, 'SSL'));
            }

        }

        $this->getForm();
    }


    public function copy() {
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/bundle_expert_kit');

        if (isset($this->request->post['selected']) && $this->validateCopy()) {
            foreach ($this->request->post['selected'] as $kit_id) {
                $this->model_catalog_bundle_expert_kit->copyKit($kit_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if(isset($this->request->get['from_product_id'])){
                $this->response->redirect($this->url->link('catalog/product/edit', $this->token_name . $this->token_value . $url . '&product_id=' . $this->request->get['from_product_id'], 'SSL'));
            }else{
                $this->response->redirect($this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url, 'SSL'));
            }

        }

        $this->getList();
    }

    public function delete_from_form() {
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/bundle_expert_kit');

        if (isset($this->request->get['kit_id']) && $this->validateDelete()) {

            $kit_id = (int) $this->request->get['kit_id'];

            $this->model_catalog_bundle_expert_kit->deleteKit($kit_id);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if(isset($this->request->get['from_product_id'])){
                $this->response->redirect($this->url->link('catalog/product/edit', $this->token_name . $this->token_value . $url . '&product_id=' . $this->request->get['from_product_id'], 'SSL'));
            }else{
                $this->response->redirect($this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url, 'SSL'));
            }
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/bundle_expert_kit');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $kit_id) {
                $this->model_catalog_bundle_expert_kit->deleteKit($kit_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if(isset($this->request->get['from_product_id'])){
                $this->response->redirect($this->url->link('catalog/product/edit', $this->token_name . $this->token_value . $url . '&product_id=' . $this->request->get['from_product_id'], 'SSL'));
            }else{
                $this->response->redirect($this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url, 'SSL'));
            }


        }

        $this->getList();
    }

    public function repair() {
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/bundle_expert_kit');

        if ($this->validateRepair()) {
            $this->model_catalog_bundle_expert_kit->repairKits();

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {

        if(!$this->bundle_expert->isLicensed()) {
            $this->response->redirect($this->url->link('module/bundle_expert', $this->token_name . $this->token_value . '', 'SSL'));
        }

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('catalog/option');
        $this->load->model('catalog/filter');
        $this->load->model('catalog/manufacturer');

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_product_id'])) {
            $filter_product_id = $this->request->get['filter_product_id'];
        } else {
            $filter_product_id= null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_product_id'])) {
            $url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['title'])) {
            $url .= '&sort=' . $this->request->get['title'];
        }

        if (isset($this->request->get['quantity'])) {
            $url .= '&sort=' . $this->request->get['quantity'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->token_name . $this->token_value, 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url, 'SSL')
        );

        $data['add'] = $this->url->link('catalog/bundle_expert_kit/add', $this->token_name . $this->token_value . $url, 'SSL');
        $data['delete'] = $this->url->link('catalog/bundle_expert_kit/delete', $this->token_name . $this->token_value . $url, 'SSL');
        $data['copy'] = $this->url->link('catalog/bundle_expert_kit/copy', $this->token_name . $this->token_value . $url, true);
        $data['repair'] = $this->url->link('catalog/bundle_expert_kit/repair', $this->token_name . $this->token_value . $url, 'SSL');

        $data['bundle_expert_navigation'] = $this->load->controller('catalog/bundle_expert_navigation');

        $data['token_value'] = $this->token_value;
        $data['token_name'] = $this->token_name;

        $this->load->language('catalog/bundle_expert_kit');

        $data['kits'] = array();

        $limit = $this->config->get('config_limit_admin');


        $filter_data = array(
            'filter_name'	  => $filter_name,
            'filter_product_id'	  => $filter_product_id,
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $kit_total = $this->model_catalog_bundle_expert_kit->getTotalKits($filter_data);

        $results = $this->model_catalog_bundle_expert_kit->getKits($filter_data);

        foreach ($results as $result) {

            $kit_history_status_info = $this->model_catalog_bundle_expert_kit->getKitHistoryStatusInfo($result['kit_id']);

            $data['kits'][] = array(
                'kit_id' => $result['kit_id'],
                'name'        => $result['name'],
                'title'        => $result['title'],
                'kit_quantity_mode' => $result['kit_quantity_mode'],
                'sort_order'  => $result['sort_order'],
                'kit_history_status_info' => $kit_history_status_info,
                'status'  => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit'        => $this->url->link('catalog/bundle_expert_kit/edit', $this->token_name . $this->token_value . '&kit_id=' . $result['kit_id'] . $url, 'SSL'),
                'delete'      => $this->url->link('catalog/bundle_expert_kit/delete', $this->token_name . $this->token_value . '&kit_id=' . $result['kit_id'] . $url, 'SSL')
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['entry_status'] = $this->language->get('entry_status');

        $data['column_name'] = $this->language->get('column_name');
        $data['column_title'] = $this->language->get('column_title');
        $data['column_sort_order'] = $this->language->get('column_sort_order');
        $data['column_action'] = $this->language->get('column_action');
        $data['column_status'] = $this->language->get('column_status');

        $data['button_copy'] = $this->language->get('button_copy');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_rebuild'] = $this->language->get('button_rebuild');

        $data['text_quantity'] =  $this->language->get('text_quantity');
        $data['text_kit_log'] =  $this->language->get('text_kit_log');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_products_name_contain'] = $this->language->get('entry_products_name_contain');
        $data['button_filter'] = $this->language->get('button_filter');


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_product_id'])) {
            $url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['filter_product_name'] = '';
        $data['filter_product_id'] = '';
        if(isset($filter_product_id)){
            $filter_product_info = $this->model_catalog_product->getProduct($filter_product_id);
            if($filter_product_info){
                $data['filter_product_name'] = $filter_product_info['name'];
                $data['filter_product_id'] = $filter_product_info['product_id'];
            }
        }

        $data['filter_name'] = $filter_name;
        $data['sort_name'] = $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . '&sort=name' . $url, 'SSL');
        $data['sort_title'] = $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . '&sort=title' . $url, 'SSL');
        $data['sort_quantity'] = $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . '&sort=quantity' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . '&sort=status' . $url, 'SSL');
        $data['sort_order'] = $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . '&sort=sort_order' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_product'])) {
            $url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_product_id'])) {
            $url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $kit_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($kit_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($kit_total - $this->config->get('config_limit_admin'))) ? $kit_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $kit_total, ceil($kit_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_kit_list.tpl', $data));
        }else{
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_kit_list', $data));
        }


    }

    public function getFormContentHtml($data) {

        $kit_id = $data['kit_id'];

        if(isset($data['error'])){
            $this->error = $data['error'];
        }

        
        if ($this->config->get('config_editor_default')) {
            $this->document->addScript('view/javascript/ckeditor/ckeditor.js');
            $this->document->addScript('view/javascript/ckeditor/ckeditor_init.js');
            $data['editor'] = 'ckeditor';
        } else {
            $this->document->addScript('view/javascript/summernote/summernote.js');

            $this->document->addScript('view/javascript/summernote/opencart.js');
            $this->document->addStyle('view/javascript/summernote/summernote.css');
            $data['editor'] = 'summernote';
        }

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('catalog/option');
        $this->load->model('catalog/filter');
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/attribute');
        $this->load->model('catalog/attribute_group');


        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($kit_id) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_meta_title'] = $this->language->get('entry_meta_title');
        $data['entry_meta_description'] = $this->language->get('entry_meta_description');
        $data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $data['entry_keyword'] = $this->language->get('entry_keyword');
        $data['entry_parent'] = $this->language->get('entry_parent');
        $data['entry_filter'] = $this->language->get('entry_filter');
        $data['entry_store'] = $this->language->get('entry_store');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_top'] = $this->language->get('entry_top');
        $data['entry_column'] = $this->language->get('entry_column');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_layout'] = $this->language->get('entry_layout');

        $data['help_filter'] = $this->language->get('help_filter');
        $data['help_keyword'] = $this->language->get('help_keyword');
        $data['help_top'] = $this->language->get('help_top');
        $data['help_column'] = $this->language->get('help_column');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');

        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_data'] = $this->language->get('tab_data');
        $data['tab_design'] = $this->language->get('tab_design');


        $data['text_quantity'] = $this->language->get('text_quantity');
        $data['text_product'] = $this->language->get('text_product');
        $data['text_category'] = $this->language->get('text_category');
        $data['text_filter'] = $this->language->get('text_filter');
        $data['text_manufacturer'] = $this->language->get('text_manufacturer');
        $data['text_option'] = $this->language->get('text_option');
        $data['text_check_items'] = $this->language->get('text_check_items');
        $data['text_check_options'] = $this->language->get('text_check_options');
        $data['text_products_total'] = $this->language->get('text_products_total');
        $data['text_products_total_minus_percent'] = $this->language->get('text_products_total_minus_percent');
        $data['text_products_total_minus_sum'] = $this->language->get('text_products_total_minus_sum');
        $data['text_products_total_fixed'] = $this->language->get('text_products_total_fixed');
        $data['text_product_price'] = $this->language->get('text_product_price');
        $data['text_product_price_minus_sum'] = $this->language->get('text_product_price_minus_sum');
        $data['text_product_price_minus_percent'] = $this->language->get('text_product_price_minus_percent');
        $data['text_product_price_fix_price'] = $this->language->get('text_product_price_fix_price');
        $data['text_fix_product'] = $this->language->get('text_fix_product');
        $data['text_select_product'] = $this->language->get('text_select_product');
        $data['text_free_product'] = $this->language->get('text_free_product');
        $data['quantity_edit_enable'] = $this->language->get('quantity_edit_enable');
        $data['text_free_product_default_in_kit'] = $this->language->get('text_free_product_default_in_kit');

        $data['select_product_empty_mode'] = $this->language->get('select_product_empty_mode');
        $data['select_product_empty_default'] = $this->language->get('select_product_empty_default');
        $data['text_select_product_not_empty_in_cart'] = $this->language->get('text_select_product_not_empty_in_cart');
        $data['entry_group_name'] = $this->language->get('entry_group_name');
        $data['text_products'] =  $this->language->get('text_products');
        $data['entry_title_description'] =  $this->language->get('entry_title_description');
        $data['entry_cart_title'] =  $this->language->get('entry_cart_title');
        $data['text_widgets'] = $this->language->get('text_widgets');
        $data['text_widget'] = $this->language->get('text_widget');
        $data['text_kits_log'] = $this->language->get('text_kits_log');
        $data['text_kit_log_description'] = $this->language->get('text_kit_log_description');
        $data['text_heading_title'] = $this->language->get('text_heading_title');
        $data['text_link_to_products'] = $this->language->get('text_link_to_products');
        $data['text_input_name'] = $this->language->get('text_input_name');
        $data['text_kit_price'] = $this->language->get('text_kit_price');
        $data['text_consider_actions'] = $this->language->get('text_consider_actions');
        $data['text_limit_kit_count'] = $this->language->get('text_limit_kit_count');
        $data['text_kit_count'] = $this->language->get('text_kit_count');
        $data['text_limit_kit_in_cart'] = $this->language->get('text_limit_kit_in_cart');
        $data['text_kit_count_in_cart'] = $this->language->get('text_kit_count_in_cart');
        $data['text_kit_type'] = $this->language->get('text_kit_type');
        $data['text_kit_type_main_product'] = $this->language->get('text_kit_type_main_product');
        $data['text_kit_type_product_list'] = $this->language->get('text_kit_type_product_list');
        $data['text_main_product'] = $this->language->get('text_main_product');
        $data['text_options'] = $this->language->get('text_options');
        $data['text_price'] = $this->language->get('text_price');
        $data['text_kit_products'] = $this->language->get('text_kit_products');
        $data['text_product_select_mode'] = $this->language->get('text_product_select_mode');
        $data['text_widgets_with_kit'] = $this->language->get('text_widgets_with_kit');
        $data['text_widget_name'] = $this->language->get('text_widget_name');
        $data['text_kit_no_has_widgets'] = $this->language->get('text_kit_no_has_widgets');
        $data['text_disabled_options'] = $this->language->get('text_disabled_options');
        $data['text_enabled_options'] = $this->language->get('text_enabled_options');
        $data['text_fixed_options'] = $this->language->get('text_fixed_options');
        $data['text_input_disable_option_name'] = $this->language->get('text_input_disable_option_name');
        $data['text_input_fixed_option_name'] = $this->language->get('text_input_fixed_option_name');
        $data['text_input_enabled_option_name'] = $this->language->get('text_input_enabled_option_name');
        $data['text_minus_value'] = $this->language->get('text_minus_value');
        $data['text_minus_percent'] = $this->language->get('text_minus_percent');
        $data['text_product_discount_in_total'] = $this->language->get('text_product_discount_in_total');
        $data['text_kit_as_product'] = $this->language->get('text_kit_as_product');
        $data['text_kit_as_product_light_mode'] = $this->language->get('text_kit_as_product_light_mode');
        $data['text_kit_in_cart_as_product'] = $this->language->get('text_kit_in_cart_as_product');
        $data['text_save_kit_as_product_total'] = $this->language->get('text_save_kit_as_product_total');
        $data['text_disable_bundle_discount_in_customer_group'] = $this->language->get('text_disable_bundle_discount_in_customer_group');

        $data['text_main_mode'] = $this->language->get('text_main_mode');
        $data['text_main_mode_kit'] = $this->language->get('text_main_mode_kit');
        $data['text_main_mode_series'] = $this->language->get('text_main_mode_series');
        $data['text_main_mode_collection'] = $this->language->get('text_main_mode_collection');


        $data['text_product_quantity_limit'] = $this->language->get('text_product_quantity_limit');
        $data['text_product_quantity_limit_min'] = $this->language->get('text_product_quantity_limit_min');
        $data['text_product_quantity_limit_max'] = $this->language->get('text_product_quantity_limit_max');

        $data['text_disbanded_bundle_clear'] = $this->language->get('text_disbanded_bundle_clear');

        $data['text_hide_special_products'] = $this->language->get('text_hide_special_products');
        $data['text_products_combine_mode'] = $this->language->get('text_products_combine_mode');
        $data['text_randomize_select_product'] = $this->language->get('text_randomize_select_product');
        $data['text_custom_field'] = $this->language->get('text_custom_field');
        $data['text_consider_discount'] = $this->language->get('text_consider_discount');
        $data['text_warning_price_rewrite'] = $this->language->get('text_warning_price_rewrite');
        $data['text_kit_discount_by_product_count'] = $this->language->get('text_kit_discount_by_product_count');
        $data['text_kit_as_product_main_product_use_default_discount'] = $this->language->get('text_kit_as_product_main_product_use_default_discount');
        $data['text_bundle_total_price_hide_special'] = $this->language->get('text_bundle_total_price_hide_special');
        $data['text_show_default_specials_in_kit_discounts'] = $this->language->get('text_show_default_specials_in_kit_discounts');
        $data['text_auto_kit_in_cart'] = $this->language->get('text_auto_kit_in_cart');
        $data['text_add_to_cart_mode'] = $this->language->get('text_add_to_cart_mode');
        $data['text_add_to_cart_mode_bundle'] = $this->language->get('text_add_to_cart_mode_bundle');
        $data['text_add_to_cart_mode_simple_products'] = $this->language->get('text_add_to_cart_mode_simple_products');

        $data['button_kit_period_add'] = $this->language->get('button_add');
        $data['tab_kit_period'] = $this->language->get('tab_kit_period');
        $data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $data['entry_priority'] = $this->language->get('entry_priority');
        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');

        $data['text_product_combine_mode_intersect'] = $this->language->get('text_product_combine_mode_intersect');
        $data['text_product_combine_mode_union'] = $this->language->get('text_product_combine_mode_union');
        $data['text_product_combine_mode_subtract'] = $this->language->get('text_product_combine_mode_subtract');


        $data['text_series_mode'] = $this->language->get('text_series_mode');
        $data['text_series_mode_values_default'] = $this->language->get('text_series_mode_values_default');
        $data['text_series_mode_values_no_main'] = $this->language->get('text_series_mode_values_no_main');
        $data['text_save_and_stay'] = $this->language->get('text_save_and_stay');

        $data['text_tab_groups'] = $this->language->get('text_tab_groups');

        $data['text_custom_field_text'] = $this->language->get('text_custom_field_text');
        $data['text_custom_field_string'] = $this->language->get('text_custom_field_string');
        $data['text_custom_field_number'] = $this->language->get('text_custom_field_number');

        
        $data['text_kit_price_mode_to_customer_groups'] = $this->language->get('text_kit_price_mode_to_customer_groups');
        $data['text_kit_price_mode_to_customer_groups_status'] = $this->language->get('text_kit_price_mode_to_customer_groups_status');
        $data['text_price_mode_to_customer_groups'] = $this->language->get('text_price_mode_to_customer_groups');
        $data['text_price_mode_to_customer_groups_status'] = $this->language->get('text_price_mode_to_customer_groups_status');
        

        
        $data['text_kit_type_auto_list'] = $this->language->get('text_kit_type_auto_list');
        $data['text_kit_mode_auto_list_grouping_none'] = $this->language->get('text_kit_mode_auto_list_grouping_none');
        $data['text_kit_mode_auto_list_grouping_by_category'] = $this->language->get('text_kit_mode_auto_list_grouping_by_category');
        $data['text_kit_mode_auto_list_grouping_by_category_top'] = $this->language->get('text_kit_mode_auto_list_grouping_by_category_top');
        $data['text_kit_mode_auto_list_grouping_by_category_bottom'] = $this->language->get('text_kit_mode_auto_list_grouping_by_category_bottom');
        $data['text_kit_mode_auto_list_grouping'] = $this->language->get('text_kit_mode_auto_list_grouping');
        



        $bundle_expert_settings = $this->config->get('bundle_expert_settings');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        if (isset($this->error['link_product'])) {
            $data['error_link_product'] = $this->error['link_product'];
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_product_id'])) {
            $url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->token_name . $this->token_value, 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url, 'SSL')
        );

        if (!isset($kit_id)) {
            if (isset($this->request->get['from_product_id'])) {
                $data['action'] = $this->url->link('catalog/bundle_expert_kit/add', $this->token_name . $this->token_value . $url . '&from_product_id=' . $this->request->get['from_product_id'], 'SSL');
            }else{
                $data['action'] = $this->url->link('catalog/bundle_expert_kit/add', $this->token_name . $this->token_value . $url, 'SSL');
            }
        } else {
            if (isset($this->request->get['from_product_id'])) {
                $data['action'] = $this->url->link('catalog/bundle_expert_kit/edit', $this->token_name . $this->token_value . '&kit_id=' . $kit_id . $url . '&from_product_id=' . $this->request->get['from_product_id'], 'SSL');
            }else{
                $data['action'] = $this->url->link('catalog/bundle_expert_kit/edit', $this->token_name . $this->token_value . '&kit_id=' . $kit_id . $url, 'SSL');

            }
        }

        if (isset($this->request->get['from_product_id'])) {
            $data['cancel'] = $this->url->link('catalog/product/edit', $this->token_name . $this->token_value . $url . '&product_id=' . $this->request->get['from_product_id'], 'SSL');
        }else{
            $data['cancel'] = $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url, 'SSL');
        }

        if (!isset($kit_id)) {
            $data['delete_href'] = '';
        } else {
            if (isset($this->request->get['from_product_id'])) {
                $data['delete_href'] = $this->url->link('catalog/bundle_expert_kit/delete_from_form', $this->token_name . $this->token_value . '&kit_id=' . $kit_id . $url . '&from_product_id=' . $this->request->get['from_product_id'], 'SSL');
            }else{
                $data['delete_href'] = $this->url->link('catalog/bundle_expert_kit/delete_from_form', $this->token_name . $this->token_value . '&kit_id=' . $kit_id . $url, 'SSL');
            }
        }

        if (!isset($kit_id)) {
            if (isset($this->request->get['from_product_id'])) {
                $data['save_kit_ajax_href'] = $this->url->link('catalog/bundle_expert_kit/save_kit_ajax', $this->token_name . $this->token_value . $url . '&from_product_id=' . $this->request->get['from_product_id'], 'SSL');
            }else{
                $data['save_kit_ajax_href'] = $this->url->link('catalog/bundle_expert_kit/save_kit_ajax', $this->token_name . $this->token_value . $url, 'SSL');
            }
        } else {
            if (isset($this->request->get['from_product_id'])) {
                $data['save_kit_ajax_href'] = $this->url->link('catalog/bundle_expert_kit/save_kit_ajax', $this->token_name . $this->token_value . '&kit_id=' . $kit_id . $url . '&from_product_id=' . $this->request->get['from_product_id'], 'SSL');
            }else{
                $data['save_kit_ajax_href'] = $this->url->link('catalog/bundle_expert_kit/save_kit_ajax', $this->token_name . $this->token_value . '&kit_id=' . $kit_id . $url, 'SSL');
            }
        }

        $data['save_kit_ajax_href'] = htmlspecialchars_decode( $data['save_kit_ajax_href'] );



        $data['bundle_expert_navigation'] = $this->load->controller('catalog/bundle_expert_navigation');

        if (isset($kit_id) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $kit_info = $this->model_catalog_bundle_expert_kit->getKit($kit_id);
        }

        $data['token_value'] = $this->token_value;
        $data['token_name'] = $this->token_name;

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        foreach ($data['languages'] as $index=>$language){
            if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                $img_url = "view/image/flags/" . $language['image'];
            }else{
                $img_url = "language/".$language['code']."/".$language['code'].".png";
            }
            $data['languages'][$index]['image_url'] = $img_url;
        }

        if (isset($this->request->post['kit_description'])) {
            $data['kit_description'] = $this->request->post['kit_description'];
        } elseif (isset($kit_id)) {
            $data['kit_description'] = $this->model_catalog_bundle_expert_kit->getKitDescriptions($kit_id);
        } else {
            $data['kit_description'] = array();
        }

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($kit_info)) {
            $data['image'] = $kit_info['image'];
        } else {
            $data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($kit_info) && is_file(DIR_IMAGE . $kit_info['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($kit_info['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);


        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($kit_info)) {
            $data['sort_order'] = $kit_info['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($kit_info)) {
            $data['name'] = $kit_info['name'];
        } else {
            $data['name'] = 'New Kit';
        }

        if (isset($this->request->post['custom_field_text'])) {
            $data['custom_field_text'] = $this->request->post['custom_field_text'];
        } elseif (!empty($kit_info)) {
            $data['custom_field_text'] = $kit_info['custom_field_text'];
        } else {
            $data['custom_field_text'] = '';
        }

        if (isset($this->request->post['custom_field_string'])) {
            $data['custom_field_string'] = $this->request->post['custom_field_string'];
        } elseif (!empty($kit_info)) {
            $data['custom_field_string'] = $kit_info['custom_field_string'];
        } else {
            $data['custom_field_string'] = '';
        }

        if (isset($this->request->post['custom_field_number'])) {
            $data['custom_field_number'] = $this->request->post['custom_field_number'];
        } elseif (!empty($kit_info)) {
            $data['custom_field_number'] = $kit_info['custom_field_number'];
        } else {
            $data['custom_field_number'] = 0;
        }

        $data['text_custom_field_text'] = $this->language->get('text_custom_field_text');
        $data['text_custom_field_string'] = $this->language->get('text_custom_field_string');
        $data['text_custom_field_number'] = $this->language->get('text_custom_field_number');

        $show_custom_field_text = false;
        $show_custom_field_string = false;
        $show_custom_field_number = false;

        $data['show_custom_field_text'] = $show_custom_field_text;
        $data['show_custom_field_string'] = $show_custom_field_string;
        $data['show_custom_field_number'] = $show_custom_field_number;



        if (isset($this->request->post['main_mode'])) {
            $data['main_mode'] = $this->request->post['main_mode'];
        } elseif (!empty($kit_info) && !empty($kit_info['main_mode'])) {
            $data['main_mode'] = $kit_info['main_mode'];
        } else {
            $data['main_mode'] = 'kit';
        }

        if (isset($this->request->post['series_mode'])) {
            $data['series_mode'] = $this->request->post['series_mode'];
        } elseif (!empty($kit_info) && !empty($kit_info['series_mode'])) {
            $data['series_mode'] = $kit_info['series_mode'];
        } else {
            $data['series_mode'] = 'default';
        }

        $data['series_mode_values'] = array(
            'default' => $data['text_series_mode_values_default'],
            'no_main' => $data['text_series_mode_values_no_main'],
        );

        if (isset($this->request->post['kit_mode'])) {
            $data['kit_mode'] = $this->request->post['kit_mode'];
        } elseif (!empty($kit_info) && !empty($kit_info['kit_mode'])) {
            $data['kit_mode'] = $kit_info['kit_mode'];
        } else {
            $data['kit_mode'] = 'list_product';
        }

        
        $data['kit_modes_list'] = array();
        $kit_mode = array(
            'title' => $data['text_kit_type_main_product'],
            'code' => 'main_product',

        );
        $data['kit_modes_list'][] = $kit_mode;
        $kit_mode = array(
            'title' => $data['text_kit_type_product_list'],
            'code' => 'list_product',

        );
        $data['kit_modes_list'][] = $kit_mode;
        $kit_mode = array(
            'title' => $data['text_kit_type_auto_list'],
            'code' => 'auto_list',

        );
        $data['kit_modes_list'][] = $kit_mode;

        if (isset($this->request->post['kit_mode_auto_list_grouping'])) {
            $data['kit_mode_auto_list_grouping'] = $this->request->post['kit_mode_auto_list_grouping'];
        } elseif (!empty($kit_info) && !empty($kit_info['kit_mode_auto_list_grouping'])) {
            $data['kit_mode_auto_list_grouping'] = $kit_info['kit_mode_auto_list_grouping'];
        } else {
            $data['kit_mode_auto_list_grouping'] = 'none';
        }

        $data['kit_mode_auto_list_grouping_items'] = array();
        $grouping_mode = array(
            'title' => $data['text_kit_mode_auto_list_grouping_none'],
            'code' => 'none',

        );
        $data['kit_mode_auto_list_grouping_items'][] = $grouping_mode;
        $grouping_mode = array(
            'title' => $data['text_kit_mode_auto_list_grouping_by_category_top'],
            'code' => 'by_category_top',

        );
        $data['kit_mode_auto_list_grouping_items'][] = $grouping_mode;
        $grouping_mode = array(
            'title' => $data['text_kit_mode_auto_list_grouping_by_category_bottom'],
            'code' => 'by_category_bottom',

        );
        $data['kit_mode_auto_list_grouping_items'][] = $grouping_mode;
        $grouping_mode = array(
            'title' => $data['text_kit_mode_auto_list_grouping_by_category'],
            'code' => 'by_category',

        );
        $data['kit_mode_auto_list_grouping_items'][] = $grouping_mode;

        

        $item_warning = false;
        $options_warning = false;

        $data['link_products'] = array();

        if (isset($this->request->post['link_product'])) {
            $link_products = $this->request->post['link_product'];
            if(empty($link_products))
                $link_products = array();
        } else if(!empty($kit_info)){
            $link_products = $this->model_catalog_bundle_expert_kit->getKitLinkProducts($kit_info['kit_id']);
        } else {
            $link_products = array();
        }

        foreach($link_products as $link_product){

            $deleted_item=false;

            $text = $this->language->get('text_'.$link_product['item_type']);

            switch ($link_product['item_type']){
                case 'product':
                    $product_info = $this->model_catalog_product->getProduct($link_product['item_id']);
                    if($product_info){
                        $name = $text . ' -> ' . $product_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'category':
                    $category_info = $this->model_catalog_category->getCategory($link_product['item_id']);
                    if($category_info){
                        $name = $text . ' -> ' . $category_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'filter':
                    $filter_info = $this->model_catalog_filter->getFilter($link_product['item_id']);
                    if($filter_info){
                        $name = $text . ' > ' . $filter_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'manufacturer':
                    $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($link_product['item_id']);
                    if($manufacturer_info){
                        $name = $text . ' > ' . $manufacturer_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'model':
                case 'sku':
                case 'upc':
                case 'ean':
                case 'jan':
                case 'isbn':
                case 'mpn':
                    $name = $text.' > ' . $link_product['item_value'];
                    $name = htmlspecialchars_decode($name);
                    $name = htmlspecialchars($name);
                    break;
                case 'group':
                case 'group_kit':
                     if($this->bundle_expert->isGroupsInstalled()) {
                         $this->load->model('catalog/bundle_expert_product_group');
                         $group_info = $this->model_catalog_bundle_expert_product_group->getGroup($link_product['item_id']);
                         if($group_info){
                             $name = $text . ' -> ' . $group_info['name'];
                             $name = htmlspecialchars_decode($name);
                             $name = htmlspecialchars($name);
                         }else{
                             $deleted_item=true;
                         }
                    }

                    break;

                default:
                    $deleted_item=false;
            }


            if(!$deleted_item) {
                $link_product['name'] = $name;

                $has_options = false;
                if ($link_product['item_type'] == 'product') {
                    $product_options = $this->model_catalog_product->getProductOptions($link_product['item_id']);
                    if (!empty($product_options))
                        $has_options = true;
                }

                $link_product['has_options'] = $has_options;

                $data['link_products'][] = $link_product;;
            }else{
                $item_warning=true;
            }

        }

        $data['link_products_count'] = count($data['link_products']);

        if (isset($this->request->post['link_products_combine_mode'])) {
            $data['link_products_combine_mode'] = $this->request->post['link_products_combine_mode'];
        } elseif (!empty($kit_info) && !empty($kit_info['link_products_combine_mode'])) {
            $data['link_products_combine_mode'] = $kit_info['link_products_combine_mode'];
        } else {
            $data['link_products_combine_mode'] = 'union';
        }

        if (isset($this->request->post['list_product'])) {
            $kit_products = $this->request->post['list_product'];
        } else if(!empty($kit_info)){
            $kit_products = $this->model_catalog_bundle_expert_kit->getKitProducts($kit_info['kit_id']);
        } else {
            $kit_products = array();
        }

        $data['kit_products'] = array();




        foreach($kit_products as $kit_product){
            $deleted_item=false;
            $store_status=true;

            $text = $this->language->get('text_'.$kit_product['item_type']);

            switch ($kit_product['item_type']){
                case 'product':
                    $product_info = $this->model_catalog_product->getProduct($kit_product['item_id']);

                    if($product_info){
                        $name = $text . ' > ' . $product_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                        $store_status = $product_info['status'];
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'category':
                    $category_info = $this->model_catalog_category->getCategory($kit_product['item_id']);
                    if($category_info){
                        $name = $text.' > ' . $category_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                        $store_status = $category_info['status'];
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'filter':
                    $filter_info = $this->model_catalog_filter->getFilter($kit_product['item_id']);
                    if($filter_info){
                        $name = $text . ' > ' . $filter_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'manufacturer':
                    $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($kit_product['item_id']);
                    if($manufacturer_info){
                        $name = $text.' > ' . $manufacturer_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'model':
                case 'sku':
                case 'upc':
                case 'ean':
                case 'jan':
                case 'isbn':
                case 'mpn':
                    $name = $text.' > ' . $kit_product['item_value'];
                    $name = htmlspecialchars_decode($name);
                    $name = htmlspecialchars($name);
                    break;
                case 'group':
                case 'group_kit':
                    if($this->bundle_expert->isGroupsInstalled()) {
                        $this->load->model('catalog/bundle_expert_product_group');
                        $group_info = $this->model_catalog_bundle_expert_product_group->getGroup($kit_product['item_id']);
                        if($group_info){
                            $name = $text . ' > ' . $group_info['name'];
                            $name = htmlspecialchars_decode($name);
                            $name = htmlspecialchars($name);
                        }else{
                            $deleted_item=true;
                        }
                    }

                    break;


                
                case 'auto_attribute':
                    $attribute_info = $this->model_catalog_attribute->getAttribute($kit_product['item_id']);

                    if($attribute_info){
                        $attribute_group_info = $this->model_catalog_attribute_group->getAttributeGroup($attribute_info['attribute_group_id']);
                        $attribute_group_descriptions = $this->model_catalog_attribute_group->getAttributeGroupDescriptions($attribute_group_info['attribute_group_id']);
                        if(isset($attribute_group_descriptions[$this->config->get('config_language_id')])){
                            $name = $text . ' > ' . $attribute_group_descriptions[$this->config->get('config_language_id')]['name']. ' > ' . $attribute_info['name'];
                        }else{
                            $name = $text . ' > ' . $attribute_info['name'];
                        }

                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                        $store_status = true;
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'auto_model':
                case 'auto_sku':
                case 'auto_upc':
                case 'auto_ean':
                case 'auto_jan':
                case 'auto_isbn':
                case 'auto_mpn':
                    $name = $text;
                    $name = htmlspecialchars_decode($name);
                    $name = htmlspecialchars($name);
                    break;
                
                default:
                    $deleted_item=true;
            }


            if(!$deleted_item) {
                $has_options = false;
                if ($kit_product['item_type'] == 'product') {
                    $product_options = $this->model_catalog_product->getProductOptions($kit_product['item_id']);
                    if (!empty($product_options))
                        $has_options = true;
                }


                if (isset($kit_product['disable_options'])) {
                    if (isset($this->request->post['kit_products']))
                        $disable_options = $kit_product['disable_options'];
                    else
                        $disable_options = $kit_product['disable_options'];
                } else {
                    $disable_options = array();
                }

                foreach ($disable_options as $index => $disable_option) {

                    
                    $option_in_product = $this->checkOptionInProduct($disable_option, $product_options);
                    if ($option_in_product) {
                        if ($disable_option['item_type'] == 'option') {

                            $option_info = $this->model_catalog_option->getOption($disable_option['option_id']);
                            if($option_info) {
                                $disable_options[$index]['item_name'] = $data['text_option'] . ' > ' . $option_info['name'];
                                $disable_options[$index]['name'] = $option_info['name'];
                            }else{
                                $option_in_product = false;
                            }
                        } else {
                            $option_info = $this->model_catalog_option->getOption($disable_option['option_id']);
                            $option_value_info = $this->model_catalog_option->getOptionValue($disable_option['option_value_id']);
                            if($option_value_info && $option_info) {
                                $disable_options[$index]['item_name'] = $data['text_option'] . ' >' . $option_info['name'] . ' > ' . $option_value_info['name'];
                                $disable_options[$index]['name'] = $option_info['name'] . ' > ' . $option_value_info['name'];
                            }else{
                                $option_in_product = false;
                            }
                        }
                    }

                    if(!$option_in_product){
                        unset($disable_options[$index]);
                        $options_warning = true;
                    }

                }

                if (isset($kit_product['fixed_options'])) {
                    if (isset($this->request->post['kit_products']))
                        $fixed_options = $kit_product['fixed_options'];
                    else
                        $fixed_options = $kit_product['fixed_options'];
                } else {
                    $fixed_options = array();
                }


                foreach ($fixed_options as $index => $fixed_option) {

                    
                    $option_in_product = $this->checkOptionInProduct($fixed_option, $product_options);
                    if ($option_in_product) {
                        if ($fixed_option['item_type'] == 'option') {
                            $option_info = $this->model_catalog_option->getOption($fixed_option['option_id']);
                            if($option_info) {
                                $fixed_options[$index]['item_name'] = $data['text_option'] . ' > ' . $option_info['name'];
                                $fixed_options[$index]['name'] = $option_info['name'];
                            }else{
                                $option_in_product = false;
                            }
                        } else {
                            $option_info = $this->model_catalog_option->getOption($fixed_option['option_id']);
                            $option_value_info = $this->model_catalog_option->getOptionValue($fixed_option['option_value_id']);
                            if($option_value_info && $option_info) {
                                $fixed_options[$index]['item_name'] = $data['text_option'] . ' > ' . $option_info['name'] . '->' . $option_value_info['name'];
                                $fixed_options[$index]['name'] = $option_info['name'] . '->' . $option_value_info['name'];
                            }else{
                                $option_in_product = false;
                            }
                        }
                    }

                    if(!$option_in_product){
                        unset($fixed_options[$index]);
                        $options_warning = true;
                    }
                }


                if (isset($kit_product['enabled_options'])) {
                    if (isset($this->request->post['kit_products']))
                        $enabled_options = $kit_product['enabled_options'];
                    else
                        $enabled_options = $kit_product['enabled_options'];
                } else {
                    $enabled_options = array();
                }

                foreach ($enabled_options as $index => $enabled_option) {

                    
                    $option_in_product = $this->checkOptionInProduct($enabled_option, $product_options);
                    if ($option_in_product) {
                        if ($enabled_option['item_type'] == 'option') {

                            $option_info = $this->model_catalog_option->getOption($enabled_option['option_id']);
                            if($option_info) {
                                $enabled_options[$index]['item_name'] = $data['text_option'] . ' > ' . $option_info['name'];
                                $enabled_options[$index]['name'] = $option_info['name'];
                            }else{
                                $option_in_product = false;
                            }
                        } else {
                            $option_info = $this->model_catalog_option->getOption($enabled_option['option_id']);
                            $option_value_info = $this->model_catalog_option->getOptionValue($enabled_option['option_value_id']);
                            if($option_value_info && $option_info) {
                                $enabled_options[$index]['item_name'] = $data['text_option'] . ' >' . $option_info['name'] . ' > ' . $option_value_info['name'];
                                $enabled_options[$index]['name'] = $option_info['name'] . ' > ' . $option_value_info['name'];
                            }else{
                                $option_in_product = false;
                            }
                        }
                    }

                    if(!$option_in_product){
                        unset($enabled_options[$index]);
                        $options_warning = true;
                    }

                }

            }else{
                $item_warning=true;
                $name = '';
                $has_options = false;
                $disable_options = array();
                $fixed_options = array();
                $enabled_options = array();
                $store_status = 0;
            }

            if (isset($kit_product['item_empty_mode']) && isset($this->request->post['main_mode'])) {
                $item_empty_mode = $kit_product['item_empty_mode'];
                $item_empty_mode['enable'] = isset($item_empty_mode['enable']) ? 1 : 0;
                $item_empty_mode['default_empty'] = isset($item_empty_mode['default_empty']) ? 1 : 0;
                $item_empty_mode['title'] = isset($item_empty_mode['title']) ? $item_empty_mode['title'] : array();
                $item_empty_mode['not_empty_in_cart'] = isset($item_empty_mode['not_empty_in_cart']) ? 1: 0;
            } elseif (isset($kit_product['item_empty_mode'])) {
                if(!is_array($kit_product['item_empty_mode'])){
                    $item_empty_mode = $kit_product['item_empty_mode'];
                }else{
                    $item_empty_mode = $kit_product['item_empty_mode'];
                    $item_empty_mode['enable'] = isset($item_empty_mode['enable']) ? $item_empty_mode['enable'] : 0;
                    $item_empty_mode['default_empty'] = isset($item_empty_mode['default_empty']) ? $item_empty_mode['default_empty'] : 0;
                    $item_empty_mode['title'] = isset($item_empty_mode['title']) ? $item_empty_mode['title'] : array();
                    $item_empty_mode['not_empty_in_cart'] = isset($item_empty_mode['not_empty_in_cart']) ? $item_empty_mode['not_empty_in_cart'] : 0;
                }
            } else {
                $item_empty_mode = array(
                    'enable' => 0,
                    'default_empty' => 0,
                    'title' => array(),
                    'not_empty_in_cart' => 0,
                );
            }


            if (isset($kit_product['hide_special_products']) && isset($this->request->post['main_mode'])) {
                $hide_special_products = (boolean)1;
            } elseif (isset($kit_product['hide_special_products'])) {
                $hide_special_products = (boolean)$kit_product['hide_special_products'];
            } else {
                $hide_special_products = false;
            }

            if (isset($kit_product['products_combine_mode']) && isset($this->request->post['main_mode'])) {
                $products_combine_mode = $kit_product['products_combine_mode'];
            } elseif (isset($kit_product['products_combine_mode'])) {
                $products_combine_mode = $kit_product['products_combine_mode'];
            } else {
                $products_combine_mode = 'union';
            }

            if (isset($kit_product['empty_group_image'])) {
                $empty_group_image = $kit_product['empty_group_image'];
            } else {
                $empty_group_image = '';
            }

            
            if (isset($kit_product['price_mode_to_customer_groups'])) {
                $price_mode_to_customer_groups = $kit_product['price_mode_to_customer_groups'];
                $price_mode_to_customer_groups = array_values($price_mode_to_customer_groups);

            } else {
                $price_mode_to_customer_groups = array();
            }
            if (isset($kit_product['price_mode_to_customer_groups_status'])) {
                $price_mode_to_customer_groups_status = (int)$kit_product['price_mode_to_customer_groups_status'];

            } else {
                $price_mode_to_customer_groups_status = false;
            }

            

            if (!empty($empty_group_image) && is_file(DIR_IMAGE . $empty_group_image)) {
                $empty_group_image_thumb = $this->model_tool_image->resize($empty_group_image, 100, 100);
            } else {
                $empty_group_image_thumb = $this->model_tool_image->resize('no_image.png', 100, 100);
            }

            if (isset($kit_product['randomize_select_product']) && isset($this->request->post['main_mode'])) {
                $randomize_select_product = 1;
            } elseif (isset($kit_product['randomize_select_product'])) {
                $randomize_select_product = (int)$kit_product['randomize_select_product'];
            } else {
                $randomize_select_product = 0;
            }

            if (isset($kit_product['custom_field'])) {
                $custom_field = $kit_product['custom_field'];
            } else {
                $custom_field = '';
            }

            $name = str_replace('\\','',$name);
            $name = preg_replace('/[\x00-\x1F\x7F]/', '', $name);

            $data['kit_products'][] = array(

                'name' =>  $name,
                'item_position' => $kit_product['item_position'],
                'item_mode' => $kit_product['item_mode'],
                'main' => $kit_product['main'],
                'item_type' => $kit_product['item_type'],
                'item_id' => $kit_product['item_id'],
                'item_value' => $kit_product['item_value'],
                'quantity' => $kit_product['quantity'],
                'quantity_edit' => isset($kit_product['quantity_edit'])?$kit_product['quantity_edit']:0,
                'free_product_default_in_kit' => isset($kit_product['free_product_default_in_kit'])?$kit_product['free_product_default_in_kit']:0,
                'price_mode' => $kit_product['price_mode'],
                'price' => $kit_product['price'],
                'price_minus_sum' => $kit_product['price_minus_sum'],
                'price_minus_percent' => $kit_product['price_minus_percent'],
                
                'price_mode_to_customer_groups_status' => $price_mode_to_customer_groups_status,
                'price_mode_to_customer_groups' => $price_mode_to_customer_groups,
                
                'has_options' => $has_options,
                'disable_options' =>array_values($disable_options),
                'fixed_options' => array_values($fixed_options),
                'enabled_options' => array_values($enabled_options),
                'store_status' => $store_status,

                'item_empty_mode_enable' => $item_empty_mode['enable'],
                'item_empty_mode_default_empty' => $item_empty_mode['default_empty'],
                'item_empty_mode_title' => $item_empty_mode['title'],
                'item_empty_mode_not_empty_in_cart' => $item_empty_mode['not_empty_in_cart'],

                'hide_special_products' => $hide_special_products,
                'products_combine_mode' => $products_combine_mode,

                'empty_group_image' => $empty_group_image,
                'empty_group_image_thumb' => $empty_group_image_thumb,

                'randomize_select_product' => $randomize_select_product,
                'custom_field' => $custom_field,

            );
        }


        $data['kit_products_json_encode'] = json_encode($data['kit_products'], JSON_HEX_APOS);

        
        $data['item_warning'] = $item_warning;
        $data['options_warning'] = $options_warning;
        if($item_warning){
            $this->error['warning'] = $data['text_check_items'];

        }
        if($options_warning){
            $data['error_warning']  = $data['text_check_options'];
        }

        if (isset($this->request->post['kit_price_mode'])) {
            $data['kit_price_mode'] = $this->request->post['kit_price_mode'];
        } elseif (!empty($kit_info) && !empty($kit_info['kit_price_mode'])) {
            $data['kit_price_mode'] = json_decode($kit_info['kit_price_mode'], true);
        } else {
            $data['kit_price_mode'] = array(
                'mode' => 'sum'
            );
        }

        
        if (isset($this->request->post['kit_price_mode_to_customer_groups'])) {
            $data['kit_price_mode_to_customer_groups'] = $this->request->post['kit_price_mode_to_customer_groups'];
        } elseif (!empty($kit_info) && !empty($kit_info['kit_price_mode'])) {
            $data['kit_price_mode_to_customer_groups'] = $kit_info['kit_price_mode_to_customer_groups'];
        } else {
            $data['kit_price_mode_to_customer_groups'] = array();
        }







        if (isset($this->request->post['kit_price_mode_to_customer_groups_status'])) {
            $data['kit_price_mode_to_customer_groups_status'] = $this->request->post['kit_price_mode_to_customer_groups_status'];
        } elseif (!empty($kit_info) && !empty($kit_info['kit_price_mode_to_customer_groups_status'])) {
            $data['kit_price_mode_to_customer_groups_status'] = $kit_info['kit_price_mode_to_customer_groups_status'];
        } else {
            $data['kit_price_mode_to_customer_groups_status'] = false;
        }


        

        $data['kit_price_mode_list'] = array(
            'sum' => $data['text_products_total'],
            'sum_minus_percent' => $data['text_products_total_minus_percent'],
            'sum_minus_value' => $data['text_products_total_minus_sum'],
            'sum_fix' => $data['text_products_total_fixed'],
        );

        $data['kit_product_price_mode'] = array(
            'product_price' => $data['text_product_price'],
            'product_price_minus_sum' => $data['text_product_price_minus_sum'],
            'product_price_minus_percent' => $data['text_product_price_minus_percent'],
            'fix_price' => $data['text_product_price_fix_price'],
        );

        $data['kit_product_item_mode'] = array(
            'fix_product' => $data['text_fix_product'],
            'select_product' => $data['text_select_product'],
            'free_product' => $data['text_free_product'],
        );

        $data['product_combine_mode_values'] = array(
            'union' => $data['text_product_combine_mode_union'],
            'intersect' => $data['text_product_combine_mode_intersect'],
            'subtract' => $data['text_product_combine_mode_subtract'],
        );

        if (isset($this->request->post['kit_id'])) {
            $data['kit_id'] = $this->request->post['kit_id'];
        } elseif (!empty($kit_info)) {
            $data['kit_id'] = $kit_info['kit_id'];
        } else {
            $data['kit_id'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($kit_info)) {
            $data['status'] = $kit_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['kit_as_product_main_product_use_default_discount'])) {
            $data['kit_as_product_main_product_use_default_discount'] = $this->request->post['kit_as_product_main_product_use_default_discount'];
        } elseif (!empty($kit_info)) {
            $data['kit_as_product_main_product_use_default_discount'] = $kit_info['kit_as_product_main_product_use_default_discount'];
        } else {
            $data['kit_as_product_main_product_use_default_discount'] = false;
        }

        if (isset($this->request->post['bundle_total_price_hide_special'])) {
            $data['bundle_total_price_hide_special'] = $this->request->post['bundle_total_price_hide_special'];
        } elseif (!empty($kit_info)) {
            $data['bundle_total_price_hide_special'] = $kit_info['bundle_total_price_hide_special'];
        } else {
            $data['bundle_total_price_hide_special'] = false;
        }

        if (isset($this->request->post['enable_discount'])) {
            $data['enable_discount'] = $this->request->post['enable_discount'];
        } elseif (!empty($kit_info)) {
            $data['enable_discount'] = $kit_info['enable_discount'];
        } else {
            $data['enable_discount'] = false;
        }



        if (isset($this->request->post['enable_special'])) {
            $data['enable_special'] = $this->request->post['enable_special'];
        } elseif (!empty($kit_info)) {
            $data['enable_special'] = $kit_info['enable_special'];
        } else {
            $data['enable_special'] = false;
        }

        if (isset($this->request->post['show_default_specials_in_kit_discounts'])) {
            $data['show_default_specials_in_kit_discounts'] = $this->request->post['show_default_specials_in_kit_discounts'];
        } elseif (!empty($kit_info)) {
            $data['show_default_specials_in_kit_discounts'] = $kit_info['show_default_specials_in_kit_discounts'];
        } else {
            $data['show_default_specials_in_kit_discounts'] = false;
        }

        if (isset($this->request->post['auto_kit_in_cart'])) {
            $data['auto_kit_in_cart'] = $this->request->post['auto_kit_in_cart'];
        } elseif (!empty($kit_info)) {
            $data['auto_kit_in_cart'] = $kit_info['auto_kit_in_cart'];
        } else {
            $data['auto_kit_in_cart'] = false;
        }

        if (isset($this->request->post['add_to_cart_mode'])) {
            $data['add_to_cart_mode'] = $this->request->post['add_to_cart_mode'];
        } elseif (!empty($kit_info)) {
            $data['add_to_cart_mode'] = $kit_info['add_to_cart_mode'];
        } else {
            $data['add_to_cart_mode'] = 'bundle';
        }

        if (isset($this->request->post['quantity_control'])) {
            $data['quantity_control'] = $this->request->post['quantity_control'];
        } elseif (!empty($kit_info)) {
            $data['quantity_control'] = $kit_info['quantity_control'];
        } else {
            $data['quantity_control'] = true;
        }

        if (isset($this->request->post['product_quantity_limit'])) {
            $data['product_quantity_limit'] = $this->request->post['product_quantity_limit'];
        } elseif (!empty($kit_info)) {
            $data['product_quantity_limit'] = json_decode($kit_info['product_quantity_limit'], true);
        } else {
            $data['product_quantity_limit'] = array(
                'status' => false,
                'min' => '',
                'max' => '',
            );
        }

        if (isset($this->request->post['kit_discount_by_product_count'])) {
            $data['kit_discount_by_product_count'] = $this->request->post['kit_discount_by_product_count'];
        } elseif (!empty($kit_info)) {
            $data['kit_discount_by_product_count'] = json_decode($kit_info['kit_discount_by_product_count'], true);
        } else {
            $data['kit_discount_by_product_count'] = array(
                'status' => false,
                'min' => '',
                'max' => '',
            );
        }

        if (isset($this->request->post['disbanded_bundle_clear'])) {
            $data['disbanded_bundle_clear'] = $this->request->post['disbanded_bundle_clear'];
        } elseif (!empty($kit_info)) {
            $data['disbanded_bundle_clear'] = $kit_info['disbanded_bundle_clear'];
        } else {
            $data['disbanded_bundle_clear'] = 0;
        }

        if (isset($this->request->post['kit_quantity_mode'])) {
            $data['kit_quantity_mode'] = $this->request->post['kit_quantity_mode'];
        } elseif (!empty($kit_info)) {
            $data['kit_quantity_mode'] = json_decode($kit_info['kit_quantity_mode'], true);
        } else {
            $data['kit_quantity_mode'] = array(
                'limit' => false,
                'value' => '5'
            );
        }

        if (isset($this->request->post['kit_cart_limit_mode'])) {
            $data['kit_cart_limit_mode'] = $this->request->post['kit_cart_limit_mode'];
        } elseif (!empty($kit_info)) {
            $data['kit_cart_limit_mode'] = json_decode($kit_info['kit_cart_limit_mode'], true);
        } else {
            $data['kit_cart_limit_mode'] = array(
                'limit' => false,
                'value' => '1'
            );
        }

        if (isset($this->request->post['product_discount_in_total'])) {
            $data['product_discount_in_total'] = $this->request->post['product_discount_in_total'];
        } elseif (!empty($kit_info)) {
            $data['product_discount_in_total'] = (isset($kit_info['product_discount_in_total']))? $kit_info['product_discount_in_total']:true;
        } else {
            $data['product_discount_in_total'] = true;
        }

        if (isset($this->request->post['kit_as_product'])) {
            $data['kit_as_product'] = $this->request->post['kit_as_product'];
        } elseif (!empty($kit_info)) {
            $data['kit_as_product'] = (isset($kit_info['kit_as_product']))? $kit_info['kit_as_product']:false;
        } else {
            $data['kit_as_product'] = false;
        }

        if (isset($this->request->post['save_kit_as_product_total'])) {
            $data['save_kit_as_product_total'] = $this->request->post['save_kit_as_product_total'];
        } elseif (!empty($kit_info)) {
            $data['save_kit_as_product_total'] = (isset($kit_info['save_kit_as_product_total']))? $kit_info['save_kit_as_product_total']:false;
        } else {
            $data['save_kit_as_product_total'] = false;
        }

        if (isset($this->request->post['kit_as_product_light_mode'])) {
            $data['kit_as_product_light_mode'] = $this->request->post['kit_as_product_light_mode'];
        } elseif (!empty($kit_info)) {
            $data['kit_as_product_light_mode'] = (isset($kit_info['kit_as_product_light_mode']))? $kit_info['kit_as_product_light_mode']:false;
        } else {
            $data['kit_as_product_light_mode'] = false;
        }

        if (isset($this->request->post['kit_in_cart_as_product'])) {
            $data['kit_in_cart_as_product'] = $this->request->post['kit_in_cart_as_product'];
        } elseif (!empty($kit_info)) {
            $data['kit_in_cart_as_product'] = (isset($kit_info['kit_in_cart_as_product']))? $kit_info['kit_in_cart_as_product']:false;
        } else {
            $data['kit_in_cart_as_product'] = false;
        }

        $data['hide_empty_group_image_field'] = true;
        $data['hide_list_product_custom_field'] = false;
        $data['hide_list_product_custom_field'] = true;

        $this->load->model('customer/customer_group');

        $customer_groups = $this->model_customer_customer_group->getCustomerGroups();
        foreach ($customer_groups as $index=>$customer_group){
            $customer_groups[$index]['name'] = addslashes($customer_group['name']);
        }

        $data['customer_groups'] = $customer_groups;

        if (isset($this->request->post['kit_period'])) {
            $kit_periods = $this->request->post['kit_period'];
        } elseif (isset($kit_id)) {
            $kit_periods = $this->model_catalog_bundle_expert_kit->getKitPeriods($kit_id);
        } else {
            $kit_periods = array();
        }

        $data['kit_periods'] = array();

        foreach ($kit_periods as $kit_period) {
            
            if($kit_period['customer_group_id']=="-1000"){
                continue;
            }

            $data['kit_periods'][] = array(
                'customer_group_id' => $kit_period['customer_group_id'],
                'priority'          => $kit_period['priority'],
                'date_start'        => ($kit_period['date_start'] != '0000-00-00') ? $kit_period['date_start'] : '',
                'date_end'          => ($kit_period['date_end'] != '0000-00-00') ? $kit_period['date_end'] :  ''
            );
        }

        $data['kit_widgets'] = array();

        $kit_widgets = $this->model_catalog_bundle_expert_kit->getKitWidgets($data['kit_id']);

        foreach ($kit_widgets as $kit_widget){
            $url="";
            $data['kit_widgets'][] = array(
                'widget_id' => $kit_widget['widget_id'],
                'name' => $kit_widget['name'],
                'href' => $this->url->link('catalog/bundle_expert_widget/edit', $this->token_name . $this->token_value . '&widget_id=' . $kit_widget['widget_id'] . $url, 'SSL'),
            );

        }

        $data['kit_history_status'] = $this->model_catalog_bundle_expert_kit->getKitHistoryStatus($data['kit_id']);

        if(isset($bundle_expert_settings['product_item_quantity_edit_default_value'])){
            $data['product_item_quantity_edit_default_value'] = $bundle_expert_settings['product_item_quantity_edit_default_value'];
        }else{
            $data['product_item_quantity_edit_default_value'] = 1;
        }

        
        
        if (!isset($kit_id) && isset($this->request->get['from_product_id'])) {
            $data['kit_as_product'] = true;

            $product_info_2 = $this->model_catalog_product->getProduct($this->request->get['from_product_id']);
            $product_oprions = $this->model_catalog_product->getProductOptions($this->request->get['from_product_id']);

            if ($product_info_2) {
                $data['link_products'][] = array(
                    'id' => '',
                    'kit_id' => '',
                    'item_type' => 'product',
                    'item_id' => $product_info_2['product_id'],
                    'item_value' => '',
                    'name' => $product_info_2['name'],
                    'has_options' => empty($product_oprions) ? false : true
                );
                $data['link_products_count'] = count($data['link_products']);
            }
        }





















        if($this->bundle_expert->isGroupsInstalled()){
            $data['groups_enable'] = true;
            $data['groups_tab_html'] = $this->load->controller('catalog/bundle_expert_product_group/getTabHtml', $data);
        }else{
            $data['groups_enable'] = false;
            $data['groups_tab_html'] = '';
        }




        $this->load->model('design/layout');

        $data['layouts'] = $this->model_design_layout->getLayouts();

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $html = $this->load->view('catalog/bundle_expert/bundle_expert_kit_form_content.tpl', $data);
        }else{
            $html = $this->load->view('catalog/bundle_expert/bundle_expert_kit_form_content', $data);
        }

        $result = array('data'=>$data,'html'=>$html);

        return $result;

    }

    protected function getForm() {
        if(!$this->bundle_expert->isLicensed()) {
            $this->response->redirect($this->url->link('module/bundle_expert', $this->token_name . $this->token_value . '', 'SSL'));
        }

        if (isset($this->request->get['kit_id'])) {
            $kit_id = (int) $this->request->get['kit_id'];
        }else{
            $kit_id = null;
        }

        $result = $this->getFormContentHtml(array('kit_id'=>$kit_id));

        $data = $result['data'];

        $data['content_html'] = $result['html'];

        
        $show_price_mode_to_customer_groups = true;
        $data['show_price_mode_to_customer_groups'] = $show_price_mode_to_customer_groups;
        

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $data['style_html'] = $this->load->view('catalog/bundle_expert/bundle_expert_kit_form_style.tpl', $data);
        }else{
            $data['style_html'] = $this->load->view('catalog/bundle_expert/bundle_expert_kit_form_style', $data);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_kit_form.tpl', $data));
        }else{
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_kit_form', $data));
        }

    }

    public function validateForm(&$error_from_other_form = null) {
        if (isset($error_from_other_form)) {
            $error = &$error_from_other_form;
        } else {
            $error = $this->error;
        }

        if (!$this->user->hasPermission('modify', 'catalog/bundle_expert_kit')) {
            $error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['kit_description'] as $language_id => $value) {
            if ((utf8_strlen($value['title']) < 2) || (utf8_strlen($value['title']) > 255)) {
                $error['name'][$language_id] = $this->language->get('error_name');
            }

        }

        
        if(isset($this->request->post['kit_as_product']) && $this->request->post['kit_as_product']){
            if(!isset($this->request->post['link_product']) || empty($this->request->post['link_product'])){
                $error['link_product'] = $this->language->get('error_product_as_kit_link_product');

                
                if(isset($this->request->post['template'])){
                    unset( $error['link_product'] );
                }
            }

        }
        if(isset($this->request->post['kit_as_product_light_mode']) && $this->request->post['kit_as_product_light_mode']){
            if(!isset($this->request->post['link_product']) || empty($this->request->post['link_product'])){
                $error['link_product'] = $this->language->get('error_product_as_kit_link_product');
            }

        }
        if(isset($this->request->post['main_mode']) && $this->request->post['main_mode']=='collection'){
            if(!isset($this->request->post['link_product']) || empty($this->request->post['link_product'])){
                $error['link_product'] = $this->language->get('error_product_as_kit_link_product');
            }
        }

        
        
        
        
        
        
        
        
        
        if(isset($this->request->post['auto_kit_in_cart']) && $this->request->post['auto_kit_in_cart']){
            if(isset($this->request->post['kit_as_product']) && ($this->request->post['kit_as_product'])){
                $error['warning'] = $this->language->get('error_auto_kit_in_cart_only_simple_kit');
            }
            if(isset($this->request->post['kit_as_product']) && ($this->request->post['kit_as_product_light_mode'])){
                $error['warning'] = $this->language->get('error_auto_kit_in_cart_only_simple_kit');
            }
            if(isset($this->request->post['kit_mode']) && $this->request->post['kit_mode']=="main_product"){
                $error['warning'] = $this->language->get('error_auto_kit_in_cart_only_list_product');
            }
            if(isset($this->request->post['enable_discount']) && $this->request->post['enable_discount']==true){

            }
            if(isset($this->request->post['enable_special']) && $this->request->post['enable_special']==true){

            }
            if(isset($this->request->post['list_product']) ){
                foreach ($this->request->post['list_product'] as $list_product){
                    if($list_product['item_mode']!="fix_product"){
                        $error['warning'] = $this->language->get('error_auto_kit_in_cart_only_fix_position');
                        break;
                    }
                }
            }
        }
        if(isset($this->request->post['add_to_cart_mode']) && $this->request->post['add_to_cart_mode']=='simple_products'){




            if(isset($this->request->post['list_product']) ){
                foreach ($this->request->post['list_product'] as $list_product){
                    if($list_product['price_mode']!="product_price"){


                    }
                }
            }
        }
        if ($error && !isset($error['warning'])) {
            $error['warning'] = $this->language->get('error_warning');
        }

        if (isset($error_from_other_form)) {

            return $error;
        } else {
            $this->error = $error;
            return !$this->error;
        }


    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'catalog/bundle_expert_kit')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateRepair() {
        if (!$this->user->hasPermission('modify', 'catalog/bundle_expert_kit')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete_product() {
        $json = array();

        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');
        $this->load->model('setting/setting');


        if (isset($this->request->get['filter_name'])) {


            $this->load->model('catalog/bundle_expert_kit');
            if ($this->bundle_expert->isGroupsInstalled()) {
                $this->load->model('catalog/bundle_expert_product_group');
            }
            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('catalog/filter');
            $this->load->model('catalog/manufacturer');
            $this->load->model('catalog/attribute');

            $filter_name = html_entity_decode($this->request->get['filter_name']);

            if (mb_strripos($filter_name, '>') != false) {
                $filter_name = trim(str_replace(mb_substr($filter_name, 0, mb_strripos($filter_name, '>') + 1), '', $filter_name));
            }

            $bundle_expert_auto_filter = $this->model_setting_setting->getSetting('bundle_expert_auto_filter');
            $bundle_expert_auto_filter_limit = $bundle_expert_auto_filter['bundle_expert_auto_filter_limit'];
            $bundle_expert_auto_filter = $bundle_expert_auto_filter['bundle_expert_auto_filter'];

            if (isset($this->request->get['auto_filter_only_fields'])) {
                foreach ($bundle_expert_auto_filter as $index => $value) {
                    $bundle_expert_auto_filter[$index] = false;
                }
                $bundle_expert_auto_filter[$this->request->get['auto_filter_only_fields']] = true;
            }


            $filter_only_product = $this->request->get['filter_only_product'];
            if (isset($this->request->get['kit_id'])) {
                $kit_id = (int)$this->request->get['kit_id'];

            } else {
                $kit_id = '';

            }
            if (isset($this->request->get['filter_by_type'])) {
                $filter_by_type = $this->request->get['filter_by_type'];
            } else {
                $filter_by_type = '';
            }

            
            if (isset($this->request->get['kit_mode'])) {
                $kit_mode = $this->request->get['kit_mode'];
            } else {
                $kit_mode = '';
            }
            if ($kit_mode == 'auto_list') {
                foreach ($bundle_expert_auto_filter as $index => $value) {
                    $bundle_expert_auto_filter[$index] = false;
                }
                $bundle_expert_auto_filter['auto_attribute'] = true;
                $bundle_expert_auto_filter['auto_model'] = true;
                $bundle_expert_auto_filter['auto_sku'] = true;
                $bundle_expert_auto_filter['auto_upc'] = true;
                $bundle_expert_auto_filter['auto_ean'] = true;
                $bundle_expert_auto_filter['auto_jan'] = true;
                $bundle_expert_auto_filter['auto_isbn'] = true;
                $bundle_expert_auto_filter['auto_mpn'] = true;
            }
            
        }


        $filter_data = array(
            'filter_name' => htmlspecialchars($filter_name),
            'sort' => 'name',
            'order' => 'ASC',
            'start' => 0,
            'limit' => $bundle_expert_auto_filter_limit
        );

        $results = array();


        foreach ($bundle_expert_auto_filter as $filter_field_key => $filter_field_status) {
            $result = array();

            if ($filter_only_product && $filter_field_key !== 'product') {
                continue;
            }
            if ($filter_by_type && $filter_field_key !== $filter_by_type) {
                continue;
            }
            $result = array();

            if ($filter_field_status) {
                switch ($filter_field_key) {
                    case 'product':
                        $result = $this->model_catalog_bundle_expert_kit->getProducts($filter_data);
                        break;
                    case 'category':
                        $result = $this->model_catalog_category->getCategories($filter_data);
                        break;
                    case 'manufacturer':
                        $result = $this->model_catalog_bundle_expert_kit->getManufacturers($filter_data);
                        break;
                    case 'filter':
                        $result = $this->model_catalog_filter->getFilters($filter_data);
                        break;
                    case 'model':
                    case 'sku':
                    case 'upc':
                    case 'ean':
                    case 'jan':
                    case 'isbn':
                    case 'mpn':
                        $result = $this->model_catalog_bundle_expert_kit->getProductsByField($filter_data, $filter_field_key);
                        break;
                    case 'group':
                        if ($this->bundle_expert->isGroupsInstalled()) {
                            if ($kit_id) {
                                $result_group_kit = $this->model_catalog_bundle_expert_product_group->getGroups($filter_data, $kit_id);
                            }
                            $result_group = $this->model_catalog_bundle_expert_product_group->getGroups($filter_data, -1);
                        }
                        break;
                    
                    case 'auto_attribute':
                        $result = $this->model_catalog_attribute->getAttributes($filter_data);
                        break;
                    case 'auto_model':
                    case 'auto_sku':
                    case 'auto_upc':
                    case 'auto_ean':
                    case 'auto_jan':
                    case 'auto_isbn':
                    case 'auto_mpn':
                        $result = array();
                        $result[] = 1;
                        break;
                    
                }

                if ($filter_field_key == "group") {
                    if (!empty($result_group_kit)) {
                        $results['group_kit'] = $result_group_kit;
                    }
                    if (!empty($result_group)) {
                        $results['group'] = $result_group;
                    }
                } else {
                    if (!empty($result)) {
                        $results[$filter_field_key] = $result;
                    }
                }

            }
        }

        foreach ($results as $result_item_key => $result_item) {
            $has_options = false;

            $text = $this->language->get('text_' . $result_item_key);

            foreach ($result_item as $result) {

                $custom_parameter = '';

                switch ($result_item_key) {
                    case 'product':
                        $product_options = $this->model_catalog_product->getProductOptions($result['product_id']);
                        if (!empty($product_options))
                            $has_options = true;
                        $item_id = $result['product_id'];
                        $item_name = $result['name'];
                        $item_value = '';
                        break;
                    case 'category':
                        $item_id = $result['category_id'];
                        $item_name = $result['name'];
                        $item_value = '';
                        break;
                    case 'manufacturer':
                        $item_id = $result['manufacturer_id'];
                        $item_name = $result['name'];
                        $item_value = '';
                        break;
                    case 'filter':
                        $item_id = $result['filter_id'];
                        $item_name = $result['name'];
                        $item_value = '';
                        break;
                    case 'model':
                    case 'sku':
                    case 'upc':
                    case 'ean':
                    case 'jan':
                    case 'isbn':
                    case 'mpn':
                        $item_id = -1;
                        $item_name = $result[$result_item_key];
                        $item_value = $result[$result_item_key];
                        break;
                    case 'group':
                    case 'group_kit':
                        $item_id = $result['group_id'];
                        $item_name = $result['name'];
                        $item_value = '';
                        if ($result['add_mode'] == "products") {
                            $custom_parameter = 'add_group_as_products';

                        }
                        break;
                        
                    case 'auto_attribute':
                        $item_id = $result['attribute_id'];
                        $item_name = $result['attribute_group'] . ">" . $result['name'];
                        $item_value = '';
                        break;
                    case 'auto_model':
                    case 'auto_sku':
                    case 'auto_upc':
                    case 'auto_ean':
                    case 'auto_jan':
                    case 'auto_isbn':
                    case 'auto_mpn':
                        $item_id = -1;
                        $item_name = $this->language->get('text_' . $result_item_key);;
                        $item_value = $result_item_key;
                        break;
                        
                }

                $json[] = array(
                    'item_id' => $item_id,
                    'item_value' => $item_value,
                    'type' => $result_item_key,
                    'title' => $text . ' > ' . (html_entity_decode($item_name)),
                    'name' => $text . ' > ' . (html_entity_decode($item_name)),
                    'default_name' => html_entity_decode($item_name),
                    'custom_parameter' => $custom_parameter,
                    'has_options' => $has_options,

                );
            }


        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['title'];
        }



        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function autocomplete_disable_option() {
        $json = array();
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        if (isset($this->request->post['filter_name']) && isset($this->request->post['product_id'])) {
            $this->load->model('catalog/bundle_expert_kit');
            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('catalog/option');

            $data['text_option'] = $this->language->get('text_option');


            $filter_name = $this->request->post['filter_name'];

            if(mb_strripos($filter_name,'>')!=false) {
                $filter_name = trim(str_replace(mb_substr($filter_name, 0, mb_strripos($filter_name, '>') + 1), '', $filter_name));
            }

            $product_id = $this->request->post['product_id'];

            $filter_list = array();

            if (isset($this->request->post['list_product'])) {
                $list_product = $this->request->post['list_product'];

                $filter_list = current($list_product)['disable_options'];
            }

            $product_options = array();

            $data['product_options'] = $this->model_catalog_product->getProductOptions($product_id);

            $data['option_values'] = array();

            foreach ($data['product_options'] as $product_option) {
                if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                    if (!isset($data['option_values'][$product_option['option_id']])) {
                        $data['option_values'][$product_option['option_id']] = $this->model_catalog_bundle_expert_kit->getOptionValues($product_option['option_id']);
                    }
                }
            }

            foreach ($data['product_options'] as $product_option) {

                $product_options[] = array(
                    'product_option_id' => $product_option['product_option_id'],
                    'option_id' => $product_option['option_id'],
                    'product_option_value_id' => '-1',
                    'option_value_id' => '-1',
                    'item_type' => 'option',
                    'item_name' => $data['text_option'].' > ' . strip_tags(html_entity_decode($product_option['name'], ENT_QUOTES, 'UTF-8')),
                    'name' => strip_tags(html_entity_decode($product_option['name'], ENT_QUOTES, 'UTF-8'))
                );


                
                if (!$this->isOptionDisabled($product_option, $filter_list)) {
                    if (isset($product_option['product_option_value'])) {
                        foreach ($product_option['product_option_value'] as $product_option_value) {
                            $option_data = array(
                                'product_option_id' => $product_option['product_option_id'],
                                'option_id' => $product_option['option_id'],
                                'product_option_value_id' => $product_option_value['product_option_value_id'],
                                'option_value_id' => $product_option_value['option_value_id'],
                                'item_type' => 'option_value',
                                'item_name' => $data['text_option'].' > ' . strip_tags(html_entity_decode($product_option['name'] . ' > ' . $data['option_values'][$product_option['option_id']][$product_option_value['option_value_id']]['name'], ENT_QUOTES, 'UTF-8')),
                                'name' => strip_tags(html_entity_decode($product_option['name'] . ' > ' . $data['option_values'][$product_option['option_id']][$product_option_value['option_value_id']]['name'], ENT_QUOTES, 'UTF-8'))
                            );

                            $product_options[] = $option_data;
                        }
                    }
                }
            }
        }

        
        if(!empty($filter_list)) {
            foreach ($product_options as $index => $product_option) {
                if ($this->isInOptionList($product_option, $filter_list) ) {
                    unset($product_options[$index]);
                }
            }
        }

        
        if(!empty($filter_name)) {
            foreach ($product_options as $index => $product_option) {
                if (!mb_stristr($product_option['item_name'], $filter_name)) {
                    unset($product_options[$index]);
                }
            }
        }




        $json = $product_options;

        $sort_order = array();







        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function autocomplete_enabled_option() {
        $json = array();
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        if (isset($this->request->post['filter_name']) && isset($this->request->post['product_id'])) {
            $this->load->model('catalog/bundle_expert_kit');
            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('catalog/option');

            $data['text_option'] = $this->language->get('text_option');


            $filter_name = $this->request->post['filter_name'];

            if(mb_strripos($filter_name,'>')!=false) {
                $filter_name = trim(str_replace(mb_substr($filter_name, 0, mb_strripos($filter_name, '>') + 1), '', $filter_name));
            }

            $product_id = $this->request->post['product_id'];

            $filter_list = array();

            if (isset($this->request->post['list_product'])) {
                $list_product = $this->request->post['list_product'];

                $filter_list = current($list_product)['enabled_options'];
            }

            $product_options = array();

            $data['product_options'] = $this->model_catalog_product->getProductOptions($product_id);

            $data['option_values'] = array();

            foreach ($data['product_options'] as $product_option) {
                if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                    if (!isset($data['option_values'][$product_option['option_id']])) {
                        $data['option_values'][$product_option['option_id']] = $this->model_catalog_bundle_expert_kit->getOptionValues($product_option['option_id']);
                    }
                }
            }

            foreach ($data['product_options'] as $product_option) {

                $product_options[] = array(
                    'product_option_id' => $product_option['product_option_id'],
                    'option_id' => $product_option['option_id'],
                    'product_option_value_id' => '-1',
                    'option_value_id' => '-1',
                    'item_type' => 'option',
                    'item_name' => $data['text_option'].' > ' . strip_tags(html_entity_decode($product_option['name'], ENT_QUOTES, 'UTF-8')),
                    'name' => strip_tags(html_entity_decode($product_option['name'], ENT_QUOTES, 'UTF-8'))
                );


                
                if (!$this->isOptionDisabled($product_option, $filter_list)) {
                    if (isset($product_option['product_option_value'])) {
                        foreach ($product_option['product_option_value'] as $product_option_value) {
                            if(!isset($data['option_values'][$product_option['option_id']][$product_option_value['option_value_id']])){
                                continue;
                            }
                            $option_data = array(
                                'product_option_id' => $product_option['product_option_id'],
                                'option_id' => $product_option['option_id'],
                                'product_option_value_id' => $product_option_value['product_option_value_id'],
                                'option_value_id' => $product_option_value['option_value_id'],
                                'item_type' => 'option_value',
                                'item_name' => $data['text_option'].' > ' . strip_tags(html_entity_decode($product_option['name'] . ' > ' . $data['option_values'][$product_option['option_id']][$product_option_value['option_value_id']]['name'], ENT_QUOTES, 'UTF-8')),
                                'name' => strip_tags(html_entity_decode($product_option['name'] . ' > ' . $data['option_values'][$product_option['option_id']][$product_option_value['option_value_id']]['name'], ENT_QUOTES, 'UTF-8'))
                            );

                            $product_options[] = $option_data;
                        }
                    }
                }
            }
        }

        
        if(!empty($filter_list)) {
            foreach ($product_options as $index => $product_option) {
                if ($this->isInOptionList($product_option, $filter_list) ) {
                    unset($product_options[$index]);
                }
            }
        }

        
        if(!empty($filter_name)) {
            foreach ($product_options as $index => $product_option) {
                if (!mb_stristr($product_option['item_name'], $filter_name)) {
                    unset($product_options[$index]);
                }
            }
        }




        $json = $product_options;

        $sort_order = array();







        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function autocomplete_fixed_option() {
        $json = array();

        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        if (isset($this->request->post['filter_name']) && isset($this->request->post['product_id'])) {

            $this->load->model('catalog/bundle_expert_kit');
            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('catalog/option');

            $data['text_option'] = $this->language->get('text_option');


            $filter_name = $this->request->post['filter_name'];

            if(mb_strripos($filter_name,'>')!=false) {
                $filter_name = trim(str_replace(mb_substr($filter_name, 0, mb_strripos($filter_name, '>') + 1), '', $filter_name));
            }

            $product_id = $this->request->post['product_id'];

            $filter_list = array();

            if( isset($this->request->post['list_product'])) {
                $list_product = $this->request->post['list_product'];

                $filter_list = current($list_product)['fixed_options'];
            }

            $product_options = array();

            $data['product_options'] = $this->model_catalog_product->getProductOptions($product_id);

            $data['option_values'] = array();

            foreach ($data['product_options'] as $product_option) {
                if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                    if (!isset($data['option_values'][$product_option['option_id']])) {
                        $data['option_values'][$product_option['option_id']] = $this->model_catalog_bundle_expert_kit->getOptionValues($product_option['option_id']);
                    }
                }
            }

            foreach ($data['product_options'] as $product_option) {

                
                if($product_option['type']=='file' || $product_option['type']=='date' || $product_option['type']=='text' || $product_option['type']=='textarea' || $product_option['type']=='datetime' || $product_option['type']=='time') {
                    $product_options[] = array(
                        'product_option_id' => $product_option['product_option_id'],
                        'option_id' => $product_option['option_id'],
                        'product_option_value_id' => '-1',
                        'option_value_id' => '-1',
                        'item_type' => 'option',
                        'item_name' => $data['text_option'].' > ' . strip_tags(html_entity_decode($product_option['name'], ENT_QUOTES, 'UTF-8')),
                        'name' => strip_tags(html_entity_decode($product_option['name'], ENT_QUOTES, 'UTF-8'))
                    );
                }


                if (isset($product_option['product_option_value'])) {
                    foreach ($product_option['product_option_value'] as $product_option_value) {
                        
                        if($this->isInFixedOptions($product_option, $filter_list) && $product_option['type']!='checkbox'){

                        }else{
                            $product_options[] = array(
                                'product_option_id' => $product_option['product_option_id'],
                                'option_id' => $product_option['option_id'],
                                'product_option_value_id' => $product_option_value['product_option_value_id'],
                                'option_value_id' => $product_option_value['option_value_id'],
                                'item_type' => 'option_value',
                                'item_name' => $data['text_option'].' > ' . strip_tags(html_entity_decode($product_option['name'] . ' > ' . $data['option_values'][$product_option['option_id']][$product_option_value['option_value_id']]['name'], ENT_QUOTES, 'UTF-8')),
                                'name' => strip_tags(html_entity_decode($product_option['name'] . ' > ' . $data['option_values'][$product_option['option_id']][$product_option_value['option_value_id']]['name'], ENT_QUOTES, 'UTF-8'))
                            );
                        }


                    }
                }

            }

        }

        
        if(!empty($filter_list)) {
            foreach ($product_options as $index => $product_option) {
                if ($this->isInOptionList($product_option, $filter_list) ) {
                    unset($product_options[$index]);
                }
            }
        }

        
        if(!empty($filter_name)) {
            foreach ($product_options as $index => $product_option) {
                if (!mb_stristr($product_option['item_name'], $filter_name) ) {
                    unset($product_options[$index]);
                }
            }
        }



        $json = $product_options;









        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function autocomplete_kit() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('catalog/bundle_expert_kit');
            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('catalog/option');

            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            $filter_name = trim(urldecode($filter_name));

            $filter_data = array(
                'filter_name'  => $filter_name,
                'start'        => 0,
                'limit'        => 20
            );

            $results = $this->model_catalog_bundle_expert_kit->getKits($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'kit_id' => $result['kit_id'],
                    'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                );

            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    public function autocomplete_kit_widget() {
        $json = array();

        if (isset($this->request->post['filter_name'])) {
            $this->load->model('catalog/bundle_expert_kit');
            $this->load->model('catalog/bundle_expert_widget');
            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('catalog/option');


            $filter_name = $this->request->post['filter_name'];
            $filter_name = trim(urldecode($filter_name));

            $filter_list = array();

            if (isset($this->request->post['kit_widgets'])) {
                $kit_widgets = $this->request->post['kit_widgets'];

                foreach ($kit_widgets as $kit_widget){
                    $filter_list[] = $kit_widget['widget_id'];
                }
            }

            $widgets = $this->model_catalog_bundle_expert_widget->getWidgets();


            
            foreach ($widgets as $index=>$widget) {
                $url = "";

                $widgets[$index]['href'] = html_entity_decode($this->url->link('catalog/bundle_expert_widget/edit', $this->token_name . $this->token_value . '&widget_id=' . $widget['widget_id'] . $url, 'SSL'));

                if (in_array($widget['widget_id'], $filter_list)) {

                }
            }


        }

        
        if(!empty($filter_name)) {
            foreach ($widgets as $index => $widget) {
                if (!mb_stristr($widget['name'], $filter_name)) {
                    unset($widgets[$index]);
                }
            }
        }

        $json = $widgets;

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function isInOptionList($product_option, $filter_list) {
        $is = false;

        foreach ($filter_list as $index => $value) {
            if ($product_option['product_option_id'] == $value['product_option_id']
                && $product_option['product_option_value_id'] == $value['product_option_value_id']
                && $product_option['item_type'] == $value['item_type']
            ) {
                $is = true;
                break;
            }
        }

        return $is;
    }

    
    private function isOptionDisabled($product_option, $filter_list) {
        $is = false;

        foreach ($filter_list as $index => $value) {
            if ($product_option['product_option_id'] == $value['product_option_id']
                && $value['item_type']=='option'
            ) {
                $is = true;
                break;
            }
        }

        return $is;
    }

    private function isInFixedOptions($option, $filter_list) {
        $is = false;

        foreach ($filter_list as $index => $value) {
            if ($option['product_option_id'] == $value['product_option_id']
                
            ) {
                $is = true;
                break;
            }
        }

        return $is;
    }

    
    private function checkOptionInProduct($option, $product_options){
        $option_in_product = false;

        foreach ($product_options as  $product_option){

            if ($option['item_type'] == 'option') {
                if($product_option['product_option_id']==$option['product_option_id']){
                    $option_in_product = true;
                    break;
                }
            }else{
                foreach ($product_option['product_option_value'] as $product_option_value){
                    if($product_option_value['product_option_value_id']==$option['product_option_value_id']){
                        $option_in_product = true;
                        break;
                    }
                }
            }

        }

        return $option_in_product;
    }

    protected function validateCopy() {
        if (!$this->user->hasPermission('modify', 'catalog/bundle_expert_kit')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function save_kit_ajax(){

        $json = array();

        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_kit');

        $this->load->model('catalog/bundle_expert_kit');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            if(isset($this->request->post['kit_id']) && !empty($this->request->post['kit_id'])){
                $kit_id =  $this->model_catalog_bundle_expert_kit->editKit($this->request->post['kit_id'], $this->request->post);
                $json['new_kit'] = false;
            }else{
                $kit_id =  $this->model_catalog_bundle_expert_kit->addKit($this->request->post);
                $json['new_kit'] = true;
            }

            $kit = $this->model_catalog_bundle_expert_kit->getKit($kit_id);

            if($kit){
                $json['success']  =  $this->language->get('text_success');
                $json['kit_id']  =  $kit['kit_id'];
            }else{
                $json['error'] = $this->language->get('text_error');
            }


        }else{
            $json['error'] = $this->error;

        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_product_id'])) {
            $url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if(isset($this->request->get['from_product_id'])){
            $json['redirect'] = $this->url->link('catalog/product/edit', $this->token_name . $this->token_value . $url . '&product_id=' . $this->request->get['from_product_id'], 'SSL');
        }else{
            $json['redirect'] = $this->url->link('catalog/bundle_expert_kit', $this->token_name . $this->token_value . $url, 'SSL');
        }


        $json['redirect']  = htmlspecialchars_decode($json['redirect'] );


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}