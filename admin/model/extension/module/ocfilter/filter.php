<?php

class ModelExtensionModuleOCFilterFilter extends Model {
  private $logger;
  private $time_start;
  private $time_last;

  public function __construct($registry) {
    parent::__construct($registry);

    if (is_file(DIR_LOGS . 'ocfilter.log') && filesize(DIR_LOGS . 'ocfilter.log') > 1024 * 1024 * 1) { // 1Mb max
      rename(DIR_LOGS . 'ocfilter.log', DIR_LOGS . 'ocfilter-' . date('Y-m-d_H-i-s') . '.log');
    }
    
    $logs = glob(DIR_LOGS . 'ocfilter-*', GLOB_NOSORT);
    
    if ($logs && count($logs) > 5) {
      foreach (array_slice($logs, 5) as $log) {
        unlink($log);
      }
    }

    $this->logger = new Log('ocfilter.log');
    
    $this->time_start = $this->time_last = microtime(true);
  }

  private function writeLog($message) {       
    $this->logger->write(number_format((microtime(true) - $this->time_last), 3) . ' sec.');
    $this->logger->write($message);
    
    $this->time_last = microtime(true);    
  }

  public function addFilter($data) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter SET source = '" . (int)$this->ocfilter->params->source('default')->id() . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', sort_order = '" . (int)$data['sort_order'] . "', type = '" . $this->db->escape($data['type']) . "', dropdown = '" . (isset($data['dropdown']) ? (int)$data['dropdown'] : 0) . "', color = '" . (isset($data['color']) ? (int)$data['color'] : 0) . "', image = '" . (isset($data['image']) ? (int)$data['image'] : 0) . "'");

    $filter_id = $this->db->getLastId();

    foreach ($data['filter_description'] as $language_id => $value) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_description SET filter_id = '" . (int)$filter_id . "', source = " . (int)$this->ocfilter->params->source('default')->id() . ", language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', suffix = '" . $this->db->escape($value['suffix']) . "'");
    }

    if (isset($data['filter_category'])) {
      if (array_key_exists(0, $data['filter_category'])) {
        $data['filter_category'] = [ 'all' ];
      }
      
      foreach ($data['filter_category'] as $category_id => $name) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_to_category SET filter_id = '" . (int)$filter_id . "', source = " . (int)$this->ocfilter->params->source('default')->id() . ", category_id = '" . (int)$category_id . "'");
      }
    }

    if (isset($data['filter_store'])) {
      foreach ($data['filter_store'] as $store_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_to_store SET filter_id = '" . (int)$filter_id . "', source = " . (int)$this->ocfilter->params->source('default')->id() . ", store_id = '" . (int)$store_id . "'");
      }
    }

    if (!empty($data['filter_value']) && is_array($data['filter_value'])) {
      foreach ($data['filter_value'] as $value) {
        $this->addFilterValue($filter_id, $this->ocfilter->params->source('default')->id(), $value);
      }
    }

    return $filter_id . '.' . $this->ocfilter->params->source('default')->id();
  }

  public function editFilter($filter_key, $data) {
    $filter_info = $this->getFilter($filter_key);
    
    if (!$filter_info) {
      return false;
    }
    
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source);
    
    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_filter SET status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', sort_order = '" . (int)$data['sort_order'] . "', type = '" . $this->db->escape($data['type']) . "', dropdown = '" . (isset($data['dropdown']) ? (int)$data['dropdown'] : 0) . "', color = '" . (isset($data['color']) ? (int)$data['color'] : 0) . "', image = '" . (isset($data['image']) ? (int)$data['image'] : 0) . "' WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_description WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);

    foreach ($data['filter_description'] as $language_id => $value) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_description SET filter_id = '" . (int)$filter_id . "', source = " . (int)$source . ", language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', suffix = '" . $this->db->escape($value['suffix']) . "'");
    }

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_to_category WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);

    if (isset($data['filter_category'])) {
      if (array_key_exists(0, $data['filter_category'])) {
        $data['filter_category'] = [ 'all' ];
      }
      
      foreach ($data['filter_category'] as $category_id => $name) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_to_category SET filter_id = '" . (int)$filter_id . "', source = " . (int)$source . ", category_id = '" . (int)$category_id . "'");
      }
    }

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_to_store WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);

    if (isset($data['filter_store'])) {
      foreach ($data['filter_store'] as $store_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_to_store SET filter_id = '" . (int)$filter_id . "', source = " . (int)$source . ", store_id = '" . (int)$store_id . "'");
      }
    }

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value_description WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);

    if (!empty($data['filter_value']) && is_array($data['filter_value'])) {
      foreach ($data['filter_value'] as $value) {
        $this->addFilterValue($filter_id, $source, $value);
      }
    }

    /* Until better times
    if (!$data['status'] && $filter_info['status']) {
      $this->deactivateFilter($filter_key);
    }
    
    if ($data['status'] && !$filter_info['status']) {
      $this->activateFilter($filter_key);
    }    
    */

    if ($data['status'] && ($data['type'] == 'slide' || $data['type'] == 'slide_dual')) {
      $this->convertFilterToSlider($filter_id, $source);
    }

    $this->ocfilter->cache->key('filter', $filter_id, $source)->delete();
  }
  
  public function editFilterImmediately($filter_key, $data) {    
    $filter_info = $this->getFilter($filter_key);
    
    if (!$filter_info) {
      return false;
    }

    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source);  

    if ($data['field'] == 'name') {
      $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_filter_description SET name = '" . $this->db->escape(urldecode($data['value'])) . "' WHERE filter_id = '" . (int)$filter_id . "' AND source = '" . (int)$source . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
    } else if ($data['field'] == 'type') {
      $slider = ($data['value'] == 'slide' || $data['value'] == 'slide_dual');
      
      $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_filter SET `type` = '" . $this->db->escape($data['value']) . "' WHERE filter_id = '" . (int)$filter_id . "' AND source = '" . (int)$source . "'");
      
      if ($filter_info['status'] && $slider) {
        $this->convertFilterToSlider($filter_id, $source);
      }
    } else {
      $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_filter SET `" . $this->db->escape($data['field']) . "` = '" . $this->db->escape($data['value']) . "' WHERE filter_id = '" . (int)$filter_id . "' AND source = '" . (int)$source . "'");
    }
    
    /* Until better times
    if ($data['field'] == 'status') {
      if (!$data['value'] && $filter_info['status']) {
        $this->deactivateFilter($filter_key);
      }
      
      if ($data['value'] && !$filter_info['status']) {
        $this->activateFilter($filter_key);
        
        if ($filter_info['type'] == 'slide' || $filter_info['type'] == 'slide_dual') {
          $this->convertFilterToSlider($filter_id, $source);
        }
      }      
    }
    */
   
    $this->ocfilter->cache->key('filter', $filter_id, $source)->delete();
    
    return true;
  }
  
  protected function activateFilter($filter_key) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source);
    
    if ($this->ocfilter->params->source($source)->is('attribute')) {
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_to_product (filter_id, source, value_id, product_id) SELECT " . (int)$filter_id . ", " . (int)$source . ", `key`, product_id FROM " . DB_PREFIX . "ocfilter_attribute_cache WHERE attribute_id = " . (int)$filter_id . " AND language_id = '" . (int)$this->config->get('config_language_id') . "'"); 
    } else if ($this->ocfilter->params->source($source)->is('option')) {
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_to_product (filter_id, source, value_id, product_id) SELECT " . (int)$filter_id . ", " . (int)$source . ", option_value_id, product_id FROM " . DB_PREFIX . "product_option_value WHERE option_id = " . (int)$filter_id . " AND quantity > '0'");
    } else if ($this->ocfilter->params->source($source)->is('filter')) {
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_to_product (filter_id, source, value_id, product_id) SELECT " . (int)$filter_id . ", " . (int)$source . ", pf.filter_id, pf.product_id FROM " . DB_PREFIX . "product_filter pf LEFT JOIN " . DB_PREFIX . "filter f ON (pf.filter_id = f.filter_id) WHERE f.filter_group_id = " . (int)$filter_id);
    }       
  }
  
  protected function deactivateFilter($filter_key) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source);
    
    // Do not delete manual adding filter relations
    if (!$this->ocfilter->params->source($source)->is('default')) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value_to_product WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);
      $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_range_to_product WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);      
    }    
  }  
  
  protected function addFilterValue($filter_id, $source, $data) {
    $insert = [
      'filter_id' => $filter_id,
      'source' => $source,
      'sort_order' => (int)$data['sort_order'],
      'color' => $this->db->escape($data['color']),
      'image' => $this->db->escape($data['image']),
    ];

    if (!empty($data['value_id'])) {
      $insert['value_id'] = $this->db->escape((string)$data['value_id']);
    }
      
    $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_value (`" . implode('`,`', array_keys($insert)) . "`) VALUES ('" . implode("','", $insert) . "')");

    if (empty($data['value_id'])) {
      $data['value_id'] = (string)$this->db->getLastId();
    }    

    $insert = [];

    foreach ($data['description'] as $language_id => $description) {
      $insert[] = "'" . $this->db->escape($data['value_id']) . "','" . (int)$filter_id . "','" . (int)$source . "','" . (int)$language_id . "','" . $this->db->escape($description['name']) . "'";
    }
    
    if ($insert) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_value_description (`value_id`,`filter_id`,`source`,`language_id`,`name`) VALUES (" . implode("),(", $insert) . ")");
    }    
  }

  public function deleteFilter($filter_key) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source);
    
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_description WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_to_category WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_to_store WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value_to_product WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_range_to_product WHERE filter_id = '" . (int)$filter_id . "' AND source = " . (int)$source);
    
    // Delete page
    $this->load->model('extension/module/ocfilter/page');
    
    $query = $this->db->query("SELECT page_id FROM " . DB_PREFIX . "ocfilter_page WHERE params LIKE '%\"" . $this->db->escape($filter_key) . "\"%'");
   
    foreach ($query->rows as $result) {
      $this->model_extension_module_ocfilter_page->deletePage($result['page_id']);
    }
   
    $this->ocfilter->cache->key('filter', $filter_id, $source)->delete();
  }
  
  public function convertFilterToSlider($filter_id, $source) {        
    $insertRows = function($values) {
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_range_to_product (filter_id, source, product_id, `min`, `max`) VALUES (" . implode("),(", $values) . ")");
    };
    
    $has_update = false;
    
    $insert = [];
    
    $i = 0;
    
    $query = $this->db->query("SELECT fv2p.product_id, GROUP_CONCAT(fvd.name SEPARATOR '|') AS `values` FROM " . DB_PREFIX . "ocfilter_filter_value_to_product fv2p LEFT JOIN " . DB_PREFIX . "ocfilter_filter_value_description fvd ON (fv2p.source = fvd.source AND fv2p.value_id = fvd.value_id) WHERE fv2p.filter_id = '" . (int)$filter_id . "' AND fv2p.source = '" . (int)$source . "' AND fvd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY fv2p.product_id");   
    
    foreach ($query->rows as $key => $result) {
      $numbers = [];
      
      foreach (explode('|', $result['values']) as $value) {
        if (false !== ($range = $this->ocfilter->params->parseRange($value))) {   
          $numbers = array_merge($numbers, $range);
        }
      }
      
      $numbers = array_filter($numbers, 'strlen');
      
      if ($numbers) {
        $min = min($numbers);
        $max = max($numbers);
        
        if (floatval($min) != 0 || floatval($max) != 0) {
          $insert[] = "'" . (int)$filter_id . "','" . (int)$source . "','" . $this->db->escape($result['product_id']) . "','" . (float)$min . "','" . (float)$max . "'";
        
          $i++;           
        }
      }     
      
      if ($i > 250) {
        $insertRows($insert);
        
        $insert = [];
        
        $i = 0;
        
        $has_update = true;
      }     

      unset($query->rows[$key]);
    }
    
    if ($insert) {
      $insertRows($insert);
      
      $has_update = true;
    }

    if ($has_update) {     
      $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value WHERE filter_id = '" . (int)$filter_id . "' AND source = '" . (int)$source . "'");
      $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value_description WHERE filter_id = '" . (int)$filter_id . "' AND source = '" . (int)$source . "'");
      $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value_to_product WHERE filter_id = '" . (int)$filter_id . "' AND source = '" . (int)$source . "'");   
    }
  }  

  public function getFilter($filter_key) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
    
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_filter f LEFT JOIN " . DB_PREFIX . "ocfilter_filter_description fd ON (f.filter_id = fd.filter_id AND f.source = fd.source) WHERE f.filter_id = '" . (int)$filter_id . "' AND f.source = '" . (int)$source . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

    return $query->row;
  }

  public function getFilters($data = []) {
    $sql = "SELECT CONCAT(f.filter_id, '.', f.source) AS filter_key, f.filter_id, f.source, fd.name, fd.suffix, f.type, f.status, f.sort_order, f.dropdown";
    
    if (!isset($data['autocomplete'])) {
      $sql .= ", IF((f.type = 'slide' OR f.type = 'slide_dual'), 
                     (SELECT COUNT(DISTINCT fr2p.`min`) FROM " . DB_PREFIX . "ocfilter_filter_range_to_product fr2p WHERE fr2p.filter_id = f.filter_id AND fr2p.source = f.source),
                     (SELECT COUNT(*) FROM " . DB_PREFIX . "ocfilter_filter_value fv WHERE fv.filter_id = f.filter_id AND fv.source = f.source)
                 ) AS total_values";  
    }
    
    $sql .= " FROM " . DB_PREFIX . "ocfilter_filter f LEFT JOIN " . DB_PREFIX . "ocfilter_filter_description fd ON (f.filter_id = fd.filter_id AND f.source = fd.source)";

    if (isset($data['filter_category_id'])) {
      $sql .= " LEFT JOIN " . DB_PREFIX . "ocfilter_filter_to_category f2c ON (f.filter_id = f2c.filter_id AND f.source = f2c.source)";
    }
    
    if (!empty($data['filter_name'])) {
      $sql .= " LEFT JOIN " . DB_PREFIX . "ocfilter_filter_value_description fvd ON (f.filter_id = fvd.filter_id AND f.source = fvd.source)";
    }

    $sql .= " WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (isset($data['filter_category_id'])) {
      if (is_array($data['filter_category_id'])) {
        $data['filter_category_id'][] = 0;
        
        $sql .= " AND f2c.category_id IN('" . implode("','", array_map('intval', array_unique($data['filter_category_id']))) . "')";
      } else {
        if ($data['filter_category_id'] > 0) { // Regular search
          $sql .= " AND (f2c.category_id = '" . (int)$data['filter_category_id'] . "' OR f2c.category_id = '0')"; // Include filters with all categories
        } else if ($data['filter_category_id'] < 0) {
          $sql .= " AND f2c.category_id IS NULL"; // Not has categories
        } else {
          $sql .= " AND f2c.category_id = '0'"; // Only with all categories
        }          
      }      
    }

    if (!empty($data['filter_type'])) {
      $sql .= " AND f.type = '" . $this->db->escape($data['filter_type']) . "'";
    } else if (!empty($data['filter_ignore_slide'])) {
      $sql .= " AND f.type NOT IN('slide', 'slide_dual')";
    }    

    if (!empty($data['filter_name'])) {
      $sql .= " AND (LCASE(fd.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%' OR LCASE(fvd.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%')";
    }

    if (isset($data['filter_status'])) {
      $sql .= " AND f.status = '" . (int)$data['filter_status'] . "'";
    }
    
    if (isset($data['filter_source'])) {
      $sql .= " AND f.source = '" . (int)$this->ocfilter->params->source($data['filter_source'])->id() . "'";
    }

    $sql .= " GROUP BY f.filter_id, f.source";

    $sort_data = [
      'total_values',
      'numeric',
      'f.sort_order',
      'fd.name'
    ];

    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
      if ($data['sort'] == 'numeric') {
        $sql .= " ORDER BY ((SELECT AVG(ABS(fvd2.name + 0)) FROM " . DB_PREFIX . "ocfilter_filter_value_description fvd2 WHERE fvd2.filter_id = f.filter_id AND fvd2.source = f.source AND fvd2.name <> '') < 10), total_values DESC";
      } else {
        $sql .= " ORDER BY " . $data['sort'];
        
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
          $sql .= " DESC";
        } else {
          $sql .= " ASC";
        }        
      }            
    } else if (!isset($data['autocomplete'])) {
      $sql .= " ORDER BY f.sort_order, fd.name";
    }

    if (isset($data['start']) || isset($data['limit'])) {
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

  public function getTotalFilters($data = []) {
    $sql = "SELECT COUNT(DISTINCT CONCAT(f.filter_id, '.', f.source)) AS total FROM " . DB_PREFIX . "ocfilter_filter f LEFT JOIN " . DB_PREFIX . "ocfilter_filter_description fd ON (f.filter_id = fd.filter_id AND f.source = fd.source)";

    if (isset($data['filter_category_id'])) {
      $sql .= " LEFT JOIN " . DB_PREFIX . "ocfilter_filter_to_category f2c ON (f.filter_id = f2c.filter_id AND f.source = f2c.source)";
    }
    
    if (!empty($data['filter_name'])) {
      $sql .= " LEFT JOIN " . DB_PREFIX . "ocfilter_filter_value_description fvd ON (f.filter_id = fvd.filter_id AND f.source = fvd.source)";
    }   

    $sql .= " WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (isset($data['filter_category_id'])) {
      if (is_array($data['filter_category_id'])) {
        $data['filter_category_id'][] = 0;
        
        $sql .= " AND f2c.category_id IN('" . implode("','", array_map('intval', array_unique($data['filter_category_id']))) . "')";
      } else {
        if ($data['filter_category_id'] > 0) { // Regular search
          $sql .= " AND (f2c.category_id = '" . (int)$data['filter_category_id'] . "' OR f2c.category_id = '0')"; // Include filters with all categories
        } else if ($data['filter_category_id'] < 0) {
          $sql .= " AND f2c.category_id IS NULL"; // Not has categories
        } else {
          $sql .= " AND f2c.category_id = '0'"; // Only with all categories
        }          
      }      
    }

    if (!empty($data['filter_type'])) {
      $sql .= " AND f.type = '" . $this->db->escape($data['filter_type']) . "'";
    } else if (!empty($data['filter_ignore_slide'])) {
      $sql .= " AND f.type NOT IN('slide', 'slide_dual')";
    } 

    if (!empty($data['filter_name'])) {
      $sql .= " AND (LCASE(fd.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%' OR LCASE(fvd.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%')";
    }

    if (isset($data['filter_status'])) {
      $sql .= " AND f.status = '" . (int)$data['filter_status'] . "'";
    }
    
    if (isset($data['filter_source'])) {
      $sql .= " AND f.source = '" . (int)$this->ocfilter->params->source($data['filter_source'])->id() . "'";
    }    

    $query = $this->db->query($sql);

    return $query->row['total'];
  }
  
  public function getFilterDescriptions($filter_key) { 
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
    
    $filter_description_data = [];

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_filter_description WHERE filter_id = '" . (int)$filter_id . "' AND source = '" . (int)$source . "'");

    foreach ($query->rows as $result) {
      $filter_description_data[$result['language_id']] = [
        'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
        'description' => strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),
        'suffix'      => strip_tags(html_entity_decode($result['suffix'], ENT_QUOTES, 'UTF-8'))
      ];
    }

    return $filter_description_data;
  }    

  public function getFilterCategories($filter_key) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
    
    $query = $this->db->query("SELECT f2c.category_id, cd.name FROM " . DB_PREFIX . "ocfilter_filter_to_category f2c LEFT JOIN " . DB_PREFIX . "category_description cd ON (f2c.category_id = cd.category_id) WHERE f2c.filter_id = '" . (int)$filter_id . "' AND f2c.source = '" . (int)$source . "' AND (cd.language_id = '" . (int)$this->config->get('config_language_id') . "' OR cd.language_id IS NULL)");

    return array_combine(array_column($query->rows, 'category_id'), array_column($query->rows, 'name'));
  }
  
  // @autocomplete
  public function getFilterCategory($filter_key) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
    
    $query = $this->db->query("SELECT f2c.category_id, cd.name FROM " . DB_PREFIX . "ocfilter_filter_to_category f2c LEFT JOIN " . DB_PREFIX . "category_description cd ON (f2c.category_id = cd.category_id) WHERE f2c.filter_id = '" . (int)$filter_id . "' AND f2c.source = '" . (int)$source . "' AND (cd.language_id = '" . (int)$this->config->get('config_language_id') . "' OR cd.language_id IS NULL) ORDER BY cd.name LIMIT 1");

    return $query->row;
  }  

  public function getFilterStores($filter_key) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
    
    $query = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "ocfilter_filter_to_store WHERE filter_id = '" . (int)$filter_id . "' AND source = '" . (int)$source . "'");

    return array_column($query->rows, 'store_id');
  }

  public function getProductValues($product_id) {
    $query = $this->db->query("SELECT *, CONCAT(filter_id, '.', source) AS filter_key FROM " . DB_PREFIX . "ocfilter_filter_value_to_product WHERE product_id = '" . (int)$product_id . "'");

    return $query->rows;
  }
  
  public function getProductRangeValues($product_id) {
    $query = $this->db->query("SELECT *, CONCAT(filter_id, '.', source) AS filter_key FROM " . DB_PREFIX . "ocfilter_filter_range_to_product WHERE product_id = '" . (int)$product_id . "'");

    return $query->rows;
  }  
  
  // @filter list
  public function getFilterValuesCondensed($filter_key, $limit = 5) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
    
    $query = $this->db->query("SELECT name FROM " . DB_PREFIX . "ocfilter_filter_value_description WHERE filter_id = '" . (int)$filter_id . "' AND source = '" . (int)$source . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "' AND name <> '' ORDER BY LENGTH(name), name LIMIT " . (int)$limit);    
    
    return array_column($query->rows, 'name');
  }
     
  public function getFilterValue($value_id, $source) {  
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_filter_value fv LEFT JOIN " . DB_PREFIX . "ocfilter_filter_value_description fvd ON (fv.value_id = fvd.value_id) WHERE fv.value_id = '" . $this->db->escape((string)$value_id) . "' AND fv.source = '" . (int)$source . "' AND fvd.source = '" . (int)$source . "' AND fvd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
    
    return $query->row;
  }
     
  // @filter relation form, autocomplete
  public function getFilterValues($data = []) {   
    $sql = "SELECT fv.value_id, fv.filter_id, fvd.name, CONCAT(fv.filter_id, '.', fv.source) AS filter_key FROM " . DB_PREFIX . "ocfilter_filter_value fv LEFT JOIN " . DB_PREFIX . "ocfilter_filter_value_description fvd ON (fv.source = fvd.source AND fv.value_id = fvd.value_id) WHERE fvd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
    
    if (!empty($data['filter_key'])) {
      $this->ocfilter->params->key($data['filter_key'])->expand($filter_id, $source);       
      
      $sql .= " AND fv.filter_id = '" . (int)$filter_id . "' AND fv.source = '" . (int)$source . "'";
    }
    
    if (!empty($data['filter_name'])) {
      $sql .= " AND LCASE(fvd.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
    } else {
      $sql .= " AND fvd.name <> ''";
    }

    $sql .= " ORDER BY ";

    if (!empty($data['filter_name'])) {
      $sql .= " LENGTH(fvd.name), ";
    }

    $sql .= "fvd.name";
    
    if (isset($data['start']) || isset($data['limit'])) {
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
  
  // @filter form
  public function getFilterValuesDescriptions($filter_key) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
    
    $query = $this->db->query("SELECT fv.value_id, fv.sort_order, fv.image, fv.color, GROUP_CONCAT(CONCAT(fvd.language_id, '{ln}', fvd.name) SEPARATOR '{v}') AS lang_name FROM " . DB_PREFIX . "ocfilter_filter_value fv LEFT JOIN " . DB_PREFIX . "ocfilter_filter_value_description fvd ON (fvd.source = fv.source AND fvd.value_id = fv.value_id) WHERE fv.filter_id = '" . (int)$filter_id . "' AND fv.source = '" . (int)$source . "' GROUP BY fv.value_id ORDER BY fv.sort_order, (fvd.language_id != '" . (int)$this->config->get('language_id') . "'), fvd.name");

    foreach ($query->rows as $key => $row) {
      $query->rows[$key]['description'] = [];  
      
      foreach (explode('{v}', $row['lang_name']) as $lang_name) {
        list($language_id, $name) = explode('{ln}', $lang_name);
        
        $query->rows[$key]['description'][$language_id] = [ 'name' => $name ];
      }
      
      unset($query->rows[$key]['lang_name']);
    }

    return $query->rows;
  }
  
  //@page generator
  public function getFilterValueDescriptions($filter_key, $value_id) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
    
    $query = $this->db->query("SELECT language_id, name FROM " . DB_PREFIX . "ocfilter_filter_value_description WHERE value_id = '" . $this->db->escape((string)$value_id) . "' AND source = '" . (int)$source . "'");

    return array_combine(array_column($query->rows, 'language_id'), array_column($query->rows, 'name'));
  }
  
  public function getAllFilterValueDescriptions($filter_key) {
    $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
    
    $value_description_data = [];

    $query = $this->db->query("SELECT value_id, GROUP_CONCAT(CONCAT(language_id, '{ln}', name) SEPARATOR '{v}') AS lang_name FROM " . DB_PREFIX . "ocfilter_filter_value_description WHERE filter_id = '" . (int)$filter_id . "' AND source = '" . (int)$source . "' GROUP BY value_id");

    foreach ($query->rows as $key => $row) {
      $value_description_data[$row['value_id']] = [];
            
      foreach (explode('{v}', $row['lang_name']) as $lang_name) {
        list($language_id, $name) = explode('{ln}', $lang_name);
        
        $value_description_data[$row['value_id']][$language_id] = $name;
      }
      
      unset($query->rows[$key]);
    }

    return $value_description_data;
  }       
  
  // Deprecated
  /*
  public function getCategories($parent_id, $level = -1) {
    $level++;

    $results = $this->getCategoriesByParentId($parent_id);

    $categories_data = [];

    foreach ($results as $result) {
      $categories_data[] = [
        'category_id' => $result['category_id'],
        'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
        'level'       => $level
      ];

      $categories_data = array_merge($categories_data, $this->getCategories($result['category_id'], $level));
    }

    return $categories_data;
  }
  */
  
  public function getCategories($data = array()) {
    $sql = "SELECT cp.category_id, cd2.name, MAX(cp.level) AS `level`, c1.parent_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS path_name FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id ORDER BY path_name";

    if (isset($data['start']) || isset($data['limit'])) {
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
  
  public function getAttributeGroups($data = array()) {
    $sql = "SELECT * FROM " . DB_PREFIX . "attribute_group ag LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE agd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (!empty($data['filter_name'])) {
      $sql .= " AND LCASE(agd.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";    
    }

    $sql .= " ORDER BY agd.name";

    if (isset($data['start']) || isset($data['limit'])) {
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
  
  // Manufacturers 
  public function getManufacturer($manufacturer_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
  
    return $query->row;
  }
  
  public function getManufacturers($data = []) {
    $sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m";

    if ($this->ocfilter->config('use_manufacturer_description')) {
      $sql .= " LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id)";
    }  

    if (!empty($data['filter_name'])) {
      if ($this->ocfilter->config('use_manufacturer_description')) {
        $sql .= " WHERE LCASE(md.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
      } else {
        $sql .= " WHERE LCASE(m.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
      }
    }

    $sort_data = [
      'name',
      'sort_order'
    ];

    $sort = 'm.name';

    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {      
      $sort = 'm.' . $data['sort'];
    }

    if ($sort == 'm.name' && $this->ocfilter->config('use_manufacturer_description')) {
      $sort = 'md.name';
    }

    $sql .= " ORDER BY " . $sort;

    if (isset($data['order']) && ($data['order'] == 'DESC')) {
      $sql .= " DESC";
    } else {
      $sql .= " ASC";
    }

    if (isset($data['start']) || isset($data['limit'])) {
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
  
  public function getTotalManufacturers($data = []) {
    $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer m";

    if ($this->ocfilter->config('use_manufacturer_description')) {
      $sql .= " LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id)";
    }  

    if (!empty($data['filter_name'])) {
      if ($this->ocfilter->config('use_manufacturer_description')) {
        $sql .= " WHERE LCASE(md.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
      } else {
        $sql .= " WHERE LCASE(m.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
      }
    }

    return $query->num_rows ? $query->row['total'] : 0;
  }    
  
  // @page generator
  public function getManufacturerDescriptions($manufacturer_id) {
    if ($this->ocfilter->config('use_manufacturer_description')) {
      $query = $this->db->query("SELECT language_id, name FROM " . DB_PREFIX . "manufacturer_description WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");      
    } else {     
      $query = $this->db->query("SELECT l.language_id, m.name FROM " . DB_PREFIX . "manufacturer m, " . DB_PREFIX . "language l WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "'");
    }
    
    return array_combine(array_column($query->rows, 'language_id'), array_column($query->rows, 'name'));
  }
  
  public function getAllManufacturerDescriptions() {
    $manufacturer_description_data = [];

    if ($this->ocfilter->config('use_manufacturer_description')) {
      $query = $this->db->query("SELECT manufacturer_id, GROUP_CONCAT(CONCAT(language_id, '{ln}', name) SEPARATOR '{v}') AS lang_name FROM " . DB_PREFIX . "manufacturer_description GROUP BY manufacturer_id");
    } else {
      $query = $this->db->query("SELECT m.manufacturer_id, GROUP_CONCAT(CONCAT(l.language_id, '{ln}', m.name) SEPARATOR '{v}') AS lang_name FROM " . DB_PREFIX . "manufacturer m, " . DB_PREFIX . "language l GROUP BY m.manufacturer_id");      
    }
      
    foreach ($query->rows as $key => $row) {
      $manufacturer_description_data[$row['manufacturer_id']] = [];
            
      foreach (explode('{v}', $row['lang_name']) as $lang_name) {
        list($language_id, $name) = explode('{ln}', $lang_name);
        
        $manufacturer_description_data[$row['manufacturer_id']][$language_id] = $name;
      }
      
      unset($query->rows[$key]);
    }

    return $manufacturer_description_data;
  }     

  // Stock status
  public function getStockStatusDescriptions($stock_status_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "'");

    return array_combine(array_column($query->rows, 'language_id'), array_column($query->rows, 'name'));
  }
  
  public function getAllStockStatusDescriptions() {
    $stock_status_description_data = [];

    $query = $this->db->query("SELECT stock_status_id, GROUP_CONCAT(CONCAT(language_id, '{ln}', name) SEPARATOR '{v}') AS lang_name FROM " . DB_PREFIX . "stock_status GROUP BY stock_status_id");

    foreach ($query->rows as $key => $row) {
      $stock_status_description_data[$row['stock_status_id']] = [];
            
      foreach (explode('{v}', $row['lang_name']) as $lang_name) {
        list($language_id, $name) = explode('{ln}', $lang_name);
        
        $stock_status_description_data[$row['stock_status_id']][$language_id] = $name;
      }
      
      unset($query->rows[$key]);
    }

    return $stock_status_description_data;
  }    

  // @copyProduct, include with modification
  public function getProductOCFilterValues($product_id) {
    $product_filter_data = [];

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_filter_value_to_product WHERE product_id = '" . $this->db->escape((string)$product_id) . "'");

    foreach ($query->rows as $result) {
      $key = $result['filter_id'] . '.' . $result['source'];
      
      if (!isset($product_filter_data[$key])) {
        $product_filter_data[$key] = [];
      }
      
      $product_filter_data[$key][] = (string)$result['value_id'];
    }
    
    // Ranges
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_filter_range_to_product WHERE product_id = '" . $this->db->escape((string)$product_id) . "'");

    foreach ($query->rows as $result) {
      $key = $result['filter_id'] . '.' . $result['source'];
      
      $product_filter_data[$key] = [
        'min' => (float)$result['min'],
        'max' => (float)$result['max'],
      ];
    }    

    return $product_filter_data;
  }
  
  // @product add/edit, include with modification
  public function setOCFilterFilter($product_id, $data, $product_info = null) {
    $product_filter = $this->getProductOCFilterValues($product_id);
    
    $will_change_product = (!isset($product_info) || !$product_info); // New or not founded product   
    $will_change_filter = (($product_filter && !isset($data['ocfilter_filter'])) || (!$product_filter && isset($data['ocfilter_filter']))); 
    
    if (!$will_change_filter && isset($data['ocfilter_filter']) && $data['ocfilter_filter'] != $product_filter) {
      $will_change_filter = true; 
    }
    
    if ($will_change_filter) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value_to_product WHERE product_id = '" . $this->db->escape((string)$product_id) . "'");
      $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_range_to_product WHERE product_id = '" . $this->db->escape((string)$product_id) . "'");

      if (isset($data['ocfilter_filter'])) {
        foreach ($data['ocfilter_filter'] as $filter_key => $values) {       
          $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
          
          if (isset($values['min']) && isset($values['max']) && (strlen($values['min']) + strlen($values['max'])) > 0) {
            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_range_to_product SET product_id = '" . $this->db->escape((string)$product_id) . "', filter_id = '" . (int)$filter_id . "', source = '" . (int)$source . "', `min` = '" . (float)$values['min'] . "', `max` = '" . (float)$values['max'] . "'");
          } else if (is_array($values)) {
            foreach ($values as $value_id) {    
              $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_to_product SET product_id = '" . $this->db->escape((string)$product_id) . "', filter_id = '" . (int)$filter_id . "', source = '" . (int)$source . "', value_id = '" . $this->db->escape((string)$value_id) . "'");          
            }          
          }        
        }
      }  
    } else if (!$will_change_product && is_array($product_info)) {
      foreach ($data as $key => $value) {
        if (isset($product_info[$key]) && in_array($key, [ 'status', 'manufacturer_id', 'price', 'weight', 'length', 'height', 'width', 'quantity', 'stock_status_id' ])) {
          $will_change_product = ($value != $product_info[$key]);
          
          if ($will_change_product) {
            break;
          }
        }
      }
    }
        
    if ($will_change_filter || $will_change_product) {
      // Clear cache
      if (!empty($data['manufacturer_id'])) {
        $this->ocfilter->cache->key('counter', 'manufacturer', $data['manufacturer_id'])->delete();
        $this->ocfilter->cache->key('filter', '*', 'slider', 'manufacturer', $data['manufacturer_id'])->delete();
      }
      
      if (isset($data['product_category'])) {
        foreach ($data['product_category'] as $category_id) {
          $this->ocfilter->cache->key('counter', 'category', $category_id)->delete();
          $this->ocfilter->cache->key('filter', '*', 'slider', 'category', $category_id)->delete();
        }
      }

      $this->ocfilter->cache->key('counter', 'special')->delete();
      $this->ocfilter->cache->key('filter', '*', 'slider', 'special')->delete();
      $this->ocfilter->cache->key('counter', 'search')->delete();
      $this->ocfilter->cache->key('filter', '*', 'slider', 'search')->delete();
      $this->ocfilter->cache->key('counter', 'custom')->delete();
      $this->ocfilter->cache->key('filter', '*', 'slider', 'custom')->delete();
    }
  }
  
  // Totals
  public function getTotalOpencartAttributes() {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "attribute`");
    
    return $query->num_rows ? $query->row['total'] : 0;
  }
  
  public function getTotalOpencartAttributeValues() {
    $query = $this->db->query("SELECT COUNT(DISTINCT `text`) AS total FROM `" . DB_PREFIX . "product_attribute` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
    
    return $query->num_rows ? $query->row['total'] : 0;
  }

  public function getTotalOpencartFilters() {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "filter_group`");
    
    return $query->num_rows ? $query->row['total'] : 0;    
  }  

  public function getTotalOpencartFilterValues() {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "filter`");
    
    return $query->num_rows ? $query->row['total'] : 0;
  }

  public function getTotalOpencartOptions() {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "option`");
    
    return $query->num_rows ? $query->row['total'] : 0;
  }

  public function getTotalOpencartOptionValues() {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "option_value`");
    
    return $query->num_rows ? $query->row['total'] : 0;
  }

  public function getCopyAttributes($exclude = false) {
    $attributes_data = [];
    
    // By count of unique attribute values and max text length   
    $query = $this->db->query("SELECT pa.attribute_id, ad.name FROM (SELECT COUNT(DISTINCT `text`) AS total, attribute_id FROM " . DB_PREFIX . "product_attribute WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY attribute_id) pa LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (pa.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.total " . ($exclude ? '>' : '<') . " 1000 ORDER BY pa.total DESC");
    
    $results = array_combine(array_column($query->rows, 'attribute_id'), array_column($query->rows, 'name'));      
    
    // By avg length of unique attribute values text
    if ($exclude) {
      //$sql = "SELECT pa.attribute_id, ad.name, pa.total FROM (SELECT ROUND(LENGTH(GROUP_CONCAT(DISTINCT `text` SEPARATOR '')) / COUNT(DISTINCT `text`)) AS `length`, attribute_id FROM " . DB_PREFIX . "product_attribute WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY attribute_id) pa LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (pa.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";
          
      $query = $this->db->query("SELECT pa.attribute_id, ad.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (pa.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' AND LENGTH(pa.`text`) > 150 GROUP BY pa.attribute_id ORDER BY LENGTH(pa.`text`) DESC");
      
      $results = $results + array_combine(array_column($query->rows, 'attribute_id'), array_column($query->rows, 'name'));      
    }
    
    foreach ($results as $attribute_id => $name) {
      $attributes_data[] = [
        'attribute_id' => $attribute_id,
        'name' => $name
      ];
    }

    return $attributes_data;
  }

  public function copyFilters($data = []) {   
    $this->writeLog('================== [Copy START] ==================');

    if (!empty($data['copy_truncate'])) {
      $this->writeLog('[Clear filters]');

      $source = $this->ocfilter->params->source('default')->id();

      $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter` WHERE `source` != " . (int)$source);
      $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter_description` WHERE `source` != " . (int)$source);
      $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter_to_category` WHERE `source` != " . (int)$source);
      $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter_to_store` WHERE `source` != " . (int)$source);
      $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter_value` WHERE `source` != " . (int)$source);
      $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter_value_description` WHERE `source` != " . (int)$source);
      $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter_value_to_product` WHERE `source` != " . (int)$source);
      $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter_range_to_product` WHERE `source` != " . (int)$source);      
    }

    // Copy Product Options
    if (!empty($data['copy_option'])) {
      $this->copyProductOption($data);
    }

    // Copy Product Filters
    if (!empty($data['copy_filter'])) {
      $this->copyProductFilter($data);
    }

    // Copy Product Attributes
    if (!empty($data['copy_attribute'])) {
      $this->copyProductAttribute($data);
    }

    if (!empty($data['copy_option']) || !empty($data['copy_filter']) || !empty($data['copy_attribute'])) {
      // Category
      if (!empty($data['copy_category'])) {
        $this->setCopyFilterCategory($data);
      }

      // Store
      $this->setCopyFilterStore($data);

      // Disable bad filters
      $this->disableCopyBrokenFilters($data);

      // Convert to slide
      $this->setCopyFilterSlider($data);
    }

    // Separate
    if (!empty($data['copy_value_separator'])) {
      $this->separateCopyFilterValue($data);
    }

    $this->writeLog('[Optimize]');
    
    $this->db->query("OPTIMIZE NO_WRITE_TO_BINLOG TABLE 
      " . DB_PREFIX . "ocfilter_filter, 
      " . DB_PREFIX . "ocfilter_filter_description,
      " . DB_PREFIX . "ocfilter_filter_to_category,
      " . DB_PREFIX . "ocfilter_filter_to_store,
      " . DB_PREFIX . "ocfilter_filter_value,
      " . DB_PREFIX . "ocfilter_filter_value_description,
      " . DB_PREFIX . "ocfilter_filter_value_to_product,
      " . DB_PREFIX . "ocfilter_filter_range_to_product
    ");

    $this->writeLog('[Clear cache]');
    
    $this->ocfilter->cache->delete('ocfilter');
       
    $this->writeLog('================== [Copy END] ==== (' . (float)number_format((microtime(true) - $this->time_start), 3) . ' sec. total) ==============');
    
    if (!empty($data['copy_hash'])) {
      // For js finish triggering
      $this->writeLog($data['copy_hash']);      
    }
  }

  private function copyProductOption($data) {
    $this->writeLog('[Copy product options]');
    
    $source = $this->ocfilter->params->source('option')->id();
      
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value_to_product WHERE source = " . (int)$source);
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_range_to_product WHERE source = " . (int)$source);

    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter (filter_id, source, `type`, `status`, dropdown, sort_order, image) SELECT option_id, " . (int)$source . ", '" . $this->db->escape($data['copy_type']) . "', '" . (int)$data['copy_status'] . "', '" . (int)$data['copy_dropdown'] . "', sort_order, IF(`type` = 'image', '1', '0') FROM `" . DB_PREFIX . "option`");
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_description (filter_id, source, language_id, name) SELECT option_id, " . (int)$source . ", language_id, name FROM " . DB_PREFIX . "option_description");

    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value (filter_id, source, value_id, image, sort_order) SELECT option_id, " . (int)$source . ", option_value_id, image, sort_order FROM " . DB_PREFIX . "option_value");
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_description (filter_id, source, value_id,  language_id, name) SELECT option_id, " . (int)$source . ", option_value_id, language_id, name FROM " . DB_PREFIX . "option_value_description");

    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_to_product (filter_id, source, value_id, product_id) SELECT option_id, " . (int)$source . ", option_value_id, product_id FROM " . DB_PREFIX . "product_option_value WHERE quantity > '0'");
  }

  private function copyProductFilter($data) {
    $this->writeLog('[Copy OpenCart filters]');
    
    $source = $this->ocfilter->params->source('filter')->id();
      
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value_to_product WHERE source = " . (int)$source);
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_range_to_product WHERE source = " . (int)$source);

    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter (filter_id, source, `type`, `status`, dropdown, sort_order) SELECT filter_group_id, " . (int)$source . ", '" . $this->db->escape($data['copy_type']) . "', '" . (int)$data['copy_status'] . "', '" . (int)$data['copy_dropdown'] . "', sort_order FROM `" . DB_PREFIX . "filter_group`");
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_description (filter_id, source, language_id, name) SELECT filter_group_id, " . (int)$source . ", language_id, name FROM " . DB_PREFIX . "filter_group_description");

    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value (filter_id, source, value_id, sort_order) SELECT filter_group_id, " . (int)$source . ", filter_id, sort_order FROM " . DB_PREFIX . "filter");
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_description (filter_id, source, value_id, language_id, name) SELECT filter_group_id, " . (int)$source . ", filter_id, language_id, name FROM " . DB_PREFIX . "filter_description");

    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_to_product (filter_id, source, value_id, product_id) SELECT (SELECT fv.filter_id FROM " . DB_PREFIX . "ocfilter_filter_value fv WHERE fv.value_id = pf.filter_id AND fv.source = " . (int)$source . "), " . (int)$source . ", pf.filter_id, pf.product_id FROM " . DB_PREFIX . "product_filter pf");
  }

  private function copyProductAttribute($data) {
    $this->writeLog('[Copy attributes start]');    
    
    $source = $this->ocfilter->params->source('attribute')->id();
    
    $replace = function($rule, $column) {
      $sql = "";
      
      $sql .= str_repeat('REPLACE(', count($rule)) . $column;

      foreach ($rule as $from => $to) {
        $sql .= ", '" . $from . "', '" . $to . "')";
      }
     
      return $sql;
    };    
    
    $this->writeLog('[Delete old product value (attribute) relations]');    
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_value_to_product WHERE source = " . (int)$source);
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_filter_range_to_product WHERE source = " . (int)$source);

    if (!empty($data['copy_group_as_attribute'])) {
      $attribute_groups_id = array_filter(array_unique(array_keys($data['copy_attribute_group_id'])), 'intval');    
    
      if ($attribute_groups_id) {    
        if (!empty($data['copy_attribute_group_id_exclude'])) {
          $where = " WHERE attribute_group_id NOT IN(" . implode(',', array_map('intval', $attribute_groups_id)) . ")";
        } else {
          $where = " WHERE attribute_group_id IN(" . implode(',', array_map('intval', $attribute_groups_id)) . ")";
        }  
      }      
      
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter (filter_id, source, `type`, `status`, dropdown, sort_order) SELECT attribute_group_id, " . (int)$source . ", '" . $this->db->escape($data['copy_type']) . "', '" . (int)$data['copy_status'] . "', '" . (int)$data['copy_dropdown'] . "', sort_order FROM " . DB_PREFIX . "attribute_group");
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_description (filter_id, source, language_id, name) SELECT attribute_group_id, " . (int)$source . ", language_id, name FROM " . DB_PREFIX . "attribute_group_description");

      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value (filter_id, source, value_id, sort_order) SELECT attribute_group_id, " . (int)$source . ", attribute_id, sort_order FROM " . DB_PREFIX . "attribute");
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_description (filter_id, source, value_id, language_id, name) SELECT a.attribute_group_id, " . (int)$source . ", a.attribute_id, ad.language_id, ad.name FROM " . DB_PREFIX . "attribute_description ad LEFT JOIN " . DB_PREFIX . "attribute a ON (ad.attribute_id = a.attribute_id)");

      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_to_product (filter_id, source, value_id, product_id) SELECT a.attribute_group_id, " . (int)$source . ", pa.attribute_id, pa.product_id FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) WHERE pa.language_id = '" . (int)$this->config->get('config_language_id') . "'");
    } else {  
      $this->writeLog('[Create attribute cache]');
      
      $include = [];
      $exclude = [];      
      
      $attributes_id = array_filter(array_unique(array_keys($data['copy_attribute_id'])), 'intval');    
    
      if ($attributes_id) {       
        if (!empty($data['copy_attribute_id_exclude'])) {
          $exclude = $attributes_id;
        } else {
          $include = $attributes_id;
        }      
      }      

      $attribute_categories_id = array_filter(array_unique(array_keys($data['copy_attribute_category_id'])), 'intval');
  
      if ($attribute_categories_id) {
        $query = $this->db->query("SELECT DISTINCT pa.attribute_id FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (p2c.product_id = pa.product_id) WHERE pa.attribute_id IS NOT NULL AND p2c.category_id IN(" . implode(',', array_map('intval', $attribute_categories_id)) . ")");
        
        if ($query->num_rows) {
          if (!empty($data['copy_attribute_category_id_exclude'])) {
            $exclude = array_unique(array_merge($exclude, array_column($query->rows, 'attribute_id')));
          } else {
            $include = array_unique(array_merge($include, array_column($query->rows, 'attribute_id')));
          }  
        }               
      }

      $where = "";

      if ($include || $exclude) {            
        if ($include && $exclude && array_intersect($include, $exclude)) {
          if (count($include) > count($exclude)) {
            $include = array_diff($include, $exclude);
          } else {
            $exclude = array_diff($exclude, $include);
          }
        }
        
        $implode = [];
        
        if ($include) {          
          $implode[] = "attribute_id IN(" . implode(',', array_map('intval', $include)) . ")";
        }
        
        if ($exclude) {          
          $implode[] = "attribute_id NOT IN(" . implode(',', array_map('intval', $exclude)) . ")";
        }        
        
        if ($implode) {
          $where = " WHERE " . implode(" AND ", $implode);
        }
        
        $this->writeLog('[Attribute condition] ' . $where);
      }

      $this->db->query("TRUNCATE `" . DB_PREFIX . "ocfilter_attribute_cache`");     
      $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_attribute_cache (product_id, attribute_id, language_id, text, `key`) SELECT product_id, attribute_id, language_id, TRIM(" . $replace([ '\r' => '', '\n' => '', '\t' => '' ], '`text`') . "), IF(language_id = '" . (int)$this->config->get('config_language_id') . "', CRC32(CONCAT(attribute_id, '.', LCASE(" . $replace([ '\r' => '', '\n' => '', '\t' => '', ' ' => '' ], '`text`') . "))), 0) FROM " . DB_PREFIX . "product_attribute WHERE `text` <> ''" . str_replace(" WHERE ", " AND ", $where));

      $this->writeLog('[Insert filters]');
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter (filter_id, source, `type`, status, dropdown, sort_order) SELECT attribute_id, " . (int)$source . ", '" . $this->db->escape($data['copy_type']) . "', '" . (int)$data['copy_status'] . "', '" . (int)$data['copy_dropdown'] . "', sort_order FROM " . DB_PREFIX . "attribute" . $where);

      $this->writeLog('[Insert filters description]');
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_description (filter_id, source, language_id, name) SELECT attribute_id, " . (int)$source . ", language_id, name FROM " . DB_PREFIX . "attribute_description" . $where);

      $this->writeLog('[Insert filter values]');
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value (filter_id, source, value_id) SELECT attribute_id, " . (int)$source . ", `key` FROM " . DB_PREFIX . "ocfilter_attribute_cache WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY `key`");

      $this->writeLog('[Insert filter values description]');
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_description (filter_id, source, value_id, language_id, name, attribute_text) SELECT attribute_id, " . (int)$source . ", `key`, language_id, `text`, IF(LENGTH(`text`) > 255, `text`, '') FROM " . DB_PREFIX . "ocfilter_attribute_cache WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY `key`");

      $this->load->model('localisation/language');

      $languages = $this->model_localisation_language->getLanguages();

      if (count($languages) > 1) {
        $this->writeLog('[Insert filter values description for another languages]'); 
      }        

      foreach ($languages as $language) {
        if ($language['language_id'] != $this->config->get('config_language_id')) {
          $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_description (filter_id, source, value_id, language_id, name, attribute_text) SELECT c1.attribute_id, " . (int)$source . ", c2.`key`, '" . (int)$language['language_id'] . "', c1.`text`, IF(LENGTH(c1.`text`) > 255, c1.`text`, '') FROM " . DB_PREFIX . "ocfilter_attribute_cache c1 LEFT JOIN " . DB_PREFIX . "ocfilter_attribute_cache c2 ON (c1.product_id = c2.product_id AND c1.attribute_id = c2.attribute_id) WHERE c2.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c1.language_id = '" . (int)$language['language_id'] . "' GROUP BY c2.`key`");
        }
      }

      $this->writeLog('[Insert product value new relations]');
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_to_product (filter_id, source, value_id, product_id) SELECT attribute_id, " . (int)$source . ", `key`, product_id FROM " . DB_PREFIX . "ocfilter_attribute_cache WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");              
    }
  }

  private function setCopyFilterCategory($data) {
    $this->writeLog('[Set categories]');
        
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_to_category (filter_id, source, category_id) SELECT fv2p.filter_id, fv2p.source, p2c.category_id FROM " . DB_PREFIX . "ocfilter_filter_value_to_product fv2p LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = fv2p.product_id) WHERE p2c.category_id != '0' AND fv2p.source != " . (int)$this->ocfilter->params->source('default')->id() . " GROUP BY fv2p.filter_id, fv2p.source, p2c.category_id");
  }

  private function setCopyFilterStore($data) {
    $this->writeLog('[Set store]');
      
    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_to_store (filter_id, source, store_id) SELECT filter_id, source, '0' FROM " . DB_PREFIX . "ocfilter_filter WHERE source != " . (int)$this->ocfilter->params->source('default')->id());

    $this->load->model('setting/store');

    $results = $this->model_setting_store->getStores();

    foreach ($results as $result) {
      $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_to_store (filter_id, source, store_id) SELECT filter_id, source, '" . (int)$result['store_id'] . "' FROM " . DB_PREFIX . "ocfilter_filter WHERE source != " . (int)$this->ocfilter->params->source('default')->id());
    }
  }

  private function setCopyFilterSlider($data) {
    $this->writeLog('[Convert sliders]');
    
    $filter_query = $this->db->query("SELECT filter_id, source FROM " . DB_PREFIX . "ocfilter_filter WHERE status = '1' AND source != " . (int)$this->ocfilter->params->source('default')->id() . " AND (`type` = 'slide' OR `type` = 'slide_dual')");

    foreach ($filter_query->rows as $filter) {
      $this->convertFilterToSlider($filter['filter_id'], $filter['source']);
    }
  }
  
  private function disableCopyBrokenFilters($data) {
    $this->writeLog('[Disable bad filters]');
    
    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_filter f LEFT JOIN " . DB_PREFIX . "ocfilter_filter_to_category f2c ON (f.filter_id = f2c.filter_id) SET f.status = '0' WHERE f.source != " . (int)$this->ocfilter->params->source('default')->id() . " AND f2c.category_id IS NULL");

    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_filter f LEFT JOIN " . DB_PREFIX . "ocfilter_filter_value_to_product fv2p ON (f.filter_id = fv2p.filter_id) SET f.status = '0' WHERE f.source != " . (int)$this->ocfilter->params->source('default')->id() . " AND fv2p.product_id IS NULL");

    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_filter f LEFT JOIN " . DB_PREFIX . "ocfilter_filter_value fv ON (f.filter_id = fv.filter_id) SET f.status = '0' WHERE f.source != " . (int)$this->ocfilter->params->source('default')->id() . " AND fv.value_id IS NULL");    
  }

  private function separateCopyFilterValue($data) {
    $separators = array_filter($data['copy_value_separator'], 'strlen');
    
    if ($separators) {
      $this->writeLog('[Separate values by (' . implode('), (', $separators) . ')]');
    }   
    
    // Set unique key by name
    $getKey = function($string) {
      return (string)crc32($this->ocfilter->helper->translit($string));      
    };
    
    $query = $this->db->query("SELECT fv.value_id, fv.source, fvd.name FROM " . DB_PREFIX . "ocfilter_filter_value fv LEFT JOIN " . DB_PREFIX . "ocfilter_filter_value_description fvd ON (fv.value_id = fvd.value_id) LEFT JOIN " . DB_PREFIX . "ocfilter_filter f ON (fv.filter_id = f.filter_id) WHERE f.source != " . (int)$this->ocfilter->params->source('default')->id() . " AND fvd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND fv.`key` = ''");

    while ($query->rows) {
      $insert = [];

      foreach (array_splice($query->rows, 0, 250) as $row) {       
        $insert[] = "'" . $this->db->escape($row['value_id']) . "','" . $this->db->escape($row['source']) . "','" . $this->db->escape($getKey($row['name'])) . "'";
      }

      if ($insert) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_value (value_id, source, `key`) VALUES (" . implode("),(", $insert) . ") ON DUPLICATE KEY UPDATE `key` = VALUES(`key`)");
      }
    }    

    $separateValues = function($separator) use($getKey) {  
      $separator = html_entity_decode($separator, ENT_QUOTES, 'UTF-8');
      
      if ($separator == '|') {
        $separator = '||';
      }
      
      $is_tag = (false !== strpos($separator, '<'));
      
      $sql = "SELECT 
        GROUP_CONCAT(CONCAT(fvd.language_id, '{sep_val}', IF(fvd.attribute_text <> '', fvd.attribute_text, fvd.name)) SEPARATOR '{sep_lang}') AS language_name,      
        fvd.value_id, 
        fvd.source, 
        fvd.filter_id 
      FROM " . DB_PREFIX . "ocfilter_filter_value_description fvd 
      LEFT JOIN " . DB_PREFIX . "ocfilter_filter f ON (fvd.filter_id = f.filter_id) WHERE f.source != " . (int)$this->ocfilter->params->source('default')->id();

      if ($is_tag) {
        $sql .= " AND REPLACE(REPLACE(fvd.name, '&lt;', '<'), '&gt;', '>') LIKE '%" . $this->db->escape($separator) . "%'";
      } else {
        $sql .= " AND fvd.name LIKE '%" . $this->db->escape($separator) . "%'";
      }

      if ($separator == '||') {
        $sql .= " ESCAPE '|'";
      }

      $sql .= " GROUP BY fvd.value_id, fvd.source";
      
      $query = $this->db->query($sql);

      foreach ($query->rows as $result) {
        $language_name_rows = explode('{sep_lang}', $result['language_name']);

        $value_rows = [];

        foreach ($language_name_rows as $language_value) {
          list($language_id, $name) = explode('{sep_val}', $language_value);
          
          $name = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
          
          if (!$is_tag) {
            $name = strip_tags($name);
          }
          
          $multivalues = explode($separator, $name);

          if ($multivalues) {
            foreach (array_unique($multivalues) as $value) {
              $value_rows[$language_id][] = trim(trim($value), trim($separator));
            }
          }
        }

        if (isset($value_rows[$this->config->get('config_language_id')])) {
          foreach ($value_rows[$this->config->get('config_language_id')] as $key => $multivalue) {         
            $value_query = $this->db->query("SELECT value_id FROM " . DB_PREFIX . "ocfilter_filter_value WHERE filter_id = '" . (int)$result['filter_id'] . "' AND source = '" . $this->db->escape($result['source']) . "' AND `key` = '" . $this->db->escape($getKey($multivalue)) . "'");

            if ($value_query->num_rows) {
              $new_value_id = $value_query->row['value_id'];
            } else {
              $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_value (filter_id, source, `key`) VALUES ('" . (int)$result['filter_id'] . "', '" . $this->db->escape($result['source']) . "', '" . $this->db->escape($getKey($multivalue)) . "')");

              $new_value_id = $this->db->getLastId();

              $insert = [];

              $insert[] = "'" . (int)$result['filter_id'] . "', '" . $this->db->escape($result['source']) . "', '" . $this->db->escape($new_value_id) . "', '" . (int)$this->config->get('config_language_id') . "', '" . $this->db->escape($multivalue) . "'";

              foreach ($value_rows as $language_id => $_values) {
                // Another languages
                if ($language_id != $this->config->get('config_language_id') && isset($_values[$key])) {
                  $insert[] = "'" . (int)$result['filter_id'] . "', '" . $this->db->escape($result['source']) . "', '" . $this->db->escape($new_value_id) . "', '" . (int)$language_id . "', '" . $this->db->escape($_values[$key]) . "'";
                }
              }

              $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_filter_value_description (filter_id, source, value_id, language_id, name) VALUES (" . implode("),(", $insert) . ")");
            }

            $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ocfilter_filter_value_to_product (product_id, filter_id, value_id, source) SELECT product_id, '" . (int)$result['filter_id'] . "', '" . $this->db->escape($new_value_id) . "', '" . $this->db->escape($result['source']) . "' FROM " . DB_PREFIX . "ocfilter_filter_value_to_product WHERE filter_id = '" . (int)$result['filter_id'] . "' AND value_id = '" . $this->db->escape($result['value_id']) . "' AND source = '" . $this->db->escape($result['source']) . "'");
          }

          $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter_value` WHERE value_id = '" . $this->db->escape($result['value_id']) . "' AND source = '" . $this->db->escape($result['source']) . "'");
          $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter_value_description` WHERE value_id = '" . $this->db->escape($result['value_id']) . "' AND source = '" . $this->db->escape($result['source']) . "'");
          $this->db->query("DELETE FROM `" . DB_PREFIX . "ocfilter_filter_value_to_product` WHERE filter_id = '" . (int)$result['filter_id'] . "' AND value_id = '" . $this->db->escape($result['value_id']) . "' AND source = '" . $this->db->escape($result['source']) . "'");
        }
      }      
    };    
      
    foreach ($separators as $separator) {
      $separateValues($separator);
    }        
  }
}