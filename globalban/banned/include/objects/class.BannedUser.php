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

require_once(ROOTDIR."/include/database/class.BadNameQueries.php");
/****************************************************************************************************** 
	This class is stores information about a banned user
******************************************************************************************************/

class BannedUser {
	
	// Variables
	var $banId;
	var $steamId;
	var $ip;
	var $length;
	var $timeScale;
	var $reasonId;
	var $reason;
	var $serverId;
	var $server;
	var $webpage;
	var $name;
	var $banner;
	var $bannerSteamId; // Steam ID of banner
	var $modifiedBy;
	var $addDate;
	var $expireDate;
	var $expireDateSeconds;
	var $active;
	var $pending;
	var $demoCount;
	var $comments;
	var $offenses;
    var $kickcounter;
	


	var $badNameQueries;
	
	// Default Constructor (PHP 5)
	function __construct() {
		$this->init();
	}
	
	// Default Constructor (PHP 4)
	function BannedUser() {
		$this->init();
	}
	
	function init() {
    $this->badNameQueries = new BadNameQueries;
  }
	
	/************************************************************************
	Accessor Methods
	************************************************************************/
	function getBanId() {
    return $this->banId;
  }
  
	function getSteamId() {
    return $this->steamId;
  }
  
  function getIp() {
    return $this->ip;
  }
  
  function getLength() {
    return $this->length;
  }
  
  function getTimeScale() {
    return $this->timeScale;
  }
  
  function getReasonId() {
    return $this->reasonId;
  }
  
  function getReason() {
    return $this->reason;
  }
  
  function getServerId() {
    return $this->serverId;
  }
  
  function getServer() {
    return $this->server;
  }
  
  function getWebpage() {
    return $this->webpage;
  }
  
	function getName() {
    $namesToFilter = $this->badNameQueries->getFilterNames();
    // Loop through the names to kick to see if the word exists in the user's name
    foreach($namesToFilter as $nameToFilter) {
      $stars = "";
      for($i=0; $i<strlen($nameToFilter->getBadName()); $i++) {
        $stars .= "*";
      }
      $this->name = str_replace($nameToFilter->getBadName(), $stars, strtolower($this->name));
    }
		return $this->name;
	}
	
	function getBanner() {
    return $this->banner;
  }
  
  function getBannerSteamId() {
    return $this->bannerSteamId;
  }
  
  function getModifiedBy() {
    return $this->modifiedBy;
  }
  
  function getAddDate() {
    return $this->addDate;
  }
  
  function getExpireDate() {
    return $this->expireDate;
  }
  
  function getExpireDateSeconds() {
    return $this->expireDateSeconds;
  }
  
  function getActive() {
    return $this->active;
  }
  
  function getPending() {
    return $this->pending;
  }
  
  function getDemoCount() {
    return $this->demoCount;
  }
	
	function getComments() {
    return $this->comments;
  }
  
  function getOffenses() {
    return $this->offenses;
  }

  function getKickCounter() {
    return $this->kickcounter;
  }
	/************************************************************************
	Mutator Methods
	************************************************************************/
	function setBanId($banId) {
    $this->banId = $banId;
  }
	
	function setSteamId($steamId) {
    $this->steamId = $steamId;
  }
  
  function setIp($ip) {
    $this->ip = $ip;
  }
  
  function setLength($length) {
    $this->length = $length;
  }
  
  function setTimeScale($timeScale) {
    $this->timeScale = $timeScale;
  }
  
  function setReasonId($reasonId) {
    $this->reasonId = $reasonId;
  }
  
  function setReason($reason) {
    $this->reason = $reason;
  }
  
  function setServerId($serverId) {
    $this->serverId = $serverId;
  }
  
  function setServer($server) {
    $this->server = $server;
  }
  
  function setWebpage($webpage) {
    $this->webpage = $webpage;
  }
  
	function setName($name) {
		$this->name = $name;
	}
	
	function setBanner($banner) {
    $this->banner = $banner;
  }
  
  function setBannerSteamId($bannerSteamId) {
    $this->bannerSteamId = $bannerSteamId;
  }
  
  function setModifiedBy($modifiedBy) {
    $this->modifiedBy = $modifiedBy;
  }
  
  function setAddDate($addDate) {
    $this->addDate = $addDate;
  }
  
  function setExpireDate($expireDate) {
    $this->expireDate = $expireDate;
  }
  
  function setExpireDateSeconds($expireDateSeconds) {
    $this->expireDateSeconds = $expireDateSeconds;
  }
  
  function setActive($active) {
    $this->active = $active;
  }
  
  function setPending($pending) {
    $this->pending = $pending;
  }
  
  function setDemoCount($demoCount) {
    $this->demoCount = $demoCount;
  }
  
  function setComments($comments) {
    $this->comments = $comments;
  }
  
  function setOffenses($offenses) {
    $this->offenses = $offenses;
  }
  function setKickCounter($kickcounter) {
    $this->kickcounter = $kickcounter;
  }
}
?>
