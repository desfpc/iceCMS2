<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2CreateMaterialExtraValuesTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateMaterialExtraValuesTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
        CREATE TABLE `material_extra_values`  (
            `material_id` int UNSIGNED NOT NULL COMMENT 'Material ID',
            `param_id` int UNSIGNED NOT NULL COMMENT 'Parameter ID',
            `value_int` int UNSIGNED NULL DEFAULT NULL COMMENT 'Integer value',
            `value_char` varchar(255) NULL DEFAULT NULL COMMENT 'String value',
            `value_mat` int UNSIGNED NULL DEFAULT NULL COMMENT 'Material ID value',
            `value_text` text NULL DEFAULT NULL COMMENT 'Text value',
            `value_bool` tinyint(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Boolean value',
            PRIMARY KEY (`material_id`, `param_id`) USING BTREE,
            CONSTRAINT `mev_par_fk` FOREIGN KEY (`param_id`) REFERENCES `material_extra_params` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
        return 'DROP TABLE IF EXISTS `material_extra_values`;';
    }
}