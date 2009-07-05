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

require_once(ROOTDIR."/include/database/class.UserQueries.php"); // User specific queries
require_once(ROOTDIR."/include/objects/class.User.php"); // User class to store user info

$userQuery = new UserQueries;
$user = new User;

$error = false;
$emailError = true;

// Make variables empty
$username = "";
$password = "";
$remember = "";

// Post Data from form
if(isset($_POST['username'])) {
	$username = $_POST['username'];
}
if(isset($_POST['lpassword'])) {
	$password = $_POST['lpassword'];
}
if(isset($_POST['remember'])) {
	$remember = $_POST['remember'];
}

// User has entered a user name and password
if(!empty($username) && !empty($password)) {

	// Check if username and password is valid
	if($userQuery->verifyUser($username, $password)) {
		// Grab user information object
		$user = $userQuery->getUserInfo($username);
		
		if($remember) { // User wants to be remembered... set up cookies
     	 setcookie("gbu", $user->getName(), time()+60*60*24*100, "/"); // 100 days
     	 setcookie("gbp", $user->getPassword(), time()+60*60*24*100, "/"); // 100 days
		}
		
		// Store stuff into session (creates a valid session)
		$_SESSION['name'] = $user->getName();
   	    $_SESSION['password'] = $user->getPassword(); // password should already be md5 encrypted
		$_SESSION['accessLevel'] = $user->getAccessLevel(); // Level of access
		
		header("Location: index.php?page=banlist"); // Requires ob_start and ob_flush
	}
	else {
		$error = true;
	}
}

// User has submitted that they forgot their password
if(isset($_POST['forgotPassword'])) {
  if($userQuery->forgotPassword($_POST['email'])) {
    $emailError = false;
  } else {
    $emailError = true;
  }
}

if($_GET['created'] == 1) {
  ?><h5>New user successfully created.  You may now log in.</h5><?php
}

?>
<div class="tborder">
  <div id="tableHead">
    <div><b>Login</b></div>
  </div>
  <form action="index.php?page=login" method="post" id="form">
  	<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
  		<tr>
  			<td class="rowColor1" width="1%" nowrap>Usuario:</td>
  			<td class="rowColor1" ><input type="text" name="username" /></td>
  		</tr>
  		<tr>
  			<td class="rowColor2" width="1%" nowrap>Contrase&ntilde;a:</td>
  			<td class="rowColor2"><input type="password" name="lpassword" /></td>
  		</tr>
  		<tr>
  			<td colspan="2" align="left" class="rowColor1">Recordar sesion
  			  <input type="checkbox" name="remember" value="1" />
        <?php
  				if($error) {
  			?>
  			<span class="error">El usuario o la contrase&ntilde;a introducida son incorrectos.</span>
  			<?php
  				}
  			?>
        </td>
  		</tr>
  		<tr>			
		  <td align="left" colspan="2" class="rowColor2">
		  <input type="submit" name="login" value="Login" class="button" /></td>
  		</tr>
  	</table>
  </form>
</div>

<p><a href="index.php?page=newuser">Para Registrarse como nuevo admin, pulsa aqui</a></p>
<p>Si has olvidado tu contrase&ntilde;a, introduce tu email y se te mandara unna nueva?
<form action="index.php?page=login" method="post"> Email:<input type="text" name="email" size="60" maxlength="80"/>
<input type="submit" name="forgotPassword" value="Enviar nueva contraseña" class="button">
</form>
<?php
if(!$emailError && isset($_POST['forgotPassword'])) {
?>
<p class="error">Se te ha enviado un email con tu nueva contrase&ntilde;a.</p>
<?php
} else if($emailError && isset($_POST['forgotPassword'])) {
?>
<p class="error">No hay ningun administrador registrado con ese email que has introducido, revisa si era correcto.</p>
<?php
}
?>
</p>
