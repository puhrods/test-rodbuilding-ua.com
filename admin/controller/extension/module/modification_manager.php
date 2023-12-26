<?php
class ControllerExtensionModuleModificationManager extends Controller {
	public function index() {
		$this->response->redirect($this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token']));
	}

	public function install() {
		$data = array(
			'module_modification_manager_status' => 1
		);

		$this->load->model('setting/setting');

		$this->model_setting_setting->editSetting('module_modification_manager', $data);

		$this->load->model('extension/module/modification_manager');

		$this->model_extension_module_modification_manager->install();

		return true;
	}
}