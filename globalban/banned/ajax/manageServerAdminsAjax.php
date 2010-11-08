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

require_once(ROOTDIR."/include/database/class.ServerQueries.php");
require_once(ROOTDIR."/include/objects/class.Server.php");
require_once(ROOTDIR."/include/database/class.UserQueries.php");
require_once(ROOTDIR."/include/objects/class.User.php");
require_once(ROOTDIR."/include/database/class.AdminGroupQueries.php");

// Only those with full privs can access this page
if($fullPower) {

$serverId = 0;
$groupId = 0;

if (isset($_POST['serverId'])) {
	$serverId = $_POST['serverId'];
}
if (isset($_POST['serverGroupId'])) {
	$groupId = $_POST['serverGroupId'];
}
$serverQueries = new ServerQueries();
$userQueries = new UserQueries();

$error = false;

// Set the server Id to nothing so that we are only editing the group
if(isset($_POST['switchGroup'])) {
  $serverId = -1;
}

// Get selected server info
$server = $serverQueries->getServer($serverId);

// This will set the group for the server
if(isset($_POST['setGroup'])) {
  // Only set if a server is selected
  if($serverId > 0) {
    $serverQueries->setServerGroup($serverId, $groupId);
  }
}

// Get the group id when we switch servers
if(empty($groupId) || $groupId == -1 || isset($_POST['switchServer'])) {
  $groupId = $server->getGroupId();
}

if($groupId > 0) {
  $group = $serverQueries->getServerGroup($groupId);
  $groupName = $group->getName();
}

// If this is set, then that means an admin is bein added to a group or server
if(isset($_POST['addAdmin'])) {
  $serverQueries->addAdminToServer($serverId, $groupId, $_POST['addNewAdmin']);
}

// Add all un-added admins to the admin list
if(isset($_POST['addAllAdmin'])) {
  $unAddedUsers = $userQueries->getUnAddedServerAdmins($serverId, $groupId);
  
  foreach($unAddedUsers as $unAddedUser) {
    $serverQueries->addAdminToServer($serverId, $groupId, $unAddedUser->getId());
  }
}

if(isset($_POST['submitDelete'])) {
  $userQueries->removeAdminFromGroup($_POST['adminId'], $serverId, $groupId);
}

// If an "action" is passed, then we know it was from an AJAX call
// Next take action depending on the type
if(isset($_POST['action'])) {
  $action = $_POST['action'];
  
  // We want to delete the selected admins
  if($action = 'deleteSelected') {
    $admins = explode(",", $_POST['admins']);
    
    foreach($admins as $admin) {
      $userQueries->removeAdminFromGroup($admin, $serverId, $groupId);
    }
  }
}

// Get list of users
$unAddedUsers = $userQueries->getUnAddedServerAdmins($serverId, $groupId);
// If the group id is set, that means that we want to edit the group's admin list
if($groupId > 0) {
  $serverAdmins = $userQueries->getGroupAdmins($groupId);
} else {
  $serverAdmins = $userQueries->getServerAdmins($serverId);
}

// Get the list of servers
$serverList = $serverQueries->getServers();

// Get admin groups
$adminGroupQueries = new AdminGroupQueries();
$adminGroups = $adminGroupQueries->getAdminGroups();
?>

<table id="serverAdminsTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
<tr>
  <th class="colColor2" width="1%" nowrap><input type="checkbox" id="selectAll"/></th>
  <th class="colColor1" width="25%" nowrap>Admin</th>
  <th class="colColor2" width="25%" nowrap>Group</th>
  <th class="colColor1" width="49%" nowrap>Remove</th>
</tr>
<?php
foreach($serverAdmins as $serverAdmin) {
?>
<tr>
  <td class="colColor2">
    <input type="checkbox" id="admin-<?php echo $serverAdmin->getId()?>"/>
    <input type="hidden" id="serverId-<?php echo $serverAdmin->getId()?>" value="<?php echo $server->getId()?>"/>
    <input type="hidden" id="groupId-<?php echo $serverAdmin->getId()?>" value="<?php echo $groupId?>"/>
  </td>
  <td class="colColor1" nowrap><?php echo $serverAdmin->getName()?></td>
  <td class="colColor2">
    <select id="adminGroupId-<?php echo $serverAdmin->getId()?>" onchange="updateAdminGroup('<?php echo $server->getId()?>', '<?php echo $groupId?>', this.value, '<?php echo $serverAdmin->getId()?>')">
    <option value="0">-- No Powers --</option>
    <?php
    foreach($adminGroups as $adminGroup) {
      $selected = "";
      if($adminGroup->getId() == $serverAdmin->getAdminGroupId()) {
        $selected = " selected";
      }
      ?><option value="<?php echo $adminGroup->getId()?>"<?php echo $selected?>><?php echo $adminGroup->getName()?></option><?php
    }
    ?>
    </select>
  </td>
  <td class="colColor1">
    <form action="index.php?page=manageServerAdmins&adminPage=1" id="deleteUser:<?php echo $serverAdmin->getId()?>" name="deleteUser:<?php echo $serverAdmin->getId()?>" method="POST"
    onclick="deleteVerify('<?php echo $serverAdmin->getId()?>', '<?=addslashes($serverAdmin->getName())?>');">
      <input type="hidden" name="adminId" id="adminId" value="<?php echo $serverAdmin->getId()?>"/>
      <input type="hidden" name="serverId" id="serverId" value="<?php echo $server->getId()?>"/>
      <input type="hidden" name="groupId" id="groupId" value="<?php echo $groupId?>"/>
      <input type="hidden" name="submitDelete" value="1">
      <img src="images/trash-full.png" style="cursor:pointer" onmouseover="Tip('Click to remove <?php echo $serverAdmin->getName()?> from this list', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverAdminsTable'));"/>
    </form>
  </td>
</tr>
<?php
}
?>
</table>

<?php
}
?>