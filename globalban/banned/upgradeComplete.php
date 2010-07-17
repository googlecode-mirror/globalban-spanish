<h1>Global Ban Upgrade Complete!</h1>
<p>The upgrade has completed successfully!</p>
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

// Delete install.php and upgrade.php files
if(!@unlink("install.php")) {
  echo "<p style='color:red;'>The install.php file could not be removed.  Please manually delete this file.</p>";
} else {
  echo "<p>The install.php file was successfully removed.</p>";
}
if(!@unlink("upgrade.php")) {
 echo "<p style='color:red;'>The upgrade.php file could not be removed.  Please manually delete this file.</h4>";
} else {
  echo "<p>The upgrade.php file was successfully removed.</p>";
}
?>

<p>Please be sure to re-save your configuration from the "Configuration" page.</p>
