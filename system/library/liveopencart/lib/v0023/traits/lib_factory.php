<?php

namespace liveopencart\lib\v0023\traits;

trait lib_factory {
	
	protected function getFullLibClassName($lib_name) {
		$instance_namespace = '\liveopencart\lib\v0023';
		return $instance_namespace.'\\'.$lib_name;
	}
	
	protected function getNewLibInstance() {
		
		$args = func_get_args();
		$lib_name = $args[0];
		$args = array_slice($args, 1);
		
		$class_name = $this->getFullLibClassName($lib_name);
		
		return (new \ReflectionClass($class_name))->newInstanceArgs($args);
		
	}
	
}