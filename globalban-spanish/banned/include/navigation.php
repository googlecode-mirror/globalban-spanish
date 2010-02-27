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
  if($config->enableWebLink) {
      ?>
      <li><div align="center"><a href="<?php echo $config->webUrl?>"><span><?php echo $LANNAV_001; ?></span></a></div></li>
      <?php
  }
  ?>

  <?php
  // Forums Button
  if($config->enableForumLink) {
      ?>
      <li><div align="center"><a href="<?php echo $config->forumURL ?>"><span><?php echo $LANNAV_002; ?></span></a></div></li>
      <?php
  }
  ?>
  
  <?php
  // HlstatsX:CE
  if($config->enableHLstatsLink) {
      ?>
      <li><div align="center"><a href="<?php echo $config->HLstatsUrl ?>"><span><?php echo $LANNAV_020; ?></span></a></div></li>
      <?php
  }
  ?>
  
  <?php
  // Ban List button
  ?>
  <li><div align="center"><a href="index.php?page=banlist&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_003; ?></a></div></li>

  <?php
  // Ip List button
  ?>
  <li><div align="center"><a href="index.php?page=ipbanlist&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_004; ?></a></div></li>

  <?php
  // Add Ban Button
  if($member || $admin || $banManager || $fullPower) {
      ?>
      <li><div align="center"><a href="index.php?page=addBan&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_005; ?></a></div></li>
      <?php
  }
  ?>
  
  <?php
  // Add Ban Button
  if($fullPower) {
      ?>
      <li><div align="center"><a href="index.php?page=importBans&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_006; ?></a></div></li>
      <?php
  }
  ?>
  
  <?php
  // Demos Button
  ?>
  <li><div align="center"><a href="index.php?page=demos&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_018; ?></a></div></li>
  
  <?php // Server List Button ?>
  <li><div align="center"><a href="index.php?page=serverList&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_007; ?></a></div></li>

  
  <?php
  // Profile Button
  if($member || $admin || $banManager || $fullPower) {
      ?>
      <li><div align="center"><a href="index.php?page=profile&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_008; ?></a></div></li>
      <?php
  }
  ?>
  
  <?php
  // Menu for full power admins only
  if($fullPower) {
    ?>
    <li><div align="center"><a href="#"><?php echo $LANNAV_019 ?></a></div>
      <ul>
        <li><a href="index.php?page=configuration&adminPage=1&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_009; ?></a></li>
        <li><a href="index.php?page=banReasons&adminPage=1&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_010; ?></a></li>
        <li><a href="index.php?page=banLengths&adminPage=1&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_011; ?></a></li>
        <li><a href="index.php?page=badNames&adminPage=1&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_012; ?></a></li>
        <li><a href="index.php?page=manageServers&adminPage=1&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_013; ?></a></li>
        <li><a href="index.php?page=manageServerGroups&adminPage=1&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_014; ?></a></li>
        <li><a href="index.php?page=manageAdminGroups&adminPage=1&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_015; ?></a></li>
        <li><a href="index.php?page=manageUsers&adminPage=1&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_016; ?></a></li>
      </ul>
    </li>
    <?php
  }
  ?>

  <?php
  // Logout
  if(isset($_SESSION['accessLevel']) && $page != "logout" && !$config->enableSmfIntegration) {
      ?>
      <li><div align="center"><a href="index.php?page=logout&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_017; ?></a></div></li>
      <?php
  }else if(!$config->enableSmfIntegration) {
      ?>
      <li><div align="center"><a href="index.php?page=login&lg=<?php echo $LANGUAGE; ?>"><?php echo $LANNAV_025; ?></a></div></li>
      <?php
  }
  if ($adminPage==1) {
      $adPage = "&adminPage=1";
  }else{
      $adPage = ""; 
      if(isset($_GET['banId'])) {
        $adPage .= '&banId='.$_GET['banId']; // Get banId value
      }
  }
  ?>
  </ul>
</div>
<ul>
    <a href="index.php?page=<?php echo $page.$adPage ?>&lg=English"><img src="images/flags/gb_large.png" width="32" height="19" alt="<?php echo $LANNAV_021; ?>" /></a>
    <a href="index.php?page=<?php echo $page.$adPage ?>&lg=Spanish"><img src="images/flags/es_large.png" width="32" height="19" alt="<?php echo $LANNAV_022; ?>" /></a>
    <a href="index.php?page=<?php echo $page.$adPage ?>&lg=French"><img src="images/flags/fr_large.png" width="32" height="19" alt="<?php echo $LANNAV_023; ?>" /></a>
    <a href="index.php?page=<?php echo $page.$adPage ?>&lg=Russian"><img src="images/flags/ru_large.png" width="32" height="19" alt="<?php echo $LANNAV_024; ?>" /></a>
</ul>