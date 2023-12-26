<?php

namespace liveopencart\lib\v0023;

class theme_details {
  
  use traits\theme;
  
  protected $registry;
  
  public function __construct($registry) {
		$this->registry = $registry;
	}
	public function __get($key) {
		return $this->registry->get($key);
	}
  
  public function init($params=array()) {
    
    $this->setThemesShorten( $this->getValueFromArrayIfIsSet($params, 'themes_shorten', array()) );
    $this->setThemeSiblingDir( $this->getValueFromArrayIfIsSet($params, 'sibling_dir') );
    $this->setCustomThemeName( $this->getValueFromArrayIfIsSet($params, 'custom_theme_name') );
  }
  
  protected function getValueFromArrayIfIsSet($arr, $key, $default=false) {
    if ( isset($arr[$key]) ) {
      return $arr[$key];
    } else {
      return $default;
    }
  }
  
}