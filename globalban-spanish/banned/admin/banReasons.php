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

require_once(ROOTDIR."/include/database/class.ReasonQueries.php");
require_once(ROOTDIR."/include/objects/class.Reason.php");

?>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
function deleteVerify(id, reason) {
  if(confirm("Do you really want to delete the reason: "+reason+"?")) {
    document.getElementById("deleteReason"+id).submit();
  }
}
</script>
<?php

$reasonQueries = new ReasonQueries();

$error = false;

// If this is set, then that means a server is being added
if(isset($_POST['submitAdd'])) {
  
  // Convert + to |plus|
  // Convert & to |amp|
  $reasonText = $_POST['reasonText'];
  $reasonText = str_replace("&", "|amp|", $reasonText);
  $reasonText = str_replace("+", "|plus|", $reasonText);
  
  $newReasonId = $reasonQueries->addReason($reasonText);
  
  if($newReasonId < 1) {
    $error = true;
  }
}

// If a reason is being deleted
if(isset($_POST['deleteReason'])) {
  $reasonQueries->deleteReason($_POST['reasonId']);
}
// Get list of reasons
$reasons = $reasonQueries->getReasonList();

if($fullPower) {
?>
<div class="tborder">
  <div id="tableHead">
    <div><b>Ban Reason List</b></div>
  </div>
  <table id="serverManagementTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <th class="colColor1" width="1%" nowrap>Ban Reason ID</th>
      <th class="colColor2" width="1%" nowrap>Reason</th>
      <th class="colColor2" width="1%" nowrap>Save</th>
      <th class="colColor1" width="1%" nowrap>Delete</th>    
    </tr>
    <?php foreach($reasons as $reason) {           
          $reasonId = $reason->getId();
          $reasonText = $reason->getReason();
          ?>
          <tr>
            <td class="colColor1" width="1%" nowrap><?=$reasonId?></td>
            <td class="colColor2" width="1%" nowrap><input type="text" id="reasonText:<?=$reasonId?>" name="reasonText:<?=$reasonId?>" value="<?=$reasonText?>" size="60" maxlength="60"/></td>
            <td id="save:<?=$reasonId?>" class="colColor1" onclick="saveBanReason('<?=$reasonId?>');" style="cursor:pointer;">
            <img src="images/tick.png"/>
            </td>
            <td class="colColor2" style="cursor:pointer;" onclick="deleteVerify('<?=$reasonId?>', '<?=$reasonText?>');">
            <form action="index.php?page=banReasons&adminPage=1" id="deleteReason<?=$reasonId?>" name="deleteReason<?=$reasonId?>" method="POST">
              <input type="hidden" name="reasonId" id="reasonId" value="<?=$reasonId?>"/>
              <input type="hidden" name="deleteReason" value="1">
              <img src="images/trash-full.png"/>
            </form>
            </td>
          </tr>
    <?php } ?>
    
    <tr>
      <td class="colColor1" width="1%" nowrap>&nbsp;</td>
      <td class="colColor2" width="1%" nowrap></td>
      <td class="colColor1" ></td>
      <td class="colColor2"></td>
      </tr>
    
    <!-- This row is for adding a new reason -->
    <tr>
      <form action="index.php?page=banReasons&adminPage=1" method="POST" onsubmit="return formVerify();">
      <td class="colColor1" width="1%" nowrap>Add New Reason</td>
      <td class="colColor2" width="1%" nowrap><input type="text" name="reasonText" id="reasonText" value="" size="60" maxlength="60"/></td>
      <td class="colColor1" ><input type="submit" name="submitAdd" id="submitAdd" value="Add"/></td>
      <td class="colColor2"></td>
      </form>
    </tr>
    <?php
      if($error) {
      ?><tr><td class="colColor1" colspan="9"><span class="error">Reason Already Exists</span></td></tr><?php
      }
    ?>
  </table>
</div>
<br/>
<br/>
<input type="button" value="Save Ban Reasons to Servers" class="button" onclick="location.href='index.php?page=uploadBanReasons&adminPage=1'" />

<?php
}
?>
