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

include_once(ROOTDIR."/include/database/class.BanQueries.php");
include_once(ROOTDIR."/include/database/class.LengthQueries.php");
require_once(ROOTDIR."/include/objects/class.Length.php");

$banId = $_POST['banId'];
$bannedUser = $_POST['bannedUser'];
$lengthId = $_POST['length']; // Length ID
$reason = $_POST['reason']; // Reason id number
$serverId = $_POST['serverId']; // Server ID of ban
$comments = $_POST['comments']; // comments

// Make sure the user is an UNBU member, admin, or ban manager
if($member || $admin || $banManager || $fullPower) {
  $allowedToBan = true;
}

if($allowedToBan) {
  
  $banQueries = new BanQueries();
  
  $lengthQueries = new LengthQueries();
  $length = $lengthQueries->getBanLength($lengthId);
  
  // We are banning an IP
  if(isset($_POST['banIp'])) {
    $banQueries->addIpBan($_POST['ip']);
    header("Location: index.php?page=updateBan&banId=".$banId);
  } else if(isset($_POST['updateBan'])) { // We are updating ban information
  
    if($config->enableSmfIntegration) {
      $username = $user_info['username'];
    } else {
      $username = $_SESSION['name'];
    }

    if($member) {
      $pending = 1;
    } else {
      $pending = 0;
    }
    
    // Get add date of ban
    $addDate = $banQueries->getBanAddDate($banId);
    $lengthInSec = $length->getLengthInSeconds();

    $newExpireDate = $addDate + $lengthInSec;

    // Update ban
    $banQueries->updateWebBanWithLength($length->getLength(), $length->getTimeScale(), $newExpireDate, $reason, $pending, $user, $serverId, $bannedUser, $banId, $comments);
    
    // Email
    $subject = "Ban Updated for ".$bannedUser;

    $body = "<html><body>";
    $body .= "The following ban has been updated by " . $username;

    // Use this to build the URL link (replace processWebBanUpdate with updateBan)
    $url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    str_replace("processWebBanUpdate", "updateBan", "$url");

    $body .= "\n\n";
    $body .= "Click on the following link to review the change: <a href='".$url."&banId=".$banId."'>Updated Ban</a>";
    $body .= "</body></html>";

    if($config->sendEmails) {
      $banManagerEmails = $config->banManagerEmails;
      for($i=0; $i<count($banManagerEmails); $i++) {

        // To send HTML mail, the Content-type header must be set
        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
        // Additional headers
        $headers .= "From: ".$config->siteName." Ban Management <".$config->emailFromHeader.">" . "\r\n";

        // Send an email to those who wish to be notified of updated bans
        mail($banManagerEmails[$i], $subject, $body, $headers);
      }
    }
    header( 'Location: index.php?page=banlist' );
  }
} else {
  echo "You lack the permissions to update a ban.";
}
?>
