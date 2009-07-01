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

if($_GET['serverId'] > 0) {
  $serverId = $_GET['serverId'];
}

$userQueries = new UserQueries();
$serverQueries = new ServerQueries();
$adminGroupQueries = new AdminGroupQueries();

$server = $serverQueries->getServer($serverId);

$groupId = $server->getGroupId();

// If we have a valid group id, get the admins of that group
if($groupId > 0) {
  $users = $userQueries->getGroupAdmins($groupId); // Admins of a server group
  $adminGroups = $adminGroupQueries->getAdminGroupsOfGroup($groupId, 'mani'); // Admin groups
  $immunityGroups = $adminGroupQueries->getAdminGroupsOfGroup($groupId, 'mani-immunity'); // Immunity Groups
} else {
  $users = $userQueries->getServerAdmins($serverId); // Admins of a server
  $adminGroups = $adminGroupQueries->getAdminGroupsOfServer($serverId, 'mani'); // Admin groups
  $immunityGroups = $adminGroupQueries->getAdminGroupsOfServer($serverId, 'mani-immunity'); // Immunity groups
}

// If the above return no values, that means we should use the users list
if(count($users) < 1) {
  $users = $userQueries->getUsers();
}

echo "\"clients.txt\"\n";
echo "{\n";
echo "  \"version\"	\"1\"\n";

echo "  \"players\"\n";
echo "  {\n";

foreach($users as $user) {
  if($user->getActive() == 1) {
    echo "    \"".$user->getName()."\"\n";
    echo "    {\n";
    echo "       \"name\"    \"".$user->getName()."\"\n";
    echo "       \"steam\"   \"".$user->getSteamId()."\"\n";
    echo "       \"groups\"\n";
    echo "       {\n";
    echo "        \"Admin\"   \"".$user->getAdminGroupName()."\"\n";
    echo "        \"Immunity\"   \"".$user->getAdminGroupName()." Immune\"\n";
    echo "       }\n";
    echo "    }\n\n";
  }
}
echo "  }\n";
  
echo "  \"groups\"\n";
echo "  {\n";
echo "    \"Admin\"\n";
echo "    {\n";

foreach($adminGroups as $adminGroup) {
  // Sepcial replace to remove the special flag "reserved" as it is not a true mani flag
  // but a special flag added for GBan to manage mani reserved slots
  echo "      \"".$adminGroup->getName()."\"   \"".str_replace("reserved ", "", $adminGroup->getFlags())."\"\n";
}
echo "    }\n";
echo "    \"Immunity\"\n";
echo "    {\n";

foreach($immunityGroups as $immunityGroup) {
  echo "      \"".$immunityGroup->getName()." Immune\"   \"".$immunityGroup->getFlags()."\"\n";
}
echo "    }\n";
echo "  }\n\n";
echo "}\n";