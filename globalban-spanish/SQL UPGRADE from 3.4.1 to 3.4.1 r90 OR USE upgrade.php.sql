ALTER TABLE `gban_ban` ADD `kick_counter` int(10) NOT NULL DEFAULT '0' AFTER `add_date`;
ALTER TABLE `gban_ban_history` ADD `kick_counter` int(10) NOT NULL DEFAULT '0' AFTER `add_date`;