<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * isecms2_create_logs_table DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Isecms2CreateLogsTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
        CREATE TABLE `logs`  (
            `id` int unsigned auto_increment comment 'ID' primary key,
            `alias` varchar(255) null comment 'name file log',
            `value` LONGTEXT null comment 'logs',
            `created_time` timestamp default CURRENT_TIMESTAMP not null comment 'created time',
            `updated_time` timestamp default CURRENT_TIMESTAMP not null comment 'updated time'
        ) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
        ";
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `logs`;';
    }
}