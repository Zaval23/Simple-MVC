-- SQL скрипт для миграции БД my-first-cms на SimpleMVC
-- Выполните этот скрипт для создания/обновления структуры БД

-- Создание таблицы categories (если не существует)
CREATE TABLE IF NOT EXISTS `categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание таблицы subcategories (если не существует)
CREATE TABLE IF NOT EXISTS `subcategories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `categoryId` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categoryId` (`categoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание/обновление таблицы articles
-- Проверяем и добавляем недостающие поля
CREATE TABLE IF NOT EXISTS `articles` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `publicationDate` date NOT NULL,
  `categoryId` smallint(5) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Добавляем поле subcategoryId если его нет
SET @dbname = DATABASE();
SET @tablename = "articles";
SET @columnname = "subcategoryId";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " smallint(5) unsigned NULL AFTER categoryId")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Добавляем поле is_visible если его нет
SET @columnname = "is_visible";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " tinyint(1) NOT NULL DEFAULT 1 AFTER content")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Создание таблицы article_authors (связь многие-ко-многим)
CREATE TABLE IF NOT EXISTS `article_authors` (
  `articleId` smallint(5) unsigned NOT NULL,
  `userId` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`articleId`, `userId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Обновление таблицы users (если нужно)
-- Проверяем наличие поля role
SET @tablename = "users";
SET @columnname = "role";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " varchar(25) NOT NULL DEFAULT 'auth_user' AFTER pass")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

