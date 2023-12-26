<?php
class ControllerExtensionMayAdvancedOptionsSetting extends Controller {
    
	private $error = array();

	public function index() {
        $this->load->language('extension/may/advanced_options');
        $_ = $this->language->get('advanced_options');

		$this->document->setTitle($_['Maybooster Advanced Options']);
		$this->document->addStyle('view/javascript/may/css/colorpicker.css');
		$this->document->addScript('view/javascript/may/js/colorpicker.js');

		$this->document->addStyle('view/stylesheet/may/advanced_options.css');

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (isset($this->request->post['sync_stock'])) {
				$this->load->model('extension/may/advanced_options/product');
				$this->model_extension_may_advanced_options_product->syncProductAdvancedOptionsStock();
			}

			$this->model_setting_setting->editSetting('may_advanced_options', $this->request->post);

			$this->model_setting_setting->editSetting('module_may_advanced_options', array(
				'module_may_advanced_options_status' => $this->request->post['may_advanced_options_status']
			));

			$this->load->model('extension/may/advanced_options');
			$this->load->model('setting/event');
			$events = $this->model_setting_event->getEvents();	
			foreach ($events as $event) {
				if ($this->model_extension_may_advanced_options->isValidEvent($event['code'])) {
					$this->model_setting_event->deleteEventByCode($event['code']);
				}
			}
			$events = $this->model_extension_may_advanced_options->getEvents();
			foreach ($events as $event_code => $event) {
				$this->model_setting_event->deleteEventByCode($event_code);
				$this->model_setting_event->addEvent($event_code, $event['trigger'], $event['action']);
			}
	
			$this->session->data['success'] = $_['Success: You have modified Maybooster Advanced Options module!'];

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $_['Home'],
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $_['Extensions'],
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $_['Maybooster Advanced Options'],
			'href' => $this->url->link('extension/may/advanced_options/setting', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/may/advanced_options/setting', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            foreach ($this->request->post as $key => $value) {
                if (strpos($key, 'may_advanced_options_') === 0) {
                    $data[$key] = $value;
                }
            }
        } else {
            foreach ($this->model_setting_setting->getSetting('may_advanced_options') as $key => $value) {
                $data[$key] = $value;
            }
        }

		$this->load->model('extension/may/advanced_options/product');
		$data['total_stock_sync_errors'] = $this->model_extension_may_advanced_options_product->getTotalStockSyncErrors();

        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $data['_'] = $this->language->get('advanced_options');

		$this->response->setOutput($this->load->view('extension/may/advanced_options/setting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/may/advanced_options/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
