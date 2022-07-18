<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * icecms2CreateEntityImageSizesTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateEntityImageSizesTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return 'CREATE TABLE `entity_image_sizes`  (
  `keyString` varchar(255) NOT NULL,
  `image_size_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`keyString`, `image_size_id`),
  CONSTRAINT `entity_image_sizes_fk` FOREIGN KEY (`image_size_id`) REFERENCES `image_sizes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)
  ENGINE = InnoDB ROW_FORMAT = Dynamic CHARACTER SET utf8 COLLATE utf8_general_ci;';
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `entity_image_sizes`;';
    }
}