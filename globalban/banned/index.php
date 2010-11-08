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
    along with GlobalBan.  If not, see <http://www.gnu.org/licenses/>.ñ
*/
error_reporting(E_ALL&~E_STRICT);

define("ROOTDIR", dirname(__FILE__)); // Global Constant of root directory

// ############################# INCLUDES #####################################
require_once(ROOTDIR."/config/class.Config.php");
require_once(ROOTDIR."/include/database/class.Database.php");
require_once(ROOTDIR."/include/php4functions.php");
// ############################################################################

// ############################### INIT #######################################
$db = new Database(); // Just about every page makes a database call
$config = new Config(); // All configuration variables are contained in this object
$version = "v3.4.1 r146";
$copyright = "Developed by <a href='mailto:lsjonasson@gmail.com'>Soynuts</a>, <a href='mailto:apadrones@gmail.com'>Odonel</a>, <a href='mailto:fantole@gmail.com'>Fantole</a> and <a href='mailto:mader6000@gmail.com'>Mader6000</a> &copy;2007-2010";
$translators = "Translators: French by <a href='mailto:alpha.cssupport@gmail.com'>Owned</a>, Russian by <a href='http://universal-games.ru/'>Co0l</a>, Spanish by <a href='mailto:apadrones@gmail.com'>Odonel</a>, German by <a href='http://www.clan-holyshit.de/'>Neo@Bln + RST_Freak</a> and Latvian by <a href='mailto:admin@dga.lv'>Dagunchi and Foxxz</a>";
// ############################################################################

// Page to access
if(isset($_GET['page'])) {
	$page = basename(str_replace(chr(0), "", $_GET['page'])); // Get page name from
} else {
	$page = "banlist";
}

// Language
if(isset($_GET['lg'])) {
	$LANGUAGE = basename(str_replace(chr(0), "", $_GET['lg'])); // Get page name from
} else {
	$LANGUAGE = $config->LANGUAGE; // Default Languaje
}

$ajax = 0;
if(isset($_GET['ajax'])) {
	$ajax = basename(str_replace(chr(0), "", $_GET['ajax'])); // Get ajax value
}

$esScript = 0;
if(isset($_GET['es'])) {
	$esScript = basename(str_replace(chr(0), "", $_GET['es'])); // Get es value
}

$adminPage = 0;
if(isset($_GET['adminPage'])) {
    $adminPage = basename(str_replace(chr(0), "", $_GET['adminPage']));
}

// We need access to user permissions on everything
if($config->enableSmfIntegration) {
  // Verify the User by using the SMF tables
  require_once(ROOTDIR."/../SSI.php");
  require_once(ROOTDIR."/config/SMFConfig.php"); // SMF permissions
} else {
  // Verify User by using the gban_admins table
  require_once(ROOTDIR."/include/verifyUser.php");
  require_once(ROOTDIR."/config/permissions.php"); // Regular permissions
}

// For non-AJAX pages
if($ajax == 1) {
  // We do not need or want the header or footer for ajax pages as they are
  // used to generate XML
  require_once(ROOTDIR."/ajax/".$page.".php");
} else if($esScript == 1) {
  // We are executing a page from an eventscript
  // The header and footer is not needed
  require_once(ROOTDIR."/es/".$page.".php");
} else {
  
  // ############################# HEADER #####################################
  require_once(ROOTDIR."/include/header.php");
  // ##########################################################################
  
  if($adminPage == 1) {
    // We are loading admin pages
    require_once(ROOTDIR."/admin/".$page.".php");
  } else {
    require_once(ROOTDIR."/".$page.".php");
  }
  
  // ############################# FOOTER #####################################
  require_once(ROOTDIR."/include/footer.php");
  // ##########################################################################
}
?>
