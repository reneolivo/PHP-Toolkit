<?php
	
	require_once(dirname(__FILE__)."/pdo.php");
	
	class sqlDriverTemplate extends PDODriverTemplate {
		protected function fieldsToString($array) {
			if (is_array($array)) {
				$string = '';
				
				foreach($array as $v) {
					$string .= "\n\t`{$v}`, ";	
				}
				
				$string = rtrim($string, ', ');
				
				return $string;
			} else {
				return $array;	
			}
		}
		
		protected function valuesToString($array) {
			if (is_array($array)) {
				$string = '';
				
				foreach($array as $v) {
					$string .= "\n\t".$this->escapeString($v).", ";	
				}
				
				$string = rtrim($string, ', ');
				return $string;
			} else {
				return $array;	
			}
		}
		
		protected function setString($fields, $values = NULL) {
			if (is_array($fields) and is_array($values)) {
				$string = '';
				
				foreach($fields as $k => $v) {
					$string .= "\n\t`{$fields[$k]}` = ".$this->escapeString($values[$k]).", ";
				}
				
				$string = rtrim($string, ', '); 
				
				return $string;
			} else {
				return $fields;	
			}
		}
		
		protected function filterString($where = NULL, $order = NULL, $limit = NULL) {
			$queryString = ''; 
			
			if ($where !== NULL) 
				$queryString .= " \nWHERE \n\t{$where}";
				
			if ($order !== NULL)
				$queryString .= " \nORDER BY \n\t{$order}";
			
			if ($limit !== NULL)
				$queryString .= " \nLIMIT \n\t{$limit}";
				
			return $queryString."\n";
		}
		
		protected function selectString($fields, $tableName, $where = NULL, $order = NULL, $limit = NULL) {
			$fields = $this->fieldsToString($fields);
			
			$queryString = "SELECT \n\t$fields \nFROM \n\t{$tableName}".$this->filterString($where, $order, $limit);
			
			return $queryString."\n";
		}
		
		protected function countRowsString($tableName, $where = NULL, $order = NULL, $limit = NULL) {
			$queryString = "SELECT count(*) as `count` FROM {$tableName}".$this->filterString($where, $order, $limit);
			
			return $queryString;
		}
		
		protected function insertString($tableName, $fields, $values) {
			$values = $this->valuesToString($values);
			
			$queryString = "INSERT INTO \n\t{$tableName}";
			
			if ($fields !== NULL) {
				$fields = $this->fieldsToString($fields);
				$queryString .= " ({$fields}\n)";	
			}
			
			$queryString .= " \nVALUES ({$values}\n)";
			
			$this->debug($queryString);
			
			
			return $queryString;	
		}
		
		protected function updateString($tableName, $fields, $values = NULL, $where = NULL, $order = NULL, $limit = NULL) {
			$queryString = "UPDATE \n\t{$tableName} \nSET ".$this->setString($fields, $values).$this->filterString($where, $order, $limit);
			
			return $queryString;
		}
		
		protected function deleteString($tableName, $where = NULL, $order = NULL, $limit = NULL) {
			$queryString = "DELETE FROM \n\t{$tableName}".$this->filterString($where, $order, $limit);
			
			return $queryString;
		}
	}
?>