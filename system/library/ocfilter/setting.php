<?php

namespace OCFilter;

class Setting extends Factory {
  private $tmp = [];
  private $installed = false;
  
  public function __construct() {   
    $query = $this->opencart->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "ocfilter_setting'");
    
    $this->installed = (bool)$query->num_rows;
    
    if (!$this->installed) {
      return;  
    }
      
    $query = $this->opencart->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_setting");
    
    foreach ($query->rows as $result) {
      $this->tmp[$result['key']] = ($result['serialized'] ? json_decode($result['value'], true) : $result['value']);
    }
  }
  
  public function set($setting = array()) {
    if (!$this->installed) {
      return;  
    }
    
    $this->tmp = [];
    
    $this->query("TRUNCATE " . DB_PREFIX . "ocfilter_setting");
    
    foreach ($setting as $key => $value) {
      $this->add($key, $value);
    }  
  }
  
  public function add($key, $value) {
    if (!$this->installed) {
      return;  
    }
    
    $this->tmp[$key] = $value;

    $serialized = is_array($value);
    
    if ($serialized) {
      $value = json_encode($value);
    } else if (is_numeric($value) || is_bool($value)) {
      $value = (int)$value;
    } else {
      $value = (string)$value;
    }
    
    $this->opencart->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_setting (`key`, `value`, `serialized`) VALUES ('" . $this->opencart->db->escape($key) . "', '" . $this->opencart->db->escape($value) . "', '" . (int)$serialized . "') ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `serialized` = VALUES(`serialized`)");
  }
  
  public function get($key) {  
    if (!$this->installed) {
      return;  
    }
    
    if (isset($this->tmp[$key])) {
      return $this->tmp[$key];
    }   
    
    $query = $this->opencart->db->query("SELECT * FROM " . DB_PREFIX . "ocfilter_setting WHERE `key` = '" . $this->opencart->db->escape($key) . "'");
    
    if ($query->num_rows) {
      $value = ($query->row['serialized'] ? json_decode($query->row['value'], true) : $query->row['value']);
      
      $this->tmp[$key] = $value;
      
      return $value;
    }
    
    return null;
  }  
  
  public function export() {
    
  }
  
  public function import() {
    
  }  
}