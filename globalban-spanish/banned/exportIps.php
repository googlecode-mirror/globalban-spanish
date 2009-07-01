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

include_once(ROOTDIR."/include/database/class.BanQueries.php");
require_once(ROOTDIR."/include/objects/class.Ip.php");

$filename = $config->siteName."_banned_ip.cfg";
$header = "Content-Disposition: attachment; filename=\"".$filename."\"";
header("Content-type: text/plain");
header($header);

$banQueries = new BanQueries();

$bannedIps = $banQueries->downloadActiveIps();

// Empty line after banid print is needed to force a new line
foreach($bannedIps as $bannedIp) {
?>
addip 0 <?=$bannedIp->getIp()?>

<?php
}
?>
