<?php
declare(strict_types=1);

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * IceCMS2Structure DB Migration
 */

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
CREATE TABLE IF NOT EXISTS `migrations_test` (
  `version` bigint(14) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `value_mtype` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`version`) USING BTREE,
  INDEX `migrations_test_name_idx`(`name`) USING BTREE
) ENGINE = InnoDB ROW_FORMAT = Dynamic;
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
        return 'DROP TABLE IF EXISTS `migrations_test`;';
    }
}