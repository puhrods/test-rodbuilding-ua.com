<?php

require_once(DIR_SYSTEM . "/engine/neoseo_controller.php");

class ControllerCatalogNeoSeoWarehouse extends NeoSeoController
{

	private $error = array();

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->_sysModuleName = "neoseo_exchange1c";
		$this->_module_code = "neoseo_exchange1c";
		$this->_logFile = $this->_moduleSysName() . ".log";
		$this->debug = $this->config->get($this->_moduleSysName() . "_debug") == 1;
	}

	public function index()
	{
		$data = $this->language->load('catalog/neoseo_warehouse');

		$this->document->setTitle($this->language->get('heading_title_raw'));

		$this->getList();
	}

	public function add()
	{
		$this->language->load('catalog/neoseo_warehouse');

		$this->document->setTitle($this->language->get('heading_title_raw'));

		$this->load->model('catalog/neoseo_warehouse');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_neoseo_warehouse->addWarehouse($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/neoseo_warehouse', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit()
	{
		$this->language->load('catalog/neoseo_warehouse');

		$this->document->setTitle($this->language->get('heading_title_raw'));

		$this->load->model('catalog/neoseo_warehouse');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_neoseo_warehouse->editWarehouse($this->request->get['warehouse_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/neoseo_warehouse', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('catalog/neoseo_warehouse');

		$this->document->setTitle($this->language->get('heading_title_raw'));

		$this->load->model('catalog/neoseo_warehouse');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $warehouse_id) {
				$this->model_catalog_neoseo_warehouse->deleteWarehouse($warehouse_id);
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

			$this->response->redirect($this->url->link('catalog/neoseo_warehouse', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm()
	{
		$data = $this->language->load('catalog/neoseo_warehouse');

		$data['text_form'] = !isset($this->request->get['warehouse_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['code_1c'])) {
			$data['error_code_1c'] = $this->error['code_1c'];
		} else {
			$data['error_code_1c'] = '';
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
			'text' => $this->language->get('heading_title_raw'),
			'href' => $this->url->link('catalog/neoseo_warehouse', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['warehouse_id'])) {
			$data['action'] = $this->url->link('catalog/neoseo_warehouse/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/neoseo_warehouse/edit', 'user_token=' . $this->session->data['user_token'] . '&warehouse_id=' . $this->request->get['warehouse_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/neoseo_warehouse', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		if (isset($this->request->get['warehouse_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$warehouse_info = $this->model_catalog_neoseo_warehouse->getWarehouse($this->request->get['warehouse_id']);
		}

		if (isset($this->request->post['code_1c'])) {
			$data['code_1c'] = $this->request->post['code_1c'];
		} elseif (!empty($warehouse_info)) {
			$data['code_1c'] = $warehouse_info['1c_id'];
		} else {
			$data['code_1c'] = '';
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($warehouse_info)) {
			$data['name'] = $warehouse_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($warehouse_info)) {
			$data['sort_order'] = $warehouse_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['warehouse_description'])) {
			$data['warehouse_description'] = $this->request->post['warehouse_description'];
		} elseif (isset($this->request->get['warehouse_id'])) {
			$data['warehouse_description'] = $this->model_catalog_neoseo_warehouse->getWarehouseDescriptions($this->request->get['warehouse_id']);
		} else {
			$data['warehouse_description'] = array();
		}


		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['warehouse_store'])) {
			$data['warehouse_store'] = $this->request->post['warehouse_store'];
		} elseif (isset($this->request->get['warehouse_id'])) {
			$data['warehouse_store'] = $this->model_catalog_neoseo_warehouse->getWarehouseStores($this->request->get['warehouse_id']);
		} else {
			$data['warehouse_store'] = array(0);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/neoseo_warehouse_form', $data));
	}

	protected function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/neoseo_warehouse')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (utf8_strlen($this->request->post['name']) < 1) {
			$this->error['name'] = $this->language->get('error_required');
		}
		if (utf8_strlen($this->request->post['code_1c']) < 1) {
			$this->error['code_1c'] = $this->language->get('error_required');
		}
		$this->request->post['1c_id'] = $this->request->post['code_1c'];

		return !$this->error;
	}

	protected function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'catalog/neoseo_warehouse')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function getList()
	{
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
			'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_raw'),
			'href' => $this->url->link('catalog/neoseo_warehouse', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'),
			'separator' => ' :: '
		);

		$data['warehouses'] = array();

		$data1 = array(
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$this->load->model('catalog/neoseo_warehouse');
		$warehouse_total = $this->model_catalog_neoseo_warehouse->getTotalWarehouses();

		$results = $this->model_catalog_neoseo_warehouse->getWarehouses($data1);

		foreach ($results as $result) {
			$data['warehouses'][] = array(
				'warehouse_id' => $result['warehouse_id'],
				'name' => $result['name'],
				'1c_id' => $result['1c_id'],
				'selected' => isset($this->request->post['selected']) && in_array($result['warehouse_id'], $this->request->post['selected']),
				'edit' => $this->url->link('catalog/neoseo_warehouse/edit', 'user_token=' . $this->session->data['user_token'] . '&warehouse_id=' . $result['warehouse_id'] . $url, 'SSL'),
			);
		}

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

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/neoseo_warehouse', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, 'SSL');
		$data['sort_1c_id'] = $this->url->link('catalog/neoseo_warehouse', 'user_token=' . $this->session->data['user_token'] . '&sort=1c_id' . $url, 'SSL');
		$data['add'] = $this->url->link('catalog/neoseo_warehouse/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/neoseo_warehouse/delete', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $warehouse_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/neoseo_warehouse', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($warehouse_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($warehouse_total - $this->config->get('config_limit_admin'))) ? $warehouse_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $warehouse_total, ceil($warehouse_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/neoseo_warehouse_list', $data));
	}

	public function autocomplete()
	{
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/neoseo_warehouse');

			$data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start' => 0,
				'limit' => 20
			);

			$json = array();

			$results = $this->model_catalog_neoseo_warehouse->getWarehouses($data);

			foreach ($results as $result) {
				$json[] = array(
					'warehouse_id' => $result['warehouse_id'],
					'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->setOutput(json_encode($json));
	}

}

?>