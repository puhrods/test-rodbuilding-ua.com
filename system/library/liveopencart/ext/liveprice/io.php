<?php

namespace liveopencart\ext\liveprice;

class io extends \liveopencart\lib\v0023\sub_library {
  
	use \liveopencart\lib\v0023\traits\cache;
	use \liveopencart\lib\v0023\traits\installed;
	
	public function installed() {
		return $this->getExtensionInstalledStatus('improved_options', 'io_installed');
	}
	
	protected function getModule() {
		if ( !$this->hasCacheSimple('module') ) {
			$this->setCacheSimple('module', \liveopencart\ext\io::getInstance($this->registry));
		}
		return $this->getCacheSimple('module');
	}
	
	public function getProductDefaultOptions($product_id, $current_options=array()) {
	  
		$io_defaults = $current_options;
		
		if ( $this->installed() ) {
		  
			$io_settings = $this->getModule()->getSettings();
			if ( !empty($io_settings['pov_default']) ) {
				
				if ( method_exists('\liveopencart\ext\io', 'getDefaultOptionsFront') ) { // new
					$io_defaults = $this->liveopencart_ext_io->getDefaultOptionsFront($product_id, false, $current_options ? array_values($current_options) : false);
				} else { // comp for old
					$sql_where_pov_customer_groups = "";
					if ( method_exists('\liveopencart\ext\io', 'getSQLWhereProductOptionValueByCustomerGroup') ) {
						$sql_where_pov_customer_groups = $this->liveopencart_ext_io->getSQLWhereProductOptionValueByCustomerGroup();
					}
				  
					$query = $this->db->query("
						SELECT PO.required, POV.product_option_id, POV.subtract, POV.quantity, POV.product_option_value_id, POV.default
						FROM ".DB_PREFIX."product_option_value POV
							,".DB_PREFIX."option_value OV
							,".DB_PREFIX."product_option PO
						WHERE POV.product_id = ".(int)$product_id."
						  AND POV.option_value_id = OV.option_value_id
						  AND PO.product_option_id = POV.product_option_id
						  ".($sql_where_pov_customer_groups ? " AND ".$sql_where_pov_customer_groups : "")."
						ORDER BY OV.sort_order ASC
					");
			
					$options_with_defaults = array(); // to use default values only first time
					foreach ($query->rows as $row) {
						
						if ( $io_settings['pov_default'] == 1 ) { // first available option value
							if ( !isset($io_defaults[$row['product_option_id']]) ) {
								if ( !$row['subtract'] || $row['quantity'] ) {
									$io_defaults[$row['product_option_id']] = $row['product_option_value_id'];
								}
							}
						} elseif ( $io_settings['pov_default'] == 2 ) { // first available option value is default is not set
							if ( !isset($io_defaults[$row['product_option_id']]) || ( $row['default'] && !in_array($row['product_option_id'], $options_with_defaults) ) ) {
								$io_defaults[$row['product_option_id']] = $row['product_option_value_id'];
								if ( $row['default'] ) {
									$options_with_defaults[] = $row['product_option_id'];
								}
							}
						} elseif ( $io_settings['pov_default'] == 3 ) { // default value
							if ( !isset($io_defaults[$row['product_option_id']]) && $row['default'] ) {
								$io_defaults[$row['product_option_id']] = $row['product_option_value_id'];
							}
						}
					}
				}
			}
		}
		
		return $io_defaults;
	}
  
}