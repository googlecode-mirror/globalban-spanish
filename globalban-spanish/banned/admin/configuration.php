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

// Only those with full privs can do configuration
if($fullPower) {
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
    // No Power
    if(document.getElementById("smfNoPowerGroup").value == "" || document.getElementById("smfNoPowerGroup").value == 0) {
      warningMessage += "You must specify a SMF group that will have no immediate powers.\n";
      document.getElementById("smfNoPowerGroupWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("smfNoPowerGroupWarn").style.display = "none";
    }
    document.getElementById("createUserCodeWarn").style.display = "none";
  } else {
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
  }

  if(!noWarnings) {
    alert(warningMessage);
  }
  
  // Check all inputs for the $ and other php special characters.
  
  return noWarnings;
}
</script>

<?php
$currentVersion = substr($version, 1);
$currentVersionTemp = split(" ", $currentVersion);

// Make sure we can read data from another URL
if(file_get_contents("http://unbuinc.net/gbanversion.php")) {
  $officialVersion = substr(file_get_contents("http://unbuinc.net/gbanversion.php"), 1);
} else {
  $officialVersion = "N/A";
}
?>

<form action="index.php?page=configurationSave&adminPage=1" onsubmit="return formVerify();" method="POST">
  <div class="tborder">
    <div id="tableHead">
      <div><b>Version Information</b></div>
    </div>
    <table id="settingsTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
      <tr>
        <?php
          if((float)$currentVersionTemp[0] < (float)$officialVersion) {
        ?>
        <td class="rowColor1" width="1%" nowrap><b>Your Version:</b><font color="red"><?=$currentVersion?></font></td>
        <?php
          } else {
        ?>
        <td class="rowColor1" width="1%" nowrap><b>Your Version:</b><?=$currentVersion?></td>
        <?php
          }
        ?>
        <td class="rowColor1" width="1%" nowrap><b>Official Version:</b><?=$officialVersion?></td>
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
      <td class="rowColor1" width="1%" nowrap><input type="text" name="siteName" value="<?=$config->siteName?>" size="40" maxlength="255" onkeyup="removeSpecialCharacters(this)"/></td>
      <td class="rowColor1" width="1%" nowrap>Logo <img src="images/help.png" style="cursor:help" onmouseover="Tip('This must be the exact file name of your logo image found in the images directory. (dislpays at the top of the page)', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="logo" value="<?=$config->siteLogo?>" size="40" maxlength="100"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Enable Forum Link <img src="images/help.png" style="cursor:help" onmouseover="Tip('This will add a menu item that will go to your community forum.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="enableForumLink">
          <option value="true"<?php if($config->enableForumLink) echo " selected"; ?>>Yes</option>
          <option value="false"<?php if(!$config->enableForumLink) echo " selected"; ?>>No</option>
        </select>
      </td>
      <td class="rowColor2" width="1%" nowrap>Forum Address <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter in the URL of your forum if you have enabled the forum link.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap><input type="text" name="forumURL" value="<?=$config->forumURL?>" size="40" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>Bans Per Page <img src="images/help.png" style="cursor:help" onmouseover="Tip('This sets the number of bans to be displayed per page on the ban list page.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="bansPerPage" value="<?=$config->bansPerPage?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/></td>
      <td class="rowColor1" width="1%" nowrap>Number of Page Links <img src="images/help.png" style="cursor:help" onmouseover="Tip('The number of links to show before and after selected page (IE: set at 2 you would see 1 2 ... 10 11 [12] 13 14 ... 23 24).', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="numPageLinks" value="<?=$config->maxPageLinks?>" size="5" maxlength="2" onkeyup="removeCharacters(this)"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Demo Directory <img src="images/help.png" style="cursor:help" onmouseover="Tip('The directory relative to the root of this webpage.  By default it is set to the demos folder.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="demoDir" name="demoDir" value="<?=$config->demoRootDir?>" size="40" maxlength="40"/>
        <img src="images/warning.png" id="demoDirWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap>Demo Size Limit(MB) <img src="images/help.png" style="cursor:help" onmouseover="Tip('The max demo size in MB that can be uploaded.  This can not be higher than what is defined in the php.ini configuration file.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="demoSizeLimit" name="demoSizeLimit" value="<?=$config->demoSizeLimit?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="demoSizeLimitWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>User Create Code <img src="images/help.png" style="cursor:help" onmouseover="Tip('This is the code that you can give to members/admins to create their own account to access this site if you are running in standalone mode.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="createUserCode" name="createUserCode" value="<?=$config->createUserCode?>" size="20" maxlength="30" onkeyup="removeSpecialCharacters(this)"/>
        <img src="images/warning.png" id="createUserCodeWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap></td>
      <td class="rowColor1" width="1%" nowrap></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Send Emails On Ban <img src="images/help.png" style="cursor:help" onmouseover="Tip('If yes, all emails listed below will recieve an email when a new ban is added.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="sendEmailsOnBan">
          <option value="true"<?php if($config->sendEmails) echo " selected"; ?>>Yes</option>
          <option value="false"<?php if(!$config->sendEmails) echo " selected"; ?>>No</option>
        </select>
      </td>
      <td class="rowColor2" width="1%" nowrap>Send Emails On Demo Add <img src="images/help.png" style="cursor:help" onmouseover="Tip('If yes, all emails listed below will recieve an email when a new demo is added.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="sendEmailsDemo">
          <option value="true"<?php if($config->sendDemoEmails) echo " selected"; ?>>Yes</option>
          <option value="false"<?php if(!$config->sendDemoEmails) echo " selected"; ?>>No</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>Email Address of Sender <img src="images/help.png" style="cursor:help" onmouseover="Tip('This is the \'from\' address the emails below will see when they recieve an email for newly added bans or demos.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" colspan="3" nowrap><input type="text" name="senderEmail" value="<?=$config->emailFromHeader?>" size="40" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap valign="top">Email Addresses to Recieve Ban and Demo Notices <img src="images/help.png" style="cursor:help" onmouseover="Tip('The email address of people you wish to recieve ban add or demo add notifications.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" valign="top" nowrap>
        <input type="text" id="email" name="email" value="" size="40" maxlength="255"/>
        <input type="button" value="Add >>" onclick="addEmail()"/>
        <input type="button" value="<< Remove" onclick="removeEmail()"/>
      </td>
      <td class="rowColor2" width="1%" nowrap colspan="2">
        <select id="emailList" name="emailList" size="5">
          <?php
          for($i=0; $i<count($config->banManagerEmails); $i++) {
                ?><option value="<?=$config->banManagerEmails[$i]?>"><?=$config->banManagerEmails[$i]?></option><?php
            }
          ?>
        </select>
        <textarea id="emails" name="emails" rows="5" cols="40" readonly style="display:none"><?php
            for($i=0; $i<count($config->banManagerEmails); $i++) {
              echo $config->banManagerEmails[$i];
              if($i<count($config->banManagerEmails)-1) {
                echo "\n";
              }
            }
          ?></textarea>
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
      <td class="rowColor1" width="1%" nowrap>Ban Message <img src="images/help.png" style="cursor:help" onmouseover="Tip('The message that banned users will see when they attempt to connect to your servers.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="banMessage" name="banMessage" value="<?=$config->banMessage?>" size="60" maxlength="255" onkeyup="removeSpecialCharacters(this)"/>
        <img src="images/warning.png" id="banMessageWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap>Allow Admins to be Banned <img src="images/help.png" style="cursor:help" onmouseover="Tip('Set this to true to allow admins to ban other admins.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select name="allowAdminBan">
          <option value="true"<?php if($config->allowAdminBans) echo " selected"; ?>>Yes</option>
          <option value="false"<?php if(!$config->allowAdminBans) echo " selected"; ?>>No</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>Days to keep pending bans banned <img src="images/help.png" style="cursor:help" onmouseover="Tip('The number of days a ban in pending mode should be banned for.  This only applies to bans greater than 1 hour and issued by a member.  The ban will be no different from an inactive ban after this number of days if it is not removed from pending status.  Set to 0 to let anyone banned by a member for more than an hour to be able to rejoin instantly.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="daysBanPending" name="daysBanPending" value="<?=$config->daysBanPending?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="daysBanPendingWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap>Remove pending on demo upload <img src="images/help.png" style="cursor:help" onmouseover="Tip('Remove the pending status of a ban if a member uploads a demo for the pending ban.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="removePendingOnUpload">
          <option value="true"<?php if($config->removePendingOnUpload) echo " selected"; ?>>Yes</option>
          <option value="false"<?php if(!$config->removePendingOnUpload) echo " selected"; ?>>No</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>Hash Code <img src="images/help.png" style="cursor:help" onmouseover="Tip('This is a secret code that is used by the ES script to talk to the web server when banning.  This is to prevent some random person from adding a ban.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="hash" name="hash" value="<?=$config->matchHash?>" size="40" maxlength="255" onkeyup="removeSpecialCharacters(this)"/>
        <img src="images/warning.png" id="hashWarn" style="display:none"/>
        <input type="button" value="Generate" onclick="generateHash()">
      </td>
      <td class="rowColor1" width="1%" nowrap>Teach Admins <img src="images/help.png" style="cursor:help" onmouseover="Tip('Set this to yes if you wish to display the \'Type !banmenu\' message after a member/admin dies.  This is to teach or remind members how to ban.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select name="teachAdmins">
          <option value="1"<?php if($config->teachAdmins == 1) echo " selected"; ?>>Yes</option>
          <option value="0"<?php if($config->teachAdmins == 0) echo " selected"; ?>>No</option>
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
      <td class="rowColor1" width="1%" nowrap>Enable SMF Integration <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enable this to integrate with your SMF boards and use the SMF member groups instead.  The SMF tables must start with smf_.  The GlobalBan web pages must be installed under your Forums folder (yoursite.com/Forums/banned).', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select id="smfIntegration" name="smfIntegration">
          <option value="true"<?php if($config->enableSmfIntegration) {echo " selected";} ?>>Yes</option>
          <option value="false"<?php if(!$config->enableSmfIntegration) {echo " selected";} ?>>No</option>
        </select>
      </td>
      <td class="rowColor1" width="1%" nowrap>SMF Table Prefix <img src="images/help.png" style="cursor:help" onmouseover="Tip('The prefix of your SMF tables (normally smf_ by default).', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="smfTablePrefix" name="smfTablePrefix" value="smf_" size="15" maxlength="10"/>
        <img src="images/warning.png" id="smfTablePrefixWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap>SMF Super-User Group <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter in the group ID that you wish to associate with that will have full access to this site.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="smfFullPowerGroup" name="smfFullPowerGroup" value="<?=$config->fullPowerGroup?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfFullPowerGroupWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap>SMF Ban Manger Group <img src="images/help.png" style="cursor:help" onmouseover="Tip('TEnter the group ID that you wish to associate with that will be able to access all bans.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="smfBanManagerGroup" name="smfBanManagerGroup" value="<?=$config->banManagerGroup?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfBanManagerGroupWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>SMF Admin Group <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter the group ID that you wish to associate with that will be able to ban anyone without any restrictions.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="smfAdminGroup" name="smfAdminGroup" value="<?=$config->adminGroup?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfAdminGroupWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap>SMF Member Group <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter the group ID that you wish to associate with that will be able to ban, but all bans greater than 1 hour will be placed in pending mode.  If the ban is not removed from pending mode after the number of days specified by \'days to keep pending banned\' then the ban will become inactive.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="smfMemberGroup" name="smfMemberGroup" value="<?=$config->memberGroup?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfMemberGroupWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap>SMF No Power Group <img src="images/help.png" style="cursor:help" onmouseover="Tip('Enter the group ID that you wish to associate with that will have no powers and will rely on admin group assignment powers.', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="smfNoPowerGroup" name="smfNoPowerGroup" value="<?=$config->noPowerGroup?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfNoPowerGroupWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap></td>
      <td class="rowColor1" width="1%" nowrap></td>
    </tr>
    </table>
  </div>

  <br/>
  
  <div class="tborder">
    <div id="tableHead">
      <div><input type="submit" value="Save Configuration"></div>
    </div>
  </div>

  <h5>Note: Saving the configuration will also update GlobalBan.cfg on all active servers.</h5>

</form>

<?php
} else {
?>
<div class="tborder">
  <div id="tableHead">
    <div><b>Access Denied</b></div>
  </div>
<div class="tborder">
<?php
}
?>
