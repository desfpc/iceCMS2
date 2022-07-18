<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * icecms2CreateUsersTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateUsersTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
CREATE TABLE IF NOT EXISTS `users`
(
    `id`                 int unsigned auto_increment comment 'ID' primary key,
    `email`              varchar(255)                                     not null comment 'E-mail',
    `phone`              varchar(24)                                      not null comment 'Phone number',
    `telegram`           varchar(32)                                      null comment 'Telegram login',
    `language`           enum ('En', 'Ge', 'Ru')                          not null comment 'User language',
    `name`               varchar(255)                                     null comment 'User name',
    `nikname`            varchar(255)                                     not null comment 'User nikname',
    `status`             enum ('created', 'active', 'deleted')            not null comment 'Activity status',
    `role`               enum ('user', 'moderator', 'admin')              not null comment 'User role',
    `rating`             float                            default 0       not null comment 'User rating',
    `avatar`             int unsigned                                     null comment 'Avatar image id',
    `email_approve_code` varchar(24)                                      not null comment 'Code to approve E-mail',
    `email_approved`     smallint unsigned                default 0       not null comment 'E-mail approved status',
    `created_time`       timestamp                                        not null comment 'User created time',
    `sex`                enum ('male', 'female', 'other') default 'other' not null comment 'User sex',
    `contacts`           json                                             null comment 'User contacts',
    `password`           varchar(255)                                     not null comment 'Password',
    constraint `users_email_idx` unique (`email`)
) ENGINE = InnoDB ROW_FORMAT = Dynamic CHARACTER SET utf8 COLLATE utf8_general_ci;
";
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `users`;';
    }
}