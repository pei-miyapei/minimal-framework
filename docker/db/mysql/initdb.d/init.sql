CREATE DATABASE IF NOT EXISTS dev_db;

USE dev_db;

CREATE TABLE IF NOT EXISTS `throwable_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `request_controller_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `line` int unsigned NOT NULL,
  `stack_trace` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pid` int unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `demos`;
CREATE TABLE `demos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='デモ用のテーブルです';

INSERT INTO `demos` (`id`, `title`, `created_at`, `updated_at`) VALUES
(1,	'data1',	NOW(),	NOW()),
(2,	'data2',	NOW(),	NOW());
