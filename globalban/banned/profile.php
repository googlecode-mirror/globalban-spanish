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
require_once(ROOTDIR."/include/objects/class.User.php");

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_profile.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_profile.php");

$userQueries = new UserQueries();

$user = $userQueries->getUserInfo($_SESSION['name']); // Get current logged in user's info

// Boolean values of whether post values are valid
$valid = array("username"=>true,
        "steamId"=>true,
				"email"=>true,
				"curPassword"=>true,
				"cpassword"=>true,
				"npassword"=>true,
				"vpassword"=>true);

// *********************************************
// If the user is updating their general profile
// *********************************************
if(!empty($_POST['generalProfile']) && isset($_POST['generalProfile'])) {
  $generalChangesMade = false;
  $generalErrors = false;
  
  $username = $_POST['username'];
  // Check if user name was changed
  if($user->getName() != addslashes($username)) {
    // Determine if NEW username already exists    
  	if(!$userQueries->usernameExist($username) && !empty($username)) {
  		$valid['username'] = true;
  		$generalChangesMade = true; // A change has been made
  		// Update username
  		$user->setName($username);
  	} else {
      $valid['username'] = false;
      $generalErrors = true;
    }
	}
	
	// Steam ID
	$steamId = $_POST['steamId'];
	if($user->getSteamId() != $steamId) {
	 if(preg_match("/^STEAM_[01]:[01]:\d{0,10}$/", $steamId)) {
		  $valid['steamId'] = true;
		  $generalChangesMade = true; // A change has been made
		  // Update Steam ID
		  $user->setSteamId($steamId);
		} else {
      $valid['steamId'] = false;
      $generalErrors = true;
    }
	}
  
  // Email
	$email = $_POST['email'];	
  // Email changed and password correct
	if($user->getEmail() != $email && $user->getPassword() == md5($_POST['curPassword'])) {
	  
  	// Simplified version that does not do dns validation
		if(preg_match("/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/i", $email)) {
			$valid['email'] = true;
      $valid['curPassword'] = true;
      $generalChangesMade = true;
      // Everything is valid for an email change, set the changes  
  		$user->setEmail($email);
		} else {
        $valid['email'] = false;
        $generalErrors = true;
    }
	} else if($user->getEmail() == $email) {
    // If email isn't being changed then this can be valid
    $valid['curPassword'] = true;
  } else if($user->getEmail() != $email && $user->getPassword() != md5($_POST['curPassword'])) {
    // Email changed but password incorrect
    $valid['curPassword'] = false;
    $generalErrors = true;
    // Email was correct but password still wrong
    if(preg_match("/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/i", $email)) {
			$valid['email'] = true;
		} else {
      $valid['email'] = false;
    }			
  }
	
	// Save changes to the database as long as everything is valid
	if($generalChangesMade && $valid['username'] && $valid['steamId'] && $valid['email'] && $valid['curPassword']) {
    // DB save
    $userQueries->updateUser($user);
  	// Need to update cookie and session values if username is updated
  	$_SESSION['name'] = $user->getName();
  	setcookie("gbu", $user->getName(), time()+60*60*24*100, "/"); // 100 days  	
  }
}

// *********************************************
// If the user is updating their password
// *********************************************
if(!empty($_POST['updatePassword']) && isset($_POST['updatePassword'])) {

  // Current password check
  if($user->getPassword() == md5($_POST['cpassword'])) {
    $valid['cpassword'] = true;
  } else {
    $valid['cpassword'] = false;
  }
  
  // New Password check
	$newpassword = $_POST['npassword'];	
	// Must have atleast 1 alphanumeric and at least 1 number and be a length of at least 6
	$regex = "/^\w*(?=\w*\d)(?=\w*[a-zA-Z])\w*$/";
	
	if(strlen($newpassword) > 5 && preg_match($regex,$newpassword)) {
		$valid['npassword'] = true;
	} else {
    $valid['npassword'] = false;
  }
	
	// New Password verification check
	$vpassword = $_POST['vpassword'];
	// Check if it matches the first password
	if($vpassword == $newpassword) {
		$valid['vpassword'] = true;
	} else {
    $valid['vpassword'] = false;
  }

  // Save changes to the database as long as everything is valid
	if($valid['cpassword'] && $valid['npassword'] && $valid['vpassword']) {
    // DB save
    $passwordChangesMade = true;
    $user->setPassword(md5($newpassword)); // Need to md5 the new password
    $userQueries->updateUser($user);
  	// Need to update cookie and session values if username is updated
  	$_SESSION['password'] = $user->getPassword(); // password should already be md5 encrypted
  	setcookie("gbp", $user->getPassword(), time()+60*60*24*100, "/"); // 100 days
  }
}
?>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LAN_PROFILE_001; ?></b></div>
  </div>
  <form action="index.php?page=profile" method="post" id="form">
  	<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
  		<tr>
  			<td class="rowColor1" width="1%" nowrap><?php echo $LAN_PROFILE_002; ?></td>
  			<td class="rowColor1"><input type="text" name="username" value="<?php echo $user->getName() ?>" size="40" maxlength="40" />
  			<?php if(!$valid['username']) { ?><span class="error"><?php echo $LAN_PROFILE_003; ?></span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor2" width="1%" nowrap><?php echo $LAN_PROFILE_004; ?></td>
  			<td class="rowColor2"><input name="steamId" id="steamdId" type="text" value="<?php echo $user->getSteamId() ?>" size="25" maxlength="25"/> <?php echo $LAN_PROFILE_005; ?>
  			<?php if(!$valid['steamId']) { ?><span class="error"><?php echo $LAN_PROFILE_006; ?></span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor1" width="1%" nowrap><?php echo $LAN_PROFILE_007; ?></td>
  			<td class="rowColor1"><input type="text" name="email" size="60" maxlength="80" value="<?php echo $user->getEmail() ?>" />
  			<?php if(!$valid['email']) { ?><span class="error"><?php echo $LAN_PROFILE_008; ?></span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor2" width="1%" nowrap><img src="images/bullet_star.png"/> <?php echo $LAN_PROFILE_009 ?></td>
  			<td class="rowColor2"><input type="password" name="curPassword" value="" size="25" maxlength="25"/>
  			<?php if(!$valid['curPassword']) { ?><span class="error"><?php echo $LAN_PROFILE_010; ?></span><?php } else { ?><span><?php echo $LAN_PROFILE_014; ?></span><?php } ?></td>
  		</tr>
  		<tr>			
  			<td align="left" colspan="3" class="rowColor1">
  				<input type="submit" value="<?php echo $LAN_PROFILE_011; ?>" name="generalProfile" class="button" /></td>
  		</tr>
  	</table>
  </form>
</div>
<?php
// Display that the changes were successful
if(!empty($generalChangesMade)) {
	if ($generalChangesMade) {
  ?><h5 class="error"><?php echo $LAN_PROFILE_012; ?></h5><?php
	}
}
?>
<?php
// Display an error message if there was any bad input
if(!empty($generalErrors)) {
	if ($generalErrors) {
  ?><h5 class="error"><?php echo $LAN_PROFILE_013; ?></h5><?php
  }
}
?>
<br/>
<br/>
<br/>
<br/>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LAN_PROFILE_015; ?></b></div>
  </div>
  <form action="index.php?page=profile" method="post" id="form">
  	<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
      	<tr>
  			<td class="rowColor1" width="1%" nowrap><?php echo $LAN_PROFILE_016; ?></td>
  			<td class="rowColor1"><input type="password" name="cpassword" value="" size="25" maxlength="25"/>
  			<?php if(!$valid['cpassword']) { ?><span class="error"><?php echo $LAN_PROFILE_017; ?></span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor2" width="1%" nowrap><?php echo $LAN_PROFILE_018; ?></td>
  			<td class="rowColor2"><input type="password" name="npassword" value="" size="25" maxlength="25"/>
  			<?php if(!$valid['npassword']) { ?><span class="error"><?php echo $LAN_PROFILE_019; ?></span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor1" width="1%" nowrap><?php echo $LAN_PROFILE_020; ?></td>
  			<td class="rowColor1"><input type="password" name="vpassword" value="" size="25" maxlength="25"/>
  			<?php if(!$valid['vpassword']) { ?><span class="error"><?php echo $LAN_PROFILE_021; ?></span><?php } ?></td>
  		</tr>
  		<tr>			
  			<td align="left" colspan="3" class="rowColor2">
  				<input type="hidden" name="nopostpass" value="0" />
  				<input type="submit" value="<?php echo $LAN_PROFILE_022; ?>" name="updatePassword" class="button" /></td>
  		</tr>
    </table>
  </form>
</div>

<h5>
<?php echo $LAN_PROFILE_023; ?>

</h5>

<?php
// Display that the changes were successful
if(!empty($passwordChangesMade)) {
	if($passwordChangesMade) {
  ?><h5 class="error"><?php echo $LAN_PROFILE_024; ?></h5><?php
	}
}
?>
