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
	This class contains all the queries needed for a full install and incremental upgrades.
	This class is very similiar to the Database class, but it needs to be slightly different.
******************************************************************************************************/

class InstallAndUpgradeQueries {
  var $dbhost; //Database Host
  var $dbuser; //Database User Name
  var $dbpass; //Database Password
  var $dbase; //Database
  var $sql_query; //SQL Query
  var $mysql_link; //Resource ID for Query
  var $sql_result; //SQL Query Result
  var $connection;

  // Constructor that initializes the database abstraction layer
	function __construct() {
  }

  function InstallAndUpgradeQueries() {
  }

  function setDbHost($dbHost) {
    $this->dbhost = $dbHost;
  }

  function getDbHost() {
    return $this->dbhost;
  }

  function setDbUser($dbUser) {
    $this->dbuser = $dbUser;
  }

  function getDbUser() {
    return $this->dbuser;
  }

  function setDbPass($dbPass) {
    $this->dbpass = $dbPass;
  }

  function getDbPass() {
    return $this->dbpass;
  }

  function setDbase($dbase) {
    $this->dbase = $dbase;
  }

  function getDbase() {
    return $this->dbase;
  }

	// This function connects to the database
	// Create connection must be called before this
	function connectToDatabase() {
    $this->mysql_link = mysql_select_db($this->dbase, $this->connection);
		if (!$this->mysql_link) { //Error Detection
		   die('Could not connect: ' . mysql_error());
		}
  }

	// This creates a connection
	function createConnection() {
    $this->connection = mysql_connect($this->dbhost, $this->dbuser, $this->dbpass);
  }

	function testConnection() {
    // The @ sign tells php to hide any errors that mysql returns
    @mysql_connect($this->dbhost, $this->dbuser, $this->dbpass) or die("<h1>The database information entered is incorrect.</h1><br/>Error: " . mysql_error()."<br/>Please go back and input the correct information.");
    return true;
  }

	// This function is fed the mySQL query that is to be evaluated
	function sql_query($sql) {
		$this->sql_result = mysql_query($sql);
	}

	// Get the number of rows in the sql query
	function num_rows() {
    $mysql_rows = @mysql_num_rows( $this->sql_result );
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
    while($row = $this->get_row()) {
      // We found the database!
      if($row[0] == $this->dbase) {
        $returnVal = true;
      }
    }
    return $returnVal;
  }

  /************************************************************************
  Get a list of all databases the user has access to.
	************************************************************************/
  function getListOfDatabases() {
    $this->sql_result = mysql_list_dbs(mysql_connect($this->dbhost, $this->dbuser, $this->dbpass));
    $array = Array();
    $i=0;
    while($row = $this->get_row()) {
      if($row[0] != "information_schema" && $row[0] != "mysql") {
        $array[$i] = $row[0];
        $i++;
      }
    }
    return $array;
  }

  // createConnection must be called before this!
  function createDatabase() {
    $sql = "CREATE DATABASE `".$this->dbase."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";

    $this->sql_query($sql);
  }

  /************************************************************************
  Full install of all tables
	************************************************************************/
  function fullInstall() {
    $sql = "CREATE TABLE `gban_admin_steam` (
              `admin_id` int(10) NOT NULL,
              `steam_id` varchar(255) NOT NULL,
              `active` TINYINT(1) NOT NULL DEFAULT '1',
              PRIMARY KEY  (`admin_id`),
              UNIQUE KEY `steam_id` (`steam_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_admins` (
              `admin_id` int(10) NOT NULL auto_increment,
              `name` varchar(255) NOT NULL,
              `access_level` mediumint(3) NOT NULL default '1',
              `password` varchar(255) NOT NULL,
              `email` varchar(255) default NULL,
              PRIMARY KEY  (`admin_id`),
              UNIQUE KEY `name` (`name`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_ban` (
              `ban_id` int(10) NOT NULL auto_increment,
              `steam_id` varchar(255) NOT NULL,
              `ip` varchar(15) default NULL,
              `name` varchar(255) default NULL,
              `length` int(10) NOT NULL default '0',
              `time_scale` VARCHAR( 12 ) NOT NULL DEFAULT 'hours',
              `add_date` datetime NOT NULL,
              `kick_counter` int(10) NOT NULL default '0',
              `expire_date` datetime NOT NULL,
              `reason_id` int(10) NOT NULL default '1',
              `banner` varchar(255) NOT NULL,
              `banner_steam_id` varchar(255) default NULL,
              `active` tinyint(1) NOT NULL default '1',
              `pending` tinyint(1) NOT NULL default '0',
              `server_id` int(10) NOT NULL default '1',
              `webpage` varchar(255) NULL,
              `modified_by` varchar(255) NOT NULL,
              `comments` TEXT NULL,
              PRIMARY KEY  (`ban_id`),
              UNIQUE KEY `steam_id` (`steam_id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_demo` (
              `demo_id` int(10) NOT NULL auto_increment,
              `steam_id` varchar(255) NOT NULL,
              `demo_name` varchar(255) NOT NULL,
              `offender_name` varchar(255) NOT NULL,
              `uploader_name` varchar(255) default NULL,
              `uploader_steam_id` varchar(255) default NULL,
              `server_id` int(10) NOT NULL,
              `reason_id` int(10) NOT NULL,
              `add_date` date NOT NULL,
              `banned` tinyint(1) NOT NULL default '0',
              PRIMARY KEY  (`demo_id`),
              UNIQUE KEY `demo_name` (`demo_name`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_ip` (
              `ip` varchar(15) NOT NULL,
              `active` tinyint(1) NOT NULL default '1',
              PRIMARY KEY  (`ip`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_reason` (
              `reason_id` int(10) NOT NULL auto_increment,
              `reason` varchar(255) NOT NULL,
              PRIMARY KEY  (`reason_id`),
              UNIQUE KEY `reason` (`reason`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_servers` (
              `server_id` int(10) NOT NULL auto_increment,
              `name` varchar(255) NOT NULL,
              `ip` varchar(255) NOT NULL,
              `port` int(6) NOT NULL default '27015',
              `rcon` varchar(40) NOT NULL,
              `type` varchar(40) NOT NULL default 'cstrike',
              `plugin` VARCHAR(50) NULL,
              `server_group_id` INT( 11 ) NULL,
              PRIMARY KEY  (`server_id`),
              UNIQUE KEY `ip` (`ip`,`port`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_ban_history` (
              `ban_id` int(10) NOT NULL,
              `steam_id` varchar(255) NOT NULL,
              `ip` varchar(15) default NULL,
              `name` varchar(255) default NULL,
              `length` int(10) NOT NULL default '0',
              `time_scale` VARCHAR( 12 ) NOT NULL DEFAULT 'hours',
              `add_date` datetime NOT NULL,
              `kick_counter` int(10) NOT NULL default '0',
              `expire_date` datetime NOT NULL,
              `reason_id` int(10) NOT NULL default '1',
              `banner` varchar(255) NOT NULL,
              `banner_steam_id` varchar(255) default NULL,
              `active` tinyint(1) NOT NULL default '1',
              `pending` tinyint(1) NOT NULL default '0',
              `server_id` int(10) NOT NULL default '1',
              `webpage` varchar(255) default NULL,
              `modified_by` varchar(255) NOT NULL,
              `comments` text,
              `offenses` int(3) NOT NULL default '1',
              UNIQUE KEY `ban_id` (`ban_id`),
              KEY `steam_id` (`steam_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_length` (
            `length_id` int(10) NOT NULL auto_increment,
            `length` int(10) NOT NULL,
            `time_scale` varchar(12) NOT NULL default 'hours',
            PRIMARY KEY  (`length_id`)
          ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_bad_names` (
              `bad_name_id` int(10) NOT NULL auto_increment,
              `bad_name` varchar(45) NOT NULL,
              `filter` tinyint(1) NOT NULL default '0',
              `kick` varchar(1) NOT NULL default '0',
              PRIMARY KEY  (`bad_name_id`),
              UNIQUE KEY `bad_name` (`bad_name`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_server_admin` (
            `server_id` INT( 11 ) NOT NULL,
            `admin_id` INT( 11 ) NOT NULL,
            `admin_group_id` INT( 11 ) NOT NULL,
            PRIMARY KEY ( `server_id` , `admin_id` )
            ) ENGINE = MYISAM ;";

    $this->sql_query($sql);

    // Poor table name... stuck with it...
    $sql = "CREATE TABLE `gban_group_admin` (
            `server_group_id` INT( 11 ) NOT NULL ,
            `admin_id` INT( 11 ) NOT NULL ,
            `admin_group_id` INT( 11 ) NOT NULL,
            PRIMARY KEY ( `server_group_id` , `admin_id` )
            ) ENGINE = MYISAM ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_plugin` (
              `plugin` VARCHAR( 25 ) NOT NULL ,
              `name` VARCHAR( 128 ) NOT NULL ,
              `description` VARCHAR( 255 ) NOT NULL ,
              PRIMARY KEY ( `plugin` )
            ) ENGINE = MYISAM ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_plugin_flag` (
              `plugin_flag_id` int(11) NOT NULL auto_increment,
              `plugin` varchar(50) NOT NULL,
              `flag` varchar(25) NOT NULL,
              `description` varchar(255) default NULL,
              PRIMARY KEY  (`plugin_flag_id`),
              KEY `plugin` (`plugin`,`flag`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_server_group` (
              `server_group_id` int(11) NOT NULL auto_increment,
              `group_name` varchar(255) NOT NULL,
              `description` varchar(255) NOT NULL,
              `sm_immunity` INT( 3 ) NOT NULL DEFAULT '0',
              PRIMARY KEY  (`server_group_id`),
              UNIQUE KEY `group_name` (`group_name`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    $this->sql_query($sql);

    $this->connectToDatabase();

    $sql = "INSERT INTO `gban_reason` (`reason_id`, `reason`) VALUES
            (1, 'Breaking Server Rules'),
            (2, 'Aimbot'),
            (3, 'Wallhacking'),
            (4, 'Aimbot and Wallhacking'),
            (5, 'No Recoil'),
            (6, 'Speed Hacking'),
            (7, 'Bad Behaviour (language/attitude/rude)'),
            (8, 'Mic Spamming'),
            (9, 'Nade Spamming'),
            (10, 'Offensive Spray'),
            (11, 'Advertising other servers'),
            (12, 'Clan recruitment'),
            (13, 'Ghosting'),
            (14, 'Not doing objective (Camping)'),
            (15, 'Imported Ban');";

    $this->sql_query($sql);

    $sql = "INSERT INTO `gban_length` (`length_id`, `length`, `time_scale`) VALUES
            (1, 0, 'minutes'),
            (2, 5, 'minutes'),
            (3, 15, 'minutes'),
            (4, 30, 'minutes'),
            (5, 1, 'hours'),
            (6, 3, 'hours'),
            (7, 6, 'hours'),
            (8, 12, 'hours'),
            (9, 1, 'days'),
            (10, 3, 'days'),
            (11, 1, 'weeks'),
            (12, 2, 'weeks'),
            (13, 1, 'months'),
            (14, 3, 'months'),
            (15, 6, 'months');";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_plugin_flag` (
            `plugin_flag_id` int(11) NOT NULL auto_increment,
            `plugin` varchar(50) NOT NULL,
            `flag` varchar(25) NOT NULL,
            `description` varchar(255) default NULL,
            PRIMARY KEY  (`plugin_flag_id`),
            KEY `plugin` (`plugin`,`flag`)
          ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_admin_group` (
              `admin_group_id` int(11) NOT NULL auto_increment,
              `group_name` varchar(255) NOT NULL,
              `description` varchar(255) NOT NULL,
              `sm_immunity` INT( 3 ) NOT NULL DEFAULT '0',
              PRIMARY KEY  (`admin_group_id`),
              UNIQUE KEY `group_name` (`group_name`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_admin_group_flag` (
            `admin_group_id` INT( 11 ) NOT NULL ,
            `plugin_flag_id` INT( 11 ) NOT NULL ,
            `enabled` TINYINT( 1 ) NOT NULL DEFAULT '0',
            PRIMARY KEY ( `admin_group_id` , `plugin_flag_id` )
            ) ENGINE = MYISAM ;";

    $this->sql_query($sql);

    $this->sql_query($sql);

    $sql = "-- Plugin Default Data
            INSERT INTO `gban_plugin` (`plugin`, `name`, `description`) VALUES
            ('gban', 'GlobalBan Powers', 'These are the powers that are specific to GlobalBan.'),
            ('mani', 'Mani Admin Plugin', 'These are the powers for the Mani Admin Plugin.'),
            ('sourcemod', 'SourceMOD Plugin', 'These are the powers for the SourceMOD plugin.');";
            
    $this->sql_query($sql);
    
    $sql = "-- GlobalBan flags
            INSERT INTO `gban_plugin_flag` (`plugin`, `flag`, `description`) VALUES
            ('gban', 'b', 'Ban Manager'),
            ('gban', 'a', 'Admin'),
            ('gban', 'm', 'Member');";
            
    $this->sql_query($sql);
    
    $sql = "-- Mani Flags
            INSERT INTO `gban_plugin_flag` (`plugin`, `flag`, `description`) VALUES
            ('mani', 'j', 'Gimp'),
            ('mani', 'k', 'Kick'),
            ('mani', 'r', 'Rcon'),
            ('mani', 'q', 'Rcon Menu Level 1'),
            ('mani', 'e', 'Explode'),
            ('mani', 'm', 'Slay'),
            ('mani', 'b', 'Non Permanent Ban'),
            ('mani', 's', 'Admin Say'),
            ('mani', 'o', 'Admin Chat'),
            ('mani', 'a', 'Private Admin Chat'),
            ('mani', 'c', 'Map Change'),
            ('mani', 'p', 'Sound'),
            ('mani', 'w', 'Weapon Restrictions'),
            ('mani', 'z', 'Config'),
            ('mani', 'x', 'Client Execution'),
            ('mani', 'y', 'Client Execution Menu'),
            ('mani', 'i', 'Blind'),
            ('mani', 'l', 'Slap'),
            ('mani', 'f', 'Freeze'),
            ('mani', 't', 'Teleport'),
            ('mani', 'd', 'Drug'),
            ('mani', 'g', 'Swap Player'),
            ('mani', 'R', 'Rcon Voting'),
            ('mani', 'B', 'Rcon Voting Menu'),
            ('mani', 'v', 'Random Map Vote'),
            ('mani', 'V', 'Map Vote'),
            ('mani', 'Q', 'Console Question Vote'),
            ('mani', 'D', 'Menu Question Vote'),
            ('mani', 'C', 'Cancel Vote'),
            ('mani', 'A', 'Accept Vote'),
            ('mani', 'E', 'View Client Rates'),
            ('mani', 'F', 'Burn'),
            ('mani', 'G', 'No Clip Mode'),
            ('mani', 'H', 'War Mode'),
            ('mani', 'I', 'Mute'),
            ('mani', 'J', 'Reset stats'),
            ('mani', 'K', 'Cash'),
            ('mani', 'L', 'Rcon Say'),
            ('mani', 'M', 'Admin Skins'),
            ('mani', 'N', 'Set Skins'),
            ('mani', 'O', 'Drop C4'),
            ('mani', 'P', 'Set Client Flags'),
            ('mani', 'S', 'Set Skin Color'),
            ('mani', 'T', 'Time Bomb'),
            ('mani', 'U', 'Fire Bomb'),
            ('mani', 'W', 'Freeze Bomb'),
            ('mani', 'X', 'Adjust Health'),
            ('mani', 'Y', 'Beacon Player'),
            ('mani', 'Z', 'Give Player Item'),
            ('mani', 'admin', 'Basic Admin'),
            ('mani', 'client', 'Create Clients and Set Powers'),
            ('mani', 'pban', 'Permanent Ban'),
            ('mani', 'spray', 'Spray Tag Tracking'),
            ('mani', 'grav', 'Adjust Player Gravity'),
            ('mani', 'q2', 'Rcon Menu Level 2'),
            ('mani', 'reserved', 'Reserved Slot');";
            
      $this->sql_query($sql);
      $sql = "-- Sourcemod flags
            INSERT INTO `gban_plugin_flag` (`plugin`, `flag`, `description`) VALUES
            ('sourcemod', 'a', 'Reserved Slot'),
            ('sourcemod', 'b', 'Generic Admin (required for admins)'),
            ('sourcemod', 'c', 'Kick'),
            ('sourcemod', 'd', 'Ban'),
            ('sourcemod', 'e', 'Unban'),
            ('sourcemod', 'f', 'Slay'),
            ('sourcemod', 'g', 'Map Changing'),
            ('sourcemod', 'h', 'Modify cvars'),
            ('sourcemod', 'i', 'Modify Configs'),
            ('sourcemod', 'j', 'Special Chat Privileges'),
            ('sourcemod', 'k', 'Voting'),
            ('sourcemod', 'l', 'Password the server'),
            ('sourcemod', 'm', 'Remote Console'),
            ('sourcemod', 'n', 'Change sv_cheats and related commands'),
            ('sourcemod', 'z', 'Grant all permissions (ignores immunities)');";

      $this->sql_query($sql);
      
      $sql = "INSERT INTO `gban_plugin` (`plugin`, `name`, `description`) VALUES
            ('mani-immunity', 'Mani Admin Plugin Immunities', 'These are the immunities for the Mani Admin Plugin.');";
            
      $this->sql_query($sql);
      
      $sql = "INSERT INTO `gban_plugin_flag` (`plugin`, `flag`, `description`) VALUES
            ('mani-immunity', 'j', 'Gimp Immunity'),
            ('mani-immunity', 'k', 'Kick Immunity'),
            ('mani-immunity', 'm', 'Slay Immunity'),
            ('mani-immunity', 'b', 'Ban Immunity'),
            ('mani-immunity', 'x', 'Client Execution Immunity'),
            ('mani-immunity', 'i', 'Blind Immunity'),
            ('mani-immunity', 'l', 'Slap Immunity'),
            ('mani-immunity', 'f', 'Freeze Immunity'),
            ('mani-immunity', 't', 'Teleport Immunity'),
            ('mani-immunity', 'd', 'Drug Immunity'),
            ('mani-immunity', 'g', 'Swap Immunity'),
            ('mani-immunity', 'a', 'Name Tag Kick Immunity'),
            ('mani-immunity', 'c', 'Balance Team Immunity'),
            ('mani-immunity', 'e', 'Burn Immunity'),
            ('mani-immunity', 'h', 'Mute Immunity'),
            ('mani-immunity', 'n', 'Reserved Slot Kick Immunity'),
            ('mani-immunity', 'o', 'Set Skin Immunity'),
            ('mani-immunity', 'p', 'Reserved Skin Immunity'),
            ('mani-immunity', 'q', 'Timebomb Immunity'),
            ('mani-immunity', 'r', 'Firebomb Immunity'),
            ('mani-immunity', 's', 'Freezebomb Immunity'),
            ('mani-immunity', 'u', 'Beacon Immunity'),
            ('mani-immunity', 'v', 'Ghost Immunity'),
            ('mani-immunity', 'w', 'Give Item Immunity'),
            ('mani-immunity', 'y', 'Color Change Immunity'),
            ('mani-immunity', 'Immunity', 'Basic Immunity'),
            ('mani-immunity', 'grav', 'Per Player Gravity Immunity'),
            ('mani-immunity', 'autojoin', 'Autojoin Immunity'),
            ('mani-immunity', 'afk', 'AFK Kick Immunity'),
            ('mani-immunity', 'ping', 'Ping Kick Immunity');";

      $this->sql_query($sql);
  }

  /************************************************************************
  Upgrade from Version 2.2 to 3.0
	************************************************************************/
  function upgradeTwoPointTwoToThree() {
    $sql = "ALTER TABLE `gban_ban` ADD `time_scale` VARCHAR( 12 ) NOT NULL DEFAULT 'hours' AFTER `length` ;";

    $this->sql_query($sql);
  }

  function upgradeThreeToThreePointOne() {
    $sql = "ALTER TABLE `gban_ban_history` ADD `time_scale` VARCHAR( 12 ) NOT NULL DEFAULT 'hours' AFTER `length` ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_length` (
            `length_id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `length` INT( 10 ) NOT NULL ,
            `time_scale` VARCHAR( 12 ) NOT NULL DEFAULT 'hours'
            ) ENGINE = MYISAM ;";

    $this->sql_query($sql);

    $sql = "INSERT INTO `gban_length` (`length_id`, `length`, `time_scale`) VALUES
            (1, 0, 'minutes'),
            (2, 5, 'minutes'),
            (3, 1, 'hours'),
            (4, 6, 'hours'),
            (5, 1, 'days'),
            (6, 1, 'weeks'),
            (7, 2, 'weeks'),
            (8, 1, 'months'),
            (9, 6, 'months');";

    $this->sql_query($sql);
  }

  function upgradeThreePointTwoToThreePointThree() {
    $sql = "ALTER TABLE `gban_admin_steam` ADD `active` TINYINT( 1 ) NOT NULL DEFAULT '1';";

    $this->sql_query($sql);
  }

  function upgradeThreePointThreeToThreePointFour() {

    $sql = "CREATE TABLE `gban_bad_names` (
              `bad_name_id` int(10) NOT NULL auto_increment,
              `bad_name` varchar(45) NOT NULL,
              `filter` tinyint(1) NOT NULL default '0',
              `kick` varchar(1) NOT NULL default '0',
              PRIMARY KEY  (`bad_name_id`),
              UNIQUE KEY `bad_name` (`bad_name`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    $this->sql_query($sql);

    $sql = "ALTER TABLE `gban_demo` ADD INDEX (`steam_id`);";

    $this->sql_query($sql);

    $sql = "ALTER TABLE `gban_servers` ADD `plugin` VARCHAR(50) NULL ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_server_admin` (
            `server_id` INT( 11 ) NOT NULL ,
            `admin_id` INT( 11 ) NOT NULL ,
            PRIMARY KEY ( `server_id` , `admin_id` )
            ) ENGINE = MYISAM ;";

    $this->sql_query($sql);

    $sql = "ALTER TABLE `gban_server_admin` ADD `admin_group_id` INT( 11 ) NOT NULL ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_group_admin` (
            `server_group_id` INT( 11 ) NOT NULL ,
            `admin_id` INT( 11 ) NOT NULL ,
            PRIMARY KEY ( `server_group_id` , `admin_id` )
            ) ENGINE = MYISAM ;";

    $this->sql_query($sql);

    $sql = "ALTER TABLE `gban_group_admin` ADD `admin_group_id` INT( 11 ) NOT NULL ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_server_group` (
              `server_group_id` int(11) NOT NULL auto_increment,
              `group_name` varchar(255) NOT NULL,
              `description` varchar(255) NOT NULL,
              PRIMARY KEY  (`server_group_id`),
              UNIQUE KEY `group_name` (`group_name`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    $this->sql_query($sql);

    $sql = "ALTER TABLE `gban_servers` ADD `server_group_id` INT( 11 ) NULL ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_plugin` (
              `plugin` VARCHAR( 25 ) NOT NULL ,
              `name` VARCHAR( 128 ) NOT NULL ,
              `description` VARCHAR( 255 ) NOT NULL ,
              PRIMARY KEY ( `plugin` )
            ) ENGINE = MYISAM ;";

    $this->sql_query($sql);
    
    $sql = "DROP TABLE `gban_plugin_flag`";
    
    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_plugin_flag` (
              `plugin_flag_id` int(11) NOT NULL auto_increment,
              `plugin` varchar(50) NOT NULL,
              `flag` varchar(25) NOT NULL,
              `description` varchar(255) default NULL,
              PRIMARY KEY  (`plugin_flag_id`),
              KEY `plugin` (`plugin`,`flag`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    
    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_admin_group` (
              `admin_group_id` int(11) NOT NULL auto_increment,
              `group_name` varchar(255) NOT NULL,
              `description` varchar(255) NOT NULL,
              PRIMARY KEY  (`admin_group_id`),
              UNIQUE KEY `group_name` (`group_name`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    $this->sql_query($sql);

    $sql = "CREATE TABLE `gban_admin_group_flag` (
            `admin_group_id` INT( 11 ) NOT NULL ,
            `plugin_flag_id` INT( 11 ) NOT NULL ,
            `enabled` TINYINT( 1 ) NOT NULL DEFAULT '0',
            PRIMARY KEY ( `admin_group_id` , `plugin_flag_id` )
            ) ENGINE = MYISAM ;";

    $this->sql_query($sql);
    
    // Truncate data from the plugin and plugin_flag tables
    
    $sql = "TRUNCATE TABLE `gban_plugin`;";
    
    $this->sql_query($sql);
    
    $sql = "TRUNCATE TABLE `gban_plugin_flag`;";
    
    $this->sql_query($sql);

    $sql = "-- Plugin Default Data
            INSERT INTO `gban_plugin` (`plugin`, `name`, `description`) VALUES
            ('gban', 'GlobalBan Powers', 'These are the powers that are specific to GlobalBan.'),
            ('mani', 'Mani Admin Plugin', 'These are the powers for the Mani Admin Plugin.'),
            ('sourcemod', 'SourceMOD Plugin', 'These are the powers for the SourceMOD plugin.');";
            
    $this->sql_query($sql);
    
    $sql = "-- GlobalBan flags
            INSERT INTO `gban_plugin_flag` (`plugin`, `flag`, `description`) VALUES
            ('gban', 'b', 'Ban Manager'),
            ('gban', 'a', 'Admin'),
            ('gban', 'm', 'Member');";
            
    $this->sql_query($sql);
    
    $sql = "-- Mani Flags
            INSERT INTO `gban_plugin_flag` (`plugin`, `flag`, `description`) VALUES
            ('mani', 'j', 'Gimp'),
            ('mani', 'k', 'Kick'),
            ('mani', 'r', 'Rcon'),
            ('mani', 'q', 'Rcon Menu Level 1'),
            ('mani', 'e', 'Explode'),
            ('mani', 'm', 'Slay'),
            ('mani', 'b', 'Non Permanent Ban'),
            ('mani', 's', 'Admin Say'),
            ('mani', 'o', 'Admin Chat'),
            ('mani', 'a', 'Private Admin Chat'),
            ('mani', 'c', 'Map Change'),
            ('mani', 'p', 'Sound'),
            ('mani', 'w', 'Weapon Restrictions'),
            ('mani', 'z', 'Config'),
            ('mani', 'x', 'Client Execution'),
            ('mani', 'y', 'Client Execution Menu'),
            ('mani', 'i', 'Blind'),
            ('mani', 'l', 'Slap'),
            ('mani', 'f', 'Freeze'),
            ('mani', 't', 'Teleport'),
            ('mani', 'd', 'Drug'),
            ('mani', 'g', 'Swap Player'),
            ('mani', 'R', 'Rcon Voting'),
            ('mani', 'B', 'Rcon Voting Menu'),
            ('mani', 'v', 'Random Map Vote'),
            ('mani', 'V', 'Map Vote'),
            ('mani', 'Q', 'Console Question Vote'),
            ('mani', 'D', 'Menu Question Vote'),
            ('mani', 'C', 'Cancel Vote'),
            ('mani', 'A', 'Accept Vote'),
            ('mani', 'E', 'View Client Rates'),
            ('mani', 'F', 'Burn'),
            ('mani', 'G', 'No Clip Mode'),
            ('mani', 'H', 'War Mode'),
            ('mani', 'I', 'Mute'),
            ('mani', 'J', 'Reset stats'),
            ('mani', 'K', 'Cash'),
            ('mani', 'L', 'Rcon Say'),
            ('mani', 'M', 'Admin Skins'),
            ('mani', 'N', 'Set Skins'),
            ('mani', 'O', 'Drop C4'),
            ('mani', 'P', 'Set Client Flags'),
            ('mani', 'S', 'Set Skin Color'),
            ('mani', 'T', 'Time Bomb'),
            ('mani', 'U', 'Fire Bomb'),
            ('mani', 'W', 'Freeze Bomb'),
            ('mani', 'X', 'Adjust Health'),
            ('mani', 'Y', 'Beacon Player'),
            ('mani', 'Z', 'Give Player Item'),
            ('mani', 'admin', 'Basic Admin'),
            ('mani', 'client', 'Create Clients and Set Powers'),
            ('mani', 'pban', 'Permanent Ban'),
            ('mani', 'spray', 'Spray Tag Tracking'),
            ('mani', 'grav', 'Adjust Player Gravity'),
            ('mani', 'q2', 'Rcon Menu Level 2'),
            ('mani', 'reserved', 'Reserved Slot');";
            
      $this->sql_query($sql);
      $sql = "-- Sourcemod flags
            INSERT INTO `gban_plugin_flag` (`plugin`, `flag`, `description`) VALUES
            ('sourcemod', 'a', 'Reserved Slot'),
            ('sourcemod', 'b', 'Generic Admin (required for admins)'),
            ('sourcemod', 'c', 'Kick'),
            ('sourcemod', 'd', 'Ban'),
            ('sourcemod', 'e', 'Unban'),
            ('sourcemod', 'f', 'Slay'),
            ('sourcemod', 'g', 'Map Changing'),
            ('sourcemod', 'h', 'Modify cvars'),
            ('sourcemod', 'i', 'Modify Configs'),
            ('sourcemod', 'j', 'Special Chat Privileges'),
            ('sourcemod', 'k', 'Voting'),
            ('sourcemod', 'l', 'Password the server'),
            ('sourcemod', 'm', 'Remote Console'),
            ('sourcemod', 'n', 'Change sv_cheats and related commands'),
            ('sourcemod', 'z', 'Grant all permissions (ignores immunities)');";

      $this->sql_query($sql);
      
      $sql = "INSERT INTO `gban_plugin` (`plugin`, `name`, `description`) VALUES
            ('mani-immunity', 'Mani Admin Plugin Immunities', 'These are the immunities for the Mani Admin Plugin.');";
            
      $this->sql_query($sql);
      
      $sql = "INSERT INTO `gban_plugin_flag` (`plugin`, `flag`, `description`) VALUES
            ('mani-immunity', 'j', 'Gimp Immunity'),
            ('mani-immunity', 'k', 'Kick Immunity'),
            ('mani-immunity', 'm', 'Slay Immunity'),
            ('mani-immunity', 'b', 'Ban Immunity'),
            ('mani-immunity', 'x', 'Client Execution Immunity'),
            ('mani-immunity', 'i', 'Blind Immunity'),
            ('mani-immunity', 'l', 'Slap Immunity'),
            ('mani-immunity', 'f', 'Freeze Immunity'),
            ('mani-immunity', 't', 'Teleport Immunity'),
            ('mani-immunity', 'd', 'Drug Immunity'),
            ('mani-immunity', 'g', 'Swap Immunity'),
            ('mani-immunity', 'a', 'Name Tag Kick Immunity'),
            ('mani-immunity', 'c', 'Balance Team Immunity'),
            ('mani-immunity', 'e', 'Burn Immunity'),
            ('mani-immunity', 'h', 'Mute Immunity'),
            ('mani-immunity', 'n', 'Reserved Slot Kick Immunity'),
            ('mani-immunity', 'o', 'Set Skin Immunity'),
            ('mani-immunity', 'p', 'Reserved Skin Immunity'),
            ('mani-immunity', 'q', 'Timebomb Immunity'),
            ('mani-immunity', 'r', 'Firebomb Immunity'),
            ('mani-immunity', 's', 'Freezebomb Immunity'),
            ('mani-immunity', 'u', 'Beacon Immunity'),
            ('mani-immunity', 'v', 'Ghost Immunity'),
            ('mani-immunity', 'w', 'Give Item Immunity'),
            ('mani-immunity', 'y', 'Color Change Immunity'),
            ('mani-immunity', 'Immunity', 'Basic Immunity'),
            ('mani-immunity', 'grav', 'Per Player Gravity Immunity'),
            ('mani-immunity', 'autojoin', 'Autojoin Immunity'),
            ('mani-immunity', 'afk', 'AFK Kick Immunity'),
            ('mani-immunity', 'ping', 'Ping Kick Immunity');";

      $this->sql_query($sql);
      
      $sql = "ALTER TABLE `gban_admin_group` ADD `sm_immunity` INT( 3 ) NOT NULL DEFAULT '0';";
      
      $this->sql_query($sql);
      
      $sql = "ALTER TABLE `gban_server_group` ADD `sm_immunity` INT( 3 ) NOT NULL DEFAULT '0';";
      
      $this->sql_query($sql);
  }
  function upgradeThreePointFourToOdonelPointOne() {

    $sql = "ALTER TABLE `gban_ban` ADD `kick_counter` int(10) NOT NULL DEFAULT '0' AFTER `add_date` ;";

    $this->sql_query($sql);
    
    $sql = "ALTER TABLE `gban_ban_history` ADD `kick_counter` int(10) NOT NULL DEFAULT '0' AFTER `add_date` ;";

    $this->sql_query($sql);
    
  }
}
?>
