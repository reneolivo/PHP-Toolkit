<?php
	class userTemplate extends cmp_interface {
		public function __construct($cmp_name, $fields) {
			parent::cmp_interface($cmp_name, $fields);
		}
		
		public function login($email, $password) {
			$this->where = "email = ".data::escapeString($email)." AND password = ".data::escapeString(md5($password))." AND isActive = TRUE";
			
			$r = $this->fetch();
			
			if (isset($r->id)) {
				
				$_SESSION['user_id'] = $r->id;
				
				return $r;
			} else {
				return false;
			}
		}
		
		public function validate_user($user_id, $fail_url = NULL) {
			$r = $this->fetch($user_id);
			
			if (isset($r->id)) {
				return $r;
			} else if ($fail_url != NULL) {
				header('location: '.$fail_url);
			} else {
				return false;
			}
		}
		
		public function validate($fail_url = NULL) {
			if (isset($_SESSION['user_id'])) {
				$user_id = $_SESSION['user_id'];
				
				return $this->validate_user($user_id, $fail_url);		
			} else if ($fail_url != NULL) {
				header('location: '.$fail_url);
			} else {
				return false;
			}
		}
		
		public function logout() {
				unset($_SESSION['user_id']);
		}
		
		public function insert(array $values_array) {
			if (isset($values_array['password'])) {
				$values_array['password'] = md5(data::escapeString($values_array['clave']));
			}
			
			return parent::insert($values_array);
		}
		
		public function update($id, array $values_array) {
			if (isset($values_array['password'])) {
				$values_array['password'] = md5(data::escapeString($values_array['clave']));
			}
			
			return parent::update($id, $values_array);
		}
	}
?>