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
	This class contains all queries that deal with the gban_ban table and the gban_ip table
******************************************************************************************************/

require_once(ROOTDIR."/include/database/class.Database.php");
require_once(ROOTDIR."/include/objects/class.BadName.php");
require_once(ROOTDIR."/config/class.Config.php");

class BadNameQueries extends Config {

	// Variables
	var $db; // Object of database class

	// Default constructor
	function __construct() {
		$this->init();
  }

	function BadNameQueries() {
		$this->init();
  }

  function init() {
    $this->db = new Database; // Create new database connection for this object
  }

  /************************************************************************
	Adds a new bad name/word to the database
	************************************************************************/
  function addBadName($badName, $filter, $kick) {

    $badName = trim($badName);

    if(!empty($badName)) {

      // Force to all lowercase
      $badName = strtolower($badName);

      // Add the bad name
      $sql = "INSERT INTO gban_bad_names (bad_name, filter, kick)
              values('".addslashes($badName)."', '".addslashes($filter)."', '".addslashes($kick)."')";
              
      // Insert into db
      $this->db->sql_query($sql);
    }
  }
  
  /************************************************************************
	Remove a bad name/word from the database
	************************************************************************/
  function removeBadName($id) {
    if(!empty($id)) {

      // Force to all lowercase
      $badName = strtolower($badName);

      // Add the bad name
      $sql = "DELETE FROM gban_bad_names WHERE bad_name_id = ".addslashes($id)."";

      // Delete from DB
      $this->db->sql_query($sql);
    }
  }
  
  /************************************************************************
	Get all the bad names for display on the bad names page
	************************************************************************/
  function getBadNames() {
    $sql = "SELECT bad_name_id, bad_name, filter, kick
            FROM gban_bad_names
            ORDER BY bad_name ASC";
            
    $this->db->sql_query($sql);
    
    $badNamesArray = $this->db->get_array();
    
    $badNames = array();

    for($i=0; $i<count($badNamesArray); $i++) {
      $badName = new BadName();

      $badName->setId($badNamesArray[$i]['bad_name_id']);
      $badName->setBadName(stripslashes($badNamesArray[$i]['bad_name']));
      $badName->setFilter($badNamesArray[$i]['filter']);
      $badName->setKick($badNamesArray[$i]['kick']);

      array_push($badNames, $badName);
    }

    return $badNames;
  }
  
  /************************************************************************
  Get all the names/words that should be filtered on the ban list page
	************************************************************************/
  function getFilterNames() {
    $sql = "SELECT bad_name_id, bad_name, filter
            FROM gban_bad_names
            WHERE filter = 1";

    $this->db->sql_query($sql);

    $badNamesArray = $this->db->get_array();

    $badNames = array();

    for($i=0; $i<count($badNamesArray); $i++) {
      $badName = new BadName();

      $badName->setId($badNamesArray[$i]['bad_name_id']);
      $badName->setBadName(stripslashes($badNamesArray[$i]['bad_name']));
      $badName->setFilter($badNamesArray[$i]['filter']);
      $badName->setKick($badNamesArray[$i]['kick']);

      array_push($badNames, $badName);
    }

    return $badNames;
  }
  
  /************************************************************************
	Get all of the names that should be kicked
	************************************************************************/
  function getKickNames() {
    $sql = "SELECT bad_name_id, bad_name, kick
            FROM gban_bad_names
            WHERE kick = 1";

    $this->db->sql_query($sql);

    $badNamesArray = $this->db->get_array();

    $badNames = array();

    for($i=0; $i<count($badNamesArray); $i++) {
      $badName = new BadName();

      $badName->setId($badNamesArray[$i]['bad_name_id']);
      $badName->setBadName(strtolower(stripslashes($badNamesArray[$i]['bad_name'])));
      $badName->setFilter($badNamesArray[$i]['filter']);
      $badName->setKick($badNamesArray[$i]['kick']);

      array_push($badNames, $badName);
    }

    return $badNames;
  }
  
  /************************************************************************
	Change the bad name filter from on to off or vise versa
	************************************************************************/
  function updateBadNameFilter($id, $filter) {
    $this->db->sql_query("UPDATE gban_bad_names SET filter = '".addslashes($filter)."' WHERE bad_name_id = '".addslashes($id)."'");
  }
  
  /************************************************************************
	Change the bad name kick from on to off or vise versa
	************************************************************************/
  function updateBadNameKick($id, $kick) {
    $this->db->sql_query("UPDATE gban_bad_names SET kick = '".addslashes($kick)."' WHERE bad_name_id = '".addslashes($id)."'");
  }
}
?>
