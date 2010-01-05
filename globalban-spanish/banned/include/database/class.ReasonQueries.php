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
	This class contains all queries that deal with the gban_reason table
******************************************************************************************************/

require_once(ROOTDIR."/include/database/class.Database.php"); //Database class
require_once(ROOTDIR."/include/objects/class.Reason.php");
require_once(ROOTDIR."/include/objects/class.Admin.php");

class ReasonQueries {	

	// Variables
	var $db; // Object of database class
	
	// Default constructor
	function __construct() {
		$this->init();
  }
  
	function ReasonQueries() {
		$this->init();
  }
  
  function init() {
    $this->db = new Database; // Create new database connection for this object
  }
  
  /************************************************************************
	Gets the list of reasons with their reason id
	************************************************************************/
  function getReasonList() {  
    $reasonQuery = "SELECT reason_id, reason FROM gban_reason ORDER BY reason_id";
    $this->db->sql_query($reasonQuery);    
    
    $reasonList = array();
    
    $reasons = $this->db->get_array();
    
    for($i=0; $i<count($reasons); $i++) {
      $reason = new Reason();
      
      $reason->setId($reasons[$i]['reason_id']);
      $reason->setReason($reasons[$i]['reason']);
      
      array_push($reasonList, $reason); // Add the reason object to the array
    }
    
    return $reasonList;
  }
  
  /************************************************************************
	Gets the admins list with their admin id
	************************************************************************/
  function getAdminsList() {  
    // $adminsQuery = "SELECT admin_id, name FROM gban_admins ORDER BY admin_id";
    $adminsQuery = "(SELECT admin_id, name FROM gban_admins ORDER BY admin_id) UNION (SELECT 0 admin_id, b.banner name FROM gban_ban b WHERE b.banner NOT IN (SELECT a.name banner FROM gban_admins a))";
    $this->db->sql_query($adminsQuery);    
    
    $adminsList = array();
    
    $admins = $this->db->get_array();
    
    for($i=0; $i<count($admins); $i++) {
      $admin = new Admin();
      
      $admin->setId($admins[$i]['admin_id']);
      $admin->setAdmin($admins[$i]['name']);
      
      array_push($adminsList, $admin); // Add the reason object to the array
    }
    
    return $adminsList;
  }

  /************************************************************************
	Add a new reason
	************************************************************************/
  function addReason($addReason) {
    $addReasonQuery = "INSERT INTO gban_reason (reason) 
    values('".addslashes($addReason)."')";
    $this->db->sql_query($addReasonQuery);
  
    return $this->db->get_insert_id();
  }
  
  /************************************************************************
	Delete an existing reason by id
	************************************************************************/
  function deleteReason($id) {
    $deleteReasonQuery = "DELETE FROM gban_reason WHERE reason_id = '".$id."'";
    $this->db->sql_query($deleteReasonQuery);
  }

  /************************************************************************
	Get an existing reason by id
	************************************************************************/
  function getReason($id) {
    $getReasonQuery = "SELECT reason FROM gban_reason WHERE reason_id = '".$id."'";
    $this->db->sql_query($getReasonQuery);
	
	$temp = $this->db->get_row();

    return $temp['reason'];

  }
  
  /************************************************************************
	Update an existing ban reason with new text
	************************************************************************/
  function updateBanReason($reason, $id) {
    $this->db->sql_query("UPDATE gban_reason 
                    SET reason='".addslashes($reason)."'
                    WHERE reason_id = '".$id."'");
  }
  
  /************************************************************************
	Get the first server that is listed in the server table
	************************************************************************/
  function getFirstReason() {
    $sql = "SELECT MIN( reason_id ) as min FROM `gban_reason`";
    $this->db->sql_query($sql);
    $id = $this->db->get_row();
    return $id['min'];
  }
}
?>
