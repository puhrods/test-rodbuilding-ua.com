<?php

namespace liveopencart\lib\v0023\traits;

trait curl {
 
	protected function getCURLData($url) {
		$ch = curl_init($url);
		//curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$result = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	
		return array(
			'code' => $code,
			'url_exists' => ($code == 200),
			'result' => $result,
		);
	}
	
}