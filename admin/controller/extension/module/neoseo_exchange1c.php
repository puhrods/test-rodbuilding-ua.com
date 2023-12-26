<?php

require_once(DIR_SYSTEM . "/engine/neoseo_controller.php");
require_once(DIR_SYSTEM . '/engine/neoseo_view.php' );

class ControllerExtensionModuleNeoSeoExchange1c extends NeoSeoController
{

	private $error = array();

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->_moduleSysName = "neoseo_exchange1c";
		$this->_module_code = "neoseo_exchange1c";
		$this->_logFile = $this->_moduleSysName() . ".log";
		$this->debug = $this->config->get($this->_moduleSysName() . "_debug") == 1;
	}

	public function index()
	{
		$this->upgrade();

		$data = $this->language->load('extension/module/neoseo_exchange1c');
		$this->document->setTitle($this->language->get('heading_title_raw'));

		$this->load->model("extension/" . $this->_route . "/" . $this->_moduleSysName());
		$this->load->model('setting/setting');
		$this->load->model('tool/image');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting($this->_moduleSysName(), $this->request->post);
			$this->model_extension_module_neoseo_exchange1c->setModuleStatus($this->request->post[$this->_moduleSysName() . "_status"]);
			$this->session->data['success'] = $this->language->get('text_success');
			if ($this->request->post['action'] == "save") {
				$this->response->redirect($this->url->link('extension/module/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'], 'SSL'));
			} else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$data = $this->initBreadcrumbs(array(
			array('marketplace/extension', 'text_module'),
			array('extension/' . $this->_route . '/' . $this->_moduleSysName(), "heading_title_raw")
				), $data);

		$data = $this->initButtons($data);

		$data['user_token'] = $this->session->data['user_token'];
		$data['import'] = $this->url->link('tool/neoseo_exchange1c/import', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['export'] = $this->url->link('tool/neoseo_exchange1c/export', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['export_product'] = $this->url->link('tool/neoseo_exchange1c/export_product', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['check_password'] = $this->url->link('module/neoseo_exchange1c/check_password', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data[$this->_moduleSysName() . '_link'] = rtrim(HTTP_CATALOG, "/") . "/export/neoseo_exchange1c.php";
		$data[$this->_moduleSysName() . "_cron_command"] = "php " . realpath(DIR_SYSTEM . "../export/" . $this->_moduleSysName() . ".php") . " import";

		$data['delete_orders'] = $this->url->link('tool/neoseo_exchange1c/delete_orders', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['delete_export_list_orders'] = $this->url->link('tool/neoseo_exchange1c/deleteExportListOrders', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['get_orders'] = $this->url->link('tool/neoseo_exchange1c/get_orders', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['delete_categories'] = $this->url->link('tool/neoseo_exchange1c/delete_categories', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['delete_manufacturers'] = $this->url->link('tool/neoseo_exchange1c/delete_manufacturers', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['delete_products'] = $this->url->link('tool/neoseo_exchange1c/delete_products', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['delete_1c_products'] = $this->url->link('tool/neoseo_exchange1c/delete_1c_products', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['delete_products_warehouses'] = $this->url->link('tool/neoseo_exchange1c/delete_products_warehouses', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['delete_attributes'] = $this->url->link('tool/neoseo_exchange1c/delete_attributes', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['delete_options'] = $this->url->link('tool/neoseo_exchange1c/delete_options', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['delete_links'] = $this->url->link('tool/neoseo_exchange1c/delete_links', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['counterparties_download'] = $this->url->link('tool/neoseo_exchange1c/exportContragents', 'token=' . $this->session->data['user_token'], 'SSL');
		$data['counterparties_delete'] = $this->url->link('tool/neoseo_exchange1c/counterpartiesDelete', 'token=' . $this->session->data['user_token'], 'SSL');
		$data['counterparties_delete_links'] = $this->url->link('tool/neoseo_exchange1c/counterpartiesDeleteLinks', 'token=' . $this->session->data['user_token'], 'SSL');

		$data = $this->initParamsListEx($this->{"model_extension_" . $this->_route . "_" . $this->_moduleSysName()}->getParams(), $data);

		$this->load->model('customer/customer_group');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['customer_groups_special'] = array();

		foreach ($data['customer_groups'] as $customer_group) {
			$data['customer_groups_special'][$customer_group['customer_group_id']] = $customer_group['name'];
		}

		$this->load->model('localisation/stock_status');

		$results = $this->model_localisation_stock_status->getStockStatuses();
		$stock_statuses = array();
		foreach ($results as $result) {
			$stock_statuses[$result['stock_status_id']] = $result['name'];
		}
		$data['stock_statuses'] = $stock_statuses;

		$this->load->model('localisation/order_status');

		$order_statuses = $this->model_localisation_order_status->getOrderStatuses();
		$statuses = array();
		foreach ($order_statuses as $order_status) {
			$statuses[$order_status['order_status_id']] = $order_status['name'];
		}

		$this->load->model('catalog/category');

		$filter_data = array(
			'sort' => 'name',
			'order' => 'ASC'
		);

		$data['categories'] = array();
		$categories = $this->model_catalog_category->getCategories($filter_data);
		foreach ($categories as $category) {
			$data['categories'][$category['category_id']] = $category['name'];
		}

		$data['order_statuses'] = $statuses;

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();
		$product_languages = array();
		foreach ($languages as $language) {
			$product_languages[$language['language_id']] = $language['name'];
		}
		$data['product_languages'] = $product_languages;

		//Чтобы обновление с более ранних версий прошло незаметно для Клиента
		if ($data[$this->_moduleSysName() . "_disable_missing"] == 1) {
			$data[$this->_moduleSysName() . "_disable_missing"] = array(1);
		} elseif ($data[$this->_moduleSysName() . "_disable_missing"] == 2) {
			$data[$this->_moduleSysName() . "_disable_missing"] = array(1, 2);
		}
		if ($data[$this->_moduleSysName() . "_price_special"]) {
			$data[$this->_moduleSysName() . "_special_price_type"] = array(array(
					'keyword' => $data[$this->_moduleSysName() . "_price_special"],
					'customer_group_id' => $data[$this->_moduleSysName() . "_special_group_id"],
					'priority' => 0
			));
		}

		$data['disable_missing'] = array(
			1 => $this->language->get('text_disable_out_of_stock'),
			2 => $this->language->get('text_disable_out_of_stock_null_price'),
			3 => $this->language->get('text_disable_out_of_stock_without_images'),
			4 => $this->language->get('text_disable_null_price'),
			5 => $this->language->get('text_disable_without_images'),
			6 => $this->language->get('text_disable_null_quantity'),
		);

		$data['transaction_statuses'] = array(
			0 => $this->language->get('text_disabled'),
			1 => $this->language->get('text_transaction_always'),
			2 => $this->language->get('text_transaction_change'),
		);

		$data['tax_list'] = array(
			0 => $this->language->get('text_disabled')
		);

		$this->load->model('localisation/tax_rate');
		$tax_data = $this->model_localisation_tax_rate->getTaxRates();
		foreach ($tax_data as $tax) {
			$data['tax_list'][$tax['tax_rate_id']] = $tax['name'];
		}

		$data['filters'] = array(
			0 => $this->language->get('text_disabled'),
			'neoseo_filter' => $this->language->get('text_neoseo_filter'),
			'ocfilter' => $this->language->get('text_ocfilter'),
			'filter' => $this->language->get('text_filter'),
		);

		$pd_columns_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_description`;");

		$pd_columns = array();

		foreach ($pd_columns_query->rows as $column) {
			if ($column['Field'] == 'product_id' || $column['Field'] == 'language_id') {
				continue;
			}

			$pd_columns[$column['Field']] = $column['Field'];
		}

		$data['product_description_columns'] = $pd_columns;

		// API login
		// API login
		$this->load->model('user/api');

		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

		if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
			$session = new Session($this->config->get('session_engine'), $this->registry);

			$session->start();

			if (substr(VERSION, 0, 7) == '3.0.3.7' || substr(VERSION, 0, 7) == '3.0.3.8') {
				$this->model_user_api->deleteApiSessionBySessionId($session->getId());
			}
			else {
				$this->model_user_api->deleteApiSessionBySessonId($session->getId());
			}

			$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);

			$session->data['api_id'] = $api_info['api_id'];

			$data['api_token'] = $session->getId();
		} else {
			$data['api_token'] = '';
		}

		$data['isOptionsHasSpecial'] = $this->{"model_extension_" . $this->_route . "_" . $this->_moduleSysName()}->isOptionsHasSpecial();

		$data['cprice_module_status'] = $this->{"model_extension_" . $this->_route . "_" . $this->_moduleSysName()}->getCpriceModuleStatus();


		$data['store'] = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;
		$data['params'] = $data;
		$data["logs"] = $this->getLogs();

		if($this->config->get("neoseo_exchange1c_exchange_control")){
			$this->load->model("tool/neoseo_exchange1c");
			$status_data = $this->model_tool_neoseo_exchange1c->getExchangeStatus();
		}else{
			$status_data['status'] = 0;
			$status_data['time'] = 0;
		}

		$data["exchange_control_status"] = $status_data['status'];
		$data["exch_time"] = $status_data['time'];
		$data['exchange_control_link_clear'] = $this->url->link('tool/neoseo_exchange1c/clearExchangeStatus', 'user_token=' . $this->session->data['user_token'], 'SSL');


		$widgets = new NeoSeoWidgets($this->_moduleSysName() . '_', $data);
		$widgets->text_select_all = $this->language->get('text_select_all');
		$widgets->text_unselect_all = $this->language->get('text_unselect_all');
		$data['widgets'] = $widgets;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/' . $this->_moduleSysName(), $data));
	}

	private function validate()
	{
		if(!$this->validateKey()){
			$this->response->redirect($this->url->link('extension/module/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}

		if (!$this->user->hasPermission('modify', 'extension/module/' . $this->_moduleSysName())) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

}

?>
