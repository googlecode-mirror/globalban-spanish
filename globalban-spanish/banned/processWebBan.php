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
require_once(ROOTDIR."/include/database/class.ReasonQueries.php");

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_processWebBan.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_processWebBan.php");

// Initialize Objects
$reasonQueries = new ReasonQueries();

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

    if ($banId > 0){

        kickUser($steamId, $serverId, $config);
        
        // Use this to build the URL link (replace processWebBan with updateBan)
        $url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $url = str_replace("processWebBan", "banlist", $url)."&searchText=".addslashes($steamId);

        if($config->enableAutoPoste107Forum) {
          $postId = NewPostForum_e107(addslashes($bannedName)." - ".addslashes($steamId),"[b]".$LAN_PROCESSWEBBAN_021."[/b] [color=#009900]".addslashes($username)."[/color]\r\n\r\n[b]".$LAN_PROCESSWEBBAN_022." [/b][color=#990000][link=".$url."]".addslashes($bannedName)." - ".addslashes($steamId)."[/link][/color]\r\n\r\n[b]".$LAN_PROCESSWEBBAN_023." [/b]".$reasonQueries->getReason($reason)."\r\n\r\n[b]".$LAN_PROCESSWEBBAN_024." [/b]".$length->getReadable(), time(),$config);
          UpdateBanWebpage ($postId , $banId, $config);
        }

        if($config->sendEmails) {
          // Email
          $subject = $LAN_PROCESSWEBBAN_001." ".$username;	

          $body = "<html><body><h2>".$LAN_PROCESSWEBBAN_001." ".$username."</h2><br/>";
          $body .= $LAN_PROCESSWEBBAN_003." <b>". $username ."</b>";
          if($member) {
            $body .= " ".$LAN_PROCESSWEBBAN_004;
          }
          $body .= ":<br/><br/><p><b>".$bannedName."</b> [".$steamId."] ".$LAN_PROCESSWEBBAN_007."</p>";  
          $body .= "<br/>".$LAN_PROCESSWEBBAN_005." <a href='".$url."'>".$LAN_PROCESSWEBBAN_006."</a>";
          if($config->enableAutoPoste107Forum) {
            $body .= "<br/><p>".$LAN_PROCESSWEBBAN_008." <a href='".$config->e107Url."e107_plugins/forum/forum_viewtopic.php?".$postId."'>".$LAN_PROCESSWEBBAN_009."</a></p>";
          }
          $body .= "</body></html>";

          $banManagerEmails = $config->banManagerEmails;
          for($i=0; $i<count($banManagerEmails); $i++) {

            // To send HTML mail, the Content-type header must be set
            $headers  = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
            // Additional headers
            $headers .= "From: ".$config->siteName." ".$LAN_PROCESSWEBBAN_011." <".$config->emailFromHeader.">" . "\r\n";
            mail($banManagerEmails[$i], $subject, $body, $headers);
          }
          // Finish Email
        }
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

function NewPostForum_e107($TituloPost, $AsuntoPost, $now, $config) {

	// Connecting, selecting database
	$link = mysql_connect($config->e107_dbHostName, $config->e107_dbUserName, $config->e107_dbPassword)
	    or die($LAN_PROCESSWEBBAN_012." ".mysql_error());

	mysql_select_db($config->e107_dbName) or die($LAN_PROCESSWEBBAN_013);
	
	// Performing SQL query
	$query = "INSERT INTO `".$config->e107TablePrefix."forum_t` (`thread_id`, `thread_name`, `thread_thread`, `thread_forum_id`, `thread_datestamp`, `thread_parent`, `thread_user`, `thread_views`, `thread_active`, `thread_lastpost`, `thread_s`, `thread_edit_datestamp`, `thread_lastuser`, `thread_total_replies`) ";
	$query .= "VALUES (NULL, '".$TituloPost."', '".$AsuntoPost."', '".$config->e107_bans_forum_category_number."', '".$now."', '0', '".$config->e107_GlobalBan_user."', '0', '1', '".$now."', '0', '0', '', '0')";
	
	mysql_query($query) or die('Query failed: ' . mysql_error());
	
	$insertId = mysql_insert_id();
    
	// Closing connection
	mysql_close($link);    
    
	return $insertId;
}

function UpdateBanWebpage ($postId , $banId, $config){

	$banQueries = new BanQueries();
	$banQueries->updateBanWebpage ($config->e107Url."e107_plugins/forum/forum_viewtopic.php?".$postId , $banId);

}
?>