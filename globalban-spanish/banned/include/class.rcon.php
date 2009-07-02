<?php
/*
	CS:S Rcon PHP Class - code by 1FO|zyzko 01/12/2005
	www.1formatik.com - www.1fogames.com
    --------------------------------------------------
*/
define("SERVERDATA_EXECCOMMAND",02);
define("SERVERDATA_AUTH",03);

class rcon {
    var $Password;
    var $Host;
    var $Port = 27015;
    var $_Sock = null;
    var $_Id = 0;
    var $valid = false;

    function __construct($Host,$Port,$Password) {
  	  return $this->init($Host,$Port,$Password);
    }

    function rcon ($Host,$Port,$Password) {
    	return $this->init($Host,$Port,$Password);
    }
    
    function init($Host,$Port,$Password) {
      $this->Password = $Password;
    	$this->Host = $Host;
    	$this->Port = $Port;
    	$this->_Sock = @fsockopen($this->Host,$this->Port, $errno, $errstr, 30);// or
    	    //die("Unable to open the port: $errstr ($errno)\n"+$this->Host+":"+$this->Port);
      if($this->_Sock) {
        $this->_Set_Timeout($this->_Sock,2,500);
        $this->valid = true;
      }
    }
    
    function isValid() {
      return $this->valid;
    }
    
    function Auth() {
    	$PackID = $this->_Write(SERVERDATA_AUTH,$this->Password);
    	
    	$ret = $this->_PacketRead();
    	if ($ret[1]['ID'] == -1) {
        return 0;
    	} else {
        return 1;
      }
    }

    function _Set_Timeout(&$res,$s,$m=0) {
    	if (version_compare(phpversion(),'4.3.0','<')) {
    	    return socket_set_timeout($res,$s,$m);
    	}
    	return stream_set_timeout($res,$s,$m);
    }

    function _Write($cmd, $s1='', $s2='') {
    	$id = ++$this->_Id;
    	$data = pack("VV",$id,$cmd).$s1.chr(0).$s2.chr(0);
    	$data = pack("V",strlen($data)).$data;
    	fwrite($this->_Sock,$data,strlen($data));
    	return $id;
    }

    function _PacketRead() {
    	$retarray = array();
    	while ($size = @fread($this->_Sock,4)) {
  	    $size = unpack('V1Size',$size);
  	    if ($size["Size"] > 4096) {
    		  $packet = "\x00\x00\x00\x00\x00\x00\x00\x00".fread($this->_Sock,4096);
    	  } else {
    		  $packet = fread($this->_Sock,$size["Size"]);
    	  }
    	    array_push($retarray,unpack("V1ID/V1Reponse/a*S1/a*S2",$packet));
    	}
    	return $retarray;
    }

    function Read() {
    	$Packets = $this->_PacketRead();	
    	foreach($Packets as $pack) {
    	  if (isset($ret[$pack['ID']])) {
      		$ret[$pack['ID']]['S1'] .= $pack['S1'];
      		$ret[$pack['ID']]['S2'] .= $pack['S1'];
    	  } else {
      		$ret[$pack['ID']] = array(
  					'Reponse' => $pack['Reponse'],
  					'S1' => $pack['S1'],
  					'S2' =>	$pack['S2'],
  				   );
      	  }
    	}
    	return $ret;
    }

    /**
     * Deprecated
     */
    function sendCommand($Command) {
    	$Command = '"'.trim(str_replace(' ','" "', $Command)).'"';
    	$this->_Write(SERVERDATA_EXECCOMMAND,$Command,'');
    }

    /**
     * Deprecated
     */
    function rconCommand($Command) {
    	$this->sendCommand($Command);
    	$ret = $this->Read();
    	return $ret[$this->_Id]['S1'];
    }
    
    /**
     * Send an rcon command the server.  The command must be properly formatted before
     * sending to this method.  This means that variables that require quotes must have
     * quotes put around them.
     * Ex. $command = "kickid ". "\"".$steamId."\" ".$banReason
     */
    function sendRconCommand($command) {
      $this->_Write(SERVERDATA_EXECCOMMAND,$command,'');
      $ret = $this->Read();
      return $ret[$this->_Id]['S1'];
    }
    
    /**
     * This method is used to kick a user.  It requires a steam id and a kick reason
     */         
    function kickUser($steamId, $banReason) {
      $kick = "kickid";
      $command = $kick." \"".$steamId."\" ".$banReason;
      $this->_Write(SERVERDATA_EXECCOMMAND,$command,'');
      $ret = $this->Read();
    	return $ret[$this->_Id]['S1'];
    }
}
?>
