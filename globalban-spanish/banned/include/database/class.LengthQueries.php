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
require_once(ROOTDIR."/include/objects/class.Length.php");

class LengthQueries {

	// Variables
	var $db; // Object of database class

	// Default constructor
	function __construct() {
		$this->init();
  }

	function LengthQueries() {
		$this->init();
  }

  function init() {
    $this->db = new Database; // Create new database connection for this object
  }

  /************************************************************************
	Gets the list of ban lengths
	************************************************************************/
  function getLengthList() {
    $query = "SELECT length_id, length, time_scale FROM gban_length ORDER BY length";
    $this->db->sql_query($query);

    $lengthList = array();
    $minutesList = array();
    $hoursList = array();
    $daysList = array();
    $weeksList = array();
    $monthsList = array();

    $lengths = $this->db->get_array();

    for($i=0; $i<count($lengths); $i++) {
      $length = new Length();

      $length->setId($lengths[$i]['length_id']);
      $length->setLength($lengths[$i]['length']);
      $length->setTimeScale($lengths[$i]['time_scale']);

      if($length->getTimeScale() == "minutes") {
        array_push($minutesList, $length);
      } else if($length->getTimeScale() == "hours") {
        array_push($hoursList, $length);
      } else if($length->getTimeScale() == "days") {
        array_push($daysList, $length);
      } else if($length->getTimeScale() == "weeks") {
        array_push($weeksList, $length);
      } else if($length->getTimeScale() == "months") {
        array_push($monthsList, $length);
      }
    }
    
    // Order the length list with smallest to largest
    $lengthList = $this->combineLengthLists($lengthList, $minutesList);
    $lengthList = $this->combineLengthLists($lengthList, $hoursList);
    $lengthList = $this->combineLengthLists($lengthList, $daysList);
    $lengthList = $this->combineLengthLists($lengthList, $weeksList);
    $lengthList = $this->combineLengthLists($lengthList, $monthsList);

    return $lengthList;
  }
  
  function combineLengthLists($lengthList, $array) {
    for($i=0; $i<count($array); $i++) {
      array_push($lengthList, $array[$i]);
    }
    
    return $lengthList;
  }
  
  /************************************************************************
	Get the ban length object by id
	************************************************************************/
  function getBanLength($id) {
    $query = "SELECT length_id, length, time_scale FROM gban_length
              WHERE length_id = '".$id."'";
    $this->db->sql_query($query);
    
    $temp = $this->db->get_row();

    $length = new Length();

    $length->setId($temp['length_id']);
    $length->setLength($temp['length']);
    $length->setTimeScale($temp['time_scale']);

    return $length;
  }

  /************************************************************************
	Add a new ban length
	************************************************************************/
  function addLength($length, $timeScale) {
    if(!$this->doesBanLengthExist($length, $timeScale)) {
      $addLengthQuery = "INSERT INTO gban_length (length, time_scale)
      values('".addslashes($length)."', '".addslashes($timeScale)."')";
      $this->db->sql_query($addLengthQuery);

      return $this->db->get_insert_id();
    } else {
      return -1;
    }
  }

  /************************************************************************
	Delete an existing ban length by id
	************************************************************************/
  function deleteLength($id) {
    $deleteReasonQuery = "DELETE FROM gban_length WHERE length_id = '".$id."'";
    $this->db->sql_query($deleteReasonQuery);
  }

  /************************************************************************
	Update an existing ban length, however the time scale can not be changed
	************************************************************************/
  function updateBanLength($id, $length) {
    $this->db->sql_query("UPDATE gban_length
                    SET legnth='".addslashes($length)."'
                    WHERE length_id = '".$id."'");
  }

  /************************************************************************
	Determines if a length/timescale combination already exists in the length
	table.
	************************************************************************/
  function doesBanLengthExist($length, $timeScale) {
    if($length != 0) {
      $query = "SELECT * FROM gban_length WHERE length = '".addslashes($length)."'
                AND time_scale = '".addslashes($timeScale)."'";
    } else {
      // Only allow 1 zero
      $query = "SELECT * FROM gban_length WHERE length = '".addslashes($length)."'";
    }
    
    $this->db->sql_query($query);
    $count = $this->db->get_row();
    
    if($count == 0) {
      return false;
    } else {
      return true;
    }
  }
}
?>
