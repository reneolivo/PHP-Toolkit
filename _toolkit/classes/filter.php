<?php
	class filter {
		public $where = NULL;
		public $order = NULL;
		public $limit = NULL;
		
		public function filter($where = NULL, $order = NULL, $limit = NULL) {
			$this->setFilter($where, $order, $limit, true);
		}
		
		public function setFilter($where = NULL, $order = NULL, $limit = NULL, $override = false) {
			if ($override !== true) {
				if ($where !== NULL) $this->where =  $where;
				if ($order !== NULL) $this->order =  $order;
				if ($limit !== NULL) $this->limit =  $limit;
			} else {
				$this->where = $where;
				$this->order = $order;
				$this->limit = $limit;	
			}
		}
	}
?>