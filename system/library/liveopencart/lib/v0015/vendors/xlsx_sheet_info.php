<?php

namespace liveopencart\lib\v0015\vendors;

class xlsx_sheet_info {
	
	public $name = '';
	public $data = array();
	
	public function __construct($name, $data) {
		$this->name = $name;
		$this->data = $data;
	}
	
	public function addRow($row=array()) {
		$this->data[] = $row;
	}
	
	public function getNumRows() {
		return count($this->data);
	}
	
	public function getValue($row_index, $column_index) {
		return isset($this->data[$row_index][$column_index]) ? $this->data[$row_index][$column_index] : '';
	}
	
	public function setValue($row_index, $column_index, $value) {
		
		while ( !isset($this->data[$row_index]) ) { // add rows if necessary
			$this->data[] = array();
		}
		
		while ( !isset($this->data[$row_index][$column_index]) ) { // all columns should contain at least empty value
			$this->data[$row_index][] = '';
		}
		
		$this->data[$row_index][$column_index] = $value;
	}
	
	public function setLastRowValue($column_index, $value) {
		if ( count($this->data) == 0 ) {
			$this->addRow();
		}
		$this->setValue(count($this->data)-1, $column_index, $value);
	}
	
}