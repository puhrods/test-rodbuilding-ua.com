<?php

class ControllerExtensionModuleSearchEngine extends Controller {

	private $c = array();

	public function index() {

		$this->load->language('extension/module/search_engine');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('extension/module/search_engine');

		unset($this->session->data['indexing_process']);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

      if (!empty($this->request->post['module_search_engine_options']['categories'])) {
        foreach($this->request->post['module_search_engine_options']['categories'] as $d => $e) {
          if (!is_numeric($e['coefficient'])) {
            $this->request->post['module_search_engine_options']['categories'][$d]['coefficient'] = 1;
          }
        }
      }

      if (!empty($this->request->post['module_search_engine_options']['manufacturers'])) {
        foreach($this->request->post['module_search_engine_options']['manufacturers'] as $d => $f) {
          if (!is_numeric($f['coefficient'])) {
            $this->request->post['module_search_engine_options']['manufacturers'][$d]['coefficient'] = 1;
          }
        }
      }

			$this->model_setting_setting->editSetting('module_search_engine', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

					}

		if (isset($this->error['warning'])) {
			$g['error_warning'] = $this->error['warning'];
		} else {
			$g['error_warning'] = '';
		}

		if (isset($this->error['new_fields'])) {
			$g['error_new_fields'] = $this->error['new_fields'];
		} else {
			$g['error_new_fields'] = '';
		}
		
    if (isset($this->session->data['success'])) {
      $g['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    } else {
      $g['success'] = '';
    }

		$g['breadcrumbs'] = array();

		$g['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
		);

		$g['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
		);

		$g['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/search_engine', 'user_token=' . $this->session->data['user_token'], true),
		);

		$g['action'] = $this->url->link('extension/module/search_engine', 'user_token=' . $this->session->data['user_token'], true);
		$g['delete'] = $this->url->link('extension/module/search_engine/delete', 'user_token=' . $this->session->data['user_token'], true);
		$g['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$this->load->model('localisation/language');
		$g['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['module_search_engine_status'])) {
			$g['status'] = $this->request->post['module_search_engine_status'];
		} else {
			$g['status'] = $this->config->get('module_search_engine_status');
		}

		if (isset($this->request->post['module_search_engine_options'])) {
			$g['options'] = $this->request->post['module_search_engine_options'];
		} elseif ($this->config->get('module_search_engine_options')) {
			$g['options'] = $this->config->get('module_search_engine_options');
    }
    
    if (empty($g['options']['types_order'])) {
      $g['options']['types_order'] = array(
        'fix_keyboard_layout' => array('sort' => 0),
        'transliteration' => array('sort' => 1),
        'inexact_search' => array('sort' => 2)
      );
    }

    uasort($g['options']['types_order'], array($this, 'sort_fields'));

    if (!isset($g['options']['categories'])) {
      $g['options']['categories'] = array();
    }

    if (!isset($g['options']['manufacturers'])) {
      $g['options']['manufacturers'] = array();
    }

    $g['fields'] = $this->model_extension_module_search_engine->getFields($g['options']);
    $g['parts_of_speech'] = $this->model_extension_module_search_engine->getPartsOfSpeech();
		
		$g['total_indexed'] = $this->model_extension_module_search_engine->getTotalIndexed();
		$g['total_not_indexed'] = $this->model_extension_module_search_engine->getTotalNotIndexed();
		
		$g['user_token'] = $this->session->data['user_token'];
		
		$g['header'] = $this->load->controller('common/header');
		$g['column_left'] = $this->load->controller('common/column_left');
		$g['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/search_engine', $g));
	}

  private function sort_fields ($h, $i) {
    return $h['sort'] - $i['sort'];
  }

  public function install() {

		$this->load->model('setting/setting');
		$this->load->model('extension/module/search_engine');

		$this->model_extension_module_search_engine->install();

		$this->model_setting_setting->deleteSetting('search_engine');

		$j['module_search_engine_options'] = $this->model_extension_module_search_engine->getDefaultOptions();
		$j['module_search_engine_status'] = 1; 
		
		$this->model_setting_setting->editSetting('module_search_engine', $j);		
	}

	public function uninstall() {

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_search_engine');
		$this->load->model('extension/module/search_engine');
		$this->model_extension_module_search_engine->uninstall();
	}

	public function add() {
		
		$this->load->language('extension/module/search_engine');
		
		$this->load->model('extension/module/search_engine');
		
		$k = array();
		
		$l = 100;
		
		if ($this->validate()) {
			
			$m = $this->model_extension_module_search_engine->getTotalNotIndexed();
			
			if ($m == 0) {

				$k['progress'] = 100;
				$k['success'] = $this->language->get('text_success_index');
				
				unset($this->session->data['indexing_process']);				
				
			} else {
			
				if (!isset ($this->session->data['indexing_process'])) {
					$this->session->data['indexing_process'] = array();
					$this->session->data['indexing_process']['start_not_indexed'] = $m;
					$this->session->data['indexing_process']['last_not_indexed'] = 0;
				}

				$n = $this->session->data['indexing_process'];

				$o = number_format(($n['start_not_indexed'] - $m) * 100 / $n['start_not_indexed'], 2);
				
				if ($o < 100) {

					if ($n['last_not_indexed'] == 0 || ($n['last_not_indexed'] - $l) >= $m) {
						$this->session->data['indexing_process']['last_not_indexed'] = $m;
						$c = $this->model_extension_module_search_engine->addIndexes($l);														
						if ($c) {
							$k['error'] = $c;
						}
					}
					
					$m = $this->model_extension_module_search_engine->getTotalNotIndexed();				
					$o = number_format(($n['start_not_indexed'] - $m) * 100 / $n['start_not_indexed'], 2);										
				}	
				
				$k['progress'] = $o;
				
				if ($o >= 100) {
					$k['success'] = $this->language->get('text_success_index');;					
				} else {
					$k['text'] = sprintf($this->language->get('text_index_progress'), $o);
				}
			}
		} else {
			$k['error'] = $this->error['warning'];
		}		
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($k));
	}
	
	public function stop() {		
		sleep(2);
		unset($this->session->data['indexing_process']);				
	}
	
	public function getTotals() {		
		$this->load->model('extension/module/search_engine');
		
		$k = array();
		
		$k['total_indexed'] = $this->model_extension_module_search_engine->getTotalIndexed();
		$k['total_not_indexed'] = $this->model_extension_module_search_engine->getTotalNotIndexed();
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($k));
	}
	
	public function delete() {
		
		if ($this->validate()) {
			$this->load->model('extension/module/search_engine');
			$this->model_extension_module_search_engine->deleteIndexes();
		}
		
		$this->response->redirect($this->url->link('extension/module/search_engine', 'user_token=' . $this->session->data['user_token'], true));
	}
	
	private function validate() {

		if (!$this->user->hasPermission('modify', 'extension/module/search_engine')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!empty($this->request->post['module_search_engine_options']['new_fields'])) {
			foreach ($this->request->post['module_search_engine_options']['new_fields'] as $p) {
				if ((utf8_strlen(trim($p['field'])) < 1)) {
					$this->error['new_fields'][$p['field']] = $this->language->get('error_field');
					$this->error['warning'] = $this->language->get('error_warning');
				}
			}
		}

		return!$this->error ? true : false;
	}

}

