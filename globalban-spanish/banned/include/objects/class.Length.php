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
class Length {
  var $id;
  var $length;
  var $timeScale;
  var $readable;
  
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
  
  function getLength() {
    return $this->length;
  }
  
  function setId($id) {
    $this->id = $id;
  }
  
  function setLength($length) {
    $this->length = $length;
  }
  
  function getTimeScale() {
    return $this->timeScale;
  }
  
  function setTimeScale($timeScale) {
    $this->timeScale = $timeScale;
  }
  
  function getReadable() {
    $this->readable = $this->length . " " . $this->timeScale;
    if($this->length == 1) {
      if($this->timeScale == "minutes")
        $this->readable = $this->length . " minuto";
      if($this->timeScale == "hours")
        $this->readable = $this->length . " hora";
      if($this->timeScale == "days")
        $this->readable = $this->length . " dia";
      if($this->timeScale == "weeks")
        $this->readable = $this->length . " semana";
      if($this->timeScale == "months")
        $this->readable = $this->length . " mes";
    }else {
      if($this->timeScale == "minutes")
        $this->readable = $this->length . " minutos";
      if($this->timeScale == "hours")
        $this->readable = $this->length . " horas";
      if($this->timeScale == "days")
        $this->readable = $this->length . " dias";
      if($this->timeScale == "weeks")
        $this->readable = $this->length . " semanas";
      if($this->timeScale == "months")
        $this->readable = $this->length . " meses";
    }
    if($this->length == 0) {
      $this->readable = "Permanente";
    }
    
    return $this->readable;
  }
  
  function getLengthInSeconds() {
    $lengthInSec = 0;
    // Convert the length to seconds
    if($this->timeScale == "minutes") {
      $lengthInSec = $this->length*60;
    } else if($this->timeScale == "hours") {
      $lengthInSec = $this->length*3600;
    } else if($this->timeScale == "days") {
      $lengthInSec = $this->length*3600*24;
    } else if($this->timeScale == "weeks") {
      $lengthInSec = $this->length*3600*24*7;
    } else if($this->timeScale == "months") {
      $lengthInSec = $this->length*3600*24*7*4; // 4 weeks a month
    }
    return $lengthInSec;
  }
}
?>
