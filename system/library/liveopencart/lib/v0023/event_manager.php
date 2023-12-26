<?php

namespace liveopencart\lib\v0023;

class event_manager {

	protected $events = array();
	protected $existing_codes = array();
	
	public function addHanderIfNotExistByCode($event_name, $callable, $code='') {
		if ( !$code || !in_array($code, $this->existing_codes) ) {
			$this->on($event_name, $callable, $code);
		}
	}
	
	public function on($event_name, $callable, $code='') {
		$this->events[$event_name][] = $callable;
		if ( $code ) {
			$this->existing_codes[] = $code;
		}
	}
	
	public function trigger($event_name, $args) {
		$result = null;
		if ( isset($this->events[$event_name]) ) {
			foreach ( $this->events[$event_name] as $callable ) {
				$current_result = call_user_func_array($callable, $args);
				if ( $current_result ) {
					$result = $current_result;
				}
			}
		}
		return $result;
	}
	
}