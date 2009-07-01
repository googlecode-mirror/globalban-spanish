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
include_once(ROOTDIR."/include/objects/class.Length.php");

// Page gets (for range and sorts and other things)
$startRange = $_GET['sr']; // Start Range
$sortBy = $_GET['sc']; // Column to sort by
$sortDirection = $_GET['sd']; // Direction to sort by
$searchText = $_POST['searchText']; // Search text

if(empty($startRange)) {
  $startRange = 0;
}
if(empty($sortBy)) {
  $sortBy = "add_date";
}
if(empty($sortDirection)) {
  $sortDirection = "DESC";
}

$banQueries = new BanQueries();

// Count how many bans exist in the database
$banCount = $banQueries->getNumberOfBans($member, $admin, $banManager, $fullPower, $searchText);

$bannedUsers = $banQueries->getBanList($member, $admin, $banManager, $fullPower, $startRange, $banCount, $sortBy, $sortDirection, $searchText);

$endRange = $banQueries->getEndRange();

if($endRange > $banCount) {
  $endRange = $banCount;
}

if(count($bannedUsers) > 0) {
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
  
  <div id="search" align="right">
  <form action="" method="post">
  <input name="searchText" id="searchText" type="text" value="" size="40" maxLength="40"/>
  <input type="submit" value="Search">
  </form>
  </div>
  
  <div class="tborder">
    <div id="tableHead">
      <div><b>Ban List showing bans <?=($startRange+1)?> to <?=$endRange?> of <?=$banCount?></b></div>
      <div>
        <?php pageLinks($config, $startRange, $banCount, $sortDirection, $sortBy); ?>
      </div>
    </div>
    
    <div>
    <table id="banlistTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    
    <tr>
      <th class="colColor2" width="1%" nowrap></th>
      <th class="colColor1" width="1%" nowrap>
        <a href="index.php?page=banlist&sc=b.steam_id&sd=ASC&sr=<?=$startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        Steam ID
        <a href="index.php?page=banlist&sc=b.steam_id&sd=DESC&sr=<?=$startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <th class="colColor2" width="1%" nowrap>
        <a href="index.php?page=banlist&sc=name&sd=ASC&sr=<?=$startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        Person Banned
        <a href="index.php?page=banlist&sc=name&sd=DESC&sr=<?=$startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <th class="colColor1" width="1%" nowrap>
        <a href="index.php?page=banlist&sc=length&sd=ASC&sr=<?=$startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        Length
        <a href="index.php?page=banlist&sc=length&sd=DESC&sr=<?=$startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <th class="colColor2" width="1%" nowrap>
        <a href="index.php?page=banlist&sc=banner&sd=ASC&sr=<?=$startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        Banner
        <a href="index.php?page=banlist&sc=banner&sd=DESC&sr=<?=$startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <th class="colColor1" width="1%" nowrap>
        <a href="index.php?page=banlist&sc=add_date&sd=ASC&sr=<?=$startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        Add Date
        <a href="index.php?page=banlist&sc=add_date&sd=DESC&sr=<?=$startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <th class="colColor2" width="1%" nowrap>
        <a href="index.php?page=banlist&sc=b.expire_date&sd=ASC&sr=<?=$startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        Expire Date
        <a href="index.php?page=banlist&sc=b.expire_date&sd=DESC&sr=<?=$startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <th class="colColor1">
        <a href="index.php?page=banlist&sc=b.reason_id&sd=ASC&sr=<?=$startRange?>"><img src="images/arrow_up.png"/></a>
        Ban Reason
        <a href="index.php?page=banlist&sc=b.reason_id&sd=DESC&sr=<?=$startRange?>"><img src="images/arrow_down.png"/></a>
      </th>
      <?php
      // Show extra headers for ban manager
      if($member || $admin || $banManager || $fullPower) {
        ?>
        <th class="colColor2" width="1%" nowrap>
          <a href="index.php?page=banlist&sc=active&sd=ASC&sr=<?=$startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
          Active
          <a href="index.php?page=banlist&sc=active&sd=DESC&sr=<?=$startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
        </th>
        <th class="colColor1" width="1%" nowrap>
          <a href="index.php?page=banlist&sc=pending&sd=ASC&sr=<?=$startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
          Pending
          <a href="index.php?page=banlist&sc=pending&sd=DESC&sr=<?=$startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/>
        </th>
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
        $expireDate = "<i>".$expireDate."</i>";
      }
      list($addDate, $addTime, $year) = split(' ', $bannedUser->getAddDate());
      $comments = str_replace(array("\r\n", "\n", "\r"), "<br/>", $bannedUser->getComments()); // Convert newlines into html line breaks
      $comments = str_replace('"', "&#34;", $comments); // Replace quotes with the HTML code
      
      $banLength = new Length();
      $banLength->setLength($bannedUser->getLength());
      $banLength->setTimeScale($bannedUser->getTimeScale());
      
      if($bannedUser->getLength() == 0) {
        $expireDate = "Never";
        $expireTime = "Never";
      }
      
      $length = $banLength->getReadable();
      
      $information = "<div class='tborder'>";
      $information .= "<div id='tableHead'>";
      $information .= "<div style='color:#FFFFFF'><b>Detailed Ban Information</b></div>";
      $information .= "</div>";
      $information .= "<table class='bordercolor' width='100%'' cellspacing='1' cellpadding='5' border='0' style='margin-top: 1px;'>";
      $information .= "<tr class='rowColor1'><td>Steam ID:</td><td>".$bannedUser->getSteamId()."</td></tr>";
      $information .= "<tr class='rowColor2'><td>Person Banned:</td><td>".str_replace('"', "&#34;", $bannedUser->getName())."</td></tr>";
      $information .= "<tr class='rowColor1'><td>Length:</td><td>".$length."</td></tr>";
      $information .= "<tr class='rowColor2'><td>Banner:</td><td>".str_replace('"', "&#34;", $bannedUser->getBanner())."</td></tr>";
      $information .= "<tr class='rowColor1'><td>Add Date:</td><td>".$bannedUser->getAddDate()."</td></tr>";
      $information .= "<tr class='rowColor2'><td>Expire Date:</td><td>".$expireDate." ".$expireTime."</td></tr>";
      $information .= "<tr class='rowColor1'><td>Ban Reason:</td><td>".$bannedUser->getReason()."</td></tr>";
      if($bannedUser->getServerId() != -1) {
        $information .= "<tr class='rowColor2'><td>Server:</td><td>".str_replace('"', "&#34;", $bannedUser->getServer())."</td></tr>";
      } else {
        $information .= "<tr class='rowColor2'><td>Server:</td><td><a href='".str_replace('"', "&#34;", $bannedUser->getServer())."'>Import Server</a></td></tr>";
      }
      $information .= "<tr class='rowColor2'><td>Previous Offenses:</td><td>".str_replace('"', "&#34;", $bannedUser->getOffenses())."</td></tr>";
      $information .= "<tr class='rowColor1'><td>Demos:</td>";
      $information .= "<td>";
      if($bannedUser->getDemoCount() > 0) {
        $information .= "<a href='index.php?page=demos&searchText=".$bannedUser->getSteamId()."'><b>View Demos (".$bannedUser->getDemoCount().")</b></a>";
      } else {
        $information .= "<b>".$bannedUser->getDemoCount()." demos</b>";
      }
      $information .= "</td></tr>";
      $information .= "<tr class='rowColor2'><td valign='top'>Comments:</td><td>".$comments."</td></tr>";
      $information .= "</table>";
      $information .= "</div>";
      
      //$information = str_replace("'", "\\'", $information);
      $information = addslashes($information);
      
      ?>
      <tr>
        <td class="colColor2"><img src="images/information.png" style="cursor:help" onmouseover="Tip('<?=$information?>', WIDTH, 300, SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, OFFSETX, -20, CLOSEBTN, true, CLICKCLOSE, true, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'))"/></td>
        <?php
          if(($banManager || $fullPower || $admin || $member) && ($bannedUser->getComments() != null || $bannedUser->getComments() != "")) {
            ?><td class="colColor1" nowrap><?php
          } else {
            ?><td class="colColor1" nowrap><?php
          }
        ?>
        
        <?php
          // Fullpower admins and Ban Mangers can modify ALL bans
          // Members and Amdins can only edit their own bans (which is matched by either banner name or banner steam id)
          if($fullPower || $banManager || 
            (($bannedUser->getBanner() == $_SESSION['name'] && !empty($_SESSION['name'])) && ($admin || $member)) || 
            (($bannedUser->getBannerSteamId() == $_SESSION['steamId'] && !empty($_SESSION['steamId'])) && ($admin || $member))) {
            ?><a href="index.php?page=updateBan&banId=<?=$bannedUser->getBanId()?>"><?=$bannedUser->getSteamId()?></a><?php
          } else {
        ?>  
        <?=$bannedUser->getSteamId()?>
        <?php
          }  
        ?>
        </td>
        <td class="colColor2" nowrap><?=$bannedUser->getName()?></td>
        <td class="colColor1" nowrap><?=$length?></td>
        <td class="colColor2" nowrap><?=$bannedUser->getBanner()?></td>
        <td class="colColor1" nowrap><?=$addDate?></td>
        <td class="colColor2" nowrap><?=$expireDate?></td>
        <td class="colColor1"><?=$bannedUser->getReason()?></td>
        <?php
        // Show extra headers for ban manager
        if($banManager || $fullPower) {
          ?>
          <td id="active-<?=$bannedUser->getBanId()?>" class="colColor2" style="cursor:pointer;"
              onmouseover="if(<?=$bannedUser->getActive()?> == 0) { Tip('Click to activate', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}else{Tip('Click to de-activate', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}">
          <?php if($bannedUser->getActive() == 0) {
            ?><img id="activeImg-<?=$bannedUser->getBanId()?>" src="images/cross.png"/><?php
          } else {
            ?><img id="activeImg-<?=$bannedUser->getBanId()?>" src="images/tick.png"/><?php
          } ?>
          </td>
          <td id="pending:<?=$bannedUser->getBanId()?>" class="colColor1"
              onclick="changePendingStatus(<?=$bannedUser->getBanId()?>, <?=$bannedUser->getPending()?>)" style="cursor:pointer;"
              onmouseover="if(<?=$bannedUser->getActive()?> == 0) { Tip('Click to place into pending mode', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}else{Tip('Click to remove from pending mode', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}">
          <?php if($bannedUser->getPending() == 0) {
            ?><img id="pendingImg-<?=$bannedUser->getBanId()?>" src="images/cross.png"/><?php
          } else {
            ?><img id="pendingImg-<?=$bannedUser->getBanId()?>" src="images/hourglass.png"/><?php
          } ?>
          </td>
          <?php
        } else if($member || $admin) {
          // Show the status to members and admins, but they can not update it
          // They can however update it if they were the ones to ban
          if((($bannedUser->getBanner() == $_SESSION['name'] && !empty($_SESSION['name'])) && ($admin || $member)) || 
            (($bannedUser->getBannerSteamId() == $_SESSION['steamId'] && !empty($_SESSION['steamId'])) && ($admin || $member))) {
            ?><td id="active:<?=$bannedUser->getBanId()?>" class="colColor2"
                  onclick="changeActiveStatus(<?=$bannedUser->getBanId()?>, <?=$bannedUser->getActive()?>);" style="cursor:pointer;"
                  onmouseover="if(<?=$bannedUser->getActive()?> == 0) { Tip('Click to activate', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}else{Tip('Click to de-activate', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('banlistTable'));}"><?php
          } else {
          ?><td id="active:<?=$bannedUser->getBanId()?>" class="colColor2"><?php
          }
          if($bannedUser->getActive() == 0) {
            ?><img src="images/cross.png"/><?php
          } else {
            ?><img src="images/tick.png"/><?php
          } ?>
          </td>
          <td id="pending:<?=$bannedUser->getBanId()?>" class="colColor1">
          <?php if($bannedUser->getPending() == 0) {
            ?><img src="images/cross.png"/><?php
          } else {
            ?><img src="images/hourglass.png"/><?php
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
        <?php pageLinks($config, $startRange, $banCount, $sortDirection, $sortBy); ?>
      </div>
    </div>
  </div>
  <h5>*NOTE: Bans apply to ALL servers on the server list page.</h5>
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

    alert("You must select either Perma Bans Only or All Bans when downloading the Ban List");
    return false;
  }
  </script>
  <div class="tborder">
    <div id="tableHead">
      <div><b>Download Bans</b</div>
    </div>
    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <form action="exportBans.php" method="post" id="form" onsubmit="return formVerify();">
    	<table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
        <tr>
          <td align="left" class="rowColor1">
            <input type="checkbox" id="permaBans" name="permaBans" value="1" checked/> Perma Bans Only
            <input type="checkbox" id="allBans" name="allBans" value="1"/> All Bans
            <input type="checkbox" id="demosOnly" name="demosOnly" value="1"/> Bans with demos Only
            <input type="checkbox" id="saveSQL" name="saveSQL" value="1" checked/> Save as XML
          </td>
        </tr>
    		<tr>
    			<td align="left" class="rowColor2">
    				<input type="submit" name="submit" value="Download Ban List" class="button" /></td>
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
    <div><b>Ban List is Empty</b></div>
  </div>
</div>
<?php
}

function pageLinks($config, $startRange, $banCount, $sortDirection, $sortBy) {
  if($config->bansPerPage > 0) {

    $y=0;
    $page = 1; // Starting page
    $currentPage = ($startRange/$config->bansPerPage)+1; // Page we are currently on

    // Show previous button
    if($currentPage != 1) {
    ?><a href="index.php?page=banlist&sr=<?=($startRange-$config->bansPerPage)?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>">&lt;&lt;Previous</a> <?php
    }

    // Show Middle Links
    $eitherside = (($config->maxPageLinks+1) * $config->bansPerPage);
    if($startRange+1 > $eitherside) {
      // Show first page
      if($currentPage == $page) {
        ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>"><b>[<?=$page?>]</b></a> <?php
      } else {
        ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>"><?=$page?></a> <?php
      }
      ?> ... <?php
    }

    while($y<$banCount) {
      if(($y > ($startRange - $eitherside)) && ($y < ($startRange + $eitherside))) {
        if($currentPage == $page) {
          ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>"><b>[<?=$page?>]</b></a> <?php
        } else {
          ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>"><?=$page?></a> <?php
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
        ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>"><b>[<?=$page?>]</b></a> <?php
      } else {
        ?><a href="index.php?page=banlist&sr=<?=$y?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>"><?=$page?></a> <?php
      }
    }

    // Show next button
    if(($page-1 > ($startRange/$config->bansPerPage)+1 || $currentPage == 1) && $banCount > $config->bansPerPage) {
    ?><a href="index.php?page=banlist&sr=<?=($startRange+$config->bansPerPage)?>&sd=<?=$sortDirection?>&sc=<?=$sortBy?>"> Next&gt;&gt;</a> <?php
    }
  }
}
?>
