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
include_once(ROOTDIR."/include/database/class.UserQueries.php");

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_processWebBanUpdate.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_processWebBanUpdate.php");

$banId = $_POST['banId'];
$bannedUser = $_POST['bannedUser'];
$lengthId = $_POST['length']; // Length ID
$admin_banner = $_POST['admin_banner']; // Admin Name
$reason = $_POST['reason']; // Reason id number
$serverId = $_POST['serverId']; // Server ID of ban
$comments = $_POST['comments']; // comments
$bannedPost = $_POST['bannedPost']; // Link to Post about ban 
$ModifiedBy = $_POST['ModifiedBy'];
$fullPowerLevelEditUser = $_POST['fullPowerLevelEditUser'];

// Make sure the user is an UNBU member, admin, or ban manager
if($member || $admin || $banManager || $fullPower) {
  $allowedToBan = true;
}

if($allowedToBan) {
    
    // Make sure special chars for MySQL are escaped
    $bannedUser = addslashes($bannedUser);
    $bannedUser = str_ireplace(array("\"", "\r\n", "\n", "\r", ";"), "", $bannedUser); // Remove ; to prevent kick command inyection like name; quit or ; _restart

  
    $banQueries = new BanQueries();

    $lengthQueries = new LengthQueries();
    $length = $lengthQueries->getBanLength($lengthId);
    
    // Banned user information
    $bannedUserO = $banQueries->getBannedUser($banId);

    if($fullPower || $banManager || 
            (($bannedUserO->getBanner() == $_SESSION['name'] && !empty($_SESSION['name'])) && ($admin || $member)) || 
            (($bannedUserO->getBannerSteamId() == $_SESSION['steamId'] && !empty($_SESSION['steamId'])) && ($admin || $member))) {
  
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
        
            if (!$fullPowerLevelEditUser || $fullPower) {
                $ModifiedBy = $username;
            }

            if($member) {
                $pending = 1;
            } else {
                $pending = 0;
            }

            $userQueries = new UserQueries();

            $user = $userQueries->getUserInfo($admin_banner);
            
            // Get add date of ban
            $addDate = $banQueries->getBanAddDate($banId);
            $lengthInSec = $length->getLengthInSeconds();

            $newExpireDate = $addDate + $lengthInSec;

            // Update ban
            $banQueries->updateWebBanWithLength($length->getLength(), $length->getTimeScale(), $newExpireDate, $reason, $pending, $admin_banner, $ModifiedBy, $serverId, $bannedUser,$user->getSteamId(), $banId, $comments, $bannedPost);
            
            // Email
            $subject = $LAN_PROCESSWEBBANUPDATE_001." ".$bannedUser." ".$LAN_PROCESSWEBBANUPDATE_010." ".$username;

            $body = "<html><body><br/><h2>".$subject."</h2><br/><br/>";
            $body .= $LAN_PROCESSWEBBANUPDATE_002." <b>";
            if($member) {
                $body .= $LAN_PROCESSWEBBANUPDATE_003;
            } else if($admin) {
                $body .= $LAN_PROCESSWEBBANUPDATE_004;
            } else if($banManager || $fullPower) {
                $body .= $LAN_PROCESSWEBBANUPDATE_005;
            }
            $body .= "</b>.";
            // $body .= " <b>".$username."</b>";

            // Use this to build the URL link (replace processWebBanUpdate with updateBan)
            $link = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            $link = str_replace("processWebBanUpdate", "updateBan", $link);
         
            $body .= "\n\n";
            $body .= "<br/><br/>".$LAN_PROCESSWEBBANUPDATE_006." <a href='".$link."&banId=".$banId."'>".$LAN_PROCESSWEBBANUPDATE_007."</a>";
            $body .= "</body></html>";

            if($config->sendEmails) {
                $banManagerEmails = $config->banManagerEmails;
                for($i=0; $i<count($banManagerEmails); $i++) {

                    // To send HTML mail, the Content-type header must be set
                    $headers  = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
                    // Additional headers
                    $headers .= "From: ".$config->siteName." ".$LAN_PROCESSWEBBANUPDATE_008." <".$config->emailFromHeader.">" . "\r\n";

                    // Send an email to those who wish to be notified of updated bans
                    mail($banManagerEmails[$i], $subject, $body, $headers);
                }
            }
            header( 'Location: index.php?page=banlist' );
        }
    } else {
        echo $LAN_PROCESSWEBBANUPDATE_009;
    }
} else {
    echo $LAN_PROCESSWEBBANUPDATE_009;
}
?>
