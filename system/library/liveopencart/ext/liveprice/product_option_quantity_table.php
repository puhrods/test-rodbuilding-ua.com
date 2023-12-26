<?php

namespace liveopencart\ext\liveprice;

class product_option_quantity_table extends \liveopencart\lib\v0023\sub_library {
  
	use \liveopencart\lib\v0023\traits\cache;
	use \liveopencart\lib\v0023\traits\installed;
	
	public function installed() {
		return $this->getExtensionInstalledStatus('prdoptqtytbl', 'product_option_quantity_table_installed', function(){
			return !empty($this->getSettings()['module_prdoptqtytbl_status']);
		});
	}
	
	protected function getSettings() {
		if ( !$this->hasCacheSimple(__FUNCTION__) ) {
			
			$this->load->model('setting/setting');
			
			$settings = $this->model_setting_setting->getSetting('module_prdoptqtytbl');
			
			$this->setCacheSimple(__FUNCTION__, $settings);
		}
		return $this->getCacheSimple(__FUNCTION__);
	}
	
	protected function getOptionTypeStatus($option_type) {
		return $option_type == 'prdoptqtytbl_radio' || $option_type == 'prdoptqtytbl_checkbox';
	}
	
	public function getProductOptionValueIdFromValue($option_type, $value) {
		
		if ( $this->installed() && $this->getOptionTypeStatus($option_type) ) {
		
			$value_parts = explode('|', $value);
			
			if ( count($value_parts) > 1 ) {
				return $value_parts[0];
			}
			
		}
//			$prdoptqtytbl_optprice = '';
//			if($this->check_prdoptqtytbl_status($option_query->row['type'])) {
//				if(isset($product_option_value_id) && $product_option_value_id && stristr($product_option_value_id,"|") && $option_query->row['type'] == 'prdoptqtytbl_checkbox') {
//					$prdoptqtytbl_optqty = substr($product_option_value_id,strpos($product_option_value_id,"|")+1);	
//				} else if(isset($value) && $value && stristr($value,"|")) {
//					$prdoptqtytbl_optqty = substr($value,strpos($value,"|")+1);
//				}
//				$optprc = $this->get_prdoptqtytbl_curprice($option_value_query->row['price'], $product_query->row['tax_class_id']);
//				$optprctot = $this->get_prdoptqtytbl_curprice($option_value_query->row['price'], $product_query->row['tax_class_id'], $prdoptqtytbl_optqty);
// 				$prdoptqtytbl_optprice = ' ('.$option_value_query->row['price_prefix'] . $optprc .' X '.$prdoptqtytbl_optqty .' = '.$optprctot.') ';
// 				$option_value_query->row['price'] *= $prdoptqtytbl_optqty;
//			}
		
	}
	
	public function getQuantityFromValue($option_type, $value) {
		
		if ( $this->installed() && $this->getOptionTypeStatus($option_type) ) {
			
			$value_parts = explode('|', $value);
			
			if ( count($value_parts) > 1 ) {
				return $value_parts[1];
			}
			
		}
	}
	
	public function getChangedPOVPrice($option_type, $value, $price) {
		
		$pov_quantity = $this->getQuantityFromValue($option_type, $value);
			
		if ( $pov_quantity ) {
			$price = $price*$pov_quantity;
		}
			
		return $price;
	}
	
	protected function getProductTaxClassId($product_id) {
		$cache_key = __FUNCTION__.$product_id;
		if ( !$this->hasCacheSimple($cache_key) ) {
			$query = $this->db->query("SELECT `tax_class_id` FROM `".DB_PREFIX."product` WHERE `product_id` = ".(int)$product_id." ");
			$tax_class_id = $query->num_rows ? $query->row['tax_class_id'] : '';
			$this->setCacheSimple($cache_key, $tax_class_id);
		}
		return $this->getCacheSimple($cache_key);
	}
	
	protected function formatProductPriceToDisplay($product_id, $price, $currency) { // in this mod - displays option prices with tax
		$tax_class_id = $this->getProductTaxClassId($product_id);
		return $this->currency->format($this->tax->calculate($price, $tax_class_id, $this->config->get('config_tax') ? 'P' : false) , $currency);
	}
	
	public function getChangedPOVName($product_id, $option_type, $value, $pov, $pov_name, $currency) {
		
		$pov_quantity = $this->getQuantityFromValue($option_type, $value);
			
		if ( $pov_quantity ) {
			
			$price_f = $this->formatProductPriceToDisplay($product_id, $pov['price'], $currency);
			$total_f = $this->formatProductPriceToDisplay($product_id, $pov['price']*$pov_quantity, $currency);
			$pov_name .= ' ('.$pov['price_prefix'].$price_f .' X '. $pov_quantity.' = '.$pov['price_prefix'].$total_f.')';
		}
			
		return $pov_name;
	}
	
	
}