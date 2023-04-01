<?php

namespace OCFilter;

abstract class Factory {
  protected static $core;
  
	public function __construct($core = null) {  
    if ($core instanceof Core) {     
      self::$core = $core;
    }		
  }  
  
	public function __get($key) {  
    return self::$core->{$key}; 
	}

	public function __set($key, $value) {   
		self::$core->{$key} = $value;
	}

  public function __call($key, array $args) {    
    if (!method_exists(self::$core, $key)) {
      throw new \Exception('Error: Could not found method OCFilter->' . $key . '!');
    }

    return call_user_func_array(array(self::$core, $key), $args);
  }
}