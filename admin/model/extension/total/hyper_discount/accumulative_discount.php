<?php
class ModelExtensionTotalHyperDiscountAccumulativeDiscount extends Model {

    public function getAccumulativeDiscountsList() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_accumulative` ORDER BY id");
        return $query->rows;
    }

    public function addAccumulativeDiscount($accumulative_discount) {
        $this->load->model('extension/total/hyper_discount/setting');
        $settings = $this->model_extension_total_hyper_discount_setting->getSetting();

        $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_accumulative` SET `name` = '" . $this->db->escape(json_encode($accumulative_discount['name'])) . "', `description` = '" . $this->db->escape($accumulative_discount['description']) . "', `discount_variant_condition`='" . $settings['discount_variant_condition'] . "',`discount_variant_discount`='" . $settings['discount_variant_discount'] . "',`discount_variant_options`='" . $settings['discount_variant_options'] . "',`discount_variant_specials`='" . $settings['discount_variant_specials'] . "'");
        $discount_id = $this->db->getLastId();
        
        return $discount_id;
    }

    public function editAccumulativeDiscount($accumulative_discount) {
        $order_statuses         = isset($accumulative_discount['order_statuses']) ? $accumulative_discount['order_statuses'] : array();
        $accumulative_shops_all = isset($accumulative_discount['shops_all']) ? (int)$accumulative_discount['shops_all'] : null;
        $accumulative_geos_alls = isset($accumulative_discount['geos_all']) ? (int)$accumulative_discount['geos_all'] : null;
        $correction             = isset($accumulative_discount['correction']) ? (int)$accumulative_discount['correction'] : null;
        $start_date             = (!empty($accumulative_discount['start_date'])) ? "'" . ($accumulative_discount['start_date']) . "'" : 'null';
        $end_date               = (!empty($accumulative_discount['end_date'])) ? "'" . ($accumulative_discount['end_date']) . "'" : 'null';

        $discount_variant_condition = isset($accumulative_discount['discount_variant_condition']) ? $accumulative_discount['discount_variant_condition'] : '';
        $discount_variant_discount  = isset($accumulative_discount['discount_variant_discount']) ? $accumulative_discount['discount_variant_discount'] : '';
        $discount_variant_specials  = isset($accumulative_discount['discount_variant_specials']) ? $accumulative_discount['discount_variant_specials'] : '';
        $discount_variant_options   = isset($accumulative_discount['discount_variant_options']) ? $accumulative_discount['discount_variant_options'] : '';

        $products_all  = isset($accumulative_discount['products_all']) ? 1 : 0;
        $customers_all = isset($accumulative_discount['customers_all']) ? 1 : 0;

        $this->db->query("UPDATE `" . DB_PREFIX . "hd_accumulative` SET
        `name` = '" . $this->db->escape(json_encode($accumulative_discount['name'])) . "',
        `description` = '" . $this->db->escape($accumulative_discount['description']) . "',
        `order_statuses` = '" . $this->db->escape(json_encode($order_statuses)) . "',
        `correction` = '$correction',
        `shops_all` = '" . $accumulative_shops_all . "',
        `geos_all` = '" . $accumulative_geos_alls . "',
        `start_date` = $start_date,
        `end_date` = $end_date,

        `discount_variant_specials` = '$discount_variant_specials',
        `discount_variant_discount` = '$discount_variant_discount',
        `discount_variant_condition` = '$discount_variant_condition',
        `discount_variant_options` = '$discount_variant_options',

        `products_all` = '$products_all',
        `customers_all` = '$customers_all'
        WHERE `id` = '" . (int)$accumulative_discount['discount_id'] . "'");

        $this->db->query("DELETE FROM `" . DB_PREFIX . "hd_accumulative_discount_editor` WHERE discount_id = '" . (int)$accumulative_discount['discount_id'] . "'");
            
        if (isset($accumulative_discount['discount_editor'])) {
            foreach ($accumulative_discount['discount_editor'] as $editor_key => $editor_val) {
                $discount_percent           = (int) ($editor_val['discount_percent']);
                $start_date                 = (!empty($editor_val['start_date'])) ? "'" . ($editor_val['start_date']) . "'" : 'null';
                $end_date                   = (!empty($editor_val['end_date'])) ? "'" . ($editor_val['end_date']) . "'" : 'null';
                $discount_status_id         = (int)$editor_val['discount_status'];
                $discount_function_orders   = $editor_val['discount_function_orders'];
                $discount_function_products = $editor_val['discount_function_products'];
                $discount_function_sum      = $editor_val['discount_function_sum'];
                $discount_function          = $editor_val['discount_function'];
                $discount_percent           = $editor_val['discount_percent'];
                $discount_type              = $editor_val['discount_type'];
                $status                     = (isset($editor_val['status'])) ? 1 : 0;

                if (empty($editor_val['id']))$editor_val['id'] = 0;
                
                $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_accumulative_discount_editor` SET "
                    . "`id` = '" . (int)$editor_val['id'] . "', "
                    . "`discount_id` = '" . (int)$accumulative_discount['discount_id'] . "', "
                    . "`editor_id` = '" . $editor_key . "', "
                    . "`start_date`=$start_date,"
                    . "`end_date`=$end_date,"
                    . "`discount_status_id`='$discount_status_id',"
                    . "`discount_function_orders`='$discount_function_orders',"
                    . "`discount_function_products`='$discount_function_products',"
                    . "`discount_function_sum`='$discount_function_sum',"
                    . "`discount_function` = '$discount_function', "
                    . "`discount_percent` = '$discount_percent', "
                    . "`discount_type` = '$discount_type', "
                    . "`status` = '$status'"
                );
            }
        }
    }

    public function getAccumulativeDiscount($accumulative_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_accumulative` WHERE `id` = '" . (int)$accumulative_discount_id . "'");
        return $query->row;
    }

    public function getAccumulativeDiscountEditors($accumulative_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_accumulative_discount_editor` WHERE `discount_id` = '" . (int)$accumulative_discount_id . "' ORDER BY id");
        return $query->rows;
    }

    public function deleteAccumulativeDiscount($accumulative_discount_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "hd_accumulative WHERE id = '" . (int)$accumulative_discount_id . "'");
    }

    public function editClientGroups($discount_id, $groups, $url) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_accumulative` SET `customers_filter_url`='$url',`customers` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getClientGroups($discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_accumulative` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['customers'] && !empty($query->row['customers'])) {
            return json_decode($query->row['customers'], true);
        } else {
            return array();
        }
    }

    public function getProductFilterUrl($discount_id) {
        $query = $this->db->query("SELECT products_filter_url FROM `" . DB_PREFIX . "hd_accumulative` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['products_filter_url']) {
            return $query->row['products_filter_url'];
        } else {
            return false;
        }

    }

    public function getClientFilterUrl($discount_id) {
        $query = $this->db->query("SELECT `customers_filter_url` FROM `" . DB_PREFIX . "hd_accumulative` WHERE `id` = '" . (int)$discount_id . "';");

        if (isset($query->row['customers_filter_url'])) {
            return $query->row['customers_filter_url'];
        } else {
            return false;
        }

    }

    public function editDiscountEditorGroupProducts($discount_id, $groups) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_accumulative_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row) {
            $this->db->query("UPDATE `" . DB_PREFIX . "hd_accumulative_discount_editor` SET `product_group` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
        } else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_accumulative_discount_editor` SET `id` = '" . (int)$discount_id . "', `product_group` = '" . $this->db->escape(json_encode($groups)) . "'");
        }
    }

    public function ClearGroupProducts($discount_id) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_accumulative` SET `products` = '' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getEditorProducts($discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_accumulative_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if (isset($query->row['product_group'])) {
            return json_decode($query->row['product_group'], true);
        } else {
            return array();
        }
    }

    public function editProducts($discount_id, $groups, $url) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_accumulative` SET `products_filter_url`='$url',`products` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getAccumulativeProducts($discount_id) {
        $query = $this->db->query("SELECT products FROM `" . DB_PREFIX . "hd_accumulative` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['products']) {
            return json_decode($query->row['products'], true);
        } else {
            return array();
        }
    }

    public function editShopsList($discount_id, $groups) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_accumulative` SET `shops` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getShopsList($discount_id) {
        $query = $this->db->query("SELECT shops FROM `" . DB_PREFIX . "hd_accumulative` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['shops']) {
            return json_decode($query->row['shops'], true);
        } else {
            return array();
        }
    }

    public function getGeoList($discount_id) {
        $query = $this->db->query("SELECT geos FROM `" . DB_PREFIX . "hd_accumulative` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['geos']) {
            return json_decode($query->row['geos'], true);
        } else {
            return array();
        }
    }

    public function editGeoList($discount_id, $groups) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_accumulative` SET `geos_all`='0',`geos` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }
}
