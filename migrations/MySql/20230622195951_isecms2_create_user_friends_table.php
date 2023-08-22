<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * isecms2_create_user_friends_table DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Isecms2CreateUserFriendsTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
        CREATE TABLE `user_friends`  (
            `parent_id` int UNSIGNED NOT NULL COMMENT 'User 1 ID',
            `child_id` int UNSIGNED NOT NULL COMMENT 'User 2 ID',
            `status` ENUM ('pending', 'friend', 'subscriber', 'ignore') NOT NULL DEFAULT 'pending' COMMENT 'User friend status',
            `initiator` int UNSIGNED NOT NULL COMMENT 'Initiator user ID',
            `type` ENUM ('friend', 'family member', 'teammate', 'other') NOT NULL DEFAULT 'friend' COMMENT 'User friend type',
            `date_add` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'User friend initiation date',
            `date_edit` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'User friend last edit date',
            PRIMARY KEY (`parent_id`, `child_id`),
            CONSTRAINT `user_friends_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `user_friends_child_id` FOREIGN KEY (`child_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            INDEX `user_friends_parent_id_status` (`parent_id`, `status`) USING BTREE,
            INDEX `user_friends_child_id_status` (`child_id`, `status`) USING BTREE
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
        return 'DROP TABLE IF EXISTS `user_friends`;';
    }
}