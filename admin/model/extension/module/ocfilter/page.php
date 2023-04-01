<?php

class ModelExtensionModuleOCFilterPage extends Model {
  public function addPage($data) {   
    $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_page SET category_id = '" . (int)$data['category_id'] . "', dynamic = '" . (int)$data['dynamic'] . "', status = '" . (int)$data['status'] . "', category = '" . (int)$data['category'] . "', module = '" . (int)$data['module'] . "', product = '" . (int)$data['product'] . "', sitemap = '" . (int)$data['sitemap'] . "'");

    $page_id = $this->db->getLastId();

    foreach ($data['page_description'] as $language_id => $value) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_page_description SET page_id = '" . (int)$page_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', heading_title = '" . $this->db->escape($value['heading_title']) . "', description_top = '" . $this->db->escape($value['description_top']) . "', description_bottom = '" . $this->db->escape($value['description_bottom']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
    }
    
    if (isset($data['page_store'])) {
      foreach ($data['page_store'] as $store_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_page_to_store SET page_id = '" . (int)$page_id . "', store_id = '" . (int)$store_id . "'");
      }
    }

    if (isset($data['page_layout'])) {
      foreach ($data['page_layout'] as $store_id => $layout_id) {
        if (!$layout_id) {
          continue;
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_page_to_layout SET page_id = '" . (int)$page_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
      }
    }    

    $this->setPageParams($page_id, $data);

    $this->setPageKeyword($page_id, $data);
    
    return $page_id;
  }

  public function editPage($page_id, $data) {    
    $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_page SET dynamic = '" . (int)$data['dynamic'] . "', category_id = '" . (int)$data['category_id'] . "', status = '" . (int)$data['status'] . "', category = '" . (int)$data['category'] . "', module = '" . (int)$data['module'] . "', product = '" . (int)$data['product'] . "', sitemap = '" . (int)$data['sitemap'] . "', `params` = '', params_count = '0', `params_key` = '' WHERE page_id = '" . (int)$page_id . "'");

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_page_description WHERE page_id = '" . (int)$page_id . "'");

    foreach ($data['page_description'] as $language_id => $value) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_page_description SET page_id = '" . (int)$page_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', heading_title = '" . $this->db->escape($value['heading_title']) . "', description_top = '" . $this->db->escape($value['description_top']) . "', description_bottom = '" . $this->db->escape($value['description_bottom']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
    }

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_page_to_store WHERE page_id = '" . (int)$page_id . "'");

    if (isset($data['page_store'])) {
      foreach ($data['page_store'] as $store_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_page_to_store SET page_id = '" . (int)$page_id . "', store_id = '" . (int)$store_id . "'");
      }
    }

    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_page_to_layout WHERE page_id = '" . (int)$page_id . "'");

    if (isset($data['page_layout'])) {
      foreach ($data['page_layout'] as $store_id => $layout_id) {
        if (!$layout_id) {
          continue;
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_page_to_layout SET page_id = '" . (int)$page_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
      }
    }
    
    $this->setPageParams($page_id, $data);

    $this->setPageKeyword($page_id, $data);

    $this->ocfilter->cache->key('page', $page_id)->delete();
  }
  
  protected function setPageParams($page_id, $data) {
    if (isset($data['ocfilter_filter'])) {
      $params = [];
            
      foreach ($data['ocfilter_filter'] as $filter_key => $values) {              
        if (isset($values['min']) && isset($values['max']) && (strlen($values['min']) + strlen($values['max'])) > 0) {
          if (strlen($values['min']) < 1) {
            $values['min'] = 0;
          }
          
          if (strlen($values['max']) < 1) {
            if ($values['min'] < 0) {
              $values['max'] = 0;
            } else {
              $values['max'] = -1;              
            }            
          }
          
          $params[$filter_key] = [ $values['min'] . '-' . $values['max'] ];
        } else if (is_array($values)) {
          if (false !== ($group = array_search('group', $values, true))) {
            unset($values[ $group ]);
          } 

          $params[$filter_key] = $values;  
        }        
      }

      if ($params) {
        $params = $this->ocfilter->params->normalizeArray($params);
        
        $params_key = crc32($this->ocfilter->params->encode($params));
   
        $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_page SET `params` = '" . $this->db->escape(json_encode($params)) . "', params_count = '" . (int)count($params) . "', `params_key` = '" . $this->db->escape((string)$params_key) . "' WHERE page_id = '" . (int)$page_id . "'");        
      }
    }         
  }
  
  protected function setPageKeyword($page_id, $data) {
    // Add SEO URL keyword
    if ($this->ocfilter->opencart->version >= 30) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `query` = 'ocfilter_page_id=" . (int)$page_id . "'");
      
      foreach ($data['keyword'] as $store_id => $languages) {
        foreach ($languages as $language_id => $keyword) {
          if (utf8_strlen($keyword) > 0) {
            $this->ocfilter->query("INSERT INTO " . DB_PREFIX . "seo_url SET language_id = '" . (int)$language_id . "', store_id = '" . (int)$store_id . "', `query` = 'ocfilter_page_id=" . (int)$page_id . "', keyword = '" . $this->db->escape($keyword) . "'");
          }
        }
      }
    } else if (is_string($data['keyword']) && utf8_strlen($data['keyword']) > 0) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE `query` = 'ocfilter_page_id=" . (int)$page_id . "'");
      
      $this->ocfilter->query("INSERT INTO " . DB_PREFIX . "url_alias SET `query` = 'ocfilter_page_id=" . (int)$page_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
    }
    
    // Seo pro cache
    if ($this->config->get('config_seo_url_type') == 'seo_pro' || $this->config->get('config_seo_pro')) {
      $url_query = 'ocfilter_page_id=' . (int)$page_id;
      
      if ($this->ocfilter->opencart->version >= 30 && ($this->config->get('config_seo_url_cache') || (bool)$this->cache->get('seopro.keywords'))) {
        $seo_pro_keywords = $this->cache->get('seopro.keywords');
        $seo_pro_queries = $this->cache->get('seopro.queries');

        if ($seo_pro_keywords && is_array($seo_pro_keywords) && $seo_pro_queries && is_array($seo_pro_queries)) {
          $query = $this->ocfilter->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'ocfilter_page_id=" . (int)$page_id . "'");
          
          foreach ($query->rows as $result) {
            $seo_pro_keywords[$url_query][$result['store_id']][$result['language_id']] = $result['keyword'];
            $seo_pro_queries[$result['keyword']][$result['store_id']][$result['language_id']] = $url_query;
          }

          $this->cache->set('seopro.keywords', $seo_pro_keywords);
          $this->cache->set('seopro.queries', $seo_pro_queries);
        } 
      } else if ($this->ocfilter->opencart->version < 30 && is_string($data['keyword']) && utf8_strlen($data['keyword']) > 0 && (bool)$this->cache->get('seo_pro')) {
        $seo_pro_data = $this->cache->get('seo_pro');
        
        if (isset($seo_pro_data['keywords']) && is_array($seo_pro_data['keywords']) && isset($seo_pro_data['queries']) && is_array($seo_pro_data['queries'])) {
          $seo_pro_data['keywords'][$data['keyword']] = $url_query;
          $seo_pro_data['queries'][$url_query] = $data['keyword'];

          $this->cache->set('seo_pro', $seo_pro_data);                
        }
      }
    }
  }  
  
  public function editPageImmediately($page_id, $data) {   
    if ($data['field'] == 'name') {
      $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_page_description SET name = '" . $this->db->escape(urldecode($data['value'])) . "' WHERE page_id = '" . (int)$page_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
    } else {
      $this->db->query("UPDATE " . DB_PREFIX . "ocfilter_page SET `" . $this->db->escape($data['field']) . "` = '" . $this->db->escape($data['value']) . "' WHERE page_id = '" . (int)$page_id . "'");
    }

    $this->ocfilter->cache->key('page', $page_id)->delete();
    
    return true;
  }  
  
  public function editPageBatch($data = []) {   
    if ($data['edit_action'] == 'delete') {
      foreach ($this->getPages($data) as $result) {
        $this->deletePage($result['page_id']);
      }
       
      $this->cache->delete('seo_pro');
      $this->cache->delete('seopro.keywords');
      $this->cache->delete('seopro.queries');
       
      return true;
    }
  
    $sql = "UPDATE " . DB_PREFIX . "ocfilter_page op INNER JOIN " . DB_PREFIX . "ocfilter_page_description opd ON (op.page_id = opd.page_id)";
       
    $implode = [];
        
    if ($data['edit_action'] == 'update') {
      if (isset($data['edit_category_id']) && $data['edit_category_id'] != '*') {
        $implode[] = "op.category_id = '" . (int)$data['edit_category_id'] . "'";
      }
      
      if (isset($data['edit_status']) && $data['edit_status'] != '*') {
        $implode[] = "op.status = '" . (int)$data['edit_status'] . "'";
      }

      if (isset($data['edit_category']) && $data['edit_category'] != '*') {
        $implode[] = "op.category = '" . (int)$data['edit_category'] . "'";
      }

      if (isset($data['edit_product']) && $data['edit_product'] != '*') {
        $implode[] = "op.product = '" . (int)$data['edit_product'] . "'";
      }

      if (isset($data['edit_module']) && $data['edit_module'] != '*') {
        $implode[] = "op.module = '" . (int)$data['edit_module'] . "'";
      }

      if (isset($data['edit_sitemap']) && $data['edit_sitemap'] != '*') {
        $implode[] = "op.sitemap = '" . (int)$data['edit_sitemap'] . "'";
      }    
    } else { 
      $destination_columns = [
        'opd.`name`',             'opd.`heading_title`', 
        'opd.`meta_title`',       'opd.`description_top`', 'opd.`description_bottom`', 
        'opd.`meta_description`', 'opd.`meta_keyword`', 
      ];    
          
      foreach ($destination_columns as $column) {
        if ($data['edit_destination'] != 'all' && $column != 'opd.`' . $data['edit_destination'] . '`') {
          continue;
        }
        
        if ($data['edit_action'] == 'replace') {
          $implode[] = $column . " = REPLACE(" . $column . ", '" . $this->db->escape($data['edit_text_1']) . "', '" . $this->db->escape($data['edit_text_2']) . "')";
        } else if ($data['edit_action'] == 'add') {
          if ($data['edit_position'] == 'prepend') {
            $implode[] = $column . " = CONCAT('" . $this->db->escape($data['edit_text_1']) . "', " . $column . ")";
          } else if ($data['edit_position'] == 'append') {
            $implode[] = $column . " = CONCAT(" . $column . ", '" . $this->db->escape($data['edit_text_1']) . "')";
          }
        }      
      } 
    }
    
    if ($implode) {
      $sql .= " SET " . implode(", ", $implode);
    } else {
      return false;
    }      
    
    if ($data['edit_target'] == 'selected' && !empty($data['selected']) && is_array($data['selected'])) {
      $sql .= " WHERE op.page_id IN(" . implode(",", array_map('intval', $data['selected'])) . ")";
    } else if ($data['edit_target'] == 'filter') {
      $implode = [];
      
      if (!empty($data['filter_category_id'])) {
        $implode[] = "op.category_id = '" . (int)$data['filter_category_id'] . "'";
      }

      if (isset($data['filter_status']) && $data['filter_status'] != '*') {
        $implode[] = "op.status = '" . (int)$data['filter_status'] . "'";
      }

      if (!empty($data['filter_name'])) {
        $implode[] = "LCASE(opd.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])). "%'";
      }
      
      if ($implode) {
        $sql .= " WHERE " . implode(" AND ", $implode);
      } else {
        return false;
      }      
    }
               
    $this->db->query($sql);
    
    // Edit/Add keywords
    if ($data['edit_action'] != 'update' && ($data['edit_destination'] == 'keyword' || $data['edit_destination'] == 'all')) {
      $keyword_sql = "";
      
      if ($data['edit_action'] == 'replace') {
        $keyword_sql = "REPLACE(keyword, '" . $this->db->escape($data['edit_text_1']) . "', '" . $this->db->escape($data['edit_text_2']) . "')";
      } else if ($data['edit_action'] == 'add') {
        if ($data['edit_position'] == 'prepend') {
          $keyword_sql = "CONCAT('" . $this->db->escape($data['edit_text_1']) . "', keyword)";
        } else if ($data['edit_position'] == 'append') {
          $keyword_sql = "CONCAT(keyword, '" . $this->db->escape($data['edit_text_1']) . "')";
        }
      }        
      
      if ($keyword_sql) {
        $results = $this->getPages($data);
             
        if ($this->ocfilter->opencart->version >= 30) {
          foreach ($results as $result) {
            $this->db->query("UPDATE " . DB_PREFIX . "seo_url SET keyword = " . $keyword_sql . " WHERE `query` = 'ocfilter_page_id=" . (int)$result['page_id'] . "'");
          }
        } else {
          foreach ($results as $result) {
            $this->db->query("UPDATE " . DB_PREFIX . "url_alias SET keyword = " . $keyword_sql . " WHERE `query` = 'ocfilter_page_id=" . (int)$result['page_id'] . "'");
          }     
        }
                 
        $this->cache->delete('seo_pro');
        $this->cache->delete('seopro.keywords');
        $this->cache->delete('seopro.queries');       
      }
    }
    
    $this->ocfilter->cache->delete('ocfilter.page');
    
    return true;
  }

  public function deletePage($page_id) {
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_page WHERE page_id = '" . (int)$page_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_page_description WHERE page_id = '" . (int)$page_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_page_to_layout WHERE page_id = '" . (int)$page_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_page_to_store WHERE page_id = '" . (int)$page_id . "'");
    
    if ($this->ocfilter->opencart->version >= 30) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `query` = 'ocfilter_page_id=" . (int)$page_id . "'");
    } else {
      $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE `query` = 'ocfilter_page_id=" . (int)$page_id . "'");
    }

    $this->ocfilter->cache->key('page', $page_id)->delete();
  }

  public function getPage($page_id) {
    $query = $this->db->query("SELECT op.*, opd.*, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR ' > ') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) WHERE cp.category_id = op.category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id) AS path FROM " . DB_PREFIX . "ocfilter_page op LEFT JOIN " . DB_PREFIX . "ocfilter_page_description opd ON (op.page_id = opd.page_id) WHERE op.page_id = '" . (int)$page_id . "' AND opd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

    return $query->row;
  }

  public function getPages($data = []) {
    $sql = "SELECT op.*, opd.heading_title, opd.name, (SELECT cd.name FROM " . DB_PREFIX . "category_description cd WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd.category_id = op.category_id) AS category_name FROM " . DB_PREFIX . "ocfilter_page op LEFT JOIN " . DB_PREFIX . "ocfilter_page_description opd ON (op.page_id = opd.page_id) WHERE opd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (isset($data['filter_category_id'])) {
      $sql .= " AND op.category_id = '" . (int)$data['filter_category_id'] . "'";
    }

    if (isset($data['filter_status'])) {
      $sql .= " AND op.status = '" . (int)$data['filter_status'] . "'";
    }

    if (!empty($data['filter_name'])) {
      $sql .= " AND LCASE(opd.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])). "%'";
    }
    
    if (!empty($data['selected']) && is_array($data['selected'])) {
      $sql .= " AND op.page_id IN(" . implode(",", array_map('intval', $data['selected'])) . ")";
    }    

    $sql .= " ORDER BY (CASE WHEN (op.dynamic_id OR op.dynamic) THEN CONCAT(IF(op.dynamic_id, 1, op.dynamic), '.', IF(op.dynamic_id, op.dynamic_id, op.page_id), '.', (op.dynamic < 1), '.', op.category_id) ELSE CONCAT(op.category_id, opd.name) END)";

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

  public function getTotalPages($data = []) {
    $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ocfilter_page op LEFT JOIN " . DB_PREFIX . "ocfilter_page_description opd ON (op.page_id = opd.page_id) WHERE opd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (isset($data['filter_category_id'])) {
      $sql .= " AND op.category_id = '" . (int)$data['filter_category_id'] . "'";
    }

    if (isset($data['filter_status'])) {
      $sql .= " AND op.status = '" . (int)$data['filter_status'] . "'";
    }

    if (!empty($data['filter_name'])) {
      $sql .= " AND LCASE(opd.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])). "%'";
    }

    $query = $this->db->query($sql);

    return $query->row['total'];
  }

  public function getPageDescriptions($page_id) {
    $page_description_data = [];

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_page_description WHERE page_id = '" . (int)$page_id . "'");

    foreach ($query->rows as $result) {
      $page_description_data[$result['language_id']] = $result;
    }

    return $page_description_data;
  }
  
  public function getPageLayouts($page_id) {
    $page_layout_data = [];

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_page_to_layout WHERE page_id = '" . (int)$page_id . "'");

    foreach ($query->rows as $result) {
      $page_layout_data[$result['store_id']] = $result['layout_id'];
    }

    return $page_layout_data;
  }  
  
  public function getPageStores($page_id) {
    $query = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "ocfilter_page_to_store WHERE page_id = '" . (int)$page_id . "'");

    return array_column($query->rows, 'store_id');    
  }  
  
  public function getPageUrlKeyword($page_id) {
    if ($this->ocfilter->opencart->version >= 30) {      
      $page_url_keyword_data = [ [] ];

      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'ocfilter_page_id=" . (int)$page_id . "'");

      foreach ($query->rows as $result) {
        $page_url_keyword_data[$result['store_id']][$result['language_id']] = $result['keyword'];
      }

      return $page_url_keyword_data;      
    } else {
      $query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE `query` = 'ocfilter_page_id=" . (int)$page_id . "'");
        
      if ($query->num_rows) {
        return $query->row['keyword'];
      }        
      
      return '';
    }    
  }  
  
  public function getPageValues($page_id) {
    $page_values = [];
    
    $page_info = $this->getPage($page_id);
    
    if ($page_info && $page_info['params']) {
      $params = json_decode($page_info['params']);
      
      foreach ($params as $filter_key => $values) {
        $this->ocfilter->params->key($filter_key)->expand($filter_id, $source); 
        
        foreach ($values as $value_id) {
          if ($this->ocfilter->params->isRange($value_id)) {
            list($min, $max) = $this->ocfilter->params->parseRange($value_id);

            if (isset($min) && isset($max)) {
              $page_values[] = [
                'filter_key' => $filter_key,
                'filter_id' => $filter_id,
                'source' => $source,
                'value_id' => 0,
                'min' => $min,
                'max' => $max,
              ];
            }
          } else {
            $page_values[] = [
              'filter_key' => $filter_key,
              'filter_id' => $filter_id,
              'source' => $source,
              'value_id' => $value_id,
              'min' => 0,
              'max' => 0,
            ];              
          }         
        }
      }
    }
  
    return $page_values;
  }    

  public function getSeoUrl($keyword, $category_id, $store_id = null, $language_id = null) {
    if ($this->ocfilter->opencart->version >= 30) {
      $query = $this->db->query("SELECT keyword, REPLACE(`query`, 'ocfilter_page_id', 'page_id') AS `query` FROM " . DB_PREFIX . "seo_url WHERE store_id = '" . (int)$store_id . "' AND language_id = '" . (int)$language_id . "' AND keyword = '" . $this->db->escape($keyword) . "'");
    } else {
      $query = $this->db->query("SELECT keyword, REPLACE(`query`, 'ocfilter_page_id', 'page_id') AS `query` FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword) . "'");
    }      
    
    if ($query->num_rows) {
      list($entity, $id) = explode('=', $query->row['query']);
      
      if ($entity == 'page_id') {
        $page_info = $this->getPage($id);
        
        if ($page_info && $page_info['category_id'] != $category_id) {
          return [];
        }
      }
    }

    return $query->row;
  }
}