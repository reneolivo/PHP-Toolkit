<?php
	class fileInterface {
		private $source;
		
		public function fileInterface($source) {
			$this->source = $source;
		}
		
		public function getExtension() {
			return substr($this->source, strrpos($this->source, '.'));	
		}
		
		public function rename($newName) {
			$newName = dirname($this->source).'/'.$newName;
			if (rename($this->source, $newName)) {
				$this->source = $newName;
				return true;
			} else {
				return false;	
			}
		}
		
		public function move($destination) {
			$destination = dirname($destination).'/'.basename($this->source);
			if (File::move($this->source, $destination)) {
				$this->source = $destination;
				return true;
			} else {
				return false;	
			}
		}
		
		
		/*--------------------------------------------*/
		
		public function getBaseName() {
			return basename($this->source);	
		}
		
		public function getFolder() {
			return dirname($this->source).'/';	
		}
		
		public function getPath() {
			return $this->source;	
		}
		
		public function getImage() {
			return new ImageInterface($this);	
		}
		
		/*------------------------------------------*/
		
		public function __toString() {
			return $this->source;	
		}
	}
?>