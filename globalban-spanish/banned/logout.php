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

// Destroy cookies
setcookie("gbu", NULL, time()-60*60*24*100, "/");
setcookie("gbp", NULL, time()-60*60*24*100, "/");
unset($_COOKIE['gbu']);
unset($_COOKIE['gbp']);
$_COOKIE = array();
// Destroy session
$_SESSION['accessLevel'] = 0;
unset($_SESSION['accessLevel']);
unset($_SESSION['name']);
unset($_SESSION['password']);
session_destroy();
?>

<div class="tborder">
  <div id="tableHead">
    <div><b>Logout Successful</b></div>
  </div>
</div>
<p>You have logged out successfully!</p>
