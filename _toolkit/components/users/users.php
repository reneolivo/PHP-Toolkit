<?php
	require_once(dirname(__FILE__).'/../../templates/user.php');
	
	class users extends userTemplate {
		public function __construct() {
			parent::__construct(__CLASS__, new fieldSet(
			 	new field('id', 'id'),
				new field('name', 'varchar'),
				new field('email', 'email'),
				new field('password', 'password'),
				new field('isActive', 'boolean')
			));
		}
	}
?>