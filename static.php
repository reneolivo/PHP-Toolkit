<?php
	class a {
		public static $var;
		
		public function a($var) {
			self::$var = $var;
		}
		
		public function ret() {
			return self::$var;	
		}
	}
	
	class b extends a {
		public static $var;
	}
	
	class c extends a {}
	
	$a = new a('-a-');
	$a2 = new a('-a2-');
	$b = new b('-b-');
	$c = new c('-c-');
	
	b::$var = 'b';
	
	
	echo 'A: '.a::$var;
	echo 'A2: '.$a2::$var;
	echo ', B: '.b::$var;
	echo ', C: '.c::$var;
?>