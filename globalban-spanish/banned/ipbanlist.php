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
include_once(ROOTDIR."/include/objects/class.BannedUser.php");

// Page gets (for range and sorts and other things)
$startRange = $_GET['sr']; // Start Range
$sortBy = $_GET['sc']; // Column to sort by
$sortDirection = $_GET['sd']; // Direction to sort by
$searchText = $_POST['searchText']; // Search text

if(empty($startRange)) {
  $startRange = 0;
}
if(empty($sortBy)) {
  $sortBy = "ip";
}
if(empty($sortDirection)) {
  $sortDirection = "DESC";
}

$banQueries = new BanQueries();

// Count how many bans exist in the database
$ipCount = $banQueries->getNumberOfIpBans($banManager, $fullPower, $searchText);

$bannedIps = $banQueries->getIpBanList($banManager, $fullPower, $startRange, $ipCount, $sortBy, $sortDirection, $searchText);

if(count($bannedIps) > 0) {
  ?>
  <script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
  <script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
    <script language="Javascript" type="text/javascript">
    rowHighlight("ipbanlistTable");
  </script>
  
  
  <div id="search" align="right">
  <form action="" method="post">
  <input name="searchText" id="searchText" type="text" value="" size="40" maxLength="40"/>
  <input type="submit" value="Search">
  </form>
  </div>
  
  <div class="tborder">
    <div id="tableHead">
      <div><b>IP Ban List showing IP bans <?=($startRange+1)?> to <?=$banQueries->getEndRange()?> of <?=$ipCount?></b></div>
      <div>
        <?php pageLinks($config, $startRange, $ipCount); ?>
      </div>
    </div>
    
    <div>
    <table id="ipbanlistTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    
    <tr>
      <th class="colColor1" nowrap>
        <a href="index.php?page=ipbanlist&sc=ip&sd=ASC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        IP Address
        <a href="index.php?page=ipbanlist&sc=ip&sd=DESC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <?php
      // Show extra headers for ban manager
      if($banManager || $fullPower) {
        ?>
        <th class="colColor2" width="1%" nowrap>
          <a href="index.php?page=ipbanlist&sc=active&sd=ASC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
          Active
          <a href="index.php?page=ipbanlist&sc=active&sd=DESC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
        </th>
        <?php
      }
      ?>
    </tr>
    <?php
    // Loop through banned users and display them
    foreach($bannedIps as $bannedIp) {

      ?>
      <tr>
        <td class="colColor1" nowrap><?=$bannedIp->getIp()?></td>
        <?php
        // Show extra headers for ban manager
        if($banManager || $fullPower) {
          ?>
          <td id="active:<?=$bannedIp->getIp()?>" class="colColor2" onclick="changeIpActiveStatus('<?=$bannedIp->getIp()?>', <?=$bannedIp->getActive()?>);" style="cursor:pointer;">
          <?php if($bannedIp->getActive() == 0) {
            ?><img src="images/cross.png"/><?php
          } else {
            ?><img src="images/tick.png"/><?php
          } ?>
          </td>
          <?php
        }
        ?>
      </tr>
      <?php
    } // End for loop
        
    ?>
    </table>
    </div>
    
    <div id="tableBottom">
      <div>
        <?php pageLinks($config, $startRange, $ipCount); ?>
      </div>
    </div>
  </div>
  <h5>*NOTE: IP Bans apply to ALL servers on the server list page.</h5>
  <br/>
<?php
  // Only display if there are bans
  if(count($bannedIps) > 0) {
  ?>
  <div class="tborder">
    <div id="tableHead">
      <div><b>Download IP Bans</b</div>
    </div>
    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <form action="exportIps.php" method="post" id="form">
    	<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    		<tr>
    			<td align="left" class="rowColor2">
    				<input type="submit" name="submit" value="Download IP List" class="button" /></td>
    		</tr>
    </table>
  </div>
<?php
  }
} // End count
else {
?>
<div class="tborder">
  <div id="tableHead">
    <div><b>IP List is Empty</b></div>
  </div>
</div>
<?php
}

function pageLinks($config, $startRange, $ipCount) {
  if($config->bansPerPage > 0) {

    $y=0;
    $page = 1; // Starting page
    $currentPage = ($startRange/$config->bansPerPage)+1; // Page we are currently on

    // Show previous button
    if($currentPage != 1) {
    ?><a href="index.php?page=ipbanlist&sr=<?=($startRange-$config->bansPerPage)?>">&lt;&lt;Previous</a> <?php
    }

    // Show Middle Links
    $eitherside = (($config->maxPageLinks+1) * $config->bansPerPage);
    if($startRange+1 > $eitherside) {
      // Show first page
      if($currentPage == $page) {
        ?><a href="index.php?page=ipbanlist&sr=<?=$y?>"><b>[<?=$page?>]</b></a> <?php
      } else {
        ?><a href="index.php?page=ipbanlist&sr=<?=$y?>"><?=$page?></a> <?php
      }
      ?> ... <?php
    }

    while($y<$ipCount) {
      if(($y > ($startRange - $eitherside)) && ($y < ($startRange + $eitherside))) {
        if($currentPage == $page) {
          ?><a href="index.php?page=ipbanlist&sr=<?=$y?>"><b>[<?=$page?>]</b></a> <?php
        } else {
          ?><a href="index.php?page=ipbanlist&sr=<?=$y?>"><?=$page?></a> <?php
        }
      }
      $page++;
      $y+=$config->bansPerPage;
      $lastPage = $y;
    }
    if(($startRange+$eitherside)<$ipCount) {
      ?> ... <?php

      // Undo last iteration for showing last page
      $page--;
      $y-=$config->bansPerPage;

      // Show last page
      if($y == $lastPage && ($startRange+$eitherside)<$ipCount) {
        ?><a href="index.php?page=ipbanlist&sr=<?=$y?>"><b>[<?=$page?>]</b></a> <?php
      } else {
        ?><a href="index.php?page=ipbanlist&sr=<?=$y?>"><?=$page?></a> <?php
      }
    }

    // Show next button
    if(($page-1 > ($startRange/$config->bansPerPage)+1 || $currentPage == 1) && $ipCount > $config->bansPerPage) {
    ?><a href="index.php?page=ipbanlist&sr=<?=($startRange+$config->bansPerPage)?>"> Next&gt;&gt;</a> <?php
    }
  }
}
?>
