ALTER TABLE `auth_log`
	ADD COLUMN `client_version` TEXT NULL DEFAULT NULL AFTER `ip`,
	ADD COLUMN `bundle_version` TEXT NULL DEFAULT NULL AFTER `client_version`;