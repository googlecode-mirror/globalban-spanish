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

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_configuration.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_configuration.php");

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
    alert("<?php echo $LAN_CONFIGURATION_001 ?>");
    return false;
  }
  
  // Do not allow duplicate email addresses
  var emailArray = emails.value.split("\n");
  for(i=0; i<emailArray.length; i++) {
    if(emailArray[i] == email) {
      alert(email + " <?php echo $LAN_CONFIGURATION_002 ?>");
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
    warningMessage += "<?php echo $LAN_CONFIGURATION_003 ?>\n";
    document.getElementById("demoDirWarn").style.display = "";
    noWarnings = false;
  } else {
    document.getElementById("demoDirWarn").style.display = "none";
  }
  
  // Demo Size check
  if(document.getElementById("demoSizeLimit").value == "") {
	warningMessage += "<?php echo $LAN_CONFIGURATION_004 ?>\n";
    document.getElementById("demoSizeLimitWarn").style.display = "";
    noWarnings = false;
  } else {
    document.getElementById("demoSizeLimitWarn").style.display = "none";
  }
  
  // Ban Message
  if(document.getElementById("banMessage").value == "") {
	warningMessage += "<?php echo $LAN_CONFIGURATION_005 ?>\n";
    document.getElementById("banMessageWarn").style.display = "";
    noWarnings = false;
  } else {
    document.getElementById("banMessageWarn").style.display = "none";
  }
  
  // Days to keep pending banned
  if(document.getElementById("daysBanPending").value == "") {
	warningMessage += "<?php echo $LAN_CONFIGURATION_006 ?>\n";
    document.getElementById("daysBanPendingWarn").style.display = "";
    noWarnings = false;
  } else {
    document.getElementById("daysBanPendingWarn").style.display = "none";
  }
  
  // Hash
  if(document.getElementById("hash").value == "") {
	warningMessage += "<?php echo $LAN_CONFIGURATION_007 ?>\n";
    document.getElementById("hashWarn").style.display = "";
    noWarnings = false;
  } else {
    document.getElementById("hashWarn").style.display = "none";
  }
  
  // SMF
  if(document.getElementById("smfIntegration").value == "true") {
    if(document.getElementById("smfTablePrefix").value == "") {
	  warningMessage += "<?php echo $LAN_CONFIGURATION_008 ?>\n";
      document.getElementById("smfTablePrefixWarn").style.display = "none";
      noWarnings = false;
    }
    // Full Power Users
    if(document.getElementById("smfFullPowerGroup").value == "" || document.getElementById("smfFullPowerGroup").value == 0) {
	  warningMessage += "<?php echo $LAN_CONFIGURATION_009 ?>\n";
      document.getElementById("smfFullPowerGroupWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("smfFullPowerGroupWarn").style.display = "none";
    }
    // Ban Managers
    if(document.getElementById("smfBanManagerGroup").value == "" || document.getElementById("smfBanManagerGroup").value == 0) {
	  warningMessage += "<?php echo $LAN_CONFIGURATION_010 ?>\n";
      document.getElementById("smfBanManagerGroupWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("smfBanManagerGroupWarn").style.display = "none";
    }
    // Admins
    if(document.getElementById("smfAdminGroup").value == "" || document.getElementById("smfAdminGroup").value == 0) {
	  warningMessage += "<?php echo $LAN_CONFIGURATION_011 ?>\n";
      document.getElementById("smfAdminGroupWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("smfAdminGroupWarn").style.display = "none";
    }
    // Members
    if(document.getElementById("smfMemberGroup").value == "" || document.getElementById("smfMemberGroup").value == 0) {
	  warningMessage += "<?php echo $LAN_CONFIGURATION_012 ?>\n";
      document.getElementById("smfMemberGroupWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("smfMemberGroupWarn").style.display = "none";
    }
    // No Power
    if(document.getElementById("smfNoPowerGroup").value == "" || document.getElementById("smfNoPowerGroup").value == 0) {
	  warningMessage += "<?php echo $LAN_CONFIGURATION_013 ?>\n";
      document.getElementById("smfNoPowerGroupWarn").style.display = "";
      noWarnings = false;
    } else {
      document.getElementById("smfNoPowerGroupWarn").style.display = "none";
    }
    document.getElementById("createUserCodeWarn").style.display = "none";
  } else {
    // Create User Code Check
    if(document.getElementById("createUserCode").value == "") {
	  warningMessage += "<?php echo $LAN_CONFIGURATION_014 ?>\n";
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

/*
// Make sure we can read data from another URL
if(file_get_contents("http://unbuinc.net/gbanversion.php")) {
  $officialVersion = substr(file_get_contents("http://unbuinc.net/gbanversion.php"), 1);
} else {
  $officialVersion = "N/A";
}
*/

/*
// Make sure we can read data from another URL
if(file_get_contents("http://unbuinc.net/gbanversion.php")) {
  $officialVersion = substr(file_get_contents("http://unbuinc.net/gbanversion.php"), 1);
} else {
  $officialVersion = "N/A";
}
*/
// Make sure we can read data from another URL
if(file_get_contents("http://code.google.com/p/globalban-spanish/source/list")) {
  $officialVersion = "3.4.1 r".str_replace('"', '',substr(strstr(file_get_contents("http://code.google.com/p/globalban-spanish/source/list"), '<td class="id"><a href="detail?r='),strlen('<td class="id"><a href="detail?r='),3));
} else {
  $officialVersion = "N/A";
}


if(!is_writable("config/class.Config.php")) {
    echo "<h4 style='color:red; margin-bottom:0px'><i>The file config/class.Config.php is not writable. Please check your web server's permissions before continuing.</i></h4></br>";
}
?>

<form action="index.php?page=configurationSave&adminPage=1" onsubmit="return formVerify();" method="POST">
  <div class="tborder">
    <div id="tableHead">
      <div><b><?php echo $LAN_CONFIGURATION_015 ?></b></div>
    </div>
    <table id="settingsTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
      <tr>
        <?php
          // if((float)$currentVersionTemp[0] < (float)$officialVersion) {
          if($currentVersion <> $officialVersion) {
        ?>
        <td class="rowColor1" width="1%" nowrap><b>Your Version: </b><font color="red"><?php echo $currentVersion ?></font></td>
        <?php
          } else {
        ?>
        <td class="rowColor1" width="1%" nowrap><b><?php echo $LAN_CONFIGURATION_016 ?> </b><?php echo $currentVersion ?></td>
        <?php
          }
        ?>
        <td class="rowColor1" width="1%" nowrap><a href="http://code.google.com/p/globalban-spanish/source/list"><b><?php echo $LAN_CONFIGURATION_022 ?> </b><?php echo $officialVersion ?></a></td>
      </tr>
    </table>
  </div>

  <br/>

  <div class="tborder">
    <div id="tableHead">
      <div><b><?php echo $LAN_CONFIGURATION_017 ?></b></div>
    </div>

    <table id="settingsTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_018 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_019 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="siteName" value="<?php echo $config->siteName ?>" size="40" maxlength="255" onkeyup="removeSpecialCharacters(this)"/></td>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_020 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_021 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="logo" value="<?php echo $config->siteLogo ?>" size="40" maxlength="100"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_083 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_084 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="enableWebLink">
          <option value="true"<?php if($config->enableWebLink) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_025 ?></option>
          <option value="false"<?php if(!$config->enableWebLink || empty($config->enableWebLink)) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_026 ?></option>
        </select>
      </td>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_085 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_086 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap><input type="text" name="webUrl" value="<?php echo $config->webUrl ?>" size="40" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_023 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_024 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select name="enableForumLink">
          <option value="true"<?php if($config->enableForumLink) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_025 ?></option>
          <option value="false"<?php if(!$config->enableForumLink) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_026 ?></option>
        </select>
      </td>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_027 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_028 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="forumURL" value="<?php echo $config->forumURL ?>" size="40" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_087 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_088 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="enableHLstatsLink">
          <option value="true"<?php if($config->enableHLstatsLink) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_025 ?></option>
          <option value="false"<?php if(!$config->enableHLstatsLink || empty($config->enableHLstatsLink)) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_026 ?></option>
        </select>
      </td>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_089 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_090 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap><input type="text" name="HLstatsUrl" value="<?php echo $config->HLstatsUrl ?>" size="40" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_029 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_030 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="bansPerPage" value="<?php echo $config->bansPerPage ?>" size="5" maxlength="3" onkeyup="removeCharacters(this)"/></td>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_031 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_032 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap><input type="text" name="numPageLinks" value="<?php echo $config->maxPageLinks ?>" size="3" maxlength="2" onkeyup="removeCharacters(this)"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_033 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_034 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="demoDir" name="demoDir" value="<?php echo $config->demoRootDir ?>" size="40" maxlength="40"/>
        <img src="images/warning.png" id="demoDirWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_035 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_036 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="demoSizeLimit" name="demoSizeLimit" value="<?php echo $config->demoSizeLimit ?>" size="3" maxlength="3" onkeyup="removeCharacters(this)"/><?php echo " post_max_size: ".ini_get("post_max_size")." and upload_max_filesize: ".ini_get("upload_max_filesize") ?>
        <img src="images/warning.png" id="demoSizeLimitWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_037 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_038 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="createUserCode" name="createUserCode" value="<?php echo $config->createUserCode ?>" size="40" maxlength="40" onkeyup="removeSpecialCharacters(this)"/>
        <img src="images/warning.png" id="createUserCodeWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_091 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_092 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select name="LANGUAGE">
          <option value="English"<?php if($config->LANGUAGE == "English" || empty($config->LANGUAGE)) echo " selected"; ?>>English</option>
          <option value="Spanish"<?php if($config->LANGUAGE == "Spanish") echo " selected"; ?>>Spanish</option>
          <option value="French"<?php if($config->LANGUAGE == "French") echo " selected"; ?>>French</option>
          <option value="French"<?php if($config->LANGUAGE == "Russian") echo " selected"; ?>>Russian</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_039 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_040 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="sendEmailsOnBan">
          <option value="true"<?php if($config->sendEmails) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_025 ?></option>
          <option value="false"<?php if(!$config->sendEmails) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_026 ?></option>
        </select>
      </td>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_041 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_042 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="sendEmailsDemo">
          <option value="true"<?php if($config->sendDemoEmails) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_025 ?></option>
          <option value="false"<?php if(!$config->sendDemoEmails) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_026 ?></option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_043 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_044 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" colspan="3" nowrap><input type="text" name="senderEmail" value="<?php echo $config->emailFromHeader ?>" size="40" maxlength="255"/></td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap valign="top"><?php echo $LAN_CONFIGURATION_045 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_046 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" valign="top" nowrap>
        <input type="text" id="email" name="email" value="" size="40" maxlength="255"/><br/><br/>
        <input type="button" value="<?php echo $LAN_CONFIGURATION_047 ?>" onclick="addEmail()"/>
      </td>
      <td class="rowColor2" width="1%" nowrap colspan="2">
        <input type="button" value="<?php echo $LAN_CONFIGURATION_048 ?>" onclick="removeEmail()"/>
        <select id="emailList" name="emailList" size="5">
          <?php
          for($i=0; $i<count($config->banManagerEmails); $i++) {
                ?><option value="<?php echo $config->banManagerEmails[$i] ?>"><?php echo $config->banManagerEmails[$i] ?></option><?php
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
      <div><b><?php echo $LAN_CONFIGURATION_049 ?></b></div>
    </div>

    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_050 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_051 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="banMessage" name="banMessage" value="<?php echo $config->banMessage ?>" size="70" maxlength="255" onkeyup="removeSpecialCharacters(this)"/>
        <img src="images/warning.png" id="banMessageWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_052 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_053 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select name="allowAdminBan">
          <option value="true"<?php if($config->allowAdminBans) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_025 ?></option>
          <option value="false"<?php if(!$config->allowAdminBans) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_026 ?></option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_054 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_055 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="daysBanPending" name="daysBanPending" value="<?php echo $config->daysBanPending ?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="daysBanPendingWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_056 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_057 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="removePendingOnUpload">
          <option value="true"<?php if($config->removePendingOnUpload) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_025 ?></option>
          <option value="false"<?php if(!$config->removePendingOnUpload) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_026 ?></option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_058 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_059 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="hash" name="hash" value="<?php echo $config->matchHash ?>" size="40" maxlength="255" onkeyup="removeSpecialCharacters(this)"/>
        <img src="images/warning.png" id="hashWarn" style="display:none"/>
        <input type="button" value="<?php echo $LAN_CONFIGURATION_082 ?>" onclick="generateHash()">
      </td>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_060 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_061 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select name="teachAdmins">
          <option value="1"<?php if($config->teachAdmins == 1) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_025 ?></option>
          <option value="0"<?php if($config->teachAdmins == 0) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_026 ?></option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_112 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_113 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <select name="adviseInGame">
          <option value="1"<?php if($config->adviseInGame == 1 || empty($config->adviseInGame)) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_114 ?></option>
          <option value="2"<?php if($config->adviseInGame == 2) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_115 ?></option>
          <option value="3"<?php if($config->adviseInGame == 3) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_116 ?></option>
          <option value="4"<?php if($config->adviseInGame == 4) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_117 ?></option>
          <option value="5"<?php if($config->adviseInGame == 5) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_118 ?></option>
        </select>
      </td>
      <td class="rowColor2" width="1%" nowrap></td>
      <td class="rowColor2" width="1%" nowrap></td>
    </tr>
    </table>
  </div>
  
  <br/>

  <div class="tborder">
    <div id="tableHead">
      <div><b><?php echo $LAN_CONFIGURATION_062 ?></b></div>
    </div>

    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_063 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_064 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select id="smfIntegration" name="smfIntegration">
          <option value="true"<?php if($config->enableSmfIntegration) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_025 ?></option>
          <option value="false"<?php if(!$config->enableSmfIntegration) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_026 ?></option>
        </select>
      </td>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_065 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_066 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="smfTablePrefix" name="smfTablePrefix" value="smf_" size="15" maxlength="10"/>
        <img src="images/warning.png" id="smfTablePrefixWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_067 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_068 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="smfFullPowerGroup" name="smfFullPowerGroup" value="<?php echo $config->fullPowerGroup ?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfFullPowerGroupWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_069 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_070 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="smfBanManagerGroup" name="smfBanManagerGroup" value="<?php echo $config->banManagerGroup ?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfBanManagerGroupWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_071 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_072 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="smfAdminGroup" name="smfAdminGroup" value="<?php echo $config->adminGroup ?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfAdminGroupWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_073 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_074 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="smfMemberGroup" name="smfMemberGroup" value="<?php echo $config->memberGroup ?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfMemberGroupWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_075 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_076 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="smfNoPowerGroup" name="smfNoPowerGroup" value="<?php echo $config->noPowerGroup ?>" size="10" maxlength="5" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="smfNoPowerGroupWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap></td>
      <td class="rowColor2" width="1%" nowrap></td>
    </tr>
    </table>
  </div>
  
  <br/>

  <div class="tborder">
    <div id="tableHead">
      <div><b><?php echo $LAN_CONFIGURATION_093 ?></b></div>
    </div>

    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_094 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_095 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <select id="enableAutoPoste107Forum" name="enableAutoPoste107Forum">
          <option value="true"<?php if($config->enableAutoPoste107Forum) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_025 ?></option>
          <option value="false"<?php if(!$config->enableAutoPoste107Forum || empty($config->enableAutoPoste107Forum)) echo " selected"; ?>><?php echo $LAN_CONFIGURATION_026 ?></option>
        </select>
      </td>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_096 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_097 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="e107Url" name="e107Url" value="<?php echo $config->e107Url ?>" size="40" maxlength="255"/>
        <img src="images/warning.png" id="e107UrlWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_098 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_099 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="e107_dbHostName" name="e107_dbHostName" value="<?php echo $config->e107_dbHostName ?>" size="40" maxlength="255"/>
        <img src="images/warning.png" id="e107_dbHostNameWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_100 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_101 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="e107TablePrefix" name="e107TablePrefix" value="<?php echo $config->e107TablePrefix ?>" size="30" maxlength="15"/>
        <img src="images/warning.png" id="e107TablePrefixWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_102 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_103 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="e107_dbUserName" name="e107_dbUserName" value="<?php echo $config->e107_dbUserName ?>" size="40" maxlength="255"/>
        <img src="images/warning.png" id="e107_dbUserNameWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_104 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_105 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="e107_GlobalBan_user" name="e107_GlobalBan_user" value="<?php echo $config->e107_GlobalBan_user ?>" size="30" maxlength="255"/>
        <img src="images/warning.png" id="e107_GlobalBan_userWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_106 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_107 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="e107_dbPassword" name="e107_dbPassword" value="<?php echo $config->e107_dbPassword ?>" size="40" maxlength="255"/>
        <img src="images/warning.png" id="e107_dbPasswordWarn" style="display:none"/>
      </td>
      <td class="rowColor2" width="1%" nowrap><?php echo $LAN_CONFIGURATION_108 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_109 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor2" width="1%" nowrap>
        <input type="text" id="e107_bans_forum_category_number" name="e107_bans_forum_category_number" value="<?php echo $config->e107_bans_forum_category_number ?>" size="15" maxlength="255" onkeyup="removeCharacters(this)"/>
        <img src="images/warning.png" id="e107_bans_forum_category_numberWarn" style="display:none"/>
      </td>
    </tr>
    <tr>
      <td class="rowColor1" width="1%" nowrap><?php echo $LAN_CONFIGURATION_110 ?> <img src="images/help.png" style="cursor:help" onmouseover="Tip('<?php echo $LAN_CONFIGURATION_111 ?>', WIDTH, 400, SHADOW, true, FADEIN, 300, FADEOUT, 300, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('settingsTable'))"/>:</td>
      <td class="rowColor1" width="1%" nowrap>
        <input type="text" id="e107_dbName" name="e107_dbName" value="<?php echo $config->e107_dbName ?>" size="40" maxlength="255"/>
        <img src="images/warning.png" id="e107_dbNameWarn" style="display:none"/>
      </td>
      <td class="rowColor1" width="1%" nowrap></td>
      <td class="rowColor1" width="1%" nowrap></td>
    </tr>
    </table>
  </div>

  <br/>
  
  <div class="tborder">
    <div id="tableHead">
      <div><input type="submit" value="<?php echo $LAN_CONFIGURATION_077 ?>"></div>
    </div>
  </div>

  <h5><?php echo $LAN_CONFIGURATION_078 ?></h5>

</form>

<?php
} else {
?>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LAN_CONFIGURATION_079; ?></b></div>
  </div>
<div class="tborder">
<?php
}
?>
