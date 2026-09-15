CREATE TABLE `tbl_country2` (
  `country_id` int(11) NOT NULL COMMENT 'Country id',
  `country_name` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT 'Country Name',
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='Country table'
