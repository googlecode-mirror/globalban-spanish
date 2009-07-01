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

require_once(ROOTDIR."/include/database/class.ServerQueries.php");
require_once(ROOTDIR."/include/database/class.LengthQueries.php");
require_once(ROOTDIR."/include/class.rcon.php");

$serverId = $_GET['serverId'];

$serverQueries = new ServerQueries();
$lengthQueries = new LengthQueries();

$server = $serverQueries->getServer($serverId);
$banLengths = $lengthQueries->getLengthList();

// Create an rcon object to connect to a single server on each iteration
$r = new rcon($server->getIp(),$server->getPort(),$server->getRcon());


$active = 0;
// Make sure it connected to the server
if($r->isValid()) {
  $active = 1;
  $r->Auth(); // Establish the connection
  /*
  // First delete the existing clanMembers keygroup and then re-create it
  $r->sendRconCommand("es_keygroupdelete GlobalBan_Length; es_keygroupcreate GlobalBan_Length");

  // Cycle through each user and create the keygroup
  foreach($banLengths as $banLength) {

    // Create a key for the user
    $command = "gb_addBanLength ".$banLength->getId()." \"".$banLength->getLength()."\" \"".$banLength->getTimeScale()."\"";
    $r->sendRconCommand($command);
  }

  // Now save the keygroup
  $r->sendRconCommand("es_keygroupsave GlobalBan_Length |GlobalBan");

  // Now reload the clan_db script
  $r->sendRconCommand("es_reload GlobalBan");
  */
  $r->sendRconCommand("es gb_refreshBanLengths");
}

// Send back the new active state
header('Content-Type: text/xml');
header("Cache-Control: no-cache, must-revalidate");

function convertXmlSpecial($string) {
  $string = str_replace("&", "&amp;", $string);
  $string = str_replace("'", "&apos;", $string);
  $string = str_replace("\"", "&quot;", $string);
  $string = str_replace("<", "&lt;", $string);
  $string = str_replace(">", "&gt;", $string);
  return $string;
}
?>
<?echo "<?xml version=\"1.0\" ?>";?>
<root>
  <id><?=$server->getId()?></id>
	<name><?=convertXmlSpecial($server->getName())?></name>
	<active><?=$active?></active>
</root>
