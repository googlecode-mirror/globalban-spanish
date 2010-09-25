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

// require_once(ROOTDIR."/include/live.class.php");
require_once(ROOTDIR."/include/database/class.ServerQueries.php");
require_once(ROOTDIR."/include/objects/class.Server.php");
require_once(ROOTDIR."/include/class.rcon.php");
require_once(ROOTDIR."/include/steam_condenser/steam-condenser.php");

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_serverList.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_serverList.php");

$serverQueries = new ServerQueries();

$error = false;

// Get list of server objects
$servers = $serverQueries->getServers();
?>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LAN_SERVERLIST_001; ?></b></div>
  </div>
  
  <table id="serverListTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
  <tr>
    <?php 
		$i=0;
		$serversPerRow = 3; // The number of servers to show per row
		foreach($servers as $server) {
			$serverIp = new InetAddress($server->getIp());
			$serverSC = new SourceServer($serverIp, $server->getPort());
			$serverSC->initialize();

			// Create an rcon object to connect to a single server on each iteration
			$r = new rcon($server->getIp(),$server->getPort(),$server->getRcon());
				// $liveData = new LIVE();
			$serverData = $serverSC->getServerInfo();
			?>
			<td class="colColor1" valign="top">
			<?php
			if($r->isValid()) {
				?><img src="images/connect.png" onmouseover="Tip('<?php echo $LAN_SERVERLIST_002; ?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverListTable'))"/><?php
			} else {
				?><img src="images/disconnect.png" onmouseover="Tip('<?php echo $LAN_SERVERLIST_003; ?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverListTable'))"/><?php
			}
			?>
			<b><?php echo $server->getName() ?></b><br/>
			<?php echo $server->getIp() ?>:<?php echo $server->getPort() ?><br/>
			<?php
			if($r->isValid()) {
				?>
				<?php echo $serverData['gameDesc'] ?><br/>
				<?php echo $serverData['mapName'] ?>
				<?php echo $serverData['playerNumber'] ?>/<?php echo $serverData['maxPlayers'] ?>

				<?php
				// $server->getRcon()  To new Steam_Comunity version when they fix rcon conecction
				$SteamPlayers = $serverSC->getPlayers();
				if(count($SteamPlayers) > 0) {
					$usersOnlineTable = "<div class='tborder'>";
					$usersOnlineTable .= "<div id='tableHead'>";
					$usersOnlineTable .= $LAN_SERVERLIST_004;
					$usersOnlineTable .= "</div>";
					$usersOnlineTable .= "<table class='bordercolor' width='100%'' cellspacing='1' cellpadding='5' border='0' style='margin-top: 1px;'>";
					$usersOnlineTable .= $LAN_SERVERLIST_005;
					foreach($SteamPlayers as $SteamPlayer) {
						if($i%2==0) {
							$usersOnlineTable .= "<tr class='rowColor1'>";
						} else {
							$usersOnlineTable .= "<tr class='rowColor2'>";
						}
						$usersOnlineTable .= "<td>";
						$usersOnlineTable .= $SteamPlayer->getId();
						$usersOnlineTable .= "</td>";
						$usersOnlineTable .= "<td>";
						$usersOnlineTable .= $SteamPlayer->getName();
						$usersOnlineTable .= "</td>";
						$usersOnlineTable .= "<td>";
						$usersOnlineTable .= $SteamPlayer->getScore();
						$usersOnlineTable .= "</td>";
						$usersOnlineTable .= "<td>";
						$usersOnlineTable .= SecondsToString($SteamPlayer->getConnectTime());
						$usersOnlineTable .= "</td>";
						$usersOnlineTable .= "</tr>";
					}
					$usersOnlineTable .= "</table>";
					$usersOnlineTable .= "</div>";
				} else {
					$usersOnlineTable = $LAN_SERVERLIST_006;
				}
				?>
				<img src="images/group.png" onmouseover="Tip('<?php echo addslashes($usersOnlineTable) ?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('serverListTable'))"/><br/>
				<?php
			} ?>
			<?php
			/*
			$appNumber = 0;
			if($server->getType() == "cstrike") {
				$appNumber = 240;
			} else if($server->getType() == "dod") {
				$appNumber = 300;
			}
			$appNumber = $serverData['appId'];
			*/
			?>
			<a href='steam://connect/<?php echo $server->getIp()?>:<?php echo $server->getPort() ?>'><?php echo $LAN_SERVERLIST_007; ?></a>
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
		function SecondsToString($sec, $textual=true) {
			$div = array( 2592000, 604800, 86400, 3600, 60, 1 );
			if($textual)
			{
				$desc = array ('mo','wk','d','hr','min','sec');
				$ret = "";
				for ($i=0;$i<count($div);$i++)
				{
					if (($cou = round($sec / $div[$i])) >= 1)
					{
						$ret .= $cou.' '.$desc[$i].', ';
						$sec %= $div[$i];
					}
				}
				$ret = substr($ret,0,strlen($ret)-2);
			}else{
				$hours = floor ($sec / 60 / 60);
				$sec -= $hours * 60*60;
				$mins = floor ($sec / 60);
				$secs = $sec % 60;
				$ret = $hours . ":" . $mins . ":" . $secs;
			}
			return $ret;
	  }
    ?>
  </tr>
  </table>
</div>
