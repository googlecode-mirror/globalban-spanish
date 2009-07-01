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

$adminGroupQueries = new AdminGroupQueries();

$missingPlugins = $adminGroupQueries->getUnaddedPluginList($groupId);

// The HTML must match what is in the manageAdminGroups.php file
?>
<select id="pluginList-<?=$groupId?>" name="plugin"><?php foreach($missingPlugins as $missingPlugin) { echo "<option value='".$missingPlugin->getId()."'>".$missingPlugin->getName()."</option>"; } ?></select>