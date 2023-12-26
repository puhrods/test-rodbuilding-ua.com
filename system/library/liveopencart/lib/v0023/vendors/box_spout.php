<?php

namespace liveopencart\lib\v0023\vendors;

class box_spout extends \liveopencart\lib\v0023\abstracts\xlsx_lib implements \liveopencart\lib\v0023\interfaces\xlsx_lib {
	
	protected $download_md5_hash = '4a559c056ff96360c7c25300bed6b8a0';
	protected $download_url = 'https://update.liveopencart.com/dist/spout-3.1.0.tar';
	protected $loader_file_name = 'spout-3.1.0/src/Spout/Autoloader/autoload.php';
	protected $name = 'Box/Spout';
	
	
	public function getPossibility() {
		return version_compare(phpversion(), '7.1.0', '>=');
	}
	
	public function getAvailability() {
		if ( $this->getPossibility() ) {
			if ( !class_exists('\Box\Spout\Reader\Common\Creator\ReaderEntityFactory') ) {
				$this->loadLib();
			}
			return class_exists('\Box\Spout\Reader\Common\Creator\ReaderEntityFactory');
		}
	}
	
	public function getSheetDataFromFile($file_name, $sheet_index=0) {
	
		$this->loadLib();
	
		$reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createXLSXReader();
		$reader->open($file_name);
		
		$data = array();
		
		foreach ($reader->getSheetIterator() as $sheet) {
			if ($sheet->getIndex() === $sheet_index) { // index is 0-based
				foreach ($sheet->getRowIterator() as $row) {
					$data[] = $row->toArray();
				}
				break;
			}
		}
		
		return $data;
	}
	
	public function getSheetsInfosFromFile($file_name) {
		
		$this->loadLib();
		
		$reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createXLSXReader();
		$reader->open($file_name);
		
		$sheets_infos = array();
		
		foreach ($reader->getSheetIterator() as $sheet) {
			$data = array();
			foreach ($sheet->getRowIterator() as $row) {
				$data[] = $row->toArray();
			}
			$sheets_infos[] = $this->getNewSheetInfo($sheet->getName(), $data);
		}
		
		return $sheets_infos;
		
	}
	
	public function getSheetsDataFromFile($file_name) {
		
		$sheets_infos = $this->getSheetsFromFile($file_name);
		
		return array_map(function($sheet_info){
			return $sheet_info->data;
		}, $sheets_infos);
		
	}
	
	protected function exportSheetsToWriter($writer, $sheets_data, $sheets_infos=array()) {
		foreach ( $sheets_data as $sheet_index=>$sheet_data ) {
			
			$sheet = $writer->getCurrentSheet();
			
			if ( $sheets_infos && isset($sheets_infos[$sheet_index])  ) {
				$sheet->setName( $this->prepareSheetName($sheets_infos[$sheet_index]->name) );
			}
			
			foreach ( $sheet_data as $row_data ) {
				$rowFromValues = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray($row_data);
				$writer->addRow($rowFromValues);
			}
			if ( $sheet_index+1 != count($sheets_data) ) {
				$writer->addNewSheetAndMakeItCurrent();
			}
		}
	}
	
	protected function exportSheets($to_browser, $sheets_data, $file_name, $sheets_infos=array()) {
		$this->loadLib();
		
		$writer = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
		
		$column_cnt = 0;
		
		if ( $to_browser ) {
			$writer->openToBrowser($file_name);
		} else {
			$writer->openToFile($file_name);
		}
		
		$this->exportSheetsToWriter($writer, $sheets_data, $sheets_infos);
		
		$writer->close();
	}
	
	public function exportSheetsDataToBrowser($sheets_data, $export_file_name='') {
		
		$this->exportSheets(true, $sheets_data, $export_file_name);
		
	}
	
	public function exportSheetsDataToFile($sheets_infos, $file_name) {
		
		$this->exportSheets(true, $sheets_data, $export_file_name);
		
	}
	
	public function exportSheetsInfosToBrowser($sheets_infos, $browser_file_name) {
		
		$sheets_data = array_map(function($sheet_info){
			return $sheet_info->data;
		}, $sheets_infos);
		
		$this->exportSheets(true, $sheets_data, $browser_file_name, $sheets_infos);
		
	}
	
	public function exportSheetsInfosToFile($sheets_data, $file_name) {
		
		$sheets_data = array_map(function($sheet_info){
			return $sheet_info->data;
		}, $sheets_infos);
		
		$this->exportSheets(false, $sheets_data, $file_name, $sheets_infos);
		
	}
	
}