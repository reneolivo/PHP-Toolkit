<?php
	class a {
		private static $a;
		public function a($a) {
			$this->a = $a;	
		}
	}
	
	class b {
		var $b = 1;
		private static $a;
		public function b($a) {
			self::$a = $a;	
		}
	}
	
	class_alias('b', 'b2');
	
	$b = new b(new a('a1'));
	$b2 = new b2(new a('a2'));
	
	echo '<br />1';
	var_export($b);
	
	echo '<br />2';
	var_export($b2);
	
	echo '<br />3';
	var_export($b);
?>