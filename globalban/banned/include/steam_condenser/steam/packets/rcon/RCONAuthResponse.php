<?php
/**
 * This code is free software; you can redistribute it and/or modify it under
 * the terms of the new BSD License.
 *
 * Copyright (c) 2008-2009, Sebastian Staudt
 *
 * @author     Sebastian Staudt
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package    Steam Condenser (PHP)
 * @subpackage RCONAuthResponse
 */

require_once STEAM_CONDENSER_PATH . 'steam/packets/rcon/RCONPacket.php';

class RCONAuthResponse extends RCONPacket
{
	public function __construct($requestId)
	{
		parent::__construct($requestId, RCONPacket::SERVERDATA_AUTH_RESPONSE);
	}
}
?>
