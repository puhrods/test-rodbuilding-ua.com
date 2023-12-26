<?php

require_once(DIR_SYSTEM . 'library/client_translate_expert.php');
require_once(DIR_SYSTEM . 'library/client_localization_translate_expert.php');

class ModelExtensionModuleClientTranslateExpert extends Model
{
	public function translate($text, $source, $target, $format)
	{
		try {
			$res = $this->getLibrary()->translate($text, $source, $target, $format);
			$this->getLibrary()->gebugLog("function translate \n" . var_export($res, true));
			return $res;
		} catch (\Exception $e) {
			$this->getLibrary()->gebugLog("EXCEPTION!!!!!! \n" . "function translate \n" . $e->getMessage());
			throw $e;
		}		
	}
	
	public function translateTable($languageIdFrom, $mode, $tableName, $textColumnNameParam, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status)
	{		
		try {
			$res = $this->getLibrary()->translateTable($languageIdFrom, $mode, $tableName, $textColumnNameParam, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status);
			$this->getLibrary()->gebugLog("function translateTable \n" . var_export($res, true));
			return $res;
		} catch (\Exception $e) {
			$this->getLibrary()->gebugLog("EXCEPTION!!!!!! \n" . "function translateTable \n" . $e->getMessage());
			throw $e;
		}		
	}
	
	public function analizeTableDetail($languageIdFrom, $mode, $tableName, $textColumnNameParam, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status)
	{		
		try {
			$res = $this->getLibrary()->analizeTableDetail($languageIdFrom, $mode, $tableName, $textColumnNameParam, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status);
			$this->getLibrary()->gebugLog("function analizeTableDetail \n" . var_export($res, true));
			return $res;
		} catch (\Exception $e) {
				$this->getLibrary()->gebugLog("EXCEPTION!!!!!! \n" . "function analizeTableDetail \n" . $e->getMessage());
				throw $e;
		}		
	}

	public function analize($languageIdFrom, $languageIdTo, $mode = null, $filterTable = '', $product_status = false, $product_quantity = false, $product_category_id = null, $stock_status = null, $continueAfterTable = null, $continueAfterColumn = null, $continueOffset = 0)
	{
		try {
			$res = $this->getLibrary()->analize($languageIdFrom, $languageIdTo, $mode, $filterTable, $product_status, $product_quantity, $product_category_id, $stock_status, $continueAfterTable, $continueAfterColumn, $continueOffset);
			$this->getLibrary()->gebugLog("function analize \n" . var_export($res, true));
			return $res;
		} catch (\Exception $e) {
				$this->getLibrary()->gebugLog("EXCEPTION!!!!!! \n" . "function analize \n" . $e->getMessage());
				throw $e;
		}		
	}

	public function analizeLocalization($languageIdFrom, $languageIdTo, $nextFileIndex = 0)
	{
		try {
			$res = $this->getLocaliationLibrary()->analizeLocalization($languageIdFrom, $languageIdTo, $nextFileIndex);
			$this->getLibrary()->gebugLog("function analizeLocaliation \n" . var_export($res, true));
			return $res;
		} catch (\Exception $e) {
				$this->getLibrary()->gebugLog("EXCEPTION!!!!!! \n" . "function analizeLocaliation \n" . $e->getMessage());
				throw $e;
		}		
	}
	
	public function saveLocalizationFile($fileContent, $langId, $path)
	{
		try {
			$res = $this->getLocaliationLibrary()->saveLocalizationFile($fileContent, $langId, $path);
			$this->getLibrary()->gebugLog("function saveLocalizationFile \n" . var_export($res, true));
			return $res;
		} catch (\Exception $e) {
				$this->getLibrary()->gebugLog("EXCEPTION!!!!!! \n" . "function saveLocalizationFile \n" . $e->getMessage());
				throw $e;
		}
	}
	
	public function validateTranslateSiteInfo ()
	{
		try {
			$res = $this->getLibrary()->validateTranslateSiteInfo ();
			$this->getLibrary()->gebugLog("function validateTranslateSiteInfo  \n" . var_export($res, true));
			return $res;
		} catch (\Exception $e) {
				$this->getLibrary()->gebugLog("EXCEPTION!!!!!! \n" . "function validateTranslateSiteInfo  \n" . $e->getMessage());
				throw $e;
		}		
	}
	
	public function getTables()
	{
		try {
			$res = $this->getLibrary()->getTables();
			$this->getLibrary()->gebugLog("function getTables \n" . var_export($res, true));
			return $res;
		} catch (\Exception $e) {
				$this->getLibrary()->gebugLog("EXCEPTION!!!!!! \n" . "function getTables \n" . $e->getMessage());
				throw $e;
		}		
	}
	
	public function install()
	{
		try {
			$res = $this->getLibrary()->install();
			$this->getLibrary()->gebugLog("function install \n" . var_export($res, true));
			return $res;
		} catch (\Exception $e) {
				$this->getLibrary()->gebugLog("EXCEPTION!!!!!! \n" . "function install \n" . $e->getMessage());
				throw $e;
		}		
	}
	
	public function uninstall()
	{
		try {
			$res = $this->getLibrary()->uninstall();
			$this->getLibrary()->gebugLog("function uninstall \n" . var_export($res, true));
			return $res;
		} catch (\Exception $e) {
				$this->getLibrary()->gebugLog("EXCEPTION!!!!!! \n" . "function uninstall \n" . $e->getMessage());
				throw $e;
		}		
	}
	
	private $_library = null;
	
	private function getLibrary()
	{
		if (is_null($this->_library))
			$this->_library = new LibraryClientTranslateExpert($this, $this->config->get('module_client_translate_expert_debug_status'));
		return $this->_library;
	}
	
	private $_localiationLibrary = null;
	
	private function getLocaliationLibrary()
	{
		if (is_null($this->_localiationLibrary))
			$this->_localiationLibrary = new LocalizationLibraryClientTranslateExpert($this, $this->config->get('module_client_translate_expert_debug_status'));
		return $this->_localiationLibrary;
	}
}

