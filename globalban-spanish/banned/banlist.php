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
include_once(ROOTDIR."/include/database/class.ReasonQueries.php");
include_once(ROOTDIR."/include/objects/class.BannedUser.php");
include_once(ROOTDIR."/include/objects/class.AdminStats.php");
include_once(ROOTDIR."/include/objects/class.ReasonStats.php");
include_once(ROOTDIR."/include/objects/class.Length.php");

// Page gets (for range and sorts and other things)
$startRange = $_GET['sr']; // Start Range
$sortBy = $_GET['sc']; // Column to sort by
$sortDirection = $_GET['sd']; // Direction to sort by
$searchText = $_GET['searchText']; // Search text
$reasonSortBy = $_GET['rsc'];
$reasonSortDirection = $_GET['rsd'];
$reasonSearchText = $_GET['rst'];
$adminSortBy = $_GET['asc'];
$adminSortDirection = $_GET['asd'];
$adminSearchText = $_GET['ast'];
$bansFilter = $_GET['bf'];
$bansReason_id = $_GET['bri'];
$bansAdmin = $_GET['ba'];

if(empty($startRange)) {
  $startRange = 0;
}
if(empty($sortBy)) {
  $sortBy = "add_date";
}
if(empty($sortDirection)) {
  $sortDirection = "DESC";
}

// $LANGUAGE = "Spanish";
$LANGUAGE = "English";

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_banlist.php';
include_once(file_exists($lan_file) ? $lan_file : "languages/English/lan_banlist.php");

$banQueries = new BanQueries();

// Ban delete process
if($fullPower) {
  // A full power admin executed a ban delete
  if($_GET['process'] == "delete") {
    if($_GET['steamId'] != null || $_GET['steamId'] != "") {
      $banQueries->deleteBan($_GET['steamId']);
    }
  }
}

if(empty($bansFilter)) {
	$bansFilter = "";
}
if(empty($bansReason_id)) {
	$bansReason_id = "";
}
if(empty($bansAdmin)) {
	$bansAdmin = "";
}

// Count how many bans exist in the database
$banCount = $banQueries->getNumberOfBans($member, $admin, $banManager, $fullPower, $searchText, $bansFilter, $bansReason_id, $bansAdmin);

$bannedUsers = $banQueries->getBanList($member, $admin, $banManager, $fullPower, $startRange, $banCount, $sortBy, $sortDirection, $searchText, $bansFilter, $bansReason_id, $bansAdmin);

if(empty($reasonSortBy)) {
	$reasonSortBy = "NumPermanentes";
}
if(empty($reasonSortDirection)) {
	$reasonSortDirection = "DESC";
}
if(empty($reasonSearchText)) {
	$reasonSearchText = "";
}
if(empty($adminSortBy)) {
	$adminSortBy = "NumPermanentes";
}
if(empty($adminSortDirection)) {
	$adminSortDirection = "DESC";
}
if(empty($adminSearchText)) {
	$adminSearchText = "";
}

$reasonStats = $banQueries->getReasonStats($reasonSortBy, $reasonSortDirection, $reasonSearchText);

$adminStats = $banQueries->getAminStats($adminSortBy, $adminSortDirection, $adminSearchText);

$endRange = $banQueries->getEndRange();

if($endRange > $banCount) {
  $endRange = $banCount;
}
?>
  <script language="javascript" type="text/javascript">
    $(document).ready(function() {
      // For all "active" cells assign an onclick method
      $("td[id^='active-']").click(function() {
        var checkboxId = $(this).attr("id");
        var banId = checkboxId.replace("active-", "");
        var img = $("#activeImg-"+banId).attr("src");
        var active = 0;
        if(img == "images/tick.png") {
          active = 1;
        }
        $.post("index.php?page=changeActiveStatus&ajax=1", {id: banId, active: active}, 
          function(data) {
            if(active == 1) {
              $("#activeImg-"+banId).attr("src", "images/cross.png");
            } else {
              $("#activeImg-"+banId).attr("src", "images/tick.png");
            }
          });
      });
    });
  </script>
  <script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
  <style type="text/css">
<!--
.bannedPreviously {
	color: #C4DE07;
	font-style: italic;
	font-weight: bold;
}
.longSelect {
	color: #D5D39F;
	font-weight: bold;
	font-size: 15px;
}
.adminSelect {
	color: #33CC33;
	font-weight: bold;
	font-size: 15px;
}
.reasonSelect {
	color: #CC9900;
	font-weight: bold;
	font-size: 15px;
}
.kickCounter {
	color: #CC9900;
	font-weight: bold;
}
-->
  </style>
  
  
  <div id="search" align="right">
  <form action="" method="get">
  <input name="searchText" id="searchText" type="text" value="<?=$searchText?>" size="40" maxLength="40"/>
  <input type="hidden" name="bf" size=2 value="<?=$bansFilter?>">
  <input type="hidden" name="bri" size=2 value="<?=$bansReason_id?>"> 
  <input type="hidden" name="ba" size=2 value="<?=$bansAdmin?>"> 
  <input type="submit" value="<?=LANINS_001?>">
  </form>
  </div>

<?php
if(count($bannedUsers) > 0) {
  ?>
  <div class="tborder">
    <div id="tableHead">
      <div><b><?=LANINS_002?>
<?php
  if(!empty($bansFilter)) {
	  switch ($bansFilter) {
	    case 1:
			?>
			 <span class="longSelect"> <?=LANINS_003?> </span>
			<?php
	        break;
	    case 2:
			?>
			 <span class="longSelect"> <?=LANINS_004?> </span>
			<?php
	        break;
	    case 3:
			?>
			 <span class="longSelect"> <?=LANINS_005?> </span>
			<?php
	        break;
		case 4:
			?>
			 <span class="longSelect"> <?=LANINS_006?> </span>
			<?php
	        break;
	  }
  } 
?>
  <?=LANINS_007?>
<?php
  if(!empty($bansAdmin)) {
?>
  <?=LANINS_008?> <span class="adminSelect"><?=$bansAdmin?></span> 
<?php
  }
  if(!empty($bansReason_id)) {
    $reasonQueries = new ReasonQueries();
?>
  <?=LANINS_009?> <span class="reasonSelect"><?=$reasonQueries->getReason($bansReason_id);?></span> 
<?php
  }
?>
 <?=LANINS_010.number_format(($startRange+1), 0, ",", ".")." ".LANINS_011.number_format($endRange, 0, ",", ".")." ".LANINS_012.number_format($banCount, 0, ",", ".")?></b></div>
      <div>
        <?php pageLinks($config, $startRange, $banCount, $sortDirection, $sortBy, $searchText, $bansFilter, $bansReason_id, $bansAdmin); ?>
      </div>
    </div>
    
    <div>
    <table id="banlistTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    
    <tr>
      <th class="colColor1" width="1%" nowrap>
        <div align="center"><a href="/index.php?page=banlist&amp;sc=b.steam_id&amp;sd=ASC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_up.png" style="cursor:pointer;"/></a>
          <?=LANINS_013?>
        <a href="/index.php?page=banlist&amp;sc=b.steam_id&amp;sd=DESC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
      <th class="colColor2" width="1%" nowrap>
        <div align="center"><a href="/index.php?page=banlist&amp;sc=name&amp;sd=ASC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_up.png" style="cursor:pointer;"/></a>
          <?=LANINS_014?>
        <a href="/index.php?page=banlist&amp;sc=name&amp;sd=DESC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
	  <th class="colColor1" width="1%" nowrap>
        <div align="center"><a href="/index.php?page=banlist&amp;sc=length&amp;sd=ASC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_up.png" style="cursor:pointer;"/></a>
          <?=LANINS_016?>
        <a href="/index.php?page=banlist&amp;sc=length&amp;sd=DESC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
      <th class="colColor2" width="1%" nowrap><div align="center"><a href="/index.php?page=banlist&amp;sc=banner&amp;sd=ASC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_up.png" style="cursor:pointer;"/></a><?=LANINS_016?>
        <a href="/index.php?page=banlist&amp;sc=banner&amp;sd=DESC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
      <th class="colColor1" width="1%" nowrap>
        <div align="center"><a href="/index.php?page=banlist&amp;sc=add_date&amp;sd=ASC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_up.png" style="cursor:pointer;"/></a>
          <?=LANINS_018?>
        <a href="/index.php?page=banlist&amp;sc=add_date&amp;sd=DESC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
      <th class="colColor2" width="1%" nowrap>
        <div align="center"><a href="/index.php?page=banlist&amp;sc=b.reason_id&amp;sd=ASC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_up.png"/></a>
          <?=LANINS_020?>
        <a href="/index.php?page=banlist&amp;sc=b.reason_id&amp;sd=DESC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_down.png"/></a> </div></th>
      <th class="colColor1" width="1%" nowrap>
        <div align="center">Post</div></th>
      <?php
      // Show extra headers for ban manager
      if($member || $admin || $banManager || $fullPower) {
        ?>
        <th class="colColor2" width="1%" nowrap>
          <div align="center"><a href="/index.php?page=banlist&amp;sc=active&amp;sd=ASC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_up.png" style="cursor:pointer;"/></a>
		  <?=LANINS_021?>
          <a href="/index.php?page=banlist&amp;sc=active&amp;sd=DESC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
        <th class="colColor1" width="1%" nowrap>
          <div align="center"><a href="/index.php?page=banlist&amp;sc=pending&amp;sd=ASC&amp;sr=<?=$startRange?>&amp;bf=<?=$bansFilter?>&amp;bri=<?=$bansReason_id?>&amp;ba=<?=$bansAdmin?>&amp;searchText=<?=$searchText?>"><img src="/images/arrow_up.png" style="cursor:pointer;"/></a>
          <?=LANINS_022?>
          <a href="index.php?page=banlist&sc=pending&sd=DESC&sr=<?=$startRange?>&bf=<?=$bansFilter?>&bri=<?=$bansReason_id?>&ba=<?=$bansAdmin?>&searchText=<?=$searchText?>"><img src="/images/arrow_down.png" style="cursor:pointer;"/>        </div></th>
      	<?php
		if($fullPower) {
          ?>
		  <th class="colColor2" width="1%" nowrap>
        <div align="center"><?=LANINS_037?></div></th>
		<?php
      	}
      	?>
        <?php
      }
      ?>
    </tr>
    <?php
    // Loop through banned users and display them
    foreach($bannedUsers as $bannedUser) {
      
      $length = "";
      $expireDate = $bannedUser->getExpireDate();
      $expireTime = "";
      if($expireDate != 'Expired') {
        list($expireDate, $expireTime) = split(' ', $bannedUser->getExpireDate());
      } else {
        $expireDate = "<i>".LANINS_023."</i>";
      }
      list($addDate, $addTime, $year) = split(' ', $bannedUser->getAddDate());
      $comments = str_replace(array("\r\n", "\n", "\r"), "<br/>", $bannedUser->getComments()); // Convert newlines into html line breaks
      $comments = str_replace('"', "&#34;", $comments); // Replace quotes with the HTML code
      
      $banLength = new Length();
      $banLength->setLength($bannedUser->getLength());
      $banLength->setTimeScale($bannedUser->getTimeScale());
      
      if($bannedUser->getLength() == 0) {
        $expireDate = LANINS_024;
        $expireTime = "";
      }
      
      $length = $banLength->getReadable();
      
      $information = "<div class='tborder'>";
      $information .= "<div id='tableHead'>";
      $information .= "<div style='color:#FFFFFF'><b><?=LANINS_026?></b></div>";
      $information .= "</div>";
      $information .= "<table class='bordercolor' width='100%'' cellspacing='1' cellpadding='5' border='0' style='margin-top: 1px;'>";
      $information .= "<tr class='rowColor1'><td>".LANINS_013.":</td><td>".$bannedUser->getSteamId()."</td></tr>";
      $information .= "<tr class='rowColor2'><td>".LANINS_014.":</td><td>".str_replace('"', "&#34;", $bannedUser->getName())."</td></tr>";
      $information .= "<tr class='rowColor1'><td>".LANINS_016.":</td><td>".$length."</td></tr>";
      $information .= "<tr class='rowColor2'><td>".LANINS_017.":</td><td>".str_replace('"', "&#34;", $bannedUser->getBanner())."</td></tr>";
      $information .= "<tr class='rowColor1'><td>".LANINS_018.":</td><td>".$bannedUser->getAddDate()."</td></tr>";
      $information .= "<tr class='rowColor2'><td>".LANINS_019.":</td><td>".$expireDate." ".$expireTime."</td></tr>";
      $information .= "<tr class='rowColor1'><td>".LANINS_020.":</td><td>".$bannedUser->getReason()."</td></tr>";
      if($bannedUser->getServerId() != -1) {
        $information .= "<tr class='rowColor2'><td>".LANINS_029.":</td><td>".str_replace('"', "&#34;", $bannedUser->getServer())."</td></tr>";
      } else {
        $information .= "<tr class='rowColor2'><td>".LANINS_029.":</td><td><a href='".str_replace('"', "&#34;", $bannedUser->getServer())."'>Import Server</a></td></tr>";
      }
      $information .= "<tr class='rowColor2'><td>".LANINS_025.":</td><td>".str_replace('"', "&#34;", $bannedUser->getOffenses())."</td></tr>";
      $information .= "<tr class='rowColor1'><td>".LANINS_030.":</td>";
      $information .= "<td>";
      if($bannedUser->getDemoCount() > 0) {
        $information .= "<a href='index.php?page=demos&searchText=".$bannedUser->getSteamId()."'><b>".LANINS_035." (".$bannedUser->getDemoCount().")</b></a>";
      } else {
        $information .= "<b>".$bannedUser->getDemoCount()." ".LANINS_030."</b>";
      }
      $information .= "</td></tr>";
      $information .= "<tr class='rowColor2'><td valign='top'>".LANINS_028.":</td><td>".$bannedUser->getKickCounter()."</td></tr>";
      $information .= "<tr class='rowColor1'><td valign='top'>".LANINS_027.":</td><td>".$comments."</td></tr>";
      $information .= "</table>";
      $information .= "</div>";

      $information = addslashes($information);
      

      $information2 = "<div class='tborder'>";
      $information2 .= "<div id='tableHead'>";
      $information2 .= "<div style='color:#FFFFFF'><b><?=LANINS_031?></b></div>";
      $information2 .= "</div>";
      $information2 .= "<table class='bordercolor' width='800px' cellspacing='1' cellpadding='5' border='0' style='margin-top: 1px;'>";

	  $information2 .= "<tr>";
	  $information2 .= " <th class='colColor1' align='center'>".LANINS_014."</th>";
	  $information2 .= " <th class='colColor1' align='center'>".LANINS_020."</th>";
	  $information2 .= " <th class='colColor2' width='1%' nowrap align='center'>".LANINS_016."</th>";
	  $information2 .= " <th class='colColor1' width='1%' nowrap align='center'>".LANINS_017."</th>";
	  $information2 .= " <th class='colColor2' width='1%' nowrap align='center'>".LANINS_018."</th>";
	  $information2 .= " <th class='colColor1' width='1%' nowrap align='center'>".LANINS_019."</th>";
	  $information2 .= " <th class='colColor2' align='center'>".LANINS_032."</th>";
	  $information2 .= " <th class='colColor1' align='center'>".LANINS_027."</th>";
	  $information2 .= "</tr>";
      
	  // Ban history of the user
	  $banHistory = $banQueries->getBanHistory($bannedUser->getBanId());

	  // Loop through banned users and display them
	  foreach($banHistory as $banHistUser) {
	      list($expireDateHist, $expireTimeHist) = split(' ', $banHistUser->getExpireDate());
	      list($addDateHist, $addTimeHist, $yearHist) = split(' ', $banHistUser->getAddDate());
	      $commentsHist = str_replace(array("\r\n", "\n", "\r"), "<br/>", $banHistUser->getComments()); // Convert newlines into html line breaks
	
	      $banLengthHist = new Length();
	      $banLengthHist->setLength($banHistUser->getLength());
	      $banLengthHist->setTimeScale($banHistUser->getTimeScale());
	
	      if($banHistUser->getLength() == 0) {
	        $expireDateHist = LANINS_033;
	        $expireTimeHist = "";
	      }

      	  if($banHistUser->getExpireDate() == 'Expired') {
	        $expireDateHist = "<i>".LANINS_024."</i>";
			$expireTimeHist = "";
    	  }

		  $information2 .= " <tr>";
		  $information2 .= "  <td class='colColor1' nowrap align='center'>".$banHistUser->getName()."</td>";
		  $information2 .= "  <td class='colColor1' nowrap align='center'>".$banHistUser->getReason()."</td>";
		  $information2 .= "  <td class='colColor2' nowrap align='center'>".$banLengthHist->getReadable()."</td>";
		  $information2 .= "  <td class='colColor1' nowrap align='center'>".$banHistUser->getBanner()."</td>";
		  $information2 .= "  <td class='colColor2' nowrap align='center'>".$addDateHist." ".$addTimeHist."</td>";
		  $information2 .= "  <td class='colColor1' nowrap align='center'>".$expireDateHist." ".$expireTimeHist."</td>";

		  if($banHistUser->getWebpage() != "") {
		    $information2 .= "<td class='rowColor2' align='center'><a href='".$banHistUser->getWebpage()."'><img src='images/database_add.png' align='absmiddle'/></a></td>";
		  } else {
		    $information2 .= "<td class='rowColor2' align='center'>Sin Post</td>";
		  }

		  $information2 .= "  <td class='colColor1'>".$commentsHist."</td>";
		  $information2 .= " </tr>";
	  }

      $information2 .= "</table>";
      $information2 .= "</div>";


      $information2 = addslashes($information2);

	  $steamArray = explode(':',str_replace(array("\t"," "), "", $bannedUser->getSteamId()));
      $linkprofile = "http://steamcommunity.com/profiles/".bcadd((($steamArray[2]*2)+$steamArray[1]),'76561197960265728');
      ?>
      <tr>
        <td class="colColor1" nowrap>
        <img src="images/information.png" style="cursor:help" onmouseover="Tip('<?=$information?>', WIDTH, 300, SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, OFFSETX, -20, CLOSEBTN, true, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'))">
		&nbsp;<a href='<?=$configOdonel->HLstatsUrl?>hlstats.php?mode=search&q=<?=str_replace(array("\t"," "), "", $bannedUser->getSteamId())?>&st=uniqueid&game=css'><img src='images/hxce.png' align='absmiddle'/></a>
		&nbsp;<a href='<?=$linkprofile?>'><img src='images/steam.png' align='absmiddle'/></a>
		&nbsp;
		<?php
          // Fullpower admins and Ban Mangers can modify ALL bans
          // Members and Amdins can only edit their own bans (which is matched by either banner name or banner steam id)
          if($fullPower || $banManager || 
            (($bannedUser->getBanner() == $_SESSION['name'] && !empty($_SESSION['name'])) && ($admin || $member)) || 
            (($bannedUser->getBannerSteamId() == $_SESSION['steamId'] && !empty($_SESSION['steamId'])) && ($admin || $member))) {
            ?><a href="index.php?page=updateBan&banId=<?=$bannedUser->getBanId()?>"><?=str_replace(array("\t"," "), "", $bannedUser->getSteamId())?></a><?php
          } else {
        ?>
		<?=str_replace(array("\t"," "), "", $bannedUser->getSteamId())?>
		<?php
		 }  
        ?>
		</td>
		<td class="colColor2" nowrap>
        <?php

		  // Player name with link to the demo
		  if($bannedUser->getDemoCount() > 0) {
          	?><a href='index.php?page=demos&searchText=<?=$bannedUser->getSteamId()?>#_demos'><b><?=$bannedUser->getName()?> (<?=$bannedUser->getDemoCount()?> demo)</b></a><?php
          } else {
            	?><?=str_replace(array("\"","\r\n", "\n", "\r"), "&quot;", $bannedUser->getName())?><?php
          }
          if($bannedUser->getComments() != null || $bannedUser->getComments() != "") {
            ?>&nbsp;<img src="images/information.png" style="cursor:help" onmouseover="Tip('<?=LANINS_027?>:<br/><?=str_replace(array("\r\n", "\n", "\r"), "<br/>", $bannedUser->getComments())?>', WIDTH, 300, SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, OFFSETX, -20, CLOSEBTN, false, CLICKCLOSE, false, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'))"><?php
          }  
          if($bannedUser->getKickCounter() > 0) {
            ?>&nbsp;<span class="kickCounter">(<?=$bannedUser->getKickCounter()?>)</span><?php
          }


		  // Advise Player Banned previously
		  if($bannedUser->getOffenses() == 1) {
		  		$bannedPreviously = " (1 ".LANINS_036.")";
		  } else if($bannedUser->getOffenses() > 1) {
		  		$bannedPreviously = " (".$bannedUser->getOffenses()." ".LANINS_025.")";
		  } else {
            	$bannedPreviously ="";
          }
		  if($bannedUser->getOffenses() > 0) {
			?><span class="bannedPreviously"><?=$bannedPreviously?></span><img src="images/information.png" style="cursor:help" onmouseover="Tip('<?=$information2?>', WIDTH, 800, SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, OFFSETX, -20, CLOSEBTN, true, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'))"><?php
		  }
		?>
		</td>
        <td class="colColor1" nowrap><div align="center"><?=$length?></div></td>
        <td class="colColor2" nowrap><div align="center"><?=$bannedUser->getBanner()?></div></td>
		<?php
          if($bannedUser->getLength() != 0) {
            ?><td class="colColor1" nowrap onmouseover="Tip('Hasta:<br/><?=$expireDate." ".$expireTime?>', WIDTH, 150, SHADOW, true, FADEIN, 150, FADEOUT, 150, STICKY, 1, OFFSETX, -20, CLOSEBTN, false, CLICKCLOSE, false, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'))"><?php
          } else {
            ?><td class="colColor1" nowrap><?php
          }
        ?>
        <div align="center"><?=$bannedUser->getAddDate()?></div></td>
        <td class="colColor2" nowrap><div align="center"><?=$bannedUser->getReason()?></div></td>
		<td class="colColor1"><div align="center">
			<?php if($bannedUser->getWebpage() != "") {
			?>
			<a href="<?=$bannedUser->getWebpage()?>" style="cursor:pointer;"><img src="images/database_add.png" align="absmiddle"/></a>
			<?php
			} else {
            ?><img src="images/cross.png" align="absmiddle"/><?php
			}
			?></div></td>
		<?
        // Show extra headers for ban manager
        if($banManager || $fullPower) {
          ?>
          <td id="active-<?=$bannedUser->getBanId()?>" class="colColor2" style="cursor:pointer;"
              onmouseover="if(<?=$bannedUser->getActive()?> == 0) { Tip('<?=LANINS_039?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}else{Tip('<?=LANINS_038?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}"><div align="center">
          <?php if($bannedUser->getActive() == 0) {
            ?><img id="activeImg-<?=$bannedUser->getBanId()?>" src="images/cross.png"/><?php
          } else {
            ?><img id="activeImg-<?=$bannedUser->getBanId()?>" src="images/tick.png"/><?php
          } ?>          </div></td>
          <td id="pending:<?=$bannedUser->getBanId()?>" class="colColor1"
              onclick="changePendingStatus(<?=$bannedUser->getBanId()?>, <?=$bannedUser->getPending()?>)" style="cursor:pointer;"
              onmouseover="if(<?=$bannedUser->getPending()?> == 0) { Tip('<?=LANINS_040?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}else{Tip('C<?=LANINS_041?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}"><div align="center">
          <?php if($bannedUser->getPending() == 0) {
            ?><img id="pendingImg-<?=$bannedUser->getBanId()?>" src="images/cross.png"/><?php
          } else {
            ?><img id="pendingImg-<?=$bannedUser->getBanId()?>" src="images/hourglass.png"/><?php
          } ?>          </div></td>
          <?php
        } else if($member || $admin) {
          // Show the status to members and admins, but they can not update it
          // They can however update it if they were the ones to ban
          if((($bannedUser->getBanner() == $_SESSION['name'] && !empty($_SESSION['name'])) && ($admin || $member)) || 
            (($bannedUser->getBannerSteamId() == $_SESSION['steamId'] && !empty($_SESSION['steamId'])) && ($admin || $member))) {
            ?><td id="active:<?=$bannedUser->getBanId()?>" class="colColor2"
                  onclick="changeActiveStatus(<?=$bannedUser->getBanId()?>, <?=$bannedUser->getActive()?>);" style="cursor:pointer;"
                  onmouseover="if(<?=$bannedUser->getActive()?> == 0) { Tip('<?=LANINS_039?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}else{Tip('<?=LANINS_038?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}"><div align="center"><?php
          } else {
          ?><td id="active:<?=$bannedUser->getBanId()?>" class="colColor2"><div align="center"><?php
          }
          if($bannedUser->getActive() == 0) {
            ?><img src="images/cross.png" align="absmiddle"/><?php
          } else {
            ?><img src="images/tick.png" align="absmiddle"/><?php
          } ?>
          </td>
          <td id="pending:<?=$bannedUser->getBanId()?>" class="colColor1">
          <?php if($bannedUser->getPending() == 0) {
            ?><img src="images/cross.png" align="absmiddle"/><?php
          } else {
            ?><div align="center"><img src="images/hourglass.png"/></div><?php
          } ?>          </div></td>
        <?php
        }
        ?>
		<?php
		if($fullPower) {
          ?>
		  <td class="colColor2"><div align="center"><a href="index.php?page=banlist&process=delete&steamId=<?=$bannedUser->getSteamId()?>" style="cursor:pointer;">
            <img src="images/trash-full.png" align="absmiddle"/></a></div></td>
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
        <?php pageLinks($config, $startRange, $banCount, $sortDirection, $sortBy, $searchText, $bansFilter, $bansReason_id, $bansAdmin); ?>
      </div>
  </div>
</div>
<h5><?=LANINS_042?></h5>
<br/>
<?php
	$banCountVigentes = $banQueries->getNumberOfBans($member, $admin, $banManager, $fullPower, "" , "4", "", "");
  ?>
		
	  <div class="tborder">
		<div id="tableHead">
	      <div><b><?=LANINS_043?></b></div>
	    </div>
	    <div>
		<table id="banlistTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
	    <tr>
	      <th class="colColor1" width="20%" nowrap>
	        <div align="center"><a href="index.php?page=banlist&rsc=Motivo&rsd=DESC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
	          <?=LANINS_044?>
	        <a href="index.php?page=banlist&rsc=Motivo&rsd=ASC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
	      <th class="colColor2" width="20%" nowrap COLSPAN=2>
	        <div align="center"><a href="index.php?page=banlist&rsc=NumPermanentes&rsd=DESC"><img src="images/arrow_up.png"/></a>
	          <?=LANINS_003?>
	        <a href="index.php?page=banlist&rsc=NumPermanentes&rsd=ASC"><img src="images/arrow_down.png"/></a> </div></th>
	      <th class="colColor1" width="20%" nowrap COLSPAN=2>
	        <div align="center"><a href="index.php?page=banlist&rsc=NumCumpliendose&rsd=DESC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
	          <?=LANINS_004?>
	        <a href="index.php?page=banlist&rsc=NumCumpliendose&rsd=ASC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
		  <th class="colColor2" width="20%" nowrap COLSPAN=2>
	        <div align="center"><a href="index.php?page=banlist&rsc=NumCumplidos&rsd=DESC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
	          <?=LANINS_005?>
	        <a href="index.php?page=banlist&rsc=NumCumplidos&rsd=ASC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
	      <th class="colColor1" width="20%" nowrap COLSPAN=3>
	        <div align="center"><a href="index.php?page=banlist&rsc=NumBaneados&rsd=DESC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
	          <?=LANINS_045?>
	        <a href="index.php?page=banlist&rsc=NumBaneados&rsd=ASC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
	    </tr>
	
	    <?php
		$i = 0;
		$SumPermanentes=0;
		$SumCumpliendose=0;
		$SumCumplidos=0;
	    // Loop through reason stats and display them
	    foreach($reasonStats as $reasonStat) {
				$i += 1;
				$SumPermanentes+=$reasonStat->getNumPermanentes();
                $SumCumpliendose+=$reasonStat->getNumCumpliendose();
                $SumCumplidos+=$reasonStat->getNumCumplidos();
				?>
			    <tr>
			      <td class="colColor1" width="20%" nowrap>
			        <div align="left">
			          <?=$i.".- ".$reasonStat->getMotivo()?>
					</div></td>
			      <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=1&bri=<?=$reasonStat->getMotivo_id()?>"><?=number_format($reasonStat->getNumPermanentes(), 0, ",", ".")?></a>
			         </div></td>
			      <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($reasonStat->getNumPermanentes()/$reasonStat->getNumBaneados()*100, 0, ",", ".")." %"?>
			         </div></td>
			      <td class="colColor1" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=2&bri=<?=$reasonStat->getMotivo_id()?>"><?=number_format($reasonStat->getNumCumpliendose(), 0, ",", ".")?>
			        </div></td>
				  <td class="colColor1" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($reasonStat->getNumCumpliendose()/$reasonStat->getNumBaneados()*100, 0, ",", ".")." %"?>
			        </div></td>
				  <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=3&bri=<?=$reasonStat->getMotivo_id()?>"><?=number_format($reasonStat->getNumCumplidos(), 0, ",", ".")?>
			        </div></td>
				  <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($reasonStat->getNumCumplidos()/$reasonStat->getNumBaneados()*100, 0, ",", ".")." %"?>
			        </div></td>
			      <td class="colColor1" width="7%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bri=<?=$reasonStat->getMotivo_id()?>"><?=number_format($reasonStat->getNumBaneados(), 0, ",", ".")?>
			        </div></td>
			      <td class="colColor1" width="7%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=4&bri=<?=$reasonStat->getMotivo_id()?>"><?=number_format(($reasonStat->getNumCumpliendose()+$reasonStat->getNumPermanentes()), 0, ",", ".")?>
			        </div></td>
				  <td class="colColor1" width="6%" nowrap>
			        <div align="right">
			          <?=number_format(($reasonStat->getNumCumpliendose()+$reasonStat->getNumPermanentes())/$banCountVigentes*100, 2, ",", ".")." %"?>
			        </div></td>

			    </tr>
	      		<?php
	    } // End for loop
	    
			$SumBaneados=$SumPermanentes+$SumCumpliendose+$SumCumplidos;
			$SumVigentes=$SumPermanentes+$SumCumpliendose;
	    ?>
			    <tr>
			      <td class="colColor1" width="20%" nowrap>
			        <div align="center">
			          TOTAL
					</div></td>
			      <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=1"><?=number_format($SumPermanentes, 0, ",", ".")?></a> 
			         </div></td>
			      <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($SumPermanentes/$SumBaneados*100, 0, ",", ".")." %"?>
			         </div></td>
			      <td class="colColor1" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=2"><?=number_format($SumCumpliendose, 0, ",", ".")?></a> 
			        </div></td>
				  <td class="colColor1" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($SumCumpliendose/$SumBaneados*100, 0, ",", ".")." %"?>
			        </div></td>
				  <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=3"><?=number_format($SumCumplidos, 0, ",", ".")?></a> 
			        </div></td>
				  <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($SumCumplidos/$SumBaneados*100, 0, ",", ".")." %"?>
			        </div></td>
			      <td class="colColor1" width="7%" nowrap>
			        <div align="right">
			          <?=number_format($SumBaneados, 0, ",", ".")?>
			        </div></td>
			      <td class="colColor1" width="7%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=4"><?=number_format($SumVigentes, 0, ",", ".")?></a> 
			        </div></td>
			      <td class="colColor1" width="6%" nowrap>
			        <div align="right">
			          <?=number_format($SumVigentes/$SumBaneados*100, 0, ",", ".")." %"?>
			        </div></td>
			    </tr>
		</table>
	    </div>
	  </div>
  <br/>
  <?php
			// ==========> Estadisticas Baneados Segun Administradores <===========
  ?>
	  <div class="tborder">
		<div id="tableHead">
	      <div><b><?=LANINS_046?></b></div>
	    </div>
	    <div>
		<table id="banlistTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
	    <tr>
	      <th class="colColor1" width="20%" nowrap>
	        <div align="center"><a href="index.php?page=banlist&asc=Admin&asd=DESC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
	          <?=LANINS_017?>
	        <a href="index.php?page=banlist&asc=Admin&asd=ASC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
	      <th class="colColor2" width="20%" nowrap COLSPAN=2>
	        <div align="center"><a href="index.php?page=banlist&asc=NumPermanentes&asd=DESC"><img src="images/arrow_up.png"/></a>
	          <?=LANINS_003?>
	        <a href="index.php?page=banlist&asc=NumPermanentes&asd=ASC"><img src="images/arrow_down.png"/></a> </div></th>
	      <th class="colColor1" width="20%" nowrap COLSPAN=2>
	        <div align="center"><a href="index.php?page=banlist&asc=NumCumpliendose&asd=DESC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
	          <?=LANINS_004?>
	        <a href="index.php?page=banlist&asc=NumCumpliendose&asd=ASC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
		  <th class="colColor2" width="20%" nowrap COLSPAN=2>
	        <div align="center"><a href="index.php?page=banlist&asc=NumCumplidos&asd=DESC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
	          <?=LANINS_005?>
	        <a href="index.php?page=banlist&asc=NumCumplidos&asd=ASC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
	      <th class="colColor1" width="20%" nowrap COLSPAN=3>
	        <div align="center"><a href="index.php?page=banlist&asc=NumBaneados&asd=DESC"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
	          <?=LANINS_045?>
	        <a href="index.php?page=banlist&asc=NumBaneados&asd=ASC"><img src="images/arrow_down.png" style="cursor:pointer;"/></a> </div></th>
	    </tr>
	
	    <?php
		$i=0;
		$SumPermanentes=0;
		$SumCumpliendose=0;
		$SumCumplidos=0;

	    // Loop through reason stats and display them
	    foreach($adminStats as $adminStat) {
			$i += 1;
			$SumPermanentes+=$adminStat->getNumPermanentes();
			$SumCumpliendose+=$adminStat->getNumCumpliendose();
			$SumCumplidos+=$adminStat->getNumCumplidos();
				?>
			    <tr>
			      <td class="colColor1" width="20%" nowrap>
			        <div align="left">
			          <?=$i.".- ".$adminStat->getAdmin()?>
					</div></td>
			      <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=1&ba=<?=$adminStat->getAdmin()?>"><?=number_format($adminStat->getNumPermanentes(), 0, ",", ".")?></a> 
			         </div></td>
			      <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($adminStat->getNumPermanentes()/$adminStat->getNumBaneados()*100, 0, ",", ".")." %"?>
			         </div></td>
			      <td class="colColor1" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=2&ba=<?=$adminStat->getAdmin()?>"><?=number_format($adminStat->getNumCumpliendose(), 0, ",", ".")?></a> 
			        </div></td>
				  <td class="colColor1" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($adminStat->getNumCumpliendose()/$adminStat->getNumBaneados()*100, 0, ",", ".")." %"?>
			        </div></td>
				  <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=3&ba=<?=$adminStat->getAdmin()?>"><?=number_format($adminStat->getNumCumplidos(), 0, ",", ".")?></a> 
			        </div></td>
				  <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($adminStat->getNumCumplidos()/$adminStat->getNumBaneados()*100, 0, ",", ".")." %"?>
			        </div></td>
			      <td class="colColor1" width="7%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&ba=<?=$adminStat->getAdmin()?>"><?=number_format($adminStat->getNumBaneados(), 0, ",", ".")?></a> 
			        </div></td>
			      <td class="colColor1" width="7%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=4&ba=<?=$adminStat->getAdmin()?>"><?=number_format(($adminStat->getNumCumpliendose()+$adminStat->getNumPermanentes()), 0, ",", ".")?></a> 
			        </div></td>
			      <td class="colColor1" width="6%" nowrap>
			        <div align="right">
			          <?=number_format(($adminStat->getNumCumpliendose()+$adminStat->getNumPermanentes())/$banCountVigentes*100, 2, ",", ".")." %"?>
			        </div></td>
			    </tr>
	      		<?php
	    } // End for loop
			$SumBaneados=$SumPermanentes+$SumCumpliendose+$SumCumplidos;
			$SumVigentes=$SumPermanentes+$SumCumpliendose;
	    ?>
			    <tr>
			      <td class="colColor1" width="20%" nowrap>
			        <div align="center">
			          <?=LANINS_047?>
					</div></td>
			      <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=1"><?=number_format($SumPermanentes, 0, ",", ".")?></a> 
			         </div></td>
			      <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($SumPermanentes/$SumBaneados*100, 0, ",", ".")." %"?>
			         </div></td>
			      <td class="colColor1" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=2"><?=number_format($SumCumpliendose, 0, ",", ".")?></a> 
			        </div></td>
				  <td class="colColor1" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($SumCumpliendose/$SumBaneados*100, 0, ",", ".")." %"?>
			        </div></td>
				  <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=3"><?=number_format($SumCumplidos, 0, ",", ".")?></a> 
			        </div></td>
				  <td class="colColor2" width="10%" nowrap>
			        <div align="right">
			          <?=number_format($SumCumplidos/$SumBaneados*100, 0, ",", ".")." %"?>
			        </div></td>
			      <td class="colColor1" width="7%" nowrap>
			        <div align="right">
			          <?=number_format($SumBaneados, 0, ",", ".")?>
			        </div></td>
			      <td class="colColor1" width="7%" nowrap>
			        <div align="right">
			          <a href="index.php?page=banlist&bf=4"><?=number_format($SumVigentes, 0, ",", ".")?></a> 
			        </div></td>
			      <td class="colColor1" width="6%" nowrap>
			        <div align="right">
			          <?=number_format($SumVigentes/$SumBaneados*100, 0, ",", ".")." %"?>
			        </div></td>
			    </tr>

		</table>
	    </div>
	  </div>
	<?php
	 // }
	?>
  <br/> 
<?php
  // Only display if there are bans
  if(count($bannedUsers) > 0) {
  ?>
  <script type="text/javascript">
  function formVerify() {

    if(document.getElementById("permaBans").checked || document.getElementById("allBans").checked) {
      return true;
    }

    alert(LANINS_048);
    return false;
  }
  </script>
  <div class="tborder">
    <div id="tableHead">
      <div><b><?=LANINS_049?></b></div>
    </div>
    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <form action="exportBans.php" method="post" id="form" onsubmit="return formVerify();">
    	<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
        <tr>
          <td align="left" class="rowColor1">
            <input type="checkbox" id="permaBans" name="permaBans" value="1" checked/> <?=LANINS_050?>
            <input type="checkbox" id="allBans" name="allBans" value="1"/> <?=LANINS_051?>
            <input type="checkbox" id="demosOnly" name="demosOnly" value="1"/> <?=LANINS_052?>
            <input type="checkbox" id="saveSQL" name="saveSQL" value="1"/> <?=LANINS_053?>
          </td>
        </tr>
    		<tr>
    			<td align="left" class="rowColor2">
    				<input type="submit" name="submit" value="<?=LANINS_054?>" class="button" /></td>
    		</tr>
    </table>
  </div>
<?php
  }
} // End count banned users
else {
?>
<div class="tborder">
  <div id="tableHead">
    <div><b><?=LANINS_055?></b></div>
  </div>
</div>
<?php
}

function pageLinks($config, $startRange, $banCount, $sortDirection, $sortBy, $searchText, $bansFilter, $bansReason_id, $bansAdmin) {
  if($config->bansPerPage > 0) {

    $y=0;
    $page = 1; // Starting page
    $currentPage = ($startRange/$config->bansPerPage)+1; // Page we are currently on

    // Show previous button
    if($currentPage != 1) {
    ?><a href="index.php?page=banlist&sr=<?=($startRange-$config->bansPerPage)?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>&bf=<?=$bansFilter?>&bri=<?=$bansReason_id?>&ba=<?=$bansAdmin?>&searchText=<?=$searchText?>">&lt;&lt;<?=LANINS_056?></a> <?php
    }

    // Show Middle Links
    $eitherside = (($config->maxPageLinks+1) * $config->bansPerPage);
    if($startRange+1 > $eitherside) {
      // Show first page
      if($currentPage == $page) {
        ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>&bf=<?=$bansFilter?>&bri=<?=$bansReason_id?>&ba=<?=$bansAdmin?>&searchText=<?=$searchText?>"><b>[<?=$page?>]</b></a> <?php
      } else {
        ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>&bf=<?=$bansFilter?>&bri=<?=$bansReason_id?>&ba=<?=$bansAdmin?>&searchText=<?=$searchText?>"><?=$page?></a> <?php
      }
      ?> ... <?php
    }

    while($y<$banCount) {
      if(($y > ($startRange - $eitherside)) && ($y < ($startRange + $eitherside))) {
        if($currentPage == $page) {
          ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>&bf=<?=$bansFilter?>&bri=<?=$bansReason_id?>&ba=<?=$bansAdmin?>&searchText=<?=$searchText?>"><b>[<?=$page?>]</b></a> <?php
        } else {
          ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>&bf=<?=$bansFilter?>&bri=<?=$bansReason_id?>&ba=<?=$bansAdmin?>&searchText=<?=$searchText?>"><?=$page?></a> <?php
        }
      }
      $page++;
      $y+=$config->bansPerPage;
      $lastPage = $y;
    }
    if(($startRange+$eitherside)<$banCount) {
      ?> ... <?php

      // Undo last iteration for showing last page
      $page--;
      $y-=$config->bansPerPage;

      // Show last page
      if($y == $lastPage && ($startRange+$eitherside)<$banCount) {
        ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>&bf=<?=$bansFilter?>&bri=<?=$bansReason_id?>&ba=<?=$bansAdmin?>&searchText=<?=$searchText?>"><b>[<?=$page?>]</b></a> <?php
      } else {
        ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>&bf=<?=$bansFilter?>&bri=<?=$bansReason_id?>&ba=<?=$bansAdmin?>&searchText=<?=$searchText?>"><?=$page?></a> <?php
      }
    }

    // Show next button
    if(($page-1 > ($startRange/$config->bansPerPage)+1 || $currentPage == 1) && $banCount > $config->bansPerPage) {
    ?><a href="index.php?page=banlist&sr=<?=($startRange+$config->bansPerPage)?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>&bf=<?=$bansFilter?>&bri=<?=$bansReason_id?>&ba=<?=$bansAdmin?>&searchText=<?=$searchText?>"> <?=LANINS_057?>&gt;&gt;</a> <?php
    }
  }
}
?>
