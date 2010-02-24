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

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_banReasons.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_banReasons.php");

?>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
function deleteVerify(id, reason) {
  if(confirm("<?php echo $LAN_BANREASONS001 ?> "+reason+" <?php echo $LAN_BANREASONS002 ?>")) {
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
    <div><b><?php echo $LAN_BANREASONS003 ?> </b></div>
  </div>
  <table id="serverManagementTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <th class="colColor2" width="1%" nowrap><div align="center"><?php echo $LAN_BANREASONS004 ?></div></th>
      <th class="colColor1" width="1%" nowrap><div align="center"><?php echo $LAN_BANREASONS005 ?></div></th>
      <th class="colColor2" width="1%" nowrap><div align="center"><?php echo $LAN_BANREASONS006 ?></div></th>
      <th class="colColor1" width="1%" nowrap><div align="center"><?php echo $LAN_BANREASONS007 ?></div></th>    
    </tr>
    <?php foreach($reasons as $reason) {           
          $reasonId = $reason->getId();
          $reasonText = $reason->getReason();
          ?>
          <tr>
            <td class="colColor2" width="1%" nowrap><div align="center"><?php echo $reasonId?></div></td>
            <td class="colColor1" width="1%" nowrap><div align="center"><input type="text" id="reasonText:<?php echo $reasonId ?>" name="reasonText:<?php echo $reasonId ?>" value="<?php echo $reasonText ?>" size="60" maxlength="60"/></div></td>
            <td id="save:<?php echo $reasonId ?>" class="colColor2" onclick="saveBanReason('<?php echo $reasonId ?>');" style="cursor:pointer;">
            <div align="center"><img src="images/save.png"/></div>
            </td>
            <td class="colColor1" style="cursor:pointer;" onclick="deleteVerify('<?php echo $reasonId ?>', '<?php echo $reasonText ?>');">
            <form action="index.php?page=banReasons&adminPage=1" id="deleteReason<?php echo $reasonId ?>" name="deleteReason<?php echo $reasonId ?>" method="POST">
              <input type="hidden" name="reasonId" id="reasonId" value="<?php echo $reasonId ?>"/>
              <input type="hidden" name="deleteReason" value="1">
              <div align="center"><img src="images/trash-full.png"/></div>
            </form>
            </td>
          </tr>
    <?php } ?>
    
    <tr>
      <td class="colColor2" width="1%" nowrap>&nbsp;</td>
      <td class="colColor1" width="1%" nowrap></td>
      <td class="colColor2" ></td>
      <td class="colColor1"></td>
      </tr>
    
    <!-- This row is for adding a new reason -->
    <tr>
      <form action="index.php?page=banReasons&adminPage=1" method="POST" onsubmit="return formVerify();">
      <td class="colColor2" width="1%" nowrap><div align="center"><?php echo $LAN_BANREASONS008 ?></div></td>
      <td class="colColor1" width="1%" nowrap><div align="center"><input type="text" name="reasonText" id="reasonText" value="" size="60" maxlength="60"/></div></td>
      <td class="colColor2" ><div align="center"><input type="submit" name="submitAdd" id="submitAdd" value="<?php echo $LAN_BANREASONS009 ?>"/></div></td>
      <td class="colColor1"></td>
      </form>
    </tr>
    <?php
      if($error) {
      ?><tr><td class="colColor1" colspan="9"><span class="error"><?php echo $LAN_BANREASONS010 ?></span></td></tr><?php
      }
    ?>
  </table>
</div>
<br/>
<br/>
<input type="button" value="<?php echo $LAN_BANREASONS011 ?>" class="button" onclick="location.href='index.php?page=uploadBanReasons&adminPage=1'" />

<?php
}
?>
