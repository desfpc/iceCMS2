<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2CreateMaterialMaterialsTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateMaterialMaterialsTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
        CREATE TABLE `material_materials`  (
            `parent_id` int UNSIGNED NOT NULL COMMENT 'Parent material ID',
            `child_id` int UNSIGNED NOT NULL COMMENT 'Child material ID',
            `count` int UNSIGNED NULL DEFAULT NULL COMMENT 'Count of child materials',
            `price` decimal(10, 2) NULL DEFAULT NULL COMMENT 'Price of child materials',
            `ordernum` int UNSIGNED NULL DEFAULT NULL COMMENT 'Order number',
            PRIMARY KEY (`parent_id`, `child_id`) USING BTREE,
            INDEX `matmat_parent_idx`(`parent_id`) USING BTREE,
            INDEX `matmat_child_idx`(`child_id`) USING BTREE
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
        return 'DROP TABLE IF EXISTS `material_materials`;';
    }
}