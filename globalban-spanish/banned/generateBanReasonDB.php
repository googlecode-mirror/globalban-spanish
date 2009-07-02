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
 * This creates a text file called es_GlobalBan_Reason_db.txt which the sever admin
 * must place in eventscripts/GlobalBan whenever they add or remove ban reasons. 
 * This is the ONLY file that does not use index.php as a reference 
 */ 

define("ROOTDIR", dirname(__FILE__)); // Global Constant of root directory

require_once(ROOTDIR."/include/database/class.Database.php"); //Database class
require_once(ROOTDIR."/include/database/class.ReasonQueries.php"); //Database class
 
header('Content-Type: text');
header('Content-Disposition: attachment; filename="es_GlobalBan_Reason_db.txt"');

$db = new Database(); // Create a new database object
$reasonQueries = new ReasonQueries();

$reasons = $reasonQueries->getReasonList();

?>
GlobalBan_Reason
{
<?php

for($i=0; $i<count($reasons); $i++) { 
    $reason = $reasons[$i];
?>   "<?=$reason->getId()?>"
   {
    "reasonId"       "<?=$reason->getId()?>"
	  "reasonText"     "<?=$reason->getReason()?>"
   }
<?php
}
?>
}
