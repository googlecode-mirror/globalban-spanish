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
include_once(ROOTDIR."/include/objects/class.Length.php");

// Initialize Objects
$serverQueries = new ServerQueries();
$reasonQueries = new ReasonQueries();
$banQueries = new BanQueries();
$lengthQueries = new LengthQueries();

// Ban ID
$banId = $_GET['banId'];

// Get the list of servers
$serverList = $serverQueries->getServers();

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
	if (confirm('Do you really want to ban this IP?')){
    document.getElementById('banIpForm').submit()
	}
}
//-->
</script>
<div class="tborder">
<div id="tableHead">
  <div><b>Update Ban Information</b</div>
</div>
<form action="index.php?page=processWebBanUpdate" method="POST">
<input type="hidden" name="banId" value="<?=$banId?>">
<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
<tr>
  <td class="rowColor1" width="1%" nowrap>Steam ID:</td>
  <td class="rowColor1"><?=$bannedUser->getSteamId()?></td>
</tr>
<tr>
  <td class="rowColor2" width="1%" nowrap>IP Address:</td>
  <td class="rowColor2"><?=$bannedUser->getIp()?>
  <?php
    if($bannedUser->getIp() != "") {
      if($banQueries->isIpBanned($bannedUser->getIp())) {
        ?><span class="error">&nbsp; &lt;&lt; IP Banned &gt;&gt; </span><?php
      } else {
        // Only admins, ban mangers, and full power admins can IP ban
        // Members are not allowed to IP ban
        if($admin || $banManager || $fullPower) {
          ?><input type="button" value="Ban IP" onclick="confirmIpBan()"><?php
        }
      }
    } else {
      ?>No IP recorded for this <?=$bannedUser->getSteamId()?><?php
    }
  ?>
  </td>
</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap>Person Banned:</td>
  <td class="rowColor1"><input type="text" name="bannedUser" size="40" maxlength="128" value="<?=$bannedUser->getName()?>"/></td>
</tr>
<tr>
  <td class="rowColor2" width="1%" nowrap>Banned By:</td>
  <td class="rowColor2"><?=$bannedUser->getBanner()?></td>
</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap>Length of Ban:</td>
  <td class="rowColor1">
    <select name="length">
      <?php
      foreach($banLengths as $banLength) {
        $match = false;
        if($bannedUser->getLength() == $banLength->getLength() && $bannedUser->getTimeScale() == $banLength->getTimeScale()) {
          $match = true;
        }
      
        ?><option value="<?=$banLength->getId()?>"<?php if($match) { echo " selected"; } ?>><?=$banLength->getReadable()?></option><?php
      }
      ?>
    </select>
  </td class="rowColor1">
</tr>
<tr>
  <td class="rowColor2" width="1%" nowrap>Server:</td>
  <td class="rowColor2">
    <select name="serverId">
    <?php
    if(count($serverList > 0)) {
      for($i=0; $i<count($serverList);$i++) {
        $server = $serverList[$i];
        if($server->getId() == $bannedUser->getServerId()) {
          ?><option value="<?=$server->getId()?>" selected><?=$server->getName()?></option><?php
        } else {
          ?><option value="<?=$server->getId()?>"><?=$server->getName()?></option><?php
        }
      }
    } else {
    ?><option value="-1">No Servers</option><?php
    }
    ?>
    </select>
  </td>
</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap>Reason:</td>
  <td class="rowColor1">
    <select name="reason">
    <?php
    // Make sure we have a list of ban reasons to display
    if(count($banReasons > 0)) {
      // Ignore first reason as it is a generic reason for importing bans from a ban list
      for($i=0; $i<count($banReasons);$i++) {
        $reason = $banReasons[$i]; 
        if($reason->getId() == $bannedUser->getReasonId()) {
          ?><option value="<?=$reason->getId()?>" selected><?=$reason->getReason()?></option><?php
        } else {
          ?><option value="<?=$reason->getId()?>"><?=$reason->getReason()?></option><?php
        }
      }
    } else {
    ?><option value="-1">Breaking Server Rules</option><?php
    }
    ?>
    </select>
  </td>
</tr>
<tr>
  <td class="rowColor2" width="1%" valign="top" nowrap>Comments:</td>
  <td class="rowColor2"><textarea id="comments" name="comments" cols="30" rows="5"><?=$bannedUser->getComments()?></textarea></td>
</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap>Last Modified By:</td>
  <?php
  if($bannedUser->getModifiedBy() == "") { 
  ?>
    <td class="rowColor1"><?=$bannedUser->getBanner()?></td>
  <?php
  } else {
  ?>
    <td class="rowColor1"><?=$bannedUser->getModifiedBy()?></td>
  <?php
  }
  ?>
</tr>
<tr>
  <td colspan="2" class="rowColor2"><input type="submit" name="updateBan" value="Update Ban"></td>
</tr>
</table>
</form>
<form name="bandIpForm" id="banIpForm" action="index.php?page=processWebBanUpdate" method="POST">
  <input type="hidden" name="banIp" id="banIp" value="1"/>
  <input type="hidden" name="banId" value="<?=$banId?>">
  <input type="hidden" name="ip" id="ip" value="<?=$bannedUser->getIp()?>"/>
</form>
</div>

<br/>
<br/>

<div class="tborder">
  <div id="tableHead">
    <div><b>Ban History</b></div>
  </div>

  <div>
    <table id="banlistTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">

    <tr>
      <th class="colColor1" width="1%" nowrap>Length</th>
      <th class="colColor2" width="1%" nowrap>Banner</th>
      <th class="colColor1" width="1%" nowrap>Add Date</th>
      <th class="colColor2" width="1%" nowrap>Expire Date</th>
      <th class="colColor1">Server</th>
      <th class="colColor2">Ban Reason</th>
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

      if($bannedUser->getLength() == 0) {
        $expireDate = "Never";
        $expireTime = "Never";
      }

      $length = $banLength->getReadable();
      
    
    ?>
    <tr>
      <td class="colColor1" nowrap><?=$length?></td>
      <td class="colColor2" nowrap><?=$bannedUser->getBanner()?></td>
      <td class="colColor1" nowrap><?=$addDate?></td>
      <td class="colColor2" nowrap><?=$expireDate?></td>
      <?php
      if($banHistUser->getServerId() != -1) {
        echo "<td class='rowColor1'>".$banHistUser->getServer()."</td>";
      } else {
        echo "<td class='rowColor2'><a href='".$banHistUser->getServer()."'>Import Server</a></td>";
      }
      ?>
      <td class="colColor2"><?=$banHistUser->getReason()?></td>
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
    <div><b>Access Denied/b</div>
  </div>
</div>
<?php
}
?>
