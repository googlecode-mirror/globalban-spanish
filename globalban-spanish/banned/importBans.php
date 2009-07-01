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

include_once(ROOTDIR."/include/database/class.BanQueries.php");
include_once(ROOTDIR."/include/database/class.ServerQueries.php");
include_once(ROOTDIR."/include/database/class.ReasonQueries.php");

// Import bans from banned_users.cfg file
if($fullPower) {

$bansAdded = false;

$reasonQueries = new ReasonQueries();
  
// Get list of ban reasons
$banReasons = $reasonQueries->getReasonList();

// If this is set, then that means a server is being added
if(isset($_POST['submitImport'])) {  
  // Parse input and add bans
  //$bans = $_POST['bans'];
  $reason = $_POST['reason']; // Reason Id
  $tempName = $_FILES['cfgfile']['tmp_name']; // Temp name of when it is uploaded

  if(is_uploaded_file($tempName)) {
    $bans = "";
    $f = fopen($tempName, 'r' );
    while( $data = fread( $f, 4096 ) ) { $bans .= $data; }
    fclose( $f );

    echo $bans;

    $banLines = explode("\n", $bans);

    $banQueries = new BanQueries();
    $serverQueries = new ServerQueries();

    // Constant Variables
    $serverId = $serverQueries->getFirstServer(); // We need to assign it to a valid server for it to display
    $nameOfBanned = ""; // Name is unknown
    $banner = ""; // Don't know who banner is, so leave empty
    $count = 0;
    $failed = 0;

    foreach($banLines as $banLine) {
      $ban = explode(" ", $banLine);
      // 0 = banid (discard)
      // 1 = length
      // 2 = steamid

      $length = $ban[1]; // Length of ban in minutes
      $steamId = $ban[2]; // Steam ID of banned
      $timeScale = "minutes";

      $banId = -1;

      if($steamId != "") {

        if($length > 0) {

          $lengthInSec = $length*60; // Convert to seconds
          $expireDate = time() + $lengthInSec; // Expire date

          // Add the new ban non-perma ban
          $banId = $banQueries->addBan($steamId, $length, $timeScale, $expireDate, $reason, $banner, 0, $nameOfBanned, $serverId, null, '');
        } else {
          // Add perma ban
          $banId = $banQueries->addBan($steamId, $length, $timeScale, time(), $reason, $banner, 0, $nameOfBanned, $serverId, null, '');
        }

        if($banId > 0) {
          $count++;
        } else {
          $failed++;
        }
      }
    }

    $bansAdded = true;
  }
}

// The XML form was submitted
if(isset($_POST['submitXMLImport'])) {
  $reason = $_POST['reason']; // Reason Id
  $tempName = $_FILES['file']['tmp_name']; // Temp name of when it is uploaded
  $count = 0;
  $alreadyAdded = 0;
  $failed = 0;

  if(is_uploaded_file($tempName)) {
    $banQueries = new BanQueries();
  
    $xml = "";
    $f = fopen($tempName, 'r' );
    while( $data = fread( $f, 4096 ) ) { $xml .= $data; }
    fclose( $f );

    preg_match_all("/\<Ban\>(.*?)\<\/Ban\>/s", $xml, $banBlocks);

    foreach($banBlocks[1] as $block ) {
      preg_match_all( "/\<SteamID\>(.*?)\<\/SteamID\>/", $block, $steamId);
      preg_match_all( "/\<IP\>(.*?)\<\/IP\>/", $block, $ip);
      preg_match_all( "/\<Name\>(.*?)\<\/Name\>/", $block, $name);
      preg_match_all( "/\<Length\>(.*?)\<\/Length\>/", $block, $length);
      preg_match_all( "/\<TimeScale\>(.*?)\<\/TimeScale\>/", $block, $timeScale);
      preg_match_all( "/\<AddDate\>(.*?)\<\/AddDate\>/", $block, $addDate);
      preg_match_all( "/\<ExpireDate\>(.*?)\<\/ExpireDate\>/", $block, $expireDate);
      preg_match_all( "/\<Webpage\>(.*?)\<\/Webpage\>/", $block, $webpage);
      
      $id = $banQueries->importBan($steamId[1][0], $name[1][0], $length[1][0], $timeScale[1][0], $addDate[1][0], $expireDate[1][0], $webpage[1][0], $reason, $ip[1][0]);
      
      if($id > 0) {
        $count++;
      } else {
        $alreadyAdded++;
      }
    }
    
    $bansAdded = true;
  }
}

?>
<?php
// Display that the changes were successful
if($bansAdded) {
  ?><h5 class="error"><?=$count?> steam IDs imported to ban table.<br/>
  <?php
    if($alreadyAdded > 0) {
      echo $alreadyAdded . " of the imports already exist in the database.";
    }
    if($failed > 0) {
      echo "Failed to add ".$failed." bans as they already exist.";
    }
  ?>
  </h5><?php
}
?>
<div class="tborder">
  <div id="tableHead">
    <div><b>Import Bans</b</div>
  </div>
  <form action="index.php?page=importBans" method="POST" enctype="multipart/form-data">
  <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr class="rowColor2">
      <th style="text-align: left;">Import from banned_users.cfg</th>
      <th style="text-align: left;">Import from GlobalBan XML file</th>
    </tr>
    <tr class="rowColor1">
      <td>Locate the appropriate banned_users.cfg with the browse button below.</td>
      <td>Locate the appropriate banned_users.xml with the browse button below.</td>
    </tr>
    <tr class="rowColor2">
    <td width="1%" nowrap>Reason to apply to all imports:
      <select name="reason">
      <?php
      if(count($banReasons > 0)) {
        for($i=0; $i<count($banReasons);$i++) {
          $reason = $banReasons[$i];
          ?><option value="<?=$reason->getId()?>"><?=$reason->getReason()?></option><?php
        }
      } else {
      ?><option value="-1">Reason List Empty, Populate before proceeding</option><?php
      }
      ?>
      </select>
    </td>
    <td width="1%" nowrap>Reason to apply to all imports:
      <select name="reason">
      <?php
      if(count($banReasons > 0)) {
        for($i=0; $i<count($banReasons);$i++) {
          $reason = $banReasons[$i];
          ?><option value="<?=$reason->getId()?>"><?=$reason->getReason()?></option><?php
        }
      } else {
      ?><option value="-1">Reason List Empty, Populate before proceeding</option><?php
      }
      ?>
      </select>
    </td>
  </tr>
    <tr class="rowColor1">
      <td><input id="file" name="cfgfile" size="40" type="file" /></td>
      <td><input id="file" name="file" size="40" type="file" /></td>
    </tr>
    <tr class="rowColor2">
      <td><input type="submit" name="submitImport" value="Import CFG Bans"/></td>
      <td><input type="submit" name="submitXMLImport" value="Import XML Bans"/></td>
    </tr>
  </table>
  </form>
</div>
<br/>
<h5>Note: This process may take some time if the import is large.</h5>
<?php
}
?>
