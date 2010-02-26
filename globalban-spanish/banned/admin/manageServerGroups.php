<?php
/*
    EDIT : This file as been edited by Fantole
	http://www.css-ressource.com
	
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

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_manageServerGroups.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_manageServerGroups.php");

?>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
<!--<script type="text/javascript">
function deleteVerify(serverId, serverName) {
  if(confirm("<?php /*?><?php echo $LAN_MANAGESERVERGROUPS_001 ?><?php */?> .  "+serverName+"<?php /*?><?php echo $LAN_MANAGESERVERGROUPS_002 ?><?php */?>")) {
    document.getElementById("deleteServer"+serverId).submit();
  }
}
</script> -->

<?php
// Only those with full privs can remove or add servers to the list
if($fullPower) {

$serverQueries = new ServerQueries();

$error = false;

// If this is set, then that means a server is being added
if(isset($_POST['submitAdd']))
{
  if (!empty($_POST['groupName']) && !empty($_POST['description']))
  {
  	$newGroupId = $serverQueries->addServerGroup($_POST['groupName'], $_POST['description']);

  	if($newGroupId < 0)
  	{
   	 $error = true;
  	}
  }
  if (empty($_POST['groupName']) && empty($_POST['description']))
  {
  	$error = true;
  }
}

// If a server is being deleted
if(!empty($_GET['deleteServer'])) {
  $serverQueries->deleteServerGroup($_GET['deleteServer']);
}
// Get list of server objects
$groups = $serverQueries->getServerGroups();
?>
<br/>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LAN_MANAGESERVERGROUPS_003; ?></b></div>
  </div>
  <table id="serverGroup" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <th class="colColor1" width="1%" nowrap><div align="center"><?php echo $LAN_MANAGESERVERGROUPS_004; ?></div></th>
      <th class="colColor2" width="1%" nowrap><div align="center"><?php echo $LAN_MANAGESERVERGROUPS_005; ?></div></th>
      <th class="colColor1" width="1%" nowrap><div align="center"><?php echo $LAN_MANAGESERVERGROUPS_006; ?></div></th>
      <th class="colColor2" width="1%" nowrap><div align="center"><?php echo $LAN_MANAGESERVERGROUPS_007; ?></div></th>
      <th class="colColor1" width="1%" nowrap><div align="center"><?php echo $LAN_MANAGESERVERGROUPS_008; ?></div></th>
      <th class="colColor2" width="1%" nowrap><div align="center"><?php echo $LAN_MANAGESERVERGROUPS_009; ?></div></th>
    </tr>
    <?php foreach($groups as $group) {
      
      $serversInGroup = "<div class='tborder'>";
      $serversInGroup .= "<div id='tableHead'>";
	  $serversInGroup .= "<div style='color:#FFFFFF'><b>".$group->getName()." ".$LAN_MANAGESERVERGROUPS_010."</b></div>";
      $serversInGroup .= "</div>";
      $serversInGroup .= "<table class='bordercolor' width='100%'' cellspacing='1' cellpadding='5' border='0' style='margin-top: 1px;'>";

      $servers = $group->getServers();
      if(count($servers) == 0) {
        $serversInGroup .= $LAN_MANAGESERVERGROUPS_011;
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
        <td class="colColor1" width="1%" nowrap><div align="center"><input type="text" id="groupName:<?php echo $group->getId()?>" name="groupName:<?php echo $group->getId()?>" value="<?php echo $group->getName()?>" size="40" maxlength="255"/></div></td>
        <td class="colColor2" width="1%" nowrap><div align="center"><input type="text" id="groupDescription:<?php echo $group->getId()?>" name="groupDescription:<?php echo $group->getId()?>" value="<?php echo $group->getDescription()?>" size="40" maxlength="255"/></div></td>
        <td class="colColor1" width="1%" nowrap
            onmouseover="Tip('<?php echo $serversInGroup?>', STICKY, 1, SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverGroup'))">
            <div align="center"><img src="images/network.png"/></div>
        </td>
        <td class="colColor2" width="1%" nowrap style="cursor:pointer;" onclick="location.href='index.php?page=manageServerAdmins&adminPage=1&serverGroupId=<?php echo $group->getId()?>'"
            onmouseover="Tip('<?php echo $LAN_MANAGESERVERGROUPS_012; ?> <?php echo $group->getName()?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverGroup'))">
            <div align="center"><img src="images/group.png"/></div>
        </td>
		<!-- onclick"saveserver"-->
        <td class="colColor1" id="save:<?php echo $group->getId()?>" onclick="saveServerGroup('<?php echo $group->getId()?>');" style="cursor:pointer;"
            onmouseover="Tip('<?php echo $LAN_MANAGESERVERGROUPS_013; ?> <?php echo $group->getName()?> ', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverGroup'))">
            <div align="center"><img src="images/save.png"/></div>
        </td>
        <td class="colColor2" style="cursor:pointer;" onclick="deleteVerify('<?php echo $group->getId()?>', '<?php echo $group->getName()?>');"
            onmouseover="Tip('<?php echo $LAN_MANAGESERVERGROUPS_014; ?> <?php echo $group->getName()?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverGroup'))">
        <form action="index.php?page=manageServerGroups&adminPage=1" id="deleteServer<?php echo $group->getId()?>" name="deleteServer<?php echo $group->getId()?>" method="POST">
          <input type="hidden" name="serverGroupId" id="serverGroupId" value="<?php echo $group->getId()?>"/>
          <input type="hidden" name="deleteServer" value="1">
          <div align="center"><img src="images/trash-full.png"/></div>
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
      <div><b><?php echo $LAN_MANAGESERVERGROUPS_015; ?></b></div>
    </div>
    <form action="index.php?page=manageServerGroups&adminPage=1" method="POST" onsubmit="return formVerify();">
    <table id="newGroupTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="colColor1" width="1%" nowrap><?php echo $LAN_MANAGESERVERGROUPS_016; ?></td>
      <td class="colColor1" nowrap><input type="text" name="groupName" id="groupName" value="" size="40" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="colColor2" width="1%" nowrap><?php echo $LAN_MANAGESERVERGROUPS_017; ?></td>
      <td class="colColor2" nowrap><input type="text" name="description" id="description" value="" size="40" maxlength="255"/></td>
    </tr>
      <td class="colColor1" colspan="2"><input type="submit" name="submitAdd" id="submitAdd" value="<?php echo $LAN_MANAGESERVERGROUPS_019; ?>"/></td>
    </tr>
    <?php
      if($error) {
      ?><tr><td class="colColor1" colspan="2"><span class="error"><?php echo $LAN_MANAGESERVERGROUPS_018; ?></span></td></tr><?php
      }
    ?>
  </table>
  </form>
</div>
<h5><?php echo $LAN_MANAGESERVERGROUPS_021; ?> <a href="index.php?page=configuration&adminPage=1">"<?php echo $LAN_MANAGESERVERGROUPS_022; ?>"</a><?php echo $LAN_MANAGESERVERGROUPS_023 ?></h5>

<?php
}
?>
