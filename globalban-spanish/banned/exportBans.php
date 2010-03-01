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

/**
 * This is a standalone page to save a file.  All globals/constants on the index.php
 * page should be declared here so that no errors occur.
 */
define("ROOTDIR", dirname(__FILE__)); // Global Constant of root directory

require_once(ROOTDIR."/config/class.Config.php");
$config = new Config(); // All configuration variables are contained in this object

require_once(ROOTDIR."/include/database/class.BanQueries.php");
require_once(ROOTDIR."/include/objects/class.BannedUser.php");
require_once(ROOTDIR."/include/php4functions.php");

$allBans = $_POST['allBans'];
$permaBans = $_POST['permaBans'];
$saveSQL = $_POST['saveSQL'];
$demosOnly = $_POST['demosOnly'];

if($allBans == 1) {
  $all = true;
} else {
  $all = false;
}

if($demosOnly == 1) {
  $demosOnly = true;
} else {
  $demosOnly = false;
}

if($saveSQL == 1) {
  $saveSQL = true;
} else {
  $saveSQL = false;
}

if($saveSQL) {
  $filename = $config->siteName."_banned_users.xml";
  header("Content-type: text/xml charset=UTF-8");
} else {
  $filename = $config->siteName."_banned_users.cfg";
  header("Content-type: text/plain");
}

$header = "Content-Disposition: attachment; filename=\"".$filename."\"";
header($header);

$banQueries = new BanQueries();

$bannedUsers = $banQueries->downloadActiveBans($all, $demosOnly);

function strleft($s1, $s2) { return substr($s1, 0, strpos($s1, $s2)); }

function selfURL() {
  $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
  $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
  $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
  return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}

$url = selfURL();
$url = substr($url, 0, strrpos($url, "/")) . "/";

if($saveSQL) {
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";?>

<GlobalBans>
<?php
foreach($bannedUsers as $bannedUser) {
?>
  <Ban>
    <SteamID><?php echo $bannedUser->getSteamId() ?></SteamID>
    <IP><?php echo $bannedUser->getIp() ?></IP>
    <Name><?php echo undoUnwantedConvert(utf8_to_html(convertXmlSpecial(stripslashes($bannedUser->getName())))) ?></Name>
    <Length><?php echo $bannedUser->getLength() ?></Length>
    <TimeScale><?php echo $bannedUser->getTimeScale() ?></TimeScale>
    <AddDate><?php echo $bannedUser->getAddDate() ?></AddDate>
    <ExpireDate><?php echo $bannedUser->getExpireDate() ?></ExpireDate>
    <Webpage><?php echo $url ?></Webpage>
  </Ban>
<?php
}
?></GlobalBans><?php
} else {
// Empty line after banid print is needed to force a new line
foreach($bannedUsers as $bannedUser) {
?>
banid <?php echo $bannedUser->getLength() ?> <?php echo $bannedUser->getSteamId() ?>

<?php
}
}

function convertXmlSpecial($string) {
  $string = str_replace("&", "&amp;", $string);
  $string = str_replace("'", "&apos;", $string);
  $string = str_replace("\"", "&quot;", $string);
  $string = str_replace("<", "&lt;", $string);
  $string = str_replace(">", "&gt;", $string);
  return $string;
}

function utf8_to_html($data) {
  return preg_replace("/([\\xC0-\\xF7]{1,1}[\\x80-\\xBF]+)/e", '_utf8_to_html("\\1")', $data);
}

function _utf8_to_html($data) {
  $ret = 0;
  foreach((str_split(strrev(chr((ord($data{0}) % 252 % 248 % 240 % 224 % 192) + 128) . substr($data, 1)))) as $k => $v)
      $ret += (ord($v) % 128) * pow(64, $k);
  return "&#$ret;";
}

function undoUnwantedConvert($string) {
  $string = str_replace("&amp;#", "&#", $string);
  return $string;
}

?>
