<?php
class data {
	private static $driver = NULL;
	
	public static $result = NULL;
	
	public static function driver($driverName = 'mysql') {
		$driverName = strtolower($driverName);
		
		$driverFile = dirname(__dir__).'/drivers/'.$driverName.'.php';
		
		if (is_file($driverFile)) {
			require_once($driverFile);
			
			$driverClass = $driverName.'Driver';
			
			
			self::$driver = new $driverClass();
		} else {
			die('Error: No such Driver: '.$driverName);	
		}
	}
	
	public static function debug() {
		return self::$driver->debug();	
	}
	
	public static function connect() {
		$args = func_get_args();
		
		self::driver($args[0]);
		
		return call_user_func_array(array(self::$driver, 'connect'), array_slice($args, 1));
	}
	
	public static function disconnect() {
		return call_user_func_array(array(self::$driver, 'disconnect'), func_get_args());	
	}
	
	public static function escapeString() {
		return call_user_func_array(array(self::$driver, 'escapeString'), func_get_args());	
	}
	
	public static function query() {
		return call_user_func_array(array(self::$driver, 'query'), func_get_args());
	}
	
	public static function select() {
		return call_user_func_array(array(self::$driver, 'select'), func_get_args());
	}
	
	public static function fetch() {
		return call_user_func_array(array(self::$driver, 'fetch'), func_get_args());
	}
	
	public static function forEachRow() {
		return call_user_func_array(array(self::$driver, 'forEachRow'), func_get_args());
	}
	
	public static function countRows() {
		return call_user_func_array(array(self::$driver, 'countRows'), func_get_args());
	}
	public static function countRowsFromResult() {
		return call_user_func_array(array(self::$driver, 'countRowsFromResult'), func_get_args());
	}
	
	public static function insert() {
		return call_user_func_array(array(self::$driver, 'insert'), func_get_args());	
	}
	
	public static function update() {
		return call_user_func_array(array(self::$driver, 'update'), func_get_args());	
	}
	
	public static function delete() {
		return call_user_func_array(array(self::$driver, 'delete'), func_get_args());	
	}
	
	public static function createComponentFromTable() {
		return call_user_func_array(array(self::$driver, 'createComponentFromTable'), func_get_args());
	}
	
	public static function charset() {
		return call_user_func_array(array(self::$driver, 'charset'), func_get_args());
	}
}
?>