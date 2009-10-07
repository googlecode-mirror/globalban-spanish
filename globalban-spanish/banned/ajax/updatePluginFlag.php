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

require_once(ROOTDIR."/include/database/class.AdminGroupQueries.php");

$groupId = $_POST['groupId'];
$flagId = $_POST['flagId'];
$checked = $_POST['checked'];

$adminGroupQueries = new AdminGroupQueries();

$success = 0;

// Must be logged in to change the status
if($fullPower) {
  $success = $adminGroupQueries->updateGroupPluginFlag($groupId, $flagId, $checked);
}

// Flip it for the case of failure
if($checked == 0) {
  $checked = 1;
} else {
  $checked = 0;
}

// Send back the new active state
header('Content-Type: text/xml');
header("Cache-Control: no-cache, must-revalidate");
?>
<?echo "<?xml version=\"1.0\" ?>";?>
<root>
  <groupId><?php echo $groupId?></groupId>
  <flagId><?php echo $flagId?></flagId>
  <success><?php echo $success?></success>
  <checked><?php echo $checked?></checked>
</root>
