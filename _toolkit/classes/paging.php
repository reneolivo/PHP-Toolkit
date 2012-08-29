<?php
	class paging {
		public $totalRows	= NULL;
		public $totalPages	= NULL;
		public $rowsPerPage = NULL;
		public $currentPage = NULL;
		public $filter		= NULL;
		
		public function paging($rowsPerPage, $totalRows, $currentPage = NULL, $filter = NULL) {
			$this-> setValues($rowsPerPage, $totalRows, $currentPage, $filter);
		}
		
		public function setValues($rowsPerPage, $totalRows, $currentPage = NULL, $filter = NULL) {
			$this->rowsPerPage	= $rowsPerPage;
			$this->totalRows 	= $totalRows;
			$this->totalPages	= ceil($totalRows / $rowsPerPage);
			$this->currentPage	= ($currentPage !== NULL) ? ($currentPage <= $this->totalPages) ? $currentPage : $this->totalPages : 1;
			if ($filter !== NULL) {
				$filter->limit = $this->limit();
				$this->filter = $filter;
			} 
		}
		
		public function filter($where = NULL, $order = NULL, $limit = NULL, $override = false) {
			PHPToolkit::getClass('filter');
			
			$this->filter = new filter($where, $order, $limit, $override);
		}
		
		public function limit() {
			return ($this->rowsPerPage * $this->currentPage - $this->rowsPerPage).', '.$this->rowsPerPage; ##FIX FOR NON SQL LIKE DRIVERS	
		}
		
		public function view() {
			$paging = '<div class="_tkPaging">';
			for ($i = 1; $i <= $this->totalPages; $i++) {
				$paging .= html::a($i, '?pag='.$i, NULL, ($i == $this->currentPage) ? '_tkPagingSelectedPage' : NULL ).' ';
			}
			$paging .= '</div>';
			
			return $paging;	
		}
		
		public function __toString() {
			return $this->view();	
		}
	}
?>