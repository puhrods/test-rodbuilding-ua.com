<?php
//  Related Options / Связанные опции
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ControllerExtensionModuleRelatedOptions extends Controller {
	private $error = array();
	
	public function __construct() {
		call_user_func_array( array('parent', '__construct') , func_get_args());
		
		\liveopencart\ext\ro::getInstance($this->registry);
	}
	
	private function getLinks() {
		
		$data = array();
		
		$route_home_page 			= 'common/dashboard';
		$route_extensions			= 'marketplace/extension';
		$route_extension_type	= '&type=module';
		$route_module 				= 'extension/module/related_options';
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link($route_home_page, 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => false
		);
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link($route_extensions, 'user_token=' . $this->session->data['user_token'].$route_extension_type, 'SSL'),
			'separator' => ' :: '
		);
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('module_name'),
			'href'      => $this->url->link($route_module, 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => ' :: '
		);
		
		$data['action'] = $this->url->link($route_module, 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['action_export'] = $this->url->link($route_module.'/export', '&user_token=' . $this->session->data['user_token'], 'SSL');
	
		$data['cancel'] = $this->url->link($route_extensions, 'user_token=' . $this->session->data['user_token'].$route_extension_type, 'SSL');
		
		$data['redirect'] = $this->url->link($route_module, 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		return $data;
	}
	
  public function index() {
		
    $mod_language = $this->load->language('extension/module/related_options');
		
		$links = $this->getLinks();

		$this->document->setTitle($this->language->get('module_name'));
		
		$this->load->model('setting/setting');
    $this->load->model('extension/module/related_options');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      
      if (isset($this->request->post['variants'])) {
        $this->model_extension_module_related_options->setVariantsOfRelatedOptions($this->request->post['variants']);
        unset($this->request->post['variants']);
      } else {
				$this->model_extension_module_related_options->setVariantsOfRelatedOptions(array());
			}
      
			$this->model_setting_setting->editSetting('related_options', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($links['redirect']);
      
		}
    
		$data['breadcrumbs'] 			= $links['breadcrumbs'];
		$data['action'] 					= $links['action'];
		$data['action_export'] 		= $links['action_export'];
		$data['cancel'] 					= $links['cancel'];
		
		$data['user_token'] 			= $this->session->data['user_token'];
		
		$data['ocmod_is_applied'] = $this->model_extension_module_related_options->ocmodIsApplied();
		$data['version_pro']			= $this->liveopencart_ext_ro->versionPRO();
		
		$PHPExcelPath = $this->model_extension_module_related_options->PHPExcelPath();
		$data['PHPExcelPath'] = str_replace(DIR_SYSTEM,"./system",$PHPExcelPath);
		$data['PHPExcelExists'] = file_exists($PHPExcelPath);
		
		
    
    if (!empty($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
    
    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    } 
		
		$data['modules'] = array();
    if (isset($this->request->post['related_options'])) {
			$data['modules'] = $this->request->post['related_options'];
		} elseif ($this->config->get('related_options')) {
			$data['modules'] = $this->config->get('related_options');
		}
		
		$data['fields'] = $this->getFields($mod_language);
		$data['additional_fields'] = $this->getAdditionalFields($mod_language);
		
		$data['module_version'] = $this->liveopencart_ext_ro->getCurrentVersion();

		$data['config_admin_language'] = $this->config->get('config_admin_language');
		
		$data['extension_code'] = $this->liveopencart_ext_ro->getExtensionCode();
    
		$data['export_new_action'] 					= $this->url->link('extension/module/related_options/export_new', '&user_token=' . $this->session->data['user_token'], 'SSL');
		$data['export_new_fields'] 					= $this->model_extension_module_related_options->getExportNewFields();
		$related_options_export = $this->model_setting_setting->getSetting('related_options_export');
		if ( !empty($related_options_export['related_options_export']) ) {
			$data['export_new_settings'] 			= $related_options_export['related_options_export'];
		}
		$data['export_new_PHPExcelExists'] 	= $this->model_extension_module_related_options->PHPExcelExists();
    
		$data['min_product_id'] 						= $this->model_extension_module_related_options->getMinProductIdWithRO();
		$data['max_product_id'] 						= $this->model_extension_module_related_options->getMaxProductIdWithRO();
		
    $data['options'] 										= $this->model_extension_module_related_options->getCompatibleOptions();
    $data['variants_options'] 					= $this->model_extension_module_related_options->getVariants();
    
		
		$data['header'] 			= $this->load->controller('common/header');
		$data['column_left'] 	= $this->load->controller('common/column_left');
		$data['footer'] 			= $this->load->controller('common/footer');
				
		$this->response->setOutput($this->load->view('extension/module/related_options', $data));
    
  }
	
	public function productForm($data) {
		
		$data['max_input_vars']										= (int)ini_get('max_input_vars');
		$data['warning_max_input_vars']     			= sprintf($this->language->get('warning_max_input_vars'), $data['max_input_vars']);
		$data['max_number_of_combinations']				= 100000;
		$data['confirm_number_of_combinations']		= 2000;
		$data['related_options_title']						= $this->language->get('module_name');
		
		$this->load->model('extension/module/related_options');
		
		
		$data['ro_installed'] 										= $this->liveopencart_ext_ro->installed();
		if ( $data['ro_installed'] ) {
			$data['variants_options'] 							= $this->model_extension_module_related_options->getVariants(true, true);
			$data['ro_all_options'] 								= $this->model_extension_module_related_options->getCompatibleOptionValues();
			
			$ro_settings = $this->config->get('related_options');
			$data['ro_version_pro']        					=	$this->liveopencart_ext_ro->versionPRO();
			if ( !$data['ro_version_pro'] ) {
				unset($ro_settings['pagination']);
			}
			
			$data['ro_settings']										= $ro_settings;
			$data['ro_version']               			=	$this->liveopencart_ext_ro->getCurrentVersion();
			
			
			
			$data['ro_texts'] 											=	$this->getTextsProductEditPage($data);
			
			$ro_product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0; 
		
			if (isset($this->request->post['ro_data'])) {
				$data['ro_data'] = $this->request->post['ro_data'];
			} elseif ( $ro_product_id ) {
				$data['ro_data'] = $this->model_extension_module_related_options->getROData($ro_product_id);
			} else {
				$data['ro_data'] = array();
			}
			
			$this->addScriptsProductForm();
		}
		
		return $data;
	}
	
	public function getTextsProductEditPage($data) {
		
		$text_keys = array(
			'related_options_title',
			'text_ro_set_options_variants',
			'entry_ro_use',
			'text_ro_all_options',
			'entry_add_all_variants',
			'entry_add_product_variants',
			'entry_delete_all_combs',
			'entry_options_values',
			'entry_model',
			'entry_sku',
			'entry_upc',
			'entry_ean',
			'entry_location',
			'entry_stock_status',
			'entry_weight',
			'entry_price',
			'tab_discount',
			'tab_special',
			'entry_select_first_short',
			'entry_customer_group',
			'entry_quantity',
			'entry_add_related_options',
			'entry_add_discount',
			'entry_del_discount_title',
			'entry_add_special',
			'entry_del_special_title',
			'entry_priority',
			'text_combs_will_be_added',
			'warning_max_input_vars',
			'max_input_vars',
			'entry_select_first_priority',
			'button_remove',
			'warning_equal_options',
			'text_delete_all_combs',
			'max_number_of_combinations',
			'confirm_number_of_combinations',
			'text_combs_number',
			'text_combs_number_out_of_limit',
			'text_combs_number_is_big',
			'entry_ro_variant',
			'entry_related_options_quantity',
			'text_yes',
			'text_no',
			'entry_copy_comb_button_help_title',
		);
		
		$texts = array();
		foreach ($text_keys as $text_key) {
			if ( isset($data[$text_key]) ) {
				$texts[$text_key] = $data[$text_key];
			} else {
				$texts[$text_key] = $this->language->get($text_key);
			}
		}
		return $texts;
	}
	
	protected function addScriptsProductForm() {
		
		$script = 'view/javascript/liveopencart/related_options/ro_product_edit_page.js';
		$script_pro = 'view/javascript/liveopencart/related_options/ro_product_edit_page_pro.js';
		
		
		$this->document->addScript( $this->liveopencart_ext_ro->getResourceLinkWithVersion($script) );
		if ( $this->liveopencart_ext_ro->versionPRO() ) {
			$this->document->addScript( $this->liveopencart_ext_ro->getResourceLinkWithVersion($script_pro) );
		}
	}
	
	private function prepareField($mod_language, $name, $values=false, $parent='', $with_delimiters=false) {
		
		$title 	= isset($mod_language['entry_ro_'.$name]) ? $mod_language['entry_ro_'.$name] : $mod_language['entry_'.$name] ;
		$help 	= isset($mod_language['entry_ro_'.$name.'_help']) ? $mod_language['entry_ro_'.$name.'_help'] : $mod_language['entry_'.$name.'_help'];
		
		$delimiters = array();
		$delimiter_keys = array('_delimiter_product', '_delimiter_ro');
		if ( $with_delimiters ) {
			foreach ( $delimiter_keys as $delimiter_key ) {
				$delimiter_name = $name.$delimiter_key;
				$delimiters[] = array('name'=>$delimiter_name, 'title'=> (isset($mod_language['entry_ro_'.$delimiter_name]) ? $mod_language['entry_ro_'.$delimiter_name] : $mod_language['entry_'.$delimiter_name]) );
			}
		}
		
		return array(	'name'				=> $name,
									'title'				=> $title,
									'help' 				=> $help,
									'parent'			=> $parent,
									'values'			=> $values,
									'delimiters'	=> $delimiters,
								);
	}
	
	private function getAdditionalFields($mod_language) {
		$fields = array();
		
		foreach ($mod_language as $lang_key => $lang_val) {
			$$lang_key = $lang_val;
		}
		
		$values = array(
			0 => $entry_spec_model_0,
			1 => $entry_spec_model_1,
			2 => $entry_spec_model_2,
			3 => $entry_spec_model_3,
		);
		$fields[] = $this->prepareField($mod_language, 'spec_model', $values, '', true);
		$fields[] = $this->prepareField($mod_language, 'spec_sku');
		$fields[] = $this->prepareField($mod_language, 'spec_upc');
		$fields[] = $this->prepareField($mod_language, 'spec_ean');
		$fields[] = $this->prepareField($mod_language, 'spec_location');
		$fields[] = $this->prepareField($mod_language, 'spec_ofs');
		$fields[] = $this->prepareField($mod_language, 'spec_weight');
		$fields[] = $this->prepareField($mod_language, 'spec_price_prefix');
		$fields[] = $this->prepareField($mod_language, 'spec_price');
		$fields[] = $this->prepareField($mod_language, 'spec_price_discount');
		$fields[] = $this->prepareField($mod_language, 'spec_price_special');
		
		return $fields;
	}
	
	private function getFields($mod_language) {
		$fields = array();
		
		foreach ($mod_language as $lang_key => $lang_val) {
			$$lang_key = $lang_val;
		}
		
		$fields[] = $this->prepareField($mod_language, 'update_quantity');
		
		$fields[] = $this->prepareField($mod_language, 'update_options');
		$fields[] = $this->prepareField($mod_language, 'update_options_remove', false, 'update_options');
		
		$values = array(0 => $text_subtract_stock_from_product,
										1 => $text_subtract_stock_from_product_first_time,
										2 => $text_yes,
										3 => $text_no,
										);
		$fields[] = $this->prepareField($mod_language, 'subtract_stock', $values, 'update_options');
		
		$values = array(0 => $text_yes,
										1 => $text_no,
										2 => $text_required_first_time,
										);
		$fields[] = $this->prepareField($mod_language, 'required', $values, 'update_options');
		if ( $this->liveopencart_ext_ro->versionPRO() ) {
			$fields[] = $this->prepareField($mod_language, 'pagination');
		}
		$fields[] = $this->prepareField($mod_language, 'copy_comb_button');
		
		$fields[] = $this->prepareField($mod_language, 'allow_zero_select');
		$fields[] = $this->prepareField($mod_language, 'stock_control');
		
		
		$values = array(0 => $option_show_clear_options_not,
										1 => $option_show_clear_options_top,
										2 => $option_show_clear_options_bot,
										);
		$fields[] = $this->prepareField($mod_language, 'show_clear_options', $values);
		$fields[] = $this->prepareField($mod_language, 'hide_inaccessible');
		$fields[] = $this->prepareField($mod_language, 'hide_option');
		$fields[] = $this->prepareField($mod_language, 'unavailable_not_required');
		
		$values = array(0 => $option_select_first_not,
										1 => $option_select_first,
										2 => $option_select_first_last,
										3 => $option_select_first_always,
										);
		$fields[] = $this->prepareField($mod_language, 'select_first', $values);
		$fields[] = $this->prepareField($mod_language, 'step_by_step');
		
		$fields[] = $this->prepareField($mod_language, 'disable_all_options_variant');
		$fields[] = $this->prepareField($mod_language, 'ro_use_variants');
                
		
		return $fields;
	}
	
	
	public function export_new() {
		
		if ( $this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['export_fields'])
			&& is_array($this->request->post['export_fields']) && count($this->request->post['export_fields'])>0 ) {
			
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('related_options_export', array('related_options_export'=>$this->request->post) );	
			
			$this->load->model('extension/module/related_options');
			$this->model_extension_module_related_options->makeExport();
			exit;
		}
		
	}
	
	public function import_new() {
		
		$this->load->model('extension/module/related_options');
		$json = $this->model_extension_module_related_options->makeImport();
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
  
	public function export() { // to remove (old)
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['export']) && is_array($this->request->post['export']) && count($this->request->post['export'])>0) {
			
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('related_options_export', $this->request->post['export']);
			$export_settings = $this->request->post['export'];
			
			
			$this->load->model('extension/module/related_options');
			$data = $this->model_extension_module_related_options->getExportData();
			
			require_once $this->model_extension_module_related_options->PHPExcelPath();
			PHPExcel_Shared_File::setUseUploadTempDirectory(true);
			
			$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM; //PHPExcel_CachedObjectStorageFactory::cache_to_discISAM ; //
			$cacheSettings = array( 'memoryCacheSize' => '32MB');
			if (!PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings)) {
				$this->log->write("Related options, PHPExcel cache error");
			}
			
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			
			$show_settings = array();
			foreach ($data as $data_str) {
				
				foreach ($data_str as $data_key => $data_value) {
					foreach ($export_settings as $setting => $setting_on) {
						if ($setting_on) {
							if (($data_key == $setting) || (substr($data_key, 0, strlen($setting)) == $setting) ) {
								if (!in_array($data_key, $show_settings)) {
									$show_settings[] = $data_key;
								}
							}
						}
					}
				}
			}
			
			
			$column = 0;
			foreach ($show_settings as $setting) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $setting);
				$column++;
			}
			
			$current_data = array();
			$line_cnt = 2;
			$loop_cnt = 0;
			foreach ($data as &$data_str) {
				$loop_cnt++;
				
				$current_str = array();
				foreach ($show_settings as $setting) {
					$current_str[$setting] = isset($data_str[$setting]) ? $data_str[$setting] : null;
				}
				$current_data[] = $current_str;
				$data_str = ""; // memory opt
				
				if ($loop_cnt%1000 == 0) {
					$objPHPExcel->getActiveSheet()->fromArray($current_data, null, 'A'.$line_cnt);
					$line_cnt=2+$loop_cnt;
					$current_data = array();
					//sleep(1);
				}
					
			}
			unset($data);

			if (count($current_data)>0) {
				$objPHPExcel->getActiveSheet()->fromArray($current_data, null, 'A'.$line_cnt);
			}
			
			
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			
			
			$file = DIR_CACHE."/roexport.xls";
			
			$objWriter->save($file);
			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			// read file and send to user
			readfile($file);
			exit;
			
		}
	}
  
	public function import() { // to remove (old)
		
		$this->load->language('extension/module/related_options');
		$this->load->model('extension/module/related_options');
		
		$json = array();
		
		if (!empty($this->request->files['file']['name']) && $this->request->files['file']['tmp_name'] ) {
			
			require_once $this->model_extension_module_related_options->PHPExcelPath();
			
			$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp; //PHPExcel_CachedObjectStorageFactory::cache_to_discISAM ; //
			$cacheSettings = array( 'memoryCacheSize' => '32MB');
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			
			$excel = PHPExcel_IOFactory::load($this->request->files['file']['tmp_name']); // PHPExcel
			$sheet = $excel->getSheet(0);
			
			$data = $sheet->toArray();
			
			
			if (count($data) > 1) {
				
				$head = array_flip($data[0]);
				
				if (!isset($head['product_id'])) {
					$json['error'] = "product_id not found";
				}
				
				if (!isset($head['quantity'])) {
					$json['error'] = "quantity not found";
				}
				
				if (!isset($head['option_id1'])) {
					$json['error'] = "option_id1 not found";
				}
				
				if (!isset($head['option_value_id1'])) {
					$json['error'] = "option_value_id1 not found";
				}
				
				if (!isset($json['error'])) {
					
					$f_options = array();
					for ($i=1;$i<=100;$i++) {
						if ( isset($head['option_id'.$i]) && isset($head['option_value_id'.$i]) ) {
							$f_options[] = $i;
						}
					}
					
					
					$products = array();
					
					for ($i=1;$i<count($data);$i++) {
						
						$row = $data[$i];
						
						$product_id = (int)$row[$head['product_id']];
						if (!isset($products[$product_id])) {
							$products[$product_id] = array('relatedoptions'=>array(), 'related_options_use'=>true, 'related_options_variant_search'=>true);
						}
						
						$options = array();
						foreach ($f_options as $opt_num) {
							if ((int)$row[$head['option_id'.$opt_num]]!=0) {
								$options[(int)$row[$head['option_id'.$opt_num]]] = (int)$row[$head['option_value_id'.$opt_num]];
							}
						}
						
						$products[$product_id]['relatedoptions'][] = array(	'options'=>$options
																															, 'quantity'=>$row[(int)$head['quantity']]
																															, 'price_prefix'=> isset($head['price_prefix']) ? (string)$row[(int)$head['price_prefix']] : ''
																															, 'price'=> isset($head['price']) ? (float)$row[(int)$head['price']] : 0
																															, 'model'=> isset($head['relatedoptions_model']) ? $row[(int)$head['relatedoptions_model']] : ''
																															, 'sku'=> isset($head['relatedoptions_sku']) ? $row[(int)$head['relatedoptions_sku']] : ''
																															, 'upc'=> isset($head['relatedoptions_upc']) ? $row[(int)$head['relatedoptions_upc']] : ''
																															, 'ean'=> isset($head['relatedoptions_ean']) ? $row[(int)$head['relatedoptions_ean']] : ''
																															, 'stock_status_id'=> isset($head['stock_status_id']) ? $row[(int)$head['stock_status_id']] : ''
																															, 'weight_prefix'=> isset($head['weight_prefix']) ? $row[(int)$head['weight_prefix']] : ''
																															, 'weight'=> isset($head['weight']) ? $row[(int)$head['weight']] : ''
																															);
						
						
					}
					
					
					$this->load->model('extension/module/related_options');
					
					if (isset($this->request->post['import_delete_before']) && $this->request->post['import_delete_before'] == 1) {
						$this->model_extension_module_related_options->removeRelatedOptions();
					}
					
					$ro_cnt = 0;
					foreach ($products as $product_id => $product) {
						$ro_cnt+= count($product['relatedoptions']);
						
						if (isset($this->request->post['import_delete_before']) && $this->request->post['import_delete_before'] == 2) {
							$this->model_extension_module_related_options->removeRelatedOptions($product_id);
						}
						
						
						$ro_data = $this->model_extension_module_related_options->getROData($product_id);
						
						$new_ro_combs = array();
						
						
						foreach ($product['relatedoptions'] as $new_ro) {
							
							$new_options_ids = array();
							foreach ($new_ro['options'] as $option_id => $option_value_id) {
								$new_options_ids[] = $option_id;
							}
							
							$ro_found = false;
							foreach ($ro_data as &$ro_dt) {
								
								// combination set is relevant, let's find current new combination in this set
								if ( !array_diff_assoc($new_options_ids, $ro_dt['options_ids']) && count($new_ro['options']) == count($ro_dt['options_ids']) ) {
									
									foreach ($ro_dt['ro'] as &$ro_comb) {
										if ( !array_diff_assoc($new_ro['options'], $ro_comb['options']) && count($new_ro['options']) == count($ro_comb['options'])) {
											// refresh relevant combination field accordingly to new combination
											foreach ($ro_comb as $ro_comb_key => &$ro_comb_value) {
												if (isset($new_ro[$ro_comb_key])) {
													$ro_comb_value = $new_ro[$ro_comb_key];
												}
											}
											unset($ro_comb_value);
											$ro_found = true;
											break;
										}
									}
									unset($ro_comb);
									
									// relevant combination is not found, but combination set is relevant, let's add this combination to this set
									if (!$ro_found) {
										$ro_dt['ro'][] = $new_ro;
										$ro_found = true;
									}
								}
							}
							unset($ro_dt);
							if (!$ro_found) { // if there's not relevant set of options combinations, let's add new set
								
								$new_ro_combs_set = array(	'rovp_id' => ''
																					,	'use' 		=> true
																					,	'related_options_variant_search' 	=> true
																					, 'ro'			=> array($new_ro)
																					, 'options_ids'	=> $new_options_ids
																					);
								
								$ro_data[] = $new_ro_combs_set;
								
							}
						}
						
						$product_data = array('ro_data_included'=>true, 'ro_data'=>$ro_data);
						$this->model_extension_module_related_options->setROData($product_id, $product_data);	
						
					}
					$json['products'] = count($products);
					$json['relatedoptions'] = $ro_cnt;
					
					$json['success'] = $this->language->get('entry_import_ok');
					
				}
				
			} else {
				$json['error'] = "empty table";
			}
			
		} else {
			$json['error'] = "file not uploaded";
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function install() {
    $this->load->model('extension/module/related_options');
    $this->model_extension_module_related_options->install();
		
		$this->load->model('setting/setting');
		$msettings = array('related_options' => array('update_quantity'=>1,
																									'update_options'=>1,
																									'ro_use_variants'=>1,
																									'disable_all_options_variant'=>1,
																									'related_options_version'=>$this->liveopencart_ext_ro->getCurrentVersion(),
																									));
		$this->model_setting_setting->editSetting('related_options', $msettings);
		
		$this->model_setting_setting->editSetting('module_related_options', array('module_related_options_status'=>1)); // status = enabled
  }
  
  public function uninstall() {
    $this->load->model('extension/module/related_options');
    $this->model_extension_module_related_options->uninstall();
  }
  
  private function validate() {
    if ( !$this->user->hasPermission('modify', 'extension/module/related_options')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }
    
    if (!$this->error) {
      return true;
    } else {
      return false;
    }	
  }
  
}