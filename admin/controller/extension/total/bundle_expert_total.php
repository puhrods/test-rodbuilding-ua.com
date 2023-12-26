<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ControllerExtensionTotalBundleExpertTotal extends Controller {
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
		$this->load->language('extension/total/bundle_expert_total');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            if (version_compare(VERSION, '3.0.0.0', '<')) {
                $this->model_setting_setting->editSetting('bundle_expert_total', $this->request->post);
            } else {
                $this->model_setting_setting->editSetting('total_bundle_expert_total', $this->request->post);
            }

			$this->session->data['success'] = $this->language->get('text_success');


            $this->response->redirect($this->url->link($this->path_extension_module, $this->token_name . $this->token_value, 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');

		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_fee'] = $this->language->get('entry_fee');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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
			'text' => $this->language->get('text_total'),
			'href' => $this->url->link('extension/total', $this->token_name . $this->token_value, 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/total/bundle_expert_total', $this->token_name . $this->token_value, 'SSL')
		);

		$data['action'] = $this->url->link('extension/total/bundle_expert_total', $this->token_name . $this->token_value, 'SSL');

		$data['cancel'] = $this->url->link('extension/total', $this->token_name . $this->token_value, 'SSL');


        if (version_compare(VERSION, '3.0.0.0', '<')) {
            $name_status='bundle_expert_total_status';
            $name_sort_order='bundle_expert_total_sort_order';
        }else{
            $name_status='total_bundle_expert_total_status';
            $name_sort_order='total_bundle_expert_total_sort_order';
        }

        if (isset($this->request->post[$name_status])) {
            $data['status'] = $this->request->post[$name_status];
        } else {
            $data['status'] = $this->config->get($name_status);
        }

        if (isset($this->request->post[$name_sort_order])) {
            $data['sort_order'] = $this->request->post[$name_sort_order];
        } else {
            $data['sort_order'] = $this->config->get($name_sort_order);
        }

        $data['name_status']=$name_status;
        $data['name_sort_order']=$name_sort_order;





		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        if (version_compare(VERSION, '3.0.0.0', '<')) {
            $this->response->setOutput($this->load->view('extension/total/bundle_expert_total.tpl', $data));
        }else{
            $this->response->setOutput($this->load->view('extension/total/bundle_expert_total', $data));
        }

	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/total/bundle_expert_total')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}