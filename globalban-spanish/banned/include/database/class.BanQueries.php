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
require_once(ROOTDIR."/include/objects/class.BannedUser.php");
require_once(ROOTDIR."/include/objects/class.Ip.php");
require_once(ROOTDIR."/config/class.Config.php");

class BanQueries extends Config {	

	// Variables
	var $db; // Object of database class
	var $endRange;
	
	// Default constructor
	function __construct() {
		$this->init();
  }
  
	function BanQueries() {
		$this->init();
  }
  
  function init() {
    $this->db = new Database; // Create new database connection for this object
  }
  
  /************************************************************************
	Adds a new ban to the database and returns the ID of the new banned person
	************************************************************************/
  function addBan($steamId, $length, $timeScale, $now, $reason, $user, $pending, $bannedName, $serverId, $ip, $bannerSteam) {
  
    // Remove new line characters from steam id
    $steamId = trim($steamId);
    
    $insertId = -1;
    
    if($steamId != "") {
    
      // No need to add ban if there is a current ban that is still active
      $sql = "SELECT count(1) as count FROM gban_ban 
              WHERE steam_id = '".addslashes($steamId)."'
              AND (expire_date > NOW() OR length = 0)
              AND active = 1";
      $this->db->sql_query($sql);
      $count = $this->db->get_row();
    
      $count = $count['count'];
            
      if($count > 0) {
        return -1;
      }
      
      // Archive an old ban
      if($this->isSteamIdBanned($steamId, -1)) {
        $this->archiveBan($steamId);
      }
    
      // Add the ban
      $sql = "INSERT INTO gban_ban (steam_id, length, time_scale, add_date, expire_date, reason_id, banner, pending, name, modified_by, server_id, ip, banner_steam_id)
      values('".addslashes($steamId)."', '".addslashes($length)."', '".$timeScale."', NOW(), FROM_UNIXTIME(".$now."), '".$reason."', '".$user."', '".$pending."',
      '".addslashes($bannedName)."', '".$user."', '".$serverId."', '".$ip."', '".addslashes($bannerSteam)."')";

      // Insert into db
      $this->db->sql_query($sql);

      $insertId = $this->db->get_insert_id();
    }
    
    return $insertId;
  }
  
  /************************************************************************
	Imports a ban from a GlobalBan XML file.
	************************************************************************/
  function importBan($steamId, $name, $length, $timeScale, $addDate, $expireDate, $webpage, $reason, $ip) {
    // Remove new line characters from steam id
    $steamId = trim($steamId);

    $insertId = -1;

    // Add the ban if it is currently inactive or does not exist in the current ban list
    if(!$this->isSteamIdBanned($steamId, -1) || $this->isSteamIdBanned($steamId, 0)) {
    
      // Only archive if the ban is currently inactive, leave active bans alone
      if($this->isSteamIdBanned($steamId, 0)) {
        $this->archiveBan($steamId);
      }
      
      // Add the ban
      $sql = "INSERT INTO gban_ban (steam_id, length, time_scale, add_date, expire_date, reason_id, banner, name, server_id, ip, webpage)
      values('".addslashes($steamId)."', '".addslashes($length)."', '".addslashes($timeScale)."', '".addslashes($addDate)."', '".addslashes($expireDate)."',
      '".addslashes($reason)."', '".addslashes($user)."', '".addslashes($name)."', '-1', '".addslashes($ip)."', '".addslashes($webpage)."')";

      // Insert into db
      $this->db->sql_query($sql);

      $insertId = $this->db->get_insert_id();
    }
    
    return $insertId;
  }
  
  /************************************************************************
	Determines if a steam ID already exists in the gban table.  If it does,
	instead of deleting it right away, copy it to the archive table and then
	delete it.  Once deleted, the new ban can be added.
	************************************************************************/
  function archiveBan($steamId) {
    // Remove new line characters from steam id
    $steamId = trim($steamId);
    
    // Copy the ban from gban_ban to gban_ban_history
    $sql = "INSERT INTO gban_ban_history (ban_id, steam_id, ip, name, length, time_scale, add_date, expire_date, reason_id, banner, banner_steam_id, active, pending, server_id, webpage, modified_by, comments)
    SELECT ban_id, steam_id, ip, name, length, time_scale, add_date, expire_date, reason_id, banner, banner_steam_id, active, pending, server_id, webpage, modified_by, comments
    FROM gban_ban WHERE steam_id = '".addslashes($steamId)."'";

    $this->db->sql_query($sql);
    
    // Delete the old ban from the gban_ban table as it is now in archive
    $this->deleteBan($steamId);
  }
  
  /************************************************************************
	Determine if a steam ID is in the gban table.  Set $active = 1 to look for
	only if it is active, 0 if it is only inactive, or -1 for both
	************************************************************************/
  function isSteamIdBanned($steamId, $active) {
    // Remove new line characters from steam id
    $steamId = trim($steamId);
    $sql = "";
    
    if($active > -1) {
      $sql = "SELECT * FROM gban_ban WHERE steam_id = '".$steamId."' AND active = '".$active."'";
    } else {
      $sql = "SELECT * FROM gban_ban WHERE steam_id = '".$steamId."'";
    }
    
    $this->db->sql_query($sql);
    $count = $this->db->num_rows();
    
    if($count == 0) {
      return false;
    } else {
      return true;
    }
  }
  
  /************************************************************************
	Adds a new ban ip to the database and returns a postitive number if
	the add is successful.
	************************************************************************/
  function addIpBan($ip) {
    // Make sure the IP doesn't already exist, if it does delete it first
    $this->deleteIpBan($ip);
  
    $sql = "INSERT INTO gban_ip (ip, active)
    values('".$ip."', 1)";
    
    $this->db->sql_query($sql);
    
    return true;
  }
  
  /************************************************************************
	Delete an IP Ban from the database
	************************************************************************/
  function deleteIpBan($ip) {
    $sql = "DELETE FROM gban_ip WHERE ip = '".$ip."'";

    $this->db->sql_query($sql);

    return true;
  }
  
  /************************************************************************
	Determine if an IP is banned or not.
	************************************************************************/
  function isIpBanned($ip) {
    if($ip == "") {
      return false;
    }
    $sql = "SELECT count(*) as count FROM gban_ip WHERE ip = '".$ip."' AND active = 1";
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
	Deletes a ban from the database based on steam id
	THIS SHOULD ONLY BE USED BY THE ARCHIVEBAN METHOD
	************************************************************************/
  function deleteBan($steamId) {
    $sql = "DELETE FROM gban_ban WHERE steam_id = '".$steamId."'"; 
    
    $this->db->sql_query($sql);
  }
  
  /************************************************************************
	Returns the date the ban was added in seconds
	************************************************************************/
  function getBanAddDate($banId) {
    $sql = "SELECT UNIX_TIMESTAMP(add_date) as add_date, length FROM gban_ban WHERE ban_id = '".$banId."'";
    $this->db->sql_query($sql);
    $banData = $this->db->get_row();
    
    $addDate = $banData['add_date']; // Currently in seconds
    
    return $addDate;
  }
  
  /************************************************************************
	Returns the total number of bans in the database depending on the user
	viewing the ban list.  Average users will only see the active and non-pending
	ban totals, while admins will see the real total.
	************************************************************************/
  function getNumberOfBans($member, $admin, $banManager, $fullPower, $searchText) {
    $banCount = 0;
    
    $searchText = trim($searchText); // Remove whitespace from search text
    $searchText = addslashes($searchText); // Prevent SQL Injection
    $searchJoin = "";
    if($searchText != null && $searchText != "") {
      if($member || $admin || $banManager || $fullPower) {
        $searchJoin = " WHERE (steam_id LIKE '%".$searchText."%' OR name LIKE '%".$searchText."%') ";
      } else {
        $searchJoin = " AND (steam_id LIKE '%".$searchText."%' OR name LIKE '%".$searchText."%') ";
      }
    }
    
    if($member || $admin || $banManager || $fullPower) {
      $banCountQuery = "SELECT count(*) as count FROM gban_ban".$searchJoin;
      $this->db->sql_query($banCountQuery);
      $banCount = $this->db->get_row();
      $banCount = $banCount['count'];
    } else {
      $banCountQuery = "SELECT count(*) as count FROM gban_ban
                        WHERE active = 1 AND pending = 0 ".$searchJoin;
      $this->db->sql_query($banCountQuery);
      $banCount = $this->db->get_row();
      $banCount = $banCount['count'];
    }
    return $banCount;
  }
  
  /************************************************************************
	Get the current list of bans to display on a page limited by the config
	value BansPerPage.
	************************************************************************/
  function getBanList($member, $admin, $banManager, $fullPower, $startRange, $banCount, $sortBy, $sortDirection, $searchText) {
    $searchText = trim($searchText); // Remove whitespace from search text
    $searchJoin = "";
    $searchText = addslashes($searchText); // Prevent SQL Injection
    if($searchText != null && $searchText != "") {
      $searchJoin = " (b.steam_id LIKE '%".$searchText."%' OR b.name LIKE '%".$searchText."%') ";
    }

    // Prevent SQL injection
    $sortBy = addslashes($sortBy);
    $sortDirection = addslashes($sortDirection);
  
    // Use LEFT JOIN so that bad reasons or serverids still display on the ban list, but as empty values
    // Get list of all the banned (Clan members can see ALL bans, active or not)
    $banList = "SELECT b.ban_id, b.steam_id, b.server_id, COALESCE(s.name, b.webpage) AS servername, b.length, b.time_scale, b.add_date,
    case when b.expire_date < NOW() then
      'Expired'
    else
      b.expire_date
    end expire_date,
    br.reason, COALESCE(b.banner_steam_id, 'N/A') as banner_steam_id, b.active, b.pending, b.name, b.comments, 
    (SELECT count(1) FROM gban_demo d WHERE d.steam_id = b.steam_id) as demo_count,
    (SELECT count(1) FROM gban_ban_history bh WHERE bh.steam_id = b.steam_id) AS offenses, ";
    if($this->enableSmfIntegration) {
      $banList .= "COALESCE(COALESCE(a.memberName, b.banner), 'N/A') as banner ";
    } else {
      $banList .= "COALESCE(COALESCE(a.name, b.banner), 'N/A') as banner ";
    }
    
    $banList .= "FROM gban_ban b
    LEFT JOIN gban_reason br ON b.reason_id = br.reason_id
    LEFT JOIN gban_servers s ON b.server_id = s.server_id
    LEFT JOIN gban_admin_steam asteam ON b.banner_steam_id = asteam.steam_id ";
    
    if($this->enableSmfIntegration) {
      $banList .= "LEFT JOIN smf_members a ON asteam.admin_id = a.ID_MEMBER ";
    } else {
      $banList .= "LEFT JOIN gban_admins a ON asteam.admin_id = a.admin_id ";
    }      
    
    // Get list of all the banned but do not show in-active or pending to non-admins
    if(!($member || $admin || $banManager || $fullPower)) {
      $banList .= "WHERE b.active = 1 AND b.pending = 0 ";
      if(!empty($searchJoin)) {
        $banList .= " AND ".$searchJoin;
      }
    } else {
      if(!empty($searchJoin)) {
        $banList .= " WHERE ".$searchJoin;
      }
    }
    
    $banList .= " ORDER BY $sortBy $sortDirection";
        
    if($this->bansPerPage > 0) {
      $this->endRange = $this->bansPerPage;
      $banList .= " LIMIT ".$startRange.", ".$this->endRange;
    }
    
    $this->db->sql_query($banList);
    $bannedUsersArray = $this->db->get_array();
    
    $bannedUsers = array();
    
    for($i=0; $i<count($bannedUsersArray); $i++) {
      $bannedUser = new BannedUser();
    
      $bannedUser->setBanId($bannedUsersArray[$i]['ban_id']);
      $bannedUser->setName(stripslashes($bannedUsersArray[$i]['name']));
      $bannedUser->setSteamId($bannedUsersArray[$i]['steam_id']); // Steam ID of banned
      $bannedUser->setLength($bannedUsersArray[$i]['length']);
      $bannedUser->setTimeScale($bannedUsersArray[$i]['time_scale']);
      
      $bannedUser->setAddDate($bannedUsersArray[$i]['add_date']);
      $bannedUser->setExpireDate($bannedUsersArray[$i]['expire_date']);
      $bannedUser->setReason($bannedUsersArray[$i]['reason']);
      $bannedUser->setActive($bannedUsersArray[$i]['active']);
      $bannedUser->setPending($bannedUsersArray[$i]['pending']);
      $bannedUser->setServerId($bannedUsersArray[$i]['server_id']);
      if($bannedUsersArray[$i]['servername'] == "") {
        $bannedUser->setServer("Bad Server ID");
      } else {
        $bannedUser->setServer($bannedUsersArray[$i]['servername']);
      }
      $bannerSteamId = $bannedUsersArray[$i]['banner_steam_id'];
      if($bannerSteamId == -1) {
        $bannedUser->setBannerSteamId("N/A");
        $bannedUser->setBanner("Console"); // Name of banner
      } else {
        $bannedUser->setBannerSteamId(bannerSteamId);
        $bannedUser->setBanner($bannedUsersArray[$i]['banner']); // Name of banner
      }
      
      $bannedUser->setDemoCount($bannedUsersArray[$i]['demo_count']);
      $bannedUser->setComments(stripslashes($bannedUsersArray[$i]['comments']));
      $bannedUser->setOffenses($bannedUsersArray[$i]['offenses']);
      
      array_push($bannedUsers, $bannedUser); // Add the banned user object to the array
    }
        
    return $bannedUsers;
  }
  
  /************************************************************************
	Returns the total number of ip bans in the database depending on the user
	viewing the ban list.  Average users will only see the active and non-pending
	ban totals, while admins will see the real total.
	************************************************************************/
  function getNumberOfIpBans($banManager, $fullPower, $searchText) {
    $ipCount = 0;

    $searchText = trim($searchText); // Remove whitespace from search text
    $searchText = addslashes($searchText); // Prevent SQL Injection
    $searchJoin = "";
    if($searchText != null && $searchText != "") {
      if($banManager || $fullPower) {
        $searchJoin = " WHERE (ip LIKE '%".$searchText."%') ";
      } else {
        $searchJoin = " AND (ip LIKE '%".$searchText."%') ";
      }
    }

    if($banManager || $fullPower) {
      $ipCountQuery = "SELECT count(*) as count FROM gban_ip".$searchJoin;
      $this->db->sql_query($ipCountQuery);
      $ipCount = $this->db->get_row();
      $ipCount = $ipCount['count'];
    } else {
      $banCountQuery = "SELECT count(*) as count FROM gban_ip WHERE active = 1 ".$searchJoin;
      $this->db->sql_query($banCountQuery);
      $ipCount = $this->db->get_row();
      $ipCount = $ipCount['count'];
    }
    return $ipCount;
  }

  /************************************************************************
	Get the current list of bans to display on a page limited by the config
	value BansPerPage.
	************************************************************************/
  function getIpBanList($banManager, $fullPower, $startRange, $banCount, $sortBy, $sortDirection, $searchText) {
    $searchText = trim($searchText); // Remove whitespace from search text
    $searchJoin = "";
    $searchText = addslashes($searchText); // Prevent SQL Injection
    if($searchText != null && $searchText != "") {
      $searchJoin = " (ip LIKE '%".$searchText."%') ";
    }

    // Prevent SQL injection
    $sortBy = addslashes($sortBy);
    $sortDirection = addslashes($sortDirection);

    // Use LEFT JOIN so that bad reasons or serverids still display on the ban list, but as empty values
    if($banManager || $fullPower) {
      // Get list of all the banned (Clan members can see ALL bans, active or not)
      $ipList = "SELECT ip, active FROM gban_ip ";
      if(!empty($searchJoin)) {
        $ipList .= " WHERE ".$searchJoin;
      }
      $ipList .= "ORDER BY $sortBy $sortDirection";
    } else {
      // Get list of all the banned but do not show in-active or pending to non-UNBU
      $ipList = "SELECT ip, active FROM gban_ip WHERE active = 1 ";
      if(!empty($searchJoin)) {
        $ipList .= " AND ".$searchJoin;
      }
      $ipList .= "ORDER BY $sortBy $sortDirection";
    }

    if($this->bansPerPage > 0) {
      $this->endRange = $this->bansPerPage;
      $ipList .= " LIMIT ".$startRange.", ".$this->endRange;
    }

    $this->db->sql_query($ipList);
    $ipArray = $this->db->get_array();

    $bannedIps = array();

    for($i=0; $i<count($ipArray); $i++) {
      $bannedIp = new BannedUser();

      $bannedIp->setIp($ipArray[$i]['ip']);
      $bannedIp->setActive($ipArray[$i]['active']);

      array_push($bannedIps, $bannedIp); // Add the banned user object to the array
    }

    return $bannedIps;
  }
  
  /************************************************************************
	Get all active bans for downloading.  Pending bans are ignored.
	************************************************************************/
  function downloadActiveBans($all, $demosOnly) {
    // Get ALL bans
    $banList = "SELECT b.ban_id, b.steam_id, b.server_id, COALESCE(s.name, b.webpage) AS servername, b.length, b.time_scale, b.add_date, b.expire_date,
                br.reason, b.banner, b.banner_steam_id, b.active, b.pending, b.name, count(d.steam_id) as demo_count, b.comments, b.ip, UNIX_TIMESTAMP(b.expire_date) as expire_date_seconds
                FROM gban_ban b
                LEFT JOIN gban_reason br ON b.reason_id = br.reason_id
                LEFT JOIN gban_servers s ON b.server_id = s.server_id
                LEFT JOIN gban_demo d ON b.steam_id = d.steam_id
                WHERE active = 1 AND pending = 0 ";
                
    // Restrict to only perma bans if flagged as such
    if(!$all) {
      $banList .= " AND length = 0";
    }
    
    $banList .= " GROUP BY b.steam_id";

    $this->db->sql_query($banList);
    $bannedUsersArray = $this->db->get_array();
    
    $bannedUsers = array();

    for($i=0; $i<count($bannedUsersArray); $i++) {
      $bannedUser = new BannedUser();
      
      // Remove new line characters from steam id
      $bannedUser->setBanId($bannedUsersArray[$i]['ban_id']);
      $bannedUser->setName(str_replace("'", "''", $bannedUsersArray[$i]['name']));
      $bannedUser->setIp($bannedUsersArray[$i]['ip']);
      $bannedUser->setSteamId(str_replace(array("\r\n", "\n", "\r"), "", $bannedUsersArray[$i]['steam_id'])); // Steam ID of banned
      $bannedUser->setLength($bannedUsersArray[$i]['length']);
      $bannedUser->setTimeScale($bannedUsersArray[$i]['time_scale']);
      $bannedUser->setBanner($bannedUsersArray[$i]['banner']); // Name of banner
      $bannedUser->setAddDate($bannedUsersArray[$i]['add_date']);
      $bannedUser->setExpireDate($bannedUsersArray[$i]['expire_date']);
      $bannedUser->setExpireDateSeconds($bannedUsersArray[$i]['expire_date_seconds']);
      $bannedUser->setReason($bannedUsersArray[$i]['reason']);
      $bannedUser->setActive($bannedUsersArray[$i]['active']);
      $bannedUser->setPending($bannedUsersArray[$i]['pending']);
      $bannedUser->setServerId($bannedUsersArray[$i]['server_id']);
      if($bannedUsersArray[$i]['servername'] == "") {
        $bannedUser->setServer("Bad Server ID");
      } else {
        $bannedUser->setServer(str_replace("'", "''", $bannedUsersArray[$i]['servername']));
      }
      $bannedUser->setBannerSteamId($bannedUsersArray[$i]['banner_steam_id']);
      $bannedUser->setDemoCount($bannedUsersArray[$i]['demo_count']);
      $bannedUser->setComments(stripslashes(str_replace("'", "''", $bannedUsersArray[$i]['comments'])));
      
      if($demosOnly) {
        if($bannedUsersArray[$i]['demo_count'] > 0) {
          array_push($bannedUsers, $bannedUser); // Only add those with demos to the array
        }
      } else {
        array_push($bannedUsers, $bannedUser); // Add everyone to the array
      }
    }
    return $bannedUsers;
  }
  
  /************************************************************************
	Get all active ip bans for downloading.
	************************************************************************/
  function downloadActiveIps() {
    $ipList = "SELECT ip FROM gban_ip WHERE active = 1";

    $this->db->sql_query($ipList);
    $bannedIpArray = $this->db->get_array();

    $bannedIps = array();

    for($i=0; $i<count($bannedIpArray); $i++) {
      $bannedIp = new Ip();

      // Remove new line characters from steam id
      $bannedIp->setIp($bannedIpArray[$i]['ip']);

      array_push($bannedIps, $bannedIp); // Add the banned user object to the array
    }
    return $bannedIps;
  }
  
  /************************************************************************
	Determine if the banned user already exists in the database
	************************************************************************/
  function doesUserExist($steamId) {
    // Check to see if the user does exist in the ban list
    $query = "SELECT length, UNIX_TIMESTAMP(expire_date) as expire_date, name " . 
             "FROM gban_ban " .
             "WHERE steam_id = '" . $steamId . "' AND active = 1";
    $this->db->sql_query($query);
    
    if($this->db->num_rows() > 0) {
      return true;
    } else {
      return false;
    }
  }
  
  /************************************************************************
	Get the information of a baned user based on id
	************************************************************************/
  function getBannedUser($banId) {
    $bannedUser = new BannedUser();
	
    $bannedQuery = "SELECT ban_id, steam_id, ip, length, time_scale, reason_id, server_id, name, banner, banner_steam_id, modified_by, comments
                    FROM gban_ban 
                    WHERE ban_id = '".$banId."' 
                    LIMIT 1";
    $this->db->sql_query($bannedQuery);
    
    $tempBannedUser = $this->db->get_row();
    
    $bannedUser->setSteamId($tempBannedUser['steam_id']);
    $bannedUser->setIp($tempBannedUser['ip']);
    $bannedUser->setLength($tempBannedUser['length']);
    $bannedUser->setTimeScale($tempBannedUser['time_scale']);
    $bannedUser->setReasonId($tempBannedUser['reason_id']);
    $bannedUser->setServerId($tempBannedUser['server_id']);
    $bannedUser->setName(stripslashes($tempBannedUser['name']));
    $bannedUser->setBanner(stripslashes($tempBannedUser['banner']));
    $bannedUser->setBannerSteamId($tempBannedUser['banner_steam_id']);
    $bannedUser->setModifiedBy(stripslashes($tempBannedUser['modified_by']));
    $bannedUser->setComments(stripslashes($tempBannedUser['comments']));
    
    return $bannedUser;
  }
  
  /************************************************************************
	Get the information of a baned user based on steam id
	************************************************************************/
  function getBannedUserBySteamId($steamId) {
    $bannedUser = new BannedUser();
    
    $steamId = trim($steamId);

    $bannedQuery = "SELECT ban_id, steam_id, ip, length, time_scale, reason_id, server_id, name, banner, banner_steam_id, modified_by,
                    UNIX_TIMESTAMP(expire_date) as expire_date, UNIX_TIMESTAMP(add_date) as add_date, pending, comments
                    FROM gban_ban
                    WHERE steam_id = '".$steamId."'
                    LIMIT 1";
    $this->db->sql_query($bannedQuery);

    $tempBannedUser = $this->db->get_row();

    $bannedUser->setSteamId(trim($tempBannedUser['steam_id']));
    $bannedUser->setIp($tempBannedUser['ip']);
    $bannedUser->setLength($tempBannedUser['length']);
    $bannedUser->setTimeScale($tempBannedUser['time_scale']);
    $bannedUser->setReasonId($tempBannedUser['reason_id']);
    $bannedUser->setServerId($tempBannedUser['server_id']);
    $bannedUser->setName(stripslashes($tempBannedUser['name']));
    $bannedUser->setBanner(stripslashes($tempBannedUser['banner']));
    $bannedUser->setBannerSteamId($tempBannedUser['banner_steam_id']);
    $bannedUser->setModifiedBy(stripslashes($tempBannedUser['modified_by']));
    $bannedUser->setAddDate($tempBannedUser['add_date']);
    $bannedUser->setExpireDate($tempBannedUser['expire_date']);
    $bannedUser->setPending($tempBannedUser['pending']);
    $bannedUser->setComments(stripslashes($tempBannedUser['comments']));

    return $bannedUser;
  }
  
  /************************************************************************
	Get the ban history of a specific ban id
	************************************************************************/
  function getBanHistory($banId) {
    $query = "SELECT b.ban_id, h.steam_id, h.ip, h.name, h.server_id, COALESCE(s.name, h.webpage) AS servername, h.length,
              h.time_scale, h.add_date, h.expire_date, h.modified_by,
              br.reason, h.banner, h.banner_steam_id, h.active, h.pending, h.name, h.comments
              FROM gban_ban b, gban_ban_history h
              LEFT JOIN gban_reason br ON h.reason_id = br.reason_id
              LEFT JOIN gban_servers s ON h.server_id = s.server_id
              WHERE b.ban_id = '".$banId."'
              AND h.steam_id = b.steam_id
              ORDER BY h.add_date DESC";
              
    $this->db->sql_query($query);

    $bannedUsersArray = $this->db->get_array();

    $bannedUsers = array();

    for($i=0; $i<count($bannedUsersArray); $i++) {
      $bannedUser = new BannedUser();

      $bannedUser->setBanId($bannedUsersArray[$i]['ban_id']);
      $bannedUser->setName(stripslashes($bannedUsersArray[$i]['name']));
      $bannedUser->setSteamId($bannedUsersArray[$i]['steam_id']); // Steam ID of banned
      $bannedUser->setLength($bannedUsersArray[$i]['length']);
      $bannedUser->setTimeScale($bannedUsersArray[$i]['time_scale']);
      $bannedUser->setBanner($bannedUsersArray[$i]['banner']); // Name of banner
      $bannedUser->setAddDate($bannedUsersArray[$i]['add_date']);
      $bannedUser->setExpireDate($bannedUsersArray[$i]['expire_date']);
      $bannedUser->setReason($bannedUsersArray[$i]['reason']);
      $bannedUser->setActive($bannedUsersArray[$i]['active']);
      $bannedUser->setPending($bannedUsersArray[$i]['pending']);
      $bannedUser->setServerId($bannedUsersArray[$i]['server_id']);
      if($bannedUsersArray[$i]['servername'] == "") {
        $bannedUser->setServer("Bad Server ID");
      } else {
        $bannedUser->setServer($bannedUsersArray[$i]['servername']);
      }
      $bannedUser->setBannerSteamId($bannedUsersArray[$i]['banner_steam_id']);
      $bannedUser->setDemoCount($bannedUsersArray[$i]['demo_count']);
      $bannedUser->setComments(stripslashes($bannedUsersArray[$i]['comments']));
      $bannedUser->setOffenses($bannedUsersArray[$i]['offenses']);

      array_push($bannedUsers, $bannedUser); // Add the banned user object to the array
    }

    return $bannedUsers;
  }
  
  /************************************************************************
	Update the name of a banned user if their name was empty and they attempted
	to connect to the server.
	************************************************************************/
  function updateBanName($nameOfBanned, $steamId) {
    $addNameQuery = "UPDATE gban_ban SET name = '".addslashes($nameOfBanned)."' WHERE steam_id = '".$steamId."'";
    $this->db->sql_query($addNameQuery);
  }
  
  /************************************************************************
	Update the ip of a banned user if their name was empty and they attempted
	to connect to the server.
	************************************************************************/
  function updateBanIp($ip, $steamId) {
    $addNameQuery = "UPDATE gban_ban SET ip = '".addslashes($ip)."' WHERE steam_id = '".$steamId."'";
    $this->db->sql_query($addNameQuery);
  }
  
  /************************************************************************
	Change the banned ip's active status from active to inactive or vise versa
	************************************************************************/
  function updateBanIpActiveStatus($active, $ip) {
    $this->db->sql_query("UPDATE gban_ip SET active = '".$active."' WHERE ip = '".$ip."'");
  }
  
  /************************************************************************
	Change the ban's active status from active to inactive or vise versa
	************************************************************************/
  function updateBanActiveStatus($active, $id) {
    $this->db->sql_query("UPDATE gban_ban SET active = '".$active."' WHERE ban_id = '".$id."'");
  }
  
  
  /************************************************************************
	Change the ban's pending status from pending to non-pending or vise versa
	************************************************************************/
  function updateBanPendingStatus($pending, $id) {
    $this->db->sql_query("UPDATE gban_ban SET pending = '".$pending."' WHERE ban_id = '".$id."'");
  }
  
  /************************************************************************
	Just update the banned user's information
	************************************************************************/
  function updateWebBan($reason, $pending, $username, $serverId, $bannedUser, $banId, $comments) {
    $sql = "UPDATE gban_ban SET reason_id = '".$reason."', pending = '".$pending."', modified_by = '".$username."', 
    server_id = '".$serverId."', name = '".$bannedUser."', comments = '".addslashes($comments)."'
    WHERE ban_id = '".$banId."'";

    // Update db
    $this->db->sql_query($sql);	
  }
  
  /************************************************************************
	Update the banned user's information and ban length
	************************************************************************/
  function updateWebBanWithLength($length, $timeScale, $newExpireDate, $reason, $pending, $user, $serverId, $bannedUser, $banId, $comments) {
    $sql = "UPDATE gban_ban SET length = '".$length."', time_scale = '".$timeScale."', expire_date = FROM_UNIXTIME(".$newExpireDate."), comments = '".addslashes($comments)."',
    reason_id = '".$reason."', pending = '".$pending."', modified_by = '".$user."', server_id = '".$serverId."', name = '".$bannedUser."'
    WHERE ban_id = '".$banId."'";

    // Update db
    $this->db->sql_query($sql);
  }
  
  /************************************************************************
	Used for banlist page
	************************************************************************/
  function getEndRange() {
    return $this->endRange;
  }
}
?>
