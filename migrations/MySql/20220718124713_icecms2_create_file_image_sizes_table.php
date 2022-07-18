<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * icecms2CreateFileImageSizesTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateFileImageSizesTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return 'CREATE TABLE `file_image_sizes`  (
  `file_id` int UNSIGNED NOT NULL,
  `image_size_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`file_id`, `image_size_id`),
  CONSTRAINT `file_image_sizes_fk1` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `file_image_sizes_fk2` FOREIGN KEY (`image_size_id`) REFERENCES `image_sizes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)
  ENGINE = InnoDB ROW_FORMAT = Dynamic CHARACTER SET utf8 COLLATE utf8_general_ci;';
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `file_image_sizes`;';
    }
}