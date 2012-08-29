<?php
class log {
	static private $uid;
	static private $logid;

	static private $session_status = NULL;
	
	static private $types = array();
	
	static public function start($uid = 0) {
		
		if (!isset($_SESSION)) session_start();
		
		
		if (isset($_SESSION['tk_log_id'], $_SESSION['tk_log_user_id'])) {
			
		
			if ($_SESSION['tk_log_user_id'] == $uid) {
				self::$logid	= $_SESSION['tk_log_id'];
				self::$uid		= $_SESSION['tk_log_user_id'];
			
				//self::store('refreshed');
				
				return 'refreshed';
			} else {
				self::user_login($uid);
				
				return 'new';				
			}
		} else if(isset($_COOKIE['tk_log_user_id'])) {
			if ($_COOKIE['tk_log_user_id'] == $uid) {
				self::user_login($uid);
				//self::store('returning');
				
				return 'returning';	
			} else {
				self::user_login($uid);
				
				return 'new';
			}
		} else {
			self::user_login($uid);
			
			return 'new';	
		}
	}
	
	static private function user_login($uid) {
		$logid = data::insert(
			'logs_usuarios', 
			'identificador, ip, inicio_session, ultima_session',
			'"'.$uid.'", "'.$_SERVER['REMOTE_ADDR'].'", "'.calendar::now().'", "'.calendar::now().'"'
		);
		
		self::$logid 				= $logid;
		self::$uid 					= $uid;
		
		//session_start();
		
		$_SESSION['tk_log_id'] 		= $logid;
		$_SESSION['tk_log_user_id'] = $uid;
		
		
		//setcookie('tk_log_user_id', self::$uid, time()+60*60*24*30);
		
		return $uid;
	}
	
	static function store($type, $message = NULL) {
		$type_id = self::get_type($type);
		
		data::insert(
			'logs', 
			'id_usuario, id_tipo, log, fecha',
			'"'.self::$logid.'", "'.$type_id.'", "'.$message.'", "'.calendar::now().'"'
		);
		
		self::update_last_session();
		
		return true;
	}
	
	static public function get_type($type) {
		if (array_key_exists($type, self::$types)) {
			return self::$types[$type];
		} else {
			$t = data::fetch('*', 'logs_tipos', 'tipo = "'.$type.'"');
			
			if (isset($t->id)) {
				self::$types[$type] = $t->id;
				
				return $t->id;
			} else {
				$id = data::insert('logs_tipos', 'tipo', '"'.$type.'"');
				self::$types[$type] = $id;
				
				return $id;
			}
		}
	}
	
	static public function update_last_session() {
		data::update(
			'logs_usuarios',
			'ultima_session = "'.calendar::now().'"',
			self::$logid
		);
	}
	
	static public function session_time() {
		$session = data::fetch('*', 'logs_usuarios', 'id = "'.self::$logid.'"');
		return calendar::diference($session->inicio_session, $session->ultima_session);
	}
}


/*require_once('../toolkit.php');


if (log::start(3) != 'refreshed') {
	log::store('login');
} else {
	log::store('navigating', calendar::now());
	var_export(log::session_time());
}*/
	
	
	
	
?>