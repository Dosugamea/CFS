ALTER TABLE `tmp_duty_user_room`
	ALTER `deck_id` DROP DEFAULT;
ALTER TABLE `tmp_duty_user_room`
	CHANGE COLUMN `deck_id` `deck_id` INT(11) NULL AFTER `pos_id`;
