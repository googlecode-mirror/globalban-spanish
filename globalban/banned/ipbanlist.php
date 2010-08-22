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
if(!empty($_GET['sr'])){
	$startRange = $_GET['sr']; // Start Range
}else{
	$startRange = 0;
}

if(!empty($_GET['sc'])){
	$sortBy = $_GET['sc']; // Start Range
}else{
	$sortBy = "ip";
}

if(!empty($_GET['sd'])){
	$sortDirection = $_GET['sd']; // Start Range
}else{
	$sortDirection = "DESC";
}

if(!empty($_POST['searchText'])){
	$searchText = $_POST['searchText']; // Start Range
}else{
	$searchText = "";
}

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_ipbanlist.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_ipbanlist.php");

$banQueries = new BanQueries();

// Count how many bans exist in the database
$ipCount = $banQueries->getNumberOfIpBans($banManager, $fullPower, $searchText);

$bannedIps = $banQueries->getIpBanList($banManager, $fullPower, $startRange, $ipCount, $sortBy, $sortDirection, $searchText);
?>
<div id="search" align="right">
  <form action="" method="post">
  <input name="searchText" id="searchText" type="text" value="<?php echo $searchText?>" size="40" maxLength="40"/>
  <input type="submit" value="<?php echo $LANIPBAN_011; ?>">
  </form>
  </div>
<?php

if(count($bannedIps) > 0) {
  ?>
  <script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
  <script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
    <script language="Javascript" type="text/javascript">
    rowHighlight("ipbanlistTable");
  </script>
 
  
  
  <div class="tborder">
    <div id="tableHead">
      <div><b><?php echo $LANIPBAN_001; ?><?php echo ($startRange+1) ?> <?php echo $LANIPBAN_002; ?> <?php echo $banQueries->getEndRange() ?> <?php echo $LANIPBAN_003; ?> <?php echo $ipCount ?></b></div>
      <div>
        <?php pageLinks($config, $startRange, $ipCount); ?>
      </div>
    </div>
    
    <div>
    <table id="ipbanlistTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    
    <tr>
      <th class="colColor1" nowrap>
        <a href="index.php?page=ipbanlist&sc=ip&sd=ASC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        <?php echo $LANIPBAN_004; ?>
        <a href="index.php?page=ipbanlist&sc=ip&sd=DESC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <?php
      // Show extra headers for ban manager
      if($banManager || $fullPower) {
        ?>
        <th class="colColor2" width="1%" nowrap>
          <a href="index.php?page=ipbanlist&sc=active&sd=ASC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
          <?php echo $LANIPBAN_005; ?>
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
        <td class="colColor1" nowrap><?php echo $bannedIp->getIp() ?></td>
        <?php
        // Show extra headers for ban manager
        if($banManager || $fullPower) {
          ?>
          <td id="active:<?php echo $bannedIp->getIp() ?>" class="colColor2" onclick="changeIpActiveStatus('<?php echo $bannedIp->getIp() ?>', <?php echo $bannedIp->getActive() ?>);" style="cursor:pointer;">
          <?php if($bannedIp->getActive() == 0) {
            ?><img src="images/cross.png"/><?php
          } else {
            ?><div align="center"><img src="images/tick.png"/></div><?php
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
  <h5><img src="images/bullet_star.png" /> <?php echo $LANIPBAN_006; ?></h5>
  <br/>
<?php
  // Only display if there are bans
  if(count($bannedIps) > 0) {
  ?>
  <div class="tborder">
    <div id="tableHead">
      <div><b><?php echo $LANIPBAN_007; ?></b></div>
    </div>
    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <form action="exportIps.php" method="post" id="form">
    	<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    		<tr>
    			<td align="left" class="rowColor2">
    				<input type="submit" name="submit" value="<?php echo $LANIPBAN_007; ?>" class="button" />
    			</td>
    		</tr>
    	</table>
    </form>
    </table>
  </div>
<?php
  }
} // End count
else {
?>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LANIPBAN_008; ?></b></div>
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
    ?><a href="index.php?page=ipbanlist&sr=<?php echo ($startRange-$config->bansPerPage) ?>">&lt;&lt;<?php echo $LANIPBAN_009; ?></a> <?php
    }

    // Show Middle Links
    $eitherside = (($config->maxPageLinks+1) * $config->bansPerPage);
    if($startRange+1 > $eitherside) {
      // Show first page
      if($currentPage == $page) {
        ?><a href="index.php?page=ipbanlist&sr=<?php echo $y?>"><b>[<?php echo $page?>]</b></a> <?php
      } else {
        ?><a href="index.php?page=ipbanlist&sr=<?php echo $y?>"><?php echo $page?></a> <?php
      }
      ?> ... <?php
    }

    while($y<$ipCount) {
      if(($y > ($startRange - $eitherside)) && ($y < ($startRange + $eitherside))) {
        if($currentPage == $page) {
          ?><a href="index.php?page=ipbanlist&sr=<?php echo $y?>"><b>[<?php echo $page?>]</b></a> <?php
        } else {
          ?><a href="index.php?page=ipbanlist&sr=<?php echo $y?>"><?php echo $page?></a> <?php
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
        ?><a href="index.php?page=ipbanlist&sr=<?php echo $y?>"><b>[<?php echo $page?>]</b></a> <?php
      } else {
        ?><a href="index.php?page=ipbanlist&sr=<?php echo $y?>"><?php echo $page?></a> <?php
      }
    }

    // Show next button
    if(($page-1 > ($startRange/$config->bansPerPage)+1 || $currentPage == 1) && $ipCount > $config->bansPerPage) {
    ?><a href="index.php?page=ipbanlist&sr=<?php echo ($startRange+$config->bansPerPage)?>"> <?php echo $LANIPBAN_010; ?>&gt;&gt;</a> <?php
    }
  }
}
?>
