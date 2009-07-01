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

include_once(ROOTDIR."/include/objects/class.User.php");
include_once(ROOTDIR."/include/database/class.UserQueries.php");

// Must be logged in to change the status
if($fullPower) {
  $user = new User();
  $userQueries = new UserQueries();

  $user = $userQueries->getUserInfoById($_GET['id']);

  $user->setName(addslashes($_GET['name']));
  $user->setEmail($_GET['email']);
  $user->setAccessLevel($_GET['accessLevel']);
  $user->setSteamId($_GET['steamId']);

  $error = "";
  if(!preg_match("/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/i", $user->getEmail()) && !$config->enableSmfIntegration) {
    // We don't care if the email is empty
    if($user->getEmail() != "") {
      $error .= "Bad Email Address.  ";
    }
  }
  if(!preg_match("/^STEAM_[01]:[01]:\d{0,10}$/", $user->getSteamId())) {
    $error .= "Bad Steam ID.  ";
  }

  // Update if no errors
  if($error == "") {
    $userQueries->updateUser($user);
  }

  // Send back the new active state
  header('Content-Type: text/xml');
  header("Cache-Control: no-cache, must-revalidate");
?>
<?echo "<?xml version=\"1.0\" ?>";?>
<root>
  <id><?=$user->getId()?></id>
	<name><?=$user->getName()?></name>
	<steamid><?=$user->getSteamId()?></steamid>
	<error><?=$error?></error>
</root>
<?php
}
?>