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
	This class is stores user information into a User object.
******************************************************************************************************/
class User {
	
	// Variables
	var $name;
	var $password; // Encrypted password
	var $email;
	var $accessLevel; // User level of access
  var $id;
  var $steamId;
  var $active;
  var $adminGroupId;
  var $adminGroupName;
  var $flags;
	
	// Default Constructor (PHP 5)
	function __construct() {
		$this->init();
	}
	
	// Default Constructor (PHP 4)
	function User() {
		$this->init();
	}
	
	function init() {
    $this->name = "";
		$this->password = "";
		$this->email = "";
		$this->accessLevel = -1; // Default to a defunct level
		$this->id = 0;
		$this->steamId = "";
		$this->active = 1;
    $this->adminGroupId = 0;
  }
  
  /* This is the static comparing function: */
  function cmp_obj($a, $b) {
    if($a->getAccessLevel() == $b->getAccessLevel()) {
      return 0;
    } else if($a->getAccessLevel() < $b->getAccessLevel()) {
      return -1;
    } else {
      return 1;
    }
  }
	
	/************************************************************************
	Accessor Methods
	************************************************************************/
	// Accessor Method - Returns the name stored in the user object
	function getName() {
		return $this->name;
	}
		
	// Accessor Method - Returns the password stored in the user object
	function getPassword() {
		return $this->password;
	}
			
	// Accessor Method - Returns the level stored in the user object
	function getAccessLevel() {
		return $this->accessLevel;
	}
	
	// Accessor Method - Returns the email stored in the user object
	function getEmail() {
		return $this->email;
	}
	
	// Accessor Method - Returns the id stored in the user object
	function getId() {
		return $this->id;
	}
	
	// Accessor Method - Returns the id stored in the user object
	function getSteamId() {
		return $this->steamId;
	}
	
	function getActive() {
    return $this->active;
  }
  
  function getAdminGroupId() {
    return $this->adminGroupId;
  }
  
  function getAdminGroupName() {
    return $this->adminGroupName;
  }
  
  function getFlags() {
    return $this->flags;
  }
	
	/************************************************************************
	Mutator Methods
	************************************************************************/
	// Mutator Method - Sets the name in the user object
	function setName($name) {
		$this->name = $name;
	}
	
	// Mutator Method - Sets the password in the user object
	function setPassword($password) {
		$this->password = $password;
	}
	
	// Mutator Method - Sets the level in the user object
	function setAccessLevel($accessLevel) {
		$this->accessLevel = $accessLevel;
	}
	
	// Mutator Method - Sets the level in the user object
	function setEmail($email) {
		$this->email = $email;
	}
	
	// Mutator Method - Sets the id in the user object
	function setId($id) {
		$this->id = $id;
	}
	
	// Mutator Method - Sets the id in the user object
	function setSteamId($steamId) {
		$this->steamId = $steamId;
	}
	
	function setActive($active) {
    $this->active = $active;
  }
  
  function setAdminGroupId($adminGroupId) {
    $this->adminGroupId = $adminGroupId;
  }
  
  function setAdminGroupName($adminGroupName) {
    $this->adminGroupName = $adminGroupName;
  }
  
  function setFlags($flags) {
    $this->flags = $flags;
  }
}
?>
