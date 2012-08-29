<?php
	error_reporting(E_ALL);
	
	require_once('classes/phptoolkit.php');
	
	phptoolkit::init();
	
	phptoolkit::$http = 'http://minet.com.do/kola/';
	
	data::connect('sqlite', 'test.db');
?>