<?php
class ControllerExtensionModuleClientTranslateExpert extends Controller {
	private $error = array();

	public function getCurrentVersion()
	{
		return '02.06.1000';
	}

	public function index() {
		$this->load->language('extension/module/client_translate_expert');

		$this->document->setTitle($this->language->get('heading_title'));

		// $this->load->model('extension/module');
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_client_translate_expert', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['entry_server_key'] = $this->language->get('entry_server_key');
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_analization'] = $this->language->get('entry_analization');
		$data['entity_begin_analization'] = $this->language->get('entity_begin_analization');
		$data['entry_analization_result'] = $this->language->get('entry_analization_result');

		$data['entry_info'] = $this->language->get('entry_info');
		$data['entry_char_quantity'] = $this->language->get('entry_char_quantity');
		$data['entry_login'] = $this->language->get('entry_login');
		$data['entry_analization_mode'] = $this->language->get('entry_analization_mode');

		$data['entry_stop_modal_process'] = $this->language->get('entry_stop_modal_process');
		$data['entry_close'] = $this->language->get('entry_close');
		$data['entry_stop_modal_process_confirmation'] = $this->language->get('entry_stop_modal_process_confirmation');
		$data['entry_try_translate'] = $this->language->get('entry_try_translate');
		$data['entry_try_translate_text'] = $this->language->get('entry_try_translate_text');
		$data['entry_translate_expert_version'] = $this->language->get('entry_translate_expert_version');
		$data['entry_php_version'] = $this->language->get('entry_php_version');
		$data['entry_opencart_version'] = $this->language->get('entry_opencart_version');
		$data['entry_license_info'] = $this->language->get('entry_license_info');
		$data['entry_debug_status'] = $this->language->get('entry_debug_status');
		$data['entry_table'] = $this->language->get('entry_table');
		$data['entry_analization_language_from'] = $this->language->get('entry_analization_language_from');
		$data['entry_analization_language_to'] = $this->language->get('entry_analization_language_to');
		$data['entry_all_tables'] = $this->language->get('entry_all_tables');
		$data['entry_table_deep'] = $this->language->get('entry_table_deep');
		$data['entry_table_deep_description'] = $this->language->get('entry_table_deep_description');
		
		$data['entry_localization_language_from'] = $this->language->get('entry_localization_language_from');
		$data['entry_localization_language_to'] = $this->language->get('entry_localization_language_to');
		$data['entry_localization_analization'] = $this->language->get('entry_localization_analization');
		$data['entry_localization_analization_result'] = $this->language->get('entry_localization_analization_result');
		
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_login_is_correct'] = $this->language->get('text_login_is_correct');
		$data['text_need_to_login'] = $this->language->get('text_need_to_login');
		$data['text_no_need_to_pay'] = $this->language->get('text_no_need_to_pay');
		$data['text_need_to_pay'] = $this->language->get('text_need_to_pay');
		$data['text_about_page'] = $this->language->get('text_about_page');
		$data['text_current_module_version'] = $this->language->get('text_current_module_version');
		$data['text_newest_module_version'] = $this->language->get('text_newest_module_version');
		$data['text_you_need_to_update_module'] = $this->language->get('text_you_need_to_update_module');
		$data['text_you_have_the_newest_version_of_module'] = $this->language->get('text_you_have_the_newest_version_of_module');
		$data['text_debug_log'] = $this->language->get('text_debug_log');
		$data['text_clear_confirm'] = $this->language->get('text_clear_confirm');

		$data['js_const_done_status'] = $this->language->get('js_const_done_status');
		$data['js_const_in_progress_status'] = $this->language->get('js_const_in_progress_status');
		$data['js_const_statistic_html'] = $this->language->get('js_const_statistic_html');
		$data['js_const_analization_process_is_started'] = $this->language->get('js_const_analization_process_is_started');

		$data['value_analization_mode_only_empty'] = $this->language->get('value_analization_mode_only_empty');
		$data['value_analization_mode_same_value'] = $this->language->get('value_analization_mode_same_value');
		$data['value_analization_mode_both'] = $this->language->get('value_analization_mode_both');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_translate_site'] = $this->language->get('tab_translate_site');
		$data['tab_localization'] = $this->language->get('tab_localization');
		$data['tab_debug'] = $this->language->get('tab_debug');
		$data['tab_about'] = $this->language->get('tab_about');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_download'] = $this->language->get('button_download');
		$data['button_clear'] = $this->language->get('button_clear');
		$data['button_view'] = $this->language->get('button_view');

		$data['entry_table_product_filter'] = $this->language->get('entry_table_product_filter');
		$data['entry_table_product_status_filter'] = $this->language->get('entry_table_product_status_filter');
		$data['entry_table_product_quantity_filter'] = $this->language->get('entry_table_product_quantity_filter');
		$data['entry_table_product_category_id_filter'] = $this->language->get('entry_table_product_category_id_filter');
		$data['entry_table_product_stock_status_filter'] = $this->language->get('entry_table_product_stock_status_filter');
		$data['entry_filter_all'] = $this->language->get('entry_filter_all');

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

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		foreach($data['languages'] as $code => $language)
		{
			if (version_compare(VERSION, '2.2', '<'))
				$langUrl = 'view/image/flags/' . $language['image'];
			else
				$langUrl = "language/{$code}/{$code}.png";
			$data['languages'][$code]['image_url'] = $langUrl;
		}


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/client_translate_expert', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/client_translate_expert', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		$data['action'] = $this->url->link('extension/module/client_translate_expert', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_client_translate_expert_key'])) {
			$data['module_client_translate_expert_key'] = $this->request->post['module_client_translate_expert_key'];
		} else {
			$data['module_client_translate_expert_key'] = $this->config->get('module_client_translate_expert_key');
		}

		if (isset($this->request->post['module_client_translate_expert_status'])) {
			$data['module_client_translate_expert_status'] = $this->request->post['module_client_translate_expert_status'];
		} else {
			$data['module_client_translate_expert_status'] = $this->config->get('module_client_translate_expert_status');
		}
		
		if (isset($this->request->post['module_client_translate_expert_debug_status'])) {
			$data['module_client_translate_expert_debug_status'] = $this->request->post['module_client_translate_expert_debug_status'];
		} else {
			$data['module_client_translate_expert_debug_status'] = $this->config->get('module_client_translate_expert_debug_status');
		}

		$data['te_langIdFrom'] = isset($this->session->data['te_langIdFrom']) ? $this->session->data['te_langIdFrom'] : null;
		$data['te_langIdTo'] = isset($this->session->data['te_langIdTo']) ? $this->session->data['te_langIdTo'] : null;
		$data['te_mode'] = isset($this->session->data['te_mode']) ? $this->session->data['te_mode'] : null;
		$data['te_table'] = isset($this->session->data['te_table']) ? $this->session->data['te_table'] : null;
		$data['te_product_status'] = isset($this->session->data['te_product_status']) ? $this->session->data['te_product_status'] : null;
		$data['te_product_quantity'] = isset($this->session->data['te_product_quantity']) ? $this->session->data['te_product_quantity'] : null;
		$data['te_product_category_id'] = isset($this->session->data['te_product_category_id']) ? $this->session->data['te_product_category_id'] : null;
		$data['te_stock_status'] = isset($this->session->data['te_stock_status']) ? $this->session->data['te_stock_status'] : null;
		
		$data['translate_expert_version'] = $this->getCurrentVersion();
		$data['php_version'] = phpversion();
		$data['opencart_version'] = VERSION;
		$data['tab_general_class'] = 'active';
		$data['tab_debug_class'] = '';

		$this->load->model('localisation/stock_status');
		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
		
		$this->load->model('catalog/category');
		$filter_data = array(
			'sort'        => 'name',
			'order'       => 'ASC'
		);
		$data['categories'] = $this->model_catalog_category->getCategories($filter_data);

		$this->load->model('extension/module/client_translate_expert');
		$siteInfo = $this->model_extension_module_client_translate_expert->validateTranslateSiteInfo();
		$data['license_info'] = $siteInfo;
		if (!$siteInfo->licensed)
		{
			if ($siteInfo->active)
				$data['error_unlicensed'] = $this->language->get('error_unlicensed_active');
			else
				$data['error_unlicensed'] = $this->language->get('error_unlicensed');
		}

		$data['download_href'] = $this->url->link('extension/module/client_translate_expert/downloadDebugLog', 'user_token=' . $this->session->data['user_token'], true);
		$data['clear_href'] = $this->url->link('extension/module/client_translate_expert/clearDebugLog', 'user_token=' . $this->session->data['user_token'], true);
		$data['view_href'] = $this->url->link('extension/module/client_translate_expert', 'view=1&user_token=' . $this->session->data['user_token'], true);		
		
		if (isset($this->request->get)
			&& isset($this->request->get['view'])
			&& $this->request->get['view'] == '1')
		{
			$data['tab_general_class'] = '';
			$data['tab_debug_class'] = 'active';			
			$data['debug_log'] = '';
			$file = DIR_LOGS . 'client_translate_expert.log';
			if (file_exists($file))
			{
				$size = filesize($file);
				if ($size >= 5242880)
				{
					$suffix = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
					$i = 0;
					while (($size / 1024) > 1)
					{
						$size = $size / 1024;
						$i++;
					}
					$data['error_debug_log_warning_frm'] = sprintf($this->language->get('error_debug_log_warning'), basename($file), round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]);
				} else {
					$data['debug_log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
				}
			}
			else
			{
				$data['debug_log'] = 'File is empty!';
			}
		}
		
		$data['tables'] = $this->getTables();

		if (version_compare(VERSION, '2.0', '<'))
		{
			$this->children = array(
				'common/header',
				'common/footer'
			);
	
			$this->template = 'module/client_translate_expert.tpl';
			$this->response->setOutput($this->render());
		}
		else
		{
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->config->set('template_engine', 'template'); $this->response->setOutput($this->load->view('extension/module/client_translate_expert', $data));
		}
	}

	public function translate() {
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$text = isset($this->request->post['text']) ? html_entity_decode($this->request->post['text']) : '';
			$textArr = isset($this->request->post['textArr']) ? html_entity_decode($this->request->post['textArr']) : '';
			$source = isset($this->request->post['source']) ? $this->request->post['source'] : '';
			$target = isset($this->request->post['target']) ? $this->request->post['target'] : 'en';
			$format = isset($this->request->post['format']) ? $this->request->post['format'] : 'html';
		}
		else {
			$text = isset($this->request->get['text']) ? html_entity_decode($this->request->get['text']) : '';
			$textArr = isset($this->request->get['textArr']) ? html_entity_decode($this->request->get['textArr']) : '';
			$source = isset($this->request->get['source']) ? $this->request->get['source'] : '';
			$target = isset($this->request->get['target']) ? $this->request->get['target'] : 'en';
			$format = isset($this->request->get['format']) ? $this->request->get['format'] : 'html';
		}
		
		if ($textArr)
		{
			$textArr = json_decode($textArr);	
			foreach($textArr as &$textEl)
				$textEl = html_entity_decode($textEl);
			$text = $textArr;
		}
		
		$this->load->model('localisation/language');
		if (is_numeric($source))
		{
			$source = $this->model_localisation_language->getLanguage($source)['code'];
		}		
		if (is_numeric($target))
		{
			$target = $this->model_localisation_language->getLanguage($target)['code'];
		}

		$this->load->model('extension/module/client_translate_expert');
		$result = $this->model_extension_module_client_translate_expert->translate($text, $source, $target, $format);

		$this->response->addHeader('Content-Type: application/json; Charset=UTF-8');		
		$this->response->setOutput(json_encode($result));
	}

	public function translateTable() {
		$langIdFrom = isset($this->request->post['langIdFrom']) ? html_entity_decode($this->request->post['langIdFrom']) : null;
		$mode = isset($this->request->post['mode']) ? html_entity_decode($this->request->post['mode']) : null;
		$tableName = isset($this->request->post['tableName']) ? html_entity_decode($this->request->post['tableName']) : null;
		$textColumnName = isset($this->request->post['textColumnName']) ? html_entity_decode($this->request->post['textColumnName']) : null;
		$langIdTo = isset($this->request->post['langIdTo']) ? html_entity_decode($this->request->post['langIdTo']) : null;
		$product_status = isset($this->request->post['product_status']) ? boolval(html_entity_decode($this->request->post['product_status'])) : false;
		$product_quantity = isset($this->request->post['product_quantity']) ? boolval(html_entity_decode($this->request->post['product_quantity'])) : false;
		$product_category_id = isset($this->request->post['product_category_id']) ? intval(html_entity_decode($this->request->post['product_category_id'])) : null;
		$stock_status = isset($this->request->post['stock_status']) ? intval(html_entity_decode($this->request->post['stock_status'])) : null;

		$this->load->model('extension/module/client_translate_expert');
		$result = $this->model_extension_module_client_translate_expert->translateTable($langIdFrom, $mode, $tableName, $textColumnName, $langIdTo, $product_status, $product_quantity, $product_category_id, $stock_status);

		$this->response->addHeader('Content-Type: application/json; Charset=UTF-8');
		$this->response->setOutput(json_encode($result));
	}

	public function analizeTableDetail() {
		$langIdFrom = isset($this->request->post['langIdFrom']) ? html_entity_decode($this->request->post['langIdFrom']) : null;
		$mode = isset($this->request->post['mode']) ? html_entity_decode($this->request->post['mode']) : null;
		$tableName = isset($this->request->post['tableName']) ? html_entity_decode($this->request->post['tableName']) : null;
		$textColumnName = isset($this->request->post['textColumnName']) ? html_entity_decode($this->request->post['textColumnName']) : null;
		$langIdTo = isset($this->request->post['langIdTo']) ? html_entity_decode($this->request->post['langIdTo']) : null;
		$product_status = isset($this->request->post['product_status']) ? boolval(html_entity_decode($this->request->post['product_status'])) : false;
		$product_quantity = isset($this->request->post['product_quantity']) ? boolval(html_entity_decode($this->request->post['product_quantity'])) : false;
		$product_category_id = isset($this->request->post['product_category_id']) ? intval(html_entity_decode($this->request->post['product_category_id'])) : null;
		$stock_status = isset($this->request->post['stock_status']) ? intval(html_entity_decode($this->request->post['stock_status'])) : null;

		$this->load->model('extension/module/client_translate_expert');
		$result = $this->model_extension_module_client_translate_expert->analizeTableDetail($langIdFrom, $mode, $tableName, $textColumnName, $langIdTo, $product_status, $product_quantity, $product_category_id, $stock_status);

		$this->response->addHeader('Content-Type: application/json; Charset=UTF-8');
		$this->response->setOutput(json_encode($result));
	}
	
	public function analize() {
		$langIdFrom = isset($this->request->get['langIdFrom']) ? html_entity_decode($this->request->get['langIdFrom']) : null;
		$langIdTo = isset($this->request->get['langIdTo']) ? html_entity_decode($this->request->get['langIdTo']) : null;
		$mode = isset($this->request->get['mode']) ? html_entity_decode($this->request->get['mode']) : null;
		$table = isset($this->request->get['table']) ? html_entity_decode($this->request->get['table']) : null;
		$product_status = isset($this->request->get['product_status']) ? boolval(html_entity_decode($this->request->get['product_status'])) : false;
		$product_quantity = isset($this->request->get['product_quantity']) ? boolval(html_entity_decode($this->request->get['product_quantity'])) : false;
		$product_category_id = isset($this->request->get['product_category_id']) ? intval(html_entity_decode($this->request->get['product_category_id'])) : null;
		$stock_status = isset($this->request->get['stock_status']) ? intval(html_entity_decode($this->request->get['stock_status'])) : null;

		$continueAfterTable = isset($this->request->get['continue_after_table']) ? html_entity_decode($this->request->get['continue_after_table']) : null;
		$continueAfterColumn = isset($this->request->get['continue_after_column']) ? html_entity_decode($this->request->get['continue_after_column']) : null;
		$continueOffset = isset($this->request->get['continue_offset']) ? intval(html_entity_decode($this->request->get['continue_offset'])) : 0;

		$this->session->data['te_langIdFrom'] = $langIdFrom;
		$this->session->data['te_langIdTo'] = $langIdTo;
		$this->session->data['te_mode'] = $mode;
		$this->session->data['te_table'] = $table;

		$this->session->data['te_product_status'] = $product_status;
		$this->session->data['te_product_quantity'] = $product_quantity;
		$this->session->data['te_product_category_id'] = $product_category_id;
		$this->session->data['te_stock_status'] = $stock_status;
		
		$this->load->model('extension/module/client_translate_expert');
		$result = $this->model_extension_module_client_translate_expert->analize($langIdFrom, $langIdTo, $mode, $table, $product_status, $product_quantity, $product_category_id, $stock_status,
			$continueAfterTable, $continueAfterColumn, $continueOffset);
		
		$this->response->addHeader('Content-Type: application/json; Charset=UTF-8');
		$this->response->setOutput(json_encode($result));
	}
	
	public function analizeLocalization() {
		$langIdFrom = isset($this->request->get['langIdFrom']) ? html_entity_decode($this->request->get['langIdFrom']) : null;
		$langIdTo = isset($this->request->get['langIdTo']) ? html_entity_decode($this->request->get['langIdTo']) : null;
		$nextFileIndex = isset($this->request->get['nextFileIndex']) ? html_entity_decode($this->request->get['nextFileIndex']) : 0;
		
		$this->session->data['te_langIdFrom'] = $langIdFrom;
		$this->session->data['te_langIdTo'] = $langIdTo;

		$this->load->model('extension/module/client_translate_expert');
		$result = $this->model_extension_module_client_translate_expert->analizeLocalization($langIdFrom, $langIdTo, $nextFileIndex);
		
		$result = $this->convert_from_latin1_to_utf8_recursively($result);
		
		$this->response->addHeader('Content-Type: application/json; Charset=UTF-8');
		$this->response->setOutput(json_encode($result));
	}
	
	private function is_utf8($str) {
		$strlen = strlen($str);
		for ($i = 0; $i < $strlen; $i++) {
			$ord = ord($str[$i]);
			if ($ord < 0x80) continue; // 0bbbbbbb
			elseif (($ord & 0xE0) === 0xC0 && $ord > 0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
			elseif (($ord & 0xF0) === 0xE0) $n = 2; // 1110bbbb
			elseif (($ord & 0xF8) === 0xF0 && $ord < 0xF5) $n = 3; // 11110bbb (exkl F5-FF)
			else return false; // invalid UTF-8-Zeichen
			for ($c=0; $c<$n; $c++) // $n following bytes? // 10bbbbbb
				if (++$i === $strlen || (ord($str[$i]) & 0xC0) !== 0x80)
					return false; // invalid UTF-8 char
		}
		return true; // didn't find any invalid characters
	}
	
	private function convert_from_latin1_to_utf8_recursively($dat)
	{
		if (is_string($dat)) {
			return $this->is_utf8($dat) ? $dat : utf8_encode($dat);
		} elseif (is_array($dat)) {
			$ret = [];
			foreach ($dat as $i => $d)
				$ret[ $i ] = $this->convert_from_latin1_to_utf8_recursively($d);
			return $ret;
		} elseif (is_object($dat)) {
			foreach ($dat as $i => $d)
				$dat->$i = $this->convert_from_latin1_to_utf8_recursively($d);
			return $dat;
		} else {
			return $dat;
		}
	}	
	
	public function saveLocalizationFile() 
	{
		$fileContent = isset($this->request->post['fileContent']) ? html_entity_decode($this->request->post['fileContent']) : null;
		$langId = isset($this->request->post['langId']) ? html_entity_decode($this->request->post['langId']) : null;
		$path = isset($this->request->post['path']) ? html_entity_decode($this->request->post['path']) : null;

		$this->load->model('extension/module/client_translate_expert');
		$result = $this->model_extension_module_client_translate_expert->saveLocalizationFile($fileContent, $langId, $path);
		
		$this->response->addHeader('Content-Type: application/json; Charset=UTF-8');
		$this->response->setOutput(json_encode($result));
	}
	
	public function downloadDebugLog() {
		$this->load->language('extension/module/client_translate_expert');

		$file = DIR_LOGS . 'client_translate_expert.log';

		if (file_exists($file) && filesize($file) > 0) {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename="' . $this->config->get('config_name') . '_' . date('Y-m-d_H-i-s', time()) . '_error.log"');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->response->setOutput(file_get_contents($file, FILE_USE_INCLUDE_PATH, null));
		} else {
			$this->session->data['error'] = sprintf($this->language->get('error_debug_log_warning'), basename($file), '0B');

			$this->response->redirect($this->url->link('extension/module/client_translate_expert', 'view=1&user_token=' . $this->session->data['user_token'], true));
		}
	}
	
	public function clearDebugLog() {
		$this->load->language('extension/module/client_translate_expert');

		if (!$this->user->hasPermission('modify', 'extension/module/client_translate_expert')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = DIR_LOGS . 'client_translate_expert.log';

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] = $this->language->get('text_clear_debug_log_success');
		}

		$this->response->redirect($this->url->link('extension/module/client_translate_expert', 'view=1&user_token=' . $this->session->data['user_token'], true));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/client_translate_expert')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	protected function getInfo() {
		$this->load->model('extension/module/client_translate_expert');
		$result = $this->model_extension_module_client_translate_expert->getInfo();

		return $result;
	}
	
	protected function getTables() {
		$this->load->model('extension/module/client_translate_expert');
		$result = $this->model_extension_module_client_translate_expert->getTables();

		return $result;
	}
	
	public function install()
	{
		$this->load->model('extension/module/client_translate_expert');
		$result = $this->model_extension_module_client_translate_expert->install();
	}
	
	public function uninstall()
	{
		$this->load->model('extension/module/client_translate_expert');
		$result = $this->model_extension_module_client_translate_expert->uninstall();
	}
}








