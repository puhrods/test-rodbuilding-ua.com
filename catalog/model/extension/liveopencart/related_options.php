<?php
//  Related Options / Связанные опции 
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ModelExtensionLiveopencartRelatedOptions extends Model {

	public $cache_sets_by_poids = array();
	public $cache_ro_data = array(); 
	private $liveprice_settings = false;
	private $module_installed_status = null;
	private $module_installed_status_liveprice = null;
	
	public function __construct() {
		call_user_func_array( array('parent', '__construct'), func_get_args());
		
	}
	
	public function getThemeName() {
		if (!$this->theme_name) {
			$theme_name = '';
			
			if ($this->config->get('config_theme') == 'theme_default' || $this->config->get('config_theme') == 'default') {
				$theme_name = $this->config->get('theme_default_directory');
			} else {
				$theme_name = substr($this->config->get('config_theme'), 0, 6) == 'theme_' ? substr($this->config->get('config_theme'), 6) : $this->config->get('config_theme') ;
			}
			
			// shorten theme name
			$themes_shorten = $this->getAdaptedThemes();
			foreach ( $themes_shorten as $theme_shorten ) {
				$theme_shorten_length = strlen($theme_shorten);
				if ( substr($theme_name, 0, $theme_shorten_length) == $theme_shorten ) {
					$theme_name = substr($theme_name, 0, $theme_shorten_length);
					break;
				}
			}
			$this->theme_name = $theme_name;
		}
		return $this->theme_name;
  }
	
	protected function getAdaptedThemes() {
		
		$dir_of_themes = $this->getBasicDirOfTemplates();
		
		$themes = glob($dir_of_themes . '*' , GLOB_ONLYDIR);
		if ( $themes ) {
			$themes = array_map( 'basename', $themes );
			
			if ( ($default_key = array_search('default', $themes)) !== false ) {
				unset($themes[$default_key]);
			}
			
			usort($themes, function($a, $b) {
				return strlen($b) - strlen($a);
			});
			return $themes;
		} else {
			return array();
		}
		
	}
	
	public function getBasicDirOfExtension() {
		return DIR_TEMPLATE.'extension_liveopencart/related_options/';
	}
	public function getBasicDirOfTemplates() {
		return $this->getBasicDirOfExtension().'themes/';
	}
	
	public function getBasicScripts() {
		
		$scripts = array();
		
		$scripts[] = $this->getScriptPathWithVersion('view/theme/extension_liveopencart/related_options/js/liveopencart.select_option_toggle.js');
		$scripts[] = $this->getScriptPathWithVersion('view/theme/extension_liveopencart/related_options/js/liveopencart.related_options.js');
		
		return $scripts;
	}
	
	public function getScriptCommon() {
		return $this->getScriptPathWithVersion('view/theme/extension_liveopencart/related_options/js/product_page_common.js');
	}
	public function getScriptProductPage() {
		return $this->getScriptPathWithVersion('view/theme/extension_liveopencart/related_options/js/product_page_with_related_options.js');
	}
	public function getScriptProductPageTheme() {
		$script_path = 'view/theme/extension_liveopencart/related_options/themes/'.$this->getThemeName().'/code.js';
		if ( file_exists(DIR_APPLICATION.$script_path) ) {
			return $this->getScriptPathWithVersion($script_path);
		}
	}
	
	private function getScriptPathWithVersion($path) {
		$basic_dir = 'catalog/';
		return $basic_dir.$path.'?v='.filemtime(DIR_APPLICATION.$path);
	}
	
	public function clearCaches() {
		$this->cache_sets_by_poids = array();
		$this->cache_ro_data = array();
	}
	public function getCacheROData() {
		return $this->cache_ro_data;
	}
	
	public function getProductControllerData($data, $return_all_scripts=false, $basic_data=array()) {
		$this->load->language('extension/liveopencart/related_options');
		
		$data['ro_installed']								= $this->installed();
		
		if ( $data['ro_installed'] ) {
		
			// $this->request->get['product'] - fnt_product_design;
			if ( isset($data['ro_product_id']) ) {
				$ro_product_id = $data['ro_product_id'];
			} elseif ( isset($data['product_id']) ) {
				$ro_product_id = $data['product_id'];	
			} elseif ( isset($this->request->get['pid']) ) {
				$ro_product_id = $this->request->get['pid'];
			} elseif ( isset($this->request->get['product_id']) ) {
				$ro_product_id = $this->request->get['product_id'];
			} elseif ( isset($this->request->post['product_id']) ) {
				$ro_product_id = $this->request->post['product_id'];
			} elseif ( isset($this->request->get['product_id']) ) {
				$ro_product_id = $this->request->get['product_id'];		
			} else {
				$ro_product_id = $this->request->get['product'];
			}
			
			$data['ro_installed']								= $this->installed();
			$data['ro_settings']								= $this->config->get('related_options');
			$data['ro_product_id']							= $ro_product_id;
			$data['ro_theme_name']							= $this->getThemeName();
			$data['ro_data'] 										= $this->get_ro_data($ro_product_id, true);
			if ( !empty($this->request->get['filter_name']) ) {
				$data['ro_filter_name'] = $this->request->get['filter_name'];
			}
			if ( !empty($this->request->get['search']) ) {
				$data['ro_search'] = $this->request->get['search'];
			}
			
			if ( $return_all_scripts ) { // add the basic module scripts too
				$data['ro_scripts'] = $this->getBasicScripts();
			}
			
			// the common part and the part for option reset
			if ( !empty($data['ro_data']) || !empty($data['ro_settings']['show_clear_options']) ) {
				$data['ro_scripts'][] = $this->getScriptCommon();
			}
		
			// the part when the product has related options
			//if ( !empty($data['ro_data']) ) {
				$data['ro_scripts'][] = $this->getScriptProductPage();
				$theme_script = $this->getScriptProductPageTheme();
				if ( $theme_script ) {
					$data['ro_scripts'][] = $theme_script;
				}
			//}
			
			$this->load->model('catalog/product');
			$ro_product = $this->model_catalog_product->getProduct($ro_product_id);
			$data['ro_product_model'] = empty($ro_product['model']) ? '' : $ro_product['model'];

			if ( !empty($this->request->get['roid']) && $this->productHasRelatedOptionsId($ro_product_id, $this->request->get['roid']) ) {
				$ro_id = (int)$this->request->get['roid'];
				$data['ros_to_select'] = array( $ro_id );
				$default_product_data = $this->liveopencart_ext_ro->getProductInfoForROId($ro_product, $ro_id, $data['ro_data'] );
				if ( isset($default_product_data['model']) ) {
					$data['ro_default_product_model'] = $default_product_data['model'];
				}
				
			} else {
				$data['ros_to_select'] = $this->getROCombSelectedByDefault($ro_product_id, isset($this->request->get['search']) ? $this->request->get['search'] : '');
			}
			
			$data['ro_product_page_script'] = $this->render( 'extension_liveopencart/related_options/tpl/product_page_script', array_merge($basic_data, $data) );

		}
		return $data;
	}
	
	//public function getProductInfoForROId($product, $ro_id, $ro_data=false, $ro_comb=false) {
	//	
	//	$data = array();
	//	
	//	if ( $product && $ro_id ) {
	//		if ( $ro_comb === false ) {
	//			if ( $ro_data === false ) {
	//				$ro_data = $this->get_ro_data($product['product_id'], true);
	//			}
	//			$ro_comb = $this->getROCombFromRODataByROId($ro_data, $ro_id);
	//		}
	//		if ( $ro_comb ) {
	//			$ro_custom_fields = $this->getCustomFields($product, array($ro_comb));
	//			if ( isset($ro_custom_fields['codes']['model']) ) {
	//				$data['model'] = $ro_custom_fields['codes']['model'];
	//			}
	//			
	//			if ( $this->installedLivePrice() ) { // allows to take standard product option prices into account
	//				
	//				$this->load->model('extension/liveopencart/liveprice');
	//				$lp_data = $this->model_extension_liveopencart_liveprice->getProductPriceByParamsArray( array('product_id' 				=> $product['product_id'],
	//																																																			'quantity'					=> 1,
	//																																																			'options'						=> $ro_comb['options'],
	//																																																			'use_ro_data_cache'	=> true, // always use caches
	//																																																			'use_cart_cache'		=> true,
	//																																																		 ) );
	//				//$lp_data = $this->model_extension_liveopencart_liveprice->getProductPrice( $product['product_id'], 1, $ro_comb['options']);
	//				if ( $lp_data ) {
	//					$data['price'] = $lp_data['prices']['price_old_opt'];
	//					if ( $lp_data['prices']['special'] ) {
	//						$data['special'] = $lp_data['prices']['special_opt'];
	//					}
	//				}
	//			} else { // allows to take only prices of related options into account
	//			
	//				$ro_price_data = $this->calcProductPriceWithRO($product['price'], array($ro_comb), $product['special']);
	//				if ($ro_price_data) {
	//					$data['price'] = $ro_price_data['price'];
	//					$data['special'] = $ro_price_data['special'];
	//				}
	//			}
	//		}
	//	}
	//	return $data;
	//}
	
	private function productHasRelatedOptionsId($product_id, $ro_id) {
		$query = $this->db->query("
			SELECT *
			FROM `".DB_PREFIX."relatedoptions`
			WHERE `product_id` = ".(int)$product_id."
				AND `relatedoptions_id` = ".(int)$ro_id."
		");
		return $query->num_rows;
	}
	
	private function render($route, $data) {
		
		$ReflectionMethod =  new \ReflectionMethod('Template', '__construct');
    $params = array();
		foreach ($ReflectionMethod->getParameters() as $param_reflection ) {
			$params[] = $param_reflection->getName();
		}
		
		if ( !empty($params[1]) && $params[1] == 'registry' ) { // $this->registry is added for compatibility with d_twig_manager.xml
			$template = new Template($this->registry->get('config')->get('template_engine'), $this->registry);
		} else { // std
			$template = new Template($this->registry->get('config')->get('template_engine'));
		}
		
		// $this->registry is added for compatibility with d_twig_manager.xml
		//$template = new Template($this->registry->get('config')->get('template_engine'), $this->registry);
		
		foreach ($this->language->all() as $key => $value) {
			$template->set($key, $value);
		}
		
		foreach ($data as $key => $value) {
			$template->set($key, $value);
		}
		
		$classMethod = new ReflectionMethod($template,'render');
		if ( count($classMethod->getParameters()) > 2 )  { // for some mods ($route, $registry, $cache=false)
			$output = $template->render( $route, $this->registry );
		} else { // std
			$output = $template->render( $route );
		}
		//$output = $template->render( $route, $this->registry ); // $this->registry for compatibility with the file replaced by fastor theme
		
		return $output;
	}
	
	public function getRODataForProductList($product_id) {
		
		if ( $this->installed() && ( $this->getThemeName() == 'themeXXX' || $this->getThemeName() == 'theme725' ) ) {
			return $this->get_ro_data($product_id, true);
		}
		
	}
	
	public function getROCombSelectedByDefault($product_id, $search_request='') {
		$ro_settings = $this->config->get('related_options');
		$ros_to_select = false;
		if ( $search_request && !empty($ro_settings['spec_model']) ) {
			$ros_to_select = $this->getRelatedOptionsIdsFromSearch($product_id, $search_request);
		} elseif ( isset($ro_settings['select_first']) && $ro_settings['select_first'] == 1 ) {
			$ros_to_select = $this->getRelatedOptionsIdsAutoSelectFirst($product_id);
		}
		return $ros_to_select;
	}
	
	// << orders editing 
	public function getOrderOptions($order_id, $order_product_id) {
		
		// comp with old code
		\liveopencart\ext\ro::getInstance($this->registry)->getOrderOptions($order_id, $order_product_id);
	}
	
	// return order quantity back to product quantity (on order delete)
	public function update_ro_quantity($product_id, $order_id, $order_product_id, $quantity, $sign='+') {
		
		// comp with old code
		\liveopencart\ext\ro::getInstance($this->registry)->updateROQuantity($product_id, $order_id, $order_product_id, $quantity, $sign);
		
	}
	// >> orders editing
	
	

	// returns only switched-on additional fields (sku, upc, location)
	public function getAdditionalFields($include_model=false) {
		
		$fields = array();
		
		if ($this->installed()) {
			$ro_settings = $this->config->get('related_options');
			$std_fields = array('sku', 'upc', 'ean', 'location');
			if ( $include_model ) {
				array_unshift($std_fields, 'model');
			}
			foreach ($std_fields as $field) {
				if ( isset($ro_settings['spec_'.$field]) && $ro_settings['spec_'.$field] ) {
					$fields[] = $field;
				}
			}
		}
		
		return $fields;
	}
	
	public function getCustomFields($product_info, $ro_combs) {
		
		// comp for old code
		return \liveopencart\ext\ro::getInstance($this->registry)->getCustomFields($product_info, $ro_combs);
		
	}
	
	public function updateOrderProductAdditionalFields($product, $order_product_id) {
		
		// comp for old code
		return \liveopencart\ext\ro::getInstance($this->registry)->updateOrderProductAdditionalFields($product, $order_product_id);
		
	}
	
	
	
	public function getRelatedOptionsIdsAutoSelectFirst($product_id) {
		
		$ro_ids = array();
		$ro_data = $this->get_ro_data($product_id);
		
		$existing_options = array();
		foreach ($ro_data as $ro_dt) {
			
			$ro_combs = array();
			if ( $existing_options ) { // filter combinations by option values from previous combinations
				
				foreach ( $ro_dt['ro'] as $ro ) {
					$all_values_equal = true;
					foreach ($ro['options_original'] as $option_id => $option_value_id) {
						if ( isset($existing_options[$option_id]) && $existing_options[$option_id] != $option_value_id ) {
							$all_values_equal = false;
							break;
						}
					}
					if ( $all_values_equal ) {
						$ro_combs[] = $ro;
					}
				}
				
			} else {
				$ro_combs = $ro_dt['ro'];
			}
			
			$ro_default = array();
			
			foreach ( $ro_combs as $ro ) {
				if ($ro['defaultselect']) {
					$ro_default[] = $ro;
				}
			}
			
			$ro_comb = false;
			if ( count($ro_default) == 0 ) {
				$ro_default = $ro_combs;
			}
			
			foreach ($ro_default as $ro) {
				if ($ro_comb === false || $ro_comb['defaultselectpriority'] > $ro['defaultselectpriority']) {
					$ro_comb = $ro;
				}
			}
			
			if ($ro_comb) {
				$ro_ids[] = $ro_comb['relatedoptions_id'];
				foreach ( $ro_comb['options_original'] as $option_id => $option_value_id ) {
					$existing_options[$option_id] = $option_value_id;
				}
			}
		}
		
		return $ro_ids;
	}
	
	public function getRelatedOptionsIdsFromSearch($product_id, $search_string) {
		
		$ro_settings = $this->config->get('related_options');
		
		if ( isset($ro_settings['spec_model']) ) {
			if ( $ro_settings['spec_model']==2 || $ro_settings['spec_model']==3 ) {
			
				$query = $this->db->query("	SELECT *
																		FROM 	`".DB_PREFIX."relatedoptions_search`
																		WHERE	product_id = ".(int)$product_id."
																			AND LCASE(`model`) = '" . $this->db->escape(utf8_strtolower($search_string)) . "'
																		");
				
				if ($query->num_rows) {
					return explode(',',$query->row['ro_ids']);
				}
				
			} elseif ( $ro_settings['spec_model']==1 ) {
				
				$query = $this->db->query("	SELECT *
																		FROM 	`".DB_PREFIX."relatedoptions`
																		WHERE	product_id = ".(int)$product_id."
																			AND LCASE(`model`) = '" . $this->db->escape(utf8_strtolower($search_string)) . "'
																		");
				
				$ro_ids = array();
				foreach ($query->rows as $row) {
					$ro_ids[] = $row['relatedoptions_id'];
				}
				return $ro_ids;
				
			}
		}
		return false;
	}
	
	//// Live Price PRO has setting to calculate RO combination discounts as addition (+ or -) if RO combination price prefix is + (plus) or - (minus)
	//// in this case RO discounts do not replace standard discounts, just go to ro_price_modificator
	//private function calcProductPriceWithRO_getDiscount($ro_comb, $quantity) {
	//	$discount_price = 0;
	//	if ($quantity !== false && $quantity >= 1 && !empty($ro_comb['discounts'])) {
	//		
	//		$discounts_reversed = array_reverse($ro_comb['discounts']); // change order to from the higher to the lower
	//		foreach ($discounts_reversed as $discount) { 
	//			if ($quantity >= $discount['quantity']) {
	//				
	//				$discount_price = $discount['price'];
	//				
	//				break; // array of discounts is ordered (DESC), thus first found occurrence is enough
	//			}
	//		}
	//	}
	//	return $discount_price;
	//}
	//
	//// without discouts and specials ?
	//public function calcProductPriceWithRO($product_price, $ro_combs, $special=0, $stock=false, $ro_price_modificator=0, $quantity=false) {
	// 		
	//	$ro_settings = $this->config->get('related_options');
	//	$lp_settings = $this->getLivePriceSettings();
	//	
	//	$ro_price = $product_price;
	//	$ro_discount_addition_total = 0;
	//	$ro_special_addition_total = 0;
	//	/*
	//	if ( !empty($ro_settings['spec_price']) ) {
	//		$ro_price = $product_price;
	//	} else {
	//		$ro_price = false;
	//	}
	//	*/
	//	$in_stock = null;
	//	
	//	if ($this->installed()) {
	//		
	//		foreach ($ro_combs as $ro_comb) {
	//			
	//			if ( !empty($ro_settings['spec_price']) ) {
	//				
	//				//if (isset($ro_comb['price']) && $ro_comb['price']!=0) {
	//					// "+" may has effect even without price (by discounts)
	//					if (isset($ro_settings['spec_price_prefix']) && $ro_settings['spec_price_prefix'] && ($ro_comb['price_prefix'] == '+' || $ro_comb['price_prefix'] == '-') ) {
	//						
	//						$ro_price_addition = $ro_comb['price'];
	//						
	//						if ( $ro_comb['price_prefix'] == '-' ) {
	//							$ro_price_addition = -$ro_price_addition;
	//						}
	//						
	//						if (!empty($ro_price_addition)) {
	//							//$ro_price+= $ro_price_addition;
	//							$ro_price_modificator+= $ro_price_addition;
	//						}
	//						
	//						if ( !empty($lp_settings['ropro_discounts_addition']) ) {
	//							if ( $ro_comb['price_prefix'] == '+' ) {
	//								$ro_discount_addition_total+= $this->calcProductPriceWithRO_getDiscount($ro_comb, $quantity);
	//							} else { // -
	//								$ro_discount_addition_total-= $this->calcProductPriceWithRO_getDiscount($ro_comb, $quantity);
	//							}
	//						}
	//						
	//						if ( !empty($lp_settings['ropro_specials_addition']) && $ro_comb['specials'] && $ro_comb['specials'][0] ) {
	//							$ro_special_row = $ro_comb['specials'][0];
	//							if ( $ro_comb['price_prefix'] == '+' ) {
	//								$ro_special_addition_total+= $ro_special_row['price'];
	//							} else { // -
	//								$ro_special_addition_total-= $ro_special_row['price'];
	//							}
	//						}
	//					
	//					} elseif ( !empty($ro_comb['price']) && (float)$ro_comb['price'] ) {
	//						$ro_price = $ro_comb['price'];
	//					}
	//				//}
	//				
	//				if (isset($ro_comb['current_customer_group_special_price']) && $ro_comb['current_customer_group_special_price']) {
	//					$special = $ro_comb['current_customer_group_special_price'];
	//				}
	//			}
	//
	//			if ( !empty($ro_settings['spec_ofs']) ) {
	//				$stock = $ro_comb['stock'];
	//			} elseif ( $this->config->get('config_stock_display') ) {
	//				$stock = (int)$ro_comb['quantity'];
	//			} else {
	//				$stock = false;
	//			}
	//			$in_stock = $ro_comb['in_stock'];
	//		}
	//		$ro_price+= $ro_price_modificator; // apply + and - modifiers at the last step (after = )
	//	}
	//	return array('price'=>$ro_price,
	//							 'special'=>$special,
	//							 'stock'=>$stock,
	//							 'in_stock'=>$in_stock,
	//							 'price_modificator'=>$ro_price_modificator,
	//							 'discount_addition'=>$ro_discount_addition_total,
	//							 'special_addition'=>$ro_special_addition_total,
	//							 );
	//	
	//}
	
	// get price and stock
  public function getJournal2Price($product_id, $price, $special=false) {
		
		if ($this->installed()) {
			
			$this->load->model('catalog/product');
			
			$product_options = $this->model_catalog_product->getProductOptions($product_id);
			$options = array();
			foreach ($product_options as $option) {
				if (!in_array($option['type'], array('select', 'radio', 'image', 'block', 'color'))) continue;
							
				$option_ids = Journal2Utils::getProperty($this->request->post, 'option.' . $option['product_option_id'], array());
				
				if (is_scalar($option_ids)) {
					$options[$option['product_option_id']] = $option_ids;
				} elseif (is_array($option_ids) && count($option_ids) > 0) {
					$options[$option['product_option_id']] = $option_ids[0];
				}
			}
			
			return $this->liveopencart_ext_ro->getProductPriceWithRoByProductOptions($product_id, $options, $price, $special);
			
		}	
			
		//	$ro_settings = $this->config->get('related_options');
		//	if ( $ro_settings && is_array($ro_settings) ) {
		//		
		//		if ( !$this->model_catalog_product ) {
		//			$this->load->model('catalog/product');
		//		}
		//		$product_options = $this->model_catalog_product->getProductOptions($product_id);
		//		$options = array();
		//		foreach ($product_options as $option) {
		//			if (!in_array($option['type'], array('select', 'radio', 'image', 'block', 'color'))) continue;
		//						
		//			$option_ids = Journal2Utils::getProperty($this->request->post, 'option.' . $option['product_option_id'], array());
		//			
		//			if (is_scalar($option_ids)) {
		//				$options[$option['product_option_id']] = $option_ids;
		//			} elseif (is_array($option_ids) && count($option_ids) > 0) {
		//				$options[$option['product_option_id']] = $option_ids[0];
		//			}
		//		}
		//		
		//		if (count($options) > 0 ) {
		//			$ro_combs = $this->getROCombsByPOIds($product_id, $options);
		//			$ro_price_data = $this->calcProductPriceWithRO($price, $ro_combs, $special);
		//			return $ro_price_data;
		//		}
		//	}	
		//}
		return false;
	}
	
	//public function getProductPriceWithRoByProductOptions($product_id, $options, $price, $special=false) {
	//	if ($this->installed()) {
	//		
	//		$ro_settings = $this->config->get('related_options');
	//		if ( $ro_settings && is_array($ro_settings) && count($options) ) {
	//			
	//			$ro_combs = $this->getROCombsByPOIds($product_id, $options);
	//			return $this->calcProductPriceWithRO($price, $ro_combs, $special);
	//		}	
	//	}
	//	return false;
	//}
	
	// get price and stock
  public function getJournalPrice($product_id, $price, $special=false) {
		
		if ($this->installed()) {
			
			$ro_settings = $this->config->get('related_options');
			if ( $ro_settings && is_array($ro_settings) ) {
				
				if ( !$this->model_catalog_product ) {
					$this->load->model('catalog/product');
				}
				$product_options = $this->model_catalog_product->getProductOptions($product_id);
				$options = array();
				foreach ($product_options as $option) {
					if (!in_array($option['type'], array('select', 'radio', 'image', 'block', 'color'))) continue;
								
					$option_ids = isset($this->request->post['option'][$option['product_option_id']]) ? $this->request->post['option'][$option['product_option_id']] : [];
					
					if (is_scalar($option_ids)) {
						$options[$option['product_option_id']] = $option_ids;
					} elseif (is_array($option_ids) && count($option_ids) > 0) {
						$options[$option['product_option_id']] = $option_ids[0];
					}
				}
				
				return $this->liveopencart_ext_ro->getProductPriceWithRoByProductOptions($product_id, $options, $price, $special);
				
				//if (count($options) > 0 ) {
				//	$ro_combs = $this->getROCombsByPOIds($product_id, $options);
				//	$ro_price_data = $this->calcProductPriceWithRO($price, $ro_combs, $special);
				//	return $ro_price_data;
				//}
			}	
		}
		return false;
	}
	
	
	// check is there enough product quantity for related options (for all products in cart)
	public function cart_ckeckout_stock($products) {
		
		if ($this->installed()) {
			if (is_array($products)) {
				foreach ($products as &$product) {
					if ($product['stock']) {
						if (isset($product['option'])&&is_array($product['option'])) {
							$poids = array();
							foreach ($product['option'] as $option) {
								if ($option) {
									$poids[$option['product_option_id']] = $option['product_option_value_id'];
								}
							}
							if (count($poids) > 0) {
								$product['stock'] = $this->cart_stock($product['product_id'], $poids, $product['quantity']);
							}
						}
					}
				}
				unset($product);
			}
		}
		return $products;
		
	}
	
	private function getROCombsWithQuantitiesInCartByProductId($p_product_id) {
		
		$qtys = array();
		
		$products = $this->cart->getProducts();
		foreach ($products as $product) {
			if ($product['product_id'] == $p_product_id) {
				$cart_options = array();
				foreach ($product['option'] as $option) {
					$cart_options[$option['product_option_id']] = $option['product_option_value_id'];
				}
				
				$ro_combs = $this->getROCombsByPOIds($p_product_id, $cart_options, true, true);
				foreach ($ro_combs as $ro_comb) {
					if ( !isset($qtys[$ro_comb['relatedoptions_id']]) ) {
						$qtys[$ro_comb['relatedoptions_id']] = 0;
					}
					$qtys[$ro_comb['relatedoptions_id']]+= $product['quantity'];
				}
			}
		}
		return $qtys;
	}
	
	public function getROFreeQuantitiesByOptions($product_id, $options) {
		
		$result = array();
		
		if ( $options && $product_id ) {
		
			$ro_combs_in_cart = $this->getROCombsWithQuantitiesInCartByProductId($product_id);
		
			$qtys = array();
			$ro_combs = $this->getROCombsByPOIds($product_id, $options, true, true);
			foreach ($ro_combs as $ro_comb) {
				$qtys[$ro_comb['relatedoptions_id']] = MAX(0, $ro_comb['quantity']);
			}
			
			foreach ( $qtys as $relatedoptions_id => &$qty ) {
				if ( !empty($ro_combs_in_cart[$relatedoptions_id]) ) {
					$ro_in_cart_quantity = $ro_combs_in_cart[$relatedoptions_id];
					$qty = MAX(0, $qty-$ro_in_cart_quantity);
				}
			}
			unset($qty);
			/*
			foreach ( $ro_combs_in_cart as $relatedoptions_id => $ro_in_cart_quantity ) {
				if ( isset($qtys[$ro_comb['relatedoptions_id']]) ) {
					$qtys[$relatedoptions_id] = MAX(0, $qtys[$relatedoptions_id]-$ro_in_cart_quantity);
				}
			}
			*/
			$quantity = false;
			foreach ($qtys as $qty) {
				if ($quantity === false) {
					$quantity = $qty;
				} else {
					$quantity = MIN($quantity, $qty);
				}
			}
			$result['quantity'] = $quantity;
			
			// check for specific option view (separate quantity inputs/selects for option values )
			// should return quantities allowed to add to cart (available) only for option combs where customer is set greater quantity (to display only warnings)
			if ( !empty($this->request->post['quantity_per_option']) && is_array($this->request->post['quantity_per_option']) ) {
				
				$quantity_per_options = $this->request->post['quantity_per_option'];
				
				foreach ( $quantity_per_options as $product_option_id => $quantity_per_option ) { // generally, there should be only on product option (product_option_value_id)
					if ( $quantity_per_option ) {
						foreach ( $quantity_per_option as $product_option_value_id => $product_option_value_quantity ) {
							$product_option_value_quantity = (int)$product_option_value_quantity;
							if ( $product_option_value_quantity ) {
								$current_options = $options;
								$current_options[$product_option_id] = $product_option_value_id;
								$qtys = array();
								
								$ro_combs = $this->getROCombsByPOIds($product_id, $current_options, true, true);
								foreach ($ro_combs as $ro_comb) {
									$qtys[$ro_comb['relatedoptions_id']] = MAX(0, $ro_comb['quantity']);
								}
								
								foreach ( $qtys as $relatedoptions_id => &$qty ) {
									if ( !empty($ro_combs_in_cart[$relatedoptions_id]) ) {
										$ro_in_cart_quantity = $ro_combs_in_cart[$relatedoptions_id];
										$qty = MAX(0, $qty-$ro_in_cart_quantity);
									}
								}
								unset($qty);
								if ( $qtys ) {
									$current_quantity = false;
									foreach ($qtys as $qty) {
										if ($current_quantity === false) {
											$current_quantity = $qty;
										} else {
											$current_quantity = MIN($current_quantity, $qty);
										}
									}
									if ( $product_option_value_quantity > $current_quantity ) {
										if ( !isset($result['quantity_per_option_value']) ) {
											$result['quantity_per_option_value'] = array();
										}
										$result['quantity_per_option_value'][$product_option_value_id] = $current_quantity;
									}
								}
							}
						}
					}
				}
			}
			
		}
		
		return $result;
		
	}
	
	
	// check is there's enough quantity for related options
	public function cart_stock($product_id, $options, $quantity) {
		
		$ro_settings = $this->config->get('related_options');
		$ro_combs = $this->getROCombsByPOIds($product_id, $options, true);
		//$ro_combs = $this->getROCombsByPOIds($product_id, $options);
		$stock_ok = true;
		if ($ro_combs) {
			foreach ($ro_combs as $ro_comb) {
				$stock_ok = $stock_ok && ($quantity <= $ro_comb['quantity'] || !empty($ro_settings['allow_zero_select']));
			}
		}
		
		return $stock_ok;
		
	}
	
	
	//protected function getROCombFromRODataByROId($ro_data, $ro_id) {
	//	foreach ( $ro_data as $ro_dt ) {
	//		foreach ( $ro_dt['ro'] as $ro_comb ) {
	//			if ( $ro_comb['relatedoptions_id'] == $ro_id ) {
	//				return $ro_comb;
	//			}
	//		}
	//	}
	//}
	
	
	// returns information for all relevant related options combinations
	// discounts and specials for current customer
	// if there's not price, discount or special for combination, this data takes from product 
	// all options values from related options combination should be equal to options given as parameter of function
	// (it's possible to have more options in parameter than in a related options combination)
	public function getROCombsByPOIds($product_id, $param_options, $use_cache=false, $p_allow_zero_quantity=-1, $use_ro_data_cache=false) {
		
		// comp for old code
		return \liveopencart\ext\ro::getInstance($this->registry)->getROCombsByPOIds($product_id, $param_options, $use_cache, $p_allow_zero_quantity, $use_ro_data_cache);
		
	}
	

//  public function get_option_types() {
//		return "'select', 'radio', 'image', 'block', 'color'";
//	}
  
//  public function get_compatible_options() {
//		
//		if (!$this->installed()) {
//			return array();
//		}
//		
//		$lang_id = $this->config->get('config_language_id');
//		//$lang_id = $this->getLanguageId($this->config->get('config_language'));
//		
//		$query = $this->db->query("SELECT O.option_id, OD.name FROM `".DB_PREFIX."option` O, `".DB_PREFIX."option_description` OD
//															WHERE O.option_id = OD.option_id
//																AND OD.language_id = ".$lang_id."
//																AND O.type IN (".$this->get_option_types().")
//															ORDER BY O.sort_order
//															");
//		
//		$opts = array();
//		foreach ($query->rows as $row) {
//			$opts[$row['option_id']] = $row['name'];
//		}
//		
//		return $opts;
//		
//	}
//  
//  public function get_compatible_options_values() {
//		
//		if (!$this->installed()) {
//			return array();
//		}
//		
//		$lang_id = $this->config->get('config_language_id');
//		//$lang_id = $this->getLanguageId($this->config->get('config_language'));
//		
//		$optsv = array();
//		$compatible_options = $this->get_compatible_options();
//		$str_opt = "";
//		foreach ($compatible_options as $option_id => $option_name) {
//			$optsv[$option_id] = array('name'=>$option_name, 'values'=>array());
//			$str_opt .= ",".$option_id;
//		}
//		if ($str_opt!="") {
//			$str_opt = substr($str_opt, 1);
//			$query = $this->db->query("	SELECT OV.option_id, OVD.name, OVD.option_value_id
//																	FROM `".DB_PREFIX."option_value` OV, `".DB_PREFIX."option_value_description` OVD 
//																	WHERE OV.option_id IN (".$str_opt.")
//																		AND OVD.language_id = ".$lang_id."
//																		AND OV.option_value_id = OVD.option_value_id
//																	ORDER BY OV.sort_order
//																	");
//			foreach ($query->rows as $row) {
//				$optsv[$row['option_id']]['values'][$row['option_value_id']] = $row['name'];
//			}
//		}
//		
//		return $optsv;
//		
//	}
  
//  public function get_options_for_variant($relatedoptions_variant_id) {
//		
//		$options = array();
//		if ($relatedoptions_variant_id == 0) {
//			$copts = $this->get_compatible_options();
//			$options = array_keys($copts);
//		} else {
//			$options = array();
//			$query = $this->db->query("	SELECT VO.option_id
//																	FROM `".DB_PREFIX."relatedoptions_variant_option` VO
//																	WHERE relatedoptions_variant_id = ".$relatedoptions_variant_id."
//																	");
//			foreach ($query->rows as $row) {
//				$options[] = $row['option_id'];
//			}
//		}
//		
//		return $options;
//		
//	}
  
  
  // option_id
//  public function getProductROVariantOptions($product_id) {
//		
//		$options = array();
//		
//		$ro_variant_id = 0;
//		$query = $this->db->query("	SELECT VP.relatedoptions_variant_id
//																FROM 	" . DB_PREFIX . "relatedoptions_variant_product VP
//																WHERE VP.product_id = ".(int)$product_id."
//																");
//		if ($query->num_rows) {
//			$ro_variant_id = $query->row['relatedoptions_variant_id'];
//		}
//		
//		$options = $this->get_options_for_variant($ro_variant_id);
//		return $options;
//		
//	}
  
	function check_order_product_table() {
		
		// comp for old code
		return \liveopencart\ext\ro::getInstance($this->registry)->checkTableOrderProduct();
		
	}
	
	
	
	
		
	
	// comp for old code
	public function get_ro_data($product_id, $for_front_end=false, $p_allow_zero_quantity=-1, $use_cache=false) {
		
		return \liveopencart\ext\ro::getInstance($this->registry)->getROData($product_id, $for_front_end, $p_allow_zero_quantity, $use_cache);
		
	}
	

  public function installed() {
		
		return \liveopencart\ext\ro::getInstance($this->registry)->installed();
		//if ( is_null($this->module_installed_status) ) {
		//	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'module' AND `code` = 'related_options'");
		//	$this->module_installed_status = $query->num_rows;
		//}
		//return $this->module_installed_status;
	}
	
	//public function installedLivePrice() {
	//	
	//	if ( is_null($this->module_installed_status_liveprice) ) {
	//		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'module' AND `code` = 'liveprice'");
	//		$this->module_installed_status_liveprice = $query->num_rows;
	//	}
	//	return $this->module_installed_status_liveprice;
	//}
	//
	//private function getLivePriceSettings() {
	//	if ($this->liveprice_settings === false) {
	//		$this->liveprice_settings = array();
	//		if ( $this->installedLivePrice() ) {
	//		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'module' AND `code` = 'liveprice'");
	//		//if ($query->num_rows) {
	//			if ( class_exists('\\liveopencart\\ext\\liveprice') ) { // newer version
	//				$this->liveprice_settings = \liveopencart\ext\liveprice::getInstance($this->registry)->getSettings();
	//			} else { // old version
	//				$this->liveprice_settings = $this->config->get('liveprice_settings');
	//			}
	//		}
	//	}
	//	return $this->liveprice_settings;
	//}

	
	

}

