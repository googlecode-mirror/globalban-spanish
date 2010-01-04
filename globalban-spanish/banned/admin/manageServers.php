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

?>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
function formVerify() {
  var errorFound = false;
  var alertMessage = "";

  // Validate IP
  var regex = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
  var serverIp = document.getElementById("serverIp").value;
  if(!serverIp.match(regex)) {
    alertMessage += "Server IP is invalid.\n\r";
    errorFound = true;
  }

  // Validate Port
  var regex = /^\d{1,8}$/;
  var serverPort = document.getElementById("serverPort").value;
  if(!serverPort.match(regex)) {
    alertMessage += "Server Port is invalid.\n\r";
    errorFound = true;
  }

  // We have an error, do not submit the form
  if(errorFound) {
    alert(alertMessage);
    return false;
  }

  return true;
}

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

  $newServerId = $serverQueries->addServer($_POST['serverName'], $_POST['serverIp'], $_POST['serverPort'], $_POST['serverRcon'], $_POST['serverType'], $_POST['serverPlugin']);

  if($newServerId < 0) {
    $error = true;
  }
}

// If a server is being deleted
if(isset($_POST['deleteServer'])) {
  $serverQueries->deleteServer($_POST['serverId']);
}
// Get list of server objects
$servers = $serverQueries->getServers();
?>
<br/>
<br/>
<div class="tborder">
  <div id="tableHead">
    <div><b>Server Management List</b></div>
  </div>
  <table id="serverManagementTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <th class="colColor1" width="1%" nowrap>ID</th>
      <th class="colColor2" width="1%" nowrap>Server Name</th>
      <th class="colColor1" width="1%" nowrap>IP</th>
      <th class="colColor2" width="1%" nowrap>Port</th>
      <th class="colColor1" width="1%" nowrap>Type</th>
      <th class="colColor1" width="1%" nowrap>Current RCON Password</th>
      <th class="colColor2" width="1%" nowrap>New RCON Password</th>
      <th class="colColor1" width="1%" nowrap>Admins</th>
      <th class="colColor2" width="1%" nowrap>Save</th>
      <th class="colColor1" width="1%" nowrap>Delete</th>
    </tr>
    <?php foreach($servers as $server) {
          ?>
          <tr>
            <td class="colColor1" width="1%" nowrap><?php echo $server->getId()?></td>
            <td class="colColor2" width="1%" nowrap><input type="text" id="serverName:<?php echo $server->getId()?>" name="serverName:<?php echo $server->getId()?>" value="<?php echo $server->getName()?>" size="40" maxlength="128"/></td>
            <td class="colColor1" width="1%" nowrap><input type="text" id="serverIp:<?php echo $server->getId()?>" name="serverIp:<?php echo $server->getId()?>" value="<?php echo $server->getIp()?>" size="18" maxlength="16"/></td>
            <td class="colColor2" width="1%" nowrap><input type="text" id="serverPort:<?php echo $server->getId()?>" name="serverPort:<?php echo $server->getId()?>" value="<?php echo $server->getPort()?>" size="8" maxlength="8"/></td>
            <td class="colColor1" width="1%" nowrap>
              <select id="serverType:<?php echo $server->getId()?>" name="serverType:<?php echo $server->getId()?>">
                <?php if(strtolower($server->getType()) == "cstrike") { ?>
                  <option value="cstrike" selected>CS:S</option>
                <?php }else { ?>
                  <option value="cstrike">CS:S</option>
                <?php } ?>
                <?php if(strtolower($server->getType()) == "tf2") { ?>
                  <option value="cstrike" selected>TF2</option>
                <?php }else { ?>
                  <option value="tf2">TF2</option>
                <?php } ?>
                <?php if(strtolower($server->getType()) == "dod") { ?>
                  <option value="dod" selected>DOD:S</option>
                <?php }else { ?>
                  <option value="dod">DOD:S</option>
                <?php } ?>
                <?php if(strtolower($server->getType()) == "hl2mp") { ?>
                  <option value="hl2mp" selected>HL2:DM</option>
                <?php }else { ?>
                  <option value="hl2mp">HL2:DM</option>
                <?php } ?>
                <?php if(strtolower($server->getType()) == "zps") { ?>
                  <option value="ZPS" selected>ZPS</option>
                <?php }else { ?>
                  <option value="ZPS">ZPS</option>
                <?php } ?>
              </select>
            </td>
            <td class="colColor2" width="1%" nowrap><input type="password" id="currentServerRcon:<?php echo $server->getId()?>"name="currentServerRcon:<?php echo $server->getId()?>" value="" maxlength="40"/></td>
            <td class="colColor1" width="1%" nowrap><input type="password" id="serverRcon:<?php echo $server->getId()?>"name="serverRcon:<?php echo $server->getId()?>" value="" maxlength="40"/></td>
            <td class="colColor2" width="1%" nowrap style="cursor:pointer;" onclick="location.href='index.php?page=manageServerAdmins&adminPage=1&serverId=<?php echo $server->getId()?>&groupId=<?php echo $server->getGroupId()?>'"
                onmouseover="Tip('Click to manage the admins for <?php echo $server->getName()?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverManagementTable'))">
                <img src="images/group.png"/>
            </td>
            <td id="save:<?php echo $server->getId()?>" class="colColor2" onclick="saveServer('<?php echo $server->getId()?>');" style="cursor:pointer;"
                onmouseover="Tip('Click to save settings for <?php echo $server->getName()?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverManagementTable'))">
            <img src="images/tick.png"/>
            </td>
            <td class="colColor1" style="cursor:pointer;" onclick="deleteVerify('<?php echo $server->getId()?>', '<?php echo $server->getName()?>');"
                onmouseover="Tip('Click to delete <?php echo $server->getName()?> from the server list', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverManagementTable'))">
            <form action="index.php?page=manageServers&adminPage=1" id="deleteServer<?php echo $server->getId()?>" name="deleteServer<?php echo $server->getId()?>" method="POST">
              <input type="hidden" name="serverId" id="serverId" value="<?php echo $server->getId()?>"/>
              <input type="hidden" name="deleteServer" value="1">
              <img src="images/trash-full.png"/>
            </form>
            </td>
          </tr>
    <?php } ?>

    </table>
   </div>
    <br/><br/>

    <!-- This row is for adding a new server -->
    <div class="tborder">
    <div id="tableHead">
      <div><b>Add New Server</b></div>
    </div>
    <form action="index.php?page=manageServers&adminPage=1" method="POST" onsubmit="return formVerify();">
    <table id="newServerTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="colColor1" width="1%" nowrap>Server Name:</td>
      <td class="colColor1" nowrap><input type="text" name="serverName" id="serverName" value="" size="40" maxlength="128"/></td>
    </tr>
    <tr>
      <td class="colColor2" width="1%" nowrap>Server IP:</td>
      <td class="colColor2" nowrap><input type="text" name="serverIp" id="serverIp" value="" size="20" maxlength="16"/></td>
    </tr>
    <tr>
      <td class="colColor1" width="1%" nowrap>Server Port:</td>
      <td class="colColor1" nowrap><input type="text" name="serverPort" id="serverPort" value="27015" size="10"/></td>
    </tr>
    <tr>
      <td class="colColor2" width="1%" nowrap>Server Type:</td>
      <td class="colColor2" nowrap>
        <select id="serverType" name="serverType">
            <option value="cstrike">CS:S</option>
            <option value="dod">DOD:S</option>
            <option value="tf2">TF2</option>
            <option value="hl2mp">HL2:DM</option>
            <option value="ZPS">ZPS</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="colColor1" width="1%" nowrap>Server RCON:</td>
      <td class="colColor1" nowrap><input type="text" name="serverRcon" id="serverRcon" value="" maxlength="40"/></td>
    </tr>
      <td class="colColor2" colspan="2"><input type="submit" name="submitAdd" id="submitAdd" value="Add Server"/></td>

    </tr>
    <?php
      if($error) {
      ?><tr><td class="colColor1" colspan="2"><span class="error">Server Already Exists</span></td></tr><?php
      }
    ?>
  </table>
  </form>
</div>
<h5>Note: You must perform a "Save Configuration" from the "Configuration" page after adding any new servers.</h5>
<?php
}
?>
