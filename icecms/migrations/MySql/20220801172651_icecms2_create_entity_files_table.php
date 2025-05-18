<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2CreateEntityImagesTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateEntityFilesTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return 'CREATE TABLE `entity_files`  (
  `key_string` varchar(255) NOT NULL,
  `file_id` int UNSIGNED NOT NULL,
  `entity_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`key_string`, `file_id`, `entity_id`),
  CONSTRAINT `entity_files_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)
  ENGINE = InnoDB ROW_FORMAT = Dynamic CHARACTER SET utf8 COLLATE utf8_general_ci;';
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `entity_files`;';
    }
}