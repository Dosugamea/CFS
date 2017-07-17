CREATE TABLE `log` (
	`command_num` TEXT NOT NULL,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`module` TEXT NOT NULL,
	`action` TEXT NOT NULL,
	`request` BLOB NOT NULL,
	`response` BLOB NOT NULL,
	PRIMARY KEY (`command_num`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;