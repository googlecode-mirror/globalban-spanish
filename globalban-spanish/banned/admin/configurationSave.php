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

// This will save the class.Config.php file AND save the GlobalBan.cfg file on all servers
define("ROOTDIR", dirname(__FILE__)); // Global Constant of root directory
require_once(ROOTDIR."/config/class.Config.php");


$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_configurationSave.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_configurationSave.php");

// Get the config object, we want to read the database connection information
$config = new Config();

$logo = $_POST['logo'];

if($logo == "") {
  $logo = "logo.png";
}

$emails = explode("\n", $_POST['emails']);
$emailList = "";

// Generate the list of Ban Manager Emails
for($i=0; $i<count($emails); $i++) {
  if(!empty($emails[$i])) {
    $emailList .= "\"".str_replace(array("\r\n", "\n", "\r"), "", $emails[$i])."\"";
    if($i < count($emails)-1) {
      $emailList .= ", ";
    }
  }
}

// Create config file
$fh = fopen("config/class.Config.php", 'w');

// Set default values for all post values
$bansPerPage = $_POST['bansPerPage'];
if(empty($bansPerPage)) {
  $bansPerPage = 100;
}

$smfNoPowerGroup = $_POST['smfNoPowerGroup'];
if(empty($smfNoPowerGroup)) {
  $smfNoPowerGroup = 0;
}

$enableAutoPoste107Forum = $_POST['enableAutoPoste107Forum'];
if(empty($enableAutoPoste107Forum)) {
  $enableAutoPoste107Forum = false;
}

$adviseInGame = $_POST['adviseInGame'];
if(empty($adviseInGame)) {
  $adviseInGame = 1;
}

$enableWebLink = $_POST['enableWebLink'];
if(empty($enableWebLink)) {
  $enableWebLink = false;
}

$enableHLstatsLink = $_POST['enableHLstatsLink'];
if(empty($enableHLstatsLink)) {
  $enableHLstatsLink = false;
}

$LANGUAGE = $_POST['LANGUAGE'];
if(empty($LANGUAGE)) {
  $LANGUAGE = "English";
}


// Generate the php config file
$configData = "<?php
/**
 *  This makes it easier to include configuration variables to other classes
 *  by simply extending the class with the Config class.  Any new variables that
 *  get added MUST have a getter as that will be the only way to retrieve a Config
 *  value if the config value needs to be used outside as it's own object.
 *
 *  Change ALL values below to what you desire for your website.  If you did not
 *  change the gban.sql file, then the database name will be global_ban.  Otherwise
 *  all other variables, espeically those in the database block should be changed
 *  appropriately.
 */

class Config {
  /**
   * Site specific settings
   */
  var $"."LANGUAGE = \"".$LANGUAGE."\"; // Default Language (English, Spanish, French, ...)   
  var $"."bansPerPage = ".$bansPerPage."; // Number of bans to display on ban list page for each page (-1 show all)
  var $"."maxPageLinks = ".$_POST['numPageLinks']."; // Number of links to show before and after selected page (IE: set at 2 you would see 1 2 ... 10 11 [12] 13 14 ... 23 24)
  var $"."demoRootDir = \"".$_POST['demoDir']."\"; // Folder to save demos to (folder must be relative to banned dir)
  var $"."demoSizeLimit = \"".$_POST['demoSizeLimit']."\"; // Demo size limit in MB
  var $"."siteName = \"".str_replace("$", "\\$", $_POST['siteName'])."\"; // The name of your website
  var $"."siteUrl = \"".$url."\"; // Your clan/server's home page
  var $"."siteLogo = \"".$logo."\"; // Found in images directory; you must save your logo to the images dir!!

  /**
   * SMF integration settings
   * The gban tables MUST be installed in your SMF database ($"."dbName = \"YOUR_SMF_DB\")
   * Full power admins are those with FULL ADMIN rights to the SMF boards
   * If you wish to use SMF integration you MUST install the zip under your Forums directory
   * So you will access the pages by going to Forums/banned
   */
  var $"."enableSmfIntegration = ".$_POST['smfIntegration'].";  // Whether to enable SMF integartion
  var $"."smfTablePrefix = \"".$_POST['smfTablePrefix']."\"; // The prefix of the SMF tables
  var $"."memberGroup = ".$_POST['smfMemberGroup']."; // The SMF group id that contains all your members
  var $"."adminGroup = ".$_POST['smfAdminGroup']."; // The SMF group id that contains all your admins
  var $"."banManagerGroup = ".$_POST['smfBanManagerGroup']."; // The SMF group id that contains all your ban managers
  var $"."fullPowerGroup = ".$_POST['smfFullPowerGroup']."; // The SMF group id that is allowed full access to the GlobalBan site and admin tools
  var $"."noPowerGroup = ".$smfNoPowerGroup."; // The SMF group id that has no power unless given by an admin group

  /**
   * e107 integration settings
   */
  var $"."enableAutoPoste107Forum = ".$enableAutoPoste107Forum.";  // Whether to enable e107 integration, just generate Auto-Post in the e107 Forum with each new ban.
  var $"."e107TablePrefix = \"".$_POST['e107TablePrefix']."\"; // The prefix of the e107 tables
  var $"."e107Url = \"".$_POST['e107Url']."\"; // Your e107 web site Ej: \"http://www.e107.com/\"
  var $"."e107_dbName = \"".$_POST['e107_dbName']."\"; //  Set the e107 Database Name to access
  var $"."e107_dbUserName = \"".$_POST['e107_dbUserName']."\";  // Set the Database's user name login (recommend a user with only select and insert privs)
  var $"."e107_dbPassword = \"".$_POST['e107_dbPassword']."\";  // Set the Database user's password login
  var $"."e107_dbHostName = \"".$_POST['e107_dbHostName']."\";  // Set the Database's host
  var $"."e107_bans_forum_category_number = \"".$_POST['e107_bans_forum_category_number']."\";  //  For example if your Banned forum category link is http://www.youre107.com/e107_plugins/forum/forum_viewforum.php?19 you must set it to \"19\"
  var $"."e107_GlobalBan_user = \"".$_POST['e107_GlobalBan_user']."\";  // e107 user to use like post owner, format must be \"user_number_ID.user_name\", Ex: \"5.GlobalBan\"

  /**
   * Ban specific settings
   */
  var $"."banMessage = \"".str_replace("$", "\\$", $_POST['banMessage'])."\"; // Message to display to those banned
  var $"."daysBanPending = ".$_POST['daysBanPending']."; // Number of days to keep someone with a \"pending\" ban off the server (0 to let the person come back after being \"banned\"); this only affects \"members\" who do bans longer than 1 day
  var $"."allowAdminBans = ".$_POST['allowAdminBan']."; // Set to true to allow the banning of admins (Default off - false)
  var $"."teachAdmins = ".$_POST['teachAdmins']."; // Teach admins the !banmenu command
  var $"."removePendingOnUpload = ".$_POST['removePendingOnUpload']."; // Remove the pending status from a ban when a member uploads a demo for that ban
  var $"."adviseInGame = ".$adviseInGame."; // Allows you to select which players will be advised during the game when you connect a player who was banned temporarily by a ban already expired: 1 - All (Panel), 2 - Only Admins Chat & Ex-Banned Player; 3 - Only Admins Chat; 4 - Only Ex-Banned (Panel); 5 - NoBody
  //var $"."numDemosToBan = -1; // The person uploading a demo needs to have X number of people banned from his demos before future uploads will auto-ban. (-1 is off)

  /**
   * Forum Settings
   * Very simple forum integration (Just adds a link button)
   */
  var $"."enableForumLink = ".$_POST['enableForumLink'].";
  var $"."forumURL = \"".$_POST['forumURL']."\"; // Link to your forums
  
  /**
   * Web Settings
   * Very simple web integration (Just adds a link button)
   */
  var $"."enableWebLink = ".$enableWebLink.";
  var $"."webUrl = \"".$_POST['webUrl']."\"; // Link to your forums
  
  /**
   * HLstatsX Settings
   * Very simple web integration (Just adds a link button)
   */
  var $"."enableHLstatsLink = ".$enableHLstatsLink.";
  var $"."HLstatsUrl = \"".$_POST['HLstatsUrl']."\"; // Link to your forums

  /**
   * Database Block
   */
  var $"."dbName = \"".$config->dbName."\"; // Set the Database to access (where all gban tables are located, change if you place your gban tables in a different db)
  var $"."dbUserName = \"".$config->dbUserName."\"; // Set the Database's user name login (recommend a user with only select, insert, update, and delete privs)
  var $"."dbPassword = \"".str_replace("$", "\\$", $config->dbPassword)."\"; // Set the Database user's password login
  var $"."dbHostName = \"".$config->dbHostName."\"; // Set the Database's host
  var $"."matchHash = \"".str_replace("$", "\\$", $_POST['hash'])."\"; // This must match the has found in the ES script (prevent's people from accessing the page outside)
  var $"."createUserCode = \"".str_replace("$", "\\$", $_POST['createUserCode'])."\"; // This code must be entered for someone to create a new basic user

  /**
   *  Email address of those who should get notices of when a new ban has been added
   *  or changed.
   */
  var $"."sendEmails = ".$_POST['sendEmailsOnBan']."; // Send an email whenever a ban is added or updated (does not include imports)
  var $"."sendDemoEmails = ".$_POST['sendEmailsDemo']."; // Send an email whenever a new demo is added
  var $"."emailFromHeader = \"".$_POST['senderEmail']."\"; // The from email address
  var $"."banManagerEmails = array(".$emailList."); // Who recieves emails when new bans are added

  function __construct() {
  }

  function Config() {
  }
}
?".">
";

fwrite($fh, $configData);
fclose($fh);
?>

<?php
require_once(ROOTDIR."/include/class.rcon.php");
require_once(ROOTDIR."/include/database/class.ServerQueries.php");

$serverQueries = new ServerQueries();

$servers = $serverQueries->getServers();

// Cycle through each server
foreach($servers as $server) {
  ?>
  <h3 id="server:<?php echo $server->getId()?>"><?php echo $LANCONFIGURATIONSAVE_001; ?> <?php echo $server->getName()?> <img src="images/wait.gif"/></h3>
  <?php
}
?>
<h5><?php echo $LANCONFIGURATIONSAVE_002; ?></h5>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script language="Javascript" type="text/javascript">
<?php
foreach($servers as $server) {
?>
  saveServerConfig(<?php echo $server->getId()?>);
<?php
}
?>
</script>
