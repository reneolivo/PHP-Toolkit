<?php
	class PDODriverTemplate {
		protected $debugString;
		public $pdo;
		public $result;
		
		public function __construct() {
			//$this->pdo = new PDO('');	
		}
		
		public function debug($debugString = NULL) {
			if ($debugString === NULL) {
				return $this->debugString;
			} else {
				$this->debugString = $debugString;
			}
		}
		
		public function connect($dsn, $username = NULL, $password = NULL) {
			try {
				$this->pdo = new PDO($dsn, $username, $password);
			} catch (PDOException $e) {
				echo $e->getMessage();	
			}
		}
		
		public function disconnect() {
			$this->hostname = NULL;
			$this->username = NULL;
			$this->password = NULL;
			$this->database = NULL;
			
			$this->pdo = NULL;
		}
		
		public function escapeString($string) {
			if (get_magic_quotes_gpc()) $string = stripslashes($string);
			
			return $this->pdo->quote($string);
		}
		
		public function query($queryString) {
			$this->debug($queryString);
			
			$result = $this->pdo->query($queryString) or die(var_export($this->pdo->errorInfo()));
			
			return $result;
		}
		
		public function select($fields, $tableName, $where = NULL, $order = NULL, $limit = NULL) {
			$queryString = $this->selectString($fields, $tableName, $where, $order, $limit);
			
			return $this->query($queryString);
		}
		
		public function fetch(PDOStatement $result) {
			return $result->fetchObject();
		}
		
		public function countRows($tableName, $where = NULL, $order = NULL, $limit = NULL) {
			$queryString = $this->countRowsString($tableName, $where, $order, $limit);
			
			return $this->fetch($this->query($queryString))->count;
		}
		
		public function countRowsFromResult(PDOStatement $result) {
			return $result->rowCount();
		}
		
		public function forEachRow(PDOStatement $result, $function) {
			while ($row = $result->fetchObject()) {
				$function($row);	
			}
		}
		
		public function insert($tableName, $fields, $values) {
			$queryString = $this->insertString($tableName, $fields, $values);
			$this->query($queryString);
			return $this->pdo->lastInsertId();
		}
		
		public function update($tableName, $fields, $values = NULL, $where = NULL, $oder = NULL, $limit = NULL) {
			$queryString = $this->updateString($tableName, $fields, $values, $where, $oder, $limit);
			
			return $this->query($queryString);
		}
		
		public function delete($tableName, $where = NULL, $order = NULL, $limit = NULL) {
			$queryString = $this->deleteString($tableName, $where, $order, $limit);
			
			return $this->query($queryString);
		}
		
		public function createComponentFromTable($tableName) {
			$return = NULL;
			
			$count = data::select('COUNT(*) AS TOTAL', $tableName, NULL, NULL, 1) or ($return = false);
			$count = $count->fetch();
			if ($count['TOTAL'] == 0)
				data::insert($tableName, array('id'), array(1)); //TODO: Fix ID for primary key.	
			
			$result = data::select('*', $tableName, NULL, NULL, 1) or ($return = false);
			
			if ($return === false) return false;
		
			$total = $result->columnCount();
			
			$fields = array();
			 
			for ($x = 0; $x < $total; $x++) {
				
				$meta = $result->getColumnMeta($x);
				
				//var_export($meta); echo "------------------<br />\n";
				
				$type = (in_array('primary_key', $meta['flags'])) ? 'id' : $this->getTypeFromMeta($meta, $tableName, $meta['name']);
				
				array_push($fields,
					new Field(
						$meta['name'],
						$type
					)
				);
			}
			
			if ($count['TOTAL'] == 0)
				data::delete($tableName);
			
			$newComponent = new cmp_interface($tableName, new fieldSet($fields));
			$newComponent->name = $tableName;
			
			return $newComponent;
		}
		
		protected function getTypeFromMeta($meta) {
			return (isset($meta['native_type'])) ? strtolower($meta['native_type']) : 'varchar';	
		}
	}
?>