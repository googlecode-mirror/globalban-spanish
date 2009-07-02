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
	This class contains all queries that deal with the gban_server table
******************************************************************************************************/

require_once(ROOTDIR."/include/database/class.Database.php");
require_once(ROOTDIR."/config/class.Config.php");
require_once(ROOTDIR."/include/objects/class.Server.php");
require_once(ROOTDIR."/include/objects/class.ServerGroup.php");

class ServerQueries extends Config {	

	// Variables
	var $db; // Object of database class
	
	// Default constructor
	function __construct() {
		$this->init();
  }
  
	function ServerQueries() {
		$this->init();
  }
  
  function init() {
    $this->db = new Database; // Create new database connection for this object
  }
    
  /************************************************************************
	Add a new server
	************************************************************************/
  function addServer($name, $ip, $port, $rcon, $type, $plugin) {
    $addServerQuery = "INSERT INTO gban_servers (name, ip, port, rcon, type, plugin)
    values('".addslashes($name)."', '".addslashes($ip)."', '".addslashes($port)."', '".addslashes($rcon)."', '".addslashes($type)."', LOWER('".addslashes($plugin)."'))";
    $this->db->sql_query($addServerQuery);
    
    return $this->db->get_insert_id();
  }
  
  /************************************************************************
	Delete a server
	************************************************************************/
  function deleteServer($id) {
    $deleteServerQuery = "DELETE FROM gban_servers WHERE server_id = '".$id."'";
    $this->db->sql_query($deleteServerQuery);
  }
  
  /************************************************************************
	Return an array of server objects
	************************************************************************/
  function getServers() {
    $serversQuery = "SELECT server_id, name, ip, port, rcon, type, plugin, server_group_id
                     FROM gban_servers ORDER BY server_id";
    $this->db->sql_query($serversQuery);
    $servers = $this->db->get_array();
    
    $serverList = array();
    
    for($i=0; $i<count($servers); $i++) {
      $server = new Server();
      
      $server->setId($servers[$i]['server_id']);
      $server->setName(stripslashes($servers[$i]['name']));
      $server->setIp($servers[$i]['ip']);
      $server->setPort($servers[$i]['port']);
      $server->setRcon(stripslashes($servers[$i]['rcon']));
      $server->setType($servers[$i]['type']);
      $server->setPlugin($servers[$i]['plugin']);
      $server->setGroupId($servers[$i]['server_group_id']);
      
      array_push($serverList, $server); // Add the server object to the array
    }
    
    return $serverList;
  }
  
  /************************************************************************
	Get the number of servers
	************************************************************************/
  function getNumberOfServers() {
    $countQuery = "SELECT count(*) as count FROM gban_servers";
    $this->db->sql_query($countQuery);
    $count = $this->db->get_row();
    $count = $count['count'];
    return $count;
  }
  
  /************************************************************************
	Get a server based on the server's id
	************************************************************************/
  function getServer($id) {
    $serversQuery = "SELECT server_id, name, ip, port, rcon, type, plugin, server_group_id
                     FROM gban_servers 
                     WHERE server_id = '".addslashes($id)."'";
    $this->db->sql_query($serversQuery);
    $tempServer = $this->db->get_row();
    
    $server = new Server();
    
    $server->setId($tempServer['server_id']);
    $server->setName(stripslashes($tempServer['name']));
    $server->setIp($tempServer['ip']);
    $server->setPort($tempServer['port']);
    $server->setRcon(stripslashes($tempServer['rcon']));
    $server->setType($tempServer['type']);
    $server->setPlugin($tempServer['plugin']);
    $server->setGroupId($tempServer['server_group_id']);
    
    return $server;
  }
  
  /************************************************************************
	Update the specified server's information
	************************************************************************/
  function updateServer($name, $ip, $port, $rcon, $currentRcon, $type, $id, $plugin) {
  
    // Verify that the "current rcon" matches
    $this->db->sql_query("SELECT rcon FROM gban_servers WHERE server_id = '".$id."'");
    $thisRcon = $this->db->get_row();
    $thisRcon = $thisRcon['rcon'];
    
    if($thisRcon != $currentRcon) {
      return "false";
    } else {
      // It may be empty if they are only updating
      if(empty($rcon)) {
        $rcon = $currentRcon;
      }
      $sql = "UPDATE gban_servers
          SET name='".addslashes($name)."',
          ip = '".addslashes($ip)."',
          port = '".addslashes($port)."',
          rcon = '".addslashes($rcon)."',
          type = '".addslashes($type)."',
          plugin = '".addslashes($plugin)."'
          WHERE server_id = '".addslashes($id)."'";

      // Update the db
      $this->db->sql_query($sql);

      return "true";
    }
  }
  
  /************************************************************************
	Get the first server that is listed in the server table
	************************************************************************/
  function getFirstServer() {
    $sql = "SELECT MIN( server_id ) as min FROM `gban_servers`";
    $this->db->sql_query($sql);
    $id = $this->db->get_row();
    return $id['min'];
  }
  
  /************************************************************************
	Get the list of server groups.
	************************************************************************/
  function getServerGroups() {
    $sql = "SELECT server_group_id, group_name, description FROM gban_server_group ORDER BY group_name ASC";
    $this->db->sql_query($sql);
    
    $groups = $this->db->get_array();

    $serverGroups = array();
    
    for($i=0; $i<count($groups); $i++) {
      $serverGroup = new ServerGroup();

      $serverGroup->setId($groups[$i]['server_group_id']);
      $serverGroup->setName(stripslashes($groups[$i]['group_name']));
      $serverGroup->setDescription(stripslashes($groups[$i]['description']));
      
      // Determine the servers associated with this server group
      if($serverGroup->getId() != null) {
        $serversQuery = "SELECT server_id, name, ip, port, type, plugin
                         FROM gban_servers
                         WHERE server_group_id = '".$serverGroup->getId()."'
                         ORDER BY server_id";
        $this->db->sql_query($serversQuery);
        $servers = $this->db->get_array();
        
        $serverList = array();

        for($j=0; $j<count($servers); $j++) {
          $server = new Server();

          $server->setId($servers[$j]['server_id']);
          $server->setName(stripslashes($servers[$j]['name']));
          $server->setIp($servers[$j]['ip']);
          $server->setPort($servers[$j]['port']);
          $server->setType($servers[$j]['type']);
          $server->setPlugin($servers[$j]['plugin']);

          array_push($serverList, $server); // Add the server object to the array
        }

        $serverGroup->setServers($serverList);
      }
      // Done getting servers associated with this server group

      array_push($serverGroups, $serverGroup); // Add the server object to the array
    }

    return $serverGroups;
  }
  
  /************************************************************************
	Add a new server group
	************************************************************************/
  function addServerGroup($groupName, $description) {
    $sql = "INSERT INTO gban_server_group (group_name, description)
            values('".addslashes($groupName)."', '".addslashes($description)."')";
    $this->db->sql_query($sql);
    
    return $this->db->get_insert_id();
  }
  
  /************************************************************************
	Set a server to a specific group
	************************************************************************/
  function setServerGroup($serverId, $groupId) {
    $sql = "UPDATE gban_servers SET server_group_id = '".addslashes($groupId)."'
            WHERE server_id = '".addslashes($serverId)."'";
    $this->db->sql_query($sql);
  }
  
  /************************************************************************
	Get a specific group
	************************************************************************/
  function getServerGroup($groupId) {
    $sql = "SELECT server_group_id, group_name, description FROM gban_server_group
            WHERE server_group_id = '".addslashes($groupId)."'";
    $this->db->sql_query($sql);
    
    $tempGroup = $this->db->get_row();

    $group = new ServerGroup();

    $group->setId($tempGroup['server_group_id']);
    $group->setName(stripslashes($tempGroup['group_name']));
    $group->setDescription(stripslashes($tempGroup['description']));

    return $group;
  }
  
  /************************************************************************
	Add an admin to either a server or group
	************************************************************************/
  function addAdminToServer($serverId, $groupId, $adminId) {
    // This means we are adding an admin to a group
    if($groupId > 0) {
      $sql = "INSERT INTO gban_group_admin (server_group_id, admin_id)
              values('".addslashes($groupId)."', '".addslashes($adminId)."')";
    } else if($serverId > 0) { // This means we are adding an admin to a specific server
      $sql = "INSERT INTO gban_server_admin (server_id, admin_id)
              values('".addslashes($serverId)."', '".addslashes($adminId)."')";
    } else {
      // Can't do anything
      return;
    }
    $this->db->sql_query($sql);
  }
  
  /************************************************************************
	Gets the plugins for a specific server
	************************************************************************/
  function getServerPlugins($serverId) {
    $sql = "SELECT DISTINCT plugin
            FROM gban_admin_group_flag gf, gban_plugin_flag pf
            WHERE gf.plugin_flag_id = pf.plugin_flag_id
            AND admin_group_id IN (    
              SELECT DISTINCT admin_group_id
              FROM gban_server_admin
              WHERE server_id = '".addslashes($serverId)."'
              AND admin_group_id > 0
              
              UNION
              
              SELECT DISTINCT admin_group_id
              FROM gban_group_admin ga, gban_servers s
              WHERE s.server_id = '".addslashes($serverId)."'
              AND ga.server_group_id = s.server_group_id
              AND admin_group_id > 0)";
    
    $this->db->sql_query($sql);
    
    $plugins = $this->db->get_array();

    $pluginList = array();

    for($i=0; $i<count($plugins); $i++) {
      array_push($pluginList, $plugins[$i]['plugin']);
    }

    return $pluginList;
  }
  
  /************************************************************************
	Gets the list of ALL available plugins
	************************************************************************/
  function getAllPlugins() {
    $sql = "SELECT plugin FROM gban_plugin";
    
    $this->db->sql_query($sql);
    
    $plugins = $this->db->get_array();

    $pluginList = array();

    for($i=0; $i<count($plugins); $i++) {
      array_push($pluginList, $plugins[$i]['plugin']);
    }

    return $pluginList;
  }
}
?>
