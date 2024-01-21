<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * isecms2_create_user_subscribe_table DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Isecms2CreateUserSubscribeTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
        CREATE TABLE `user_subscribers`  (
            `parent_id` int UNSIGNED NOT NULL COMMENT 'User 1 ID',
            `child_id` int UNSIGNED NOT NULL COMMENT 'User 2 ID',
            `date_add` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'User subscription initiation date',
            PRIMARY KEY (`parent_id`, `child_id`),
            CONSTRAINT `user_friends_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `user_friends_child_id` FOREIGN KEY (`child_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
        return 'DROP TABLE IF EXISTS `user_subscribers`;';
    }
}