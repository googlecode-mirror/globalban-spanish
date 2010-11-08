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
 * Instead of putting each step on a separate page, each step will post back to this page and be
 * processed in the next step if it passes the previous step.
 */
 
define("ROOTDIR", dirname(__FILE__)); // Global Constant of root directory

error_reporting(E_ALL&~E_STRICT);

session_start();

require_once(ROOTDIR."/include/database/class.InstallAndUpgradeQueries.php");
require_once(ROOTDIR."/include/php4functions.php");

$lan_file = ROOTDIR.'/languages/English/lan_configuration.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_configuration.php");

$installAndUpgrade = new InstallAndUpgradeQueries();

function selfURL() {
  $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
  $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
  $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
  return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}

function strleft($s1, $s2) {
  return substr($s1, 0, strpos($s1, $s2));
}

$url = selfURL();
$url = substr($url, 0, strrpos($url, "/")) . "/";

// The step of the install process
$step = 1;
if(isset($_POST['step'])) {
  $step = $_POST['step'];
}

/*************************************************
 * Validate step data
 *************************************************/
// Step 1 has been processed
if($step == 2) {
  // Determine if we can really continue to step 2
  // Check to see if the database user and password are correct
  $installAndUpgrade->setDbHost($_POST['dbhost']);
  $installAndUpgrade->setDbUser($_POST['dbuser']);
  $installAndUpgrade->setDbPass($_POST['dbpass']);
  
  // Store the values into the session so that they can be grabbed on the next page
  $_SESSION['dbhost'] = $_POST['dbhost'];
  $_SESSION['dbuser'] = $_POST['dbuser'];
  $_SESSION['dbpass'] = $_POST['dbpass'];
  
  // If the test fails, they will told and will have to click the back button to correct it
  $installAndUpgrade->testConnection();
}
// Step 2 has been processed
if($step == 3) {
  $newOrExisting = $_POST['newOrExisting']; // 'New' or 'Existing'
  
  $installAndUpgrade->setDbHost($_SESSION['dbhost']);
  $installAndUpgrade->setDbUser($_SESSION['dbuser']);
  $installAndUpgrade->setDbPass($_SESSION['dbpass']);

  $installAndUpgrade->createConnection();
  
  $dbToUse = "";

  if($newOrExisting == "New") {
    // Create the database
    $dbToUse = $_POST['dbnameinput'];
    $installAndUpgrade->setDbase($dbToUse);
    $installAndUpgrade->createDatabase();
  } else {
    $dbToUse = $_POST['dbnameselect'];
    $installAndUpgrade->setDbase($dbToUse);
  }
  
  // Now connect to the database (new or existing)
  $installAndUpgrade->connectToDatabase();
  
  // Now create all the tables
  $installAndUpgrade->fullInstall();
  
  // Create config file
  $fh = fopen("config/class.Config.php", 'w');

  if(!$fh) {
    $fileError = 1;
  } else {
	$fileError = 0;

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

    // Generate the php config file
    // We have it aligned to the right so that extra white space does not get into the final file
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
  var $"."LANGUAGE = \"".$_POST['LANGUAGE']."\"; // Default Language (English, Spanish, French, ...)   
  var $"."bansPerPage = ".$_POST['bansPerPage']."; // Number of bans to display on ban list page for each page (-1 show all)
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
  var $"."noPowerGroup = ".$_POST['smfNoPowerGroup']."; // The SMF group id that has no power unless given by an admin group

  /**
   * e107 integration settings
   */
  var $"."enableAutoPoste107Forum = ".$_POST['enableAutoPoste107Forum'].";  // Whether to enable e107 integration, just generate Auto-Post in the e107 Forum with each new ban.
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
  var $"."adviseInGame = ".$_POST['adviseInGame']."; // Allows you to select which players will be advised during the game when you connect a player who was banned temporarily by a ban already expired: 1 - All (Panel), 2 - Only Admins Chat & Ex-Banned Player; 3 - Only Admins Chat; 4 - Only Ex-Banned (Panel); 5 - NoBody
  var $"."adviseInGameLenght = ".$_POST['adviseInGameLenght']."; // Allows you to select required Min Ban Lenght in seconds to advise.

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
  var $"."enableWebLink = ".$_POST['enableWebLink'].";
  var $"."webUrl = \"".$_POST['webUrl']."\"; // Link to your forums
  
  /**
   * HLstatsX Settings
   * Very simple web integration (Just adds a link button)
   */
  var $"."enableHLstatsLink = ".$_POST['enableHLstatsLink'].";
  var $"."HLstatsUrl = \"".$_POST['HLstatsUrl']."\"; // Link to your forums

  /**
   * Database Block
   */
  var $"."dbName = \"".$dbToUse."\"; // Set the Database to access (where all gban tables are located, change if you place your gban tables in a different db)
  var $"."dbUserName = \"".$_SESSION['dbuser']."\"; // Set the Database's user name login (recommend a user with only select, insert, update, and delete privs)
  var $"."dbPassword = \"".str_replace("$", "\\$", $_SESSION['dbpass'])."\"; // Set the Database user's password login
  var $"."dbHostName = \"".$_SESSION['dbhost']."\"; // Set the Database's host
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
    
    // We can only save the super user if the config file exists
    // Add the super user
    if($_POST['smfIntegration'] == "false") {
      include_once(ROOTDIR."/include/database/class.UserQueries.php");
      // User specific queries
      $userQueries = new UserQueries();
      if($userQueries->addUser($_POST['username'], $_POST['password'], 1, $_POST['steamId'], $_POST['userEmail'])) {
      }
    }
  }
}
ob_start();
?>

<html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>GlobalBan Full Installation</title>
  <link rel="icon" href="images/favico.ico" type="image/vnd.microsoft.icon">
  <link rel="stylesheet" type="text/css" href="css/banned.css" />
  <script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
  </head>
  <!-- -----------------------------------------------------------------------
      Special Thanks to: ub|Delta One - http://www.urbanbushido.net/
                         tnbporsche911 - http://www.tnbsourceclan.net/

      Default GlobalBan logo designed with template from http://www.freepsd.com/logo
   ----------------------------------------------------------------------- -->
<body id="body">
<script type="text/javascript" src="javascript/wz_tooltip.js"></script>
<div id="container">

<div id="logo"><img src="images/logo.png"/></div>

<?php
/*************************************************
 * STEP 1 - THE FIRST PAGE
 * GET THE DATABASE USER AND PASSWORD
 *************************************************/
if($step == 1) {
?>
<form action="install.php" method="post">
  <div class="tborder">
    <div id="tableHead">
      <div><b>Full Installation Configuration</b></div>
    </div>
  </div>

  <br/>

  <div class="tborder">
    <div id="tableHead">
      <div><b>Database Information</b></div>
    </div>
    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" nowrap>Database Host:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" id="dbhost" name="dbhost" value="localhost"></td>
      <td class="rowColor1">(Normally localhost unless your database is external)</td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Database Username:</td>
      <td class="rowColor2" width="1%" nowrap><input type="text" id="dbuser" name="dbuser"></td>
      <td class="rowColor2"></td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>Database Password:</td>
      <td class="rowColor1" width="1%" nowrap><input type="password" id="dbpass" name="dbpass"></td>
      <td class="rowColor1"></td>
    </tr>
    <tr>
      <td class="rowColor2" colspan="3"><input type="submit" value="Continue"></td>
    </tr>
      </table>
  </div>
  
  <input type="hidden" id="step" name="step" value="2"/>
</form>
<?php
}
/*************************************************
 * END STEP 1
 *************************************************/
?>




<?php
/*************************************************
 * STEP 2 - THE SECOND STEP
 * SELECT THE DATABASE TO USE
 *************************************************/
if($step == 2) {
?>
<script language="javascript">
// This adds a new email address to the email list textarea
function addEmail() {
  var email = document.getElementById("email").value;
  var emails = document.getElementById("emails");
  var emailList = document.getElementById("emailList");

  // Validate the email address
  var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!filter.test(email)) {
    alert("Please enter a valid email address");
    return false;
  }

  // Do not allow duplicate email addresses
  var emailArray = emails.value.split("\n");
  for(i=0; i<emailArray.length; i++) {
    if(emailArray[i] == email) {
      alert(email + " is already in the list");
      return false;
    }
  }

  // Add a new line if there is more than 1 email
  if(emails.value.length > 1) {
    emails.value += "\n";
  }

  // Add to hidden textarea
  emails.value += email;

  // Add to visible scroll list
  var emailOption = document.createElement('option');
  emailOption.text = email;
  emailOption.value = email;
  try {
    emailList.add(emailOption, null); // standards compliant; doesn't work in IE
  }
  catch(ex) {
    emailList.add(emailOption); // IE only
  }
}

function removeEmail() {
  var emailList = document.getElementById("emailList");
  var emails = document.getElementById("emails");
  emails.value = "";
  var i;
  for(i=0; i<emailList.length; i++) {
    if(emailList.options[i].selected) {
      emailList.remove(i);
    } else {
      emails.value += emailList.options[i].value;
      if(i < emailList.length-1) {
        emails.value += "\n";
      }
    }
  }
}

// Generate a random hash for the user
function generateHash() {
  var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 16;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	document.getElementById("hash").value = randomstring;
}

// Javascript will do all the field checking before sending it off for processing
function formVerify() {
  var noWarnings = true;
  var warningMessage = "";

  // Demo dir check
  if(document.getElementById("demoDir").value == "") {
    warningMessage += "You must specify a directory for demos.\n";
    document.getElementById("demoDirWarn").style.display = "";
    noWarnings = false;
  } else {
    document.getElementById("demoDirWarn").style.display = "none";
  }

  // Demo Size check
  if(document.getElementById("demoSizeLimit").value == "") {
    warningMessage += "You must specify a demo size limit.\n";
    document.getElementById("demoSizeLimitWarn").style.display = "";
    noWarnings = false;
  } else {
    document.getElementById("demoSizeLimitWarn").style.display = "none";
  }

  // Ban Message
  if(document.getElementById("banMessage").value == "") {
    warningMessage += "You must specify ban message.\n";
    document.getElementById("banMessageWarn").style.display = "";
    noWarnings = false;
  } else {
    document.getElementById("banMessageWarn").style.display = "none";
  }

  // Days to keep pending banned
  if(document.getElementById("daysBanPending").value == "") {
    warningMessage += "You must specify the number of days to keep a pending ban banned for.\n";
    document.getElementById("daysBanPendingWarn").style.display = "";
    noWarnings = false;
  } else {
    document.getElementById("daysBanPendingWarn").style.display = "none";
  }

  // Hash
  if(document.getElementById("hash").value == "") {
    warningMessage += "You must specify a hash code for security reasons.\n";
    document.getElementById("hashWarn").style.display = "";
    noWarnings = false;
  } else {
    document.getElementById("hashWarn").style.display = "none";
  }

  // SMF
  if(document.getElementById("smfIntegration").value == "true") {
    if(document.getElementById("smfTablePrefix").value == "") {
      warningMessage += "You must specify a SMF table prefix.\n";
      document.getElementById("smfTablePrefixWarn").style.display = "none";
      noWarnings = false;
    }
    // Full Power Users
    if(document.getElementById("smfFullPowerGroup").value == "" || document.getElementById("smfFullPowerGroup").value == 0) {
      warningMessage += "You must specify a SMF group that will have full power privileges.\n";
      document.getElementById("smfFullPowerGroupWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("smfFullPowerGroupWarn").style.display = "none";
    }
    // Ban Managers
    if(document.getElementById("smfBanManagerGroup").value == "" || document.getElementById("smfBanManagerGroup").value == 0) {
      warningMessage += "You must specify a SMF group that will have ban manager privileges.\n";
      document.getElementById("smfBanManagerGroupWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("smfBanManagerGroupWarn").style.display = "none";
    }
    // Admins
    if(document.getElementById("smfAdminGroup").value == "" || document.getElementById("smfAdminGroup").value == 0) {
      warningMessage += "You must specify a SMF group that will have admin privileges.\n";
      document.getElementById("smfAdminGroupWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("smfAdminGroupWarn").style.display = "none";
    }
    // Members
    if(document.getElementById("smfMemberGroup").value == "" || document.getElementById("smfMemberGroup").value == 0) {
      warningMessage += "You must specify a SMF group that will have member privileges.\n";
      document.getElementById("smfMemberGroupWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("smfMemberGroupWarn").style.display = "none";
    }
    document.getElementById("createUserCodeWarn").style.display = "none";
  } else {
    // SMF Integration off... we need to make sure user variables are set
    // Create User Code Check
    if(document.getElementById("createUserCode").value == "") {
      warningMessage += "You must specify user creation code.\n";
      document.getElementById("createUserCodeWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("createUserCodeWarn").style.display = "none";
    }

    document.getElementById("smfFullPowerGroupWarn").style.display = "none";
    document.getElementById("smfBanManagerGroupWarn").style.display = "none";
    document.getElementById("smfAdminGroupWarn").style.display = "none";
    document.getElementById("smfMemberGroupWarn").style.display = "none";
    
    // Validate Super-User Block Data
    // validate the password
    var regex = /^\w*(?=\w*\d)(?=\w*[a-zA-Z])\w*$/;
    var password = document.getElementById("password").value;
    if(!password.match(regex)) {
      warningMessage += "Your super-user password must be at least 6 characters in length and contain at least 1 number.\n";
      document.getElementById("passwordWarn").style.display = "";
      noWarnings = false;
    }
    if(document.getElementById("password").value != document.getElementById("vpassword").value) {
      warningMessage += "Your super-user passwords do not match.\n";
      document.getElementById("vpasswordWarn").style.display = "";
      noWarnings = false;
    }
    // validate the steam id
    var regex = /^STEAM_[01]:[01]:\d{0,10}$/;
    var steamId = document.getElementById("steamId").value;
    if(!steamId.match(regex)) {
      warningMessage += "The steam ID entered is not valid.\n";
      document.getElementById("steamIdWarn").style.display = "";
      noWarnings = false;
    }
    //userEmail
    var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var userEmail = document.getElementById("userEmail").value;
    if(!userEmail.match(regex)) {
      warningMessage += "You must provide a valid email address for the super-user account.\n";
      document.getElementById("userEmailWarn").style.display = "";
      noWarnings = false;
    }
  }

  if(!noWarnings) {
    alert(warningMessage);
  }
  
  return noWarnings;
}

// Hide or Show the superuser block
function hideShowSuperUserBlock() {
  if(document.getElementById("smfIntegration").value ==  "true") {
    document.getElementById("superUserBlock").style.display = "none";
  } else {
    document.getElementById("superUserBlock").style.display = "block";
  }
}
</script>

<?php
  $fh = @fopen("config/class.Config.php", 'w');
  
  if(!$fh) {
    $fileError = "<h4 style='color:red; margin-bottom:0px'><i>Unable to prepare the config/class.Config.php file for writing. Please check your web server's permissions before continuing.</i></h4>";
  } else {
    fclose($fh);
  }
?>
<form action="install.php" method="post" id="step0" onsubmit="return formVerify();">
  
  <div class="tborder">
    <div id="tableHead">
      <div><b>Full Installation Configuration</b><br/><?php if(isset($fileError)) {echo $fileError; }?></div>
    </div>
  </div>
  
  <br/>
  
  <div class="tborder">
    <div id="tableHead">
      <div><b>Database Selection/Creation</b></div>
    </div>
    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
        <tr>
          <td colspan="2" class="rowColor1">
            <input type="radio" name="newOrExisting" value="New" checked onclick="document.getElementById('dbInputName').style.display='';document.getElementById('dbSelectName').style.display='none';">New Database<img src="images/help.png" style="cursor:help" onmouseover="Tip('This will create a new database for GlobalBan that you enter below. (The username you entered before MUST have database create privileges!).', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>
            <input type="radio" name="newOrExisting" value="Existing" onclick="document.getElementById('dbSelectName').style.display='';document.getElementById('dbInputName').style.display='none';">Existing Database<img src="images/help.png" style="cursor:help" onmouseover="Tip('This will use an existing database that you select below.  For SMF integration, you will want to select your SMF database.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>
          </td>
        </tr>
        <tr>
          <td class="rowColor2" width="1%" nowrap>Database Name:</td>
          <td id="dbInputName" class="rowColor2"><input type="text" id="dbnameinput" name="dbnameinput"" value="global_ban"></td>
          <td id="dbSelectName" style="display:none;" class="rowColor2">
            <select id="dbnameselect" name="dbnameselect">
              <?php
                $dbs = $installAndUpgrade->getListOfDatabases();
                for($i=0; $i<count($dbs); $i++) {
                  echo "<option>".$dbs[$i]."</option>";
                }
              ?>
            </select>
          </td>
        </tr>
      </table>
  </div>
  
  <br/>
    
  <div class="tborder">
    <div id="tableHead">
      <div><b>Website Settings</b></div>
    </div>

    <table id="settingsTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" nowrap>Site Name <img src="images/help.png" style="cursor:help" onmouseover="Tip('This is the name of your community that displays in the title bar of the browser.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="siteName" value="GlobalBan" size="40" maxlength="255" onkeyup="removeSpecialCharacters(this)"/></td>
      <td class="rowColor1" width="1%" nowrap>Logo <img src="images/help.png" style="cursor:help" onmouseover="Tip('This must be the exact file name of your logo image found in the images directory. (dislpays at the top of the page)', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="logo" value="logo.png" size="40" maxlength="100"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Enable Web Link <img src="images/help.png" style="cursor:help" onmouseover="Tip('This will add a menu item that will go to your community website.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="enableWebLink">
          <option value="true" selected>Yes</option>
          <option value="false">No</option>
        </select>
      </td>
      <td class="rowColor2" width="1%" nowrap>Web Address <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter in the URL of your web if you have enabled the web link. Ex: http://www.yourdomain.com', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap><input type="text" name="webUrl" value="http://www.yourdomain.com" size="50" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>Enable Forum Link <img src="images/help.png" style="cursor:help" onmouseover="Tip('This will add a menu item that will go to your community forum.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select name="enableForumLink">
          <option value="true" selected>Yes</option>
          <option value="false">No</option>
        </select>
      </td>
      <td class="rowColor1" width="1%" nowrap>Forum Address <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter in the URL of your forum if you have enabled the forum link.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="forumURL" value="http://www.yourdomain.com/forum/" size="50" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Enable HLstatsX Link <img src="images/help.png" style="cursor:help" onmouseover="Tip('This will add a link per each Steam_ID at Ban List, that will search it in your HLstatsX Community Edition (http://www.hlxcommunity.com/).', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="enableHLstatsLink">
          <option value="true" selected>Yes</option>
          <option value="false">No</option>
        </select>
      </td>
      <td class="rowColor2" width="1%" nowrap>HlstatsX Address <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter in the URL of your HlstatsX web if you have enabled the HlstatsX link. Ex: http://www.yourdomain.com/HlstatsX/', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap><input type="text" name="HLstatsUrl" value="http://www.yourweb.com/HLstats/" size="50" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>Bans Per Page <img src="images/help.png" style="cursor:help" onmouseover="Tip('This sets the number of bans to be displayed per page on the ban list page.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="bansPerPage" value="100" size="10" maxlength="5" onkeyup="removeCharacters(this)"/></td>
      <td class="rowColor1" width="1%" nowrap>Number of Page Links <img src="images/help.png" style="cursor:help" onmouseover="Tip('The number of links to show before and after selected page (IE: set at 2 you would see 1 2 ... 10 11 [12] 13 14 ... 23 24).', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="numPageLinks" value="2" size="5" maxlength="2" onkeyup="removeCharacters(this)"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Demo Directory <img src="images/help.png" style="cursor:help" onmouseover="Tip('The directory relative to the root of this webpage.  By default it is set to the demos folder.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="demoDir" name="demoDir" value="demos" size="40" maxlength="40"/>
        <img src="images/warning.png" id="demoDirWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap>Demo Size Limit(MB) <img src="images/help.png" style="cursor:help" onmouseover="Tip('The max demo size in MB that can be uploaded.  This can not be higher than what is defined in the php.ini configuration file.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="demoSizeLimit" name="demoSizeLimit" value="30" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="demoSizeLimitWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>User Create Code <img src="images/help.png" style="cursor:help" onmouseover="Tip('This is the code that you can give to members/admins to create their own account to access this site if you are running in standalone mode.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="createUserCode" name="createUserCode" value="" size="20" maxlength="30" onkeyup="removeSpecialCharacters(this)"/>
        <img src="images/warning.png" id="createUserCodeWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap>Default Language <img src="images/help.png" style="cursor:help" onmouseover="Tip('The language they were shown by default to all visitors that do not specify one. Will also be used in the messages that are sent to game server.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select name="LANGUAGE">
          <option value="English" selected>English</option>
          <option value="Spanish">Spanish</option>
          <option value="French">French</option>
          <option value="Russian">Russian</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Send Emails On Ban <img src="images/help.png" style="cursor:help" onmouseover="Tip('If yes, all emails listed below will recieve an email when a new ban is added.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="sendEmailsOnBan">
          <option value="true">Yes</option>
          <option value="false">No</option>
        </select>
      </td>
      <td class="rowColor2" width="1%" nowrap>Send Emails On Demo Add <img src="images/help.png" style="cursor:help" onmouseover="Tip('If yes, all emails listed below will recieve an email when a new demo is added.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="sendEmailsDemo">
          <option value="true">Yes</option>
          <option value="false">No</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>Email Address of Sender <img src="images/help.png" style="cursor:help" onmouseover="Tip('This is the \'from\' address the emails below will see when they recieve an email for newly added bans or demos.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" colspan="3" nowrap><input type="text" name="senderEmail" value="" size="40" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap valign="top">Email Addresses to Recieve Ban and Demo Notices <img src="images/help.png" style="cursor:help" onmouseover="Tip('The email address of people you wish to recieve ban add or demo add notifications.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" valign="top" nowrap align="right">
        <input type="text" id="email" name="email" value="" size="40" maxlength="255"/><br/>
        <input type="button" value="Add >>" onclick="addEmail()"/><br/>
        <input type="button" value="<< Remove" onclick="removeEmail()"/>
      </td>
      <td class="rowColor2" nowrap colspan="2">
        <select id="emailList" name="emailList" size="5">
        </select>
        <textarea id="emails" name="emails" rows="5" cols="40" readonly style="display:none"></textarea>
      </td>
    </tr>
    </table>
  </div>

  <br/>

  <div class="tborder">
    <div id="tableHead">
      <div><b>Ban Settings</b></div>
    </div>

    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" nowrap>Ban Message <img src="images/help.png" style="cursor:help" onmouseover="Tip('The message that banned users will see when they attempt to connect to your servers. Use the Var \'gb_time\' to add the lenght of the ban in the message, example: You are banned gb_time. Visit www.yourweb.com/banned/ for more info.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" nowrap>
        <input type="text" id="banMessage" name="banMessage" value="You are banned gb_time because gb_reason. Appeal at yourdomain.com" size="60" maxlength="255" onkeyup="removeSpecialCharacters(this)"/>
        <img src="images/warning.png" id="banMessageWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap>Allow Admins to be Banned <img src="images/help.png" style="cursor:help" onmouseover="Tip('Set this to true to allow admins to ban other admins.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" nowrap>
        <select name="allowAdminBan">
          <option value="true">Yes</option>
          <option value="false">No</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Days to keep pending bans banned <img src="images/help.png" style="cursor:help" onmouseover="Tip('The number of days a ban in pending mode should be banned for.  This only applies to bans greater than 1 hour and issued by a member.  The ban will be no different from an inactive ban after this number of days if it is not removed from pending status.  Set to 0 to let anyone banned by a member for more than an hour to be able to rejoin instantly.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="daysBanPending" name="daysBanPending" value="5" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="daysBanPendingWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap>Remove pending on demo upload <img src="images/help.png" style="cursor:help" onmouseover="Tip('Remove the pending status of a ban if a member uploads a demo for the pending ban.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="removePendingOnUpload">
          <option value="true">Yes</option>
          <option value="false">No</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Hash Code <img src="images/help.png" style="cursor:help" onmouseover="Tip('This is a secret code that is used by the ES script to talk to the web server when banning.  This is to prevent some random person from adding a ban.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="hash" name="hash" value="" size="40" maxlength="255" onkeyup="removeSpecialCharacters(this)"/>
        <img src="images/warning.png" id="hashWarn" style="display:none"/>
        <input type="button" value="Auto-Generate" onclick="generateHash()">
      </td>
      <td class="rowColor2" width="1%" nowrap>Teach Admins <img src="images/help.png" style="cursor:help" onmouseover="Tip('Set this to yes if you wish to display the \'Type !banmenu\' message after a member/admin dies.  This is to teach or remind members how to ban.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" nowrap>
        <select name="teachAdmins">
          <option value="1">Yes</option>
          <option value="0">No</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_112 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_113 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="adviseInGame">
          <option value="1" selected><?php echo $LAN_CONFIGURATION_114 ?></option>
          <option value="2"><?php echo $LAN_CONFIGURATION_115 ?></option>
          <option value="3"><?php echo $LAN_CONFIGURATION_116 ?></option>
          <option value="4"><?php echo $LAN_CONFIGURATION_117 ?></option>
          <option value="5"><?php echo $LAN_CONFIGURATION_118 ?></option>
        </select>
      </td>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_123; ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_124; ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="adviseInGameLenght">
            <option value="300">5 Minutes</option>
            <option value="900">15 Minutes</option>
            <option value="1800">30 Minutes</option>
            <option value="3600">1 Hour</option>
            <option value="10800">3 Hours</option>
            <option value="21600" selected>6 Hours</option>
            <option value="43200">12 Hours</option>
            <option value="86400">1 Day</option>
            <option value="259200">3 Days</option>
            <option value="604800">1 Week</option>
            <option value="1209600">2 Weeks</option>
            <option value="2419200">1 Month</option>
            <option value="7257600">3 Months</option>
            <option value="14515200">6 Months</option>
        </select>
      </td>
    </tr>
    </table>
  </div>

  <br/>

  <div class="tborder">
    <div id="tableHead">
      <div><b>SMF Integration Settings</b></div>
    </div>

    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" nowrap>Enable SMF Integration <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enable this to integrate with your SMF boards and use the SMF member groups instead.  The GlobalBan web pages must be installed under your Forums folder (yoursite.com/Forums/banned).', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select id="smfIntegration" name="smfIntegration" onchange="hideShowSuperUserBlock();">
          <option value="true">Yes</option>
          <option value="false" selected>No</option>
        </select>
      </td>
      <td class="rowColor1" width="1%" nowrap>SMF Super-User Group <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter in the group ID that you wish to associate with that will have full access to this site.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="smfFullPowerGroup" name="smfFullPowerGroup" value="0" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfFullPowerGroupWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap>SMF Admin Group <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter the group ID that you wish to associate with that will be able to ban anyone without any restrictions.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" nowrap>
        <input type="text" id="smfAdminGroup" name="smfAdminGroup" value="0" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfAdminGroupWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>SMF Table Prefix <img src="images/help.png" style="cursor:help" onmouseover="Tip('The prefix of your SMF tables (normally smf_ by default).', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" nowrap>
        <input type="text" id="smfTablePrefix" name="smfTablePrefix" value="smf_" size="15" maxlength="10"/>
        <img src="images/warning.png" id="smfTablePrefixWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap>SMF Ban Manger Group <img src="images/help.png" style="cursor:help" onmouseover="Tip('TEnter the group ID that you wish to associate with that will be able to access all bans.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="smfBanManagerGroup" name="smfBanManagerGroup" value="0" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfBanManagerGroupWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap>SMF Member Group <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter the group ID that you wish to associate with that will be able to ban, but all bans greater than 1 hour will be placed in pending mode.  If the ban is not removed from pending mode after the number of days specified by \'days to keep pending banned\' then the ban will become inactive.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" nowrap>
        <input type="text" id="smfMemberGroup" name="smfMemberGroup" value="0" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfMemberGroupWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>SMF No Power Group <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter the group ID that you wish to associate with that will have no powers and will rely on admin group assignment powers.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="smfNoPowerGroup" name="smfNoPowerGroup" value="0" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfNoPowerGroupWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap></td>
      <td class="rowColor1" width="1%" nowrap></td>
      <td class="rowColor1" width="1%" nowrap></td>
      <td class="rowColor1" width="1%" nowrap></td>
    </tr>
    </table>
  </div>

<br/>

  <div class="tborder">
    <div id="tableHead">
      <div><b>e107 Integration Settings</b></div>
    </div>

    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" nowrap>Enable e107 Auto New Post <img src="images/help.png" style="cursor:help" onmouseover="Tip('If you have an e107 forum and want GlobalBan automatically create a new post in your forum e107 every time someone adds a new ban, select this option and set the rest of this section.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select id="enableAutoPoste107Forum" name="enableAutoPoste107Forum">
          <option value="true">Yes</option>
          <option value="false" selected>No</option>
        </select>
      </td>
      <td class="rowColor1" width="1%" nowrap>e107 Web Address <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter in the URL of your e107 web. Ex: \'http://www.your_e107_domain.com/\'', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="e107Url" name="e107Url" value="http://www.your_e107_web.com/" size="40" maxlength="255"/>
        <img src="images/warning.png" id="e107UrlWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>e107 Database Host <img src="images/help.png" style="cursor:help" onmouseover="Tip('Set the e107 Database\'s host, normaly it\'s localhost.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="e107_dbHostName" name="e107_dbHostName" value="localhost" size="40" maxlength="255"/>
        <img src="images/warning.png" id="e107_dbHostNameWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap>e107 Table Prefix <img src="images/help.png" style="cursor:help" onmouseover="Tip('The prefix of your e107 tables (normally \'e107_\' by default).', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="e107TablePrefix" name="e107TablePrefix" value="e107_" size="30" maxlength="15"/>
        <img src="images/warning.png" id="e107TablePrefixWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>Database Username <img src="images/help.png" style="cursor:help" onmouseover="Tip('MySQL user with access to the database used by e107.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="e107_dbUserName" name="e107_dbUserName" value="db_e107_UserName" size="40" maxlength="255"/>
        <img src="images/warning.png" id="e107_dbUserNameWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap>e107 Username <img src="images/help.png" style="cursor:help" onmouseover="Tip('e107 Registered User that we wish listed as author of the post GlobalBan generated. We introduce it using the format \'ID.UserName\' for example \'5.Globalban\'.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="e107_GlobalBan_user" name="e107_GlobalBan_user" value="5.GlobalBan" size="30" maxlength="255"/>
        <img src="images/warning.png" id="e107_GlobalBan_userWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Database Password <img src="images/help.png" style="cursor:help" onmouseover="Tip('MySQL Password to access e107 database.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="e107_dbPassword" name="e107_dbPassword" value="db_e107_Password" size="40" maxlength="255"/>
        <img src="images/warning.png" id="e107_dbPasswordWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap>ID Category Forum <img src="images/help.png" style="cursor:help" onmouseover="Tip('For example if your Banned e107 Forum category link is \'http://www.youre107.com/e107_plugins/forum/forum_viewforum.php?19\' you must set it to \'19\'', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="e107_bans_forum_category_number" name="e107_bans_forum_category_number" value="1" size="15" maxlength="255" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="e107_bans_forum_category_numberWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>Database Name <img src="images/help.png" style="cursor:help" onmouseover="Tip('MySQL Database Name used by your e107 website.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="e107_dbName" name="e107_dbName" value="db_e107" size="40" maxlength="255"/>
        <img src="images/warning.png" id="e107_dbNameWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap></td>
      <td class="rowColor1" width="1%" nowrap></td>
    </tr>
    </table>
  </div>

  <br/>
  
  <div class="tborder" id="superUserBlock">
    <div id="tableHead">
      <div><b>Super-User Setup (ignored if SMF integration enabled)</b></div>
    </div>
    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
  		<tr>
  			<td class="rowColor1" width="1%" nowrap>Username: <img src="images/help.png" style="cursor:help" onmouseover="Tip('This is the username you will use to log in as a super-user admin for the site.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
  			<td class="rowColor1">
	          <input type="text" name="username" id="username" value="" size="40" maxlength="40" onkeyup="removeSpecialCharacters(this)"/>
	          <img src="images/warning.png" id="usernameWarn" style="display:none"/>
	        </td>
	  		</tr>
  		<tr>
  			<td class="rowColor2" width="1%" nowrap>Steam ID: <img src="images/help.png" style="cursor:help" onmouseover="Tip('This is your steam ID.  If it is not valid, you will not be able to execute in-game bans.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
  			<td class="rowColor2">
	          <input name="steamId" id="steamId" type="text" value="" size="25" maxlength="25"/> (must be in <b>STEAM_X:X:XXXXXX</b> format)
	          <img src="images/warning.png" id="steamIdWarn" style="display:none"/>
        	</td>
  		</tr>
  		<tr>
  			<td class="rowColor1" width="1%" nowrap>Password: <img src="images/help.png" style="cursor:help" onmouseover="Tip('The password associated with your username that you will use to log in.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
  			<td class="rowColor1">
	          <input type="password" name="password" id="password" value="" size="25" maxlength="25"/>
	          <img src="images/warning.png" id="passwordWarn" style="display:none"/>
	        </td>
  		</tr>
  		<tr>
  			<td class="rowColor2" width="1%" nowrap>Verify Password:  <img src="images/help.png" style="cursor:help" onmouseover="Tip('The password verification is to make sure you typed in your password correctly.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
  			<td class="rowColor2">
	          <input type="password" name="vpassword" id="vpassword" value="" size="25" maxlength="25"/>
	          <img src="images/warning.png" id="vpasswordWarn" style="display:none"/>
	        </td>
  		</tr>
  		<tr>
  			<td class="rowColor1" width="1%" nowrap>Email: <img src="images/help.png" style="cursor:help" onmouseover="Tip('This email address will be used to email you your password if you ever forget it.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td></td>
  			<td class="rowColor1">
	          <input type="text" name="userEmail" id="userEmail" value="" size="60" maxlength="80" />
	          <img src="images/warning.png" id="userEmailWarn" style="display:none"/>
	        </td>
  		</tr>
  	</table>
	</div>

  <br/>

  <div class="tborder">
    <div id="tableHead">
      <div>
        <input type="hidden" id="step" name="step" value="3"/>
        <input type="submit" value="Install GlobalBan">
      </div>
    </div>
  </div>
  
</form>
<?php
}
/*************************************************
 * END STEP 2
 *************************************************/
?>


<?php
/*************************************************
 * STEP 3 - THE LAST PAGE
 * ALL DATABASE TABLES CREATED AND CONFIG SET
 *************************************************/
if($step == 3) {
?>
<h2>Install Complete - Redirecting...</h2>
<script type="text/javascript">
window.location = "installComplete.php?fileError=<?php echo $fileError?>"
</script>
<?php
}
/*************************************************
 * END STEP 3
 *************************************************/

?>

<div id="footer">
Developed by <a href="mailto:soynuts@unbuinc.net">Soynuts</a> of <a href="http://unbuinc.net">UNBU Inc.</a> &copy;2007
</div>
</div>

</body>
</html>
<?php ob_flush(); ?>
