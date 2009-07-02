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
require_once(ROOTDIR."/include/objects/class.ServerGroup.php");

?>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
function deleteVerify(serverId, serverName) {
  if(confirm("Do you really want to delete "+serverName+"?")) {
    document.getElementById("deleteServer"+serverId).submit();
  }
}
</script>
<?php
// Only those with full privs can remove or add servers to the list
if($fullPower) {

$serverQueries = new ServerQueries();

$error = false;

// If this is set, then that means a server is being added
if(isset($_POST['submitAdd'])) {

  $newGroupId = $serverQueries->addServerGroup($_POST['groupName'], $_POST['description']);

  if($newGroupId < 0) {
    $error = true;
  }
}

// If a server is being deleted
if(isset($_POST['deleteServer'])) {
  $serverQueries->deleteServer($_POST['serverId']);
}
// Get list of server objects
$groups = $serverQueries->getServerGroups();
?>
<br/>
<div class="tborder">
  <div id="tableHead">
    <div><b>Server Group List</b></div>
  </div>
  <table id="serverGroup" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <th class="colColor1" width="1%" nowrap>Group Name</th>
      <th class="colColor2" width="1%" nowrap>Description</th>
      <th class="colColor1" width="1%" nowrap>Servers</th>
      <th class="colColor2" width="1%" nowrap>Admins</th>
      <th class="colColor1" width="1%" nowrap>Save</th>
      <th class="colColor2" width="1%" nowrap>Delete</th>
    </tr>
    <?php foreach($groups as $group) {
      
      $serversInGroup = "<div class='tborder'>";
      $serversInGroup .= "<div id='tableHead'>";
      $serversInGroup .= "<div style='color:#FFFFFF'><b>".$group->getName()." Servers</b></div>";
      $serversInGroup .= "</div>";
      $serversInGroup .= "<table class='bordercolor' width='100%'' cellspacing='1' cellpadding='5' border='0' style='margin-top: 1px;'>";

      $servers = $group->getServers();
      if(count($servers) == 0) {
        $serversInGroup .= "<tr class='rowColor1'><td>No Servers</td></tr>";
      } else {
        foreach($servers as $server) {
          $serversInGroup .= "<tr class='rowColor1'><td>".$server->getName()."</td></tr>";
        }
      }
      
      $serversInGroup .= "</table>";
      $serversInGroup .= "</div>";
      
      $serversInGroup = addslashes($serversInGroup);
    
      ?>
      <tr>
        <td class="colColor1" width="1%" nowrap><input type="text" id="groupName:<?=$group->getId()?>" name="groupName:<?=$group->getId()?>" value="<?=$group->getName()?>" size="40" maxlength="255"/></td>
        <td class="colColor2" width="1%" nowrap><input type="text" id="groupDescription:<?=$group->getId()?>" name="groupDescription:<?=$group->getId()?>" value="<?=$group->getDescription()?>" size="40" maxlength="255"/></td>
        <td class="colColor1" width="1%" nowrap
            onmouseover="Tip('<?=$serversInGroup?>', STICKY, 1, SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverGroup'))">
            <img src="images/network.png"/>
        </td>
        <td class="colColor2" width="1%" nowrap style="cursor:pointer;" onclick="location.href='index.php?page=manageServerAdmins&adminPage=1&serverGroupId=<?=$group->getId()?>'"
            onmouseover="Tip('Click to manage the admins for <?=$group->getName()?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverGroup'))">
            <img src="images/group.png"/>
        </td>
        <td class="colColor1" id="save:<?=$group->getId()?>" onclick="saveServer('<?=$group->getId()?>');" style="cursor:pointer;"
            onmouseover="Tip('Click to save settings for <?=$group->getName()?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverGroup'))">
        <img src="images/tick.png"/>
        </td>
        <td class="colColor2" style="cursor:pointer;" onclick="deleteVerify('<?=$group->getId()?>', '<?=$group->getName()?>');"
            onmouseover="Tip('Click to delete <?=$group->getName()?> from the server list', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverGroup'))">
        <form action="index.php?page=manageServerGroups&adminPage=1" id="deleteServer<?=$group->getId()?>" name="deleteServer<?=$group->getId()?>" method="POST">
          <input type="hidden" name="serverGroupId" id="serverGroupId" value="<?=$group->getId()?>"/>
          <input type="hidden" name="deleteServer" value="1">
          <img src="images/trash-full.png"/>
        </form>
        </td>
      </tr>
    <?php } ?>

    </table>
   </div>
    <br/><br/>

    <!-- This row is for adding a new group -->
    <div class="tborder">
    <div id="tableHead">
      <div><b>Add New Group</b></div>
    </div>
    <form action="index.php?page=manageServerGroups&adminPage=1" method="POST" onsubmit="return formVerify();">
    <table id="newGroupTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="colColor1" width="1%" nowrap>Group Name:</td>
      <td class="colColor1" nowrap><input type="text" name="groupName" id="groupName" value="" size="40" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="colColor2" width="1%" nowrap>Description:</td>
      <td class="colColor2" nowrap><input type="text" name="description" id="description" value="" size="40" maxlength="255"/></td>
    </tr>
      <td class="colColor1" colspan="2"><input type="submit" name="submitAdd" id="submitAdd" value="Add Group"/></td>
    </tr>
    <?php
      if($error) {
      ?><tr><td class="colColor1" colspan="2"><span class="error">Group Name Already Exists</span></td></tr><?php
      }
    ?>
  </table>
  </form>
</div>
<?php
}
?>
