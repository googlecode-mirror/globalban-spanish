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

$serverId = $_GET['serverId'];
$groupId = $_GET['serverGroupId'];

if(empty($serverId)) {
  $serverId = $_POST['serverId'];
}
if(empty($groupId)) {
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

// Get server groups
$groups = $serverQueries->getServerGroups();

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
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
  $("#selectAll").change(function() {
    if($(this).attr("checked")) {
      $(":checkbox").attr("checked", true);
      $(this).attr("checked", true);
    } else {
      $(":checkbox").attr("checked", false);
      $(this).attr("checked", false);
    }
  });
});

function updateAdminGroup(serverId, groupId, adminGroupId, adminId) {
  $.post("index.php?page=updateAdminGroup&ajax=1", { groupId: groupId, serverId: serverId, adminId: adminId, adminGroupId: adminGroupId} );
}

function setSelectedAdminsToGroup(serverId, groupId) {
  var adminGroupId = $("#setSelectedAdminsToGroup").val();
  
  // For each checked
  $(":checked").each(function() {
    var checkboxId = $(this).attr("id");
    if(checkboxId != "selectAll") {
      var adminId = checkboxId.replace("admin-", "");
      $("#adminGroupId-"+adminId).val(adminGroupId);
      $.post("index.php?page=updateAdminGroup&ajax=1", { groupId: groupId, serverId: serverId, adminId: adminId, adminGroupId: adminGroupId} );
    }
  });
}

// This will delete the selected admins and refresh the table
function deleteSelected(serverId, groupId) {
  
  if(confirm("Do you really want to delete the admins selected above?")) {
    var admins = "";
    // Get all the admins checked
    $(":checked").each(function() {
      var checkboxId = $(this).attr("id");
      if(checkboxId != "selectAll") {
        var adminId = checkboxId.replace("admin-", "");
        admins += adminId + ",";
        
        var adminName = $("#adminName-"+adminId).val();
        // Add the admin back to the "admin to add" list
        $("#addNewAdmin").append("<option value='"+adminId+"'>"+adminName+"</option>");
      }
    });
    
    // Now make the AJAX call to delete them and refresh the table
    $.post("index.php?page=manageServerAdminsAjax&ajax=1", { groupId: groupId, serverId: serverId, admins: admins, action: "deleteSelected" },
      function(data) {
        $("#serverAdminsList").empty();
        $("#serverAdminsList").append(data);
      }
    );  
  } // end confirm check
}

function deleteVerify(id, name) {
  if(confirm("Do you really want to delete "+name+"?")) {
    document.getElementById("deleteUser:"+id).submit();
  }
}
</script>
<div class="tborder">
<form id="setGroupAddAdmin" action="index.php?page=manageServerAdmins&adminPage=1" method="POST">
  <div id="tableHead">
    <div>
      <b>Server: </b>
      <select name="serverId" id="serverId">
        <option value="-1">-- Select a server --</option>
        <?php
        foreach($serverList as $singleServer) {
          $serverSelected = "";
          if($serverId == $singleServer->getId())
            $serverSelected = " selected";
          ?><option value="<?php echo $singleServer->getId()?>"<?php echo $serverSelected?>><?php echo $singleServer->getName()?></option><?php
        }
        ?>
      </select>
      <img src="images/help.png" style="cursor:help"
           onmouseover="Tip('Change this dropdown to switch to a different server. This is not required if a group is selected.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverAdminsTable'))"/>
      <input type="submit" id="switchServer" name="switchServer" value="Switch To Server">
      <br/>
      <br/>
      <b>Server Group: </b>
      <select name="serverGroupId" id="serverGroupId">
        <option value="-1">-- Server does not belong to a group --</option>
        <?php
        foreach($groups as $group) {
          $groupSelected = "";
          if($groupId == $group->getId())
            $groupSelected = " selected";
          ?><option value="<?php echo $group->getId()?>"<?php echo $groupSelected?>><?php echo $group->getName()?></option><?php
        }
        ?>
      </select>
      <img src="images/help.png" style="cursor:help"
           onmouseover="Tip('Servers set to the same group will share admin lists and powers. Editing the list on one server affects all servers in that group. Set it to the first option to define an admin list specific to this server only.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverAdminsTable'))"/>
      <input type="submit" id="switchGroup" name="switchGroup" value="Switch To Group">
      <input type="submit" id="setGroup" name="setGroup" value="Set Group for Server" onclick="return confirm('Are you sure you wish to set the group for this server?')">
      <br/>
      <br/>
      <b>Admin to add: </b><select name="addNewAdmin" id="addNewAdmin">
      <?php
      foreach($unAddedUsers as $unAddedUser) {
        ?><option value="<?php echo $unAddedUser->getId()?>"><?php echo $unAddedUser->getName()?></option><?php
      }
      ?>
      </select>
      <img src="images/help.png" style="cursor:help"
           onmouseover="Tip('This will add a new admin to the admin list. Once added, you must edit their admin powers below. If a server group is set, all servers with the same group will have the new adming added.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverAdminsTable'))"/>
      <input type="submit" id="addAdmin" name="addAdmin" value="Add"/> <input type="submit" id="addAllAdmin" name="addAllAdmin" value="Add All"/>
    </div>
  </div>
</form>
</div>

<br/>

<?php
  $listFor = $server->getName()." (Server)";
  if($groupId > 0) {
    $listFor = $groupName." (Group)";
  }
?>

<div class="tborder">
  <div id="tableHead">
    <div><b>Admin List for <?php echo $listFor?></b></div>
  </div>
  <div id="serverAdminsList">
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
        <input type="hidden" id="adminName-<?php echo $serverAdmin->getId()?>" value="<?php echo $serverAdmin->getName()?>"/>
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
    </div>
    <div id="tableBottom">
      <div>
        <br/>
        <b>Set selected admins to the following group:</b> 
        <select id="setSelectedAdminsToGroup">
        <option value="0">-- No Powers --</option>
        <?php
        foreach($adminGroups as $adminGroup) {
          ?><option value="<?php echo $adminGroup->getId()?>"><?php echo $adminGroup->getName()?></option><?php
        }
        ?>
        </select>
        <input type="button" value="Set Selected To Group" onclick="setSelectedAdminsToGroup('<?php echo $server->getId()?>', '<?php echo $groupId?>')"/>
        <input type="button" value="Delete Selected" onclick="deleteSelected('<?php echo $server->getId()?>', '<?php echo $groupId?>')"/>
      </div>
    </div>
   </div>
<?php
}
?>
