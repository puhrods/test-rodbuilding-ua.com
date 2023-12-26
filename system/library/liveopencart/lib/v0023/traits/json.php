<?php

namespace liveopencart\lib\v0023\traits;

trait json {
	
	public function arrayToUTF8($param) {
		if (is_array($param)) {
			foreach ($param as $k => $v) {
				$param[$k] = $this->arrayToUTF8($v);
			}
		} else if (is_string ($param)) {
			return mb_convert_encoding($param, 'UTF-8');
			//return utf8_encode($param);
		}
		return $param;
	}
	
	public function jsonEncodeToUTF8($arr) {
		return json_encode( $this->arrayToUTF8($arr) );
	}
	
}