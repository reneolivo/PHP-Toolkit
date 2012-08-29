<?php
	class calendar {
		public function calendar() {
			date_default_timezone_set('America/Caracas');
		}
		
		static function now() {
			date_default_timezone_set('America/Caracas');
			return date('Y-m-d H:i:s.u');
		}
		
		static function years($date) {
			###http://www.php.net/date :: txmn41@yahoo.com
			
			 list($year,$mon,$day) = explode("-",$date);
			 $today = getdate(time());
			 // find the difference in the years of the two dates
			 
			 $yeardiff = $today['year'] - $year;
			 // if the current date occurs before the birthday, subtract one
			 $birth_jd = gregoriantojd($mon,$day,$today['year']);
			 $today_jd = gregoriantojd($today['mon'],$today['mday'],$today['year']);
			 if ($today_jd < $birth_jd) {
				$yeardiff--;
			 }
			 return($yeardiff); 
		}
		
		static function diference($date1, $date2) {			
			$date1 = strtotime($date1);
			$date2 = strtotime($date2);
			
			$date3 = $date2 - $date1;
			
			return $date3;
		}
		
		static function secondstotime($sec) {
			/*$year = floor($sec / (60*60*24*364.25));
			$mons = floor($year 
			$mins = floor($sec / 60);*/
		}
	}
	
	//echo calendar::diference('2009-07-1 12:30:15', calendar::now());
	
	
?>