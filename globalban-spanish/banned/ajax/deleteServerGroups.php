<?php
/*
    EDIT : File created by Fantole
	http://www.css-ressource.com
	
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

$id = $_GET['id'];
$name = $_GET['name'];

// Update the server status
$serverQueries = new ServerQueries();

if(!empty($id)) {
  //$success = "true";
  $success = $serverQueries->deleteServerGroup($id, $name);
} else {
  $success = "false";
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
  <id><?php echo $id?></id>
	<name><?php echo convertXmlSpecial($name)?></name>
	<success><?php echo $success?></success>
</root>
