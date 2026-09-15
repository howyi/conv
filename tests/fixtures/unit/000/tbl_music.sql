CREATE TABLE `tbl_music` (
  `music_id` int(10) NOT NULL COMMENT 'Unique music ID',
  `name` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT 'Music name',
  `released_at` datetime NOT NULL COMMENT 'Music released date',
  PRIMARY KEY (`music_id`,`name`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='Music management table'
