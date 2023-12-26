<?php

namespace liveopencart\lib\v0023;

class library {
	
	protected $registry;
	
	static $_class_id = '';
	
	public function __construct($registry) {
		$this->registry = $registry;
		//$this->init();
	}
	
	public function __get($name) {
		return $this->registry->get($name);
	}
	
	public static function getClassId() {
		if ( static::$_class_id ) {
			return static::$_class_id;
		} else {
			return str_replace('\\', '_', get_called_class());
		}
	}
	
	protected static function initInstance($registry, $args=array()) {
		if ( !$registry->has( static::getClassId() ) ) {
			$instance = new static($registry);
			$registry->set(static::getClassId(), $instance);
			
			call_user_func_array( array($instance, 'init') , $args);
			//$registry->get(static::getClassId())->init(); 
		}
	}
	
	public static function getInstance($registry) {
		static::initInstance($registry, array_slice(func_get_args(), 1));
		return $registry->get(static::getClassId());
	}

	// to create instances of classes based on the class 'liveopencart ... library'
	protected function getLibraryInstanceByName($lib_name) { 
		
		if ( !class_exists($lib_name) && class_exists(__NAMESPACE__.'\\'.$lib_name) ) {
			$lib_name = __NAMESPACE__.'\\'.$lib_name;
		}
		$args = func_get_args();
		$args[0] = $this->registry;
		return forward_static_call_array(array($lib_name, 'getInstance'), $args);
		//return $lib_name::getInstance()
		//return library_loader::getLibraryInstance($this->registry, $lib_name);
	}
	
	// to create instances of classes not based in the class 'liveopencart ... library' (means also do not put it to 'registry')
	protected function getOuterLibraryInstanceByName($lib_name) {
		if ( !class_exists($lib_name) && class_exists(__NAMESPACE__.'\\'.$lib_name) ) {
			$lib_name = __NAMESPACE__.'\\'.$lib_name;
		}
		$instance = new $lib_name($this->registry);
		if (method_exists($instance, 'init')) {
			call_user_func_array( array($instance, 'init') , array_slice(func_get_args(), 1));
		}
		return $instance;
	}
	
	protected function inAdminSection() {
		return defined('DIR_CATALOG');
	}
	protected function inCustomerSection() {
		return !$this->inAdminSection();
	}

	
	protected function init() {
		
	}
	
}