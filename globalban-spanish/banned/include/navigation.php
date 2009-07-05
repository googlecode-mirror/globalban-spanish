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
  // Web
  if($config->enableForumLink) {
  ?>
  <li><a href="<?=$configOdonel->e107Url?>"><span>Web LDS</span></a></li>
  <?php
  }
  ?>

  <?php
  // Forums Button
  if($config->enableForumLink) {
  ?>
  <li><a href="<?=$config->forumURL?>"><span>Foro</span></a></li>
  <?php
  }
  ?>
  
  <?php
  // Ban List button
  ?>
  <li><a href="index.php?page=banlist">Baneados</a></li>

  <?php
  // Ip List button
  ?>
  <li><a href="index.php?page=ipbanlist">IPs Baneadas</a></li>

  <?php
  // Add Ban Button
  if($member || $admin || $banManager || $fullPower) {
  ?>
  <li><a href="index.php?page=addBan">A&ntilde;adir Ban</a></li>
  <?php
  }
  ?>
  
  <?php
  // Add Ban Button
  if($fullPower) {
  ?>
  <li><a href="index.php?page=importBans">Importar</a></li>
  <?php
  }
  ?>
  
  <?php
  // Demos Button
  ?>
  <li><a href="index.php?page=demos">Demos</a></li>
  
  <?php // Server List Button ?>
  <li><a href="index.php?page=serverList">Servidores</a></li>

  
  <?php
  // Profile Button
  if($member || $admin || $banManager || $fullPower) {
  ?>
  <li><a href="index.php?page=profile">Perfil</a></li>
  <?php
  }
  ?>
  
  <?php
  // Menu for full power admins only
  if($fullPower) {
  ?>
  
    <li><a href="#">Admin</a>
      <ul>
        <li><a href="index.php?page=configuration&adminPage=1">Configuraci&oacute;n</a></li>
        <li><a href="index.php?page=banReasons&adminPage=1">Motivos</a></li>
        <li><a href="index.php?page=banLengths&adminPage=1">Duraciones</a></li>
        <li><a href="index.php?page=badNames&adminPage=1">Bad Names</a></li>
        <li><a href="index.php?page=manageServers&adminPage=1">Servidores</a></li>
        <li><a href="index.php?page=manageServerGroups&adminPage=1">Grupos Server</a></li>
        <li><a href="index.php?page=manageAdminGroups&adminPage=1">Grupos Admins</a></li>
        <li><a href="index.php?page=manageUsers&adminPage=1">Administradores</a></li>
      </ul>
    </li>
  
  <?php
  }
  ?>

  <?php
  // Logout
  if(isset($_SESSION['accessLevel']) && $page != "logout" && !$config->enableSmfIntegration) {
  ?>
  <li><a href="index.php?page=logout">Salir</a></li>
  <?php
  }
  ?>
  </ul>
</div>
<a href="index.php?page=banlist&lg=English"><img src="images/flags/gb.gif" width="24" height="16" alt="English" /></a>
<a href="index.php?page=banlist&lg=Spanish"><img src="images/flags/es.gif" width="24" height="16" alt="Spanish" /></a>
