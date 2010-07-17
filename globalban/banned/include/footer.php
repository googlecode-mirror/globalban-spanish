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

$lan_file = ROOTDIR.'/languages/'.$LANGUAGE.'/lan_footer.php';
include(file_exists($lan_file) ? $lan_file : ROOTDIR."/languages/English/lan_footer.php");

?>
<div id="footer">
<a href='http://code.google.com/p/globalban-spanish/'><?php echo "GlobalBan  ".$version; ?></a> - <?php echo $copyright; ?>
<?php if(!$config->enableSmfIntegration) { ?> - <a href="index.php?page=login&lg=<?php echo $LANGUAGE; ?>"><span><?php echo $LANFOOTER_001; ?></span></a><?php } ?>
<br/><?php echo $translators; ?>
</div>
</div>
</body>
</html>
<?php ob_flush(); ?>
