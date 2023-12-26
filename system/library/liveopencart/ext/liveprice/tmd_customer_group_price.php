<?php

namespace liveopencart\ext\liveprice;

class tmd_customer_group_price extends \liveopencart\lib\v0023\sub_library {
  
	use \liveopencart\lib\v0023\traits\cache;
	use \liveopencart\lib\v0023\traits\installed;
	
	public function installed() {
		return $this->getExtensionInstalledStatus('customergroupprice', 'tmd_customer_group_price_installed', function(){
			return !empty($this->getConfig());
		});
	}
	
	protected function getConfig() {
		if ( !$this->hasCacheSimple(__FUNCTION__) ) {
			
			$customer_group_price = array();
			if ( $this->config->get('customergroupprice_status') ) {
				$customergroupprice_data = $this->config->get('customergroupprice_data');
				if (!empty($customergroupprice_data)) {
					foreach ($customergroupprice_data as $key => $value) {
						$customer_group_price[$value['customer_group_id']] = array(
							'customer_group_id' => $value['customer_group_id'],
							'prefix' 			=> $value['prefix'],
							'value' 			=> $value['value'],
						 );
					}
				}
			}
			$this->setCacheSimple(__FUNCTION__, $customer_group_price);
		}
		return $this->getCacheSimple(__FUNCTION__);
	}
  
	public function getChangedPOVPrice($pov_id, $pov_price) {
		
		$cgp_config = $this->getConfig();
		if ( $cgp_config ) {
			
			$customer_group_id = (int)$this->config->get('config_customer_group_id');
			
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "product_customergroup_optionvalue
				WHERE customer_group_id='".(int)$customer_group_id."'
				  AND product_option_value_id='".(int)$pov_id."'
			");
			if ( $query->num_rows && (float)$query->row['price'] ) {
				$pov_price = (float)$query->row['price'];
			}
			
			if (!empty($cgp_config[$customer_group_id]['value'])) {
				$prefix = $cgp_config[$customer_group_id]['prefix'];
				$value = $cgp_config[$customer_group_id]['value'];
				if ($prefix == 1) {
					$pov_price = $pov_price + $value;
				}else if ($prefix == 2 && $pov_price > $value){
					$pov_price = $pov_price  - $value;
				}else if ($prefix == 3){
					$pov_price = $pov_price  + ($pov_price * $value)/100;
				}else if ($prefix == 4){
					$pov_price = $pov_price  - ($pov_price * $value)/100;
				}
			}
			
			
		}
		return $pov_price;
	}
	
	public function getChangedProductPrice($product_id, $price) {
		
		$cgp_config = $this->getConfig();
		if ( $cgp_config ) {
			
			$customer_group_id = (int)$this->config->get('config_customer_group_id');
		
			$query = $this->db->query("
				SELECT * 
				FROM " . DB_PREFIX . "product_customergroup_price
				WHERE customer_group_id='".(int)$customer_group_id."'
				  AND product_id='".(int)$product_id."'
			");
			if ($query->num_rows) {
				if( (float)$query->row['price'] ){
					$price = $query->row['price'];
				}
			}
			else {
				if (!empty($cgp_config[$this->customer->getGroupId()]['value'])) {
					$prefix = $cgp_config[$this->customer->getGroupId()]['prefix'];
					$value = $cgp_config[$this->customer->getGroupId()]['value'];
					if ($prefix == 1) {
						$price = $price + $value;
					}else if ($prefix == 2 && $price > $value){
						$price = $price  - $value;
					}else if ($prefix == 3){
						$price = $price  + ($price * $value)/100;
					}else if ($prefix == 4){
						$price = $price  - ($price * $value)/100;
					}
				}	

			}
		}
		return $price;
	}
	
	
}