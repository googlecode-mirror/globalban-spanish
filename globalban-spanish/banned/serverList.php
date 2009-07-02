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

require_once(ROOTDIR."/include/live.class.php");
require_once(ROOTDIR."/include/database/class.ServerQueries.php");
require_once(ROOTDIR."/include/objects/class.Server.php");
require_once(ROOTDIR."/include/class.rcon.php");

$serverQueries = new ServerQueries();

$error = false;

// Get list of server objects
$servers = $serverQueries->getServers();
?>
<div class="tborder">
  <div id="tableHead">
    <div><b>Server List</b></div>
  </div>
  
  <table id="serverListTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
  <tr>
    <?php 
          $i=0;
          $serversPerRow = 3; // The number of servers to show per row
          foreach($servers as $server) {
          // Create an rcon object to connect to a single server on each iteration
          $r = new rcon($server->getIp(),$server->getPort(),$server->getRcon());
          $liveData = new LIVE();
          $serverData = $liveData->getInfo($server->getIp(),$server->getPort());
          ?>
          <td class="colColor1" valign="top">
          <?php
            if($r->isValid()) {
              ?><img src="images/connect.png" onmouseover="Tip('The server is online.', SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverListTable'))"/><?php
            } else {
              ?><img src="images/disconnect.png" onmouseover="Tip('<b>The server is offline.</b><br/><i>The server information may not be configured correctly or <br/>the web server firewall is preventing communication with the server.</i>', SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverListTable'))"/><?php
            }
          ?>
          <b><?=$server->getName()?></b><br/>
          <?=$server->getIp()?>:<?=$server->getPort()?><br/>
          <?php
          if($r->isValid()) {
            ?>
            <?=$serverData['gamedesc']?><br/>
            <?=$serverData['map']?>
            <?=$serverData['numplayers']?>/<?=$serverData['maxplayers']?>
            
            <?php
            $usersOnline = $liveData->getPlayers($server->getIp(),$server->getPort());
            if(count($usersOnline) > 0) {
              $usersOnlineTable = "<div class='tborder'>";
              $usersOnlineTable .= "<div id='tableHead'>";
              $usersOnlineTable .= "<div style='color:#FFFFFF'><b>Player Information</b></div>";
              $usersOnlineTable .= "</div>";
              $usersOnlineTable .= "<table class='bordercolor' width='100%'' cellspacing='1' cellpadding='5' border='0' style='margin-top: 1px;'>";
              $usersOnlineTable .= "<tr class='rowColor1'><th>ID</th><th>Name</th><th>Kills</th><th>Connect Time</th></tr>";
              foreach($usersOnline as $userOnline) {
                if($i%2==0) {
                  $usersOnlineTable .= "<tr class='rowColor1'>";
                } else {
                  $usersOnlineTable .= "<tr class='rowColor2'>";
                }
                $usersOnlineTable .= "<td>";
                $usersOnlineTable .= $userOnline['index'];
                $usersOnlineTable .= "</td>";
                $usersOnlineTable .= "<td>";
                $usersOnlineTable .= $userOnline['name'];
                $usersOnlineTable .= "</td>";
                $usersOnlineTable .= "<td>";
                $usersOnlineTable .= $userOnline['kills'];
                $usersOnlineTable .= "</td>";
                $usersOnlineTable .= "<td>";
                $usersOnlineTable .= $userOnline['time'];
                $usersOnlineTable .= "</td>";
                $usersOnlineTable .= "</tr>";
              }
              $usersOnlineTable .= "</table>";
              $usersOnlineTable .= "</div>";
            } else {
              $usersOnlineTable = "No players online.";
            }
            ?>
            <img src="images/group.png" onmouseover="Tip('<?=addslashes($usersOnlineTable)?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverListTable'))"/><br/>
          <?php } ?>
          <?php
            $appNumber = 0;
            if($server->getType() == "cstrike") {
              $appNumber = 240;
            } else if($server->getType() == "dod") {
              $appNumber = 300;
            }
          ?>
          <a href='steam: "-applaunch <?=$appNumber?> -game <?=$server->getType()?> +connect <?=$server->getIp()?>:<?=$server->getPort()?>"'>Join Server</a>
          </td>
          <?php
            if(($i+1)%$serversPerRow==0 && ($i+1) != count($servers)) {
              ?></tr><tr><?php
            }
            $i++;
          }
          
          // Add missing cells if needed
          if($i%$serversPerRow != 0) {
            for($j=0; $j<($serversPerRow-($i%$serversPerRow)); $j++) {
              ?><td class="colColor1"></td><?php
            }
          }
    ?>
  </tr>
  </table>
</div>
