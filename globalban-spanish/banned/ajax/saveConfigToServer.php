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
require_once(ROOTDIR."/include/database/class.UserQueries.php");
require_once(ROOTDIR."/include/class.rcon.php");

function selfURL() {
  $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
  $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
  $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
  return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}

function strleft($s1, $s2) {
  return substr($s1, 0, strpos($s1, $s2));
}

$url = selfURL();
$url = substr($url, 0, strrpos($url, "/")) . "/";

$serverId = $_GET['serverId'];

$serverQueries = new ServerQueries();

$server = $serverQueries->getServer($serverId);

// Create an rcon object to connect to a single server on each iteration
$r = new rcon($server->getIp(),$server->getPort(),$server->getRcon());
$success = 0;

// Make sure it connected to the server
if($r->isValid()) {
  // Is admin banning allowed
  $adminBanning = 0;
  if($config->allowAdminBans) $adminBanning = 1;

  $r->Auth(); // Establish the connection
  $command = "gb_saveConfig \"".$server->getId()."\" \"".$url."\" \"".$config->banMessage."\" \"".$config->matchHash."\" \"".$config->teachAdmins."\" \"".$config->siteName."\" \"".$adminBanning."\" ";
  $r->sendRconCommand($command);

  $success = 1; // Successfully updated
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
<?php echo "<?xml version=\"1.0\" ?>";?>
<root>
  <id><?php echo $server->getId()?></id>
	<name><?php echo convertXmlSpecial($server->getName())?></name>
	<port><?php echo $server->getPort()?></port>
	<success><?php echo $success?></success>
</root>
