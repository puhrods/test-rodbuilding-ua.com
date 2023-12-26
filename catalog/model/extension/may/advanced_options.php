<?php
class ModelExtensionMayAdvancedOptions extends Model {
	public function getCartProductVariableData() {
		$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

		$cart_product_models = array();
		$cart_product_skus = array();
		$cart_product_upcs = array();
		$cart_product_eans = array();
		$cart_product_jans = array();
		$cart_product_isbns = array();
		$cart_product_mpns = array();
		$cart_product_locations = array();
		$cart_product_dimension_ls = array();
		$cart_product_dimension_ws = array();
		$cart_product_dimension_hs = array();
		$cart_product_length_units = array();
		$cart_product_weights = array();
		$cart_product_weight_units = array();
		$cart_product_images = array();

		foreach ($cart_query->rows as $cart) {
			$cart_option = json_decode($cart['option'], true);

			$cart_product_models[$cart['cart_id']] = "";
			if (isset($cart_option['model']) && $cart_option['model'] != '') {
				$cart_product_models[$cart['cart_id']] = $cart_option['model'];
			}

			if (isset($cart_option['sku']) && $cart_option['sku'] != '') {
				$cart_product_skus[$cart['cart_id']] = $cart_option['sku'];
			}

			if (isset($cart_option['upc']) && $cart_option['upc'] != '') {
				$cart_product_upcs[$cart['cart_id']] = $cart_option['upc'];
			}

			if (isset($cart_option['ean']) && $cart_option['ean'] != '') {
				$cart_product_eans[$cart['cart_id']] = $cart_option['ean'];
			}

			if (isset($cart_option['jan']) && $cart_option['jan'] != '') {
				$cart_product_jans[$cart['cart_id']] = $cart_option['jan'];
			}

			if (isset($cart_option['isbn']) && $cart_option['isbn'] != '') {
				$cart_product_isbns[$cart['cart_id']] = $cart_option['isbn'];
			}

			if (isset($cart_option['mpn']) && $cart_option['mpn'] != '') {
				$cart_product_mpns[$cart['cart_id']] = $cart_option['mpn'];
			}

			if (isset($cart_option['location']) && $cart_option['location'] != '') {
				$cart_product_locations[$cart['cart_id']] = $cart_option['location'];
			}

			if (isset($cart_option['dimension_l']) && $cart_option['dimension_l'] != '') {
				$cart_product_dimension_ls[$cart['cart_id']] = $cart_option['dimension_l'];
			}

			if (isset($cart_option['dimension_w']) && $cart_option['dimension_w'] != '') {
				$cart_product_dimension_ws[$cart['cart_id']] = $cart_option['dimension_w'];
			}

			if (isset($cart_option['dimension_h']) && $cart_option['dimension_h'] != '') {
				$cart_product_dimension_hs[$cart['cart_id']] = $cart_option['dimension_h'];
			}

			if (isset($cart_option['length_unit']) && $cart_option['length_unit'] != '') {
				$cart_product_length_units[$cart['cart_id']] = $cart_option['length_unit'];
			}

			if (isset($cart_option['weight']) && $cart_option['weight'] != '') {
				$cart_product_weights[$cart['cart_id']] = $cart_option['weight'];
			}

			if (isset($cart_option['weight_unit']) && $cart_option['weight_unit'] != '') {
				$cart_product_weight_units[$cart['cart_id']] = $cart_option['weight_unit'];
			}

			if (isset($cart_option['image']) && $cart_option['image'] != '') {
				$cart_product_images[$cart['cart_id']] = $cart_option['image'];
			}
		}

		return array(
			'models' => $cart_product_models,
			'skus' => $cart_product_skus,
			'upcs' => $cart_product_upcs,
			'eans' => $cart_product_eans,
			'jans' => $cart_product_jans,
			'isbns' => $cart_product_isbns,
			'locations' => $cart_product_locations,
			'mpns' => $cart_product_mpns,
			'dimension_ls' => $cart_product_dimension_ls,
			'dimension_ws' => $cart_product_dimension_ws,
			'dimension_hs' => $cart_product_dimension_hs,
			'length_units' => $cart_product_length_units,
			'weights' => $cart_product_weights,
			'weight_units' => $cart_product_weight_units,
			'images' => $cart_product_images
		);
	}

	public function getProductFirstOptionValues($product_id) {
		$this->load->library('may/may_advanced_options');
		$advanced_option_config = $this->may_advanced_options->getProductAdvancedOptionConfig($product_id);
		if (isset($advanced_option_config['show_first_option_in_list']) && !$advanced_option_config['show_first_option_in_list']) {
			return array();
		}

		$query = $this->db->query("SELECT maopv.*, pov.product_option_value_id, od.name AS option_name, p.price as main_price, p.tax_class_id, ovd.name, ov.image AS option_value_image, ov.sort_order FROM (SELECT * FROM " . DB_PREFIX . "may_advanced_option_product_value WHERE product_id = '" . (int)$product_id . "' AND hide <> '1' AND combination_id NOT LIKE '%-%' AND product_option_id IN (SELECT product_option_id FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "' AND `value` = 'may_advanced_option')) AS maopv LEFT JOIN " . DB_PREFIX . "product_option_value as pov ON maopv.product_option_id=pov.product_option_id AND maopv.option_value_id=pov.option_value_id LEFT JOIN " . DB_PREFIX . "option_description as od ON maopv.option_id = od.option_id AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' LEFT JOIN " . DB_PREFIX . "product as p ON p.product_id = maopv.product_id LEFT JOIN " . DB_PREFIX . "option_value ov ON maopv.option_value_id = ov.option_value_id LEFT JOIN " . DB_PREFIX . "option_value_description as ovd ON ov.option_value_id = ovd.option_value_id AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order ASC, ov.option_value_id ASC");

		$values = array();
		foreach ($query->rows as $row) {
			if (!$this->config->get('may_advanced_options_show_out_of_stock_option') && isset($advanced_option_config['subtract']) && $advanced_option_config['subtract'] && !$row['quantity']) {
				continue;
			}

			if ($row['price_prefix'] == 'as' && (float)$row['price']) {
				if ((float)$row['main_price'] > (float)$row['price']) {
					$row['price'] = (float)$row['main_price'] - (float)$row['price'];
					$row['price_prefix'] = '-';
				} else {
					$row['price'] = (float)$row['price'] - (float)$row['main_price'];
					$row['price_prefix'] = '+';
				}
			}

			$images = json_decode($row['image'], true);
			if (is_array($images) && count($images)) {
				$row['image'] = $images;
				
				if (isset($advanced_option_config['swatch_image']) && $advanced_option_config['swatch_image']) {
					$row['swatch_image'] = $images[0];
				} else {
					$row['swatch_image'] = $row['option_value_image'];
				}
			} else {
				$row['image'] = array();
				$row['swatch_image'] = $row['option_value_image'];
			}

			$row['subtract'] = isset($advanced_option_config['subtract']) && $advanced_option_config['subtract'] ? 1 : 0;

			$values[] = $row;
		}
		
		return $values;
	}

	public function getLayoutModuleByCode($layout_id, $code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int)$layout_id . "' AND `code` LIKE '" . $code . "%' LIMIT 0, 1");

		return $query->row;
	}

	public function dispatchJson($json) {
		return $json;
	}

	public function updateOrderProductAdvancedOptions($order_product, $order_options) {
		foreach ($order_options as $order_option) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option_value` WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
			
			$row = $query->row;
			if (isset($row) && !empty($row)) {
				$this->db->query("UPDATE " . DB_PREFIX . "may_advanced_option_product_value SET quantity = quantity - " . (int)$order_product['quantity'] . " WHERE product_option_id = '" . (int)$row['product_option_id'] . "' AND option_value_id = '" . (int)$row['option_value_id'] . "'");
			}
		}
	}

    public function getOptionQuantity($product_id, $model) {

       $query = $this->db->query("SELECT quantity FROM " . DB_PREFIX . "may_advanced_option_product_value WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($model) . "'");

       if($query->num_rows) {
           return $query->row['quantity'];
       }

       return false;

    }
}
