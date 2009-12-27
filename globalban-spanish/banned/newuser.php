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

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_newuser.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_newuser.php");

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
    $subject = $config->siteName." ".$LAN_NEWUSER_025." ".$LAN_NEWUSER_026;

    $body = "<html><body>";
    $body .= $LAN_NEWUSER_022.": ".$username;
    $body .= "\n\n";

    $body .= "\n\n";
    $body .= "<p>".$LAN_NEWUSER_023."</p>";
    $body .= "</body></html>";

    // To send HTML mail, the Content-type header must be set
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
    // Additional headers
    $headers .= "From: ".$config->siteName." ".$LAN_NEWUSER_025." <".$config->emailFromHeader.">\r\n";

    // Send an email message to those that wish to recieve a notice of a newly added ban
    mail($email, $subject, $body, $headers);
	
		header("Location: index.php?page=login&created=1"); // Requires ob_start and ob_flush to do header re-direct
	} else {
		header("Location: index.php?page=newuser&error=1");
	}
}

if($_GET['error'] == 1) {
  echo "<h5>".$LAN_NEWUSER_002."</h5>";
}

?>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LAN_NEWUSER_001; ?></b></div>
  </div>
  <form action="index.php?page=newuser" method="post" id="form">
  	<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
  		<tr>
  			<td class="rowColor1">*<?php echo $LAN_NEWUSER_003; ?>:</td>
  			<td class="rowColor1"><input type="text" name="username" value="<?php echo $username?>" size="40" maxlength="40" />
  			<?php if(!$valid['username'] && !$nopost) { ?><span class="error"><?php echo $LAN_NEWUSER_004; ?></span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor2">*<?php echo $LAN_NEWUSER_005; ?>:</td>			
  			<td class="rowColor2"><input name="steamId" id="steamdId" type="text" value="<?php echo $steamId?>" size="25" maxlength="25"/> (<?php echo $LAN_NEWUSER_006; ?>)
  			<?php if(!$valid['steamId'] && !$nopost) { ?><span class="error"><?php echo $LAN_NEWUSER_007; ?></span><?php } ?></td>
  		</tr>
  			<td class="rowColor1">**<?php echo $LAN_NEWUSER_008; ?>:</td>
  			<td class="rowColor1"><input type="password" name="password" value="" size="25" maxlength="25"/>
  			<?php if(!$valid['password'] && !$nopost) { ?><span class="error"><?php echo $LAN_NEWUSER_009; ?></span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor2">**<?php echo $LAN_NEWUSER_010; ?>:</td>
  			<td class="rowColor2"><input type="password" name="vpassword" value="" size="25" maxlength="25"/>
  			<?php if(!$valid['vpassword'] && !empty($password) && !$nopost) { ?><span class="error"><?php echo $LAN_NEWUSER_011; ?></span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor1">*<?php echo $LAN_NEWUSER_012; ?>:</td>
  			<td class="rowColor1"><input type="text" name="email" value="<?php if(!empty($email)) { echo $email; } ?>" size="60" maxlength="80" />
  			<?php if(!$valid['email'] && !$nopost) { ?><span class="error"><?php echo $LAN_NEWUSER_013; ?></span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor2">*<?php echo $LAN_NEWUSER_014; ?>:</td>
  			<td class="rowColor2"><input type="text" name="vemail" value=""  size="60" maxlength="80" />
  			<?php if(!$valid['vemail'] && !empty($email) && !$nopost) { ?><span class="error"><?php echo $LAN_NEWUSER_015; ?></span><?php } ?></td>
  		</tr>
  		<tr>
  			<td class="rowColor1">*<?php echo $LAN_NEWUSER_016; ?>:</td>
  			<td class="rowColor1"><input type="password" name="userCode" value="" />
  			<?php
			if(isset($_POST['submit']))
			{
				if((!$valid['userCode'] && !empty($code) && !$nopost) || empty($code))
				{?>
					<span class="error"><?php echo $LAN_NEWUSER_017; ?> </span><?php
				}?></td>
			<?php
			}
			?>
  		</tr>
  		<tr>			
  			<td align="left" colspan="3" class="rowColor2">
  				<input type="hidden" name="nopost" value="0" />
  				<input type="reset" value="<?php echo $LAN_NEWUSER_021; ?>" class="button" />&nbsp;
  				<input type="submit" value="<?php echo $LAN_NEWUSER_018; ?>" class="button" /></td>
  		</tr>
  	</table>
  </form>
</div>
<h5>
* <?php echo $LAN_NEWUSER_019; ?><br />
** <?php echo $LAN_NEWUSER_020; ?>
</h5>

