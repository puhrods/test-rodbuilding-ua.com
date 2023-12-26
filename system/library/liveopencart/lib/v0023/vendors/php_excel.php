<?php

namespace liveopencart\lib\v0023\vendors;

class php_excel extends \liveopencart\lib\v0023\abstracts\xlsx_lib implements \liveopencart\lib\v0023\interfaces\xlsx_lib {
	
	use \liveopencart\lib\v0023\traits\vendor_lib;
	
	protected $download_md5_hash = '27e59a937e412a8d04c09912e73baf26';
	protected $download_url = 'https://update.liveopencart.com/dist/PHPExcel-1.8.2.liveopencart.1.tar';
	protected $loader_file_name = 'PHPExcel-1.8.2.liveopencart.1/Classes/PHPExcel.php';
	protected $name = 'PHPExcel';

	public function getAvailability() {
		if ( !class_exists('\PHPExcel_IOFactory') ) {
			$this->loadLib();
		}
		return class_exists('\PHPExcel_IOFactory');
	}
	
	public function getSheetDataFromFile($file_name, $sheet_index=0) {
	
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		
		$this->loadLib();
		//require_once $this->PHPExcelPath();
		
		$cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( 'memoryCacheSize' => '32MB');
		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		
		$excel = \PHPExcel_IOFactory::load($file_name); // PHPExcel
		$sheet = $excel->getSheet(0);
		
		return $sheet->toArray();
	}
	
	public function getSheetsInfosFromFile($file_name) {
		
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		
		$this->loadLib();
		//require_once $this->PHPExcelPath();
		
		$cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( 'memoryCacheSize' => '32MB');
		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		
		$excel = \PHPExcel_IOFactory::load($file_name); // PHPExcel
		
		$sheets_infos = array();
		foreach ($excel->getAllSheets() as $sheet) {
			$sheets_infos[] = $this->getNewSheetInfo($sheet->getTitle(), $sheet->toArray());
		}
		
		return $sheets_infos;
		
	}
	
	public function getSheetsDataFromFile($file_name) {
		
		$sheets_infos = $this->getSheetsInfosFromFile($file_name);
		
		return array_map(function($sheet_info){
			return $sheet_info->data;
		}, $sheets_infos);
		
	}
	
	protected function exportSheetsToBrowser($sheets_data, $browser_file_name='', $sheets_infos=array()) {
		
		$tmp_file_name = DIR_CACHE.'liveopencart-poip-export-'.round(microtime(true)*1000).'-'.rand(1000000, 9999999);
		
		$this->exportSheetsToFile($sheets_data, $tmp_file_name, $sheets_infos);
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $browser_file_name);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($tmp_file_name));
		// read file and send to user
		readfile($tmp_file_name);
		
		@unlink($tmp_file_name);
		
	}

	public function exportSheetsDataToBrowser($sheets_data, $browser_file_name='') {
		
		$this->exportSheetsToBrowser($sheets_data, $browser_file_name);
		
	}
	
	protected function exportSheetsToFile($sheets_data, $file_name, $sheets_infos=array()) {
		
		$this->loadLib();
		
		\PHPExcel_Shared_File::setUseUploadTempDirectory(true);
		
		$cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
		$cacheSettings = array( 'memoryCacheSize' => '32MB');
		if (!\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings)) {
			$this->log->write("PHPExcel cache error");
		}
		
		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		
		foreach ($sheets_data as $sheet_index => $sheet_data) {
			
			if ( $sheet_index == 0 ) { // use default sheet
				$objPHPExcel->setActiveSheetIndex(0);
				$current_sheet = $objPHPExcel->getActiveSheet();
			} else {
				$current_sheet = $objPHPExcel->createSheet($sheet_index);
			}
			$current_sheet->getStyle( $current_sheet->calculateWorksheetDimension() )->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
			$current_sheet->fromArray($sheet_data,null,'A1');
			$current_sheet->getStyle( $current_sheet->calculateWorksheetDimension() )->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
			
			if ( $sheets_infos && isset($sheets_infos[$sheet_index]) ) {
				$current_sheet->setTitle( $this->prepareSheetName($sheets_infos[$sheet_index]->name) );
			}
		}
		
		//$objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
		$objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
		
		$objWriter->save($file_name);
		
	}
	
	public function exportSheetsDataToFile($sheets_data, $file_name) {
		
		$this->exportSheetsToFile($sheets_data, $file_name);
		
	}
	
	public function exportSheetsInfosToFile($sheets_infos, $file_name) {
		
		$sheets_data = array_map(function($sheet_info){
			return $sheet_info->data;
		}, $sheets_infos);
		
		$this->exportSheetsToFile($sheets_data, $file_name, $sheets_infos);

	}
	
	public function exportSheetsInfosToBrowser($sheets_infos, $browser_file_name) {
		
		$sheets_data = array_map(function($sheet_info){
			return $sheet_info->data;
		}, $sheets_infos);
		$this->exportSheetsToBrowser($sheets_data, $browser_file_name, $sheets_infos);
		
	}
	
}