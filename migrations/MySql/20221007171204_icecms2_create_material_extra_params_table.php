<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2CreateMaterialExtraParamsTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateMaterialExtraParamsTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
        CREATE TABLE `material_extra_params`  (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
            `name` varchar(255) NOT NULL COMMENT 'Param name',
            `mtype_id` int(11) NOT NULL COMMENT 'Material type ID',
            `value_type` ENUM('int', 'bool', 'varchar', 'text', 'float', 'decimal') NOT NULL COMMENT 'Param value type',
            `value_mtype` int UNSIGNED NULL DEFAULT NULL COMMENT 'Material type ID',
            PRIMARY KEY (`id`) USING BTREE,
            INDEX `mep_mtype_idx`(`mtype_id`) USING BTREE
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
        return 'DROP TABLE IF EXISTS `material_extra_params`;';
    }
}