<?php
class ModelExtensionModuleModificationManager extends Model {
	public function addModification($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET date_modified = NOW(), code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', author = '" . $this->db->escape($data['author']) . "', version = '" . $this->db->escape($data['version']) . "', link = '" . $this->db->escape($data['link']) . "', xml = '" . $this->db->escape($data['xml']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
	}

	public function deleteModification($modification_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE modification_id = '" . (int)$modification_id . "'");
	}

	public function enableModification($modification_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "modification SET status = '1', date_modified = NOW() WHERE modification_id = '" . (int)$modification_id . "'");
	}

	public function disableModification($modification_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "modification SET status = '0', date_modified = NOW() WHERE modification_id = '" . (int)$modification_id . "'");
	}


	public function editModification($modification_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "modification SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', author = '" . $this->db->escape($data['author']) . "', version = '" . $this->db->escape($data['version']) . "', link = '" . $this->db->escape($data['link']) . "', xml = '" . $this->db->escape($data['xml']) . "', date_modified = NOW() WHERE modification_id = '" . (int)$modification_id . "'");
	}

	public function getModification($modification_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE modification_id = '" . (int)$modification_id . "'");

		return $query->row;
	}

	public function getModifications($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "modification";

		$cond = array();

		if (!empty($data['filter_name'])) {
			$cond[] = " `name` LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_xml'])) {
			$cond[] = " `xml` LIKE '%" . $this->db->escape($data['filter_xml']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$cond[] = " `author` LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if ($cond) {
			$sql .= " WHERE " . implode(' AND ', $cond);
		}

		$sort_data = array(
			'date_modified' => 'GREATEST(COALESCE(date_modified, 0), COALESCE(date_added, 0))',
			'name' => 'name',
			'author' => 'author',
			'version' => 'version',
			'status' => 'status',
			'date_added' => 'date_added'
		);

		if (isset($data['sort']) && isset($sort_data[$data['sort']])) {
			$sql .= " ORDER BY " . $sort_data[$data['sort']];
		} else {
			$sql .= " ORDER BY date_modified";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
		}

		if (!isset($data['sort']) || $data['sort'] == 'date_modified') {
			$sql .= ", date_added";

			if (isset($data['order']) && ($data['order'] == 'ASC')) {
				$sql .= " ASC";
			} else {
				$sql .= " DESC";
			}
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

	public function getTotalModifications($data = array()) {

		$cond = array();

		if (!empty($data['filter_name'])) {
			$cond[] = " `name` LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_xml'])) {
			$cond[] = " `xml` LIKE '%" . $this->db->escape($data['filter_xml']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$cond[] = " `author` LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
		}

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "modification";

		if ($cond) {
			$sql .= " WHERE " . implode(' AND ', $cond);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getModificationByCode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	public function install() {
		$data = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "modification`");
		$column_names = array();

		foreach ($data->rows as $row) {
			$column_names[] = $row['Field'];

			switch($row['Field']) {
				case 'xml' && strtoupper($row['Type']) == 'TEXT':
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "modification` CHANGE `xml` `xml` MEDIUMTEXT NOT NULL");
					break;
			}
		}

		if (!in_array('date_modified', $column_names)) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "modification` ADD COLUMN  `date_modified` datetime");

			$this->db->query("UPDATE `" . DB_PREFIX . "modification` SET `date_modified` = `date_added` WHERE `date_modified` = '0000-00-00 00:00:00'");
		}
	}
}
