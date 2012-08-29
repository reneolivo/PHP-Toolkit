<?php
class phptoolkit {
	private static $class_list = 'cmp_interface, cmp, data, fieldSet, Field, form, html, email, folder, file, FileInterface, image, ImageInterface, calendar, log, record';
	
	public static $document;
	public static $http;
	public static $classes;
	private static $javascripts = array();
	private static $CSS = array();
	
	public static function init($document = NULL, $http = '') {
		if (!isset($_SESSION)) session_start();
		
		self::$document = $_SERVER['DOCUMENT_ROOT'].'/';
		
		if ($document !== NULL) self::$document .= rtrim($document, '/').'/';
		
		self::$http = rtrim($http, '/').'/';
		
		self::$classes = explode(', ', self::$class_list);
		
		/*foreach ($classes as $c) {
			self::getClass(trim($c));
		}*/
	}
	
	public static function getClass($class_name) {
		require_once(dirname(__FILE__).'/'.$class_name.'.php');
	}
	
	public static function location($location) {
		die(header("location: {$location}"));
	}
	
	public static function loadJavascript($fileName) {
		if (!in_array($fileName, self::$javascripts)) {
			self::$javascripts[] = $fileName;
			return '<script type="text/javascript" src="_toolkit/js/'.$fileName.'"></script>'."\n";
		} else {
			return NULL;	
		}
	}
	
	public static function loadCSS($fileName) {
		if (!in_array($fileName, self::$CSS)) {
			self::$CSS[] = $fileName;
			return '<link rel="stylesheet" href="_toolkit/css/'.$fileName.'" />'."\n";
		} else {
			return NULL;	
		}
	}
}

function __autoload($className) {
		//echo 'loading: '.__DIR__.'\\'.$className. '.php<br />';
	if (in_array($className, phptoolkit::$classes)) {
    	require_once __DIR__.'/'.$className. '.php';
	}
}

?>