<?php

class ModelExtensionTotalHyperDiscountDiscount extends Model {
    public function editAllDiscounts($data) {
        if (isset($data['user_discount'])) {
            foreach ($data['user_discount'] as $discount) {
                $status = isset($discount['status']) ? (int)$discount['status'] : 0;
                $this->db->query("UPDATE `" . DB_PREFIX . "hd_users` SET `status` = '" . (int)$status . "' WHERE `id` = '" . (int)$discount['discount_id'] . "'");
            }
        }

        if (isset($data['accumulative_discount'])) {
            foreach ($data['accumulative_discount'] as $discount) {
                $status = isset($discount['status']) ? (int) $discount['status'] : 0;
                $this->db->query("UPDATE `" . DB_PREFIX . "hd_accumulative` SET `status` = '" . (int)$status . "' WHERE `id` = '" . (int)$discount['discount_id'] . "'");
            }
        }

        if (isset($data['quantitative_discount'])) {
            foreach ($data['quantitative_discount'] as $discount) {
                $status = isset($discount['status']) ? (int) $discount['status'] : 0;
                $this->db->query("UPDATE `" . DB_PREFIX . "hd_quantitative` SET `status` = '" . (int)$status . "'  WHERE `id` = '" . (int)$discount['discount_id'] . "'");
            }
        }

        if (isset($data['kit_discount'])) {
            foreach ($data['kit_discount'] as $discount) {
                $status = isset($discount['status']) ? (int) $discount['status'] : 0;
                $this->db->query("UPDATE `" . DB_PREFIX . "hd_kit` SET `status` = '" . (int)$status . "'  WHERE `id` = '" . (int)$discount['discount_id'] . "'");
            }
        }

        if (isset($data['products_discount'])) {
            foreach ($data['products_discount'] as $discount) {
                $status = isset($discount['status']) ? (int) $discount['status'] : 0;
                $this->db->query("UPDATE `" . DB_PREFIX . "hd_product` SET `status` = '" . (int)$status . "'  WHERE `id` = '" . (int)$discount['discount_id'] . "'");
            }
        }

        if (isset($data['discount_variant'])) {
            $discount = $data['discount_variant'];
            if (!isset($discount['status'])) {
                $discount['status'] = 0;
            }

            if (!isset($discount['debugmode'])) {
                $discount['debugmode'] = 0;
            }
            
            if (!isset($discount['special_counter_one_day'])) {
                $discount['special_counter_one_day'] = 0;
            }

            foreach ($discount as $key => $val) {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "hd_setting` WHERE `key` = '" . $this->db->escape($key) . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_setting`(`key`, `value`)  VALUES ('" . $this->db->escape($key) . "', '" . $this->db->escape($val) . "')");
            }
        }
    }


    public function validKey() {
        $query  = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "hd_setting` WHERE `key`='license_key'");
        return !empty($query->row['value']);
    }
    
    public function updateDb() {
        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_special` LIKE 'hdpid'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_special` ADD `hdpid` int(11) DEFAULT 0");
        }
        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_discount` LIKE 'hdpid'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_discount` ADD `hdpid` int(11) DEFAULT 0");
        }
        
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hd_products`");        
    }
    
    public function checkDb() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_accumulation_statuses` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` text NOT NULL,
        `description` text NOT NULL,
        PRIMARY KEY (`id`)
    ) CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_info` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` text NOT NULL,
        `description` text NOT NULL,
        PRIMARY KEY (`id`)
    ) CHARSET=utf8");

        $this->db->query("

        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_level` (
          `id` int(11)  AUTO_INCREMENT,
          `name` text,
          `description` text ,
          `status` tinyint(1),
          `shops_all` tinyint(1),
          `shops` text ,
          `products` text ,
          `order_statuses` varchar(100) ,
          `products_filter_url` text,
          `start_date` date DEFAULT NULL,
          `end_date` date DEFAULT NULL,
          `products_all` tinyint(1) ,

          PRIMARY KEY (`id`)
      ) CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_level_editor` (
      `id` int(11)  AUTO_INCREMENT,
      `levels_id` int(11) ,
      `editor_id` int(11) ,
      `level_status_id` int(11) ,
      `level_function_orders` varchar(5) ,
      `level_function_products` varchar(5) ,
      `level_function_sum` varchar(20) ,
      `level_function` varchar(50) ,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `status` tinyint(1) ,
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");

        $this->db->query("ALTER TABLE `" . DB_PREFIX . "hd_level_editor` MODIFY `level_function_sum` varchar(20)");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_setting` (
        `key` varchar(255) NOT NULL,
        `value` text NOT NULL,
        PRIMARY KEY (`key`)
    ) CHARSET=utf8
    ");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_accumulative` (
          `id` int(11)  AUTO_INCREMENT,
          `name` text,
          `description` text ,
          `status` tinyint(1),
          `shops_all` tinyint(1),
          `shops` text ,
          `customers` text,
          `products` text ,
          `correction` tinyint(1),
          `order_statuses` varchar(100) ,
          `geos_all` tinyint(4) ,
          `geos` text ,
          `products_filter_url` text,
          `customers_filter_url` text,
          `start_date` date DEFAULT NULL,
          `end_date` date DEFAULT NULL,
          `products_all` tinyint(1) ,
          `customers_all` tinyint(1) ,
          `discount_variant_discount` varchar(50) ,
          `discount_variant_condition` varchar(50) ,
          `discount_variant_specials` varchar(50) ,
          `discount_variant_options` varchar(50) ,
          PRIMARY KEY (`id`)
      ) CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_accumulative_discount_editor` (
      `id` int(11)  AUTO_INCREMENT,
      `discount_id` int(11) ,
      `editor_id` int(11) ,
      `discount_status_id` int(11) ,
      `discount_function_orders` varchar(5) ,
      `discount_function_products` varchar(5) ,
      `discount_function_sum` varchar(20) ,
      `discount_function` varchar(50) ,
      `discount_percent` varchar(5) ,
      `discount_type` varchar(50) ,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `status` tinyint(1) ,
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_kit` (
      `id` int(11)  AUTO_INCREMENT,
      `name` text ,
      `description` text ,
      `correction` int(1),
      `status` tinyint(1),
      `shops_all` tinyint(1),
      `shops` text ,
      `customers_all` tinyint(1) ,
      `customers` text,
      `guests` tinyint(1),
      `geos_all` tinyint(4) ,
      `geos` text ,
      `customers_filter_url` text,
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_kit_discount_editor` (
      `id` int(11)  AUTO_INCREMENT,
      `discount_id` int(11) ,
      `editor_id` int(11) ,
      `discount_count_products` varchar(5) ,
      `discount_accumulation_sum` varchar(20) ,
      `discount_function` varchar(50) ,
      `discount_percent` varchar(5) ,
      `discount_type` varchar(50) ,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `products_all` tinyint(1) ,
      `products` text ,
      `discount_variant_discount` varchar(50) ,
      `discount_variant_condition` varchar(50) ,
      `discount_variant_specials` varchar(50) ,
      `discount_variant_options` varchar(50) ,
      `status` tinyint(1) ,
      `products_filter_url` text,
      `products2` text ,
      `type_qty` tinyint(1) ,
      `discount_count_products2`  varchar(5) ,
      `products_filter_url2` text ,
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_product` (
      `id` int(11)  AUTO_INCREMENT,
      `name` text ,
      `description` text,
      `status` tinyint(1) ,
      `shops_all` tinyint(1),
      `shops` text,
      `cost` varchar(55),
      `round` varchar(55),
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_product_discount_editor` (
      `id` int(11)  AUTO_INCREMENT,
      `discount_id` int(11) ,
      `editor_id` int(11) ,
      `discount_count_products`  varchar(5) ,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `products_all` tinyint(1) ,
      `products` text ,
      `status` tinyint(1) ,
      `products_filter_url` text,
      `discount_coef1`  varchar(5) ,
      `discount_coef2` varchar(5) ,
      `discount_coef3`  varchar(5) ,
      `discount_priority`  varchar(5) ,
      `discount_type1` varchar(5) ,
      `discount_type2` varchar(5) ,
      `discount_type3` varchar(5) ,
      `hd_product_discount_editor` text,
      `discount_week` text,
      `discount_time` text,
      `discount_period` varchar(55) ,
      `save_time` int(11) ,
      `customer_groups` text ,
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_wholesale` (
      `id` int(11)  AUTO_INCREMENT,
      `name` text ,
      `description` text,
      `status` tinyint(1) ,
      `shops_all` tinyint(1),
      `shops` text,
      `cost` varchar(55),
      `round` varchar(55),
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_wholesale_discount_editor` (
      `id` int(11)  AUTO_INCREMENT,
      `discount_id` int(11) ,
      `editor_id` int(11) ,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `products_all` tinyint(1) ,
      `products` text ,
      `status` tinyint(1) ,
      `products_filter_url` text,
      `discount_coef1`  varchar(5) ,
      `discount_coef2` varchar(5) ,
      `discount_coef3`  varchar(5) ,
      `discount_priority`  varchar(5) ,
      `discount_type1` varchar(5) ,
      `discount_type2` varchar(5) ,
      `discount_type3` varchar(5) ,
      `hd_product_discount_editor` text,
      `discount_week` text,
      `discount_time` text,
      `discount_period` varchar(55) ,
      `save_time` int(11) ,
      `customer_groups` text ,
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_specials` (
      `id` int(11)  AUTO_INCREMENT,
      `name` text ,
      `description` text,
      `status` tinyint(1) ,
      `shops_all` tinyint(1),
      `shops` text,
      `cost` varchar(55),
      `round` varchar(55),
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_specials_discount_editor` (
      `id` int(11)  AUTO_INCREMENT,
      `discount_id` int(11) ,
      `editor_id` int(11) ,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `products_all` tinyint(1) ,
      `products` text ,
      `status` tinyint(1) ,
      `products_filter_url` text,
      `discount_coef1`  varchar(5) ,
      `discount_coef2` varchar(5) ,
      `discount_coef3`  varchar(5) ,
      `discount_priority`  varchar(5) ,
      `discount_type1` varchar(5) ,
      `discount_type2` varchar(5) ,
      `discount_type3` varchar(5) ,
      `customer_groups` text ,
      `discount_week` text,
      `discount_time` text,
      `discount_period` varchar(55) ,
      `save_time` int(11) ,
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_quantitative` (
      `id` int(11)  AUTO_INCREMENT,
      `name` text ,
      `description` text ,
      `status` tinyint(1),
      `shops_all` tinyint(1),
      `shops` text ,
      `customers_all` tinyint(1),
      `customers` text,
      `correction` int(1),
      `guests` tinyint(1) ,
      `geos_all` tinyint(4) ,
      `geos` text ,
      `customers_filter_url` text,
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_quantitative_discount_editor` (
      `id` int(11)  AUTO_INCREMENT,
      `discount_id` int(11) ,
      `editor_id` int(11) ,
      `discount_count_products` varchar(11) ,
      `discount_accumulation_sum` varchar(11) ,
      `discount_function` varchar(50) ,
      `discount_percent` varchar(5) ,
      `discount_type` varchar(50) ,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `products_all` tinyint(1) ,
      `products` text ,
      `discount_variant_discount` varchar(50) ,
      `discount_variant_condition` varchar(50) ,
      `discount_variant_specials` varchar(50) ,
      `discount_variant_options` varchar(50) ,
      `status` tinyint(1) ,
      `products_filter_url` text,
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_users` (
      `id` int(11)  AUTO_INCREMENT,
      `name` text,
      `description` text ,
      `status` tinyint(1),
      `shops_all` tinyint(1),
      `shops` text ,
      `geos_all` tinyint(4) ,
      `geos` text ,
      `customers_all` tinyint(1) ,
      `customers` text,
      `guests` tinyint(1) ,
      `customers_filter_url` text,
      `correction` int(1),
      PRIMARY KEY (`id`)
    ) CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hd_users_discount_editor` (
      `id` int(11)  AUTO_INCREMENT,
      `editor_id` int(11) ,
      `discount_id` int(11) ,
      `products` text ,
      `products_filter_url` text,
      `discount_variant_discount` text ,
      `discount_variant_condition` text ,
      `discount_variant_specials` text ,
      `discount_variant_options` text ,
      `status` tinyint(1) ,
      `discount_percent` varchar(5) ,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `products_all` tinyint(1) ,
      `discount_type` varchar(50) ,
      PRIMARY KEY (`id`)
  ) CHARSET=utf8");

        $q = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "hd_setting`");
        if ($q->row['total'] == 0) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "hd_setting` (`key`, `value`) VALUES
              ('license_key', ''),
              ('setting_variants', 'most_profitable_type'),
              ('status', '1'),
              ('discount_variant_discount','protect_ignore'),
              ('discount_variant_specials','protect_ignore'),
              ('debugmode','0')");
        }

        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hd_product` LIKE 'cost'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "hd_product` ADD `cost` varchar(55) DEFAULT ''");
        }

        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hd_product` LIKE 'round'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "hd_product` ADD `round` varchar(55) DEFAULT ''");
        }
        

        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hd_product_discount_editor` LIKE 'discount_week'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "hd_product_discount_editor` ADD `discount_week` text DEFAULT ''");
        }

        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hd_product_discount_editor` LIKE 'discount_time'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "hd_product_discount_editor` ADD `discount_time` text DEFAULT ''");
        }

        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hd_product_discount_editor` LIKE 'discount_period'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "hd_product_discount_editor` ADD `discount_period` varchar(55) DEFAULT ''");
        }

        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hd_product_discount_editor` LIKE 'save_time'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "hd_product_discount_editor` ADD `save_time` int DEFAULT 0");
        }


        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_special` LIKE 'discount_week'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_special` ADD `discount_week` text DEFAULT ''");
        }

        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_special` LIKE 'discount_time'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_special` ADD `discount_time` text DEFAULT ''");
        }

        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_special` LIKE 'discount_period'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_special` ADD `discount_period` varchar(55) DEFAULT ''");
        }

        $result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_special` LIKE 'save_time'");
        if ($result->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_special` ADD `save_time` int DEFAULT 0");
        }
        
        $q = $this->db->query("SHOW FIELDS FROM `" . DB_PREFIX . "hd_quantitative_discount_editor` WHERE Field = 'end_date' OR Field = 'start_date' AND Type = 'date'");
        if ($q->num_rows) {
            $this->db->query("alter table `" . DB_PREFIX . "hd_quantitative_discount_editor` modify start_date date");
            $this->db->query("alter table `" . DB_PREFIX . "hd_quantitative_discount_editor` modify end_date date");
        }
        
        $this->updateDb();
    }
}
