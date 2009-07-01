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
	This class is stores demo information into a Demo object.
******************************************************************************************************/

class Demo {

	// Variables
	var $demoId;
  var $steamId;
  var $demoName;
  var $offenderName;
  var $uploaderName;
  var $uploaderSteamId;
  var $serverId;
  var $server;
  var $reasonId;
  var $reason;
  var $addDate;
  var $banned;

	// Default Constructor (PHP 5)
	function __construct() {
		$this->init();
	}

	// Default Constructor (PHP 4)
	function User() {
		$this->init();
	}

	function init() {
  }

	/************************************************************************
	Accessor Methods
	************************************************************************/

	// Accessor Method - Returns the id stored in the user object
	function getDemoId() {
    return $this->demoId;
  }
	
	function getSteamId() {
		return $this->steamId;
	}
	
	function getDemoName() {
    return $this->demoName;
  }
  
  function getOffenderName() {
    return $this->offenderName;
  }
  
  function getUploaderName() {
    return $this->uploaderName;
  }
  
  function getUploaderSteamId() {
    return $this->uploaderSteamId;
  }
  
  function getServerId() {
    return $this->serverId;
  }
  
  function getServer() {
    return $this->server;
  }
  
  function getReasonId() {
    return $this->reasonId;
  }
  
  function getReason() {
    return $this->reason;
  }
  
  function getAddDate() {
    return $this->addDate;
  }
  
  function isBanned() {
    return $this->banned;
  }

	/************************************************************************
	Mutator Methods
	************************************************************************/
	
	// Mutator Method - Sets the id in the user object
	function setDemoId($demoId) {
    $this->demoId = $demoId;
  }

  function setSteamId($steamId) {
		$this->steamId = $steamId;
	}
	
	function setDemoName($demoName) {
    $this->demoName = $demoName;
  }
  
  function setOffenderName($offenderName) {
    $this->offenderName = $offenderName;
  }
  
  function setUploaderName($uploaderName) {
    $this->uploaderName = $uploaderName;
  }
  
  function setUploaderSteamId($uploaderSteamId) {
    $this->uploaderSteamId = $uploaderSteamId;
  }
  
  function setServerId($serverId) {
    $this->serverId = $serverId;
  }
  
  function setServer($server) {
    $this->server = $server;
  }
  
  function setReasonId($reasonId) {
    $this->reasonId = $reasonId;
  }
  
  function setReason($reason) {
    $this->reason = $reason;
  }
  
  function setAddDate($addDate) {
    $this->addDate = $addDate;
  }
  
  function setBanned($banned) {
    if($banned == 1) {
      $banned = true;
    } else {
      $banned = false;
    }
    $this->banned = $banned;
  }
}
?>
