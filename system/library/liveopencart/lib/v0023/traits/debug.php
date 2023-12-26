<?php
namespace liveopencart\lib\v0023\traits;

trait debug {
	
	protected function isDebug() {
		return substr(parse_url(HTTP_SERVER)['host'], -5) == '.tnkr';
	}
	
}