<?php
	class folder {
		static function create($dir) {
			$dir = rtrim($dir, '/').'/';
 
			//echo "<h6>{$dir}</h6>";
 
			if (!is_dir($dir) and $dir != '') {
				$dir_ar = preg_split('[\\/]', $dir);
 
				$dir_str = '';
				foreach($dir_ar as $d) {
					$dir_str .= $d.'/';
 
					self::recursiveCreate($dir_str);
				}
 
				return true;
			} else {
				return false;
			}
		}
 
		static private function recursiveCreate($dir) {
			if (!is_dir($dir)) {				
				$success = (mkdir($dir));
				chmod($dir, 0777);
				return $success;
			} else {
				return false;
			}
		}
 
 
		static function delete($path) {
			self::recursiveDelete($path);
			if (is_dir($path))
				rmdir($path);
			else 
				return false;
		}
 
		static private function recursiveDelete($path) {
			$path= rtrim($path, '/').'/';
			if (is_dir($path)) {
				$handle = opendir($path);
				while(false !== ($file = readdir($handle)))
					if($file != "." and $file != ".." )
					{
						$fullpath= $path.$file;
						if( is_dir($fullpath) )
						{
							self::recursiveDelete($fullpath);
							rmdir($fullpath);
						}
						else
						  unlink($fullpath);
					}
				closedir($handle);
				return true;
			} else {
				return false;
			}
		}
 
		static function copy($from, $to) {
			$from	= dirname($from);
			$to		= dirname($to);
 
			if ($from != $to) {
				if (!is_dir($to)) {
					self::create($to);
				}
 
				return self::recursiveCopy($from, $to);
			} else {
				return false;
			}
		}
 
		static private function recursiveCopy($from, $to) {
			$from= rtrim(dirname($from), '/').'/';
			$to = rtrim(dirname($to), '/').'/';
 
			$handle = opendir($from);
			while(false !== ($file = readdir($handle))) {	
 
				if($file != "." and $file != "..")
				{
 
					$fullpath= $from.$file;
 
					if (rtrim($fullpath,'/') != rtrim($to,'/')) {
 
						if( is_dir($fullpath) )
						{
							//echo "<br /><strong>CREATE({$fullpath})</strong>";
 
							self::create($to.$file);
							self::recursiveCopy($fullpath, $to.$file);
 
						} else {
							copy($fullpath, $to.$file);
						}
					}
				}
			}
 
			closedir($handle);
		}
 
		static function move($from, $to) {
			$from 	= dirname($from);
			$to 	= dirname($to);
 
			if (!is_dir($to) and is_dir($from)) {
				return rename($from, $to);
			} else {
				return false;
			}
		}
 
		
	}
 
	/*var_dump(folder::create('juan/de/losantos'));
 
	var_dump(folder::copy('juan/de', 'juan/de/maria'));
 
	var_dump(folder::move('juan/de/maria', 'juan/de/jose'));
 
	var_dump(folder::delete('juan/de/losantos'));*/
 
	//var_dump(folder::copy_file('html.php', 'mihermano/fulano/html.php'));
 
	//var_dump(folder::move_file('html.php', 'mihermano/fulano/html.php'));
 
	//var_dump(folder::delete_file('Copia de html.php'));
?>