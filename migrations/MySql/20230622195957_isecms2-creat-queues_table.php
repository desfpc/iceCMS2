<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * isecms2_create_queues_table DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Isecms2CreatQueuesTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
        CREATE TABLE `queues`  (
            `id` int unsigned auto_increment comment 'ID' primary key,
            `queue` varchar(255) not null comment 'name queue',
            `task_id` varchar(255) not null comment 'task_id',
            `value` LONGTEXT not null comment 'value task',
            `status` ENUM ('processing', 'in process', 'completed', 'failed') NOT NULL COMMENT 'name statuses',
            `created_time` timestamp default CURRENT_TIMESTAMP not null comment 'created time',
            `updated_time` timestamp default CURRENT_TIMESTAMP not null comment 'updated time',
             constraint `queues_task_id_idx` unique (`task_id`)
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
        return 'DROP TABLE IF EXISTS `queues`;';
    }
}