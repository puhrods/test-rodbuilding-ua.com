<?php
class ModelShippingUkrPoshta extends Model {
    protected $extension = 'ukrposhta';

    public function creatTables() {
        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . $this->extension . '_references` (
   			`type` varchar(100) NOT NULL, 
   			`value` mediumtext NOT NULL,  
   			UNIQUE(`type`)
   			) ENGINE=MyISAM DEFAULT CHARSET=utf8'
        );

        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . $this->extension . '_regions` (
   			`REGION_ID` int(11) NOT NULL,
   			`REGION_UA` varchar(50) NOT NULL,
   			`REGION_EN` varchar(50) NOT NULL,
   			`REGION_RU` varchar(50) NOT NULL, 
   			`REGION_KATOTTG` bigint(20) NOT NULL,
   			`REGION_KOATUU` bigint(20) NOT NULL,
   			PRIMARY KEY (`REGION_ID`)
   			) ENGINE=MyISAM DEFAULT CHARSET=utf8'
        );

        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . $this->extension . '_cities` (
   			`CITY_ID` int(11) NOT NULL,
   			`CITY_UA` varchar(100) NOT NULL,
   			`CITY_EN` varchar(100) NOT NULL, 
   			`CITY_RU` varchar(100) NOT NULL,
   			`OLDCITY_UA` varchar(100) NOT NULL,
   			`OLDCITY_EN` varchar(100) NOT NULL, 
   			`OLDCITY_RU` varchar(100) NOT NULL,   
   			`CITYTYPE_UA` varchar(20) NOT NULL,
   			`CITYTYPE_EN` varchar(20) NOT NULL, 
   			`CITYTYPE_RU` varchar(20) NOT NULL,
   			`SHORTCITYTYPE_UA` varchar(10) NOT NULL,
   			`SHORTCITYTYPE_EN` varchar(10) NOT NULL, 
   			`SHORTCITYTYPE_RU` varchar(10) NOT NULL,
   			`CITY_KOATUU` bigint(20) NOT NULL,
   			`CITY_KATOTTG` bigint(20) NOT NULL,
   			`LONGITUDE` double NOT NULL,
   			`LATTITUDE` double NOT NULL,
   			`OWNOF` varchar(50) NOT NULL,
   			`POPULATION` int(11) NOT NULL,
   			`DISTRICT_ID` int(11) NOT NULL,
   			`DISTRICT_UA` varchar(50) NOT NULL,
   			`DISTRICT_EN` varchar(50) NOT NULL, 
   			`DISTRICT_RU` varchar(50) NOT NULL,
   			`NEW_DISTRICT_UA` varchar(50) NOT NULL,
   			`REGION_ID` int(11) NOT NULL,
   			`REGION_UA` varchar(50) NOT NULL,
   			`REGION_EN` varchar(50) NOT NULL, 
   			`REGION_RU` varchar(50) NOT NULL,
   			`NAME_UA` varchar(50) NOT NULL,
   			INDEX (`DISTRICT_ID`),
   			INDEX (`REGION_ID`),	
   			PRIMARY KEY (`CITY_ID`)
   			) ENGINE=MyISAM DEFAULT CHARSET=utf8'
        );

        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . $this->extension . '_departments` (
            `ID` int(11) NOT NULL,
            `POSTINDEX` varchar(10) NOT NULL,
            `TECHINDEX` varchar(10) NOT NULL,	
            `PO_LONG` varchar(300) NOT NULL,
            `PO_SHORT` varchar(100) NOT NULL,
            `PHONE` varchar(100) NOT NULL,
            `ADDRESS` varchar(300) NOT NULL,
            `MEREZA_NUMBER` int(11) NOT NULL,	
            `PARENT_ID` int(11) NOT NULL,
            `POSTTERMINAL` tinyint(1) NOT NULL,
            `PELPEREKAZY` tinyint(1) NOT NULL,	
            `IS_CASH` tinyint(1) NOT NULL,	
            `IS_SECURITY` tinyint(1) NOT NULL,	
            `IS_SMARTBOX` tinyint(1) NOT NULL,
            `IS_FLAGMAN` tinyint(1) NOT NULL,
            `IS_AUTOMATED` tinyint(1) NOT NULL,
            `ISVPZ` tinyint(1) NOT NULL,
            `IS_DHL` tinyint(1) NOT NULL,
   			`LONGITUDE` double NOT NULL,
   			`LATTITUDE` double NOT NULL,
   			`TYPE_LONG` varchar(200) NOT NULL,
            `TYPE_SHORT` varchar(200) NOT NULL,
            `TYPE_ACRONYM` varchar(20) NOT NULL,
            `HOUSENUMBER` varchar(100) NOT NULL,
            `STREET_UA` varchar(200) NOT NULL,
   			`STREET_EN` varchar(200) NOT NULL, 
   			`STREET_RU` varchar(200) NOT NULL,
   			`STREETTYPE_UA` varchar(20) NOT NULL,
   			`STREETTYPE_EN` varchar(20) NOT NULL, 
   			`STREETTYPE_RU` varchar(20) NOT NULL,
   			`CITY_ID` int(11) NOT NULL,
   			`CITY_UA` varchar(100) NOT NULL,
   			`CITY_EN` varchar(100) NOT NULL, 
   			`CITY_RU` varchar(100) NOT NULL,
   			`CITYTYPE_UA` varchar(20) NOT NULL,
   			`CITYTYPE_EN` varchar(20) NOT NULL, 
   			`CITYTYPE_RU` varchar(20) NOT NULL,
   			`SHORTCITYTYPE_UA` varchar(10) NOT NULL,
   			`SHORTCITYTYPE_EN` varchar(10) NOT NULL, 
   			`SHORTCITYTYPE_RU` varchar(10) NOT NULL,
   			`DISTRICT_ID` int(11) NOT NULL,
   			`DISTRICT_UA` varchar(50) NOT NULL,
   			`DISTRICT_EN` varchar(50) NOT NULL, 
   			`DISTRICT_RU` varchar(50) NOT NULL,
   			`NEW_DISTRICT_UA` varchar(50) NOT NULL,
   			`REGION_ID` int(11) NOT NULL,
   			`REGION_UA` varchar(50) NOT NULL,
   			`REGION_EN` varchar(50) NOT NULL, 
   			`REGION_RU` varchar(50) NOT NULL,
   			`LOCK_CODE` int(11) NOT NULL,
   			`LOCK_UA` varchar(50) NOT NULL,
   			`LOCK_EN` varchar(50) NOT NULL, 
   			`LOCK_RU` varchar(50) NOT NULL,
   			INDEX (`CITY_ID`),
   			INDEX (`DISTRICT_ID`),
   			INDEX (`REGION_ID`),	
   			PRIMARY KEY (`ID`)
   			) ENGINE=MyISAM DEFAULT CHARSET=utf8'
        );

        $result = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_name` = '" . DB_PREFIX . "order' AND `table_schema` = '" . DB_DATABASE . "' AND `column_name` = 'ukrposhta_cn_number'")->row;

        if (!$result) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` 
				ADD `ukrposhta_cn_number` varchar(100) NOT NULL AFTER `invoice_prefix`, 
				ADD `ukrposhta_cn_uuid` varchar(100) NOT NULL AFTER `ukrposhta_cn_number`"
            );
        }
    }

    public function deleteTables() {
        $this->db->query("DROP TABLE `" . DB_PREFIX  . $this->extension . "_references`, `" . DB_PREFIX  . $this->extension . "_regions`, `" . DB_PREFIX  . $this->extension . "_cities`, `" . DB_PREFIX  . $this->extension . "_departments`");
    }

    public function getOrder($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `order_id` = '" . (int)$order_id . "'");

        return $query->row;
    }

	public function getOrderByDocumentNumber($number) {
		$query = $this->db->query("SELECT `order_id` FROM `" . DB_PREFIX . "order` WHERE `ukrposhta_cn_number` = '" . $this->db->escape($number) . "'");

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
	
	public function addCNToOrder($order_id, $number, $uuid) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `ukrposhta_cn_number` = '" . $this->db->escape($number) . "', `ukrposhta_cn_uuid` = '" . $this->db->escape($uuid) . "' WHERE `order_id` = " . (int)$order_id);

        return $this->db->countAffected();
	}
	
	public function deleteCNFromOrder($uuids) {
		foreach ($uuids as $k => $v) {
				$uuids[$k] = "'" . $v . "'";
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `ukrposhta_cn_number` = '', `ukrposhta_cn_uuid` = '' WHERE `ukrposhta_cn_uuid` IN (" . implode(',', $uuids) . ")");
	}

    public function getOrders($data = array()) {
        $sql = "SELECT `order_id`, `ukrposhta_cn_number`, `ukrposhta_cn_uuid` FROM `" . DB_PREFIX . "order` WHERE `ukrposhta_cn_number` <> ''";

        if (!empty($data['filter_cn_number'])) {
            $sql .= " AND `ukrposhta_cn_number` = '" . $data['filter_cn_number'] . "'";
        }

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND `order_id` = '" . (int)$data['filter_order_id'] . "'";
        }

        $sort_data = array(
            'order_id',
            'ukrposhta_cn_number'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY `order_id`";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalOrders() {
        $sql = "SELECT COUNT(*) as `total` FROM `" . DB_PREFIX . "order` WHERE `ukrposhta_cn_number` <> ''";

        $query = $this->db->query($sql)->row;

        return isset($query['total']) ? $query['total'] : 0;
    }
	
	public function getSimpleFields($order_id) {
		$data = array();
		
		$table = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "order_simple_fields'")->row;
		
		if ($table) {
			$data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_simple_fields` WHERE `order_id` = '" . (int)$order_id . "'")->row;
		}
		
		return $data;
	}
}

class ModelExtensionShippingUkrPoshta extends ModelShippingUkrPoshta {

}