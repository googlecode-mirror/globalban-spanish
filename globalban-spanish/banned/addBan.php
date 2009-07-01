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

include_once(ROOTDIR."/include/database/class.ServerQueries.php");
include_once(ROOTDIR."/include/database/class.ReasonQueries.php");
include_once(ROOTDIR."/include/database/class.LengthQueries.php");

$steamId = $_GET['steamId'];
$bannedName = stripslashes($_GET['bannedName']);
$serverId = $_GET['serverId'];
$reasonId = $_GET['reasonId'];

// Initialize Objects
$serverQueries = new ServerQueries();
$reasonQueries = new ReasonQueries();
$lengthQueries = new LengthQueries();

// Get the list of servers
$serverList = $serverQueries->getServers();

// List of Reasons
$banReasons = $reasonQueries->getReasonList();

// List of Ban Lengths
$banLengths = $lengthQueries->getLengthList();
?>
<script type="text/javascript">
function formVerify() {
  var errorFound = false;

  // Validate Steam ID
  var regex = /^STEAM_[01]:[01]:\d{0,10}$/;
  var steamId = document.getElementById("steamdId").value;
  if(!steamId.match(regex)) {
    document.getElementById("steamIdError").style.display = "";
    errorFound = true;
  } else {
    document.getElementById("steamIdError").style.display = "none";
  }
    
  // We have an error, do not submit the form
  if(errorFound) {
    return false;
  }
  
  return true;
}

function formVerifyIp() {
  var errorFound = false;

  // Validate Ip Address
  var regex = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/ ;
  var ip = document.getElementById("ip").value;
  if(!ip.match(regex)) {
    document.getElementById("ipError").style.display = "";
    errorFound = true;
  } else {
    document.getElementById("ipError").style.display = "none";
  }

  // We have an error, do not submit the form
  if(errorFound) {
    return false;
  }

  return true;
}
</script>
<?php
if($_GET['dupe'] == "1") {
  ?><script type="text/javascript">alert("The user you tried to ban is already banned.");</script><?php
}
?>
<div class="tborder">
  <div id="tableHead">
    <div><b>Add New Ban</b></div>
  </div>
  <form action="index.php?page=processWebBan" onsubmit="return formVerify();" method="POST">
  <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
  <tr>
    <td class="rowColor1" width="1%" nowrap>Steam ID:</td>
    <td class="rowColor1"><input name="steamId" id="steamdId" type="text" value="<?=$steamId?>" size="40"/> (must be in <b>STEAM_X:X:XXXXXX</b> format)
    &nbsp;&nbsp;<font id="steamIdError" color='red' style="display:none;">Steam ID not in vaild format</font></td>
  </tr>
  <tr>
    <td class="rowColor2" width="1%" nowrap>Person Banned:</td>
    <td class="rowColor2"><input name="bannedName" id="bannedName" type="text" value="<?=$bannedName?>" size="40"/> (Not required)</td>
  </tr>
  <tr>
    <td class="rowColor1" width="1%" nowrap>Length of Ban:</td>
    <td class="rowColor1">
      <select name="length">
        <?php
        foreach($banLengths as $banLength) {
          ?><option value="<?=$banLength->getId()?>"><?=$banLength->getReadable()?></option><?php
        }
        ?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="rowColor2" width="1%" nowrap>Server:</td>
    <td class="rowColor2">
      <select name="server">
      <?php
      if(count($serverList > 0)) {
        foreach($serverList as $server) {
          if($server->getId() == $serverId) {
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
      if(count($banReasons > 0)) {
        foreach($banReasons as $reason) {
          if($reason->getId() == $reasonId) {
            ?><option value="<?=$reason->getId()?>" selected><?=$reason->getReason()?></option><?php
          } else {
            ?><option value="<?=$reason->getId()?>"><?=$reason->getReason()?></option><?php
          }
        }
      } else {
      ?><option value="0">Breaking Server Rules</option><?php
      }
      ?>
      </select>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="rowColor2"><input type="submit" value="Add Ban"></td>
  </tr>
  </table>
  </form>
</div>

<?php
// Only admins, ban managers, and full power admins can IP ban
if($admin || $banManager || $fullPower) {
?>
  <br/>
  <div class="tborder">
    <div id="tableHead">
      <div><b>Add New IP Ban</b</div>
    </div>
    <form action="index.php?page=processWebBan" onsubmit="return formVerifyIp();" method="POST">
    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" nowrap>IP Address:</td>
      <td class="rowColor1"><input name="ip" id="ip" type="text" value="" size="40"/>
      &nbsp;&nbsp;<font id="ipError" color='red' style="display:none;">Invalid IP Address</font></td>
    </tr>
    <tr>
      <td colspan="2" class="rowColor2"><input type="submit" name="ipBan" value="Add IP Ban"></td>
    </tr>
    </table>
    </form>
  </div>

<?php
  }
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
