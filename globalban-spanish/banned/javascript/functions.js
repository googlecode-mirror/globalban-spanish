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

// This highlights a row in a datatable when the user mouseovers it.
function rowHighlight(table) {
    var i=0;
    while(document.getElementById(table) !== null) {
        var table = document.getElementById(table);
        var rows = table.getElementsByTagName("tr");
        for(j=1; j < rows.length; j++){
           rows[j].onmouseover = function() { this.className = "highlightRow"; };
           rows[j].onmouseout = function() { this.className = ""; };
        }
        i++;
    }
}

// This gets the background-color of an element
function getStyleBackgroundColor(el) {
  el = document.getElementById(el);
  if(el.currentStyle) {
    return el.currentStyle.backgroundColor;
  }
  if(document.defaultView) {
    return document.defaultView.getComputedStyle(el, '').getPropertyValue("background-color");
  }
  return "Don\'t know how to get color";
}

// This gets the foreground color of an element
function getStyleColor(el){
  el = document.getElementById(el);
  if(el.currentStyle) {
    return el.currentStyle.backgroundColor;
  }
  if(document.defaultView) {
    return document.defaultView.getComputedStyle(el, '').getPropertyValue("background-color");
  }
  return "Don\'t know how to get color";
}

function removeCharacters(element) {

  searchValue = element.value.replace(/\D/g,"");
  element.value = searchValue;
}

function removeSpecialCharacters(element) {
  if(element.value.indexOf("\"") > -1 || element.value.indexOf("\\") > -1) {
    alert("The following characters are not allowed: \" \\ \{ \}");
  }
  searchValue = element.value.replace(/\"/g, "");
  searchValue = searchValue.replace(/\\/g, "");
  element.value = searchValue;
}
