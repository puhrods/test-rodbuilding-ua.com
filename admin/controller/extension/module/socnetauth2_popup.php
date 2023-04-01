<?php
class ControllerExtensionModuleSocnetauth2Popup extends Controller {
	private $error = array();
	private $data = array();

	public function index() {
		$this->load->language('extension/module/socnetauth2_popup');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			foreach($this->request->post as $key=>$val)
			{
				if( preg_match("/^socnetauth2/", $key) )
				{
					$this->request->post[ 'module_'.$key ] = $val;
					unset($this->request->post[ $key ]);
				}
			}
			
			$this->model_setting_setting->editSetting('module_socnetauth2_popup', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_edit'] = $this->language->get('text_edit');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');

		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_label'] = $this->language->get('entry_label');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=module', 'SSL')
		);

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/socnetauth2_popup', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$this->data['action'] = $this->url->link('extension/module/socnetauth2_popup', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$this->data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=module', 'SSL');

		if (isset($this->request->post['socnetauth2_popup_status'])) {
			$this->data['socnetauth2_popup_status'] = $this->request->post['socnetauth2_popup_status'];
		} else {
			$this->data['socnetauth2_popup_status'] = $this->config->get('module_socnetauth2_popup_status');
		}

		$default_hash = array();
		
		$this->load->model('localisation/language');
		
		
		$default_hash = array();
		
		$language_list = $this->model_localisation_language->getLanguages();

		foreach ($language_list as $result) {
			
			$directory = $result['code'];
				
			$language = new Language($directory);
			$language->load('extension/module/socnetauth2_popup');	
			
			$default_hash['default_popup_label'][ $result['language_id'] ] = $language->get('default_popup_label');
		}
		
		
		if (isset($this->request->post['socnetauth2_popup_label'])) {
			$this->data['socnetauth2_popup_label'] = $this->request->post['socnetauth2_popup_label'];
		} elseif ($this->config->has('module_socnetauth2_popup_label')) { 
			$this->data['socnetauth2_popup_label'] = $this->custom_unserialize($this->config->get('module_socnetauth2_popup_label'));
		} else {
			$this->data['socnetauth2_popup_label'] = $default_hash['default_popup_label'];
		}
		
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach($this->data['languages'] as $i=>$lang)
		{
			$this->data['languages'][$i]['image'] = 'language/'.$lang['code'].'/'.$lang['code'].'.png';
			
		}
		
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/socnetauth2_popup', $this->data));
	}
	
	private function custom_unserialize($s)
	{
		if( is_array($s) ) return $s;
	
		if(
			stristr($s, '{' ) != false &&
			stristr($s, '}' ) != false &&
			stristr($s, ';' ) != false &&
			stristr($s, ':' ) != false
		){
			return unserialize($s);
		}else{
			return $s;
		}

	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/socnetauth2_popup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}