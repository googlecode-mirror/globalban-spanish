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
ob_start();

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_header.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/French/lan_header.php");

function my_explode($delim, $str, $lim = 1) {
    if ($lim > -2) return explode($delim, $str, abs($lim));

    $lim = -$lim;
    $out = explode($delim, $str);
    if ($lim >= count($out)) return $out;

    $out = array_chunk($out, count($out) - $lim + 1);

    return array_merge(array(implode($delim, $out[0])), $out[1]);
}

$siteLogo = my_explode('.', $config->siteLogo , -2);
?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo $config->siteName ?><?php echo $LANHEAD_001; ?></title>
    <link rel="icon" href="images/favicon.ico" type="image/vnd.microsoft.icon">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/banned.css" />
    <!--<link rel="stylesheet" type="text/css" href="css/print.css" media="print" /> -->
    <script src="javascript/functions.js" language="javascript" type="text/javascript"></script>
    <script src="javascript/jquery-1.2.6.min.js" language="javascript" type="text/javascript"></script>
    <script src="javascript/ui.core.js" language="javascript" type="text/javascript"></script>
    <script src="javascript/ui.accordion.js" language="javascript" type="text/javascript"></script>
    <script src="javascript/ui.tabs.js" language="javascript" type="text/javascript"></script>
    <script type="text/javascript"><!--//--><![CDATA[//><!--

    sfHover = function() {
    	var sfEls = document.getElementById("nav").getElementsByTagName("LI");
    	for (var i=0; i<sfEls.length; i++) {
    		sfEls[i].onmouseover=function() {
    			this.className+=" sfhover";
    		}
    		sfEls[i].onmouseout=function() {
    			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
    		}
    	}
    }
    if (window.attachEvent) window.attachEvent("onload", sfHover);

    //--><!]]></script>

    <!--[if IE]>
    <style type="text/css" media="screen">
    #menu {margin-bottom:20px}
    </style>
    <![endif]-->

  </head>
  <!-- -----------------------------------------------------------------------
      Special Thanks to: ub|Delta One - http://www.urbanbushido.net/
                         tnbporsche911 - http://www.tnbsourceclan.net/
                         
      Default GlobalBan logo designed with template from http://www.freepsd.com/logo
      Navigation code from http://www.htmldog.com/articles/suckerfish/dropdowns/
   ----------------------------------------------------------------------- -->
<body id="body">
<script type="text/javascript" src="javascript/wz_tooltip.js"></script>
<div id="container">
 <div align="center">
    <?PHP
    if ($siteLogo[1] == "swf") {
        ?>
        <script type="text/javascript" src="javascript/flash.js"></script>
        <script type="text/javascript">
            show_flash("931", "210", "images/<?php echo $config->siteLogo?>", "", "team=Global Ban");
        </script>
        <?php
    }else{
        ?>
        <img alt="<?php echo $config->siteName?>" src="images/<?php echo $config->siteLogo?>"/>
        <?php   
    }
    ?>
</div>
  <p>&nbsp;</p>
  <?php include("include/navigation.php")?>
