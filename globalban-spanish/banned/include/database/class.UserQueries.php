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

require_once(ROOTDIR."/include/objects/class.User.php"); // Used to store user info
require_once(ROOTDIR."/include/objects/class.BannedUser.php"); // Used to store banned user info
require_once(ROOTDIR."/include/database/class.Database.php"); //Database class
require_once(ROOTDIR."/config/class.Config.php");

class UserQueries extends Config {	

	// Variables
	var $db; // Object of database class
	var $user; // Object to store user info
	
	// Default constructor
	function __construct() {
		$this->init();
  }
  
	function UserQueries() {
		$this->init();
  }
  
  function init() {
    $this->db = new Database; // Create new database connection
		$this->user = new User; // Create a User object
  }
		
	/************************************************************************
	Check for username existance in database 
	Return ture if the username is in the database
	************************************************************************/
	function usernameExist($name) {	
		
		// Query the database to see if the specified email exists
		$this->db->sql_query("SELECT admin_id FROM gban_admins WHERE name='".addslashes($name)."'");
		
		// It does exist
		if($this->db->get_row()) {
			return true;
		}
		
		return false;
	}
	
	/************************************************************************
	Determine if the steam id passed in belongs to a member
	************************************************************************/
	function isMember($steamId) {
    $steamId = trim($steamId);
    
    // Query the database to see if the specified email exists
		$this->db->sql_query("SELECT admin_id FROM gban_admins WHERE steam_id='".addslashes($steamId)."'");

		// It does exist
		if($this->db->get_row()) {
			return true;
		}

		return false;
  }
	
	/************************************************************************
	Check for email existance in database 
	Return true if the email is in the database
	************************************************************************/
	function emailExist($email) {	
		
		// Query the database to see if the specified email exists
		$this->db->sql_query("SELECT admin_id FROM gban_admins WHERE email='".addslashes($email)."'");
		
		// It does exist
		if($this->db->get_row()) {
			return true;
		}
		
		return false;
	}
	
	/************************************************************************
	Add new user to the database 
	************************************************************************/
	function addUser($name, $password, $accessLevel, $steamId, $email) {
		
		// Check if the fields have values before inserting
		if(!empty($name) || !empty($password) || !empty($steamId)) {		
			// Encrypt password
			$this->db->sql_query("INSERT INTO gban_admins (name, password, email, access_level) 
			values ('".addslashes($name)."', 
			'".md5($password)."', 
			'".addslashes($email)."',
			'".addslashes($accessLevel)."')");
			
			$this->db->sql_query("SELECT admin_id FROM gban_admins WHERE name = '".addslashes($name)."'");
			
			$adminId = $this->db->get_row();
			$adminId = $adminId['admin_id'];

			// Add their steam id to the database
			$this->db->sql_query("INSERT INTO gban_admin_steam (admin_id, steam_id)
      values('".$adminId."', '".addslashes($steamId)."')");
			
			// Make sure query executed succesfully
			if($this->db->sql_result) {	
				return true;
			}
			else {
				return false;
			}
		}
		return false;
	}
	
 /************************************************************************
	Add new smf user to the gban admin table
	************************************************************************/
	function addSmfUser($id, $steamId) {

		// Check if the fields have values before inserting
		if(!empty($steamId)) {
			// Add their steam id to the database
			$this->db->sql_query("INSERT INTO gban_admin_steam (admin_id, steam_id)
      values('".$id."', '".addslashes($steamId)."')");

			// Make sure query executed succesfully
			if($this->db->sql_result) {
				return true;
			}
			else {
				return false;
			}
		}
		return false;
	}
	
	/************************************************************************
	Verify that the username and password match those in the database
	************************************************************************/
	function verifyUser($name, $password) {
	   	
		$this->db->sql_query("SELECT admin_id 
                          FROM gban_admins 
		                      WHERE name='".addslashes($name)."' AND password='".md5($password)."'");
				
		// Determine if verification passed or not
		if($this->db->get_row()) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/************************************************************************
	Get everything about the user and store it to a user object
	************************************************************************/
	function getUserInfo($username) {

    if($this->enableSmfIntegration) {
      $this->db->sql_query("SELECT gas.admin_id, sm.memberName, sm.ID_GROUP, sm.additonalGroups, sm.emailAddress, gas.active
                            FROM gban_admin_steam gas, ".$this->smfTablePrefix."members sm
                            WHERE sm.memberName = '".addslashes($username)."'
                            AND sm.ID_MEMBER = gas.admin_id");

      $info = $this->db->get_row();

      if($info) {
  			// Set variables in user object
  			$this->user->setId($info['admin_id']);
  			$this->user->setName($info['memberName']);
  			$this->user->setAccessLevel($this->getAccessLevel($info['ID_GROUP'], $info['additionalGroups']));
  			$this->user->setEmail($info['emailAddress']);
  			$this->user->setActive($info['active']);

  			return $this->user; // Return user object
  		}
    } else {
  		$this->db->sql_query("SELECT ga.admin_id, ga.name, ga.password, ga.access_level, ga.email, gas.steam_id, gas.active
  		                      FROM gban_admins ga, gban_admin_steam gas
                            WHERE name='".addslashes($username)."'
                            AND ga.admin_id = gas.admin_id");

  		$info = $this->db->get_row();

  		if($info) {
  			// Set variables in user object
  			$this->user->setId($info['admin_id']);
  			$this->user->setName($info['name']);
  			$this->user->setPassword($info['password']);
  			$this->user->setAccessLevel($info['access_level']);
  			$this->user->setEmail($info['email']);
  			$this->user->setSteamId($info['steam_id']);
  			$this->user->setActive($info['active']);

  			return $this->user; // Return user object
  		}
  		else {
  			return new User();
  		}
		}
		return new User(); // We should never get to here
	}
	
	/************************************************************************
	Get everything about the user and store it to a user object
	************************************************************************/
	function getUserInfoById($id) {
    
    if($this->enableSmfIntegration) {
      $this->db->sql_query("SELECT gas.admin_id, sm.memberName, sm.ID_GROUP, sm.additionalGroups, sm.emailAddress
                            FROM gban_admin_steam gas, ".$this->smfTablePrefix."members sm
                            WHERE gas.admin_id = '".$id."'
                            AND sm.ID_MEMBER = gas.admin_id");

  		$info = $this->db->get_row();

  		if($info) {
  			// Set variables in user object
  			$this->user->setId($info['admin_id']);
  			$this->user->setName($info['memberName']);
  			$this->user->setAccessLevel($this->getAccessLevel($info['ID_GROUP'], $info['additionalGroups']));
  			$this->user->setEmail($info['emailAddress']);

  			return $this->user; // Return user object
  		}
  		else {
  			return new User();
  		}
    } else {
        if($id==-1){
            $this->user->setId('-1');
            $this->user->setName('Console');
            $this->user->setBannerSteamId('N/A');
            
            return $this->user;
        } else {
            $this->db->sql_query("SELECT ga.admin_id, ga.name, ga.password, ga.access_level, ga.email, gas.steam_id
                                  FROM gban_admins ga, gban_admin_steam gas
                                WHERE ga.admin_id='".$id."'
                                AND ga.admin_id = gas.admin_id");

            $info = $this->db->get_row();

            if($info) {
                // Set variables in user object
                $this->user->setId($info['admin_id']);
                $this->user->setName($info['name']);
                $this->user->setPassword($info['password']);
                $this->user->setAccessLevel($info['access_level']);
                $this->user->setEmail($info['email']);

                return $this->user; // Return user object
            } else {
                return new User();
            }
        }    
	}
	}
	
	/************************************************************************
	Get everything about the user and store it to a user object
	************************************************************************/
	function getUserInfoBySteamId($steamId) {
	
    if($this->enableSmfIntegration) {
  		$this->db->sql_query("SELECT gas.admin_id, sm.memberName, sm.ID_GROUP, sm.additionalGroups, sm.emailAddress
                            FROM gban_admin_steam gas, ".$this->smfTablePrefix."members sm
                            WHERE gas.steam_id = '".addslashes($steamId)."'
                            AND sm.ID_MEMBER = gas.admin_id");
                            
      $info = $this->db->get_row();
      
      if($info) {
  			// Set variables in user object
  			$this->user->setId($info['admin_id']);
  			$this->user->setName($info['memberName']);
  			$this->user->setAccessLevel($this->getAccessLevel($info['ID_GROUP'], $info['additionalGroups']));
  			$this->user->setEmail($info['emailAddress']);

  			return $this->user; // Return user object
  		}
  		else {
  			return new User();
  		}
    } else {
      $this->db->sql_query("SELECT ga.admin_id, ga.name, ga.password, ga.access_level, ga.email, gas.steam_id
  		                      FROM gban_admins ga, gban_admin_steam gas
                            WHERE gas.steam_id ='".$steamId."'
                            AND ga.admin_id = gas.admin_id");
                            
      $info = $this->db->get_row();

  		if($info) {
  			// Set variables in user object
  			$this->user->setId($info['admin_id']);
  			$this->user->setName($info['name']);
  			$this->user->setPassword($info['password']);
  			$this->user->setAccessLevel($info['access_level']);
  			$this->user->setEmail($info['email']);

  			return $this->user; // Return user object
  		}
  		else {
  			return new User();
  		}
    }
		return new User(); // We should never get to here
	}
	
	/************************************************************************
	This method updates the info in the database with the info found in 
	the $user object.
	************************************************************************/
	function updateUser($user) {
	  // Update the gban_admins table if SMF integration is off
    if(!$this->enableSmfIntegration) {
  		$this->db->sql_query("UPDATE gban_admins SET
  			name='".$user->getName()."',
  			email='".$user->getEmail()."',
  			access_level='".$user->getAccessLevel()."',
  			password='".$user->getPassword()."'
  			WHERE admin_id='".$user->getId()."'");
		}
		// Update their steam id
	  $this->db->sql_query("UPDATE gban_admin_steam SET 
                          steam_id = '".$user->getSteamId()."'
                          WHERE admin_id = '".$user->getId()."'");
	}
	
	/************************************************************************
	Get the list of users in the database
	************************************************************************/
	function getUsers() {
	
    if($this->enableSmfIntegration) {
      $this->db->sql_query("SELECT sm.ID_MEMBER, sm.memberName, sm.ID_GROUP, sm.additionalGroups, sm.emailAddress, gas.steam_id, gas.active
                            FROM ".$this->smfTablePrefix."members sm, gban_admin_steam gas
                            WHERE sm.ID_MEMBER = gas.admin_id
                            ORDER BY sm.ID_GROUP, UPPER(sm.memberName) ASC");

  		$users = $this->db->get_array();

  		$userList = array(); // Array of user objects

  		for($i=0; $i<count($users); $i++) {
        $user = new User();

        $user->setId($users[$i]['ID_MEMBER']);
  			$user->setName(stripslashes($users[$i]['memberName']));
  			$user->setAccessLevel($this->getAccessLevel($users[$i]['ID_GROUP'], $users[$i]['additionalGroups']));
  			$user->setEmail($users[$i]['emailAddress']);
  			$user->setSteamId($users[$i]['steam_id']);
  			$user->setActive($users[$i]['active']);

        // Do not add those that fail
        if($user->getAccessLevel() > 0) {
          array_push($userList, $user); // Add the user object to the array
        }
      }
      
      usort($userList, array("User", "cmp_obj"));
      
      return $userList;
    } else {
      $this->db->sql_query("SELECT ga.admin_id, ga.name, ga.access_level, ga.email, gas.steam_id, gas.active
                            FROM gban_admins ga, gban_admin_steam gas
                            WHERE ga.admin_id = gas.admin_id
                            ORDER BY ga.access_level, UPPER(ga.name) ASC");

  		$users = $this->db->get_array();

  		$userList = array(); // Array of user objects

  		for($i=0; $i<count($users); $i++) {
        $user = new User();

        $user->setId($users[$i]['admin_id']);
  			$user->setName(stripslashes($users[$i]['name']));
  			$user->setAccessLevel($users[$i]['access_level']);
  			$user->setEmail($users[$i]['email']);
  			$user->setSteamId($users[$i]['steam_id']);
  			$user->setActive($users[$i]['active']);

        array_push($userList, $user); // Add the user object to the array
      }

      return $userList;
    }
    return array();
  }
  
  /************************************************************************
	Get a list of SMF users that are in 1 of the 4 groups and not added to
	the admin list.
	************************************************************************/
  function getSMFUsers() {
    $this->db->sql_query("SELECT sm.ID_MEMBER, sm.memberName, sm.ID_GROUP, sm.additionalGroups, sm.emailAddress
                            FROM ".$this->smfTablePrefix."members sm
                            WHERE sm.ID_MEMBER NOT IN (
                            SELECT gas.admin_id
                            FROM gban_admin_steam gas)
                          ");

		$users = $this->db->get_array();

		$userList = array(); // Array of user objects

		for($i=0; $i<count($users); $i++) {
      $user = new User();

      $user->setId($users[$i]['ID_MEMBER']);
			$user->setName(stripslashes($users[$i]['memberName']));
			$user->setAccessLevel($this->getAccessLevel($users[$i]['ID_GROUP'], $users[$i]['additionalGroups']));
			$user->setEmail($users[$i]['emailAddress']);

      // Do not add those that fail
      if($user->getAccessLevel() > 0) {
        array_push($userList, $user); // Add the user object to the array
      }
    }

    usort($userList, array("User", "cmp_obj"));

    return $userList;
  }
  
  /************************************************************************
	Delete the user
	************************************************************************/
  function deleteUser($id){
    if($this->enableSmfIntegration) {
      // Delete them from the steam id table first as it is a foreign key to gban_admins
      $this->db->sql_query("DELETE FROM gban_admin_steam
                            WHERE admin_id = '".$id."'");
      return true;
    } else {
      // Do not allow deletion of super users, especially if there is only 1 left
      $this->db->sql_query("SELECT count(*) as count FROM gban_admins
                            WHERE access_level = 1");

      $count = $this->db->get_row();
      $count = $count['count']; // Number of super-users

      // Get information about user about to be deleted
      $userObj = $this->getUserInfoById($id);

      // There needs to be at least 1 super user account for a delete to be allowed
      // As super users are the only ones allowed to delete
      if($count > 0) {
        // However, if there is only 1 super-user, they should not be allowed to
        // be deleted, all other users can be though
        if($count == 1 && $userObj->getAccessLevel() == 1) {
          return false; // Trying to delete last super-user (bad)
        } else {
          // Delete them from the steam id table first as it is a foreign key to gban_admins
          $this->db->sql_query("DELETE FROM gban_admin_steam
                                WHERE admin_id = '".$id."'");

          // The user can be deleted once their steam id has been removed
          $this->db->sql_query("DELETE FROM gban_admins
                                WHERE admin_id = '".$id."'");

          return true;
        }
      } else {
        return false;
      }
    }
  }
  
  /************************************************************************
	If the user forgets their current password, randomly generate a new password 
	and email it to them.
	This can be used by the user themselves or the super admin on the admin 
	management page for resetting passwords.
	************************************************************************/
  function forgotPassword($email) {
  
    // make sure a valid email type of address was entered
		if(!preg_match("/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/i", $email)) {
			return false;
		}
    
    // only do this if the email does exist
    if($this->emailExist($email)) {
      $pass = $this->createRandomPassword();
  
      // Update the database (email is unique)
      $this->db->sql_query("UPDATE gban_admins SET password = '".md5($pass)."'
                            WHERE email = '".addslashes($email)."'");

      $this->db->sql_query("SELECT name FROM gban_admins WHERE LOWER(email) = LOWER('".$email."')");
      $username = $this->db->get_row();
      $username = $username['name'];
      
      // Use this to build the URL link (replace processWebBanUpdate with updateBan)
      $url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
      $url = str_replace('manageUsers&adminPage=1', 'login', $url);
      
      // Send the email
      // To send HTML mail, the Content-type header must be set
      $headers  = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";			
      // Additional headers
      $headers .= "From: ".$this->siteName." Ban Management <".$this->emailFromHeader.">" . "\r\n";
      
      $subject = $this->siteName." Ban Management Password Reset";
      
      $body = "<html><body>";
      $body .= "<h2>".$this->siteName."- GlobalBan Password Reset</h2>";
      $body .= "<br/><p>Username: ".$username."<br/><br/>New Random Password: ".$pass."</p>";
      $body .= "<br/><br/>You may login in you accont following the new link: <a href='".$url."'>Admin Login</a>";
      $body .= "<p>Please update your profile once logged in with a new password of your choice.</p>";
      $body .= "</body></html>";
      
      mail($email, $subject, $body, $headers);
      
      return true;
    }
    return false;
  }
  
  /************************************************************************
	Generate a random password
	************************************************************************/
  function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = ''; // This is the new password

    while ($i <= 7) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    
    return $pass;
  }
  
  /************************************************************************
	Get the accessLevel based on the groupId.  This is only used for SMF
	integration.
	************************************************************************/
  function getAccessLevel($groupId, $otherGroups) {
    if($this->enableSmfIntegration) {
      $otherGroups = split(',', $otherGroups);
      if($groupId == $this->fullPowerGroup || in_array($this->fullPowerGroup, $otherGroups)) {
        return 1;
      } else if($groupId == $this->banManagerGroup || in_array($this->banManagerGroup, $otherGroups)) {
        return 2;
      } else if($groupId == $this->adminGroup || in_array($this->adminGroup, $otherGroups)) {
        return 3;
      } else if($groupId == $this->memberGroup || in_array($this->memberGroup, $otherGroups)) {
        return 4;
      } else if($groupId == $this->noPowerGroup || in_array($this->noPowerGroup, $otherGroups)) {
        return 5;
      }else  {
        return -1; // Invalid group found
      }
    } else {
      return $groupId;
    }
  }
  
  /************************************************************************
	Update the active status of a user.
	************************************************************************/
  function updateUserActiveStatus($active, $id) {
    $this->db->sql_query("UPDATE gban_admin_steam SET active = '".$active."' WHERE admin_id = '".$id."'");
  }
  
  /************************************************************************
	Get the list of admins not currently added to the selected server.
	************************************************************************/
  function getUnAddedServerAdmins($serverId, $groupId) {

    // SMF query
    if($this->enableSmfIntegration) {
      if($groupId > 0) {
        $sql = "SELECT sm.ID_MEMBER as admin_id, sm.memberName as name, sm.ID_GROUP, sm.additionalGroups
                FROM ".$this->smfTablePrefix."members sm
                WHERE sm.ID_MEMBER NOT IN (
                  SELECT sa.admin_id
                  FROM gban_group_admin sa
                  WHERE server_group_id = '".addslashes($groupId)."'
                  )
                ORDER BY name ASC";
      } else {
        $sql = "SELECT sm.ID_MEMBER as admin_id, sm.memberName as name, sm.ID_GROUP, sm.additionalGroups
                FROM ".$this->smfTablePrefix."members sm
                WHERE sm.ID_MEMBER NOT IN (
                  SELECT sa.admin_id
                  FROM gban_server_admin sa
                  WHERE server_id = '".addslashes($serverId)."'
                  )
                ORDER BY name ASC";
      }
    } else { // Standalone query
      if($groupId > 0) {
               
        $sql = "SELECT a.admin_id, a.name
                FROM gban_admins a
                Inner Join gban_admin_steam s ON a.admin_id = s.admin_id
                WHERE s.active = '1' AND a.admin_id NOT IN (
                    SELECT sa.admin_id
                    FROM gban_group_admin sa
                    WHERE server_group_id = '".addslashes($groupId)."'
                    )
                ORDER BY a.name ASC";
      } else {
        $sql = "SELECT a.admin_id, a.name
                FROM gban_admins a
                Inner Join gban_admin_steam s ON a.admin_id = s.admin_id
                WHERE s.active = '1' AND a.admin_id NOT IN (
                    SELECT sa.admin_id
                    FROM gban_group_admin sa
                    WHERE server_id = '".addslashes($serverId)."'
                    )
                ORDER BY a.name ASC";
      }
    }
    $this->db->sql_query($sql);
    
    $users = $this->db->get_array();

		$userList = array(); // Array of user objects

		for($i=0; $i<count($users); $i++) {
      $user = new User();

      $user->setId($users[$i]['admin_id']);
			$user->setName(stripslashes($users[$i]['name']));
      
      if($this->enableSmfIntegration) {
        $user->setAccessLevel($this->getAccessLevel($users[$i]['ID_GROUP'], $users[$i]['additionalGroups']));
        
        // Do not add those that fail
        if($user->getAccessLevel() > 0) {
          array_push($userList, $user); // Add the user object to the array
        }
      } else {
        array_push($userList, $user); // Add the user object to the array
      }
    }
    
    return $userList;
  }

  /************************************************************************
	Get the list of admins for a specific server that does not belong to a group.
	************************************************************************/
  function getServerAdmins($serverId) {
    if($this->enableSmfIntegration) {
      // SMF query
      $sql = "SELECT sm.ID_MEMBER as admin_id, sm.memberName as name, sm.additionalGroups, sm.emailAddress as email, gas.steam_id, gas.active, sa.admin_group_id, ag.group_name, sm.ID_GROUP, sm.additionalGroups
              FROM ".$this->smfTablePrefix."members sm, gban_admin_steam gas, gban_server_admin sa
              LEFT OUTER JOIN gban_admin_group ag ON ag.admin_group_id = sa.admin_group_id
              WHERE sm.ID_MEMBER = gas.admin_id 
              AND gas.admin_id = sa.admin_id
              AND sa.server_id = '".addslashes($serverId)."'
              AND sm.ID_MEMBER IN (
                SELECT sa.admin_id
                FROM gban_server_admin sa
                WHERE server_id = '".addslashes($serverId)."'
                )
             ORDER BY UPPER(sm.memberName) ASC";
    } else {
      // Standalone query
      $sql = "SELECT a.admin_id, a.name, a.access_level, a.email, gas.steam_id, gas.active, sa.admin_group_id, ag.group_name
              FROM gban_admins a, gban_admin_steam gas, gban_server_admin sa 
              LEFT OUTER JOIN gban_admin_group ag ON ag.admin_group_id = sa.admin_group_id
              WHERE a.admin_id = gas.admin_id 
              AND gas.admin_id = sa.admin_id
              AND sa.server_id = '".addslashes($serverId)."'
              AND a.admin_id IN (
                SELECT sa.admin_id
                FROM gban_server_admin sa
                WHERE server_id = '".addslashes($serverId)."'
                )
             ORDER BY sa.admin_group_id ASC, a.name ASC";
    }
    
    $this->db->sql_query($sql);
    
    $users = $this->db->get_array();

		$userList = array(); // Array of user objects

		for($i=0; $i<count($users); $i++) {
      $user = new User();

      $user->setId($users[$i]['admin_id']);
			$user->setName(stripslashes($users[$i]['name']));
			
			if($this->enableSmfIntegration) {
        $user->setAccessLevel($this->getAccessLevel($users[$i]['ID_GROUP'], $users[$i]['additionalGroups']));
			} else {
        $user->setAccessLevel($users[$i]['access_level']);
      }
			
			$user->setEmail($users[$i]['email']);
			$user->setSteamId($users[$i]['steam_id']);
			$user->setActive($users[$i]['active']);
      $user->setAdminGroupId($users[$i]['admin_group_id']);
      $user->setAdminGroupName($users[$i]['group_name']);

      array_push($userList, $user); // Add the user object to the array
    }
    
    return $userList;
  }
  
  /************************************************************************
	Get the list of admins for a specific server group.
	************************************************************************/
  function getGroupAdmins($groupId) {
    if($this->enableSmfIntegration) {
      // SMF query
      $sql = "SELECT sm.ID_MEMBER as admin_id, sm.memberName as name, sm.additionalGroups, sm.emailAddress as email, 
              gas.steam_id, gas.active, ga.admin_group_id, sm.ID_GROUP, sm.additionalGroups,
              (SELECT COALESCE(group_name, '') from gban_admin_group WHERE admin_group_id = ga.admin_group_id) as group_name
              FROM ".$this->smfTablePrefix."members sm, gban_admin_steam gas, gban_group_admin ga
              WHERE sm.ID_MEMBER = gas.admin_id 
              AND ga.admin_id = sm.ID_MEMBER
              AND ga.server_group_id = '".addslashes($groupId)."'
              AND sm.ID_MEMBER IN (
                SELECT sa.admin_id
                FROM gban_group_admin sa
                WHERE server_group_id = '".addslashes($groupId)."'
                )
             ORDER BY UPPER(sm.memberName)";
    } else {
      // Standalone query
      $sql = "SELECT a.admin_id, a.name, a.access_level, a.email, gas.steam_id, gas.active, ga.admin_group_id,
              (SELECT COALESCE(group_name, '') from gban_admin_group WHERE admin_group_id = ga.admin_group_id) as group_name
              FROM gban_admins a, gban_admin_steam gas, gban_group_admin ga
              WHERE a.admin_id = gas.admin_id 
              AND ga.admin_id = a.admin_id
              AND ga.server_group_id = '".addslashes($groupId)."'
              AND a.admin_id IN (
                SELECT sa.admin_id
                FROM gban_group_admin sa
                WHERE server_group_id = '".addslashes($groupId)."'
                )
             ORDER BY ga.admin_group_id ASC, a.name ASC";
    }

    $this->db->sql_query($sql);

    $users = $this->db->get_array();

		$userList = array(); // Array of user objects

		for($i=0; $i<count($users); $i++) {
      $user = new User();

      $user->setId($users[$i]['admin_id']);
			$user->setName(stripslashes($users[$i]['name']));
			
			if($this->enableSmfIntegration) {
        $user->setAccessLevel($this->getAccessLevel($users[$i]['ID_GROUP'], $users[$i]['additionalGroups']));
			} else {
        $user->setAccessLevel($users[$i]['access_level']);
      }
			
			$user->setEmail($users[$i]['email']);
			$user->setSteamId($users[$i]['steam_id']);
			$user->setActive($users[$i]['active']);
      $user->setAdminGroupId($users[$i]['admin_group_id']);
      $user->setAdminGroupName($users[$i]['group_name']);

      array_push($userList, $user); // Add the user object to the array
    }

    return $userList;
  }
  
  /************************************************************************
	Remove the admin from either the server group admin list or the server admin
	list.
	************************************************************************/
  function removeAdminFromGroup($adminId, $serverId, $groupId) {
    if($groupId > 0) {
      $sql = "DELETE FROM gban_group_admin WHERE admin_id = '".addslashes($adminId)."'
              AND server_group_id = '".addslashes($groupId)."'";
    } else {
      $sql = "DELETE FROM gban_server_admin WHERE admin_id = '".addslashes($adminId)."'
              AND server_id = '".addslashes($serverId)."'";
    }
    
    $this->db->sql_query($sql);
  }
  
  /************************************************************************
	Update the admin group id of the specified admin
	************************************************************************/
  function updateAdminGroup($serverId, $serverGroupId, $adminGroupId, $adminId) {

    // SMF query
    if($serverGroupId > 0) {
      $sql = "UPDATE gban_group_admin SET admin_group_id = '".addslashes($adminGroupId)."'
              WHERE server_group_id = '".addslashes($serverGroupId)."' ";
      if($adminId != "%")
        $sql .= "AND admin_id = '".addslashes($adminId)."'";
    } else {
      $sql = "UPDATE gban_server_admin SET admin_group_id = '".addslashes($adminGroupId)."'
              WHERE server_id = '".addslashes($serverId)."' ";
      if($adminId != "%")
        $sql .= "AND admin_id = '".addslashes($adminId)."'";
    }

    $this->db->sql_query($sql);
  }
}
?>
