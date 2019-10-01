CREATE TABLE `activity_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `controller` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `method` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `params` text COLLATE utf8mb4_general_ci,
  `response` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;