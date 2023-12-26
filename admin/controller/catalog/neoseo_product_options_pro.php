<?php

require_once( DIR_SYSTEM . "/engine/neoseo_controller.php");

class ControllerCatalogNeoSeoProductOptionsPro extends NeoSeoController
{

	private $error = array();

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->_moduleSysName = "neoseo_product_options_pro";
		$this->_module_code = 'neoseo_product_options_pro';
		$this->_logFile = $this->_moduleSysName() . ".log";
		$this->debug = $this->config->get($this->_moduleSysName() . "_debug");
	}

	public function index()
	{
		$this->load->language('catalog/' . $this->_moduleSysName());

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/option');
		$this->load->model('catalog/' . $this->_moduleSysName());

		$this->getList();
	}

	public function add()
	{
		$this->load->language('catalog/' . $this->_moduleSysName());

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/option');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->load->model('catalog/' . $this->_moduleSysName());
			$this->model_catalog_neoseo_product_options_pro->addOption($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit()
	{
		$this->load->language('catalog/' . $this->_moduleSysName());

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/option');
		$this->load->model('catalog/' . $this->_moduleSysName());

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_neoseo_product_options_pro->updateOption($this->request->post, $this->request->get['product_option_pro_id']);

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

			$this->response->redirect($this->url->link('catalog/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('catalog/' . $this->_moduleSysName());

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/option');
		$this->load->model('catalog/' . $this->_moduleSysName());

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $option_id) {
				$this->model_catalog_neoseo_product_options_pro->deleteOption($option_id);
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

			$this->response->redirect($this->url->link('catalog/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList()
	{
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'od.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['status'])) {
			$status = $this->request->get['status'];
		} else {
			$status = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('catalog/' . $this->_moduleSysName() . '/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/' . $this->_moduleSysName() . '/delete', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		$data['options_pro'] = array();

		$filter_data = array(
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$option_total = $this->model_catalog_neoseo_product_options_pro->getTotalOptions();

		$results = $this->model_catalog_neoseo_product_options_pro->getOptions($filter_data);

		foreach ($results as $result) {
			$data['options_pro'][] = array(
				'product_option_pro_id' => $result['product_option_pro_id'],
				'name' => $result['name'],
				'sort_order' => $result['sort_order'],
				'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit' => $this->url->link('catalog/' . $this->_moduleSysName() . '/edit', 'user_token=' . $this->session->data['user_token'] . '&product_option_pro_id=' . $result['product_option_pro_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

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
			$data['selected'] = (array) $this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'] . '&sort=bod.name' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('catalog/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'] . '&sort=bo.sort_order' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('catalog/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'] . '&sort=bo.status' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $option_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($option_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($option_total - $this->config->get('config_limit_admin'))) ? $option_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $option_total, ceil($option_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/' . $this->_moduleSysName() . '_list', $data));
	}

	protected function getForm()
	{
		$data = $this->load->language('catalog/' . $this->_moduleSysName());
		$data['text_form'] = !isset($this->request->get['option_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

		if (isset($this->error['no_options'])) {
			$data['error_no_options'] = $this->error['no_options'];
		} else {
			$data['error_no_options'] = false;
		}

		if (isset($this->error['duplicate'])) {
			$data['error_duplicate'] = $this->error['duplicate'];
		} else {
			$data['error_duplicate'] = false;
		}

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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['product_option_pro_id'])) {
			$data['action'] = $this->url->link('catalog/' . $this->_moduleSysName() . '/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/' . $this->_moduleSysName() . '/edit', 'user_token=' . $this->session->data['user_token'] . '&product_option_pro_id=' . $this->request->get['product_option_pro_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		if (isset($this->request->get['product_option_pro_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$option_info = $this->model_catalog_neoseo_product_options_pro->getOption($this->request->get['product_option_pro_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['option_description'])) {
			$data['option_description'] = $this->request->post['option_description'];
		} elseif (isset($this->request->get['product_option_pro_id'])) {
			$data['option_description'] = $option_info['description'];
		} else {
			$data['option_description'] = array();
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($option_info)) {
			$data['sort_order'] = $option_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($option_info)) {
			$data['status'] = $option_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['status_image'])) {
			$data['status_image'] = $this->request->post['status_image'];
		} elseif (!empty($option_info)) {
			$data['status_image'] = $option_info['status_image'];
		} else {
			$data['status_image'] = 0;
		}

		if (isset($this->request->post['option_related'])) {
			$option_values = $this->request->post['option_related'];
		} elseif (isset($this->request->get['product_option_pro_id'])) {
			$option_values = $option_info['options'];
		} else {
			$option_values = array();
		}

		$data['option_relateds'] = array();

		foreach ($option_values as $option_value) {
			$data['option_relateds'][] = array(
				'option_id' => $option_value['option_related_id'],
				'sort_order' => $option_value['sort_order']
			);
		}

		$results = $this->model_catalog_option->getOptions();

		$data['options_list'] = '';
		$data['options'] = array();

		foreach ($results as $result) {
			if (!($result['type'] == 'related' || $result['type'] == 'images')) {
				$data['options'][] = array(
					'option_id' => $result['option_id'],
					'name' => $result['name'],
					'sort_order' => $result['sort_order'],
				);
				$data['options_list'] .= '<option value="' . $result['option_id'] . '">' . $result['name'] . '</option>';
			}
		}
		$this->load->model('tool/image');
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/' . $this->_moduleSysName() . '_form', $data));
	}

	protected function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/' . $this->_moduleSysName())) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['option_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 128)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		if (!isset($this->request->post['option_related']) || count($this->request->post['option_related']) < 1) {
			$this->error['no_options'] = $this->language->get('error_no_options');
		} if (isset($this->request->post['option_related'])) {
			$options = array();
			foreach ($this->request->post['option_related'] as $option) {
				$options[] = $option['option_related_id'];
			}
			foreach (array_count_values($options) as $value) {
				if ($value >= 2) {
					$this->error['duplicate'] = $this->language->get('error_duplicate');
				}
			}
		}

		return !$this->error;
	}

	protected function validateDelete()
	{
		//TODO добавить проверку к удалению по связным к опции товарам
		if (!$this->user->hasPermission('modify', 'catalog/' . $this->_moduleSysName())) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');

		foreach ($this->request->post['selected'] as $option_id) {
			$product_total = $this->model_catalog_neoseo_product_options_pro->getTotalProductsByOptionId($option_id);
			if ($product_total) {
				$list = '<ul>';
				$count = 0;
				$end_message = '';
				foreach ($product_total as $product) {
					if ($count < 20) {
						$link = $this->url->link('catalog/product/edit&user_token=' . $this->session->data['user_token'] . '&product_id=' . $product['product_id']);
						$list .= '<li>' . sprintf($this->language->get('error_option_product'), $link, $product['name']) . '</li>';
					} else {
						$end_message = sprintf($this->language->get('error_and_more_products'), (count($product_total) - 20));
					}
				}
				$list .= '</ul>';
				$list = $list . $end_message;
				$this->error['warning'] = sprintf($this->language->get('error_product'), count($product_total), $list);
			}
		}
		return !$this->error;
	}

	public function getCurrencys()
	{
		$json = array();

		if (isset($this->request->get['base_price']) && is_numeric($this->request->get['base_price'])) {
			if (isset($this->request->get['manufacturer_id'])) {
				$manufacturer_id = $this->request->get['manufacturer_id'];
			} else {
				$manufacturer_id = '';
			}

			if (isset($this->request->get['base_price'])) {
				$base_price = $this->request->get['base_price'];
			} else {
				$base_price = '0';
			}

			$this->load->model('catalog/neoseo_product_options_pro');

			if (!empty($manufacturer_id)) {
				$results = $this->model_catalog_neoseo_product_options_pro->getCurrencys($manufacturer_id);

				foreach ($results as $result) {
					$json['price'] = round($result['rate'] * $base_price, 1);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
