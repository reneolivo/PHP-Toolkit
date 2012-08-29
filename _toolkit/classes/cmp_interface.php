<?php
class cmp_interface {
	public $name;
 
	public $tableName;
	public $fieldSet;
 
	private $query = NULL; ## ELIMINAR
	public $result = NULL;
 
	public $id = NULL;
 
	public $where = NULL;
	public $order = NULL;
	public $limit = NULL;
 
	protected $select_str = NULL;
	
	protected $events = array();
 
	public function cmp_interface($tableName, fieldSet $fieldSet) {
		//$this->cmp		= new components();
 
		$this->name 	= get_class($this);
 
		$this->tableName = $tableName;
 
		$this->fieldSet = $fieldSet;
	}
	
	public function __invoke() {
		$args = func_get_args();
		
		return call_user_func_array(array($this, 'filter'), $args);
	}
	
	public function addEvent($eventName, $function) {
		$this->events[strtolower($eventName)] = $function;
	}
	
	public function clearEvent() {
		$args = func_get_args();
		
		foreach($args as $eventName) {
			unset($this->events[$eventName]);
		}
	}
	
	public function fireEvent() {
		$args = func_get_args();
		$eventName = strtolower($args[0]);
		
		if (isset($this->events[$eventName])) {
			$args = array_slice($args, 1);
			call_user_func_array($this->events[$eventName], $args);
		}
	}
	
	private function getRecordClass($values = NULL) {
		# I DIDN'T WANT TO RESORT TO THIS TRICK, BUT PHP DOESN'T HAVE REAL PRIVATE PROPERTIES";
		
		$className = 'CMP'.$this->name.'Record';
		if (!class_exists($className)) {
			class_alias('record', $className);
		}
		
		$class = new $className();
		$class->setParent(&$this);
		//var_export($className::$parent);
		if ($values !== NULL) $class->setValues($values);
		
		return $class;
	}
 
	protected function select($fields = NULL) {
		if ($fields === NULL) {
			if ($this->select_str === NULL) {
				$fields = $this->fieldSet->selectString();
 
			} else {
				$fields = $this->select_str;
			}
		}
		
 
		//printf($fields.', '.$this->where.', '.$this->order.', '.$this->limit);
 
		$this->result = data::select($fields, $this->tableName, $this->where, $this->order, $this->limit);
 		
		$this->clearQuery();
 
		return $this->result;
	}
	
	public function filter($where = NULL, $order = NULL, $limit = NULL, $override = false) {
		if (is_object($where) and (get_class($where) == 'filter' or (get_class($where) == 'paging' and $where->filter !== NULL))) {
			$filter = ($where->filter !== NULL) ? $where->filter : $where;
			$override = $order;
			
			$where = $filter->where;
			$order = $filter->order;
			$limit = $filter->limit;
		}
		
		if ($override === true) {
			if ($where !== NULL) $this->where = $where;
			if ($order !== NULL) $this->order = $order;
			if ($limit !== NULL) $this->limit = $limit;
		} else {
			$this->where = $where;
			$this->order = $order;
			$this->limit = $limit;
		}
		
		return $this;
	}
	
	public function getFilter($clearFilter = false) {
		PHPToolkit::getClass('filter');
		
		$filter = new filter($this->where, $this->order, $this->limit);	
		
		if ($clearFilter === true) $this->clearFilter();
		
		return $filter;
	}
	
	public function clearFilter() {
		$this->where = NULL;
		$this->order = NULL;
		$this->limit = NULL;
	}
	
	public function isFiltered() {
		return $this->where != NULL or $this->order != NULL or $this->limit != NULL;
	}
 
	public function fetch($id = NULL) {
		$query = NULL;
		
		if ($id !== NULL) {
			$query = $this->filter('id = '.data::escapeString($id))->select();	
			$this->clearQuery();
		} else if ($this->isFiltered()) {
			$query = $this->filter($this->where, $this->order, 1)->select();
			$this->clearQuery();
		} else {
			$query = $this->filter(NULL, 'id DESC')->select();
			$this->clearQuery();
		}
		
		$class = $this->getRecordClass(data::fetch($query));
		return $class;
	}
	
	public function fetchArray() {
		$query = data::select('*', $this->tableName, $this->where, $this->order, $this->limit);
		
		$array = array();
		while ($r = mysql_fetch_object($query)) {
			array_push($array, $r);
		}
		
		return $array;
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
			
			$this->fireEvent('beforeforeachrow', $this);
			
			$query = $this->select();
			
			data::forEachRow($query, $function);
			
			$this->fireEvent('afterforeachrow', $this);
		}
	}
	
	public function beforeForEachRow($function) {
		$this->addEvent('beforeforeachrow', $function);
	}
	
	public function afterForEachRow($function) {
		$this->addEvent('afterforeachrow', $function);
	}
	
	public function paging($rowsPerPage, $currentPage = NULL, $result = NULL) {
		$totalRows = $this->countRowsFromResult($result);
		
		PHPToolkit::getClass('paging');
		
		$paging = new paging($rowsPerPage, $totalRows, $currentPage, $this->getFilter(true));
		
		$this->limit = $paging->limit();
		
		return $paging; 
	}
 
	public function fetchKeysValues($key, $value) {
		$query = data::select($key.', '.$value, $this->tableName, $this->where, $this->order, $this->limit);
 		//data::debug();
		
		$array = array();
		while ($r = mysql_fetch_object($query)) {
			$array[$r->{$key}] = $r->{$value};
		}
 
		return $array;
	}
 
	public function countRows() {
		return data::countRows($this->tableName, $this->where, $this->order, $this->limit);
	}
	
	public function countRowsFromResult($result = NULL) {
		if ($result === NULL) {
			if ($this->result === NULL) {
				return $this->countRows();	
			} else {
				$result = $this->result;
			}
		}
		
		return data::countRowsFromResult($result);
	}
	
	public function fieldsValuesArray($fieldsValues, &$fields, &$values) {
		foreach ($fieldsValues as $k => $v) {
			array_push($fields, $k);
			array_push($values, $v);
		}
	}
 
	public function insert(array $fieldsValues) {
		
		$fields = array();
		$values = array();
		$this->fieldsValuesArray($fieldsValues, $fields, $values);
 
		return data::insert(
			$this->tableName,
			$fields,
			$values
		);
	}
 
	public function update($id, array $fieldsValues) {		
		$this->filter("id = ".data::escapeString($id));
		
		$fields = array();
		$values = array();
		$this->fieldsValuesArray($fieldsValues, $fields, $values);
		
		
		$return = data::update(
			$this->tableName,
			$fields,
			$values,
			$this->where,
			$this->order,
			$this->limit
		);
		
		$this->clearQuery();
		
		return $return;
	}
 
	public function delete($id = NULL) {
		
		if ($id !== NULL) {
			$this->filter("id = ".data::escapeString($id));
			
			data::delete(
				$this->tableName,
				$this->where,
				$this->order,
				$this->limit
			);
		} else {
			###LO DE MA	
		}
	}
 
	public function clearQuery() {
		$this->query = NULL;
		$this->where = NULL;
		$this->order = NULL;
		$this->limit = NULL;
	}
	
	
	public function form($action = '', $method = 'post', $enctType = '') {
		PHPToolkit::getClass('formInterface');
		
		$form = new formInterface(clone $this->fieldSet, $action, $method, $enctType);
		$form->clearValues(); //TODO: find why values are left behind from previous operations (passed by reference).
		
		return $form;
	}
}
?>