<?php
  
namespace OCFilter;

class Cache extends Factory {
  const PREFIX = 'ocfilter';
  const EXPIRE = 7200;
  
  private $memory = array();
  private $_key = '';

  public function __construct() {
    if (!(time() % 5) && $this->config('cache') && $this->config('cache_store') == 'db') {
      $this->query("DELETE FROM " . DB_PREFIX . "ocfilter_cache WHERE `expire` < '" . (int)time() . "'");
    }      
  }
  
  public function get($key = '') {
    if (!$key) {
      $key = $this->_key;
    }    
    
    if ($this->config('cache') && $key) {     
      $this->_key = '';
      
      if ($this->config('cache_store') == 'system') {
        $value = $this->opencart->cache->get($key);
        
        return (is_null($value) ? false : $value);
      } else {
        $key_hash = crc32($key);
        
        if (isset($this->memory[$key_hash])) {
          return $this->memory[$key_hash];
        }
        
        $query = $this->query("SELECT `value` FROM " . DB_PREFIX . "ocfilter_cache WHERE `key` = '" . $this->opencart->db->escape($key_hash) . "'");
        
        if ($query->num_rows && $query->row['value']) {
          $value = json_decode($query->row['value'], true);
          
          $this->memory[$key_hash] = $value;
          
          return $value;
        }
      }
    }

    return false;
  }

  public function set() {
    $args = func_get_args();
    
    if (isset($args[1])) {
      $key = $args[0];
      $value = $args[1];
    } else if ($args) {
      $key = $this->_key;
      $value = $args[0];
    }       
    
    if ($this->config('cache') && $key) {     
      $this->_key = '';
      
      if ($this->config('cache_store') == 'system') {
        return $this->opencart->cache->set($key, $value);
      } else {          
        $key_hash = crc32($key);      
           
        $this->query("INSERT INTO " . DB_PREFIX . "ocfilter_cache SET `key` = '" . $this->opencart->db->escape($key_hash) . "', path = '" . $this->opencart->db->escape(preg_replace('/\.\{.+$/', '', $key)) . "', `value` = '" . $this->opencart->db->escape(json_encode($value)) . "', `expire` = '" . (time() + self::EXPIRE) . "' ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `expire` = VALUES(`expire`)");
      
        $this->memory[$key_hash] = $value;
      
        return true;        
      }           
    }

    return false;
  }

  public function delete($key = '') {       
    if (!$key) {
      $key = $this->_key;
    }
      
    if ($this->config('cache') && $key) {         
      $this->_key = '';
      
      if ($this->config('cache_store') == 'system') {
        if ($key == '*') {
          $key = self::PREFIX;
        }

        return $this->opencart->cache->delete($key);
      } else {
        if ($key == '*') {
          $this->query("TRUNCATE " . DB_PREFIX . "ocfilter_cache");
          
          $this->memory = array();
        } else {
          $this->query("DELETE FROM " . DB_PREFIX . "ocfilter_cache WHERE path LIKE '" . $this->opencart->db->escape($key) . "%'");
        }
        
        return true;
      }       
    }
  }
    
  /*
    Method chaining
    e.g.: $this->ocfilter->cache->key('filter.10')->get();
  */
  public function key() {
    $parts = func_get_args();
    
    $key = self::PREFIX;
    
    foreach ($parts as $part) {
      if (is_bool($part)) {
        $key .= '.' . (int)$part;
      } else if (is_null($part)) {
        $key .= '.0';
      } else if (is_array($part)) {
        $key .= '.' . (string)crc32(json_encode($part));
      } else if ($part === '*' && $this->config('cache_store') == 'db') {
        $key .= '.%';
      } else {  
        $key .= '.' . (string)$part;
      }
    }       

    $this->_key = $key;

    return $this;
  }
    
  /*
    $key = (string)$this->ocfilter->cache->key('filter.10');
  */  
  public function __toString() {
    return $this->_key;
  }
}