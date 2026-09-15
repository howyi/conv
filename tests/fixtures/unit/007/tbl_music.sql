CREATE TABLE `tbl_music` (
  `music_id` int(10) NOT NULL COMMENT 'Unique music ID',
  `music_name` varchar(22) COLLATE utf8mb4_bin NOT NULL COMMENT 'Music name',
  `description` varchar(22) COLLATE utf8mb4_bin NOT NULL COMMENT 'Music description',
  `released_at` datetime NOT NULL COMMENT 'Music released date',
  PRIMARY KEY (`music_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='Music management table'
