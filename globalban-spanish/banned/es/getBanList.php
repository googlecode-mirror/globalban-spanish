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

require_once(ROOTDIR."/include/database/class.BanQueries.php");

$banQueries = new BanQueries();
$bannedUsers = $banQueries->downloadActiveBans(true, false);

// Cycle through each user and create the keygroup
foreach($bannedUsers as $bannedUser) {
  
  $now = time();  // In seconds
  $expireDateSeconds = $bannedUser->getExpireDateSeconds();
  
  $banTime = 0;
  
  // Determine how much longer the ban is based on the expire date
  if($expireDateSeconds > 0) {
    $banTime = $now - $expireDateSeconds;
  }

  if($banTime > -1) {
    echo "banid ".floor(($banTime/60))." ".$bannedUser->getSteamId()."\n";
  }
}
?>