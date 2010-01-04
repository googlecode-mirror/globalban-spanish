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

require_once(ROOTDIR."/include/database/class.UserQueries.php");
require_once(ROOTDIR."/include/database/class.ServerQueries.php");

$userQueries = new UserQueries();
$serverQueries = new ServerQueries();

?>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#userTabs > ul").tabs();
  });

function deleteVerify(id, name) {
  if(confirm("Do you really want to delete "+name+"?")) {
    document.getElementById("deleteUser"+id).submit();
  }
}
</script>
<?php

$error = false;
$newAdminError = false;

$nopost = true; // Flag of whether the form was submitted yet
if(isset($_POST['nopost'])) {
	$nopost = $_POST['nopost'];
}

// Boolean values of whether post values are valid for new admin
$valid = array("username"=>false,
        "steamId"=>false,
        "email"=>true);

// If this is set, then that means a new admin is being added
if(isset($_POST['submitAdd'])) {
  if($config->enableSmfIntegration) {
    $id = $_POST['username'];
    // Steam ID
    if(isset($_POST['steamId'])) {
    	$steamId = $_POST['steamId'];
    	if(!empty($steamId)) {
    	 if(preg_match("/^STEAM_[01]:[01]:\d{0,10}$/", $steamId)) {
    		  $valid['steamId'] = true;
    		}
    	}
    }
    
    if($valid['steamId']) {
      $userQueries->addSmfUser($id, $steamId);
      $steamId = "";
    }
  } else {
    // User name
    if(isset($_POST['username'])) {
    	$username = $_POST['username'];
    	if(!$userQueries->usernameExist($username) && !empty($username)) {
    		$valid['username'] = true;
    	}
    }

    // Steam ID
    if(isset($_POST['steamId'])) {
    	$steamId = $_POST['steamId'];
    	if(!empty($steamId)) {
    	 if(preg_match("/^STEAM_[01]:[01]:\d{0,10}$/", $steamId)) {
    		  $valid['steamId'] = true;
    		}
    	}
    }

    if(isset($_POST['email'])) {
    	$email = $_POST['email'];
    	if(!$userQueries->emailExist($email) && !empty($email)) {
    		if(!preg_match("/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/i", $email)) {
    			$valid['email'] = false;
    		}
    	}
    }

    // Only a username and steam id are required for this
    if($valid['username'] && $valid['steamId']) {
      
      $pass = $userQueries->createRandomPassword();
      
      $userQueries->addUser($username, $pass, $_POST['userAccessLevel'], $steamId, $email);
      
      // Send the email
      // To send HTML mail, the Content-type header must be set
      $headers  = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";			
      // Additional headers
      $headers .= "From: ".$config->siteName." Ban Management <".$config->emailFromHeader.">" . "\r\n";
      
      $subject = $config->siteName." Ban Management New User Created";
      
      $body = "<html><body>";
      $body .= "<h2>".$config->siteName." Ban Management</h2>";
      $body .= "<br/><p>Your username is ".$username."<br/>Your password is ".$pass."</p>";
      $body .= "<br/><br/>Your account has now been activated, you may login in: <a href='".ROOTDIR."/index.php?page=login'>Admin Login</a>";;
      $body .= "<br/><p>Please update your profile once logged in with a new password of your choice.</p>";
      $body .= "</body></html>";
      
      mail($email, $subject, $body, $headers);
            
      $username = "";
      $steamId = "";
      $email = "";
    } else {
      $newAdminError = true;
    }
  }
}

// If a server is being deleted
if(isset($_POST['submitDelete'])) {
  if(!$userQueries->deleteUser($_POST['userId'])) {
    $deleteError = true;
  }
}

// Resetting a person's password
if(isset($_POST['forgotPassword'])) {
  $userQueries->forgotPassword($_POST['email']);
}

// Get list of users
$users = $userQueries->getUsers();
?>

<?php
// Only those with full privs can edit users
if($fullPower) {

  if($deleteError) {
    ?><p class="error">Not allowed to delete the last super-user</p><?php
  }
?>
<div class="tborder">
  <div id="tableHead">
    <div><b>Admin List</b></div>
  </div>
  
  <div id="userTabs" class="flora">
      <ul>
          <li><a href="#fragment-1"><span>Super User</span></a></li>
          <li><a href="#fragment-2"><span>Ban Manager</span></a></li>
          <li><a href="#fragment-3"><span>Admin</span></a></li>
          <li><a href="#fragment-4"><span>Member</span></a></li>
          <li><a href="#fragment-5"><span>No Power</span></a></li>
      </ul>
      <div id="fragment-1">
        <?php userTable($users, 1); ?>
      </div>
      <div id="fragment-2">
        <?php userTable($users, 2); ?>
      </div>
      <div id="fragment-3">
        <?php userTable($users, 3); ?>
      </div>
      <div id="fragment-4">
        <?php userTable($users, 4); ?>
      </div>
      <div id="fragment-5">
        <?php userTable($users, 5); ?>
      </div>
  </div>

  
  
  <div id="tableBottom">
    <div>
			<input type="button" value="Save Admins to Servers" class="button" onclick="location.href='index.php?page=uploadAdmins&adminPage=1'" />
    </div>
  </div>
    
</div>
<h5>Resetting a password emails the user a new randomly generated password.</h5>
<br/>
<div class="tborder">
  <div id="tableHead">
    <div><b>Add New Admin</b></div>
  </div>
  <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
  <form action="index.php?page=manageUsers&adminPage=1" method="post" id="form">
  	<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
      <?php
      if(!$config->enableSmfIntegration) {
      ?>
      <tr>
  			<td class="rowColor1"><img src="images/bullet_star.png"/> Username:</td>
  			<td class="rowColor1"><input type="text" name="username" value="<?=$username?>" size="40" maxlength="40" />
  			<?php if(!$valid['username'] && !$nopost) { ?><span class="error">Username already taken</span><?php } ?></td>
  		</tr>
  		<?php
      } else {
      ?>
      <tr>
        <td class="rowColor1">*Username:</td>
  			<td class="rowColor1">
          <select name="username">
            <option value=""></option>
            <?php
            $smfUsers = $userQueries->getSMFUsers();
            foreach($smfUsers as $smfUser) {
              ?><option value="<?=$smfUser->getId()?>"><?=$smfUser->getName()?></option><?php
            }
            ?>
          </select>
  			</td>
      </tr>
      <?php
      }
      ?>
  		<tr>
  			<td class="rowColor2"><img src="images/bullet_star.png"/> Steam ID:</td>			
  			<td class="rowColor2"><input name="steamId" id="steamdId" type="text" value="<?=$steamId?>" size="25" maxlength="25"/> (must be in <b>STEAM_X:X:XXXXXX</b> format)
  			<?php if(!$valid['steamId'] && !$nopost) { ?><span class="error">Steam ID not in vaild format</span><?php } ?></td>
  		</tr>
  		<?php
      if(!$config->enableSmfIntegration) {
      ?>
  		<tr>
  			<td class="rowColor1">Email:</td>
  			<td class="rowColor1"><input type="text" name="email" value="<?php if(!empty($email)) { echo $email; } ?>" size="60" maxlength="80" />
  			<?php if(!$valid['email'] && !$nopost) { ?><span class="error">Enter a valid email</span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor2">Access Level:</td>
  			<td class="rowColor2">
  			 <select id="userAccessLevel" name="userAccessLevel">
            <option value="1">Super User</option>
            <option value="2">Ban Manager</option>
            <option value="3">Admin</option>
            <option value="4">Member</option>
            <option value="5" selected>No Powers</option>
          </select>
        </td>
  		</tr>
  		<?php
      }
      ?>
  		<tr>
  			<td align="left" colspan="3" class="rowColor1">
  				<input type="hidden" name="nopost" value="0" />
  				<input type="reset" value="Reset Form" class="button" />&nbsp;
  				<input type="submit" name="submitAdd" value="Submit" class="button" /></td>
  		</tr>
  </table>
</div>
<h5><img src="images/bullet_star.png"/> Is a required field when adding a new admin</h5>
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
// Power is the access level
function userTable($users, $power) {
  ?>
  <table id="adminUserTable-<?=$power?>" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <th class="colColor2" nowrap>ID</th>
      <th class="colColor1" nowrap>Name</th>
      <th class="colColor2" nowrap>Access Level</th>
      <th class="colColor1" nowrap>Email</th>
      <th class="colColor2" nowrap>Steam ID</th>
      <th class="colColor1" nowrap>Save</th>
      <th class="colColor2" nowrap>Active</th>
      <th class="colColor1" nowrap>Delete</th>
      <?php
      if(!$config->enableSmfIntegration) {
      ?>
      <th class="colColor1" nowrap></th>
      <?php } ?>
    </tr>
    <?php foreach($users as $user) {
          if($user->getAccessLevel() == $power) {
          ?>
          <tr>
            <td class="colColor2" nowrap><?=$user->getId()?></td>
            <?php if(!$config->enableSmfIntegration) { ?>
            <td class="colColor1" nowrap><input type="text" id="username:<?=$user->getId()?>" name="username:<?=$user->getId()?>" value="<?=$user->getName()?>" size="40" maxlength="40"/></td>
            <?php }
            else { ?>
            <td class="colColor1" nowrap><input type="hidden" id="username:<?=$user->getId()?>" name="username:<?=$user->getId()?>" value="<?=$user->getName()?>"/><?=$user->getName()?></td>
            <?php } ?>
            <td class="colColor2" nowrap>
              <select id="userAccessLevel:<?=$user->getId()?>" name="userAccessLevel:<?=$user->getId()?>">
                <option value="1" <?php if($user->getAccessLevel() == 1) { echo "selected"; } ?>>Super User</option>
                <option value="2" <?php if($user->getAccessLevel() == 2) { echo "selected"; } ?>>Ban Manager</option>
                <option value="3" <?php if($user->getAccessLevel() == 3) { echo "selected"; } ?>>Admin</option>
                <option value="4" <?php if($user->getAccessLevel() == 4) { echo "selected"; } ?>>Member</option>
                <option value="5" <?php if($user->getAccessLevel() == 5) { echo "selected"; } ?>>No Powers</option>
              </select>
            </td>
            <?php if(!$config->enableSmfIntegration) { ?>
            <td class="colColor1" nowrap><input type="text" id="userEmail:<?=$user->getId()?>" name="userEmail:<?=$user->getId()?>" value="<?=$user->getEmail()?>" size="35" maxlength="80"/></td>
            <?php }
            else { ?>
            <td class="colColor1" nowrap><input type="hidden" id="userEmail:<?=$user->getId()?>" name="userEmail:<?=$user->getId()?>" value="<?=$user->getEmail()?>"/><?=$user->getEmail()?></td>
            <?php } ?>
            <td class="colColor2" nowrap><input type="text" id="userSteamId:<?=$user->getId()?>"name="userSteamId:<?=$user->getId()?>" value="<?=$user->getSteamId()?>" size="25" maxlength="25"/></td>
            <td id="save:<?=$user->getId()?>" class="colColor1" onclick="saveUser('<?=$user->getId()?>');" style="cursor:pointer;"
                onmouseover="Tip('Click to save <?=$user->getName()?>\'s information', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('adminUserTable-<?=$power?>'))">
            <img src="images/tick.png"/>
            </td>
            <td id="active:<?=$user->getId()?>" class="colColor2" onclick="changeUserActiveStatus(<?=$user->getId()?>, <?=$user->getActive()?>);" style="cursor:pointer;"
                onmouseover="if(<?=$user->getActive()?> == 0) { Tip('Click to activate <?=$user->getName()?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('adminUserTable-<?=$power?>'));}else{Tip('Click to de-activate <?=$user->getName()?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('adminUserTable'));}">
            <?php if($user->getActive() == 0) {
              ?><img src="images/cross.png"/><?php
            } else {
              ?><img src="images/tick.png"/><?php
            } ?>
            </td>
            <td class="colColor1" style="cursor:pointer;" onclick="deleteVerify('<?=$user->getId()?>', '<?=$user->getName()?>');"
                onmouseover="Tip('Click to delete <?=$user->getName()?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('adminUserTable-<?=$power?>'))">
            <form action="index.php?page=manageUsers&adminPage=1" id="deleteUser<?=$user->getId()?>" name="deleteUser<?=$user->getId()?>" method="POST">
              <input type="hidden" name="userId" id="userId" value="<?=$user->getId()?>"/>
              <input type="hidden" name="submitDelete" value="1">
              <img src="images/trash-full.png"/>
            </form>
            </td>
            <?php
            if(!$config->enableSmfIntegration) {
            ?>
            <td class="colColor2">
              <form action="index.php?page=manageUsers&adminPage=1" method="post">
                <input type="hidden" name="email" size="60" maxlength="80" value="<?=$user->getEmail()?>"/>
                <input type="submit" name="forgotPassword" value="Reset Password" class="button" style="cursor:pointer;" onclick="alert('Password reset for <?=$user->getName()?>')">
              </form>            
            </td>
            <?php } ?>
          </tr>
    <?php } // End power if
          } ?>
    
    <tr>
      <td class="colColor2" nowrap></td>
      <td class="colColor1" nowrap>&nbsp;</td>
      <td class="colColor2" nowrap></td>
      <td class="colColor1" nowrap></td>
      <td class="colColor2" nowrap></td>
      <td class="colColor1" nowrap></td>
      <td class="colColor2" nowrap></td>
      <td class="colColor1" nowrap></td>
      <?php
      if(!$config->enableSmfIntegration) {
      ?>
      <td class="colColor2" nowrap></td>
      <?php } ?>
    </tr>
  </table>
  <?php
}
?>
