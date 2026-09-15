CREATE TABLE `tbl_user` (
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'User name',
  `age` int(11) DEFAULT NULL COMMENT 'User age',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='User management table'
