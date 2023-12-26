<?php
class ModelExtensionTotalHyperDiscountProductGroups extends Model {
    public function getProductsForGen() {
        $product_data = array();

        $sql   = "SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'cost'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            $sql = "SELECT product_id,price,cost FROM `" . DB_PREFIX . "product`";
        } else {
            $sql = "SELECT product_id,price FROM `" . DB_PREFIX . "product`";
        }

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $product_data[$result['product_id']] = $result;
        }

        return $product_data;
    }
    
    public function getMax($column,$data) {
        $sql = "SELECT MAX(p." . $this->db->escape($column) . ") AS value FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) ";

        if (!empty($data['filter_category']))
        {
            $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
        }

        $sql .= " WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";


        if (!empty($data['filter_name']))
        {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_model']))
        {
            $sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
        }

        if (isset($data['filter_suppler']) && !is_null($data['filter_suppler']))
        {
            $parts = explode(',', $data['filter_suppler']);
            $i = 0;
            foreach ($parts as $p)
            {
                if (!$i)
                    $sql .= " AND ( ";
                else
                    $sql .= " OR ";

                $p = trim($p);
                $sql .= "p.model LIKE '%$p'";

                $i++;

                if (count($parts) == $i)
                    $sql .= " ) ";
            }
        }


        if (!empty($data['filter_manufacturer']))
        {
            $sql .= " AND p.manufacturer_id IN (" . $this->db->escape($data['filter_manufacturer']) . ")";
        }

        if (isset($data['filter_price']) && !is_null($data['filter_price']))
        {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity']))
        {
            $sql .= " AND p.quantity = '" . (int) $data['filter_quantity'] . "'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status']))
        {
            $sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
        }


        if (!empty($data['filter_category']))
        {
            if (!empty($data['filter_sub_category']))
            {
                $implode_data = array();

                $implode_data[] = "category_id = '" . (int) $data['filter_category'] . "'";

                $this->load->model('catalog/category');

                $categories = $this->model_catalog_category->getCategories($data['filter_category']);

                foreach ($categories as $category)
                {
                    $implode_data[] = "p2c.category_id = '" . (int) $category['category_id'] . "'";
                }

                $sql .= " AND (" . implode(' OR ', $implode_data) . ")";
            } else
            {
                $sql .= " AND p2c.category_id IN (" . $data['filter_category'] . ")";
            }
        }

        $query = $this->db->query($sql);

        return $query->row['value'];
    }

    public function getMax2($column) {
        $query = $this->db->query("SELECT MAX(" . $this->db->escape($column) . ") AS value FROM `" . DB_PREFIX . "product`");
        return $query->row['value'];
    }
    
    private function getFilter($data) {
        $sql = '';
        
        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['slider_price_min_val'])) {
            $sql .= " AND p.price >= '" . (float)$data['slider_price_min_val'] . "'";
        }

        if (isset($data['slider_price_max_val'])) {
            $sql .= " AND p.price <= '" . (float)$data['slider_price_max_val'] . "'";
        }

        if (isset($data['slider_qty_min_val'])) {
            $sql .= " AND p.quantity >= '" . (int)$data['slider_qty_min_val'] . "'";
        }

        if (isset($data['slider_qty_max_val'])) {
            $sql .= " AND p.quantity <= '" . (int)$data['slider_qty_max_val'] . "'";
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
        }

        if (!empty($data['filter_manufacturer'])) {
            $sql .= " AND p.manufacturer_id IN (" . $this->db->escape($data['filter_manufacturer']) . ")";
        }

        if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sql .= " AND p.quantity = '" . (int) $data['filter_quantity'] . "'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
        }

        if (!empty($data['filter_category'])) {
            $sql .= " AND p2c.category_id IN (" . $this->db->escape($data['filter_category']) . ")";
        }
        
        return $sql;
    }
    
    public function getProducts($data = array()) {
        $sql = "SELECT *, p.image, md.name AS manufacturer_name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "manufacturer md ON (p.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

        if (!empty($data['filter_category']))
        {
            $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
        }

        $sql .= " WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        $sql .= $this->getFilter($data);

        $sql .= " GROUP BY p.product_id";

        $sort_data = array(
            'pd.name',
            'p.model',
            'md.name',
            'p.price',
            'p.quantity',
            'p.status',
            'p.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY pd.name";
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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }
        
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalProducts($data = array()) {
        $sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";

        $sql .= " WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        $sql .= $this->getFilter($data);

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getProductsId($data = array()) {
        $sql = "SELECT DISTINCT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";

        $sql .= " WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";
        
        $sql .= $this->getFilter($data);
        
        $query = $this->db->query($sql);
        
        if ($query->rows) {
            $products = [];
            foreach ($query->rows as $product) {
                $products[] = $product['product_id'];
            }
            return $products;
        } else {
            return [];
        }
    }
    
    public function getCategories($data = array()) {
        $sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order, c1.status,(select count(product_id) as product_count from " . DB_PREFIX . "product_to_category pc where pc.category_id = c1.category_id) as product_count FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int) $this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND cd2.category_id IN (" . $data['filter_category'] . ")";
        }

        $sql .= " GROUP BY cp.category_id";

        $sort_data = array(
            'product_count',
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order";
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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

}
