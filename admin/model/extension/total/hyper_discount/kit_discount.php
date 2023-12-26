<?php
class ModelExtensionTotalHyperDiscountKitDiscount extends Model {
    public function getKitDiscountsList() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_kit` ORDER BY id");
        return $query->rows;
    }

    public function cloneKitEditor($discount_id, $editor_id) {
        $query = $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_kit_discount_editor`(`discount_id`, `editor_id`, `discount_count_products`, `discount_accumulation_sum`, `discount_function`, `discount_percent`, `discount_type`, `start_date`, `end_date`, `products_all`, `products`, `discount_variant_discount`, `discount_variant_condition`, `discount_variant_specials`, `discount_variant_options`, `status`, `products_filter_url`, `products2`, `type_qty`, `discount_count_products2`, `products_filter_url2`) 
        SELECT `discount_id`, `editor_id`, `discount_count_products`, `discount_accumulation_sum`, `discount_function`, `discount_percent`, `discount_type`, `start_date`, `end_date`, `products_all`, `products`, `discount_variant_discount`, `discount_variant_condition`, `discount_variant_specials`, `discount_variant_options`, `status`, `products_filter_url`, `products2`, `type_qty`, `discount_count_products2`, `products_filter_url2`
        FROM `" . DB_PREFIX . "hd_kit_discount_editor`
        WHERE `editor_id` = '$editor_id' && `discount_id`='$discount_id'");
        $id = $this->db->getLastId();
        $query = $this->db->query("SELECT `editor_id` FROM `" . DB_PREFIX . "hd_kit_discount_editor` WHERE `discount_id`='$discount_id' ORDER BY editor_id DESC LIMIT 0, 1");
        $new_editor_id = (int)$query->row['editor_id'] + 1;
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_kit_discount_editor` SET `editor_id` = '$new_editor_id' WHERE `id`='$id'");
    }

    public function addKitDiscount($kit_discount) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_kit` SET `name` = '" . $this->db->escape(json_encode($kit_discount['name'])) . "', `description` = '" . $this->db->escape($kit_discount['description']) . "'");
        
        $discount_id = $this->db->getLastId();
        
        return $discount_id;
    }

    public function deleteKitDiscount($kit_discount_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "hd_kit WHERE id = '" . (int)$kit_discount_id . "'");
    }

    public function getKitDiscount($kit_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_kit` WHERE `id` = '" . (int)$kit_discount_id . "'");
        return $query->row;
    }

    public function getKitDiscountEditors($kit_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_kit_discount_editor` WHERE `discount_id` = '" . (int)$kit_discount_id . "' ORDER BY id");
        return $query->rows;
    }

    public function editKitDiscount($kit_discount) {
        $save_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_kit_discount_editor WHERE  `discount_id` = '" . (int)$kit_discount['discount_id'] . "'");
        foreach ($query->rows as $editor_val) {
            $save_data[$editor_val['id']] = array(
                'products_filter_url'   => $editor_val['products_filter_url'],
                'products'              => $editor_val['products'],
                'products_filter_url2'  => $editor_val['products_filter_url2'],
                'products2'             => $editor_val['products2'],
            );
        }
        
        $this->db->query("DELETE FROM `" . DB_PREFIX . "hd_kit_discount_editor` WHERE discount_id = '" . (int)$kit_discount['discount_id'] . "'");
        
        if (isset($kit_discount['discount_editor'])) {
            foreach ($kit_discount['discount_editor'] as $editor_key => $editor_val) {
                $discount_percent = $editor_val['discount_percent'];
                $start_date = (!empty($editor_val['start_date'])) ? "'" . ($editor_val['start_date']) . "'" : 'null';
                $end_date = (!empty($editor_val['end_date'])) ? "'" . ($editor_val['end_date']) . "'" : 'null';

                $status = isset($editor_val['status']) ? 1 : null;
                $product_group = isset($editor_val['products_all']) ? 1 : null;
                $discount_count_products = $editor_val['discount_count_products'];
                $discount_count_products2 = $editor_val['discount_count_products2'];
                $type_qty = isset($editor_val['type_qty']) ? (int)$editor_val['type_qty'] : 0;

                if (empty($editor_val['id']))$editor_val['id'] = 0;
                
                if (!empty($save_data[$editor_val['id']])) {
                    $products_filter_url = $save_data[$editor_val['id']]['products_filter_url'];
                    $products = $save_data[$editor_val['id']]['products'];
                    $products_filter_url2 = $save_data[$editor_val['id']]['products_filter_url2'];
                    $products2 = $save_data[$editor_val['id']]['products2'];
                } else {
                    $products_filter_url = '';
                    $products = '';
                    $products_filter_url2 = '';
                    $products2 = '';
                }
                
                $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_kit_discount_editor` SET
                  `id` = '" . (int)$editor_val['id'] . "',
                  `products_filter_url` = '" . $this->db->escape($products_filter_url) . "',
                  `products` = '" . $this->db->escape($products) . "',
                  `products_filter_url2` = '" . $this->db->escape($products_filter_url2) . "',
                  `products2` = '" . $this->db->escape($products2) . "',
                  `discount_id` = '" . (int)$kit_discount['discount_id'] . "',
                  `editor_id` = '" . (int)$editor_val['editor_id'] . "',
                  `discount_count_products` = '" . $discount_count_products . "',
                  `discount_count_products2` = '" . $discount_count_products2 . "',
                  `type_qty` = '$type_qty',
                  `discount_percent` = '" . $discount_percent . "',
                  `discount_type` = '" . $this->db->escape($editor_val['discount_type']) . "',
                  `start_date` = " .  $start_date . ",
                  `end_date` = " . $end_date . ",
                  `products_all` = '" . $product_group . "',
                  `discount_variant_discount` = '" . $this->db->escape(isset($editor_val['discount_variant_discount']) ? $editor_val['discount_variant_discount'] : '') . "',
                  `discount_variant_condition` = '" . $this->db->escape(isset($editor_val['discount_variant_condition']) ? $editor_val['discount_variant_condition'] : '') . "',
                  `discount_variant_specials` = '" . $this->db->escape(isset($editor_val['discount_variant_specials']) ? $editor_val['discount_variant_specials'] : '') . "',
                  `discount_variant_options` = '" . $this->db->escape(isset($editor_val['discount_variant_options']) ? $editor_val['discount_variant_options'] : '') . "',
                  `status` = '" . $status . "'");
            }
        }

        $shops_all = isset($kit_discount['shops_all']) ? (int)$kit_discount['shops_all'] : null;
        $geos_alls = isset($kit_discount['geos_all']) ? (int)$kit_discount['geos_all'] : null;
        $correction = isset($kit_discount['correction']) ? (int)$kit_discount['correction'] : null;
        $customers_all = isset($kit_discount['customers_all']) ? (int)$kit_discount['customers_all'] : null;
        $guests = isset($kit_discount['guests']) ? (int)$kit_discount['guests'] : null;


        $this->db->query("UPDATE `" . DB_PREFIX . "hd_kit` SET
        `name` = '" . $this->db->escape(json_encode($kit_discount['name'])) . "',
        `description` = '" . $this->db->escape($kit_discount['description']) . "',
        `shops_all` = '" . $shops_all . "',
        `geos_all` = '" . $geos_alls . "',
        `customers_all` = '" . $customers_all . "',
        `guests` = '" . $guests . "',
        `correction`='$correction' WHERE `id`='" . (int)$kit_discount['discount_id'] . "'");
    }

    public function editClientGroups($discount_id, $groups, $url) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_kit` SET `customers_filter_url`='$url',`customers` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getClientGroups($discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_kit` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['customers'] && !empty($query->row['customers'])) {
            return json_decode($query->row['customers'], true);
        } else {
            return array();
        }
    }

    public function getGroupIdDiscount($discount_id) {
        $query = $this->db->query("SELECT `discount_id` FROM `" . DB_PREFIX . "hd_kit_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");
        if($query->num_rows)
            return $query->row['discount_id'];
        else
            return false;
    }

    public function getProductFilterUrl($discount_id) {
        $query = $this->db->query("SELECT products_filter_url FROM `" . DB_PREFIX . "hd_kit_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['products_filter_url'])
            return $query->row['products_filter_url'];
        else
            return false;
    }

    public function getProductFilterUrl2($discount_id) {
        $query = $this->db->query("SELECT products_filter_url2 FROM `" . DB_PREFIX . "hd_kit_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['products_filter_url2'])
            return $query->row['products_filter_url2'];
        else
            return false;
    }

    public function getClientFilterUrl($discount_id) {
        $query = $this->db->query("SELECT `customers_filter_url` FROM `" . DB_PREFIX . "hd_kit` WHERE `id` = '" . (int)$discount_id . "'");

        if (isset($query->row['customers_filter_url']))
            return $query->row['customers_filter_url'];
        else
            return false;
    }

    public function ClearGroupProducts($discount_id) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_kit_discount_editor` SET `products` = '' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getEditorProducts($discount_id) {
        $query = $this->db->query("SELECT products FROM `" . DB_PREFIX . "hd_kit_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['products']) {
            return json_decode($query->row['products'], true);
        } else {
            return array();
        }
    }

    public function editEditorProducts($discount_id, $groups, $url) {
        $query = $this->db->query("SELECT `discount_id` FROM `" . DB_PREFIX . "hd_kit_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row) {
            $this->db->query("UPDATE `" . DB_PREFIX . "hd_kit_discount_editor` SET `products_filter_url`='$url', `products` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
        } else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_kit_discount_editor` SET `products_filter_url`='$url', `id` = '" . (int)$discount_id . "', `products` = '" . $this->db->escape(json_encode($groups)) . "'");
        }
    }

    public function editEditorProducts2($discount_id, $groups, $url) {
        $query = $this->db->query("SELECT `discount_id` FROM `" . DB_PREFIX . "hd_kit_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row) {
            $this->db->query("UPDATE `" . DB_PREFIX . "hd_kit_discount_editor` SET `products_filter_url2`='$url', `products2` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
        } else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_kit_discount_editor` SET `products_filter_url2`='$url', `id` = '" . (int)$discount_id . "', `products2` = '" . $this->db->escape(json_encode($groups)) . "'");
        }
    }

    public function getEditorProducts2($discount_id) {
        $query = $this->db->query("SELECT products2 AS products FROM `" . DB_PREFIX . "hd_kit_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['products']) {
            return json_decode($query->row['products'], true);
        } else {
            return array();
        }
    }

    public function editShopsList($discount_id, $groups) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_kit` SET `shops` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getShopsList($discount_id) {
        $query = $this->db->query("SELECT shops FROM `" . DB_PREFIX . "hd_kit` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['shops']) {
            return json_decode($query->row['shops'], true);
        } else {
            return array();
        }
    }

    public function getGeoList($discount_id) {
        $query = $this->db->query("SELECT geos FROM `" . DB_PREFIX . "hd_kit` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['geos']) {
            return json_decode($query->row['geos'], true);
        } else {
            return array();
        }
    }

    public function editGeoList($discount_id, $groups) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_kit` SET `geos_all`='0',`geos` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }
}
