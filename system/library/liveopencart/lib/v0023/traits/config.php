<?php

namespace liveopencart\lib\v0023\traits;

trait config {
	
	protected function getExtensionSettingCode() {
		return $this->extension_type.'_'.$this->extension_code;
	}
	
	protected function getExtensionSettingPrefix() {
		return $this->getExtensionSettingCode().'_';
	}
	
	protected function getExtensionConfig($setting_name) {
		return $this->config->get($this->getExtensionSettingPrefix().$setting_name);
	}
	
}