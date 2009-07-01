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
require_once(ROOTDIR."/include/database/class.ServerQueries.php");
include_once(ROOTDIR."/include/database/class.UserQueries.php");
include_once(ROOTDIR."/include/database/class.LengthQueries.php");
require_once(ROOTDIR."/include/objects/class.Server.php");
require_once(ROOTDIR."/include/objects/class.Length.php");

// Variables
$steamId = $_POST['steamId'];
$lengthId = $_POST['length']; // Length ID
$reason = $_POST['reason']; // Reason id number
$bannedName = $_POST['bannedName']; // Name of person banned
$serverId = $_POST['server']; // Server ID

// Make sure the user has the privileges
if($member || $admin || $banManager || $fullPower) {
  $allowedToBan = true;
}

if($allowedToBan) {

  $lengthQueries = new LengthQueries();
  $length = $lengthQueries->getBanLength($lengthId);

  $lengthInSec = $length->getLengthInSeconds();
  $now = time() + $lengthInSec;

  // Members can add, but the ban does not take immediate affect (must be approved)
  // All others can ban and it will become active
  if($member) {
    $pending = 1;
  } else {
    $pending = 0;
  }
  
  if($config->enableSmfIntegration) {
    $username = $user_info['username'];
  } else {
    $username = $_SESSION['name'];
  }
  
  $banQueries = new BanQueries();
  $userQueries = new UserQueries();

  $user = $userQueries->getUserInfo($username);
  
  $banId = 0;
  
  // Check to see if we are adding an IP ban
  if(isset($_POST['ipBan'])) {
    $banQueries->addIpBan($_POST['ip']);
  } else { // Otherwise we are adding a regular ban
  
    // Add the ban
    $banId = $banQueries->addBan($steamId, $length->getLength(), $length->getTimeScale(), $now, $reason, $username, $pending, $bannedName, $serverId, null, $user->getSteamId());

    kickUser($steamId, $serverId, $config);

    if($config->sendEmails) {
      // Email
      $subject = "Ban Added By Web App";

      $body = "<html><body>";
      $body .= "The following ban has been added by " . $username;
      if($member) {
        $body .= " and MUST be reviewed.";
      }
      $body .= "\n\n";

      // Use this to build the URL link (replace processWebBan with updateBan)
      $url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
      str_replace("processWebBan", "updateBan", "$url");

      $body .= "\n\n";
      $body .= "Click on the following link to review the newly added ban: <a href='".$url."&banId=".$banId."'>New Ban</a>";
      $body .= "<p>".$bannedName." (".$steamId.") was banned from all servers.</p>";
      $body .= "</body></html>";

      $banManagerEmails = $config->banManagerEmails;
      for($i=0; $i<count($banManagerEmails); $i++) {

        // To send HTML mail, the Content-type header must be set
        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
        // Additional headers
        $headers .= "From: ".$config->siteName." Ban Management <".$config->emailFromHeader.">" . "\r\n";
        mail($banManagerEmails[$i], $subject, $body, $headers);
      }
      // Finish Email
    }
  }
  
  
}

if($banId == "-1") {
  header( 'Location: index.php?page=addBan&dupe=1' );
} else {
  header( 'Location: index.php?page=banlist' );
}

echo $banId;

function kickUser($steamId, $serverId, $config) {

  $serverQueries = new ServerQueries();
  
  $server = $serverQueries->getServer($serverId);
  
  $r = new rcon($server->getIp(),$server->getPort(),$server->getRcon());
  $r->Auth();
  $r->rconCommand("kickid " . $steamId . " '" . $config->banMessage . "'");
}
?>
