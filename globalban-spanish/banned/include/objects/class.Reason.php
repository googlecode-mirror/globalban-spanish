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
class Reason{ 
  var $id;
  var $reason;
  
  function __construct() {
  }
  
	function Reason() {
	}
	
	function init() {
    $this->id = -1;
    $this->reason = "Breaking Server Rules";
  }
  
  function getId() {
    return $this->id;
  }
  
  function getReason() {
    return stripslashes($this->reason);
  }
  
  function setId($id) {
    $this->id = $id;
  }
  
  function setReason($reason) {
    $this->reason = $reason;
  }
}
?>
