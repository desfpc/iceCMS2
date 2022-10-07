<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Icecms2CreateMaterialTemplatesTable DB Migration
 */

namespace app\migrations\MySql;

use iceCMS2\Cli\AbstractMigration;

class Icecms2CreateMaterialTemplatesTable extends AbstractMigration
{
    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
        return "
        CREATE TABLE `material_templates`  (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `filename` varchar(127) NOT NULL COMMENT 'Template filename',
            `name` varchar(255) NOT NULL COMMENT 'Template name',
            `type` ENUM ('admin', 'material', 'list') NOT NULL COMMENT 'Template type',
            `content` varchar(1024) NULL DEFAULT NULL COMMENT 'Template description',
            PRIMARY KEY (`id`) USING BTREE
        ) ENGINE = InnoDB AUTO_INCREMENT = 0 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
    }

    /**
     * Rollback Migration
     *
     * @return string
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS `material_templates`;';
    }
}