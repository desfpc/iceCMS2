<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * IceCMS2Structure DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2Structure extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return '
CREATE TABLE IF NOT EXISTS `migrations` (
  `version` bigint(14) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `value_mtype` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`version`) USING BTREE,
  INDEX `migrations_name_idx`(`name`) USING BTREE
) ENGINE = InnoDB ROW_FORMAT = Dynamic CHARACTER SET utf8 COLLATE utf8_general_ci;
';
    }

    /**
     * Rollback Migration
     *
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `migrations`;';
    }
}