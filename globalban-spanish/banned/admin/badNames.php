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

require_once(ROOTDIR."/include/database/class.BadNameQueries.php");
require_once(ROOTDIR."/include/objects/class.BadName.php");

// Only full power admins can access this page
if($fullPower) {

$badNameQueries = new BadNameQueries();

// The user is wanting to add a new bad name
if(isset($_POST['add'])) {
  $filter = 0;
  $kick = 0;
  if(isset($_POST['kick'])) {
    $kick = 1;
  }
  if(isset($_POST['filter'])) {
    $filter = 1;
  }
  $badNameQueries->addBadName($_POST['badName'], $filter, $kick);
}

// The user is wanting to delete a badname
if(isset($_POST['deleteBadName'])) {
    $badNameQueries->removeBadName($_POST['idToDelete']);
}

// Get list of bad names
$badNames = $badNameQueries->getBadNames();
?>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<div class="tborder">
  <div id="tableHead">
    <div><b>Add Bad Name/Word</b></div>
  </div>
  <form action="index.php?page=badNames&adminPage=1" method="POST">
  <table class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <td class="rowColor1" width="1%" valign="top" nowrap>
        <input type="text" id="badName" name="badName" value="" size="40" maxlength="40"/>
        <span onmouseover="Tip('Check to filter this name/word from the Ban List and Demo pages.', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('badNamesTable'));">
          <input type="checkbox" id="filter" name="filter" value="1"/>Filter
        </span>
        <span onmouseover="Tip('Check to kick anyone that has this name/word in their name.', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('badNamesTable'));">
          <input type="checkbox" id="kick" name="kick" value="1">Kick
        </span>
      </td>
      <td class="rowColor1" nowrap>
        <input type="submit" id="add" name="add" value="Add"/>
      </td>
    </tr>
  </table>
  </form>
</div>

<br/>

<div class="tborder">
  <div id="tableHead">
    <div><b>Bad Names/Words</b></div>
  </div>
  <table id="badNamesTable" class="bordercolor" width="100%" cellspacing="1" cellpadding="5" border="0" style="margin-top: 1px;">
    <tr>
      <th class="rowColor1">Name/Word</th>
      <th class="rowColor1">Filter</th>
      <th class="rowColor1">Kick</th>
      <th class="rowColor1">Delete</th>
    </tr>
    <?php
    $i = 0;
    foreach($badNames as $badName) {
      $rowStyle = "rowColor1";
      if($i%2 == 0) {
        $rowStyle = "rowColor2";
      }
    ?>
      <tr>
        <td class="<?php echo $rowStyle?>"><?php echo $badName->getBadName()?></td>
        <td class="<?php echo $rowStyle?>" id="filter:<?php echo $badName->getId()?>">
          <?php if($badName->getFilter()) {
            ?>
              <img src="images/tick.png"
                   onclick="changeBadNameFilter(<?php echo $badName->getId()?>, <?php echo $badName->getFilter()?>)" style="cursor:pointer;"
                   onmouseover="Tip('Click stop filtering this bad name/word.', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('badNamesTable'));"/>
            <?php
          } else {
            ?>
              <img src="images/cross.png"
                   onclick="changeBadNameFilter(<?php echo $badName->getId()?>, <?php echo $badName->getFilter()?>)" style="cursor:pointer;"
                   onmouseover="Tip('Click start filtering this bad name/word.', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('badNamesTable'));"/>
            <?php
          }?>
        </td>
        <td class="<?php echo $rowStyle?>"  id="kick:<?php echo $badName->getId()?>">
          <?php if($badName->getKick()) {
            ?>
              <img src="images/tick.png"
                   onclick="changeBadNameKick(<?php echo $badName->getId()?>, <?php echo $badName->getFilter()?>)" style="cursor:pointer;"
                   onmouseover="Tip('Click to stop kicking people that have this bad name/word in their name.', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('badNamesTable'));"/>
            <?php
          } else {
            ?>
              <img src="images/cross.png"
                   onclick="changeBadNameKick(<?php echo $badName->getId()?>, <?php echo $badName->getFilter()?>)" style="cursor:pointer;"
                   onmouseover="Tip('Click to start kicking people that have this bad name/word in their name.', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('badNamesTable'));"/>
            <?php
          }?>
        </td>
        <td class="<?php echo $rowStyle?>">
          <form action="index.php?page=badNames&adminPage=1" method="POST" onsubmit="return confirm('Are you sure you wish to delete this item?')">
            <input type="hidden" id="idToDelete" name="idToDelete" value="<?php echo $badName->getId()?>"/>
            <input type="submit" id="deleteBadName" name="deleteBadName" value=""
                   style="background:transparent url(images/trash-full.png) no-repeat scroll 0%; border:0px; margin-bottom:3px;cursor:pointer;"
                   onmouseover="Tip('Click to delete bad name/word from the list.', SHADOW, true, FADEIN, 300, FADEOUT, 300, BGCOLOR, getStyleBackgroundColor('container'), BORDERCOLOR, getStyleBackgroundColor('badNamesTable'));"/>
          </form>
        </td>
      </tr>
    <?php
      $i++;
    }
    ?>
  </table>
</div>

<h5>Note: Avoid using short words/names for kicking.  For example, adding "a" will kick anyone with an "a" in their name.</h5>
<?php
}
?>
