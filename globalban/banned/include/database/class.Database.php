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

/* 
This Class is called on every page.  The database is opened and closed on every page.
This class will be evolved into an Abstraction layer.
*/

require_once(ROOTDIR."/config/class.Config.php");

class Database extends Config {
	var $dbhost; //Database Host
  var $dbuser; //Database User Name
  var $dbpass; //Database Password
  var $dbase; //Database
  var $sql_query; //SQL Query
  var $mysql_link; //Resource ID for Query
  var $sql_result; //SQL Query Result
	
  // Constructor that initializes the database abstraction layer
	function __construct() {
    $this->init();
  } 
  
  function Database() {
    $this->init();
  } 
  
  function init() {    
    $this->dbhost = $this->dbHostName; //Set the Database's host
    $this->dbuser = $this->dbUserName; //Set the Database's user name login
	  $this->dbpass = $this->dbPassword; //Set the Database's password login
	  $this->dbase = $this->dbName; //Set the Database to access
	  $this->mysql_link = '0';
    $this->sql_result = '';
    $this->connection(); //Connect to database now
  }
	
	// This function connects to the database
	function connection() {
    $this->mysql_link = mysql_select_db($this->dbase, mysql_connect($this->dbhost, $this->dbuser, $this->dbpass));
		if (!$this->mysql_link) { //Error Detection
		   die('Could not connect: ' . mysql_error());
		}
	}
			
	// This function is fed the mySQL query that is to be evaluated
	function sql_query($sql) {
		$this->sql_result = mysql_query($sql);
	} 
	
	// Get the number of rows in the sql query
	function num_rows() {
    $mysql_rows = mysql_num_rows( $this->sql_result );
    return $mysql_rows;
  }

	// Stores the entire mySQL query result into an array
  function get_array() {
		$i=0;
		$rowset = array();
		while ($row = $this->get_row() ) {
			$rowset[$i] = $row;		
			$i++;
		}
		return $rowset;
	}
	
	// Just get a single row
  function get_row() {
		$row = @mysql_fetch_array($this->sql_result);
		return $row;
	}
	
	// Get the last auto-increment number
	function get_insert_id() {
		return mysql_insert_id();
	}
	
	/************************************************************************
	Determine if the database exists
	************************************************************************/
  function databaseExists() {
    $returnVal = false;
    $this->sql_result = mysql_list_dbs(mysql_connect($this->dbhost, $this->dbuser, $this->dbpass));
    while($row=get_row()) {
      // We found the database!
      if($row[0] == $this->dbase) {
        $returnVal = true;
      }
    }
    return $returnVal;
  }
}
?>
