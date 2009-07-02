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

	kickUser($steamId, $serverId, $config);

    if($configOdonel->enableAutoPoste107Forum) {
      // Use this to build the URL link (replace processWebBan with updateBan)
      $url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	  $url = str_replace("processWebBan", "banlist", $url);
      $postId = NewPostForum_e107(addslashes($bannedName)." - ".addslashes($steamId),"[b]Admin:[/b] [color=#009900]".addslashes($username)."[/color]\r\n\r\n[b]Nick Baneado: [/b][color=#990000][link=".$url."&searchText=".addslashes($steamId)."]".addslashes($bannedName)." - ".addslashes($steamId)."[/link][/color]\r\n\r\n[b]Motivo:[/b] ".$reasonQueries->getReason($reason)."\r\n\r\n[b]Periodo:[/b] ".$length->getReadable(), time(),$configOdonel);
	  UpdateBanWebpage ($postId , $banId, $configOdonel);
	}

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
      $url = str_replace("processWebBan", "updateBan", $url);

      $body .= "\n\n";
      $body .= "Haga click en el siguiente link para ver el ban: <a href='".$url."&banId=".$banId."'>Nuevo Ban</a>";
      $body .= "<p>".$bannedName." (".$steamId.") ha sido baneado de todos los servidores.</p>";  
	  if($configOdonel->enableAutoPoste107Forum) {
	    $body .= "<p>Post en el Foro: <a href='".$configOdonel->e107Url."e107_plugins/forum/forum_viewtopic.php?".$postId."'>Link</a></p>";
      }
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

function NewPostForum_e107($TituloPost, $AsuntoPost, $now, $configOdonel) {

	// Connecting, selecting database
	$link = mysql_connect($configOdonel->e107_dbHostName, $configOdonel->e107_dbUserName, $configOdonel->e107_dbPassword)
	    or die('No se pudo conectar a la BD_e107: ' . mysql_error());
	echo 'Connected successfully';
	mysql_select_db($configOdonel->e107_dbName) or die('Could not select database e107');
	
	// Performing SQL query
	$query = "INSERT INTO `".$configOdonel->e107TablePrefix."forum_t` (`thread_id`, `thread_name`, `thread_thread`, `thread_forum_id`, `thread_datestamp`, `thread_parent`, `thread_user`, `thread_views`, `thread_active`, `thread_lastpost`, `thread_s`, `thread_edit_datestamp`, `thread_lastuser`, `thread_total_replies`) ";
	$query .= "VALUES (NULL, '".$TituloPost."', '".$AsuntoPost."', '".$configOdonel->e107_bans_forum_category_number."', '".$now."', '0', '".$configOdonel->e107_GlobalBan_user."', '0', '1', '".$now."', '0', '0', '', '0')";
	
	mysql_query($query) or die('Query failed: ' . mysql_error());
	
	$insertId = mysql_insert_id();
    
	// Closing connection
	mysql_close($link);    
    
	return $insertId;
}

function UpdateBanWebpage ($postId , $banId, $configOdonel){

	$banQueries = new BanQueries();
	$banQueries->updateBanWebpage ($configOdonel->e107Url."e107_plugins/forum/forum_viewtopic.php?".$postId , $banId);

}
?>