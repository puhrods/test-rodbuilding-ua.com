<?php

namespace liveopencart\lib\v0023\vendors;

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
	
	public function getAsAssocArrayTableByHead($head_row_index=0) {
		
		$result = [];
		
		if ( $head_row_index+1 <= count($this->data) ) {
			$head = array_flip( array_filter($this->data[$head_row_index]) );
			for($row_index=$head_row_index+1; $row_index<count($this->data); $row_index++) {
				$row = $this->data[$row_index];
				$result_row = array();
				foreach ( $head as $key => $column ) {
					$result_row[$key] = isset($row[$column]) ? $row[$column] : '';
				}
				$result[] = $result_row;
			}
		}
		
		return $result;
	}
	
}