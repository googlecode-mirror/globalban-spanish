<?php 
/*
    This file is part of GlobalBan.

    Written by Stefan Jonasson <soynuts@unbuinc.net>
    Copyright 2008 Stefan Jonasson
    
    GlobalBan is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GlobalBan is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GlobalBan.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * This class is used for creating paging
 */ 
class Pager
{
  var $total; // Total number of items
  var $limit; // Number of items per page
  var $page; // The page number
  var $numPages;
  var $offset;
  
  function __construct($total, $limit, $page) {
    $this->init($total, $limit, $page);
  }
  
  function Pager($total, $limit, $page) {
    $this->init($total, $limit, $page);
  }

	function init($total, $limit, $page)  
	{  
		$this->total  = (int) $total;  //Total number of items
		$this->limit    = max((int) $limit, 1);  //Items per page
		$this->page     = (int) $page;  //Page Number
		$this->numPages = ceil($total / $limit);  
		
		$this->page = max($page, 1);  
		$this->page = min($page, $numPages);  
		
		$this->offset = ($page - 1) * $limit;
	}
  
  function getTotal() {
    return $this->total;
  }
  
  function setTotal($total) {
    $this->total = $total;
  }
  
  function getLimit() {
    return $this->limit;
  }
  
  function setLimit($limit) {
    $this->limit = $limit;
  }
  
  function getPage() {
    return $this->page;
  }
  
  function setPage($page) {
    $this->page = $page;
  }
  
  function getNumPages() {
    return $this->numPages;
  }
  
  function setNumPages($numPages) {
    $this->numPages = $numPages;
  }
  
  function getOffset() {
    return $this->offset;
  }
  
  function setOffset($offset) {
    $this->offset = $offset;
  }
}
?>
