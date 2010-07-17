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
   * Admin Variables
   */
  
  $fullPower = false;
  $banManager = false;
  $admin = false;
  $member = false;
  
  if(!isset($accessLevel)){
   $accessLevel = -1;
  }
  
  switch($accessLevel) {
  case 1:
    $fullPower = true;// Allowed EVERYTHING regarding the server
    break;
  case 2:
    $banManager = true; // Allowed to do everything regarding bans
    break;
  case 3:
    $admin = true; // Can only add (by-passes pending)
    break;
  case 4:
    $member = true; // Can only add pending
    break;
  }
?>
