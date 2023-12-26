<?php
class ModelExtensionTotalHyperDiscountAccamulationStatus extends Model {
    public function getAccamulationStatussList() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_accumulation_statuses` ");
        return $query->rows;
    }

    public function addAccamulationStatus($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_accumulation_statuses` SET `name` = '" . $this->db->escape(json_encode($data['name'])) . "', `description` = '" . $this->db->escape(json_encode($data['description'])) . "'");
    }
    
    public function editAccamulationStatus($id, $data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_accumulation_statuses` SET `name` = '" . $this->db->escape(json_encode($data['name'])) . "', `description` = '" . $this->db->escape(json_encode($data['description'])) . "' WHERE `id` = '" . (int)$id . "'");
    }
    
    public function getAccamulationStatus($id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_accumulation_statuses` WHERE `id` = '" . (int)$id . "'");
        return $query->row;
    }

    public function deleteAccamulationStatus($id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "hd_accumulation_statuses WHERE id = '" . (int)$id . "'");
    }
}
