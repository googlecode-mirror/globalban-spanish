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

define("ROOTDIR", dirname(__FILE__)); // Global Constant of root directory
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

<h4>The installation has completed successfully!</h4>

<?php
  if($_GET['fileError'] == "1") {
    echo "<h4 style='color:red;'>Failed to create the config/class.Config.php file.  You must create this file manually or correct file permissions on your web server and re-do the installation.  This also caused the super-user to not be created.  Install.php and upgrade.php were NOT removed!</h4>";
  } else {
    if(!@unlink("install.php")) {
      echo "<h4 style='color:red;'>The install.php file could not be removed.  Please manually delete this file.</h4>";
    } else {
      echo "<h4>The install.php file was successfully removed.</h4>";
    }
    if(!@unlink("upgrade.php")) {
     echo "<h4 style='color:red;'>The upgrade.php file could not be removed.  Please manually delete this file.</h4>";
    } else {
      echo "<h4>The upgrade.php file was successfully removed.</h4>";
    }
  }
?>

<h4>Please <a href="./index.php?page=login">login here</a>, go into the admin section and add all your servers.  After adding your servers, you MUST do a
"Save Configuration" from the "Configuration" page to update the config file on all your servers.</h4>


<div id="footer">
Developed by <a href="mailto:soynuts@unbuinc.net">Soynuts</a> of <a href="http://unbuinc.net">UNBU Inc.</a> &copy;2007
</div>
</div>

</body>
</html>
