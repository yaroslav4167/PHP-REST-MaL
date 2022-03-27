CREATE TABLE `authors` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
 `patronymic` text COLLATE utf8mb4_unicode_ci NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `magazines` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
 `create_date` date NOT NULL,
 `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
 `image` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
 `authors` text COLLATE utf8mb4_unicode_ci NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
