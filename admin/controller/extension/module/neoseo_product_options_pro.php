<?php

require_once( DIR_SYSTEM . "/engine/neoseo_controller.php");
require_once(DIR_SYSTEM . '/engine/neoseo_view.php' );

class ControllerExtensionModuleNeoSeoProductOptionsPro extends NeoSeoController
{

	private $error = array();

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->_moduleSysName = "neoseo_product_options_pro";
		$this->_module_code = 'neoseo_product_options_pro';
		$this->_logFile = $this->_moduleSysName() . ".log";
		$this->debug = $this->config->get($this->_moduleSysName(). "_debug");
	}

	public function index()
	{
		$this->upgrade();

		$data = $this->load->language('extension/module/' . $this->_moduleSysName());

		$this->document->setTitle($this->language->get('heading_title_raw'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting($this->_moduleSysName(), $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->load->model('extension/module/neoseo_product_options_pro');
			$this->model_extension_module_neoseo_product_options_pro->setModuleStatus($this->request->post[$this->_moduleSysName() . "_status"]);

			if (!isset($_GET["close"])) {
				$this->response->redirect($this->url->link('extension/' . $this->_route . '/' . $this->_moduleSysName(), 'user_token=' . $this->session->data['user_token'], 'SSL'));
			} else {
				$this->response->redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}
		}

		$data = $this->initParamsListEx($this->{"model_extension_" . $this->_route . "_" . $this->_moduleSysName()}->getParams(), $data);
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$data = $this->initBreadcrumbs(array(
			array("extension/module", "text_module"),
			array("module/" . $this->_moduleSysName(), "heading_title_raw")
				), $data);

		$data = $this->initButtons($data);

		$data["logs"] = $this->getLogs();

		$this->load->model('tool/image');
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$widgets = new NeoSeoWidgets('neoseo_product_options_pro_', $data);
		$widgets->text_select_all = $data['text_select_all'];
		$widgets->text_unselect_all = $data['text_unselect_all'];
		$data['widgets'] = $widgets;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/' . $this->_moduleSysName(), $data));
	}

	private function validate()
	{
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