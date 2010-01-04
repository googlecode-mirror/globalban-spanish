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
require_once(ROOTDIR."/include/database/class.ServerQueries.php");

$serverQueries = new ServerQueries();

$servers = $serverQueries->getServers();

// Cycle through each server
foreach($servers as $server) {
  ?>
  <h3 id="server:<?php echo $server->getId()?>">Updating Ban Reasons for <?php echo $server->getName()?> <img src="images/wait.gif"/></h3>
  <?php
}
?>
<h5>Note: The Ban Length List will continue to upload if you navigate off this page.  However, reloading this page or navigating
back too quickly can have odd results and require a new upload.</h5>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script language="Javascript" type="text/javascript">
<?php
foreach($servers as $server) {
?>
  uploadBanLengths(<?php echo $server->getId()?>);
<?php
}
?>
</script>
