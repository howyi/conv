CREATE TABLE `tbl_user_address` (
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `address_line` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT 'User address',
  `city` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT 'User city',
  `zip_code` int(11) NOT NULL COMMENT 'User zip code',
  `country_id` int(11) NOT NULL COMMENT 'Country id',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='User address management table'
