<?php

require_once( DIR_SYSTEM . "/engine/neoseo_controller.php");

class ControllerExtensionModuleNeoSeoProductOptionsPro extends NeoSeoController
{

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->_moduleSysName = "neoseo_product_options_pro";
		$this->_module_code = 'neoseo_product_options_pro';
		$this->_logFile = $this->_moduleSysName() . ".log";
		$this->debug = $this->config->get($this->_moduleSysName() . "_debug");
	}

	public function getProductOptions()
	{
		$json['success'] = false;

		if (isset($this->request->post['product_id']) && isset($this->request->post['option']) && !empty($this->request->post['option'])) {
			$product_id = $this->request->post['product_id'];
			$this->load->model('extension/module/' . $this->_moduleSysName());

			$json['success'] = true;
			$json[$product_id] = $this->model_extension_module_neoseo_product_options_pro->getProductOptions($product_id);
			if (!$json[$product_id]) {
				$json[$product_id] = false;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
