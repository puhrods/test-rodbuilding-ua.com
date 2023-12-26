<?php
//  Related Options / Связанные опции
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

namespace liveopencart\ext;

class ro extends \liveopencart\lib\v0007\extension {
	
	protected $extension_code = 'ro3';
	protected $version = '3.1.0';
	
	protected function init() {
		if ( class_exists('\liveopencart\ext\ropro') ) {
			$this->extension_code = \liveopencart\ext\ropro::getInstance($this->registry)->getExtensionCode();
		}
	}
	
	public function installed() {
		return $this->getExtensionInstalledStatus('related_options', 'ro_installed');
	}
	
	public function versionPRO() {
		return ($this->getExtensionCode() == 'ropro3');
	}
	
	public function getSettings() {
		if ( !$this->hasCacheSimple(__FUNCTION__) ) {
			$ro_settings = $this->config->get('related_options');
			$this->setCacheSimple(__FUNCTION__, $ro_settings ? $ro_settings : array() );
		}
		return $this->getCacheSimple(__FUNCTION__);
	}
	
	public function getSetting($setting_key) {
		$ro_settings = $this->getSettings();
		if ( isset($ro_settings[$setting_key]) ) {
			return $ro_settings[$setting_key];
		}
	}
	
	public function installedLivePrice() {
		return $this->getExtensionInstalledStatus('liveprice', 'liveprice_installed');
	}
	
	public function getLivePriceSettings() {
	
		if ( !$this->hasCacheSimple(__FUNCTION__) ) {
			$lp_settings = array();
			if ( $this->installedLivePrice() ) {
				if ( class_exists('\\liveopencart\\ext\\liveprice') ) { // newer version
					$lp_settings = \liveopencart\ext\liveprice::getInstance($this->registry)->getSettings();
				} else { // old version
					$lp_settings = $this->config->get('liveprice_settings');
				}
			}
			$this->setCacheSimple(__FUNCTION__, $lp_settings );
		}
		return $this->getCacheSimple(__FUNCTION__);
	}
	
	
	public function callPOIU($method_name, $args=array()) {
		if ( class_exists('\liveopencart\ext\poiu') ) {
			return call_user_func_array(array(\liveopencart\ext\poiu::getInstance($this->registry), $method_name), $args);
		}
	}
	
	public function callPOIP($method_name, $args=array()) {
		if ( class_exists('\liveopencart\ext\poip') ) {
			return call_user_func_array(array(\liveopencart\ext\poip::getInstance($this->registry), $method_name), $args);
		}
	}
	
	// returns only switched-on additional fields (sku, upc, location)
	public function getAdditionalFields($include_model=false) {
		
		$fields = array();
		
		if ($this->installed()) {
			$ro_settings = $this->getSettings();
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
	
	private function getOrderProduct($order_product_id) {
		$query = $this->db->query("SELECT * FROM `".DB_PREFIX."order_product` WHERE `order_product_id` = ".(int)$order_product_id." ");
		return $query->row;
	}
	
	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
	
	private function fillProductAdditionalFieldsByOrderProduct($product, $order_product_id) {
		$order_product = $this->getOrderProduct($order_product_id);
		if ( $order_product ) {
			foreach ( $this->getAdditionalFields(true) as $field ) {
				if ( isset($order_product[$field]) ) {
					$product[$field] = $order_product[$field];
				}
			}
		}
		return $product;
	}
	
	public function fillOrderProductsAdditionalFieldsForProducts($order_products) {
		$new_order_products = array();
		foreach ( $order_products as $order_product ) {
			$order_product = $this->fillProductAdditionalFieldsByOrderProduct($order_product, $order_product['order_product_id']);
			$new_order_products[] = $order_product;
		}
		return $new_order_products;
	}
	
	//update_ro_quantity
	public function updateROQuantity($product_id, $order_id, $order_product_id, $quantity, $sign='+') {
		
		if (!$this->installed()) {
			return;
		}
		
		$query = $this->db->query("SELECT subtract FROM `".DB_PREFIX."product` WHERE `product_id` = ".(int)$product_id." " );
		if ($query->num_rows && $query->row['subtract']) {
			
			$product_options = $this->getOrderOptions((int)$order_id, (int)$order_product_id);
			if ($product_options) {
				
				$options = array();
				foreach ($product_options as $product_option) {
					$options[$product_option['product_option_id']] = $product_option['product_option_value_id'];
				}
				
				$ro_combs = $this->getROCombsByPOIds($product_id, $options, false, true);
				if ($ro_combs) {
					foreach ($ro_combs as $ro_comb) {
						
						$this->db->query("UPDATE `".DB_PREFIX."relatedoptions` SET quantity=(quantity".$sign."".(int)$quantity.") WHERE `relatedoptions_id` = ".(int)$ro_comb['relatedoptions_id']." " );
					}
				}
			}	
		}
		
	}
	
	public function getROCombsByPOIdsAssocParams($params = array()) {
		$defaults = array('use_cache'=>false, 'allow_zero_quantity'=>-1, 'use_ro_data_cache'=>false, 'strict'=>true);
		foreach ( $defaults as $param_key => $def_val ) {
			if ( !isset($params[$param_key]) ) {
				$params[$param_key] = $def_val;
			}
		}
		return $this->getROCombsByPOIds($params['product_id'], $params['options'], $params['use_cache'], $params['allow_zero_quantity'], $params['use_ro_data_cache'], $params['strict']);
	}
	
	// returns information for all relevant related options combinations
	// discounts and specials for current customer
	// if there's not price, discount or special for combination, this data takes from product 
	// all options values from related options combination should be equal to options given as parameter of function
	// (it's possible to have more options in parameter than in a related options combination)
	public function getROCombsByPOIds($product_id, $param_options, $use_cache=false, $p_allow_zero_quantity=-1, $use_ro_data_cache=false, $strict=true) {
		
		if (!$param_options || !is_array($param_options) || count($param_options)==0 ) {
			return FALSE;
		}
		
		$options = array();
		foreach ($param_options as $po_id => $pov_id) {
			if ( !is_array($pov_id) && !is_object($pov_id) ) {
				$options[(int)$po_id] = (int)$pov_id;
			}
		}
		
		$cache_key = $product_id.'_'.json_encode($options).'_'.$p_allow_zero_quantity.'_'.$strict;
		
		if ( $use_cache && $this->hasCache(__FUNCTION__, $cache_key) ) {
			return $this->getCache(__FUNCTION__, $cache_key);
		}
		
		$matches = array();
		$ro_data = $this->getROData($product_id, false, $p_allow_zero_quantity, $use_ro_data_cache);
		
		foreach ($ro_data as $ro_dt) {
			
			$options_values_to_check = array();
			
			if ( $ro_dt['options_ids'] ) {
				
				foreach ($ro_dt['options_ids'] as $po_id) {
					if ( isset($options[$po_id]) && $options[$po_id] ) {
						$options_values_to_check[$po_id] = (int)$options[$po_id];
					}
				}
			}
			
			foreach ($ro_dt['ro'] as $ro_comb) {
				
				if ( $strict && $options_values_to_check == $ro_comb['options'] ) {
				//if ( !array_diff_assoc($options_values_to_check, $ro_comb['options']) && count($options_values_to_check) == count($ro_comb['options']) ) {
					$matches[] = $ro_comb;
					break;
				} elseif ( !$strict && $options_values_to_check && !array_diff_assoc($options_values_to_check, $ro_comb['options']) ) {
					$matches[] = $ro_comb;
				}
			}
		}
		
		$this->setCache(__FUNCTION__, $cache_key, $matches);
		
		return $matches;
	}
	
	private function getProductStockStatusName($product_id) {
		$query = $this->db->query("
			SELECT PS.name product_stock_status, PS.stock_status_id stock_status_id
			FROM `" . DB_PREFIX . "product` P
				LEFT JOIN ".DB_PREFIX."stock_status PS ON (PS.stock_status_id = P.stock_status_id && PS.language_id = ".(int)$this->config->get('config_language_id')." )
			WHERE P.product_id = ".(int)$product_id."
		");
		if ( $query->num_rows ) {
			return $query->row['product_stock_status'];
		}
	}
	
	private function hasProductRO($product_id) {
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."relatedoptions_variant_product WHERE product_id = ".(int)$product_id." AND relatedoptions_use = 1 ");
		return $query->num_rows;
	}
	
	public function getROData($product_id, $for_front_end=false, $p_allow_zero_quantity=-1, $use_cache=false) {
		
		if (!$this->installed()) {
			return array();
		}
		
		$this->load->language('product/product');
		
		$customer_group_id = (int)$this->config->get('config_customer_group_id');
		$lang_id = $this->config->get('config_language_id');
		
		$ro_settings = $this->getSettings();
		
		$allow_zero_quantity = $p_allow_zero_quantity!==-1 ? $p_allow_zero_quantity : !empty($ro_settings['allow_zero_select']);
		
		$cache_key = json_encode( array($product_id, $for_front_end, $allow_zero_quantity, $customer_group_id, $lang_id) );
		if ( $use_cache && $this->hasCache(__FUNCTION__, $cache_key) ) {
			return $this->getCache(__FUNCTION__, $cache_key);
		} else {
		
			$ro_data = array();
			
			if ( $this->hasProductRO($product_id) ) {
			
				$product_stock_status = $this->getProductStockStatusName($product_id);
				
				$query = $this->db->query("
					SELECT ROVP.*
					FROM 	`" . DB_PREFIX . "relatedoptions_variant_product` ROVP
						LEFT JOIN	`" . DB_PREFIX . "relatedoptions_variant` ROV ON (ROVP.relatedoptions_variant_id = ROV.relatedoptions_variant_id)
					WHERE ROVP.product_id = " . (int)$product_id . "
						AND ROVP.relatedoptions_use = 1
					ORDER BY ROV.sort_order, ROV.relatedoptions_variant_name, ROVP.relatedoptions_variant_id, ROVP.relatedoptions_variant_product_id
				");
				
				$rovp_rows = $query->rows;
				
				foreach ($rovp_rows as $rovp_row) {
					
					$ro_data[] = array(	'rovp_id' 		=> $rovp_row['relatedoptions_variant_product_id'],
										'use' 			=> $rovp_row['relatedoptions_use'],
										'rov_id' 		=> $rovp_row['relatedoptions_variant_id'],
										'ro'			=> array(),
										'options_ids' 	=> array(),
										);
					$cnt = count($ro_data)-1;
					$rovp_id = (int)$rovp_row['relatedoptions_variant_product_id'];
					
					
					$query = $this->db->query("
						SELECT RO.*, SS.name stock_status
						FROM ( 	SELECT * FROM `" . DB_PREFIX . "relatedoptions` RO
										WHERE RO.relatedoptions_variant_product_id = " . (int)$rovp_id . "
										".($allow_zero_quantity ? "" : " AND RO.quantity > 0 ")."
									) RO
								LEFT JOIN ".DB_PREFIX."stock_status SS ON (SS.stock_status_id = RO.stock_status_id && SS.language_id = ".(int)$lang_id." )
						ORDER BY RO.relatedoptions_id
					");
					
					foreach ($query->rows as $row) {
						
						$row['product_stock_status'] = $product_stock_status;
						
						$ro_data[$cnt]['ro'][$row['relatedoptions_id']] = $row;
						
						if ($for_front_end) {
							unset($ro_data[$cnt]['ro'][$row['relatedoptions_id']]['quantity']);
						}
						
						$stock = '';
						$in_stock = false;
						if (isset($ro_settings['spec_ofs'])&& $ro_settings['spec_ofs']) {
							$in_stock = true;
							if ($row['quantity'] <= 0) {
								$stock = ($row['stock_status']) ? $row['stock_status'] : $row['product_stock_status'] ;
								$in_stock = false;
							} elseif ($this->config->get('config_stock_display')) {
								$stock = $row['quantity'];
							} else {
								$stock = $this->language->get('text_instock');
							}
						}
						
						$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['in_stock'] = $in_stock;
						$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['stock'] = $stock;
						$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['options'] = array();
						$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['discounts'] = array();
						$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['specials'] = array();
						
					}
					
					$query = $this->db->query("
						SELECT ROO.*, POV.product_option_id, POV.product_option_value_id
						FROM 	(	SELECT ROO.*
										FROM ".DB_PREFIX."relatedoptions_option ROO, ".DB_PREFIX."relatedoptions RO
										WHERE ROO.product_id = ".(int)$product_id."
											AND ROO.product_id = RO.product_id
											AND ROO.relatedoptions_id = RO.relatedoptions_id
											AND RO.relatedoptions_variant_product_id = " . (int)$rovp_id . "
											".($allow_zero_quantity ? "" : " AND RO.quantity > 0 ")."
									) ROO 
								, (	SELECT *
										FROM ".DB_PREFIX."product_option_value POV
										WHERE POV.product_id = ".(int)$product_id."
									) POV 
						WHERE ROO.option_value_id = POV.option_value_id
					");
					
					foreach ($query->rows as $row) {
						$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['options'][$row['product_option_id']] = $row['product_option_value_id'];
						if (!$for_front_end) {
							$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['options_original'][$row['option_id']] = $row['option_value_id'];
						}
						if ( !in_array($row['product_option_id'], $ro_data[$cnt]['options_ids']) ) {
							$ro_data[$cnt]['options_ids'][] = $row['product_option_id'];
						}
					}
					
					if (!$for_front_end) {
						$query = $this->db->query("
							SELECT RD.*
							FROM 	`" . DB_PREFIX . "relatedoptions` RO
									, `" . DB_PREFIX . "relatedoptions_discount` RD
							WHERE RO.product_id = " . (int)$product_id . "
								AND RO.relatedoptions_id = RD.relatedoptions_id
								AND RO.relatedoptions_variant_product_id = ".(int)$rovp_id."
								AND RD.customer_group_id = ".(int)$customer_group_id."
								".($allow_zero_quantity ? "" : " AND RO.quantity > 0 ")."
							ORDER BY RD.relatedoptions_id, RD.customer_group_id, RD.quantity 
						");
						
						foreach ($query->rows as $row) {
							$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['discounts'][] = $row;
						}
						
						
						$query = $this->db->query("
							SELECT RS.*
							FROM 	`" . DB_PREFIX . "relatedoptions` RO
									, `" . DB_PREFIX . "relatedoptions_special` RS
							WHERE RO.product_id = " . (int)$product_id . "
								AND RO.relatedoptions_id = RS.relatedoptions_id
								AND RO.relatedoptions_variant_product_id = ".(int)$rovp_id."
								AND RS.customer_group_id = ".(int)$customer_group_id."
								".($allow_zero_quantity ? "" : " AND RO.quantity > 0 ")."
							ORDER BY RS.relatedoptions_id, RS.customer_group_id
						");
						
						foreach ($query->rows as $row) {
							$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['specials'][] = $row;
							$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['current_customer_group_special_price'] = $row['price'];
						}
					}
					
					if ( $for_front_end ) {
						if ( !$ro_data[$cnt]['ro'] ) {
							$ro_data[$cnt]['ro'][] = array('relatedoptions_id'=>0, 'options'=>array());
							$ro_data[$cnt]['options_ids'] = $this->getProductOptionsForVariant($ro_data[$cnt]['rov_id'], $product_id);
						}
					}
					
				}
			}
			
			
			
			$this->setCache(__FUNCTION__, $cache_key, $ro_data);
			
			return $ro_data;
		}
		
	}
	
	public function updateOrderProductAdditionalFields($product, $order_product_id) {
		
		if ($this->installed()) {
			$this->checkTableOrderProduct();
			$ro_settings = $this->getSettings();
			$quantity = (int)$product['quantity'];
			
			$ro_options = array();
			foreach ($product['option'] as $option) {
				if (isset($option['product_option_value_id'])) {
					$ro_options[$option['product_option_id']] = $option['product_option_value_id'];
				}
			}
			
			$ro_combs = $this->getROCombsByPOIds($product['product_id'], $ro_options);
			
			// to set values even product hasn't related options
				
			$product = $this->getProduct($product['product_id']);

			$custom_fields = $this->getCustomFields($product, $ro_combs);
			
			foreach ($custom_fields['codes'] as $ro_field_name => $ro_field_value) {
				if (!empty($ro_settings['spec_'.$ro_field_name])) {
					$this->db->query("UPDATE " . DB_PREFIX . "order_product SET `".$ro_field_name."`='".$this->db->escape($ro_field_value)."' WHERE order_product_id = " . (int)$order_product_id . "");
				}
			}
			
			if (!empty($ro_settings['spec_weight'])) {
				$this->db->query("UPDATE " . DB_PREFIX . "order_product SET `weight`='".(float)($custom_fields['weight']*$quantity)."' WHERE order_product_id = " . (int)$order_product_id . "");
			}
		}
	}
	
	public function getROCombFromRODataByROId($ro_data, $ro_id) {
		foreach ( $ro_data as $ro_dt ) {
			foreach ( $ro_dt['ro'] as $ro_comb ) {
				if ( $ro_comb['relatedoptions_id'] == $ro_id ) {
					return $ro_comb;
				}
			}
		}
	}
	
	public function getProductInfoForROId($product, $ro_id, $ro_data=false, $ro_comb=false) {
		
		$data = array();
		
		if ( $product && $ro_id ) {
			
			if ( $ro_comb === false ) {
				if ( $ro_data === false ) {
					$ro_data = $this->getROData($product['product_id'], true);
				}
				$ro_comb = $this->getROCombFromRODataByROId($ro_data, $ro_id);
			}
			if ( $ro_comb ) {
				$ro_custom_fields = $this->getCustomFields($product, array($ro_comb));
				foreach ( $ro_custom_fields['codes'] as $code_key => $code_val ) {
					$data[$code_key] = $code_val;
				}
				
				if ( $this->installedLivePrice() ) { // allows to take standard product option prices into account
					
					if ( class_exists('\\liveopencart\\ext\\liveprice') ) { // new LP
						$lp_prices = \liveopencart\ext\liveprice::getInstance($this->registry)->getCalc()->getProductPriceByParamsArray( array(
							'product_id' 		=> $product['product_id'],
							'quantity'			=> 1,
							'options'			=> $ro_comb['options'],
							'use_ro_data_cache'	=> true, // always use caches
							'use_cart_cache'	=> true,
						) );
					} else { // old LP version
						$this->load->model('extension/liveopencart/liveprice');
						$lp_data = $this->model_extension_liveopencart_liveprice->getProductPriceByParamsArray( array(
							'product_id' 		=> $product['product_id'],
							'quantity'			=> 1,
							'options'			=> $ro_comb['options'],
							'use_ro_data_cache'	=> true, // always use caches
							'use_cart_cache'	=> true,
						) );
						if ( !empty($lp_data['prices']) ) {
							$lp_prices = $lp_data['prices'];
						}
					}
					if ( !empty($lp_prices) ) {
						$data['price'] = $lp_prices['price_old_opt'];
						if ( $lp_prices['special'] ) {
							$data['special'] = $lp_prices['special_opt'];
						}
					}
				} else { // allows to take only prices of related options into account
				
					$ro_price_data = $this->calcProductPriceWithRO($product['price'], array($ro_comb), $product['special']);
					if ($ro_price_data) {
						$data['price'] = $ro_price_data['price'];
						$data['special'] = $ro_price_data['special'];
					}
				}
			}
		}
		return $data;
	}
	
	private function getProduct($product_id) {
		$query = $this->db->query("SELECT * FROM `".DB_PREFIX."product` WHERE `product_id` = ".(int)$product_id." ");
		return $query->row;
	}
	
	public function checkTableOrderProduct() {
		
		if (!$this->installed()) return;
		
		$ro_settings = $this->getSettings();
		
		if (isset($ro_settings['spec_sku']) && $ro_settings['spec_sku']) {
			$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` WHERE field='sku' ");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `".DB_PREFIX."order_product` ADD COLUMN `sku` varchar(64) NOT NULL " );
			}
		}
		
		if (isset($ro_settings['spec_upc']) && $ro_settings['spec_upc']) {
			$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` WHERE field='upc' ");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `".DB_PREFIX."order_product` ADD COLUMN `upc` varchar(12) NOT NULL " );
			}
		}
		
		if (isset($ro_settings['spec_ean']) && $ro_settings['spec_ean']) {
			$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` WHERE field='ean' ");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `".DB_PREFIX."order_product` ADD COLUMN `ean` varchar(14) NOT NULL " );
			}
		}
		
		if (isset($ro_settings['spec_location']) && $ro_settings['spec_location']) {
			$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` WHERE field='location' ");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `".DB_PREFIX."order_product` ADD COLUMN `location` varchar(128) NOT NULL " );
			}
		}
		
		if (isset($ro_settings['spec_weight']) && $ro_settings['spec_weight']) {
			$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` WHERE field='weight' ");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `".DB_PREFIX."order_product` ADD COLUMN `weight` decimal(15,8) NOT NULL " );
			}
		}
		
		
	}
	
	public function getCustomFields($product_info, $ro_combs) {
		
		$ro_settings = $this->getSettings();
		
		$ro_weight = $product_info['weight'];
		$ro_adds = array(
			'model'		=>	$product_info['model'],
			'sku'		=>	$product_info['sku'],
			'upc'		=>	$product_info['upc'],
			'ean'		=>	$product_info['ean'],
			'location'	=>	$product_info['location'],
		);
		
		$last_model_is_from_product = true;
		if ($ro_combs) {
			
			foreach ($ro_combs as $ro_comb) {
				
				foreach ($ro_adds as $ro_field_name => &$ro_field_value) {
					
					if ( isset($ro_settings['spec_'.$ro_field_name]) && $ro_settings['spec_'.$ro_field_name] ) {
						
						if ( $ro_comb[$ro_field_name] ) {
						
							if ($ro_field_name == 'model') {
								
								if ( $ro_settings['spec_'.$ro_field_name] == 1 ) {
									$ro_field_value = $ro_comb[$ro_field_name];
								} elseif ( $ro_settings['spec_'.$ro_field_name] == 2 ) {
									if ( $last_model_is_from_product ) {
										$ro_field_value = '';
										$last_model_is_from_product = false;
									}
									if ( $ro_field_value && isset($ro_settings['spec_model_delimiter_ro']) ) {
										$ro_field_value.= isset($ro_settings['spec_model_delimiter_ro']);
									}
									$ro_field_value.= $ro_comb[$ro_field_name];
								} elseif ( $ro_settings['spec_'.$ro_field_name] == 3 ) {
									/*
									if ($ro_field_value == '') {
										$ro_field_value = $product_info[$ro_field_name];
										$last_model_is_from_product = true;
									}
									*/
									
									if ( $last_model_is_from_product && isset($ro_settings['spec_model_delimiter_product']) ) {
										$ro_field_value.= $ro_settings['spec_model_delimiter_product'];
									} elseif ( !$last_model_is_from_product && isset($ro_settings['spec_model_delimiter_ro']) ) {
										$ro_field_value.= $ro_settings['spec_model_delimiter_ro'];
									}
									
									$ro_field_value.= $ro_comb[$ro_field_name];
									$last_model_is_from_product = false;
								}
								
							} else {
								
								if ($ro_comb[$ro_field_name]) {
									$ro_field_value = $ro_comb[$ro_field_name];
								}
							}
						}
					}
				}
				unset($ro_field_value);
				
				if (!empty($ro_settings['spec_weight'])) {
					
					if ( (float)$ro_comb['weight'] ) {
						if ($ro_comb['weight_prefix'] == '=') {
							$ro_weight = (float)$ro_comb['weight'];
						} elseif ($ro_comb['weight_prefix'] == '+') {
							$ro_weight+= (float)$ro_comb['weight'];
						} elseif ($ro_comb['weight_prefix'] == '-') {
							$ro_weight-= (float)$ro_comb['weight'];
						}
					}
				}
			}
		}
		
		return array('codes' => $ro_adds, 'weight' => $ro_weight);
	}
	
	public function getDiscountQueryForCart($ro_combs, $ro_quantities) {
		$ro_settings = $this->getSettings();
		
		if ( !empty($ro_combs) && !empty($ro_settings['spec_price']) && !empty($ro_settings['spec_price_discount']) ) {
			
			// get first option combination with discount
			foreach ($ro_combs as $ro_comb) {
				
				if ( !empty($ro_comb['discounts']) ) {
					$ro_discount_quantity = $ro_quantities[$ro_comb['relatedoptions_id']];
					$product_ro_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "relatedoptions_discount
																													WHERE relatedoptions_id = '" . (int)$ro_comb['relatedoptions_id'] . "'
																													AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'
																													AND quantity <= '" . (int)$ro_discount_quantity . "'
																													ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");
					if ($product_ro_discount_query->num_rows) {
						return $product_ro_discount_query;
						break;
					}
				}
			}
		}		
	}
	public function getSpecialQueryForCart($ro_combs) {
		
		$ro_settings = $this->getSettings();
		
		if ( !empty($ro_combs) && !empty($ro_settings['spec_price']) && !empty($ro_settings['spec_price_discount']) ) {
			
			// get first option combination with special
			foreach ($ro_combs as $ro_comb) {
				
				if ( !empty($ro_comb['specials']) ) {
					$product_ro_special_query = $this->db->query("SELECT price FROM ".DB_PREFIX."relatedoptions_special 
																												WHERE relatedoptions_id = '" . (int)$ro_comb['relatedoptions_id'] . "'
																													AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'
																												ORDER BY priority ASC, price ASC LIMIT 1");
					if ($product_ro_special_query->num_rows) {
						return $product_ro_special_query;
						break;
					}
				}
			}
		}
	}
	
	public function getProductPriceWithRoByProductOptions($product_id, $options, $price, $special=false) {
		if ($this->installed()) {
			
			$ro_settings = $this->getSettings();
			if ( $ro_settings && is_array($ro_settings) && count($options) ) {
				
				$ro_combs = $this->getROCombsByPOIds($product_id, $options);
				return $this->calcProductPriceWithRO($price, $ro_combs, $special);
			}	
		}
		return false;
	}
	
	// Live Price PRO has setting to calculate RO combination discounts as addition (+ or -) if RO combination price prefix is + (plus) or - (minus)
	// in this case RO discounts do not replace standard discounts, just go to ro_price_modificator
	private function calcProductPriceWithRO_getDiscount($ro_comb, $quantity) {
		$discount_price = 0;
		if ($quantity !== false && $quantity >= 1 && !empty($ro_comb['discounts'])) {
			
			$discounts_reversed = array_reverse($ro_comb['discounts']); // change order to from the higher to the lower
			foreach ($discounts_reversed as $discount) { 
				if ($quantity >= $discount['quantity']) {
					
					$discount_price = $discount['price'];
					
					break; // array of discounts is ordered (DESC), thus first found occurrence is enough
				}
			}
		}
		return $discount_price;
	}
	
	// without discouts and specials ?
	public function calcProductPriceWithRO($product_price, $ro_combs, $special=0, $stock=false, $ro_price_modificator=0, $quantity=false) {
  		
		$ro_settings = $this->getSettings();
		$lp_settings = $this->getLivePriceSettings();
		
		$ro_price = $product_price;
		$ro_discount_addition_total = 0;
		$ro_special_addition_total = 0;
		
		$in_stock = null;
		
		$current_product_price = $product_price;
		
		if ($this->installed()) {
			
			foreach ($ro_combs as $ro_comb) {
				
				if ( !empty($ro_settings['spec_price']) ) {
					
					//if (isset($ro_comb['price']) && $ro_comb['price']!=0) {
						// "+" may has effect even without price (by discounts)
						if (isset($ro_settings['spec_price_prefix']) && $ro_settings['spec_price_prefix'] && $ro_comb['price_prefix'] && $ro_comb['price_prefix'] != '=' ) {
						//if (isset($ro_settings['spec_price_prefix']) && $ro_settings['spec_price_prefix'] && ($ro_comb['price_prefix'] == '+' || $ro_comb['price_prefix'] == '-') ) {
							
							//$ro_price_addition = $ro_comb['price'];
							$ro_price_addition = 0;
							
							if ( $ro_comb['price_prefix'] == '+' ) {
								$ro_price_addition = $ro_comb['price'];
							} elseif ( $ro_comb['price_prefix'] == '-' ) {
								$ro_price_addition = -$ro_comb['price'];
							//} elseif ( $ro_comb['price_prefix'] == '+%' ) {
							//	$ro_price_addition = $current_product_price/100*$ro_comb['price'];
							//} elseif ( $ro_comb['price_prefix'] == '-%' ) {
							//	$ro_price_addition = -$current_product_price/100*$ro_comb['price'];
							//} elseif ( $ro_comb['price_prefix'] == '=%' ) {
							//	$ro_price_addition = $current_product_price/100*$ro_comb['price'] - $current_product_price;
							//} elseif ( $ro_comb['price_prefix'] == '*' ) {
							//	$ro_price_addition = $current_product_price*$ro_comb['price'] - $current_product_price;
							//} elseif ( $ro_comb['price_prefix'] == '/' ) {
							//	if ( (float)$ro_comb['price'] != 0 ) {
							//		$ro_price_addition = $current_product_price/$ro_comb['price'] - $current_product_price;
							//	}
							}
							
							$current_product_price+= $ro_price_addition;
							
							if (!empty($ro_price_addition)) {
								//$ro_price+= $ro_price_addition;
								$ro_price_modificator+= $ro_price_addition;
							}
							
							if ( !empty($lp_settings['ropro_discounts_addition']) ) {
								if ( $ro_comb['price_prefix'] == '+' ) {
									$ro_discount_addition_total+= $this->calcProductPriceWithRO_getDiscount($ro_comb, $quantity);
								} elseif ( $ro_comb['price_prefix'] == '-' ) { 
									$ro_discount_addition_total-= $this->calcProductPriceWithRO_getDiscount($ro_comb, $quantity);
								}
							}
							
							if ( !empty($lp_settings['ropro_specials_addition']) && $ro_comb['specials'] && $ro_comb['specials'][0] ) {
								$ro_special_row = $ro_comb['specials'][0];
								if ( $ro_comb['price_prefix'] == '+' ) {
									$ro_special_addition_total+= $ro_special_row['price'];
								} elseif ( $ro_comb['price_prefix'] == '-' ) { 
									$ro_special_addition_total-= $ro_special_row['price'];
								}
							}
						
						} elseif ( !empty($ro_comb['price']) && (float)$ro_comb['price'] ) {
							$ro_price = $ro_comb['price'];
						}
					//}
					
					if (isset($ro_comb['current_customer_group_special_price']) && $ro_comb['current_customer_group_special_price']) {
						$special = $ro_comb['current_customer_group_special_price'];
					}
				}

				if ( !empty($ro_settings['spec_ofs']) ) {
					$stock = $ro_comb['stock'];
				} elseif ( $this->config->get('config_stock_display') ) {
					$stock = (int)$ro_comb['quantity'];
				} else {
					$stock = false;
				}
				$in_stock = $ro_comb['in_stock'];
			}
			$ro_price+= $ro_price_modificator; // apply + and - modifiers at the last step (after = )
		}
		return array('price'=>$ro_price,
								 'special'=>$special,
								 'stock'=>$stock,
								 'in_stock'=>$in_stock,
								 'price_modificator'=>$ro_price_modificator,
								 'discount_addition'=>$ro_discount_addition_total,
								 'special_addition'=>$ro_special_addition_total,
								 );
		
	}
	
	public function getProductROVariantOptions($product_id) {
		
		$options = array();
		
		$ro_variant_id = 0;
		$query = $this->db->query("	SELECT VP.relatedoptions_variant_id
									FROM 	" . DB_PREFIX . "relatedoptions_variant_product VP
									WHERE VP.product_id = ".(int)$product_id."
									");
		if ($query->num_rows) {
			$ro_variant_id = $query->row['relatedoptions_variant_id'];
		}
		
		$options = $this->getOptionsForVariant($ro_variant_id);
		return $options;
		
	}
	
	// get_options_for_variant
	public function getOptionsForVariant($relatedoptions_variant_id) {
		
		$options = array();
		if ($relatedoptions_variant_id == 0) {
			$copts = $this->getCompatibleOptions();
			$options = array_keys($copts);
		} else {
			$options = array();
			$query = $this->db->query("	SELECT VO.option_id
										FROM `".DB_PREFIX."relatedoptions_variant_option` VO
										WHERE relatedoptions_variant_id = ".$relatedoptions_variant_id."
										");
			foreach ($query->rows as $row) {
				$options[] = $row['option_id'];
			}
		}
		
		return $options;
		
	}
	
	protected function getProductOptionsForVariant($rov_id, $product_id) {
		$option_ids = $this->getOptionsForVariant($rov_id);
		$option_ids = array_map('intval', $option_ids);
		$product_option_ids = array();
		if ( $option_ids ) {
			$query = $this->db->query("SELECT * FROM ".DB_PREFIX."product_option_value WHERE product_id = ".(int)$product_id." AND option_id IN (".implode(',',$option_ids).") ");
			foreach ( $query->rows as $row ) {
				$product_option_ids[] = $row['product_option_id'];
			}
		}
		return $product_option_ids;
	}
	
	// get_compatible_options
	public function getCompatibleOptions() {
		
		if (!$this->installed()) {
			return array();
		}
		
		$lang_id = $this->config->get('config_language_id');
		
		$query = $this->db->query("SELECT O.option_id, OD.name FROM `".DB_PREFIX."option` O, `".DB_PREFIX."option_description` OD
															WHERE O.option_id = OD.option_id
																AND OD.language_id = ".$lang_id."
																AND O.type IN (".$this->getCompatibleOptionTypes().")
															ORDER BY O.sort_order
															");
		
		$opts = array();
		foreach ($query->rows as $row) {
			$opts[$row['option_id']] = $row['name'];
		}
		
		return $opts;
		
	}
  
	// get_compatible_options_values
	public function getCompatibleOptionValues() {
		
		if (!$this->installed()) {
			return array();
		}
		
		$lang_id = $this->config->get('config_language_id');
		
		$optsv = array();
		$compatible_options = $this->getCompatibleOptions();
		$str_opt = "";
		foreach ($compatible_options as $option_id => $option_name) {
			$optsv[$option_id] = array('name'=>$option_name, 'values'=>array());
			$str_opt .= ",".$option_id;
		}
		if ($str_opt!="") {
			$str_opt = substr($str_opt, 1);
			$query = $this->db->query("	SELECT OV.option_id, OVD.name, OVD.option_value_id
																	FROM `".DB_PREFIX."option_value` OV, `".DB_PREFIX."option_value_description` OVD 
																	WHERE OV.option_id IN (".$str_opt.")
																		AND OVD.language_id = ".$lang_id."
																		AND OV.option_value_id = OVD.option_value_id
																	ORDER BY OV.sort_order
																	");
			foreach ($query->rows as $row) {
				$optsv[$row['option_id']]['values'][$row['option_value_id']] = $row['name'];
			}
		}
		
		return $optsv;
		
	}
	
	// get_option_types
	public function getCompatibleOptionTypes() {
		return "'select', 'radio', 'image', 'block', 'color'";
	}
	
	protected function getOptionsWithNames() {
		if ( !$this->hasCacheSimple(__FUNCTION__) ) {
			
			$options = array();
			
			$query = $this->db->query("SELECT * FROM `".DB_PREFIX."option_description` WHERE language_id = ".(int)$this->config->get('config_language_id')." ");
			foreach ( $query->rows as $row ) {
				$options[$row['option_id']] = array(
					'option_id' => $row['option_id'],
					'name' => $row['name'],
					'values' => array(),
				);
			}
			
			$query = $this->db->query("
				SELECT OVD.*, OV.option_id
				FROM `".DB_PREFIX."option_value` OV
					,`".DB_PREFIX."option_value_description` OVD
				WHERE OV.option_value_id = OVD.option_value_id
				  AND OVD.language_id = ".(int)$this->config->get('config_language_id')."
			");
			foreach ( $query->rows as $row ) {
				if ( isset($options[$row['option_id']]) ) {
					$options[$row['option_id']]['values'][$row['option_value_id']] = array(
						'option_value_id' => $row['option_value_id'],
						'name' => $row['name'],
					);
				}
			}
			
			
			$this->setCacheSimple(__FUNCTION__, $options );
		}
		return $this->getCacheSimple(__FUNCTION__);
	}
	
	//protected function getOptionsWithNamesOrdered() {
	//	if ( !$this->hasCacheSimple(__FUNCTION__) ) {
	//		
	//		$options = array();
	//		
	//		$query = $this->db->query("
	//			SELECT OVD.*, OV.option_id, OD.name option_name
	//			FROM `".DB_PREFIX."option_value` OV
	//				,`".DB_PREFIX."option_value_description` OVD
	//				,`".DB_PREFIX."option` O,
	//				,`".DB_PREFIX."option_description` OD
	//			WHERE OV.option_value_id = OVD.option_value_id
	//			  AND O.option_id = OV.option_id
	//			  AND O.option_id = OD.option_id
	//			  AND OD.language_id = ".(int)$this->config->get('config_language_id')."
	//			  AND OVD.language_id = ".(int)$this->config->get('config_language_id')."
	//			ORDER BY O.sort_order ASC, OV.sort_order ASC
	//		");
	//		foreach ( $query->rows as $row ) {
	//			if ( count($options) == 0 || $options[count($options)]['option_id'] != $row['option_id'] ) {
	//				$options[] = array(
	//					'option_id' => $row['option_id'],
	//					'name' => $row['option_name'],
	//					'values' => array(),
	//				);
	//			}
	//			$options[count($options)]['values'] = array(
	//				'option_value_id' => $row['option_value_id'],
	//				'name' => $row['name'],
	//			);
	//			
	//		}
	//		
	//		
	//		$this->setCacheSimple(__FUNCTION__, $options );
	//	}
	//	return $this->getCacheSimple(__FUNCTION__);
	//}
	
	protected function getOptionsOrdered() {
		if ( !$this->hasCacheSimple(__FUNCTION__) ) {
			
			$options = array();
			
			$query = $this->db->query("SELECT `option_id` FROM `".DB_PREFIX."option` ORDER BY `sort_order` ASC ");
			foreach ( $query->rows as $row ) {
				$options[] = $row['option_id'];
			}
			
			$this->setCacheSimple(__FUNCTION__, $options );
		}
		return $this->getCacheSimple(__FUNCTION__);
	}
	
	public function getROCombDescription($ro_id) {
		
		$ro_options = $this->getROCombBasicOptionValues($ro_id);
		
		$options_with_names = $this->getOptionsWithNames();
		$options_ordered = $this->getOptionsOrdered();
		
		$texts = array();
		foreach ( $options_ordered as $option_id ) {
			if ( isset($ro_options[$option_id]) ) {
				$option_value_id = $ro_options[$option_id];
				if ( isset($options_with_names[$option_id]) && isset($options_with_names[$option_id]['values'][$option_value_id]) ) {
					$texts[] = $options_with_names[$option_id]['name'].':'.$options_with_names[$option_id]['values'][$option_value_id]['name'];
				}
			}
		}
		return implode(' / ', $texts);
	}
	
	public function getROCombBasicOptionValues($ro_id) {
		$query = $this->db->query("
			SELECT POV.option_id, POV.option_value_id
			FROM `".DB_PREFIX."relatedoptions_option` ROO
				,`".DB_PREFIX."product_option_value` POV
			WHERE ROO.relatedoptions_id = ".(int)$ro_id."
			  AND ROO.option_value_id = POV.option_value_id
		");
		$options = array();
		foreach ( $query->rows as $row ) {
			$options[$row['option_id']] = $row['option_value_id'];
		}
		return $options;
	}
	
}