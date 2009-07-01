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

require_once(ROOTDIR."/include/database/class.UserQueries.php");
require_once(ROOTDIR."/include/database/class.ServerQueries.php");
require_once(ROOTDIR."/include/database/class.AdminGroupQueries.php");
require_once(ROOTDIR."/include/objects/class.PluginFlag.php");

$userQueries = new UserQueries();

if($_GET['serverId'] > 0) {
  $serverId = $_GET['serverId'];
}

$serverQueries = new ServerQueries();
$server = $serverQueries->getServer($serverId);
$adminGroupQueries = new AdminGroupQueries();

$groupId = $server->getGroupId();

// If we have a valid group id, get the admins of that group
if($groupId > 0) {
  $users = $userQueries->getGroupAdmins($groupId);
  $adminGroups = $adminGroupQueries->getAdminGroupsOfGroup($groupId, 'gban'); // Admin groups
} else {
  $users = $userQueries->getServerAdmins($serverId);
  $adminGroups = $adminGroupQueries->getAdminGroupsOfServer($serverId, 'gban'); // Admin groups
}

// If the above return no values, that means we should use the users list
if(count($users) < 1) {
  $users = $userQueries->getUsers();
}

echo "\"clanMembers\"";
echo "{";

// Cycle through each user and create the keygroup
foreach($users as $user) {
  
  // If they belong to an admin group, check to see if they have gban powers
  // Otherwise if they belong to group 0, use the power from the gban users page
  if($user->getAdminGroupId() > 0) {  
    // Cycle through each admin group
    foreach($adminGroups as $adminGroup) {
      // Add them if they do exist
      if($user->getAdminGroupId() == $adminGroup->getId()) {
        // Get the flags they have in gban
        $flags = $adminGroup->getFlags();
        $user->setFlags($flags);
        
        // Determine if they have member powers
        $findme = 'm';
        $pos = strpos($flags, $findme);
        
        if($pos > -1)
          $user->setAccessLevel(4);
          
        // Determine if they have admin powers
        $findme = 'a';
        $pos = strpos($flags, $findme);
        
        if($pos > -1)
          $user->setAccessLevel(3);
          
        // Determine if they have ban manager powers
        $findme = 'b';
        $pos = strpos($flags, $findme);
        
        if($pos > -1)
          $user->setAccessLevel(2);
        
        printUser($user);
      }
    }
  } else {
    // Only add them if they are active and have powers
    // It will also go to here if the "gban plugin" is not added on the admin group page to the group
    printUser($user);
  }
}

function printUser($user) {
  if($user->getActive() == 1 && $user->getAccessLevel() != 5) {
      $admin = 0;
      echo "  \"".$user->getSteamId()."\"";
      echo "  {";
      // Determine if the user has "admin" privileges
      // 4 is member only
      if($user->getAccessLevel() == 4) {
        $admin = 0;
      } else {
        $admin = 1;
      }

      echo "    \"name\"      \"".$user->getName()."\"";
      echo "    \"member\"    \"1\"";
      echo "    \"admin\"     \"".$admin."\"";
      echo "    \"access\"    \"".$user->getAccessLevel()."\"";

      echo "  }";
    }
}
?>
}
