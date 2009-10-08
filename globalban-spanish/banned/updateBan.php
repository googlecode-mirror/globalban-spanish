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

if($member || $admin || $banManager || $fullPower) {

require_once(ROOTDIR."/include/database/class.ServerQueries.php");
require_once(ROOTDIR."/include/database/class.ReasonQueries.php");
require_once(ROOTDIR."/include/database/class.BanQueries.php");
include_once(ROOTDIR."/include/database/class.LengthQueries.php");
require_once(ROOTDIR."/include/database/class.UserQueries.php"); // User specific queries
require_once(ROOTDIR."/include/objects/class.User.php"); // User class to store user info
include_once(ROOTDIR."/include/objects/class.Length.php");

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_updateBan.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_updateBan.php");


// Initialize Objects
$serverQueries = new ServerQueries();
$reasonQueries = new ReasonQueries();
$banQueries = new BanQueries();
$lengthQueries = new LengthQueries();
$userQuery = new UserQueries;
$userEdit = new User;

// Ban ID
$banId = $_GET['banId'];

// Get the list of servers
$serverList = $serverQueries->getServers();

// List of Admins
$banAmins = $reasonQueries->getAdminsList();

// List of Reasons
$banReasons = $reasonQueries->getReasonList();

// Banned user information
$bannedUser = $banQueries->getBannedUser($banId);

// List of Ban Lengths
$banLengths = $lengthQueries->getLengthList();

// Ban history of the user
$banHistory = $banQueries->getBanHistory($banId);

?>
<script type="text/javascript">
<!--
function confirmIpBan() {
	if (confirm('<?php echo $LANUPDATEBAN_032 ?>')){
    document.getElementById('banIpForm').submit()
	}
}
//-->
</script>
<div class="tborder">
<div id="tableHead">
  <div><b><?php echo $LANUPDATEBAN_001; ?></b></div>
</div>
<form action="index.php?page=processWebBanUpdate" method="POST">
<input type="hidden" name="banId" value="<?php echo $banId ?>">
<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
<tr>
  <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_002; ?></td>
  <td class="rowColor1"><?php echo $bannedUser->getSteamId() ?></td>
</tr>
<tr>
  <td class="rowColor2" width="1%" nowrap><?php echo $LANUPDATEBAN_003; ?></td>
  <td class="rowColor2"><?php echo $bannedUser->getIp() ?>
  <?php
    if($bannedUser->getIp() != "") {
      if($banQueries->isIpBanned($bannedUser->getIp())) {
        ?><span class="error">&nbsp; &lt;&lt; <?php echo $LANUPDATEBAN_004; ?> &gt;&gt; </span><?php
      } else {
        // Only admins, ban mangers, and full power admins can IP ban
        // Members are not allowed to IP ban
        if($admin || $banManager || $fullPower) {
          ?><input type="button" value="<?php echo $LANUPDATEBAN_031; ?>" onclick="confirmIpBan()"><?php
        }
      }
    } else {
      ?><?php echo $LANUPDATEBAN_005; ?>  <?php echo $bannedUser->getSteamId() ?><?php
    }
  ?>
  </td>
</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_006; ?></td>
  <td class="rowColor1"><input type="text" name="bannedUser" size="40" maxlength="128" value="<?php echo $bannedUser->getName() ?>"/></td>
</tr>
<tr>


<td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_007; ?></td>
  <td class="rowColor1">
    <select name="admin">
    <?php
      $fullPowerLevelEditUser = false;
      $userEdit = $userQuery->getUserInfo($bannedUser->getModifiedBy());
      if($userEdit->getAccessLevel() == 1){
	    $fullPowerLevelEditUser = true;
	  }
	
	// Make sure we have a list of admis to display
    if(count($banAmins > 0)) {

      for($i=0; $i<count($banAmins);$i++) {
        $admin = $banAmins[$i]; 
        if($admin->getAdmin() == $bannedUser->getBanner()) {
          ?><option value="<?php echo $admin->getAdmin()?>" selected><?php echo $bannedUser->getBanner()?></option><?php
        } else if(!$fullPowerLevelEditUser && $userQuery->getUserInfo($bannedUser->getBanner())->getAccessLevel() != 1){
          ?><option value="<?php echo $admin->getAdmin()?>"><?php echo $admin->getAdmin()?></option><?php
        } else if ($fullPower){
		  ?><option value="<?php echo $admin->getAdmin()?>"><?php echo $admin->getAdmin()?></option><?php
		}
      }
    } else {
    ?><option value="-1"><?php echo $LANUPDATEBAN_008; ?></option><?php
    }
    ?>
    </select>
  </td>

</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_009; ?></td>
  <td class="rowColor1">
    <select name="length">
      <?php
      foreach($banLengths as $banLength) {

        if($bannedUser->getLength() == $banLength->getLength() && $bannedUser->getTimeScale() == $banLength->getTimeScale()) {
				?><option value="<?php echo $banLength->getId()?>" selected><?php echo $banLength->getReadable()?></option><?php
        } else if(!$fullPowerLevelEditUser && $userQuery->getUserInfo($bannedUser->getBanner())->getAccessLevel() != 1) {
				?><option value="<?php echo $banLength->getId()?>"><?php echo $banLength->getReadable()?></option><?php
		} else if ($fullPower){
				?><option value="<?php echo $banLength->getId()?>"><?php echo $banLength->getReadable()?></option><?php
		}
      }
      ?>
    </select>
  </td class="rowColor1">
</tr>
<tr>
  <td class="rowColor2" width="1%" nowrap><?php echo $LANUPDATEBAN_010; ?></td>
  <td class="rowColor2">
    <select name="serverId">
    <?php
    if(count($serverList > 0)) {
      for($i=0; $i<count($serverList);$i++) {
        $server = $serverList[$i];
        if($server->getId() == $bannedUser->getServerId()) {
          ?><option value="<?php echo $server->getId() ?>" selected><?php echo $server->getName() ?></option><?php
        } else {
          ?><option value="<?php echo $server->getId() ?>"><?php echo $server->getName() ?></option><?php
        }
      }
    } else {
    ?><option value="-1"><?php echo $LANUPDATEBAN_011; ?></option><?php
    }
    ?>
    </select>
  </td>
</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_012; ?></td>
  <td class="rowColor1">
    <select name="reason">
    <?php
    // Make sure we have a list of ban reasons to display
    if(count($banReasons > 0)) {
      // Ignore first reason as it is a generic reason for importing bans from a ban list
      for($i=0; $i<count($banReasons);$i++) {
        $reason = $banReasons[$i]; 
        if($reason->getId() == $bannedUser->getReasonId()) {
          ?><option value="<?php echo $reason->getId() ?>" selected><?php echo $reason->getReason() ?></option><?php
        } else {
          ?><option value="<?php echo $reason->getId() ?>"><?php echo $reason->getReason() ?></option><?php
        }
      }
    } else {
    ?><option value="-1"><?php echo $LANUPDATEBAN_013; ?></option><?php
    }
    ?>
    </select>
  </td>
</tr>
<tr>
  <td class="rowColor2" width="1%" valign="top" nowrap><?php echo $LANUPDATEBAN_014; ?></td>
  <td class="rowColor2"><textarea id="comments" name="comments" cols="30" rows="5"><?php echo $bannedUser->getComments() ?></textarea></td>
</tr>
</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_015; ?></td>
  <td class="rowColor1"><input type="text" name="bannedPost" size="100" maxlength="128" value="<?php echo $bannedUser->getWebpage()?>"/></td>
</tr>
<tr>
<tr>
  <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_016; ?></td>
  <?php
  if($bannedUser->getModifiedBy() == "") { 
  ?>
    <td class="rowColor1"><?php echo $bannedUser->getBanner() ?></td>
  <?php
  } else {
  ?>
    <td class="rowColor1"><?php echo $bannedUser->getModifiedBy() ?></td>
  <?php
  }
  ?>
</tr>
<tr>
  <td colspan="2" class="rowColor2"><input type="submit" name="updateBan" value="<?php echo $LANUPDATEBAN_017; ?>"></td>
</tr>
</table>
</form>
<form name="bandIpForm" id="banIpForm" action="index.php?page=processWebBanUpdate" method="POST">
  <input type="hidden" name="banIp" id="banIp" value="1"/>
  <input type="hidden" name="banId" value="<?php echo $banId ?>">
  <input type="hidden" name="ip" id="ip" value="<?php echo $bannedUser->getIp() ?>"/>
</form>
</div>

<br/>
<br/>

<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LANUPDATEBAN_018 ?></b></div>
  </div>

  <div>
    <table id="banlistTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">

    <tr>
      <th class="colColor1" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_019; ?></th>
      <th class="colColor2" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_020; ?></th>
      <th class="colColor1" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_021; ?></th>
      <th class="colColor2" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_022; ?></th>
      <th class="colColor1" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_023; ?></th>
      <th class="colColor2" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_024; ?></th>
	  <th class="colColor1" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_025; ?></th>
	  <th class="colColor2" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_026; ?></th>
    </tr>
    <?php
    // Loop through banned users and display them
    foreach($banHistory as $banHistUser) {
      $length = "";
      list($expireDate, $expireTime) = split(' ', $banHistUser->getExpireDate());
      list($addDate, $addTime, $year) = split(' ', $banHistUser->getAddDate());
      $comments = str_replace(array("\r\n", "\n", "\r"), "<br/>", $banHistUser->getComments()); // Convert newlines into html line breaks

      $banLength = new Length();
      $banLength->setLength($banHistUser->getLength());
      $banLength->setTimeScale($banHistUser->getTimeScale());

      if($banHistUser->getLength() == 0) {
        $expireDate = $LANUPDATEBAN_027;
        $expireTime = "";
      }

	  if($banHistUser->getExpireDate() == 'Expired') {
		$expireDate = $LANUPDATEBAN_028;
		$expireTime = "";
	  }
$length = $banLength->getReadable();
      
    
    ?>
    <tr>
	  <td class="colColor1" nowrap align="center"><?=$banHistUser->getName()?></td>
	  <td class="colColor2" nowrap align="center"><?=$banHistUser->getReason()?></td>
      <td class="colColor1" nowrap align="center"><?=$length?></td>
      <td class="colColor2" nowrap align="center"><?=$banHistUser->getBanner()?></td>
      <td class="colColor1" nowrap align="center"><?=$addDate." ".$addTime?></td>
      <td class="colColor2" nowrap align="center"><?=$expireDate." ".$expireTime?></td>
      <?php
      if($banHistUser->getWebpage() != "") {
        echo "<td class='rowColor2' align='center'><a href='".$banHistUser->getWebpage()."'><img src='images/database_add.png' align='absmiddle'/></a></td>";
      } else {
        echo "<td class='rowColor2' align='center'><img src='images/cross.png' align='absmiddle' alt='".$LANUPDATEBAN_029."'/></td>";
      }
      ?>
	  <td class="colColor1" nowrap><?php echo $comments?></td>
    </tr>
    <?php
    }
    ?>
    
    </table>
  </div>
</div>
<?php
} else {
?>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LANUPDATEBAN_030; ?></b</div>
  </div>
</div>
<?php
}
?>
