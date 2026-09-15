CREATE TABLE `products` (
  `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT '製品名',
  `description` text COLLATE utf8mb4_general_ci NOT NULL COMMENT '製品説明',
  `fulltext` text COLLATE utf8mb4_general_ci AS (concat(`name`,' ',`description`)) STORED COMMENT '全文検索用',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '作成日時',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新日時',
  PRIMARY KEY (`product_id`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`),
  FULLTEXT KEY `fulltext` (`fulltext`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='製品';