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

require_once(ROOTDIR."/include/database/class.InstallAndUpgradeQueries.php");
require_once(ROOTDIR."/config/class.Config.php");

$config = new Config();
$installAndUpgrade = new InstallAndUpgradeQueries();

$installAndUpgrade->setDbHost($config->dbHostName);
$installAndUpgrade->setDbUser($config->dbUserName);
$installAndUpgrade->setDbPass($config->dbPassword);
$installAndUpgrade->setDbase($config->dbName);

$complete = false;

if(isset($_POST['submit'])) {
  $installAndUpgrade->createConnection();
  $installAndUpgrade->connectToDatabase();
  
  $upgradeVersion = $_POST['upgradeVersion'];

  // ---------------------------------------------------------------------------
  // UPGRADE FROM 2.2 TO ANY 3.0+ VERSION
  // ---------------------------------------------------------------------------

  // Upgrade from Version 2.2 to Version 3.0
  if($upgradeVersion == "22to30") {
    $installAndUpgrade->upgradeTwoPointTwoToThree();
  }
  
  // Upgrade from Version 2.2 to Version 3.1
  if($upgradeVersion == "22to31") {
    $installAndUpgrade->upgradeTwoPointTwoToThree();
    $installAndUpgrade->upgradeThreeToThreePointOne();
  }
  
  // Upgrade from Version 2.2 to Version 3.2
  if($upgradeVersion == "22to32") {
    $installAndUpgrade->upgradeTwoPointTwoToThree();
    $installAndUpgrade->upgradeThreeToThreePointOne();
    // There are no database upgrades for 3.2
  }
  
  // Upgrade from Version 2.2 to Version 3.3
  if($upgradeVersion == "22to33") {
    $installAndUpgrade->upgradeTwoPointTwoToThree();
    $installAndUpgrade->upgradeThreeToThreePointOne();
    // There are no database upgrades for 3.2
    $installAndUpgrade->upgradeThreePointTwoToThreePointThree();
  }
  
  // Upgrade from Version 2.2 to Version 3.4
  if($upgradeVersion == "22to34") {
    $installAndUpgrade->upgradeTwoPointTwoToThree();
    $installAndUpgrade->upgradeThreeToThreePointOne();
    // There are no database upgrades for 3.2
    $installAndUpgrade->upgradeThreePointTwoToThreePointThree();
    $installAndUpgrade->upgradeThreePointThreeToThreePointFour();
  }
  
  // ---------------------------------------------------------------------------
  // UPGRADE FROM 3.0 TO ANY 3.1+ VERSION
  // ---------------------------------------------------------------------------
  
  // Upgrade from Version 3.0 to Version 3.1
  if($upgradeVersion == "30to31") {
    $installAndUpgrade->upgradeThreeToThreePointOne();
  }
  
  // Upgrade from Version 3.0 to Version 3.2
  if($upgradeVersion == "30to32") {
    $installAndUpgrade->upgradeThreeToThreePointOne();
    // There are no database upgrades for 3.2
  }
  
  // Upgrade from Version 3.0 to Version 3.3
  if($upgradeVersion == "30to33") {
    $installAndUpgrade->upgradeThreeToThreePointOne();
    // There are no database upgrades for 3.2
    $installAndUpgrade->upgradeThreePointTwoToThreePointThree();
  }
  
  // Upgrade from Version 3.0 to Version 3.4
  if($upgradeVersion == "30to34") {
    $installAndUpgrade->upgradeThreeToThreePointOne();
    // There are no database upgrades for 3.2
    $installAndUpgrade->upgradeThreePointTwoToThreePointThree();
    $installAndUpgrade->upgradeThreePointThreeToThreePointFour();
  }
  
  // ---------------------------------------------------------------------------
  // UPGRADE FROM 3.1 TO ANY 3.2+ VERSION
  // ---------------------------------------------------------------------------
  
  // Upgrade from Version 3.1 to Version 3.2
  if($upgradeVersion == "31to32") {
    // There are no database upgrades for 3.2
    // This is here for consistency reasons
  }
  
  // Upgrade from Version 3.1 to Version 3.3
  if($upgradeVersion == "31to33") {
    $installAndUpgrade->upgradeThreePointTwoToThreePointThree();
  }
  
  // Upgrade from Version 3.1 to Version 3.4
  if($upgradeVersion == "31to34") {
    $installAndUpgrade->upgradeThreePointTwoToThreePointThree();
    $installAndUpgrade->upgradeThreePointThreeToThreePointFour();
  }
  
  // ---------------------------------------------------------------------------
  // UPGRADE FROM 3.2 TO ANY 3.3+ VERSION
  // ---------------------------------------------------------------------------
  
  // Upgrade from Version 3.2 to Version 3.3
  if($upgradeVersion == "32to33") {
    $installAndUpgrade->upgradeThreePointTwoToThreePointThree();
  }
  
  // Upgrade from Version 3.2 to Version 3.4
  if($upgradeVersion == "32to4") {
    $installAndUpgrade->upgradeThreePointTwoToThreePointThree();
    $installAndUpgrade->upgradeThreePointThreeToThreePointFour();
  }
  
  // ---------------------------------------------------------------------------
  // UPGRADE FROM 3.3 TO ANY 3.4+ VERSION
  // ---------------------------------------------------------------------------
  
  // Upgrade from Version 3.3 to Version 3.4
  if($upgradeVersion == "33to34") {
    $installAndUpgrade->upgradeThreePointThreeToThreePointFour();
  }
  
  // ---------------------------------------------------------------------------
  // UPGRADE FROM 3.4.X TO ANY 3.4.1 r90+ VERSION
  // ---------------------------------------------------------------------------
  if($upgradeVersion == "34to341r90") {
    $installAndUpgrade->upgradeThreePointFourToOdonelPointOne();
  }
  
  $complete = true;
}
?>


<?php
/*************************************************
 * Start upgrade form
 *************************************************/
if(!$complete) {
?>
<h1>Global Ban Upgrade</h1>
<p>Both the install.php and upgrade.php files will be deleted after a successful upgrade!</p>
<p>Select the upgrade type you wish to perform!</p>

<form action="upgrade.php" method="post" onsubmit="return confirm('Have you selected the appropriate upgrade option?');">
  Upgrade Type: <select name="upgradeVersion">
                  <option value="22to30">2.2 to 3.0</option>
                  <option value="22to31">2.2 to 3.1</option>
                  <option value="22to32">2.2 to 3.2</option>
                  <option value="22to33">2.2 to 3.3</option>
                  <option value="22to34">2.2 to 3.4</option>
                  <option value="30to31">3.0 to 3.1</option>
                  <option value="30to32">3.0 to 3.2</option>
                  <option value="30to33">3.0 to 3.3</option>
                  <option value="30to34">3.0 to 3.4</option>
                  <option value="31to32">3.1 to 3.2</option>
                  <option value="31to33">3.1 to 3.3</option>
                  <option value="31to34">3.1 to 3.4</option>
                  <option value="32to33">3.2 to 3.3</option>
                  <option value="32to34">3.2 to 3.4</option>
                  <option value="33to34">3.3 to 3.4</option>
                  <option value="34to341r90" selected>3.4.1 to 3.4.1 r90</option>
                </select>
  <input type="submit" name="submit" value="Upgrade!">
</form>

<?php
}
/*************************************************
 * End upgrade form
 *************************************************/
?>


<?php
/*************************************************
 * Upgrade COMPLETE
 *************************************************/
if($complete) {
?>
<script type="text/javascript">
window.location = "upgradeComplete.php?version=<?php echo $upgradeVersion?>"
</script>
<?php
}
/*************************************************
 * End Upgrade COMPLETE
 *************************************************/
?>
