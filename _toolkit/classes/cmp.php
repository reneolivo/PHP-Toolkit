<?php
class cmp {
	private static $cmp = array();
	
	private static function add($object) {
		
		if (!isset(self::$cmp[$object])) {
			if (!class_exists($object)) {
				$file = dirname(__FILE__)."/../components/{$object}/{$object}.php";
				if (is_file($file)) {
					require_once($file);
					self::$cmp[$object] = new $object();
				} else {
					self::createFromTable($object);	
				}				
			} else {
				self::$cmp[$object] = new $object();
			}
		}
	}
	
	public static function __callStatic($object, $args) {	
		self::add($object);
		
		$args = array_pad($args, 3, NULL);
		if (is_array($args[0])) $args[1] = true; else $args[3] = true;
		
		call_user_func_array(array(self::$cmp[$object], 'filter'), $args);
			
		return self::$cmp[$object];
	}
	
	public static function createFromTable($tableName) {		
		$cmp = data::createComponentFromTable($tableName);

		if ($cmp !== false)
			self::$cmp[$tableName] = $cmp;
		else
			return false;
	}
}
?>