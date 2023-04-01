<?php
class ModelModuleNovaPoshtaCron extends Model {
	public function getOrders() {
        if (version_compare(VERSION, '3', '>=')) {
            $settings = $this->config->get('shipping_novaposhta');
        } else {
            $settings = $this->config->get('novaposhta');
        }

		$result = $this->db->query("SELECT `o`.*, `l`.`code`, `l`.`directory`, CONCAT(`c`.`firstname`, ' ', `c`.`lastname`) as `customer` FROM `" . DB_PREFIX . "order` as `o` LEFT JOIN `" . DB_PREFIX . "language` as `l` ON (`l`.`language_id` = `o`.`language_id`) LEFT JOIN `" . DB_PREFIX . "customer` as `c` ON (`c`.`customer_id` = `o`.`customer_id`)  WHERE `order_status_id` IN (" . implode(',', $settings['tracking_statuses']) . ") AND `novaposhta_cn_number` <> ''");

		return $result;
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
	
	public function getSimpleFields($order_id) {
		$data = array();
		
		$table = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "order_simple_fields'")->row;
		
		if ($table) {
			$data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_simple_fields` WHERE `order_id` = '" . (int) $order_id . "'")->row;
		}
		
		return $data;
	}
}

class ModelExtensionModuleNovaPoshtaCron extends ModelModuleNovaPoshtaCron {

}