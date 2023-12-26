<?php

namespace liveopencart\lib\v0023;

class sub_library {
  
  protected $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}
	public function __get($key) {
		return $this->registry->get($key);
	}
  
}