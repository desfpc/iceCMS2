<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2CreateMaterialsTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateMaterialsTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
        CREATE TABLE `materials`  (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Material ID',
            `name` varchar(1024) NOT NULL COMMENT 'Material name',
            `id_char` varchar(1024) NOT NULL COMMENT 'Material char ID',
            `material_type_id` int UNSIGNED NOT NULL COMMENT 'Material type ID',
            `language` ENUM('en', 'ge', 'ru') NOT NULL COMMENT 'Material language',
            `anons` varchar(2048) NULL DEFAULT NULL COMMENT 'Material anons',
            `content` text NULL DEFAULT NULL COMMENT 'Material content HTML',
            `parent_id` int UNSIGNED NULL DEFAULT NULL COMMENT 'Parent material ID',
            `date_add` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Material creation date',
            `date_edit` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Material last edit date',
            `date_event` datetime(0) NULL DEFAULT NULL COMMENT 'Material event date',
            `date_end` datetime(0) NULL DEFAULT NULL COMMENT 'Material event end date',
            `user_id` int UNSIGNED NULL DEFAULT NULL COMMENT 'Material author ID',
            `status_id` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Material status ID',
            `material_count` int UNSIGNED NULL DEFAULT NULL COMMENT 'Material some count (views, good count, etc)',
            `price` decimal(10, 2) NULL DEFAULT NULL COMMENT 'Material price',
            `goodcode` varchar(23) NULL DEFAULT NULL COMMENT 'Material good code (BARCODE, etc)',
            `important` tinyint(1) NULL DEFAULT NULL COMMENT 'Material important flag',
            `ordernum` int UNSIGNED NULL DEFAULT NULL COMMENT 'Material order number',
            `tags` varchar(1024) NULL DEFAULT NULL COMMENT 'Material tags',
            PRIMARY KEY (`id`) USING BTREE,
            UNIQUE INDEX `mat_goodcode_uq`(`goodcode`) USING BTREE,
            INDEX `mat_charid_idx`(`id_char`(255)) USING BTREE,
            CONSTRAINT `mat_type_fk` FOREIGN KEY (`material_type_id`) REFERENCES `material_types` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
        ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
        ";
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `materials`';
    }
}