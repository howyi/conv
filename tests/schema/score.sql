CREATE TABLE `score` (
   `user_id` int(11) NOT NULL COMMENT 'User id',
   `score` int(11) NOT NULL COMMENT 'score',
   `created_date` datetime DEFAULT NULL COMMENT '作成日時',
   PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='Score table';
