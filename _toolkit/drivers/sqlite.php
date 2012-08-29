<?php
	require_once(dirname(__DIR__)."/drivers/templates/sql.php");
	
	class sqliteDriver extends sqlDriverTemplate {
		public $database;
		public $tableInfo = array();
		
		public function connect($database) {
			
			$this->database = $database;
			
			$dsn = "sqlite:{$database}";
			
			parent::connect($dsn);
			
			$return = true;
			
			return $return;
		}
		
		protected function tableInfo($tableName, $buffered = false) {
			if (($buffered !== true and array_key_exists($tableName, $this->tableInfo)) or !array_key_exists($tableName, $this->tableInfo)) {
				$result = data::query("PRAGMA table_info('".$tableName."')");
				
				$sqliteDriver = $this;
				data::forEachRow($result, function($r) use ($sqliteDriver, $tableName) {
					$sqliteDriver->tableInfo[$tableName][$r->name] = $r;
				});
			}
		}
		
		public function getTypeFromMeta($meta, $tableName, $columnName) {
			$this->tableInfo($tableName, true);
			
			if ($this->tableInfo[$tableName][$columnName]->pk) {
				return 'id';
			} else {
				$match = array();
				
				preg_match('/^([A-z]+)/', $meta['sqlite:decl_type'], $match);
				
				return strtolower($match[0]);
			}
		}
	}
?>