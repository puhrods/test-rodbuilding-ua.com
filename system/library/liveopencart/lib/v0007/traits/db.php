<?php

namespace liveopencart\lib\v0007\traits;

trait db {
 
    public function existTable($table_name) {
		
		$query = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX.$table_name."'");
		return $query->num_rows;
	}
}