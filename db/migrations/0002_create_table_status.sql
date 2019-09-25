CREATE TABLE `status` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `status_code_index` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;