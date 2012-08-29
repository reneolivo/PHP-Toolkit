<?php
	class fieldSet {
		protected $fieldSet = array();
		
		public function fieldSet() {
			$fields = func_get_args();
			
			if (is_array($fields[0])) $fields = $fields[0];
			
			foreach($fields as $field) {
				if (get_class($field) == 'field')
					$this->fieldSet[$field->name] = $field;
			}
		}
		
		public function selectString() {
			//return implode('`, `', $this->fieldSet);
			$selectString = '';
			
			foreach ($this->fieldSet as $field) {
				$selectString .= "`{$field->name}`, ";	
			}
			
			return rtrim($selectString, ', ');
		}
		
		public function field($fieldName) {
			if (array_key_exists($fieldName, $this->fieldSet)) {
				return $this->fieldSet[$fieldName];	
			}
		}
		
		/*##############################################################
								VALUES
		################################################################*/
		
		public function setValues(array $values) {
			foreach ($this->fieldSet as $field) {
				$field->value = $values[$field->name];	
			}
		}
		
		public function setValuesStrict($values) {
			foreach ($this->fieldSet as $field) {
				if (!$field->value($values[$field->name]))
					return false;
			}
		}
		
		public function clearValues() {
			foreach ($this->fieldSet as $field) {
				$field->value = NULL;	
			}
		}
		
		/*##############################################################
								ETC
		################################################################*/
		
		public function forEachField($function) {
			foreach ($this->fieldSet as $field) {
				$function($field);
			}
		}
		
		public function __get($name) {
			if (!in_array($name, $this->fieldSet))
				return $this->fieldSet[$name];
			return false;
		}
		
		/*##############################################################
								FIELDS
		################################################################*/
		
		public function addField($field) {
			return $this->push($field);
		}
		
		public function getField($fieldName) {
			return $this->fieldSet[$fieldName];
		}
		
		public function prepend(field $field) {
			return $this->addFieldAt(0, $field);
		}
		
		public function push(field $field) {
			if (!array_key_exists($field->name, $this->fieldSet)) {
				$this->fieldSet[$field->name] = $field;
				return true;
			} else {
				return false;
			}
		}
		
		public function pop() {
			return array_pop($this->fieldSet);
		}
		
		public function sort() {
			return ksort($this->fieldSet);	
		}
		
		public function addFieldAfter($fieldName, field $field) {
			$new = array();
			$return = false;
			foreach($this->fieldSet as $k => $v) {
				$new[$k] = $v;
				if ($fieldName == $k) {
					$new[$field->name] = $field;
					$return = true;
				}
			}
			
			$this->fieldSet = $new;
			return $return;
		}
		
		public function addFieldBefore($fieldName, field $field) {
			$new = array();
			$return = false;
			foreach($this->fieldSet as $k => $v) {
				if ($fieldName == $k) {
					$new[$field->name] = $field;
					$return = true;
				}
				$new[$k] = $v;
			}
			
			$this->fieldSet = $new;
			return $return;
		}
		
		public function addFieldAt($position, field $field) {
			$new = array();
			$return = false;
			$x = 0;
			foreach($this->fieldSet as $k => $v) {
				if ($position == $x) {
					$new[$field->name] = $field;
					$return = true;
				}
				$new[$k] = $v;
				$x++;
			}
			
			$this->fieldSet = $new;
			return $return;
		}
		
		public function removeField($fieldName) {
			if (array_key_exists($fieldName, $this->fieldSet)) {
				unset($this->fieldSet[$fieldName]);
				return true;
			} else {
				return false;	
			}
		}
		
		public function removeFieldAt($position) {
			$position = (int) $position;
			if ($position >= 0) {
				$keys = array_keys($this->fieldSet);
				$position = $keys[$position];
				unset($this->fieldSet[$position]);
				return true;
			} else {
				return false;	
			}
		}
	}
?>