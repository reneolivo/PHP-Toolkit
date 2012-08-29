<?php
class cmp_interface {
	public $name;
 
	public $table;
	public $fields = array();
 
	private $query = NULL;
 
	public $id = NULL;
 
	public $where = NULL;
	public $order = NULL;
	public $limit = NULL;
 
	//protected $cmp;
 
	protected $select_str = NULL;
 
	public function cmp_interface($table, array $fields) {
		//$this->cmp		= new components();
 
		$this->name 	= get_class($this);
 
		$this->table	= $table;
 
		foreach ($fields as $f) {
			if (is_a($f, 'cmp_field')) {
				$this->fields[$f->field] = $f;
			} else {
				die("<br /><b>ERROR:</b> field <i>\"{$f}\"</i> is not an instance of <i>\"cmp_field\"</i><br />");
			}
		}
 
	}
 
	protected function select($fields = NULL) {
		if ($fields == NULL) {
			if ($select_str == NULL) {
				$fields = '`'.implode('`, `', array_keys($this->fields)).'`';
 
				/*$fields = '';
				foreach($this->fields as $field) {
					if ($field->type != 'fk') {
						$fields .= '`'.$this->table.'`.`'.$field->field.'`, ';
					} else {
						$fields .= '`'.$field->table.'`.`'.$field->field.'`, ';
					}
				}
				$fields = substr($fields, 0, -2);
 
				$this->select_str = $fields;*/
			} else {
				$fields = $this->select_str;
			}
		}
 
		//printf($fields.', '.$this->where.', '.$this->order.', '.$this->limit);
 
		$query = data::select($fields, $this->table, $this->where, $this->order, $this->limit);
 
 
		return $query;
	}
 
	public function fetch($id = NULL) {
		if ($this->query !== NULL)
			$tmp_query = $this->query;
			
		$query = NULL;
 
		if ($id != NULL) {
			$this->where = "id = {$id}"; 
			$query = $this->select(); //data::select('*', $this->table, "id = {$id}");
		} else if ($this->where != NULL or $this->order != NULL or $this->limit != NULL) {	
			$this->limit = 1;		
			$query = $this->select();
			$this->clearQuery();
		} else {
			//return false;
			$this->order = 'id DESC';
			$query = $this->select();
		}
		
		$this->query = $tmp_query;
 
		if (is_resource($query)) {		
			return mysql_fetch_object($query);
		} else {
			return false;
		}
	}
	
	public function fetchAll() {
		$query = data::select('*', $this->table, $this->where, $this->order, $this->limit);
		
		$array = array();
		while ($r = mysql_fetch_object($query)) {
			array_push($array, $r);
		}
		
		return $array;
	}
 
	public function fetchKeysValues($key, $value) {
		$query = data::select($key.', '.$value, $this->table, $this->where, $this->order, $this->limit);
 		//data::debug();
		
		$array = array();
		while ($r = mysql_fetch_object($query)) {
			$array[$r->{$key}] = $r->{$value};
		}
 
		return $array;
	}
 
	public function countRows() {
		return data::count_rows($this->table, $this->where);
	}
 
	public function view($file) {
		$query = $this->select();
 
		while ($r = mysql_fetch_object($query)) {
			require(dirname(__FILE__)."/../components/{$this->name}/views/{$file}.php");
			echo "\n";
		}
 
		$this->clearQuery();
	}
 
	public function includeView($file, &$r) {
		include(dirname(__FILE__)."/../components/{$this->name}/views/{$file}.php");
		echo "\n";
	}
 
	public function display($function) {
		$query = $this->select();
 
		while ($row = mysql_fetch_object($query)) {
			$function($row);
		}
 
		$this->clearQuery();
	}
 
	public function displayForm($action = NULL, $method = NULL, $enctype = NULL) {
		echo form::init($action, $method, $enctype, array('class' => '_tk_form _tk_cmp_'.$this->name));
 
		foreach ($this->fields as $f) {
			if ($f->type != 'id' and (!isset($f->options['visible']) or $f->options['visible'] == true)) {
				echo form::input(
					$f->field, 
					$f->type,
					$f->value,
					$f->label
				);
			}
		}
 
		echo form::submit();
	}
 
	public function displayFormFields() {
		echo form::init();
 
		foreach (func_get_args() as $f) {
			if (isset($this->fields[$f])) {
				echo form::input(
					$this->fields[$f]->field, 
					$this->fields[$f]->type,
					$this->fields[$f]->value,
					$this->fields[$f]->label
				);
			}
		}
 
		echo form::submit();
	}
 
	public function displayUpdateForm($id, $action = NULL, $method = NULL, $enctype = NULL) {
		$r = $this->fetch($id);
 
		echo form::init($action, $method, $enctype, array('class' => '_tk_form _tk_cmp_'.$this->name));
 
		foreach ($this->fields as $f) {
			if ($f->type != 'id' and (!isset($f->options['visible']) or $f->options['visible'] == true)) {
				if (in_array($f->type, array('select', 'boolean'))) {
					echo form::input(
						$f->field, 
						$f->type,
						$f->value,
						$f->label,
						$r->{$f->field}
					);
				} else {
					echo form::input(
						$f->field, 
						$f->type,
						$r->{$f->field},
						$f->label
					);
				}
			}
		}
 
		echo form::submit();
	}
 
	public function form($action = NULL, $method = NULL, $enctype = NULL) {
		echo form::init($action, $method, $enctype, array('class' => '_tk_form _tk_cmp_'.$this->name));
	}
 
	public function input($f, $type = NULL) {		
		$field = $this->fields[$f];
		if ($type == NULL) $type = $field->type;
 
		echo form::input(
			$field->field, 
			$type,
			$field->value
		);
	}
 
	public function submit($label) {
		echo form::submit($label);
 
		foreach ($this->fields as $f) {
			$f->value = "";
		}
	}
 
	public function insert(array $valuesArray) {
		$valuesArray = data::escape($valuesArray);
		
		$fields = '';
		$values = '';
 
		foreach ($valuesArray as $k => $v) {
			if (array_key_exists($k, $this->fields)) {
				$fields .= '`'.($k)."`, ";
				$values .= "'".($v)."', ";
			} else {
				die("<br /><b>ERROR:</b> field <i>\"{$k}\"</i> is not part of <i>\"{$this->name}\"</i><br />");
			}
		}
 
		$fields = substr($fields, 0, -2);
		$values = substr($values, 0, -2);
 
 
		return data::insert(
			$this->table,
			$fields,
			$values
		);
	}
 
	public function update($id, array $valuesArray) {
		$set = '';
 
		foreach ($valuesArray as $k => $v) {
			if (array_key_exists($k, $this->fields)) {
				$set .= "`".mysql_real_escape_string($k)."` = '".mysql_real_escape_string($v)."',\n ";
			} else {
				die("<br /><b>ERROR:</b> field <i>\"{$k}\"</i> is not part of <i>\"{$this->name}\"</i><br />");
			}
		}
 
		$set = substr($set, 0, -3);
 
		return data::update(
			$this->table,
			$set,
			$id
		);
	}
 
	public function delete($id = NULL) {
		if ($this->where != NULL or $this->order != NULL or $this->limit != NULL) {
			$return = data::delete(
				$this->table,
				$this->where,
				$this->order,
				$this->limit
			);
			$this->clearQuery();
 
			return $return;
 
		} else if (id != NULL) {
			return data::delete(
				$this->table,
				$id
			);
		} else {
			return false;
		}
	}
 
	public function clearQuery() {
		$this->query = NULL;
		$this->where = NULL;
		$this->order = NULL;
		$this->limit = NULL;
	}
 
	public function forEachRow($function = NULL) {
		if ($function === NULL) {
			if ($this->query == NULL) {
				$this->query = $this->select() or die(mysql_error());
			}
	 
			$r = mysql_fetch_object($this->query);
	 
			if ($r == false) {
				$this->clearQuery();
			}
	 
			return $r;
		} else {
			$query = $this->select() or die(mysql_error());
			
			while ($r = mysql_fetch_object($query)) {
				$function($r);	
			}
		}
	}
 
	/*public function jason() {
		$query = $this->select();
 
		$jason = '[';
		$row = '';
		while ($r = $this->foreachrow()) {
			$row = '{';
 
			$fields = '';
			foreach ($r as $k => $v) {
				$fields .= "'{$k}': '{$v}',";
			}
 
			$row .= substr($fields, 0, -1).'},';
		}
		$jason .= substr($row, 0, -1).']';
 
		return $jason;
	}
	
	public function field_attributes() {
		$jason = '{';
		foreach($this->fields as $k => $v) {
			
			if ($v->type != 'id' and ((isset($v->options['visible']) and $v->options['visible'] == true) or !isset($v->options['visible'])))		
				$jason .= "\n\t\"".$k.'": '.html::to_jason($v->options).',';
		}
		return substr($jason, 0, -1)."\n}";
	}*/
	
	###################################################################
	# VALIDATION FUNCTIONS
 	
	public function javascriptValidator() {
		if (toolkit::load_javascript_once('configuration.php') and toolkit::load_javascript_once('jquery.js') and toolkit::load_javascript_once('validator.js')) {
			
			return true;
		} else {
			return false;
		}
	}
	
	public function validateInput(array $values) {
		foreach ($values as $k => $v) {
			if (array_key_exists($k, $this->fields) and !$this->fields[$k]->validate($v)) {
				return false;
			}
		}
 
		return true;
	}
 
	###################################################################
	# DIRECTORY FUNCTIONS
 
	public function upload($file, $destiny) {
		//echo "FILE: {$_FILES[$file]['name']}, SIZE: {$_FILES[$file]['size']} IS UPLOADED: ".is_uploaded_file($_FILES[$file]['tmp_name']);
		
		if (isset($_FILES[$file]) and $_FILES[$file]['size'] > 0 and is_uploaded_file($_FILES[$file]['tmp_name'])) {
			$dir = dirname($file);
			folder::create_dir($dir);
 			//echo '<h3>'.$_FILES[$file]['tmp_name'].' to '.$destiny.'</h3>';
			return move_uploaded_file($_FILES[$file]['tmp_name'], $destiny);
		} else {
			return false;
		}
	}
}
?>