<?php
	require_once(dirname(__DIR__)."/drivers/templates/sql.php");
	
	class mysqlDriver extends sqlDriverTemplate  {
		protected $hostname;
		protected $username;
		protected $password;
		protected $database;
		protected $charset;
		
		public function connect($hostname, $database, $username, $password) {
			$this->hostname = $hostname;
			$this->username = $username;
			$this->password = $password;
			$this->database = $database;
			
			$dsn = "mysql:host={$hostname};dbname={$database}";
			
			parent::connect($dsn, $username, $password);
			
			$return = true;
			
			return $return;
		}
		
		public function charset($charset = NULL) {
			if ($charset === NULL) {
				return $this->charset;	
			} else {
				$this->charset = $charset;
				$this->pdo->exec("SET CHARACTER SET ".$this->escapeString($charset));
				return true;
			}
		}
	}
?>