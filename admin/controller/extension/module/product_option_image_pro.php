<?php
//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ControllerExtensionModuleProductOptionImagePro extends liveopencart\lib\v0015\ControllerAdminExtension {
	
	protected $lang;
	
	protected $extension_code='product_option_image_pro'; // for paths and urls
	
	protected $event_prefix = 'liveopencart.poip.';
	protected $events = array();
	protected $xlsx_lib;
	
	public function __construct() {
		
		call_user_func_array( array('parent', '__construct') , func_get_args());
		
		liveopencart\ext\poip::getInstance($this->registry); // library also loads the model (accessible standard way from registry or by $this->liveopencart_ext_poip->getModel  )
		
	}
	
	public function index() {
		
		$this->updateEvents();
		
		$this->loadLanguage();
		$links = $this->getLinks();

		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('poip_module', $this->request->post);		
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($links['redirect']);
		}
				
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} 
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$data['breadcrumbs'] 	= $links['breadcrumbs'];
		$data['action'] 		= $links['action'];
		$data['cancel'] 		= $links['cancel'];
		
		$data['action_export'] 	= $this->getLinkWithToken( $this->getRouteExtension('', 'export'), '&type='.$this->extension_type);
		
		$data['fields'] 		= $this->liveopencart_ext_poip->getModel()->getModuleSettingsDetails();
		
		
		if ( $this->getXLXSLib()->getAvailability() ) {
			$data['import_export_is_possible'] = true;
		} else {
			$data['xlsx_lib_error'] = true;
			$data['xlsx_lib_name'] = $this->getXLXSLib()->getName();
			$data['lib_install_available'] = $this->user->hasPermission('modify', 'extension/module/product_option_image_pro');
		}

		$data['modules'] = array();
		
		if (isset($this->request->post['poip_module'])) {
			$data['modules'] = $this->request->post['poip_module'];
		} elseif ($this->config->get('poip_module')) {
			$data['modules'] = $this->config->get('poip_module');
		}
		
		$data['min_product_id'] = $this->liveopencart_ext_poip->getModel()->getMinProductId();
		$data['max_product_id'] = $this->liveopencart_ext_poip->getModel()->getMaxProductId();
		
		$data['extension_code'] 		= $this->liveopencart_ext_poip->getExtensionCode();
		$data['module_version'] 		= $this->liveopencart_ext_poip->getCurrentVersion();
		$data['config_admin_language']	= $this->config->get('config_admin_language');
		
			
		$this->document->setTitle($this->language->get('module_name'));
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
				
		$this->response->setOutput($this->load->view('extension/module/product_option_image_pro/product_option_image_pro', $data));
		
	}
	
	protected function getXLXSLib($force_using_php_excel=false) {
		
		if ( $force_using_php_excel && (!$this->xlsx_lib || $this->xlsx_lib->getName() != 'PHPExcel' ) ) {
			$this->xlsx_lib = $this->getNewLibInstance('vendors\php_excel', $this->registry);
		}
		
		if ( !$this->xlsx_lib ) {
			$box_spout = $this->getNewLibInstance('vendors\box_spout', $this->registry);
			
			if ( $box_spout->getPossibility() && !$force_using_php_excel ) {
				$this->xlsx_lib = $box_spout;
			} else{
				$this->xlsx_lib = $this->getNewLibInstance('vendors\php_excel', $this->registry);
			}
		}
		return $this->xlsx_lib;
	}
	
	public function installXLSXLib() {
		
		$json = array();
		
		$this->loadLanguage();
		
		if ( !$this->user->hasPermission('modify', 'extension/module/product_option_image_pro')) {
			
			$json['error'] = $this->language->get('error_permission');
			
		} else {
			
			$result = $this->getXLXSLib()->install();
			
			if ( $result ) {
				$json['error'] = $result;
			}
			
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	
	public function import() {
		
		$this->loadLanguage();

		
		$json = array();
		
		if ( !$this->user->hasPermission('modify', 'extension/module/product_option_image_pro') ) {
			$json['error'] = $this->language->get('error_permission');
		}
		
		
		if ( empty($json['error']) && !empty($this->request->files['file']['name'])) {
			
			$real_file_name = $this->request->files['file']['tmp_name'];
			
			$force_php_excel = strtolower(substr($real_file_name, -4)) == '.xls';
			
			if ( $this->getXLXSLib($force_php_excel)->getAvailability() ) {
				$data = $this->getXLXSLib()->getSheetDataFromFile($real_file_name, 0);
			} else{
				$json['error'] = sprintf($this->language->get('error_xlsx_lib_is_not_found'), $this->getXLXSLib()->getName());
				if ( $force_php_excel ) {
					$json['error'].= ' '.$this->language->get('error_php_excel_is_necessary_for_xls');
				}
			}
			
			if ( !isset($json['error']) ) {
				if (count($data) > 1) {
					
					if (isset($this->request->post['import_delete_before']) && $this->request->post['import_delete_before'] == 1) {
						$this->liveopencart_ext_poip->getModel()->deleteAllImages();
					}
					
					foreach ($data[0] as $head_key => &$head_val) {
						$head_val = trim($head_val);
					}
					unset($head_val);
					$head = array_flip($data[0]);
					
					
					if (!isset($head['product_id'])) {
						$json['error'] = "product_id not found";
					}
					
					if (!isset($head['image'])) {
						$json['error'] = "image not found";
					}
					
					if (!isset($head['option_value_id'])) {
						$json['error'] = "option_value_id not found";
					}
					
					if (!isset($json['error'])) {
						
						$images = 0;
						$json['added'] = array();
						$json['skipped'] = array();
						$json['not_found'] = array();
						$json['already_exist'] = array();
						$json['skipped'] = array();
						
						$poip_settings = $this->liveopencart_ext_poip->getModel()->getGlobalSettings();
						
						// collect images by products
						$file_products_images = array();
						for ($i=1;$i<count($data);$i++) {
							$row = $data[$i];
							$product_id 		= (int)$row[$head['product_id']];
							$image 				= trim((string)$row[$head['image']]);
							if ( $product_id && $image ) {
								
								$option_value_id 	= (int)$row[$head['option_value_id']];
								
								if ( empty($file_products_images[$product_id]) ) {
									$file_products_images[$product_id] = array();
								}
								if ( empty($poip_settings['options_images_edit']) ) { // images to options
									if ( empty($file_products_images[$product_id]) ) {
										$file_products_images[$product_id][$option_value_id] = array();
									}
									$file_products_images[$product_id][$option_value_id][] = $image;
								} else { // options to images
									$file_products_images[$product_id][] = $image;
								}
								
							}
						}
						
						
						for ($i=1;$i<count($data);$i++) {
							
							$row = $data[$i];
							
							$product_id 		= (int)$row[$head['product_id']];
							$image 				= (string)$row[$head['image']];
							
							if ( $product_id && $image ) {
							//if (trim((string)$row[$head['image']]) != "") {
								
								$option_value_id 	= (int)$row[$head['option_value_id']];
								if ( isset($head['sort_order']) ) {
									$sort_order 	= (int)$row[$head['sort_order']];
								} else {
									if ( empty($poip_settings['options_images_edit']) ) { // images to options
										$sort_order = array_search($image, $file_products_images[$product_id][$option_value_id])+1;
									} else { // options to images
										$sort_order = array_search($image, $file_products_images[$product_id])+1;
									}
								}
								
								$ro_id 				= isset($head['relatedoptions_id']) ? (int)$row[$head['relatedoptions_id']] : 0;
								
								$result = $this->liveopencart_ext_poip->getModel()->addProductOptionValueImage($product_id, $option_value_id, $image, $sort_order, $ro_id);
								if ( $result === 1 ) { // added
									$json['added'][] = $i;
								} elseif ( $result === 0 ) {
									$json['not_found'][] = $i;
								} elseif ( $result === -1 ) {
									$json['already_exist'][] = $i;
								} else {
									$json['skipped'][] = $i;
								}
							
							} else {
								$json['no_image'][] = $i;
							}
							
						}
						
						if ( !empty($poip_settings['options_images_edit']) ) { // options to images
							// change sort order of additional product images if they are linked with options
							// use max sort order of additional images not linked with options as the base
							$this->liveopencart_ext_poip->getModel()->resortProductsImagesByProductOptionImages(array_keys($file_products_images));
						}
						
						$json['rows'] = count($data)-1;
						
						$this->liveopencart_ext_poip->getModel()->deleteOptionImagesOfInexistentProductOptionValues();
					}
					
				} else {
					$json['error'] = "empty table";
				}
				
			}
			
		} else {
			$json['error'] = "file not uploaded";
		}
		
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function export() {
		
		$this->loadLanguage();
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
		
			ini_set('display_errors', 1);
			error_reporting(E_ERROR|E_PARSE);
			
			$include_options_without_images = isset($this->request->post['include_options_without_images']) ? $this->request->post['include_options_without_images'] : false;
			$include_names = isset($this->request->post['include_names']) ? $this->request->post['include_names'] : false;
			$first_product_id = isset($this->request->post['export_first_product_id']) ? (int)$this->request->post['export_first_product_id'] : 0;
			$last_product_id = isset($this->request->post['export_last_product_id']) ? (int)$this->request->post['export_last_product_id'] : 0;
			$data = $this->liveopencart_ext_poip->getModel()->getAllImages($include_options_without_images, $include_names, $first_product_id, $last_product_id);
			
			$first_row_data = array();
		
			$first_row_data[] = 'product_id';
			if ( $this->liveopencart_ext_poip->versionUltimate() && $this->liveopencart_ext_poip->getSetting('images_for_ro') ) {
				$first_row_data[] = 'relatedoptions_id';
			}
			$first_row_data[] = 'option_value_id';
			$first_row_data[] = 'image';
			$first_row_data[] = 'sort_order';
			
			if ( $include_names ) {
				$first_row_data[] = 'product_name';
				$first_row_data[] = 'option_name';
				$first_row_data[] = 'option_value_name';
			}
			
			array_unshift($data, $first_row_data);
			
			//$export_file_name = 'poip_export.xlsx';
			//$this->exportToBrowserWithPHPExcel($data, $export_file_name);
			
			//$export_file_name = 'poip_export.xlsx';
			
			$this->getXLXSLib()->exportSheetsDataToBrowser(array($data), 'poip_export.xlsx');

			
			exit;
		}
	}
	
	public function getOptionPageData($data) {
		
		$this->loadLanguage();

		
		$data['poip_installed'] 				= $this->liveopencart_ext_poip->installed();
		if ( $data['poip_installed'] ) {
			$data['poip_settings_details'] 		= $this->liveopencart_ext_poip->getModel()->getModuleSettingsDetails(true);
			$data['poip_module_name'] 			= $this->language->get('poip_module_name');
			$data['poip_saved_settings'] 		= $this->liveopencart_ext_poip->getModel()->getRealOptionSettings( isset($this->request->get['option_id']) ? $this->request->get['option_id'] : 0 );
			
			$data['poip_settings_enable_disable_options'] = array('0'=>$this->language->get('entry_settings_default'), '2'=>$this->language->get('entry_settings_yes'), '1'=>$this->language->get('entry_settings_no'));
			
			$data['poip_option_settings']		= $this->load->view('extension/module/product_option_image_pro/option_settings', $data);
		}
		return $data;
	}
	
	public function getProductPageData($data) {
		
		$this->load->language('catalog/product');
		$this->loadLanguage();

		
		$this->load->model('catalog/product');
		
		$product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
		
		$data['poip_installed'] = $this->liveopencart_ext_poip->installed();
		$data['poip_version_ultimate'] = $this->liveopencart_ext_poip->versionUltimate();
		$data['poip_global_settings'] = $this->liveopencart_ext_poip->getModel()->getGlobalSettings();
		$data['poip_settings_details'] = $this->liveopencart_ext_poip->getModel()->getModuleSettingsDetails(true);
		$data['poip_module_name'] = $this->language->get('poip_module_name');
		$data['poip_saved_settings'] = $this->liveopencart_ext_poip->getModel()->getRealProductSettings( $product_id );
		$data['poip_product_options'] = $this->liveopencart_ext_poip->getModel()->getProductOptions($product_id);
		$data['poip_option_values'] = $this->liveopencart_ext_poip->getModel()->getOptionValuesByArrayOfProductOptions($data['poip_product_options']);
		
		$data['poip_settings_enable_disable_options'] = array('0'=>$this->language->get('entry_settings_default'), '2'=>$this->language->get('entry_settings_yes'), '1'=>$this->language->get('entry_settings_no'));
		
		$data['poip_texts']['entry_image'] 			= $this->language->get('entry_image');
		$data['poip_texts']['entry_no_value'] 		= $this->language->get('entry_no_value');
		$data['poip_texts']['entry_no_value_help'] 	= $this->language->get('entry_no_value_help');
		$data['poip_texts']['entry_any_value'] 		= $this->language->get('entry_any_value');
		$data['poip_texts']['entry_any_value_help'] = $this->language->get('entry_any_value_help');
		$data['poip_texts']['poip_module_name'] 	= $this->language->get('poip_module_name');
		$data['poip_texts']['entry_sort_order'] 	= $this->language->get('entry_sort_order');
		$data['poip_texts']['button_remove'] 		= $this->language->get('button_remove');
		$data['poip_texts']['entry_show_settings'] 	= $this->language->get('entry_show_settings');
		$data['poip_texts']['entry_hide_settings'] 	= $this->language->get('entry_hide_settings');
		$data['poip_texts']['entry_hide_settings'] 	= $this->language->get('entry_hide_settings');
		$data['poip_texts']['button_select_images'] = $this->language->get('button_select_images');
		$data['poip_texts']['button_add_images'] 	= $this->language->get('button_add_images');
		$data['poip_texts']['button_add_image'] 	= $this->language->get('button_image_add');
		

		return $data;
	}
	
	public function addProductPageResources() {
		
		if ( $this->liveopencart_ext_poip->installed() ) {
			$this->document->addScript( $this->liveopencart_ext_poip->getResourceLinkPathWithVersion('view/javascript/liveopencart/product_option_image_pro/poip_product_edit_page.js') );
			$this->document->addStyle( 	$this->liveopencart_ext_poip->getResourceLinkPathWithVersion('view/stylesheet/liveopencart/product_option_image_pro/poip_product_edit_page.css') );
			if ( $this->liveopencart_ext_poip->versionUltimate() ) {
				$this->document->addScript( $this->liveopencart_ext_poip->getResourceLinkPathWithVersion('view/javascript/liveopencart/product_option_image_ultimate/jquery-ui.sortable.min.js') );
				$this->document->addScript( $this->liveopencart_ext_poip->getResourceLinkPathWithVersion('view/javascript/liveopencart/product_option_image_ultimate/poiu_product_edit_page.js') );
			}
		}
	}
	
	public function install() {
		$this->liveopencart_ext_poip->getModel()->install();
		
		$this->model_setting_setting->editSetting('module_product_option_image_pro', array('module_product_option_image_pro_status'=>1)); // status = enabled
		
		$this->updateEvents();
	}
  
	public function uninstall() {
		$this->liveopencart_ext_poip->getModel()->uninstall();
		$this->removeEvents();
	}
	
	protected function validate() {
		
		$this->loadLanguage();

		
		if ( !$this->user->hasPermission('modify', 'extension/module/product_option_image_pro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
