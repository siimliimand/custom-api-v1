CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `api_token` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_id` int(11) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_name_index` (`name`),
  KEY `users_name_email_index` (`name`,`email`),
  KEY `users_api_token_index` (`api_token`),
  KEY `users_status_id_foreign` (`status_id`),
  CONSTRAINT `users_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;