<?php

namespace liveopencart\lib\v0023\traits;

trait theme {
	protected $theme_name 		= '';
	protected $themes_shorten = array();
	protected $theme_sibling_dir = '';
	protected $custom_theme_name = '';
	
	protected function setThemesShorten($themes_shorten) {
		$this->themes_shorten = $themes_shorten;
	}
	
	protected function getThemesShorten() {
		return $this->themes_shorten;
	}
	
	protected function setThemeSiblingDir($dir_path) {
		$this->theme_sibling_dir = $dir_path;
	}
	
	protected function getThemeSiblingDir() {
		return $this->theme_sibling_dir;
	}
	
	protected function setCustomThemeName($theme_name) {
		$this->custom_theme_name = $theme_name;
	}
	
	protected function getCustomThemeName() {
		return $this->custom_theme_name;
	}
	
	public function getThemeName() {
		if ( !$this->theme_name) {
			$theme_name = $this->getCustomThemeName();
			
			if ( !$theme_name ) {
				if ($this->config->get('config_theme') == 'theme_default' || $this->config->get('config_theme') == 'default') {
					$theme_name = $this->config->get('theme_default_directory');
				} else {
					$theme_name = substr($this->config->get('config_theme'), 0, 6) == 'theme_' ? substr($this->config->get('config_theme'), 6) : $this->config->get('config_theme') ;
				}
				
				if ($theme_name == 'BurnEngine') {
					$theme_info = (array) $this->config->get( 'BurnEngine_theme');
					if ($theme_info && !empty($theme_info['id']) ) {
						$theme_name = $theme_name.'_'.$theme_info['id']; 
					}
				}
				
				// shorten theme name
				if ( $this->getThemesShorten() ) {
					foreach ( $this->getThemesShorten() as $theme_shorten ) {
						$theme_shorten_length = strlen($theme_shorten);
						if ( substr($theme_name, 0, $theme_shorten_length) == $theme_shorten ) {
							$theme_name = substr($theme_name, 0, $theme_shorten_length);
							break;
						}
					}
				}
				$theme_name = $this->replaceThemeNameIfSibling($theme_name);
				
			}
			$this->theme_name = $theme_name;
		}
		return $this->theme_name;
	}
	
	protected function replaceThemeNameIfSibling($theme_name) {
		if ( $this->getThemeSiblingDir() ) {
			$sibling_file_name = $this->getThemeSiblingDir().$theme_name.'.php';
			if ( file_exists($sibling_file_name) ) {
				require($sibling_file_name); // $sibling_main_theme should be defined there
				if ( !empty($sibling_main_theme) ) {
					return $sibling_main_theme;
				}
			}
		}
		return $theme_name;
	}
	
}