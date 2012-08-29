<?php
	class ImageInterface {
		public $file;
		private $image;
		
		public function imageInterface($file) {
			if (get_class($file) != 'FileInterface')
				$file = new FileInterface($file);
			$this->file = $file;
			
			if (!$this->image = image::init($file->getPath())) {
				return false;	
			}
		}
		
		public function resize($width, $height = NULL) {
			if ($height === NULL)
				$height = $width;
			
			return image::resize($this->file->getPath(), $this->file->getPath(), $width, $height);	
		}
	}
?>