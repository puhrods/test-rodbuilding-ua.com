<?php
class ModelExtensionMayAdvancedOptionsTemplate extends Model {

	public function addOption($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "may_advanced_option_template` SET option_name = '" . $this->db->escape($data['advanced_option_name']) . "', children = '" . implode(",", array_keys($data['option_values'])) . "', swatch_image = '" . (int)$data['swatch_image'] . "', show_first_option_in_list = '" . (int)$data['show_first_option_in_list'] . "', sort_order = '" . (int)$data['sort_order'] . "', content = '" . json_encode($data['option_values'], JSON_UNESCAPED_UNICODE) . "'");

		$option_id = $this->db->getLastId();

		return $option_id;
	}

	public function editOption($option_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "may_advanced_option_template` SET option_name = '" . $this->db->escape($data['advanced_option_name']) . "', children = '" . implode(",", array_keys($data['option_values'])) . "', swatch_image = '" . (int)$data['swatch_image'] . "', show_first_option_in_list = '" . (int)$data['show_first_option_in_list'] . "', sort_order = '" . (int)$data['sort_order'] . "', content = '" . json_encode($data['option_values'], JSON_UNESCAPED_UNICODE) . "' WHERE option_id = '" . (int)$option_id . "'");
	}

	public function updateOptionContent($option_id, $content) {
		if (is_array($content)) {
			$content = json_encode($content, JSON_UNESCAPED_UNICODE);
		}

		$this->db->query("UPDATE `" . DB_PREFIX . "may_advanced_option_template` SET content = '" . $content . "' WHERE option_id = '" . (int)$option_id . "'");
	}

	public function deleteOption($option_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "may_advanced_option_template` WHERE option_id = '" . (int)$option_id . "'");
	}

	public function deleteOptionByChild($child_option_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "may_advanced_option_template` WHERE FIND_IN_SET(" . (int)$child_option_id . ", children)");
	}

	public function deleteChildOptionValues($option, $child_option_value_ids) {
		if (!is_array($option) && is_numeric($option)) {
			$option = $this->getOption($option);
		}

		if ($option['option_id']) {
			$content = json_decode($option['content'], true);
			$content = $this->walk_recursive_remove($content, $child_option_value_ids, true);
			$this->updateOptionContent($option['option_id'], $content);
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "may_advanced_option_product_value` WHERE `combination_id` LIKE '" . $option['option_id'] . "-%'");
		foreach ($query->rows as $row) {
			foreach (explode('-', $row['combination_id']) as $index => $option_value_id) {
				if (!$index || !in_array($option_value_id, $child_option_value_ids)) {
					continue;
				}

				$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option_value` WHERE product_option_id = '" . (int)$row['product_option_id'] . "'");
				$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option` WHERE product_option_id = '" . (int)$row['product_option_id'] . "'");
				$this->db->query("DELETE FROM `" . DB_PREFIX . "may_advanced_option_product_value` WHERE product_option_id = '" . (int)$row['product_option_id'] . "'");

				break;
			}

			foreach ($child_option_value_ids as $option_value_id) {
				$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option_value` WHERE product_id = '" . (int)$row['product_id'] . "' AND option_value_id = '" . $option_value_id . "'");
				$this->db->query("DELETE FROM `" . DB_PREFIX . "may_advanced_option_product_value` WHERE product_id = '" . (int)$row['product_id'] . "' AND option_value_id = '" . $option_value_id . "'");
			}
		}
	}

	public function getOption($option_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "may_advanced_option_template` WHERE option_id = '" . (int)$option_id . "'");

		return $query->row;
	}

	public function getOptionsByChild($child_option_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "may_advanced_option_template` WHERE FIND_IN_SET(" . (int)$child_option_id . ", children)");

		return $query->rows;
	}

	public function getOptions($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "may_advanced_option_template`";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE option_name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'option_name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY option_name";
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

	public function getOptionContent($option_id) {
		$query = $this->db->query("SELECT content FROM `" . DB_PREFIX . "may_advanced_option_template` WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['content'];
	}

	public function getChildren($option_id) {
		$query = $this->db->query("SELECT children FROM `" . DB_PREFIX . "may_advanced_option_template` WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['children'];
	}

	public function getTotalOptions() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "may_advanced_option_template`");

		return $query->row['total'];
	}

	protected function walk_recursive_remove (array $array, $values_to_remove, $key_check = false, $value_check = true) {
		$is_flat_array = true;
		foreach ($array as $k => $v) {
			if ($key_check && in_array($k, $values_to_remove) && (!$value_check || is_array($v))) {
				unset($array[$k]);
				continue;
			}
	
			if (is_array($v)) {
				$is_flat_array = false;
				$array[$k] = $this->walk_recursive_remove($v, $values_to_remove, $key_check, $value_check);
			} else {
				if ($value_check && in_array($v, $values_to_remove)) {
					unset($array[$k]);
				}
			}
		}
	
		if ($value_check && $is_flat_array) {
			$array = array_values($array);
		}
	
		return $array;
	}
}
