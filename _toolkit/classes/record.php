<?php
	class record {
			private static $parent = null;
			protected $values = array();
			var $parent2;
			
			
			##############################################
			#RECORD FUNCTIONS
			
			/*public function setParent(cmp_interface &$parent) {
				self::$parent = $parent;	
			}*/
			
			public function setParent(&$parent) {
				self::$parent = $parent;
				$this->parent2 = $parent;
			}
			
			public function setValues($values = array()) {
				$this->values = (array) $values;	
			}
			
			public function __get($name) {
				if (isset($this->values[$name]))
					return $this->values[$name];
			}
			
			public function __set($name, $value) {
				$this->values[$name] = $value;	
			}
			
			##############################################
			#TESTING FUNCTIONS
			
			public function varExport($return = false) {
				return var_export($this->values, $return);
			}
			
			public function __toString() {
				return $this->varExport(true);
			}
			
			##############################################
			#DB FUNCTIONS
			
			public function update() {
				self::$parent->update($this->values['id'], $this->values);	
			}
			
			##############################################
			#FORM FUNCTIONS
			
			public function form() {
				$form = self::$parent->form();
				if ($this->values !== NULL)
					$form->setValues($this->values);
				return $form;
			}
	}
?>