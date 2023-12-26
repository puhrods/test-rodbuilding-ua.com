<?php

namespace liveopencart\lib\v0023\interfaces;

interface xlsx_lib {
	
	public function install();
	
	public function getName();
	
	public function getAvailability();
	
	public function getNewSheetInfo($name, $data);
	
	//public function getNewReader();
	//
	//public function getNewWriter();
	
	public function getSheetsInfosFromFile($file_name);
	
	public function getSheetDataFromFile($file_name, $sheet_index);
	
	public function getSheetsDataFromFile($file_name);
	
	public function exportSheetsDataToBrowser($browser_file_name);
	
	public function exportSheetsDataToFile($sheets_data, $file_name);
	
	public function exportSheetsInfosToBrowser($sheets_infos, $browser_file_name);
	
	public function exportSheetsInfosToFile($sheets_data, $file_name);
	
}
	
	