<?php

namespace liveopencart\lib\v0023;

class simple_db {
	
	use traits\db;
	
	protected $registry;
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($name) {
		return $this->registry->get($name);
	}
	
	protected function getSqlWhereFromArrayOfIds($arr) {
		$where = array();
		foreach ( $arr as $id_key => $id_val ) {
			$sql_where = " `".$id_key."` ";
			if ( is_array($id_val) ) {
				$sql_where.= " IN (".implode(",", array_map('intval', $id_val)).") ";
			} else {
				$sql_where.= " = '".(int)$id_val."' ";
			}
			$where[] = $sql_where;
		}
		return implode(" AND ", $where);
	}
	
	protected function getTableDefaultId($table_name) {
		return $table_name.'_id';
	}
	
	
	
	public function selectRowById($table_name, $id, $sql_order="") {
		return $this->selectRowByIds($table_name, array($table_name.'_id' => $id), $sql_order);
	}
	
	public function selectRowByIds($table_name, $ids, $sql_order="") {
		
		$rows = $this->selectRowsByIds($table_name, $ids, $sql_order);
		if ( $rows ) {
			return $rows[0];
		}
	}
	
	public function getSQLSelectByIds($table_name, $ids, $fields=array(), $sql_order="") {
		
		if ( $fields ) {
			if ( is_array($fields) ) {
				$fields = array_map(function($field){
					return "`".$field."`";
				});
			} else {
				$fields = array( $fields=="*" ? $fields : "`".$fields."`" );
			}
		} else {
			$fields = array("*");
		}
		
		$sql = "SELECT ".implode(", ", $fields)." FROM `".DB_PREFIX."".$table_name."` WHERE ";
		$sql.= $this->getSqlWhereFromArrayOfIds($ids);
		$sql.= $this->getSqlOrder($sql_order);
		return $sql;
	}
	
	public function selectRowsByIds($table_name, $ids, $sql_order="") {
		
		//$sql = "SELECT * FROM `".DB_PREFIX."".$table_name."` WHERE ";
		//$sql.= $this->getSqlWhereFromArrayOfIds($ids);
		//$sql.= $this->getSqlOrder($sql_order);
		
		$sql = $this->getSQLSelectByIds($table_name, $ids, "*", $sql_order);
		
		$query = $this->db->query($sql);
		return $query->rows;
		
	}
	
	protected function getSqlOrder($sql_order) {
		$sql = '';
		if ( $sql_order ) {
			if ( is_array($sql_order) ) {
				$order_parts = array();
				foreach ( $sql_order as $order_item ) {
					if ( count($order_item) == 1 ) {
						$order_parts[] = "`".$order_item[0]."`";
					} elseif ( count($order_item) == 2 ) {
						$order_parts[] = "`".$order_item[0]."` ".$order_item[1];
					} elseif ( count($order_item) == 3 ) {
						$order_parts[] = "`".$order_item[0]."`.`".$order_item[1]."` ".$order_item[2];
					}
				}
				$sql.= " ORDER BY ".implode(", ", $order_parts)." ";
			} else {
				$sql.= " ORDER BY ".$sql_order." ";
			}
		}
		return $sql;
	}
	
	public function deleteRowsById($table_name, $id) {
		$this->deleteRowsByIds($table_name, array($table_name.'_id' => $id));
	}
	public function deleteRowsByIds($table_name, $ids) {
		$sql = "DELETE FROM `".DB_PREFIX."".$table_name."` WHERE ";
		$sql.= $this->getSqlWhereFromArrayOfIds($ids);
		$this->db->query($sql);
	}
	
	public function truncateTable($table_name) {
		$this->db->query("TRUNCATE TABLE `".DB_PREFIX.$table_name."` ");
	}
	
	protected function existIndex($table_name, $column_name) {
		
		$query = $this->db->query(" SHOW INDEX FROM `".DB_PREFIX."".$table_name."` WHERE Column_name = '".$column_name."' ");
		return $query->num_rows;
		
	}
	
	public function addTableIndexIfNotExists($table_name, $column_name) {
		
		if ( !$this->existIndex($table_name, $column_name) ) {
			$this->db->query("CREATE INDEX `".$column_name."` ON `".DB_PREFIX."".$table_name."`(`".$column_name."`) ");
		}
		
	}
	
	public function insertEscaped($table_name, $data) {
		
		$fields = array_map(function($value, $column_name){
			return "`".$column_name."` = '".$this->db->escape($value)."'";
		}, $data, array_keys($data));
		
		$this->db->query("INSERT INTO `".DB_PREFIX."".$table_name."` SET ".implode(", ", $fields)." ");
		
	}
	
}