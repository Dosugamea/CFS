ALTER TABLE `live_precise_log`
	ADD COLUMN `trigger_log` MEDIUMTEXT NOT NULL AFTER `deck_info`;
ALTER TABLE `live_precise_log`
	CHANGE COLUMN `max_combo` `max_combo` SMALLINT NOT NULL AFTER `precise_list`;
ALTER TABLE `live_precise_log`
	ADD COLUMN `tap_adjust` SMALLINT NOT NULL DEFAULT '0' AFTER `trigger_log`,
	ADD COLUMN `live_setting` MEDIUMTEXT NULL DEFAULT NULL AFTER `tap_adjust`;