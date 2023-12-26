<?php
class ModelExtensionTotalHyperDiscountUsersDiscount extends Model {
    public function getUsersDiscountsList() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_users` ORDER BY id");
        return $query->rows;
    }

    public function addUsersDiscount($users_discount) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_users` SET `name` = '" . $this->db->escape(json_encode($users_discount['name'])) . "', `description` = '" . $this->db->escape($users_discount['description']) . "'");
        
        $discount_id = $this->db->getLastId();
        
        return $discount_id;
    }

    public function editUsersDiscount($users_discount)
    {
        $users_customers_all     = isset($users_discount['customers_all']) ? $users_discount['customers_all'] : false;
        $users_geos_all          = isset($users_discount['geos_all']) ? $users_discount['geos_all'] : false;
        $users_discount_settings = isset($users_discount['shops_all']) ? $users_discount['shops_all'] : false;
        $users_guests            = isset($users_discount['guests']) ? $users_discount['guests'] : false;
        $correction              = isset($users_discount['correction']) ? (int)$users_discount['correction'] : null;

        $this->db->query("UPDATE `" . DB_PREFIX . "hd_users` SET `name` = '" . $this->db->escape(json_encode($users_discount['name'])) . "', "
            . " `description` = '" . $this->db->escape($users_discount['description']) . "', "
            . "`guests` = '" . $this->db->escape($users_guests) . "', "
            . " `geos_all` = '" . $this->db->escape($users_geos_all) . "', "
            . "`customers_all` = '" . $this->db->escape($users_customers_all) . "', "
            . " `shops_all` = '" . $this->db->escape($users_discount_settings) . "', "
            . " `correction` = '$correction' WHERE `id` = '" . (int)$users_discount['discount_id'] . "'");

            
        $save_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_users_discount_editor WHERE  `discount_id` = '" . (int)$users_discount['discount_id'] . "'");
        foreach ($query->rows as $editor_val) {
            $save_data[$editor_val['id']] = array(
                'products_filter_url'   => $editor_val['products_filter_url'],
                'products'              => $editor_val['products'],
            );
        }
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "hd_users_discount_editor WHERE `discount_id` = '" . (int)$users_discount['discount_id'] . "'");
        
        if (isset($users_discount['discount_editor'])) {
            foreach ($users_discount['discount_editor'] as $editor_key => $editor_val) {

                $discount_percent = ($editor_val['discount_percent']);
                $start_date       = (!empty($editor_val['start_date'])) ? "'" . ($editor_val['start_date']) . "'" : 'null';
                $end_date         = (!empty($editor_val['end_date'])) ? "'" . ($editor_val['end_date']) . "'" : 'null';

                $status        = (isset($editor_val['status'])) ? 1 : 0;
                $discount_type = $editor_val['discount_type'];
                $products_all  = (isset($editor_val['products_all'])) ? 1 : 0;

                $discount_variant_condition = isset($editor_val['discount_variant_condition']) ? $editor_val['discount_variant_condition'] : '';
                $discount_variant_discount  = isset($editor_val['discount_variant_discount']) ? $editor_val['discount_variant_discount'] : '';
                $discount_variant_specials  = isset($editor_val['discount_variant_specials']) ? $editor_val['discount_variant_specials'] : '';
                $discount_variant_options   = isset($editor_val['discount_variant_options']) ? $editor_val['discount_variant_options'] : '';
                
                if (empty($editor_val['id']))$editor_val['id'] = 0;
                
                if (!empty($save_data[$editor_val['id']])) {
                    $products_filter_url = $save_data[$editor_val['id']]['products_filter_url'];
                    $products = $save_data[$editor_val['id']]['products'];
                } else {
                    $products_filter_url = '';
                    $products = '';
                }
        
                $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_users_discount_editor` SET "
                    . "`id` = '" . (int)$editor_val['id'] . "', "
                    . "`products_filter_url` = '" . $this->db->escape($products_filter_url) . "', "
                    . "`products` = '" . $this->db->escape($products) . "', "
                    . "`discount_id` = '" . (int)$users_discount['discount_id'] . "', "
                    . "`editor_id` = '" . $editor_key . "', "
                    . "`start_date`=$start_date,"
                    . "`end_date`=$end_date,"
                    . "`discount_variant_condition`='$discount_variant_condition',"
                    . "`discount_variant_discount`='$discount_variant_discount',"
                    . "`discount_variant_specials`='$discount_variant_specials',"
                    . "`discount_variant_options`='$discount_variant_options',"
                    . "`products_all` = '$products_all', "
                    . "`discount_type` = '$discount_type', "
                    . "`status` = '$status', "
                    . "`discount_percent` = '$discount_percent'"
                );
            }
        }
    }

    public function getUsersDiscount($users_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_users` WHERE `id` = '" . (int)$users_discount_id . "'");
        return $query->row;
    }

    public function getUsersDiscountEditor($users_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_users_discount_editor` WHERE `discount_id` = '" . (int)$users_discount_id . "' ORDER BY id");
        return $query->rows;
    }

    public function deleteUsersDiscount($users_discount_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "hd_users_discount_editor WHERE `discount_id` = '" . (int)$users_discount_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "hd_users WHERE id = '" . (int)$users_discount_id . "'");
    }

    public function editClientGroups($discount_id, $groups, $url) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_users` SET `customers_filter_url` = '$url', `customers` = '" . $this->db->escape(json_encode($groups)) . "', `customers_all` = '0' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getClientGroups($discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_users` WHERE `id` = '" . (int)$discount_id . "'");

        if (!empty($query->row['customers'])) {
            return json_decode($query->row['customers'], true);
        } else {
            return array();
        }
    }

    public function getGroupIdDiscount($discount_id) {
        $query = $this->db->query("SELECT `discount_id` FROM `" . DB_PREFIX . "hd_users_discount_editor` WHERE `id` = '" . (int)$discount_id . "' ");
        if ($query->num_rows) {
            return $query->row['discount_id'];
        } else {
            return false;
        }

    }

    public function getProductFilterUrl($id) {
        $query = $this->db->query("SELECT `products_filter_url` FROM `" . DB_PREFIX . "hd_users_discount_editor` WHERE `id` = '" . (int)$id . "'");

        if (isset($query->row['products_filter_url'])) {
            return $query->row['products_filter_url'];
        } else {
            return false;
        }

    }

    public function getClientFilterUrl($discount_id) {
        $query = $this->db->query("SELECT `customers_filter_url` FROM `" . DB_PREFIX . "hd_users` WHERE `id` = '" . (int)$discount_id . "'");

        if (isset($query->row['customers_filter_url'])) {
            return $query->row['customers_filter_url'];
        } else {
            return false;
        }

    }

    public function ClearGroupProducts($id) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_users_discount_editor` SET `products` = '' WHERE  `id` = '" . (int)$id . "'");
    }

    public function getEditorProducts($id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_users_discount_editor` WHERE `id` = '" . (int)$id . "'");

        if ($query->row && $query->row['products']) {
            return json_decode($query->row['products'], true);
        } else {
            return array();
        }
    }

    public function editEditorProducts($id, $groups, $url) {
        $query = $this->db->query("SELECT `discount_id` FROM `" . DB_PREFIX . "hd_users_discount_editor` WHERE `id` = '" . (int)$id . "'");

        if ($query->row) {
            $this->db->query("UPDATE `" . DB_PREFIX . "hd_users_discount_editor` SET `products_filter_url` = '$url', `products` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$id . "'");
        } else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_users_discount_editor` SET `products_filter_url` = '$url', `id` = '" . (int)$id . "', `products` = '" . $this->db->escape(json_encode($groups)) . "'");
        }
    }

    public function editShopsList($discount_id, $groups) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_users` SET `shops` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getShopsList($discount_id) {
        $query = $this->db->query("SELECT shops FROM `" . DB_PREFIX . "hd_users` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['shops']) {
            return json_decode($query->row['shops'], true);
        } else {
            return array();
        }
    }

    public function getGeoList($discount_id) {
        $query = $this->db->query("SELECT geos FROM `" . DB_PREFIX . "hd_users` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['geos']) {
            return json_decode($query->row['geos'], true);
        } else {
            return array();
        }
    }

    public function editGeoList($discount_id, $groups) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_users` SET `geos_all` = '0', `geos` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

}
