CREATE TABLE `event_point` (
	`user_id` INT(11) NOT NULL,
	`event_id` INT(2) NOT NULL,
	`event_point` INT(11) NOT NULL DEFAULT '0',
	`score_point` INT(11) NOT NULL DEFAULT '0',
	UNIQUE INDEX `UNIQUE` (`user_id`, `event_id`)
)
COLLATE='utf8_general_ci'