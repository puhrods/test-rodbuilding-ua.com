<?php
class ModelExtensionMayAdvancedOptionsProduct extends Model {
	public function setProductAdvancedOptions($product_id, $data) {
		if (!isset($data['may_advanced_option_values'])) {
            $this->deleteProductAdvancedOptions($product_id);
			return;
		}

		foreach ($data['may_advanced_option_values'] as $option_row => $product_options) {
			$product_options = json_decode(html_entity_decode($product_options), true);

			$max_depth = 0;
			$subtract = 0;
			foreach (array_reverse($product_options) as $option_value_row => $product_option) {
				if ($product_option['type'] != 'may_advanced_option') {
					continue;
				}

				$max_depth = count(explode('-', str_replace($product_option['name'] . ':::', '', $product_option['value'])));

				$this->db->query("DELETE FROM " . DB_PREFIX . "may_advanced_option_product_config WHERE product_id = '" . (int)$product_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "may_advanced_option_product_value WHERE product_id = '" . (int)$product_id . "'");
	
				$first_value = count($product_option['product_option_value']) ? current($product_option['product_option_value']) : array();
				$subtract = isset($first_value['subtract']) ? (int)$first_value['subtract'] : 0;
	
				$this->db->query("INSERT INTO " . DB_PREFIX . "may_advanced_option_product_config SET product_id = '" . (int)$product_id . "', option_name = '" . $this->db->escape($product_option['name']) . "', subtract = '" . $subtract . "', swatch_image = '" . (int)$product_option['swatch_image'] . "', show_first_option_in_list = '" . (int)$product_option['show_first_option_in_list'] . "'");

				break;
			}

			$tags = array();
			$total_quantity = 0;
			foreach ($product_options as $option_value_row => $product_option) {
				if ($product_option['type'] != 'may_advanced_option') {
					continue;
				}

				$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = 'may_advanced_option', required = '" . (int)$product_option['required'] . "'");
	
				$product_option_id = $this->db->getLastId();

				foreach ($product_option['product_option_value'] as $option_value_index => &$product_option_value) {
					$images = isset($data['may_advanced_option_images'][$option_row][$option_value_row][$product_option_value['option_value_id']]) ? $images = array_filter(array_unique($data['may_advanced_option_images'][$option_row][$option_value_row][$product_option_value['option_value_id']])) : array();

					$combination_id = str_replace($product_option['name'] . ':::', '', $product_option['value']);

					if (count(explode('-', $combination_id)) != $max_depth) {
						$option_value_key = $product_option['value'] . '-' . $product_option_value['option_value_id'];
						$quantity = 0;
						foreach ($product_options as $option_value_row2 => $product_option2) {
							if ($product_option2['type'] == 'may_advanced_option' &&
								strpos($product_option2['value'], $option_value_key) === 0 &&
								count(explode('-', str_replace($product_option2['name'] . ':::', '', $product_option2['value']))) == $max_depth) {
								foreach ($product_option2['product_option_value'] as $product_option_value2) {
									$quantity += $product_option_value2['quantity'];
								}
							}
						}
						$product_option_value['quantity'] = $quantity;
					} else {
						$total_quantity += (int)$product_option_value['quantity'];
					}

					if ($product_option_value['price_prefix'] == 'as' && (float)$product_option_value['price'] > 0) {
						if ((float)$data['price'] > (float)$product_option_value['price']) {
							$product_option_value_price = (float)$data['price'] - (float)$product_option_value['price'];
							$product_option_value_price_prefix = '-';
						} else {
							$product_option_value_price = (float)$product_option_value['price'] - (float)$data['price'];
							$product_option_value_price_prefix = '+';
						}
					} else {
						$product_option_value_price = (float)$product_option_value['price'];
						$product_option_value_price_prefix = $product_option_value['price_prefix'] == 'as' ? '+' : $product_option_value['price_prefix'];
					}

					if ($product_option_value['points_prefix'] == 'as' && (float)$product_option_value['points'] > 0) {
						if ((int)$data['points'] > (int)$product_option_value['points']) {
							$product_option_value_points = (int)$data['points'] - (int)$product_option_value['points'];
							$product_option_value_points_prefix = '-';
						} else {
							$product_option_value_points = (int)$product_option_value['points'] - (int)$data['points'];
							$product_option_value_points_prefix = '+';
						}
					} else {
						$product_option_value_points = (int)$product_option_value['points'];
						$product_option_value_points_prefix = $product_option_value['points_prefix'] == 'as' ? '+' : $product_option_value['points_prefix'];
					}

					if ($product_option_value['weight_prefix'] == 'as' && (float)$product_option_value['weight'] > 0) {
						if ((float)$data['weight'] > (float)$product_option_value['weight']) {
							$product_option_value_weight = (float)$data['weight'] - (float)$product_option_value['weight'];
							$product_option_value_weight_prefix = '-';
						} else {
							$product_option_value_weight = (float)$product_option_value['weight'] - (float)$data['weight'];
							$product_option_value_weight_prefix = '+';
						}
					} else {
						$product_option_value_weight = (float)$product_option_value['weight'];
						$product_option_value_weight_prefix = $product_option_value['weight_prefix'] == 'as' ? '+' : $product_option_value['weight_prefix'];
					}

					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value_price . "', price_prefix = '" . $this->db->escape($product_option_value_price_prefix) . "', points = '" . (int)$product_option_value_points . "', points_prefix = '" . $this->db->escape($product_option_value_points_prefix) . "', weight = '" . (float)$product_option_value_weight . "', weight_prefix = '" . $this->db->escape($product_option_value_weight_prefix) . "'");

					$this->db->query("INSERT INTO " . DB_PREFIX . "may_advanced_option_product_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', combination_id = '" . $combination_id . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', model = '" . $this->db->escape($product_option_value['model']) . "', sku = '" . $this->db->escape($product_option_value['sku']) . "', upc = '" . $product_option_value['upc'] . "', ean = '" . $product_option_value['ean'] . "', jan = '" . $product_option_value['jan'] . "', isbn = '" . $product_option_value['isbn'] . "', mpn = '" . $product_option_value['mpn'] . "', location = '" . $this->db->escape($product_option_value['location']) . "', image = '" . json_encode($images) . "', price = '" . $product_option_value['price'] . "', price_prefix = '" . $product_option_value['price_prefix'] . "', point = '" . $product_option_value['points'] . "', point_prefix = '" . $product_option_value['points_prefix'] . "', weight = '" . $product_option_value['weight'] . "', weight_prefix = '" . $product_option_value['weight_prefix'] . "', dimension_l = '" . $product_option_value['dimension_l'] . "', dimension_w = '" . $product_option_value['dimension_w'] . "', dimension_h = '" . $product_option_value['dimension_h'] . "', hide = '" . $product_option_value['hide'] . "', quantity = '" . $product_option_value['quantity'] . "', stock_status_id = '" . (int)$product_option_value['stock_status_id'] . "'");

					if (!$product_option_value['hide']) {
						$tags = array_merge($tags, array(
							$product_option_value['model'],
							$product_option_value['sku'],
							$product_option_value['upc'],
							$product_option_value['ean'],
							$product_option_value['jan'],
							$product_option_value['isbn'],
							$product_option_value['mpn'],
						));
					}
				}
			}

			if ($subtract) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '" . $total_quantity . "' WHERE product_id = '" . (int)$product_id . "'");
			}

			$tags = implode(',', array_filter(array_unique($tags)));
			if (!empty($tags)) {
				foreach ($data['product_description'] as $language_id => $value) {
					$this->db->query("UPDATE " . DB_PREFIX . "product_description SET tag = '" . $this->db->escape($value['tag'] . ':::' . $tags) . "' WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'");
				}
			}
		}
	}

	public function deleteProductAdvancedOptions($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "may_advanced_option_product_config WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "may_advanced_option_product_value WHERE product_id = '" . (int)$product_id . "'");
	}

	public function copyProductAdvancedOptions($source, $options) {
		$config = $this->db->query("SELECT * FROM " . DB_PREFIX . "may_advanced_option_product_config WHERE product_id = '" . (int)$source['product_id'] . "'")->row;
		$target = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE model = '" . $this->db->escape($source['model']) . "' AND image = '" . $this->db->escape($source['image']) . "' AND upc = '' AND sku = '' AND viewed = '0' AND status = '0' ORDER BY product_id DESC")->row;

		if (empty($config) || empty($target)) {
			return;
		}

		$product_id = $target['product_id'];

		$this->db->query("INSERT INTO " . DB_PREFIX . "may_advanced_option_product_config SET product_id = '" . (int)$product_id . "', option_name = '" . $this->db->escape($config['option_name']) . "', subtract = '" . $config['subtract'] . "', swatch_image = '" . (int)$config['swatch_image'] . "', show_first_option_in_list = '" . (int)$config['show_first_option_in_list'] . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		uasort($options, function($a, $b) {
			return ($a['product_option_id'] < $b['product_option_id']) ? -1 : 1;
		});

		foreach ($options as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (isset($product_option['product_option_value'])) {
					if ($product_option['value'] == 'may_advanced_option') {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', `value` = 'may_advanced_option', required = '" . (int)$product_option['required'] . "'");						
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
					}

					$product_option_id = $this->db->getLastId();

					foreach ($product_option['product_option_value'] as $product_option_value) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");

						if ($product_option['value'] == 'may_advanced_option') {
							$row = $this->db->query("SELECT * FROM " . DB_PREFIX . "may_advanced_option_product_value WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "' AND option_value_id = '" . (int)$product_option_value['option_value_id'] . "'")->row;

							if (isset($row) && !empty($row)) {
								$this->db->query("INSERT INTO " . DB_PREFIX . "may_advanced_option_product_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', combination_id = '" . $row['combination_id'] . "', option_value_id = '" . (int)$row['option_value_id'] . "', model = '" . $this->db->escape($row['model']) . "', sku = '" . $this->db->escape($row['sku']) . "', upc = '" . $row['upc'] . "', ean = '" . $row['ean'] . "', jan = '" . $row['jan'] . "', isbn = '" . $row['isbn'] . "', mpn = '" . $row['mpn'] . "', location = '" . $this->db->escape($row['location']) . "', image = '" . $this->db->escape($row['image']) . "', price = '" . $row['price'] . "', price_prefix = '" . $row['price_prefix'] . "', point = '" . $row['point'] . "', point_prefix = '" . $row['point_prefix'] . "', weight = '" . $row['weight'] . "', weight_prefix = '" . $row['weight_prefix'] . "', dimension_l = '" . $row['dimension_l'] . "', dimension_w = '" . $row['dimension_w'] . "', dimension_h = '" . $row['dimension_h'] . "', hide = '" . $row['hide'] . "', quantity = '" . $row['quantity'] . "', stock_status_id = '" . $row['stock_status_id'] . "'");
							}
						}	
					}
				}
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
			}
		}
	}

	public function getTotalStockSyncErrors() {
		return $this->db->query("SELECT maopv.product_id, maopv.product_option_id, maopv.option_value_id, maopv.quantity, pov.quantity, pov.subtract FROM " . DB_PREFIX . "may_advanced_option_product_value AS maopv LEFT JOIN " . DB_PREFIX . "product_option_value AS pov ON maopv.product_option_id = pov.product_option_id AND maopv.option_value_id = pov.option_value_id WHERE pov.subtract = 1 AND maopv.quantity != pov.quantity")->num_rows;
	}

	public function syncProductAdvancedOptionsStock() {
		$query = $this->db->query("SELECT maopv.product_id, maopv.product_option_id, maopv.option_value_id, maopv.quantity AS mao_quantity, pov.quantity, pov.subtract FROM " . DB_PREFIX . "may_advanced_option_product_value AS maopv LEFT JOIN " . DB_PREFIX . "product_option_value AS pov ON maopv.product_option_id = pov.product_option_id AND maopv.option_value_id = pov.option_value_id WHERE pov.subtract = 1 AND maopv.quantity != pov.quantity");

		foreach ($query->rows as $row) {
			$this->db->query("UPDATE " . DB_PREFIX . "may_advanced_option_product_value SET quantity = '" . (int)$row['quantity'] . "' WHERE product_option_id = '" . (int)$row['product_option_id'] . "' AND option_value_id = '" . (int)$row['option_value_id'] . "'");
		}
	}
}