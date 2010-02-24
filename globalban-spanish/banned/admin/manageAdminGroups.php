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

require_once(ROOTDIR."/include/database/class.AdminGroupQueries.php");
require_once(ROOTDIR."/include/objects/class.AdminGroup.php");

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_manageAdminGroups.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_manageAdminGroups.php");

?>
<script type="text/javascript">
function deleteVerify(serverId, serverName) {
  if(confirm("<?php echo $LAN_MANAGEADMINGROUPS_014 ?> "+serverName+"<?php echo $LAN_MANAGEADMINGROUPS_015 ?>")) {
    document.getElementById("deleteServer"+serverId).submit();
  }
}
</script>
<?php
// Only those with full privs can remove or add servers to the list
if($fullPower) {

$adminGroupQueries = new AdminGroupQueries();

$error = false;

// If this is set, then that means a server is being added
if(isset($_POST['submitAdd'])) {

  $newGroupId = $adminGroupQueries->addAdminGroup($_POST['groupName'], $_POST['description']);

  if($newGroupId < 0) {
    $error = true;
  }
}

// If a server is being deleted
if(isset($_POST['deleteAdminGroup'])) {
  $adminGroupQueries->deleteAdminGroup($_POST['adminGroupId']);
}

if(isset($_POST['addPlugin'])) {
  $adminGroupQueries->addPluginToGroup($_POST['addPluginGroupId'], $_POST['plugin']);
}

// Get list of admin group objects
$groups = $adminGroupQueries->getAdminGroups();
?>
<script type="text/javascript">
  $(window).bind("load",function(){
	 $('#adminGroupList').accordion({autoHeight: false });
  });
  
  $(document).ready(function(){
    // When focus is lost, update the group name field
    $("input[id^='groupName-']").blur(function() {
      var id = $(this).attr("id");
      var splitId = id.split("-");
      var groupId = splitId[1];
      $.post("index.php?page=updateAdminGroupName&ajax=1", { groupId: groupId, groupname: $(this).val()});
    });
    
    // When focus is lost, update the description field
    $("input[id^='groupDescription-']").blur(function() {
      var id = $(this).attr("id");
      var splitId = id.split("-");
      var groupId = splitId[1];
      $.post("index.php?page=updateAdminGroupDescription&ajax=1", { groupId: groupId, description: $(this).val()});
    });
  });

    
  // This will select all the flags of a plugin within a group
  function selectAllOfPlugin(plugin, groupId) {
    $("input[@id^='"+plugin+"-flag-"+groupId+"']").attr('checked', true);
    // For each flag value found
    $("input[@id^='"+plugin+"-flagValue-"+groupId+"']").each(function() {
      // Fire an AJAX call to update the DB
      $.post("index.php?page=updatePluginFlag&ajax=1", { groupId: groupId, flagId: $(this).val(), checked: "1" } );    
    });
  }
  
  // This will un-select all the flags of a plugin within a group
  function selectNoneOfPlugin(plugin, groupId) {
    $("input[@id^='"+plugin+"-flag-"+groupId+"']").attr('checked', false);
    // For each flag value found
    $("input[@id^='"+plugin+"-flagValue-"+groupId+"']").each(function() {
      // Fire an AJAX call to update the DB
      $.post("index.php?page=updatePluginFlag&ajax=1", { groupId: groupId, flagId: $(this).val(), checked: "0" } );    
    });
  }
  
  // This will update an individual flag
  function updatePluginFlag(groupId, flagId, element) {
    var checked = element.checked;
    if(checked) 
      checked = 1;
    else 
      checked = 0;
    $.post("index.php?page=updatePluginFlag&ajax=1", { groupId: groupId, flagId: flagId, checked: checked } );  
  }
  
  // This will remove a plugin from a group
  function removePlugin(plugin, groupId, pluginName) {
    if(confirm("<?php echo $LAN_MANAGEADMINGROUPS_022 ?> " + pluginName + " <?php echo $LAN_MANAGEADMINGROUPS_023 ?>")) {
      $.post("index.php?page=removePlugin&ajax=1", { groupId: groupId, plugin: plugin} );
      // Now delete the DOM object
      $("#pluginSection-"+groupId+"-"+plugin).remove();
      // Now need to refresh the plugin to add dropdown
      $.post("index.php?page=missingPluginsList&ajax=1", { groupId: groupId },
      function(data){
        $("#pluginList-"+groupId).replaceWith(data);
      });
    }
  }
  
  // This will add a plugin to a group
  function addAdminPlugin(groupId) {
    var plugin = $("#pluginList-"+groupId).val(); // remove this option
    //$("input[@id^='pluginSection-"+groupId+"']").remove();
    $.post("index.php?page=addPlugin&ajax=1", { groupId: groupId, plugin: plugin } ,
    function(data){
      $("#pluginTables-"+groupId).replaceWith(data);
    });
    // Replace all plugin tables
    // Now need to refresh the plugin to add dropdown
    $.post("index.php?page=missingPluginsList&ajax=1", { groupId: groupId },
    function(data){
      $("#pluginList-"+groupId).replaceWith(data);
    });
  }
</script>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LAN_MANAGEADMINGROUPS_001 ?></b></div>
  </div>
  <div id="adminGroupList" class="accordianStyle ui-accordion-container ui-accordion" style="width:100%;margin-bottom:-16px;">
    <?php foreach($groups as $group) {
      $missingPlugins = $adminGroupQueries->getUnaddedPluginList($group->getId());
      $pluginList = $adminGroupQueries->getPluginList($group->getId());
    ?>
    <a class="ui-accordion-header"><?php echo $group->getName()?></a>
    <div>
        <table class="bordercolor" width="99%" cellspacing="1" cellpadding="5" border="0" align="center" style="margin-top: 10px; margin-bottom: 10px;">
          <tr>
            <td class="colColor1" width="1%" nowrap><?php echo $LAN_MANAGEADMINGROUPS_002 ?> <input type="text" id="groupName-<?php echo $group->getId()?>" name="groupName-<?php echo $group->getId()?>" value="<?php echo $group->getName()?>" size="40" maxlength="255"/></td>
            <td class="colColor2" width="1%" nowrap><?php echo $LAN_MANAGEADMINGROUPS_003 ?> <input type="text" id="groupDescription-<?php echo $group->getId()?>" name="groupDescription-<?php echo $group->getId()?>" value="<?php echo $group->getDescription()?>" size="40" maxlength="255"/></td>
          </tr>
        </table>
      <?php if(count($missingPlugins) > 0) { ?>
        <table class="bordercolor" width="99%" cellspacing="1" cellpadding="5" border="0" align="center" style="margin-bottom: 10px;">
          <tr>
            <td class="colColor1">
              <form action="index.php?page=manageAdminGroups&adminPage=1" method="post">
              <input type="hidden" name="addPluginGroupId" id="addPluginGroupId" value="<?php echo $group->getId()?>"/>
              <?php echo $LAN_MANAGEADMINGROUPS_004?> <select id="pluginList-<?php echo $group->getId()?>" name="plugin"><?php foreach($missingPlugins as $missingPlugin) { echo "<option value='".$missingPlugin->getId()."'>".$missingPlugin->getName()."</option>"; } ?></select>
              <input type="button" name="addPlugin" id="addPlugin" value="<?php echo $LAN_MANAGEADMINGROUPS_005?>" style="margin-left:10px" onclick="addAdminPlugin('<?php echo $group->getId()?>')">
              </form>
            </td>
          </tr>
        </table>
      <?php } ?>
      <span id="pluginTables-<?php echo $group->getId()?>">
      <?php
      // Now create each plugin table
      foreach($pluginList as $plugin) {
        $adminGroupQueries->addMissingAdminFlags($group->getId(), $plugin->getId());
        $flags = $adminGroupQueries->getGroupPluginPowers($group->getId(), $plugin->getId());
      ?>
        <table id="pluginSection-<?php echo $group->getId()?>-<?php echo $plugin->getId()?>" class="bordercolor" width="99%" cellspacing="1" cellpadding="5" border="0" align="center" style="margin-bottom: 10px;">
          <tr>
            <th class="colColor1" colspan="3">
              <?php echo $plugin->getName()?> &nbsp;
              [<span class="actionLink" onclick="selectAllOfPlugin('<?php echo $plugin->getId()?>', '<?php echo $group->getId()?>')"><?php echo $LAN_MANAGEADMINGROUPS_006?></span>] 
              [<span class="actionLink" onclick="selectNoneOfPlugin('<?php echo $plugin->getId()?>', '<?php echo $group->getId()?>')"><?php echo $LAN_MANAGEADMINGROUPS_007?></span>]
              [<span class="actionLink" onclick="removePlugin('<?php echo $plugin->getId()?>', '<?php echo $group->getId()?>', '<?php echo $plugin->getName()?>')"><?php echo $LAN_MANAGEADMINGROUPS_008?></span>]
            </th>
          </tr>
          <tr>
          <?php

          $i=0;
          $flagsPerRow = 3; // The number of flags to show per row
          
          foreach($flags as $flag) {
            $checked = "";
            if($flag->isEnabled()) {
              $checked = " checked";
            }
          ?>
            <td class="colColor2">
              <input type="hidden" id="<?php echo $plugin->getId()?>-flagValue-<?php echo $group->getId()?>" value="<?php echo $flag->getPluginFlagId()?>"/>
              <input type="checkbox" id="<?php echo $plugin->getId()?>-flag-<?php echo $group->getId()?>-<?php echo $flag->getPluginFlagId()?>" onclick="updatePluginFlag('<?php echo $group->getId()?>', '<?php echo $flag->getPluginFlagId()?>', this)"<?php echo $checked?>/> <?php echo $flag->getDescription()?>
            </td>
            <?php
              if(($i+1)%$flagsPerRow==0 && ($i+1) != count($flags)) {
                ?></tr><tr><?php
              }
              $i++;
            }

            // Add missing cells if needed
            if($i%$flagsPerRow != 0) {
              for($j=0; $j<($flagsPerRow-($i%$flagsPerRow)); $j++) {
                ?><td class="colColor2"></td><?php
              }
            }
          ?>
          </tr>
        </table>
      <?php
      } // End Plugin loop
      ?>
      </span>
    </div>
    <?php } ?>
  </div>
  <br/>
  <table id="adminGroup" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px">
    <tr>
      <th class="colColor1" width="1%" nowrap><?php echo $LAN_MANAGEADMINGROUPS_009?></th>
      <th class="colColor2" width="1%" nowrap><?php echo $LAN_MANAGEADMINGROUPS_010?></th>
      <th class="colColor1" width="1%" nowrap><?php echo $LAN_MANAGEADMINGROUPS_011?></th>
      <th class="colColor2" width="1%" nowrap><?php echo $LAN_MANAGEADMINGROUPS_012?></th>
      <th class="colColor1" width="1%" nowrap><?php echo $LAN_MANAGEADMINGROUPS_013?></th>
    </tr>
    <?php foreach($groups as $group) {
      ?>
      <tbody>
      
        <td class="colColor1" width="1%" nowrap>
          <input id="editPowers" type="button" value="Edit"/>
        </td>
        <td class="colColor2" id="save:<?php echo $group->getId()?>" onclick="saveAdminGroup('<?php echo $group->getId()?>');" style="cursor:pointer;"
            onmouseover="Tip('<?php echo $LAN_MANAGEADMINGROUPS_016?> <?php echo $group->getName()?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('adminGroup'))">
        <img src="images/save.png"/>
        </td>
        <td class="colColor1" style="cursor:pointer;" onclick="deleteVerify('<?php echo $group->getId()?>', '<?php echo $group->getName()?>');"
            onmouseover="Tip('<?php echo $LAN_MANAGEADMINGROUPS_017?> <?php echo $group->getName()?> <?php echo $LAN_MANAGEADMINGROUPS_018?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('adminGroup'))">
        <form action="index.php?page=manageAdminGroups&adminPage=1" id="adminGroup:<?php echo $group->getId()?>" name="deleteServer<?php echo $group->getId()?>" method="POST">
          <input type="hidden" name="adminGroupId" id="adminGroupId" value="<?php echo $group->getId()?>"/>
          <input type="hidden" name="deleteAdminGroup" value="1">
          <img src="images/trash-full.png"/>
        </form>
        </td>
      </tr>
      <tr>
        <td class="colColor1" colspan="5">
        <div id="powers:<?php echo $group->getId()?>">
        Test
        </div>
        </td>
      </tr>
      </tbody>
    <?php } ?>

    </table>
   </div>
    <br/><br/>

    <!-- This row is for adding a new group -->
    <div class="tborder">
    <div id="tableHead">
      <div><b><?php echo $LAN_MANAGEADMINGROUPS_019?></b></div>
    </div>
    <form action="index.php?page=manageAdminGroups&adminPage=1" method="POST" onsubmit="return formVerify();">
    <table id="newGroupTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="colColor1" width="1%" nowrap><?php echo $LAN_MANAGEADMINGROUPS_002 ?></td>
      <td class="colColor1" nowrap><input type="text" name="groupName" id="groupName" value="" size="40" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="colColor2" width="1%" nowrap><?php echo $LAN_MANAGEADMINGROUPS_003 ?></td>
      <td class="colColor2" nowrap><input type="text" name="description" id="description" value="" size="40" maxlength="255"/></td>
    </tr>
      <td class="colColor1" colspan="2"><input type="submit" name="submitAdd" id="submitAdd" value="<?php echo $LAN_MANAGEADMINGROUPS_020 ?>"/></td>
    </tr>
    <?php
      if($error) {
      ?><tr><td class="colColor1" colspan="2"><span class="error"><?php echo $LAN_MANAGEADMINGROUPS_021 ?></span></td></tr><?php
      }
    ?>
  </table>
  </form>
</div>
<?php
}
?>
