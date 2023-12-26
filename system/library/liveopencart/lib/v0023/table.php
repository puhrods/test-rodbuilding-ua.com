<?php
namespace liveopencart\lib\v0023;

class Table extends Library {
	
	// [[name=>'abc', type=>' int(11) NOT NULL AUTO INCREMENT ', key|primary=>true, cast=>'float|int|string'], [...], ...]
	protected $name = '';
	protected $engine = 'MyISAM';
	protected $charset = 'utf8';
	protected $collate = 'utf8_general_ci';
	protected $columns = [];
	protected $columns_assoc = [];
	protected $simple_db;
	
	public function __construct($registry) {
		call_user_func_array( array('parent', '__construct') , func_get_args());
		
		$this->init();
	}
	
	protected function getTableName() {
		return DB_PREFIX.$this->name;
	}
	
	protected function init() {
		$this->simple_db = new simple_db($this->registry);
		foreach ( $this->columns as $column ) {
			$this->columns_assoc[$column['name']] = $column;
		}
		$this->checkColumns();
	}
	
	protected function checkColumns() {
		
		if ( $this->exists() ) {
			foreach ( $this->columns as $column ) {
				$this->simple_db->addTableColumnIfNotExists($this->name, $column['name'], $column['type']);
				if ( !empty($column['key']) ) {
					$this->simple_db->addTableIndexIfNotExists($this->name, $column['name']);
				}
			}
		}
	}
	
	public function exists() {
		return $this->simple_db->existTable($this->name);
	}
	
	//protected function addColumns($columns) {
	//	foreach ( $columns as $column ) {
	//		$this->addColumn($column);
	//	}
	//}
	//
	//protected function addColumn($column) {
	//	$this->columns[] = $column;
	//	$this->columns_assoc[$column['name']] = $column;
	//}
	
	protected function getPrimaryKeyColumn() {
		$column_name = $this->name.'_id';
		$column = $this->getColumnByName($column_name);
		if ( !$column ) {
			throw new \Exception('Error: Table "'.$this->name.'" does not have default ID !');
		}
		return $column_name;
	}
	
	protected function getColumnByName($column_name) {
		if ( isset($this->columns_assoc[$column_name]) ) {
			return $this->columns_assoc[$column_name];	
		} else {
			throw new \Exception('Error: Column "'.$column_name.'" is not defined for the table "'.$this->name.'" !');
		}
	}
	
	// $sql_sets for specified set commands, like "`date_added` = NOW()"
	protected function getSQLSet($data, $sql_sets=[]) {
		$sqls = [];
		foreach ( $this->columns as $column ) {
			$column_name = $column['name'];
			if ( isset($data[$column_name]) ) {
				$value = isset($data[$column_name]) ? $data[$column_name] : '';
				$sql_value = $this->castColumnValue($column_name, $value, $column);
				
				$sqls[] = " `".$column_name."` = '".$this->db->escape($sql_value)."' ";
			}
		}
		$sqls = array_merge($sqls, $sql_sets);
		
		return implode(",", $sqls);
	}
	
	protected function castColumnValue($column_name, $value, $column=false) {
		
		if ( !$column ) {
			$column = $this->getColumnByName($column_name);
		}
		
		if ( $column['cast'] == 'float' ) {
			$sql_value = "".(float)$value;
		} elseif ( $column['cast'] == 'int' ) {
			$sql_value = "".(int)$value;
		} else {
			$sql_value = (string)$value;
		}
		return $sql_value;
	}
	
	// 	$where can be:
	// 		int(primary id)
	// 		string (sql)
	// 		array of
	// 			'column_name'=>'column_value' ( condition '=' )
	//			strings
	//			int(ids)
	protected function getSQLWhere($where, $operator="AND") {
		$sql_where = "";
		if ( $where ) {
			if ( is_int($where) || (is_string($where) && is_numeric($where) && (int)$where == trim($where)) ) {
				return " `".$this->getPrimaryKeyColumn()."` = ".(int)$where." ";
			} elseif ( is_string($where) ) {
				return $where;
			
			} elseif( is_array($where) ) {
				
				$where_keys = array_keys($where);
				if ( is_string($where_keys[0]) ) { // key => val 
					
					$sqls = [];
					foreach ( $where as $column_name => $column_value ) {
						$sql_value = $this->castColumnValue($column_name, $column_value);
						$sqls[] = " `".$column_name."` = '".$this->db->escape($sql_value)."' ";
					}
					$sql_where = implode(" ".$operator." ", $sqls);
					
				} elseif ( is_int($where_keys[0]) ) {
					$where = array_values($where); // to make sure indexes are 0-based
					if ( count($where) == 2 AND is_string($where[0]) AND in_array(strtoupper($where[0]), ['OR', 'AND', 'NOT']) AND is_array($where[1]) ) {
						$new_operator = strtoupper($where[0]);
						if ( $new_operator == 'NOT') {
							$sql_where = " NOT (".$this->getSQLWhere($where[1]).")";
						} elseif ($new_operator == 'AND' || $new_operator == 'OR') {
							$sql_where = $this->getSQLWhere($where[1], $new_operator);
						//} else {
						//	throw new \Exception('Error: Unknown operator "'.$where[0].'" for table "'.$this->name.'" !');
						}
					} elseif (count($where) == 3 && is_string($where[0]) && is_string($where[1])) { // [column_name, condition, value/values]
						$column_name = $where[0];
						$column_condition = strtoupper($where[1]);
						$column_value = $where[2];
						
						$sql_where = " `".$column_name."` ".$column_condition." ";
						if ( $column_condition == 'IN' ) {
							if ( is_array($column_value) ) { 
								$sql_values = array_map(function($val){
									return "'".$this->db->escape($this->castColumnValue($column_name, $val))."'";
								}, $column_value);
								$sql_where.= " (".implode(",", $sql_values).") ";
							} else {
								$sql_where.= " (".$column_value.") "; // sql subquery
							}
						} else {
							$sql_value = $this->castColumnValue($column_name, $column_value);
							$sql_where.= " '".$this->db->escape($sql_value)."' ";
						}
					} else { // array of where conditions 
						
						$sqls = [];
						foreach ( $where as $where_item ) {
							$sqls[] = $this->getSQLWhere($where_item);
						}
						$sql_where = implode(" ".$operator." ", $sqls);
						
					}
				}
				
			}
		}
		return $sql_where ? " (".$sql_where.") " : "";
	}
	
	// 	$order:
	//		string
	//		array [column_name => ASC|DESC]
	//		array of arrays [column_name, ASC|DESC]
	//		array of strings
	protected function getSQLOrder($order) {
		if ( is_string($order) ) {
			return $order;
		} elseif ( is_array($order) ) {
			if ( $order ) {
				$sqls = [];
				if ( is_int(array_keys($order)[0]) ) {
					foreach ( $order as $order_rule ) {
						if ( is_string($order_rule) ) {
							$sqls[] = $order_rule;
						} else {
							$sqls[] = "`".$order_rule[0]."` ".$order_rule['1']."";
						}
					}
				} else {
					foreach ( $order as $column_name => $order_direction ) {
						$sqls[] = "`".$column_name."` ".$order_direction."";
					}
				}
				return $sqls ? " ".implode(", ", $sqls)." " : "";
			}
		} else {
			throw new \Exception('Error: Unsupported order parameter type "'.gettype($order).'" for the table "'.$this->name.'" !');
		}
	}
	
	public function updateByIdOrInsert($data) {
		$key_column_name = $this->getPrimaryKeyColumn();
		if ( !empty($data[$key_column_name]) && $this->selectOne((int)$data[$key_column_name]) ) {
			return $this->updateById((int)$data[$key_column_name], $data);
		} else {
			return $this->insert($data);
		}
	}
	
	public function insert($data, $sql_sets=[]) {
		$this->db->query("INSERT INTO `".$this->getTableName()."` SET ".$this->getSQLSet($data, $sql_sets)." ");
		return $this->db->getLastId();
	}
	
	public function update($where, $data, $sql_sets=[]) {
		$sql = "UPDATE `".$this->getTableName()."` SET ".$this->getSQLSet($data, $sql_sets)." ";
		if ( $where ) {
			$sql_where = $this->getSQLWhere($where);
			if ( $sql_where ) {
				$sql.= " WHERE ".$sql_where;
			}
		}
		$this->db->query($sql);
	}
	
	public function updateById($id, $data) {
		$this->update((int)$id, $data);
		return $id;
	}
	
	public function updateAll($where, $data) {
		foreach ( $this->columns as $column ) {
			$column_name = $column['name'];
			if ( !isset($data[$column_name]) ) {
				$data[$column_name] = '';
			}
		}
		return $this->update($where, $data);
	}
	
	// $where can be of different types, see getSQLWhere
	// $order_rules	= []|str
	// $limit		= int|str
	public function select($where="", $order="", $limit="") {
		$sql = " SELECT * FROM `".$this->getTableName()."` ";
		if ( $where ) {
			$sql_where = $this->getSQLWhere($where);
			if ( $sql_where ) {
				$sql.= " WHERE ".$sql_where." ";
			}
		}
		if ($order) {
			$sql_order = $this->getSQLOrder($order);
			if ( $sql_order ) {
				$sql.= " ORDER BY ".$sql_order." ";
			}
		}
		if ( $limit ) {
			$sql.= " LIMIT ".$limit;
		}
		
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function selectOne($where="", $order="") {
		$rows = $this->select($where, $order, 1);
		return $rows ? $rows[0] : [];
	}
	
	public function delete($where="") {
		$sql = " DELETE FROM `".$this->getTableName()."` ";
		if ( $where ) {
			$sql_where = $this->getSQLWhere($where);
			if ( $sql_where ) {
				$sql.= " WHERE ".$sql_where." ";
			}
		}
		
		$query = $this->db->query($sql);
	}
	
	public function create() {
		
		$sqls = array();
		foreach ( $this->columns as $column ) {
			$sqls[] = " `".$column['name']."` ".$column['type']." ";
		}
		foreach ( $this->columns as $column ) {
			if ( !empty($column['key']) ) {
				$sqls[] = " KEY (`".$column['name']."`) ";
			} elseif ( !empty($column['primary']) ) {
				$sqls[] = " PRIMARY KEY (`".$column['name']."`) ";
			}
		}
		$sql_columns = implode(',', $sqls);
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".$this->getTableName()."` (
					".$sql_columns."
				) ENGINE=".$this->engine." DEFAULT CHARSET=".$this->charset." COLLATE=".$this->collate."				 
		");
	}
	
	public function drop() {
		$this->db->query("DROP TABLE IF EXISTS `".$this->getTableName()."` ");
	}
	
	
}