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

include_once(ROOTDIR."/include/database/class.ServerQueries.php");
include_once(ROOTDIR."/include/database/class.BanQueries.php");
include_once(ROOTDIR."/include/database/class.ReasonQueries.php");
include_once(ROOTDIR."/include/database/class.DemoQueries.php");
include_once(ROOTDIR."/include/database/class.UserQueries.php");
include_once(ROOTDIR."/include/objects/class.Demo.php");


// Page gets (for range and sorts and other things)
if(!empty($_GET['sr'])){
	$startRange = $_GET['sr']; // Start Range
}else{
	$startRange = 0;
}
if(!empty($_GET['sc'])){
	$sortBy = $_GET['sc']; // Column to sort by
}else{
	$sortBy = "add_date";
}
if(!empty($_GET['sd'])){
	$sortDirection = $_GET['sd']; // Direction to sort by
}else{
	$sortDirection = "DESC";
}
if(!empty($_POST['searchText'])){
	$searchText = $_POST['searchText']; // Search text
}else{
	$searchText = "";
}

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_demos.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_demos.php");

// Initialize Objects
$serverQueries = new ServerQueries();
$reasonQueries = new ReasonQueries();
$demoQueries = new DemoQueries();

$success = "";

// Submit button was pushed
if(!empty($_POST['submitDemo']) && $_POST['submitDemo']) {
	$filename = $_FILES['file']['name']; // Get the name of the file
	$tempName = $_FILES['file']['tmp_name']; // Temp name of when it is uploaded
	$fileType = $_FILES['userfile']['type'];

	// We check for multiple page types as apache may be configured to support them
	$filename = str_ireplace(".php", "_", $filename);
	$filename = str_ireplace(".jsp", "_", $filename);
	$filename = str_ireplace(".asp", "_", $filename);
	$extension = substr($filename, strlen($filename)-3, strlen($filename));
	$allowedExtensions = array("zip", "rar");
	if(in_array($extension, $allowedExtensions)) {
		if(uploadFile($filename, $tempName, $config, $demoQueries)) {
			$success = "success";
		} else {
			$success = "error";
		}
	} else {
		$success = "ext not allowed";
	}
  
}

// Demo delete process
if($banManager || $fullPower) {
  // A ban manager or full power admin executed a demo delete
  if(!empty($_POST['process']) && $_GET['process'] == "delete") {
    if($_GET['demoId'] != null || $_GET['demoId'] != "") {
      $demoDeleted = $demoQueries->deleteDemo(ROOTDIR."/".$config->demoRootDir."/", $_GET['demoId']);
    }
  }
}

// Get the list of servers
$serverList = $serverQueries->getServers();

// List of Reasons
$banReasons = $reasonQueries->getReasonList();

// Get the total number of demos
$demoCount = $demoQueries->getNumberOfDemos($searchText);

// Get Demos
$demoList = $demoQueries->getDemoList($startRange, $demoCount, $sortBy, $sortDirection, $searchText);

$userQueries = new UserQueries();

if($config->enableSmfIntegration) {
  $username = $user_info['username'];
} else {
  $username = $_SESSION['name'];
}

$user = $userQueries->getUserInfo($username);
?>

<script type="text/javascript">
function formVerify() {
  var errorFound = false;

  // Validate Steam ID
  var regex = /^STEAM_[01]:[01]:\d{0,10}$/;
  var steamId = document.getElementById("steamdId").value;
  if(!steamId.match(regex)) {
    document.getElementById("steamIdError").style.display = "";
    errorFound = true;
  } else {
    document.getElementById("steamIdError").style.display = "none";
  }
  
  var offenderName = document.getElementById("offenderName").value;
  if(offenderName == "" || offenderName == null) {
    errorFound = true;
    document.getElementById("offenderNameError").style.display = "";
  } else {
    document.getElementById("offenderNameError").style.display = "none";
  }
  var file = document.getElementById("file").value;
  if(file == "" || file == null) {
    errorFound = true;
    document.getElementById("fileError").style.display = "";
  } else {
    document.getElementById("fileError").style.display = "none";
    // We have a file, now check the extension (can only be zip, or rar)
    var fileExt = file.substring(file.length-3, file.length);
    fileExt = fileExt.toLowerCase();
    if(fileExt != "zip" && fileExt != "rar") {
      errorFound = true;
      document.getElementById("fileExtError").style.display = "";
    } else {
      document.getElementById("fileExtError").style.display = "none";
    }
  }
  
  // We have an error, do not submit the form
  if(errorFound) {
    return false;
  }

  return true;
}
</script>
<?php
// Display a message if the upload was successful
if($success == "success") {
?>
<h5><?php echo $filename?> <?php echo $LANDEMOS_001; ?> <?php echo $fileType?></h5>
<?php
} // end success if
else if($success == "error"){
?>
<h5><?php echo $LANDEMOS_002; ?></h5>
<?php
} else if($success == "ext not allowed") {
?>
<h5><?php echo $LANDEMOS_003; ?></h5>
<?php
}// end success else

if(!empty($demoDeleted) && ($demoDeleted != "" || $demoDeleted != null)) {
?>
<h5><?php echo $demoDeleted?> <?php echo $LANDEMOS_004; ?></h5>
<?php
}
?>
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LANDEMOS_005; ?></b></div>
  </div>
  <form action="index.php?page=demos" onsubmit="return formVerify();" method="POST" enctype="multipart/form-data">
  <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
  <tr>
    <td class="rowColor1" width="1%" nowrap><img src="images/bullet_star.png" /> <?php echo $LANDEMOS_006; ?></td>
    <td class="rowColor1"><input name="steamId" id="steamdId" type="text" value="" size="40"  maxLength="40"/> <?php echo $LANDEMOS_007; ?>
    &nbsp;&nbsp;<font id="steamIdError" color='red' style="display:none;"><?php echo $LANDEMOS_008; ?></font></td>
  </tr>
  <tr>
    <td class="rowColor2" width="1%" nowrap><img src="images/bullet_star.png" /> <?php echo $LANDEMOS_009; ?></td>
    <td class="rowColor2"><input name="offenderName" id="offenderName" type="text" value="" size="40" maxLength="40"/>
    &nbsp;&nbsp;<font id="offenderNameError" color='red' style="display:none;"><?php echo $LANDEMOS_010; ?></font></td>
  </tr>
  <tr>
    <td class="rowColor1" width="1%" nowrap><?php echo $LANDEMOS_011; ?></td>
    <td class="rowColor1"><input name="uploaderName" id="uploaderName" type="text" value="<?php echo $user->getName()?>" size="40" maxLength="40"/> <?php echo $LANDEMOS_012; ?></td>
  </tr>
  <tr>
    <td class="rowColor2" width="1%" nowrap><?php echo $LANDEMOS_013; ?></td>
    <td class="rowColor2"><input name="uploaderSteamId" id="uploaderSteamId" type="text" value="<?php echo $user->getSteamId()?>" size="40" maxLength="40"/> <?php echo $LANDEMOS_012 ?></td>
  </tr>
  <tr>
    <td class="rowColor1" width="1%" nowrap><img src="images/bullet_star.png" /> <?php echo $LANDEMOS_014; ?></td>
    <td class="rowColor1">
      <select name="serverId">
      <?php
      if(count($serverList > 0)) {
        foreach($serverList as $server) {
          ?><option value="<?php echo $server->getId()?>"><?php echo $server->getName()?></option><?php
        }
      } else {
      ?><option value="-1"><?php echo $LANDEMOS_015; ?></option><?php
      }
      ?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="rowColor2" width="1%" nowrap><img src="images/bullet_star.png" /> <?php echo $LANDEMOS_016; ?></td>
    <td class="rowColor2">
      <select name="reasonId">
      <?php
      if(count($banReasons > 0)) {
        foreach($banReasons as $reason) {
          ?><option value="<?php echo $reason->getId()?>"><?php echo $reason->getReason()?></option><?php
        }
      } else {
      ?><option value="0"><?php echo $LANDEMOS_017; ?></option><?php
      }
      ?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="rowColor1" width="1%" nowrap><img src="images/bullet_star.png" /> <?php echo $LANDEMOS_018; ?></td>
    <td class="rowColor1">
      <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ($config->demoSizeLimit)*1000000?>" />
      <input id="file" name="file" size="40" type="file" />
      &nbsp;&nbsp;<font id="fileError" color='red' style="display:none;"><?php echo $LANDEMOS_019; ?></font>
      <font id="fileExtError" color='red' style="display:none;"><?php echo $LANDEMOS_020; ?></font>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="rowColor2"><input type="submit" name="submitDemo" value="<?php echo $LANDEMOS_021; ?>"> <b><?php echo $LANDEMOS_022; ?></b></td>
  </tr>
  </table>
  </form>
</div>

<h5>
<img src="images/bullet_star.png" /> <?php echo $LANDEMOS_023; ?> <br/>
<?php echo $LANDEMOS_024 ?> <img src="images/bullet_star.png" /> <?php echo $LANDEMOS_025; ?><br />
<strong><?php echo $LANDEMOS_026; ?> <?php echo $config->demoSizeLimit?><?php echo $LANDEMOS_027; ?></strong></h5>
<br/>

<?php
if(count($demoList) > 0) {
?>

  <div id="search" align="right">
    <form action="" method="post">
    <input name="searchText" id="searchText" type="text" value="" size="40" maxLength="40"/>
    <input type="submit" value="<?php echo $LANDEMOS_046; ?>">
    </form>
  </div>
  
  <div class="tborder">
    <div id="tableHead">
      <div><b><?php echo $LANDEMOS_028; ?> <?php echo ($startRange+1)?> <?php echo $LANDEMOS_029; ?> <?php echo $demoQueries->getEndRange()?> <?php echo $LANDEMOS_030 ?> <?php echo $demoCount?></b></div>
      <div>
        <?php pageLinks($config, $startRange, $demoCount, $sortDirection, $sortBy); ?>
      </div>
    </div>

    <div>
    <table id="demosTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">

    <tr>
      <th class="colColor1" width="1%" nowrap>
        <a href="index.php?page=demos&sc=d.steam_id&sd=ASC&sr=<?php echo $startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        <?php echo $LANDEMOS_006; ?>
        <a href="index.php?page=demos&sc=d.steam_id&sd=DESC&sr=<?php echo $startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <th class="colColor2" width="1%" nowrap>
        <a href="index.php?page=demos&sc=d.offender_name&sd=ASC&sr=<?php echo $startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        <?php echo $LANDEMOS_031; ?>
        <a href="index.php?page=demos&sc=d.offender_name&sd=DESC&sr=<?php echo $startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <th class="colColor1" width="1%" nowrap>
        <a href="index.php?page=demos&sc=d.demo_name&sd=ASC&sr=<?php echo $startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        <?php echo $LANDEMOS_032; ?>
        <a href="index.php?page=demos&sc=d.demo_name&sd=DESC&sr=<?php echo $startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <th class="colColor2" width="1%" nowrap>
        <a href="index.php?page=demos&sc=add_date&sd=ASC&sr=<?php echo $startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
        <?php echo $LANDEMOS_033; ?>
        <a href="index.php?page=demos&sc=add_date&sd=DESC&sr=<?php echo $startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <th class="colColor1">
        <a href="index.php?page=demos&sc=d.reason_id&sd=ASC&sr=<?php echo $startRange?>"><img src="images/arrow_up.png"/></a>
        <?php echo $LANDEMOS_016; ?>
        <a href="index.php?page=demos&sc=d.reason_id&sd=DESC&sr=<?php echo $startRange?>"><img src="images/arrow_down.png"/></a>
      </th>
      <th class="colColor2">
        <a href="index.php?page=demos&sc=d.server_id&sd=ASC&sr=<?php echo $startRange?>"><img src="images/arrow_up.png"/></a>
        <?php echo $LANDEMOS_034; ?>
        <a href="index.php?page=demos&sc=d.server_id&sd=DESC&sr=<?php echo $startRange?>"><img src="images/arrow_down.png"/></a>
      </th>
      <th class="colColor1" width="1%" nowrap>
        <a href="index.php?page=demos&sc=banned&sd=ASC&sr=<?php echo $startRange?>"><img src="images/arrow_up.png" style="cursor:pointer;"/></a>
       <?php echo $LANDEMOS_035; ?> 
        <a href="index.php?page=demos&sc=banned&sd=DESC&sr=<?php echo $startRange?>"><img src="images/arrow_down.png" style="cursor:pointer;"/></a>
      </th>
      <?php
      if($banManager || $fullPower) {
        ?><th class="colColor1" width="1%" nowrap><?php echo $LANDEMOS_036; ?></th>
        <th class="colColor2" width="1%" nowrap><?php echo $LANDEMOS_037; ?></th><?php
      }
      ?>
    </tr>
    <?php
    // Loop through banned users and display them
    foreach($demoList as $demo) {
      ?>
      <tr>
        <td class="colColor1" nowrap><?php echo $demo->getSteamId()?></td>
        <td class="colColor2" nowrap><?php echo $demo->getOffenderName()?></td>
        <td class="colColor1" nowrap><a href="<?php echo $config->demoRootDir."/".$demo->getDemoName()?>"><?php echo $demo->getDemoName()?></a></td>
        <td class="colColor2" nowrap><?php echo $demo->getAddDate()?></td>
        <td class="colColor1"><?php echo $demo->getReason()?></td>
        <td class="colColor2"><?php echo $demo->getServer()?></td>
        <td id="banned:<?php echo $demo->getDemoId()?>" class="colColor1" width="1%" nowrap>
        <?php
          // Non ban managers can only look
          if($demo->isBanned()) {
            ?><img src="images/tick.png"/><?php
          } else {
            ?><img src="images/cross.png"/><?php
          }
          // Add to banlist and Delete demo column
          if($banManager || $fullPower) {
            if(!$demo->isBanned()) { ?>
            <td class="colColor1"><a href="index.php?page=addBan&steamId=<?php echo $demo->getSteamId()?>&bannedName=<?php echo $demo->getOffenderName()?>&reasonId=<?php echo $demo->getReasonId()?>&serverId=<?php echo $demo->getServerId()?>" style="cursor:pointer;" onmouseover="Tip('<?php echo $LANDEMOS_038 ?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('demosTable'));">
            <img src="images/database_add.png"/></td>
            <?php } else { ?>
            <td class="colColor1"></td>
            <?php } ?>
            <td class="colColor2"><a href="index.php?page=demos&process=delete&demoId=<?php echo $demo->getDemoId()?>" style="cursor:pointer;" onmouseover="Tip('<?php echo $LANDEMOS_039 ?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('demosTable'));">
            <img src="images/trash-full.png"/></a></td>
            <?php
          }
        ?>
        </td>
      </tr>
      <?php
    } // End for loop

    ?>
    </table>
    </div>

    <div id="tableBottom">
      <div>
        <?php pageLinks($config, $startRange, $demoCount, $sortDirection, $sortBy); ?>
      </div>
    </div>
  </div>
  
<?php
} // End demolist count if
?>

</body>
</html>
<?php
function uploadFile($filename, $tempName, $config, $demoQueries) {

  $steamId = $_POST['steamId'];
  $offenderName = $_POST['offenderName'];
  $uploaderName = $_POST['uploaderName'];
  $uploaderSteamId = $_POST['uploaderSteamId'];
  $reasonId = $_POST['reasonId'];
  $serverId = $_POST['serverId'];

  $i=1;
  $start = strlen($filename)-4;
  $end = strlen($filename)-1;
  $demoName = substr($filename, 0, $start); // Get the file name minus extension
  $ext = substr($filename, $start, $end); // Get the file extension
  
  // Append a number to the end of the demo name to make it unique
  while($demoQueries->doesDemoNameExist($filename)) {
    $filename = $demoName.$i.$ext;
    $i++;
  }
  // Check to see if the demo name exists in the database
  // TODO: If it exists... create a new demo name...
  if(!$demoQueries->doesDemoNameExist($filename)) {

    // Upload and add to db
    if(move_uploaded_file($tempName, ROOTDIR."/".$config->demoRootDir."/".$filename."")){
      $demoQueries->addDemo($steamId, $filename, $offenderName, $uploaderName, $uploaderSteamId, $reasonId, $serverId);

      /*
      // FUTURE IMPLEMENTATION
      // Allow uploaders to ban assuming they have enough "credit"
      if($config->numDemosToBan > 0) {
        // First find out how many bans there are because of demos submitted by the user
        
        // If it is equal to or greater than numDemosToBan, add the ban right away
        
        // Send an email if a ban was added
        if($config->sendEmails) {
          // Email
          $subject = "Ban Added from demo upload by ".$uploaderName;

          $body = "<html><body>";
          $body .= "The following ban has been added by ";
          if($member) {
            $body .= "a Member and MUST be reviewed.";
          } else {
            $body .= "an Admin.";
          }
          $body .= "\n\n";

          // Use this to build the URL link (replace processWebBanUpdate with updateBan)
          $url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
          str_replace("processWebBanUpdate", "updateBan", "$url");

          $body .= "\n\n";
          $body .= "Click on the following link to review the newly added ban: <a href='".$url."&banId=".$banId."'>New Ban</a>";
          $body .= "<p>".$nameOfBanned." (".$steamId.") was banned from all servers.</p>";
          $body .= "</body></html>";

          $banManagerEmails = $config->banManagerEmails;
          for($i=0; $i<count($banManagerEmails); $i++) {

            // To send HTML mail, the Content-type header must be set
            $headers  = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
            // Additional headers
            $headers .= "From: ".$config->siteName." Ban Management <".$config->emailFromHeader.">" . "\r\n";

            // Send an email message to those that wish to recieve a notice of a newly added ban
            mail($banManagerEmails[$i], $subject, $body, $headers);
        }
      }
      */
      if($config->sendDemoEmails) {
        // Email
		$subject = $LANDEMOS_040;

        $body = "<html><body>";

        // Use this to build the URL link
        $url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

        $body .= $LANDEMOS_041 . " <a href='".$url."&searchText=".$steamId."'>".$offenderName." (".$steamId.")</a>";
        $body .= "</body></html>";

        $banManagerEmails = $config->banManagerEmails;
        for($i=0; $i<count($banManagerEmails); $i++) {

          // To send HTML mail, the Content-type header must be set
          $headers  = "MIME-Version: 1.0" . "\r\n";
          $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
          // Additional headers
          $headers .= "From: ".$config->siteName." ".$LANDEMOS_043." <".$config->emailFromHeader.">" . "\r\n";

          // Send an email message to those that wish to recieve a notice of a newly added ban
          mail($banManagerEmails[$i], $subject, $body, $headers);
      }
      return true;
      } else {
        return false;
      }
    }
  }
  return false;
}

function pageLinks($config, $startRange, $banCount, $sortDirection, $sortBy) {
  if($config->bansPerPage > 0) {

    $y=0;
    $page = 1; // Starting page
    $currentPage = ($startRange/$config->bansPerPage)+1; // Page we are currently on

    // Show previous button
    if($currentPage != 1) {
    ?><a href="index.php?page=demos&sr=<?php echo ($startRange-$config->bansPerPage)?>&sd=<?php echo $sortDirection?>&sc=<?php echo $sortBy?>">&lt;&lt; <?php echo $LANDEMOS_044 ?></a> <?php
    }

    // Show Middle Links
    $eitherside = (($config->maxPageLinks+1) * $config->bansPerPage);
    if($startRange+1 > $eitherside) {
      // Show first page
      if($currentPage == $page) {
        ?><a href="index.php?page=demos&sr=<?php echo $y?>&sd=<?php echo $sortDirection?>&sc=<?php echo $sortBy?>"><b>[<?php echo $page?>]</b></a> <?php
      } else {
        ?><a href="index.php?page=demos&sr=<?php echo $y?>&sd=<?php echo $sortDirection?>&sc=<?php echo $sortBy?>"><?php echo $page?></a> <?php
      }
      ?> ... <?php
    }

    while($y<$banCount) {
      if(($y > ($startRange - $eitherside)) && ($y < ($startRange + $eitherside))) {
        if($currentPage == $page) {
          ?><a href="index.php?page=demos&sr=<?php echo $y?>&sd=<?php echo $sortDirection?>&sc=<?php echo $sortBy?>"><b>[<?php echo $page?>]</b></a> <?php
        } else {
          ?><a href="index.php?page=demos&sr=<?php echo $y?>&sd=<?php echo $sortDirection?>&sc=<?php echo $sortBy?>"><?php echo $page?></a> <?php
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
        ?><a href="index.php?page=demos&sr=<?php echo $y?>&sd=<?php echo $sortDirection?>&sc=<?php echo $sortBy?>"><b>[<?php echo $page?>]</b></a> <?php
      } else {
        ?><a href="index.php?page=demos&sr=<?php echo $y?>&sd=<?php echo $sortDirection?>&sc=<?php echo $sortBy?>"><?php echo $page?></a> <?php
      }
    }

    // Show next button
    if(($page-1 > ($startRange/$config->bansPerPage)+1 || $currentPage == 1) && $banCount > $config->bansPerPage) {
    ?><a href="index.php?page=demos&sr=<?php echo ($startRange+$config->bansPerPage)?>&sd=<?php echo $sortDirection?>&sc=<?php echo $sortBy?>"> <?php echo $LANDEMOS_045 ?>&gt;&gt;</a> <?php
    }
  }
}
?>
