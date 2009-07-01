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

/****************************************************************************************************** 
	This class is stores information about a banned ip
******************************************************************************************************/

class Ip {
	
	// Variables
	var $ip;
	var $active;
	
	// Default Constructor (PHP 5)
	function __construct() {
		$this->init();
	}
	
	// Default Constructor (PHP 4)
	function BannedUser() {
		$this->init();
	}
	
	function init() {
  }
	
	/************************************************************************
	Accessor Methods
	************************************************************************/
  function getIp() {
    return $this->ip;
  }
  
  function getActive() {
    return $this->active;
  }
	
	/************************************************************************
	Mutator Methods
	************************************************************************/
  function setIp($ip) {
    $this->ip = $ip;
  }
  
  function setActive($active) {
    $this->active = $active;
  }
}
?>
