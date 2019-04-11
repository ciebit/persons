CREATE TABLE `cb_persons` (
  `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `nickname` varchar(200) DEFAULT NULL,
  `description` varchar(300) DEFAULT NULL,
  `birth_date` datetime DEFAULT NULL,
  `type` enum('natural','legal') NOT NULL,
  `educational_level` tinyint(1) UNSIGNED DEFAULT NULL,
  `gender` tinyint(1) UNSIGNED DEFAULT NULL,
  `marital_status` tinyint(1) UNSIGNED DEFAULT NULL,
  `slug` varchar(300) DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='version:1.0';
