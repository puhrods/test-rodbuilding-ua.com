<?php
class ModelExtensionTotalHyperDiscountQuantitativeDiscount extends Model {
    public function getQuantitativeDiscountsList() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_quantitative` ORDER BY id");
        return $query->rows;
    }

    public function addQuantitativeDiscount($quantitative_discount) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_quantitative` SET `name` = '" . $this->db->escape(json_encode($quantitative_discount['name'])) . "', `description` = '" . $this->db->escape($quantitative_discount['description']) . "'");
        
        $discount_id = $this->db->getLastId();
        
        return $discount_id;
    }

    public function deleteQuantitativeDiscount($quantitative_discount_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "hd_quantitative WHERE id = '" . (int)$quantitative_discount_id . "'");
    }

    public function getQuantitativeDiscount($quantitative_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_quantitative` WHERE `id` = '" . (int)$quantitative_discount_id . "'");
        return $query->row;
    }

    public function getQuantitativeDiscountEditors($quantitative_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_quantitative_discount_editor` WHERE `discount_id` = '" . (int)$quantitative_discount_id . "' ORDER BY id");
        return $query->rows;
    }

    public function editQuantitativeDiscount($quantitative_discount) {
        $save_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_quantitative_discount_editor WHERE  `discount_id` = '" . (int)$quantitative_discount['discount_id'] . "'");
        foreach ($query->rows as $editor_val) {
            $save_data[$editor_val['id']] = array(
                'products_filter_url'   => $editor_val['products_filter_url'],
                'products'              => $editor_val['products'],
            );
        }
        
        $this->db->query("DELETE FROM `" . DB_PREFIX . "hd_quantitative_discount_editor` WHERE discount_id = '" . (int)$quantitative_discount['discount_id'] . "'");
        
        if (isset($quantitative_discount['discount_editor'])) {
            foreach ($quantitative_discount['discount_editor'] as $editor_key => $editor_val) {
                $discount_percent = $editor_val['discount_percent'];
                $start_date       = (!empty($editor_val['start_date'])) ? "'" . ($editor_val['start_date']) . "'" : 'null';
                $end_date         = (!empty($editor_val['end_date'])) ? "'" . ($editor_val['end_date']) . "'" : 'null';

                $status                    = isset($editor_val['status']) ? 1 : null;
                $product_group             = isset($editor_val['products_all']) ? 1 : null;
                $discount_count_products   = $editor_val['discount_count_products'];
                $discount_accumulation_sum = $editor_val['discount_accumulation_sum'];

                if (empty($editor_val['id']))$editor_val['id'] = 0;
                
                if (!empty($save_data[$editor_val['id']])) {
                    $products_filter_url = $save_data[$editor_val['id']]['products_filter_url'];
                    $products = $save_data[$editor_val['id']]['products'];
                } else {
                    $products_filter_url = '';
                    $products = '';
                }
                
                $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_quantitative_discount_editor` SET
                  `id` = '" . (int)$editor_val['id'] . "',
                  `products_filter_url` = '" . $this->db->escape($products_filter_url) . "', 
                  `products` = '" . $this->db->escape($products) . "',
                  `discount_id` = '" . (int)$quantitative_discount['discount_id'] . "',
                  `editor_id` = '" . (int)$editor_val['editor_id'] . "',
                  `discount_count_products` = '" . $discount_count_products . "',
                  `discount_accumulation_sum` = '" . $discount_accumulation_sum . "',
                  `discount_function` = '" . $this->db->escape($editor_val['discount_function']) . "',
                  `discount_percent` = '" . $discount_percent . "',
                  `discount_type` = '" . $this->db->escape($editor_val['discount_type']) . "',
                  `start_date` = " . $start_date . ",
                  `end_date` = " . $end_date . ",
                  `products_all` = '" . $product_group . "',
                  `discount_variant_discount` = '" . $this->db->escape(isset($editor_val['discount_variant_discount']) ? $editor_val['discount_variant_discount'] : '') . "',
                  `discount_variant_condition` = '" . $this->db->escape(isset($editor_val['discount_variant_condition']) ? $editor_val['discount_variant_condition'] : '') . "',
                  `discount_variant_specials` = '" . $this->db->escape(isset($editor_val['discount_variant_specials']) ? $editor_val['discount_variant_specials'] : '') . "',
                  `discount_variant_options` = '" . $this->db->escape(isset($editor_val['discount_variant_options']) ? $editor_val['discount_variant_options'] : '') . "',
                  `status` = '" . $status . "'");
            }
        }
        //   $set_discount_editor = isset($quantitative_discount['discount_editor']) ? $this->db->escape(json_encode($quantitative_discount['discount_editor'])) : '';
        $shops_all = isset($quantitative_discount['shops_all']) ? (int)$quantitative_discount['shops_all'] : null;
        $geos_alls = isset($quantitative_discount['geos_all']) ? (int)$quantitative_discount['geos_all'] : null;

        $correction    = isset($quantitative_discount['correction']) ? (int)$quantitative_discount['correction'] : null;
        $customers_all = isset($quantitative_discount['customers_all']) ? (int)$quantitative_discount['customers_all'] : null;
        $guests        = isset($quantitative_discount['guests']) ? (int)$quantitative_discount['guests'] : null;
        //echo '<pre>'; print_r($quantitative_discount); die;
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_quantitative` SET
        `name` = '" . $this->db->escape(json_encode($quantitative_discount['name'])) . "',
        `description` = '" . $this->db->escape($quantitative_discount['description']) . "',
        `shops_all` = '" . $shops_all . "',
        `geos_all` = '" . $geos_alls . "',
        `correction` = '$correction',
        `customers_all` = '" . $customers_all . "',
        `guests` = '" . $guests . "'
        WHERE `id` = '" . (int)$quantitative_discount['discount_id'] . "'");
    }

    public function editClientGroups($discount_id, $groups, $url) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_quantitative` SET `customers_filter_url`='$url',`customers` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getClientGroups($discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_quantitative` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['customers'] && !empty($query->row['customers'])) {
            return json_decode($query->row['customers'], true);
        } else {
            return array();
        }
    }

    public function getGroupIdDiscount($discount_id) {
        $query = $this->db->query("SELECT `discount_id` FROM `" . DB_PREFIX . "hd_quantitative_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");
        
        if ($query->num_rows) {
            return $query->row['discount_id'];
        } else {
            return false;
        }

    }

    public function getProductFilterUrl($discount_id) {
        $query = $this->db->query("SELECT products_filter_url FROM `" . DB_PREFIX . "hd_quantitative_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['products_filter_url']) {
            return $query->row['products_filter_url'];
        } else {
            return false;
        }

    }

    public function getClientFilterUrl($discount_id) {
        $query = $this->db->query("SELECT `customers_filter_url` FROM `" . DB_PREFIX . "hd_quantitative` WHERE `id` = '" . (int)$discount_id . "'");

        if (isset($query->row['customers_filter_url'])) {
            return $query->row['customers_filter_url'];
        } else {
            return false;
        }

    }

    public function ClearGroupProducts($discount_id) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_quantitative_discount_editor` SET `products` = '' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getEditorProducts($discount_id) {
        $query = $this->db->query("SELECT products FROM `" . DB_PREFIX . "hd_quantitative_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['products']) {
            return json_decode($query->row['products'], true);
        } else {
            return array();
        }
    }

    public function editEditorProducts($discount_id, $groups, $url) {
        $query = $this->db->query("SELECT `discount_id` FROM `" . DB_PREFIX . "hd_quantitative_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row) {
            $this->db->query("UPDATE `" . DB_PREFIX . "hd_quantitative_discount_editor` SET `products_filter_url`='$url', `products` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
        } else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_quantitative_discount_editor` SET `products_filter_url`='$url', `id` = '" . (int)$discount_id . "', `products` = '" . $this->db->escape(json_encode($groups)) . "'");
        }
    }
    
    public function editShopsList($discount_id, $groups) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_quantitative` SET `shops` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getShopsList($discount_id){
        $query = $this->db->query("SELECT shops FROM `" . DB_PREFIX . "hd_quantitative` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['shops']) {
            return json_decode($query->row['shops'], true);
        } else {
            return array();
        }
    }

    public function getGeoList($discount_id) {
        $query = $this->db->query("SELECT geos FROM `" . DB_PREFIX . "hd_quantitative` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['geos']) {
            return json_decode($query->row['geos'], true);
        } else {
            return array();
        }
    }

    public function editGeoList($discount_id, $groups) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_quantitative` SET `geos_all`='0',`geos` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }
}
