<?php
class ModelExtensionTotalHyperDiscountWholesaleDiscount extends Model {
    public function getWholesaleDiscountsList() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_wholesale` ORDER BY id");
        return $query->rows;
    }

    public function addWholesaleDiscount($products_discount) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_wholesale` SET `name` = '" . $this->db->escape(json_encode($products_discount['name'])) . "', `description` = '" . $this->db->escape($products_discount['description']) . "'");
        
        $discount_id = $this->db->getLastId();
        
        return $discount_id;
    }

    public function cloneProductsEditor($discount_id) {
        $query = $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_wholesale_discount_editor`( `discount_id`, `editor_id`, `start_date`, `end_date`, `products_all`, `products`, `status`, `products_filter_url`, `discount_coef1`, `discount_coef2`, `discount_coef3`, `discount_priority`, `discount_type1`, `discount_type2`, `discount_type3`, `customer_groups`) 
         SELECT  `discount_id`, `editor_id`, `start_date`, `end_date`, `products_all`, `products`, `status`, `products_filter_url`, `discount_coef1`, `discount_coef2`, `discount_coef3`, `discount_priority`, `discount_type1`, `discount_type2`, `discount_type3`, `customer_groups`
         FROM `" . DB_PREFIX . "hd_wholesale_discount_editor`
         WHERE `id`='$discount_id'");
        $id = $this->db->getLastId();
        $query = $this->db->query("SELECT `editor_id` FROM `" . DB_PREFIX . "hd_wholesale_discount_editor` WHERE `id`='$discount_id' ORDER BY editor_id DESC LIMIT 0, 1");
        $new_editor_id=(int)$query->row['editor_id']+1;
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_wholesale_discount_editor` SET `editor_id` = '$new_editor_id',`status`='0' WHERE `id`='$id'");
    }

    public function editWholesaleDiscount($products_discount) {
        $save_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_wholesale_discount_editor WHERE  `discount_id` = '" . (int)$products_discount['discount_id'] . "'");
        foreach ($query->rows as $editor_val) {
            $save_data[$editor_val['id']] = array(
                'products_filter_url'   => $editor_val['products_filter_url'],
                'products'              => $editor_val['products'],
            );
        }
        
        $this->db->query("DELETE FROM `" . DB_PREFIX . "hd_wholesale_discount_editor` WHERE `discount_id` = '" . (int)$products_discount['discount_id'] . "'");

        if (isset($products_discount['discount_editor'])) {
            foreach ($products_discount['discount_editor'] as $editor_key => $editor_val) {
                $products_all = isset($editor_val['products_all']) ? 1 : 0;

                $start_date = (!empty($editor_val['start_date'])) ? "'" . ($editor_val['start_date']) . "'" : 'null';
                $end_date = (!empty($editor_val['end_date'])) ? "'" . ($editor_val['end_date']) . "'" : 'null';

                $discount_priority = $editor_val['discount_priority'];

                $customer_groups = (isset($editor_val['customer_groups'])) ? json_encode($editor_val['customer_groups']) : json_encode(array());

                $discount_coef1 = $editor_val['discount_coef1'];
                $discount_coef2 = $editor_val['discount_coef2'];
                $discount_coef3 = $editor_val['discount_coef3'];

                $discount_type1 = $editor_val['discount_type1'];
                $discount_type2 = $editor_val['discount_type2'];
                $discount_type3 = $editor_val['discount_type3'];
                $status = (isset($editor_val['status'])) ? $editor_val['status'] : 0;
                $save_time=time();

                if (empty($editor_val['id']))$editor_val['id'] = 0;
                
                if (!empty($save_data[$editor_val['id']])) {
                    $products_filter_url = $save_data[$editor_val['id']]['products_filter_url'];
                    $products = $save_data[$editor_val['id']]['products'];
                } else {
                    $products_filter_url = '';
                    $products = '';
                }
                
                $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_wholesale_discount_editor` SET "
                    . "`id` = '" . (int)$editor_val['id'] . "', "
                    . "`products_filter_url` = '" . $this->db->escape($products_filter_url) . "',"
                    . "`products` = '" . $this->db->escape($products) . "',"
                    . "`status` = '" . (int)$status . "', "
                    . "`discount_id` = '" . (int)$products_discount['discount_id'] . "', "
                    . "`editor_id` = '" . $editor_key . "', "
                    . "`start_date`=$start_date,"
                    . "`end_date`=$end_date,"
                    . "`products_all`='$products_all',"
                    . "`discount_priority`='$discount_priority',"
                    . "`discount_coef1`='$discount_coef1',"
                    . "`discount_coef2` = '$discount_coef2', "
                    . "`discount_coef3` = '$discount_coef3', "
                    . "`discount_type1` = '$discount_type1', "
                    . "`discount_type2` = '$discount_type2', "
                    . "`discount_type3` = '$discount_type3', "
                    . "`customer_groups` = '$customer_groups' "

                );
            }
        }


        $shops_all = isset($products_discount['shops_all']) ? $products_discount['shops_all'] : 0;


        $this->db->query("UPDATE `" . DB_PREFIX . "hd_wholesale` SET `name` = '" . $this->db->escape(json_encode($products_discount['name'])) . "',"
            . " `description` = '" . $this->db->escape($products_discount['description']) . "',"
            . " `shops_all` = '" . $shops_all . "' ,"
            . " `cost` = '" . $products_discount['cost'] . "' , "
            . " `round` = '" . $products_discount['round'] . "' "
            . "  WHERE `id` = '" . (int)$products_discount['discount_id'] . "'");
    }

    public function getWholesaleDiscount($products_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_wholesale` WHERE `id` = '" . (int)$products_discount_id . "'");
        return $query->row;
    }
    
    public function getWholesaleDiscountEditorsById($products_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_wholesale_discount_editor` WHERE `id` = '" . (int)$products_discount_id . "'");
        return $query->row;
    }
    
    public function getWholesaleDiscountEditors($products_discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_wholesale_discount_editor` WHERE `discount_id` = '" . (int)$products_discount_id . "' ORDER BY id");
        return $query->rows;
    }

    public function deleteWholesaleDiscount($products_discount_id) {
        $query=$this->db->query("SELECT `id` FROM " . DB_PREFIX . "hd_wholesale_discount_editor WHERE `discount_id`='" . (int)$products_discount_id . "'");

        if($query->num_rows) {
            foreach ($query->rows as $key => $value) {
                if ($value['id'] > 0) {
                    $this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE hdpid = '" . (int)(-1*$value['id']) . "'");
                }
            }
        }
        $this->db->query("DELETE FROM " . DB_PREFIX . "hd_wholesale_discount_editor WHERE `discount_id`='" . (int)$products_discount_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "hd_wholesale WHERE id = '" . (int)$products_discount_id . "'");
    }

    public function productClear($id) {
        if ($id > 0) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE hdpid = '" . (int)(-1*$id) . "'");
        }
    }
    
    public function productAdd($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET date_start='" . $this->db->escape($data['date_start']) . "', date_end='" . $this->db->escape($data['date_end']) . "', product_id = '" . (int)$data['product_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', quantity = '" . (int)$data['quantity'] . "', priority = '" . (int)$data['priority'] . "', price = '" . (float)$data['price'] ."', hdpid = '" . (int)(-1*$data['hdpid']) . "'");
    }
    
    public function editClientGroups($discount_id, $groups, $url) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_wholesale` SET `customers_filter_url`='$url',`customers` = '" . $this->db->escape(json_encode($groups)) . "',`customers_all`='0' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getClientGroups($discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_wholesale` WHERE `id` = '" . (int)$discount_id . "'");
        //   var_dump($query->row['customers_ids']);
        if ($query->row['customers_ids'] && !empty($query->row['customers_ids'])) {
            return json_decode($query->row['customers_ids'], true);
        } else {
            return array();
        }
    }

    public function getGroupIdDiscount($discount_id)
    {
        $query = $this->db->query("SELECT `discount_id` FROM `" . DB_PREFIX . "hd_wholesale_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");
        if($query->num_rows)
            return $query->row['discount_id'];
        else
            return false;
    }

    public function getProductFilterUrl($discount_id) {
        $query = $this->db->query("SELECT `products_filter_url` FROM `" . DB_PREFIX . "hd_wholesale_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if (isset($query->row['products_filter_url']))
            return $query->row['products_filter_url'];
        else
            return false;
    }

    public function getClientFilterUrl($discount_id) {
        $query = $this->db->query("SELECT `customers_filter_url` FROM `" . DB_PREFIX . "hd_wholesale` WHERE `id` = '" . (int)$discount_id . "'");

        if (isset($query->row['customers_filter_url']))
            return $query->row['customers_filter_url'];
        else
            return false;
    }

    public function ClearGroupProducts($discount_id) {
        $this->db->query("UPDATE `" . DB_PREFIX . "hd_wholesale_discount_editor` SET `products` = '' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getEditorProducts($discount_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_wholesale_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row && $query->row['products']) {
            return json_decode($query->row['products'], true);
        } else {
            return array();
        }
    }

    public function editEditorProducts($discount_id, $groups, $url) {
        $query = $this->db->query("SELECT `discount_id` FROM `" . DB_PREFIX . "hd_wholesale_discount_editor` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row) {
            $this->db->query("UPDATE `" . DB_PREFIX . "hd_wholesale_discount_editor` SET `products_filter_url`='$url', `products` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
        } else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_wholesale_discount_editor` SET `products_filter_url`='$url', `id` = '" . (int)$discount_id . "',`products` = '" . $this->db->escape(json_encode($groups)) . "'");
        }
    }
    
    public function editShopsList($discount_id, $groups) {

        $this->db->query("UPDATE `" . DB_PREFIX . "hd_wholesale` SET `shops` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

    public function getShopsList($discount_id) {
        $query = $this->db->query("SELECT shops FROM `" . DB_PREFIX . "hd_wholesale` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['shops']) {
            return json_decode($query->row['shops'], true);
        } else {
            return array();
        }
    }

    public function getGeoList($discount_id) {
        $query = $this->db->query("SELECT geos FROM `" . DB_PREFIX . "hd_wholesale` WHERE `id` = '" . (int)$discount_id . "'");

        if ($query->row['geos']) {
            return json_decode($query->row['geos'], true);
        } else {
            return array();
        }
    }

    public function editGeoList($discount_id, $groups) {

        $this->db->query("UPDATE `" . DB_PREFIX . "hd_wholesale` SET `geos_all`='0',`geos` = '" . $this->db->escape(json_encode($groups)) . "' WHERE `id` = '" . (int)$discount_id . "'");
    }

}
