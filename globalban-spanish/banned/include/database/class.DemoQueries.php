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
	This class contains all queries that deal with the gban_ban table
******************************************************************************************************/

require_once(ROOTDIR."/include/database/class.Database.php");
require_once(ROOTDIR."/include/objects/class.Demo.php");
require_once(ROOTDIR."/config/class.Config.php");

class DemoQueries extends Config {

	// Variables
	var $db; // Object of database class
	var $endRange;

	// Default constructor
	function __construct() {
		$this->init();
  }

	function DemoQueries() {
		$this->init();
  }

  function init() {
    $this->db = new Database; // Create new database connection for this object
  }

  /************************************************************************
	Adds a new demo to the database and returns the ID of the new demo inserted
	************************************************************************/
  function addDemo($steamId, $demoName, $offenderName, $uploaderName, $uploaderSteamId, $reasonId, $serverId) {

    $insertId = -1;

    // Remove new line characters from steam id
    $steamId = str_replace(array("\r\n", "\n", "\r"), "", $steamId);

    // Only add the demo if it does not already exist
    if(!$this->doesDemoNameExist($demoName)) {
      // Add the demo
      $sql = "INSERT INTO gban_demo (steam_id, demo_name, offender_name, uploader_name, uploader_steam_id, reason_id, server_id, add_date)
      values('".addslashes($steamId)."', '".addslashes($demoName)."', '".addslashes($offenderName)."', '".addslashes($uploaderName)."', '".addslashes($uploaderSteamId)."', '".addslashes($reasonId)."', '".addslashes($serverId)."', NOW())";

      // Insert into db
      $this->db->sql_query($sql);
      
      $insertId = $this->db->get_insert_id();
      
      if($this->removePendingOnUpload) {
        // If it exists, flag the ban as no longer pending if the uploader is a member
        $this->db->sql_query("UPDATE gban_ban SET pending = '0' WHERE steam_id = '".addslashes($steamId)."'
                              AND 1 = (SELECT count(*) FROM gban_admin_steam gas WHERE gas.steam_id = '".addslashes($uploaderSteamId)."')");
      }
    }

    return $insertId;
  }
  
  /************************************************************************
	Deletes a demo from the database and on disk if it exists.
	************************************************************************/
  function deleteDemo($pathToDemo, $demoId) {
    $sql = "SELECT demo_name FROM gban_demo WHERE demo_id = '".$demoId."'";
    $this->db->sql_query($sql);
    $demoName = $this->db->get_row();
    $demoName = $demoName['demo_name'];
    
    $sql = "DELETE FROM gban_demo WHERE demo_id = '".$demoId."'";

    $this->db->sql_query($sql);
    
    // Delete the file on disk
    unlink($pathToDemo.$demoName);
    
    return $demoName;
  }

  /************************************************************************
	Determine if the demo name being added is already taken
	************************************************************************/
  function doesDemoNameExist($demoName) {
    $sql = "SELECT count(*) as count FROM gban_demo WHERE demo_name = '".addslashes($demoName)."'";
    $this->db->sql_query($sql);
    $count = $this->db->get_row();

    $count = $count['count'];

    if($count == 0) {
      return false;
    } else {
      return true;
    }
  }
  
  /************************************************************************
	Get the current list of demos to display on a page limited by the config
	value BansPerPage.
	************************************************************************/
  function getDemoList($startRange, $banCount, $sortBy, $sortDirection, $searchText) {
    $searchText = trim($searchText); // Remove whitespace from search text
    $searchJoin = "";
    
    // Prevent SQL injection
    $sortBy = addslashes($sortBy);
    $sortDirection = addslashes($sortDirection);
    $searchText = addslashes($searchText); // Prevent SQL Injection
    
    if($searchText != null && $searchText != "") {
      $searchJoin = " (d.steam_id LIKE '%".$searchText."%' OR d.demo_name LIKE '%".$searchText."%') ";
    }

    // Use LEFT JOIN so that bad reasons or serverids still display on the ban list, but as empty values
    // Get list of all demos that fit the criteria
    $demoList = "SELECT d.demo_id, d.steam_id, COALESCE(s.name, 'Bad Server ID') AS servername, d.server_id, d.add_date, r.reason,
    d.reason_id, d.demo_name, d.offender_name, d.uploader_name, d.uploader_steam_id,
    (SELECT count(1) FROM gban_ban b WHERE b.steam_id = d.steam_id and b.active = 1) as banned
    FROM gban_demo d
    LEFT JOIN gban_reason r ON d.reason_id = r.reason_id
    LEFT JOIN gban_servers s ON d.server_id = s.server_id ";
    if(!empty($searchJoin)) {
      $demoList .= " WHERE ".$searchJoin;
    }
    $demoList .= "ORDER BY $sortBy $sortDirection";

    if($this->bansPerPage > 0) {
      $this->endRange = $this->bansPerPage;
      $demoList .= " LIMIT ".$startRange.", ".$this->endRange;
    }

    $this->db->sql_query($demoList);
    $demosArray = $this->db->get_array();

    $demos = array();

    for($i=0; $i<count($demosArray); $i++) {
      $demo = new Demo();

      $demo->setDemoId($demosArray[$i]['demo_id']);
      $demo->setSteamId($demosArray[$i]['steam_id']);
      $demo->setDemoName($demosArray[$i]['demo_name']);
      $demo->setServer($demosArray[$i]['servername']);
      $demo->setServerId($demosArray[$i]['server_id']);
      $demo->setAddDate($demosArray[$i]['add_date']);
      $demo->setReason($demosArray[$i]['reason']);
      $demo->setReasonId($demosArray[$i]['reason_id']);
      $demo->setOffenderName($demosArray[$i]['offender_name']);
      $demo->setUploaderName($demosArray[$i]['uploader_name']);
      $demo->setUploaderSteamId($demosArray[$i]['uploader_steam_id']);
      $demo->setBanned($demosArray[$i]['banned']); // Can only be 0 or 1
      
      array_push($demos, $demo); // Add the demo object to the array
    }

    return $demos;
  }
  
  /************************************************************************
	Returns the total number of demos in the database.
	************************************************************************/
  function getNumberOfDemos($searchText) {
    $banCount = 0;

    $searchText = trim($searchText); // Remove whitespace from search text
    $searchText = addslashes($searchText); // Prevent SQL Injection
    
    $searchJoin = "";
    if($searchText != null && $searchText != "") {
      $searchJoin = " WHERE (steam_id LIKE '%".$searchText."%' OR demo_name LIKE '%".$searchText."%') ";
    }
    
    $banCountQuery = "SELECT count(*) as count FROM gban_demo ".$searchJoin;
    $this->db->sql_query($banCountQuery);
    $banCount = $this->db->get_row();
    $banCount = $banCount['count'];
      
    return $banCount;
  }
  
  /************************************************************************
	Used for demolist page
	************************************************************************/
  function getEndRange() {
    return $this->endRange;
  }
}
?>
