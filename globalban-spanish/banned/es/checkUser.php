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

require_once(ROOTDIR."/include/class.rcon.php");
require_once(ROOTDIR."/include/database/class.BanQueries.php");
require_once(ROOTDIR."/include/database/class.BadNameQueries.php");
require_once(ROOTDIR."/include/database/class.ServerQueries.php");
require_once(ROOTDIR."/include/objects/class.Length.php");
require_once(ROOTDIR."/include/objects/class.Server.php");

// Get the hash value passed from ES
$hash = $_GET['hash'];

if($hash == $config->matchHash) {

  $steamId = $_GET['steamId'];
  $serverId = $_GET['sid'];
  $nameOfBanned = $_GET['name'];
  $ipOfBanned = $_GET['ip'];

  $banQueries = new BanQueries();
  $badNameQueries = new BadNameQueries();
  
  $kickedByName = false;
  
  $namesToKick = $badNameQueries->getKickNames();
  // Loop through the names to kick to see if the word exists in the user's name
  foreach($namesToKick as $nameToKick) {
    if(strpos(strtolower($nameOfBanned), $nameToKick->getBadName()) !== false) {
      kickUser($steamId, $serverId, "Your name contains the word ".$nameToKick->getBadName()." which is not allowed on the server");
      $kickedByName = true;
      break; // Break out of the loop
    }
  }

  // No point checking if they were kicked by name
  if(!$kickedByName) {
    // Determine if this user is IP banned, if so kick them
    if($banQueries->isIpBanned($ipOfBanned)) {
      kickUser($steamId, $serverId, $config->banMessage);
    }

    // Check to see if the user does exist in the ban list (if active)
    if($banQueries->doesUserExist($steamId)) {
      $now = time(); // Get the time now in seconds

    	$bannedUser = $banQueries->getBannedUserBySteamId($steamId);

    	$length = new Length();
      $length->setLength($bannedUser->getLength());
    	$length->setTimeScale($bannedUser->getTimeScale());
    	$lengthInSec = $length->getLengthInSeconds();

      // Pending bans are banned for X days
      if($bannedUser->getPending() == 1) {
        // Kick the user if the ban is 24 hours or less
        if($lengthInSec > 0 && $lengthInSec/3600 <= 24) {
          kickUser($steamId, $serverId, $config->banMessage);
        } else {
          // Kick the user for the first 5 days that their ban is in pending mode
          $addDate = $bannedUser->getAddDate();

          $daysToKeepBanned = 24*3600*$config->daysBanPending;
          $expireDate = $addDate + $daysToKeepBanned;

          // Kick the user if it's still within X days of the pending ban add
          if($expireDate > $now) {
            kickUser($steamId, $serverId, $config->banMessage);
          }
        }
      } else { // Handle non-pending bans normally
        // If length is 0, don't bother checking expire_date
        if($lengthInSec == 0) {
          // Send rcon command to kick user
          kickUser($steamId, $serverId, $config->banMessage);
        } else {
          // Check expire date to today's date
          if($bannedUser->getExpireDate() > $now) {
            kickUser($steamId, $serverId, $config->banMessage);
          }
        }
      }

      // If their name is empty, update it
      if($bannedUser->getName() == "" || $bannedUser->getName() == null) {
        $banQueries->updateBanName($nameOfBanned, $steamId);
      }

      // Update their IP
      $banQueries->updateBanIp($ipOfBanned, $steamId);

    } // Make sure we found a user
  }
} // End hash match

// Kick the user from the specified server
function kickUser($steamId, $serverId, $message) {
  // Leave this in to be compatible with the alternate thread version
  $kick = "kickid";
  $command = $kick." \"".$steamId."\" ".$message;
  echo $command;
  
  // This will send an RCON command to the server
  $serverQueries = new ServerQueries();

  $server = $serverQueries->getServer($serverId);

  $r = new rcon($server->getIp(),$server->getPort(),$server->getRcon());
  if($r->isValid()) {
    $r->Auth();
    $r->kickUser($steamId, $message);
  }
}
?>
