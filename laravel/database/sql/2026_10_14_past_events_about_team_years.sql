-- ---------------------------------------------------------------------------
-- Manual version of the three 2026_10_14 migrations, for when
-- "Run database updates" / php artisan migrate cannot be used.
-- Run once, in phpMyAdmin (SQL tab) on the site's database, all together.
-- ---------------------------------------------------------------------------

-- 1. Past events: Facebook album links + a photo album per event ------------

ALTER TABLE `events`
  ADD COLUMN `facebook_album_urls` TEXT NULL AFTER `image_url`;

CREATE TABLE `event_photos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` BIGINT UNSIGNED NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(300) NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `event_photos_event_id_sort_order_index` (`event_id`, `sort_order`),
  CONSTRAINT `event_photos_event_id_foreign`
    FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. The About page ---------------------------------------------------------

CREATE TABLE `pages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `content` LONGTEXT NULL,
  `page_options` TEXT NULL,
  `image_url` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pages` (`slug`, `title`, `description`, `created_at`, `updated_at`)
VALUES ('about', 'About Mora Lenz',
        'Mora Lenz is the Mass Media Club of the University of Moratuwa. We capture moments, create stories and bring visions to life through the lens of creativity.',
        NOW(), NOW());

-- 3. One team per year ------------------------------------------------------

CREATE TABLE `team_terms` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `year` SMALLINT UNSIGNED NOT NULL,
  `description` VARCHAR(300) NULL,
  `is_current` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_terms_slug_unique` (`slug`),
  KEY `team_terms_is_active_year_index` (`is_active`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `team_groups`
  ADD COLUMN `term_id` BIGINT UNSIGNED NULL AFTER `id`,
  ADD CONSTRAINT `team_groups_term_id_foreign`
    FOREIGN KEY (`term_id`) REFERENCES `team_terms` (`id`) ON DELETE CASCADE;

-- Everything entered so far becomes the current team. Rename it later in
-- Admin -> Team -> Edit this year.
INSERT INTO `team_terms` (`name`, `slug`, `year`, `is_current`, `is_active`, `created_at`, `updated_at`)
VALUES ('2026/27', '2026-27', 2026, 1, 1, NOW(), NOW());

UPDATE `team_groups`
SET `term_id` = (SELECT `id` FROM `team_terms` WHERE `slug` = '2026-27')
WHERE `term_id` IS NULL;

-- 4. Tell Laravel these migrations are done ---------------------------------
-- Without this the dashboard keeps saying updates are pending.

SET @batch = (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('2026_10_14_000001_add_photos_to_events', @batch),
  ('2026_10_14_000002_create_pages_table', @batch),
  ('2026_10_14_000003_create_team_terms', @batch);
