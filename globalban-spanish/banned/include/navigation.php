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

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_navigation.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_navigation.php");

?>
<div id="menu">
  <ul id="nav">
  <?php
  // Web
  if($configOdonel->webUrl) {
  ?>
  <li><a href="<?=$configOdonel->webUrl?>"><span><?=$LANNAV_001?></span></a></li>
  <?php
  }
  ?>

  <?php
  // Forums Button
  if($config->enableForumLink) {
  ?>
  <li><a href="<?=$config->forumURL?>"><span><?=$LANNAV_002?></span></a></li>
  <?php
  }
  ?>
  
  <?php
  // Ban List button
  ?>
  <li><a href="index.php?page=banlist&lg=<?=$LANGUAGE?>"><?=$LANNAV_003?></a></li>

  <?php
  // Ip List button
  ?>
  <li><a href="index.php?page=ipbanlist&lg=<?=$LANGUAGE?>"><?=$LANNAV_004?></a></li>

  <?php
  // Add Ban Button
  if($member || $admin || $banManager || $fullPower) {
  ?>
  <li><a href="index.php?page=addBan&lg=<?=$LANGUAGE?>"><?=$LANNAV_005?></a></li>
  <?php
  }
  ?>
  
  <?php
  // Add Ban Button
  if($fullPower) {
  ?>
  <li><a href="index.php?page=importBans&lg=<?=$LANGUAGE?>"><?=$LANNAV_006?></a></li>
  <?php
  }
  ?>
  
  <?php
  // Demos Button
  ?>
  <li><a href="index.php?page=demos&lg=<?=$LANGUAGE?>"><?=$LANNAV_018?></a></li>
  
  <?php // Server List Button ?>
  <li><a href="index.php?page=serverList&lg=<?=$LANGUAGE?>"><?=$LANNAV_007?></a></li>

  
  <?php
  // Profile Button
  if($member || $admin || $banManager || $fullPower) {
  ?>
  <li><a href="index.php?page=profile&lg=<?=$LANGUAGE?>"><?=$LANNAV_008?></a></li>
  <?php
  }
  ?>
  
  <?php
  // Menu for full power admins only
  if($fullPower) {
  ?>
  
    <li><a href="#"><?php echo $LANNAV_019 ?></a>
      <ul>
        <li><a href="index.php?page=configuration&adminPage=1&lg=<?=$LANGUAGE?>"><?=$LANNAV_009?></li>
        <li><a href="index.php?page=banReasons&adminPage=1&lg=<?=$LANGUAGE?>"><?=$LANNAV_010?></a></li>
        <li><a href="index.php?page=banLengths&adminPage=1&lg=<?=$LANGUAGE?>"><?=$LANNAV_011?></a></li>
        <li><a href="index.php?page=badNames&adminPage=1&lg=<?=$LANGUAGE?>"><?=$LANNAV_012?></a></li>
        <li><a href="index.php?page=manageServers&adminPage=1&lg=<?=$LANGUAGE?>"><?=$LANNAV_013?></a></li>
        <li><a href="index.php?page=manageServerGroups&adminPage=1&lg=<?=$LANGUAGE?>"><?=$LANNAV_014?></a></li>
        <li><a href="index.php?page=manageAdminGroups&adminPage=1&lg=<?=$LANGUAGE?>"><?=$LANNAV_015?></a></li>
        <li><a href="index.php?page=manageUsers&adminPage=1&lg=<?=$LANGUAGE?>"><?=$LANNAV_016?></a></li>
      </ul>
    </li>
  
  <?php
  }
  ?>

  <?php
  // Logout
  if(isset($_SESSION['accessLevel']) && $page != "logout" && !$config->enableSmfIntegration) {
  ?>
  <li><a href="index.php?page=logout&lg=<?=$LANGUAGE?>"><?=$LANNAV_017?></a></li>
  <?php
  }
  if ($adminPage==1) {
      $adPage = "&adminPage=1";
  }else{
      $adPage = ""; 
  }
  ?>
  </ul>
</div>
<ul>
    <a href="index.php?page=<?=$page.$adPage?>&lg=English"><img src="images/flags/gb_large.png" width="32" height="19" alt="English" /></a>
    <a href="index.php?page=<?=$page.$adPage?>&lg=Spanish"><img src="images/flags/es_large.png" width="32" height="19" alt="Spanish" /></a>
    <a href="index.php?page=<?=$page.$adPage?>&lg=French"><img src="images/flags/fr_large.png" width="32" height="19" alt="Français" /></a>
</ul>