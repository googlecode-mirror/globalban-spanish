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
	This class is part of the database abstraction layer.  This deals with all user specific queries.
	Provides much more security.  Uses mySQL built in aes_encryption.
	addslashes() used on all usernames to prevent sql injections.
******************************************************************************************************/

require_once(ROOTDIR."/include/objects/class.AdminGroup.php"); // Used to store user info
require_once(ROOTDIR."/include/objects/class.Plugin.php"); // Used to store user info
require_once(ROOTDIR."/include/objects/class.PluginFlag.php"); // Used to store user info
require_once(ROOTDIR."/include/objects/class.User.php"); // Used to store banned user info
require_once(ROOTDIR."/include/database/class.Database.php"); //Database class
require_once(ROOTDIR."/config/class.Config.php");

class AdminGroupQueries extends Config {

	// Variables
	var $db; // Object of database class
	var $user; // Object to store user info

	// Default constructor
	function __construct() {
		$this->init();
  }

	function AdminGroupQueries() {
		$this->init();
  }

  function init() {
    $this->db = new Database; // Create new database connection
		$this->user = new User; // Create a User object
  }

	/************************************************************************
	Add a new admin group
	************************************************************************/
	function addAdminGroup($groupName, $description) {
    $sql = "INSERT INTO gban_admin_group (group_name, description)
            values('".addslashes($groupName)."', '".addslashes($description)."')";
    $this->db->sql_query($sql);

    $groupId = $this->db->get_insert_id();

    // Now add default gban admin flags
    $sql = "INSERT INTO gban_admin_group_flag (admin_group_id, plugin_flag_id)
            SELECT ".$groupId." as admin_group_id, plugin_flag_id FROM gban_plugin_flag WHERE plugin='gban'";

    $this->db->sql_query($sql);

    return $groupId;
  }

    /************************************************************************
	Delete a AdminGroup
	************************************************************************/
	function deleteAdminGroup($id) {
	
		// 1º Delete fron all Server_Groups the Admins that was using this AdminGroup
		$deleteAdminGroupQuery = "DELETE FROM `gban_group_admin` WHERE (`admin_group_id`='".$id."')";
		$this->db->sql_query($deleteAdminGroupQuery);
		
		// 2º Delete Plugin and Flags that was using this AdminGroup
		$deleteAdminGroupQuery = "DELETE FROM `gban_admin_group_flag` WHERE (`admin_group_id`='".$id."')";
		$this->db->sql_query($deleteAdminGroupQuery);
		
		// 3º Delete fron all Servers (without Server_Group) the Admins that was using this AdminGroup
		$deleteAdminGroupQuery = "DELETE FROM `gban_server_admin` WHERE (`admin_group_id`='".$id."')";
		$this->db->sql_query($deleteAdminGroupQuery);
		
		// 4º Delete the AdminGroup
		$deleteAdminGroupQuery = "DELETE FROM `gban_admin_group` WHERE (`admin_group_id`='".$id."')";
		$this->db->sql_query($deleteAdminGroupQuery);
	}
  /************************************************************************
	Get the a specific admin group
	************************************************************************/
  function getAdminGroup($groupId) {
    $sql = "SELECT admin_group_id, group_name, description, sm_immunity
            FROM gban_admin_group WHERE admin_group_id = '"+addslashes($groupId)+"'";
    $this->db->sql_query($sql);

    $group = $this->db->get_row();

    $adminGroup = new AdminGroup();

    $adminGroup->setId($group[$i]['admin_group_id']);
    $adminGroup->setName(stripslashes($group[$i]['group_name']));
    $adminGroup->setDescription(stripslashes($group[$i]['description']));
    $adminGroup->setSmImmunity($group[$i]['sm_immunity']);

    return $adminGroup;
  }

  /************************************************************************
	Get the list of admin groups.
	************************************************************************/
  function getAdminGroups() {
    $sql = "SELECT admin_group_id, group_name, description, sm_immunity FROM gban_admin_group ORDER BY group_name ASC";
    $this->db->sql_query($sql);

    $groups = $this->db->get_array();

    $adminGroups = array();

    for($i=0; $i<count($groups); $i++) {
      $adminGroup = new AdminGroup();

      $adminGroup->setId($groups[$i]['admin_group_id']);
      $adminGroup->setName(stripslashes($groups[$i]['group_name']));
      $adminGroup->setDescription(stripslashes($groups[$i]['description']));
      $adminGroup->setSmImmunity($groups[$i]['sm_immunity']);
      // Done getting servers associated with this server group

      array_push($adminGroups, $adminGroup); // Add the server object to the array
    }

    return $adminGroups;
  }

  /************************************************************************
	Get the list of admin groups for a sepcific server and gets the plugin flags of that group.
	************************************************************************/
  function getAdminGroupsOfServer($serverId, $plugin) {
    // Get the list of admin groups for this server
    $sql = "SELECT admin_group_id, group_name, description, sm_immunity
            FROM gban_admin_group
            WHERE admin_group_id IN (SELECT DISTINCT admin_group_id
                                     FROM gban_server_admin
                                     WHERE server_id = '".addslashes($serverId)."')
            ORDER BY group_name ASC";
    $this->db->sql_query($sql);

    $groups = $this->db->get_array();

    $adminGroups = array();

    for($i=0; $i<count($groups); $i++) {
      $adminGroup = new AdminGroup();

      $adminGroup->setId($groups[$i]['admin_group_id']);
      $adminGroup->setName(stripslashes($groups[$i]['group_name']));
      $adminGroup->setDescription(stripslashes($groups[$i]['description']));
      $adminGroup->setSmImmunity($groups[$i]['sm_immunity']);
      // Done getting servers associated with this server group

      // Get flags for a group
      $sql = "SELECT pf.flag
              FROM gban_admin_group_flag agf, gban_plugin_flag pf
              WHERE agf.admin_group_id = '".$adminGroup->getId()."'
              AND pf.plugin_flag_id = agf.plugin_flag_id
              AND pf.plugin = '".addslashes($plugin)."'
              AND agf.enabled = 1";

      $this->db->sql_query($sql);

      $flags = $this->db->get_array();

      $flagString = "";

      for($j=0; $j<count($flags); $j++) {
        $flagString .= $flags[$j]['flag'] . " ";
      }

      $adminGroup->setFlags($flagString);

      array_push($adminGroups, $adminGroup); // Add the server object to the array
    }

    return $adminGroups;
  }

  /************************************************************************
	Get the list of admin groups for a sepcific server and gets the plugin flags of that group.
	************************************************************************/
  function getAdminGroupsOfGroup($groupId, $plugin) {
    // Get the list of admin groups for this server group
    $sql = "SELECT admin_group_id, group_name, description, sm_immunity
            FROM gban_admin_group
            WHERE admin_group_id IN (SELECT DISTINCT admin_group_id
                                     FROM gban_group_admin
                                     WHERE server_group_id = '".addslashes($groupId)."')
            ORDER BY group_name ASC";
    $this->db->sql_query($sql);

    $groups = $this->db->get_array();

    $adminGroups = array();

    for($i=0; $i<count($groups); $i++) {
      $adminGroup = new AdminGroup();

      $adminGroup->setId($groups[$i]['admin_group_id']);
      $adminGroup->setName(stripslashes($groups[$i]['group_name']));
      $adminGroup->setDescription(stripslashes($groups[$i]['description']));
      $adminGroup->setSmImmunity($groups[$i]['sm_immunity']);
      // Done getting servers associated with this server group

      // Get flags for a group
      $sql = "SELECT pf.flag
              FROM gban_admin_group_flag agf, gban_plugin_flag pf
              WHERE agf.admin_group_id = '".$adminGroup->getId()."'
              AND pf.plugin_flag_id = agf.plugin_flag_id
              AND pf.plugin = '".addslashes($plugin)."'
              AND agf.enabled = 1";

      $this->db->sql_query($sql);

      $flags = $this->db->get_array();

      $flagString = "";

      for($j=0; $j<count($flags); $j++) {
        $flagString .= $flags[$j]['flag'] . " ";
      }

      $adminGroup->setFlags($flagString);

      array_push($adminGroups, $adminGroup); // Add the server object to the array
    }

    return $adminGroups;
  }

  /************************************************************************
	List of available admin mods for a group
	************************************************************************/
  function getUnaddedPluginList($adminGroup) {
    $sql = "SELECT plugin, name, description FROM gban_plugin WHERE plugin NOT IN (
              SELECT DISTINCT gp.plugin FROM gban_plugin gp, gban_plugin_flag pf, gban_admin_group_flag agf
              WHERE admin_group_id = '".$adminGroup."' AND pf.plugin_flag_id = agf.plugin_flag_id
              AND gp.plugin = pf.plugin
            )
            ORDER BY plugin ASC";

    $this->db->sql_query($sql);

    $plugins = $this->db->get_array();

    $pluginList = array();

    for($i=0; $i<count($plugins); $i++) {
      $plugin = new Plugin();

      $plugin->setId($plugins[$i]['plugin']);
      $plugin->setName(stripslashes($plugins[$i]['name']));
      $plugin->setDescription(stripslashes($plugins[$i]['description']));

      array_push($pluginList, $plugin);
    }

    return $pluginList;
  }

  /************************************************************************
	List of admin mods a group has
	************************************************************************/
  function getPluginList($adminGroup) {
    $sql = "SELECT plugin, name, description FROM gban_plugin WHERE plugin IN (
              SELECT DISTINCT gp.plugin FROM gban_plugin gp, gban_plugin_flag pf, gban_admin_group_flag agf
              WHERE admin_group_id = '".$adminGroup."' AND pf.plugin_flag_id = agf.plugin_flag_id
              AND gp.plugin = pf.plugin
            )
            ORDER BY plugin ASC";

    $this->db->sql_query($sql);

    $plugins = $this->db->get_array();

    $pluginList = array();

    for($i=0; $i<count($plugins); $i++) {
      $plugin = new Plugin();

      $plugin->setId($plugins[$i]['plugin']);
      $plugin->setName(stripslashes($plugins[$i]['name']));
      $plugin->setDescription(stripslashes($plugins[$i]['description']));

      array_push($pluginList, $plugin);
    }

    return $pluginList;
  }

  /************************************************************************
	Add the plugin powers to a group
	************************************************************************/
  function addPluginToGroup($adminGroup, $plugin) {
    $sql = "INSERT INTO gban_admin_group_flag (admin_group_id, plugin_flag_id)
            SELECT ".$adminGroup." as admin_group_id, plugin_flag_id FROM gban_plugin_flag WHERE plugin='".addslashes($plugin)."'";

    $this->db->sql_query($sql);
  }

  /************************************************************************
	Get the list of flags for a specific group and plugin.
	************************************************************************/
  function getGroupPluginPowers($adminGroup, $plugin) {
    $sql = "SELECT agf.plugin_flag_id, pf.flag, pf.description, agf.enabled FROM gban_admin_group_flag agf, gban_plugin_flag pf
            WHERE agf.plugin_flag_id = pf.plugin_flag_id
            AND agf.admin_group_id = '".$adminGroup."'
            AND pf.plugin = '".$plugin."'
            ORDER BY description ASC";

    $this->db->sql_query($sql);

    $flags = $this->db->get_array();

    $flagList = array();

    for($i=0; $i<count($flags); $i++) {
      $pluginFlag = new PluginFlag();

      $pluginFlag->setAdminGroupId($adminGroup);
      $pluginFlag->setPluginFlagId($flags[$i]['plugin_flag_id']);
      $pluginFlag->setEnabled($flags[$i]['enabled']);
      $pluginFlag->setFlag(stripslashes($flags[$i]['flag']));
      $pluginFlag->setDescription(stripslashes($flags[$i]['description']));

      array_push($flagList, $pluginFlag);
    }

    return $flagList;
  }

  /************************************************************************
	Update the status of a flag for a specific group
	************************************************************************/
  function updateGroupPluginFlag($groupId, $flagId, $checked) {
    $sql = "UPDATE gban_admin_group_flag SET enabled = '".addslashes($checked)."'
            WHERE admin_group_id = '".addslashes($groupId)."' AND plugin_flag_id = '".addslashes($flagId)."'";

    $this->db->sql_query($sql);

    return 1;
  }

  /************************************************************************
	Removes a plugin from a group
	************************************************************************/
  function removePlugin($groupId, $plugin) {
    $sql = "DELETE FROM gban_admin_group_flag
            WHERE admin_group_id = '".addslashes($groupId)."'
            AND plugin_flag_id IN (SELECT plugin_flag_id FROM gban_plugin_flag WHERE plugin='".addslashes($plugin)."')";

    $this->db->sql_query($sql);
  }

  /************************************************************************
	Adds missing admin flags to the specified group for a specific plugin
	************************************************************************/
  function addMissingAdminFlags($groupId, $plugin) {
    $sql = "INSERT INTO gban_admin_group_flag (admin_group_id, plugin_flag_id)
            SELECT '".addslashes($groupId)."', plugin_flag_id FROM gban_plugin_flag
            WHERE plugin = '".addslashes($plugin)."'
            AND plugin_flag_id NOT IN (SELECT plugin_flag_id FROM gban_admin_group_flag
                                        WHERE admin_group_id = '".addslashes($groupId)."')";

    $this->db->sql_query($sql);
  }
  
  /************************************************************************
	This will update the group name of an admin group
	************************************************************************/
  function updateAdminGroupName($groupId, $groupname) {
    $sql = "UPDATE gban_admin_group SET group_name = '".addslashes($groupname)."' WHERE admin_group_id = '".addslashes($groupId)."'";
    
    $this->db->sql_query($sql);
  }
  
  /************************************************************************
	This will update the description of an admin group
	************************************************************************/
  function updateAdminGroupDescription($groupId, $description) {
    $sql = "UPDATE gban_admin_group SET description = '".addslashes($description)."' WHERE admin_group_id = '".addslashes($groupId)."'";
    
    $this->db->sql_query($sql);
  }
}
?>
