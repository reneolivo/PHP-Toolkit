<?php
	class image {
		const top_left = 0;
		const top_center = 1;
		const top_right = 2;
 
		const middle_left = 3;
		const middle_center = 4;
		const middle_right = 5;
 
		const bottom_left = 6;
		const bottom_center = 7;
		const bottom_right = 8;
 
		static function init($file) {
 			ini_set("memory_limit","60M");
			
			if (is_file($file)) {
				$info = getimagesize($file);
 
				$img = NULL;
 
				switch($info['mime']) {
					case 'image/jpeg':
						$img = imagecreatefromjpeg($file);
					break;
 
					case 'image/gif':
						$img = imagecreatefromgif($file);
					break;
 
					case 'image/png':
						$img = imagecreatefrompng($file);
					break;
 
					case 'image/bmp':
						$img = imagecreatefromwbmp($file);
					break;
 
					default:
						return false;
					break;
				}
				return $img;
			} else {
				return false;
			}
		}
 
		static function crop($source, $file, $width, $height, $orientation = 0) {
			file::copy($source, $file);
 
			if ($img = self::init($file)) {
 
				switch($orientation) {
					case 1:
						$x = ceil((imagesx($img) / 2) - ($width / 2));
						$y = 0;
					break;
					case 2:
						$x = imagesx($img) - $width;
						$y = 0;
					break;
					case 3:
						$x = 0;
						$y = ceil((imagesy($img) / 2) - ($height / 2));
					break;
					case 4:
						$x = ceil((imagesx($img) / 2) - ($width / 2));
						$y = ceil((imagesy($img) / 2) - ($height / 2));
					break;
					case 5:
						$x = imagesx($img) - $width;
						$y = ceil((imagesy($img) / 2) - ($height / 2));
					break;
					case 6:
						$x = 0;
						$y = imagesy($img) - $height;
					break;
					case 7:
						$x = ceil((imagesx($img) / 2) - ($width / 2));
						$y = imagesy($img) - $height;
					break;
					case 8:
						$x = imagesx($img) - $width;							
						$y = imagesy($img) - $height;
					break;
					default:
						$x = 0; $y = 0;
					break;
				}
 
				if (imagesx($img) < $width) {
					$width = imagesx($img);
					$x = 0;
				}
 
				if (imagesy($img) < $height) {
					$height = imagesy($img);
					$y = 0;
				}
 
				$crop = imagecreatetruecolor($width, $height);
 
				$white = imagecolorallocate($crop, 255, 255, 255);
 
				imagefill($crop, 0, 0, $white);
 
				imagecopy($crop, $img, 0, 0, $x, $y, $width, $height);
 
				//echo "w: ".imagesx($img)."<br />y: ".imagesy($img)."<br />x: $x<br />y: $y";
 
 
				imagejpeg($crop, $file, 100);
 
				imagedestroy($img);
				imagedestroy($crop);
			} else {
				return false;
			}
		}
 
		static function resize($source, $file, $width, $height) {
			$const = 0.5;
 
			file::copy($source, $file);
 
			$img = self::init($file);
			$resize = NULL;
 
			$fwidth = imagesx($img);
			$fheight = imagesy($img);
 
			if ($fwidth > $fheight) {
				$por = $fheight / $fwidth;
 
				$fwidth = $width;
 
 
				if ($por > $const) {
					$fheight = $width * $por;
 
					$resize = imagecreatetruecolor($fwidth, $fheight);
 
					imagecopyresampled($resize, $img, 0, 0, 0, 0, $fwidth, $fheight, imagesx($img), imagesy($img));
 
					imagejpeg($resize, $file, 100);
 
					imagedestroy($img);
					imagedestroy($resize);
				} else {
					//$por = $height / imagesy($img);
 
					if (imagesy($img) > $height) {
						$fwidth = ceil($por * imagesx($img));
						$fheight = $height;
 
						//echo "x: ".imagesx($img)." <br />y: ".imagesy($img)." <br />por: $por <br />fwidth: $fwidth <br />fheight: $fheight";
 
						$resize = imagecreatetruecolor($fwidth, $fheight);
 
						imagecopyresampled($resize, $img, 0, 0, 0, 0, $fwidth, $fheight, imagesx($img), imagesy($img));
 
						imagejpeg($resize, $file, 100);
 
						imagedestroy($img);
						imagedestroy($resize);
					}
 
					if (!self::crop($file, $file, $width, $height, self::middle_center)) {
						return false;
					}
				}
			} else {
				$por = $fwidth / $fheight;
 
 
				$fheight = $height;
 
 
				if ($por > $const) {
					$fwidth = $height * $por;
 
					$resize = imagecreatetruecolor($fwidth, $fheight);
 
					imagecopyresampled($resize, $img, 0, 0, 0, 0, $fwidth, $fheight, imagesx($img), imagesy($img));
 
					imagejpeg($resize, $file, 100);
 
					imagedestroy($img);
					imagedestroy($resize);
				} else {
					//$por = $width / imagesx($img);
 
					if (imagesx($img) > $width) {
						$fwidth = $width;
						$fheight = ceil($por * imagesy($img));
 
						//echo "<hr />x: ".imagesx($img)." <br />y: ".imagesy($img)." <br />por: $por <br />fwidth: $fwidth <br />fheight: $fheight";
 
						$resize = imagecreatetruecolor($fwidth, $fheight);
 
						imagecopyresampled($resize, $img, 0, 0, 0, 0, $fwidth, $fheight, imagesx($img), imagesy($img));
 
						imagejpeg($resize, $file, 100);
 
						imagedestroy($img);
						imagedestroy($resize);
					}
 
 
					if (!self::crop($file, $file, $width, $height, self::middle_center)) {
						return false;
					}
 
 
				}
			}
 
			return true;
		}
		
		static function crop_resize($source, $file, $width, $height, $orientation = self::middle_center){
			file::copy($source, $file);
 
			$img = self::init($file);
			
			if ($img == false) die();
			
			$resize = NULL;
 
			$fwidth = imagesx($img);
			$fheight = imagesy($img);
 
			if($fwidth < $fheight){
				$por = $fheight / $fwidth;
				$fheight = ceil($width * $por);
				$fwidth = $width;
 
			}else{
				$por = $fwidth / $fheight;
				$fwidth = ceil($height * $por);
				$fheight = $height;
 
				if($fwidth < $width){
					$por = imagesy($img) / imagesx($img);
					$fheight = ceil($width * $por);
					$fwidth = $width;	
 
				}				
			}			
			$resize = imagecreatetruecolor($fwidth, $fheight);
			imagecopyresampled($resize, $img, 0, 0, 0, 0, $fwidth, $fheight, imagesx($img), imagesy($img));
 
			imagejpeg($resize, $file, 100);
 
			imagedestroy($img);
			imagedestroy($resize);
 
			self::crop($file, $file, $width, $height, $orientation);		
 
		}
 
		static function execute_resize($img, $width, $height) {
 
		}
	}
?>