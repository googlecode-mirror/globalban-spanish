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


$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_importBans.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_importBans.php");

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

   

    $banLines = explode("\n", $bans);

    $banQueries = new BanQueries();
    $serverQueries = new ServerQueries();

    // Constant Variables
    $serverId = $serverQueries->getFirstServer(); // We need to assign it to a valid server for it to display
    $nameOfBanned = ""; // Name is unknown
    $banner = ""; // Don't know who banner is, so leave empty
    $count = 0;
    $failed = 0;
    $failedIDs = "";

    foreach($banLines as $banLine) {
      echo $banLine."<br>";
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
          $failedIDs .= " - ".$LAN_IMPORTBANS_015.$steamId."<br>";
        }
      }
    }

    $bansAdded = true;
  }
}

// The XML form was submitted
if(isset($_POST['submitXMLImport'])) {
  $reason = $_POST['reasonXML']; // Reason Id
  $reason = $_POST['reason']; // Reason Id
  $tempName = $_FILES['file']['tmp_name']; // Temp name of when it is uploaded
  $count = 0;
  $alreadyAdded = 0;
  $alreadyDs = "";
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
        $alreadyDs .= " - ".$LAN_IMPORTBANS_015.$steamId."<br>";
      }
    }
    
    $bansAdded = true;
  }
}

?>
<?php
// Display that the changes were successful
if($bansAdded) {
  ?><h5 class="error"><?php echo $count ?> <?php echo $LAN_IMPORTBANS_001 ?><br/>
  <?php
    if($alreadyAdded > 0) {
      echo $alreadyAdded . $LAN_IMPORTBANS_012 . "<br/><br/>";
    }
    if($failed > 0) {
      echo $LAN_IMPORTBANS_013 . $failed.$LAN_IMPORTBANS_014 . "<br/><br/>";
    }
  ?>
  </h5><?php
  
    echo $alreadyIDs;
    echo $failedIDs;        
}
?>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LAN_IMPORTBANS_002 ?></b></div>
  </div>
  <form action="index.php?page=importBans" method="POST" enctype="multipart/form-data">
  <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr class="rowColor2">
      <th style="text-align: left;"><?php echo $LAN_IMPORTBANS_003 ?></th>
      <th style="text-align: left;"><?php echo $LAN_IMPORTBANS_004 ?></th>
    </tr>
    <tr class="rowColor1">
      <td><?php echo $LAN_IMPORTBANS_005 ?></td>
      <td><?php echo $LAN_IMPORTBANS_006 ?></td>
    </tr>
    <tr class="rowColor2">
    <td width="1%" nowrap><?php echo $LAN_IMPORTBANS_007 ?>
      <select name="reason">
      <?php
      if(count($banReasons > 0)) {
        for($i=0; $i<count($banReasons);$i++) {
          $reason = $banReasons[$i];
          ?><option value="<?php echo $reason->getId() ?>"><?php echo $reason->getReason() ?></option><?php
        }
      } else {
      ?><option value="-1"><?php echo $LAN_IMPORTBANS_008 ?></option><?php
      }
      ?>
      </select>
    </td>
    <td width="1%" nowrap><?php echo $LAN_IMPORTBANS_007 ?>
      <select name="reasonXML">
      <?php
      if(count($banReasons > 0)) {
        for($i=0; $i<count($banReasons);$i++) {
          $reason = $banReasons[$i];
          ?><option value="<?php echo $reason->getId() ?>"><?php echo $reason->getReason() ?></option><?php
        }
      } else {
      ?><option value="-1"><?php echo $LAN_IMPORTBANS_008 ?></option><?php
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
      <td><input type="submit" name="submitImport" value="<?php echo $LAN_IMPORTBANS_009 ?>"/></td>
      <td><input type="submit" name="submitXMLImport" value="<?php echo $LAN_IMPORTBANS_010 ?>"/></td>
    </tr>
  </table>
  </form>
</div>
<br/>
<h5><?php echo $LAN_IMPORTBANS_011 ?></h5>
<?php
}
?>
