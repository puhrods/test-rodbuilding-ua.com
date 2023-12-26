<?php
class ModelExtensionTotalHyperDiscountCustomer extends Model {
    private function getFilter($data = array()) {
        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_email'])) {
            $implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
        }

        if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
            $implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
        }

        if (!empty($data['filter_customer_group_id'])) {
            $filtered_id = [];
            foreach ($data['filter_customer_group_id'] as $customer_id) {
                $filtered_id[] = (int)$customer_id;
            }
            $implode[] = "c.customer_group_id IN(" . implode(',', $filtered_id) . ")";
        }

        if (!empty($data['filter_ip'])) {
            $implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            return implode(" AND ", $implode);
        } else {
            return '';
        }
    }

    public function getCustomers($data = array()) {
        $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        $filter = $this->getFilter($data);
        if ($filter) {
            $sql .= " AND " . $filter;
        }

        $sort_data = array(
            'name',
            'c.email',
            'customer_group',
            'c.status',
            'c.ip',
            'c.date_added'
        );
        
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) && isset($data['limit'])) {
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

    public function getTotalCustomers($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer c";

        $filter = $this->getFilter($data);
        if ($filter) {
            $sql .= " WHERE " . $filter;
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
        
    public function getCustomersId($data = array()) {
        $sql = "SELECT GROUP_CONCAT(DISTINCT c.customer_id ORDER BY c.customer_id ASC SEPARATOR ',') AS customers FROM " . DB_PREFIX . "customer c";

        $filter = $this->getFilter($data);
        if ($filter) {
            $sql .= " WHERE " . $filter;
        }
        
        $query = $this->db->query($sql);
        
        if ($query->row['customers']) {
            return explode(',', $query->row['customers']);
        } else {
            return [];
        }
    }
}
