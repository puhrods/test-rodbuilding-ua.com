<?php

namespace OCFilter;

class Params extends Factory {
  // DO NOT CHANGE ALL CONST!
  const INDEX = 'ocf';
  
  /*
  ?ocf=F1S0V1V2V3F2S0V4V5F3S0VN43D9T56D5 => array(
    1.0 => array( 1, 2, 3 ),
    2.0 => array( 4, 5 ),
    3.0 => array( -43.9-56.5 ),    
  )
  */
  const SEP_FILT = 'F'; // Filter id prefix
  const SEP_FSRC = 'S'; // Filter source prefix
  const SEP_VALS = 'V'; // Value prefix
  const SEP_SDOT = 'D'; // Slider decimal dot 
  const SEP_SNEG = 'N'; // Slider negative prefix
  const SEP_SRAN = 'T'; // Slider range separator

  const ID_PATTERN                = '/^\d+?$/';
  const KEY_PATTERN               = '/^\d+\.\d+?$/';
  const PARAMS_PATTERN            = '/F(\d+)S(\d+)V((N?[0-9DTN]|V\d+)+)/';
  const CHECK_RANGE_PATTERN       = '/^(-?\d*\.?\d+)\-(-?\d*\.?\d+)$/';
  const CHECK_RANGE_PARAM_PATTERN = '/^(N?\d*D?\d+)T(N?\d*D?\d+)$/';
  const GET_RANGE_PATTERN         = '/^.*?(-?\d+\.?\d*)\D?.*?(-?\d*\.?\d*)?\D*$/';

  private $_key;
  private $_source;
  private $_special;
  
  // Filter source identifier constant
  // DO NOT CHANGE!
  private $_SOURCE = [
    'special'    => 0, // Special Filters (Price, Manufacturer, etc.)
    'default'    => 1, // Default Filters (manual adding)
    'attribute'  => 2, // Attributes
    'filter'     => 3, // Native OpenCart Filters
    'option'     => 4, // Product Options
  ]; 

  // Special filter keys
  private $_SPECIAL_KEY = [
    'manufacturer'  => '1.0',
    'price'         => '2.0',
    'stock'         => '3.0',
    'discount'      => '4.0',
    'newest'        => '5.0',
    'weight'        => '6.0',
    'width'         => '7.0',
    'height'        => '8.0',
    'length'        => '9.0',
  ];    
  
  private $params = [];
     
  public function getIndex() {
    return self::INDEX;
  } 
  
  public function getSepFilter() {
    return self::SEP_FILT;
  } 

  public function getSepSource() {
    return self::SEP_FSRC;
  } 

  public function getSepValues() {
    return self::SEP_VALS;
  }   
  
  public function getSepSliderDot() {
    return self::SEP_SDOT;
  }   
  
  public function getSepSliderNegative() {
    return self::SEP_SNEG;
  }   

  public function getSepSliderRange() {
    return self::SEP_SRAN;
  }   
   
  /*
    Filter keys
    
    (bool)  $this->ocfilter->params->key('4.0')->is('discount');
    (bool)  $this->ocfilter->params->key('1.0')->is('special');
    
    (int)   $this->ocfilter->params->source('attribute')->id();
    (string)$this->ocfilter->params->source(3)->name();
    (bool)  $this->ocfilter->params->source(3)->is('filter');  
    
    (string)$this->ocfilter->params->special('manufacturer')->key();
    (string)$this->ocfilter->params->key(4.0)->special();
  */
    
  public function key($key = null) {
    if (!is_null($key)) {
      $this->_key = $key;  
    }
    
    if ($this->_special && isset($this->_SPECIAL_KEY[$this->_special])) {
      $_special = $this->_special;
      
      $this->_special = null;
      
      return $this->_SPECIAL_KEY[$_special];
    }
              
    return $this;   
  }     
  
  public function source($source) {
    $this->_source = $source;
    
    return $this;
  }     

  public function special($source = null) {
    if (!is_null($source)) {
      $this->_special = $source;
      
      return $this;      
    }
    
    if ($this->_key && false !== ($name = array_search($this->_key, $this->_SPECIAL_KEY))) {           
      $this->_key = null;
      
      return $name;
    }
    
    return '';
  }   
  
  public function is($name) {
    if ($this->_key) {     
      if (isset($this->_SOURCE[$name])) {
        $this->expand($id, $source);
        
        return ($source == $this->_SOURCE[$name]);      
      }
      
      if (isset($this->_SPECIAL_KEY[$name])) {
        return ($this->_key == $this->_SPECIAL_KEY[$name]);
      }
      
      $this->_key = null;
    }
    
    if (is_numeric($this->_source)) {
      if (isset($this->_SOURCE[$name])) {
        return ($this->_source == $this->_SOURCE[$name]);
      }
      
      $this->_source = null;
    }
    
    return false;
  }
  
  // Get source id by name
  public function id() {
    if (is_string($this->_source) && isset($this->_SOURCE[$this->_source])) {      
      $id = $this->_SOURCE[$this->_source];
      
      $this->_source = null;
      
      return $id;
    }
    
    return 0;
  }
  
  // Get source name by id
  public function name() {
    if (is_numeric($this->_source) && false !== ($name = array_search($this->_source, $this->_SOURCE))) {           
      $this->_source = null;
      
      return $name;
    }
    
    return '';
  }  
  
  // Separate filter key into $id and $source
  public function expand(&$id, &$source) {
    if ($this->_key) {
      $id = 0;
      $source = 0;    
      
      $parts = explode('.', $this->_key);
      
      if (isset($parts[0]) && isset($parts[1])) {
        list($id, $source) = $parts;
      } 

      $this->_key = null;
    }
  }
  
  public function getSpecialKeys() {
    return $this->_SPECIAL_KEY;
  }
  
  // PARAMS
  public function set($params) {
    $this->params = $this->decode($params);
  }

  public function get() {
    return $this->params;
  }
  
  public function getWithout($filter_key, $params = []) {
    if (!$params) {
      $params = $this->params;
    }
    
    if (isset($params[$filter_key])) {
      unset($params[$filter_key]);
    }

    return $params;
  }
  
  public function getValueParams($filter_key, $value_id, $type = 'checkbox') {
    $params = $this->params;

    if (isset($params[$filter_key])) {
      if (false !== $key = array_search($value_id, $params[$filter_key])) {
        unset($params[$filter_key][$key]);
      } else {
        $params[$filter_key][] = $value_id;
      }
    } else {
      $params[$filter_key] = [ $value_id ];
    }
    
    if (!$params[$filter_key]) {
      unset($params[$filter_key]);
    }

    return $params;
  }

  public function has($filter_key = null, $value_id = null) {
    if (!is_null($filter_key) && !is_null($value_id)) {
      return !empty($this->params[$filter_key]) && in_array($value_id, $this->params[$filter_key]);      
    } else if (!is_null($filter_key)) {
      return !empty($this->params[$filter_key]);
    } else if (!is_null($value_id)) {      
      foreach ($this->params as $filter_key => $values) {       
        if (in_array($value_id, $values)) {
          return true;
        }
      }
    }
    
    return false;
  }
  
  public function hasSlider($filter_key = null) {
    if (!is_null($filter_key)) {
      return is_string($this->getSelectedFilter($filter_key));
    } else {
      return $this->hasParamsSlider($this->params);
    }
    
    return false;
  }
  
  public function hasParamsSlider($params) {
    foreach ($params as $filter_key => $values) {       
      if ($values && is_array($values) && $this->isRange($values[0])) {
        return true;
      }
    }
    
    return false;
  }
  
  public function isEnabledSlider($filter_key) {   
    return (!isset($this->opencart->request->get[self::INDEX . '_slider']) || (is_array($this->opencart->request->get[self::INDEX . '_slider']) && in_array($filter_key, $this->opencart->request->get[self::INDEX . '_slider'])));
  }

  public function getSelectedFilter($filter_key) {
    if ($this->has($filter_key)) {
      $values = $this->params[$filter_key];
      
      if ($values && is_array($values) && $this->isRange($values[0])) {
        return $values[0];
      }
      
      return $values;
    }

    return false;
  }

  public function getSelectedFiltersCount() {
    return count($this->params);
  }

  public function getSelectedValuesCount($filter_key = null) {
    if (!is_null($filter_key) && false !== ($values = $this->getSelectedFilter($filter_key))) {
      return count($values);
    }

    return (count($this->params, COUNT_RECURSIVE) - $this->getSelectedFiltersCount());
  } 
    
  public function normalizeArray(array $params) {
    foreach ($params as $filter_key => $values) {
      if (isset($values['min']) && isset($values['max'])) {
        $params[$filter_key] = [
          'min' => (float)$values['min'],
          'max' => (float)$values['max'],
        ];
      } else {
        $values = array_map('strval', $values);
        
        sort($values);
        
        $params[$filter_key] = $values;
      }     
    }
    
    ksort($params);
    
    return $params;
  }  
    
  // From params string to array
  public function decode($params = '') {
    if (!$params) {
      return [];
    }  
    
    $decode = [];
    
    if (!$this->isValidParams($params)) {
      return $decode;
    }
    
    preg_match_all(self::PARAMS_PATTERN, $params, $output);
    
    foreach ($output[1] as $key => $filter_id) {
      $filter_key = $filter_id . '.' . $output[2][$key];
        
      if (!$this->isKEY($filter_key)) {
        continue;
      }
      
      if ($this->isRangeParam($output[3][$key])) {
        $range = strtr($output[3][$key], [
          self::SEP_SNEG => '-',
          self::SEP_SDOT => '.',
          self::SEP_SRAN => '-',
        ]);
        
        list($min, $max) = $this->parseRange($range);

        $decode[$filter_key] = [ $min . '-' . $max ];
        
        continue;
      }  
      
      $values = explode(self::SEP_VALS, $output[3][$key]);          

      $values = array_filter($values, 'strlen');

      if (!$values) {
        continue;
      }    
      
      $values = array_map('strval', $values);
      
      sort($values);
      
      $decode[$filter_key] = $values;
    }
    
    ksort($decode);

    return $decode;
  }
  
  // From params array to string
  public function encode(array $params) {
    if (!$params) {
      return '';
    }  
    
    $encode = [];

    ksort($params);

    foreach ($params as $filter_key => $values) {
      if ($values) {
        $values = array_values($values);
        
        $filter_key = str_replace('.', self::SEP_FSRC, $filter_key);

        if ($this->isRange($values[0])) {
          list($min, $max) = $this->parseRange($values[0]);
          
          $encode[] = $filter_key . self::SEP_VALS . 
            strtr($min, [ '-' => self::SEP_SNEG, '.' => self::SEP_SDOT ]) . 
            self::SEP_SRAN . 
            strtr($max, [ '-' => self::SEP_SNEG, '.' => self::SEP_SDOT ]);
        } else {
          $values = array_map('strval', $values);
          
          sort($values);

          $encode[] = $filter_key . self::SEP_VALS . implode(self::SEP_VALS, $values);          
        }       
      }
    }

    if ($encode) {
      return self::SEP_FILT . implode(self::SEP_FILT, $encode);
    }
    
    return '';
  }
  
  public function isRange($string) {
    return (is_string($string) && preg_match(self::CHECK_RANGE_PATTERN, $string));
  }

  public function isRangeParam($string) {
    return (is_string($string) && preg_match(self::CHECK_RANGE_PARAM_PATTERN, $string));
  }

  public function parseRange($string) {
    $string = preg_replace('/(\d+),(\d+)/', '$1.$2', $string);
    $string = preg_replace('/(\d+)\s(\d+)/', '$1$2', $string);
    
    preg_match(self::GET_RANGE_PATTERN, $string, $output);

    if (isset($output[1]) && isset($output[2])) {
      if (strlen($output[2]) > 0) {
        return [ (float)$output[1], (float)$output[2] ];
      } else {
        return [ (float)$output[1], (float)$output[1] ];
      }       
    }

    return false;
  }

  public function isID($string) {
    return preg_match(self::ID_PATTERN, $string);
  }  
  
  public function isKEY($string) {
    return preg_match(self::KEY_PATTERN, $string);
  }   

  public function isValidParams($string) {
    return (preg_match(self::PARAMS_PATTERN, $string, $out) && isset($out[4]));
  }     
}