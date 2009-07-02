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

$lengthQueries = new LengthQueries();
$banLengths = $lengthQueries->getLengthList();

echo "\"GlobalBan_Length\"";
echo "{";

// Cycle through each user and create the keygroup
foreach($banLengths as $banLength) {

  $readable = $banLength->getLength() . " " . $banLength->getTimeScale();

  if($banLength->getLength() == 0) {
    $readable = "Permanente";
  } else if($banLength->getLength() == 1) {
    if($banLength->getTimeScale() == "minutes") {
      $readable = $banLength->getLength() . " minuto";
    } else if($banLength->getTimeScale() == "hours") {
      $readable = $banLength->getLength() . " hora";
    } else if($banLength->getTimeScale() == "days") {
      $readable = $banLength->getLength() . " dia";
    } else if($banLength->getTimeScale() == "weeks") {
      $readable = $banLength->getLength() . " semana";
    } else if($banLength->getTimeScale() == "months") {
      $readable = $banLength->getLength() . " mes";
    }
  } else {
    if($banLength->getTimeScale() == "minutes") {
      $readable = $banLength->getLength() . " minutos";
    } else if($banLength->getTimeScale() == "hours") {
      $readable = $banLength->getLength() . " horas";
    } else if($banLength->getTimeScale() == "days") {
      $readable = $banLength->getLength() . " dias";
    } else if($banLength->getTimeScale() == "weeks") {
      $readable = $banLength->getLength() . " semanas";
    } else if($banLength->getTimeScale() == "months") {
      $readable = $banLength->getLength() . " meses";
  	 }
  }
  if($banLength->getLength() != 0) {
	  echo "  \"".$banLength->getId()."\"";
	  echo "  {";
	  echo "    \"length\"        \"".$banLength->getLength()."\"";
	  echo "    \"timeScale\"     \"".$banLength->getTimeScale()."\"";
	  echo "    \"readable\"      \"".$readable."\"";
	  echo "  }";
  }
}
	  echo "  \"1\"";
	  echo "  {";
	  echo "    \"length\"        \"0\"";
	  echo "    \"timeScale\"     \"minutes\"";
	  echo "    \"readable\"      \"Permanente\"";
	  echo "  }";
?>
}
