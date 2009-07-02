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
	if (confirm('Do you really want to ban this IP?')){
    document.getElementById('banIpForm').submit()
	}
}
//-->
</script>
<div class="tborder">
<div id="tableHead">
  <div><b>Actualizar Informacion del Ban</b></div>
</div>
<form action="index.php?page=processWebBanUpdate" method="POST">
<input type="hidden" name="banId" value="<?=$banId?>">
<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
<tr>
  <td class="rowColor1" width="1%" nowrap>Steam ID:</td>
  <td class="rowColor1"><?=$bannedUser->getSteamId()?></td>
</tr>
<tr>
  <td class="rowColor2" width="1%" nowrap>Direccion IP:</td>
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
      ?>No hay IP guardada para esta <?=$bannedUser->getSteamId()?><?php
    }
  ?>
  </td>
</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap>Nick Baneado:</td>
  <td class="rowColor1"><input type="text" name="bannedUser" size="40" maxlength="128" value="<?=str_replace(array("\"","\r\n", "\n", "\r"), "&quot;", $bannedUser->getName())?>"/></td>
</tr>
<tr>


<td class="rowColor1" width="1%" nowrap>Admin:</td>
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
          ?><option value="<?=$admin->getAdmin()?>" selected><?=$bannedUser->getBanner()?></option><?php
        } else if(!$fullPowerLevelEditUser && $userQuery->getUserInfo($bannedUser->getBanner())->getAccessLevel() != 1){
          ?><option value="<?=$admin->getAdmin()?>"><?=$admin->getAdmin()?></option><?php
        } else if ($fullPower){
		  ?><option value="<?=$admin->getAdmin()?>"><?=$admin->getAdmin()?></option><?php
		}
      }
    } else {
    ?><option value="-1">No hay admins disponibles</option><?php
    }
    ?>
    </select>
  </td>

</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap>Duracion:</td>
  <td class="rowColor1">
    <select name="length">
      <?php
      foreach($banLengths as $banLength) {
        if($bannedUser->getLength() == $banLength->getLength() && $bannedUser->getTimeScale() == $banLength->getTimeScale()) {
				?><option value="<?=$banLength->getId()?>" selected><?=$banLength->getReadable()?></option><?php
        } else if(!$fullPowerLevelEditUser && $userQuery->getUserInfo($bannedUser->getBanner())->getAccessLevel() != 1) {
				?><option value="<?=$banLength->getId()?>"><?=$banLength->getReadable()?></option><?php
		} else if ($fullPower){
				?><option value="<?=$banLength->getId()?>"><?=$banLength->getReadable()?></option><?php
		}
      }
      ?>
    </select>
  </td class="rowColor1">
</tr>
<tr>
  <td class="rowColor2" width="1%" nowrap>Servidor:</td>
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
  <td class="rowColor1" width="1%" nowrap>Motivo:</td>
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
  <td class="rowColor2" width="1%" valign="top" nowrap>Comentarios:</td>
  <td class="rowColor2"><textarea id="comments" name="comments" cols="87" rows="5"><?=$bannedUser->getComments()?></textarea></td>
</tr>
</tr>
<tr>
  <td class="rowColor1" width="1%" nowrap>Link al Post:</td>
  <td class="rowColor1"><input type="text" name="bannedPost" size="100" maxlength="128" value="<?=$bannedUser->getWebpage()?>"/></td>
</tr>
<tr>
<tr>
  <td class="rowColor1" width="1%" nowrap>Ultima Modificacion Por:</td>
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
    <div><b>Historial de Banes Anteriores</b></div>
  </div>

  <div>
    <table id="banlistTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">

    <tr>
	  <th class="colColor1" align="center">Nick</th>
	  <th class="colColor1" align="center">Motivo</th>
      <th class="colColor2" width="1%" nowrap align="center">Duracion</th>
      <th class="colColor1" width="1%" nowrap align="center">Admin</th>
      <th class="colColor2" width="1%" nowrap align="center">Desde</th>
      <th class="colColor1" width="1%" nowrap align="center">Hasta</th>
      <th class="colColor2" align="center">Post Foro</th>
	  <th class="colColor1" align="center">Comentarios</th>
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
        $expireDate = "Permanente";
        $expireTime = "";
      }

      if($banHistUser->getExpireDate() == 'Expired') {
		$expireDate = "<i>Cumplido</i>";
		$expireTime = "";
	  }

      $length = $banLength->getReadable();
      
    
    ?>
    <tr>
	  <td class="colColor1" nowrap align="center"><?=$banHistUser->getName()?></td>
	  <td class="colColor1" nowrap align="center"><?=$banHistUser->getReason()?></td>
      <td class="colColor2" nowrap align="center"><?=$length?></td>
      <td class="colColor1" nowrap align="center"><?=$banHistUser->getBanner()?></td>
      <td class="colColor2" nowrap align="center"><?=$addDate." ".$addTime?></td>
      <td class="colColor1" nowrap align="center"><?=$expireDate." ".$expireTime?></td>
      <?php
      if($banHistUser->getWebpage() != "") {
        echo "<td class='rowColor2' align='center'><a href='".$banHistUser->getWebpage()."'><img src='images/database_add.png' align='absmiddle'/></a></td>";
      } else {
        echo "<td class='rowColor2' align='center'>Sin Post</td>";
      }
      ?>
	  <td class="colColor1" nowrap><?=$comments?></td>
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
    <div><b>Acceso Denegado</b></div>
  </div>
</div>
<?php
}
?>
