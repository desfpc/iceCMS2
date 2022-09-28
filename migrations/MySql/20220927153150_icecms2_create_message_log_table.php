<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2CreateMessageLogTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateMessageLogTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return 'CREATE TABLE `message_log` (
        `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
        `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `type` enum(\'email\',\'sms\',\'push\',\'fake\') NOT NULL,
        `to` varchar(255) NOT NULL,
        `to_name` varchar(255) NOT NULL,
        `from` varchar(255) NOT NULL,
        `from_name` varchar(255) NOT NULL,
        `theme` varchar(255) NOT NULL,
        `text` text NOT NULL,
        PRIMARY KEY (`id`)) ENGINE = InnoDB ROW_FORMAT = Dynamic CHARACTER SET utf8 COLLATE utf8_general_ci;';
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `message_log`;';
    }
}