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
 *  This class contains all information that pertains to a server.
 */

class PluginFlag {

  // Variables
  var $adminGroupId;
  var $pluginFlagId;
  var $enabled;
  var $flag;
  var $description;

  // Default constructor
	function __construct() {
		$this->init();
  }

  // Default constructor
	function PluginFlag() {
		$this->init();
  }

  function init() {
    $adminGroupId = -1;
    $pluginFlagId = -1;
    $enabled = 0;
    $flag = "";
    $description = "";
  }

  /**
   *  Getters and setters
   */
  function getAdminGroupId() {
    return $this->adminGroupId;
  }

  function setAdminGroupId($adminGroupId) {
    $this->adminGroupId = $adminGroupId;
  }

  function getPluginFlagId() {
    return $this->pluginFlagId;
  }

  function setPluginFlagId($pluginFlagId) {
    $this->pluginFlagId = $pluginFlagId;
  }

  function getEnabled() {
    return $this->enabled;
  }
  
  function isEnabled() {
    if($this->enabled == 1) {
      return true;
    } else {
      return false;
    }
  }

  function setEnabled($enabled) {
    $this->enabled = $enabled;
  }
  
  function getFlag() {
    return $this->flag;
  }
  
  function setFlag($flag) {
    $this->flag = $flag;
  }
  
  function getDescription() {
    return $this->description;
  }
  
  function setDescription($description) {
    $this->description = $description;
  }
}
?>
