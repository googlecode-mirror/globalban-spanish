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

$groupId = $_POST['groupId'];
$plugin = $_POST['plugin'];

$adminGroupQueries = new AdminGroupQueries();

$adminGroupQueries->addPluginToGroup($groupId, $plugin);

$pluginList = $adminGroupQueries->getPluginList($groupId);

// The HTML must match what is in the manageAdminGroups.php file
?>
<span id="pluginTables-<?php echo $groupId?>">
<?php
// Now create each plugin table
foreach($pluginList as $plugin) {
  $flags = $adminGroupQueries->getGroupPluginPowers($groupId, $plugin->getId());
?>
  <table id="pluginSection-<?php echo $groupId?>-<?php echo $plugin->getId()?>" class="bordercolor" width="99%" cellspacing="1" cellpadding="5" border="0" align="center" style="margin-bottom: 10px;">
    <tr>
      <th class="colColor1" colspan="3">
        <?php echo $plugin->getName()?> &nbsp;
        [<span class="actionLink" onclick="selectAllOfPlugin('<?php echo $plugin->getId()?>', '<?php echo $groupId?>')">Select All</span>] 
        [<span class="actionLink" onclick="selectNoneOfPlugin('<?php echo $plugin->getId()?>', '<?php echo $groupId?>')">Select None</span>]
        [<span class="actionLink" onclick="removePlugin('<?php echo $plugin->getId()?>', '<?php echo $groupId?>', '<?php echo $plugin->getName()?>')">Remove Plugin</span>]
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
        <input type="hidden" id="<?php echo $plugin->getId()?>-flagValue-<?php echo $groupId?>" value="<?php echo $flag->getPluginFlagId()?>"/>
        <input type="checkbox" id="<?php echo $plugin->getId()?>-flag-<?php echo $groupId?>-<?php echo $flag->getPluginFlagId()?>" onclick="updatePluginFlag('<?php echo $groupId?>', '<?php echo $flag->getPluginFlagId()?>', this)"<?php echo $checked?>/> <?php echo $flag->getDescription()?>
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