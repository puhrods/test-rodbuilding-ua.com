<?php
class ModelShippingNovaPoshta extends Model {
    protected $extension = 'novaposhta';

    public function creatTables() {
        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . $this->extension . '_cities` (
   			`CityID` int(11) NOT NULL,
   			`Ref` varchar(36) NOT NULL,
   			`Description` varchar(200) NOT NULL, 
   			`DescriptionRu` varchar(200) NOT NULL,    
   			`Area` varchar(36) NOT NULL,
   			`SettlementType` varchar(36) NOT NULL,
   			`SettlementTypeDescription` varchar(200) NOT NULL, 
   			`SettlementTypeDescriptionRu` varchar(200) NOT NULL,
   			`Delivery1` tinyint(1) NOT NULL,
   			`Delivery2` tinyint(1) NOT NULL,
   			`Delivery3` tinyint(1) NOT NULL,
   			`Delivery4` tinyint(1) NOT NULL,
   			`Delivery5` tinyint(1) NOT NULL,
   			`Delivery6` tinyint(1) NOT NULL,
   			`Delivery7` tinyint(1) NOT NULL,
   			`Conglomerates` text NOT NULL,
   			`PreventEntryNewStreetsUser` text NOT NULL,
   			`IsBranch` tinyint(1) NOT NULL,
   			`SpecialCashCheck` tinyint(1) NOT NULL,
   			INDEX (`CityID`),
   			INDEX (`Area`),		
   			INDEX (`SettlementType`), 			
   			PRIMARY KEY (`Ref`)
   			) ENGINE=MyISAM DEFAULT CHARSET=utf8'
        );

        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . $this->extension . '_warehouses` (
   			`SiteKey` int(11) NOT NULL,
   			`Ref` varchar(36) NOT NULL,
   			`Description` varchar(500) NOT NULL, 
   			`DescriptionRu` varchar(500) NOT NULL,
   			`ShortAddress` varchar(500) NOT NULL, 
   			`ShortAddressRu` varchar(500) NOT NULL,		 
   			`TypeOfWarehouse` varchar(36) NOT NULL,
   			`CityRef` varchar(36) NOT NULL,
   			`CityDescription` varchar(200) NOT NULL,
   			`CityDescriptionRu` varchar(200) NOT NULL,  
   			`Number` int(11) NOT NULL, 	
   			`Phone` varchar(50) NOT NULL,  					
   			`Longitude` double NOT NULL,
   			`Latitude` double NOT NULL,
   			`PostFinance` tinyint(1) NOT NULL,
   			`BicycleParking` tinyint(1) NOT NULL,
   			`PaymentAccess` tinyint(1) NOT NULL,
   			`POSTerminal` tinyint(1) NOT NULL,
   			`InternationalShipping` tinyint(1) NOT NULL,
   			`TotalMaxWeightAllowed` int(11) NOT NULL,
   			`PlaceMaxWeightAllowed` int(11) NOT NULL,
   			`Reception` text NOT NULL,
   			`Delivery` text NOT NULL,
   			`Schedule` text NOT NULL,
   			`DistrictCode` varchar(20) NOT NULL,
   			`WarehouseStatus` varchar(20) NOT NULL,
   			`CategoryOfWarehouse` varchar(20) NOT NULL,
   			INDEX (`SiteKey`),
   			INDEX (`TypeOfWarehouse`),
   			INDEX (`CityRef`),
   			PRIMARY KEY (`Ref`)
   			) ENGINE=MyISAM DEFAULT CHARSET=utf8'
        );

        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . $this->extension . '_references` (
   			`type` varchar(100) NOT NULL, 
   			`value` mediumtext NOT NULL,  
   			UNIQUE(`type`)
   			) ENGINE=MyISAM DEFAULT CHARSET=utf8'
        );

        $result = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_name` = '" . DB_PREFIX . "order' AND `table_schema` = '" . DB_DATABASE . "' AND `column_name` = '" . $this->extension . "_cn_number'")->row;

        if (!$result) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` 
				ADD `" . $this->extension . "_cn_number` varchar(100) NOT NULL AFTER `invoice_prefix`, 
				ADD `" . $this->extension . "_cn_ref` varchar(100) NOT NULL AFTER `" . $this->extension . "_cn_number`"
            );
        }
    }

    public function deleteTables() {
        $this->db->query("DROP TABLE `" . DB_PREFIX . $this->extension . "_cities`,  `" . DB_PREFIX  . $this->extension . "_warehouses`, `" . DB_PREFIX  . $this->extension . "_references`");
    }

    public function getOrder($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `order_id` = '" . (int)$order_id . "'");

        return $query->row;
    }

	public function getOrderByDocumentNumber($number) {
		$query = $this->db->query("SELECT `order_id` FROM `" . DB_PREFIX . "order` WHERE `" . $this->extension . "_cn_number` = '" . $this->db->escape($number) . "'");

		return $query->row;
	}

    public function getOrderProducts($order_id) {
        $product_data = array();

        if (version_compare(VERSION, '1.5.4', '>=')) {
            $products = $this->db->query("SELECT `op`.*, `p`.`sku`, `p`.`ean`, `p`.`upc`, `p`.`jan`, `p`.`isbn`, `p`.`mpn`, `p`.`weight`, `p`.`weight_class_id`, `p`.`length`, `p`.`width`, `p`.`height`, `p`.`length_class_id` FROM `" . DB_PREFIX . "order_product` AS `op` INNER JOIN `" . DB_PREFIX . "product` AS `p` ON `op`.`product_id` = `p`.`product_id` AND `op`.`order_id` = " . (int)$order_id)->rows;
        } else {
            $products = $this->db->query("SELECT `op`.*, `p`.`sku`, `p`.`upc`, `p`.`weight`, `p`.`weight_class_id`, `p`.`length`, `p`.`width`, `p`.`height`, `p`.`length_class_id` FROM `" . DB_PREFIX . "order_product` AS `op` INNER JOIN `" . DB_PREFIX . "product` AS `p` ON `op`.`product_id` = `p`.`product_id` AND `op`.`order_id` = " . (int)$order_id)->rows;
        }

        foreach ($products as $product) {
            $options = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_option` WHERE `order_id` = '" . (int)$order_id . "' AND `order_product_id` = '" . (int)$product['order_product_id'] . "'")->rows;

            $option_data   = array();
            $option_weight = 0;

            foreach ($options as $option) {
                if ($option['type'] != 'file') {
                    $option_data[] = array(
                        'name'  => $option['name'],
                        'value' => $option['value']
                    );
                }

                $product_option_value_info = $this->db->query("SELECT `ovd`.`name`, `pov`.`option_value_id`, `pov`.`quantity`, `pov`.`subtract`, `pov`.`price`, `pov`.`price_prefix`, `pov`.`points`, `pov`.`points_prefix`, `pov`.`weight`, `pov`.`weight_prefix` FROM `" . DB_PREFIX . "product_option_value` AS `pov` LEFT JOIN `" . DB_PREFIX . "option_value_description` AS `ovd` ON (`pov`.`option_value_id` = `ovd`.`option_value_id`) WHERE `pov`.`product_id` = '" . (int)$product['product_id'] . "' AND `pov`.`product_option_value_id` = '" . (int)$option['product_option_value_id'] . "' AND `ovd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'")->row;

                if ($product_option_value_info) {
                    if ($product_option_value_info['weight_prefix'] == '+') {
                        $option_weight += $product_option_value_info['weight'];
                    } elseif ($product_option_value_info['weight_prefix'] == '-') {
                        $option_weight -= $product_option_value_info['weight'];
                    }
                }
            }

            if (version_compare(VERSION, '1.5.4', '>=')) {
                $ean  = $product['ean'];
                $jan  = $product['jan'];
                $isbn = $product['isbn'];
                $mpn  = $product['mpn'];
            } else {
                $ean  = '';
                $jan  = '';
                $isbn = '';
                $mpn  = '';
            }

            $product_data[] = array(
                'order_product_id' => $product['order_product_id'],
                'name'             => $product['name'],
                'model'            => $product['model'],
                'option'   	 	   => $option_data,
                'quantity'         => $product['quantity'],
                'sku'              => $product['sku'],
                'upc'              => $product['upc'],
                'ean'              => $ean,
                'jan'              => $jan,
                'isbn'             => $isbn,
                'mpn'              => $mpn,
                'weight'           => ($product['weight'] + $option_weight) * $product['quantity'],
                'weight_class_id'  => $product['weight_class_id'],
                'length'           => $product['length'],
                'width'            => $product['width'],
                'height'           => $product['height'],
                'length_class_id'  => $product['length_class_id']
            );
        }

        return $product_data;
    }
	
	public function addCNToOrder($order_id, $number, $ref = '') {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `" . $this->extension . "_cn_number` = '" . $this->db->escape(trim($number)) . "', `" . $this->extension . "_cn_ref` = '" . $this->db->escape(trim($ref)) . "' WHERE `order_id` = " . (int)$order_id);

        return $this->db->countAffected();
	}
	
	public function deleteCNFromOrder($orders) {
		foreach ($orders as $k => $v) {
            $orders[$k] = "'" . $v . "'";
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `" . $this->extension . "_cn_number` = '', `" . $this->extension . "_cn_ref` = '' WHERE `order_id` IN (" . implode(',', $orders) . ")");
	}
	
	public function getSimpleFields($order_id) {
		$data = array();
		
		$table = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "order_simple_fields'")->row;
		
		if ($table) {
			$data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_simple_fields` WHERE `order_id` = '" . (int) $order_id . "'")->row;
		}
		
		return $data;
	}

    public function getZoneIDByName($name) {
        $zone = $this->db->query("SELECT `zone_id` FROM `" . DB_PREFIX . "zone` WHERE `name` = '" . $this->db->escape($name) . "'")->row;

        return !empty($zone['zone_id']) ? $zone['zone_id'] : false;
    }
}

class ModelExtensionShippingNovaPoshta extends ModelShippingNovaPoshta {

}