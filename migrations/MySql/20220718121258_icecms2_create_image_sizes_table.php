<?php
declare(strict_types=1);

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2CreateImageSizesTable DB Migration
 */

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateImageSizesTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return 'CREATE TABLE IF NOT EXISTS `image_sizes`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT primary key,
  `width` mediumint UNSIGNED NULL DEFAULT NULL,
  `height` mediumint UNSIGNED NULL DEFAULT NULL,
  `stringId` varchar(255) NOT NULL,
  `watermark_id` int UNSIGNED NULL DEFAULT NULL,
  `watermark_width` mediumint UNSIGNED NULL DEFAULT NULL,
  `watermark_height` mediumint UNSIGNED NULL DEFAULT NULL,
  `watermark_top` mediumint UNSIGNED NULL DEFAULT NULL,
  `watermark_left` mediumint UNSIGNED NULL DEFAULT NULL,
  `watermark_bottom` mediumint UNSIGNED NULL DEFAULT NULL,
  `watermark_right` mediumint UNSIGNED NULL DEFAULT NULL,
  UNIQUE INDEX `image_sizes_uk`(`stringId`),
  CONSTRAINT `image_sizes_fk` FOREIGN KEY (`watermark_id`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE SET NULL) 
  ENGINE = InnoDB ROW_FORMAT = Dynamic CHARACTER SET utf8 COLLATE utf8_general_ci;';
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `image_sizes`;';
    }
}