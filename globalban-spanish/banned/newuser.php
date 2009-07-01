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

include_once(ROOTDIR."/include/database/class.UserQueries.php");
// User specific queries
$userQueries = new UserQueries();

// Make variables empty
$username = "";
$email = "";
$vemail = "";
$password = "";
$vpassword = "";
$steamId = "";

$nopost = true; // Flag of whether the form was submitted yet
if(isset($_POST['nopost'])) {
	$nopost = $_POST['nopost'];
}

// Boolean values of whether post values are valid
$valid = array("username"=>false,
        "steamId"=>false,
				"email"=>false,
				"vemail"=>false,
				"password"=>false,
				"vpassword"=>false,
        "userCode"=>false);

/**
 * Post Data from form
 */ 

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
	
	// take a given email address and split it into the username and domain.
	//list($userName, $mailDomain) = split("@", $email);
	// Check if the dns is valid
	//if(checkdnsrr($mailDomain, "MX")) {
	//	$valid['email'] = true;
	//}
	// Simplified version that does not do dns validation
	if(!$userQueries->emailExist($email) && !empty($email)) {
		if(preg_match("/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/i", $email)) {
			$valid['email'] = true;
		}
	}
}
if(isset($_POST['vemail'])) {
	$vemail = $_POST['vemail'];
	// Check if it matches the email address
	if($vemail == $email) {
		$valid['vemail'] = true;
	}
}


if(isset($_POST['password'])) {
	$password = $_POST['password'];
	
	// Must have atleast 1 alphanumeric and at least 1 number and be a length of at least 6
	$regex = "/^\w*(?=\w*\d)(?=\w*[a-zA-Z])\w*$/";
	
	if(strlen($password) > 5 && preg_match($regex,$password)) {
		$valid['password'] = true;
	}
}
if(isset($_POST['vpassword'])) {
	$vpassword = $_POST['vpassword'];
	// Check if it matches the first password
	if($vpassword == $password) {
		$valid['vpassword'] = true;
	}
}

// User name
if(isset($_POST['userCode'])) {
	$code = $_POST['userCode'];
	
  if(!empty($code)) {
  	if($config->createUserCode == $code || $config->createSuperCode == $code) {
      $valid['userCode'] = true;
    }
  }
}

// Redirect if everything works
if($valid['username'] && $valid['email'] && $valid['vemail'] && $valid['password'] && $valid['steamId'] && $valid['userCode']) {
  // Always add the user as a member
	if($userQueries->addUser($username, $password, 4, $steamId, $email)) {
	
    // Email the user
    $subject = $config->siteName." Ban Management (New User)";

    $body = "<html><body>";
    $body .= "You have successfully created your user: ".$username;
    $body .= "\n\n";

    $body .= "\n\n";
    $body .= "<p>You may start using your account immediately.</p>";
    $body .= "</body></html>";

    // To send HTML mail, the Content-type header must be set
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
    // Additional headers
    $headers .= "From: ".$config->siteName." Ban Management <".$config->emailFromHeader.">" . "\r\n";

    // Send an email message to those that wish to recieve a notice of a newly added ban
    mail($email, $subject, $body, $headers);
	
		header("Location: index.php?page=login&created=1"); // Requires ob_start and ob_flush to do header re-direct
	} else {
		header("Location: index.php?page=newuser&error=1");
	}
}

if($_GET['error'] == 1) {
  echo "<h5>There is a database error.  Admin IDs between the gban_admins and gban_admin_steam tables are not matching.</h5>";
}

?>
<div class="tborder">
  <div id="tableHead">
    <div><b>New User Creation</b></div>
  </div>
  <form action="index.php?page=newuser" method="post" id="form">
  	<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
  		<tr>
  			<td class="rowColor1">*Username:</td>
  			<td class="rowColor1"><input type="text" name="username" value="<?=$username?>" size="40" maxlength="40" />
  			<?php if(!$valid['username'] && !$nopost) { ?><span class="error">Username already taken</span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor2">*Steam ID:</td>			
  			<td class="rowColor2"><input name="steamId" id="steamdId" type="text" value="<?=$steamId?>" size="25" maxlength="25"/> (must be in <b>STEAM_X:X:XXXXXX</b> format)
  			<?php if(!$valid['steamId'] && !$nopost) { ?><span class="error">Steam ID not in vaild format</span><?php } ?></td>
  		</tr>
  			<td class="rowColor1">**Password:</td>
  			<td class="rowColor1"><input type="password" name="password" value="" size="25" maxlength="25"/>
  			<?php if(!$valid['password'] && !$nopost) { ?><span class="error">Enter a valid password</span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor2">**Verify Password:</td>
  			<td class="rowColor2"><input type="password" name="vpassword" value="" size="25" maxlength="25"/>
  			<?php if(!$valid['vpassword'] && !empty($password) && !$nopost) { ?><span class="error">Password mis-match</span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor1">*Email:</td>
  			<td class="rowColor1"><input type="text" name="email" value="<?php if(!empty($email)) { echo $email; } ?>" size="60" maxlength="80" />
  			<?php if(!$valid['email'] && !$nopost) { ?><span class="error">Enter a valid email</span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor2">*Verify Email:</td>
  			<td class="rowColor2"><input type="text" name="vemail" value=""  size="60" maxlength="80" />
  			<?php if(!$valid['vemail'] && !empty($email) && !$nopost) { ?><span class="error">Email mis-match</span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor1">*Create Code:</td>
  			<td class="rowColor1"><input type="password" name="userCode" value="" />
  			<?php if((!$valid['userCode'] && !empty($code) && !$nopost) || empty($code)) { ?><span class="error">Incorrect user creation code</span><?php } ?></td>
  		</tr>
  		<tr>			
  			<td align="left" colspan="3" class="rowColor2">
  				<input type="hidden" name="nopost" value="0" />
  				<input type="reset" value="Reset Form" class="button" />&nbsp;
  				<input type="submit" value="Submit" class="button" /></td>
  		</tr>
  	</table>
  </form>
</div>
<h5>
* Denotes a required field.<br />
** Passwords must contain at least 1 digit and be at least 6 characters in length.
</h5>

