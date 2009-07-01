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
?>
<div id="menu">
  <ul id="nav">
  <?php
  // Forums Button
  if($config->enableForumLink) {
  ?>
  <li><a href="<?=$config->forumURL?>">Forums</a></li>
  <?php
  }
  ?>
  
  <?php
  // Ban List button
  ?>
  <li><a href="index.php?page=banlist">Ban List</a></li>

  <?php
  // Ip List button
  ?>
  <li><a href="index.php?page=ipbanlist">IP Ban List</a></li>

  <?php
  // Add Ban Button
  if($member || $admin || $banManager || $fullPower) {
  ?>
  <li><a href="index.php?page=addBan">Add Ban</a></li>
  <?php
  }
  ?>
  
  <?php
  // Add Ban Button
  if($fullPower) {
  ?>
  <li><a href="index.php?page=importBans">Import Bans</a></li>
  <?php
  }
  ?>
  
  <?php
  // Demos Button
  ?>
  <li><a href="index.php?page=demos">Demos</a></li>
  
  <?php // Server List Button ?>
  <li><a href="index.php?page=serverList">Server List</a></li>

  
  <?php
  // Profile Button
  if($member || $admin || $banManager || $fullPower) {
  ?>
  <li><a href="index.php?page=profile">Profile</a></li>
  <?php
  }
  ?>
  
  <?php
  // Menu for full power admins only
  if($fullPower) {
  ?>
  
    <li><a href="#">Admin</a>
      <ul>
        <li><a href="index.php?page=configuration&adminPage=1">Configuration</a></li>
        <li><a href="index.php?page=banReasons&adminPage=1">Ban Reasons</a></li>
        <li><a href="index.php?page=banLengths&adminPage=1">Ban Lengths</a></li>
        <li><a href="index.php?page=badNames&adminPage=1">Bad Names</a></li>
        <li><a href="index.php?page=manageServers&adminPage=1">Servers</a></li>
        <li><a href="index.php?page=manageServerGroups&adminPage=1">Server Groups</a></li>
        <li><a href="index.php?page=manageAdminGroups&adminPage=1">Admin Groups</a></li>
        <li><a href="index.php?page=manageUsers&adminPage=1">Users</a></li>
      </ul>
    </li>
  
  <?php
  }
  ?>

  <?php
  // Logout
  if(isset($_SESSION['accessLevel']) && $page != "logout" && !$config->enableSmfIntegration) {
  ?>
  <li><a href="index.php?page=logout">Logout</a></li>
  <?php
  }
  ?>
  </ul>
</div>
