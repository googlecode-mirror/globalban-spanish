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
class BadName {

  var $id;
  var $badName;
  var $filter;
  var $kick;
  
  function __construct() {
    $this->init();
  }
  
	function BadName() {
    $this->init();
  }
	
	function init() {
    $this->id = -1;
    $this->badName = -1;
    $this->filter = 1;
    $this->kick = 0;
  }
  
  function getId() {
    return $this->id;
  }
  
  function setId($id) {
    $this->id = $id;
  }
  
  function getBadName() {
    return $this->badName;
  }
  
  function setBadName($badName) {
    $this->badName = $badName;
  }
  
  function getFilter() {
    return $this->filter;
  }
  
  function setFilter($filter) {
    $this->filter = $filter;
  }
  
  function getKick() {
    return $this->kick;
  }
  
  function setKick($kick) {
    $this->kick = $kick;
  }
}
?>
