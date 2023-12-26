<?php

namespace liveopencart\lib\v0007;

class ControllerAdminExtension extends \Controller {
	use traits\config;
	use traits\language;
	use traits\json;
	use traits\db;
	
	
	protected $extension_type='module';
	protected $extension_code='';
	
	protected $route_home_page 			= 'common/dashboard';
	protected $route_extensions			= 'marketplace/extension';
	
	protected $error = array();
	
	protected $event_prefix = 'liveopencart.';
	protected $events = array();
	
	/*
	public function __construct() {
		call_user_func_array( array('parent', '__construct') , func_get_args());
	}
	*/
	
	public function indexStandard($data=array()) {
		
    $this->loadLanguage();
		
		$data = array_merge($data, $this->getLinks());
    
    $this->document->setTitle($this->language->get('module_name'));
		
		$this->load->model('setting/setting');
		
		$this->saveSettingsIfAny($data);
    
    if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['config_admin_language']	= $this->config->get('config_admin_language');
		
		if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    }
    
		$data['settings'] = $this->getCurrentPageSettings();
		
		$this->setOutputStandard($data);
  
  }
	
	
	protected function loadView($data, $custom_code='') {
		return $this->load->view($this->getRouteExtension($custom_code), $data);
	}
	
	
	protected function getLinkWithToken($route, $params='') {
		$current_params = $params;
		if ( $current_params && substr($current_params, 0, 1) != '&' ) {
			$current_params = '&'.$current_params;
		}
		return $this->url->link($route, 'user_token='.$this->session->data['user_token'] . $current_params , true);
	}
	
	protected function getLinks() {
		
		$data = array();
		
		$data['breadcrumbs'] = $this->getBreadcrumbs();
		$data['action'] = $this->getLinkWithToken( $this->getRouteExtension() );
		$data['cancel'] = $this->getLinkWithToken( $this->getRouteExtensions(), '&type='.$this->extension_type);
		$data['redirect'] = $this->getLinkWithToken( $this->getRouteExtension() );
		
		return $data;
		
	}
	
	protected function getRouteHomePage() {
		return $this->route_home_page;
	}
	
	protected function getRouteExtensions() {
		return $this->route_extensions;
	}
	
	protected function getRouteExtension($custom_code='', $addition_code='') {
		return 'extension/'.$this->extension_type.'/'.( $custom_code ? $custom_code : $this->extension_code).( $addition_code ? '/'.$addition_code : '' );
	}
	
	protected function getBreadcrumbs() {
		
		$breadcrumbs = array();

		$breadcrumbs[] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->getLinkWithToken( $this->getRouteHomePage() )
		);

		$breadcrumbs[] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->getLinkWithToken( $this->getRouteExtensions(), '&type='.$this->extension_type)
		);

		$breadcrumbs[] = array(
			'text' => $this->language->get('module_name'),
			'href' => $this->getLinkWithToken( $this->getRouteExtension() )
		);
		
		return $breadcrumbs;
	}
	
	protected function setSettings($settings) {
		$data = array();
		foreach ( $settings as $setting_key => $setting_value ) {
			$data[$this->getExtensionSettingPrefix().$setting_key] = $setting_value;
		}
		
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting($this->getExtensionSettingCode(), $data );
		
	}
	
	
	protected function getCurrentPageSettings($defaults=array()) {
		$settings = $defaults;
		if (isset($this->request->post['settings'])) {
			$settings = $this->request->post['settings'];
		} elseif ($this->getExtensionConfig('settings')) { 
			$settings = $this->getExtensionConfig('settings');
		} else {
			$settings = array();
		}
		return $settings;
	}
	
	protected function saveSettingsIfAny($data) {
		if ( $this->existSettingsToSave() ) {
			$this->saveSettingsStandard($data);
		}
	}
	
	protected function existSettingsToSave() {
		return (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate());
	}
	
	protected function saveSettingsStandard($data, $post_data=false) {
		
		if ( $post_data===false ) {
			$post_data = $this->request->post;
		}
		$settings = array(
			'settings'=>isset($post_data['settings']) ? $post_data['settings'] : array(),
			'status'=>isset($post_data['settings']['status']) ? $post_data['settings']['status'] : '', // for standard opencart module status
		);
		$this->setSettings( $settings );
	
		$this->session->data['success'] = $this->language->get('text_success');
		$this->response->redirect($data['redirect']);
	}
	
	protected function setOutputStandard($data) {
		
		$data['header'] 			= $this->load->controller('common/header');
		$data['column_left'] 	= $this->load->controller('common/column_left');
		$data['footer'] 			= $this->load->controller('common/footer');

		$this->response->setOutput( $this->loadView($data) );
	}
	
	protected function updateEvents() {
		$this->load->model('setting/event');
		
		foreach ( $this->getEvents() as $event ) {
			$status = isset($event['status']) ? event['status'] : 1;
			$sort_order = isset($event['sort_order']) ? $event['sort_order'] : 0;
			
			if ( $status ) {
				$oc_event = $this->model_setting_event->getEventByCode($event['code']);
				if ( $oc_event ) {
					if ( $oc_event['trigger'] != $event['trigger'] || $oc_event['action'] != $event['action'] ) {
						$this->model_setting_event->deleteEventByCode($event['code']);
						$this->model_setting_event->addEvent($event['code'], $event['trigger'], $event['action'], $status, $sort_order);
					}
				} else {
					$this->model_setting_event->addEvent($event['code'], $event['trigger'], $event['action'], $status, $sort_order);
				}
			} else { // we remove disabled events
				$this->model_setting_event->deleteEventByCode($event['code']);
			}
		}
	}
	
	protected function removeEvents() {
		$this->load->model('setting/event');
		foreach ( $this->getEvents() as $event ) {
			$this->model_setting_event->deleteEventByCode($event['code']);
		}
	}
	
	protected function getEvents() { // returns events with prefixes added to codes
		$events = array();
		foreach ( $this->events as $event ) {
			$event['code'] = $this->event_prefix.$event['code'];
			$events[] = $event;
		}
		return $events;
	}
	
	
	protected function validatePermission() {
		if ( !$this->user->hasPermission('modify', 'extension/'.$this->extension_type.'/'.$this->extension_code) ) {
			$this->error['warning'] = $this->language->get('error_permission');
			return false;
		}
		return true;
	}
	
	private function validate() {
		return $this->validatePermission();
	}
	
}