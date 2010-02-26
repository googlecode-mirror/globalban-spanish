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

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_banLengths.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_banLengths.php");
?>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
function deleteVerify(id, length, timeScale) {
  if(confirm("<?php echo $LAN_BANLENGHTS_001; ?> " + length + " " + timeScale + "<?php echo $LAN_BANLENGHTS_019; ?>")) {
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
    <div><b><?php echo $LAN_BANLENGHTS_002; ?></b></div>
  </div>
  <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <th class="colColor1" width="1%" nowrap><div align="center"><?php echo $LAN_BANLENGHTS_003; ?></div></th>
      <th class="colColor2" width="1%" nowrap><div align="center"><?php echo $LAN_BANLENGHTS_004; ?></div></th>
      <th class="colColor1" width="1%" nowrap><div align="center"><?php echo $LAN_BANLENGHTS_005; ?></div></th>
      <th class="colColor2" nowrap></th>
    </tr>
    <?php foreach($banLengths as $banLength) {
          $lengthId = $banLength->getId();
          $length = $banLength->getLength();
          $timeScale = $banLength->getTimeScale();
          if($length == 0) {
            $length = "";
            $timeScale = $LAN_BANLENGHTS_006;
          }
          ?>
          <tr>
            <td class="colColor1" width="1%" nowrap><div align="center"><?php echo $length ?></div></td>
            <td class="colColor2" width="1%" nowrap><div align="center"><?php echo $timeScale ?></div></td>
            <td class="colColor1" width="1%" style="cursor:pointer;" onclick="deleteVerify('<?php echo $lengthId ?>', '<?php echo $length ?>', '<?php echo $timeScale ?>');">
            <form action="index.php?page=banLengths&adminPage=1" id="deleteLength<?php echo $lengthId ?>" name="deleteLength<?php echo $lengthId ?>" method="POST">
              <input type="hidden" name="lengthId" id="lengthId" value="<?php echo $lengthId ?>"/>
              <input type="hidden" name="deleteLength" value="1">
              <div align="center"><img src="images/trash-full.png"/></div>
            </form>
            </td>
          <th class="colColor2" nowrap></th>
          </tr>
    <?php } ?>

    <tr>
      <td class="colColor1">&nbsp;</td>
      <td class="colColor2"></td>
      <td class="colColor1"></td>
    <th class="colColor2"></th>
    </tr>

    </table>
</div>
    
<br/>
<br/>
    
<div class="tborder">
  <div id="tableHead">
    <div><b><?php echo $LAN_BANLENGHTS_007; ?></b></div>
  </div>
    <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">

    <tr>
      <form action="index.php?page=banLengths&adminPage=1" method="POST" onsubmit="return formVerify();">
      <td class="colColor1" width="1%" nowrap><?php echo $LAN_BANLENGHTS_008; ?></td>
      <td class="colColor1" nowrap><input type="text" id="length" name="length" value="" size="5" maxlength="5" onkeyup="removeCharacters(this)"/></td>
    </tr>
    <tr>
      <td class="colColor2" width="1%" nowrap><?php echo $LAN_BANLENGHTS_009; ?></td>
      <td class="colColor2" nowrap>
        <select id="timeScale" name="timeScale">
          <option value="minutes"><?php echo $LAN_BANLENGHTS_010; ?></option>
          <option value="hours"><?php echo $LAN_BANLENGHTS_011; ?></option>
          <option value="days"><?php echo $LAN_BANLENGHTS_012; ?></option>
          <option value="weeks"><?php echo $LAN_BANLENGHTS_013; ?></option>
          <option value="months"><?php echo $LAN_BANLENGHTS_014; ?></option>
        </select>
      </td>
    <tr>
      <td class="colColor1" colspan="2"><input type="submit" name="submitAdd" id="submitAdd" value="<?php echo $LAN_BANLENGHTS_015; ?>"/></td>
      </form>
    </tr>
    <?php
      if($error) {
      ?><tr><td class="colColor1" colspan="9"><span class="error"><?php echo $LAN_BANLENGHTS_016; ?></span></td></tr><?php
      }
    ?>
  </table>
</div>

<h5><?php echo $LAN_BANLENGHTS_017; ?></h5>
<input type="button" value="<?php echo $LAN_BANLENGHTS_018; ?>" class="button" onclick="location.href='index.php?page=uploadBanLengths&adminPage=1'" />

<?php
}
?>
