CREATE TABLE `secretbox_count` (
	`user_id` INT(11) NOT NULL,
	`secretbox_id` INT(11) NOT NULL,
	`count` INT(11) NOT NULL DEFAULT '0',
	`last_scout` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE INDEX `Index 1` (`user_id`, `secretbox_id`)
)
COLLATE='utf8_general_ci'