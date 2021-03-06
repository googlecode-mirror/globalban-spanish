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
 * @subpackage Steam Community
 */

require_once STEAM_CONDENSER_PATH . 'steam/community/tf2/TF2Class.php';

/**
 * Represents the stats for the Team Fortress 2 engineer class for a specific
 * user
 * @package    Steam Condenser (PHP)
 * @subpackage Steam Community
 */
class TF2Engineer extends TF2Class
{
	/**
	 * Creates a new instance of TF2Engineer based on the assigned XML data
	 * @param $classData
	 */
	public function __construct($classData)
	{
		parent::__construct($classData);

		$this->maxBuildingsBuilt = (int) $classData->ibuildingsbuilt;
		$this->maxTeleports      = (int) $classData->inumteleports;
		$this->maxSentryKills    = (int) $classData->isentrykills;
	}
}
?>
