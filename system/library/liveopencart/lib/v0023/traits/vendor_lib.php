<?php

namespace liveopencart\lib\v0023\traits;

trait vendor_lib {
	
	use curl;
	use language;
	
	protected function getVendorDir() {
		$dir = DIR_SYSTEM.'library/liveopencart/lib/vendor/';
		if ( !is_dir($dir) ) {
			mkdir($dir, 0755, true);
		}
		
		return $dir;
	}
	
	protected function installVendorLib($url, $md5_hash='') {
		
		$curl_data = $this->getCURLData($url);
		
		if ( !$curl_data['url_exists'] ) {
			return $this->getLanguageValue('error_source_file_is_not_found', 'Source file is not found');
		}
		
		if ( $md5_hash && md5_file($url) != $md5_hash ) {
			return $this->getLanguageValue('error_wrong_hash_remote', 'Wrong remote file hash');
		} else {
			
			$tmp_file_name = $this->getVendorDir().basename($url);
			
			file_put_contents($tmp_file_name, $curl_data['result']);
					
			if ( $md5_hash && md5_file($tmp_file_name) != $md5_hash ) { // recheck
				@unlink($tmp_file_name);
				return $this->getLanguageValue('error_wrong_hash_local', 'Wrong downloaded file hash');
			} else {
				$phar = new \PharData($tmp_file_name);
					
				$phar->extractTo( $this->getVendorDir(), null, true );
				@unlink($tmp_file_name);
			}
			
		}
		
	}
	
	protected function getExtensionInstalledStatus($code, $cache_key='', $extra_function_to_check=false) {
		
		if ( !$cache_key || !$this->hasCache($this->extension_installed_cache_name, $cache_key) ) {
			$query = $this->db->query("SELECT * FROM ".DB_PREFIX."extension WHERE `type` = 'module' AND `code` = '".$this->db->escape($code)."'");
			$installed = $query->num_rows && ( !$extra_function_to_check || $extra_function_to_check() );
			if ( !$cache_key ) {
				return $installed;
			}
			$this->setCache($this->extension_installed_cache_name, $cache_key, $installed);
		}
		return $this->getCache($this->extension_installed_cache_name, $cache_key);
	}
}