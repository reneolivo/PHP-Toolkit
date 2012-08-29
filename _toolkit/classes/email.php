<?php
	class email {
		private static $content;
		private static $random;
		
		public static function send($from, $to, $subject, $html, $bcc=NULL, array $attac=NULL) {
			self::$random = uniqid();
			
			$heads		 = "MIME-Version: 1.0\n";			
			$heads	   	.= 'From: '.$from."\r\n";
			$heads	   	.= 'To: '.$to."\r\n";
			$heads	   	.= 'Subject: '.$subject."\r\n";	
			$heads		.= "Content-type: multipart/mixed; boundary='PHP-mixed-".self::$random."'\r\n";

			
			
			/*self::$content 		 = "--PHP-alt-".self::$random."\r\n";
			self::$content 		.= "Content-Type: text/html; charset='utf-8'\r\n";
			self::$content		.= "Content-Transfer-Encoding: 7bit\r\n";
			self::$content		.= "<p><strong>Hola mundo!</strong></p>";
			self::$content		.= "--PHP-alt-".self::$random."--";		*/
		
			if (mail($to, $subject, self::$content, $heads)) {
				return true;
			} else {
				return false;
			}
		}
	}
?>