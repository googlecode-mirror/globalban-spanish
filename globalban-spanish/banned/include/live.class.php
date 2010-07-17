<?php
/********************************************************************
 * This is a very simplified version of PsychoQuery from            *
 * PsychoStats. Do not use it. Use the PsychoQuery Class instead    *
 * (bundled with psychostats)                                       *
 ********************************************************************/

define('INFO_RESPONSE_HL1',0x6D);
define('CHALLENGE_RESPONSE',0x41);

class LIVE {
	function getInfo($address,$port) {
		$ret = array();

		$s = fsockopen("udp://".$address,$port,$errno,$errstring,1);
		stream_set_timeout($s,1); // 1 second timeout on read/write operations

		fwrite($s,"\xFF\xFF\xFF\xFF\x57Source Engine Query");

		$packet = fread($s,1260);
		

		echo ($packet);
		
		//Left 6 characters are the standard header
		$parr = explode("\x00",substr($packet,6));
		
		print_r ($parr);

		if(ord($packet[4]) == INFO_RESPONSE_HL1) {
      //HL1 Response
			//Skip the header, and ip:port combo
			$packet = substr($packet,strpos($packet,"\x00")+1);

			$parr = explode("\x00",$packet);
			$ret['hostname'] = $parr[0];

			$ret['map'] = $parr[1];
			$ret['gamename'] = $parr[2];
			$ret['gamedesc'] = $parr[3];

			$packet = substr($packet,strlen($ret['hostname'])+1+strlen($ret['map'])+1+strlen($ret['gamename'])+1+strlen($ret['gamedesc'])+1);
			$ret['numplayers'] = ord($packet[0]);
			$ret['maxplayers'] = ord($packet[1]);
			//$version = ord($packet[2]);
			$ret['dedicated'] = $packet[3];
			$ret['os'] = $packet[4];
			$ret['password'] = ord($packet[5]);
			if(ord($packet[6])) {
        //Skip mod info
				$packet = substr($packet,strpos($packet,"\x00")+1);
				$packet = substr($packet,strpos($packet,"\x00")+3);
			} else {
				$packet = substr($packet,6);
			}
			$ret['secure'] = ord($packet[0]);
			$ret['botcount'] = ord($packet[1]);
		} else {	//HL2 Response
			$ret['hostname'] = $parr[0];

			$ret['map'] = $parr[1];
			$ret['gamename'] = $parr[2];
			$ret['gamedesc'] = $parr[3];

			$packet = substr($packet,6+strlen($ret['hostname'])+1+strlen($ret['map'])+1+strlen($ret['gamename'])+1+strlen($ret['gamedesc'])+1+2);
			$ret['numplayers'] = ord($packet[0]);
			$ret['maxplayers'] = ord($packet[1]);
			$ret['botcount'] = ord($packet[2]);
			$ret['dedicated'] = ord($packet[3]);
			$ret['os'] = $packet[4];
			$ret['password'] = ord($packet[5]);
			$ret['secure'] = $packet[6];
		}
		return $ret;
	}

	function getPlayers($address,$port) {
		set_magic_quotes_runtime(0);
		$ret = array();

		$s = fsockopen("udp://".$address,$port,$errno,$errstring,1);
		stream_set_timeout($s,1); // 1 second timeout on read/write operations

		fwrite($s,"\xFF\xFF\xFF\xFF\x57"); //Get challenge #
		$packet = fread($s,1024);

		$chalId = $this->getChallenge($packet);

		fwrite($s,"\xFF\xFF\xFF\xFF\x55".$chalId);

		$packet = fread($s,2048);

		$packet = substr($packet,5);

		$nump = ord($packet[0]);
		$packet = substr($packet,1);

		for ($i=0;$i<$nump;$i++) {
			$temp = array();

			$temp['index'] = ord($packet[0]);
			$packet = substr($packet,1);

			$temp['name'] = substr($packet,0,strpos($packet,"\0"));
			$packet = substr($packet,strlen($temp['name'])+1);

			$temp['kills'] = ord(substr($packet,0,4));
			$packet = substr($packet,4);

			$temp['time'] = $this->SecondsToString((ord(substr($packet,0,4))), true);
			if(empty($temp['time'])) {
        $temp['time'] = "BOT";
      }

			$packet = substr($packet,4);


			if($temp['name'])
				array_push($ret,$temp);
			//return substr($packet,0,strpos($packet,"\0"));
		}
		return $ret;
	}
	
	function getRules($address,$port) {
		set_magic_quotes_runtime(0);
		$ret = array();

		$s = fsockopen("udp://".$address,$port,$errno,$errstring,1);
		stream_set_timeout($s,1); // 1 second timeout on read/write operations

		fwrite($s, pack("V", -1) . 'W'); //Get challenge #
		$packet = fread($s,1024);

		$chalId = $this->getChallenge($packet);

		$command = pack("V", -1) . 'V' . pack("V", $chalId);
		fwrite($s, pack("V", -1) . 'V' . pack("V", $chalId));

		$packet = fread($s,2048);

		$packet = substr($packet,7);

		$parr = explode("\x00",$packet);

		for ($i=0;$i<count($parr);$i+=2)
		{
			$rule = array();
			$rule['name'] = $parr[$i];
			$rule['value'] = $parr[$i+1];
			array_push($ret,$rule);
		}

		return $ret;
	}

	function getChallenge($packet) {
		if (ord($packet[4]) == CHALLENGE_RESPONSE)
		{
			return substr($packet,5);
		}
		return "\xFF\xFF\xFF\xFF";
	}

	function getbyte($raw) {
		if (empty($raw)) return '';
		$byte = substr($raw, 0, 1);
		$raw = substr($raw, 1);
		return ord($byte);
	}

	function getshort($raw) {
		if (empty($raw)) return '';
		$lo = $this->getbyte($raw);
		$hi = $this->getbyte($raw);
		$short = ($hi << 8) | $lo;
		return $short;
	}

	function getlong($raw) {
		if (empty($raw)) return '';
		$lo = $this->getshort($raw);
		$hi = $this->getshort($raw);
		$long = ($hi << 16) | $lo;
		return $long;
	}
	
	function SecondsToString($sec, $textual=true) {
  	$div = array( 2592000, 604800, 86400, 3600, 60, 1 );
  	if($textual)
  	{
  		$desc = array ('mo','wk','d','hr','min','sec');
  		$ret = "";
  		for ($i=0;$i<count($div);$i++)
  		{
  			if (($cou = round($sec / $div[$i])) >= 1)
  			{
  				$ret .= $cou.' '.$desc[$i].', ';
  				$sec %= $div[$i];
  			}
  		}
  		$ret = substr($ret,0,strlen($ret)-2);
  	}else{
  		$hours = floor ($sec / 60 / 60);
  		$sec -= $hours * 60*60;
  		$mins = floor ($sec / 60);
  		$secs = $sec % 60;
  		$ret = $hours . ":" . $mins . ":" . $secs;
  	}
  	return $ret;
  }
}
?>