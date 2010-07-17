<?php
/*
+ ----------------------------------------------------------------------------+
|     esGlobalBan - Language File.
|
|     $Source: /cvsroot/banned/languages/Russian/lan_install.php,v $
|     $Revision: 1.0 $
|     $Date: 2009/07/02 30:36:39 $
|     $Author: Odonel $
+----------------------------------------------------------------------------+
*/

$LAN_INSTALL_001 = "Полная установка GlobalBan\'a";
$LAN_INSTALL_002 = "Full Installation Configuration";
$LAN_INSTALL_003 = "Database Information";
$LAN_INSTALL_004 = "Database Host:";
$LAN_INSTALL_005 = "(Обычно localhost, за исключением случаев с использованием удаленной базы дынных)";
$LAN_INSTALL_006 = "Database Username:";
$LAN_INSTALL_007 = "Database Password:";
$LAN_INSTALL_008 = "Continue";
$LAN_INSTALL_009 = "Please enter a valid email address";
$LAN_INSTALL_010 = " is already in the list";
$LAN_INSTALL_011 = "You must specify a directory for demos.";
$LAN_INSTALL_012 = "You must specify a demo size limit.";
$LAN_INSTALL_013 = "You must specify ban message";
$LAN_INSTALL_014 = "You must specify the number of days to keep a pending ban banned for";
/*
;$LAN_INSTALL_014 = "You must specify the number of days to keep a pending ban banned for";
*/
$LAN_INSTALL_015 = "You must specify a hash code for security reasons.";
$LAN_INSTALL_016 = "You must specify a SMF table prefix.";
$LAN_INSTALL_017 = "You must specify a SMF group that will have full power privileges.";
$LAN_INSTALL_018 = "You must specify a SMF group that will have ban manager privileges.";
$LAN_INSTALL_019 = "You must specify a SMF group that will have admin privileges.";
$LAN_INSTALL_020 = "You must specify a SMF group that will have member privileges.";
/*$LAN_INSTALL_021 = "Vous devez oirs immédiat.";*/
$LAN_INSTALL_022 = "You must specify user creation code.";
$LAN_INSTALL_023 = "Your super-user password must be at least 6 characters in length and contain at least 1 number.";
$LAN_INSTALL_024 = "Your super-user passwords do not match !";
$LAN_INSTALL_025 = "The steam ID entered is not valid.";
$LAN_INSTALL_026 = "You must provide a valid email address for the super-user account.";
$LAN_INSTALL_027 = "<h4 style='color:red; margin-bottom:0px'>Unable to prepare the config/class.Config.php file for writing. Please check your web server's permissions before continuing</h4>";
$LAN_INSTALL_028 = "Database Selection/Creation";
$LAN_INSTALL_029 = "New";
$LAN_INSTALL_030 = "Nouvelle base de donnée";
$LAN_INSTALL_031 = "Cela va créer une nouvelle base de donnée pour GlobalBan. Le nom d\'utilisateur que vous avez entré précédemment doit avoir des privilèges !.";
$LAN_INSTALL_032 = "Existing";
$LAN_INSTALL_033 = "Base de donnée existante";
$LAN_INSTALL_034 = "Cette fonction utilisera une base de donnée existante que vous choisirez ci-dessous. Pour une intégration SMF, vous devrez sélectionner votre base de donnée SMF.";
$LAN_INSTALL_035 = "Database Name:";
$LAN_INSTALL_036 = "Website Settings";
$LAN_INSTALL_037 = "Site Name";
$LAN_INSTALL_038 = "This is the name of your community that displays in the title bar of the browser";
$LAN_INSTALL_039 = "Logo";
$LAN_INSTALL_040 = "This must be the exact file name of your logo image found in the images directory. (dislpays at the top of the page)";
$LAN_INSTALL_041 = "Super-User Setup (ignored if SMF integration enabled)";
$LAN_INSTALL_042 = "Username";
$LAN_INSTALL_043 = "This is the username you will use to log in as a super-user admin for the site.";
$LAN_INSTALL_044 = "Steam ID ";
$LAN_INSTALL_045 = "This is your steam ID.  If it is not valid, you will not be able to execute in-game bans.";
$LAN_INSTALL_046 = "(must be in <font color='#FF0000'><b>STEAM_X:X:XXXXXX</b></font> format)";
$LAN_INSTALL_047 = "Password ";
$LAN_INSTALL_048 = "The password associated with your username that you will use to log in.";
$LAN_INSTALL_049 = "Verify Password ";
$LAN_INSTALL_050 = "The password verification is to make sure you typed in your password correctly.";
$LAN_INSTALL_051 = "EMail ";
$LAN_INSTALL_052 = "This email address will be used to email you your password if you ever forget it.";
$LAN_INSTALL_053 = "Install GlobalBan";
$LAN_INSTALL_054 = "Install Complete - Redirecting...";
$LAN_INSTALL_055 = "Default Language";
$LAN_INSTALL_056 = "The language they were shown by default to all visitors that do not specify one. Will also be used in the messages that are sent to game server.";
$LAN_INSTALL_057 = "You are banned gb_time because gb_reason. Appeal at yourdomain.com";
$LAN_INSTALL_058 = "Generate";
$LAN_INSTALL_060 = "Enable e107 Auto New Post";
$LAN_INSTALL_061 = "If you have an e107 forum and want GlobalBan automatically create a new post in your forum e107 every time someone adds a new ban, select this option and set the rest of this section.";
$LAN_INSTALL_062 = "e107 Web Address";
$LAN_INSTALL_063 = "Enter in the URL of your e107 web. Ex: \'http://www.your_e107_domain.com/\'";
$LAN_INSTALL_064 = "e107 Database Host";
$LAN_INSTALL_065 = "Set the e107 Database\'s host, normaly it\'s localhost.";
$LAN_INSTALL_066 = "e107 Table Prefix";;
$LAN_INSTALL_067 = "Database Username";
$LAN_INSTALL_068 = "MySQL user with access to the database used by e107.";
$LAN_INSTALL_069 = "e107 Username";
$LAN_INSTALL_070 = "e107 Registered User that we wish listed as author of the post GlobalBan generated. We introduce it using the format \'ID.UserName\' for example \'5.Globalban\'.";
$LAN_INSTALL_071 = "Database Password";
$LAN_INSTALL_072 = "MySQL Password to access e107 database.";
$LAN_INSTALL_073 = "ID Category Forum";
$LAN_INSTALL_074 = "For example if your Banned e107 Forum category link is \'http://www.youre107.com/e107_plugins/forum/forum_viewforum.php?19\' you must set it to \'19\'";
$LAN_INSTALL_075 = "Database Name";
$LAN_INSTALL_076 = "MySQL Database Name used by your e107 website.";
$LAN_INSTALL_077 = "Enable Web Link";
$LAN_INSTALL_078 = "This will add a menu item that will go to your community website.";
$LAN_INSTALL_079 = "Web Address";
$LAN_INSTALL_080 = "Enter in the URL of your web if you have enabled the web link. Ex: http://www.yourdomain.com";
$LAN_INSTALL_081 = "Enable HLstatsX Link";
$LAN_INSTALL_082 = "This will add a link per each Steam_ID at Ban List, that will search it in your HLstatsX Community Edition (http://www.hlxcommunity.com/).";
$LAN_INSTALL_083 = "HlstatsX Address";
$LAN_INSTALL_084 = "Enter in the URL of your HlstatsX web if you have enabled the HlstatsX link. Ex: http://www.yourdomain.com/HlstatsX/";
$LAN_INSTALL_085 = "e107 Integration Settings";
$LAN_INSTALL_086 = "The prefix of your e107 tables (normally \'e107_\' by default).";
$LAN_INSTALL_105 = "Yes";
$LAN_INSTALL_106 = "No";
?>