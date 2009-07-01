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

require_once(ROOTDIR."/include/database/class.LengthQueries.php");
require_once(ROOTDIR."/include/objects/class.Length.php");

?>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
function deleteVerify(id, length, timeScale) {
  if(confirm("Do you really want to delete the length: " + length + " " + timeScale + "?")) {
    document.getElementById("deleteLength"+id).submit();
  }
}
</script>
<?php

$lengthQueries = new LengthQueries();

$error = false;

// If this is set, then that means a server is being added
if(isset($_POST['submitAdd'])) {

  $length = $_POST['length'];
  $timeScale = $_POST['timeScale'];

  $newLengthId = $lengthQueries->addLength($length, $timeScale);

  if($newLengthId < 1) {
    $error = true;
  }
}

// If a reason is being deleted
if(isset($_POST['deleteLength'])) {
  $lengthQueries->deleteLength($_POST['lengthId']);
}
// Get list of reasons
$banLengths = $lengthQueries->getLengthList();

if($fullPower) {
?>
<div class="tborder">
  <div id="tableHead">
    <div><b>Ban Lengths</b></div>
  </div>
  <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <th class="colColor1" width="1%" nowrap>Length</th>
      <th class="colColor2" width="1%" nowrap>Time Scale</th>
      <th class="colColor1" nowrap align="left">Delete</th>
    </tr>
    <?php foreach($banLengths as $banLength) {
          $lengthId = $banLength->getId();
          $length = $banLength->getLength();
          $timeScale = $banLength->getTimeScale();
          if($length == 0) {
            $length = "Forever";
            $timeScale = "Forever";
          }
          ?>
          <tr>
            <td class="colColor1" width="1%" nowrap><?=$length?></td>
            <td class="colColor2" nowrap><?=$timeScale?></td>
            <td class="colColor1" style="cursor:pointer;" onclick="deleteVerify('<?=$lengthId?>', '<?=$length?>', '<?=$timeScale?>');">
            <form action="index.php?page=banLengths&adminPage=1" id="deleteLength<?=$lengthId?>" name="deleteLength<?=$lengthId?>" method="POST">
              <input type="hidden" name="lengthId" id="lengthId" value="<?=$lengthId?>"/>
              <input type="hidden" name="deleteLength" value="1">
              <img src="images/trash-full.png"/>
            </form>
            </td>
          </tr>
    <?php } ?>

    <tr>
      <td class="colColor1" width="1%" nowrap>&nbsp;</td>
      <td class="colColor2" nowrap></td>
      <td class="colColor1"></td>
    </tr>

    </table>
</div>
    
<br/>
<br/>
    
<div class="tborder">
  <div id="tableHead">
    <div><b>Add New Ban Length</b></div>
  </div>
    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">

    <tr>
      <form action="index.php?page=banLengths&adminPage=1" method="POST" onsubmit="return formVerify();">
      <td class="colColor1" width="1%" nowrap>Length:</td>
      <td class="colColor1" nowrap><input type="text" id="length" name="length" value="" size="5" maxlength="5" onkeyup="removeCharacters(this)"/></td>
    </tr>
    <tr>
      <td class="colColor2" width="1%" nowrap>Time Scale:</td>
      <td class="colColor2" nowrap>
        <select id="timeScale" name="timeScale">
          <option value="minutes">minutes</option>
          <option value="hours">hours</option>
          <option value="days">days</option>
          <option value="weeks">weeks</option>
          <option value="months">months</option>
        </select>
      </td>
    <tr>
      <td class="colColor1" colspan="2"><input type="submit" name="submitAdd" id="submitAdd" value="Add Ban Length"/></td>
      </form>
    </tr>
    <?php
      if($error) {
      ?><tr><td class="colColor1" colspan="9"><span class="error">Ban Length Already Exists</span></td></tr><?php
      }
    ?>
  </table>
</div>

<h5>Note: 0 minutes = Perma Ban</h5>
<input type="button" value="Save Ban Lengths to Servers" class="button" onclick="location.href='index.php?page=uploadBanLengths&adminPage=1'" />

<?php
}
?>
