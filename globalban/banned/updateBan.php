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

if($member || $admin || $banManager || $fullPower) {

    require_once(ROOTDIR."/include/database/class.ServerQueries.php");
    require_once(ROOTDIR."/include/database/class.ReasonQueries.php");
    require_once(ROOTDIR."/include/database/class.BanQueries.php");
    include_once(ROOTDIR."/include/database/class.LengthQueries.php");
    require_once(ROOTDIR."/include/database/class.UserQueries.php"); // User specific queries
    require_once(ROOTDIR."/include/objects/class.User.php"); // User class to store user info
    include_once(ROOTDIR."/include/objects/class.Length.php");

    $lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_updateBan.php';
    include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_updateBan.php");


    // Initialize Objects
    $serverQueries = new ServerQueries();
    $reasonQueries = new ReasonQueries();
    $banQueries = new BanQueries();
    $lengthQueries = new LengthQueries();
    $userQuery = new UserQueries;
    $userEdit = new User;

    // Ban ID
    $banId = $_GET['banId'];

    // Get the list of servers
    $serverList = $serverQueries->getServers();

    // List of Admins
    $banAmins = $reasonQueries->getAdminsList();

    // List of Reasons
    $banReasons = $reasonQueries->getReasonList();

    // Banned user information
    $bannedUser = $banQueries->getBannedUser($banId);

    // List of Ban Lengths
    $banLengths = $lengthQueries->getLengthList();

    // Ban history of the user
    $banHistory = $banQueries->getBanHistory($banId);


    if($fullPower || $banManager || 
            (($bannedUser->getBanner() == $_SESSION['name'] && !empty($_SESSION['name'])) && ($admin || $member)) || 
            (($bannedUser->getBannerSteamId() == $_SESSION['steamId'] && !empty($_SESSION['steamId'])) && ($admin || $member))) {

        ?>
        <script type="text/javascript">
        <!--
        function confirmIpBan() {
            if (confirm('<?php echo $LANUPDATEBAN_032; ?>')){
            document.getElementById('banIpForm').submit()
            }
        }
        //-->
        </script>
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
        <div class="tborder">
        <div id="tableHead">
          <div><b><?php echo $LANUPDATEBAN_001; ?></b></div>
        </div>
        <form action="index.php?page=processWebBanUpdate" method="POST">
        <input type="hidden" name="banId" value="<?php echo $banId ?>">
        <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
        <tr>
          <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_002; ?></td>
          <td class="rowColor1"><?php echo $bannedUser->getSteamId() ?></td>
        </tr>
        <tr>
          <td class="rowColor2" width="1%" nowrap><?php echo $LANUPDATEBAN_003; ?></td>
          <td class="rowColor2"><?php echo $bannedUser->getIp() ?>
          <?php
            if($bannedUser->getIp() != "") {
              if($banQueries->isIpBanned($bannedUser->getIp())) {
                ?><span class="error">&nbsp; &lt;&lt; <?php echo $LANUPDATEBAN_004; ?> &gt;&gt; </span><?php
              } else {
                // Only admins, ban mangers, and full power admins can IP ban
                // Members are not allowed to IP ban
                if($admin || $banManager || $fullPower) {
                  ?><input type="button" value="<?php echo $LANUPDATEBAN_031; ?>" onclick="confirmIpBan()"><?php
                }
              }
            } else {
              ?><?php echo $LANUPDATEBAN_005; ?>  <?php echo $bannedUser->getSteamId() ?><?php
            }
            
            $userEdit = $userQuery->getUserInfo($bannedUser->getModifiedBy());
            $fullPowerLevelEditUser = false;
            if($userEdit->getAccessLevel() == 1) {
                $fullPowerLevelEditUser = true;
            }
          ?>
          </td>
        </tr>
        <tr>
          <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_006; ?></td>
          <?php
          if ($fullPowerLevelEditUser && !$fullPower){ 
            ?><td class="rowColor1"><input type="hidden" name="bannedUser" value="<?php echo $bannedUser->getName(); ?>"><?php echo $bannedUser->getName(); ?></td><?php
          } else {
            ?><td class="rowColor1"><input type="text" name="bannedUser" size="40" maxlength="128" value="<?php echo $bannedUser->getName(); ?>"/></td><?php
          }
        ?>
        </tr>
        <tr>


        <td class="rowColor2" width="1%" nowrap><?php echo $LANUPDATEBAN_007; ?></td>
          <td class="rowColor2">
            <select name="admin_banner">
            <?php

            
            // Make sure we have a list of admis to display
            if(count($banAmins > 0)) {
              $selectedAdmin = false;

              for($i=0; $i<count($banAmins);$i++) {
                $admin_banner = $banAmins[$i]; 
                if($admin_banner->getAdmin() == $bannedUser->getBanner()) {
                  $selectedAdmin = true;
                  ?><option value="<?php echo $admin_banner->getAdmin()?>" selected><?php echo $bannedUser->getBanner()?></option><?php
                } else if(!$fullPowerLevelEditUser && $userQuery->getUserInfo($bannedUser->getBanner())->getAccessLevel() != 1 && $banManager){
                  ?><option value="<?php echo $admin_banner->getAdmin()?>"><?php echo $admin_banner->getAdmin()?></option><?php
                } else if ($fullPower){
                  ?><option value="<?php echo $admin_banner->getAdmin()?>"><?php echo $admin_banner->getAdmin()?></option><?php
                }
              }
              if(!$selectedAdmin){
                ?><option value="<?php echo $bannedUser->getBanner()?>" selected><?php echo $bannedUser->getBanner()?></option><?php
              }
            } else {
            ?><option value="-1"><?php echo $LANUPDATEBAN_008; ?></option><?php
            }
            ?>
            </select>
          </td>

        </tr>
        <tr>
          <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_009; ?></td>
          <td class="rowColor1">
            <select name="length">
              <?php
              foreach($banLengths as $banLength) {

                if($bannedUser->getLength() == $banLength->getLength() && $bannedUser->getTimeScale() == $banLength->getTimeScale()) {
                        ?><option value="<?php echo $banLength->getId()?>" selected><?php echo $banLength->getReadable()?></option><?php
                } else if(!$fullPowerLevelEditUser && $userQuery->getUserInfo($bannedUser->getBanner())->getAccessLevel() != 1) {
                        ?><option value="<?php echo $banLength->getId()?>"><?php echo $banLength->getReadable()?></option><?php
                } else if ($fullPower){
                        ?><option value="<?php echo $banLength->getId()?>"><?php echo $banLength->getReadable()?></option><?php
                }
              }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td class="rowColor2" width="1%" nowrap><?php echo $LANUPDATEBAN_010; ?></td>
          <td class="rowColor2">
            <select name="serverId">
            <?php
            if(count($serverList > 0)) {
              for($i=0; $i<count($serverList);$i++) {
                $server = $serverList[$i];
                if($server->getId() == $bannedUser->getServerId()) {
                  ?><option value="<?php echo $server->getId() ?>" selected><?php echo $server->getName() ?></option><?php
                } else {
                  ?><option value="<?php echo $server->getId() ?>"><?php echo $server->getName() ?></option><?php
                }
              }
            } else {
            ?><option value="-1"><?php echo $LANUPDATEBAN_011; ?></option><?php
            }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_012; ?></td>
          <td class="rowColor1">
            <select name="reason">
            <?php
            // Make sure we have a list of ban reasons to display
            if(count($banReasons > 0)) {
              // Ignore first reason as it is a generic reason for importing bans from a ban list
              for($i=0; $i<count($banReasons);$i++) {
                $reason = $banReasons[$i]; 
                if($reason->getId() == $bannedUser->getReasonId()) {
                  ?><option value="<?php echo $reason->getId() ?>" selected><?php echo $reason->getReason() ?></option><?php
                } else if(!$fullPowerLevelEditUser && $userQuery->getUserInfo($bannedUser->getBanner())->getAccessLevel() != 1){
                  ?><option value="<?php echo $reason->getId() ?>"><?php echo $reason->getReason() ?></option><?php
                } else if($fullPower){
                  ?><option value="<?php echo $reason->getId() ?>"><?php echo $reason->getReason() ?></option><?php
                }
              }
            } else {
            ?><option value="-1"><?php echo $LANUPDATEBAN_013; ?></option><?php
            }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td class="rowColor2" width="1%" valign="top" nowrap><?php echo $LANUPDATEBAN_014; ?></td>
          <td class="rowColor2"><textarea id="comments" name="comments" cols="80" rows="10"><?php echo $bannedUser->getComments() ?></textarea></td>
        </tr>
        <tr>
          <td class="rowColor1" width="1%" nowrap><?php echo $LANUPDATEBAN_015; ?></td>
          <td class="rowColor1"><input type="text" name="bannedPost" size="100" maxlength="128" value="<?php echo $bannedUser->getWebpage(); ?>"/></td>
        </tr>
        <tr>
          <td class="rowColor2" width="1%" nowrap><?php echo $LANUPDATEBAN_016; ?></td>
          <?php
          if($bannedUser->getModifiedBy() == "") { 
          ?>
            <td class="rowColor2"><?php echo $bannedUser->getBanner(); ?></td>
          <?php
          } else {
          ?>
            <td class="rowColor2"><?php echo $bannedUser->getModifiedBy(); ?></td>
          <?php
          }
          ?>
        </tr>
        <tr>
          <td colspan="2" class="rowColor1"><input type="submit" name="updateBan" value="<?php echo $LANUPDATEBAN_017; ?>"></td>
        </tr>
        </table>
        <input type="hidden" name="ModifiedBy" id="ModifiedBy" value="<?php echo $bannedUser->getModifiedBy(); ?>"/>
        <input type="hidden" name="fullPowerLevelEditUser" id="fullPowerLevelEditUser" value="<?php echo $fullPowerLevelEditUser; ?>"/>
        </form>
        <form name="bandIpForm" id="banIpForm" action="index.php?page=processWebBanUpdate" method="POST">
          <input type="hidden" name="banIp" id="banIp" value="1"/>
          <input type="hidden" name="banId" value="<?php echo $banId ?>">
          <input type="hidden" name="ip" id="ip" value="<?php echo $bannedUser->getIp() ?>"/>
        </form>
        </div>

        <br/>
        <br/>

        <div class="tborder">
          <div id="tableHead">
            <div><b><?php echo $LANUPDATEBAN_018 ?></b></div>
          </div>

          <div>
            <table id="banlistTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">

            <tr>
              <th class="colColor1" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_019; ?></th>
              <th class="colColor2" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_020; ?></th>
              <th class="colColor1" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_021; ?></th>
              <th class="colColor2" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_022; ?></th>
              <th class="colColor1" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_023; ?></th>
              <th class="colColor2" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_024; ?></th>
              <th class="colColor1" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_025; ?></th>
              <th class="colColor2" width="1%" align="center" nowrap><?php echo $LANUPDATEBAN_026; ?></th>
            </tr>
            <?php
            // Loop through banned users and display them
            foreach($banHistory as $banHistUser) {
              $length = "";
              list($expireDate, $expireTime) = split(' ', $banHistUser->getExpireDate());
              list($addDate, $addTime, $year) = split(' ', $banHistUser->getAddDate());
              $comments = str_replace(array("\r\n", "\n", "\r"), "<br/>", $banHistUser->getComments()); // Convert newlines into html line breaks

              $banLength = new Length();
              $banLength->setLength($banHistUser->getLength());
              $banLength->setTimeScale($banHistUser->getTimeScale());

              if($banHistUser->getLength() == 0) {
                $expireDate = $LANUPDATEBAN_027;
                $expireTime = "";
              }

              if($banHistUser->getExpireDate() == 'Expired') {
                $expireDate = "<i>".$LANUPDATEBAN_028."</i>";
                $expireTime = "";
              }
        $length = $banLength->getReadable();
              
            
            ?>
            <tr>
              <td class="colColor1" nowrap align="center"><?php echo $banHistUser->getName();
                if($banHistUser->getKickCounter() > 0) {
                    echo "&nbsp;<span class='kickCounter'>(".$banHistUser->getKickCounter().")</span>";
                }
              ?></td>
              <td class="colColor2" nowrap align="center"><?php echo $banHistUser->getReason()?></td>
              <td class="colColor1" nowrap align="center"><?php echo $length?></td>
              <td class="colColor2" nowrap align="center"><?php echo $banHistUser->getBanner()?></td>
              <td class="colColor1" nowrap align="center"><?php echo $addDate." ".$addTime?></td>
              <td class="colColor2" nowrap align="center"><?php echo $expireDate." ".$expireTime?></td>
              <?php
              if($banHistUser->getWebpage() != "") {
                echo "<td class='rowColor1' align='center'><a href='".$banHistUser->getWebpage()."'><img src='images/database_add.png' align='absmiddle'/></a></td>";
              } else {
                echo "<td class='rowColor1' align='center'><img src='images/cross.png' align='absmiddle' alt='".$LANUPDATEBAN_029."'/></td>";
              }
              ?>
              <td class="colColor2" nowrap><?php echo $comments?></td>
            </tr>
            <?php
            }
            ?>
            
            </table>
          </div>
        </div>
        <?php
    } else {
        ?>
        <div class="tborder">
          <div id="tableHead">
            <div><b><?php echo $LANUPDATEBAN_030; ?></b></div>
          </div>
        </div>
        <?php
    }
} else {
    ?>
    <div class="tborder">
      <div id="tableHead">
        <div><b><?php echo $LANUPDATEBAN_030; ?></b></div>
      </div>
    </div>
    <?php
}
?>
