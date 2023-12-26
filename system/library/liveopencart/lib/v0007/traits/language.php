<?php

namespace liveopencart\lib\v0007\traits;

trait language {
	
	protected function loadLanguage() {
		$this->load->language( $this->getRouteExtension() );
	}
	
	protected function getLanguageData() {
		$language = new \Language($this->config->get('language_directory'));
		$language->load( $this->getRouteExtension() );
		return $language->all();
	}
	
	protected function getLanguageValueIfExists($key) {
		$all = $this->language->all();
		if ( isset($all[$key]) ) {
			return $all[$key];
		}
	}
	
}