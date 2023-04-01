<?php

class ModelExtensionModuleOCFilterSetting extends Model {   
  public function setSitemapKeyword() {
    if ($this->ocfilter->opencart->version >= 30) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `keyword` = 'ocfilter-sitemap'");
      
      if (!$query->num_rows) {               
        foreach ($this->ocfilter->opencart->getStores() as $store_id) {
          foreach ($this->ocfilter->opencart->getLanguages() as $language) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET `keyword` = 'ocfilter-sitemap', `query` = 'extension/feed/ocfilter_sitemap', store_id = '" . (int)$store_id . "', language_id = '" . (int)$language['language_id'] . "'");  
          }
        }        
      }           
    } else {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `keyword` = 'ocfilter-sitemap'");
      
      if (!$query->num_rows) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET `keyword` = 'ocfilter-sitemap', `query` = 'extension/feed/ocfilter_sitemap'");      
      }    
    }
    
    $this->cache->delete('seo_pro');
    $this->cache->delete('seopro');
  }
  
  public function addLanguage($language_id) {
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_description (`filter_id`, `source`, `language_id`, `name`, `suffix`, `description`) SELECT `filter_id`, `source`, '" . (int)$language_id . "', `name`, `suffix`, `description` FROM " . DB_PREFIX . "ocfilter_filter_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_description (`value_id`, `source`, `language_id`, `filter_id`, `name`) SELECT `value_id`, `source`, '" . (int)$language_id . "', `filter_id`, `name` FROM " . DB_PREFIX . "ocfilter_filter_value_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_page_description (`page_id`, `language_id`, `name`, `heading_title`, `meta_title`, `meta_keyword`, `meta_description`, `description_top`, `description_bottom`) SELECT `page_id`, '" . (int)$language_id . "', `name`, `heading_title`, `meta_title`, `meta_keyword`, `meta_description`, `description_top`, `description_bottom` FROM " . DB_PREFIX . "ocfilter_page_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
  }

  // Env, Maintenance      
  public function isUseKJseries() {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "modification` WHERE LCASE(`code`) = 'seriesbykj' AND status = '1'");

    return ($query->num_rows && $query->row['total']);
  }  
  
  public function isUseManufacturerDescription() {
    return $this->isColumnExists('manufacturer_description', 'name');
  }  
  
  public function isUseMainCategory() {
    return $this->isColumnExists('product_to_category', 'main_category');
  }  
  
  public function isUseMultiCurrency() {
    return $this->isColumnExists('product', 'currency_id');
  }   
  
  public function isUseSpecialPrefix() {
    return $this->isColumnExists('product_special', 'price_prefix');
  }     

  public function isUseHPModel() {   
    if ($this->isTableExists('hpmodel_product_hidden') && $this->isTableExists('hpmodel_links') && $this->isTableExists('hpmodel_to_store')) {
      $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "modification` WHERE LCASE(`code`) = 'hmpm14' AND status = '1'");

      if ($query->num_rows && $query->row['total']) {
        return true;
      }
            
      if (glob(DIR_SYSTEM . '*hpm*.ocmod.xml')) {
        return true;
      }
    }
  
    return false;
  } 
  
  public function isUseProductMaster() {
    return $this->isTableExists('product_master');
  }   
  
  public function isUseTMDCurrency() {
    return $this->isTableExists('product_currency_price');
  }  

  public function isUseProductMultistore() {
    return $this->isTableExists('product_to_multistore');
  }         
  
  // Sync timezones
  public function getTime() {
    $query = $this->db->query("SELECT UNIX_TIMESTAMP() AS time");
    
    return $query->row['time'];
  }

  public function isTableExists($table) {
    $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . $this->db->escape($table) . "'");  
       
    return (bool)$query->num_rows;
  }
  
  public function isColumnExists($table, $column) {
    if (!$this->isTableExists($table)) {
      return false;
    }
    
    $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . $this->db->escape($table) . "` LIKE '" . $this->db->escape($column) . "'");    
    
    return (bool)$query->num_rows;
  } 

  public function isIndexExists($table, $column) {
    $query = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . $this->db->escape($table) . "` WHERE COLUMN_NAME = '" . $this->db->escape($column) . "'");    
    
    return (bool)$query->num_rows;
  }

  public function addIndex($table, $column, $name) {
    $this->db->query("ALTER TABLE `" . DB_PREFIX . $this->db->escape($table) . "` ADD INDEX `" . $this->db->escape($name) . "` (`" . $this->db->escape($column) . "`)");    
  }      
   
  // Install, Upgrade
  public function install() {    
    if ($this->isTableExists('ocfilter_option') && $this->isTableExists('ocfilter_page_description')) {
      return $this->upgradeFrom47();
    } else if ($this->isTableExists('ocfilter_option') && $this->isColumnExists('ocfilter_page', 'ocfilter_params')) {
      return $this->upgradeFrom42();
    } else if (!$this->isTableExists('ocfilter_setting')) {
      $this->dropTable('ocfilter_option');
      $this->dropTable('ocfilter_option_description');
      $this->dropTable('ocfilter_option_value');
      $this->dropTable('ocfilter_option_value_description');
      $this->dropTable('ocfilter_option_to_category');
      $this->dropTable('ocfilter_option_to_store');
      $this->dropTable('ocfilter_option_value_to_product');
      $this->dropTable('ocfilter_option_value_to_product_description');
      $this->dropTable('ocfilter_page');
      $this->dropTable('ocfilter_page_description');
      $this->dropTable('ocfilter_page_old');
      $this->dropTable('ocfilter_page_description_old');
    
      foreach ($this->getCreateTables() as $table => $sql) {
        $this->createTable($table);
      }
    }
  }
  
  public function upgradeFrom47() {
    $s_default = $this->ocfilter->params->source('default')->id();
    $s_attribute = $this->ocfilter->params->source('attribute')->id();
    $s_filter = $this->ocfilter->params->source('filter')->id();
    $s_option = $this->ocfilter->params->source('option')->id();
    
    // Migrate filters
    $this->createTable('ocfilter_filter');    
    
    // Temp columns for old table 
    if (!$this->isColumnExists('ocfilter_option', 'filter_id')) {
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "ocfilter_option` ADD COLUMN `filter_id` INT(11) NOT NULL DEFAULT 0 AFTER `option_id`");      
    }
    
    if (!$this->isColumnExists('ocfilter_option', 'source')) {
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "ocfilter_option` ADD COLUMN `source` TINYINT(1) NOT NULL DEFAULT 0 AFTER `option_id`"); 
    }       
    
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option");
    
    foreach ($query->rows as $result) {    
      $source = $s_default;
        
      $filter_id = $result['option_id'];
        
      if (($result['option_id'] - 10000) > 0) {
        $a_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute WHERE attribute_id = '" . (int)($result['option_id'] - 10000) . "'"); 

        if ($a_query->num_rows) {
          $source = $s_attribute;
            
          $filter_id = $a_query->row['attribute_id'];
        }
      } else if (($result['option_id'] - 5000) > 0) {
        $f_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter_group WHERE filter_group_id = '" . (int)($result['option_id'] - 5000) . "'"); 

        if ($f_query->num_rows) {
          $source = $s_filter;
            
          $filter_id = $f_query->row['filter_group_id'];
        }
      } else {
        $o_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option` WHERE option_id = '" . (int)$result['option_id'] . "'"); 

        if ($o_query->num_rows) {
          $v_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_option_value WHERE option_id = '" . (int)$result['option_id'] . "' LIMIT 5");
          
          $match = 0;
          
          foreach ($v_query->rows as $v_row) {           
            if ($this->db->query("SELECT * FROM " . DB_PREFIX . "option_value WHERE option_value_id = '" . (int)$v_row['value_id'] . "'")->num_rows) {
              $match++;
            }
          }
          
          if ($match > 2) {
            $source = $s_option;
              
            $filter_id = $o_query->row['option_id'];            
          }          
        }
      }
      
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter (`filter_id`, `source`, `type`, `dropdown`, `color`, `image`, `status`, `sort_order`) VALUES ('" . (int)$filter_id . "', '" . (int)$source . "', '" . $this->db->escape($result['type']) . "', '" . (int)$result['selectbox'] . "', '" . (int)$result['color'] . "', '" . (int)$result['image'] . "', '" . (int)$result['status'] . "', '" . (int)$result['sort_order'] . "')");
      
      $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option SET `source` = '" . (int)$source . "', filter_id = '" . (int)$filter_id . "' WHERE option_id = '" . (int)$result['option_id'] . "'");
    }
   
    // Update filter value_id
    if (!$this->isColumnExists('ocfilter_option_value_description', 'new_value_id')) {
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "ocfilter_option_value_description` ADD COLUMN `new_value_id` BIGINT(20) NOT NULL DEFAULT 0 AFTER `value_id`"); 
    }     
    
    $replace = function($rule, $column) {
      $sql = "";
      
      $sql .= str_repeat('REPLACE(', count($rule)) . $column;

      foreach ($rule as $from => $to) {
        $sql .= ", '" . $from . "', '" . $to . "')";
      }
     
      return $sql;
    };    
    
    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_option_value_description oovd LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (oovd.option_id = oo.option_id) SET oovd.new_value_id = CRC32(CONCAT(oo.filter_id, '.', LCASE(" . $replace([ '\r' => '', '\n' => '', '\t' => '', ' ' => '' ], 'oovd.name') . "))) WHERE oo.source = '" . (int)$s_attribute . "' AND oovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");   
   
    // Filter description
    $this->createTable('ocfilter_filter_description'); 
    
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_description (`filter_id`, `source`, `language_id`, `name`, `suffix`, `description`) SELECT oo.filter_id, oo.source, ood.language_id, ood.name, ood.postfix, ood.description FROM " . DB_PREFIX . "ocfilter_option_description ood LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (ood.option_id = oo.option_id)");
       
    // Filter value
    $this->createTable('ocfilter_filter_value'); 
    
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value (`value_id`, `source`, `filter_id`, `color`, `image`, `sort_order`) SELECT IF(oo.source = '" . (int)$s_attribute . "', oovd.new_value_id, IF(oo.source = '" . (int)$s_filter . "', (oov.value_id - 10000), oov.value_id)), oo.source, oo.filter_id, oov.color, oov.image, oov.sort_order FROM " . DB_PREFIX . "ocfilter_option_value oov LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (oov.option_id = oo.option_id) LEFT JOIN " . DB_PREFIX . "ocfilter_option_value_description oovd ON (oov.value_id = oovd.value_id) WHERE oovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

    // Filter value description
    $this->createTable('ocfilter_filter_value_description'); 
    
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_description (`value_id`, `source`, `filter_id`, `language_id`, `name`) SELECT IF(oo.source = '" . (int)$s_attribute . "', oovd2.new_value_id, IF(oo.source = '" . (int)$s_filter . "', (oovd.value_id - 10000), oovd.value_id)), oo.source, oo.filter_id, oovd.language_id, oovd.name FROM " . DB_PREFIX . "ocfilter_option_value_description oovd LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (oovd.option_id = oo.option_id) LEFT JOIN " . DB_PREFIX . "ocfilter_option_value_description oovd2 ON (oovd.value_id = oovd2.value_id) WHERE oovd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

    // Filter value product
    $this->createTable('ocfilter_filter_value_to_product'); 
    
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_to_product (`filter_id`, `value_id`, `source`, `product_id`) SELECT oo.filter_id, IF(oo.source = '" . (int)$s_attribute . "', oovd.new_value_id, IF(oo.source = '" . (int)$s_filter . "', (oov2p.value_id - 10000), oov2p.value_id)), oo.source, oov2p.product_id FROM " . DB_PREFIX . "ocfilter_option_value_to_product oov2p LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (oov2p.option_id = oo.option_id) LEFT JOIN " . DB_PREFIX . "ocfilter_option_value_description oovd ON (oov2p.value_id = oovd.value_id) WHERE oov2p.slide_value_min = '0.0000' AND oov2p.slide_value_max = '0.0000' AND oovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

    $this->createTable('ocfilter_filter_range_to_product'); 

    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_range_to_product (`filter_id`, `source`, `product_id`, `min`, `max`) SELECT oo.filter_id, oo.source, oov2p.product_id, oov2p.slide_value_min, oov2p.slide_value_max FROM " . DB_PREFIX . "ocfilter_option_value_to_product oov2p LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (oov2p.option_id = oo.option_id) WHERE (oov2p.slide_value_min <> '0.0000' OR oov2p.slide_value_max <> '0.0000')");

    // Filter category
    $this->createTable('ocfilter_filter_to_category'); 
    
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_to_category (`filter_id`, `source`, `category_id`) SELECT oo.filter_id, oo.source, oo2c.category_id FROM " . DB_PREFIX . "ocfilter_option_to_category oo2c LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (oo2c.option_id = oo.option_id)");
    
    // Filter store
    $this->createTable('ocfilter_filter_to_store'); 
    
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_to_store (`filter_id`, `source`, `store_id`) SELECT oo.filter_id, oo.source, oo2s.store_id FROM " . DB_PREFIX . "ocfilter_option_to_store oo2s LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (oo2s.option_id = oo.option_id)");

    // SEO Page
    $this->dropTable('ocfilter_page_old');
    $this->renameTable('ocfilter_page', 'ocfilter_page_old');
    $this->createTable('ocfilter_page'); 
    
    $this->dropTable('ocfilter_page_description_old');
    $this->renameTable('ocfilter_page_description', 'ocfilter_page_description_old');
    $this->createTable('ocfilter_page_description');     
    
    $this->createTable('ocfilter_page_to_layout');   
    $this->createTable('ocfilter_page_to_store');   
       
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_page_old");
    
    foreach ($query->rows as $result) {
      if (!$result['category_id'] || !$result['params']) {
        continue;
      }
      
      $params = $this->getParamsByKeywords(explode('/', $result['params']), $result['category_id']);
      
      if ($params) {
        if (!$result['keyword']) {
          $result['keyword'] = str_replace('/', '-', $result['params']);
        } else {
          $result['keyword'] = str_replace('/', '-', $result['keyword']);
        }
        
        $params = $this->ocfilter->params->normalizeArray($params);
        
        $params_key = crc32($this->ocfilter->params->encode($params));
             
        $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_page (`category_id`, `params_key`, `params_count`, `params`, `dynamic`, `status`, `sitemap`, `category`) VALUES ('" . (int)$result['category_id'] . "', '" . $this->db->escape((string)$params_key) . "', '" . (int)count($params) . "', '" . $this->db->escape(json_encode($params)) . "', '0', '" . (int)$result['status'] . "', '1', '1')");
        
        $page_id = $this->db->getLastId();
        
        $page_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_page_description_old WHERE ocfilter_page_id = '" . (int)$result['ocfilter_page_id'] . "'");
        
        foreach ($page_description_query->rows as $page_description_old) {
          $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_page_description (`page_id`, `language_id`, `name`, `heading_title`, `meta_title`, `meta_keyword`, `meta_description`, `description_bottom`) VALUES ('" . (int)$page_id . "', '" . (int)$page_description_old['language_id'] . "', '" . $this->db->escape($page_description_old['title']) . "', '" . $this->db->escape($page_description_old['title']) . "', '" . $this->db->escape($page_description_old['meta_title']) . "', '" . $this->db->escape($page_description_old['meta_keyword']) . "', '" . $this->db->escape($page_description_old['meta_description']) . "', '" . $this->db->escape($page_description_old['description']) . "')");
        } 
        
        foreach ($this->ocfilter->opencart->getStores() as $store_id) {
          $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_page_to_store SET page_id = '" . (int)$page_id . "', store_id = '" . (int)$store_id . "'");
        }  

        if ($this->ocfilter->opencart->version >= 30) {
          foreach ($this->ocfilter->opencart->getLanguages() as $key => $language) {
            foreach ($this->ocfilter->opencart->getStores() as $store_id) {
              $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "seo_url (`query`, `keyword`, `language_id`, `store_id`) VALUES ('ocfilter_page_id=" . (int)$page_id . "', '" . $this->db->escape($result['keyword'] . ($key > 0 ? '-' . $language['code'] : '')) . "', '" . (int)$language['language_id'] . "', '" . (int)$store_id . "')");
            }                     
          }                     
        } else {         
          $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "url_alias (`query`, `keyword`) VALUES ('ocfilter_page_id=" . (int)$page_id . "', '" . $this->db->escape($result['keyword']) . "')");     
        } 
      }     
    } // end each old pages
    
    $this->cache->delete('seo_pro');
    $this->cache->delete('seopro');
    
    $this->dropTable('ocfilter_option');
    $this->dropTable('ocfilter_option_description');
    $this->dropTable('ocfilter_option_value');
    $this->dropTable('ocfilter_option_value_description');
    $this->dropTable('ocfilter_option_to_category');
    $this->dropTable('ocfilter_option_to_store');
    $this->dropTable('ocfilter_option_value_to_product');
    $this->dropTable('ocfilter_option_value_to_product_description');
    $this->dropTable('ocfilter_page_old');
    $this->dropTable('ocfilter_page_description_old');
    
    // New tables
    $this->createTable('ocfilter_attribute_cache'); 
    $this->createTable('ocfilter_cache'); 
    $this->createTable('ocfilter_setting');
  }
  
  public function upgradeFrom42() {
    $this->dropTable('ocfilter_option');
    $this->dropTable('ocfilter_option_description');
    $this->dropTable('ocfilter_option_value');
    $this->dropTable('ocfilter_option_value_description');
    $this->dropTable('ocfilter_option_to_category');
    $this->dropTable('ocfilter_option_to_store');
    $this->dropTable('ocfilter_option_value_to_product');
    $this->dropTable('ocfilter_option_value_to_product_description');
    $this->dropTable('ocfilter_page');
    $this->dropTable('ocfilter_page_description');
    $this->dropTable('ocfilter_page_old');
    $this->dropTable('ocfilter_page_description_old');
  
    foreach ($this->getCreateTables() as $table => $sql) {
      $this->createTable($table);
    }
  }    
  
  public function uninstall() {
    
  }
  
  protected function getCreateTables() {
    $sql = [];
    
    $sql['ocfilter_attribute_cache'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_attribute_cache` (`attribute_cache_id` INT(11) NOT NULL AUTO_INCREMENT, `product_id` INT(11) UNSIGNED NOT NULL, `attribute_id` INT(11) UNSIGNED NOT NULL, `language_id` INT(11) UNSIGNED NOT NULL, `key` BIGINT(20) UNSIGNED NOT NULL, `text` TEXT NOT NULL COLLATE 'utf8_general_ci',PRIMARY KEY (`attribute_cache_id`) USING BTREE,INDEX `language_id` (`language_id`) USING BTREE,INDEX `key` (`key`) USING BTREE,INDEX `attribute_id` (`attribute_id`) USING BTREE,INDEX `product_id` (`product_id`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";    
    $sql['ocfilter_cache'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_cache` (`key` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0', `value` LONGTEXT NOT NULL COLLATE 'utf8_general_ci', `path` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_general_ci', `expire` INT(11) NOT NULL,PRIMARY KEY (`key`) USING BTREE,INDEX `path` (`path`) USING BTREE,INDEX `expire` (`expire`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";     
    $sql['ocfilter_filter'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_filter` (`filter_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, `source` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', `type` SET('checkbox','radio','slide','slide_dual') NOT NULL DEFAULT 'checkbox' COLLATE 'utf8_general_ci', `dropdown` TINYINT(1) NOT NULL DEFAULT '0', `color` TINYINT(1) NOT NULL DEFAULT '0', `image` TINYINT(1) NOT NULL DEFAULT '0', `status` TINYINT(1) NOT NULL DEFAULT '0', `sort_order` INT(11) NOT NULL DEFAULT '0',PRIMARY KEY (`filter_id`, `source`) USING BTREE,INDEX `sort_order` (`sort_order`) USING BTREE,INDEX `slider_status` (`status`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_filter_description'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_filter_description` (`filter_id` INT(11) UNSIGNED NOT NULL, `source` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', `language_id` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0', `name` VARCHAR(128) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci', `suffix` VARCHAR(32) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci', `description` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',PRIMARY KEY (`filter_id`, `language_id`, `source`) USING BTREE,INDEX `language_id` (`language_id`) USING BTREE,INDEX `name` (`name`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_filter_range_to_product'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_filter_range_to_product` (`filter_id` INT(11) UNSIGNED NOT NULL, `source` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', `product_id` INT(11) UNSIGNED NOT NULL, `min` DECIMAL(15,3) NULL DEFAULT NULL, `max` DECIMAL(15,3) NULL DEFAULT NULL,PRIMARY KEY (`filter_id`, `source`, `product_id`) USING BTREE,INDEX `product_id` (`product_id`) USING BTREE,INDEX `min_max` (`min`, `max`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_filter_to_category'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_filter_to_category` (`filter_id` INT(11) UNSIGNED NOT NULL, `source` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', `category_id` INT(11) UNSIGNED NOT NULL,PRIMARY KEY (`category_id`, `filter_id`, `source`) USING BTREE,INDEX `category_id` (`category_id`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_filter_to_store'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_filter_to_store` (`filter_id` INT(11) UNSIGNED NOT NULL, `source` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', `store_id` INT(11) NOT NULL,PRIMARY KEY (`store_id`, `filter_id`, `source`) USING BTREE,INDEX `store_id` (`store_id`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_filter_value'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_filter_value` (`value_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, `source` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', `filter_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', `key` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0', `color` CHAR(6) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci', `image` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci', `sort_order` INT(11) NOT NULL DEFAULT '0',PRIMARY KEY (`value_id`, `source`) USING BTREE,INDEX `option_id` (`filter_id`) USING BTREE,INDEX `sort_order` (`sort_order`) USING BTREE,INDEX `translit` (`key`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_filter_value_description'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_filter_value_description` (`value_id` BIGINT(20) UNSIGNED NOT NULL, `source` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', `language_id` TINYINT(3) UNSIGNED NOT NULL, `filter_id` INT(11) UNSIGNED NOT NULL, `name` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci', `attribute_text` TEXT NOT NULL COLLATE 'utf8_general_ci',PRIMARY KEY (`value_id`, `language_id`, `source`) USING BTREE,INDEX `filter_id` (`filter_id`) USING BTREE,INDEX `language_id` (`language_id`) USING BTREE,INDEX `name` (`name`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_filter_value_to_product'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_filter_value_to_product` (`filter_id` INT(11) UNSIGNED NOT NULL, `value_id` BIGINT(20) UNSIGNED NOT NULL, `source` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', `product_id` INT(11) UNSIGNED NOT NULL,PRIMARY KEY (`filter_id`, `value_id`, `source`, `product_id`) USING BTREE,INDEX `product_id` (`product_id`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_page'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_page` (`page_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, `category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', `dynamic_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', `params_key` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0', `params_count` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0', `params` TEXT NOT NULL COLLATE 'utf8_general_ci', `dynamic` TINYINT(1) NOT NULL DEFAULT '0', `module` TINYINT(1) NOT NULL DEFAULT '0', `sitemap` TINYINT(1) NOT NULL DEFAULT '0', `category` TINYINT(1) NOT NULL DEFAULT '0', `product` TINYINT(1) NOT NULL DEFAULT '0', `status` TINYINT(1) NOT NULL DEFAULT '0',PRIMARY KEY (`page_id`) USING BTREE,INDEX `category_id` (`category_id`) USING BTREE,INDEX `params_key` (`params_key`) USING BTREE,INDEX `params_count` (`params_count`) USING BTREE,INDEX `dynamic_id` (`dynamic_id`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_page_description'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_page_description` (`page_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', `language_id` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0', `name` VARCHAR(128) NOT NULL COLLATE 'utf8_general_ci', `heading_title` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci', `meta_title` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci', `meta_keyword` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci', `meta_description` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci', `description_top` TEXT NOT NULL COLLATE 'utf8_general_ci', `description_bottom` TEXT NOT NULL COLLATE 'utf8_general_ci',PRIMARY KEY (`page_id`, `language_id`) USING BTREE,INDEX `language_id` (`language_id`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_page_to_layout'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_page_to_layout` (`page_id` INT(11) UNSIGNED NOT NULL, `store_id` INT(11) UNSIGNED NOT NULL, `layout_id` INT(11) UNSIGNED NOT NULL,PRIMARY KEY (`page_id`, `store_id`) USING BTREE,INDEX `store_id` (`store_id`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_page_to_store'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_page_to_store` (`page_id` INT(11) UNSIGNED NOT NULL, `store_id` INT(11) UNSIGNED NOT NULL,PRIMARY KEY (`store_id`, `page_id`) USING BTREE,INDEX `store_id` (`store_id`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";      
    $sql['ocfilter_setting'] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ocfilter_setting` (`key` VARCHAR(64) NOT NULL COLLATE 'utf8_general_ci', `serialized` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', `value` LONGTEXT NOT NULL COLLATE 'utf8_general_ci',PRIMARY KEY (`key`) USING BTREE) COLLATE='utf8_general_ci' ENGINE=MyISAM";
  
    return $sql;
  }
  
  protected function getCreateTable($table) {    
    return $this->getCreateTables()[$table];
  }     
  
  protected function createTable($table) {
    $this->db->query($this->getCreateTable($table)); 
  }
   
  protected function renameTable($table, $name) {
    $this->db->query("RENAME TABLE `" . DB_PREFIX . $table . "` TO `" . DB_PREFIX . $name . "`");
  }    
  
  protected function dropTable($table) {
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $table . "`");
  }  
  
  // Migrate helper
  private function isRange($string) {
    return preg_match('/^(-|)(\d+\.)?\d+?\-(-|)(\d+\.)?\d+?$/', $string);
  }

  private function isID($string) {
    return preg_match('/^[0-9]+?$/', $string);
  }
  
  private function getParamsByKeywords($keywords, $category_id) {
    $price = $this->ocfilter->params->special('price')->key();
    $stock = $this->ocfilter->params->special('stock')->key();
    $manufacturer = $this->ocfilter->params->special('manufacturer')->key();
    
    $params = [];
         
    // Special filters
    foreach ($keywords as $key => $keyword) {
      if ($keyword == 'price') {
        unset($keywords[$key++]);

        if (isset($keywords[$key])) {
          $params[$price] = $keywords[$key];

          unset($keywords[$key]);
        }
      } else if ($keyword == 'sklad' && isset($keywords[$key + 1]) && !$this->isID($keywords[$key + 1])) {
        unset($keywords[$key++]);

        $params[$stock] = [ ($keywords[$key] == 'in' ? 1 : 2) ];

        unset($keywords[$key]);
      }
    }

    $current = '';
    $current_key = '';

    foreach ($keywords as $key => $keyword) {
      $founded = 0;

      // Values
      if ($current_key == $stock && $this->isID($keyword)) {         
        $params[$stock][] = $keyword;

        $founded = 1;
      } else if ($current) {
        $value_id = $this->decodeValue($keyword, $current);

        if ($value_id) {
          $params[$current_key][] = $value_id;

          $founded = 1;
        } else if ($this->isRange($keyword)) {
          $params[$current_key] = $keyword;

          $founded = 2;
        }
      }

      if ($founded > 0) {
        if ($founded > 1) {
          $current = '';
        }

        unset($keywords[$key]);

        continue;
      }

      // Options
      if ($keyword == 'sklad') {
        $params[$stock] = [];

        $current_key = $stock;

        unset($keywords[$key]);
      } else if (!$this->isRange($keyword)) {
        $option_info = $this->decodeOption($keyword, $category_id);

        if ($option_info) {
          $current_key = $option_info['filter_id'] . '.' . $option_info['source'];
          $current = $option_info['option_id'];            
          
          $params[$current_key] = [];

          unset($keywords[$key]);
        }
      }
    }

    // Manufacturer
    foreach ($keywords as $key => $keyword) {      
      $manufacturer_id = $this->decodeManufacturer($keyword);

      if ($manufacturer_id) {
        if (!isset($params[$manufacturer])) {
          $params[$manufacturer] = [];
        }

        $params[$manufacturer][] = $manufacturer_id;

        unset($keywords[$key]);
      }
    }  

    return $params;
  }
  
  public function decodeOption($keyword, $category_id) {
    // Get Option by keyword
    $query = $this->db->query("SELECT oo.option_id, oo.filter_id, oo.source FROM " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_to_category oo2c ON (oo.option_id = oo2c.option_id) LEFT JOIN " . DB_PREFIX . "category_path cp ON (oo2c.category_id = cp.category_id) WHERE oo.status = '1' AND oo.`keyword` = '" . $this->db->escape($keyword) . "' AND cp.path_id = '" . (int)$category_id . "' LIMIT 1");

    // Get Option by ID
    if (!$query->num_rows && $this->isID($keyword)) {
      $query = $this->db->query("SELECT oo.option_id, oo.filter_id, oo.source FROM " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_to_category oo2c ON (oo.option_id = oo2c.option_id) LEFT JOIN " . DB_PREFIX . "category_path cp ON (oo2c.category_id = cp.category_id) WHERE oo.status = '1' AND oo.option_id = '" . (int)$keyword . "' AND cp.path_id = '" . (int)$category_id . "'");
    }

    if ($query->num_rows) {
      return $query->row;
    } else {
      return 0;
    }
  }

  public function decodeValue($keyword, $option_id) {
    $s_attribute = $this->ocfilter->params->source('attribute')->id();
    $s_filter = $this->ocfilter->params->source('filter')->id();

    $query = $this->db->query("SELECT oov.value_id, oovd.new_value_id, oo.source FROM " . DB_PREFIX . "ocfilter_option_value oov LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (oov.option_id = oo.option_id) LEFT JOIN " . DB_PREFIX . "ocfilter_option_value_description oovd ON (oov.value_id = oovd.value_id) WHERE oov.option_id = '" . (int)$option_id . "' AND oov.`keyword` = '" . $this->db->escape($keyword) . "' AND oovd.language_id = '" . (int)$this->config->get('config_language_id'). "' LIMIT 1");

    // If keyword is ID
    if (!$query->num_rows && $this->isID($keyword)) {
      $query = $this->db->query("SELECT oov.value_id, oovd.new_value_id, oo.source FROM " . DB_PREFIX . "ocfilter_option_value oov LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (oov.option_id = oo.option_id) LEFT JOIN " . DB_PREFIX . "ocfilter_option_value_description oovd ON (oov.value_id = oovd.value_id) WHERE oov.value_id = '" . $this->db->escape((string)$keyword) . "' AND oovd.language_id = '" . (int)$this->config->get('config_language_id'). "'");
    }

    if (!empty($query->row['value_id'])) {
      $value_id = $query->row['value_id'];
      
      if ($query->row['source'] == $s_attribute) {
        $value_id = $query->row['new_value_id'];
      } else if ($query->row['source'] == $s_filter) {
        $value_id -= 10000;
      }
      
      return $value_id;
    } else {
      return 0;
    }
  }

  public function decodeManufacturer($keyword) {
    if ($this->ocfilter->opencart->version >= 30) {
      $query = $this->db->query("SELECT REPLACE(`query`, 'manufacturer_id=', '') AS manufacturer_id FROM " . DB_PREFIX . "seo_url WHERE `query` LIKE 'manufacturer_id=%' AND LCASE(`keyword`) = '" . $this->db->escape(utf8_strtolower($keyword)) . "' LIMIT 1"); 
    } else {
      $query = $this->db->query("SELECT REPLACE(`query`, 'manufacturer_id=', '') AS manufacturer_id FROM " . DB_PREFIX . "url_alias WHERE `query` LIKE 'manufacturer_id=%' AND LCASE(`keyword`) = '" . $this->db->escape(utf8_strtolower($keyword)) . "' LIMIT 1");
    }

    if (!$query->num_rows && $this->isID($keyword)) {
      $query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$keyword . "'");
    }

    if (!empty($query->row['manufacturer_id'])) {
      return $query->row['manufacturer_id'];
    } else {
      return 0;
    }
  }  
  
  // OpenCart Common methods
	public function getSetting($code, $store_id = 0) {
		$setting_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$setting_data[$result['key']] = $result['value'];
			} else {
				$setting_data[$result['key']] = json_decode($result['value'], true);
			}
		}

		return $setting_data;
	}
  
	public function getSettingValue($key, $store_id = 0) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");

		if ($query->num_rows) {
			return $query->row['value'];
		} else {
			return null;	
		}
	}
  
	public function editSettingValue($code = '', $key = '', $value = '', $store_id = 0) {
		if (!is_array($value)) {
			$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape($value) . "', serialized = '0'  WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape(json_encode($value)) . "', serialized = '1' WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		}
	}  

	public function getEventByCode($code) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "event` WHERE `code` = '" . $this->db->escape($code) . "' LIMIT 1");

		return $query->row;
	}      
}