<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ModelCatalogBundleExpert extends Model
{

    
    public function isModuleInstalled(){
        $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `code` = 'bundle_expert' OR `code` = 'bundle_expert_v'");
        if($result->num_rows) {
            
            return true;
        } else {
            
            return false;
        }
    }

    public function install()
    {
        $demo_prefix='';

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert` (
                    kit_id INT(11) NOT NULL AUTO_INCREMENT,
                      name VARCHAR(255) DEFAULT NULL,
                      kit_mode VARCHAR(64) DEFAULT NULL,
                      kit_price_mode VARCHAR(255) DEFAULT NULL,
                      image VARCHAR(255) DEFAULT NULL,
                      sort_order INT(3) NOT NULL DEFAULT 0,
                      status TINYINT(1) NOT NULL,
                      date_added DATETIME NOT NULL,
                      date_modified DATETIME NOT NULL,
                      kit_quantity_mode VARCHAR(255) DEFAULT NULL,
                      quantity_control TINYINT(1) NOT NULL,
                      enable_special TINYINT(1) DEFAULT NULL,
                      kit_cart_limit_mode VARCHAR(255) DEFAULT NULL,
                      product_discount_in_total TINYINT(1) DEFAULT NULL,
                      kit_in_cart_as_product TINYINT(1) DEFAULT NULL,
                      kit_as_product TINYINT(1) DEFAULT NULL,
                      save_kit_as_product_total TINYINT(1) DEFAULT NULL,
                      main_mode VARCHAR(32) DEFAULT 'kit',
                      product_quantity_limit VARCHAR(255) DEFAULT NULL,
                      disbanded_bundle_clear TINYINT(1) DEFAULT 0,
                      kit_as_product_light_mode TINYINT(1) DEFAULT 0,
                      enable_discount TINYINT(1) DEFAULT 0,
                      kit_discount_by_product_count VARCHAR(255) DEFAULT NULL,
                      kit_as_product_main_product_use_default_discount TINYINT(1) DEFAULT 0,
                      bundle_total_price_hide_special TINYINT(1) DEFAULT 0,
                      import_template_id INT(11) DEFAULT NULL,
                      import_template_mode TINYINT(1) DEFAULT 0,
                      show_default_specials_in_kit_discounts TINYINT(1) DEFAULT 0,
                      import_kit_key VARCHAR(255) DEFAULT '',
                      series_mode VARCHAR(255) DEFAULT 'default',
                      link_products_combine_mode VARCHAR(255) DEFAULT 'union',
                      custom_field_text TEXT NOT NULL,
                      custom_field_string VARCHAR(255) NOT NULL,
                      custom_field_number INT(11) DEFAULT 0,
                      auto_kit_in_cart TINYINT(1) DEFAULT 0,
                      add_to_cart_mode VARCHAR(24) DEFAULT 'bundle',
                      kit_price_mode_to_customer_groups TEXT DEFAULT NULL,
                      kit_price_mode_to_customer_groups_status TINYINT(1) DEFAULT NULL,
                      kit_mode_auto_list_grouping VARCHAR(64) DEFAULT NULL,
                      PRIMARY KEY (kit_id)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");



        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'custom_field_text'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `custom_field_text` text NOT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'custom_field_string'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `custom_field_string` varchar(255) NOT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'custom_field_number'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `custom_field_number` int(11) DEFAULT 0");
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_description` (
               kit_id int(11) NOT NULL,
          language_id int(11) NOT NULL,
          title varchar(255) NOT NULL,
          description text NOT NULL,
          cart_title varchar(255) NOT NULL,
          PRIMARY KEY (kit_id, language_id)
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
             CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_items` (
              id INT(11) NOT NULL AUTO_INCREMENT,
              kit_id INT(11) DEFAULT NULL,
              item_position INT(11) DEFAULT NULL,
              item_mode VARCHAR(64) DEFAULT NULL,
              item_empty_mode TEXT DEFAULT NULL,
              item_type VARCHAR(64) NOT NULL,
              item_id INT(11) DEFAULT NULL,
              quantity INT(11) NOT NULL,
              quantity_edit TINYINT(1) DEFAULT NULL,
              price_mode VARCHAR(64) NOT NULL,
              price DECIMAL(15, 4) NOT NULL,
              price_minus_sum DECIMAL(15, 4) DEFAULT NULL,
              price_minus_percent DECIMAL(15, 4) DEFAULT NULL,
              main TINYINT(1) DEFAULT NULL,
              disable_options TEXT DEFAULT NULL,
              fixed_options TEXT DEFAULT NULL,
              free_product_default_in_kit TINYINT(1) DEFAULT 0,
              hide_special_products TINYINT(1) DEFAULT 0,
              products_combine_mode VARCHAR(255) DEFAULT 'union',
              empty_group_image VARCHAR(255) DEFAULT NULL,
              randomize_select_product TINYINT(1) DEFAULT NULL,
              enabled_options TEXT DEFAULT NULL,
              item_value VARCHAR(512) DEFAULT '',
              custom_field VARCHAR(1024) DEFAULT '',
              price_mode_to_customer_groups TEXT DEFAULT NULL,
              price_mode_to_customer_groups_status TINYINT(1) DEFAULT NULL,
              PRIMARY KEY (id)
            )
            ENGINE = MYISAM,
            AUTO_INCREMENT = 328,
            AVG_ROW_LENGTH = 95,
            CHARACTER SET utf8,
            CHECKSUM = 0,
            COLLATE utf8_general_ci;");

                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_kit_history_status` (
                       kit_id int(11) DEFAULT NULL,
          main_product_id varchar(255) DEFAULT NULL,
          `position` varchar(255) DEFAULT NULL,
          kit_history_code varchar(255) DEFAULT NULL,
          success tinyint(1) DEFAULT NULL,
          date_modified datetime DEFAULT NULL
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_order` (
                       id int(11) NOT NULL AUTO_INCREMENT,
          order_id int(11) DEFAULT NULL,
          order_product_id int(11) DEFAULT NULL,
          product_id int(11) DEFAULT NULL,
          cart_kit_info varchar(1024) DEFAULT NULL,
          product_as_kit_info varchar(1024) DEFAULT NULL,
          PRIMARY KEY (id)
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_order_kit_info` (
                     id int(11) NOT NULL AUTO_INCREMENT,
          order_id int(11) DEFAULT NULL,
          kit_unique_id varchar(255) DEFAULT NULL,
          kit_info text DEFAULT NULL,
          PRIMARY KEY (id)
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_to_product` (
                      id int(11) NOT NULL AUTO_INCREMENT,
          kit_id int(11) DEFAULT NULL,
          item_type varchar(255) DEFAULT NULL,
          item_id int(11) DEFAULT NULL,
           item_value varchar(512) DEFAULT '',
          PRIMARY KEY (id)
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("ALTER TABLE `" . DB_PREFIX . $demo_prefix. "order_product` MODIFY `name` VARCHAR(1024)");

                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_widget` (
                     widget_id int(11) NOT NULL AUTO_INCREMENT,
          name varchar(255) DEFAULT NULL,
          display_mode varchar(255) DEFAULT NULL,
          config_module text DEFAULT NULL,
          config_product_page text DEFAULT NULL,
          config_category_page text DEFAULT NULL,
          config_custom_page text DEFAULT NULL,
          template varchar(255) DEFAULT NULL,
          widget_width_mode text DEFAULT NULL,
          set_image_size_mode text DEFAULT NULL,
          background_image_size text DEFAULT NULL,
          status tinyint(1) DEFAULT NULL,
          slider_mode tinyint(1) DEFAULT NULL,
          checkbox_mode tinyint(1) DEFAULT 0,
          sort_order int(11) DEFAULT NULL,
          slider_autoplay_status int(11) DEFAULT 3000,
          slider_autoplay_time int(11) DEFAULT 0,
          table_mode_config text DEFAULT NULL,
          product_click_mode VARCHAR(255) DEFAULT 'default',
          main_mode VARCHAR(32) DEFAULT 'default',
          product_from_kit_mode_items VARCHAR(32) DEFAULT 'link_to_product_page',
          product_from_kit_mode_kits VARCHAR(32) DEFAULT 'all_kits',
          product_from_kit_mode_product_source VARCHAR(32) DEFAULT 'from_page',
          link_products_combine_mode varchar(255) DEFAULT 'union',
          html_element_action INT(11) DEFAULT '0',
          html_element_action_selector varchar(1024) DEFAULT '',
          kits_per_category_page int(11) DEFAULT 5,
          link_category_filter_kits varchar(64) DEFAULT 'all',
          
          PRIMARY KEY (widget_id)
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");



        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix.  "bundle_expert_widget_description` (
               widget_id int(11) NOT NULL,
          language_id int(11) NOT NULL,
          title varchar(255) NOT NULL,
          description text NOT NULL,
          PRIMARY KEY (widget_id, language_id)
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");


                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_widget_history_status` (
                       widget_id int(11) DEFAULT NULL,
          widget_history_code varchar(255) DEFAULT NULL,
          success tinyint(1) DEFAULT NULL,
          date_modified datetime DEFAULT NULL
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_widget_kit` (
                       id int(11) NOT NULL AUTO_INCREMENT,
          widget_id int(11) DEFAULT NULL,
          kit_id int(11) DEFAULT NULL,
          PRIMARY KEY (id)
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_widget_to_store` (
                       widget_id int(11) NOT NULL,
          store_id int(11) NOT NULL,
          PRIMARY KEY (widget_id, store_id)
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_cache_kit_items` (
                          kit_id int(11) DEFAULT NULL,
                          kit_items longtext DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_cache_kit_items_info` (
                          kit_id int(11) DEFAULT NULL,
                          kit_items_info longtext DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_cache_kit_link_products` (
                          kit_id int(11) DEFAULT NULL,
                          product_id int(11) DEFAULT NULL,
                          mode varchar(255) DEFAULT 'default',
                          kit_link_products longtext DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_cache_kit_products` (
                          kit_id int(11) DEFAULT NULL,
                          item_position int(11) DEFAULT NULL,
                          product_id int(11) DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_kit_period` (
                          kit_period_id INT(11) NOT NULL AUTO_INCREMENT,
                          kit_id INT(11) NOT NULL,
                          customer_group_id INT(11) NOT NULL,
                          priority INT(5) NOT NULL DEFAULT 1,
                          date_start DATE NOT NULL DEFAULT '0000-00-00',
                          date_end DATE NOT NULL DEFAULT '0000-00-00',
                          PRIMARY KEY (kit_period_id)
                        )
              ENGINE = INNODB,
                CHARACTER SET utf8,
                COLLATE utf8_general_ci");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_kit_enable_status` (
                         kit_id INT(11) DEFAULT NULL,
                          main_product_id INT(11) DEFAULT NULL,
                          enable TINYINT(4) DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $demo_prefix. "bundle_expert_cache_widget_link_products` (
                          widget_id INT(11) DEFAULT NULL,
                          product_id INT(11) DEFAULT NULL
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_widget_to_product` (
                          id INT(11) NOT NULL AUTO_INCREMENT,
                          widget_id INT(11) DEFAULT NULL,
                          item_type VARCHAR(255) DEFAULT NULL,
                          item_id INT(11) DEFAULT NULL,
                          item_value VARCHAR(512) DEFAULT '',
                          PRIMARY KEY (id)
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_price` (
                         product_id INT(11) DEFAULT NULL,
                         customer_group_id INT(11) DEFAULT NULL,
                          price DECIMAL(15, 4) NOT NULL DEFAULT 0.0000,
                          special DECIMAL(15, 4) NOT NULL DEFAULT 0.0000
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_product_group` (
                              group_id INT(11) NOT NULL AUTO_INCREMENT,
                              name VARCHAR(255) DEFAULT NULL,
                              kit_id INT(11) DEFAULT NULL,
                              add_mode VARCHAR(50) NOT NULL,
                              products_mode VARCHAR(50) NOT NULL,
                              sort_order INT(11) DEFAULT NULL,
                              PRIMARY KEY (group_id)
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_product_group_item` (
                              kit_id INT(11) NOT NULL,
                              item_id INT(11) NOT NULL AUTO_INCREMENT,
                              group_id INT(11) DEFAULT NULL,
                              combine_mode VARCHAR(255) DEFAULT NULL,
                              source VARCHAR(255) DEFAULT NULL,
                              operation VARCHAR(255) DEFAULT NULL,
                              value VARCHAR(255) DEFAULT NULL,
                              sort_order INT(11) DEFAULT NULL,
                              PRIMARY KEY (item_id)
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_product_group_selected_product` (
                              kit_id INT(11) NOT NULL,
                              group_id INT(11) NOT NULL,
                              product_id INT(11) NOT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data` (
                              kit_id INT(11) DEFAULT NULL,
                              main_product_id INT(11) DEFAULT NULL,
                              quantity INT(4) DEFAULT NULL,
                              stock_in_status tinyint(1) DEFAULT 0
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");


        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data_le` (
                              kit_id INT(11) DEFAULT NULL,
                              main_product_id INT(11) DEFAULT NULL,
                              quantity INT(4) DEFAULT NULL,
                              stock_in_status tinyint(1) DEFAULT 0
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_auto_kit_in_cart` (
                                                kit_id INT(11) DEFAULT NULL,
                      `position` INT(11) DEFAULT NULL,
                      product_id INT(11) DEFAULT NULL,
                      quantity INT(11) DEFAULT NULL,
                      cart_quantity INT(11) DEFAULT NULL,
                      items_count INT(11) DEFAULT NULL,
                      kit_sort_order INT(11) DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_widget_to_category` (
                      widget_id INT(11) DEFAULT NULL,
                      category_id INT(11) DEFAULT NULL
                        )
                    ENGINE = INNODB,
                    AVG_ROW_LENGTH = 4096,
                    CHARACTER SET utf8,
                    COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_category_kits` (
                      widget_id INT(11) DEFAULT NULL,
                      category_id INT(11) DEFAULT NULL,
                      kit_id INT(11) DEFAULT NULL,
                      main_product_id INT(11) DEFAULT NULL,
                      stock_in_status TINYINT(1) DEFAULT NULL
                        )
                ENGINE = INNODB,
                AVG_ROW_LENGTH = 4096,
                CHARACTER SET utf8,
                COLLATE utf8_general_ci;");

        $this->load->model('setting/setting');

        $help_data = array(
            'bundle_expert_help_url'=>'http://demo.opencart-expert.com/index.php?route=information/information&information_id=8',
            'bundle_expert_help_data'=>array(),
            'bundle_expert_help_update' =>''
        );

        $this->model_setting_setting->editSetting('bundle_expert_help', $help_data);
    }

    public function uninstall()
    {
        $demo_prefix='';
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_description`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_items`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_kit_history_status`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_order`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_order_kit_info`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_to_product`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_widget`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_widget_history_status`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_widget_kit`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_widget_to_store`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_cache_kit_items`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_cache_kit_products`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_cache_kit_items_info`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_cache_kit_link_products`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_kit_period`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_kit_import`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_widget_to_product`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_cache_widget_link_products`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_cache_product_as_kit_price`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_product_group`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_product_group_item`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_product_group_selected_product`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $demo_prefix."bundle_expert_auto_kit_in_cart`;");

    }

    public function getOrderMarkeplaces() {
        $list = array(
            array(
                'market_id' => 0,
                'name' => 'opencartforum.com'
            ),
            array(
                'market_id' => 1,
                'name' => 'opencart.com'
            ),

        );

        return $list;
    }

    private static $attributes = array(
        'kit-title',
        'widget-id',
        'kit-id',
        'main-product-id',
        'widget-unique-id',
        'free-products-table-mode',
        'image-width',
        'empty-item',
        'checkbox-in-kit-show',
        'item-position',
        'item-position-free',
        'default-item-position',
        'is-main-product-position',
        'item-product-position',
        'last-item-product-position',
        'product-id',
        'price-value',
        'div-unique-id',
    );

    private static $duplicate_id = array(
        'input-quantity',
        'input-product-id',
        'input-item-position',
        'input-item-position-free',
        'input-for-quantity-edit',
        'input-is-item-is-empty',
        'input-is-free-product',
        'input-free-product-in-kit',
        'input-checkbox-in-kit-field',
    );


    private static $replace_selectors = array(
        '.product-form',
        '.product-thumb',
        '.product-thumb-overlay',
        '.product-title',
        '.price',
        '.price-old',
        '.price-new',
        '.image',
        '.imag',
        '.img-thumbnail',
        '.caption',
        '.price-tax',
        '#product',
    );

    public function update_files(){
        $dir_tpl = DIR_CATALOG . 'view/theme/default/template/module/bundle_expert/custom_widget/';
        $dir_js = DIR_CATALOG . 'view/javascript/bundle-expert/';
        $dir_css = DIR_CATALOG . 'view/theme/default/stylesheet/';
        $files_tpl = array(
            $dir_tpl . 'custom_template_1_kit_item.tpl',
            $dir_tpl . 'custom_template_1_widget.tpl',
            $dir_tpl . 'custom_template_2_kit_item.tpl',
            $dir_tpl . 'custom_template_2_widget.tpl',
            $dir_tpl . 'custom_template_3_kit_item.tpl',
            $dir_tpl . 'custom_template_3_widget.tpl',
            $dir_tpl . 'custom_template_4_kit_item.tpl',
            $dir_tpl . 'custom_template_4_widget.tpl',

            $dir_tpl . 'custom_template_1_kit_item.twig',
            $dir_tpl . 'custom_template_1_widget.twig',
            $dir_tpl . 'custom_template_2_kit_item.twig',
            $dir_tpl . 'custom_template_2_widget.twig',
            $dir_tpl . 'custom_template_3_kit_item.twig',
            $dir_tpl . 'custom_template_3_widget.twig',
            $dir_tpl . 'custom_template_4_kit_item.twig',
            $dir_tpl . 'custom_template_4_widget.twig',
        );

        $files_js = array(
            $dir_js . 'bundle-expert-custom.js'
        );
        $files_css = array(
            $dir_css . 'bundle_expert_custom.css'
        );

        $all_files = array(
            $dir_tpl . 'custom_template_1_kit_item.tpl',
            $dir_tpl . 'custom_template_1_widget.tpl',
            $dir_tpl . 'custom_template_2_kit_item.tpl',
            $dir_tpl . 'custom_template_2_widget.tpl',
            $dir_tpl . 'custom_template_3_kit_item.tpl',
            $dir_tpl . 'custom_template_3_widget.tpl',
            $dir_tpl . 'custom_template_4_kit_item.tpl',
            $dir_tpl . 'custom_template_4_widget.tpl',

            $dir_tpl . 'custom_template_1_kit_item.twig',
            $dir_tpl . 'custom_template_1_widget.twig',
            $dir_tpl . 'custom_template_2_kit_item.twig',
            $dir_tpl . 'custom_template_2_widget.twig',
            $dir_tpl . 'custom_template_3_kit_item.twig',
            $dir_tpl . 'custom_template_3_widget.twig',
            $dir_tpl . 'custom_template_4_kit_item.twig',
            $dir_tpl . 'custom_template_4_widget.twig',

            $dir_js . 'bundle-expert-custom.js',

            $dir_css. 'bundle_expert_custom.css'
        );

        
        
        
        
        foreach ($all_files as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);

                $base_file_name = basename($file);
                $old_base_file_name = '_old_' . $base_file_name;
                $old_file_name = str_replace($base_file_name, $old_base_file_name, $file);
                file_put_contents($old_file_name, $content);

                if (in_array($file, $files_tpl)) {

                    $content = $this->file_replace_attribute($content);
                    $content = $this->file_replace_duplicate_id($content);
                    $content = $this->file_replace_class($content);
                    $content = $this->file_replace_selectors($content);
                }

                if (in_array($file, $files_js)) {
                    $content = $this->file_replace_selectors($content);
                }

                if (in_array($file, $files_css)) {
                    $content = $this->file_replace_selectors($content);
                }

                $base_file_name = basename($file);
                $file_name = '' . $base_file_name;

                $file_name = str_replace($base_file_name, $file_name, $file);
                file_put_contents($file_name, $content);
                

                
            }


        }
    }

    private function file_replace_attribute($content){

        foreach (self::$attributes as $attribute) {
            $replace_value = " data-" . $attribute;
            $search_value = " " . $attribute;
            $content = str_replace($search_value, $replace_value, $content);
        }

        return $content;
    }

    private function file_replace_duplicate_id($content){
        foreach (self::$duplicate_id as $duplicate_id) {
            $replace_value = ' data-id="' . $duplicate_id;
            $search_value = ' id="' . $duplicate_id;
            $content = str_replace($search_value, $replace_value, $content);
        }

        return $content;
    }

    private function file_replace_class($content){

        foreach (self::$replace_selectors as $replace_selector) {
            $replace_class = str_replace('.', '', $replace_selector);
            $replace_class = str_replace('#', '', $replace_class);

            $search_value = '"'.$replace_class.'"';
            $replace_value = '"be-'.$replace_class.'"';
            $content = str_replace($search_value, $replace_value, $content);

            $search_value = '"'.$replace_class.' ';
            $replace_value = '"be-'.$replace_class.' ';
            $content = str_replace($search_value, $replace_value, $content);

            $search_value = ' '.$replace_class.'"';
            $replace_value = ' be-'.$replace_class.'"';
            $content = str_replace($search_value, $replace_value, $content);

            $search_value = ' '.$replace_class.' ';
            $replace_value = ' be-'.$replace_class.' ';
            $content = str_replace($search_value, $replace_value, $content);

            
            if($replace_class=="product"){
                $search_value =  'be-product =';
                $replace_value = 'product =';
                $content = str_replace($search_value, $replace_value, $content);
            }
            if($replace_class=="image"){
                $search_value =  'class="radio be-image"';
                $replace_value = 'class="radio image"';
                $content = str_replace($search_value, $replace_value, $content);
            }

        }

        return $content;

    }

    private function file_replace_selectors($content){
        
        
        

        foreach (self::$attributes as $attribute) {
            $search_value = "[" . $attribute . "=";
            $replace_value = "[data-" . $attribute . "=";
            $content = str_replace($search_value, $replace_value, $content);

            $search_value = ".attr('" . $attribute . "')";
            $replace_value = ".attr('data-" . $attribute . "')";
            $content = str_replace($search_value, $replace_value, $content);

            $search_value = '.attr("' . $attribute . '")';
            $replace_value = '.attr("data-' . $attribute . '")';
            $content = str_replace($search_value, $replace_value, $content);
        }

        foreach (self::$duplicate_id as $duplicate_id) {
            $search_value = "#" . $duplicate_id . "";
            $replace_value = "[data-id=" . $duplicate_id . "]";
            $content = str_replace($search_value, $replace_value, $content);


        }

        foreach (self::$replace_selectors as $replace_selector) {
            $search_value = $replace_selector;
            $replace_value = str_replace('.', '.be-', $replace_selector);
            $replace_value = str_replace('#', '#be-', $replace_value);
            $content = str_replace($search_value, $replace_value, $content);

            
            if($search_value==".price"){
                $search_value =  '.be-price;';
                $replace_value = '.price;';
                $content = str_replace($search_value, $replace_value, $content);

                $search_value =  '.be-price_value;';
                $replace_value = '.price_value;';
                $content = str_replace($search_value, $replace_value, $content);

            }
        }

        return $content;
    }



    public function update(){

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'table_mode_config'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `table_mode_config` TEXT");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'product_discount_in_total'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `product_discount_in_total` tinyint(1)");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'kit_in_cart_as_product'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `kit_in_cart_as_product` tinyint(1)");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'kit_as_product'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `kit_as_product` tinyint(1)");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'save_kit_as_product_total'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `save_kit_as_product_total` tinyint(1)");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_order` LIKE 'product_as_kit_info'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_order` ADD `product_as_kit_info` varchar(1024) DEFAULT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_items` LIKE 'free_product_default_in_kit'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_items` ADD `free_product_default_in_kit` tinyint(1) DEFAULT 0");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'slider_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `slider_mode` tinyint(1) DEFAULT 1");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'main_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `main_mode` varchar(32) DEFAULT 'kit'");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'product_quantity_limit'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `product_quantity_limit` varchar(255) DEFAULT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'disbanded_bundle_clear'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `disbanded_bundle_clear` tinyint(1) DEFAULT 0");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'kit_as_product_light_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `kit_as_product_light_mode` tinyint(1) DEFAULT 0");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_items` LIKE 'hide_special_products'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_items` ADD `hide_special_products` tinyint(1) DEFAULT 0");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_items` LIKE 'products_combine_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_items` ADD `products_combine_mode`  varchar(255) DEFAULT 'union'");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_items` LIKE 'empty_group_image'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_items` ADD `empty_group_image`  varchar(255) DEFAULT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'enable_discount'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `enable_discount` tinyint(1) DEFAULT 0");
        }



        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_items` LIKE 'randomize_select_product'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_items` ADD `randomize_select_product`  tinyint(1) DEFAULT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'checkbox_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `checkbox_mode` tinyint(1) DEFAULT 0");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'kit_discount_by_product_count'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `kit_discount_by_product_count` varchar(255) DEFAULT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'kit_as_product_main_product_use_default_discount'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `kit_as_product_main_product_use_default_discount` tinyint(1) DEFAULT 0");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'bundle_total_price_hide_special'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `bundle_total_price_hide_special` tinyint(1) DEFAULT 0");
        }

        
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'import_template_id'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `import_template_id` INT(11) DEFAULT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'import_template_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `import_template_mode` TINYINT(1) DEFAULT 0");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'import_kit_key'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `import_kit_key` varchar(255) DEFAULT ''");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'product_click_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `product_click_mode` VARCHAR(255) DEFAULT 'default'");
        }


        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'show_default_specials_in_kit_discounts'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `show_default_specials_in_kit_discounts` TINYINT(1) DEFAULT 0");
        }

        

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_kit_items` (
                          kit_id int(11) DEFAULT NULL,
                          kit_items longtext DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_kit_items_info` (
                          kit_id int(11) DEFAULT NULL,
                          kit_items_info longtext DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_kit_link_products` (
                          kit_id int(11) DEFAULT NULL,
                          kit_link_products longtext DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_kit_products` (
                          kit_id int(11) DEFAULT NULL,
                          product_id int(11) DEFAULT NULL,
                          stock_quantity int(11) DEFAULT 0,
                          sort_order int(11) DEFAULT 0
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");


        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_kit_period` (
                          kit_period_id INT(11) NOT NULL AUTO_INCREMENT,
                          kit_id INT(11) NOT NULL,
                          customer_group_id INT(11) NOT NULL,
                          priority INT(5) NOT NULL DEFAULT 1,
                          date_start DATE NOT NULL DEFAULT '0000-00-00',
                          date_end DATE NOT NULL DEFAULT '0000-00-00',
                          PRIMARY KEY (kit_period_id)
                        )
              ENGINE = INNODB,
                CHARACTER SET utf8,
                COLLATE utf8_general_ci");

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_cache_kit_link_products` LIKE 'product_id'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_cache_kit_link_products` ADD `product_id` INT(11) DEFAULT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_items` LIKE 'enabled_options'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_items` ADD `enabled_options` TEXT DEFAULT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'series_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `series_mode` varchar(255) DEFAULT 'default'");
        }

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_kit_enable_status` (
                         kit_id INT(11) DEFAULT NULL,
                          main_product_id INT(11) DEFAULT NULL,
                          enable TINYINT(4) DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'slider_autoplay_status'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `slider_autoplay_status` TINYINT(1) DEFAULT '0'");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'slider_autoplay_time'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `slider_autoplay_time` INT(11) DEFAULT '3000' ");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'main_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `main_mode` varchar(32) DEFAULT 'default' ");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'product_from_kit_mode_items'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `product_from_kit_mode_items` varchar(32) DEFAULT 'link_to_product_page' ");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'product_from_kit_mode_kits'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `product_from_kit_mode_kits` varchar(32) DEFAULT 'all_kits' ");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'product_from_kit_mode_product_source'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `product_from_kit_mode_product_source` varchar(32) DEFAULT 'from_page' ");
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_widget_description` (
               widget_id int(11) NOT NULL,
          language_id int(11) NOT NULL,
          title varchar(255) NOT NULL,
          description text NOT NULL,
          PRIMARY KEY (widget_id, language_id)
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_to_product` LIKE 'item_value'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_to_product` ADD `item_value` varchar(512) DEFAULT '' ");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_items` LIKE 'item_value'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_items` ADD `item_value` varchar(512) DEFAULT '' ");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_cache_kit_products` LIKE 'item_position'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_cache_kit_products` ADD `item_position` INT(11) DEFAULT 0");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'link_products_combine_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `link_products_combine_mode`  varchar(255) DEFAULT 'union'");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'link_products_combine_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `link_products_combine_mode`  varchar(255) DEFAULT 'union'");
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_widget_to_product` (
                  id INT(11) NOT NULL AUTO_INCREMENT,
                  widget_id INT(11) DEFAULT NULL,
                  item_type VARCHAR(255) DEFAULT NULL,
                  item_id INT(11) DEFAULT NULL,
                  item_value VARCHAR(512) DEFAULT '',
                  PRIMARY KEY (id)
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_widget_link_products` (
                  widget_id INT(11) DEFAULT NULL,
                  product_id INT(11) DEFAULT NULL
                    ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'html_element_action'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `html_element_action`  INT(11) DEFAULT '0'");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'html_element_action_selector'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `html_element_action_selector`  varchar(1024) DEFAULT ''");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_cache_kit_link_products` LIKE 'mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_cache_kit_link_products` ADD `mode` varchar(255) DEFAULT 'default'");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_items` LIKE 'custom_field'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_items` ADD `custom_field` varchar(1024) DEFAULT ''");
        }


        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_price` (
  product_id INT(11) DEFAULT NULL,
  price DECIMAL(15, 4) NOT NULL DEFAULT 0.0000,
  special DECIMAL(15, 4) NOT NULL DEFAULT 0.0000,
  customer_group_id INT(11) DEFAULT 0,
  kit_id INT(11) DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");


        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_product_group` (
                              group_id INT(11) NOT NULL AUTO_INCREMENT,
                              name VARCHAR(255) DEFAULT NULL,
                              kit_id INT(11) DEFAULT NULL,
                              add_mode VARCHAR(50) NOT NULL,
                              products_mode VARCHAR(50) NOT NULL,
                              sort_order INT(11) DEFAULT NULL,
                              PRIMARY KEY (group_id)
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_product_group_item` (
                              kit_id INT(11) NOT NULL,
                              item_id INT(11) NOT NULL AUTO_INCREMENT,
                              group_id INT(11) DEFAULT NULL,
                              combine_mode VARCHAR(255) DEFAULT NULL,
                              source VARCHAR(255) DEFAULT NULL,
                              operation VARCHAR(255) DEFAULT NULL,
                              value VARCHAR(255) DEFAULT NULL,
                              sort_order INT(11) DEFAULT NULL,
                              PRIMARY KEY (item_id)
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_product_group_selected_product` (
                              kit_id INT(11) NOT NULL,
                              group_id INT(11) NOT NULL,
                              product_id INT(11) NOT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data` (
                              kit_id INT(11) DEFAULT NULL,
                              main_product_id INT(11) DEFAULT NULL,
                              quantity INT(4) DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data_le` (
                              kit_id INT(11) DEFAULT NULL,
                              main_product_id INT(11) DEFAULT NULL,
                              quantity INT(4) DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data` LIKE 'stock_in_status'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data` ADD `stock_in_status` tinyint(1) DEFAULT 0");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data_le` LIKE 'stock_in_status'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data_le` ADD `stock_in_status` tinyint(1) DEFAULT 0");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_cache_kit_products` LIKE 'stock_quantity'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_cache_kit_products` ADD `stock_quantity` int(11) DEFAULT 0");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_cache_kit_products` LIKE 'sort_order'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_cache_kit_products` ADD `sort_order` int(11) DEFAULT 0");
        }

        $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` MODIFY `slider_autoplay_time` int(11)");


        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'custom_field_text'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `custom_field_text` text NOT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'custom_field_string'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `custom_field_string` varchar(255) NOT NULL");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'custom_field_number'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `custom_field_number` int(11) DEFAULT 0");
        }

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_auto_kit_in_cart` (
                                                kit_id INT(11) DEFAULT NULL,
                      `position` INT(11) DEFAULT NULL,
                      product_id INT(11) DEFAULT NULL,
                      quantity INT(11) DEFAULT NULL,
                      cart_quantity INT(11) DEFAULT NULL,
                      items_count INT(11) DEFAULT NULL,
                      kit_sort_order INT(11) DEFAULT NULL
                        )
                        ENGINE = INNODB,
                        AVG_ROW_LENGTH = 16384,
                        CHARACTER SET utf8,
                        COLLATE utf8_general_ci;");

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'auto_kit_in_cart'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `auto_kit_in_cart` tinyint(1) DEFAULT 0");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'add_to_cart_mode'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `add_to_cart_mode` varchar(24) DEFAULT 'bundle'");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'kits_per_category_page'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `kits_per_category_page` int(11) DEFAULT 5");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_widget` LIKE 'link_category_filter_kits'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_widget` ADD `link_category_filter_kits` varchar(64) DEFAULT 'all'");
        }

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_widget_to_category` (
                      widget_id INT(11) DEFAULT NULL,
                      category_id INT(11) DEFAULT NULL
                        )
                    ENGINE = INNODB,
                    AVG_ROW_LENGTH = 4096,
                    CHARACTER SET utf8,
                    COLLATE utf8_general_ci;");

        $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "bundle_expert_cache_category_kits` (
                      widget_id INT(11) DEFAULT NULL,
                      category_id INT(11) DEFAULT NULL,
                      kit_id INT(11) DEFAULT NULL,
                      main_product_id INT(11) DEFAULT NULL,
                      stock_in_status TINYINT(1) DEFAULT NULL
                        )
                ENGINE = INNODB,
                AVG_ROW_LENGTH = 4096,
                CHARACTER SET utf8,
                COLLATE utf8_general_ci;");


        
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_price` LIKE 'customer_group_id'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_price` ADD `customer_group_id` int(11) DEFAULT 0");
        }
        

        
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'kit_price_mode_to_customer_groups'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `kit_price_mode_to_customer_groups` TEXT DEFAULT NULL");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'kit_price_mode_to_customer_groups_status'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `kit_price_mode_to_customer_groups_status` TINYINT(1) DEFAULT NULL");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_items` LIKE 'price_mode_to_customer_groups'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_items` ADD `price_mode_to_customer_groups` TEXT DEFAULT NULL");
        }
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_items` LIKE 'price_mode_to_customer_groups_status'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_items` ADD `price_mode_to_customer_groups_status` TINYINT(1) DEFAULT NULL");
        }
        

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_price` LIKE 'kit_id'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert_cache_product_as_kit_price` ADD `kit_id` INT(11) DEFAULT NULL");
        }

        
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_expert` LIKE 'kit_mode_auto_list_grouping'");
        if($query->num_rows==0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_expert` ADD `kit_mode_auto_list_grouping` VARCHAR(64) DEFAULT NULL");
        }
        




    }






}