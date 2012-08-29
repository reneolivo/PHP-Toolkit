<?php
class data {
	static private $database;
 
	static private $conection;
	
	static private $debug = NULL;
 
	static function connect($host = 'localhost', $username = 'root', $password = '', $database = NULL) {
		self::$database = $database;
 
		self::$conection = mysql_pconnect($host, $username, $password);
 
		if ($database != NULL) {
			mysql_select_db($database);
		}
	}
	
	static function debug() {
		echo "<hr /><h3>QUERY String:</h3> ".self::$debug."<hr />";
	}
	
	static function query($query_str) {
		self::$debug = $query_str;
		
		$query = mysql_query($query_str);
		
		self::$debug = $query_str;
		
		return $query;
	}
 
	static function select($select, $from, $where = NULL, $order = NULL, $limit = NULL) {
		$query_str = "
			SELECT
				{$select}
			FROM
				".self::$database.".{$from}
		";
 
		if ($where != NULL) {
			$query_str .= "
			WHERE
				{$where}
			";
		}
 
		if ($order != NULL) {
			$query_str .= "
			ORDER BY
				{$order}
			";
		}
 
		if ($limit != NULL) {
			$query_str .= "
			LIMIT
				{$limit}
			";
		}
 
 		self::$debug = $query_str;
		
 
		$query = mysql_query($query_str); //or die(mysql_error(). "<br />{$query_str}");
 
		return $query;
	}
	
	static function fetch($select, $table, $where = NULL, $order = NULL) {
		$query = self::select($select, $table, $where, $order, 1);
		
		return mysql_fetch_object($query);
	}
 
	static function insert($table, $fields, $values) {
		$query_str = "
			INSERT INTO
				{$table}
				({$fields})
			VALUES
				({$values})
		";
 
		mysql_query($query_str, self::$conection) or die(mysql_error(). "<br /> {$query_str}");
		
		self::$debug = $query_str;
		
		return mysql_insert_id(self::$conection);
	}
 
	static function update($table, $set, $id) {
		$query_str = "
			UPDATE
				{$table}
			SET
				{$set}
			WHERE
				id = {$id}
		";
	
		mysql_query($query_str, self::$conection); // or die(mysql_error(). "<br /> {$query_str}");
		
		self::$debug = $query_str;
		
		return $id;
	}
 
	static function delete($table, $id) {
		$query_str = "
			DELETE FROM
				{$table}
			WHERE
				id = {$id}
		";
 
		mysql_query($query_str, self::$conection); // or die(mysql_error(). "<br /> {$query_str}");
		
		self::$debug = $query_str;
		
		return $id;
	}
 
	static function delete_conditional($table, $where = NULL, $oder = NULL, $limit = NULL) {
		$query_str = "
			DELETE FROM
				{$table}
		";
 
		if ($where != NULL)
		$query_str .= "
			WHERE
				{$where}
		";
 
		if ($oder != NULL)
		$query_str .= "
			ORDER BY
				{$oder}
		";
 
		if ($limit != NULL)
		$query_str .= "
			LIMIT
				{$limit}
		";
 
		mysql_query($query_str, self::$conection); // or die(mysql_error(). "<br /> {$query_str}");
		
		self::$debug = $query_str;
		
		return mysql_affected_rows(self::$conection);
	}
	
	static public function removeMagicQuotes($value) {
		if (get_magic_quotes_gpc()) {
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					$value[$k] = stripslashes($v);
				}
			} else {
				$value = stripslashes($value);
			}
		}
		
		return $value;
	}
	
	static public function escape($value) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = mysql_real_escape_string($v);
			}
		} else {
			$value = mysql_real_escape_string($value);
		}
		
		return $value;
	}
	
 
	static function count_rows($table, $where = NULL) {
		$query_str = "
			SELECT
				COUNT(*) as count
			FROM
				".self::$database.".{$table}
		";
 
		if ($where != NULL) {
			$query_str .= "
				WHERE
					{$where}
			";
		}
 
		$query = mysql_query($query_str); // or die(mysql_error(). "<br /> {$query_str}");
		
		self::$debug = $query_str;
		
		if (is_resource($query)) {
			$query = mysql_fetch_row($query);			
			return $query[0];
		} else {
			echo '<h3>Error Query:</h3>'.$query_str.'<hr />';
			echo mysql_error();
			echo '<hr />';
			die();
		}
	}
}
?>