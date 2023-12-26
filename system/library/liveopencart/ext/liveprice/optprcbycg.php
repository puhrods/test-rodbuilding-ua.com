<?php

namespace liveopencart\ext\liveprice;

class optprcbycg extends \liveopencart\lib\v0023\sub_library {
  
	use \liveopencart\lib\v0023\traits\cache;
	use \liveopencart\lib\v0023\traits\db;
	
	public function installed() {
		if (!$this->hasCacheSimple('optprcbycg_installed')) {
			$query = $this->db->query("SELECT * FROM  `".DB_PREFIX."modification` WHERE `code` = '37588' AND `status` = 1 "); // Product Option Price By Customer Group  opencarttools@gmail.com
			if ( $query->num_rows ) {
				$installed = $this->existColumn('product_option_value', 'optprcbycg');
			} else {
				$installed = false;
			}
			$this->setCacheSimple('optprcbycg_installed', $installed);
		}
		return $this->getCacheSimple('optprcbycg_installed');
	}
	
	public function getPOVPriceByPOV($pov) {
		
		$price = $pov['price'];
		
		if ($this->installed()) {
			
			if (!empty($pov['optprcbycg'])) {
				$prices_by_cg = json_decode($pov['optprcbycg'], true);
				$customer_group_id = $this->config->get('config_customer_group_id');
				if (isset($prices_by_cg[$customer_group_id])) {
					$price = (float)$prices_by_cg[$customer_group_id];
				}
			}
			
		}
		
		return $price;
		
	}
	
	
}