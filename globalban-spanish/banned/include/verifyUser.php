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

// Start a Session
session_start();
// Check if ALL cookies exist, if they do proceed to have them as "logged in" (remember me feature)
// Otherwise, check the session to see if they have logged in "this" session
if((isset($_COOKIE['gbu']) && isset($_COOKIE['gbp'])) || ($_SESSION['accessLevel'] && $_SESSION['name']))
{	
	if(!$_SESSION['accessLevel'] && !$_SESSION['name'] && $page != "logout") // If session is not a "valid" session
	{
		// Do a database verify before registering values
		require_once(ROOTDIR."/include/database/class.UserQueries.php");
		require_once(ROOTDIR."/include/objects/class.User.php");
		$userQuery = new UserQueries();
		$user = new User();
		
		$user = $userQuery->getUserInfo($_COOKIE['gbu']); // Place stuff into user object		
		 
		// Make sure we have a valid user object
		if(isset($user)) {
  		// Check if md5 encrypted passwords match
  		// If they do, auto log them in
  		// Also make sure that the user is active
  		if($user->getPassword() == $_COOKIE['gbp'] && $user->getActive() == 1) {
  			// Register Session Values
  			$_SESSION['name'] = $_COOKIE['gbu']; // Username
  			$_SESSION['password'] = $_COOKIE['gbp']; // md5 encrypted
  			$_SESSION['accessLevel'] = $user->getAccessLevel();
  			$_SESSION['steamId'] = $user->getSteamId(); // Steam ID of logged in user
  		}
		} else {
      // Destroy cookies since the user stored in the cookie does not seem to exist
      // Destroy cookies
      setcookie("gbu", NULL, time()-60*60*24*100, "/");
      setcookie("gbp", NULL, time()-60*60*24*100, "/");
      unset($_COOKIE['gbu']);
      unset($_COOKIE['gbp']);
    }
	}
  	
	$accessLevel = $_SESSION['accessLevel']; // User access level
}
?>
