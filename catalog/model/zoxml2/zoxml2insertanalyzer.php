<?php
class ModelZoXml2ZoXml2InsertAnalyzer extends Model {

public function doInsertAnalyzer($output,$data,$item) {
  $parts = explode (':',$data['settings']['insert_analyzer']);
  if (isset($parts[1]))   return $this->old_doInsertAnalyzer(trim($parts[0]),$output,$data,$item,trim($parts[1]));
  return $this->old_doInsertAnalyzer($data['settings']['insert_analyzer'],$output,$data,$item,'');
  }
public function old_doInsertAnalyzer($index,$output,$data,$item,$param) {
  if ($index=='log_output') {
    if ($param=='all' || $param==$output['sku']) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'log_output', data = '" . $this->db->escape(json_encode($output)) . "', user = '" . $this->db->escape($data['user']) . "'");
    return $output;
    }
  if ($index=='names_stop_list_mp_tkani') {
    if (strpos($output['name'], "М/П ткани")!==FALSE) return NULL; 
    return $output;
    }
  if ($index=='min_20') {
    if ($output['quantity'] < 20) return NULL; 
    return $output;
    }
  if ($index=='js-company') {
    if (!empty($item->typePrefix)) {
      $output['attr'][27] = trim($item->typePrefix); 
      }
    if (!empty($item->_categoryId)) {
      if (!empty($data['LoadCatNames'][trim($item->_categoryId)])) $output['attr'][28] = $data['LoadCatNames'][trim($item->_categoryId)]; 
      else                                                         $output['attr'][28] = trim($item->_categoryId); 
      }
    return $output;
    }
  if ($index=='poip') {
    if (isset($output['option'][0] )) $output['option'][0]['poip'] = $output['image'];
    return $output;
    }
  if ($index=='UseWhiteListOfSku') { 
    if (!isset($data['WhiteListOfSku']))                 {
//      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'Error', data = '" . $this->db->escape('WhiteListOfSku отсутствует!') . "', user = '" . $this->db->escape($data['user']) . "'");
      return $output;
      }
    if ( isset($data['WhiteListOfSku'][$output['sku']])) return $output;
    return NULL;
    }
  if ($index=='UseBlackListOfSku') { 
    if (!isset($data['BlackListOfSku']))                 return $output;
    if (!isset($data['BlackListOfSku'][$output['sku']])) return $output;
    return NULL;
    }
  if ($index=='SNT') { 
    return NULL;
    }
  if ($index=='Skip_no_image') { 
    if (count($output['image'])==0) return NULL;
    return $output;
    }
  if ($index=='Skip_if_SKU_exist') { 
    if (empty($output['sku'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape('ПРОПУСК - артикул не определен!') . "', user = '" . $this->db->escape($data['user']) . "'");
      return NULL;
      }
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($output['sku']) . "'");
    if ($query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape('ПРОПУСК - артикул уже есть на сайте: '. $output['sku']) . "', user = '" . $this->db->escape($data['user']) . "'");
      return NULL;
      }
    return $output;
    }
  if ($index=='Skip_if_ISBN_exist') { 
    if (empty($output['isbn'])) return NULL;
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE isbn = '" . $this->db->escape($output['isbn']) . "'");
    if ($query->row) return NULL;
    return $output;
    }
  if ($index=='Skip_if_UPC_exist') { 
    if (empty($output['upc'])) return NULL;
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE upc = '" . $this->db->escape($output['upc']) . "'");
    if ($query->row) return NULL;
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE upc = '" . $this->db->escape($output['upc']) . "'");
    if ($query->row) return NULL;
    return $output;
    }
  if ($index=='Skip_if_MODEL_exist') { 
    if (empty($output['model'])) {
      $txt = "Блокируем добавление товара. Модель не указана! Номер товара: " . $output['sku'];
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($data['session_key']) . "', type = 'ПРОПУСК', data = '" . $this->db->escape($txt) ."'");
      return NULL;
      }
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE model = '" . $this->db->escape($output['model']) . "'");
    if ($query->row) {
      $txt = "Блокируем добавление товара. Модель : " . $output['model'] . " уже есть на сайте!";
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($data['session_key']) . "', type = 'ПРОПУСК', data = '" . $this->db->escape($txt) ."'");
      return NULL;
      }
    return $output;
    }
  return $output;
  }

}
?>