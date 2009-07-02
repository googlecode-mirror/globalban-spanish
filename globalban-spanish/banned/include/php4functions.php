<?php
/**
 * This file contains PHP5 functions that are not available in PHP4
 */
if (!function_exists('str_split')){
	function str_split($string, $split_length=1){

		if ($split_length < 1) {
			return false;
		}

		for ($pos=0, $chunks = array(); $pos < strlen($string); $pos+=$split_length) {
			$chunks[] = substr($string, $pos, $split_length);
		}
			return $chunks;
		}
}

//PHP 4.2.x Compatibility function
if (!function_exists('file_get_contents')) {
      function file_get_contents($filename, $incpath = false, $resource_context = null)
      {
          if (false === $fh = fopen($filename, 'rb', $incpath)) {
              trigger_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
              return false;
          }

          clearstatcache();
          if ($fsize = @filesize($filename)) {
              $data = fread($fh, $fsize);
          } else {
              $data = '';
              while (!feof($fh)) {
                  $data .= fread($fh, 8192);
              }
          }

          fclose($fh);
          return $data;
      }
  }
?>
