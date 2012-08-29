<?php
class field {
	public $name;
	public $type;
	public $value;
	public $label;
	public $options;
	
	protected static $regex = array(
		'id'		=> '/[0-9]+/',
		'integer'	=> '/[0-9]+/',
		'email'		=> '/[A-z0-9_\-+\.]+@[A-z0-9_+\.]+\.[A-z]{3}(\.[A-z]{2})?/',
		'date'		=> '/[0-9]{2,3}-[0-9]{1,2}-[0-9]{1,2}/',
		'time'		=> '/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,4}/',
		'datetime'	=> '/[0-9]{2,3}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,4}/'
	);
	
	public function field($name, $type = 'varchar', $value = NULL, $label = NULL, array $options = NULL) {
		$this->name 	= $name;
		$this->type		= $type;
		$this->value	= $value;
		$this->options	= $options;
		
		if (!isset($this->options['validate'])) //TODO: create validate property.
			$this->options['validate'] = false;
		
		if ($label == NULL) {
			$this->label = $name;
		} else {
			$this->label = $label;
		}
		
		if (isset($options['type'], $options['table'], $options['fk']) and $options['type'] == 'fk') {
			$this->type		= $options['type'];
			$this->table 	= $options['table'];
			$this->fk		= $options['fk'];
		}
	}
	
	public function isValidable() {
		if (array_key_exists($this->type, self::$regex)) {
			return true;	
		} else {
			return false;	
		}
	}
	
	public function validate($value) {
		if ($this->isValidable() === true) {
			
			//echo $this->type.': '.$this->regex[$this->type].'<br />';
			
			if (preg_match(self::$regex[$this->type], $value) != false) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	
	public function validateInput($value = NULL) {
		if ($value === NULL) {
			return $this->options['validate'];
		} else {
			$this->options['validate'] = (boolean) $value;
		}
	}
	
	public function label() {
		return html::label($this->name, $this->name);
	}
	
	public function input() {
		return form::field($this->name, $this->value, $this->type, true, $this->options); //???
	}
	
	public function view() {
		return form::input($this->name, $this->type, $this->value, true, NULL, $this->options); // (isset($this->options['selected'])) ? $this->options : NULL
	}
	
	public function __toString() {
		return $this->view();	
	}
}

?>