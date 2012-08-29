<?php
	class formInterface {
		public $fieldSet;
		
		public $action;
		public $method;
		public $enctType;
		public $options;
		
		public $hide = array();
		
		private $validate = false;
		
		public function formInterface(fieldSet $fieldSet, $action = '', $method = 'post', $enctType = '') {
			$this->fieldSet($fieldSet);
			$this->setState($action, $method, $enctType);
		}
		
		public function setState($action = '', $method = 'post', $enctType = '', array $options = array()) {
			$this->action = $action;
			$this->method = $method;
			$this->enctType = $enctType;
			$this->options = $options;
		}
		
		public function setValues($array) {
			$this->fieldSet->setValues((array) $array);	
		}
		
		public function clearValues() {
			$this->fieldSet->clearValues();	
		}
		
		public function hideFields() {
			$this->hide = func_get_args();
		}
		
		public function validate($validate = NULL) {
			if ($validate !== NULL) {
				$this->validate = $validate;
				
				$this->fieldSet->forEachField(function($field) use ($validate) {
					if ($field->isValidable() === true)
						$field->validateInput($validate);
				});
			} else {
				return $validate;	
			}
		}
		
		public function init($action = '', $method = 'post', $enctType = '', array $options = array()) {
			$this->setState($action, $method, $enctType, $options);
			
			return form::init($action, $method, $enctType, $options);
		}
		
		public function open() {
			return form::init($this->action, $this->method, $this->enctType);	
		}
		
		public function fieldSet(fieldSet $fieldSet) {
			$this->fieldSet = $fieldSet;	
		}
		
		public function forEachField($function) {
			return $this->fieldSet->foreachField($function);
		}
		
		public function submit($value = NULL) {
			return form::submit($value);
		}
		
		public function close() {
			return '</form>';	
		}
		
		public function view($action = '', $method = 'post', $enctType = '') {
			$view = $this->init($action, $method, $enctType, array('validate' => $this->validate));
			$hide = $this->hide;
			
			$this->forEachField(function($field) use (&$view, $hide) {
					//echo $field->type.', ';
					if (!in_array($field->name, $hide) and $field->type != 'id') {
						if ($field->validateInput() === true)
							$view = form::loadValidator().$view;
							
						$view .= $field->view();
					}
			});
				
			$view .= $this->submit();
			$view .= $this->close();
			
			return $view;
		}
		
		public function field($fieldName) {
			return $this->fieldSet->field($fieldName);
		}
		
		public function addField($field) {
			return $this->fieldSet->addField($field);
		}
		
		public function prepend($field) {
			return $this->fieldSet->prepend($field);	
		}
		
		public function addFieldAfter($fieldName, $field) {
			return $this->fieldSet->addFieldAfter($fieldName, $field);	
		}
		
		public function addFieldBefore($fieldName, $field) {
			return $this->fieldSet->addFieldBefore($fieldName, $field);	
		}
		
		public function addFieldAt($position, $field) {
			return $this->fieldSet->addFieldAt($position, $field);	
		}
		
		public function removeField($fieldName) {
			return $this->fieldSet->removeField($fieldName);
		}
		
		public function removeFieldAt($position) {
			return $this->fieldSet->removeField($position);
		}
		
		public function pushField($field) {
			return $this->fieldSet->push($field);
		}
		
		public function popField() {
			return $this->fieldSet->pop();
		}
		
		public function sortFields() {
			return $this->fieldSet->sort();
		}
		
		
		public function __invoke() {
			$fieldName = func_get_arg(0);
			if ($fieldName !== false)
				return $this->fieldSet->{$fieldName};
		}
		
		public function __toString() {
			return $this->view();	
		}
	}
?>