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
  *  SMF Specific Integration.  These are ignored if the $generic variable above
  *  is set to 1.
  *
  *  The following groups can ban, however the limited User Group can initiate a ban
  *  but it can not become activated until a Ban Manager Approves the Ban.  The Admin
  *  Group is the group of users that have full admin right and can ban who ever they
  *  please when ever they want but can only modify their own bans.  The Ban Manager
  *  group consists of people that can approve a 'limited user's' ban, however
  *  they can also enable or diable a ban completely and are able to change
  *  ny bans at their whim.
  */
   
  include_once(ROOTDIR."/include/database/class.UserQueries.php");
  
  // Admin Variables
  $banManager = false; // Allowed to do everything regarding bans
  $admin = false; // Can only add (by-passes pending)
  $member = false; // Can only add pending
  $fullPower = false; // Allowed EVERYTHING regarding the server

  $userQuery = new UserQueries();
  $user = new User();

  $user = $userQuery->getUserInfo($user_info['username']); // Place stuff into user object

  // Only do this if they are active
  if($user->getActive() == 1) {
    // Member
    if(in_array($config->memberGroup, $user_info['groups'])) {
      $member = true;
    }

    // Admin
    if(in_array($config->adminGroup, $user_info['groups'])) {
      $admin = true;
    }

    // Ban Manager
    if(in_array($config->banManagerGroup, $user_info['groups'])) {
      $banManager = true;
    }

    // Full Power
    if(in_array($config->fullPowerGroup, $user_info['groups'])) {
      $fullPower = true;
    }
  }
?>
