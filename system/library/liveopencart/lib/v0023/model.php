<?php
namespace liveopencart\lib\v0023;

class Model extends \Model {
	
	protected $simple_db;
	
	public function __construct($registry) {
		call_user_func_array( array('parent', '__construct') , func_get_args());
		
		$this->simple_db = new simple_db($registry);
		
	}
	
	
}