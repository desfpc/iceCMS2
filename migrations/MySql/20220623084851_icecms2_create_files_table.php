<?php
declare(strict_types=1);

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2CreateFilesTable DB Migration
 */

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateFilesTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
CREATE TABLE IF NOT EXISTS `files`
(
    `id`               int unsigned auto_increment comment 'ID' primary key,
    `name`             varchar(255)                  not null,
    `filename`         varchar(255)                  not null,
    `extension`        varchar(12)                   not null,
    `anons`            varchar(2048)                 null,
    `filetype`         enum ('file', 'image')        not null,
    `size`             int unsigned                  not null,
    `url`              varchar(255)                  null,
    `image_width`      smallint unsigned             null,
    `image_height`     smallint unsigned             null,
    `user_id`          int unsigned                  null,
    `private`          smallint unsigned default '0' not null,
    `created_time`     timestamp                     not null comment 'File created time',
    constraint `files_users_fk`
    foreign key (`user_id`) references `users` (`id`)
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
        return 'DROP TABLE IF EXISTS `files`;';
    }
}