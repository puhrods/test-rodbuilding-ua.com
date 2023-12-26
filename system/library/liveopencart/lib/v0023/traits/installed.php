<?php

namespace liveopencart\lib\v0023\traits;

trait installed {
 
    protected $extension_installed_cache_name = 'installed_extensions';
    
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