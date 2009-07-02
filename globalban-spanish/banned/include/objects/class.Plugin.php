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

class Plugin {

  // Variables
  var $id;
  var $name;
  var $description;

  // Default constructor
	function __construct() {
		$this->init();
  }

  // Default constructor
	function Plugin() {
		$this->init();
  }

  function init() {
    $id = -1;
    $name = "";
    $description = "";
  }

  /**
   *  Getters and setters
   */
  function getId() {
    return $this->id;
  }

  function setId($id) {
    $this->id = $id;
  }

  function getName() {
    return $this->name;
  }

  function setName($name) {
    $this->name = $name;
  }

  function getDescription() {
    return $this->description;
  }

  function setDescription($description) {
    $this->description = $description;
  }
}
?>
