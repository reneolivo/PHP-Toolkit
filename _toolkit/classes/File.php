<?php
class file {
	static function copy($from, $to) {
 
		if ($from != $to and is_file($from)) {
			
			$dir_name = dirname($to);
			folder::create($dir_name);

			return copy($from, $to);
		} else {
			return false;
		}
	}

	static function move($from, $to) {			
		if ($from != $to and is_file($from)) {
			if (is_file($to))
				unlink($to);

			$dir_to = dirname($to);
			
			//echo "<h1>{$dir_to}</h1>";
			
			if (!is_dir($dir_to)) {
				//$dir_name = dirname($dir_to);
				//echo('---<strong>dirname</strong>: '.$dir_name);
				folder::create($dir_to);
			}

			return rename($from, $to);
		} else {
			return false;
		}
	}

	static function delete($file) {
		if (is_file($file)) {
			return unlink($file);
		} else {
			return false;
		}
	}
	
	static function upload($fileName, $destination) {
		if (isset($_FILES[$fileName])) {
			folder::create(dirname($destination));
			
			if (move_uploaded_file($_FILES[$fileName]['tmp_name'], $destination)) {
				PHPToolkit::getClass('FileInterface');
				return new FileInterface($destination);
			}
		}
		
		return false;
	}
}
?>