CREATE TABLE `sushi_log` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'auto_increment ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `sushi_id` int(11) NOT NULL COMMENT 'Sushi ID',
  `date` datetime NOT NULL COMMENT 'Eat date',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='Sushi log table';