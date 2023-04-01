<?php 
class ModelSaleOrderprotel extends Model {
	public function getTelCustomers($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name']) && !empty($data['filter_email']) && !empty($data['filter_phone'])) {
			$sql .= " AND (LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			$sql .= " OR LCASE(c.email) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "%'";
			$sql .= " OR c.telephone LIKE '%" . $this->db->escape($data['filter_phone']) . "%')";
		}
		
		if (!empty($data['filter_status'])) {
			$sql .= " AND c.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " ORDER BY name ASC LIMIT 0,10";

		$query = $this->db->query($sql);

		return $query->rows;
	}
}
?>